<?php
/*------------------------------------------------------------------------
# "Hot Film Tape" Joomla module
# Copyright (C) 2012 HotJoomlaTemplates.com. All Rights Reserved.
# License: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
# Author: HotJoomlaTemplates.com
# Website: http://www.hotjoomlatemplates.com
-------------------------------------------------------------------------*/
 
// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Path assignments
$mosConfig_absolute_path = JPATH_SITE;
$mosConfig_live_site = JURI :: base();
if(substr($mosConfig_live_site, -1)=="/") { $mosConfig_live_site = substr($mosConfig_live_site, 0, -1); }
 
// get parameters from the module's configuration
$enablejQuery = $params->get('enablejQuery','1');
$noConflict = $params->get('noConflict','1');
$elementID = $params->get('elementID','hot_film_tape');
$linkNewWindow = $params->get('linkNewWindow','0');
$responsive = $params->get('responsive','1');
$minVisible = $params->get('minVisible','2');
$maxVisible = $params->get('maxVisible','7');
$scrollAmount = $params->get('scrollAmount','1');
$pagination = $params->get('pagination','0');
$navigation = $params->get('navigation','1');
$timer = $params->get('timer','0');

for ($loop = 1; $loop <= 20; $loop += 1) {
$enableSlide[$loop] = $params->get('enableSlide'.$loop,'');
}

for ($loop = 1; $loop <= 20; $loop += 1) {
$imageContentArray[$loop] = $params->get('image'.$loop.'content','');
}

for ($loop = 1; $loop <= 20; $loop += 1) {
$imageLinkArray[$loop] = $params->get('image'.$loop.'link','');
}

for ($loop = 1; $loop <= 20; $loop += 1) {
$imageTitleArray[$loop] = $params->get('image'.$loop.'title','');
}

$autoSlideShow = $params->get('autoSlideShow','true');

$elementWidth = $params->get('elementWidth','240');
$elementHeight = $params->get('elementHeight','320');
$elementMargin = $params->get('elementMargin','10');
$elementPadding = $params->get('elementPadding','0');
$textColor = $params->get('textColor','#333333');
$borderSize = $params->get('borderSize','1');
$borderColor = $params->get('borderColor','#999999');
$buttonTextColor = $params->get('buttonTextColor','#FFFFFF');
$buttonColor = $params->get('buttonColor','#333333');
$buttonHoverColor = $params->get('buttonHoverColor','#89051c');

require(JModuleHelper::getLayoutPath('mod_hot_film_tape'));