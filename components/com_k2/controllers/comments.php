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

class K2ControllerComments extends JController {
	function display() {
		$user = &JFactory::getUser();
		if ($user->guest) {
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
		}
		JRequest::setVar('tmpl','component');
		
		$params = &JComponentHelper::getParams('com_k2');
		
		$document = &JFactory::getDocument();

		if(version_compare(JVERSION,'1.6.0','ge')) {
			JHtml::_('behavior.framework');
		} else {
			JHTML::_('behavior.mootools');
		}
		
		// Language
		$language = &JFactory::getLanguage();
		$language->load('com_k2', JPATH_ADMINISTRATOR);
		
		// CSS
		$document->addStyleSheet(JURI::root(true).'/media/k2/assets/css/k2.css');
		
		// JS
		$jQueryHandling = $params->get('jQueryHandling','1.7remote');
		if($jQueryHandling && strpos($jQueryHandling,'remote')==true){
			$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/'.str_replace('remote','',$jQueryHandling).'/jquery.min.js');
		} elseif($jQueryHandling && strpos($jQueryHandling,'remote')==false) {
			$document->addScript(JURI::root(true).'/media/k2/assets/js/jquery-'.$jQueryHandling.'.min.js');
		}
		$backendJQueryHandling = $params->get('backendJQueryHandling','remote');
		if($backendJQueryHandling=='remote'){
			$document->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js');
		} else {
			$document->addScript(JURI::root(true).'/media/k2/assets/js/jquery-ui-1.8.16.custom.min.js');
		}
		$document->addScript(JURI::root(true).'/media/k2/assets/js/k2.js?v=252');
		
		$this->addViewPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'views');
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$view = & $this->getView('comments', 'html');
		$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'views'.DS.'comments'.DS.'tmpl');
		$view->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers');
		$view->display();
	}
	function publish() {
		JRequest::checkToken() or jexit('Invalid Token');
		$user = &JFactory::getUser();
		if ($user->guest) {
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
		}
		JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$model = & JModel::getInstance('Comments', 'K2Model');
		$model->publish();
	}

	function unpublish() {
		JRequest::checkToken() or jexit('Invalid Token');
		$user = &JFactory::getUser();
		if ($user->guest) {
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
		}
		JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$model = & JModel::getInstance('Comments', 'K2Model');
		$model->unpublish();
	}

	function remove() {
		JRequest::checkToken() or jexit('Invalid Token');
		$user = &JFactory::getUser();
		if ($user->guest) {
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
		}
		JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$model = & JModel::getInstance('Comments', 'K2Model');
		$model->remove();
	}

	function deleteUnpublished() {
		JRequest::checkToken() or jexit('Invalid Token');
		$user = &JFactory::getUser();
		if ($user->guest) {
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
		}
		JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$model = & JModel::getInstance('Comments', 'K2Model');
		$model->deleteUnpublished();
	}

	function saveComment() {
		JRequest::checkToken() or jexit('Invalid Token');
		$user = &JFactory::getUser();
		if ($user->guest) {
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
		}
		JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$model = & JModel::getInstance('Comments', 'K2Model');
		$model->save();
		$mainframe->close();
	}

	function report() {
		JRequest::setVar('tmpl','component');
		$view = & $this->getView('comments', 'html');
		$view->setLayout('report');
		$view->report();
	}

	function sendReport(){
		JRequest::checkToken() or jexit('Invalid Token');
		$params = K2HelperUtilities::getParams('com_k2');
		$user = &JFactory::getUser();
		if(!$params->get('comments') || !$params->get('commentsReporting') || ($params->get('commentsReporting')=='2' && $user->guest) ){
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
		}
		JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'models');
		$model = & JModel::getInstance('Comments', 'K2Model');
		$model->setState('id', JRequest::getInt('id'));
		$model->setState('name', JRequest::getString('name'));
		$model->setState('reportReason', JRequest::getString('reportReason'));
		if(!$model->report()){
			echo $model->getError();
		}
		else {
			echo JText::_('K2_REPORT_SUBMITTED');
		}
		$mainframe = &JFactory::getApplication();
		$mainframe->close();
	}

}
