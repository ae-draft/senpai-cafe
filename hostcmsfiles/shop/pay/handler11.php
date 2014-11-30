<?php 
/* Оплата через Cyberplat */
class system_of_pay_handler
{
	/* Кошельки */
	var $wmr = 'R123456789123';
	var $wmz = 'Z123456789123';

	/* Определяем валюты для WMR и WMZ */
	var $wmr_currency_id = 1;
	var $wmz_currency_id = 3;

	/* Определяем коэффициенты перерасчета для WMR и WMZ */
	var $wmr_coefficient_id = 1;
	var $wmz_coefficient_id = 1;

	/* Секретные ключи для кошельков, должны совпадать с настройками
	на https://merchant.webmoney.ru/conf/purses.asp */
	var $wmr_secret_key = 'hostmake';
	var $wmz_secret_key = 'hostmake';

	protected function _processOrder()
	{
		parent::_processOrder();

		// Установка XSL-шаблонов в соответствии с настройками в узле структуры
		$this->setXSLs();

		// Отправка писем клиенту и пользователю
		$this->send();

		return $this;
	}
	
	/**
	 * Обработка статуса оплаты
	 *
	 */
	function ProcessResult()
	{
		/* Информация о заказе */
		$order_id = to_int($_POST['order_id']);

		if (!$order_id)
		{
			return false;
		}

		$shop = & singleton('shop');

		$order_row = $shop->GetOrder($order_id);

		// Заказ не найден или уже оплачен
		if (!$order_row || $order_row['shop_order_status_of_pay'])
		{
			return false;
		}

		$shop_row = $shop->GetShop($order_row['shop_shops_id']);

		/* В зависимости от типа кошелька выбираем Secret Key */
		switch (to_int($_POST['purse']))
		{
			case 1: /* WMR */
			{
				$secret_key = $this->wmr_secret_key;
				$currency_name = "WMR";
				break;
			}
			case 2: /* WMZ */
			{
				$secret_key = $this->wmz_secret_key;
				$currency_name = "WMZ";
				break;
			}
			default:
				{
					return false;
				}
		}

		/* Проверяем целостность данных */
		$str = to_str($_POST['LMI_PAYEE_PURSE']).
		to_str($_POST['LMI_PAYMENT_AMOUNT']).
		to_str($_POST['LMI_PAYMENT_NO']).
		to_str($_POST['LMI_MODE']).
		to_str($_POST['LMI_SYS_INVS_NO']).
		to_str($_POST['LMI_SYS_TRANS_NO']).
		to_str($_POST['LMI_SYS_TRANS_DATE']).
		$secret_key.
		to_str($_POST['LMI_PAYER_PURSE']).
		to_str($_POST['LMI_PAYER_WM']);

		$str = md5($str);

		/* Устанавливаем параметры */
		$param['id'] = $order_row['shop_order_id'];
		$param['shop_shops_id'] = $order_row['shop_shops_id'];

		/* Сравниваем хэши */
		if (mb_strtoupper($str) == mb_strtoupper(to_str($_POST['LMI_HASH'])))
		{
			/* Проверка прошла успешно!
			Добавляем комментарий */
			$param['system_information'] = "Товар оплачен через WebMoney.\n".
			"Атрибуты:\n".
			"Кошелек продавца: {$_POST['LMI_PAYEE_PURSE']}\n".
			"Сумма: {$_POST['LMI_PAYMENT_AMOUNT']} {$currency_name}\n".
			"Номер покупки: {$_POST['LMI_PAYMENT_NO']}\n".
			"Режим (0 - реальный, 1 - тестовый): {$_POST['LMI_MODE']}\n".
			"Номер счета для покупателя: {$_POST['LMI_SYS_INVS_NO']}\n".
			"Номер платежа: {$_POST['LMI_SYS_TRANS_NO']}\n".
			"Кошелек покупателя: {$_POST['LMI_PAYER_PURSE']}\n".
			"WM-идентификатор покупателя: {$_POST['LMI_PAYER_WM']}";

			/* Устанавливаем признак оплаты */
			//$param['date_of_pay'] = date("Y-m-d H:i:s");
			//$param['status_of_pay'] = true;

			// Обновляем информацию о заказе
			$shop->InsertOrder($param);
			
			// Изменяем статус оплаты ПОСЛЕ ОБНОВЛЕНИЯ ИНФОРМАЦИ, генерируем ссылки для эл.товаров, списываем товары
			$shop->SetOrderPaymentStatus($order_id);
		}
		else
		{
			$param['system_information'] = 'WM хэш не совпал!';
			
			// Обновляем информацию о заказе
			$shop->InsertOrder($param);			
		}

		// Отправку письма об оплате делаем только после вставки информации об оплате
		if (mb_strtoupper($str) == mb_strtoupper(to_str($_POST['LMI_HASH'])))
		{
			$structure = & singleton('Structure');
			$structure_row = $structure->GetStructureItem(to_int($shop_row['structure_id']));

			$lib = new lib();
			$LA = $lib->LoadLibPropertiesValue(to_int($structure_row['lib_id']), to_int($structure_row['structure_id']));

			$order_row = $shop->GetOrder($order_id);

			// Отправляем письмо администратору о подтверждении платежа
			$shop->SendMailAboutOrder($order_row['shop_shops_id'], $order_id, $order_row['site_users_id'],
			to_str($LA['xsl_letter_to_admin']),
			to_str($LA['xsl_letter_to_user']),
			$order_row['shop_order_users_email'],
			array(
			'admin-content-type' => 'html',
			'user-content-type' => 'html',
			'admin-subject' => sprintf($GLOBALS['MSG_shops']['shop_order_confirm_admin_subject'], $order_id, $shop_row['shop_shops_name'], $order_row['shop_order_date_of_pay']),
			'user-subject' => sprintf($GLOBALS['MSG_shops']['shop_order_confirm_user_subject'], $order_id, $shop_row['shop_shops_name'], $order_row['shop_order_date_of_pay']),
			'email_from_admin' => $order_row['shop_order_users_email']));
		}
	}

