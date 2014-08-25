<?php
/**
 * @version		$Id: default.php 1499 2012-02-28 10:28:38Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>


<div class="owl-carousel" data-max-items="3">

    <?php //if(count($items)): ?>

    <?php foreach ($items as $key=>$item):	?>
        <div>
            <div class="blog-post animate-onscroll">

                <!-- Plugins: BeforeDisplay -->
                <?php echo $item->event->BeforeDisplay; ?>
                <!-- K2 Plugins: K2BeforeDisplay -->
                <?php echo $item->event->K2BeforeDisplay; ?>
                <!-- Plugins: AfterDisplayTitle -->
                <?php echo $item->event->AfterDisplayTitle; ?>
                <!-- K2 Plugins: K2AfterDisplayTitle -->
                <?php echo $item->event->K2AfterDisplayTitle; ?>
                <!-- Plugins: BeforeDisplayContent -->
                <?php echo $item->event->BeforeDisplayContent; ?>
                <!-- K2 Plugins: K2BeforeDisplayContent -->
                <?php echo $item->event->K2BeforeDisplayContent; ?>

                <?php if($params->get('itemImage') || $params->get('itemIntroText')): ?>
                    <?php if($params->get('itemImage') && isset($item->image)): ?>
                        <div class="post-image">
                            <a class="moduleItemImage" href="<?php echo $item->link; ?>" title="<?php echo JText::_('K2_CONTINUE_READING'); ?> &quot;<?php echo K2HelperUtilities::cleanHtml($item->title); ?>&quot;">
                                <img src="<?php echo $item->image; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($item->title); ?>"/>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if($params->get('itemTitle')): ?>
                        <h4 class="post-title"><a class="moduleItemTitle" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h4>
                    <?php endif; ?>
                    <div class="post-meta">
                        <?php if($params->get('itemAuthor')): ?>
                            <span>
                          <?php echo K2HelperUtilities::writtenBy($item->authorGender); ?>

                                <?php if(isset($item->authorLink)): ?>
                                    <a rel="author" title="<?php echo K2HelperUtilities::cleanHtml($item->author); ?>" href="<?php echo $item->authorLink; ?>"><?php echo $item->author; ?></a>
                                <?php else: ?>
                                    <?php echo $item->author; ?>
                                <?php endif; ?>
                      </span>
                        <?php endif; ?>
                        <?php if($params->get('itemDateCreated')): ?>
                            <span> <?php echo JHTML::_('date', $item->created, JText::_('K2_DATE_FORMAT_LC2')); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if($params->get('itemIntroText')): ?>
                        <?php echo $item->introtext; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Plugins: AfterDisplayContent -->
                <?php echo $item->event->AfterDisplayContent; ?>
                <!-- K2 Plugins: K2AfterDisplayContent -->
                <?php echo $item->event->K2AfterDisplayContent; ?>

                <?php if($params->get('itemReadMore') && $item->fulltext): ?>
                    <a class="button read-more-button big button-arrow" href="<?php echo $item->link; ?>">
                        <?php echo JText::_('Подробнее'); ?>
                    </a>
                <?php endif; ?>

                <!-- Plugins: AfterDisplay -->
                <?php echo $item->event->AfterDisplay; ?>
                <!-- K2 Plugins: K2AfterDisplay -->
                <?php echo $item->event->K2AfterDisplay; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php //endif; ?>
</div>
