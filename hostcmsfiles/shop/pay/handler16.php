<?php

/**
QIWI Кошелек

1. В разделе «Справочники» -> „Платежные системы“(в центре администрирования внутри вашего магазина) создайте новую платежную систему „QIWI Кошелек“ и в поле „Обработчик“ вставьте код этого файла.
После вставки в этом коде в качестве значения переменной $login вместо xxx введите логин вашего магазина, а в качестве значения переменной $password вместо yyy введите пароль вашего магазина(эти данные выдает QIWI после заключения с ними договора).
2. В директорию /hostcmsfiles/shop/pay/ скопируйте из дистрибутива файлы IShopClientWS.wsdl и IShopServerWS.wsdl.
3. В «Структуре сайта» внутри узла /shop/ создайте раздел под названием server_qiwi. В его поле „Название раздела“ введите также server_qiwi. Типом раздела нужно выбрать „Динамическая страница“. Поле „Динамическая страница“ оставляете пустым, а в поле „Настройки динамической страницы“ внесите код:
<?php
$iShop_Payment_System_Id = 16;

// Вызов обработчика платежной системы
Shop_Payment_System_Handler::factory(
		Core_Entity::factory('Shop_Payment_System', $iShop_Payment_System_Id)
	)
	->handleSoapServer();

die();
?>
в этом коде в качестве значения переменной $iShop_Payment_System_Id вместо 16 укажите идентификатор платежной системы «QIWI Кошелек».
4. Произвести  настройки в  личном кабинете на сайте QIWI. В качестве протокола взаимодействия нужно включить SOAP-протокол, в настройках которого нужно снять галочку «С использованием подписи WSS…» для передачи пароля, а также в поле ввода адреса страницы вашего сайта для обработки запросов от QIWI укажите: http://www.site.ru/shop/server_qiwi/, где вместо www.site.ru укажите домен своего сайта.
5. Обращаем внимание, что обмен данными (а значит и сам обработчик платежной системы) по протоколу SOAP будет работать только в том случае, если PHP на вашем сервере собран с поддержкой SOAP и SSL.
*/
class createBill {
	/* логин (id) магазина */
	public $login = 'xxx';

	/* пароль для магазина */
	public $password = 'yyy';

	/* идентификатор пользователя (номер телефона) */
	public $user; // string

	/* сумма, на которую выставляется счет (разделитель «.») */
	public $amount; // string

	/* комментарий к счету, который увидит пользователь (максимальная длина 255 байт) */
	public $comment; // string

	/* уникальный идентификатор счета (максимальная длина 30 байт) */
	public $txn; // string

	/* время действия счета в секундах, в течение которого счет будет храниться в системе QIWI и будет доступен для оплаты */
	public $lifetime_sec = 3888000; //45 суток

	/* отправить оповещение пользователю (1 - уведомление SMS-сообщением, 2 - уведомление звонком, 0 - не оповещать).
	Уведомления доступны только магазинам, зарегистрированным по схеме "Именной кошелек".
	Для магазинов, зарегистрированных по схеме "Прием платежей", уведомления заблокированы. */
	public $alarm = 0;

	/* флаг для создания нового пользователя (если он не зарегистрирован в системе) */
	public $create = false;
}

class createBillResponse {
	public $createBillResult; // int
}

class Response {
  public $updateBillResult;
}

class Param {
  public $login;
  public $password;
  public $txn;
  public $status;
}

