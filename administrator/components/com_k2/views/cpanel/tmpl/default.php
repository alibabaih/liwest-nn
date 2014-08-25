<?php
/**
 * @version		$Id: default.php 785 2011-04-28 12:39:17Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks, a business unit of Nuevvo Webware Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

?>

<!--[if lt IE 7]>
<div style="border:1px solid #F7941D;background:#FEEFDA;text-align:center;clear:both;height:75px;position:relative;margin-bottom:16px;">
  <div style="position:absolute;right:3px;top:3px;font-family:courier new;font-weight:bold;">
  	<a href="#" onclick="javascript:this.parentNode.parentNode.style.display='none';return false;"><img src="<?php echo JURI::base(true); ?>/components/com_k2/images/ie6nomore/ie6nomore-cornerx.jpg" style="border:none;" alt="<?php echo JText::_('K2_CLOSE_THIS_NOTICE'); ?>"/></a>
  </div>
  <div style="width:640px;margin:0 auto;text-align:left;padding:0;overflow:hidden;color:black;">
    <div style="width:75px;float:left;">
    	<img src="<?php echo JURI::base(true); ?>/components/com_k2/images/ie6nomore/ie6nomore-warning.jpg" alt="<?php echo JText::_('K2_WARNING'); ?>"/>
    </div>
    <div style="width:275px;float:left;font-family:Arial,sans-serif;">
      <div style="font-size:14px;font-weight:bold;margin-top:12px;">
      	<?php echo JText::_('K2_YOU_ARE_USING_AN_OUTDATED_BROWSER'); ?>
      </div>
      <div style="font-size:12px;margin-top:6px;line-height:12px;">
      	<?php echo JText::_('K2_FOR_A_BETTER_EXPERIENCE_USING_THIS_SITE_PLEASE_UPGRADE_TO_A_MODERN_WEB_BROWSER'); ?>
      </div>
    </div>
    <div style="width:75px;float:left;"><a href="http://www.firefox.com" target="_blank"><img src="<?php echo JURI::base(true); ?>/components/com_k2/images/ie6nomore/ie6nomore-firefox.jpg" style="border:none;" alt="<?php echo JText::_('K2_GET_FIREFOX_35'); ?>"/></a></div>
    <div style="width:75px;float:left;"><a href="http://www.browserforthebetter.com/download.html" target="_blank"><img src="<?php echo JURI::base(true); ?>/components/com_k2/images/ie6nomore/ie6nomore-ie8.jpg" style="border:none;" alt="<?php echo JText::_('K2_GET_INTERNET_EXPLORER_8'); ?>"/></a></div>
    <div style="width:73px;float:left;"><a href="http://www.apple.com/safari/download/" target="_blank"><img src="<?php echo JURI::base(true); ?>/components/com_k2/images/ie6nomore/ie6nomore-safari.jpg" style="border:none;" alt="<?php echo JText::_('K2_GET_SAFARI_4'); ?>"/></a></div>
    <div style="float:left;"><a href="http://www.google.com/chrome" target="_blank"><img src="<?php echo JURI::base(true); ?>/components/com_k2/images/ie6nomore/ie6nomore-chrome.jpg" style="border:none;" alt="<?php echo JText::_('K2_GET_GOOGLE_CHROME'); ?>"/></a></div>
  </div>
</div>
<![endif]-->

<script type="text/javascript">
	//<![CDATA[
	$K2(document).ready(function(){
		$K2('#K2ImportContentButton').click(function(event){
			var answer = confirm('<?php echo JText::_('K2_WARNING_YOU_ARE_ABOUT_TO_IMPORT_ALL_SECTIONS_CATEGORIES_AND_ARTICLES_FROM_JOOMLAS_CORE_CONTENT_COMPONENT_COM_CONTENT_INTO_K2_IF_THIS_IS_THE_FIRST_TIME_YOU_IMPORT_CONTENT_TO_K2_AND_YOUR_SITE_HAS_MORE_THAN_A_FEW_THOUSAND_ARTICLES_THE_PROCESS_MAY_TAKE_A_FEW_MINUTES_IF_YOU_HAVE_EXECUTED_THIS_OPERATION_BEFORE_DUPLICATE_CONTENT_MAY_BE_PRODUCED', true); ?>');
			if (!answer){
				event.preventDefault();
			}
		});
	});
	//]]>
</script>

<div id="cpanel" class="k2AdminCpanel">
	<?php echo $this->loadTemplate('quickicons'); ?>
	<div class="clr"></div>
</div>
<div id="k2AdminStats">
	<?php echo $this->loadTemplate('tabs'); ?>
	<div class="clr"></div>
</div>

<div class="clr"></div>
