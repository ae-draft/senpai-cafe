<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * INSERT Database Abstraction Layer (DBAL)
 *
 * http://dev.mysql.com/doc/refman/5.5/en/insert.html
 *
 * <code>
 * // Sample 1
 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName')
 * 	->columns('column1', 'column2', 'column3')
 * 	->values('value1', 'value2', 11)
 * 	->values('value3', 'value4', 17)
 * 	->values('value5', 'value6', 19)
 *	->execute();
 * </code>
 *
 * <code>
 * // Sample 2
 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName', array('column1' => 'value1', 'column2' => 'value2'))
 *	->execute();
 * </code>
 *
 * @package HostCMS 6\Core\Querybuilder
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_QueryBuilder_Insert extends Core_QueryBuilder_Statement
{
	/**
	 * Table name
	 * @var mixed
	 */
	protected $_into = NULL;

	/**
	 * Columns
	 * @var array
	 */
	protected $_columns = array();

	/**
	 * Array of values
	 * @var array
	 */
	protected $_values = array();

	/**
	 * Use LOW_PRIORITY
	 * @var mixed
	 */
	protected $_priority = NULL;

	/**
	 * Use IGNORE
	 * @var mixed
	 */
	protected $_ignore = FALSE;

	/**
	 * DataBase Query Type
	 * 1 - INSERT
	 */
	protected $_queryType = 1;

	/**
	 * Constructor.
	 * @param array $args list of arguments
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName');
	 * </code>
	 *
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName', array('column1' => 'value1', 'column2' => 'value2'));
	 * </code>
	 * @see into()
	 */
	public function __construct(array $args = array())
	{
		// Set table name
		call_user_func_array(array($this, 'into'), $args);

		// Set columns and values
		if (count($args) > 1 && is_array($args[1]))
		{
			$this->_columns = array_merge($this->_columns, array_keys($args[1]));
			$this->_values[] = array_values($args[1]);
		}

		return parent::__construct($args);
	}

	/**
	 * Set LOW_PRIORITY
	 *
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName')->lowPriority();
	 * </code>
	 * @return Core_QueryBuilder_Insert
	 */
	public function lowPriority()
	{
		$this->_priority = 'LOW_PRIORITY';
		return $this;
	}

	/**
	 * Set DELAYED
	 *
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName')->delayed();
	 * </code>
	 * @return Core_QueryBuilder_Insert
	 */
	public function delayed()
	{
		$this->_priority = 'DELAYED';
		return $this;
	}

	/**
	 * Set HIGH_PRIORITY
	 *
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName')->highPriority();
	 * </code>
	 * @return Core_QueryBuilder_Insert
	 */
	public function highPriority()
	{
		$this->_priority = 'HIGH_PRIORITY';
		return $this;
	}

	/**
	 * Set IGNORE
	 *
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName')->ignore();
	 * </code>
	 * @return Core_QueryBuilder_Insert
	 */
	public function ignore()
	{
		$this->_ignore = TRUE;
		return $this;
	}

	/**
	 * Set table name
	 * @param string $tableName table name
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert()->into('tableName');
	 * </code>
	 * @return Core_QueryBuilder_Insert
	 */
	public function into($tableName)
	{
		$this->_into = $tableName;
		return $this;
	}

	/**
	 * Add columns for INSERT
	 *
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName')
	 * 		->columns('column1', 'column2', 'column3');
	 * </code>
	 * @return Core_QueryBuilder_Insert
	 */
	public function columns()
	{
		$args = func_get_args();
		$this->_columns = array_merge($this->_columns, $args);

		return $this;
	}

	/**
	 * Set values for INSERT
	 *
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName')
	 * 	->columns('column1', 'column2', 'column3')
	 * 	->values('value1', 'value2', 11)
	 * 	->values('value3', 'value4', 17)
	 * 	->values('value5', 'value6', 19);
	 * </code>
	 * @return Core_QueryBuilder_Insert
	 */
	public function values()
	{
		$args = func_get_args();
		$this->_values[] = $args;

		return $this;
	}

	/**
	 * Clear values
	 *
	 * <code>
	 * $oCore_QueryBuilder_Insert = Core_QueryBuilder::insert('tableName')
	 * 	->columns('column1', 'column2', 'column3')
	 * 	->values('value1', 'value2', 11);
	 * $oCore_QueryBuilder_Insert->execute();
	 * $oCore_QueryBuilder_Insert->clearValues();
	 * $oCore_QueryBuilder_Insert->values('value3', 'value4', 17)
	 * 	->execute();
	 * </code>
	 * @return Core_QueryBuilder_Insert
	 */
	public function clearValues()
	{
		$this->_values = array();
		return $this;
	}

	/**
	 * Build the SQL query
	 *
	 * @return string The SQL query
	 */
	public function build()
	{
		$query = array('INSERT');

		if (!is_null($this->_priority))
		{
			$query[] = $this->_priority;
		}

		if ($this->_ignore)
		{
			$query[] = 'IGNORE';
		}

		$query[] = 'INTO ' . $this->_dataBase->quoteColumnName($this->_into);

		$query[] = "\n(" . implode(', ', $this->quoteColumns($this->_columns)) . ')';

		$query[] = "\nVALUES ";

		$aValues = array();
		foreach ($this->_values as $aValue)
		{
			$aValues[] = '(' . implode(', ',  $this->_quoteValues($aValue)) . ')';
		}

		$query[] = implode(",\n", $aValues);

		$sql = implode(' ', $query);

		return $sql;
	}
}