class ServerQIWI
{
	public function updateBill($param_update_bill)
	{
		$oShop_Order = Core_Entity::factory('Shop_Order')->find($param_update_bill->txn);

		$oShop = $oShop_Order->Shop;

		$return_status = new Response();

		$return_status->updateBillResult = 300;

		$shop_parametres = new createBill();
		$txn_windows_1251 = iconv("utf-8", "windows-1251", $param_update_bill->txn);
		$password_windows_1251 = iconv("utf-8", "windows-1251", $shop_parametres->password);
		$password = strtoupper(md5($txn_windows_1251 . strtoupper(md5($password_windows_1251))));

		if ($shop_parametres->login == $param_update_bill->login && $param_update_bill->password == $password)
		{
			if(is_null($oShop_Order->id))
			{
				//Счет не найден
			   $return_status->updateBillResult = 210;
			}
			else
			{
				$oShop_Order->system_information = $this->ReferenceAccountStatus($param_update_bill->status);
				$oShop_Order->save();

				if ($param_update_bill->status == 60 && !$oShop_Order->paid)
				{
					$oShop_Order->paid();

					// Вызов обработчика платежной системы
					// Отправка писем
					Shop_Payment_System_Handler::factory($oShop_Order->Shop_Payment_System)
						->shopOrder($oShop_Order)
						->setXSLs()
						->send();
				}

				$return_status->updateBillResult = 0;
			}
		}
		else
		{
			$return_status->updateBillResult = 150;
		}

	   return $return_status;
	}

	public function ReferenceAccountStatus($status)
	{
		$return_code_message = "";

		switch ($status) {
			case 60:
				$return_code_message = "Заказ оплачен через QIWI Кошелек.\n";
			break;
			case 50:
				$return_code_message = "Счет выставлен.\n";
			break;
			case 52:
				$return_code_message = "Счет проводится.\n";
			break;
			case 150:
				$return_code_message = "Счет отменен(ошибка на терминале).\n";
			break;
			case 151:
				$return_code_message = "Счет отменен (ошибка авторизации: недостаточно средств на балансе, отклонен абонентом при оплате с лицевого счета оператора сотовой связи и т.п.).\n";
			break;
			case 160:
				$return_code_message = "Счет отменен.\n";
			break;
			case 161:
				$return_code_message = "Счет отменен(истекло время).\n";
			break;
			default:
				$return_code_message = "Неизвестный статус.\n";
			break;
		}
		return $return_code_message;
	}
}

class Shop_Payment_System_Handler16 extends Shop_Payment_System_Handler
{
	private $_rub_currency_id = 1;

