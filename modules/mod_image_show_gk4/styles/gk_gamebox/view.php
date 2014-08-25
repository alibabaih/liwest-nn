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

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_gamebox">
	<div class="gkIsPreloader"></div>
	
	<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
        
        <?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
				
				unset($path, $title, $link);
				// creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Gamebox_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
				if($this->config['image_show_data'][$i]->type == "k2"){
               	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                } else {
                // creating slide title
                $title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
				// creating slide link
				$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
				}
			?>
			
			<div class="gkIsSlide" style="z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>"><?php echo $path; ?><a href="<?php echo $link; ?>">link</a></div>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
	
	<?php if($this->config['config']->gk_gamebox->gk_gamebox_interface) : ?>
	<div class="gkIsPrev"><span>&laquo;</span></div>
	<div class="gkIsNext"><span>&raquo;</span></div>
	<?php endif; ?>
	
	<?php if($this->config['config']->gk_gamebox->gk_gamebox_show_text_block) : ?>
	<div class="gkIsTextBg" style="height:<?php echo $this->config['config']->gk_gamebox->gk_gamebox_text_height; ?>px;top:<?php echo $this->config['config']->gk_gamebox->gk_gamebox_text_position; ?>px;"></div>
	<div class="gkIsText" style="height:<?php echo $this->config['config']->gk_gamebox->gk_gamebox_text_height; ?>px;top:<?php echo $this->config['config']->gk_gamebox->gk_gamebox_text_position; ?>px;"></div>
	
	<div class="gkIsTextData">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
		<?php if($this->config['image_show_data'][$i]->published) : ?>
		
		<?php 
			
			unset($title, $link);
            if($this->config['image_show_data'][$i]->type == "k2"){
       	    $title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
            $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
            } else {
			$title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
			$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];			
			}
		?>
		
		<div class="gkIsTextItem">
			<?php if($this->config['config']->gk_gamebox->gk_gamebox_show_info == 1) : ?>
			<?php if($this->config['image_show_data'][$i]->type != "text") : ?>
			<span class="gkIsInfo">
				<?php
                    if($this->config['image_show_data'][$i]->type == "k2"){
                   	$gk_is_date = JHTML::_('date', $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["date"], $this->config['config']->gk_sporter1->gk_sporter1_date_format);
				    $gk_is_author = ($this->config['config']->gk_sporter1->gk_sporter1_author_name == 'username') ? $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["username"] : $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["name"];
                    } else {
					$gk_is_date = JHTML::_('date', $this->articles[$this->config['image_show_data'][$i]->art_id]["date"], $this->config['config']->gk_gamebox->gk_gamebox_date_format);
					$gk_is_author =  $this->articles[$this->config['image_show_data'][$i]->art_id][$this->config['config']->gk_gamebox->gk_gamebox_author_name];
					}
                    echo str_replace('%author', $gk_is_author, str_replace('%date', $gk_is_date, $this->config['config']->gk_gamebox->gk_gamebox_info_format));
				?>
			</span>
			<?php endif; ?>
			<?php endif; ?>
			<h4><span><?php echo $title; ?></span></h4>
			<a href="<?php echo $link; ?>" class="readon" style="top:<?php echo (($this->config['config']->gk_gamebox->gk_gamebox_text_height - 24)/ 2); ?>px;"><?php echo $this->config['config']->gk_gamebox->gk_gamebox_readmore_text; ?></a>
		</div>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
	<?php endif; ?>
</div>