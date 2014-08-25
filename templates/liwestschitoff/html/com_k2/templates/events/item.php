<?php
/**
 * @version		$Id: item.php 1492 2012-02-22 17:40:09Z joomlaworks@gmail.com $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
// дополнительные поля материала K2
$extrafields = array();
foreach($this->item->extra_fields as $item)
{
    $extraField[$item->id]= $item->name;
    $extrafields[$item->id] = $item->value;
}
?>




<div class="row">
<!-- Start K2 Item Layout -->
<div class="col-lg-9 col-md-9 col-sm-8">

    <?php if($this->item->params->get('catItemImage') && !empty($this->item->image)): ?>
        <!-- Item Image -->
        <div class="event-image">
            <img src="<?php echo $this->item->image; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" style="width:<?php echo $this->item->imageWidth; ?>px; height:auto;" />
        </div>
    <?php endif; ?>


    <?php if($this->item->params->get('catItemTitle')): ?>
        <!-- Item title -->
        <h2>
            <?php if ($this->item->params->get('catItemTitleLinked')): ?>
                <a href="<?php echo $this->item->link; ?>">
                    <?php echo $this->item->title; ?>
                </a>
            <?php else: ?>
                <?php echo $this->item->title; ?>
            <?php endif; ?>

            <?php if($this->item->params->get('catItemFeaturedNotice') && $this->item->featured): ?>
                <!-- Featured flag -->
                <span>
                                        <sup>
                                            <?php echo JText::_('K2_FEATURED'); ?>
                                        </sup>
                                    </span>
            <?php endif; ?>
        </h2>
    <?php endif; ?>
    <?php if(!empty($this->item->fulltext)): ?>
        <?php if($this->item->params->get('itemIntroText')): ?>
            <!-- Item introtext -->
            <div class="itemIntroText">
                <?php echo $this->item->introtext; ?>
            </div>
        <?php endif; ?>
        <?php if($this->item->params->get('itemFullText')): ?>
            <!-- Item fulltext -->
            <div class="itemFullText">
                <?php echo $this->item->fulltext; ?>
            </div>
        <?php endif; ?>
        <?php else: ?>
        <!-- Item text -->

            <?php echo $this->item->introtext; ?>
    <?php endif; ?>

    <?php if($this->item->params->get('itemImageGallery') && !empty($this->item->gallery)): ?>
        <!-- Item image gallery -->

        <div class="itemImageGallery">
            <h3><?php echo JText::_('K2_IMAGE_GALLERY'); ?></h3>
            <?php echo $this->item->gallery; ?>
        </div>
    <?php endif; ?>

</div>


    <div class="col-lg-3 col-md-3 col-sm-4">
        <div class="event-meta">
            <?php if($extrafields[5]): ?>
                <div class="event-meta-block animate-onscroll">
                    <i class="icons icon-calendar"></i>
                    <p class="title">Начало — Завершение</p>
                    <p><?php echo $extrafields[5];?></p>
                </div>
            <?php endif; ?>
            <?php if($extrafields[6]): ?>
                <div class="event-meta-block animate-onscroll">
                    <i class="icons icon-clock"></i>
                    <p class="title">Начало — Завершение</p>
                    <p><?php echo $extrafields[6];?></p>
                </div>
            <?php endif; ?>
            <?php if($extrafields[7]): ?>
                <div class="event-meta-block animate-onscroll">
                    <i class="icons icon-location"></i>
                    <p class="title">Место проведения</p>
                    <p><?php echo $extrafields[7];?></p>
                </div>
            <?php endif; ?>
            <?php if($extrafields[9]): ?>
                <div class="event-meta-block animate-onscroll">
                    <i class="icons icon-ticket"></i>
                    <p class="title">Стоимость</p>
                    <p><?php echo $extrafields[9];?></p>
                </div>
            <?php endif; ?>
            <?php if($extrafields[9]): ?>
                <div class="event-meta-block animate-onscroll">
                    <i class="icons icon-folder-open"></i>
                    <p class="title">Тематика мероприятия</p>
                    <p><?php echo $extrafields[9];?></p>
                </div>
            <?php endif; ?>
            <?php if($extrafields[10]): ?>
                <div class="event-meta-block animate-onscroll">
                    <i class="icons icon-phone"></i>
                    <p class="title">Телефон</p>
                    <p><?php echo $extrafields[10];?></p>
                </div>
            <?php endif; ?>
            <?php if($extrafields[11]): ?>
                <div class="event-meta-block animate-onscroll">
                    <i class="icons icon-mail-alt"></i>
                    <p class="title">Email</p>
                    <p><?php echo $extrafields[11];?></p>
                </div>
            <?php endif; ?>





            <div class="event-meta-block animate-onscroll">
                <?php if($this->item->params->get('catItemTags') && count($this->item->tags)): ?>
                <!-- Item tags -->
                <i class="icons icon-tags"></i>
                <p class="title"><?php echo JText::_('K2_TAGGED_UNDER'); ?></p>
                <p>
                    <?php foreach ($this->item->tags as $tag): ?>
                        <a href="<?php echo $tag->link; ?>"><?php echo $tag->name; ?></a> <?php echo " " ?>
                    <?php endforeach; ?>
                <p>
                    <?php endif; ?>
            </div>
            <div class="event-meta-block animate-onscroll">
                <i class="icons icon-share"></i>
                <p class="title">Поделиться</p>
                <ul class="social-share">
                    <li class="facebook"><a href="#" class="tooltip-ontop" title="Facebook"><i class="icons icon-facebook"></i></a></li>
                    <li class="twitter"><a href="#" class="tooltip-ontop" title="Twitter"><i class="icons icon-twitter"></i></a></li>
                    <li class="google"><a href="#" class="tooltip-ontop" title="Google Plus"><i class="icons icon-gplus"></i></a></li>
                    <li class="pinterest"><a href="#" class="tooltip-ontop" title="Pinterest"><i class="icons icon-pinterest-3"></i></a></li>
                    <li class="email"><a href="#" class="tooltip-ontop" title="Email"><i class="icons icon-mail"></i></a></li>
                </ul>
            </div>
        </div>



        <!-- Plugins: BeforeDisplay -->
    <?php echo $this->item->event->BeforeDisplay; ?>

    <!-- K2 Plugins: K2BeforeDisplay -->
    <?php echo $this->item->event->K2BeforeDisplay; ?>

    <!-- Plugins: AfterDisplayTitle -->
    <?php echo $this->item->event->AfterDisplayTitle; ?>

    <!-- K2 Plugins: K2AfterDisplayTitle -->
    <?php echo $this->item->event->K2AfterDisplayTitle; ?>

    <!-- Plugins: BeforeDisplayContent -->
    <?php echo $this->item->event->BeforeDisplayContent; ?>

    <!-- K2 Plugins: K2BeforeDisplayContent -->
    <?php echo $this->item->event->K2BeforeDisplayContent; ?>
    <!-- Plugins: AfterDisplayContent -->
    <?php echo $this->item->event->AfterDisplayContent; ?>

    <!-- K2 Plugins: K2AfterDisplayContent -->
    <?php echo $this->item->event->K2AfterDisplayContent; ?>

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

            <?php echo $this->item->gallery; ?>
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


</div>
<!-- End K2 Item Layout -->
    <!--item navigation-->
    <?php if($this->item->params->get('itemNavigation') && !JRequest::getCmd('print') && (isset($this->item->nextLink) || isset($this->item->previousLink))): ?>

        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6 button-pagination align-left">
                <?php if(isset($this->item->previousLink)): ?>
                    <a class="button big previous" href="<?php echo $this->item->previousLink; ?>">
                        Сюда
                    </a>
                <?php endif; ?>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-6 button-pagination align-right">
                <?php if(isset($this->item->nextLink)): ?>
                    <a class="button big next" href="<?php echo $this->item->nextLink; ?>">
                        Туда
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
