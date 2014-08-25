<?php
/**
 * @version		$Id: extrafieldsgroups.php 1343 2011-11-25 16:39:33Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class K2ControllerExtraFieldsGroups extends JController
{

	function display() {
		JRequest::setVar('view', 'extrafieldsgroups');
		$model = & $this->getModel('extraFields');
		$view = & $this->getView('extrafieldsgroups', 'html');
		$view->setModel($model, true);
		parent::display();
	}
	
	function add() {
		$mainframe = &JFactory::getApplication();
		$mainframe->redirect('index.php?option=com_k2&view=extrafieldsgroup');
	}

	function edit() {
		$mainframe = &JFactory::getApplication();
		$cid = JRequest::getVar('cid');
		$mainframe->redirect('index.php?option=com_k2&view=extrafieldsgroup&cid='.$cid[0]);
	}
	
	function remove() {
		JRequest::checkToken() or jexit('Invalid Token');
		$model = & $this->getModel('extraFields');
		$model->removeGroups();
	}

}
