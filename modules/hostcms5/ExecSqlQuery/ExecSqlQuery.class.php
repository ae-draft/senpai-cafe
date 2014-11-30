<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "SQL-запросы".
 * 
 * Файл: /modules/ExecSqlQuery/ExecSqlQuery.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class ExecSqlQuery
{
	/**
	* Выполнение SQL-запроса
	*
	* @param string $query SQL-запрос
	* <code>
	* <?php
	* $ExecSqlQuery = new ExecSqlQuery();
	*
	* $site_id = intval(CURRENT_SITE);
	*
	* $query = "INSERT INTO `structure_table` (  `menu_id` , `templates_id` , `data_templates_id` , `site_id` , `documents_id` , `lib_id` , `structure_parent_id` , `structure_show` , `structure_menu_name` , `structure_title` , `structure_description` , `structure_keywords` , `structure_external_link` , `structure_order` , `structure_path_name` , `structure_type` , `structure_access` , `structure_activity` , `structure_allow_indexation` , `structure_change_frequency` , `structure_priority` , `users_id` )
VALUES (
 '1', '1', '1', '{$site_id}', NULL , '0', 0 , 1 , 'Имя в меню', 'Заголовок страницы', NULL , NULL , NULL , 0 , 'my_path_name' , 0 , '-1', '1', '1', '0', '0', '0'
);
	*
	* -- Далее можно указывать следующие запросы";
	*
	* $ExecSqlQuery->ExecDump($query);
	* ?>
	* </code>
	* @return int количество выполненных запросов или false
	*/
	function ExecDump($query)
	{
		return Sql_Controller::instance()->execute($query);
	}

	/**
	* Получение списка таблиц, разделенных запятыми.
	* 
	* @return str список таблиц в виде: `table1`,`table2`,`table3` либо false.
	* <code>
	* <?php
	* $ExecSqlQuery = new ExecSqlQuery();
	*
	* $list_table = $ExecSqlQuery->GetTableNames();
	*
	* // Распечатаем результат
	* echo $list_table;
	* ?>
	* </code>	
	*/
	function GetTableNames()
	{
		$query = "SHOW TABLES FROM `" . DB_NAME . "`";

		$DataBase = & singleton('DataBase');
		$resource = $DataBase->query($query);
		
		if (!$resource)
		{
			return FALSE;
		}
		
		$m = array();

		while ($row = mysql_fetch_row($resource)) 
		{
			$m[] = "`{$row[0]}`";
		}
		return  implode(',', $m);
	}
}