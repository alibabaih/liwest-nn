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

?>

<form action="index.php" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
  <table class="admintable">
    <tr>
      <td class="key"><?php	echo JText::_('K2_NAME'); ?></td>
      <td><?php echo $this->row->name; ?></td>
    </tr>
    <tr>
      <td class="key"><?php	echo JText::_('K2_GENDER'); ?></td>
      <td><?php echo $this->lists['gender']; ?></td>
    </tr>
    <tr>
      <td class="key"><?php	echo JText::_('K2_USER_GROUP'); ?></td>
      <td><?php echo $this->lists['userGroup']; ?></td>
    </tr>
    <tr>
      <td class="key"><?php echo JText::_('K2_DESCRIPTION'); ?></td>
      <td>
  			<div class="k2ItemFormEditor">
  				<?php echo $this->editor; ?>
					<div class="dummyHeight"></div>
					<div class="clr"></div>
				</div>
			</td>
    </tr>
    <tr>
      <td class="key"><?php echo JText::_('K2_USER_IMAGE_AVATAR'); ?></td>
      <td>
      	<input type="file" name="image" />
        <?php if($this->row->image): ?>
        <img class="k2AdminImage" src="<?php echo JURI::root().'media/k2/users/'.$this->row->image; ?>" alt="<?php echo $this->row->name; ?>" />
        <input type="checkbox" name="del_image" id="del_image" />
        <label for="del_image"><?php echo JText::_('K2_CHECK_THIS_BOX_TO_DELETE_CURRENT_IMAGE_OR_JUST_UPLOAD_A_NEW_IMAGE_TO_REPLACE_THE_EXISTING_ONE'); ?></label>
        <?php endif; ?></td>
    </tr>
    <tr>
      <td class="key"><?php	echo JText::_('K2_URL'); ?></td>
      <td><input type="text" size="50" value="<?php echo $this->row->url; ?>" name="url" /></td>
    </tr>
  </table>
  
	<?php if(count(array_filter($this->K2Plugins))): ?>
	<?php foreach ($this->K2Plugins as $K2Plugin): ?>
	<?php if(!is_null($K2Plugin)): ?>
	<fieldset class="adminform">
		<legend><?php echo $K2Plugin->name; ?></legend>
		<?php echo $K2Plugin->fields; ?>
	</fieldset>
	<?php endif; ?>
	<?php endforeach; ?>
	<?php endif; ?>
	
  <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
  <input type="hidden" name="option" value="com_k2" />
  <input type="hidden" name="view" value="user" />
  <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
  <input type="hidden" name="userID" value="<?php echo $this->row->userID; ?>" />
  <?php echo JHTML::_('form.token'); ?>
</form>