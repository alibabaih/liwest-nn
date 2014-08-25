<?php
/**
 * @version		$Id: comments.php 1370 2011-11-28 18:29:41Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class K2ControllerComments extends JController
{

	function display() {
		require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
		JRequest::setVar('view', 'comments');
		parent::display();
	}

	function publish() {
		JRequest::checkToken() or jexit('Invalid Token');
		$model = & $this->getModel('comments');
		$model->publish();
	}

	function unpublish() {
		JRequest::checkToken() or jexit('Invalid Token');
		$model = & $this->getModel('comments');
		$model->unpublish();
	}

	function remove() {
		JRequest::checkToken() or jexit('Invalid Token');
		$model = & $this->getModel('comments');
		$model->remove();
	}
	
	function deleteUnpublished() {
		JRequest::checkToken() or jexit('Invalid Token');
		$model = & $this->getModel('comments');
		$model->deleteUnpublished();
	}
	
	function saveComment() {
		JRequest::checkToken() or jexit('Invalid Token');
		$model = & $this->getModel('comments');
		$model->save();
	}

}
