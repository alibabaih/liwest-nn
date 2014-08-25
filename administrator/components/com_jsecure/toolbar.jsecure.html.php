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
 * @version     $Id: toolbar.jsecure.html.php  $
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
class TOOLBAR_jsecure {
			  
	function _help(){
		JToolBarHelper::title( JText::_( 'jSecure Authentication Help' ), 'generic.png' );
	} 
	
	function _AUTH(){
		JToolBarHelper::title( JText::_( 'jSecure Authentication' ), 'generic.png' );
	}
	
	function _LOG(){
		JToolBarHelper::title( JText::_( 'jSecure Authentication' ), 'generic.png' );
	}

	function _DEFAULT(){
		JToolBarHelper::title( JText::_( 'jSecure Authentication' ), 'generic.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::custom( 'help', 'help.png', 'help.png', 'Help', false, false );
	}
	
}

?>