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
<tr>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::_('COM_ZHYANDEXMAP_MAPPATH_HEADING_TITLE'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_ZHYANDEXMAP_MAPPATH_HEADING_MAPTITLE'); ?>
	</th>
	<th width="5">
		<?php echo JText::_('COM_ZHYANDEXMAP_MAPPATH_HEADING_PUBLISHED'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_ZHYANDEXMAP_MAPPATH_HEADING_CATEGORY'); ?>
	</th>
</tr>


