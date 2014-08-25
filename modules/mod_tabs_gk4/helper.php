<?php

/**
* Helper class for GK Tab module
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
// import JString class for UTF-8 problems
jimport('joomla.utilities.string'); 
// Main GK Tab class
class GK4TabHelper {
	private $config; // configuration array
	private $tabs_content; // array of tabs content
	private $tabs_title; // array of tabs titles
	private $tabs_type; // array of tabs types
	private $mod_getter; // object to get the modules
	// constructor
	public function __construct($module, $params) {
		// initialize config array
		$this->config = array();
		// get the JSON tabs data
		$this->config['tabs_data'] = $params->get('tabs_data', '[]'); //
		// basic settings
		$this->config['automatic_module_id'] = $params->get('automatic_module_id', 1); //
		$this->config['module_id'] = ($this->config['automatic_module_id'] == 1) ? 'gkTab-' . $module->id : $params->get('module_id', 'gk-tab-1'); //
		$this->config['module_height'] = $params->get('module_height', 200); //
		// interface
		$this->config['buttons'] = $params->get('buttons', 1); //
		$this->config['tabs_position'] = $params->get('tabs_position', 'top'); //
		$this->config['styleCSS'] =  $params->get('styleCSS', 'style4'); //
		// animation
		$this->config['activator'] = $params->get('activator', 'click'); //
		$this->config['animation'] = $params->get('animation', 1); //
		$this->config['animation_speed'] = $params->get('animation_speed', 250); //
		$this->config['animation_interval'] = $params->get('animation_interval', 5000); //
		$this->config['animation_type'] = $params->get('animation_type', 'opacity'); //
		$this->config['animation_function'] = $params->get('animation_function', 'Fx.Transitions.linear'); //
		// advanced settings
		$this->config['url_tab_selection'] = $params->get('url_tab_selection', 1); //
		$this->config['cookie_tab_selection'] = $params->get('cookie_tab_selection', 0); //
		$this->config['parse_plugins'] = $params->get('parse_plugins', 1); //
		$this->config['useCSS'] = $params->get('useCSS', 1); //
		$this->config['useScript'] = $params->get('useScript', 1); //
		// parse JSON data
		$this->config['tabs_data'] = json_decode($this->config['tabs_data']);
	}
	// function to render module code
	public function render() {
		if(count($this->config['tabs_data']) == 0) {
			echo JText::_('MOD_TABS_GK4_NO_TABS_TO_SHOW');
			return false;
		}
		// create arrays for the content
		$this->tabs_titles = array();
		$this->tabs_content = array();
		$this->tabs_type = array();
		// get the user ID
		$user = JFactory::getUser();
		$registered_user = ($user->get('id', 0) != 0) ? true : false;
		// remove the unpublished or invisible for specified user tabs and put only necessary tabs
		for($i = 0; $i < count($this->config['tabs_data']); $i++) {
			if($this->config['tabs_data'][$i]->published == 1 && 
				($this->config['tabs_data'][$i]->access == 'public' || 
				($this->config['tabs_data'][$i]->access == 'registered' && $registered_user))) {
				// parse plugins code in the tab XHTML content
				if($this->config['parse_plugins'] == 1) {
					$this->config['tabs_data'][$i]->content = JHtml::_('content.prepare', $this->config['tabs_data'][$i]->content);
				}
				// put the data to specific array
				$this->tabs_titles[] = $this->config['tabs_data'][$i]->name;
				$this->tabs_content[] = $this->config['tabs_data'][$i]->content;
				$this->tabs_type[] = $this->config['tabs_data'][$i]->type;
			}
		}
		// create necessary instances of the Joomla! classes 
		$document = JFactory::getDocument();
		$uri = JURI::getInstance();
		// add stylesheets to document header
		if($this->config["useCSS"] == 1) {
			$document->addStyleSheet( $uri->root().'modules/mod_tabs_gk4/styles/'.$this->config['styleCSS'].'.css', 'text/css' );
		}
		// set active tab:
		$active_tab = 1;
		$uri_id_fragment = '';
		// puth height CSS rules
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('#'.$this->config['module_id'].' .gkTabContainer0, #'.$this->config['module_id'].' .gkTabContainer1, #'.$this->config['module_id'].' .gkTabContainer2 { height: '.$this->config['module_height'].'px; }');
		// if url selection is enabled
		if($this->config['url_tab_selection'] == 1) {
			if($uri->getVar('gktab', '') != '') {
				$active_tab = (int) $uri->getVar('gktab', '');
			}
		}	
		// if cookie selection is enabled
		if($this->config['cookie_tab_selection'] == 1) {
			if(isset($_COOKIE['gktab-' . $this->config['module_id']])) {
				$active_tab = (int) $_COOKIE['gktab-' . $this->config['module_id']];
			}
		}
		// check the active_tab value
		if($active_tab > count($this->config['tabs_data'])) {
			$active_tab = 1;
		}
		// getting module head section datas
		$headData = $document->getHeadData();
		// generate keys of script section
		$headData_keys = array_keys($headData["scripts"]);
		// set variable for false
		$engine_founded = false;
		// searching phrase mootools in scripts paths
		if(array_search($uri->root().'modules/mod_tabs_gk4/scripts/engine.js', $headData_keys) > 0) {
			// if founded set variable to true
			$engine_founded = true;
		}
		// if engine file doesn't exists in document head section
		if(!$engine_founded || $this->config['useScript'] == 1) {
			// add new script tag connected with mootools from module
			$document->addScript($uri->root().'modules/mod_tabs_gk4/scripts/engine.js');
		}
		// generate GK Tab configuration script
		$config_script = '//<![CDATA[
		try {$Gavick;}catch(e){$Gavick = {};};
		$Gavick["gktab-' . $this->config['module_id'] . '"] = {
			"activator" : "' . $this->config['activator'] . '",
			"animation" : ' . $this->config['animation'] .',
			"animation_speed" : ' . $this->config['animation_speed'] .',
			"animation_interval" : ' . $this->config['animation_interval'] .',
			"animation_type" : "' . $this->config['animation_type'] .'",
			"animation_function" : ' . $this->config['animation_function'] . ',
			"active_tab" : ' . $active_tab . ',
			"cookie_save" : ' . $this->config['cookie_tab_selection'] . '
		};
		//]]>';
		// put configuration code in the header
		$document->addScriptDeclaration($config_script);	
		// include main module view
		require(JModuleHelper::getLayoutPath('mod_tabs_gk4', 'default'));
	}
	// function to generate the module tabs
	public function moduleRender($active_tab) {	
		// iterate all tabs
		for($i = 0; $i < count($this->tabs_content); $i++) {
			// check if selected tab is active
			$active_class = ($active_tab == $i + 1) ? ' active' : '';
			// if the tab contains the module
			if($this->tabs_type[$i] == 'module') {
				$this->mod_getter = JModuleHelper::getModules($this->tabs_content[$i]);
				require(JModuleHelper::getLayoutPath('mod_tabs_gk4','module'));
			}
			// tabs with XHTML code
			if($this->tabs_type[$i] == 'xhtml') {
				require(JModuleHelper::getLayoutPath('mod_tabs_gk4','xhtml'));
			}
		}
	}
}

/* eof */