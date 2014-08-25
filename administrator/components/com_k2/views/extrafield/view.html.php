<?php
/**
 * @version		$Id: view.html.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class K2ViewExtraField extends JView
{

	function display($tpl = null)
	{
		JRequest::setVar('hidemainmenu', 1);
		$model = & $this->getModel();
		$extraField = $model->getData();
		if(!$extraField->id){
			$extraField->published=1;
		}
		$this->assignRef('row', $extraField);

		$lists = array ();
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $extraField->published);

		$groups[] = JHTML::_('select.option', 0, JText::_('K2_CREATE_NEW_GROUP'));

		require_once (JPATH_COMPONENT.DS.'models'.DS.'extrafields.php');
		$extraFieldModel= new K2ModelExtraFields;
		$uniqueGroups= $extraFieldModel->getGroups();
		foreach ($uniqueGroups as $group){
			$groups[] = JHTML::_('select.option', $group->id, $group->name);
		}

		$lists['group'] = JHTML::_('select.genericlist', $groups, 'groups', '', 'value', 'text', $extraField->group);

		$typeOptions[] = JHTML::_('select.option', 0, JText::_('K2_SELECT_TYPE'));
		$typeOptions[] = JHTML::_('select.option', 'textfield', JText::_('K2_TEXT_FIELD'));
		$typeOptions[] = JHTML::_('select.option', 'textarea', JText::_('K2_TEXTAREA'));
		$typeOptions[] = JHTML::_('select.option', 'select', JText::_('K2_DROPDOWN_SELECTION'));
		$typeOptions[] = JHTML::_('select.option', 'multipleSelect', JText::_('K2_MULTISELECT_LIST'));
		$typeOptions[] = JHTML::_('select.option', 'radio', JText::_('K2_RADIO_BUTTONS'));
		$typeOptions[] = JHTML::_('select.option', 'link', JText::_('K2_LINK'));
		$typeOptions[] = JHTML::_('select.option', 'csv', JText::_('K2_CSV_DATA'));
		$typeOptions[] = JHTML::_('select.option', 'labels', JText::_('K2_SEARCHABLE_LABELS'));
		$typeOptions[] = JHTML::_('select.option', 'date', JText::_('K2_DATE'));
		$lists['type'] = JHTML::_('select.genericlist', $typeOptions, 'type', '', 'value', 'text', $extraField->type);

		$this->assignRef('lists', $lists);
		(JRequest::getInt('cid'))? $title = JText::_('K2_EDIT_EXTRA_FIELD') : $title = JText::_('K2_ADD_EXTRA_FIELD');
		JToolBarHelper::title($title, 'k2.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JHTML::_('behavior.calendar');
	  
		$document = &JFactory::getDocument();
		$document->addScriptDeclaration('
		var K2Language = [
		"'.JText::_('K2_REMOVE', true).'", 
		"'.JText::_('K2_OPTIONAL', true).'",
		"'.JText::_('K2_COMMA_SEPARATED_VALUES', true).'",
		"'.JText::_('K2_USE_EDITOR', true).'",
		"'.JText::_('K2_ALL_SETTINGS_ABOVE_ARE_OPTIONAL', true).'",
		"'.JText::_('K2_ADD_AN_OPTION', true).'",
		"'.JText::_('K2_LINK_TEXT', true).'",
		"'.JText::_('K2_URL', true).'",
		"'.JText::_('K2_OPEN_IN', true).'",
		"'.JText::_('K2_SAME_WINDOW', true).'",
		"'.JText::_('K2_NEW_WINDOW', true).'",
		"'.JText::_('K2_CLASSIC_JAVASCRIPT_POPUP', true).'",
		"'.JText::_('K2_LIGHTBOX_POPUP', true).'",
		"'.JText::_('K2_RESET_VALUE', true).'",
		"'.JText::_('K2_CALENDAR', true).'",
		"'.JText::_('K2_PLEASE_SELECT_A_FIELD_TYPE_FROM_THE_LIST_ABOVE', true).'",
		];');
	  
		parent::display($tpl);
	}

}
