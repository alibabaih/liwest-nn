<?php
/**
 * @version		$Id: html.php 1193 2011-10-17 15:51:26Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class K2HelperHTML {

	function subMenu() {
		$user = &JFactory::getUser();
		$view = JRequest::getCmd('view');
		$view = JString::strtolower($view);
		$params = & JComponentHelper::getParams('com_k2');
		JSubMenuHelper::addEntry(JText::_('K2_ITEMS'), 'index.php?option=com_k2&view=items', $view == 'items');
		JSubMenuHelper::addEntry(JText::_('K2_CATEGORIES'), 'index.php?option=com_k2&view=categories', $view == 'categories');
		if( !$params->get('lockTags') || $user->gid>23) {
			JSubMenuHelper::addEntry(JText::_('K2_TAGS'), 'index.php?option=com_k2&view=tags', $view == 'tags');
		}
		JSubMenuHelper::addEntry(JText::_('K2_COMMENTS'), 'index.php?option=com_k2&view=comments', $view == 'comments');
		if ($user->gid > 23) {
			JSubMenuHelper::addEntry(JText::_('K2_USERS'), 'index.php?option=com_k2&view=users', $view == 'users');
			JSubMenuHelper::addEntry(JText::_('K2_USER_GROUPS'), 'index.php?option=com_k2&view=usergroups', $view == 'usergroups');
			JSubMenuHelper::addEntry(JText::_('K2_EXTRA_FIELDS'), 'index.php?option=com_k2&view=extrafields', $view == 'extrafields');
			JSubMenuHelper::addEntry(JText::_('K2_EXTRA_FIELD_GROUPS'), 'index.php?option=com_k2&view=extrafieldsgroups', $view == 'extrafieldsgroups');
		}
		JSubMenuHelper::addEntry(JText::_('K2_MEDIA_MANAGER'), 'index.php?option=com_k2&view=media', $view == 'media');
		JSubMenuHelper::addEntry(JText::_('K2_INFORMATION'), 'index.php?option=com_k2&view=info', $view == 'info');
	}

	function stateToggler(&$row, $key, $property = 'published', $tasks = array('publish', 'unpublish'), $labels = array('K2_PUBLISH', 'K2_UNPUBLISH')) {
		$task = $row->$property ? $tasks[1] : $tasks[0];
		$action = $row->$property ? JText::_($labels[1]) : JText::_($labels[0]);
		$class = 'k2Toggler';
		$status = $row->$property ? 'k2Active':'k2Inactive';
		$href = '<a class="'.$class.' '.$status.'" href="javascript:void(0);" onclick="return listItemTask(\'cb'. $key .'\',\''. $task .'\')" title="'. $action .'">'.$action.'</a>';
		return $href;
	}
	
}