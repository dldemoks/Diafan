<?php
/**
 * Настройка Payeer для администратора
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    5.4
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2014 OOO «Диафан» (http://diafan.ru)
 */

if (! defined('DIAFAN'))
{
	include dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/includes/404.php';
}

class Payment_payeer_admin
{
	public $config;

	public function __construct()
	{
		$this->config = array(
			"name" => 'Payeer',
			"params" => array(
                'm_url' => 'URL мерчанта (по умолчанию //payeer.com/merchant/ )',
                'm_shop' => 'ID магазина',
                'm_key' => 'Секретный ключ',
				'm_curr' => 'Валюта магазина (возможны варианты RUB, USD, EUR)',
				'm_desc' => 'Комментарий к оплате',
				'payeer_pathlog' => 'Путь до файла для журнала оплат через Payeer (например, /payeer_orders.log)',
				'payeer_ipfilter' => 'IP фильтр обработчика платежа',
				'payeer_emailerr' => 'Email для ошибок оплаты'
			)
		);
	}
}