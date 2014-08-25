<?php
/*------------------------------------------------------------------------
# mod_zhyandexmap - Zh YandexMap Module
# ------------------------------------------------------------------------
# author    Dmitry Zhuk
# copyright Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# Websites: http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Zh YandexMap Helper
 */
class modZhYandexMapHelper
{

	public static function getMap($id) 
	{
			$db = JFactory::getDBO();

            $query = $db->getQuery(true);

			$query->select('h.*, c.title as category')
				->from('#__zhyandexmaps_maps as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('h.id=' . (int)$id);
				
			$db->setQuery($query);
				
			$item = $db->loadObject();

		return $item;
	}

	public static function getMarkers($id, $placemarkListId, $usermarkers, $usermarkersfilter, $usercontact) 
	{

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);
			
			if ($placemarkListId == "")
			{
				$mainWhereClause = 'h.mapid='.(int)$id;
			}
			else
			{
				$mainWhereClause = 'h.id IN ('.str_replace(';',',', $placemarkListId).')';
			}

			// Create some addition filters - Begin
			$addWhereClause = '';
			
			if ($usermarkers == 0)
			{
				// You can not enter markers

				// You can see all published, and you can't enter markers
				
				switch ((int)$usermarkersfilter)
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
				
				switch ((int)$usermarkersfilter)
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
			

			if ((int)$usercontact == 1)
			{
				$query->select('h.*, '.
					' c.title as category, g.icontype as groupicontype, g.overridemarkericon as overridemarkericon, g.published as publishedgroup, g.markermanagerminzoom as markermanagerminzoom, g.markermanagermaxzoom as markermanagermaxzoom, '.
					' g.iconofsetx groupiconofsetx, g.iconofsety groupiconofsety,'.
					' cn.name as contact_name, cn.address as contact_address, cn.con_position as contact_position, cn.telephone as contact_phone, cn.mobile as contact_mobile, cn.fax as contact_fax ')
					->from('#__zhyandexmaps_markers as h')
					->leftJoin('#__categories as c ON h.catid=c.id')
					->leftJoin('#__zhyandexmaps_markergroups as g ON h.markergroup=g.id')
					->leftJoin('#__contact_details as cn ON h.contactid=cn.id')
					->where($mainWhereClause . $addWhereClause)
					->order('h.title');
			}
			else
			{
				$query->select('h.*, '.
					' c.title as category, g.icontype as groupicontype, g.overridemarkericon as overridemarkericon, g.published as publishedgroup, g.markermanagerminzoom as markermanagerminzoom, g.markermanagermaxzoom as markermanagermaxzoom, '.
					' g.iconofsetx groupiconofsetx, g.iconofsety groupiconofsety')
					->from('#__zhyandexmaps_markers as h')
					->leftJoin('#__categories as c ON h.catid=c.id')
					->leftJoin('#__zhyandexmaps_markergroups as g ON h.markergroup=g.id')
					->where($mainWhereClause . $addWhereClause)
					->order('h.title');
			}

			$nullDate = $db->Quote($db->getNullDate());
			$nowDate = $db->Quote(JFactory::getDate()->toMySQL());
			$query->where('(h.publish_up = ' . $nullDate . ' OR h.publish_up <= ' . $nowDate . ')');
			$query->where('(h.publish_down = ' . $nullDate . ' OR h.publish_down >= ' . $nowDate . ')');
			
            $db->setQuery($query);        

			// Markers
			$markers = $db->loadObjectList();


		return $markers;
	}

	public static function getMarkerGroups($id, $placemarkListId) 
	{

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);

			$addWhereClause = "";

			if ($placemarkListId == "")
			{
				$addWhereClause .= ' and m.mapid='.(int)$id;
			}
			else
			{
				$addWhereClause .= ' and m.id IN ('.str_replace(';',',', $placemarkListId).')';
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
			$markergroups = $db->loadObjectList();


		return $markergroups;
	}

	
	public static function getRouters($id) 
	{

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);

			$query->select('h.*, c.title as category ')
				->from('#__zhyandexmaps_routers as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('h.published=1 and h.mapid=' . (int)$id);
				
			$db->setQuery($query);

			// Routers
			$routers = $db->loadObjectList();

		return $routers;
	}


	public static function getPaths($id) 
	{

			$db = JFactory::getDBO();

            $query = $db->getQuery(true);

			$query->select('h.*, c.title as category ')
				->from('#__zhyandexmaps_paths as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('h.published=1 and h.mapid=' . (int)$id);
				
			$db->setQuery($query);

			// Paths
			$paths = $db->loadObjectList();


		return $paths;
	}

	public static function getAPIKey() 
	{
		// Get global params
		$app = JFactory::getApplication();
        $comparams = JComponentHelper::getParams( 'com_zhyandexmap' );

		$apikey = $comparams->get( 'map_key');

		return $apikey;
	}

	public static function getCompatibleModeRSF() 
	{
		// Get global params
		$app = JFactory::getApplication();

        $comparams = JComponentHelper::getParams( 'com_zhyandexmap' );


		$compatiblemodersf = $comparams->get( 'map_compatiblemode_rsf');

		return $compatiblemodersf;
	}
	
	public static function getCompatibleMode() 
	{
		// Get global params
		$app = JFactory::getApplication();

        $comparams = JComponentHelper::getParams( 'com_zhyandexmap' );


		$compatiblemode = $comparams->get( 'map_compatiblemode');

		return $compatiblemode;
	}
	
	public static function getAPIVersion() 
	{
		// Get global params
		$app = JFactory::getApplication();

        $comparams = JComponentHelper::getParams( 'com_zhyandexmap' );


		$componentapiversion = $comparams->get( 'map_api_version');

		return $componentapiversion;
	}
	
	public static function getMapLicenseInfo() 
	{
		// Get global params
		$app = JFactory::getApplication();
        $comparams = JComponentHelper::getParams( 'com_zhyandexmap' );
		
		$licenseinfo = $comparams->get( 'licenseinfo');

		return $licenseinfo;
	}
	
	public static function getMapTypes() 
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select('h.*, c.title as category ')
			->from('#__zhyandexmaps_maptypes as h')
			->leftJoin('#__categories as c ON h.catid=c.id')
			->where('h.published=1');
		$db->setQuery($query);        
		
		// Map Types
		$maptypes = $db->loadObjectList();

		return $maptypes;
	}
	
}
