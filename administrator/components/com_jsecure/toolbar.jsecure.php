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
 * @version     $Id: toolbar.jsecure.php  $
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JApplicationHelper::getPath( 'toolbar_html' ) );
 if (!jsecureControllerjsecure::isMasterLogged())
		$task = 'auth'; 

	switch($task) {
		case 'help':
			TOOLBAR_jsecure::_help();
		break;
		
		case 'auth':
			TOOLBAR_jsecure::_AUTH();
		break;

		case 'log':
			TOOLBAR_jsecure::_LOG();
		break;

		default:
			TOOLBAR_jsecure::_DEFAULT();
		break;
	}

?>