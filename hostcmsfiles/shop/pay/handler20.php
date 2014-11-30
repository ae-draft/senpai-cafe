<?php

/**
 * LiqPay
 */
 class Shop_Payment_System_Handler20 extends Shop_Payment_System_Handler
{
	/**
	 * Номер мерчанта
	 * @var string
	 */
	protected $_merchant_id = 'XXX';
	
	/**
	 * Пароль мерчанта
	 * @var string
	 */
	protected $_merchant_sig = 'YYY';

	/**
	 * Международное название валюты из списка валют магазина
	 * @var string
	 */
	protected $_currency_name = 'RUB';
	
	/**
	 * Идентификатор валюты
	 * @var string
	 */
	protected $_currency_id = 1;
	
	/**
	 * Acquirer ID
	 * 414963 – Украина
	 * 469584 – Россия
	 * @var string
	 */
	protected $_acqid = 469584;
	
	/**
	 * Валюта покупки
	 * 980 — украинская гривна
	 * 643 — российский рубль
	 * @var string
	 */
	protected $_currency = 643;
	
	/**
	 * Метод, запускающий выполнение обработчика
	 * @return self
	 */
	public function execute()
	{
		parent::execute();

		$this->printNotification();

		return $this;
	}
	
	public function __construct(Shop_Payment_System_Model $oShop_Payment_System_Model)
	{
		parent::__construct($oShop_Payment_System_Model);
		$oCurrency = Core_Entity::factory('Shop_Currency')->getByCode($this->_currency_name);
		!is_null($oCurrency) && $this->_currency_id = $oCurrency->id;
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
		if (isset($_REQUEST['acqid']))
		{
			$this->ProcessResult();
			return TRUE;
		}
	}
	
	public function hexbin($temp) 
	{
		$data="";
		$len = strlen($temp);
		for ($i=0;$i<$len;$i+=2) $data.=chr(hexdec(substr($temp,$i,2)));
		return $data;
	}
	
	/*
	 * Обработка статуса оплаты
	 */
	function ProcessResult()
	{
		if($this->_shopOrder->paid)
		{
			return FALSE;
		}
		
		$password = $this->_merchant_sig;
		$merid = Core_Array::getPost('merid');
		$acqid = Core_Array::getPost('acqid');
		$orderid = Core_Array::getPost('orderid');
		$responsecode = Core_Array::getPost('responsecode');
		$reasoncode = Core_Array::getPost('reasoncode');
		$reasoncodedesc = Core_Array::getPost('reasoncodedesc');
		$lp_signature = Core_Array::getPost('signature');
		
		$our_signature =base64_encode($this->hexbin(sha1($password.$merid.$acqid.$orderid.$responsecode.$reasoncode.$reasoncodedesc)));
	
		if($lp_signature != '' 
			&& $our_signature == $lp_signature 
			&& $responsecode == 1 
			&& $reasoncode == 1)
		{
			$this->_shopOrder->system_information = sprintf("Заказ оплачен через LiqPay, данные заказа:\n№ заказа: %s\nAcquirer ID: %s\nКод ответа: %s\nОписание ответа: %s", $orderid, $acqid, $responsecode, $reasoncodedesc);

			$this->_shopOrder->paid();
			$this->setXSLs();
			$this->send();
		}
		else
		{
			$this->_shopOrder->system_information = sprintf("Заказ НЕ оплачен через LiqPay, данные заказа:\n№ заказа: %s\nAcquirer ID: %s\nКод ответа: %s\nОписание ответа: %s", $orderid, $acqid, $responsecode, $reasoncodedesc);
			$this->_shopOrder->save();
		}
	}
	
	public function getInvoice()
	{
		return $this->getNotification();
	}
	
	public function getSumWithCoeff()
	{
		return Shop_Controller::instance()->round(($this->_currency_id > 0
		&& $this->_shopOrder->shop_currency_id > 0
		? Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
		$this->_shopOrder->Shop_Currency,
		Core_Entity::factory('Shop_Currency', $this->_currency_id)
		)
		: 0) * $this->_shopOrder->getAmount());
	}
	
	public function getNotification()
	{
		$oSite_Alias = $this->_shopOrder->Shop->Site->getCurrentAlias();
		$sum = $this->getSumWithCoeff();
		
		if (is_null($oSite_Alias))
		{
			throw new Core_Exception('Site does not have default alias!');
		}
	
		$shop_path = $this->_shopOrder->Shop->Structure->getPath();
		$handler_url = 'http://' . $oSite_Alias->name . $shop_path . "cart/";
	
		$Version='1.0.0';
		$Password=$this->_merchant_sig;
		$MerID=$this->_merchant_id;
		$AcqID=$this->_acqid;
		$OrderID=$this->_shopOrder->id;
		$PurchaseCurrencyExponent='2';
		$PurchaseAmt=sprintf("%012s", number_format($sum, $PurchaseCurrencyExponent,'',''));
		$PurchaseCurrency=$this->_currency;
		$OrderDescription='Оплата заказа №'.$OrderID;
		$str = $Password.$MerID.$AcqID.$OrderID.$PurchaseAmt.$PurchaseCurrency.$OrderDescription;
		$Signature=sha1($str);
		$Signature=$this->hexbin($Signature);
		$Signature = base64_encode($Signature);
		?>
             <h1>Оплата кредитной картой через систему LiqPay</h1>
	
		<p>Сумма к оплате составляет <strong><?php echo $this->_shopOrder->sum()?></strong></p>

		<p>Для оплаты нажмите кнопку "Оплатить".</p>

		<p style="color: rgb(112, 112, 112);">
		Внимание! Нажимая &laquo;Оплатить&raquo; Вы подтверждаете передачу контактных данных на сервер LiqPay для оплаты.
		</p>
		<form method="post" action="https://ecommerce.liqpay.com/ecommerce/CheckOutPagen">
		<input name="version" value="<?php echo $Version?>" type="hidden">
		<input name="orderid" value="<?php echo $OrderID?>" type="hidden">
		<input name="merrespurl" value="<?php echo $handler_url?>" type="hidden">
		<input name="merid" value="<?php echo $MerID?>" type="hidden">
		<input name="acqid" value="<?php echo $AcqID?>" type="hidden">
		<input name="purchaseamt" value="<?php echo $PurchaseAmt?>" type="hidden">
		<input name="purchasecurrencyexponent" value="<?php echo $PurchaseCurrencyExponent?>" type="hidden">
		<input name="purchasecurrency" value="<?php echo $PurchaseCurrency?>" type="hidden">
		<input name="orderdescription" value="<?php echo $OrderDescription?>" type="hidden">
		<input name="signature" value="<?php echo $Signature?>" type="hidden">
		<input type="submit" value="Оплатить">
		</form>
		<?php
	}
}