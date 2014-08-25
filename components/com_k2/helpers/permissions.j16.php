<?php
/**
 * @version		$Id: permissions.j16.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter');

class K2HelperPermissions {

	function checkPermissions() {
		// Set some variables
		$mainframe = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		$id = JRequest::getInt('cid');

		//Generic manage check
		if (!$user->authorise('core.manage', $option)) {
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			$mainframe->redirect('index.php');
		}

		// Determine action for rest checks
		$action = false;
		if($mainframe->isAdmin() && $view!='' && $view!='info'){
			switch($task){
				case '':
				case 'save':
				case 'apply':
					if(!$id){
						$action = 'core.create';
					}
					else {
						$action = 'core.edit';
					}
					break;
				case 'trash':
				case 'remove':
					$action = 'core.delete';
					break;
				case 'publish':
				case 'unpublish':
					$action = 'core.edit.state';
			}

			// Check the determined action
			if($action){
				if(!$user->authorize($action, $option)){
					JError::raiseWarning(404, JText::_('K2_JERROR_ALERTNOAUTHOR'));
					$mainframe->redirect('index.php?option=com_k2');
				}
			}

			// Finaly, check for the edit own function
			if($view == 'item' && $id){
				JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
				$item = &JTable::getInstance('K2Item', 'Table');
				$item->load($id);
				if($item->created_by == $user->id){
					if(!$user->authorize('core.edit.own', $option)){
						JError::raiseWarning(404, JText::_('K2_JERROR_ALERTNOAUTHOR'));
						$mainframe->redirect('index.php?option=com_k2');
					}
				}
			}
		}
	}
}