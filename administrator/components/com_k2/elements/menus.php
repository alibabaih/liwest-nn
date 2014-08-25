<?php
/**
 * @version		$Id: menus.php 1046 2011-10-05 18:22:44Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldMenus extends JFormField {

		var	$type = 'menus';

		function getInput(){
			return JElementMenus::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}

	}
}

jimport('joomla.html.parameter.element');

class JElementMenus extends JElement {

	var	$_name = 'menus';

	function fetchElement($name, $value, &$node, $control_name){
		$fieldName = (K2_JVERSION=='16')? $name : $control_name.'['.$name.']';
		$db = &JFactory::getDBO();
		$query = "SELECT menutype, title FROM #__menu_types";
		$db->setQuery($query);
		$menus = $db->loadObjectList();

		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('K2_NONE_ONSELECTLISTS'));

		foreach ($menus as $menu) {
			$options[]	= JHTML::_('select.option', $menu->menutype, $menu->title);
		}
		
		return JHTML::_('select.genericlist',  $options, $fieldName, 'class="inputbox"', 'value', 'text', $value);
	}
}