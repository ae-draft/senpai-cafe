<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для работы с СУБД.
 *
 * Файл: /modules/Kernel/mysql.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */

/**
 * @access private
 */
class MySQL extends DataBase {}

/**
 * Ядро, класс для работы с СУБД
 */
class DataBase
{
	var $_dataBase = NULL;

	/**
	 * Количество выбранных строк в последнем запросе
	 *
	 * @var int
	 */
	var $count_row;

	/**
	 * результат выполнения SQL запроса
	 * @access private
	 */
	var $result;

	var $db_connect = false;
	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->_dataBase = Core_DataBase::instance();
	}
	
	function setDataBase()
	{
		if (is_null($this->_dataBase))
		{
			$this->_dataBase = Core_DataBase::instance();
		}
	}
		
	/**
	 * Метод соединения с БД. В случае невозможности соединения с БД или выбора БД - прерывает работу программы.
	 * <code>
	 * <?php
	 * $DataBase = & singleton('DataBase');
	 *
	 * $result = $DataBase->db_connect();
	 *
	 * if ($result)
	 * {
	 * 	echo "Соединение с БД прошло успешно";
	 * }
	 * else
	 * {
	 * 	echo "Соединение с БД не выполнено";
	 * }
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function db_connect()
	{
		$this->setDataBase();
	
		// Соединение с БД
		$this->db_connect = $this->_dataBase->getConnection();

		if (defined('SITE_TIMEZONE'))
		{
			mysql_query("SET LOCAL time_zone = '" . quote_smart(SITE_TIMEZONE) . "'", $this->db_connect);
		}
		return TRUE;
	}

	function __destruct()
	{
		//$this->db_close();
	}
	
	/**
	 * Закрывает соединение $this->db_connect
	 *
	 */
	function db_close()
	{
		$this->_dataBase->disconnect();
	}

	/**
	 * Метод выбор данных из БД. Количество элементов, выбранных методом, можно получить с помощью метода get_count_row()
	 *
	 * @param string $query SQL-запрос
	 * <code>
	 * <?php
	 * $DataBase = & singleton('DataBase');
	 *
	 * $query = "SELECT * FROM `structures` WHERE `id` = ".CURRENT_STRUCTURE_ID;
	 *
	 * $result = $DataBase->select($query);
	 *
	 * if ($result)
	 * {
	 * 	// Распечатаем результат
	 * 	while($row = mysql_fetch_assoc($result))
	 * 	{
	 *		print_r($row);
	 * 	}
	 * }
	 * else
	 * {
	 * 	echo "Ошибка выполнения запроса";
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function select($query)
	{
		$this->_dataBase->setQueryType(0);

		$this->query($query);

		$this->count_row = $this->_dataBase->getNumRows();
		return $this->result;
	}

	/**
	 * Метод выполнения SQL-запроса
	 *
	 * @param string $query SQL-запрос
	 * <code>
	 * <?php
	 * $DataBase = & singleton('DataBase');
	 *
	 * $query = "SELECT * FROM `structures` WHERE `id` = ".CURRENT_STRUCTURE_ID;
	 *
	 * $result = $DataBase->query($query);
	 *
	 * if ($result)
	 * {
	 * 	// Распечатаем результат
	 * 	while($row = mysql_fetch_assoc($result))
	 * 	{
	 *		print_r($row);
	 * 	}
	 * }
	 * else
	 * {
	 * 	echo "Ошибка выполнения запроса";
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function query($query)
	{
		try
		{
			$this->result = $this->_dataBase->query($query)->getResult();
		}
		catch (Exception $e)
		{
			$this->result = FALSE;
			Core_Message::show($e->getMessage(), 'error');
		}
		
		return $this->result;
	}

	/**
	 * Метод возвращает количество строк в последнем запросе
	 * <code>
	 * <?php
	 * $DataBase = & singleton('DataBase');
	 *
	 * $count = $DataBase->get_count_row();
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return int кличество строк, возвращенных последним запросом, выполненным методом select()
	 */
	function get_count_row()
	{
		return $this->count_row;
	}

	/**
	 * Возвращает ID, сгенерированный колонкой с AUTO_INCREMENT последним запросом INSERT к серверу
	 *
	 * @return unknown
	 */
	function GetLastInsertId()
	{
		return mysql_insert_id($this->db_connect);
	}

	/**
	 * Вставка записи в таблицу.
	 *
	 * <br />Пример использования:
	 * <code>
	 * <?php
	 *
	 * $DataBase = & singleton('DataBase');
	 *
	 * $DataBase->Insert('my_table', array('my_table_field1' => 123, 'my_table_field2' => 'Значение "второго" поля'));
	 *
	 * // В результате будет выполнен следующий запрос:
	 * // INSERT INTO `my_table` (`my_table_field1`,`my_table_field2`) VALUES ('123', 'Значение \"второго\" поля')
	 *
	 * ?>
	 * </code>
	 *
	 * @param string $table имя таблицы
	 * @param array $fields массив полей, при этом кючи массива - имена полей, значения - значения полей
	 * @return resource
	 */
	function Insert($table, $fields)
	{
		if (empty($table) || !is_array($fields) || count($fields) == 0)
		{
			return false;
		}

		$sqlstr = "INSERT INTO `{$table}` (`" . implode('`,`', array_keys($fields)) . '`)';

		array_walk($fields, array('DataBase', 'filter_sql_strings'));

		$sqlstr .= " VALUES ('" . implode("', '", $fields) . "')";

		return $this->query($sqlstr);
	}

	/**
	 * Обновление записей в таблицах.
	 *
	 * <br />Пример использования:
	 * <code>
	 * <?php
	 * $DataBase = & singleton('DataBase');
	 *
	 * $DataBase->Update('my_table', array('my_table_field1' => 123, 'my_table_field2' => 'Значение "второго" поля'), "`my_table_field3` = '777'");
	 *
	 * // В результате будет выполнен следующий запрос:
	 * // UPDATE `my_table` SET `my_table_field1` = '123', `my_table_field2` = 'Значение \"второго\" поля' WHERE `my_table_field3` = '777'
	 * ?>
	 * </code>
	 *
	 * @param string $table имя таблицы
	 * @param array $fields массив полей, при этом кючи массива - имена полей, значения - значения полей
	 * @param string $expr условие для WHERE
	 * @return resource
	 */
	function Update($table, $fields, $expr = '')
	{
		if (empty($table) || !is_array($fields) || count($fields) == 0)
		{
			return false;
		}

		$sqlstr = "UPDATE `{$table}` SET ";

		array_walk($fields, array('DataBase', 'filter_sql_strings'));

		$fields_array = array();

		foreach ($fields as $key => $value)
		{
			$fields_array[] = "`$key` = '$value'";
		}

		$sqlstr .= implode(', ', $fields_array);

		if (strlen(trim($expr)) > 0)
		{
			$sqlstr .= " WHERE {$expr}";
		}

		return $this->query($sqlstr);
	}

	/**
	 * Удаление записей из таблиц.
	 *
	 * <br />Пример использования:
	 * <code>
	 * <?php
	 *
	 * $DataBase = & singleton('DataBase');
	 * $DataBase->Delete('my_table', "`my_table_field3` = '777'");
	 *
	 * // В результате будет выполнен следующий запрос:
	 * // DELETE FROM `my_table` WHERE `my_table_field3` = '777'
	 *
	 * ?>
	 * </code>
	 *
	 * @param string $table имя таблицы
	 * @param string $expr условие для WHERE
	 * @return resource
	 */
	function Delete($table, $expr = '')
	{
		$query = "DELETE FROM `{$table}`";

		if (strlen(trim($expr)) > 0)
		{
			$query .= " WHERE {$expr}";
		}

		return $this->query($query);
	}

	/**
	 * Служебный метод для экранирования данных
	 *
	 * @param mixed $val ссылка на значение массива
	 * @param mixed $key ключ
	 * @access private
	 */
	function filter_sql_strings(&$val, &$key)
	{
		$val = quote_smart($val);
	}

	function HighlightSql($sql)
	{
		return Core_DataBase::instance()->highlightSql($sql);
	}
}
