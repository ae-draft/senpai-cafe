<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2012 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Item_Discount_Controller_Delete extends Admin_Form_Action_Controller
{
	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 */
	public function execute($operation = NULL)
	{
		$oShopItem = Core_Entity::factory('Shop_Item', Core_Array::getGet('shop_item_id', 0));
		$oShopDiscount = Core_Entity::factory('Shop_Discount', $this->_object->id);
		$oShopItemDiscount = $oShopItem->Shop_Item_Discounts->getByDiscountId($oShopDiscount->id);

		if(!is_null($oShopItemDiscount))
		{
			$oShopItemDiscount->delete();
		}
	}
}