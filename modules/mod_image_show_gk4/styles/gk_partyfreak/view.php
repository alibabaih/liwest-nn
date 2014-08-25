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
$thumbs_parsed_margins = array( "top" => 0, "right" => 0, "bottom" => 0, "left" => 0 );
$URI = JURI::getInstance();
// parsing margins and paddings
$exploded_thumbs_margins = explode(" ", trim($this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_margin));
// parsing
// thumbs margins
if(count($exploded_thumbs_margins) == 1){
	$thumbs_parsed_margins["top"] = str_replace("px", "", $exploded_thumbs_margins[0]);
	$thumbs_parsed_margins["right"] = str_replace("px", "", $exploded_thumbs_margins[0]);
	$thumbs_parsed_margins["bottom"] = str_replace("px", "", $exploded_thumbs_margins[0]);
	$thumbs_parsed_margins["left"] = str_replace("px", "", $exploded_thumbs_margins[0]);
}elseif(count($exploded_thumbs_margins) == 2){
	$thumbs_parsed_margins["top"] = str_replace("px", "", $exploded_thumbs_margins[0]);
	$thumbs_parsed_margins["right"] = str_replace("px", "", $exploded_thumbs_margins[1]);
	$thumbs_parsed_margins["bottom"] = str_replace("px", "", $exploded_thumbs_margins[0]);
	$thumbs_parsed_margins["left"] = str_replace("px", "", $exploded_thumbs_margins[1]);	
}elseif(count($exploded_thumbs_margins) == 4){
	$thumbs_parsed_margins["top"] = str_replace("px", "", $exploded_thumbs_margins[0]);
	$thumbs_parsed_margins["right"] = str_replace("px", "", $exploded_thumbs_margins[1]);
	$thumbs_parsed_margins["bottom"] = str_replace("px", "", $exploded_thumbs_margins[2]);
	$thumbs_parsed_margins["left"] = str_replace("px", "", $exploded_thumbs_margins[3]);	
}
// calculating thumbs block width
$total_block_height = 0;
$total_block_height += $height;
$total_block_width = $this->config['config']->gk_partyfreak->gk_partyfreak_total_width;
$total_thumbs_width = $total_block_width - ($width + 24 + 16);
//
?>

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_partyfreak" style="width: <?php echo $this->config['config']->gk_partyfreak->gk_partyfreak_total_width; ?>px;">
	<div class="gkIsPreloader"></div>

	<div class="gkIsHeaderTop gkIsImageWrap">
		<div class="gkIsHeaderBottom">
			<div class="gkIsHeaderLeft">
				<div class="gkIsHeaderRight">
					<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
					
							
						<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
						<?php if($this->config['image_show_data'][$i]->published) : ?>
							<?php 
								// cleaning variables
								unset($path_big, $title, $link);
								// creating slide path
								$path_big = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_PartyFreak_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
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

						<?php if($this->config['config']->gk_partyfreak->gk_partyfreak_show_title == 1) : ?>
						<div class="gkIsTextBg" style="height:<?php echo $this->config['config']->gk_partyfreak->gk_partyfreak_text_block_height; ?>px;"></div>
						<div class="gkIsText" style="height:<?php echo $this->config['config']->gk_partyfreak->gk_partyfreak_text_block_height; ?>px;"></div>

						<div class="gkIsTextData">
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
                                
                                $text = htmlspecialchars_decode($text);
								
								if($this->config['config']->gk_sporter2->gk_sporter2_clean_xhtml == 1) {
									$text = strip_tags($text);
								}
								
								$exploded_text = explode(" ", $text);
								$text = '';
								//
								for($j = 0; $j < $this->config['config']->gk_partyfreak->gk_partyfreak_text_word_amount; $j++) {
									if(isset($exploded_text[$j])) {
										$text .= $exploded_text[$j]." ";
									}
								}
							?>
							<div class="gkIsTextItem">
								<h4><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h4>
								<p><?php echo $text; ?></p>
							</div>
						<?php endif; ?>
						<?php endfor; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>	
			</div>
		</div>
	</div>		

	<div class="gkIsThumbs" style="height:<?php echo $total_block_height; ?>px;width:<?php echo $total_thumbs_width; ?>px;">	
		<div class="gkIsBtnUp"></div>
		<div class="gkIsThumbsSlider1 gkClear" style="height:<?php echo ($total_block_height + 24) - (40 + (2 * $this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_space)); ?>px;margin:<?php echo $this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_space; ?>px 0;">
			<div class="gkIsThumbsSlider2">
			
			<?php
				$thumb_height = ((($total_block_height + 24) - (40 + (2 * $this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_space))) - (($this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_amount - 1) * $this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_space)) / $this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_amount;
			?>

			<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>

				<?php 
					// cleaning variables
					unset($path, $title, $thumb_style, $text);
					// creating slide path
					$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_PartyFreak_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id'], 'thumb_');
					//
					if($this->config['image_show_data'][$i]->type == "k2"){
                    	$link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                    	$text = $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["content"];
                    } else {
						$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
						$text = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->content : $this->articles[$this->config['image_show_data'][$i]->art_id]["content"];
					}
					
                    $title = htmlentities(htmlspecialchars($this->config['image_show_data'][$i]->name), ENT_QUOTES, 'UTF-8', false);
                    $text = htmlspecialchars_decode($text);
                    
					if($this->config['config']->gk_sporter2->gk_sporter2_clean_xhtml == 1) {
						$text = strip_tags($text);
					}
					
					$exploded_text = explode(" ", $text);
					$text = '';
					//
					for($j = 0; $j < $this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_text_word_amount; $j++) {
						if(isset($exploded_text[$j])) {
							$text .= $exploded_text[$j]." ";
						}
					}
					// creating thumbnail styles
					$thumb_style = '';

					if($this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_margin != 0) {
						$thumb_style .= 'margin:'.$this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_margin.';';
					}
					
					if($this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_padding != 0) {
					 	$thumb_style .= 'padding:'.$this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_padding.';';
					}
				?>
				<div class="gkIsThumb" style="height:<?php echo $thumb_height; ?>px;<?php if($i != 0) : ?>margin-top:<?php echo $this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_space; ?>px;<?php endif; ?>">
					<img src="<?php echo $path; ?>" class="gkIsThumb" alt="<?php echo $title; ?>" style="<?php echo $thumb_style; ?>border-width:<?php echo $this->config['config']->gk_partyfreak->gk_partyfreak_thumbs_border;?>px;" title="<?php echo $title; ?>" />
					<h4><?php echo $title; ?></h4>
					<p><?php echo $text; ?></p>	
				</div>
			<?php endif; ?>
			<?php endfor; ?>
			</div>
		</div>
		<div class="gkIsBtnDown gkClear"></div>
	</div>
</div>