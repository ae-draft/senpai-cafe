<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Управление модулями".
 * 
 * Файл: /modules/Modules/Modules.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class modules
{
	var $error;

	/**
	* Вставка/обновление информации о модуле
	*
	* @param int $type – параметр, определяющий производится вставка или обновление информации о модуле (0 – вставка, 1 - обновление) 
	* @param int $modules_id – идентификатор обновляемого модуля (при вставке равен 0)
	* @param string $modules_name - название модуля
	* @param string $modules_description  – описание модуля
	* @param string $modules_status - флаг активности (доступности) модуля (1 – модуль активен (доступен), 0 – модуль неактивен)
	* @param string $modules_path – директория размещения модуля
	* @param int $modules_order – порядковый номер модуля в списке сортировки модулей
	* @param int $users_id – идентификатор пользователя центра администрирования, котрый добавил элемент
	* <code>
	* <?php
	* $modules = & singleton('modules');
	*
	* $type = 0;
	* $modules_id = 0;
	* $modules_name = 'Новый модуль';
	* $modules_description = 'Описание модуля';
	* $modules_status = 0;
	* $modules_path = 'newmodul';
	* $modules_order = 0;
	*
	* $newid = $modules->insert_module($type, $modules_id, $modules_name, $modules_description, $modules_status, $modules_path, $modules_order);
	*
	* // Распечатаем результат
	* echo ($newid);
	* ?>
	* </code>
	* @return int идентификатор добавляемого/редактируемого модуля
	*/
	function insert_module($type, $modules_id, $modules_name, $modules_description,
	$modules_status, $modules_path, $modules_order, $users_id = false)
	{
		if ($modules_id == 0)
		{
			$modules_id = NULL;
		}
	
		$oModule = Core_Entity::factory('Module', $modules_id);
		
		$oModule->name = $modules_name;
		$oModule->description = $modules_description;
		$oModule->active = $modules_status;
		$oModule->path = $modules_path;
		$oModule->sorting = $modules_order;
		
		if (is_null($modules_id) && $users_id)
		{
			$oModule->user_id = $users_id;
		}
		
		$oModule->save();

		return $oModule->id;
	}

	/**
	* Получение информации о модуле
	*
	* @param int $modules_id – идентификатор модуля (если $modules_id = -1 получаем информацию о всех модулях системы)
	* <code>
	* <?php
	* $modules = & singleton('modules');
	*
	* $modules_id = 63;
	*
	* $resource = $modules->select_modules($modules_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource информация о модуле (модулях)
	*/
	function select_modules($modules_id)
	{
		$modules_id= intval($modules_id);
		
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'modules_id'),
				array('name', 'modules_name'),
				array('description', 'modules_description'),
				array('active', 'modules_status'),
				array('path', 'modules_path'),
				array('sorting', 'modules_order'),
				array('user_id', 'users_id')
			)->from('modules');
	
		if ($modules_id != -1)
		{					
			$queryBuilder->where('id', '=', $modules_id);
		}
		
		$queryBuilder->where('deleted', '=', 0);
		
		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение информации о модуле
	*
	* @param int $modules_id – идентификатор модуля
	* <code>
	* <?php
	* $modules = & singleton('modules');
	*
	* $modules_id = 63;
	*
	* $row = $modules->GetModule($modules_id);
	*
	* print_r($row);
	* ?>
	* </code>
	* @return array информация о модуле
	*/
	function GetModule($modules_id)
	{
		$modules_id = intval($modules_id);
		$result = $this->select_modules($modules_id);
		
		if (mysql_num_rows($result) == 1)
		{
			return mysql_fetch_assoc($result);
		}
		else 
		{
			return false;
		}
	}
	
	/**
	* Удаление информации о модуле
	*
	* @param int $modules_id идентификатор удаляемого модуля
	* <code>
	* <?php
	* $modules = & singleton('modules');
	*
	* $modules_id = 78;
	*
	* $result = $modules->del_module($modules_id);
	*
	* if ($result)
	* {
	*	echo "Удаление выполнено успешно";
	* }
	* else 
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	* @return boolean результат запроса на удаление модуля
	*/
	function del_module($modules_id)
	{
		$modules_id = intval($modules_id);
		Core_Entity::factory('Module', $modules_id)->markDeleted();
		return TRUE;
	}

	/**
	* Проверка наличия активного модуля
	*
	* @param string $module_path – название модуля, например Search
	* <code>
	* <?php
	* $modules = & singleton('modules');
	*
	* $module_path = 'Site_users';
	*
	* $result = $modules->IssetModule($module_path);
	*
	* if ($result)
	* {
	* 	echo "Модуль существует и активен";
	* }
	* else 
	* {
	*	echo "Модуль не существует или он не активен";
	* }
	* ?>
	* </code>
	* @return boolean true - модуль существует и активен, false – модуль не существует или он не активен.
	*/
	function IssetModule($module_path)
	{
		if (isset(Core::$modulesList[$module_path]))
		{
			return Core::$modulesList[$module_path]->active != 0;
		}
		else
		{
			$queryBuilder = Core_QueryBuilder::select('id')
			->from('modules')
			->where('path', '=', $module_path)
			->where('active', '=', 1)
			->where('deleted', '=', 0);

			$result = $queryBuilder->execute()->getNumRows();
			
			if ($result == 1)
			{
				$result = TRUE;
			}
			else
			{
				$result = FALSE;
			}
		}
		
		return $result;
	}
}
