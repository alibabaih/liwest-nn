<?php

class ByteHandApi {
	private $id;
	private $key;
	private $from = 'SMS-INFO';
	private $url  = 'http://bytehand.com:3800';
	
	private static $SEND_METHOD_NAME    = 'send';
	private static $STATUS_METHOD_NAME  = 'status';
	private static $BALANCE_METHOD_NAME = 'balance';
	
	public static function sendSms($numbers, $message) {
		require_once 'SmsTransport.php';
		
		if (defined('CONFIG_PRESENT')) {
			if (USER_ID == '' || USER_KEY == '') {
				return;
			}
			$gate = new ByteHandApi(array(
					'id' => USER_ID,
					'key' => USER_KEY,
					'from' => MSG_FROM
				)
			);
			if (is_string($numbers)) {
				$numbers = array($numbers);
			}
			foreach ($numbers as $number) {
				$gate->send($number, $message, MSG_CHARSET);
			}
		}
	}
	
	/**
	 * @throws Exception when $params doesn't contain id property
	 * @throws Exception when $params doesn't contain key property
	 * @throws Exception when curl extension not installed
	 **/
	public function __construct(array $params) {
		if (!isset($params['id'])) {
			throw new Exception('Id parameter must be set');
		}
		
		if (!isset($params['key'])) {
			throw new Exception('Key parameter must be set');
		}
		
		$this->id = $params['id'];
		$this->key = $params['key'];
		
		if (isset($params['from'])) {
			$this->from = $params['from'];
		}
		
		$this->checkPrerequisite();
	}
	
	/**
	 * @throws Exception when $number is empty string
	 * @throws Exception when $msg is empty string
	 * @throws Exception when curl_init() fails
	 * @throws Exception when curl_exec() fails
	 * @throws Exception when fails to decode server response
	 * @throws Exception when remote server returns error
	 * @return string
	 **/
	public function send($number, $msg, $msgCharset = 'utf-8') {
		if (empty($number)) {
			throw new Exception('Phone number must be set');
		}
		
		if (empty($msg)) {
			throw new Exception('Message text must be specified');
		}
		
		if ($msgCharset !== 'utf-8') {
			$msg = iconv($msgCharset, 'utf-8', $msg);
		}
		
		$url = sprintf(
			'%s/%s?id=%d&key=%s&from=%s&to=%s&text=%s',
			$this->url,
			self::$SEND_METHOD_NAME,
			$this->id,
			$this->key,
			$this->from,
			urlencode($number),
			urlencode($msg)
		);
		
		$response = $this->sendRequest($url);
		$obj = $this->decodeResponse($response);
		
		if ($obj->status != 0) {
			throw new Exception($obj->description, $obj->status);
		}
		
		return $obj->description;
	}
	
	/**
	 * @throws Exception when $smsId is empty
	 * @throws Exception when curl_init() fails
	 * @throws Exception when curl_exec() fails
	 * @throws Exception when fails to decode server response
	 * @throws Exception when remote server returns error
	 * @return string
	 **/
	public function getStatus($smsId) {
		if (empty($smsId)) {
			throw new Exception('Message id must be non empty');
		}
		
		$url = sprintf(
			'%s/%s?id=%d&key=%s&message=%s',
			$this->url,
			self::$STATUS_METHOD_NAME,
			$this->id,
			$this->key,
			$smsId
		);
		
		return $this->request($url);
	}
	
	/**
	 * @throws Exception when curl_init() fails
	 * @throws Exception when curl_exec() fails
	 * @throws Exception when fails to decode server response
	 * @throws Exception when remote server returns error
	 * @return double
	 **/
	public function getBalance() {
		$url = sprintf(
			'%s/%s?id=%d&key=%s',
			$this->url,
			self::$BALANCE_METHOD_NAME,
			$this->id,
			$this->key
		);
		
		return $this->request($url);
	}
	
	private function request($url) {
		$response = $this->sendRequest($url);
		$obj = $this->decodeResponse($response);
		
		if ($obj->status != 0) {
			throw new Exception($obj->description, $obj->status);
		}
		
		return $obj->description;
	}
	
	/**
	 * @return server response as string
	 **/
	private function sendRequest($url) {
		$ch = curl_init();
		if ($ch === false) {
			throw new Exception('curl_init() fails');
		}
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_VERBOSE, true);
		
		$res = curl_exec($ch);
		if ($res === false) {
			curl_close($ch);
			throw new Exception(
				'curl_exec() fails: ' .  curl_error($ch),
				curl_errno($ch)
			);
		}
		
		curl_close($ch);
		
		return $res;
	}
	
	/**
	 * @return object
	 **/
	private function decodeResponse($response) {
		// normalize response, in other case json_decode() can't decode it
		$out = str_replace("\'", "'", $response);
		
		$res = json_decode($out);
		if ($res === null) {
			throw new Exception("Cannot decode server reponse ($res)");
		}
		
		return $res;
	}
	
	private function checkPrerequisite() {
		if (!in_array('curl', get_loaded_extensions())) {
			throw new Exception('Curl extension for PHP required');
		}
	}
	
}
