<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Выбор платежной системы.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Payment_System_Controller_Show extends Core_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'shop_delivery_id'
	);

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
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();
			if ($oSiteuser)
			{
				$this->addEntity($oSiteuser->clearEntities());
			}
		}
	}

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Shop_Payment_System_Controller_Show.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);

		$oShop = $this->getEntity();

		$oShop_Payment_Systems = $oShop->Shop_Payment_Systems;

		if ($this->shop_delivery_id)
		{
			$oShop_Payment_Systems
				->queryBuilder()
				->select('shop_payment_systems.*')
				->join('shop_delivery_payment_systems', 'shop_delivery_payment_systems.shop_payment_system_id', '=', 'shop_payment_systems.id')
				->where('shop_delivery_payment_systems.shop_delivery_id', '=', $this->shop_delivery_id);
		}

		$aShop_Payment_Systems = $oShop_Payment_Systems->getAllByActive(1);
		foreach ($aShop_Payment_Systems as $oShop_Payment_System)
		{
			$this->addEntity($oShop_Payment_System->clearEntities());
		}

		return parent::show();
	}
}