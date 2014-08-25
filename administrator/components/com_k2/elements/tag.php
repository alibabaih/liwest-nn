<?php
/**
 * @version		$Id: tag.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldTag extends JFormField {

		var	$type = 'tag';

		function getInput(){
			return JElementTag::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}
	}
}

jimport('joomla.html.parameter.element');

class JElementTag extends JElement
{

	var $_name = 'Tag';

	function fetchElement($name, $value, & $node, $control_name)
	{

		$mainframe = &JFactory::getApplication();

		$db = & JFactory::getDBO();
		$doc = & JFactory::getDocument();
		$fieldName = (K2_JVERSION=='16')? $name : $control_name.'['.$name.']';
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
		$tag = & JTable::getInstance('K2Tag', 'Table');

		if ($value) {

			$db = &JFactory::getDBO();
			$query = "SELECT * FROM #__k2_tags WHERE name=".$db->Quote($value);
			$db->setQuery( $query );
			$tag = $db->loadObject();

		}
		else {
			$tag->name = JText::_('K2_SELECT_A_TAG');
		}
		
		// Move this to main JS file
		$js = "
		function jSelectTag(id, title, object) {
			document.getElementById('".$name."' + '_id').value = id;
			document.getElementById('".$name."' + '_name').value = title;
			if(typeof(window.parent.SqueezeBox.close=='function')){
				window.parent.SqueezeBox.close();
			}
			else {
				document.getElementById('sbox-window').close();
			}
		}
		";

		$doc->addScriptDeclaration($js);

		$link = 'index.php?option=com_k2&amp;view=tags&amp;task=element&amp;tmpl=component&amp;object='.$name;

		JHTML::_('behavior.modal', 'a.modal');

		$html = '
		<div style="float:left;">
			<input style="background:#fff;margin:3px 0;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($tag->name, ENT_QUOTES, 'UTF-8').'" disabled="disabled" />
		</div>
		<div class="button2-left">
			<div class="blank">
				<a class="modal" title="'.JText::_('K2_SELECT_A_TAG').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 700, y: 450}}">'.JText::_('K2_SELECT').'</a>
			</div>
		</div>
		<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.$value.'" />
		';

		return $html;
	}

}

