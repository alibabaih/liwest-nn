<?php
/**
 * @version		$Id: view.html.php 1209 2011-10-17 22:04:23Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class K2ViewComments extends JView {

	function report($tpl = null) {
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$row = & JTable::getInstance('K2Comment', 'Table');
		$row->load(JRequest::getInt('commentID'));
		if(!$row->published){
			 JError::raiseError(404, JText::_('K2_NOT_FOUND'));
		}
		$this->assignRef('row', $row);
		$user = &JFactory::getUser();
		$this->assignRef('user', $user);
		$params = &K2HelperUtilities::getParams('com_k2');
		if(!$params->get('comments') || !$params->get('commentsReporting') || ($params->get('commentsReporting')=='2' && $user->guest) ){
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
		}
		$this->assignRef('params', $params);
		if($params->get('recaptcha') && $user->guest){
		  $document = &JFactory::getDocument();
		  $document->addScript('http://api.recaptcha.net/js/recaptcha_ajax.js');
  		$js = '
			function showRecaptcha(){
				Recaptcha.create("'.$params->get('recaptcha_public_key').'", "recaptcha", {
					theme: "'.$params->get('recaptcha_theme', 'clean').'"
				});
			}
			$K2(window).load(function() {
				showRecaptcha();
			});
			';
			$document->addScriptDeclaration($js);
		}

		parent::display($tpl);
	}

}
