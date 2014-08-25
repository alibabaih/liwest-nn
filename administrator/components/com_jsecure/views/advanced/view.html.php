<?php
/**
 * jSecure Authentication components for Joomla!
 * jSecure Authentication extention prevents access to administration (back end)
 * login page without appropriate access key. 
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

class jsecureViewAdvanced extends JView {
	
	function display($tpl=null){

		$basepath   = JPATH_ADMINISTRATOR .'/components/com_jsecure';
		$configFile	= $basepath.'/params.php';
		require_once($configFile);
		$JSecureConfig = new JSecureConfig();
		
		$this->assignRef('JSecureConfig',$JSecureConfig);
		
		$pane =& JPane::getInstance('Tabs', array(), true);
		$this->assignRef('pane',$pane);
		parent::display($tpl);
	}
	
	function save(){
		$app    = &JFactory::getApplication();
     	$msg  = 'Details Has Been Saved';
		$result = $this->saveDetails();

 		if($result){
 			$link = 'index.php?option=com_jsecure&task=advanced';
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
			$link = "index.php?option=com_jsecure&task=advanced";
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

		$config	  = new JRegistry('JSecureConfig');
		$oldValue = new JSecureConfig();
		$config_array = array();
		$config_array['publish']	                      = $oldValue->publish;
		$config_array['key']                          =  $oldValue->key;
		$config_array['passkeytype']	             =  $oldValue->passkeytype;
		$config_array['options']                     =  $oldValue->options; 
		$config_array['custom_path']				 =  $oldValue->custom_path;
		
		$config_array['enableMasterPassword'] = JRequest::getVar('enableMasterPassword', 0, 'post', 'int');
		$config_array['master_password']       = JRequest::getVar('master_password', '', 'post', 'string');
		
		$config_array['sendemail']				 = JRequest::getVar('sendemail', 0, 'post', 'string');
		$config_array['sendemaildetails']		 = JRequest::getVar('sendemaildetails', '3', 'post', 'string');
		$config_array['emailid']					 = JRequest::getVar('emailid', '', 'post', 'string');
		$config_array['emailsubject']				 = JRequest::getVar('emailsubject', '', 'post', 'string');
		$config_array['iptype']	                     = JRequest::getVar('iptype', 0, 'post', 'int');
		$config_array['iplistB']                        = JRequest::getVar('iplistB', '', 'post', 'string');
		$config_array['iplistW']                        = JRequest::getVar('iplistW', '', 'post', 'string');
		$config_array['mpsendemail']			 = JRequest::getVar('mpsendemail', 0, 'post', 'int');
		$config_array['mpemailsubject']			 = JRequest::getVar('mpemailsubject', '', 'post', 'string');
		$config_array['mpemailid']				 = JRequest::getVar('mpemailid', '', 'post', 'string');
		
		if($config_array['master_password'] == ''){
				$config_array['master_password'] = $oldValue->master_password;			
		} else {
				$masterkeyvalue = $config_array['master_password'];
				$config_array['master_password'] = md5(base64_encode($config_array['master_password']));
		}
	
		$config_array['adminType'] = $oldValue->adminType ;
		$config_array['delete_log']  = JRequest::getVar('delete_log', '0', 'post', 'int');

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
		
						case 'master_password':
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . JText::_('JSECURE_EVENT_MASTER_PASS_KEY_CHANGE') . '<br/>') : JText::_('JSECURE_EVENT_MASTER_PASS_KEY_CHANGE') . '<br/>';
							break;
						
						case 'iplistB':
							$IPB     = explode("\n",$newValue[$currentKeyName]);
							$iplistB = implode(", ",$IPB);
		
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $iplistB . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $iplistB . '<br/>');
							break;
							
						case 'iplistW':
							$IPW     = explode("\n",$newValue[$currentKeyName]);
							$iplistW = implode(", ",$IPW);
		
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $iplistW . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $iplistW . '<br/>');
							break;	
						
						case 'enableMasterPassword':
							$val = ($newValue[$currentKeyName] !=0) ? $enableMasterPassword[1] :  $enableMasterPassword[0];
							
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
							break;

						case 'passkeytype':
							$val = ($newValue[$currentKeyName] == 'form') ? $passkeytype[1] :  $passkeytype[0] ;
							
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
						break;

						case 'options':
							$val = ($newValue[$currentKeyName] !=0) ? $options[1] :  $options[0];
							
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
							break;

						case 'sendemail':
							$val = ($newValue[$currentKeyName] !=0) ? $sendemail[1] :  $sendemail[0];
						
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
							break;

						case 'sendemaildetails':
							if($newValue[$currentKeyName] ==1)
								$val = $sendemaildetails[1];
							else if($newValue[$currentKeyName] == 2)
								$val = $sendemaildetails[2];
							else if($newValue[$currentKeyName] == 3)
								$val = $sendemaildetails[3];

							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
							break;

						case 'iptype':
							$val = ($newValue[$currentKeyName] !=0) ? $iptype[1] :  $iptype[0];
							
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : ( $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
							break;

						case 'mpsendemail':
							$val = ($newValue[$currentKeyName] !=0) ? $mpsendemail[1] :  $mpsendemail[0];
							
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>') : (  $JSecureCommon[$currentKeyName] . ' = ' . $val . '<br/>');
							break;

						case 'delete_log':
							
							$val=0;
							$newLogValue= $newValue[$currentKeyName].' Month';
							if($newLogValue == 0){
								$val='Forever';
							}else{

								for($i=0;$i<=5;$i++){
									if($newLogValue == $delete_log[$i])
										$val=$delete_log[$i];
								}

							}
							$ModifiedValue = ($ModifiedValue != '') ? ($ModifiedValue . $JSecureCommon[$currentKeyName] . ' : ' . $val . '<br/>') : (  $JSecureCommon[$currentKeyName] . ' : ' . $val . '<br/>');
							
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