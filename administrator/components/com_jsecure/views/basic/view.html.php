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
jimport('joomla.html.pane');

class jsecureViewBasic extends JView {
	
	function display($tpl=null){

		$basepath   = JPATH_ADMINISTRATOR .'/components/com_jsecure';
		$configFile	= $basepath.'/params.php';
		require_once($configFile);
		$JSecureConfig = new JSecureConfig();
		
		$this->assignRef('JSecureConfig',$JSecureConfig);
		
		$pane =& JPane::getInstance('Tabs', array(), true);
		$this->assignRef('pane',$pane);

		
	//Log start here
		$model = $this->getModel('jsecurelog');
		
		if($JSecureConfig->delete_log != 0)
			$deleteLog = $model->deleteLog($JSecureConfig->delete_log);
		
		$data = $model->getLimitList();		
		$total = $model->getTotalList();
		
		// Create the pagination object
		jimport('joomla.html.pagination');

		$this->assignref('data',$data);
	//log end here


		parent::display($tpl);
	}
	
	function save(){
		$app = &JFactory::getApplication();
	    $msg  = 'Details Has Been Saved';
		$result = $this->saveDetails();
 		if($result){
 			$link = 'index.php?option=com_jsecure&task=basic';
 			$msg  = 'Details Has Been Saved';
 			$app->redirect($link,$msg);
 	    }
 	}
 	
 	function saveDetails(){	
 		
		jimport('joomla.filesystem.file');	
		$app           =& JFactory::getApplication();
		$option		= JRequest::getVar('option', '', '', 'cmd');
		$post       = JRequest::get( 'post' );
		
		$basepath   = JPATH_ADMINISTRATOR .'/components/com_jsecure';
		$configFile	= $basepath.'/params.php';
		
		$xml	    = $basepath.'/com_jsecure.xml';
		
		require_once($configFile);
		
		if(! is_writable($configFile)){
			$link = "index.php?option=com_jsecure";
			$msg = 'Configuration File is Not Writable /administrator/components/com_jsecure/params.php ';
			$app->redirect($link, $msg, 'notice'); 
			exit();
		}

		// Read the ini file
		if (JFile::exists($configFile)) {
			$content = JFile::read($configFile);
		} else {
			$content = null;
		}

		$config = new JRegistry('JSecureConfig');
		$oldValue = new JSecureConfig();

		$config_array = array();
		$config_array['publish']	                      = JRequest::getVar('publish', 0, 'post', 'int');
		$config_array['key']                          = JRequest::getVar('key', '', 'post', 'string');
		$config_array['passkeytype']	             = JRequest::getVar('passkeytype', 'url', 'post', 'string');
		$config_array['options']                     = JRequest::getVar('options', 0, 'post', 'string'); 
		$config_array['custom_path']				 = JRequest::getVar('custom_path', '', 'post', 'string');

		$config_array['enableMasterPassword'] = $oldValue->enableMasterPassword;
		$config_array['master_password']       = $oldValue->master_password;
		$config_array['sendemail']				 = $oldValue->sendemail;
		$config_array['sendemaildetails']		 = $oldValue->sendemaildetails;
		$config_array['emailid']					 = $oldValue->emailid;
		$config_array['emailsubject']				 = $oldValue->emailsubject;
		$config_array['iptype']	                     = $oldValue->iptype;
		$config_array['iplistB']                        = $oldValue->iplistB;
		$config_array['iplistW']                        = $oldValue->iplistW;
		$config_array['mpsendemail']			 = $oldValue->mpsendemail;
		$config_array['mpemailsubject']			 = $oldValue->mpemailsubject;
		$config_array['mpemailid']				 = $oldValue->mpemailid;
		$config_array['adminType']				 = $oldValue->adminType;
		$config_array['delete_log']				 = $oldValue->delete_log;
		
		if($config_array['key'] == ''){
			
			$config_array['key'] = $oldValue->key;			
		} else {
			$keyvalue = $config_array['key'];
			$config_array['key'] = md5(base64_encode($config_array['key']));
		}

		if($config_array['publish']	== 1){
			$session    =& JFactory::getSession();
			$session->set('jSecureAuthentication', 1);
		}

		$modifiedFieldName	=$this->checkModifiedFieldName($config_array, $oldValue, $JSecureCommon, $keyvalue, $masterkeyvalue);
		
		$config->loadArray($config_array);
		
		$fname = JPATH_COMPONENT_ADMINISTRATOR.DS.'params.php';
		if (JFile::write($fname, $config->toString('PHP', array('class' => 'JSecureConfig','closingtag' => false)))) 
			$msg = JText::_('The Configuration Details have been updated');
		 else 
			$msg = JText::_('ERRORCONFIGFILE');
	
		if($modifiedFieldName != ''){
			$basepath   = JPATH_COMPONENT_ADMINISTRATOR .'/models/jsecurelog.php';
			require_once($basepath);
		
			$model 	= $this->getModel( 'jsecurelog' );
			$change_variable = str_replace('<br/>', '\n', $modifiedFieldName); 
		
			$insertLog = $model ->insertLog('JSECURE_EVENT_CONFIGURATION_FILE_CHANGED', $change_variable);			
		}
		
		$JSecureConfig		  = new JSecureConfig();
		if($JSecureConfig->mpsendemail != '0')
			$result	= $this->sendmail($JSecureConfig, $modifiedFieldName);
		
		return true;
 	}	
	
 	function checkModifiedFieldName($newValue, $oldValue, $JSecureCommon, $keyvalue=null, $masterkeyvalue=null){

	$basepath   = JPATH_ADMINISTRATOR .'/components/com_jsecure';
	$commonFile	= $basepath.'/common.php';
	require_once($commonFile);
	
		foreach($newValue as $key){
			$currentKeyName =  key($newValue);
		
			if(isset($oldValue)){
			 
			 if(array_key_exists($currentKeyName, $oldValue)){
				$result=($newValue[$currentKeyName] == $oldValue->$currentKeyName) ? '1' : '0';
				
				if(!$result){
					
					switch($currentKeyName){
					
						case 'key':
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . JText::_('JSECURE_EVENT_PASS_KEY_CHANGE') . '<br/>') : JText::_('JSECURE_EVENT_PASS_KEY_CHANGE') . '<br/>';
							break;

						case 'passkeytype':
							$val = ($newValue[$currentKeyName] == 'form') ? $passkeytype[1] :  $passkeytype[0] ;
							
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
						break;

						case 'options':
							$val = ($newValue[$currentKeyName] !=0) ? $options[1] :  $options[0];
							
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
							break;

						default:
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $newValue[$currentKeyName] . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $newValue[$currentKeyName] . '<br/>');
							break;
					}

				}	
				next($newValue);
			 }
		  }
		}
	  return $ModifiedValue;
   }
 	
   function sendmail($JSecureConfig, $fieldName){
   		
		$config   = new JConfig();

		 $to        = $JSecureConfig->mpemailid;	
		 $to        = ($to) ? $to :  $config->mailfrom;
		 
		 if($to){
			$fromEmail  = $config->mailfrom;
			$fromName  = $config->fromname;
			$subject      = $JSecureConfig->mpemailsubject;
			$body         = JText::_( 'BODY_MESSAGE_FOR_MODIFIED_FIELDNAME:' ) .$_SERVER['REMOTE_ADDR'];
			$body		.= " ";
			$body		.= $fieldName ;  
			
			//JUtility::sendMail($fromEmail, $fromName, $to, $subject, $body,1);
			$headers .= 'From: '. $fromName . ' <' . $fromEmail . '>';
			mail($to, $subject, $body, $headers);
		 }	
	}   
}

?>