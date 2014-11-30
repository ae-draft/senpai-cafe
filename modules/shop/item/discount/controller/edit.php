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
class Shop_Item_Discount_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		$oShopItem = Core_Entity::factory('Shop_Item', Core_Array::getGet('shop_item_id', 0));

		$oShop = $oShopItem->Shop;

		if (is_null($object->id))
		{
			$object->shop_id = $oShop->id;
		}

		parent::setObject($object);

		$oMainTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Shop_Item.tab_description'))
			->name('main');

		$this
			->addTab($oMainTab);

		$oMainTab->add(Admin_Form_Entity::factory('Select')
			->caption(Core::_('Shop_Discount.item_discount_name'))
			->options($this->_fillDiscounts($oShop->id))
			->name('shop_discount_id')
			->value($this->_object->id));

		$windowId =  $this->_Admin_Form_Controller->getWindowId();

		$oMainTab->add(Admin_Form_Entity::factory('Radiogroup')
			->radio(array(
				'—',
				Core::_("Shop_Discount.shop_apply_modification_discount"),
				Core::_("Shop_Discount.shop_not_apply_modification_discount")
			))
			//->caption('Caption')
			->name('apply_for_modifications')
			->divAttr(array('id' => 'import_types'))
		)
		->add(Admin_Form_Entity::factory('Code')
			->html("<script>$(function() {
				$('#{$windowId} #import_types').buttonset();
			});</script>")
		);
		/*$oMainTab->add(Admin_Form_Entity::factory('Checkbox')
			->value(1)
			->caption(Core::_("Shop_Discount.shop_apply_modification_discount"))
			->name("apply_for_modifications"));*/

		$title = $this->_object->id
					? Core::_('Shop_Discount.item_discount_edit_form_title')
					: Core::_('Shop_Discount.item_discount_add_form_title');

		$this->title($title);

		return $this;
	}

	/**
	 * Fill discounts list
	 * @param int $iShopId shop ID
	 * @return array
	 */
	protected function _fillDiscounts($iShopId)
	{
		$aShopDiscounts = Core_Entity::factory('Shop', $iShopId)->Shop_Discounts->findAll();

		$aReturn = array(" … ");

		foreach($aShopDiscounts as $oShopDiscount)
		{
			$aReturn[$oShopDiscount->id] = $oShopDiscount->name;
		}

		return $aReturn;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @return self
	 * @hostcms-event Shop_Item_Discount_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		$oShopItem = Core_Entity::factory('Shop_Item', Core_Array::getGet('shop_item_id', 0));
		$oShopDiscount = Core_Entity::factory('Shop_Discount', Core_Array::getPost('shop_discount_id', 0));
		$oShopItemDiscount = $oShopItem->Shop_Item_Discounts->getByDiscountId($oShopDiscount->id);

		if(is_null($oShopItemDiscount))
		{
			$oShopItem->add($oShopDiscount);
		}

		/*if (Core_Array::getPost('apply_for_modifications'))
		{
			$aModifications = $oShopItem->Modifications->findAll();
			foreach ($aModifications as $oModification)
			{
				$oModification->add($oShopDiscount);
			}
		}*/
		switch(Core_Array::getPost('apply_for_modifications'))
		{
			case 1:
				$aModifications = $oShopItem->Modifications->findAll();
				foreach ($aModifications as $oModification)
				{
					$oModification->add($oShopDiscount);
				}
			break;
			case 2:
				$aModifications = $oShopItem->Modifications->findAll();
				foreach ($aModifications as $oModification)
				{
					$oModification->remove($oShopDiscount);
				}
			break;
		}

		//parent::_applyObjectProperty();

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		return $this;
	}
}