<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Модуль Kernel.
 *
 * Файл: /modules/Kernel/Kernel.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */

// Путь к директории модулей
$sModulesPath = CMS_FOLDER . 'modules/hostcms5/';

// Обработка при использовании IIS
if (!isset($_SERVER['REQUEST_URI']))
{
	$_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];

	// Объединяем со строкой запроса GET
	if (!empty ($_SERVER['QUERY_STRING']))
	{
		$_SERVER['REQUEST_URI'] .= strpos($_SERVER['REQUEST_URI'], '?') ? "&" : "?";
		$_SERVER['REQUEST_URI'] .= $_SERVER['QUERY_STRING'];
	}
}

$GLOBALS['FILE_CLASS']['jshttprequest'] = $sModulesPath . 'Kernel/ajax/JsHttpRequest.php';
$GLOBALS['FILE_CLASS']['eventsjournal'] = $sModulesPath . 'Kernel/EventsJournal.class.php';
$GLOBALS['FILE_CLASS']['password'] = $sModulesPath . 'Kernel/Password.class.php';
$GLOBALS['FILE_CLASS']['wysiwyg'] = $sModulesPath . 'Kernel/Wysiwyg.class.php';
$GLOBALS['FILE_CLASS']['admin'] = $sModulesPath . 'Kernel/Admin.class.php';
$GLOBALS['FILE_CLASS']['file'] = $sModulesPath . 'Kernel/File.class.php';
$GLOBALS['FILE_CLASS']['image'] = $sModulesPath . 'Kernel/Image.class.php';
$GLOBALS['FILE_CLASS']['url'] = $sModulesPath . 'Kernel/Url.class.php';
$GLOBALS['FILE_CLASS']['rsswrite'] = $sModulesPath . 'Kernel/RssWrite.class.php';
$GLOBALS['FILE_CLASS']['rss'] = $sModulesPath . 'Kernel/RssWrite.class.php';
$GLOBALS['FILE_CLASS']['rssread'] = $sModulesPath . 'Kernel/RssRead.class.php';
$GLOBALS['FILE_CLASS']['captcha'] = $sModulesPath . 'Kernel/Captcha.class.php';
$GLOBALS['FILE_CLASS']['xmlparser'] = $sModulesPath . 'Kernel/XMLparser.class.php';
$GLOBALS['FILE_CLASS']['archive_tar'] = $sModulesPath . 'Kernel/tar.class.php';
$GLOBALS['FILE_CLASS']['mail'] = $sModulesPath . 'Kernel/Mail.class.php';
$GLOBALS['FILE_CLASS']['mysql'] = $sModulesPath . 'Kernel/Mysql.class.php';
$GLOBALS['FILE_CLASS']['database'] = $sModulesPath . 'Kernel/Mysql.class.php';
$GLOBALS['FILE_CLASS']['externalxml'] = $sModulesPath . 'Kernel/ExternalXml.class.php';
$GLOBALS['FILE_CLASS']['graphic'] = $sModulesPath . 'Kernel/Graphic.class.php';
$GLOBALS['FILE_CLASS']['dateclass'] = $sModulesPath . 'Kernel/Date.class.php';
$GLOBALS['FILE_CLASS']['adminmenu'] = $sModulesPath . 'Kernel/AdminMenu.class.php';

// Для 4-й версии инициализируем сразу
if (PHP_VERSION < 5)
{
	foreach ($GLOBALS['FILE_CLASS'] as $path)
	{
		require_once($path);
	}
}

