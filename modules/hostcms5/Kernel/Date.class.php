<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для работы с датами.
 * 
 * Файл: /modules/Kernel/date.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class DateClass
{
	/**
	* Метод преобразовывает дату в формате SQL в число секунд с начала эпохи
	*
	* @param string $date дата в формате MySQL
	* @param string $date_separator разделитель даты (по умолчанию "-")
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $date = date('Y-m-d H:i:s');
	* $date_separator = '-';
	*
	* echo $DateClass->DateSqlToUnix($date, $date_separator);
	* ?>
	* </code>
	* @return mixe дата в формате числа секунд с начала эпохи в случае успешного выполнения, false в противном случае
	*/
	function DateSqlToUnix($date, $date_separator='-')
	{
		return Core_Date::sql2timestamp($date);
	}
	/**
	* Метод преобразовывает дату из числа секунд в формат SQL
	*
	* @param string $timestamp число секунд с начала эпохи
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $timestamp = 1221030528;
	*
	* echo $DateClass->DateUnixToSQL($timestamp);
	* ?>
	* </code>
	* @return string дата в формате SQL
	*/
	function DateUnixToSQL($timestamp)
	{
		return Core_Date::timestamp2sql($timestamp);
	}

	/**
	* Метод преобразует формат выводы даты из sql-формата в формат, указанный в константе DATE_FORMAT
	*
	* @param string $date принимаемая дата
	* @param string $date_format формат даты, необязательное поле, по умолчанию %d.%m.%Y
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $date = date('Y-m-d H:i:s');
	*
	* echo $DateClass->date_format($date, $date_format = "%d.%m.%Y");
	* ?>
	* </code>
	* @return mixed преобразованная дата в случае успешного выполнения, false в противном случае 
	*/
	function date_format($date, $date_format = "%d.%m.%Y")
	{
		if (!$date_format)
		{
			if (defined('DATE_FORMAT'))
			{
				$date_format = DATE_FORMAT;
			}
		}

		if ($date != '0000-00-00' && $date != '0000-00-00 00:00:00')
		{
			$date = explode(' ', $date);
			$date_mas = explode('-', $date[0]); // Разделяем дату по '-'

			if (count($date_mas) != 3)
			{
				return false;
			}

			$date = mktime(0, 0, 0, $date_mas[1], $date_mas[2], $date_mas[0]);

			if ($date < 0)
			{
				$date = 0;
			}
			return strftime($date_format, $date);
		}
		else
		{
			$datetime = mktime('00', '00', '00', '10', '10', '1970');
			return preg_replace('/[0-9]/', '0', strftime($date_format, $datetime));
		}
	}

	/**
	* Метод преобразует формат вывода даты-времени из SQL-формата в формат, указанный в константе DATE_TIME_FORMAT
	*
	* @param string $date принимаемая дата в SQL формате
	* <code>
	 * <?php
	 * $DateClass = new DateClass();
	 *
	 * $date = date('Y-m-d H:i:s');
	 *
	 * echo $DateClass->datetime_format($date);
	 * ?>
	 * </code>
	* @return mixed преобразованная дата в случае успешного выполнения, false в противном случае 
	*/
	function datetime_format($date)
	{
		$format = defined('DATE_TIME_FORMAT') ? DATE_TIME_FORMAT : "%d.%m.%Y %H:%M:%S";

		if ($date != '0000-00-00 00:00:00')
		{
			$date = explode(' ', $date);
			if (!isset($date[1]))
			{
				return false;
			}

			// Разделяем время на ЧЧ.ММ.СС
			$time_mas = explode(':', $date[1]);
			if (count($time_mas) != 3)
			{
				return false;
			}

			// Разделяем дату
			$date_mas = explode('-', $date[0]); // Разделяем дату по '-'

			if (count($date_mas) != 3)
			{
				return false;
			}

			$datetime = mktime($time_mas[0], $time_mas[1], $time_mas[2], $date_mas[1], $date_mas[2], $date_mas[0]);

			return $datetime > 0 ?
				strftime($format, $datetime)
				: FALSE;
		}
		else
		{
			$datetime = mktime('00', '00', '00', '10', '10', '1970');
			return preg_replace('/[0-9]/', '0', strftime($format, $datetime));
		}
	}

	/**
	* Метод преобразования даты и времени из формата ДД.ММ.ГГ ЧЧ:ММ:СС в формат MySQL
	*
	* @param string $date принимаемая дата
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $date = date('d.m.Y H:i:s');
	*
	* echo $DateClass->datetime_format_sql($date);
	* ?>
	* </code>
	* @return mixed датавремя в формате MySQL в случае успешного выполнения, false в противном случае
	*/
	function datetime_format_sql($date)
	{
		return Core_Date::datetime2sql($date);
	}

	/**
	* Метод преобразования даты из формата ДД.ММ.ГГГГ в SQL формат
	*
	* @param string $date принимаемая дата
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $date = date('d.m.Y');
	*
	* echo $DateClass->date_format_sql($date);
	* ?>
	* </code>
	* @return mixed дата в формате MySQL в случае успешного выполнения, false в противном случае
	*/
	function date_format_sql($date)
	{
		return Core_Date::date2sql($date);
	}

	/**
	* Получение значения даты в формате MySQL путем изменения переданной на заданное число секунд
	*
	* @param int $second число секунд
	* @param string $date принимаемая дата
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $second = 1221030528;
	* $date = date('Y-m-d');
	*
	* echo $DateClass->ChangeMySqlDate($second, $date='');
	* ?>
	* </code>
	* @return string измененная дата
	*/
	function ChangeMySqlDate($second, $date='')
	{
		$second = intval($second);

		if ($date == '')
		{
			$date = date('Y-m-d H:i:s');
		}

		$datetime = $this->DateSqlToUnix($date) + $second;
		return $this->DateUnixToSQL($datetime);
	}

	/**
	* Получение значения даты в формате ДД.ММ.ГГГГ ЧЧ:ММ:СС путем изменения переданной на заданное число секунд
	*
	* @param int $second число секунд
	* @param string $date дата в формате ДД.ММ.ГГГГ ЧЧ:ММ:СС
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $second = 100000;
	* $date = date('d.m.Y H:i:s');
	*
	* echo $DateClass->ChangeNormalDate($second, $date);
	* ?>
	* </code>
	* @return string дата в формате ДД.ММ.ГГГГ ЧЧ:ММ:СС
	*/
	function ChangeNormalDate($second, $date = '')
	{
		$second = intval($second);

		if ($date == '')
		{
			$date = date('d.m.Y H:i:s');
		}
		$datetime = $this->DateSqlToUnix($this->datetime_format_sql($date)) + $second;
		return $this->datetime_format($this->DateUnixToSQL($datetime));
	}

	/**
	* Метод для центра администрирования,
	* преобразует формат выводы даты из sql-формата в формат d.m.Y
	*
	* @param string $date принимаемая дата
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $date = date('Y-m-d H:i:s');
	*
	* echo $DateClass->admin_date_format($date);
	* ?>
	* </code>
	* @return mixed преобразованная дата в случае успешного выполнения, false в противном случае 
	*/
	function admin_date_format($date)
	{
		return Core_Date::sql2date($date);
	}

	/**
	* Метод для центра администрирования,
	* преобразует формат выводы даты из sql-формата в формат  d.m.Y H:i:s
	*
	* @param string $date принимаемая дата
	* <code>
	* <?php
	* $DateClass = new DateClass();
	*
	* $date = date('Y-m-d H:i:s');
	*
	* echo $DateClass->admin_datetime_format($date);
	* ?>
	* </code>
	* @return mixed преобразованная дата в случае успешного выполнения, false в противном случае 
	*/
	function admin_datetime_format($date)
	{
		return Core_Date::sql2datetime($date);
	}
}
