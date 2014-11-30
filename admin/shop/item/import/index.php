<?php
require_once('../../../../bootstrap.php');

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
	->path('/admin/shop/item/import/index.php')
;

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
	->name(Core::_('Shop_Item.import_price_list_link'))
	->href($oAdmin_Form_Controller->getAdminLoadHref(
	$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
	))
	->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
	$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"
	))
);

// Формируем массивы данных
$aLangConstNames = array(
	Core::_('Shop_Exchange.!download'),
	Core::_('Shop_Exchange.group_id'),
	Core::_('Shop_Exchange.group_name'),
	Core::_('Shop_Exchange.group_path'),
	Core::_('Shop_Exchange.group_sorting'),
	Core::_('Shop_Exchange.group_description'),
	Core::_('Shop_Exchange.group_seo_title'),
	Core::_('Shop_Exchange.group_seo_description'),
	Core::_('Shop_Exchange.group_seo_keywords'),
	Core::_('Shop_Exchange.group_image_large'),
	Core::_('Shop_Exchange.group_image_small'),
	Core::_('Shop_Exchange.group_guid'),
	Core::_('Shop_Exchange.parent_group_guid'),
	Core::_('Shop_Exchange.currency_id'),
	Core::_('Shop_Exchange.tax_id'),
	Core::_('Shop_Exchange.producer_id'),
	Core::_('Shop_Exchange.producer_name'),
	Core::_('Shop_Exchange.seller_id'),
	Core::_('Shop_Exchange.seller_name'),
	Core::_('Shop_Exchange.measure_id'),
	Core::_('Shop_Exchange.measure_value'),
	Core::_('Shop_Exchange.item_id'),
	Core::_('Shop_Exchange.item_name'),
	Core::_('Shop_Exchange.item_marking'),
	Core::_('Shop_Exchange.item_datetime'),
	Core::_('Shop_Exchange.item_description'),
	Core::_('Shop_Exchange.item_text'),
	Core::_('Shop_Exchange.item_image_large'),
	Core::_('Shop_Exchange.item_image_small'),
	Core::_('Shop_Exchange.item_tags'),
	Core::_('Shop_Exchange.item_weight'),
	Core::_('Shop_Exchange.item_price'),
	Core::_('Shop_Exchange.item_active'),
	Core::_('Shop_Exchange.item_sorting'),
	Core::_('Shop_Exchange.item_path'),
	Core::_('Shop_Exchange.item_seo_title'),
	Core::_('Shop_Exchange.item_seo_description'),
	Core::_('Shop_Exchange.item_seo_keywords'),
	Core::_('Shop_Exchange.item_indexing'),
	Core::_('Shop_Exchange.item_yandex_market'),
	Core::_('Shop_Exchange.item_yandex_market_bid'),
	Core::_('Shop_Exchange.item_yandex_market_cid'),
	Core::_('Shop_Exchange.item_parent_mark'),
	Core::_('Shop_Exchange.digital_item_value'),
	Core::_('Shop_Exchange.digital_item_filename'),
	Core::_('Shop_Exchange.digital_item_count'),
	Core::_('Shop_Exchange.item_end_datetime'),
	Core::_('Shop_Exchange.item_start_datetime'),
	Core::_('Shop_Exchange.item_type'),
	Core::_('Shop_Exchange.item_additional_group'),
	Core::_('Shop_Exchange.specialprices_min_quantity'),
	Core::_('Shop_Exchange.specialprices_max_quantity'),
	Core::_('Shop_Exchange.specialprices_price'),
	Core::_('Shop_Exchange.specialprices_percent'),
	Core::_('Shop_Exchange.item_guid'),
	Core::_('Shop_Exchange.group_active'),
	Core::_('Shop_Exchange.item_parent_sop'),
	Core::_('Shop_Exchange.siteuser_id'),
	Core::_('Shop_Exchange.digital_item_name'),
	Core::_('Shop_Exchange.item_yandex_market_sales_notes'),
	Core::_('Shop_Exchange.order_guid'),
	Core::_('Shop_Exchange.order_number'),
	Core::_('Shop_Exchange.order_country'),
	Core::_('Shop_Exchange.order_location'),
	Core::_('Shop_Exchange.order_city'),
	Core::_('Shop_Exchange.order_city_area'),
	Core::_('Shop_Exchange.order_name'),
	Core::_('Shop_Exchange.order_surname'),
	Core::_('Shop_Exchange.order_patronymic'),
	Core::_('Shop_Exchange.order_email'),
	Core::_('Shop_Exchange.order_akt'),
	Core::_('Shop_Exchange.order_schet_fak'),
	Core::_('Shop_Exchange.order_company_name'),
	Core::_('Shop_Exchange.order_inn'),
	Core::_('Shop_Exchange.order_kpp'),
	Core::_('Shop_Exchange.order_phone'),
	Core::_('Shop_Exchange.order_fax'),
	Core::_('Shop_Exchange.order_address'),
	Core::_('Shop_Exchange.order_order_status'),
	Core::_('Shop_Exchange.order_currency'),
	Core::_('Shop_Exchange.order_payment_system_id'),
	Core::_('Shop_Exchange.order_date'),
	Core::_('Shop_Exchange.order_pay_status'),
	Core::_('Shop_Exchange.order_pay_date'),
	Core::_('Shop_Exchange.order_description'),
	Core::_('Shop_Exchange.order_info'),
	Core::_('Shop_Exchange.order_canceled'),
	Core::_('Shop_Exchange.order_pay_status_change_date'),
	Core::_('Shop_Exchange.order_delivery_info'),

	Core::_('Shop_Exchange.order_item_marking'),
	Core::_('Shop_Exchange.order_item_name'),
	Core::_('Shop_Exchange.order_item_quantity'),
	Core::_('Shop_Exchange.order_item_price'),
	Core::_('Shop_Exchange.order_item_rate'),
	Core::_('Shop_Exchange.order_item_type')
);