	/**
	 * Отображает стартовую страницу для оплаты через Web Money.
	 *
	 */
	function ShowPurseRequest()
	{
		$shop = & singleton('shop');
		
		/* ID платежной системы берем из сессии */
		$system_of_pay_id = to_int($_SESSION['system_of_pay_id']);
		$row_system_of_pay = $shop->GetSystemOfPay($system_of_pay_id);
		
		if ($row_system_of_pay)
		{
			$shop_id = $row_system_of_pay['shop_shops_id'];
		}
		else
		{
			return false;
		}

		/* Получаем id текущего пользователя сайта */
		if (class_exists('SiteUsers'))
		{
			/* Получаем id текущего пользователя сайта */
			$SiteUsers = & singleton('SiteUsers');
			$site_users_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_users_id = false;
		}

		// статус платежа, по умолчанию 0
		$order_row['status_of_pay'] = 0 ;

		// дата платежа, по умолчанию пустая строка
		$order_row['date_of_pay'] = '';

		$order_row['description'] = to_str($_SESSION['description']);
		
		// описание и системная информация, по умолчанию пустая строка
		if (to_str($_SESSION['shop_coupon_text']) != '')
		{
			$order_row['description'] .= "Купон на скидку: ".to_str($_SESSION['shop_coupon_text'])."\n";
		}

		if (!isset($_SESSION['last_order_id']))
		{
			$_SESSION['last_order_id'] = 0;
		}

		// Если заказ еще не был оформлен
		if ($_SESSION['last_order_id'] == 0)
		{
			/* Оформляем заказ */
			$order_id = $shop->ProcessOrder($shop_id, $site_users_id, $system_of_pay_id, $order_row);
		}
		else
		{
			$order_id = $_SESSION['last_order_id'];
		}

		if ($order_id > 0)
		{
			if (!class_exists('SiteUsers'))
			{
				/* Класс пользователей сайта не существует, дописываем информацию о заказчике
				в поле shop_order_description из текущей сессии */
				if ($order_row)
				{
					/* Описание заказчика */
					$order_row['description'] .= "Информация о заказчике:\n"
					."Имя: ".to_str($_SESSION['site_users_name'])."\n"
					."Фамилия: ".to_str($_SESSION['site_users_surname'])."\n"
					."Отчество: ".to_str($_SESSION['site_users_patronymic'])."\n"
					."E-Mail: ".to_str($_SESSION['site_users_email'])."\n"
					."Телефон: ".to_str($_SESSION['site_users_phone'])."\n"
					."Факс: ".to_str($_SESSION['site_users_fax'])."\n"
					."Адрес: ".to_str($_SESSION['full_address'])."\n";
					
					/* Дополнительная информация о заказе */
					$order_row['system_information'] = to_str($_SESSION['system_information']);	

					/* Обязательно добавляем идентификатор! */
					$order_row['id'] = $order_id;

					$shop->InsertOrder($order_row);
				}
			}

			$order_row = $shop->GetOrder($order_id);
			
			if ($order_row)
			{
				$this->PrintOrder($order_id);
			}
			
			$shop_row = $shop->GetShop($shop_id);
			
			if ($_SESSION['last_order_id'] == 0)
			{
				$structure = & singleton('Structure');
				$structure_row = $structure->GetStructureItem(to_int($shop_row['structure_id']));

				$lib = new lib();
				$LA = $lib->LoadLibPropertiesValue(to_int($structure_row['lib_id']), to_int($structure_row['structure_id']));

				$date_str = date("d.m.Y H:i:s");

				if (trim(to_str($order_row['shop_order_account_number'])) != '')
				{
					$shop_order_account_number = trim(to_str($order_row['shop_order_account_number']));
				}
				else
				{
					$shop_order_account_number = $order_id;
				}
				
				/* Отправляем письмо заказчику */
				$shop->SendMailAboutOrder($shop_id,
				$order_id,
				$site_users_id,
				to_str($LA['xsl_letter_to_admin']),
				to_str($LA['xsl_letter_to_user']),
				$order_row['shop_order_users_email'],
				array('admin-content-type' => 'html',
				'user-content-type' => 'html',
				'admin-subject' => sprintf($GLOBALS['MSG_shops']['shop_order_admin_subject'], $shop_order_account_number, $shop_row['shop_shops_name'], $date_str),
				'user-subject' => sprintf($GLOBALS['MSG_shops']['shop_order_user_subject'], $shop_order_account_number, $shop_row['shop_shops_name'], $date_str),
				'email_from_admin' => $order_row['shop_order_users_email']));
			}
			
			// Сохраняем ID последнего оформленного заказа ТОЛЬКО ПОСЛЕ ОТПРАВКИ ПИСЬМА
			$_SESSION['last_order_id'] = $order_id;
		}
		else
		{
			switch ($order_id)
			{
				case -1:
					{
						?><div id="error">Ошибка вставки заказа в базу данных. Обратитесь к администратору.</div><?php
						break;
					}
				case -2:
					{
						?><div id="error">Ошибка - не найден магазин. Обратитесь к администратору.</div><?php
						break;
					}
				case -3:
					{
						?><div id="error">Ошибка - корзина пуста. Добавьте товар в корзину и оформите заказ.</div><?php
						break;
					}
			}
		}
	}

