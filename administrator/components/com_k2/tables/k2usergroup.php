<?php
/**
 * @version		$Id: k2usergroup.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableK2UserGroup extends JTable {

	var $id = null;
	var $name = null;
	var $permissions = null;

	function __construct( & $db) {
	
		parent::__construct('#__k2_user_groups', 'id', $db);
	}

	function check() {
	
		if (trim($this->name) == '') {
			$this->setError(JText::_('K2_GROUP_CANNOT_BE_EMPTY'));
			return false;
		}
		return true;
	}

	function bind($array, $ignore = '') {
		
		if (key_exists('params', $array) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			if(JRequest::getVar('categories')=='all' || JRequest::getVar('categories')=='none')
			$registry->setValue('categories',JRequest::getVar('categories'));
			$array['permissions'] = $registry->toString();
		}
		return parent::bind($array, $ignore);
	}

}
