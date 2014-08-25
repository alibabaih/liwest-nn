<?php

defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'cart.php';


class CartHelper {
	function __construct() {
		if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
		$this->cart = VirtueMartCart::getCart(false);
		JFactory::getLanguage()->load('com_virtuemart');
	}
	
	function assignValues() {
		$new=false;
		
		$this->cart->prepareAddressDataInCart("BT",$new);
		$this->BTaddress=$this->cart->BTaddress;
		$this->cart->prepareAddressDataInCart("ST",$new);
		$this->STaddress=$this->cart->STaddress;
		$this->lSelectShipment();
		$this->lSelectPayment();
	}
	
	public function getPrices() {
		$price=$this->cart->getCartPrices();
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php';
		$cdisp=CurrencyDisplay::getInstance();
		//echo "<pre>";print_r($this->cart);exit;
		
		foreach($price as $id=>$value) {
			if(!is_array($value)) {
				continue;
			}
			$nprice["products"][$id]["subtotal_tax_amount"]=!empty($price[$id]["subtotal_tax_amount"])?$cdisp->priceDisplay($price[$id]["subtotal_tax_amount"]):"";
			$nprice["products"][$id]["subtotal_discount"]=!empty($price[$id]["subtotal_discount"])?$cdisp->priceDisplay($price[$id]["subtotal_discount"]):"";
			if (VmConfig::get('checkout_show_origprice',1) && !empty($this->cart->pricesUnformatted[$id]['basePriceWithTax']) && $this->cart->pricesUnformatted[$id]['basePriceWithTax'] != $this->cart->pricesUnformatted[$id]['salesPrice'] ) {
				$nprice["products"][$id]["subtotal_with_tax"]='<span class="line-through">'.$cdisp->createPriceDiv('basePriceWithTax','', $this->cart->pricesUnformatted[$id],true,false,$this->cart->products[$id]->quantity).'</span><br />';
			}
			$nprice["products"][$id]["subtotal_with_tax"].=$cdisp->createPriceDiv('salesPrice','', $this->cart->pricesUnformatted[$id],false,false,$this->cart->products[$id]->quantity);	
		}
		
		$nprice["taxAmount"]=!empty($this->cart->pricesUnformatted["taxAmount"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["taxAmount"]):"";
		$nprice["discountAmount"]=!empty($this->cart->pricesUnformatted["discountAmount"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["discountAmount"]):"";
		$nprice["salesPrice"]=!empty($this->cart->pricesUnformatted["salesPrice"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["salesPrice"]):"";
		$nprice["shipmentTax"]=!empty($this->cart->pricesUnformatted["shipmentTax"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["shipmentTax"]):"";
		$nprice["salesPriceShipment"]=!empty($this->cart->pricesUnformatted["salesPriceShipment"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["salesPriceShipment"]):"";
		$nprice["paymentTax"]=!empty($this->cart->pricesUnformatted["paymentTax"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["paymentTax"]):"";
		$nprice["salesPricePayment"]=!empty($this->cart->pricesUnformatted["salesPricePayment"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["salesPricePayment"]):"";
		$nprice["billTaxAmount"]=!empty($this->cart->pricesUnformatted["billTaxAmount"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["billTaxAmount"]):"";
		$nprice["billDiscountAmount"]=!empty($this->cart->pricesUnformatted["billDiscountAmount"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["billDiscountAmount"]):"";	
		$nprice["billTotal"]=!empty($this->cart->pricesUnformatted["billTotal"])?$cdisp->priceDisplay($this->cart->pricesUnformatted["billTotal"]):"";
		
		/*$nprice["taxAmount"]=!empty($price["taxAmount"])?$cdisp->priceDisplay($price["taxAmount"]):"";
		$nprice["discountAmount"]=!empty($price["discountAmount"])?$cdisp->priceDisplay($price["discountAmount"]):"";
		$nprice["salesPrice"]=!empty($price["salesPrice"])?$cdisp->priceDisplay($price["salesPrice"]):"";
		$nprice["shipmentTax"]=!empty($price["shipmentTax"])?$cdisp->priceDisplay($price["shipmentTax"]):"";
		$nprice["salesPriceShipment"]=!empty($price["salesPriceShipment"])?$cdisp->priceDisplay($price["salesPriceShipment"]):"";
		$nprice["paymentTax"]=!empty($price["paymentTax"])?$cdisp->priceDisplay($price["paymentTax"]):"";
		$nprice["salesPricePayment"]=!empty($price["salesPricePayment"])?$cdisp->priceDisplay($price["salesPricePayment"]):"";
		$nprice["billTaxAmount"]=!empty($price["billTaxAmount"])?$cdisp->priceDisplay($price["billTaxAmount"]):"";
		$nprice["billDiscountAmount"]=!empty($price["billDiscountAmount"])?$cdisp->priceDisplay($price["billDiscountAmount"]):"";	
		$nprice["billTotal"]=!empty($price["billTotal"])?$cdisp->priceDisplay($price["billTotal"]):"";*/
		//echo "<pre>";print_r($nprice);exit;
		
		return $nprice;
	}
	
	public function lSelectShipment() {
		$found_shipment_method=false;
		$shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		$this->shipment_not_found_text=$shipment_not_found_text;

		$shipments_shipment_rates=array();
		if (!$this->checkShipmentMethodsConfigured()) {
			$this->shipments_shipment_rates=$shipments_shipment_rates;
			$this->found_shipment_method=$found_shipment_method;
			return;
		}
		$selectedShipment = (empty($this->cart->virtuemart_shipmentmethod_id) ? 0 : $this->cart->virtuemart_shipmentmethod_id);

		$shipments_shipment_rates = array();
		if (!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$dispatcher = JDispatcher::getInstance();
		// Call this to get new VirtueMart Prices - i dont kno why is in the cart old prices before this :O
		$this->cart->getCartPrices();
		//echo "<pre>";print_r($this->cart);exit;
		if(empty($this->cart->BT["zip"])) {
		    $this->cart->BT=array();
		    $this->cart->BT["zip"]="1";
		    //echo "<pre>";print_r($this->cart);exit;
		}
		if(is_array($this->cart->ST) && empty($this->cart->ST["zip"])) {
		    //$this->cart->ST=array();
		    $this->cart->ST["zip"]="1";
		}
		//echo "<pre>";print_r($this->cart);exit;
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEShipment', array( $this->cart, $selectedShipment, &$shipments_shipment_rates));
		// if no shipment rate defined
		$found_shipment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_shipment_method = true;
				break;
			}
		}
		$shipment_not_found_text = JText::_('COM_VIRTUEMART_CART_NO_SHIPPING_METHOD_PUBLIC');
		
		$shipments=array();
		foreach($shipments_shipment_rates as $items) {
			if(is_array($items)) {
				foreach($items as $item) {
					$shipments[]=$item;
				}
			} else {
				$shipments[]=$items;
			}
		}
		
		$this->shipment_not_found_text=$shipment_not_found_text;
		$this->shipments_shipment_rates=$shipments;
		$this->found_shipment_method=$found_shipment_method;
		return;
	}
	
	public function lSelectPayment() {
		$found_payment_method=false;
		$payment_not_found_text='';
		$payments_payment_rates=array();
		if (!$this->checkPaymentMethodsConfigured()) {
			$this->paymentplugins_payments=$payments_payment_rates;
			$this->found_payment_method=$found_payment_method;
		}

		$selectedPayment = empty($this->cart->virtuemart_paymentmethod_id) ? 0 : $this->cart->virtuemart_paymentmethod_id;

		$paymentplugins_payments = array();
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$dispatcher = JDispatcher::getInstance();
		// Call this to get new VirtueMart Prices - i dont kno why is in the cart old prices before this :O
		$this->cart->getCartPrices();
		$returnValues = $dispatcher->trigger('plgVmDisplayListFEPayment', array($this->cart, $selectedPayment, &$paymentplugins_payments));
		// if no payment defined
		$found_payment_method = false;
		foreach ($returnValues as $returnValue) {
			if($returnValue){
				$found_payment_method = true;
				break;
			}
		}

		if (!$found_payment_method) {
			$link=''; // todo
			$payment_not_found_text = JText::sprintf('COM_VIRTUEMART_CART_NO_PAYMENT_METHOD_PUBLIC', '<a href="'.$link.'">'.$link.'</a>');
		}
		
		$payments=array();
		foreach($paymentplugins_payments as $items) {
			if(is_array($items)) {
				foreach($items as $item) {
					$payments[]=$item;
				}
			} else {
				$payments[]=$items;
			}
		}
		$this->payment_not_found_text=$payment_not_found_text;
		$this->paymentplugins_payments=$payments;
		$this->found_payment_method=$found_payment_method;
	}
	
	private function checkPaymentMethodsConfigured() {

		//For the selection of the payment method we need the total amount to pay.
		$paymentModel = VmModel::getModel('Paymentmethod');
		$payments = $paymentModel->getPayments(true, false);
		if (empty($payments)) {

			$text = '';
			if (!class_exists('Permissions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) {
				$uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=paymentmethod';
				$text = JText::sprintf('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			}

			vmInfo('COM_VIRTUEMART_NO_PAYMENT_METHODS_CONFIGURED', $text);

			$tmp = 0;
			$this->found_payment_method=$tmp;

			return false;
		}
		return true;
	}
	
	private function checkShipmentMethodsConfigured() {

		//For the selection of the shipment method we need the total amount to pay.
		$shipmentModel = VmModel::getModel('Shipmentmethod');
		$shipments = $shipmentModel->getShipments();
		if (empty($shipments)) {

			$text = '';
			if (!class_exists('Permissions'))
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'permissions.php');
			if (Permissions::getInstance()->check("admin,storeadmin")) {
				$uri = JFactory::getURI();
				$link = $uri->root() . 'administrator/index.php?option=com_virtuemart&view=shipmentmethod';
				$text = JText::sprintf('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED_LINK', '<a href="' . $link . '">' . $link . '</a>');
			}

			vmInfo('COM_VIRTUEMART_NO_SHIPPING_METHODS_CONFIGURED', $text);

			$tmp = 0;
			$this->found_shipment_method=$tmp;

			return false;
		}
		return true;
	}
	
	function setPayment() {
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmpayment');
		$this->cart->setPaymentMethod(JRequest::getInt('virtuemart_paymentmethod_id'));
		$dispatcher=JDispatcher::getInstance();
		
		$msg="";
		$rets=$dispatcher->trigger('plgVmOnSelectCheckPayment',array($this->cart,&$msg));
		
		if(JRequest::getCmd('opc_task')!='set_checkout') {
			$db=JFactory::getDBO();
			$query=$db->getQuery(true);
			$query->select('payment_element');
			$query->from('#__virtuemart_paymentmethods');
			$query->where('virtuemart_paymentmethod_id='.JRequest::getInt('virtuemart_paymentmethod_id'));
			$db->setQuery($query);
			$method=$db->loadResult();
			
			if($method=='authorizenet') {
				$this->cart->setCartIntoSession();
				return true;
			}
		}
		
		foreach($rets as $ret) {
			if($ret===false) {
				$msgs=JFactory::getApplication()->getMessageQueue();
				$messages=array();
				foreach($msgs as $msg) {
					$messages[]=str_replace("<br/>","\n",$msg["message"]);
				}
				
				return $messages;
			}
		}
		$this->cart->setCartIntoSession();
		return true;
	}
	
	function setShipment() {
		if(!class_exists('vmPSPlugin')) require(JPATH_VM_PLUGINS.DS.'vmpsplugin.php');
		JPluginHelper::importPlugin('vmshipment');
		$this->cart->setShipment(JRequest::getInt('virtuemart_shipmentmethod_id'));
		$dispatcher=JDispatcher::getInstance();
		$rets=$dispatcher->trigger('plgVmOnSelectCheckShipment',array(&$this->cart));
		foreach($rets as $ret) {
			if($ret===false) {
				$msgs=JFactory::getApplication()->getMessageQueue();
				$messages=array();
				foreach($msgs as $msg) {
					$messages[]=str_replace("<br/>","\n",$msg["message"]);
				}
				
				return $messages;
			}
		}
		$this->cart->setCartIntoSession();
		return true;
	}
	
	function setAddress() {
		$post=JRequest::get('post');
		if(JRequest::getInt('STsameAsBT')=='1') {
			$this->cart->STsameAsBT=1;
			$this->cart->ST=0;
			$this->cart->setCartIntoSession();
		} else {
			$this->cart->STsameAsBT=0;
			if(!strlen($post['shipto_address_type_name'])) {
				$post['shipto_address_type_name']='ST';
			}
		}
		
		if($post['tosAccepted']) {
			$post['agreed']=1;
		} else {
			$post['agreed']=0;
		}
		if($this->cart->STsameAsBT==1) {
			$this->cart->saveAddressInCart($post,'BT');
		} else {
			$this->cart->saveAddressInCart($post,'ST');
			$this->cart->saveAddressInCart($post,'BT');
		}
	}
	
	function register() {
		$user=VmModel::getModel('user');
		$user->_id=0;
		$ret=$user->store(JRequest::get('post'));
		
		if(!isset($ret["success"]) || $ret["success"]==false || $ret==false) {
			$messages=array();
			foreach(JFactory::getApplication()->getMessageQueue() as $message) {
				$messages[]=$message["message"];
			} 
			return array('error'=>1,'message'=>implode(" ",$messages));
		}
		
		$messages=array();
		foreach(JFactory::getApplication()->getMessageQueue() as $message) {
			$messages[]=$message["message"];
		}
		/*if(count($messages)) {
			return array('error'=>1,'message'=>implode(" ",$messages));
		}*/
		
		/*define('JPATH_COMPONENT',JPATH_SITE.DS.'components'.DS.'com_virtuemart');
		require_once JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'controllers'.DS.'user.php';
		$user=new VirtueMartControllerUser();
		$ret=$user->saveData(false,true);*/
		return $ret;
	}
	
	function updateProduct() {
		$this->cart->updateProductCart(JRequest::getString('id'));
	}
	
	function removeProduct() {
		$this->cart->removeProductCart(JRequest::getString('id'));
	}
	
	function setCoupon() {
		$msg=$this->cart->setCouponCode(JRequest::getString('coupon'));
		$price=$this->cart->getCartPrices();
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php';
		$cdisp=CurrencyDisplay::getInstance();
		$nprice=array();
		
		$nprice["couponTax"]=!empty($price["couponTax"])?$cdisp->priceDisplay($price["couponTax"]):"";
		$nprice["salesPriceCoupon"]=!empty($price["salesPriceCoupon"])?$cdisp->priceDisplay($price["salesPriceCoupon"]):"";
		$nprice["billTaxAmount"]=!empty($price["billTaxAmount"])?$cdisp->priceDisplay($price["billTaxAmount"]):"";
		$nprice["billDiscountAmount"]=!empty($price["billDiscountAmount"])?$cdisp->priceDisplay($price["billDiscountAmount"]):"";	
		$nprice["billTotal"]=!empty($price["billTotal"])?$cdisp->priceDisplay($price["billTotal"]):"";
		if(strlen($msg)) {
			$lang=JFactory::getLanguage();
			$lang->load('com_virtuemart');
			return array('error'=>1,'message'=>JText::_($msg),'cart'=>$nprice);
		}
		return $nprice;
	}

}
?>
