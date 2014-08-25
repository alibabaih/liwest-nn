<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldLastmod extends JFormField {
	protected $type = 'Lastmod';

	protected function getLabel() {
		return '';
	}

	protected function getInput() {
		return '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.time().'" />';
	}
}
