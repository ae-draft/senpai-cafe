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
class Shop_Order_Item_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		if (is_null($object->id))
		{
			$object->shop_order_id = Core_Array::getGet('shop_order_id');
		}

		$this->addSkipColumn('hash');
		$this->addSkipColumn('shop_item_digital_id');

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');
		$oSeparatorField = Admin_Form_Entity::factory('Separator');

		$oOrder = Core_Entity::factory('Shop_Order', intval(Core_Array::getGet('shop_order_id')));

		$this->getField('quantity')
			->style("width: 200px")
			->divAttr(array('style' => 'float: left'))
			->class("large");

		$this->getField('name')->format(array('minlen' => array('value' => 0)));

		$oPriceField = $this->getField('price');

		$oPriceField
			->style("width: 200px")
			->class("large")
			->divAttr(array('style' => 'float: left'));

		$oRateField = $this->getField('rate');
		$oRateField->style("width: 100px")
			->class("large")
			->divAttr(array('style' => 'float: left'));

		$oMainTab->moveAfter($oRateField, $oPriceField);

		$oSpanPercent = Admin_Form_Entity::factory('Span');
		$oSpanPercent->value('%')
			->style("font-size: 200%")
			->divAttr(array('style' => 'padding-top: 20px'));

		$oMainTab->addAfter(
			$oSpanPercent, $oRateField
		)->addAfter(
			$oSeparatorField, $oSpanPercent
		);

		$oAdditionalTab->delete(
			$this->getField('shop_warehouse_id')
		);

		$oWarehouseSelect = Admin_Form_Entity::factory('Select');

		$oWarehouseSelect->caption(Core::_('Shop_Order_Item.shop_warehouse_id'))
			->options(
				$this->_fillWarehousesList(Core_Array::getGet('shop_id'))
			)
			->name('shop_warehouse_id')
			->value($this->_object->shop_warehouse_id)
			->style("width: 200px")
			->divAttr(array('style' => 'float: left'));

		$oMainTab->addAfter(
			$oWarehouseSelect, $oSeparatorField
		);

		$oMainTab->delete(
			$this->getField('type')
		);

		$oTypeSelect = Admin_Form_Entity::factory('Select');

		$oTypeSelect->caption(Core::_('Shop_Order_Item.type'))
			->options(
				array(
					Core::_('Shop_Order_Item.order_item_type_caption0'),
					Core::_('Shop_Order_Item.order_item_type_caption1'),
					Core::_('Shop_Order_Item.order_item_type_caption2')
				)
			)
			->name('type')
			->value($this->_object->type)
			->style("width: 200px");

		$oMainTab->addAfter(
			$oTypeSelect, $oWarehouseSelect
		);

		$oMarkingField = $this->getField('marking');

		$oMarkingField
			->style("width: 200px")
			->divAttr(array('style' => 'float: left'));

		//$this->getField('rate')->divAttr(array('style' => 'display: none'));
		//$this->getField('hash')->divAttr(array('style' => 'display: none'));
		//$this->getField('shop_item_digital_id')->divAttr(array('style' => 'display: none'));

		$oAdditionalTab
			->move($this->getField('shop_item_id')->style("width: 200px"), $oMainTab);

		$title = $this->_object->id
			? Core::_('Shop_Order_Item.order_items_edit_form_title', $oOrder->invoice)
			: Core::_('Shop_Order_Item.order_items_add_form_title', $oOrder->invoice);

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @return self
	 * @hostcms-event Shop_Order_Item_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		// New order item
		if (is_null($this->_object->id))
		{
			$shop_item_id = Core_Array::get($this->_formValues, 'shop_item_id');

			if ($shop_item_id &&
				!is_null($oShop_Item = Core_Entity::factory('Shop_Item')->find($shop_item_id, FALSE)))
			{
				Core_Array::get($this->_formValues, 'name') == '' && $this->_formValues['name'] = $oShop_Item->name;
				floatval(Core_Array::get($this->_formValues, 'quantity')) == 0.0 && $this->_formValues['quantity'] = 1.0;
				floatval(Core_Array::get($this->_formValues, 'price')) == 0.0 && $this->_formValues['price'] = $oShop_Item->price;
				Core_Array::get($this->_formValues, 'marking') == '' && $this->_formValues['marking'] = $oShop_Item->marking;
			}
		}

		parent::_applyObjectProperty();

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		return $this;
	}

	/**
	 * Fill warehouses list
	 * @param int $iShopId shop ID
	 * @return array
	 */
	protected function _fillWarehousesList($iShopId)
	{
		$oObject = Core_Entity::factory('Shop_Warehouse');

		$oObject->queryBuilder()
				->where("shop_id", "=", $iShopId)
				->orderBy("sorting")
				->orderBy("id")
			;

		$aObjects = $oObject->findAll();

		$aReturn = array(" … ");

		foreach ($aObjects as $oObject)
		{
			$aReturn[$oObject->id] = $oObject->name;
		}

		return $aReturn;
	}
}