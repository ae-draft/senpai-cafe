<?php

$oShop = Core_Entity::factory('Shop', Core_Array::get(Core_Page::instance()->libParams, 'shopId'));

// ------------------------------------------------
// Обработка запросов от Яндекс.Денег
// ------------------------------------------------
if (isset($_POST['action']) && isset($_POST['invoiceId']) && isset($_POST['orderNumber']))
{
	// Получаем ID заказа
	$order_id = intval(Core_Array::getPost('orderNumber'));

	$oShop_Order = Core_Entity::factory('Shop_Order')->find($order_id);

	if (!is_null($oShop_Order->id))
	{
		header("Content-type: application/xml");

		// Вызов обработчика платежной системы
		Shop_Payment_System_Handler::factory($oShop_Order->Shop_Payment_System)
			->shopOrder($oShop_Order)
			->paymentProcessing();
	}
}

if (isset($_REQUEST['type']))
{
	// Получаем ID заказа
	$order_id = intval(Core_Array::getPost('pay_for'));

	$oShop_Order = Core_Entity::factory('Shop_Order')->find($order_id);

	if (!is_null($oShop_Order->id))
	{
		header("Content-type: application/xml");

		// Вызов обработчика платежной системы
		Shop_Payment_System_Handler::factory($oShop_Order->Shop_Payment_System)
			->shopOrder($oShop_Order)
			->paymentProcessing();
	}
}

// ------------------------------------------------
// Обработка уведомления об оплате от IntellectMoney
// ------------------------------------------------
if (isset($_REQUEST['orderId']))
{
	// Получаем ID заказа
	$order_id = intval(Core_Array::getRequest('orderId'));

	$oShop_Order = Core_Entity::factory('Shop_Order')->find($order_id);

	if (!is_null($oShop_Order->id))
	{
		// Вызов обработчика платежной системы
		Shop_Payment_System_Handler::factory($oShop_Order->Shop_Payment_System)
			->shopOrder($oShop_Order)
			->paymentProcessing();
	}
}
// ------------------------------------------------
// Обработка уведомления об оплате от Interkassa
// ------------------------------------------------
if (isset($_REQUEST['ik_shop_id']))
{
	// Получаем ID заказа
	$order_id = intval(Core_Array::getRequest('ik_payment_id'));

	$oShop_Order = Core_Entity::factory('Shop_Order')->find($order_id);

	if (!is_null($oShop_Order->id))
	{
		// Вызов обработчика платежной системы
		Shop_Payment_System_Handler::factory($oShop_Order->Shop_Payment_System)
			->shopOrder($oShop_Order)
			->paymentProcessing();
	}
}

// ------------------------------------------------
// Обработка уведомления об оплате от ROBOKASSA
// должно быть только в настройках типовой дин. страницы
// ------------------------------------------------
if (isset($_REQUEST['SignatureValue'])
// для отличия от SuccessURL/FailURL
&& !isset($_REQUEST['Culture']))
{
	// Получаем ID заказа
	$order_id = intval(Core_Array::getRequest('InvId'));

	$oShop_Order = Core_Entity::factory('Shop_Order')->find($order_id);

	if (!is_null($oShop_Order->id))
	{
		// Вызов обработчика платежной системы
		Shop_Payment_System_Handler::factory($oShop_Order->Shop_Payment_System)
			->shopOrder($oShop_Order)
			->paymentProcessing();
	}

	exit();
}

// ------------------------------------------------
// Обработка уведомления об оплате от PayAnyWay
// ------------------------------------------------
if (isset($_REQUEST['MNT_OPERATION_ID']))
{
	// Получаем ID заказа
	$order_id = intval(Core_Array::getRequest('MNT_TRANSACTION_ID'));

	$oShop_Order = Core_Entity::factory('Shop_Order')->find($order_id);

	if (!is_null($oShop_Order->id))
	{
		// Вызов обработчика платежной системы
		Shop_Payment_System_Handler::factory($oShop_Order->Shop_Payment_System)
			->shopOrder($oShop_Order)
			->paymentProcessing();
	}
	exit();
}

// Добавление товара в корзину
if (Core_Array::getRequest('add'))
{
	$shop_item_id = intval(Core_Array::getRequest('add'));

	if ($shop_item_id)
	{
		$oShop_Cart_Controller = Shop_Cart_Controller::instance();
		$oShop_Cart_Controller
			->checkStock(FALSE)
			->shop_item_id($shop_item_id)
			->quantity(Core_Array::getRequest('count', 1))
			->add();
	}

	// Ajax
	if (Core_Array::getRequest('_', FALSE))
	{
		ob_start();

		// Краткая корзина
		$Shop_Cart_Controller_Show = new Shop_Cart_Controller_Show(
			$oShop
		);
		$Shop_Cart_Controller_Show
			->xsl(Core_Entity::factory('Xsl')->getByName("МагазинКорзинаКраткаяSenpaiMode")
			/*
				Core_Entity::factory('Xsl')->getByName(
					Core_Array::get(Core_Page::instance()->libParams, 'littleCartXsl')
				)*/
			)
			->couponText(Core_Array::get($_SESSION, 'coupon_text'))
			->show();

		echo json_encode(ob_get_clean());
		exit();
	}
}

