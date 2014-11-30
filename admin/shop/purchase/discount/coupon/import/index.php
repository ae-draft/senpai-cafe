<?php
require_once('../../../../../../bootstrap.php');
Core_Auth::authorization('shop');

$oShop = Core_Entity::factory('Shop', Core_Array::getRequest('shop_id', 0));
$oShopDir = $oShop->Shop_Dir;
$shop_group_id = Core_Array::getRequest('shop_group_id', 0);
$oShopGroup = Core_Entity::factory('Shop_Group', $shop_group_id);
$oAdmin_Form_Controller = Admin_Form_Controller::create();
$oAdmin_Form_Entity_Breadcrumbs = Admin_Form_Entity::factory('Breadcrumbs');

// Контроллер формы
$oAdmin_Form_Controller
	->setUp()
	->path('/admin/shop/purchase/discount/coupon/import/index.php');

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

// Крошка на предыдущую форму
$oAdmin_Form_Entity_Breadcrumbs->add(
Admin_Form_Entity::factory('Breadcrumb')
	->name(Core::_('Shop_Purchase_Discount_Coupon.list_of_coupons'))
	->href($oAdmin_Form_Controller->getAdminLoadHref(
	"/admin/shop/purchase/discount/coupon/index.php", NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
	))
	->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
	"/admin/shop/purchase/discount/coupon/index.php", NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
	))
);

// Крошка на текущую форму
$oAdmin_Form_Entity_Breadcrumbs->add(
Admin_Form_Entity::factory('Breadcrumb')
	->name(Core::_('Shop_Purchase_Discount_Coupon.import'))
	->href($oAdmin_Form_Controller->getAdminLoadHref(
	$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
	))
	->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
	$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
	))
);

ob_start();

$oAdmin_Form_Entity_Title = Admin_Form_Entity::factory('Title');
$oAdmin_Form_Entity_Title->name = Core::_('Shop_Purchase_Discount_Coupon.import');
$oAdmin_Form_Entity_Title->execute();
$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);
$oAdmin_Form_Entity_Form->action($oAdmin_Form_Controller->getPath());
$oAdmin_Form_Entity_Form->enctype('multipart/form-data');
$oAdmin_Form_Entity_Form->add($oAdmin_Form_Entity_Breadcrumbs);

if($oAdmin_Form_Controller->getAction() != 'start_import')
{
	$oAdmin_Form_Entity_Form->add(Admin_Form_Entity::factory('File')
		->name("csv_file")
		->caption(Core::_('Shop_Purchase_Discount_Coupon.import_form'))
		->largeImage(array('show_params' => FALSE))
		->smallImage(array('show' => FALSE))
	)->add(Admin_Form_Entity::factory('Button')
		->name('start_import')
		->type('submit')
		->class('applyButton')
		->onclick($oAdmin_Form_Controller->getAdminSendForm('start_import'))
	);
}
else
{
	if($_FILES['csv_file']['size'] === 0)
	{
		// Файл не указан!
		Core_Message::show(Core::_('Shop_Item.file_does_not_specified'), "error");
	}
	else
	{
		$iCounter = 0;
		$sFileName = $_FILES['csv_file']['tmp_name'];
		$sTmpFileName = CMS_FOLDER . TMP_DIR . 'file_'.date("U").'.csv';
		try
		{
			Core_File::upload($sFileName, $sTmpFileName);

			$fInputFile = fopen($sTmpFileName, 'rb');

			while(!feof($fInputFile))
			{
				$aCurrentCSVLine = fgetcsv($fInputFile, 10000, ';', '"');

				// Если пустая строка - пропускаем
				if (!is_array($aCurrentCSVLine) || (count($aCurrentCSVLine) == 1 && empty ($aCurrentCSVLine[0])))
				{
					continue;
				}

				if($aCurrentCSVLine)
				{
					$oShop_Purchase_Discount_Coupon = Core_Entity::factory('Shop_Purchase_Discount_Coupon');

					foreach($aCurrentCSVLine as $sCode => $sData)
					{
						switch($sCode)
						{
						case 0:
							// Название купона
							// Текст купона
							if(trim(strval($sData)) != '')
							{
								$oShop_Purchase_Discount_Coupon->name = $sData;
							}
							break;
						case 1:
							// Скидка, если её магазин не равен текущему - пропускаем импорт
							if(trim(intval($sData)) != 0)
							{
								$oShop_Purchase_Discount_Coupon->shop_purchase_discount_id = $sData;
								
								if(intval(Core_Entity::factory('Shop_Purchase_Discount', $oShop_Purchase_Discount_Coupon->shop_purchase_discount_id)->Shop->id) != $oShop->id)
								{
									continue 3;
								}
							}
							break;
						case 2:
							// Активность
							if(trim(intval($sData)) != 0)
							{
								$oShop_Purchase_Discount_Coupon->active = $sData;
							}
							break;
						case 3:
							// Количество
							if(trim(intval($sData)) != 0)
							{
								$oShop_Purchase_Discount_Coupon->count = $sData;
							}
							break;
						case 4:
							// Текст купона
							if(trim(strval($sData)) != '')
							{
								$oShop_Purchase_Discount_Coupon->text = $sData;
							}
							break;
						case 5:
							// Идентификатор купона (для обновления)
							if(trim(intval($sData)) != 0)
							{
								$oShop_Purchase_Discount_Coupon->id = $sData;
							}
							break;
						}
					}

					$oShop_Purchase_Discounts = $oShop->Shop_Purchase_Discounts;
					$oShop_Purchase_Discounts
						->queryBuilder()
						->join('shop_purchase_discount_coupons', 'shop_purchase_discounts.id', '=', 'shop_purchase_discount_coupons.shop_purchase_discount_id')
						->where('shop_purchase_discount_coupons.text', '=', $oShop_Purchase_Discount_Coupon->text)
						->where('shop_purchase_discount_coupons.deleted', '=', 0)
						->limit(1);

					if(intval($oShop_Purchase_Discounts->getCount()) === 0)
					{
						$oShop_Purchase_Discount_Coupon->save() && $iCounter++;
					}
				}
			}

			fclose($fInputFile);

			try
			{
				Core_File::delete($sTmpFileName);
			}
			catch(Exception $e)
			{
				Core_Message::show(Core::_('Shop_Purchase_Discount_Coupon.import_error', $e->getMessage()), "error");
			}

			Core_Message::show(Core::_('Shop_Purchase_Discount_Coupon.import_result', $iCounter));
		}
		catch (Exception $exc){Core_Message::show($exc->getMessage(), "error");}
	}
}

$oAdmin_Form_Entity_Form->execute();
$oAdmin_Answer = Core_Skin::instance()->answer();
$oAdmin_Answer
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(
		iconv("UTF-8", "UTF-8//IGNORE//TRANSLIT", ob_get_clean())
	)
	->title(Core::_('Shop_Purchase_Discount_Coupon.import'))
	->execute();