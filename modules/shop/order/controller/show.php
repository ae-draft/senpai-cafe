<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Показ заказов пользователя в магазине.
 *
 * Доступные методы:
 *
 * - itemsProperties(TRUE|FALSE|array()) выводить значения дополнительных свойств товаров, по умолчанию FALSE. Может принимать массив с идентификаторами дополнительных свойств, значения которых необходимо вывести.
 *
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Order_Controller_Show extends Core_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'itemsProperties',
	);

	/**
	 * Shop orders
	 * @var Shop_Orders
	 */
	protected $_Shop_Orders = NULL;

	/**
	 * Constructor.
	 * @param Shop_Model $oShop shop
	 */
	public function __construct(Shop_Model $oShop)
	{
		parent::__construct($oShop->clearEntities());

		$this->_Shop_Orders = $oShop->Shop_Orders;

		$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

		if (!is_null($oSiteuser))
		{
			$siteuser_id = $oSiteuser->id;
		}
		else
		{
			throw new Core_Exception('Siteuser does not exist.');
		}

		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('siteuser_id')
				->value($siteuser_id)
		);

		$this->_Shop_Orders
			->queryBuilder()
			->where('shop_orders.siteuser_id', '=', $siteuser_id)
			->orderBy('shop_orders.datetime', 'DESC');

		$this->itemsProperties = FALSE;
	}

	/**
	 * Get orders
	 * @return Shop_Order_Model
	 */
	public function shopOrders()
	{
		return $this->_Shop_Orders;
	}

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Shop_Order_Controller_Show.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);

		$oShop = $this->getEntity();

		$oShopPaymentSystemsEntity = Core::factory('Core_Xml_Entity')
				->name('shop_payment_systems');

		$this->addEntity(
			$oShopPaymentSystemsEntity
		);

		$aShop_Payment_Systems = $oShop->Shop_Payment_Systems->getAllByActive(1);
		foreach ($aShop_Payment_Systems as $oShop_Payment_System)
		{
			$oShopPaymentSystemsEntity->addEntity(
				$oShop_Payment_System->clearEntities()
			);
		}

		$aShop_Orders = $this->_Shop_Orders->findAll();
		foreach ($aShop_Orders as $oShop_Order)
		{
			$oShop_Order
				->clearEntities()
				->showXmlCurrency(TRUE)
				->showXmlCountry(TRUE)
				->showXmlItems(TRUE)
				->showXmlDelivery(TRUE)
				->showXmlPaymentSystem(TRUE)
				->showXmlOrderStatus(TRUE)
				->showXmlProperties(TRUE);

			$this->itemsProperties && $oShop_Order->showXmlProperties($this->itemsProperties);

			$this->addEntity($oShop_Order);
		}

		return parent::show();
	}
}