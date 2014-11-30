<?php
/**
 * Online shop.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../../../bootstrap.php');

Core_Auth::authorization('shop');

// Код формы
$iAdmin_Form_Id = 65;
$sFormAction = '/admin/shop/item/index.php';

$oAdmin_Form = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id);

$oShop = Core_Entity::factory('Shop', Core_Array::getGet('shop_id', 0));
$oShopGroup = Core_Entity::factory('Shop_Group', Core_Array::getGet('shop_group_id', 0));
$oShopDir = $oShop->Shop_Dir;

$sFormTitle = $oShop->name;

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create($oAdmin_Form);
$oAdmin_Form_Controller
	->setUp()
	->path($sFormAction)
	->title($sFormTitle)
	->pageTitle($sFormTitle);

// Меню формы
$oMenu = Admin_Form_Entity::factory('Menus');

$additionalParams = "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}";

// Элементы меню
$oMenu->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Shop_Item.links_items'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.links_items_add'))
				->img('/admin/images/page_add.gif')
				->href(
					$oAdmin_Form_Controller->getAdminActionLoadHref
					(
						$oAdmin_Form_Controller->getPath(), 'edit', NULL, 1, 0
					)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminActionLoadAjax
					(
						$oAdmin_Form_Controller->getPath(), 'edit', NULL, 1, 0
					)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.shops_add_form_link_properties'))
				->img('/admin/images/page_gear.gif')
				->href(
          $oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/item/property/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
          $oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/property/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.properties_item_for_groups_link'))
				->img('/admin/images/folder_page_gear.gif')
				->href(
          $oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/item/property/for/group/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
          $oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/property/for/group/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.items_catalog_add_form_comment_link'))
				->img('/admin/images/comments.gif')
				->href(
          $oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/item/comment/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
          $oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/comment/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.change_prices_for_shop_group'))
				->img('/admin/images/service.gif')
				->href(
          $oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/item/change/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
          $oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/change/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.import_price_list_link'))
				->img('/admin/images/import.gif')
				->href(
          $oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/item/import/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
          $oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/import/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.export_shop'))
				->img('/admin/images/export.gif')
				->href(
          $oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/item/export/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
          $oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/export/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.item_cards'))
				->img('/admin/images/export.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/item/card/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/card/index.php', NULL, NULL, $additionalParams)
				)
		)
)->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Shop_Group.links_groups'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Group.links_groups_add'))
				->img('/admin/images/folder_add.gif')
				->href(
					$oAdmin_Form_Controller->getAdminActionLoadHref
					(
						$oAdmin_Form_Controller->getPath(), 'edit', NULL, 0, 0
					)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminActionLoadAjax
					(
						$oAdmin_Form_Controller->getPath(), 'edit', NULL, 0, 0
					)
				)
		)
    ->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Group.shops_add_form_link_properties_for_group'))
				->img('/admin/images/folder_gear.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/group/property/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/group/property/index.php', NULL, NULL, $additionalParams)
				)
		)
)->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Shop_Item.shops_link_orders'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.shops_add_form_link_orders'))
				->img('/admin/images/order.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/order/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/order/index.php', NULL, NULL, $additionalParams)
				)
		)
)->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Shop_Item.show_delivery_on'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.show_type_of_delivery_link'))
				->img('/admin/images/type_of_delivery.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/delivery/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/delivery/index.php', NULL, NULL, $additionalParams)
				)
		)
)->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Shop_Item.show_sds_link'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.show_prices_title'))
				->img('/admin/images/prices.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/price/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/price/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.system_of_pays'))
				->img('/admin/images/payment.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/payment/system/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/payment/system/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.show_producers_link'))
				->img('/admin/images/company.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/producer/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/producer/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.show_sellers_link'))
				->img('/admin/images/company.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/seller/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/seller/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.main_menu_warehouses_list'))
				->img('/admin/images/company.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/warehouse/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/warehouse/index.php', NULL, NULL, $additionalParams)
				)
		)
)->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Shop_Item.show_reports_title'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.show_sales_order_link'))
				->img('/admin/images/report.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/order/report/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/order/report/index.php', NULL, NULL, $additionalParams)
				)
		)
)->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Shop_Item.shop_menu_title'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.show_discount_link'))
				->img('/admin/images/money.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/discount/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/discount/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.order_discount_show_title'))
				->img('/admin/images/money.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/purchase/discount/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/purchase/discount/index.php', NULL, NULL, $additionalParams)
				)
		)
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.coupon_group_link'))
				->img('/admin/images/money.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/purchase/discount/coupon/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/purchase/discount/coupon/index.php', NULL, NULL, $additionalParams)
				)
		)
)->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Shop_Item.affiliate_menu_title'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Shop_Item.affiliate_menu_title'))
				->img('/admin/images/money.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/affiliate/plan/index.php', NULL, NULL, $additionalParams)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/affiliate/plan/index.php', NULL, NULL, $additionalParams)
				)
		)
);

// Добавляем все меню контроллеру
$oAdmin_Form_Controller->addEntity($oMenu);

// Хлебные крошки
$oBreadcrumbs = Admin_Form_Entity::factory('Breadcrumbs');

// Первая крошка на список магазинов
$oBreadcrumbs->add(
Admin_Form_Entity::factory('Breadcrumb')
	->name(Core::_('Shop.menu'))
	->href($oAdmin_Form_Controller->getAdminLoadHref(
		'/admin/shop/index.php', NULL, NULL, ''
	))
	->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
		'/admin/shop/index.php', NULL, NULL, ''
	))
);

// Крошки по директориям магазинов
if($oShopDir->id)
{
	$oShopDirBreadcrumbs = $oShopDir;

	$aBreadcrumbs = array();

	do
	{
		$aBreadcrumbs[] = Admin_Form_Entity::factory('Breadcrumb')
		->name($oShopDirBreadcrumbs->name)
		->href($oAdmin_Form_Controller->getAdminLoadHref(
				'/admin/shop/index.php', NULL, NULL, "shop_dir_id={$oShopDirBreadcrumbs->id}"
		))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
				'/admin/shop/index.php', NULL, NULL, "shop_dir_id={$oShopDirBreadcrumbs->id}"
		));
	}while($oShopDirBreadcrumbs = $oShopDirBreadcrumbs->getParent());

	$aBreadcrumbs = array_reverse($aBreadcrumbs);

	foreach ($aBreadcrumbs as $oBreadcrumb)
	{
		$oBreadcrumbs->add($oBreadcrumb);
	}
}

// Крошка на список товаров и групп товаров магазина
$oBreadcrumbs->add(
Admin_Form_Entity::factory('Breadcrumb')
	->name($oShop->name)
	->href($oAdmin_Form_Controller->getAdminLoadHref
	(
		$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}"
	))
	->onclick($oAdmin_Form_Controller->getAdminLoadAjax
	(
		$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}"
	))
);

// Крошки по группам товаров
if($oShopGroup->id)
{
	$oShopGroupBreadcrumbs = $oShopGroup;

	$aBreadcrumbs = array();

	do
	{
		$aBreadcrumbs[] = Admin_Form_Entity::factory('Breadcrumb')
			->name($oShopGroupBreadcrumbs->name)
			->href($oAdmin_Form_Controller->getAdminLoadHref
			(
				'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroupBreadcrumbs->id}"
			))
			->onclick($oAdmin_Form_Controller->getAdminLoadAjax
			(
				'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroupBreadcrumbs->id}"
			));
	}while($oShopGroupBreadcrumbs = $oShopGroupBreadcrumbs->getParent());

	$aBreadcrumbs = array_reverse($aBreadcrumbs);

	foreach ($aBreadcrumbs as $oBreadcrumb)
	{
		$oBreadcrumbs->add($oBreadcrumb);
	}
}

$oAdmin_Form_Controller->addEntity($oBreadcrumbs);

// Действие "Редактировать"
$oEditAction = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('edit');

if ($oEditAction)
{
	$oEditController = Admin_Form_Action_Controller::factory(
		'Shop_Item_Controller_Edit', $oEditAction
	);
	$oEditController->addEntity($oBreadcrumbs);
	$oAdmin_Form_Controller->addAction($oEditController);
}

// Действие "Создать ярлык"
$oAction = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('shortcut');

if ($oAction && $oAdmin_Form_Controller->getAction() == 'shortcut' && $oEditAction)
{
	$oShortcutController = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Shortcut', $oAction
	);

	$oShortcutController
		->title(Core::_('Shop_Item.shortcut_creation_window_caption'))
		->selectCaption(Core::_('Shop_Item.add_item_shortcut_shop_groups_id'))
		->selectOptions(array(' … ') + Shop_Item_Controller_Edit::fillShopGroup($oShop->id))
		->value($oShopGroup->id);

	$oAdmin_Form_Controller->addAction($oShortcutController);
}

// Действие "Загрузка элементов магазина"
$oAdminFormActionLoadShopItemList = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('loadShopItemList');

if ($oAdminFormActionLoadShopItemList && $oAdmin_Form_Controller->getAction() == 'loadShopItemList')
{
	$oShop_Controller_Load_Select_Options = Admin_Form_Action_Controller::factory(
		'Shop_Controller_Load_Select_Options',  $oAdminFormActionLoadShopItemList
	);

	$oShop_Controller_Load_Select_Options
		->model(
			Core_Entity::factory('Shop_Item')->shop_id($oShop->id)
		)
		->defaultValue(' … ')
		->addCondition(
			array('where' => array('shop_group_id', '=', $oShopGroup->id))
		)->addCondition(
			array('where' => array('shop_id', '=', $oShop->id))
		);

	$oAdmin_Form_Controller->addAction($oShop_Controller_Load_Select_Options);
}

// Действие "Применить"
$oAction = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('apply');

if ($oAction && $oAdmin_Form_Controller->getAction() == 'apply')
{
	$oApplyController = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Apply', $oAction
	);

	$oAdmin_Form_Controller->addAction($oApplyController);
}

// Действие "Копировать"
$oAdminFormActionCopy = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('copy');

if ($oAdminFormActionCopy && $oAdmin_Form_Controller->getAction() == 'copy')
{
	$oControllerCopy = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Copy', $oAdminFormActionCopy
	);

	// Добавляем типовой контроллер редактирования контроллеру формы
	$oAdmin_Form_Controller->addAction($oControllerCopy);
}

// Действие "Перенести"
$oAdminFormActionMove = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('move');

if ($oAdminFormActionMove && $oAdmin_Form_Controller->getAction() == 'move')
{
	$Admin_Form_Action_Controller_Type_Move = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Move', $oAdminFormActionMove
	);

	$aExclude = array();

	$aChecked = $oAdmin_Form_Controller->getChecked();

	foreach ($aChecked as $datasetKey => $checkedItems)
	{
		// Exclude just dirs
		if ($datasetKey == 0)
		{
			foreach ($checkedItems as $key => $value)
			{
				$aExclude[] = $key;
			}
		}
	}

	$Admin_Form_Action_Controller_Type_Move
		->title(Core::_('Informationsystem_Item.move_items_groups_title'))
		->selectCaption(Core::_('Informationsystem_Item.move_items_groups_information_groups_id'))
		// Список директорий генерируется другим контроллером
		->selectOptions(array(' … ') + Shop_Item_Controller_Edit::fillShopGroup($oShop->id, 0, $aExclude))
		->value($oShopGroup->id);

	// Добавляем типовой контроллер редактирования контроллеру формы
	$oAdmin_Form_Controller->addAction($Admin_Form_Action_Controller_Type_Move);
}

// Действие "Удаление значения свойства"
$oAction = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('deletePropertyValue');

if ($oAction && $oAdmin_Form_Controller->getAction() == 'deletePropertyValue')
{
	$oDeletePropertyValueController = Admin_Form_Action_Controller::factory(
		'Property_Controller_Delete_Value', $oAction
	);

	$oDeletePropertyValueController
		->linkedObject(array(
				Core_Entity::factory('Shop_Group_Property_List', $oShop->id),
				Core_Entity::factory('Shop_Item_Property_List', $oShop->id)
			));

	$oAdmin_Form_Controller->addAction($oDeletePropertyValueController);
}

// Действие "Удаление файла большого изображения"
$oAction = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('deleteLargeImage');

if ($oAction && $oAdmin_Form_Controller->getAction() == 'deleteLargeImage')
{
	$oDeleteLargeImageController = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Delete_File', $oAction
	);

	$oDeleteLargeImageController
		->methodName('deleteLargeImage')
		->divId('control_large_image');

	// Добавляем контроллер удаления изображения к контроллеру формы
	$oAdmin_Form_Controller->addAction($oDeleteLargeImageController);
}

// Действие "Удаление файла малого изображения"
$oAction = Core_Entity::factory('Admin_Form', $iAdmin_Form_Id)
	->Admin_Form_Actions
	->getByName('deleteSmallImage');

if ($oAction && $oAdmin_Form_Controller->getAction() == 'deleteSmallImage')
{
	$oDeleteSmallImageController = Admin_Form_Action_Controller::factory(
		'Admin_Form_Action_Controller_Type_Delete_File', $oAction
	);

	$oDeleteSmallImageController
		->methodName('deleteSmallImage')
		->divId('control_small_image');

	$oAdmin_Form_Controller->addAction($oDeleteSmallImageController);
}

// Источник данных 0
$oDataset = new Admin_Form_Dataset_Entity(Core_Entity::factory('Shop_Group'));

$oDataset
	->addCondition(
		array(
				'select' => array('*', array(Core_QueryBuilder::expression("''"), 'adminPrice')
			)
		)
	)
	->addCondition(array('where' => array('parent_id', '=', $oShopGroup->id)))
	->addCondition(array('where' => array('shop_id', '=', $oShop->id)))
	->changeField('related', 'type', 1)
	->changeField('modifications', 'type', 1)
	->changeField('discounts', 'type', 1)
	->changeField('type', 'type', 1)
	->changeField('reviews', 'type', 1)
	->changeField('adminPrice', 'type', 1)
	;

$oAdmin_Form_Controller->addDataset($oDataset);

// Источник данных 1
$oDataset = new Admin_Form_Dataset_Entity(Core_Entity::factory('Shop_Item'));

$oDataset
	->addCondition(
		array(
				'select' => array('*', array('price', 'adminPrice')
			)
		)
	)
	->addCondition(array('where' => array('shop_group_id', '=', $oShopGroup->id)))
	->addCondition(array('where' => array('shop_id', '=', $oShop->id)))
	->addCondition(array('where' => array('modification_id', '=', 0)))
;

// Change field type
if(Core_Entity::factory('Shop', $oShop->id)->Shop_Warehouses->getCount() == 1)
{
	$oDataset->changeField('adminRest', 'type', 2);
}

$oAdmin_Form_Controller->addDataset($oDataset);

$oAdmin_Form_Controller->addExternalReplace('{shop_group_id}', $oShopGroup->id);

// Показ формы
$oAdmin_Form_Controller->execute();