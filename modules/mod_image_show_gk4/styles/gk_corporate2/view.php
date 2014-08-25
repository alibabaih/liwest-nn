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

jimport( 'joomla.utilities.string' );

?>

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_corporate2" style="min-height:<?php echo $height; ?>px;">
	<div class="gkIsPreloader"><span>LOADING</span></div>
	<?php if($interface == 1) : ?>
	<div class="gkIsPrev<?php if($interface_animation == 1) echo ' anim'; ?>" style="top:<?php echo (($height/2) - 23); ?>px;"><div>PREV</div></div>
	<div class="gkIsNext<?php if($interface_animation == 1) echo ' anim'; ?>" style="top:<?php echo (($height/2) - 23); ?>px;"><div>NEXT</div></div>
	<?php endif; ?>
	<div class="gkIsImage<?php echo ' '.$image_position; ?>" style="width:<?php echo $width; ?>px;height:<?php echo $height;?>px;">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
				// cleaning variables
				unset($path, $title);
				// creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_corporate2_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
				
                if($this->config['image_show_data'][$i]->type == "k2"){
                  	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                } else {
                // creating slide title
				$title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
                }
            ?>
			<div class="gkIsSlide" style="z-index: <?php echo $i+1; ?>;" title="<?php echo $title; ?>"><?php echo $path; ?></div>
			<?php endif; ?>
		<?php endfor; ?>	
	</div>

	<div class="gkIsContent" style="position:relative;margin-<?php echo $image_position_margin; ?>:<?php echo $width; ?>px;">
		<?php if($show_list == 1) : ?>
		<div class="gkIsList<?php echo ' '.$list_position; ?>" style="width:<?php echo 100 - $art_width; ?>%;">
			<div>
				<h3>
					<?php if($list_title_small != '') : ?>
					<span class="gkTitleSmall"><?php echo $list_title_small; ?></span>
					<?php endif; ?>
					<?php if($list_title_big != '') : ?>
					<span class="gkTitleBig"><?php echo $list_title_big; ?></span>
					<?php endif; ?>
				</h3>
				
				
				
				<ul>
					<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
					<?php if($this->config['image_show_data'][$i]->published) : ?>
					<li><?php 
                        if($this->config['image_show_data'][$i]->type == "k2"){
                  	         $str_len = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                        } else {
                             $str_len = JString::strlen(strip_tags(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]));
						}
                        
                        if($this->config['image_show_data'][$i]->type == "k2"){
                        echo JString::substr(stripslashes(strip_tags($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"])), 0, $list_char_count); 
                        } else {
                        echo JString::substr(stripslashes(strip_tags(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"])), 0, $list_char_count); 
                        }
						if($str_len > $list_char_count){
							echo $list_text_overflow;
						}
					?></li>
					<?php endif; ?>
					<?php endfor; ?>
				</ul>
				
				
			</div>
		</div>
		<?php endif; ?>



		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
		<?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
				// cleaning variables
				unset($text, $title, $link, $exploded_text);
				// creating slide text
				if($this->config['image_show_data'][$i]->type == "k2"){
   	                $title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                    $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                    $text = $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["introtext"];
                } else {
                    $title = htmlspecialchars(($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->name : $this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
				    $link = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->url : $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
                    $text = ($this->config['image_show_data'][$i]->type == "text") ? $this->config['image_show_data'][$i]->content : $this->articles[$this->config['image_show_data'][$i]->art_id]["introtext"];
				}
                $text = htmlspecialchars_decode($text);
				if($clean_xhtml == 1) $text = strip_tags($text);
				$exploded_text = explode(" ", stripslashes($text));
				$text = '';

				for($j = 0; $j < $wordcount; $j++) {
					if(isset($exploded_text[$j])) {
						$text .= $exploded_text[$j]." ";
					}
				}
				
				
			?>
			
			
		<div class="gkIsArt gkUnvisible <?php if($i == 0) echo 'gkFirstSlide'; ?>" style="position:absolute;z-index:<?php echo $i+5; ?>;width:<?php echo ($show_list == 1) ? $art_width : '100'; ?>%;<?php if($show_list == 1 && $list_position == 'gkLeftFloat') echo 'margin-left:'.(100-$art_width).'%;'; ?>">
			<div>
				<?php if($item_title == 1): ?>
					<?php if($title_link == 1): ?>
						<h4><a href="<?php echo $link; ?>"><?php echo $title; ?></a></h4>
					<?php else: ?>
						<h4><?php echo $title; ?></h4>
					<?php endif; ?>
				<?php endif; ?>

				<p><?php echo $text; ?></p>
				
				<?php if($readmore_button == 1) : ?>
				<a href="<?php echo $link; ?>" class="readon"><?php echo $readmore_text; ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; ?>
		<?php endfor; ?>
	</div>
</div>