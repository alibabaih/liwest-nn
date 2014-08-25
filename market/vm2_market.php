<?php
/*
 * version 2.4
 * author Bordyzhan Sergey (cmsdev.org)
*/

define('NAME', 'Ли Вест'); // название организации (не должно превышать 20 символов)
define('DESC', 'Ли Вест - это продукции традиционной китайской медицины на рынке продуктов здорового питания, лечебно-профилактической косметики, растительного сырья, акупунктурных игл и других инструментов для восточных методик оздоровления. '); // описание организации
define('CURRENCY', 'RUB'); // валюта магазина (RUB, USD, EUR, UAH, KZT)
define('DELIVERY', 'true'); // наличие доставки в магазине (true - есть, false - нет)
define('EXCLUDE_CAT', '16,17'); // id категорий которые нужно исключить из выгрузки, перечислить через запятую, например define('EXCLUDE_CAT', '2,8,54,5')
define('EXCLUDE_PROD', '0'); // id товаров которые нужно исключить из выгрузки, перечислить через запятую, например define('EXCLUDE_PROD', '2,8,54,5')
define('FILE', 0); // cоздать файл vm2_market.xml (define('FILE', 1)) или генерировать данные динамически (define('FILE', 0)), если define('FILE', 0), то в настройках якдеса нужно указать ссылку http://ваш_сайт/market/vm2_market.php, если define('FILE', 1), то http://ваш_сайт/market/vm2_market.xml, также, если define('FILE', 1), то после каждого обновления товаров в магазине, нужно в браузере набрать адрес http://ваш_сайт/market/vm2_market.php и запустить скрипт, чтоб сгенерировать файл vm2_market.xml
define('CONVERT_PRICE', 0); // 1 - конвертировать цены с одной валюты в другую, 0 – оставить цены как есть

define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
define('JPATH_BASE', str_replace('market', '', dirname(__FILE__)));

require_once(JPATH_BASE.DS.'includes'.DS.'defines.php');
require_once(JPATH_BASE.DS.'includes'.DS.'framework.php');

$app = JFactory::getApplication('site');
$app->initialise();

