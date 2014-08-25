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

// access restriction
defined('_JEXEC') or die('Restricted access');
// vars
$highest_layer = 0;
// initializing variables
$URI = JURI::getInstance();

?>

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_cherrydesign" style="height: <?php echo $height; ?>px;">
	<?php if($this->config['config']->gk_cherrydesign->gk_cherrydesign_pagination) : ?>
	<ul class="gkIsPagination<?php echo ($this->config['config']->gk_cherrydesign->gk_cherrydesign_pagination_position == 'left') ? ' left' : ' right'; ?>">
		<?php 
			$counter = 0;
			for($x = 0; $x < count($this->config['image_show_data']); $x++) : 
		?>
			<?php if($this->config['image_show_data'][$x]->published) : ?>
			<li>
			<?php 
				echo $counter+1; 
				$counter++; 
			?>
			</li>
			<?php endif; ?>
		<?php endfor; ?>
	</ul>
	<?php endif; ?>
	
	<div class="gkIsImage<?php echo ($this->config['config']->gk_cherrydesign->gk_cherrydesign_pagination_position == 'left') ? ' right' : ' left'; ?>" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
        <?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
				<?php 
					
					unset($path, $title, $link);
					// creating slide path
					$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Coffe_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
					
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
				
				<div class="gkIsSlide" style="z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>"><a href="<?php echo $path; ?>">src</a><a href="<?php echo $link; ?>">link</a></div>
			<?php endif; ?>	
		<?php endfor; ?>
		
		<div class="gkIsPreloader"></div>
        <div class="gkIsOverlay"></div>
	
		<?php if($this->config['config']->gk_cherrydesign->gk_cherrydesign_show_text_block) : ?>		
		<div class="gkIsTextData">
			<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
				<?php if($this->config['image_show_data'][$i]->published) : ?>
					<?php 
						// cleaning variables
						unset($title, $link, $text, $exploded_text);
						if($this->config['image_show_data'][$i]->type == "k2"){
                       	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                        $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                        $text = $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["content"];
                        } else {
                        // creating slide title
						$title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
						// creating slide link
						$link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];
						// creating slide text
						$text = ($this->config['image_show_data'][$i]->type != "text") ? $this->articles[$this->config['image_show_data'][$i]->art_id]["text"] : $this->config['image_show_data'][$i]->content;
						}
                        $text = htmlspecialchars_decode($text);
						if($this->config['config']->gk_cherrydesign->gk_cherrydesign_clean_xhtml) $text = strip_tags($text);
						$exploded_text = explode(" ", stripslashes($text));
						$text = '';
		
						for($j = 0; $j < $this->config['config']->gk_cherrydesign->gk_cherrydesign_wordcount; $j++) {
							if(isset($exploded_text[$j])) {
								$text .= $exploded_text[$j]." ";
							}
						}
						
						if($this->config['config']->gk_cherrydesign->gk_cherrydesign_wordcount < count($exploded_text)) {
						    $text .= '&hellip;';
						}
		
					?>
					<div class="gkIsTextItem">
						<h4><span><?php echo $title; ?></span></h4>
						<p><?php echo $text; ?></p>
		                <a href="<?php echo $link; ?>" class="readmore"><?php echo $this->config['config']->gk_cherrydesign->gk_cherrydesign_readmore_text; ?></a>
					</div>
				<?php endif; ?>
		<?php endfor; ?>
		</div>
		<?php endif; ?>
	</div>
</div>