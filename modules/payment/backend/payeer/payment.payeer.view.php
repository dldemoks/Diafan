<?php
/**
 * Шаблон платежа через Payeer
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

echo $result['text'];
?>
<p><img src="https://payeer.com/bitrix/templates/difiz/images/logo.png"></p>
<form method="GET" action="<?php echo $result['m_url'];?>">
	<input type="hidden" name="m_shop" value="<?php echo $result['m_shop'];?>">
	<input type="hidden" name="m_orderid" value="<?php echo $result['m_orderid'];?>">
	<input type="hidden" name="m_amount" value="<?php echo $result['m_amount'];?>">
	<input type="hidden" name="m_curr" value="<?php echo $result['m_curr'];?>">
	<input type="hidden" name="m_desc" value="<?php echo $result['m_desc'];?>">
	<input type="hidden" name="m_sign" value="<?php echo $result['m_sign'];?>">
	<p><input type="submit" name="m_process" value="<?php echo $this->diafan->_('Оплатить', false);?>" /></p>
</form>