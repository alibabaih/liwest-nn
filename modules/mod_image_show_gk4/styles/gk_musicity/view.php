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

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_musicity">
	<div class="gkIsPreloader"></div>
		
	<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
        
        <?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
				
				unset($path, $title, $link);
                // creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Musicity_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
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
	
	<?php if($this->config['config']->gk_musicity->gk_musicity_show_date_block == 1) : ?>
	<div class="gkIsDate" style="top: <?php echo $this->config['config']->gk_musicity->gk_musicity_date_block_y; ?>px;"></div>
	<?php endif; ?>
	
	<?php if($this->config['config']->gk_musicity->gk_musicity_show_title_block == 1 || $this->config['config']->gk_musicity->gk_musicity_interface == 1) : ?>
	<div class="gkIsText" style="bottom: <?php echo $this->config['config']->gk_musicity->gk_musicity_title_block_y; ?>px;">
		<?php if($this->config['config']->gk_musicity->gk_musicity_show_title_block == 1) : ?>
		<div class="gkIsTextTitle"></div>
		<?php endif; ?>
		<?php if($this->config['config']->gk_musicity->gk_musicity_interface == 1) : ?>
		<div class="gkIsTextInterface">
			<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
			<span><?php echo $i; ?></span>
			<?php endif; ?>
			<?php endfor; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	
	<?php if($this->config['config']->gk_musicity->gk_musicity_show_title_block == 1 || $this->config['config']->gk_musicity->gk_musicity_show_date_block == 1) : ?>
	<div class="gkIsTextData">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
		<?php if($this->config['image_show_data'][$i]->published) : ?>
		
		<?php 
			// cleaning variables
			unset($title, $link, $date);
			if($this->config['image_show_data'][$i]->type == "k2"){
		       	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
		        $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
		        $date = JHTML::_('date', $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["date"], $this->config['config']->gk_musicity->gk_musicity_date_format);
		    } else {
				if($this->config['image_show_data'][$i]->type == "text") {
					$date = '';
					$title = htmlspecialchars($this->config['image_show_data'][$i]->name);
					$link = $this->config['image_show_data'][$i]->url;	
				} else {
					$date = JHTML::_('date', $this->articles[$this->config['image_show_data'][$i]->art_id]["date"], $this->config['config']->gk_musicity->gk_musicity_date_format);
					$title = htmlspecialchars($this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
					$link = $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
				}
			}
		?>
		
		<div class="gkIsTextItem">
			<a href="<?php echo $link; ?>"><?php echo $title; ?></a>
		</div>
		
		<div class="gkIsDateItem">
			<span><?php echo $date; ?></span>
		</div>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
	<?php endif; ?>
</div>