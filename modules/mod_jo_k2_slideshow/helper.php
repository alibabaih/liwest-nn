<?php
/*------------------------------------------------------------------------
# mod_jo_k2_slideshow - JO k2 slide show item for Joomla 1.6, 1.7, 2.5 Module
# -----------------------------------------------------------------------
# author: http://www.joomcore.com
# copyright Copyright (C) 2011 Joomcore.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomcore.com
# Technical Support:  Forum - http://www.joomcore.com/Support
-------------------------------------------------------------------------*/
defined('_JEXEC') or die ('Restricted access');
require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'utilities.php');
if(class_exists('resize') != true)	{
	require_once (JPATH_SITE.DS.'modules'.DS.'mod_jo_k2_slideshow'.DS.'lib'.DS.'resize-class.php');
}
class modJoK2SliderShow{
	function getList(&$params)
	{
		jimport('joomla.filesystem.file');
		$mainframe = &JFactory::getApplication();
		$limit = $params->get('itemCount', 5);
		$cid = $params->get('category_id', NULL);
		$ordering = $params->get('itemsOrdering','');
		$componentParams = &JComponentHelper::getParams('com_k2');
		$limitstart = JRequest::getInt('limitstart');

		$user = &JFactory::getUser();
		$aid = $user->get('aid');
		$db = &JFactory::getDBO();

		$jnow = &JFactory::getDate();
		$now = $jnow->toMySQL();
		$nullDate = $db->getNullDate();

		if($params->get('source')=='specific'){

			$value = $params->get('items');
			$current = array();
			if(is_string($value) && !empty($value))
			$current[]=$value;
			if(is_array($value))
			$current=$value;

			$items = array();
			foreach($current as $id){

				$query = "SELECT i.*, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams 
				FROM #__k2_items as i 
				LEFT JOIN #__k2_categories c ON c.id = i.catid 
				WHERE i.published = 1 ";
				if(K2_JVERSION=='16'){
					$query .= " AND i.access IN(".implode(',', $user->authorisedLevels()).") ";
				}
				else {
					$query .=" AND i.access<={$aid} ";
				}
				$query .= " AND i.trash = 0 AND c.published = 1 ";
				if(K2_JVERSION=='16'){
					$query .= " AND c.access IN(".implode(',', $user->authorisedLevels()).") ";
				}
				else {
					$query .=" AND c.access<={$aid} ";
				}
				$query .= " AND c.trash = 0 
				AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." ) 
				AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." ) 
				AND i.id={$id}";
				if(K2_JVERSION=='16'){
					if($mainframe->getLanguageFilter()) {
						$languageTag = JFactory::getLanguage()->getTag();
						$query .= " AND c.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').") AND i.language IN (".$db->Quote($languageTag).", ".$db->Quote('*').")";
					}
				}
				$db->setQuery($query);
				$item = $db->loadObject();
				if($item)
				$items[]=$item;

			}
		}else {
			$query = "SELECT i.*, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.params AS categoryparams";

			if ($ordering == 'best')
			$query .= ", (r.rating_sum/r.rating_count) AS rating";

			if ($ordering == 'comments')
			$query .= ", COUNT(comments.id) AS numOfComments";

			$query .= " FROM #__k2_items as i LEFT JOIN #__k2_categories c ON c.id = i.catid";

			if ($ordering == 'best')
			$query .= " LEFT JOIN #__k2_rating r ON r.itemID = i.id";

			if ($ordering == 'comments')
			$query .= " LEFT JOIN #__k2_comments comments ON comments.itemID = i.id";
			if(K2_JVERSION=='16'){
				$query .= " WHERE i.published = 1 AND i.access IN(".implode(',', $user->authorisedLevels()).") AND i.trash = 0 AND c.published = 1 AND c.access IN(".implode(',', $user->authorisedLevels()).")  AND c.trash = 0";
			}
			else {
				$query .= " WHERE i.published = 1 AND i.access <= {$aid} AND i.trash = 0 AND c.published = 1 AND c.access <= {$aid} AND c.trash = 0";
			}
			$query .= " AND ( i.publish_up = ".$db->Quote($nullDate)." OR i.publish_up <= ".$db->Quote($now)." )";
			$query .= " AND ( i.publish_down = ".$db->Quote($nullDate)." OR i.publish_down >= ".$db->Quote($now)." )";


			if ($params->get('catfilter')) {
				if (!is_null($cid)) {
					if (is_array($cid)) {
						if ($params->get('getChildren')) {
							require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php');
							$categories = K2ModelItemlist::getCategoryTree($cid);
							$sql = @implode(',', $categories);
							$query .= " AND i.catid IN ({$sql})";

						} else {
							JArrayHelper::toInteger($cid);
							$query .= " AND i.catid IN(".implode(',', $cid).")";
						}

					} else {
						if ($params->get('getChildren')) {
							require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'itemlist.php');
							$categories = K2ModelItemlist::getCategoryTree($cid);
							$sql = @implode(',', $categories);
							$query .= " AND i.catid IN ({$sql})";
						} else {
							$query .= " AND i.catid=".(int)$cid;
						}

					}
				}
			}

