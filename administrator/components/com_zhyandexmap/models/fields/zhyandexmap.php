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

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * ZhYandexMap Form Field class for the ZhYandexMap component
 */
class JFormFieldZhYandexMap extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'ZhYandexMap';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('h.id as id,h.title as title,c.title as category,h.catid,h.width,h.height,h.latitude,h.longitude,h.zoom,h.doubleclickzoom,h.scrollwheelzoom,h.zoomcontrol,h.scalecontrol,h.maptype,h.minimap,h.toolbar,h.description,h.maptypecontrol,h.search,h.traffic,h.balloon');
		$query->from('#__zhyandexmaps_maps as h');
		$query->leftJoin('#__categories as c on h.catid=c.id');
		$query->order('h.title');
		
		$db->setQuery((string)$query);
		$maps = $db->loadObjectList();
		$options = array();
		if ($maps)
		{
			foreach($maps as $map) 
			{
				$options[] = JHtml::_('select.option', $map->id, $map->title . ($map->catid ? ' (' . $map->category . ')' : ''));
			}
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