$aColors = array(
	'#999999',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#E7A1B0',
	'#C8B560',
	'#E77471',
	'#E78A61',
	'#E78A61',
	'#ADA96E',
	'#ADA96E',
	'#AFC7C7',
	'#AFC7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#92C7C7',
	'#E18B6B',
	'#E18B6B',
	'#E18B6B',
	'#E18B6B',
	'#E18B6B',
	'#E18B6B',
	'#E18B6B',
	'#C48189',
	'#C48189',
	'#C48189',
	'#C8B560',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',

	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF',
	'#C6E2FF'
);

$aEntities = array(
	'',
	'shop_groups_id',
	'shop_groups_value',
	'shop_groups_path',
	'shop_groups_order',
	'shop_groups_description',
	'shop_groups_seo_title',
	'shop_groups_seo_description',
	'shop_groups_seo_keywords',
	'shop_groups_image',
	'shop_groups_small_image',
	'shop_groups_cml_id',
	'shop_shop_groups_parent_cml_id',
	'shop_currency_id',
	'shop_tax_id',
	'shop_producers_list_id',
	'shop_producers_list_value',
	'shop_shop_sallers_id',
	'shop_sallers_name',
	'shop_mesures_id',
	'shop_mesures_value',
	'shop_items_catalog_item_id',
	'shop_items_catalog_name',
	'shop_items_catalog_marking',
	'shop_shop_items_catalog_date_time',
	'shop_items_catalog_description',
	'shop_items_catalog_text',
	'shop_items_catalog_image',
	'shop_items_catalog_small_image',
	'shop_items_catalog_label',
	'shop_items_catalog_weight',
	'shop_items_catalog_price',
	'shop_items_catalog_is_active',
	'shop_items_catalog_order',
	'shop_items_catalog_path',
	'shop_items_catalog_seo_title',
	'shop_items_catalog_seo_description',
	'shop_items_catalog_seo_keywords',
	'shop_items_catalog_indexation',
	'shop_shop_items_catalog_yandex_market_allow',
	'shop_shop_items_catalog_yandex_market_bid',
	'shop_shop_items_catalog_yandex_market_cid',
	'shop_item_parent_mark',
	'shop_eitems_text',
	'shop_eitems_file',
	'shop_eitem_count',
	'shop_shop_items_catalog_putend_date',
	'shop_shop_items_catalog_putoff_date',
	'shop_shop_items_catalog_type',
	'additional_group',
	'shop_special_prices_from',
	'shop_special_prices_to',
	'shop_special_prices_price',
	'shop_special_prices_percent',
	'shop_items_cml_id',
	'shop_groups_activity',
	'shop_item_parent_soput',
	'site_users_id',
	'shop_eitem_name',
	'shop_items_catalog_yandex_market_sales_notes',
	'order_guid',
	'order_invoice',
	'order_shop_country_id',
	'order_shop_country_location_id',
	'order_shop_country_location_city_id',
	'order_shop_country_location_city_area_id',
	'order_name',
	'order_surname',
	'order_patronymic',
	'order_email',
	'order_acceptance_report',
	'order_vat_invoice',
	'order_company',
	'order_tin',
	'order_kpp',
	'order_phone',
	'order_fax',
	'order_address',
	'order_shop_order_status_id',
	'order_shop_currency_id',
	'order_shop_payment_system_id',
	'order_datetime',
	'order_paid',
	'order_payment_datetime',
	'order_description',
	'order_system_information',
	'order_canceled',
	'order_status_datetime',
	'order_delivery_information',

	'order_item_marking',
	'order_item_name',
	'order_item_quantity',
	'order_item_price',
	'order_item_rate',
	'order_item_type'
);

