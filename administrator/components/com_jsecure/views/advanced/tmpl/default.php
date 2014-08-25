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
	 * @version     $Id: default.php  $
	 */
	// No direct access
	defined( '_JEXEC' ) or die( 'Restricted access' );

	$JSecureConfig = $this->JSecureConfig;
	$document =& JFactory::getDocument();
	$document->addScript(JURI::base()."components/com_jsecure/js/advanced.js");
		
	jimport('joomla.environment.browser');
    $doc =& JFactory::getDocument();
    $browser = &JBrowser::getInstance();
    $browserType = $browser->getBrowser();
    $browserVersion = $browser->getMajor();
    if(($browserType == 'msie') && ($browserVersion = 7))
    {
    	$document->addScript(JURI::base()."components/com_jsecure/js/tabs.js");
    }
	
?>
<form action="index.php?option=com_jsecure&task=advanced" method="post" name="adminForm" onsubmit="return submitbutton();">
<?php
	echo $this->pane->startPane('config-pane');
	echo $this->pane->startPanel(JText::_('MAIL_CONFIG'), 'mail');
?>
<fieldset class="adminform">
	<table class="admintable">
	<tr>
		<td class="paramlist_key">
			<span class="editlinktip hasTip" title="Enable">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('SEND_MAIL_DESCRIPTION'); ?>">
					<?php echo JText::_('SEND_MAIL'); ?>
				</label>
			</span>
		</td>
		<td class="paramlist_value">
			<select name="sendemail" id="sendemail" style="width:100px" onchange="javascript: checkMailStatus(this);">
				<option value="0" <?php echo ($JSecureConfig->sendemail==0)?"selected":''; ?>><?php echo JText::_('COM_JSECURE_NO'); ?></option>
				<option value="1" <?php echo ($JSecureConfig->sendemail==1)?"selected":''; ?>><?php echo JText::_('COM_JSECURE_YES'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('SEND_MAIL_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>
		</td>
	</tr>
	<tr id="sendMailDetails">
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('SEND_MAIL_DETAILS_DESCRIPTION'); ?>">
					<?php echo JText::_('SEND_MAIL_DETAILS'); ?>
				</label>
			</span>
		</td>
		<td class="paramlist_value">
			<select name="sendemaildetails" style="width:150px">
				<option value="1" <?php echo ($JSecureConfig->sendemaildetails == 1)?"selected":''; ?>><?php echo JText::_('SEND_CORRECT_KEY'); ?></option>
				<option value="2" <?php echo ($JSecureConfig->sendemaildetails == 2)?"selected":''; ?>><?php echo JText::_('SEND_WRONG_KEY'); ?></option>
				<option value="3" <?php echo ($JSecureConfig->sendemaildetails == 3)?"selected":''; ?>><?php echo JText::_('SEND_BOTH'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('SEND_MAIL_DETAILS_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>
	</tr>
	<tr id="emailid">
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('EMAIL_ID_DESCRIPTION'); ?>">
					<?php echo JText::_('EMAIL_ID'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<input name="emailid" type="text" value="<?php echo $JSecureConfig->emailid; ?>" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('EMAIL_ID_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>				
	</tr>
	<tr id="emailsubject">
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('EMAIL_SUBJECT_DESCRIPTION'); ?>">
					<?php echo JText::_('EMAIL_SUBJECT'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<input name="emailsubject" type="text" value="<?php echo $JSecureConfig->emailsubject; ?>" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('EMAIL_SUBJECT_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>			
	</tr>
	</table>
</fieldset>
<?php
	echo $this->pane->endPanel();
	echo $this->pane->startPanel(JText::_('IP_CONFIG'), 'ip');
?>
<fieldset class="adminform">
	<table class="admintable">
	<tr>
		<td class="paramlist_key">
		<span class="editlinktip hasTip" title="<?php echo JText::_('IP_TYPE'); ?>">
			<?php echo JText::_('IP_TYPE'); ?>
		</span>
		</td>
		<td class="paramlist_value">
			<select name="iptype" id="iptype" style="width:100px" onchange="javascript: ipLising(this);">
				<option value="0" <?php echo ($JSecureConfig->iptype == 0)?"selected":''; ?>><?php echo JText::_('BLOCKED_IP'); ?></option>
				<option value="1" <?php echo ($JSecureConfig->iptype == 1)?"selected":''; ?>><?php echo JText::_('WHISH_IP'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('IP_TYPE'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>	
		</td>			
	</tr>	
	<tr id="BipLisingAddbox">
		<td class="paramlist_key">
			<span class="editlinktip hasTip" title="<?php echo JText::_('ADD_IP'); ?>">
				<?php echo JText::_('ADD_IP'); ?>
			</span>
		</td>
		<td>
        	<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td align="left" width="58" valign="bottom"><input type="text" name="blacklist_ip1" id="blacklist_ip1" value="" size="3" maxlength="3" onkeyup="isNumeric(this)" /><b>&nbsp;.</b></td>
			<td align="left" width="58" valign="bottom"><input type="text" name="blacklist_ip2" id="blacklist_ip2" value="" size="3" maxlength="3" onkeyup="isNumeric(this)" /><b>&nbsp;.</b></td>
			<td align="left" width="58" valign="bottom"><input type="text" name="blacklist_ip3" id="blacklist_ip3" value="" size="3" maxlength="3" onkeyup="isNumeric(this)" /><b>&nbsp;.</b></td>
			<td align="left" width="58" valign="bottom"><input type="text" name="blacklist_ip4" id="blacklist_ip4" value="" size="3" maxlength="3" onkeyup="isNumeric(this)" /></td>
			<td align="left"><input type="button" id="add_ipB" name="" value="<?php echo JText::_('ADD'); ?>" onclick="addIpB('blacklist_ip', 'blacklist_ips');" /></td>
            </tr>
            </table>
		</td>
	</tr>
	<tr id="BipLisingIpbox">
		<td width="100" align="right" class="key">
			<span class="editlinktip hasTip" title="<?php echo JText::_('ACCESS_IP'); ?>">
			<?php echo JText::_('ACCESS_IP_BLACKLIST'); ?>
			</span>
		</td>
		<td>
			<textarea cols="80" rows="10" class="text_area" type="text" name="iplistB" id="blacklist_ips"><?php echo $JSecureConfig->iplistB; ?></textarea>
		</td>
	</tr>
    <tr id="WipLisingAddbox">
		<td class="paramlist_key">
			<span class="editlinktip hasTip" title="<?php echo JText::_('ADD_IP'); ?>">
				<?php echo JText::_('ADD_IP'); ?>
			</span>
		</td>
		<td>
        	<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td align="left" width="58" valign="bottom"><input type="text" name="whitelist_ip1" id="whitelist_ip1" value="" size="3" maxlength="3" onkeyup="isNumeric(this)" /><b>&nbsp;.</b></td>
			<td align="left" width="58" valign="bottom"><input type="text" name="whitelist_ip2" id="whitelist_ip2" value="" size="3" maxlength="3" onkeyup="isNumeric(this)" /><b>&nbsp;.</b></td>
			<td align="left" width="58" valign="bottom"><input type="text" name="whitelist_ip3" id="whitelist_ip3" value="" size="3" maxlength="3" onkeyup="isNumeric(this)" /><b>&nbsp;.</b></td>
			<td align="left" width="58" valign="bottom"><input type="text" name="whitelist_ip4" id="whitelist_ip4" value="" size="3" maxlength="3" onkeyup="isNumeric(this)" /></td>
			<td align="left"><input type="button" id="add_ipW" name="" value="<?php echo JText::_('ADD'); ?>" onclick="addIpW('whitelist_ip', 'whitelist_ips');" /></td>
            </tr>
            </table>
		</td>
	</tr>
    <tr id="WipLisingIpbox">
		<td width="100" align="right" class="key">
			<span class="editlinktip hasTip" title="<?php echo JText::_('ACCESS_IP'); ?>">
			<?php echo JText::_('ACCESS_IP_WHITELIST'); ?>
			</span>
		</td>
		<td>
			<textarea cols="80" rows="10" class="text_area" type="text" name="iplistW" id="whitelist_ips"><?php echo $JSecureConfig->iplistW; ?></textarea>
		</td>
	</tr>
	</table>
</fieldset>
<?php
	echo $this->pane->endPanel();
	echo $this->pane->startPanel(JText::_('MASTER_PASSWORD'), 'master');
?>
<fieldset class="adminform">
	<table class="admintable">
	<tr>
		<td class="paramlist_key">
		<span class="editlinktip hasTip" title="Enable Master Password">
			<?php echo JText::_('MASTER_PASSWORD_ENABLED'); ?>
		</span>
		</td>
		<td class="paramlist_value" width="570">
			<select name="enableMasterPassword" id="enablemasterpassword" style="width:100px" onchange="javascript: hideMasterPassword(this);">
				<option value="0" <?php echo ($JSecureConfig->enableMasterPassword == 0)?"selected":''; ?>><?php echo JText::_('COM_JSECURE_NO'); ?></option>
				<option value="1" <?php echo ($JSecureConfig->enableMasterPassword == 1)?"selected":''; ?>><?php echo JText::_('COM_JSECURE_YES'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('ENABLEMASTERPASSWORD_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>	
		</td>
	</tr>	
	<tr id="master_password">
		<td class="paramlist_key">
			<span class="editlinktip hasTip" title="Master Password">
					<?php echo JText::_('MASTER_PASSWORD'); ?>
			</span>
		</td>
		<td class="paramlist_value">
			<input type="password" name="master_password" value="" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('MASTER_PASSWORD_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>
		</td>			
	</tr>
    <tr id="verify_master_password">
			<td width="150"><?php echo JText::_('REENTER_MASTER_PASSWORD'); ?></td>
			<td><input type="password" name="ret_master_password" class="textarea" value="" size="50" /></td>
            <td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('REENTER_MASTER_PASSWORD_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>	
		</td>	
		</tr>
	</table>
</fieldset> 
<?php
	echo $this->pane->endPanel();
	echo $this->pane->startPanel(JText::_('EMAIL_MASTER'), 'masteremail');
?>
<fieldset class="adminform">
	<table class="admintable">
		<tr>
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CONFIGURATION_SEND_MAIL_DESCRIPTION'); ?>">
					<?php echo JText::_('CONFIGURATION_SEND_MAIL'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value" width="570">
			<select name="mpsendemail" id="mpsendemail" style="width:100px" onchange="javascript: checkMPMailStatus(this);">
				<option value="0" <?php echo ($JSecureConfig->mpsendemail==0)?"selected":''; ?>><?php echo JText::_('COM_JSECURE_NO'); ?></option>
				<option value="1" <?php echo ($JSecureConfig->mpsendemail==1)?"selected":''; ?>><?php echo JText::_('COM_JSECURE_YES'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CONFIGURATION_SEND_MAIL_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>				
	</tr>	
	<tr id="mpemailsubject">
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CONFIGURATION_EMAIL_SUBJECT_DESCRIPTION'); ?>">
					<?php echo JText::_('CONFIGURATION_EMAIL_SUBJECT'); ?>
				</label>
			</span>			
		</td>
		<td class="paramlist_value">
			<input name="mpemailsubject" type="text" value="<?php echo $JSecureConfig->mpemailsubject; ?>" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CONFIGURATION_EMAIL_SUBJECT_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>			
	</tr>
	<tr id="mpemailid">
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CONFIGURATION_EMAIL_ID_DESCRIPTION'); ?>">
					<?php echo JText::_('CONFIGURATION_EMAIL_ID'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<input name="mpemailid" type="text" value="<?php echo $JSecureConfig->mpemailid; ?>" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CONFIGURATION_EMAIL_ID_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>				
	</tr>
  </table>
</fieldset>
<?php
	
	echo $this->pane->endPanel();
	echo $this->pane->startPanel(JText::_('LOG'), 'Log');
?>
<fieldset class="adminform">
	<table class="admintable">
		<tr>
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('KEEP_LOG_FOR'); ?>">
					<?php echo JText::_('KEEP_LOG_FOR'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<select name="delete_log" id="delete_log" style="width:100px">
				<option value="0" <?php echo ($JSecureConfig->delete_log==0)?"selected":''; ?>>Forever</option>
				<option value="1" <?php echo ($JSecureConfig->delete_log==1)?"selected":''; ?>>1 Months</option>
				<option value="2" <?php echo ($JSecureConfig->delete_log==2)?"selected":''; ?>>2 Months</option>
				<option value="3" <?php echo ($JSecureConfig->delete_log==3)?"selected":''; ?>>3 Months</option>
				<option value="4" <?php echo ($JSecureConfig->delete_log==4)?"selected":''; ?>>4 Months</option>
				<option value="5" <?php echo ($JSecureConfig->delete_log==5)?"selected":''; ?>>5 Months</option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('KEEP_LOG_FOR_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>				
	</tr>	
  </table>
</fieldset>
<?php
	echo $this->pane->endPanel();
	echo $this->pane->endPane();

?>

<input type="hidden" name="option" value="com_jsecure"/>
<input type="hidden" name="task" value="" />
</form>



<script type="text/javascript">
	checkMailStatus(document.getElementById('sendemail'));
	//hideCustomPath(document.getElementById('options'));
	hideMasterPassword(document.getElementById('enablemasterpassword'));
	ipLising(document.getElementById('iptype'));
	checkMPMailStatus(document.getElementById('mpsendemail'));
</script>
