<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Trash.
 *
 * @package HostCMS 6\Trash
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2012 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Trash_Dataset extends Admin_Form_Dataset
{
	/**
	 * Items count
	 * @var int
	 */
	protected $_count = NULL;

	/**
	 * Database instance
	 * @var Core_DataBase
	 */
	protected $_dataBase = NULL;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->_dataBase = Core_DataBase::instance();
	}

	/**
	 * Get count of finded objects
	 * @return int
	 */
	public function getCount()
	{
		if (is_null($this->_count))
		{
			$this->_getTables();
			$this->_count = count($this->_objects);
		}

		return $this->_count;
	}

	/**
	 * Dataset objects list
	 *  @var array
	 */
	protected $_objects = array();

	/**
	 * Get new object
	 * @return object
	 */
	protected function _newObject()
	{
		return new Trash_Entity();
	}

	/**
	 * Load objects
	 * @return array
	 */
	public function load()
	{
		return array_slice($this->_objects, $this->_offset, $this->_limit);
	}

	/**
	 * Load data
	 * @return self
	 */
	protected function _getTables()
	{
		$this->_objects = array();
		$aTables = $this->_dataBase->getTables();

		$queryBuilder = Core_QueryBuilder::select();

		foreach ($aTables as $key => $name)
		{
			$aColumns = $this->_dataBase->getColumns($name);

			$id = $key + 1;

			if (isset($aColumns['deleted']))
			{
				$row = $queryBuilder
					->clear()
					->select(array('COUNT(*)', 'count'))
					->from($name)
					->where('deleted', '=', 1)
					->execute()
					->asAssoc()
					->current();

				if ($row['count'])
				{
					$oTrash_Entity = $this->_objects[$id] = $this->_newObject();

					$oTrash_Entity->setTableColums(array(
						'id' => array(),
						'table_name' => array(),
						'name' => array(),
						'count' => array(),
					));

					$singular = Core_Inflection::getSingular($name);

					$oTrash_Entity->id = $id;
					$oTrash_Entity->table_name = $name;
					$oTrash_Entity->name = Core::_($singular . '.model_name');
					$oTrash_Entity->count = $row['count'];
				}
			}
		}

		return $this;
	}

	/**
	 * Get entity
	 * @return object
	 */
	public function getEntity()
	{
		return $this->_newObject();
	}

	/**
	 * Get object
	 * @param int $primaryKey ID
	 * @return object
	 */
	public function getObject($primaryKey)
	{
		!count($this->_objects) && $this->_getTables();
		return isset($this->_objects[$primaryKey])
			? $this->_objects[$primaryKey]
			: $this->_newObject();
	}
}