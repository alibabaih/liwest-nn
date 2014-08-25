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
 * @version     $Id: jsecurelog.php  $
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

class jSecureModeljSecureLog extends JModel {
	
	function __construct(){
		parent::__construct();
 	}
 	
 	function getData(){ 		
 		 		
		$app    = &JFactory::getApplication();
		$limit		= $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart	= $app->getUserStateFromRequest('limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		$db =& $this->getDBO();
		
		$query = "SELECT * from #__jsecurelog";
		if(!empty($this->id))
			$query .= " Where id=".$this->id;
		
		$query .= " ORDER BY id desc"; 
		if(!empty($this->id)){
			$db->setQuery( $query );
		} else {
			$db->setQuery( $query,$limitstart,$limit );
		}
		
		$rows = $db->loadObjectList();
		
		return $rows;
 	}
	
	function getLimitList(){
		$db =& $this->getDBO();
		
		$query = "SELECT * from #__jsecurelog ORDER BY id desc LIMIT 0 , 10 ";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;
	}

 	function getTotalList(){
		$db =& $this->getDBO();
		
		$query = "SELECT * from #__jsecurelog";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return count($rows);
	}

	function insertLog($code, $change_variables=null){
		$db =& $this->getDBO();

		$user =& JFactory::getUser();
		$userid = $user->id;
		
		$query = 'INSERT INTO #__jsecurelog(date, ip, userid, code, change_variable) VALUES ("'.date('Y-m-d H:i:s').'", "'.$_SERVER['REMOTE_ADDR'].'", '.$userid.', "'.$code.'", "'.htmlspecialchars($change_variables).'")';

		$db->setQuery($query);
		$db->query();
		return true;
		
	}

	function deleteLog($month){
		$db =& $this->getDBO();
		
		$date =  date('Y-m-d H:i:s',mktime( date('H'), date('i'), date('s'), date('m')-$month, date('d'), date('Y')));
		
		$WHERE =	" WHERE `date` < ' ".$date." '";
		
		$query = "DELETE FROM #__jsecurelog " . $WHERE ;
		
		$db->setQuery($query);
		$db->query();
		
		return true;
	}
}
?>