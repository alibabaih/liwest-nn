<?php
/**
 * @version		$Id: categories.php 1189 2011-10-17 14:31:02Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

class K2ModelCategories extends JModel
{

	function getData() {

		$mainframe = &JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$db = & JFactory::getDBO();
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.$view.'.limitstart', 'limitstart', 0, 'int');
		$search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$filter_order = $mainframe->getUserStateFromRequest($option.$view.'filter_order', 'filter_order', 'c.ordering', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.$view.'filter_order_Dir', 'filter_order_Dir', '', 'word');
		$filter_trash = $mainframe->getUserStateFromRequest($option.$view.'filter_trash', 'filter_trash', 0, 'int');
		$filter_state = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', -1, 'int');
		$language = $mainframe->getUserStateFromRequest($option.$view.'language', 'language', '', 'string');

		$query = "SELECT c.*, g.name AS groupname, exfg.name as extra_fields_group FROM #__k2_categories as c LEFT JOIN #__groups AS g ON g.id = c.access LEFT JOIN #__k2_extra_fields_groups AS exfg ON exfg.id = c.extraFieldsGroup WHERE c.id>0";

		if (!$filter_trash){
			$query .= " AND c.trash=0";
		}

		if ($search) {
			$query .= " AND LOWER( c.name ) LIKE ".$db->Quote('%'.$db->getEscaped($search, true).'%', false);
		}

		if ($filter_state > -1) {
			$query .= " AND c.published={$filter_state}";
		}
		if ($language) {
			$query .= " AND c.language = ".$db->Quote($language);
		}

		$query .= " ORDER BY {$filter_order} {$filter_order_Dir}";

		if(K2_JVERSION=='16'){
			$query = JString::str_ireplace('#__groups', '#__viewlevels', $query);
			$query = JString::str_ireplace('g.name AS groupname', 'g.title AS groupname', $query);
		}

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(K2_JVERSION=='16'){
			foreach($rows as $row){
				$row->parent_id = $row->parent;
				$row->title = $row->name;
			}
		}
		$categories = array();

		if ($search) {
			foreach ($rows as $row) {
				$row->treename = $row->name;
				$categories[]=$row;
			}

		}
		else {
			$categories = $this->indentRows($rows);
		}
		if (isset($categories)){
			$total = count($categories);
		}
		else {
			$total = 0;
		}
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $limitstart, $limit);
		$categories = @array_slice($categories, $pageNav->limitstart, $pageNav->limit);
		foreach($categories as $category) {
			$category->parameters = new JParameter($category->params);
			if($category->parameters->get('inheritFrom')) {
				$db->setQuery("SELECT name FROM #__k2_categories WHERE id = ".(int)$category->parameters->get('inheritFrom'));
				$category->inheritFrom = $db->loadResult();
			}
			else {
				$category->inheritFrom = '';
			}
		}
		return $categories;
	}

	function getTotal() {

		$mainframe = &JFactory::getApplication();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$db = & JFactory::getDBO();
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');
		$search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$filter_trash = $mainframe->getUserStateFromRequest($option.$view.'filter_trash', 'filter_trash', 0, 'int');
		$filter_state = $mainframe->getUserStateFromRequest($option.$view.'filter_state', 'filter_state', 1, 'int');
		$language = $mainframe->getUserStateFromRequest($option.$view.'language', 'language', '', 'string');
		
		$query = "SELECT COUNT(*) FROM #__k2_categories WHERE id>0";

		if (!$filter_trash){
			$query .= " AND trash=0";
		}

		if ($search) {
			$query .= " AND LOWER( name ) LIKE ".$db->Quote('%'.$db->getEscaped($search, true).'%', false);
		}

		if ($filter_state > -1) {
			$query .= " AND published={$filter_state}";
		}
		
		if ($language) {
			$query .= " AND language = ".$db->Quote($language);
		}

		$db->setQuery($query);
		$total = $db->loadResult();
		return $total;

	}

	function indentRows( & $rows) {
		$children = array ();
		if(count($rows)){
			foreach ($rows as $v) {
				$pt = $v->parent;
				$list = @$children[$pt]?$children[$pt]: array ();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
				
		$categories = JHTML::_('menu.treerecurse', 0, '', array (), $children);
		return $categories;
	}

	function publish() {

		$mainframe = &JFactory::getApplication();
		$cid = JRequest::getVar('cid');
		$row = & JTable::getInstance('K2Category', 'Table');
		foreach ($cid as $id) {
			$row->load($id);
			$row->publish($id, 1);
		}
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$mainframe->redirect('index.php?option=com_k2&view=categories');
	}

	function unpublish() {

		$mainframe = &JFactory::getApplication();
		$cid = JRequest::getVar('cid');
		$row = & JTable::getInstance('K2Category', 'Table');
		foreach ($cid as $id) {
			$row->load($id);
			$row->publish($id, 0);
		}
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$mainframe->redirect('index.php?option=com_k2&view=categories');
	}

	function saveorder() {

		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$cid = JRequest::getVar('cid', array (0), 'post', 'array');
		$total = count($cid);
		$order = JRequest::getVar('order', array (0), 'post', 'array');
		JArrayHelper::toInteger($order, array (0));
		$row = & JTable::getInstance('K2Category', 'Table');
		$groupings = array ();
		for ($i = 0; $i < $total; $i++) {
			$row->load(( int )$cid[$i]);
			$groupings[] = $row->parent;
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError(500, $db->getErrorMsg());
				}
			}
		}
		$params = &JComponentHelper::getParams('com_k2');
		if(!$params->get('disableCompactOrdering')){
			$groupings = array_unique($groupings);
			foreach ($groupings as $group) {
				$row->reorder('parent = '.( int )$group.' AND trash=0');
			}
		}
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$msg = JText::_('K2_NEW_ORDERING_SAVED');
		$mainframe->redirect('index.php?option=com_k2&view=categories', $msg);
	}

	function orderup() {

		$mainframe = &JFactory::getApplication();
		$cid = JRequest::getVar('cid');
		$row = & JTable::getInstance('K2Category', 'Table');
		$row->load($cid[0]);
		$row->move(-1, 'parent = '.$row->parent.' AND trash=0');
		$params = &JComponentHelper::getParams('com_k2');
		if(!$params->get('disableCompactOrdering'))
		$row->reorder('parent = '.$row->parent.' AND trash=0');
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$msg = JText::_('K2_NEW_ORDERING_SAVED');
		$mainframe->redirect('index.php?option=com_k2&view=categories', $msg);
	}

	function orderdown() {

		$mainframe = &JFactory::getApplication();
		$cid = JRequest::getVar('cid');
		$row = & JTable::getInstance('K2Category', 'Table');
		$row->load($cid[0]);
		$row->move(1, 'parent = '.$row->parent.' AND trash=0');
		$params = &JComponentHelper::getParams('com_k2');
		if(!$params->get('disableCompactOrdering'))
		$row->reorder('parent = '.$row->parent.' AND trash=0');
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$msg = JText::_('K2_NEW_ORDERING_SAVED');
		$mainframe->redirect('index.php?option=com_k2&view=categories', $msg);
	}

	function accessregistered() {

		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$row = & JTable::getInstance('K2Category', 'Table');
		$cid = JRequest::getVar('cid');
		$row->load($cid[0]);
		$row->access = 1;
		if (!$row->check()) {
			return $row->getError();
		}
		if (!$row->store()) {
			return $row->getError();
		}
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$msg = JText::_('K2_NEW_ACCESS_SETTING_SAVED');
		$mainframe->redirect('index.php?option=com_k2&view=categories', $msg);
	}

	function accessspecial() {

		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$row = & JTable::getInstance('K2Category', 'Table');
		$cid = JRequest::getVar('cid');
		$row->load($cid[0]);
		$row->access = 2;
		if (!$row->check()) {
			return $row->getError();
		}
		if (!$row->store()) {
			return $row->getError();
		}
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$msg = JText::_('K2_NEW_ACCESS_SETTING_SAVED');
		$mainframe->redirect('index.php?option=com_k2&view=categories', $msg);
	}

	function accesspublic() {

		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$row = & JTable::getInstance('K2Category', 'Table');
		$cid = JRequest::getVar('cid');
		$row->load($cid[0]);
		$row->access = 0;
		if (!$row->check()) {
			return $row->getError();
		}
		if (!$row->store()) {
			return $row->getError();
		}
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$msg = JText::_('K2_NEW_ACCESS_SETTING_SAVED');
		$mainframe->redirect('index.php?option=com_k2&view=categories', $msg);
	}

	function trash() {

		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		$row = & JTable::getInstance('K2Category', 'Table');

		JArrayHelper::toInteger($cid);

		require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php');
		$model = new K2ModelItemlist;
		$categories = K2ModelItemlist::getCategoryTree($cid);
		$sql = @implode(',',$categories);
		$db = & JFactory::getDBO();
		$query = "UPDATE #__k2_categories SET trash=1  WHERE id IN ({$sql})";
		$db->setQuery($query);
		$db->query();
		$query = "UPDATE #__k2_items SET trash=1  WHERE catid IN ({$sql})";
		$db->setQuery($query);
		$db->query();

		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		$mainframe->redirect('index.php?option=com_k2&view=categories', JText::_('K2_CATEGORIES_MOVED_TO_TRASH'));

	}

	function restore() {

		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		$row = & JTable::getInstance('K2Category', 'Table');
		$warning = false;
		foreach ($cid as $id) {
			$row->load($id);
			if ((int)$row->parent==0){
				$row->trash = 0;
				$row->store();
			}
			else {
				$query = "SELECT COUNT(*) FROM #__k2_categories WHERE id={$row->parent} AND trash = 0";
				$db->setQuery($query);
				$result=$db->loadResult();
				if ($result){
					$row->trash = 0;
					$row->store();
				}
				else {
					$warning=true;
				}

			}


		}
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();
		if($warning)
		$mainframe->enqueueMessage(JText::_('K2_SOME_OF_THE_CATEGORIES_HAVE_NOT_BEEN_RESTORED_BECAUSE_THEIR_PARENT_CATEGORY_IS_IN_TRASH'), 'notice');
		$mainframe->redirect('index.php?option=com_k2&view=categories', JText::_('K2_CATEGORIES_RESTORED'));

	}

	function remove() {

		$mainframe = &JFactory::getApplication();
		jimport('joomla.filesystem.file');
		$db = & JFactory::getDBO();
		$cid = JRequest::getVar('cid');
		JArrayHelper::toInteger($cid);
		$row = & JTable::getInstance('K2Category', 'Table');

		$warningItems = false;
		$warningChildren = false;
		$cid = array_reverse($cid);
		for ($i = 0; $i < sizeof($cid); $i++) {
			$row->load($cid[$i]);

			$query = "SELECT COUNT(*) FROM #__k2_items WHERE catid={$cid[$i]}";
			$db->setQuery($query);
			$num = $db->loadResult();

			if ($num > 0 ){
				$warningItems = true;
			}

			$query = "SELECT COUNT(*) FROM #__k2_categories WHERE parent={$cid[$i]}";
			$db->setQuery($query);
			$children = $db->loadResult();

			if ($children > 0) {
				$warningChildren = true;
			}

			if ($children==0 && $num==0){

				if ($row->image) {
					JFile::delete(JPATH_ROOT.DS.'media'.DS.'k2'.DS.'categories'.DS.$row->image);
				}
				$row->delete($cid[$i]);

			}
		}
		$cache = & JFactory::getCache('com_k2');
		$cache->clean();

		if ($warningItems){
			$mainframe->enqueueMessage(JText::_('K2_SOME_OF_THE_CATEGORIES_HAVE_NOT_BEEN_DELETED_BECAUSE_THEY_HAVE_ITEMS'), 'notice');
		}
		if ($warningChildren){
			$mainframe->enqueueMessage(JText::_('K2_SOME_OF_THE_CATEGORIES_HAVE_NOT_BEEN_DELETED_BECAUSE_THEY_HAVE_CHILD_CATEGORIES'), 'notice');
		}

		$mainframe->redirect('index.php?option=com_k2&view=categories', JText::_('K2_DELETE_COMPLETED'));
	}

	function categoriesTree(  $row = NULL, $hideTrashed=false, $hideUnpublished=true) {

		$db = & JFactory::getDBO();
		if (isset($row->id)) {
			$idCheck = ' AND id != '.( int )$row->id;
		}
		else {
			$idCheck = null;
		}
		if (!isset($row->parent)) {
			$row->parent = 0;
		}
		$query = "SELECT m.* FROM #__k2_categories m WHERE id > 0 {$idCheck}";

		if ($hideUnpublished){
			$query.=" AND published=1 ";
		}

		if ($hideTrashed){
			$query.=" AND trash=0 ";
		}

		$query.=" ORDER BY parent, ordering";
		$db->setQuery($query);
		$mitems = $db->loadObjectList();
		$children = array ();
		if ($mitems) {
			foreach ($mitems as $v) {
				if(K2_JVERSION=='16'){
					$v->title = $v->name;
					$v->parent_id = $v->parent;
				}
				$pt = $v->parent;
				$list = @$children[$pt]?$children[$pt]: array ();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array (), $children, 9999, 0, 0);
		$mitems = array ();
		foreach ($list as $item) {
			$item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
			
			if($item->trash) $item->treename .= ' [**'.JText::_('K2_TRASHED_CATEGORY').'**]';
			if(!$item->published) $item->treename .= ' [**'.JText::_('K2_UNPUBLISHED_CATEGORY').'**]';
			
			$mitems[] = JHTML::_('select.option', $item->id, $item->treename);
		}
		return $mitems;
	}

	function copy() {
		jimport('joomla.filesystem.file');
		$mainframe = &JFactory::getApplication();
		$cid = JRequest::getVar('cid');
		JArrayHelper::toInteger($cid);
		foreach ($cid as $id) {
			//Load source category
			$category = &JTable::getInstance('K2Category', 'Table');
			$category->load($id);

			//Save target category
			$row = &JTable::getInstance('K2Category', 'Table');
			$row = $category;
			$row->id = NULL;
			$row->name = JText::_('K2_COPY_OF').' '.$category->name;
			$row->published = 0;
			$row->store();
			//Target image
			if ($category->image && JFile::exists(JPATH_SITE.DS.'media'.DS.'k2'.DS.'categories'.DS.$category->image)){
				JFile::copy(JPATH_SITE.DS.'media'.DS.'k2'.DS.'categories'.DS.$category->image, JPATH_SITE.DS.'media'.DS.'k2'.DS.'categories'.DS.$row->id.'.jpg');
				$row->image = $row->id.'.jpg';
				$row->store();
			}
		}

		$mainframe->redirect('index.php?option=com_k2&view=categories', JText::_('K2_COPY_COMPLETED'));
	}

	function move() {

		$mainframe = &JFactory::getApplication();
		$cid = JRequest::getVar('cid');
		$catid = JRequest::getInt('category');
		$row = &JTable::getInstance('K2Category', 'Table');
		foreach ($cid as $id) {
			$row->load($id);
			$row->parent = $catid;
			$row->ordering = $row->getNextOrder('parent = '.$row->parent.' AND published = 1');
			$row->store();
		}
		$cache = &JFactory::getCache('com_k2');
		$cache->clean();
		$mainframe->redirect('index.php?option=com_k2&view=categories', JText::_('K2_MOVE_COMPLETED'));

	}

}
