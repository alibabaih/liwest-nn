<?php
/**
 * @version		$Id: menuitem.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldMenuItem extends JFormField {

		var	$type = 'MenuItem';

		function getInput(){
			return JElementMenuItem::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}

	}
}

jimport('joomla.html.parameter.element');

class JElementMenuItem extends JElement
{

	var	$_name = 'MenuItem';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db =& JFactory::getDBO();

		// load the list of menu types
		// TODO: move query to model
		$query = 'SELECT menutype, title' .
				' FROM #__menu_types' .
				' ORDER BY title';
		$db->setQuery( $query );
		$menuTypes = $db->loadObjectList();

		$where = '';
		if ($state = $node->attributes('state')) {
			$where .= ' AND published = '.(int) $state;
		}

		// load the list of menu items
		// TODO: move query to model
		if(K2_JVERSION == '16') {
			$query = 'SELECT id, parent_id, title, menutype, type, published' .
				' FROM #__menu' .
			$where .
				' ORDER BY menutype, parent_id, ordering'
				;
		}
		else {
			$query = 'SELECT id, parent, name, menutype, type, published' .
				' FROM #__menu' .
			$where .
				' ORDER BY menutype, parent, ordering'
				;
					
		}


		$db->setQuery($query);
		$menuItems = $db->loadObjectList();

		// establish the hierarchy of the menu
		// TODO: use node model
		$children = array();

		if ($menuItems)
		{
			// first pass - collect children
			foreach ($menuItems as $v)
			{
				if(K2_JVERSION == '16') {
					$v->parent = $v->parent_id;
					$v->name = $v->title;
				}
				$pt 	= $v->parent;
				$list 	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}

		// second pass - get an indent list of the items
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

			foreach ( $list as $item ) {
			$item->treename = JString::str_ireplace('&#160;', ' -', $item->treename);
			$mitems[] = JHTML::_('select.option',  $item->id, '   '.$item->treename );
		}
		
		// assemble into menutype groups
		$n = count( $list );
		$groupedList = array();
		foreach ($list as $k => $v) {
			$groupedList[$v->menutype][] = &$list[$k];
		}

		// assemble menu items to the array
		$options 	= array();
		$options[]	= JHTML::_('select.option', '', '- '.JText::_('K2_SELECT_MENU_ITEM').' -');

		foreach ($menuTypes as $type)
		{
			if ($type != '')
			{
				$options[]	= JHTML::_('select.option',  '0', '&nbsp;', 'value', 'text', true);
				$options[]	= JHTML::_('select.option',  $type->menutype, $type->title . ' - ' . JText::_('K2_TOP'), 'value', 'text', true );
			}
			if (isset( $groupedList[$type->menutype] ))
			{
				$n = count( $groupedList[$type->menutype] );
				for ($i = 0; $i < $n; $i++)
				{
					$item = &$groupedList[$type->menutype][$i];

					//If menutype is changed but item is not saved yet, use the new type in the list
					if ( JRequest::getString('option', '', 'get') == 'com_menus' ) {
						$currentItemArray = JRequest::getVar('cid', array(0), '', 'array');
						$currentItemId = (int) $currentItemArray[0];
						$currentItemType = JRequest::getString('type', $item->type, 'get');
						if ( $currentItemId == $item->id && $currentItemType != $item->type) {
							$item->type = $currentItemType;
						}
					}

					$disable = strpos($node->attributes('disable'), $item->type) !== false ? true : false;

					if($item->published==0) $item->treename.=' [**'.JText::_('K2_UNPUBLISHED').'**]';
					if($item->published==-2) $item->treename.=' [**'.JText::_('K2_TRASHED').'**]';

					$options[] = JHTML::_('select.option',  $item->id, $item->treename, 'value', 'text', $disable );

				}
			}
		}
		
		if(K2_JVERSION == '16') {
			$fieldName = $name;
		}
		else {
			$fieldName = $control_name.'['.$name.']';
		}

		return JHTML::_('select.genericlist',  $options, $fieldName, 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
}
