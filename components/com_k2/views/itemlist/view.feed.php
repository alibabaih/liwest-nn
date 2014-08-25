<?php
/**
 * @version		$Id: view.feed.php 1371 2011-11-28 18:40:29Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class K2ViewItemlist extends JView {

	function display($tpl = null) {

		$mainframe = &JFactory::getApplication();
		$params = &K2HelperUtilities::getParams('com_k2');
		$document = &JFactory::getDocument();
		$model = &$this->getModel('itemlist');
		$limitstart = JRequest::getInt('limitstart');

		$moduleID = JRequest::getInt('moduleID');
		if ($moduleID) {

			$result = $model->getModuleItems($moduleID);
			$items = $result->items;
			$title = $result->title;

		}
		else {

			//Get data depending on task
			$task = JRequest::getCmd('task');
			switch ($task) {

				case 'category':
					//Get category
					$id = JRequest::getInt('id');
					JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
					$category = &JTable::getInstance('K2Category', 'Table');
					$category->load($id);
					
					// State check
					if (!$category->published || $category->trash) {
						JError::raiseError(404, JText::_('K2_CATEGORY_NOT_FOUND'));
					}

					//Access check
					$user = &JFactory::getUser();
					if(K2_JVERSION=='16'){
						if (!in_array($category->access, $user->authorisedLevels())) {
							JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
						}
						$languageFilter = $mainframe->getLanguageFilter();
						$languageTag = JFactory::getLanguage()->getTag();
						if($languageFilter && $category->language!=$languageTag && $category->language!='*') {
							return;
						}
					}
					else {
						if ($category->access > $user->get('aid', 0)) {
							JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));
						}
					}


					//Merge params
					$cparams = new JParameter($category->params);
					if ($cparams->get('inheritFrom')) {
						$masterCategory = &JTable::getInstance('K2Category', 'Table');
						$masterCategory->load($cparams->get('inheritFrom'));
						$cparams = new JParameter($masterCategory->params);
					}
					$params->merge($cparams);

					//Category link
					$category->link = urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($category->id.':'.urlencode($category->alias))));

					//Set featured flag
					JRequest::setVar('featured', $params->get('catFeaturedItems'));

					//Set title
					$title = $category->name;
					
					// Set ordering
					if($params->get('singleCatOrdering')) {
						$ordering = $params->get('singleCatOrdering');
					}
					else {
						$ordering = $params->get('catOrdering');
					}
					
					break;

				case 'user':
					//Get user
					$id = JRequest::getInt('id');
					$userObject = &JFactory::getUser($id);

					//Check user status
					if ($userObject->block) {
						JError::raiseError(404, JText::_('K2_USER_NOT_FOUND'));
					}

					//Set title
					$title = $userObject->name;
					
					// Set ordering
					$ordering = $params->get('userOrdering');

					break;

				case 'tag':
					//set title
					$title = JText::_('K2_DISPLAYING_ITEMS_BY_TAG').' '.JRequest::getVar('tag');
					if(JRequest::getCmd('type')!='atom'){
						$title = JFilterOutput::ampReplace($title);
					}
					// Set ordering
					$ordering = $params->get('tagOrdering');
					break;

				case 'search':
					//Set title
					$title = JText::_('K2_SEARCH_RESULTS_FOR').' '.JRequest::getVar('searchword');
					break;

				case 'date':
					// Set title
					if (JRequest::getInt('day')) {
						$date = strtotime(JRequest::getInt('year').'-'.JRequest::getInt('month').'-'.JRequest::getInt('day'));
						$dateFormat = (K2_JVERSION == '15')? '%A, %d %B %Y': 'l, d F Y';
						$title = JText::_('K2_ITEMS_FILTERED_BY_DATE').' '.JHTML::_('date', $date, $dateFormat);
					} else {
						$date = strtotime(JRequest::getInt('year').'-'.JRequest::getInt('month'));
						$dateFormat = (K2_JVERSION == '15')? '%B %Y': 'F Y';
						$title = JText::_('K2_ITEMS_FILTERED_BY_DATE').' '.JHTML::_('date', $date, $dateFormat);
					}
					// Set ordering
					$ordering = 'rdate';
					break;

				default:

					//Set featured flag
					JRequest::setVar('featured', $params->get('catFeaturedItems'));

					//Set title
					$title = $params->get('page_title');
					
					// Set ordering
					$ordering = $params->get('catOrdering');

					break;

			}

			//Get items
			if(!isset($ordering)) {
				$items = $model->getData();
			}
			else {
				$items = $model->getData($ordering);
			}


		}

		//Prepare feed items
		$model = &$this->getModel('item');
		foreach ($items as $item) {

			$item = $model->prepareFeedItem($item);
			$item->title = $this->escape($item->title);
			$item->title = html_entity_decode($item->title);
			$feedItem = new JFeedItem();
			$feedItem->title = $item->title;
			$feedItem->link = $item->link;
			$feedItem->description = $item->description;
			$feedItem->date = $item->created;
			$feedItem->category = $item->category->name;
			$feedItem->author = $item->author->name;
			if($params->get('feedBogusEmail')) {
				$feedItem->authorEmail = $params->get('feedBogusEmail');
			}
			else {
				if($mainframe->getCfg('feed_email') == 'author') {
					$feedItem->authorEmail = $item->author->email;
				}
				else {
					$feedItem->authorEmail = $mainframe->getCfg('mailfrom');
				}
			}

			//Add item
			$document->addItem($feedItem);
		}

		//Set title
		$document = &JFactory::getDocument();
		$menus = &JSite::getMenu();
		$menu = $menus->getActive();
		if (is_object($menu)) {
			$menu_params = new JParameter($menu->params);
			if (!$menu_params->get('page_title'))
			$params->set('page_title', $title);
		} else {
			$params->set('page_title', $title);
		}
		if(K2_JVERSION == '16') {
			if ($mainframe->getCfg('sitename_pagetitles', 0) == 1) {
				$title = JText::sprintf('JPAGETITLE', $mainframe->getCfg('sitename'), $params->get('page_title'));
				$params->set('page_title', $title);
			}
			elseif ($mainframe->getCfg('sitename_pagetitles', 0) == 2) {
				$title = JText::sprintf('JPAGETITLE', $params->get('page_title'), $mainframe->getCfg('sitename'));
				$params->set('page_title', $title);
			}
		}
		$document->setTitle($params->get('page_title'));

	}

}
