<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Типовые динамические страницы".
 * 
 * Файл: /modules/lib/lib.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class lib
{
	var $section_path = '';
	var $mas_lib_dir = array();

	/**
	* Получение пути к директории lib
	*
	* @param int $lib_id Идентификатор типовой динамической страницы
	* 
	* @return string путь к файлу
	*/
	function GetLibPath($lib_id)
	{
		$lib_id = intval($lib_id);
		return CMS_FOLDER . "hostcmsfiles/lib/lib_{$lib_id}";
	}
	
	/**
	* Получение типовой динамической страницы по её идентификатору
	*
	* @param int $lib_id идентификатор типовой страницы
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_id = 33;
	*
	* $row = $lib->GetLib($lib_id);
	*
	* // Распечатаем результат
	* print_r ($row); 
	* ?>
	* </code>
	* @return mixed массив с информацией о типовой  динамической странице или FALSE
	*/
	function GetLib($lib_id)
	{
		$oLib = Core_Entity::factory('Lib')->find($lib_id);
	
		if($oLib->id)
		{
			return array(
				'lib_id' => $oLib->id,
				'lib_name' => $oLib->name,
				'lib_description' => $oLib->description,
				'users_id' => $oLib->user_id,
				'lib_module' => $oLib->loadLibFile(),
				'lib_module_config' => $oLib->loadLibConfigFile()
			);		
		}
		else
		{
			return FALSE;
		}
	}

	/**
	* Вставка информации о типовой динамической странице
	*
	* @param array $param список доп. параметров
	* - int $param['lib_id'] идентификатор динамической страницы
	* - int $param['lib_dir_id'] идентификатор раздела динамической страницы
	* - string $param['lib_name'] название страницы
	* - string $param['lib_description'] описание страницы
	* - string $param['lib_module'] код динамической страницы (берем из файла)
	* - string $param['lib_module_config'] код настроек динамической страницы (берем из файла)
	* - int $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $param['lib_dir_id'] = 27;
	* $param['lib_name'] = 'newlib';
	* $param['lib_description'] = 'Тестовая типовая динамическая страница';
	*
	* $newid = $lib->InsertLib($param);
	* 
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return mixed идентификатор вставленной страницы или FALSE
	*/
	function InsertLib($param)
	{
		$lib_id = Core_Type_Conversion::toInt($param['lib_id']);
		$user_id = Core_Type_Conversion::toInt($param['users_id']);
		
		$oLib = Core_Entity::factory('Lib', $lib_id == 0 ? NULL : $lib_id);
		
		if($user_id && !$lib_id)
		{
			$oLib->user_id = $user_id;
		}
		
		$oLib->lib_dir_id = Core_Type_Conversion::toInt($param['lib_dir_id']);
		$oLib->name = Core_Type_Conversion::toStr($param['lib_name']);
		$oLib->description = $param['lib_description'];
		$oLib->save();

		$oLib->saveLibFile(Core_Type_Conversion::toStr($param['lib_module']));
		$oLib->saveLibConfigFile(Core_Type_Conversion::toStr($param['lib_module_config']));

		return $oLib->id;
	}

	/**
	* Удаление типовой динамической страницы из библиотеки страниц
	*
	* @param int $lib_id идентификатор типовой динамической страницы
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_id = 33;
	*
	* $result = $lib->DeleteLib($lib_id);
	* 
	* if ($result)
	* {
	*	 echo "Удаление выполнено успешно";
	* }
	* else 
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	* @return mixed результат выполнения запроса
	*/
	function DeleteLib($lib_id)
	{
		Core_Entity::factory('Lib', $lib_id)->markDeleted();

		return TRUE;
	}

	/**
	* Получение информации о параметре типовой динамической страницы
	*
	* @param int $lib_property_id идентификатор параметра
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_property_id = 95;
	*
	* $row = $lib->GetLibProperty($lib_property_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed информация о параметре типовой динамической страниы или FALSE
	*/
	function GetLibProperty($lib_property_id)
	{
		$oLibProperty = Core_Entity::factory('Lib_Property')->find($lib_property_id);
		
		if(!is_null($oLibProperty->id))
		{
			return array(
				'lib_property_id' => $oLibProperty->id,
				'lib_id' => $oLibProperty->lib_id,
				'lib_property_name' => $oLibProperty->name,
				'lib_property_description' => $oLibProperty->description,
				'lib_property_varible_name' => $oLibProperty->varible_name,
				'lib_property_type' => $oLibProperty->type,
				'lib_property_default_value' => $oLibProperty->default_value,
				'lib_property_order' => $oLibProperty->sorting,
				'lib_property_sql_request' => $oLibProperty->sql_request,
				'lib_property_sql_caption_field' => $oLibProperty->sql_caption_field,
				'lib_property_sql_value_field' => $oLibProperty->sql_value_field,
				'users_id' => $oLibProperty->user_id
			);
		}
		
		return FALSE;
	}

	/**
	* Вставка информации о параметре типовой динамической страницы
	*
	* @param array $param список доп. параметров
	* - int $param['lib_property_id'] идентификатор параметра динамической страницы
	* - int $param['lib_id'] идентификатор динамической страницы
	* - string $param['lib_property_name'] название параметра динамической страницы
	* - string $param['lib_property_description'] описание параметра динамической страницы
	* - string $param['lib_property_varible_name'] название переменной параметра динамической страницы
	* - int $param['lib_property_type'] тип свойства параметра динамической страницы
	* - string $param['lib_property_default_value'] значение по умолчанию параметра динамической страницы
	* - int $param['lib_property_order'] порядок сортировки параметра динамической страницы
	* - int $param['lib_property_sql_request'] параметр типовой дин. страницы - SQL запрос, текст запроса
	* - int $param['lib_property_sql_caption_field'] параметр типовой дин. страницы - SQL запрос, поле заголовка
	* - int $param['lib_property_sql_value_field'] параметр типовой дин. страницы - SQL запрос, поле значения
	* - int $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $param['lib_id'] = 34;
	* $param['lib_property_name'] = 'Новый параметр';
	* $param['lib_property_varible_name'] = 'test_param';
	* $param['lib_property_type'] = 0;
	* $param['lib_property_default_value'] = 'Значение по умолчанию'; 
	* $param['lib_property_order'] = 10;
	*
	* $newid = $lib->InsertLibProperty($param);
	* 
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return mixed идентификатор вставленного параметра или FALSE
	*/
	function InsertLibProperty($param)
	{
		$lib_property_id = Core_Type_Conversion::toInt($param['lib_property_id']);
		$user_id = Core_Type_Conversion::toInt($param['users_id']);
	
		$oLibProperty = Core_Entity::factory('Lib_Property', $lib_property_id == 0 ? NULL : $lib_property_id);
		
		if(!$lib_property_id && $user_id)
		{
			$oLibProperty->user_id = $user_id;
		}
		
		$oLibProperty->lib_id = Core_Type_Conversion::toInt($param['lib_id']);
		$oLibProperty->name = Core_Type_Conversion::toStr($param['lib_property_name']);
		$oLibProperty->description = Core_Type_Conversion::toStr($param['lib_property_description']);
		$oLibProperty->varible_name = Core_Type_Conversion::toStr($param['lib_property_varible_name']);
		$oLibProperty->type = Core_Type_Conversion::toInt($param['lib_property_type']);
		$oLibProperty->default_value = Core_Type_Conversion::toStr($param['lib_property_default_value']);
		$oLibProperty->sorting = Core_Type_Conversion::toInt($param['lib_property_order']);
		$oLibProperty->sql_request = Core_Type_Conversion::toStr($param['lib_property_sql_request']);
		$oLibProperty->sql_caption_field = Core_Type_Conversion::toStr($param['lib_property_sql_caption_field']);
		$oLibProperty->sql_value_field = Core_Type_Conversion::toStr($param['lib_property_sql_value_field']);

		$oLibProperty->save();
		
		return $oLibProperty->id;
	}

	/**
	* Удаление параметра типовой динамической страницы
	*
	* @param int $lib_property_id идентификатор параметра типовой динамической страницы
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_property_id = 426;
	*
	* $result = $lib->DeleteLibProperty($lib_property_id);
	* 
	* if ($result)
	* {
	* 	echo "Параметр типовой динамической страницы удален";
	* }
	* else 
	* {
	* 	echo "Ошибка! Параметр типовой динамической страницы не удален!";
	* }
	* ?>
	* </code>
	* @return mixed результат выполнения запроса
	*/
	function DeleteLibProperty($lib_property_id)
	{
		Core_Entity::factory('Lib_Property', intval($lib_property_id) == 0 ? NULL : $lib_property_id)->markDeleted();

		return TRUE;
	}

	/**
	* Получение списка типовых динамических страниц
	*
	* @param int $lib_dir_id идентификатор родительской директории, если FALSE, то отображаются все директории
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_dir_id = FALSE;
	*
	* $resource = $lib->GetAllLibs($lib_dir_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return mixed результат выборки
	*/
	function GetAllLibs($lib_dir_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'lib_id'),	
				array('lib_dir_id', 'lib_dir_id'),	
				array('name', 'lib_name'),	
				array('description', 'lib_description'),	
				array('user_id', 'users_id')
			)
			->from('libs')
			->where('deleted', '=', 0);
		
		if ($lib_dir_id !== FALSE)
		{
			$queryBuilder->where('lib_dir_id', '=', $lib_dir_id);
		}
		
		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение списка всех дополнительных параметров типовой динамической страницы
	*
	* @param int $lib_id идентификатор типовой динамической страницы
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_id = 1;
	*
	* $resource = $lib->GetAllLibProperties($lib_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return mixed результат выборки
	*/
	function GetAllLibProperties($lib_id)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'lib_property_id'),	
				array('lib_id', 'lib_id'),	
				array('name', 'lib_property_name'),	
				array('description', 'lib_property_description'),	
				array('varible_name', 'lib_property_varible_name'),
				array('type', 'lib_property_type'),
				array('default_value', 'lib_property_default_value'),
				array('sql_request', 'lib_property_sql_request'),
				array('sql_caption_field', 'lib_property_sql_caption_field'),
				array('sql_value_field', 'lib_property_sql_value_field'),
				array('user_id', 'users_id')
			)
			->from('lib_properties')
			->where('deleted', '=', 0)
			->where('lib_id', '=', $lib_id)
			->orderBy('sorting');
		
		return $queryBuilder->execute()->getResult();
	}

	/**
	* Сохранение настроек типовой динамической страницы для стурктуры в файл
	*
	* @param int $lib_id идентификатор типовой динамической страницы
	* @param int $structure_id идентификатор структуры
	* @param array $values массив с данными в виде 'Имя_переменной' => 'значение'
	* @return bool
	*/
	function SaveLibPropertiesValue($lib_id, $structure_id, $values)
	{
		Core_Entity::factory('Lib', $lib_id)->saveDatFile($values, $structure_id);
		return TRUE;
	}

	/**
	* Загрузка значений параметров типовой дин. страницы для структуры
	*
	* @param int $lib_id идентификатор типовой динамической страницы
	* @param int $structure_id идентификатор структуры
	* @return array массив с данными в виде 'Имя_переменной' => 'значение'
	*/
	function LoadLibPropertiesValue($lib_id, $structure_id)
	{ 
		return Core_Entity::factory('Lib', $lib_id)->getDat($structure_id);
	}

	/**
	* Обновление порядка сортировки параметра типовой дин. страницы
	*
	* @param int $lib_property_id идентификатор параметра типовой дин. страницы
	* @param int $lib_property_order порядок сортировки
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_property_id = 91;
	* $lib_property_order = 65;
	*
	* $result = $lib->UpdateLibPropertyOrder($lib_property_id, $lib_property_order);
	* 
	* // Распечатаем результат
	* if ($result)
	* {
	* 	echo 'Порядок сортировки параметра типовой динамической страницы изменен';
	* }
	* else
	* {
	* 	echo 'Ошибка! Порядок сортировки параметра типовой динамической страницы не изменен!';
	* }
	* ?>
	* </code>
	* @return результат запроса
	*/
	function UpdateLibPropertyOrder($lib_property_id, $lib_property_order)
	{
		$oLibProperty = Core_Entity::factory('Lib_Property')->find($lib_property_id);
		$oLibProperty->sorting = $lib_property_order;
		$oLibProperty->save();
		
		return TRUE;
	}

	/**
	* Создание копии типовой динамической страницы
	*
	* @param int $lib_id идентификатор типовой дин. страницы
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_id = 1;
	*
	* $copy_result = $lib->CopyLib($lib_id);
	* 
	* // Распечатаем результат
	* if ($copy_result)
	* {
	* 	echo 'Типовая динамическая страница скопирована';
	* }
	* else
	* {
	* 	echo 'Ошибка! Типовая динамическая страница не скопирована!';
	* }
	* ?>
	* </code>
	* @return boolean  
	*/
	function CopyLib($lib_id)
	{
		return Core_Entity::factory('Lib')->find($lib_id)->copy()->id;
	}

	/**
	* Получение списка директорий типовых дин. страниц
	*
	* @param int $lib_dir_parent_id идентификатор родительской директории, если FALSE, то отображаются все директории
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_dir_parent_id = FALSE;
	*
	* $resource = $lib->GetAllLibDirs($lib_dir_parent_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return mixed результат выборки
	*/
	function GetAllLibDirs($lib_dir_parent_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'lib_dir_id'),	
				array('parent_id', 'lib_dir_parent_id'),	
				array('name', 'lib_dir_name'),	
				array('user_id', 'users_id')
			)
			->from('lib_dirs')
			->where('deleted', '=', 0);
		
		if ($lib_dir_parent_id !== FALSE)
		{
			$queryBuilder->where('parent_id', '=', $lib_dir_parent_id);
		}
		
		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение информации о директории типовых дин. страниц
	*
	* @param int $lib_dir_id идентификатор директории
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_dir_id = 15;
	*
	* $row = $lib->GetLibDir($lib_dir_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed массив с информацией о директории или FALSE
	*/
	function GetLibDir($lib_dir_id)
	{
		$oLibDir = Core_Entity::factory('Lib_Dir')->find($lib_dir_id);
		
		if (!is_null($oLibDir->id))
		{
			return array(
				'lib_dir_id' => $oLibDir->id,
				'lib_dir_parent_id' => $oLibDir->parent_id,
				'lib_dir_name' => $oLibDir->name,
				'users_id' => $oLibDir->user_id
			);
		}
		
		return FALSE;
	}

	/** 
	* Внутренний метод возвращает путь к типовой дин. странице
	*
	* @param int $lib_dir_id идентификатор директории или типовой дин. страницы
	*/
	/*function GetSectionPath($lib_dir_id)
	{
		$lib_dir_id = intval($lib_dir_id);

		$DataBase = & singleton('DataBase');
		$query = "SELECT * FROM `lib_dirs` WHERE `id` = '$lib_dir_id'";
		$DataBase->select($query);

		if ($DataBase->get_count_row() == 0)
		{
			$this->section_path = '<a href="?action=show_lib">'.Core::_('Lib_Dir.lib_dir_root').'</a> // '.$this->section_path;

		}
		else
		{
			$row = mysql_fetch_assoc($DataBase->result);
			$this->section_path = '<a href="?action=show_lib&lib_dir_parent_id='.$row['lib_dir_id'].'">'.htmlspecialchars($row['lib_dir_name']).'</a>&nbsp;//&nbsp;'.$this->section_path;
			$this->GetSectionPath($row['lib_dir_parent_id']);
		}
	}*/

	/**
	* Получение пути от текущего каталога типовых динамических страниц до корневого
	*
	* @param int $lib_dir_id идентификатор узла
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_dir_id = 28;
	*
	* $row = $lib->GetLibPathArray($lib_dir_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив каталогов, от текущего до корневого каталога
	*/
	function GetLibPathArray($lib_dir_id, $first_call = true)
	{
		$lib_dir_id = Core_Type_Conversion::toInt($lib_dir_id);
		$first_call = Core_Type_Conversion::toBool($first_call);
		
		if ($first_call)
		{
			// Обнуляем массив.
			$this->path_array = array();
		}
		
		$oLibDir = Core_Entity::factory('Lib_Dir')->find($lib_dir_id);
		
		if($oLibDir->id == NULL)
		{
			$this->path_array[0] = Core::_('Lib_Dir.lib_group_root');
		}
		else
		{
			$this->path_array = $this->GetLibPathArray($oLibDir->parent_id, FALSE);
			
			$this->path_array[$lib_dir_id] = array(
				'lib_dir_id' => $oLibDir->id,
				'lib_dir_parent_id' => $oLibDir->parent_id,
				'lib_dir_name' => $oLibDir->name,
				'users_id' => $oLibDir->user_id
			);
		}
		
		return $this->path_array;
	}

	/**
	* Формирование дерева разделов
	*
	* @param int $lib_dir_parent_id - идентификатор родительского раздела
	* @param string $separator - символ (строка)-разделитель
	* @param int lib_dir_id_not_set - идентификатор группы, которая не включается в дерево вместе с потомками. Необязательный параметр.
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_dir_parent_id = 15;
	* $separator = '';
	*
	* $row = $lib->GetLibDirTree($lib_dir_parent_id, $separator);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array массив с данными о дереве разделов типовых дин. страниц
	*/
	function GetLibDirTree($lib_dir_parent_id, $separator, $lib_dir_id_not_set = FALSE)
	{
		$oLibDir = Core_Entity::factory('Lib_Dir')->find($lib_dir_parent_id);
	
		$aChildLibDirs = $oLibDir->lib_dirs->findAll();
		
		foreach($aChildLibDirs as $oChildLibDir)
		{
			if ($lib_dir_id_not_set !== $oChildLibDir->id)
			{
				$this->mas_lib_dir[] = array($oChildLibDir->id, $separator . htmlspecialchars($oChildLibDir->name));
				$this->GetLibDirTree($oChildLibDir->id, $separator.$separator, $lib_dir_id_not_set);
			}
		}
		
		return $this->mas_lib_dir;
	}

	/**
	* Вставка/обновление информации о разделе типовых динамических страниц
	*
	* @param array $param массив параметров
	* - int $param['lib_dir_id'] идентификатор раздела динамической страницы
	* - int $param['lib_dir_parent_id'] идентификатор родителя раздела динамической страницы
	* - int $param['lib_dir_name'] название раздела
	* - int $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $param['lib_dir_parent_id'] = 15;
	* $param['lib_dir_name'] = 'New';
	*
	* $newid = $lib->InsertLibDir($param);
	* 
	* // Распечатаем результат
	* if ($newid)
	* { 
	* 	echo 'Раздел типовых динамических страниц добавлен';
	* }
	* else
	* {
	* 	echo 'Ошибка! Раздел типовых динамических страниц не добавлен!';
	* } 
	* ?>
	* </code>
	* @return mixed идентификатор раздела типовых динамических страниц или FALSE
	*/
	function InsertLibDir($param)
	{
		$lib_dir_id = Core_Type_Conversion::toInt($param['lib_dir_id']);
		$user_id = Core_Type_Conversion::toInt($param['users_id']);
	
		$oLibDir = Core_Entity::factory('Lib_Dir', $lib_dir_id);
	
		if(!$lib_dir_id && $user_id)
		{
			$oLibDir->user_id = $user_id;
		}
		
		if (isset($param['lib_dir_parent_id']))
		{
			$oLibDir->parent_id = Core_Type_Conversion::toInt($param['lib_dir_parent_id']);
		}
		
		if (isset($param['lib_dir_name']))
		{
			$oLibDir->name = Core_Type_Conversion::toStr($param['lib_dir_name']);
		}
		
		return $oLibDir->save()->id;
	}

	/**
	* Рекурсивное удаление раздела типовых динамической страниц со всеми его подразделами
	*
	* @param int $lib_dir_id идентификатор раздела
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_dir_id = 28;
	*
	* $result = $lib->DeleteLibDir($lib_dir_id);
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
	* @return boolean
	*/
	function DeleteLibDir($lib_dir_id)
	{
		Core_Entity::factory('Lib_Dir')->find($lib_dir_id)->markDeleted();
	
		return TRUE;
	}

	/**
	* Вставка/обновление элемента списка для параметра типовой динамической страницы
	*
	* @param array $param массив с параметрами элемента списка
	* - int $param['lib_property_id'] идентификатор параметра типовой дин. страницы
	* - int $param['lib_property_list_id'] идентификатор элемента списка
	* - string $param['lib_property_list_name'] название элемента списка
	* - string $param['lib_property_list_value'] значение элемента списка
	* - int $param['$lib_property_list_order'] порядок сортировки элемента списка
	* - int $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $param['lib_property_id'] = 439;
	* $param['lib_property_list_name'] = 'Вариант 1';
	* $param['lib_property_list_value'] = '1';
	*
	* $param['$lib_property_list_order'] = 0;
	*
	* $newid = $lib->InsertLibPropertyListItem($param);
	*
	* // Распечатаем результат
	* if ($newid)
	* {
	* 	echo 'Элемент списка для параметра типовой динамической страницы добавлен';
	* }
	* else
	* {
	* 	echo 'Ошибка! Элемент списка для параметра типовой динамической страницы не добавлен!';
	* }
	* ?>
	* </code>
	* @return mixed идентификатор вставленного/обновленного элемента или FALSE
	*/
	function InsertLibPropertyListItem($param)
	{
		$lib_property_list_id = Core_Type_Conversion::toInt($param['lib_property_list_id']);
		$user_id = Core_Type_Conversion::toInt($param['users_id']);
	
		$oLibPropertyListValue = Core_Entity::factory('Lib_Property_List_Value', $lib_property_list_id);
		
		if(!$lib_property_list_id && $user_id)
		{
			$oLibPropertyListValue->user_id = $user_id;
		}
		
		$oLibPropertyListValue->lib_property_id = Core_Type_Conversion::toInt($param['lib_property_id']);
		$oLibPropertyListValue->name = Core_Type_Conversion::toStr($param['lib_property_list_name']);
		$oLibPropertyListValue->value = Core_Type_Conversion::toStr($param['lib_property_list_value']);
		$oLibPropertyListValue->sorting = Core_Type_Conversion::toInt($param['lib_property_list_order']);

		return $oLibPropertyListValue->save()->id;
	}

	/**
	* Получение списка элеметов параметра типовой динамической страницы типа "Список"
	*
	* @param int $lib_property_id идентификатор параметра типовой динамическойстраницы типа "Список", если равен FALSE - получаем список элементов всех парметров типовых динамических страниц типа "Список"
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_property_id = 81;
	*
	* $resource = $lib->GetAllLibPropertyListItems($lib_property_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return mixed результат выборки
	*/
	function GetAllLibPropertyListItems($lib_property_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'lib_property_list_id'),	
				array('lib_property_id', 'lib_property_id'),	
				array('name', 'lib_property_list_name'),	
				array('value', 'lib_property_list_value'),	
				array('sorting', 'lib_property_list_order'),	
				array('user_id', 'users_id')
			)
			->from('lib_property_list_values')
			->where('deleted', '=', 0);
		
		if ($lib_property_id !== FALSE)
		{
			$queryBuilder->where('lib_property_id', '=', $lib_property_id);
		}
		
		return $queryBuilder->execute()->getResult();
	}

	/**
	* Удаление элемента списка параметра типовой динамической страницы типа "Список"
	*
	* @param int $lib_property_list_id идентификатор элемента списка
	* <code>
	* <?php 
	* $lib = new lib();
	*
	* $lib_property_list_id = 33;
	*
	* $result = $lib->DeleteLibPropertyListItem($lib_property_list_id);
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
	* @return результат выполнения запроса
	*/
	function DeleteLibPropertyListItem($lib_property_list_id)
	{
		Core_Entity::factory('Lib_Property_List_Value')->find($lib_property_list_id)->markDeleted();
	
		return TRUE;
	}

	/**
	* Конвертирование SQL-запроса, введенного для свойства типовой динамической страницы, с учетом замены заранее определенных значенией.
	* <br/>Список замен:
	* <br/>{SITE_ID} - идентификатор текущего выбранного сайта
	*
	* @param string $sql
	*/
	function ConvertSqlProperty($sql)
	{
		$sql = strval($sql);
		$sql = str_replace('{SITE_ID}', CURRENT_SITE, $sql);

		return $sql;
	}
}