<?php

/**
 * @access private
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class UserAccess extends user_access
{

}

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Пользователи раздела администрирования".
 *
 * Файл: /modules/UserAccess/UserAccess.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class user_access
{
	/**
	 * Кэш для метода GetUser()
	 *
	 * @var array
	 */
	var $CacheGetUser = array();

	/**
	* Вставка и обновление информации о типе пользователей
	*
	* @param array $param массив параметров
	* - $param['users_type_id'] идентификатор типа пользователей
	* - $param['site_id'] идентификатор сайта, к которому принадлежит тип пользователей
	* - $param['users_type_name'] название типа пользователей
	* - $param['users_type_comment'] описание типа пользователей
	* - $param['users_type_root_dir'] название корневой директории, выше которой не могут подниматься пользователи данного типа
	*
	* @return mixed идентификатор добавленной/обновленной информации о типе пользователей в случае успешного выполнения или false в противном случае
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $param['users_type_id'] = 0;
	* $param['site_id'] = CURRENT_SITE;
	* $param['users_type_name'] = 'new_users';
	* $param['users_type_comment'] = 'описание типа пользователей';
	* $param['users_type_root_dir'] ='/images/';
	*
	* $newid = $user_access->insert_user_type($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function insert_user_type($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['users_type_id']) || $param['users_type_id'] == 0)
		{
			$param['users_type_id'] = NULL;
		}

		$user_group = Core_Entity::factory('User_Group', $param['users_type_id']);

		$user_group->name = trim(Core_Type_Conversion::toStr($param['users_type_name']));
		$user_group->comment = trim(Core_Type_Conversion::toStr($param['users_type_comment']));
		$user_group->root_dir = trim(Core_Type_Conversion::toStr($param['users_type_root_dir']));

		if (is_null($param['users_type_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$user_group->user_id = $param['users_id'];
		}

		$user_group->site_id = isset($param['site_id'])
			? Core_Type_Conversion::toInt($param['site_id'])
			: CURRENT_SITE;

		$user_group->save();

		return $user_group->id;
	}

	/**
	* УСТАРЕВШИЙ! Метод вставки и обновления информации о пользователе
	*
	* @param int $type - параметр, определяющий производится вставка или обновление информации о пользователе
	* @param int $users_id - идентификатор пользователя, для которого обновляется информация (при вставке равен 0)
	* @param int $users_type_id - идентификатор типа пользователей, к которому относится пользователь, для которого добавляется/обновляется информация
	* @param string $users_name - имя пользователя
	* @param string $users_password - пароль пользователя
	* @param int $users_superuser - параметр,  определяющий, является пользователь сурерюзером или нет (0 - обычный пользователь, 1 - суперюзер)
	* @return mixed идентификатор добавленной/обновленной информации о пользователе в случае успешного выполнения метода, 0 или false в противном случае (false - при вставке/обновлении пользователя с именем уже имеющимся в базе данных, 0 - в случае ошибки выполнения запроса)
	* @access private
	*/
	function insert_user($type,$users_id,$users_type_id,$users_name,$users_password, $users_superuser=0)
	{
		$param = array();

		if (intval($type) == 1)
		{
			$param['users_id'] = $users_id;
		}
		$param['users_type_id'] = $users_type_id;
		$param['users_name'] = $users_name;
		$param['users_password'] = $users_password;
		$param['users_superuser'] = $users_superuser;

		return $this->InsertUser($param);
	}

	/**
	* Вставка и обновление информации о пользователе
	*
	* @param array $param массив параметров
	* - int $param['users_id'] идентификатор пользователя
	* - int $param['users_type_id'] идентификатор группы пользователей
	* - string $param['users_name'] логин пользоваетля (логин)
	* - string $param['users_password'] пароль
	* - int $param['users_superuser'] пользователь - супер-юзер
	* - int $param['users_settings'] целое число с настройками пользователя
	* - str $param['users_name_text'] имя пользователя
	* - str $param['users_surname'] фамилия пользователя
	* - str $param['users_patronymic'] отчество пользователя
	* - str $param['users_email'] электронный адрес пользователя
	* - str $param['users_position'] должность пользователя
	* - str $param['users_icq'] номер icq пользователя
	* - str $param['users_site'] сайт пользователя
	* - int $param['users_only_own'] доступ пользователя только к тем элементам, которые он создал
	* - int $param['users_own_id'] пользователь, создавший пользователя
	* @return int идентификатор вставленного/обновленного пользователя
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $param['users_type_id'] = 5;
	* $param['users_name'] = 'vasya2';
	* $param['users_password'] = 'vasya';
	* $param['users_superuser'] = 0;
	* $param['users_settings'] = 0;
	* $param['users_name_text'] = 'Вася';
	* $param['users_surname'] = 'Пупкин';
	* $param['users_own_id'] = 0;
	*
	* $newid = $user_access->InsertUser($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function InsertUser($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['users_id']) || $param['users_id'] == 0)
		{
			$param['users_id'] = NULL;
		}

		$user = Core_Entity::factory('User', $param['users_id']);

		if (isset($param['user_group_id']))
		{
			$user->user_group_id = intval($param['user_group_id']);
		}

		// Дублироване логина пользователя
		if (isset($param['users_name']) && $user->id
			&& Core_Entity::factory('User')->getByLogin($param['users_name'])->id != $user->id)
		{
				return FALSE;
		}

		if (isset($param['users_password']))
		{
			$password = Core_Type_Conversion::toStr($param['users_password']);
			$user->password = !empty($password) ? Core_Hash::instance()->hash($password) : '';
		}

		if (isset($param['users_superuser']))
		{
			$user->superuser = intval($param['users_superuser']);
		}

		if (isset($param['users_settings']))
		{
			$user->settings = intval($param['users_settings']);
		}

		if (isset($param['users_name_text']))
		{
			$user->name = mb_substr(Core_Type_Conversion::toStr($param['users_name_text']), 0, 255);
		}

		if (isset($param['users_surname']))
		{
			$user->surname = mb_substr(Core_Type_Conversion::toStr($param['users_surname']), 0, 255);
		}

		if (isset($param['users_patronymic']))
		{
			$user->patronymic = mb_substr(Core_Type_Conversion::toStr($param['users_patronymic']), 0, 255);
		}

		if (isset($param['users_email']))
		{
			$user->email = mb_substr(Core_Type_Conversion::toStr($param['users_email']), 0, 255);
		}

		if (isset($param['users_position']))
		{
			$user->position = mb_substr(Core_Type_Conversion::toStr($param['users_position']), 0, 255);
		}

		if (isset($param['users_icq']))
		{
			$user->icq = mb_substr(Core_Type_Conversion::toStr($param['users_icq']), 0, 32);
		}

		if(isset($param['users_site']))
		{
			$user->site = mb_substr(Core_Type_Conversion::toStr($param['users_site']), 0, 255);
		}

		if (isset($param['users_only_own']))
		{
			$user->only_access_my_own = intval($param['users_only_own']);
		}

		if (is_null($param['users_id']) && isset($param['users_own_id']) && $param['users_own_id'])
		{
			$user->user_id = $param['users_own_id'];
		}

		$user->save();

		return $user->id;
	}

	function getArrayUser($oUser)
	{
		return array (
			'users_id' => $oUser->id,
			'users_type_id' => $oUser->user_group_id,
			'users_name' => $oUser->login,
			'users_password' => $oUser->password,
			'users_superuser' => $oUser->superuser,
			'users_settings' => $oUser->settings,
			'users_name_text' => $oUser->name,
			'users_surname' => $oUser->surname,
			'users_patronymic' => $oUser->patronymic,
			'users_email' => $oUser->email,
			'users_position' => $oUser->position,
			'users_icq' => $oUser->icq,
			'users_site' => $oUser->site,
			'users_only_own' => $oUser->only_access_my_own,
			'users_own_id' => $oUser->user_id
		);
	}

	function getArrayUserGroup($oUser_Group)
	{
		return array (
			'users_type_id' => $oUser_Group->id,
			'site_id' => $oUser_Group->site_id,
			'users_type_name' => $oUser_Group->name,
			'users_type_comment' => $oUser_Group->comment,
			'users_type_root_dir' => $oUser_Group->root_dir,
			'users_id' => $oUser_Group->user_id
		);
	}

	function getArrayUserGroupActionAccess($oUserGroupActionAccess)
	{
		return array (
			'users_type_avents_access_id' => $oUserGroupActionAccess->id,
			'users_type_id' => $oUserGroupActionAccess->user_group_id,
			'admin_forms_events_id' => $oUserGroupActionAccess->admin_form_action_id,
			'site_id' => $oUserGroupActionAccess->site_id,
			'users_id' => $oUserGroupActionAccess->user_id
		);
	}

	/**
	* Получение данных о группе пользователей центра администрирования
	*
	* @param int $users_type_id идентификатор группы пользователй центра администрирования
	* @return mixed массив с данными о группе или false в случае неудачи
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $users_type_id = 5;
	*
	* $row = $user_access->GetUserType($users_type_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetUserType($users_type_id)
	{
		$users_type_id = intval($users_type_id);

		$oUser_Group = Core_Entity::factory('User_Group')->find($users_type_id);

		if (!is_null($oUser_Group->id))
		{
			return $this->getArrayUserGroup($oUser_Group);
		}

		return FALSE;
	}

	/**
	* УСТАРЕВШИЙ. Метод выбора типа пользователей
	*
	* @param int $users_type_id - идентификатор типа пользователей
	* @version 3.2.3
	* @return mixed result в случае успешного выполнения, false в противном случае
	*/
	function select_user_type($users_type_id)
	{
		$users_type_id = intval($users_type_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'users_type_id'),
			'site_id',
			array('name', 'users_type_name'),
			array('comment', 'users_type_comment'),
			array('root_dir', 'users_type_root_dir'),
			array('user_id', 'users_id')
		)
		->from('user_groups')
		->where('deleted', '=', 0);

		// Выбрать все группы
		if ($users_type_id != -1)
		{
			$queryBuilder->where('id', '=', $users_type_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение информации о пользователе по его имени
	*
	* @param int $users_name имя пользователя центра администрирования
	* @return mixed массив с данными или false, если пользователь не найден
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $users_name = 'admin';
	*
	* $row = $user_access->GetUserByName($users_name);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetUserByName($users_name)
	{
		return $this->getArrayUser(
			Core_Entity::factory('User')->getByLogin($users_name)
		);
	}

	/**
	* Получение информации о пользователе
	*
	* @param int $users_id идентификатор пользователя центра администрирования
	* @return mixed массив с данными или false, если пользователь не найден
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $users_id = 19;
	*
	* $row = $user_access->GetUser($users_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetUser($users_id)
	{
		$users_id = intval($users_id);

		return $this->getArrayUser(
			Core_Entity::factory('User')->find($users_id)
		);
	}

	/**
	* Получение данных о всех пользователях центра администрирования
	*
	* @return resource
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $resource = $user_access->GetAllUsers();
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	*/
	function GetAllUsers()
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'users_id'),
			array('user_group_id', 'users_type_id'),
			array('login', 'users_name'),
			array('password', 'users_password'),
			array('superuser', 'users_superuser'),
			array('settings', 'users_settings'),
			array('name', 'users_name_text'),
			array('surname', 'users_surname'),
			array('patronymic', 'users_patronymic'),
			array('email', 'users_email'),
			array('position', 'users_position'),
			array('icq', 'users_icq'),
			array('site', 'users_site'),
			array('only_access_my_own', 'users_only_own'),
			array('user_id', 'users_own_id')
		)
		->from('users')
		->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	* УСТАРЕВШИЙ. Получение информации о пользователе
	*
	* @param int $users_id - идентификатор пользователя
	* @return mixed ассоциативный массив с информацией о пользователе в случае успешного выполнения, false в противном случае
	*/
	function select_user($users_id)
	{
		$users_id = intval($users_id);
		return $this->getArrayUser(
			Core_Entity::factory('User', $users_id)
		);
	}

	/**
	* Получение информации о типах доступа пользователей
	*
	* @param mixed $users_access_id - идентификатор типа доступа пользователей, если $users_access_id равен -1 получаем информацию о всех типах доступа пользователей
	* @return mixed result в случае успешного выполнения метода, false в противном случае
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $users_access_id = 1;
	*
	* $row = $user_access->select_user_access($users_access_id);
	*
	* // Распечатаем результат
	* print_r (mysql_fetch_assoc($row));
	* ?>
	* </code>
	*/
	function select_user_access($users_access_id)
	{
		$users_access_id = intval($users_access_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'users_access_id'),
			array('user_group_id', 'users_type_id'),
			array('module_id', 'modules_id'),
			'site_id',
			array('user_id', 'users_id')
			)
		->from('user_modules');
		//->where('deleted', '=', 0) // deleted нет у сущности

		// $users_access_id = -1  -  выбрать все
		if ($users_access_id != -1)
		{
			$queryBuilder->where('id', '=', $users_access_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Удаление типа пользователей
	*
	* @param int $users_type_id - идентификатор типа пользователей
	* @return boolean true в случае успешного выполнения, false в противном случае
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $users_type_id = 6;
	*
	* $result = $user_access->del_user_type($users_type_id);
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
	*/
	function del_user_type($users_type_id)
	{
		Core_Entity::factory('User_Group', $users_type_id)->markDeleted();
		return TRUE;
	}

	/**
	* Удаление пользователя
	*
	* @param int $users_id - идентификатор удаляемого пользователя
	* @return boolean true в случае успешного выполнения, false в противном случае
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $users_id = 22;
	*
	* $result = $user_access->del_user($users_id);
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
	function del_user($users_id)
	{
		Core_Entity::factory('User', $users_id)->markDeleted();
		return TRUE;
	}

	/**
	* Получение списка типов пользователей
	*
	* @param int $site_id идентификатор сайта, которому принадлежит тип пользователей, если false - учитываются все сайты
	* @return resource с информацией о типах пользователей
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $site_id = CURRENT_SITE;
	*
	* $resource = $user_access->GetAllUserTypes($site_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	*/
	function GetAllUserTypes($site_id)
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'users_type_id'),
			'site_id',
			array('name', 'users_type_name'),
			array('comment', 'users_type_comment'),
			array('root_dir', 'users_type_root_dir'),
			array('user_id', 'users_id')
		)
		->from('user_groups')
		->where('deleted', '=', 0);

		if ($site_id !== false)
		{
			$queryBuilder->where('site_id', '=', $site_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Вставка/обновление информации о доступе группы пользователей к модулю
	*
	* @param array $param ассоциативный массив параметров
	* - int $param['users_type_id'] идентификатор группы пользователей
	* - int $param['users_access_value'] параметр, определяющий доступность модуля группе пользователей (1 - модуль доступен, 0 - модуль недоступен)
	* - int $param['modules_id'] идентификатор модуля системы управления, для которого устанавливается доступ
	* - int $param['site_id'] идентификатор сайта
	* - int $param['users_id'] идентификатор пользователя
	* @return mixed идентификатор доступа группы пользователей к модулю системы управления
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $param['users_type_id'] = 5;
	* $param['modules_id'] = 54;
	* $param['site_id'] = CURRENT_SITE;
	* $param['users_id'] = 21;
	*
	* $result = $user_access->SetUsersTypeAccess($param);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function SetUsersTypeAccess($param)
	{
		$param = Core_Type_Conversion::toArray($param);
		$users_type_id = Core_Type_Conversion::toInt($param['users_type_id']);
		$modules_id = Core_Type_Conversion::toInt($param['modules_id']);
		$site_id = Core_Type_Conversion::toInt($param['site_id']);
		$users_access_value = Core_Type_Conversion::toInt($param['users_access_value']);

		$oUser_Group = Core_Entity::factory('User_Group', $users_type_id);

		$oUser_Module = $oUser_Group->User_Modules->getBySiteAndModule($site_id, $module_id);

		// Запрещаем доступ к модулю
		if (!$users_access_value)
		{
			if ($oUser_Module)
			{
				$oUser_Module->delete();
			}

			return TRUE;
		}
		// Разрешаем доступ к модулю
		else
		{
			if (is_null($oUser_Module))
			{
				$oUser_Module = Core_Entity::factory('User_Module');
				$oUser_Module->site_id = $site_id;
				$oUser_Module->module_id = $module_id;
				$oUser_Group->add($oUser_Module);
			}

			return $oUser_Module->id;
		}
	}

	/**
	* Установление/снятие доступа группы пользователей центра администрирования к событию формы центра администрирования
	*
	* @param array $param массив параметров
	* - $param['access_value'] значение доступа группы пользователей к событию формы (0 - событие не доступно, 1 - событие доступно)
	* - $param['users_type_id'] идентификатор группы пользователей центра администрирования
	* - $param['admin_form_event_id'] идентификатор события формы центра администрирования
	* - $param['site_id'] идентификатор сайта
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $param['access_value'] = 1;
	* $param['users_type_id'] = 5;
	* $param['admin_form_event_id'] = 207;
	* $param['site_id'] = CURRENT_SITE;
	*
	* $result = $user_access->SetUsersTypeEventAccess($param);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function SetUsersTypeEventAccess($param)
	{
		if (!isset($param['access_value']) || $param['access_value'] > 1 || $param['access_value'] < 0)
		{
			return FALSE;
		}

		$access_value = intval($param['access_value']);

		if (!isset($param['users_type_id']))
		{
			return FALSE;
		}
		else
		{
			$users_type_id = intval($param['users_type_id']);
		}

		if (!isset($param['admin_form_event_id']))
		{
			return FALSE;
		}
		else
		{
			$admin_form_event_id = intval($param['admin_form_event_id']);
		}

		if (!isset($param['site_id']))
		{
			$site_id = CURRENT_SITE;
		}
		else
		{
			$site_id = intval($param['site_id']);
		}

		if (isset($param['users_id']))
		{
			$users_id = intval($param['users_id']);
		}
		else
		{
			$kernel = & singleton('kernel');
			$users_id = $kernel->GetCurrentUser();
		}

		$oUser_Group = Core_Entity::factory('User_Group', $users_type_id);

		$oUser_Group_Action_Access = $oUser_Group->User_Group_Action_Accesses->getBySiteAndAction($site_id, $admin_form_event_id);

		if (!$access_value)
		{
			if ($oUser_Group_Action_Access)
			{
				$oUser_Group_Action_Access->delete();
			}

			return TRUE;
		}
		else
		{
			if (is_null($oUser_Group_Action_Access))
			{
				$oUser_Group_Action_Access = Core_Entity::factory('User_Module');
				$oUser_Group_Action_Access->site_id = $site_id;
				$oUser_Group_Action_Access->admin_form_action_id = $admin_form_event_id;
				$oUser_Group->add($oUser_Group_Action_Access);
			}

			return $oUser_Group_Action_Access->id;
		}
	}

	/**
	* Определение доступности действия формы центра администрирования для группы пользователей
	*
	* @param array $param массив параметров
	* - $param['users_type_id'] идентификатор группы пользователей
	* - $param['admin_form_event_id'] идентификатор действия формы
	* - $param['site_id'] идентификатор сайта (по умолчанию используется идентификатор текущего сайта)
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $param['users_type_id'] = 5;
	* $param['admin_form_event_id'] = 207;
	* $param['site_id'] = CURRENT_SITE;
	*
	* $result = $user_access->IssetUsersTypeEventAccess($param);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function IssetUsersTypeEventAccess($param)
	{
		if (!isset($param['users_type_id']))
		{
			return FALSE;
		}
		else
		{
			$users_type_id = intval($param['users_type_id']);
		}

		if (!isset($param['admin_form_event_id']))
		{
			return FALSE;
		}
		else
		{
			$admin_form_event_id = intval($param['admin_form_event_id']);
		}

		if (!isset($param['site_id']))
		{
			$site_id = CURRENT_SITE;
		}
		else
		{
			$site_id = intval($param['site_id']);
		}

		$oUser_Group = Core_Entity::factory('User_Group', $users_type_id);

		$oUser_Group_Action_Access = $oUser_Group->User_Group_Action_Accesses->getBySiteAndAction($site_id, $admin_form_event_id);

		return !is_null($oUser_Group_Action_Access->id);
	}

	/**
	* Определение доступности действия формы центра администрирования для пользователя
	*
	* @param array $param массив параметров
	* - $param['user_id'] идентификатор пользователя
	* - $param['admin_form_event_id'] идентификатор действия формы
	* - $param['site_id'] идентификатор сайта (по умолчанию используется идентификатор текущего сайта)
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $param['user_id'] = 21;
	* $param['admin_form_event_id'] = 207;
	* $param['site_id'] = CURRENT_SITE;
	*
	* $result = $user_access->IssetUserEventAccess($param);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function IssetUserEventAccess($param)
	{
		if (!isset($param['user_id']))
		{
			return FALSE;
		}
		else
		{
			$user_id = intval($param['user_id']);
		}

		if ($user_info = $this->GetUser($user_id))
		{
			$param['users_type_id'] = $user_info['users_type_id'];
			return $this->IssetUsersTypeEventAccess($param);
		}

		return FALSE;
	}

	/**
	* Определение возможности доступа пользователя к управлению объектом
	*
	* @param int $object_user_id идентификатор пользователя, которому принадлежит объект
	* @param int $users_id идентификатор текущего пользователя, если не передан - определяется автоматически
	* @return boolean
	* <code>
	* <?php
	* $user_access = new user_access();
	* $InformationSystem = new InformationSystem();
	*
	* $group_id = 2;
	*
	* $row = $InformationSystem->GetInformationGroup($group_id);
	* $object_user_id = $row['users_id'];
	*
	* $kernel = new kernel();
	* $users_id = $kernel->GetCurrentUser();
	*
	* $result = $user_access->IssetUserAccessForObject($object_user_id, $users_id);
	*
	* if ($result)
	* {
	*	 echo "Пользователь имеет доступ к объекту";
	* }
	* else
	* {
	* 	echo "Пользователь не имеет доступ к объекту";
	* }
	* ?>
	* </code>
	*/
	function IssetUserAccessForObject($object_user_id, $users_id = FALSE)
	{
		$object_user_id = intval($object_user_id);

		if ($users_id === FALSE && isset($_SESSION['current_users_id']))
		{
			$users_id = intval($_SESSION['current_users_id']);
		}
		else
		{
			$users_id = intval($users_id);
		}

		// Проверяем наличие в кэше в памяти
		if (isset($this->CacheGetUser[$object_user_id][$users_id]))
		{
			return $this->CacheGetUser[$object_user_id][$users_id];
		}

		$return = FALSE;

		$user_access_row = $this->GetUser($users_id);

		// Информация о пользователе найдена
		if ($user_access_row)
		{
			// Пользователь имеет доступ ко всем объектам или только к своим и объект принадлежит ему или никому
			if (
			$user_access_row['users_superuser'] == 1 // Superuser
			|| $user_access_row['users_only_own'] == 0 // Доступ ко всем
			|| $user_access_row['users_only_own'] == 1
			&& ($object_user_id == 0 || $object_user_id == $users_id
			)
			)
			{
				$return = TRUE;
			}
		}

		// Сохраняем в кэше
		$this->CacheGetUser[$object_user_id][$users_id] = $return;

		return $return;
	}

	function MessageOnlyOwn()
	{
		show_error_message(Core::_('user_module.error_object_owned_another_user'));
	}

	/**
	* Определение информации о правах доступа пользователя к модулю
	*
	* @param int $users_type_id индентификатор группы пользователей центра администрирования
	* @param int $modules_id идентификатор модуля
	* @param int $site_id идентификатор сайта, если не передан, определяется автоматически
	* @return mixed массив с данными или false
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $users_type_id = 5;
	* $modules_id = 54;
	* $site_id = CURRENT_SITE;
	*
	* $result = $user_access->GetUsersSuccess($users_type_id, $modules_id, $site_id);
	*
	* if ($result)
	* {
	*	echo "Пользователь имеет доступ к модулю";
	* }
	* else
	* {
	*	echo "Пользователь не имеет доступ к модулю";
	* }
	* ?>
	* </code>
	*/
	function GetUsersSuccess($users_type_id, $modules_id, $site_id = false)
	{
		$users_type_id = intval($users_type_id);
		$modules_id = intval($modules_id);

		if ($site_id === false)
		{
			$site_id = CURRENT_SITE;
		}
		else
		{
			$site_id = intval($site_id);
		}

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'users_access_id'),
			array('user_group_id', 'users_type_id'),
			array('module_id', 'modules_id'),
			'site_id',
			array('user_id', 'users_id')
			)
			->from('user_modules')
			->where('user_group_id', '=', $users_type_id)
			->where('module_id', '=', $modules_id)
			->where('site_id', '=', $site_id)
			->limit(1);

		return $queryBuilder->execute()->current();
	}

	/**
	* Определение информации о правах доступа пользователя к действию
	*
	* @param int $users_type_id индентификатор группы пользователей центра администрирования
	* @param int $admin_forms_events_id идентификатор действия
	* @param int $site_id идентификатор сайта, если не передан, определяется автоматически
	* @return mixed массив с данными или false
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $users_type_id = 5;
	* $admin_forms_events_id = 207;
	* $site_id = CURRENT_SITE;
	*
	* $row = $user_access->GetUsersSuccessEvent($users_type_id, $admin_forms_events_id, $site_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetUsersSuccessEvent($users_type_id, $admin_forms_events_id, $site_id = false)
	{
		$users_type_id = intval($users_type_id);
		$admin_forms_events_id = intval($admin_forms_events_id);

		if ($site_id === FALSE)
		{
			$site_id = CURRENT_SITE;
		}
		else
		{
			$site_id = intval($site_id);
		}

		$oUser_Group_Action_Accesses = Core_Entity::factory('User_Group', $users_type_id)
			->User_Group_Action_Accesses
			->getBySiteAndAction($site_id, $admin_forms_events_id);

		if ($oUser_Group_Action_Accesses)
		{
			return $this->getArrayUserGroupActionAccess($oUser_Group_Action_Accesses);
		}

		return FALSE;
	}

	/**
	* Установление/снятие доступа группы пользователей центра администрирования к событию формы центра администрирования
	*
	* @param int $admin_forms_id идентификатор формы центра администрирования
	* @param int $users_type_id идентификатор группы пользователей центра администрирования
	* @param int $access_value значение доступа группы пользователей к событию формы (0 - событие не доступно, 1 - событие доступно)
	* @param int $site_id идентификатор сайта
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $admin_forms_id = 104;
	* $users_type_id = 5;
	* $access_value = 1;
	* $site_id = CURRENT_SITE;
	*
	* $user_access->SetUsersTypeAccessFormEvents($admin_forms_id, $users_type_id, $access_value, $site_id);
	* ?>
	* </code>
	*/
	function SetUsersTypeAccessFormEvents($admin_forms_id, $users_type_id, $access_value, $site_id)
	{
		$admin_forms_id = Core_Type_Conversion::toInt($admin_forms_id);
		$users_type_id = Core_Type_Conversion::toInt($users_type_id);
		$access_value = Core_Type_Conversion::toInt($access_value);

		$AdminForms = new admin_forms();

		$events_data_array = $AdminForms->GetAllAdminFormEvents($admin_forms_id);

		if ($events_data_array)
		{
			foreach ($events_data_array as $data_row)
			{
				$this->SetUsersTypeEventAccess(array(
					'access_value' => $access_value,
					'users_type_id' => $users_type_id,
					'admin_form_event_id' => $data_row['admin_forms_events_id'],
					'site_id' => $site_id
				));
			}
		}
	}

	/**
	* Получение списка неудачных доступов к BackOffice для конкретного ip-адреса за последние 24 часа
	*
	* @param string $user_ip IP-адрес пользователя
	* @param string $current_date текущая дата
	* @return resource
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $user_ip = $_SERVER['REMOTE_ADDR'];
	* $current_date = time();
	*
	* $resource = $user_access->SelectUserDeniedAccess($user_ip, $current_date);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	*/
	function SelectUserDeniedAccess($user_ip, $current_date)
	{
		// преобразуем полученную дату из UNIX формата в формат MySQL;
		$date = Core_Date::timestamp2sql($current_date - 60*60*24);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'users_access_denied_id'),
			array('datetime', 'users_access_denied_datetime'),
			array('ip', 'users_access_denied_ip')
		)
			->from('user_accessdenieds')
			->where('datetime', '>=', $date)
			->where('ip', '=', $user_ip)
			->orderBy('users_access_denied_datetime', 'DESC');

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Запись в базу информации о неудачной попытке входа в BackOffice
	*
	* @param string $user_ip IP-адрес пользователя
	* @param string $current_date текущая дата в формате UNIX Timestamp
	* @return resource
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $user_ip = $_SERVER['REMOTE_ADDR'];
	* $current_date = time();
	*
	* $resource = $user_access->InsertUserDeniedAccess($user_ip, $current_date);
	*
	* if ($resource)
	* {
	* 	echo "Информация записана";
	* }
	* else
	* {
	*	echo "Информация не записана";
	* }
	* ?>
	* </code>
	*/
	function InsertUserDeniedAccess($user_ip, $current_date)
	{
		$oUser_Accessdenied = Core_Entity::factory('User_Accessdenied');
		$oUser_Accessdenied->datetime = Core_Date::timestamp2sql($current_date);
		$oUser_Accessdenied->ip = $user_ip;
		$oUser_Accessdenied->save();

		return TRUE;
	}

	/**
	* Удаление неудачных попыток входа в BackOffice за период ранее заданного числа секунд
	*
	* @param string $current_date текущая дата
	* @param int $delta_time число секунд, по умолчанию сутки, от текущего момента времени
	* @return resource
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $current_date = time();
	* $delta_time = 86400;
	*
	* $resource = $user_access->DeleteUserDeniedAccessBefore($current_date, $delta_time);
	*
	* if ($resource)
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
	function DeleteUserDeniedAccessBefore($current_date, $delta_time = 86400)
	{
		$current_date = intval($current_date);
		$delta_time = intval($delta_time);

		$date = Core_Date::timestamp2sql($current_date - $delta_time);

		$oUser_Accessdenied = Core_Entity::factory('User_Accessdenied');
		$oUser_Accessdenied->queryBuilder()
			->clear()
			->where('datetime', '<', $date);

		$aUser_Accessdenieds = $oUser_Accessdenied->findAll();

		foreach ($aUser_Accessdenieds as $oUser_Accessdenied)
		{
			$oUser_Accessdenied->delete();
		}

		return TRUE;
	}

	/**
	* Удаление неудачных попыток входа в BackOffice для заданного IP-адреса
	*
	* @param string $ip IP-адрес
	* @return resource
	* <code>
	* <?php
	* $user_access = new user_access();
	*
	* $ip = $_SERVER['REMOTE_ADDR'];
	*
	* $resource = $user_access->DeleteUserDeniedAccessIP($ip);
	*
	* if ($resource)
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
	function DeleteUserDeniedAccessIP($ip)
	{
		$oUser_Accessdenied = Core_Entity::factory('User_Accessdenied');
		$oUser_Accessdenied->queryBuilder()
			->clear()
			->where('ip', '=', $ip);

		$aUser_Accessdenieds = $oUser_Accessdenied->findAll();

		foreach ($aUser_Accessdenieds as $oUser_Accessdenied)
		{
			$oUser_Accessdenied->delete();
		}

		return TRUE;
	}

	/**
	 * Копирование информации о группе пользователей центра администрирования
	 *
	 * @param int $users_type_id идентификатор группы пользователей центра администрирования
	 * @param int $site_id идентификатор сайта, если не передан, используется текущий сайт
	 * @return int идентификатор скопированного элемента
	 */
	function CopyUserType($users_type_id, $site_id = FALSE)
	{
		$oUser_Group = Core_Entity::factory('User_Group', $users_type_id);

		$oNew_User_Group = $oUser_Group->copy();

		if ($site_id)
		{
			$oNew_User_Group->site_id = $site_id;
			$oNew_User_Group->save();
		}

		return TRUE;
	}
}