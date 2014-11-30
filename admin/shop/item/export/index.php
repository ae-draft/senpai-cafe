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

// Получаем параметры
$oShop = Core_Entity::factory('Shop', Core_Array::getRequest('shop_id', 0));
$oShopDir = $oShop->Shop_Dir;
$oShopGroup = Core_Entity::factory('Shop_Group', Core_Array::getRequest('shop_group_id', 0));

if(Core_Array::getPost('action') == 'export')
{
	switch(Core_Array::getPost('export_type'))
	{
		case 0:
			$aSeparator = array(",", ";");
			$iSeparator = Core_Array::getPost('export_price_separator', 0);
			$oShop_Item_Export_Csv_Controller = new Shop_Item_Export_Csv_Controller(Core_Array::getPost('shop_id', 0), !is_null(Core_Array::getPost('export_external_properties_allow_items')), !is_null(Core_Array::getPost('export_external_properties_allow_groups')), !is_null(Core_Array::getPost('export_modifications_allow')));
			$oShop_Item_Export_Csv_Controller
				->separator($iSeparator > 1 ? "" : $aSeparator[$iSeparator])
				->encoding(Core_Array::getPost('import_price_encoding', "UTF-8"))
				->parentGroup(Core_Array::getPost('shop_groups_parent_id', 0))
				->execute();
		break;
		case 1:
		
			$aSeparator = array(",", ";");
			$iSeparator = Core_Array::getPost('export_price_separator', 0);
		
			$oShop_Item_Export_Csv_Controller = new Shop_Item_Export_Csv_Controller(Core_Array::getPost('shop_id', 0), FALSE, FALSE, FALSE, TRUE);
			$oShop_Item_Export_Csv_Controller
				->separator($iSeparator > 1 ? "" : $aSeparator[$iSeparator])
				->encoding(Core_Array::getPost('import_price_encoding', "UTF-8"))
				->execute();
		break;
		case 2:

			$oShop_Item_Export_Cml_Controller = new Shop_Item_Export_Cml_Controller(Core_Entity::factory('Shop', Core_Array::getPost('shop_id', 0)));
			$oShop_Item_Export_Cml_Controller->group = Core_Entity::factory('Shop_Group', $oShopGroup->id);
			$oShop_Item_Export_Cml_Controller->exportItemModifications = !is_null(Core_Array::getPost('export_modifications_allow'));
			$oShop_Item_Export_Cml_Controller->exportItemExternalProperties = !is_null(Core_Array::getPost('export_external_properties_allow_items'));

			header("Pragma: public");
			header("Content-Description: File Transfer");
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename = " . 'import_' .date("Y_m_d_H_i_s").'.xml'. ";");
			header("Content-Transfer-Encoding: binary");

			echo $oShop_Item_Export_Cml_Controller->exportImport();

			exit();
		break;
		case 3:
			$oShop_Item_Export_Cml_Controller = new Shop_Item_Export_Cml_Controller(Core_Entity::factory('Shop', Core_Array::getPost('shop_id', 0)));
			$oShop_Item_Export_Cml_Controller->group = Core_Entity::factory('Shop_Group', $oShopGroup->id);
			$oShop_Item_Export_Cml_Controller->exportItemModifications = !is_null(Core_Array::getPost('export_modifications_allow'));
			$oShop_Item_Export_Cml_Controller->exportItemExternalProperties = !is_null(Core_Array::getPost('export_external_properties_allow_items'));

			header("Pragma: public");
			header("Content-Description: File Transfer");
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename = " . 'offers_' .date("Y_m_d_H_i_s").'.xml'. ";");
			header("Content-Transfer-Encoding: binary");

			echo $oShop_Item_Export_Cml_Controller->exportOffers();

			exit();
		break;
	}
}

// Создаем экземпляры классов
$oAdmin_Form_Controller = Admin_Form_Controller::create();

// Контроллер формы
$oAdmin_Form_Controller
	->setUp()
	->path('/admin/shop/item/export/index.php')
;

$oAdmin_Form_Entity_Breadcrumbs = Admin_Form_Entity::factory('Breadcrumbs');

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
	} while($oShopGroupBreadcrumbs = $oShopGroupBreadcrumbs->getParent());

	$aBreadcrumbs = array_reverse($aBreadcrumbs);

	foreach ($aBreadcrumbs as $oBreadcrumb)
	{
		$oAdmin_Form_Entity_Breadcrumbs->add($oBreadcrumb);
	}
}

