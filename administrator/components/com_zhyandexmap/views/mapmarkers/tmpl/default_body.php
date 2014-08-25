<?php
/*------------------------------------------------------------------------
# com_zhyandexmap - Zh YandexMap
# ------------------------------------------------------------------------
# author    Dmitry Zhuk
# copyright Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# Websites: http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php 
	$user		= JFactory::getUser();

	foreach($this->items as $i => $item): 
	$canChange	= $user->authorise('core.edit.state',	'com_zhyandexmap.category.'.$item->catid);
?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>
			<?php echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_zhyandexmap&task=mapmarker.edit&id=' . $item->id); ?>">
				<?php echo $item->title; ?>
			</a>
		</td>
		<td>
			<?php echo $item->mapname; ?>
		</td>
		<td align="center">
			<?php echo '<img src="'.JURI::root() .'administrator/components/com_zhyandexmap/assets/icons/'.str_replace("#", "%23", $item->icontype).'.png" alt="" />'; ?>
		</td>
		<td align="center">
			<?php 
				echo JHtml::_('jgrid.published', $item->published, $i, 'mapmarkers.', $canChange, 'cb', $item->publish_up, $item->publish_down); 
				//echo '<img src="'.JURI::root() .'administrator/components/com_zhyandexmap/assets/utils/published'.$item->published.'.png" alt="" />'; 
			?>			
		</td>
		<td>
			<?php echo $item->markergroupname; ?>
		</td>
		<td>
			<?php echo $item->category; ?>
		</td>
		<td>
			<?php echo $item->fullusername; ?>
		</td>
	</tr>
<?php endforeach; ?>

