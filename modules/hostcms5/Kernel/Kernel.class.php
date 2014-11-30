<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс ядра системы управления сайтом HostCMS.
 *
 * Файл: /modules/Kernel/kernel.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class kernel
{
	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $title;

	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $description;

	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $keywords;

	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $page;

	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $data_template;

	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $CSS;

	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $timestamp = 0;

	/**
	 * Enter description here...
	 *
	 * @var int
	 * @access private
	 */
	var $LastModified;

	/**
	 * Enter description here...
	 *
	 * @var int
	 * @access private
	 */
	var $Expires;

	/**
	 * Enter description here...
	 *
	 * @var int
	 * @access private
	 */
	var $template;

	/**
	 * Информация о загруженных модулях системы
	 *
	 * @var array
	 */
	var $modules = array ();

	//var $ShowXmlContent = '';

	/**
	 * Кэш для метода GetTableFields()
	 *
	 * @var array
	 * @access private
	 */
	var $CacheGetTableFields = array();

	/**
	 * Массив ассоциаций расширений и пиктограмм
	 *
	 * @var array
	 */
	var $icon_array = array();

	/**
	 * Доступные для загрузки расширения файлов
	 *
	 * @var array
	 */
	var $available_extantions = array('JPG','JPEG','GIF','PNG');

	/**
	 * Рашсирения графических файлов, доступные для уменьшения
	 *
	 * @var array
	 */
	var $resize_extension = array ('JPG','JPEG','GIF','PNG');

	/**
	 * HTTP-прокси
	 *
	 * @var string
	 */
	var $CURLOPT_PROXY = FALSE;

	/**
	 * Имя пользователя и пароль в формате "[username]:[password]" для использования при соединении с прокси
	 *
	 * @var string
	 */
	var $CURLOPT_PROXYUSERPWD = FALSE;

	/**
	 * Метод HTTP-авторизации для использования при соединении с прокси.
	 * Для прокси-авторизации доступны только CURLAUTH_BASIC и CURLAUTH_NTLM
	 *
	 * @var string
	 */
	var $CURLOPT_PROXYAUTH = FALSE;

	/**
	 * Номер порта для соединения с прокси-сервером; используется совместно с CURLOPT_PROXY
	 *
	 * @var int
	 */
	var $CURLOPT_PROXYPORT = FALSE;

	/**
	 * CURLPROXY_HTTP по умолчанию или CURLPROXY_SOCKS5
	 * @var string
	 */
	var $CURLOPT_PROXYTYPE = FALSE;

	/**
	 * При установке этого параметра в true данные будут передаваться через прокси-сервер
	 *
	 * @var bool
	 */
	var $CURLOPT_HTTPPROXYTUNNEL = FALSE;

	var $aJs = array();

	var $sModulesPath = NULL;

	/**
	 * Данные внешнего SMTP-сервера для отправки писем
	 *  array(
	 *		'smtp_username' => 'address@domain.com',
	 *		'smtp_port' => '25', // для SSL порт 465
	 *		'smtp_host' => 'smtp.server.com', // для SSL используйте ssl://smtp.gmail.com
	 *		'smtp_password' => ''
	 *	);
	 */
	var $useSmtp = NULL;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->sModulesPath = CMS_FOLDER . 'modules/hostcms5/';
		$this->available_extantions = Core::$mainConfig['availableExtension'];
		$this->icon_array = Core::$mainConfig['fileIcons'];
	}

	/**
	 * Добавление внешнего JavaScript файла для подключения в центре администрирования
	 *
	 * @param $path путь к внешнему JavaScript файлу
	 * <code>
	 * $kernel = & singleton('kernel');
	 *
	 * $kernel->AddJs('/admin/js/my.js');
	 * </code>
	 */
	function AddJs($path)
	{
		$this->aJs[] = $path;
	}

	/**
	 * Получения текущего языка центра администрирования
	 *
	 * @return str
	 */
	function GetCurrentLng()
	{
		return isset($_SESSION["current_lng"]) ? $_SESSION["current_lng"] : 'ru';
	}

	/**
	 * Получения временной метки по текущей версии системы управления
	 *
	 * @return int
	 */
	function GetVersionTimestamp()
	{
		return defined('CURRENT_VERSION')
			? $this->crc32(CURRENT_VERSION.CURRENT_VERSION)
			: 1281797056;
	}

	/**
	 * Получения списка внешних JavaScript файлов
	 *
	 * @return array
	 */
	function GetAllJs()
	{
		return $this->aJs;
	}

	/**
	 * Используется ли HTTPS-доступ
	 *
	 * @return boolean
	 */
	function UseHttps()
	{
		return Core::httpsUses();
	}

	/**
	 * Возвращает уникальный идентификатор GUID
	 *
	 * @return string
	 */
	function Guid()
	{
		return Core_Guid::get();
	}

	/**
	 * Установить бит числа
	 *
	 * @param int $int исходное число
	 * @param int $bit_number номер бита, счет ведется с 0
	 * @param int $value значение бита (0, 1). по умолчанию значение 1
	 */
	function SetBit($int, $bit_number, $value = 1)
	{
		return Core_Bit::setBit($int, $bit_number, $value);
	}

	/**
	 * Установить бит числа в 0
	 *
	 * @param int $int исходное число
	 * @param int $bit_number номер бита, счет ведется с 0
	 */
	function ResetBit($int, $bit_number)
	{
		return Core_Bit::setBit($int, $bit_number, 0);
	}

	/**
	 * Получить бит номер $bit_number числа $int
	 *
	 * @param int $int исходное число
	 * @param int $bit_number номер бита, счет ведется с 0
	 */
	function GetBit($int, $bit_number)
	{
		return Core_Bit::getBit($int, $bit_number);
	}

	/**
	 * Дописывает $xml к $this->ShowXmlContent
	 *
	 * @param string $xml
	 */
	function AddXmlContent($xml)
	{
		//$this->ShowXmlContent .= $xml;
	}

	/**
	 *Возвращает $this->ShowXmlContent
	 *
	 * @return string
	 */
	function GetXmlContent()
	{
		//return $this->ShowXmlContent;
	}

	/**
	 * Осуществляет добавление файла модуля в список файлов модулей для загрузки.
	 *
	 * @param string $module_name наименование модуля
	 * @param string $file_path полный путь к файлу
	 * @param string $class_name наименование класса, размещенного в файле модуля
	 * @return boolean
	 */
	function AddModuleFile($module_name, $file_path, $class_name = FALSE)
	{
		// Если нет для данного модуля такого файла или модуль в массиве еще не упоминался
		if (!isset ($this->modules[$module_name]) || (isset ($this->modules[$module_name]) && !in_array($file_path, $this->modules[$module_name])))
		{
			// Добавляем файл для загрузки
			$this->modules[$module_name][] = $file_path;

			// Если класс не указан, то используется в качестве имени класса имя модуля
			if (!$class_name)
			{
				$class_name = $module_name;
			}

			// Для динамической загрузки файла при использовании __includeclass()
			$GLOBALS['FILE_CLASS'][$class_name] = $file_path;
		}
	}

	/**
	 * Служебный метод
	 *
	 * @param string $module_name
	 */
	function LoadModuleFiles($module_name)
	{
		if (isset ($this->modules[$module_name]) && is_array($this->modules[$module_name]))
		{
			// Получаем спсиок файлов модуля
			foreach ($this->modules[$module_name] as $value)
			{
				// Подключаем каждый файл
				$this->AddInclude($value);
			}
		}
	}

	/**
	 * Устаревший!!!
	 * Метод создает глобально доступный объект
	 *
	 * @param string $class_name имя класса
	 * @return mixed object или FALSE, если класс отсутствует
	 * @access private
	 */
	function & GetObject($class_name)
	{
		$object = & singleton($class_name);
		return $object;
	}

	/**
	 * Метод возвращает расширение файла
	 *
	 * @param string $filename имя файла
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $filename = 'file.jpg';
	 *
	 * $result = $kernel->GetExtension($filename);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return string расширение файла
	 */
	function GetExtension($filename)
	{
		return Core_File::getExtension($filename);
	}

	/**
	 * Метод определения MIME-типа файла
	 *
	 * @param string $filename имя файла с расширением
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $filename = 'file.jpg';
	 *
	 * $result = $kernel->GetMimeType($filename);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return string mime-тип
	 */
	function GetMimeType($filename)
	{
		return Core_Mime::getFileMime($filename);
	}

	/**
	 * Метод удаляет добавленные слэши в суперглобальных массивах при magic_quotes_gpc = on и невозможности отключить через директивы php_flag
	 *
	 */
	function strips()
	{
		// Если включены магические кавычки
		if (get_magic_quotes_gpc())
		{
			strips($_GET);
			strips($_POST);
			strips($_COOKIE);
			strips($_REQUEST);

			if (isset ($_SERVER['PHP_AUTH_USER']))
			{
				strips($_SERVER['PHP_AUTH_USER']);
			}

			if (isset ($_SERVER['PHP_AUTH_PW']))
			{
				strips($_SERVER['PHP_AUTH_PW']);
			}
		}
	}

	/**
	 * Метод возвращает номер версии PHP
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->phpversion();
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int номер версии PHP, например 4 или 5
	 */
	function phpversion()
	{
		$version = explode('.', phpversion());
		return intval($version[0]);
		//return PHP_VERSION;
	}

	/**
	 * Метод загрузки языкового файла для модуля
	 *
	 * @param string $module_path_name имя в пути модуля, например Document
	 * @param string $modules_name текстовое имя модуля, например "Документы"
	 * <code>
	 * <?php
	 * <?php
	 *
	 * $kernel = & singleton('kernel');
	 *
	 * $module_path_name = 'Documents';
	 * $modules_name = 'Документы';
	 *
	 * $kernel->LoadModulesLngFile($module_path_name, $modules_name);
	 * ?>
	 * </code>
	 */
	function LoadModulesLngFile($module_path_name, $modules_name)
	{
		if (defined('CURRENT_LNG') && is_file($this->sModulesPath . $module_path_name.'/lng/'.CURRENT_LNG.'/'.CURRENT_LNG.'.php'))
		{
			require_once ($this->sModulesPath.$module_path_name.'/lng/'.CURRENT_LNG.'/'.CURRENT_LNG.'.php');
		}
		else
		{
			if (is_file($this->sModulesPath.$module_path_name.'/lng/'.DEFAULT_LNG.'/'.DEFAULT_LNG.'.php'))
			{
				require_once ($this->sModulesPath.$module_path_name.'/lng/'.DEFAULT_LNG.'/'.DEFAULT_LNG.'.php');
			}
			else
			{
				show_error_message("Ошибка! Отсутствует языковой файл для модуля $modules_name!");
			}
		}
	}

	/**
	 * Метод загрузка модулей системы управления, вызывается при формировании любой страницы, генерируемой HostCMS
	 * @param bool $load_all_active_module необязательный параметр, указывает на обязательную загрузку всех активных модулей (для PHP 5 и выше), по умолчанию true
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $load_all_active_module = true;
	 *
	 * $kernel->LoadModules($load_all_active_module);
	 * ?>
	 * </code>
	 */
	function LoadModules($load_all_active_module = true)
	{
		$php_version = $this->phpversion();

		foreach (Core::$modulesList AS $oModule)
		{
			if ($oModule->active == 1)
			{
				// Для 4 версии подключаем все модули сразу, иначе в __autoload
				if ($php_version == 4 || $load_all_active_module)
				{
					$this->AddModule($oModule->path, FALSE);
				}
			}
		}

		if ($php_version == 4 || $load_all_active_module)
		{
			// Проверяем на наличие критически важных модулей
			if (!$load_all_active_module && !class_exists('modules'))
			{
				show_error_message('Внимание! В списке модулей отсутствует Modules, произведена принудительная загрузка. Добавьте отсутствующий модуль в список модулей.');
				$this->AddModule('Modules');
			}

			// Проверяем на наличие критически важных модулей
			if (!$load_all_active_module && !class_exists('user_access'))
			{
				show_error_message('Внимание! В списке модулей отсутствует UserAccess, произведена принудительная загрузка. Добавьте отсутствующий модуль в список модулей.');
				$this->AddModule('UserAccess');
			}

			// Проверяем на наличие критически важных модулей
			if (!$load_all_active_module && !class_exists('ip'))
			{
				show_error_message('Внимание! В списке модулей отсутствует модуль IP, произведена принудительная загрузка. Добавьте отсутствующий модуль в список модулей.');
				$this->AddModule('ip');
			}

			// Проверяем на наличие критически важных модулей
			if (!$load_all_active_module && !class_exists('Structure'))
			{
				show_error_message('Внимание! В списке модулей отсутствует модуль Structure, произведена принудительная загрузка. Добавьте отсутствующий модуль в список модулей.');
				$this->AddModule('Structure');
			}

			// Проверяем на наличие критически важных модулей
			if (!$load_all_active_module && !class_exists('documents'))
			{
				show_error_message('Внимание! В списке модулей отсутствует модуль Documents, произведена принудительная загрузка. Добавьте отсутствующий модуль в список модулей.');
				$this->AddModule('Documents');
			}

			// Проверяем на наличие критически важных модулей
			if (!$load_all_active_module && !class_exists('xsl'))
			{
				show_error_message('Внимание! В списке модулей отсутствует модуль Xsl, произведена принудительная загрузка. Добавьте отсутствующий модуль в список модулей.');
				$this->AddModule('Xsl');
			}

			// Проверяем на наличие критически важных модулей
			if (!$load_all_active_module && !class_exists('templates'))
			{
				show_error_message('Внимание! В списке модулей отсутствует модуль Templates, произведена принудительная загрузка. Добавьте отсутствующий модуль в список модулей.');
				$this->AddModule('Templates');
			}

			// Проверяем на наличие критически важных модулей
			if (!$load_all_active_module && !class_exists('site'))
			{
				show_error_message('Внимание! В списке модулей отсутствует модуль site, произведена принудительная загрузка. Добавьте отсутствующий модуль в список модулей.');
				$this->AddModule('site');
			}
		}
	}

	/**
	 * Метод подключения модуля
	 *
	 * @param string $modules_path path-имя модуля, например Structure
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $modules_path = 'Structure';
	 *
	 * $kernel->AddModule($modules_path);
	 * ?>
	 * </code>
	 */
	function AddModule($modules_path, $load_module_files = true)
	{
		// Подключаем настройки модуля
		$this->AddInclude($this->sModulesPath . $modules_path . '/config/config.php');

		// Подключаем модуль
		$path = $this->sModulesPath . $modules_path . '/' . $modules_path . '.php';

		if (!$this->AddInclude($path))
		{
			//show_error_message("Ошибка загрузки файла модуля {$modules_path}, путь загрузки {$path}");
		}

		if ($load_module_files || PHP_VERSION < 5)
		{
			// Подключаем файлы модуля
			$this->LoadModuleFiles($modules_path);
		}
	}

	/**
	 * Метод подключает файл по указанному адресу
	 *
	 * @param string $file адрес файла
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $file = 'file.txt';
	 *
	 * $result = $kernel->AddInclude($file);
	 *
	 * if ($result)
	 * {
	 * 	echo "Подключение файла выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка подключения файла";
	 * }
	 * ?>
	 * </code>
	 * @return bool
	 */
	function AddInclude($file)
	{
		if (file_exists($file))
		{
			require_once($file);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Метод инициализации констант сайта в соответствии с константой CURRENT_SITE
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->InitConstants();
	 *
	 * if ($result)
	 * {
	 * 	echo "Инициализации констант выполнена успешно";
	 * }
	 * else
	 * {
	 *	echo "Ошибка инициализации констант";
	 * }
	 * ?>
	 * </code>
	 * @return boolean true в случае успеха, FALSE - данные о сайте отсутствуют
	 */
	function InitConstants()
	{
		if (!defined('CURRENT_SITE'))
		{
			return FALSE;
		}

		$oSite = Core_Entity::factory('Site')->find(intval(CURRENT_SITE));
		if (is_NULL($oSite->id))
		{
			$oSite = $oSite->getFirstSite();
		}

		Core::initConstants($oSite);
		return TRUE;
	}

	/**
	 * Метод вывода заголовка страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->show_title();
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 */
	function show_title()
	{
		Core_Page::instance()->showTitle();
	}

	/**
	 * Получение заголовка страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->get_title();
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return string значение заголовка страницы
	 */
	function get_title()
	{
		return Core_Page::instance()->title;
	}

	/**
	 * Метод изменения заголовока страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $title = 'test2';
	 *
	 * $kernel->set_title($title);
	 *
	 * ?>
	 * </code>
	 * @param string $title текст заголовка страницы
	 */
	function set_title($title)
	{
		Core_Page::instance()->title($title);
	}

	/**
	 * Метод вывода описания страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 * $kernel->show_description();
	 * ?>
	 * </code>
	 */
	function show_description()
	{
		Core_Page::instance()->showDescription();
	}

	/**
	 * Получение описания страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 * $title = 'test2';
	 * $result = $kernel->get_description($title);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return string описание страницы
	 */
	function get_description()
	{
		return Core_Page::instance()->description;
	}

	/**
	 * Метод изменения описания страницы
	 *
	 * @param string $description описание страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $description = 'Новое описание';
	 *
	 * $kernel->set_description($description);
	 * ?>
	 * </code>
	 */
	function set_description($description)
	{
		Core_Page::instance()->description($description);
	}

	/**
	 * Метод вывода ключевых слов страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * echo $kernel->show_keywords();
	 * ?>
	 * </code>
	 */
	function show_keywords()
	{
		Core_Page::instance()->showKeywords();
	}

	/**
	 * Получение ключевых слов страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->get_keywords();
	 *
	 * // Распечатаем результат
	 * print_r ($result);
	 * ?>
	 * </code>
	 * @return string ключевые слова, установленные для страницы
	 */
	function get_keywords()
	{
		return Core_Page::instance()->keywords;
	}

	/**
	 * Метод изменения ключевых слов страницы
	 *
	 * @param string $keywords ключевые слова
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $keywords = 'Новые ключевые слова';
	 *
	 * $kernel->set_keywords($keywords);
	 * ?>
	 * </code>
	 */
	function set_keywords($keywords)
	{
		Core_Page::instance()->keywords($keywords);
	}

	/**
	 * Метод установления времени последнего изменения страницы
	 *
	 * @param int $time временная метка
	 */
	function SetLastModified($time)
	{
		$this->LastModified = $time;
	}

	/**
	 * Получение времени последнего изменения страницы
	 * @return int временная метка для страницы
	 */
	function GetLastModified()
	{
		return $this->LastModified;
	}

	/**
	 * Установка времени истечения актуальности страницы
	 * @param int $time временная метка
	 */
	function SetExpires($time)
	{
		$this->Expires = $time;
	}

	/**
	 * Получение времени истечения актуальности страницы
	 * @return int временная метка для страницы
	 */
	function GetExpires()
	{
		return $this->Expires;
	}

	/**
	 * Метод установления текущего макета
	 *
	 * @param int $template_id идентификатор макета
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $template_id = 1;
	 *
	 * $kernel->SetTemplate($template_id);
	 * ?>
	 * </code>
	 */
	function SetTemplate($template_id)
	{

		//throw new Core_Exception('Method SetTemplate() does not allow');
	}

	/**
	 * Получение текущего макета
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $resource = $kernel->GetTemplate();
	 *
	 * // Распечатаем результат
	 * echo ($resource);
	 * ?>
	 * </code>
	 * @return int идентификатор макета
	 */
	function GetTemplate()
	{
		return Core_Page::instance()->template->id;
	}

	/**
	 * Метод изменения адреса файла CSS-стиля для страницы
	 *
	 * @param string $CSS код стилей
	 */
	function set_CSS($CSS)
	{
		Core_Page::instance()->css('/' . $CSS);
	}

	/**
	 * Изменение временной метки файла CSS-стиля для страницы
	 *
	 * @param string $CSS код стилей
	 */
	function setTimestamp($timestamp){}

	/**
	 * Получение временной метки файла CSS-стиля для страницы
	 *
	 * @return int
	 */
	function getTimestamp()
	{
		return Core_Page::instance()->template->timestamp;
	}

	/**
	 * Метод вывода CSS стиля для страницы
	 *
	 * @param boolean $is_external true - ссылка на CSS, FALSE - вывод содержания CSS стиля
	 */
	function show_CSS($is_external = TRUE)
	{
		echo $this->get_CSS($is_external);
	}

	/**
	 * Получение CSS стиля для страницы
	 *
	 * @param boolean $is_external необязательный параметр $is_external - true - ссылка на CSS, FALSE - возвращение содержания CSS стиля
	 * @return string CSS-стиль для страницы
	 */
	function get_CSS($is_external = TRUE)
	{
		return Core_Page::instance()->getCss($is_external);
	}

	/**
	 * Метод изменения шаблона страницы
	 *
	 * @param string $data_template_id идентификатор шаблона страницы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $data_template_id = 3;
	 *
	 * $kernel->set_current_page_data_template($data_template_id);
	 * ?>
	 * </code>
	 */
	function set_current_page_data_template($data_template_id)
	{
		//throw new Core_Exception('Method set_current_page_data_template() does not allow');
	}

	/**
	 * Метод изменения адреса страницы
	 *
	 * @param string $page абсолютный адрес файла статичного документа
	 */
	function set_current_page($page)
	{
		//throw new Core_Exception('Method set_current_page() does not allow');

		$aPage = explode('/', $page);
		$last = Core_Array::end($aPage);

		$oCore_Page = Core_Page::instance();

		preg_match("#documents(\d*).html#i", $last, $matches);

		if (isset($matches[1]))
		{
			$oDocument_Version = Core_Entity::factory('Document_Version', $matches[1]);

			if (!is_null($oDocument_Version) && $oDocument_Version->Template->id)
			{
				$oCore_Page->template($oDocument_Version->Template);
			}
			$oCore_Page->addChild($oDocument_Version);
			return TRUE;
		}

		preg_match("#Structure(\d*).html#i", $last, $matches);

		if (isset($matches[1]))
		{
			$oStructure = Core_Entity::factory('Structure')->find($matches[1]);

			$oCore_Page->template($oStructure->Template);

			$oCore_Page->addChild($oStructure->getRelatedObjectByType());
			$oStructure->setCorePageSeo($oCore_Page);
			return TRUE;
		}

		preg_match("#lib_(\d*).html#i", $last, $matches);

		if (isset($matches[1]))
		{
			$oLib = Core_Entity::factory('Lib', $matches[1]);

			$oCore_Page->addChild($oLib);
			return TRUE;
		}
	}

	/**
	 * Получение пути текущей страницы
	 *
	 * @return string абсолютный путь к текущей статичной странице
	 */
	function get_current_page_path()
	{
		throw new Core_Exception('Method get_current_page_path() does not allow');
	}

	/**
	 * Метод вывода установленной страницы
	 *
	 */
	function show_current_page()
	{
		Core_Page::instance()->execute();
	}

	/**
	 * Метод вывода текущего макета.
	 *
	 */
	function show_current_template()
	{
		Core_Page::instance()->execute();
	}

	/**
	 * Получение значения времени
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->getmicrotime();
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int время
	 */
	function getmicrotime()
	{
		return Core::getmicrotime();
	}

	/**
	 * Получение идентификатора текущего узла структуры
	 *
	 * @param array $param имена разделов от родителя к ребенку,
	 * например Array ([0] => services[1] => guard_business_center)
	 * @param int $site_id идентификатор сайта
	 * @return mixed идентификатор узла структуры или FALSE
	 */
	function get_current_page($param, $site_id = CURRENT_SITE)
	{
		$site_id = intval($site_id);

		// если param не массив - возвращаем FALSE
		if (!is_array($param))
		{
			return FALSE;
		}

		$parent_id = 0;

		// Число элементов массива
		$param_count = count($param);

		for ($i = 0; $i < $param_count; $i++)
		{
			$temp = $this->get_page_id($param[$i], $parent_id, $site_id);

			if ($temp != FALSE) // !==
			{
				$parent_id = $temp;
			}
			else
			{
				// Определим тип узла структуры
				$oStructure = Core_Entity::factory('Structure')->find($parent_id);

				// Типы структуры:
				// 0 - статичный документ
				if (!is_NULL($oStructure->id) && $oStructure->type == 0)
				{
					// Укажем, что узел структуры не найден - сборосим $parent_id
					$parent_id = FALSE;
				}

				break; // Прерываем, если у страницы нет таких дочерних (значит дальше идут параметры)
			}
		}

		$page_id = $parent_id;

		/* Обработчик и константа необходима на случай размещения инфосистемы на главной страницы*/
		if (defined('INDEX_PAGE_IS_DEFAULT') && INDEX_PAGE_IS_DEFAULT && $page_id == 0)
		{
			// Получаем ID главной страницы
			$page_id = $this->get_page_id('/', 0, $site_id);
		}

		return $page_id;
	}

	/**
	 * Метод определения идентификатора страницы по имени узла стурктуры и идентификатору родителя
	 *
	 * @param string $name имя узла стурктуры
	 * @param int $parent_id идентификатор родителя
	 * @param int $site_id идентификатор сайта
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $name = 'guestbook';
	 * $parent_id = 78;
	 * $site_id = CURRENT_SITE;
	 *
	 * $resource = $kernel->get_page_id($name, $parent_id, $site_id);
	 *
	 * // Распечатаем результат
	 * echo $resource;
	 * ?>
	 * </code>
	 * @return int идентификатор узла структуры или FALSE
	 */
	function get_page_id($name, $parent_id, $site_id)
	{
		$name = strval($name);
		$parent_id = intval($parent_id);
		$site_id = intval($site_id);

		if (class_exists('Cache'))
		{
			$cache_element = "{$name}_{$parent_id}_{$site_id}";
			$cache = & singleton('Cache');
			$cache_name = 'STRUCTURE_ID_BY_NAME';
			$in_cache = $cache->GetCacheContent($cache_element, $cache_name);

			if ($in_cache)
			{
				return $in_cache['value'];
			}
		}

		$oStructure = Core_Entity::factory('Site', $site_id)
			->Structures
			->getByPathAndParentId($name, $parent_id);

		if (!is_NULL($oStructure) && $oStructure->active == 1)
		{
			$result = $oStructure->id;
		}
		else
		{
			$result = FALSE;
		}

		if (class_exists('Cache'))
		{
			$cache->Insert($cache_element, $result, $cache_name);
		}

		return $result;
	}

	/**
	 * Метод определения версии GD библиотеки
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->GetGDVersion();
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($result);
	 * ?>
	 * </code>
	 * @return string версия GD библиотеки
	 */
	function GetGDVersion()
	{
		return Core_Image::instance('gd')->getVersion();
	}

	/**
	 * Метод определения версии MySQL
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->GetDBVersion();
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($result);
	 * ?>
	 * </code>
	 * @return string версия MySQL
	 */
	function GetDBVersion()
	{
		return Core_DataBase::instance()->getVersion();
	}

	/**
	 * Получение значения текущего года
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->GetCurrentYear();
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int текущий год
	 */
	function GetCurrentYear()
	{
		return date("Y");
	}

	/**
	 * Метод добавления модуля в массив-список модулей
	 *
	 * @param string $path путь к модулю
	 * @param string $version версия модуля
	 * @param string $date дата
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $module_path_name = 'admin_forms';
	 * $module_name = 'Формы центра управления системой';
	 * $version = '5.2';
	 * $date = '27.03.2009';
	 *
	 * $kernel->add_modules_version($module_path_name,'5.6', '05.06.2009');
	 * ?>
	 * </code>
	 */
	function add_modules_version($path, $version, $date = '')
	{
		if (!isset ($GLOBALS['gModulesVersionDate']))
		{
			$GLOBALS['gModulesVersionDate'] = array ();
		}

		// Добавление элемента
		$GLOBALS['gModulesVersionDate'][$path]['version'] = $version;
		$GLOBALS['gModulesVersionDate'][$path]['date'] = $date;
	}

	/**
	 * Метод отображения сообщения о том, что изменения, сделанные в админке не могут вступить в силу
	 * @access private
	 */
	function ReadMode()
	{
		show_error_message('Внимание! Демонстрационный режим. Действие запрещено!');
	}

	/**
	 * Метод конкатенации элементов многомерного массива. Вызывает себя рекурсивно
	 *
	 * @param array $array массив
	 * @param array $separator разделитель
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $array = array('field1', 'field2');
	 * $separator = '';
	 *
	 * $result = $kernel->implode_array($array, $separator);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return string строка
	 */
	function implode_array($array, $separator = '')
	{
		$text = '';

		if (is_array($array))
		{
			foreach ($array as $key => $value)
			{
				if (is_array($value))
				{
					// если элемент - массив, вызываем рекурсивно
					$text .= $this->implode_array($value, $separator);
				}
				else
				{
					$text .= $key.$separator.$value.$separator;
				}
			}
		}
		else
		{
			// если не массив - пишем в текст значение и возврщаем его
			$text = $separator.$array;
		}

		return $text;
	}

	/**
	 * Метод преобразует сущности в их числовое представление, например &amp;nbsp; в &#160;
	 *
	 * @param string $html
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $html = '&amp;nbsp;';
	 *
	 * $result = $kernel->numeric_character_references($html);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($result);
	 * ?>
	 * </code>
	 * @return числовое значение
	 */
	function numeric_character_references($html)
	{
		return Core_Str::str2ncr($html);
	}

	/**
	 * Метод перемешивания элементов массива. Если передан hash - сортировка будет осуществлена в соответствии с этим значением.
	 *
	 * @param array $array массив
	 * @param int $hash некое числовое значение
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $array = array('field1', 'field2', 'field3');
	 * $hash = 10;
	 *
	 * $row = $kernel->random_shuffle($array, $hash);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array перемешанный массив
	 */
	function random_shuffle($array, $hash = FALSE)
	{
		return Core_Array::randomShuffle($array, $hash === FALSE ? NULL : $hash);
	}

	/**
	 * Создание цвета для некоторого идентификатора.
	 * Используется при создании уникального цвета фона для каждого пользователея.
	 * @param $id идентификтаор
	 * @return array массив цветов из 3-х элементов R, G, B
	 */
	function GetColor($id)
	{
		// Выбираем тип сортировки массива
		$rand_type = $id % 3;

		$max_color = 190;

		$array = array(0, $max_color, abs($this->crc32($id)) % $max_color);

		$array_shuffle = $this->random_shuffle($array, $rand_type);

		return $array_shuffle;
	}

	/**
	 * Получение существительного в форме, соответствующей числу
	 *
	 * @param int $num число, с которым связано существительное
	 * @param string $word_stem основа слова
	 * @param array $ends_of_word массив окончаний слова
	 * @param string $prefix префикс
	 * @param string $postfix суфикс
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $word_stem = 'новост';
	 * $ends_of_word = array('ей', 'ь', 'и', 'и', 'и', 'ей', 'ей', 'ей', 'ей', 'ей');
	 *
	 * for ($num = 0; $num < 100; $num++)
	 * {
	 *	$result = $kernel->declension($num, $word_stem, $ends_of_word);
	 *
	 * // Распечатаем результат
	 * echo "{$num} {$result} <br />";
	 * }
	 * ?>
	 * </code>
	 * @return string сформированная строка
	 */
	function declension($num, $word_stem, $ends_of_word, $prefix = '', $postfix = '')
	{
		return $prefix . Core_Str::declension($num, $word_stem, $ends_of_word) . $postfix;
	}

	/**
	 * Преобразует текст из UTF-8 в Windows1251. В своей работе использует
	 * iconv() при его наличии или kernel::utf8_win1251()
	 *
	 * @param string $utf8
	 * @return string
	 */
	function Utf8ToWindows1251($utf8)
	{
		if (function_exists('iconv'))
		{
			return @iconv('UTF-8', 'Windows-1251//IGNORE//TRANSLIT', $utf8);
		}
		else
		{
			return $this->utf8_win1251($utf8);
		}
	}

	/**
	 * Преобразует текст из Windows1251 в UTF-8. В своей работе использует
	 * iconv() при его наличии или kernel::win1251_utf8()
	 *
	 * @param string $windows1251
	 * @return string
	 */
	function Windows1251ToUtf8($windows1251)
	{
		if (function_exists('iconv'))
		{
			return @iconv('Windows-1251', 'UTF-8//IGNORE//TRANSLIT', $windows1251);
		}
		else
		{
			return $this->win1251_utf8($windows1251);
		}
	}

	/**
	 * Перевод из UTF-8 в Windows-1251, используется при отсутствии iconv()
	 *
	 * @param string $text
	 * @return string
	 */
	function utf8_win1251($text)
	{
		$_utf8win1251 = array (
		"\xD0\x90" => "\xC0",
		"\xD0\x91" => "\xC1",
		"\xD0\x92" => "\xC2",
		"\xD0\x93" => "\xC3",
		"\xD0\x94" => "\xC4",
		"\xD0\x95" => "\xC5",
		"\xD0\x81" => "\xA8",
		"\xD0\x96" => "\xC6",
		"\xD0\x97" => "\xC7",
		"\xD0\x98" => "\xC8",
		"\xD0\x99" => "\xC9",
		"\xD0\x9A" => "\xCA",
		"\xD0\x9B" => "\xCB",
		"\xD0\x9C" => "\xCC",
		"\xD0\x9D" => "\xCD",
		"\xD0\x9E" => "\xCE",
		"\xD0\x9F" => "\xCF",
		"\xD0\xA0" => "\xD0",
		"\xD0\xA1" => "\xD1",
		"\xD0\xA2" => "\xD2",
		"\xD0\xA3" => "\xD3",
		"\xD0\xA4" => "\xD4",
		"\xD0\xA5" => "\xD5",
		"\xD0\xA6" => "\xD6",
		"\xD0\xA7" => "\xD7",
		"\xD0\xA8" => "\xD8",
		"\xD0\xA9" => "\xD9",
		"\xD0\xAA" => "\xDA",
		"\xD0\xAB" => "\xDB",
		"\xD0\xAC" => "\xDC",
		"\xD0\xAD" => "\xDD",
		"\xD0\xAE" => "\xDE",
		"\xD0\xAF" => "\xDF",
		"\xD0\x87" => "\xAF",
		"\xD0\x86" => "\xB2",
		"\xD0\x84" => "\xAA",
		"\xD0\x8E" => "\xA1",
		"\xD0\xB0" => "\xE0",
		"\xD0\xB1" => "\xE1",
		"\xD0\xB2" => "\xE2",
		"\xD0\xB3" => "\xE3",
		"\xD0\xB4" => "\xE4",
		"\xD0\xB5" => "\xE5",
		"\xD1\x91" => "\xB8",
		"\xD0\xB6" => "\xE6",
		"\xD0\xB7" => "\xE7",
		"\xD0\xB8" => "\xE8",
		"\xD0\xB9" => "\xE9",
		"\xD0\xBA" => "\xEA",
		"\xD0\xBB" => "\xEB",
		"\xD0\xBC" => "\xEC",
		"\xD0\xBD" => "\xED",
		"\xD0\xBE" => "\xEE",
		"\xD0\xBF" => "\xEF",
		"\xD1\x80" => "\xF0",
		"\xD1\x81" => "\xF1",
		"\xD1\x82" => "\xF2",
		"\xD1\x83" => "\xF3",
		"\xD1\x84" => "\xF4",
		"\xD1\x85" => "\xF5",
		"\xD1\x86" => "\xF6",
		"\xD1\x87" => "\xF7",
		"\xD1\x88" => "\xF8",
		"\xD1\x89" => "\xF9",
		"\xD1\x8A" => "\xFA",
		"\xD1\x8B" => "\xFB",
		"\xD1\x8C" => "\xFC",
		"\xD1\x8D" => "\xFD",
		"\xD1\x8E" => "\xFE",
		"\xD1\x8F" => "\xFF",
		"\xD1\x96" => "\xB3",
		"\xD1\x97" => "\xBF",
		"\xD1\x94" => "\xBA",
		"\xD1\x9E" => "\xA2"
		);
		if (is_array($text))
		{
			foreach ($text as $k => $v)
			{
				if (is_array($v))
				{
					$text[$k] = utf8_win1251($v);
				}
				else
				{
					$text[$k] = strtr($v, $_utf8win1251);
				}
			}
			return $text;
		}
		else
		{
			return strtr($text, $_utf8win1251);
		}
	}

	/**
	 * Перевод из Windows-1251 в UTF-8, используется при отсутствии iconv()
	 *
	 * @param string $text
	 * @return string
	 */
	function win1251_utf8($text)
	{
		$_win1251utf8 = array (
		"\xC0" => "\xD0\x90",
		"\xC1" => "\xD0\x91",
		"\xC2" => "\xD0\x92",
		"\xC3" => "\xD0\x93",
		"\xC4" => "\xD0\x94",
		"\xC5" => "\xD0\x95",
		"\xA8" => "\xD0\x81",
		"\xC6" => "\xD0\x96",
		"\xC7" => "\xD0\x97",
		"\xC8" => "\xD0\x98",
		"\xC9" => "\xD0\x99",
		"\xCA" => "\xD0\x9A",
		"\xCB" => "\xD0\x9B",
		"\xCC" => "\xD0\x9C",
		"\xCD" => "\xD0\x9D",
		"\xCE" => "\xD0\x9E",
		"\xCF" => "\xD0\x9F",
		"\xD0" => "\xD0\xA0",
		"\xD1" => "\xD0\xA1",
		"\xD2" => "\xD0\xA2",
		"\xD3" => "\xD0\xA3",
		"\xD4" => "\xD0\xA4",
		"\xD5" => "\xD0\xA5",
		"\xD6" => "\xD0\xA6",
		"\xD7" => "\xD0\xA7",
		"\xD8" => "\xD0\xA8",
		"\xD9" => "\xD0\xA9",
		"\xDA" => "\xD0\xAA",
		"\xDB" => "\xD0\xAB",
		"\xDC" => "\xD0\xAC",
		"\xDD" => "\xD0\xAD",
		"\xDE" => "\xD0\xAE",
		"\xDF" => "\xD0\xAF",
		"\xAF" => "\xD0\x87",
		"\xB2" => "\xD0\x86",
		"\xAA" => "\xD0\x84",
		"\xA1" => "\xD0\x8E",
		"\xE0" => "\xD0\xB0",
		"\xE1" => "\xD0\xB1",
		"\xE2" => "\xD0\xB2",
		"\xE3" => "\xD0\xB3",
		"\xE4" => "\xD0\xB4",
		"\xE5" => "\xD0\xB5",
		"\xB8" => "\xD1\x91",
		"\xE6" => "\xD0\xB6",
		"\xE7" => "\xD0\xB7",
		"\xE8" => "\xD0\xB8",
		"\xE9" => "\xD0\xB9",
		"\xEA" => "\xD0\xBA",
		"\xEB" => "\xD0\xBB",
		"\xEC" => "\xD0\xBC",
		"\xED" => "\xD0\xBD",
		"\xEE" => "\xD0\xBE",
		"\xEF" => "\xD0\xBF",
		"\xF0" => "\xD1\x80",
		"\xF1" => "\xD1\x81",
		"\xF2" => "\xD1\x82",
		"\xF3" => "\xD1\x83",
		"\xF4" => "\xD1\x84",
		"\xF5" => "\xD1\x85",
		"\xF6" => "\xD1\x86",
		"\xF7" => "\xD1\x87",
		"\xF8" => "\xD1\x88",
		"\xF9" => "\xD1\x89",
		"\xFA" => "\xD1\x8A",
		"\xFB" => "\xD1\x8B",
		"\xFC" => "\xD1\x8C",
		"\xFD" => "\xD1\x8D",
		"\xFE" => "\xD1\x8E",
		"\xFF" => "\xD1\x8F",
		"\xB3" => "\xD1\x96",
		"\xBF" => "\xD1\x97",
		"\xBA" => "\xD1\x94",
		"\xA2" => "\xD1\x9E"
		);

		if (is_array($text))
		{
			foreach ($text as $k => $v)
			{
				if (is_array($v))
				{
					$text[$k] = utf8_win1251($v);
				}
				else
				{
					$text[$k] = strtr($v, $_win1251utf8);
				}
			}
			return $text;
		}
		else
		{
			return strtr($text, $_win1251utf8);
		}
	}

	/**
	 * Метод определения идентификатора текущего авторизированного пользователя раздела администрирования
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $result = $kernel->GetCurrentUser();
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int идентификатор пользователя или 0, если нет авторизированного пользователя
	 */
	function GetCurrentUser()
	{
		return Core_Type_Conversion::toInt($_SESSION['current_users_id']);
	}

	/**
	 * Метод отправки электронного письма с прикрепленным файлом
	 *
	 * @param string $to адрес получателя письма
	 * @param string $from адрес отправителя письма
	 * @param string $subject тема письма
	 * @param string $text текст письма
	 * @param array $file_path_name ассоциативный массив, содержащий пути к прикрепляемым файлам и их именам, например
	 * - $file_path_name[0]['filepath'] = "C:\file1.txt";
	 * - $file_path_name[0]['filename'] = "file1.txt";
	 * - $file_path_name[0]['Content-ID'] = "123456";
	 * - $file_path_name[0]['Content-Disposition'] = "attachment"; // attachment или inline
	 * - $file_path_name[0]['Content-Type'] = "application/octet-stream";
	 * @param string $ContentType по умолчанию text/plain
	 * @param array $param массив дополнительных параметров
	 * - $param['sender_name'] - текстовое имя отправителя
	 * - $param['bound'] - граница прикрепляемого файла. Если не передан, создается автоматически
	 * - $param['header'] - массив дополнительных заголовков письма, например, array('XHostMakeReason' => 'Order');
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $to = 'xyz@localhost.ru';
	 * $from = 'admin@localhost.ru';
	 * $subject = 'Тема письма';
	 * $text = 'Текст письма';
	 * $file_path_name = array();
	 * $file_path_name[0]['filepath'] = CMS_FOLDER . 'file.jpg';
	 * $file_path_name[0]['filename'] = "file.jpg";
	 * $file_path_name[0]['Content-ID'] = "123456";
	 * $file_path_name[0]['Content-Disposition'] = "attachment"; // attachment или inline
	 * $file_path_name[0]['Content-Type'] = "application/octet-stream";
	 *
	 * $param = array();
	 * $param['sender_name'] = "Имя отправителя";
	 *
	 * $result = $kernel->SendMailWithFile($to, $from, $subject, $text, $file_path_name, 'text/plain', $param);
	 *
	 * if ($result)
	 * {
	 * 	echo "Отправка письма выполнена успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка отправки письма";
	 * }
	 * ?>
	 * </code>
	 * @return bool true - письмо отправлено успешно, FALSE - неуспешно
	 */
	function SendMailWithFile($to, $from, $subject, $text, $file_path_name = array(),
	$ContentType = 'text/plain', $param = array (), $config = NULL)
	{
		if (is_NULL($config))
		{
			$config = $this->useSmtp;
		}

		$oCore_Mail_Driver = is_NULL($config)
			? Core_Mail::instance()
			: Core_Mail::instance('smtp');

		$oCore_Mail_Driver
			->to($to)
			->from($from)
			->subject($subject)
			->message($text)
			->contentType($ContentType);

		if (isset($param['bound']))
		{
			$oCore_Mail_Driver->bound($param['bound']);
		}

		if (isset($param['sender_name']))
		{
			$oCore_Mail_Driver->senderName($param['sender_name']);
		}

		if (isset($param['multipart_related']) && $param['multipart_related'])
		{
			$oCore_Mail_Driver->multipartRelated($param['multipart_related']);
		}

		// Дополнительные заголовки
		if (isset($param['header']) && is_array($param['header']) && count($param['header']) > 0)
		{
			foreach ($param['header'] as $key => $value)
			{
				$key = strval($key);
				$value = strval($value);
				$oCore_Mail_Driver->header($key, $value);
			}
		}

		if (is_array($file_path_name) && count($file_path_name) > 0)
		{
			foreach ($file_path_name as $key => $value)
			{
				$oCore_Mail_Driver->attach($value);
			}
		}

		return $oCore_Mail_Driver->send();
	}

	/**
	 * Укорачивает описание до определённого количества символов, оставляя целое число предложений
	 *
	 * @param string $text - текст описания
	 * @param int $max_lenght - длина описания
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $text = 'Текст описания, который необходимо укоротить';
	 * $max_lenght = 20;
	 *
	 * $result = $kernel->ReductionDescription($text, $max_lenght);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return string часть описания
	 */
	function ReductionDescription($text, $max_lenght = 255)
	{
		return Core_Str::cutSentences($text, $max_lenght);
	}

	/**
	 * Загрузка файлов в центре администрирования
	 * @param array $param массив параметров
	 * - $param['path_source_big_image'] путь к файлу-источнику большого изображения
	 * - $param['path_source_small_image'] путь к файлу-источнику малого изображения
	 * - $param['original_file_name_big_image'] оригинальное имя файла большого изображения
	 * - $param['path_target_big_image'] путь к создаваемому файлу большого изображения
	 * - $param['path_target_small_image'] путь к создаваемому файлу малого изображения
	 * - $param['original_file_name_small_image'] оригинальное имя файла малого изображения
	 * - $param['use_big_image'] использовать большое изображение для создания малого (true - использовать (по умолчанию), FALSE - не использовать)
	 * - $param['max_width_big_image'] значение максимальной ширины большого изображения
	 * - $param['max_height_big_image'] значение максимальной высоты большого изображения
	 * - $param['max_width_small_image'] значение максимальной ширины малого изображения
	 * - $param['max_height_small_image'] значение максимальной высоты малого изображения
	 * - $param['watermark_file_path'] путь к файлу с "водяным знаком", если водяной знак не должен накладываться, не передавайте этот параметр
	 * - $param['watermark_position_x'] позиция "водяного знака" по оси X
	 * - $param['watermark_position_y'] позиция "водяного знака" по оси Y
	 * - $param['used_watermark_big_image'] наложить "водяной знак" на большое изображение (true - наложить (по умолчанию), FALSE - не наложить)
	 * - $param['used_watermark_small_image'] наложить "водяной знак" на малое изображение (true - наложить (по умолчанию), FALSE - не наложить)
	 * - $param['isset_big_image'] существует ли большое изображение (true - существует, FALSE - не существует (по умолчанию))
	 * - $param['preserve_aspect_ratio_for_big_image'] сохранять пропорции изображения для большого изображения (true - по умолчанию)
	 * - $param['preserve_aspect_ratio_for_small_image'] сохранять пропорции изображения для большого изображения (true - по умолчанию)
	 * @return array $result
	 * - $result['big_image'] = true в случае успешного создания большого изображения, FALSE - в противном случае
	 * - $result['small_image'] = true в случае успешного создания малого изображения, FALSE - в противном случае
	 */
	function AdminLoadFiles($param)
	{
		$aSearchReplace = array(
			'use_big_image' => 'create_small_image_from_large',
			'max_width_big_image' => 'large_image_max_width',
			'max_height_big_image' => 'large_image_max_height',
			'max_width_small_image' => 'small_image_max_width',
			'max_height_small_image' => 'small_image_max_height',
			'used_watermark_big_image' => 'large_image_watermark',
			'used_watermark_small_image' => 'small_image_watermark',
			'preserve_aspect_ratio_for_big_image' => 'large_image_preserve_aspect_ratio',
			'preserve_aspect_ratio_for_small_image' => 'small_image_preserve_aspect_ratio',

			'path_source_big_image' => 'large_image_source',
			'path_source_small_image' => 'small_image_source',
			'original_file_name_big_image' => 'large_image_name',
			'original_file_name_small_image' => 'small_image_name',
			'path_target_big_image' => 'large_image_target',
			'path_target_small_image' => 'small_image_target',
			'isset_big_image' => 'large_image_isset',
		);

		foreach ($param as $key => $value)
		{
			if (isset($aSearchReplace[$key]))
			{
				$param[$aSearchReplace[$key]] = $value;
			}
		}

		$aResult = Core_File::adminUpload($param);
		return array(
			'big_image' => $aResult['large_image'],
			'small_image' => $aResult['small_image']
		);
	}

	/**
	 * Получение структуры таблицы БД
	 *
	 * @param string $table_name имя таблицы
	 * @param string $field_name имя поля таблицы, если не указано или равно пустой строке - выбираются все поля таблицы
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $table_name = 'admin_forms_field_table';
	 *
	 * $result = $kernel->GetTableFields($table_name);
	 *
	 * // Распечатаем результат
	 * print_r ($result);
	 * ?>
	 * </code>
	 * @return mixed массив с информацией о полях таблицы в случае успешного выполнения, FALSE - в противном случае
	 */
	function GetTableFields($table_name, $field_name = '')
	{
		if (isset($this->CacheGetTableFields[$table_name.' '.$field_name]))
		{
			return $this->CacheGetTableFields[$table_name.' '.$field_name];
		}

		$table_name = quote_smart(strval($table_name));
		$field_name = trim($field_name);

		$query = "SHOW COLUMNS FROM $table_name";

		$DataBase = & singleton('DataBase');
		if (!$result_table_fields = $DataBase->query($query))
		{
			return FALSE;
		}

		$mas_fields = array ();

		while ($row_table_fields = mysql_fetch_assoc($result_table_fields))
		{
			if (strlen($field_name) == 0 || $row_table_fields['Field'] == $field_name)
			{
				$mas_fields[$row_table_fields['Field']] = $row_table_fields;
			}
		}

		$this->CacheGetTableFields[$table_name.' '.$field_name] = $mas_fields;

		return $mas_fields;
	}

	/**
	 *
	 * Получение размера в символах поля таблицы.
	 * Например, для поля varchar(200) будет возвращено 200, для поля text будет возвращено 65535.
	 *
	 * @param string $table_name имя таблицы
	 * @param string $field_name имя поля таблицы
	 * @return mixed строка с длиной поля или FALSE
	 */
	function GetTableFieldSize($table_name, $field_name)
	{
		$table_name = strval($table_name);
		$field_name = strval($field_name);

		$mass_field = $this->GetTableFields($table_name, $field_name);

		if ($mass_field && isset($mass_field[$field_name]) && isset($mass_field[$field_name]['Type']))
		{
			preg_match("/([a-zA-Z]+)(\((\d+)\))*/siu", $mass_field[$field_name]['Type'], $type);

			// Размер явно указан
			if (isset($type[3]))
			{
				$size = $type[3];
			}
			else
			{
				switch (strtoupper($type[1]))
				{
					case 'TINYINT':
						$size = strlen('255');
						break;
					case 'SMALLINT':
						$size = strlen('65535');
						break;
					case 'MEDIUMINT':
						$size = strlen('16777215');
						break;
					case 'INT':
						$size = strlen('4294967295');
						break;
					case 'BIGINT':
						$size = strlen('18446744073709551615');
						break;
					case 'FLOAT':
						$size = '24';
						break;
					case 'DOUBLE':
					case 'REAL':
						$size = '53';
						break;
					case 'TINYBLOB':
					case 'TINYTEXT':
						$size = '255';
						break;
					case 'BLOB':
					case 'TEXT':
						$size = '65535';
						break;
					case 'MEDIUMBLOB':
					case 'MEDIUMTEXT':
						$size = '16777215';
						break;
					case 'LONGBLOB':
					case 'LONGTEXT':
						$size = '4294967295';
						break;
					default:
						$size = FALSE;
						break;
				}
			}
		}
		else
		{
			$size = FALSE;
		}

		return $size;
	}

	/**
	 * Преобразование XML в массив
	 *
	 * @param string $xml исходный XML
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $xml = '<?xml version="1.0" encoding="utf-8"?>
	 <cart>
	 <shop id="1">
	 <name>Демонстрационный магазин</name>
	 <description></description>
	 <path>/shop/</path>
	 <site_id>1</site_id>
	 <shop_image_small_max_width>100</shop_image_small_max_width>
	 <shop_country_id>175</shop_country_id>
	 <shop_currency id="1">
	 <shop_currency_name>руб.</shop_currency_name>
	 <shop_currency_international_name>RUR</shop_currency_international_name>
	 <shop_currency_value_in_basic_currency>1.000000</shop_currency_value_in_basic_currency>
	 <shop_currency_is_default>1.000000</shop_currency_is_default>
	 </shop_currency>
	 </shop>
	 </cart>';
	 *
	 * $result = $kernel->Xml2Array($xml);
	 *
	 * // Распечатаем результат
	 * print_r ($result);
	 * ?>
	 * </code>
	 * @return array XML-дерево в виде массива
	 */
	function OldXml2Array($xml)
	{
		// Берем заголовок
		$header = mb_substr($xml, 0, 200);

		// Отрезаем второй байт с 0x00, если была передана двухбайтовая кодировка (например, UTF-16)
		$header = str_replace(chr(0x00), '', $header);

		// Определяем кодировку, без u, т.к. работать можем еще не с UTF-8 версией файла
		preg_match("'<\?xml .*?encoding=\"(.*?)\".*?\?>'si", $header, $res);

		// Кодировка определилась
		if (isset($res[1]) && defined('SITE_CODING'))
		{
			$encoding = $res[1];

			if (strtoupper($encoding) != 'UTF-8' && function_exists('iconv'))
			{
				// Удаляем UTF-маркер (byte order marker) EF BB BF
				$xml = str_replace(chr(0xEF).chr(0xBB).chr(0xBF), '', $xml);
				$xml = @iconv($encoding, 'UTF-8' . "//IGNORE//TRANSLIT", $xml);
			}
		}

		// Убираем <![CDATA[ ... ]]>
		/*$xml = str_replace('<![CDATA[', '', $xml);
		$xml = str_replace(']]>', '', $xml);*/

		$result = array ();

		$parse_end = 0;

		// Стэк ссылок на родительские ресурсы
		$stack_array = array ();
		$stack_array[] = & $result;

		$position = 0;
		$i = 0;

		// Ищем упоминание хотя бы одной <![CDATA[, чтобы включить ее обработку
		// работа без <![CDATA[ идет намного быстрее
		$bIssetCdata = (strpos($xml, "<![CDATA[") !== FALSE);

		while (($parse_begin = strpos($xml, "<", $parse_end)) !== FALSE
		/*&& $i < 40000*/)
		{
			// Получаем закрывающий тег ">" в смещении от $parse_begin
			if (!($parse_end = strpos($xml, '>', $parse_begin)))
			{
				// Прерываем цикл, закрывающийся тэг не найден
				break;
			}

			// Извлекаем содержимое тэга
			$tag_content = substr($xml, $parse_begin + 1, $parse_end - $parse_begin - 1);

			$tag_content_first_char = mb_substr($tag_content, 0, 1);

			if (/* <?xml */
			$tag_content_first_char == "?" // <!DOCTYPE
			//|| $tag_content_first_char == "!") // т.к. сработает и на <![CDATA[
			|| $tag_content_first_char == "!D")
			{
				continue;
			}
			elseif ($tag_content_first_char == "/")
			{
				// Закрыли тег - родительским теперь является предыдущий
				$position--;
			}
			else
			{
				// New node
				$temp_array = array ();

				$tag_content_explode = explode(' ', $tag_content, 2);

				$temp_array['name'] = $tag_content_explode[0];

				// Отрезаем $xml до начала содержимого тега
				//$xml = trim(substr($xml, $parse_end + 1));
				$xml = substr($xml, $parse_end + 1);
				$parse_end = 0;

				// Если <![CDATA[ используется
				if ($bIssetCdata)
				{
					// Получаем КОД первого символа строки
					$sTmpFirstChar = ord(substr($xml, 0, 1));

					// Если нужно делать trim(), т.к. на больших строках операция ресурсоемкая
					if ($sTmpFirstChar == 0x20
					|| $sTmpFirstChar == 0x09
					|| $sTmpFirstChar == 0x0A
					|| $sTmpFirstChar == 0x0D
					|| $sTmpFirstChar == 0x00
					|| $sTmpFirstChar == 0x0B)
					{
						$xml = ltrim($xml);
					}
				}

				/*echo "<br>==".(mb_strpos($xml, '<![CDATA[', $parse_end) - $parse_end);
				echo "<br>==".htmlspecialchars(mb_substr($xml, $parse_end, 100));*/

				// Если <![CDATA[ используется
				// Ищем начало следующего тега, чтобы определить содержимое тега
				$is_cdata = $bIssetCdata && (substr($xml, 0, 9) == '<![CDATA[');

				if ($is_cdata)
				{
					$end_char = ']]>';
					$end_char_length = 3;
				}
				else
				{
					$end_char = '<';
					$end_char_length = 1;
				}

				$parse_end_content = strpos($xml, $end_char, $parse_end);

				$temp_array['value'] = substr($xml, $parse_end, $parse_end_content - $parse_end);

				// Смещаем указатель конца на конец сохраненных данных
				$parse_end = $parse_end_content + $end_char_length - 1;

				// Убираем <![CDATA[ ... ]]>
				if ($is_cdata)
				{
					$temp_array['value'] = str_replace(array('<![CDATA[', ']]>'), '', $temp_array['value']);
				}

				// Аттрибуты
				if (!empty ($tag_content_explode[1]))
				{
					// Заносим все найденные строки по шаблону A-zА-я="" в массив $attrs_tmp
					// s - если данный модификатор используется, метасимвол "точка" в шаблоне соответствует всем символам, включая перевод строк.
					preg_match_all("#(\S+)\s*=\s*\"(.*?)\"#su", $tag_content_explode[1], $attrs_tmp);

					$temp_array['attr'] = array ();

					foreach ($attrs_tmp[1] as $key => $attr_name)
					{
						// Формируем массив $result по формату: $result[ИмяСвойства] = ЗначениеСвойства, если в значении присутствует символ амперсанта, заменяем все подстроки совпадающие с элементами массива $search на строки из массива $replace для корректной обработки специальных символов в значениях свойств
						$temp_array['attr'][$attr_name] = html_entity_decode($attrs_tmp[2][$key], ENT_COMPAT, 'UTF-8');
					}
				}

				// Текущий уровень - последний в стэке
				$current_node = & $stack_array[$position];

				$current_node['children'][] = $temp_array;

				unset($temp_array);

				$tag_content_strlen = strlen($tag_content);

				// Если тег не закрывается тут же
				if (!($tag_content_strlen > 1
				&& substr($tag_content, $tag_content_strlen - 1, 1) == '/'))
				{
					$position++;
					$stack_array[$position] = & $current_node['children'][count($current_node['children']) - 1];
				}
			}
			$i++;
		}

		return $result;
	}

	function Xml2Array($xml)
	{
		return array(
			'children' => array(
				Core_Xml::xml2array($xml)
			)
		);
	}

	/**
	 * Метод преобразования IP в HEX
	 *
	 * @param string $ip ip-адрес
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $ip = $_SERVER['REMOTE_ADDR'];
	 *
	 * $result = $kernel->code_ip($ip);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return string ip-адрес, преобразованный в 16-ое значение для записи в базу
	 */
	function code_ip($ip)
	{
		return Core_Str::ip2hex($ip);
	}

	/**
	 * Метод декодирования IP из HEX
	 *
	 * @param srting $hex ip-адрес в 16-ричном формате
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $hex = 'c0a80007';
	 *
	 * $result = $kernel->decode_ip($hex);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return string ip-адрес в обычном формате для вывода
	 */
	function decode_ip($hex)
	{
		return Core_Str::hex2ip($hex);
	}

	/**
	 * Запрос URL с возвращением заголовка и данных
	 *
	 * @param string $url адрес ресурса
	 * @param int $port порт, по умолчанию 80
	 * @param int $timeout таймаут, по умолчанию 10
	 * @param array $param дополнительные параметры
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $url = 'http://www.hostcms.ru/';
	 *
	 * $row = $kernel->GetUrlWithHeader($url);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array ('header', 'data')
	 */
	function GetUrlWithHeader($url, $port = 80, $timeout = 10, $param = array())
	{
		$Core_Http = Core_Http::instance()
			->url($url)
			->port($port)
			->timeout($timeout);

		$result = array (
			'header' => '',
			'data' => ''
		);

		if (isset($param['method']))
		{
			$Core_Http->method($param['method']);
		}

		if (isset($param['header']['Content-Type']))
		{
			$Core_Http->contentType($param['header']['Content-Type']);
		}

		if (isset($param['POST']))
		{
			foreach ($param['POST'] as $key => $value)
			{
				$Core_Http->data($key, $value);
			}
		}

		try
		{
			$Core_Http->execute();
			$result['header'] = $Core_Http->getHeaders();
			$result['data'] = $Core_Http->getBody();
		}
		catch (Exception $e){}

		return $result;
	}

	/**
	 * Запрос URL
	 *
	 * @param string $url адрес ресурса
	 * @param int $port порт, по умолчанию 80
	 * @param int $timeout таймаут, по умолчанию 10
	 * @param array $param дополнительные параметры
	 * <code>
	 * <?php
	 * $kernel = & singleton('kernel');
	 *
	 * $url = 'http://www.hostcms.ru/';
	 *
	 * $result = $kernel->GetUrl($url);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($result);
	 * ?>
	 * </code>
	 * @return string
	 * @see GetUrlWithHeader()
	 */
	function GetUrl($url, $port = 80, $timeout = 10, $param = array())
	{
		$result = $this->GetUrlWithHeader($url, $port, $timeout, $param);
		return $result['data'];
	}

	/**
	 * Вычисление crc32 в диапазоне от -2147483647 до 2147483647.
	 * @param mixed $value значение
	 * @return int
	 */
	function crc32($value)
	{
		return Core::crc32($value);
	}

	/**
	 * Формирование панели редактирования элементов ЦА в клиентской части
	 *
	 * @param array $param Массив добавляемых элементов на панель
	 * - array $param['attributes'] - дополнительные атрибуты панели
	 * - str $param[0]['type'] тип, 0 - графическая кнопка, 1 - текст "как есть"
	 * - str $param[0]['image_path'] Путь к изображению
	 * - str $param[0]['href'] содержимое атибута href
	 * - str $param[0]['onclick'] содержимое атибута onclick
	 * - str $param[0]['alt'] alt к изображению
	 *
	 * @return сгенерированный код
	 */
	function ShowFlyPanel($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		$sClass = 'hostcmsPanel';
		$sAttributes = '';

		if (isset($param['attributes']))
		{
			foreach ($param['attributes'] as $key => $value)
			{
				if ($key == 'class')
				{
					$sClass .= ' '.$value;
				}
				else
				{
					$sAttributes .= $key.'="'.$value.'" ';
				}
			}

			unset($param['attributes']);
		}

		$block = '<div class="'.$sClass.'" '.$sAttributes.'>' .
		'<div class="hostcmsSubPanel hostcmsWindow">' .
		'<img src="/hostcmsfiles/images/drag_bg.gif" />';

		// Добавляем элементы на панель
		foreach ($param as $key => $button)
		{
			if (!isset($button['type']))
			{
				$button['type'] = 0;
			}

			switch ($button['type'])
			{
				// Графическая кнопка
				case 0:
					if (!isset($button['href']))
					{
						$button['href'] = 'javascript:void(0)';
					}
					$block .= '<a href="'.Core_Type_Conversion::toStr($button['href']).'" onclick="'.Core_Type_Conversion::toStr($button['onclick']).'" target="_blank"><img src="'.Core_Type_Conversion::toStr($button['image_path']).'" alt="'.Core_Type_Conversion::toStr($button['alt']).'" title="'.Core_Type_Conversion::toStr($button['alt']).'" width="16" height="16"></a>';
					break;
				// Как есть
				case 1:
					$block .= Core_Type_Conversion::toStr($button['text']);
				break;
				default:
				break;
			}
		}
		$block .= '</div></div>';

		return $block;
	}

	/**
	 * Определяет возможность отображения панели пользователю.
	 *
	 * @return bool
	 */
	function AllowShowPanel()
	{
		return Core_Auth::logged() && (!defined('ALLOW_PANEL') || ALLOW_PANEL) && strtoupper(SITE_CODING) == 'UTF-8';
	}

	/**
	 * Метод расчета шинглов для переданной строки текста
	 *
	 * @param array $array исходный массив с текстом, предварительно приведенным к первой нормальной форме
	 * @param int $step шаг шингла
	 * @param str $hash хэш, может быть пустым, crc32 или md5
	 * @return array
	 * @access private
	 */
	function GetShingles($array, $step = 2, $hash = 'crc32')
	{
		$position = 0;

		$count = count($array);

		$shingles_array = array ();

		while ($position + $step <= $count)
		{
			$shingle = '';
			for ($i = 0; $i < $step; $i++)
			{
				$shingle .= $array[$position + $i]." ";
			}

			$shingle = trim($shingle);

			switch ($hash)
			{
				case '' :
					$shingles_array[] = $shingle;
				break;
				case 'md5' :
					$shingles_array[] = md5($shingle);
				break;
				case 'crc32' :
					$shingles_array[] = crc32($shingle);
				break;
			}

			$position++;
		}

		return $shingles_array;
	}

	/**
	 * Метод очищает HTML от ненужных тегов, хеширует и возвращает массив хэшей слов
	 *
	 * @param string $text исходный текст;
	 * @param array $param массив дополнительных параметров
	 * - $param['hash_function'] = 'md5' {'md5','crc32',''} используемая ХЭШ-функция;
	 *
	 * @return array массив хэшей слов
	 * @access private
	 */
	function ClearHtml($text, $param =array())
	{
		return Core_Str::getHashes($text, $param);
	}

	/**
	 * Получение пути к директории, используемой для решения проблемы с ограничением
	 * количества поддиректорий в директории.
	 * Например, для сущности с кодом 17 и уровнем вложенности 3 в зависимости от
	 * типа возвращаемого значения создается
	 * строка 0/1/7 или массив из 3-х элементов - 0,1,7
	 * Для сущности с кодом 23987 и уровнем вложенности 3 создается
	 * строка 2/3/9 или массив из 3-х элементов - 2,3,9.
	 *
	 * @param $id код(идентификатор) сущности
	 * @param $level уровень вложенности
	 * @param $type тип возвращаемого результата, 0 (по умолчанию) - строка, 1 - массив
	 * @return mixed строка или массив названий групп
	 */
	function GetDirPath($id, $level = 1, $type = 0)
	{
		return Core_File::getNestingDirPath($id, $level, $type);
	}

	/**
	 * Создание директорий относительно корневой директории сайта
	 *
	 * @param $path путь к директории относительно корневой директории сайта
	 * @param $chmod права доступа к создаваемой директории.
	 * <br /> по умолчанию равен FALSE - используются права доступа, заданные в константе CHMOD.
	 * @return boolean
	 */
	function PathMkdir($path, $chmod = FALSE)
	{
		$path = strval($path);

		// Путь существует
		if (is_dir(CMS_FOLDER . $path))
		{
			return true;
		}
		else // Путь не существут
		{
			if ($chmod === FALSE)
			{
				$chmod = CHMOD;
			}
			// Получаем массив частей пути
			$mas_path = explode('/', $path);

			$path_part = CMS_FOLDER;

			// Цикл по частям пути
			foreach($mas_path as $key => $value)
			{
				$path_part .= $value;

				// Путь не существует
				if (!is_dir($path_part))
				{
					if (mkdir($path_part, $chmod))
					{
						@chmod($path_part, $chmod);
					}
					else
					{
						return FALSE;
					}
				}

				$path_part .= '/';
			}

			return true;
		}
	}

	/**
	 * Сортировка многомерного массива $array по полю $filed_name
	 *
	 * @param array $array многомерный массив
	 * @param string $filed_name название поля, по котормоу производится сортировка
	 * @param int $type направление сортировки, SORT_ASC или SORT_DESC, по умолчанияю SORT_ASC
	 * @return array
	 */
	function my_array_multisort(& $array, $filed_name, $type = SORT_ASC)
	{
		$aTmp = array();

		if (count($array) > 0)
		{
			foreach ($array as $key => $value)
			{
				// Устанавливаем первым то поле, по которому будем сортировать
				if (isset($value[$filed_name]))
				{
					$aTmp[$key][$filed_name] = $value[$filed_name];
				}

				if (count($value) > 0)
				{
					// копируем оставшиеся поля
					foreach ($value as $field_key => $field_value)
					{
						$aTmp[$key][$field_key] = $field_value;
					}
				}
			}

			array_multisort($aTmp, $type);
		}

		return $aTmp;
	}

	/**
	 * Определение доступности php-функции
	 *
	 * @param string $function_name имя функции
	 * @return boolean
	 */
	function function_enabled($function_name)
	{
		return Core::isFunctionEnable($function_name);
	}
}
