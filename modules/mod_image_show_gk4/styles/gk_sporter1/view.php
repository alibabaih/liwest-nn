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
	
<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_sporter1" style="width: <?php echo $width; ?>px;">
	<div class="gkIsPreloader"></div>
	
	<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">		
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
		<?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
				
				unset($path, $title, $link);
				// creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Sporter1_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
				
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
		

		<?php if($this->config['config']->gk_sporter1->gk_sporter1_show_text_block == 1) : ?>
		<div class="gkIsTextBg" style="height:<?php echo $this->config['config']->gk_sporter1->gk_sporter1_text_height; ?>px;top:<?php echo $this->config['config']->gk_sporter1->gk_sporter1_text_position;?>px;"></div>
		<div class="gkIsText" style="height:<?php echo $this->config['config']->gk_sporter1->gk_sporter1_text_height; ?>px;top:<?php echo $this->config['config']->gk_sporter1->gk_sporter1_text_position;?>px;"></div>
		
		
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
				$title = substr($title, 0, $this->config['config']->gk_sporter1->gk_sporter1_title_char_amount);
                if($this->config['config']->gk_sporter1->gk_sporter1_clean_xhtml == 1) $text = strip_tags($text);
				$exploded_text = explode(" ", stripslashes($text));
				$text = '';

				for($j = 0; $j < $this->config['config']->gk_sporter1->gk_sporter1_wordcount; $j++) {
					if(isset($exploded_text[$j])) {
						$text .= $exploded_text[$j]." ";
					}
				}
				
				if($this->config['config']->gk_sporter1->gk_sporter1_wordcount < count($exploded_text)) {
				    $text .= '&hellip;';
				}
			?>
			
			<div class="gkIsTextItem">
				<?php if($this->config['config']->gk_sporter1->gk_sporter1_show_info == 1) : ?>
				<span class="gkIsInfo">
					<?php
                        if($this->config['image_show_data'][$i]->type == "k2"){
                       	$gk_is_date = JHTML::_('date', $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["date"], $this->config['config']->gk_sporter1->gk_sporter1_date_format);
						$gk_is_author = ($this->config['config']->gk_sporter1->gk_sporter1_author_name == 'username') ? $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["username"] : $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["name"];
                    } else {
						$gk_is_date = JHTML::_('date', $this->articles[$this->config['image_show_data'][$i]->art_id]["date"], $this->config['config']->gk_sporter1->gk_sporter1_date_format);
						$gk_is_author = ($this->config['config']->gk_sporter1->gk_sporter1_author_name == 'username') ? $this->articles[$this->config['image_show_data'][$i]->art_id]["username"] : $this->articles[$this->config['image_show_data'][$i]->art_id]["name"];
						
					}
                    echo str_replace('%author', $gk_is_author, str_replace('%date', $gk_is_date, $this->config['config']->gk_sporter1->gk_sporter1_info_format));
                    ?>
				</span>
				<?php endif; ?>
				<h4><span><?php echo $title; ?></span></h4>
				<p><?php echo $text; ?></p>
				<a href="<?php echo $link; ?>" class="readon" style="top:<?php echo (($this->config['config']->gk_sporter1->gk_sporter1_text_height - 24)/ 2); ?>px;"><?php echo $this->config['config']->gk_sporter1->gk_sporter1_readmore_text; ?></a>
			</div>
		<?php endif; ?>	
		<?php endfor; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php 
     
        if($this->config['config']->gk_sporter1->gk_sporter1_pagination == 1) : 
        
            $cols = $this->config['config']->gk_sporter1->gk_sporter1_pagination_cols;
            $show_date = $this->config['config']->gk_sporter1->gk_sporter1_pagination_date;
            $char_limit = $this->config['config']->gk_sporter1->gk_sporter1_pagination_limit;
                
    ?>
	<div class="gkIsPagination gkIsCols<?php echo $cols; ?>">
    <?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
    <?php if($this->config['image_show_data'][$i]->published) : ?>
        <div class="gkIsTab">
            <div>
            <?php 
                
                if($show_date == 1) : 
                    if($this->config['image_show_data'][$i]->type == "k2"){
                    $gk_is_date = JHTML::_('date', $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["date"], $this->config['config']->gk_sporter1->gk_sporter1_date_format);    
                    } else {
                    $gk_is_date = JHTML::_('date', $this->articles[$this->config['image_show_data'][$i]->art_id]["date"], $this->config['config']->gk_sporter1->gk_sporter1_date_format);
                    }
            ?>
                <span><?php echo $gk_is_date; ?></span>  
            <?php endif; ?>
            <?php   
                if($this->config['image_show_data'][$i]->type == "k2"){
                $title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                } else {
                $title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
                }
                $title = substr($title, 0, $char_limit);
            ?>
                <h3><?php echo $title; ?></h3>
            </div>
        </div>
        <?php if($i > 0 && ($i+1) % $cols == 0) : ?>
        <div class="clear"></div>
        <?php endif; ?>
    <?php endif; ?>
    <?php endfor; ?>
    </div>
	<?php endif; ?>
</div>