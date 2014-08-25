<?php
/**
 * @version		$Id: authors.php 1251 2011-10-19 17:50:13Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="k2ModuleBox<?php echo $module->id; ?>" class="k2AuthorsListBlock<?php if($params->get('moduleclass_sfx')) echo ' '.$params->get('moduleclass_sfx'); ?>">
  <ul>
    <?php foreach ($authors as $author): ?>
    <li>
      <?php if ($params->get('authorAvatar')): ?>
      <a class="k2Avatar abAuthorAvatar" rel="author" href="<?php echo $author->link; ?>" title="<?php echo K2HelperUtilities::cleanHtml($author->name); ?>">
      	<img src="<?php echo $author->avatar; ?>" alt="<?php echo K2HelperUtilities::cleanHtml($author->name); ?>" style="width:<?php echo $avatarWidth; ?>px;height:auto;" />
      </a>
      <?php endif; ?>

      <a class="abAuthorName" rel="author" href="<?php echo $author->link; ?>">
      	<?php echo $author->name; ?>

      	<?php if ($params->get('authorItemsCounter')): ?>
      	<span>(<?php echo $author->items; ?>)</span>
      	<?php endif; ?>
      </a>

      <?php if ($params->get('authorLatestItem')): ?>
      <a class="abAuthorLatestItem" href="<?php echo $author->latest->link; ?>" title="<?php echo K2HelperUtilities::cleanHtml($author->latest->title); ?>">
      	<?php echo $author->latest->title; ?>
	      <span class="abAuthorCommentsCount">
	      	(<?php echo $author->latest->numOfComments; ?> <?php echo JText::_('K2_COMMENTS'); ?>)
	      </span>
      </a>
      <?php endif; ?>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
