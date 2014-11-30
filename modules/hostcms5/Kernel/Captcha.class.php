<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для работы с CAPTCHA.
 *
 * Файл: /modules/Kernel/captcha.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class Captcha
{
	/**
	 * Генерация строки CAPTCHA
	 *
	 * @return str
	 */
	function GenerateKeyString()
	{
		return Core_Captcha::createValue();
	}

	/**
	 * Метод генерирует и возвращает уникальный индекс для CAPTCHA
	 *
	 * @return int
	 */
	function GetCaptchaId()
	{
		return Core_Captcha::getCaptchaId();
	}

	/**
	 * Метод проверки соответствия введенного пользователем текста хранимому в сессии
	 * Пример использования метода см. в руководстве по интеграции.
	 *
	 * @param int $captchaId уникальный номер CAPTCHA
	 * @param string $value текст, введённый пользователем
	 * @return boolean
	 */
	function ValidCaptcha($captchaId, $value)
	{
		return Core_Captcha::valid($captchaId, $value);
	}

	/**
	 * Метод для построения изображения CAPTCHA и помещения его текста в сессию
	 *
	 * @param int $captchaId - уникальный номер CAPTCHA
	 * @param array $param
	 * - $param['allowed_symbols'] Символы, исключающие ошибки распознавания (o = 0, i = j, 1 = l) используются для формирования строки
	 * - $param['length'] Количество символов
	 * - $param['width'] Ширина базового изображения
	 * - $param['height'] Высота базового изображения
	 * - $param['dest_x'] Ширина реального изображения
	 * - $param['dest_y'] Высота реального изображения
	 * - $param['foreground_color'] Цвет текста в RGB задаётся массивом
	 * - $param['background_color'] Цвет фона в RGB задаётся массивом
	 * - $param['image_extension'] Расширение выходного изображения CAPTCHA (jpg, gif или png)
	 */
	function BuildCapture($captchaId, $param = array())
	{
		$Core_Captcha = new Core_Captcha();
		return $Core_Captcha->build($captchaId, $param = array());
	}
}
