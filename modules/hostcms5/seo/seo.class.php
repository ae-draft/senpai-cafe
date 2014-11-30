<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Поисковой оптимизации".
 * 
 * Файл: /modules/seo/seo.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class Seo
{
	/**
	* Кэш запрошенных страниц
	*
	* @var array - массив кэшированных страниц
	* @access private
	*/
	var $CachePage = array();

	/**
	* Отображение стрелочек динамики изменения значений
	*
	* @param int $prev_value предыдущее значение
	* @param int $current_value текущее значение
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $prev_value = 1;
	* $current_value = 10;
	*
	* $Seo->ShowArrow($prev_value, $current_value);
	* ?>
	* </code>
	*/
	function ShowArrow($prev_value, $current_value)
	{
		throw new Core_Exception('Method ShowArrow() does not allow');
	}

	/**
	 * Проверка возможности использования Яндекс.XML
	 */
	function AllowYandexXml()
	{
		throw new Core_Exception('Method AllowYandexXml() does not allow');
	}
	
	/**
	* Отображение стрелочек динамики изменения значений поисковых запросов
	*
	* @param int $prev_value предыдущее значение
	* @param int $current_value текущее значение
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $prev_value = 1;
	* $current_value = 10;
	*
	* $Seo->ShowArrowQuery($prev_value, $current_value);
	* ?>
	* </code>
	*/
	function ShowArrowQuery($prev_value, $current_value)
	{
		throw new Core_Exception('Method ShowArrowQuery() does not allow');
	}

	/**
	* Очищает кэш запрошенных страниц в $this->CachePage
	*
	*/
	function ClearCachePage()
	{
		$this->CachePage = array();
	}

	/**
	* Поисковый запрос URL и сохранение полученной страницы в кэше
	*
	* @param str $url URL-документа
	* @param boolean $use_cache использовать ли кэширование запрошенной страницы
	* @return str контент документа
	*/
	function GetUrl($url, $use_cache = true)
	{
		throw new Core_Exception('Method GetUrl() does not allow');
	}

	/**
	* Удаление характеристик страницы
	*
	* @param int $seo_characteristic_id идентификатор характеристики, которую необходимо удалить
	* @return resource
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $seo_characteristic_id = 1;
	*
	* $resource = $Seo->DeleteCharacteristic($seo_characteristic_id);
	*
	* // Распечатаем результат
	* echo $resource;
	* ?>
	* </code>
	*/
	function DeleteCharacteristic($seo_characteristic_id)
	{
		$oSeo = Core_Entity::factory('Seo')->find($seo_characteristic_id);

		return !is_null($oSeo->id)
			? $oSeo->markDeleted()
			: false;
	}

	/**
	* Получение характеристики
	*
	* @param int $seo_characteristic_id идентификатор характеристики, которую необходимо получить
	* @return mixed массив с информацией или false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $seo_characteristic_id = 2;
	*
	* $resource = $Seo->GetCharacteristic($seo_characteristic_id);
	*
	* // Распечатаем результат
	* print_r ($resource);
	* ?>
	* </code>
	*/
	function GetCharacteristic($seo_characteristic_id)
	{
		$seo_characteristic_id = intval($seo_characteristic_id);

		$oSeo = Core_Entity::factory('Seo')->find($seo_characteristic_id);

		if (!is_null($oSeo->id))
		{
			return array(
				'seo_characteristic_id' => $oSeo->id,
				'site_id' => $oSeo->site_id,
				'seo_characteristic_date_time' => $oSeo->datetime,
				'seo_characteristic_pr' => $oSeo->pr,
				'seo_characteristic_yc' => $oSeo->tcy,
				'seo_characteristic_yc_rubric' => $oSeo->tcy_topic,
				'seo_characteristic_catalog_yandex' => $oSeo->yandex_catalog,
				'seo_characteristic_catalog_rambler' => $oSeo->rambler_catalog,
				'seo_characteristic_catalog_aport' => $oSeo->aport_catalog,
				'seo_characteristic_catalog_dmoz' => $oSeo->dmoz_catalog,
				'seo_characteristic_catalog_mail' => $oSeo->mail_catalog,
				'seo_characteristic_links_google' => $oSeo->google_links,
				'seo_characteristic_links_yandex' => $oSeo->yandex_links,
				'seo_characteristic_links_yahoo' => $oSeo->yahoo_links,
				'seo_characteristic_links_msn' => $oSeo->bing_links,
				'seo_characteristic_indexed_yandex' => $oSeo->yandex_indexed,
				'seo_characteristic_indexed_rambler' => $oSeo->rambler_indexed,
				'seo_characteristic_indexed_google' => $oSeo->google_indexed,
				'seo_characteristic_indexed_yahoo' => $oSeo->yahoo_indexed,
				'seo_characteristic_indexed_msn' => $oSeo->bing_indexed,
				'seo_characteristic_counter_rambler' => $oSeo->rambler_counter,
				'seo_characteristic_counter_spylog' => $oSeo->spylog_counter,
				'seo_characteristic_counter_hotlog' => $oSeo->hotlog_counter,
				'seo_characteristic_counter_liveinternet' => $oSeo->liveinternet_counter,
				'seo_characteristic_counter_mail' => $oSeo->mail_counter,
				'users_id' => $oSeo->user_id,
				'seo_characteristic_indexed_aport' => 0
			);
		}

		return FALSE;
	}

	/**
	* Добавление характеристики страницы
	*
	* @param array $param Массив параметров
	* - $param['seo_characteristic_date_time'] datetime Время и дата анализа страницы в формате ДД.ММ.ГГГГ ЧЧ:ММ:СС
	* - $param['seo_characteristic_yc_rubric'] str Рубрика в поисковой системе Яндекс.ru
	* - $param['seo_characteristic_yc'] int Индекс цитирования
	* - $param['site_id'] int Идентификатор сайта
	* - $param['seo_characteristic_catalog_yandex'] bool Наличие в каталоге Яндекс.ru
	* - $param['seo_characteristic_links_yandex'] int Ссылающиеся страницы по данным Яндекс.ru
	* - $param['seo_characteristic_pr'] int Значение Google PageRank
	* - $param['seo_characteristic_links_google'] int Ссылающиеся страницы по данным Googl.ru
	* - $param['seo_characteristic_links_yahoo'] int Ссылающиеся страницы по данным Yahoo.com
	* - $param['seo_characteristic_links_msn'] int Ссылающиеся страницы по данным Bing.com
	* - $param['seo_characteristic_indexed_aport'] int Проиндексировано страниц сайта в Aport.ru
	* - $param['seo_characteristic_indexed_yandex'] int Проиндексировано страниц сайта в Яндекс.ru
	* - $param['seo_characteristic_indexed_yahoo'] int Проиндексированно страниц сайта в Yahoo.com
	* - $param['seo_characteristic_indexed_msn'] int Проиндексировано страниц сайта в Bing.com
	* - $param['seo_characteristic_indexed_rambler'] int Проиндексировано страниц сайта в Rambler.ru
	* - $param['seo_characteristic_indexed_google'] int Проиндексировано страниц сайта в Google.ru
	* - $param['seo_characteristic_catalog_rambler'] bool Наличие в каталоге Rambler.ru
	* - $param['seo_characteristic_catalog_mail'] bool Наличие в каталоге Mail.ru
	* - $param['seo_characteristic_catalog_dmoz'] bool Наличие в каталоге Dmoz.ru
	* - $param['seo_characteristic_catalog_aport'] bool Наличие в каталоге Aport.ru
	* - $param['seo_characteristic_catalog_yandex'] bool Наличие в каталоге Яндекс.ru
	* - $param['seo_characteristic_counter_rambler'] bool Наличие счетчика Rambler's Top100
	* - $param['seo_characteristic_counter_spylog'] bool Наличие счетчика SpyLog.ru
	* - $param['seo_characteristic_counter_hotlog'] bool Наличие счетчика HotLog.ru
	* - $param['seo_characteristic_counter_mail'] bool Наличие счетчика Mail.ru
	* - $param['seo_characteristic_counter_liveinternet'] bool Наличие счетчика LiveInternet.ru
	* @return mixed Идентификатор вставленной записи, либо false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $param['seo_characteristic_date_time'] = date('d.m.Y H:i:s');
	* $param['seo_characteristic_yc_rubric'] = 'Рубрика';
	* $param['seo_characteristic_yc'] = 1000;
	* $param['site_id'] = CURRENT_SITE;
	* $param['seo_characteristic_catalog_yandex'] = true;
	* $param['seo_characteristic_links_yandex'] = 15000;
	* $param['seo_characteristic_pr'] = 5;
	* $param['seo_characteristic_links_google'] = 14500;
	* $param['seo_characteristic_links_yahoo'] = 12000;
	* $param['seo_characteristic_links_msn'] = 12500;
	* $param['seo_characteristic_indexed_aport'] = 25000;
	* $param['seo_characteristic_indexed_yandex'] = 27000;
	* $param['seo_characteristic_indexed_yahoo'] = 25500;
	* $param['seo_characteristic_indexed_msn'] = 26000;
	* $param['seo_characteristic_indexed_rambler'] = 26500;
	* $param['seo_characteristic_indexed_google'] = 27000;
	* $param['seo_characteristic_catalog_rambler'] = true;
	* $param['seo_characteristic_catalog_mail'] = true;
	* $param['seo_characteristic_catalog_dmoz'] = true;
	* $param['seo_characteristic_catalog_aport'] = true;
	* $param['seo_characteristic_catalog_yandex'] = true;
	* $param['seo_characteristic_counter_rambler'] = false;
	* $param['seo_characteristic_counter_spylog'] = false;
	* $param['seo_characteristic_counter_hotlog'] = false;
	* $param['seo_characteristic_counter_mail'] = false;
	* $param['seo_characteristic_counter_liveinternet'] = true;
	*
	* $newid = $Seo->InsertCharacteristic($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function InsertCharacteristic($param = array())
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!$param['seo_characteristic_id'] = Core_Type_Conversion::toInt($param['seo_characteristic_id']))
		{
			$param['seo_characteristic_id'] = NULL;
		}

		$oSeo = Core_Entity::factory('Seo', $param['seo_characteristic_id']);

		if (Core_Type_Conversion::toInt($param['users_id']))
		{
			$oSeo->user_id = intval($param['users_id']);
		}

		if (Core_Type_Conversion::toInt($param['site_id']))
		{
			$oSeo->site_id = intval($param['site_id']);
		}

		if (isset($param['seo_characteristic_date_time']))
		{
			$param['seo_characteristic_date_time'] = Core_Date::datetime2sql($param['seo_characteristic_date_time']);

			if (preg_match("'^([\d]{4})-([\d]{1,2})-([\d]{1,2}) ([\d]{1,2}):([\d]{1,2}):([\d]{1,2})'u", $param['seo_characteristic_date_time']))
			{
				$oSeo->datetime = $param['seo_characteristic_date_time'];
			}
		}

		$oSeo->tcy_topic = Core_Type_Conversion::toStr($param['seo_characteristic_yc_rubric']);
		$oSeo->tcy = Core_Type_Conversion::toInt($param['seo_characteristic_yc']);
		$oSeo->pr = Core_Type_Conversion::toInt($param['seo_characteristic_pr']);
		$oSeo->google_links = Core_Type_Conversion::toInt($param['seo_characteristic_links_google']);
		$oSeo->yandex_links = Core_Type_Conversion::toInt($param['seo_characteristic_links_yandex']);
		$oSeo->yahoo_links = Core_Type_Conversion::toInt($param['seo_characteristic_links_yahoo']);
		$oSeo->bing_links = Core_Type_Conversion::toInt($param['seo_characteristic_links_msn']);
		$oSeo->yandex_indexed = Core_Type_Conversion::toInt($param['seo_characteristic_indexed_yandex']);
		$oSeo->yahoo_indexed = Core_Type_Conversion::toInt($param['seo_characteristic_indexed_yahoo']);
		$oSeo->bing_indexed = Core_Type_Conversion::toInt($param['seo_characteristic_indexed_msn']);
		$oSeo->rambler_indexed = Core_Type_Conversion::toInt($param['seo_characteristic_indexed_rambler']);
		$oSeo->google_indexed = Core_Type_Conversion::toInt($param['seo_characteristic_indexed_google']);
		$oSeo->yandex_catalog = Core_Type_Conversion::toInt($param['seo_characteristic_catalog_yandex']);
		$oSeo->rambler_catalog = Core_Type_Conversion::toInt($param['seo_characteristic_catalog_rambler']);
		$oSeo->mail_catalog = Core_Type_Conversion::toInt($param['seo_characteristic_catalog_mail']);
		$oSeo->dmoz_catalog = Core_Type_Conversion::toInt($param['seo_characteristic_catalog_dmoz']);
		$oSeo->aport_catalog = Core_Type_Conversion::toInt($param['seo_characteristic_catalog_aport']);
		$oSeo->rambler_counter = Core_Type_Conversion::toInt($param['seo_characteristic_counter_rambler']);
		$oSeo->spylog_counter = Core_Type_Conversion::toInt($param['seo_characteristic_counter_spylog']);
		$oSeo->hotlog_counter = Core_Type_Conversion::toInt($param['seo_characteristic_counter_hotlog']);
		$oSeo->mail_counter = Core_Type_Conversion::toInt($param['seo_characteristic_counter_mail']);
		$oSeo->liveinternet_counter = Core_Type_Conversion::toInt($param['seo_characteristic_counter_liveinternet']);

		$oSeo->save();

		return $oSeo->id;
	}

	/**
	* Поиск значения Google PageRank. Основан на данных Google toolbar. Возвращает значение PageRank страницы
	*
	* @param string $url Адрес сайта
	* @return int PageRank значение
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $url = 'www.hostcms.ru';
	*
	* $pr = $Seo->GetPageRank($url);
	*
	* // Распечатаем результат
	* echo $pr;
	* ?>
	* </code>
	*/
	function GetPageRank($url)
	{
		return Seo_Controller::instance()->getPageRank($url);
	}

	/**
    * Сервисный метод
    */
	function ZeroFillShift($a, $b)
	{
		throw new Core_Exception('Method ZeroFillShift() does not allow');
	}

	/**
    * Сервисный метод хеширования
    * 
    * @param str $value Строка
    * @access private
    * @return mixed 
    */
	function GoogleHash($value)
	{
		throw new Core_Exception('Method GoogleHash() does not allow');
	}

	/**
	* Определения наличия страницы в каталоге Yandex, тИЦ, темы, страны и региона страницы
	*
	* @param str $domain Адрес сайта
	* @return array Массив значений
	* - $info['tyc'] int тИЦ страницы
	* - $info['topic'] str Тема страницы 
	* - $info['country'] str Страна
	* - $info['region'] str Регион 
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $row = $Seo->GetYandexCatalog($domain);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetYandexCatalog($domain)
	{
		$aYandexCatalog = Seo_Controller::instance()->getYandexCatalog($domain);

		return array(
			'tyc' => $aYandexCatalog->tcy,
			'topic' => $aYandexCatalog->tcy_topic,
			'country' => $aYandexCatalog->country,
			'region' => $aYandexCatalog->region
		);
	}

	/**
	* Определение наличия страницы в каталоге Rambler
	*
	* @param string $domain Адрес сайта
	* @return bool 
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetRamblerCatalog($domain);
	*
	* if ($result)
	* {
	* 	echo "Сайт присутствует в каталоге Rambler";
	* }
	* else 
	* {
	* 	echo "Сайт отсутствует в каталоге Rambler";
	* }
	* ?>
	* </code>
	*/
	function GetRamblerCatalog($domain)
	{
		return Seo_Controller::instance()->getRamblerCatalog($domain);
	}

	/**
	* Определение наличия страницы в каталоге Апорт
	*
	* @param string $domain Адрес сайта
	* @return bool
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetAportCatalog($domain);
	*
	* if ($result)
	* {
	* 	echo "Сайт присутствует в каталоге Апорт";
	* }
	* else 
	* {
	* 	echo "Сайт отсутствует в каталоге Апорт";
	* }
	* ?>
	* </code>
	*/
	function GetAportCatalog($domain)
	{
		return Seo_Controller::instance()->getAportCatalog($domain);
	}

	/**
	* Определение наличия страницы в каталоге Dmoz
	*
	* @param string $domain Адрес сайта
	* @return bool
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetDmozCatalog($domain);
	*
	* if ($result)
	* {
	* 	echo "Сайт присутствует в каталоге Dmoz";
	* }
	* else 
	* {
	* 	echo "Сайт отсутствует в каталоге Dmoz";
	* }
	* ?>
	* </code>
	*/
	function GetDmozCatalog($domain)
	{
		return Seo_Controller::instance()->getDmozCatalog($domain);
	}

	/**
	* Определение наличия страницы в каталоге Mail.ru
	*
	* @param string $domain Адрес сайта
	* @return bool
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetMailCatalog($domain);
	*
	* if ($result)
	* {
	* 	echo "Сайт присутствует в каталоге Mail.ru";
	* }
	* else 
	* {
	* 	echo "Сайт отсутствует в каталоге Mail.ru";
	* }
	* ?>
	* </code>
	*/
	function GetMailCatalog($domain)
	{
		return Seo_Controller::instance()->getMailCatalog($domain);
	}

	/**
	* Заменяет "млн" и "тыс" на соответствующее количество нулей
	*
	* @param string $str Строка с числом и наименованием разряда словами
	* @return int
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $str = '100 тыс.';
	*
	* $result = $Seo->EdZero($str);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function EdZero($str)
	{
		throw new Core_Exception('Method EdZero() does not allow');
	}

	function ParseYandex($file)
	{
		throw new Core_Exception('Method ParseYandex() does not allow');
	}
	
	function ParseGoogle($file)
	{
		throw new Core_Exception('Method ParseGoogle() does not allow');
	}
	
	function YandexXmlRequest($query, $page = 0)
	{
		throw new Core_Exception('Method YandexXmlRequest() does not allow');
	}
	
	/**
	* Определение количества проиндексированных странниц в Яндекс.ру
	*
	* @param string $domain Адрес сайта
	* @return int количество проиндексированных страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetIndexYandex($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetIndexYandex($domain)
	{
		return Seo_Controller::instance()->getYandexIndex($domain);
	}

	/**
	* Определение количества проиндексированных странниц сервисом Rambler
	*
	* @param string $domain Адрес сайта
	* @return int количество проиндексированных страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetIndexRambler($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetIndexRambler($domain)
	{
		return Seo_Controller::instance()->getRamblerIndex($domain);
	}

	/**
	* Определяет количество проиндексированных странниц сервисом Google
	*
	* @param string $domain Адрес сайта
	* @return int количество проиндексированных страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetIndexGoogle($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetIndexGoogle($domain)
	{
		return Seo_Controller::instance()->getGoogleIndex($domain);
	}

	/**
	* Определение количества проиндексированных странниц сервисом Апорт
	*
	* @param string $domain Адрес сайта
	* @return int количество проиндексированных страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetIndexAport($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetIndexAport($domain)
	{
		return 0;
	}

	/**
	* Определение количества проиндексированных странниц сервисом Yahoo
	*
	* @param string $domain Адрес сайта
	* @return int количество проиндексированных страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetIndexYahoo($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetIndexYahoo($domain)
	{
		return Seo_Controller::instance()->getYahooIndex($domain);
	}

	/**
	* Определение количества проиндексированных странниц сервисом Bing.com
	*
	* @param string $domain Адрес сайта
	* @return int количество проиндексированных страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetIndexMsn($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetIndexMsn($domain)
	{
		return Seo_Controller::instance()->getBingIndex($domain);
	}

	/**
	* Определение количества ссылающихся страниц с сервиса Yandex
	*
	* @param string $domain Адрес сайта
	* @return int Количество ссылающихся страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetLinksYandex($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/	
	function GetLinksYandex($domain)
	{
		return Seo_Controller::instance()->getYandexLinks($domain);
	}

	/**
	* Определение количества ссылающихся страниц с сервиса Google
	*
	* @param string $domain Адрес сайта
	* @return int Количество ссылающихся страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetLinksGoogle($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetLinksGoogle($domain)
	{
		return Seo_Controller::instance()->getGoogleLinks($domain);
	}

	/**
	* Определение количества ссылающихся страниц с сервиса Yahoo
	*
	* @param string $domain Адрес сайта
	* @return int Количество ссылающихся страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetLinksYahoo($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetLinksYahoo($domain)
	{
		return Seo_Controller::instance()->getYahooLinks($domain);
	}

	/**
	* Определение количества ссылающихся страниц с сервиса Bing.com
	*
	* @param string $domain Адрес сайта
	* @return int Количество ссылающихся страниц
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetLinksMsn($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetLinksMsn($domain)
	{
		return Seo_Controller::instance()->getBingLinks($domain);
	}

	/**
	* Проверка наличия счетчика статистики SpyLog
	*
	* @param string $domain Адрес сайта
	* @return mixed номер счетчика или false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetCounterSpyLog($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetCounterSpyLog($domain)
	{
		return Seo_Controller::instance()->getSpyLogCounter($domain);
	}

	/**
	* Проверка наличия счетчика статистики Rambler
	*
	* @param string $domain Адрес сайта
	* @return mixed Номер счетчика, или false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetCounterRambler($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetCounterRambler ($domain)
	{
		return Seo_Controller::instance()->getRamblerCounter($domain);
	}

	/**
	* Проверка наличия счетчика статистики Mail
	*
	* @param string $domain Адрес сайта
	* @return mixed Номер счетчика, или false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetCounterMail($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetCounterMail($domain)
	{
		return Seo_Controller::instance()->getMailCounter($domain);
	}

	/**
	* Проверка наличия счетчика статистики HotLog
	*
	* @param string $domain Адрес сайта
	* @return mixed Номер счетчика, или false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetCounterHotLog($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetCounterHotLog($domain)
	{
		return Seo_Controller::instance()->getHotLogCounter($domain);
	}

	/**
	* Проверка наличия счетчика статистики LiveInternet
	*
	* @param string $domain Адрес сайта
	* @return mixed Адрес на страницу со статистикой, или false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->GetCounterLiveInternet($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetCounterLiveInternet($domain)
	{
		return Seo_Controller::instance()->getLiveInternetCounter($domain);
	}

	function GetLr()
	{
		throw new Core_Exception('Method GetLr() does not allow');
	}
	
	/**
 	* Определение позиции сайта в поисковой системе Yandex
 	*
 	* @param string $domain Адрес сайта
 	* @param string $text Поисковый запрос
 	* @param array $param массив дополнительных параметров
 	* - $param['search_subdomain'] искать ли поддомены переданного домена. по умолчанию true
 	* - $param['page_count'] количество просматриваемых страниц. по умолчанию 5
 	* @return mixed номер позиции или false
 	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	* $text = 'cms';
	*
	* $result = $Seo->GetPosYandex ($domain, $text);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
 	*/
	function GetPosYandex ($domain, $text, $param = array())
	{
		return Seo_Controller::instance()->getYandexPosition ($domain, $text, $param);
	}

	/**
 	* Определение позиции сайта в поисковой системе Rambler
 	*
 	* @param string $domain Адрес сайта
 	* @param string $text Поисковый запрос
 	* @param array $param Массив дополнительных параметров
 	* - $param['search_subdomain'] Искать ли поддомены переданного домена. по умолчанию true
 	* - $param['page_count'] Количество просматриваемых страниц. по умолчанию 5
 	* @return mixed Номер позиции или false
 	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	* $text = 'cms';
	*
	* $result = $Seo->GetPosRambler ($domain, $text);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
 	*/
	function GetPosRambler ($domain, $text, $param = array())
	{
		return Seo_Controller::instance()->getRamblerPosition ($domain, $text, $param);
	}

	/**
 	* Определение позиции сайта в поисковой системе Google
 	*
 	* @param string $domain Адрес сайта
 	* @param string $text Поисковый запрос
 	* @param array $param Массив дополнительных параметров
 	* - $param['search_subdomain'] Искать ли поддомены переданного домена. по умолчанию true
 	* - $param['page_count'] Количество просматриваемых страниц. по умолчанию 5
 	* @return mixed Номер позиции или false
 	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	* $text = 'cms';
	*
	* $result = $Seo->GetPosGoogle ($domain, $text);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
 	*/
	function GetPosGoogle ($domain, $text, $param = array())
	{
		return Seo_Controller::instance()->getGooglePosition ($domain, $text, $param);
	}

	/**
 	* Определение позиции сайта в поисковой системе Апорт
 	*
 	* @param string $domain Адрес сайта
 	* @param string $text Поисковый запрос
 	* @param array $param Массив дополнительных параметров
 	* - $param['search_subdomain'] Искать ли поддомены переданного домена. по умолчанию true
 	* - $param['page_count'] Количество просматриваемых страниц. по умолчанию 5
 	* @return mixed Номер позиции или false
 	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	* $text = 'cms';
	*
	* $result = $Seo->GetPosAport($domain, $text);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
 	*/
	function GetPosAport($domain, $text, $param = array())
	{
		return 0;
	}

	/**
 	* Определение позиции сайта в поисковой системе GoGo
 	*
 	* @param string $domain Адрес сайта
 	* @param string $text Поисковый запрос
 	* @param array $param Массив дополнительных параметров
 	* - $param['search_subdomain'] Искать ли поддомены переданного домена. по умолчанию true
 	* - $param['page_count'] Количество просматриваемых страниц. по умолчанию 5
 	* @return mixed Номер позиции или false
 	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	* $text = 'cms';
	*
	* $result = $Seo->GetPosGogo ($domain, $text);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
 	*/
	function GetPosGogo ($domain, $text, $param = array())
	{
		return 0;
	}
	
	/**
 	* Определение позиции сайта в поисковой системе Yahoo
 	*
 	* @param string $domain Адрес сайта
 	* @param string $text Поисковый запрос
 	* @param array $param Массив дополнительных параметров
 	* - $param['search_subdomain'] Искать ли поддомены переданного домена. по умолчанию true
 	* - $param['page_count'] Количество просматриваемых страниц. по умолчанию 5
 	* @return mixed Номер позиции или false
 	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	* $text = 'cms';
	*
	* $result = $Seo->GetPosYahoo ($domain, $text);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
 	*/
	function GetPosYahoo ($domain, $text, $param = array())
	{
		return Seo_Controller::instance()->getYahooPosition ($domain, $text, $param);
	}

	/**
	* Обрезает "www" у домена
	*
	* @param str $domain Анализируемый адрес домена
	* @return str Адрес домена без "www"
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	*
	* $result = $Seo->UrlWww ($domain);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function UrlWww ($domain)
	{
		throw new Core_Exception('Method UrlWww() does not allow');
	}

	/**
 	* Определение позиции сайта в поисковой системе Bing.com
 	*
 	* @param string $domain Адрес сайта
 	* @param string $text Поисковый запрос
 	* @param array $param Массив дополнительных параметров
 	* - $param['search_subdomain'] Искать ли поддомены переданного домена. по умолчанию true
 	* - $param['page_count'] Количество просматриваемых страниц. по умолчанию 5
 	* @return mixed Номер позиции или false
 	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $domain = 'www.hostcms.ru';
	* $text = 'cms';
	*
	* $result = $Seo->GetPosLivesearch ($domain, $text);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
 	*/
	function GetPosLivesearch ($domain, $text, $param = array())
	{
		return Seo_Controller::instance()->getBingPosition ($domain, $text, $param);
	}

	/**
	* Получение списка поисковых запросов из БД
	*
	* @param int $edit_query_id Идентификатор запроса, который необходимо получить
	* @return mixed Массив с записью БД, либо false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $edit_query_id = 1;
	*
	* $row = $Seo->GetQuery($edit_query_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetQuery($edit_query_id)
	{
		$edit_query_id = intval($edit_query_id);

		if (!$edit_query_id)
		{
			$edit_query_id = NULL;
		}

		$oSeo_Query = Core_Entity::factory('Seo_Query')->find($edit_query_id);

		if (!is_null($oSeo_Query->id))
		{
			return array(
				'seo_search_query_id' => $oSeo_Query->id,
				'site_id' => $oSeo_Query->site_id,
				'seo_search_query_value' => $oSeo_Query->query,
				'users_id' => $oSeo_Query->user_id
			);
		}

		return FALSE;
	}

	/**
	* Получение списка поисковых запросов
	*
	* @param int $site_id Идентификатор сайта
	* @return resourse
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $site_id = CURRENT_SITE;
	*
	* $resource = $Seo->GetAllQuery($site_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	*/
	function GetAllQuery($site_id)
	{
		$site_id = intval($site_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'seo_search_query_id'),
				'site_id',
				array('query', 'seo_search_query_value'),
				array('user_id', 'users_id'))
			->from('seo_queries')
			->where('site_id', '=', $site_id)
			->where('deleted', '=', '0');

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Удаление запроса
	*
	* @param int $seo_search_query_id - идентификатор запроса, который необходимо удалить
	* @return resource
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $seo_search_query_id = 3;
	*
	* $resource = $Seo->DeleteQuery($seo_search_query_id);
	*
	* if ($resource)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* else 
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	*/
	function DeleteQuery($seo_search_query_id)
	{
		$oSeo_Query = Core_Entity::factory('Seo_Query')->find($seo_search_query_id);

		return !is_null($oSeo_Query->id) 
			? $oSeo_Query->markDeleted()
			: FALSE;
	}

	/**
	* Добавление/обновление поискового запроса в БД
	*
	* @param array $param Массив параметров
	* - int $param['seo_search_query_id'] Идентификатор записи
	* - int $param['site_id'] Идентификатор сайта
	* - str $param['seo_search_query_value'] Ключевые слова	 
	* 
	* @return mixed Идентификатор вставленной записи, либо false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $param['seo_search_query_id'] = 0;
	* $param['site_id'] = CURRENT_SITE;
	* $param['seo_search_query_value'] = 'система управления контентом';
	*
	* $newid = $Seo->InsertQuery($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function InsertQuery($param = array())
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!$param['seo_search_query_id'] = Core_Type_Conversion::toInt($param['seo_search_query_id']))
		{
			$param['seo_search_query_id'] = NULL;
		}

		$oSeo_Query = Core_Entity::factory('Seo_Query', $param['seo_search_query_id']);

		if (Core_Type_Conversion::toInt($param['users_id']))
		{
			$oSeo_Query->user_id = intval($param['users_id']);
		}

		if (Core_Type_Conversion::toInt($param['site_id']))
		{
			$oSeo_Query->site_id = intval($param['site_id']);
		}

		if (trim(Core_Type_Conversion::toStr($param['seo_search_query_value'])) != '')
		{
			$oSeo_Query->query = $param['seo_search_query_value'];

			$oSeo_Query->save();

			return $oSeo_Query->id;
		}

		return FALSE;
	}

	/**
	* Удаление позиции в поисковой системе из БД
	*
	* @param int $seo_search_query_id - идентификатор позиции в поисковой системе, которую необходимо удалить
	* @return resource
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $seo_position_search_query_id = 1;
	*
	* $resource = $Seo->DeletePositionSearch($seo_position_search_query_id);
	*
	* if ($resource)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* 	else 
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	*/
	function DeletePositionSearch($seo_position_search_query_id)
	{
		$oSeo_Query_Position = Core_Entity::factory('Seo_Query_Position')->find($seo_position_search_query_id);

		return !is_null($oSeo_Query_Position->id)
			? $oSeo_Query_Position->markDeleted()
			: FALSE;
	}

	/**
	* Получение списка позиций в поисковой системе из БД
	*
	* @param int $seo_characteristic_id - идентификатор характеристики, которую необходимо получить
	* @return mixed массив с информацией или false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $seo_position_search_query_id = 2;
	*
	* $row = $Seo->GetPositionSearch($seo_position_search_query_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetPositionSearch($seo_position_search_query_id)
	{
		$oSeo_Query_Position = Core_Entity::factory('Seo_Query_Position')->find($seo_position_search_query_id);

		if (!is_null($oSeo_Query_Position->id))
		{
			return array(
				'seo_position_search_query_id' => $oSeo_Query_Position->id,
				'seo_search_query_id' => $oSeo_Query_Position->seo_query_id,
				'seo_position_search_query_date_time' => $oSeo_Query_Position->datetime,
				'seo_position_search_query_yandex' => $oSeo_Query_Position->yandex,
				'seo_position_search_query_rambler' => $oSeo_Query_Position->rambler,
				'seo_position_search_query_google' => $oSeo_Query_Position->google,
				'seo_position_search_query_aport' => 0,
				'seo_position_search_query_gogo' => 0,
				'seo_position_search_query_yahoo' => $oSeo_Query_Position->yahoo,
				'seo_position_search_query_livesearch' => $oSeo_Query_Position->bing,
				'users_id' => $oSeo_Query_Position->user_id
			);
		}

		return FALSE;
	}

	/**
	* Добавление/обновление статистики по поисковым запросам в БД
	*
	* @param array $param Массив параметров
	* - int $param['seo_position_search_query_id'] Идентификатор позиции в поисковой системе
	* - int $param['seo_search_query_id'] Идентификатор поискового запроса
	* - datetime $param['seo_position_search_query_date_time'] Дата
	* - int $param['seo_position_search_query_yandex'] позиция в яндекс
	* - int $param['seo_position_search_query_rambler'] позиция в Rambler
	* - int $param['seo_position_search_query_google'] позиция в Google
	* - int $param['seo_position_search_query_yahoo'] позиция в Yahoo
	* - int $param['seo_position_search_query_livesearch'] позиция в Bing
	* @return mixed Идентификатор вствленной записи, либо false
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $param['seo_position_search_query_id'] = 0;
	* $param['seo_search_query_id'] = 1;
	* $param['seo_position_search_query_date_time'] = date('Y.m.d H:i:s');
	* $param['seo_position_search_query_yandex'] = 1;
	* $param['seo_position_search_query_rambler'] = 1;
	* $param['seo_position_search_query_google'] = 1;
	* $param['seo_position_search_query_yahoo'] = 1;
	* $param['seo_position_search_query_livesearch'] = 1;
	*
	* $newid = $Seo->InsertPositionSearch($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function InsertPositionSearch($param = array())
	{
		if (!$param['seo_position_search_query_id'] = Core_Type_Conversion::toInt($param['seo_position_search_query_id']))
		{
			$param['seo_position_search_query_id'] = NULL;
		}

		$oSeo_Query_Position = Core_Entity::factory('Seo_Query_Position', $param['seo_position_search_query_id']);

		// ID запроса
		if (Core_Type_Conversion::toInt($param['seo_search_query_id']))
		{
			$oSeo_Query_Position->seo_query_id = intval($param['seo_search_query_id']);

			if (isset($param['seo_position_search_query_date_time']))
			{
				$param['seo_position_search_query_date_time'] = Core_Date::datetime2sql($param['seo_position_search_query_date_time']);

				if (preg_match("'^([\d]{4})-([\d]{1,2})-([\d]{1,2}) ([\d]{1,2}):([\d]{1,2}):([\d]{1,2})'u", $param['seo_position_search_query_date_time']))
				{
					$oSeo_Query_Position->datetime = $param['seo_position_search_query_date_time'];
				}
			}

			$oSeo_Query_Position->yandex = Core_Type_Conversion::toInt($param['seo_position_search_query_yandex']);
			$oSeo_Query_Position->rambler = Core_Type_Conversion::toInt($param['seo_position_search_query_rambler']);
			$oSeo_Query_Position->google = Core_Type_Conversion::toInt($param['seo_position_search_query_google']);
			$oSeo_Query_Position->yahoo = Core_Type_Conversion::toInt($param['seo_position_search_query_yahoo']);
			$oSeo_Query_Position->bing = Core_Type_Conversion::toInt($param['seo_position_search_query_livesearch']);

			if (Core_Type_Conversion::toInt($param['users_id']))
			{
				$oSeo_Query_Position->user_id = intval($param['users_id']);
			}

			$oSeo_Query_Position->save();

			return $oSeo_Query_Position->id;
		}

		return FALSE;
	}

	/**
	* Графическое отображение статуса наличия счетчиков и страницы в каталогах в отчете 
	*
	* @param bool $value Наличие сайта в каталоге, либо счетчика на странице - true, false - иначе
	* <code>
	* <?php
	* $Seo = new Seo();
	*
	* $value = true;
	*
	* $Seo->DrawStatusReport($value);
	* ?>
	* </code>
	*/
	function DrawStatusReport($value)
	{
		throw new Core_Exception('Method DrawStatusReport() does not allow');
	}

	/**
	* Отображение шапки таблицы в отчете
	*
	* @param array $report Массив данных
	* - $report[]['seo_characteristic_date_time'] str Дата
	* - $report[]['seo_position_search_query_date_time'] str Дата
	* @param array $param Массив дополнительных параметров
	*/
	function ShowTableTitleReport($report, $field_name, $param = array())
	{
		throw new Core_Exception('Method ShowTableTitleReport() does not allow');
	}

	/**
	 * Отображение строк таблицы в Отчете
	 *
	 * @param array $report Массив данных
	 * - $report[]['seo_characteristic_yc'] int тИЦ
	 * - $report[]['seo_characteristic_pr'] int PR
	 * - $report[]['seo_characteristic_links_google'] int Ссылающиеся страницы по данным Google
	 * - $report[]['seo_characteristic_links_yandex'] int Ссылающиеся страницы по данным Yandex
	 * - $report[]['seo_characteristic_links_yahoo'] int Ссылающиеся страницы по данным Yahoo
	 * - $report[]['seo_characteristic_links_msn'] int Ссылающиеся страницы по данным Bing.com
	 * - $report[]['seo_characteristic_indexed_aport'] int Индексированные страницы сервисом Aport
	 * - $report[]['seo_characteristic_indexed_yandex'] int Индексированные страницы сервисом Yandex
	 * - $report[]['seo_characteristic_indexed_yahoo'] int Индексированные страницы сервисом Yahoo
	 * - $report[]['seo_characteristic_indexed_msn'] int Индексированные страницы сервисом Bing.com
	 * - $report[]['seo_characteristic_indexed_rambler'] int Индексированные страницы сервисом Rambler
	 * - $report[]['seo_characteristic_indexed_google'] int Индексированные страницы сервисом Google
	 * - $report[]['seo_characteristic_catalog_yandex'] bool Наличие страницы в каталоге Yandex
	 * - $report[]['seo_characteristic_catalog_rambler'] bool Наличие страницы в каталоге Rambler
	 * - $report[]['seo_characteristic_catalog_mail'] bool Наличие страницы в каталоге Mail
	 * - $report[]['seo_characteristic_catalog_dmoz'] bool Наличие страницы в каталоге Dmoz
	 * - $report[]['seo_characteristic_catalog_aport'] bool Наличие страницы в каталоге Aport
	 * - $report[]['seo_characteristic_counter_rambler'] bool Наличие счетчика Rambler
	 * - $report[]['seo_characteristic_counter_spylog'] bool Наличие счетчика SpyLog
	 * - $report[]['seo_characteristic_counter_hotlog'] bool Наличие счетчика HotLog
	 * - $report[]['seo_characteristic_counter_mail'] bool Наличие счетчика Mail
	 * - $report[]['seo_characteristic_counter_liveinternet'] bool Наличие счетчика LiveInternet
	 * @param array $param Массив дополнительных параметров
	 * - $param['arrow'] bool Отображение стрелочек динамики изменения значений
	 * - $param['status'] bool Графическое отображение статуса наличия счетчиков и страницы в каталогах
	 * - $param['inverse'] bool Инвертирование отображения динамики изменения значений
	 * - $param['count'] int Количество строк в массиве данных
	 * @param str $field_name Название строки
	 * @param str $field_value Название поля БД
	 */
	function ShowTableRow($report, $field_name, $field_value, $param = array())
	{
		throw new Core_Exception('Method ShowTableRow() does not allow');
	}

	/**
	* Игнорирование столбцов таблицы
	*
	* @param array $report Массив данных
	* @param str $value_type Тип поля значений
	* @return array
	*/
	function BuildMassReport($report, $value_type)
	{
		throw new Core_Exception('Method BuildMassReport() does not allow');
	}
}
?>