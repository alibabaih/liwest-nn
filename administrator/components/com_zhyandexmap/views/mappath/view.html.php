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
 * ZhYandex MapPath View
 */
class ZhYandexMapViewMapPath extends JView
{
	/**
	 * display method of ZhYandex MapPath view
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');
		$script = $this->get('Script');
		$mapList = $this->get('mapList');
		$this->assignRef( 'mapList',	$mapList);

		$mapAPIVersion = $this->get('APIVersion');
		$this->assignRef( 'mapAPIVersion',	$mapAPIVersion );

		$mapMapTypeYandex = $this->get('MapTypeYandex');
		$this->assignRef( 'mapMapTypeYandex',	$mapMapTypeYandex );
		$mapMapTypeOSM = $this->get('MapTypeOSM');
		$this->assignRef( 'mapMapTypeOSM',	$mapMapTypeOSM );
		$mapMapTypeCustom = $this->get('MapTypeCustom');
		$this->assignRef( 'mapMapTypeCustom',	$mapMapTypeCustom );
		$mapMapTypeList = $this->get('MapTypeList');
		$this->assignRef( 'mapMapTypeList',	$mapMapTypeList );
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;

		// Set the toolbar
		$this->addToolBar();

		// Map API Key
		$mapapikey = $this->get('APIKey');
		$this->assignRef( 'mapapikey',	$mapapikey );

		// Map DefLat and DefLng
		$mapDefLat = $this->get('DefLat');
		$this->assignRef( 'mapDefLat',	$mapDefLat );
		$mapDefLng = $this->get('DefLng');
		$this->assignRef( 'mapDefLng',	$mapDefLng );

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
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		$canDo = ZhYandexMapHelper::getPathActions($this->item->id);
		JToolBarHelper::title($isNew ? JText::_('COM_ZHYANDEXMAP_MAPPATH_NEW') : JText::_('COM_ZHYANDEXMAP_MAPPATH_EDIT'), 'mappath');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::apply('mappath.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('mappath.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('mappath.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('mappath.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('mappath.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('mappath.save', 'JTOOLBAR_SAVE');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')) 
				{
					JToolBarHelper::custom('mappath.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::custom('mappath.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('mappath.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_ZHYANDEXMAP_ADMINISTRATION_MAPPATH_CREATING') : JText::_('COM_ZHYANDEXMAP_ADMINISTRATION_MAPPATH_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "administrator/components/com_zhyandexmap/views/mappath/submitbutton.js");
		JText::script('COM_ZHYANDEXMAP_MAPPATH_ERROR_UNACCEPTABLE');
	}
}
