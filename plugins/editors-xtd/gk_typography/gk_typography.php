<?php
 
/**
* Typography plugin
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @version $Revision: GK4 1.0 $
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgButtonGK_Typography extends JPlugin {

    function plgButtonGK_Typography(&$subject, $config) {
        parent::__construct($subject, $config);
    }

    function onDisplay($name) {
        $option = JRequest::getCmd('option');
        
        if($option != 'com_virtuemart') {
	        // parse filename and template
	        $selected = $this->params->get('typo_override');
			// create button
	        $button = new JObject();
	        $doc = JFactory::getDocument();
	        // include scripts
	        $doc->addStyleSheet('../plugins/editors-xtd/gk_typography/gk_typography/css/gk_typography.css');
	        $doc->addScript('../plugins/editors-xtd/gk_typography/gk_typography/js/gk_typography.js', "text/javascript");
	        $acl = JFactory::getACL();
	        $doc->addScriptDeclaration('$GKEditor = "' . $name . '";');
	        $app = JFactory::getApplication();
	        $template = $app->getTemplate();
	        //settings for button
	        JHtml::_('behavior.modal');
	        $button->set('modal', true);
	        //$button->set('link', $link);
	        $button->set('text', JText::_('Typography'));
	        $button->set('name', 'gk_typography');
	        $button->set('options', "{handler:'clone',target:$$('.gk_typography_content')[0],size:{x:800,y:690}}");
	        
			if($selected != 'error') {
		        $template_name = substr($selected, 0, strpos($selected, ':'));
		        $file = substr($selected, strpos($selected, ':') + 1, strlen($selected));
		        // parse XML
		        $path = JPATH_SITE . DS . 'templates' . DS . $template_name . DS . 'typography' . DS . $file;
		        $xml = JFactory::getXMLParser('Simple');
		        $result = $xml->loadFile($path);
		        // check content view
		        $app = JFactory::getApplication();
		        $params = JComponentHelper::getParams('com_media');
		        // start parsing  XML file
		        // script section
		        if($result == true){ // parse only when file load correctly
			        $generated_content = '<script type="text/javascript">';
			        $generated_content .= '$GKTypo = [';
			        for ($i = 0; $i < count($xml->document->group); $i++) {
			            for ($j = 0; $j < count($xml->document->group[$i]->item); $j++) {
			                if ($j == 0) $generated_content .= '[';
			                $generated_content .= '\'' . $xml->document->group[$i]->item[$j]->data() . '\',';
			            }
	
			            $generated_content = substr($generated_content, 0, strlen($generated_content) - 1);
			            $generated_content .= '],';
			        }
	
			        $generated_content = substr($generated_content, 0, strlen($generated_content) - 1);
			        $generated_content .= '];';
			        $generated_content .= '</script>';
			        // tabs for elements
			        $generated_content .= '<h2 class="gkTypoHeader">TYPOGRAPHY</h2>';
			        $generated_content .= '<div class="gkTypoWrap">';
			        $generated_content .= '<ul class="gkTypoMenu">';
	
			        for ($i = 0; $i < count($xml->document->group); $i++) {
			            $first = false;
	
			            for ($j = 0; $j < 1; $j++) {
			                $generated_content .= '<li><a onclick="gkhideTab(' . $i . ')">' . $xml->document->group[$i]->attributes('name') . '</a></li>';
			            }
			        }
	
			        $generated_content .= '</ul>';
			        // arrays for elements
			        $generated_content .= '<div class="gkTypoContent">';
	
			        for ($i = 0; $i < count($xml->document->group); $i++) {
			            $generated_content .= '<table style="margin-top: 0; margin-left:0, position: absolute; z-index: -999; width:100%" class="gkTypoTable"><tbody>';
	
			            $first = false;
	
			            for ($j = 0; $j < count($xml->document->group[$i]->item); $j++) {
							$generated_image = '';
	
			                if ($xml->document->group[$i]->attributes('type') == 'icons') {
			                    $generated_image = '<img src=\'' . JURI::root() . DS . 'templates' . DS . $template_name . DS . 'images' . DS . $xml->document->group[$i]->attributes('directory') . DS . $xml->document->group[$i]->item[$j]->attributes('fname') . "'/>";
			                }
			                
			                $generated_content .= '<tr ' . (!$first ? 'class="first"' : '') . '><td>'.$generated_image.'<span onclick="gkclick($GKTypo[' . $i . '][' . $j . '])">';
	
			                $generated_content .= $xml->document->group[$i]->item[$j]->attributes('name') . '</span></td>';
	
			                $generated_content .= '<td><code>' . htmlspecialchars($xml->document->group[$i]->item[$j]->data()) . '</code></td></tr>';
	
			                if ($j == 0) $first = true;
			            }
	
			            $generated_content .= '</tbody></table>';
			        }
			        
		        	$generated_content .= '</div>';
		        	$generated_content .= '</div>';
		        } else {
		            $generated_content = '<h3> Parse error in selected filed. Please check file structure. </h3>';
		        }
			} else {
				$generated_content = '<h3> No files found in template/typography directory. </h3>';
			}
			
	        $generated_content = '<div style="display:none;"><div class="gk_typography_content">' . $generated_content . '</div></div>';
			
			echo $generated_content;
	
	        return $button;
        }
    }
}