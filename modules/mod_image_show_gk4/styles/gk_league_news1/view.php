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

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_league_news1">
	<div class="gkIsPreloader"></div>
	
	<div class="gkIsImages">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
        <?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
				
				unset($path, $title, $link);
                // creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_LeagueNews1_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
                //
                if($this->config['image_show_data'][$i]->type == "k2"){
                   	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
                    $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
                } else {
                	if($this->config['image_show_data'][$i]->type == "text") {
                		$title = htmlspecialchars($this->config['image_show_data'][$i]->name);
                		$link = $this->config['image_show_data'][$i]->url;	
                	} else {
                		$title = htmlspecialchars($this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
                		$link = $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
                	}
                }
                
                if(strlen($title) > $this->config['config']->gk_league_news1->gk_league_news1_title_chars) {
                	$title = substr($title, 0, $this->config['config']->gk_league_news1->gk_league_news1_title_chars) . '&hellip;';
                	$title = str_replace(array('&&', '&a&' , '&am&', '&amp&', '&q&', '&qu&', '&quo&', '&quot'), '&', $title);
                }
			?>
			
			<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
				<a href="<?php echo $link; ?>" title="<?php echo $title; ?>">
					<?php echo $path; ?>
				</a>
			</div>
			<?php endif; ?>
		<?php endfor; ?>
		<a href="#" class="gkIsMoreNews"><?php echo JText::_('TPL_GK_LANG_IS_MORE_NEWS'); ?></a>
	</div>
	
	<div class="gkMoreNews">
		<div>
			<div>
			<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
			<?php if($this->config['image_show_data'][$i]->published) : ?>
				<?php 
					
					unset($path, $title, $link, $desc);
			        // creating slide path
					$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_LeagueNews1_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id'], true);
			        //
			        if($this->config['image_show_data'][$i]->type == "k2"){
			           	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
			           	$desc = htmlspecialchars(strip_tags($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["text"]));
			            $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
			        } else {
			        	if($this->config['image_show_data'][$i]->type == "text") {
			        		$title = htmlspecialchars($this->config['image_show_data'][$i]->name);
			        		$desc = htmlspecialchars(strip_tags($this->config['image_show_data'][$i]->text));
			        		$link = $this->config['image_show_data'][$i]->url;	
			        	} else {
			        		$title = htmlspecialchars($this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
			        		$desc = htmlspecialchars(strip_tags($this->articles[$this->config['image_show_data'][$i]->art_id]["text"]));
			        		$link = $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
			        	}
			        }
			        
			        if(strlen($title) > $this->config['config']->gk_league_news1->gk_league_news1_title_chars) {
			        	$title = substr($title, 0, $this->config['config']->gk_league_news1->gk_league_news1_title_chars) . '&hellip;';
			        	$title = str_replace(array('&&', '&a&' , '&am&', '&amp&', '&q&', '&qu&', '&quo&', '&quot'), '&', $title);
			        }
			        
			        if(strlen($desc) > $this->config['config']->gk_league_news1->gk_league_news1_desc_chars) {
			        	$desc = substr($desc, 0, $this->config['config']->gk_league_news1->gk_league_news1_desc_chars) . '&hellip;';
			        	$desc = str_replace(array('&&', '&a&' , '&am&', '&amp&', '&q&', '&qu&', '&quo&', '&quot'), '&', $desc);
			        }
				?>
				
				<div class="gkMoreNewsItem">
					<a href="<?php echo $link; ?>" title="<?php echo $title; ?>">
						<img src="<?php echo $path; ?>" alt="<?php echo $title; ?>" />
					</a>
					<div>
					<h3>
						<a href="<?php echo $link; ?>" title="<?php echo $title; ?>">
							<?php echo $title; ?>
						</a>
					</h3>
					<p>
						<a href="<?php echo $link; ?>" title="<?php echo $title; ?>">
							<?php echo $desc; ?>
						</a>
					</p>
					</div>
				</div>
				<?php endif; ?>
			<?php endfor; ?>
			</div>
		</div>
	</div>
	
	<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
	<?php 
		if($this->config['image_show_data'][$i]->published) : 
		//
		unset($title, $link);
        //
        if($this->config['image_show_data'][$i]->type == "k2"){
           	$title = htmlspecialchars($this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["title"]);
            $link =  $this->articlesK2[$this->config['image_show_data'][$i]->artK2_id]["link"];
        } else {
        	if($this->config['image_show_data'][$i]->type == "text") {
        		$title = htmlspecialchars($this->config['image_show_data'][$i]->name);
        		$link = $this->config['image_show_data'][$i]->url;	
        	} else {
        		$title = htmlspecialchars($this->articles[$this->config['image_show_data'][$i]->art_id]["title"]);
        		$link = $this->articles[$this->config['image_show_data'][$i]->art_id]["link"];	
        	}
        }
        
        if(strlen($title) > $this->config['config']->gk_league_news1->gk_league_news1_title_chars) {
        	$title = substr($title, 0, $this->config['config']->gk_league_news1->gk_league_news1_title_chars) . '&hellip;';
        	$title = str_replace(array('&&', '&a&' , '&am&', '&amp&', '&q&', '&qu&', '&quo&', '&quot'), '&', $title);
        }
        
	?>
	<h3 class="gkIsTitle">
		<a href="<?php echo $link; ?>" title="<?php echo $title; ?>">
			<?php echo $title; ?>
		</a>
	</h3>
	<?php endif; ?>
	<?php endfor; ?>
	
	<div class="gkIsLoader">
		<div class="gkIsProgress">Loading...</div>
	</div>
</div>