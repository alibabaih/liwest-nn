<?php
/*------------------------------------------------------------------------
# com_zhyandexmap - Zh YandexMap
# ------------------------------------------------------------------------
# author    Dmitry Zhuk
# copyright Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# Websites: http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Zh YandexMap Component Controller
 */
class ZhYandexMapController extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false, $urlparams = false) 
	{
		$vName	= JRequest::getCmd('view', 'ZhYandexMaps');

		JRequest::setVar('view', $vName);

		// call parent behavior
		parent::display($cachable, $urlparams);
		
		// Set the submenu
		ZhYandexMapHelper::addSubmenu($vName);

		return $this;

	}
}
