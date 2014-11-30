<?php

/**
 * Пример вызова:
 * /usr/bin/php /var/www/site.ru/httpdocs/cron/assist_orders.php
 *
 * Пример вызова с передачей php.ini (например, если бинарный php по умолчанию не видит ZendOptimizer)
 * /usr/bin/php --php-ini /etc/php.ini /var/www/site.ru/httpdocs/cron/assist_orders.php
 *
 * Реальный путь на сервере к корневой директории сайта уточните в службе поддержки хостинга.
 *
 * @package HostCMS 6\cron
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */

@set_time_limit(300);

// Подключаем основные классы с учетов размещения в директории ./cron/
// При размещении в корневой директории '/../' заменить на '/'
require_once(dirname(__FILE__) . '/../' . 'bootstrap.php');

$kernel = & singleton('kernel');

// Загрузка модулей
$kernel->LoadModules();

/* Идентификатор сайта */
define('CURRENT_SITE', 1);

/* Идентификатор сайта (Shop_IDP) в системе ASSIST, например, 123456 */
$Shop_IDP = '123456';

/* Логин для доступа к ASSIST */
$Login = '';

/* Пароль для доступа к ASSIST */
$Password = '';

/*
AS000 * АВТОРИЗАЦИЯ УСПЕШНО ЗАВЕРШЕНА
AS001 * АВТОРИЗАЦИЯ УСПЕШНО ЗАВЕРШЕНА (c CVC2)
AS010 *	АВТОРИЗАЦИЯ УСПЕШНО ЗАВЕРШЕНА
AS011 *	АВТОРИЗАЦИЯ УСПЕШНО ЗАВЕРШЕНА (c CVC2)
AS020 *	ФИНАНСОВОЕ ПОДТВЕРЖДЕНИЕ УСПЕШНО ЗАВЕРШЕНО
Примечание: Звездочкой отмечены ответы при двустадийном механизме работы.
*/
$Success = array('AS000', 'AS001', 'AS010', 'AS011', 'AS020');

/* URL для запроса в формате SOAP, HTTP POST или HTTP GET */
$url = "https://test.assist.ru/results/results.cfm";

// Инициализация констант.
$kernel->InitConstants();

$url .= "?SHOP_ID={$Shop_IDP}&LOGIN={$Login}&PASSWORD={$Password}";

$content = $kernel->GetUrl($url, $port = 80, $timeout = 3600);

// Функция str_getcsv() может быть неопределена
if (!function_exists('str_getcsv'))
{
	function str_getcsv($str, $delimiter = ',', $enclosure = '"') {

		$md5_delimiter = md5($delimiter);
		$md5_delimiter_line = md5(time());

		$buf = '';

		$len = mb_strlen($str);

		$open = false;

		for ($i = 0; $i < $len; $i++)
		{
			$char = $str[$i];

			switch ($char)
			{
				case $delimiter:
					{
						if ($open)
						{
							$buf .= $char;
						}
						else
						{
							$buf .= $md5_delimiter;
						}
						break;
					}
				case $enclosure:
					{
						// Если есть следующий символ и он равен ограничителю
						if ($i+1 < $len && $str[$i+1] == $enclosure)
						{
							$buf .= $enclosure;
							$i++;
						}
						else
						{
							$open = !$open;
						}
						break;
					}
				case "\n":
					{
						if ($open)
						{
							$buf .= $char;
						}
						else
						{
							$buf .= $md5_delimiter_line;
						}
						break;
					}
				default:
					{
						$buf .= $char;
						break;
					}
			}
		}

		$aLines = explode($md5_delimiter_line, $buf);

		$return = array();

		foreach ($aLines as $line)
		{
			$return[] = explode($md5_delimiter, $line);
		}

		return $return;
	}
}

