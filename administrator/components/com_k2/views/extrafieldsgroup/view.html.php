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

class K2ViewExtraFieldsGroup extends JView
{

	function display($tpl = null) {
	
		JRequest::setVar('hidemainmenu', 1);
		$model = & $this->getModel();
		$extraFieldsGroup = $model->getExtraFieldsGroup();
		JFilterOutput::objectHTMLSafe( $extraFieldsGroup );
		$this->assignRef('row', $extraFieldsGroup);
		(JRequest::getInt('cid'))? $title = JText::_('K2_EDIT_EXTRA_FIELD_GROUP') : $title = JText::_('K2_ADD_EXTRA_FIELD_GROUP');
		JToolBarHelper::title($title, 'k2.png');
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	
		parent::display($tpl);
	}

}
