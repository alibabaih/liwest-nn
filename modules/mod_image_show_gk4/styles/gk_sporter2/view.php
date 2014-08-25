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

// creating JURI instance
$URI = JURI::getInstance();

$slides_counter = 0;

for($i = 0; $i < count($this->config['image_show_data']); $i++) {
	if($this->config['image_show_data'][$i]->published) {
		$slides_counter++;
	}
}

$amount = ($this->config['config']->gk_sporter2->gk_sporter2_image_rows * $this->config['config']->gk_sporter2->gk_sporter2_image_cols > $slides_counter) ? $slides_counter : $this->config['config']->gk_sporter2->gk_sporter2_image_rows * $this->config['config']->gk_sporter2->gk_sporter2_image_cols;

jimport('joomla.utilities.string');

?>


<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_sporter2">

	<?php if($this->config['config']->gk_sporter2->gk_sporter2_slide_popups == 1) : ?>
	<div class="gkIsPopup-gk_sporter2">
		<div class="wrap">
	        <div class="close"></div>
	        <div class="tl"></div>
	        <div class="t"></div>
	        <div class="tr"></div>
	        <div class="ml"></div>
	        <div class="m">
	              <div class="padding">
                        <div class="overlay"></div>
  						<div class="prev">PREV</div>
				  		<div class="next">NEXT</div>
  						<?php if($this->config['config']->gk_sporter2->gk_sporter2_show_text_block == 1) : ?>
  						<div class="text" style="height:<?php echo $this->config['config']->gk_sporter2->gk_sporter2_text_block_height; ?>px;"></div>
						<?php endif; ?>
  						<div class="content"></div>
				  </div>
	        </div>
	        <div class="mr"></div>
	        <div class="bl"></div>
	        <div class="b"></div>
	        <div class="br"></div>
	  	</div>
	</div>
	<?php endif; ?>
	
	<?php if($this->config['config']->gk_sporter2->gk_sporter2_overlay == 1) : ?>
	<div class="gkIsOverlay-gk_sporter2"></div>
	<?php endif; ?>
	
	<div class="gkIsThumbs">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
		<?php if($this->config['image_show_data'][$i]->published) : ?>
			
			<?php 
			
				// cleaning variables
				unset($path, $title, $thumb_style);
				// creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Sporter2_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id'], 'thumb_');
				$path_big = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Sporter2_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
				if($this->config['image_show_data'][$i]->type == "k2"){
               	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                } else {
                // creating slide title
				$title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
				}
                // creating thumbnail styles
				$thumb_style = '';
				if($this->config['config']->gk_sporter2->gk_sporter2_image_margin != '')
					$thumb_style .= 'margin:'.$this->config['config']->gk_sporter2->gk_sporter2_image_margin.';';
				if($this->config['config']->gk_sporter2->gk_sporter2_image_padding != '')
					$thumb_style .= 'padding:'.$this->config['config']->gk_sporter2->gk_sporter2_image_padding.';';
				if($this->config['config']->gk_sporter2->gk_sporter2_image_border != '')
					$thumb_style .= 'border:'.$this->config['config']->gk_sporter2->gk_sporter2_image_border.';';
					
			?>
			
			<?php if($i > 0 && $i % $this->config['config']->gk_sporter2->gk_sporter2_image_cols == 0) : ?>
			<div class="clear"></div>
			<?php endif; ?>
			
			<a href="<?php echo $path_big; ?>">
				<img src="<?php echo $path; ?>" alt="<?php echo $title; ?>" style="<?php echo $thumb_style; ?>" />
			</a>
			
		<?php endif; ?>	
		<?php endfor; ?>
	</div>		
	
	<div class="gkIsImages gkUnvisible">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
		<?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 	
				// cleaning variables
				unset($path, $title, $link);
				// creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Sporter2_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
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
			
			<div class="gkIsImage" style="z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>">
				<a href="<?php echo $link; ?>" class="gkIsImagePath"><?php echo $path; ?></a>
				<?php if($this->config['config']->gk_sporter2->gk_sporter2_show_text_block == 1) : ?>
				
					<?php 
						// cleaning variables
						unset($text, $exploded_text);
						// creating slide text
                        if($this->config['image_show_data'][$i]->type == "k2"){
                        $text = $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["content"];
                        } else {
						$text = ($this->config['image_show_data'][$i]->type == "text") ? str_replace(array('[leftbracket]', '[rightbracket]'), array('<', '>'), $this->config['image_show_data'][$i]->content) : $this->articles[$this->config['image_show_data'][$i]->art_id]["content"];
						}
                        $text = htmlspecialchars_decode($text);
						if($this->config['config']->gk_sporter2->gk_sporter2_clean_xhtml == 1) $text = strip_tags($text);
						$exploded_text = explode(" ", $text);
						$text = '';
						for($j = 0; $j < $this->config['config']->gk_sporter2->gk_sporter2_text_wordcount; $j++) if(isset($exploded_text[$j])) $text .= $exploded_text[$j]." ";
						if(JString::strlen($title) > $this->config['config']->gk_sporter2->gk_sporter2_title_char_amount){
							$title = JString::substr($title, 0, $this->config['config']->gk_sporter2->gk_sporter2_title_char_amount);	
						}
					?>
					
					
					<div class="gkIsTextBlock" style="height:<?php echo $this->config['config']->gk_sporter2->gk_sporter2_text_block_height; ?>px;">
						<h4>
							<span>
								<a href="<?php echo $link; ?>"><?php echo $title; ?></a>
							</span>
						</h4>
						
						<p>
							<?php echo $text; ?>
							<a href="<?php echo $link; ?>"><?php echo $this->config['config']->gk_sporter2->gk_sporter2_readmore_text; ?></a>
						</p>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
</div>