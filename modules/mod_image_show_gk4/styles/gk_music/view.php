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

$amount_of_published = 0;

for($i = 0; $i < count($this->config['image_show_data']); $i++) {
	$amount_of_published++;
}

?>

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_music">
	<div class="gkIsPreloader"></div>
	
	<?php if($this->config['config']->gk_music->gk_music_title_block == '1') : ?>
	<div class="gkIsTitleBlock">
		<h3>
			<?php 
				$part_one = explode(' ', $this->config['config']->gk_music->gk_music_title);
				$part_one = $part_one[0];
				if(count(explode(' ', $this->config['config']->gk_music->gk_music_title)) > 1) $part_two = substr($this->config['config']->gk_music->gk_music_title, strpos($this->config['config']->gk_music->gk_music_title,' '));
				else $part_two = '';
				echo '<span>' . $part_one . '</span>' . $part_two; 
			?>
		</h3>
		
		<?php if(
			count($this->config['image_show_data']) > $this->config['config']->gk_music->gk_music_slides_per_page ||
			$this->config['config']->gk_music->gk_music_show_pagination
		) : ?>
		<ol class="gkIsPagination">
			<?php for($i = 0; $i < ceil(count($this->config['image_show_data']) / $this->config['config']->gk_music->gk_music_slides_per_page); $i++) : ?>
			<li><?php echo $i; ?></li>
			<?php endfor; ?>
		</ol>
		<?php endif; ?>
	</div>
	<?php endif; ?>
		
	<div class="gkIsImages">
		<?php 
			$inner_counter = 0;
			for($i = 0; $i < count($this->config['image_show_data']); $i++) : 
		?>
        
        <?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php if($inner_counter == 0) : ?><div class="gkIsSlides" style="z-index: <?php echo $i; ?>"><?php endif; ?>
			
			<?php 
				
				unset($path, $title, $link, $date);
                // creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Music_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
                //
                if($this->config['image_show_data'][$i]->type == "k2"){
                   	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                    $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                    $date = JHTML::_('date', $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["date"], $this->config['config']->gk_music->gk_music_date_format);
                } else {
                	if($this->config['image_show_data'][$i]->type == "text") {
                		$date = '';
                		$title = htmlspecialchars($this->config['image_show_data'][$i]->name);
                		$link = $this->config['image_show_data'][$i]->url;	
                	} else {
                		$date = JHTML::_('date', $this->articles[$this->config['image_show_data'][$i]->art_id]["date"], $this->config['config']->gk_music->gk_music_date_format);
                		$title = htmlspecialchars($this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
                		$link = $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
                	}
                }
                
                if(strlen($title) > $this->config['config']->gk_music->gk_music_title_chars) {
                	$title = substr($title, 0, $this->config['config']->gk_music->gk_music_title_chars) . '&hellip;';
                	$title = str_replace(array('&&', '&a&' , '&am&', '&amp&', '&q&', '&qu&', '&quo&', '&quot'), '&', $title);
                }
			?>
			
			<div class="gkIsSlide<?php if($i >= $this->config['config']->gk_music->gk_music_slides_per_page) echo ' gkIsHidden'; ?>" title="<?php echo $title; ?>">
				<div>
					<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
						<a href="<?php echo $link; ?>">
							<?php echo $path; ?>
						</a>
					</div>
				
					<?php if($this->config['config']->gk_music->gk_music_show_title_block == '1') : ?>
					<h3 style="width:<?php echo $width; ?>px;"><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h3>
					<?php endif; ?>
					
					<?php if($this->config['config']->gk_music->gk_music_show_date_block == '1') : ?>
					<span class="gkIsDateItem" style="width:<?php echo $width; ?>px;"><?php echo $date; ?></span>
					<?php endif; ?>
				</div>
			</div>
			
			<?php 
				$inner_counter++;
				if(
					$inner_counter == $this->config['config']->gk_music->gk_music_slides_per_page ||
				    $i == count($this->config['image_show_data']) - 1
				) :
					$inner_counter = 0;	
			?></div><?php endif; ?>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
	
	<?php if(
		$this->config['config']->gk_music->gk_music_loader && 
		$amount_of_published > $this->config['config']->gk_music->gk_music_slides_per_page
	) : ?>
	<div class="gkIsLoader">
		<?php if(
			$amount_of_published > $this->config['config']->gk_music->gk_music_slides_per_page &&
			$this->config['config']->gk_music->gk_music_autoanimation
		) : ?>
		<div class="gkIsProgress">Loading...</div>
		<?php endif; ?>
		<div class="gkIsCursor">Slide me</div>
	</div>
	<?php endif; ?>
</div>