<?php
/**
 * @version		$Id: default.php 1342 2011-11-25 16:34:43Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$document = & JFactory::getDocument();
$document->addScriptDeclaration("
	Joomla.submitbutton = function(pressbutton) {
		if (pressbutton == 'remove') {
			if (confirm('".JText::_('K2_WARNING_ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_EXTRA_FIELDS_GROUPS_DELETING_THE_GROUPS_WILL_ALSO_DELETE_THE_ASSIGNED_EXTRA_FIELDS', true)."')){
				submitform( pressbutton );
			}
		} else {
			submitform( pressbutton );
		}
	}
");

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
  <table class="adminlist">
    <thead>
      <tr>
        <th>#</th>
        <th><input id="jToggler" type="checkbox" name="toggle" value="" /></th>
        <th><?php echo JText::_('K2_GROUP_NAME'); ?></th>
        <th><?php echo JText::_('K2_ASSIGNED_CATEGORIES'); ?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="4"><?php echo $this->page->getListFooter(); ?></td>
      </tr>
    </tfoot>
    <tbody>
    <?php foreach ($this->rows as $key=>$row): ?>
      <tr class="row<?php echo ($key%2); ?>">
        <td align="center"><?php echo $key+1; ?></td>
        <td align="center"><?php $row->checked_out = 0; echo JHTML::_('grid.checkedout', $row, $key ); ?></td>
        <td><a href="<?php echo JRoute::_('index.php?option=com_k2&view=extrafieldsgroup&cid='.$row->id); ?>"><?php echo $row->name; ?></a></td>
        <td><?php echo $row->categories; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <input type="hidden" name="option" value="com_k2" />
  <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>
