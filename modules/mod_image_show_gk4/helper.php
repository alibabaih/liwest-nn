<?php

/**
* Helper class for Image Show module
*
* GK Tab
* @package Joomla!
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @ version $Revision: GK4 1.0 $
**/

// access restriction
defined('_JEXEC') or die('Restricted access');
// Main GK Tab class
class GK4ImageShowHelper {
	// configuration array
	private $config;
	// module info
	private $module;
	// constructor
	public function __construct($module, $params) {
		// initialize config array
		$this->config = array();
		// init the module info
		$this->module = $module;
		// basic settings
		$this->config['automatic_module_id'] = $params->get('automatic_module_id', 1); //
		$this->config['module_id'] = ($this->config['automatic_module_id'] == 1) ? 'gk-is-' . $module->id : $params->get('module_id', 'gk-is-1'); //
		$this->config['styles'] = $params->get('module_style', 'gk_coffe');
		// get the JSON slides and config data
		$this->config['image_show_data'] = $params->get('image_show_data', '[]');
		$this->config['config'] = $params->get('config', '{}');
		$this->config['last_modification'] = $params->get('last_modification', 0);
		// parse JSON data
		$this->config['image_show_data'] = json_decode($this->config['image_show_data']);
		$this->config['config'] = json_decode($this->config['config']);
		// advanced
		$this->config['use_style_css'] = $params->get('use_style_css', 1);
	}
	// function to render module code
	public function render() {
		// include style Controller
		require_once('styles'.DS.$this->config['styles'].DS.'controller.php');	
		// initialize Controller
		$controller_class = 'GKIS_' . $this->config['styles'] . '_Controller';
		$controller = new $controller_class($this->module, $this->config);
	}
}

/* eof */