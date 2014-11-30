<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Показ корзины магазина.
 *
 * Доступные методы:
 *
 * - itemsProperties(TRUE) выводить значения дополнительных свойств товаров, по умолчанию FALSE
 * - itemsPropertiesList(TRUE) выводить список дополнительных свойств товаров, по умолчанию TRUE
 *
 * <code>
 * $Shop_Cart_Controller_Show = new Shop_Cart_Controller_Show(
 * 		Core_Entity::factory('Shop', 1)
 * 	);
 *
 * 	$Shop_Cart_Controller_Show
 * 		->xsl(
 * 			Core_Entity::factory('Xsl')->getByName('МагазинКорзина')
 * 		)
 * 		->show();
 * </code>
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Cart_Controller_Show extends Core_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'couponText',
		'itemsProperties',
		'itemsPropertiesList',
	);

	/**
	 * List of properties for item
	 * @var array
	 */
	protected $_aItem_Properties = array();

	/**
	 * List of property directories for item
	 * @var array
	 */
	protected $_aItem_Property_Dirs = array();

	/**
	 * Current Siteuser
	 * @var Siteuser_Model|NULL
	 */
	protected $_oSiteuser = NULL;

	/**
	 * Constructor.
	 * @param Shop_Model $oShop shop
	 */
	public function __construct(Shop_Model $oShop)
	{
		parent::__construct($oShop->clearEntities());

		if (Core::moduleIsActive('siteuser'))
		{
			// Если есть модуль пользователей сайта, $siteuser_id равен 0 или ID авторизованного
			$this->_oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($this->_oSiteuser)
			{
				// Move goods from cookies to session
				$Shop_Cart_Controller = $this->_getCartController();
				$Shop_Cart_Controller->moveTemporaryCart($oShop);
			}
		}

		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('siteuser_id')
				->value($this->_oSiteuser ? $this->_oSiteuser->id : 0)
		);

		$this->itemsProperties = FALSE;
		$this->itemsPropertiesList = TRUE;
	}

	/**
	 * Get Shop_Cart_Controller
	 * @return Shop_Cart_Controller
	 */
	protected function _getCartController()
	{
		return Shop_Cart_Controller::instance();
	}

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Shop_Cart_Controller_Show.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);

		$oShop = $this->getEntity();

		// Coupon text
		!is_null($this->couponText) && $this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('coupon_text')
				->value($this->couponText)
		);

		//Активность модуля "Пользователи сайта"
		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('siteuser_exists')
				->value(Core::moduleIsActive('siteuser') ? 1 : 0)
		);

		// Список свойств товаров
		if ($this->itemsPropertiesList)
		{
			$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $oShop->id);

			$aProperties = $oShop_Item_Property_List->Properties->findAll();

			foreach ($aProperties as $oProperty)
			{
				$this->_aItem_Properties[$oProperty->property_dir_id][] = $oProperty->clearEntities();

				$oShop_Item_Property = $oProperty->Shop_Item_Property;

				$oShop_Item_Property->shop_measure_id && $oProperty->addEntity(
					$oShop_Item_Property->Shop_Measure
				);
			}

			$aProperty_Dirs = $oShop_Item_Property_List->Property_Dirs->findAll();
			foreach ($aProperty_Dirs as $oProperty_Dir)
			{
				$oProperty_Dir->clearEntities();
				$this->_aItem_Property_Dirs[$oProperty_Dir->parent_id][] = $oProperty_Dir->clearEntities();
			}

			$Shop_Item_Properties = Core::factory('Core_Xml_Entity')
				->name('shop_item_properties');

			$this->addEntity($Shop_Item_Properties);

			$this->_addItemsPropertiesList(0, $Shop_Item_Properties);
		}

		$Shop_Cart_Controller = $this->_getCartController();

		$quantity = $amount = $tax = $weight = 0;

		$aShop_Cart = $Shop_Cart_Controller->getAll($oShop);
		foreach ($aShop_Cart as $oShop_Cart)
		{
			if ($oShop_Cart->Shop_Item->id)
			{
				$this->itemsProperties && $oShop_Cart->showXmlProperties(TRUE);

				$this->addEntity($oShop_Cart->clearEntities());
				if ($oShop_Cart->postpone == 0)
				{
					$quantity += $oShop_Cart->quantity;

					// Prices
					$oShop_Item_Controller = new Shop_Item_Controller();
					if (Core::moduleIsActive('siteuser'))
					{
						$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();
						$oSiteuser && $oShop_Item_Controller->siteuser($oSiteuser);
					}

					$oShop_Item_Controller->count($oShop_Cart->quantity);
					$aPrices = $oShop_Item_Controller->getPrices($oShop_Cart->Shop_Item);
					$amount += $aPrices['price_discount'] * $oShop_Cart->quantity;

					$tax += $aPrices['tax'] * $oShop_Cart->quantity;

					$weight += $oShop_Cart->Shop_Item->weight * $oShop_Cart->quantity;
				}
			}
			else
			{
				$oShop_Cart->delete();
			}
		}

		// Скидки от суммы заказа
		$oShop_Purchase_Discount_Controller = new Shop_Purchase_Discount_Controller($oShop);
		$oShop_Purchase_Discount_Controller
			->amount($amount)
			->quantity($quantity)
			->couponText($this->couponText)
			->siteuserId($this->_oSiteuser ? $this->_oSiteuser->id : 0)
			;

		$totalDiscount = 0;
		$aShop_Purchase_Discounts = $oShop_Purchase_Discount_Controller->getDiscounts();
		foreach ($aShop_Purchase_Discounts as $oShop_Purchase_Discount)
		{
			$this->addEntity($oShop_Purchase_Discount->clearEntities());
			$totalDiscount += $oShop_Purchase_Discount->getDiscountAmount();
		}

		// Total order amount
		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('total_amount')
				->value($amount - $totalDiscount)
		)->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('total_tax')
				->value($tax)
		)->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('total_quantity')
				->value($quantity)
		)->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('total_weight')
				->value($weight)
		);

		return parent::show();
	}

	/**
	 * Add items properties to XML
	 * @param int $parent_id
	 * @param object $parentObject
	 * @return self
	 */
	protected function _addItemsPropertiesList($parent_id, $parentObject)
	{
		if (isset($this->_aItem_Property_Dirs[$parent_id]))
		{
			foreach ($this->_aItem_Property_Dirs[$parent_id] as $oProperty_Dir)
			{
				$parentObject->addEntity($oProperty_Dir);
				$this->_addItemsPropertiesList($oProperty_Dir->id, $oProperty_Dir);
			}
		}

		if (isset($this->_aItem_Properties[$parent_id]))
		{
			$parentObject->addEntities($this->_aItem_Properties[$parent_id]);
		}

		return $this;
	}
}