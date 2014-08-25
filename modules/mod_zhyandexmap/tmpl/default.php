<?php
/*------------------------------------------------------------------------
# mod_zhyandexmap - Zh YandexMap Module
# ------------------------------------------------------------------------
# author    Dmitry Zhuk
# copyright Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# Websites: http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$componentApiVersion = modZhYandexMapHelper::getAPIVersion();

if ($componentApiVersion == "")
{
	$componentApiVersion = '2.x';
}

if ($componentApiVersion == '2.x')
{
	require_once(JPATH_SITE.'/modules/mod_zhyandexmap/tmpl/v2x.php');
}
else
{
	require_once(JPATH_SITE.'/modules/mod_zhyandexmap/tmpl/v1x.php');
}
