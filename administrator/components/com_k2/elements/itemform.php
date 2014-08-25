<?php
/**
 * @version		$Id$
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldItemForm extends JFormField {

		var	$type = 'itemForm';

		function getInput(){
			return JElementItemForm::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}

		function getLabel(){
			return '';
		}

	}
}

jimport('joomla.html.parameter.element');

class JElementItemForm extends JElement {

	var	$_name = 'itemForm';

	function fetchElement($name, $value, &$node, $control_name){
		$document = & JFactory::getDocument();
		$document->addScriptDeclaration("
			window.addEvent('domready', function() {
				if($('request-options')) {
					$$('.panel')[0].setStyle('display', 'none');
				}
				if($('jform_browserNav')) {
					$('jform_browserNav').setProperty('value', 2);
					$('jform_browserNav').getElements('option')[0].destroy();
				}
				if($('browserNav')) {
					$('browserNav').setProperty('value', 2);
					options = $('browserNav').getElements('option');
					if(options.length == 3) {
						options[0].remove();
					}
				}				
			});
		");
		return '';
	}

	function fetchTooltip($label, $description, &$node, $control_name, $name){
		return '';
	}
}
