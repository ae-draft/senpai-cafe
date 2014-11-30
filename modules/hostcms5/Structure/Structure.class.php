<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Структура".
 *
 * Файл: /modules/Structure/Structure.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class Structure
{
	/**
	* Идентификатор родительского узла элемента структуры
	* @var int
	*/
	var $parent_id;

	/**
	* Максимальный уровень структуры
	* @var int
	*/
	var $level;

	/**
	* Разделитель для уровней
	* @var string
	*/
	var $separator = '';

	/**
	* Идентификатор раздела меню
	* @var int
	*/
	var $menu_id;

	/**
	* @access private
	* @var array
	*/
	var $mass;

	/**
	* Массив родительских разделов
	* @var array
	*/
	var $StructureParentMass = array ();

	/**
	* Массив с элементами структуры
	* @var array
	*/
	var $StructureMass = array ();

	/**
	* Двумерный массив для хранения св-в структуры. Первый элемент - ID структуры.
	* @var array
	*/
	var $cache_structure_property = array();

	/**
	* Хранит результат запроса на выборку дополнительных свойств структуры
	* @var resource
	*/
	var $structure_propertys_list;

	/**
	* Ассоциативный массив-Кэш количества свойств для разных сайтов
	* @var array
	*/
	var $a_count_property;

	/**
	* @access private
	*/
	var $MassIS;

	/**
	* @access private
	*/
	var $MassIG;

	/**
	* @access private
	*/
	var $MassII;

	/**
	*
	* @var unknown_type
	* @access private
	*/
	var $XmlDataFromGenXml4StructureLevelMass = '';

	/**
	* @access private
	*/
	var $MassShopShops;

	/**
	* @access private
	*/
	var $MassShopItems;

	/**
	* @access private
	*/
	var $MassShopGroups;

	/**
	* Получение пути к директории lib
	*
	* @param int $lib_id Идентификатор типовой динамической страницы
	*
	* @return string путь к файлу
	*/
	function GetStructureFilesPath()
	{
		return CMS_FOLDER . "hostcmsfiles/structure";
	}

	/**
	* Получение идентификатора родительского узла текущего элемента структуры($this->parent_id)
	*
	* @return int идентификатор родительского узла элемента структуры
	* @access private
	*/
	function GetPropertyParentId()
	{
		return $this->parent_id;
	}

	/**
	* Получение информации о максимальном уровне($this->level)
	*
	* @return int значение максимального уровня
	*/
	function GetPropertyLevel()
	{
		return $this->level;
	}

	/**
	* Получение информации о разделителе($this->separator)
	*
	* @return string разделитель для уровней
	*/
	function GetPropertySeparator()
	{
		return $this->separator;
	}

	/**
	* Получение информации об элементах структуры
	*
	* @return array массив с элементами структуры
	*/
	function GetPropertyStructureMass()
	{
		return $this->StructureMass;
	}

	/**
	* Получение информации о текущем разделе меню($this->menu_id)
	* @return int идентификатор текущего раздела
	*/
	function GetPropertyMenuId()
	{
		return $this->menu_id;
	}

	/**
	* Получение информации о родительских разделах элемента структуры($this->StructureParentMass)
	*
	* @return array информация о родительских разделах
	*/
	function GetPropertyStructureParentMass()
	{
		return $this->StructureParentMass;
	}

	/**
	* Получение дополнительных свойств структуры
	* @return resource результат запроса на выборку дополнительных свойств структуры
	*/
	function GetPropertyStructurePropertysList()
	{
		return $this->structure_propertys_list;
	}

	/**
	* Получение количества свойств для разных сайтов
	* @return array ассоциативный массив-Кэш количества свойств для разных сайтов
	*/
	function GetPropertyACountProperty()
	{
		return $this->a_count_property;
	}

	/**
	* @access private
	* @return array
	*/
	function GetPropertyMassIS()
	{
		return $this->MassIS;
	}

	/**
	* @access private
	*/
	function GetPropertyMassIG()
	{
		return $this->MassIG;
	}

	/**
	* @access private
	*/
	function GetPropertyMassII()
	{
		return $this->MassII;
	}

	/**
	* Метод очистки кэша($this->structure_mass)
	*/
	function ClearStructure()
	{
		// Инициализируем данные XML пустым значением
		$this->structure_mass = array ();
	}

	function getArrayStructure($oStructure)
	{
		return array(
			'structure_id' => $oStructure->id,
			'menu_id' => $oStructure->structure_menu_id,
			'templates_id' => $oStructure->template_id,
			'data_templates_id' => $oStructure->data_template_id,
			'site_id' => $oStructure->site_id,
			'documents_id' => $oStructure->document_id,
			'lib_id' => $oStructure->lib_id,
			'structure_parent_id' => $oStructure->parent_id,
			'structure_show' => $oStructure->show,
			'structure_menu_name' => $oStructure->name,
			'structure_title' => $oStructure->seo_title,
			'structure_description' => $oStructure->seo_description,
			'structure_keywords' => $oStructure->seo_keywords,
			'structure_external_link' => $oStructure->url,
			'structure_order' => $oStructure->sorting,
			'structure_path_name' => $oStructure->path,
			'structure_type' => $oStructure->type,
			'structure_access' => $oStructure->siteuser_group_id,
			'structure_access_protocol' => $oStructure->https,
			'structure_activity' => $oStructure->active,
			'structure_allow_indexation' => $oStructure->indexing,
			'structure_change_frequency' => $oStructure->changefreq,
			'structure_priority' => $oStructure->priority,
			'users_id' => $oStructure->user_id
		);
	}

	function getArrayPropertyValue($oPropertyValue)
	{
		$oProperty = Core_Entity::factory('Property', $oPropertyValue->property_id);

		$array = array(
			'structure_propertys_id' => $oProperty->id,
			'site_id' => $oProperty->Structure_Property->site_id,
			'structure_propertys_name' => $oProperty->name,
			'structure_propertys_type' => $oProperty->type,
			'structure_propertys_order' => $oProperty->sorting,
			'structure_propertys_define_value' => $oProperty->default_value,
			'structure_propertys_xml_name' => $oProperty->tag_name,
			'structure_propertys_lists_id' => $oProperty->list_id,
			'structure_propertys_information_systems_id' => $oProperty->informationsystem_id,
			'users_id' => $oProperty->user_id,
			'structure_propertys_values_id' => $oPropertyValue->id,
			'structure_id' => $oPropertyValue->entity_id,
		);

		if ($oProperty->type != 2)
		{
			$array['structure_propertys_values_value'] = $oPropertyValue->value;
			$array['structure_propertys_values_file'] = '';
			$array['structure_propertys_values_value_small'] = '';
			$array['structure_propertys_values_file_small'] = '';
		}
		else
		{
			$array['structure_propertys_values_value'] = $oPropertyValue->file_name;
			$array['structure_propertys_values_file'] = $oPropertyValue->file;
			$array['structure_propertys_values_value_small'] = $oPropertyValue->file_small_name;
			$array['structure_propertys_values_file_small'] = $oPropertyValue->file_small;
		}

		return $array;
	}

	/**
	* Получение данных об элементе структуры. Идентификатор текущего узла структуры содержится в константе CURRENT_STRUCTURE_ID
	*
	* @param int $structure_id идетификатор структуры
	* @param array $param ассоциативный массив параметров
	* - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Воспользуемся Идентификатором текущего узла структуры сайта
	* $structure_id = CURRENT_STRUCTURE_ID;
	*
	* $row = $structure->GetStructureItem($structure_id);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	* @return mixed ассоциативный массив с данными об узле структуры или FALSE, если узел не найден
	*/
	function GetStructureItem($structure_id, $param = array ())
	{
		$structure_id = intval($structure_id);
		$param = Core_Type_Conversion::toArray($param);

		/* Если есть в массиве - возвращаем это значение*/
		if (isset($this->StructureMass[$structure_id]) && !isset($param['cache_off']))
		{
			return $this->StructureMass[$structure_id];
		}

		$cache_name = 'STRUCTURE';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($structure_id, $cache_name))
			{
				if (isset($this->StructureMass))
				{
					$this->StructureMass[$structure_id] = $in_cache['value'];
				}

				return $in_cache['value'];
			}
		}

		$row = FALSE;

		if ($structure_id > 0)
		{
			$oStructure = Core_Entity::factory('Structure')->find($structure_id);

			if (!is_null($oStructure->id))
			{
				// Полный путь к файлу, Нельзя, т.к. будет зацикливание
				//$path = $this->GetStructurePath($row['structure_id'], 0);
				$row = $this->getArrayStructure($oStructure);

				// Сохраняем в массиве значение, возможно понадобится позже
				if (isset($this->StructureMass))
				{
					$this->StructureMass[$structure_id] = $row;
				}
			}
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache->Insert($structure_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	* Получение информации о доступе к узлу элементов структуры
	*
	* @param int $structure_id идентификатор страницы структуры
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $structure_id = CURRENT_STRUCTURE_ID;
	*
	* $result = $structure->GetStructureAccess($structure_id);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	* @return int ID группы пользователей сайта, которой доступен узел.
	* 0 - доступно всем, -1 - узел (или один из родительских узлов) не найден.
	*/
	function GetStructureAccess($structure_id)
	{
		$structure_id = intval($structure_id);

		do
		{
			$row = $this->GetStructureItem($structure_id);

			// Если структура с заданным идентификатором не найдена, то возвращаем -1
			if (!$row && $structure_id != 0)
			{
				return -1;
			}

			$structure_id = $row['structure_parent_id'];

		}
		while ($row['structure_parent_id'] != 0 && $row['structure_access'] == -1);

		// Если достигнут корень и для родителя доступ - "Наследование" - то возвращаем 0 - доступ разрешен всем.
		if ($row['structure_parent_id'] == 0 && $row['structure_access'] == -1 || $row['structure_access'] == 0)
		{
			return 0;
		}
		else
		{
			return $row['structure_access'];
		}
	}

	/**
	* @access private
	*/
	function GerStructureItem($structure_id)
	{
		return $this->GetStructureItem($structure_id);
	}

	/**
	* Метод выборки всех страниц структуры
	*
	* @param string $separator разделитель для уровней
	* @param int or boolean $site_id идентификатор сайта, если равен 0 или FALSE - то сайт игнорируется
	* @param int $menu_id идентификатор раздела меню(если не задан - все разделы меню)
	* @param int $level максимальный уровень
	* @param int $parent_id идентификатор родительского узла
	* @param int $edit_id идентификатор редактируемой записи(необязательный параметр), для того, чтобы убрать редактируемую запись из списка возможных род. разделов
	* @param array $param массив дополнительных параметров
	* - $param['select_structure_property'] разрешает заполнение кэша значений свойств узлов структуры, по умолчанию true
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $separator = '&nbsp';
	* $menu_id = 1;
	* $level = 0;
	* $parent_id = 0;
	* $site_id = 1;
	*
	* $array = $structure->GetStructure($separator, $site_id, $menu_id, $level, $parent_id);
	*
	* // Распечатаем результат
	* print_r($array);
	* ?>
	* </code>
	* @return array информация об узлах структуры сайта
	*/
	function GetStructure($separator, $site_id, $menu_id, $level, $parent_id, $edit_id = FALSE, $param = FALSE)
	{
		$this->menu_id = intval($menu_id);
		$this->separator = strval($separator);

		// Максимальный уровень
		$this->level = intval($level);
		$this->parent_id = intval($parent_id);

		if (!isset($param['select_structure_property']))
		{
			$param['select_structure_property'] = TRUE;
		}

		$oStructure = Core_Entity::factory('Structure');

		// если $menu_id не равен 0 или FALSE
		if ($menu_id)
		{
			$oStructure
				->queryBuilder()
				->where('structure_menu_id', '=', $this->menu_id);
		}

		$site_id = intval($site_id);
		if ($site_id)
		{
			$oStructure
				->queryBuilder()
				->where('site_id', '=', $site_id);
		}

		$oStructure
			->queryBuilder()
			->orderBy('parent_id')
			->orderBy('sorting')
			->orderBy('name');

		$this->StructureParentMass = array ();

		// Массив ID для определения св-в
		$propertylist = array ();

		$aStructure = $oStructure->findAll();

		// Заполняем массив ID родителей и детей
		//while ($row = mysql_fetch_assoc($result))
		foreach ($aStructure as $key => $oStructure)
		{
			// исключаем редактируемый узел и все его подпункты(если передан Edit-ID)
			if ($edit_id != $oStructure->id)
			{
				$this->StructureMass[$oStructure->id] = $this->getArrayStructure($oStructure);

				// Список для определения св-в
				$propertylist[] = $oStructure->id;

				// Нужно для вывода подстраниц из инфосистем, когда у самой структуры потомков нет
				if (!isset($this->StructureParentMass[$oStructure->id]))
				{
					$this->StructureParentMass[$oStructure->id] = array();
				}

				// Для массива родителей сохраняем только ID элементов
				$this->StructureParentMass[intval($oStructure->parent_id)][] = intval($oStructure->id);
			}
		}

		if ($param['select_structure_property'])
		{
			// В кэш пишем полученные свойства
			$this->GetStructureProperty($propertylist, $site_id);
		}

		// Начинаем заполнять массив с parent_id;
		$level = 0; // Начальный уровень = 0, не путать с $this->level
		$this->GetStructureForParent($parent_id, $separator, $level);

		$this->StructureMass = $this->SortStructureMass($parent_id);

		// Возвращаем отсортированный массив
		return $this->StructureMass;
	}

	/**
	* Метод сортирует массив StructureMass, возвращает отсортированный массив в соответствии с иерархией
	*
	* @param int $parent_id идентификатор родительского раздела
	* @param array $mass массив с элементами структуры
	* <br />Пример использования:
	* <code>
	* <?php
	* $Structure = new Structure();
	*
	* $parent_id = 0;
	*
	* $row = $Structure->SortStructureMass($parent_id, $mass=array());
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array отсортированный массив
	*/
	function SortStructureMass($parent_id, $mass = array ())
	{
		if (!isset($this->StructureParentMass[$parent_id]))
		{
			return $mass;
		}

		// Сортируем массив в соответствии с иерархической структурой
		$count = count($this->StructureParentMass[$parent_id]);

		for ($i = 0; $i < $count; $i++)
		{
			if (!isset($mass[$this->StructureParentMass[$parent_id][$i]])
			&& isset($this->StructureMass[$this->StructureParentMass[$parent_id][$i]]))
			{
				$mass[$this->StructureParentMass[$parent_id][$i]] = $this->StructureMass[$this->StructureParentMass[$parent_id][$i]];

				$mass = $this->SortStructureMass($this->StructureParentMass[$parent_id][$i], $mass);
			}
		}

		return $mass;
	}

	/**
	* Получение дочерних узлов структуры для узла структуры.
	* Результирующий массив размещается в $this->StructureMass
	* Перед вызовом метода должен быть заполнен $this->StructureParentMass с помощью метода $this->GetStructure()
	*
	* @param int $parent_id идентификатор родительского раздела
	* @param string $separator разделитель для уровней
	* @param int $current_level текущий уровень вложенности. Для корневого раздела указывается 0.
	* @return boolean формирует массив с информацией о подразделах, а в случае ошибки возвращает ложь
	* @see GetStructure()
	*/
	function GetStructureForParent($parent_id, $separator, $current_level)
	{
		$parent_id = intval($parent_id);

		// Если пустой массив - ничего не производим
		if (sizeof($this->StructureParentMass) == 0)
		{
			return FALSE;
		}

		if (isset($this->StructureParentMass[$parent_id]) && is_array($this->StructureParentMass[$parent_id]))
		{
			// Разбираем массив родителей с ID элементов структуры для данного родителя.
			foreach ($this->StructureParentMass[$parent_id] as $key => $structure_id)
			{
				// Получаем значение элемента
				$row = $this->GetStructureItem($structure_id);

				// Если корневая рубрика - перед ней разделитель не ставится
				if ($current_level == 0)
				{
					$separator = '';
				}

				$name = $separator . $row["structure_menu_name"];

				// Имя с разделителем для текущего уровня
				$this->StructureMass[$row['structure_id']]['name_with_separator'] = $name;

				// Разделитель для данного уровня
				$this->StructureMass[$row['structure_id']]['separator'] = $separator;

				// Полный путь к файлу
				$path = $this->GetStructurePath($row['structure_id'], 0);

				if (trim($path) != '/')
				{
					$path = '/'.$path;
				}

				$this->StructureMass[$row['structure_id']]['full_path'] = $path;
				$this->StructureMass[$row['structure_id']]['current_level'] = $current_level;

				if (($this->level == -1) || ($current_level < $this->level))
				{
					$this->parent_id = $row["structure_id"];
					//$this->menu_id = $this->menu_id;

					// рекурсивный вызов функции
					$this->GetStructureForParent($row['structure_id'], $separator.$this->separator, $current_level +1);
				}
			}
		}
	}

	/**
	* Определение полного пути к странице
	*
	* @param int $structure_id идентификатор раздела, для него определяется полный пути снизу вверх
	* @param int $type тип результата(строка -- 0 или масссив -- 1). по умолчанию 0
	* @param mixed $path переменная, в которую возвращается результат(строка, либо массив)
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $structure_id = CURRENT_STRUCTURE_ID;
	* $type = 0;
	* $path = $structure->GetStructurePath($structure_id, $type);
	*
	* // Распечатаем результат
	* echo $path;
	* ?>
	* </code>
	* @return mixed полный путь к странице(в виде строки или массива)
	*/
	function GetStructurePath($structure_id, $type = 0, $path = '')
	{
		$structure_id = intval($structure_id);

		$row = $this->GetStructureItem($structure_id);

		if ($row)
		{
			if ($type == 0)
			{
				// если это главная страница
				if (trim($row['structure_path_name']) != '/')
				{
					$separator = '/';
				}
				else
				{
					$separator = '';
					$row['structure_path_name'] = '';
				}

				$path = rawurlencode($row['structure_path_name']) . $separator . $path;
			}
			else
			{
				$path[] = rawurlencode($row['structure_path_name']);
			}

			if ($row['structure_parent_id'] != 0 && $structure_id != $row['structure_parent_id'])
			{
				$path = $this->GetStructurePath($row['structure_parent_id'], $type, $path);
			}
		}

		return $path;
	}

	/**
	* Метод определения наличия пути
	*
	* @param array $path_array массив с элементами пути, ID меню
	* @param int $menu_id идентификатор раздела меню
	* @return mixed идентификатор родительского раздела - при наличии у данного меню страницы с аналогичным путем, FALSE при ее отсутствии
	*/
	function IssetPath($path_array, $menu_id)
	{
		$menu_id = intval($menu_id);

		$count = count($path_array);

		// ID родительского раздела
		$parent_id = 0;

		for ($i = $count; $i > 0; $i--)
		{
			$oStructure = Core_Entity::factory('Structure');
			$oStructure
				->queryBuilder()
				->clear()
				->where('path', '=', $path_array[$i-1])
				->where('parent_id', '=', $parent_id)
				->where('structure_menu_id', '=', $menu_id)
				->limit(1);

			$oStructure->find();

			if (!is_null($oStructure->id))
			{
				$parent_id = $oStructure->id;
			}
			else
			{
				return FALSE;
			}
		}

		return $parent_id; // идентификатор родительского раздела
	}

	/**
	* Метод обновления идентификатора меню у подразделов определенного раздела
	*
	* @param int $structure_id идентификатор раздела структуры
	* @param int $menu_id идентификатор раздела меню
	*
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $structure_id = CURRENT_STRUCTURE_ID;
	* $menu_id = 2;
	*
	* $structure->UpdateSubDir($structure_id, $menu_id);
	* ?>
	* </code>
	*/
	function UpdateSubDir($structure_id, $menu_id)
	{
		$structure_id = intval($structure_id);
		$menu_id = intval($menu_id);

		$aStructure = Core_Entity::factory('Structure', $structure_id)->Structures->findAll();

		foreach ($aStructure as $oStructure)
		{
			$oStructure->structure_menu_id = $menu_id;
			$oStructure->save();

			$this->UpdateSubDir($oStructure->id, $menu_id);
		}
	}

	/**
	* Метод добавления/редактирования узла структуры
	* @param array $param массив параметров
	* - int $param['structure_id'] идентификатор узла структуры
	* - int $param['menu_id'] идентификатор раздела меню
	* - int $param['templates_id'] идентификатор макета
	* - int $param['data_templates_id'] идентификатор шаблона страниц
	* - int $param['site_id'] идентификатор сайта
	* - int $param['documents_id'] идентификатор документа
	* - int $param['structure_parent_id'] идентификатор родительского раздела
	* - int $param['structure_show'] флаг вывода элемента структуры
	* - string $param['structure_menu_name'] название раздела в меню
	* - string $param['structure_title'] заголовок для раздела
	* - string $param['structure_description'] описание раздела
	* - string $param['structure_keywords'] ключевые фразы
	* - string $param['structure_external_link'] внешняя ссылка для раздела
	* - int $param['structure_order'] порядок сортировки
	* - string $param['structure_path_name'] название раздела
	* - int $param['structure_type'] тип раздела. 0 - Страница, 1 - Динамическая страница, 2 - Типовая динамическая страница
	* - string $param['module'] значение для динамической страницы
	* - string $param['module_config'] нстройки для динамической страницы
	* - int $param['structure_activity'] параметр, определяющий активность узла структуры(1 по умолчанию) - узел активен, 0 - неактивен)
	* - int $param['structure_access'] параметр, определяющий группу пользователей сайта, имеющих право доступа к узлу структуры.
	* Значения 0 (узел доступен всем, по умолчанию), -1 - как у родителя, другое значение - идентификатор группы пользователей сайта)
	* - int $param['structure_access_protocol'] параметр, определяющий протокол, используемый для доступа к узлу структуры. 0 - HTTP, 1 - HTTPS
	* - int $param['structure_allow_indexation'] параметр, определяющий индексировать узел структуры или нет(1 по умолчанию) - индексировать, 0 - не индексировать)
	* - int $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь.
	* - int $param['lib_id'] идентификатор типовой динамической страницы
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $param = array();
	*
	* $param['structure_id'] = '';
	* $param['menu_id'] = 1;
	* $param['templates_id'] = 1;
	* $param['data_templates_id']= 1;
	* $param['site_id'] = 1;
	* $param['documents_id'] = 55;
	* $param['structure_parent_id'] = 0;
	* $param['structure_show'] = 1;
	* $param['structure_menu_name'] = 'Название в меню';
	* $param['structure_title'] = 'SEO - заголовок';
	* $param['structure_description'] = 'SEO - описание';
	* $param['structure_keywords'] = 'SEO - ключевые слова';
	* $param['structure_order'] = 50;
	* $param['structure_path_name'] = 'page';
	* $param['structure_type'] = 2;
	* $param['module'] = '';
	* $param['module_config'] = '';
	* $param['structure_access'] = 0;
	* $param['structure_activity'] = 1;
	* $param['structure_allow_indexation'] = 1;
	* $param['lib_id'] = 9;
	*
	* $new_structure_id = $structure->InsertStructure($param);
	*
	* // Распечатаем результат
	* echo $new_structure_id;
	* ?>
	* </code>
	* @return mixed идентификатор добавленного/измененного узла структуры в случае успешного выполнения, FALSE - в противном случае
	*/
	function InsertStructure($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['structure_id']) || $param['structure_id'] == 0)
		{
			$param['structure_id'] = NULL;
		}

		$oStructure = Core_Entity::factory('Structure', $param['structure_id']);

		$menu_id = Core_Type_Conversion::toInt($param['menu_id']);

		// Изменение меню у всех потомков
		if ($param['structure_id']
			&& $oStructure->structure_menu_id != $menu_id)
		{
			$this->UpdateSubDir($param['structure_id'], $menu_id);
		}
		$oStructure->structure_menu_id = $menu_id;

		// Warning: data_template будет объединен с макетами
		$oStructure->data_template_id = Core_Type_Conversion::toInt($param['data_templates_id']);

		$oStructure->site_id = Core_Type_Conversion::toInt($param['site_id']);
		$oStructure->document_id = Core_Type_Conversion::toInt($param['documents_id']);
		$oStructure->parent_id = Core_Type_Conversion::toInt($param['structure_parent_id']);
		$oStructure->show = Core_Type_Conversion::toInt($param['structure_show']);
		$oStructure->name = Core_Type_Conversion::toStr($param['structure_menu_name']);
		$oStructure->seo_title = Core_Type_Conversion::toStr($param['structure_title']);
		$oStructure->seo_description = Core_Type_Conversion::toStr($param['structure_description']);
		$oStructure->seo_keywords = Core_Type_Conversion::toStr($param['structure_keywords']);
		$oStructure->url = Core_Type_Conversion::toStr($param['structure_external_link']);
		$oStructure->sorting = Core_Type_Conversion::toInt($param['structure_order']);

		$oStructure->path = Core_Type_Conversion::toStr($param['structure_path_name']);

		$oStructureSearch = Core_Entity::factory('Structure');
		$oStructureSearch
			->queryBuilder()
			->clear()
			->where('parent_id', '=', Core_Type_Conversion::toInt($param['structure_parent_id']))
			->where('path', '=', Core_Type_Conversion::toStr($param['structure_path_name']))
			->where('site_id', '=', Core_Type_Conversion::toInt($param['site_id']))
			->where('id', '!=', Core_Type_Conversion::toInt($param['structure_id']))
			->limit(1);

		$oStructureSearch->find();

		// Если существует узел с таким путем, сгенерируем ему другое имя
		if (!is_null($oStructureSearch->id))
		{
			$oStructure->path = Core_Guid::get();
		}

		$oStructure->type = Core_Type_Conversion::toInt($param['structure_type']);

		// Проверяем, если это страница, то templates_id = ''
		if ($oStructure->type == 0)
		{
			$oStructure->template_id = 0;
		}
		else
		{
			$oStructure->template_id = Core_Type_Conversion::toInt($param['templates_id']);
		}

		if (isset($param['structure_access']))
		{
			$oStructure->siteuser_group_id = Core_Type_Conversion::toInt($param['structure_access']);
		}

		$oStructure->https = Core_Type_Conversion::toInt($param['structure_access_protocol']);

		if (isset($param['structure_activity']))
		{
			$oStructure->active = Core_Type_Conversion::toInt($param['structure_activity']);
		}

		if (isset($param['structure_allow_indexation']))
		{
			$oStructure->indexing = Core_Type_Conversion::toInt($param['structure_allow_indexation']);
		}

		if (is_null($param['structure_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oStructure->user_id = $param['users_id'];
		}

		if (isset($param['structure_change_frequency']))
		{
			$oStructure->changefreq = Core_Type_Conversion::toInt($param['structure_change_frequency']);
		}

		if (isset($param['structure_priority']))
		{
			$oStructure->priority = Core_Type_Conversion::toFloat($param['structure_priority']);
		}

		$lib_id = Core_Type_Conversion::toInt($param['lib_id']);

		// Редактируем узел структуры
		if ($param['structure_id'])
		{
			if ($oStructure->id)
			{
				// Удаляем информацию о проиндексированном информационном элементе
				// до обновления, т.к. при изменении группы элементы мы не сможем удалить
				// его предыдущие данные
				if (class_exists('Search'))
				{
					$this->IndexationStructure(0, 1, array('structure_id' => $oStructure->id));
				}
			}

			if ($structure_parent_id != -1)
			{
				$oStructure->save();
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			$oStructure->save();
		}

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'STRUCTURE';
			$cache->DeleteCacheItem($cache_name, $oStructure->id);
		}

		if (isset($this->StructureMass[$oStructure->id]))
		{
			unset($this->StructureMass[$oStructure->id]);
		}

		// Добавляем индексирование структуры
		if ($oStructure->indexing == 1 && class_exists('Search'))
		{
			$result = $this->IndexationStructure(0, 1, array('structure_id' => $oStructure->id));
		}

		// Дин. страницы
		try
		{
			$oStructure->saveStructureFile(Core_Type_Conversion::toStr($param['module']));
		}
		catch (Exception $e)
		{
			Core_Message::show($e->getMessage(), 'error');
			Core_Message::show(Core::_('Structure.file_write_error_message', $file_name), 'error');
		}

		// Настройки дин. страницы
		try
		{
			$oStructure->saveStructureConfigFile(Core_Type_Conversion::toStr($param['module_config']));
		}
		catch (Exception $e)
		{
			Core_Message::show($e->getMessage(), 'error');
			Core_Message::show(Core::_('Structure.file_write_error_message', $file_name), 'error');
		}

		return $oStructure->id;
	}

	/**
	* Устаревший метод удаления страницы. Рекомендуется использовать $this->DeleteStructure($structure_id);
	*
	* @param int $structure_id идентификатор страницы
	*
	* @return boolean истина при удачном удалении, ложь - в обратном случае
	* @see DeleteStructure()
	*/
	function delete_page($structure_id)
	{
		return $this->DeleteStructure($structure_id);
	}

	/**
	* Метод для отображения структуры в клиентском разделе сайта
	*
	* @param int $menu_id ID меню для отображения
	* @param string $xsl имя XSL шаблона для отображения
	* @param array $param содержит дополнительные параметры
	* - $param['parent_id'] - ID родительского узла
	* - $param['cache'] - разрешение кэширования, по умолчанию true
	* - $param['show_groups'] - отображать ли группы элементов инфосистемы, по умолчанию FALSE
	* - $param['show_items'] - отображать ли элементы инфосистемы, может принимать значение true, FALSE или массив идентификаторов элементов, подлежащих отображению. по умолчанию FALSE
	* - $param['level'] - уровень, до которого выбирать узлы в дереве структуры. по умолчанию равен -1.
	* - $param['show_information_systems'] - отображать ли элементы указанных инфосистемы(массив)
	* - $param['do_not_show_information_systems'] - отображать ли элементы указанных инфосистемы(массив)
	* - $param['hidden_groups'] - массив групп инфосистем, скрытых для показа в текущем меню
	* - $param['hidden_groups_shop'] - массив групп товаров, скрытых для показа в текущем меню
	* - $param['site_id'] - ID сайта, для которого отображается структура, по умолчанию равен текущему сайту
	* - $param['show_shop_groups'] - отображать ли группы товаров, по умолчанию FALSE
	* - $param['show_shop_items'] - отображать ли товары, может принимать значение true, FALSE или массив идентификаторов товаров, подлежащих отображению. по умолчанию FALSE
	* - $param['show_shops'] - массив идентификаторов магазинов, подлежащих отображению
	* - $param['do_not_show_shops'] - массив идентификаторов магазинов, исключаемых из отображения
	* - $param['xml_show_structure_property'] разрешает указание в XML значений свойств узлов структуры, по умолчанию true
	* - $param['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	*
	* @param array $external_propertys многомерный массив дополнительных свойств для включения в исходный XML код
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $xsl = 'Картасайта';
	* $menu_id = FALSE;
	*
	* // Необязательные параметры
	* $param = array();
	* $param['parent_id'] = 0;
	*
	* // Внешние параметры для передачи в XML
	* $external_propertys = array();
	* $external_propertys['некий_внешний_параметр'] = "некое значение";
	*
	* $structure->ShowStructure($menu_id, $xsl, $param, $external_propertys);
	* ?>
	* </code>
	* @return boolean истину при успешном отображении меню
	*/
	function ShowStructure($menu_id, $xsl, $param = array (), $external_propertys = array ())
	{
		// обрабатывается ниже
		// $menu_id = intval($menu_id);

		// Определяем ID родителя
		if (!empty ($param['parent_id']))
		{
			$parent_id = intval($param['parent_id']);
		}
		else
		{
			$parent_id = 0;
		}

		// Определяем ID сайта для которого строится структура
		if (!empty ($param['site_id']))
		{
			$site_id = intval($param['site_id']);
		}
		else
		{
			$site_id = defined('CURRENT_SITE') ? CURRENT_SITE : FALSE;
		}

		// по умолчанию кэширование - включено
		if (!isset($param['cache']))
		{
			$param['cache'] = TRUE;
		}

		// по умолчанию "отображать ли отображать ли группы элементов инфосистем" - выключено
		if (!isset($param['show_groups']))
		{
			$param['show_groups'] = FALSE;
		}

		// по умолчанию "отображать ли элементы инфосистемы" - выключено
		if (!isset($param['show_items']))
		{
			$param['show_items'] = FALSE;
		}

		// по умолчанию "отображать ли группы товаров" - выключено
		if (!isset($param['show_shop_groups']))
		{
			$param['show_shop_groups'] = FALSE;
		}

		// по умолчанию "отображать ли товары" - выключено
		if (!isset($param['show_shop_items']))
		{
			$param['show_shop_items'] = FALSE;
		}

		if (!isset($param['xml_show_structure_property']))
		{
			$param['xml_show_structure_property'] = TRUE;
		}

		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();

			if ($site_user_id > 0)
			{
				$access = $SiteUsers->GetGroupsForUser($site_user_id);
			}
		}
		else
		{
			$site_user_id = 0;
		}

		if (!isset($access))
		{
			$access[] = 0;
		}

		// Проверка на кэширование
		if (isset($param['cache']) && $param['cache'])
		{
			$kernel = & singleton('kernel');

			$cache_element = $menu_id.'_'.$xsl.'_'.$parent_id.'_'.CURRENT_STRUCTURE_ID.' '.$kernel->implode_array($param)." ".$kernel->implode_array($external_propertys)." ".$kernel->implode_array($access);

			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOW_STRUCTURE_XML';

				if (($in_cache = $cache->GetCacheContent($cache_element, $cache_name)) && $in_cache)
				{
					echo $in_cache['value'];
					return TRUE;
				}
			}
		}

		$this->XmlDataFromGenXml4StructureLevelMass = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$this->XmlDataFromGenXml4StructureLevelMass .= '<document>'."\n";

		// Добавляем информацию о сайте
		$site = & singleton('site');
		$this->XmlDataFromGenXml4StructureLevelMass .= $site->GetXmlForSite(CURRENT_SITE);

		// Вносим в XML дополнительные теги из массива дополнительных параметров
		$ExternalXml = new ExternalXml;
		$this->XmlDataFromGenXml4StructureLevelMass .= $ExternalXml->GenXml($external_propertys);

		/* Вносим внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа*/
		if (isset($param['external_xml']))
		{
			$this->XmlDataFromGenXml4StructureLevelMass .= $param['external_xml'];
		}

		if (isset($param['level']))
		{
			$level = intval($param['level']);
		}
		else
		{
			$level = -1;
		}

		// Получаем массив инф. систем
		$this->MassIS = array();
		$this->MassIG = array();
		$this->MassII = array();

		if ($param['show_items'] || $param['show_groups'])
		{
			$oInformationsystem = Core_Entity::factory('Informationsystem');
			$oInformationsystem
				->queryBuilder()
				->where('site_id', '=', $site_id);

			// Если передан параметр с указанием списка отображаемых инфосистем и количество элементов больше нуля,
			// тогда ограничиваем список инфосистем для вывода
			if (isset($param['show_information_systems'])
				&& count(Core_Type_Conversion::toArray($param['show_information_systems'])) > 0)
			{
				$param['show_information_systems'] = Core_Array::toInt($param['show_information_systems']);

				$oInformationsystem
					->queryBuilder()
					->where('id', 'IN', $param['show_information_systems']);

				/*$in_show_inf_sys = implode(',', $param['show_information_systems']);
				$SQL_SHOW_INF_SYS = ' AND information_systems_id IN('.$in_show_inf_sys.')';*/
			}

			// Если передан параметр с указанием списка НЕотображаемых инфосистем и количество элементов больше нуля,
			// тогда ограничиваем список инфосистем для вывода
			if (isset($param['do_not_show_information_systems'])
				&& count(Core_Type_Conversion::toArray($param['do_not_show_information_systems'])) > 0)
			{
				$param['do_not_show_information_systems'] = Core_Array::toInt($param['do_not_show_information_systems']);

				$oInformationsystem
					->queryBuilder()
					->where('id', 'NOT IN', $param['do_not_show_information_systems']);

				/*$in_show_inf_sys = implode(',', $param['do_not_show_information_systems']);
				$SQL_DO_NOT_SHOW_INF_SYS = ' AND information_systems_id NOT IN('.$in_show_inf_sys.')';*/
			}

			/*$query = "SELECT * FROM informationsystems AS t1
			{$QUERY_MENU} AND t1.site_id = '{$site_id}'
			{$SQL_SHOW_INF_SYS}
			{$SQL_DO_NOT_SHOW_INF_SYS}";*/
			$aInformationsystems = $oInformationsystem->findAll();

			$InformatioSystem = & singleton('InformationSystem');

			//while ($row = mysql_fetch_assoc($rows))
			foreach ($aInformationsystems as $oInformationsystem)
			{
				$this->MassIS[$oInformationsystem->structure_id] = $oInformationsystem->id;

				// Сохраняем в массив ИС полученные данные
				$InformatioSystem->cache_InformationSystem[$oInformationsystem->id]
					= $InformatioSystem->getArrayInformationsystem($oInformationsystem);
			}

			// -------------------------

			// Получаем массив инф. групп
			if ($param['show_groups'])
			{
				$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group');
				$oInformationsystem_Group
					->queryBuilder()
					->where('active', '=', 1)
					->orderBy('informationsystem_id')
					->orderBy('parent_id')
					->orderBy('sorting')
					->orderBy('name');

				if (isset($param['show_information_systems'])
					&& count(Core_Type_Conversion::toArray($param['show_information_systems'])) > 0)
				{
					$oInformationsystem
						->queryBuilder()
						->where('informationsystem_id', 'IN', $param['show_information_systems']);
				}

				if (is_array($param['show_groups']) && count($param['show_groups']) > 0)
				{
					$param['show_groups'] = Core_Array::toInt($param['show_groups']);

					$aTmpInfSysGroups = array(0);

					foreach($param['show_groups'] as $group_id)
					{
						if (!in_array($group_id, $aTmpInfSysGroups))
						{
							// Получаем пути для каждой группы от нее до корня
							$aGroupsToRoot = $InformatioSystem->GetInformationGroupsPathArray($group_id, FALSE);

							if (is_array($aGroupsToRoot) && count($aGroupsToRoot) > 0)
							{
								foreach ($aGroupsToRoot as $aGroupRow)
								{
									$aTmpInfSysGroups[] = $aGroupRow['information_groups_id'];
								}
							}
						}
					}

					$oInformationsystem_Group
						->queryBuilder()
						->where('id', 'IN', $aTmpInfSysGroups);

					//$sQueryInfSysGroups = "AND informationsystem_groups.`id` IN (" . implode(',', $aTmpInfSysGroups) . ")";
				}

				/* $query = "SELECT * FROM `informationsystem_groups`
				WHERE `information_groups_activity` = 1
				{$SQL_SHOW_INF_SYS} {$sQueryInfSysGroups}
				ORDER BY information_systems_id, information_groups_parent_id,
				information_groups_order, information_groups_name"; */

				//while ($row = mysql_fetch_assoc($rows))

				$aInformationsystem_Groups = $oInformationsystem_Group->findAll();

				foreach ($aInformationsystem_Groups as $oInformationsystem_Group)
				{
					$row = $InformatioSystem->getArrayInformationsystemGroup($oInformationsystem_Group);

					// Проверяем права доступа текущего зарегистрированного пользователя к информационной группе
					if ($InformatioSystem->IssetAccessForInformationSystemGroup(
						$site_user_id, $oInformationsystem_Group->id, $oInformationsystem_Group->informationsystem_id, $row
					))
					{
						$this->MassIG[$oInformationsystem_Group->informationsystem_id][$oInformationsystem_Group->parent_id][] = $oInformationsystem_Group->id;

						// Сохраняем в кэше для ИС
						$InformatioSystem->MasGroup[$oInformationsystem_Group->id] = $row;
					}
				}
			}

			if ($param['show_items'])
			{
				foreach ($this->MassIS AS $key => $information_systems_id)
				{
					$current_date = date('Y-m-d H:i:s');

					$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item');
					$oInformationsystem_Item
						->queryBuilder()
						->where('informationsystem_id', '=', $information_systems_id)
						->where('start_datetime', '<=', $current_date)
						->open()
						->where('end_datetime', '>=', $current_date)
						->setOr()
						->where('end_datetime', '=', '0000-00-00 00:00:00')
						->close()
						->where('active', '=', 1)
						->where('shortcut_id', '=', 0)
					;

					// Определяем поле сортировки информационных элементов
					$oInformationsystem = Core_Entity::factory('Informationsystem', $information_systems_id);
					switch ($oInformationsystem->items_sorting_field)
					{
						case 0 :
						default :
							$order_field = 'datetime';
						break;
						case 1 :
							$order_field = 'name';
						break;
						case 2 :
							$order_field = 'sorting';
						break;
					}

					switch ($oInformationsystem->items_sorting_direction)
					{
						case 0 :
						default :
								$order_type = 'ASC';
							break;
						case 1 :
								$order_type = 'DESC';
							break;
					}

					$oInformationsystem_Item
						->queryBuilder()
						->orderBy($order_field, $order_type);

					// Если явно был передан массив идентификаторов для отображения
					if (is_array($param['show_items']) && count($param['show_items']) > 0)
					{
						$param['show_items'] = Core_Array::toInt($param['show_items']);

						$oInformationsystem_Item
							->queryBuilder()
							->where('id', 'IN', $param['show_items']);

						//$SQL_SELECT_INF_ITEM = ' AND informationsystem_items.information_items_id IN('.implode(',', $param['show_items']).')';
					}

					/*$query_select_information_system = "SELECT *
					FROM informationsystem_items
					WHERE information_systems_id = ".$information_systems_id."
					AND informationsystem_items.information_items_putoff_date <= '$current_date'
					AND(informationsystem_items.information_items_putend_date>='$current_date'
					OR informationsystem_items.information_items_putend_date = '0000-00-00 00:00:00')
					AND informationsystem_items.information_items_status = '1'
					AND informationsystem_items.information_items_shortcut_id = '0'
					{$SQL_SELECT_INF_ITEM}
					ORDER BY $order_field $order_type";
					*/

					$aInformationsystem_Items = $oInformationsystem_Item->findAll();

					//while ($row = mysql_fetch_assoc($rows))
					foreach ($aInformationsystem_Items as $oInformationsystem_Item)
					{
						$row = $InformatioSystem->getArrayInformationsystemItem($oInformationsystem_Item);

						// Проверяем права доступа к элементу
						if ($InformatioSystem->GetAccessItem($site_user_id, $oInformationsystem_Item->id, $row))
						{
							$this->MassII
								[$oInformationsystem_Item->informationsystem_id]
								[$oInformationsystem_Item->informationsystem_group_id]
								[] = $oInformationsystem_Item->id;

							$InformatioSystem->ItemMass[$oInformationsystem_Item->id] = $row;
						}
					}
				}
			}
		}

		$this->MassShopShops = array ();
		$this->MassShopGroups = array ();
		$this->MassShopItems = array ();

		if (class_exists('shop'))
		{
			$shop = & singleton('shop');

			if ($param['show_shop_groups'] || $param['show_shop_items'])
			{
				$oShop = Core_Entity::factory('Shop');
				$oShop->queryBuilder()
					->where('site_id', '=', $site_id);

				// Если передан параметр с указанием списка отображаемых магазинов и количество элементов больше нуля,
				// тогда ограничиваем список магазинов для вывода
				if (isset($param['show_shops']) && count(Core_Type_Conversion::toArray($param['show_shops'])) > 0)
				{
					$param['show_shops'] = Core_Array::toInt($param['show_shops']);

					$oShop->queryBuilder()
						->where('id', 'IN', $param['show_shops']);

					/*$in_show_shop_sys = implode(',', $param['show_shops']);
					$SQL_SHOW_SHOP = ' AND shop_shops_id IN('.$in_show_shop_sys.')';*/
				}

				// Если передан параметр с указанием списка НЕотображаемых магазинов и количество элементов больше нуля,
				// тогда ограничиваем список магазинов для вывода
				if (isset($param['do_not_show_shops']) && count(Core_Type_Conversion::toArray($param['do_not_show_shops'])) > 0)
				{
					$param['do_not_show_shops'] = Core_Array::toInt($param['do_not_show_shops']);

					$oShop->queryBuilder()
						->where('id', 'NOT IN', $param['do_not_show_shops']);

					/*$in_show_inf_sys = implode(',', $param['do_not_show_shops']);
					$SQL_DO_NOT_SHOW_SHOP = ' AND shop_shops_id NOT IN('.$in_show_inf_sys.')';*/
				}

				/*$query = "SELECT * FROM shop_shops_table AS t1
				{$QUERY_MENU} AND t1.site_id = '{$site_id}'
				{$SQL_SHOW_SHOP}
				{$SQL_DO_NOT_SHOW_SHOP}";
				*/

				$aShops = $oShop->findAll();
				foreach ($aShops as $oShop)
				{
					$this->MassShopShops[$oShop->structure_id] = $oShop->id;
					// Сохраняем в массив магазинов полученные данные
					$shop->g_array_shop[$oShop->id] = $shop->getArrayShop($oShop);
				}
			}

			if ($param['show_shop_groups'])
			{
				$oShop_Group = Core_Entity::factory('Shop_Group');
				$oShop_Group
					->queryBuilder()
					->where('active', '=', 1)
					->orderBy('shop_id')
					->orderBy('parent_id')
					->orderBy('sorting')
					->orderBy('name');

				if (isset($param['show_shops']) && count(Core_Type_Conversion::toArray($param['show_shops'])) > 0)
				{
					$oShop_Group
					->queryBuilder()
					->where('shop_id', 'IN', $param['show_shops']);
				}

				if (is_array($param['show_shop_groups']) && count($param['show_shop_groups']) > 0)
				{
					$param['show_shop_groups'] = Core_Array::toInt($param['show_shop_groups']);

					$aTmpShopGroups = array(0);

					foreach($param['show_shop_groups'] as $group_id)
					{
						if (!in_array($group_id, $aTmpShopGroups))
						{
							// Получаем пути для каждой группы от нее до корня
							$aGroupsToRoot = $shop->GetShopGroupsToRoot($group_id, FALSE);

							if (is_array($aGroupsToRoot) && count($aGroupsToRoot) > 0)
							{
								foreach ($aGroupsToRoot as $aGroupRow)
								{
									$aTmpShopGroups[] = $aGroupRow['shop_groups_id'];
								}
							}
						}
					}

					$oShop_Group
						->queryBuilder()
						->where('id', 'IN', $aTmpShopGroups);
				}

				/*$query = "SELECT * FROM `shop_groups_table`
				WHERE `shop_groups_activity` = 1
				{$SQL_SHOW_SHOP} {$sQueryShopGroups}
				ORDER BY shop_shops_id, shop_groups_parent_id, shop_groups_order";
				*/

				$aShop_Groups = $oShop_Group->findAll();

				//while ($row = mysql_fetch_assoc($rows))
				foreach ($aShop_Groups as $oShop_Group)
				{
					$row = $shop->getArrayShopGroup($oShop_Group);

					// Проверяем права доступа текущего зарегистрированного пользователя к группе
					$param_group_access = array(
						'site_users_id' => $site_user_id,
						'shop_group_id' => $oShop_Group->id,
						'shop_id' => $oShop_Group->shop_id,
						'shop_group_info' => $row
					);

					if ($shop->IssetAccessForShopGroup($param_group_access))
					{
						$this->MassShopGroups[$oShop_Group->shop_id][$oShop_Group->parent_id][] = $oShop_Group->id;

						// Сохраняем в кэше для магазинов
						$shop->MasGroup[$oShop_Group->id] = $row;
					}
				}

				if ($param['show_shop_items'])
				{
					foreach ($this->MassShopShops AS $key => $shop_shops_id)
					{
						$current_date = date('Y-m-d H:i:s');

						$oShop_Item = Core_Entity::factory('Shop_Item');
						$oShop_Item
							->queryBuilder()
							->where('shop_id', '=', $shop_shops_id)
							->where('start_datetime', '<=', $current_date)
							->open()
							->where('end_datetime', '>=', $current_date)
							->setOr()
							->where('end_datetime', '=', '0000-00-00 00:00:00')
							->close()
							->where('modification_id', '=', 0)
							->where('active', '=', 1)
							->where('shortcut_id', '=', 0)
						;

						/*$row_shops = $shop->GetShop($shop_shops_id, array (
							'cache_off' => true
						));*/

						$oShop = Core_Entity::factory('Shop', $shop_shops_id);

						// Определяем поле сортировки магазинов
						switch ($oShop->items_sorting_field)
						{
							case 0 :
							default :
								$order_field = 'datetime';
							break;
							case 1 :
								$order_field = 'name';
							break;
							case 2 :
								$order_field = 'sorting';
							break;
						}

						switch ($oShop->items_sorting_direction)
						{
							case 0 :
							default :
								$order_type = 'ASC';
							break;
							case 1 :
								$order_type = 'DESC';
							break;
						}

						$oShop_Item
							->queryBuilder()
							->orderBy($order_field, $order_type);

						// Если явно был передан массив идентификаторов для отображения
						if (is_array($param['show_shop_items']) && count($param['show_shop_items']) > 0)
						{
							$param['show_shop_items'] = Core_Array::toInt($param['show_shop_items']);

							$oShop_Item
								->queryBuilder()
								->where('id', 'IN', $param['show_shop_items']);

							/*$SQL_SELECT_INF_ITEM = ' AND shop_items_catalog_table.shop_items_catalog_item_id IN('.implode(',', $param['show_shop_items']).')';*/
						}

						/*$query_select_shop = "SELECT * FROM shop_items_catalog_table
						WHERE shop_shops_id = ".$shop_shops_id."
						AND shop_items_catalog_table.shop_items_catalog_putoff_date <= '$current_date'
						AND(shop_items_catalog_table.shop_items_catalog_putend_date>='$current_date'
						OR shop_items_catalog_table.shop_items_catalog_putend_date = '0000-00-00 00:00:00')
						AND shop_items_catalog_table.shop_items_catalog_modification_id = 0
						AND shop_items_catalog_table.shop_items_catalog_is_active = '1'
						AND shop_items_catalog_table.shop_items_catalog_shortcut_id = '0'
						{$SQL_SELECT_INF_ITEM}
						ORDER BY $order_field $order_type";
						*/

						$aShop_Items = $oShop_Item->findAll();

						foreach ($aShop_Items as $oShop_Item)
						{
							$row = $shop->getArrayShopItem($oShop_Item);

							// Проверяем права доступа к товару
							if ($shop->GetAccessShopItem($site_user_id, $oShop_Item->id, $row))
							{
								$this->MassShopItems[$oShop_Item->shop_id][$oShop_Item->shop_group_id][] = $oShop_Item->id;
								$shop->CacheGetItem[$oShop_Item->id] = $row;
							}
						}
					}
				}
			}
		}

		// Формируем массив полной структуры при заданных условиях
		$this->GetStructure(
			"&nbsp;", $site_id, $menu_id, $level,
			$parent_id, FALSE,
			array(
				'select_structure_property' => $param['xml_show_structure_property']
			)
		);

		// Очищаем XML для дерева структуры
		//$this->XmlDataFromGenXml4StructureLevelMass = '';

		// Результат дописывается в $this->XmlDataFromGenXml4StructureLevelMass
		$this->GenXml4StructureLevelMass($parent_id, $access, 0, $param);

		$this->XmlDataFromGenXml4StructureLevelMass .= '</document>';

		$xslt = & singleton('xsl');
		$result = $xslt->build($this->XmlDataFromGenXml4StructureLevelMass, $xsl);

		// Очищаем XML для дерева структуры
		$this->XmlDataFromGenXml4StructureLevelMass = '';

		// Проверка на кэширование
		if (isset($param['cache']) && $param['cache'])
		{
			if (class_exists('Cache'))
			{
				// Запись в кэш
				$cache->Insert($cache_element, $result, $cache_name);
			}
		}

		echo $result;

		return TRUE;
	}

	/**
	* Метод, возвращающий число свойств раздела сайта или FALSE, если свойства отсутствуют
	*
	* @param int $site_id идентификатор сайта
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Идентификатор сайта
	* $site_id = 1;
	*
	* $count = $structure->GetCountProperty4Structure($site_id);
	*
	* // Распечатаем результат
	* echo $count;
	* ?>
	* </code>
	* @return mixed число свойств раздела сайта или FALSE, если свойства отсутствуют
	*/
	function GetCountProperty4Structure($site_id)
	{
		$site_id = intval($site_id);

		if (isset($this->a_count_property[$site_id]))
		{
			return $this->a_count_property[$site_id];
		}

		$oStructure_Property_List = Core_Entity::factory('Structure_Property_List', $site_id);
		$this->a_count_property[$site_id] = count(
			$oStructure_Property_List->Properties->findAll()
		);

		return $this->a_count_property[$site_id];
	}

	/**
	* Внутренний метод генерации XML для отображения элементов и групп элементов инфосистем при отображении структуры. В своей работе использует массивы $this->MassIG, $this->MassII.
	*
	* @param int $inf_sys_id идентификатор информационной системы
	* @param int $menu_id идентификатор меню
	* @param int $level текущий уровень вложенности
	* @param int $inf_group_id идентификатор группы элементов информационной системы
	* @param string $full_path полный путь к текущей группе элементов информационной системы
	* @param array $param ассоциативный массив включающий следующие параметры:
	* $param['show_groups'] - отображать ли группы элементов инфосистемы
	* $param['show_items'] - отображать ли элементы инфосистемы
	* $param['hidden_groups'] - массив групп информационных систем, скрытых для показа в текущем меню
	* @return string XML для отображения элементов и групп элементов инфосистем при отображении структуры
	*/
	function GenXML4InfSys($inf_sys_id, $menu_id, $level, $inf_group_id, $full_path, $param = array ())
	{
		$inf_sys_id = intval($inf_sys_id);
		$inf_group_id = intval($inf_group_id);
		$menu_id = intval($menu_id);

		$index = $inf_sys_id . '_' . $inf_group_id;

		/* Из структуры создаем класс инфосистемы*/
		$InformationSystem = & singleton('InformationSystem');
		$row_is = $InformationSystem->GetInformationSystem($inf_sys_id);

		if (!$row_is)
		{
			return FALSE;
		}

		$row_structure = $this->GetStructureItem($row_is['structure_id']);

		if (!$row_structure)
		{
			return FALSE;
		}

		$xmlData = '';

		if (isset($this->MassIG[$inf_sys_id][$inf_group_id])
			&& is_array($this->MassIG[$inf_sys_id][$inf_group_id])
			&& isset($param['show_groups']) && $param['show_groups'])
		{
			foreach ($this->MassIG[$inf_sys_id][$inf_group_id] AS $key => $value)
			{
				$information_group_row = $InformationSystem->GetInformationGroup($value);

				$xmlData .= '<structure id="group_'.$information_group_row['information_groups_id'].'" menu_id="'.$menu_id.'">'."\n";
				$xmlData .= '<current_structure_id>'.CURRENT_STRUCTURE_ID.'</current_structure_id>'."\n";
				$xmlData .= '<name>'.str_for_xml($information_group_row['information_groups_name']).'</name>'."\n";
				$xmlData .= '<show>'.str_for_xml($information_group_row['information_groups_activity']).'</show>'."\n";

				// Ссылка показывается только при связи раздела структуры с каким либо документом
				// либо тип динамическая страница
				$xmlData .= '<show_link>'. ((!isset($param['hidden_groups'])
				|| isset($param['hidden_groups'])
				&& !isset($param['hidden_groups'][$information_group_row['information_groups_id']])
				&& !in_array($information_group_row['information_groups_id'], $param['hidden_groups'])
				)
				? '1' : '0').'</show_link>'."\n";

				$xmlData .= '<level>'.$level.'</level>'."\n";
				$xmlData .= '<id_parent>'.$information_group_row['information_groups_parent_id'].'</id_parent>'."\n";
				$xmlData .= '<is_external_link>0</is_external_link>'."\n";
				$xmlData .= '<external_link></external_link>'."\n";
				$current_path = $full_path . rawurlencode($information_group_row['information_groups_path']) . '/';
				$xmlData .= '<link>'.str_for_xml($current_path).'</link>'."\n";

				// GoogleSiteMap для узла структуры
				$xmlData .= '<structure_change_frequency>'.str_for_xml($row_structure['structure_change_frequency']).'</structure_change_frequency>'."\n";
				$xmlData .= '<structure_priority>'.str_for_xml($row_structure['structure_priority']).'</structure_priority>'."\n";

				$information_group_dir = $InformationSystem->GetInformationGroupDir($information_group_row['information_groups_id']);

				if (!empty ($information_group_row['information_groups_image']))
				{
					$xmlData .= '<image>/'.$information_group_dir.$information_group_row['information_groups_image'].'</image>'."\n";
				}

				if (!empty ($information_group_row['information_groups_small_image']))
				{
					$xmlData .= '<small_image>/'.$information_group_dir.$information_group_row['information_groups_small_image'].'</small_image>'."\n";
				}

				if (!isset($param['hidden_groups'])
				|| !isset($param['hidden_groups'][$information_group_row['information_groups_id']])
				&& !in_array($information_group_row['information_groups_id'], $param['hidden_groups'])
				)
				{
					$xmlData .= $this->GenXML4InfSys($inf_sys_id, $menu_id, $level + 1,
					$information_group_row['information_groups_id'],
					$current_path, $param);
				}
				$xmlData .= '</structure>'."\n";
			}
		}

		/* Если отображать инфоэлементы в структуре*/
		if (isset($this->MassII[$inf_sys_id][$inf_group_id])
		&& isset($param['show_items']) && $param['show_items'])
		{
			foreach ($this->MassII[$inf_sys_id][$inf_group_id] AS $key => $information_items_id)
			{
				$information_item_row = $InformationSystem->GetInformationSystemItem($information_items_id);

				// Формируем XML дерево
				$xmlData .= '<structure id="item_'.$information_item_row['information_items_id'].'" menu_id="'.$menu_id.'">'."\n";
				$xmlData .= '<current_structure_id>'.CURRENT_STRUCTURE_ID.'</current_structure_id>'."\n";
				$xmlData .= '<name>'.str_for_xml($information_item_row['information_items_name']).'</name>'."\n";
				$xmlData .= '<seo_title>'.str_for_xml($information_item_row['information_items_seo_title']).'</seo_title>'."\n";
				$xmlData .= '<seo_description>'.str_for_xml($information_item_row['information_items_seo_description']).'</seo_description>'."\n";
				$xmlData .= '<seo_keywords>'.str_for_xml($information_item_row['information_items_seo_keywords']).'</seo_keywords>'."\n";
				$xmlData .= '<show>'.str_for_xml($information_item_row['information_items_status']).'</show>'."\n";

				// Ссылка показывается только при связи раздела структуры с каким либо документом
				// либо тип динамическая страница
				$xmlData .= '<show_link>'.'1'.'</show_link>'."\n";
				$xmlData .= '<level>'.$level.'</level>'."\n";
				$xmlData .= '<id_parent>group_'.$information_item_row['information_groups_id'].'</id_parent>'."\n";

				// Внешняя ссылка есть, если значение внешней ссылки не пустой
				$xmlData .= '<is_external_link>0</is_external_link>'."\n";

				$xmlData .= '<external_link></external_link>'."\n";
				$xmlData .= '<link>'.str_for_xml($full_path. ((!empty ($information_item_row['information_items_url']))
				? rawurlencode($information_item_row['information_items_url'])
				: $information_item_row['information_items_id']).'/').'</link>'."\n";

				// GoogleSiteMap для узла структуры
				$xmlData .= '<structure_change_frequency>'.str_for_xml($row_structure['structure_change_frequency']).'</structure_change_frequency>'."\n";
				$xmlData .= '<structure_priority>'.str_for_xml($row_structure['structure_priority']).'</structure_priority>'."\n";

				// Получаем путь к папке информационного элемента
				$item_dir = $InformationSystem->GetInformationItemDir($information_items_id);

				if (!empty ($information_item_row['information_items_image']))
				{
					$xmlData .= '<image width="'.$information_item_row['information_items_image_width'].'" height="'.$information_item_row['information_items_image_height'].'" >/'.$item_dir.$information_item_row['information_items_image'].'</image>'."\n";
				}

				if (!empty ($information_item_row['information_items_small_image']))
				{
					$xmlData .= '<small_image width="'.$information_item_row['information_items_small_image_width'].'" height="'.$information_item_row['information_items_small_image_height'].'">/'.$item_dir.$information_item_row['information_items_small_image'].'</small_image>'."\n";
				}

				$xmlData .= '</structure>'."\n";
			}
		}
		return $xmlData;
	}

	/**
	* Внутренний метод построения XML для групп товаров и товаров интернет-магазина при отображении структуры. Использует в своей работе $this->MassShopGroups, $this->MassShopItems
	*
	* @param int $shop_shops_id
	* @param int $menu_id
	* @param int $level
	* @param int $shop_groups_id
	* @param string $full_path
	* @param array $param
	* @return string XML для отображения элементов и групп элементов инфосистем при отображении структуры
	*/
	function GenXML4Shop($shop_shops_id, $menu_id, $level, $shop_groups_id, $full_path, $param = array ())
	{
		$shop_shops_id = intval($shop_shops_id);
		$shop_groups_id = intval($shop_groups_id);
		$menu_id = intval($menu_id);

		$index = $shop_shops_id.'_'.$shop_groups_id;

		$shop = & singleton('shop');
		$row_shop = $shop->GetShop($shop_shops_id);

		if (!$row_shop)
		{
			return FALSE;
		}

		$row_structure = $this->GetStructureItem($row_shop['structure_id']);

		if (!$row_structure)
		{
			return FALSE;
		}

		$xmlData = '';

		if (isset($this->MassShopGroups[$shop_shops_id][$shop_groups_id])
		&& is_array($this->MassShopGroups[$shop_shops_id][$shop_groups_id])
		&& isset($param['show_shop_groups']) && $param['show_shop_groups'])
		{
			foreach ($this->MassShopGroups[$shop_shops_id][$shop_groups_id] AS $key => $value)
			{
				// Получаем данные о группе
				$shop_group_row = $shop->GetGroup($value);
				// 000036373
				//$shop_group_row = $shop->GetGroup($value, array ('cache_off' => true));

				if (!isset($param['hidden_groups_shop'])
				|| !isset($param['hidden_groups_shop'][$shop_group_row['shop_groups_id']])
				&& !in_array($shop_group_row['shop_groups_id'], $param['hidden_groups_shop']))
				{
					$xmlData .= '<structure id="group_'.$shop_group_row['shop_groups_id'].'" menu_id="'.$menu_id.'">'."\n";
					$xmlData .= '<current_structure_id>'.CURRENT_STRUCTURE_ID.'</current_structure_id>'."\n";
					$xmlData .= '<name>'.str_for_xml($shop_group_row['shop_groups_name']).'</name>'."\n";
					$xmlData .= '<show>'.str_for_xml($shop_group_row['shop_groups_activity']).'</show>'."\n";

					$xmlData .= '<show_link>'. ((!isset($param['hidden_groups_shop'])
					|| isset($param['hidden_groups_shop'])
					&& !isset($param['hidden_groups_shop'][$shop_group_row['shop_groups_id']])
					&& !in_array($shop_group_row['shop_groups_id'], $param['hidden_groups_shop']))
					? '1' : '0').'</show_link>'."\n";

					$xmlData .= '<level>'.$level.'</level>'."\n";
					$xmlData .= '<id_parent>'.$shop_group_row['shop_groups_parent_id'].'</id_parent>'."\n";
					$xmlData .= '<is_external_link>0</is_external_link>'."\n";
					$xmlData .= '<external_link></external_link>'."\n";

					$current_path = $full_path . rawurlencode($shop_group_row['shop_groups_path']).'/';
					$xmlData .= '<link>'.str_for_xml($current_path).'</link>'."\n";

					$xmlData .= '<structure_change_frequency>'.str_for_xml($row_structure['structure_change_frequency']).'</structure_change_frequency>'."\n";
					$xmlData .= '<structure_priority>'.str_for_xml($row_structure['structure_priority']).'</structure_priority>'."\n";

					$uploaddir = $shop->GetGroupDir($shop_group_row['shop_groups_id']);

					if (!empty ($shop_group_row['shop_groups_image']))
					{
						$xmlData .= '<image width="'.$shop_group_row['shop_groups_big_image_width'].'" height="'.$shop_group_row['shop_groups_big_image_height'].'">/'.$uploaddir.$shop_group_row['shop_groups_image'].'</image>'."\n";
					}

					if (!empty ($shop_group_row['shop_groups_small_image']))
					{
						$xmlData .= '<small_image width="'.$shop_group_row['shop_groups_small_image_width'].'" height="'.$shop_group_row['shop_groups_small_image_height'].'">/'.$uploaddir.$shop_group_row['shop_groups_small_image'].'</small_image>'."\n";
					}

					// Рекурсивный вызов
					$xmlData .= $this->GenXML4Shop($shop_shops_id, $menu_id, $level + 1, $shop_group_row['shop_groups_id'], $current_path, $param);

					$xmlData .= '</structure>'."\n";
				}
			}
		}

		// Если отображать товары в структуре
		if (isset($this->MassShopItems[$shop_shops_id][$shop_groups_id])
		&& isset($param['show_shop_items']) && ($param['show_shop_items']))
		{
			foreach ($this->MassShopItems[$shop_shops_id][$shop_groups_id] as $key => $shop_items_catalog_item_id)
			{
				$shop_items_catalog_item_row = $shop->GetItem($shop_items_catalog_item_id);

				$xmlData .= '<structure id="item_'.$shop_items_catalog_item_row['shop_items_catalog_item_id'].'" menu_id="'.$menu_id.'">'."\n";
				$xmlData .= '<current_structure_id>'.CURRENT_STRUCTURE_ID.'</current_structure_id>'."\n";
				$xmlData .= '<name>'.str_for_xml($shop_items_catalog_item_row['shop_items_catalog_name']).'</name>'."\n";
				$xmlData .= '<seo_title>'.str_for_xml($shop_items_catalog_item_row['shop_items_catalog_seo_title']).'</seo_title>'."\n";
				$xmlData .= '<seo_description>'.str_for_xml($shop_items_catalog_item_row['shop_items_catalog_seo_description']).'</seo_description>'."\n";
				$xmlData .= '<seo_keywords>'.str_for_xml($shop_items_catalog_item_row['shop_items_catalog_seo_keywords']).'</seo_keywords>'."\n";

				$xmlData .= '<show>'.str_for_xml($shop_items_catalog_item_row['shop_items_catalog_is_active']).'</show>'."\n";
				$xmlData .= '<show_link>'.'1'.'</show_link>'."\n";
				$xmlData .= '<level>'.$level.'</level>'."\n";
				$xmlData .= '<id_parent>group_'.$shop_items_catalog_item_row['shop_groups_id'].'</id_parent>'."\n";
				$xmlData .= '<is_external_link>0</is_external_link>'."\n";
				$xmlData .= '<external_link></external_link>'."\n";
				$xmlData .= '<link>'.str_for_xml($full_path . (
					!empty($shop_items_catalog_item_row['shop_items_catalog_path'])
						? rawurlencode($shop_items_catalog_item_row['shop_items_catalog_path'])
						: $shop_items_catalog_item_row['shop_items_catalog_item_id']
					) . '/').'</link>'."\n";

				$xmlData .= '<structure_change_frequency>'.str_for_xml($row_structure['structure_change_frequency']).'</structure_change_frequency>'."\n";
				$xmlData .= '<structure_priority>'.str_for_xml($row_structure['structure_priority']).'</structure_priority>'."\n";

				$uploaddir = $shop->GetItemDir($shop_items_catalog_item_id);

				if (!empty ($shop_items_catalog_item_row['shop_items_catalog_image']))
				{
					$xmlData .= '<image width="'.$shop_items_catalog_item_row['shop_items_catalog_big_image_width'].'" height="'.$shop_items_catalog_item_row['shop_items_catalog_big_image_height'].'">/'.$uploaddir.$shop_items_catalog_item_row['shop_items_catalog_image'].'</image>'."\n";
				}

				if (!empty ($shop_items_catalog_item_row['shop_items_catalog_small_image']))
				{
					$xmlData .= '<small_image width="'.$shop_items_catalog_item_row['shop_items_catalog_small_image_width'].'" height="'.$shop_items_catalog_item_row['shop_items_catalog_small_image_height'].'">/'.$uploaddir.$shop_items_catalog_item_row['shop_items_catalog_small_image'].'</small_image>'."\n";
				}

				$xmlData .= '</structure>'."\n";
			}
		}

		return $xmlData;
	}

	/**
	* Метод возвращает заполненные св-ва узлов структуры
	*
	*(также метод заполняет значение кэша $this->cache_structure_property)
	*
	* @param array $propertylist массив ID узлов структуры, например $propertylist[] = 80;
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $propertylist = array();
	* $propertylist[] = 80;
	*
	* $row = $structure->GetStructureProperty($propertylist);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	* @return array массив свойств элемента
	*/
	function GetStructureProperty($propertylist, $site_id = FALSE)
	{
		// Формируем IN со значениями свойств структуры
		$count = count($propertylist);

		// Цикл по узлам структуры, загрузка всех значений связанных свойств
		if ($site_id)
		{
			$oStructure_Property_List = Core_Entity::factory('Structure_Property_List', $site_id);
			$aProperties = $oStructure_Property_List->Properties->findAll();

			foreach ($aProperties as $oProperty)
			{
				$oProperty->loadAllValues();
			}
		}

		$returnmass = array ();

		// если были переданы ID
		if (is_array($propertylist) && $count > 0)
		{
			// Меняем местами значения и ключи массива
    		$aTmp = array_flip($propertylist);

    		// Вычислить пересечение массивов, сравнивая ключи
    		$aTmpIntersect = array_intersect_key($aTmp, $this->cache_structure_property);

    		if (count($aTmpIntersect) != count($aTmp))
    		{
    			foreach ($propertylist as $structure_id)
				{
					$aValues = Core_Entity::factory('Structure', $structure_id)->getPropertyValues();

					foreach ($aValues as $oValue)
					{
						$returnmass[$structure_id][$oValue->property_id] =
							$this->cache_structure_property[$structure_id][$oValue->property_id] =
							$this->getArrayPropertyValue($oValue);
					}
				}
			}
			else // Запрос был выполнен ранее и данные в кэше, извлекаем их
			{
				foreach ($propertylist as $structure_id)
				{
					$returnmass[$structure_id] = $this->cache_structure_property[$structure_id];
				}
			}

			return $returnmass;
		}
		else
		{
			return array();
		}
	}

	/**
	 * XML для узла структуры. Перед вызовом очистите $Structure->XmlDataFromGenXml4StructureLevelMass
	 *
	 * @param $structure_id идентификатор узла структуры
	 * @param $param
	 * - $param['level'] уровень вложенности, который необходимо генерировать
	 *
	 * @return string XML
	 */
	function GetStructureXml($structure_id, $param = array())
	{
		if (!isset($param['show_document_content']))
		{
			$param['show_document_content'] = FALSE;
		}

		if (!isset($param['xml_show_structure_property']))
		{
			$param['xml_show_structure_property'] = TRUE;
		}

		$level = Core_Type_Conversion::toInt($param['level']);

		$row = $this->GetStructureItem($structure_id);

		// получем право доступа к узлу структуры
		$structure_access = $this->GetStructureAccess($row['structure_id']);

		$access = array(0);

		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();

			if ($site_user_id > 0)
			{
				$access = $SiteUsers->GetGroupsForUser($site_user_id);
			}
		}
		else
		{
			$site_user_id = 0;
		}

		// если есть доступ к данному разделу и которые активны
		if (in_array($structure_access, $access) && $row['structure_activity'] == 1)
		{
			// Формируем XML дерево
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure id="'.$row['structure_id'].'" menu_id="'.$row['menu_id'].'">'."\n";

			$this->XmlDataFromGenXml4StructureLevelMass .= '<current_structure_id>'.CURRENT_STRUCTURE_ID.'</current_structure_id>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<name>'.str_for_xml($row['structure_menu_name']).'</name>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<show>'.Core_Type_Conversion::toInt($row['structure_show']).'</show>'."\n";

			// Ссылка показывается только при связи раздела структуры с каким либо документом, либо тип динамическая страница
			$this->XmlDataFromGenXml4StructureLevelMass .= '<show_link>'. (
				$row['documents_id'] == 0
				&& strlen(trim($row['structure_external_link'])) == 0
				/*&& $row['current_level'] != 0*/
				&& $row['structure_type'] == 0
					? '0'
					: '1'
			) . '</show_link>'."\n";

			$this->XmlDataFromGenXml4StructureLevelMass .= '<level>'.Core_Type_Conversion::toInt($row['current_level']).'</level>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<id_parent>'.Core_Type_Conversion::toInt($row['structure_parent_id']).'</id_parent>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_title>'.str_for_xml($row['structure_title']).'</structure_title>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_description>'.str_for_xml($row['structure_description']).'</structure_description>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_keywords>'.str_for_xml($row['structure_keywords']).'</structure_keywords>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_order>'.str_for_xml($row['structure_order']).'</structure_order>'."\n";

			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_path_name>'.str_for_xml($row['structure_path_name']).'</structure_path_name>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_type>'.str_for_xml($row['structure_type']).'</structure_type>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_access>'.str_for_xml($row['structure_access']).'</structure_access>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_allow_indexation>'.str_for_xml($row['structure_allow_indexation']).'</structure_allow_indexation>'."\n";

			// Внешняя ссылка есть, если значение внешней ссылки не пустой
			$this->XmlDataFromGenXml4StructureLevelMass .= '<is_external_link>'. ((mb_strlen((trim($row['structure_external_link']))) == 0) ? "0" : "1").'</is_external_link>'."\n";

			$this->XmlDataFromGenXml4StructureLevelMass .= '<external_link>'.str_for_xml($row['structure_external_link']).'</external_link>'."\n";

			if (!isset($row['full_path']))
			{
				// Полный путь к файлу
				$path = $this->GetStructurePath($row['structure_id'], 0);

				if (trim($path) != '/')
				{
					$path = '/' . $path;
				}

				$this->StructureMass[$row['structure_id']]['full_path'] = $path;
				$row['full_path'] = $path;
			}

			$this->XmlDataFromGenXml4StructureLevelMass .= '<link>'.str_for_xml($row['full_path']).'</link>'."\n";

			if ($param['show_document_content'])
			{
				$structure_external_link = trim($row["structure_external_link"]);

				// Проверяем, является ли даннная страница внешней ссылкой
				// $is_extrernal_link
				$is_extrernal_link = strlen($structure_external_link) != 0;

				//
				// Если тип - страница
				if ($row["structure_type"] == 0 && !$is_extrernal_link)
				{
					$documents_class = new documents();
					// Получаем ассоциативный массив с информацией о текущей версии документа
					$documents_version_row = $documents_class->GetCurrentDocumentVersion($row["documents_id"]);
					$document_version_path = $documents_class->GetDocumentVersionPath($documents_version_row['documents_version_id']);

					if (file_exists($document_version_path) && is_readable($document_version_path))
					{
						$this->XmlDataFromGenXml4StructureLevelMass .= '<content>'.str_for_xml(@file_get_contents($document_version_path)).'</content>'."\n";
					}
				}
			}

			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_change_frequency>'.str_for_xml($row['structure_change_frequency']).'</structure_change_frequency>'."\n";
			$this->XmlDataFromGenXml4StructureLevelMass .= '<structure_priority>'.str_for_xml($row['structure_priority']).'</structure_priority>'."\n";

			// Формируем массив подпунктов по двум готовым массивам и генерирем для него XML
			if (($this->level == -1 || $level < $this->level)
			&& isset($this->StructureParentMass[$row['structure_id']])
			&& is_array($this->StructureParentMass[$row['structure_id']]))
			{
				// Вызываем рекурсивно построение XML для подпунктов
				$this->GenXml4StructureLevelMass($row['structure_id'], $access, $level + 1, $param);
			}

			// для структуры сайта задано хотя бы одно свойство
			if ($param['xml_show_structure_property'] && $this->GetCountProperty4Structure($row['site_id']))
			{
				// Генерируем XML для списка свойств
				$this->XmlDataFromGenXml4StructureLevelMass .= $this->GetXml4StructurePropertys($row['structure_id']);
			}

			$this->XmlDataFromGenXml4StructureLevelMass .= '</structure>'."\n";
		}

		// Удаляем использованый элемент из массива для экономии памяти
		if (isset($this->StructureMass[$structure_id]))
		{
			unset ($this->StructureMass[$structure_id]);
		}

		return $this->XmlDataFromGenXml4StructureLevelMass;
	}

	/**
	* Внутренний метод генерации части XML-а для записей указанного уровня
	*
	* @param int $parent_id идентификатор родительского раздела
	* @param array $access массив с правами доступа
	* @param int $level текущий уровень, по умолчанию 0
	* @param array $param ассоциативный массив включающий следующие параметры:
	* - $param['show_groups'] - отображать ли группы элементов инфосистемы
	* - $param['show_items'] - отображать ли элементы инфосистемы
	* - $param['hidden_groups'] - массив групп информационных элементов, скрытых для показа в текущем меню
	* - $param['hidden_groups_shop'] - массив групп магазинов, скрытых для показа в текущем меню
	* - $param['show_shop_groups'] - отображать ли группы товаров
	* - $param['show_shop_items'] - отображать ли товары
	* - $param['hidden_shop_groups'] - массив групп, скрытых для показа в текущем меню
	* - $param['show_document_content'] - отображать содержимое статичной страницы, по умолчанию FALSE
	* - $param['xml_show_structure_property'] разрешает указание в XML значений свойств узлов структуры, по умолчанию true
	* @return string часть XML-а для записей указанного уровня<br>
	*/
	function GenXml4StructureLevelMass($parent_id, $access, $level = 0, $param = array ())
	{
		if (isset($this->StructureParentMass[$parent_id]))
		{
			// Получаем количество узлов для указанного родителя
			$count = count($this->StructureParentMass[$parent_id]);

			// Для GetStructureXml
			$param['level'] = $level;

			for ($i = 0; $i < $count; $i++)
			{
				$structure_id = $this->StructureParentMass[$parent_id][$i];
				$this->GetStructureXml($structure_id, $param);
			}

			// Показ инфосистем, если родитель - некий узел, у которого потомков в стурктуре нет, но есть связанные инфогруппы или инфоэлементы
			$structure_path = '/' . $this->GetStructurePath($parent_id, 0);

			// Получаем данные о родительском узле структуры
			$structure_row = $this->GetStructureItem($parent_id);

			$structure_row['current_level'] = 0;

			// ПОКАЗЫВАЕМ ИНФОГРУППЫ и ИНФОЭЛЕМЕНТЫ
			// Если существует инфосистема с адресом аналогичным текущему элементу структуры, то формируем XML для этой инфосистемы
			if (isset($this->MassIS[$parent_id]) && $parent_id != 0)
			{
				$information_systems_id = $this->MassIS[$parent_id];
				$this->XmlDataFromGenXml4StructureLevelMass .= $this->GenXML4InfSys($information_systems_id, $structure_row['menu_id'], $structure_row['current_level'], 0, $structure_path, $param);
			}

			// ПОКАЗЫВАЕМ ГРУППЫ и ТОВАРЫ МАГАЗИНА
			// Если существует магазин с адресом аналогичным текущему элементу структуры, то формируем XML для этого магазина
			if (isset($this->MassShopShops[$parent_id]) && $parent_id != 0)
			{
				$shop_shops_id = $this->MassShopShops[$parent_id];
				$this->XmlDataFromGenXml4StructureLevelMass .= $this->GenXML4Shop($shop_shops_id, $structure_row['menu_id'], $structure_row['current_level'], 0, $structure_path, $param);
			}
		}

		return $this->XmlDataFromGenXml4StructureLevelMass;
	}

	/**
	* Получение XML для свойств структуры. Перед вызовом метода необходимо вызвать $this->GetStructureProperty(array()); и передать массив ID узлов, для которых геренируются св-ва
	*
	* @param int $structure_id идентификатор раздела структуры
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $structure_id = CURRENT_STRUCTURE_ID;
	*
	* $xml = $structure->GetXml4StructurePropertys($structure_id);
	*
	* // Распечатаем результат
	* echo htmlspecialchars($xml);
	* ?>
	* </code>
	* @return string XML для свойств структуры
	*/
	function GetXml4StructurePropertys($structure_id)
	{
		$image = & singleton('Image');

		$kernel = & singleton ('kernel');

		$xmlData = '';
		$xmlData .= '<propertys>'."\n";

		// Если нет данных для узла в кэше
		if (!isset($this->cache_structure_property[$structure_id])
		|| !is_array($this->cache_structure_property[$structure_id]))
		{
			// Получаем для узла св-ва
			$count_propertys_item = 0;
		}
		else
		{
			$count_propertys_item = count($this->cache_structure_property[$structure_id]);
		}

		if (isset($this->cache_structure_property[$structure_id]) && is_array($this->cache_structure_property[$structure_id]))
		{
			foreach ($this->cache_structure_property[$structure_id] as $row1)
			{
				if (trim($row1['structure_propertys_xml_name']) != '')
				{
					switch ($row1['structure_propertys_type'])
					{
						case 2 : // свойство является файлом
							{
								if ($row1['structure_propertys_values_value'] != '' || $row1['structure_propertys_values_value_small'] != '')
								{
									$xmlData .= '<property type="File" id="' . $row1['structure_propertys_values_id'] . '" xml_name="' . str_for_xml($row1['structure_propertys_xml_name']) . '" name="' . str_for_xml($row1['structure_propertys_xml_name']) . '">' . "\n";
									$xmlData .= '<property_name>' . str_for_xml($row1['structure_propertys_xml_name']) . '</property_name>' . "\n";
									$xmlData .= '<value>' . str_for_xml($row1['structure_propertys_values_value']) . '</value>' . "\n";
									$xmlData .= '<' . str_for_xml($row1['structure_propertys_xml_name']) . '>' . str_for_xml($row1['structure_propertys_values_value']) . '</' . $row1['structure_propertys_xml_name'] . '>' . "\n";

									// Получаем элементы пути к папке с дополнительными свойствами структуры
									$structure_dir = $this->GetStructureItemDir($structure_id);

									// Учитываем CMS_FOLDER
									$file_path = CMS_FOLDER . $structure_dir . $row1['structure_propertys_values_file'];

									// проверяем существует ли файл большого изображения
									if (is_file($file_path))
									{
										// если дополнительное свойство является изображением, тегу value
										// дописываем атрибуты width - ширина и height - высота
										if (Core_Image::instance()->exifImagetype($file_path))
										{
											$size_property_big_image = $image->GetImageSize($file_path);
											$atributs = ' width="'.$size_property_big_image['width'].'" height="'.$size_property_big_image['height'].'"';
										}
										else
										{
											$atributs = '';
										}

										// Определяем размер файла в байтах
										$size = @ filesize($file_path);

										$atributs .= ' size="'.$size.'"';

										$xmlData .= '<file_path ' . trim($atributs) . '>' . '/' . $structure_dir . str_for_xml($row1['structure_propertys_values_file']) . '</file_path>' . "\n";

										// Оставлено для совместимости
										$xmlData .= '<property_file_path ' . trim($atributs) . '>' . '/' . $structure_dir . str_for_xml($row1['structure_propertys_values_file']).'</property_file_path>'."\n";
									}

									// Учитываем CMS_FOLDER
									$file_path = CMS_FOLDER . $structure_dir . $row1['structure_propertys_values_file_small'];

									// проверяем существует ли файл малого изображения
									if (is_file($file_path))
									{
										$xmlData .= '<small_image>'."\n";
										// если дополнительное свойство является изображением, тегу value
										// дописываем атрибуты width - ширина и height - высота
										if (Core_Image::instance()->exifImagetype($file_path))
										{
											$size_property_big_image = $image->GetImageSize($file_path);
											$atributs = ' width="'.$size_property_big_image['width'].'" height="'.$size_property_big_image['height'].'"';
										}
										else
										{
											$atributs = '';
										}

										// Определяем размер файла в байтах
										$size = @ filesize($file_path);

										$atributs .= ' size="'.$size.'"';

										$xmlData .= '<value>'.str_for_xml($row1['structure_propertys_values_value_small']).'</value>'."\n";
										$xmlData .= '<file_path ' . trim($atributs) . '>' . '/' . $structure_dir . str_for_xml($row1['structure_propertys_values_file_small']) . '</file_path>' . "\n";

										// Оставлено для совместимости
										$xmlData .= '<property_file_path ' . trim($atributs) . '>' . '/' . $structure_dir . str_for_xml($row1['structure_propertys_values_file_small']) . '</property_file_path>' . "\n";
										$xmlData .= '</small_image>' . "\n";
									}

									$xmlData .= '<order>' . $row1['structure_propertys_order'] . '</order>' . "\n";

									$xmlData .= '</property>'."\n";
								}
								break;
							}
						case 3 : // св-во является списком
							{

								if ($row1['structure_propertys_values_value'] != '')
								{

									if (class_exists('lists'))
									{
										$xmlData .= '<property type="List" id="'.$row1['structure_propertys_values_id'].'" xml_name="' . str_for_xml($row1['structure_propertys_xml_name']) . '" name="'.str_for_xml($row1['structure_propertys_xml_name']).'">'."\n";
										$xmlData .= '<property_name>'.str_for_xml($row1['structure_propertys_xml_name']).'</property_name>'."\n";

										// Определяем значение списка
										// Делаем запрос, если записанное значение - ID элемента списка не пустое
										if (!empty ($row1['structure_propertys_values_value']))
										{
											$lists = & singleton('lists');

											$row3 = $lists->GetListItem($row1['structure_propertys_values_value']);

											/* Если существует значение списка*/
											if ($row3)
											{
												$xmlData .= '<'.str_for_xml($row1['structure_propertys_xml_name']).'>'.str_for_xml($row3['lists_items_value']).'</'.str_for_xml($row1['structure_propertys_xml_name']).'>'."\n";
												$xmlData .= '<value>'.str_for_xml($row3['lists_items_value']).'</value>'."\n";
												$xmlData .= '<description>'.str_for_xml($row3['lists_items_description']).'</description>'."\n";
											}
										}

										$xmlData .= '<order>'.$row1['structure_propertys_order'].'</order>'."\n";
										$xmlData .= '</property>'."\n";
									}
								}
								break;
							}
						case 5 : // св-во является инфосистемой
							{

								if ($row1['structure_propertys_values_value'] != '')
								{
									$xmlData .= '<property type="InformationSystemItem" id="'.$row1['structure_propertys_values_id'].'" xml_name="' . str_for_xml($row1['structure_propertys_xml_name']) . '" name="'.$row1['structure_propertys_xml_name'].'">'."\n";
									$xmlData .= '<property_name>'.str_for_xml($row1['structure_propertys_xml_name']).'</property_name>'."\n";

									// Значение свойства типа информационный элемент получаем только в том случае, если это само не получение для свойства
									$is_get_information_for_property = TRUE;

									$InformationSystem = & singleton('InformationSystem');

									// value = 0, если не был указан в таблице соовтетствия, поэтому зря не выполняем получение данных
									if ($row1['structure_propertys_values_value'] != 0)
									{
										$xmlData .= $InformationSystem->GetXmlForInformatioItem($row1['structure_propertys_values_value'], array (
											'is_get_information_for_property' => $is_get_information_for_property
										));
									}

									$xmlData .= '<order>'.$row1['structure_propertys_order'].'</order>'."\n";

									$xmlData .= '</property>'."\n";
								}
								break;
							}

							// свойство - флажок
						case 7 :
							{

								$xmlData .= '<property type="Checkbox" id="'.$row1['structure_propertys_values_id'].'" xml_name="' . str_for_xml($row1['structure_propertys_xml_name']) . '" name="'.$row1['structure_propertys_xml_name'].'">'."\n";
								$xmlData .= '<property_name>'.str_for_xml($row1['structure_propertys_xml_name']).'</property_name>'."\n";
								$xmlData .= '<value>'.str_for_xml($row1['structure_propertys_values_value']).'</value>'."\n";
								$xmlData .= '<default_value>'.str_for_xml($row1['structure_propertys_define_value']).'</default_value>'."\n";
								$xmlData .= '<order>'.$row1['structure_propertys_order'].'</order>'."\n";
								$xmlData .= '</property>'."\n";

								break;
							}
							// свойство - Дата
						case 8 :
							{
								$DateClass = new DateClass();

								$xmlData .= '<property type="Data" id="'.$row1['structure_propertys_values_id'].'" xml_name="' . str_for_xml($row1['structure_propertys_xml_name']) . '" name="'.$row1['structure_propertys_xml_name'].'">'."\n";
								$xmlData .= '<property_name>'.str_for_xml($row1['structure_propertys_xml_name']).'</property_name>'."\n";
								$xmlData .= '<value>'.str_for_xml($DateClass->date_format($row1['structure_propertys_values_value'])).'</value>'."\n";
								$xmlData .= '<default_value>'.str_for_xml($row1['structure_propertys_define_value']).'</default_value>'."\n";
								$xmlData .= '<order>'.$row1['structure_propertys_order'].'</order>'."\n";
								$xmlData .= '</property>'."\n";

								break;
							}
							// свойство - ДатаВремя
						case 9 :
							{
								$DateClass = new DateClass();

								$xmlData .= '<property type="DataTime" id="'.$row1['structure_propertys_values_id'].'" xml_name="' . str_for_xml($row1['structure_propertys_xml_name']) . '" name="'.$row1['structure_propertys_xml_name'].'">'."\n";
								$xmlData .= '<property_name>'.str_for_xml($row1['structure_propertys_xml_name']).'</property_name>'."\n";
								$xmlData .= '<value>'.str_for_xml($DateClass->datetime_format($row1['structure_propertys_values_value'])).'</value>'."\n";
								$xmlData .= '<default_value>'.str_for_xml($row1['structure_propertys_define_value']).'</default_value>'."\n";
								$xmlData .= '<order>'.$row1['structure_propertys_order'].'</order>'."\n";
								$xmlData .= '</property>'."\n";

								break;
							}

						default :
							{

								if ($row1['structure_propertys_values_id'] != '')
								{
									switch ($row1['structure_propertys_type'])
									{
										case 0 : // Число
											{
												$property_type_name = 'Number';
												break;
											}
										case 1 : // Строка
											{
												$property_type_name = 'String';
												break;
											}
										case 4 : // Большое текстовое поле
											{
												$property_type_name = 'Textarea';
												break;
											}
										case 6 : // Визуальный редактор
											{
												$property_type_name = 'WYSIWYG';
												break;
											}
										default :
											{
												$property_type_name = 'Any';
												break;
											}
									}

									$xmlData .= '<property type="'.$property_type_name.'" id="'.$row1['structure_propertys_values_id'].'" xml_name="' . str_for_xml($row1['structure_propertys_xml_name']) . '" name="'.str_for_xml($row1['structure_propertys_xml_name']).'">'."\n";
									$xmlData .= '<property_name>'.str_for_xml($row1['structure_propertys_xml_name']).'</property_name>'."\n";
									$xmlData .= '<value>'.str_for_xml($row1['structure_propertys_values_value']).'</value>'."\n";
									$xmlData .= '<'.str_for_xml($row1['structure_propertys_xml_name']).'>'.str_for_xml($row1['structure_propertys_values_value']).'</'.str_for_xml($row1['structure_propertys_xml_name']).'>'."\n";
									$xmlData .= '<default_value>'.str_for_xml($row1['structure_propertys_define_value']).'</default_value>'."\n";
									$xmlData .= '<order>'.$row1['structure_propertys_order'].'</order>'."\n";
									$xmlData .= '</property>'."\n";
								}

								break;
							}
					}
				}
			}
		}

		$xmlData .= '</propertys>'."\n";

		return $xmlData;
	}

	/**
	* Метод вставки и редактирования дополнительного свойства для структуры
	*
	* @param int $type тип действия 0 - вставка, 1 - обновление
	* @param int $structure_propertys_id идентификатор дополнительного свойства
	* @param int $site_id идентификатор сайта
	* @param string $structure_propertys_name наименование дополнительного свойства
	* @param int $structure_propertys_type тип дополнительного свойства
	* @param int $structure_propertys_order порядок сортировки
	* @param string $structure_propertys_define_value значение по умолчанию для дополнительного свойства
	* @param string $structure_propertys_xml_name имя xml тега для дополнительного свойства
	* @param int $structure_propertys_lists_id идентификатор списка(если тип свойства - список)
	* @param int $structure_propertys_information_systems_id идентификатор информационной системы(для типа информационная система)
	* @param int $users_id идентификатор пользователя, если FALSE - берется текущий пользователь.
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $type = 0;
	* $structure_propertys_id = '';
	* $site_id = 1;
	* $structure_propertys_name = 'свойство';
	* $structure_propertys_type = 0;
	* $structure_propertys_order = 50;
	* $structure_propertys_define_value = '';
	* $structure_propertys_xml_name = 'myname';
	* $structure_propertys_lists_id = '';
	* $structure_propertys_information_systems_id = 0;
	*
	* $new_structure_propertys_id = $structure->InsertStructurePropertys($type, $structure_propertys_id, $site_id, $structure_propertys_name, $structure_propertys_type, $structure_propertys_order, $structure_propertys_define_value, $structure_propertys_xml_name, $structure_propertys_lists_id, $structure_propertys_information_systems_id);
	*
	* // Распечатаем результат
	* echo $new_structure_propertys_id;
	* ?>
	* </code>
	* @return int идентификатор отредактированного(вставленного) дополнительного свойства
	*/
	function InsertStructurePropertys($type, $structure_propertys_id, $site_id, $structure_propertys_name, $structure_propertys_type, $structure_propertys_order, $structure_propertys_define_value, $structure_propertys_xml_name, $structure_propertys_lists_id, $structure_propertys_information_systems_id, $users_id = FALSE)
	{
		$site_id = intval($site_id);
		$oStructure_Property_List = Core_Entity::factory('Structure_Property_List', $site_id);

		if ($structure_propertys_id == 0)
		{
			$structure_propertys_id = NULL;
		}

		$oProperty = Core_Entity::factory('Property', $structure_propertys_id);

		$oProperty->name = $structure_propertys_name;
		$oProperty->type = intval($structure_propertys_type);
		$oProperty->sorting = intval($structure_propertys_order);
		$oProperty->default_value = $structure_propertys_define_value;

		// Оставляем только латинские буквы и цифры
		$oProperty->tag_name = preg_replace('/[^a-zA-Z0-9а-яА-ЯЁ.\-_]/u', '', $structure_propertys_xml_name);
		$oProperty->list_id = intval($structure_propertys_lists_id);
		$oProperty->informationsystem_id = intval($structure_propertys_information_systems_id);

		if (is_null($structure_propertys_id) && $users_id)
		{
			$oProperty->user_id = $users_id;
		}

		$oStructure_Property_List->add($oProperty);

		return $oProperty->id;
	}

	/**
	* Метод выбора дополнительного свойства структуры определенного сайта(false - выбор всех)
	*
	* @param int $site_id идентификатор сайта
	* @param int $structure_propertys_id идентификатор дополнительного свойства
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $site_id = 1;
	* $structure_propertys_id = 10;
	*
	* $resource = $structure->SelectStructurePropertys($site_id, $structure_propertys_id);
	*
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource результат выборки информации о дополнительном свойстве(обо всех дополнительных свойствах)
	*/
	function SelectStructurePropertys($site_id, $structure_propertys_id = FALSE)
	{
		$site_id = intval($site_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('properties.id', 'structure_propertys_id'),
				array('structure_properties.site_id', 'site_id'),
				array('properties.name', 'structure_propertys_name'),
				array('properties.type', 'structure_propertys_type'),
				array('properties.sorting', 'structure_propertys_order'),
				array('properties.default_value', 'structure_propertys_define_value'),
				array('properties.tag_name', 'structure_propertys_xml_name'),
				array('properties.list_id', 'structure_propertys_lists_id'),
				array('properties.informationsystem_id', 'structure_propertys_information_systems_id'),
				array('properties.user_id', 'users_id')
			)
			->from('properties')
			->leftJoin('structure_properties', 'properties.id', '=', 'structure_properties.property_id')
			->where('structure_properties.site_id', '=', $site_id);

		if ($structure_propertys_id != -1 && $structure_propertys_id != FALSE)
		{
			//$structure_propertys_id = intval($structure_propertys_id);
			//$where = " AND `structure_propertys_id` = '$structure_propertys_id'";
			$queryBuilder->where('id', '=', $structure_propertys_id);
		}

		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Метод удаления дополнительного свойства
	*
	* @param int $structure_propertys_id идентификатор удаляемого дополнительного свойства
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Идентификатор удаляемого дополнительного свойства структуры
	* $structure_propertys_id = 8;
	*
	* $result = $structure->DelStructurePropertys($structure_propertys_id);
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
	* @return истина при удачном удалении, ложь - в обратном случае
	*/
	function DelStructurePropertys($structure_propertys_id)
	{
		Core_Entity::factory('Property', $structure_propertys_id)->markDeleted();

		return TRUE;
	}

	/**
	* Метод вставки и редактирования значения дополнительного свойства в таблице связи
	*
	* @param int $type тип действия 0 - вставка, 1 - обновление
	* @param int $structure_propertys_values_id идентификатор значения дополнительного свойства
	* @param int $structure_id идентификатор раздела структуры
	* @param int $structure_propertys_id идентификатор дополнительного свойства
	* @param string $structure_propertys_values_value значение дополнительного свойства
	* @return int идентификатор отредактированной(добавленной) записи
	*/
	function InsertStructurePropertysValue($type, $structure_propertys_values_id, $structure_id, $structure_propertys_id, $structure_propertys_values_value, $structure_propertys_values_file = '', $structure_propertys_values_value_small = '', $structure_propertys_values_file_small = '')
	{
		$oProperty = Core_Entity::factory('Property', $structure_propertys_id);

		$aValues = $oProperty->getValues($structure_id);

		if (count($aValues) > 0)
		{
			// Value already exist
			$oValue = $aValues[0];
		}
		else
		{
			$oValue = $oProperty->createNewValue($structure_id);
		}

		if ($oProperty->type != 2)
		{
			$oValue->setValue($structure_propertys_values_value);
		}
		else
		{
			$oValue->file = $structure_propertys_values_file;
			$oValue->file_name = $structure_propertys_values_value;
			$oValue->file_small = $structure_propertys_values_file_small;
			$oValue->file_small_name = $structure_propertys_values_value_small;
		}

		$oValue->save();

		return $oValue->id;
	}

	/**
	* Метод удаления значений дополнительных свойств из таблицы связи
	*
	* @param int $structure_id идентификатор раздела структуры
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Идентификатор структуры для удаляемых дополнительных свойств
	* $structure_id = 84;
	*
	* $result = $structure->DelStructurePropertysValue($structure_id);
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
	* @return bool истина при удачном удалении, ложь - в обратном случае
	*/
	function DelStructurePropertysValue($structure_id)
	{
		$structure_id = intval($structure_id);

		$oStructure = Core_Entity::factory('Structure', $structure_id);
		$aPropertyValues = $oStructure->getPropertyValues();

		$oStructure_Property_List = Core_Entity::factory('Structure_Property_List', $oStructure->site_id);

		foreach ($aPropertyValues as $oPropertyValue)
		{
			if ($oPropertyValue->Property->type == 2)
			{
				$oPropertyValue->setDir($oStructure_Property_List->getDirPath($oStructure));
			}
			$oPropertyValue->delete();
		}

		return TRUE;
	}

	/**
	* Получение информации о значении свойства структуры
	*
	* @param int $structure_propertys_values_id идентификатор значения свойства
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Идентификатор значения свойства структуры сайта
	* $structure_propertys_values_id = 9;
	*
	* $row = $structure->GetPropertyValue($structure_propertys_values_id);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	* @return array с информацией о значении свойства или FALSE
	*/
	function GetPropertyValue($structure_propertys_values_id)
	{
		throw new Core_Exception('Method GetPropertyValue() does not allow');
	}

	/**
	* Метод для удаления значения свойства узла структуры
	*
	* @param int $structure_propertys_values_id идентификатор значения свойства, которое нужно удалить
	* @param array $param массив дополнительных параметров
	* - $param['del_big_image'] параметр, определяющий удалять файл большого изображения или нет(true - удалять (по умолчанию), FALSE - не удалять)
	* - $param['del_small_image'] параметр, определяющий удалять файл малого изображения или нет(true - удалять (по умолчанию), FALSE - не удалять)
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Идентификатор удаляемого значения свойства узла структуры
	* $structure_property_value_id = 133;
	*
	* $result = $structure->DeletePropertyValue($structure_property_value_id);
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
	* @return resource результат выполнения запроса
	*/
	function DeletePropertyValue($structure_propertys_values_id, $param = array ())
	{
		throw new Core_Exception('Method DeletePropertyValue() does not allow');
	}

	/**
	* Старое наименование метода индексации структуры, оставлено для совместимости
	* @access private
	*/
	function IndexationDocuments($limit, $on_step, $parameters = array ())
	{
		return $this->IndexationStructure($limit, $on_step, $parameters);
	}

	/**
	* Метод индексации элементов структуры
	*
	* @param int $offset ограничение(по сколько элементов индексировать)
	* @param int $limit шаг для индексации
	* @param array $parameters массив дополнительных параметров
	* - $parameters['document_id'] - идентификатор индексируемого документа при событийной индексации
	* - $parameters['structure_id'] - идентификатор индексируемого узла структуры при событийной индексации
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $offset = 5;
	* $limit = 10;
	*
	* $row = $structure->IndexationStructure($offset, $limit);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	* @return array массив с данными о проиндексированных элементах
	*/
	function IndexationStructure($offset, $limit, $parameters = array ())
	{
		$result = array();

		if (isset($parameters['document_id']))
		{
			$aStructure = Core_Entity::factory('Structure', $parameters['structure_id']);

			$aStructure
				->queryBuilder()
				->where('document_id', '=', intval($parameters['document_id']))
				->limit(1)
				->findAll();

			if (isset($aStructure[0]))
			{
				$oStructure = $aStructure[0];
			}
			else
			{
				return FALSE;
			}
		}
		elseif (isset($parameters['structure_id']))
		{
			$oStructure = Core_Entity::factory('Structure', $parameters['structure_id']);
		}
		else
		{
			return FALSE;
		}

		Search_Controller::indexingSearchPages(array(
			$oStructure->indexing()
		));

		return TRUE;
	}

	/**
	 * Функция обратного вызова, используется модулем поисковой системы при выводе результатов поиска
	 *
	 * @param array $row массив с информацией о странице
	 * @return string дополнительный XML, включаемый в результат поиска
	 */
	function _CallbackSearch($row)
	{
		if (isset($row['search_page_module_value_type']) && isset($row['search_page_module_value_id']))
		{
			switch ($row['search_page_module_value_type'])
			{
				case 0 : // Узел структуры

					$access = array(0);

					if (class_exists('SiteUsers'))
					{
						$SiteUsers = & singleton('SiteUsers');

						$site_user_id = $SiteUsers->GetCurrentSiteUser();
						if ($site_user_id > 0)
						{
							$access = $SiteUsers->GetGroupsForUser($site_user_id);
						}
					}

					// Добавим группу в дерево потомком самого себя, чтобы GenXml4StructureLevelMass() провел генерацию
					$this->StructureParentMass[$row['search_page_module_value_id']][] = $row['search_page_module_value_id'];

					$xml = $this->GenXml4StructureLevelMass($row['search_page_module_value_id'], $access, 0,
						array('show_document_content' => TRUE)
					);

					unset ($this->StructureParentMass[$row['search_page_module_value_id']]);

					return $xml;
					break;

				default :
					break;
			}
		}

		return '';
	}

	/**
	* Метод определения принадлежности узла структуры вышестоящему родителю
	*
	* @param int $children_node_id идентификатор дочернего узла
	* @param int $parent_node_id идетификатор узла - возможного родителя
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $children_node_id = 88;
	* $parent_node_id = 80;
	*
	* if ($structure->NodeIsParent($children_node_id, $parent_node_id))
	* {
	* 	echo "Узел {$children_node_id} является дочерним для узла {$parent_node_id}\n";
	* }
	* else
	* {
	* 	echo "Узел {$children_node_id} не является дочерним для узла {$parent_node_id}\n";
	* }
	* ?>
	* </code>
	* @return boolean истина, если узел принадлежит родителю, иначе ложь
	*/
	function NodeIsParent($children_node_id, $parent_node_id)
	{
		$children_node_id = intval($children_node_id);
		$parent_node_id = intval($parent_node_id);

		// флаг указывающий является ли узел $children_node_id дочерним для узла
		$flag = FALSE;
		$current_node_id = $children_node_id;

		$row = $this->GetStructureItem($children_node_id);

		// цикл по узлам стуктуры, пока существует родитель
		while (isset($row['structure_parent_id']))
		{
			if ($row['structure_parent_id'] == $parent_node_id)
			{
				$flag = TRUE;
				break;
			}
			$row = $this->GetStructureItem($row['structure_parent_id']);
		}
		return $flag;
	}

	/**
	* Метод для получения списка всех доп. свойств структуры
	*
	* @param int $site_id идентификатор сайта, которому принадлежит свойство, если FALSE - учитываются все сайты
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Идентификатор сайта
	* $site_id = 1;
	*
	* $resource = $structure->GetAllStructureProperties($site_id);
	*
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	*
	* @return resource с информацией о свойстве
	*/
	function GetAllStructureProperties($site_id = FALSE)
	{
		return $this->SelectStructurePropertys($site_id, FALSE);
	}

	/**
	* Метод для получения списка всех значений доп. свойств структуры по идентификатору свойства
	*
	* @param int $structure_propertys_id свойства, которому принадлежит значение свойства, если FALSE-учитываются все свойства
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Идентификатор свойства
	* $structure_propertys_id = 7;
	*
	* $resource = $structure->GetAllStructurePropertyValuesOfProperty($structure_propertys_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource с информацией о значениях свойств
	*/
	function GetAllStructurePropertyValuesOfProperty($structure_propertys_id = FALSE)
	{
		throw new Core_Exception('Method GetAllStructurePropertyValuesOfProperty() does not allow');
	}

	/**
	* Метод для получения списка всех узлов структуры сайта
	*
	* @param int $site_id идентификатор сайта, которому принадлежит узел структуры, если FALSE - учитываются все сайты
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	* // Идентификатор сайта
	* $site_id = 1;
	* $resource = $structure->GetAllStructure($site_id);
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource с информацией об узлах структуры
	*/
	function GetAllStructure($site_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'structure_id'),
				array('structure_menu_id', 'menu_id'),
				array('template_id', 'templates_id'),
				array('data_template_id', 'data_templates_id'),
				array('site_id', 'site_id'),
				array('document_id', 'documents_id'),
				array('lib_id', 'lib_id'),
				array('parent_id', 'structure_parent_id'),
				array('show', 'structure_show'),
				array('name', 'structure_menu_name'),
				array('seo_title', 'structure_title'),
				array('seo_description', 'structure_description'),
				array('seo_keywords', 'structure_keywords'),
				array('url', 'structure_external_link'),
				array('sorting', 'structure_order'),
				array('path', 'structure_path_name'),
				array('type', 'structure_type'),
				array('siteuser_group_id', 'structure_access'),
				array('https', 'structure_access_protocol'),
				array('active', 'structure_activity'),
				array('indexing', 'structure_allow_indexation'),
				array('changefreq', 'structure_change_frequency'),
				array('priority', 'structure_priority'),
				array('user_id', 'users_id')
			)->from('structures');

		if ($site_id)
		{
			$queryBuilder->where('site_id', '=', $site_id);
		}

		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Метод для удлаения узла структуры с его дочерними узлами
	*
	* @param int $structure_id идентификатор узла структуры
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* // Идентификатор удаляемого узла структуры
	* $structure_id = 82;
	*
	* $result = $structure->DeleteStructure($structure_id);
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
	* @return mixed результат выполнения запроса
	*/
	function DeleteStructure($structure_id)
	{
		$structure_id = intval($structure_id);

		if (!$structure_id)
		{
			// Структуры с идентификатором 0 не существует, выходим
			return FALSE;
		}

		Core_Entity::factory('Structure', $structure_id)->markDeleted();

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'STRUCTURE';
			$cache->DeleteCacheItem($cache_name, $structure_id);
		}

		return TRUE;
	}

	/**
	* Получение пути от текущего узла к корневому
	*
	* @param integer $dir_id идентификатор текущего узла
	* @param boolean $first_call первый ли это вызов функции
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* //Идентификатор текущего узла структуры сайта
	* $dir_id = 86;
	*
	* $row = $structure->GetStructurePathArray($dir_id);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	* @return array массив данных
	*/
	function GetStructurePathArray($dir_id, $first_call = TRUE)
	{
		$dir_id = intval($dir_id);
		$first_call = Core_Type_Conversion::toBool($first_call);

		if ($first_call)
		{
			// Обнуляем массив.
			$this->path_array = array ();
		}

		$row = $this->GetStructureItem($dir_id);

		if ($row)
		{
			$this->path_array = $this->GetStructurePathArray($row['structure_parent_id'], FALSE);

			$this->path_array[] = array (
				$dir_id => $row['structure_menu_name'],
				'parent_link' => $row['structure_path_name']
			);
		}
		else
		{
			//$this->path_array[0] = '';
			unset($this->path_array[0]);
		}
		return $this->path_array;
	}

	/**
	* Получение значения свойства узла структуры по идентификатору узла структуры и идентификатору свойства
	*
	* @param int $structure_id идентификатор узла структуры
	* @param int $structure_propertys_id идентификатор дополнительного свойства
	* <br />Пример использования:
	* <code>
	* <?php
	* $structure = new Structure();
	*
	* $structure_id = CURRENT_STRUCTURE_ID;
	* $structure_propertys_id = 10;
	*
	* $row = $structure->GetStructurePropertyValue($structure_id, $structure_propertys_id);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	* @return mixed ассоциативный массив с информацией о значении дополнительного свойства узла структуры в случае успешного выполнения, FALSE - в противном случае
	*/
	function GetStructurePropertyValue($structure_id, $structure_propertys_id)
	{
		$oProperty = Core_Entity::factory('Property', $structure_propertys_id);
		$aPropertyValues = $oProperty->getValues($structure_id);

		if (isset($aPropertyValues[0]))
		{
			return $this->getArrayPropertyValue($aPropertyValues[0]);
		}
	}

	/**
	* Копирование узла структуры
	*
	* @param int $structure_id идентификатор копируемого узла структуры
	* @param int $site_id идентификатор сайта, в который нужно скопировать структуру (если FALSE, то в текущий сайт)
	* @param array $array_document_ids Массив с идентификаторами старых и новых документов
	* - int $array_document_ids['old_id'] = $new_id Идентификатор документа, который необходимо сопоставить со скопированной структурой (по умолчанию в соответствие ставится идентификатор документа копируемого узла структуры)
	* @param int $array_menu_ids Массив соответствий старых и новых идентификаторов меню, к которым необходимо отнести скопированный узел структуры (если не передан, то к тому же, что и копируемый узел)
	* @param array $array_datatemplate_ids Массив соответствий старых и новых идентификаторов шаблонов, с которыми необходимо связать скопированный узел структуры (если не передан, то к тому же, что и копируемый узел)
	* @param array $array_template_ids Массив соответствий старых и новых идентификаторов макетов, с которыми необходимо связать скопированный узел структуры (если не передан, то к тому же, что и копируемый узел)
	* <code>
	* <?php
	* $Structure = new Structure();
	*
	* $structure_id = 22;
	*
	* $newid = $Structure->CopyStructure($structure_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return mixed идентификатор копии узла структуры в случае успешного выполнения, FALSE - в противном случае
	*/
	function CopyStructure($structure_id, $site_id = FALSE, $new_structure_id = FALSE, $array_document_ids = array (), $array_menu_ids = array(), $array_datatemplate_ids = array(), $array_template_ids = array(), $is_assign_arr = array(), $shop_assign_arr = array())
	{
		$oNewStructure = Core_Entity::factory('Structure')->find($structure_id)->copy();

		if ($site_id)
		{
			$oNewStructure->site_id = $site_id;
			$oNewStructure->save();
		}

		return $oNewStructure->id;
	}

	/**
	 * Получение информации об узлах структуры конкрентного уровня
	 *
	 * @param int $structure_parent_id идентификатор родительского раздела
	 * @param int $menu_id идентификатор меню
	 * @param int $site_id идентификатор сайта
	 * @return resource Ответ базы
	 */
	function SelectStructureForParent($structure_parent_id, $menu_id = FALSE, $site_id = FALSE)
	{
		$structure_parent_id = intval($structure_parent_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'structure_id'),
				array('structure_menu_id', 'menu_id'),
				array('template_id', 'templates_id'),
				array('data_template_id', 'data_templates_id'),
				array('site_id', 'site_id'),
				array('document_id', 'documents_id'),
				array('lib_id', 'lib_id'),
				array('parent_id', 'structure_parent_id'),
				array('show', 'structure_show'),
				array('name', 'structure_menu_name'),
				array('seo_title', 'structure_title'),
				array('seo_description', 'structure_description'),
				array('seo_keywords', 'structure_keywords'),
				array('url', 'structure_external_link'),
				array('sorting', 'structure_order'),
				array('path', 'structure_path_name'),
				array('type', 'structure_type'),
				array('siteuser_group_id', 'structure_access'),
				array('https', 'structure_access_protocol'),
				array('active', 'structure_activity'),
				array('indexing', 'structure_allow_indexation'),
				array('changefreq', 'structure_change_frequency'),
				array('priority', 'structure_priority'),
				array('user_id', 'users_id')
			)
			->from('structures')
			->where('parent_id', '=', $structure_parent_id);

		if ($menu_id)
		{
			$queryBuilder->where('structure_menu_id', '=', $menu_id);
		}

		if ($site_id)
		{
			$queryBuilder->where('site_id', '=', $site_id);
		}

		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение пути хранения файлов узла структуры
	 *
	 * @param $structure_id идентификатор узла структуры
	 * @return mixed путь к узлу структуры или ложь, если такого узла структуры не существует
	 */
	function GetStructureItemDir($structure_id)
	{
		$structure_id = intval($structure_id);

		if ($structure_item_row = $this->GetStructureItem($structure_id))
		{
			$kernel = & singleton('kernel');
			$site = & singleton('site');

			if (!defined('UPLOADDIR'))
			{
				$site_row = $site->GetSite($structure_item_row['site_id']);
				$uploaddir = $site_row['site_uploaddir'];
			}
			else
			{
				$uploaddir = UPLOADDIR;
			}

			// Константа SITE_NESTING_LEVEL не определена
			if (!defined('SITE_NESTING_LEVEL'))
			{
				if (!isset($site_row))
				{
					$site_row = $site->GetSite($structure_item_row['site_id']);
				}

				$site_nesting_level = $site_row['site_nesting_level'];
			}
			else
			{
				$site_nesting_level = SITE_NESTING_LEVEL;
			}

			return $uploaddir . 'structure_' . Core_Type_Conversion::toInt($structure_item_row['site_id']) . '/' . $kernel->GetDirPath($structure_id, $site_nesting_level) . '/structure_' . $structure_id . '/';
		}

		return FALSE;
	}

	/**
	 * Копирование дополнительного свойства узлов структуры
	 * @param $structure_property_id
	 * @return mixed идентификатор копии дополнительного свойства узлов структуры в случае успешного выполнения, FALSE - в противном случае
	 */
	function CopyStructureProperty($structure_property_id, $site_id = CURRENT_SITE)
	{
		$structure_property_id = intval($structure_property_id);
		$site_id = intval($site_id);

		$oProperty = Core_Entity::factory('Property', $structure_property_id);
		$oNewProperty = $oProperty->copy(FALSE);

		// Relation with structure
		$oNewProperty->add(clone $oProperty->Structure_Property);

		$oNewProperty->Structure_Property->site_id = $site_id;
		$oNewProperty->Structure_Property->save();

		return $oNewProperty->id;
	}
}