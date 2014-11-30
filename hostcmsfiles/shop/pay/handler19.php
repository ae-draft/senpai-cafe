<?php

/**
 * Interkassa
 */
class Shop_Payment_System_Handler19 extends Shop_Payment_System_Handler
{
	// Идентификатор магазина в системе Interkassa
	protected $_ik_shop_id = "XXX";

	//Секретный ключ (Secret Key) в системе Interkassa
	protected $_ik_secret_key = "YYY";

	// Код валюты в магазине HostCMS для валюты платежа в личном кабинете Интеркассы
	protected $_interkassa_currency = 3;

	/*
	 * Метод, запускающий выполнение обработчика
	 */
	public function execute()
	{
		parent::execute();

		$this->printNotification();

		return $this;
	}

	protected function _processOrder()
	{
		parent::_processOrder();

		// Установка XSL-шаблонов в соответствии с настройками в узле структуры
		$this->setXSLs();

		// Отправка писем клиенту и пользователю
		$this->send();

		return $this;
	}

	public function paymentProcessing()
	{
		// Пришло подтверждение оплаты, обработаем его
		if (isset($_REQUEST['ik_payment_id']))
		{
			$this->ProcessResult();
			return TRUE;
		}
	}

	/*
	 * Обработка статуса оплаты
	 */
	function ProcessResult()
	{
		$eshopId = Core_Array::getRequest('ik_shop_id');

		if ($eshopId != $this->_ik_shop_id
			|| $this->_shopOrder->paid
			|| Core_Array::getRequest('ik_payment_state') != 'success'
			// При ik_payment_state == success поля payment быть не должно
			|| !is_null(Core_Array::getRequest('payment'))
			)
		{
			return FALSE;
		}

		// Проверяем контрольную подпись
		$ik_shop_id = Core_Array::getRequest('ik_shop_id');
		$ik_payment_amount = Core_Array::getRequest('ik_payment_amount');
		$ik_payment_id = Core_Array::getRequest('ik_payment_id');
		$ik_payment_desc = Core_Array::getRequest('ik_payment_desc');
		$ik_paysystem_alias = Core_Array::getRequest('ik_paysystem_alias');
		$ik_baggage_fields = Core_Array::getRequest('ik_baggage_fields');
		$ik_payment_state = Core_Array::getRequest('ik_payment_state');
		$ik_trans_id = Core_Array::getRequest('ik_trans_id');
		$ik_currency_exch = Core_Array::getRequest('ik_currency_exch');
		$ik_fees_payer = Core_Array::getRequest('ik_fees_payer');

		$sCheck = $ik_shop_id.':'.$ik_payment_amount.':'.$ik_payment_id.':'.$ik_paysystem_alias.':'.$ik_baggage_fields.':'.$ik_payment_state.':'.$ik_trans_id.':'.$ik_currency_exch.':'.$ik_fees_payer.':'.$this->_ik_secret_key;

		$str_md5 = md5($sCheck);

		if (strtoupper($str_md5) == Core_Array::getRequest('ik_sign_hash', ''))
		{
			$this->_shopOrder->system_information = sprintf("Товар оплачен через Interkassa.\nАтрибуты:\nНомер сайта продавца: %s\nВнутренний номер покупки продавца: %s\nСумма платежа: %s\nВалюта платежа: %s\nНомер счета в системе Interkassa: %s\nДата и время выполнения платежа: %s\nСтатус платежа: 5 - Платеж зачислен\n",
				$ik_shop_id, $this->_shopOrder->id, $ik_payment_amount,
				$ik_currency_exch, $ik_payment_id, $ik_payment_state);

			$this->_shopOrder->paid();
			$this->setXSLs();
			$this->send();
		}
		else
		{
			$this->_shopOrder->system_information = 'Хэш не совпал, проверочная строка: ' . $sCheck;
			$this->_shopOrder->save();
		}
	}

	public function getSumWithCoeff()
	{
		return Shop_Controller::instance()->round(($this->_interkassa_currency > 0
		&& $this->_shopOrder->shop_currency_id > 0
		? Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
		$this->_shopOrder->Shop_Currency,
		Core_Entity::factory('Shop_Currency', $this->_interkassa_currency)
		)
		: 0) * $this->_shopOrder->getAmount());
	}

	public function getNotification()
	{
		$sum = $this->getSumWithCoeff();

		$oSite_Alias = $this->_shopOrder->Shop->Site->getCurrentAlias();

		if (is_null($oSite_Alias))
		{
			throw new Core_Exception('Site does not have default alias!');
		}

		$shop_path = $this->_shopOrder->Shop->Structure->getPath();
		$handler_url = 'http://' . $oSite_Alias->name . $shop_path . "cart/?orderId={$this->_shopOrder->id}";

		$successUrl = $handler_url . "&payment=success";
		$failUrl = $handler_url . "&payment=fail";

		$oShop_Currency = Core_Entity::factory('Shop_Currency')->find($this->_interkassa_currency);

		if(!is_null($oShop_Currency->id))
		{
			$serviceName = 'Оплата счета N ' . $this->_shopOrder->id;

			?>
			<h1>Оплата через систему Interkassa</h1>

			<p>
				<a href="http://www.interkassa.com/" target="_blank">
				<img src="http://www.interkassa.com/img/ik/interkassa_logo.gif" border="0" alt="Система электронных платежей">
				</a>
			</p>
			<p>Сумма к оплате составляет <strong><?php echo $this->_shopOrder->sum()?></strong></p>

			<p>Для оплаты нажмите кнопку "Оплатить".</p>

			<p style="color: rgb(112, 112, 112);">
				Внимание! Нажимая &laquo;Оплатить&raquo; Вы подтверждаете передачу контактных данных на сервер Interkassa для оплаты.
			</p>

			<form action="http://www.interkassa.com/lib/payment.php" method="POST">
				<input type="hidden" name="ik_shop_id" value="<?php echo $this->_ik_shop_id?>">
				<input type="hidden" name="ik_payment_amount" value="<?php echo $sum?>">
				<input type="hidden" name="ik_payment_id" value="<?php echo $this->_shopOrder->id?>">
				<input type="hidden" name="ik_payment_desc" value="<?php echo $serviceName?>">
				<input type="hidden" name="ik_paysystem_alias" value="">
				<input type="hidden" name="ik_success_url" value="<?php echo $successUrl?>">
				<input type="hidden" name="ik_fail_url" value="<?php echo $failUrl?>">
				<input type="hidden" name="ik_baggage_fields" value="<?php echo $_SESSION['hostcmsOrder']['description']?>">
				<input type="submit" value="Оплатить">
			</form>
			<?php
		}
	}
	public function getInvoice()
	{
		return $this->getNotification();
	}
}