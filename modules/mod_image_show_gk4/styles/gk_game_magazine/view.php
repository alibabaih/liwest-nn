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

jimport('joomla.utilities.string');

// vars
$highest_layer = 0;
// initializing variables
$URI = JURI::getInstance();
// parsing
// calculating thumbs block width
$total_block_height = 0;
$total_block_height += $height;
$total_block_width = $this->config['config']->gk_game_magazine->gk_game_magazine_total_width;
$total_thumbs_width = $total_block_width - ($width + 20);
//
?>

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_game_magazine" style="width: <?php echo $this->config['config']->gk_game_magazine->gk_game_magazine_total_width; ?>px;">
	<div class="gkIsPreloader"></div>

	<div class="gkIsList gkFloat<?php echo $this->config['config']->gk_game_magazine->gk_game_magazine_text_block_position; ?>" style="width:<?php echo $total_thumbs_width; ?>px;">	
		<div class="gkIsBtnUp"></div>
		<div class="gkIsListSlider" style="height:<?php echo $height; ?>px;">
			<div class="gkIsListContent">
			
			<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
				<?php if($this->config['image_show_data'][$i]->published) : ?>

				<?php 
					// cleaning variables
					unset($path, $link, $title);
					// creating slide path
					$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_GameMagazine_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id'], 'thumb_');
					//
					if($this->config['image_show_data'][$i]->type == "k2") {
                    	$link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                    } else {
						$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
					}
					
                    $title = htmlentities(htmlspecialchars($this->config['image_show_data'][$i]->name), ENT_QUOTES, 'UTF-8', false);
                    
				?>
				<a href="<?php echo $link; ?>" class="gkIsListItem"><?php echo $title; ?></a>
				<?php endif; ?>
			<?php endfor; ?>
			</div>
		</div>
		<div class="gkIsBtnDown"></div>
	</div>

	<div class="gkIsImageWrap">
		<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
		
			<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
				<?php if($this->config['image_show_data'][$i]->published) : ?>
				<?php 
					// cleaning variables
					unset($path_big, $title, $link);
					// creating slide path
					$path_big = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_GameMagazine_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
					// creating the title
					if($this->config['image_show_data'][$i]->type == "k2") {
                   		$title = htmlentities(htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]), ENT_QUOTES, 'UTF-8', false);
                    	$link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                    } else {
                    	// creating slide title
						$title = htmlentities(htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]), ENT_QUOTES, 'UTF-8', false);
						$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
					}		
				?>
				
				<div class="gkIsSlide" style="z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>"><a href="<?php echo $path_big; ?>">src</a><a href="<?php echo $link; ?>">link</a></div>
				<?php endif; ?>
			<?php endfor; ?>

			<?php if($this->config['config']->gk_game_magazine->gk_game_magazine_show_text_block == 1) : ?>
				<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
				<?php if($this->config['image_show_data'][$i]->published) : ?>
					<?php 
						// cleaning variables
						unset($text, $title, $link, $exploded_text);
						//
						$title = htmlentities(htmlspecialchars($this->config['image_show_data'][$i]->name), ENT_QUOTES, 'UTF-8', false);
	                    
	                    if($this->config['image_show_data'][$i]->type == "k2") {
	                    	$link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
	                    	$text = $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["content"];
	                    } else {
							$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
							$text = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->content : $this->articles[$this->config['image_show_data'][$i]->art_id]["content"];
						}
	                    
	                    $text = strip_tags(htmlspecialchars_decode($text));
						
					?>
					<a class="gkIsTextItem" style="left:<?php echo $this->config['config']->gk_game_magazine->gk_game_magazine_text_block_x; ?>px;bottom:<?php echo $this->config['config']->gk_game_magazine->gk_game_magazine_text_block_y; ?>px;" href="<?php echo $link; ?>">
						<span class="gkIsTextBig"><?php echo substr($title, 0, $this->config['config']->gk_game_magazine->gk_game_magazine_title_chars_amount); ?></span>
						<span class="gkIsTextSmall"><?php echo substr($text, 0, $this->config['config']->gk_game_magazine->gk_game_magazine_text_chars_amount); ?></span>
					</a>
				<?php endif; ?>
			<?php endfor; ?>
			<?php endif; ?>
		</div>
	</div>
</div>