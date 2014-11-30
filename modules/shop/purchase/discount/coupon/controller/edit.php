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
class Shop_Purchase_Discount_Coupon_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');

		$oAdditionalTab->delete($this->getField('shop_purchase_discount_id'));

		$oCouponSelect = Admin_Form_Entity::factory('Select');

		$aOptions = $this->_fillShopPurchaseDiscounts(Core_Array::getGet('shop_id', 0));
		
		$oCouponSelect->caption(Core::_('Shop_Purchase_Discount_Coupon.shop_purchase_discount_id'))
			->options(
				count($aOptions) ? $aOptions : array(' … ')
			)
			->name('shop_purchase_discount_id')
			->value($this->_object->shop_purchase_discount_id);

		$oMainTab->addAfter($oCouponSelect, $this->getField('name'));

		$title = $this->_object->id
					? Core::_('Shop_Purchase_Discount_Coupon.coupon_form_table_title_edit')
					: Core::_('Shop_Purchase_Discount_Coupon.coupon_form_table_title_add');

		$this->title($title);

		return $this;
	}

	/**
	 * Fill discounts list
	 * @param int $iShopId shop ID
	 * @return array
	 */
	protected function _fillShopPurchaseDiscounts($iShopId)
	{
		$oShopPurchaseDiscountCoupon = Core_Entity::factory('Shop_Purchase_Discount');

		$oShopPurchaseDiscountCoupon
			->queryBuilder()
			->where('shop_id', '=', $iShopId)
			->where('active', '=', 1)
			->orderBy('id', 'ASC');

		$aShopPurchaseDiscountCoupons = $oShopPurchaseDiscountCoupon->findAll();

		$aReturn = array();
		foreach ($aShopPurchaseDiscountCoupons as $oShopPurchaseDiscountCoupon)
		{
			$aReturn[$oShopPurchaseDiscountCoupon->id] = $oShopPurchaseDiscountCoupon->name;
		}

		return $aReturn;
	}
}