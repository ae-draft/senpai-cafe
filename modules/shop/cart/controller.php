<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online store cart operations.
 * Работа с корзиной интернет-магазина.
 *
 * Доступные методы:
 *
 * - shop_item_id($id) идентификатор товара
 * - quantity($value) количество товара
 * - postpone(TRUE|FALSE) товар отложен
 * - shop_warehouse_id($id) идентификатор склада
 * - siteuser_id($id) идентификатор пользователя сайта
 * - checkStock(TRUE|FALSE) проверять наличие товара на складе, по умолчанию FALSE
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Cart_Controller extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'shop_item_id',
		'quantity',
		'postpone',
		'shop_warehouse_id',
		'siteuser_id',
		'checkStock',
	);

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->quantity = 1;
		$this->postpone = 0;
		$this->shop_warehouse_id = 0;

		$this->siteuser_id = 0;
		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$this->siteuser_id = $oSiteuser->id;
			}
		}

		$this->checkStock = FALSE;
	}

	/**
	 * The singleton instances.
	 * @var mixed
	 */
	static public $instance = NULL;

	/**
	 * Register an existing instance as a singleton.
	 * @return object
	 */
	static public function instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Move goods from session cart to database
	 * @param Shop_Model $oShop shop
	 * @return self
	 */
	public function moveTemporaryCart(Shop_Model $oShop)
	{
		if ($this->siteuser_id)
		{
			$aShop_Cart = $this->_getAllFromSession($oShop);

			if (count($aShop_Cart))
			{
				foreach ($aShop_Cart as $oShop_Cart)
				{
					$this->clear()
						->shop_item_id($oShop_Cart->shop_item_id)
						->quantity($oShop_Cart->quantity)
						->postpone($oShop_Cart->postpone)
						->shop_warehouse_id($oShop_Cart->shop_warehouse_id)
						->siteuser_id($this->siteuser_id)
						->add();
				}
				$this->clearSessionCart();
			}
		}

		return $this;
	}

	/**
	 * Get all goods in the cart
	 * @param Shop_Model $oShop shop
	 * @return array
	 */
	public function getAll(Shop_Model $oShop)
	{
		// Проверяем наличие данных о пользователе
		$aShop_Cart = $this->siteuser_id
			? $this->_getAllFromDb($oShop)
			: $this->_getAllFromSession($oShop);

		return $aShop_Cart;
	}

	/**
	 * Clear session cart
	 * @return Shop_Cart_Controller
	 */
	public function clearSessionCart()
	{
		Core_Session::start();
		if (isset($_SESSION['hostcmsCart']))
		{
			unset($_SESSION['hostcmsCart']);
		}
		return $this;
	}

	/**
	 * Get all carts from database
	 * @param Shop_Model $oShop shop
	 * @return array
	 */
	protected function _getAllFromDb(Shop_Model $oShop)
	{
		return $oShop->Shop_Carts->getBySiteuserId($this->siteuser_id, FALSE);
	}

	/**
	 * Get all carts from session
	 * @param Shop_Model $oShop shop
	 * @return array
	 */
	protected function _getAllFromSession(Shop_Model $oShop)
	{
		Core_Session::start();

		$shop_id = $oShop->id;

		$aCart = Core_Array::get($_SESSION, 'hostcmsCart', array());
		$aCart[$shop_id] = Core_Array::get($aCart, $shop_id, array());

		$aShop_Cart = array();
		foreach ($aCart[$shop_id] as $shop_item_id => $aCartItem)
		{
			// Temporary object
			$oShop_Cart = Core_Entity::factory('Shop_Cart');
			$oShop_Cart->shop_item_id = $shop_item_id;
			$oShop_Cart->quantity = $aCartItem['quantity'];
			$oShop_Cart->postpone = $aCartItem['postpone'];
			$oShop_Cart->shop_id = $shop_id;
			$oShop_Cart->shop_warehouse_id = $aCartItem['shop_warehouse_id'];
			$oShop_Cart->siteuser_id = 0;
			$aShop_Cart[] = $oShop_Cart;
		}

		return $aShop_Cart;
	}

	/**
	 * Get item from cart
	 * @return object
	 */
	public function get()
	{
		// Проверяем наличие данных о пользователе
		if ($this->siteuser_id)
		{
			$oShop_Cart = Core_Entity::factory('Shop_Cart')
				->getByShopItemIdAndSiteuserId($this->shop_item_id, $this->siteuser_id, FALSE);

			if (is_null($oShop_Cart))
			{
				$oShop_Cart = Core_Entity::factory('Shop_Cart');
				$oShop_Cart->shop_item_id = $this->shop_item_id;
				$oShop_Cart->siteuser_id = $this->siteuser_id;
			}
		}
		else
		{
			Core_Session::start();

			$Shop_Item = Core_Entity::factory('Shop_Item', $this->shop_item_id);
			$aCart = Core_Array::get($_SESSION, 'hostcmsCart', array());
			$aCart[$Shop_Item->shop_id] = Core_Array::get($aCart, $Shop_Item->shop_id, array());
			$aReturn = Core_Array::get($aCart[$Shop_Item->shop_id], $this->shop_item_id, array()) + array(
				'shop_item_id' => $this->shop_item_id,
				'quantity' => 0,
				'postpone' => 0,
				'shop_id' => $Shop_Item->shop_id,
				'shop_warehouse_id' => 0
			);

			$oShop_Cart = (object)$aReturn;
		}
		return $oShop_Cart;
	}

	/**
	 * Delete item from cart
	 * @return Shop_Cart_Controller
	 */
	public function delete()
	{
		// Проверяем наличие данных о пользователе
		if ($this->siteuser_id)
		{
			$oShop_Cart = Core_Entity::factory('Shop_Cart')
				->getByShopItemIdAndSiteuserId($this->shop_item_id, $this->siteuser_id, FALSE);

			!is_null($oShop_Cart) && $oShop_Cart->delete();
		}
		else
		{
			Core_Session::start();
			$oShop_Item = Core_Entity::factory('Shop_Item')->find($this->shop_item_id);
			if (isset($_SESSION['hostcmsCart'][$oShop_Item->shop_id][$this->shop_item_id]))
			{
				unset($_SESSION['hostcmsCart'][$oShop_Item->shop_id][$this->shop_item_id]);
			}
		}
		return $this;
	}

	/**
	 * Add item into cart
	 * @return Shop_Cart_Controller
	 */
	public function add()
	{
		if (is_null($this->shop_item_id))
		{
			throw new Core_Exception('Shop item id is NULL.');
		}

		$oItem_In_Cart = $this->get();

		// Увеличиваем на количество уже в корзине
		$this->quantity += $oItem_In_Cart->quantity;

		return $this->update();
	}

	/**
	 * Update item in cart
	 * @return Shop_Cart_Controller
	 */
	public function update()
	{
		$oShop_Item = Core_Entity::factory('Shop_Item')->find($this->shop_item_id);

		if (!is_null($oShop_Item->id))
		{
			$aSiteuserGroups = array(0, -1);
			if (Core::moduleIsActive('siteuser'))
			{
				$oSiteuser = Core_Entity::factory('Siteuser', $this->siteuser_id);

				if ($oSiteuser)
				{
					$aSiteuser_Groups = $oSiteuser->Siteuser_Groups->findAll();
					foreach ($aSiteuser_Groups as $oSiteuser_Group)
					{
						$aSiteuserGroups[] = $oSiteuser_Group->id;
					}
				}
			}

			// Проверяем право пользователя добавить этот товар в корзину
			if (in_array($oShop_Item->getSiteuserGroupId(), $aSiteuserGroups))
			{
				// Если передано количество товара и товар обычный или электронный
				if ($oShop_Item->type == 1 || $oShop_Item->type == 0)
				{
					// Нужно получить реальное количество товара, если товар электронный
					if ($oShop_Item->type == 1)
					{
						// Получаем количество электронного товара на складе
						$iShop_Item_Digitals = $oShop_Item->Shop_Item_Digitals->getCountDigitalItems();

						if ($iShop_Item_Digitals != -1 && $iShop_Item_Digitals < $this->quantity)
						{
							$this->quantity = $iShop_Item_Digitals;
						}
					}

					// Товар обычный, поэтому intval()
					$this->quantity = intval($this->quantity);
				}
				// Если делимый товар
				elseif ($oShop_Item->type == 2)
				{
					// Товар делимый, поэтому floatval()
					$this->quantity = floatval($this->quantity);
				}

				// Проверять остаток для обычных товаров
				if ($this->checkStock && ($oShop_Item->type == 0 || $oShop_Item->type == 2))
				{
					$iRest = $oShop_Item->getRest();
					$iRest < $this->quantity && $this->quantity = $iRest;
				}

				if ($this->quantity > 0)
				{
					// Проверяем наличие данных о пользователе
					if ($this->siteuser_id)
					{
						$oShop_Cart = Core_Entity::factory('Shop_Cart')
							->getByShopItemIdAndSiteuserId($this->shop_item_id, $this->siteuser_id, FALSE);

						if (is_null($oShop_Cart))
						{
							$oShop_Cart = Core_Entity::factory('Shop_Cart');
							$oShop_Cart->shop_item_id = $this->shop_item_id;
							$oShop_Cart->siteuser_id = $this->siteuser_id;
						}

						// Вставляем данные в таблицу корзины
						$oShop_Cart->quantity = $this->quantity;
						$oShop_Cart->postpone = $this->postpone;
						$oShop_Cart->shop_id = $oShop_Item->shop_id;
						$oShop_Cart->shop_warehouse_id = $this->shop_warehouse_id;
						$oShop_Cart->save();
					}
					else
					{
						Core_Session::start();
						$_SESSION['hostcmsCart'][$oShop_Item->shop_id][$this->shop_item_id] = array(
							'quantity' => $this->quantity,
							'postpone' => $this->postpone,
							'siteuser_id' => $this->siteuser_id,
							'shop_warehouse_id' => $this->shop_warehouse_id
						);
					}
				}
				else
				{
					$this->delete();
				}
			}
		}

		return $this;
	}

	/**
	 * Clear the cart
	 * @return Shop_Cart_Controller
	 */
	public function clear()
	{
		$this->shop_item_id = $this->quantity = $this->postpone = $this->shop_warehouse_id = NULL;
		return $this;
	}
}