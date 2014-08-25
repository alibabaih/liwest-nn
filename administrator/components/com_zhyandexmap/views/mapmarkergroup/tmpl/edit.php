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
		<legend><?php echo JText::_( 'COM_ZHYANDEXMAP_MAPMARKERGROUP_DETAIL' ); ?></legend>
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php 
					if ($field->id == 'jform_icontype')
					{
						echo $field->label;

						$imgpath = JURI::root() .'administrator/components/com_zhyandexmap/assets/icons/';

						$iconTypeJS = " onchange=\"javascript:
						if (document.forms.adminForm.jform_icontype.options[selectedIndex].value!='') 
						{document.image.src='".$imgpath."' + document.forms.adminForm.jform_icontype.options[selectedIndex].value.replace(/#/g,'%23') + '.png'}
						else 
						{document.image.src=''}\"";


						$scriptPosition = ' name=';

						echo str_replace($scriptPosition, $iconTypeJS.$scriptPosition, $field->input);
						echo '<img name="image" src="'.$imgpath .str_replace("#", "%23", $this->item->icontype).'.png" alt="" />';
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
<?php echo JHtml::_('sliders.start', 'mapmarkergroup-slider'); ?>

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
	<input type="hidden" name="task" value="mapmarkergroup.edit" />
	<?php echo JHtml::_('form.token'); ?>
</div>




</form>