$aGroupProperties = Core_Entity::factory('Shop_Group_Property_List', $oShop->id)->Properties->findAll();
foreach ($aGroupProperties as $oGroupProperty)
{
	$oPropertyDir = $oGroupProperty->Property_Dir;

	$aLangConstNames[] = $oGroupProperty->name . "&nbsp;[" . ($oPropertyDir->id ? $oPropertyDir->name : Core::_('Shop_item.root_folder')) . "]";
	$aColors[] = "#E6E6FA";
	$aEntities[] = 'prop_group-' . $oGroupProperty->id;

	if($oGroupProperty->type == 2)
	{
		$aLangConstNames[] = Core::_('Shop_Item.import_small_images') . $oGroupProperty->name . " [" . ($oPropertyDir->id ? $oPropertyDir->name : Core::_('Shop_item.root_folder')) . "]";
		$aColors[] = "#E6E6FA";
		$aEntities[] = 'propsmall-' . $oGroupProperty->id;
	}
}

$aItemProperties = Core_Entity::factory('Shop_Item_Property_List', $oShop->id)->Properties->findAll();
foreach ($aItemProperties as $oItemProperty)
{
	$oPropertyDir = $oItemProperty->Property_Dir;

	$aLangConstNames[] = $oItemProperty->name . " [" . ($oPropertyDir->id ? $oPropertyDir->name : Core::_('Shop_item.root_folder')) . "]";
	$aColors[] = "#FFE4E1";
	$aEntities[] = 'prop-' . $oItemProperty->id;

	if($oItemProperty->type == 2)
	{
		$aLangConstNames[] = Core::_('Shop_Item.import_small_images') . $oItemProperty->name . " [" . ($oPropertyDir->id ? $oPropertyDir->name : Core::_('Shop_item.root_folder')) . "]";
		$aColors[] = "#FFE4E1";
		$aEntities[] = 'propsmall-' . $oItemProperty->id;
	}
}

$aShopPrices = Core_Entity::factory('Shop', $oShop->id)->Shop_prices->findAll();
foreach ($aShopPrices as $oShopPrice)
{
	$aLangConstNames[] = $oShopPrice->name;
	$aColors[] = "#EEE8AA";
	$aEntities[] = 'price-' . $oShopPrice->id;
}

// Выводим склады
$aShopWarehouses = Core_Entity::factory('Shop', $oShop->id)->Shop_Warehouses->findAll();
foreach ($aShopWarehouses as $oShopWarehouse)
{
	$aLangConstNames[] = Core::_('Shop_Item.warehouse_import_field', $oShopWarehouse->name);
	$aColors[] = "#DEB887";
	$aEntities[] = 'warehouse-' . $oShopWarehouse->id;
}

$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();

ob_start();

