<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (!class_exists('vmPSPlugin'))
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVmPaymentQiwi extends vmPSPlugin {

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
		'qiwi_login' => array('', 'string'),
		'qiwi_password' => array('', 'string'),
		'qiwi_lifetime' => array(168, 'int'),
		'qiwi_agent' => array(1, 'int'),
	    'status_pending' => array('', 'char'),
	    'status_success' => array('', 'char'),
	    'status_canceled' => array('', 'char')
	    );

	    $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
	   // self::$_this = $this;

    }

    /**
     * Create the table for this plugin if it does not yet exist.
     * @author Valérie Isaksen
     */
    protected function getVmPluginCreateTableSQL() {
	return $this->createTableSQL('Payment Qiwi Table');
    }
    /**
     * Fields to create the payment table
     * @return string SQL Fileds
     */
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
	if(!$method->payment_currency)$this->getPaymentCurrency($method);
	// END printing out HTML Form code (Payment Extra Info)
	$q = 'SELECT `currency_code_3` FROM `#__virtuemart_currencies` WHERE `virtuemart_currency_id`="' . $method->payment_currency . '" ';
	$db = &JFactory::getDBO();
	$db->setQuery($q);
	$currency_code_3 = $db->loadResult();
	$paymentCurrency = CurrencyDisplay::getInstance($method->payment_currency);
	$totalInPaymentCurrency = round($paymentCurrency->convertCurrencyTo($method->payment_currency, $order['details']['BT']->order_total, false), 2);
	$cd = CurrencyDisplay::getInstance($cart->pricesCurrency);
	$virtuemart_order_id = VirtueMartModelOrders::getOrderIdByOrderNumber($order['details']['BT']->order_number);

	$address = ((isset($order['details']['ST'])) ? $order['details']['ST'] : $order['details']['BT']);
	
	$phone = $order['details']['BT']->phone_2;
	$repl = array(' ','-','(',')');
	$phone = str_replace($repl,"",$phone);
	if (!preg_match("#^(7|8|\\+7)+?(9|7){1}([0-9]{9})$#",$phone,$match)){
		JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT'),JText::_('VMPAYMENT_QIWI_PHONE_ERROR'));
	}
	$phone = $match[2].$match[3];
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
	$check_agt = $method->qiwi_agent?'true':'false';
	$html = '<form method="post" action="https://w.qiwi.ru/setInetBill_utf.do"  name="vm_qiwi_form">';
	$html .= "<input type='hidden' name='from' value='".$method->qiwi_login."' />";
	$html .= "<input type='hidden' name='to' value='$phone'>";
	$html .= "<input type='hidden' name='summ' value='$totalInPaymentCurrency'>";
	$html .= "<input type='hidden' name='com' value='Оплата за заказ № {$order['details']['BT']->order_number}. Спасибо за покупку! ' />";
	$html .= "<input type='hidden' name='lifetime' value='".$method->qiwi_lifetime."' />";
	$html .= "<input type='hidden' name='check_agt' value='$check_agt' />";
    $html .= "<input type='hidden' name='txn_id' value='$virtuemart_order_id'>";
    $html .= "<input type='submit' value='Оплатить через Киви' name='send'>
	</form>";
	$html.= ' <script type="text/javascript">';
	$html.= ' document.forms.vm_qiwi_form.submit();';
	$html.= ' </script>';
	return $this->processConfirmedOrderPaymentResponse(true, $cart, $order, $html,$this->renderPluginName($method, $order),'P');
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
    public function plgVmOnPaymentNotification() {
		return null;
    }


}


// No closing tag
