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
defined('_JEXEC') or die ('Restricteted access');

require_once (dirname(__FILE__).DS.'helper.php');
$doc = &Jfactory::getDocument();

$doc->addStyleSheet(JURI::root(true).'/modules/mod_jo_k2_slideshow/css/style.css');

if($params->get('left_right') == 'left') {
	$doc->addStyleSheet(JURI::root(true).'/modules/mod_jo_k2_slideshow/css/left.css');
}	

$doc->addScript(JURI::root(true).'/modules/mod_jo_k2_slideshow/js/jquery-1.3.2.min.js');
$doc->addScript(JURI::root(true).'/modules/mod_jo_k2_slideshow/js/jquery.easing.1.3.min.js');
$doc->addScript(JURI::root(true).'/modules/mod_jo_k2_slideshow/js/script.js');

$list = modJoK2SliderShow::getList($params);

require(JModuleHelper::getLayoutPath('mod_jo_k2_slideshow'));
?>