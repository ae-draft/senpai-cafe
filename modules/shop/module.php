<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Module extends Core_Module{	/**
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
		$this->menu = array(			array(				'sorting' => 40,				'block' => 0,
				'ico' => 'fa-shopping-cart',				'name' => Core::_('Shop.menu'),				'href' => "/admin/shop/index.php",				'onclick' => "$.adminLoad({path: '/admin/shop/index.php'}); return false"			)		);	}

	/**
	 * Функция обратного вызова для поисковой индексации данных модуля
	 *
	 * @param $offset
	 * @param $limit
	 * @return array
	 */
	public function indexing($offset, $limit)
	{
		/**
		 * $_SESSION['search_block'] - номер блока индексации
		 * $_SESSION['last_limit'] - количество проиндексирвоанных последним блоком
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
				$aTmpResult = $this->indexingShopGroups($offset, $limit);

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
				// Следующая индексация
				$aTmpResult = $this->indexingShopItems($offset, $limit);

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

			case 2:
				// Следующая индексация
				$aTmpResult = $this->indexingShopSellers($offset, $limit);

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

		// По окончанию индексации сбрасываем сессии в 0
		$_SESSION['search_block'] = 0;

		return $result;
	}

	/**
	 * Индексация групп
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 * @hostcms-event Shop_Module.indexingShopGroups
	 */
	public function indexingShopGroups($offset, $limit)
	{
		$offset = intval($offset);
		$limit = intval($limit);

		$oShopGroup = Core_Entity::factory('Shop_Group');
		$oShopGroup
			->queryBuilder()
			->join('shops', 'shop_groups.shop_id', '=', 'shops.id')
			->join('structures', 'shops.structure_id', '=', 'structures.id')
			->where('structures.active', '=', 1)
			->where('structures.indexing', '=', 1)
			->where('shop_groups.indexing', '=', 1)
			->where('shop_groups.active', '=', 1)
			->where('shop_groups.deleted', '=', 0)
			->where('shops.deleted', '=', 0)
			->where('structures.deleted', '=', 0)
			->limit($offset, $limit);

		Core_Event::notify(get_class($this) . '.indexingShopGroups', $this, array($oShopGroup));

		$aShopGroups = $oShopGroup->findAll();

		$result = array();
		foreach($aShopGroups as $oShopGroup)
		{
			$result[] = $oShopGroup->indexing();
		}

		return $result;
	}

	/**
	 * Индексация товаров
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 * @hostcms-event Shop_Module.indexingShopItems
	 */
	public function indexingShopItems($offset, $limit)
	{
		$limit = intval($limit);
		$offset = intval($offset);

		$oShopItem = Core_Entity::factory('Shop_Item');

		$oShopItem
			->queryBuilder()
			->join('shops', 'shop_items.shop_id', '=', 'shops.id')
			->join('structures', 'shops.structure_id', '=', 'structures.id')
			->where('structures.active', '=', 1)
			->where('structures.indexing', '=', 1)
			->where('shop_items.indexing', '=', 1)
			->where('shop_items.active', '=', 1)
			->where('shop_items.shortcut_id', '=', 0)
			->where('shop_items.deleted', '=', 0)
			->where('shops.deleted', '=', 0)
			->where('structures.deleted', '=', 0)
			->limit($offset, $limit);

		Core_Event::notify(get_class($this) . '.indexingShopItems', $this, array($oShopItem));

		$aShopItems = $oShopItem->findAll();

		$result = array();
		foreach($aShopItems as $oShopItem)
		{
			$result[] = $oShopItem->indexing();
		}

		return $result;
	}

	/**
	 * Индексация продавцов
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return array
	 * @hostcms-event Shop_Module.indexingShopSellers
	 */
	public function indexingShopSellers($offset, $limit)
	{
		$offset = intval($offset);
		$limit = intval($limit);

		$oShopSeller = Core_Entity::factory('Shop_Seller');

		$oShopSeller
			->queryBuilder()
			->join('shops', 'shop_sellers.shop_id', '=', 'shops.id')
			->join('structures', 'shops.structure_id', '=', 'structures.id')
			->where('structures.active', '=', 1)
			->where('structures.indexing', '=', 1)
			->where('shop_sellers.deleted', '=', 0)
			->where('shops.deleted', '=', 0)
			->where('structures.deleted', '=', 0)
			->limit($offset, $limit);

		Core_Event::notify(get_class($this) . '.indexingShopSellers', $this, array($oShopSeller));

		$aShopSellers = $oShopSeller->findAll();

		$result = array();
		foreach($aShopSellers as $oShopSeller)
		{
			$result[] = $oShopSeller->indexing();
		}

		return $result;
	}

	/**
	 * Search callback function
	 * @param Search_Page_Model $oSearch_Page
	 * @return self
	 * @hostcms-event Shop_Module.searchCallback
	 */
	public function searchCallback($oSearch_Page)
	{
		if ($oSearch_Page->module_value_id)
		{
			switch ($oSearch_Page->module_value_type)
			{
				case 1: // Группы
					$oShop_Group = Core_Entity::factory('Shop_Group')->find($oSearch_Page->module_value_id);

					Core_Event::notify(get_class($this) . '.searchCallback', $this, array($oSearch_Page, $oShop_Group));

					!is_null($oShop_Group->id) && $oSearch_Page->addEntity($oShop_Group);
				break;
				case 2: // Товары
					$oShop_Item = Core_Entity::factory('Shop_Item')->find($oSearch_Page->module_value_id);

					if (!is_null($oShop_Item->id))
					{
						$oShop_Item
							->showXmlComments(TRUE)
							->showXmlProperties(TRUE)
							->showXmlSpecialprices(TRUE);

						Core_Event::notify(get_class($this) . '.searchCallback', $this, array($oSearch_Page, $oShop_Item));

						$oSearch_Page
							->addEntity($oShop_Item)
							->addEntity($oShop_Item->Shop_Group);
					}
				break;
			}
		}

		return $this;
	}
}