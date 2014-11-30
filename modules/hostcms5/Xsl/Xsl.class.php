<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "XSL-шаблоны".
 *
 * Файл: /modules/Xsl/Xsl.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class xsl
{
	var $mas_xsl_dir = array();

	/**
	* Массив с информацией о пути к каждому разделу XSL
	* - $mas_xsl_group_path[<Идентификатор раздела XSL>][<Идентификатор родительского раздела>][array('xsl_dir_id'=><Идентификатор родительского раздела>, 'xsl_dir_parent_id'=><Идентификатор родительского узла родительского раздела>, 'xsl_dir_name'=>'<Название родительского раздела>', 'xsl_dir_order'=>'<Порядковый номер родительского раздела>')]
	* - $mas_xsl_group_path[20][3]['xsl_dir_id'] = 3
	* - $mas_xsl_group_path[20][3]['xsl_dir_parent_id'] = 1
	* - $mas_xsl_group_path[20][3]['xsl_dir_name'] = 'Подгруппа группы1'
	* - $mas_xsl_group_path[20][3]['xsl_dir_order'] = 5
	*
	* - $mas_xsl_group_path[20][1]['xsl_dir_id'] = 1
	* - $mas_xsl_group_path[20][1]['xsl_dir_parent_id'] = 0
	* - $mas_xsl_group_path[20][1]['xsl_dir_name'] = 'Группа1'
	* - $mas_xsl_group_path[20][1]['xsl_dir_order'] = 10
	*
	* - $mas_xsl_group_path[20][0]['xsl_dir_id'] = 0
	* - $mas_xsl_group_path[20][0]['xsl_dir_parent_id'] = ''
	* - $mas_xsl_group_path[20][0]['xsl_dir_name'] = 'Корневая группа'
	* - $mas_xsl_group_path[20][0]['xsl_dir_order'] = 0
	* @var array
	*/
	var $mas_xsl_group_path = array();

	var $section_path = '';

	/**
	 * Кэш для метода GetXsl()
	 *
	 * @var array
	 */
	var $CacheGetXsl = array();

	/**
	* Получение пути к файлу xsl
	*
	* @param int $xsl_id Идентификатор XSL-шаблона
	* @return string путь к файлу
	*/
	function GetXslPath($xsl_id)
	{
		return Core_Entity::factory('Xsl', $xsl_id)->getXslFilePath();
	}

	/**
	* Получение информаци об XSL-шаблоне по его имени
	*
	* @param string $xslName - имя XSL-шаблона
	* @param array $param ассоциативный массив параметров
	* - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xslName = 'МагазинТовар';
	*
	* $newxsl = $xsl->GetXsl($xslName);
	*
	* // Распечатаем результат
	* print_r ($newxsl);
	* ?>
	* </code>
	* @return mixed ассоциативный массив с информацией об XSL-шаблоне в случае успешного выполнения, false в противном случае
	*/
	function GetXsl($xslName, $param = array())
	{
		if (isset($this->CacheGetXsl[$xslName]) && !isset($param['cache_off']))
		{
			return $this->CacheGetXsl[$xslName];
		}

		/* Если добавлено кэширование*/
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'XSL_BY_NAME';

			if (($in_cache = $cache->GetCacheContent($xslName, $cache_name)) && $in_cache)
			{
				return $in_cache['value'];
			}
		}

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'xsl_id'),
				array('xsl_dir_id', 'xsl_dir_id'),
				array('name', 'xsl_name'),
				array('comment', 'xsl_comment'),
				array('sorting', 'xsl_order'),
				array('format', 'xsl_format'),
				array('user_id', 'users_id')
			)->from('xsls')
			->where('name', '=', $xslName)
			->where('deleted', '=', 0);

		$result = $queryBuilder->execute()->asAssoc()->current();

		/* Если добавлено кэширование*/
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($xslName, $result, $cache_name);
		}

		if (!isset($param['cache_off']))
		{
			$this->CacheGetXsl[$xslName] = $result;
		}

		return $result;
	}

	/**
	* Обработка XML данных с помощью XSL шаблона
	*
	* @param string $xmlData - XML данные
	* @param string $xslName  - название XSL шаблона для обработки XML данных
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xmlData = '<?xml version="1.0" encoding="utf-8"?>
	* <source>
	* <title>XSL</title>
	* <author>John Smith</author>
	* </source>';
	*
	* $xslName = 'ВыводЕдиницыИнформационнойСистемы';
	*
	* $result = $xsl->build($xmlData, $xslName);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	* @return string результат обработки XML данных XSL шаблоном
	*/
	function build($xmlData, $xslName)
	{
		//$xmlData = Core_Type_Conversion::toStr($xmlData);
		$xslName = Core_Type_Conversion::toStr($xslName);

		$oXsl = Core_Entity::factory('Xsl')->getByName($xslName);

		if (is_null($oXsl))
		{
			throw new Core_Exception("XSL '%xslName' does not exist.", array('%xslName' => $xslName));
		}

		$xsl_result = Xsl_Processor::instance()
			->xml($xmlData)
			->xsl($oXsl)
			->process();

		return trim($xsl_result);
	}

	/**
	* Вставка/обновление XSL. Устаревший метод
	*
	* @param int $type параметр, определяющий производится вставка или обновление XSL
	* @param int $xsl_id идентификатор обновляемого XSL шаблона (при вставке нового XSL шаблона равен 0)
	* @param int $xsl_dir_id идентификатор раздела, к которому относится данный XSL шаблон
	* @param string $xslName имя XSL шаблона
	* @param string $xsl_value текст XSL шаблона
	* @param string $xsl_comment комментарий к XSL шаблону
	* @param int $users_id идентификатор пользователя, если false - берется текущий пользователь.
	* @return boolean true при успешном добавлении/обновлении XSL шаблона, false – в противном случае
	*/
	function insert_xsl($type, $xsl_id, $xsl_dir_id, $xslName, $xsl_value, $xsl_comment,
	$users_id = false)
	{
		$param = array();

		$param['type'] = $type;
		$param['xsl_id'] =  $xsl_id;
		$param['xsl_dir_id'] = $xsl_dir_id;
		$param['xsl_name'] = $xslName;
		$param['xsl_value'] = $xsl_value;
		$param['xsl_comment'] = $xsl_comment;
		$param['users_id'] = $users_id;

		return  $this->InsertXsl($param);
	}

	/**
	* Вставка/обновление информации об XSL-шаблоне.
	*
	* @param array param массив параметров
	* param['xsl_id'] идентификатор обновляемого XSL шаблона (при вставке нового XSL шаблона не указывается или равен 0)
	* param['xsl_dir_id'] идентификатор раздела, к которому относится данный XSL шаблон
	* param['xsl_name'] имя XSL-шаблона
	* param['xsl_value'] текст XSL-шаблона
	* param['xsl_comment'] комментарий к XSL-шаблону
	* param['xsl_order'] порядковый номер XSL-шаблона в разделе шаблонов
	* param['xsl_format'] параметр, определяющий будет ли проводится форматирование XSL-шаблона
	* param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $param['xsl_dir_id'] = 0;
	* $param['xsl_name'] = 'Новый XSL';
	* $param['xsl_value'] = 'Текст XSL-шаблона';
	* $param['xsl_comment'] = 'Комментарий к XSL-шаблону';
	*
	* $newxsl = $xsl->InsertXsl($param);
	*
	* // Распечатаем результат
	* echo $newxsl;
	* ?>
	* </code>
	* @return mixed идентификатор XSL-шаблона при успешном добавлении/обновлении XSL шаблона, false – в противном случае
	*/
	function InsertXsl($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['xsl_id']) || $param['xsl_id'] == 0)
		{
			$param['xsl_id'] = NULL;
		}

		// Получаем информацию о XSL-шаблоне по его названию
		$row = $this->GetXsl(Core_Type_Conversion::toStr($param['xsl_name']), array('cache_off' => true));

		// XSL с таким названием есть, но его ID не совпадает с ID редактируемого XSL-шаблона
		if ($row && $param['xsl_id'] != $row['xsl_id'])
		{
			return FALSE;
		}

		$xsl = Core_Entity::factory('Xsl', $param['xsl_id']);

		$xsl->xsl_dir_id = $param['xsl_dir_id'];
		$xsl->name = $param['xsl_name'];
		$xsl->comment = $param['xsl_comment'];
		$xsl->sorting = $param['xsl_order'];
		$xsl->format = $param['xsl_format'];

		if (is_null($param['xsl_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$xsl->user_id = $param['users_id'];
		}

		$xsl->save();

		if (isset($param['xsl_value']))
		{
			$xsl->saveXslFile(Core_Type_Conversion::toStr($param['xsl_value']));
		}

		return $xsl->id;
	}

	/**
	* Вставка и обновление XSL-рубрик. Устаревший метод
	*
	* @param int $type - параметр, определяющий производится вставка или обновление информации
	* @param int $xsl_dir_id - идентификатор обновляемой рубрики (при вставке равен 0)
	* @param int $xsl_dir_parent_id - идентификатор родительской рубрики
	* @param string $xsl_dir_name - название рубрики
	* @param int $users_id идентификатор пользователя, если false - берется текущий пользователь.
	* @return mixed идентификатор вставленной/обновленной рубрики в случае успешного выполнения, false в противном случае
	*/
	function insert_xsl_dir($type, $xsl_dir_id, $xsl_dir_parent_id, $xsl_dir_name,
	$users_id = false)
	{
		$param['type'] = (int)$type;
		$param['xsl_dir_id'] = (int)$xsl_dir_id;
		$param['xsl_dir_parent_id'] = (int)$xsl_dir_parent_id;
		$param['xsl_dir_name'] = quote_smart($xsl_dir_name);
		$param['users_id'] = (int)$users_id;

		return $this->InsertXslDir($param);
	}

	/**
	* Вставка и обновление XSL-рубрики
	*
	* @param array $param массив параметров
	* - $param['type'] параметр, определяющий производится вставка или обновление информации
	* - $param['xsl_dir_id'] идентификатор обновляемой рубрики (при вставке равен 0)
	* - $param['xsl_dir_parent_id'] идентификатор родительской рубрики
	* - $param['xsl_dir_name'] название рубрики
	* - $param['xsl_dir_order'] порядковый номер рубрики
	* - $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $param['xsl_dir_parent_id'] = '';
	* $param['xsl_dir_name'] = 'Новая XSL-рубрика';
	* $param['xsl_dir_order'] = 10;
	*
	* $newxsl = $xsl->InsertXslDir($param);
	*
	* // Распечатаем результат
	* echo $newxsl;
	* ?>
	* </code>
	* @return mixed идентификатор XSL-рубрики в случае успешного выполнения метода, false - в противном случае
	*/
	function InsertXslDir($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['xsl_dir_id']) || $param['xsl_dir_id'] == 0)
		{
			$param['xsl_dir_id'] = NULL;
		}

		$xsl_dir = Core_Entity::factory('Xsl_Dir', $param['xsl_dir_id']);

		$xsl_dir->parent_id = $param['xsl_dir_parent_id'];
		$xsl_dir->name = $param['xsl_dir_name'];
		$xsl_dir->sorting = $param['xsl_dir_order'];

		if (is_null($param['xsl_dir_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$xsl_dir->user_id = $param['users_id'];
		}

		$xsl_dir->save();

		return $xsl_dir->id;
	}

	/**
	* Получение данных о XSL-шаблонах
	*
	* @param int $xsl_id - идентификатор XSL-шаблона, если необходимо получить информацию о конкретном XSL-шаблоне, -1 - получить информацию о всех XSL-шаблонах
	* @param int $xsl_dir_id - идентификатор директории XSL шаблона, если false, то директория не учитывается
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xsl_id = 3;
	*
	* $resource = $xsl->select_xsl($xsl_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return mixed result в случае успешного выполнения метода, false в противном случае
	*/
	function select_xsl($xsl_id, $xsl_dir_id = FALSE)
	{
		$xsl_id = intval($xsl_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'xsl_id'),
				'xsl_dir_id',
				array('name', 'xsl_name'),
				array('comment', 'xsl_comment'),
				array('sorting', 'xsl_order'),
				array('format', 'xsl_format'),
				array('user_id', 'users_id')
			)
			->from('xsls')
			->where('deleted', '=', 0);

		if ($xsl_id != -1)
		{
			$queryBuilder->where('id', '=', $xsl_id);
		}

		if ($xsl_dir_id)
		{
			$queryBuilder->where('xsl_dir_id', '=', $xsl_dir_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение информации о XSL-разделах
	*
	* @param int $xsl_dir_id - идентификатор XSL-раздела, если необходимо получить информацию о конкретном разделе, -1 - получить информацию о всех XSL-разделах
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xsl_dir_id = 2;
	*
	* $resource = $xsl->select_xsl_dir($xsl_dir_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return mixed resorce в случае успешного выполнения метода, false в противном случае
	*/
	function select_xsl_dir($xsl_dir_id)
	{
		$xsl_dir_id = intval($xsl_dir_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'xsl_dir_id'),
				array('parent_id', 'xsl_dir_parent_id'),
				array('name', 'xsl_dir_name'),
				array('sorting', 'xsl_dir_order'),
				array('user_id', 'users_id')
			)
			->from('xsl_dirs')
			->where('deleted', '=', 0);

		if ($xsl_dir_id != -1)
		{
			$queryBuilder->where('id', '=', $xsl_dir_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Удаление XSL-шаблона
	*
	* @param int $xsl_id - идентификатор XSL-шаблона
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xsl_id = 183;
	*
	* $result = $xsl->del_xsl($xsl_id);
	*
	* if ($result)
	* {
	*	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	*	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	* @return boolean true в случае успешного выполнения метода, false в противном случае
	*/
	function del_xsl($xsl_id)
	{
		$xsl_id = intval($xsl_id);

		Core_Entity::factory('Xsl', $xsl_id)->markDeleted();

		return TRUE;
	}

	/**
	* Удаление XSL-раздела
	*
	* @param int $xsl_dir_id - идентификатор XSL-заздела
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xsl_dir_id = 81;
	*
	* $result = $xsl->del_xsl_dir($xsl_dir_id);
	*
	* if ($result)
	* {
	*	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	*	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	* @return boolean true в случае успешного выполнения метода, false в противном случае
	*/
	function del_xsl_dir($xsl_dir_id)
	{
		$xsl_dir_id = intval($xsl_dir_id);

		Core_Entity::factory('Xsl_Dir', $xsl_dir_id)->markDeleted();

		return TRUE;
	}

	/**
	* Формирование дерева разделов
	*
	* @param int $xsl_dir_parent_id - идентификатор родительского раздела
	* @param string $separator - символ (строка)-разделитель
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xsl_dir_parent_id = 0;
	* $separator = '';
	*
	* $row = $xsl->fill_xsl_dir($xsl_dir_parent_id, $separator);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	* @return array массив с данными о дереве XSL-разделов
	*/
	function fill_xsl_dir($xsl_dir_parent_id, $separator)
	{
		$xsl_dir_parent_id = intval($xsl_dir_parent_id);

		$xsl_dir = Core_Entity::factory('Xsl_Dir', $xsl_dir_parent_id);

		$return_mas = array();

		// Дочерние разделы
		$children_dirs = $xsl_dir->xsl_dirs->findAll();

		if (count($children_dirs))
		{
			foreach ($children_dirs as $children_dir)
			{
				$return_mas[] = array($children_dir->id, $separator . $children_dir->name);
				$return_mas += $this->fill_xsl_dir($children_dir->id, $separator . $separator);
			}
		}

		return $return_mas;
	}

	/**
	* Получение пути по дереву разделов XSL. Устаревший метод
	*
	* @param int $xsl_dir_id
	*/
	function get_section_path($xsl_dir_id)
	{
		$xsl_dir_id = intval($xsl_dir_id);

		$result = $this->select_xsl_dir($xsl_dir_id);

		if (mysql_num_rows($result) == 0)
		{
			$this->section_path='<a href=xsl.php>' . Core::_('xsl.XSL_dir_title') . '</a> // '.$this->section_path;
		}
		else
		{
			$row = mysql_fetch_assoc($result);
			$this->section_path='<a href=xsl.php?xsl_dir_parent_id=' . $row['xsl_dir_id'] . '>' . htmlspecialchars($row['xsl_dir_name']) . '</a> // ' . $this->section_path;
			$this->get_section_path($row['xsl_dir_parent_id']);
		}
	}

	/**
	* Получение массива с информацией о разделах, составляющих путь от текущего до корневого раздела
	*
	* @param int $xsl_dir_id идентификатор раздела XSL
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xsl_dir_parent_id = 0;
	* $xsl_dir_id = 3;
	*
	* $row = $xsl->GetMasXslGroupPath($xsl_dir_parent_id, $xsl_dir_id);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	* @return array массив с информацией о разделах XSL, составляющих путь от данного раздела до корневого
	*/
	function GetMasXslGroupPath($xsl_dir_parent_id, $xsl_dir_id)
	{
		$xsl_dir_id = intval($xsl_dir_id);
		$xsl_dir_parent_id = intval($xsl_dir_parent_id);

		// Для раздела задан путь
		if (isset($this->mas_xsl_group_path[$xsl_dir_id]))
		{
			return $this->mas_xsl_group_path[$xsl_dir_id];
		}
		else // Путь для раздела не задан
		{
			// Родительский раздел - корневой
			if (!$xsl_dir_id)
			{
				$this->mas_xsl_group_path[$xsl_dir_parent_id][0] = array('xsl_dir_id' => 0,
				'xsl_dir_parent_id' => '',
				'xsl_dir_name' => Core::_('xsl.XSL_root_dir'),
				'xsl_dir_order' => 0);

				return $this->mas_xsl_group_path[$xsl_dir_id];
			}
			// Родительский раздел - некорневой
			else
			{
				$result = $this->select_xsl_dir($xsl_dir_id);

				$row = mysql_fetch_assoc($result);

				// Родительский раздел - корневой
				if ($row['xsl_dir_parent_id'] == 0)
				{
					$this->mas_xsl_group_path[$xsl_dir_parent_id][$row['xsl_dir_id']] = $row;

					$this->mas_xsl_group_path[$xsl_dir_parent_id][0] = array('xsl_dir_id'=>0, 'xsl_dir_parent_id'=>'',
					'xsl_dir_name' => Core::_('xsl.XSL_root_dir'),
					'xsl_dir_order' => 0);

					return $this->mas_xsl_group_path[$xsl_dir_parent_id];
				}
				else // Родительский раздел - некорневой
				{
					$this->mas_xsl_group_path[$xsl_dir_parent_id][$row['xsl_dir_id']] = $row;
					$this->GetMasXslGroupPath($xsl_dir_parent_id, $row['xsl_dir_parent_id']);
				}
			}
		}
	}

	/**
	* Копирование XSL-шаблона
	*
	* @param int $xsl_id
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xsl_id = 184;
	*
	* $newid = $xsl->CopyXsl($xsl_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return mixed идентификатор копии XSL-шаблона в случае успешного выполнения, false - в противном случае
	*/
	function CopyXsl($xsl_id)
	{
		$xsl_id = intval($xsl_id);

		$clone_xsl = Core_Entity::factory('Xsl', $xsl_id)->copy();

		return $clone_xsl->id;
	}

	/**
	* Форматирование XML-документа
	*
	* @param string $content текст форматируемого XML-документа
	* <code>
	* <?php
	* $xsl = new xsl();
	*
	* $xml = '<?xml version="1.0" encoding="utf-8"?>
	* <source>
	* <title>XSL</title>
	* <author>John Smith</author>
	* </source>';
	*
	* $result = $xsl->FormatXml($xml);
	*
	* // Распечатаем результат
	* echo "<pre>".htmlspecialchars($result)."<pre>";
	* ?>
	* </code>
	* @return string отформатированный текст
	*/
	function FormatXml($content)
	{
		return Xsl_Processor::instance()->formatXml($content);
	}
}
