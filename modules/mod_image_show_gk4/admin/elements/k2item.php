<?php
/**
 * @version		$Id: item.php 785 2011-04-28 12:39:17Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks, a business unit of Nuevvo Webware Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldK2Item extends JFormField {

		var	$type = 'item';

		function getInput(){
			return JElementK2Item::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}


	}
}

jimport('joomla.html.parameter.element');

class JElementK2Item extends JElement {

	var $_name = 'Item';

	function fetchElement($name, $value, & $node, $control_name) {

		$mainframe = &JFactory::getApplication();

		$db = & JFactory::getDBO();
		$doc = & JFactory::getDocument();
		$fieldName = (K2_JVERSION=='16')?$name:$control_name.'['.$name.']';
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
		$item = & JTable::getInstance('K2Item', 'Table');

		if ($value) {
			$item->load($value);
		}
		else {
			$item->title = JText::_('K2_SELECT_AN_ITEM');
		}

	

		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object='.$name;

		JHTML::_('behavior.modal', 'a.modal');

		$html = '
		<div style="float:left;">
			<input style="background:#fff;margin:3px 0;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" />
		</div>
		<div class="button2-left">
			<div class="blank">
				<a class="modal" title="'.JText::_('K2_SELECT_AN_ITEM').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 700, y: 450}}">'.JText::_('K2_SELECT').'</a>
			</div>
		</div>
		<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.( int )$value.'" />
		';

		return $html;
	}

}

