<?php
/**
 * @version		$Id: mod_k2_quickicons.php 1293 2011-10-30 18:30:19Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$user = &JFactory::getUser();

if(K2_JVERSION == '16'){
	$language = &JFactory::getLanguage();
	$language->load('mod_k2.j16', JPATH_ADMINISTRATOR);
	if($user->authorise('core.admin', 'com_k2')){
		$user->gid = 1000;
	}
	else {
		$user->gid = 1;
	}
}

// JoomlaWorks reference parameters
$mod_name 							= "mod_k2_quickicons";
$mod_copyrights_start 	= "\n\n<!-- JoomlaWorks \"K2 QuickIcons\" Module starts here -->\n";
$mod_copyrights_end 		= "\n<!-- JoomlaWorks \"K2 QuickIcons\" Module ends here -->\n\n";

// API
$mainframe	= &JFactory::getApplication();
$document 	= &JFactory::getDocument();
$user = &JFactory::getUser();

// Module parameters
$moduleclass_sfx 	= $params->get('moduleclass_sfx','');
$modCSSStyling		= (int) $params->get('modCSSStyling',1);
$modLogo					= (int) $params->get('modLogo',1);

// Component parameters
$componentParams = &JComponentHelper::getParams('com_k2');

$onlineImageEditor = $componentParams->get('onlineImageEditor','splashup');

switch($onlineImageEditor){
	case 'splashup':
		$onlineImageEditorLink = 'http://splashup.com/splashup/';
		break;
	case 'aviary':
		$onlineImageEditorLink = 'http://www.aviary.com/online/image-editor';
		break;
	case 'picnik':
		$onlineImageEditorLink = 'http://www.picnik.com/app';
		break;
	case 'sumopaint':
		$onlineImageEditorLink = 'http://www.sumopaint.com/app/';
		break;
	case 'pixlr':
		$onlineImageEditorLink = 'http://pixlr.com/editor/';
		break;
}

// Call the modal and add some needed JS
JHTML::_('behavior.modal');

$document->addScriptDeclaration("
	window.addEvent('domready', function() {
		$('k2OnlineImageEditor').addEvent('click', function(e){
			e.preventDefault();
			var w = 1040, h = 700, l = (document.documentElement.clientWidth-w)/2, t = (document.documentElement.clientHeight-h)/2;
			window.open(this, 'splashupEditor', 'height='+h+', width='+w+', left='+l+', top ='+t+', toolbar=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes');
		});
	});
");

// Append CSS to the document's head
if($modCSSStyling) $document->addStyleSheet(JURI::base(true).'/modules/'.$mod_name.'/tmpl/css/style.css');

// Output content with template
echo $mod_copyrights_start;
require(JModuleHelper::getLayoutPath($mod_name,'default'));
echo $mod_copyrights_end;
