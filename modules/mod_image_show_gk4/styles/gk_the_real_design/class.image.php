<?php

/**
* @author: GavickPro
* @copyright: 2008-2009
**/
	
// no direct access
defined('_JEXEC') or die('Restricted access');

class GKIS_The_real_design_Image {
	/*
		function to change file path to filename.
		For example:
		./images/stories/demo.jpg
		will be translated to:
		stories.demo.jpg
		(in this situation mirror of ./images/ directory isn't necessary)
	*/
	function translateName($name, $mod_id) {
		$name = GKIS_The_real_design_Image::getRealPath($name);
		$start = strpos($name, DS.'images'.DS);
		$name = substr($name, $start+8);
		$ext = substr($name, -4);
		$name = substr($name, 0, -4);
		$name = str_replace(DS,'.',$name);
		$name .= $mod_id.$ext;
		return $name;
	}
	// function to change file path to  real path.
	function getRealPath($path) {
		$start = strpos($path, 'images/');
		$path = './'.substr($path, $start);
		return realpath($path);
	}
	/*
		function to check cache
		
		this function checks if file exists in cache directory
		and checks if time of file life isn't too long
	*/
	function checkCache($filename, $last_modification_time) {
		$cache_dir = JPATH_ROOT.DS.'modules'.DS.'mod_image_show_gk4'.DS.'cache'.DS;
		$file = $cache_dir.$filename;
		
		return (!is_file($file)) ? FALSE : (filemtime($file) > $last_modification_time);
	}
	// Creating thumbnails
	function createThumbnail($path, $config, $width, $height, $image_bg, $image_stretch, $quality) {
		if(GKIS_The_real_design_Image::checkCache(GKIS_The_real_design_Image::translateName($path,$config['module_id']), $config['last_modification'], $config['module_id'])){
			return TRUE;	
		}else{
			// importing classes
			jimport('joomla.filesystem.file');
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.path');
			//script configuration - increase memory limit to 64MB
			ini_set('memory_limit', '64M');
			// cache dir
			$cache_dir = JPATH_ROOT.DS.'modules'.DS.'mod_image_show_gk4'.DS.'cache'.DS;
			// file path
			$file = GKIS_The_real_design_Image::getRealPath($path);
			// filename
			$filename = GKIS_The_real_design_Image::translateName($path,$config['module_id']);
			// Getting informations about image
			if(is_file($file)){
				$imageData = getimagesize($file);
				// loading image depends from type of image		
				if($imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg') $imageSource = @imagecreatefromjpeg($file);
				elseif($imageData['mime'] == 'image/gif') $imageSource = @imagecreatefromgif($file);
				else $imageSource = @imagecreatefrompng($file); 
				// here can be exist an error when image is to big - then class return blank page	
				// setting image size in variables
				$imageSourceWidth = imagesx($imageSource);
				$imageSourceHeight = imagesy($imageSource);
				// Creating blank canvas
                $imageBG = imagecreatetruecolor($imageSourceWidth, $imageSourceHeight);
				// If image is JPG or GIF
				if($imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg' || $imageData['mime'] == 'image/gif') {
					// when bg is set to transparent - use black background
					if($image_bg == 'transparent'){
						$bgColorR = 0;
						$bgColorG = 0;
						$bgColorB = 0;				
					}else{ // in other situation - translate hex to RGB
						$bg = $image_bg;
						if(strlen($bg) == 4) $bg = $bg[0].$bg[1].$bg[1].$bg[2].$bg[2].$bg[3].$bg[3];
						$hex_color = strtolower(trim($bg,'#;&Hh'));
			  			$bg = array_map('hexdec',explode('.',wordwrap($hex_color, ceil(strlen($hex_color)/3),'.',1)));
						$bgColorR = $bg[0];
						$bgColorG = $bg[1];
						$bgColorB = $bg[2];
					}
					// Creating color
					$rgb = imagecolorallocate($imageBG, $bgColorR, $bgColorG, $bgColorB);
					// filling canvas with new color
					imagefill($imageBG, 0, 0, $rgb);	
				}else {// for PNG images	
                    $imageBG = imagecreatetruecolor($imageSourceWidth, $imageSourceHeight);
					// enable transparent background 
					if($image_bg == 'transparent'){
						// create transparent color
						$rgb = imagecolorallocatealpha($imageBG, 0, 0, 0, 127);
					}else {// create normal color
						$bg = $image_bg;
						// translate hex to RGB
						$hex_color = strtolower(trim($bg,'#;&Hh'));
			  			$bg = array_map('hexdec',explode('.',wordwrap($hex_color, ceil(strlen($hex_color)/3),'.',1)));
						// creating color
						$rgb = imagecolorallocate($imageBG, $bg[0], $bg[1], $bg[2]);
					}
					// filling the canvas
					imagefill($imageBG, 0, 0, $rgb);
					// enabling transparent settings for better quality
					imagealphablending($imageBG, false);
					imagesavealpha($imageBG, true);
				}
				// copy image	
				imagecopyresampled($imageBG, $imageSource, 0, 0, 0, 0, $imageSourceWidth, $imageSourceHeight, $imageSourceWidth, $imageSourceHeight);
				// save image depends from MIME type	
				if($imageData['mime'] == 'image/jpeg' || $imageData['mime'] == 'image/pjpeg' || $imageData['mime'] == 'image/jpg') imagejpeg($imageBG,$cache_dir.$filename, $quality);
				elseif($imageData['mime'] == 'image/gif') imagegif($imageBG, $cache_dir.$filename); 
				else imagepng($imageBG, $cache_dir.$filename);
				return TRUE;
			}else{
				return FALSE;
			}	
		}
	}	
}

/* eof */