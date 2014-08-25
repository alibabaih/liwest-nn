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

JHTML::_('behavior.mootools');
JHTML::_('script','system/modal.js', false, true);
JHTML::_('stylesheet','system/modal.css', array(), true);

$document =& JFactory::getDocument();
$document->addScriptDeclaration("window.addEvent('domready', function() {
			$$('.hasTip').each(function(el) {
				var title = el.get('title');
				if (title) {
					var parts = title.split('::', 2);
					el.store('tip:title', parts[0]);
					el.store('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
		});
		window.addEvent('domready', function() {

			SqueezeBox.initialize({});
			SqueezeBox.assign($$('a.modal'), {
				parse: 'rel'
			});
		});
");

?>
<form action="index.php?option=com_jsecure" method="post" name="adminForm">
<table class="adminlist" cellspacing="1">
<thead>
	<tr><th colspan=6 style='text-align:left;'><h1><?php echo JText::_( 'ADMIN_ACCESS_LOG' ); ?></h1></th></tr>
</thead>
<thead>
	<tr>
		<th width="5">
			<?php echo JText::_( 'Num' ); ?>
		</th>
		<th class="title">
			<?php echo JText::_( 'IP' ); ?>
		</th>
		<th class="title">
			<?php echo JText::_( 'User Name' ); ?>
		</th>
		<th class="title">
			<?php echo JText::_( 'Code' ); ?>
		</th>
		<th class="title">
			<?php echo JText::_( 'Log' ); ?>
		</th>
		<th class="title">
			<?php echo JText::_( 'Date' ); ?>
		</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<td colspan="15">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
<tbody>
	<?php
	$i=0;$k = 0;
	foreach($this->data as $row){
		$user = JUser::getInstance($row->userid);
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td>
			<?php echo  $this->pagination->getRowOffset( $i ); ?>
		</td>
		<td align="left">
			<a class="modal" title="IP INFO"  href="index.php?option=com_jsecure&amp;task=ipinfo&amp;ip=<?php echo $row->ip;?>&amp;tpl=component" rel="{handler: 'iframe', size: {x: 500, y: 350}}">		
				<?php echo $row->ip; ?>
			</a>
		</td>	
		<td align="left">
			<?php echo $user->username; ?>
		</td>
		<td align="left">
			<?php echo JText::_($row->code); ?>
		</td>
		<td align="left">
			<?php echo str_replace("\n","<br/>",$row->change_variable); ?>
		</td>
		<td align="left">
			<?php echo str_replace("\n","<br/>",$row->date); ?>
		</td>
	</tr>
	<?php
		$k = 1 - $k;	$i++;
	}
	?>
</tbody>
</table>
<input type="hidden" name="option" value="com_jsecure" />
<input type="hidden" name="task" value="log" />
<input type="hidden" name="boxchecked" value="0" />
</form>