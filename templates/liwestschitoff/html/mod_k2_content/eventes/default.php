<?php
/**
 * @version		$Id: default.php 1251 2011-10-19 17:50:13Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>


    <?php if($params->get('itemPreText')): ?>
        <p class="modulePretext"><?php echo $params->get('itemPreText'); ?></p>
    <?php endif; ?>

<div class="sidebar-box white animate-onscroll">
    <h3>События</h3>
    <?php if(count($items)): ?>
        <ul class="upcoming-events">
            <?php foreach ($items as $key=>$item):	?>
                <li class="<?php echo ($key%2) ? "odd" : "even"; if(count($items)==$key+1) echo ' lastItem'; ?>">

                    <!-- Plugins: BeforeDisplay -->
                    <?php echo $item->event->BeforeDisplay; ?>

                    <!-- K2 Plugins: K2BeforeDisplay -->
                    <?php echo $item->event->K2BeforeDisplay; ?>



                    <div class="date">
                        <span>
                        <?php if($params->get('itemDateCreated')): ?>
                            <span class="moduleItemDateCreated"><?php echo JHTML::_('date', $item->created, d); ?></span>
                            <span class="moduleItemDateCreated"><?php echo JHTML::_('date', $item->created, M); ?></span>
                        <?php endif; ?>
                        </span>
                    </div>

                    <div class="event-content">
                        <?php if($params->get('itemTitle')): ?>
                            <h6></h6><a class="moduleItemTitle" href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h6>
                        <?php endif; ?>

                        <ul class="event-meta">
                            <li> <i class="icons icon-calendar"></i> <?php echo $extrafields[5];?></li>
                            <li><i class="icons icon-location"></i> <?php echo $extrafields[7];?></li>
                        </ul>
                    </div>



                    <!-- Plugins: AfterDisplayTitle -->
                    <?php echo $item->event->AfterDisplayTitle; ?>

                    <!-- K2 Plugins: K2AfterDisplayTitle -->
                    <?php echo $item->event->K2AfterDisplayTitle; ?>

                    <!-- Plugins: BeforeDisplayContent -->
                    <?php echo $item->event->BeforeDisplayContent; ?>

                    <!-- K2 Plugins: K2BeforeDisplayContent -->
                    <?php echo $item->event->K2BeforeDisplayContent; ?>











                    <!-- Plugins: AfterDisplayContent -->
                    <?php echo $item->event->AfterDisplayContent; ?>

                    <!-- K2 Plugins: K2AfterDisplayContent -->
                    <?php echo $item->event->K2AfterDisplayContent; ?>



                    <!-- Plugins: AfterDisplay -->
                    <?php echo $item->event->AfterDisplay; ?>

                    <!-- K2 Plugins: K2AfterDisplay -->
                    <?php echo $item->event->K2AfterDisplay; ?>


                </li>
            <?php endforeach; ?>
        </ul>
        <a href="../events" class="button transparent button-arrow">Больше событий</a>
    <?php endif; ?>
</div>
