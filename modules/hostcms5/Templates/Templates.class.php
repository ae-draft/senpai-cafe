<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Шаблоны и макеты".
 *
 * Файл: /modules/Templates/Templates.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class templates
{
	var $mas_data_templates_dir = array();
	var $path_array = array();

	/**
	 * @var array $array_template_ids массив соответствий старых и новых идентификаторов скопированных шаблонов
	 */
	var $array_template_ids = array();

	/**
	* Получение пути к файлу шаблона
	*
	* @param int $data_templates_id Идентификатор шаблона

	* @return string путь к файлу
	*/
	function GetDataTemplatePath($data_templates_id)
	{
		$data_templates_id = intval($data_templates_id);
		return CMS_FOLDER . "hostcmsfiles/data_templates/{$data_templates_id}.htm";
	}

	/**
	* Методы добавления (редактирования) шаблонов страниц
	*
	* @param int $type тип действия 0 - вставка, 1 - обновление
	* @param int $data_templates_id идентификатор шаблона страниц
	* @param string $data_templates_value шаблон
	* @param string $data_templates_name наименование шаблона страниц
	* @param string $data_templates_description описание шаблона страниц
	* @param int $data_templates_order порядок сортировки
	* @param int $users_id идентификатор пользователя, если false - берется текущий пользователь.
	* <code>
	* <?php
	* $template = new templates();
	*
	* $type = 0;
	* $data_templates_group_id = '';
	* $data_templates_value = '';
	* $data_templates_name = 'Шаблон 1';
	* $data_templates_description = '';
	* $data_templates_order = 10;
	*
	* $newid = $template->insert_data_templates($type, $data_templates_id, $data_templates_group_id, $data_templates_value, $data_templates_name, $data_templates_description, $data_templates_order);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return mixed идентификатор добавленного (отредактированного) шаблона в случае успешной операции, ложь - при возникновении ошибки
	*/
	function insert_data_templates($type, $data_templates_id, $data_templates_group_id,
	$data_templates_value, $data_templates_name, $data_templates_description,
	$data_templates_order, $users_id = false, $site_id = false)
	{
		throw new Core_Exception('Method insert_data_templates() does not allow');
	}

	/**
	* Метод добавления/обновления группы шаблонов данных
	*
	* @param array $param массив параметров
	* - $param['data_templates_group_id'] идентификатор группы
	* - $param['data_templates_group_parent_id'] идентификатор родительской группы
	* - $param['data_templates_group_name'] имя группы
	* <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	* <code>
	* <?php
	* $template = new templates();
	*
	* $param['data_templates_group_parent_id'] = '';
	* $param['data_templates_group_name'] = 'Группа шаблонов';
	* $param['site_id'] = CURRENT_SITE;
	*
	* $newid = $template->InsertDataTemplatesGroup($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return mixed идентификатор добавленной группы в случае успешного выполнения, false - при возникновении ошибки
	*/
	function InsertDataTemplatesGroup($param)
	{
		throw new Core_Exception('Method InsertDataTemplatesGroup() does not allow');
	}

	/**
	* Метод обновления информации о группе шаблонов данных
	*
	* @param array $param массив параметров
	* - $param['data_templates_group_id'] идентификатор группы
	* - $param['data_templates_group_parent_id'] идентификатор родительской группы
	* - $param['data_templates_group_name'] имя группы
	* <code>
	* <?php
	* $template = new templates();
	*
	* $param['data_templates_group_id'] = 1;
	* $param['data_templates_group_parent_id'] = 5;
	* $param['data_templates_group_name'] = 'Группа шаблонов2';
	*
	* $newid = $template->UpdateDataTemplatesGroup($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return mixed идентификатор группы в случае успешного выполнения, false - в случае возникновения ошибки
 	*/
	function UpdateDataTemplatesGroup($param)
	{
		throw new Core_Exception('Method UpdateDataTemplatesGroup() does not allow');
	}

	/**
	* Метод выбора информации о шаблоне страниц или обо всех шаблонах (при $data_templates_id = -1)
	*
	* @param int $data_templates_id идентификатор шаблона
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_templates_id = 1;
	*
	* $resource = $template->select_data_templates($data_templates_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	*	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource информация о шаблоне (обо всех щаблонах)
	*/
	function select_data_templates($data_templates_id)
	{
		throw new Core_Exception('Method select_data_templates() does not allow');
	}

	/**
	*  Метод построения дерева шаблонов сайта
	*
	* @param int $parent_data_templates_group_id идентификатор группы, начиная с которой необходимо стороить дерево
	* @param array $param массив параметров
	* - $param['data_templates_group_separator'] строка, используемая в качестве префикса имен групп шаблонов сайта
	* - $param['data_templates_separator'] строка, используемая в качестве префикса имен шаблонов сайта
	* - $param['sort_field_data_templates_groups'] поле сортировки групп шаблонов сайта, по умолчанию сортировка по названию
	* - $param['sort_field_data_templates'] поле сортировки шаблонов сайта, по умолчанию сортировка по названию
	* - $param['order_data_templates_groups'] направление сортировки групп шаблонов сайта, asc - по возрастанию (по умолчанию), desc - по убыванию;
	* - $param['order_data_templates'] напрвление сортировки шаблонов сайта, asc - по возрастанию (по умолчанию), desc - по убыванию;
	* - $param['site_id'] идентификатор сайта, для которого необходимо получитб дерево шаблонов.
	* - по умолчанию используется идентификатор текущего сайта.
	* - Если $param['site_id'] = false, сайт не учитывается.
	* <code>
	* <?php
	* $template = new templates();
	*
	* $parent_data_templates_group_id = 0;
	* $param['data_templates_group_separator'] = '';
	* $param['data_templates_separator'] = '';
	* $param['site_id'] = CURRENT_SITE;
	*
	* $row = $template->GetDataTemplatesTree($parent_data_templates_group_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed массив, содержащий
	*/
	function GetDataTemplatesTree($parent_data_templates_group_id, $param = array())
	{
		throw new Core_Exception('Method GetDataTemplatesTree() does not allow');
	}

	/**
	* Метод построения поддерева узла дерева
	* @param $mas_groups_tree  исходный массив, содержащий информацию о группах
	* @param $mas_item_key идентификатор узла,  для которого необходимо построить поддерево
	* @param $target_mas_groups массив, в котором содержится часть поддерева данного узла
	* @param array $target_mas_groups массив групп, расположенных в соответствии со структурой поддерева
	*/
	function GetChildrenItemMasDataTemplatesGroup($mas_groups_tree, $mas_item_key, $target_mas_groups=array())
	{
		throw new Core_Exception('Method GetChildrenItemMasDataTemplatesGroup() does not allow');
	}

	/**
	* Метод построения пути от заданного узла дерева групп шаблонов страниц до корневого
	*
	* @param int $data_templates_group_id идентификатор узла дерева групп шаблонов страниц
	* @param array $mas_data_templates_groups массив, содержащий дерево групп шаблонов страниц
	* @param string $separator строка, разделяющая названия групп шаблонов страниц
	*/
	function GetPathDataTemplatesGroupCurrentToRoot($data_templates_group_id, $mas_data_templates_groups, $path_data_templates_groups = '', $separator = ' - ')
	{
		throw new Core_Exception('Method GetPathDataTemplatesGroupCurrentToRoot() does not allow');
	}

	/**
	*  Построение дерева макетов сайта
	*
	* @param int $parent_templates_group_id идентификатор группы, начиная с которой необходимо стороить дерево
	* @param array $param массив параметров
	* - $param['templates_group_separator'] строка, используемая в качестве префикса имен групп макетов сайта
	* - $param['templates_separator'] строка, используемая в качестве префикса имен макетов сайта
	* - $param['sort_field_templates_groups'] поле сортировки групп макетов сайта, по умолчанию сортировка по названию
	* - $param['sort_field_templates'] поле сортировки макетов сайта, по умолчанию сортировка по названию
	* - $param['order_templates_groups'] направление сортировки групп макетов сайта, asc - по возрастанию (по умолчанию), desc - по убыванию;
	* - $param['order_templates'] напрвление сортировки макетов сайта, asc - по возрастанию (по умолчанию), desc - по убыванию;
	* - $param['site_id'] идентификатор сайта, для которого необходимо получитб дерево макетов.
	* - по умолчанию используется идентификатор текущего сайта.
	* - Если $param['site_id'] = false, сайт не учитывается.
	* <code>
	* <?php
	* $template = new templates();
	*
	* $parent_data_templates_group_id = 0;
	* $param['templates_group_separator'] = '';
	* $param['templates_separator'] = '';
	* $param['site_id'] = CURRENT_SITE;
	*
	* $row = $template->GetTemplatesTree($parent_templates_group_id, $param = array());
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed массив, содержащий
	*/
	function GetTemplatesTree($parent_templates_group_id, $param = array())
	{
		$kernel = & singleton('kernel');

		$parent_templates_group_id = Core_Type_Conversion::toInt($parent_templates_group_id);

		$param = Core_Type_Conversion::toArray($param);

		if (isset($param['templates_group_separator']))
		{
			$param['templates_group_separator'] = Core_Type_Conversion::toStr($param['templates_group_separator']);
		}
		else
		{
			$param['templates_group_separator'] = ' - ';
		}

		if (isset($param['templates_separator']))
		{
			$param['templates_separator'] = Core_Type_Conversion::toStr($param['templates_separator']);
		}
		else
		{
			$param['templates_separator'] = ' - ';
		}

		if (isset($param['sort_field_templates_groups']))
		{
			// Получаем информацию о полях таблицы групп макетов страниц
			$mas_field_templates_groups = $kernel->GetTableFields('templates_group_table');

			$param['sort_field_templates_groups'] = Core_Type_Conversion::toStr($param['sort_field_templates_groups']);

			// Переданое поле не существует в таблице - задаем поле сортировки по умолчанию (по названию макета)
			if (!isset($mas_field_templates_groups[$param['sort_field_templates_groups']]))
			{
				$param['sort_field_templates_groups'] = 'templates_group_name';
			}
		}
		else
		{
			$param['sort_field_templates_groups'] = 'templates_group_name';
		}

		if (isset($param['sort_field_templates']))
		{
			// Получаем информацию о полях таблицы макетов страниц
			$mas_field_templates = $kernel->GetTableFields('templates_table');

			$param['sort_field_templates'] = Core_Type_Conversion::toStr($param['sort_field_templates']);

			// Переданое поле не существует в таблице - задаем поле сортировки по умолчанию (по названию макета)
			if (!isset($mas_field_templates[$param['sort_field_templates']]))
			{
				$param['sort_field_templates'] = 'templates_name';
			}
		}
		else
		{
			$param['sort_field_templates'] = 'templates_name';
		}

		// Порядок сортировки групп макетов сайта
		if (isset($param['order_templates_groups']))
		{
			// Приводим  поле сортировки групп макетов к нижнему регистру
			$param['order_templates_groups'] = mb_strtolower(Core_Type_Conversion::toStr($param['order_templates_groups']));

			if ($param['order_templates_groups'] != 'asc' && $param['order_templates_groups'] != 'desc')
			{
				$param['order_templates_groups'] = 'ASC';
			}
		}
		else
		{
			$param['order_templates_groups'] = 'ASC';
		}

		// Порядок сортировки макетов сайта
		if (isset($param['order_templates']))
		{
			// Приводим  поле сортировки макетов к нижнему регистру
			$param['order_templates'] = mb_strtolower(Core_Type_Conversion::toStr($param['order_templates']));

			if ($param['order_templates'] != 'asc' && $param['order_templates'] != 'desc')
			{
				$param['order_templates'] = 'ASC';
			}
		}
		else
		{
			$param['order_templates'] = 'ASC';
		}

		if (isset($param['site_id']))
		{
			if ($param['site_id'] !== false)
			{
				$param['site_id'] = Core_Type_Conversion::toInt($param['site_id']);
			}
			else
			{
				$param['site_id'] = '';
			}
		}
		else
		{
			$param['site_id'] = CURRENT_SITE;
		}

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'templates_group_id'),
				array('parent_id', 'templates_group_parent_id'),
				array('name', 'templates_group_name'),
				array('user_id', 'users_id'),
				'site_id'
		)->from('template_dirs')->where('deleted', '=', 0);

		if ($param['site_id'] != '')
		{
			$queryBuilder->where('site_id', '=', $param['site_id']);
		}

		$queryBuilder->orderBy($param['sort_field_templates_groups'], $param['order_templates_groups']);

		$result_templates_groups = $queryBuilder->execute()->asAssoc()->getResult();

		// Массив групп макетов сайта
		$mas_templates_groups = array();

		// В цикле формируем массив групп макетов сайта
		while ($row_templates_groups = mysql_fetch_assoc($result_templates_groups))
		{
			$mas_templates_groups[$row_templates_groups['templates_group_id']] = $row_templates_groups;
		}

		// Массив, содержащий группы в соответствии с их иерархией
		$new_mas_templates_groups = array();

		foreach ($mas_templates_groups as $key => $value)
		{
			if ($value['templates_group_parent_id'] == 0)
			{
				// Временный массив для хранения поддерева
				$temp_mas_templates_groups = array();

				// Сохраняем данные об узле, для которого строим поддерево
				$temp_mas_templates_groups[] = $value;

				// Получаем поддерево, сохраняем данные об узле и его поддереве
				$temp_mas_templates_groups = $this->GetChildrenItemMasTemplatesGroup($mas_templates_groups, $key, $temp_mas_templates_groups);

				// Добавляем данные об узле дерева
				$new_mas_templates_groups = array_merge($new_mas_templates_groups, $temp_mas_templates_groups);
			}
		}

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'templates_id'),
				array('template_dir_id', 'templates_group_id'),
				array('name', 'templates_name'),
				array('sorting', 'templates_order'),
				array('timestamp', 'templates_timestamp'),
				array('user_id', 'users_id'),
				'site_id'
		)->from('templates')->where('deleted', '=', 0);

		if ($param['site_id'] != '')
		{
			$queryBuilder->where('site_id', '=', $param['site_id']);
		}

		$queryBuilder->orderBy('templates_order', 'ASC');
		$queryBuilder->orderBy($param['sort_field_templates'], $param['order_templates']);

		$result_templates = $queryBuilder->execute()->asAssoc()->getResult();

		// Массив макетов сайта
		$mas_templates = array();

		// В цикле формируем массив макетов сайта
		while ($row_templates = mysql_fetch_assoc($result_templates))
		{
			$mas_templates[$row_templates['templates_group_id']][] = $row_templates;
		}

		// Массив полных путей от текущего макета до корневого
		$mas_path_templates = array();

		$i = 0;

		// Цикл по группам макетов данных
		foreach ($new_mas_templates_groups as $key1 => $value1)
		{
			if (isset($mas_templates[$value1['templates_group_id']]))
			{
				foreach ($mas_templates[$value1['templates_group_id']] as $data_template_row)
				{
					$mas_path_templates[$data_template_row['templates_id']] = $this->GetPathTemplatesGroupCurrentToRoot($value1['templates_group_id'], $new_mas_templates_groups, '', $param['templates_group_separator']);

					if (!empty($mas_path_templates[$data_template_row['templates_id']]))
					{
						$mas_path_templates[$data_template_row['templates_id']] = $mas_path_templates[$data_template_row['templates_id']] . $param['templates_separator'];
					}

					$mas_path_templates[$data_template_row['templates_id']] = $mas_path_templates[$data_template_row['templates_id']] . $data_template_row['templates_name'];
				}
			}
		}

		if (isset($mas_templates[0]) && count($mas_templates[0]) > 0)
		{
			foreach ($mas_templates[0] as $key => $value )
			{
				$mas_path_templates[$value['templates_id']] =  $value['templates_name'];
			}
		}

		return $mas_path_templates;
	}

	/**
	* Построение поддерева узла дерева
	*
	* @param $mas_groups_tree исходный массив, содержащий информацию о группах
	* @param $mas_item_key идентификатор узла,  для которого необходимо построить поддерево
	* @param $target_mas_groups массив, в котором содержится часть поддерева данного узла
	* @param array $target_mas_groups массив групп, расположенных в соответствии со структурой поддерева
	*/
	function GetChildrenItemMasTemplatesGroup($mas_groups_tree, $mas_item_key, $target_mas_groups=array())
	{
		$mas_groups_tree = Core_Type_Conversion::toArray($mas_groups_tree);
		$mas_item_key = Core_Type_Conversion::toInt($mas_item_key);
		$target_mas_groups = Core_Type_Conversion::toArray($target_mas_groups);

		foreach ($mas_groups_tree as $key => $value)
		{
			// Текущий узел является дочерним для узла переданного в параметре
			if ($mas_item_key == $value['templates_group_parent_id'])
			{
				$target_mas_groups[] = $value;

				$target_mas_groups = $this->GetChildrenItemMasTemplatesGroup($mas_groups_tree, $value['templates_group_id'], $target_mas_groups);
			}
		}

		return $target_mas_groups;
	}

	/**
	* Построение пути от заданного узла дерева групп макетов страниц до корневого
	*
	* @param int $templates_group_id идентификатор узла дерева групп макетов страниц
	* @param array $mas_templates_groups массив, содержащий дерево групп макетов страниц
	* @param string $separator строка, разделяющая названия групп макетов страниц
	*/
	function GetPathTemplatesGroupCurrentToRoot($templates_group_id, $mas_templates_groups, $path_templates_groups = '', $separator = ' - ')
	{
		$templates_group_id = Core_Type_Conversion::toInt($templates_group_id);

		$mas_templates_groups = Core_Type_Conversion::toArray($mas_templates_groups);

		$separator = Core_Type_Conversion::toStr($separator);

		foreach ($mas_templates_groups as $key => $value)
		{
			if ($value['templates_group_id'] ==	$templates_group_id)
			{
				if ($path_templates_groups != '')
				{
					$path_templates_groups = $separator . $path_templates_groups;
				}

				$path_templates_groups = $value['templates_group_name'] . $path_templates_groups;

				if ($value['templates_group_parent_id'] != 0)
				{
					$path_templates_groups = $this->GetPathTemplatesGroupCurrentToRoot($value['templates_group_parent_id'], $mas_templates_groups, $path_templates_groups, $separator);
				}
			}
		}

		return $path_templates_groups;
	}

	/**
	* Получение информации о группе шаблонов данных
	*
	* @param int $data_templates_group_id идентификатор шаблона данных
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_templates_group_id = 5;
	*
	* $row = $template->GetDataTemplatesGroup($data_templates_group_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed ассоциативный массив с данными о группе шаблонов данных в случае успешного выполнения, false - в случае возникновения ошибки
	*/
	function GetDataTemplatesGroup($data_templates_group_id)
	{
		throw new Core_Exception('Method GetDataTemplatesGroup() does not allow');
	}

	/**
	* Получение информации обо всех группах шаблонов сайта
	*
	* @param int $data_templates_group_parent_id идентификатор родительской группы, если false - информация о всех группах
	* @param int $site_id идентификатор сайта, если не передан - используется текущий сайт
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_templates_group_parent_id = false;
	*
	* $row = $template->GetAllDataTemplatesGroups($data_templates_group_parent_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив со списком групп шаблонов
	*/
	function GetAllDataTemplatesGroups($data_templates_group_parent_id = false, $site_id = false)
	{
		throw new Core_Exception('Method GetAllDataTemplatesGroups() does not allow');
	}

	/**
	* Удаление шаблона страниц
	*
	* @param int $data_templates_id идентификатор удаляемого шаблона
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_templates_id = 13;
	*
	* $result = $template->del_data_templates($data_templates_id);
	*
	* if ($result)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	* @return boolean истина при удачном удалении, ложь - в обратном случае
	*/
	function del_data_templates($data_templates_id)
	{
		throw new Core_Exception('Method del_data_templates() does not allow');
	}

	/**
	* Метод удаления группы шаблонов данных
	*
	* @param int $data_templates_group_id идентификатор группы шаблонов данных
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_templates_group_id = 1;
	*
	* $result = $template->DelDataTemplatesGroup($data_templates_group_id);
	*
	* if ($result)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	* @return boolean  true в случае успешного выполнения, false - в противном случае
	*/
	function DelDataTemplatesGroup($data_templates_group_id)
	{
		throw new Core_Exception('Method DelDataTemplatesGroup() does not allow');
	}

	/**
	* Удаление макета
	*
	* @param int $templates_id идентификатор удаляемого макета
	* <code>
	* <?php
	* $template = new templates();
	*
	* $templates_id = 29;
	*
	* $result = $template->del_templates($templates_id);
	*
	* if ($result)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	* @return boolean истина при удачном удалении, ложь - в обратном случае
	*/
	function del_templates($templates_id)
	{
		$templates_id = intval($templates_id);

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'TEMPLATE';
			$cache->DeleteCacheItem($cache_name, $templates_id);
		}

		Core_Entity::factory('Template', $templates_id)->markDeleted();

		return TRUE;
	}

	/**
	* Добавление/редактирование макета
	*
	* @param array $param массив параметров
	* <br />int $param['templates_id'] идентификатор макета
	* <br />int $param['templates_group_id'] идентификатор группы макетов
	* <br />string $param['templates_name'] наименование макета
	* <br />int $param['templates_order'] порядковый номер
	* <br />string $param['template'] исходный текст макета
	* <br />string $param['css'] CSS макета
	* <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	* <code>
	* <?php
	* $template = new templates();
	*
	* $param['templates_group_id'] = 0;
	* $param['templates_name'] = 'Макет 1';
	* $param['template'] = '';
	* $param['css'] = '';
	* $param['site_id'] = CURRENT_SITE;
	*
	* $newid = $template->InsertTemplate($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return int идентификатор нового или редатируемого макета (в зависимости от типа действия)
	*/
	function InsertTemplate($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['templates_id']) || $param['templates_id'] == 0)
		{
			$param['templates_id'] = NULL;
		}

		$template = Core_Entity::factory('Template', $param['templates_id']);

		if (isset($param['templates_group_id']))
		{
			$template->template_dir_id = $param['templates_group_id'];
		}

		if (isset($param['templates_name']))
		{
			$template->name = $param['templates_name'];
		}

		if (isset($param['templates_order']))
		{
			$template->sorting = $param['templates_order'];
		}

		$template->timestamp = date('Y-m-d H:i:s');

		if (is_null($param['templates_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$template->user_id = $param['users_id'];
		}

		if (isset($param['site_id']))
		{
			$template->site_id = intval($param['site_id']);
		}

		$template->save();

		$template->saveTemplateFile(Core_Type_Conversion::toStr($param['template']));
		$template->saveTemplateCssFile(Core_Type_Conversion::toStr($param['css']));

		// Очистка файлового кэша.
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'TEMPLATE';
			$cache->DeleteCacheItem($cache_name, $functions_result);
		}

		return $template->id;
	}

	/**
	* Добавление/обновление группы макетов
	*
	* @param array $param массив параметров
	* - $param['templates_group_id'] идентификатор группы макетов
	* - $param['templates_group_parent_id'] идентификатор родительской группы
	* - $param['templates_group_name'] название группы
	* <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	* <code>
	* <?php
	* $template = new templates();
	*
	* $param['templates_group_parent_id'] = 0;
	* $param['templates_group_name'] = 'Группа макетов';
	* $param['site_id'] = CURRENT_SITE;
	*
	* $newid = $template->InsertTemplatesGroup($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return mixed идентификатор добавленной/обновленной группы в случае успешного выполнения, false - в противном случае
	*/
	function InsertTemplatesGroup($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['templates_group_id']) || $param['templates_group_id'] == 0)
		{
			$param['templates_group_id'] = NULL;
		}

		$template_dir = Core_Entity::factory('Template_Dir', $param['templates_group_id']);

		if (isset($param['templates_group_parent_id']))
		{
			$template_dir->parent_id = $param['templates_group_parent_id'];
		}

		if (isset($param['templates_group_name']))
		{
			$template_dir->name = $param['templates_group_name'];
		}

		if (is_null($param['templates_group_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$template_dir->user_id = $param['users_id'];
		}

		if (isset($param['site_id']))
		{
			$template_dir->site_id = $param['site_id'];
		}

		$template_dir->save();

		return $template_dir->id;
	}

	/**
	* Получение информации о группе макетов
	*
	* @param int $templates_group_id идентификатор группы макетов
	* <code>
	* <?php
	* $template = new templates();
	*
	* $templates_group_id = 4;
	*
	* $row = $template->GetTemplatesGroup($templates_group_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed ассоциативный массив с данными о группе макетов в случае успешного выполнения, false - в случае возникновения ошибки
	*/
	function GetTemplatesGroup($templates_group_id)
	{
		$templates_group_id = intval($templates_group_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'templates_group_id'),
				array('parent_id', 'templates_group_parent_id'),
				array('name', 'templates_group_name'),
				array('user_id', 'users_id'),
				'site_id'
			)
			->from('template_dirs')
			->where('deleted', '=', 0);

		if ($templates_group_id != -1)
		{
			$queryBuilder->where('id', '=', $templates_group_id);
		}

		return $queryBuilder->execute()->asAssoc()->current();
	}

	/**
	* Получение информации о всех группах макетов
	*
	* @param int $templates_group_parent_id идентификатор группы макетов, если false - извлекается информация обо всех макетах
	* @param int $site_id идентификатор сайта, если не передан - используется текущий сайт
	*
	* <code>
	* <?php
	* $template = new templates();
	*
	* $templates_group_parent_id = false;
	* $site_id = CURRENT_SITE;
	*
	* $row = $template->GetAllTemplatesGroups($templates_group_parent_id, $site_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return массив ассоциативных массивов с информацией о группах макетов в случае успешного выполнения, false - в противном случае
	*/
	function GetAllTemplatesGroups($templates_group_parent_id = FALSE, $site_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'templates_group_id'),
				array('parent_id', 'templates_group_parent_id'),
				array('name', 'templates_group_name'),
				array('user_id', 'users_id'),
				'site_id'
			)
			->from('template_dirs')
			->where('deleted', '=', 0);

		if ($templates_group_parent_id !== FALSE)
		{
			$templates_group_parent_id = intval($templates_group_parent_id);

			$queryBuilder->where('parent_id', '=', $templates_group_parent_id);
		}

		$site_id = $site_id === FALSE ? CURRENT_SITE : intval($site_id);

		$queryBuilder->where('site_id', '=', $site_id);

		return $queryBuilder->execute()->asAssoc()->result();
	}

	/**
	* Удаление группы макетов
	*
	* @param int $templates_group_id идентификатор группы макетов
	* <code>
	* <?php
	* $template = new templates();
	*
	* $templates_group_id = 4;
	*
	* $result = $template->DelTemplatesGroup($templates_group_id);
	*
	* if ($result)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	*/
	function DelTemplatesGroup($templates_group_id)
	{
		$templates_group_id = intval($templates_group_id);
		Core_Entity::factory('Template_Dir', $templates_group_id)->markDeleted();
		return TRUE;
	}

	/**
	* Копирование макета
	*
	* @param int $template_id идентификатор копируемого макета
	* @param int $new_templates_group_parent_id Идентификатор группы, в которую необходимо положить скопированный макет (если не указан, или имеет значение -1, то относится к той же группе, что и копируемый, по умолчанию -1)
	* <code>
	* <?php
	* $template = new templates();
	*
	* $template_id = 6;
	*
	* $newid = $template->CopyTemplate($template_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return int идентификатор нового добавленного макета
	*/
	function CopyTemplate($template_id, $site_id = FALSE, $new_templates_group_parent_id = -1)
	{
		$template_id = intval($template_id);
		$clone_template = Core_Entity::factory('Template', $template_id)->copy();
		return $clone_template->id;
	}

	/**
	* Метод копирования шаблона страниц
	*
	* @param int $data_template_id идентификатор копируемого шаблона страниц
	* @param int $new_data_templates_group_parent_id Идентификатор группы, к которой необходимо отнести скопированный шаблон (если не указан, или имеет значение -1, то относится к той же группе, что и копируемый. по умолчанию -1)
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_template_id = 11;
	*
	* $newid = $template->CopyDataTemplate($data_template_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return int идентификатор нового добавленного шаблона страниц
	*/
	function CopyDataTemplate($data_template_id, $site_id = FALSE, $new_data_templates_group_parent_id = -1)
	{
		throw new Core_Exception('Method CopyDataTemplate() does not allow');
	}

	/**
	* Получение информации о макете
	*
	* @param int $templates_id идентификатор макета
	* @param array $param ассоциативный массив параметров
	* - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	* <code>
	* <?php
	* $template = new templates();
	*
	* $templates_id = 1;
	*
	* $row = $template->GetTemplate($templates_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed ассоциативный массив с информацией о макете в случае удачной выборки, ложь - если макет не выбран
	*/
	function GetTemplate($templates_id, $param = array())
	{
		$templates_id = intval($templates_id);
		$param = Core_Type_Conversion::toArray($param);

		$cache_name = 'TEMPLATE';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($templates_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'templates_id'),
			array('template_dir_id', 'templates_group_id'),
			array('name', 'templates_name'),
			array('sorting', 'templates_order'),
			array('timestamp', 'templates_timestamp'),
			array('user_id', 'users_id'),
			'site_id'
		)->from('templates')
		->where('deleted', '=', 0)
		->where('id', '=', $templates_id);

		/* Если добавлено кэширование*/
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache->Insert($templates_id, $template_row, $cache_name);
		}

		return $queryBuilder->execute()
			->asAssoc()->current();
	}

	/**
	* Формирование дерева шаблонов
	*
	* @param int $data_templates_group_parent_id - идентификатор родительского раздела
	* @param string $separator - символ (строка)-разделитель
	* @param bool $first_call первый вызов ф-ции, по умолчанию - true
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_templates_group_parent_id = 5;
	* $separator= '';
	*
	* $row = $template->GetDataTemplateDirTree($data_templates_group_parent_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив с данными о дереве шаблонов
	*/
	function GetDataTemplateDirTree($data_templates_group_parent_id, $separator= '',
	$current_group_id = FALSE, $first_call = true, $site_id = FALSE)
	{
		throw new Core_Exception('Method GetDataTemplateDirTree() does not allow');
	}

	/**
	* Построение массива пути от текущей группы шаблонов к корневой
	*
	* @param int $data_templates_group_id идентификатор текущего узла
	* @param bool $first_call первый вызов ф-ции, по умолчанию - true
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_templates_group_id = 6;
	*
	* $row = $template->GetDataTemplatePathArray($data_templates_group_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив с элементами пути группы
	*/
	function GetDataTemplatePathArray($data_templates_group_id, $first_call = true)
	{
		throw new Core_Exception('Method GetDataTemplatePathArray() does not allow');
	}

	/**
	* Построение массива пути от текущей группы макетов к корневой
	*
	* @param int $templates_group_id идентификатор текущей группы
	* @param bool $first_call первый вызов ф-ции, по умолчанию - true
	* <code>
	* <?php
	* $template = new templates();
	*
	* $templates_group_id = 5;
	*
	* $row = $template->GetTemplatePathArray($templates_group_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив с элементами пути группы
	*/
	function GetTemplatePathArray($templates_group_id, $first_call = true)
	{
		$templates_group_id = intval($templates_group_id);
		$first_call = Core_Type_Conversion::toBool($first_call);

		if ($first_call)
		{
			$this->path_array = array();
		}

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'templates_group_id'),
				array('parent_id', 'templates_group_parent_id'),
				array('name', 'templates_group_name'),
				array('user_id', 'users_id'),
				'site_id'
		)->from('template_dirs')->where('deleted', '=', 0)
		->where('id', '=', $templates_group_id);

		$row = $queryBuilder->execute()->asAssoc()->current();

		if ($row)
		{
			$this->path_array = $this->GetTemplatePathArray($row['templates_group_parent_id'], FALSE);
			$this->path_array[$templates_group_id] = $row['templates_group_name'];
		}
		else
		{
			$this->path_array[0] = Core::_('template_dir.templates_dir_root');
		}

		return $this->path_array;
	}

	/**
	* Формирование дерева макетов
	*
	* @param int $data_templates_group_parent_id - идентификатор родительского раздела
	* @param string $separator - символ (строка)-разделитель
	* @param bool $first_call первый вызов ф-ции, по умолчанию - true
	* @param  int $site_id идентификатор сайта
	* <code>
	* <?php
	* $template = new templates();
	*
	* $templates_group_parent_id = 2;
	* $separator = '';
	*
	* $row = $template->GetTemplateDirTree($templates_group_parent_id, $separator);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив с данными о дереве макетов
	*/
	function GetTemplateDirTree($templates_group_parent_id, $separator,
	$current_group_id = FALSE, $first_call = true, $site_id = FALSE)
	{
		$templates_group_parent_id = intval($templates_group_parent_id);
		$first_call = Core_Type_Conversion::toBool($first_call);
		if ($site_id === FALSE)
		{
			$site_id = CURRENT_SITE;
		}
		else
		{
			$site_id = Core_Type_Conversion::toInt($site_id);
		}
		if ($first_call)
		{
			$this->mas_data_templates_dir = array();
		}

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'templates_group_id'),
				array('parent_id', 'templates_group_parent_id'),
				array('name', 'templates_group_name'),
				array('user_id', 'users_id'),
				'site_id'
		)->from('template_dirs')->where('deleted', '=', 0)
		->where('parent_id', '=', $templates_group_parent_id)
		->where('site_id', '=', $site_id)
		->orderBy('templates_group_name', 'ASC');

		$result = $queryBuilder->execute()->getResult();

		while($row = mysql_fetch_assoc($result))
		{
			if ($current_group_id)
			{
				if ($row['templates_group_id'] == $current_group_id)
				{
					continue;
				}
			}

			$count_mas = count($this->mas_data_templates_dir);

			$this->mas_data_templates_dir[$count_mas][0] = $row['templates_group_id'];
			$this->mas_data_templates_dir[$count_mas][1] = $separator.htmlspecialchars($row['templates_group_name']);
			$this->GetTemplateDirTree($row['templates_group_id'],
			$separator.$separator, $current_group_id, FALSE);
		}

		return $this->mas_data_templates_dir;
	}

	/**
	* Получение списка макетов текущего сайта
	*
	* @param int $templates_group_id идентификатор группы макетов, если FALSE - извлекается информация обо всех макетах
	* @param int $site_id идентификатор сайта, если не передан - используется текущий сайт
	* <code>
	* <?php
	* $template = new templates();
	*
	* $templates_group_id = FALSE;
	* $site_id = CURRENT_SITE;
	*
	* $row = $template->GetAllTemplates($templates_group_id, $site_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив с данными о макетах
	*/
	function GetAllTemplates($templates_group_id = FALSE, $site_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'templates_id'),
				array('template_dir_id', 'templates_group_id'),
				array('name', 'templates_name'),
				array('sorting', 'templates_order'),
				array('timestamp', 'templates_timestamp'),
				array('user_id', 'users_id'),
				'site_id'
		)->from('templates')->where('deleted', '=', 0);

		$site_id = $site_id === FALSE ? CURRENT_SITE : intval($site_id);
		$queryBuilder->where('site_id', '=', $site_id);

		if ($templates_group_id !== FALSE)
		{
			$templates_group_id = intval($templates_group_id);

			$queryBuilder->where('id', '=', $templates_group_id);
		}

		$queryBuilder->orderBy('templates_order');
		$queryBuilder->orderBy('templates_name');

		return $queryBuilder->execute()->asAssoc()->result();
	}

	/**
	* Метод для получения списка шаблонов страниц текущего сайта
	*
	* @param int $data_templates_group_id идентификатор группы макетов, если FALSE - извлекается информация обо всех макетах
	* @param int $site_id идентификатор сайта, если не передан - используется текущий сайт
	* <code>
	* <?php
	* $template = new templates();
	*
	* $data_templates_group_id = FALSE;
	* $site_id = CURRENT_SITE;
	*
	* $row = $template->GetAllDataTemplates($data_templates_group_id, $site_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив с данными о макетах
	*/
	function GetAllDataTemplates($data_templates_group_id = FALSE, $site_id = FALSE)
	{
		throw new Core_Exception('Method GetAllDataTemplates() does not allow');
	}

	/**
	 * Копирование групп макетов с макетами и подгруппами
	 *
	 * @param int $templates_group_parent_id идентификатор родительской группы
	 * @param int $new_templates_group_parent_id идентификатор скопированной родительской группы
	 * @param int $site_id идентификатор сайта
	 * @param int $new_site_id идентификатор скопированного сайта
	 */
	function CopyTemplatesDir($templates_group_parent_id, $new_templates_group_parent_id, $site_id, $new_site_id, $return_array_ids = FALSE)
	{
		throw new Core_Exception('Method CopyTemplatesDir() does not allow');
	}

	/**
	 * Копирование групп шаблонов с шаблонами и подгруппами
	 *
	 * @param int $data_templates_group_parent_id идентификатор родительской группы
	 * @param int $new_data_templates_group_parent_id идентификатор скопированной родительской группы
	 * @param int $site_id идентификатор сайта
	 * @param int $new_site_id идентификатор скопированного сайта
	 */
	function CopyDataTemplatesDir($data_templates_group_parent_id, $new_data_templates_group_parent_id, $site_id, $new_site_id)
	{
		throw new Core_Exception('Method CopyDataTemplatesDir() does not allow');
	}
}
