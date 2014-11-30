<?php

/**
 * PayPal
 */
class Shop_Payment_System_Handler10 extends Shop_Payment_System_Handler
{
	private $_SandboxFlag = TRUE;
	private $_Api_Username = "nataly_1314795713_biz_api1.hostmake.ru";
	private $_Api_Password = "1314795733";
	private $_Api_Signature = "AKF.NjiuiXmELognC3ImjWqYgQ89APiQyO8rxvI5sctA-ADaTNXZo30s";
	private $_sBNCode = "PP-ECWizard";
	private $_Api_Endpoint = "";
	private $_Paypal_Url = "";
	private $_Subject = "sdk-three@sdk.com";
	private $_Use_Proxy = FALSE;
	private $_Proxy_Host = "127.0.0.1";
	private $_Proxy_Port = "808";
	private $_Version = 56.0;
	private $_Ack_Success = "Success";
	private $_Ack_Success_With_Warning = "SuccessWithWarning";
	private $_Default_Currency_Id = 0;

	public function __construct(Shop_Payment_System_Model $oShop_Payment_System_Model)
	{
		parent::__construct($oShop_Payment_System_Model);
		$oCurrency = Core_Entity::factory('Shop_Currency')->getByCode('USD');
		!is_null($oCurrency) && $this->_Default_Currency_Id = $oCurrency->id;

		if ($this->_SandboxFlag == TRUE)
		{
			$this->_Api_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
			$this->_Paypal_Url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
		}
		else
		{
			$this->_Api_Endpoint = "https://api-3t.paypal.com/nvp";
			$this->_Paypal_Url = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
		}
	}

	/* Вызывается на 4-ом шаге оформления заказа*/
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

