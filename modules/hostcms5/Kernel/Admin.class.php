<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, методы управления разделом центра администрирования.
 *
 * Файл: /modules/Kernel/Admin.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class Admin
{
	function Admin()
	{
		//session_name('HOSTCMSSESSID');
	}

	/**
	 * Запуск сессии для центра администрирования
	 * @param int $expires время сохранения идентификатора сессии в cookies,
	 * необязательный параметр, по умолчанию имеет значение 86400 (24 часа)
	 *
	 */
	function AdminSessionStart($expires = 86400)
	{
		return Core_Auth::adminSessionStart($expires);
	}

	/**
	 * Метод старта сессии для раздела администрирования
	 */
	function system_init()
	{
		return Core_Auth::systemInit();
	}

	/**
	 * Метод вносит в сессию текущий CURRENT_SITE, инициализирует константы для данного сайта
	 *
	 */
	function admin_init()
	{
		if (!defined('DISABLE_COMPRESSION') || !DISABLE_COMPRESSION)
		{
			// Если сжатие уже не включено на сервере
			// директивой zlib.output_compression = On
			// Для Off ini_get() возвращает 0, для On возвращает 1
			// http://ru.php.net/manual/en/function.ini-get.php
			// A boolean ini value of off will be returned as an empty string or "0"
			// while a boolean ini value of on will be returned as "1".
			// The function can also return the literal string of INI value.
			if (ini_get('zlib.output_compression') == 0)
			{
				// включаем сжатие буфера вывода
				ob_start("ob_gzhandler");
        	}
		}

		// Выполняем только после регистрации пользователя
		if ($this->UserSessionValid())
		{
			// Устанавливаем текущий сайт
			$this->SetCurrentSite();

			if (defined('CURRENT_SITE'))
			{
				$oSite = Core_Entity::factory('Site', intval(CURRENT_SITE));

				if (is_null($oSite->name))
				{
					$oSite = $oSite->getFirstSite();
				}
				Core::initConstants($oSite);
			}
		}
	}

	/**
	 * Метод устанавливает текущий сайт, обрабатывает изменение текущего сайта
	 */
	function SetCurrentSite()
	{
		return Core_Auth::setCurrentSite();
	}

	/**
	 * Проверяет, авторизован ли текущий пользователь в центре администрирования
	 *
	 * @return bool
	 */
	function UserSessionValid()
	{
		return Core_Auth::logged();
	}

	/**
	 * Метод проверки авторизации пользователя
	 *
	 * @param string $aModuleNames путь к модулю
	 * @return boolean true в случае  наличия у авторизированного пользователя прав доступа к данному модулю на текущем сайте или прерывает выполнение страницы, производя редирект на страницу авторизации в противном случае
	 */
	function admin_session_valid($aModuleNames)
	{
		return Core_Auth::authorization($aModuleNames);
	}

	/**
	 * Метод производит авторизацию пользователя в разделе администрирования
	 *
	 * @param string $login логин
	 * @param string $password пароль
	 * @return mixed
	 * <br />true -- автооризация произведена успешно
	 * <br />false -- неправильные данные доступа
	 * <br />-1 -- не истекло время до следующей попытки авторизации
	 */
	function UserAuth($login, $password)
	{
		$return = Core_Auth::login($login, $password, $assignSessionToIp = isset($_POST['ip']));
		Core_Auth::setCurrentSite();
		return $return;
	}
}
