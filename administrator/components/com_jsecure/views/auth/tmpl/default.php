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
 * @version     $Id: default.php  $
 */
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document =& JFactory::getDocument();
$document->addScript(JURI::base()."components/com_jsecure/js/auth.js");
?>

<form action="index.php" method="post" name="adminForm">
	<table class="adminlist" cellspacing="1">
		<tr>
			<td width="105"><?php echo JText::_('MASTER_PASSWORD'); ?></td>
			<td><input type="password" name="master_password" class="textarea" value="" size="50" /></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="" value="<?php echo JText::_('JSECURE_LOGIN'); ?>" /></td>
		</tr>
	</table>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_jsecure" />
	<input type="hidden" name="task" value="login" />
	<input type="hidden" name="view" value="auth" />
</form>