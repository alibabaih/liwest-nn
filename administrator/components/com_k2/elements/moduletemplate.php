<?php
/**
 * @version		$Id: moduletemplate.php 1113 2011-10-11 14:39:02Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

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
