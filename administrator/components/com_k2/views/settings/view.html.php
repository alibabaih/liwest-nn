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

class K2ViewSettings extends JView {

	function display($tpl = null) {

		JHTML::_('behavior.tooltip');
		jimport('joomla.html.pane');
		$model = &$this->getModel();
		$params = &$model->getParams();
		$this->assignRef('params', $params);
		$pane = & JPane::getInstance('Tabs');
		$this->assignRef('pane', $pane);
		parent::display($tpl);
	}

}
