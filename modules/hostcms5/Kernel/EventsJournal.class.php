<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс журналирования событий.
 *
 * Файл: /modules/Kernel/EventsJournal.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class EventsJournal
{
	/**
	 * Метод протоколирования получения доступа к системе управления
	 *
	 * @param string $login логин пользователя
	 * @param string_type $message текст сообщения для события
	 * @param string $status статус события (значимость события), необязательный параметр.
	 * <br />Статус имеет значения:
	 * <br />0 – Нейтральное
	 * <br />1 – Успешное
	 * <br />2 – Низкий уровень критичности
	 * <br />3 – Средний уровень критичности
	 * <br />4 – Наивысший уровень критичности
	 *
	 * @param string $send_email отправлять администратору сообщение о произошедшем событии (по умолчанию true), необязательный параметр
	 * <code>
	 * <?php
	 * $EventsJournal = new EventsJournal();
	 *
	 * $login = 'tygra';
	 * $message = 'Текст сообщения';
	 * $status = 0;
	 *
	 * $result = $EventsJournal->log_access($login, $message, $status);
	 *
	 * if ($result)
	 * {
	 * 	echo "Доступ получен";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка получения доступа";
	 * }
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function log_access($login, $message, $status = 0, $send_email = true)
	{
		// Если статус события = -1, происходит запись ошибки E_STRICT
		if ($status == -1)
		{
			$status = 0;
		}

		Core::$log
			->clear()
			->status($status)
			->notify($send_email);

		if ($login !== FALSE)
		{
			Core::$log->login($login);
		}

		Core::$log->write($message);

		return TRUE;
	}
}
