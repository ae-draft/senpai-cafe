<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Admin_Form_Dataset_Entity extends Admin_Form_Dataset
{
	/**
	 * Entity of dataset
	 * @var object
	 */
	protected $_entity = NULL;

	/**
	 * Items count
	 * @var int
	 */
	protected $_count = NULL;

	/**
	 * Constructor.
	 * @param Core_Entity $oCore_Entity entity
	 */
	public function __construct(Core_Entity $oCore_Entity)
	{
		$this->_entity = $oCore_Entity;
	}

	/**
	 * Get items count
	 * @return int
	 */
	public function getCount()
	{
		if (is_null($this->_count))
		{
			// Apply conditions
			$this->_setConditions();

			$this->_entity->applyMarksDeleted();

			$issetHaving = FALSE;
			foreach ($this->_conditions as $condition)
			{
				$aCondition = each($condition);

				if ($aCondition['key'] == 'having' || $aCondition['key'] == 'groupBy')
				{
					$issetHaving = TRUE;
					break;
				}
			}

			if (!$issetHaving)
			{
				$queryBuilder = $this->_entity->queryBuilder()
					->clearSelect()
					->clearOrderBy()
					->select(array('COUNT(*)', 'count'))
					->from($this->_entity->getTableName())
					->limit(1)
					->offset(0)
					->asAssoc();

				$Core_DataBase = $queryBuilder->execute();
			}
			else
			{
				$queryBuilder = $this->_entity->queryBuilder()
					//->clearSelect()
					->clearOrderBy()
					->sqlCalcFoundRows()
					->from($this->_entity->getTableName())
					->limit(1)
					->offset(0)
					->asAssoc();

				$queryBuilder->execute();

				$queryBuilder
					->clear()
					->select(array('FOUND_ROWS()', 'count'))
					->asAssoc();

				$Core_DataBase = $queryBuilder->execute();
			}

			$row = $Core_DataBase->current();
			$this->_count = $row['count'];

			// Warning
			/*if (Core_Array::getRequest('debug'))
			{
				echo '<p><b>Запрос количества</b>: <pre>', $Core_DataBase->getLastQuery(), '</pre></p>';
			}*/
			//$this->_count = count($this->_entity->findAll());
		}

		return $this->_count;
	}

	/**
	 * Dataset objects list
	 *  @var array
	 */
	protected $_objects = array();

	/**
	 * Get objects
	 * @return array
	 */
	public function getObjects()
	{
		return $this->_objects;
	}

	/**
	 * Get entity
	 * @return object
	 */
	public function getEntity()
	{
		return $this->_entity;
	}

	/**
	 * Load objects
	 * @return array
	 */
	public function load()
	{
		if (!$this->_loaded)
		{
			// Применение внесенных условий отбора
			$this->_setConditions();

			$queryBuilder = $this->_entity->queryBuilder()
				->limit($this->_limit)
				->offset($this->_offset);

			is_null($this->_count) && $this->_entity->queryBuilder()->sqlCalcFoundRows();

			// Load columns
			$this->_entity->getTableColums();

			$this->_objects = $this->_entity->findAll(FALSE);

			// Warning
			/*if (Core_Array::getRequest('debug'))
			{
				echo '<p><b>Запрос на выборку</b>: <pre>', Core_DataBase::instance()->getLastQuery(), '</pre></p>';
			}*/
			$this->_loaded = TRUE;

			// Расчет количества
			if (is_null($this->_count))
			{
				$queryBuilder
					->clear()
					->select(array('FOUND_ROWS()', 'count'))
					->asAssoc();

				$Core_DataBase = $queryBuilder->execute();

				$row = $Core_DataBase->current();
				$this->_count = $row['count'];
			}
		}
		return $this->_objects;
	}

	/**
	 * Add condition for the selection of elements
	 * @param array $condition condition
	 * @return Admin_Form_Dataset
	 */
	public function addCondition($condition)
	{
		$aCondition = each($condition);

		// Уточнение таблицы при поиске WHERE
		if ($aCondition['key'] == 'where' /*|| $aCondition['key'] == 'having'*/)
		{
			if (isset($aCondition['value'][0]))
			{
				if (strpos($aCondition['value'][0], '.') === FALSE)
				{
					$condition[$aCondition['key']][0] = $this->_entity->getTableName() . '.' . $condition[$aCondition['key']][0];
				}
			}
		}

		return parent::addCondition($condition);
	}

	/**
	 * Apply conditions for the selection of elements
	 */
	protected function _setConditions()
	{
		$queryBuilder = $this->_entity->queryBuilder()
			->clear();

		foreach ($this->_conditions as $condition)
		{
			list($operator, $args) = each($condition);
			call_user_func_array(array($queryBuilder, $operator), $args);
		}
	}

	/**
	 * Get object
	 * @param int $primaryKey ID
	 * @return object
	 */
	public function getObject($primaryKey)
	{
		$this->_entity
			->queryBuilder()
			->clear();

		// При NULL применяются условия _setConditions() и находим первый в списке
		/*$primaryKey = ($primaryKey === 0)
			? NULL
			: $primaryKey;*/

		// Применение внесенных условий отбора, чтобы нельзя было получить элемент не из этой группы
		//$this->_setConditions();

		$newObject = clone $this->_entity;
		return $newObject->find($primaryKey, FALSE);
		//return $this->_entity->find($primaryKey, FALSE);
	}
}