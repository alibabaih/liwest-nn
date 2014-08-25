<?php

/**
* GK Image Show - view file
* @package Joomla!
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @ version $Revision: GK4 1.0 $
**/

// no direct access
defined('_JEXEC') or die;

?>
	
<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_yourshop" style="width: <?php echo $width; ?>px;">
	<div class="gkIsPreloader"></div>
	
	<div class="gkIsPrev"><span>Prev</span></div>
	<div class="gkIsNext"><span>Next</span></div>
	
	<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
				unset($path, $title, $link);
				// creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Yourshop_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
				
                if($this->config['image_show_data'][$i]->type == "k2"){
               	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                } else {// creating slide title
				$title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
				// creating slide link
				$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
				}
			?>
			
			<div class="gkIsSlide" style="z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>"><a href="<?php echo $path; ?>">src</a><a href="<?php echo $link; ?>">link</a></div>
			<?php endif; ?>
		<?php endfor; ?>
		

		<?php if($this->config['config']->gk_yourshop->gk_yourshop_show_text_block == 1) : ?>
		<div class="gkIsTextBg" style="height:<?php echo $this->config['config']->gk_yourshop->gk_yourshop_text_height; ?>px;top:<?php echo $this->config['config']->gk_yourshop->gk_yourshop_text_position;?>px;"></div>
		<div class="gkIsText" style="height:<?php echo $this->config['config']->gk_yourshop->gk_yourshop_text_height; ?>px;top:<?php echo $this->config['config']->gk_yourshop->gk_yourshop_text_position;?>px;"></div>
		
		
		<div class="gkIsTextData">
			<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
			
			<?php 
				// cleaning variables
				unset($title, $link, $text, $exploded_text);
				// creating slide title
				if($this->config['image_show_data'][$i]->type == "k2"){
               	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                $text = $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["content"];
                } else {
				$title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
				// creating slide link
				$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];				
				// creating slide text
				$text = ($this->config['image_show_data'][$i]->type == "text") ? str_replace(array('[leftbracket]', '[rightbracket]'), array('<', '>'), $this->config['image_show_data'][$i]->content) : $this->articles[$this->config['image_show_data'][$i]->art_id]["content"];
				}
                $text = htmlspecialchars_decode($text);
				$title = substr($title, 0, $this->config['config']->gk_yourshop->gk_yourshop_title_char_amount);
                if($this->config['config']->gk_yourshop->gk_yourshop_clean_xhtml == 1) $text = strip_tags($text);
				$exploded_text = explode(" ", stripslashes($text));
				$text = '';

				for($j = 0; $j < $this->config['config']->gk_yourshop->gk_yourshop_wordcount; $j++) {
					if(isset($exploded_text[$j])) {
						$text .= $exploded_text[$j]." ";
					}
				}
				
				if($this->config['config']->gk_yourshop->gk_yourshop_wordcount < count($exploded_text)) {
				    $text .= '&hellip;';
				}
			?>
			
			<div class="gkIsTextItem">
				<h4><?php echo $title; ?></h4>
				<p>
					<?php echo $text; ?>
					<a href="<?php echo $link; ?>" class="readon"><?php echo $this->config['config']->gk_yourshop->gk_yourshop_readmore_text; ?></a>
				</p>
			</div>
		<?php endif; ?>	
		<?php endfor; ?>
		</div>
		<?php endif; ?>
	</div>
</div>