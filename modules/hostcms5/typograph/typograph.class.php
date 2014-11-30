<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Типограф".
 *
 * Файл: /modules/typograph/typograph.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class typograph
{
	/**
	 * Метод для удаления тегов предыдущего оптического выравнивания
	 *
	 * @param string $str исходная строка
	 * <code>
	 * <?php
	 * 
	 *
	 * $str = '<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">&laquo;Типограф</span>&raquo;&nbsp;&mdash; удобный инструмент для&nbsp;автоматического типографирования в&nbsp;соответствии с&nbsp;правилами, принятыми для&nbsp;экранной типографики. Может применяться как&nbsp;для&nbsp;обычного текста, так&nbsp;и&nbsp;HTML-кода.';
	 *
	 * $newstr = Typograph_Controller::instance()->eraseOpticalAlignment($str);
	 *
	 * // Распечатаем результат
	 * echo $newstr;
	 * ?>
	 * </code>
	 * @return string строка с вырезанными тегами отического выравнивания
	 */
	function EraseOpticalAlignment($str)
	{
		return Typograph_Controller::instance()->eraseOpticalAlignment($str);
	}

	/**
	 * Типографирование текста
	 *
	 * @source_string текст
	 * <code>
	 * <?php
	 * 
	 *
	 * $str = '<span style="margin-right: 0.3em"> </span> <span style="margin-left: -0.3em">&laquo;Типограф</span>&raquo;&nbsp;&mdash; удобный инструмент для&nbsp;автоматического типографирования в&nbsp;соответствии с&nbsp;правилами, принятыми для&nbsp;экранной типографики. Может применяться как&nbsp;для&nbsp;обычного текста, так&nbsp;и&nbsp;HTML-кода.';
	 *
	 * $newstr = Typograph_Controller::instance()->process($str);
	 *
	 * // Распечатаем результат
	 * echo $newstr;
	 * ?>
	 * </code>
	 * @return результат работы ф-ции в виде строки.
	 */
	function ProcessTypographic($str, $use_trailing_punctuation = FALSE)
	{
		return Typograph_Controller::instance()->process($str, $use_trailing_punctuation);
	}
	
	/**
	 * Производит замену массива подстрок $search на массив подстрок $replace в тексте $html, исключая замену в атрибутах HTML-тегов
	 *
	 * @param mixed $search строка или массив строк
	 * @param mixed $replace строка или массив строк
	 * @param str $html исходная строка
	 * @return str
	 */
	/*function replaceWithoutTagAttribute($search, $replace, $html)
	{
		preg_match_all("#([^<^>]*)(<[^<^>]*?>)([^<^>]*)#siu", $html, $matches);

		$str = '';

		// хотя бы один тег
		if (count($matches[3]) > 0)
		{
			foreach ($matches[3] as $key => $val)
			{
				$str .= str_replace($search, $replace, $matches[1][$key]) . $matches[2][$key] . str_replace($search, $replace, $val);
			}
		}
		else
		{
			$str = str_replace($search, $replace, $html);
		}

		return $str;
	}*/
}
