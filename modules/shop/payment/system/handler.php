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
abstract class Shop_Payment_System_Handler
{
	/**
	 * Create instance of payment system
	 * @param Shop_Payment_System_Model $oShop_Payment_System_Model payment system
	 * @return mixed
	 */
	static public function factory(Shop_Payment_System_Model $oShop_Payment_System_Model)
	{
		require_once($oShop_Payment_System_Model->getPaymentSystemFilePath());

		$name = 'Shop_Payment_System_Handler' . intval($oShop_Payment_System_Model->id);
		
		if (class_exists($name))
		{
			return new $name($oShop_Payment_System_Model);
		}
		return NULL;
	}

	/**
	 * List of properties
	 * @var array
	 */
	protected $_aProperties = array();

	/**
	 * Property directories
	 * @var array
	 */
	protected $_aProperty_Dirs = array();

	/**
	 * Params of the order
	 * @var array
	 */
	protected $_orderParams = NULL;

	/**
	 * Set order params
	 * @param array $orderParams
	 * @return self
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeOrderParams
	 * @hostcms-event Shop_Payment_System_Handler.onAfterOrderParams
	 */
	public function orderParams($orderParams)
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeOrderParams', $this, array($orderParams));

		$this->_orderParams = $orderParams + array(
			'invoice' => NULL,
			'acceptance_report' => NULL,
			'coupon_text' => NULL,
		);

		Core_Event::notify('Shop_Payment_System_Handler.onAfterOrderParams', $this, array($orderParams));

