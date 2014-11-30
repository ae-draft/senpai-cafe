<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Information systems.
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Module extends Core_Module{	/**
	 * Module version
	 * @var string
	 */
	public $version = '6.1';

	/**
	 * Module date
	 * @var date
	 */
	public $date = '2014-08-22';
	/**
	 * Constructor.
	 */	public function __construct()	{
		parent::__construct();
		$this->menu = array(			array(				'sorting' => 30,				'block' => 0,
				'ico' => 'fa-tasks',				'name' => Core::_('Informationsystem.menu'),				'href' => "/admin/informationsystem/index.php",				'onclick' => "$.adminLoad({path: '/admin/informationsystem/index.php'}); return false"			)		);	}

	/**
	 * Функция обратного вызова для поисковой индексации
	 *
	 * @param $offset
	 * @param $limit
	 * @return array
	 */
	public function indexing($offset, $limit)
	{
		/**
		 * $_SESSION['search_block'] - номер блока индексации
		 */
		if (!isset($_SESSION['search_block']))
		{
			$_SESSION['search_block'] = 0;
		}

		if (!isset($_SESSION['last_limit']))
		{
			$_SESSION['last_limit'] = 0;
		}

		$limit_orig = $limit;

		$result = array();

		switch ($_SESSION['search_block'])
		{
			case 0:
				$aTmpResult = $this->indexingInformationsystemGroups($offset, $limit);

				$_SESSION['last_limit'] = count($aTmpResult);

				$result = array_merge($result, $aTmpResult);
				$count = count($result);

				if ($count < $limit_orig)
				{
					$_SESSION['search_block']++;
					$limit = $limit_orig - $count;
					$offset = 0;
				}
				else
				{
					return $result;
				}

			case 1:
				$aTmpResult = $this->indexingInformationsystemItems($offset, $limit);

				$_SESSION['last_limit'] = count($aTmpResult);

				$result = array_merge($result, $aTmpResult);
				$count = count($result);

				// Закончена индексация
				if ($count < $limit_orig)
				{
					$_SESSION['search_block']++;
					$limit = $limit_orig - $count;
					$offset = 0;
				}
				else
				{
					return $result;
				}
		}

		$_SESSION['search_block'] = 0;

		return $result;
	}

	/**
	 * Индексация информационных групп
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 * @hostcms-event Informationsystem_Module.indexingInformationsystemGroups
	 */
	public function indexingInformationsystemGroups($offset, $limit)
	{
		$offset = intval($offset);
		$limit = intval($limit);

		$oInformationsystemGroup = Core_Entity::factory('Informationsystem_Group');
		$oInformationsystemGroup
			->queryBuilder()
			->join('informationsystems', 'informationsystem_groups.informationsystem_id', '=', 'informationsystems.id')
			->join('structures', 'informationsystems.structure_id', '=', 'structures.id')
			->where('structures.active', '=', 1)
			->where('structures.indexing', '=', 1)
			->where('informationsystem_groups.indexing', '=', 1)
			->where('informationsystem_groups.active', '=', 1)
			->where('informationsystem_groups.deleted', '=', 0)
			->where('informationsystems.deleted', '=', 0)
			->where('structures.deleted', '=', 0)
			->limit($offset, $limit);

		Core_Event::notify(get_class($this) . '.indexingInformationsystemGroups', $this, array($oInformationsystemGroup));

		$aInformationsystemGroups = $oInformationsystemGroup->findAll();

		$result = array();
		foreach($aInformationsystemGroups as $oInformationsystemGroup)
		{
			$result[] = $oInformationsystemGroup->indexing();
		}

		return $result;
	}

	/**
	 * Индексация информационных элементов
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 * @hostcms-event Informationsystem_Module.indexingInformationsystemItems
	 */
	public function indexingInformationsystemItems($offset, $limit)
	{
		$offset = intval($offset);
		$limit = intval($limit);

		$oInformationsystemItem = Core_Entity::factory('Informationsystem_Item');

		$oInformationsystemItem
			->queryBuilder()
			->join('informationsystems', 'informationsystem_items.informationsystem_id', '=', 'informationsystems.id')
			->join('structures', 'informationsystems.structure_id', '=', 'structures.id')
			->where('structures.active', '=', 1)
			->where('structures.indexing', '=', 1)
			->where('informationsystem_items.indexing', '=', 1)
			->where('informationsystem_items.active', '=', 1)
			->where('informationsystem_items.shortcut_id', '=', 0)
			->where('informationsystem_items.deleted', '=', 0)
			->where('informationsystems.deleted', '=', 0)
			->where('structures.deleted', '=', 0)
			->limit($offset, $limit);

		Core_Event::notify(get_class($this) . '.indexingInformationsystemItems', $this, array($oInformationsystemItem));

		$aInformationsystemItems = $oInformationsystemItem->findAll();

		$result = array();
		foreach($aInformationsystemItems as $oInformationsystemItem)
		{
			$result[] = $oInformationsystemItem->indexing();
		}

		return $result;
	}

	/**
	 * Search callback function
	 * @param Search_Page_Model $oSearch_Page
	 * @return self
	 * @hostcms-event Informationsystem_Module.searchCallback
	 */
	public function searchCallback($oSearch_Page)
	{
		if ($oSearch_Page->module_value_id)
		{
			switch ($oSearch_Page->module_value_type)
			{
				case 1: // Информационые группы
					$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group')->find($oSearch_Page->module_value_id);

					Core_Event::notify(get_class($this) . '.searchCallback', $this, array($oSearch_Page, $oInformationsystem_Group));

					!is_null($oInformationsystem_Group->id) && $oSearch_Page->addEntity($oInformationsystem_Group);
				break;
				case 2: // Информационые элементы
					$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($oSearch_Page->module_value_id);

					if (!is_null($oInformationsystem_Item->id))
					{
						$oInformationsystem_Item
							->showXmlComments(TRUE)
							->showXmlProperties(TRUE);

						Core_Event::notify(get_class($this) . '.searchCallback', $this, array($oSearch_Page, $oInformationsystem_Item));

						$oSearch_Page
							->addEntity($oInformationsystem_Item)
							->addEntity($oInformationsystem_Item->Informationsystem_Group);
					}
				break;
			}
		}

		return $this;
	}
}