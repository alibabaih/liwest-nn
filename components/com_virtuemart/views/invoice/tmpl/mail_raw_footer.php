﻿<?php
/**
*
* Layout for the shopping cart, look in mailshopper for more details
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers, Valerie Isaksen
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
/* TODO Chnage the footer place in helper or assets ???*/
// if (empty($this->vendor)) {
// 		$vendorModel = VmModel::getModel('vendor');
// 		$this->vendor = $vendorModel->getVendor();
// }

//$link = JURI::root(). 'index.php?option=com_virtuemart' ;

echo "\n";
//$link= JHTML::_('link', $link, $this->vendor->vendor_name) ;

//	echo JText::_('COM_VIRTUEMART_MAIL_VENDOR_TITLE').$this->vendor->vendor_name.'<br/>';

/* GENERAL FOOTER FOR ALL MAILS */
	echo "<em>Cпасибо за покупку!</em><br />";
	echo "--<br />Ли Вест НН — традиционная китайская медицина,<br /> <a href=\"http://liwest-nn.ru\" target=\"_blank\">liwest-nn.ru</a>"
?>