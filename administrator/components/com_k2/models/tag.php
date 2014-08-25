<?php
/**
 * @version		$Id: tag.php 1175 2011-10-17 11:53:58Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

class K2ModelTag extends JModel
{

	function getData() {

		$cid = JRequest::getVar('cid');
		$row = & JTable::getInstance('K2Tag', 'Table');
		$row->load($cid);
		return $row;
	}

	function save() {

		$mainframe = &JFactory::getApplication();
		$row = & JTable::getInstance('K2Tag', 'Table');

		if (!$row->bind(JRequest::get('post'))) {
			$mainframe->redirect('index.php?option=com_k2&view=tags', $row->getError(), 'error');
		}

		if (!$row->check()) {
			$mainframe->redirect('index.php?option=com_k2&view=tag&cid='.$row->id, $row->getError(), 'error');
		}

		if (!$row->store()) {
			$mainframe->redirect('index.php?option=com_k2&view=tags', $row->getError(), 'error');
		}

		$cache = & JFactory::getCache('com_k2');
		$cache->clean();

		switch(JRequest::getCmd('task')) {
			case 'apply':
			$msg = JText::_('K2_CHANGES_TO_TAG_SAVED');
			$link = 'index.php?option=com_k2&view=tag&cid='.$row->id;
			break;
			case 'save':
			default:
			$msg = JText::_('K2_TAG_SAVED');
			$link = 'index.php?option=com_k2&view=tags';
			break;
		}
		$mainframe->redirect($link, $msg);
	}

	function addTag(){

		$mainframe = &JFactory::getApplication();

		$user = &JFactory::getUser();
		$params = &JComponentHelper::getParams('com_k2');
		if($user->gid<24 && $params->get('lockTags'))
			JError::raiseError(403, JText::_('K2_ALERTNOTAUTH'));

		$tag=JRequest::getString('tag');
		$tag = str_replace('-','',$tag);
		$tag = str_replace('.','',$tag);

		$response = new JObject;
		$response->set('name',$tag);


		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'JSON.php');
		$json=new Services_JSON;

		if (empty($tag)){
			$response->set('msg',JText::_('K2_YOU_NEED_TO_ENTER_A_TAG', true));
			echo $json->encode($response);
			$mainframe->close();
		}

		$db = & JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__k2_tags WHERE name=".$db->Quote($tag);
		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result>0){
			$response->set('msg',JText::_('K2_TAG_ALREADY_EXISTS', true));
			echo $json->encode($response);
			$mainframe->close();
		}

		$row = & JTable::getInstance('K2Tag', 'Table');
		$row->name=$tag;
		$row->published=1;
		$row->store();

		$cache = & JFactory::getCache('com_k2');
		$cache->clean();

		$response->set('id', $row->id);
		$response->set('status','success');
		$response->set('msg', JText::_('K2_TAG_ADDED_TO_AVAILABLE_TAGS_LIST', true));
		echo $json->encode($response);

		$mainframe->close();

	}

	function tags(){

		$mainframe = &JFactory::getApplication();
		$db = & JFactory::getDBO();
 		$word = JRequest::getString('q', null);
		$word = $db->Quote($db->getEscaped($word, true).'%', false);
		$query = "SELECT name FROM #__k2_tags WHERE name LIKE ".$word;
		$db->setQuery($query);
		$result = $db->loadResultArray();
		require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'JSON.php');
		$json=new Services_JSON;
		echo $json->encode($result);
		$mainframe->close();


	}

}
