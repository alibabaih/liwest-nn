<?php
/**
 * @version		$Id: latest.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class K2ControllerLatest extends JController {
	function display() {
		$view = &$this->getView('latest', 'html');
		$model=&$this->getModel('itemlist');
		$view->setModel($model);
		$itemModel=&$this->getModel('item');
		$view->setModel($itemModel);
	    $user = &JFactory::getUser();
        if ($user->guest){
            $cache = true;
        }
        else {
            $cache = false;
        }
		parent::display($cache);
	}

}
