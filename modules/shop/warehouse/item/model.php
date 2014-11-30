<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Warehouse_Item_Model extends Core_Entity
{
	/**
	 * Disable markDeleted()
	 * @var mixed
	 */
	protected $_marksDeleted = NULL;

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'shop_item' => array(),
		'shop_warehouse' => array()
	);

	/**
	 * Forbidden tags. If list of tags is empty, all tags will be shown.
	 * @var array
	 */
	protected $_forbiddenTags = array(
		'user_id',
	);

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;
		}
	}

	/**
	 * Get item count by item ID
	 * @param int $shop_item_id item ID
	 * @param boolean $bCache cache mode
	 * @return Shop_Warehouse_Item_Model|NULL
	 */
	public function getByShopItemId($shop_item_id, $bCache = TRUE)
	{
		$this->queryBuilder()
			//->clear()
			->where('shop_item_id', '=', $shop_item_id)
			->limit(1);

		$aShop_Warehouse_Items = $this->findAll($bCache);

		return isset($aShop_Warehouse_Items[0]) ? $aShop_Warehouse_Items[0] : NULL;
	}

	/**
	 * Get item count by warehouse ID
	 * @param int $shop_warehouse_id warehouse ID
	 * @param boolean $bCache cache mode
	 * @return Shop_Warehouse_Item_Model|NULL
	 */
	public function getByWarehouseId($shop_warehouse_id, $bCache = TRUE)
	{
		$this->queryBuilder()
			//->clear()
			->where('shop_warehouse_id', '=', $shop_warehouse_id)
			->limit(1);

		$aShop_Warehouse_Items = $this->findAll($bCache);

		if (isset($aShop_Warehouse_Items[0]))
		{
			return $aShop_Warehouse_Items[0];
		}

		return NULL;
	}
}