	/* Вызывается на 4-ом шаге оформления заказа*/
	public function execute()
	{
		parent::execute();

		/* Иначе оформляем заказ и отображаем стартовую страницу для оплаты через QIWI Кошелек */
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
		return Shop_Controller::instance()->round(($this->_rub_currency_id > 0
				&& $this->_shopOrder->shop_currency_id > 0
			? Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
				$this->_shopOrder->Shop_Currency,
				Core_Entity::factory('Shop_Currency', $this->_rub_currency_id)
			)
			: 0) * $this->_shopOrder->getAmount());
	}

	/* обработка ответа от платёжной системы */
	public function paymentProcessing()
	{
		$this->ProcessResult();

		return TRUE;
	}

	/* оплачивает заказ */
	function ProcessResult()
	{
		if($this->_shopOrder->paid)
		{
			return FALSE;
		}

		$params = new createBill();

		$params->user = Core_Array::getPost('user_qiwi', ''); // пользователь, которому выставляется счет
		$params->amount = $this->getSumWithCoeff(); // сумма
		$params->comment = sprintf("Оплата заказа №%s интернет-магазина %s", $this->_shopOrder->invoice, $this->_shopOrder->Shop->name);
		$params->txn = $this->_shopOrder->id; // номер заказа
		$params->lifetime = date('d.m.Y H:i:s', time() + $params->lifetime_sec);

		if(isset($_POST['need_to_register_user_qiwi']))
		{
			$params->create = true;
		}

		$wsdlPath = CMS_FOLDER."hostcmsfiles/shop/pay/IShopServerWS.wsdl";
		$options = array();
		$options['classmap']['createBill'] = 'createBill';
		$options['classmap']['createBillResponse'] = 'createBillResponse';

		try
		{
			$client = new SoapClient($wsdlPath, $options);
			$bill_result = new createBillResponse();
			$bill_result = $client->createBill($params);
			$bill_result_text = $this->referenceCodeCompletion($bill_result->createBillResult);

			$this->_shopOrder->system_information = sprintf("Код ответа: %s - %s", $bill_result->createBillResult, $bill_result_text);
			$this->_shopOrder->save();

			?>
			<h2>Оплата через систему QIWI Кошелек</h2>
			<?php

			if($bill_result->createBillResult === 0)
			{
				echo $bill_result_text . ". Можете оплатить его одним из способов:<br/><ul><li>Наличными</li><li>Из QIWI Кошелька</li><li>Банковскими картами Visa, MasterCard</li><li>С лицевого счета МТС, Билайн, Мегафон</li></ul>";
				?><h1><a href="https://w.qiwi.ru/orders.action" target="blank">Перейти к оплате счета</a></h1><?php
			}
			else
			{
				echo "Счет не добавлен. Код ответа: ". $bill_result->createBillResult . " - " . $bill_result_text;
			}
		}
		catch (Exception $e)
		{
			Core_Message::show($e->getMessage(), 'error');
		}

		return true;
	}

	/* печатает форму отправки запроса на сайт платёжной системы */
	public function getNotification()
	{
		$oSite_Alias = $this->_shopOrder->Shop->Site->getCurrentAlias();
		$site_alias = !is_null($oSite_Alias) ? $oSite_Alias->name : '';
		$shop_path = $this->_shopOrder->Shop->Structure->getPath();
		$handler_url = 'http://'.$site_alias.$shop_path.'cart/';

		?>
		<h2>Оплата через систему QIWI Кошелек</h2>

		<form method="POST" action="<?php echo $handler_url?>">
			<input type="hidden" name="order_id" value="<?php echo $this->_shopOrder->id?>">
			<table>
				<tr>
					<td class="field">Номер Вашего мобильного телефона(должен совпадать с номером в системе QIWI Кошелек в случае, если Вы там зарегистрированы) - без восьмерки, без скобок, без дефисов:</td>
					<td>
						<input type="text" size="30" maxlength="32" name="user_qiwi" value=""/>
					</td>
				</tr>
				<tr>
					<td class="field">Зарегистрировать Вас в системе QIWI Кошелек?</td>
					<td>
						<input type="checkbox" name="need_to_register_user_qiwi"/> Да
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="qiwi_payment_options" value="Далее">
					</td>
				</tr>
			</table>
			<div style="clear: both;"></div>
		</form>
		<?php
	}

	public function getInvoice()
	{
		return $this->getNotification();
	}

	public function referenceCodeCompletion($code)
	{
		$return_code_message = "";

		switch ($code)
		{
			case 0:
				$return_code_message = "Добавление счета прошло успешно";
			break;
			case 13:
				$return_code_message = "Сервер занят, повторите запрос позже";
			break;
			case 150:
				$return_code_message = "Ошибка авторизации (неверный логин/пароль)";
			break;
			case 210:
				$return_code_message = "Счет не найден";
			break;
			case 215:
				$return_code_message = "Счет с таким txn-id уже существует";
			break;
			case 241:
				$return_code_message = "Сумма слишком мала";
			break;
			case 242:
				$return_code_message = "Превышена максимальная сумма платежа – 15 000р.";
			break;
			case 278:
				$return_code_message = "Превышение максимального интервала получения списка счетов";
			break;
			case 298:
				$return_code_message = "Агента не существует в системе";
			break;
			case 300:
				$return_code_message = "Неизвестная ошибка";
			break;
			case 330:
				$return_code_message = "Ошибка шифрования";
			break;
			case 370:
				$return_code_message = "Превышено максимальное кол-во одновременно выполняемых запросов";
			break;
			default:
				$return_code_message = "Неизвестный статус";
			break;
		}
		return $return_code_message;
	}

	public function handleSoapServer()
	{
		$wsdlPath = CMS_FOLDER . "hostcmsfiles/shop/pay/IShopClientWS.wsdl";
		$server = new SoapServer($wsdlPath, array(
			'classmap' => array(
				'tns:updateBill' => 'Param',
				'tns:updateBillResponse' => 'Response')
			)
		);
		$server->setClass("ServerQIWI");
		$server->handle();
	}
}