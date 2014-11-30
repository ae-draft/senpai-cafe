<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для работы с адресной строкой.
 *
 * Файл: /modules/Kernel/url.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class url
{
	/**
	 * Метод сокращения длины строки
	 *
	 * @param string $str строка
	 * @param int $max_lenght максимальная длина строки
	 * <code>
	 * <?php
	 * $url = new url();
	 *
	 * $str = 'Тестовая строка с текстом';
	 * $max_lenght = 19;
	 *
	 * $newstr= $url->link_len($str, $max_lenght);
	 *
	 * echo $newstr;
	 * ?>
	 * </code>
	 * @return string сокращенная строка
	 */
	function link_len($str, $max_lenght)
	{
		return Core_Str::cut($str, $max_lenght);
	}

	/**
	 * Уменьшает длину слов в строке, имеющий длину больше $max_word_len
	 *
	 * @param string $str строка
	 * @param int $max_word_len максимальная длина слова
	 * <code>
	 * <?php
	 * $url = new url();
	 *
	 * $str = 'Строка с тестовым текстом';
	 * $max_word_len = 5;
	 *
	 * $newstr= $url->CutWordLen($str, $max_word_len);
	 *
	 * echo $newstr;
	 * ?>
	 * </code>
	 * @return string строка
	 */
	function CutWordLen($str, $max_word_len)
	{
		return Core_Str::cutWords($str, $max_word_len);
	}

	/**
	 * Метод транслитерации с переводом символам исходного текста к прописным
	 *
	 * @param string $string
	 * <code>
	 * <?php
	 * $url = new url();
	 *
	 * $string = 'Строка с тестовым текстом';
	 *
	 * $newstr= $url->translit_lower($string);
	 *
	 * echo $newstr;
	 * ?>
	 * </code>
	 * @return string
	 */
	function translit_lower($string)
	{
		return Core_Str::transliteration($string);
	}
}