// Добавление товара в корзину
if (Core_Array::getRequest('add_r'))
{
	$shop_item_id = intval(Core_Array::getRequest('add_r'));

	if ($shop_item_id)
	{
		$oShop_Cart_Controller = Shop_Cart_Controller::instance();
		$oShop_Cart_Controller
			->checkStock(FALSE)
			->shop_item_id($shop_item_id)
			->quantity(Core_Array::getRequest('count', 1))
			->add();
	}

	// Ajax
	if (Core_Array::getRequest('_', FALSE))
	{
		ob_start();

		// Краткая корзина
		$Shop_Cart_Controller_Show = new Shop_Cart_Controller_Show(
			$oShop
		);
		$Shop_Cart_Controller_Show
			->xsl(Core_Entity::factory('Xsl')->getByName("МагазинКорзинаКраткаяSenpaiMode_responsive")
			/*
				Core_Entity::factory('Xsl')->getByName(
					Core_Array::get(Core_Page::instance()->libParams, 'littleCartXsl')
				)*/
			)
			->couponText(Core_Array::get($_SESSION, 'coupon_text'))
			->show();

		echo json_encode(ob_get_clean());
		exit();
	}
}



if (Core_Array::getGet('action') == 'repeat')
{
	$guid = Core_Array::getGet('guid');
	if (strlen($guid))
	{
		$oShop_Order = $oShop->Shop_Orders->getByGuid($guid);

		if (!is_null($oShop_Order))
		{
			$aShop_Order_Items = $oShop_Order->Shop_Order_Items->findAll();

			$oShop_Cart_Controller = Shop_Cart_Controller::instance();

			foreach ($aShop_Order_Items as $oShop_Order_Item)
			{
				$oShop_Order_Item->shop_item_id && $oShop_Cart_Controller
					->checkStock(FALSE)
					->shop_item_id($oShop_Order_Item->shop_item_id)
					->quantity($oShop_Order_Item->quantity)
					->add();
			}
		}
	}
}

if (!is_null(Core_Array::getGet('ajaxLoad')))
{
	$aObjects = array();

	if (Core_Array::getGet('shop_country_id'))
	{
		$oShop_Country_Location = Core_Entity::factory('Shop_Country_Location');
		$oShop_Country_Location
			->queryBuilder()
			->where('shop_country_id', '=', intval(Core_Array::getGet('shop_country_id')));
		$aObjects = $oShop_Country_Location->findAll();
	}
	elseif (Core_Array::getGet('shop_country_location_id'))
	{
		$oShop_Country_Location_City = Core_Entity::factory('Shop_Country_Location_City');
		$oShop_Country_Location_City
			->queryBuilder()
			->where('shop_country_location_id', '=', intval(Core_Array::getGet('shop_country_location_id')));
		$aObjects = $oShop_Country_Location_City->findAll();
	}
	elseif (Core_Array::getGet('shop_country_location_city_id'))
	{
		$oShop_Country_Location_City_Area = Core_Entity::factory('Shop_Country_Location_City_Area');
		$oShop_Country_Location_City_Area
			->queryBuilder()
			->where('shop_country_location_city_id', '=', intval(Core_Array::getGet('shop_country_location_city_id')));
		$aObjects = $oShop_Country_Location_City_Area->findAll();
	}

	$aArray = array('…');
	foreach ($aObjects as $Object)
	{
		$aArray['_' . $Object->id] = $Object->name;
	}

	echo json_encode($aArray);
	exit();
}

// Удаляение товара из корзины
if (Core_Array::getGet('delete'))
{
	$shop_item_id = intval(Core_Array::getGet('delete'));

	if ($shop_item_id)
	{
		$oShop_Cart_Controller = Shop_Cart_Controller::instance();
		$oShop_Cart_Controller
			->shop_item_id($shop_item_id)
			->delete();
	}
}

if (Core_Array::getPost('recount') || Core_Array::getPost('step') == 1)
{
	$oShop_Cart_Controller = Shop_Cart_Controller::instance();
	$aCart = $oShop_Cart_Controller->getAll($oShop);

	foreach ($aCart as $oShop_Cart)
	{
		$oShop_Cart_Controller
			->checkStock(FALSE)
			->shop_item_id($oShop_Cart->shop_item_id)
			->quantity(Core_Array::getPost('quantity_' . $oShop_Cart->shop_item_id))
			->postpone(is_null(Core_Array::getPost('postpone_' . $oShop_Cart->shop_item_id)) ? 0 : 1)
			->shop_warehouse_id(Core_Array::getPost('warehouse_' . $oShop_Cart->shop_item_id, 0))
			->update();
	}

	// Запоминаем купон
	$_SESSION['hostcmsOrder']['coupon_text'] = trim(strval(Core_Array::getPost('coupon_text')));
}

$Shop_Cart_Controller_Show = new Shop_Cart_Controller_Show($oShop);

Core_Page::instance()->object = $Shop_Cart_Controller_Show;