	/**
	 * Метод, запускающий выполнение обработчика
	 */
	function Execute()
	{
		/* Пришло подтверждение оплаты, обработаем его */
		if (isset($_POST['LMI_PAYEE_PURSE']))
		{
			$this->ProcessResult();
			return true;
		}

		/* Иначе оформляем заказ и отображаем стартовую страницу для оплаты через WebMoney */
		$this->ShowPurseRequest();
	}

	/**
	 * Метод для отображения формы заказа для печати.
	 *
	 * @param int $order_id идентификатор заказа
	 */
	function PrintOrder($order_id)
	{
		$shop = & singleton('shop');
		
		$order_row = $shop->GetOrder($order_id);
		if (!$order_row)
		{
			return false;
		}
		
		if ($order_row)
		{
			$shop_row = $shop->GetShop($order_row['shop_shops_id']);

			$order_sum = $shop->GetOrderSum($order_id);

			/* Делаем перерасчет суммы в валюты, выбранные для WebMoney */
			$shop_currency_id = $shop_row['shop_currency_id'];

			/* Для WMR */
			$coefficient = $shop->GetCurrencyCoefficientToShopCurrency($shop_currency_id, $this->wmr_currency_id);
			$wmr_sum = round($order_sum * $coefficient * $this->wmr_coefficient_id, 2);

			/* Для WMZ */
			$coefficient = $shop->GetCurrencyCoefficientToShopCurrency($shop_currency_id, $this->wmz_currency_id);
			$wmz_sum = round($order_sum * $coefficient * $this->wmz_coefficient_id, 2);

			/* Информация об алиасе сайта */
			$site = & singleton ('site');
			$site_alias = $site->GetCurrentAlias($shop_row['site_id']);

			/* Получаем путь к магазину */
			$Structure = & singleton('Structure');
			$shop_path = "/".$Structure->GetStructurePath($shop_row['structure_id'], 0);

			$handler_url = 'http://'.$site_alias.$shop_path.'cart/';

			?>
			<h1>Оплата через систему WebMoney</h1>
			<!-- Форма для оплаты через WMR -->
			<p>К оплате <strong><?php echo $wmr_sum?> WMR</strong></p>
			<form id="pay" name="pay" method="post" action="https://merchant.webmoney.ru/lmi/payment.asp">
				<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?php echo $wmr_sum?>">
				<input type="hidden" name="LMI_PAYMENT_DESC" value="Оплата счета N <?php echo $order_row['shop_order_account_number']?> через WMR">
				<input type="hidden" name="LMI_PAYMENT_NO" value="<?php echo $order_row['shop_order_account_number']?>">
				<input type="hidden" name="LMI_PAYEE_PURSE" value="<?php echo $this->wmr?>">
				<input type="hidden" name="LMI_SIM_MODE" value="0">
				<input type="hidden" name="LMI_RESULT_URL" value="<?php echo $handler_url?>">
				<input type="hidden" name="LMI_SUCCESS_URL" value="<?php echo $handler_url."?order_id={$order_row['shop_order_account_number']}&payment=success"?>">
				<input type="hidden" name="LMI_SUCCESS_METHOD" value="POST">
				<input type="hidden" name="LMI_FAIL_URL" value="<?php echo $handler_url."?order_id={$order_row['shop_order_account_number']}&payment=fail"?>">
				<input type="hidden" name="LMI_FAIL_METHOD" value="POST">
				<input type="hidden" name="step_4" value="1">
				<input type="hidden" name="system_of_pay_id" value="<?php echo $order_row['shop_system_of_pay_id']?>">
				<input type="hidden" name="order_id" value="<?php echo $order_row['shop_order_account_number']?>">
				<input type="hidden" name="purse" value="1">
				<div style="margin: 10px 0px; float: left" class="shop_button_block red_button_block">
					<input name="submit" value="Перейти к оплате в WMR" type="submit"/>
				</div>
				<div style="clear: both;"></div>
			</form>

			<form method="post" action="https://payment.cyberplat.ru/cgi-bin/Version1.5/GetForm.cgi">
			<input type="hidden" name="version" value="2.0">
			<input type="hidden" name="message" value="<?php echo "0000056601SM000003090000030900000121\n"
			."0mt0888             00011206\n"
			."                    00000000\n"
			."BEGIN\n"
			."OrderID=10160156&Amount=400&Currency=RUR&PaymentDetails=оплата заказа #10160156&Email=support@cyberplat.com&FirstName=cyberplat&"
			."LastName=support&MiddleName=none&Phone=74"
			."5-4060&Address=Moscow, Kutuzovsky pr.12&Language="
			."ru&return_url=http://mike.cyberplat.com/cgi-bin/cyberpos/result.cgi"
			."END"
			."//BEGIN SIGNATURE"
			."iQBRAwkBAAArxjyPLFgBARB4AgCRvBKq1eCmQ8Fh5Hr2NShLVbcRE0PqJTre0gd3"
			."5MhUk86jOy8JTqjxGp5WYTEsJ4JZaJYRWK5FLmtIRZ05/BWj=l/qV"
			."END SIGNATURE";?>">
		
		<div style="margin: 10px 0px; float: left" class="shop_button_block red_button_block">
				<input name="submit" value="Оплатить" type="submit"/>
		</div>
		
		</form>

			<?php 
		}
	}