			if ($params->get('FeaturedItems') == '0')
			$query .= " AND i.featured != 1";

			if ($params->get('FeaturedItems') == '2')
			$query .= " AND i.featured = 1";
			
			if ($ordering == 'comments')
			$query .= " AND comments.published = 1";

			switch ($ordering) {

				case 'date':
					$orderby = 'i.created ASC';
					break;

				case 'rdate':
					$orderby = 'i.created DESC';
					break;

				case 'alpha':
					$orderby = 'i.title';
					break;

				case 'ralpha':
					$orderby = 'i.title DESC';
					break;

				case 'order':
					if ($params->get('FeaturedItems') == '2')
					$orderby = 'i.featured_ordering';
					else
					$orderby = 'i.ordering';
					break;

				case 'rorder':
					if ($params->get('FeaturedItems') == '2')
					$orderby = 'i.featured_ordering DESC';
					else
					$orderby = 'i.ordering DESC';
					break;

				case 'hits':
					if ($params->get('popularityRange')){
						$datenow = &JFactory::getDate();
						$date = $datenow->toMySQL();
						$query.=" AND i.created > DATE_SUB('{$date}',INTERVAL ".$params->get('popularityRange')." DAY) ";
					}
					$orderby = 'i.hits DESC';
					break;

				case 'rand':
					$orderby = 'RAND()';
					break;

				case 'best':
					$orderby = 'rating DESC';
					break;

				case 'comments':
					if ($params->get('popularityRange')){
						$datenow = &JFactory::getDate();
						$date = $datenow->toMySQL();
						$query.=" AND i.created > DATE_SUB('{$date}',INTERVAL ".$params->get('popularityRange')." DAY) ";
					}
					$query.=" GROUP BY i.id ";
					$orderby = 'numOfComments DESC';
					break;
					
				case 'modified':
					$orderby = 'i.modified DESC';
					break;

				default:
					$orderby = 'i.id DESC';
					break;
			}

			$query .= " ORDER BY ".$orderby;
			$db->setQuery($query, 0, $limit);
			$rows = $db->loadObjectList();
		
			//return $items;
		}

		require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'models'.DS.'item.php');
		$model = new K2ModelItem;

		if (count($rows)) {
		
			foreach ( $rows as $row )
			{
				$lists[$i] = new stdClass;
				
				$lists[$i]->title = JFilterOutput::ampReplace($row->title);
				
				$lists[$i]->link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($row->id.':'.urlencode($row->alias), $row->catid.':'.urlencode($row->categoryalias))));
				
				
				
				$lists[$i]->ct= strip_tags(preg_replace('/<img([^>]+)>/i',"",$row->introtext));//load introtext and remove images in introtext
				$lists[$i]->introtext = substr($lists[$i]->ct, 0, $params->get('numberdisplay'));// show count from introtext on text numberdisolay
				$lists[$i]->detailshort = substr($lists[$i]->ct, 0, $params->get('limitintrotext'));
				
				
				preg_match_all('/src="([^"]+)"/i', $row->introtext . $row->fulltext, $matches);// load images first on introtex or fulltext
				if(empty($matches[1][0])){
					$lists[$i]->im="";
				}else{
					$lists[$i]->im= $matches [1] [0];//show images
				}

				$name = 'thumb_'.$row->id;
				
				if(!empty($matches[1][0])){
					if($params->get('showthumbnails')==1){
						$lists[$i]->resizeObj = new resize($lists[$i]->im);
						$lists[$i]->resizeObj -> resizeImage($params->get('imagewidth'), $params->get('imageheight'), 'crop');
						$lists[$i]->resizeObj -> saveImage('modules/mod_jo_k2_slideshow/thumb_article/'.$name.'.gif', 100);
						$lists[$i]->images = '<img src="modules/mod_jo_k2_slideshow/thumb_article/'.$name.'.gif" alt="'.$row->title.'" title="'.$row->title.'" />';
					}else{
						$lists[$i]->images="";
					}		
				}else{
					if(($params->get('showthumbnailsdefault')==1) && ($params->get('showthumbnails')==1) && empty($lists[$i]->im)){// if images null then default images
						$lists[$i]->resizeObj = new resize($params->get('imagedefault'));
						$lists[$i]->resizeObj -> resizeImage($params->get('imagewidth'), $params->get('imageheight'), 'crop');
						$lists[$i]->resizeObj -> saveImage('modules/mod_jo_k2_slideshow/thumb_article/'.$name.'.gif', 100);
						$lists[$i]->images = '<img src="modules/mod_jo_k2_slideshow/thumb_article/'.$name.'.gif" alt="'.$row->title.'" title="'.$row->title.'" />';
					}		
				}
				
				if($params->get('showreadmore')==1){
					$lists[$i]->readmore = '<a class="jo-k2-readmore" href = ' .$lists[$i]->link. '>Readmore </a>' ; 
				}else{
					$lists[$i]->readmore ="";
				}
	
				$i++;
			}
		}
	return $lists;
	}
}
?>
