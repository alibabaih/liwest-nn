<?php
/**
 * @version		$Id: default.php 1251 2011-10-19 17:50:13Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$document = & JFactory::getDocument();
$document->addScriptDeclaration("
	\$K2(document).ready(function(){
		\$K2('#K2ImportUsersButton').click(function(event){
			var answer = confirm('".JText::_('K2_WARNING_YOU_ARE_ABOUT_TO_IMPORT_JOOMLA_USERS_TO_K2_GENERATING_CORRESPONDING_K2_USER_GROUPS_IF_YOU_HAVE_EXECUTED_THIS_OPERATION_BEFORE_DUPLICATE_CONTENT_MAY_BE_PRODUCED', true)."');
			if (!answer){
				event.preventDefault();
			}
		});
	});
");

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="k2AdminTableFilters">
	<tr>
		<td class="k2AdminTableFiltersSearch">
			<?php echo JText::_('K2_FILTER'); ?>
			<input type="text" name="search" value="<?php echo $this->lists['search'] ?>" class="text_area"	title="<?php echo JText::_('K2_FILTER_BY_NAME'); ?>" />
			<button id="k2SubmitButton"><?php echo JText::_('K2_GO'); ?></button>
			<button id="k2ResetButton"><?php echo JText::_('K2_RESET'); ?></button>
		</td>
		<td class="k2AdminTableFiltersSelects">
			<?php echo $this->lists['filter_group_k2']; ?>
			<?php echo $this->lists['filter_group']; ?>
			<?php echo $this->lists['status']; ?>
		</td>
	</tr>
</table>
<table class="adminlist">
    <thead>
      <tr>
        <th>#</th>
        <th><input id="jToggler" type="checkbox" name="toggle" value="" /></th>
        <th><?php echo JHTML::_('grid.sort', 'K2_NAME', 'juser.name', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
        <th><?php echo JHTML::_('grid.sort', 'K2_USERNAME', 'juser.username', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
        <th><?php echo JText::_('K2_LOGGED_IN'); ?></th>
        <th><?php echo JHTML::_('grid.sort', 'K2_ENABLED', 'juser.block', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
        <th><?php echo JHTML::_('grid.sort', 'K2_JOOMLA_GROUP', 'juser.usertype', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
        <th><?php echo JHTML::_('grid.sort', 'K2_GROUP', 'groupname', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
        <th><?php echo JHTML::_('grid.sort', 'K2_EMAIL', 'juser.email', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
        <th><?php echo JHTML::_('grid.sort', 'K2_LAST_VISIT', 'juser.lastvisitDate', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
        <th><?php echo JHTML::_('grid.sort', 'K2_ID', 'juser.id', @$this->lists['order_Dir'], @$this->lists['order'] ); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php	foreach ($this->rows as $key => $row): ?>
      <tr class="row<?php echo ($key%2); ?>">
        <td><?php echo $key+1; ?></td>
        <td class="k2Center"><?php $row->checked_out = 0; echo JHTML::_('grid.id', $key, $row->id ); ?></td>
        <td><a href="<?php echo $row->link; ?>"><?php echo $row->name; ?></a></td>
        <td><?php echo $row->username; ?></td>
        <td class="k2Center"><?php $row->published = $row->loggedin; echo strip_tags(JHTML::_('grid.published', $row, $key ), '<img>'); ?></td>
        <td class="k2Center">
        <?php if($row->block): ?>
        <a title="<?php echo JText::_('K2_ENABLE'); ?>" onclick="return listItemTask('cb<?php echo $key; ?>','enable')" href="#"><img alt="<?php echo JText::_('K2_ENABLED'); ?>" src="<?php echo (K2_JVERSION == '16')? 'templates/'.$this->template.'/images/admin/': 'images/'; ?>publish_x.png"></a>
        <?php else: ?>
        <a title="<?php echo JText::_('K2_DISABLE'); ?>" onclick="return listItemTask('cb<?php echo $key; ?>','disable')" href="#"><img alt="<?php echo JText::_('K2_DISABLED'); ?>" src="<?php echo (K2_JVERSION == '16')? 'templates/'.$this->template.'/images/admin/': 'images/'; ?>tick.png"></a>
        <?php endif; ?>
        </td>
        <td><?php echo $row->usertype; ?></td>
        <td><?php echo $row->groupname; ?></td>
        <td><?php echo $row->email; ?></td>
        <td class="k2Date"><?php echo ($row->lvisit)?JHTML::_('date', $row->lvisit , $this->dateFormat):JText::_('K2_NEVER'); ?></td>
        <td><?php echo $row->id; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="11"><?php echo $this->page->getListFooter(); ?></td>
      </tr>
    </tfoot>
  </table>
  <input type="hidden" name="option" value="com_k2" />
  <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
  <input type="hidden" name="boxchecked" value="0" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>