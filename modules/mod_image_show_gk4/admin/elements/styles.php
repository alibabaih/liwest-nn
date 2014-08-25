<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class JFormFieldStyles extends JFormField {
	protected $type = 'Styles';

	protected function getInput() {
		$catalog_path = JPATH_SITE.DS.'modules'.DS.'mod_image_show_gk4'.DS.'styles';
		
		$folders = JFolder::folders($catalog_path);
		$options = array();
		
		if(count($folders) > 0) {
			foreach($folders as $folder) {
				array_push($options, JHTML::_( 'select.option', $folder, $folder ));
			}
		} else {
			return 'Module have no styles. Please install some style package.';
		}
		
		return JHTML::_('select.genericlist', $options, 'jform[params][module_style]', '', 'value', 'text', $this->value, 'jform_params_module_style');
	}
}

/* eof */