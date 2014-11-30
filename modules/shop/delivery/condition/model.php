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
class Shop_Delivery_Condition_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var int
	 */
	public $img=1;

	/**
	 * Backend property
	 * @var int
	 */
	public $currency_name = '';

	/**
	 * Backend property
	 * @var int
	 */
	public $orderfield = '';

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'shop_delivery' => array(),
		'shop_delivery_condition_dir' => array(),
		'shop_country' => array(),
		'shop_country_location' => array(),
		'shop_country_location_city' => array(),
		'shop_country_location_city_area' => array(),
		'shop_tax' => array(),
		'shop_currency' => array()
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'max_weight' => 0.00,
		'min_weight' => 0.00,
		'max_price' => 0.00,
		'min_price' => 0.00,
		'price' => 0.00,
		'sorting' => 0,
		'active' => 1
	);

	/**
	 * Forbidden tags. If list of tags is empty, all tags will show.
	 * @var array
	 */
	protected $_forbiddenTags = array(
		'price',
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
	 * Определение цены товара для условия доставки
	 * Determination of the price of goods for delivery terms
	 * @return array возвращает массив значений цен
	 * - $price['tax'] сумма налога
	 * - $price['rate'] размер налога
	 * - $price['price'] цена с учетом валюты без налога
	 */
	public function getPriceArray()
	{
		$oShop = $this->Shop_Delivery->Shop;

		$price = array(
			'tax' => 0,
			'rate' => 0,
			'price' => $this->price,
			'price_tax' => 0,
			'discount' => 0,
			'discounts' => array()
		);

		// Определяем коэффициент пересчета
		$fCurrencyCoefficient = $this->shop_currency_id > 0 && $oShop->shop_currency_id > 0
			? Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
				$this->Shop_Currency, $oShop->Shop_Currency
			)
			: 0;

		// Умножаем цену товара на курс валюты в базовой валюте
		$price['price'] *= $fCurrencyCoefficient;

		$price['price_tax'] = $price['price_discount'] = $price['price'];

		if ($this->shop_tax_id)
		{
			$oShop_Tax = $this->Shop_Tax;

			if ($oShop_Tax->id)
			{
				$price['rate'] = $oShop_Tax->rate;

				// Если он не входит в цену
				if ($oShop_Tax->tax_is_included == 0)
				{
					// То считаем цену с налогом
					$price['tax'] = $oShop_Tax->rate / 100 * $price['price'];
					$price['price_tax'] = $price['price_discount'] = $price['price'] + $price['tax'];
				}
				else
				{
					$price['tax'] = $price['price'] / (100 + $oShop_Tax->rate) * $oShop_Tax->rate;
					$price['price_tax'] = $price['price'];
					$price['price'] -= $price['tax'];
				}
			}
		}

		$oShop_Controller = Shop_Controller::instance();

		// Округляем значения, переводим с научной нотации 1Е+10 в десятичную
		$price['tax'] = $oShop_Controller->round($price['tax']);
		$price['price'] = $oShop_Controller->round($price['price']);
		$price['price_discount'] = $oShop_Controller->round($price['price_discount']);
		$price['price_tax'] = $oShop_Controller->round($price['price_tax']);

		return $price;
	}

	/**
	 * Get XML for entity and children entities
	 * @return string
	 * @hostcms-event shop_delivery_condition.onBeforeRedeclaredGetXml
	 */
	public function getXml()
	{
		Core_Event::notify($this->_modelName . '.onBeforeRedeclaredGetXml', $this);

		$aPrices = $this->getPriceArray();

		$this->clearXmlTags()
			->addXmlTag('price', $aPrices['price_tax']);

		return parent::getXml();
	}

	/**
	 * Change status
	 */
	public function changeStatus()
	{
		$this->active = 1 - $this->active;
		return $this->save();
	}
}