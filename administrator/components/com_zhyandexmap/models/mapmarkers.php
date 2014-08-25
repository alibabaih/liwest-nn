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

// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * ZhYandexMarkers Model
 */
class ZhYandexMapModelMapMarkers extends JModelList
{

	var $mapList;
	var $groupList;
    var $userList;
	var $iconList;
	
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'mapid', 'h.mapid',
				'published', 'h.published',
				'markergroup', 'h.markergroup',
				'catid', 'h.catid', 'category_title',
				'h.createdbyuser',
			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		parent::populateState();

		$app = JFactory::getApplication();

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$mapid = $this->getUserStateFromRequest($this->context.'.filter.mapid', 'filter_mapid', '');
		$this->setState('filter.mapid', $mapid);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$icontype = $this->getUserStateFromRequest($this->context.'.filter.icontype', 'filter_icontype', '');
		$this->setState('filter.icontype', $icontype);

		$markergroup = $this->getUserStateFromRequest($this->context.'.filter.markergroup', 'filter_markergroup', '');
		$this->setState('filter.markergroup', $markergroup);

		$createdbyuser = $this->getUserStateFromRequest($this->context.'.filter.createdbyuser', 'filter_createdbyuser', '');
		$this->setState('filter.createdbyuser', $createdbyuser);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_zhyandexmap');
		$this->setState('params', $params);

	}


	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery() 
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('h.id,h.title,h.mapid,h.published,h.publish_up,h.publish_down,h.catid,h.icontype,c.title as category,m.title as mapname,g.title as markergroupname, usr.username, usr.name fullusername');
		$query->from('#__zhyandexmaps_markers as h');
		$query->leftJoin('#__categories as c on h.catid=c.id');
		$query->leftJoin('#__zhyandexmaps_maps as m on h.mapid=m.id');
		$query->leftJoin('#__zhyandexmaps_markergroups as g on h.markergroup=g.id');
		$query->leftJoin('#__users as usr on h.createdbyuser=usr.id');

		// Filter by mapid.
		$mapId = $this->getState('filter.mapid');
		if (is_numeric($mapId)) {
			$query->where('h.mapid = '.(int) $mapId);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('h.published = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(h.published IN (0, 1))');
		}
		
		// Filter by markergroup.
		$markerGroup = $this->getState('filter.markergroup');
		if (is_numeric($markerGroup)) {
			$query->where('h.markergroup = '.(int) $markerGroup);
		}

		// Filter by createdbyuser.
		$createdByUser = $this->getState('filter.createdbyuser');
		if (is_numeric($createdByUser)) {
			$query->where('h.createdbyuser = '.(int) $createdByUser);
		}

		// Filter by icontype
		$icontype = $this->getState('filter.icontype');
		if ($icontype != "") {
			$query->where('h.icontype = \''.$icontype.'\'');
		}
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			$search = $db->Quote('%'.$db->getEscaped($search, true).'%', false);
			$query->where('(h.title LIKE '.$search.')');
		}
		
		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('h.catid = '.(int) $categoryId);
		}
		else if (is_array($categoryId)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('h.catid IN ('.$categoryId.')');
		}
		
		return $query;
	}

	public function getmapList() 
	{
		if (!isset($this->mapList)) 
		{       

			$this->_db->setQuery($this->_db->getQuery(true)
				->select('h.title as text, h.id as value ')
				->from('#__zhyandexmaps_maps as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('1=1'));

			$this->mapList = $this->_db->loadObjectList();

			// Custom Fields
			//if (!$this->mapList = $this->_db->loadObjectList()) 
			//{
			//	$this->setError($this->_db->getError());
			//}

		}

		return $this->mapList;
	}

	public function getgroupList() 
	{
		if (!isset($this->groupList)) 
		{       

			$this->_db->setQuery($this->_db->getQuery(true)
				->select('h.title as text, h.id as value ')
				->from('#__zhyandexmaps_markergroups as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('1=1'));

			$this->groupList = $this->_db->loadObjectList();

			// Custom Fields
			//if (!$this->mapList = $this->_db->loadObjectList()) 
			//{
			//	$this->setError($this->_db->getError());
			//}

		}

		return $this->groupList;
	}

	public function getuserList() 
	{
		if (!isset($this->userList)) 
		{       

			$this->_db->setQuery($this->_db->getQuery(true)
				->select('h.name as text, h.id as value ')
				->from('#__users as h')
				->where('1=1'));

			$this->userList = $this->_db->loadObjectList();

			// Custom Fields
			//if (!$this->mapList = $this->_db->loadObjectList()) 
			//{
			//	$this->setError($this->_db->getError());
			//}

		}

		return $this->userList;
	}

	
	public function geticonList() 
	{
		if (!isset($this->iconList)) 
		{       

			$this->_db->setQuery($this->_db->getQuery(true)
				->select('distinct h.icontype as text, h.icontype as value ')
				->from('#__zhyandexmaps_markers as h')
				->leftJoin('#__categories as c ON h.catid=c.id')
				->where('1=1'));

			$this->iconList = $this->_db->loadObjectList();

			// Custom Fields
			//if (!$this->mapList = $this->_db->loadObjectList()) 
			//{
			//	$this->setError($this->_db->getError());
			//}

		}

		return $this->iconList;
	}
	
}
