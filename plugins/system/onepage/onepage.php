<?php
/**
*
* One Page Checkout plugin for VirtueMart 2
* @author LineLab
*
* @link http://www.linelab.org
* @copyright Copyright (c) 2011 - 2012 Linelab.org Team. All rights reserved.
* @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL3
* @version $Id: onepage.php 2.0. 2012-06-03
* @technical Support: http://www.linelab.org/download/joomla-templates-forum
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemOnePage extends JPlugin {
	function __construct($config,$params) {
		parent::__construct($config,$params);
	}
	
	function onAfterRoute() {
		if(JFactory::getApplication()->isAdmin()) {
			return;
		}
		
		if(JRequest::getCmd('type')=='onepage') {
			define('JPATH_COMPONENT',JPATH_SITE.DS.'components'.DS.'com_virtuemart');
			require_once JPATH_SITE.DS.'templates'.DS.JFactory::getApplication()->getTemplate().DS.'html'.DS.'com_virtuemart'.DS.'cart'.DS.'helper.php';
			$helper=new CartHelper();
			switch(JRequest::getCmd('opc_task')) {
				case 'set_coupon':
					$ret=$helper->setCoupon();
					echo json_encode($ret);
					break;
				case 'update_form':
					if(JRequest::getInt('update_address',1)==1) {
						$helper->setAddress();
					}
					$ret=$helper->setPayment();
					if(is_array($ret)) {
						echo json_encode(array('error'=>1,'message'=>implode($ret)));
						break;
					} 
					$ret=$helper->setShipment();
					if(is_array($ret)) {
						echo json_encode(array('error'=>1,'message'=>implode($ret)));
						break;
					}
					$helper->lSelectShipment();
					$helper->lSelectPayment();
					$data=array();
					$data["shipments"]=$helper->shipments_shipment_rates;
					$data["payments"]=$helper->paymentplugins_payments;
					$data["price"]=$helper->getPrices();
					echo json_encode($data);
					break;
				case 'update_product':
					$helper->setAddress();
					$helper->updateProduct();
					$helper->lSelectShipment();
					$helper->lSelectPayment();
					$data=array();
					$data["shipments"]=$helper->shipments_shipment_rates;
					$data["payments"]=$helper->paymentplugins_payments;
					$data["price"]=$helper->getPrices();
					echo json_encode($data);
					break;
				case 'remove_product':
					$helper->setAddress();
					$helper->removeProduct();
					$helper->lSelectShipment();
					$helper->lSelectPayment();
					$data=array();
					$data["shipments"]=$helper->shipments_shipment_rates;
					$data["payments"]=$helper->paymentplugins_payments;
					$data["price"]=$helper->getPrices();
					echo json_encode($data);
					break;
				case 'register':
					$ret=$helper->register();
					echo json_encode($ret);
					break;
				case 'set_checkout':
					$helper->setAddress();
					$helper->updateProduct();
					$ret=$helper->setPayment();
					$ret=$helper->setShipment();
					echo json_encode(array());
					break;
				
				
				
			}
			JFactory::getApplication()->close();
		}
	}
}
?>