require(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
VmConfig::loadConfig();

$db = JFactory::getDBO();

$live_site = str_replace(array('market/', 'http://'), '', JURI::base());
$lang = VmConfig::get('vmlang', 'en_gb');

if (CONVERT_PRICE) {
	$converterFile = VmConfig::get('currency_converter_module');
	
	if (file_exists('..'.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'plugins'.DS.'currency_converter'.DS.$converterFile)) {
		$module_filename = substr($converterFile, 0, -4);
		require_once('..'.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'plugins'.DS.'currency_converter'.DS.$converterFile);
		
		if (class_exists($module_filename))
			$currencyConverter = new $module_filename();
	} else {
		if(!class_exists('convertECB'))
			require('..'.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'plugins'.DS.'currency_converter'.DS.'convertECB.php');
		
		$currencyConverter = new convertECB();
	}
}

if (!FILE) {
	ob_start('ob_gzhandler', 9);
	header('Content-Type: application/xml; charset=utf-8');
} else
	header('Content-Type: text/html; charset=UTF-8');

$xml = '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd"><yml_catalog date="'.date('Y-m-d H:i').'"><shop><name>'.htmlspecialchars(mb_substr(NAME, 0, 20, 'UTF-8')).'</name><company>'.htmlspecialchars(DESC).'</company><url>'.$live_site.'</url><currencies><currency id="'.CURRENCY.'" rate="1"/>';
$xml .= '<currency id="USD" rate="CBRF"/>'; //курс по Центральному банку РФ
//$xml .= '<currency id="EUR" rate="CBRF"/>'; //курс по Центральному банку РФ
//$xml .= '<currency id="UAH" rate="NBU"/>'; //курс по Национальному банку Украины
//$xml .= '<currency id="KZT" rate="NBK"/>'; //курс по Национальному банку Казахстана
//$xml .= '<currency id="KZT" rate="CB"/>'; //курс по банку той страны, к которой относится магазин по своему региону, указанному в партнерском интерфейсе
$xml .= '</currencies><categories>';

$query = 'SELECT a.category_parent_id, a.category_child_id, b.category_name FROM #__virtuemart_category_categories a RIGHT JOIN #__virtuemart_categories_'.$lang.' b ON b.virtuemart_category_id = a.category_child_id WHERE a.category_child_id NOT IN ('.EXCLUDE_CAT.') ORDER BY a.category_child_id';
$db->setQuery($query);
$rows = $db->loadObjectList();

$exclude_cat_arr = explode(',', EXCLUDE_CAT);

foreach ($rows as $row) { 
	$cat_parent_id = $row->category_parent_id;
	$cat_child_id = $row->category_child_id;
	$cat_name = htmlspecialchars(trim(strip_tags($row->category_name)));
	
	if ($cat_name == '')
		continue;
		
	if ($cat_parent_id == 0 || in_array($cat_parent_id, $exclude_cat_arr))
		$xml .= '<category id="'.$cat_child_id.'">'.$cat_name.'</category>';
	else	
		$xml .= '<category id="'.$cat_child_id.'" parentId="'.$cat_parent_id.'">'.$cat_name.'</category>';
}

$xml .= '</categories><offers>';

$query = 'SELECT a.virtuemart_product_id, b.product_name, b.slug, c.file_url, b.product_desc, d.product_price, d.product_override_price, d.product_currency, e.mf_name, f.virtuemart_manufacturer_id, g.virtuemart_category_id, k.currency_code_3 FROM (#__virtuemart_product_categories g LEFT JOIN (#__virtuemart_product_prices d RIGHT JOIN ((#__virtuemart_product_manufacturers f RIGHT JOIN #__virtuemart_products a ON f.virtuemart_product_id = a.virtuemart_product_id) LEFT JOIN #__virtuemart_manufacturers_'.$lang.' e ON f.virtuemart_manufacturer_id = e.virtuemart_manufacturer_id LEFT JOIN #__virtuemart_products_'.$lang.' b ON b.virtuemart_product_id = a.virtuemart_product_id LEFT JOIN #__virtuemart_product_medias h ON h.virtuemart_product_id = a.virtuemart_product_id LEFT JOIN #__virtuemart_medias c ON c.virtuemart_media_id = h.virtuemart_media_id) ON d.virtuemart_product_id = a.virtuemart_product_id) ON g.virtuemart_product_id = a.virtuemart_product_id) RIGHT JOIN #__virtuemart_currencies k ON k.virtuemart_currency_id = d.product_currency WHERE a.published = 1 AND d.product_price > 0 AND b.product_name <> \'\' AND g.virtuemart_category_id NOT IN ('.EXCLUDE_CAT.') AND a.virtuemart_product_id NOT IN ('.EXCLUDE_PROD.')';
$db->setQuery($query);
$rows = $db->loadObjectList();

$product_log = array();

foreach ($rows as $row) {

	if (!in_array($row->virtuemart_product_id, $product_log)) {
		
		$product_name = htmlspecialchars(trim(strip_tags($row->product_name)));
		
		if ($product_name == '')
			continue;
		
		$product_log[] = $product_id = $row->virtuemart_product_id;
		$product_cat_id = $row->virtuemart_category_id;
		
		if (CONVERT_PRICE) {
			$product_price = !(float)$row->product_override_price ? $row->product_price : $row->product_override_price;
			$product_price = sprintf('%.2f', $currencyConverter->convert($product_price, $row->currency_code_3, CURRENCY));
		} else
			$product_price = sprintf('%.2f', !(float)$row->product_override_price ? $row->product_price : $row->product_override_price);
		
		$type = $row->mf_name ? ' type="vendor.model"' : '';
		$url = 'http://'.str_replace(array('/market/', '//'), array('', '/'), $live_site.JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$product_id.'&virtuemart_category_id='.$product_cat_id));
			
		$xml .= '<offer'.$type.' id="'.$product_id.'" available="true"><url>'.$url.'</url><price>'.$product_price.'</price><currencyId>'.CURRENCY.'</currencyId><categoryId>'.$product_cat_id.'</categoryId>';
		
		if ($row->file_url)
			$xml .= '<picture>http://'.$live_site.htmlspecialchars(str_replace(' ', '%20', $row->file_url)).'</picture>';
		
		$xml .= '<delivery>'.DELIVERY.'</delivery>';
		
		if ($row->mf_name) {
			$xml .= '<vendor>'.htmlspecialchars($row->mf_name).'</vendor><model>'.$product_name.'</model>';
		} else
			$xml .= '<name>'.$product_name.'</name>';
		
		if ($row->product_desc)
			$xml .= '<description>'.htmlspecialchars(strip_tags($row->product_desc)).'</description>';
			
		$xml .= '</offer>';
	}
}

$xml .= '</offers></shop></yml_catalog>';

if (FILE) {
	$xml_file = fopen('vm2_market.xml', 'w+');
	
	if (!$xml_file)
		echo('Ошибка открытия файла');
	else {
		ftruncate($xml_file, 0);
		fputs($xml_file, $xml);
		
		echo('Файл создан, url - <a href="'.$live_site.'market/vm2_market.xml">'.$live_site.'market/vm2_market.xml</a>');
	}
		
	fclose($xml_file);
} else
	echo $xml;
?>