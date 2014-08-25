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

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_black_and_white<?php if($this->config['config']->gk_black_and_white->gk_black_and_white_slide_links == 1) echo ' slide-links'; ?>">

	<div class="gkIsPreloader">Loading...</div>
	
	<div class="gkIsSlides" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
			
			<?php 
				
				unset($path, $title, $link);
				// creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_BlackAndWhite_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
				
			?>
			
			<span class="gkIsSlide"><?php echo $path; ?></span>
			
			<?php endif; ?>
		<?php endfor; ?>	
	</div>

	<?php if($this->config['config']->gk_black_and_white->gk_black_and_white_show_text_block == 1) : ?>
	<div class="gkIsText" style="bottom:<?php echo $this->config['config']->gk_black_and_white->gk_black_and_white_text_block_position_y; ?>px;right:<?php echo $this->config['config']->gk_black_and_white->gk_black_and_white_text_block_position_x; ?>px;width:<?php echo $this->config['config']->gk_black_and_white->gk_black_and_white_text_block_width; ?>px;">Text</div>
	<?php endif; ?>
	
	<?php if($this->config['config']->gk_black_and_white->gk_black_and_white_pagination == 1) : ?>
	<ul class="gkIsPagination" style="top:<?php echo $this->config['config']->gk_black_and_white->gk_black_and_white_pagination_position_y; ?>px;left:<?php echo $this->config['config']->gk_black_and_white->gk_black_and_white_pagination_position_x; ?>px;">
	    <?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
	    <?php if($this->config['image_show_data'][$i]->published) : ?> 
	    <li><?php echo $i+1; ?></li>
	    <?php endif; ?>
	    <?php endfor; ?>
	</ul>
	<?php endif; ?>

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
                $link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];
				$text = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->content : $this->articles[$this->config['image_show_data'][$i]->art_id]["content"];
                }
                $title = substr($title, 0, $this->config['config']->gk_black_and_white->gk_black_and_white_title_char_amount);	
				$part_one = explode(' ', $title);
	            $part_one = $part_one[0];
			
				if(count(explode(' ', $title)) > 1) $part_two = substr($title, strpos($title,' '));
	            else $part_two = '';
				
	            $title = '<span>' . $part_one . '</span>' . $part_two ;
	            		
				// creating slide text
				
				
				$text = htmlspecialchars_decode($text);
				if($this->config['config']->gk_black_and_white->gk_black_and_white_clean_xhtml == 1) $text = strip_tags($text);
				$exploded_text = explode(" ", stripslashes($text));
				$text = '';
	
				for($j = 0; $j < $this->config['config']->gk_black_and_white->gk_black_and_white_wordcount; $j++) {
					if(isset($exploded_text[$j])) $text .= $exploded_text[$j]." ";
				}
				
				if($this->config['config']->gk_black_and_white->gk_black_and_white_wordcount < count($exploded_text)) $text .= '&hellip;';
			?>
			
			<div class="gkIsTextItem">
				 <?php if($this->config['config']->gk_black_and_white->gk_black_and_white_title_link == 1) : ?>
					<h4><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h4>
				 <?php else : ?>
				 	<h4><a href="<?php echo $link; ?>" class="gkToRemove"><?php echo $title; ?></a></h4>
				 <?php endif; ?>
				 
				<p><?php echo $text; ?>
	                <?php if($this->config['config']->gk_black_and_white->gk_black_and_white_show_readmore == 1) : ?>
	                <a href="<?php echo $link; ?>" class="readon">&raquo;</a>
	                <?php endif; ?>            
	            </p>
			</div>
			
			<?php endif; ?>
		<?php endfor; ?>
	</div>
</div>		