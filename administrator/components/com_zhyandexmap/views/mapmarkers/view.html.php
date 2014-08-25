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

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * View class for the ZhYandex MapMarkers Component
 */
class ZhYandexMapViewMapMarkers extends JView
{

	protected $state;
	protected $items;
	protected $pagination;

	// Overwriting JView display method
	function display($tpl = null) 
	{
		// Get data from the model
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$mapList = $this->get('mapList');
		$this->assignRef( 'mapList',	$mapList);
 
		$groupList = $this->get('groupList');
		$this->assignRef( 'groupList',	$groupList);

		$userList = $this->get('userList');
		$this->assignRef( 'userList',	$userList);

		$iconList = $this->get('iconList');
		$this->assignRef( 'iconList',	$iconList);
		
		// Check for errors.
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
 
                // Set the toolbar
                $this->addToolBar();
 
                // Display the template
                parent::display($tpl);

		// Set the document
		$this->setDocument();

	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$canDo = ZhYandexMapHelper::getMarkerActions();
		JToolBarHelper::title(JText::_('COM_ZHYANDEXMAP_MAPMARKER_MANAGER'), 'mapmarker');
		if ($canDo->get('core.create')) 
		{
			JToolBarHelper::addNew('mapmarker.add', 'JTOOLBAR_NEW');
		}
		if ($canDo->get('core.edit')) 
		{
			JToolBarHelper::editList('mapmarker.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) 
		{
				JToolBarHelper::divider();
				JToolBarHelper::publish('mapmarkers.publish', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::unpublish('mapmarkers.unpublish', 'JTOOLBAR_UNPUBLISH', true);
				JToolBarHelper::divider();
		}
		if ($canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'mapmarkers.delete', 'JTOOLBAR_DELETE');
		}
		if ($canDo->get('core.admin')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_zhyandexmap');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_ZHYANDEXMAP_MAPMARKER_ADMINISTRATION'));
	}

}
