<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 *
 * Контроллер загрузки значений списка инф. элементов для <select> доп. св-в
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Controller_Load_Select_Options extends Admin_Form_Action_Controller_Type_Load_Select_Options
{
	/**
	 * Add value
	 * @return self
	 */
	public function addValues()
	{
		foreach ($this->_objects as $Object)
		{
			$this->_values[$Object->id] = !$Object->shortcut_id
				? $Object->name
				: $Object->Informationsystem_Item->name;
		}

		return $this;
	}

	/**
	 * Find objects by $this->_model
	 * @return self
	 */
	protected function _findObjects()
	{
		$oInformationsystem = $this->_model->Informationsystem;

		switch ($oInformationsystem->items_sorting_direction)
		{
			case 1:
				$items_sorting_direction = 'DESC';
			break;
			case 0:
			default:
				$items_sorting_direction = 'ASC';
		}

		$this->_model
			->queryBuilder()
			->clearOrderBy();

		// Определяем поле сортировки информационных элементов
		switch ($oInformationsystem->items_sorting_field)
		{
			case 1:
				$this->_model
					->queryBuilder()
					->orderBy('informationsystem_items.name', $items_sorting_direction)
					->orderBy('informationsystem_items.sorting', $items_sorting_direction);
				break;
			case 2:
				$this->_model
					->queryBuilder()
					->orderBy('informationsystem_items.sorting', $items_sorting_direction)
					->orderBy('informationsystem_items.name', $items_sorting_direction);
				break;
			case 0:
			default:
				$this->_model
					->queryBuilder()
					->orderBy('informationsystem_items.datetime', $items_sorting_direction)
					->orderBy('informationsystem_items.sorting', $items_sorting_direction);
		}

		// Find all objects
		$this->_objects = $this->_model->findAll();

		return $this;
	}
}