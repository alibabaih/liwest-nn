<?php
/**
 * @version		$Id: view.html.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class K2ViewTag extends JView
{

	function display($tpl = null) {
	
		JRequest::setVar('hidemainmenu', 1);
		$model = & $this->getModel();
		$tag = $model->getData();
		JFilterOutput::objectHTMLSafe( $tag );
		if(!$tag->id)
			$tag->published=1;
		$this->assignRef('row', $tag);
	
		$lists = array ();
		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $tag->published);
		$this->assignRef('lists', $lists);
		(JRequest::getInt('cid'))? $title = JText::_('K2_EDIT_TAG') : $title = JText::_('K2_ADD_TAG');
		JToolBarHelper::title($title, 'k2.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	
		parent::display($tpl);
	}

}
