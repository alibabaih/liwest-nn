<?php
/**
 * @version		$Id: view.html.php 785 2011-04-28 12:39:17Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks, a business unit of Nuevvo Webware Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class K2ViewCpanel extends JView
{

	function display($tpl = null) {
	  
		$params = &JComponentHelper::getParams('com_k2');
		$frontEditConflict = false;
		if (count(JPluginHelper::getPlugin('system','jfdatabase')) && JPluginHelper::isEnabled('system','jfdatabase') && $params->get('frontendEditing')){
			$frontEditConflict = true;
		}

		$this->assignRef('frontEditConflict',$frontEditConflict);

		$user = & JFactory::getUser();

		JToolBarHelper::title(JText::_('K2_DASHBOARD'), 'k2.png');
		$toolbar=& JToolBar::getInstance('toolbar');

		if(K2_JVERSION == '16'){
			JToolBarHelper::preferences('com_k2');
		}
		else {
			$toolbar->appendButton('Popup', 'config', 'Parameters', 'index.php?option=com_k2&view=settings');
		}

		$params = &JComponentHelper::getParams('com_k2');
		if ($user->gid > 23 && !$params->get('hideImportButton')){
			$buttonUrl = JURI::base().'index.php?option=com_k2&amp;view=items&amp;task=import';
			$buttonText = JText::_('K2_IMPORT_JOOMLA_CONTENT');
			$button	= '<a id="K2ImportContentButton" href="'.$buttonUrl.'"><span class="icon-32-archive" title="'.$buttonText.'"></span>'.$buttonText.'</a>';
			$toolbar->prependButton('Custom', $button);
		}

		JSubMenuHelper::addEntry(JText::_('K2_DASHBOARD'), 'index.php?option=com_k2', true);
		JSubMenuHelper::addEntry(JText::_('K2_ITEMS'), 'index.php?option=com_k2&view=items');
		JSubMenuHelper::addEntry(JText::_('K2_CATEGORIES'), 'index.php?option=com_k2&view=categories');
		if( !$params->get('lockTags') || $user->gid>23){
			JSubMenuHelper::addEntry(JText::_('K2_TAGS'), 'index.php?option=com_k2&view=tags');
		}
		JSubMenuHelper::addEntry(JText::_('K2_COMMENTS'), 'index.php?option=com_k2&view=comments');

		if ($user->gid > 23) {
			JSubMenuHelper::addEntry(JText::_('K2_USERS'), 'index.php?option=com_k2&view=users');
			JSubMenuHelper::addEntry(JText::_('K2_USER_GROUPS'), 'index.php?option=com_k2&view=userGroups');
			JSubMenuHelper::addEntry(JText::_('K2_EXTRA_FIELDS'), 'index.php?option=com_k2&view=extraFields');
			JSubMenuHelper::addEntry(JText::_('K2_EXTRA_FIELD_GROUPS'), 'index.php?option=com_k2&view=extraFieldsGroups');
			JSubMenuHelper::addEntry(JText::_('K2_INFORMATION'), 'index.php?option=com_k2&view=info');
		}

		$this->assignRef('user', $user);
		$this->assignRef('params',$params);
		parent::display($tpl);
	}

}
