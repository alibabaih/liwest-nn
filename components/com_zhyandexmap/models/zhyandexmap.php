<?php
/*------------------------------------------------------------------------
# com_zhyandexmap - Zh YandexMap
# ------------------------------------------------------------------------
# author    Dmitry Zhuk
# copyright Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# Websites: http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

/**
 * Zh YandexMap Model
 */
class ZhYandexMapModelZhYandexMap extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;

		var $markers;
		var $markergroups;
		var $routers;
		var $paths;
        var $mapapikey;
		var $mapapiversion;
		var $maptypes;

		var $mapcompatiblemode;
		var $mapcompatiblemodersf;
		var $licenseinfo;
		
		var $externalmarkerlink;
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState() 
	{
		$app = JFactory::getApplication();
		// Get the map id
		$id = JRequest::getInt('id');
		$this->setState('map.id', $id);

		$placemarklistid = JRequest::getVar('placemarklistid');
		$this->setState('map.placemarklistid', $placemarklistid);
		
		$externalmarkerlink = JRequest::getVar('externalmarkerlink');
		$this->setState('map.externalmarkerlink', $externalmarkerlink);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'ZhYandexMap', $prefix = 'ZhYandexMapTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Get the map
	 * @return object The map to be displayed to the user
	 */
	public function getItem() 
	{
		if (!isset($this->item)) 
		{
			$id = $this->getState('map.id');

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);

			$query->select('h.*, c.title as category')
				->from('#__zhyandexmaps_maps as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('h.id=' . (int)$id);
				
			$db->setQuery($query);
				
			if (!$this->item = $db->loadObject()) 
			{
				$this->setError($db->getError());
			}
			else
			{
				// Load the JSON string
				$params = new JRegistry;
				$params->loadJSON($this->item->params);
				$this->item->params = $params;

				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($this->item->params);
				$this->item->params = $params;
			}

		}

		return $this->item;
	}

	public function getMarkers() 
	{
		if (!isset($this->markers)) 
		{       
			$id = $this->getState('map.id');

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);
			
			// Create some addition filters - Begin
			$addWhereClause = '';
			
			// Check if placemark list defined
			$placemarklistid = $this->getState('map.placemarklistid');
			if ($placemarklistid == "")
			{
				$addWhereClause .= ' and h.mapid='.(int)$id;

			}
			else
			{
				$addWhereClause .= ' and h.id IN ('.str_replace(';',',', $placemarklistid).')';
			}
			
			if ($this->item->usermarkers == 0)
			{
				// You can not enter markers

				// You can see all published, and you can't enter markers
				
				switch ((int)$this->item->usermarkersfilter)
				{
					case 0:
						$addWhereClause .= ' and h.published=1';
					break;
					case 1:
						$currentUser = JFactory::getUser();
						$addWhereClause .= ' and h.published=1';
						$addWhereClause .= ' and h.createdbyuser='.(int)$currentUser->id;
					break;
					default:
						$addWhereClause .= ' and h.published=1';
					break;					
				}
			}
			else
			{
				// You can enter markers
				
				switch ((int)$this->item->usermarkersfilter)
				{
					case 0:
						$currentUser = JFactory::getUser();
						if ((int)$currentUser->id == 0)
						{
							$addWhereClause .= ' and h.published=1';
						}
						else
						{
							$addWhereClause .= ' and (h.published=1 or h.createdbyuser='.(int)$currentUser->id .')';
						}
					break;
					case 1:
						$currentUser = JFactory::getUser();
						if ((int)$currentUser->id == 0)
						{
							$addWhereClause .= ' and h.published=1';
							$addWhereClause .= ' and h.createdbyuser='.(int)$currentUser->id;
						}
						else
						{
							$addWhereClause .= ' and h.createdbyuser='.(int)$currentUser->id;
						}
					break;
					default:
						$addWhereClause .= ' and h.published=1';
					break;					
				}
			}
			// Create some addition filters - End
			

			if ((int)$this->item->usercontact == 1)
			{
				$query->select('h.*, '.
					' c.title as category, g.icontype as groupicontype, g.overridemarkericon as overridemarkericon, g.published as publishedgroup, g.markermanagerminzoom as markermanagerminzoom, g.markermanagermaxzoom as markermanagermaxzoom, '.
					' g.iconofsetx groupiconofsetx, g.iconofsety groupiconofsety,'.
					' cn.name as contact_name, cn.address as contact_address, cn.con_position as contact_position, cn.telephone as contact_phone, cn.mobile as contact_mobile, cn.fax as contact_fax ')
					->from('#__zhyandexmaps_markers as h')
					->leftJoin('#__categories as c ON h.catid=c.id')
					->leftJoin('#__zhyandexmaps_markergroups as g ON h.markergroup=g.id')
					->leftJoin('#__contact_details as cn ON h.contactid=cn.id')
					->where('1=1' . $addWhereClause);
			}
			else
			{
				$query->select('h.*, '.
					' c.title as category, g.icontype as groupicontype, g.overridemarkericon as overridemarkericon, g.published as publishedgroup, g.markermanagerminzoom as markermanagerminzoom, g.markermanagermaxzoom as markermanagermaxzoom, '.
					' g.iconofsetx groupiconofsetx, g.iconofsety groupiconofsety')
					->from('#__zhyandexmaps_markers as h')
					->leftJoin('#__categories as c ON h.catid=c.id')
					->leftJoin('#__zhyandexmaps_markergroups as g ON h.markergroup=g.id')
					->where('1=1' . $addWhereClause);
			}

			$nullDate = $db->Quote($db->getNullDate());
			$nowDate = $db->Quote(JFactory::getDate()->toMySQL());
			$query->where('(h.publish_up = ' . $nullDate . ' OR h.publish_up <= ' . $nowDate . ')');
			$query->where('(h.publish_down = ' . $nullDate . ' OR h.publish_down >= ' . $nowDate . ')');
			$query->order('h.title');
			
            $db->setQuery($query);        

			// Markers
			if (!$this->markers = $db->loadObjectList()) 
			{
				$this->setError($db->getError());
			}

		}

		return $this->markers;
	}

	public function getMarkerGroups() 
	{
		if (!isset($this->markergroups)) 
		{       
			$id = $this->getState('map.id');

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);

			$addWhereClause = "";
			
			$placemarklistid = $this->getState('map.placemarklistid');
			if ($placemarklistid == "")
			{
				$addWhereClause .= ' and m.mapid='.(int)$id;

			}
			else
			{
				$addWhereClause .= ' and m.id IN ('.str_replace(';',',', $placemarklistid).')';
			}
			
			// Remove 'h.published=1 and m.published=1
			// because group may be disabled, but manual edit users placemark enable

			$query->select('distinct h.*, c.title as category ')
				->from('#__zhyandexmaps_markergroups as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->leftJoin('#__zhyandexmaps_markers as m ON m.markergroup=h.id')
				->where('1=1 ' . $addWhereClause)
				->order('h.title');

			$nullDate = $db->Quote($db->getNullDate());
			$nowDate = $db->Quote(JFactory::getDate()->toMySQL());
			$query->where('(m.publish_up = ' . $nullDate . ' OR m.publish_up <= ' . $nowDate . ')');
			$query->where('(m.publish_down = ' . $nullDate . ' OR m.publish_down >= ' . $nowDate . ')');
				
			$db->setQuery($query);

			// MarkerGroups
			if (!$this->markergroups = $db->loadObjectList()) 
			{
				$this->setError($db->getError());
			}

		}

		return $this->markergroups;
	}

	
	public function getRouters() 
	{
		if (!isset($this->routers)) 
		{       
			$id = $this->getState('map.id');

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);

			$query->select('h.*, c.title as category ')
				->from('#__zhyandexmaps_routers as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('h.published=1 and h.mapid=' . (int)$id);
				
			$db->setQuery($query);

			// Markers
			if (!$this->routers = $db->loadObjectList()) 
			{
				$this->setError($db->getError());
			}

		}

		return $this->routers;
	}


	public function getPaths() 
	{
		if (!isset($this->paths)) 
		{       
			$id = $this->getState('map.id');

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);

			$query->select('h.*, c.title as category ')
				->from('#__zhyandexmaps_paths as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('h.published=1 and h.mapid=' . (int)$id);
				
			$db->setQuery($query);

			// Paths
			if (!$this->paths = $db->loadObjectList()) 
			{
				$this->setError($db->getError());
			}

		}

		return $this->paths;
	}

	public function getAPIKey() 
	{
		// Get global params
		$app = JFactory::getApplication();
		$params = $app->getParams();

		return $mapapikey = $params->get( 'map_key', '' );
	}

	public function getAPIVersion() 
	{
		// Get global params
		$app = JFactory::getApplication();
		$params = $app->getParams();

		return $mapapiversion = $params->get( 'map_api_version', '' );
	}
	
	public function getCompatibleModeRSF() 
	{
		// Get global params
		$app = JFactory::getApplication();
		$params = $app->getParams();

		return $mapcompatiblemodersf = $params->get( 'map_compatiblemode_rsf', '' );
	}

	public function getCompatibleMode() 
	{
		// Get global params
		$app = JFactory::getApplication();
		$params = $app->getParams();

		return $mapcompatiblemode = $params->get( 'map_compatiblemode', '' );
	}
	
	public function getLicenseInfo() 
	{
		// Get global params
		$app = JFactory::getApplication();
		$params = $app->getParams();

		return $licenseinfo = $params->get( 'licenseinfo', '' );
	}
	
	public function getMapTypes() 
	{
		if (!isset($this->maptypes)) 
		{       
			$db = JFactory::getDBO();

            $query = $db->getQuery(true);
            $query->select('h.*, c.title as category ')
                ->from('#__zhyandexmaps_maptypes as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
                ->where('h.published=1');
            $db->setQuery($query);        
			
			// Map Types
			if (!$this->maptypes = $db->loadObjectList()) 
			{
				$this->setError($db->getError());
			}

		}

		return $this->maptypes;
	}
	
	public function getExternalMarkerLink() 
	{
		$externalmarkerlink = $this->getState('map.externalmarkerlink');
		return $externalmarkerlink;
	}
	
}
