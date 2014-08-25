<?php
/**
 * @version		$Id$
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks, a business unit of Nuevvo Webware Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');?>

<?php jimport('joomla.html.pane'); $pane =& JPane::getInstance('Tabs'); ?>

<?php echo $pane->startPane('myPane'); ?>

    <?php echo $pane->startPanel(JText::_('K2_ABOUT'), 'aboutK2Tab');?>
    	<!--[if lte IE 7]>
    	<br class="ie7fix" />
    	<![endif]-->
    	<?php echo JString::str_ireplace('"_QQ_"', '"', JText::_('K2_ABOUT_TEXT')); ?>
	<?php echo $pane->endPanel(); ?>
	
	<?php echo $pane->startPanel(JText::_('K2_CREDITS'), 'creditsTab'); ?>
        <!--[if lte IE 7]>
        <br class="ie7fix" />
        <![endif]-->
        <table class="adminlist">
        <thead>
          <tr>
            <td class="title"></td>
            <td class="title"><?php echo JText::_('K2_VERSION'); ?></td>
            <td class="title"><?php echo JText::_('K2_TYPE'); ?></td>
            <td class="title"><?php echo JText::_('K2_LICENSE'); ?></td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><a target="_blank" href="http://nuovext.pwsp.net/">NuoveXT</a></td>
            <td>2.2</td>
            <td><?php echo JText::_('K2_ICONS'); ?></td>
            <td><?php echo JText::_('K2_GNUGPL'); ?></td>
          </tr>
          <tr>
            <td><a target="_blank" href="http://blog.tpdkdesign.net/2006/05/01/choose-your-sport/">tpdkdesign.net</a></td>
            <td><?php echo JText::_('K2_NA'); ?></td>
            <td><?php echo JText::_('K2_ICONS'); ?></td>
            <td><?php echo JText::_('K2_CREATIVE_COMMONS_ATTRIBUTION_NONCOMMERCIAL_NO_DERIVATIVE_WORKS_30'); ?></td>
          </tr>
          
          <tr>
            <td><a target="_blank" href="http://www.komodomedia.com/">Komodo Media</a></td>
            <td><?php echo JText::_('K2_NA'); ?></td>
            <td><?php echo JText::_('K2_ICONS'); ?></td>
            <td><?php echo JText::_('K2_CREATIVE_COMMONS_ATTRIBUTIONSHARE_ALIKE_30_UNPORTED_LICENSE'); ?></td>
          </tr>
          <tr>
            <td><a target="_blank" href="http://p.yusukekamiyamane.com/">Fugue by Yusuke Kamiyamane</a></td>
            <td>2.9.4</td>
            <td><?php echo JText::_('K2_ICONS'); ?></td>
            <td><?php echo JText::_('K2_CREATIVE_COMMONS_ATTRIBUTION_30_LICENSE'); ?></td>
          </tr>
          <tr>
            <td><a target="_blank" href="http://pear.php.net/package/Services_JSON/">Services_JSON</a></td>
            <td>1.0.1</td>
            <td><?php echo JText::_('K2_PHP_CLASS'); ?></td>
            <td><?php echo JText::_('K2_BSD'); ?></td>
          </tr>
          <tr>
            <td><a target="_blank" href="http://www.verot.net/php_class_upload.htm">class.upload.php</a></td>
            <td>0.30</td>
            <td><?php echo JText::_('K2_PHP_CLASS'); ?></td>
            <td><?php echo JText::_('K2_GNUGPL'); ?></td>
          </tr>
          <tr>
            <td><a target="_blank" href="http://komra.de/labs/simpletabs/">SimpleTabs</a></td>
            <td>1.3</td>
            <td><?php echo JText::_('K2_TABS_SCRIPT'); ?></td>
            <td><?php echo JText::_('K2_GNUGPL'); ?></td>
          </tr>
          <tr>
            <td><a target="_blank" href="http://digitarald.de/project/autocompleter/">Autocompleter (modified by JoomlaWorks)</a></td>
            <td>1.0rc4</td>
            <td><?php echo JText::_('K2_AUTOCOMPLETER_SCRIPT'); ?></td>
            <td><?php echo JText::_('K2_MIT'); ?></td>
          </tr>
        </tbody>
        </table>
	<?php echo $pane->endPanel(); ?>
<?php echo $pane->endPane(); ?>