// Крошка на текущую форму
$oAdmin_Form_Entity_Breadcrumbs->add(
Admin_Form_Entity::factory('Breadcrumb')
	->name(Core::_('Shop_Item.export_shop'))
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
	->name(Core::_('Shop_Item.export_shop'))
	->execute();

$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);
$oAdmin_Form_Entity_Form
	->action($oAdmin_Form_Controller->getPath())
	->target('_blank');
$oAdmin_Form_Entity_Form->add($oAdmin_Form_Entity_Breadcrumbs);
$windowId = $oAdmin_Form_Controller->getWindowId();

$oAdmin_Form_Entity_Form
	->add(
		Admin_Form_Entity::factory('Radiogroup')
			->radio(array(
				Core::_('Shop_Item.import_price_list_file_type1_items'),
				Core::_('Shop_Item.import_price_list_file_type1_orders'),
				Core::_('Shop_Item.export_price_list_file_type3_import'),
				Core::_('Shop_Item.export_price_list_file_type3_offers')
			))
			->caption(Core::_('Shop_Item.export_file_type'))
			->divAttr(array('id' => 'export_types'))
			->name('export_type')
			->onchange("ShowImport('{$windowId}', $(this).val())")
	)
	->add(Admin_Form_Entity::factory('Code')
		->html("<script>$(function() {
			$('#{$windowId} #export_types').buttonset();
		});</script>")
	)
	->add(
		Admin_Form_Entity::factory('Radiogroup')
			->radio(array(
				Core::_('Shop_Item.import_price_list_separator1'),
				Core::_('Shop_Item.import_price_list_separator2')
			))
			->name('export_price_separator')
			->divAttr(array('id' => 'import_price_list_separator'))
			->caption(Core::_('Shop_Item.import_price_list_separator'))
	)
	->add(Admin_Form_Entity::factory('Code')
		->html("<script>$(function() {
			$('#{$windowId} #import_price_list_separator').buttonset();
		});</script>")
	)
	->add(Admin_Form_Entity::factory('Select')
		->name("import_price_encoding")
		->options(array(
			'Windows-1251' => Core::_('Shop_Item.input_file_encoding0'),
			'UTF-8' => Core::_('Shop_Item.input_file_encoding1')
		))
		->divAttr(array('style' => 'width: 400px;', 'id' => 'import_price_encoding'))
		->caption(Core::_('Shop_Item.price_list_encoding')))
	->add(Admin_Form_Entity::factory('Select')
		->name("shop_groups_parent_id")
		->options(array(' … ') + Shop_Item_Controller_Edit::fillShopGroup($oShop->id))
		->divAttr(array('style' => 'width: 400px;', 'id' => 'import_price_encoding'))
		->caption(Core::_('Shop_Item.import_price_list_parent_group'))
		->value($oShopGroup->id)
	)
	->add(Admin_Form_Entity::factory('Checkbox')
		->name("export_external_properties_allow_items")
		->caption(Core::_('Shop_Item.export_external_properties_allow_items'))
		->value(TRUE))
	->add(Admin_Form_Entity::factory('Checkbox')
		->name("export_external_properties_allow_groups")
		->caption(Core::_('Shop_Item.export_external_properties_allow_groups'))
		->divAttr(array('id' => 'export_external_properties_allow_groups'))
		->value(TRUE))
	->add(Admin_Form_Entity::factory('Checkbox')
		->name("export_modifications_allow")
		->caption(Core::_('Shop_Item.export_modifications_allow'))
		->value(TRUE))
	->add(Admin_Form_Entity::factory('Input')
		->name('action')
		->type('hidden')
		->value('export'))
	->add(Admin_Form_Entity::factory('Input')
		->name('shop_group_id')
		->type('hidden')
		->value(Core_Array::getGet('shop_group_id')))
	->add(Admin_Form_Entity::factory('Input')
		->name('shop_id')
		->type('hidden')
		->value(Core_Array::getGet('shop_id', 0)))
	->add(
		Admin_Form_Entity::factory('Button')
		->name('show_form')
		->type('submit')
		->class('applyButton')
		//->onclick($oAdmin_Form_Controller->getAdminSendForm('export'))
		)
;

$oAdmin_Form_Entity_Form->execute();

$oAdmin_Answer = Core_Skin::instance()->answer();

$oAdmin_Answer
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(ob_get_clean())
	//->message()
	->title(Core::_('Shop_Item.export_shop'))
	->execute();