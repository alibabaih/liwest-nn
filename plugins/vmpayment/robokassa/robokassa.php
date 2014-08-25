<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (!class_exists('vmPSPlugin'))
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVmPaymentRobokassa extends vmPSPlugin {

    // instance of class
    public static $_this = false;

    function __construct(& $subject, $config) {
	//if (self::$_this)
	 //   return self::$_this;
	parent::__construct($subject, $config);

	    $this->_loggable = true;
	    $this->tableFields = array_keys($this->getTableSQLFields());
	    $varsToPush = array('payment_logos' => array('', 'char'),
		'countries' => array(0, 'int'),
		'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\' ',
		'payment_currency' =>  array(0, 'int'),
		'min_amount' => array(0, 'int'),
		'max_amount' => array(0, 'int'),
		'cost_per_transaction' => array(0, 'int'),
		'cost_percent_total' => array(0, 'int'),
		'tax_id' => array(0, 'int'),
		'robokassa_login' => array('', 'string'),
		'robokassa_password1' => array('', 'string'),
		'robokassa_password2' => array('', 'string'),
		'robokassa_demo' => array(1, 'int'),
	    'status_success' => array('', 'char'),
	    );

	    $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
	   // self::$_this = $this;

    }

    protected function getVmPluginCreateTableSQL() {
	return $this->createTableSQL('Payment Robokassa Table');
    }

    function getTableSQLFields() {
	$SQLfields = array(
	    'id' => 'tinyint(1) unsigned NOT NULL AUTO_INCREMENT',
	    'virtuemart_order_id' => 'int(11) UNSIGNED DEFAULT NULL',
	    'order_number' => 'char(32) DEFAULT NULL',
	    'virtuemart_paymentmethod_id' => 'mediumint(1) UNSIGNED DEFAULT NULL',
	    'payment_name' => 'char(255) NOT NULL DEFAULT \'\' ',
	    'payment_order_total' => 'decimal(15,5) NOT NULL DEFAULT \'0.00000\' ',
	    'payment_currency' => 'char(3) ',
	    'cost_per_transaction' => ' decimal(10,2) DEFAULT NULL ',
	    'cost_percent_total' => ' decimal(10,2) DEFAULT NULL ',
	    'tax_id' => 'smallint(11) DEFAULT NULL'
	);

	return $SQLfields;
    }

    function plgVmConfirmedOrder($cart, $order) {

	if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
	    return null; // Another method was selected, do nothing
	}
	if (!$this->selectedThisElement($method->payment_element)) {
	    return false;
	}
	//$params = new JParameter($payment->payment_params);
	$lang = JFactory::getLanguage();
	$filename = 'com_virtuemart';
	$lang->load($filename, JPATH_ADMINISTRATOR);
	$vendorId = 0;
	
	
	$session = JFactory::getSession();
	$return_context = $session->getId();
	//$this->_debug = $method->debug;
	$this->logInfo('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');

	$html = "";

	if (!class_exists('VirtueMartModelOrders'))
	    require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
	$this->getPaymentCurrency($method);
	// END printing out HTML Form code (Payment Extra Info)
	$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
	$db = &JFactory::getDBO();
	$db->setQuery($q);
	$currency_code_3 = $db->loadResult();
	$paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
	$totalInPaymentCurrency = number_format($paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total, false), 2,'.','');
	$cd = CurrencyDisplay::getInstance($cart->pricesCurrency);
	$virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order['details']['BT']->order_number);

	
	$this->_virtuemart_paymentmethod_id = $order['details']['BT']->virtuemart_paymentmethod_id;
	$dbValues['payment_name'] = $this->renderPluginName($method);
	$dbValues['order_number'] = $order['details']['BT']->order_number;
	$dbValues['virtuemart_paymentmethod_id'] = $this->_virtuemart_paymentmethod_id;
	$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
	$dbValues['cost_percent_total'] = $method->cost_percent_total;
	$dbValues['payment_currency'] = $currency_code_3 ;
	$dbValues['payment_order_total'] = $totalInPaymentCurrency;
	$dbValues['tax_id'] = $method->tax_id;
	$this->storePSPluginInternalData($dbValues);
	$sig = md5("{$method->robokassa_login}:$totalInPaymentCurrency:$virtuemart_order_id:{$method->robokassa_password1}:SHPCONTEXT=$return_context:SHPON={$order['details']['BT']->order_number}:SHPPM={$order['details']['BT']->virtuemart_paymentmethod_id}");
	if($method->robokassa_demo==0){
		$url = 'https://merchant.roboxchange.com/Index.aspx';
	} else {
		$url = 'http://test.robokassa.ru/Index.aspx';
		//$url = 'http://localhost.ru/tmp/robo/index.php';
	}
	$html = '<form method="post" action="'.$url.'" name="vm_robokassa_form">';
	$html .= "<input type='hidden' name='MrchLogin' value='".$method->robokassa_login."' />";
	$html .= "<input type='hidden' name='OutSum' value='$totalInPaymentCurrency'>";
	$html .= "<input type='hidden' name='Desc' value='Оплата за заказ № {$order['details']['BT']->order_number}. Спасибо за покупку! ' />";
    $html .= "<input type='hidden' name='InvId' value='$virtuemart_order_id'>";
	$html .= "<input type='hidden' name='SignatureValue' value='$sig'>";
	$html .= "<input type='hidden' name='SHPCONTEXT' value='$return_context'>";
	$html .= "<input type='hidden' name='SHPON' value='{$order['details']['BT']->order_number}'>";
	$html .= "<input type='hidden' name='SHPPM' value='{$order['details']['BT']->virtuemart_paymentmethod_id}'>";
	$html .= "</form>";
	$html.= ' <script type="text/javascript">';
	$html.= ' document.forms.vm_robokassa_form.submit();';
	$html.= ' </script>';
	return $this->processConfirmedOrderPaymentResponse(2, $cart, $order, $html,$this->renderPluginName($method, $order),'P');
