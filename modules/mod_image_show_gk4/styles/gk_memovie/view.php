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

<div id="gkIs-<?php echo $this->config['module_id'];?>" class="gkIsWrapper-gk_memovie">
	<div class="gkIsPreloader"></div>
	
	<div class="gkIsImage" style="width: <?php echo $width; ?>px;height: <?php echo $height; ?>px;">
		<?php for($i = 0; $i < count($this->config['image_show_data']); $i++) : ?>
        
        <?php if($this->config['image_show_data'][$i]->published) : ?>
			<?php 
				
				unset($path, $title, $link);
				// creating slide path
				$path = $uri->root().'modules/mod_image_show_gk4/cache/'.GKIS_Memovie_Image::translateName($this->config['image_show_data'][$i]->image, $this->config['module_id']);
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
	
	<?php if($this->config['config']->gk_memovie->gk_memovie_interface) : ?>
	<div class="gkIsPrev<?php echo ($this->config['config']->gk_memovie->gk_memovie_interface_animation == 0) ? '': ' anim'; ?>" style="bottom:<?php echo $this->config['config']->gk_memovie->gk_memovie_interface_y; ?>px;left:<?php echo $this->config['config']->gk_memovie->gk_memovie_interface_x; ?>px;">&laquo;</div>
	<div class="gkIsNext" style="bottom:<?php echo $this->config['config']->gk_memovie->gk_memovie_interface_y; ?>px;left:<?php echo $this->config['config']->gk_memovie->gk_memovie_interface_x; ?>px;">&raquo;</div>
	<?php endif; ?>
</div>