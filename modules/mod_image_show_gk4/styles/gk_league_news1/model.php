<?php

/**
* GK Image Show - model file
* @package Joomla!
* @Copyright (C) 2009-2011 Gavick.com
* @ All rights reserved
* @ Joomla! is Free Software
* @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
* @ version $Revision: GK4 1.0 $
**/

// no direct access
defined('_JEXEC') or die;

// import com_content route helper
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class GKIS_gk_league_news1_Model {
	// configuration array
	private $config;
	// constructor
	function __construct($config) {
		// init the style config
		$this->config = $config;
	}
	// getData function
	function getData($ids) {
		// prepare an array
		$results = array();
		// prepare an query part
		$query_ids = implode(',', $ids);
		// generate the query
		$database = JFactory::getDBO();
		// SQL query for slides
		$query = '
		SELECT 
			`c`.`id` AS `id`,
			`c`.`catid` AS `cid`,
			`c`.`title` AS `title`,
			`c`.`created` AS `date`,
			`c`.`introtext` AS `introtext`
		FROM 
			#__content AS `c` 
		WHERE 
			`c`.`id` IN ('.$query_ids.')
		;';
		// running query
		$database->setQuery($query);
		// if results exists
		if( $datas = $database->loadObjectList() ) {
			// parsing data
			foreach($datas as $item) {
				// array with prepared image
			 	$results[$item->id] = array(
					'id' => $item->id,
					'cid' => $item->cid,
					'title' => stripslashes($item->title),
					'link' => JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->cid)),
					'date' => $item->date,
					'text' => $item->introtext
				);
			}
		}
		// return the results
		return $results;
	}
		
	function getDataK2($ids) {
    	//
    	require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
		// prepare an array
		$results = array();
		// prepare an query part
		$query_ids = implode(',', $ids);
		// generate the query
		$database = JFactory::getDBO();
		// SQL query for slides
		$query = '
		SELECT 
			`c`.`id` AS `id`,
			`c`.`catid` AS `cid`,
			`c`.`title` AS `title`,
			`c`.`introtext` AS `introtext`,
			`c`.alias AS `alias`,
			`cats`.alias AS `cat_alias`,
			`c`.`created` AS `date`
		FROM 
			#__k2_items AS `c` 
			LEFT JOIN 
					#__k2_categories AS `cats`
					ON cats.id = `c`.`id` 
		WHERE 
			`c`.`id` IN ('.$query_ids.')
		;';
		// running query
		$database->setQuery($query);
		// if results exists
		if( $datas = $database->loadObjectList() ) {
			// parsing data
			foreach($datas as $item) {
				// array with prepared image
			 	$results[$item->id] = array(
					'id' => $item->id,
					'cid' => $item->cid,
					'title' => stripslashes($item->title),
					'link' => urldecode(JRoute::_(K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias), $item->cid.':'.urlencode($item->cat_alias)))),
					'date' => $item->date,
					'text' => $item->introtext
				);
			}
		}
		// return the results
		return $results;
	}
}

/* eof */