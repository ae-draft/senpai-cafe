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
class Shop_Warehouse_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
			$object->shop_id = Core_Array::getGet('shop_id');

			if ($object->Shop->Shop_Warehouses->getCount() == 0)
			{
				$object->default = 1;
			}
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');

		$oAdditionalTab = $this->getTab('additional');

		// Удаляем типы доставок
		$oAdditionalTab
			->delete($this->getField('shop_country_id'))
			->delete($this->getField('shop_country_location_id'))
			->delete($this->getField('shop_country_location_city_id'))
			->delete($this->getField('shop_country_location_city_area_id'));

		// флаг установки количества товара на складе
		$oShopItemCountCheckBox = Admin_Form_Entity::factory('Checkbox');
		$oShopItemCountCheckBox
			->value(
				is_null($object->id) ? 1 : 0
			)
			->caption(Core::_("Shop_Warehouse.warehouse_default_count"))
			->name("warehouse_default_count");

		$oMainTab->addAfter($oShopItemCountCheckBox, $this->getField('active'));

		$Shop_Delivery_Condition_Controller_Edit = new Shop_Delivery_Condition_Controller_Edit($this->_Admin_Form_Action);

		$Shop_Delivery_Condition_Controller_Edit->controller($this->_Admin_Form_Controller);

		$Shop_Delivery_Condition_Controller_Edit->generateCountryFields($this, $oMainTab, $this->getField('default'));

		$title = $this->_object->id
			? Core::_('Shop_Warehouse.form_warehouses_edit')
			: Core::_('Shop_Warehouse.form_warehouses_add');

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Shop_Warehouse_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		parent::_applyObjectProperty();

		if($this->_object->default)
		{
			$this->_object->active = 1;
			$this->_object->changeDefaultStatus();
		}

		//Установка количества товара на складе
		if (Core_Array::getPost('warehouse_default_count'))
		{
			$offset = 0;
			$limit = 100;

			do {
				$oShop_Items = $this->_object->Shop->Shop_Items;

				$oShop_Items
					->queryBuilder()
					->offset($offset)->limit($limit);

				$aShop_Items = $oShop_Items->findAll(FALSE);

				foreach ($aShop_Items as $oShop_Item)
				{
					if (is_null($oShop_Item->Shop_Warehouse_Items->getByShop_warehouse_id($this->_object->id, FALSE)))
					{
						$oShop_Warehouse_Item = Core_Entity::factory('Shop_Warehouse_Item');
						$oShop_Warehouse_Item->shop_warehouse_id = $this->_object->id;
						$oShop_Warehouse_Item->count = 0;
						$oShop_Item->add($oShop_Warehouse_Item);
					}
				}

				$offset += $limit;
			}
			while (count($aShop_Items));
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}
}