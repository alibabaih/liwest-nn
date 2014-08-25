<?php
/**
 * @version		$Id: categoriesmultiple.php 1351 2011-11-25 17:04:53Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldCategoriesMultiple extends JFormField {

		var	$type = 'categoriesmultiple';

		function getInput(){
			return JElementCategoriesMultiple::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}
	}
}

jimport('joomla.html.parameter.element');

class JElementCategoriesmultiple extends JElement
{

	var	$_name = 'categoriesmultiple';

	function fetchElement($name, $value, &$node, $control_name){
		$params = &JComponentHelper::getParams('com_k2');
		
		$document = &JFactory::getDocument();
		
		if(version_compare(JVERSION,'1.6.0','ge')) {
			JHtml::_('behavior.framework');
		} else {
			JHTML::_('behavior.mootools');
		}

		$backendJQueryHandling = $params->get('backendJQueryHandling','remote');
		if($backendJQueryHandling=='remote'){
			$document->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js');
			//$document->addScript('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js');
		} else {
			$document->addScript(JURI::root(true).'/media/k2/assets/js/jquery-1.7.1.min.js');
			//$document->addScript(JURI::root(true).'/media/k2/assets/js/jquery-ui-1.8.16.custom.min.js');
		}
		
		$db = &JFactory::getDBO();
		$query = 'SELECT m.* FROM #__k2_categories m WHERE published=1 AND trash = 0 ORDER BY parent, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		$children = array();
		if ($mitems){
			foreach ( $mitems as $v ){
				if(K2_JVERSION=='16'){
					$v->title = $v->name;
					$v->parent_id = $v->parent;
				}
				$pt = $v->parent;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );
		$mitems = array();

		foreach ( $list as $item ) {
			$item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
			$mitems[] = JHTML::_('select.option',  $item->id, '   '.$item->treename );
		}

		$doc = & JFactory::getDocument();
		if(K2_JVERSION=='16'){
			$js = "
			var \$K2 = jQuery.noConflict();
			\$K2(document).ready(function(){
				
				\$K2('#jform_params_catfilter0').click(function(){
					\$K2('#jformparamscategory_id').attr('disabled', 'disabled');
					\$K2('#jformparamscategory_id option').each(function() {
						\$K2(this).attr('selected', 'selected');
					});
				})
				
				\$K2('#jform_params_catfilter1').click(function(){
					\$K2('#jformparamscategory_id').removeAttr('disabled');
					\$K2('#jformparamscategory_id option').each(function() {
						\$K2(this).removeAttr('selected');
					});
	
				})
				
				if (\$K2('#jform_params_catfilter0').attr('checked')) {
					\$K2('#jformparamscategory_id').attr('disabled', 'disabled');
					\$K2('#jformparamscategory_id option').each(function() {
						\$K2(this).attr('selected', 'selected');
					});
				}
				
				if (\$K2('#jform_params_catfilter1').attr('checked')) {
					\$K2('#jformparamscategory_id').removeAttr('disabled');
				}
				
			});
			";			
				
		}
		else {
			$js = "
			var \$K2 = jQuery.noConflict();
			\$K2(document).ready(function(){
				
				\$K2('#paramscatfilter0').click(function(){
					\$K2('#paramscategory_id').attr('disabled', 'disabled');
					\$K2('#paramscategory_id option').each(function() {
						\$K2(this).attr('selected', 'selected');
					});
				})
				
				\$K2('#paramscatfilter1').click(function(){
					\$K2('#paramscategory_id').removeAttr('disabled');
					\$K2('#paramscategory_id option').each(function() {
						\$K2(this).removeAttr('selected');
					});
	
				})
				
				if (\$K2('#paramscatfilter0').attr('checked')) {
					\$K2('#paramscategory_id').attr('disabled', 'disabled');
					\$K2('#paramscategory_id option').each(function() {
						\$K2(this).attr('selected', 'selected');
					});
				}
				
				if (\$K2('#paramscatfilter1').attr('checked')) {
					\$K2('#paramscategory_id').removeAttr('disabled');
				}
				
			});
			";			
				
				
		}

		if(K2_JVERSION=='16'){
			$fieldName = $name.'[]';
		}
		else {
			$fieldName = $control_name.'['.$name.'][]';
		}

		$doc->addScriptDeclaration($js);
		$output= JHTML::_('select.genericlist',  $mitems, $fieldName, 'class="inputbox" style="width:90%;" multiple="multiple" size="10"', 'value', 'text', $value );
		return $output;
	}
}
