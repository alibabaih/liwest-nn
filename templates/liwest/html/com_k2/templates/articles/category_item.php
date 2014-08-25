<?php
/**
 * @version		$Id: category_item.php 1251 2011-10-19 17:50:13Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Define default image size (do not change)
K2HelperUtilities::setDefaultImage($this->item, 'itemlist', $this->params);

?>

<!-- Start K2 Item Layout -->
<div class=" group<?php echo ucfirst($this->item->itemGroup); ?><?php echo ($this->item->featured) ? ' catItemIsFeatured' : ''; ?><?php if($this->item->params->get('pageclass_sfx')) echo ' '.$this->item->params->get('pageclass_sfx'); ?>">
	<div>
		<div><div class="itemView">
			<!-- Plugins: BeforeDisplay -->
			<?php echo $this->item->event->BeforeDisplay; ?>

			<!-- K2 Plugins: K2BeforeDisplay -->
			<?php echo $this->item->event->K2BeforeDisplay; ?>

			

		  <!-- Plugins: AfterDisplayTitle -->
		  <?php echo $this->item->event->AfterDisplayTitle; ?>

		  <!-- K2 Plugins: K2AfterDisplayTitle -->
		  <?php echo $this->item->event->K2AfterDisplayTitle; ?>

			<?php if($this->item->params->get('catItemRating')): ?>
			<!-- Item Rating -->
			<div class="catItemRatingBlock">
				<span><?php echo JText::_('K2_RATE_THIS_ITEM'); ?></span>
				<div class="itemRatingForm">
					<ul class="itemRatingList">
						<li class="itemCurrentRating" id="itemCurrentRating<?php echo $this->item->id; ?>" style="width:<?php echo $this->item->votingPercentage; ?>%;"></li>
						<li><a href="#" rel="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_1_STAR_OUT_OF_5'); ?>" class="one-star">1</a></li>
						<li><a href="#" rel="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_2_STARS_OUT_OF_5'); ?>" class="two-stars">2</a></li>
						<li><a href="#" rel="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_3_STARS_OUT_OF_5'); ?>" class="three-stars">3</a></li>
						<li><a href="#" rel="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_4_STARS_OUT_OF_5'); ?>" class="four-stars">4</a></li>
						<li><a href="#" rel="<?php echo $this->item->id; ?>" title="<?php echo JText::_('K2_5_STARS_OUT_OF_5'); ?>" class="five-stars">5</a></li>
					</ul>
					<div id="itemRatingLog<?php echo $this->item->id; ?>" class="itemRatingLog"><?php echo $this->item->numOfvotes; ?></div>
					<div class="clr"></div>
				</div>
				<div class="clr"></div>
			</div>
			<?php endif; ?>

		  <div class="catItemBody">

			  <!-- Plugins: BeforeDisplayContent -->
			  <?php echo $this->item->event->BeforeDisplayContent; ?>

			  <!-- K2 Plugins: K2BeforeDisplayContent -->
			  <?php echo $this->item->event->K2BeforeDisplayContent; ?>

			  <?php if($this->item->params->get('catItemImage') && !empty($this->item->image)): ?>
			  <!-- Item Image -->

				  <div class="catItemImage">
				    <a href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">
				    	<img class="img-polaroid" src="<?php echo $this->item->image; ?>"  alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" style="width:100%; height:auto;" />
				    </a>
				  </div>


			  <?php endif; ?>

			  <?php if($this->item->params->get('catItemIntroText')): ?>

				  <div class="catItemHeader">
					<?php if($this->item->params->get('catItemDateCreated')): ?>
					<!-- Date created -->
					<div class="catItemDateCreated">
						<?php echo JHTML::_('date', $this->item->created , JText::_('K2_DATE_FORMAT_LC2')); ?>
					</div>
					<?php endif; ?>

				  <?php if($this->item->params->get('catItemTitle')): ?>
				  <!-- Item title -->
				  <h3 class="catItemTitle">

				  	<?php if ($this->item->params->get('catItemTitleLinked')): ?>
						<a href="<?php echo $this->item->link; ?>">
				  		<?php echo $this->item->title; ?>
				  	</a>
				  	<?php else: ?>
				  	<?php echo $this->item->title; ?>
				  	<?php endif; ?>
				  	</h3>
				  	<?php if($this->item->params->get('catItemFeaturedNotice') && $this->item->featured): ?>
				  	<!-- Featured flag -->
				  	<span>
					  	<sup>
					  		<?php echo JText::_('K2_FEATURED'); ?>
					  	</sup>
				  	</span>
				  	<?php endif; ?>
				  </span>
				  <?php endif; ?>
			  </div>

			  <!-- Item introtext -->
			  <div class="catItemIntroText">
			  	<?php echo $this->item->introtext; ?>
			  </div>

			  <?php endif; ?>

				<!-- фикс
					<div class="clr"></div>
				</div>


			  <?php if($this->item->params->get('catItemExtraFields') && count($this->item->extra_fields)): ?>
			  <!-- Item extra fields -->
			  <div class="catItemExtraFields">
			  	<h4><?php echo JText::_('K2_ADDITIONAL_INFO'); ?></h4>
			  	<ul>
					<?php foreach ($this->item->extra_fields as $key=>$extraField): ?>
					<?php if($extraField->value): ?>
					<li class="<?php echo ($key%2) ? "odd" : "even"; ?> type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?>">
						<span class="catItemExtraFieldsLabel"><?php echo $extraField->name; ?></span>
						<span class="catItemExtraFieldsValue"><?php echo $extraField->value; ?></span>
					</li>
					<?php endif; ?>
					<?php endforeach; ?>
					</ul>
		  	<!-- фикс
				<div class="clr"></div>
			-->
			  </div>
			  <?php endif; ?>

			  <!-- Plugins: AfterDisplayContent -->
			  <?php echo $this->item->event->AfterDisplayContent; ?>

			  <!-- K2 Plugins: K2AfterDisplayContent -->
			  <?php echo $this->item->event->K2AfterDisplayContent; ?>

			  <!-- фикс
			  	<div class="clr"></div>
			  -->
			  	
		  </div>

		  <?php if(
		  $this->item->params->get('catItemCategory') ||
		  $this->item->params->get('catItemTags')
		  ): ?>
		  <div class="row-fluid catItemLinks">

			  <?php if($this->item->params->get('catItemTags') && count($this->item->tags)): ?>
			  <!-- Item tags -->
			  <div class="span6 catItemTagsBlock">
				  <span><i class="icon-tags"></i></span>
				  <ul class="catItemTags">
				    <?php foreach ($this->item->tags as $tag): ?>
				    <li><a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a></li>
				    <?php endforeach; ?>
				  </ul>

			  </div>
			  <?php endif; ?>

				<?php if($this->item->params->get('catItemCategory')): ?>
				<!-- Item category name -->
				<div class="span6 catItemCategory">
					<span><i class="icon-folder-open"></i></span>
					<a href="<?php echo $this->item->category->link; ?>"><?php echo $this->item->category->name; ?></a>
				</div>
				<?php endif; ?>


		  </div>
		  <?php endif; ?>
		  	<!-- фикс
				<div class="clr"></div>
			-->

		  <?php if($this->item->params->get('catItemVideo') && !empty($this->item->video)): ?>
		  <!-- Item video -->
		  <div class="catItemVideoBlock">
		  	<h3><?php echo JText::_('K2_RELATED_VIDEO'); ?></h3>
				<?php if($this->item->videoType=='embedded'): ?>
				<div class="catItemVideoEmbedded">
					<?php echo $this->item->video; ?>
				</div>
				<?php else: ?>
				<span class="catItemVideo"><?php echo $this->item->video; ?></span>
				<?php endif; ?>
		  </div>
		  <?php endif; ?>

		  <?php if($this->item->params->get('catItemImageGallery') && !empty($this->item->gallery)): ?>
		  <!-- Item image gallery -->
		  <div class="catItemImageGallery">
			  <h4><?php echo JText::_('K2_IMAGE_GALLERY'); ?></h4>
			  <?php echo $this->item->gallery; ?>
		  </div>
		  <?php endif; ?>

		  <!-- фикс
		  	<div class="clr"></div>
		  -->

			<?php if($this->item->params->get('catItemCommentsAnchor') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1')) ): ?>
			<!-- Anchor link to comments below -->
			<div class="catItemCommentsLink">
				<?php if(!empty($this->item->event->K2CommentsCounter)): ?>
					<!-- K2 Plugins: K2CommentsCounter -->
					<?php echo $this->item->event->K2CommentsCounter; ?>
				<?php else: ?>
					<?php if($this->item->numOfComments > 0): ?>
					<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
						<?php echo $this->item->numOfComments; ?> <?php echo ($this->item->numOfComments>1) ? JText::_('K2_COMMENTS') : JText::_('K2_COMMENT'); ?>
					</a>
					<?php else: ?>
					<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
						<?php echo JText::_('K2_BE_THE_FIRST_TO_COMMENT'); ?>
					</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<?php endif; ?>


			<?php if($this->item->params->get('catItemDateModified')): ?>
			<!-- Item date modified -->
			<?php if($this->item->modified != $this->nullDate && $this->item->modified != $this->item->created ): ?>
			<span class="catItemDateModified">
				<?php echo JText::_('K2_LAST_MODIFIED_ON'); ?> <?php echo JHTML::_('date', $this->item->modified, JText::_('K2_DATE_FORMAT_LC2')); ?>
			</span>
			<?php endif; ?>
			<?php endif; ?>

		  <!-- Plugins: AfterDisplay -->
		  <?php echo $this->item->event->AfterDisplay; ?>

		  <!-- K2 Plugins: K2AfterDisplay -->
		  <?php echo $this->item->event->K2AfterDisplay; ?>

			  	<!-- фикс
				<div class="clr"></div>
			-->
		</div><div class="bottom-white-bg-k2"></div></div>
		<div class="offset1"></div>
	</div>

</div>
<!-- End K2 Item Layout -->
