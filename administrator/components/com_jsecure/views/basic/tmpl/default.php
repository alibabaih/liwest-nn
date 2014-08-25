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
$document->addScript(JURI::base()."components/com_jsecure/js/basic.js");

JHTML::_('behavior.mootools');
JHTML::_('script','system/modal.js', false, true);
JHTML::_('stylesheet','system/modal.css', array(), true);



$document =& JFactory::getDocument();
$document->addScriptDeclaration("window.addEvent('domready', function() {
			$$('.hasTip').each(function(el) {
				var title = el.get('title');
				if (title) {
					var parts = title.split('::', 2);
					el.store('tip:title', parts[0]);
					el.store('tip:text', parts[1]);
				}
			});
			var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
		});
		window.addEvent('domready', function() {

			SqueezeBox.initialize({});
			SqueezeBox.assign($$('a.modal'), {
				parse: 'rel'
			});
		});
");

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
<form action="index.php?option=com_jsecure" method="post" name="adminForm" onsubmit="return submitbutton();">
<?php
echo $this->pane->startPane('config-pane');

echo $this->pane->startPanel(JText::_('BASIC_CONFIG'), 'basic');
?>
<fieldset class="adminform">
	<table class="admintable">
	<tr>
		<td class="paramlist_key">
		<span class="editlinktip hasTip" title="Enable">
			<?php echo JText::_('ENABLE'); ?>
		</span>
		</td>
		<td class="paramlist_value">
			<select name="publish" id="publish" style="width:100px">
				<option value="0" <?php echo ($JSecureConfig->publish == 0)?"selected":''; ?>><?php echo JText::_('COM_JSECURE_NO'); ?></option>
				<option value="1" <?php echo ($JSecureConfig->publish == 1)?"selected":''; ?>><?php echo JText::_('COM_JSECURE_YES'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('PUBLISHED_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>	
		</td>			
	</tr>	
	<tr>
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('KEY_DESCRIPTION'); ?>">
					<?php echo JText::_('PASS_KEY'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<select name="passkeytype" style="width:100px">
				<?php
				$url  = $form = '';
				$url  = ($JSecureConfig->passkeytype == "url")? "selected" : "";
				$form = ($JSecureConfig->passkeytype == "form")? "selected" : "";
				if($form == '')
					$url = "selected";	 	
				?>
				<option value="url" <?php echo $url; ?>><?php echo JText::_('URL'); ?></option>
				<option value="form" <?php echo $form; ?>><?php echo JText::_('FORM'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">	
		</td>			
	</tr>
	<tr>
		<td class="paramlist_key">
					<?php echo JText::_('KEY'); ?>
		</td>
		<td class="paramlist_value">
			<input type="password" name="key" value="" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('KEY_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>	
		</td>			
	</tr>
	<tr>
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('REDIRECT_OPTIONS_DESCRIPTION'); ?>">
					<?php echo JText::_('REDIRECT_OPTIONS'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<select name="options" id="options" style="width:150px" onchange="javascript: hideCustomPath(this);">
				<option value="0" <?php echo ($JSecureConfig->options == 0)?"selected":''; ?>><?php echo JText::_('REDIRECT_INDEX'); ?></option>
				<option value="1" <?php echo ($JSecureConfig->options == 1)?"selected":''; ?>><?php echo JText::_('CUSTOM_PATH'); ?></option>
			</select>
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('REDIRECT_OPTIONS_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>			
	</tr>
	<tr id="custom_path">
		<td class="paramlist_key">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CUSTOM_PATH_DESCRIPTION'); ?>">
					<?php echo JText::_('CUSTOM_PATH'); ?>
				</label>
			</span>		
		</td>
		<td class="paramlist_value">
			<input name="custom_path" type="text" value="<?php echo $JSecureConfig->custom_path; ?>" size="50" />
		</td>
		<td class="paramlist_description">
			<span class="editlinktip">
				<label id="paramsshowAllChildren-lbl" for="paramsshowAllChildren" class="hasTip" title="<?php echo JText::_('CUSTOM_PATH_DESCRIPTION'); ?>">
					<img src="templates/bluestork/images/menu/icon-16-info.png" border="0">
				</label>
			</span>		
		</td>				
	</tr>
	</table>
</fieldset>
<input name="sendemail" type="hidden" value="<?php echo $JSecureConfig->sendemail; ?>" size="50" />
<?php
	echo $this->pane->endPanel();
	echo $this->pane->endPane();
?>

<input type="hidden" name="option" value="com_jsecure"/>
<input type="hidden" name="task" value="saveBasic" />
</form>

<!-- Footer Start -->

<table cellpadding="4" cellspacing="0" border="0" class="adminform" width="100%">
  <tr>
	<td valign="top" style="padding-top:10px;" width="60%">
		<!-- log start here -->
			<table class="adminlist" cellpadding="4" cellspacing="0" border="0" style="width:100%" width="100%">
			<thead>
				<tr>
					<th width="5">
						<?php echo JText::_( 'Num' ); ?>
					</th>
					<th class="title">
						<?php echo JText::_( 'IP' ); ?>
					</th>
					<th class="title">
						<?php echo JText::_( 'User Name' ); ?>
					</th>
					<th class="title">
						<?php echo JText::_( 'Code' ); ?>
					</th>
					<th class="title">
						<?php echo JText::_( 'Log' ); ?>
					</th>
					<th class="title">
						<?php echo JText::_( 'Date' ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6" style="text-align:right;padding-right:40px;">
						<a href="index.php?option=com_jsecure&amp;task=log">Read More... </a>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
				$i=0;$k = 0;
				foreach($this->data as $row){
					$user = JUser::getInstance($row->userid);
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo  $i+1; ?>
					</td>
					<td align="left">
						<a class="modal" title="IP INFO"  href="index.php?option=com_jsecure&amp;task=ipinfo&amp;ip=<?php echo $row->ip;?>&amp;tpl=component" rel="{handler: 'iframe', size: {x: 500, y: 350}}">		
							<?php echo $row->ip; ?>
						</a>
					</td>	
					<td align="left">
						<?php echo $user->username; ?>
					</td>
					<td align="left">
						<?php echo JText::_($row->code); ?>
					</td>
					<td align="left">
						<?php echo str_replace("\n","<br/>",$row->change_variable); ?>
					</td>
					<td align="left">
						<?php echo str_replace("\n","<br/>",$row->date); ?>
					</td>
				</tr>
				<?php
					$k = 1 - $k;	$i++;
				}
				?>
			</tbody>
			</table>
			<input type="hidden" name="option" value="com_jsecure" />
			<input type="hidden" name="task" value="log" />
			<input type="hidden" name="boxchecked" value="0" />
<!-- log end here -->
	</td>
	<td valign="top"> 
		<table cellpadding="4" cellspacing="0" border="1" class="adminform">
			
			<tr class="row0">
				<th colspan="2"  style="background-color:#FFF;">
						<div style="float:left;">
						<a href="http://www.joomlaserviceprovider.com" title="Joomla Service Provider" target="_blank"><img src="components/com_jsecure/images/logo.jpg" alt="Joomla Service Provider" border="none"/></a></div>
						<div style="text-align:center;margin-top:25px;"><h3><?php echo JText::_( 'jSecure Authentication' ); ?></h3></div>
						
				</th>
			</tr>
			<tr class="row1">
				<td width="100"><?php echo JText::_( 'VERSION_TEXT' ); ?></td>
				<td><?php echo JText::_( 'VERSION_DESCRIPTION' ); ?></td>
			</tr>
			<tr class="row2">
				<td><?php echo JText::_( 'SUPPORT' ); ?></td>
				<td><a href="http://www.joomlaserviceprovider.com/component/kunena/5-jsecure-authentication.html" target="_blank"><?php echo JText::_( 'JSECURE_AUTHENTICATION_FORUM' ); ?></a></td>
			</tr>
		</table>
	</td>
  </tr>
  </table>


<!-- Footer End  -->

<script type="text/javascript">
	hideCustomPath(document.getElementById('options'));
</script>

