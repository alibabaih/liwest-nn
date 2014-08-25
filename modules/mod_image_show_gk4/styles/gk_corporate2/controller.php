<?php

/**
* GK Image Show - main PHP file
* @package Joomla!
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @ version $Revision: GK4 1.0 $
**/

// no direct access
defined('_JEXEC') or die;
// Image class loading
require_once (dirname(__FILE__).DS.'class.image.php');
// Model class loading
require_once (dirname(__FILE__).DS.'model.php');

class GKIS_gk_corporate2_Controller {
	// configuration array
	private $config;
	// module info
	private $module;
	// article data
	private $articles;
    private $articlesK2;
	// constructor
	function __construct($module, $config) {
		// init the style config
		$this->config = $config;
		// init the module info
		$this->module = $module;
		// init the articles array
		$this->articles = array();
        $this->articlesK2 = array();
		// check the module images
		$this->checkImages();
		// get the articles data
		$this->getArticleData();
		// generate the view
		$this->generateView();
	}
	// check the images
	function checkImages() {
		// basic images params		
		$img_width = $this->config['config']->gk_corporate2->gk_corporate2_image_width;
		$img_height = $this->config['config']->gk_corporate2->gk_corporate2_image_height;
		$img_bg = $this->config['config']->gk_corporate2->gk_corporate2_image_bg;
		$quality = $this->config['config']->gk_corporate2->gk_corporate2_quality;
		// check the slides
		foreach($this->config['image_show_data'] as $slide) {
			$stretch = ($slide->stretch == 'nostretch') ? false : true;
			GKIS_corporate2_Image::createThumbnail($slide->image, $this->config, $img_width, $img_height, $img_bg, $stretch, $quality);	
		}
	}
	// get the articles data
	function getArticleData() {
		// create the array
		$ids = array();
        $idsK2 = array();
		// generate the content of the array
		foreach($this->config['image_show_data'] as $slide) {
			if($slide->type == 'article') {
				array_push($ids, $slide->art_id);
			}
            if($slide->type == 'k2') {
				array_push($idsK2, $slide->artK2_id);
			}
		}
		// get the data
		if(count($idsK2) > 0) {
			$this->articlesK2 = GKIS_gk_corporate2_Model::getDataK2($idsK2);
		}
		if(count($ids) > 0) {
			$this->articles = GKIS_gk_corporate2_Model::getData($ids);
		}
	}
 
	// generate view
	function generateView() {
		// generate the head section
		$document = JFactory::getDocument();
		$uri = JURI::getInstance();
		// get the head data
		$headData = $document->getHeadData();
		// generate keys of script section
		$headData_js_keys = array_keys($headData["scripts"]);
		// generate keys of css section
		$headData_css_keys = array_keys($headData["style"]);
		// set variables for false
		$engine_founded = false;
		$css_founded = false;
		// searching engine in scripts paths
		if(array_search($uri->root().'modules/mod_image_show_gk4/styles/'.$this->config['styles'].'/engine.js', $headData_js_keys) > 0) {
			$engine_founded = true;
		}
		// searching css in CSSs paths
		if(array_search($uri->root().'modules/mod_image_show_gk4/styles/'.$this->config['styles'].'/style.css', $headData_css_keys) > 0) {
			$css_founded = true;
		}
		// if mootools file doesn't exists in document head section
		if(!$engine_founded){ 
			// add new script tag connected with mootools from module
			$document->addScript($uri->root().'modules/mod_image_show_gk4/styles/'.$this->config['styles'].'/engine.js');
		}
		// if CSS not found
		if(!$css_founded && $this->config['use_style_css'] == 1) {
			// add stylesheets to document header
			$document->addStyleSheet($uri->root().'modules/mod_image_show_gk4/styles/'.$this->config['styles'].'/style.css' );
		}
		// add script fragment
		$document->addScriptDeclaration('try {$Gavick;}catch(e){$Gavick = {};};$Gavick["gkIs-'.$this->config['module_id'].'"] = { "anim_speed": '.$this->config['config']->gk_corporate2->gk_corporate2_animation_speed.', "anim_interval": '.$this->config['config']->gk_corporate2->gk_corporate2_animation_interval.', "autoanimation": '.$this->config['config']->gk_corporate2->gk_corporate2_autoanimation.', "anim_type": "'.$this->config['config']->gk_corporate2->gk_corporate2_animation_type.'" };');
		// generate necessary variables
		$width = $this->config['config']->gk_corporate2->gk_corporate2_image_width;
		$height = $this->config['config']->gk_corporate2->gk_corporate2_image_height;
		$interface = $this->config["config"]->gk_corporate2->gk_corporate2_interface;
		$interface_animation = $this->config["config"]->gk_corporate2->gk_corporate2_interface_animation;
		$image_position = $this->config["config"]->gk_corporate2->gk_corporate2_image_position;
		$image_position_margin = ($image_position == 'gkFloatLeft') ? 'left' : 'right';
		$show_list = $this->config['config']->gk_corporate2->gk_corporate2_show_list;
		$list_position = $this->config["config"]->gk_corporate2->gk_corporate2_list_position;
		$art_width = $this->config['config']->gk_corporate2->gk_corporate2_art_width;
		$list_title_small = $this->config['config']->gk_corporate2->gk_corporate2_list_title_small;
		$list_title_big = $this->config['config']->gk_corporate2->gk_corporate2_list_title_big; 
		$list_char_count = $this->config['config']->gk_corporate2->gk_corporate2_list_char_count;
		$list_text_overflow = $this->config['config']->gk_corporate2->gk_corporate2_list_text_overflow;
		$item_title = $this->config['config']->gk_corporate2->gk_corporate2_title;
		$title_link = $this->config['config']->gk_corporate2->gk_corporate2_title_link;
		$readmore_button = $this->config['config']->gk_corporate2->gk_corporate2_readmore_button;
		$readmore_text = $this->config['config']->gk_corporate2->gk_corporate2_readmore_text;
		$wordcount = $this->config['config']->gk_corporate2->gk_corporate2_wordcount;
		$clean_xhtml = $this->config['config']->gk_corporate2->gk_corporate2_clean_xhtml;
		// load view
		require(dirname(__FILE__).DS.'view.php');
	}
}

/* eof */