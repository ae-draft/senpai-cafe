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
class Shop_Item_Controller extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'count',
		'siteuser'
	);

	/**
	 * Price array
	 * @var array
	 */
	protected $_aPrice = array();

	/**
	 * Get $this->_aPrice
	 * @return array
	 */
	public function getAPrice()
	{
		return $this->_aPrice;
	}

	/**
	 * Set $this->_aPrice
	 * @param array $aPrice
	 * @return array
	 */
	public function setAPrice(array $aPrice)
	{
		$this->_aPrice = $aPrice;
		return $this;
	}

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		// Количество товара по умолчанию равно 1
		$this->count = 1;
	}

	/**
	 * Calculate the cost with tax and discounts
	 * @param float $price price
	 * @param Shop_Item_Model $oShop_Item item
	 * @param boolean $bRound round prices
	 * @return array
	 * @hostcms-event Shop_Item_Controller.onBeforeCalculatePrice
	 * @hostcms-event Shop_Item_Controller.onAfterCalculatePrice
	 */
	public function calculatePrice($price, Shop_Item_Model $oShop_Item, $bRound = TRUE)
	{
		$oShop = $oShop_Item->Shop;

		$this->_aPrice = array(
			'tax' => 0,
			'rate' => 0,
			'price' => $price,
			'discount' => 0,
			'discounts' => array()
		);

		Core_Event::notify(get_class($this) . '.onBeforeCalculatePrice', $this, array($oShop_Item));

		// Определяем коэффициент пересчета
		$fCurrencyCoefficient = $oShop_Item->Shop_Currency->id > 0 && $oShop->Shop_Currency->id > 0
			? Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
				$oShop_Item->Shop_Currency, $oShop->Shop_Currency
			)
			: 0;

		// Умножаем цену товара на курс валюты в базовой валюте
		$this->_aPrice['price'] *= $fCurrencyCoefficient;

		// Определены ли скидки на товар
		$aShop_Item_Discounts = $oShop_Item->Shop_Item_Discounts->findAll();
		if (count($aShop_Item_Discounts))
		{
			// определяем количество скидок на товар
			$discountPercent = 0;

			// Цикл по идентификаторам скидок для товара
			foreach ($aShop_Item_Discounts as $oShop_Item_Discount)
			{
				if ($oShop_Item_Discount->Shop_Discount->isActive())
				{
					$this->_aPrice['discounts'][] = $oShop_Item_Discount->Shop_Discount;
					$discountPercent += $oShop_Item_Discount->Shop_Discount->percent;
				}
			}

			// определяем суммарную величину скидки в валюте
			$this->_aPrice['discount'] = $this->_aPrice['price'] * $discountPercent / 100;

			// вычисляем цену со скидкой как ее разность с величиной скидки
			$this->_aPrice['price_discount'] = $this->_aPrice['price'] - $this->_aPrice['discount'];
		}
		else
		{
			// если скидок нет
			$this->_aPrice['price_discount'] = $this->_aPrice['price'];
		}

		// Выбираем информацию о налогах
		if ($oShop_Item->shop_tax_id)
		{
			// Извлекаем информацию о налоге
			$oShop_Tax = $oShop_Item->Shop_Tax;

			if ($oShop_Tax->id)
			{
				$this->_aPrice['rate'] = $oShop_Tax->rate;

				// Если он не входит в цену
				if ($oShop_Tax->tax_is_included == 0)
				{
					// То считаем цену с налогом
					$this->_aPrice['tax'] = $oShop_Tax->rate / 100 * $this->_aPrice['price_discount'];
					$this->_aPrice['price_tax'] = $this->_aPrice['price_discount'] = $this->_aPrice['price_discount'] + $this->_aPrice['tax'];
				}
				else
				{
					$this->_aPrice['tax'] = $this->_aPrice['price_discount'] / (100 + $oShop_Tax->rate) * $oShop_Tax->rate;
					$this->_aPrice['price_tax'] = $this->_aPrice['price_discount'];
					$this->_aPrice['price'] -= $this->_aPrice['tax'];
				}
			}
			else
			{
				$this->_aPrice['price_tax'] = $this->_aPrice['price_discount'];
			}
		}
		else
		{
			$this->_aPrice['price_tax'] = $this->_aPrice['price_discount'];
		}

		$oShop_Controller = Shop_Controller::instance();

		Core_Event::notify(get_class($this) . '.onAfterCalculatePrice', $this, array($oShop_Item));

		// Округляем значения, переводим с научной нотации 1Е+10 в десятичную
		if ($bRound)
		{
			$this->_aPrice['tax'] = $oShop_Controller->round($this->_aPrice['tax']);
			$this->_aPrice['price'] = $oShop_Controller->round($this->_aPrice['price']);
			$this->_aPrice['price_discount'] = $oShop_Controller->round($this->_aPrice['price_discount']);
			$this->_aPrice['price_tax'] = $oShop_Controller->round($this->_aPrice['price_tax']);
		}

		return $this->_aPrice;
	}

	/**
	 * Get price for current user
	 * @param Shop_Item_Model $oShop_Item item
	 * @return float
	 */
	public function getPrice(Shop_Item_Model $oShop_Item)
	{
		$oShop = $oShop_Item->Shop;

		$price = $oShop_Item->price;

		// Пользователь задан - цена определяется из таблицы товаров
		if ($this->siteuser && Core::moduleIsActive('siteuser'))
		{
			$aPrices = array();

			$aSiteuser_Groups = $this->siteuser->Siteuser_Groups->findAll();
			foreach ($aSiteuser_Groups as $oSiteuser_Group)
			{
				// Может быть создано несколько цен для одной группы пользователей
				$aShop_Prices = Core_Entity::factory('Shop_Price')->getAllBySiteuserGroupAndShop(
					$oSiteuser_Group->id, $oShop->id
				);

				foreach ($aShop_Prices as $oShop_Price)
				{
					// Если есть цена для группы
					if ($oShop_Price)
					{
						// Смотрим, определена ли такая цена для данного товара
						$oShop_Item_Price =$oShop_Item->Shop_Item_Prices->getByPriceId($oShop_Price->id);

						if ($oShop_Item_Price)
						{
							$aPrices[] = $oShop_Item_Price->value;
						}
					}
				}
			}

			count($aPrices) > 0 && $price = min($aPrices);
		}

		return $price;
	}

	/**
	 * Определение цены товара для заданного пользователя $this->siteuser
	 *
	 * @param Shop_Item_Model $oShop_Item товар
	 * @param boolean $bRound round prices
	 * @return array возвращает массив значений цен для данного пользователя
	 * - $price['tax'] сумма налога
	 * - $price['rate'] размер налога
	 * - $price['price'] цена с учетом валюты без налога
	 * - $price['price_tax'] цена с учетом налога
	 * - $price['price_discount'] цена с учетом налога и со скидкой
	 */
	public function getPrices(Shop_Item_Model $oShop_Item, $bRound = TRUE)
	{
		if (is_null($oShop_Item->id))
		{
			throw new Core_Exception('Shop_Item_Controller::getPrices Shop_Item id is NULL.');
		}

		$price = $this->getPrice($oShop_Item);

		// Цены в зависимости от количества самого товара в корзине (а не все корзины)
		$aShop_Specialprices = $oShop_Item->Shop_Specialprices->findAll();
		foreach ($aShop_Specialprices as $oShop_Specialprice)
		{
			if ($this->count >= $oShop_Specialprice->min_quantity && ($this->count <= $oShop_Specialprice->max_quantity || $oShop_Specialprice->max_quantity == 0))
			{
				$price = $oShop_Specialprice->percent != 0
					? $price * $oShop_Specialprice->percent / 100
					: $oShop_Specialprice->price;
				break;
			}
		}

		return $this->calculatePrice($price, $oShop_Item, $bRound);
	}
}