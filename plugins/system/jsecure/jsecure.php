<?php 
/**
 * jSecure Authentication plugin for Joomla!
 * jSecure Authentication extention prevents access to administration (back end)
 * login page without appropriate access key.
 *
 * @author      $Author: Ajay Lulia $
 * @copyright   Joomla Service Provider - 2011
 * @package     jSecure2.1.10
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: jsecure.php  $
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

$lang =& JFactory::getLanguage();

if($lang->getName()=="English (United Kingdom)")
{
	JPlugin::loadLanguage('plg_system_jsecure');
}

require_once('jsecure/jsecure.class.php');

$basepath     = JPATH_ADMINISTRATOR .'/components/com_jsecure';
$configFile	  = $basepath.'/params.php';
				
require_once($configFile);		

class plgSystemJSecure extends JPlugin {
	
	function plgSystemCanonicalization(& $subject, $config) {
		parent :: __construct($subject, $config);
	}
	
	function onAfterDispatch() {
		$app=& JFactory::getApplication();
		
		if (!$app->isAdmin()) {
			return true; // Dont run in admin
		}
		
		$config        = new JConfig();
		$JSecureConfig = new JSecureConfig();
		$app           =& JFactory::getApplication();
		$path          = '';
		$path         .= $JSecureConfig->options == 1 ? JURI::root().$JSecureConfig->custom_path : JURI::root();
		$jsecure 	   =  new jsecure();
		$publish       = $JSecureConfig->publish;
		
		if(!$publish){			
			return true;
		}

		$session    =& JFactory::getSession();
		$checkedKey = $session->get('jSecureAuthentication');

		if(!empty($checkedKey)){			
			return true;
		}
		
		$submit       = JRequest::getVar('submit', '');
		$passkey      = $JSecureConfig->key;

		if($submit == 'submit'){			
			$resultFormAction = jsecure::formAction($JSecureConfig);
			
			if(!empty($resultFormAction)){
				$session->set('jSecureAuthentication', 1);
				$link = JURI::root()."administrator/index.php?option=com_login";
				$app->redirect($link);
			} else {
				$app->redirect($path);
			}
		}
		
		$resultBloackIPs = jsecure::checkIps($JSecureConfig);
		
		if(!$resultBloackIPs){
			$app->redirect($path);
		}
		
		$task        = $JSecureConfig->passkeytype;

		switch($task){
			case 'form':
				jsecure::displayForm();
			exit;
			break;

			case 'url':
			default:
				$resultUrlKey = jsecure::checkUrlKey($JSecureConfig);
				if(!empty($resultUrlKey)){
					 $session->set('jSecureAuthentication', 1);
					 return true;
				} else {
					$app->redirect($path);
				}
			break;
		}
	}
}
?>