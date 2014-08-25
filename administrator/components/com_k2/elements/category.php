<?php
/**
 * @version		$Id: category.php 1295 2011-10-30 19:22:36Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION=='16'){
	jimport('joomla.form.formfield');
	class JFormFieldCategory extends JFormField {

		var	$type = 'category';

		function getInput(){
			return JElementCategory::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
		}

	}
}

jimport('joomla.html.parameter.element');

class JElementCategory extends JElement
{

	var	$_name = 'category';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = &JFactory::getDBO();
		$query = 'SELECT m.* FROM #__k2_categories m WHERE published = 1 ORDER BY parent, ordering';
		$db->setQuery( $query );
		$mitems = $db->loadObjectList();
		$children = array();

		if ( $mitems )
		{
			foreach ( $mitems as $v )
			{
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
		if($name=='categories' || $name=='jform[params][categories]'){
			$doc = & JFactory::getDocument();
			$js = "
			window.addEvent('domready', function(){
				setTask();
			});
			
			function setTask() {
				var counter=0;
				$$('#paramscategories option').each(function(el) {
					if (el.selected){
						value=el.value;
						counter++;
					}
				});
				if (counter>1 || counter==0){
					$('urlparamsid').setProperty('value','');
					$('urlparamstask').setProperty('value','');
					$('paramssingleCatOrdering').setProperty('disabled', 'disabled');
					enableParams();
				}
				if (counter==1){
					$('urlparamsid').setProperty('value',value);
					$('urlparamstask').setProperty('value','category');
					$('paramssingleCatOrdering').removeProperty('disabled');
					disableParams();
				}
			}
			
			function disableParams(){
				$('paramsnum_leading_items').setProperty('disabled','disabled');
				$('paramsnum_leading_columns').setProperty('disabled','disabled');
				$('paramsleadingImgSize').setProperty('disabled','disabled');
				$('paramsnum_primary_items').setProperty('disabled','disabled');
				$('paramsnum_primary_columns').setProperty('disabled','disabled');
				$('paramsprimaryImgSize').setProperty('disabled','disabled');
				$('paramsnum_secondary_items').setProperty('disabled','disabled');
				$('paramsnum_secondary_columns').setProperty('disabled','disabled');
				$('paramssecondaryImgSize').setProperty('disabled','disabled');
				$('paramsnum_links').setProperty('disabled','disabled');
				$('paramsnum_links_columns').setProperty('disabled','disabled');
				$('paramslinksImgSize').setProperty('disabled','disabled');
				$('paramscatCatalogMode').setProperty('disabled','disabled');
				$('paramscatFeaturedItems').setProperty('disabled','disabled');
				$('paramscatOrdering').setProperty('disabled','disabled');
				$('paramscatPagination').setProperty('disabled','disabled');
				$('paramscatPaginationResults0').setProperty('disabled','disabled');
				$('paramscatPaginationResults1').setProperty('disabled','disabled');
				$('paramscatFeedLink0').setProperty('disabled','disabled');
				$('paramscatFeedLink1').setProperty('disabled','disabled');
				$('paramscatFeedIcon0').setProperty('disabled','disabled');
				$('paramscatFeedIcon1').setProperty('disabled','disabled');
				$('paramstheme').setProperty('disabled','disabled');
			}
			
			function enableParams(){
				$('paramsnum_leading_items').removeProperty('disabled');
				$('paramsnum_leading_columns').removeProperty('disabled');
				$('paramsleadingImgSize').removeProperty('disabled');
				$('paramsnum_primary_items').removeProperty('disabled');
				$('paramsnum_primary_columns').removeProperty('disabled');
				$('paramsprimaryImgSize').removeProperty('disabled');
				$('paramsnum_secondary_items').removeProperty('disabled');
				$('paramsnum_secondary_columns').removeProperty('disabled');
				$('paramssecondaryImgSize').removeProperty('disabled');
				$('paramsnum_links').removeProperty('disabled');
				$('paramsnum_links_columns').removeProperty('disabled');
				$('paramslinksImgSize').removeProperty('disabled');
				$('paramscatCatalogMode').removeProperty('disabled');
				$('paramscatFeaturedItems').removeProperty('disabled');
				$('paramscatOrdering').removeProperty('disabled');
				$('paramscatPagination').removeProperty('disabled');
				$('paramscatPaginationResults0').removeProperty('disabled');
				$('paramscatPaginationResults1').removeProperty('disabled');
				$('paramscatFeedLink0').removeProperty('disabled');
				$('paramscatFeedLink1').removeProperty('disabled');
				$('paramscatFeedIcon0').removeProperty('disabled');
				$('paramscatFeedIcon1').removeProperty('disabled');
				$('paramstheme').removeProperty('disabled');
			}
			";
			
			if(K2_JVERSION == '16') {
				$js = "
				function disableParams(){
					$('jform_params_num_leading_items').setProperty('disabled','disabled');
					$('jform_params_num_leading_columns').setProperty('disabled','disabled');
					$('jform_params_leadingImgSize').setProperty('disabled','disabled');
					$('jform_params_num_primary_items').setProperty('disabled','disabled');
					$('jform_params_num_primary_columns').setProperty('disabled','disabled');
					$('jform_params_primaryImgSize').setProperty('disabled','disabled');
					$('jform_params_num_secondary_items').setProperty('disabled','disabled');
					$('jform_params_num_secondary_columns').setProperty('disabled','disabled');
					$('jform_params_secondaryImgSize').setProperty('disabled','disabled');
					$('jform_params_num_links').setProperty('disabled','disabled');
					$('jform_params_num_links_columns').setProperty('disabled','disabled');
					$('jform_params_linksImgSize').setProperty('disabled','disabled');
					$('jform_params_catCatalogMode').setProperty('disabled','disabled');
					$('jform_params_catFeaturedItems').setProperty('disabled','disabled');
					$('jform_params_catOrdering').setProperty('disabled','disabled');
					$('jform_params_catPagination').setProperty('disabled','disabled');
					$('jform_params_catPaginationResults0').setProperty('disabled','disabled');
					$('jform_params_catPaginationResults1').setProperty('disabled','disabled');
					$('jform_params_catFeedLink0').setProperty('disabled','disabled');
					$('jform_params_catFeedLink1').setProperty('disabled','disabled');
					$('jform_params_catFeedIcon0').setProperty('disabled','disabled');
					$('jform_params_catFeedIcon1').setProperty('disabled','disabled');
					$('jformparamstheme').setProperty('disabled','disabled');
				}
				
				function enableParams(){
					$('jform_params_num_leading_items').removeProperty('disabled');
					$('jform_params_num_leading_columns').removeProperty('disabled');
					$('jform_params_leadingImgSize').removeProperty('disabled');
					$('jform_params_num_primary_items').removeProperty('disabled');
					$('jform_params_num_primary_columns').removeProperty('disabled');
					$('jform_params_primaryImgSize').removeProperty('disabled');
					$('jform_params_num_secondary_items').removeProperty('disabled');
					$('jform_params_num_secondary_columns').removeProperty('disabled');
					$('jform_params_secondaryImgSize').removeProperty('disabled');
					$('jform_params_num_links').removeProperty('disabled');
					$('jform_params_num_links_columns').removeProperty('disabled');
					$('jform_params_linksImgSize').removeProperty('disabled');
					$('jform_params_catCatalogMode').removeProperty('disabled');
					$('jform_params_catFeaturedItems').removeProperty('disabled');
					$('jform_params_catOrdering').removeProperty('disabled');
					$('jform_params_catPagination').removeProperty('disabled');
					$('jform_params_catPaginationResults0').removeProperty('disabled');
					$('jform_params_catPaginationResults1').removeProperty('disabled');
					$('jform_params_catFeedLink0').removeProperty('disabled');
					$('jform_params_catFeedLink1').removeProperty('disabled');
					$('jform_params_catFeedIcon0').removeProperty('disabled');
					$('jform_params_catFeedIcon1').removeProperty('disabled');
					$('jformparamstheme').removeProperty('disabled');
				}
				
				function setTask() {
					var counter=0;
					$$('#jformparamscategories option').each(function(el) {
						if (el.selected){
							value=el.value;
							counter++;
						}
					});
					if (counter>1 || counter==0){
						$('jform_request_id').setProperty('value','');
						$('jform_request_task').setProperty('value','');
						$('jform_params_singleCatOrdering').setProperty('disabled', 'disabled');
						enableParams();
					}
					if (counter==1){
						$('jform_request_id').setProperty('value',value);
						$('jform_request_task').setProperty('value','category');
						$('jform_params_singleCatOrdering').removeProperty('disabled');
						disableParams();
					}
				}
				
				window.addEvent('domready', function(){
					if($('request-options')) {
						$$('.panel')[0].setStyle('display', 'none');
					}
					setTask();
				});
				";
			}
			
			$doc->addScriptDeclaration($js);
		}

		foreach ( $list as $item ) {
			$item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
			@$mitems[] = JHTML::_('select.option',  $item->id,  $item->treename );
		}
		
		if(K2_JVERSION=='16'){
			$fieldName = $name.'[]';
		}
		else {
			$fieldName = $control_name.'['.$name.'][]';
		}
		
		if($name=='categories' || $name=='jform[params][categories]'){
			$onChange = 'onchange="setTask();"';
		}
		else {
			$onChange = '';
		}

		return JHTML::_('select.genericlist',  $mitems, $fieldName, $onChange.' class="inputbox" style="width:90%;" multiple="multiple" size="15"', 'value', 'text', $value );

	}

}