	/* вычисление суммы товаров заказа */
	public function getSumWithCoeff()
	{
		return Shop_Controller::instance()->round(($this->_Default_Currency_Id > 0
				&& $this->_shopOrder->shop_currency_id > 0
			? Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
				$this->_shopOrder->Shop_Currency,
				Core_Entity::factory('Shop_Currency', $this->_Default_Currency_Id)
			)
			: 0) * $this->_shopOrder->getAmount() );
	}

	/* обработка ответа от платёжной системы */
	public function paymentProcessing()
	{
		/* Пришло подтверждение оплаты, обработаем его */
		if (!is_null(Core_Array::getRequest('PayPalOrderConfirmation')))
		{
			$this->ProcessResult();
			return TRUE;
		}

		/* Пришел запрос на редирект, обработаем его */
		if (!is_null(Core_Array::getRequest('paymentType')))
		{
			$this->ReviewOrder();
			return TRUE;
		}

		return TRUE;
	}

	/* оплачивает заказ */
	function ProcessResult()
	{
		if($this->_shopOrder->paid)
		{
			return FALSE;
		}

		ini_set('session.bug_compat_42',0);
		ini_set('session.bug_compat_warn',0);

		$token = urlencode(Core_Array::getRequest('token', ''));
		$paymentType = urlencode(Core_Array::getRequest('paymentType', ''));
		$currCodeType = urlencode(Core_Array::getRequest('currencyCodeType', ''));
		$payerID = urlencode(Core_Array::getRequest('PayerID', ''));
		$serverName = urlencode(Core_Array::get($_SERVER, 'SERVER_NAME', ''));
		$paymentAmount = urlencode(Core_Array::get($_SESSION, 'TotalAmount', ''));

		$nvpstr = sprintf("&TOKEN=%s&PAYERID=%s&PAYMENTREQUEST_0_PAYMENTACTION=%s&PAYMENTREQUEST_0_AMT=%s&PAYMENTREQUEST_0_CURRENCYCODE=%s&IPADDRESS=%s", $token, $payerID, $paymentType, $paymentAmount, $currCodeType, $serverName);

		if($this->_Version < 63)
		{
			$nvpstr .= sprintf("&PAYMENTACTION=%s&AMT=%s", $paymentType, $paymentAmount);
		}

		$resArray = $this->hash_call("DoExpressCheckoutPayment", $nvpstr);

		$ack = strtoupper($resArray["ACK"]);

		if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" )
		{
			$this->_shopOrder->system_information = sprintf("Товар оплачен через PayPal.\nАтрибуты:\nTransaction ID: %sAmount: %s %s", $resArray['TRANSACTIONID'], $resArray['AMT'], $currCodeType);

			$this->_shopOrder->paid();
			$this->setXSLs();
			$this->send();
		}
	}

	/* печатает форму отправки запроса на сайт платёжной системы */
	public function getNotification()
	{
		$oSite_Alias = $this->_shopOrder->Shop->Site->getCurrentAlias();
		$site_alias = !is_null($oSite_Alias) ? $oSite_Alias->name : '';
		$shop_path = $this->_shopOrder->Shop->Structure->getPath();
		$handler_url = 'http://'.$site_alias.$shop_path.'cart/';

		$default_sum = $this->getSumWithCoeff();

		?>
		<h1>Оплата через систему PayPal</h1>

		<!-- Форма для оплаты через WMR -->
		<form id="pay" name="pay" method="post" action="<?php echo $handler_url?>">
			<input type="hidden" name="paymentType" value="Sale">
			<input type="hidden" name="L_NAME0"	value="Order N <?php echo $this->_shopOrder->invoice?>">
			<input type="hidden" name="L_AMT0" value="<?php echo $default_sum?>" />
			<input type="hidden" name="L_QTY0" value="1" />
			<table>
			<tr>
			<td class="field"><strong><?php echo $default_sum?></strong></td>
			<td><select name="currencyCodeType">
			<option value="USD">USD</option>
			</select></td>
			</tr>
			<tr>
			<td colspan="2" align="center"><input type="image" name="submit"
			src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" /></td>
			</tr>
			<tr>
			<td colspan="2" align="center"><small>Save time. Pay securely without
			sharing your financial information.</small></td>
			</tr>
			</table>

			<!-- Для определения платежной системы на странице корзины --> <input
			type="hidden" name="order_id" value="<?php echo $this->_shopOrder->id?>">

			<div style="clear: both;"></div>
		</form>

		<?php
	}

	public function getInvoice()
	{
		return $this->getNotification();
	}


	/**
	 * Обработка формы для получения ссылки и редирект
	 */
	function ReviewOrder()
	{
		if($this->_shopOrder->paid)
		{
			return FALSE;
		}

		if(is_null(Core_Array::getRequest('token')))
		{
			$currencyCodeType = Core_Array::getRequest('currencyCodeType', '');
			$paymentType = Core_Array::getRequest('paymentType', '');

			$oSite_Alias = $this->_shopOrder->Shop->Site->getCurrentAlias();
			$site_alias = !is_null($oSite_Alias) ? $oSite_Alias->name : '';
			$shop_path = $this->_shopOrder->Shop->Structure->getPath();
			$handler_url = 'http://'.$site_alias.$shop_path.'cart/';

			$returnURL = urlencode(sprintf("%s?currencyCodeType=%s&paymentType=%s&order_id=%s&payment=success&PayPalOrderConfirmation=1", $handler_url, $currencyCodeType, $paymentType, $this->_shopOrder->id));
			$cancelURL = urlencode(sprintf("%s?paymentType=%s&order_id=%s&payment=failed", $handler_url, $paymentType, $this->_shopOrder->id));

			$paymentAmount = $this->getSumWithCoeff();

			$nvpstr = sprintf("&PAYMENTREQUEST_0_AMT=%s&PAYMENTREQUEST_0_PAYMENTACTION=%s&RETURNURL=%s&CANCELURL=%s&PAYMENTREQUEST_0_CURRENCYCODE=%s", $paymentAmount, $paymentType, $returnURL, $cancelURL, $currencyCodeType);

			if($this->_Version < 63)
			{
				$nvpstr = sprintf("%s&AMT=%s", $nvpstr, $paymentAmount);
			}

			$_SESSION["currencyCodeType"] = $currencyCodeType;
			$_SESSION["PaymentType"] = $paymentType;
			$_SESSION['TotalAmount'] = $paymentAmount;

			$resArray = $this->hash_call("SetExpressCheckout", $nvpstr);
			$ack = strtoupper(Core_Array::get($resArray, 'ACK', ''));

			if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
			{
				// Redirect to paypal.com here
				$token = urldecode(Core_Array::get($resArray, 'TOKEN', ''));
				$payPalURL = $this->_Paypal_Url . $token;

				?>
				<script language="JavaScript" type="text/javascript">
				location="<?php echo $payPalURL?>";
				</script>
				<?php
				exit();
			}
			else
			{
				//Display a user friendly Error on the page using any of the following error information returned by PayPal
				$ErrorCode = urldecode(Core_Array::get($resArray, 'L_ERRORCODE0', ''));
				$ErrorShortMsg = urldecode(Core_Array::get($resArray, 'L_SHORTMESSAGE0', ''));
				$ErrorLongMsg = urldecode(Core_Array::get($resArray, 'L_LONGMESSAGE0', ''));
				$ErrorSeverityCode = urldecode(Core_Array::get($resArray, 'L_SEVERITYCODE0', ''));

				echo "<p><b>SetExpressCheckout API call failed.</b></p>";
				echo "Detailed Error Message: " . $ErrorLongMsg;
				echo "<br />Short Error Message: " . $ErrorShortMsg;
				echo "<br />Error Code: " . $ErrorCode;
				echo "<br />Error Severity Code: " . $ErrorSeverityCode;
			}
		}
	}

	/**
	  '-------------------------------------------------------------------------------------------------------------------------------------------
	  * hash_call: Function to perform the API call to PayPal using API signature
	  * @methodName is name of API  method.
	  * @nvpStr is nvp string.
	  * returns an associtive array containing the response from the server.
	  '-------------------------------------------------------------------------------------------------------------------------------------------
	*/
	function hash_call($methodName,$nvpStr)
	{
		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_Api_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);

	    //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
	   //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
		if($this->_Use_Proxy)
			curl_setopt ($ch, CURLOPT_PROXY, $this->_Proxy_Host. ":" . $this->_Proxy_Port);

		//NVPRequest for submitting to server
		$nvpreq="METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($this->_Version) . "&PWD=" . urlencode($this->_Api_Password) . "&USER=" . urlencode($this->_Api_Username) . "&SIGNATURE=" . urlencode($this->_Api_Signature) . $nvpStr . "&BUTTONSOURCE=" . urlencode($this->_sBNCode);

		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		//getting response from server
		$response = curl_exec($ch);

		//convrting NVPResponse to an Associative Array
		$nvpResArray = $this->deformatNVP($response);
		$nvpReqArray = $this->deformatNVP($nvpreq);
		$_SESSION['nvpReqArray']=$nvpReqArray;

		if (curl_errno($ch))
		{
			// moving to display page to display curl errors
			  $_SESSION['curl_error_no']=curl_errno($ch) ;
			  $_SESSION['curl_error_msg']=curl_error($ch);
		}
		else
		{
			 //closing the curl
		  	curl_close($ch);
		}

		return $nvpResArray;
	}

	/** This function will take NVPString and convert it to an Associative Array and it will decode the response.
	 * It is usefull to search for a particular key and displaying arrays.
	 * @nvpstr is NVPString.
	 * @nvpArray is Associative Array.
	 */
	function deformatNVP($nvpstr)
	{
		$intial=0;
	 	$nvpArray = array();

		while(strlen($nvpstr))
		{
			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
	     }
		return $nvpArray;
	}
}
