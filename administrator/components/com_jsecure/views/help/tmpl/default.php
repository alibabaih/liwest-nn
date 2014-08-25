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
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> Readme </TITLE>
 </HEAD>

 <BODY>
	<form action="index.php" method="post" name="adminForm">
	<div style='width: 90%; text-align: left;'>
		<p><strong>Drawback:</strong></p>    
		<p>Joomla has one drawback, any web user can easily know the site is created in Joomla! by typing the URL to access the administration area (i.e. www.site name.com/administration). This makes hackers hack the site easily once they crack id and password for Joomla!. </p>    
		<p><h1>Instructions</h1></p>

		<p>jSecure Authentication module prevents access to administration (back end) login page without appropriate access key.</p>

		<p><h2>Important! </h2></p>

		<p>In order for jSecure to work the jSecure <b>plugin</b> must be enabled. Go to Extensions>Plugin Manager and look for the "<b>System - jSecure Authentication plugin</b>". Make sure this plug in is enabled.</p>

		<p><h2>Basic Configuration:</h2></p>

		<p>The basic configuration will hide your administrator URL from public access. This is all most people need.</p>

		<ol>
			<li>Set "enable" to "yes".</li>
			<li>Set the "Pass Key" to "URL" This will hide the administrator URL.</li>
			<li><p>In the "Key" field enter the key that will be part of your new administrator URL. For example, if you enter "test" into the key field, then the administrator URL will be http://www.yourwebsite/administrator/?test. Please note that you cannot have a key that is only numbers.
			<p>If you do not enter a key, but enable the jSecure component, then the URL to access the administrator area is /?jSecure (http://www.yourwebsite/administrator/?jSecure).</li>
			<li>Set the "Redirect Options" field. By default, if someone tries to access you /administrator URL without the correct key, they will be redirected to the home page of your Joomla site. You can also set up a "Custom Path" is you would like the user to be redirected somewhere else, such as a 404 error page.</li>
		</ol>
		 
		<p><h2>Advanced Configuration:</h2>

		<p>The Advanced Configuration tab has additional features that you can activate. </p>

		<p><b>Mail tab:</b> This sets whether you want an email to be sent every time there is a failed login attempt into the Joomla administration area. You can set it to send the jSecure key or the incorrect key that was entered</p>

		<p><b>IP tab:</b> This tab allows you to control which IPs have access to your administrator URL. 

		<p><b>White Listed IPs:</b>  If set to "White Listed IPs" you can make a white list for certain IPs. Only those specific IPS will be allowed to access your administrator URL.</p> 

		<p><b>Blocked IPs:</b> If set to "Blocked IPs" you can block certain IPs  form accessing your administrator URL.</p>

		<p><b>Master Password:</b> You can block access to the jSecure component from other administrators. Setting to "Yes", allows you to create a password that will be required when any administrator tries to access the jSecure configuration settings in the Joomla administration area. If you do not enter a master password, the default password will be "jSecure".</p>

		<p><b>Master Mail:</b> These setting allow you to have an email sent every time the jSecure configuration is changed.</p>

		</p><b>Log:</b> This setting allows you to decide how long the jSecure logs should remain in the database. The longer this is set for, the more database space will be used.</p>

		<p><h2>View Log:</h2></p>

		<p>jSecure will record any attempt that is made to access the Joomla /administrator directory. It will record the users IP,  user name (if the login is successful), the nature of their login attempt, and the date the login attempt occurred.</p>



		<p>For More information http://joomlaserviceprovider.com<br>
		Thanks to the team (Ajay Lulia, Bhavin Shah, Anurag Soni) for developing the Component and Plugin.<br>
		Thanks to Aaron Handford, Ajay Lulia for help with the component conceptualization.<br>
		Thanks to Sam Moffatt for converting Joomla! 1.0 module to a Joomla!  1.5 system plugin.<br></p>
		<p><h2>Change Log:</h2></p>
		<p style="padding-left:20px;">
        		2.1.10(3-Feb-2012):<br/>
				Fixed  JSecureConfig::$iplistB and JSecureConfig::$iplistW  bug for Joomla 1.5.X, Joomla 1.6.X & Joomla 1.7.0.<br/>
				Fixed issues with mail headers for Joomla 2.5 .<br/>
				Added text input feild instead of text area in the form option of Basic Parameters for Joomla 1.5.X, Joomla 1.6.X & Joomla 1.7.0.<br/><br/>

        		2.1.9(18-August-2011):<br/>
				Separate boxes added for blacklist and whitelist IP Addresses for Joomla 1.5.X, Joomla 1.6.X & Joomla 1.7.0.<br/>
				Multiple IP Addresses problem resolved for Joomla 1.5.X, Joomla 1.6.X & Joomla 1.7.0.<br/>
				Fixed Master Password & Verify Master field  bug for Joomla 1.5.X, Joomla 1.6.X & Joomla 1.7.0.<br/>
				Language files updated for description of Verify Master Password field.<br/>
				Updated validations for Master Password & Verify Master Password fields.<br/>
				Fixed issues with tabs for Joomla 1.7.0 on IE7.<br/><br/>
				2.1.9(21-Mar-11):<br/>
				Fixed language related issues.<br/><br/>
				2.1.8(14-Jan-11):<br/>
				Fixed the code for redirection.<br/><br/>
				2.1.7(04-Aug-10):<br/>
				Fixed save functionality issue on IE8<br/><br/>
				2.1.6(28-July-10):<br/>
				Fixed notices issue.<br/><br/>
				2.1.5(20-July-10):<br/>
				1. Added condition to check the configuration file is writable or not.<br/>
				2. Added redirection on login page after correct key entered.<br/><br/>
				2.1.4(03-July-10):<br/>
				Fixed Email Validation issue.<br/><br/>
				2.1.3(02-July-10):<br/>	
				1. Added log feature.<br/>
				2. Fixed white listed ip issue.<br/>
				3. Changed the component parameters to convert in Basic and Advanced configuration.<br/>
				4. Changed the layout of backend.<br/>
				5. Created jSecure component and plugin for Joomla 1.6.<br/><br/>
				2.1.2(02-June-10):<br/>
				Fixed small error.<br/><br/>
				2.1.1(31-May-10):<br>
				1. Added Master Password to access the jSecure Authentication.<br>
				2. Added E-mail option to send the change log in jSecure Authentication.<br>
				3. User can choose from White Listed IPs / Blocked IPs.<br>
				4. User Friendly option to add ip address.<br>
			    5. Enter specific IPs(White Listed IPs) that will allow access to administration area.<br><br>
				2.1.0(19-Apr-10):<br>
				Fixed security bug.<br><br>
				2.0.1(14-Apr-10):<br>
				1. Optimized the code.<br>
				2. Fixed the IP issue in mail.<br>
				3. Added Licenses information in files.<br><br>
				2.0(01-Apr-10):<br>
				Added new features<br><br>
				1.0.9(10-Jun-09):<br>
				Fixed warning message.<br><br>
				1.0.8(02-Jun-09):<br>
				Fixed the case sensitivity check.<br><br>
				1.0.7(21-Mar-09):<br>
				Fixed the code for redirection.<br><br>
				1.0.6(23-Dec-08):<br> 
				Fixed security bug. Updated the readme file.<br><br>
				1.0.5(16-Oct-08):<br> 
				Fixed redirection issue.<br><br>
				1.0.4(26-Sep-08):<br>
				Fix for J1.5 to use proper custom tag and fixed a php error.<br><br>
				1.0.3(15-Sep-08):<br>
				Fix for J1.5 call to admin login page using index2.php, please update your copy of jSecure Authentication.<br><br>
				1.0.2(30-Aug-08):<br>
				Fix for J1.5 params (Thanks to Christer) <br><br>
				1.0: Initial Version 1.0.1:<br>
				Fix for J1.5 Native<br><br>
		</p>
		<br>
		<p>
			<strong>License:</strong> This is free software and you may redistribute it under the GPL. jSecure comes with absolutely no warranty. Use at your own risk. For details, see the license at http://www.gnu.org/licenses/gpl.txt Other licenses can be found in LICENSES folder.
		</p>
	</div>
	<input type="hidden" name="option" value="com_jsecure"/>
	<input type="hidden" name="task" value=""/>
	</form>
 </BODY>
</HTML>