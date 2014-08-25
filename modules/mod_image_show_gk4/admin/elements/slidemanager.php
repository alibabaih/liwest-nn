<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldSlidemanager extends JFormField {
	protected $type = 'Slidemanager';

	protected function getInput() {
		$add_form = '<div id="gk_tab_manager"><div id="gk_tab_add_header">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEMS_CREATED').'<a href="#add">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_ADD').'</a></div><div id="gk_tab_add_form">'.$this->getForm('add').'</div></div>';
		
		$edit_form = $this->getForm('edit');
		
		$item_form = '<div id="invisible"><div class="gk_tab_item"><div class="gk_tab_item_desc"><span class="gk_tab_item_name"></span><span class="gk_tab_item_order_down" title="'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_ORDER_DOWN').'"></span><span class="gk_tab_item_order_up" title="'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_ORDER_UP').'"></span><a href="#remove" class="gk_tab_item_remove" title="'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_REMOVE').'">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_REMOVE').'</a><a href="#edit" class="gk_tab_item_edit" title="'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_EDIT').'">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_EDIT').'</a><span class="gk_tab_item_type"></span><span class="gk_tab_item_access"></span><span class="gk_tab_item_state published"><span>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_PUBLISHED').'</span><span>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_UNPUBLISHED').'</span></span><a rel="{handler:\'image\'}" class="gk-modal modal-img" title="'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_PREVIEW').'">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ITEM_PREVIEW').'</a></div><div class="gk_tab_editor_scroll"><div class="gk_tab_item_editor">'.$edit_form.'</div></div></div></div>';
		
		$tabs_list = '<div id="tabs_list"></div>';
		$textarea = '<textarea name="'.$this->name.'" id="'.$this->id.'" rows="20" cols="50">'.$this->value.'</textarea>';
		return $item_form . $add_form . $tabs_list . $textarea;
	}
	
	private function getForm($type = 'add') {
        // form_type
        $form_type_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_TYPE_TOOLTIP') . '"' : '';
        $form_type = '<p><label'.$form_type_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_TYPE').'</label><select class="gk_tab_'.$type.'_type"><option value="article">'.JText::_('MOD_IMAGE_SHOW_GK4_TYPE_ARTICLE').'</option><option value="text" selected="selected">'.JText::_('MOD_IMAGE_SHOW_GK4_TYPE_TEXT').'</option><option value="k2">'.JText::_('MOD_IMAGE_SHOW_GK4_TYPE_K2').'</option></select></p>';
        
        // form_image
        $form_image_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_IMAGE_TOOLTIP') . '"' : '';
        $form_image = '';
        if($type == 'add') {
    		// Build the script.
    		$script = array();
    		$script[] = '	function jInsertFieldValue(value,id) {';
    		$script[] = '		var old_id = document.getElementById(id).value;';
    		$script[] = '		if (old_id != id) {';
    		$script[] = '			document.getElementById(id).value = value;';
    		$script[] = '		}';
    		$script[] = '	}';
    		// Add the script to the document head.
    		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
        	// label
        	$form_image .= '<p><label'.$form_image_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_IMAGE_TYPE').'</label>';
        	// The text field.
        	$form_image .= '<div class="fltlft"><input type="text" name="jform_params_img" id="jform_params_img" value="" readonly="readonly" class="gk_tab_'.$type.'_image" /></div>';
        	// The button.
        	$form_image .= '<div class="button2-left"><div class="blank"><a class="gk-modal modal-media" title="'.JText::_('JSELECT').'" href="index.php?option=com_media&view=images&tmpl=component&asset=&author=&fieldid=jform_params_img&folder=" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_('JSELECT').'</a></div></div>';

        	$form_image .= '<div class="button2-left"><div class="blank"><a title="'.JText::_('JCLEAR').'" href="#" onclick="javascript:document.getElementById(\'jform_params_img\').value=\'\';return false;">'.JText::_('JCLEAR').'</a></div></div></p>';
        } else {
        	$form_image = '';
        	// label
        	$form_image .= '<p><label'.$form_image_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_IMAGE_TYPE').'</label>';
        	// The text field.
        	$form_image .= '<div class="fltlft"><input type="text" name="jform_params_edit_img" id="jform_params_edit_img" value="" readonly="readonly" class="gk_tab_'.$type.'_image" /></div>';
        	// The button.
        	$form_image .= '<div class="button2-left"><div class="blank"><a class="gk-modal modal-media" title="'.JText::_('JSELECT').'" href="index.php?option=com_media&view=images&tmpl=component&asset=&author=&fieldid=jform_params_edit_img&folder=" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">'.JText::_('JSELECT').'</a></div></div>';
        	
        	$form_image .= '<div class="button2-left"><div class="blank"><a title="'.JText::_('JCLEAR').'" href="#" onclick="javascript:document.getElementById(\'jform_params_edit_img\').value=\'\';return false;" class="modal-media-clear">'.JText::_('JCLEAR').'</a></div></div></p>';
        }
        
        // form_stretch
        $form_stretch_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_STRETCH_TOOLTIP') . '"' : '';
        $form_stretch = '<p><label'.$form_stretch_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_STRETCH').'</label><select class="gk_tab_'.$type.'_stretch"><option value="nostretch" selected="selected">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_OPTION_NOSTRETCH').'</option><option value="stretch">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_OPTION_STRETCH').'</option></select></p>';
        
        // form_access_level
        $form_access_level_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_ACCESS_TOOLTIP') . '"' : '';
        $form_access_level = '<p><label'.$form_access_level_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ACCESS').'</label><select class="gk_tab_'.$type.'_content_access"><option value="public">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ACCESS_PUBLIC').'</option><option value="registered">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ACCESS_REGISTERED').'</option></select></p>';
        
        // form_published
        $form_published_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_PUBLISHED_TOOLTIP') . '"' : '';
        $form_published = '<p><label'.$form_published_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_PUBLISHED').'</label><select class="gk_tab_'.$type.'_published"><option value="1">'.JText::_('MOD_IMAGE_SHOW_GK4_PUBLISHED').'</option><option value="0">'.JText::_('MOD_IMAGE_SHOW_GK4_UNPUBLISHED').'</option></select></p>';
        
        // form_name
        $form_name_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_NAME_TOOLTIP') . '"' : '';
        $form_name = '<p><label'.$form_name_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_NAME').'</label><input type="text" class="gk_tab_'.$type.'_name" /></p>';
        
        // form_content
        $form_content_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_CONTENT_TOOLTIP') . '"' : '';
        $form_content = '<p><label'.$form_content_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_CONTENT').'</label><textarea class="gk_tab_'.$type.'_content"></textarea><p>';
        
        // form_url
        $form_url_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_URL_TOOLTIP') . '"' : '';
        $form_url = '<p><label'.$form_url_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_URL').'</label><input type="text" class="gk_tab_'.$type.'_url" /></p>';;
         // form_article K2
        if($type == 'add') {
            $form_articleK2_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_ARTICLEK2_TOOLTIP') . '"' : '';
        	$form_articleK2 = '<div class="gk_tab_add_artK2"><label'.$form_article_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ARTICLEK2').'</label><div class="fltlft"><input type="text" id="jform_request_artK2_name" value="" disabled="disabled" size="25"></div><div class="button2-left"><div class="blank"><a class="gk-modal modal-art" title="Select or Change article" href="index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object=jform_request_artK2_add";function=jSelectItem_add" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">Select / Change</a></div></div><input type="hidden" id="jform_request_artK2_add" class="modal-value" name="jform[request][id]" value="" /></div>';
        } else {
        $form_articleK2 = '<div class="gk_tab_edit_artK2"><label>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ARTICLEK2').'</label><div class="fltlft"><input type="text" id="jform_request_edit_artK2_name" class="modal-artK2-name" value="" disabled="disabled" size="25"></div><div class="button2-left"><div class="blank"><a class="gk-modal modal-art" title="Select or Change article" href="index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object=jform_request_edit_artK2";function=jSelectItem2" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">Select / Change</a></div></div><input type="hidden" id="jform_request_edit_artK2" class="modal-value jform_request_edit_artK2" name="jform[request][id]" value="" /></div>';
        }
        JFactory::getDocument()->addScriptDeclaration('function jSelectItem(id, title, object) { 
                if($currently_opened != 0) { 
                document.id("jform_request_edit_artK2_" +  $currently_opened).value = id; 
                document.id("jform_request_edit_artK2_name_" + $currently_opened).value = title; 
                } else {
                document.id("jform_request_artK2_add").value = id; 
                document.id("jform_request_artK2_name").value = title; 
                }
                SqueezeBox.close();}');
       // href="index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object=jform_artK2"
        // form_articlestyles
         if($type == 'add') {
        	$form_article_tooltip = ($type == 'add') ? ' class="hasTip" title="' . JText::_('MOD_IMAGE_SHOW_GK4_FORM_ARTICLE_TOOLTIP') . '"' : '';
        	$form_article = '<div class="gk_tab_add_art"><label'.$form_article_tooltip.'>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ARTICLE').'</label><div class="fltlft"><input type="text" id="jform_request_art_name" value="" disabled="disabled" size="25"></div><div class="button2-left"><div class="blank"><a class="gk-modal modal-art" title="Select or Change article" href="index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_jform_request_art_add" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">Select / Change</a></div></div><input type="hidden" id="jform_request_art_add" class="modal-value" name="jform[request][id]" value="" /></div>';
        
        	JFactory::getDocument()->addScriptDeclaration('function jSelectArticle_jform_request_art_add(id, title, catid, object) { document.id("jform_request_art_add").value = id; document.id("jform_request_art_name").value = title; SqueezeBox.close(); }');
        } else {
        	$form_article = '<div class="gk_tab_edit_art"><label>'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_ARTICLE').'</label><div class="fltlft"><input type="text" id="jform_request_edit_art_name" class="modal-art-name" value="" disabled="disabled" size="25"></div><div class="button2-left"><div class="blank"><a class="gk-modal modal-art" title="Select or Change article" href="index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_jform_request_edit_art" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">Select / Change</a></div></div><input type="hidden" id="jform_request_edit_art" class="modal-value jform_request_edit_art" name="jform[request][id]" value="" /></div>';
        	
        	JFactory::getDocument()->addScriptDeclaration('function jSelectArticle_jform_request_edit_art(id, title, catid, object) { document.id("jform_request_edit_art_" + $currently_opened).value = id; document.id("jform_request_edit_art_name_" + $currently_opened).value = title; SqueezeBox.close(); }');
        }
        
        // form_buttons
        $form_buttons = '<div class="gk_tab_'.$type.'_submit"><a href="#save">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_SAVE').'</a><a href="#cancel">'.JText::_('MOD_IMAGE_SHOW_GK4_FORM_CANCEL').'</a></div>';
        
        
		// create the form
		$form = '<div class="height_scroll"><div class="gk_tab_'.$type.'">'.$form_type.$form_image.$form_stretch.$form_access_level.$form_published.$form_name.$form_content.$form_url.$form_article.$form_articleK2.$form_buttons.'</div></div>';
		
		return $form;
	}
}