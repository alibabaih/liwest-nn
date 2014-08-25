<?php
/**
 * @version		$Id: cpanel.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

class K2ModelCpanel extends JModel
{

	function getLatestItems() {
	
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT i.*, v.name AS author FROM #__k2_items as i 
		LEFT JOIN #__k2_categories AS c ON c.id = i.catid 
		LEFT JOIN #__users AS v ON v.id = i.created_by 
		WHERE i.trash = 0  AND c.trash = 0
		ORDER BY i.created DESC";
		if(K2_JVERSION=='16'){
		    $query = JString::str_ireplace('#__groups', '#__viewlevels', $query);
		    $query = JString::str_ireplace('g.name', 'g.title', $query);
		}
		$db->setQuery($query, 0, 10);
		$rows = $db->loadObjectList();
		return $rows;
	}
	
	function getPopularItems() {
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT i.*, v.name AS author FROM #__k2_items as i 
		LEFT JOIN #__k2_categories AS c ON c.id = i.catid 
		LEFT JOIN #__users AS v ON v.id = i.created_by 
		WHERE i.trash = 0  AND c.trash = 0
		ORDER BY i.hits DESC";
		$db->setQuery($query, 0, 10);
		$rows = $db->loadObjectList();
		return $rows;
	}
	
	function getMostCommentedItems() {
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT i.*, v.name AS author, COUNT(comments.id) AS numOfComments FROM #__k2_items as i 
		LEFT JOIN #__k2_categories AS c ON c.id = i.catid 
		LEFT JOIN #__users AS v ON v.id = i.created_by 
		LEFT JOIN #__k2_comments comments ON comments.itemID = i.id
		WHERE i.trash = 0  AND c.trash = 0
		GROUP BY i.id
		ORDER BY numOfComments DESC";
		$db->setQuery($query, 0, 10);
		$rows = $db->loadObjectList();
		return $rows;
	}
	
	function getLatestComments() {
	
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT * FROM #__k2_comments ORDER BY commentDate DESC";
		$db->setQuery($query, 0, 10);
		$rows = $db->loadObjectList();
		return $rows;
	}
	
	function countItems(){
		
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_items";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	
	function countTrashedItems(){
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_items WHERE trash=1";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	
	function countFeaturedItems(){
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_items WHERE featured=1";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	
	function countComments(){
		
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_comments";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}

	function countCategories(){
		
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_categories";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	
	function countTrashedCategories(){
		
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_categories WHERE trash=1";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	
	function countUsers(){
		
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_users";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	
	
	function countUserGroups(){
		
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_user_groups";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}
	
	function countTags(){
		
		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_tags";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}

}