/**
 * Подключает файл, содержащий класс $className
 *
 * @param string $className
 * @access private
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
function __includeclass($className)
{
	if (isset($GLOBALS['FILE_CLASS'][$className]))
	{
		// Подключаем класс
		require_once($GLOBALS['FILE_CLASS'][$className]);
		return TRUE;
	}

	return FALSE;
}

$GLOBALS['HOSTCMS_CLASS']['admin_forms'] = 'admin_forms';
$GLOBALS['HOSTCMS_CLASS']['admin_forms_fields'] = 'admin_forms';
$GLOBALS['HOSTCMS_CLASS']['advertisement'] = 'Advertisement';
$GLOBALS['HOSTCMS_CLASS']['affiliate'] = 'shop';
$GLOBALS['HOSTCMS_CLASS']['warehouse'] = 'shop';
$GLOBALS['HOSTCMS_CLASS']['sitetemplate'] = 'Site';
$GLOBALS['HOSTCMS_CLASS']['backup'] = 'Backup';
$GLOBALS['HOSTCMS_CLASS']['mysqldump'] = 'Backup';
$GLOBALS['HOSTCMS_CLASS']['sns'] = 'Sns';
$GLOBALS['HOSTCMS_CLASS']['cache'] = 'Cache';
$GLOBALS['HOSTCMS_CLASS']['compression'] = 'Compression';
$GLOBALS['HOSTCMS_CLASS']['constants'] = 'Constants';
$GLOBALS['HOSTCMS_CLASS']['counter'] = 'Counter';
$GLOBALS['HOSTCMS_CLASS']['documents'] = 'Documents';
$GLOBALS['HOSTCMS_CLASS']['eventsjournaladmin'] = 'EventsJournal';
$GLOBALS['HOSTCMS_CLASS']['execsqlquery'] = 'ExecSqlQuery';
$GLOBALS['HOSTCMS_CLASS']['filemanager'] = 'FileManager';
$GLOBALS['HOSTCMS_CLASS']['forms'] = 'Forms';
$GLOBALS['HOSTCMS_CLASS']['forums'] = 'Forums';
$GLOBALS['HOSTCMS_CLASS']['information_blocks'] = 'InformationSystems';
$GLOBALS['HOSTCMS_CLASS']['informationsystem'] = 'InformationSystems';
$GLOBALS['HOSTCMS_CLASS']['informationsystems'] = 'InformationSystems';
$GLOBALS['HOSTCMS_CLASS']['ip'] = 'ip';
$GLOBALS['HOSTCMS_CLASS']['lib'] = 'lib';
$GLOBALS['HOSTCMS_CLASS']['lists'] = 'Lists';
$GLOBALS['HOSTCMS_CLASS']['maillist'] = 'Maillist';
$GLOBALS['HOSTCMS_CLASS']['menu'] = 'Structure';
$GLOBALS['HOSTCMS_CLASS']['modules'] = 'Modules';
$GLOBALS['HOSTCMS_CLASS']['polls'] = 'Polls';
$GLOBALS['HOSTCMS_CLASS']['search'] = 'Search';
$GLOBALS['HOSTCMS_CLASS']['seo'] = 'seo';
$GLOBALS['HOSTCMS_CLASS']['shop'] = 'shop';
$GLOBALS['HOSTCMS_CLASS']['site'] = 'Site';
$GLOBALS['HOSTCMS_CLASS']['siteusers'] = 'Site_users';
$GLOBALS['HOSTCMS_CLASS']['site_users'] = 'Site_users';
$GLOBALS['HOSTCMS_CLASS']['structure'] = 'Structure';
$GLOBALS['HOSTCMS_CLASS']['support'] = 'Support';
$GLOBALS['HOSTCMS_CLASS']['tag'] = 'tag';
$GLOBALS['HOSTCMS_CLASS']['templates'] = 'Templates';
$GLOBALS['HOSTCMS_CLASS']['typograph'] = 'typograph';
$GLOBALS['HOSTCMS_CLASS']['user_access'] = 'UserAccess';
$GLOBALS['HOSTCMS_CLASS']['update'] = 'update';
$GLOBALS['HOSTCMS_CLASS']['xsl'] = 'Xsl';

/**
 * Загрузка модулей по мере вызова классов
 *
 * @param string $className имя класса
 * @access private
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
function autoload($className)
{
	$className = mb_strtolower($className);

	$MTimeBegin = Core::getmicrotime();

	/* По имени класса определяем имя модуля */
	if (isset($GLOBALS['HOSTCMS_CLASS'][$className]))
	{
		$module = $GLOBALS['HOSTCMS_CLASS'][$className];
	}
	else
	{
		// Иначе пытаемся инициализировать по имени модуля
		$module = $className;
	}

	// Пытаемся инициализировать по имени класса
	if (__includeclass($className))
	{
		return TRUE;
	}

	// Массив обязательных для загрузки модулей
	$GLOBALS['HOSTCMS_NECESSARY_MODULES'] = array(
		'Modules', 'UserAccess', 'ip', 'Structure', 'Documents', 'Xsl', 'Templates', 'Site', 'admin_forms'
	);

	// Соответствие имен модулей 5-й версии к 6-й версии
	$ModulesHostCMS5 = array(
		'InformationSystems' => 'informationsystem',
		'ip' => 'ipaddress',
		'Advertisement' => 'advertisement',
		'Search' => 'search',
		'Compression' => 'compression',
		'Constants' => 'constant',
		'Counter' => 'counter',
		'Lists' => 'list',
		'Cache' => 'cache',
		'Site_users' => 'siteuser',
		'SiteUsers' => 'siteuser',
		'Forms' => 'form',
		'Polls' => 'poll',
		'Forums' => 'forum',
		'messages' => 'message',
		'Maillist' => 'maillist',
	);

	$newModuleName = isset($ModulesHostCMS5[$module])
		? $ModulesHostCMS5[$module]
		: $module;

	// Если имя модуля было определено и модуль есть в списке активных
	if (isset(Core::$modulesList[$newModuleName]) && Core::$modulesList[$newModuleName]->active == 1
	|| in_array($module, $GLOBALS['HOSTCMS_NECESSARY_MODULES']))
	{
		$kernel = & singleton('kernel');
		$kernel->AddModule($module);
	}

	// Увеличиваем общее время инициализации модулей
	if (isset($GLOBALS['MTime']))
	{
		$GLOBALS['MTime'] += Core::getmicrotime() - $MTimeBegin;
	}
}

