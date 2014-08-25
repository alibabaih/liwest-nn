<?php
/**
 * @version		$Id: view.html.php 1112 2011-10-11 14:34:53Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );

class K2ViewItems extends JView
{
	function display($tpl = null) {

		jimport('joomla.filesystem.file');
		$mainframe = &JFactory::getApplication();
		$user = & JFactory::getUser();
		$option=JRequest::getCmd('option');
		$view=JRequest::getCmd('view');
		$limit = $mainframe->getUserStateFromRequest ( 'global.list.limit', 'limit', $mainframe->getCfg ( 'list_limit' ), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest ( $option.$view.'.limitstart', 'limitstart', 0, 'int' );
		$filter_order = $mainframe->getUserStateFromRequest($option.$view.'filter_order','filter_order','i.id','cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest($option.$view.'filter_order_Dir','filter_order_Dir','DESC','word' );
		$filter_trash = $mainframe->getUserStateFromRequest($option.$view.'filter_trash', 'filter_trash', 0, 'int');
		$filter_featured = $mainframe->getUserStateFromRequest($option.$view.'filter_featured', 'filter_featured', -1, 'int');
		$filter_category = $mainframe->getUserStateFromRequest($option.$view.'filter_category', 'filter_category',0, 'int');
		$filter_author = $mainframe->getUserStateFromRequest($option.$view.'filter_author', 'filter_author',0, 'int');
		$filter_state=$mainframe->getUserStateFromRequest( $option.$view.'filter_state','filter_state',-1,'int' );
		$search = $mainframe->getUserStateFromRequest($option.$view.'search','search','','string');
		$search = JString::strtolower ( $search );
		$tag=$mainframe->getUserStateFromRequest( $option.$view.'tag','tag',0,'int' );
		$language = $mainframe->getUserStateFromRequest($option.$view.'language', 'language', '', 'string');
		$params = &JComponentHelper::getParams('com_k2');

		$model = &$this->getModel();
		$items = $model->getData();
		$this->assignRef('rows',$items);

		$lists = array ();
		$lists ['search'] = $search;

		if (!$filter_order) {
			$filter_order = 'category';
		}
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		$filter_trash_options[] = JHTML::_('select.option', 0, JText::_('K2_CURRENT'));
		$filter_trash_options[] = JHTML::_('select.option', 1, JText::_('K2_TRASHED'));
		$lists['trash'] = JHTML::_('select.genericlist', $filter_trash_options, 'filter_trash', '', 'value', 'text', $filter_trash);

		require_once(JPATH_COMPONENT.DS.'models'.DS.'categories.php');
		$categoriesModel= new K2ModelCategories;
		$categories_option[]=JHTML::_('select.option', 0, JText::_('K2_SELECT_CATEGORY'));
		$categories = $categoriesModel->categoriesTree(NULL, false, false);
		$categories_options=@array_merge($categories_option, $categories);
		$lists['categories'] = JHTML::_('select.genericlist', $categories_options, 'filter_category', '', 'value', 'text', $filter_category);

		$authors = $model->getItemsAuthors();
		$options = array();
		$options[] = JHTML::_('select.option', 0, '- '. JText::_('K2_NO_USER') .' -');
		foreach($authors as $author){
		    $name = $author->name;
		    if($author->block){
		        $name.= ' ['.JText::_('K2_USER_DISABLED').']';
		    }
		    $options[] = JHTML::_('select.option', $author->id,  $name);
		}
		$lists['authors'] = JHTML::_('select.genericlist', $options, 'filter_author', '', 'value', 'text', $filter_author);

		$filter_state_options[] = JHTML::_('select.option', -1, JText::_('K2_SELECT_PUBLISHING_STATE'));
		$filter_state_options[] = JHTML::_('select.option', 1, JText::_('K2_PUBLISHED'));
		$filter_state_options[] = JHTML::_('select.option', 0, JText::_('K2_UNPUBLISHED'));
		$lists['state'] = JHTML::_('select.genericlist', $filter_state_options, 'filter_state', '', 'value', 'text', $filter_state);

		$filter_featured_options[] = JHTML::_('select.option', -1, JText::_('K2_SELECT_FEATURED_STATE'));
		$filter_featured_options[] = JHTML::_('select.option', 1, JText::_('K2_FEATURED'));
		$filter_featured_options[] = JHTML::_('select.option', 0, JText::_('K2_NOT_FEATURED'));
		$lists['featured'] = JHTML::_('select.genericlist', $filter_featured_options, 'filter_featured', '', 'value', 'text', $filter_featured);

		if($params->get('showTagFilter')){
		    require_once(JPATH_COMPONENT.DS.'models'.DS.'tags.php');
		    $tagsModel= new K2ModelTags;
		    $options = $tagsModel->getFilter();
		    $option = new JObject();
		    $option->id = 0;
		    $option->name = JText::_('K2_SELECT_TAG');
		    array_unshift($options, $option);
		    $lists['tag'] = JHTML::_('select.genericlist', $options, 'tag', '', 'id', 'name', $tag);
		}

		if(version_compare( JVERSION, '1.6.0', 'ge' )) {
			$languages = JHTML::_('contentlanguage.existing', true, true);
			array_unshift($languages, JHTML::_('select.option', '', JText::_('K2_SELECT_LANGUAGE')));
			$lists['language'] = JHTML::_('select.genericlist', $languages, 'language', '', 'value', 'text', $language);
		}

		$this->assignRef('lists',$lists);

		$total=$model->getTotal();
		jimport ( 'joomla.html.pagination' );

		$pageNav = new JPagination ( $total, $limitstart, $limit );
		$this->assignRef('page',$pageNav);

		JToolBarHelper::title( JText::_('K2_ITEMS'), 'k2.png' );
		if ($filter_trash == 1) {
			JToolBarHelper::custom('restore','restore.png','restore_f2.png','K2_RESTORE', true);
			JToolBarHelper::deleteList('K2_ARE_YOU_SURE_YOU_WANT_TO_DELETE_SELECTED_ITEMS', 'remove', 'K2_DELETE');
		}
		else{

			$params = &JComponentHelper::getParams('com_k2');
			$toolbar=& JToolBar::getInstance('toolbar');
			
			JToolBarHelper::customX( 'featured', 'default.png', 'default_f2.png', 'K2_TOGGLE_FEATURED_STATE' );
			JToolBarHelper::publishList();
			JToolBarHelper::unpublishList();
			JToolBarHelper::customX( 'move', 'move.png', 'move_f2.png', 'K2_MOVE');
			JToolBarHelper::customX( 'copy', 'copy.png', 'copy_f2.png', 'K2_COPY' );
			JToolBarHelper::editList();
			JToolBarHelper::addNew();
			JToolBarHelper::trash('trash');

		}

		$toolbar=& JToolBar::getInstance('toolbar');
		if(K2_JVERSION == '16'){
			JToolBarHelper::preferences('com_k2', 550, 875, 'K2_PARAMETERS');
		}
		else {
			$toolbar->appendButton('Popup', 'config', 'K2_PARAMETERS', 'index.php?option=com_k2&view=settings');
		}
		
		// Import Joomla! content button
		if ($user->gid > 23 && !$params->get('hideImportButton')){
			$buttonUrl = JURI::base().'index.php?option=com_k2&amp;view=items&amp;task=import';
			$buttonText = JText::_('K2_IMPORT_JOOMLA_CONTENT');
			$button	= '<a id="K2ImportContentButton" href="'.$buttonUrl.'"><span class="icon-32-archive" title="'.$buttonText.'"></span>'.$buttonText.'</a>';
			$toolbar->appendButton('Custom', $button);
		}

		$this->loadHelper('html');
		K2HelperHTML::subMenu();

		$template = $mainframe->getTemplate();
		$this->assignRef('template', $template);
		$this->assignRef('filter_featured',$filter_featured);
		$this->assignRef('filter_trash',$filter_trash);
		$this->assignRef('user', $user);
		if(K2_JVERSION == '16') {
			$dateFormat = JText::_('K2_J16_DATE_FORMAT');
		}
		else {
			$dateFormat = JText::_('K2_DATE_FORMAT');
		}
		$this->assignRef('dateFormat', $dateFormat);

		$db = &JFactory::getDBO();
		$nullDate = $db->getNullDate();
		$this->assignRef('nullDate', $nullDate);

		$ordering = ( ($this->lists['order'] == 'i.ordering' || $this->lists['order'] == 'category' || $this->filter_featured) && (!$this->filter_trash));
		$this->assignRef('ordering', $ordering);

		parent::display($tpl);
	}

	function move(){

		$mainframe = &JFactory::getApplication();
		JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
		$cid = JRequest::getVar('cid');

		foreach ($cid as $id) {
			$row = & JTable::getInstance('K2Item', 'Table');
			$row->load($id);
			$rows[]=$row;
		}

		require_once(JPATH_COMPONENT.DS.'models'.DS.'categories.php');
		$categoriesModel= new K2ModelCategories;
		$categories = $categoriesModel->categoriesTree();
		$lists['categories'] = JHTML::_('select.genericlist', $categories, 'category', 'class="inputbox" size="8"', 'value', 'text');

		$this->assignRef('rows',$rows);
		$this->assignRef('lists',$lists);

		JToolBarHelper::title( JText::_('K2_MOVE_ITEMS'), 'k2.png' );

		JToolBarHelper::custom('saveMove','save.png','save_f2.png','K2_SAVE', false);
		JToolBarHelper::cancel();

		parent::display();
	}

}
