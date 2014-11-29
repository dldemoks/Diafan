<?php
/**
 * Формирование платежа через платежную систему Payeer
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

class Payment_payeer_model extends Diafan
{
	/**
     * Формирует данные для формы платежной системы Payeer
     * 
     * @param array $params настройки платежной системы
     * @param array $pay данные о платеже
     * @return array
     */
	public function get($params, $pay)
	{
		$m_shop = $params['m_shop'];
		$m_orderid = $pay['id'];
		$m_amount = number_format($pay['summ'], 2, '.', '');
		$m_curr = $params['m_curr'];
		$m_desc = base64_encode($params['m_desc']);
		$m_key = $params['m_key'];

		$arHash = array(
			$m_shop,
			$m_orderid,
			$m_amount,
			$m_curr,
			$m_desc,
			$m_key
		);
		$sign = strtoupper(hash('sha256', implode(':', $arHash)));

		$result = array(
			'text' => $pay['text'],
			'm_url' => $params['m_url'],
			'm_shop' => $m_shop,
			'm_orderid' => $m_orderid,
			'm_amount' => $m_amount,
			'm_curr' => $m_curr,
			'm_desc' => $m_desc,
			'm_sign' => $sign
		);

		return $result;
	}
}