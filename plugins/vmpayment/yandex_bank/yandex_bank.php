<?php

if (!defined('_VALID_MOS') && !defined('_JEXEC'))
    die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (!class_exists('vmPSPlugin'))
    require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');

class plgVmPaymentYandex_Bank extends vmPSPlugin {

    // instance of class
    public static $_this = false;

    function __construct(& $subject, $config) {
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
		'yandex_bank_name' => array('', 'string'),
		'yandex_bank_inn' => array('', 'string'),
		'yandex_bank_bik' => array('', 'string'),
		'yandex_bank_rs' => array('', 'string'),
		'yandex_bank_kpp' => array('', 'string'),
		'yandex_bank_kbk' => array('', 'string'),
		'yandex_bank_okato' => array('', 'string'),
		'yandex_bank_text' => array('', 'string'),
		'yandex_bank_label' => array('', 'string'),
		'yandex_bank_nds' => array('', 'string'),
		'yandex_bank_nds_sum' => array('', 'string')
	    );

	    $this->setConfigParameterable($this->_configTableFieldName, $varsToPush);

    }

    protected function getVmPluginCreateTableSQL() {
	return $this->createTableSQL('Payment Yandex Bank Table');
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
	    return null; 
	}
	if (!$this->selectedThisElement($method->payment_element)) {
	    return false;
	}
	$lang = JFactory::getLanguage();
	$filename = 'com_virtuemart';
	$lang->load($filename, JPATH_ADMINISTRATOR);
	$vendorId = 0;
	
	
	$session = JFactory::getSession();
	$return_context = $session->getId();
	$this->logInfo('plgVmConfirmedOrder order number: ' . $order['details']['BT']->order_number, 'message');


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

	$nds = ($method->yandex_bank_nds)?'&amp;checkbox-nds=on':'';
	
	$html = '<iframe allowtransparency="true" src="https://money.yandex.ru/embed/bank.xml?payer-type=entrep&amp;recipient-name='.urlencode($method->yandex_bank_name).
		'&amp;recipient-inn='.$method->yandex_bank_inn.'&amp;recipient-kpp='.$method->yandex_bank_kpp.
		'&amp;recipient-bik='.$method->yandex_bank_bik.'&amp;recipient-account='.$method->yandex_bank_rs.
		'&amp;recipient-kbk='.$method->yandex_bank_kbk.'&amp;recipient-okato='.$method->yandex_bank_okato.
		'&amp;writer=seller&amp;targets='.urlencode(str_ireplace(array('%id%','%number%','%date%'),array($virtuemart_order_id,
			$order['details']['BT']->order_number,
			JHtml::_('date','now','d.m.Y')),
			$method->yandex_bank_text)).'&amp;default-sum='.$totalInPaymentCurrency.'&amp;nds-rate='.$method->yandex_bank_nds_sum.
			'&amp;button-text='.$method->yandex_bank_label.'" frameborder="0" height="166" scrolling="no" width="490"></iframe>';

		return $this->processConfirmedOrderPaymentResponse(true, $cart, $order, $html,$this->renderPluginName($method, $order),'P');
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

    /**
     * Check if the payment conditions are fulfilled for this payment method
     * @author: Valerie Isaksen
     *
     * @param $cart_prices: cart prices
     * @param $payment
     * @return true: if the conditions are fulfilled, false otherwise
     *
     */
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

    /*
     * We must reimplement this triggers for joomla 1.7
     */

    /**
     * Create the table for this plugin if it does not yet exist.
     * This functions checks if the called plugin is active one.
     * When yes it is calling the standard method to create the tables
     * @author Valérie Isaksen
     *
     */
    function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
	return $this->onStoreInstallPluginTable($jplugin_id);
    }

    /**
     * This event is fired after the payment method has been selected. It can be used to store
     * additional payment info in the cart.
     *
     * @author Max Milbers
     * @author Valérie isaksen
     *
     * @param VirtueMartCart $cart: the actual cart
     * @return null if the payment was not selected, true if the data is valid, error message if the data is not vlaid
     *
     */
    public function plgVmOnSelectCheckPayment(VirtueMartCart $cart) {
	return $this->OnSelectCheck($cart);
    }

    /**
     * plgVmDisplayListFEPayment
     * This event is fired to display the pluginmethods in the cart (edit shipment/payment) for exampel
     *
     * @param object $cart Cart object
     * @param integer $selected ID of the method selected
     * @return boolean True on succes, false on failures, null when this plugin was not selected.
     * On errors, JError::raiseWarning (or JError::raiseError) must be used to set a message.
     *
     * @author Valerie Isaksen
     * @author Max Milbers
     */
    public function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {
	return $this->displayListFE($cart, $selected, $htmlIn);
    }

    /*
     * plgVmonSelectedCalculatePricePayment
     * Calculate the price (value, tax_id) of the selected method
     * It is called by the calculator
     * This function does NOT to be reimplemented. If not reimplemented, then the default values from this function are taken.
     * @author Valerie Isaksen
     * @cart: VirtueMartCart the current cart
     * @cart_prices: array the new cart prices
     * @return null if the method was not selected, false if the shiiping rate is not valid any more, true otherwise
     *
     *
     */

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

    /**
     * plgVmOnCheckAutomaticSelectedPayment
     * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
     * The plugin must check first if it is the correct type
     * @author Valerie Isaksen
     * @param VirtueMartCart cart: the cart object
     * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
     *
     */
    function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array()) {
	return $this->onCheckAutomaticSelected($cart, $cart_prices);
    }

    /**
     * This method is fired when showing the order details in the frontend.
     * It displays the method-specific data.
     *
     * @param integer $order_id The order ID
     * @return mixed Null for methods that aren't active, text (HTML) otherwise
     * @author Max Milbers
     * @author Valerie Isaksen
     */
    public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {
	$this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
    }

    /**
     * This event is fired during the checkout process. It can be used to validate the
     * method data as entered by the user.
     *
     * @return boolean True when the data was valid, false otherwise. If the plugin is not activated, it should return null.
     * @author Max Milbers

      public function plgVmOnCheckoutCheckDataPayment(  VirtueMartCart $cart) {
      return null;
      }
     */

    /**
     * This method is fired when showing when priting an Order
     * It displays the the payment method-specific data.
     *
     * @param integer $_virtuemart_order_id The order ID
     * @param integer $method_id  method used for this order
     * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
     * @author Valerie Isaksen
     */
    function plgVmonShowOrderPrintPayment($order_number, $method_id) {
	return $this->onShowOrderPrint($order_number, $method_id);
    }

    function plgVmDeclarePluginParamsPayment($name, $id, &$data) {
	return $this->declarePluginParams('payment', $name, $id, $data);
    }

    function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {
	return $this->setOnTablePluginParams($name, $id, $table);
    }

    //Notice: We only need to add the events, which should work for the specific plugin, when an event is doing nothing, it should not be added

    /**
     * Save updated order data to the method specific table
     *
     * @param array $_formData Form data
     * @return mixed, True on success, false on failures (the rest of the save-process will be
     * skipped!), or null when this method is not actived.
     * @author Oscar van Eijk
     *
      public function plgVmOnUpdateOrderPayment(  $_formData) {
      return null;
      }

      /**
     * Save updated orderline data to the method specific table
     *
     * @param array $_formData Form data
     * @return mixed, True on success, false on failures (the rest of the save-process will be
     * skipped!), or null when this method is not actived.
     * @author Oscar van Eijk
     *
      public function plgVmOnUpdateOrderLine(  $_formData) {
      return null;
      }

      /**
     * plgVmOnEditOrderLineBE
     * This method is fired when editing the order line details in the backend.
     * It can be used to add line specific package codes
     *
     * @param integer $_orderId The order ID
     * @param integer $_lineId
     * @return mixed Null for method that aren't active, text (HTML) otherwise
     * @author Oscar van Eijk
     *
      public function plgVmOnEditOrderLineBEPayment(  $_orderId, $_lineId) {
      return null;
      }

      /**
     * This method is fired when showing the order details in the frontend, for every orderline.
     * It can be used to display line specific package codes, e.g. with a link to external tracking and
     * tracing systems
     *
     * @param integer $_orderId The order ID
     * @param integer $_lineId
     * @return mixed Null for method that aren't active, text (HTML) otherwise
     * @author Oscar van Eijk
     *
      public function plgVmOnShowOrderLineFE(  $_orderId, $_lineId) {
      return null;
      }

      /**
     * This event is fired when the  method notifies you when an event occurs that affects the order.
     * Typically,  the events  represents for payment authorizations, Fraud Management Filter actions and other actions,
     * such as refunds, disputes, and chargebacks.
     *
     * NOTE for Plugin developers:
     *  If the plugin is NOT actually executed (not the selected payment method), this method must return NULL
     *
     * @param $return_context: it was given and sent in the payment form. The notification should return it back.
     * Used to know which cart should be emptied, in case it is still in the session.
     * @param int $virtuemart_order_id : payment  order id
     * @param char $new_status : new_status for this order id.
     * @return mixed Null when this method was not selected, otherwise the true or false
     *
     * @author Valerie Isaksen
     *
     **/
     
    public function plgVmOnPaymentNotification() {
      return null;
    }

      /**
     * plgVmOnPaymentResponseReceived
     * This event is fired when the  method returns to the shop after the transaction
     *
     *  the method itself should send in the URL the parameters needed
     * NOTE for Plugin developers:
     *  If the plugin is NOT actually executed (not the selected payment method), this method must return NULL
     *
     * @param int $virtuemart_order_id : should return the virtuemart_order_id
     * @param text $html: the html to display
     * @return mixed Null when this method was not selected, otherwise the true or false
     *
     * @author Valerie Isaksen
     *
     *
      function plgVmOnPaymentResponseReceived(, &$virtuemart_order_id, &$html) {
      return null;
      }
     */
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
}
// No closing tag
