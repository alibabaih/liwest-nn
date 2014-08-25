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
 * @version     $Id: uninstall.jsecure.php  $
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$database	= & JFactory::getDBO();
jimport('joomla.filesystem.file');

// remove system plugin
	$database->setQuery( "DELETE FROM `#__extensions` WHERE `element`= 'jsecure';");
	$database->query();

	JFile::delete( JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecure'.DS.'jsecure.php' );
	JFile::delete( JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecure'.DS.'jsecure.xml' );
	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecure'.DS.'404.html'); 
	JFile::delete(JPATH_ADMINISTRATOR.DS.'language'.DS.'en-GB'.DS.'en-GB.plg_system_jsecure.ini');

	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecure'.DS.'jsecure'.DS.'jsecure.class.php');
	JFile::delete(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecure'.DS.'jsecure'.DS.'css'.DS.'jsecure.css');

	rmdir(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecure'.DS.'jsecure'.DS.'css');
	rmdir(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecure'.DS.'jsecure');
	rmdir(JPATH_ROOT.DS.'plugins'.DS.'system'.DS.'jsecure');

	echo '<h3>jSecure has been succesfully uninstalled.</h3>';