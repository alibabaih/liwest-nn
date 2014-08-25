<?php
/**
 * @version		$Id: category_item.php 1251 2011-10-19 17:50:13Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
K2HelperUtilities::setDefaultImage($this->item, 'itemlist', $this->params);
?>

	<div class="catItemView-articles group<?php echo ucfirst($this->item->itemGroup); ?><?php echo ($this->item->featured) ? ' catItemIsFeatured' : ''; ?><?php if($this->item->params->get('pageclass_sfx')) echo ' '.$this->item->params->get('pageclass_sfx'); ?>">
		<?php echo $this->item->event->BeforeDisplay; ?>
		<?php echo $this->item->event->K2BeforeDisplay; ?>
		<?php echo $this->item->event->AfterDisplayTitle; ?>
		<?php echo $this->item->event->K2AfterDisplayTitle; ?>
		<div class="catItemBody-articles">
			<?php echo $this->item->event->BeforeDisplayContent; ?>
			<?php echo $this->item->event->K2BeforeDisplayContent; ?>

			<?php if($this->item->params->get('catItemImage') && !empty($this->item->image)): ?>
				<div class="row-fluid">
					<div class="span12">
						<span class="catItemImage">
							<a href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">
								<img src="<?php echo $this->item->image; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" style="width:100%; height:auto;" />
							</a>
						</span>
					</div>
				</div>
			<?php endif; ?>

			<?php if($this->item->params->get('catItemIntroText')): ?>
					<div class="catItemHeader-articles">
						<?php if($this->item->params->get('catItemDateCreated')): ?>
							<div class="catItemDateCreated-articles">
							<?php echo JHTML::_('date', $this->item->created , JText::_('K2_DATE_FORMAT_LC2')); ?>
						<?php endif; ?>
					</div>
									
				<?php if($this->item->params->get('catItemTitle')): ?>
					<?php if(isset($this->item->editLink)): ?>
						<div class="catItemEditLink">
							<a class="modal" rel="{handler:'iframe',size:{x:990,y:550}}" href="<?php echo $this->item->editLink; ?>">
								<?php echo JText::_('K2_EDIT_ITEM'); ?>
							</a>
						</div>
					<?php endif; ?>
										
					<div class="catItemTitle-articles">
						<p>
							<span class="catItemTitle-articles-p">
								<?php if ($this->item->params->get('catItemTitleLinked')): ?>
									<a href="<?php echo $this->item->link; ?>"><?php echo $this->item->title; ?></a>
								<?php endif; ?>
							</span>
						</p>

						<?php if($this->item->params->get('catItemFeaturedNotice') && $this->item->featured): ?>
							<span>
								<sup>
									<?php echo JText::_('K2_FEATURED'); ?>
								</sup>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
		</div>
								
		<div class="catItemIntroText-articles">
			<?php $this->item->introtext = mb_substr($this->item->introtext, '0', '500');
			echo $this->item->introtext. '...'; ?>
			<?php endif; ?>

				<?php if(
					$this->item->params->get('catItemHits') ||
					$this->item->params->get('catItemCategory') ||
					$this->item->params->get('catItemTags') ||
					$this->item->params->get('catItemAttachments')
					): ?>

					

				<?php endif; ?>
		</div>
		<?php echo $this->item->event->AfterDisplay; ?>
		<?php echo $this->item->event->K2AfterDisplay; ?>
	</div>
</div>
