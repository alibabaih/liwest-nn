<?php
/**
 * @version		$Id: mod_k2_stats.php 1385 2011-12-06 10:46:38Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

if(K2_JVERSION == '16'){
	$language = &JFactory::getLanguage();
	$language->load('mod_k2.j16', JPATH_ADMINISTRATOR);
}

require_once(dirname(__FILE__).DS.'helper.php');

if($params->get('latestItems', 1)){
	$latestItems = modK2StatsHelper::getLatestItems();
}
if($params->get('popularItems', 1)){
	$popularItems = modK2StatsHelper::getPopularItems();
}
if($params->get('mostCommentedItems', 1)){
	$mostCommentedItems = modK2StatsHelper::getMostCommentedItems();
}
if($params->get('latestComments', 1)){
	$latestComments = modK2StatsHelper::getLatestComments();
}
if($params->get('statistics', 1)){
	$statistics = modK2StatsHelper::getStatistics();
}

require(JModuleHelper::getLayoutPath('mod_k2_stats'));
