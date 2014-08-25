<?php
/**
 * @version		$Id: mod_k2_users.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if(K2_JVERSION == '16'){
	$language = &JFactory::getLanguage();
	$language->load('mod_k2.j16', JPATH_ADMINISTRATOR, null, true);
}

require_once(dirname(__FILE__).DS.'helper.php');

// Params
$moduleclass_sfx = $params->get('moduleclass_sfx','');
$getTemplate = $params->get('getTemplate','Default');
$userName = $params->get('userName',1);
$userAvatar = $params->get('userAvatar',1);
$userAvatarWidthSelect = $params->get('userAvatarWidthSelect','custom');
$userAvatarWidth = $params->get('userAvatarWidth',50);
$userDescription = $params->get('userDescription',1);
$userDescriptionWordLimit = $params->get('userDescriptionWordLimit');
$userURL = $params->get('userURL',1);
$userEmail = $params->get('userEmail',0);
$userFeed = $params->get('userFeed',1);
$userItemCount = $params->get('userItemCount',1);

// User avatar
if($userAvatarWidthSelect=='inherit'){
	$componentParams = &JComponentHelper::getParams('com_k2');
	$avatarWidth = $componentParams->get('userImageWidth');
} else {
	$avatarWidth = $userAvatarWidth;
}

$users = modK2UsersHelper::getUsers($params);

require(JModuleHelper::getLayoutPath('mod_k2_users', $getTemplate.DS.'default'));
