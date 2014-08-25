<?php
/**
 * jSecure Authentication components for Joomla!
 * jSecure Authentication extention prevents access to administration (back end)
 * login page without appropriate access key.
 *
 * @author      $Author: Ajay Lulia $
 * @copyright   Joomla Service Provider - 2011
 * @package     jSecure2.1.10
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: view.html.php  $
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class jsecureViewLog extends JView {
	
	function display($tpl=null){
		
		$app    = &JFactory::getApplication();
		
		$basepath   = JPATH_ADMINISTRATOR .'/components/com_jsecure';
		$configFile	= $basepath.'/params.php';
		require_once($configFile);
		$JSecureConfig = new JSecureConfig();

		$model = $this->getModel('jsecurelog');

		//delete log 
		if($JSecureConfig->delete_log != 0)
			$deleteLog = $model->deleteLog($JSecureConfig->delete_log);

		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		$data = $model->getData();		
		$total = $model->getTotalList();

		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		$this->assignref('data',$data);
		$this->assignref('pagination',$pagination);
		parent::display($tpl);
	}
	
	function ipinfo($tpl=null){
		$ip = JRequest::getVar('ip','127.0.0.1');
		$ipInfo = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$ip);
		$this->assignref('ipInfo',$ipInfo);
		parent::display($tpl);
	}
}

?>