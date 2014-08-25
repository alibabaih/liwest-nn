<?php
/**
 * @version		$Id: script.k2.php 1289 2011-10-27 17:44:40Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class Com_K2InstallerScript {
	function update($type) {

		// Database modifications [start]
		$db = &JFactory::getDBO();

		$fields = $db->getTableFields('#__k2_categories');
		if (!array_key_exists('language', $fields['#__k2_categories'])) {
			$query = "ALTER TABLE #__k2_categories ADD `language` CHAR(7) NOT NULL";
			$db->setQuery($query);
			$db->query();

			$query = "ALTER TABLE #__k2_categories ADD INDEX (`language`)";
			$db->setQuery($query);
			$db->query();
		}

		$fields = $db->getTableFields('#__k2_items');
		if (!array_key_exists('featured_ordering', $fields['#__k2_items'])) {
			$query = "ALTER TABLE #__k2_items ADD `featured_ordering` INT(11) NOT NULL default '0' AFTER `featured`";
			$db->setQuery($query);
			$db->query();
		}
		if (!array_key_exists('language', $fields['#__k2_items'])) {
			$query = "ALTER TABLE #__k2_items ADD `language` CHAR(7) NOT NULL";
			$db->setQuery($query);
			$db->query();

			$query = "ALTER TABLE #__k2_items ADD INDEX (`language`)";
			$db->setQuery($query);
			$db->query();
		}

		if($fields['#__k2_items']['video']!='text'){
			$query = "ALTER TABLE #__k2_items MODIFY `video` TEXT";
			$db->setQuery($query);
			$db->query();
		}

		if($fields['#__k2_items']['introtext']=='text'){
			$query = "ALTER TABLE #__k2_items MODIFY `introtext` MEDIUMTEXT";
			$db->setQuery($query);
			$db->query();
		}

		if($fields['#__k2_items']['fulltext']=='text'){
			$query = "ALTER TABLE #__k2_items MODIFY `fulltext` MEDIUMTEXT";
			$db->setQuery($query);
			$db->query();
		}

		$query = "SHOW INDEX FROM #__k2_items";
		$db->setQuery($query);
		$indexes = $db->loadObjectList();
		$indexExists = false;
		foreach ($indexes as $index){
			if ($index->Key_name=='search')
			$indexExists = true;
		}

		if (!$indexExists){
			$query = "ALTER TABLE #__k2_items ADD FULLTEXT `search` (`title`,`introtext`,`fulltext`,`extra_fields_search`,`image_caption`,`image_credits`,`video_caption`,`video_credits`,`metadesc`,`metakey`)";
			$db->setQuery($query);
			$db->query();

			$query = "ALTER TABLE #__k2_items ADD FULLTEXT (`title`)";
			$db->setQuery($query);
			$db->query();
		}

		$query = "SHOW INDEX FROM #__k2_tags";
		$db->setQuery($query);
		$indexes = $db->loadObjectList();
		$indexExists = false;
		foreach ($indexes as $index){
			if ($index->Key_name=='name')
			$indexExists = true;
		}

		if (!$indexExists){
			$query = "ALTER TABLE #__k2_tags ADD FULLTEXT (`name`)";
			$db->setQuery($query);
			$db->query();
		}

		$query = "SELECT COUNT(*) FROM #__k2_user_groups";
		$db->setQuery($query);
		$num = $db->loadResult();

		if ($num==0){
			$query = "INSERT INTO #__k2_user_groups (`id`, `name`, `permissions`) VALUES('', 'Registered', '{\"comment\":\"1\",\"frontEdit\":\"0\",\"add\":\"0\",\"editOwn\":\"0\",\"editAll\":\"0\",\"publish\":\"0\",\"inheritance\":0,\"categories\":\"all\"}')";
			$db->setQuery($query);
			$db->Query();

			$query = "INSERT INTO #__k2_user_groups (`id`, `name`, `permissions`) VALUES('', 'Site Owner', '{\"comment\":\"1\",\"frontEdit\":\"1\",\"add\":\"1\",\"editOwn\":\"1\",\"editAll\":\"1\",\"publish\":\"1\",\"inheritance\":1,\"categories\":\"all\"}')";
			$db->setQuery($query);
			$db->Query();

		}

		// Database modifications [end]

	}
}
