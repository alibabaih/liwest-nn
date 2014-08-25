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

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_the_real_design">
	<div class="gkHeaderBg" style="height: <?php echo $this->config['config']->gk_the_real_design->gk_the_real_design_module_height; ?>px;">
		<div class="gkWrap">	
			<div class="gkIsPreloader">
				<span><?php echo JText::_('TPL_GK_LANG_IS_LOADING'); ?></span>
			</div>
			
			<div class="gkIsImage" style="height: <?php echo $this->config['config']->gk_the_real_design->gk_the_real_design_module_height; ?>px;">
				<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
					<?php if($this->config['image_show_data'][$i]->published) : ?>
						<?php 
										
							unset($path, $title, $link);
			                if($this->config['image_show_data'][$i]->type == "k2"){
			           	        $title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
		                        $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
		                        $link = str_replace('&', '&amp;', $link);
			                 } else {
						        // creating slide title
							   $title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
			                   // creating slide link
							   $link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
							   $link = str_replace('&', '&amp;', $link);
		                    }
				           // creating slide path
							$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_The_real_design_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
							
						?>
						
						<div class="gkIsSlide" style="z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>"><?php echo $path; ?><a href="<?php echo $link; ?>">link</a></div>
					<?php endif; ?>
				<?php endfor; ?>	
			</div>
			
			<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
				<?php if($this->config['image_show_data'][$i]->published) : ?>
				<div class="gkIsTextTitle">
					<?php
						// cleaning variables
						unset($link, $content);
						
						if($this->config['image_show_data'][$i]->type == "text"){
							// creating slide title
							$title = $this->config['image_show_data'][$i]->name;
							$text = str_replace(array('[leftbracket]', '[rightbracket]'), array('<', '>'), $this->config['image_show_data'][$i]->content);
							// creating slide link
							$link = $this->config['image_show_data'][$i]->url;
					        $link = str_replace('&', '&amp;', $link);
						} else {
						 	$title = 'Please use TEXT type of the slide';
						 	$text = 'Please use TEXT type of the slide';
						 	$link = 'Please use TEXT type of the slide';
						}
						
					?>
					<a href="<?php echo $link; ?>" title="<?php echo $title; ?>"><?php echo $text; ?></a>
				</div>
				<?php endif; ?>
			<?php endfor; ?>
		</div>
	</div>
	
	<?php if($this->config['config']->gk_the_real_design->gk_the_real_design_interface == 1) : ?>
	<div class="gkIsInterface">
		<ul>
		<?php 
			$slide_counter = 1;
			for($i = 0; $i < count($this->config['image_show_data']); $i++) : 
		?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
			<li><?php echo $slide_counter; ?></li>
			<?php 
				$slide_counter++;
				endif; 
			?>
		<?php endfor; ?>
		</ul>
	</div>
	<?php endif; ?>
</div>