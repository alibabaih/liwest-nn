<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldTabmanager extends JFormField {
	protected $type = 'Tabmanager';

	protected function getInput() {
		$add_form = '<div id="gk_tab_manager"><div id="gk_tab_add_header">'.JText::_('MOD_TABS_GK4_FORM_ITEMS_CREATED').'<a href="#add">'.JText::_('MOD_TABS_GK4_FORM_ITEM_ADD').'</a></div><div id="gk_tab_add_form">'.$this->getForm('add').'</div></div>';
		
		$edit_form = $this->getForm('edit');
		
		$item_form = '<div id="invisible"><div class="gk_tab_item"><div class="gk_tab_item_desc"><span class="gk_tab_item_name"></span><span class="gk_tab_item_order_down" title="'.JText::_('MOD_TABS_GK4_FORM_ITEM_ORDER_DOWN').'"></span><span class="gk_tab_item_order_up" title="'.JText::_('MOD_TABS_GK4_FORM_ITEM_ORDER_UP').'"></span><a href="#remove" class="gk_tab_item_remove" title="'.JText::_('MOD_TABS_GK4_FORM_ITEM_REMOVE').'">'.JText::_('MOD_TABS_GK4_FORM_ITEM_REMOVE').'</a><a href="#edit" class="gk_tab_item_edit" title="'.JText::_('MOD_TABS_GK4_FORM_ITEM_EDIT').'">'.JText::_('MOD_TABS_GK4_FORM_ITEM_EDIT').'</a><span class="gk_tab_item_type"></span><span class="gk_tab_item_access"></span><span class="gk_tab_item_state published"><span>'.JText::_('MOD_TABS_GK4_FORM_ITEM_PUBLISHED').'</span><span>'.JText::_('MOD_TABS_GK4_FORM_ITEM_UNPUBLISHED').'</span></span></div><div class="gk_tab_editor_scroll"><div class="gk_tab_item_editor">'.$edit_form.'</div></div></div></div>';
		
		$tabs_list = '<div id="tabs_list"></div>';
		$textarea = '<textarea name="'.$this->name.'" id="'.$this->id.'" rows="20" cols="50">'.$this->value.'</textarea>';
		return $item_form . $add_form . $tabs_list . $textarea;
	}
	
	private function getForm($type = 'add') {
        $module_position_select = '<select class="gk_tab_'.$type.'_content_module"><option value="tab1" selected="selected">tab1</option><option value="tab2">tab2</option><option value="tab3">tab3</option><option value="tab4">tab4</option><option value="tab5">tab5</option><option value="tab6">tab6</option><option value="tab7">tab7</option><option value="tab8">tab8</option><option value="tab9">tab9</option><option value="tab10">tab10</option></select>';
       
       	$form_name_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_TABS_GK4_FORM_NAME_TOOLTIP') . '"' : '';
        $form_name = '<p><label'.$form_name_tooltip.'>'.JText::_('MOD_TABS_GK4_FORM_NAME').'</label><input type="text" class="gk_tab_'.$type.'_name" /></p>';
        
        $form_type_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_TABS_GK4_FORM_TYPE_TOOLTIP') . '"' : '';
		$form_type = '<p><label'.$form_type_tooltip.'>'.JText::_('MOD_TABS_GK4_FORM_TYPE').'</label><select class="gk_tab_'.$type.'_type"><option value="module">'.JText::_('MOD_TABS_GK4_TYPE_MODULE').'</option><option value="xhtml" selected="selected">'.JText::_('MOD_TABS_GK4_TYPE_XHTML').'</option></select></p>';
		
		$form_access_level_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_TABS_GK4_FORM_ACCESS_TOOLTIP') . '"' : '';
		$form_access_level = '<p><label'.$form_access_level_tooltip.'>'.JText::_('MOD_TABS_GK4_FORM_ACCESS').'</label><select class="gk_tab_'.$type.'_content_access"><option value="public">'.JText::_('MOD_TABS_GK4_FORM_ACCESS_PUBLIC').'</option><option value="registered">'.JText::_('MOD_TABS_GK4_FORM_ACCESS_REGISTERED').'</option></select></p>';
		
		$form_content_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_TABS_GK4_FORM_CONTENT_TOOLTIP') . '"' : '';
		$form_content = '<p><label'.$form_content_tooltip.'>'.JText::_('MOD_TABS_GK4_FORM_CONTENT').'</label><textarea class="gk_tab_'.$type.'_content_xhtml"></textarea>' . $module_position_select . '<p>';
		
		$form_published_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_TABS_GK4_FORM_PUBLISHED_TOOLTIP') . '"' : '';
		$form_published = '<p><label'.$form_published_tooltip.'>'.JText::_('MOD_TABS_GK4_FORM_PUBLISHED').'</label><select class="gk_tab_'.$type.'_published"><option value="1">'.JText::_('MOD_TABS_GK4_PUBLISHED').'</option><option value="0">'.JText::_('MOD_TABS_GK4_UNPUBLISHED').'</option></select></p>';
		
		$form_buttons = '<div class="gk_tab_'.$type.'_submit"><a href="#save">'.JText::_('MOD_TABS_GK4_FORM_SAVE').'</a><a href="#cancel">'.JText::_('MOD_TABS_GK4_FORM_CANCEL').'</a></div>';
		
		$form = '<div class="height_scroll"><div class="gk_tab_'.$type.'">'.$form_name.$form_type.$form_access_level.$form_published.$form_content.$form_buttons.'</div></div>';
		
		return $form;
	}
}