$oAdmin_Form_Entity_Title = Admin_Form_Entity::factory('Title');
$oAdmin_Form_Entity_Title->name = Core::_('Shop_Item.import_price_list_link');
$oAdmin_Form_Entity_Title->execute();

$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);
$oAdmin_Form_Entity_Form->action($oAdmin_Form_Controller->getPath());
$oAdmin_Form_Entity_Form->enctype('multipart/form-data');
$oAdmin_Form_Entity_Form->add($oAdmin_Form_Entity_Breadcrumbs);

// Количество полей
$iFieldCount = 0;

$sOnClick = NULL;

if($oAdmin_Form_Controller->getAction() == 'show_form')
{
	if (!$oUserCurrent->read_only)
	{
		$sFileName = intval($_FILES['csv_file']['size']) > 0
			? $_FILES['csv_file']['tmp_name']
			: CMS_FOLDER . Core_Array::getPost('alternative_file_pointer');

		if(is_file($sFileName) && is_readable($sFileName))
		{
			if(Core_Array::getPost('import_price_type') == 0)
			{
				// Обработка CSV-файла
				$sTmpFileName = CMS_FOLDER . TMP_DIR . 'file_'.date("U").'.csv';

				try {
					Core_File::upload($sFileName, $sTmpFileName);

					if ($fInputFile = fopen($sTmpFileName, 'rb'))
					{
						$sSeparator = Core_Array::getPost('import_price_separator');

						switch ($sSeparator)
						{
							case 0:
									$sSeparator = ',';
							break;
							case 1:
							default:
								$sSeparator = ';';
							break;
							case 2:
								$sSeparator = "\t";
							break;
							case 3:
								$sSeparator = Core_Array::getPost('import_price_separator_text');
							break;
						}

						$sLimiter = Core_Array::getPost('import_price_stop');

						switch ($sLimiter)
						{
							case 0:
							default:
								$sLimiter = '"';
							break;
							case 1:
								$sLimiter = Core_Array::getPost('import_price_stop_text');
							break;
						}

						$sLocale = Core_Array::getPost('import_price_encoding');
						$oShop_Item_Import_Csv_Controller = new Shop_Item_Import_Csv_Controller($oShop->id, Core_Array::getPost('shop_groups_parent_id', 0));
						$oShop_Item_Import_Csv_Controller->encoding(
							$sLocale
						)->separator($sSeparator)->limiter($sLimiter);

						$aCsvLine = $oShop_Item_Import_Csv_Controller->getCSVLine($fInputFile);

						$iFieldCount = is_array($aCsvLine) ? count($aCsvLine) : 0;

						fclose($fInputFile);

						if($iFieldCount)
						{
							$iValuesCount = count($aLangConstNames);

							$pos = 0;

							ob_start();

							?>
							<table>
							<?php
							for($i = 0; $i < $iFieldCount; $i++)
							{
								?>
								<tr>
									<td><?php echo $aCsvLine[$i]?></td>
									<td>
									<select name="field<?php echo $i?>">
									<?php
									$isset_selected = FALSE;

									// Генерируем выпадающий список с цветными элементами
									for($j = 0; $j < $iValuesCount; $j++)
									{
										$aCsvLine[$i] = trim($aCsvLine[$i]);
										if (!$isset_selected
										&& ($aCsvLine[$i] == $aLangConstNames[$j]
										|| (strlen($aLangConstNames[$j]) > 0
										&& strlen($aCsvLine[$i]) > 0
										&&
										(strpos($aCsvLine[$i], $aLangConstNames[$j]) !== FALSE
										|| strpos($aLangConstNames[$j], $aCsvLine[$i]) !== FALSE)
										// Чтобы не было срабатывания "Город" -> "Городской телефон"
										// Если есть целиком подходящее поле
										&& !array_search($aCsvLine[$i], $aLangConstNames))
										))
										{
											$selected = " selected";

											// Для исключения двойного указания selected для одного списка
											$isset_selected = TRUE;
										}
										else
										{
											$selected = "";
										}

										?>
										<option style="padding: 2px; border-top: 1px solid #dddddd;<?php echo (!empty($aColors[$pos])) ? 'background-color: '.$aColors[$j].'; color: #000;' : ''?>" <?php echo $selected?> value="<?php echo $aEntities[$j]?>"><?php echo $aLangConstNames[$j]?></option><?php

										$pos++;
									}

									$pos = 0;
									?>
									</select>
									</td>
								</tr>
								<?php
							}

							?>
							</table>
							<!-- <input type="hidden" name="shop_group_id" value="<?php echo $oShopGroup->id?>" /> -->
							<input type="hidden" name="csv_filename" value="<?php echo $sTmpFileName?>" />
							<input type="hidden" name="import_price_separator" value="<?php echo $sSeparator?>" />
							<input type="hidden" name="import_price_stop" value='<?php echo $sLimiter?>' />
							<input type="hidden" name="firstlineheader" value="<?php echo isset($_POST['import_price_name_field_f']) ? 1 : 0?>" />
							<input type="hidden" name="locale" value="<?php echo $sLocale?>" />
							<input type="hidden" name="import_price_max_time" value="<?php echo Core_Array::getPost('import_price_max_time')?>" />
							<input type="hidden" name="import_price_max_count" value="<?php echo Core_Array::getPost('import_price_max_count')?>" />
							<input type="hidden" name="import_price_load_files_path" value="<?php echo Core_Array::getPost('import_price_load_files_path')?>" />
							<input type="hidden" name="import_price_action_items" value="<?php echo Core_Array::getPost('import_price_action_items')?>" />
							<input type="hidden" name="shop_groups_parent_id" value="<?php echo Core_Array::getPost('shop_groups_parent_id')?>" />
							<input type="hidden" name="search_event_indexation" value="<?php echo isset($_POST['search_event_indexation']) ? 1 : 0?>" />
							<input type="hidden" name="import_price_action_delete_image" value="<?php echo isset($_POST['import_price_action_delete_image']) ? 1 : 0?>" />
							<?php

							$oAdmin_Form_Entity_Form->add(
								Admin_Form_Entity::factory('Code')->html(
									ob_get_clean()
								)
							);
						}
						else
						{
							throw new Core_Exception("File is empty!");
						}
					}
					else
					{
						throw new Core_Exception("Can't open file");
					}

				} catch (Exception $exc) {
					Core_Message::show($exc->getMessage(), "error");
				}

				$sOnClick = $oAdmin_Form_Controller->getAdminSendForm('start_import');
			}
			else
			{
				// Обработка CommerceML-файла
				$sTmpFileName = CMS_FOLDER . TMP_DIR . 'file_'.date("U").'.cml';

				try {
					Core_File::upload($sFileName, $sTmpFileName);

					$oShop_Item_Import_Cml_Controller = new Shop_Item_Import_Cml_Controller($sTmpFileName);
					$oShop_Item_Import_Cml_Controller->iShopId = $oShop->id;
					$oShop_Item_Import_Cml_Controller->iShopGroupId = Core_Array::getPost('shop_groups_parent_id', 0);
					$oShop_Item_Import_Cml_Controller->sPicturesPath = Core_Array::getPost('import_price_load_files_path');
					$oShop_Item_Import_Cml_Controller->importAction = Core_Array::getPost('import_price_action_items');
					$fRoznPrice_name = defined('SHOP_DEFAULT_CML_CURRENCY_NAME')
						? SHOP_DEFAULT_CML_CURRENCY_NAME
						: 'Розничная';
					$oShop_Item_Import_Cml_Controller->sShopDefaultPriceName = $fRoznPrice_name;
					$aReturn = $oShop_Item_Import_Cml_Controller->import();

					Core_Message::show(Core::_('Shop_Item.msg_download_price_complete'));
					echo Core::_('Shop_Item.count_insert_item') . ' &#151; <b>' . $aReturn['insertItemCount'] . '</b><br/>';
					echo Core::_('Shop_Item.count_update_item') . ' &#151; <b>' . $aReturn['updateItemCount'] . '</b><br/>';
					echo Core::_('Shop_Item.create_catalog') . ' &#151; <b>' . $aReturn['insertDirCount'] . '</b><br/>';
					echo Core::_('Shop_Item.update_catalog') . ' &#151; <b>' . $aReturn['updateDirCount'] . '</b><br/>';
				} catch (Exception $exc) {
					Core_Message::show($exc->getMessage(), "error");
				}

				Core_File::delete($sTmpFileName);

				$sOnClick = "";
			}
		}
		else
		{
			Core_Message::show(Core::_('Shop_Item.file_does_not_specified'), "error");
			$sOnClick = "";
		}
	}
	else
	{
		Core_Message::show(Core::_('User.demo_mode'), "error");
	}
}
elseif($oAdmin_Form_Controller->getAction() == 'start_import')
{
	if (!$oUserCurrent->read_only)
	{
		Core_Session::start();

		if(isset($_SESSION['Shop_Item_Import_Csv_Controller']))
		{
			$Shop_Item_Import_Csv_Controller = $_SESSION['Shop_Item_Import_Csv_Controller'];
			unset($_SESSION['Shop_Item_Import_Csv_Controller']);

			$iNextSeekPosition = $Shop_Item_Import_Csv_Controller->seek;
		}
		else
		{
			$Shop_Item_Import_Csv_Controller = new Shop_Item_Import_Csv_Controller(Core_Array::getRequest('shop_id', 0), Core_Array::getRequest('shop_groups_parent_id', 0));

			$aConformity = array();

			foreach ($_POST as $iKey => $sValue)
			{
				if(mb_strpos($iKey, "field") === 0)
				{
					$aConformity[] = $sValue;
				}
			}

			$iNextSeekPosition = 0;

			$Shop_Item_Import_Csv_Controller
				->file(Core_Array::getPost('csv_filename'))
				->encoding(Core_Array::getPost('locale', 'UTF-8'))
				->csv_fields($aConformity)
				->time(Core_Array::getPost('import_price_max_time'))
				->step(Core_Array::getPost('import_price_max_count'))
				->separator(Core_Array::getPost('import_price_separator'))
				->limiter(Core_Array::getPost('import_price_stop'))
				->imagesPath(Core_Array::getPost('import_price_load_files_path'))
				->importAction(Core_Array::getPost('import_price_action_items'))
				->searchIndexation(Core_Array::getPost('search_event_indexation'))
				->deleteImage(Core_Array::getPost('import_price_action_delete_image'))
			;

			if(Core_Array::getPost('firstlineheader', 0))
			{
				$fInputFile = fopen(Core_Array::getPost('csv_filename'), 'rb');
				@fgetcsv($fInputFile, 0, Core_Array::getPost('import_price_separator'), Core_Array::getPost('import_price_stop'));
				$iNextSeekPosition = ftell($fInputFile);
				fclose($fInputFile);
			}
		}

		$Shop_Item_Import_Csv_Controller->seek = $iNextSeekPosition;

		ob_start();

		if(($iNextSeekPosition = $Shop_Item_Import_Csv_Controller->import()) !== FALSE)
		{
			$Shop_Item_Import_Csv_Controller->seek = $iNextSeekPosition;

			if($Shop_Item_Import_Csv_Controller->importAction == 0)
			{
				$Shop_Item_Import_Csv_Controller->importAction = 1;
			}

			$_SESSION['Shop_Item_Import_Csv_Controller'] = $Shop_Item_Import_Csv_Controller;

			$sRedirectAction = $oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/import/index.php', 'start_import', NULL, "shop_id={$oShop->id}&shop_group_id={$shop_group_id}");

			showStat($Shop_Item_Import_Csv_Controller);
		}
		else
		{
			$sRedirectAction = "";
			Core_Message::show(Core::_('Shop_Item.msg_download_price_complete'));
			showStat($Shop_Item_Import_Csv_Controller);
		}

		$oAdmin_Form_Entity_Form->add(
			Admin_Form_Entity::factory('Code')->html(ob_get_clean())
		);

		Core_Session::close();

		if($sRedirectAction)
		{
			$iRedirectTime = 1000;
			Core::factory('Core_Html_Entity_Script')
				->type('text/javascript')
				->value('setTimeout(function (){ ' . $sRedirectAction . '}, ' . $iRedirectTime . ')')
				->execute();
		}

		$sOnClick = "";
	}
	else
	{
		Core_Message::show(Core::_('User.demo_mode'), "error");
	}
}
else
{
	$windowId = $oAdmin_Form_Controller->getWindowId();

	$oAdmin_Form_Entity_Form
		->add(Admin_Form_Entity::factory('Radiogroup')
			->radio(array(
				Core::_('Shop_Item.import_price_list_file_type1'),
				Core::_('Shop_Item.import_price_list_file_type2')
			))
			->caption(Core::_('Shop_Item.export_file_type'))
			->divAttr(array('id' => 'import_types'))
			->name('import_price_type')
			->onchange("ShowImport('{$windowId}', $(this).val())")
		)
		->add(Admin_Form_Entity::factory('Code')
			->html("<script>$(function() {
				$('#{$windowId} #import_types').buttonset();
			});</script>")
		)
		->add(Admin_Form_Entity::factory('File')
			->name("csv_file")
			->caption(Core::_('Shop_Item.import_price_list_file'))
			->largeImage(array('show_params' => FALSE))
			->smallImage(array('show' => FALSE))
			->divAttr(array('style' => 'float: left'))
		)
		->add(Admin_Form_Entity::factory('Input')
			->name("alternative_file_pointer")
			->divAttr(array('style' => 'width: 175px;  float: left'))
			->caption(Core::_('Shop_Item.alternative_file_pointer_form_import'))
		)
		->add(
			Admin_Form_Entity::factory('Separator')
		)
		->add(Admin_Form_Entity::factory('Checkbox')
			->name("import_price_name_field_f")
			->caption(Core::_('Shop_Item.import_price_list_name_field_f'))
			->value(TRUE)
			->divAttr(array('id' => 'import_price_name_field_f'))
		)
		/*->add(Admin_Form_Entity::factory('Code')
			->html("<script>$(function() {
				$('#{$windowId} #import_price_name_field_f').button();
			});</script>")
		)*/
		->add(Admin_Form_Entity::factory('Radiogroup')
			->radio(array(
				Core::_('Shop_Item.import_price_list_separator1'),
				Core::_('Shop_Item.import_price_list_separator2'),
				Core::_('Shop_Item.import_price_list_separator3'),
				Core::_('Shop_Item.import_price_list_separator4')
			))
			->caption(Core::_('Shop_Item.import_price_list_separator'))
			->divAttr(array('style' => 'float: left', 'id' => 'import_price_separator'))
			->name('import_price_separator')
			// Разделитель ';'
			->value(1)
		)
		->add(Admin_Form_Entity::factory('Code')
			->html("<script>$(function() {
				$('#{$windowId} #import_price_separator').buttonset();
			});</script>")
		)
		->add(Admin_Form_Entity::factory('Input')
			->name("import_price_separator_text")
			->style("width: 20px; margin-top: 20px")
			->divAttr(array('id' => 'import_price_separator_text'))
		)
		->add(Admin_Form_Entity::factory('Radiogroup')
			->radio(array(
				Core::_('Shop_Item.import_price_list_stop1'),
				Core::_('Shop_Item.import_price_list_stop2')
			))
			->caption(Core::_('Shop_Item.import_price_list_stop'))
			->name('import_price_stop')
			->divAttr(array('style' => 'float: left;', 'id' => 'import_price_stop'))
		)
		->add(Admin_Form_Entity::factory('Code')
			->html("<script>$(function() {
				$('#{$windowId} #import_price_stop').buttonset();
			});</script>")
		)
		->add(Admin_Form_Entity::factory('Input')
			->name("import_price_stop_text")
			->style("width: 20px; margin-top: 20px")
			->divAttr(array('id' => 'import_price_stop_text')))
		->add(Admin_Form_Entity::factory('Select')
			->name("import_price_encoding")
			->options(array(
				'Windows-1251' => Core::_('Shop_Item.input_file_encoding0'),
				'UTF-8' => Core::_('Shop_Item.input_file_encoding1')
			))
			->divAttr(array('style' => 'width: 150px; float: left', 'id' => 'import_price_encoding'))
			->caption(Core::_('Shop_Item.price_list_encoding')))
		->add(Admin_Form_Entity::factory('Select')
			->name("shop_groups_parent_id")
			->options(array(' … ') + Shop_Item_Controller_Edit::fillShopGroup($oShop->id))
			->divAttr(array('style' => 'width: 300px;  float: left'))
			->caption(Core::_('Shop_Item.import_price_list_parent_group'))
			->value($oShopGroup->id)
		)
		->add(Admin_Form_Entity::factory('Separator'))
		->add(Admin_Form_Entity::factory('Input')
			->name("import_price_load_files_path")
			->divAttr(array('style' => 'width: 350px;'))
			->caption(Core::_('Shop_Item.import_price_list_images_path')))
		->add(Admin_Form_Entity::factory('Radiogroup')
			->radio(array(
				1 => Core::_('Shop_Item.import_price_action_items1'),
				2 => Core::_('Shop_Item.import_price_action_items2'),
				0 => Core::_('Shop_Item.import_price_action_items0')
				)
			)
			->caption(Core::_('Shop_Item.import_price_list_action_items'))
			->name('import_price_action_items')
			->divAttr(array('id' => 'import_price_list_action_items'))
			->value(1)
		)
		->add(Admin_Form_Entity::factory('Code')
			->html("<script>$(function() {
				$('#{$windowId} #import_price_list_action_items').buttonset();
			});</script>")
		)
		->add(Admin_Form_Entity::factory('Checkbox')
			->name("import_price_action_delete_image")
			->caption(Core::_('Shop_Item.import_price_list_action_delete_image'))
			->divAttr(array('id' => 'import_price_action_delete_image')))
		->add(Admin_Form_Entity::factory('Checkbox')
			->name("search_event_indexation")
			->caption(Core::_('Shop_Item.search_event_indexation_import'))
			->divAttr(array('id' => 'search_event_indexation')))
		->add(Admin_Form_Entity::factory('Input')
			->name("import_price_max_time")
			->caption(Core::_('Shop_Item.import_price_list_max_time'))
			->value(20)
			->divAttr(array('id' => 'import_price_max_time', 'style' => 'float: left; width: 150px; margin-right: 10px'))
		)
		->add(Admin_Form_Entity::factory('Input')
			->name("import_price_max_count")
			->caption(Core::_('Shop_Item.import_price_list_max_count'))
			->value(100)
			->divAttr(array('id' => 'import_price_max_count', 'style' => 'float: left; width: 150px'))
		)
		->add(Admin_Form_Entity::factory('Separator'))
	;

	$sOnClick = $oAdmin_Form_Controller->getAdminSendForm('show_form');

	Core_Session::start();
	unset($_SESSION['csv_params']);
	unset($_SESSION['Shop_Item_Import_Csv_Controller']);
	Core_Session::close();
}

