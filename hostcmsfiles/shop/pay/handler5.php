<?php

/**
 * Яндекс.Деньги (старое API)
 */
class Shop_Payment_System_Handler5 extends Shop_Payment_System_Handler
{
	// Идентификатор валюты. Указывается ID валюты рубли (RUR)
	private $_ym_currency_id = 1;

	// Коэффициент увеличения цены при оплате Яндекс.Деньгами
	private $_ym_coefficient = 1;

	// ---------------------------------
	// Для оплаты через "Яндекс.Деньги"
	// ---------------------------------

	// код валюты - рубли
	//private $_TargetCurrency = 643;
	private $_TargetCurrency = 10643;

	// код валюты - рубли
	//private $_currency = 643;
	private $_currency = 10643;

	// идентификатор процессингового центра платежной системы
	//private $_BankID = 100;
	private $_BankID = 1003;

	// идентификатор процессингового центра платежной системы
	//private $_TargetBankID = 1001;
	private $_TargetBankID = 1003;

	// тип платежа: по технологии PayCash
	private $_PaymentTypeCD = 'PC';

	/* Идентификатор магазина в ЦПП - уникальное значение,
	присваивается Магазину платежной системой после заключения договора */
	private $_ym_shop_id = 0;

	/* Номер витрины магазина в ЦПП. Выдается ЦПП. */
	private $_ym_scid = 0;

	// ---------------------------------

	/* Адрес платежной системы
	 * money.yandex.ru - реальный режим работы
	 * demomoney.yandex.ru - демо-режим
	 */
	//private $_action = 'http://money.yandex.ru/select-wallet.xml';
	private $_action = 'http://demomoney.yandex.ru/select-wallet.xml';

	public function __construct(Shop_Payment_System_Model $oShop_Payment_System_Model)
	{
		parent::__construct($oShop_Payment_System_Model);
		$oCurrency = Core_Entity::factory('Shop_Currency')->getByCode('RUB');
		!is_null($oCurrency) && $this->_ym_currency_id = $oCurrency->id;
	}

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
			$this->ProcessResult();
			return TRUE;
	}

	public function getSumWithCoeff()
	{
		return Shop_Controller::instance()->round(($this->_ym_currency_id > 0
				&& $this->_shopOrder->shop_currency_id > 0
			? Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
				$this->_shopOrder->Shop_Currency,
				Core_Entity::factory('Shop_Currency', $this->_ym_currency_id)
			)
			: 0) * $this->_shopOrder->getAmount() * $this->_ym_coefficient);
	}

	public function getNotification()
	{

		$ym_sum = $this->getSumWithCoeff();

		$fio = sprintf("%s %s %s",
			$this->_shopOrder->surname,
			$this->_shopOrder->name,
			$this->_shopOrder->patronymic);

		$address = sprintf("%s %s", $this->_shopOrder->postcode, $this->_shopOrder->address);
		$email = $this->_shopOrder->email;

		?>
		<!-- Форма для оплаты через Yandex.Деньги -->
		<h1>Из какого Кошелька вы будете платить?</h1>

		<div style="color: #000; background-color: #D8F0C6; padding: 20px; margin-right: 5%; float: left; width: 40%">

			<span style="font-size: 150%">Яндекс.Кошелек</span>

			<p>Чтобы пользоваться Яндекс.Кошельком, достаточно активировать его и создать платежный пароль.</p>

			<form method="post" action="<?php echo $this->_action?>">
				<input type="hidden" name="TargetCurrency" value="<?php echo $this->_TargetCurrency?>">
				<input type="hidden" name="currency" value="<?php echo $this->_currency?>">
				<input type="hidden" name="wbp_InactivityPeriod" value="2">
				<input type="hidden" name="wbp_ShopAddress" value="wn1.paycash.ru:8828">
				<input type="hidden" name="wbp_Version" value="1.0">
				<input type="hidden" name="BankID" value="<?php echo $this->_BankID?>">
				<input type="hidden" name="TargetBankID" value="<?php echo $this->_TargetBankID?>">
				<input type="hidden" name="PaymentTypeCD" value="<?php echo $this->_PaymentTypeCD?>">
				<input type="hidden" name="ShopID" value="<?php echo $this->_ym_shop_id?>">
				<input type="hidden" name="scid" value="<?php echo $this->_ym_scid?>">
				<input type="hidden" name="CustomerNumber" value="<?php echo $this->_shopOrder->invoice?>">
				<input type="hidden" name="Sum" value="<?php echo $ym_sum?>">
				<input type="hidden" name="CustName" value="<?php echo $fio?>">
				<input type="hidden" name="CustAddr" value="<?php echo $address?>">
				<input type="hidden" name="CustEMail" value="<?php echo $email?>">
				<!-- Содержание заказа: -->
				<!-- <input type="hidden" name="OrderDetails" value=""> -->
				<input type="submit" value="Перейти к оплате" style="font-weight: bold">
			</form>
		</div>

		<div style="clear: both"></div>
		<?php
	}

	public function getInvoice()
	{
		return $this->getNotification();
	}

	// Вывод сообщения об успешности/неуспешности оплаты
	public function ShowResultMessage()
	{
		return;
	}

	/*
	* Обработка статуса оплаты
	*/
	public function ProcessResult()
	{
		return;
	}
}