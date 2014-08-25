<?php
/**
 * jSecure Authentication components for Joomla!
 * jSecure Authentication extention prevents access to administration (back end)
 * login page without appropriate access key.
 *
 * @author      $Author: Ajay Lulia $
 * @copyright   Joomla Service Provider - 2011
 * @package     jSecure2.1.10
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: view.html.php  $
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class jsecureViewAuth extends JView {
	function display($tpl=null){
		parent::display($tpl);
	}
	
	function login(){
		$app    = &JFactory::getApplication();
		jimport('joomla.filesystem.file');	

		$basepath   = JPATH_ADMINISTRATOR .'/components/com_jsecure';
		$configFile	= $basepath.'/params.php';
		
		require_once($configFile);
		
		$JSecureConfig = new JSecureConfig();

		$master_password = md5(base64_encode(JRequest::getCmd('master_password')));
		
		if($JSecureConfig->master_password == $master_password )
		{
			$session = JFactory::getSession();
			$session->set('jsecure_master_logged', true);
			$app->redirect('index.php?option=com_jsecure'.$oldTask.'', JText::_('LOGIN_OK'));
			
		} else {
			$model 	= $this->getModel( 'jsecurelog' );
			$change_variable = 'Wrong Master Key = '.JRequest::getCmd('master_password'); 
		
			$insertLog = $model ->insertLog('JSECURE_EVENT_MASTER_LOGIN_FAIL', $change_variable);
		
			JError::raiseWarning(500, JText::_('LOGIN_ERROR'));
			$app->redirect('index.php?option=com_jsecure&view=auth');
		}
	}
	
}

?>