<?php
/*------------------------------------------------------------------------
# mod_jo_k2_slideshow - JO k2 slide show item for Joomla 1.6, 1.7, 2.5 Module
# -----------------------------------------------------------------------
# author: http://www.joomcore.com
# copyright Copyright (C) 2011 Joomcore.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomcore.com
# Technical Support:  Forum - http://www.joomcore.com/Support
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.form.formfield');

class JFormFieldcolor extends JFormField
{
	protected $type = 'color';

	protected function getInput()
	{
		//var_dump($this->name);
		$jspath=JUri::root()."modules/mod_jo_k2_slideshow/js/jscolor.js";
		if($this->value==""){
			if($this->name == 'jform[params][overlay_link_color]'){
				$this->color = '7BC922'	;
			}elseif($this->name == 'jform[params][overlay_introtext_color]'){
				$this->color = 'FFFFFF'	;
			}elseif($this->name == 'jform[params][overlay_color]'){
				$this->color = '191919'	;
			}elseif($this->name == 'jform[params][overlay_text_color]'){
				$this->color = 'FFFFFF'	;
			}elseif($this->name == 'jform[params][overlay_heading_color]'){
				$this->color = '7BC922'	;
			}
		}else{
			$this->color = $this->value;
		}
		
		$document = &JFactory::getDocument();
		$document->addScript($jspath);
		$html = '<input type="text" size="20" class="color" value="'.$this->color.'" id="jform_params_'.$this->name.'" name="'.$this->name.'">';
		return $html;
	}
}
