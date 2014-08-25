<?php
/**
 * @version		$Id: default.php 1034 2011-10-04 17:00:00Z joomlaworks $
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
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if (\$K2.trim(\$K2('#group').val()) == '') {
			alert( '".JText::_('K2_PLEASE_SELECT_A_GROUP_OR_CREATE_A_NEW_ONE', true)."' );
		}
		else if (\$K2.trim(\$K2('#name').val()) == '') {
			alert( '".JText::_('K2_NAME_CANNOT_BE_EMPTY', true)."' );
		}
		else {
			submitform( pressbutton );
		}
	}
");

?>

<form action="index.php" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
  <table class="admintable">
    <tr>
      <td class="key"><?php echo JText::_('K2_NAME'); ?></td>
      <td><input class="text_area k2TitleBox" type="text" name="name" id="name" value="<?php echo $this->row->name; ?>" size="50" maxlength="250" /></td>
    </tr>
    <tr>
      <td class="key"><?php echo JText::_('K2_PUBLISHED'); ?></td>
      <td><?php echo $this->lists['published']; ?></td>
    </tr>
    <tr>
      <td class="key"><?php echo JText::_('K2_GROUP'); ?></td>
      <td>
        <?php echo $this->lists['group']; ?>
        <div id="groupContainer">
        	<span><?php echo JText::_('K2_NEW_GROUP_NAME'); ?></span>
        	<input id="group" type="text" name="group" value="<?php echo $this->row->group; ?>" />
        </div>
      </td>
    </tr>
    <tr>
      <td class="key"><?php echo JText::_('K2_TYPE'); ?></td>
      <td><?php echo $this->lists['type']; ?></td>
    </tr>
    <tr>
      <td class="key"><?php echo JText::_('K2_DEFAULT_VALUES'); ?></td>
      <td><div id="exFieldsTypesDiv"></div></td>
    </tr>
  </table>

  <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
  <input type="hidden" name="isNew" id="isNew" value="<?php echo ($this->row->group)?'0':'1'; ?>" />
  <input type="hidden" name="option" value="com_k2" />
  <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
  <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
  <input type="hidden" id="value" name="value" value="<?php echo htmlentities($this->row->value); ?>" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>
