<?php

/**
 * Обновление валют на текущий день по курсу ЦБ.
 *
 * @package HostCMS 6\cron
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */

require_once(dirname(__FILE__) . '/../' . 'bootstrap.php');

setlocale(LC_NUMERIC, 'POSIX');

$url = 'http://www.cbr.ru/scripts/XML_daily.asp';

$Core_Http = Core_Http::instance()
	->url($url)
	->port(80)
	->timeout(10)
	->additionalHeader('User-Agent', 'Mozilla/5.0 (Windows NT 5.1; rv:26.0) Gecko/20100101 Firefox/26.0')
	->execute();

$xml = $Core_Http->getBody();

$oXml = @simplexml_load_string($xml);

if (is_object($oXml))
{
	$oDefaultCurrency = Core_Entity::factory('Shop_Currency')->getBydefault(1);

	foreach ($oXml->Valute as $Valute)
	{
		$exchangeRate[strval($Valute->CharCode)] = floatval((str_replace(',', '.', $Valute->Value))) / floatval(str_replace(',', '.', $Valute->Nominal));
	}

	if ($oDefaultCurrency->code != 'RUB' && !isset($exchangeRate[$oDefaultCurrency->code]))
	{
		throw new Exception('Default currency does not exist in the XML');
	}

	// любая валюта по умолчанию равна 1
	$oDefaultCurrency->exchange_rate(1)->save();

	/* Рубль - не всегда валюта по умолчанию, но он всегда отсутствует во входящем XML.
	 * Итак, если:
			валюта по умолчанию НЕ рубль
			И рубль присутсвует в списке валют
		ставим рублю его котировку, относительно валюты по умолчанию
	 */
	if ($oDefaultCurrency->code != 'RUB')
	{
		$fRubRate = 1.0 / $exchangeRate[$oDefaultCurrency->code];

		!is_null($oRubCurrency = Core_Entity::factory('Shop_Currency')->getByCode('RUB'))
			&& $oRubCurrency
				->exchange_rate($fRubRate)
				->save();
	}

	foreach ($exchangeRate as $code => $rate)
	{
		// ищем текущую валюту в магазине
		$oCurrentCurrency = Core_Entity::factory('Shop_Currency')->getByCode($code);
		if(is_null($oCurrentCurrency))
		{
			// валюта не найдена, пропускаем итерацию
			continue;
		}

		if ($oDefaultCurrency->code == 'RUB')
		{
			$oCurrentCurrency->exchange_rate = $rate;
			$oCurrentCurrency->save();


		}
		elseif (isset($exchangeRate[$oDefaultCurrency->code]))
		{
			$oCurrentCurrency->exchange_rate = $rate * $fRubRate;
			$oCurrentCurrency->save();
		}

		echo "Updated currency {$code} rate is {$oCurrentCurrency->exchange_rate}\n";
	}

}

echo "OK\n";