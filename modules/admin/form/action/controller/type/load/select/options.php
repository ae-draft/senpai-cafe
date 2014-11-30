<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 * Типовой контроллер загрузки значений списка для <select>
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Admin_Form_Action_Controller_Type_Load_Select_Options extends Admin_Form_Action_Controller
{
	/**
	 * Conditions
	 * @var array
	 */
	protected $_conditions = array();

	/**
	 * Model
	 * @var Core_Entity
	 */
	protected $_model = NULL;

	/**
	 * Default value
	 * @var string
	 */
	protected $_defaultValue = NULL;

	/**
	 * Set default value
	 * @param string|int $defaultValue default value
	 * @return self
	 */
	public function defaultValue($defaultValue)
	{
		$this->_defaultValue = $defaultValue;
		return $this;
	}

	/**
	 * Add condition
	 * @param array $condition condition
	 * @return self
	 */
	public function addCondition($condition)
	{
		$this->_conditions[] = $condition;
		return $this;
	}

	/**
	 * Set model
	 * @param Core_Entity $model object
	 * @return self
	 */
	public function model($model)
	{
		$this->_model = $model;
		return $this;
	}

	/**
	 * Array of objects
	 * @var array
	 */
	protected $_objects = array();

	/**
	 * Array of values
	 * @var array
	 */
	protected $_values = array();

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 */
	public function execute($operation = NULL)
	{
		if (is_null($this->_model))
		{
			throw new Core_Exception('model is NULL.');
		}

		$queryBuilder = $this->_model->queryBuilder();

		foreach ($this->_conditions as $condition)
		{
			list($operator, $args) = each($condition);
			call_user_func_array(array($queryBuilder, $operator), $args);
		}

		!is_null($this->_defaultValue) && $this->_values[] = $this->_defaultValue;

		$this->_findObjects();

		// Add objects
		$this->addValues();

		echo json_encode($this->_values);
		die();
	}

	/**
	 * Find objects by $this->_model
	 * @return self
	 */
	protected function _findObjects()
	{
		// Find all objects
		$this->_objects = $this->_model->findAll();

		return $this;
	}

	/**
	 * Add value
	 * @return self
	 */
	public function addValues()
	{
		foreach ($this->_objects as $Object)
		{
			$this->_values[$Object->id] = $Object->name;
		}

		return $this;
	}
}