<?php
$JSecureCommon 
= array('publish'						  => JText::_('ENABLE'),
			'enableMasterPassword' => JText::_('MASTER_PASSWORD_ENABLED'), 
			'master_password'		  => JText::_('MASTER_PASSWORD'),
			'passkeytype'				  => JText::_('PASS_KEY'), 
			'key'							  => JText::_('KEY'), 
			'options'						  => JText::_('REDIRECT_OPTIONS'), 
			'custom_path'				  => JText::_('CUSTOM_PATH'), 
			'sendemail'					  => JText::_('SEND_MAIL'), 
			'sendemaildetails'		  => JText::_('SEND_MAIL_DETAILS'), 
			'emailid'						  => JText::_('EMAIL_ID'),
			'emailsubject'				  => JText::_('EMAIL_SUBJECT'),
			'iptype'						  => JText::_('IP_TYPE'),
			'iplistB'						  => JText::_('ACCESS_IP'),
			'iplistW'						  => JText::_('ACCESS_IP'),
			'mpsendemail'				  => JText::_('CONFIGURATION_SEND_MAIL'),
			'mpemailsubject'			  => JText::_('CONFIGURATION_EMAIL_SUBJECT_DESCRIPTION'),
			'mpemailid'					  => JText::_('CONFIGURATION_EMAIL_ID'),
			'delete_log'					  => JText::_('CHANGED_LOG_DESC'),
			);


$enableMasterPassword = array('0' => JText::_('NO') , '1'	=> JText::_('YES') );

$passkeytype =  array(
		   '0'	=> JText::_('URL'),
		   '1'	=> JText::_('FORM')
);

$options = array(
		   '0'	=> JText::_('REDIRECT_INDEX'),
		   '1'	=> JText::_('CUSTOM_PATH')
);

$sendemail = array(
		   '0'	=> JText::_('NO'),
		   '1'	=> JText::_('YES')
);

$sendemaildetails = array(
		   '1'	=> JText::_('SEND_CORRECT_KEY'),
		   '2'	=> JText::_('SEND_WRONG_KEY'),
		   '3'	=> JText::_('SEND_BOTH')
);

$iptype = array(
		   '0'	=> JText::_('BLOCKED_IP'),
		   '1'	=> JText::_('WHISH_IP')
);

$mpsendemail = array(
		   '0'	=> JText::_('NO'),
		   '1'	=> JText::_('YES')
);

$delete_log= array(
		   '0'	=> JText::_('Forever'),
		   '1'	=> JText::_('1 Month'),
		   '2'	=> JText::_('2 Month'),
		   '3'	=> JText::_('3 Month'),
		   '4'	=> JText::_('4 Month'),
		   '5'	=> JText::_('5 Month')
);
?>