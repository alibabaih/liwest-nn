<?php
/**
 * @version		$Id: header.php 1364 2011-11-25 19:14:50Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldHeader extends JFormField {

		var	$type = 'header';

		function getInput(){
			return JElementHeader::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}

		function getLabel(){
			return '';
		}

	}
}

jimport('joomla.html.parameter.element');

class JElementHeader extends JElement {

	var	$_name = 'header';

	function fetchElement($name, $value, &$node, $control_name){
		$document = & JFactory::getDocument();
		$document->addStyleSheet(JURI::root(true).'/media/k2/assets/css/k2.modules.css');
		if(K2_JVERSION=='16'){
			return '<div class="paramHeaderContainer"><div class="paramHeaderContent">'.JText::_($value).'</div><div class="k2clr"></div></div>';
		} else {
			return '<div class="paramHeaderContainer15"><div class="paramHeaderContent">'.JText::_($value).'</div><div class="k2clr"></div></div>';
		}
	}

	function fetchTooltip($label, $description, &$node, $control_name, $name){
		return NULL;
	}
}
