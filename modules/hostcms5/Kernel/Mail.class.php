<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, Класс "Mail" для получения писем с почтовых серверов.
 *
 * Файл: /modules/Kernel/Mail.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class Mail
{
	/**
	 * Получение списка писем
	 *
	 * @param array $param Массив параметров
	 * - $param['email_pop3'] str Адрес хоста для подключения по протоколу POP3
	 * - $param['email_imap'] str Адрес хоста для подключения по протоколу IMAP
	 * - $param['email_login'] str Имя почтового ящика пользователя
	 * - $param['email_password'] str Пароль к почтовому ящику пользователя
	 * - $param['delete'] bool  Флаг, удалять ли письма после просмотра
	 * - $param['secury'] bool Флаг, является ли подключение безопасным
	 * @return array Массив писем
	 */
	function GetMsgList ($param = array())
	{
		$Core_Mail_Imap = new Core_Mail_Imap();
		$Core_Mail_Imap->login($param['email_login'])
			->password($param['email_password']);

		// Подключение по протоколу IMAP
		if (isset($param['email_imap']))
		{
			// Порт для IMAP-соединения по умолчанию
			$Core_Mail_Imap->port = 143;

			// Имя хоста
			$Core_Mail_Imap->server($param['email_imap']);
		}

		// Подключение по протоколу POP3 (предпочтение)
		if (isset($param['email_pop3']) && Core_Type_Conversion::toStr($param['email_pop3']) != '')
		{
			// Порт для POP3-соединения по умолчанию
			$Core_Mail_Imap->port = 110;
			$Core_Mail_Imap->server($param['email_pop3']);
		}

		// Безопасное соединение TSL/SSL
		if (isset($param['secury']) && Core_Type_Conversion::toBool($param['secury']))
		{
			$Core_Mail_Imap->ssl(TRUE);
		}

		// Удалить письма после просмотра
		isset($param['delete']) && $Core_Mail_Imap->delete($param['delete']);

		$Core_Mail_Imap->execute();

		$aErrors = $Core_Mail_Imap->getErrors();

		// Соединение с почтовым сервером не установлено
		if (count($aErrors))
		{
			return array(
				'error' => $Core_Mail_Imap->getLastError()
			);
		}

		// Количество писем в почтовом ящике
		$aLetters = $Core_Mail_Imap->getMessages();
		if (count($aLetters))
		{
			return array(
				'error' => array(
					'no_letters' => "Сообщений нет."
				)
			)
		}

		return $aLetters;
	}
}