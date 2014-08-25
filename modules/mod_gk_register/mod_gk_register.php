<?php

/**
* Main file
* @package GK Register GK4
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

// no direct access
defined('_JEXEC') or die;

jimport( 'joomla.form.form' );

$path = JPATH_BASE.DS.'modules'.DS.'mod_gk_register'.DS.'registration.xml';

$gkform = JForm::getInstance($path, $path, array('control' => 'jform', 'load_data' => true));

require JModuleHelper::getLayoutPath('mod_gk_register', $params->get('layout', 'default'));