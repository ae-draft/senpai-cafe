<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для чтения RSS.
 * 
 * Файл: /modules/Kernel/RssRead.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class RssRead
{

	/**
	 * Внутренняя кодировка, используемая при работе с RSS
	 *
	 * @var string
	 */
	var $encoding = '';

	/**
	 * Пустой массив с полями канала
	 *
	 * @var array
	 * @access private
	 */
	var $default_row = array('title' => '', 'link' => '', 'desc' => '', 'category' => '', 'pubdate' => '', 'yandex:full-text' => '');

	/**
	 * Enter description here...
	 *
	 * @var resource handle
	 * @access private
	 */
	var $xml_parser;

	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $tag;

	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $rss;

	/**
	 * Массив с данными о канале
	 *
	 * @var array
	 * @access private
	 */
	var $chanel;

	/**
	 * Массив элементов
	 * @access private
	 */
	var $items;

	/**
	 * Enter description here...
	 *
	 * @var integer
	 * @access private
	 */
	var $itemCount;

	/**
	 * *
	 *
	 * @param is a reference to the XML parser calling the handler $parser
	 * @param string $name
	 * @param array $attrs
	 * @access private
	 */
	function fetchOpen($parser, $name, $attrs)
	{
		if (sizeof($attrs))
		{
			while (list($k, $v) = each($attrs))
			{
				$this->items[$this->itemCount][mb_strtolower($name)][mb_strtolower($k)] = $v;
				//echo " <font color=\"#009900\">5 $k</font>=\"<font color=\"#990000\">6 $v</font>\"";
			}
		}

		if ($name == 'RSS')
		{
			$this->rss = '^RSS';
		}
		elseif ($name == 'RDF:RDF')
		{
			$this->rss = '^RDF:RDF';
		}

		$this->tag .= '^' . $name;
	}
	/**
	 * *
	 *
	 * @param is a reference to the XML parser calling the handler $parser
	 * @param string $name
	 * @access private
	 */
	function fetchClose($parser, $name)
	{
		if ($name == 'ITEM')
		{
			$this->itemCount++;
			if (!isset($this->items[$this->itemCount]))
			{
				$this->items[$this->itemCount] = $this->default_row;
			}
		}

		$this->tag = mb_substr($this->tag, 0, mb_strrpos($this->tag, '^'));
	}
	/**
	 *
	 * @param is a reference to the XML parser calling the handler $parser
	 * @param string $data
	 * @access private
	 */
	function characterData($parser, $data)
	{
		$this->rssChannel = '';

		if ($data)
		{
			if (mb_strtolower($this->encoding) != 'utf-8'
			//&& mb_strtolower($this->encoding) != 'iso-8859-1'
			&& function_exists('iconv'))
			{
				$data = @iconv($this->encoding, "UTF-8//IGNORE//TRANSLIT", $data);
			}

			if ($this->tag == $this->rss . '^CHANNEL^TITLE') {
				$this->chanel['title'] .= $data;
			} elseif ($this->tag == $this->rss . '^CHANNEL^LINK') {
				$this->chanel['link'] .= $data;
			} elseif ($this->tag == $this->rss . '^CHANNEL^DESCRIPTION') {
				$this->chanel['description'] .= $data;
			}

			if ($this->rss == '^RSS') $this->rssChannel = '^CHANNEL';

			if ($this->tag == $this->rss . $this->rssChannel . '^ITEM^TITLE') {
				$this->items[$this->itemCount]['title'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^LINK') {
				$this->items[$this->itemCount]['link'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^YANDEX:FULL-TEXT') {
				$this->items[$this->itemCount]['yandex:full-text'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^FULL-TEXT') {
				$this->items[$this->itemCount]['yandex:full-text'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^FULLTEXT') {
				$this->items[$this->itemCount]['yandex:full-text'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^TEXT') {
				$this->items[$this->itemCount]['yandex:full-text'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^CONTENT:ENCODED') {
				$this->items[$this->itemCount]['yandex:full-text'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^DESCRIPTION') {
				$this->items[$this->itemCount]['desc'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^CATEGORY') {
				$this->items[$this->itemCount]['category'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^DC:SUBJECT') {
				$this->items[$this->itemCount]['category'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^ITEM^PUBDATE') {
				$this->items[$this->itemCount]['pubdate'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^IMAGE^TITLE') {
				$this->chanel['image']['title'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^IMAGE^LINK') {
				$this->chanel['image']['link'] .= $data;
			} elseif ($this->tag == $this->rss . $this->rssChannel . '^IMAGE^URL') {
				$this->chanel['image']['url'] .= $data;
			}
		}
	}

	/**
	 * Метод чтения данных из RSS потока
	 *
	 * @param string $url URL ресурса
	 * @param array $param массив дополнительных параметров, необязательный параметр
	 * @return array ассоциативнй массив с инфомрацией о канале и элементах
	 * <br />[chanel] => Array
	 * - (
	 * <br />[title] =>
	 * <br />[link] =>
	 * <br />[description] =>
	 * <br />[image] => Array
	 * <br />(
	 * <br />[title] =>
	 * <br />[link] =>
	 * <br />[url] =>
	 * <br />)
	 * <br />)
	 * <br />[items] => Array
	 * <br />(
	 * <br />[] => Array
	 * <br />(
	 * <br />[title] =>
	 * <br />[link] =>
	 * <br />[desc] =>
	 * <br />[category] =>
	 * <br />[pubdate] =>
	 * <br />[yandex:full-text] =>
	 * <br />)
	 * <br />)
	 * <br />[error] => Текст ошибки, если была
	 */
	function ReadRSS($url, $param=array())
	{
		$this->chanel['title'] = '';
		$this->chanel['link'] = '';
		$this->chanel['description'] = '';
		$this->chanel['image']['title'] = '';
		$this->chanel['image']['link'] = '';
		$this->chanel['image']['url'] = '';

		$this->tag = '';
		$this->rss = '';

		// Если переданный параметр не массив - делаем его пустым массивом
		if (!is_array($param))
		{
			$param = array();
		}

		if (!isset($param['encoding']))
		{
			$param['encoding'] = null;
		}

		$data = '';

		$url = trim($url);

		if (mb_strpos($url, 'http://') === 0)
		{
			$kernel = & singleton('kernel');
			$data = $kernel->GetUrl($url);
		}
		else
		{

			if ($fp = @fopen($url, "r"))
			{
				while (true)
				{
					$read = @fread($fp, 16384);

					if (mb_strlen($read) == 0)
					{
						break;
					}

					$data .= $read;
				}

				@fclose($fp);
			}
		}

		$this->itemCount = 0;
		//$this->items = array (0 => $this->default_row);
		$this->items = array(0 => array('title' => '', 'link' => '', 'desc' => '', 'category' => '', 'pubdate' => '', 'yandex:full-text' => ''));

		$this->xml_parser = xml_parser_create($param['encoding']);

		// add 26-06-08
		$this->encoding = xml_parser_get_option($this->xml_parser, XML_OPTION_TARGET_ENCODING);

		//This is the RIGHT WAY to set everything inside the object.
		xml_set_object($this->xml_parser, $this);

		xml_set_element_handler($this->xml_parser,'fetchOpen', 'fetchClose');
		xml_set_character_data_handler($this->xml_parser, 'characterData');

		/*
		$this->xml_parser = xml_parser_create();
		xml_set_element_handler($this->xml_parser, Array(&$this, 'fetchOpen'),  Array(&$this, 'fetchClose'));
		xml_set_character_data_handler($this->xml_parser, Array(&$this, 'characterData'));
		*/

		if (!empty($data))
		{
			$xmlresult = xml_parse($this->xml_parser, $data);
			$xmlerror = xml_error_string(xml_get_error_code($this->xml_parser));
			$xmlcrtline = xml_get_current_line_number($this->xml_parser);

			if (!$xmlresult)
			{
				$error = "Ошибка разбора элемента ! Ошибка: <b>$xmlerror</b> в строке: <b>$xmlcrtline</b>";
			}
		}
		else
		{
			$error = "Пустые данные";
		}

		xml_parser_free($this->xml_parser);

		$result = array();
		$result['chanel'] = $this->chanel;
		$result['items'] = $this->items;

		if (isset($error))
		{
			$result['error'] = $error;
		}

		return $result;
	}
}
?>