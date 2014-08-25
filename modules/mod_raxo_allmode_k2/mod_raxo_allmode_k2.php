<?php
/*
 * =============================================================
 * RAXO All-mode K2 J2.5
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @copyright	Copyright (C) 2009-2012 RAXO Group
 * @license		GNU General Public License v2.0 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link		http://raxo.org
 * =============================================================
 */

// no direct access
defined('_JEXEC') or die;

// Check the type of display page
if ($params->def('hide_option', 0) && (JRequest::getCmd('option') === 'com_k2' && JRequest::getCmd('view') === 'item')) {
	return;
}

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

// Module cache
$cacheparams = new stdClass;
// Cache Mode:
//	'itemid'	- (default) suitable for most module settings.
//	'safeuri'	- use this mode if you set 'Random' in module settings.
$cacheparams->cachemode = 'itemid';
$cacheparams->class = 'modRAXO_Allmode_K2';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = array('id'=>'int','Itemid'=>'int');

$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);
if (!count($list)) {return;}

// Template name
$tmpl			= $params->def('layout', 'allmode-default');
$tmpl_name		= explode(':', $tmpl);
$tmpl_name		= $tmpl_name[1];

?>
<div class="allmode-box <?php echo $tmpl_name.' '.htmlspecialchars($params->get('moduleclass_sfx')); ?>">
<?php

// Block name
$blockname_text	= trim($params->get('name_text'));
$blockname_link	= trim($params->get('name_link'));
if ($blockname_text && $blockname_link) {
	echo '<h3 class="allmode-name"><a href="'.$blockname_link.'"><span>'.$blockname_text.'</span></a></h3>';
} elseif ($blockname_text) {
	echo '<h3 class="allmode-name"><span>'.$blockname_text.'</span></h3>';
}

// TOP Items
$count_top = (int) $params->get('count_top', 2);
$toplist = '';
if ($count_top) {
	$toplist	= array_slice($list, 0, $count_top);
	$list		= array_slice($list, $count_top);
}

// Output template
require JModuleHelper::getLayoutPath('mod_raxo_allmode_k2', $tmpl);

// Show all link
$showall_text	= trim($params->get('showall_text'));
$showall_link	= trim($params->get('showall_link'));
if ($showall_text && $showall_link) {
	echo '<div class="allmode-showall"><a href="'.$showall_link.'">'.$showall_text.'</a></div>';
} elseif ($showall_text) {
	echo '<div class="allmode-showall">'.$showall_text.'</div>';
}
?>
</div>