if (!empty($content))
{
	$shop = new shop();

	// Преобразовываем из UTF-8 в windows-1251
	//$content = $kernel->Utf8ToWindows1251($content);

	$content_array = str_getcsv($content, ';');

	// print_r($content_array);

	if (count($content_array) > 0)
	{
		foreach ($content_array as $key => $assist_order_array)
		{
			/*
			$assist_order_array[0] - номер заказа
			$assist_order_array[1] - код ответа
			*/
			if (isset($assist_order_array[0]) && isset($assist_order_array[1]))
			{
				/* Response_Code соответствует успешной оплате */
				if (in_array($assist_order_array[1], $Success))
				{
					$order_id = $assist_order_array[0];

					/* Извлекаем информацию о заказе */
					$order_row = $shop->GetOrder($order_id);

					/*
					Если заказ существует и не оплачен
					*/
					if ($order_row && $order_row['shop_order_status_of_pay'] == 0)
					{
						/* Устанавливаем параметры */
						$param['id'] = $order_row['shop_order_id'];
						$param['shop_shops_id'] = $order_row['shop_shops_id'];

						$currency_row = $shop->GetCurrency($order_row['shop_currency_id']);

						if (!$currency_row)
						{
							continue;
						}

						$order_sum = $shop->GetOrderSum($order_id);

						$shop_row = $shop->GetShop($order_row['shop_shops_id']);

						/* Сравниваем код валюты и сумму оплаты */
						// $assist_order_array[7] - валюта платежа
						if(
						// Совпадает валюта
						mb_strtoupper($currency_row['shop_currency_international_name']) == mb_strtoupper($assist_order_array[7])
						// Совпадает сумма
						&& $order_sum == Core_Type_Conversion::toFloat($assist_order_array[6]))
						{
							/* Проверка прошла успешно!
							Добавляем комментарий */
							$param['system_information'] = "Товар оплачен через ASSIST.\n".
							"Атрибуты:\n".
							"Назначение платежа: {$assist_order_array[4]}\n".
							"Дата платежа: {$assist_order_array[5]}\n".
							"Сумма платежа: {$assist_order_array[6]} {$assist_order_array[7]}\n".
							"Плательщик: {$assist_order_array[10]} {$assist_order_array[11]} {$assist_order_array[12]}\n".
							"E-mail: {$assist_order_array[14]}\n";

							/* Устанавливаем признак оплаты */
							//$param['date_of_pay'] = date("Y-m-d H:i:s");
							//$param['status_of_pay'] = true;

							// Обновляем информацию о заказе
							$shop->InsertOrder($param);

							// Изменяем статус оплаты, генерируем ссылки для эл.товаров, списываем товары
							$shop->SetOrderPaymentStatus($order_id);
						}
						else
						{
							$param['system_information'] = "Заказ оплачен через ASSIST, но не активирован, т.к. не соответствует валюта или сумма.\n".
							"Сумма по нашим данным: {$order_sum} {$currency_row['shop_currency_international_name']} \n".
							"Сумма по данным ASSIST: {$assist_order_array[6]} {$assist_order_array[7]}\n";

							// Обновляем информацию о заказе
							$shop->InsertOrder($param);
						}

						$structure = & singleton('Structure');
						$structure_row = $structure->GetStructureItem(Core_Type_Conversion::toInt($shop_row['structure_id']));

						$lib = new lib();
						$LA = $lib->LoadLibPropertiesValue(Core_Type_Conversion::toInt($structure_row['lib_id']), Core_Type_Conversion::toInt($structure_row['structure_id']));

						// Отправляем письмо администратору о подтверждении платежа
						$shop->SendMailAboutOrder($order_row['shop_shops_id'], $order_id, $order_row['site_users_id'],
						Core_Type_Conversion::toStr($LA['xsl_letter_to_admin']),
						Core_Type_Conversion::toStr($LA['xsl_letter_to_user']),
						$order_row['shop_order_users_email'],
						array(
						'admin-content-type' => 'html',
						'user-content-type' => 'html',
						'admin-subject' => sprintf($GLOBALS['MSG_shops']['shop_order_confirm_admin_subject'], $order_id, $shop_row['shop_shops_name'], $param['date_of_pay']),
						'user-subject' => sprintf($GLOBALS['MSG_shops']['shop_order_confirm_user_subject'], $order_id, $shop_row['shop_shops_name'], $param['date_of_pay'])));

					}
				}
			}
		}
	}
	?>OK.<?php
}
else
{
	?>Content is empty.<?php
}