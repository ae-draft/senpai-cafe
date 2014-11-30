<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для генерации RSS.
 * 
 * Файл: /modules/Kernel/RSS.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
/**
 * @access private
 */
class rss extends RssWrite
{

}

/**
 * Ядро, класс для генерации RSS.
 * 
 * Со спецификацией можно ознакомиться на сайте http://www.rssboard.org/rss-specification
 */
class RssWrite
{
	/**
	 * Enter description here...
	 *
	 * @var resource
	 * @access private
	 */
	var $result; // результат выполнения SQL запроса

	/**
	 * Преобразует строку для публикации в RSS, использует str_for_xml() и Core_Str::str2ncr()
	 *
	 * @param string $str строка
	 * @return str обработанная строка
	 */
	function str_for_rss($str)
	{
		$str = str_for_xml($str);
		// сущности в числовые значения
		$str = Core_Str::str2ncr($str);
		return $str;
	}
	
	/**
	 * Метод формирования XML-файла в стандарте RSS
	 *
	 * @param array $rss_mas_header массив, содержащий данные заголовка канала
	 * - $rss_mas_items - массив, содержащий данные новостных заголовков
	 * - $rss_mas_header['title'] - Заголовок канала
	 * - $rss_mas_header['link'] - Ссылка на сайт
	 * - $rss_mas_header['description'] - Краткое описание RSS-канала
	 * - $rss_mas_header['image'] - Картинка для представления канала (необязательный элемент)
	 * - $rss_mas_header['image']['url'] - Ссылка на файл изображения
	 * - $rss_mas_header['image']['title'] - Заменяющий текст для изображения
	 * - $rss_mas_header['image']['link'] - Ссылка для перехода при щелчке по изображению
	 * @param array $rss_mas_items массив, соддержащий данные о элементах канала
	 * - $rss_mas_items[]['title'] - Название новости
	 * - $rss_mas_items[]['link'] - Ссылка на страничку, содержащую соответствующую новость
	 * - $rss_mas_items[]['description'] - Краткое описание новости
	 * - $rss_mas_items[]['pubDate'] - Дата публикации новости, ПРИНИМАЕТСЯ В ФОРМАТЕ SQL
	 * - $rss_mas_items[]['author'] - Автор
	 * - $rss_mas_items[]['category'] - Наименование категории, к которой относится элемент
	 * - $rss_mas_items[]['guid'] - Уникальный илентификатор элемента, наиболее часто знаение эквивалентно $rss_mas_items[]['link'] 
	 * - $rss_mas_items[]['enclosure'][0]['url'] - URL вложения
	 * - $rss_mas_items[]['enclosure'][0]['type'] - type вложения, если явно не указан - определяется автоматически
	 * - $rss_mas_items[]['enclosure'][0]['length'] - размер вложения в байтах, целое число
	 * @param array $property массив дополнительных параметров
	 * - $property['yandex:full-text'] вывод полного текста для Яндекс, по умолчанию false
	 * @return string сгенерированный XML в формате RSS 2.0
	 */
	function CreateRSS($rss_mas_header, $rss_mas_items, $property = array())
	{
		header("Content-Type: text/xml; charset=UTF-8");

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

		if (Core_Type_Conversion::toBool($property['yandex:full-text']))
		{
			$xmlns = ' xmlns:yandex="http://news.yandex.ru"';
		}
		else
		{
			$xmlns = '';
		}

		$xmlData .= '<rss version="2.0"'.$xmlns.'>'."\n";
		$xmlData .= "<channel>\n";
		if (isset($rss_mas_header['title']))
		{
			$xmlData .= '<title>'.$this->str_for_rss($rss_mas_header['title']).'</title>'."\n";
		}

		if (isset($rss_mas_header['link']))
		{
			$xmlData .= '<link>'.$rss_mas_header['link'].'</link>'."\n";
		}

		if (isset($rss_mas_header['description']))
		{
		$xmlData .= '<description>'.$this->str_for_rss($rss_mas_header['description']).'</description>'."\n";
		}

		if (isset($rss_mas_header['image']))
		{
			$xmlData .= '<image>'."\n";
			if (isset($rss_mas_header['image']['url']))
			$xmlData .= '<url>'.$this->str_for_rss($rss_mas_header['image']['url']).'</url>'."\n";

			if (isset($rss_mas_header['image']['title']))
			$xmlData .= '<title>'.$this->str_for_rss($rss_mas_header['image']['title']).'</title>'."\n";

			if (isset($rss_mas_header['image']['link']))
			$xmlData .= '<link>'.$this->str_for_rss($rss_mas_header['image']['link']).'</link>'."\n";

			$xmlData .= '</image>'."\n";
		}

		if (isset($rss_mas_header['language']))
		$xmlData .= '<language>'.$rss_mas_header['language'].'</language>'."\n";

		if (isset($rss_mas_header['copyright']))
		$xmlData .= '<copyright>'.$this->str_for_rss($rss_mas_header['copyright']).'</copyright>'."\n";

		if (isset($rss_mas_header['managinEditor']))
		$xmlData .= '<managinEditor>'.$rss_mas_header['managinEditor'].'</managinEditor>'."\n";

		if (isset($rss_mas_header['webMaster']))
		$xmlData .= '<webMaster>'.$rss_mas_header['webMaster'].'</webMaster>'."\n";

		if (isset($rss_mas_header['pubDate']))
		{
			$DateClass = new DateClass();
			$xmlData .= '<pubDate>'.date("r", $DateClass->DateSqlToUnix($rss_mas_header['pubDate'])).'</pubDate>'."\n";
		}
		else
		{
			$xmlData .= '<pubDate>'.date("r").'</pubDate>'."\n";
		}

		if (isset($rss_mas_header['lastBuildDate']))
		{
			$xmlData .= '<lastBuildDate>'.$rss_mas_header['lastBuildDate'].'</lastBuildDate>'."\n";
		}

		if (isset($rss_mas_header['category']))
		{
			$xmlData .= '<category>'.$this->str_for_rss($rss_mas_header['category']).'</category>'."\n";
		}

		if (isset($rss_mas_header['generator']))
		{
			$xmlData .= '<generator>'.$this->str_for_rss($rss_mas_header['generator']).'</generator>'."\n";
		}
		else
		{
			$xmlData .= "<generator>HostCMS</generator>\n";
		}

		if (isset($rss_mas_header['docs']))
		{
			$xmlData .= '<docs>'.$this->str_for_rss($rss_mas_header['docs']).'</docs>'."\n";
		}

		if (isset($rss_mas_header['cloud']))
		{
			$xmlData .= '<cloud>'.$this->str_for_rss($rss_mas_header['cloud']).'</cloud>'."\n";
		}

		if (isset($rss_mas_header['ttl']))
		{
			$xmlData .= '<ttl>'.$this->str_for_rss($rss_mas_header['ttl']).'</ttl>'."\n";
		}

		if (isset($rss_mas_header['rating']))
		{
			$xmlData .= '<rating>'.$this->str_for_rss($rss_mas_header['rating']).'</rating>'."\n";
		}

		if (isset($rss_mas_header['textInput']))
		{
			$xmlData .= '<textInput>'."\n";

			if (isset($rss_mas_header['textInput']['title']))
			{
				$xmlData .= '<title>'.$this->str_for_rss($rss_mas_header['textInput']['title']).'</title>'."\n";
			}

			if (isset($rss_mas_header['textInput']['description']))
			{
				$xmlData .= '<description>'.$this->str_for_rss($rss_mas_header['textInput']['description']).'</description>'."\n";
			}

			if (isset($rss_mas_header['textInput']['name']))
			{
				$xmlData .= '<name>'.$this->str_for_rss($rss_mas_header['textInput']['name']).'</name>'."\n";
			}

			if (isset($rss_mas_header['textInput']['link']))
			{
				$xmlData .= '<link>'.$this->str_for_rss($rss_mas_header['textInput']['link']).'</link>'."\n";
			}

			$xmlData .= '</textInput>'."\n";
		}
		
		if (isset($rss_mas_header['skipHours']))
		{
			$xmlData .= '<skipHours>'.(int)$rss_mas_header['skipHours'].'</skipHours>'."\n";
		}

		if (isset($rss_mas_header['skipDays']))
		{
			$xmlData .= '<skipDays>'.(int)$rss_mas_header['skipDays'].'</skipDays>'."\n";
		}

		// определяем число новостных заголовков
		$count=count($rss_mas_items);

		for($i = 0; $i < $count; $i++)
		{
			$xmlData .= '<item>'."\n";

			if (isset($rss_mas_items[$i]['title']))
			{
				$xmlData .= '<title>'.$this->str_for_rss($rss_mas_items[$i]['title']).'</title>'."\n";
			}

			if (isset($rss_mas_items[$i]['link']))
			{
				$xmlData .= '<link>'.$this->str_for_rss($rss_mas_items[$i]['link']).'</link>'."\n";
			}

			if (isset($rss_mas_items[$i]['description']))
			{
				$xmlData .= '<description>'.$this->str_for_rss($rss_mas_items[$i]['description']).'</description>'."\n";
			}

			if (isset($rss_mas_items[$i]['full-text']))
			{
				$xmlData .= '<yandex:full-text>'.$this->str_for_rss($rss_mas_items[$i]['full-text']).'</yandex:full-text>'."\n";
			}

			if (isset($rss_mas_items[$i]['pubDate']))
			{
				$DateClass = new DateClass();
				$xmlData .= '<pubDate>'.date("r",$DateClass->DateSqlToUnix($rss_mas_items[$i]['pubDate'])).'</pubDate>'."\n";
			}

			if (isset($rss_mas_items[$i]['author']))
			{
				$xmlData .= '<author>'.$this->str_for_rss($rss_mas_items[$i]['author']).'</author>'."\n";
			}

			if (isset($rss_mas_items[$i]['category']))
			{
				$xmlData .= '<category>'.$this->str_for_rss($rss_mas_items[$i]['category']).'</category>'."\n";
			}

			if (isset($rss_mas_items[$i]['comments']))
			{
				$xmlData .= '<comments>'.$this->str_for_rss($rss_mas_items[$i]['comments']).'</comments>'."\n";
			}

			if (isset($rss_mas_items[$i]['enclosure']))
			{
				$kernel = & singleton('kernel');
				
				foreach ($rss_mas_items[$i]['enclosure'] as $key => $a_enclosure)
				{
					if (!empty($a_enclosure['url']))
					{
						/* Если тип вложения не передан - определяем его автоматически */ 
						if (!isset($a_enclosure['type']))
						{
							$a_enclosure['type'] = $kernel->GetMimeType($a_enclosure['url']);
						}
						
						if (isset($a_enclosure['length']))
						{
							$length = ' length="'.intval($a_enclosure['length']).'"';
						}
						else 
						{
							$length = '';
						}
						
						$xmlData .= '<enclosure url="'.$this->str_for_rss($a_enclosure['url']).'"'.$length.' type="'.$this->str_for_rss($a_enclosure['type']).'" />'."\n";
					}
				}
			}

			if (isset($rss_mas_items[$i]['guid']))
			{
				$xmlData .= '<guid>'.$this->str_for_rss($rss_mas_items[$i]['guid']).'</guid>'."\n";
			}

			if (isset($rss_mas_items[$i]['source']))
			{
				$xmlData .= '<source>'.$this->str_for_rss($rss_mas_items[$i]['source']).'</source>'."\n";
			}

			$xmlData .= '</item>'."\n";
		}
		$xmlData .= '</channel>'."\n";
		$xmlData .= '</rss>'."";

		return $xmlData;
	}
}
