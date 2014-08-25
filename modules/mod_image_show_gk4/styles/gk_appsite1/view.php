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

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_appsite1">
	<div class="gkIsPreloader"></div>
	
	
	<div class="gkIsImage" style="height: <?php echo $this->config['config']->gk_appsite1->gk_appsite1_module_height; ?>px;">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
				<?php 
								
					unset($path, $title, $link);
                    if($this->config['image_show_data'][$i]->type == "k2"){
               	        $title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                         $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                     } else {
				        // creating slide title
					   $title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
	                   // creating slide link
					   $link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
					}
    	           // creating slide path
					$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Appsite1_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
					
				?>
				
				<div class="gkIsSlide" style="margin-left: -<?php echo floor($width / 2); ?>px; margin-top: <?php echo $this->config['config']->gk_appsite1->gk_appsite1_image_y; ?>px; z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>"><?php echo $path; ?><a href="<?php echo $link; ?>">link</a></div>
			<?php endif; ?>
		<?php endfor; ?>
		
		<?php if($this->config['config']->gk_appsite1->gk_appsite1_show_text_block == 1) : ?>
		<div class="gkIsText" style="width: <?php echo $this->config['config']->gk_appsite1->gk_appsite1_text_block_width; ?>px;top: <?php echo $this->config['config']->gk_appsite1->gk_appsite1_text_block_y; ?>px;left: <?php echo $this->config['config']->gk_appsite1->gk_appsite1_text_block_x; ?>px;"></div>
		<?php endif; ?>
		
		
		<div class="gkIsPagination">
			<?php if($this->config['config']->gk_appsite1->gk_appsite1_pagination == 1) : ?>
			<div class="gkIsPrev">&laquo;</div>
			<?php endif; ?>
			<div class="gkIsScale">
				<div class="gkIsProgress"></div>
			</div>
			<?php if($this->config['config']->gk_appsite1->gk_appsite1_pagination == 1) : ?>
			<div class="gkIsNext">&raquo;</div>
			<?php endif; ?>
		</div>
	</div>
	
	<?php if($this->config['config']->gk_appsite1->gk_appsite1_show_text_block == 1) : ?>
	<div class="gkIsTextData">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
			
				// cleaning variables
				unset($link, $content);
				// creating slide title
				$content = str_replace('[ampersand]', '&',str_replace('[leftbracket]', '<', str_replace('[rightbracket]', '>', $this->config['image_show_data'][$i]->content)));
				// creating slide link
				$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
				
			?>
			
			<div class="gkIsTextItem">
				<div class="gkIsTextItemWrap">
					<p><a href="<?php echo $link; ?>"><?php echo $content; ?></a></p>
				</div>
			</div>
			<?php endif; ?>
		<?php endfor; ?>
	</div>
	<?php endif; ?>
	
</div>