		return $this;
	}

	/**
	 * Payment system
	 * @var Shop_Payment_System_Model
	 */
	protected $_Shop_Payment_System_Model = NULL;

	/**
	 * Constructor.
	 * @param Shop_Payment_System_Model $oShop_Payment_System_Model payment system
	 */
	public function __construct(Shop_Payment_System_Model $oShop_Payment_System_Model)
	{
		$this->_Shop_Payment_System_Model = $oShop_Payment_System_Model;
	}

	/**
	 * Executes the business logic.
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeExecute
	 * @hostcms-event Shop_Payment_System_Handler.onAfterExecute
	 */
	public function execute()
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeExecute', $this);

		Core_Session::start();

		!isset($_SESSION['last_order_id']) && $_SESSION['last_order_id'] = 0;

		// Если заказ еще не был оформлен
		if ($_SESSION['last_order_id'] == 0)
		{
			// Оформить новый заказ
			$this->_processOrder();

			$_SESSION['last_order_id'] = $this->_shopOrder->id;
		}
		else
		{
			$this->shopOrder(
				Core_Entity::factory('Shop_Order', intval($_SESSION['last_order_id']))
			);
		}

		Core_Event::notify('Shop_Payment_System_Handler.onAfterExecute', $this);

		return $this;
	}

	/**
	 * Объект заказа до изменения.
	 */
	protected $_shopOrderBeforeAction = NULL;

	/**
	 * Set order before change
	 * @param Shop_Order_Model $oShopOrderBeforeAction
	 * @return self
	 */
	public function shopOrderBeforeAction(Shop_Order_Model $oShopOrderBeforeAction)
	{
		$this->_shopOrderBeforeAction = $oShopOrderBeforeAction;
		return $this;
	}

	/**
	 * Объект заказа
	 */
	protected $_shopOrder = NULL;

	/**
	 * Set order
	 * @param Shop_Order_Model $oShop_Order
	 * @return self
	 */
	public function shopOrder(Shop_Order_Model $oShop_Order)
	{
		$this->_shopOrder = $oShop_Order;
		return $this;
	}

	/**
	 * Get Shop_Order Model
	 * @return Shop_Order_Model
	 */
	public function getShopOrder()
	{
		return $this->_shopOrder;
	}

	/**
	 * Create a new order by $this->_orderParams
	 */
	public function createOrder()
	{
		$oShop = $this->_Shop_Payment_System_Model->Shop;

		$this->_shopOrder = Core_Entity::factory('Shop_Order');
		$this->_shopOrder->shop_country_id = intval(Core_Array::get($this->_orderParams, 'shop_country_id', 0));
		$this->_shopOrder->shop_country_location_id = intval(Core_Array::get($this->_orderParams, 'shop_country_location_id', 0));
		$this->_shopOrder->shop_country_location_city_id = intval(Core_Array::get($this->_orderParams, 'shop_country_location_city_id', 0));
		$this->_shopOrder->shop_country_location_city_area_id = intval(Core_Array::get($this->_orderParams, 'shop_country_location_city_area_id', 0));
		$this->_shopOrder->postcode = strval(Core_Array::get($this->_orderParams, 'postcode', ''));
		$this->_shopOrder->address = strval(Core_Array::get($this->_orderParams, 'address', ''));
		$this->_shopOrder->surname = strval(Core_Array::get($this->_orderParams, 'surname', ''));
		$this->_shopOrder->name = strval(Core_Array::get($this->_orderParams, 'name', ''));
		$this->_shopOrder->patronymic = strval(Core_Array::get($this->_orderParams, 'patronymic', ''));
		$this->_shopOrder->company = strval(Core_Array::get($this->_orderParams, 'company', ''));
		$this->_shopOrder->phone = strval(Core_Array::get($this->_orderParams, 'phone', ''));
		$this->_shopOrder->fax = strval(Core_Array::get($this->_orderParams, 'fax', ''));
		$this->_shopOrder->email = strval(Core_Array::get($this->_orderParams, 'email', ''));
		$this->_shopOrder->description = strval(Core_Array::get($this->_orderParams, 'description', ''));

		$shop_delivery_condition_id = intval(Core_Array::get($this->_orderParams, 'shop_delivery_condition_id', 0));
		$this->_shopOrder->shop_delivery_condition_id = $shop_delivery_condition_id;

		$shop_delivery_id = intval(Core_Array::get($this->_orderParams, 'shop_delivery_id', 0));
		!$shop_delivery_id && $shop_delivery_condition_id && $shop_delivery_id = Core_Entity::factory('Shop_Delivery_Condition', $shop_delivery_condition_id)->shop_delivery_id;
		$this->_shopOrder->shop_delivery_id = $shop_delivery_id;

		$this->_shopOrder->shop_payment_system_id = intval(Core_Array::get($this->_orderParams, 'shop_payment_system_id', 0));
		$this->_shopOrder->shop_currency_id = intval($oShop->shop_currency_id);
		$this->_shopOrder->shop_order_status_id = intval($oShop->shop_order_status_id);
		$this->_shopOrder->tin = strval(Core_Array::get($this->_orderParams, 'tin', ''));
		$this->_shopOrder->kpp = strval(Core_Array::get($this->_orderParams, 'kpp', ''));

		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();
			$oSiteuser && $this->_shopOrder->siteuser_id = $oSiteuser->id;
		}

		// Номер заказа
		$bInvoice = strlen($this->_orderParams['invoice']) > 0;
		$bInvoice && $this->_shopOrder->invoice = Core_Array::get($this->_orderParams, 'invoice');

		// Номер акта
		$bAcceptance_report = strlen($this->_orderParams['acceptance_report']) > 0;
		$bAcceptance_report && $this->_shopOrder->acceptance_report = Core_Array::get($this->_orderParams, 'acceptance_report');

		$oShop->add($this->_shopOrder);

		// Additional order properties
		if (isset($_SESSION['hostcmsOrder']['properties']) && is_array($_SESSION['hostcmsOrder']['properties']))
		{
			foreach ($_SESSION['hostcmsOrder']['properties'] as $aTmp)
			{
				if (count($aTmp) == 2)
				{
					$iProperty_id = $aTmp[0];
					$value = $aTmp[1];

					$oProperty = Core_Entity::factory('Property', $iProperty_id);
					$oProperty_Value = $oProperty->createNewValue($this->_shopOrder->id);

					// Дополнительные свойства
					switch ($oProperty->type)
					{
						case 0: // Int
						case 3: // List
						case 5: // Information system
							$oProperty_Value->value(intval($value));
							$oProperty_Value->save();
						break;
						case 1: // String
						case 4: // Textarea
						case 6: // Wysiwyg
							$oProperty_Value->value(strval($value));
							$oProperty_Value->save();
						break;
						case 8: // Date
							$date = strval($value);
							$date = Core_Date::date2sql($date);
							$oProperty_Value->value($date);
							$oProperty_Value->save();
						break;
						case 9: // Datetime
							$datetime = strval($value);
							$datetime = Core_Date::datetime2sql($datetime);
							$oProperty_Value->value($datetime);
							$oProperty_Value->save();
						break;
						case 2: // File

						break;
						case 7: // Checkbox
							$oProperty_Value->value(is_null($value) ? 0 : 1);
							$oProperty_Value->save();
						break;
					}
				}
			}
		}

		$oShop_Order_Property_List = Core_Entity::factory('Shop_Order_Property_List', $oShop->id);

		$aProperties = $oShop_Order_Property_List->Properties->findAll();
		foreach ($aProperties as $oProperty)
		{
			// Св-во может иметь несколько значений
			$aPropertiesValue = Core_Array::getPost('property_' . $oProperty->id);

			if (!is_null($aPropertiesValue))
			{
				!is_array($aPropertiesValue) && $aPropertiesValue = array($aPropertiesValue);
				foreach ($aPropertiesValue as $sPropertyValue)
				{
					$_SESSION['hostcmsOrder']['properties'][] = array($oProperty->id, strval($sPropertyValue));
				}
			}
		}

		$this->shopOrder($this->_shopOrder);

		// Если не установлен модуль пользователей сайта - записываем в сессию
		// идентификатор вставленного заказа, чтобы далее можно было посмотреть квитаницию
		// об оплате или счет.
		if (!Core::moduleIsActive('siteuser'))
		{
			$_SESSION['order_' . $this->_shopOrder->id] = TRUE;
		}

		// Номер заказа
		!$bInvoice && $this->_shopOrder->invoice($this->_shopOrder->id)->save();

		// Номер акта
		!$bAcceptance_report && $this->_shopOrder->acceptance_report($this->_shopOrder->id)->save();

		return $this;
	}

	/**
	 * Создание нового заказа на основе данных, указанных в orderParams
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeProcessOrder
	 * @hostcms-event Shop_Payment_System_Handler.onAfterProcessOrder
	 */
	protected function _processOrder()
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeProcessOrder', $this);

		if (!count($this->_orderParams) /*is_null($this->_orderParams)*/)
		{
			throw new Core_Exception('orderParams is empty.');
		}

		$oShop = $this->_Shop_Payment_System_Model->Shop;

		// Create new order
		$this->createOrder();

		$quantity = 0;
		$amount = 0;

		$Shop_Cart_Controller = Shop_Cart_Controller::instance();

		Core::moduleIsActive('siteuser') && $oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

		$aShop_Cart = $Shop_Cart_Controller->getAll($oShop);
		foreach ($aShop_Cart as $oShop_Cart)
		{
			if ($oShop_Cart->Shop_Item->id)
			{
				if ($oShop_Cart->postpone == 0)
				{
					$quantity += $oShop_Cart->quantity;

					$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item');
					$oShop_Order_Item->quantity = $oShop_Cart->quantity;
					$oShop_Order_Item->shop_item_id = $oShop_Cart->shop_item_id;
					$oShop_Order_Item->shop_warehouse_id = $oShop_Cart->shop_warehouse_id;

					// Prices
					$oShop_Item_Controller = new Shop_Item_Controller();
					Core::moduleIsActive('siteuser') && $oSiteuser && $oShop_Item_Controller->siteuser($oSiteuser);

					$oShop_Item_Controller->count($oShop_Cart->quantity);

					$oShop_Item = $oShop_Cart->Shop_Item;

					$aPrices = $oShop_Item_Controller->getPrices($oShop_Item, FALSE);
					$amount += $aPrices['price_discount'] * $oShop_Cart->quantity;
					$oShop_Order_Item->price = $aPrices['price_discount'] - $aPrices['tax'];
					$oShop_Order_Item->rate = $aPrices['rate'];
					$oShop_Order_Item->name = $oShop_Item->name;
					$oShop_Order_Item->marking = $oShop_Item->marking;

					$this->_shopOrder->add($oShop_Order_Item);

					// Delete item from the cart
					$Shop_Cart_Controller
						->shop_item_id($oShop_Cart->shop_item_id)
						->delete();
				}
			}
			else
			{
				$oShop_Cart->delete();
			}
		}

		if ($amount > 0)
		{
			// Add a discount to the purchase
			$this->_addPurchaseDiscount($amount, $quantity);
		}

		$this->_addDelivery();

		Core_Event::notify('Shop_Payment_System_Handler.onAfterProcessOrder', $this);

		return $this;
	}

	/**
	 * Add a discount to the purchase
	 * @param float $amount amount
	 * @param float $quantity quantity
	 * @return self
	 */
	protected function _addPurchaseDiscount($amount, $quantity)
	{
		$oShop = $this->_Shop_Payment_System_Model->Shop;

		// Скидки от суммы заказа
		$oShop_Purchase_Discount_Controller = new Shop_Purchase_Discount_Controller($oShop);
		$oShop_Purchase_Discount_Controller
			->amount($amount)
			->quantity($quantity)
			->couponText(trim($this->_orderParams['coupon_text']));

		// Получаем данные о купоне
		$shop_purchase_discount_coupon_id = $shop_purchase_discount_id = 0;
		if (strlen($oShop_Purchase_Discount_Controller->couponText))
		{
			$oShop_Purchase_Discounts_For_Coupon = $oShop->Shop_Purchase_Discounts->getByCouponText(
				$oShop_Purchase_Discount_Controller->couponText
			);
			if (!is_null($oShop_Purchase_Discounts_For_Coupon))
			{
				// ID скидки по купону
				$shop_purchase_discount_id = $oShop_Purchase_Discounts_For_Coupon->id;
				// ID самого купона
				$shop_purchase_discount_coupon_id = $oShop_Purchase_Discounts_For_Coupon->shop_purchase_discount_coupon_id;
			}
		}

		$aShop_Purchase_Discounts = $oShop_Purchase_Discount_Controller->getDiscounts();
		foreach ($aShop_Purchase_Discounts as $oShop_Purchase_Discount)
		{
			$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item');
			$oShop_Order_Item->name = $oShop_Purchase_Discount->name;
			$oShop_Order_Item->quantity = 1;
			$oShop_Order_Item->price = -1 * $oShop_Purchase_Discount->getDiscountAmount();

			if ($oShop_Purchase_Discount->id == $shop_purchase_discount_id)
			{
				$oShop_Purchase_Discount_Coupon = Core_Entity::factory('shop_purchase_discount_coupon')->find(
					$shop_purchase_discount_coupon_id
				);

				// Списываем купон
				if (!is_null($oShop_Purchase_Discount_Coupon->id) && $oShop_Purchase_Discount_Coupon->count != -1 && $oShop_Purchase_Discount_Coupon->count != 0)
				{
					$oShop_Purchase_Discount_Coupon->count = $oShop_Purchase_Discount_Coupon->count - 1;
					$oShop_Purchase_Discount_Coupon->save();
				}
			}

			$this->_shopOrder->add($oShop_Order_Item);
		}

		return $this;
	}

	/**
	 * Add a delivery into the order
	 * @return self
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeAddDelivery
	 * @hostcms-event Shop_Payment_System_Handler.onAfterAddDelivery
	 */
	protected function _addDelivery()
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeAddDelivery', $this);

		$shop_delivery_condition_id = intval(Core_Array::get($this->_orderParams, 'shop_delivery_condition_id', 0));
		$shop_delivery_id = intval(Core_Array::get($this->_orderParams, 'shop_delivery_id', 0));

		// Добавляем стоимость доставки как отдельный товар
		// Доставка может прийти как сущесвтующий shop_delivery_condition_id, так и shop_delivery_id + название рассчитанного условия доставки
		if ($shop_delivery_condition_id || $shop_delivery_id)
		{
			if ($shop_delivery_condition_id)
			{
				$oShop_Delivery_Condition = Core_Entity::factory('Shop_Delivery_Condition', $shop_delivery_condition_id);
				$name = Core::_('Shop_Delivery.delivery', $oShop_Delivery_Condition->Shop_Delivery->name);

				$aPrice = $oShop_Delivery_Condition->getPriceArray();
				$price = $aPrice['price'];
				$rate = $aPrice['rate'];
				$marking = !is_null($oShop_Delivery_Condition->marking)
					? $oShop_Delivery_Condition->marking
					: '';
			}
			// Доставка рассчитывалась кодом
			else
			{
				$oShop_Delivery = Core_Entity::factory('Shop_Delivery', $shop_delivery_id);
				$name = Core::_('Shop_Delivery.delivery', $oShop_Delivery->name);

				$price = floatval(Core_Array::get($this->_orderParams, 'shop_delivery_price', 0));
				$rate = intval(Core_Array::get($this->_orderParams, 'shop_delivery_rate', 0));
				$marking = '';

				$shop_delivery_name = strval(Core_Array::get($this->_orderParams, 'shop_delivery_name', 0));
				$this->_shopOrder->delivery_information = trim(
					$this->_shopOrder->delivery_information .  "\n" . $shop_delivery_name
				);
			}

			$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item');
			$oShop_Order_Item->name = $name;
			$oShop_Order_Item->quantity = 1;
			$oShop_Order_Item->rate = $rate;
			$oShop_Order_Item->price = $price;
			$oShop_Order_Item->marking = $marking;
			$oShop_Order_Item->type = 1;
			$this->_shopOrder->add($oShop_Order_Item);
		}

		Core_Event::notify('Shop_Payment_System_Handler.onAfterAddDelivery', $this);

		return $this;
	}

	/**
	 * XSL данных о заказе
	 */
	protected $_xsl = NULL;

	/**
	 * Set XSL for order data
	 * @param Xsl_Model $oXsl
	 * @return self
	 */
	public function xsl(Xsl_Model $oXsl)
	{
		$this->_xsl = $oXsl;
		return $this;
	}

	/**
	 * Get invoice form
	 * @return mixed
	 */
	public function getInvoice()
	{
		return $this->_processXml();
	}

	/**
	 * Get notification form
	 * @return mixed
	 */
	public function getNotification()
	{
		return $this->_processXml();
	}

	/**
	 * Shows invoice
	 * @return self
	 */
	public function printInvoice()
	{
		echo $this->getInvoice();
		return $this;
	}

	/**
	 * Shows notification
	 * @return self
	 */
	public function printNotification()
	{
		echo $this->getNotification();
		return $this;
	}

	/**
	 * Prepare XML
	 * @return Shop_Model
	 */
	protected function _prepareXml()
	{
		$oShop = $this->_shopOrder->Shop->clearEntities();

		// Список свойств заказа
		$oShop_Order_Property_List = Core_Entity::factory('Shop_Order_Property_List', $oShop->id);

		$aProperties = $oShop_Order_Property_List->Properties->findAll();
		foreach ($aProperties as $oProperty)
		{
			$this->_aProperties[$oProperty->property_dir_id][] = $oProperty->clearEntities();
		}

		$aProperty_Dirs = $oShop_Order_Property_List->Property_Dirs->findAll();
		foreach ($aProperty_Dirs as $oProperty_Dir)
		{
			$oProperty_Dir->clearEntities();
			$this->_aProperty_Dirs[$oProperty_Dir->parent_id][] = $oProperty_Dir->clearEntities();
		}

		// Список свойств
		$Shop_Order_Properties = Core::factory('Core_Xml_Entity')
			->name('properties');

		$oShop->addEntity($Shop_Order_Properties);

		$this->_addPropertiesList(0, $Shop_Order_Properties);

		$oShop
			->addEntity($oShop->Shop_Company)
			->addEntity(
				$oShop->Site->clearEntities()->showXmlAlias()
			)
			->addEntity(
				$this->_shopOrder->clearEntities()
					->showXmlCurrency(TRUE)
					->showXmlCountry(TRUE)
					->showXmlItems(TRUE)
					->showXmlDelivery(TRUE)
					->showXmlPaymentSystem(TRUE)
					->showXmlOrderStatus(TRUE)
					->showXmlProperties(TRUE)
					->showXmlSiteuser(TRUE)
			);

		return $oShop;
	}

	/**
	 * Add list of user's properties to XML
	 * @param int $parent_id parent directory
	 * @param object $parentObject
	 * @return self
	 */
	protected function _addPropertiesList($parent_id, $parentObject)
	{
		if (isset($this->_aProperty_Dirs[$parent_id]))
		{
			foreach ($this->_aProperty_Dirs[$parent_id] as $oProperty_Dir)
			{
				$parentObject->addEntity($oProperty_Dir);
				$this->_addPropertiesList($oProperty_Dir->id, $oProperty_Dir);
			}
		}

		if (isset($this->_aProperties[$parent_id]))
		{
			$parentObject->addEntities($this->_aProperties[$parent_id]);
		}

		return $this;
	}

	/**
	 * Process XML
	 * @return mixed
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeProcessXml
	 * @hostcms-event Shop_Payment_System_Handler.onAfterProcessXml
	 */
	protected function _processXml()
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeProcessXml', $this);

		$sXml = $this->_prepareXml()->getXml();

		//echo "<pre>" . htmlspecialchars($sXml) . "</pre>";
		$return = Xsl_Processor::instance()
			->xml($sXml)
			->xsl($this->_xsl)
			->process();

		$this->_shopOrder->clearEntities();

		Core_Event::notify('Shop_Payment_System_Handler.onAfterProcessXml', $this);

		return $return;
	}

	/**
	 * XSL письма администратору о заказе
	 */
	protected $_xslAdminMail = NULL;

	/**
	 * Set XSL for admin's e-mail
	 * @param Xsl_Model $oXsl
	 * @return self
	 */
	public function xslAdminMail(Xsl_Model $oXsl)
	{
		$this->_xslAdminMail = $oXsl;
		return $this;
	}

	/**
	 * XSL письма пользователю о заказе
	 */
	protected $_xslSiteuserMail = NULL;

	/**
	 * Set XSL for user's e-mail
	 * @param Xsl_Model $oXsl
	 * @return self
	 */
	public function xslSiteuserMail(Xsl_Model $oXsl)
	{
		$this->_xslSiteuserMail = $oXsl;
		return $this;
	}

	/**
	 * Content-type письма администратору о заказе
	 */
	protected $_adminMailContentType = 'text/html';

	/**
	 * Set Content-type of admin's e-mail
	 * @param string $contentType Content-type
	 * @return self
	 */
	public function adminMailContentType($contentType)
	{
		$this->_adminMailContentType = $contentType;
		return $this;
	}

	/**
	 * Content-type письма пользователю о заказе
	 */
	protected $_siteuserMailContentType = 'text/html';

	/**
	 * Set Content-type of user's e-mail
	 * @param string $contentType Content-type
	 * @return self
	 */
	public function siteuserMailContentType($contentType)
	{
		$this->_siteuserMailContentType = $contentType;
		return $this;
	}

	/**
	 * Тема письма администратору о заказе
	 */
	protected $_adminMailSubject = NULL;

	/**
	 * Set subject to shop's administrator e-mail
	 * @param string $subject subject
	 * @return self
	 */
	public function adminMailSubject($subject)
	{
		$this->_adminMailSubject = $subject;
		return $this;
	}

	/**
	 * Тема письма пользователю о заказе
	 */
	protected $_siteuserMailSubject = NULL;

	/**
	 * Set subject to user e-mail
	 * @param string $subject subject
	 * @return self
	 */
	public function siteuserMailSubject($subject)
	{
		$this->_siteuserMailSubject = $subject;
		return $this;
	}

	/**
	 * Get user mail
	 * @return Core_Mail
	 */
	public function getSiteuserEmail()
	{
		return Core_Mail::instance();
	}

	/**
	 * Get admin e-mail
	 * @return Core_Mail
	 */
	public function getAdminEmail()
	{
		return Core_Mail::instance();
	}

	/**
	 * Set XSLs to e-mail
	 * @return self
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeSetXSLs
	 * @hostcms-event Shop_Payment_System_Handler.onAfterSetXSLs
	 */
	public function setXSLs()
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeSetXSLs', $this);

		$oShopOrder = $this->_shopOrder;
		$oStructure = $oShopOrder->Shop->Structure;
		$libParams = $oStructure->Lib->getDat($oStructure->id);

		$this->xslAdminMail(
			Core_Entity::factory('Xsl')->getByName(
				Core_Array::get($libParams, 'orderAdminNotificationXsl')
			)
		)
		->xslSiteuserMail(
			Core_Entity::factory('Xsl')->getByName(
				Core_Array::get($libParams, 'orderUserNotificationXsl')
			)
		);

		Core_Event::notify('Shop_Payment_System_Handler.onAfterSetXSLs', $this);

		return $this;
	}

	/**
	 * Send emails about order
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeSend
	 * @hostcms-event Shop_Payment_System_Handler.onAfterSend
	 */
	public function send()
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeSend', $this);

		if (is_null($this->_shopOrder))
		{
			throw new Core_Exception('send(): shopOrder is empty.');
		}

		$oShopOrder = $this->_shopOrder;
		$oShop = $oShopOrder->Shop;

		// Проверяем необходимость отправить письмо администратору
		if ($oShop->send_order_email_admin)
		{
			$oCore_Mail_Admin = $this->getAdminEmail();
			$this->sendAdminEmail($oCore_Mail_Admin);
		}

		if ($oShop->send_order_email_user)
		{
			$oCore_Mail_Siteuser = $this->getSiteuserEmail();
			$this->sendSiteuserEmail($oCore_Mail_Siteuser);
		}

		Core_Event::notify('Shop_Payment_System_Handler.onAfterSend', $this);

		return $this;
	}

	/**
	 * Get array of admin emails
	 * @return array
	 */
	public function getAdminEmails()
	{
		$oShop = $this->_shopOrder->Shop;

		return trim($oShop->email) != ''
			? explode(',', $oShop->email)
			: array(EMAIL_TO);
	}

    public function sendDft($mailTo)
    {
        $smsDeliveryMailXsl = Core_Entity::factory('Xsl')->getByName("ПисьмоАдминистратору_sms");

        $this->xsl($smsDeliveryMailXsl);
        $sInvoice = $this->_processXml();
        $sInvoice = str_replace("\r\n",'',$sInvoice);
        $sInvoice = str_replace("\n",'',$sInvoice);
        $message = trim($sInvoice);
        $messLen = strlen($message);
        $smsArr = array();
        $j = 0;

        $body=file_get_contents("http://sms.ru/sms/send?api_id=44424a79-ef90-93c4-e974-58dd5d39ae7a&to=79371480438&from=79371480438&text=".urlencode($message));


        return $body;
    }

	/**
	 * Send e-mail to shop's administrator
	 * @param Core_Mail $oCore_Mail mail
	 * @return self
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeSendAdminEmail
	 * @hostcms-event Shop_Payment_System_Handler.onAfterSendAdminEmail
	 */
	public function sendAdminEmail(Core_Mail $oCore_Mail)
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeSendAdminEmail', $this, array($oCore_Mail));

		$oShopOrder = $this->_shopOrder;
		$oShop = $oShopOrder->Shop;

		// В адрес "ОТ КОГО" для администратора указывается email пользователя
		$from = Core_Valid::email($oShopOrder->email)
			? $oShopOrder->email
			: $oShop->getFirstEmail();

		$this->xsl($this->_xslAdminMail);
		$sInvoice = $this->_processXml();
		$sInvoice = str_replace(">", ">\n", $sInvoice);

		// Тема письма администратору
		$date_str = Core_Date::sql2datetime($oShopOrder->datetime);
		$admin_subject = !is_null($this->_adminMailSubject)
			? $this->_adminMailSubject
			: Core::_('Shop_Order.shop_order_admin_subject', $oShopOrder->invoice, $oShop->name, $date_str);

		$oCore_Mail
			->from($from)
			->subject($admin_subject)
			->message($sInvoice)
			->contentType($this->_adminMailContentType)
			->header('X-HostCMS-Reason', 'Order')
			->header('Precedence', 'bulk');

		$aEmails = $this->getAdminEmails();

		foreach ($aEmails as $sEmail)
		{
			$sEmail = trim($sEmail);
			if (Core_Valid::email($sEmail))
			{
				$oCore_Mail->to($sEmail)->send();

				// Anti spam filter
				sleep(1);
			}
		}

		Core_Event::notify('Shop_Payment_System_Handler.onAfterSendAdminEmail', $this, array($oCore_Mail));

		return $this;
	}

	/**
	 * Attach digital items to mail
	 * @param Core_Mail $oCore_Mail mail
	 * @return self
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeAttachDigitalItems
	 * @hostcms-event Shop_Payment_System_Handler.onAfterAttachDigitalItems
	 */
	protected function _attachDigitalItems(Core_Mail $oCore_Mail)
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeAttachDigitalItems', $this, array($oCore_Mail));

		$aShop_Order_Items = $this->_shopOrder->Shop_Order_Items->findAll(FALSE);
		foreach ($aShop_Order_Items as $oShop_Order_Item)
		{
			// Digital items
			$aShop_Order_Item_Digitals = $oShop_Order_Item->Shop_Order_Item_Digitals->findAll();
			foreach ($aShop_Order_Item_Digitals as $oShop_Order_Item_Digital)
			{
				$oShop_Item_Digital = $oShop_Order_Item_Digital->Shop_Item_Digital;

				if ($oShop_Item_Digital->filename != '' && is_file($oShop_Item_Digital->getFullFilePath()))
				{
					$oCore_Mail->attach(array(
					 'filepath' => $oShop_Item_Digital->getFullFilePath(),
					 'filename' => $oShop_Item_Digital->filename,
					 ));
				}
			}
		}

		Core_Event::notify('Shop_Payment_System_Handler.onAfterAttachDigitalItems', $this, array($oCore_Mail));

		return $this;
	}

	/**
	 * Send e-mail to user
	 * @param Core_Mail $oCore_Mail mail
	 * @return self
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeSendSiteuserEmail
	 * @hostcms-event Shop_Payment_System_Handler.onAfterSendSiteuserEmail
	 */
	public function sendSiteuserEmail(Core_Mail $oCore_Mail)
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeSendSiteuserEmail', $this, array($oCore_Mail));

		$oShopOrder = $this->_shopOrder;
		$oShop = $oShopOrder->Shop;

		$to = $oShopOrder->email;

		if (Core_Valid::email($to))
		{
			// Адрес "ОТ КОГО" для пользователя
			$from = $oShop->getFirstEmail();

			$this->xsl($this->_xslSiteuserMail);
			$sInvoice = $this->_processXml();
			$sInvoice = str_replace(">", ">\n", $sInvoice);

			$date_str = Core_Date::sql2datetime($oShopOrder->datetime);
			// Тема письма пользователю
			$user_subject = !is_null($this->_siteuserMailSubject)
				? $this->_siteuserMailSubject
				: Core::_('Shop_Order.shop_order_admin_subject', $oShopOrder->invoice, $oShop->name, $date_str);

			// Attach digitals items
			if ($this->_shopOrder->paid == 1 && $this->_shopOrder->Shop->attach_digital_items == 1)
			{
				$this->_attachDigitalItems($oCore_Mail);
			}

			$oCore_Mail
				->from($from)
				->to($to)
				->subject($user_subject)
				->message($sInvoice)
				->contentType($this->_siteuserMailContentType)
				->header('X-HostCMS-Reason', 'OrderConfirm')
				->header('Precedence', 'bulk')
				->send();
		}

		Core_Event::notify('Shop_Payment_System_Handler.onAfterSendSiteuserEmail', $this, array($oCore_Mail));

		return $this;
	}

	/**
	 * Уведомление об операциях с заказом
	 * @param string $mode режим изменения:
	 * - edit - редактирование заказа
	 * - changeStatusPaid - изменение статуса оплаты из списка заказов
	 * @hostcms-event Shop_Payment_System_Handler.onBeforeChangedOrder
	 * @hostcms-event Shop_Payment_System_Handler.onAfterChangedOrder
	 */
	public function changedOrder($mode)
	{
		Core_Event::notify('Shop_Payment_System_Handler.onBeforeChangedOrder', $this, array($mode));

		if (in_array($mode, array('changeStatusPaid', 'edit')))
		{
			if ($this->_shopOrderBeforeAction->paid != $this->_shopOrder->paid)
			{
				$date_str = Core_Date::sql2datetime($this->_shopOrder->datetime);

				$this->adminMailSubject(
					Core::_('Shop_Order.confirm_admin_subject', $this->_shopOrder->invoice, $this->_shopOrder->Shop->name, $date_str)
				);

				$this->siteuserMailSubject(
					Core::_('Shop_Order.confirm_user_subject', $this->_shopOrder->invoice, $this->_shopOrder->Shop->name, $date_str)
				);

				// Установка XSL-шаблонов в соответствии с настройками в узле структуры
				$this->setXSLs();

				// Отправка писем клиенту и пользователю
				$this->send();
			}
		}

		Core_Event::notify('Shop_Payment_System_Handler.onAfterChangedOrder', $this, array($mode));

		return $this;
	}
}