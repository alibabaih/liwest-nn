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

// count thumbs
$thumbs_count = 0;

for($i = 0; $i < count($this->config['image_show_data']); $i++) {
	if($this->config['image_show_data'][$i]->published) {
		$thumbs_count++;
	}
} 
// generate the module stylesheet
$doc = JFactory::getDocument();
$styles = '#gkIs-'.$this->config['module_id'].' { height: '.$height.'px; width: '.$width.'px; }
#gkIs-'.$this->config['module_id'].' .gkIsImage { height: '.$height.'px; width: '.$width.'px; }
#gkIs-'.$this->config['module_id'].' .gkIsImageAnimArea { height: '.$height.'px; width: '.$width.'px; }
#gkIs-'.$this->config['module_id'].' .gkIsTextTitle { '.(($this->config['config']->gk_eSport->gk_eSport_thumbnails_position == 'gkThumbsBottom') ? 'bottom' : 'top') . ':' . (($this->config['config']->gk_eSport->gk_eSport_thumbnails) ? $this->config['config']->gk_eSport->gk_eSport_thumb_h + 48 : 20).'px; }
#gkIs-'.$this->config['module_id'].' .gkIsThumbnails { height: '.($this->config['config']->gk_eSport->gk_eSport_thumb_h).'px; }
#gkIs-'.$this->config['module_id'].' .gkContentArea > div { height: '.($this->config['config']->gk_eSport->gk_eSport_thumb_h + 28).'px; }
#gkIs-'.$this->config['module_id'].'.gkThumbsTop .gkContentArea { top: -'.(($this->config['config']->gk_eSport->gk_eSport_thumbnails) ? $this->config['config']->gk_eSport->gk_eSport_thumb_h + 28 : 0).'px; }
#gkIs-'.$this->config['module_id'].'.gkThumbsBottom .gkContentArea { bottom: -'.(($this->config['config']->gk_eSport->gk_eSport_thumbnails) ? $this->config['config']->gk_eSport->gk_eSport_thumb_h + 28 : 0).'px; }
#gkIs-'.$this->config['module_id'].' .gkIsPrev,
#gkIs-'.$this->config['module_id'].' .gkIsNext { height: '.$thumb_height.'px; }
#gkIs-'.$this->config['module_id'].' .gkIsThumbnailsWrap { width: '.($width - 160).'px; height: '.$thumb_height.'px; }
#gkIs-'.$this->config['module_id'].' .gkIsThumbnailsWrap ul { width: '.($thumbs_count * ($thumb_width + 13)).'px; }
#gkIs-'.$this->config['module_id'].'.gkThumbsBottom .gkContentArea { bottom: -'.($this->config['config']->gk_eSport->gk_eSport_thumb_h + 28).'px; }';
$doc->addStyleDeclaration( $styles );

if($this->config['config']->gk_eSport->gk_eSport_thumbnails) {
	$styles = ' #gkIs-'.$this->config['module_id'].'.gkThumbsTop:hover .gkContentArea { top: 20px!important; } #gkIs-'.$this->config['module_id'].'.gkThumbsBottom:hover .gkContentArea { bottom: 20px!important; }';
	$doc->addStyleDeclaration( $styles );
}

?>

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_eSport<?php echo ' ' . $this->config['config']->gk_eSport->gk_eSport_thumbnails_position; ?><?php echo ' ' . $this->config['config']->gk_eSport->gk_eSport_anim_type; ?>">	
	<div class="gkIsPreloader">
		<span><?php echo JText::_('TPL_GK_LANG_IS_LOADING'); ?></span>
	</div>
	
	<div class="gkIsImage">
		<div class="gkIsImageAnimArea"></div>
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
				    $path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_eSport_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
					
				?>
				
				<div class="gkIsSlide" style="z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>"><?php echo $path; ?><a href="<?php echo $link; ?>">link</a></div>
			<?php endif; ?>
		<?php endfor; ?>	
	</div>
	
	<?php if(
			$this->config['config']->gk_eSport->gk_eSport_text_block == 1 || 
			$this->config['config']->gk_eSport->gk_eSport_thumbnails == 1
			) : ?>
	<div class="gkContentArea<?php if($this->config['config']->gk_eSport->gk_eSport_text_block == 1) : ?> gkText<?php endif; ?>">
		<div>
			<?php if($this->config['config']->gk_eSport->gk_eSport_text_block == 1) : ?>
				<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
					<?php if($this->config['image_show_data'][$i]->published) : ?>
					<div class="gkIsTextTitle">
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
							
						?>
						<a href="<?php echo $link; ?>" title="<?php echo $title; ?>"><?php echo $title; ?></a>
					</div>
					<?php endif; ?>
				<?php endfor; ?>
			<?php endif; ?>
			
			<?php if($this->config['config']->gk_eSport->gk_eSport_thumbnails == 1) : ?>
			<div class="gkIsThumbnails">
				<a class="gkIsPrev"><span>&laquo;</span></a>
				<div class="gkIsThumbnailsWrap">
					<ul>
					<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
						<?php 
							if($this->config['image_show_data'][$i]->published) : 
							
							$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_eSport_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id'], 'thumb_');
							
							if($this->config['image_show_data'][$i]->type == "k2"){
							    $title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
							} else {
							    // creating slide title
							   $title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
							}	
						?>
						<li><img src="<?php echo $path; ?>" class="gkIsThumb" title="<?php echo $title; ?>" alt="<?php echo $title; ?>" /></li>
						<?php endif; ?>
					<?php endfor; ?>
					</ul>
				</div>
				<a class="gkIsNext"><span>&raquo;</span></a>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
</div>