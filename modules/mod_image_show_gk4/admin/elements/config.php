<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class JFormFieldConfig extends JFormField {
	protected $type = 'Config';

	protected function getLabel() {
		return '';
	}

	protected function getInput() {
		$catalog_path = JPATH_SITE.DS.'modules'.DS.'mod_image_show_gk4'.DS.'styles';
		
		$folders = JFolder::folders($catalog_path);
		$options = array();
		
		$final_output = '';
		
		if(count($folders) > 0) {
			foreach($folders as $folder) {
				$output = '';
				// read XML file 
				$xml = &JFactory::getXMLParser('Simple');
				$result = $xml->loadFile($catalog_path.DS.$folder.DS.'info.xml');
				//
				foreach($xml->document->config[0]->field as $field) {
					$type = $field->attributes('type');
					
					$output .= '<li>' . $this->generateField($type, $field, $folder) . '</li>';
				}
				//
				$final_output .= '<div id="module_style_'.$folder.'" class="module_style"><ul class="adminformlist">' . $output . '</ul></div>';
			}
		} else {
			$final_output = 'Module have no styles. Please install some style package.';
		}
		
		$final_output .= '<textarea name="'.$this->name.'" id="'.$this->id.'" rows="20" cols="50">'.$this->value.'</textarea>';
		
		return $final_output;
	}
	//
	protected function generateField($type, $field, $style) {
		$id = $style . '_' . $field->attributes('name');
		
		switch($type) {
			case 'text': {
			 		// 
			 		$output = '<label id="'.$id.'-lbl" for="'.$id.'" class="hasTip" title="'.$field->attributes('desc').'">'.$field->attributes('label').'</label>';
			 		
			 		$unit_span = '';
			 		if($field->attributes('unit')) {
			 			$unit_span = '<span class="unit">'.$field->attributes('unit').'</span>';
			 		}
			 		$output .= '<input type="text" id="'.$id.'" value="'.$field->attributes('default').'" class="field">' . $unit_span;
			 		// 
			 		return $output;
				}
				break;
			case 'switch': {
					//
					$output = '<label id="'.$id.'-lbl" for="'.$id.'" class="hasTip" title="'.$field->attributes('desc').'">'.$field->attributes('label').'</label>';
					
					$output .= '<select id="'.$id.'" class="field '.$field->attributes('class').'">';
					
					$selected0 = '';
					$selected1 = '';
						
					if($field->attributes('default') == 0) {
						$selected0 = ' selected="selected"';
					} else {
						$selected1 = ' selected="selected"';
					}
						
					$output .= '<option value="0" '.$selected0.'>'.JText::_('MOD_IMAGE_SHOW_DISABLED').'</option>';
					$output .= '<option value="1" '.$selected1.'>'.JText::_('MOD_IMAGE_SHOW_ENABLED').'</option>';
					
					$output .= '</select>';
					//
					return $output;
				}
				break;
			case 'textarea': {
			 		$output = '<label id="'.$id.'-lbl" for="'.$id.'" class="hasTip" title="'.$field->attributes('desc').'">'.$field->attributes('label').'</label>';
			 		$output .= '<textarea id="'.$id.'" class="field '.$field->attributes('class').'" rows="'.$field->attributes('rows').'" cols="'.$field->attributes('cols').'"></textarea>';
			 		
			 		return $output;
				}
				break;
			case 'select': {
					$output = '<label id="'.$id.'-lbl" for="'.$id.'" class="hasTip" title="'.$field->attributes('desc').'">'.$field->attributes('label').'</label>';
					
					$output .= '<select id="'.$id.'" class="field '.$field->attributes('class').'">';
					
					foreach($field->option as $option) {
						$selected = '';
						
						if($option->attributes('value') == $field->attributes('value')) {
							$selected = ' selected="selected"';
						}
						
						$output .= '<option value="'.$option->attributes('value').'" '.$selected.'>'.$option->data().'</option>';
					}
					
					$output .= '</select>';
					//
					return $output;
				}
				break;
			default: 
				return ''; 
				break;
		}
	}
}

/* eof */