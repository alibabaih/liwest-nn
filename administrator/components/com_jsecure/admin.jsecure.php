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
 * @version     $Id: admin.jsecure.php  $
 */
// no direct access
defined('_JEXEC') or die('Restricted Access');

// Require the base controller
require_once (JPATH_COMPONENT_ADMINISTRATOR.DS.'controller.php');

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base()."components/com_jsecure/css/jsecure.css");

// Create the controller
$controller    = new jsecureControllerjsecure();

// Perform the Request task
if (!jsecureControllerjsecure::isMasterLogged())
{	
	if (JRequest::getCmd('task') == 'login')
		$controller->execute('login');
	else 
		$controller->execute('auth');
}
else
	$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();

?>