function showStat($Shop_Item_Import_Csv_Controller)
{
	echo Core::_('Shop_Item.count_insert_item') . ' &#151; <b>' . $Shop_Item_Import_Csv_Controller->getInsertedItemsCount() . '</b><br/>';
	echo Core::_('Shop_Item.count_update_item') . ' &#151; <b>' . $Shop_Item_Import_Csv_Controller->getUpdatedItemsCount() . '</b><br/>';
	echo Core::_('Shop_Item.create_catalog') . ' &#151; <b>' . $Shop_Item_Import_Csv_Controller->getInsertedGroupsCount() . '</b><br/>';
	echo Core::_('Shop_Item.update_catalog') . ' &#151; <b>' . $Shop_Item_Import_Csv_Controller->getUpdatedGroupsCount() . '</b><br/>';
}

if($sOnClick)
{
	$oAdmin_Form_Entity_Form->add(
		Admin_Form_Entity::factory('Button')
		->name('show_form')
		->type('submit')
		->value(Core::_('Shop_Item.import_price_list_button_load'))
		->class('applyButton')
		->onclick($sOnClick)
	);
}

$oAdmin_Form_Entity_Form->execute();

$oAdmin_Answer = Core_Skin::instance()->answer();

$oAdmin_Answer
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(
		iconv("UTF-8", "UTF-8//IGNORE//TRANSLIT", ob_get_clean())
	)
	->title(Core::_('Shop_Item.import_price_list_link'))
	->execute();