<?php
/**
* Online shop.
*
* @package HostCMS
* @version 6.x
* @author Hostmake LLC
* @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
*/
require_once('../../../../bootstrap.php');
Core_Auth::authorization('shop');

if(!is_null(Core_Array::getPost('start')))
{
	$oShop_Item_Card = new Shop_Item_Card();

	?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html>
	<head>
	<title><?php echo Core::_('Shop_Item.item_cards_print')?></title>
	<meta http-equiv="Content-Language" content="ru">
	<meta content="text/html; charset=UTF-8" http-equiv=Content-Type>
	<?php
	$oShop_Item_Card->height = Core_Array::getPost('height', 70);
	$oShop_Item_Card->width = Core_Array::getPost('width', 50);
	$oShop_Item_Card->font = Core_Array::getPost('font', 10);
	$oShop_Item_Card->showcss();
	?>
	</head>
	<body>
	<?php 
	$iParentGroupId = Core_Array::getPost('parent_group', 0);
	
	if ($iParentGroupId == 0)
	{
		$oShop_Groups = Core_Entity::factory('Shop', Core_Array::getPost('shop_id', 0))->Shop_Groups;
		$oShop_Groups->queryBuilder()->where('parent_id', '=', 0);
	}
	else
	{
		$oShop_Groups = Core_Entity::factory('Shop_Group', $iParentGroupId)->Shop_Groups;
	}

	$aShopGroupsId = array_merge(array($iParentGroupId), $oShop_Groups->getGroupChildrenId());
	
	$oShop_Item_Card->fio = Core_Array::getPost('fio', '');
	$oShop_Item_Card->date = Core_Array::getPost('date', '');
	
	foreach($aShopGroupsId as $iShopGroupId)
	{
		$oShopGroup = Core_Entity::factory('Shop_Group', $iShopGroupId);
		$aShopItems = $oShopGroup->Shop_Items->findAll(FALSE);
		foreach($aShopItems as $oShopItem)
		{
			$oShop_Item_Card->build($oShopItem);
		}
	}	
	?>
	</body>
	</html>
	<?php
}
else
{
	$oAdmin_Form_Controller = Admin_Form_Controller::create();
	$oAdmin_Form_Entity_Breadcrumbs = Admin_Form_Entity::factory('Breadcrumbs');

	// Контроллер формы
	$oAdmin_Form_Controller
		->setUp()
		->path('/admin/shop/item/card/index.php')
	;

	// Получаем параметры
	$oShop = Core_Entity::factory('Shop', Core_Array::getRequest('shop_id', 0));
	$oShopDir = $oShop->Shop_Dir;
	$oShopGroup = Core_Entity::factory('Shop_Group', Core_Array::getRequest('shop_group_id', 0));

	// Первая крошка на список магазинов
	$oAdmin_Form_Entity_Breadcrumbs->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('Shop.menu'))
		->href($oAdmin_Form_Controller->getAdminLoadHref(
			'/admin/shop/index.php'
		))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
						'/admin/shop/index.php'
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
			$oAdmin_Form_Entity_Breadcrumbs->add($oBreadcrumb);
		}
	}

	// Крошка на список товаров и групп товаров магазина
	$oAdmin_Form_Entity_Breadcrumbs->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name($oShop->name)
		->href($oAdmin_Form_Controller->getAdminLoadHref(
			'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}"
		))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
			'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}"
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
			->href($oAdmin_Form_Controller->getAdminLoadHref(
					'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroupBreadcrumbs->id}"
			))
			->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
					'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroupBreadcrumbs->id}"
			));
		}while($oShopGroupBreadcrumbs = $oShopGroupBreadcrumbs->getParent());

		$aBreadcrumbs = array_reverse($aBreadcrumbs);

		foreach ($aBreadcrumbs as $oBreadcrumb)
		{
			$oAdmin_Form_Entity_Breadcrumbs->add($oBreadcrumb);
		}
	}

	// Крошка на текущую форму
	$oAdmin_Form_Entity_Breadcrumbs->add(
	Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('Shop_Item.item_cards_print'))
		->href($oAdmin_Form_Controller->getAdminLoadHref(
		$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
		))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
		$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
		))
	);

	ob_start();
	// Заголовок
	Admin_Form_Entity::factory('Title')
		->name(Core::_('Shop_Item.item_cards_print'))
		->execute();
	// Создаем экземпляры классов
	$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);
	$oAdmin_Form_Entity_Form
		->add($oAdmin_Form_Entity_Breadcrumbs)
		->add(Admin_Form_Entity::factory('Select')
			->name("parent_group")
			->options(array(' … ') + Shop_Item_Controller_Edit::fillShopGroup($oShop->id))
			->divAttr(array('style' => 'width: 400px;'))
			->caption(Core::_('Shop_Item.item_cards_print_parent_group'))
			->value($oShopGroup->id))
		->add(Admin_Form_Entity::factory('Input')
			->name('fio')
			->divAttr(array('style' => 'width: 400px'))
			->caption(Core::_('Shop_Item.item_cards_print_fio'))
			->value($oShop->Shop_Company->accountant_legal_name))
		->add(Admin_Form_Entity::factory('Input')
			->name('font')
			->divAttr(array('style' => 'width: 100px'))
			->caption(Core::_('Shop_Item.item_cards_print_font'))
			->value(10))
		->add(Admin_Form_Entity::factory('Date')
			->name('date')
			->caption(Core::_('Shop_Item.item_cards_print_date'))
			->value(date('d.m.Y')))
		->add(Admin_Form_Entity::factory('Input')
			->name('height')
			->divAttr(array('style' => 'float: left'))
			->style('width: 100px')
			->caption(Core::_('Shop_Item.item_cards_print_height'))
			//->value(74))
			->value(70))
		->add(Admin_Form_Entity::factory('Input')
			->name('width')
			//->divAttr(array('style' => 'float: left'))
			->style('width: 100px')
			->caption(Core::_('Shop_Item.item_cards_print_width'))
			//->value(70))
			->value(50))
		->add(Admin_Form_Entity::factory('Button')
			->name('start')
			->type('submit')
			->class('applyButton'))
		->add(Admin_Form_Entity::factory('Input')
			->name('shop_id')
			->type('hidden')
			->value($oShop->id))
	;

	$oAdmin_Form_Entity_Form->execute();
	$oAdmin_Answer = Core_Skin::instance()->answer();
	$oAdmin_Answer
		->ajax(Core_Array::getRequest('_', FALSE))
		->content(ob_get_clean())
		->title(Core::_('Shop_Item.item_cards_print'))
		->execute();
}