if (function_exists('spl_autoload_register'))
{
	spl_autoload_register('autoload');
}
else
{
	/**
	 * @package HostCMS 5
	 * @author Hostmake LLC
	 * @version 5.x
	 */
	function __autoload($className)
	{
		return autoload($className);
	}
}

/**
 * Singleton
 *
 * @param string $class Имя класса
 * @return object
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
function & singleton($class)
{
	static $instances;

	if (!is_array($instances))
	{
		$instances = array();
	}

	if (!isset($instances[$class]) && class_exists($class))
	{
		$instances[$class] = new $class;
	}

	return $instances[$class];
}

if (!function_exists('com_create_guid'))
{
	/**
	 * @package HostCMS 5
	 * @author Hostmake LLC
	 * @version 5.x
	 */
	function com_create_guid()
	{
		$charid = mb_strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);// "-"
		$uuid = chr(123)// "{"
		.mb_substr($charid, 0, 8).$hyphen
		.mb_substr($charid, 8, 4).$hyphen
		.mb_substr($charid,12, 4).$hyphen
		.mb_substr($charid,16, 4).$hyphen
		.mb_substr($charid,20,12)
		.chr(125);// "}"
		return $uuid;
	}
}

if (!function_exists('bcpow'))
{
	/**
	 * @package HostCMS 5
	 * @author Hostmake LLC
	 * @version 5.x
	 */
	function bcpow($a, $b)
	{
		return exp($b * log($a));
	}
}

// PHP 5 >= 5.2.0
if (!function_exists('array_fill_keys'))
{
	/**
	 * @package HostCMS 5
	 * @author Hostmake LLC
	 * @version 5.x
	 */
	function array_fill_keys($keys, $value)
	{
		return array_combine($keys, array_fill(0, count($keys), $value));
	}
}

if (!function_exists('array_intersect_key'))
{
	/**
	 * @package HostCMS 5
	 * @author Hostmake LLC
	 * @version 5.x
	 */
	function array_intersect_key($isec, $keys)
	{
		$argc = func_num_args();
		if ($argc > 2)
		{
			for ($i = 1; !empty($isec) && $i < $argc; $i++)
			{
				$arr = func_get_arg($i);
				foreach (array_keys($isec) as $key)
				{
					if (!isset($arr[$key]))
					{
						unset($isec[$key]);
					}
				}
			}
			return $isec;
		}
		else
		{
			$res = array();
			foreach (array_keys($isec) as $key)
			{
				if (isset($keys[$key]))
				{
					$res[$key] = $isec[$key];
				}
			}
			return $res;
		}
	}
}

if (!function_exists('exif_imagetype'))
{
	/**
	 * @package HostCMS 5
	 * @author Hostmake LLC
	 * @version 5.x
	 */
	function exif_imagetype($filename)
	{
		if (file_exists($filename))
		{
			if ((list($width, $height, $type, $attr) = @getimagesize($filename)) !== false)
			{
				return $type;
			}
		}
		return false;
	}
}

$config_path = $sModulesPath . 'Kernel/config/config.php';
if (is_file($config_path))
{
	require_once($config_path);
}

if (function_exists('mb_internal_encoding'))
{
	require_once($sModulesPath . 'Kernel/Mbstring.fns.php');
}

require_once($sModulesPath . 'Kernel/Message.fns.php');
require_once($sModulesPath . 'Kernel/Security.fns.php');
require_once($sModulesPath . 'Kernel/Kernel.class.php');

// Время выполнения запросов SQL
$GLOBALS['SQL_time'] = 0;

$kernel = & singleton('kernel');
$GLOBALS['kernel'] = & $kernel;

require_once($sModulesPath . "Kernel/admin_output.fns.php");

$DataBase = & singleton('DataBase');
$DataBase->db_connect();

$current_lng = !defined('CURRENT_LNG') && isset($_SESSION["current_lng"])
	? Core_Type_Conversion::toStr($_SESSION["current_lng"])
	: DEFAULT_LNG;

require_once($sModulesPath . 'Kernel/Core.php');

// Если нет HTTP_HOST, то ставим его равным пустоте
if (!isset($_SERVER['HTTP_HOST']))
{
	$_SERVER['HTTP_HOST'] = '';
}

if (is_file($sModulesPath . 'Kernel/lng/'.$current_lng.'/'.$current_lng.'.php'))
{
	require_once($sModulesPath . 'Kernel/lng/'.$current_lng.'/'.$current_lng.'.php');
}
else
{
	if (is_file($sModulesPath . 'Kernel/lng/'.DEFAULT_LNG.'/'.DEFAULT_LNG.'.php'))
	{
		require_once($sModulesPath . 'Kernel/lng/'.DEFAULT_LNG.'/'.DEFAULT_LNG.'.php');
	}
	else
	{
		show_error_message('Ошибка! Отсутствует языковой файл для модуля "Ядро".');
	}
}

/**
 * Заголовок 404
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
function ShowHeader404()
{
	/*function_exists('apache_lookup_uri')
		? header('http/1.0 404 Not Found')
		: header('Status: 404 Not Found');*/
	Core_Page::instance()->response->status(404);
}
