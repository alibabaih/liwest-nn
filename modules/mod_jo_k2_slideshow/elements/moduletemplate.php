<?php
/*------------------------------------------------------------------------
# mod_jo_k2_slideshow - JO k2 slide show item for Joomla 1.6, 1.7, 2.5 Module
# -----------------------------------------------------------------------
# author: http://www.joomcore.com
# copyright Copyright (C) 2011 Joomcore.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomcore.com
# Technical Support:  Forum - http://www.joomcore.com/Support
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldModuletemplate extends JFormField {

		var	$type = 'moduletemplate';

		function getInput(){
			return JElementModuletemplate::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}
	}
}

jimport('joomla.html.parameter.element');

class JElementModuletemplate extends JElement
{

	var $_name = 'moduletemplate';

	function fetchElement($name, $value, &$node, $control_name) {

		jimport('joomla.filesystem.folder');

		if(K2_JVERSION == '16'){
			$moduleName = $node->getAttribute('modulename');
		}
		else {
			$moduleName = $node->_attributes['modulename'];
		}
		$moduleTemplatesPath = JPATH_SITE.DS.'modules'.DS.$moduleName.DS.'tmpl';
		$moduleTemplatesFolders = JFolder::folders($moduleTemplatesPath);

		$db =& JFactory::getDBO();
		if(K2_JVERSION == '16'){
			$query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
		}
		else {
			$query = "SELECT template FROM #__templates_menu WHERE client_id = 0 AND menuid = 0";
		}
		$db->setQuery($query);
		$defaultemplate = $db->loadResult();
		$templatePath = JPATH_SITE.DS.'templates'.DS.$defaultemplate.DS.'html'.DS.$moduleName;

		if (JFolder::exists($templatePath)){
			$templateFolders = JFolder::folders($templatePath);
			$folders = @array_merge($templateFolders, $moduleTemplatesFolders);
			$folders = @array_unique($folders);
		} else {
			$folders = $moduleTemplatesFolders;
		}

		$exclude = 'Default';
		$options = array ();

		foreach ($folders as $folder) {
			if (preg_match(chr(1).$exclude.chr(1), $folder)) {
				continue ;
			}
			$options[] = JHTML::_('select.option', $folder, $folder);
		}

		array_unshift($options, JHTML::_('select.option','Default','-- '.JText::_('K2_USE_DEFAULT').' --'));

		if(K2_JVERSION=='16'){
			$fieldName = $name;
		}
		else {
			$fieldName = $control_name.'['.$name.']';
		}
			
		return JHTML::_('select.genericlist', $options, $fieldName, 'class="inputbox"', 'value', 'text', $value, $control_name.$name);

	}

}