// 		return true;  // empty cart, send order
    }

    /**
     * Display stored payment data for an order
     *
     */
    function plgVmOnShowOrderBEPayment($virtuemart_order_id, $virtuemart_payment_id) {
	if (!$this->selectedThisByMethodId($virtuemart_payment_id)) {
	    return null; // Another method was selected, do nothing
	}

	$db = JFactory::getDBO();
	$q = 'SELECT * FROM `' . $this->_tablename . '` '
		. 'WHERE `virtuemart_order_id` = ' . $virtuemart_order_id;
	$db->setQuery($q);
	if (!($paymentTable = $db->loadObject())) {
	    vmWarn(500, $q . " " . $db->getErrorMsg());
	    return '';
	}
	$this->getPaymentCurrency($paymentTable);

	$html = '<table class="adminlist">' . "\n";
	$html .=$this->getHtmlHeaderBE();
	$html .= $this->getHtmlRowBE('STANDARD_PAYMENT_NAME', $paymentTable->payment_name);
	$html .= $this->getHtmlRowBE('STANDARD_PAYMENT_TOTAL_CURRENCY', $paymentTable->payment_order_total.' '.$paymentTable->payment_currency);
	$html .= '</table>' . "\n";
	return $html;
    }

    function getCosts(VirtueMartCart $cart, $method, $cart_prices) {
	if (preg_match('/%$/', $method->cost_percent_total)) {
	    $cost_percent_total = substr($method->cost_percent_total, 0, -1);
	} else {
	    $cost_percent_total = $method->cost_percent_total;
	}
	return ($method->cost_per_transaction + ($cart_prices['salesPrice'] * $cost_percent_total * 0.01));
    }

    protected function checkConditions($cart, $method, $cart_prices) {

// 		$params = new JParameter($payment->payment_params);
	$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

	$amount = $cart_prices['salesPrice'];
	$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
		OR
		($method->min_amount <= $amount AND ($method->max_amount == 0) ));
	if (!$amount_cond) {
	    return false;
	}
	$countries = array();
	if (!empty($method->countries)) {
	    if (!is_array($method->countries)) {
		$countries[0] = $method->countries;
	    } else {
		$countries = $method->countries;
	    }
	}

	// probably did not gave his BT:ST address
	if (!is_array($address)) {
	    $address = array();
	    $address['virtuemart_country_id'] = 0;
	}

	if (!isset($address['virtuemart_country_id']))
	    $address['virtuemart_country_id'] = 0;
	if (count($countries) == 0 || in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
	    return true;
	}

	return false;
    }

    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
	return $this->onStoreInstallPluginTable($jplugin_id);
    }

    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart) {
	return $this->OnSelectCheck($cart);
    }

    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
	return $this->displayListFE($cart, $selected, $htmlIn);
    }


    public function plgVmonSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {
	return $this->onSelectedCalculatePrice($cart, $cart_prices, $cart_prices_name);
    }

    function plgVmgetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

	if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
	    return null; // Another method was selected, do nothing
	}
	if (!$this->selectedThisElement($method->payment_element)) {
	    return false;
	}
	 $this->getPaymentCurrency($method);

	$paymentCurrencyId = $method->payment_currency;
    }

    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array()) {
	return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }

    public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
	$this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
    }

    function plgVmonShowOrderPrintPayment($order_number, $method_id) {
	return $this->onShowOrderPrint($order_number, $method_id);
    }

    function plgVmDeclarePluginParamsPayment($name, $id, &$data) {
	return $this->declarePluginParams('payment', $name, $id, $data);
    }

    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {
	return $this->setOnTablePluginParams($name, $id, $table);
    }

     
    public function plgVmOnPaymentNotification() {
		if (JRequest::getVar('pelement')!='robokassa'){
			return null;
		}
		if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
		$orderid = JRequest::getInt('InvId',0);
		$postprice = JRequest::getVar('OutSum');
		$return_context = JRequest::getVar('SHPCONTEXT');
		$payment = $this->getDataByOrderId($orderid);
		$method = $this->getVmPluginMethod($payment->virtuemart_paymentmethod_id);
        $order_model = new VirtueMartModelOrders();
        $order_info = $order_model->getOrder($orderid);
        $order_number = $order_info['details']['BT']->order_number;
        $this->getPaymentCurrency($method);
        // END printing out HTML Form code (Payment Extra Info)
        $q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
        $db = &JFactory::getDBO();
        $db->setQuery($q);
        $currency_code_3 = $db->loadResult();
		if (!class_exists('CurrencyDisplay')
		)require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
        $paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
        $totalInPaymentCurrency = round($paymentCurrency->convertCurrencyTo($method->payment_currency, $order_info['details']['BT']->order_total, false), 2);
		$string = "{$postprice}:{$orderid}:".$method->robokassa_password2.":SHPCONTEXT=$return_context:SHPON=".JRequest::getVar('SHPON').':SHPPM='.JRequest::getVar('SHPPM');
		$sig = strtoupper(md5($string));
		if ($sig == strtoupper(JRequest::getVar('SignatureValue'))&&$totalInPaymentCurrency == floatval($postprice)) {

			$order['order_status'] = $method->status_success;
			$order['virtuemart_order_id'] = $orderid;
			$order['customer_notified'] = 0;
			$order['comments'] = JTExt::sprintf('VMPAYMENT_ROBOKASSA_PAYMENT_CONFIRMED', $order_number);
			if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
			$modelOrder = new VirtueMartModelOrders();
			ob_start();
			$modelOrder->updateStatusForOneOrder($orderid, $order, true);
		
			//$this->notifyCustomer($order,$order_info);
			$this->emptyCart($return_context);
			ob_end_clean();
			echo 'OK'.$orderid;
			return true;
		}
		echo 'FAIL';
		return null;
    }

    function plgVmOnPaymentResponseReceived(  &$html) {

// the payment itself should send the parameter needed.
		$virtuemart_paymentmethod_id = JRequest::getInt('pm', 0);

		$vendorId = 0;
		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return null; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($method->payment_element)) {
			return false;
		}

		if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );

		$order_number = JRequest::getVar('on');
		$virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order_number);
		$payment_name = $this->renderPluginName($method);
		$html = '<table>' . "\n";
		$html .= $this->getHtmlRow('ROBOKASSA_PAYMENT_NAME', $payment_name);
		$html .= $this->getHtmlRow('ROBOKASSA_ORDER_NUMBER', $virtuemart_order_id);
		$html .= $this->getHtmlRow('ROBOKASSA_STATUS', JText::_('VMPAYMENT_ROBOKASSA_STATUS_SUCCESS'));

		$html .= '</table>' . "\n";

		if ($virtuemart_order_id) {
			if (!class_exists('VirtueMartCart'))
				require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
			// get the correct cart / session
			$cart = VirtueMartCart::getCart();

			// send the email ONLY if payment has been accepted
			/*if (!class_exists('VirtueMartModelOrders'))
				require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );
			$order = new VirtueMartModelOrders();
			$orderitems = $order->getOrder($virtuemart_order_id);
			//vmdebug('PaymentResponseReceived CART', $orderitems);
			$orderitems['details']['BT'] = (array)$orderitems['details']['BT'];
			$cart->sentOrderConfirmedEmail($orderitems);*/
			//We delete the old stuff

			$cart->emptyCart();
		}

		return true;
    }

    function plgVmOnUserPaymentCancel() {

		if (!class_exists('VirtueMartModelOrders'))
			require( JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orders.php' );

		$order_number = JRequest::getVar('on');
		if (!$order_number)
			return false;
		$db = JFactory::getDBO();
		$query = 'SELECT ' . $this->_tablename . '.`virtuemart_order_id` FROM ' . $this->_tablename. " WHERE  `order_number`= '" . $order_number . "'";

		$db->setQuery($query);
		$virtuemart_order_id = $db->loadResult();

		if (!$virtuemart_order_id) {
			return null;
		}
		$this->handlePaymentUserCancel($virtuemart_order_id);

		//JRequest::setVar('paymentResponse', $returnValue);
		return true;
    }

    protected function displayLogos($logo_list) {

	$img = "";

	if (!(empty($logo_list))) {
	    $url = JURI::root() . str_replace(JPATH_ROOT,'',dirname(__FILE__)).'/';
	    if (!is_array($logo_list))
		$logo_list = (array) $logo_list;
	    foreach ($logo_list as $logo) {
		$alt_text = substr($logo, 0, strpos($logo, '.'));
		$img .= '<img align="middle" src="' . $url . $logo . '"  alt="' . $alt_text . '" /> ';
	    }
	}
	return $img;
    }


	private function notifyCustomer($order, $order_info ) {
					if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
					shopFunctionsF::sentOrderConfirmedEmail($order_info);
	}
}

// No closing tag
