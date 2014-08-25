<?php 
/**
 * @version		$Id: cpanel.php 578 2011-01-11 16:25:46Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks, a business unit of Nuevvo Webware Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class K2ControllerCpanel extends JController {

    function display() {
        JRequest::setVar('view', 'cpanel');
        parent::display();
    }

}
