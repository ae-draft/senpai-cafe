<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Query builder. SELECT Database Abstraction Layer (DBAL)
 *
 * http://dev.mysql.com/doc/refman/5.5/en/select.html
 *
 * Subqueries
 * http://dev.mysql.com/doc/refman/5.5/en/subqueries.html
 *
 * The Subquery as Scalar Operand
 * http://dev.mysql.com/doc/refman/5.5/en/scalar-subqueries.html
 * <code>
 * // SELECT (SELECT MAX(`column2`) FROM `t2`) FROM `t1`
 * $oCore_QueryBuilder_Select2 = Core_QueryBuilder::select('MAX(column2)')->from('t2');
 * $oCore_QueryBuilder_Select = Core_QueryBuilder::select($oCore_QueryBuilder_Select2)->from('t1');
 * </code>
 *
 * Comparisons Using Subqueries
 * http://dev.mysql.com/doc/refman/5.5/en/comparisons-using-subqueries.html
 *
 * <code>
 * // SELECT * FROM `t1` WHERE `column1` = (SELECT MAX(`column2`) FROM `t2`)
 * $oCore_QueryBuilder_Select2 = Core_QueryBuilder::select('MAX(column2)')->from('t2');
 *
 * $oCore_QueryBuilder_Select = Core_QueryBuilder::select()->from('t1')
 * 	->where('column1', '=', $oCore_QueryBuilder_Select2);
 * </code>
 * @see Core_QueryBuilder_Selection
 *
 * @package HostCMS 6\Core\Querybuilder
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_QueryBuilder_Select extends Core_QueryBuilder_Selection
{
	/**
	 * DISTINCT
	 * @var bool
	 */
	protected $_distinct = FALSE;

	/**
	 * HIGH_PRIORITY
	 * @var mixed
	 */
	protected $_highPriority = NULL;

	/**
	 * STRAIGHT_JOIN
	 * @var mixed
	 */
	protected $_straightJoin = NULL;

	/**
	 * SQL_CALC_FOUND_ROWS
	 * @var mixed
	 */
	protected $_sqlCalcFoundRows = NULL;

	/**
	 * SELECT
	 * @var array
	 */
	protected $_select = array();

	/**
	 * FROM
	 * @var array
	 */
	protected $_from = array();

	/**
	 * JOIN
	 * @var array
	 */
	protected $_join = array();

	/**
	 * GROUP BY
	 * @var array
	 */
	protected $_groupBy = array();

	/**
	 * HAVING
	 * @var array
	 */
	protected $_having = array();

	/**
	 * Array of Core_QueryBuilder_Select's
	 * @var array
	 */
	protected $_union = array();

	/**
	 * UNION LIMIT
	 * @var mixed
	 */
	protected $_unionLimit = NULL;

	/**
	 * UNION OFFSET
	 * @var mixed
	 */
	protected $_unionOffset = NULL;

	/**
	 * DataBase Query Type
	 * 0 - SELECT
	 */
	protected $_queryType = 0;

	/**
	 * Constructor.
	 * <code>
	 * $oCore_QueryBuilder_Select = Core_QueryBuilder::select('id', array('tableName.name', 'aliasName'));
	 * </code>
	 *
	 * @param array $args list of arguments
	 * @see columns()
	 */
	public function __construct(array $args = array())
	{
		// Add columns for select
		call_user_func_array(array($this, 'columns'), $args);

		return parent::__construct($args);
	}

	/**
	 * Add columns for select
	 *
	 * <code>
	 * // SELECT `id`, `name`
	 * $Core_QueryBuilder_Select->columns('id', 'name');
	 *
	 * // SELECT `id`, `name` AS `aliasName`
	 * $Core_QueryBuilder_Select->columns('id', array('name', 'aliasName'));
	 *
	 * // SELECT `id`, `tablename`.`name` AS `aliasName`
	 * $Core_QueryBuilder_Select->columns('id', array('tablename.name', 'aliasName'));
	 * </code>
	 * @return Core_QueryBuilder_Select
	 */
	public function columns()
	{
		$args = func_get_args();
		$this->_select = array_merge($this->_select, $args);

		return $this;
	}

	/**
	 * Alias for columns()
	 *
	 * @see columns()
	 */
	public function select()
	{
		$args = func_get_args();
		return call_user_func_array(array($this, 'columns'), $args);
	}

	/**
	 * Set HIGH_PRIORITY
	 *
	 * <code>
	 * $oCore_QueryBuilder_Select = Core_QueryBuilder::select()->highPriority();
	 * </code>
	 * @return Core_QueryBuilder_Select
	 */
	public function highPriority()
	{
		$this->_highPriority = TRUE;
		return $this;
	}

	/**
	 * Set STRAIGHT_JOIN
	 *
	 * <code>
	 * $oCore_QueryBuilder_Select = Core_QueryBuilder::select()->straightJoin();
	 * </code>
	 * @return Core_QueryBuilder_Select
	 */
	public function straightJoin()
	{
		$this->_straightJoin = TRUE;
		return $this;
	}

	/**
	 * Set SQL_CALC_FOUND_ROWS
	 *
	 * <code>
	 * $oCore_QueryBuilder_Select = Core_QueryBuilder::select()->sqlCalcFoundRows();
	 * </code>
	 * @return Core_QueryBuilder_Select
	 */
	public function sqlCalcFoundRows()
	{
		$this->_sqlCalcFoundRows = TRUE;
		return $this;
	}

	/**
	 * Set DISTINCT and add columns for select
	 *
	 * @see columns()
	 * @return Core_QueryBuilder_Select
	 */
	public function selectDistinct()
	{
		$args = func_get_args();
		$this->distinct()->columns($args);

		return $this;
	}

	/**
	 * Set DISTINCT
	 *
	 * <code>
	 * $oCore_QueryBuilder_Select = Core_QueryBuilder::select()->distinct();
	 * </code>
	 * @param boolean $distinct DISTINCT option
	 * @return Core_QueryBuilder_Select
	 */
	public function distinct($distinct = TRUE)
	{
		$this->_distinct = $distinct;
		return $this;
	}

	/**
	 *
	 *
	 * <code>
	 * // FROM `tableName`
	 * $oCore_QueryBuilder_Select = $Core_QueryBuilder_Select->from('tableName');
	 *
	 * // FROM `tableName` AS `tableNameAlias`
	 * $oCore_QueryBuilder_Select = $Core_QueryBuilder_Select->from(array('tableName', 'tableNameAlias'));
	 * </code>
	 * @return Core_QueryBuilder_Select
	*/
	public function from()
	{
		$args = func_get_args();
		$this->_from = array_merge($this->_from, $args);
		return $this;
	}

	/**
	 * http://dev.mysql.com/doc/refman/5.5/en/join.html
	 * @param string $type join type
	 * @param string $table table name
	 * @param string $column column name
	 * @param string $expression expression
	 * @param string $value value
	 * @param string $additionalConditions additional conditions
	 * @return Core_QueryBuilder_Select
	 */
	protected function _join($type, $table, $column = NULL, $expression = NULL, $value = NULL, $additionalConditions = NULL)
	{
		$this->_join[] = array($type, $table, $column, $expression, $value, $additionalConditions);

		return $this;
	}

	/**
	 * INNER JOIN
	 *
	 * <code>
	 * // INNER JOIN `join1` USING (`join_field`)
	 * $Core_QueryBuilder_Select->join('join1', 'join_field');
	 *
	 * // INNER JOIN `jointable`.`join1` ON `join_field` = `join_field2`
	 * $Core_QueryBuilder_Select->join('jointable.join1', 'join_field1', '=', 'join_field2');
	 *
	 * // INNER JOIN `jointable`.`join1` ON `join_field` = `join_field2` AND `A` = '123' AND `B` = 'xyz'
	 * $Core_QueryBuilder_Select->join('jointable.join1', 'join_field1', '=', 'join_field2', array(
	 *		array('AND' => array('A', '=', '123')),
	 *		array('AND' => array('B', '=', 'xyz'))
	 *	));
	 * </code>
	 * @param string $table table name
	 * @param string $column column name
	 * @param string $expression expression
	 * @param string $value value
	 * @param string $additionalConditions additional conditions
	 * @return Core_QueryBuilder_Select
	 */
	public function join($table, $column, $expression = NULL, $value = NULL, $additionalConditions = NULL)
	{
		return $this->_join('INNER JOIN', $table, $column, $expression, $value, $additionalConditions);
	}

	/**
	 * <code>
	 * // LEFT OUTER JOIN `join1` USING (`join_field2`)
	 * $Core_QueryBuilder_Select->leftJoin('join1', 'join_field2');
	 *
	 * // LEFT OUTER JOIN `jointable`.`join1` ON `join_field` = `join_field2`
	 * $Core_QueryBuilder_Select->leftJoin('jointable.join1', 'join_field', '=', 'join_field2');
	 * </code>
	 * @param string $table table name
	 * @param string $column column name
	 * @param string $expression expression
	 * @param string $value value
	 * @param string $additionalConditions additional conditions
	 * @return Core_QueryBuilder_Select
	 */
	public function leftJoin($table, $column, $expression = NULL, $value = NULL, $additionalConditions = NULL)
	{
		return $this->_join('LEFT OUTER JOIN', $table, $column, $expression, $value, $additionalConditions);
	}

	/**
	 *
	 * <code>
	 * // RIGHT OUTER JOIN `join1` USING (`join_field2`)
	 * $Core_QueryBuilder_Select->rightJoin('join1', 'join_field2');
	 *
	 * // RIGHT OUTER JOIN `jointable`.`join1` ON `join_field` = `join_field2`
	 * $Core_QueryBuilder_Select->rightJoin('jointable.join1', 'join_field', '=', 'join_field2');
	 * </code>
	 * @param string $table table name
	 * @param string $column column name
	 * @param string $expression expression
	 * @param string $value value
	 * @param string $additionalConditions additional conditions
	 * @return Core_QueryBuilder_Select
	 */
	public function rightJoin($table, $column, $expression = NULL, $value = NULL, $additionalConditions = NULL)
	{
		return $this->_join('RIGHT OUTER JOIN', $table, $column, $expression, $value, $additionalConditions);
	}

	/**
	 * In MySQL, CROSS JOIN is a syntactic equivalent to INNER JOIN (they can replace each other).
	 * In standard SQL, they are not equivalent. INNER JOIN is used with an ON clause, CROSS JOIN is used otherwise.
	 *
	 * <code>
	 * // CROSS JOIN `join1`
	 * $Core_QueryBuilder_Select->crossJoin('join1');
	 * </code>
	 * @param string $table table name
	 * @return Core_QueryBuilder_Select
	 */
	public function crossJoin($table)
	{
		return $this->_join('CROSS JOIN', $table);
	}

	/**
	 *
	 * <code>
	 * // NATURAL JOIN `join1`
	 * $Core_QueryBuilder_Select->naturalJoin('join1');
	 * </code>
	 * @param string $table table name
	 * @return Core_QueryBuilder_Select
	 */
	public function naturalJoin($table)
	{
		return $this->_join('NATURAL JOIN', $table);
	}

	/**
	 * Open bracket in HAVING
	 * @return Core_QueryBuilder_Select
	 */
	public function havingOpen()
	{
		$this->_having[] = array(
			$this->_operator => array('(')
		);

		// Set operator as EMPTY string
		$this->_operator = '';
		return $this;
	}

	/**
	 * Close bracket in HAVING
	 * @return Core_QueryBuilder_Select
	 */
	public function havingClose()
	{
		// Set operator as EMPTY string
		$this->_operator = '';

		$this->_having[] = array(
			$this->_operator => array(')')
		);

		// Set operator as default
		$this->setDefaultOperator();

		return $this;
	}

	/**
	 * Add HAVING
	 *
	 * <code>
	 * // HAVING `a1` > '2'
	 * $Core_QueryBuilder_Select->having('a1', '>', '2');
	 *
	 * // HAVING `f5` IS TRUE
	 * $Core_QueryBuilder_Select->having('f5', 'IS', TRUE);
	 *
	 * // HAVING `a4` IN (17, 19, NULL, 'NULL')
	 * $Core_QueryBuilder_Select->having('a4', 'IN', array(17,19, NULL, 'NULL'));
	 *
	 * // HAVING `a7` BETWEEN 1 AND 10
	 * $Core_QueryBuilder_Select->having('a7', 'BETWEEN', array(1, 10));
	 * </code>
	 * @param string $column column
	 * @param string $expression expression
	 * @param string $value value
	 * @return Core_QueryBuilder_Select
	 */
	public function having($column, $expression, $value)
	{
		$this->_having[] = array(
			$this->_operator => array($column, $expression, $value)
		);

		// Set operator as default
		$this->setDefaultOperator();

		return $this;
	}

	/**
	 * Set GROUP BY
	 *
	 * <code>
	 * // GROUP BY `field`, COUNT(`id`)
	 * $Core_QueryBuilder_Select->groupBy('field1')->groupBy('COUNT(id)');
	 * </code>
	 * @param string $column column
	 * @return Core_QueryBuilder_Select
	 */
	public function groupBy($column)
	{
		$this->_groupBy[] = $column;
		return $this;
	}

	/**
	 * GROUP BY
	 * @param string $column column
	 * @return Core_QueryBuilder_Select
	 * @see groupBy()
	 */
	public function group($column)
	{
		return $this->groupBy($column);
	}

	/**
	 * UNION is used to combine the result from multiple SELECT statements into a single result set.
	 *
	 * <code>
	 * // (SELECT `id2`, `name2` FROM `tablename2`)
	 * // UNION
	 * // (SELECT `id`, `name` FROM `tablename` LIMIT 10 OFFSET 0)
	 * $select1 = Core_QueryBuilder::select('id', 'name')->from('tablename')
	 * 	->limit(0, 10);
	 *
	 * $select2 = Core_QueryBuilder::select('id2', 'name2')->from('tablename2')
	 * 	->union($select1);
	 * </code>
	 * @param Core_QueryBuilder_Select $object
	 * @return Core_QueryBuilder_Select
	 */
	public function union(Core_QueryBuilder_Select $object)
	{
		$this->_union[] = array('', $object);
		return $this;
	}

	/**
	 * UNION ALL is used to combine the result from multiple SELECT statements into a single result set.
	 *
	 * <code>
	 * // (SELECT `id2`, `name2` FROM `tablename2`)
	 * // UNION ALL
	 * // (SELECT `id`, `name` FROM `tablename` LIMIT 10 OFFSET 0)
	 * $select1 = Core_QueryBuilder::select('id', 'name')->from('tablename')
	 * 	->limit(0, 10);
	 *
	 * $select2 = Core_QueryBuilder::select('id2', 'name2')->from('tablename2')
	 * 	->unionAll($select1);
	 * </code>
	 * @param Core_QueryBuilder_Select $object
	 * @return Core_QueryBuilder_Select
	 */
	public function unionAll(Core_QueryBuilder_Select $object)
	{
		$this->_union[] = array('ALL', $object);
		return $this;
	}

	/**
	 * Warning: Needs to add unionOrderBy(), unionLimit() and unionOffset
	 *
	 * http://dev.mysql.com/doc/refman/5.5/en/union.html
	 * @param string $column column
	 * @param string $direction sorting direction
	 * @param boolean $binary binary option
	 */
	public function unionOrderBy($column, $direction = 'ASC', $binary = FALSE) {}

	/**
	 * LIMIT for UNION
	 * @param string $arg1 offset
	 * @param string $arg2 count
	 */
	public function unionLimit($arg1, $arg2 = NULL)
	{
		if (!is_null($arg2))
		{
			$this->_unionLimit = intval($arg2);
			return $this->unionOffset($arg1);
		}

		$this->_unionLimit = intval($arg1);

		return $this;
	}

	/**
	 * Set UNION offset
	 *
	 * @param int $offset offset
	 * @return Core_QueryBuilder_Selection
	 */
	public function unionOffset($offset)
	{
		$this->_unionOffset = intval($offset);
		return $this;
	}

	/**
	 * Build the SQL query
	 *
	 * @return string The SQL query
	 */
	public function build()
	{
		$query = array('SELECT');

		if ($this->_distinct)
		{
			$query[] = 'DISTINCT';
		}

		if (!is_null($this->_highPriority))
		{
			$query[] = 'HIGH_PRIORITY';
		}

		if (!is_null($this->_straightJoin))
		{
			$query[] = 'STRAIGHT_JOIN';
		}

		if (!is_null($this->_sqlCalcFoundRows))
		{
			$query[] = 'SQL_CALC_FOUND_ROWS';
		}

		if (!empty($this->_select))
		{
			$query[] = implode(', ', $this->quoteColumns($this->_select));
		}
		else
		{
			$query[] = '*';
		}

		if (!empty($this->_from))
		{
			$query[] = 'FROM ' . implode(', ', $this->quoteColumns($this->_from));
		}

		if (!empty($this->_join))
		{
			$query[] = $this->_buildJoin($this->_join);
		}

		if (!empty($this->_where))
		{
			$query[] = 'WHERE ' . $this->_buildExpression($this->_where);
		}

		if (!empty($this->_groupBy))
		{
			$query[] = 'GROUP BY ' . implode(', ', $this->quoteColumns($this->_groupBy));
		}

		if (!empty($this->_having))
		{
			$query[] = 'HAVING ' . $this->_buildExpression($this->_having);
		}

		if (!empty($this->_orderBy))
		{
			$query[] = $this->_buildOrderBy($this->_orderBy);
		}

		if (!is_null($this->_limit))
		{
			$query[] = 'LIMIT ' . $this->_limit;
		}

		if (!is_null($this->_offset))
		{
			$query[] = 'OFFSET ' . $this->_offset;
		}

		$sql = implode(" \n", $query);

		// Unions
		if (!empty($this->_union))
		{
			$aUnion = array('(', $sql);

			foreach ($this->_union as $aTmpUnion)
			{
				list($unionType, $oUnion) = $aTmpUnion;

				$aUnion[] = ") \nUNION {$unionType}\n(";
				$aUnion[] = $oUnion->build();
			}

			$aUnion[] = ')';

			if (!is_null($this->_unionLimit))
			{
				$aUnion[] = ' LIMIT ' . $this->_unionLimit;
			}

			if (!is_null($this->_unionOffset))
			{
				$aUnion[] = ' OFFSET ' . $this->_unionOffset;
			}

			$sql = implode('', $aUnion);
		}

		return $sql;
	}

	/**
	 * Clear SELECT list
	 * @return Core_QueryBuilder_Select
	 */
	public function clearSelect()
	{
		$this->_select = array();
		return $this;
	}

	/**
	 * Clear FROM
	 * @return Core_QueryBuilder_Select
	 */
	public function clearFrom()
	{
		$this->_from = array();
		return $this;
	}

	/**
	 * Clear ORDER BY
	 * @return Core_QueryBuilder_Select
	 */
	public function clearOrderBy()
	{
		$this->_orderBy = array();
		return $this;
	}

	/**
	 * Clear
	 * @return Core_QueryBuilder_Select
	 */
	public function clear()
	{
		$this->_distinct = FALSE;

		$this->_unionLimit = $this->_unionOffset = $this->_highPriority = $this->_straightJoin
			= $this->_sqlCalcFoundRows = NULL;

		$this->_select = $this->_from
			= $this->_join = $this->_groupBy
			= $this->_having = $this->_union = array();

		return parent::clear();
	}
}