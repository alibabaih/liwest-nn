<?php
/**
 * @version		$Id: k2.php 1373 2011-11-29 15:59:12Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgUserK2 extends JPlugin {

    function plgUserK2(&$subject, $config) {

        parent::__construct($subject, $config);
    }

    function onUserAfterSave($user, $isnew, $success, $msg) {
    	return $this->onAfterStoreUser($user, $isnew, $success, $msg);
    }
    
    function onUserLogin($user, $options) {
    	return $this->onLoginUser($user, $options);
    }
    
    function onUserLogout($user) {
    	return $this->onLogoutUser($user);
    }
    
    function onUserAfterDelete($user, $success, $msg){
    	return $this->onAfterDeleteUser($user, $success, $msg);
    }
    
    function onUserBeforeSave($user, $isNew){
    	return $this->onBeforeStoreUser($user, $isNew);
    }
    
    function onAfterStoreUser($user, $isnew, $success, $msg) {

    	$mainframe = &JFactory::getApplication();
    	$params = &JComponentHelper::getParams('com_k2');
    	jimport('joomla.filesystem.file');
		$task = JRequest::getCmd('task');
    	
    	if($mainframe->isSite() && $task != 'activate' && JRequest::getInt('K2UserForm')) {
    		JPlugin::loadLanguage('com_k2');
    		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'tables');
    		$row = &JTable::getInstance('K2User', 'Table');
    		$k2id = $this->getK2UserID($user['id']);
    		JRequest::setVar('id', $k2id, 'post');
    		$row->bind(JRequest::get('post'));
    		$row->set('userID', $user['id']);
    		$row->set('userName', $user['name']);
    		if($isnew)
    		$row->set('group', $params->get('K2UserGroup', 1));
    		else
    		$row->set('group', NULL);
    		$row->set('gender', JRequest::getVar('gender'));
    		$row->set('url', JRequest::getVar('url'));

    		$row->set('description', JRequest::getVar('description', '', 'post', 'string', 2));
    		if($params->get('xssFiltering')){
    			$filter = new JFilterInput(array(), array(), 1, 1, 0);
    			$row->description = $filter->clean( $row->description );
    		}

    		$file = JRequest::get('files');

    		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'class.upload.php');
    		$savepath = JPATH_ROOT.DS.'media'.DS.'k2'.DS.'users'.DS;

    		if (isset($file['image']) && $file['image']['error'] == 0 && !JRequest::getBool('del_image')) {
    			$handle = new Upload($file['image']);
    			$handle->allowed = array('image/*');
    			if ($handle->uploaded) {
    				$handle->file_auto_rename = true;
    				$handle->file_overwrite = false;
    				$handle->file_new_name_body = $row->id;
    				$handle->image_resize = true;
    				$handle->image_ratio_y = true;
    				$handle->image_x = $params->get('userImageWidth', '100');
    				$handle->Process($savepath);
    				$handle->Clean();
    			} else {
    				$mainframe->enqueueMessage(JText::_('K2_COULD_NOT_UPLOAD_YOUR_IMAGE').$handle->error, 'notice');
    			}
    			$row->image = $handle->file_dst_name;
    		}

    		if (JRequest::getBool('del_image')) {

    			if (JFile::exists(JPATH_ROOT.DS.'media'.DS.'k2'.DS.'users'.DS.$row->image)) {
    				JFile::delete(JPATH_ROOT.DS.'media'.DS.'k2'.DS.'users'.DS.$row->image);
    			}
    			$row->image = '';
    		}

    		$row->store();
    		$itemid = $params->get('redirect');

    		if(!$isnew && $itemid){
    			$menu =& JSite::getMenu();
    			$item = $menu->getItem($itemid);
    			$url = JRoute::_($item->link.'&Itemid='.$itemid, false);
    			if (JURI::isInternal($url)) {
    				$mainframe->redirect( $url, JText::_('K2_YOUR_SETTINGS_HAVE_BEEN_SAVED') );
    			}
    		}
    	}

    }
    
    function onLoginUser($user, $options) {
    	$params = &JComponentHelper::getParams('com_k2');
        $mainframe = &JFactory::getApplication();
        if($mainframe->isSite() && $params->get('cookieDomain')){
            $tmp = &JFactory::getUser();
            setcookie ("userID", $tmp->id, 0, '/', $params->get('cookieDomain'), 0 );  
        }
        return true;
    }

    function onLogoutUser($user){
    	$params = &JComponentHelper::getParams('com_k2');
        $mainframe = &JFactory::getApplication();
        if($mainframe->isSite() && $params->get('cookieDomain')){
            setcookie ("userID", 'false', 0, '/', $params->get('cookieDomain'), 0 );  
        }
        return true;
    }

    function onAfterDeleteUser($user, $succes, $msg) {

        $mainframe = &JFactory::getApplication();
        $db = &JFactory::getDBO();
        $query = "DELETE FROM #__k2_users WHERE userID={$user['id']}";
        $db->setQuery($query);
        $db->query();
    }
    
	function onBeforeStoreUser($user, $isNew) {
		$mainframe = &JFactory::getApplication();
		$params = &JComponentHelper::getParams('com_k2');
		$session = &JFactory::getSession();
		if($params->get('K2UserProfile') && $isNew && $params->get('recaptchaOnRegistration') && $mainframe->isSite() && !$session->get('socialConnectData')) {
			require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'recaptchalib.php');
			$privatekey = $params->get('recaptcha_private_key');
			$recaptcha_challenge_field = isset($_POST["recaptcha_challenge_field"])? $_POST["recaptcha_challenge_field"]:'';
			$recaptcha_response_field = isset($_POST["recaptcha_response_field"])? $_POST["recaptcha_response_field"]:'';
			$resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $recaptcha_challenge_field, $recaptcha_response_field);
			if (!$resp->is_valid) {
				if(K2_JVERSION == '16') {
					$url = 'index.php?option=com_users&view=registration';
				}
				else {
					$url = 'index.php?option=com_user&view=register';
				}
				$mainframe->redirect($url, JText::_('K2_THE_WORDS_YOU_TYPED_DID_NOT_MATCH_THE_ONES_DISPLAYED_PLEASE_TRY_AGAIN'), 'error');
			}
		}
	}

    function getK2UserID($id) {

        $db = &JFactory::getDBO();
        $query = "SELECT id FROM #__k2_users WHERE userID={$id}";
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

}