	/**
	 * Изменение статуса заказа. Позволяет пользователю внедрять собственные
	 * обработчики при изменении статуса.
	 *
	 * @param array $param массив атрибутов
	 * - $param['shop_order_id'] идентификатор заказа
	 * - $param['prev_order_row'] информация о предыдущем состоянии заказа (доступно не всегда)
	 * - $param['action'] выполняемое действие над заказом, может принимать значения:
	 * edit (редактирование заказа), cancel (отмена заказа),
	 * status (изменение статуса заказа), delete (удаление заказа),
	 * edit_item (редактирование товара в заказе), delete_item (удаление товара в заказе)
	 */
	function ChangeStatus($param = array())
	{
		// Если произошло изменение статуса
		if (isset($param['action']) && in_array($param['action'], array('status', 'edit')))
		{
			$shop_order_id = to_int($param['shop_order_id']);

			$shop = & singleton('shop');

			$order_row = $shop->GetOrder($shop_order_id);

			// Получаем информацию о магазине
			$shop_id = to_int($order_row['shop_shops_id']);

			$shop_row = $shop->GetShop($shop_id);

			$structure = & singleton('Structure');
			$structure_row = $structure->GetStructureItem(to_int($shop_row['structure_id']));

			$lib = new lib();
			$LA = $lib->LoadLibPropertiesValue(to_int($structure_row['lib_id']), to_int($structure_row['structure_id']));

			if ($order_row)
			{
				$DateClass = new DateClass();
				$date_str = $DateClass->datetime_format($order_row['shop_order_date_time']);
			}
			else
			{
				$date_str = '';
			}

			// Если предыдущий статус заказа был 1, то меняем тему на подтверждение
			if (to_int($order_row['shop_order_status_of_pay']) == 1)
			{
				$admin_subject = $GLOBALS['MSG_shops']['shop_order_confirm_admin_subject'];
				$user_subject = $GLOBALS['MSG_shops']['shop_order_confirm_user_subject'];
			}
			else
			{
				$admin_subject = $GLOBALS['MSG_shops']['shop_order_admin_subject'];
				$user_subject = $GLOBALS['MSG_shops']['shop_order_user_subject'];
			}

			$not_paid = isset($param['prev_order_row']) && $param['prev_order_row']['shop_order_status_of_pay'] == 0;
			
			// Письмо отправляем только при установке статуса активности для заказа
			if (to_int($order_row['shop_order_status_of_pay']) == 1 && $not_paid)
			{
				if (trim(to_str($order_row['shop_order_account_number'])) != '')
				{
					$shop_order_account_number = trim(to_str($order_row['shop_order_account_number']));
				}
				else
				{
					$shop_order_account_number = $shop_order_id;
				}
				
				/* Отправляем письмо заказчику */
				$shop->SendMailAboutOrder($shop_id,
				$shop_order_id,
				$order_row['site_users_id'],
				to_str($LA['xsl_letter_to_admin']),
				to_str($LA['xsl_letter_to_user']),
				$order_row['shop_order_users_email'],
				array('admin-content-type' => 'html',
				'user-content-type' => 'html',
				'admin-subject' => sprintf($admin_subject, $shop_order_account_number, $shop_row['shop_shops_name'], $date_str),
				'user-subject' => sprintf($user_subject, $shop_order_account_number, $shop_row['shop_shops_name'], $date_str),
				'email_from_admin' => $order_row['shop_order_users_email']));
			}
		}
	}
}
?>