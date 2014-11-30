<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Константы".
 *
 * Файл: /modules/Constants/Constants.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class Constants
{
	/**
	* Свойство, содержащее число строк в выборке
	*
	* @var int
	* @access private
	*/
	var $count_row;

	/**
	* Добавление/редактирование данных о константе
	*
	* @param int $type параметр, определяющий производится вставка или обновление данных о константе (0 - вставка, 1 - обновление)
	* @param int $constants_id идентификатор константы, если идет обновление
	* @param string $constants_name имя константы
	* @param string $constants_value значение константы
	* @param string $constants_description описание константы
	* @param int $constants_activity статус активности константы (0 - неактивна, 1 - активна)
	* @param int $users_id идентификатор пользователя центра администрирования, добавившего элемент
	* @return mixed идентификатор вставленной/обновленной записи в случае успешного выполнения, false - в противном случае
	* <code>
	* <?php
	* $Constant = new Constants();
	*
	* $type = 0;
	* $constants_name = 'Константа 1';
	* $constants_value = '';
	* $constants_description = '';
	* $constants_activity = '';
	*
	* $newid = $Constant->AddEditConstants($type, $constants_id, $constants_name, $constants_value, $constants_description, $constants_activity);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function AddEditConstants($type, $constants_id, $constants_name, $constants_value,
	$constants_description, $constants_activity, $users_id = false)
	{
		if ($type == 0)
		{
			$constants_id = NULL;
		}

		$constant = Core_Entity::factory('Constant', $constants_id);

		$constant->queryBuilder()->where('name', '=', $constants_name);
		$aConstants = $constant->findAll();

		if (count($aConstants) == 0)
		{
			$constant->name = $constants_name;
			$constant->value = $constants_value;
			$constant->description = $constants_description;
			$constant->active = $constants_activity;

			if (is_null($constants_id) && $users_id)
			{
				$constant->user_id = $users_id;
			}

			$constant->save();

			return $constant->id;
		}
		else
		{
			return false;
		}
	}

	/**
	* Получение данных о константе
	*
	* @param int $constants_id идентификатор константы
	* @return mixed array с данными или false при отсутствии константы
	* <code>
	* <?php
	* $Constant = new Constants();
	*
	* $constants_id = 47;
	*
	* $row = $Constant->GetConstant($constants_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetConstant($constants_id)
	{
		$constant = Core_Entity::factory('Constant')->find($constants_id);

		if (!is_null($constant->id))
		{
			return array(
				'constants_id' => $constant->id,
				'constants_name' => $constant->name,
				'constants_value' => $constant->value,
				'constants_description' => $constant->description,
				'constants_activity' => $constant->active,
				'users_id' => $constant->user_id
			);
		}

		return FALSE;
	}

	/**
	* Получение данных о константе по ее имени
	*
	* @param int $constants_name наименование константы
	* @return mixed array с данными или false при отсутствии константы
	* <code>
	* <?php
	* $Constant = new Constants();
	*
	* $constants_name = 'LOG_DAYS_LIMIT';
	*
	* $row = $Constant->GetConstantByName($constants_name);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetConstantByName($constants_name)
	{
		$oConstant = Core_Entity::factory('Constant')->getByName($constants_name);

		if (!is_null($oConstant->id))
		{
			return array(
				'constants_id' => $oConstant->id,
				'constants_name' => $oConstant->name,
				'constants_value' => $oConstant->value,
				'constants_description' => $oConstant->description,
				'users_id' => $oConstant->user_id
			);
		}

		return FALSE;
	}

	/**
	* Устаревший метод, не рекомендуется к использованию с версии 5.1.2
	*
	* @param int $constants_id
	* @see GetConstant(), GetAllConstants()
	*/
	function GetConstants($constants_id)
	{
		if ($constants_id !== false)
		{
			return $this->GetConstant($constants_id);
		}
		else
		{
			return $this->GetAllConstants();
		}
	}

	/**
	* Получение данных обо всех константах
	*
	* @return resource
	* <code>
	* <?php
	* $Constant = new Constants();
	*
	* $resource = $Constant->GetAllConstants();
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	*	print_r($row);
	* }
	* ?>
	* </code>
	*/
	function GetAllConstants()
	{
		return Core_QueryBuilder::select(
			array('id', 'constants_id'),
			array('name', 'constants_name'),
			array('value', 'constants_value'),
			array('description', 'constants_description'),
			array('active', 'constants_activity'),
			array('user_id', 'users_id')
		)->from('constants')
		->where('deleted', '=', '0')
		->execute()
		->getResult();
	}

	/**
	* Удаление константы
	*
	* @param int $constants_id идентификатор удаляемой константы
	* @return boolean true в случае успешного выполнения, false в противном случае
	* <code>
	* <?php
	* $Constant = new Constants();
	*
	* $constants_id = 51;
	*
	* $result = $Constant->DelConstants($constants_id);
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
	function DelConstants($constants_id)
	{
		Core_Entity::factory('Constant', $constants_id)->markDeleted();
		return TRUE;
	}
}
