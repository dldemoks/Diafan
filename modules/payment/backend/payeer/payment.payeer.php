<?php
/**
 * Обработчик платежа через Payeer
 * 
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    5.4
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2014 OOO «Диафан» (http://diafan.ru)
 */

if (! defined('DIAFAN'))
{
	include dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/includes/404.php';
}

if (empty($_REQUEST['m_orderid']))
{
	Custom::inc('includes/404.php');
}

if ($_GET["rewrite"] == "payeer/result")
{
	if (isset($_POST["m_operation_id"]) && isset($_POST["m_sign"]))
	{
		$err = false;
		$message = '';
		$order_id = preg_replace('/[^a-zA-Z0-9_-]/', '', substr($_POST['m_orderid'], 0, 32));
		$pay = $this->diafan->_payment->check_pay($order_id, 'payeer');

		// запись логов

		$log_text = 
		"--------------------------------------------------------\n" .
		"operation id		" . $_POST['m_operation_id'] . "\n" .
		"operation ps		" . $_POST['m_operation_ps'] . "\n" .
		"operation date		" . $_POST['m_operation_date'] . "\n" .
		"operation pay date	" . $_POST['m_operation_pay_date'] . "\n" .
		"shop				" . $_POST['m_shop'] . "\n" .
		"order id			" . $_POST['m_orderid'] . "\n" .
		"amount				" . $_POST['m_amount'] . "\n" .
		"currency			" . $_POST['m_curr'] . "\n" .
		"description		" . base64_decode($_POST['m_desc']) . "\n" .
		"status				" . $_POST['m_status'] . "\n" .
		"sign				" . $_POST['m_sign'] . "\n\n";
		
		$log_file = $pay['params']['payeer_pathlog'];
		
		if (!empty($log_file))
		{
			file_put_contents($_SERVER['DOCUMENT_ROOT'] . $log_file, $log_text, FILE_APPEND);
		}	

		// проверка цифровой подписи и ip

		$sign_hash = strtoupper(hash('sha256', implode(":", array(
			$_POST['m_operation_id'],
			$_POST['m_operation_ps'],
			$_POST['m_operation_date'],
			$_POST['m_operation_pay_date'],
			$_POST['m_shop'],
			$_POST['m_orderid'],
			$_POST['m_amount'],
			$_POST['m_curr'],
			$_POST['m_desc'],
			$_POST['m_status'],
			$pay['params']['m_key']
		))));
		
		$valid_ip = true;
		$sIP = str_replace(' ', '', $pay['params']['payeer_ipfilter']);
		
		if (!empty($sIP))
		{
			$arrIP = explode('.', $_SERVER['REMOTE_ADDR']);
			if (!preg_match('/(^|,)(' . $arrIP[0] . '|\*{1})(\.)' .
			'(' . $arrIP[1] . '|\*{1})(\.)' .
			'(' . $arrIP[2] . '|\*{1})(\.)' .
			'(' . $arrIP[3] . '|\*{1})($|,)/', $sIP))
			{
				$valid_ip = false;
			}
		}
		
		if (!$valid_ip)
		{
			$message .= " - ip-адрес сервера не является доверенным\n" .
			"   доверенные ip: " . $sIP . "\n" .
			"   ip текущего сервера: " . $_SERVER['REMOTE_ADDR'] . "\n";
			$err = true;
		}

		if ($_POST['m_sign'] != $sign_hash)
		{
			$message .= " - не совпадают цифровые подписи\n";
			$err = true;
		}
		
		if (!$err)
		{
			$order_curr = ($pay['params']['m_curr'] == 'RUR') ? 'RUB' : $pay['params']['m_curr'];
			$order_amount = number_format($pay['summ'], 2, '.', '');
			
			// проверка суммы и валюты
		
			if ($_POST['m_amount'] != $order_amount)
			{
				$message .= " - неправильная сумма\n";
				$err = true;
			}

			if ($_POST['m_curr'] != $order_curr)
			{
				$message .= " - неправильная валюта\n";
				$err = true;
			}

			// проверка статуса
			
			if (!$err)
			{
				switch ($_POST['m_status'])
				{
					case 'success':
						$this->diafan->_payment->success($pay, 'pay');
						break;
						
					default:
						$message .= " - статус платежа не является success\n";
						$err = true;
						break;
				}
			}
		}
		
		if ($err)
		{
			$to = $pay['params']['payeer_emailerr'];

			if (!empty($to))
			{
				$message = "Не удалось провести платёж через систему Payeer по следующим причинам:\n\n" . $message . "\n" . $log_text;
				$headers = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n" . 
				"Content-type: text/plain; charset=utf-8 \r\n";
				mail($to, 'Ошибка оплаты', $message, $headers);
			}
			
			exit ($_POST['m_orderid'] . '|error');
		}
		else
		{
			exit ($_POST['m_orderid'] . '|success');
		}
	}
}

if ($_GET["rewrite"] == "payeer/success")
{
	$order_id = preg_replace('/[^a-zA-Z0-9_-]/', '', substr($_GET['m_orderid'], 0, 32));
	$pay = $this->diafan->_payment->check_pay($order_id, 'payeer');
	$this->diafan->_payment->success($pay, 'redirect');
}

if ($_GET["rewrite"] == "payeer/fail")
{
	$order_id = preg_replace('/[^a-zA-Z0-9_-]/', '', substr($_GET['m_orderid'], 0, 32));
	$pay = $this->diafan->_payment->check_pay($order_id, 'payeer');
	$this->diafan->_payment->fail($pay);
}

