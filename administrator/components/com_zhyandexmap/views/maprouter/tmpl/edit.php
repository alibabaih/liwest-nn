<?php
/*------------------------------------------------------------------------
# com_zhyandexmap - Zh YandexMap
# ------------------------------------------------------------------------
# author    Dmitry Zhuk
# copyright Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# Websites: http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$params = $this->form->getFieldsets('params');

?>
<form action="<?php echo JRoute::_('index.php?option=com_zhyandexmap&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'COM_ZHYANDEXMAP_MAPROUTER_DETAIL' ); ?></legend>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php 
         				if ($field->id == 'jform_mapid')
					{
						echo $field->label;
        					array_unshift($this->mapList, JHTML::_('select.option', '', JText::_( 'COM_ZHYANDEXMAP_MAPROUTER_FILTER_MAP'), 'value', 'text')); 
						echo JHTML::_( 'select.genericlist', $this->mapList, 'jform[mapid]',  'class="inputbox required" size="1"', 'value', 'text', (int)$this->item->mapid, 'jform_mapid');
						//echo $field->label;
						//echo $field->input;
					}
         			else if ($field->id == 'jform_descriptionhtml')
					{
						echo '<div class="clr"></div>';
						echo $field->label;
						echo '<div class="clr"></div>';
						echo $field->input;
					}
					else
					{
						echo $field->label;
						echo $field->input;
					}
					?>
				</li>
			<?php endforeach; ?>

		</ul>
	</fieldset>

</div>

<div  class="width-40 fltrt">
<?php echo JHtml::_('sliders.start', 'maprouter-slider'); ?>

<?php foreach ($params as $name => $fieldset): ?>
	<?php echo JHtml::_('sliders.panel', JText::_($fieldset->label), $name.'-params');?>
	<?php if (isset($fieldset->description) && trim($fieldset->description)): ?>
		<p class="tip"><?php echo $this->escape(JText::_($fieldset->description));?></p>
	<?php endif;?>
	<fieldset class="panelform" >
		<ul class="adminformlist">
		<?php foreach ($this->form->getFieldset($name) as $field) : ?>
			<li><?php echo $field->label; ?><?php echo $field->input; ?></li>
		<?php endforeach; ?>
		</ul>
	</fieldset>
<?php endforeach; ?>

<?php echo JHtml::_('sliders.end'); ?>
</div>



<div class="width-60 fltlft">
	<input type="hidden" name="task" value="maprouter.edit" />
	<?php echo JHtml::_('form.token'); ?>
</div>




</form>


