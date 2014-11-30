<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Обновление".
 * 
 * Файл: /modules/update/update.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class update
{
	/**
	* Получение пути к директории update
	*
	* @return string путь к файлу
	*/
	function GetUpdatePath()
	{
		return Update_Controller::instance()->getPath();
	}

	/**
	 * Получение информации об обновлениях
	 *
	 * @param array $param массив параметров
	 * - ['login'] Логин пользователя
	 * - ['contract'] Номер договора
	 * - ['pin'] Пин-код
	 * - ['cms_folder'] Папка установки системы
	 * - ['php_version'] Версия PHP
	 * - ['mysql_version'] Версия MySQL
	 * - ['update_file'] Файл, куда будет записана информация об обновлениях
	 * - ['update_id'] Значение константы HOSTCMS_UPDATE_NUMBER
	 * - ['domain'] Текущий домен
	 * - ['update_server'] Значение константы HOSTCMS_UPDATE_SERVER
	 * 
	 * @return bool True в случае успеха, иначе False
	 * @access private
	 */
	function GetUpdate($param)
	{
		return Update_Controller::instance()
			->login($param['login'])
			->contract($param['contract'])
			->pin($param['pin'])
			->cms_folder($param['cms_folder'])
			->php_version($param['php_version'])
			->mysql_version($param['mysql_version'])
			->update_id($param['update_id'])
			->domain($param['domain'])
			->update_server($param['update_server'])
			->getUpdates($param);
	}
}
