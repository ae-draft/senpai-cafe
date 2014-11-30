<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Интернет-магазин".
 *
 * Файл: /modules/shop/shop.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class shop
{
	/**
	 * Массив групп товаров
	 *
	 * @var array
	 * @access private
	 */
	var $mas_groups;

	/**
	 * Устанавливать cookies для поддоменов.
	 */
	var $setCookieSubdomain = TRUE;

	/**
	 * Массив групп дополнительных свойств товаров
	 *
	 * @var array
	 * @access private
	 */
	var $mas_property_dir_groups;

	/**
	 * Массив групп товаров
	 *
	 * @var array
	 * @access private
	 */
	var $mas_groups_dir;

	/**
	 * Массив групп свойств групп товаров
	 *
	 * @var array
	 * @access private
	 */
	var $mas_ext_groups_dir;

	/**
	 * Массив дополнительных свойств для элементов
	 *
	 * @var array
	 * @access private
	 */
	var $CachePropertiesItem = array();

	/**
	 * Массив групп дополнительных свойств товара
	 *
	 * @var array
	 * @access private
	 */
	var $CachePropertiesItemsDir;

	/**
	 * Массив групп дополнительных свойств групп товара
	 *
	 * @var array
	 * @access private
	 */
	var $CachePropertiesGroupsDir;

	var $CacheDiscountsForItem = array();

	/**
	 * Массив комментариев для элементов магазина
	 *
	 * @var array
	 * @access private
	 */
	var $CacheComments = array();

	/**
	 * Массив ID подгрупп групп для магазина
	 *
	 * @var array
	 * @access private
	 */
	var $CacheGoupsIdTree;

	/**
	 * Массив цен для групп пользователей
	 *
	 * @var array
	 * @access private
	 */
	var $CacheUserSelectPrice;

	/**
	 * Массив ID сопутствующих товаров для товара
	 *
	 * @var array
	 * @access private
	 */
	var $CacheTyingProducts = array();

	/**
	 * Массив ID модификаций товара для товаров
	 *
	 * @var array
	 */
	var $CacheModificationItems = array();

	/**
	 * Кэш для метода GetPriceForItem()
	 *
	 * @var unknown_type
	 */
	var $CacheGetPriceForItem = array();

	var $error;

	/**
	 * Массив числа подгрупп и элементов для групп магазина
	 *
	 * @var array
	 * @access private
	 */
	var $CacheCountGroupsAndItems;

	var $hidden = "";

	// Массив групп
	var $MasGroup = array();

	/**
	 * Данные о разделах магазина от текущего до корневого
	 *
	 * @var array
	 * @access private
	 */
	var $mas_groups_to_root = array();

	/**
	 * Данные в кэше магазинов (в памяти)
	 * @var array
	 * @access private
	 */
	var $g_array_shop = array();

	/**
	 * Данные в кэше о валюте магазина
	 * @var array
	 * @access private
	 */
	var $g_shop_currency = array();

	/**
	 * Цена товара магазина
	 *
	 * @var float
	 */
	var $item_catalog_price = 0;

	/**
	 * Кэш для метода GetMesuresByLike
	 *
	 * @var array
	 * @access private
	 */
	var $CacheGetMesuresByLike = array();

	var $CacheGetCatalogItemIdByCmlId = array();

	var $CacheGetItemPropertyIdByCmlId = array();

	var $CacheGetPriceByCmlId = array();

	var $CacheGetCurrencyByLike = array();

	/**
	 * Кэш для метода GetSpecialPrice
	 * @var array
	 * @access private
	 */
	var $CacheGetSpecialPrice = array();

	/**
	 * Свойство содержит информацию об общем количестве товаров, выбранных методом GetAllItems()
	 *
	 * @var int
	 */
	var $GetAllItemTotalCount = 0;

	/**
	 * Свойство содержит информацию об общем количестве производителей, выбранных методом GetAllProducers()
	 *
	 * @var int
	 */
	var $GetAllProducersTotalCount = 0;

	/**
	 * Кэш товаров для метода GetItem
	 *
	 * @var array
	 */
	var $CacheGetItem = array();

	/**
	 * Кэш скидок для GetDiscount
	 *
	 * @var array
	 */
	var $CacheGetDiscount = array();

	/**
	 * Кэш для метода GetTax()
	 *
	 * @var array
	 */
	var $CacheGetTax = array();

	/**
	 * Кэш для метода GetSpecialPricesForItem()
	 *
	 * @var array
	 */
	var $CacheGetSpecialPricesForItem = array();

	/**
	 * Кэш для метода GetAllPricesForItem()
	 *
	 * @var array
	 */
	var $CacheGetAllPricesForItem = array();

	/**
	 * Кэш с данными о дополнительных свойствах групп магазина
	 *
	 * @var array
	 */
	var $PropertyGroupMass = array();

	/**
	 * Временный массив для исключения зацикливания рекурсии
	 *
	 * @var array
	 * @access private
	 */
	var $recursion_tmp = array();

	/**
	 * Кэш информации о производителях
	 *
	 * @var array
	 */
	var $cache_producer;

	/**
	 * Кэш информации о единицах измерений
	 *
	 * @var array
	 */
	var $cache_mesure;

	/**
	 * Формат упаковки и распаковки чисел
	 *
	 * @var int
	 * @access private
	 */
	var $crc32_pack_format = "i*";

	/**
	 * Формат для this->Round()
	 *
	 * @var string
	 * @see Round()
	 */
	var $float_format = "%.2f";

	/**
	 * Использовать ли систему контроля корректности данных, принимаемых из cookies.
	 * Значение по уолчанию = false. Рекомендуется указывать true.
	 *
	 * @var bool
	 */
	var $use_cookies_read_control = false;

	/**
	 * Кэш прав доступа к группе товаров
	 *
	 * @var array
	 * @access private
	 */
	var $ShopGroupAccess = array();

	/**
	 * Определенные права доступа пользователя к группе
	 *
	 * @var array
	 * @access private
	 */
	var $CacheIssetAccessForShopGroup;

	/**
	 * Число комментариев
	 *
	 * @var int
	 * @access private
	 */
	var $CountAllComments;

	/**
	 * Кэш для метода GetSpecialPriceForItem()
	 * @var array
	 */
	var $CacheGetSpecialPriceForItem = array();

	/**
	 * Кэш для метода GetCountry()
	 * @var array
	 */
	var $CacheGetCountry = array();

	/**
	 * Кэш для метода GetLocation()
	 * @var array
	 */
	var $CacheGetLocation = array();

	/**
	 * Кэш для метода GetCity()
	 * @var array
	 */
	var $CacheGetCity = array();

	/**
	 * Кэш для метода GetCityArea()
	 * @var array
	 */
	var $CacheGetCityArea = array();

	/**
	 * Тип сохранения корзины, 0 - COOKIES, 1 - СЕССИЯ.
	 * @var int
	 */
	var $CartType = 0;

	/**
	 * Количество знаков после запятой при округлении методом ConvertPrice()
	 * @var int
	 */
	var $iConvertPriceFractionalPart = 2;

	/**
	 * Кэш для GetCountItemsAndGroups
	 * @var array
	 */
	var $CacheGetCountItemsAndGroups = array();

	public function parseQueryBuilder($str, $queryBuilder)
	{
		$str = trim($str);
		$aStr = explode(' ', $str);

		$iCount = count($aStr);

		foreach ($aStr as $key => $value)
		{
			$sOriginalValue = trim($value);
			$value = strtoupper($sOriginalValue);
			switch ($value)
			{
				case 'AND':
					$queryBuilder->setAnd();
				break;
				case 'OR':
					$queryBuilder->setOr();
				break;
				case '(':
					$queryBuilder->open();
				break;
				case ')':
					$queryBuilder->close();
				break;
				case 'GROUP':
					$aTmp = explode('group by', mb_strtolower($str));
					if (isset($aTmp[1]))
					{
						$aGroup = explode(',', $aTmp[1]);
						foreach ($aGroup as $sGroup)
						{
							$queryBuilder->groupBy(trim($sGroup));
						}
					}
				break 2; // (!)
				case 'HAVING':
					$aTmp = explode('having', mb_strtolower($str));
					if (isset($aTmp[1]))
					{
						$queryBuilder->having(Core_QueryBuilder::expression(trim($aTmp[1])), NULL, NULL);
					}
				case 'LEFT':
					$aTmp = explode('left join', mb_strtolower($str), 2);
					if (isset($aTmp[1]))
					{
						$queryBuilder->leftJoin(Core_QueryBuilder::expression(trim($aTmp[1])), NULL);
					}
				break 2; // (!)
				default:
					if (is_numeric($sOriginalValue))
					{
						$sOriginalValue = intval($sOriginalValue);
						$queryBuilder->where(Core_QueryBuilder::expression($sOriginalValue), '=', $sOriginalValue);
					}
					// осталось 3 аргумента для where при конструкции "AND property_value_ints.value >= 61"
					elseif ($iCount - $key == 3)
					{
						$queryBuilder->where($sOriginalValue, $aStr[$key + 1], $aStr[$key + 2]);
					}
				break 2; // (!)
			}
		}

		return $this;
	}

	public function parseQueryBuilderWhere($column, $expression, $value, $queryBuilder)
	{
		if (trim(strtoupper($expression)) == 'IS NOT NULL')
		{
			$expression = 'IS NOT';
			$value = NULL;
		}

		$queryBuilder->where($column, $expression, $value);
		return $this;
	}

	function getPropertyValueTableName($type)
	{
		switch ($type)
		{
			default:
			case 0:
			case 3:
			case 5:
			case 7:
				$tableName = 'property_value_ints';
				$fieldName = 'value';
			break;
			case 1:
			case 10:
				$tableName = 'property_value_strings';
				$fieldName = 'value';
			break;
			case 4:
			case 6:
				$tableName = 'property_value_texts';
				$fieldName = 'value';
			break;
			case 8:
			case 9:
				$tableName = 'property_value_datetimes';
				$fieldName = 'value';
			break;
			case 2:
				$tableName = 'property_value_files';
				$fieldName = 'file';
			break;
		}

		return array('tableName' => $tableName, 'fieldName' => $fieldName);
	}

	function getArrayShop($oShop)
	{
		return array(
			'shop_shops_id' => $oShop->id,
			'shop_dir_id' => $oShop->shop_dir_id,
			'shop_company_id' => $oShop->shop_company_id,
			'shop_shops_name' => $oShop->name,
			'shop_shops_description' => $oShop->description,
			'shop_shops_yandex_market_name' => $oShop->yandex_market_name,
			'shop_image_small_max_width' => $oShop->image_small_max_width,
			'shop_image_big_max_width' => $oShop->image_large_max_width,
			'shop_image_small_max_height' => $oShop->image_small_max_height,
			'shop_image_big_max_height' => $oShop->image_large_max_height,
			'structure_id' => $oShop->structure_id,
			'shop_country_id' => $oShop->shop_country_id,
			'shop_currency_id' => $oShop->shop_currency_id,
			'shop_order_status_id' => $oShop->shop_order_status_id,
			'shop_mesures_id' => $oShop->shop_measure_id,
			'shop_shops_send_order_mail_admin' => $oShop->send_order_email_admin,
			'shop_shops_send_order_mail_user' => $oShop->send_order_email_user,
			'shop_shops_admin_mail' => $oShop->email,
			'shop_sort_order_field' => $oShop->items_sorting_field,
			'shop_sort_order_type' => $oShop->items_sorting_direction,
			'shop_group_sort_order_field' => $oShop->groups_sorting_field,
			'shop_group_sort_order_type' => $oShop->groups_sorting_direction,
			'users_id' => $oShop->user_id,
			'shop_comment_active' => $oShop->comment_active,
			'shop_watermark_file' => $oShop->watermark_file,
			'shop_watermark_default_use_big' => $oShop->watermark_default_use_large_image,
			'shop_watermark_default_use_small' => $oShop->watermark_default_use_small_image,
			'shop_watermark_default_position_x' => $oShop->watermark_default_position_x,
			'shop_watermark_default_position_y' => $oShop->watermark_default_position_y,
			'shop_items_on_page' => $oShop->items_on_page,
			'shop_shops_guid' => $oShop->guid,
			'shop_shops_url_type' => $oShop->url_type,
			'shop_format_date' => $oShop->format_date,
			'shop_format_datetime' => $oShop->format_datetime,
			'shop_typograph_item_by_default' => $oShop->typograph_default_items,
			'shop_typograph_group_by_default' => $oShop->typograph_default_groups,
			'shop_shops_apply_tags_automatic' => $oShop->apply_tags_automatically,
			'shop_shops_writeoff_payed_items' => $oShop->write_off_paid_items,
			'shop_shops_apply_keywords_automatic' => $oShop->apply_keywords_automatically,
			'shop_shops_file_name_conversion' => $oShop->change_filename,
			'shop_shops_attach_eitem' => $oShop->attach_digital_items,
			'shop_yandex_market_sales_notes_default' => $oShop->yandex_market_sales_notes_default,
			'shop_shops_access' => $oShop->siteuser_group_id,
			'shop_image_small_max_width_group' => $oShop->group_image_small_max_width,
			'shop_image_big_max_width_group' => $oShop->group_image_large_max_width,
			'shop_image_big_max_width_group' => $oShop->group_image_large_max_width,
			'shop_image_small_max_height_group' => $oShop->group_image_small_max_height,
			'shop_image_big_max_height_group' => $oShop->group_image_large_max_height,
			'shop_shops_default_save_proportions' => $oShop->preserve_aspect_ratio,
			'site_id' => $oShop->site_id,
		);
	}

	function getArrayShopDir($oShopDir)
	{
		return array(
			'shop_dir_id' => $oShopDir->id,
			'shop_dir_parent_id' => $oShopDir->parent_id,
			'shop_dir_name' => $oShopDir->name,
			'shop_dir_description' => $oShopDir->description,
			'site_id' => $oShopDir->site_id,
			'users_id' => $oShopDir->user_id
		);
	}

	function getArrayShopItem($oShopItem)
	{
		return array(
			'shop_items_catalog_item_id' => $oShopItem->id,
			'shop_items_catalog_shortcut_id' => $oShopItem->shortcut_id,
			'shop_tax_id' => $oShopItem->shop_tax_id,
			'shop_sallers_id' => $oShopItem->shop_seller_id,
			'shop_groups_id' => $oShopItem->shop_group_id,
			'shop_currency_id' => $oShopItem->shop_currency_id,
			'shop_shops_id' => $oShopItem->shop_id,
			'shop_producers_list_id' => $oShopItem->shop_producer_id,
			'shop_mesures_id' => $oShopItem->shop_measure_id,
			'shop_items_catalog_type' => $oShopItem->type,
			'shop_items_catalog_name' => $oShopItem->name,
			'shop_items_catalog_marking' => $oShopItem->marking,
			'shop_vendorcode' => $oShopItem->vendorcode,
			'shop_items_catalog_description' => $oShopItem->description,
			'shop_items_catalog_text' => $oShopItem->text,
			'shop_items_catalog_image' => $oShopItem->image_large,
			'shop_items_catalog_small_image' => $oShopItem->image_small,
			'shop_items_catalog_weight' => $oShopItem->weight,
			'shop_items_catalog_price' => $oShopItem->price,
			'shop_items_catalog_is_active' => $oShopItem->active,
			'shop_items_catalog_access' => $oShopItem->siteuser_group_id,
			'shop_items_catalog_order' => $oShopItem->sorting,
			'shop_items_catalog_path' => $oShopItem->path,
			'shop_items_catalog_seo_title' => $oShopItem->seo_title,
			'shop_items_catalog_seo_description' => $oShopItem->seo_description,
			'shop_items_catalog_seo_keywords' => $oShopItem->seo_keywords,
			'shop_items_catalog_indexation' => $oShopItem->indexing,
			'shop_items_catalog_small_image_height' => $oShopItem->image_small_height,
			'shop_items_catalog_small_image_width' => $oShopItem->image_small_width,
			'shop_items_catalog_big_image_height' => $oShopItem->image_large_height,
			'shop_items_catalog_big_image_width' => $oShopItem->image_large_width,
			'shop_items_catalog_yandex_market_allow' => $oShopItem->yandex_market,
			'shop_items_catalog_rambler_pokupki_allow' => 0,
			'shop_items_catalog_yandex_market_bid' => $oShopItem->yandex_market_bid,
			'shop_items_catalog_yandex_market_cid' => $oShopItem->yandex_market_cid,
			'shop_items_catalog_yandex_market_sales_notes' => $oShopItem->yandex_market_sales_notes,
			'site_users_id' => $oShopItem->siteuser_id,
			'shop_items_catalog_date_time' => $oShopItem->datetime,
			'shop_items_catalog_modification_id' => $oShopItem->modification_id,
			'shop_items_cml_id' => $oShopItem->guid,
			'shop_items_catalog_putoff_date' => $oShopItem->start_datetime,
			'shop_items_catalog_putend_date' => $oShopItem->end_datetime,
			'shop_items_catalog_show_count' => $oShopItem->showed,
			'users_id' => $oShopItem->user_id
		);
	}

	function getArrayShopGroup($oShopGroup)
	{
		return array(
			'shop_groups_id' => $oShopGroup->id,
			'shop_shops_id' => $oShopGroup->shop_id,
			'shop_groups_parent_id' => $oShopGroup->parent_id,
			'shop_groups_name' => $oShopGroup->name,
			'shop_groups_description' => $oShopGroup->description,
			'shop_groups_image' => $oShopGroup->image_large,
			'shop_groups_small_image' => $oShopGroup->image_small,
			'shop_groups_order' => $oShopGroup->sorting,
			'shop_groups_indexation' => $oShopGroup->indexing,
			'shop_groups_activity' => $oShopGroup->active,
			'shop_groups_access' => $oShopGroup->siteuser_group_id,
			'shop_groups_path' => $oShopGroup->path,
			'shop_groups_seo_title' => $oShopGroup->seo_title,
			'shop_groups_seo_description' => $oShopGroup->seo_description,
			'shop_groups_seo_keywords' => $oShopGroup->seo_keywords,
			'users_id' => $oShopGroup->user_id,
			'shop_groups_big_image_width' => $oShopGroup->image_large_width,
			'shop_groups_big_image_height' => $oShopGroup->image_large_height,
			'shop_groups_small_image_width' => $oShopGroup->image_small_width,
			'shop_groups_small_image_height' => $oShopGroup->image_small_height,
			'shop_groups_cml_id' => $oShopGroup->guid,
			'count_items' => $oShopGroup->items_count,
			'count_all_items' => $oShopGroup->items_total_count,
			'count_groups' => $oShopGroup->subgroups_count,
			'count_all_groups' => $oShopGroup->subgroups_total_count
		);
	}

	function getArrayShopDiscount($oShopDiscount)
	{
		return array(
			'shop_discount_id' => $oShopDiscount->id,
			'shop_shops_id' => $oShopDiscount->shop_id,
			'shop_discount_name' => $oShopDiscount->name,
			'shop_discount_from' => $oShopDiscount->start_datetime,
			'shop_discount_to' => $oShopDiscount->end_datetime,
			'shop_discount_is_active' => $oShopDiscount->active,
			'shop_discount_percent' => $oShopDiscount->percent,
			'users_id' => $oShopDiscount->user_id
		);
	}

	function getArrayShopItemDiscount($oShopItemDiscount)
	{
		return array(
			'shop_item_discount_id' => $oShopItemDiscount->id,
			'shop_items_catalog_item_id' => $oShopItemDiscount->shop_item_id,
			'shop_discount_id' => $oShopItemDiscount->shop_discount_id,
			'users_id' => $oShopItemDiscount->user_id
		);
	}

	function getArrayShopCart($oShop_Cart)
	{
		return array(
			'shop_cart_id' => $oShop_Cart->id,
			'shop_cart_flag_postpone' => $oShop_Cart->postpone	,
			'shop_cart_item_quantity' => $oShop_Cart->quantity,
			'shop_items_catalog_item_id' => $oShop_Cart->shop_item_id,
			'shop_shops_id' => $oShop_Cart->shop_id,
			'shop_warehouse_id' => $oShop_Cart->shop_warehouse_id,
			'site_user_id' => $oShop_Cart->siteuser_id
		);
	}

	function getArrayShopCurrency($oShopCurrency)
	{
		return array(
			'shop_currency_id' => $oShopCurrency->id,
			'shop_currency_name' => $oShopCurrency->name,
			'shop_currency_international_name' => $oShopCurrency->code,
			'shop_currency_value_in_basic_currency' => $oShopCurrency->exchange_rate,
			'shop_currency_is_default' => $oShopCurrency->default,
			'shop_currency_order' => $oShopCurrency->sorting,
			'users_id' => $oShopCurrency->user_id
		);
	}

	function getArrayShopMeasure($oShopMeasure)
	{
		return array(
			'shop_mesures_id' => $oShopMeasure->id,
			'shop_mesures_name' => $oShopMeasure->name,
			'shop_mesures_description' => $oShopMeasure->description,
			'users_id' => $oShopMeasure->user_id
		);
	}

	function getArrayShopProducer($oShopProducer)
	{
		return array(
			'shop_producers_list_id' => $oShopProducer->id,
			'shop_shops_id' => $oShopProducer->shop_id,
			'shop_producers_list_name' => $oShopProducer->name,
			'shop_producers_list_description' => $oShopProducer->description,
			'shop_producers_list_image' => $oShopProducer->image_large,
			'shop_producers_list_small_image' => $oShopProducer->image_small,
			'shop_producers_list_order' => $oShopProducer->sorting,
			'shop_producers_list_path' => $oShopProducer->path,
			'shop_producers_list_address' => $oShopProducer->address,
			'shop_producers_list_phone' => $oShopProducer->phone,
			'shop_producers_list_fax' => $oShopProducer->fax,
			'shop_producers_list_site' => $oShopProducer->site,
			'shop_producers_list_email' => $oShopProducer->email,
			'shop_producers_list_inn' => $oShopProducer->tin,
			'shop_producers_list_kpp' => $oShopProducer->kpp,
			'shop_producers_list_ogrn' => $oShopProducer->psrn,
			'shop_producers_list_okpo' => $oShopProducer->okpo,
			'shop_producers_list_okved' => $oShopProducer->okved,
			'shop_producers_list_bik' => $oShopProducer->bik,
			'shop_producers_list_account' => $oShopProducer->current_account,
			'shop_producers_list_corr_account' => $oShopProducer->correspondent_account,
			'shop_producers_list_bank_name' => $oShopProducer->bank_name,
			'shop_producers_list_bank_address' => $oShopProducer->bank_address,
			'shop_producers_list_seo_title' => $oShopProducer->seo_title,
			'shop_producers_list_seo_description' => $oShopProducer->seo_description,
			'shop_producers_list_seo_keywords' => $oShopProducer->seo_keywords,
			'users_id' => $oShopProducer->user_id
		);
	}

	function getArrayShopOrder($oShopOrder)
	{
		return array(
			'shop_order_id' => $oShopOrder->id,
			'shop_shops_id' => $oShopOrder->shop_id,
			'shop_location_id' => $oShopOrder->shop_country_location_id,
			'shop_country_id' => $oShopOrder->shop_country_id,
			'shop_city_id' => $oShopOrder->shop_country_location_city_id,
			'shop_city_area_id' => $oShopOrder->shop_country_location_city_area_id,
			'shop_cond_of_delivery_id' => $oShopOrder->shop_delivery_condition_id,
			'site_users_id' => $oShopOrder->siteuser_id,
			'shop_order_users_name' => $oShopOrder->name,
			'shop_order_users_surname' => $oShopOrder->surname,
			'shop_order_users_patronymic' => $oShopOrder->patronymic,
			'shop_order_users_email' => $oShopOrder->email,
			'shop_order_users_company' => $oShopOrder->company,
			'shop_order_users_fax' => $oShopOrder->fax,
			'shop_order_status_id' => $oShopOrder->shop_order_status_id,
			'shop_currency_id' => $oShopOrder->shop_currency_id,
			'shop_system_of_pay_id' => $oShopOrder->shop_payment_system_id,
			'shop_order_date_time' => $oShopOrder->datetime,
			'shop_order_status_of_pay' => $oShopOrder->paid,
			'shop_order_date_of_pay' => $oShopOrder->payment_datetime,
			'shop_order_address' => $oShopOrder->address,
			'shop_order_index' => $oShopOrder->postcode,
			'shop_order_phone' => $oShopOrder->phone,
			'shop_order_description' => $oShopOrder->description,
			'shop_order_system_information' => $oShopOrder->system_information,
			'shop_order_cancel' => $oShopOrder->canceled,
			'users_id' => $oShopOrder->user_id,
			'shop_order_account_number' => $oShopOrder->invoice,
			'shop_order_change_status_datetime' => $oShopOrder->status_datetime,
			'shop_order_guid' => $oShopOrder->guid,
			'shop_order_sending_info' => $oShopOrder->delivery_information,
			'shop_order_ip' => $oShopOrder->ip,
			'shop_order_unload' => $oShopOrder->unloaded
		);
	}

	function getArrayShopOrderStatus($oShopOrderStatus)
	{
		return array(
			'shop_order_status_id' => $oShopOrderStatus->id,
			'shop_order_status_name' => $oShopOrderStatus->name,
			'shop_order_status_description' => $oShopOrderStatus->description,
			'users_id' => $oShopOrderStatus->user_id
		);
	}

	function getArrayShopTax($oShopTax)
	{
		return array(
			'shop_tax_id' => $oShopTax->id,
			'shop_tax_name' => $oShopTax->name,
			'shop_tax_rate' => $oShopTax->rate,
			'shop_tax_is_in_price' => $oShopTax->tax_is_included,
			'shop_tax_cml_id' => $oShopTax->guid,
			'users_id' => $oShopTax->user_id
		);
	}

	function getArrayShopOrderItem($oShopOrderItem)
	{
		$aShop_Order_Item_Digitals = $oShopOrderItem->Shop_Order_Item_Digitals->findAll();

		$tax = $this->Round($oShopOrderItem->price * $oShopOrderItem->rate / 100);

		return array(
			'shop_order_items_id' => $oShopOrderItem->id,
			'shop_items_catalog_item_id' => $oShopOrderItem->shop_item_id,
			'shop_order_id' => $oShopOrderItem->shop_order_id,
			'shop_order_items_quantity' => $oShopOrderItem->quantity,
			'shop_order_items_price' => $oShopOrderItem->price + $tax,
			'shop_order_items_name' => $oShopOrderItem->name,
			'shop_order_items_marking' => $oShopOrderItem->marking,
			'shop_tax_rate' => $oShopOrderItem->rate,
			'users_id' => $oShopOrderItem->user_id,
			'shop_order_items_eitem_resource' => $oShopOrderItem->hash,
			'shop_eitem_id' => isset($aShop_Order_Item_Digitals[0]) ? $aShop_Order_Item_Digitals[0]->shop_item_digital_id : 0,
			'shop_order_items_type' => $oShopOrderItem->type,
			'shop_warehouse_id' => $oShopOrderItem->shop_warehouse_id
		);
	}

	function getArrayShopPrice($oShopPrice)
	{
		return array(
			'shop_list_of_prices_id' => $oShopPrice->id,
			'shop_shops_id' => $oShopPrice->shop_id,
			'shop_list_of_prices_name' => $oShopPrice->name,
			'shop_list_of_prices_percent_to_basic' => $oShopPrice->percent,
			'site_users_group_id' => $oShopPrice->siteuser_group_id,
			'shop_list_of_prices_cml_id' => $oShopPrice->guid,
			'users_id' => $oShopPrice->user_id
		);
	}

	function getArrayShopDelivery($oShopDelivery)
	{
		return array(
			'shop_type_of_delivery_id' => $oShopDelivery->id,
			'shop_type_of_delivery_name' => $oShopDelivery->name,
			'shop_type_of_delivery_description' => $oShopDelivery->description,
			'shop_type_of_delivery_image' => $oShopDelivery->image,
			'shop_shops_id'  => $oShopDelivery->shop_id,
			'users_id'  => $oShopDelivery->user_id,
			'shop_type_of_delivery_order' => $oShopDelivery->sorting
		);
	}

	function getArrayShopDeliveryCondition($oShopDeliveryCondition)
	{
		return array(
			'shop_cond_of_delivery_id' => $oShopDeliveryCondition->id,
			'shop_type_of_delivery_id' => $oShopDeliveryCondition->shop_delivery_id,
			'shop_country_id' => $oShopDeliveryCondition->shop_country_id,
			'shop_location_id' => $oShopDeliveryCondition->shop_country_location_id,
			'shop_city_id' => $oShopDeliveryCondition->shop_country_location_city_id,
			'shop_city_area_id' => $oShopDeliveryCondition->shop_country_location_city_area_id,
			'shop_cond_of_delivery_name' => $oShopDeliveryCondition->name,
			'shop_cond_of_delivery_weight_from' => $oShopDeliveryCondition->min_weight,
			'shop_cond_of_delivery_weight_to' => $oShopDeliveryCondition->max_weight,
			'shop_cond_of_delivery_price_from' => $oShopDeliveryCondition->min_price,
			'shop_cond_of_delivery_price_to' => $oShopDeliveryCondition->max_price,
			'shop_cond_of_delivery_description' => $oShopDeliveryCondition->description,
			'shop_cond_of_delivery_price' => $oShopDeliveryCondition->price,
			'shop_currency_id' => $oShopDeliveryCondition->shop_currency_id,
			'users_id' => $oShopDeliveryCondition->user_id,
			'shop_tax_id' => $oShopDeliveryCondition->shop_tax_id
		);
	}

	function getArrayShopPaymentSystem($oShop_Payment_System)
	{
		return array(
			'shop_system_of_pay_id' => $oShop_Payment_System->id,
			'shop_currency_id' => $oShop_Payment_System->shop_currency_id,
			'shop_shops_id' => $oShop_Payment_System->shop_id,
			'shop_system_of_pay_name' => $oShop_Payment_System->name,
			'shop_system_of_pay_description' => $oShop_Payment_System->description,
			'shop_system_of_pay_is_active' => $oShop_Payment_System->active,
			'users_id' => $oShop_Payment_System->user_id,
			'shop_system_of_pay_order' => $oShop_Payment_System->sorting
		);
	}

	function getArrayShopCountryLocation($oShop_Country_Location)
	{
		return array(
			'shop_location_id' => $oShop_Country_Location->id,
			'shop_location_name' => $oShop_Country_Location->name,
			'shop_country_id' => $oShop_Country_Location->shop_country_id,
			'shop_location_order' => $oShop_Country_Location->sorting,
			'users_id' => $oShop_Country_Location->user_id
		);
	}

	function getArrayShopCountry($oShop_Country)
	{
		return array(
			'shop_country_id' => $oShop_Country->id,
			'shop_country_name' => $oShop_Country->name,
			'shop_country_order' => $oShop_Country->sorting,
			'users_id' => $oShop_Country->user_id,
		);
	}

	function getArrayShopCountryLocationCity($oShop_Country_Location_City)
	{
		return array(
			'shop_city_id' => $oShop_Country_Location_City->id,
			'shop_location_id' => $oShop_Country_Location_City->shop_country_location_id,
			'shop_city_name' => $oShop_Country_Location_City->name,
			'shop_city_order' => $oShop_Country_Location_City->sorting,
			'users_id' => $oShop_Country_Location_City->user_id
		);
	}

	function getArrayShopCountryLocationCityArea($oShop_Country_Location_City_Area)
	{
		return array(
			'shop_city_area_id' => $oShop_Country_Location_City_Area->id,
			'shop_city_area_name' => $oShop_Country_Location_City_Area->name,
			'shop_city_id' => $oShop_Country_Location_City_Area->shop_country_location_city_id,
			'shop_city_area_order' => $oShop_Country_Location_City_Area->sorting,
			'users_id' => $oShop_Country_Location_City_Area->user_id
		);
	}

	function correctItemPropertyType($itemType)
	{
		if ($itemType == 1)
		{
			$type = 0;
		}
		elseif ($itemType == 2)
		{
			$type = 1;
		}
		elseif ($itemType == 3)
		{
			$type = 2;
		}
		elseif ($itemType == 4)
		{
			$type = 3;
		}
		elseif ($itemType == 6)
		{
			$type = 4;
		}
		elseif ($itemType == 8)
		{
			$type = 5;
		}
		elseif ($itemType == 9)
		{
			$type = 6;
		}
		elseif ($itemType == 9)
		{
			$type = 6;
		}
		else
		{
			$type = 0;
		}

		return $type;
	}

	function getArrayItemProperty($oProperty)
	{
		$type = $this->correctItemPropertyType($oProperty->type);

		return array(
			'shop_list_of_properties_id' => $oProperty->id,
			'shop_shops_id' => $oProperty->Shop_Item_Property->shop_id,
			'shop_mesures_id' => $oProperty->Shop_Item_Property->shop_measure_id,
			'lists_id' => $oProperty->list_id,
			'shop_list_of_properties_name' => $oProperty->name,
			'shop_list_of_properties_xml_name' => $oProperty->tag_name,
			'shop_list_of_properties_type' => $type,
			'shop_list_of_properties_prefics' => $oProperty->Shop_Item_Property->prefix,
			'shop_list_of_properties_default_value' => $oProperty->default_value,
			'shop_list_of_properties_order' => $oProperty->sorting,
			'shop_list_of_properties_show_kind' => $oProperty->Shop_Item_Property->filter,
			'users_id' => $oProperty->user_id,
			'shop_list_of_properties_cml_id' => $oProperty->guid,
			'shop_properties_items_dir_id' => $oProperty->property_dir_id,
			'shop_list_of_properties_default_big_width' => $oProperty->image_large_max_width,
			'shop_list_of_properties_default_big_height' => $oProperty->image_large_max_height,
			'shop_list_of_properties_default_small_width' => $oProperty->image_small_max_width,
			'shop_list_of_properties_default_small_height' => $oProperty->image_small_max_height,
			'shop_list_of_properties_description' => $oProperty->description
		);
	}

	function getArrayItemPropertyValue($oPropertyValue)
	{
		$oProperty = Core_Entity::factory('Property', $oPropertyValue->property_id);

		$array = array(
			'shop_properties_items_id' => $oPropertyValue->id
		);

		if ($oProperty->type != 2)
		{
			$array['shop_properties_items_value'] = $oPropertyValue->value;
			$array['shop_properties_items_file'] = '';
			$array['shop_properties_items_value_small'] = '';
			$array['shop_properties_items_file_small'] = '';
		}
		else
		{
			$array['shop_properties_items_value'] = $oPropertyValue->file_name;
			$array['shop_properties_items_file'] = $oPropertyValue->file;
			$array['shop_properties_items_value_small'] = $oPropertyValue->file_small_name;
			$array['shop_properties_items_file_small'] = $oPropertyValue->file_small;
		}

		return $array;
	}

	function getArrayGroupProperty($oProperty)
	{
		if ($oProperty->type == 1)
		{
			$type = 0;
		}
		elseif ($oProperty->type == 2)
		{
			$type = 1;
		}
		elseif ($oProperty->type == 3)
		{
			$type = 2;
		}
		elseif ($oProperty->type == 4)
		{
			$type = 3;
		}
		elseif ($oProperty->type == 6)
		{
			$type = 4;
		}
		elseif ($oProperty->type == 8)
		{
			$type = 5;
		}
		elseif ($oProperty->type == 9)
		{
			$type = 6;
		}
		elseif ($oProperty->type == 9)
		{
			$type = 6;
		}
		else
		{
			$type = 0;
		}

		return array(
			'shop_properties_group_id' => $oProperty->id,
			'shop_shops_id' => $oProperty->Shop_Item_Property->shop_id,
			'lists_id' => $oProperty->list_id,
			'shop_properties_group_name' =>  $oProperty->name,
			'shop_properties_group_xml_name' => $oProperty->tag_name,
			'shop_properties_group_type' => $type,
			'shop_properties_group_default_value' => $oProperty->default_value,
			'shop_properties_group_order' => $oProperty->sorting,
			'users_id' => $oProperty->user_id,
			'shop_properties_groups_dir_id' => $oProperty->property_dir_id,
			'shop_properties_group_default_small_height' => $oProperty->image_small_max_height,
			'shop_properties_group_default_small_width' => $oProperty->image_small_max_width,
			'shop_properties_group_default_big_height' => $oProperty->image_large_max_height,
			'shop_properties_group_default_big_width' => $oProperty->image_large_max_width,
			'shop_properties_group_cml' => $oProperty->guid
		);
	}

	function getArrayGroupPropertyValue($oPropertyValue)
	{
		$oProperty = Core_Entity::factory('Property', $oPropertyValue->property_id);

		$array = array(
			'shop_properties_group_value_id' => $oPropertyValue->id,
		);

		if ($oProperty->type != 2)
		{
			$array['shop_properties_group_value_value'] = $oPropertyValue->value;
			$array['shop_properties_group_value_file'] = '';
			$array['shop_properties_group_value_small'] = '';
			$array['shop_properties_group_value_file_small'] = '';
		}
		else
		{
			$array['shop_properties_group_value_value'] = $oPropertyValue->file_name;
			$array['shop_properties_group_value_file'] = $oPropertyValue->file;
			$array['shop_properties_group_value_small'] = $oPropertyValue->file_small_name;
			$array['shop_properties_group_value_file_small'] = $oPropertyValue->file_small;
		}

		return $array;
	}

	function getArrayGroupPropertyDir($oPropertyDir)
	{
		return array(
			'shop_properties_groups_dir_id' => $oPropertyDir->id,
			'shop_shops_id' => $oPropertyDir->Shop_Group_Property_Dir->shop_id,
			'shop_properties_groups_dir_parent_id' => $oPropertyDir->parent_id,
			'shop_properties_groups_dir_name' => $oPropertyDir->name,
			'shop_properties_groups_dir_description' => $oPropertyDir->description,
			'shop_properties_groups_dir_order' => $oPropertyDir->sorting,
			'users_id' => $oPropertyDir->user_id
		);
	}

	function getArrayShopItemPropertyForGroup($oShopItemPropertyForGroup)
	{
		return array(
			'shop_properties_item_for_groups_id' => $oShopItemPropertyForGroup->id,
			'shop_groups_id' => $oShopItemPropertyForGroup->shop_group_id,
			'shop_list_of_properties_id' => $oShopItemPropertyForGroup->shop_item_property_id,
			'shop_shops_id' => $oShopItemPropertyForGroup->shop_id,
			'users_id' => $oShopItemPropertyForGroup->user_id
		);
	}

	function getArrayShopItemAssociated($oShop_Item_Associated)
	{
		return array(
			'shop_intermediate_id' => $oShop_Item_Associated->id,
			'shop_items_catalog_item_id' => $oShop_Item_Associated->shop_item_id,
			'sho_shop_items_catalog_item_id' => $oShop_Item_Associated->shop_item_associated_id,
			'users_id' => $oShop_Item_Associated->user_id,
			'shop_intermediate_count' => $oShop_Item_Associated->count
		);
	}

	function getArrayShopItemPrice($oShop_Item_Price)
	{
		return array(
			'shop_prices_to_item_id' => $oShop_Item_Price->id,
			'shop_items_catalog_item_id' => $oShop_Item_Price->shop_item_id,
			'shop_list_of_prices_id' => $oShop_Item_Price->shop_price_id,
			'shop_prices_to_item_value' => $oShop_Item_Price->value
		);
	}

	function getArrayProducer($oProducer)
	{
		return array(
			'shop_producers_list_id' => $oProducer->id,
			'shop_shops_id' => $oProducer->shop_id,
			'shop_producers_list_name' => $oProducer->name,
			'shop_producers_list_description' => $oProducer->description,
			'shop_producers_list_image' => $oProducer->image_large,
			'shop_producers_list_small_image' => $oProducer->image_small,
			'shop_producers_list_order' => $oProducer->sorting,
			'shop_producers_list_path' => $oProducer->path,
			'users_id' => $oProducer->user_id,
			'shop_producers_list_address' => $oProducer->address,
			'shop_producers_list_phone' => $oProducer->phone,
			'shop_producers_list_fax' => $oProducer->fax,
			'shop_producers_list_site' => $oProducer->site,
			'shop_producers_list_email' => $oProducer->email,
			'shop_producers_list_inn' => $oProducer->tin,
			'shop_producers_list_kpp' => $oProducer->kpp,
			'shop_producers_list_ogrn' => $oProducer->psrn,
			'shop_producers_list_okpo' => $oProducer->okpo,
			'shop_producers_list_okved' => $oProducer->okved,
			'shop_producers_list_bik' => $oProducer->bik,
			'shop_producers_list_account' => $oProducer->current_account,
			'shop_producers_list_corr_account' => $oProducer->correspondent_account,
			'shop_producers_list_bank_name' => $oProducer->bank_name,
			'shop_producers_list_bank_address' => $oProducer->bank_address,
			'shop_producers_list_seo_title' => $oProducer->seo_title,
			'shop_producers_list_seo_description' => $oProducer->seo_description,
			'shop_producers_list_seo_keywords' => $oProducer->seo_keywords
		);
	}

	function getArrayShopSeller($oShop_Seller)
	{
		return array(
			'shop_sallers_id' => $oShop_Seller->id,
			'shop_shops_id' => $oShop_Seller->shop_id,
			'site_users_id' => $oShop_Seller->siteuser_id,
			'shop_sallers_name' => $oShop_Seller->name,
			'shop_sallers_comment' => $oShop_Seller->description,
			'shop_sallers_contact_person' => $oShop_Seller->contact_person,
			'shop_sallers_image' => $oShop_Seller->image_large,
			'shop_sallers_small_image' => $oShop_Seller->image_small,
			'shop_sallers_image_height' => $oShop_Seller->image_large_height,
			'shop_sallers_image_width' => $oShop_Seller->image_large_width,
			'shop_sallers_small_image_height' => $oShop_Seller->image_small_height,
			'shop_sallers_small_image_width' => $oShop_Seller->image_small_width,
			'shop_sallers_address' => $oShop_Seller->address,
			'shop_sallers_phone' => $oShop_Seller->phone,
			'shop_sallers_fax' => $oShop_Seller->fax,
			'shop_sallers_http' => $oShop_Seller->site,
			'shop_sallers_email' => $oShop_Seller->email,
			'shop_sallers_inn' => $oShop_Seller->tin,
			'users_id' => $oShop_Seller->user_id
		);
	}

	function getArrayShopItemComment($oComment)
	{
		return array(
			'shop_comment_id' => $oComment->id,
			'shop_items_catalog_item_id' => $oComment->Comment_Shop_Item->Shop_Item->id,
			'site_users_id' => $oComment->siteuser_id,
			'shop_comment_user_name' => $oComment->author,
			'shop_comment_user_email' => $oComment->email,
			'shop_comment_user_ip' => $oComment->ip,
			'shop_comment_subject' => $oComment->subject,
			'shop_comment_text' => $oComment->text,
			'shop_comment_grade' => $oComment->grade,
			'shop_comment_date_time' => $oComment->datetime,
			'shop_comment_active' => $oComment->active,
			'shop_comment_is_comment' => (trim($oComment->author) == '' && trim($oComment->subject) == '' && trim($oComment->text) == '' ? 0 : 1),
			'users_id' => $oComment->user_id
		);
	}

	function getArrayShopCompany($oShop_Company)
	{
		return array(
			'shop_company_id' => $oShop_Company->id,
			'shop_company_name' => $oShop_Company->name,
			'shop_company_description' => $oShop_Company->description,
			'shop_company_inn' => $oShop_Company->tin,
			'shop_company_kpp' => $oShop_Company->kpp,
			'shop_company_ogrn' => $oShop_Company->psrn,
			'shop_company_okpo' => $oShop_Company->okpo,
			'shop_company_okved' => $oShop_Company->okved,
			'shop_company_bik' => $oShop_Company->bic,
			'shop_company_account' => $oShop_Company->current_account,
			'shop_company_corr_account' => $oShop_Company->correspondent_account,
			'shop_company_bank_name' => $oShop_Company->bank_name,
			'shop_company_bank_address' => $oShop_Company->bank_address,
			'shop_company_fio' => $oShop_Company->legal_name,
			'shop_company_accountant_fio' => $oShop_Company->accountant_legal_name,
			'shop_company_address' => $oShop_Company->address,
			'shop_company_phone' => $oShop_Company->phone,
			'shop_company_fax' => $oShop_Company->fax,
			'shop_company_site' => $oShop_Company->site,
			'shop_company_email' => $oShop_Company->email,
			'users_id' => $oShop_Company->user_id,
			'shop_company_guid' => $oShop_Company->guid
		);
	}

	function getArrayShopPurchaseDiscount($oShop_Purchase_Discount)
	{
		return array(
			'shop_order_discount_id' => $oShop_Purchase_Discount->id,
			'shop_shops_id' => $oShop_Purchase_Discount->shop_id,
			'shop_currency_id' => $oShop_Purchase_Discount->shop_currency_id,
			'name' => $oShop_Purchase_Discount->shop_order_discount_name,
			'shop_order_discount_sum_from' => $oShop_Purchase_Discount->min_amount,
			'shop_order_discount_sum_to' => $oShop_Purchase_Discount->max_amount,
			'shop_order_discount_count_from' => $oShop_Purchase_Discount->min_count,
			'shop_order_discount_count_to' => $oShop_Purchase_Discount->max_count,
			'shop_order_discount_logic_between_elements' => $oShop_Purchase_Discount->mode,
			'shop_order_discount_active' => $oShop_Purchase_Discount->active,
			'shop_order_discount_active_from' => $oShop_Purchase_Discount->start_datetime,
			'shop_order_discount_active_to' => $oShop_Purchase_Discount->end_datetime,
			'shop_order_discount_type' => $oShop_Purchase_Discount->type,
			'shop_order_discount_value' => $oShop_Purchase_Discount->value,
			'shop_order_discount_is_coupon' => $oShop_Purchase_Discount->coupon,
			'users_id' => $oShop_Purchase_Discount->user_id
		);
	}

	function getArrayShopPurchaseDiscountCoupon($oShop_Purchase_Discount_Coupon)
	{
		return array(
			'shop_coupon_id' => $oShop_Purchase_Discount_Coupon->id,
			'shop_order_discount_id' => $oShop_Purchase_Discount_Coupon->shop_purchase_discount_id,
			'shop_coupon_name' => $oShop_Purchase_Discount_Coupon->name,
			'shop_coupon_active' => $oShop_Purchase_Discount_Coupon->active,
			'shop_coupon_count' => $oShop_Purchase_Discount_Coupon->count,
			'shop_coupon_text' => $oShop_Purchase_Discount_Coupon->text,
			'users_id' => $oShop_Purchase_Discount_Coupon->user_id
		);
	}

	function getArrayShopItemDigital($oShop_Item_Digital)
	{
		return array(
			'shop_eitem_id' => $oShop_Item_Digital->id,
			'shop_items_catalog_item_id' => $oShop_Item_Digital->shop_item_id,
			'shop_eitem_name' => $oShop_Item_Digital->name,
			'shop_eitem_value' => $oShop_Item_Digital->value,
			'shop_eitem_filename' => $oShop_Item_Digital->filename,
			'shop_eitem_count' => $oShop_Item_Digital->count,
			'users_id' => $oShop_Item_Digital->user_id
		);
	}

	function getArrayShopSiteuserTransaction($oShop_Siteuser_Transaction)
	{
		return array(
			'shop_site_users_account_id' => $oShop_Siteuser_Transaction->id,
			'shop_shops_id' => $oShop_Siteuser_Transaction->shop_id,
			'site_users_id' => $oShop_Siteuser_Transaction->siteuser_id,
			'shop_site_users_account_active' => $oShop_Siteuser_Transaction->active,
			'shop_site_users_account_datetime' => $oShop_Siteuser_Transaction->datetime,
			'shop_site_users_account_sum' => $oShop_Siteuser_Transaction->amount,
			'shop_currency_id' => $oShop_Siteuser_Transaction->shop_currency_id,
			'shop_site_users_account_sum_in_base_currency' => $oShop_Siteuser_Transaction->amount_base_currency,
			'shop_order_id' => $oShop_Siteuser_Transaction->shop_order_id,
			'shop_site_users_account_description' => $oShop_Siteuser_Transaction->description,
			'users_id' => $oShop_Siteuser_Transaction->user_id,
			'shop_site_users_account_type' => $oShop_Siteuser_Transaction->type
		);
	}

	function getArrayShopSpecialprice($oShop_Specialprice)
	{
		return array(
			'shop_special_prices_id' => $oShop_Specialprice->id,
			'shop_items_catalog_item_id' => $oShop_Specialprice->shop_item_id,
			'shop_special_prices_from' => $oShop_Specialprice->min_quantity,
			'shop_special_prices_to' => $oShop_Specialprice->max_quantity,
			'shop_special_prices_price' => $oShop_Specialprice->price,
			'shop_special_prices_percent' => $oShop_Specialprice->percent
		);
	}


	/**
	 * Функция обратного вызова для отображения блока
	 * на основной странице центра администрирования.
	 *
	 */
	function AdminMainPage()
	{

	}

	/**
	 * Получение числа комментариев к товарам
	 *
	 * @return int число комментариев
	 */
	function GetCountAllComments()
	{
		return $this->CountAllComments;
	}

	/**
	 * Установление числа комментариев к товарам
	 * @param int $count_comments общее число комментариев к товарам
	 */
	function SetCountAllComments($count_comments)
	{
		$this->CountAllComments = intval($count_comments);
	}

	/**
	 * Округление цен к формату, приведенного в $this->float_format
	 *
	 * @param float $float цена
	 * @return string
	 */
	function Round($float)
	{
		return Shop_Controller::instance()
			->floatFormat($this->float_format)
			->round($float);
	}

	/**
	 * Получение времени истечения данных в корзине, хранящейся в кукисах.
	 * Метод работает на основе значение константы SHOP_COOKIE_EXPIRES, если она задана.
	 * Значение = 0 означает хранение данных до закрытия браузера.
	 * При отсутствии заданной константы данные в кукисах хранятся 365 дней (31536000 секунд). Значение константы задается в секундах.
	 *
	 */
	function GetCookieExpires()
	{
		if (defined('SHOP_COOKIE_EXPIRES'))
		{
			$time = SHOP_COOKIE_EXPIRES == 0
				? 0
				: time() + SHOP_COOKIE_EXPIRES;
		}
		else
		{
			$time = time() + 31536000;
		}

		return $time;
	}

	/**
	 * Конструктор класса
	 *
	 * @access private
	 * @param boolean $delete_cart_from_cookie_f флаг указывающий на необходимость удаления информации о товарах корзины из кукисов
	 * (по умолчанию содержит false - кукисы не очищать)
	 * @return shop
	 */
	function shop($delete_cart_from_cookie_f = FALSE)
	{
		// Проверяем наличие зарегистрированного пользователя и корзины в кукисах
		if ($delete_cart_from_cookie_f == TRUE
		&& isset($_SESSION['siteuser_id'])
		/* && isset($_COOKIE['CART'])*/
		&& class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			if ($SiteUsers->GetSiteUser($_SESSION['siteuser_id']))
			{
				// Очищаем корзину - пишем пустой массив
				$CART = array();
				$this->SetCart($CART);
			}
		}

		if (defined('ConvertPriceFractionalPart'))
		{
			$this->iConvertPriceFractionalPart = ConvertPriceFractionalPart;
		}
	}

	/**
	 * Загрузка массива данных из cookies
	 *
	 * @param str $cookie_name наименование cookie
	 * @return mixed
	 */
	function GetCookie($cookie_name)
	{
		if (!empty ($_COOKIE[$cookie_name]))
		{
			$cookie = Core_Type_Conversion::toStr($_COOKIE[$cookie_name]);

			// Если указано, что нужна проверка контрольной суммы
			if ($this->use_cookies_read_control)
			{
				$pos = mb_strpos($cookie, ';');
				$crc32_read = mb_substr($cookie, 0, $pos);

				// Берем строку с полезными данными (от 4 позиции и дальше)
				$cookie = mb_substr($cookie, $pos + 1);

				// Контрольные суммы совпали
				$return = Core::crc32($cookie) == $crc32_read
					? $cookie
					: FALSE;
			}
			else
			{
				$return = $cookie;
			}
		}
		else
		{
			$return = FALSE;
		}

		return $return;
	}

	/**
	 * Установка значения cookie
	 *
	 * @param string $name наименование cookie
	 * @param string $value значение
	 * @param int $expire период действия
	 * @param string $path путь
	 */
	function SetCookie($name, $value, $expire, $path = '/')
	{
		// Если указано, что нужна проверка контрольной суммы
		if ($this->use_cookies_read_control)
		{
			$crc32 = Core::crc32($value);
			$value = $crc32 . ';' . $value;
		}

		$_COOKIE[$name] = $value;

		// Определим host
		$a_domain = explode(':', Core_Type_Conversion::toStr($_SERVER['HTTP_HOST']));
		$domain = Core_Type_Conversion::toStr($a_domain[0]);

		if ($this->setCookieSubdomain && !empty($domain))
		{
			// Обрезаем www у домена
			if (strpos($domain, 'www.') === 0)
			{
				$domain = substr($domain, 4);
			}

			// Явное указание domain возможно только для домена второго и боле уровня
			// http://wp.netscape.com/newsref/std/cookie_spec.html
			// http://web-notes.ru/2008/07/cookies_within_local_domains/

			if (strpos($domain, '.') !== FALSE)
			{
				setcookie($name, '', time() - 360000, $path);

				// Удалим значение для домена site.ru
				setcookie($name, '', time() - 360000, $path, $domain);

				// Удалим значение для домена www.site.ru
				setcookie($name, '', time() - 360000, $path, 'www.'.$domain);

				// Ниже установим для .site.ru
				$domain = "." . $domain;
			}
			else
			{
				$domain = '';
			}
		}

		setcookie($name, $value, $expire, $path, $domain);
	}

	/**
	 * Преобразование массива корзины при извлечении
	 *
	 * @param array $aContent содержимое корзины
	 */
	function FormatGetCart($aContent)
	{
		$tmp_array = array();

		if (is_array($aContent) && count($aContent) > 0)
		{
			foreach ($aContent as $shop_key => $shop_value)
			{
				if (is_array($shop_value))
				{
					foreach ($shop_value as $shop_items_catalog_item_id => $item_array)
					{
						if (is_array($item_array))
						{
							// shop_items_catalog_item_id не передается параметром в Cookies, т.к. является индексом
							$tmp_array[$shop_key][$shop_items_catalog_item_id]['shop_items_catalog_item_id'] = $shop_items_catalog_item_id;

							// по умолчанию значения, т.к. в результирующем массиве они обязательно должны быть
							$tmp_array[$shop_key][$shop_items_catalog_item_id]['shop_cart_flag_postpone'] = 0;
							$tmp_array[$shop_key][$shop_items_catalog_item_id]['shop_cart_item_quantity'] = 0;
							$tmp_array[$shop_key][$shop_items_catalog_item_id]['shop_warehouse_id'] = 0;

							foreach ($item_array as $key => $value)
							{
								switch ($key)
								{
									/*case 0:
									 $tmp_array[$shop_key][$shop_items_catalog_item_id]['shop_items_catalog_item_id'] = $value;
									 break;*/
									case 1 :
										$tmp_array[$shop_key][$shop_items_catalog_item_id]['shop_cart_flag_postpone'] = $value;
										break;
									case 2 :
										$tmp_array[$shop_key][$shop_items_catalog_item_id]['shop_cart_item_quantity'] = $value;
										break;
									case 3 :
										$tmp_array[$shop_key][$shop_items_catalog_item_id]['shop_warehouse_id'] = $value;
										break;
									default :
										break;
								}
							}
						}
					}
				}
			}
		}

		return $tmp_array;
	}

	/**
	 * Получить содержимое корзины в cookies
	 *
	 * @see GetCart()
	 */
	function GetCookieCart()
	{
		$sCookeContent = $this->GetCookie('CART');
		$cookie = @unserialize(Core_Type_Conversion::toStr($sCookeContent));

		$tmp_array = $this->FormatGetCart($cookie);

		return $tmp_array;
	}

	/**
	 * Получить содержимое корзины в session
	 *
	 * @see GetCart()
	 */
	function GetSessionCart()
	{
		if (isset($_SESSION['SCART']))
		{
			$session = $_SESSION['SCART'];
			$tmp_array = $this->FormatGetCart($session);
		}
		else
		{
			$tmp_array = array();
		}

		return $tmp_array;
	}

	/**
	 * Преобразование массива корзины при записи
	 *
	 * @param array $aContent содержимое корзины
	 */
	function FormatSetCart($aContent)
	{
		$tmp_array = array();

		if (is_array($aContent))
		{
			foreach ($aContent as $shop_key => $shop_value)
			{
				if (is_array($shop_value))
				{
					foreach ($shop_value as $shop_items_catalog_item_id => $item_array)
					{
						if (is_array($item_array))
						{
							foreach ($item_array as $key => $value)
							{
								switch ($key)
								{
									/*case 'shop_items_catalog_item_id':
									 $tmp_array[$shop_key][$shop_items_catalog_item_id][0] = $value;
									 break;*/
									case 'shop_cart_flag_postpone' :
										$tmp_array[$shop_key][$shop_items_catalog_item_id][1] = $value;
										break;
									case 'shop_cart_item_quantity' :
										$tmp_array[$shop_key][$shop_items_catalog_item_id][2] = $value;
										break;
									case 'shop_warehouse_id' :
										$tmp_array[$shop_key][$shop_items_catalog_item_id][3] = $value;
										break;

									default :
										break;
								}
							}
						}
					}
				}
			}
		}

		return $tmp_array;
	}

	/**
	 * Отправить в cookies содержимое корзины
	 *
	 * @param string $value значение
	 * @param int $expire период действия
	 * @param string $path путь
	 */
	function SetCookieCart($value, $expire, $path = '/')
	{
		if (count($value) == 0)
		{
			// Временную метку сбрасываем в прошлое, чтобы удалить запись
			$expire = 1;
		}
		$tmp_array = $this->FormatSetCart($value);
		$ser_value = serialize($tmp_array);

		$this->SetCookie('CART', $ser_value, $expire, $path);
	}

	/**
	 * Отправить в session содержимое корзины
	 *
	 * @param string $value значение
	 */
	function SetSessionCart($value)
	{
		$tmp_array = $this->FormatSetCart($value);

		// Стартуем сессию, если она ещё не запущена
		if (!isset($_SESSION))
		{
			@session_start();
		}

		$_SESSION['SCART'] = $tmp_array;
	}

	/**
	 * Возвращает $this->mas_groups_to_root
	 *
	 * @return array
	 */
	function GetMasGroupToRoot()
	{
		return $this->mas_groups_to_root;
	}

	/**
	 * Вставку информации о валюте
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />string $param['name'] параметр, определяющий название валюты
	 * <br />string $param['currency_international_name'] интернациональное название валюты
	 * <br />double $param['value_in_basic_currency'] значение курса валюты в базовой валюте
	 * <br />boolean $param['is_default'] параметр, определяющий, является ли валюта базовой
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['name'] = 'Фунт';
	 * $param['currency_international_name'] = 'GBP';
	 * $param['value_in_basic_currency'] = 41.78;
	 * $param['is_default'] = 0;
	 *
	 * $newid = $shop->InsertCurrency($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленной валюты (false при неудачной вставке)
	 */
	function InsertCurrency($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Currency = Core_Entity::factory('Shop_Currency', $param['id']);

		if (isset($param['name']))
		{
			$oShop_Currency->name = $param['name'];
		}

		if (isset($param['currency_international_name']))
		{
			$oShop_Currency->code = $param['currency_international_name'];
		}

		//$currency_is_default = Core_Type_Conversion::toInt($param['is_default']);

		// Устанавливаем пользователя центра администрирования только при добавлении записи
		if (is_null($oShop_Currency->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Currency->user_id = intval($param['users_id']);
		}

		if(isset($param['value_in_basic_currency']))
		{
			$currency_value_in_basic_currency = Core_Type_Conversion::toFloat($param['value_in_basic_currency']);

			/* Если вставляемая валюта является базовой, то статус всех остальных валют в таблице изменяется на "не базовая"*/
			if (isset($param['is_default']) && $param['is_default'])
			{
				$queryBuilder = Core_QueryBuilder::update('shop_currencies')
					->set('default', '0')
					->where('default', '=', '1')
					->where('deleted', '=', 0);
				$queryBuilder->execute();

				Core_DataBase::instance()->setQueryType(2)
				->query("UPDATE `shop_currencies` SET `exchange_rate`=`exchange_rate` / '{$currency_value_in_basic_currency}'");

				$oShop_Currency->exchange_rate = 1;
			}
			else
			{
				if (isset($param['is_default']))
				{
					$oShop_Currency->default = 0;
				}
				$oShop_Currency->exchange_rate = $currency_value_in_basic_currency;
			}
		}
		else
		{
			if (isset($param['is_default']))
			{
				$oShop_Currency->default = $param['is_default'] ? 1 : 0;
			}

			if (isset($param['value_in_basic_currency']))
			{
				$oShop_Currency->exchange_rate = floatval($param['value_in_basic_currency']);
			}
		}


		if (isset($param['shop_currency_order']))
		{
			$oShop_Currency->sorting = intval($param['shop_currency_order']);
		}

		if (!is_null($oShop_Currency->id))
		{
			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_CURRENCY';
				$cache->DeleteCacheItem($cache_name, $oShop_Currency->id);
			}
		}

		$oShop_Currency->save();
		return $oShop_Currency->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление информации о валюте. Является алиасом InsertCurrency()
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификатор обновляемого запроса
	 * <br />string $param['name'] параметр, определяющий название валюты
	 * <br />double $param['value_in_basic_currency'] значение курса валюты в базовой валюте
	 * <br />boolean $param['is_default'] параметр, определяющий, является ли валюта базовой
	 * @return mixed возвращает результат обновления валюты ( false при неудачном обновлении)
	 */
	function UpdateCurrency($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Currency = Core_Entity::factory('Shop_Currency', $param['id']);

		if (isset($param['name']))
		{
			$oShop_Currency->name = $param['name'];
		}

		if (isset($param['currency_international_name']))
		{
			$oShop_Currency->code = $param['currency_international_name'];
		}

		if (isset($param['value_in_basic_currency']))
		{
			$currency_value_in_basic_currency = Core_Type_Conversion::toFloat($param['value_in_basic_currency']);
			$oShop_Currency->exchange_rate = $currency_value_in_basic_currency;
		}

		if (isset($param['is_default']))
		{
			$oShop_Currency->default = $currency_is_default = $param['is_default'] ? 1 : 0;
		}

		if (isset($param['shop_currency_order']))
		{
			$oShop_Currency->sorting = intval($param['shop_currency_order']);
		}

		if (is_null($oShop_Currency->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Currency->user_id = intval($param['users_id']);
		}

		if ((isset($currency_is_default)) && $currency_is_default)
		{
			$queryBuilder = Core_QueryBuilder::update('shop_currencies')
					->set('default', '0')
					->where('default', '=', '1')
					->where('deleted', '=', 0);
			$queryBuilder->execute();

			Core_DataBase::instance()->setQueryType(2)
				->query("UPDATE `shop_currencies` SET `exchange_rate`=`exchange_rate` / '{$currency_value_in_basic_currency}'");

			$oShop_Currency->exchange_rate = 1;
		}

		if (!is_null($oShop_Currency->id))
		{
			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_CURRENCY';
				$cache->DeleteCacheItem($cache_name, $oShop_Currency->id);
			}
		}

		$oShop_Currency->save();
		return $oShop_Currency->id;
	}

	/**
	 * Получение информации о валюте
	 *
	 * @param int $shop_currency_id идентификационный номер валюты
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_currency_id = 1;
	 *
	 * $row = $shop->GetCurrency($shop_currency_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed возвращает результат выборки валюты ( false при неудачной выборке)
	 */
	function GetCurrency($shop_currency_id, $param = array())
	{
		$shop_currency_id = intval($shop_currency_id);
		$param = Core_Type_Conversion::toArray($param);

		if (isset($this->cache_currency[$shop_currency_id]))
		{
			return $this->cache_currency[$shop_currency_id];
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_CURRENCY';

			if ($in_cache = $cache->GetCacheContent($shop_currency_id, $cache_name))
			{
				$this->cache_currency[$shop_currency_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		if ($shop_currency_id > 0)
		{
			$oShop_Currency = Core_Entity::factory('Shop_Currency', $shop_currency_id);

			$row = !is_null($oShop_Currency->id)
				? $this->getArrayShopCurrency($oShop_Currency)
				: FALSE;
		}
		else
		{
			$row = FALSE;
		}

		$this->cache_currency[$shop_currency_id] = $row;

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache->Insert($shop_currency_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	 * Получение информации о валюте из базы согласно имени $string используя оператор LIKE
	 *
	 * @param string $string Строка с именем вылюты
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $string = 'руб';
	 *
	 * $row = $shop->GetCurrencyByLike($string);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed строка с данными о мере измерения или false
	 */
	function GetCurrencyByLike($string)
	{
		// Извлекаем из кэша в памяти, если есть
		if (isset($this->CacheGetCurrencyByLike[$string]))
		{
			return $this->CacheGetCurrencyByLike[$string];
		}

		$oShopCurrency = Core_Entity::factory('Shop_Currency')->getByLike($string);

		if ($oShopCurrency)
		{
			return $this->CacheGetCurrencyByLike[$string] = $this->getArrayShopCurrency($oShopCurrency);
		}

		return FALSE;
	}

	/**
	 * Формирование xml для валюты
	 *
	 * @param int $currency_id идентификатор валюты
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $currency_id = 1;
	 *
	 * $xmlData = $shop->GetCurrencyXml($currency_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return mixed xml для валюты магазина, ложь, если такой валюты не существует
	 */
	function GetCurrencyXml($currency_id)
	{
		$currency_id = intval($currency_id);

		// Выбираем информацию о валюте
		$row_currency = $this->GetCurrency($currency_id);

		// Проверяем наличие информации в базе
		if ($row_currency)
		{
			// Формируем и возвращаем информацию о валюте магазина
			$xmlData = '<currency_name>' . str_for_xml($row_currency['shop_currency_name']) . '</currency_name>' . "\n";
			$xmlData .= '<currency_international_name>'.str_for_xml($row_currency['shop_currency_international_name']).'</currency_international_name>'."\n";
			$xmlData .= '<currency_value_in_basic_currency>' . str_for_xml($row_currency['shop_currency_value_in_basic_currency']) . '</currency_value_in_basic_currency>' . "\n";
			$xmlData .= '<currency_is_default>' . str_for_xml($row_currency['shop_currency_is_default']) .  '</currency_is_default>' . "\n";
			return $xmlData;
		}

		return FALSE;
	}

	/**
	 * Получение информации обо всех валютах
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $shop->GetAllCurrency();
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed
	 */
	function GetAllCurrency()
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_currency_id'),
				array('name', 'shop_currency_name'),
				array('code', 'shop_currency_international_name'),
				array('exchange_rate', 'shop_currency_value_in_basic_currency'),
				array('default', 'shop_currency_is_default'),
				array('sorting', 'shop_currency_order'),
				array('user_id', 'users_id')
			)
			->from('shop_currencies')
			->where('deleted', '=', 0)
			->orderBy('shop_currency_order');

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление валюты
	 *
	 * @param int $shop_currency_id идентификационный номер валюты
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_currency_id = 1;
	 *
	 * $shop->DeleteCurrency($shop_currency_id);
	 *
	 * if ($result)
	 * {
	 *	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return mixed возвращает результат удаления валюты ( false при неудачном удалении)
	 */
	function DeleteCurrency($shop_currency_id)
	{
		$shop_currency_id = intval($shop_currency_id);
		$aShop_Currencies = Core_Entity::factory('Shop_Currency')->findAll();

		// Удаляем не единственную запись
		if (count($aShop_Currencies) > 1)
		{
			$oShop_Currency = Core_Entity::factory('Shop_Currency')->find($shop_currency_id);

			if (!is_null($oShop_Currency->id))
			{
				// Удаляемая валюта не является валютой по умолчанию
				if (!$oShop_Currency->default)
				{
					// Получаем валюту по умолчанию
					$oShop_Currency_Default = Core_Entity::factory('Shop_Currency')->getDefault();

					if (!is_null($oShop_Currency_Default))
					{
						Core_DataBase::instance()->setQueryType(2)->query("UPDATE `shop_items`
							SET `price`=`price` * " . $oShop_Currency->exchange_rate . ",
								`shop_currency_id`=" . $oShop_Currency_Default->id . "
							WHERE `shop_currency_id`=" . $shop_currency_id);

						// Очистка файлового кэша
						if (class_exists('Cache'))
						{
							$cache = & singleton('Cache');
							$cache_name = 'SHOP_CURRENCY';
							$cache->DeleteCacheItem($cache_name, $shop_currency_id);
						}

						$oShop_Currency->markDeleted();

						return TRUE;
					}
				}
			}
			else
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Вставка скидки для товара
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['item_catalog_id'] идентификатор товара
	 * - int $param['shop_discount_id'] идентификатор скидки
	 * - int $param['shop_item_discount_id'] идентификатор обновляемой записи, если не указан, производится добавление скидки.
	 * - int $param['users_id'] идентификатор пользователя центра адмнистрирования который добавил элемент
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['item_catalog_id'] = 167;
	 * $param['shop_discount_id'] = 2;
	 *
	 * $newid = $shop->InsertItemDiscount($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed возвращает идентификатор вставленной записи
	 */
	function InsertItemDiscount($param)
	{
		$param = Core_Type_Conversion::toArray($param);
		$item_catalog_id = Core_Type_Conversion::toInt($param['item_catalog_id']);
		$shop_discount_id = Core_Type_Conversion::toInt($param['shop_discount_id']);

		// Проверяем, была ли ранее данная скидка добавлена для данного товара
		$oShop_Item_Discount = Core_Entity::factory('Shop_Item', $item_catalog_id)
			->Shop_Item_Discounts
			->getByDiscountId($shop_discount_id);

		if (!is_null($oShop_Item_Discount))
		{
			return $oShop_Item_Discount->id;
		}

		if (!isset($param['shop_item_discount_id']) || !$param['shop_item_discount_id'])
		{
			$param['shop_item_discount_id'] = NULL;
		}

		$oShop_Item_Discount = Core_Entity::factory('Shop_Item_Discount', $param['shop_item_discount_id']);

		$oShop_Item_Discount->shop_item_id = $item_catalog_id;
		$oShop_Item_Discount->shop_discount_id = $shop_discount_id;

		if (is_null($oShop_Item_Discount->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Item_Discount->user_id = intval($param['users_id']);
		}

		$oShop_Item_Discount->save();

		if (!is_null($param['shop_item_discount_id']) && class_exists('Cache'))
		{
			// Очистка файлового кэша
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ITEM_DISCOUNT';
			$cache->DeleteCacheItem($cache_name, $oShop_Item_Discount->id);
		}

		return $oShop_Item_Discount->id;
	}

	/**
	 * Устаревший метод обновления информации о скидке для определенного товара
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_item_discount_id'] идентификатор обновляемой записи
	 * - int $param['item_catalog_id'] идентификатор товара
	 * - int $param['shop_discount_id'] идентификатор скидки
	 * @return mixed возвращает идентификатор обновленной валюты ( false при неудачном обновлении)
	 */
	function UpdateItemDiscount($param)
	{
		$this->InsertItemDiscount($param);
	}

	/**
	 * Получение информации о скидке на товар
	 *
	 * @param int $shop_item_discount_id идентификатор извлекаемой записи
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_item_discount_id = 17;
	 *
	 * $row = $shop->GetItemDiscount($shop_item_discount_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed возвращает информацию о скидке на товар, если записи с таким идентификатором нет, то false
	 */
	function GetItemDiscount($shop_item_discount_id, $param = array())
	{
		$shop_item_discount_id = intval($shop_item_discount_id);
		$param = Core_Type_Conversion::toArray($param);

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ITEM_DISCOUNT';

			if ($in_cache = $cache->GetCacheContent($shop_item_discount_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oShop_Item_Discount = Core_Entity::factory('Shop_Item_Discount')->find($shop_item_discount_id);

		$row = !is_null($oShop_Item_Discount->id)
			? $this->getArrayShopItemDiscount($oShop_Item_Discount)
			: FALSE;

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache->Insert($shop_item_discount_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	 * Удаление скики для товара
	 *
	 * @param int $shop_item_discount_id идентификатор удаляемой записи
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_item_discount_id = 1;
	 *
	 * $shop->DeleteItemDiscount($shop_item_discount_id);
	 *
	 * ?>
	 * </code>
	 * @return resource возвращает результат удаления
	 */
	function DeleteItemDiscount($shop_item_discount_id)
	{
		$shop_item_discount_id = intval($shop_item_discount_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ITEM_DISCOUNT';
			$cache->DeleteCacheItem($cache_name, $shop_item_discount_id);
		}
		Core_Entity::factory('Shop_Item_Discount', $shop_item_discount_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Устаревший метод, осуществляющий обновление информации о группе, к которой относятся товары
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификационный номер группы
	 * <br />int $param['shop_shops_id'] идентификатор магазина
	 * <br />string $param['name'] имя группы
	 * <br />string $param['parent_id'] идентификатор родителя группы (в корне - 0)
	 * <br />string $param['description'] описание группы
	 * <br />string $param['image'] путь к изображению (логотипу) группы
	 * <br />int $param['order'] порядок сортировки
	 * <br />int $param['indexation'] флаг индексации
	 * <br />string $param['path'] путь к группе
	 * <br />string $param['seo_title'] заголовок страницы
	 * <br />string $param['seo_description'] задание значения мета-тега description страницы
	 * <br />string $param['seo_keywords'] задание значения мета-тега keywords страницы
	 * <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return mixed возвращает идентификатор обновленной группы (false при неудачном обновлении)
	 */
	function UpdateGroup($param)
	{
		return $this->InsertGroup($param);
	}

	/**
	 * Получение информации о группе товаров
	 *
	 * @param int $shop_groups_id идентификационный номер группы
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_groups_id = 589;
	 *
	 * $row = $shop->GetGroup($shop_groups_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed возвращает результат выборки группы
	 */
	function GetGroup($shop_groups_id, $param = array())
	{
		$shop_groups_id = intval($shop_groups_id);

		if ($shop_groups_id > 0)
		{
			$param = Core_Type_Conversion::toArray($param);

			// Не заданы дополнительные условия для группы
			if (!isset($param['select_groups']))
			{
				// если есть в кэше памяти - вернем из него
				if (!isset($param['cache_off']) && isset($this->MasGroup[$shop_groups_id]))
				{
					return $this->MasGroup[$shop_groups_id];
				}

				$cache_name = 'SHOP_GROUP';
				if (class_exists('Cache') && !isset($param['cache_off']))
				{
					$cache = & singleton('Cache');
					if ($in_cache = $cache->GetCacheContent($shop_groups_id, $cache_name))
					{
						$this->MasGroup[$shop_groups_id] = $in_cache['value'];
						return $in_cache['value'];
					}
				}

				$oShop_Group = Core_Entity::factory('Shop_Group')->find($shop_groups_id);

				$row = !is_null($oShop_Group->id)
					? $this->getArrayShopGroup($oShop_Group)
					: FALSE;

				if (!isset($param['cache_off']))
				{
					$this->MasGroup[$shop_groups_id] = $row;
				}

				if (class_exists('Cache') && !isset($param['cache_off']))
				{
					$cache = & singleton('Cache');
					$cache->Insert($shop_groups_id, $row, $cache_name);
				}
			}
			else
			{
				$isset_items_property = FALSE;

				$queryBuilder = Core_QueryBuilder::select(
						array('id', 'shop_groups_id'),
						array('shop_id', 'shop_shops_id'),
						array('parent_id', 'shop_groups_parent_id'),
						array('name', 'shop_groups_name'),
						array('description', 'shop_groups_description'),
						array('image_large', 'shop_groups_image'),
						array('image_small', 'shop_groups_small_image'),
						array('sorting', 'shop_groups_order'),
						array('indexing', 'shop_groups_indexation'),
						array('active', 'shop_groups_activity'),
						array('siteuser_group_id', 'shop_groups_access'),
						array('path', 'shop_groups_path'),
						array('seo_title', 'shop_groups_seo_title'),
						array('seo_description', 'shop_groups_seo_description'),
						array('seo_keywords', 'shop_groups_seo_keywords'),
						array('user_id', 'users_id'),
						array('image_large_width', 'shop_groups_big_image_width'),
						array('image_large_height', 'shop_groups_big_image_height'),
						array('image_small_width', 'shop_groups_small_image_width'),
						array('image_small_height', 'shop_groups_small_image_height'),
						array('guid', 'shop_groups_cml_id'),
						array('items_count', 'count_items'),
						array('items_total_count', 'count_all_items'),
						array('subgroups_count', 'count_groups'),
						array('subgroups_total_count', 'count_all_groups')
					)
					->from('shop_groups')
					->where('shop_groups.id', '=', $shop_groups_id)
					->where('shop_groups.deleted', '=', 0);

				// формируем дополнительные условия для выборки
				if (is_array($param['select_groups']) && count($param['select_groups']) > 0)
				{
					foreach ($param['select_groups'] as $key => $value)
					{
						if ($value['type'] == 0) // основное свойство
						{
							$this->parseQueryBuilder($value['prefix'], $queryBuilder);
							$value['value'] = Core_Type_Conversion::toStr($value['value']);

							$value['name'] != '' && $value['if'] != ''
								&& $this->parseQueryBuilderWhere($value['name'], $value['if'], $value['value'], $queryBuilder);

							$this->parseQueryBuilder($value['sufix'], $queryBuilder);
						}
						else // дополнительное свойство
						{
							if (Core_Type_Conversion::toInt($value['property_id']))
							{
								$this->parseQueryBuilder($value['prefix'], $queryBuilder);
								$queryBuilder->where('shop_group_properties.property_id', '=', $value['property_id']);
								$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);
								$this->parseQueryBuilderWhere(
									$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $value['value'], $queryBuilder
								);
								$this->parseQueryBuilder($value['sufix'], $queryBuilder);
								$isset_items_property = TRUE;
							}
						}
					}

					if ($isset_items_property)
					{
						$queryBuilder
							->leftJoin('shop_item_properties', 'shop_items.shop_id', '=', 'shop_item_properties.shop_id')
							->leftJoin('property_value_ints', 'shop_items.id', '=', 'property_value_ints.entity_id',
								array(
									array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_ints.property_id')))
								)
							)
							->leftJoin('property_value_strings', 'shop_items.id', '=', 'property_value_strings.entity_id',
								array(
									array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_strings.property_id')))
								)
							)
							->leftJoin('property_value_texts', 'shop_items.id', '=', 'property_value_texts.entity_id',
								array(
									array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_texts.property_id')))
								)
							)
							->leftJoin('property_value_datetimes', 'shop_items.id', '=', 'property_value_datetimes.entity_id',
								array(
									array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_datetimes.property_id')))
								)
							)
							->leftJoin('property_value_files', 'shop_items.id', '=', 'property_value_files.entity_id',
								array(
									array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_files.property_id')))
								)
							);
					}
				}
				$row = $queryBuilder->execute()->asAssoc()->current();

				if ($row && !isset($param['cache_off']))
				{
					$this->MasGroup[$shop_groups_id] = $row;
				}
				else
				{
					$row = FALSE;
				}
			}
		}
		else
		{
			$row = FALSE;
		}

		return $row;
	}

	/**
	 * Получение информации о группе по наименованию, если такая существует в данной подгруппе магазина
	 *
	 * @param string $groups_name наименование группы
	 * @param int $groups_parent_id идентификатор родительской группы
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $param список доп. параметров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $groups_name = 'group';
	 * $groups_parent_id = 586;
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetGroupWithValue($groups_name, $groups_parent_id, $shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed информация о группе, ложь - если не существует группы с таким наименованием
	 */
	function GetGroupWithValue($groups_name, $groups_parent_id, $shop_shops_id, $param = array())
	{
		$param = Core_Type_Conversion::toArray($param);

		$oShop_Group = Core_Entity::factory('Shop_Group');

		$oShop_Group->queryBuilder()
			->where('name', '=', $groups_name)
			->where('parent_id', '=', $groups_parent_id)
			->where('shop_id', '=', $shop_shops_id)
			->limit(1);

		// Проверяем необходимость ограничивать еще и по пути для группы
		if (isset($param['group_path']))
		{
			$oShop_Group
				->queryBuilder()
				->where('path', '=', $param['group_path']);
		}

		$aShop_Groups = $oShop_Group->findAll();

		if (count($aShop_Groups) > 0)
		{
			return $this->getArrayShopGroup($aShop_Groups[0]);
		}

		return FALSE;
	}

	/**
	 * Получение информации о группе по пути, если такая существует в данной подгруппе магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $group_parent_id идентификатор родительской группы
	 * @param string $group_path путь к группе
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $group_path = 'group';
	 * $group_parent_id = 586;
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetGroupWithPath($group_path, $group_parent_id, $shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed информация о группе с таким путем в данной подгруппе для магазина, ложь, если такой группы нет
	 */
	function GetGroupWithPath($group_path, $group_parent_id, $shop_shops_id)
	{
		$oShop_Group = Core_Entity::factory('Shop_Group');

		$oShop_Group->queryBuilder()
			->where('path', '=', $group_path)
			->where('parent_id', '=', $group_parent_id)
			->where('shop_id', '=', $shop_shops_id)
			->limit(1);

		$aShop_Groups = $oShop_Group->findAll();

		if (count($aShop_Groups) > 0)
		{
			return $this->getArrayShopGroup($aShop_Groups[0]);
		}

		return FALSE;
	}

	/**
	 * Получение всех групп магазина
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param array $param массив дополнительных параметров для выборки
	 * - $param['shop_groups_parent_id'] идентификатор или массив идентификаторов родительской группы
	 * - $param['group_field_order'] поле сортировки группы
	 * - $param['group_order'] направление сортировки группы ('Asc' - по возрастанию, 'Desc' - по убыванию, 'Rand' - произвольный порядок)
	 * - $param['cache_off']  - если параметр установлен - данные не кэшируются
	 * - $param['groups_activity'] параметр, учитывающий активность групп при выборке. 1 - получаем информацию только об активных группах, если не задан, то активность группы не учитывается
	 * - $param['xml_show_group_id'] массив ID групп для выборки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $param['shop_groups_parent_id'] = 589;
	 * $param['group_order'] = 'Asc';
	 *
	 * $rows = $shop->GetAllGroups($shop_id, $param);
	 *
	 * // Распечатаем результат
	 * print_r($rows);
	 * ?>
	 * </code>
	 * @return array результат выборки
	 */
	function GetAllGroups($shop_id, $param = array())
	{
		$shop_id = intval($shop_id);

		$cache_name = 'SHOP_ALL_GROUPS';
		$cache_key = $shop_id . '_' . serialize($param);
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($cache_key, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_groups_id'),
				array('shop_id', 'shop_shops_id'),
				array('parent_id', 'shop_groups_parent_id'),
				array('name', 'shop_groups_name'),
				array('description', 'shop_groups_description'),
				array('image_large', 'shop_groups_image'),
				array('image_small', 'shop_groups_small_image'),
				array('sorting', 'shop_groups_order'),
				array('indexing', 'shop_groups_indexation'),
				array('active', 'shop_groups_activity'),
				array('siteuser_group_id', 'shop_groups_access'),
				array('path', 'shop_groups_path'),
				array('seo_title', 'shop_groups_seo_title'),
				array('seo_description', 'shop_groups_seo_description'),
				array('seo_keywords', 'shop_groups_seo_keywords'),
				array('user_id', 'users_id'),
				array('image_large_width', 'shop_groups_big_image_width'),
				array('image_large_height', 'shop_groups_big_image_height'),
				array('image_small_width', 'shop_groups_small_image_width'),
				array('image_small_height', 'shop_groups_small_image_height'),
				array('guid', 'shop_groups_cml_id'),
				array('items_count', 'count_items'),
				array('items_total_count', 'count_all_items'),
				array('subgroups_count', 'count_groups'),
				array('subgroups_total_count', 'count_all_groups')
			)
			->from('shop_groups')
			->where('shop_groups.shop_id', '=', $shop_id)
			->where('shop_groups.deleted', '=', 0);

		// Проверяем наличие переданной родительской группы
		if (isset($param['shop_groups_parent_id']))
		{
			if (is_array($param['shop_groups_parent_id'])
			&& count($param['shop_groups_parent_id']) > 0)
			{
				$param['shop_groups_parent_id'] = Core_Array::toInt($param['shop_groups_parent_id']);
				$queryBuilder->where('parent_id', 'IN', $param['shop_groups_parent_id']);
			}
			else
			{
				$queryBuilder->where('parent_id', '=', intval($param['shop_groups_parent_id']));
			}
		}

		if (isset($param['groups_activity']) && $param['groups_activity'] == 1)
		{
			$queryBuilder->where('shop_groups.active', '=', 1);
		}

		if (isset($param['xml_show_group_id']) && is_array($param['xml_show_group_id'])
		&& count($param['xml_show_group_id']) > 0)
		{
			$param['xml_show_group_id'] = Core_Array::toInt($param['xml_show_group_id']);
			$queryBuilder->where('shop_groups.id', 'IN', $param['xml_show_group_id']);
		}

		if (isset($param['group_order']))
		{
			$group_order = strtoupper(strval($param['group_order']));

			if ($group_order == 'RAND')
			{
				$group_order = 'RAND()';
				$param['group_field_order'] = '';
			}
		}
		else
		{
			$shop_row = $this->GetShop($shop_id);

			if ($shop_row)
			{
				switch ($shop_row['shop_group_sort_order_type'])
				{
					case 0 :
						$group_order = 'ASC';
					break;
					default :
						$group_order = 'DESC';
					break;
				}
			}
			else
			{
				$group_order = 'ASC';
			}
		}

		if (isset($param['group_field_order']))
		{
			$aGroupFieldOrder = explode(',', $param['group_field_order']);
			foreach ($aGroupFieldOrder as $sGroupFieldOrder)
			{
				$queryBuilder->orderBy(trim(strval($sGroupFieldOrder)), $group_order);
			}
		}
		else
		{
			// Если поле сортирвоки не передан - берем из полей магазина
			$shop_row = $this->GetShop($shop_id);

			if ($shop_row)
			{
				switch ($shop_row['shop_group_sort_order_field'])
				{
					case 0 :
						$queryBuilder->orderBy('shop_groups_name', $group_order);
						break;
					default :
						$queryBuilder
							->orderBy('shop_groups_order', $group_order)
							->orderBy('shop_groups_name');
				}
			}
			else
			{
				$queryBuilder
					->orderBy('shop_groups_order', $group_order)
					->orderBy('shop_groups_name');
			}
		}

		if (isset($param['select_groups']))
		{
			// формируем дополнительные условия для выборки
			if (is_array($param['select_groups']) && count($param['select_groups']) > 0)
			{
				foreach ($param['select_groups'] as $key => $value)
				{
					if ($value['type'] == 0) // основное свойство
					{
						$this->parseQueryBuilder($value['prefix'], $queryBuilder);

						$value['name'] != '' && $value['if'] != ''
							&& $this->parseQueryBuilderWhere($value['name'], $value['if'], strval($value['value']), $queryBuilder);

						$this->parseQueryBuilder($value['sufix'], $queryBuilder);
					}
					else // дополнительное свойство
					{
						if (Core_Type_Conversion::toInt($value['property_id']) != 0)
						{
							$this->parseQueryBuilder($value['prefix'], $queryBuilder);
							$queryBuilder->where('shop_group_properties.property_id', '=', $value['property_id']);
							$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);

							$this->parseQueryBuilderWhere(
								$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $value['value'], $queryBuilder
							);

							$this->parseQueryBuilder($value['sufix'], $queryBuilder);
						}
					}
				}
			}
		}

		$aResult = $queryBuilder->execute()->asAssoc()->result();

		count($aResult) == 0 && $aResult = FALSE;

        if (class_exists('Cache') && !isset($param['cache_off']))
        {
        	$cache->Insert($cache_key, $aResult, $cache_name);
        }

		return $aResult;
	}

	/**
	 * Удаление группы товаров (с подгруппами и товарами)
	 *
	 * @param int $shop_groups_id идентификтаор группы товаров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_groups_id = 1;
	 *
	 * $result = $shop->DeleteGroup($shop_groups_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return boolean истину при удачном удалении, ложь - в обратном случае
	 */
	function DeleteGroup($shop_groups_id)
	{
		Core_Entity::factory('Shop_Group', $shop_groups_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Вставка информации о товаре. Может принимать только часть параметров, при этом обновлены будут только переданные значения
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shops_id'] идентификационный номер магазина, обязательный параметр
	 * - int $param['item_id'] идентификационнный номер товара, указывается при обновлении информации о товаре
	 * - int $param['groups_id'] идентификатор группы, в которой расположен товар
	 * - int $param['shop_items_catalog_shortcut_id'] идентификатор товара, на который ссылается ярлык. По умолчанию равен 0, если не является ярлыком.
	 * - string $param['name'] наименование товара
	 * - string $param['marking'] артикул товара
	 * - string $param['description'] краткое описание товара
	 * - int $param['shop_items_catalog_type'] тип товара, 0 - обычный товар, 1 - электронный товар. по умолчанию 0.
	 * - string $param['shop_items_catalog_date_time'] датавремя добавления, в формате MySQL
	 * - string $param['shop_items_catalog_putoff_date'] датавремя начала публикации товара в каталоге, в формате MySQL. Необязательное поле.
	 * - string $param['shop_items_catalog_putend_date'] датавремя окончания публикации товара в каталоге, в формате MySQL. Необязательное поле.
	 * - string $param['text'] детальное описание товара
	 * - int $param['currency_id'] идентификатор валюты
	 * - double $param['weight'] вес товара
	 * - double $param['price'] цена товара для пользователя
	 * - int $param['tax_id'] идентификатор налога
	 * - int $param['is_active'] флаг доступности товара
	 * - int $param['shop_items_catalog_access'] параметр, определяющий группу пользователей, имеющих доступ к товару (0 - доступна всем, -1 - доступ как у родителя)
	 * - int $param['order'] порядок сортировки товара
	 * - int $param['indexation'] флаг индексации товара
	 * - string $param['path'] путь к товару
	 * - string $param['seo_title'] заголовок страницы товара
	 * - string $param['seo_description'] задание значения мета-тега description страницы товара
	 * - string $param['seo_keywords'] задание значения мета-тега keywords страницы товара
	 * - string $param['shop_items_cml_id'] идентификатор товара в CommerceML
	 * - string $param['shop_vendorcode'] код производителя для Яндекс.Маркет
	 * - bool $param['shop_items_catalog_yandex_market_allow'] разрешить экспорт в Яндекс.Маркет
	 * - int $param['shop_items_catalog_yandex_market_bid'] значение BID для экспорта в Яндекс.Маркет
	 * - int $param['shop_items_catalog_yandex_market_cid'] значение CID для экспорта в Яндекс.Маркет
	 * - string $param['shop_items_catalog_yandex_market_sales_notes'] описание особенностей заказа при экспорте в Яндекс.Маркет
	 * - bool $param['shop_items_catalog_rambler_pokupki_allow'] разрешить экспорт в Рамблер.Покупки
	 * - int $param['shop_sallers_id'] значение идентификатора продавца товара
	 * - int $param['producers_list_id'] идентификатор производителя
	 * - int $param['shop_items_catalog_modification_id'] идентификатор основного товара для модификации, указывается если данный товар является модификацией
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	 * - int $param['site_users_id'] идентификатор пользователя сайта, добавившего товар
	 * - int $param['mesures_id'] идентификатор единицы измерения
	 * - string $param['path_source_big_image'] путь к файлу-источнику большого изображения;
	 * - string $param['path_source_small_image'] путь к файлу-источнику малого изображения;
	 * - int $param['shop_items_catalog_image'] имя большого изображения
	 * - int $param['shop_items_catalog_small_image'] имя малого изображения
	 * - int $param['shop_items_catalog_small_image_height'] высота малого изображения
	 * - int $param['shop_items_catalog_small_image_width'] ширина малого изображения
	 * - int $param['shop_items_catalog_big_image_height'] высота большого изображения
	 * - int $param['shop_items_catalog_big_image_width'] ширина большого изображения
	 * - string $param['original_file_name_big_image'] оригинальное имя файла большого изображения
	 * - string $param['original_file_name_small_image'] оригинальное имя файла малого изображения
	 * - bool $param['use_big_image'] использовать большое изображение для создания малого (true - использовать, false - не использовать)
	 * - int $param['max_width_big_image'] значение максимальной ширины большого изображения
	 * - int $param['max_height_big_image'] значение максимальной высоты большого изображения
	 * - int $param['max_width_small_image'] значение максимальной ширины малого изображения;
	 * - int $param['max_height_small_image'] значение максимальной высоты малого изображения;
	 * - string $param['watermark_file_path'] путь к файлу с "водяным знаком"
	 * - int $param['watermark_position_x'] позиция "водяного знака" по оси X
	 * - int $param['watermark_position_y'] позиция "водяного знака" по оси Y
	 * - bool $param['used_watermark_big_image'] наложить "водяной знак" на большое изображение (true - наложить (по умолчанию), false - не наложить);
	 * - bool $param['used_watermark_small_image'] наложить "водяной знак" на малое изображение (true - наложить (по умолчанию), false - не наложить);
	 * - int $param['shop_items_catalog_show_count'] счетчик просмотра товара
	 * - bool $param['search_event_indexation'] использовать ли событийную индексацию при вставке элемента, по умолчанию true
	 *
	 * @return int идентификационный номер вставленного товара, false или -1
	 */
	function InsertItem($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['item_id']) || !$param['item_id'])
		{
			$param['item_id'] = NULL;
		}

		// Если добавление нового товара
		if (is_null($param['item_id']))
		{
			$shop_shops_id = Core_Type_Conversion::toInt($param['shops_id']);
		}
		elseif (!isset($param['shops_id']))
		{
			$item_row = $this->GetItem($param['item_id'], array('cache_off' => true));

			if ($item_row)
			{
				$shop_shops_id = $item_row['shop_shops_id'];
			}
		}
		else
		{
			$shop_shops_id = Core_Type_Conversion::toInt($param['shops_id']);
		}

		$shop_row = $this->GetShop($shop_shops_id);

		if (!$shop_row)
		{
			return FALSE;
		}

		$oShop_Item = Core_Entity::factory('Shop_Item', $param['item_id']);

		if (isset($param['name']))
		{
			$item_name = mb_substr(trim($param['name']), 0, 255);
			$oShop_Item->name = $item_name;
		}
		else
		{
			$item_name = '';
		}

		if (!isset($param['search_event_indexation']))
		{
			$param['search_event_indexation'] = TRUE;
		}

		if (isset($param['site_users_id']))
		{
			$oShop_Item->siteuser_id = intval($param['site_users_id']);
		}

		if (isset($param['marking']))
		{
			$oShop_Item->marking = $param['marking'];
		}

		if (isset($param['shop_items_catalog_putoff_date']) && $param['shop_items_catalog_putoff_date'] != '')
		{
			$oShop_Item->start_datetime = $param['shop_items_catalog_putoff_date'];
		}

		if (isset($param['shop_items_catalog_putend_date']) && $param['shop_items_catalog_putend_date'] != '')
		{
			$oShop_Item->end_datetime = $param['shop_items_catalog_putend_date'];
		}
		elseif (isset($param['shop_items_catalog_putend_date']) && $param['shop_items_catalog_putend_date'] == '')
		{
			$oShop_Item->end_datetime = '0000-00-00 00:00:00';
		}

		if (isset($param['shop_items_cml_id']))
		{
			$oShop_Item->guid = $param['shop_items_cml_id'];
		}
		// Если идет добавление нового товара, необходимо сгенерировать CML_ID
		elseif(!Core_Type_Conversion::toInt($param['item_id']))
		{
			$oShop_Item->guid = Core_Guid::get();
		}

		if (isset($param['shop_items_catalog_date_time']))
		{
			// Для MySQL 5.x строгое условие на соответствие даты
			if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $param['shop_items_catalog_date_time']))
			{
				$oShop_Item->datetime = $param['shop_items_catalog_date_time'];
			}
		}

		// Экспорт в Рамблер.Покупки ставим всегда, если не передано
		if (isset($param['shop_items_catalog_rambler_pokupki_allow']))
		{
			//$oShop_Item->rambler_pokupki = $param['shop_items_catalog_rambler_pokupki_allow'];
		}
		elseif (!Core_Type_Conversion::toInt($param['item_id']))
		{
			//$oShop_Item->rambler_pokupki = 1;
		}

		if (isset($param['description']))
		{
			$oShop_Item->description = $param['description'];
		}

		if (isset($param['tax_id']))
		{
			$oShop_Item->shop_tax_id = $param['tax_id'];
		}

		if (isset($param['shop_items_catalog_type']))
		{
			$oShop_Item->type = $param['shop_items_catalog_type'];
		}

		if (isset($param['shop_items_catalog_small_image_height']))
		{
			$oShop_Item->image_small_height = intval($param['shop_items_catalog_small_image_height']);
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->image_small_height = 0;
		}

		if (isset($param['shop_items_catalog_small_image_width']))
		{
			$oShop_Item->image_small_width = $param['shop_items_catalog_small_image_width'];
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->image_small_width = 0;
		}

		if (isset($param['shop_items_catalog_big_image_height']))
		{
			$oShop_Item->image_large_height = intval($param['shop_items_catalog_big_image_height']);
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->image_large_height = 0;
		}

		if (isset($param['shop_items_catalog_big_image_width']))
		{
			$oShop_Item->image_large_width = intval($param['shop_items_catalog_big_image_width']);
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->image_large_width = 0;
		}

		if (isset($param['shop_items_catalog_modification_id'])
		// Убрано для переноса из модификации в обычный товар
		/* && $param['shop_items_catalog_modification_id'] */)
		{
			$oShop_Item->modification_id = $param['shop_items_catalog_modification_id'];

			if($param['shop_items_catalog_modification_id'])
			{
				// Если вставка модификации - группа в 0
				$oShop_Item->shop_group_id = 0;
			}
		}

		if (isset($param['groups_id']))
		{
			$oShop_Item->shop_group_id = $param['groups_id'];
		}

		if (isset($param['shop_items_catalog_shortcut_id']))
		{
			$oShop_Item->shortcut_id = intval($param['shop_items_catalog_shortcut_id']);
		}

		if (isset($param['shop_vendorcode']))
		{
			$oShop_Item->vendorcode = $param['shop_vendorcode'];
		}

		if (isset($param['shop_items_catalog_show_count']))
		{
			$oShop_Item->showed = intval($param['shop_items_catalog_show_count']);
		}

		if (isset($param['shops_id']))
		{
			$oShop_Item->shop_id = intval($param['shops_id']);
		}

		if (isset($param['currency_id']))
		{
			$oShop_Item->shop_currency_id = intval($param['currency_id']);
		}

		if (isset($param['producers_list_id']))
		{
			$oShop_Item->shop_producer_id = intval($param['producers_list_id']);
		}

		if (isset($param['mesures_id']))
		{
			$oShop_Item->shop_measure_id = intval($param['mesures_id']);
		}

		if (isset($param['text']))
		{
			$oShop_Item->text = $param['text'];
		}

		// Экспорт в Яндекс.Маркет устанавливаем всегда
		if (isset($param['shop_items_catalog_yandex_market_allow']))
		{
			$oShop_Item->yandex_market = $param['shop_items_catalog_yandex_market_allow'];
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->yandex_market = 1;
		}

		if (isset($param['shop_items_catalog_yandex_market_bid']))
		{
			$oShop_Item->yandex_market_bid = $param['shop_items_catalog_yandex_market_bid'];
		}

		if (isset($param['shop_items_catalog_yandex_market_cid']))
		{
			$oShop_Item->yandex_market_cid = $param['shop_items_catalog_yandex_market_cid'];
		}

		if (isset($param['shop_items_catalog_yandex_market_sales_notes']))
		{
			$oShop_Item->yandex_market_sales_notes = $param['shop_items_catalog_yandex_market_sales_notes'];
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->yandex_market_sales_notes = '';
		}

		if (isset($param['weight']))
		{
			$oShop_Item->weight = floatval($param['weight']);
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->weight = 0.00;
		}

		if (isset($param['price']))
		{
			$oShop_Item->price = floatval($param['price']);
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->price = 0.00;
		}

		if (isset($param['seo_title']))
		{
			$oShop_Item->seo_title = $param['seo_title'];
		}

		if (isset($param['seo_description']))
		{
			$oShop_Item->seo_description = $param['seo_description'];
		}

		if (isset($param['seo_keywords']))
		{
			$oShop_Item->seo_keywords = $param['seo_keywords'];
		}

		if (isset($param['shop_sallers_id']))
		{
			$oShop_Item->shop_seller_id = intval($param['shop_sallers_id']);
		}

		if (isset($param['order']))
		{
			$oShop_Item->sorting = intval($param['order']);
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->sorting = 0;
		}

		$shop_groups_id = Core_Type_Conversion::toInt($param['groups_id']);

		// Путь к товару.
		// Автоматически обрабатываем только, если явно передан путь (может быть пустым) или идет создание товара
		if (isset($param['path']) || is_null($oShop_Item->id))
		{
			$item_path = Core_Type_Conversion::toStr($param['path']);

			// Оставляем только разрешенные символы.
			$item_path = $this->ClearPath($item_path);

			if ($shop_row)
			{
				if (trim($item_path) == '' && $shop_row['shop_shops_url_type'] == 1)
				{
					$item_path = Core_Str::transliteration($item_name);
				}
			}

			$queryBuilder = Core_QueryBuilder::select()
				->from('shop_items')
				->where('shop_items.path', '=', $item_path)
				->where('shop_items.shop_group_id', '=', $shop_groups_id)
				->where('shop_items.modification_id', '=', 0)
				->where('shop_items.deleted', '=', 0);

			// Проверяем наличие товара с таким же путем.
			if (!is_null($oShop_Item->id))
			{
				$queryBuilder->where('id', '!=', $oShop_Item->id);
			}

			$iCountShopItems = $queryBuilder->execute()->getNumRows();

			// Уже существует товар в данной группе - с таким же путем.
			if ($iCountShopItems > 0)
			{
				// Путь заменяем на пустоту - далее он будет изменен на путь по умолчанию.
				$item_path = '';
			}

			$oShop_Item->path = $item_path;
		}

		// Активность по умолчанию включена.
		if (isset($param['is_active']))
		{
			$oShop_Item->active = intval($param['is_active']);
		}

		// Группа пользователей, имеющих доступ к товару
		if (isset($param['shop_items_catalog_access']))
		{
			$oShop_Item->siteuser_group_id = intval($param['shop_items_catalog_access']);
		}

		// Индексация по умолчанию включена.
		if (isset($param['indexation']))
		{
			$oShop_Item->indexing = intval($param['indexation']);
		}
		elseif (is_null($oShop_Item->id))
		{
			$oShop_Item->indexing = 1;
		}

		if (is_null($oShop_Item->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Item->user_id = intval($param['users_id']);
		}

		if (!is_null($oShop_Item->id))
		{
			// Удаляем информацию о проиндексированном товаре
			// до обновления, т.к. при изменении группы элементы мы не сможем удалить
			// его предыдущие данные
			if ($param['search_event_indexation'] && class_exists('Search'))
			{
				$this->IndexationShopItems(0, 1, $oShop_Item->id);
			}

			// Перед обновлением получаем информацию о старом изображении
			$temp = $this->GetItem($oShop_Item->id, array('cache_off' => TRUE));
			$item_old_image_big = $temp['shop_items_catalog_image'];
			$item_old_image_small = $temp['shop_items_catalog_small_image'];
		}

		$oShop_Item->save();

		// Если был передан пустой путь - устанавливаем путь - item_ + идентификатор.
		if (isset($item_path) && $item_path == '')
		{
			$prefix = defined('SHOP_ITEM_PATH_PREFIX')
				? SHOP_ITEM_PATH_PREFIX
				: 'item_';

			$oShop_Item->path = $prefix . $oShop_Item->id;
			$oShop_Item->save();
		}

		// Обрабатываем изображения для товара
		$param_file_save = $param;
		if (isset($param['path_source_big_image']))
		{
			$param_file_save['path_source_big_image'] = $param['path_source_big_image'];
		}
		else
		{
			$param_file_save['path_source_big_image'] = '';
		}

		if (isset($param['path_source_small_image']))
		{
			$param_file_save['path_source_small_image'] = $param['path_source_small_image'];
		}
		else
		{
			$param_file_save['path_source_small_image'] = '';
		}

		$item_info = $this->GetItem($oShop_Item->id, array('cache_off' => TRUE));

		$uploaddir = $this->GetItemDir($oShop_Item->id);

		// Создаем директорию для хранения файлов
		Core_File::mkdir($uploaddir, CHMOD, TRUE);

		// Формируем путь по новому стандарту
		$dir_item_path = CMS_FOLDER . $uploaddir;

		$param_file_save['original_file_name_big_image'] = Core_Type_Conversion::toStr($param['original_file_name_big_image']);
		$param_file_save['original_file_name_small_image'] = Core_Type_Conversion::toStr($param['original_file_name_small_image']);

		if ($param_file_save['path_source_big_image'] != '')
		{
			// Преобразовываем название файла
			if ($shop_row['shop_shops_file_name_conversion'] == 1)
			{
				$ext = Core_File::getExtension($param_file_save['original_file_name_big_image']);

				if ($ext != '')
				{
					$ext = '.' . $ext;
				}

				// Получаем информацию о редактируемом товаре
				$item_big_image = $dir_item_path . '/shop_items_catalog_image' . $oShop_Item->id . $ext;
			}
			else // Оставляем оригинальное имя файла
			{
				$item_big_image = $dir_item_path . '/' . $param_file_save['original_file_name_big_image'];
			}
		}
		else
		{
			$item_big_image = '';
		}

		$available_extensions = array('JPG','JPEG','PNG','GIF');

		if ($param_file_save['path_source_small_image'] != '' )
		{
			// Не задан файл большого изображения и задан файл малого
			if (!empty($param_file_save['path_source_small_image']))
			{
				// Редактирование товара
				if (Core_Type_Conversion::toInt($param['item_id']) > 0)
				{
					$create_big_image = empty ($item_old_image_big);
				}
				else
				{
					$create_big_image = empty ($item_small_image);
				}
			}
			else
			{
				$create_big_image = FALSE;
			}

			$param_file_save['isset_big_image'] = !$create_big_image;

			$ext = Core_File::getExtension($param_file_save['original_file_name_small_image']);

			if ($create_big_image)
			{
				if (in_array(mb_strtoupper($ext), $available_extensions))
				{
					if ($ext != '')
					{
						$ext = '.' . $ext;
					}
					if ($shop_row['shop_shops_file_name_conversion'] == 1)
					{
						$item_big_image = $dir_item_path . '/shop_items_catalog_image' . $oShop_Item->id . $ext;
						$item_small_image = $dir_item_path . '/small_shop_items_catalog_image' . $oShop_Item->id . $ext;
					}
					else
					{
						$item_big_image = $dir_item_path . '/' . $param_file_save['original_file_name_small_image'];
						$item_small_image = $dir_item_path . '/small_' . $param_file_save['original_file_name_small_image'];
					}

					$param_file_save['original_file_name_big_image'] = $param_file_save['original_file_name_small_image'];
				}
				else
				{
					$item_big_image = '';

					if ($ext != '')
					{
						$ext = '.' . $ext;
					}

					if ($shop_row['shop_shops_file_name_conversion'] == 1)
					{
						$item_small_image = $dir_item_path . '/small_shop_items_catalog_image' . $oShop_Item->id . $ext;
					}
					else
					{
						$item_small_image = $dir_item_path . '/' . $param_file_save['original_file_name_small_image'];
					}
				}
			}
			else
			{
				if ($ext != '')
				{
					$ext = '.' . $ext;
				}

				if ($shop_row['shop_shops_file_name_conversion'] == 1)
				{
					$item_small_image = $dir_item_path . '/small_shop_items_catalog_image' . $oShop_Item->id . $ext;
				}
				else
				{
					$item_small_image = $dir_item_path . '/small_' . $param_file_save['original_file_name_small_image'];
				}
			}
		}
		else // Не задано малое изображение
		{
			// Создаем малое изображение из большого
			if (isset($param['use_big_image']) && $param_file_save['path_source_big_image'] != '')
			{
				$param_file_save['use_big_image'] = $param['use_big_image'];

				if ($shop_row['shop_shops_file_name_conversion'] == 1)
				{
					$item_small_image = $dir_item_path . '/small_shop_items_catalog_image' . $oShop_Item->id . $ext;
				}
				else
				{
					$item_small_image = $dir_item_path . '/small_' . $param_file_save['original_file_name_big_image'];
				}
			}
			else
			{
				$item_small_image = '';
			}
		}

		if (!empty ($item_big_image) || !empty ($item_small_image))
		{
			if (!file_exists($dir_item_path))
			{
				@ mkdir($dir_item_path, CHMOD);
				@ chmod($dir_item_path, CHMOD);
			}
		}

		$param_file_save['path_target_big_image'] = $item_big_image;
		$param_file_save['path_target_small_image'] = $item_small_image;

		// Вызываем метод загрузки изображений с определенными параметрами
		$kernel = & singleton('kernel');
		$lf_result = $kernel->AdminLoadFiles($param_file_save);

		$image = new Image();

		if ($lf_result['big_image'])
		{
			// Большое изображение успешно загружено, нужно обновить информацию о нем в БД
			$height = 0;
			$width = 0;

			// Обрабатываем размеры изображения
			if (is_file($param_file_save['path_target_big_image'])
			&& is_readable($param_file_save['path_target_big_image'])
			&& filesize($param_file_save['path_target_big_image']) > 12)
			{
				if (Core_Image::instance()->exifImagetype($param_file_save['path_target_big_image']))
				{
					$arr_of_image_sizes = $image->GetImageSize($param_file_save['path_target_big_image']);
					$height = $arr_of_image_sizes['height'];
					$width = $arr_of_image_sizes['width'];
				}
			}

			$temp = basename($param_file_save['path_target_big_image']);

			// Редактирование информации о товаре и новый файл имеет иное расширение
			if (!is_null($oShop_Item->id) && $temp != Core_Type_Conversion::toStr($item_info['shop_items_catalog_image']))
			{
				// Удаляем преждний файл
				if (is_file($dir_item_path . '/' . $item_info['shop_items_catalog_image']))
				{
					@ unlink($dir_item_path . '/' . $item_info['shop_items_catalog_image']);
				}
			}

			$oShop_Item->image_large = $temp;
			$oShop_Item->image_large_height = $height;
			$oShop_Item->image_large_width = $width;
			$oShop_Item->save();
		}

		if ($lf_result['small_image'])
		{
			$height = 0;
			$width = 0;

			// Обрабатываем размеры изображения
			if (is_file($param_file_save['path_target_small_image'])
			&& is_readable($param_file_save['path_target_small_image'])
			&& filesize($param_file_save['path_target_small_image']) > 12)
			{
				if (Core_Image::instance()->exifImagetype($param_file_save['path_target_small_image']))
				{
					$arr_of_image_sizes = $image->GetImageSize($param_file_save['path_target_small_image']);
					$height = $arr_of_image_sizes['height'];
					$width = $arr_of_image_sizes['width'];
				}
			}

			$temp = basename($param_file_save['path_target_small_image']);

			// Редактирование информации о товаре и новый файл малого изображения имеет иное расширение
			if (!is_null($oShop_Item->id) && $temp != Core_Type_Conversion::toStr($item_info['shop_items_catalog_small_image']))
			{
				// Удаляем преждний файл
				if (is_file($dir_item_path . '/' . $item_info['shop_items_catalog_small_image']))
				{
					@ unlink($dir_item_path . '/' . $item_info['shop_items_catalog_small_image']);
				}
			}

			$oShop_Item->image_small = $temp;
			$oShop_Item->image_small_height = $height;
			$oShop_Item->image_small_width = $width;
			$oShop_Item->save();
		}

		// Добавляем индексирование информационного элемента
		if ($param['search_event_indexation'] && class_exists('Search'))
		{
			$this->IndexationShopItems(0, 1, $oShop_Item->id);
		}

		// Очищаем кэш в памяти
		if (isset($this->CacheGetItem[$oShop_Item->id]))
		{
			unset($this->CacheGetItem[$oShop_Item->id]);
		}

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ITEM';
			$cache->DeleteCacheItem($cache_name, $oShop_Item->id);
		}

		return $oShop_Item->id;
	}

	/**
	 * Обновление информации об изображении для товара
	 *
	 * @param string $item_image наименование файла изображения для товара
	 * @return boolean истина в случае удачного обновления, ложь в обратном случае
	 */
	function UpdateImageForItem($shop_id, $shop_items_catalog_id, $shop_items_catalog_image, $shop_items_catalog_image_small = FALSE)
	{
		$shop_items_catalog_id = intval($shop_items_catalog_id);

		$oShop_Item = Core_Entity::factory('Shop_Item')->find($shop_items_catalog_id);

		if (is_null($oShop_Item->id))
		{
			return FALSE;
		}
		$shop_items_catalog_image = trim($shop_items_catalog_image);
		if ($shop_items_catalog_image_small === FALSE)
		{
			$shop_items_catalog_image_small = 'small_' . $shop_items_catalog_image;
		}
		else
		{
			$shop_items_catalog_image_small = trim($shop_items_catalog_image_small);
		}

		$Image = new Image();

		// Создаем директорию для хранения файла
		$uploaddir = $this->GetItemDir($shop_items_catalog_id);

		$kernel = & singleton('kernel');
		$kernel->PathMkdir($uploaddir);

		if (!$uploaddir)
		{
			// Ставим путь по умолчанию
			$uploaddir = CMS_FOLDER . UPLOADDIR;
		}

		if (is_file($uploaddir . $shop_items_catalog_image_small))
		{
			// Определяем размеры изображения для маленькой картинки
			$size = $Image->GetImageSize($uploaddir . $shop_items_catalog_image_small);
			$items_catalog_small_image_height = $size['height'];
			$items_catalog_small_image_width = $size['width'];

			if ($items_catalog_small_image_height == '')
			{
				$items_catalog_small_image_height = 0;
			}

			if ($items_catalog_small_image_width == '')
			{
				$items_catalog_small_image_width = 0;
			}

			$oShop_Item->image_small = $shop_items_catalog_image_small;
			$oShop_Item->image_small_height = $items_catalog_small_image_height;
			$oShop_Item->image_small_width = $items_catalog_small_image_width;
		}

		if ($shop_items_catalog_image != '' && is_file($uploaddir . $shop_items_catalog_image))
		{
			// Определяем размеры изображения для большой картинки
			$size = $Image->GetImageSize($uploaddir . $shop_items_catalog_image);

			$items_catalog_big_image_height = $size['height'];
			$items_catalog_big_image_width = $size['width'];

			if ($items_catalog_big_image_height == '')
			{
				$items_catalog_big_image_height = 0;
			}

			if ($items_catalog_big_image_width == '')
			{
				$items_catalog_big_image_width = 0;
			}

			$oShop_Item->image_large = $shop_items_catalog_image;
			$oShop_Item->image_large_height = $items_catalog_big_image_height;
			$oShop_Item->image_large_width = $items_catalog_big_image_width;
		}

		$oShop_Item->save();

		$row_item = $this->GetItem($shop_items_catalog_id, array('cache_off' => TRUE));

		// Записываем в кэш новые размеры
		if (isset($this->CacheGetItem[$shop_items_catalog_id]))
		{
			$this->CacheGetItem[$shop_items_catalog_id]['shop_items_catalog_small_image_height'] = $row_item['shop_items_catalog_small_image_height'];
			$this->CacheGetItem[$shop_items_catalog_id]['shop_items_catalog_small_image_width'] = $row_item['shop_items_catalog_small_image_width'];
			$this->CacheGetItem[$shop_items_catalog_id]['shop_items_catalog_big_image_height'] = $row_item['shop_items_catalog_big_image_height'];
			$this->CacheGetItem[$shop_items_catalog_id]['shop_items_catalog_big_image_width'] = $row_item['shop_items_catalog_big_image_width'];
		}

		return TRUE;
	}

	/**
	 * Устаревший метод, осуществляющий обновление информации о товаре. Заменен на InsertItem()
	 *
	 * @see InsertItem()
	 */
	function UpdateItem($param)
	{
		$accepted_param = array();

		if(isset($param['shop_items_catalog_yandex_market_sales_notes']))
		{
			$accepted_param['shop_items_catalog_yandex_market_sales_notes'] = $param['shop_items_catalog_yandex_market_sales_notes'];
		}

		if(isset($param['shop_items_catalog_yandex_market_cid']))
		{
			$accepted_param['shop_items_catalog_yandex_market_cid'] = $param['shop_items_catalog_yandex_market_cid'];
		}

		if(isset($param['shop_items_catalog_yandex_market_bid']))
		{
			$accepted_param['shop_items_catalog_yandex_market_bid'] = $param['shop_items_catalog_yandex_market_bid'];
		}

		if(isset($param['shop_items_catalog_yandex_market_allow']))
		{
			$accepted_param['shop_items_catalog_yandex_market_allow'] = $param['shop_items_catalog_yandex_market_allow'];
		}

		if(isset($param['shop_mesures_id']))
		{
			$accepted_param['mesures_id'] = $param['shop_mesures_id'];
		}

		if(isset($param['shop_producers_list_id']))
		{
			$accepted_param['producers_list_id'] = $param['shop_producers_list_id'];
		}

		if(isset($param['shop_vendorcode']))
		{
			$accepted_param['shop_vendorcode'] = $param['shop_vendorcode'];
		}

		if(isset($param['shop_items_catalog_small_image_height']))
		{
			$accepted_param['shop_items_catalog_small_image_height'] = $param['shop_items_catalog_small_image_height'];
		}

		if(isset($param['shop_tax_id']))
		{
			$accepted_param['tax_id'] = $param['shop_tax_id'];
		}

		if(isset($param['shop_items_catalog_rambler_pokupki_allow']))
		{
			$accepted_param['shop_items_catalog_rambler_pokupki_allow'] = $param['shop_items_catalog_rambler_pokupki_allow'];
		}

		if(isset($param['shop_items_cml_id']))
		{
			$accepted_param['shop_items_cml_id'] = $param['shop_items_cml_id'];
		}

		if(isset($param['shop_shops_id']))
		{
			$accepted_param['shops_id'] = $param['shop_shops_id'];
		}

		if(isset($param['shop_items_catalog_item_id']))
		{
			$accepted_param['item_id'] = $param['shop_items_catalog_item_id'];
		}

		if(isset($param['shop_items_catalog_marking']))
		{
			$accepted_param['marking'] = $param['shop_items_catalog_marking'];
		}

		if(isset($param['shop_groups_id']))
		{
			$accepted_param['groups_id'] = $param['shop_groups_id'];
		}

		if(isset($param['shop_items_catalog_shortcut_id']))
		{
			$accepted_param['shop_items_catalog_shortcut_id'] = $param['shop_items_catalog_shortcut_id'];
		}

		if(isset($param['shop_items_catalog_name']))
		{
			$accepted_param['name'] = $param['shop_items_catalog_name'];
		}

		if(isset($param['shop_items_catalog_description']))
		{
			$accepted_param['description'] = $param['shop_items_catalog_description'];
		}

		if(isset($param['shop_items_catalog_type']))
		{
			$accepted_param['shop_items_catalog_type'] = $param['shop_items_catalog_type'];
		}

		if(isset($param['shop_items_catalog_date_time']))
		{
			$accepted_param['shop_items_catalog_date_time'] = $param['shop_items_catalog_date_time'];
		}

		if(isset($param['shop_items_catalog_putoff_date']))
		{
			$accepted_param['shop_items_catalog_putoff_date'] = $param['shop_items_catalog_putoff_date'];
		}

		if(isset($param['shop_items_catalog_putend_date']))
		{
			$accepted_param['shop_items_catalog_putend_date'] = $param['shop_items_catalog_putend_date'];
		}

		if(isset($param['shop_items_catalog_text']))
		{
			$accepted_param['text'] = $param['shop_items_catalog_text'];
		}

		if(isset($param['shop_currency_id']))
		{
			$accepted_param['currency_id'] = $param['shop_currency_id'];
		}

		if(isset($param['shop_items_catalog_weight']))
		{
			$accepted_param['weight'] = $param['shop_items_catalog_weight'];
		}


		if(isset($param['shop_items_catalog_price']))
		{
			$accepted_param['price'] = $param['shop_items_catalog_price'];
		}

		if(isset($param['shop_items_catalog_is_active']))
		{
			$accepted_param['is_active'] = $param['shop_items_catalog_is_active'];
		}

		if(isset($param['shop_items_catalog_access']))
		{
			$accepted_param['shop_items_catalog_access'] = $param['shop_items_catalog_access'];
		}

		if(isset($param['shop_items_catalog_order']))
		{
			$accepted_param['order'] = $param['shop_items_catalog_order'];
		}

		if(isset($param['shop_items_catalog_indexation']))
		{
			$accepted_param['indexation'] = $param['shop_items_catalog_indexation'];
		}

		if(isset($param['shop_items_catalog_path']))
		{
			$accepted_param['path'] = $param['shop_items_catalog_path'];
		}

		if(isset($param['shop_items_catalog_seo_title']))
		{
			$accepted_param['seo_title'] = $param['shop_items_catalog_seo_title'];
		}

		if(isset($param['shop_items_catalog_seo_description']))
		{
			$accepted_param['seo_description'] = $param['shop_items_catalog_seo_description'];
		}

		if(isset($param['shop_items_catalog_seo_keywords']))
		{
			$accepted_param['seo_keywords'] = $param['shop_items_catalog_seo_keywords'];
		}

		if(isset($param['shop_sallers_id']))
		{
			$accepted_param['shop_sallers_id'] = $param['shop_sallers_id'];
		}

		if(isset($param['shop_items_catalog_modification_id']))
		{
			$accepted_param['shop_items_catalog_modification_id'] = $param['shop_items_catalog_modification_id'];
		}

		if(isset($param['users_id']))
		{
			$accepted_param['users_id'] = $param['users_id'];
		}

		if(isset($param['site_users_id']))
		{
			$accepted_param['site_users_id'] = $param['site_users_id'];
		}

		if(isset($param['path_source_big_image']))
		{
			$accepted_param['path_source_big_image'] = $param['path_source_big_image'];
		}

		if(isset($param['path_source_small_image']))
		{
			$accepted_param['path_source_small_image'] = $param['path_source_small_image'];
		}

		if(isset($param['original_file_name_big_image']))
		{
			$accepted_param['original_file_name_big_image'] = $param['original_file_name_big_image'];
		}

		if(isset($param['original_file_name_small_image']))
		{
			$accepted_param['original_file_name_small_image'] = $param['original_file_name_small_image'];
		}

		if(isset($param['max_width_big_image']))
		{
			$accepted_param['max_width_big_image'] = $param['max_width_big_image'];
		}

		if(isset($param['shop_items_catalog_big_image_width']))
		{
			$accepted_param['shop_items_catalog_big_image_width'] = $param['shop_items_catalog_big_image_width'];
		}

		if(isset($param['max_height_big_image']))
		{
			$accepted_param['max_height_big_image'] = $param['max_height_big_image'];
		}

		if(isset($param['shop_items_catalog_big_image_height']))
		{
			$accepted_param['shop_items_catalog_big_image_height'] = $param['shop_items_catalog_big_image_height'];
		}

		if(isset($param['max_width_small_image']))
		{
			$accepted_param['max_width_small_image'] = $param['max_width_small_image'];
		}

		if(isset($param['max_height_small_image']))
		{
			$accepted_param['max_height_small_image'] = $param['max_height_small_image'];
		}

		if(isset($param['shop_items_catalog_small_image_width']))
		{
			$accepted_param['shop_items_catalog_small_image_width'] = $param['shop_items_catalog_small_image_width'];
		}


		if(isset($param['watermark_file_path']))
		{
			$accepted_param['watermark_file_path'] = $param['watermark_file_path'];
		}

		if(isset($param['watermark_file_path']))
		{
			$accepted_param['watermark_file_path'] = $param['watermark_file_path'];
		}

		if(isset($param['watermark_position_x']))
		{
			$accepted_param['watermark_position_x'] = $param['watermark_position_x'];
		}

		if(isset($param['watermark_position_y']))
		{
			$accepted_param['watermark_position_y'] = $param['watermark_position_y'];
		}

		if(isset($param['used_watermark_big_image']))
		{
			$accepted_param['used_watermark_big_image'] = $param['used_watermark_big_image'];
		}

		if(isset($param['used_watermark_big_image']))
		{
			$accepted_param['used_watermark_big_image'] = $param['used_watermark_big_image'];
		}

		if(isset($param['shop_items_catalog_show_count']))
		{
			$accepted_param['shop_items_catalog_show_count'] = $param['shop_items_catalog_show_count'];
		}

		if(isset($param['search_event_indexation']))
		{
			$accepted_param['search_event_indexation'] = $param['search_event_indexation'];
		}

		return $this->InsertItem($accepted_param);
	}

	/**
	 * Получение информации о товаре
	 *
	 * @param int $shop_items_catalog_item_id идентификационный номер товара
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 *
	 * $row = $shop->GetItem($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array возвраает результат выборки товара
	 */
	function GetItem($shop_items_catalog_item_id, $param = array())
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['cache_off']) && isset($this->CacheGetItem[$shop_items_catalog_item_id]))
		{
			return $this->CacheGetItem[$shop_items_catalog_item_id];
		}

		$cache_name = 'SHOP_ITEM';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($shop_items_catalog_item_id, $cache_name))
			{
				if (!isset($param['cache_off']))
				{
					$this->CacheGetItem[$shop_items_catalog_item_id] = $in_cache['value'];
				}
				return $in_cache['value'];
			}
		}

		$oShop_Item = Core_Entity::factory('Shop_Item')->find($shop_items_catalog_item_id);

		if (is_null($oShop_Item->id))
		{
			return FALSE;
		}

		$row = $this->getArrayShopItem($oShop_Item);

		if (!isset($param['cache_off']))
		{
			$this->CacheGetItem[$shop_items_catalog_item_id] = $row;
		}

		// Запись в файловый кэш
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache->Insert($shop_items_catalog_item_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	 * Удаление запрещенных символов из пути
	 *
	 * @param string $path
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $path = 'g^r]o>up';
	 *
	 * $path = $shop->ClearPath($path);
	 *
	 * // Распечатаем результат
	 * echo $path;
	 * ?>
	 * </code>
	 * @return string
	 */
	function ClearPath($path)
	{
		return preg_replace('/[^а-яА-ЯёЁa-zA-Z0-9\-_ \.]/u', '', strval($path));
	}

	/**
	 * Получение информации о товаре по переданным атрибутам элемента
	 *
	 * @param $param массив с параметрами для условий
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 158;
	 *
	 * $resource = $shop->GetItemByParam($param);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource ресурс или false
	 */
	function GetItemByParam($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_items_catalog_item_id'),
				array('shortcut_id','shop_items_catalog_shortcut_id'),
				array('shop_tax_id', 'shop_tax_id'),
				array('shop_seller_id', 'shop_sallers_id'),
				array('shop_group_id', 'shop_groups_id'),
				'shop_currency_id',
				array('shop_id', 'shop_shops_id'),
				array('shop_producer_id', 'shop_producers_list_id'),
				array('shop_measure_id', 'shop_mesures_id'),
				array('type', 'shop_items_catalog_type'),
				array('name', 'shop_items_catalog_name'),
				array('marking', 'shop_items_catalog_marking'),
				array('vendorcode', 'shop_vendorcode'),
				array('description', 'shop_items_catalog_description'),
				array('text', 'shop_items_catalog_text'),
				array('image_large', 'shop_items_catalog_image'),
				array('image_small', 'shop_items_catalog_small_image'),
				array('weight', 'shop_items_catalog_weight'),
				array('price', 'shop_items_catalog_price'),
				array('active', 'shop_items_catalog_is_active'),
				array('siteuser_group_id', 'shop_items_catalog_access'),
				array('sorting', 'shop_items_catalog_order'),
				array('path', 'shop_items_catalog_path'),
				array('seo_title', 'shop_items_catalog_seo_title'),
				array('seo_description', 'shop_items_catalog_seo_description'),
				array('seo_keywords', 'shop_items_catalog_seo_keywords'),
				array('indexing', 'shop_items_catalog_indexation'),
				array('image_small_height', 'shop_items_catalog_small_image_height'),
				array('image_small_width', 'shop_items_catalog_small_image_width'),
				array('image_large_height', 'shop_items_catalog_big_image_height'),
				array('image_large_width', 'shop_items_catalog_big_image_width'),
				array('yandex_market', 'shop_items_catalog_yandex_market_allow'),
				array('yandex_market_bid', 'shop_items_catalog_yandex_market_bid'),
				array('yandex_market_cid', 'shop_items_catalog_yandex_market_cid'),
				array('yandex_market_sales_notes', 'shop_items_catalog_yandex_market_sales_notes'),
				array('siteuser_id', 'site_users_id'),
				array('datetime', 'shop_items_catalog_date_time'),
				array('modification_id', 'shop_items_catalog_modification_id'),
				array('guid', 'shop_items_cml_id'),
				array('start_datetime', 'shop_items_catalog_putoff_date'),
				array('end_datetime', 'shop_items_catalog_putend_date'),
				array('showed', 'shop_items_catalog_show_count'),
				array('user_id', 'users_id')
			)
			->from('shop_items')
			->where('deleted', '=', 0);

		foreach ($param as $field => $value)
		{
			$queryBuilder->having($field, '=', $value);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Формирование xml товара
	 *
	 * @param int $item_id идентификатор товара
	 * @param int $site_users_id идентификатор пользователя
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $item_id = 158;
	 *
	 * $xmlData = $shop->GetItemXml($item_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return mixed xml для товара (информация о товаре + дополнительные свойства + сопуствующие товары и т.д.), ложь - если запись не выбрана
	 */
	function GetItemXml($item_id, $site_users_id = 0)
	{
		$item_id = intval($item_id);
		$site_users_id = intval($site_users_id);

		// Получаем данные о товаре
		$catalog_row = $this->GetItem($item_id);

		if ($catalog_row)
		{
			$xmlData = $this->GenXml4Item(1, $catalog_row, $site_users_id);
			return $xmlData;
		}
		return FALSE;
	}

	/**
	 * Получение списка групп пользователей, в которых содержится пользователь сайта
	 *
	 * @param int $site_user_id идентификатор пользователя сайтов
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $site_user_id = 1;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 *	$site_users_id = 0;
	 * }
	 *
	 * $row = $shop->GetSiteUsersGroupsForUser($site_user_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив групп пользователей
	 */
	function GetSiteUsersGroupsForUser($site_user_id)
	{
		$site_user_id = intval($site_user_id);

		// Определяем группы доступа для текущего авторизированного	пользователя
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			// Получаем список групп доступа, в которые входит данный пользователь
			$mas_result = $SiteUsers->GetGroupsForUser($site_user_id);
		}
		else
		{
			$mas_result = array();
			$mas_result[] = 0;
		}

		// Добавляем всегда "Как у родителя"
		if (!in_array(-1, $mas_result))
		{
			$mas_result[]  = -1;
		}

		return $mas_result;
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров
	 * товаров. Заполнению подвергается массив
	 * $this->CacheGetItem[$shop_items_catalog_item_id]
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 */
	function FillMemCacheItems($mas_items_in)
	{
		$mas_items_in = Core_Type_Conversion::toArray($mas_items_in);

		if (count($mas_items_in) > 0)
		{
			// Меняем местами значения и ключи массива
			$aTmp = array_flip($mas_items_in);

			// Вычислить пересечение массивов, сравнивая ключи
			$aTmpIntersect = array_intersect_key($aTmp, $this->CacheGetItem);

			if (count($aTmpIntersect) != count($mas_items_in))
			{
				foreach ($mas_items_in as $shop_items_catalog_item_id)
				{
					$this->CacheGetItem[$shop_items_catalog_item_id] = FALSE;
				}

				$mas_items_in = Core_Array::toInt($mas_items_in);

				$queryBuilder = Core_QueryBuilder::select(
						array('id', 'shop_items_catalog_item_id'),
						array('shortcut_id','shop_items_catalog_shortcut_id'),
						array('shop_tax_id', 'shop_tax_id'),
						array('shop_seller_id', 'shop_sallers_id'),
						array('shop_group_id', 'shop_groups_id'),
						'shop_currency_id',
						array('shop_id', 'shop_shops_id'),
						array('shop_producer_id', 'shop_producers_list_id'),
						array('shop_measure_id', 'shop_mesures_id'),
						array('type', 'shop_items_catalog_type'),
						array('name', 'shop_items_catalog_name'),
						array('marking', 'shop_items_catalog_marking'),
						array('vendorcode', 'shop_vendorcode'),
						array('description', 'shop_items_catalog_description'),
						array('text', 'shop_items_catalog_text'),
						array('image_large', 'shop_items_catalog_image'),
						array('image_small', 'shop_items_catalog_small_image'),
						array('weight', 'shop_items_catalog_weight'),
						array('price', 'shop_items_catalog_price'),
						array('active', 'shop_items_catalog_is_active'),
						array('siteuser_group_id', 'shop_items_catalog_access'),
						array('sorting', 'shop_items_catalog_order'),
						array('path', 'shop_items_catalog_path'),
						array('seo_title', 'shop_items_catalog_seo_title'),
						array('seo_description', 'shop_items_catalog_seo_description'),
						array('seo_keywords', 'shop_items_catalog_seo_keywords'),
						array('indexing', 'shop_items_catalog_indexation'),
						array('image_small_height', 'shop_items_catalog_small_image_height'),
						array('image_small_width', 'shop_items_catalog_small_image_width'),
						array('image_large_height', 'shop_items_catalog_big_image_height'),
						array('image_large_width', 'shop_items_catalog_big_image_width'),
						array('yandex_market', 'shop_items_catalog_yandex_market_allow'),
						array('yandex_market_bid', 'shop_items_catalog_yandex_market_bid'),
						array('yandex_market_cid', 'shop_items_catalog_yandex_market_cid'),
						array('yandex_market_sales_notes', 'shop_items_catalog_yandex_market_sales_notes'),
						array('siteuser_id', 'site_users_id'),
						array('datetime', 'shop_items_catalog_date_time'),
						array('modification_id', 'shop_items_catalog_modification_id'),
						array('guid', 'shop_items_cml_id'),
						array('start_datetime', 'shop_items_catalog_putoff_date'),
						array('end_datetime', 'shop_items_catalog_putend_date'),
						array('showed', 'shop_items_catalog_show_count'),
						array('user_id', 'users_id')
					)
					->from('shop_items')
					->where('id', 'IN', $mas_items_in)
					->where('deleted', '=', 0);

				$aResult = $queryBuilder->execute()->asAssoc()->result();

				foreach($aResult as $row)
				{
					// Пишем в кэш для товаров
					$this->CacheGetItem[$row['shop_items_catalog_item_id']] = $row;
				}
			}
		}
	}

	/**
	 * Получение всех товаров заданного магазина и заданной группы товаров
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param mixed $group_id идентификатор группы товаров, если $group_id = false, то получаем информацию о товарах всех групп
	 * @param array $param массив дополнительных параметров
	 * $param['items_begin'] номер товара в выборке, с которого начинать отображение товаров магазина
	 * $param['items_on_page'] число товаров, отображаемых на странице
	 * $param['items_field_order'] поле сортировки
	 * $param['shop_items_catalog_is_active'] активность товара (если 2, выбираем и активные и неактивные параметры, если 0 выбираем только неактивные товары, если не передан выбираем только активные товары)
	 * $param['items_order'] направление сортировки ('Asc' - по возрастанию, 'Desc' - по убыванию, 'Rand' - произвольный порядок)
	 * $param['FillMemCacheTyingProducts'] указывает на необходимость заполнения кэша сопутствующих товаров для выбранных товаров. по умолчанию false
	 * $param['FillMemCacheDiscountsForItem'] указывает на необходимость заполнения кэша скидок для выбранных товаров. по умолчанию true
	 * $param['FillMemCacheComments'] указывает на необходимость заполнения кэша комментариев для выбранных товаров. по умолчанию false
	 * $param['FillMemCachePropertiesItem'] указывает на необходимость заполнения кэша дополнительных свойств для выбранных товаров. по умолчанию false
	 * $param['sql_from'] дополнения для SQL-запроса выборки в секции FROM. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * $param['sql_having'] дополнения для SQL-запроса выборки в секции HAVING. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * $param['sql_group_by'] дополнения для SQL-запроса выборки в секции GROUP BY. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * $param['sql_select_modification'] указывает на необходимость ограничения. Если указано true, то модификации исключаются из выборки
	 * $param['cache_off'] флаг, запрещающий кеширование (по умолчанию false)
	 * $param['select'] массив внешних условий для выборки, например
	 * - $param['select'][0]['type'] = 0;
	 * - $param['select'][0]['prefix'] = 'AND';
	 * - $param['select'][0]['name'] = 'shop_items_catalog_yandex_market_allow';
	 * - $param['select'][0]['if'] = '=';
	 * - $param['select'][0]['value'] = '1';
	 * - $param['select'][0]['sufix'] = '';
	 * $param['show_catalog_item_type'] array массив типов товаров, которые должны отображаться.
	 * Может содержать следующие элементы:
	 * <br />active - активные элементы (внесен по умолчанию, если $param['show_catalog_item_type'] не задан;
	 * <br />inactive - неактивные элементы;
	 * <br />putend_date - элементы, у которых значение поля putend_date меньше текущей даты;
	 * <br />putoff_date - элементы, у которых значение поля putoff_date превышает текущую дату;
	 * $param['sql_external_select'] параметр, задающий список дополнительных полей в оператор SELECT выборки товаров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $row = $shop->GetAllItems($shop_id, $group_id, $param);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed массив или false
	 */
	function GetAllItems($shop_id, $group_id = FALSE, $param = array())
	{
		$shop_id = intval($shop_id);

		$cache_key = $shop_id . '_' . serialize($group_id) . '_' . serialize($param);

		$cache_name = 'SHOP_ALL_ITEMS';
		if (class_exists('Cache') && (!isset($param['cache_off']) || !$param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($cache_key, $cache_name))
			{
				if (isset($in_cache['value']['result']) && isset($in_cache['value']['GetAllItemTotalCount']))
				{
					// Восстанавливаем количество
					$this->GetAllItemTotalCount = $in_cache['value']['GetAllItemTotalCount'];
					return $in_cache['value']['result'];
				}
			}
		}

		if (!isset($param['current_group_id']))
		{
			$param['current_group_id'] = 0;
		}

		if (!isset($param['sql_select_modification']))
		{
			$param['sql_select_modification'] = TRUE;
		}

		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		// Получаем список групп доступа, в которые входит данный пользователь
		$mas_result = $this->GetSiteUsersGroupsForUser($site_user_id);

		$queryBuilder = Core_QueryBuilder::select(
				'shop_items.id',
				array('shop_items.shortcut_id', 'shop_items_catalog_shortcut_id'),
				'shop_items.shop_tax_id',
				array('shop_items.shop_seller_id', 'shop_sallers_id'),
				array('shop_items.shop_group_id', 'shop_groups_id'),
				'shop_items.shop_currency_id',
				array('shop_items.shop_id', 'shop_shops_id'),
				array('shop_items.shop_producer_id', 'shop_producers_list_id'),
				array('shop_items.shop_measure_id', 'shop_mesures_id'),
				array('shop_items.type', 'shop_items_catalog_type'),
				array('shop_items.name', 'shop_items_catalog_name'),
				array('shop_items.marking', 'shop_items_catalog_marking'),
				array('shop_items.vendorcode', 'shop_vendorcode'),
				array('shop_items.description', 'shop_items_catalog_description'),
				array('shop_items.text', 'shop_items_catalog_text'),
				array('shop_items.image_large', 'shop_items_catalog_image'),
				array('shop_items.image_small', 'shop_items_catalog_small_image'),
				array('shop_items.weight', 'shop_items_catalog_weight'),
				array('shop_items.price', 'shop_items_catalog_price'),
				array('shop_items.active', 'shop_items_catalog_is_active'),
				array('shop_items.siteuser_group_id', 'shop_items_catalog_access'),
				array('shop_items.sorting', 'shop_items_catalog_order'),
				array('shop_items.path', 'shop_items_catalog_path'),
				array('shop_items.seo_title', 'shop_items_catalog_seo_title'),
				array('shop_items.seo_description', 'shop_items_catalog_seo_description'),
				array('shop_items.seo_keywords', 'shop_items_catalog_seo_keywords'),
				array('shop_items.indexing', 'shop_items_catalog_indexation'),
				array('shop_items.image_small_height', 'shop_items_catalog_small_image_height'),
				array('shop_items.image_small_width', 'shop_items_catalog_small_image_width'),
				array('shop_items.image_large_height', 'shop_items_catalog_big_image_height'),
				array('shop_items.image_large_width', 'shop_items_catalog_big_image_width'),
				array('shop_items.yandex_market', 'shop_items_catalog_yandex_market_allow'),
				array('shop_items.yandex_market_bid', 'shop_items_catalog_yandex_market_bid'),
				array('shop_items.yandex_market_cid', 'shop_items_catalog_yandex_market_cid'),
				array('shop_items.yandex_market_sales_notes', 'shop_items_catalog_yandex_market_sales_notes'),
				array('shop_items.siteuser_id', 'site_users_id'),
				array('shop_items.datetime', 'shop_items_catalog_date_time'),
				array('shop_items.modification_id', 'shop_items_catalog_modification_id'),
				array('shop_items.guid', 'shop_items_cml_id'),
				array('shop_items.start_datetime', 'shop_items_catalog_putoff_date'),
				array('shop_items.end_datetime', 'shop_items_catalog_putend_date'),
				array('shop_items.showed', 'shop_items_catalog_show_count')
			)
			->sqlCalcFoundRows()
			->from('shop_items')
			//->leftJoin('shop_groups', 'shop_items.shop_group_id', '=', 'shop_groups.id')
			->where('shop_items.deleted', '=', 0)
			//->where('shop_groups.deleted', '=', 0)
			->where('shop_items.siteuser_group_id', 'IN', $mas_result)
			->where('shop_items.shop_id', '=', $shop_id);

		if (isset($param['shop_items_catalog_is_active']) && intval($param['shop_items_catalog_is_active']) == 0)
		{
			$queryBuilder->where('shop_items.active', '=', 0);
		}
		// если параметр не передан, берем только активные
		else
		{
			$queryBuilder->where('shop_items.active', '=', 1);
		}

		// Родительская группа
		if ($group_id !== FALSE)
		{
			$group_id = intval($group_id);
		}

		$items_begin = isset($param['items_begin'])	? Core_Type_Conversion::toInt($param['items_begin']) : 0;

		if (!isset($param['show_catalog_item_type']))
		{
			$param['show_catalog_item_type'] = array('active');
		}

		$param['FillMemCacheTyingProducts'] = Core_Type_Conversion::toBool($param['FillMemCacheTyingProducts']);
		$param['FillMemCacheComments'] = Core_Type_Conversion::toBool($param['FillMemCacheComments']);
		$param['FillMemCachePropertiesItem'] = Core_Type_Conversion::toBool($param['FillMemCachePropertiesItem']);
		$param['FillMemCacheModificationItems'] = Core_Type_Conversion::toBool($param['FillMemCacheModificationItems']);
		$param['FillMemCacheSpecialPricesForItem'] = Core_Type_Conversion::toBool($param['FillMemCacheSpecialPricesForItem']);
		$param['FillMemCacheGetAllPricesForItem'] = Core_Type_Conversion::toBool($param['FillMemCacheGetAllPricesForItem']);
		$param['FillMemCachePriceForItem'] = Core_Type_Conversion::toBool($param['FillMemCachePriceForItem']);

		$param['FillMemCacheDiscountsForItem'] = !isset($param['FillMemCacheDiscountsForItem'])
			? TRUE
			: Core_Type_Conversion::toBool($param['FillMemCacheDiscountsForItem']);

		if (isset($param['sql_from']))
		{
			$sql_from = strval($param['sql_from']);

			$aSqlFrom = explode(',', $sql_from);
			foreach($aSqlFrom as $sSqlFrom)
			{
				//trim($sSqlFrom) != '' && $queryBuilder->from(trim($sSqlFrom));

				// Может содержать LEFT JOIN
				if (trim($sSqlFrom) != '')
				{
					strpos($sSqlFrom, 'JOIN ') === FALSE
						? $queryBuilder->from(trim($sSqlFrom))
						: $this->parseQueryBuilder(trim($sSqlFrom), $queryBuilder);
				}
			}
		}

		if (isset($param['sql_external_select']))
		{
			$param['sql_external_select'] = trim($param['sql_external_select']);
			$param['sql_external_select'] = trim($param['sql_external_select'], ',');

			$param['sql_external_select'] != '' && $queryBuilder->select(Core_QueryBuilder::expression($param['sql_external_select']));

			//$this->parseQueryBuilder($param['sql_external_select'], $queryBuilder);
			/*$aExternalSelect = explode(',', $param['sql_external_select']);
			foreach ($aExternalSelect as $sExternalSelect)
			{
				$sExternalSelect = trim($sExternalSelect);
				if ($sExternalSelect != '')
				{
					$queryBuilder->select(Core_QueryBuilder::expression($sExternalSelect));
				}
			}*/
		}

		if (isset($param['sql_having']))
		{
			$this->parseQueryBuilder(Core_Type_Conversion::toStr($param['sql_having']), $queryBuilder);
		}

		if (isset($param['sql_join']))
		{
			$this->parseQueryBuilder(Core_Type_Conversion::toStr($param['sql_join']), $queryBuilder);
		}

		if (isset($param['sql_group_by']))
		{
			$sql_group_by = Core_Type_Conversion::toStr($param['sql_group_by']);
			$this->parseQueryBuilder($sql_group_by, $queryBuilder);
		}

		if (isset($param['items_on_page']) && $param['items_on_page'] !== FALSE)
		{
			$items_on_page = Core_Type_Conversion::toInt($param['items_on_page']);
		}

		if (isset($param['items_order']))
		{
			$items_order = strtoupper(trim(Core_Type_Conversion::toStr($param['items_order'])));

			// неправильно задали название сортировки
			if ($items_order != 'ASC' && $items_order != 'DESC' && $items_order != 'RAND')
			{
				$items_order = 'ASC';
			}

			if ($items_order == 'RAND')
			{
				$items_order = 'RAND()';
				$param['items_field_order'] = '';
			}
		}
		else
		{
			// Если порядок сортировки не передан - берем из полей магазина
			$shop_row = $this->GetShop($shop_id);

			if ($shop_row)
			{
				switch ($shop_row['shop_sort_order_type'])
				{
					case 0 :
						$items_order = 'ASC';
					break;
					default :
						$items_order = 'DESC';
					break;
				}
			}
			else
			{
				$items_order = 'ASC';
			}
		}

		if (isset($param['items_field_order']))
		{
			$items_field_order = Core_Type_Conversion::toStr($param['items_field_order']);

			if (strtolower($items_field_order) == 'shop_comment_grade')
			{
				$queryBuilder->orderBy('avg_shop_comment_grade', $items_order);
			}
			else
			{
				$aItemsFieldOrder = explode(',', $items_field_order);
				foreach ($aItemsFieldOrder as $sItemsFieldOrder)
				{
					$queryBuilder->orderBy(trim($sItemsFieldOrder), $items_order);
				}

				//$queryBuilder->orderBy($items_field_order, $items_order);
			}
		}
		else
		{
			// Если поле сортирвоки не передан - берем из полей магазина
			$shop_row = $this->GetShop($shop_id);

			if ($shop_row)
			{
				switch ($shop_row['shop_sort_order_field'])
				{
					case 0 :
						$queryBuilder->orderBy('shop_items_catalog_date_time', $items_order);
					break;
					case 1 :
						$queryBuilder->orderBy('shop_items_catalog_name', $items_order);
					break;
					default :
						$queryBuilder
							->orderBy('shop_items_catalog_order', $items_order)
							->orderBy('shop_items_catalog_name');
				}
			}
			else
			{
				$queryBuilder
					->orderBy('shop_items_catalog_order', $items_order)
					->orderBy('shop_items_catalog_name');
			}
		}

		if (is_array($param['current_group_id']) && count($param['current_group_id']) > 0)
		{
			$param['current_group_id'] = Core_Array::toInt($param['current_group_id']);
			$queryBuilder->where('shop_items.shop_group_id', 'IN', $param['current_group_id']);
		}
		elseif ($group_id !== FALSE)
		{
			$queryBuilder->where('shop_items.shop_group_id', '=', $group_id);
		}

		$query_property = '';

		// Флаг, указывающий наличие условий для товаров
		$isset_items_property = FALSE;
		$isset_shop_items_catalog_rest = FALSE;

		if (isset($param['select']) && count($param['select']) > 0)
		{
			foreach ($param['select'] as $value)
			{
				// Ограничение по остатку на складе
				if (isset($value['name']) && $value['name'] == 'shop_items_catalog_rest')
				{
					if (!$isset_shop_items_catalog_rest)
					{
						$queryBuilder
							->select(array('SUM(shop_warehouse_items.count)', 'shop_items_catalog_rest'))
							->leftJoin('shop_warehouse_items', 'shop_warehouse_items.shop_item_id', '=', 'shop_items.id')
							->groupBy('shop_items.id');
						$isset_shop_items_catalog_rest = TRUE;
					}
					$this->parseQueryBuilder($value['prefix'], $queryBuilder);

					$value['if'] = trim($value['if']);
					$value['value'] = strtoupper($value['if']) == 'IN'
						? explode(',', $value['value'])
						: Core_Type_Conversion::toStr($value['value']);
					$queryBuilder->having('shop_items_catalog_rest', $value['if'], $value['value']);
					$this->parseQueryBuilder($value['sufix'], $queryBuilder);
				}
				else
				{
					// Есть ограничение на значения доп. свойств товаров
					if (Core_Type_Conversion::toInt($value['property_id']) != 0)
					{
						$isset_items_property = TRUE;
					}

					if (Core_Type_Conversion::toInt($value['type']) == 0) /* основное свойство*/
					{
						$this->parseQueryBuilder($value['prefix'], $queryBuilder);
						$value['if'] = trim($value['if']);
						$value['value'] = strtoupper($value['if']) == 'IN'
							? explode(',', $value['value'])
							: Core_Type_Conversion::toStr($value['value']);

						$value['name'] != '' && $value['if'] != ''
							&& $this->parseQueryBuilderWhere($value['name'], $value['if'], $value['value'], $queryBuilder);

						$this->parseQueryBuilder($value['sufix'], $queryBuilder);
					}
					else // Дополнительное свойство
					{
						$this->parseQueryBuilder($value['prefix'], $queryBuilder);
						$queryBuilder->where('shop_item_properties.property_id', '=', $value['property_id']);
						$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);

						$this->parseQueryBuilderWhere(
							$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $value['value'], $queryBuilder
						);

						$this->parseQueryBuilder($value['sufix'], $queryBuilder);
					}
				}
			}

			// Объединение со св-вами делаем только тогда, когда есть внешняя фильтрация по ним
			if ($isset_items_property)
			{
				$queryBuilder
					->leftJoin('shop_item_properties', 'shop_items.shop_id', '=', 'shop_item_properties.shop_id')
					->leftJoin('property_value_ints', 'shop_items.id', '=', 'property_value_ints.entity_id',
						array(
							array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_ints.property_id')))
						)
					)
					->leftJoin('property_value_strings', 'shop_items.id', '=', 'property_value_strings.entity_id',
						array(
							array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_strings.property_id')))
						)
					)
					->leftJoin('property_value_texts', 'shop_items.id', '=', 'property_value_texts.entity_id',
						array(
							array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_texts.property_id')))
						)
					)
					->leftJoin('property_value_datetimes', 'shop_items.id', '=', 'property_value_datetimes.entity_id',
						array(
							array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_datetimes.property_id')))
						)
					)
					->leftJoin('property_value_files', 'shop_items.id', '=', 'property_value_files.entity_id',
						array(
							array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_files.property_id')))
						)
					);
			}
		}

		// Ограничение по скидкам (если передано)
		if (isset($param['select_discount']) && is_array($param['select_discount']) && count($param['select_discount']) > 0)
		{
			$param['select_discount'] = Core_Array::toInt($param['select_discount']);

			$queryBuilder
				->leftJoin('shop_item_discounts', 'shop_item_discounts.shop_item_id', '=', 'shop_items.id')
				->leftJoin('shop_discounts', 'shop_discounts.id', '=', 'shop_item_discounts.shop_discount_id')
				->where('shop_item_discounts.shop_discount_id', 'IN', $param['select_discount'])
				->where('shop_discounts.deleted', '=', 0);
		}

		// Определяем ID элементов, которые не надо включать в выдачу
		if (isset($param['NotIn']))
		{
			// Разбиваем переданные параметры и копируем в массив
			$not_in_mass = Core_Array::toInt(explode(',', $param['NotIn']));

			if (count($not_in_mass) > 0)
			{
				$queryBuilder->where('shop_items.id', 'NOT IN', $not_in_mass);
			}
		}

		if (isset($items_on_page))
		{
			$queryBuilder->limit($items_begin, $items_on_page);
		}

		$current_date = date('Y-m-d H:i:s');

		// Если не содержит putend_date - ограничиваем по дате окончания публикации
		if (!in_array('putend_date', $param['show_catalog_item_type']))
		{
			$queryBuilder
				->open()
				->where('shop_items.end_datetime', '>=', $current_date)
				->setOr()
				->where('shop_items.end_datetime', '=', '0000-00-00 00:00:00')
				->close();
		}

		// если не содержит putoff_date - ограничиваем по дате начала публикации
		if (!in_array('putoff_date', $param['show_catalog_item_type']))
		{
			$queryBuilder->where('shop_items.start_datetime', '<=', $current_date);
		}

		// Объединяем с тегами и ограничиваем по ним
		if (isset($param['tags']) && is_array($param['tags']) && count($param['tags']) > 0)
		{
			$param['tags'] = Core_Array::toInt($param['tags']);

			$queryBuilder
				->leftJoin('tag_shop_items', 'shop_items.id', '=', 'tag_shop_items.shop_item_id')
				->where('tag_shop_items.tag_id', 'IN', $param['tags']);
		}
		else
		{
			if ($param['sql_select_modification'])
			{
				$queryBuilder->where('shop_items.modification_id', '=', 0);
			}
		}

		if (isset($param['items_field_order']) && strtolower($param['items_field_order']) == 'shop_comment_grade')
		{
			$queryBuilder
				->select(array('AVG(comments.grade)', 'avg_shop_comment_grade'))
				->groupBy('shop_items.id')
				->leftJoin('comment_shop_items', 'shop_items.id', '=', 'comment_shop_items.shop_item_id')
				->leftJoin('comments', 'comment_shop_items.comment_id', '=', 'comments.id',
					array(
						array('AND' => array('comments.grade', '!=', 0)),
						array('AND' => array('comments.active', '=', 1)),
						array('AND' => array('comments.deleted', '=', 0)),
				));
		}
		else
		{
			$queryBuilder->distinct();
		}

		/* Получаем идентификаторы товаров, удовлетворяющих всем ограничениям*/
		// Выбираем элементы для отображения с рассчетом общего количества, для этого указываем SQL_CALC_FOUND_ROWS
		$aResult = $queryBuilder->execute()->asAssoc()->result();

		$count = is_array($aResult) ? count($aResult) : 0;

		// Определим общее количество элементов
		$queryBuilderSame = Core_QueryBuilder::select(array('FOUND_ROWS()', 'count'));
		$aCountComments = $queryBuilderSame->execute()->asAssoc()->current();

		$this->GetAllItemTotalCount = $aCountComments['count'];

		// Число товаров в выборке больше 0
		if ($count > 0)
		{
			$mas_items_in = array();

			// формируем массив из идентификаторов выбранных товаров
			// для составления запроса на получение подробной информации о выбранных товарах
			foreach($aResult as $row)
			{
				$mas_items_in[] = $row['id'];
			}

			// Заполняем кэш сопутствующих товаров
			$param['FillMemCacheTyingProducts'] && $this->FillMemCacheTyingProducts($mas_items_in);

			// Заполняем кэш для скидок
			$param['FillMemCacheDiscountsForItem'] && $this->FillMemCacheDiscountsForItem($mas_items_in);

			// Заполняем кэш для комметариев
			$param['FillMemCacheComments'] && $this->FillMemCacheComments($mas_items_in);

			// Заполняем кэш для дополнительных свойств товаров
			$param['FillMemCachePropertiesItem'] && $this->FillMemCachePropertiesItem($mas_items_in);

			// Заполняем кэш для сопутствующих товаров
			if ($param['FillMemCacheModificationItems'])
			{
				$param['shop_id'] = $shop_id;
				$this->FillMemCacheModificationItems($mas_items_in, $param);
			}

			// Заполняем кэш специальных цен
			$param['FillMemCacheSpecialPricesForItem'] && $this->FillMemCacheSpecialPricesForItem($mas_items_in);

			// Заполняем кэш цен для групп пользователей для товара
			$param['FillMemCacheGetAllPricesForItem'] && $this->FillMemCacheGetAllPricesForItem($mas_items_in);

			$param['FillMemCachePriceForItem'] && $this->FillMemCachePriceForItem($mas_items_in, $shop_id);

			// Заполняем кэш для тегов
			if (class_exists('Tag'))
			{
				$oTag = & singleton('Tag');
				$oTag->FillMemCacheGetTagRelation(array('shop_items_catalog_item_id' => $mas_items_in));
			}

			// Извлекаем данные о товарах
			$this->FillMemCacheItems($mas_items_in);

			$result = array();

			// Вынесено в отдельный foreach, т.к. порядок сортировки при выборке через IN
			// не сохраняется и необходимо проходить по оригинальному массиву $mas_items_in
			foreach ($mas_items_in as $shop_items_catalog_item_id)
			{
				if (isset($this->CacheGetItem[$shop_items_catalog_item_id]))
				{
					$result[] = & $this->CacheGetItem[$shop_items_catalog_item_id];
				}
			}

			// Запись в файловый кэш
			if (class_exists('Cache') && (!isset($param['cache_off']) || !$param['cache_off']))
			{
				$cache = & singleton('Cache');
				$cache->Insert($cache_key, array(
				'result' => $result,
				'GetAllItemTotalCount' => $this->GetAllItemTotalCount
				), $cache_name);
			}

			return $result;
		}
		return FALSE;
	}

	/**
	 * Получение числа товаров, параметры которых удовлетворяют заданным условиям
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param mixed $group_id идентификатор группы товаров, если $group_id = false, то получаем информацию о товарах всех групп, если $group_id равен строке с перечислением идентификаторов - '1,2,10,12,17', то возвращаются данные о товарах из указанных групп
	 * @param array $param массив дополнительных параметров
	 * - $param['OrderField'] поле сортировки, если случайная сортировка, то записать RAND()
	 * - $param['NotIn'] идентификаторы элементов, которые необходимо исключить из результатов
	 * - $param['select'] массив ($element) с дополнительными параметрами для задания дополнительных условий отбора товаров
	 * - $element['type'] определяет, является ли поле основным свойством товара или дополнительным (0 - основное, 1 - дополнительное)
	 * - $element['prefix'] префикс - строка, размещаемая перед условием
	 * - $element['name'] имя поля для основного свойства, если свойство дополнительное, то не указывается
	 * - $element['property_id'] идентификатор дополнительногого свойства
	 * - $element['if'] строка, содержащая условный оператор
	 * - $elemenr['value'] значение поля (или параметра)
	 * - $element['sufix'] суффикс - строка, размещаемая после условия
	 * <br />Например
	 * - $element['type']=1; // 0 - основное св-во, 1 - дополнительное
	 * - $element['prefix'] = 'and'; // префикс
	 * - $element['property_id'] = 26; // ID дополнительного св-ва, указывается если тип = 1
	 * - $element['if'] = '='; // Условие
	 * - $element['value'] = '10';
	 * - $element['sufix'] = '';
	 * - $param['select'][]=$element;
	 * - $element['type']=1; // 0 - основное св-во, 1 - дополнительное
	 * - $element['prefix'] = 'and'; // префикс
	 * - $element['property_id'] = 28; // ID дополнительного св-ва, указывается если тип = 1
	 * - $element['if'] = '='; // Условие
	 * - $element['value'] = 1;
	 * - $element['sufix'] = '';
	 * $param['select'][]=$element;
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $count = $shop->GetCountItemsWithConditions($shop_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число товаров в случае успешного выполнения, false в противном случае
	 *
	 */
	function GetCountItemsWithConditions($shop_id, $group_id = FALSE, $param = array())
	{
		return count(
			$this->GetAllItems($shop_id, $group_id, $param)
		);
	}

	/**
	 * Удаление товара
	 *
	 * @param int $shop_items_catalog_item_id идентификационный номер товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 1;
	 *
	 * $result = $shop->DeleteItem($shop_items_catalog_item_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления товара
	 */
	function DeleteItem($shop_items_catalog_item_id)
	{
		$oShop_Item = Core_Entity::factory('Shop_Item')->find($shop_items_catalog_item_id);

		if (!is_null($oShop_Item->id))
		{
			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_ITEM';
				$cache->DeleteCacheItem($cache_name, $oShop_Item->id);
			}

			$oShop_Item->markDeleted();

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Удаление всех модификаций товара
	 *
	 * @param int $shop_items_catalog_item_id Идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 *
	 * $result = $shop->DeleteAllModificationForItem($shop_items_catalog_item_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function DeleteAllModificationForItem($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
		$oShop_Item = Core_Entity::factory('Shop_Item')->find($shop_items_catalog_item_id);

		if (!is_null($oShop_Item->id))
		{
			$aShop_Item_Modifications = $oShop_Item->Modifications->findAll();
			foreach($aShop_Item_Modifications as $oShop_Item_Modification)
			{
				$oShop_Item_Modification->markDeleted();
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Удаление товаров и групп
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 7;
	 *
	 * $result = $shop->DeleteAllItemsCatalogAndGroups($shop_shops_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 */
	function DeleteAllItemsCatalogAndGroups($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$oShop = Core_Entity::factory('Shop')->find($shop_shops_id);

		if (!is_null($oShop->id))
		{
			$aShop_Groups = $oShop->Shop_Groups->findAll();
			foreach($aShop_Groups as $oShop_Group)
			{
				$oShop_Group->markDeleted();
			}

			$aShopItems = $oShop->Shop_Items->findAll();
			foreach($aShopItems as $oShopItem)
			{
				$oShopItem->markDeleted();
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Вставка информации о единице измерения
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификационнный номер единицы измерения
	 * <br />string $param['name'] наименование единицы измерения
	 * <br />string $param['description'] описание единицы измерения
	 * <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['id'] = 1;
	 * $param['name'] = 'in';
	 * $param['description'] = 'дюйм';
	 *
	 * $newid = $shop->InsertMesure($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return возвращает идентификационный номер вставленной единицы измерения
	 */
	function InsertMesure($param)
	{
		$param = Core_Type_Conversion::toArray($param);
		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Measure = Core_Entity::factory('Shop_Measure', $param['id']);

		if (isset($param['name']))
		{
			$oShop_Measure->name = $param['name'];
		}

		if (isset($param['description']))
		{
			$oShop_Measure->description = $param['description'];
		}

		if (is_null($oShop_Measure->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Measure->user_id = intval($param['users_id']);
		}

		if (!is_null($oShop_Measure->id))
		{
			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_MESURE';
				$cache->DeleteCacheItem($cache_name, $oShop_Measure->id);
			}
		}

		$oShop_Measure->save();

		return $oShop_Measure->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление информации о единице измерения
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификационнный номер единицы измерения
	 * <br />string $param['name'] наименование единицы измерения
	 * <br />string $param['description'] описание единицы измерения
	 * <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления информации о единице измерения
	 */
	function UpdateMesure($param)
	{
		return $this->InsertMesure($param);
	}

	/**
	 * Получение информации о единице измерения
	 *
	 * @param int $shop_mesures_id идентификационный номер единицы измерения
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_mesures_id = 1;
	 *
	 * $row = $shop->GetMesure($shop_mesures_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки единицы измерения
	 */
	function GetMesure($shop_mesures_id, $param = array())
	{
		$shop_mesures_id = intval($shop_mesures_id);

		if (!$shop_mesures_id)
		{
			return FALSE;
		}

		$param = Core_Type_Conversion::toArray($param);

		if (!defined('ALLOW_SHOP_MEM_CACHE_MESURE') || (defined('ALLOW_SHOP_MEM_CACHE_MESURE') && ALLOW_SHOP_MEM_CACHE_MESURE))
		{
			if (isset($this->cache_mesure[$shop_mesures_id]))
			{
				return $this->cache_mesure[$shop_mesures_id];
			}
		}

		$cache_name = 'SHOP_MESURE';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($shop_mesures_id, $cache_name))
			{
				if (!defined('ALLOW_SHOP_MEM_CACHE_MESURE') || (defined('ALLOW_SHOP_MEM_CACHE_MESURE') && ALLOW_SHOP_MEM_CACHE_MESURE))
				{
					// Сохраняем в кэше в памяти
					$this->cache_mesure[$shop_mesures_id] = $in_cache['value'];
				}

				return $in_cache['value'];
			}
		}

		$oShop_Measure = Core_Entity::factory('Shop_Measure')->find($shop_mesures_id);

		if (is_null($oShop_Measure->id))
		{
			return FALSE;
		}

		$row = $this->getArrayShopMeasure($oShop_Measure);

		if (!defined('ALLOW_SHOP_MEM_CACHE_MESURE') || (defined('ALLOW_SHOP_MEM_CACHE_MESURE') && ALLOW_SHOP_MEM_CACHE_MESURE))
		{
			$this->cache_mesure[$shop_mesures_id] = $row;
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache->Insert($shop_mesures_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	 * Получение информации о всех единицах измерения
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $array = $shop->GetAllMesures();
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($array))
	 * {
	 *	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource результат выборки единиц измерения
	 */
	function GetAllMesures()
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_mesures_id'),
				array('name', 'shop_mesures_name'),
				array('description', 'shop_mesures_description'),
				array('user_id', 'users_id')
			)
			->where('deleted', '=', 0)
			->orderBy('shop_mesures_name');

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление единицы измерения
	 *
	 * @param int $shop_mesures_id идентификационный номер единицы измерения
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_mesures_id = 1;
	 *
	 * $result = $shop->DeleteMesure($shop_mesures_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления единицы измерения
	 */
	function DeleteMesure($shop_mesures_id)
	{
		$shop_mesures_id = intval($shop_mesures_id);
		$oShop_Measure = Core_Entity::factory('Shop_Measure')->find($shop_mesures_id);

		if(!is_null($oShop_Measure->id))
		{
			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_MESURE';
				$cache->DeleteCacheItem($cache_name, $oShop_Measure->id);
			}

			$oShop_Measure->markDeleted();

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Вставка информации о скидке
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['shop_shops_id'] идентификационный номер магазина
	 * <br />int $param['id'] идентификационный номер скидки
	 * <br />string $param['name'] наименование скидки
	 * <br />date $param['from'] с какого времени действует скидка
	 * <br />string $param['to'] по какое время действует скидка
	 * <br />int $param['is_active'] активна ли скидка в настоящий момент
	 * <br />numeric $param['percent'] процент скидки
	 * <br />string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['name'] = 'new';
	 * $param['from'] = '2008.08.20';
	 * $param['to'] = '2026.08.20';
	 * $param['is_active'] = 1;
	 * $param['percent'] = 20;
	 *
	 * $newid = $shop->InsertDiscount($param);
	 *
	 * // Распечатаем результат
	 * echo ($newid);
	 * ?>
	 * </code>
	 * @return возвращает идентификационный номер вставленной скидки
	 */
	function InsertDiscount($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if(!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Discount = Core_Entity::factory('Shop_Discount', $param['id']);

		$oShop_Discount->name = Core_Type_Conversion::toStr($param['name']);
		$oShop_Discount->start_datetime = Core_Type_Conversion::toStr($param['from']);
		$oShop_Discount->end_datetime = Core_Type_Conversion::toStr($param['to']);
		$oShop_Discount->active = Core_Type_Conversion::toInt($param['is_active']);
		$oShop_Discount->percent = Core_Type_Conversion::toFloat($param['percent']);
		$oShop_Discount->shop_id = Core_Type_Conversion::toInt($param['shop_shops_id']);

		if (is_null($oShop_Discount->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Discount->user_id = intval($param['users_id']);
		}

		$oShop_Discount->save();

		if (!is_null($param['id']) && class_exists('Cache'))
		{
			// Очистка файлового кэша
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_DISCOUNT';
			$cache->DeleteCacheItem($cache_name, $oShop_Discount->id);
		}

		return $oShop_Discount->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление информации о скидке
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификационный номер скидки
	 * <br />string $param['name'] наименование скидки
	 * <br />string $param['from'] с какого времени действует скидка
	 * <br />string $param['to'] по какое время действует скидка
	 * <br />boolean $param['is_active'] активна ли скидка в настоящий момент
	 * <br />numeric $param['percent'] процент скидки
	 * <br />string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления информации о скидке
	 */
	function UpdateDiscount($param)
	{
		return $this->InsertDiscount($param);
	}

	/**
	 * Получение информации о скидке
	 *
	 * @param int $shop_discount_id идентификационный номер скидки
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_discount_id = 2;
	 *
	 * $row = $shop->GetDiscount($shop_discount_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки скидки
	 */
	function GetDiscount($shop_discount_id, $param = array())
	{
		$shop_discount_id = intval($shop_discount_id);
		$param = Core_Type_Conversion::toArray($param);

		if (isset($this->CacheGetDiscount[$shop_discount_id]))
		{
			return $this->CacheGetDiscount[$shop_discount_id];
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_DISCOUNT';

			if ($in_cache = $cache->GetCacheContent($shop_discount_id, $cache_name))
			{
				$this->CacheGetDiscount[$shop_discount_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		$oShop_Discount = Core_Entity::factory('Shop_Discount')->find($shop_discount_id);

		$this->CacheGetDiscount[$shop_discount_id] = $row = !is_null($oShop_Discount->id)
			? $this->getArrayShopDiscount($oShop_Discount)
			: FALSE;

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_DISCOUNT';
			$cache->Insert($shop_discount_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	 * Получение списка скидок для магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $resource = $shop->GetShopDiscount($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource ресурс в данными о скидке
	 */
	function GetShopDiscount($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_discount_id'),
				array('shop_id', 'shop_shops_id'),
				array('name', 'shop_discount_name'),
				array('start_datetime', 'shop_discount_from'),
				array('end_datetime', 'shop_discount_to'),
				array('active', 'shop_discount_is_active'),
				array('percent', 'shop_discount_percent'),
				array('user_id', 'users_id')
			)
			->from('shop_discounts')
			->where('shop_id', '=', $shop_shops_id)
			->where('deleted', '=', 0);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление скидки
	 *
	 * @param int $shop_discount_id идентификационный номер скидки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_discount_id = 1;
	 *
	 * $result = $shop->DeleteDiscount($shop_discount_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления скидки
	 */
	function DeleteDiscount($shop_discount_id)
	{
		$shop_discount_id = intval($shop_discount_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_DISCOUNT';
			$cache->DeleteCacheItem($cache_name, $shop_discount_id);
		}

		Core_Entity::factory('Shop_Discount', $shop_discount_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Вставка производителя
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shops_id'] идентификационный номер магазина
	 * - int $param['id'] идентификационный номер производителя
	 * - string $param['name'] имя (название) производителя
	 * - string $param['description'] краткая информация о производителе
	 * - string $param['image'] путь к логотипу производителя
	 * - string $param['small_image'] путь к малому логотипу производителя
	 * - int $param['order'] тип сортировки производителей
	 * - string $param['path'] путь к производителю
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - str $param['shop_producers_list_address'] адрес производителя
	 * - str $param['shop_producers_list_phone'] телефон производителя
	 * - str $param['shop_producers_list_fax'] факс производителя
	 * - str $param['shop_producers_list_site'] сайт производителя
	 * - str $param['shop_producers_list_email'] сайт производителя
	 * - str $param['shop_producers_list_kpp'] КПП производителя
	 * - str $param['shop_producers_list_ogrn'] ОГРН производителя
	 * - str $param['shop_producers_list_okved'] ОКВЭД производителя
	 * - str $param['shop_producers_list_bik'] БИК производителя
	 * - str $param['shop_producers_list_account'] Номер счета производителя
	 * - str $param['shop_producers_list_corr_account'] Номер корр. счета производителя
	 * - str $param['shop_producers_list_bank_name'] Название банка производителя
	 * - str $param['shop_producers_list_bank_address'] Адрес банка производителя
	 * - str $param['shop_producers_list_seo_title'] заголовок страницы
	 * - str $param['shop_producers_list_seo_description'] задание значения мета-тега description страницы
	 * - str $param['shop_producers_list_seo_keywords'] задание значения мета-тега keywords страницы
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shops_id'] = 1;
	 * $param['name'] = 'newprod';
	 *
	 * $newid = $shop->InsertProducer($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного производителя
	 */
	function InsertProducer($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if(!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Producer = Core_Entity::factory('Shop_Producer', $param['id']);

		if(!is_null($oShop_Producer->id) && isset($this->cache_producer[$oShop_Producer->id]))
		{
			// Удаляем из кэша
			unset($this->cache_producer[$oShop_Producer->id]);
		}

		isset($param['name']) && $oShop_Producer->name = $param['name'];
		isset($param['description']) && $oShop_Producer->description = $param['description'];
		isset($param['image']) && $oShop_Producer->image_large = $param['image'];
		isset($param['small_image']) && $oShop_Producer->image_small = $param['small_image'];
		isset($param['order']) && $oShop_Producer->sorting = $param['order'];
		isset($param['path']) && $oShop_Producer->path = $param['path'];
		isset($param['shop_producers_list_address']) && $oShop_Producer->address = $param['shop_producers_list_address'];
		isset($param['shop_producers_list_phone']) && $oShop_Producer->phone = $param['shop_producers_list_phone'];
		isset($param['shop_producers_list_fax']) && $oShop_Producer->fax = $param['shop_producers_list_fax'];
		isset($param['shop_producers_list_site']) && $oShop_Producer->site = $param['shop_producers_list_site'];
		isset($param['shop_producers_list_email']) && $oShop_Producer->email = $param['shop_producers_list_email'];
		isset($param['shop_producers_list_inn']) && $oShop_Producer->tin = $param['shop_producers_list_inn'];
		isset($param['shop_producers_list_kpp']) && $oShop_Producer->kpp = $param['shop_producers_list_kpp'];
		isset($param['shop_producers_list_kpp']) &&  $oShop_Producer->psrn = $param['shop_producers_list_ogrn'];
		isset($param['shop_producers_list_okpo']) && $oShop_Producer->okpo = $param['shop_producers_list_okpo'];
		isset($param['shop_producers_list_okved']) && $oShop_Producer->okved = $param['shop_producers_list_okved'];
		isset($param['shop_producers_list_bik']) && $oShop_Producer->bik = $param['shop_producers_list_bik'];
		isset($param['shop_producers_list_account']) && $oShop_Producer->current_account = $param['shop_producers_list_account'];
		isset($param['shop_producers_list_corr_account']) && $oShop_Producer->correspondent_account = $param['shop_producers_list_corr_account'];
		isset($param['shop_producers_list_bank_name']) && $oShop_Producer->bank_name = $param['shop_producers_list_bank_name'];
		isset($param['shop_producers_list_bank_address']) && $oShop_Producer->bank_address = $param['shop_producers_list_bank_address'];
		isset($param['shop_producers_list_seo_title']) && $oShop_Producer->seo_title = $param['shop_producers_list_seo_title'];
		isset($param['shop_producers_list_seo_description']) && $oShop_Producer->seo_description = $param['shop_producers_list_seo_description'];
		isset($param['shop_producers_list_seo_keywords']) && $oShop_Producer->seo_keywords = $param['shop_producers_list_seo_keywords'];
		isset($param['shops_id']) && $oShop_Producer->shop_id = $param['shops_id'];

		if(is_null($oShop_Producer->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Producer->user_id = $param['users_id'];
		}

		if(!is_null($oShop_Producer->id))
		{
			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_PRODUCER';
				$cache->DeleteCacheItem($cache_name, $oShop_Producer->id);
			}
		}

		$oShop_Producer->save();

		return $oShop_Producer->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление информации о производителе
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификационный номер производителя
	 * <br />string $param['name'] имя (название) производителя
	 * <br />string $param['description'] краткая информация о производителе
	 * <br />string $param['image'] путь к логотипу производителя
	 * <br />int $param['order'] порядок сортировки производителя
	 * <br />string $param['path'] путь к производителю
	 * - $param  int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления информации о производителе
	 */
	function UpdateProducer($param)
	{
		return $this->InsertProducer($param);
	}

	/**
	 * Получение информации о производителе
	 *
	 * @param int $shop_producers_list_id идентификационный номер производителя
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_producers_list_id = 2;
	 *
	 * $row = $shop->GetProducer($shop_producers_list_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки производителя
	 */
	function GetProducer($shop_producers_list_id, $param = array())
	{
		$shop_producers_list_id = intval($shop_producers_list_id);
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['cache_off']) &&
		(!defined('ALLOW_SHOP_MEM_CACHE_PRODUCER') || (defined('ALLOW_SHOP_MEM_CACHE_PRODUCER') && ALLOW_SHOP_MEM_CACHE_PRODUCER)))
		{
			if (isset($this->cache_producer[$shop_producers_list_id]))
			{
				return $this->cache_producer[$shop_producers_list_id];
			}
		}

		$cache_name = 'SHOP_PRODUCER';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($shop_producers_list_id, $cache_name))
			{
				if (!defined('ALLOW_SHOP_MEM_CACHE_PRODUCER') || (defined('ALLOW_SHOP_MEM_CACHE_PRODUCER') && ALLOW_SHOP_MEM_CACHE_PRODUCER))
				{
					// Сохраняем в кэше в памяти
					$this->cache_producer[$shop_producers_list_id] = $in_cache['value'];
				}

				return $in_cache['value'];
			}
		}

		$oShop_Producer = Core_Entity::factory('Shop_Producer')->find($shop_producers_list_id);

		if(is_null($oShop_Producer->id))
		{
			return FALSE;
		}

		$row = $this->getArrayShopProducer($oShop_Producer);

		if (!defined('ALLOW_SHOP_MEM_CACHE_PRODUCER') || (defined('ALLOW_SHOP_MEM_CACHE_PRODUCER') && ALLOW_SHOP_MEM_CACHE_PRODUCER))
		{
			$this->cache_producer[$shop_producers_list_id] = $row;
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($shop_producers_list_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	 * Удаление производителя
	 *
	 * @param int $shop_producers_list_id идентификационный номер производителя
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_producers_list_id = 1;
	 *
	 * $result = $shop->DeleteProducer($shop_producers_list_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return boolean true в случае успешного выполнения, false - в противном случае
	 */
	function DeleteProducer($shop_producers_list_id)
	{
		$shop_producers_list_id = intval($shop_producers_list_id);
		$oShop_Producer = Core_Entity::factory('Shop_Producer')->find($shop_producers_list_id);

		if (!is_null($oShop_Producer->id))
		{
			$oShop_Producer->markDeleted();

			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_PRODUCER';
				$cache->DeleteCacheItem($cache_name, $oShop_Producer->id);
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Вставка свойства товара
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_shops_id'] идентификатор магазина
	 * - int $param['id'] идентификатор свойства товаров
	 * - int $param['mesures_id'] идентификатор единицы измерения товара
	 * - string $param['name'] название свойства товара
	 * - string $param['xml_name'] наименование xml-тега
	 * - string $param['shop_list_of_properties_description'] описание допсвойства
	 * - int $param['type'] тип свойства товара
	 * <br />1 - Файл
	 * <br />2 - Список
	 * <br />3 - Большое текстовое поле
	 * <br />4 - Визуальный редактор
	 * <br />5 - Дата
	 * <br />6 - ДатаВремя
	 * <br />7 - Флажок
	 * - string $param['prefics'] префикс свойства товара (например: свыше, более, до ...)
	 * - string $param['default_value'] значение свойства товара, устанавливаемое по умолчанию
	 * - int $param['order'] порядок сортировки
	 * - int $param['list_id'] идентификатор списка, используемого для задания значениий доп. свойства
	 * - int $param['shop_list_of_properties_show_kind'] способ отображения в фильтре
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - string $param['shop_list_of_properties_cml_id'] идентификатор CommerceML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['name'] = 'newprop';
	 * $param['mesures_id'] = 28;
	 * $param['order'] = 999;
	 *
	 * $newid = $shop->InsertPropretyOfItems($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленного товара в случае успешной вставки, ложь при возникновении ошибки
	 */
	function InsertPropretyOfItems($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		$property_id = !isset($param['id'])
			? NULL
			: intval($param['id']);

		$shop_id = intval($param['shop_shops_id']);

		$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $shop_id);
		$oProperty = Core_Entity::factory('Property', $property_id);

		$oProperty->name = isset($param['name'])
			? $param['name']
			: '';

		isset($param['type']) && $oProperty->type = intval($param['type']);
		isset($param['order']) && $oProperty->sorting = intval($param['order']);
		isset($param['default_value']) && $oProperty->default_value = $param['default_value'];
		isset($param['xml_name']) && $oProperty->tag_name = preg_replace('/[^a-zA-Z0-9а-яА-ЯЁ.\-_]/u', '', $param['xml_name']);
		isset($param['list_id']) && $oProperty->list_id = intval($param['list_id']);
		isset($param['shop_list_of_properties_default_big_width']) && $oProperty->image_large_max_width = intval($param['shop_list_of_properties_default_big_width']);
		isset($param['shop_list_of_properties_default_big_height']) && $oProperty->image_large_max_height = intval($param['shop_list_of_properties_default_big_height']);
		isset($param['shop_list_of_properties_default_small_width']) && $oProperty->image_small_max_width = intval($param['shop_list_of_properties_default_small_width']);
		isset($param['shop_list_of_properties_default_small_height']) && $oProperty->image_small_max_height = intval($param['shop_list_of_properties_default_small_height']);

		$oProperty->guid = isset($param['shop_list_of_properties_cml_id'])
			? $param['shop_list_of_properties_cml_id']
			: Core_Guid::get();

		$oProperty->Shop_Item_Property->filter = Core_Type_Conversion::toInt($param['shop_list_of_properties_show_kind']);
		$oProperty->Shop_Item_Property->shop_measure_id = Core_Type_Conversion::toInt($param['mesures_id']);
		$oProperty->Shop_Item_Property->prefix = Core_Type_Conversion::toStr($param['prefics']);

		if (is_null($oProperty->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oProperty->user_id = $param['users_id'];
		}

		$oShop_Item_Property_List->add($oProperty);

		return $oProperty->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление свойства товара
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификатор свойства товаров
	 * <br />string $param['name'] название свойства товара
	 * <br />string $param['xml_name'] наименование xml-тега
	 * <br />int $param['order'] порядок сортировки
	 * <br />int $param['type'] тип свойства товара
	 * <br />int $param['type'] тип свойства товара
	 * <br />string $param['prefics'] префикс свойства товара (например: свыше, более, до ...)
	 * <br />string $param['default_value'] значение свойства товара, устанавливаемое по умолчанию
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return mixed идентификатор обновленной записи, ложь - если обновление не произведено
	 */
	function UpdatePropretyOfItems($param)
	{
		return $this->InsertPropretyOfItems($param);
	}

	/**
	 * Изменение порядка сортировки дополнительных свойств товара
	 *
	 * @param int $list_of_properties_id идентификатор дополнительного свойства
	 * @param int $list_of_properties_order порядок сортировки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $list_of_properties_id = 137;
	 * $list_of_properties_order = 900;
	 *
	 * $properties_id = $shop->UpdateOrderPropretyOfItems($list_of_properties_id,$list_of_properties_order);
	 *
	 * // Распечатаем результат
	 * echo $properties_id;
	 * ?>
	 * </code>
	 * @return mixed идентификатор записи, если обновление прошло успешно, иначе - ложь
	 */
	function UpdateOrderPropretyOfItems($list_of_properties_id, $list_of_properties_order)
	{
		throw new Core_Exception('Method UpdateOrderPropretyOfItems() does not allow');
	}

	/**
	 * Метод, осуществляющий извлечение свойства товара по идентификатору свойства товара
	 *
	 * @param int $shop_list_of_properties_id идентификационный номер свойства товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_list_of_properties_id = 137;
	 *
	 * $row = $shop->GetPropretyOfItems($shop_list_of_properties_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки свойства товара
	 */
	function GetPropretyOfItems($shop_list_of_properties_id)
	{
		$shop_list_of_properties_id = intval($shop_list_of_properties_id);

		$oProperty = Core_Entity::factory('Property')->find($shop_list_of_properties_id);

		return !is_null($oProperty->id)
			? $this->getArrayItemProperty($oProperty)
			: FALSE;
	}

	/**
	 * Получение значения дополнительного свойства для товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @param int $shop_list_of_properties_id идентификатор дополнительного свойства
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 * $shop_list_of_properties_id = 137;
	 *
	 * $row = $shop->GetValueItemProperty($shop_items_catalog_item_id, $shop_list_of_properties_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed информация о дополнительном свойстве товара или ложь - если свойство для товара не задано
	 */
	function GetValueItemProperty($shop_items_catalog_item_id, $shop_list_of_properties_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
		$shop_list_of_properties_id = intval($shop_list_of_properties_id);

		$oProperty = Core_Entity::factory('Property', $shop_list_of_properties_id);
		$aPropertyValues = $oProperty->getValues($shop_items_catalog_item_id);

		if (isset($aPropertyValues[0]))
		{
			return $this->getArrayItemPropertyValue($aPropertyValues[0]);
		}

		return FALSE;
	}

	/**
	 * Удаление свойства товара
	 *
	 * @param int $shop_list_of_properties_id идентификационный номер свойства товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_list_of_properties_id = 132;
	 *
	 * $result = $shop->DeletePropretyOfItems($shop_list_of_properties_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return resource возвращает результат удаления свойства товара
	 */
	function DeletePropretyOfItems($shop_list_of_properties_id)
	{
		$shop_list_of_properties_id = intval($shop_list_of_properties_id);

		Core_Entity::factory('Property', $shop_list_of_properties_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Формирование массива с информацией о заказе для вставки в заказы
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param int $site_users_id идентификатор пользователя сайта
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $site_users_id = 19;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $row = $shop->GetOrderInfoArray($shop_id, $site_users_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с данными заказа, ложь - если магазина нет
	 */
	function GetOrderInfoArray($shop_id, $site_users_id)
	{
		$shop_id = intval($shop_id);
		$site_users_id = intval($site_users_id);

		// Выбираем данные о магазине.
		$row_shop = $this->GetShop($shop_id);

		// Если выбрали данные, начинаем формирование заказа.
		if ($row_shop)
		{
			// Формируем массив с данными о заказе.
			$param = array();
			$param['site_user_id'] = $site_users_id;
			$param['shop_shops_id'] = $shop_id;

			// Данные о пользователе.
			if (class_exists('SiteUsers') && $site_users_id > 0)
			{
				$SiteUsers = & singleton('SiteUsers');

				$site_user_row = $SiteUsers->GetSiteUser($site_users_id);
				if ($site_user_row)
				{
					$param['shop_order_users_name'] = isset($_SESSION['site_users_name']) ?
					Core_Type_Conversion::toStr($_SESSION['site_users_name']) : Core_Type_Conversion::toStr($site_user_row['site_users_name']);

					$param['shop_order_users_surname'] = isset($_SESSION['site_users_surname']) ?
					Core_Type_Conversion::toStr($_SESSION['site_users_surname']) : Core_Type_Conversion::toStr($site_user_row['site_users_surname']);

					$param['shop_order_users_patronymic'] = isset($_SESSION['site_users_patronymic']) ?
					Core_Type_Conversion::toStr($_SESSION['site_users_patronymic']) : Core_Type_Conversion::toStr($site_user_row['site_users_patronymic']);

					$param['shop_order_users_email'] = isset($_SESSION['site_users_email']) ?
					Core_Type_Conversion::toStr($_SESSION['site_users_email']) : Core_Type_Conversion::toStr($site_user_row['site_users_email']);

					$param['shop_order_users_company'] = isset($_SESSION['site_users_company']) ?
					Core_Type_Conversion::toStr($_SESSION['site_users_company']) : Core_Type_Conversion::toStr($site_user_row['site_users_company']);

					$param['shop_order_users_phone'] = isset($_SESSION['site_users_phone']) ?
					Core_Type_Conversion::toStr($_SESSION['site_users_phone']) : Core_Type_Conversion::toStr($site_user_row['site_users_phone']);

					$param['shop_order_users_fax'] = isset($_SESSION['site_users_fax']) ?
					Core_Type_Conversion::toStr($_SESSION['site_users_fax']) : Core_Type_Conversion::toStr($site_user_row['site_users_fax']);

					$param['phone'] = isset($_SESSION['site_users_phone']) ?
					Core_Type_Conversion::toStr($_SESSION['site_users_phone']) : Core_Type_Conversion::toStr($site_user_row['site_users_phone']);

					$param['index'] = isset($_SESSION['index']) ?
					Core_Type_Conversion::toStr($_SESSION['index']) : Core_Type_Conversion::toStr($site_user_row['index']);
				}
			}
			else
			{
				$param['shop_order_users_name'] = Core_Type_Conversion::toStr($_SESSION['site_users_name']);
				$param['shop_order_users_surname'] = Core_Type_Conversion::toStr($_SESSION['site_users_surname']);
				$param['shop_order_users_patronymic'] = Core_Type_Conversion::toStr($_SESSION['site_users_patronymic']);
				$param['shop_order_users_email'] = Core_Type_Conversion::toStr($_SESSION['site_users_email']);
				$param['shop_order_users_company'] = Core_Type_Conversion::toStr($_SESSION['site_users_company']);
				$param['shop_order_users_phone'] = Core_Type_Conversion::toStr($_SESSION['site_users_phone']);
				$param['shop_order_users_fax'] = Core_Type_Conversion::toStr($_SESSION['site_users_fax']);
				$param['phone'] = Core_Type_Conversion::toStr($_SESSION['site_users_phone']);
				$param['index'] = Core_Type_Conversion::toStr($_SESSION['index']);
			}

			// Дата оформления.
			$param['order_date_time'] = date("Y-m-d H:i:s");

			// Данные об адресе доставки.
			$param['country_id'] = Core_Type_Conversion::toInt($_SESSION['country']);
			$param['location_id'] = Core_Type_Conversion::toInt($_SESSION['location']);
			$param['shop_city_id'] = Core_Type_Conversion::toInt($_SESSION['city']);
			$param['city_area_id'] = Core_Type_Conversion::toInt($_SESSION['city_area']);
			$param['address'] = Core_Type_Conversion::toStr($_SESSION['full_address']);

			$param['delivery_price'] = Core_Type_Conversion::toFloat($_SESSION['cond_of_delivery_price']);
			// Размер налога с доставки
			$param['delivery_price_tax'] = Core_Type_Conversion::toFloat($_SESSION['cond_of_delivery_price_tax']);

			// Дополнительная информация о заказе
			$param['system_information'] = Core_Type_Conversion::toStr($_SESSION['system_information']);

			$param['description'] = Core_Type_Conversion::toStr($_SESSION['description']);

			// Валюта - выбираем по умолчанию для магазина.
			$param['currency_id'] = Core_Type_Conversion::toInt($row_shop['shop_currency_id']);

			// Способ (условия) доставки.
			$param['shop_cond_of_delivery_id'] = Core_Type_Conversion::toInt($_SESSION['cond_of_delivery']);

			// Выбираем состояние заказа, указываемое по умолчанию.
			$row_default_order_status = $this->GetDefaultOrderStatus($shop_id);

			$param['shop_order_status_id'] = $row_default_order_status
				? $row_default_order_status['shop_order_status_id']
				: 0;

			// Дата изменения статуса устанвлвиается текущая
			$param['change_status_date'] = date('Y-m-d H:i:s');

			// Получаем стоимость заказа.
			$a_param = $this->SelectAllItemsFromCartForUser($shop_id, $site_users_id);

			// Учитываем скидку на сумму заказа, в т.ч. по купону.
			$discount = $this->GetOrderDiscountForSumAndCount($shop_id, $a_param['price'], $a_param['quantity'], Core_Type_Conversion::toStr($_SESSION['shop_coupon_text']));

			// Удаляем иноформацию о купоне и вычитаем 1 из количества купонов.
			if (isset($_SESSION['shop_coupon_text']))
			{
				$coupon_row = $this->GetCouponByText(Core_Type_Conversion::toStr($_SESSION['shop_coupon_text']), false, array('shop_shops_id' => $shop_id));
				if ($coupon_row)
				{
					if ($coupon_row['shop_coupon_count'] > 0)
					{
						$coupon_row['shop_coupon_count'] = $coupon_row['shop_coupon_count'] - 1;
					}

					$this->InsertCoupon($coupon_row);
				}
				unset($_SESSION['shop_coupon_text']);
			}

			if ($discount == -1)
			{
				$discount = 0;

				// Ошибка! Общая сумма не может быть отрицательной!
				$message = "Внимание, для заказа на сумму {$a_param['price']} была расчитана скидка превышающая сумму заказа, что недопустимо. Заказ оформлен без скидок";

				// Пишем в log файл ошибку с информацией о том, что скидка
				// больше суммы заказа.
				$EventsJournal = new EventsJournal();
				$EventsJournal->log_access(USER_NONE, $message, 4);
			}

			// Сумма заказа с доставкой за вычетов скидки
			$param['sum'] = $a_param['price'] - $discount + $param['delivery_price'];

			// Сумма заказа без скидки и доставки
			$param['sum_without_discount'] = $a_param['price'];

			// Размер скидки от суммы заказа
			$param['order_discount'] = $discount;

			// Рассчитываем скидку для налога: умножением суммы налога на размер скидки

			// Деление на 0!!!!
			//$param['order_discount_tax'] = $a_param['tax'] * (100 - ($a_param['price'] - $discount) * 100 / $a_param['price']) / 100;

			$a_param['price'] == 0 && $a_param['price'] = 1;

			$param['order_discount_tax'] = $a_param['tax'] * (100 - ($a_param['price'] - $discount) * 100 / $a_param['price']) / 100;

			return $param;
		}

		// Магазин отсутствует в базе
		return FALSE;
	}

	/**
	 * Вставка заказа, если платежная система не выбрана
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param int $site_users_id идентификатор пользователя сайта
	 * @param array $additional_param массив с дополнительными параметрами <br />
	 * $additional_param['description'] string описание для заказа
	 * $additional_param['system_information'] string системная информация для заказа
	 * @param string $xsl_name название xsl-шаблона (не обязательный параметр, передается при необходимости - вывести данные о заказе)
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $site_users_id = 19;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $order_id = $shop->InsertOrderWithoutSystemOfPay($shop_id, $site_users_id);
	 *
	 * // Распечатаем результат
	 * print_r ($order_id);
	 * ?>
	 * </code>
	 */
	function InsertOrderWithoutSystemOfPay($shop_id, $site_users_id, $additional_param = array(), $xsl_name = FALSE)
	{
		$shop_id = intval($shop_id);
		$site_users_id = intval($site_users_id);

		$additional_param = Core_Type_Conversion::toArray($additional_param);

		// Получаем информацию о заказе
		$param = $this->GetOrderInfoArray($shop_id, $site_users_id);

		if ($param)
		{
			// Данные о платежной системе пустые
			$param['system_of_pay_id'] = 0;
			$param['status_of_pay'] = 0;
			$param['date_of_pay'] = '';

			// Описание и системная информация
			$param['description'] = Core_Type_Conversion::toStr($additional_param['description']);
			$param['system_information'] = Core_Type_Conversion::toStr($additional_param['system_information']);

			// Записываем информацию в таблицу заказов
			if ($order_id = $this->InsertOrder($param))
			{
				// Заполняем таблицу с заказанными товарами
				$items_array = $this->GetItemsFromCart($site_users_id, $shop_id);

				$param_all_items = array();

				if ($items_array)
				{
					$param_array = array();
					$param_array['shop_id'] = $shop_id;
					$param_array['user_id'] = $site_users_id;

					$count_items_array = count($items_array);

					for ($i = 0; $i < $count_items_array; $i++)
					{
						$param_item = array();
						$param_item['shop_items_catalog_item_id'] = $items_array[$i]['shop_items_catalog_item_id'];
						$param_item['shop_order_id'] = $order_id;
						$param_item['quantity'] = $items_array[$i]['shop_cart_item_quantity'];

						// Выбираем наименование и артикул товара
						$row_item = $this->GetItem($param_item['shop_items_catalog_item_id']);

						// Если элемент существует - добавляем его в заказ
						if ($row_item)
						{
							$param_item['name'] = $row_item['shop_items_catalog_name'];
							$param_item['marking'] = $row_item['shop_items_catalog_marking'];
							// Получаем цену товара
							$param_item['price'] = $this->GetPriceForUser($site_users_id, $param_item['shop_items_catalog_item_id'], array(), array(
							'item_count' => $param_item['quantity']
							));

							if ($this->InsertOrderItems($param_item))
							{
								$param_all_items[] = $param_item;
								// Удаляем данные из корзины для пользователя
								$param_array['item_id'] = $items_array[$i]['shop_items_catalog_item_id'];
								$this->DeleteCart($param_array);
							}
						}
					}
				}

				/*
				 // нельзя так возвращать, т.к.
				 else
				 {
				 // Нет ни одного товара в заказе
				 return false;
				 }
				 */

				// Проверяем наличие переданного имени xsl-шаблона
				if ($xsl_name != FALSE)
				{
					// Вызываем метод формирования xml для заказа
					$this->GetOrderXml($param, $param_all_items, $xsl_name);
				}
				// Возвращаем id вставленной записи (id заказа)
				return $order_id;
			}
			else
			{
				// Ошибка вставки заказа в базу
				return FALSE;
			}
		}
		// Магазин отсутствует в базе
		return FALSE;
	}

	/**
	 * Формирование xml с данными о заказе, при заданном наименовании xsl-шаблона, осуществляет вывод данных
	 *
	 * @param array $param массив с данными о заказе
	 * @param array $param_item массив с данными о заказанных товарах
	 * @param string $xsl_name наименование xsl шаблона (не обязательный параметр, если не указан метод возврщает xml)
	 * @return string $xmlData дерево xml с данными о заказе
	 */
	function GetOrderXml($param, $param_item, $xsl_name = '')
	{
		$param = Core_Type_Conversion::toArray($param);
		$param_item = Core_Type_Conversion::toArray($param_item);
		$xsl_name = Core_Type_Conversion::toStr($xsl_name);

		// Формируем строковую переменную с данными xml
		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xmlData .= '<order_list>'."\n";

		// Формируем xml для переданных массивов
		$ExternalXml = new ExternalXml;

		$xmlData .= $ExternalXml->GenXml($param);
		// xml для заказанных товаров
		for ($i = 0; $i < count($param_item); $i++)
		{
			$xmlData .= '<order_item>' . "\n";
			$xmlData .= $ExternalXml->GenXml($param_item[$i]);
			$xmlData .= '</order_item>' . "\n";
		}
		$xmlData .= '</order_list>' . "\n";

		// Проверяем передано ли наименование xsl шаблона
		if ($xsl_name != '' && $xsl_name != FALSE)
		{
			$xsl = & singleton('xsl');
			// Обрабатываем xml и выводим
			echo $xsl->build($xmlData, $xsl_name);
		}
		else
		{
			return $xmlData;
		}

		return '';
	}

	/**
	 * Формирование xml о заказе по идентификатору заказа
	 *
	 * @param int $shop_order_id идентификатор заказа
	 * @param array $order_row массив с данными о заказе или false
	 * @param bool $need_user_info определяет, нужно ли добавить информацию о юзере в XML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 46;
	 *
	 * $xmlData = $shop->GetXmlForOrder($shop_order_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return string сформированный xml для заказа
	 */
	function GetXmlForOrder($shop_order_id, $order_row = FALSE, $need_user_info = TRUE)
	{
		if ($order_row)
		{
			// Данные уже переданы.
			$row_order = Core_Type_Conversion::toArray($order_row);
		}
		else
		{
			// Получаем данные из базы.
			$shop_order_id = intval($shop_order_id);

			$row_order = $this->GetOrder($shop_order_id);

			if (!$row_order)
			{
				return '';
			}
		}

		// Начинаем формирование xml для данного заказа.
		$xmlData = '<order_list id="' . Core_Type_Conversion::toInt($row_order['shop_order_id']) . '">' . "\n";

		$xmlData .= '<order_id>' . Core_Type_Conversion::toInt($row_order['shop_order_id']) . '</order_id>' . "\n";

		if ($need_user_info)
		{
			if (class_exists('SiteUsers'))
			{
				$SiteUsers = & singleton('SiteUsers');

				// ID пользователя.
				$xmlData .= '<user_id>' . str_for_xml($row_order['site_users_id']) . '</user_id>' . "\n";

				// Получаем xml для пользователя.
				$xmlData .= '<site_user_info>' . "\n";
				$user_xml = $SiteUsers->GetSiteUserXml($row_order['site_users_id']);
				$xmlData .= $user_xml;
				$xmlData .= '</site_user_info>' . "\n";
			}

			// Данные о пользователе берем из самого заказа!
			$xmlData .= '<site_users_surname>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_surname'])) . '</site_users_surname>' . "\n";
			$xmlData .= '<site_users_name>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_name'])) . '</site_users_name>' ."\n";
			$xmlData .= '<site_users_patronymic>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_patronymic'])) . '</site_users_patronymic>' . "\n";
			$xmlData .= '<site_users_email>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_email'])) . '</site_users_email>' . "\n";
			$xmlData .= '<site_users_company>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_company'])) . '</site_users_company>' . "\n";
			$xmlData .= '<site_users_phone>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_phone'])) . '</site_users_phone>' . "\n";
			$xmlData .= '<site_users_fax>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_fax'])) . '</site_users_fax>' . "\n";
			$xmlData .= '<shop_order_description>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_description'])) . '</shop_order_description>' . "\n";
			$xmlData .= '<shop_order_system_information>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_system_information'])) . '</shop_order_system_information>' . "\n";

			$xmlData .= '<site_user_fio>' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_surname'])) . ' ' .
			str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_name'])) . ' ' . str_for_xml(Core_Type_Conversion::toStr($row_order['shop_order_users_patronymic'])) . '</site_user_fio>' . "\n";
		}

		$row = $this->GetShop($row_order['shop_shops_id']);

		// Проверяем выбрали ли мы запись.
		if ($row)
		{
			$xmlData .= '<shop id="' . Core_Type_Conversion::toInt($row_order['shop_shops_id']) . '">' . "\n";

			// Старая структура XML (до GenXml4Shop) оставлена для совместимости
			// Информация об алиасе сайта.
			$site = & singleton('site');
			$site_alias = $site->GetCurrentAlias($row['site_id']);
			$xmlData .= '<site_alias_name>' . $site_alias . '</site_alias_name>' ."\n";

			// Получаем путь к магазину
			$Structure = & singleton('Structure');

			$shop_path = $Structure->GetStructurePath($row['structure_id'], 0);

			if ($shop_path != '/')
			{
				$shop_path = '/' . $shop_path;
			}

			$xmlData .= '<shop_name>' . str_for_xml($row['shop_shops_name']) . '</shop_name>' . "\n";
			$xmlData .= '<shop_description>' . str_for_xml($row['shop_shops_description']) . '</shop_description>' . "\n";
			$xmlData .= '<shop_path>' . str_for_xml($shop_path) . '</shop_path>' . "\n";
			$xmlData .= '<shop_shops_attach_eitem>' . str_for_xml($row['shop_shops_attach_eitem']) . '</shop_shops_attach_eitem>' . "\n";

			$xmlData .= $this->GenXml4Shop($row_order['shop_shops_id'], $row);

			$xmlData .= '</shop>' . "\n";
		}

		// GUID
		$xmlData .= '<shop_order_guid>' . str_for_xml($row_order['shop_order_guid']) . '</shop_order_guid>' . "\n";

		// IP
		$xmlData .= '<shop_order_ip>' . str_for_xml($row_order['shop_order_ip']) . '</shop_order_ip>' . "\n";

		// Адрес доставки.
		// Страна.
		$xmlData .= '<country_id>' . str_for_xml($row_order['shop_country_id']) . '</country_id>' . "\n";
		// Наименование страны.
		$row_country = $this->GetCountry($row_order['shop_country_id']);
		if ($row_country)
		{
			$xmlData .= '<country_name>' . str_for_xml($row_country['shop_country_name']) . '</country_name>' . "\n";
		}

		// Область.
		$xmlData .= '<location_id>' . str_for_xml($row_order['shop_location_id']) . '</location_id>' . "\n";

		// Наименование области.
		$row_location = $this->GetLocation($row_order['shop_location_id']);
		if ($row_location)
		{
			$xmlData .= '<location_name>' . str_for_xml($row_location['shop_location_name']) . '</location_name>';
		}

		// Город.
		$xmlData .= '<city_id>' . str_for_xml($row_order['shop_city_id']) . '</city_id>' . "\n";

		// Наименование города
		$row_city = $this->GetCity($row_order['shop_city_id']);

		if ($row_city)
		{
			$xmlData .= '<city_name>' . str_for_xml($row_city['shop_city_name']) . '</city_name>' . "\n";
		}

		// Район города
		$xmlData .= '<city_area_id>' . str_for_xml($row_order['shop_city_area_id']) . '</city_area_id>' . "\n";

		// Наименование района
		$result_city_area = $this->SelectCityArea($row_order['shop_city_area_id'], $row_order['shop_city_id']);
		if ($result_city_area)
		{
			$row_city_area = mysql_fetch_assoc($result_city_area);
			$xmlData .= '<city_area_name>' . str_for_xml($row_city_area['shop_city_area_name']) . '</city_area_name>' . "\n";
		}

		$xmlData .= '<index>' . str_for_xml($row_order['shop_order_index']) . '</index>' . "\n";
		$xmlData .= '<address>' . str_for_xml($row_order['shop_order_address']) . '</address>' . "\n";
		$xmlData .= '<phone>' . str_for_xml($row_order['shop_order_phone']) . '</phone>' . "\n";

		// Выбираем информацию о валюте
		$xmlData .= '<currency_id>' . str_for_xml($row_order['shop_currency_id']) . '</currency_id>' . "\n";

		// Получаем xml для валюты
		$cur_xml = $this->GetCurrencyXml($row_order['shop_currency_id']);

		if ($cur_xml)
		{
			$xmlData .= '<currency>' . "\n";
			$xmlData .= $cur_xml;
			$xmlData .= '</currency>' . "\n";
		}

		// Информация о доставке
		$xmlData .= '<cond_of_delivery_id>' . str_for_xml($row_order['shop_cond_of_delivery_id']) . '</cond_of_delivery_id>' . "\n";

		// Получаем xml c условиями и типом доставки
		$cod_xml = $this->GetCondOfDeliveryXml($row_order['shop_cond_of_delivery_id']);
		if ($cod_xml)
		{
			$xmlData .= '<delivery_type>'."\n";
			$xmlData .= $cod_xml;
			$xmlData .= '</delivery_type>'."\n";
		}

		// Цену доставки берем из таблицы заказов
		//$xmlData .= '<order_delivery_price>' . str_for_xml($row_order['shop_order_delivery_price']) . '</order_delivery_price>' . "\n";

		// Информация о статусе заказа
		$xmlData .= '<order_status id="' . str_for_xml($row_order['shop_order_status_id']) . '">' . "\n";

		$row_delivery = $this->GetOrdersStatus($row_order['shop_order_status_id']);

		if ($row_delivery)
		{
			$xmlData .= '<order_status_name>' . str_for_xml($row_delivery['shop_order_status_name']) . '</order_status_name>' . "\n";
			$xmlData .= '<order_status_description>' . str_for_xml($row_delivery['shop_order_status_description']) . '</order_status_description>' . "\n";
		}

		$shop_order_change_status_datetime = Core_Date::sql2datetime($row_order['shop_order_change_status_datetime']);
		$xmlData .= '<order_change_status_datetime>' . str_for_xml($shop_order_change_status_datetime) . '</order_change_status_datetime>'."\n";

		$xmlData .= "</order_status>\n";

		$date_time = Core_Date::sql2datetime($row_order['shop_order_date_time']);
		$xmlData .= '<date_time>' . str_for_xml($date_time) . '</date_time>' . "\n";

		$date = Core_Date::sql2date($row_order['shop_order_date_time']);
		$xmlData .= '<date>' . str_for_xml($date) . '</date>' . "\n";

		// Стоимость заказа
		$xmlData .= '<sum>' . str_for_xml($this->Round($this->GetOrderSum($row_order['shop_order_id']))) . '</sum>' . "\n";

		// Данные о платежной системе
		$xmlData .= '<system_of_pay_id>' . str_for_xml($row_order['shop_system_of_pay_id']) . '</system_of_pay_id>' . "\n";

		// Выбираем данные о платежной системе
		$row_system_of_pay = $this->GetSystemOfPay($row_order['shop_system_of_pay_id']);

		if ($row_system_of_pay)
		{
			$xmlData .= '<system_of_pay_name>' . str_for_xml($row_system_of_pay['shop_system_of_pay_name']) . '</system_of_pay_name>' . "\n";
			$xmlData .= '<system_of_pay_description>' . str_for_xml($row_system_of_pay['shop_system_of_pay_description']) . '</system_of_pay_description>' . "\n";
			$xmlData .= '<system_of_pay_is_active>' . str_for_xml($row_system_of_pay['shop_system_of_pay_is_active']) . '</system_of_pay_is_active>' . "\n";
		}

		// Статус оплаты
		$xmlData .= '<status_of_pay>' . str_for_xml($row_order['shop_order_status_of_pay']) . '</status_of_pay>' . "\n";

		// Дата оплаты
		$date = Core_Date::sql2datetime($row_order['shop_order_date_of_pay']);
		$xmlData .= '<date_of_pay>' . str_for_xml($date) . '</date_of_pay>' . "\n";

		// Описание и системная информация
		$xmlData .= '<description>' . str_for_xml($row_order['shop_order_description']) . '</description>' . "\n";
		$xmlData .= '<system_information>' . str_for_xml($row_order['shop_order_system_information']) . '</system_information>' . "\n";
		$xmlData .= '<sending_info>' . str_for_xml($row_order['shop_order_sending_info']) . '</sending_info>' . "\n";
		$xmlData .= '<order_cancel>' . $row_order['shop_order_cancel'] . '</order_cancel>' . "\n";
		$xmlData .= '<order_account_number>' . $row_order['shop_order_account_number'] . '</order_account_number>' . "\n";

		// Элементы заказа
		$xmlData .= '<order_items>' . "\n";
		$xmlData .= $this->GetXmlForItemsOrder(Core_Type_Conversion::toInt($row_order['shop_order_id']));
		$xmlData .= '</order_items>' . "\n";

		$xmlData .= '</order_list>' . "\n";

		// Возвращаем сформированный xml
		return $xmlData;
	}

	/**
	 * Формирование xml для товаров в заказе
	 *
	 * @param int $shop_order_id идентификатор заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 46;
	 *
	 * $xmlData = $shop->GetXmlForItemsOrder($shop_order_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return mixed xml для товаров в заказе, ложь, если нет ни одного товара
	 */
	function GetXmlForItemsOrder($shop_order_id)
	{
		$shop_order_id = Core_Type_Conversion::toInt($shop_order_id);

		// Получаем данные об элементах заказа
		$result_items_order = $this->GetAllItemsForOrder($shop_order_id);

		$order_row = $this->GetOrder($shop_order_id);

		// Проверяем выбрана ли хотя бы одна запись
		if ($result_items_order && $order_row)
		{
			$xmlData = '';
			$count_items_order = mysql_num_rows($result_items_order);
			$tax_sum = 0;

			/* В цикле формируем xml для элементов в заказе*/
			for ($i = 0; $i < $count_items_order; $i++)
			{
				$row_items_order = mysql_fetch_assoc($result_items_order);

				$xmlData .= '<items_order id="' . Core_Type_Conversion::toInt($row_items_order['shop_order_items_id']) . '">' . "\n";

				// ID товара
				$xmlData .= '<catalog_item_id>' . str_for_xml($row_items_order['shop_items_catalog_item_id']) . '</catalog_item_id>' . "\n";

				// Данные о товаре в заказе
				$xmlData .= '<order_items_quantity>' . Core_Type_Conversion::toFloat($row_items_order['shop_order_items_quantity']) . '</order_items_quantity>' . "\n";
				$xmlData .= '<order_items_price>' . $this->Round($row_items_order['shop_order_items_price']) . '</order_items_price>' . "\n";
				$xmlData .= '<order_items_name>' . str_for_xml($row_items_order['shop_order_items_name']) . '</order_items_name>' . "\n";
				$xmlData .= '<order_items_marking>' . str_for_xml($row_items_order['shop_order_items_marking']) . '</order_items_marking>' . "\n";
				$xmlData .= '<order_items_tax_rate>'.$this->Round($row_items_order['shop_tax_rate']).'</order_items_tax_rate>'."\n";

				// Расчитываем сумму налогов

				$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item', $row_items_order['shop_order_items_id']);

				// order_items_quantity * order_items_price div (100 + order_items_tax_rate) * order_items_tax_rate
				//$tax_sum += Core_Type_Conversion::toFloat($row_items_order['shop_order_items_quantity']) * $row_items_order['shop_order_items_price'] / (100 + Core_Type_Conversion::toFloat($row_items_order['shop_tax_rate'])) * Core_Type_Conversion::toFloat($row_items_order['shop_tax_rate']);
				$tax_sum += $oShop_Order_Item->getTax() * $oShop_Order_Item->quantity;

				// Получаем информацию о товаре
				$item_row = $this->GetItem($row_items_order['shop_items_catalog_item_id']);

				// Выводим для родительского товара
				if ($item_row && $item_row['shop_items_catalog_modification_id'])
				{
					// Получаем модификацию
					$result_parent_item = $this->GetItem($item_row['shop_items_catalog_modification_id']);

					if ($result_parent_item)
					{
						$xmlData .= '<parent_item>' . "\n";
						$xmlData .= $this->GenXml4Item(1, $result_parent_item, $order_row['site_users_id']);
						$xmlData .= '</parent_item>' . "\n";
					}
				}

				// Данные о товаре из каталога
				$catalog_xml = $this->GetItemXml($row_items_order['shop_items_catalog_item_id'], $order_row['site_users_id']);
				if ($catalog_xml)
				{
					$xmlData .= $catalog_xml;
				}
				$xmlData .= '</items_order>' . "\n";
			}

			$xmlData .= '<tax_sum>' . $tax_sum . '</tax_sum>' ."\n";

			// Возвращаем xml для товаров из заказа
			return $xmlData;
		}
		// Нет ни одного товара
		return FALSE;
	}

	/**
	 * Отправка писем о поступившем заказе пользователю и администратору
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param int $shop_order_id идентификатор заказа
	 * @param int $site_user_id идентификатор пользователя
	 * @param string $xsl_name_for_admin имя XSL шаблона для письма администратору
	 * @param string $xsl_name_for_user имя XSL шаблона для письма пользователю
	 * @param string $custom_email e-mail заказчика, если задан, то берется из этого параметра, а не из БД
	 * @param array $param массив дополнительных атрибутов
	 * - $param['admin-content-type'] - Content-Type для письма администратору. Возможные варианты: text и html, по умолчанию text.
	 * - $param['user-content-type'] - Content-Type для письма пользователю. Возможные варианты: text и html, по умолчанию text.
	 * - $param['admin-subject'] - тема письма администратору, по умолчанию "В Интернет магазин поступил заказ"
	 * - $param['user-subject'] - тема письма пользователю, по умолчанию "Информация о заказе"
	 * - $param['email_to'] - электронный адрес получателя (можно указать несколько адресов через запятую). Не обязательный параметр, по умолчанию - адрес куратора магазина.
	 * - $param['email_from'] - электронный адрес отправителя для письма пользователю (можно указать несколько адресов через запятую). Не обязательный параметр, по умолчанию - адрес куратора магазина.
	 * - $param['email_from_admin'] - электронный адрес отправителя для письма администратору (можно указать несколько адресов через запятую). Не обязательный параметр, по умолчанию - адрес куратора магазина.
	 * - $param['header'] - массив дополнительных заголовков для метода отправки письма kernel::SendMailWithFile()
	 * - $param['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	 * - $param['array_of_files'] - массив файлов для отправки методом kernel::SendMailWithFile
	 *
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * @return bool
	 */
	function SendMailAboutOrder($shop_id, $shop_order_id, $site_user_id, $xsl_name_for_admin = '', $xsl_name_for_user = '', $custom_email = FALSE, $param = array(), $external_propertys = array())
	{
		$shop_id = intval($shop_id);
		$shop_order_id = intval($shop_order_id);
		$xsl_name_for_admin = Core_Type_Conversion::toStr($xsl_name_for_admin);
		$xsl_name_for_user = Core_Type_Conversion::toStr($xsl_name_for_user);
		$EventJournal = & singleton('EventsJournal');

		if ($site_user_id == 0) // 0 или false
		{
			// на случай, если пользователь имеет ID 0, тогда преобразуем его в ложь
			if ($site_user_id !== 0 && class_exists('SiteUsers'))
			{
				$SiteUsers = & singleton('SiteUsers');
				$site_user_id = $SiteUsers->GetCurrentSiteUser();
			}
			else
			{
				$site_user_id = FALSE;
			}
		}
		else
		{
			$site_user_id = Core_Type_Conversion::toInt($site_user_id);
		}

		$kernel = & singleton('kernel');

		// Получаем информацию о магазине
		$row_shop = $this->GetShop($shop_id);

		// Определяем тип письма для администратора
		$admin_content_type = isset($param['admin-content-type']) && strtolower($param['admin-content-type']) == 'html'
			? 'text/html'
			: 'text/plain';

		// Определяем тип письма для пользователя
		$user_content_type = isset($param['user-content-type']) && strtolower($param['user-content-type']) == 'html'
			? 'text/html'
			: 'text/plain';

		if ($row_shop)
		{
			$site = & singleton('site');
			$site_alias = $site->GetCurrentAlias($row_shop['site_id']);

			// Получаем путь к магазину
			$Structure = & singleton('Structure');
			$shop_path = $Structure->GetStructurePath($row_shop['structure_id'], 0);

			// Флаги необходимости отправки писем
			$send_order_mail_admin = $row_shop['shop_shops_send_order_mail_admin'];
			$send_order_mail_user = $row_shop['shop_shops_send_order_mail_user'];

			// e-mail администрирующего магазины (или группа e-mail через запятую)
			$admin_mail = $row_shop['shop_shops_admin_mail'];

			$row_order = $this->GetOrder($shop_order_id);

			$date_str = $row_order
				? Core_Date::sql2datetime($row_order['shop_order_date_time'])
				: '';

			// Тема письма администратору
			if (isset($param['admin-subject']))
			{
				$admin_subject = Core_Type_Conversion::toStr($param['admin-subject']);
			}
			else
			{
				if (trim(Core_Type_Conversion::toStr($row_order['shop_order_account_number'])) != '')
				{
					$shop_order_account_number = trim(Core_Type_Conversion::toStr($row_order['shop_order_account_number']));
				}
				else
				{
					$shop_order_account_number = $shop_order_id;
				}

				$admin_subject = Core::_('Shop_Order.shop_order_admin_subject', $shop_order_account_number, $row_shop['shop_shops_name'], $date_str);
			}

			// Тема письма пользователю
			$user_subject = isset($param['user-subject'])
				? Core_Type_Conversion::toStr($param['user-subject'])
				: "Информация о заказе";

			// Проверяем необходимость отправки писем
			if ($send_order_mail_admin || $send_order_mail_user)
			{
				// Есть необходимость отправить письмо(а) - получаем xml для заказа
				$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
				$xmlData .= '<document_list>' . "\n";

				/* Вносим внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа*/
				if (isset($param['external_xml']))
				{
					$xmlData .= $param['external_xml'];
				}

				// Вносим в XML дополнительные теги из массива дополнительных параметров
				$ExternalXml = new ExternalXml;
				$xmlData .= $ExternalXml->GenXml($external_propertys);
				unset($ExternalXml);

				$xmlData .= $this->GetXmlForOrder($shop_order_id, false, true);
				$xmlData .= $this->GetXmlForItemsOrder($shop_order_id);

				// Получаем о файлах и пин-кодах, содержащихся в заказе

				// массив пин-кодов
				$array_of_pins = array();

				// массив файлов
				$array_of_files = isset($param['array_of_files']) && is_array($param['array_of_files'])
					? $param['array_of_files']
					: array();

				// Отправляем файлы и пин-коды, только если заказ оплачен.
				if ($row_order['shop_order_status_of_pay'] == 1)
				{
					// Получаем список товаров оформленного заказа
					$items_from_order_res = $this->GetOrderItems($shop_order_id);

					while ($items_from_order_row = mysql_fetch_assoc($items_from_order_res))
					{
						// Получаем информацию о товаре
						$item_data = $this->GetItem($items_from_order_row['shop_items_catalog_item_id']);

						// Если товар электронный
						if ($item_data['shop_items_catalog_type'] == 1)
						{
							// <shop_eitems shop_items_catalog_id="">
							$temp_shop_items_catalog_item_id = $item_data['shop_items_catalog_item_id'];
							// Открываем блок XML-документа для записи данных об электронных товарах
							$xmlData .= '<shop_eitems shop_items_catalog_id="' . $temp_shop_items_catalog_item_id . '">' . "\n";

							$error_array = array();

							// Цикл от 0 до кол-во каждого товара, списываем
							for ($item = 0; $item < $items_from_order_row['shop_order_items_quantity']; $item++)
							{
								/*// Получаем список всех сущностей электронного товара
								 $eitems_data_res = $this->GetEitemsForItem($item_data['shop_items_catalog_item_id']);*/

								$item_id = Core_Type_Conversion::toInt($item_data['shop_items_catalog_item_id']);

								$eitem_result = $this->GetEitemsForItem($item_id);
								$error = TRUE;

								// Пробегаемся по всем эл.товарам и добавляем информацию в массивы
								if (mysql_num_rows($eitem_result))// == 1)
								{
									while ($eitems_data_row = mysql_fetch_assoc($eitem_result))
									{
										if ($eitems_data_row['shop_eitem_count'] == -1
										|| $eitems_data_row['shop_eitem_count'] > 0)
										{
											$temp_shop_eitem_id = $eitems_data_row['shop_eitem_id'];

											$xmlData .= '<shop_eitem eitem_id="' . $temp_shop_eitem_id . '">' . "\n";

											$xmlData .= '<shop_eitem_name>' . $eitems_data_row['shop_eitem_name'] . '</shop_eitem_name>' . "\n";

											$xmlData .= '<shop_eitem_value>' . $eitems_data_row['shop_eitem_value'] . '</shop_eitem_value>' . "\n";

											$xmlData .= '<shop_eitem_count>' . $eitems_data_row['shop_eitem_count'] . '</shop_eitem_count>' . "\n";

											if ($eitems_data_row['shop_eitem_filename'] != '')
											{
												$xmlData .= '<shop_eitem_filename>' . $eitems_data_row['shop_eitem_filename'] . '</shop_eitem_filename>' . "\n";

												if ($row_shop['shop_shops_attach_eitem'])
												{
													$adding_string = '';

													// Получаем расширение файла
													$ext = Core_File::getExtension($eitems_data_row['shop_eitem_filename']);

													if ($ext != '')
													{
														$adding_string .= '.' . $ext;
													}

													// Получаем путь к файлу с данными
													$fname = CMS_FOLDER . UPLOADDIR . 'shop_' . $shop_id . '/eitems/item_catalog_' . $item_data['shop_items_catalog_item_id'] . '/' . $eitems_data_row['shop_eitem_id'] . $adding_string;

													if (is_file($fname))
													{
														$array_of_files[] = array(
														'filepath' => $fname,
														'filename' => $eitems_data_row['shop_eitem_filename']
														);
													}
												}

												$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item', $items_from_order_row['shop_order_items_id']);

												// Digital items
												$aShop_Order_Item_Digitals = $oShop_Order_Item->Shop_Order_Item_Digitals->findAll();
												foreach ($aShop_Order_Item_Digitals as $oShop_Order_Item_Digital)
												{
													// Ссылка сгенерирована
													if ($oShop_Order_Item_Digital->guid != '')
													{
														// Формируем путь к файлу
														$xmlData .= '<shop_eitem_resource>' . str_for_xml($oShop_Order_Item_Digital->guid) . '</shop_eitem_resource>' . "\n";
													}
												}

												// Ссылка сгенерирована
												/*if ($items_from_order_row['shop_order_items_eitem_resource'] != '')
												{
													// Формируем путь к файлу
													$xmlData .= '<shop_eitem_resource>' . str_for_xml($items_from_order_row['shop_order_items_eitem_resource']) . '</shop_eitem_resource>' . "\n";
												}*/
											}

											$xmlData .= '</shop_eitem>' . "\n";

											if ($eitems_data_row['shop_eitem_value'] != '')
											{
												// Если существует и пин-код и файл
												$array_of_pins[$item_data['shop_items_catalog_item_id']] = $eitems_data_row['shop_eitem_value'];
											}

											// Списывам файлы, если их количество не равно -1
											if ($eitems_data_row['shop_eitem_count'] != -1)
											{
												$update_shop_eitem_id = intval($eitems_data_row['shop_eitem_id']);

												$oShop_Item_Digital = Core_Entity::factory('Shop_Item_Digital')->find($update_shop_eitem_id);

												if (!is_null($oShop_Item_Digital->id))
												{
													$oShop_Item_Digital = $oShop_Item_Digital->count - 1;
													$oShop_Item_Digital->save();
												}
											}
											$error = FALSE;
											break;
										}
									}
								}

								// Ошибка. Произошла оплата товара, которого нет в распоряжении
								if ($error)
								{
									if (!isset($error_array[$item_id])
									|| !in_array($shop_order_id, $error_array[$item_id]))
									{
										$error_array[$item_id][] = $shop_order_id;
									}
								}
							}

							// Ошибки выводим отдельно, чтобы не было повторных выводов
							if (count($error_array) > 0)
							{
								// Получаем имя пользователя
								$login = isset($_SESSION['valid_user'])
									? Core_Type_Conversion::toStr($_SESSION['valid_user'])
									: USER_NONE;

								$mail_text = '';
								foreach ($error_array as $item_id => $shop_order_array)
								{
									foreach ($shop_order_array as $shop_order_id)
									{
										// Показываем сообщение пользователю
										Core_Message::show(Core::_('Shop_Item_Digital.payed_item', $item_id, $shop_order_id), 'error');

										$mail_text .= Core::_('Shop_Item_Digital.payed_item', $item_id, $shop_order_id) . "\r\n";
									}
								}

								// Записываем в журнал событий
								$EventJournal->log_access($login, $mail_text, 4, $send_email = TRUE);
							}

							// Закрываем блок с электронными товарами
							$xmlData .= '</shop_eitems>' . "\n";
						}
					}
				}

				// Формируем XML с инфомрацией о значениях электронных товаров, включенных в заказ
				$xmlData .= '</document_list>' . "\n";

				// Проверяем необходимость отправить письмо администратору
				if ($send_order_mail_admin && $xsl_name_for_admin != '')
				{
					// Адрес "ОТ КОГО" для администратора
					if (isset($param['email_from_admin']) && trim($param['email_from_admin']) != '')
					{
						// получаем адрес
						$emailfrom_admin = trim(Core_Type_Conversion::toStr($param['email_from_admin']));
					}
					elseif(trim($admin_mail) != '')
					{
						// получаем список адресов ( если их несколько)
						$mas_email = explode(',', $admin_mail);
						$emailfrom_admin = $mas_email[0];
					}
					else
					{
						$emailfrom_admin = EMAIL_TO;
					}

					$xsl = & singleton('xsl');
					$text_letter = $xsl->build($xmlData, $xsl_name_for_admin);

					/* Из текста письма обрезаем строку <?xml version="1.0" encoding="UTF-8"?>*/
					$text_letter = str_replace('<?xml version="1.0" encoding="UTF-8"?>' . "\n\n", '', $text_letter);

					// Добавляем переводы строк после тегов.
					$text_letter = str_replace(">", ">\n", $text_letter);

					if (isset($param['email_to']) && trim($param['email_to']) != '')
					{
						// получаем список адресов ( если их несколько)
						$mas_email = explode(',', $param['email_to']);
					}
					elseif(trim($admin_mail) != '')
					{
						// получаем список адресов ( если их несколько)
						$mas_email = explode(',', $admin_mail);
					}
					else
					{
						$mas_email[0] = EMAIL_TO;
					}

					$param_mail = array();
					$param_mail['header'] = !isset($param['header'])
						? array('X-HostCMS-Reason' => 'Order', 'Precedence' => 'bulk')
						: $param['header'];

					for ($i = 0; $i < count($mas_email); $i++)
					{
						if (valid_email(trim($mas_email[$i])))
						{
							$kernel->SendMailWithFile(trim($mas_email[$i]), $emailfrom_admin, $admin_subject, $text_letter, array(), $admin_content_type, $param_mail);

							// Задержка для обхода анти-спамфильтра Agava.
							sleep(1);
						}
					}
				}

				if ($send_order_mail_user && $xsl_name_for_user != '')
				{
					if (isset($param['email_from']) && trim($param['email_from']) != '')
					{
						// получаем адрес
						$emailfrom = trim(Core_Type_Conversion::toStr($param['email_from']));
					}
					elseif(trim($admin_mail) != '')
					{
						// получаем список адресов ( если их несколько)
						$mas_email = explode(',', $admin_mail);
						$emailfrom = $mas_email[0];
					}
					else
					{
						$emailfrom = EMAIL_TO;
					}

					$user_mail = FALSE;

					// Получаем mail прользователя
					if ($custom_email)
					{
						$user_mail = Core_Type_Conversion::toStr($custom_email);
					}
					else
					{
						if (class_exists('SiteUsers'))
						{
							$SiteUsers = & singleton('SiteUsers');
							$user_list = $SiteUsers->GetListPrimaryProperties($site_user_id);
							if ($user_list)
							{
								$user_mail = $user_list[1];
							}
						}
						else
						{
							$user_mail = FALSE;
						}
					}

					if ($user_mail != FALSE && valid_email(trim($user_mail)))
					{
						$xsl = & singleton('xsl');
						$text_letter = $xsl->build($xmlData, $xsl_name_for_user);

						/* Из текста письма обрезаем строку <?xml version="1.0" encoding="UTF-8"?>*/
						$text_letter = str_replace('<?xml version="1.0" encoding="UTF-8"?>'."\n\n", '', $text_letter);

						// Добавляем переводы строк после тегов.
						$text_letter = str_replace(">", ">\n", $text_letter);

						$param_mail = array();
						if (!isset($param['header']))
						{
							$param_mail['header'] = array('X-HostCMS-Reason' => 'OrderConfirm');
						}
						else
						{
							$param_mail['header'] = $param['header'];
						}

						$kernel->SendMailWithFile(trim($user_mail), $emailfrom, $user_subject, $text_letter, $array_of_files, $user_content_type, $param_mail);
					}
				}
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Вставка информации о заказе
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор заказа
	 * - int $param['shop_shops_id'] идентификатор магазина
	 * - int $param['location_id'] идентификатор области
	 * - int $param['country_id'] идентификатор страны
	 * - int $param['shop_city_id'] идентификатор города
	 * - int $param['city_area_id'] идентификатор района
	 * - int $param['shop_cond_of_delivery_id'] идентификатор способа доставки
	 * - float $param['delivery_price'] стоимость доставки
	 * - int $param['site_user_id'] идентификатор пользователя
	 * - int $param['shop_order_status_id'] идентификатор статуса заказа
	 * - int $param['currency_id'] идентификатор валюты
	 * - int $param['shop_system_of_pay_id'] идентификатор платежной системы
	 * - str $param['order_date_time'] дата заказа
	 * - int $param['status_of_pay'] статус оплаты
	 * - str $param['date_of_pay'] дата оплаты
	 * - str $param['address'] адрес доставки
	 * - int $param['index'] почтовый индекс
	 * - str $param['phone'] телефон пользователя
	 * - str $param['description'] описание для заказа
	 * - int $param['shop_order_cancel'] заказ аннулирован пользователем
	 * - str $param['shop_order_users_name'] имя пользователя, сделавшего заказ
	 * - str $param['shop_order_users_surname'] фамилия пользователя, сделавшего заказ
	 * - str $param['shop_order_users_patronymic'] отчество пользователя, сделавшего заказ
	 * - str $param['shop_order_users_email'] электронный адрес пользователя, сделавшего заказ
	 * - str $param['shop_order_users_company'] название организации пользователя, сделавшего заказ
	 * - str $param['shop_order_users_fax'] факс пользователя, сделавшего заказ
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - str $param['change_status_date'] дата изменения статуса заказа
	 * - str $param['shop_order_guid'] GUID для заказа, если не передан генерируется автоматически
	 * - str $param['system_information'] Дополнительная информация о заказе
	 * - str $param['shop_order_sending_info'] Информация об отправлении
	 * - str $param['shop_order_ip'] IP-адрес заказчика
	 * - int $param['shop_order_unload'] статус выгрузки товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['order_date_time'] = '2008-08-20 17:53:47';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $newid = $shop->InsertOrder($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного заказа
	 */
	function InsertOrder($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$shop_order_id = $param['id'];

		$oShop_Order = Core_Entity::factory('Shop_Order', $param['id']);

		$shop_shops_id = Core_Type_Conversion::toInt($param['shop_shops_id']);

		!empty($shop_shops_id) && $oShop_Order->shop_id = $shop_shops_id;

		$oShop_Order->ip = isset($param['shop_order_ip'])
			? $param['shop_order_ip']
			: $_SERVER['REMOTE_ADDR'];

		isset($param['location_id']) && $oShop_Order->shop_country_location_id = $param['location_id'];
		isset($param['shop_order_unload']) && $oShop_Order->unloaded = $param['shop_order_unload'];
		isset($param['country_id']) && $oShop_Order->shop_country_id = $param['country_id'];
		isset($param['shop_city_id']) && $oShop_Order->shop_country_location_city_id = $param['shop_city_id'];
		isset($param['city_area_id']) && $oShop_Order->shop_country_location_city_area_id = $param['city_area_id'];
		if (isset($param['shop_cond_of_delivery_id']))
		{
			$shop_delivery_condition_id = intval($param['shop_cond_of_delivery_id']);
			$oShop_Order->shop_delivery_condition_id = $shop_delivery_condition_id;

			$shop_delivery_id = $shop_delivery_condition_id
				? intval(Core_Entity::factory('Shop_Delivery_Condition', $shop_delivery_condition_id)->shop_delivery_id)
				: 0;

			$oShop_Order->shop_delivery_id = $shop_delivery_id;
		}

		isset($param['site_user_id']) && $oShop_Order->siteuser_id = $param['site_user_id'];
		isset($param['shop_order_status_id']) && $oShop_Order->shop_order_status_id = $param['shop_order_status_id'];
		isset($param['currency_id']) && $oShop_Order->shop_currency_id = $param['currency_id'];
		isset($param['shop_system_of_pay_id']) && $oShop_Order->shop_payment_system_id = $param['shop_system_of_pay_id'];

		if(isset($param['shop_order_sending_info']))
		{
			$oShop_Order->delivery_information = $param['shop_order_sending_info'];
		}
		elseif(is_null($oShop_Order->id))
		{
			$oShop_Order->delivery_information = '';
		}

		// Дата оплаты
		if(isset($param['date_of_pay'])
		&& preg_match("'^([\d]{4})-([\d]{1,2})-([\d]{1,2}) ([\d]{1,2}):([\d]{1,2}):([\d]{1,2})'u", $param['date_of_pay']))
		{
			$oShop_Order->payment_datetime = $param['date_of_pay'];
			$param['status_of_pay'] = 1;
		}
		elseif (is_null($oShop_Order->id))
		{
			$oShop_Order->payment_datetime = '0000-00-00 00:00:00';
		}

		if (isset($param['status_of_pay']))
		{
			$oShop_Order->paid = Core_Type_Conversion::toInt($param['status_of_pay']);

			// Товар оплачен
			if($oShop_Order->paid)
			{
				if (is_null($oShop_Order->id))
				{
					$current_status = 0;
				}
				else
				{
					// Получаем текущий статус оплаченности
					$prew_order_row = $this->GetOrder($shop_order_id);
					$current_status = Core_Type_Conversion::toInt($prew_order_row['shop_order_status_of_pay']);
				}

				// Если товар еще не был оплачен
				if (!$current_status)
				{
					// Получаем информацию о магазине
					$shop_row = $this->GetShop($shop_shops_id);

					// Изменять остаток оплаченных товаров
					if ($shop_row && Core_Type_Conversion::toInt($shop_row['shop_shops_writeoff_payed_items']) > 0)
					{
						// Списываем товары со склада
						$this->ChangeItemsOfOrderRest($shop_order_id, TRUE);
					}
				}
			}
		}
		elseif (!$shop_order_id)
		{
			$oShop_Order->paid = '0';
		}

		// Дата заказа
		if (isset($param['order_date_time'])
		&& preg_match("'^([\d]{4})-([\d]{1,2})-([\d]{1,2}) ([\d]{1,2}):([\d]{1,2}):([\d]{1,2})'u", $param['order_date_time']))
		{
			$oShop_Order->datetime = $param['order_date_time'];
		}
		elseif (!$shop_order_id)
		{
			$oShop_Order->datetime = date("Y-m-d H:i:s");
		}

		// Дата изменения статуса заказа, при вставке устаналивается равной текущей
		if (isset($param['change_status_date'])
		&& preg_match("'^([\d]{4})-([\d]{1,2})-([\d]{1,2}) ([\d]{1,2}):([\d]{1,2}):([\d]{1,2})'u", $param['change_status_date']))
		{
			$oShop_Order->status_datetime = $param['change_status_date'];
		}
		elseif (is_null($oShop_Order->id))
		{
			$oShop_Order->status_datetime = date("Y-m-d H:i:s");
		}

		isset($param['address']) && $oShop_Order->address = $param['address'];
		isset($param['index']) && $oShop_Order->postcode = $param['index'];
		isset($param['phone']) && $oShop_Order->phone = $param['phone'];

		if(isset($param['description']))
		{
			$oShop_Order->description = $param['description'];
		}
		elseif (is_null($oShop_Order->id))
		{
			$oShop_Order->description = '';
		}

		if(isset($param['system_information']))
		{
			$oShop_Order->system_information = $param['system_information'];
		}
		elseif (is_null($oShop_Order->id))
		{
			$oShop_Order->system_information = '';
		}

		isset($param['shop_order_cancel']) && $oShop_Order->canceled = $param['shop_order_cancel'];
		isset($param['shop_order_users_name']) && $oShop_Order->name = $param['shop_order_users_name'];
		isset($param['shop_order_users_surname']) && $oShop_Order->surname = $param['shop_order_users_surname'];
		isset($param['shop_order_users_patronymic']) && $oShop_Order->patronymic = $param['shop_order_users_patronymic'];
		isset($param['shop_order_users_email']) && $oShop_Order->email = $param['shop_order_users_email'];
		isset($param['shop_order_users_company']) && $oShop_Order->company = $param['shop_order_users_company'];
		isset($param['shop_order_users_fax']) && $oShop_Order->fax = $param['shop_order_users_fax'];
		isset($param['shop_order_account_number']) && $oShop_Order->invoice = $param['shop_order_account_number'];

		if(isset($param['shop_order_guid']))
		{
			$oShop_Order->guid = $param['shop_order_guid'];
		}
		elseif (is_null($oShop_Order->id))
		{
			$oShop_Order->guid = Core_Guid::get();
		}

		// Устанавливаем пользователя центра администрирования только при добавлении записи
		if (is_null($oShop_Order->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Order->user_id = intval($param['users_id']);
		}

		$oShop_Order->save();

		if ($oShop_Order->invoice == '')
		{
			// Обновляем номер заказа
			$oShop_Order->invoice($oShop_Order->id)->save();
		}

		return $oShop_Order->id;
	}

	/**
	 * УСТАРЕВШИЙ метод, осуществляющий обновление заказа. См. InsertOrder()
	 *
	 * @param array $param ассоциативный массив параметров
	 * - $param int $param['id'] идентификатор заказа
	 * - $param int $param['shops_id'] идентификатор магазина
	 * - $param int $param['location_id'] идентификатор области
	 * - $param int $param['country_id'] идентификатор страны
	 * - $param int $param['city_id'] идентификатор города
	 * - $param int $param['city_area_id'] идентификатор района
	 * - $param int $param['cond_of_delivery_id'] идентификатор способа доставки
	 * - $param float $param['delivery_price'] стоимость доставки
	 * - $param int $param['users_id'] идентификатор пользователя
	 * - $param int $param['currency_id'] идентификатор валюты
	 * - $param int $param['system_of_pay_id'] идентификатор платежной системы
	 * - $param string $param['date_time'] дата заказа
	 * - $param int $param['status_of_pay'] статус оплаты
	 * - $param string $param['date_of_pay'] дата оплаты
	 * - $param string $param['address'] адрес доставки
	 * - $param int $param['index'] почтовый индекс
	 * - $param string $param['phone'] телефон пользователя
	 * - $param string $param['description'] описание для заказа
	 * - $param string $param['system_information'] системная информация для заказа
	 * - $param int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 *
	 * @return array возвращает результат обновления заказа
	 * @see InsertOrder()
	 */
	function UpdateOrder($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if(isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Order = Core_Entity::factory('Shop_Order', $param['id']);

		isset($param['shops_id']) && $oShop_Order->shop_id  = $param['shops_id'];
		isset($param['location_id']) && $oShop_Order->shop_country_location_id = $param['location_id'];
		isset($param['country_id']) && $oShop_Order->shop_country_id = $param['country_id'];
		isset($param['city_id']) && $oShop_Order->shop_country_location_city_id = $param['city_id'];
		isset($param['city_area_id']) && $oShop_Order->shop_country_location_city_area_id = $param['city_area_id'];
		isset($param['cond_of_delivery_id']) && $oShop_Order->shop_delivery_condition_id = $param['cond_of_delivery_id'];
		isset($param['users_id']) && $oShop_Order->siteuser_id = $param['users_id'];
		isset($param['currency_id']) && $oShop_Order->shop_currency_id = $param['currency_id'];
		isset($param['system_of_pay_id']) && $oShop_Order->shop_payment_system_id = $param['system_of_pay_id'];
		isset($param['date_time']) && $oShop_Order->datetime = $param['date_time'];
		isset($param['status_of_pay']) && $oShop_Order->paid = $param['status_of_pay'];
		isset($param['date_of_pay']) && $oShop_Order->payment_datetime = $param['date_of_pay'];
		isset($param['address']) && $oShop_Order->address = $param['address'];
		isset($param['index']) && $oShop_Order->postcode = $param['index'];
		isset($param['phone']) && $oShop_Order->phone = $param['phone'];
		isset($param['description']) && $oShop_Order->description = $param['description'];
		isset($param['system_information']) && $oShop_Order->system_information = $param['system_information'];

		// Устанавливаем пользователя центра администрирования только при добавлении записи
		if (is_null($oShop_Order->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Order->user_id = intval($param['users_id']);
		}

		$oShop_Order->save();

		return $oShop_Order->id;
	}

	/**
	 * Получение информации о заказах магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $param массив дополнительных параметров для выборки
	 * - $param['order'] поле, по которому осуществляем сортировку
	 * - $param['order_sort'] порядок сортировки (ASC - прямой, DESC - обратный)
	 * - $param['user_id'] идентификатор пользователя сайта (если необходимо выбрать заказы для пользователя)
	 * - $param['limit_from'] номер записи, с которой начинаем выборку (нумерация записей с 0)
	 * - $param['limit_count'] количество выбираемых записей
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $param['order_sort'] = 'ASC';
	 * $param['user_id'] = '';
	 * $param['limit_from'] = 0;
	 * $param['limit_count'] = '';
	 *
	 * $resource = $shop->GetAllOrders($shop_shops_id, $param);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed результат выборки запроса
	 */
	function GetAllOrders($shop_shops_id, $param = array())
	{
		$shop_shops_id = intval($shop_shops_id);
		$param = Core_Type_Conversion::toArray($param);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'shop_order_id'),
			array('shop_id', 'shop_shops_id'),
			'shop_country_id',
			array('shop_country_location_id', 'shop_location_id'),
			array('shop_country_location_city_id', 'shop_city_id'),
			array('shop_country_location_city_area_id', 'shop_city_area_id'),
			array('shop_delivery_condition_id', 'shop_cond_of_delivery_id'),
			array('siteuser_id', 'site_users_id'),
			array('name', 'shop_order_users_name'),
			array('surname', 'shop_order_users_surname'),
			array('patronymic', 'shop_order_users_patronymic'),
			array('email', 'shop_order_users_email'),
			array('company', 'shop_order_users_company'),
			array('fax', 'shop_order_users_fax'),
			'shop_order_status_id',
			'shop_currency_id',
			array('shop_payment_system_id', 'shop_system_of_pay_id'),
			array('datetime', 'shop_order_date_time'),
			array('paid', 'shop_order_status_of_pay'),
			array('payment_datetime', 'shop_order_date_of_pay'),
			array('address', 'shop_order_address'),
			array('postcode', 'shop_order_index'),
			array('phone', 'shop_order_phone'),
			array('description', 'shop_order_description'),
			array('system_information', 'shop_order_system_information'),
			array('canceled', 'shop_order_cancel'),
			array('user_id', 'users_id'),
			array('invoice', 'shop_order_account_number'),
			array('status_datetime', 'shop_order_change_status_datetime'),
			array('guid', 'shop_order_guid'),
			array('delivery_information', 'shop_order_sending_info'),
			array('ip', 'shop_order_ip'),
			array('unloaded', 'shop_order_unload')
		)
		->from('shop_orders')
		->where('shop_orders.deleted', '=', 0);

		if (isset($param['order']))
		{
			if (!isset($param['order_sort']))
			{
				$param['order_sort'] = 'ASC';
			}
			$queryBuilder->orderBy($param['order'], $param['order_sort']);
		}

		if (isset($param['user_id']))
		{
			$queryBuilder->where('siteuser_id', '=', $param['user_id']);
		}

		if (isset($param['limit_from']) && isset($param['limit_count']))
		{
			$queryBuilder->limit($param['limit_from'], $param['limit_count']);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Выборка количества заказов для магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина+
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetCountOrder($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed количество заказов для магазина или ложь при возникновении ошибки
	 */
	function GetCountOrder($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$aShop_Orders = Core_Entity::factory('Shop_Order')->getByShopId($shop_shops_id);
		return count($aShop_Orders);
	}

	/**
	 * Извлечение заказа по его ID
	 *
	 * @param int $shop_order_id идентификационный номер заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 46;
	 *
	 * $row = $shop->GetOrder($shop_order_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки заказа
	 */
	function GetOrder($shop_order_id)
	{
		$shop_order_id = intval($shop_order_id);

		$oShop_Order = Core_Entity::factory('Shop_Order')->find($shop_order_id);

		if (!is_null($oShop_Order->id))
		{
			return $this->getArrayShopOrder($oShop_Order);
		}

		return FALSE;
	}

	/**
	 * Извлечение заказа по его GUID
	 *
	 * @param int $shop_order_guid идентификационный номер заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_guid = '22345200-abe8-4f60-90c8-0d43c5f6c0f6';
	 *
	 * $row = $shop->GetOrderByGuid($shop_order_guid);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки заказа
	 */
	function GetOrderByGuid($shop_order_guid)
	{
		$oShop_Order = Core_Entity::factory('Shop_Order')->getByGuid($shop_order_guid);

		if (!is_null($oShop_Order))
		{
			return $this->getArrayShopOrder($oShop_Order);
		}

		return FALSE;
	}

	/**
	 * Расчет суммы заказа
	 *
	 * @param int $shop_order_id идентификатор заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 46;
	 *
	 * $sum = $shop->GetOrderSum($shop_order_id);
	 *
	 * // Распечатаем результат
	 * echo $sum;
	 * ?>
	 * </code>
	 * @return int сумма заказа
	 */
	function GetOrderSum($shop_order_id)
	{
		$shop_order_id = intval($shop_order_id);
		$result = $this->GetOrderItems($shop_order_id);
		$sum = 0;
		while ($row = mysql_fetch_assoc($result))
		{
			$sum += $row['shop_order_items_price'] * $row['shop_order_items_quantity'];
		}

		return $sum;
	}

	/**
	 * Удаление заказа
	 *
	 * @param int $shop_order_id идентификационный номер заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 1;
	 *
	 * $result = $shop->DeleteOrder($shop_order_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return resource возвращает результат удаления заказа
	 */
	function DeleteOrder($shop_order_id)
	{
		$shop_order_id = intval($shop_order_id);

		return Core_Entity::factory('Shop_Order', $shop_order_id)->markDeleted();
	}

	/**
	 * Получение заказанных товаров
	 *
	 * @param int $shop_order_id идентификатор заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 38;
	 *
	 * $array = $shop->GetAllItemsForOrder($shop_order_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($array))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed результат выборки в случае, если выбрана хотя бы одна запись, ложь - если нет ни одной записи
	 */
	function GetAllItemsForOrder($shop_order_id)
	{
		return $this->GetOrderItems($shop_order_id);
	}

	/**
	 * Вставка статуса заказа
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификационный номер статуса заказа
	 * <br />string $param['name'] название состояния доставки
	 * <br />string $param['description'] описание состояния доставки
	 * <br />string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['name'] = 'в пролете';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $newid = $shop->InsertOrderStatus($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного состояния доставки
	 */
	function InsertOrderStatus($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item', $param['id']);

		isset($param['name']) && $oShop_Order_Item->name = $param['name'];
		isset($param['description']) && $oShop_Order_Item->description = $param['description'];

		// Устанавливаем пользователя центра администрирования только при добавлении записи
		if (is_null($oShop_Order_Item->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Order_Item->user_id = intval($param['users_id']);
		}

		if(!is_null($oShop_Order_Item->id))
		{
			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_ORDER_STATUS';
				$cache->DeleteCacheItem($cache_name, $oShop_Order_Item->id);
			}
		}

		$oShop_Order_Item->save();

		return $oShop_Order_Item->id;
	}

	/**
	 * Получение информации о состоянии заказа
	 *
	 * @param int $shop_order_status_id идентификационный номер состояния заказа
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_status_id = 1;
	 *
	 * $row = $shop->GetOrdersStatus($shop_order_status_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки статуса заказа
	 */
	function GetOrdersStatus($shop_order_status_id, $param = array())
	{
		$shop_order_status_id = intval($shop_order_status_id);
		$param = Core_Type_Conversion::toArray($param);

		$cache_name = 'SHOP_ORDER_STATUS';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($shop_order_status_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oShop_Order_Status = Core_Entity::factory('Shop_Order_Status')->find($shop_order_status_id);
		if (!is_null($oShop_Order_Status->id))
		{
			$row = $this->getArrayShopOrderStatus($oShop_Order_Status);

			// Запись в файловый кэш
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache->Insert($oShop_Order_Status->id, $row, $cache_name);
			}

			return $row;
		}

		return FALSE;
	}

	/**
	 * Получение информации обо всех статусах заказаов
	 * @return mixed информация о статусах заказа (результат выборки)
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $shop->GetAllOrderStatus();
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 */
	function GetAllOrderStatus()
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id','shop_order_status_id'),
			array('name', 'shop_order_status_name'),
			array('description', 'shop_order_status_description'),
			array('user_id', 'users_id')
		)
		->from('shop_order_statuses')
		->where('deleted', '=', 0);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение статуса заказа, установленного по умолчанию
	 *
	 * @param int $shop_id идентфикатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $row = $shop->GetDefaultOrderStatus($shop_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixer результат выборки
	 */
	function GetDefaultOrderStatus($shop_id)
	{
		$shop_id = intval($shop_id);
		$oShop_Order_Status = Core_Entity::factory('Shop', $shop_id)->Shop_Order_Status;

		if ($oShop_Order_Status->id)
		{
			return $this->getArrayShopOrderStatus($oShop_Order_Status);
		}

		return FALSE;
	}

	/**
	 * Получение валюты, указанной по умолчанию для магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetCurrencyForShop($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed результат выборки или ложь, если запись не выбрана
	 */
	function GetCurrencyForShop($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$oShop_Currency = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Currency;

		if($oShop_Currency->id)
		{
			return $this->getArrayShopCurrency($oShop_Currency);
		}

		return FALSE;
	}

	/**
	 * Получение информации о базовой валюте
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $row = $shop->GetDefaultCurrency();
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed информация о базовой валюте или ложь, если базовой валюты не существует
	 */
	function GetDefaultCurrency()
	{
		$oShop_Currency = Core_Entity::factory('Shop_Currency')->getDefault();

		if (!is_null($oShop_Currency))
		{
			return $this->getArrayShopCurrency($oShop_Currency);
		}

		return FALSE;
	}

	/**
	 * Получение информации о валюте магазина
	 *
	 * @param int $shop_shops_id
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetShopCurrency($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed информация о валюте или ложь, если магазина не существует
	 */
	function GetShopCurrency($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		if (isset($this->g_shop_currency[$shop_shops_id]))
		{
			return $this->g_shop_currency[$shop_shops_id];
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_CURRENCY_FOR_SHOP';
			if (($in_cache = $cache->GetCacheContent($shop_shops_id, $cache_name)) && $in_cache)
			{
				$this->g_shop_currency[$shop_shops_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		$oCurrency = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Currency;
		if($oCurrency->id)
		{
			$this->g_shop_currency[$shop_shops_id] = $this->getArrayShopCurrency($oCurrency);

			if (class_exists('Cache'))
			{
				$cache->Insert($shop_shops_id, $this->g_shop_currency[$shop_shops_id], $cache_name);
			}

			return $this->g_shop_currency[$shop_shops_id];
		}

		return FALSE;
	}

	/**
	 * Удаление статуса заказа
	 *
	 * @param int $shop_order_status_id идентификационный номер статуса заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_status_id = 1;
	 *
	 * $result = $shop->DeleteOrderStatus($shop_order_status_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления статуса заказа
	 */
	function DeleteOrderStatus($shop_order_status_id)
	{
		$shop_order_status_id = intval($shop_order_status_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ORDER_STATUS';
			$cache->DeleteCacheItem($cache_name, $shop_order_status_id);
		}

		return Core_Entity::factory('Shop_Order_Status', $shop_order_status_id)->markDeleted();
	}

	/**
	 * Вставка налога
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_tax_id'] идентификационный номер налога
	 * - string $param['shop_tax_name'] название налога
	 * - double $param['shop_tax_rate'] ставка налога
	 * - int $param['shop_tax_is_in_price'] входит ли налог в цену (0 - не входит, 1 - входит)
	 * - int $param['shop_tax_cml_id'] CML идентификатор налога, необязательный параметр
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_tax_name'] = 'Новый налог';
	 * $param['shop_tax_rate'] = '18';
	 *
	 * $newid = $shop->InsertTax($param);
	 *
	 * // Распечатаем ID нового налога
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного налога
	 */
	function InsertTax($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if(isset($param['id']))
		{
			$tax_id = intval($param['id']);
		}
		elseif(isset($param['shop_tax_id']))
		{
			$tax_id = intval($param['shop_tax_id']);
		}

		if(!isset($tax_id) || !$tax_id)
		{
			$tax_id = NULL;
		}

		$oShop_Tax = Core_Entity::factory('Shop_Tax', $tax_id);

		// Старый способ указания
		isset($param['name']) && $oShop_Tax->name = $param['name'];
		isset($param['shop_tax_name']) && $oShop_Tax->name = $param['shop_tax_name'];

		isset($param['rate']) && $param['rate'] > 0 && $oShop_Tax->rate = floatval($param['rate']);
		isset($param['shop_tax_rate']) && $oShop_Tax->rate = floatval($param['shop_tax_rate']);

		// Старый способ указания
		isset($param['is_in_price']) && $oShop_Tax->tax_is_included = intval($param['is_in_price']);

		isset($param['shop_tax_is_in_price']) && $oShop_Tax->tax_is_included = intval($param['shop_tax_is_in_price']);

		// Идентификатор налога CommerceML
		isset($param['shop_tax_cml_id']) && $oShop_Tax->guid = $param['shop_tax_cml_id'];

		is_null($oShop_Tax->id) && isset($param['users_id']) && $param['users_id'] && $oShop_Tax->user_id = intval($param['users_id']);

		$oShop_Tax->save();
		return $oShop_Tax->id;
	}

	/**
	 * Обновление информации о налоге. Рекомендуется использовать InsertTax()
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_tax_id'] идентификационный номер налога
	 * - string $param['shop_tax_name'] название налога
	 * - double $param['shop_tax_rate'] ставка налога
	 * - int $param['shop_tax_is_in_price'] входит ли налог в цену
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_tax_id'] = 123;
	 * $param['shop_tax_rate'] = 10;
	 *
	 * $newid = $shop->UpdateTax($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return возвращает результат обновления налога
	 * @see InsertTax()
	 */
	function UpdateTax($param)
	{
		return $this->InsertTax($param);
	}

	/**
	 * Получение информации о налоге
	 *
	 * @param int $shop_tax_id идентификационный номер налога
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_tax_id = 2;
	 *
	 * $row = $shop->GetTax($shop_tax_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки налога
	 */
	function GetTax($shop_tax_id)
	{
		$shop_tax_id = intval($shop_tax_id);

		if (isset($this->CacheGetTax[$shop_tax_id]))
		{
			return $this->CacheGetTax[$shop_tax_id];
		}

		$oShop_Tax = Core_Entity::factory('Shop_Tax')->find($shop_tax_id);

		$this->CacheGetTax[$shop_tax_id] = !is_null($oShop_Tax->id)
			? $this->getArrayShopTax($oShop_Tax)
			: FALSE;

		return $this->CacheGetTax[$shop_tax_id];
	}

	/**
	 * Получение информации о налогах
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $shop->GetAllTax();
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource информация о налогах (результат запроса)
	 */
	function GetAllTax()
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'shop_tax_id'),
			array('name', 'shop_tax_name'),
			array('rate', 'shop_tax_rate'),
			array('tax_is_included', 'shop_tax_is_in_price'),
			array('guid', 'shop_tax_cml_id'),
			array('user_id', 'users_id')
		)
		->from('shop_taxes')
		->where('deleted', '=', 0);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление налога
	 *
	 * @param int $shop_tax_id идентификационный номер налога
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_tax_id = 3;
	 *
	 * $result = $shop->DeleteTax($shop_tax_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления налога
	 */
	function DeleteTax($shop_tax_id)
	{
		Core_Entity::factory('Shop_Tax', $shop_tax_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Вставка товара в заказ
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_order_items_id'] идентификатор обновляемой записи
	 * - int $param['shop_items_catalog_item_id'] идентификатор товара
	 * - int $param['shop_order_id'] идентификатор заказа, которому принадлежит товар
	 * - float $param['shop_order_items_quantity'] количество определенного товара в заказе
	 * - float $param['shop_order_items_price'] цена товара в заказе
	 * - string $param['shop_order_items_name'] наименование товара в заказе
	 * - string $param['shop_order_items_marking'] артикул товара в заказе
	 * - int $param['users_id'] идентификатор пользователя, создающего объект
	 * - int $param['shop_warehouse_id'] идентификатор склада
	 * - str $param['shop_order_items_eitem_resource'] уникальная ссылка для скачивания электронного товара
	 * - int $param['shop_eitem_id'] идентификатор сущности электронного товара. Указывается только для товаров магазина, имеющих тип "электронный товар"
	 * - int $param['shop_order_items_type'] флаг, указывающий тип товара (0 - обычный товар, 1 - доставка, 2 - пополнение счета)
	 * - int $param['shop_tax_rate'] сумма налога
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['shop_order_id'] = 47;
	 * $param['shop_order_items_quantity'] = 1;
	 * $param['shop_order_items_price'] = 500;
	 *
	 * $newid = $shop->InsertOrderItems($param);
	 *
	 * // Распечатаем результат
	 * echo  $newid;
	 *
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного товара в заказ
	 */
	function InsertOrderItems($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['shop_order_items_id']) || !$param['shop_order_items_id'])
		{
			$param['shop_order_items_id'] = NULL;
		}

		$shop_warehouse_id = Core_Type_Conversion::toInt($param['shop_warehouse_id']);

		$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item', $param['shop_order_items_id']);

		isset($param['shop_items_catalog_item_id']) && $oShop_Order_Item->shop_item_id = intval($param['shop_items_catalog_item_id']);
		isset($param['shop_order_id']) && $oShop_Order_Item->shop_order_id = intval($param['shop_order_id']);

		isset($param['shop_order_items_quantity']) && $oShop_Order_Item->quantity = floatval($param['shop_order_items_quantity']);

		// Извлекаем данные о товаре
		if (!is_null($oShop_Order_Item->shop_item_id) && $item_row = $this->GetItem($oShop_Order_Item->shop_item_id))
		{
			if (!$shop_warehouse_id)
			{
				$warehouse = & singleton('warehouse');
				$aWarehouseRow = $warehouse->GetDefaultWarehouse($item_row['shop_shops_id']);
				$shop_warehouse_id = Core_Type_Conversion::toInt($aWarehouseRow['shop_warehouse_id']);
			}

			// Извлекаем данные о налоге
			$tax_row = $this->GetTax($item_row['shop_tax_id']);

			// Получаем ставку налога для данного товара
			$shop_tax_rate = $tax_row
				? $tax_row['shop_tax_rate']
				: 0;
		}
		elseif (isset($param['shop_tax_rate']))
		{
			$shop_tax_rate = intval($param['shop_tax_rate']);
		}
		else
		{
			$shop_tax_rate = 0;
		}

		$oShop_Order_Item->rate = $shop_tax_rate;

		if (isset($param['shop_order_items_price']))
		{
			$price = $param['shop_order_items_price'];

			// shop_order_items_price содержит итоговую цену с налогом, исключаем налог из цены
			if ($shop_tax_rate)
			{
				$price = $this->Round(
					$price * 100 / (100 + $shop_tax_rate)
				);
			}

			$oShop_Order_Item->price = $price;
		}
		isset($param['shop_order_items_name']) && $oShop_Order_Item->name = $param['shop_order_items_name'];
		isset($param['shop_order_items_marking']) && $oShop_Order_Item->marking = $param['shop_order_items_marking'];
		isset($param['shop_order_items_type']) && $oShop_Order_Item->type = intval($param['shop_order_items_type']);

		isset($param['shop_order_items_eitem_resource']) && $oShop_Order_Item->hash = mb_substr($param['shop_order_items_eitem_resource'], 0, 65534);

		is_null($oShop_Order_Item->id) && isset($param['users_id']) && $param['users_id'] && $oShop_Order_Item->user_id = intval($param['users_id']);

		$oShop_Order_Item->save();

		//isset($param['shop_eitem_id']) && $oShop_Order_Item->shop_item_digital_id = intval($param['shop_eitem_id']);

		// Электронный товар, связанный с проданным электронным товаром
		if (isset($param['shop_eitem_id']))
		{
			$oShop_Order_Item_Digital = Core_Entity::factory('Shop_Order_Item_Digital');
			$oShop_Order_Item_Digital->shop_item_digital_id = $param['shop_eitem_id'];
			$oShop_Order_Item->add($oShop_Order_Item_Digital);
		}

		return $oShop_Order_Item->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление товара в заказе
	 *
	 * @param array $param ассоциативный массив параметров
	 * - $param  int $param['shop_order_items_id'] идентификатор обновляемой записи
	 * - $param  int $param['shop_items_catalog_item_id'] идентификатор товара
	 * - $param  int $param['shop_order_id'] идентификатор заказа, которому принадлежит товар
	 * - $param  int $param['shop_order_items_quantity'] количество определенного товара в заказе
	 * - $param  float $param['shop_order_items_price'] цена товара в заказе
	 * - $param  string $param['shop_order_items_name'] наименование товара в заказе
	 * - $param  string $param['shop_order_items_marking'] артикул товара в заказе
	 * @return mixed идентификатор обновленного заказа или false в случае неудачи
	 */
	function UpdateOrderItems($param)
	{
		return $this->InsertOrderItems($param);
	}

	/**
	 * Получение информации о товаре из заказа
	 *
	 * @param int $shop_order_items_id идентификационный номер товара в заказе
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_items_id = 99;
	 *
	 * $row = $shop->GetOrderItem($shop_order_items_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки товара в заказе
	 */
	function GetOrderItem($shop_order_items_id)
	{
		$shop_order_items_id = intval($shop_order_items_id);
		$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item')->find($shop_order_items_id);
		if(!is_null($oShop_Order_Item->id))
		{
			return $this->getArrayShopOrderItem($oShop_Order_Item);
		}

		return FALSE;
	}

	/**
	 * Извлечение товаров из заказа
	 *
	 * @param int $shop_order_id идентификационный номер заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 47;
	 *
	 * $resource = $shop->GetOrderItems($shop_order_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource ресурс с элементами заказа
	 */
	function GetOrderItems($shop_order_id, $limit1 = FALSE, $limit2 = FALSE)
	{
		$shop_order_id = intval($shop_order_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'shop_order_items_id'),
			array('shop_item_id', 'shop_items_catalog_item_id'),
			'shop_order_id',
			array('quantity', 'shop_order_items_quantity'),
			array(Core_QueryBuilder::expression('`price` + ROUND(`price` * `rate` / 100, 2)'), 'shop_order_items_price'),
			array('name', 'shop_order_items_name'),
			array('marking', 'shop_order_items_marking'),
			array('rate', 'shop_tax_rate'),
			array('user_id', 'users_id'),
			array('hash', 'shop_order_items_eitem_resource'),
			//array('shop_item_digital_id', 'shop_eitem_id'),
			array(Core_QueryBuilder::expression("'0'"), 'shop_eitem_id'),
			array('type', 'shop_order_items_type'),
			array('shop_warehouse_id', 'shop_warehouse_id')
		)
		->from('shop_order_items')
		->where('shop_order_id', '=', $shop_order_id)
		->where('deleted', '=', 0);

		if ($limit1 && $limit2)
		{
			$queryBuilder->limit($limit1,$limit2);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление товара в заказе
	 *
	 * @param int $shop_order_items_id идентификационный номер товара в заказе
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_items_id = 1;
	 *
	 * $result = $shop->DeleteOrderItems($shop_order_items_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления товара в заказе
	 */
	function DeleteOrderItems($shop_order_items_id)
	{
		Core_Entity::factory('Shop_Order_Item')->markDeleted();

		return TRUE;
	}

	/**
	 * Вставка информации о типе цены
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_shops_id'] идентификатор магазина
	 * - int $param['id'] идентификатор цены
	 * - string $param['name'] название цены
	 * - double $param['percent_to_basic'] процент по отношению к базовой цене
	 * - string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - string $param['shop_list_of_prices_cml_id'] CML ID цены, если не передано, устанавливается равным пустой строке
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['name'] = 'Праздничная';
	 * $param['percent_to_basic'] = 10;
	 * $param['shop_list_of_prices_cml_id'] = '00001';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $price_id = $shop->InsertPrice($param);
	 *
	 * // Распечатаем результат
	 * echo $price_id;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного типа цены, -2 в случае указания заведомо неверного процента к базовой (больше или равно 100.00)
	 */
	function InsertPrice($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Price = Core_Entity::factory('Shop_Price', $param['id']);
		$oShop_Price->name = Core_Type_Conversion::toStr($param['name']);
		$oShop_Price->percent = Core_Type_Conversion::toFloat($param['percent_to_basic']);
		$oShop_Price->siteuser_group_id = Core_Type_Conversion::toInt($param['user_group_id']);
		$oShop_Price->shop_id = Core_Type_Conversion::toInt($param['shop_shops_id']);
		$oShop_Price->guid = Core_Type_Conversion::toStr($param['shop_list_of_prices_cml_id']);

		if(is_null($oShop_Price->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Price->user_id = intval($param['users_id']);
		}

		// При импорте из CML идентификатор группы неизвестен
		if ($oShop_Price->siteuser_group_id > 0)
		{
			$queryBuilder = Core_QueryBuilder::select(array('COUNT(*)', 'count'))
				->from('shop_prices')
				->where('siteuser_group_id', '=', $oShop_Price->siteuser_group_id)
				->where('shop_id', '=', $oShop_Price->shop_id)
				->where('deleted', '=', '0');

			if (!is_null($oShop_Price->id))
			{
				$queryBuilder->where('id', '!=', $oShop_Price->id);
			}

			$aCount = $queryBuilder->execute()->asAssoc()->current();

			$count = $aCount['count'];
		}

		// Для данной группы цена еще не задана
		if ($oShop_Price->siteuser_group_id == 0 || $count == 0)
		{
			$oShop_Price->save();
			return $oShop_Price->id;
		}

		/* Цена уже задана - возвращаем -1, т.к. для группы может быть только одна цена*/
		return -1;
	}

	/**
	 * Устаревший метод, осуществляющий обновление типа цены
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор цены
	 * - string $param['name'] название цены
	 * - double $param['percent_to_basic'] процент по отношению к базовой цене
	 * - string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления типа цены
	 */
	function UpdatePrice($param)
	{
		return $this->InsertPrice($param);
	}

	/**
	 * Получение информации о типе цены
	 *
	 * @param int $shop_list_of_prices_id идентификационный номер типа цены
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_list_of_prices_id = 8;
	 *
	 * $row = $shop->GetPrice($shop_list_of_prices_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки типа цены
	 */
	function GetPrice($shop_list_of_prices_id, $param = array())
	{
		$shop_list_of_prices_id = intval($shop_list_of_prices_id);
		$param = Core_Type_Conversion::toArray($param);

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_LIST_PRICE';

			if ($in_cache = $cache->GetCacheContent($shop_list_of_prices_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oShop_Price = Core_Entity::factory('Shop_Price')->find($shop_list_of_prices_id);

		$row = !is_null($oShop_Price->id)
			? $this->getArrayShopPrice($oShop_Price)
			: FALSE;

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache->Insert($shop_list_of_prices_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	 * Получение списка цен для магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $resource = $shop->GetAllPricesForShop($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed информация о ценах для магазина при успешной выборке, ложь, если возникла ошибка или нет ни одной цены
	 */
	function GetAllPricesForShop($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id','shop_list_of_prices_id'),
			array('shop_id', 'shop_shops_id'),
			array('name', 'shop_list_of_prices_name'),
			array('percent', 'shop_list_of_prices_percent_to_basic'),
			array('siteuser_group_id', 'site_users_group_id'),
			array('guid', 'shop_list_of_prices_cml_id'),
			array('user_id', 'users_id')
		)
		->from('shop_prices')
		->where('shop_id', '=', $shop_shops_id)
		->where('deleted', '=', 0);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение информации о ценах, заданных для товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 159;
	 *
	 * $row = $shop->GetAllPricesForItem($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed информация о ценах товара (массив с данными о ценах), ложь, если возникла ошибка или нет ни одной цены
	 */
	function GetAllPricesForItem($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = Core_Type_Conversion::toInt($shop_items_catalog_item_id);

		if (isset($this->CacheGetAllPricesForItem[$shop_items_catalog_item_id]))
		{
			return $this->CacheGetAllPricesForItem[$shop_items_catalog_item_id];
		}

		$queryBuilder = Core_QueryBuilder::select(
				array('shop_prices.id','shop_list_of_prices_id'),
				array('shop_id', 'shop_shops_id'),
				array('name', 'shop_list_of_prices_name'),
				array('percent', 'shop_list_of_prices_percent_to_basic'),
				array('siteuser_group_id', 'site_users_group_id'),
				array('guid', 'shop_list_of_prices_cml_id'),
				array('user_id', 'users_id'),
				array('shop_item_prices.id', 'shop_prices_to_item_id'),
				array('shop_item_id', 'shop_items_catalog_item_id'),
				array('value', 'shop_prices_to_item_value')
			)
			->from('shop_prices')
			->join('shop_item_prices', 'shop_prices.id', '=', 'shop_price_id')
			->where('shop_item_id', '=', $shop_items_catalog_item_id)
			->where('shop_prices.deleted', '=', 0);

		$aResult = $queryBuilder->execute()->asAssoc()->result();

		// Существует хотя бы одна цена - формируем массив с информацией о ценах для товара
		if (count($aResult) > 0)
		{
			$this->CacheGetAllPricesForItem[$shop_items_catalog_item_id] = array();

			foreach ($aResult as $row)
			{
				$this->CacheGetAllPricesForItem[$shop_items_catalog_item_id][$row['shop_list_of_prices_id']] = $row;
			}
		}
		// Нет ни одной цены - возвращаем ложь
		else
		{
			$this->CacheGetAllPricesForItem[$shop_items_catalog_item_id] = FALSE;
		}

		// Возвращаем результат выборки
		return $this->CacheGetAllPricesForItem[$shop_items_catalog_item_id];
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров
	 * ценами для групп товаров. Заполнению подвергается массив
	 * $this->CacheGetSpecialPricesForItem[$shop_items_catalog_item_id]
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 */
	function FillMemCacheGetAllPricesForItem($mas_items_in)
	{
		$mas_items_in = Core_Type_Conversion::toArray($mas_items_in);

		if (count($mas_items_in) > 0)
		{
			// Меняем местами значения и ключи массива
			$aTmp = array_flip($mas_items_in);

			// Вычислить пересечение массивов, сравнивая ключи
			$aTmpIntersect = array_intersect_key($aTmp, $this->CacheGetAllPricesForItem);

			if (count($aTmpIntersect) != count($mas_items_in))
			{
				// Заполянем массив значениями FALSE
				foreach ($mas_items_in as $shop_items_catalog_item_id)
				{
					$this->CacheGetAllPricesForItem[$shop_items_catalog_item_id] = FALSE;
				}

				$queryBuilder = Core_QueryBuilder::select(
					array('shop_prices.id','shop_list_of_prices_id'),
					array('shop_id', 'shop_shops_id'),
					array('name', 'shop_list_of_prices_name'),
					array('percent', 'shop_list_of_prices_percent_to_basic'),
					array('siteuser_group_id', 'site_users_group_id'),
					array('guid', 'shop_list_of_prices_cml_id'),
					array('user_id', 'users_id'),
					array('shop_item_prices.id', 'shop_prices_to_item_id'),
					array('shop_item_id', 'shop_items_catalog_item_id'),
					array('value', 'shop_prices_to_item_value')
				)
				->from('shop_prices')
				->join('shop_item_prices', 'shop_prices.id', '=', 'shop_price_id')
				->where('shop_item_id', 'IN', $mas_items_in)
				->where('shop_prices.deleted', '=', 0);

				$aResult = $queryBuilder->execute()->asAssoc()->result();
				foreach($aResult as $row)
				{
					$this->CacheGetAllPricesForItem[$row['shop_items_catalog_item_id']][$row['shop_list_of_prices_id']] = $row;
				}
			}
		}
	}

	/**
	 * Удаление типа цены
	 *
	 * @param int $shop_list_of_prices_id идентификационный номер типа цены
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_list_of_prices_id = 1;
	 *
	 * $result = $shop->DeletePrice($shop_list_of_prices_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления типа цены
	 */
	function DeletePrice($shop_list_of_prices_id)
	{
		$shop_list_of_prices_id = intval($shop_list_of_prices_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_LIST_PRICE';
			$cache->DeleteCacheItem($cache_name, $shop_list_of_prices_id);
		}

		Core_Entity::factory('shop_prices', $shop_list_of_prices_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Вставка типа доставки
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_shops_id'] идентификатор магазина
	 * - int $param['id'] идентификатор типа доставки
	 * - string $param['name'] название типа доставки
	 * - string $param['description'] описание типа доставки
	 * - string $param['image'] путь к логотипу службы доставки
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - int $param['shop_type_of_delivery_order'] порядок сортировки элемента
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['name'] = 'Самовывоз';
	 * $param['shop_shops_id'] = 1;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 *	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 *}
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $newid = $shop->InsertTypeOfDelivery($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного типа доставки
	 */
	function InsertTypeOfDelivery($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Delivery = Core_Entity::factory('Shop_Delivery', $param['id']);
		$oShop_Delivery->name = $param['name'];
		$oShop_Delivery->description = $param['description'];
		$oShop_Delivery->image = $param['image'];
		$oShop_Delivery->shop_id = $param['shop_shops_id'];
		$oShop_Delivery->sorting = $param['shop_type_of_delivery_order'];

		if(is_null($oShop_Delivery->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Delivery->user_id = intval($param['users_id']);
		}

		return $oShop_Delivery->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление типа доставки
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор типа доставки
	 * - string $param['name'] название типа доставки
	 * - string $param['description'] описание типа доставки
	 * - string $param['image'] путь к логотипу службы доставки
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления типа доставки
	 */
	function UpdateTypeOfDelivery($param)
	{
		return $this->InsertTypeOfDelivery($param);
	}

	/**
	 * Получение информации о типе доставки
	 *
	 * @param int $shop_type_of_delivery_id идентификационный номер типа доставки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_type_of_delivery_id = 5;
	 *
	 * $row = $shop->GetTypeOfDelivery($shop_type_of_delivery_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки типа доставки
	 */
	function GetTypeOfDelivery($shop_type_of_delivery_id)
	{
		$shop_type_of_delivery_id = intval($shop_type_of_delivery_id);

		$oShop_Delivery = Core_Entity::factory('Shop_Delivery')->find($shop_type_of_delivery_id);

		if (!is_null($oShop_Delivery->id))
		{
			return $this->getArrayShopDelivery($oShop_Delivery);
		}

		return FALSE;
	}

	/**
	 * Удаление типа доставки
	 *
	 * @param int $shop_type_of_delivery_id идентификационный номер типа доставки
	 * @param int $shop_shops_id идентификатор сайта
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_type_of_delivery_id = 3;
	 * $shop_shops_id = 1;
	 *
	 * $result = $shop->DeleteTypeOfDelivery($shop_type_of_delivery_id, $shop_shops_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return boolean истина при удачном обновлении, ложь в обратном случае
	 */
	function DeleteTypeOfDelivery($shop_type_of_delivery_id, $shop_shops_id)
	{
		$shop_type_of_delivery_id = intval($shop_type_of_delivery_id);

		Core_Entity::factory('Shop_Delivery', $shop_type_of_delivery_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Вставку информации об условии доставки
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификатор условия доставки
	 * <br />string $param['name'] название условия доставки
	 * <br />int $param['type_of_delivery'] идентификатор типа доставки
	 * - $param['location'] идентификатор области
	 * <br />double $param['weight_from'] вес от
	 * <br />double $param['weight_to'] вес до
	 * <br />double $param['price_from'] цена заказа от
	 * <br />double $param['price_to'] цена заказа до
	 * <br />string $param['description'] описание условия доставки
	 * <br />double $param['price'] цена доставки
	 * <br />int $param['currency] идентификатор валюты цены доставки
	 * <br />int $param['shop_tax_id] идентификатор налога
	 * - string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['name'] = 'Из офиса';
	 * $param['type_of_delivery'] = 5;
	 *
	 * $newid = $shop->InsertCondOfDelivery($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного условия доставки
	 */
	function InsertCondOfDelivery($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			//$shop_cond_of_delivery_id = Core_Type_Conversion::toInt($param['id']);
			$param['id'] = NULL;
		}

		$oShop_Delivery_Condition = Core_Entity::factory('Shop_Delivery_Condition', $param['id']);
		$oShop_Delivery_Condition->name = Core_Type_Conversion::toStr($param['name']);
		$oShop_Delivery_Condition->weight_from = Core_Type_Conversion::toFloat($param['weight_from']);
		$oShop_Delivery_Condition->shop_delivery_id = Core_Type_Conversion::toInt($param['type_of_delivery']);
		$oShop_Delivery_Condition->shop_country_id = Core_Type_Conversion::toInt($param['country']);
		$oShop_Delivery_Condition->shop_location_id = Core_Type_Conversion::toInt($param['location']);
		$oShop_Delivery_Condition->shop_city_id = Core_Type_Conversion::toInt($param['city']);
		$oShop_Delivery_Condition->shop_city_area_id = Core_Type_Conversion::toInt($param['city_area']);
		$oShop_Delivery_Condition->weight_to = Core_Type_Conversion::toFloat($param['weight_to']);
		$oShop_Delivery_Condition->price_from = Core_Type_Conversion::toFloat($param['price_from']);
		$oShop_Delivery_Condition->price_to = Core_Type_Conversion::toFloat($param['price_to']);
		$oShop_Delivery_Condition->description = Core_Type_Conversion::toStr($param['description']);
		$oShop_Delivery_Condition->price = Core_Type_Conversion::toFloat($param['price']);
		$oShop_Delivery_Condition->shop_currency_id = Core_Type_Conversion::toInt($param['currency']);
		$oShop_Delivery_Condition->shop_tax_id = Core_Type_Conversion::toInt($param['shop_tax_id']);

		if(is_null($oShop_Delivery_Condition->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Delivery_Condition->user_id = intval($param['users_id']);
		}

		$oShop_Delivery_Condition->save();

		return $oShop_Delivery_Condition->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление условия доставки
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />int $param['id'] идентификатор условия доставки
	 * <br />string $param['name'] название условия доставки
	 * <br />int $param['type_of_delivery'] идентификатор типа доставки
	 * - $param['location'] идентификатор области
	 * <br />double $param['weight_from] вес от
	 * <br />double $param['weight_to'] вес до
	 * <br />double $param['price_from'] цена заказа от
	 * <br />double $param['price_to'] цена заказа до
	 * <br />string $param['description'] описание условия доставки
	 * <br />double $param['price'] цена доставки
	 * <br />int $param['currency] идентификатор валюты цены доставки
	 * <br />string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления условия доставки
	 */
	function UpdateCondOfDelivery($param)
	{
		return $this->InsertCondOfDelivery($param);
	}

	/**
	 * Получение информации об условии доставки
	 *
	 * @param int $shop_cond_of_delivery_id идентификационный номер условия доставки
	 * @param boolean $select_delivery_type флаг, указывающий на необходимость получить информацию о типе доставки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_cond_of_delivery_id = 1;
	 * $select_delivery_type = true;
	 *
	 * $row = $shop->GetCondOfDelivery($shop_cond_of_delivery_id, $select_delivery_type);
	 *
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array результат выборки условия доставки (+ информация о типе доставки при установленном флаге)
	 */
	function GetCondOfDelivery($shop_cond_of_delivery_id, $select_delivery_type = FALSE)
	{
		$shop_cond_of_delivery_id = intval($shop_cond_of_delivery_id);

		$DataBase = & singleton('DataBase');

		$oShop_Delivery_Condition = Core_Entity::factory('Shop_Delivery_Condition')->find($shop_cond_of_delivery_id);

		if (!is_null($oShop_Delivery_Condition->id))
		{
			return $select_delivery_type
				? $this->getArrayShopDeliveryCondition($oShop_Delivery_Condition) + $this->getArrayShopDelivery($oShop_Delivery_Condition->Shop_Delivery)
				: $this->getArrayShopDeliveryCondition($oShop_Delivery_Condition);
		}

		return FALSE;
	}

	/**
	 * Получение информации о всех условиях доставки
	 *
	 * @param array $param массив дополнительных параметров, влияющих на выборку
	 * <br / >$param['type_of_delivery_id'] int идентификатор типа доставок, для которого необходимо выбрать все условия
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $shop->GetAllCondOfDelivery($param=array());
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource результат выборки условий доставки
	 */
	function GetAllCondOfDelivery($param = array())
	{
		$param = Core_Type_Conversion::toArray($param);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_cond_of_delivery_id'),
				array('shop_delivery_id', 'shop_type_of_delivery_id'),
				array('shop_country_id', 'shop_country_id'),
				array('shop_country_location_id', 'shop_location_id'),
				array('shop_country_location_city_id', 'shop_city_id'),
				array('shop_country_location_city_area_id', 'shop_city_area_id'),
				array('name', 'shop_cond_of_delivery_name'),
				array('min_weight', 'shop_cond_of_delivery_weight_from'),
				array('max_weight', 'shop_cond_of_delivery_weight_to'),
				array('min_price', 'shop_cond_of_delivery_price_from'),
				array('max_price', 'shop_cond_of_delivery_price_to'),
				array('description', 'shop_cond_of_delivery_description'),
				array('price', 'shop_cond_of_delivery_price'),
				array('shop_currency_id', 'shop_currency_id'),
				array('user_id', 'users_id'),
				array('shop_tax_id', 'shop_tax_id')
			)
			->from('shop_delivery_conditions')
			->where('deleted', '=', 0)
			->orderBy('name');

		// Проверяем не передан ли в дополнительныхных условиях тип доставки
		if (isset($param['type_of_delivery_id']))
		{
			$queryBuilder->where('shop_delivery_id', '=', intval($param['type_of_delivery_id']));
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Формирование xml для условия доставки (в xml входит информация и о типе доставки, связанным с условием)
	 *
	 * @param int $shop_cond_of_delivery_id идентификатор условия доставки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_cond_of_delivery_id = 1;
	 *
	 * $xmlData = $shop->GetCondOfDeliveryXml($shop_cond_of_delivery_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return mixed xml для условия доставки, ложь, если информация не выбрана
	 */
	function GetCondOfDeliveryXml($shop_cond_of_delivery_id)
	{
		$shop_cond_of_delivery_id = intval($shop_cond_of_delivery_id);
		$row_cond_of_delivery = $this->GetCondOfDelivery($shop_cond_of_delivery_id, TRUE);

		// Выбрали информацию об условии и типе доставки
		if ($row_cond_of_delivery)
		{
			// Данные о типе доставки
			$xmlData = '<type_of_delivery>' . str_for_xml($row_cond_of_delivery['shop_type_of_delivery_id']) . '</type_of_delivery>' . "\n";
			$xmlData .= '<type_of_delivery_name>' . str_for_xml($row_cond_of_delivery['shop_type_of_delivery_name']) . '</type_of_delivery_name>' . "\n";
			$xmlData .= '<type_of_delivery_description>' . str_for_xml($row_cond_of_delivery['shop_type_of_delivery_description']) . '</type_of_delivery_description>' . "\n";
			$xmlData .= '<type_of_delivery_image>' . str_for_xml($row_cond_of_delivery['shop_type_of_delivery_image']) . '</type_of_delivery_image>' . "\n";
			// Данные об условиях доставки
			$xmlData .= '<cond_of_delivery_name>' . str_for_xml($row_cond_of_delivery['shop_cond_of_delivery_name']) . '</cond_of_delivery_name>' . "\n";
			$xmlData .= '<cond_of_delivery_description>' . str_for_xml($row_cond_of_delivery['shop_cond_of_delivery_description']) . '</cond_of_delivery_description>' . "\n";

			return $xmlData;
		}
		return FALSE;
	}

	/**
	 * Удаление условия доставки
	 *
	 * @param int $shop_cond_of_delivery_id идентификационный номер условия доставки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_cond_of_delivery_id = 2;
	 *
	 * $result = $shop->DeleteCondOfDelivery($shop_cond_of_delivery_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления условия доставки
	 */
	function DeleteCondOfDelivery($shop_cond_of_delivery_id)
	{
		$shop_cond_of_delivery_id = intval($shop_cond_of_delivery_id);
		Core_Entity::factory('Shop_Delivery_Condition', $shop_cond_of_delivery_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Вставка платежной системы
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор платежной системы
	 * - string $param['name'] название платежной системы
	 * - string $param['description'] описание платёжной системы
	 * - int $param['shop_shops_id'] идентификатор родительского магазина
	 * - int $param['is_active'] активна ли платежная система
	 * - string  $param['handler'] обработчик платежной системы
	 * - int $param['currency_id'] номер валюты
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - int $param['shop_system_of_pay_order'] Порядок сортировки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['name'] = 'New';
	 *
	 * $system_of_pay_id = $shop->InsertSystemOfPay($param);
	 *
	 * // Распечатаем результат
	 * echo $system_of_pay_id;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленной платёжной системы
	 */
	function InsertSystemOfPay($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if(!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Payment_System = Core_Entity::factory('Shop_Payment_System', $param['id']);

		$oShop_Payment_System->shop_id = Core_Type_Conversion::toInt($param['shop_shops_id']);
		$oShop_Payment_System->name = Core_Type_Conversion::toStr($param['name']);
		$oShop_Payment_System->shop_currency_id = Core_Type_Conversion::toInt($param['currency_id']);
		$oShop_Payment_System->shop_system_of_pay_description = Core_Type_Conversion::toStr($param['description']);
		$oShop_Payment_System->active = Core_Type_Conversion::toInt($param['is_active']);
		$oShop_Payment_System->sorting = Core_Type_Conversion::toInt($param['shop_system_of_pay_order']);

		if(is_null($oShop_Payment_System->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Payment_System->user_id = intval($param['users_id']);
		}

		$oShop_Payment_System->save();

		if(isset($param['handler']))
		{
			$system_of_pay_handler = Core_Type_Conversion::toStr($param['handler']);

			// Сохраняем обработчик платежной системы в файл
			$file_name = CMS_FOLDER . "hostcmsfiles/shop/pay/handler" . $oShop_Payment_System->id . '.php';

			if ($fp = @fopen($file_name, "w"))
			{
				fwrite($fp, $system_of_pay_handler);
				fclose($fp);
				// Устанавливаем права
				@chmod($file_name, CHMOD);
			}
			else
			{
				Core_Message::show(Core::_('Shop_Payment_System.file_error', $file_name) , 'error');
			}
		}

		return $oShop_Payment_System->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление платёжной системы
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор платёжной системы
	 * - string $param['name'] название платёжной системы
	 * - string $param['description'] описание платёжной системы
	 * - int $param['is_active'] активна ли платёжная система
	 * - string $param['handler'] обработчик платёжной системы
	 * - $param  int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления платёжной системы
	 */
	function UpdateSystemOfPay($param)
	{
		return $this->InsertSystemOfPay($param);
	}

	/**
	 * Выполнение обработчика платежной системы
	 *
	 * @param int $system_of_pay_id идентификатор платежной системы
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $system_of_pay_id = 4;
	 *
	 * $shop->ExecSystemsOfPayHandler($system_of_pay_id);
	 *
	 * ?>
	 * </code>
	 */
	function ExecSystemsOfPayHandler($system_of_pay_id)
	{
		$system_of_pay_id = intval($system_of_pay_id);

		$_SESSION['system_of_pay_id'] = $system_of_pay_id;

		// Формируем путь к файлу
		$handler_path = CMS_FOLDER . "hostcmsfiles/shop/pay/handler" . $system_of_pay_id . '.php';

		if (is_file($handler_path))
		{
			if (!class_exists('system_of_pay_handler'))
			{
				require_once($handler_path);
			}

			if (class_exists('system_of_pay_handler'))
			{
				$handler = new system_of_pay_handler();

				if (method_exists($handler, 'Execute'))
				{
					$handler->Execute();
				}

				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Выполнение обработчика платежной системы для смены статуса заказа
	 * ChangeStatus()
	 * @param int $system_of_pay_id идентификатор платежной системы
	 * @param array $param массив атрибутов
	 * - $param['shop_order_id'] идентификатор заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $system_of_pay_id = 4;
	 * $param['shop_order_id'] = '117';
	 * $shop->ExecSystemsOfPayChangeStatus ($system_of_pay_id, $param);
	 *
	 * ?>
	 * </code>
	 */
	function ExecSystemsOfPayChangeStatus($system_of_pay_id, $param = array())
	{
		$system_of_pay_id = intval($system_of_pay_id);

		$_SESSION['system_of_pay_id'] = $system_of_pay_id;

		// Формируем путь к файлу
		$handler_path = CMS_FOLDER . 'hostcmsfiles/shop/pay/handler' . $system_of_pay_id . '.php';
		if (is_file($handler_path))
		{
			if (!class_exists('system_of_pay_handler'))
			{
				require_once($handler_path);
			}

			if (class_exists('system_of_pay_handler'))
			{
				$handler = new system_of_pay_handler();

				if (method_exists($handler, 'ChangeStatus'))
				{
					$handler->ChangeStatus($param);
				}
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Получение информации о платежной системе
	 *
	 * @param int $shop_system_of_pay_id идентификационный номер платёжной системы
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_system_of_pay_id = 4;
	 *
	 * $row = $shop->GetSystemOfPay($shop_system_of_pay_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed возвращает результат выборки платёжной системы или ложь, если запись не выбрана
	 */
	function GetSystemOfPay($shop_system_of_pay_id)
	{
		$shop_system_of_pay_id = intval($shop_system_of_pay_id);
		$oShop_Payment_System = Core_Entity::factory('Shop_Payment_System')->find($shop_system_of_pay_id);

		return !is_null($oShop_Payment_System->id)
			? $this->getArrayShopPaymentSystem($oShop_Payment_System)
			: FALSE;
	}

	/**
	 * Удаление платежной системы
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_system_of_pay_id = 9;
	 *
	 * $result = $shop->DeleteSystemOfPay($shop_system_of_pay_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @param int $shop_system_of_pay_id идентификационный номер платёжной системы
	 * @return array возвращает результат удаления платёжной системы
	 */
	function DeleteSystemOfPay($shop_system_of_pay_id)
	{
		$shop_system_of_pay_id = intval($shop_system_of_pay_id);

		Core_Entity::factory('Shop_Payment_System', $shop_system_of_pay_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Вставка области
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор области
	 * - int $param['shop_country_id'] идентификатор страны
	 * - string $param['name'] название области
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - int $param['shop_location_order'] порядок сортировки области
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_country_id'] = 175;
	 * $param['name'] = 'Новая область';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $location_id = $shop->InsertLocation($param);
	 *
	 * // Распечатаем результат
	 * echo $location_id;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленной области
	 */
	function InsertLocation($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Country_Location = Core_Entity::factory('Shop_Country_Location', $param['id']);
		$oShop_Country_Location->name = Core_Type_Conversion::toStr($param['name']);
		$oShop_Country_Location->shop_country_id = Core_Type_Conversion::toInt($param['shop_country_id']);
		$oShop_Country_Location->sorting = Core_Type_Conversion::toInt($param['shop_location_order']);

		if(is_null($oShop_Country_Location->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Country_Location->user_id = intval($param['users_id']);
		}

		$oShop_Country_Location->save();

		return $oShop_Country_Location->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление области
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br />string $param['name'] название области
	 * <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления области
	 */
	function UpdateLocation($param)
	{
		return $this->InsertLocation($param);
	}

	/**
	 * Получение информации о области
	 *
	 * @param int $shop_location_id идентификационный номер области
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_location_id = 923;
	 *
	 * $row = $shop->GetLocation($shop_location_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки области
	 */
	function GetLocation($shop_location_id)
	{
		$shop_location_id = intval($shop_location_id);

		if (isset($this->CacheGetLocation[$shop_location_id]))
		{
			return $this->CacheGetLocation[$shop_location_id];
		}

		$oShop_Country_Location = Core_Entity::factory('Shop_Country_Location')->find($shop_location_id);

		if (!is_null($oShop_Country_Location->id))
		{
			return $this->CacheGetLocation[$shop_location_id] = $this->getArrayShopCountryLocation($oShop_Country_Location);
		}

		return FALSE;
	}

	/**
	 * Получение списка всех областей (штатов)
	 *
	 * @param int $county_id идентификатор страны, для которой выбираем области (при =0 - выбираем все области)
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $array = $shop->GetAllLocation($country_id = 0);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($array))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource информация об областях (штатах)
	 */
	function GetAllLocation($country_id = 0)
	{
		$country_id = intval($country_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'shop_location_id'),
			array('name', 'shop_location_name'),
			'shop_country_id',
			array('sorting', 'shop_location_order'),
			array('user_id', 'users_id')
			)
			->from('shop_country_locations')
			->where('deleted', '=', 0)
			->orderBy('shop_location_order')
			->orderBy('shop_location_name');

		// Проверяем ограничение на область
		if ($country_id)
		{
			$queryBuilder->where('shop_country_id', '=', $country_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление области
	 *
	 * @param int $shop_location_id идентификационный номер области
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_location_id = 1;
	 *
	 * $result = $shop->DeleteLocation($shop_location_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления области
	 */
	function DeleteLocation($shop_location_id)
	{
		$shop_location_id = intval($shop_location_id);
		Core_Entity::factory('Shop_Country_Location', $shop_location_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Вставка информации о стране
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор страны
	 * - string $param['name'] название страны
	 * - string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['name'] = 'Новая страна';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $country_id = $shop->InsertCountry($param);
	 *
	 * // Распечатаем результат
	 * echo $country_id;
	 * </code>
	 * @return int идентификатор вставленной страны
	 */
	function InsertCountry($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Country = Core_Entity::factory('Shop_Country', $param['id']);
		$oShop_Country->name = Core_Type_Conversion::toStr($param['name']);
		$oShop_Country->sorting = Core_Type_Conversion::toInt($param['shop_country_order']);

		if(is_null($oShop_Country->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Country->user_id = intval($param['users_id']);
		}

		$oShop_Country->save();

		return $oShop_Country->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление страны
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор страны
	 * - string $param['name'] название страны
	 * - string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления страны
	 */
	function UpdateCountry($param)
	{
		return $this->InsertCountry($param);
	}

	/**
	 * Получение информации о стране
	 *
	 * @param int $shop_country_id идентификационный номер страны
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_country_id = 1;
	 *
	 * $row = $shop->GetCountry($shop_country_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки страны
	 */
	function GetCountry($shop_country_id)
	{
		$shop_country_id = intval($shop_country_id);

		if (isset($this->CacheGetCountry[$shop_country_id]))
		{
			return $this->CacheGetCountry[$shop_country_id];
		}

		$oShop_Country = Core_Entity::factory('Shop_Country')->find($shop_country_id);

		if (!is_null($oShop_Country->id))
		{
			return $this->CacheGetCountry[$shop_country_id] = $this->getArrayShopCountry($oShop_Country);
		}

		return FALSE;
	}

	function GetCountryByName($shop_country_name)
	{
		$oShop_Country = Core_Entity::factory('Shop_Country');

		$oShop_Country->queryBuilder()
			->clear()
			->where('name', 'LIKE', "%{$shop_country_name}%")
			->limit(1);

		$aShop_Coutries = $oShop_Country->findAll();

		if (count($aShop_Coutries) > 0)
		{
			return $this->getArrayShopCountry($aShop_Coutries[0]);
		}

		return FALSE;
	}

	function GetLocationByName($shop_location_name)
	{
		$oShop_Country_Location = Core_Entity::factory('Shop_Country_Location');

		$oShop_Country_Location->queryBuilder()
			->where('name', 'LIKE', "%{$shop_location_name}%")
			->limit(1);

		$aShop_Country_Locations = $oShop_Country_Location->findAll();
		if (count($aShop_Country_Locations) > 0)
		{
			return $this->getArrayShopCountryLocation($aShop_Country_Locations[0]);
		}

		return FALSE;
	}

	function GetCityByName($shop_city_name)
	{
		$oShop_Country_Location_City = Core_Entity::factory('Shop_Country_Location_City');

		$oShop_Country_Location_City->queryBuilder()
			->where('name', 'LIKE', "%{$shop_city_name}%")
			->limit(1);

		$aShop_Country_Location_Cities = $oShop_Country_Location_City->findAll();

		if (count($aShop_Country_Location_Cities) > 0)
		{
			return $this->getArrayShopCountryLocationCity($aShop_Country_Location_Cities[0]);
		}

		return FALSE;
	}

	function GetCityAreaByName($shop_city_area_name)
	{
		$oShop_Country_Location_City_Area = Core_Entity::factory('Shop_Country_Location_City_Area');

		$oShop_Country_Location_City_Area->queryBuilder()
			->where('name', 'LIKE', "%{$shop_city_name}%")
			->limit(1);

		$aShop_Country_Location_City_Areas = $oShop_Country_Location_City_Area->findAll();

		if (count($aShop_Country_Location_City_Areas) > 0)
		{
			return $this->getArrayShopCountryLocationCityArea($aShop_Country_Location_City_Areas[0]);
		}

		return FALSE;
	}

	/**
	 * Получение информации о всех странах
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $shop->GetAllCountries();
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array
	 */
	function GetAllCountries()
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_country_id'),
				array('name', 'shop_country_name'),
				array('sorting', 'shop_country_order'),
				array('user_id', 'users_id')
			)
			->from('shop_countries')
			->where('deleted', '=', 0)
			->orderBy('sorting')
			->orderBy('name');

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление страны
	 *
	 * @param int $shop_country_id идентификационный номер страны
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_country_id = 1;
	 *
	 * $result = $shop->DeleteCountry($shop_country_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления страны
	 */
	function DeleteCountry($shop_country_id)
	{
		$shop_country_id = intval($shop_country_id);
		Core_Entity::factory('Shop_Country', $shop_country_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Вставка информации о городе
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['location_id'] идентификатор области
	 * - int $param['id'] идентификатор города
	 * - string $param['name'] название города
	 * - string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - int $param['city_order'] порядок сортировки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['name'] = 'Новый город';
	 * $param['location_id'] = 923;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $city_id = $shop->InsertCity($param);
	 *
	 * // Распечатаем результат
	 * echo $city_id;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного города
	 */
	function InsertCity($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		$oShop_Country_Location_City = Core_Entity::factory('Shop_Country_Location_City', $param['id']);

		$oShop_Country_Location_City->name = Core_Type_Conversion::toStr($param['name']);
		$oShop_Country_Location_City->shop_location_id = Core_Type_Conversion::toInt($param['location_id']);
		$oShop_Country_Location_City->sorting = Core_Type_Conversion::toInt($param['shop_city_order']);

		if(is_null($oShop_Country_Location_City->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Country_Location_City->user_id = intval($param['users_id']);
		}

		$oShop_Country_Location_City->save();

		return $oShop_Country_Location_City->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление города
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['id'] идентификатор города
	 * - string $param['name'] название города
	 * - string $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return array возвращает результат обновления города
	 */
	function UpdateCity($param)
	{
		return $this->InsertCity($param);
	}

	/**
	 * Получение информации о городе
	 *
	 * @param int $shop_city_id идентификационный номер города
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_city_id = 1;
	 *
	 * $row = $shop->GetCity($shop_city_id);
	 *
	 * //Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с данными или false, если город не найден
	 */
	function GetCity($shop_city_id)
	{
		$shop_city_id = intval($shop_city_id);

		if (isset($this->CacheGetCity[$shop_city_id]))
		{
			return $this->CacheGetCity[$shop_city_id];
		}

		$oShop_Country_Location_City = Core_Entity::factory('Shop_Country_Location_City')->find($shop_city_id);

		if (!is_null($oShop_Country_Location_City->id))
		{
			return $this->CacheGetCity[$shop_city_id] = $this->getArrayShopCountryLocationCity($oShop_Country_Location_City);
		}

		return FALSE;
	}

	/**
	 * Получение списка городов
	 *
	 * @param int $shop_location_id идентификатор местоположения, если 0, то выбираются все города
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_location_id = 1;
	 *
	 * $resource = $shop->GetAllCity($shop_location_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function GetAllCity($shop_location_id = 0)
	{
		$shop_location_id = intval($shop_location_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'shop_city_id'),
			array('shop_country_location_id', 'shop_location_id'),
			array('name', 'shop_city_name'),
			array('sorting', 'shop_city_order'),
			array('user_id', 'users_id')
		)
		->from('shop_country_location_cities')
		->where('deleted', '=', 0)
		->orderBy('shop_city_order')
		->orderBy('shop_city_name');

		if ($shop_location_id)
		{
			$queryBuilder->where('shop_country_location_id', '=', $shop_location_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление города
	 *
	 * @param int $shop_city_id идентификационный города
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_city_id = 10;
	 *
	 * $result = $shop->DeleteCity($shop_city_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return bool
	 */
	function DeleteCity($shop_city_id)
	{
		$shop_city_id = intval($shop_city_id);
		Core_Entity::factory('Shop_Country_Location_City', $shop_city_id)->markDeleted();
		return FALSE;
	}

	/**
	 * Получение информации о всех районах города
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_city_id = 0;
	 *
	 * $row = $shop->GetAllCityArea($shop_city_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return resource
	 */
	function GetAllCityArea($shop_city_id = 0)
	{
		$shop_city_id = intval($shop_city_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'shop_city_area_id'),
			array('name', 'shop_city_area_name'),
			array('shop_country_location_city_id', 'shop_city_id'),
			array('user_id', 'users_id'),
			array('sorting', 'shop_city_area_order')
		)
		->from('shop_country_location_city_areas')
		->where('deleted', '=', 0)
		->orderBy('shop_city_area_order')
		->orderBy('shop_city_area_name');

		if ($shop_city_id != 0)
		{
			$queryBuilder->where('shop_country_location_city_id', '=', $shop_city_id);

		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение информации о районе города (обо всех районах при $city_area_id=-1)
	 * @param int $city_area_id идентификатор района (при $city_area_id=-1 выбираем все районы)
	 * @param int $city_id идентификатор города
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $city_area_id = -1;
	 * $city_id = 1;
	 *
	 * $result = $shop->SelectCityArea($city_area_id, $city_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($result))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed информация о выбранных районах города (результат запроса) или ложь в случае возникновения ошибки
	 */
	function SelectCityArea($city_area_id, $city_id = 0)
	{
		$city_area_id = intval($city_area_id);
		$city_id = intval($city_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_city_area_id'),
				array('name', 'shop_city_area_name'),
				array('shop_country_location_city_id', 'shop_city_id'),
				array('sorting', 'shop_city_area_order'),
				array('user_id', 'users_id')
			)
			->from('shop_country_location_city_areas')
			->where('shop_country_location_city_id', '=', $city_id)
			->where('deleted', '=', 0);

		if ($city_area_id != -1)
		{
			$queryBuilder->where('id', '=', $city_area_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение информации о районе города по идентификатору района
	 *
	 * @param int $city_area_id идентификатор района города
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $city_area_id = 3;
	 *
	 * $row = $shop->GetCityArea($city_area_id);
	 *
	 * //Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с информацией о районе города в случае успешного выполнения, false - в противном случае
	 */
	function GetCityArea($city_area_id)
	{
		$city_area_id = intval($city_area_id);

		if ($city_area_id)
		{
			if (isset($this->CacheGetCityArea[$city_area_id]))
			{
				return $this->CacheGetCityArea[$city_area_id];
			}

			$oShop_Country_Location_City_Area = Core_Entity::factory('Shop_Country_Location_City_Area')->find($city_area_id);

			if (!is_null($oShop_Country_Location_City_Area->id))
			{
				return $this->CacheGetCityArea[$city_area_id] = $this->getArrayShopCountryLocationCityArea($oShop_Country_Location_City_Area);
			}
		}

		return FALSE;
	}

	/**
	 * Добавление/редактирование информации о районе города
	 *
	 * @param int $type тип действия 0 - вставка, 1 - обновление
	 * @param int $city_area_id идентификатор района
	 * @param int $city_area_name название района
	 * @param int $city_id идентификатор города, в котором находится район
	 * @param int $city_area_order порядок сортировки элемента
	 * @param int $users_id идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $type = 0;
	 * $city_area_name = 'Новый район';
	 * $city_id = 10963;
	 * $shop_city_area_order = 10;
	 * $city_area_id = '';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $newid = $shop->InsertCityArea($type, $city_area_id, $city_area_name, $city_id, $shop_city_area_order);
	 *
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор нового или редатируемого района(в зависимости от типа действия)
	 */
	function InsertCityArea($type, $city_area_id, $city_area_name, $city_id, $shop_city_area_order, $users_id = FALSE)
	{
		$city_area_id = intval($city_area_id);
		$city_id = intval($city_id);

		$oShop_Country_Location_City_Area = Core_Entity::factory('Shop_Country_Location_City_Area', $city_area_id);

		$oShop_Country_Location_City_Area->name = $city_area_name;
		$oShop_Country_Location_City_Area->shop_city_id = $city_id;
		$oShop_Country_Location_City_Area->sorting = $shop_city_area_order;

		if(is_null($oShop_Country_Location_City_Area->id) && $users_id)
		{
			$oShop_Country_Location_City_Area->user_id = intval($users_id);
		}

		$oShop_Country_Location_City_Area->save();

		return $oShop_Country_Location_City_Area->id;
	}

	/**
	 * Удаление района города
	 *
	 * @param int $shop_city_area_id идентификатор района
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_city_area_id = 3;
	 *
	 * $result = $shop->DeleteCityArea($shop_city_area_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return boolean истина при удачном удалении, ложь - в обратном случае
	 */
	function DeleteCityArea($shop_city_area_id)
	{
		$shop_city_area_id = intval($shop_city_area_id);
		Core_Entity::factory('Shop_Country_Location_City_Area', $shop_city_area_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Извлечение корзины в зависимости от CartType.
	 * Для универсального извлечения данных корзины используйте GetItemsFromCart()
	 *
	 * @return array содержимое корзины
	 * @see GetItemsFromCart()
	 */
	function GetCart()
	{
		// Тип хранения корзины
		return $this->CartType == 0
			// Читаем массив из кукисов
			? $this->GetCookieCart()
			// Читаем массив из сессией
			: $this->GetSessionCart();
	}

	/**
	 * Сохранение корзины в зависимости от CartType
	 *
	 * @param array $CART содержимое корзины
	 */
	function SetCart($CART)
	{
		// Тип хранения корзины
		$this->CartType == 0
			? $this->SetCookieCart($CART, $this->GetCookieExpires(), '/')
			: $this->SetSessionCart($CART);
	}

	/**
	 * Удаление товара из корзины
	 *
	 * @param array $param массив с данными
	 * - $param['shop_id'] - идентификатор магазина
	 * - $param['site_user_id'] - идентификатор пользователя сайта
	 * - $param['item_id'] - идентификатор элемента
	 * <code>
	 * <?php
	 * Shop = new Shop();
	 *
	 * $param['shop_id'] = 1;
	 * $param['item_id'] = 24;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 *	$SiteUsers = & singleton('SiteUsers');
	 *	$param['site_user_id'] = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$param['site_user_id'] = 0;
	 * }
	 *
	 * $shop->DeleteCart($param);
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function DeleteCart($param)
	{
		$shop_id = intval($param['shop_id']);

		if (isset($param['site_user_id']))
		{
			$site_user_id = intval($param['site_user_id']);
		}
		else
		{
			$site_user_id = Core_Type_Conversion::toInt($param['user_id']);
		}

		$item_id = intval($param['item_id']);

		// При наличии пользователя удаляем данные из таблицы заказов
		if ($site_user_id)
		{
			$oShop_Cart = Core_Entity::factory('Shop', $shop_id )->Shop_Carts->getByShopItemIdAndSiteuserId($item_id, $site_user_id);

			if (!is_null($oShop_Cart))
			{
				$oShop_Cart->delete();
				return TRUE;
			}

			return FALSE;
		}
		else
		{
			$CART = $this->GetCart();
			if (isset($CART[$shop_id][$item_id]))
			{
				unset($CART[$shop_id][$item_id]);

				// Тип хранения корзины
				$this->SetCart($CART);
			}

			return TRUE;
		}
	}

	/**
	 * Метод для очистки корзины кукисов и сессий
	 *
	 * @param int $shop_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $shop->ClearCookieAndSessionCart($shop_id);
	 * ?>
	 * </code>
	 */
	function ClearCookieAndSessionCart($shop_id)
	{
		$shop_id = intval($shop_id);
		$CART = $this->GetCart();

		if (isset($CART[$shop_id]))
		{
			// Удаляем информацию о магазине
			unset($CART[$shop_id]);
			$this->SetCart($CART);
		}

		/* Очищаем корзину в сессии, которая используется при оформлении заказа */
		if (isset($_SESSION['CART'][$shop_id]))
		{
			unset($_SESSION['CART'][$shop_id]);
		}
	}

	/**
	 * Вставка информации о магазине
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_id'] идентификатор магазина (указывается при обновлении)
	 * - int $param['shop_dir_id'] идентификатор раздела
	 * - string $param['name'] наименование магазина
	 * - int $param['site_id'] идентификатор сайта
	 * - string $param['description'] описание магазина
	 * - int $param['shop_items_on_page'] число товаров отображаемых на странице
	 * - int $param['shop_country_id'] идентификатор страны по умолчанию
	 * - int $param['shop_currency_id'] идентификатор валюты по умолчанию
	 * - int $param['shop_order_status_id'] идентификатор состояния заказа по умолчанию
	 * - int $param['shop_mesures_id'] идентификатор единицы измерения
	 * - int $param['structure_id'] идентификатор узла структуры
	 * - int $param['shop_access'] параметр, определяющий группу пользователей, имеющих доступ к магазину (0 - доступна всем)
	 * - int $param['shop_shops_send_order_mail_admin'] флаг необходимости отправки письма о заказе администратору
	 * - int $param['shop_shops_send_order_mail_user'] флаг необходимости отправки письма о заказе пользователю (заказчику)
	 * - string $param['shop_shops_admin_mail'] адрес(а) куратора(ов) магазина
	 * - int $param['shop_sort_order_field'] поле сортировки товара
	 * - int $param['shop_sort_order_type'] тип сортировки товара
	 * - int $param['shop_group_sort_order_field'] поле сортировки групп товара
	 * - int $param['shop_group_sort_order_type'] тип сортировки групп товара
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * - int $param['shop_comment_active'] активность комментария к товару
	 * - int $param['shop_company_id'] идентификатор компании
	 * - string $param['watermark_file'] - файл марки для наложения на изображения
	 * - string $param['watermark_file_expantion'] - расширение файла марки для наложения
	 * - int $param['watermark_default_used'] - параметр, определяющий используется ли файл марки по умолчанию (1 - используется, 0 - не используется).
	 * - int $param['watermark_default_used_small'] - параметр, определяющий используется ли файл марки по умолчанию для малых изображений(1 - используется, 0 - не используется).
	 * - string $param['watermark_default_position_x'] - позиция изображения по оси X по умолчанию. по умолчанию равна 50%
	 * - string $param['watermark_default_position_y'] - позиция изображения по оси Y по умолчанию. по умолчанию равна 100%
	 * - int $param['shop_image_small_max_width'] максимальная ширина маленькой картинки
	 * - int $param['shop_image_big_max_width'] максимальная ширина большой картинки
	 * - int $param['shop_image_small_max_heigh'] максимальная высота маленькой картинки
	 * - int $param['shop_image_big_max_height'] максимальная высота большой картинки
	 * - int $param['shop_shops_default_save_proportions'] флаг, указывающий на необходимость сохранения пропорций изображений в магазине по умолчанию
	 * - int $param['shop_image_small_max_width_group'] максимальная ширина маленькой картинки для группы товаров
	 * - int $param['shop_image_big_max_width_group'] максимальная ширина большой картинки для группы товаров
	 * - int $param['shop_image_small_max_height_group'] максимальная высота маленькой картинки для группы товаров
	 * - int $param['shop_image_big_max_height_group'] максимальная высота большой картинки для группы товаров
	 * - int $param['shop_shops_yandex_market_name_group'] название магазина
	 * - string $param['shop_yandex_market_sales_notes_default'] значение по умолчанию тега <sales_notes>
	 * - int $param['shop_shops_url_type'] тип формирования URL
	 * - int $param['shop_typograph_item_by_default'] типографировать товары по умолчанию
	 * - int $param['shop_typograph_group_by_default'] типографировать групы товаров по умолчанию
	 * - int $param['shop_shops_apply_tags_automatic'] параметр, определяющий будут ли при добавлении товаров в случае отсутствия тегов автоматически формироваться теги для товаров из их названия, описания и текста
	 * - bool $param['shop_shops_attach_eitem'] флаг, указывающий вкладывать в сообщение файл электронного товара, или ссылку на файл. (1 - файл, 0 - ссылка. по умолчанию - 1)
	 * - int $param['shop_shops_writeoff_payed_items'] параметр, определяющий будет ли при оплате товаров уменьшаться их количество
	 *  <br /> 1 - теги формируются автоматически (по умолчанию), 0 - не формируюся автоматически
	 * - int $param['shop_shops_file_name_conversion'] параметр, определяющий будут ли преобразовываться названия загружаемых файлов в служебные. Данный параметр влияет на все объекты интернет-магазина - элементы, группы,
	 * 	<br />долнительные свойства элементов, дополнительные свойства групп.
	 * 	- 0 - названия файлов не преобразуются, 1 - преобразуются (по умолчанию)
	 * @return int or boolean возвращает идентификационный номер вставленного магазина в случае успешного выполнения запроса или false в противном случае
	 */
	function InsertShop($param)
	{
		if (!isset($param['shop_id']) || !$param['shop_id'])
		{
			$param['shop_id'] = NULL;
		}

		$oShop = Core_Entity::factory('Shop', $param['shop_id']);

		$oShop->shop_dir_id = Core_Type_Conversion::toInt($param['shop_dir_id']);
		$oShop->shop_company_id = Core_Type_Conversion::toInt($param['shop_company_id']);
		$oShop->site_id = Core_Type_Conversion::toInt($param['site_id']);
		$oShop->name = Core_Type_Conversion::toStr($param['name']);
		$oShop->description = Core_Type_Conversion::toStr($param['description']);
		$oShop->yandex_market_name = Core_Type_Conversion::toStr($param['shop_shops_yandex_market_name']);
		$oShop->image_small_max_width = Core_Type_Conversion::toInt($param['shop_image_small_max_width']);
		$oShop->image_large_max_width = Core_Type_Conversion::toInt($param['shop_image_big_max_width']);
		$oShop->image_small_max_height = Core_Type_Conversion::toInt($param['shop_image_small_max_height']);
		$oShop->image_large_max_height = Core_Type_Conversion::toInt($param['shop_image_big_max_height']);
		$oShop->structure_id = Core_Type_Conversion::toInt($param['structure_id']);
		$oShop->shop_country_id = Core_Type_Conversion::toInt($param['shop_country_id']);
		$oShop->shop_currency_id = Core_Type_Conversion::toInt($param['shop_currency_id']);
		$oShop->shop_order_status_id = Core_Type_Conversion::toInt($param['shop_order_status_id']);
		$oShop->shop_measure_id = Core_Type_Conversion::toInt($param['shop_mesures_id']);
		$oShop->send_order_email_admin = Core_Type_Conversion::toInt($param['shop_shops_send_order_mail_admin']);
		$oShop->send_order_email_user = Core_Type_Conversion::toInt($param['shop_shops_send_order_mail_user']);
		$oShop->email = Core_Type_Conversion::toStr($param['shop_shops_admin_mail']);
		$oShop->items_sorting_field = Core_Type_Conversion::toInt($param['shop_sort_order_field']);
		$oShop->items_sorting_direction = Core_Type_Conversion::toInt($param['shop_sort_order_type']);
		$oShop->groups_sorting_field = Core_Type_Conversion::toInt($param['shop_group_sort_order_field']);
		$oShop->groups_sorting_direction = Core_Type_Conversion::toInt($param['shop_group_sort_order_type']);
		$oShop->comment_active = Core_Type_Conversion::toInt($param['shop_comment_active']);
		$oShop->watermark_file = Core_Type_Conversion::toStr($param['shop_watermark_file']);
		$oShop->watermark_default_use_large_image = Core_Type_Conversion::toInt($param['shop_watermark_default_use_big']);
		$oShop->watermark_default_use_small_image = Core_Type_Conversion::toInt($param['shop_watermark_default_use_small']);
		$oShop->watermark_default_position_x = Core_Type_Conversion::toStr($param['shop_watermark_default_position_x']);
		$oShop->watermark_default_position_y = Core_Type_Conversion::toStr($param['shop_watermark_default_position_y']);
		$oShop->items_on_page = Core_Type_Conversion::toInt($param['shop_items_on_page']);
		$oShop->guid = Core_Type_Conversion::toStr($param['shop_shops_guid']);
		$oShop->url_type = Core_Type_Conversion::toInt($param['shop_shops_url_type']);
		$oShop->format_date = Core_Type_Conversion::toStr($param['shop_format_date']);
		$oShop->format_datetime = Core_Type_Conversion::toStr($param['shop_format_datetime']);
		$oShop->typograph_default_items = Core_Type_Conversion::toInt($param['shop_typograph_item_by_default']);
		$oShop->typograph_default_groups = Core_Type_Conversion::toInt($param['shop_typograph_group_by_default']);
		$oShop->apply_tags_automatically = Core_Type_Conversion::toInt($param['shop_shops_apply_tags_automatic']);
		$oShop->write_off_paid_items = Core_Type_Conversion::toInt($param['shop_shops_writeoff_payed_items']);
		$oShop->apply_keywords_automatically = Core_Type_Conversion::toInt($param['shop_shops_apply_keywords_automatic']);
		$oShop->change_filename = Core_Type_Conversion::toInt($param['shop_shops_file_name_conversion']);
		$oShop->attach_digital_items = Core_Type_Conversion::toInt($param['shop_shops_attach_eitem']);
		$oShop->yandex_market_sales_notes_default = Core_Type_Conversion::toStr($param['shop_yandex_market_sales_notes_default']);
		$oShop->siteuser_group_id = Core_Type_Conversion::toInt($param['shop_access']);
		$oShop->group_image_small_max_width = Core_Type_Conversion::toInt($param['shop_image_small_max_width_group']);
		$oShop->group_image_large_max_width = Core_Type_Conversion::toInt($param['shop_image_big_max_width_group']);
		$oShop->group_image_small_max_height = Core_Type_Conversion::toInt($param['shop_image_small_max_height_group']);
		$oShop->group_image_large_max_height = Core_Type_Conversion::toInt($param['shop_image_big_max_height_group']);
		$oShop->preserve_aspect_ratio = Core_Type_Conversion::toInt($param['shop_shops_default_save_proportions']);

		// Проверяем наличие магазина, связанного с таким же узлом структуры
		$row = !is_null($oShop->id)
			? $this->GetShopWhithStructureId($structure_id, $site_id, $param['shop_id'])
			: $this->GetShopWhithStructureId($structure_id, $site_id);

		if (is_null($oShop->id) && Core_Type_Conversion::toInt($param['users_id']))
		{
			$oShop->user_id = $param['users_id'];
		}

		$oShop->user_id = Core_Type_Conversion::toInt($param['shop_group_sort_order_type']);
		$oShop->save();

		$f_path = CMS_FOLDER . UPLOADDIR . 'shop_' . $oShop->id;

		// Проверяем, существует ли папка у созданного магазина
		if (!is_dir($f_path))
		{
			@ mkdir($f_path, CHMOD);
			@ chmod($f_path, CHMOD);
		}

		// Проверям, если папка eitems не создана, то создаем ее
		$dir_name = CMS_FOLDER . UPLOADDIR . 'shop_' . $oShop->id . '/eitems';

		if (!is_dir($dir_name))
		{
			// Создаем директорию и кладем в нее файл .htaccess
			if (@ mkdir($dir_name, CHMOD))
			{
				@chmod($dir_name, CHMOD);

				// Создаем файл
				$file_access = @fopen($dir_name . '/.htaccess', "w");
				if ($file_access)
				{
					fwrite($file_access, "deny from all");
					fclose($file_access);
				}
			}
		}

		if (isset($param['shop_id']))
		{
			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP';
				$cache->DeleteCacheItem($cache_name, $oShop->id);
			}
		}

		return $oShop->id;
	}

	/**
	 * Устаревший метод, осуществляющий обновление информации о магазине
	 */
	function UpdateShop($param)
	{
		return $this->InsertShop($param);
	}

	/**
	 * Получение информации о магазине, связанном с узлом структуры
	 *
	 * @param int $structure_id идентификатор узла структуры
	 * @return int идентификатор магазина или false
	 */
	function GetShopByStructureId($structure_id)
	{
		$row = $this->GetShopWhithStructureId($structure_id);
		if ($row)
		{
			return $row['shop_shops_id'];
		}

		return FALSE;
	}

	/**
	 * Метод выбора информации о магазине данного узла структуры сайта
	 *
	 * @param int $structure_id идентификатор узла структуры
	 * @param int $site_id идентификатор сайта
	 * @param int $shop_shops_id идентификатор магазина, который не включать в условие (по умолчанию 0 - не ограничиваем по id магазина)
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $structure_id = 42;
	 * $site_id = 1;
	 *
	 * $row = $shop->GetShopWhithStructureId($structure_id, $site_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed информация о магазине или FALSE, если нет магазина с таким узлом структуры для сайта
	 */
	function GetShopWhithStructureId($structure_id, $site_id = 0, $shop_shops_id = 0)
	{
		$structure_id = intval($structure_id);
		$site_id = intval($site_id);
		$shop_shops_id = intval($shop_shops_id);

		$oShop = Core_Entity::factory('Shop')
			->getByStructureId($structure_id);

		if (!is_null($oShop))
		{
			if ($site_id != 0 && $oShop->site_id != $site_id)
			{
				return FALSE;
			}

			if ($shop_shops_id != 0 && $oShop->id != $shop_shops_id)
			{
				return FALSE;
			}

			return $this->getArrayShop($oShop);
		}

		return FALSE;
	}

	/**
	 * Получение информации о магазине
	 *
	 * @param int $shop_shops_id идентификационный номер магазине
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetShop($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает результат выборки магазина
	 */
	function GetShop($shop_shops_id, $param = array())
	{
		$shop_shops_id = intval($shop_shops_id);
		$param = Core_Type_Conversion::toArray($param);

		// Если есть данные в кэше магазинов (в памяти)
		if (isset($this->g_array_shop[$shop_shops_id]))
		{
			return $this->g_array_shop[$shop_shops_id];
		}

		$cache_name = 'SHOP';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($shop_shops_id, $cache_name))
			{
				$this->g_array_shop[$shop_shops_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		$oShop = Core_Entity::factory('Shop')->find($shop_shops_id);
		$this->g_array_shop[$shop_shops_id] = !is_null($oShop->id) ? $this->getArrayShop($oShop) : FALSE;

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache->Insert($shop_shops_id, $this->g_array_shop[$shop_shops_id], $cache_name);
		}

		return $this->g_array_shop[$shop_shops_id];
	}

	/**
	 * Удаление магазина
	 *
	 * @param int $shop_shops_id идентификационный номер магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $result = $shop->DeleteShop($shop_shops_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array возвращает результат удаления магазина
	 */
	function DeleteShop($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP';
			$cache->DeleteCacheItem($cache_name, $shop_shops_id);
		}

		Core_Entity::factory('Shop', $shop_shops_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Вставка значения дополнительного свойства товара
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_items_catalog_item_id'] идентификатор товара, для которого вставляются свойства
	 * - int $param['shop_list_of_properties_id'] идентификатор свойства товара
	 * - string $param['shop_properties_items_value'] значение свойства товара (или оригинальное имя загружаемого изображения)
	 * - string $param['shop_properties_items_value_small'] оригинальное имя загружаемого малого файла изображения
	 * - string $param['shop_properties_items_file'] системное имя файла изображения
	 * - string $param['shop_properties_items_file_small'] системное имя малого файла изображения
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['shop_list_of_properties_id'] = 141;
	 * $param['shop_properties_items_value'] = 'тест';
	 *
	 * $newid = $shop->InsertPropertiesItem($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленной записи
	 */
	function InsertPropertiesItem($param)
	{
		$param = Core_Type_Conversion::toArray($param);
		$shop_items_catalog_item_id = Core_Type_Conversion::toInt($param['shop_items_catalog_item_id']);
		$shop_list_of_properties_id = Core_Type_Conversion::toInt($param['shop_list_of_properties_id']);
		$shop_properties_items_value = Core_Type_Conversion::toStr($param['shop_properties_items_value']);
		$shop_properties_items_value_small = Core_Type_Conversion::toStr($param['shop_properties_items_value_small']);
		$shop_properties_items_file = Core_Type_Conversion::toStr($param['shop_properties_items_file']);
		$shop_properties_items_file_small = Core_Type_Conversion::toStr($param['shop_properties_items_file_small']);

		$oProperty = Core_Entity::factory('Property')->find($shop_list_of_properties_id);

		if ($oProperty->id)
		{
			$aValues = $oProperty->getValues($shop_items_catalog_item_id);

			// Value already exist
			$oValue = count($aValues) > 0
				? $aValues[0]
				: $oProperty->createNewValue($shop_items_catalog_item_id);

			if ($oProperty->type != 2)
			{
				$oValue->setValue($shop_properties_items_value);
			}
			else
			{
				$oValue->file = $shop_properties_items_file;
				$oValue->file_name = $shop_properties_items_value;
				$oValue->file_small = $shop_properties_items_file_small;
				$oValue->file_small_name = $shop_properties_items_value_small;
			}

			$oValue->save();

			return $oValue->id;
		}

		return FALSE;
	}

	/**
	 * Обновление информации о дополнительных свойствах товара
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_properties_items_id'] идентификатор обновляемой записи
	 * - int $param['shop_items_catalog_item_id'] идентификатор товара, для которого вставляются свойства
	 * - int $param['shop_list_of_properties_id'] идентификатор свойства товара
	 * - int $param['shop_properties_items_value'] значение свойства товара  (или оригинальное имя загружаемого изображения)
	 * - string $param['shop_properties_items_value_small'] оригинальное имя загружаемого малого файла изображения
	 * - string $param['shop_properties_items_file'] системное имя файла изображения
	 * - string $param['shop_properties_items_file_small'] системное имя малого файла изображения
	 *  <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_properties_items_id'] = 189;
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['shop_list_of_properties_id'] = 141;
	 * $param['shop_properties_items_value'] = 'test';
	 *
	 * $newid = $shop->UpdatePropertiesItem($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор обновленной записи
	 */
	function UpdatePropertiesItem($param)
	{
		throw new Core_Exception('Method UpdatePropertiesItem() does not allow');
	}

	/**
	 * Внутренний метод заполняет mem-кэш для переданного списка идентификаторов товаров дополнительными свойствами товаров.
	 * Заполнению подвергается массив $this->CacheDiscountsForItem[shop_items_catalog_item_id][]
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 */
	function FillMemCachePropertiesItem($mas_items_in)
	{
		$mas_items_in = Core_Type_Conversion::toArray($mas_items_in);
		if (count($mas_items_in) > 0)
		{
			// Меняем местами значения и ключи массива
			$aTmp = array_flip($mas_items_in);

			// Вычислить пересечение массивов, сравнивая ключи
			$aTmpIntersect = array_intersect_key($aTmp, $this->CachePropertiesItem);

			if (count($aTmpIntersect) != count($mas_items_in))
			{
				// Заполянем сопутствующие товары пустыми массивами
				foreach ($mas_items_in as $shop_items_catalog_item_id)
				{
					$this->CachePropertiesItem[$shop_items_catalog_item_id] = FALSE;
				}

				foreach ($mas_items_in as $shop_item_id)
				{
					$aPropertyValues = Core_Entity::factory('Shop_Item', $shop_item_id)->getPropertyValues();

					foreach($aPropertyValues as $oPropertyValue)
					{
						$this->CachePropertiesItem[$shop_item_id][] = $this->CorrectPropertiesItem($this->getArrayItemPropertyValue($oPropertyValue) + $this->getArrayItemProperty($oPropertyValue->Property));
					}
				}
			}
		}
	}

	/**
	 * Корректировка строки данных о дополнительном свойстве в соответствии с его типом.
	 *
	 * @param array $row данные о дополнительном свойстве
	 * @return array откорректированные данные
	 */
	function CorrectPropertiesItem($row, $param = array())
	{
		// В shop_properties_items_value_original сохраним оригинально значение
		$row['shop_properties_items_value_original'] = $row['shop_properties_items_value'];

		// Проверяем является ли свойство товара списком
		if ($row['shop_list_of_properties_type'] == 2)
		{
			// Проверяем наличие модуля списки
			if (class_exists('lists'))
			{
				// Проверяем необходимость получить значение элемента списка
				if (!isset($param['get_list_item_value']) || $param['get_list_item_value'] != FALSE)
				{
					$lists = & singleton('lists');

					// Получаем значение для данного списка
					$row_list = $lists->GetListItem($row['shop_properties_items_value'], $param);
					if ($row_list)
					{
						// В значение дополнительного свойства - пишем значение элемента списка
						$row['shop_properties_items_value'] = $row_list['lists_items_value'];
					}
				}
			}
		}

		return $row;
	}

	/**
	 * Получение информации о дополнительных свойствах группы товаров
	 *
	 * @param int $shop_groups_id идентификатор группы
	 * @param array $param массив дополнительных свойств
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_groups_id = 158;
	 * $param = array();
	 * $resource = $shop->GetAllPropertiesItem($shop_groups_id, $param);
	 *
	 * // Распечатаем результат
	 * print_r ($resource);
	 *
	 * ?>
	 * </code>
	 * @return mixed массив дополнительных свойств для товара, ложь - если не задано ни одного свойства для товара
	 */
	function GetAllPropertiesGroup($shop_groups_id, $param = array())
	{
		$shop_groups_id = intval($shop_groups_id);
		$param = Core_Type_Conversion::toArray($param);

		$aPropertyValues = Core_Entity::factory('Shop_Group', $shop_groups_id)->getPropertyValues();

		$aReturn = array();
		foreach($aPropertyValues as $oPropertyValue)
		{
			$aReturn = $this->CorrectPropertiesGroup($this->getArrayGroupPropertyValue($oPropertyValue) + $this->getArrayGroupProperty($oPropertyValue->Property), $param);
		}

		return $aReturn;
	}

	/**
	 * Корректировка строки данных о дополнительном свойстве группы в соответствии с его типом.
	 *
	 * @param array $row данные о дополнительном свойстве
	 * @return array откорректированные данные
	 */
	function CorrectPropertiesGroup($row, $param = array())
	{
		$row['shop_properties_group_value_value_original'] = $row['shop_properties_group_value_value'];

		// Проверяем является ли свойство товара списком
		if ($row['shop_properties_group_type'] == 2)
		{
			// Проверяем наличие модуля списки
			if (class_exists('lists'))
			{
				// Проверяем необходимость получить значение элемента списка
				if (!isset($param['get_list_item_value']) || $param['get_list_item_value'] != FALSE)
				{
					$lists = & singleton('lists');

					// Получаем значение для данного списка
					$row_list = $lists->GetListItem($row['shop_properties_group_value_value'], $param);
					if ($row_list)
					{
						// В значение дополнительного свойства - пишем значение элемента списка
						$row['shop_properties_group_value_value'] = $row_list['lists_items_value'];
					}
				}
			}
		}

		return $row;
	}

	/**
	 * Получение информации о дополнительных свойствах заданного товара
	 *
	 * @param int $item_id идентификатор товара
	 * @param array $param массив дополнительных свойств
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $item_id = 158;
	 * $param = array();
	 * $resource = $shop->GetAllPropertiesItem($item_id, $param);
	 *
	 * // Распечатаем результат
	 * print_r ($resource);
	 *
	 * ?>
	 * </code>
	 * @return mixed массив дополнительных свойств для товара, ложь - если не задано ни одного свойства для товара
	 */
	function GetAllPropertiesItem($item_id, $param = array())
	{
		$item_id = intval($item_id);
		$param = Core_Type_Conversion::toArray($param);

		if (isset($this->CachePropertiesItem[$item_id]) && !isset($param['cache_off']))
		{
			return $this->CachePropertiesItem[$item_id];
		}

		$cache_name = 'SHOP_ALL_PROPERTIES_ITEM';

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_field = $item_id . '_' . implode('_', $param);
			if (($in_cache = $cache->GetCacheContent($cache_field, $cache_name)) && $in_cache)
			{
				$this->CachePropertiesItem[$item_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		// Выбираем все дополнительные свойства для товара
		$aPropertyValues = Core_Entity::factory('Shop_Item', $item_id)->getPropertyValues();

		$properties_item_array = array();
		if (count($aPropertyValues) > 0)
		{
			foreach($aPropertyValues as $oPropertyValue)
			{
				// Корректирует дополнительное св-во (если расширенные типы, например список)
				$properties_item_array[] = $this->CorrectPropertiesItem($this->getArrayItemPropertyValue($oPropertyValue) + $this->getArrayItemProperty($oPropertyValue->Property), $param);
			}
		}
		else
		{
			$properties_item_array = FALSE;
		}

		if (!isset($param['cache_off']))
		{
			// Сохраняем в кэше свойств
			$this->CachePropertiesItem[$item_id] = $properties_item_array;
		}

		// Запись в файловый кэш
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($cache_field, $properties_item_array, $cache_name);
		}

		return $properties_item_array;
	}

	/**
	 * Получение всех дополнительных свойств для магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $param массив дополнительных параметров
	 * - $param['order'] поле сортировки
	 * - $param['where'] дополнительное условие для where
	 * @return resource информация о дополнительных свойств для магазина (результат выполнения запроса)
	 */
	function GetAllListProperties($shop_shops_id, $param = array())
	{
		$shop_shops_id = intval($shop_shops_id);
		$param = Core_Type_Conversion::toArray($param);

		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select()
			->from('properties')
			->where('shop_id', '=', $shop_shops_id)
			->where('deleted', '=', 0);

		// Проверяем налыичие переданного порядка сортировки
		if (isset($param['order']))
		{
			$queryBuilder->orderBy($param['order']);
		}
		else
		{
			$queryBuilder
				->orderBy('sorting')
				->orderBy('name');
		}

		if (isset($param['where']))
		{
			$this->parseQueryBuilder($param['where'], $queryBuilder);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Удаление значение дополнительного свойства товара
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param int $shop_items_catalog_item_id идентификатор элемента
	 * @param int $shop_list_of_properties_id идентификатор дополнительного свойства
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $shop_items_catalog_item_id = 158;
	 * $shop_list_of_properties_id = 133;
	 *
	 * $result = $shop->DeletePropertiesItem($shop_id, $shop_items_catalog_item_id, $shop_list_of_properties_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return boolean истина при удачном удалении, ложь в обратном случае
	 */
	function DeletePropertiesItem($shop_id, $shop_items_catalog_item_id, $shop_list_of_properties_id)
	{
		$shop_id = intval($shop_id);
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
		$shop_list_of_properties_id = intval($shop_list_of_properties_id);

		$oProperty = Core_Entity::factory('Property', $shop_list_of_properties_id);

		$aValues = $oProperty->getValues($shop_items_catalog_item_id);
		if (isset($aValues[0]))
		{
			// Value already exist
			$aValues[0]->delete();
			return TRUE;
		}
	}

	/**
	 * Вставка связи товара и цены из справочника цен
	 *
	 * @param array $param
	 * $param['shop_items_catalog_item_id'] - идентификатор элемента каталога<br />
	 * $param['shop_list_of_prices_id'] - идентификатор дополнительной цены<br />
	 * $param['shop_prices_to_item_value'] - значение дополнительной цены
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['shop_list_of_prices_id'] = 1;
	 * $param['shop_prices_to_item_value'] = 300;
	 *
	 * $newid = $shop->InsertPricesToItem($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 *
	 * ?>
	 * </code>
	 * @return mixed идентификатор обновленной записи или false в случае ошибки
	 */
	function InsertPricesToItem($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		$shop_items_catalog_item_id = Core_Type_Conversion::toInt($param['shop_items_catalog_item_id']);
		$shop_list_of_prices_id = Core_Type_Conversion::toInt($param['shop_list_of_prices_id']);
		$shop_prices_to_item_value = Core_Type_Conversion::toFloat($param['shop_prices_to_item_value']);

		$oShop_Item_Price = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Item_Prices->getByPriceId($shop_list_of_prices_id);

		if (is_null($oShop_Item_Price))
		{
			$oShop_Item_Price = Core_Entity::factory('Shop_Item_Price');
			$oShop_Item_Price->shop_item_id = $shop_items_catalog_item_id;
			$oShop_Item_Price->shop_price_id = $shop_list_of_prices_id;
		}

		$oShop_Item_Price->value = $shop_prices_to_item_value;
		$oShop_Item_Price->save();

		return $oShop_Item_Price->id;
	}

	/**
	 * Обновление дополнительной цены для элемента
	 *
	 * @param array $param массив параметров
	 * - $param['shop_prices_to_item_id'] - идентификатор из таблицы связи элементов каталога и значений дополнительных цен <b>(не обязательное поле)</b>
	 * - $param['shop_items_catalog_item_id'] - идентификатор элемента каталога
	 * - $param['shop_list_of_prices_id'] - идентификатор дополнительной цены
	 * - $param['shop_prices_to_item_value'] - значение дополнительной цены
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['shop_list_of_prices_id'] = 1;
	 * $param['shop_prices_to_item_value'] = 355;
	 *
	 * $newid = $shop->UpdatePricesToItem($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор обновленной записи или false в случае ошибки
	 */
	function UpdatePricesToItem($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		$shop_prices_to_item_id = Core_Type_Conversion::toInt($param['shop_prices_to_item_id']);
		$shop_items_catalog_item_id = Core_Type_Conversion::toInt($param['shop_items_catalog_item_id']);
		$shop_list_of_prices_id = Core_Type_Conversion::toInt($param['shop_list_of_prices_id']);
		$shop_prices_to_item_value = Core_Type_Conversion::toFloat($param['shop_prices_to_item_value']);

		// Если передан идентификатор из таблицы связи
		$oShop_Item_Price = $shop_prices_to_item_id
			? Core_Entity::factory('Shop_Item_Price', $shop_prices_to_item_id)
			: Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Item_Prices->getByPriceId($shop_list_of_prices_id);

		$oShop_Item_Price->shop_item_id = $shop_items_catalog_item_id;
		$oShop_Item_Price->shop_price_id = $shop_list_of_prices_id;
		$oShop_Item_Price->value = $shop_prices_to_item_value;

		$oShop_Item_Price->save();

		return $oShop_Item_Price->id;
	}

	/**
	 * Обновление связи товара и цены
	 *
	 * @param array $param массив параметров
	 * - int $param['shop_items_catalog_item_id'] Идентификатор товара
	 * - int $param['shop_list_of_prices_id'] Идентификатор цены
	 * - float $param['shop_prices_to_item_value'] Значение дополнительной цены
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['shop_list_of_prices_id'] = 1;
	 * $param['shop_prices_to_item_value'] = 333;
	 *
	 * $newid = $shop->UpdateItemPrice($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function UpdateItemPrice($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		$shop_items_catalog_item_id = Core_Type_Conversion::toInt($param['shop_items_catalog_item_id']);
		$shop_list_of_prices_id = Core_Type_Conversion::toInt($param['shop_list_of_prices_id']);
		$shop_prices_to_item_value = Core_Type_Conversion::toFloat($param['shop_prices_to_item_value']);

		$oShop_Item_Price = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Item_Prices->getByPriceId($shop_list_of_prices_id);

		if (!is_null($oShop_Item_Price))
		{
			$oShop_Item_Price->value = $shop_prices_to_item_value;
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Удаление цены для элемента каталога
	 *
	 * @param int $shop_items_catalog_id
	 * @param int $shop_list_of_prices_id (по умолчанию false)
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 1;
	 *
	 * $result = $shop->DeletePriceForItem($shop_items_catalog_item_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return int
	 */
	function DeletePriceForItem($shop_items_catalog_item_id, $shop_list_of_prices_id = FALSE)
	{
		$shop_items_catalog_item_id = Core_Type_Conversion::toInt($shop_items_catalog_item_id);

		// Если не передан идентификатор цены, удаляем ВСЕ цены для данного товара
		if ($shop_list_of_prices_id === FALSE)
		{
			return $this->DeletePricesForAllItem($shop_items_catalog_item_id);
		}
		else
		{
			$oShop_Item_Price = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Item_Prices->getByPriceId($shop_list_of_prices_id);

			if (is_null($oShop_Item_Price))
			{
				return FALSE;
			}
			$oShop_Item_Price->delete();
		}

		return TRUE;
	}

	/**
	 * Удаление всех дополнительных цен для товара
	 *
	 * @param int $shop_items_catalog_id идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_id = 1;
	 *
	 * $result = $shop->DeletePricesForAllItem($shop_items_catalog_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return resource
	 */
	function DeletePricesForAllItem($shop_items_catalog_id)
	{
		$shop_items_catalog_id = intval($shop_items_catalog_id);
		$aShop_Item_Prices = Core_Entity::factory('Shop_Item', $shop_items_catalog_id)
			->Shop_Item_Prices->deleteAll(FALSE);

		return TRUE;
	}

	/**
	 * Добавление информации о сопутствующем товаре
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_intermediate_id'] идетификатор записи о сопутствующем товаре
	 * - int $param['shop_items_catalog_item_id'] идентификатор товара, для которого вводится сопутствующий товар
	 * - int $param['sho_shop_items_catalog_item_id'] идентификатор сопутствующего товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['sho_shop_items_catalog_item_id'] = 167;
	 * $param['shop_intermediate_count'] = 1;
	 *
	 * $newid = $shop->InsertTyingProducts($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 *
	 * ?>
	 * </code>
	 * @return mixed
	 */
	function InsertTyingProducts($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['shop_intermediate_id']) || !$param['shop_intermediate_id'])
		{
			$param['shop_intermediate_id'] = NULL;
		}

		$oShop_Item_Associated = Core_Entity::factory('Shop_Item_Associated', $param['shop_intermediate_id']);

		$oShop_Item_Associated->shop_item_id = Core_Type_Conversion::toInt($param['shop_items_catalog_item_id']);
		$oShop_Item_Associated->shop_item_associated_id = Core_Type_Conversion::toInt($param['sho_shop_items_catalog_item_id']);

		$oShop_Item_Associated->count = isset($param['shop_intermediate_count'])
			? intval($param['shop_intermediate_count'])
			: 1;

		if(is_null($oShop_Item_Associated->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Item_Associated->user_id = intval($param['users_id']);
		}

		$oShop_Item_Associated->save();
		return $oShop_Item_Associated->id;
	}

	/**
	 * Устаревший метод обновления информации о сопутствующих товарах
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['shop_intermediate_id'] идетификатор записи о сопутствующем товаре
	 * - int $param['shop_items_catalog_item_id'] идентификатор товара, для которого вводится сопутствующий товар
	 * - int $param['sho_shop_items_catalog_item_id'] идентификатор сопутствующего товара
	 * @return array идентификатор результата обновления
	 */
	function UpdateTyingProducts($param)
	{
		return $this->InsertTyingProducts($param);
	}

	/**
	 * Получение информации о сопутствующем товаре
	 *
	 * @param int $shop_intermediate_id идентификатор записи о сопутствующем товаре
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 *
	 * @return array результат выборки
	 */
	function GetTyingProducts($shop_intermediate_id, $param = array())
	{
		$shop_intermediate_id = intval($shop_intermediate_id);
		$param = Core_Type_Conversion::toArray($param);

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_TYING_ITEMS';

			if ($in_cache = $cache->GetCacheContent($shop_intermediate_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oShop_Item_Associated = Core_Entity::factory('Shop_Item_Associated')->find($shop_intermediate_id);
		if(!is_null($oShop_Item_Associated->id))
		{
			$row = $this->getArrayShopItemAssociated($oShop_Item_Associated);

			if (class_exists('Cache') && !isset($param['cache_off']))
			{
				$cache->Insert($shop_intermediate_id, $row, $cache_name);
			}

			return $row;
		}

		return FALSE;
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров сопутствующими товарами.
	 * Заполнению подвергается массив $this->CacheTyingProducts[shop_items_catalog_item_id][] = shop_items_catalog_item_id
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 */
	function FillMemCacheTyingProducts($mas_items_in)
	{
		$mas_items_in = Core_Type_Conversion::toArray($mas_items_in);

		if (count($mas_items_in) > 0)
		{
			// Меняем местами значения и ключи массива
			$aTmp = array_flip($mas_items_in);

			// Вычислить пересечение массивов, сравнивая ключи
			$aTmpIntersect = array_intersect_key($aTmp, $this->CacheTyingProducts);

			if (count($aTmpIntersect) != count($mas_items_in))
			{
				// Заполянем сопутствующие товары пустыми массивами
				foreach ($mas_items_in as $shop_items_catalog_item_id)
				{
					$aShop_Item_Associateds = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Item_Associateds->findAll();

					foreach($aShop_Item_Associateds as $oShop_Item_Associated)
					{
						$this->CacheTyingProducts[$shop_items_catalog_item_id][] = $this->getArrayShopItemAssociated($oShop_Item_Associated);
					}
				}
			}
		}
	}

	/**
	 * Получение ID сопутствующих товаров для заданного товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 159;
	 *
	 * $row = $shop->GetTyingProductsForItem($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 *
	 * ?>
	 * </code>
	 * @return array массив идентификаторов сопутствующих товаров
	 */
	function GetTyingProductsForItem($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		if (isset($this->CacheTyingProducts[$shop_items_catalog_item_id]))
		{
			return $this->CacheTyingProducts[$shop_items_catalog_item_id];
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_TYING_LIST';
			if ($in_cache = $cache->GetCacheContent($shop_items_catalog_item_id, $cache_name))
			{
				$this->CacheTyingProducts[$shop_items_catalog_item_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		$this->CacheTyingProducts[$shop_items_catalog_item_id] = array();

		$aShop_Item_Associateds = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Item_Associateds->findAll();
		foreach($aShop_Item_Associateds as $oShop_Item_Associated)
		{
			$this->CacheTyingProducts[$shop_items_catalog_item_id][] = $this->getArrayShopItemAssociated($oShop_Item_Associated);
		}

		if (class_exists('Cache'))
		{
			$cache->Insert($shop_items_catalog_item_id, $this->CacheTyingProducts[$shop_items_catalog_item_id], $cache_name);
		}

		return $this->CacheTyingProducts[$shop_items_catalog_item_id];
	}

	/**
	 * Удаление сопутствующих товаров
	 *
	 * @param int $shop_intermediate_id идентификатор удаляемой записи о сопутствующих товарах
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_intermediate_id = 7;
	 *
	 * $result = $shop->DeleteTyingProducts($shop_intermediate_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return array результат удаления
	 */
	function DeleteTyingProducts($shop_intermediate_id)
	{
		$shop_intermediate_id = intval($shop_intermediate_id);
		Core_Entity::factory('Shop_Item_Associated', $shop_intermediate_id)->delete();
		return TRUE;
	}

	/**
	 * Формирование в памяти данных о свойствах групп магазина.
	 * Рекомендуется использоваться совместно с GetPropertysGroup() при выборе свойств всех групп магазина.
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param array $shop_properties_group_id_array массив свойств, для которых осущестлвяется выборка,
	 * если не передан (по умолчанию является пустым массивом) - выбираются все свойства
	 * param
	 */
	function FillMemCachePropertysGroup($shop_shops_id, $shop_properties_group_id_array = array())
	{
		$shop_shops_id = intval($shop_shops_id);

		// Очищаем массив свойств
		$this->PropertyGroupMass[$shop_shops_id] = array();

		/* Флаг, показывающий что в кэш выбраны все элементы, нужен для того, чтобы определять, есть ли
		 свойства у группы или нет в случае отсутствия этих данных в массиве.
		 Данные в массиве могут отсутствовать по двум причинам - свойства нет при ['fill_all'] == true или данные
		 для всех не выбирались а закэшировались для некоторых групп при единичном вызове, в таком случа
		 данные нужно запршиваться из базы*/
		$this->PropertyGroupMass[$shop_shops_id]['fill_all'] = TRUE;

		// Выбираем все дополнительные свойства для товара
		$oShop_Group_Properties = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Group_Properties;

		if (count($shop_properties_group_id_array) > 0)
		{
			$oShop_Group_Properties->queryBuilder()->where('property_id', 'IN', $shop_properties_group_id_array);
		}

		$aShop_Group_Properties = $oShop_Group_Properties->findAll();

		// Группы товаров
		$aShop_Groups = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Groups->findAll();

		$count = 0;

		foreach($aShop_Groups as $oShop_Group)
		{
			foreach($aShop_Group_Properties as $oShop_Group_Property)
			{
				$aValues = $oShop_Group_Property->Property->getValues($oShop_Group->id);

				if (isset($aValues[0]))
				{
					$this->PropertyGroupMass[$shop_shops_id][$oShop_Group->id][] = $this->getArrayGroupProperty($oShop_Group_Property->Property) + $this->getArrayGroupPropertyValue($aValues[0]);
					$count++;
				}
			}
		}

		return $count;
	}

	/**
	 * Получение данных о дополнительных свойствах группы магазина.
	 * Рекомендуется использоваться совместно с FillMemCachePropertysGroup() при выборе свойств всех групп магазина.
	 *
	 * @param int $shop_groups_id идентификатор группы
	 * @param int $shop_shops_id идентификатор магазина
	 * @return array массив с информацией о дополнительных свойствах
	 */
	function GetPropertysGroup($shop_groups_id, $shop_shops_id)
	{
		$shop_groups_id = intval($shop_groups_id);
		$shop_shops_id = intval($shop_shops_id);

		// В mem-кэше есть данные о свойствах текущей группы
		if (isset($this->PropertyGroupMass[$shop_shops_id][$shop_groups_id]))
		{
			return $this->PropertyGroupMass[$shop_shops_id][$shop_groups_id];
		}
		// Иначе если были выбраны все свойства инфосистемы, тогда свойств у данной группе нет
		elseif (isset($this->PropertyGroupMass[$shop_shops_id]['fill_all']))
		{
			return array();
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');

			$cache_element_name = $shop_groups_id . '_' . $shop_shops_id;
			$cache_name = 'SHOP_GROUP_PROPERTIS';

			if ($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name))
			{
				// Сохраняем в кэше в памяти
				$this->PropertyGroupMass[$shop_shops_id][$shop_groups_id] = $in_cache['value'];

				return $in_cache['value'];
			}
		}

		$this->PropertyGroupMass[$shop_shops_id][$shop_groups_id] = array();
		$aShop_Group_Properties = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Group_Properties->findAll();
		foreach($aShop_Group_Properties as $oShop_Group_Property)
		{
			$aValues = $oShop_Group_Property->Property->getValues($shop_groups_id);

			if (isset($aValues[0]))
			{
				$this->PropertyGroupMass[$shop_shops_id][$shop_groups_id][] = $this->getArrayGroupProperty($oShop_Group_Property->Property) + $this->getArrayGroupPropertyValue($aValues[0]);
			}
		}

		// Если добавлено кэширование
		if (class_exists('Cache'))
		{
			$cache->Insert($cache_element_name, $this->PropertyGroupMass[$shop_shops_id][$shop_groups_id], $cache_name);
		}

		return $this->PropertyGroupMass[$shop_shops_id][$shop_groups_id];
	}

	/**
	 * Определение, является ли группа $group_id непосредственным потомком группы $group_parent_id
	 *
	 * @param int $group_id идентификатор группы-потомка
	 * @param int $group_parent_id идентификатор группы-родителя
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $group_id = 600;
	 * $group_parent_id = 586;
	 *
	 * $result = $shop->GroupIsParent($group_id, $group_parent_id);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return bool
	 */
	function GroupIsParent($group_id, $group_parent_id)
	{
		$group_id = intval($group_id);
		$group_parent_id = intval($group_parent_id);

		while ($group_id)
		{
			$row = $this->GetGroup($group_id);

			if ($row)
			{
				$group_id = $row['shop_groups_parent_id'];
			}
			else
			{
				break;
			}

			if ($group_id == $group_parent_id)
			{
				// Является потомком
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Служебный метод построения дерева xml для групп.
	 * Перед вызовом необходимо заполнить кэши методом FillMasGroup
	 *
	 * @param int $shop_id
	 * @param array $param
	 * - int $param['parent_group_id'] идентификатор родительской группы
	 * - $param['xml_show_group_property'] разрешает указание в XML значений свойств групп магазина, по умолчанию true
	 * - $param['xml_show_group_id'] массив идентификаторов групп для отображения в XML. Если не не передано - выводятся все группы
	 * - $param['xml_show_items_property_dir'] разрешает генерацию в XML групп свойств товаров, по умолчанию true
	 * - $param['xml_show_group_type'] тип генерации XML для групп, может принимать значения (по умолчанию 'tree'):
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>all - все группы всех уровней;
	 * <li>current - группы только текущего уровня;
	 * <li>tree - группы, находящиеся выше по дереву;
	 * <li>none - не выбирать группы.
	 * </ul>
	 * </li>
	 * </ul>
	 * <br />
	 * @return string
	 */
	function GetGroupsXmlTree($shop_id, $param)
	{
		$shop_id = intval($shop_id);
		$param = Core_Type_Conversion::toArray($param);

		$image = & singleton('Image');

		$param['parent_group_id'] = isset($param['parent_group_id'])
			? Core_Type_Conversion::toInt($param['parent_group_id'])
			: 0;

		!isset($param['xml_show_group_property']) && $param['xml_show_group_property'] = TRUE;
		!isset($param['xml_show_item_property']) && $param['xml_show_item_property'] = TRUE;
		!isset($param['current_group_id']) && $param['current_group_id'] = 0;
		!isset($param['xml_show_group_type']) && $param['xml_show_group_type'] = 'current';

		// сохраняем, т.к. $param['parent_group_id'] в цикле меняется
		$groups_parent_id = $param['parent_group_id'];

		$xmlData = '';
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		$param_group_access = array();
		$param_group_access['site_users_id'] = $site_user_id;
		$param_group_access['shop_group_id'] = $param['current_group_id'];
		$param_group_access['shop_id'] = $shop_id;

		if ($this->IssetAccessForShopGroup($param_group_access))
		{
			// При выводе дерева групп нам необходимо для текущего элемента узнать его родителя, чтобы на нижнем уровне
			// выбрать все группы
			if ($param['xml_show_group_type'] == 'tree')
			{
				$current_group_row = $this->GetGroup($param['current_group_id']);
			}

			if (isset($this->CacheGoupsIdTree[$shop_id][$param['parent_group_id']]))
			{
				foreach ($this->CacheGoupsIdTree[$shop_id][$param['parent_group_id']] as $group_id)
				{
					// Получаем данные о группе
					$row = $this->GetGroup($group_id, $param);

					$param_group_access = array();
					$param_group_access['site_users_id'] = $site_user_id;
					$param_group_access['shop_group_id'] = $group_id;
					$param_group_access['shop_id'] = $shop_id;
					$param_group_access['shop_group_info'] = $row;

					if($this->IssetAccessForShopGroup($param_group_access))
					{
						// Проверяем, является ли группа текущий выбранный узел родителем отображаемого узла
						$group_id_is_parent = $param['current_group_id'];
						$group_parent_id_is_parent = $group_id;

						/*
						 Комментарий к условию:
						 Условие "|| $row['information_groups_parent_id'] == $current_group_row['information_groups_parent_id']"
						 необходимо для выбора всех групп на уровне выводимой группы.

						 Условие "|| $param['current_group_id'] == $groups_parent_id" чтобы вывести всех потомков
						 текущей группы, при этом сравнивать нужно именно с $groups_parent_id,
						 т.к. $param['parent_group_id'] в цикле меняется
						 */
						// $row['shop_groups_parent_id'] == 0 - корневые группы выбираем всегда
						if ($row
						&& (($param['xml_show_group_type'] == 'tree'
						&& ($row['shop_groups_parent_id'] == 0
						|| $param['current_group_id'] == $groups_parent_id
						|| $this->GroupIsParent($group_id_is_parent, $group_parent_id_is_parent)
						|| $row['shop_groups_parent_id'] == $current_group_row['shop_groups_parent_id']))
						|| $param['xml_show_group_type'] == 'current'
						|| $param['xml_show_group_type'] == 'all'
						/* Если был передан массив групп, подлежащих отображению*/
						|| (isset($param['xml_show_group_id'])
						&& is_array($param['xml_show_group_id'])
						&& in_array($group_id, $param['xml_show_group_id']))
						))
						{
							$uploaddir = "/" . $this->GetGroupDir($group_id);

							$size_big_image = array(
							'width' => 0,
							'height' => 0
							);
							$size_small_image = array(
							'width' => 0,
							'height' => 0
							);

							if ($row['shop_groups_image'] != '')
							{
								$item_image = $row['shop_groups_image'];

								// Размеры большой картинки
								$size_big_image = array(
								'width' => $row['shop_groups_big_image_width'],
								'height' => $row['shop_groups_big_image_height']
								);
							}
							else
							{
								$item_image = '';
							}

							if ($row['shop_groups_small_image'])
							{
								$item_small_image = $row['shop_groups_small_image'];

								// Размеры малой картинки
								$size_small_image = array(
								'width' => $row['shop_groups_small_image_width'],
								'height' => $row['shop_groups_small_image_height']
								);
							}
							else
							{
								$item_small_image = '';
							}

							$xmlData .= '<group id="' . Core_Type_Conversion::toInt($row['shop_groups_id']) . '" parent="' . Core_Type_Conversion::toInt($row['shop_groups_parent_id']) . '">' . "\n";
							$xmlData .= '<name>' . str_for_xml($row['shop_groups_name']) . '</name>' . "\n";
							$xmlData .= '<description>' . str_for_xml($row['shop_groups_description']) . '</description>' . "\n";

							if (!empty ($item_image))
							{
								$xmlData .= '<image width="' . $size_big_image['width'] . '" height="' . $size_big_image['height'] . '">' . $uploaddir . str_for_xml($item_image) . '</image>' . "\n";
							}

							if (!empty ($item_small_image))
							{
								$xmlData .= '<small_image width="' . $size_small_image['width'] . '" height="' . $size_small_image['height'] . '">' . $uploaddir . str_for_xml($item_small_image) . '</small_image>' . "\n";
							}

							$xmlData .= '<order>' . Core_Type_Conversion::toInt($row['shop_groups_order']) . '</order>' . "\n";
							$xmlData .= '<indexation>' . Core_Type_Conversion::toInt($row['shop_groups_indexation']) . '</indexation>' . "\n";
							$xmlData .= '<path>' . str_for_xml(rawurlencode(Core_Type_Conversion::toStr($row['shop_groups_path']))) . '</path>' . "\n";
							$xmlData .= '<fullpath>' . str_for_xml($this->GetPathGroup(Core_Type_Conversion::toInt($row['shop_groups_id']), '')) . '</fullpath>' . "\n";

							/* Определяем число элементов и групп в группе*/
							if (isset($param['select_groups']) && is_array($param['select_groups']))
							{
								$group_count_info = $this->GetCountItemsAndGroups(Core_Type_Conversion::toInt($row['shop_groups_id']), $shop_id, TRUE, $param);
							}
							else
							{
								$group_count_info = & $row;
							}

							$xmlData .= '<count_items>' . str_for_xml(Core_Type_Conversion::toStr($group_count_info['count_items'])) . '</count_items>' . "\n";
							$xmlData .= '<count_groups>' . str_for_xml(Core_Type_Conversion::toStr($group_count_info['count_groups'])) . '</count_groups>' . "\n";
							$xmlData .= '<count_all_items>' . str_for_xml(Core_Type_Conversion::toStr($group_count_info['count_all_items'])) . '</count_all_items>' . "\n";
							$xmlData .= '<count_all_groups>' . str_for_xml(Core_Type_Conversion::toStr($group_count_info['count_all_groups'])) . '</count_all_groups>' . "\n";

							$xmlData .= '<seo_title>' . str_for_xml(Core_Type_Conversion::toStr($row['shop_groups_seo_title'])) . '</seo_title>' . "\n";
							$xmlData .= '<seo_description>' . str_for_xml(Core_Type_Conversion::toStr($row['shop_groups_seo_description'])) . '</seo_description>' . "\n";
							$xmlData .= '<seo_keywords>' . str_for_xml(Core_Type_Conversion::toStr($row['shop_groups_seo_keywords'])) . '</seo_keywords>' . "\n";
							$param['parent_group_id'] = $row['shop_groups_id'];

							// Обработка дополнительных свойств группы
							if ($param['xml_show_group_property'])
							{
								// получаем св-ва группы
								$mas_property_group = $this->GetPropertysGroup($group_id, $shop_id);

								///if (mysql_num_rows($property_data_res) > 0)
								if (is_array($mas_property_group))
								{
									$xmlData .= '<propertys>' . "\n";

									//while($property_data_row = mysql_fetch_assoc($property_data_res))
									foreach ($mas_property_group as $property_data_row)
									{
										if ($property_data_row['shop_properties_group_name'] != '')
										{
											switch ($property_data_row['shop_properties_group_type'])
											{
												// Файл
												case 1:
													{
														if (isset($property_data_row['shop_properties_group_value_file']) && $property_data_row['shop_properties_group_value_file'] != '')
														{
															$xmlData .= '<property type="File" xml_name="' . $property_data_row['shop_properties_group_xml_name'] . '" id="' . $property_data_row['shop_properties_group_id'] . '"
		                                                    										parent_id="' . $property_data_row['shop_properties_groups_dir_id'] . '" 										value_id="' .$property_data_row['shop_properties_group_value_id'] . '">' . "\n";

															$xmlData .= '<name>' . str_for_xml($property_data_row['shop_properties_group_name']) . '</name>' . "\n";

															$xmlData .= '<value>' . str_for_xml($property_data_row['shop_properties_group_value_value']) . '</value>' . "\n";

															$xmlData .= '<default_value>' . str_for_xml($property_data_row['shop_properties_group_default_value']) . '</default_value>' . "\n";

															$xmlData .= '<order>' . $property_data_row['shop_properties_group_order'] . '</order>' . "\n";

															$file_path = CMS_FOLDER . $uploaddir . $property_data_row['shop_properties_group_value_file'];

															if (is_file($file_path)
															&& is_readable($file_path)
															&& filesize($file_path) > 12)
															{
																if (Core_Image::instance()->exifImagetype($file_path))
																{
																	$size_property_big_image = $image->GetImageSize($file_path);
																	$atributs = ' width="' . $size_property_big_image['width'] . '"  height="' . $size_property_big_image['height'] . '"';
																}
																else
																{
																	$atributs = '';
																}

																// Определяем размер файла в байтах
																$size = @ filesize($file_path);

																$atributs .= ' size="' . $size . '"';

																$xmlData .= '<property_file_path ' . trim($atributs) . '>' . $uploaddir.str_for_xml(rawurlencode($property_data_row['shop_properties_group_value_file'])) . '</property_file_path>' . "\n";
															}

															$file_path = CMS_FOLDER . $uploaddir . $property_data_row['shop_properties_group_value_file_small'];

															// проверяем существует ли файл маленькой картинки
															if (is_file($file_path)
															&& is_readable($file_path)
															&& filesize($file_path) > 12)
															{
																$xmlData .= '<small_image>' . "\n";
																// если дополнительное свойство является изображением, тегу value
																// дописываем атрибуты width - ширина и height - высота
																if (Core_Image::instance()->exifImagetype($file_path))
																{
																	$size_property_small_image = $image->GetImageSize($file_path);
																	$atributs = ' width="' . $size_property_small_image['width'] . '"  height="' . $size_property_small_image['height'] . '"';
																}
																else
																{
																	$atributs = '';
																}

																// Определяем размер файла в байтах
																$size = @ filesize($file_path);

																$atributs .= ' size="' . $size . '"';

																$xmlData .= '<value>' . str_for_xml($property_data_row['shop_properties_group_value_file_small']) . '</value>' . "\n";

																$xmlData .= '<property_file_path ' . trim($atributs) . '>' . $uploaddir . str_for_xml(rawurlencode($property_data_row['shop_properties_group_value_file_small'])) . '</property_file_path>' . "\n";

																$xmlData .= '</small_image>' . "\n";
															}

															$xmlData .= '</property>' . "\n";
														}
														break;
													}
													// Список
												case 2:
													{
														if ($property_data_row['shop_properties_group_value_value'] != '')
														{
															if (class_exists('lists'))
															{
																// проверяем существование объекта типа Lists и наличие модуля Lists
																$lists = & singleton('lists');

																$xmlData .= '<property type="List" xml_name="' . $property_data_row['shop_properties_group_xml_name'] . '" id="' . $property_data_row['shop_properties_group_id'] . '" parent_id="' . $property_data_row['shop_properties_groups_dir_id'].'" value_id="' . $property_data_row['shop_properties_group_value_id'] . '">' . "\n";

																$xmlData .= '<name>' . str_for_xml($property_data_row['shop_properties_group_name']) . '</name>' . "\n";

																$xmlData .= '<default_value>' . str_for_xml($property_data_row['shop_properties_group_default_value']) . '</default_value>' . "\n";

																$xmlData .= '<order>' . $property_data_row['shop_properties_group_order'] . '</order>' . "\n";

																$row3 = $lists->GetListItem($property_data_row['shop_properties_group_value_value']);

																if ($row3)
																{
																	$xmlData .= '<value>' . str_for_xml($row3['lists_items_value']) . '</value>'."\n";
																	$xmlData .= '<lists_items_order>' . str_for_xml($row3['lists_items_order']) . '</lists_items_order>'."\n";
																	$xmlData .= '<description>' . str_for_xml($row3['lists_items_description']) . '</description>' . "\n";
																	$xmlData .= '<value_list_id>' . intval($row3['lists_items_id']) . '</value_list_id>' . "\n";
																}

																$xmlData .= '</property>' . "\n";
															}
														}
														break;
													}

													// Дата
												case 5:
													{
														$xmlData .= '<property type="Data" xml_name="' . $property_data_row['shop_properties_group_xml_name'] . '" id="' .$property_data_row['shop_properties_group_id'] . '" parent_id="' .$property_data_row['shop_properties_groups_dir_id'] . '" value_id="' . $property_data_row['shop_properties_group_value_id'] . '">' . "\n";
														$xmlData .= '<name>' . str_for_xml($property_data_row['shop_properties_group_name']) . '</name>' . "\n";
														$xmlData .= '<value>' . str_for_xml(Core_Date::sql2datetime($property_data_row['shop_properties_group_value_value'])) . '</value>' . "\n";
														$xmlData .= '<default_value>' . str_for_xml(Core_Date::sql2datetime($property_data_row['shop_properties_group_default_value'])) . '</default_value>' . "\n";
														$xmlData .= '<order>' . $property_data_row['shop_properties_group_order'] . '</order>' . "\n";
														$xmlData .= '</property>' . "\n";
														break;
													}
													// ДатаВремя
												case 6:
													{
														$xmlData .= '<property type="DataTime" xml_name="' . $property_data_row['shop_properties_group_xml_name'] . '" id="' . $property_data_row['shop_properties_group_id'] . '" parent_id="' . $property_data_row['shop_properties_groups_dir_id'] . '" value_id="'.$property_data_row['shop_properties_group_value_id'] . '">' . "\n";
														$xmlData .= '<name>' . str_for_xml($property_data_row['shop_properties_group_name']) . '</name>' . "\n";
														$xmlData .= '<value>' . str_for_xml(Core_Date::sql2datetime($property_data_row['shop_properties_group_value_value'])) . '</value>' . "\n";
														$xmlData .= '<default_value>' . str_for_xml(Core_Date::sql2datetime($property_data_row['shop_properties_group_default_value'])) . '</default_value>' . "\n";
														$xmlData .= '<order>' . $property_data_row['shop_properties_group_order'] . '</order>' . "\n";
														$xmlData .= '</property>' . "\n";
														break;
													}
													// Флажок
												case 7 :
													{
														$xmlData .= '<property type="Checkbox" xml_name="' . $property_data_row['shop_properties_group_xml_name'] . '" id="' . $property_data_row['shop_properties_group_id'] . '" parent_id="' .$property_data_row['shop_properties_groups_dir_id'] . '" value_id="' . $property_data_row['shop_properties_group_value_id'] . '">' . "\n";
														$xmlData .= '<name>' . str_for_xml($property_data_row['shop_properties_group_name']) . '</name>' . "\n";
														$xmlData .= '<value>' . str_for_xml($property_data_row['shop_properties_group_value_value']) . '</value>' . "\n";
														$xmlData .= '<default_value>' . str_for_xml($property_data_row['shop_properties_group_default_value']) . '</default_value>' . "\n";
														$xmlData .= '<order>' . $property_data_row['shop_properties_group_order'] . '</order>' . "\n";
														$xmlData .= '</property>' . "\n";
														break;
													}
												default :
													{
														switch ($property_data_row['shop_properties_group_type'])
														{
															// Строка
															case 0:
																$property_type_name = 'String';
															break;
															// Большое текстовое поле
															case 3:
																$property_type_name = 'Textarea';
															break;
															// Визуальный редактор
															case 4:
																$property_type_name = 'WYSIWYG';
															break;
															default :
																$property_type_name = 'Any';
															break;
														}

														$xmlData .= '<property type="' . $property_type_name . '" xml_name="' . $property_data_row['shop_properties_group_xml_name'] . '" id="' . $property_data_row['shop_properties_group_id'] . '" parent_id="' . $property_data_row['shop_properties_groups_dir_id'] . '" value_id="' . $property_data_row['shop_properties_group_value_id'] . '">' . "\n";
														$xmlData .= '<name>' . str_for_xml($property_data_row['shop_properties_group_name']) . '</name>' . "\n";
														$xmlData .= '<value>' . str_for_xml($property_data_row['shop_properties_group_value_value']) . '</value>' . "\n";
														$xmlData .= '<default_value>' . str_for_xml($property_data_row['shop_properties_group_default_value']) . '</default_value>' . "\n";
														$xmlData .= '<order>' . $property_data_row['shop_properties_group_order'] . '</order>' . "\n";
														$xmlData .= '</property>' . "\n";
														break;
													}
											}
										}
									}
									$xmlData .= '</propertys>' . "\n";
								}
							}

							// Если тип построения дерева групп - от текущей и выше
							if ($param['xml_show_group_type'] == 'tree')
							{
								$group_id = $param['current_group_id'];
								$group_parent_id = $param['parent_group_id'];

								// Проверяем, является ли группа потомком текущей группы
								// или она является этой же группой, чтобы выбрать прямых потомков
								if ($this->GroupIsParent($group_id, $group_parent_id) || $group_id == $group_parent_id)
								{
									$xmlData .= $this->GetGroupsXmlTree($shop_id, $param);
									// Прерываем, т.к. дальше проверять нет смысла
									//break;
								}
							}
							// если выводится только текущая группа - подгруппы не выводим
							elseif ($param['xml_show_group_type'] != 'current')
							{
								$xmlData .= $this->GetGroupsXmlTree($shop_id, $param);
								// Прерываем, т.к. дальше проверять нет смысла
								//break;
							}

							$xmlData .= '</group>' . "\n";
						}

						// после генерации XML для группы удаляем информацию о ней из кэша в памяти
						// Используем $group_parent_id_is_parent, т.к. $group_id перезаписывается выше
						if (isset($this->MasGroup[$group_parent_id_is_parent]))
						{
							unset($this->MasGroup[$group_parent_id_is_parent]);
						}
					}
				}
			}
		}

		return $xmlData;
	}

	/**
	 * Формирование массива групп верхнего уровня для данного магазина
	 *
	 * @param int $shop_id идентификатор магазина, для которого заполняем массив групп самого верхнего уровня
	 * @param array $param массив дополнительных параметров
	 * - $param['cache_off']  - если параметр установлен - данные не кэшируются
	 * - $param['groups_activity'] параметр, учитывающий активность групп при выборке. 1 - получаем информацию только об активных группах, если не задан, то активность группы не учитывается
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $param = array();
	 *
	 * $MasGroup = $shop->FillMasGroup($shop_shops_id, $param);
	 *
	 * // Распечатаем результат
	 * print_r ($MasGroup);
	 *
	 * ?>
	 * </code>
	 */
	function FillMasGroup($shop_shops_id, $param = array())
	{
		if ($shop_shops_id !== FALSE)
		{
			$shop_shops_id = intval($shop_shops_id);
		}

		// Очищаем текущий массив
		$this->MasGroup = array();
		$this->CacheGoupsIdTree[$shop_shops_id] = array();

		$result = $this->GetAllGroups($shop_shops_id, $param);

		if ($result)
		{
			foreach ($result as $row)
			{
				$this->MasGroup[$row['shop_groups_id']] = $row;
				$this->CacheGoupsIdTree[$shop_shops_id][$row['shop_groups_parent_id']][] = $row['shop_groups_id'];
			}
		}

		/*if (class_exists('Cache'))
		 {
		 $cache_name = 'SHOP_ALL_DISCOUNT_FOR_ITEM';
		 $cache = & singleton('Cache');

		 if ($this->MasGroup)
		 {
		 foreach ($this->MasGroup as $key => $value)
		 {
		 $cache->Insert($key, $value, $cache_name);
		 }
		 }
		 }*/

		return $this->MasGroup;
	}

	/**
	 * Формирование пути по группам товара
	 *
	 * @param int $shop_groups_id - идентификатор группы, для которой надо сформировать путь
	 * @param string $path - параметр, используемый при формировании пути по группам
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_groups_id = 586;
	 *
	 * $path = $shop->GetPathGroup($shop_groups_id);
	 *
	 * // Распечатаем результат
	 * echo $path;
	 * ?>
	 * </code>
	 * @return string - путь по группам от корневой до данной
	 */
	function GetPathGroup($shop_groups_id, $path = '', $level = 0, $param = array())
	{
		$shop_groups_id = intval($shop_groups_id);

		// Произошло зацикливание
		if ($level > 100)
		{
			return $path;
		}

		if ($shop_groups_id == 0)
		{
			return $path;
		}

		$row = isset($this->MasGroup[$shop_groups_id])
			? $this->MasGroup[$shop_groups_id]
			: $this->GetGroup($shop_groups_id, $param);

		if ($row)
		{
			$path = rawurlencode($row['shop_groups_path']) . '/' . $path;

			// Для исключения зацикливания рекурсии
			if ($shop_groups_id != $row['shop_groups_parent_id'])
			{
				return $this->GetPathGroup($row['shop_groups_parent_id'], $path, ++$level, $param);
			}
		}

		return FALSE;
	}

	/**
	 * Формирование пути к товару
	 *
	 * @param int $shop_items_catalog_item_id - идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 123;
	 *
	 * $path = $shop->GetPathItem($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * echo $path;
	 * ?>
	 * </code>
	 * @return string - путь по группам от корневой до данной
	 */
	function GetPathItem($shop_items_catalog_item_id, $param = array())
	{
		$row = $this->GetItem($shop_items_catalog_item_id, $param);

		if ($row)
		{
			// Основной товар
			if ($row['shop_items_catalog_modification_id'] == 0)
			{
				$fullpath_group_id = $row['shop_groups_id'];

				// Элемент пути к родителю модификации
				$paren_item_path = '';
			}
			else // Модификация, берем ID родительской группы
			{
				$modification_row = $this->GetItem($row['shop_items_catalog_modification_id'], $param);

				if ($modification_row)
				{
					$paren_item_path = rawurlencode($modification_row['shop_items_catalog_path']) . '/';
					$fullpath_group_id = $modification_row['shop_groups_id'];
				}
				else
				{
					$paren_item_path = '';
					$fullpath_group_id = 0;
				}
			}

			return $this->GetPathGroup($fullpath_group_id, '', 0, $param) . $paren_item_path;
		}

		return FALSE;
	}

	/**
	 * Определение цены для заданной группы пользователей
	 *
	 * @param int $user_group_id идентификатор группы пользователей
	 * @param int $shop_shops_id идентификатор интернет-магазина
	 * @return array or false массив информации о цене
	 */
	function SelectPrice($user_group_id, $shop_shops_id)
	{
		$user_group_id = intval($user_group_id);
		$shop_shops_id = intval($shop_shops_id);

		if (isset($this->CacheUserSelectPrice[$user_group_id][$shop_shops_id]))
		{
			return $this->CacheUserSelectPrice[$user_group_id][$shop_shops_id];
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ALL_PRICES_FOR_GROUP';
			$cache_key = $user_group_id . '_' . $shop_shops_id;

			if ($in_cache = $cache->GetCacheContent($cache_key, $cache_name))
			{
				$this->CacheUserSelectPrice[$user_group_id][$shop_shops_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		$oShop_Price = Core_Entity::factory('Shop_Price')->getBySiteuserGroupAndShop($user_group_id, $shop_shops_id);

		$this->CacheUserSelectPrice[$user_group_id][$shop_shops_id] = is_null($oShop_Price)
			? FALSE
			: $this->getArrayShopPrice($oShop_Price);

		if (class_exists('Cache'))
		{
			$cache->Insert($user_group_id, $this->CacheUserSelectPrice[$user_group_id][$shop_shops_id], $cache_name);
		}

		return $this->CacheUserSelectPrice[$user_group_id][$shop_shops_id];
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров
	 * ценами для товаров. Заполнению подвергается массив
	 * $this->CacheGetPriceForItem[$shop_list_of_prices_id . '_' . $shop_items_catalog_item_id]
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 */
	function FillMemCachePriceForItem($mas_items_in, $shop_shops_id)
	{
		$mas_items_in = Core_Type_Conversion::toArray($mas_items_in);

		if (count($mas_items_in))
		{
			// Получаем все цены и заполняем массив false
			$rAllPricesForShop = $this->GetAllPricesForShop($shop_shops_id);

			if ($rAllPricesForShop)
			{
				while ($aPricesForShop = mysql_fetch_assoc($rAllPricesForShop))
				{
					foreach ($mas_items_in as $shop_items_catalog_item_id)
					{
						$name = $aPricesForShop['shop_list_of_prices_id'] . '_' . $shop_items_catalog_item_id;
						$this->CacheGetPriceForItem[$name] = FALSE;
					}

					reset($mas_items_in);
				}

				$oShop_Item_Prices = Core_Entity::factory('Shop_Item_Price');
				$oShop_Item_Prices
					->queryBuilder()
					->where('shop_item_id', 'IN', $mas_items_in);
				$aShop_Item_Prices = $oShop_Item_Prices->findAll(FALSE);

				foreach($aShop_Item_Prices as $oShop_Item_Price)
				{
					$row = $this->getArrayShopItemPrice($oShop_Item_Price);
					$name = $row['shop_list_of_prices_id'] . '_' . $row['shop_items_catalog_item_id'];
					$this->CacheGetPriceForItem[$name] = $row;
				}
			}
		}
	}

	/**
	 * Определение наличия указанной цены для заданного товара
	 *
	 * @param int $shop_list_of_prices_id идентификатор цены
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_list_of_prices_id = 8;
	 * $shop_items_catalog_item_id = 159;
	 *
	 * $row = $shop->GetPriceForItem($shop_list_of_prices_id, $shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array or false результат выборки
	 */
	function GetPriceForItem($shop_list_of_prices_id, $shop_items_catalog_item_id, $param = array())
	{
		$shop_list_of_prices_id = intval($shop_list_of_prices_id);
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		$cache_item_name = $shop_list_of_prices_id . '_' . $shop_items_catalog_item_id;

		if (isset($this->CacheGetPriceForItem[$cache_item_name]))
		{
			return $this->CacheGetPriceForItem[$cache_item_name];
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_PRICE_FOR_ITEM';
			if ($in_cache = $cache->GetCacheContent($cache_item_name, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oShop_Item_Price = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)
			->Shop_Item_Prices->getByPriceId($shop_list_of_prices_id);

		$row = !is_null($oShop_Item_Price)
			? $this->getArrayShopItemPrice($oShop_Item_Price)
			: FALSE;

		// Запись в файловый кэш
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($cache_item_name, $row, $cache_name);
		}

		$this->CacheGetPriceForItem[$cache_item_name] = $row;

		return $row;
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров скидками.
	 * Заполнению подвергается массив $this->CacheDiscountsForItem[shop_items_catalog_item_id][]
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 */
	function FillMemCacheDiscountsForItem($mas_items_in)
	{
		$mas_items_in = to_array($mas_items_in);

		if (count($mas_items_in) > 0)
		{
			// Меняем местами значения и ключи массива
			$aTmp = array_flip($mas_items_in);

			// Вычислить пересечение массивов, сравнивая ключи
			$aTmpIntersect = array_intersect_key($aTmp, $this->CacheDiscountsForItem);

			if (count($aTmpIntersect) != count($mas_items_in))
			{
				// Заполянем сопутствующие товары пустыми массивами
				foreach ($mas_items_in as $key => $shop_items_catalog_item_id)
				{
					$this->CacheDiscountsForItem[$shop_items_catalog_item_id] = FALSE;
				}

				$now = date("Y-m-d H:i:s");

				$queryBuilder = Core_QueryBuilder::select(
						array('shop_item_discounts.shop_item_id', 'shop_items_catalog_item_id'),
						array('shop_discounts.id', 'shop_discount_id')
					)
					->from('shop_item_discounts')
					->join('shop_discounts', 'shop_item_discounts.shop_discount_id', '=', 'shop_discounts.id')
					->where('shop_item_discounts.shop_item_id', 'IN', $mas_items_in)
					->where('shop_discounts.active', '=', 1)
					->where('shop_discounts.start_datetime', '<', $now)
					->where('shop_discounts.end_datetime', '>', $now)
					->where('shop_discounts.deleted', '=', 0);

				$aResult = $queryBuilder->execute()->asAssoc()->result();
				foreach($aResult as $row)
				{
					$this->CacheDiscountsForItem[$row['shop_items_catalog_item_id']][] = $row['shop_discount_id'];
				}
			}
		}
	}

	/**
	 * Получение информации обо всех скидках для данного товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 *
	 * $row = $shop->GetAllDiscountsForItem($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed массив идентификаторов скидок или false
	 */
	function GetAllDiscountsForItem($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		if (isset($this->CacheDiscountsForItem[$shop_items_catalog_item_id]))
		{
			return $this->CacheDiscountsForItem[$shop_items_catalog_item_id];
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ALL_DISCOUNT_FOR_ITEM';
			if ($in_cache = $cache->GetCacheContent($shop_items_catalog_item_id, $cache_name))
			{
				$this->CacheDiscountsForItem[$shop_items_catalog_item_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		$this->CacheDiscountsForItem[$shop_items_catalog_item_id] = FALSE;
		$now = date("Y-m-d H:i:s");

		$queryBuilder = Core_QueryBuilder::select(
				array('shop_item_discounts.shop_item_id', 'shop_items_catalog_item_id'),
				array('shop_discounts.id' , 'shop_discount_id')
			)
			->from('shop_discounts')
			->join('shop_item_discounts', 'shop_discounts.id', '=', 'shop_item_discounts.shop_discount_id')
			->where('shop_item_id', '=', $shop_items_catalog_item_id)
			->where('active', '=', 1)
			->where('start_datetime', '<', $now)
			->where('end_datetime', '>', $now)
			->where('shop_discounts.deleted', '=', 0);

		$aResult = $queryBuilder->execute()->asAssoc()->result();
		foreach($aResult as $row)
		{
			$this->CacheDiscountsForItem[$shop_items_catalog_item_id][] = $row['shop_discount_id'];
		}

		if (class_exists('Cache'))
		{
			$cache->Insert($shop_items_catalog_item_id, $this->CacheDiscountsForItem[$shop_items_catalog_item_id], $cache_name);
		}

		return $this->CacheDiscountsForItem[$shop_items_catalog_item_id];
	}

	/**
	 * Определение коэффициента пересчета валюты товара в валюту магазина
	 *
	 * @param float $item_shop_currency_id идентифкатор валюты товара
	 * @param float $shop_currency_id идентифкатор валюты магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $item_shop_currency_id = 2;
	 * $shop_currency_id = 1;
	 *
	 * $round = $shop->GetCurrencyCoefficientToShopCurrency($item_shop_currency_id, $shop_currency_id);
	 *
	 * // Распечатаем результат
	 * echo $round;
	 * ?>
	 * </code>
	 * @return float коэффициент пересечета валюты товара в валюту магазина
	 */
	function GetCurrencyCoefficientToShopCurrency($item_shop_currency_id, $shop_currency_id)
	{
		$item_shop_currency_id = intval($item_shop_currency_id);
		$shop_currency_id = intval($shop_currency_id);

		if ($item_shop_currency_id == 0 || $shop_currency_id == 0)
		{
			return 0;
		}

		return Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
			Core_Entity::factory('Shop_Currency', $item_shop_currency_id), Core_Entity::factory('Shop_Currency', $shop_currency_id)
		);
	}

	/**
	 * Определение цены товара для заданного пользователя
	 *
	 * @param int $site_users_id идентификатор пользователя
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @param int $item_row строка даных о товаре, необязательное поле
	 * @param array $param массив дополнительных параметров
	 * - $param['item_count'] количество товара, для которого необходимо расчитать цену, используется при определении цены для специальных цен в зависимости от партии
	 * - $param['shop_special_prices_id'] идентификатор специальной цены, которую необходимо применить при расчете
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $site_users_id = 19;
	 * $shop_items_catalog_item_id = 159;
	 *
	 * $row = $shop->GetPriceForUser($site_users_id, $shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвращает массив значений цен для данного пользователя
	 * - $price['tax'] сумма налога
	 * - $price['price'] цена с учетом валюты без налога
	 * - $price['price_tax'] цена с учетом налога
	 * - $price['price_discount'] цена с учетом налога и со скидкой
	 */
	function GetPriceForUser($site_users_id, $shop_items_catalog_item_id, $item_row = array(), $param = array())
	{
		$item_row = count($item_row) == 0
			? $this->GetItem($shop_items_catalog_item_id)
			: Core_Type_Conversion::toArray($item_row);

		// Товар не выбран, возвращем ложь
		if (!$item_row)
		{
			return FALSE;
		}

		// Количество товара по умолчанию равно 1
		if (!isset($param['item_count']))
		{
			$param['item_count'] = 1;
		}

		// Получаем данные о магазине
		$shop_row = $this->GetShop($item_row['shop_shops_id']);

		// Определяем коэффициент пересчета
		$currency_k = $this->GetCurrencyCoefficientToShopCurrency($item_row['shop_currency_id'], $shop_row['shop_currency_id']);

		$price = array();
		$price['tax'] = 0; // Только налог
		$price['price'] = $item_row['shop_items_catalog_price'];

		// Пользователь задан - цена определяется из таблицы товаров
		if ($site_users_id && class_exists('SiteUsers'))
		{
			// Определяем группу, в которой находится пользователь
			$SiteUsers = & singleton('SiteUsers');
			$a_UserGroup = $SiteUsers->GetGroupsForUser($site_users_id);

			$count_group = count(Core_Type_Conversion::toArray($a_UserGroup));

			// Выбираем цену из каталога товаров
			// И вычисляем ее с учетом валюты

			// Саму цену не учитываем
			//$price_array[] = $price['price'];
			$price_array = array();

			// Перебираем все группы по очереди
			foreach ($a_UserGroup as $user_group_id)
			{
				// Выбираем цену для группы из таблицы цен
				$row_price = $this->SelectPrice($user_group_id, $item_row['shop_shops_id']);

				// определена ли цена для группы
				if ($row_price)
				{
					// Если да, то
					// Смотрим, определена ли такая цена для данного товара
					$res = $this->GetPriceForItem($row_price['shop_list_of_prices_id'], $shop_items_catalog_item_id);

					if ($res)
					{
						$price_array[] = $res['shop_prices_to_item_value'];
					}
				}
			}


			if (count($price_array) > 0)
			{
				$price['price'] = min($price_array);
			}
		}

		// Определяем размер скидки в зависимости от количества в корзине
		$item_special_prices_array = $this->GetSpecialPricesForItem($shop_items_catalog_item_id);

		if ($item_special_prices_array)
		{
			// Определеяем идентификатор спеццены подходящей товару по количеству
			$new_price = !isset($param['shop_special_prices_id'])
				? $this->GetSpecialPriceForItem($shop_items_catalog_item_id, $param['item_count'])
				: $this->GetSpecialPrice($param['shop_special_prices_id']);

			if ($new_price)
			{
				$price['price'] = $new_price['shop_special_prices_percent'] != 0
					? $price['price'] * $new_price['shop_special_prices_percent'] / 100
					: $new_price['shop_special_prices_price'];
			}
		}

		// Умножаем цену товара на курс валюты в базовой валюте
		$price['price'] = $price['price'] * $currency_k;

		// Выбираем информацию о налогах
		if ($item_row['shop_tax_id'] != 0)
		{
			// Извлекаем информацию о налоге
			$tax = $this->GetTax($item_row['shop_tax_id']);

			// Если он не входит в цену
			if ($tax && $tax['shop_tax_is_in_price'] == 0)
			{
				// То считаем цену с налогом
				$price['tax'] = $tax['shop_tax_rate'] / 100 * $price['price'];
				$price['price_tax'] = $price['price'] + $price['tax'];
			}
			else
			{
				$price['tax'] = $price['price'] / (100 + $tax['shop_tax_rate']) * $tax['shop_tax_rate'];
				$price['price_tax'] = $price['price'];
			}
		}
		else
		{
			$price['price_tax'] = $price['price'];
		}

		// Определены ли скидки на товар
		$discount = $this->GetAllDiscountsForItem($item_row['shop_items_catalog_item_id']);

		if ($discount)
		{
			// определяем количество скидок на товар
			$percent = 0;

			// Цикл по идентификаторам скидок для товара
			foreach ($discount as $key => $shop_item_discount_id)
			{
				// Получаем информацию о скидке
				$row_discount = $this->GetDiscount($shop_item_discount_id);

				if ($row_discount)
				{
					$price['discount'][$key]['name'] = $row_discount['shop_discount_name'];
					$price['discount'][$key]['value'] = $row_discount['shop_discount_percent'];

					// в цикле определяем суммарный процент скидок
					$percent = $percent +Core_Type_Conversion::toFloat($row_discount['shop_discount_percent']);
				}
			}

			// определяем суммарную величину скидки в валюте
			$total_discount = $price['price_tax'] * $percent / 100;

			// вычисляем цену со скидкой как ее разность с величиной скидки
			$price['price_discount'] = $price['price_tax'] - $total_discount;
			//$price['price'] = $price['price_discount'];
			//$price['price_tax'] = $price['price_discount'];
		}
		else
		{
			// если скидок нет, то price_discount положим равным price
			$price['price_discount'] = $price['price_tax'];
		}

		// Округляем значения
		// Переводим с научной нотации 1Е+10 в десятичную
		$price['tax'] = $this->Round($price['tax']);
		$price['price'] = $this->Round($price['price']);
		$price['price_discount'] = $this->Round($price['price_discount']);
		$price['price_tax'] = $this->Round($price['price_tax']);

		return $price;
	}

	/**
	 * Получение суммы налога, количества элементов и их общей цены для заданного пользователя
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param int $site_users_id идентификатор пользователя
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $site_users_id = 19;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 *	$site_users_id = 0;
	 * }
	 *
	 * $row = $shop->SelectAllItemsFromCartForUser($shop_id, $site_users_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array ассоциативный массив, где элемент с индексом 'price' - общая цена товаров, а элемент с индексом 'quantity' - общее число товаров
	 */
	function SelectAllItemsFromCartForUser($shop_id, $site_users_id)
	{
		$shop_id = intval($shop_id);
		$site_users_id = intval($site_users_id);

		$a_items = array();

		// Элементы массива для товаров
		$a_items['tax'] = 0;
		$a_items['price'] = 0;
		$a_items['quantity'] = 0;
		$a_items['weight'] = 0;

		// Элементы массива для отложенных товаров
		$a_items['postpone_items'] = array();
		$a_items['postpone_items']['price'] = 0;
		$a_items['postpone_items']['quantity'] = 0;
		$a_items['postpone_items']['weight'] = 0;
		$a_items['postpone_items']['tax'] = 0;

		// Передан идентификатор пользователя
		if ($site_users_id)
		{
			// Выбираем товары из корзины данного пользователя
			$aShop_Carts = Core_Entity::factory('Shop', $shop_id)->Shop_Carts->getBySiteuserId($site_users_id);

			foreach($aShop_Carts as $oShop_Cart)
			{
				// Расчитываем цену товара с учетом скидок и т.д.
				$price = $this->GetPriceForUser($site_users_id, $oShop_Cart->shop_item_id, array(), array(
				'item_count' =>  $oShop_Cart->quantity
				));

				$oShop_Item = Core_Entity::factory('Shop_Item')->find($oShop_Cart->shop_item_id);

				if (!is_null($oShop_Item->id))
				{
					// Проверяем является ли товар отложенным
					if ($oShop_Cart->postpone == 0)
					{
						// Товар не отложен, добавляем информацию о нем в массив для товаров
						// Общая стоимость
						$a_items['price'] += Core_Type_Conversion::toFloat($price['price_discount']) * $oShop_Cart->quantity;

						// Общее количесвто
						$a_items['quantity'] += $oShop_Cart->quantity;

						// Общий вес
						$a_items['weight'] += $oShop_Item->weight * $oShop_Cart->quantity;

						// Общий налог
						$a_items['tax'] += Core_Type_Conversion::toFloat($price['tax']) * floatval($oShop_Cart->quantity);
					}
					else
					{
						// Товар отложен, добавляем информацию о нем в массив для отложенных товаров
						// Общая стоимость
						$a_items['postpone_items']['price'] = $a_items['price'] + Core_Type_Conversion::toFloat($price['price_discount']) * floatval($oShop_Cart->quantity);

						// Общее количесвто
						$a_items['postpone_items']['quantity'] = $a_items['quantity'] + floatval($oShop_Cart->quantity);

						// Общий вес
						$a_items['postpone_items']['weight'] = $a_items['weight'] + floatval($oShop_Item->weight) * floatval($oShop_Cart->quantity);

						// Общий налог
						$a_items['postpone_items']['tax'] = $a_items['tax'] + Core_Type_Conversion::toFloat($price['tax']) * floatval($oShop_Cart->quantity);
					}
				}
			}
		}
		else
		{
			// Идентификатор пользователя не задан (равен нулю или ложь)
			$a_items = $this->SelectAllItemsFromCookieCart($shop_id);
		}

		// Получаем информацию о валюте для магазина
		$row_currency_shop = $this->GetCurrencyForShop($shop_id);
		if ($row_currency_shop)
		{
			$a_items['currency'] = $row_currency_shop['shop_currency_name'];
		}

		return $a_items;
	}

	/**
	 * Получение общего количества товаров, их общей стоимости и веса из корзины
	 * <br />(в случае, когда пользователь не задан и данные корзины хранятся в кукисах)
	 * @param int $shop_shops_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->SelectAllItemsFromCookieCart($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 */
	function SelectAllItemsFromCookieCart($shop_shops_id)
	{
		$shop_shops_id = Core_Type_Conversion::toInt($shop_shops_id);

		// Элементы массива для товаров
		$a_items['price'] = 0;
		$a_items['quantity'] = 0;
		$a_items['weight'] = 0;
		$a_items['tax'] = 0;

		// Элементы массива для отложенных товаров
		$a_items['postpone_items'] = array();
		$a_items['postpone_items']['price'] = 0;
		$a_items['postpone_items']['quantity'] = 0;
		$a_items['postpone_items']['weight'] = 0;
		$a_items['postpone_items']['tax'] = 0;

		$CART = array();

		// Если модуль пользователей сайта есть - работаем с корзиной на кукисах
		if (class_exists("SiteUsers"))
		{
			$CART = $this->GetCart();
		}
		else
		{
			// Если в сессии есть корзина - получаем ее.
			if (!empty ($_SESSION['CART']))
			{
				$CART = $_SESSION['CART'];
			}
		}

		// Проверяем количество элементов в корзине
		if (count(Core_Type_Conversion::toArray($CART[$shop_shops_id])))
		{
			// Разбираем массив для данного магазина
			$result = Core_Type_Conversion::toArray($CART[$shop_shops_id]);

			// В цикле разбираем массив
			foreach ($result as $row)
			{
				// Выбираем информацию об элементе
				$row_item = $this->GetItem(Core_Type_Conversion::toInt($row['shop_items_catalog_item_id']));

				// Если выбрали информацию - добавляем данные о товаре
				if ($row_item)
				{
					// Расчитываем цену товара с учетом скидок и т.д.
					$price = $this->GetPriceForUser(FALSE, $row_item['shop_items_catalog_item_id'], array(), array(
					'item_count' => $row['shop_cart_item_quantity']
					));

					// Проверяем не является ли товар отложенным
					if (Core_Type_Conversion::toInt($row['shop_cart_flag_postpone']) == 0)
					{
						// Товар не отложен, добавляем информацию о нем в массив для товаров
						// Общая стоимость
						$a_items['price'] = $a_items['price'] + Core_Type_Conversion::toFloat($price['price_discount']) * Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
						// Общее количесвто
						$a_items['quantity'] = $a_items['quantity'] + Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
						// Общий вес
						$a_items['weight'] = $a_items['weight'] + Core_Type_Conversion::toFloat($row_item['shop_items_catalog_weight']) * Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
						// Общий налог
						$a_items['tax'] = $a_items['tax'] +  Core_Type_Conversion::toFloat($price['tax']) * Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
					}
					else
					{
						// Товар отложен, добавляем информацию о нем в массив для отложенных товаров
						// Общая стоимость
						$a_items['postpone_items']['price'] = $a_items['price'] + Core_Type_Conversion::toFloat($price['price_discount']) * Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
						// Общее количесвто
						$a_items['postpone_items']['quantity'] = $a_items['quantity'] + Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
						// Общий вес
						$a_items['postpone_items']['weight'] = $a_items['weight'] + Core_Type_Conversion::toFloat($row_item['shop_items_catalog_weight']) * Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
						// Общий налог
						$a_items['postpone_items']['tax'] = $a_items['tax'] +  Core_Type_Conversion::toFloat($price['tax']) * Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
					}
				}
			}
		}

		// Возвращаем полученный массив
		return $a_items;
	}

	/**
	 * Добавления информации о товаре в таблицу корзины
	 *
	 * @param array $param ассоциативный массив параметров
	 * - $param['user_id'] int идентификатор пользователя (при значение 0 или false - данные вставляем в кукисы)
	 * - $param['shop_id'] int идентификатор магазина
	 * - $param['item_id'] int идентификатор товара
	 * - $param['postpone'] int флаг, указывающий на то является ли данный товар отложенным
	 * - $param['count'] int колитчество заказываемого товара
	 * - $param['warehouse_id'] int идентификатор склада
	 * - $param['get_info_isset_shop'] boolean флаг необходимости проверки наличия магазина
	 *
	 * @return mixed идентификатор вставленной записи или идентификатор единицы каталога, если вставка осуществлялась в кукисы, возвращает ложь в случае возникновения ошибки
	 */
	function InsertToCart($param)
	{
		$site_users_id = Core_Type_Conversion::toInt($param['user_id']);
		$shop_id = Core_Type_Conversion::toInt($param['shop_id']);
		$item_id = Core_Type_Conversion::toInt($param['item_id']);
		$postpone = Core_Type_Conversion::toInt($param['postpone']);
		$quantity = Core_Type_Conversion::toFloat($param['count']);
		$warehouse_id = Core_Type_Conversion::toInt($param['warehouse_id']);

		// При попытке вставить отрицательное значение - устанавливаем в 1
		if ($quantity < 0)
		{
			$quantity = 1;
		}

		// Флаг необходимости проверки магазина
		if (isset($param['get_info_isset_shop']) && $param['get_info_isset_shop'] == FALSE)
		{
			$param['get_info_isset_shop'] = FALSE;
		}
		else
		{
			$param['get_info_isset_shop'] = TRUE;
		}

		// Проверяем наличие данного интернет-магазина(если это необходимо)
		if ($param['get_info_isset_shop'] == TRUE)
		{
			$row_shop = $this->GetShop($shop_id);
		}

		// Магазин существует - добавляем данные в корзину
		if ((isset($row_shop) && $row_shop != FALSE) || $param['get_info_isset_shop'] == FALSE)
		{
			// Проверяем существование элемента
			$row_item = $this->GetItem($item_id);

			if ($row_item && $row_item['shop_items_catalog_is_active'] == 1)
			{
				// Проверяем наличие данных о пользователе
				if ($site_users_id)
				{
					// Вставляем данные в таблицу корзины
					$oShop_Cart = Core_Entity::factory('Shop_Cart');

					$oShop_Cart->postpone = $postpone;
					$oShop_Cart->quantity = $quantity;
					$oShop_Cart->shop_item_id = $item_id;
					$oShop_Cart->shop_id = $shop_id;
					$oShop_Cart->shop_warehouse_id = $warehouse_id;
					$oShop_Cart->siteuser_id = $site_users_id;

					$oShop_Cart->save();

					return $oShop_Cart->id;
				}
				else
				{
					$CART = $this->GetCart();

					// Формируем новый массив с товарами
					$CART[$shop_id] = Core_Type_Conversion::toArray($CART[$shop_id]);

					$CART[$shop_id][$item_id] = Core_Type_Conversion::toArray($CART[$shop_id][$item_id]);
					$CART[$shop_id][$item_id]['shop_items_catalog_item_id'] = $item_id;
					$CART[$shop_id][$item_id]['shop_warehouse_id'] = $warehouse_id;
					$CART[$shop_id][$item_id]['shop_cart_flag_postpone'] = $postpone;
					$CART[$shop_id][$item_id]['shop_cart_item_quantity'] = $quantity;

					$this->SetCart($CART);

					// Возвращаем идентификатор элемента
					return $item_id;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Обновление данных корзины
	 *
	 * @param array $param ассоциативный массив параметров
	 * - $param['user_id'] int идентификатор пользователя (если передан 0 или ложь - работаем с кукисами)
	 * - $param['shop_id'] int идентификатор магазина
	 * - $param['item_id'] int идентификатор товара
	 * - $param['cart_id'] int идентификатор корзины
	 * - $param['count'] int количество товара в корзине
	 * - $param['warehouse_id'] int идентификатор склада
	 * - $param['postpone'] int отложить ли товар для следующей покупки
	 * - $param['get_info_isset_shop'] boolean флаг необходимости проверки наличия магазина
	 * @return mixed идентификатор отредактрованной записи или ложь в случае возникновения ошибки
	 */
	function UpdateCart($param)
	{
		$site_users_id = Core_Type_Conversion::toInt($param['user_id']);
		$shop_id = Core_Type_Conversion::toInt($param['shop_id']);
		$item_id = Core_Type_Conversion::toInt($param['item_id']);
		$cart_id = Core_Type_Conversion::toInt($param['cart_id']);
		$warehouse_id = Core_Type_Conversion::toInt($param['warehouse_id']);
		$postpone = Core_Type_Conversion::toInt($param['postpone']);

		if (isset($param['count']))
		{
			$quantity = $this->ConvertPrice($param['count']);

			// При попытке вставить отрицательное значение - устанавливаем в 1
			if ($quantity < 0)
			{
				$quantity = 1;
			}
		}
		else
		{
			$quantity = 1;
		}

		// Флаг необходимости проверки магазина
		if (isset($param['get_info_isset_shop']) && $param['get_info_isset_shop'] == FALSE)
		{
			$param['get_info_isset_shop'] = FALSE;
		}
		else
		{
			$param['get_info_isset_shop'] = TRUE;
		}

		// Проверяем наличие данного товара в базе
		$row_item = $this->GetItem($item_id);

		// Нужно получить реальное количество товара, если товар электронный
		if ($row_item && $row_item['shop_items_catalog_type'] == 1)
		{
			// Получаем количество электронного товара на складе
			$current_count_item = $this->GetEitemCount($item_id);

			if ($current_count_item < $quantity && $current_count_item != -1)
			{
				$quantity = $current_count_item;
			}
		}
		// Если делимый товар
		elseif ($row_item && $row_item['shop_items_catalog_type'] == 2)
		{
			// Товар делимый, поэтому Core_Type_Conversion::toFloat()
			$quantity = Core_Type_Conversion::toFloat($quantity);
		}
		else
		{
			// Товар обычный, поэтому Core_Type_Conversion::toInt()
			$quantity = Core_Type_Conversion::toInt($quantity);
		}

		// Если количество товара равно 0
		if ($quantity == 0)
		{
			// Удаляем информацию о данном товаре из базы
			$this->DeleteCart(array(
			'shop_id' => $shop_id,
			'user_id' => $site_users_id,
			'item_id' => $item_id
			));

			return TRUE;
		}

		// Проверяем наличие данных о пользователе
		if ($site_users_id)
		{
			// Пользователь передан - обновляем данные в таблице корзины, после проверки существования интернет магазина (если это необходимо) и товара
			if ($param['get_info_isset_shop'] == TRUE)
			{
				$row_shop = $this->GetShop($shop_id);
			}

			if ((isset($row_shop) && $row_shop != FALSE) || $param['get_info_isset_shop'] == FALSE)
			{
				// Товар существует - обновляем данные
				if ($row_item)
				{
					$oShop_Cart = Core_Entity::factory('Shop_Cart', $cart_id);

					$oShop_Cart->postpone = $postpone;
					$oShop_Cart->quantity = $quantity;
					$oShop_Cart->shop_item_id = $item_id;
					$oShop_Cart->shop_id = $shop_id;
					$oShop_Cart->shop_warehouse_id = $warehouse_id;
					$oShop_Cart->siteuser_id = $site_users_id;

					$oShop_Cart->save();

					return $oShop_Cart->id;
				}
				else
				{
					// Удаляем информацию о данном товаре из базы
					$this->DeleteCart(array(
					'shop_id' => $shop_id,
					'user_id' => $site_users_id,
					'item_id' => $item_id
					));

					return TRUE;
				}
			}
			// Такого магазина не существует
			return FALSE;
		}
		// Данных о пользователе нет, работаем с кукисами - вызываем метод вставки
		else
		{
			$param['count'] = $quantity;
			return $this->InsertToCart($param);
		}
	}

	/**
	 * Получение информации о товаре из корзины для пользователя
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param int $site_users_id идентификатор пользователя
	 * @param int $item_id идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $site_users_id = 19;
	 * $item_id = 24;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $row = $shop->GetItemFromCart($shop_id, $site_users_id, $item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array or false
	 */
	function GetItemFromCart($shop_id, $site_users_id, $item_id)
	{
		$shop_id = Core_Type_Conversion::toInt($shop_id);
		$site_users_id = intval($site_users_id);
		$item_id = Core_Type_Conversion::toInt($item_id);

		if ($site_users_id)
		{
			$oShop_Cart = Core_Entity::factory('Shop', $shop_id)->Shop_Carts->getByShopItemIdAndSiteuserId($item_id, $site_users_id);

			return !is_null($oShop_Cart)
				? $this->getArrayShopCart($oShop_Cart)
				: FALSE;
		}
		$CART = $this->GetCart();

		return isset($CART[$shop_id][$item_id])
			? Core_Type_Conversion::toArray($CART[$shop_id][$item_id])
			: FALSE;
	}

	/**
	 * Добавление товара в корзину
	 *
	 * @param array $param ассоциативный массив параметров
	 * - $param['shop_id'] int идентификатор магазина
	 * - $param['item_id'] int идентификатор товара
	 * - $param['user_id'] int идентификатор пользователя
	 * - $param['count'] int количество товара
	 * - $param['warehouse_id'] int идентификатор склада
	 * - $param['postpone'] int флаг, указывающия на то, является ли данный товар отложенным
	 * @return mixed идентификатор записи таблицы корзины, id товара, если пользователь не указан, ложь - в случае возникновения ошибки
	 */
	function AddIntoCart($param)
	{
		$param['shop_id'] = Core_Type_Conversion::toInt($param['shop_id']);
		$param['item_id'] = Core_Type_Conversion::toInt($param['item_id']);
		$param['user_id'] = Core_Type_Conversion::toInt($param['user_id']);
		$param['postpone'] = Core_Type_Conversion::toInt($param['postpone']);
		$param['warehouse_id'] = Core_Type_Conversion::toInt($param['warehouse_id']);

		// Флаг необходимости проверки магазина
		if (isset($param['get_info_isset_shop']) and $param['get_info_isset_shop'] == FALSE)
		{
			$param['get_info_isset_shop'] = FALSE;
		}
		else
		{
			$param['get_info_isset_shop'] = TRUE;
		}

		$item_row = $this->GetItem($param['item_id'], array('cache_off' => TRUE));

		if ($item_row)
		{
			// Проверяем право пользователя добавить этот товар в корзину
			$iItemAccess = $this->GetShopItemAccess($param['item_id']);

			if (class_exists('SiteUsers'))
			{
				$SiteUsers = & singleton('SiteUsers');
				$site_user_id = $SiteUsers->GetCurrentSiteUser();

				$aSiteUserGroup = $SiteUsers->GetGroupsForUser($site_user_id);
			}
			else
			{
				$aSiteUserGroup = array(0);
			}

			// Если пользователь входит в группу,которой принадлежит товар
			if (in_array($iItemAccess, $aSiteUserGroup))
			{
				// Проверяем есть ли данные о товаре в корзине пользователя для данного магазина
				$row_cart = $this->GetItemFromCart($param['shop_id'], $param['user_id'], $param['item_id']);

				// Если передано количество товара и товар обычный или электронный
				if ((isset($param['count']) && $param['count'] != 0)
				&& ($item_row['shop_items_catalog_type'] == 1
				|| $item_row['shop_items_catalog_type'] == 0))
				{
					// Нужно получить реальное количество товара, если товар электронный
					if ($item_row['shop_items_catalog_type'] == 1)
					{
						// Если не передано количество товара, ставим его в единицу
						if (!isset($param['count']))
						{
							$param['count'] = 1;
						}

						// Получаем количество электронного товара на складе
						$current_count_item = $this->GetEitemCount($param['item_id']);

						if ($current_count_item < ($row_cart['shop_cart_item_quantity'] + $param['count']) && $current_count_item != -1)
						{
							$param['count'] = $current_count_item;
						}
					}

					// Товар обычный, поэтому Core_Type_Conversion::toInt()
					$param['count'] = Core_Type_Conversion::toInt($param['count']);
				}
				// Если делимый товар
				elseif (isset($param['count']) && ($param['count'] != 0)
				&& $item_row['shop_items_catalog_type'] == 2)
				{
					// Товар делимый, поэтому Core_Type_Conversion::toFloat()
					$param['count'] = Core_Type_Conversion::toFloat($param['count']);
				}
				else
				{
					// Количество не передано, по умолчанию ставим его = 1
					$param['count'] = 1;
				}

				if ($row_cart)
				{
					// Идентификатор записи
					$param['cart_id'] = Core_Type_Conversion::toInt($row_cart['shop_cart_id']);

					// Общее кол-во товара
					$param['count'] = $param['count'] + Core_Type_Conversion::toFloat($row_cart['shop_cart_item_quantity']);
				}

				// Если товар уже есть в корзине (какой из двух - не важно)
				if (isset($row_cart['shop_items_catalog_item_id'])
				&& $row_cart['shop_items_catalog_item_id'] != 0)
				{
					return $this->UpdateCart($param);
				}
				else
				{
					// Если количество товара 0 - не кладем товар
					if ($param['count'] > 0)
					{
						return $this->InsertToCart($param);
					}
				}
			}
		}

		return FALSE;
	}

	/**
	 * Получение количества оставшегося электронного товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор электронного товара
	 * @return int количество
	 */
	function GetEitemCount($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		/*$eitems_res = $this->GetEitemsForItem($shop_items_catalog_item_id);
		$sum = 0;
		while ($eitems_row = mysql_fetch_assoc($eitems_res))
		{
			// Если хотя бы у одного электронного товара количество равно -1 (бесконечность), то считаем что весь товар неограничен
			if ($eitems_row['shop_eitem_count'] == -1)
			{
				$sum = -1;

				break;
			}

			$sum += $eitems_row['shop_eitem_count'];
		}*/

		return Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Item_Digitals->getCountDigitalItems();
	}

	/**
	 * Устаревший метод. См. TransferFromCartIntoTable()
	 *
	 * @param int $site_users_id идентификатор пользователя сайта
	 * @return boolean
	 * @see TransferFromCartIntoTable
	 */
	function GetItemsFromCookiesToCart($site_users_id)
	{
		return $this->TransferFromCartIntoTable($site_users_id);
	}

	/**
	 * Перемещение информации о заказанном товаре в таблицу корзины
	 *
	 * @param int $site_users_id идентификатор пользователя сайта
	 * @return boolean
	 */
	function TransferFromCartIntoTable($site_users_id)
	{
		$param = array();

		$CART = $this->GetCart();

		// Корзина пустая - в результат пишем ложь
		$res = FALSE;

		// Массив не пустой - обрабатываем данные
		if (is_array($CART) && count($CART) > 0)
		{
			// Информацию о пользователе - в массив
			$param['user_id'] = Core_Type_Conversion::toInt($site_users_id);

			// В цикле разбираем массив с данными корзины
			foreach ($CART as $shop_id => $result)
			{
				// Проверяем наличие данного магазина в базе
				$row_shop = $this->GetShop($shop_id);

				// Магазин существует - перемещаем товары для данного магазина в таблицу корзины
				if ($row_shop)
				{
					// Информацию о магазине - в массив
					$param['shop_id'] = Core_Type_Conversion::toInt($shop_id);

					$result = Core_Type_Conversion::toArray($result);

					foreach ($result as $row)
					{
						$param['item_id'] = Core_Type_Conversion::toInt($row['shop_items_catalog_item_id']);
						$param['postpone'] = Core_Type_Conversion::toInt($row['shop_cart_flag_postpone']);
						$param['count'] = Core_Type_Conversion::toFloat($row['shop_cart_item_quantity']);
						$param['warehouse_id'] = Core_Type_Conversion::toInt($row['shop_warehouse_id']);

						// Флаг указывающий на необходимость проверять наличие магазина ставим в ложь, т.к. наличие магазина - уже проверили
						$param['get_info_isset_shop'] = FALSE;

						// Вызываем метод вставки данных в корзину
						$this->AddIntoCart($param);
					}
					$res = TRUE;
				}
			}
		}

		if ($this->CartType == 0 && isset($_COOKIE['CART']))
		{
			unset($_COOKIE['CART']);
		}
		elseif (isset($_SESSION['SCART']))
		{
			unset($_SESSION['SCART']);
		}

		// Возвращаем результат
		return $res;
	}

	/**
	 * Определение идентификатора группы товаров и идентификатора товара по значению URL
	 *
	 * @param int $shop_id - идентификатор магазина, к которому принадлежит данный товар
	 * @param array $param1 - массив, содержащий все элементы URL
	 * @param bool $break_if_path_not_found прерывает поиск пути, если очередной элемент не был найден, по умолчанию true
	 * @param array $param массив дополнительных параметров
	 * - array $param['shop_items_groups_activity'] массив параметров активности группы товаров, по умолчанию только активные
	 * - array $param['shop_items_activity'] массив параметров активности товара, по умолчанию только активные

	 * @return array or boolean - возвращает ассоциативный массив, содержащий идентификатор группы товаров и идентификатор товара
	 */
	function GetItemPath($shop_id, $param1 = '', $break_if_path_not_found = TRUE, $param = array())
	{
		$shop_id = intval($shop_id);
		$aShopItemsGroupsActivity = Core_Type_Conversion::toArray($param['shop_items_groups_activity']);
		$aShopItemsActivity = Core_Type_Conversion::toArray($param['shop_items_activity']);

		// если массив был передан
		if (is_array($param1))
		{
			// перезаписываем массив param на переданный
			$param = $param1;
		}
		else
		{
			$param = $GLOBALS['URL_ARRAY'];
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_field_name = $shop_id . '_' . implode('_', $param);
			$cache_name = 'SHOP_ITEM_PATH';

			if ($in_cache = $cache->GetCacheContent($cache_field_name, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$row = $this->GetShop($shop_id);

		// магазин с таким id существует
		if ($row !== FALSE)
		{
			// Получаем путь к магазину
			$Structure = & singleton('Structure');
			$shop_path = $Structure->GetStructurePath($row['structure_id'], 0);

			if ($shop_path != '/')
			{
				$shop_path = '/' . $shop_path;
			}

			// проверяем правильно ли задан url магазина
			$mas_url = explode('/', trim($shop_path));
			$count_mas_url = count($mas_url);

			$count_param = count($param);

			// корень
			$shop_groups_parent_id = 0;

			// Возвращаемый массив по умолчанию
			$return['group'] = $shop_groups_parent_id;
			$return['item'] = FALSE;

			// Выясняем точку отсечения
			if ($count_mas_url < 3)
			{
				if ($param[0] == '/')
				{
					$count_mas_url = $count_mas_url -1; // -1, т.к. при пути на главную, получается / и 2 элемента.
				}
				else
				{
					$count_mas_url = $count_mas_url -2;
				}
			}
			else // url магазина задан правильно
			{
				$count_mas_url = $count_mas_url -2;
			}

			// Получаем значения активности групп товаров для выборки
			if (count($aShopItemsGroupsActivity) == 0)
			{
				$aShopItemsGroupsActivity = array(1);
			}

			foreach ($aShopItemsGroupsActivity as $value)
			{
				$aShopItemsGroupsActivitySql[] = intval($value);
			}

			// Получаем значения активности товаров для выборки
			if (count($aShopItemsActivity) == 0)
			{
				$aShopItemsActivity = array(1);
			}

			foreach ($aShopItemsActivity as $value)
			{
				$aShopItemsActivitySql[] = intval($value);
			}

			// Проходимся по $param с целью определения ID группы и элемента
			for ($i = $count_mas_url; $i < $count_param; $i++)
			{
				// Для того, чтобы выводилось, если нет последнего слэша (т.к. в $count_param может быть на один элемент больше
				if (empty ($param[$i]))
				{
					continue;
				}

				if ($param[$i] == 'tag' && class_exists('Tag'))
				{
					$oTag = & singleton ('Tag');
					$tag_name = Core_Type_Conversion::toStr($param[$i + 1]);

					if ($oTag->GetTagByPath($tag_name))
					{
						// Сохраним тег
						$return['tag_name'] = $tag_name;

						// Это вывод тегов - прерываем и сохраним в результате сам тег
						break;
					}
					else
					{
						// Тег отсутствует - возвращаем ложь
						$return = false;
						break;
					}
				}

				if (class_exists('SiteUsers'))
				{
					$SiteUsers = & singleton('SiteUsers');
					$site_user_id = $SiteUsers->GetCurrentSiteUser();
				}
				else
				{
					$site_user_id = 0;
				}

				// Получаем массив групп пользователий, в которых содержится текущий пользователь сайта
				$mas_result = $this->GetSiteUsersGroupsForUser($site_user_id);

				$queryBuilder = Core_QueryBuilder::select(
						array('id', 'shop_groups_id'),
						array('shop_id', 'shop_shops_id'),
						array('parent_id', 'shop_groups_parent_id'),
						array('name', 'shop_groups_name'),
						array('description', 'shop_groups_description'),
						array('image_large', 'shop_groups_image'),
						array('image_small', 'shop_groups_small_image'),
						array('sorting', 'shop_groups_order'),
						array('indexing', 'shop_groups_indexation'),
						array('active', 'shop_groups_activity'),
						array('siteuser_group_id', 'shop_groups_access'),
						array('path', 'shop_groups_path'),
						array('seo_title', 'shop_groups_seo_title'),
						array('seo_description', 'shop_groups_seo_description'),
						array('seo_keywords', 'shop_groups_seo_keywords'),
						array('user_id', 'users_id'),
						array('image_large_width', 'shop_groups_big_image_width'),
						array('image_large_height', 'shop_groups_big_image_height'),
						array('image_small_width', 'shop_groups_small_image_width'),
						array('image_small_height', 'shop_groups_small_image_height'),
						array('guid', 'shop_groups_cml_id'),
						array('items_count', 'count_items'),
						array('items_total_count', 'count_all_items'),
						array('subgroups_count', 'count_groups'),
						array('subgroups_total_count', 'count_all_groups')
					)
					->from('shop_groups')
					->where('parent_id', '=', $shop_groups_parent_id)
					->where('active', 'IN', $aShopItemsGroupsActivitySql)
					->where('path', '=', $param[$i])
					->where('shop_id', '=', $row['shop_shops_id'])
					->where('siteuser_group_id', 'IN', $mas_result)
					->where('deleted', '=', 0);

				$aGroupResult = $queryBuilder->execute()->asAssoc()->current();

				if ($aGroupResult)
                {
                	$param_access_group = array();
                	$param_access_group['site_users_id'] = $site_user_id;
                	$param_access_group['shop_group_id'] = $aGroupResult['shop_groups_id'];
                	$param_access_group['shop_id'] = $shop_id;
                	$param_access_group['shop_group_info'] = $aGroupResult;

                	if ($this->IssetAccessForShopGroup($param_access_group))
                	{
                		// Сохраняем ID группы
                		$shop_groups_parent_id = $aGroupResult['shop_groups_id'];
                		$return['group'] = $shop_groups_parent_id;
                	}
                	else
                	{
                		$return = FALSE;
                	}
                }
                else // Не существует  в данной родительской группе группы с таким названием
                {
					$queryBuilder = Core_QueryBuilder::select(
						array('id', 'shop_items_catalog_item_id'),
						array('shortcut_id', 'shop_items_catalog_shortcut_id'),
						array('shop_tax_id', 'shop_tax_id'),
						array('shop_seller_id', 'shop_sallers_id'),
						array('shop_group_id', 'shop_groups_id'),
						'shop_currency_id',
						array('shop_id', 'shop_shops_id'),
						array('shop_producer_id', 'shop_producers_list_id'),
						array('shop_measure_id', 'shop_mesures_id'),
						array('type', 'shop_items_catalog_type'),
						array('name', 'shop_items_catalog_name'),
						array('marking', 'shop_items_catalog_marking'),
						array('vendorcode', 'shop_vendorcode'),
						array('description', 'shop_items_catalog_description'),
						array('text', 'shop_items_catalog_text'),
						array('image_large', 'shop_items_catalog_image'),
						array('image_small', 'shop_items_catalog_small_image'),
						array('weight', 'shop_items_catalog_weight'),
						array('price', 'shop_items_catalog_price'),
						array('active', 'shop_items_catalog_is_active'),
						array('siteuser_group_id', 'shop_items_catalog_access'),
						array('sorting', 'shop_items_catalog_order'),
						array('path', 'shop_items_catalog_path'),
						array('seo_title', 'shop_items_catalog_seo_title'),
						array('seo_description', 'shop_items_catalog_seo_description'),
						array('seo_description', 'shop_items_catalog_seo_keywords'),
						array('indexing', 'shop_items_catalog_indexation'),
						array('image_small_height', 'shop_items_catalog_small_image_height'),
						array('image_small_width', 'shop_items_catalog_small_image_width'),
						array('image_large_height', 'shop_items_catalog_big_image_height'),
						array('image_large_width', 'shop_items_catalog_big_image_width'),
						array('yandex_market', 'shop_items_catalog_yandex_market_allow'),
						array('yandex_market_bid', 'shop_items_catalog_yandex_market_bid'),
						array('yandex_market_cid', 'shop_items_catalog_yandex_market_cid'),
						array('yandex_market_sales_notes', 'shop_items_catalog_yandex_market_sales_notes'),
						array('siteuser_id', 'site_users_id'),
						array('datetime', 'shop_items_catalog_date_time'),
						array('modification_id', 'shop_items_catalog_modification_id'),
						array('guid', 'shop_items_cml_id'),
						array('start_datetime', 'shop_items_catalog_putoff_date'),
						array('end_datetime', 'shop_items_catalog_putend_date'),
						array('showed', 'shop_items_catalog_show_count'),
						array('user_id', 'users_id')
					)
					->from('shop_items')
					->where('shop_group_id', '=', $shop_groups_parent_id)
					->where('shop_id', '=', $row['shop_shops_id'])
					->where('active', 'IN', $aShopItemsActivitySql)
					->where('modification_id', '=', 0)
					->open()
					->where('id', '=', Core_Type_Conversion::toInt($param[$i]))
					->where('path', '=', '')
					->setOr()
					->where('path', '=', $param[$i])
					->close()
					->where('siteuser_group_id', 'IN', $mas_result)
					->where('deleted', '=', 0);

					$aItemResult = $queryBuilder->execute()->asAssoc()->current();

				   // элемент массива param является товаром
				   if($aItemResult)
				   {
						if ($this->GetAccessShopItem($site_user_id, $aItemResult['shop_items_catalog_item_id'], $aItemResult))
						{
							// ДАННЫЙ ЭЛЕМЕНТ URL - ЭТО ТОВАР
							$return['group'] = $aItemResult['shop_groups_id'];

							// Метод возвращает ID или имя в URL, если оно задано.
							$return['item'] = $aItemResult['shop_items_catalog_item_id'];
							// Выходим из цикла
							//break;
						}
						else
						{
							$return = FALSE;
						}
						// Продолжаем обработку, чтобы проверить на 404 ошибку
					}
					else
					{
						// Модификация
						if ($return['item'])
						{
							$queryBuilder = Core_QueryBuilder::select(
								array('id', 'shop_items_catalog_item_id'),
								array('shortcut_id', 'shop_items_catalog_shortcut_id'),
								array('shop_tax_id', 'shop_tax_id'),
								array('shop_seller_id', 'shop_sallers_id'),
								array('shop_group_id', 'shop_groups_id'),
								'shop_currency_id',
								array('shop_id', 'shop_shops_id'),
								array('shop_producer_id', 'shop_producers_list_id'),
								array('shop_measure_id', 'shop_mesures_id'),
								array('type', 'shop_items_catalog_type'),
								array('name', 'shop_items_catalog_name'),
								array('marking', 'shop_items_catalog_marking'),
								array('vendorcode', 'shop_vendorcode'),
								array('description', 'shop_items_catalog_description'),
								array('text', 'shop_items_catalog_text'),
								array('image_large', 'shop_items_catalog_image'),
								array('image_small', 'shop_items_catalog_small_image'),
								array('weight', 'shop_items_catalog_weight'),
								array('price', 'shop_items_catalog_price'),
								array('active', 'shop_items_catalog_is_active'),
								array('siteuser_group_id', 'shop_items_catalog_access'),
								array('sorting', 'shop_items_catalog_order'),
								array('path', 'shop_items_catalog_path'),
								array('seo_title', 'shop_items_catalog_seo_title'),
								array('seo_description', 'shop_items_catalog_seo_description'),
								array('seo_description', 'shop_items_catalog_seo_keywords'),
								array('indexing', 'shop_items_catalog_indexation'),
								array('image_small_height', 'shop_items_catalog_small_image_height'),
								array('image_small_width', 'shop_items_catalog_small_image_width'),
								array('image_large_height', 'shop_items_catalog_big_image_height'),
								array('image_large_width', 'shop_items_catalog_big_image_width'),
								array('yandex_market', 'shop_items_catalog_yandex_market_allow'),
								array('yandex_market_bid', 'shop_items_catalog_yandex_market_bid'),
								array('yandex_market_cid', 'shop_items_catalog_yandex_market_cid'),
								array('yandex_market_sales_notes', 'shop_items_catalog_yandex_market_sales_notes'),
								array('siteuser_id', 'site_users_id'),
								array('datetime', 'shop_items_catalog_date_time'),
								array('modification_id', 'shop_items_catalog_modification_id'),
								array('guid', 'shop_items_cml_id'),
								array('start_datetime', 'shop_items_catalog_putoff_date'),
								array('end_datetime', 'shop_items_catalog_putend_date'),
								array('showed', 'shop_items_catalog_show_count'),
								array('user_id', 'users_id')
							)
							->from('shop_items')

							->where('shop_group_id', '=', 0)
							->where('shop_id', '=', $row['shop_shops_id'])
							->where('active', 'IN', $aShopItemsActivitySql)
							->where('modification_id', '=', $return['item'])
							->open()
							->where('id', '=', Core_Type_Conversion::toInt($param[$i]))
							->where('path', '=', '')
							->setOr()
							->where('path', '=', $param[$i])
							->close()
							->where('siteuser_group_id', 'IN', $mas_result)
							->where('deleted', '=', 0);

							$aItemResult = $queryBuilder->execute()->asAssoc()->result();

							// элемент массива param является товаром
							if (count($aItemResult) == 1)
							{
								$row = $aItemResult[0];

								if ($this->GetAccessShopItem($site_user_id, $row['shop_items_catalog_item_id'], $row))
								{
									// ДАННЫЙ ЭЛЕМЕНТ URL - ЭТО ТОВАР
									//$return['group'] = $row['shop_groups_id'];

									// Метод возвращает ID или имя в URL, если оно задано.
									$return['item'] = $row['shop_items_catalog_item_id'];
									// Выходим из цикла
									//break;
								}
								else
								{
									$return = FALSE;
								}

								// Это модификация - прерываем
								break;
							}
						}

						// Тэг
						if ($param[$i] == 'tag')
						{
							// Сохраним тег
							$return['tag_name'] = Core_Type_Conversion::toStr($param[$i +1]);

							// Это вывод тегов - прерываем и сохраним в результате сам тег
							break;
						}

						// Это и не элемент, и не группа, возвращаем ID предыдущей группы
						if (preg_match("/^page-([0-9]*)$/", $param[$i], $regs) && Core_Type_Conversion::toInt($regs[1]) > 0 || !$break_if_path_not_found)
						{
							// Это переход на страницу, к следующему элементу
							continue;
						}

						// Это не элемент и не группа, возвращаем false, чтобы вывести 404
						$return = FALSE;

						// Это и не элемент, и не группа, возвращаем ID предыдущей группы
						break;
					}
				}
			}
			//}
		}
		else
		{
			$return = FALSE;
		}

		// Запись в файловый кэш
		if (class_exists('Cache'))
		{
			$cache->Insert($cache_field_name, $return, $cache_name);
		}

		return $return;
	}

	/**
	 * Внутренний метод построения XML для товара, его свойств и сопутствующих товаров
	 *
	 * @param int $root параметр, определяющий, является ли данный товар сопутствующим (0-основной, 1 - сопутствующий или модификация)
	 * @param array $row информация о товаре
	 * @param int $site_users_id идентификатор пользователя
	 * @param array $param дополнительные параметры
	 * - $param['xml_show_tying_products'] разрешает указание в XML сопутствующих товаров, по умолчанию true
	 * - $param['xml_show_modification'] разрешает указание в XML модификаций товаров, по умолчанию true
	 * - $param['xml_show_comments'] разрешает добавление в XML отзывов о товаре, по умолчанию true
	 * @return string XML для свойств товара
	 */
	function GenXml4Item($root, $row, $site_users_id = 0, $param = array())
	{
		$row = Core_Type_Conversion::toArray($row);
		$root = intval($root);
		$param = Core_Type_Conversion::toArray($param);

		// Идентификатор пользователя для вычисления цены
		$site_users_id = intval($site_users_id);

		!isset($param['xml_show_tying_products']) && $param['xml_show_tying_products'] = TRUE;
		!isset($param['xml_show_modification']) && $param['xml_show_modification'] = TRUE;
		!isset($param['xml_show_comments']) && $param['xml_show_comments'] = TRUE;
		!isset($param['xml_show_item_property']) && $param['xml_show_item_property'] = TRUE;
		!isset($param['xml_show_producers']) && $param['xml_show_producers'] = TRUE;

		$param['FillMemCacheTyingProducts'] = Core_Type_Conversion::toBool($param['FillMemCacheTyingProducts']);
		$param['FillMemCacheComments'] = Core_Type_Conversion::toBool($param['FillMemCacheComments']);
		$param['FillMemCachePropertiesItem'] = Core_Type_Conversion::toBool($param['FillMemCachePropertiesItem']);
		$param['FillMemCacheModificationItems'] = Core_Type_Conversion::toBool($param['FillMemCacheModificationItems']);
		$param['FillMemCacheSpecialPricesForItem'] = Core_Type_Conversion::toBool($param['FillMemCacheSpecialPricesForItem']);
		$param['FillMemCacheGetAllPricesForItem'] = Core_Type_Conversion::toBool($param['FillMemCacheGetAllPricesForItem']);
		$param['FillMemCachePriceForItem'] = Core_Type_Conversion::toBool($param['FillMemCachePriceForItem']);

		$param['FillMemCacheDiscountsForItem'] = !isset($param['FillMemCacheDiscountsForItem'])
			? TRUE
			: Core_Type_Conversion::toBool($param['FillMemCacheDiscountsForItem']);

		// Получаем информацию о магазине
		$shop_row = $this->GetShop($row['shop_shops_id']);

		if (!$shop_row)
		{
			return FALSE;
		}

		// ADD 05.03.2009
		// Проверяем, совпадают ли размеры изображений с максимально возможными для магазина. Если совпадают - обновляем информацию об изображениях, записывая в базу правильные размеры.
		// Убрано 02.03.2010
		/*
		if (($shop_row['shop_image_small_max_width'] == $row['shop_items_catalog_small_image_width'] && $shop_row['shop_image_small_max_height'] == $row['shop_items_catalog_small_image_height']) ||
		($shop_row['shop_image_big_max_width'] == $row['shop_items_catalog_big_image_width'] && $shop_row['shop_image_big_max_height'] == $row['shop_items_catalog_big_image_height']))
		{
			// Обновляем информацию об изображении
			$this->UpdateImageForItem($row['shop_shops_id'], $row['shop_items_catalog_item_id'], $row['shop_items_catalog_image'], $row['shop_items_catalog_small_image']);

			// Получаем информацию о товаре (чтобы получить новые размеры изображений)
			$row = $this->GetItem($row['shop_items_catalog_item_id']);
		}
		*/
		// END ADD 05.03.2009

		$xmlData = '';
		$shop_items_catalog_item_id = intval($row['shop_items_catalog_item_id']);

		// Определяем из входных параметров идентификатор товара и группу, к которойон относится
		$xmlData .= '<item id="' . $shop_items_catalog_item_id . '" group="'.intval($row['shop_groups_id']).'">'."\n";

		/* Генерируем идентификатор CAPTCHA*/
		$Captcha = & singleton('Captcha');
		$xmlData .= '<captcha_key>'.$Captcha->GetCaptchaID().'</captcha_key>'."\n";

		/* Определяем имя товара*/
		$xmlData .= '<name>'.str_for_xml($row['shop_items_catalog_name']).'</name>'."\n";
		$xmlData .= '<show_count>'.str_for_xml($row['shop_items_catalog_show_count']).'</show_count>'."\n";
		$xmlData .= '<saller>'.$row['shop_sallers_id'].'</saller>'."\n";
		$xmlData .= '<marking_of_goods>'.str_for_xml($row['shop_items_catalog_marking']).'</marking_of_goods>'."\n";
		$xmlData .= '<description>'.str_for_xml($row['shop_items_catalog_description']).'</description>'."\n";

		if ($row['shop_items_catalog_date_time'] != '0000-00-00 00:00:00')
		{
			$shop_items_catalog_date = strftime(Core_Type_Conversion::toStr($shop_row['shop_format_date']), Core_Date::sql2timestamp($row['shop_items_catalog_date_time']));
			$shop_items_catalog_date_time = strftime(Core_Type_Conversion::toStr($shop_row['shop_format_datetime']), Core_Date::sql2timestamp($row['shop_items_catalog_date_time']));
		}
		else
		{
			$shop_items_catalog_date = '00.00.0000';
			$shop_items_catalog_date_time = '00.00.0000 00:00:00';
		}

		$xmlData .= '<date>' . str_for_xml($shop_items_catalog_date) . '</date>' . "\n";
		$xmlData .= '<datetime>' . str_for_xml($shop_items_catalog_date_time) . '</datetime>' . "\n";

		$date = explode(' ', $row['shop_items_catalog_date_time']);

		// Добавляем в XML время
		if (isset($date[1]))
		{
			$xmlData .= '<time>' . $date[1] . '</time>' . "\n";
		}

		// Дата
		if ($row['shop_items_catalog_putoff_date'] != '0000-00-00 00:00:00')
		{
			$putoff_date = strftime(Core_Type_Conversion::toStr($shop_row['shop_format_datetime']), Core_Date::sql2timestamp($row['shop_items_catalog_putoff_date']));
		}
		else
		{
			$putoff_date = '00.00.0000 00:00:00';
		}

		$xmlData .= '<putoff_date>' . str_for_xml($putoff_date) . '</putoff_date>' . "\n";

		// Дата
		if ($row['shop_items_catalog_putend_date'] != '0000-00-00 00:00:00')
		{
			$putend_date = strftime(Core_Type_Conversion::toStr($shop_row['shop_format_datetime']), Core_Date::sql2timestamp($row['shop_items_catalog_putend_date']));
		}
		else
		{
			$putend_date = '00.00.0000 00:00:00';
		}

		$xmlData .= '<putend_date>' . str_for_xml($putend_date) . '</putend_date>' . "\n";

		// проверяем включать в XML текст элемента или нет
		if (!(isset($param['show_text']) && !$param['show_text']))
		{
			// Определяем детальное описание товара
			$xmlData .= '<text>' . str_for_xml($row['shop_items_catalog_text']) . '</text>' . "\n";
		}

		if ($param['xml_show_producers'])
		{
			// Выбираем имя производителя по его идентификатору
			$select = $this->GetProducer($row['shop_producers_list_id']);
			if ($select)
			{
				// Строим для производителя XML
				$xmlData .= $this->GetXmlProducer($select);
			}
		}

		$image = & singleton('Image');

		// Извлекаем путь для хранения картинки
		$uploaddir = '/' . $this->GetItemDir($row['shop_items_catalog_item_id']);

		if ($uploaddir)
		{
			$queryBuilder = Core_QueryBuilder::update('shop_items');

			$bQueryBuilderSet = FALSE;

			// Информацию об изображениях в xml
			if (!empty ($row['shop_items_catalog_image']))
			{
				$big_image_path = CMS_FOLDER . $uploaddir . $row['shop_items_catalog_image'];

				// Для загруженного большого изображения заданы нулевуе размеры
				if ($row['shop_items_catalog_big_image_width'] == 0 && $row['shop_items_catalog_big_image_height'] == 0
				&& is_file($big_image_path)
				&& filesize($big_image_path) > 12)
				{
					// Получаем размеры большого изображения
					if (Core_Image::instance()->exifImagetype($big_image_path))
					{
						//$big_image_sizes =  $image->GetImageSize($big_image_path);
						$big_image_sizes = Core_Image::instance()->getImageSize($big_image_path);
						$big_image_height = $big_image_sizes['height'];
						$big_image_width = $big_image_sizes['width'];

						$queryBuilder
							->set('image_large_height', $big_image_height)
							->set('image_large_width', $big_image_width);
						$bQueryBuilderSet = TRUE;
					}
					else
					{
						$big_image_height = 0;
						$big_image_width = 0;
					}
				}
				else
				{
					$big_image_height = $row['shop_items_catalog_big_image_height'];
					$big_image_width = $row['shop_items_catalog_big_image_width'];
				}

				$xmlData .= '<image width="' . $big_image_width . '" height="' . $big_image_height . '">' . $uploaddir . str_for_xml($row['shop_items_catalog_image']) . '</image>' . "\n";
			}

			if (!empty ($row['shop_items_catalog_small_image']))
			{
				$small_image_path = CMS_FOLDER . $uploaddir . $row['shop_items_catalog_small_image'];
				// Для загруженного малого изображения заданы нулевуе размеры
				if ($row['shop_items_catalog_small_image_width'] == 0 && $row['shop_items_catalog_small_image_height'] == 0
				&& is_file($small_image_path)
				&& filesize($small_image_path) > 12)
				{
					// Получаем размеры малого изображения
					if (Core_Image::instance()->exifImagetype($small_image_path))
					{
						$smal_image_sizes = $image->GetImageSize($small_image_path);
						$small_image_height = $smal_image_sizes['height'];
						$small_image_width = $smal_image_sizes['width'];

						$queryBuilder
							->set('image_small_height', $small_image_height)
							->set('image_small_width', $small_image_width);
						$bQueryBuilderSet = TRUE;
					}
					else
					{
						$small_image_height = 0;
						$small_image_width = 0;
					}
				}
				else
				{
					$small_image_height = $row['shop_items_catalog_small_image_height'];
					$small_image_width = $row['shop_items_catalog_small_image_width'];
				}

				$xmlData .= '<small_image width="' . $small_image_width . '" height="' . $small_image_height . '">' . $uploaddir . str_for_xml($row['shop_items_catalog_small_image']) . '</small_image>'."\n";
			}

			if ($bQueryBuilderSet)
			{
				$queryBuilder
					->where('id', '=', $shop_items_catalog_item_id)
					->execute();
			}
		}

		// Тип товара
		$xmlData .= '<type>' . Core_Type_Conversion::toFloat($row['shop_items_catalog_type']) . '</type>' . "\n";

		// Если электронный товар
		if ($row['shop_items_catalog_type'] == 1)
		{
			// Количество электронных товаров
			$xmlData .= '<eitem_count>' . str_for_xml($this->GetEitemCount($row['shop_items_catalog_item_id'])) . '</eitem_count>' . "\n";
		}

		// Вес товара
		$xmlData .= '<weight>' . Core_Type_Conversion::toFloat($row['shop_items_catalog_weight']) . '</weight>' . "\n";

		// Единица измерения веса товара
		$ShopMesure = $this->GetMesure($shop_row['shop_mesures_id']);

		$xmlData .= '<weight_mesure>' . str_for_xml($ShopMesure['shop_mesures_name']) . '</weight_mesure>' . "\n";

		$warehouse = & singleton('warehouse');

		// Остаток товара на складе
		$xmlData .= '<rest>' . $warehouse->GetItemCountForAllWarehouses($row['shop_items_catalog_item_id']) . '</rest>'."\n";

		// Единица измерения
		$select = $this->GetMesure($row['shop_mesures_id']);

		$xmlData .= '<mesure>' . str_for_xml($select['shop_mesures_name']) . '</mesure>' . "\n";

		$xmlData .= '<shop_tax_id>' . Core_Type_Conversion::toInt($row['shop_tax_id']) . '</shop_tax_id>' . "\n";

		// Вычисляем цену товара
		$price = $this->GetPriceForUser($site_users_id, $row['shop_items_catalog_item_id'], $row, $param);

		// Будет совпадать с ценой вместе с налогом
		$xmlData .= '<price>' . str_for_xml($price['price_tax']) . '</price>' . "\n";

		//Устанавливаем цену товара(свойство класса)
		//$this->SetClassPropertyItemCatalogPrice(Core_Type_Conversion::toFloat($price['price']));

		$xmlData .= '<price_tax>' . str_for_xml($price['price_tax']) . '</price_tax>' . "\n";
		$xmlData .= '<price_discount>' . str_for_xml($price['price_discount']) . '</price_discount>' . "\n";

		// Проверяем наличие информации о скидках в массиве цен
		if (isset($price['discount']))
		{
			$price['discount'] = Core_Type_Conversion::toArray($price['discount']);
			$count = count($price['discount']);
			for ($i = 0; $i < $count; $i++)
			{
				$xmlData .= '<discount>' . "\n";
				$xmlData .= '<name>' . str_for_xml($price['discount'][$i]['name']) . '</name>' . "\n";
				$xmlData .= '<value>' . str_for_xml(Core_Type_Conversion::toFloat($price['discount'][$i]['value'])) . '</value>' . "\n";
				$xmlData .= '</discount>' . "\n";
			}
		}

		// Информация об оригинальной валюте для товара
		$Currency = $this->GetCurrency($row['shop_currency_id']);
		$xmlData .= '<item_currency>' . str_for_xml($Currency['shop_currency_name']) . '</item_currency>' . "\n";

		// Информация о валюте для вычисленной цены товара
		$Currency = $this->GetShopCurrency($row['shop_shops_id']);
		$xmlData .= '<currency>' . str_for_xml($Currency['shop_currency_name']) . '</currency>' . "\n";

		$xmlData .= '<is_active>' . $row['shop_items_catalog_is_active'] . '</is_active>' . "\n";
		$xmlData .= '<order>' . Core_Type_Conversion::toInt($row['shop_items_catalog_order']) . '</order>' . "\n";

		$xmlData .= '<path>' . str_for_xml(rawurlencode($row['shop_items_catalog_path'])) . '</path>' . "\n";

		$xmlData .= '<fullpath>' . str_for_xml($this->GetPathItem($shop_items_catalog_item_id)) . '</fullpath>' . "\n";

		$xmlData .= '<seo_title>' . str_for_xml($row['shop_items_catalog_seo_title']) . '</seo_title>' . "\n";
		$xmlData .= '<seo_description>' . str_for_xml($row['shop_items_catalog_seo_description']) . '</seo_description>' . "\n";
		$xmlData .= '<seo_keywords>' . str_for_xml($row['shop_items_catalog_seo_keywords']) . '</seo_keywords>' . "\n";
		$xmlData .= '<indexation>' . $row['shop_items_catalog_indexation'] . '</indexation>' . "\n";

		/* vendorCode*/
		$xmlData .= '<vendorCode>' . str_for_xml($row['shop_vendorcode']) . "</vendorCode>\n";

		// CML ID
		$xmlData .= '<shop_items_cml_id>' . str_for_xml($row['shop_items_cml_id']) . "</shop_items_cml_id>\n";

		// ID пользователя.
		$xmlData .= '<user_id>' . str_for_xml($row['site_users_id']) . '</user_id>' . "\n";
		$xmlData .= '<site_users_id>' . str_for_xml($row['site_users_id']) . '</site_users_id>' . "\n";

		// Информация о пользователе сайта, добавившем товар
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');

			if ($row['site_users_id'] > 0)
			{
				// Получаем xml для пользователя.
				$xmlData .= '<site_user_info>' . "\n";
				$xmlData .= $SiteUsers->GetSiteUserXml($row['site_users_id']);
				$xmlData .= '</site_user_info>' . "\n";
			}
		}

		if ($param['xml_show_item_property'])
		{
			// Дополнительные свойства
			$prop_array = $this->GetAllPropertiesItem($row['shop_items_catalog_item_id'], $param);

			if ($prop_array)
			{
				$count_prop = count($prop_array);

				$Image = & singleton('Image');

				for ($i = 0; $i < $count_prop; $i++)
				{
					// Не указано ограничения на вывод доп св-в или явно указан массив со всписком
					if (!isset($param['xml_show_item_property_id'])
					|| count($param['xml_show_item_property_id']) > 0
					&& in_array($prop_array[$i]['shop_list_of_properties_id'], $param['xml_show_item_property_id']))
					{
						$xmlData .= '<property id="' . Core_Type_Conversion::toInt($prop_array[$i]['shop_list_of_properties_id']) . '" dir_id="' . str_for_xml($prop_array[$i]['shop_properties_items_dir_id']) . '" xml_name="' . str_for_xml($prop_array[$i]['shop_list_of_properties_xml_name']) . '">' . "\n";
						$xmlData .= '<name>' . str_for_xml($prop_array[$i]['shop_list_of_properties_name']) . '</name>' . "\n";
						$xmlData .= '<xml_name>' . str_for_xml($prop_array[$i]['shop_list_of_properties_xml_name']) . '</xml_name>' . "\n";
						$xmlData .= '<type>' . str_for_xml($prop_array[$i]['shop_list_of_properties_type']) . '</type>' . "\n";
						$xmlData .= '<order>' . str_for_xml($prop_array[$i]['shop_list_of_properties_order']) . '</order>' . "\n";

						$xmlData .= '<prefics>' . str_for_xml($prop_array[$i]['shop_list_of_properties_prefics']) . '</prefics>' . "\n";
						$xmlData .= '<value>' . str_for_xml($prop_array[$i]['shop_properties_items_value']) . '</value>' . "\n";
						//$xmlData .= '<lists_items_order>'.str_for_xml($prop_array[$i]['lists_items_order']).'</lists_items_order>'."\n";

						// Если uploaddir начинается с символа "/", создаем переменную, содержащую uploaddir, но без ведущего символа "/"
						if (substr($uploaddir, 0, 1) == '/')
						{
							$uploaddir_without_first_slash = substr($uploaddir, 1, strlen($uploaddir) - 1);
						}
						else
						{
							$uploaddir_without_first_slash = $uploaddir;
						}

						$fname_picture = $uploaddir . rawurlencode($prop_array[$i]['shop_properties_items_file']);

						// Путь к картинке
						if ($prop_array[$i]['shop_list_of_properties_type'] == 1)
						{
							$image_attributes = '';

							$tmp_image_path = CMS_FOLDER . $uploaddir_without_first_slash . $prop_array[$i]['shop_properties_items_file'];

							if (is_file($tmp_image_path)
							&& is_readable($tmp_image_path)
							&& filesize($tmp_image_path) > 12)
							{
								// Если файл - картинка
								if (Core_Image::instance()->exifImagetype($tmp_image_path))
								{
									// Определяем размеры
									$image_sizes = $Image->GetImageSize($tmp_image_path);

									// Если ошибки нет
									if ($image_sizes)
									{
										$image_attributes = ' width = "' . Core_Type_Conversion::toInt($image_sizes['width']) . '" height = "' . Core_Type_Conversion::toInt($image_sizes['height']) . '"';
									}
								}

								$xmlData .= '<file_path' . $image_attributes . '>' . str_for_xml($fname_picture) . '</file_path>' . "\n";
							}

							if ($prop_array[$i]['shop_properties_items_file_small'] != '')
							{
								$xmlData .= "<small_image>\n";

								$fname_small_picture = $uploaddir . rawurlencode($prop_array[$i]['shop_properties_items_file_small']);

								$image_attributes = '';

								$tmp_image_path = CMS_FOLDER . $uploaddir_without_first_slash . $prop_array[$i]['shop_properties_items_file_small'];

								if (is_file($tmp_image_path)
								&& is_readable($tmp_image_path)
								&& filesize($tmp_image_path) > 12)
								{
									// Если файл - картинка
									if (Core_Image::instance()->exifImagetype($tmp_image_path))
									{
										// Определяем размеры
										$image_sizes = $Image->GetImageSize($tmp_image_path);

										// Если ошибки нет
										if ($image_sizes)
										{
											$image_attributes = ' width = "' . Core_Type_Conversion::toInt($image_sizes['width']) . '" height = "' . Core_Type_Conversion::toInt($image_sizes['height']) . '"';
										}
									}

									$xmlData .= '<file_path' . $image_attributes . '>' . str_for_xml($fname_small_picture) . '</file_path>' . "\n";
								}

								$xmlData .= '<value>' . str_for_xml($prop_array[$i]['shop_properties_items_value_small']) . '</value>' . "\n";

								$xmlData .= "</small_image>\n";
							}
						}

						// Выбираем единицу измерения свойства товара из таблицы единиц измерения
						$mesure = $this->GetMesure($prop_array[$i]['shop_mesures_id']);
						if ($mesure)
						{
							$xmlData .= '<mesure>' . str_for_xml($mesure['shop_mesures_name']) . '</mesure>';
						}

						$xmlData .= '</property>' . "\n";
					}
				}
			}
		}

		if ($param['xml_show_comments'] === TRUE)
		{
			// Отзывы о товаре
			$xmlData .= $this->GenXml4ItemCatalogComments($row['shop_items_catalog_item_id'], $param);
		}

		if ($root == 0 && !$row['shop_items_catalog_modification_id'] && $param['xml_show_modification'])
		{
			// Возвращать только активные модификации товара
			$modif_param = array();
			$modif_param['shop_items_catalog_is_active'] = 1;

			// Получить список модификаций для данного товара
			$modifications_array = $this->GetAllModificationItems($row['shop_items_catalog_item_id'], $modif_param);
			$modifications_array = Core_Type_Conversion::toArray($modifications_array);

			if (count($modifications_array) > 0)
			{
				$mass_item = array();
				foreach ($modifications_array as $key => $value)
				{
					$mass_item[] = $value['shop_items_catalog_item_id'];
				}

				$this->FillMemCacheItems($mass_item);

				// Заполняем кэш специальных цен
				$param['FillMemCacheSpecialPricesForItem'] && $this->FillMemCacheSpecialPricesForItem($mass_item);

				// Заполняем кэш для скидок
				$param['FillMemCacheDiscountsForItem'] && $this->FillMemCacheDiscountsForItem($mass_item);

				// Заполняем кэш цен для групп пользователей для товара
				$param['FillMemCacheGetAllPricesForItem'] && $this->FillMemCacheGetAllPricesForItem($mass_item);

				$param['FillMemCacheModificationItems'] && $this->FillMemCacheModificationItems($mass_item);

				// Заполняем кэш для дополнительных свойств товаров
				$param['FillMemCachePropertiesItem'] && $this->FillMemCachePropertiesItem($mass_item);

				// Заполняем кэш сопутствующих товаров
				$param['FillMemCacheTyingProducts'] && $this->FillMemCacheTyingProducts($mass_item);

				$param['FillMemCachePriceForItem'] && $this->FillMemCachePriceForItem($mass_item, $shop_row['shop_shops_id']);

				// Заполняем кэш для комметариев
				$param['FillMemCacheComments'] && $this->FillMemCacheComments($mass_item);

				// Заполняем кэш для тегов
				if (class_exists('Tag'))
				{
					$oTag = & singleton('Tag');
					$oTag->FillMemCacheGetTagRelation(array('shop_items_catalog_item_id' => $mass_item));
				}

				reset($modifications_array);

				$xmlData .= "<modifications>\n";
				foreach ($modifications_array as $value)
				{
					if ($value && count($value) > 0)
					{
						// Если товар сопутствующий, то $root=1
						$xmlData .= $this->GenXml4Item(1, $value, $site_users_id, $param);
					}
				}
				$xmlData .= "</modifications>\n";
			}
		}

		// Получаем теги для товаров
		if (class_exists('Tag'))
		{
			$oTag = & singleton('Tag');
			$tags = $oTag->GetTagRelation(array('shop_items_catalog_item_id' => $row['shop_items_catalog_item_id']));

			if ($tags)
			{
				$xmlData .= "<tags>\n";

				foreach ($tags as $tag)
				{
					// XML для тега
					$tag_xml = $oTag->GenXmlForTag($tag['tag_id'], $tag);

					if ($tag_xml)
					{
						$xmlData .= $tag_xml;
					}
				}

				$xmlData .= "</tags>\n";
			}
		}

		// Если основной товар
		if (!$root && $param['xml_show_tying_products'])
		{
			// Выбираем все сопутствующие товары для заданного товара
			$select = $this->GetTyingProductsForItem($row['shop_items_catalog_item_id']);

			if (count($select) > 0)
			{
				foreach ($select as $key => $value)
				{
					// Получаем информацию о сопутствующем товаре
					$row_tying = $this->GetItem($value['sho_shop_items_catalog_item_id']);

					// Ярлык на товар
					if ($row_tying['shop_items_catalog_shortcut_id'])
					{
						// Сохраняем группу ярлыка
						$shortcut_group_id = $row_tying['shop_groups_id'];

						// Получаем информацию о товаре, на который ссылаемся
						$row_tying = $this->GetItem($row_tying['shop_items_catalog_shortcut_id']);

						// Подменям группу на группу ярлыка
						$row_tying['shop_groups_id'] = $shortcut_group_id;
					}

					// Если товар есть и активен
					if ($row_tying && $row_tying['shop_items_catalog_is_active'] == 1)
					{
						$xmlData .= '<tying id="' . $row_tying['shop_items_catalog_item_id'] . '">' . "\n";
						$xmlData .= '<tying_count>' . Core_Type_Conversion::toInt($value['shop_intermediate_count']) . '</tying_count>' . "\n";

						// Если товар сопутствующий, то $root=1
						$xmlData .= $this->GenXml4Item(1, $row_tying, $site_users_id, $param);

						$xmlData .= '</tying>' . "\n";
					}
				}
			}
		}

		$xmlData .= '<shop_special_prices>' . "\n";

		// Получаем список всех специальных цен товара
		$special_prices_array = $this->GetSpecialPricesForItem($row['shop_items_catalog_item_id']);

		if ($special_prices_array)
		{
			foreach ($special_prices_array as $special_prices_row)
			{
				$xmlData .= $this->GenXML4SpecialPrice($special_prices_row['shop_special_prices_id']);
			}
		}
		$xmlData .= '</shop_special_prices>' . "\n";

		// Получаем информацию о ценах для групп пользователей
		$list_of_price_res = $this->GetAllPricesForItem($row['shop_items_catalog_item_id']);

		if ($list_of_price_res)
		{
			$xmlData .= '<shop_list_of_prices_for_item>' . "\n";

			// Получаем коэффициент перевода валют
			$currency_k = $this->GetCurrencyCoefficientToShopCurrency($row['shop_currency_id'], $shop_row['shop_currency_id']);

			foreach ($list_of_price_res as $k => $v)
			{
				$xmlData .= '<shop_list_of_price_for_item id="' . Core_Type_Conversion::toInt($v['shop_list_of_prices_id']) . '">' . Core_Type_Conversion::toFloat($v['shop_prices_to_item_value']) * $currency_k . '</shop_list_of_price_for_item>' . "\n";
			}

			$xmlData .= '</shop_list_of_prices_for_item>' . "\n";
		}

		// Дописываем информацию о количестве по каждому складу
		$aWarehouses = $warehouse->GetAllWarehousesForShop($shop_row['shop_shops_id']);

		if($aWarehouses)
		{
			$xmlData .= '<warehouses>' . "\n";

			foreach ($aWarehouses as $rows)
			{
				$xmlData .= '<warehouse id="' . $rows['shop_warehouse_id'] . '">';
				$xmlData .= $warehouse->GetItemCountForWarehouse($rows['shop_warehouse_id'], $row['shop_items_catalog_item_id']);
				$xmlData .= '</warehouse>' . "\n";
			}

			$xmlData .= '</warehouses>' . "\n";
		}

		$xmlData .= '</item>' . "\n";
		return $xmlData;
	}

	/**
	 * Генерация XML для групп дополнительных свойств
	 *
	 * @param int $shop_shops_id Идентификатор магазина
	 * @param int $shop_properties_items_dir_parent_id Идентификатор родительской директории
	 *
	 */
	function GenXmlForItemsPropertyDir($shop_shops_id, $shop_properties_items_dir_parent_id = 0)
	{
		$shop_shops_id = intval($shop_shops_id);

		$shop_properties_items_dir_parent_id = intval($shop_properties_items_dir_parent_id);

		if (isset($this->cache_propertys_items_dir_tree[$shop_shops_id][$shop_properties_items_dir_parent_id]) && $this->cache_propertys_items_dir_tree[$shop_shops_id][$shop_properties_items_dir_parent_id] > 0)
		{
			$counter = 0;
			foreach ($this->cache_propertys_items_dir_tree[$shop_shops_id][$shop_properties_items_dir_parent_id] as $shop_properties_items_dir_id)
			{
				// Получаем информацию о текущей группе дополнительных свойств товаров
				$shop_properties_items_dir_row = $this->GetPropertiesItemsDir($shop_properties_items_dir_id);

				// Генерация XML
				if ($shop_properties_items_dir_row)
				{
					$this->buffer .= '<properties_items_dir id="' . $shop_properties_items_dir_row['shop_properties_items_dir_id'] . '" parent_id="' . str_for_xml($shop_properties_items_dir_row['shop_properties_items_dir_parent_id']) . '">' . "\n";
					$this->buffer .= '<shop_shops_id>' . str_for_xml($shop_properties_items_dir_row['shop_shops_id']) . '</shop_shops_id>' . "\n";
					$this->buffer .= '<shop_properties_items_dir_name>' . str_for_xml($shop_properties_items_dir_row['shop_properties_items_dir_name']) . '</shop_properties_items_dir_name>' . "\n";
					$this->buffer .= '<shop_properties_items_dir_description>' . str_for_xml($shop_properties_items_dir_row['shop_properties_items_dir_description']) . '</shop_properties_items_dir_description>' . "\n";
					$this->buffer .= '<shop_properties_items_dir_order>' . str_for_xml($shop_properties_items_dir_row['shop_properties_items_dir_order']) . '</shop_properties_items_dir_order>' . "\n";
					$this->GenXmlForItemsPropertyDir($shop_shops_id, $shop_properties_items_dir_row['shop_properties_items_dir_id']);
					$this->buffer .= '</properties_items_dir>' . "\n";
				}
			}
		}
	}

	/**
	 * Генерация XML для групп дополнительных свойств групп товаров
	 *
	 * @param int $shop_shops_id Идентификатор магазина
	 * @param int $shop_properties_groups_dir_parent_id Идентификатор родительской директории
	 */
	function GenXmlForGroupsPropertyDir($shop_shops_id, $shop_properties_groups_dir_parent_id = 0)
	{
		$shop_shops_id = intval($shop_shops_id);
		$shop_properties_groups_dir_parent_id = intval($shop_properties_groups_dir_parent_id);

		if (isset($this->cache_propertys_groups_dir_tree[$shop_shops_id][$shop_properties_groups_dir_parent_id]) && $this->cache_propertys_groups_dir_tree[$shop_shops_id][$shop_properties_groups_dir_parent_id] > 0)
		{
			$counter = 0;
			foreach ($this->cache_propertys_groups_dir_tree[$shop_shops_id][$shop_properties_groups_dir_parent_id] as $shop_properties_groups_dir_id)
			{
				// Получаем информацию о текущей группе дополнительных свойств товаров
				$shop_properties_groups_dir_row = $this->GetPropertiesGroupsDir($shop_properties_groups_dir_id);

				// Генерация XML
				if ($shop_properties_groups_dir_row)
				{
					$this->buffer .= '<properties_groups_dir id="' . $shop_properties_groups_dir_row['shop_properties_groups_dir_id'] . '" parent_id="' . $shop_properties_groups_dir_row['shop_properties_groups_dir_parent_id'] . '">' . "\n";

					$this->buffer .= '<shop_shops_id>' . $shop_properties_groups_dir_row['shop_shops_id'] . '</shop_shops_id>' . "\n";

					$this->buffer .= '<shop_properties_groups_dir_name>' . $shop_properties_groups_dir_row['shop_properties_groups_dir_name'] . '</shop_properties_groups_dir_name>' . "\n";

					$this->buffer .= '<shop_properties_groups_dir_description>' . $shop_properties_groups_dir_row['shop_properties_groups_dir_description'] . '</shop_properties_groups_dir_description>' . "\n";

					$this->buffer .= '<shop_properties_groups_dir_order>' . $shop_properties_groups_dir_row['shop_properties_groups_dir_order'] . '</shop_properties_groups_dir_order>' . "\n";

					$this->GenXmlForGroupsPropertyDir($shop_shops_id, $shop_properties_groups_dir_row['shop_properties_groups_dir_id']);

					$this->buffer .= '</properties_groups_dir>' . "\n";
				}
			}
		}
	}

	/**
	 * Построение XML для товаров
	 *
	 * @param int $shop_id идентификатор Internet-магазина
	 * @param mixed $group_id идентификатор раздела каталога, если $group_id=false, то генерируем XML для товаров из всех разделов
	 * @param array $param массив дополнительных параметров
	 * - $param['items_begin'] номер товара в выборке, с которого начинать отображение товаров магазина
	 * - $param['items_on_page'] число товаров, отображаемых на странице
	 * - $param['items_field_order'] поле сортировки
	 * - $param['items_order'] направление сортировки ('Asc' - по возрастанию, 'Desc' - по убываниюб , 'Rand' - произвольный порядок)
	 * - $param['show_catalog_item_type'] array массив типов товаров, которые должны отображаться.
	 * Может содержать следующие элементы:
	 * <br />active - активные элементы (внесен по умолчанию, если $param['show_catalog_item_type'] не задан;
	 * <br />inactive - неактивные элементы;
	 * <br />putend_date - элементы, у которых значение поля putend_date меньше текущей даты;
	 * <br />putoff_date - элементы, у которых значение поля putoff_date превышает текущую дату;
	 * - $param['cache_off'] - Флаг, разрешающий кеширование данных (по умолчанию true)
	 * @return string XML с данными о товарах
	 *
	 */
	function GetItemsXmlTree($shop_id, $group_id = FALSE, $param = array())
	{
		if ($group_id !== FALSE)
		{
			$group_id = intval($group_id);
		}

		$shop_id = intval($shop_id);

		$param = Core_Type_Conversion::toArray($param);

		// Определяем id пользователя
		$site_users_id = Core_Type_Conversion::toInt($param['user_id']);

		$param['FillMemCacheTyingProducts'] = Core_Type_Conversion::toBool($param['FillMemCacheTyingProducts']);
		$param['FillMemCacheComments'] = Core_Type_Conversion::toBool($param['FillMemCacheComments']);
		$param['FillMemCachePropertiesItem'] = Core_Type_Conversion::toBool($param['FillMemCachePropertiesItem']);
		$param['FillMemCacheModificationItems'] = Core_Type_Conversion::toBool($param['FillMemCacheModificationItems']);
		$param['FillMemCacheSpecialPricesForItem'] = Core_Type_Conversion::toBool($param['FillMemCacheSpecialPricesForItem']);
		$param['FillMemCacheGetAllPricesForItem'] = Core_Type_Conversion::toBool($param['FillMemCacheGetAllPricesForItem']);
		$param['FillMemCachePriceForItem'] = Core_Type_Conversion::toBool($param['FillMemCachePriceForItem']);

		$xmlData = '';

		$items_begin = isset($param['items_begin'])
			? Core_Type_Conversion::toInt($param['items_begin'])
			: 0;

		$items_on_page = isset($param['items_on_page']) && $param['items_on_page'] !== FALSE
			? intval($param['items_on_page'])
			: 0;

		// Если сказано выводить товары, только тогда ведем их выборку
		if ($items_on_page > 0)
		{
			$result = $this->GetAllItems($shop_id, $group_id, $param);
			$count_items = $this->GetAllItemTotalCount;
		}
		else
		{
			$result = FALSE;
			$count_items = 0;
		}

		if ($count_items < $items_begin)
		{
			$items_begin = $count_items - $items_on_page;
			if ($items_begin < 0)
			{
				$items_begin = 0;
			}
		}

		$param['items_begin'] = $items_begin;

		$xmlData .= '<count_items>' . $count_items . '</count_items>' . "\n";

		// Определяем число страниц
		if ($items_on_page > 0)
		{
			$current_page = round($items_begin / $items_on_page);
		}
		else
		{
			$current_page = '';
		}

		$xmlData .= '<current_page>' . $current_page . '</current_page>' . "\n";
		$xmlData .= '<items_on_page>' . $items_on_page . '</items_on_page>' . "\n";

		if ($result)
		{
			foreach ($result as $row)
			{
				// Ярлык на товар
				if ($row['shop_items_catalog_shortcut_id'])
				{
					// Сохраняем группу ярлыка
					$shortcut_group_id = $row['shop_groups_id'];

					// Получаем информацию о товаре, на который ссылаемся
					$row = $this->GetItem($row['shop_items_catalog_shortcut_id']);

					// Shortcut doesn't exist
					if (!$row)
					{
						continue;
					}

					// Подменям группу на группу ярлыка
					$row['shop_groups_id'] = $shortcut_group_id;
				}

				$xmlData .= $this->GenXml4Item(0, $row, $site_users_id, $param);
			}
		}

		return $xmlData;
	}

	/**
	 * Вывод информации о товаре
	 *
	 * @param int $item_id идентификатор товара
	 * @param string $xsl_name название XSL шаблона
	 * @param array $param доп. параметры
	 * - $param['cache'] Флаг, указывающий, можно ли брать информацию с кэша (по умолчанию - true)
	 * - $param['current_group_id'] mixed идентификатор раздела магазина или массив идентификаторов
	 * - $param['user_id'] идентификатор пользователя
	 * - $param['group_field_order'] поле сортировки группы
	 * - $param['group_order'] направление сортировки группы ('Asc' - по возрастанию, 'Desc' - по убыванию, 'Rand' - произвольный порядок)
	 * - $param['xml_show_group_property'] разрешает указание в XML значений свойств групп магазина, по умолчанию true
	 * - $param['show_text'] параметр, указывающий включать в XML текст товара или нет, по умолчанию равен true
	 * - $param['xml_show_tying_products'] разрешает указание в XML сопутствующих товаров, по умолчанию true
	 * - $param['xml_show_modification'] разрешает указание в XML модификаций товаров, по умолчанию true
	 * - $param['xml_show_group_property_id'] массив идентификаторов дополнительных свойств для отображения в XML. Если не не передано - выводятся все свойства
	 * - $param['xml_show_comments'] разрешает добавление в XML отзывов о товаре, по умолчанию true
	 * - $param['xml_show_items_property_dir'] разрешает генерацию в XML групп свойств товаров, по умолчанию true
	 * - $param['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	 * - $param['xml_show_group_type'] тип генерации XML для групп, может принимать значения (по умолчанию 'tree'):
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>all - все группы всех уровней;
	 * <li>current - группы только текущего уровня;
	 * <li>tree - группы, находящиеся выше по дереву;
	 * <li>none - не выбирать группы.
	 * </ul>
	 * </li>
	 * </ul>
	 * <br />
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $item_id = 159;
	 * $xsl_name = 'МагазинТовар';
	 *
	 * $shop->ShowItem($item_id, $xsl_name);
	 *
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function ShowItem($item_id, $xsl_name, $param = array(), $external_propertys = array())
	{
		$item_id = intval($item_id);

		$external_propertys = Core_Type_Conversion::toArray($external_propertys);

		// Нужно обновить счетчик показов товара в базе
		$oShop_Item = Core_Entity::factory('Shop_Item', $item_id);
		$oShop_Item->showed++;
		$oShop_Item->save();

		!isset($param['cache']) && $param['cache'] = TRUE;
		!isset($param['xml_show_tying_products']) && $param['xml_show_tying_products'] = TRUE;
		!isset($param['xml_show_items_property_dir']) && $param['xml_show_items_property_dir'] = TRUE;
		!isset($param['xml_show_group_property']) && $param['xml_show_group_property'] = TRUE;
		!isset($param['xml_show_group_property_id']) && $param['xml_show_group_property_id'] = array();
		!isset($param['xml_show_item_property']) && $param['xml_show_item_property'] = TRUE;

		// по умолчанию выбираем данные по группам в виде дерева до текущей
		!isset($param['xml_show_group_type']) && $param['xml_show_group_type'] = 'tree';

		if (!isset($param['user_id']))
		{
			if (class_exists("SiteUsers"))
			{
				$SiteUsers = & singleton('SiteUsers');
				$site_users_id = $SiteUsers->GetCurrentSiteUser();
			}
			else
			{
				$site_users_id = 0;
			}
		}
		else
		{
			$site_users_id = intval($param['user_id']);
		}

		$result = $this->GetItem($item_id);

		// Текущий зарегистрированный пользователь имеет доступ товару
		if ($this->GetAccessShopItem($site_users_id, $item_id, $result))
		{
			$kernel = & singleton('kernel');

			if (isset($param['show_catalog_item_type']))
			{
				$aShop_items_catalog_is_active = array();

				// Если только активные (без неактивных)
				if (in_array('active', $param['show_catalog_item_type']))
				{
					$aShop_items_catalog_is_active[] = 1;
				}

				// только неактивные
				if (in_array('inactive', $param['show_catalog_item_type']))
				{
					$aShop_items_catalog_is_active[] = 0;
				}
			}
			else
			{
				$aShop_items_catalog_is_active = array(1);
			}

			// Информация о товаре получена и товар активен
			if ($result && in_array($result['shop_items_catalog_is_active'], $aShop_items_catalog_is_active))
			{

				// Модификация, берем ID родительской группы
				if ($result['shop_items_catalog_modification_id'] != 0)
				{
					$modification_row = $this->GetItem($result['shop_items_catalog_modification_id']);
					if ($modification_row)
					{
						// Подменяем ID группы
						$result['shop_groups_id'] = $modification_row['shop_groups_id'];
					}
				}

				if ($kernel->AllowShowPanel())
				{
					$param_panel = array();

					$row_shop = $this->GetShop(Core_Type_Conversion::toInt($result['shop_shops_id']));

					if ($row_shop)
					{
						// Редактирование информации о товаре
						$param_panel[0]['image_path'] = "/hostcmsfiles/images/edit.gif";

						$sPath = '/admin/shop/item/index.php';
						$sAdditional = "hostcms[action]=edit&shop_id={$row_shop['shop_shops_id']}&shop_group_id={$result['shop_groups_id']}&hostcms[checked][1][{$item_id}]=1";

						$param_panel[0]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
						$param_panel[0]['href'] = "{$sPath}?{$sAdditional}";
						$param_panel[0]['alt'] = "Редактировать информацию о товаре";

						// Копирование информации о товаре
						$param_panel[1]['image_path'] = "/hostcmsfiles/images/copy.gif";

						$sPath = '/admin/shop/item/index.php';
						$sAdditional = "hostcms[action]=copy&shop_id={$row_shop['shop_shops_id']}&shop_group_id={$result['shop_groups_id']}&hostcms[checked][1][{$item_id}]=1";

						$param_panel[1]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
						$param_panel[1]['href'] = "{$sPath}?{$sAdditional}";
						$param_panel[1]['alt'] = "Копировать информацию о товаре";

						// Удалить
						$param_panel[2]['image_path'] = "/hostcmsfiles/images/delete.gif";

						$sPath = '/admin/shop/item/index.php';
						$sAdditional = "hostcms[action]=markDeleted&shop_id={$row_shop['shop_shops_id']}&shop_group_id={$result['shop_groups_id']}&hostcms[checked][1][{$item_id}]=1";

						$param_panel[2]['onclick'] = "if (confirm('Вы действительно хотите удалить информацию о товаре?') == true){ $.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false} else {return false}";
						$param_panel[2]['href'] = "{$sPath}?{$sAdditional}";
						$param_panel[2]['alt'] = "Удалить информацию о товаре";

						// Выводим панель
						echo $kernel->ShowFlyPanel($param_panel);
					}
				}

				// Проверка на кэширование
				$cache_element_name = $item_id . '_' . $xsl_name . '_' . $site_users_id . '_' . $kernel->implode_array($param, '_') . '_' . $kernel->implode_array($external_propertys, '_');

				// Проверяем, установлен ли модуль кэширования
				if (class_exists('Cache') && $param['cache'])
				{
					$cache = & singleton('Cache');

					$cache_name = 'SHOW_SHOP_ITEM_XML';

					if (($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name)) && $in_cache)
					{
						echo $in_cache['value'];
						return TRUE;
					}
				}

				$content = FALSE;

				//if ($result)
				//{
				// Текущая группа
				if (isset($param['current_group_id']))
				{
					if ($param['current_group_id'] !== FALSE)
					{
						// Если родительская группа передана массивом хотя бы с одним элементом
						if (is_array($param['current_group_id']) && count($param['current_group_id']) > 0)
						{
							$current_group_id = implode($param['current_group_id']);
						}
						else
						{
							$current_group_id = Core_Type_Conversion::toInt($param['current_group_id']);
						}
					}
					else
					{
						$current_group_id = FALSE;
					}
				}
				else
				{
					// Нужно генерировать от корня до той группы, в которой хранится товар
					$param['current_group_id'] = $result['shop_groups_id'];
					$current_group_id = 0;
				}

				$row_shop = $this->GetShop(Core_Type_Conversion::toInt($result['shop_shops_id']));

				if ($row_shop)
				{
					$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
					$xmlData .= '<shop id="' . Core_Type_Conversion::toInt($row_shop['shop_shops_id']) . '" current_group_id="' . Core_Type_Conversion::toInt($result['shop_groups_id']) . '">' . "\n";

					/* Вносим внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа*/
					if (isset($param['external_xml']))
					{
						$xmlData .= $param['external_xml'];
					}

					// Вносим в XML дополнительные теги из массива дополнительных параметров
					$ExternalXml = new ExternalXml();
					$xmlData .= $ExternalXml->GenXml($external_propertys);

					// Информация о магазине
					$xmlData .= $this->GenXml4Shop($row_shop['shop_shops_id'], $row_shop);

					$param['parent_group_id'] = 0;

					// Если сказано отображать группы
					if ($param['xml_show_group_type'] != 'none')
					{
						// Выбираем только активные группы
						$param['groups_activity'] = 1;

						/* Заполняем массив групп*/
						$this->FillMasGroup($row_shop['shop_shops_id'], $param);

						if ($param['xml_show_group_type'] == 'current')
						{
							// Если в XML передаются только текущие группы - дерево групп строим от текущей группы, а не от корня.
							$param['parent_group_id'] = $current_group_id;
						}

						// Выбираем группы
						$result_groups = $this->GetAllGroups($row_shop['shop_shops_id']);

						if ($result_groups)
						{
							// Если стоит передавать свойства в группу
							// СВОЙСТВА ЗАПОЛЯНЕМ ТОЛЬКО ЕСЛИ НЕТ УЖЕ СГЕНЕРИРВОАННОГО XML
							if ($param['xml_show_group_property'])
							{
								// Заполняем значения свойств всех групп магазина
								//$this->FillMemCachePropertysGroup($result['shop_shops_id'], $param['xml_show_group_property_id']);
							}
							$xmlData .= $this->GetGroupsXmlTree($row_shop['shop_shops_id'], $param);
						}
					}

					// Обрабатываем группы дополнительных свойств товаров, очищаем кэши директорий для элементов для магазина
					// Формируем XML-данные для групп дополнительных свойств товара
					$dir_prop_array = $this->GetAllPropertiesItemsDirForShop($row_shop['shop_shops_id']);

					$this->cache_propertys_items_dir_tree[$row_shop['shop_shops_id']] = array();

					if (mysql_num_rows($dir_prop_array) > 0)
					{
						while ($dir_prop_row = mysql_fetch_assoc($dir_prop_array))
						{
							$this->cache_propertys_items_dir[$dir_prop_row['shop_properties_items_dir_id']] = $dir_prop_row;
							$this->cache_propertys_items_dir_tree[$row_shop['shop_shops_id']][$dir_prop_row['shop_properties_items_dir_parent_id']][] = $dir_prop_row['shop_properties_items_dir_id'];
						}
					}

					// Временный буфер
					$this->buffer = '';

					if ($param['xml_show_items_property_dir'])
					{
						// Вызов функции генерацци XML для групп дополнительных свойств
						$this->GenXmlForItemsPropertyDir($row_shop['shop_shops_id']);

						$xmlData .= $this->buffer;

						$this->buffer = '';
					}

					// Формируем XML для групп дополнительных свойств групп товаров
					$all_groups_property_for_groups = $this->GetAllPropertiesGroupsDirForShop($row_shop['shop_shops_id']);

					$this->cache_propertys_groups_dir_tree[$row_shop['shop_shops_id']] = array();

					if ($all_groups_property_for_groups && mysql_num_rows($all_groups_property_for_groups) > 0)
					{
						while ($all_groups_property_for_groups_row = mysql_fetch_assoc($all_groups_property_for_groups))
						{
							$this->cache_propertys_groups_dir[$all_groups_property_for_groups_row['shop_properties_groups_dir_id']] = $all_groups_property_for_groups_row;
							$this->cache_propertys_groups_dir_tree[$row_shop['shop_shops_id']][$all_groups_property_for_groups_row['shop_properties_groups_dir_parent_id']][] = $all_groups_property_for_groups_row['shop_properties_groups_dir_id'];
						}
					}

					// Вызов функции генерацци XML для групп дополнительных свойств
					$this->GenXmlForGroupsPropertyDir($row_shop['shop_shops_id']);

					$xmlData .= $this->buffer;

					$this->buffer = '';

					// Добавляем в XML данные о продавцах
					$xmlData .= $this->GenXml4Sallers($row_shop['shop_shops_id'], $param);

					// Добавляем данные о дополнительных свойствах
					$xmlData .= $this->GenXml4Properties($row_shop['shop_shops_id'], $result['shop_groups_id'], $param);

					$param['cache_off'] = !$param['cache'];

					// Выводим для родительского товара
					if ($result && $result['shop_items_catalog_modification_id'])
					{
						$result_parent_item = $this->GetItem($result['shop_items_catalog_modification_id']);

						if ($result_parent_item)
						{
							$xmlData .= '<parent_item>' . "\n";
							$xmlData .= $this->GenXml4Item(1, $result_parent_item, $site_users_id, $param);
							$xmlData .= '</parent_item>' . "\n";
						}
					}

					$xmlData .= $this->GenXml4Item(0, $result, $site_users_id, $param);
					$xmlData .= '</shop>' . "\n";

					$xsl = & singleton('xsl');
					$content = $xsl->build($xmlData, $xsl_name);

					echo $content;
				}
				else
				{
					$content = FALSE;
				}

				// Проверяем, установлен ли модуль кэширования
				if (class_exists('Cache') && $param['cache'])
				{
					$cache->Insert($cache_element_name, $content, $cache_name);
				}
			}
		}
	}

	/**
	 * Получение информации обо всех производителях
	 *
	 * @param array $param массив дополнительных условий для выборки
	 * - $param['shop_id'] int идентификатор магазина
	 * - $param['current_group_id'] int идентификатор или группа идентификаторов группы магазина, для товаров из которой необходимо получить производителей. Необязательно для заполнения
	 * - $param['xml_show_all_producers'] bool отображать всех производителей магазина
	 * - $param['begin'] начальная позиция выбора производителей
	 * - $param['count'] количество отображаемых производителей
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_id'] = 1;
	 * $param['current_group_id'] = 589;
	 * $param['begin'] = 0;
	 * $param['count'] = 10;
	 *
	 * $resource = $shop->GetAllProducers($param);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource информация о производителях (результат запроса)
	 */
	function GetAllProducers($param = array())
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'shop_producers_list_id'),
			array('shop_id', 'shop_shops_id'),
			array('name', 'shop_producers_list_name'),
			array('description', 'shop_producers_list_description'),
			array('image_large', 'shop_producers_list_image'),
			array('image_small', 'shop_producers_list_small_image'),
			array('sorting', 'shop_producers_list_order'),
			array('path',  'shop_producers_list_path'),
			array('user_id', 'users_id'),
			array('address', 'shop_producers_list_address'),
			array('phone', 'shop_producers_list_phone'),
			array('fax', 'shop_producers_list_fax'),
			array('site', 'shop_producers_list_site'),
			array('email', 'shop_producers_list_email'),
			array('tin', 'shop_producers_list_inn'),
			array('kpp', 'shop_producers_list_kpp'),
			array('psrn', 'shop_producers_list_ogrn'),
			array('okpo', 'shop_producers_list_okpo'),
			array('okved', 'shop_producers_list_okved'),
			array('bik', 'shop_producers_list_bik'),
			array('current_account', 'shop_producers_list_account'),
			array('correspondent_account', 'shop_producers_list_corr_account'),
			array('bank_name', 'shop_producers_list_bank_name'),
			array('bank_address', 'shop_producers_list_bank_address'),
			array('seo_title', 'shop_producers_list_seo_title'),
			array('seo_description', 'shop_producers_list_seo_description'),
			array('seo_keywords', 'shop_producers_list_seo_keywords')
			)
			->sqlCalcFoundRows()
			->from('shop_producers')
			->where('shop_producers.deleted', '=', 0);

		if (isset($param['count']))
		{
			$begin = Core_Type_Conversion::toInt($param['begin']);
			$count = Core_Type_Conversion::toInt($param['count']);
			$queryBuilder->limit($begin, $count);
		}

		// Не учитываем идентификаторы групп магазина
		if (!isset($param['current_group_id']) || isset($param['xml_show_all_producers']) && $param['xml_show_all_producers'])
		{
			// Проверяем наличие id магазина
			if (isset($param['shop_id']))
			{
				$param['shop_id'] = intval($param['shop_id']);
				$queryBuilder->where('shop_id', '=', $param['shop_id']);
			}
			$queryBuilder
				->orderBy('sorting')
				->orderBy('name');

			$return = $queryBuilder->execute()->asAssoc()->getResult();

			$aCount = Core_QueryBuilder::select(array('FOUND_ROWS()', 'count'))
				->execute()
				->asAssoc()
				->current();

			$this->GetAllProducersTotalCount = $aCount['count'];
		}
		else
		{
			$queryBuilder
				->clear()
				->select('shop_producers.id')
				->sqlCalcFoundRows()
				->distinct()
				->from('shop_producers')
				->where('shop_producers.deleted', '=', 0);

			// Если текущая группа не массив и не false
			if (!is_array($param['current_group_id']) && $param['current_group_id'] !== FALSE)
			{
				$param['current_group_id'] = array($param['current_group_id']);
			}

			if (is_array($param['current_group_id']) && count($param['current_group_id']) > 0)
			{
				if (isset($param['shop_items_catalog_is_active']) && intval($param['shop_items_catalog_is_active']) == 0)
				{
					$queryBuilder->where('shop_items.active', '=', 0);
				}
				else
				{
					$queryBuilder->where('shop_items.active', '=', 1);
				}

				$queryBuilder
					->join('shop_items', 'shop_items.shop_producer_id', '=', 'shop_producers.id')
					->where('shop_group_id', 'IN', $param['current_group_id'])
					->where('shop_items.deleted', '=', 0);
			}

			// Проверяем наличие id магазина
			if (isset($param['shop_id']))
			{
				$param['shop_id'] = intval($param['shop_id']);
				$queryBuilder->where('shop_producers.shop_id', '=', $param['shop_id']);
			}

			if (isset($param['count']))
			{
				$queryBuilder->limit($begin, $count);
			}

			$aResult = $queryBuilder->execute()->asAssoc()->result();

			$mas_shop_producers_list_id = array();
			foreach($aResult as $row)
			{
				$mas_shop_producers_list_id[] = $row['id'];
			}

			// Определим количество производителей
			$aCount = Core_QueryBuilder::select(array('FOUND_ROWS()', 'count'))->execute()->asAssoc()->current();

			$this->GetAllProducersTotalCount = $aCount['count'];

			if (count($mas_shop_producers_list_id) > 0)
			{
				$queryBuilder
					->clear()
					->select(
						array('id', 'shop_producers_list_id'),
						array('shop_id', 'shop_shops_id'),
						array('name', 'shop_producers_list_name'),
						array('description', 'shop_producers_list_description'),
						array('image_large', 'shop_producers_list_image'),
						array('image_small', 'shop_producers_list_small_image'),
						array('sorting', 'shop_producers_list_order'),
						array('path',  'shop_producers_list_path'),
						array('user_id', 'users_id'),
						array('address', 'shop_producers_list_address'),
						array('phone', 'shop_producers_list_phone'),
						array('fax', 'shop_producers_list_fax'),
						array('site', 'shop_producers_list_site'),
						array('email', 'shop_producers_list_email'),
						array('tin', 'shop_producers_list_inn'),
						array('kpp', 'shop_producers_list_kpp'),
						array('psrn', 'shop_producers_list_ogrn'),
						array('okpo', 'shop_producers_list_okpo'),
						array('okved', 'shop_producers_list_okved'),
						array('bik', 'shop_producers_list_bik'),
						array('current_account', 'shop_producers_list_account'),
						array('correspondent_account', 'shop_producers_list_corr_account'),
						array('bank_name', 'shop_producers_list_bank_name'),
						array('bank_address', 'shop_producers_list_bank_address'),
						array('seo_title', 'shop_producers_list_seo_title'),
						array('seo_description', 'shop_producers_list_seo_description'),
						array('seo_keywords', 'shop_producers_list_seo_keywords')
					)
					->from('shop_producers')
					->where('id', 'IN', $mas_shop_producers_list_id)
					->where('deleted', '=', 0)
					->orderBy('sorting')
					->orderBy('name');

				$return = $queryBuilder->execute()->asAssoc()->getResult();
			}
			else
			{
				$return = FALSE;
			}
		}

		return $return;
	}

	/**
	 * Генерация XML для производителя в магазине
	 *
	 * @param array $select данные о производителе
	 * @return mixed XML или false в случае отсутствия производителя
	 *
	 */
	function GetXmlProducer($select)
	{
		$select = Core_Type_Conversion::toArray($select);
		$xmlData = '';
		$xmlData .= '<producer id="' . Core_Type_Conversion::toInt($select['shop_producers_list_id']) . '">' . "\n";
		/*$xmlData .= '<name>'.str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_name'])).'</name>'."\n";
		$xmlData .= '<description>'.str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_description'])).'</description>'."\n";*/

		if (!empty($select['shop_producers_list_image']))
		{
			$xmlData .= '<image>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_image'])) . '</image>' . "\n";
			$xmlData .= '<image_path>/' . UPLOADDIR . 'shop_' . Core_Type_Conversion::toInt($select['shop_shops_id']) . '/producers/' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_image'])) . '</image_path>' . "\n";
		}

		if (!empty($select['shop_producers_list_small_image']))
		{
			$xmlData .= '<small_image>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_small_image'])) . '</small_image>' . "\n";
			$xmlData .= '<small_image_path>/' . UPLOADDIR . 'shop_' . Core_Type_Conversion::toInt($select['shop_shops_id']) . '/producers/' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_small_image'])) .'</small_image_path>' . "\n";
		}

		$xmlData .= '<path>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_path'])) . '</path>' . "\n";

		$xmlData .= '<shop_producers_list_name>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_name'])) . '</shop_producers_list_name>' . "\n";
		$xmlData .= '<shop_producers_list_description>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_description'])) . '</shop_producers_list_description>' . "\n";
		$xmlData .= '<shop_producers_list_image>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_image'])) . '</shop_producers_list_image>' . "\n";
		$xmlData .= '<shop_producers_list_path>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_path'])) . '</shop_producers_list_path>' . "\n";
		$xmlData .= '<shop_producers_list_address>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_address'])) . '</shop_producers_list_address>' . "\n";
		$xmlData .= '<shop_producers_list_phone>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_phone'])) . '</shop_producers_list_phone>' . "\n";
		$xmlData .= '<shop_producers_list_fax>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_fax'])) . '</shop_producers_list_fax>' . "\n";
		$xmlData .= '<shop_producers_list_site>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_site'])) . '</shop_producers_list_site>' . "\n";
		$xmlData .= '<shop_producers_list_email>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_email'])) . '</shop_producers_list_email>' . "\n";
		$xmlData .= '<shop_producers_list_inn>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_inn'])) . '</shop_producers_list_inn>' . "\n";
		$xmlData .= '<shop_producers_list_kpp>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_kpp'])).'</shop_producers_list_kpp>'."\n";
		$xmlData .= '<shop_producers_list_ogrn>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_ogrn'])) . '</shop_producers_list_ogrn>' . "\n";
		$xmlData .= '<shop_producers_list_okpo>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_okpo'])) . '</shop_producers_list_okpo>' . "\n";
		$xmlData .= '<shop_producers_list_okved>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_okved'])) . '</shop_producers_list_okved>' . "\n";
		$xmlData .= '<shop_producers_list_bik>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_bik'])) . '</shop_producers_list_bik>' . "\n";
		$xmlData .= '<shop_producers_list_account>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_account'])) . '</shop_producers_list_account>' . "\n";
		$xmlData .= '<shop_producers_list_corr_account>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_corr_account'])) . '</shop_producers_list_corr_account>' . "\n";
		$xmlData .= '<shop_producers_list_bank_name>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_bank_name'])) . '</shop_producers_list_bank_name>' . "\n";
		$xmlData .= '<shop_producers_list_bank_address>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_bank_address'])) . '</shop_producers_list_bank_address>' . "\n";
		$xmlData .= '<shop_producers_list_seo_title>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_seo_title'])) . '</shop_producers_list_seo_title>' . "\n";
		$xmlData .= '<shop_producers_list_seo_description>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_seo_description'])) . '</shop_producers_list_seo_description>' . "\n";
		$xmlData .= '<shop_producers_list_seo_keywords>' . str_for_xml(Core_Type_Conversion::toStr($select['shop_producers_list_seo_keywords'])) . '</shop_producers_list_seo_keywords>' . "\n";

		$xmlData .= '</producer>' . "\n";
		return $xmlData;
	}

	/**
	 * Получение информации о производителе по пути
	 *
	 * @param string $path путь производителя
	 * @param int $shop_id идентификатор магазина, необязательное поле
	 * @return result
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $path = 'hostcms';
	 *
	 * $result = $shop->GetPathProducer($path);
	 *
	 * // Распечатаем результат
	 * print_r ($result);
	 * ?>
	 * </code>
	 */
	function GetPathProducer($path, $shop_id = FALSE)
	{
		$oProducer = Core_Entity::factory('Shop_Producer');

		if ($shop_id)
		{
			$shop_id = intval($shop_id);
			$oProducer->queryBuilder()->where('shop_id', '=', $shop_id);
		}

		$oProducer->queryBuilder()->where('path', '=', $path)->limit(1);
		$aProducers = $oProducer->findAll();
		if (isset($aProducers[0]))
		{
			return $this->getArrayProducer($aProducers[0]);
		}

		return FALSE;
	}

	/**
	 * Устаревший метод формирования xml для производителя
	 *
	 * @param int $shop_producers_list_id идентификатор производителя
	 * @param string $xsl_name имя XSL шаблона
	 * @param array $param массив дополнительных параметров
	 * @access private
	 */
	function ShowProducer($shop_producers_list_id, $xsl_name, $param = array())
	{
		$shop_producers_list_id = intval($shop_producers_list_id);
		$result = $this->GetProducer($shop_producers_list_id);

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= $this->GetXmlProducer($result);

		$xsl = & singleton('xsl');
		$result = $xsl->build($xmlData, $xsl_name);

		echo $result;
	}

	/**
	 * Формирование xml для производителей
	 *
	 * @param string $xsl_name имя XSL шаблона
	 * @param array $param массив дополнительных параметров<br/>
	 * - $param['shop_id'] идентификатор магазина
	 * - $param['current_group_id'] идентификатор или группа идентификаторов группы магазина, для товаров из которой необходимо получить производителей. Необязательно для заполнения
	 * - $param['begin'] начальная позиция выбора производителей
	 * - $param['count'] количество отображаемых производителей
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $xsl_name = 'МагазинСписокПроизводителей';
	 * $param['shop_id'] = 1;
	 *
	 * $shop->ShowProducersList($xsl_name, $param);
	 *
	 * ?>
	 * </code>
	 * @return bool
	 * @see GenXmlProducerList()
	 */
	function ShowProducersList($xsl_name, $param = array(), $external_propertys = array())
	{
		$xmlData = $this->GenXmlProducerList($param, $external_propertys);

		if ($xmlData)
		{
			$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
			$xmlData;

			$xsl = & singleton('xsl');
			echo $xsl->build($xmlData, $xsl_name);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Генерация XML для списка продавцов магазина
	 *
	 * @param array $param массив дополнительных параметров
	 * - $param['shop_id'] идентификатор магазина
	 * - $param['current_group_id'] идентификатор или группа идентификаторов группы магазина, для товаров из которой необходимо получить производителей. Необязательно для заполнения
	 * - $param['begin'] начальная позиция выбора производителей
	 * - $param['count'] количество отображаемых производителей
	 * - $param['show_shop_xml'] вносить информацию в XML о магазине, по умолчанию true
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_id'] = 1;
	 * $param['current_group_id'] =589;
	 *
	 * $xmlData = $shop->GenXmlProducerList($param);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return mixed XML или false в случае отсутствия продавцов
	 */
	function GenXmlProducerList($param, $external_propertys = array())
	{
		if (!isset($param['show_shop_xml']))
		{
			$param['show_shop_xml'] = TRUE;
		}
		$xmlData = '';
		$a_producers = $this->GetAllProducers($param);

		if ($a_producers)
		{
			$xmlData .= '<producerslist>' . "\n";

			$shop_id = Core_Type_Conversion::toInt($param['shop_id']);

			// Данные о магазине в xml
			if ($param['show_shop_xml'])
			{
				$xmlData .= '<shop id="' . $shop_id . '">' . "\n";
				$xmlData .= $this->GenXml4Shop($shop_id);
				$xmlData .= '</shop>' . "\n";
			}

			$begin = isset($param['begin'])
				? intval($param['begin'])
				: 0;

			$items_on_page = isset($param['count']) && $param['count'] !== FALSE
				? intval($param['count'])
				: 0;

			// Реальное количество выбранных
			$count = mysql_num_rows($a_producers);
			$total_count = $this->GetAllProducersTotalCount;
			if ($total_count < $begin)
			{
				$begin = $total_count - $count;

				if ($begin < 0)
				{
					$begin = 0;
				}
			}

			$xmlData .= '<count_items>' . $total_count . '</count_items>' . "\n";

			// Определяем число страниц
			$current_page = $items_on_page > 0
				? round($begin / $items_on_page)
				: '';

			$xmlData .= '<current_page>' . $current_page . '</current_page>' . "\n";
			$xmlData .= '<items_on_page>' . $items_on_page . '</items_on_page>' . "\n";

			// Вносим в XML дополнительные теги из массива дополнительных параметров
			$ExternalXml = new ExternalXml();
			$xmlData .= $ExternalXml->GenXml($external_propertys);

			for ($i = 0; $i < $count; $i++)
			{
				$value = mysql_fetch_assoc($a_producers);
				$xmlData .= $this->GetXmlProducer($value);
			}
			$xmlData .= '</producerslist>' . "\n";
			return $xmlData;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Получение товаров, находящихся в корзине
	 *
	 * @param int $site_users_id идентификатор пользователя
	 * @param int $shop_id идентификатор магазина
	 * @param bool $always_use_cookies_cart использовать корзину кукисов, если $site_users_id = false
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $site_users_id = '';
	 * $shop_id = 1;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $a_row = $shop->GetItemsFromCart($site_users_id, $shop_id);
	 *
	 * // Распечатаем результат
	 * print_r ($a_row);
	 * ?>
	 * </code>
	 * @return array $a_row массив товаров и их свойств
	 */
	function GetItemsFromCart($site_users_id, $shop_id, $always_use_cookies_cart = TRUE)
	{
		$site_users_id = intval($site_users_id);
		$shop_id = intval($shop_id);

		if ($site_users_id)
		{
			$aShop_Carts = Core_Entity::factory('Shop', $shop_id)->Shop_Carts->getBySiteuserId($site_users_id);

			$a_row = array();
			foreach($aShop_Carts as $oShop_Cart)
			{
				$oShop_Cart->quantity = floatval($oShop_Cart->quantity);
				$a_row[] = $this->getArrayShopCart($oShop_Cart);
			}
		}
		else
		{
			$CART = array();

			// Если модуль пользователей сайта есть - работаем с корзиной на кукисах
			if (class_exists("SiteUsers") || $always_use_cookies_cart)
			{
				$CART = $this->GetCart();
			}
			else
			{
				// Модуля пользователей сайта нет - работаем с корзиной сессии
				if (!empty ($_SESSION['CART']))
				{
					$CART = $_SESSION['CART'];
				}
			}

			$a_row = Core_Type_Conversion::toArray($CART[$shop_id]);
		}

		return $a_row;
	}

	/**
	 * Отображение формы для авторизации пользователя
	 *
	 * @param string $xsl_name наименование xsl-шаблона в соответствии, с которым выводим данные
	 * @param array $external_propertys массив внешних данных для включения в XML
	 */
	function ShowEnter($xsl_name, $external_propertys = array())
	{
		$external_propertys = Core_Type_Conversion::toArray($external_propertys);
		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<enter>' . "\n";

		if (count($external_propertys) > 0)
		{
			// Вносим в XML дополнительные теги из массива дополнительных параметров
			$ExternalXml = new ExternalXml;
			$xmlData .= $ExternalXml->GenXml($external_propertys);
		}

		$xmlData .= '</enter>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Показ сообщения о необходимости подтвердить аккаунт
	 *
	 * @param string $xsl_name наименование xsl-шаблона
	 * @param array $external_propertys массив внешних данных для включения в XML
	 */
	function ShowConfirmation($xsl_name, $external_propertys = array())
	{
		$external_propertys = Core_Type_Conversion::toArray($external_propertys);
		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<Confirmation>' . "\n";

		if (count($external_propertys) > 0)
		{
			// Вносим в XML дополнительные теги из массива дополнительных параметров
			$ExternalXml = new ExternalXml;
			$xmlData .= $ExternalXml->GenXml($external_propertys);
		}

		$xmlData .= '</Confirmation>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Показ содержимого корзины
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param int $site_users_id идентификатор пользователя, если false - определяем пользователя внутри метода.
	 * @param string $xsl_name имя XSL-шаблона
	 * @param array $param массив дополнительных параметров
	 * - $param['shop_coupon_text'] - текст купона
	 * - $param['xml_show_group_type'] - метод отображения групп товаров, по умолчанию 'none'
	 * - $param['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	 * - $param другие параметры, указанные для GetGroupsXmlTree()
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * @see GetGroupsXmlTree()
	 */
	function ShowCart($shop_id, $site_users_id = FALSE, $xsl_name, $param = array(), $external_propertys = array())
	{
		$xsl_name = Core_Type_Conversion::toStr($xsl_name);
		$shop_coupon_text = Core_Type_Conversion::toStr($param['shop_coupon_text']);

		if ($site_users_id === FALSE)
		{
			if (class_exists('SiteUsers'))
			{
				$SiteUsers = & singleton('SiteUsers');
				$site_users_id = $SiteUsers->GetCurrentSiteUser();
			}
			else
			{
				$site_users_id = FALSE;
			}
		}
		else
		{
			$site_users_id = Core_Type_Conversion::toInt($site_users_id);
		}

		// по умолчанию выбираем данные о группах не выводить
		if (!isset($param['xml_show_group_type']))
		{
			$param['xml_show_group_type'] = 'none';
		}

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<cart>' . "\n";

		/* Вносим внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа*/
		if (isset($param['external_xml']))
		{
			$xmlData .= $param['external_xml'];
		}

		// Вносим в XML дополнительные теги из массива дополнительных параметров
		$ExternalXml = new ExternalXml;
		$xmlData .= $ExternalXml->GenXml($external_propertys);

		// Получаем данные о магазине
		$row_shop = $this->GetShop($shop_id);

		if ($row_shop)
		{
			// Данные о магазине в xml
			$xmlData .= '<shop id="' . $shop_id . '">' . "\n";

			$xmlData .= $this->GenXml4Shop($shop_id, $row_shop);

			// Формируем данные о группах товаров в зависимости от переданных параметров
			// Если сказано отображать группы
			if ($param['xml_show_group_type'] != 'none')
			{
				if ($param['xml_show_group_type'] == 'current')
				{
					// Если в XML передаются только текущие группы - дерево групп строим от текущей группы, а не от корня.
					$param['parent_group_id'] = Core_Type_Conversion::toInt($param['current_group_id']);
				}

				// Заполняем массив групп
				$this->FillMasGroup($shop_id, $param);

				$xmlData .= $this->GetGroupsXmlTree($shop_id, $param);
			}

			$xmlData .= '</shop>' . "\n";

			// Пользователь сайта
			$xmlData .= '<user_id>' . $site_users_id . '</user_id>' . "\n";

			/* Определяем, подключен ли класс пользователей сайта.
			 В бесплатной и стартовой версии этот класс не установлен*/
			if (class_exists('SiteUsers'))
			{
				$xmlData .= "<site_users_class_exists>1</site_users_class_exists>\n";
			}
			else
			{
				$xmlData .= "<site_users_class_exists>0</site_users_class_exists>\n";
			}

			// Получаем данные об элементах из корзины
			$aCartRows = $this->GetItemsFromCart($site_users_id, $shop_id);

			$count_item = 0; // Количество элементов
			$count_postpone_item = 0; // Количество отложенных товаров

			// Общее количество элементов
			$quantity_all = 0;

			// Общий вес
			$weight_all = 0;

			// Общая стоимость
			$total_sum = 0;

			// Общее количество элементов и т.д. для отложенных элементов
			$quantity_all_for_postpone = 0;
			$weight_all_for_postpone = 0;
			$total_sum_for_postpone = 0;

			if (count(Core_Type_Conversion::toArray($aCartRows)) > 0)
			{
				$mass_item = array();

				foreach ($aCartRows as $key => $value)
				{
					$mass_item[] = $value['shop_items_catalog_item_id'];
				}

				$this->FillMemCacheItems($mass_item);
				$this->FillMemCacheSpecialPricesForItem($mass_item);
				$this->FillMemCacheDiscountsForItem($mass_item);
				$this->FillMemCacheGetAllPricesForItem($mass_item);
				$this->FillMemCacheModificationItems($mass_item);
				$this->FillMemCachePropertiesItem($mass_item);
				$this->FillMemCacheTyingProducts($mass_item);
				$this->FillMemCachePriceForItem($mass_item, $shop_id);
				$this->FillMemCacheComments($mass_item);

				// Заполняем кэш для тегов
				if (class_exists('Tag'))
				{
					$oTag = & singleton('Tag');
					$oTag->FillMemCacheGetTagRelation(array(
					'shop_items_catalog_item_id' => $mass_item
					));
				}

				reset($aCartRows);

				// В цикле разбираем массив товаров, полученных из корзины
				foreach ($aCartRows as $key => $value)
				{
					// Проверяем наличие данного товара в базе
					$item_row = $this->GetItem($value['shop_items_catalog_item_id']);

					if ($item_row)
					{
						$xmlData .= '<itemincart id="' . Core_Type_Conversion::toInt($value['shop_cart_id']) . '">' . "\n";

						// Выводим для родительского товара
						if ($item_row && $item_row['shop_items_catalog_modification_id'])
						{
							$result_parent_item = $this->GetItem($item_row['shop_items_catalog_modification_id']);

							if ($result_parent_item)
							{
								$xmlData .= '<parent_item>' . "\n";
								$xmlData .= $this->GenXml4Item(1, $result_parent_item, $site_users_id);
								$xmlData .= '</parent_item>' . "\n";
							}
						}

						$xmlData .= $this->GenXml4Item(0, $item_row, $site_users_id, array(
							'item_count' => $value['shop_cart_item_quantity']
						));

						$xmlData .= '<shop_warehouse_id>' . str_for_xml(Core_Type_Conversion::toInt($value['shop_warehouse_id'])) . '</shop_warehouse_id>' . "\n";
						$xmlData .= '<flag_postpone>' . str_for_xml(Core_Type_Conversion::toInt($value['shop_cart_flag_postpone'])) . '</flag_postpone>' . "\n";
						$xmlData .= '<quantity>' . str_for_xml($value['shop_cart_item_quantity']) . '</quantity>' . "\n";

						// Общая стоимость товаров
						$price = $this->GetPriceForUser($site_users_id, $item_row['shop_items_catalog_item_id'], $item_row, array(
						'item_count' => $value['shop_cart_item_quantity']
						));

						// Для расчетов корзины берем цену со скидкой
						$price = $price['price_discount'];

						$price_all = $this->Round($value['shop_cart_item_quantity'] * $price);

						$xmlData .= '<price_all>' . str_for_xml($price_all) . '</price_all>' . "\n";

						// Проверяем является ли данный товар отложенным
						// если товар не отложен
						if (!Core_Type_Conversion::toInt($value['shop_cart_flag_postpone']))
						{
							// Наращиваем общее кол-во
							$quantity_all += $value['shop_cart_item_quantity'];

							// Добавляем цену товара в общую стоимость
							$total_sum += $value['shop_cart_item_quantity'] * $price;

							// Добавляем вес товара в общий вес
							$weight_all += $value['shop_cart_item_quantity'] * $item_row['shop_items_catalog_weight'];
							$count_item++;
						}
						// Товар является отложенным
						else
						{
							// Наращиваем кол-во отложенных товаров
							$quantity_all_for_postpone += $value['shop_cart_item_quantity'];
							// Добавляем цену товара в общую стоимость отложенных товаров
							$total_sum_for_postpone += $value['shop_cart_item_quantity'] * $price;
							// Добавляем вес товара в общий вес отложенных товаров
							$weight_all_for_postpone += $value['shop_cart_item_quantity'] * $item_row['shop_items_catalog_weight'];
							$count_postpone_item++;
						}
						$xmlData .= '</itemincart>' . "\n";
					}
				}
			}

			// Определяем наличие скидки на сумму заказа
			$discount = $this->GetOrderDiscountForSumAndCount($shop_id, $total_sum, $quantity_all, $shop_coupon_text);
			if ($discount == -1)
			{
				$discount = 0;

				// Ошибка! Общая сумма не может быть отрицательной!
				$message = "Внимание для заказа на сумму $total_sum была расчитана скидка на сумму превышающую сумму заказа, что недопустимо. Заказ будет оформлен без скидок";

				// Пишем в log файл ошибку с информацией о том, что скидка больше суммы заказа
				$EventsJournal = new EventsJournal();
				$EventsJournal->log_access(USER_NONE, $message, 4);
			}

			$total_sum_without_discount = $total_sum;
			$total_sum = $total_sum - $discount;

			// Текст купона
			if (!empty ($shop_coupon_text))
			{
				$xmlData .= '<coupon_text>' . str_for_xml($shop_coupon_text) . '</coupon_text>' . "\n";
			}

			// Общее кол-во, вес и стоимость товаров
			$xmlData .= '<totalquantity>' . str_for_xml($quantity_all) . '</totalquantity>' . "\n";
			$xmlData .= '<totalweight>' . str_for_xml($weight_all) . '</totalweight>' . "\n";

			$xmlData .= '<totalsum>' . $this->Round($total_sum) . '</totalsum>' . "\n";

			$total_sum_without_discount = $this->Round($total_sum_without_discount);
			$xmlData .= '<total_sum_without_discount>' . $total_sum_without_discount . '</total_sum_without_discount>' . "\n";

			// Общее кол-во, вес и стоимость товаров для отложенных товаров
			$xmlData .= '<totalquantity_postpone_item>' . str_for_xml($quantity_all_for_postpone) . '</totalquantity_postpone_item>' . "\n";
			$xmlData .= '<totalweight_postpone_item>' . str_for_xml($weight_all_for_postpone) . '</totalweight_postpone_item>' . "\n";

			$totalsum_postpone_item = $this->Round($total_sum_for_postpone);
			$xmlData .= '<totalsum_postpone_item>' . $totalsum_postpone_item . '</totalsum_postpone_item>' . "\n";

			// Количество отдельных товаров (разновидностей)
			$xmlData .= '<count_item>' . $count_item . '</count_item>' . "\n";

			// Количество отложенных товаров (разновидностей)
			$xmlData .= '<count_postpone_item>' . $count_postpone_item . '</count_postpone_item>' . "\n";
		}
		else
		{
			$xmlData .= '<error_not_isset_shop>1</error_not_isset_shop>' . "\n";
		}

		$xmlData .= $this->GenXml4Sallers($shop_id);

		$xmlData .= '</cart>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Показ адреса для заказчика
	 *
	 * @param string $xsl_name имя XSL-шаблона
	 * @param int $shop_id идентификатор магазина
	 * @param array $param массив допольнительных параметров
	 * - $param['show_country'] добавлять в XML список стран, по умолчанию true
	 * - $param['show_location'] добавлять в XML список областей, по умолчанию true
	 * - $param['show_city'] добавлять в XML список городов, по умолчанию true
	 * - $param['show_city_area'] добавлять в XML список районов городов, по умолчанию true
	 * - $param['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	 * @param array $external_propertys массив внешних данных для включения в XML
	 */
	function ShowAddress($xsl_name, $shop_id, $param = array(), $external_propertys = array())
	{
		$shop_id = intval($shop_id);

		$external_propertys = Core_Type_Conversion::toArray($external_propertys);

		!isset($param['show_country']) && $param['show_country'] = TRUE;
		!isset($param['show_location']) && $param['show_location'] = TRUE;
		!isset($param['show_city']) && $param['show_city'] = TRUE;
		!isset($param['show_city_area']) && $param['show_city_area'] = TRUE;

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<locations>' . "\n";

		if (isset($param['external_xml']))
		{
			$xmlData .= $param['external_xml'];
		}

		// Данные о магазине в xml
		$xmlData .= '<shop id="' . $shop_id . '">' . "\n";
		$xmlData .= $this->GenXml4Shop($shop_id);
		$xmlData .= '</shop>' . "\n";

		// Внешние данные для включения в XML
		$xmlData .= "<external_propertys>\n";
		$ExternalXml = & singleton ('ExternalXml');
		$xmlData .= $ExternalXml->GenXml($external_propertys);
		$xmlData .= "</external_propertys>\n";

		$shop_row = $this->GetShop($shop_id);

		$default_country = $shop_row ? $shop_row['shop_country_id'] : 0;

		if ($param['show_country'])
		{
			$result = $this->GetAllCountries();
			if ($result)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$selected = $default_country == $row['shop_country_id'] ? 1 : 0;

					$xmlData .= '<country id="' . Core_Type_Conversion::toInt($row['shop_country_id']) . '" select="' . $selected . '">' . "\n";
					$xmlData .= '<name>' . str_for_xml($row['shop_country_name']) . '</name>' . "\n";
					$xmlData .= '</country>' . "\n";
				}
			}
		}

		if ($param['show_location'])
		{
			$result = $this->GetAllLocation();
			if ($result)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$xmlData .= '<location id="' . str_for_xml($row['shop_location_id']) . '" parent="' . $row['shop_country_id'] . '">' . "\n";
					$xmlData .= '<name>' . str_for_xml($row['shop_location_name']) . '</name>' . "\n";
					$xmlData .= '</location>' . "\n";
				}
			}
		}

		if ($param['show_city'])
		{
			$result = $this->GetAllCity();
			if ($result)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$xmlData .= '<city id="' . str_for_xml($row['shop_city_id']) . '" parent="' . $row['shop_location_id'] . '">' . "\n";
					$xmlData .= '<name>' . str_for_xml($row['shop_city_name']) . '</name>' . "\n";
					$xmlData .= '</city>' . "\n";
				}
			}
		}

		if ($param['show_city_area'])
		{
			$result = $this->GetAllCityArea();
			if ($result)
			{
				while ($row = mysql_fetch_assoc($result))
				{
					$xmlData .= '<cityarea id="' . str_for_xml($row['shop_city_area_id']) . '" parent="' . $row['shop_city_id'] . '">' . "\n";
					$xmlData .= '<name>' . str_for_xml($row['shop_city_area_name']) . '</name>' . "\n";
					$xmlData .= '</cityarea>' . "\n";
				}
			}
		}

		$xmlData .= '</locations>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Получение способа доставки определенного типа для заказа с определенными параметрами. Возвращает первый подходящий способ доставки для типа доставки.
	 *
	 * @param int $shop_type_of_delivery_id идентификатор типа доставки
	 * @param int $country идентификатор страны доставки
	 * @param int $location идентификатор области доставки
	 * @param int $city идентификатор города доставки
	 * @param int $city_area идентификатор района доставки
	 * @param float $weight вес заказа
	 * @param float $price цена заказа
	 * @return array результат выборки
	 */
	function GetTypeOfDeliveryForOrder($shop_type_of_delivery_id, $country, $location, $city, $city_area, $weight, $price)
	{
		$shop_type_of_delivery_id = intval($shop_type_of_delivery_id);
		$country = intval($country);
		$location = intval($location);
		$city = intval($city);
		$city_area = intval($city_area);
		$weight = floatval($weight);
		$price = floatval($price);

		// Выбираем все способы доставки для данного типа с заданными условиями
		// Формируем и выполняем запрос
		$i = 0;

		while ($i <= 4)
		{
			// Поле orderfield внесено для того, чтобы поля со всеми заполенынми условиями были выше
			$queryBuilder = Core_QueryBuilder::select(
					array('shop_delivery_conditions.id', 'shop_cond_of_delivery_id'),
					array('shop_delivery_id', 'shop_type_of_delivery_id'),
					array('shop_country_id', 'shop_country_id'),
					array('shop_country_location_id', 'shop_location_id'),
					array('shop_country_location_city_id', 'shop_city_id'),
					array('shop_country_location_city_area_id', 'shop_city_area_id'),
					array('shop_delivery_conditions.name', 'shop_cond_of_delivery_name'),
					array('min_weight', 'shop_cond_of_delivery_weight_from'),
					array('max_weight', 'shop_cond_of_delivery_weight_to'),
					array('min_price', 'shop_cond_of_delivery_price_from'),
					array('max_price', 'shop_cond_of_delivery_price_to'),
					array('shop_delivery_conditions.description', 'shop_cond_of_delivery_description'),
					array('price', 'shop_cond_of_delivery_price'),
					array('shop_currency_id', 'shop_currency_id'),
					array('shop_delivery_conditions.user_id', 'users_id'),
					array('shop_tax_id', 'shop_tax_id'),
					array('shop_deliveries.id', 'shop_type_of_delivery_id'),
					array('shop_deliveries.name', 'shop_type_of_delivery_name'),
					array('shop_deliveries.description', 'shop_type_of_delivery_description'),
					array('image', 'shop_type_of_delivery_image'),
					array('shop_deliveries.shop_id', 'shop_shops_id'),
					array('shop_deliveries.sorting', 'shop_type_of_delivery_order'),
					Core_QueryBuilder::expression('IF ( `min_weight` > 0 AND `max_weight` > 0 AND  `min_price` > 0 AND `max_price` > 0, 1, 0) as orderfield')
				)
				->from('shop_delivery_conditions')
				->leftJoin('shop_deliveries', 'shop_delivery_conditions.shop_delivery_id', '=', 'shop_deliveries.id')
				->where('shop_deliveries.deleted', '=', 0)
				->where('shop_delivery_conditions.deleted', '=', 0)
				// Отрезаем по типу доставки
				->where('shop_delivery_conditions.shop_delivery_id', '=', $shop_type_of_delivery_id)
				// Отрезаем по Стране, Области, Городу и Району
				->where('shop_delivery_conditions.shop_country_id', '=', $country)
				->where('shop_delivery_conditions.shop_country_location_id', '=', $location)
				->where('shop_delivery_conditions.shop_country_location_city_id', '=', $city)
				->where('shop_delivery_conditions.shop_country_location_city_area_id', '=', $city_area)
				// Основная обрезка по характеристикам заказа
				->where('shop_delivery_conditions.min_weight', '<=', $weight)
				->open()
				->where('shop_delivery_conditions.max_weight', '>=', $weight)
				->setOr()
				->where('shop_delivery_conditions.max_weight', '=', 0)
				->close()
				->where('shop_delivery_conditions.min_price', '<=', $price)
				->open()
				->where('shop_delivery_conditions.max_price', '>=', $price)
				->setOr()
				->where('shop_delivery_conditions.max_price', '=', 0)
				->close()
				// Сортируем вывод
				->orderBy('orderfield', 'DESC')
				->orderBy('min_weight', 'DESC')
				->orderBy('max_weight', 'DESC')
				->orderBy('min_price', 'DESC')
				->orderBy('max_price', 'DESC')
				->orderBy('price', 'DESC');

			$aResult = $queryBuilder->execute()->asAssoc()->result();

			// Проверяем выбрали ли хотя бы одну запись
			if (count($aResult) > 0)
			{
				$result = $aResult[0];

				// Проверяем количество выбранных записей
				if (count($aResult) > 1)
				{
					// Выбираем наименование типа доставки
					$row = $this->GetTypeOfDelivery($shop_type_of_delivery_id);

					if ($row)
					{
						/* Пишем в log файл ошибку с информацией о том, что доставки
						 пересекаются*/
						$EventsJournal = new EventsJournal();

						$shop_type_of_delivery_name = $row['shop_type_of_delivery_name'];
						$message = "Внимание при выборе доставки было выбрано несколько одинаковых условий доставки для типа \"{$shop_type_of_delivery_name}\".
						Было оставлено условие доставки (код {$result['shop_cond_of_delivery_id']}) с наименьшей ценой, рекомендуем проверить условия доставки для данного типа.";

						$EventsJournal->log_access(USER_NONE, $message, 3);
					}
				}

				return $result;
			}
			else
			{
				switch ($i)
				{
					/* По порядку цикла отменяем ограничения, начинаем с района (метро),
					 и заканчиваем страной*/
					case 0 :
						$city_area = 0;
					break;
					case 1 :
						$city = 0;
					break;
					case 2 :
						$location = 0;
					break;
					case 3 :
						$country = 0;
					break;
				}
			}
			$i++;
		}
		return FALSE;
	}

	/**
	 * Получение всех типов доставки для конкретного магазина
	 * @param int $shop_shops_id идентификатор магазина
	 * @return resource список типов доставки (результат запроса)
	 */
	function GetAllTypeOfDelivery($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_type_of_delivery_id'),
				array('name', 'shop_type_of_delivery_name'),
				array('description', 'shop_type_of_delivery_description'),
				array('image', 'shop_type_of_delivery_image'),
				array('user_id', 'users_id'),
				array('sorting', 'shop_type_of_delivery_order'),
				array('shop_id', 'shop_shops_id')
			)
			->from('shop_deliveries')
			->where('shop_id', '=', $shop_shops_id)
			->where('deleted', '=', 0)
			->orderBy('sorting')
			->orderBy('name');

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Показ типов доставки
	 *
	 * @param int $country идентификатор страны (0-любая страна)
	 * @param int $location идентификатор местоположения (области) (0-любая область)
	 * @param int $city идентификатор города (0-любой город)
	 * @param int $city_area идентификатор района (0-любой район)
	 * @param float $weight вес корзины
	 * @param float $price цена корзины
	 * @param int $currency идентификатор валюты
	 * @param string $xsl_name имя XSL-шаблона
	 * @param array $param массив с доп. параметрами
 	 * - $param['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $country = 175;
	 * $location = 58;
	 * $city = 2005;
	 * $city_area = 8;
	 * $weight = 10;
	 * $price = 1000;
	 * $xsl_name = 'МагазинДоставки';
	 *
	 * $result = $shop->ShowTypeOfDelivery($shop_shops_id, $country, $location, $city, $city_area, $weight, $price, $xsl_name, $external_propertys = array());
	 *
	 * ?>
	 * </code>
	 */
	function ShowTypeOfDelivery($shop_shops_id, $country, $location, $city, $city_area, $weight, $price, $xsl_name, $param, $external_propertys = array())
	{
		$param = Core_Type_Conversion::toArray($param);
		$shop_shops_id = intval($shop_shops_id);
		$country = intval($country);
		$location = intval($location);
		$city = intval($city);
		$city_area = intval($city_area);
		$weight = floatval($weight);
		$price = floatval($price);

		$count = Core_Type_Conversion::toInt($param['count']);
		$shop_coupon_text = Core_Type_Conversion::toStr($param['shop_coupon_text']);

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<types_of_delivery>' . "\n";
		if (isset($param['external_xml']))
		{
			$xmlData .= $param['external_xml'];
		}

		$xmlData .= '<shop id="' . $shop_shops_id . '">' . "\n";
		$xmlData .= $this->GenXml4Shop($shop_shops_id);
		$xmlData .= '</shop>' . "\n";

		$xmlData .= '<country>' . str_for_xml($country) . '</country>' . "\n";
		$xmlData .= '<location>' . str_for_xml($location) . '</location>' . "\n";
		$xmlData .= '<city>' . str_for_xml($city) . '</city>' . "\n";
		$xmlData .= '<city_area>' . str_for_xml($city_area) . '</city_area>' . "\n";

		// Вносим в XML дополнительные теги из массива дополнительных параметров
		$ExternalXml = new ExternalXml;
		$xmlData .= $ExternalXml->GenXml($external_propertys);

		// Выбираем все типы доставки для данного магазина
		$result_type_of_delivery = $this->GetAllTypeOfDelivery($shop_shops_id);
		$count_type_of_delivery = mysql_num_rows($result_type_of_delivery);

		// Кол-во доставок
		$count_cond_of_delivery = 0;

		// Определяем наличие скидки на сумму заказа
		$discount = $this->GetOrderDiscountForSumAndCount($shop_shops_id, $price, $count, $shop_coupon_text);
		if ($discount == -1)
		{
			$discount = 0;

			// Ошибка! Общая сумма не может быть отрицательной!
			$message = "Внимание для заказа на сумму $total_sum была расчитана скидка на сумму превышающую сумму заказа, что недопустимо. Заказ будет оформлен без скидок";

			// Пишем в log файл ошибку с информацией о том, что скидка больше суммы заказа
			$EventsJournal = new EventsJournal();
			$EventsJournal->log_access(USER_NONE, $message, 4);
		}

		$total_sum_without_discount = $price;
		$price = $price - $discount;

		// Текст купона
		if (!empty ($shop_coupon_text))
		{
			$xmlData .= '<coupon_text>' . str_for_xml($shop_coupon_text) . '</coupon_text>' . "\n";
		}

		// В цикле выбираем способы доставки для данного типа
		for ($i = 0; $i < $count_type_of_delivery; $i++)
		{
			$row_type_of_delivery = mysql_fetch_assoc($result_type_of_delivery);
			$type_of_delivery_id = $row_type_of_delivery['shop_type_of_delivery_id'];

			// Выбор способа доставки для типа
			$row = $this->GetTypeOfDeliveryForOrder($type_of_delivery_id, $country, $location, $city, $city_area, $weight, $price);
			if ($row)
			{
				// Наращиваем кол-во способов доставок
				$count_cond_of_delivery++;

				// Условия доставки
				$xmlData .= '<type_of_delivery id="' . str_for_xml($row['shop_type_of_delivery_id']) . '">' . "\n";
				$xmlData .= '<shop_cond_of_delivery_id>' . str_for_xml($row['shop_cond_of_delivery_id']) . '</shop_cond_of_delivery_id>' . "\n";
				$xmlData .= '<shop_cond_of_delivery_name>' . str_for_xml($row['shop_cond_of_delivery_name']) . '</shop_cond_of_delivery_name>' . "\n";
				$xmlData .= '<shop_cond_of_delivery_weight_from>' . str_for_xml($row['shop_cond_of_delivery_weight_from']) . '</shop_cond_of_delivery_weight_from>' . "\n";
				$xmlData .= '<shop_cond_of_delivery_weight_to>' . str_for_xml($row['shop_cond_of_delivery_weight_to']) . '</shop_cond_of_delivery_weight_to>' . "\n";
				$xmlData .= '<shop_cond_of_delivery_price_from>' . str_for_xml($row['shop_cond_of_delivery_price_from']) . '</shop_cond_of_delivery_price_from>' . "\n";
				$xmlData .= '<shop_cond_of_delivery_price_to>' . str_for_xml($row['shop_cond_of_delivery_price_to']) . '</shop_cond_of_delivery_price_to>' . "\n";
				$xmlData .= '<shop_cond_of_delivery_description>' . str_for_xml($row['shop_cond_of_delivery_description']) . '</shop_cond_of_delivery_description>' . "\n";

				// Остальные данные о типе доставки
				$xmlData .= '<name>' . str_for_xml($row['shop_type_of_delivery_name']) . '</name>' . "\n";
				$xmlData .= '<description>' . str_for_xml($row['shop_type_of_delivery_description']) . '</description>' . "\n";
				$xmlData .= '<image>' . str_for_xml($row['shop_type_of_delivery_image']) . '</image>' . "\n";

				// Получаем данные о магазине
				$shop_row = $this->GetShop($row['shop_shops_id']);

				// идентификатор валюты магазина
				$shop_currency_id = Core_Type_Conversion::toInt($shop_row['shop_currency_id']);

				$aPrice = $this->GetPriceForCondOfDelivery($row['shop_cond_of_delivery_id']);

				$coefficient = $this->GetCurrencyCoefficientToShopCurrency($row['shop_currency_id'], $shop_currency_id);

				$price_delivery = $this->Round($aPrice['price_tax'] * $coefficient);

				$xmlData .= '<cart_sum>' . $this->Round($price) . '</cart_sum>' . "\n";
				$xmlData .= '<cart_sum_without_discount>' . $this->Round($total_sum_without_discount) . '</cart_sum_without_discount>' . "\n";
				$xmlData .= '<price>' . $this->Round($price_delivery) . '</price>' . "\n";

				//$xmlData .= '<currency>' . str_for_xml($currency) . '</currency>' . "\n";

				$total = $price + $price_delivery;
				$xmlData .= '<total>' . $this->Round($total) . '</total>' . "\n";
				$xmlData .= '</type_of_delivery>' . "\n";
			}
		}

		// Количество полученных способов доставок
		$xmlData .= '<count_cond_of_delivery>' . intval($count_cond_of_delivery) . '</count_cond_of_delivery>' . "\n";

		$xmlData .= '</types_of_delivery>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Получение цены доставки с учетом указанного налога
	 *
	 * @param int $shop_cond_of_delivery_id идентификатор условия доставки
	 * @return array возвращает массив значений цен для данного пользователя
	 * - $price['price'] цена с учетом валюты
	 * - $price['price_tax'] цена с учетом налога
	 */
	function GetPriceForCondOfDelivery($shop_cond_of_delivery_id)
	{
		$row = $this->GetCondOfDelivery($shop_cond_of_delivery_id);

		// Товар не выбран, возвращем ложь
		if (!$row)
		{
			return FALSE;
		}

		// Умножаем цену товара на курс валюты в базовой валюте
		$price['price'] = $row['shop_cond_of_delivery_price'];

		// Выбираем информацию о налогах
		if ($row['shop_tax_id'])
		{
			// Извлекаем информацию о налоге
			$tax = $this->GetTax($row['shop_tax_id']);

			// Если он не входит в цену
			$price['price_tax'] = $tax && $tax['shop_tax_is_in_price'] == 0
				// То считаем цену с налогом
				? Core_Type_Conversion::toFloat($tax['shop_tax_rate']) / 100 * $price['price'] + $price['price']
				: $price['price'];
		}
		else
		{
			$price['price_tax'] = $price['price'];
		}

		// Округляем значения
		// Переводим с научной нотации 1Е+10 в десятичную
		$price['price'] = $this->Round($price['price']);
		$price['price_tax'] = $this->Round($price['price_tax']);
		return $price;
	}

	/**
	 * Получение всех активных платежных систем
	 * @param int $shop_shops_id идентификатор идентификатор магазина (по умолчанию равен FALSE)
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $shop->GetAllSystemOfPay();
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource возвращает результат выборки
	 */
	function GetAllSystemOfPay($shop_shops_id = FALSE)
	{
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_system_of_pay_id'),
				array('shop_currency_id', 'shop_currency_id'),
				array('shop_id', 'shop_shops_id'),
				array('name', 'shop_system_of_pay_name'),
				array('description', 'shop_system_of_pay_description'),
				array('active', 'shop_system_of_pay_is_active'),
				array('user_id', 'users_id'),
				array('sorting', 'shop_system_of_pay_order')
			)
			->from('shop_payment_systems')
			->where('active', '=', 1)
			->where('deleted', '=', 0)
			->orderBy('shop_system_of_pay_order');

		if ($shop_shops_id !== FALSE)
		{
			$queryBuilder->where('shop_id', '=', $shop_shops_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Построение XML для платежных систем
	 *
	 * @param string $xsl_name имя XSL шаблона
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * @param array $param массив дополнительных  параметров
  	 * - $param['shop_shops_id'] int Идентификатор магазина для выбора платежных систем
	 * - $param['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	* <code>
	* <?php
	* $shop = new shop();
	*
	* $xsl_name = 'МагазинПлатежнаяСистема';
	*
	* $shop->ShowSystemOfPay($xsl_name);
	*
	* ?>
	* </code>
	*/
	function ShowSystemOfPay($xsl_name, $external_propertys = array(), $param = array())
	{
		$xsl_name = Core_Type_Conversion::toStr($xsl_name);

		$shop_shops_id = isset($param['shop_shops_id'])
			? intval($param['shop_shops_id'])
			: FALSE;

		// Записываем в сессию информацию о способе доставки
		$_SESSION['cond_of_delivery'] = Core_Type_Conversion::toInt($external_propertys['cond_of_delivery']);

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<list>' . "\n";

		if (isset($param['external_xml']))
		{
			$xmlData .= $param['external_xml'];
		}

		// Вносим в XML дополнительные теги из массива дополнительных параметров
		$ExternalXml = new ExternalXml;
		$xmlData .= $ExternalXml->GenXml($external_propertys);
		$xmlData .= $this->GenXmlForSystemOfPays($shop_shops_id);
		$xmlData .= '</list>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Генерация XML для списка всех платежных систем.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $xmlData = $shop->GenXmlForSystemOfPays();
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 *
	 * ?>
	 * </code>
	 * @return string
	 */
	function GenXmlForSystemOfPays($shop_shops_id = FALSE)
	{
		$result = $this->GetAllSystemOfPay($shop_shops_id);

		$count = $result
			? mysql_num_rows($result)
			: 0;

		// Запоминаем кол-во платежных систем
		$xmlData = '<count_system_of_pay>' . intval($count) . '</count_system_of_pay>' . "\n";

		// В цикле заполняем xml для платежных систем
		for ($i = 0; $i < $count; $i++)
		{
			$row = mysql_fetch_assoc($result);
			$xmlData .= '<system_of_pay id="' . str_for_xml($row['shop_system_of_pay_id']) . '">' . "\n";
			$xmlData .= '<name>' . str_for_xml($row['shop_system_of_pay_name']) . '</name>' . "\n";
			$xmlData .= '<description>' . str_for_xml($row['shop_system_of_pay_description']) . '</description>' . "\n";
			$xmlData .= '</system_of_pay>' . "\n";
		}

		return $xmlData;
	}

	/**
	 * Cписок групп магазина
	 * @param $shop_id int идентификатор магазина
	 * @param $shop_groups_parent_id int идентификатор родительской группы, по умолчанию не задан.
	 * При задании идентификатора родительской группы выбираются только непосредственные потомки указанного родителя.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $resource = $shop->GetGroups($shop_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return $resource
	 */
	function GetGroups($shop_id, $shop_groups_parent_id = FALSE)
	{
		$shop_id = intval($shop_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_groups_id'),
				array('shop_id', 'shop_shops_id'),
				array('parent_id', 'shop_groups_parent_id'),
				array('name', 'shop_groups_name'),
				array('description', 'shop_groups_description'),
				array('image_large', 'shop_groups_image'),
				array('image_small', 'shop_groups_small_image'),
				array('sorting', 'shop_groups_order'),
				array('indexing', 'shop_groups_indexation'),
				array('active', 'shop_groups_activity'),
				array('siteuser_group_id', 'shop_groups_access'),
				array('path', 'shop_groups_path'),
				array('seo_title', 'shop_groups_seo_title'),
				array('seo_description', 'shop_groups_seo_description'),
				array('seo_keywords', 'shop_groups_seo_keywords'),
				array('user_id', 'users_id'),
				array('image_large_width', 'shop_groups_big_image_width'),
				array('image_large_height', 'shop_groups_big_image_height'),
				array('image_small_width', 'shop_groups_small_image_width'),
				array('image_small_height', 'shop_groups_small_image_height'),
				array('guid', 'shop_groups_cml_id'),
				array('items_count', 'count_items'),
				array('items_total_count', 'count_all_items'),
				array('subgroups_count', 'count_groups'),
				array('subgroups_total_count', 'count_all_groups')
			)
			->from('shop_groups')
			->where('deleted', '=', 0);

		if ($shop_groups_parent_id !== FALSE)
		{
			$shop_groups_parent_id = intval($shop_groups_parent_id);
			$queryBuilder->where('parent_id', '=', $shop_groups_parent_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Показ магазина (товаров и групп).
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param string $xsl_name имя XSL-шаблона
	 * @param array $param ассоциативный массив параметров
	 * - $param['current_group_id'] mixed идентификатор раздела магазина или массив идентификаторов, товары которого отображаются, если $param['current_group_id'] = false, то отображаются элементы всех групп
	 * - $param['shop_groups_parent_id'] идентификатор или массив идентификаторов родительской группы, влияющее на ограничние выборки групп магазина
	 * - $param['items_begin'] номер товара в выборке, с которого начинать отображение товаров магазина
	 * - $param['items_on_page'] число товаров, отображаемых на странице
	 * - $param['items_field_order'] поле сортировки товаров каталога, при сортировке по средней оценке товара указывается поле 'shop_comment_grade' (начиная с версии 5.1.2)
	 * - $param['items_order'] направление сортировки ('Asc' - по возрастанию, 'Desc' - по убыванию, 'Rand' - произвольный порядок)
	 * - $param['user_id'] идентификатор пользователя
	 * - $param['group_field_order'] поле сортировки группы
	 * - $param['group_order'] направление сортировки группы ('Asc' - по возрастанию, 'Desc' - по убыванию, 'Rand' - произвольный порядок)
	 * - $param['dec_reques_number'] использовать режим снижения количесва запросов, актуален для больших выборок
	 * - $param['NotIn'] идентификаторы элементов, которые необходимо исключить из результатов
	 * - $param['select_discount'] массив ID скидок, с учетом которых должны выбираться товары из каталога
	 * - $param['cache'] разрешение кэширования, по умолчанию true
	 * - $param['select'] массив массивов $element, каждый из которых задает дополнительные условия отбора товаров
	 * - $param['show_text'] параметр, указывающий включать в XML текст товара или нет, по умолчанию равен true
	 * - $param['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	 * - $param['xml_show_all_producers'] отображать всех производителей магазина
	 * - $param['xml_show_producers'] отображать производителей магазина, по умолчанию true
	 * - $param['xml_show_all_sellers'] отображать всех продавцов магазина
	 * - $param['xml_show_tying_products'] разрешает указание в XML сопутствующих товаров, по умолчанию true
	 * - $param['xml_show_modification'] разрешает указание в XML модификаций товаров, по умолчанию true
	 * - $param['xml_show_group_property'] разрешает указание в XML значений свойств групп магазина, по умолчанию true
	 * - $param['xml_show_group_property_id'] массив идентификаторов дополнительных свойств групп для отображения в XML. Если не не передано - выводятся все свойства
	 * - $param['xml_show_item_property'] разрешает указание в XML значений свойств товаров магазина, по умолчанию true
	 * - $param['xml_show_item_property_id'] массив идентификаторов дополнительных свойств товара для отображения в XML. Если не не передано - выводятся все свойства
	 * - $param['xml_show_tags'] разрешает генерацию в XML облака тегов магазина, по умолчанию false
	 * - $param['xml_show_group_id'] массив идентификаторов групп для отображения в XML. Если не не передано - выводятся все группы
	 * - $param['xml_show_items_property_dir'] разрешает генерацию в XML групп свойств товаров, по умолчанию true
	 * - $param['xml_show_group_type'] тип генерации XML для групп, может принимать значения (по умолчанию 'tree'):
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>all - все группы всех уровней;
	 * <li>current - только непосредственные подгруппы текущей группы;
	 * <li>tree - группы, находящиеся от текущей выше и ниже по дереву;
	 * <li>none - не выбирать группы.
	 * </ul>
	 * </li>
	 * </ul>
	 * <br />
	 * - $element['type'] определяет, является ли поле основным свойством товара или дополнительным (0 - основное, 1 - дополнительное)
	 * - $element['prefix'] префикс - строка, размещаемая перед условием
	 * - $element['name'] имя поля для основного свойства, если свойство дополнительное, то не указывается
	 * - $element['property_id'] идентификатор дополнительногого свойства
	 * - $element['if'] строка, содержащая условный оператор
	 * - $elemenr['value'] значение поля (или параметра)
	 * - $element['sufix'] суффикс - строка, размещаемая после условия
	 * <br />
	 * <br />Например:
	 * <code>$element = array();
	 * $element['type']=1; // 0 - основное св-во, 1 - дополнительное
	 * $element['prefix'] = 'and'; // префикс
	 * $element['property_id'] = 26; // ID дополнительного св-ва, указывается если тип = 1
	 * $element['if'] = '='; // Условие
	 * $element['value'] = '10';
	 * $element['sufix'] = '';
	 * $param['select'][]=$element; // Указываем очередное ограничение, введенное выше
	 *
	 * $element = array();
	 * $element['type']=1; // 0 - основное св-во, 1 - дополнительное
	 * $element['prefix'] = 'and'; // префикс
	 * $element['property_id'] = 28; // ID дополнительного св-ва, указывается если тип = 1
	 * $element['if'] = '='; // Условие
	 * $element['value'] = 1;
	 * $element['sufix'] = '';
	 * </code>
	 * - $param['select'][]=$element; // Указываем очередное ограничение, введенное выше
	 * - $param['show_catalog_item_type'] array массив типов товаров, которые должны отображаться.
	 * Может содержать следующие элементы:
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>active - активные элементы (внесен по умолчанию, если $param['show_catalog_item_type'] не задан;
	 * <li>inactive - неактивные элементы;
	 * <li>putend_date - элементы, у которых значение поля putend_date меньше текущей даты;
	 * <li>putoff_date - элементы, у которых значение поля putoff_date превышает текущую дату;
	 * </ul>
	 * </li>
	 * </ul>
	 *
	 * $param['select_groups'] массив ($element) с дополнительными параметрами для задания дополнительных условий отбора групп магазина
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>$element['type'] определяет, является ли поле основным свойством группы магазина или дополнительным (0 - основное, 1 - дополнительное)
	 * <li>$element['prefix'] префикс - строка, размещаемая перед условием
	 * <li>$element['name'] имя поля для основного свойства, если свойство дополнительное, то не указывается
	 * <li>$element['property_id'] идентификатор дополнительногого свойства групп магазина
	 * <li>$element['if'] строка, содержащая условный оператор
	 * <li>$element['value'] значение поля (или параметра)
	 * <li>$element['sufix'] суффикс - строка, размещаемая после условия
	 * </ul>
	 * </li>
	 * </ul>
	 * - $param['TagsOrder'] параметр, определяющий порядок сортировки тегов. Принимаемые значения: ASC - по возрастанию (по умолчанию), DESC - по убыванию
	 * - $param['TagsOrderField'] поле сортировки тегов, если случайная сортировка, то записать RAND(). по умолчанию теги сортируются по названию.
	 * - $param['sql_external_select'] параметр, задающий список дополнительных полей в оператор SELECT выборки товаров
	 *
	 * Пример ограничения выборки групп:
	 * <code>
	 * <?php
	 * $shop= & singleton('shop');
	 *
	 * $param = array();
	 * // Заполняем первое условие
	 * $element['type'] = 0;
	 * $element['prefix'] = ' and ';
	 * $element['name'] = 'shop_groups_order';
	 * $element['if'] = '>';
	 * $element['value'] = '30';
	 * $element['sufix'] = '';
	 * $param['select_groups'][] = $element;
	 *
	 * // Заполняем второе условие, в данном случае по значению дополнительного св-ва. Используется, если условие не должно содержать диапазона допустимых значений доп. свойства.
	 * $element['type'] = 1;
	 * $element['prefix'] = 'and';
	 * $element['property_id'] = 7;
	 * $element['if'] = '=';
	 * $element['value'] = '10';
	 * $element['sufix'] = '';
	 * $param['select_groups'][] = $element;
	 *
	 * // Если во втором условии используется доп. свойство, содержащее числовые значения и в условии необходимо указать
	 * // диапазон значений, то вместо приведенной выше конструкции в качестве второго условия необходимо использовать следующий код
	 * // Ограниение для дополнительного свойства содержащего числовые значения целого и/или вещественного типа
	 * $element['property_id'] = 7;
	 * $element['type'] = 1;
	 * $element['prefix'] = ' AND';
	 * $element['if'] = '!=';
	 * // Указываем идентификатор магазина, для которого производится отбор товаров
	 * $element['value'] = '';
	 * // Указываем идентификатор дополнительного свойства, по значениям которого производиться отбор записей,
	 * // а также нижняя границы допустимых значений дополнительного свойства
	 * $element['sufix'] = " AND shop_list_of_properties_table.shop_list_of_properties_id='". $element['property_id'] ."' AND (REPLACE(shop_properties_items_table.shop_properties_items_value, ',', '.') + 0.0) >= 3 AND (REPLACE(shop_properties_items_table.shop_properties_items_value, ',', '.') + 0.0) <= 40.5";
	 * $param['select'][] = $element;
	 *
	 * $param['current_group_id'] = 0;
	 * $param['items_begin'] = 0;
	 * $param['items_on_page'] = 10;
	 * // Выводим элементы
	 * $shop->ShowShop(1, 'МагазинКаталогТоваров',  $param);
	 * ?>
	 * </code>
	 *
	 *  Пример использования ограничения по 2-м дополнительным свойствам товара:
	 * <code>
	 * <?php
	 * $shop = & singleton('shop');
	 * $param = array()
	 *
	 * // Ограничение по цвету
	 * $element['type'] = 1; // 0 - основное св-во, 1 - дополнительное
	 * $element['property_id'] = 159;
	 * $element['prefix'] = ' AND ('; // префикс
	 * $element['if'] = '='; // Условие
	 * $element['value'] = '10';  //
	 * $element['sufix'] = '';
	 * $param['select'][] = $element;

	 * // Ограничение по флажку
	 * $element['type'] = 1; // 0 - основное св-во, 1 - дополнительное
	 * $element['property_id'] = 183;
	 * $element['prefix'] = ' OR '; // префикс
	 * $element['if'] = '='; // Условие
	 * $element['value'] = '1';  //
	 * $element['sufix'] = ' ) ';
	 * $param['select'][] = $element;
	 *
	 * $param['sql_group_by'] = "GROUP BY shop_items.shop_items_catalog_item_id ";
	 * $param['sql_having'] = "HAVING COUNT(shop_properties_items_table.shop_properties_items_id) = 2";
	 *
	 * $shop->ShowShop(1,'МагазинКаталогТоваров', $param);
	 * ?>
	 * </code>
	 *
	 * Пример использования сортировки товара по значению дополнительнго свойства:
	 * <code>
	 * <?php
	 * $shop = & singleton('shop');
	 *
	 * $param = array();
	 * $external_propertys = array();
	 *
	 * $param['current_group_id'] = false;
	 * $param['xml_show_group_type'] = 'all';
	 * $param['items_on_page'] = 10;
	 *  // Ограничение по дате
	 * $element['type'] = 1; // 0 - основное св-во, 1 - дополнительное
	 * $element['property_id'] = 194;
	 * $element['prefix'] = ' AND '; // префикс
	 * $element['if'] = '!='; // Условие
	 * $element['value'] = "''";  //
	 * $element['sufix'] = '';
	 * $param['select'][] = $element;

	 * // Сортируем как строку
	 * //$param['items_field_order'] = ' shop_properties_items_table.shop_properties_items_value';

	 * // Сортируем как число
	 * //$param['items_field_order'] = ' CONVERT(shop_properties_items_table.shop_properties_items_value, UNSIGNED)';

	 * // Сортируем как ДатуВремя
	 * //$param['items_field_order'] = " CONVERT(CONCAT(SUBSTRING(shop_properties_items_value, 7, 4), CHAR(45), SUBSTRING(shop_properties_items_value, 4, 2), CHAR(45), SUBSTRING(shop_properties_items_value, 1, 2), CHAR(32), SUBSTRING(shop_properties_items_value, 12, 8)) , DATETIME)";

	 * // Сортируем как Дату
	 * $param['items_field_order'] = " CONVERT(CONCAT(SUBSTRING(shop_properties_items_value, 7, 4), CHAR(45), SUBSTRING(shop_properties_items_value, 4, 2), CHAR(45), SUBSTRING(shop_properties_items_value, 1, 2)),  DATE)";

	 * $param['items_order'] = 'ASC';
	 *
	 * $shop->ShowShop(1,'МагазинКаталогТоваров', $param);
	 * ?>
	 * </code>
	 *
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $xsl_name = 'МагазинКаталогТоваров';
	 *
	 * $shop->ShowShop($shop_id, $xsl_name);
	 * ?>
	 * </code>
	 */
	function ShowShop($shop_id, $xsl_name, $param = array(), $external_propertys = array())
	{
		$shop_id = intval($shop_id);
		$xsl_name = Core_Type_Conversion::toStr($xsl_name);
		$param = Core_Type_Conversion::toArray($param);

		// по умолчанию кэширование - включено
		if (!isset($param['cache']))
		{
			$param['cache'] = TRUE;
		}

		$external_propertys = Core_Type_Conversion::toArray($external_propertys);

		/* Если Free версия - проверяем, чтобы количество магазинов для сайта
		 было не более 1, иначе отображаем сообщение об ошибке*/
		if (defined('INTEGRATION') && INTEGRATION == 0)
		{
			$shop_resource = $this->GetAllShops(CURRENT_SITE);
			if ($shop_resource)
			{
				if (mysql_num_rows($shop_resource) > 2)
				{
					show_error_message('<strong>Внимание! Редакция HostCMS.Халява не поддерживает более двух Интернет-магазинов для сайта.</strong>');
					return FALSE;
				}
			}
		}

		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		!isset($param['xml_show_producers']) && $param['xml_show_producers'] = TRUE;
		!isset($param['xml_show_items_property_dir']) && $param['xml_show_items_property_dir'] = TRUE;
		!isset($param['xml_show_tying_products']) && $param['xml_show_tying_products'] = TRUE;
		!isset($param['xml_show_tags']) && $param['xml_show_tags'] = FALSE;
		!isset($param['show_catalog_item_type']) && $param['show_catalog_item_type'] = array('active');
		!isset($param['xml_show_group_property_id']) && $param['xml_show_group_property_id'] = array();
		!isset($param['xml_show_group_property']) && $param['xml_show_group_property'] = TRUE;
		!isset($param['xml_show_item_property']) && $param['xml_show_item_property'] = TRUE;

		if (!isset($param['user_id']))
		{
			if (class_exists("SiteUsers"))
			{
				$SiteUsers = & singleton('SiteUsers');
				$param['user_id'] = $SiteUsers->GetCurrentSiteUser();
			}
			else
			{
				$param['user_id'] = 0;
			}
		}

		// по умолчанию выбираем данные по группам в виде дерева до текущей
		if (!isset($param['xml_show_group_type']))
		{
			$param['xml_show_group_type'] = 'tree';
		}

		// Текущая группа
		if (isset($param['current_group_id']))
		{
			if ($param['current_group_id'] !== FALSE)
			{
				// Если родительская группа передана массивом хотя бы с одним элементом
				if (is_array($param['current_group_id']) && count($param['current_group_id']) > 0)
				{
					$current_group_id = 0;
					// В запросы данные будут идти из $param['current_group_id'], а при передаче массива текущей группой является 0
				}
				else
				{
					$current_group_id = Core_Type_Conversion::toInt($param['current_group_id']);
				}
			}
			else
			{
				$current_group_id = FALSE;
			}
		}
		else
		{
			$current_group_id = $param['current_group_id'] = 0;
		}

		$kernel = & singleton('kernel');
		if ($kernel->AllowShowPanel())
		{
			$param_panel = array();

			$group_row = $this->GetGroup($current_group_id);
			$shop_group_id = $group_row ? $current_group_id : 0;

			// Добавление информации о товаре
			$param_panel[0]['image_path'] = "/hostcmsfiles/images/page_add.gif";
			$param_panel[0]['alt'] = "Добавить информацию о товаре";

			$sPath = '/admin/shop/item/index.php';
			$sAdditional = "hostcms[action]=edit&shop_id={$shop_id}&shop_group_id={$shop_group_id}&hostcms[checked][1][0]=1";

			$param_panel[0]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
			$param_panel[0]['href'] = "{$sPath}?{$sAdditional}";

			// Добавление информации о группе товаров
			$param_panel[1]['image_path'] = "/hostcmsfiles/images/folder_add.gif";
			$param_panel[1]['alt'] = "Добавить информацию о группе товаров";

			$sPath = '/admin/shop/item/index.php';
			$sAdditional = "hostcms[action]=edit&shop_id={$shop_id}&shop_group_id={$shop_group_id}&hostcms[checked][0][0]=1";

			$param_panel[1]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
			$param_panel[1]['href'] = "{$sPath}?{$sAdditional}";

			if ($group_row = $this->GetGroup($current_group_id))
			{
				// Редактирование информации о группе товаров
				$param_panel[2]['image_path'] = "/hostcmsfiles/images/folder_edit.gif";
				$param_panel[2]['alt'] = "Редактировать информацию о группе товаров";

				$sPath = '/admin/shop/item/index.php';
				$sAdditional = "hostcms[action]=edit&shop_id={$shop_id}&shop_group_id={$shop_group_id}&hostcms[checked][0][{$current_group_id}]=1";

				$param_panel[2]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
				$param_panel[2]['href'] = "{$sPath}?{$sAdditional}";
			}

			// Редактирование магазина
			$param_panel[3]['image_path'] = "/hostcmsfiles/images/shop_edit.gif";
			$param_panel[3]['alt'] = "Редактировать магазин";

			$sPath = '/admin/shop/index.php';
			$sAdditional = "hostcms[action]=edit&hostcms[checked][0][{$shop_id}]=1";

			$param_panel[3]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
			$param_panel[3]['href'] = "{$sPath}?{$sAdditional}";

			// Выводим панель
			echo $kernel->ShowFlyPanel($param_panel);
		}

		// Проверяем, установлен ли модуль кэширования
		if (class_exists('Cache') && $param['cache'])
		{
			$cache = & singleton('Cache');

			$cache_element_name = 'ShowShop_' . $shop_id . '_' . $xsl_name . '_' . serialize($param) . '_' . serialize($external_propertys) . '_' . serialize($this->GetCookie('SHOPCOMPARE')) . '_' . $site_user_id;

			$cache_name = 'SHOW_SHOP_XML';
			if (($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name)) && $in_cache)
			{
				echo $in_cache['value'];
				return TRUE;
			}
		}

		// С какого элемента необходимо выводить
		$items_begin = Core_Type_Conversion::toInt($param['items_begin']);

		$param_shop_access = array();
		$param_shop_access['site_users_id'] = $site_user_id;
		$param_shop_access['shop_group_id'] = 0;
		$param_shop_access['shop_id'] = $shop_id;


		// Проверяем доступен ли данный магазин текущему зарегистрированному пользователю
		if ($this->IssetAccessForShopGroup($param_shop_access))
		{
			// Получаем данные о магазине
			$row = $this->GetShop($shop_id);

			// Количество на страницу
			if (isset($param['items_on_page']))
			{
				$items_on_page = Core_Type_Conversion::toInt($param['items_on_page']);
			}
			else
			{
				$items_on_page = $row['shop_items_on_page'];
				$param['items_on_page'] = $items_on_page;
			}

			// Проверяем выбрали ли мы запись
			if ($row)
			{
				// Начинаем формирование xml-а для вывода групп и товаров
				$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

				$xmlData .= '<shop id="' . $shop_id . '" current_group_id="' . Core_Type_Conversion::toInt($current_group_id) . '">' . "\n";

				/* Вносим внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа*/
				if (isset($param['external_xml']))
				{
					$xmlData .= $param['external_xml'];
				}

				// Вносим в XML дополнительные теги из массива дополнительных параметров
				$ExternalXml = new ExternalXml();
				$xmlData .= $ExternalXml->GenXml($external_propertys);

				// Генерируем общие данные о магазине
				$xmlData .= $this->GenXml4Shop($shop_id, $row);

				$param_group_access = array();
				$param_group_access['site_users_id'] = $site_user_id;
				$param_group_access['shop_group_id'] = $current_group_id;
				$param_group_access['shop_id'] = $shop_id;

				// Текущий зарегистрированный пользователь имеет доступ к группе товаров
				if ($this->IssetAccessForShopGroup($param_group_access))
				{
					if (class_exists('Tag') && $param['xml_show_tags'])
					{
						// Облако тегов
						$xmlData .= $this->GetXml4Tags($shop_id, $param);
					}

					// Выбираем группы
					// $count = count($this->MasGroup);

					$param['parent_group_id'] = 0;

					// Если отображать группы
					if ($param['xml_show_group_type'] != 'none')
					{
						/* Если модуль кэширования подключен*/
						/*if (class_exists('Cache') && $param['cache'])
						 {
						 $kernel = & singleton('kernel');

						 $cache_element_name_xml = $shop_id . "_" . $kernel->implode_array($param, '_');

						 $cache = & singleton('Cache');

						 $cache_name_xml = 'SHOP_GEN_GROUP_XML_TREE';

						 if (($in_cache = $cache->GetCacheContent($cache_element_name_xml, $cache_name_xml)) && $in_cache)
						 {
						 $GroupXmlTree = $in_cache['value'];
						 }
						 }*/

						// В кэше данных не было
						if (!isset($GroupXmlTree))
						{
							// Если стоит передавать свойства в группу
							// СВОЙСТВА ЗАПОЛЯНЕМ ТОЛЬКО ЕСЛИ НЕТ УЖЕ СГЕНЕРИРВОАННОГО XML
							if ($param['xml_show_group_property'])
							{
								// Заполняем значения свойств всех групп магазина
								// 000051686
								$this->FillMemCachePropertysGroup($shop_id, $param['xml_show_group_property_id']);
							}

							// Выбираем только активные группы
							$param['groups_activity'] = 1;

							// add 06-05-10
							/*if ($param['xml_show_group_type'] != 'all' && !isset($param['shop_groups_parent_id']))
							{
								// Ограничиваем выборку в FillMasGroup() только по родителю
								//$param['shop_groups_parent_id'] = $current_group_id;
							}*/

							// Заполняем массив групп
							$this->FillMasGroup($shop_id, $param);

							if ($param['xml_show_group_type'] == 'current')
							{
								// Если в XML передаются только текущие группы - дерево групп строим от текущей группы, а не от корня.
								$param['parent_group_id'] = intval($current_group_id);
							}

							//$GroupXmlTree = $this->GetGroupsXmlTree($shop_id, $param);
							$xmlData .= $this->GetGroupsXmlTree($shop_id, $param);

							/* Если модуль кэширования*/
							/*if (class_exists('Cache') && $param['cache'])
							 {
							 $cache->Insert($cache_element_name_xml, $GroupXmlTree, $cache_name_xml);
							 }*/
						}
					}

					// Добавляем в XML данные о продавцах
					$xmlData .= $this->GenXml4Sallers($shop_id, $param);

					// Добавляем данные о производителях
					//if (isset($param['xml_show_all_producers'])
					//&& $param['xml_show_all_producers'])
					if ((isset($param['xml_show_all_producers']) && $param['xml_show_all_producers']) || !isset($param['xml_show_all_producers']))
					{
						$param['shop_id'] = $shop_id;
						$param['show_shop_xml'] = FALSE;
						$xmlData .= $this->GenXmlProducerList($param);
					}

					// Добавляем в XML данные о налогах
					$xmlData .= $this->GenXml4Taxes();

					// Обрабатываем группы дополнительных свойств товаров, очищаем кэши директорий для элементов для магазина
					// Формируем XML-данные для групп дополнительных свойств товара
					$dir_prop_array = $this->GetAllPropertiesItemsDirForShop($row['shop_shops_id']);

					$this->cache_propertys_items_dir_tree[$shop_id] = array();

					if (mysql_num_rows($dir_prop_array) > 0)
					{
						while ($dir_prop_row = mysql_fetch_assoc($dir_prop_array))
						{
							$this->cache_propertys_items_dir[$dir_prop_row['shop_properties_items_dir_id']] = $dir_prop_row;
							$this->cache_propertys_items_dir_tree[$shop_id][$dir_prop_row['shop_properties_items_dir_parent_id']][] = $dir_prop_row['shop_properties_items_dir_id'];
						}
					}

					// Временный буфер
					$this->buffer = '';

					if ($param['xml_show_items_property_dir'])
					{
						// Вызов функции генерацци XML для групп дополнительных свойств
						$this->GenXmlForItemsPropertyDir($row['shop_shops_id']);

						$xmlData .= $this->buffer;

						$this->buffer = '';
					}

					// Формируем XML для групп дополнительных свойств групп товаров
					$all_groups_property_for_groups = $this->GetAllPropertiesGroupsDirForShop($row['shop_shops_id']);

					$this->cache_propertys_groups_dir_tree[$row['shop_shops_id']] = array();

					if ($all_groups_property_for_groups && mysql_num_rows($all_groups_property_for_groups) > 0)
					{
						while ($all_groups_property_for_groups_row = mysql_fetch_assoc($all_groups_property_for_groups))
						{
							$this->cache_propertys_groups_dir[$all_groups_property_for_groups_row['shop_properties_groups_dir_id']] = $all_groups_property_for_groups_row;
							$this->cache_propertys_groups_dir_tree[$row['shop_shops_id']][$all_groups_property_for_groups_row['shop_properties_groups_dir_parent_id']][] = $all_groups_property_for_groups_row['shop_properties_groups_dir_id'];
						}
					}

					// Вызов функции генерацци XML для групп дополнительных свойств
					$this->GenXmlForGroupsPropertyDir($row['shop_shops_id']);

					$xmlData .= $this->buffer;

					$this->buffer = '';

					if ($param['xml_show_item_property'])
					{
						// Добавляем данные о дополнительных свойствах, доступных группе
						$xmlData .= $this->GenXml4Properties($shop_id, $current_group_id, $param);
					}

					// Добавляем данные о сравнении товаров
					$xmlData .= $this->GenXml4CompareItems();

					if (class_exists('Tag'))
					{
						$oTag = & singleton('Tag');

						// Добавляем информацию о выбранных тегах
						if (isset($param['tags']) && count($param['tags']) > 0)
						{
							$xmlData .= "<selected_tags>\n";

							// Приводим к целому числу
							foreach ($param['tags'] as $key => $tag_id)
							{
								$param['tags'][$key] = intval($tag_id);

								// XML для тега
								$tag_xml = $oTag->GenXmlForTag($tag_id);

								if ($tag_xml)
								{
									$xmlData .= $tag_xml;
								}
							}

							$xmlData .= "</selected_tags>\n";
						}
					}

					// Передаем условия выборки активности для товаров
					//$param['shop_items_catalog_is_active'] активность товара (если 2, выбираем и активные и неактивные параметры, если 0 выбираем только неактивные товары, если не передан выбираем только активные товары)
					// Если только активные (без неактивных)
					if (in_array('active', $param['show_catalog_item_type']) && !in_array('inactive', $param['show_catalog_item_type']))
					{
						$param['shop_items_catalog_is_active'] = 1;
					}
					// только неактивные
					elseif (in_array('inactive', $param['show_catalog_item_type']) && !in_array('active', $param['show_catalog_item_type']))
					{
						$param['shop_items_catalog_is_active'] = 0;
					}
					// а иначе выбираем и активные, и неактивные, соответственно в запрос ограничение не идет.
					else
					{
						$param['shop_items_catalog_is_active'] = 2;
					}

					$param['cache_off'] = !Core_Type_Conversion::toBool($param['cache']);

					// Выбираем товар
					$xmlData .= $this->GetItemsXmlTree($shop_id, $current_group_id, $param);
					$xmlData .= '</shop>';

					$xsl = & singleton('xsl');
					$result = $xsl->build($xmlData, $xsl_name);

					echo $result;
				}
				else // Текущий зарегистрированный пользователь не имеет доступ к группе товаров
				{
					$result = FALSE;
				}
			}
			else
			{
				$result = FALSE;
			}
		}
		else // Пользователь не имеет доступа к магазину
		{
			$result = FALSE;
		}

		/* Проверяем, начинали ли мы кэширование*/
		if (isset($param['cache']) && $param['cache'])
		{
			/* Если модуль кэширования*/
			if (class_exists('Cache'))
			{
				$cache->Insert($cache_element_name, $result, $cache_name);
			}
		}
	}

	/**
	 * Генерация XML для облака тегов магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $property массив дополнительных атрибутов
	 * - $property['begin'] начальная позиция отображения тегов (по умолчанию 0)
	 * - $property['count'] количество отображаемых тегов
	 * - $property['TagsOrder'] параметр, определяющий порядок сортировки тегов. Принимаемые значения: ASC - по возрастанию (по умолчанию), DESC - по убыванию
	 * - $property['TagsOrderField'] поле сортировки тегов, если случайная сортировка, то записать RAND(). по умолчанию теги сортируются по названию.
	 * - $property['tags_group_id'] идентификатор или массив идентификаторов групп тегов, из которых необходимо вести отбор тегов
	 * - $property['shop_groups_id'] идентификатор группы магазина, для которой необходимо вести отбор тегов
	 * - $property['NotIn'] строка идентификаторов товаров, исключаемых из выборки тегов
	 * - $property['In'] массив идентификаторов товаров, только для которых выбирать теги
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $property['count'] = 10;
	 *
	 * $xmlData = $shop->GetXml4Tags($shop_shops_id, $property);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function GetXml4Tags($shop_shops_id, $property)
	{
		$shop_shops_id = intval($shop_shops_id);

		// по умолчанию показываем только активные элементы
		if (!isset($property['show_catalog_item_type']))
		{
			$property['show_catalog_item_type'] = array('active');
		}

		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		// Получаем список групп доступа, в которые входит данный пользователь
		$mas_result = $this->GetSiteUsersGroupsForUser($site_user_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('COUNT(tag_shop_items.tag_id)', 'count'),
				array('tags.id', 'tag_id')
			)
			->from('tag_shop_items')
			->leftJoin('shop_items', 'tag_shop_items.shop_item_id', '=', 'shop_items.id')
			->leftJoin('shop_groups', 'shop_items.shop_group_id', '=', 'shop_groups.id',
				array(
					array('AND' => array('shop_groups.siteuser_group_id', 'IN', $mas_result))
				)
			)
			->leftJoin('tags', 'tag_shop_items.tag_id', '=', 'tags.id')
			->where('shop_items.shop_id', '=', $shop_shops_id)
			->where('shop_items.siteuser_group_id', 'IN', $mas_result)
			->where('shop_items.deleted', '=', 0)
			->where('shop_groups.deleted', '=', 0)
			->where('tags.deleted', '=', 0)
			->groupBy('tag_shop_items.tag_id')
			->having('count', '>', 0);


		if (isset($property['tags_group_id']))
		{
			if (is_array($property['tags_group_id']) && count($property['tags_group_id']) > 0)
			{
				$queryBuilder->where('tag_group_id', 'IN', Core_Array::toInt($property['tags_group_id']));
			}
			else
			{
				$queryBuilder->where('tag_group_id', '=', intval($property['tags_group_id']));
			}
		}

		// Ограничение по группе магазина
		if (isset($property['shop_groups_id']))
		{
			$shop_groups_id = intval($property['shop_groups_id']);
			$queryBuilder->where('shop_items.shop_group_id', '=', $shop_groups_id);
		}

		// Определяем ID элементов, которые не надо включать в выдачу
		if (isset($property['NotIn']))
		{
			// Разбиваем переданные параметры и копируем в массив
			$not_in_mass = Core_Array::toInt(explode(',', $property['NotIn']));

			if (count($not_in_mass) > 0)
			{
				// Объединяем элементы массива и включаем в запрос
				$queryBuilder->where('shop_items.id', 'NOT IN', $not_in_mass);
			}
		}

		// Определяем ID элементов, которые не надо включать в выдачу
		if (isset($property['In']) && is_array($property['In']))
		{
			$property['In'] = Core_Array::toInt($property['In']);
			if (count($property['In']) > 0)
			{
				// Объединяем элементы массива и включаем в запрос
				$queryBuilder->where('shop_items.id', 'IN', $property['In']);
			}
		}
		$current_date = date('Y-m-d H:i:s');

		// Если только активные (без неактивных)
		if (in_array('active', $property['show_catalog_item_type']) && !in_array('inactive', $property['show_catalog_item_type']))
		{
			$queryBuilder->where('shop_items.active', '=', 1);
		}
		// только неактивные
		elseif (in_array('inactive', $property['show_catalog_item_type']) && !in_array('active', $property['show_catalog_item_type']))
		{
			$queryBuilder->where('shop_items.active', '=', 0);
		}

		// Если не содержит putend_date - ограничиваем по дате окончания публикации
		if (!in_array('putend_date', $property['show_catalog_item_type']))
		{
			$queryBuilder
				->open()
				->where('end_datetime', '>=', $current_date)
				->setOr()
				->where('end_datetime', '=', '0000-00-00 00:00:00')
				->close();
		}

		// если не содержит putoff_date - ограничиваем по дате начала публикации
		if (!in_array('putoff_date', $property['show_catalog_item_type']))
		{
			$queryBuilder->where('start_datetime', '<=', $current_date);
		}

		if (isset($property['count']))
		{
			$begin = Core_Type_Conversion::toInt($property['begin']);
			$count = Core_Type_Conversion::toInt($property['count']);
			$queryBuilder->limit($begin, $count);
		}

		// Поле сортировки
		$order_field = isset($property['TagsOrderField'])
			? $property['TagsOrderField']
			: 'tags.name';

		$order = 'ASC';

		// Не задана случайная сортировка
		if (strtoupper($order_field) != 'RAND()' && isset($property['TagsOrder']))
		{
			$order = $property['TagsOrder'];
		}
		$queryBuilder->orderBy($order_field, $order);

		$result = $queryBuilder->execute()->asAssoc()->getResult();

		$xmlData = '<tags>' . "\n";

		if (class_exists('Tag')
		// bugfix with mysql_data_seek()
		&& mysql_num_rows($result) > 0)
		{
			$oTag = & singleton('Tag');

			// если есть хоть один элемент
			$aTags = array();
			while ($row = mysql_fetch_assoc($result))
			{
				$aTags[] = $row['tag_id'];
			}
			$oTag->GetTags($aTags);

			mysql_data_seek($result, 0);
			while ($row = mysql_fetch_assoc($result))
			{
				// генерация XML для товаров
				$xmlData .= $oTag->GenXmlForTag($row['tag_id'], FALSE, $row['count']);
			}
		}
		$xmlData .= '</tags>' . "\n";
		return $xmlData;
	}

	/**
	 * Генерация XML для магазина
	 *
	 * @param int $shop_id;
	 * @param array $shop_row;
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $xmlData = $shop->GenXml4Shop($shop_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return string
	 */
	function GenXml4Shop($shop_id, $shop_row = FALSE)
	{
		$shop_id = intval($shop_id);

		// Если не переданы данные о магазине
		if (!is_array($shop_row))
		{
			$shop_row = $this->GetShop($shop_id);
		}

		// Получаем путь к магазину
		$Structure = & singleton('Structure');
		$shop_path = $Structure->GetStructurePath($shop_row['structure_id'], 0);
		if ($shop_path != '/')
		{
			$shop_path = '/' . $shop_path;
		}

		$xmlData = '<name>' . str_for_xml($shop_row['shop_shops_name']) . '</name>' . "\n";
		$xmlData .= '<description>' . str_for_xml($shop_row['shop_shops_description']) . '</description>' . "\n";
		$xmlData .= '<path>' . str_for_xml($shop_path) . '</path>' . "\n";
		$xmlData .= '<site_id>' . str_for_xml($shop_row['site_id']) . '</site_id>' . "\n";
		$xmlData .= '<shop_image_small_max_width>' . $shop_row['shop_image_small_max_width'] .'</shop_image_small_max_width>' . "\n";
		$xmlData .= '<shop_image_big_max_width>' . $shop_row['shop_image_big_max_width'] . '</shop_image_big_max_width>' . "\n";
		$xmlData .= '<shop_image_small_max_height>' . $shop_row['shop_image_small_max_height'] . '</shop_image_small_max_height>' . "\n";
		$xmlData .= '<shop_image_big_max_height>' . $shop_row['shop_image_big_max_height'] . '</shop_image_big_max_height>' . "\n";
		$xmlData .= '<structure_id>' . $shop_row['structure_id'] . '</structure_id>' . "\n";

		// Страна по умолчанию
		$xmlData .= '<shop_country_id>' . $shop_row['shop_country_id'] . '</shop_country_id>' . "\n";

		// Добавляем данные о валюте
		$xmlData .= $this->GenXML4Currency($shop_row['shop_currency_id']);

		// Выбираем все валюты магазина
		$rAllCurrency = $this->GetAllCurrency();

		$xmlData .= '<all_currency>' . "\n";
		while ($aCurrency = mysql_fetch_assoc($rAllCurrency))
		{
			// Добавляем данные о валюте
			$xmlData .= $this->GenXML4Currency($aCurrency['shop_currency_id'], $shop_id);
		}
		$xmlData .= '</all_currency>' . "\n";

		// Тип доставки по умолчанию
		$xmlData .= $this->GenXml4OrderStatus($shop_row['shop_order_status_id']);

		// Единица измерения веса товара
		$xmlData .= $this->GenXml4Mesures($shop_row['shop_mesures_id']);

		$xmlData .= '<shop_shops_send_order_mail_admin>' . str_for_xml($shop_row['shop_shops_send_order_mail_admin']) . '</shop_shops_send_order_mail_admin>' . "\n";
		$xmlData .= '<shop_shops_send_order_mail_user>' . str_for_xml($shop_row['shop_shops_send_order_mail_user']) . '</shop_shops_send_order_mail_user>' . "\n";
		$xmlData .= '<shop_shops_admin_mail>' . str_for_xml($shop_row['shop_shops_admin_mail']) . '</shop_shops_admin_mail>' . "\n";

		// Справочник цен магазина
		// Получаем информацию о ценах для магазина
		$list_of_price_res = $this->GetAllPricesForShop($shop_id);

		if ($list_of_price_res && mysql_num_rows($list_of_price_res) > 0)
		{
			// Справочник цен
			$xmlData .= '<shop_list_of_prices>' . "\n";

			while ($list_of_price_row = mysql_fetch_assoc($list_of_price_res))
			{
				// Формируем XML для цен
				$xmlData .= $this->GenXML4PriceForShop($list_of_price_row['shop_list_of_prices_id'], $list_of_price_row);
			}

			$xmlData .= '</shop_list_of_prices>' . "\n";
		}

		// Необходимо дописать информацию о складах магазина
		$warehouse = & singleton('warehouse');
		$xmlData .= $warehouse->GetWarehousesXml($shop_id);

		return $xmlData;
	}

	/**
	 * Генерация XML для единиц измерения
	 *
	 * @param int $shop_mesures_id
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_mesures_id = 1;
	 *
	 * $xmlData = $shop->GenXml4Mesures($shop_mesures_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return str
	 */
	function GenXml4Mesures($shop_mesures_id)
	{
		$shop_mesures_id = intval($shop_mesures_id);
		$xmlData = '<shop_mesures id="' . $shop_mesures_id . '">' . "\n";
		$shop_mesures_row = $this->GetMesure($shop_mesures_id);
		if ($shop_mesures_row)
		{
			$xmlData .= '<shop_mesures_name>' . str_for_xml($shop_mesures_row['shop_mesures_name']) . '</shop_mesures_name>' . "\n";
			$xmlData .= '<shop_mesures_description>' . str_for_xml($shop_mesures_row['shop_mesures_description']) . '</shop_mesures_description>' . "\n";
		}

		$xmlData .= '</shop_mesures>' . "\n";

		return $xmlData;
	}

	/**
	 * Генерация XML для статуса доставки
	 *
	 * @param int $shop_delivery_id
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_status_id = 1;
	 *
	 * $xmlData = $shop->GenXml4OrderStatus($shop_order_status_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return str
	 */
	function GenXml4OrderStatus($shop_order_status_id)
	{
		$shop_order_status_id = intval($shop_order_status_id);

		$xmlData = '<shop_order_status id="' . $shop_order_status_id . '">' . "\n";

		$shop_order_status_row = $this->GetOrdersStatus($shop_order_status_id);
		if ($shop_order_status_row)
		{
			$xmlData .= '<shop_order_status_name>' . str_for_xml($shop_order_status_row['shop_order_status_name']) . '</shop_order_status_name>' . "\n";
			$xmlData .= '<shop_order_status_description>' . str_for_xml($shop_order_status_row['shop_order_status_description']) . '</shop_order_status_description>' . "\n";
		}

		$xmlData .= '</shop_order_status>' . "\n";

		return $xmlData;
	}

	/**
	 * Генерация XML для указанной валюты
	 *
	 * @param int $shop_currency_id идентификатор валюты
	 * @param int $shop_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_currency_id = 1;
	 *
	 * $xmlData = $shop->GenXML4Currency($shop_currency_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 *
	 * ?>
	 * </code>
	 */
	function GenXML4Currency($shop_currency_id, $shop_id = FALSE)
	{
		$shop_currency_id = intval($shop_currency_id);

		$xmlData = '<shop_currency id="' . $shop_currency_id . '">' . "\n";

		$currency_row = $this->GetCurrency($shop_currency_id);

		if ($currency_row)
		{
			$xmlData .= '<shop_currency_name>' . str_for_xml($currency_row['shop_currency_name']) . '</shop_currency_name>' . "\n";
			$xmlData .= '<shop_currency_international_name>' . str_for_xml($currency_row['shop_currency_international_name']) . '</shop_currency_international_name>' . "\n";
			$xmlData .= '<shop_currency_value_in_basic_currency>' . str_for_xml($currency_row['shop_currency_value_in_basic_currency']) . '</shop_currency_value_in_basic_currency>' . "\n";
			$xmlData .= '<shop_currency_is_default>' . str_for_xml($currency_row['shop_currency_is_default']) . '</shop_currency_is_default>' . "\n";

			if ($shop_id)
			{
				$aShopRow = $this->GetShop($shop_id);

				// Валюта товара извлекается из магазина
				$coefficient = $this->GetCurrencyCoefficientToShopCurrency($aShopRow['shop_currency_id'], $shop_currency_id);

				$xmlData .= '<shop_currency_coefficient>' . str_for_xml($coefficient) . '</shop_currency_coefficient>' . "\n";
			}

		}

		$xmlData .= '</shop_currency>' . "\n";

		return $xmlData;
	}

	/**
	 * Получение массива с данными о каталогах магазина, начиная с данной и до корневой
	 *
	 * @param int $group_id идентификатор группы
	 * @param int $shop_id идентификатор магазина,
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $group_id = 600;
	 *
	 * $row = $shop->GetShopGroupsToRoot($group_id, $shop_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array ассоциативный массив с данными о каталогах магазина
	 */
	function GetShopGroupsToRoot($group_id, $shop_id = FALSE)
	{
		$group_id = intval($group_id);
		$shop_id = intval($shop_id);

		$row = $this->GetGroup($group_id);
		if (!$row)
		{
			return $this->mas_groups_to_root;
		}
		else
		{
			$count = count($this->mas_groups_to_root);
			$this->mas_groups_to_root[$count] = $row;
			$this->GetShopGroupsToRoot($row['shop_groups_parent_id'], $shop_id);
		}

		return $this->mas_groups_to_root;
	}

	/**
	 * Получение числа магазинов для одного или всех сайтов, обслуживаемых системой управления
	 *
	 * @param mixed $site_id идентификатор сайта, для которого необходимо получить магазинов или false, если необходимо получить число магазинов для всех сайтов
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $site_id = 1;
	 *
	 * $count = $shop->GetCountShops($site_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число магазинов для одного или всех сайтов в случае успешного выполнения метода, false - в противном случае
	 */
	function GetCountShops($site_id)
	{
		$oShop = Core_Entity::factory('Shop');

		if ($site_id !== FALSE)
		{
			$site_id = intval($site_id);
			$oShop->queryBuilder()->where('site_id', '=', $site_id);
		}
		$aShops = $oShop->findAll();

		return count($aShops);
	}

	/**
	 * Получение числа систем оплаты
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $count = $shop->GetCountSystemOfPay();
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число систем оплаты в случае успешного выполнения метода, false - в противном случае
	 */
	function GetCountSystemOfPay($shop_shops_id = FALSE)
	{
		$oShop_Payment_System = Core_Entity::factory('Shop_Payment_System');

		if($shop_shops_id)
		{
			$shop_shops_id = intval($shop_shops_id);
			$oShop_Payment_System->queryBuilder()->where('shop_id', '=', $shop_shops_id);
		}
		$aShop_Payment_Systems = $oShop_Payment_System->findAll();
		return count($aShop_Payment_Systems);
	}

	/**
	 * Получение числа товаров в одном или во всех разделах магазина
	 *
	 * @param int $shop_id идентификатор магазиа
	 * @param mixed $group_id идентификатор раздела магазина, или false - если необходимо получить число всех товаров магазина
	 * @param array $param массив дополнительных параметров
	 * $param['shop_items_catalog_is_active'] активность товара (если 2, выбираем и активные и неактивные параметры, если 0 выбираем только неактивные товары, если не передан выбираем только активные товары)
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $group_id = 589;
	 *
	 * $count = $shop->GetCountItemsCatalog($shop_id, $group_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число товаров одного или всех разделов магазина в случае успешного выполнения метода, false - в противном случае
	 */
	function GetCountItemsCatalog($shop_id, $group_id, $param = array())
	{
		$shop_id = intval($shop_id);
		$oShop_Item = Core_Entity::factory('Shop_Item');
		$oShop_Item->queryBuilder()->where('shop_id', '=', $shop_id);

		if ($group_id !== FALSE)
		{
			$group_id = intval($group_id);
			$oShop_Item->queryBuilder()->where('shop_group_id', '=', $group_id);
		}

		if (isset($param['shop_items_catalog_is_active']) && intval($param['shop_items_catalog_is_active']) == 0)
		{
			$oShop_Item->queryBuilder()->where('active', '=', 0);
		}
		// если параметр не передан, берем только активные
		else
		{
			$oShop_Item->queryBuilder()->where('active', '=', 1);
		}

		$aShop_Items = $oShop_Item->findAll();

		return count($aShop_Items);
	}

	/**
	 * Получение числа стран
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $count = $shop->GetCountCountry();
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число стран в справочнике в случае удачного выполнения, false - в противном случае
	 */
	function GetCountCountry()
	{
		$oShop_Country = Core_Entity::factory('Shop_Country');
		$aShop_Countries = $oShop_Country->findAll();
		return count($aShop_Countries);
	}

	/**
	 * Получение числа областей (штатов) для страны
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $count = $shop->GetCountLocation($shop_country_id = 0);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число областей в справочнике в случае удачного выполнения, false - в противном случае
	 */
	function GetCountLocation($shop_country_id = 0)
	{
		$shop_country_id = intval($shop_country_id);
		$oShop_Country_Location = Core_Entity::factory('Shop_Country_Location');

		if ($shop_country_id > 0)
		{
			$oShop_Country_Location->queryBuilder()->where('shop_country_id', '=', $shop_country_id);
		}
		$aShop_Country_Locations = $oShop_Country_Location->findAll();
		return count($aShop_Country_Locations);
	}

	/**
	 * Получение числа городов для области
	 * @param int $shop_location_id идентификатор области
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_location_id = 0;
	 *
	 * $count = $shop->GetCountCity($shop_location_id = 0);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число стран в справочнике в случае удачного выполнения, false - в противном случае
	 */
	function GetCountCity($shop_location_id = 0)
	{
		$shop_location_id = intval($shop_location_id);
		$oShop_Country_Location_City = Core_Entity::factory('Shop_Country_Location_City');
		if ($shop_location_id > 0)
		{
			$oShop_Country_Location_City->queryBuilder()->where('shop_country_location_id', '=', $shop_location_id);
		}

		$aShop_Country_Location_Cities = $oShop_Country_Location_City->findAll();

		return count($aShop_Country_Location_Cities);
	}

	/**
	 * Получение числа налогов в справочнике
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $count = $shop->GetCountTax();
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число налогов в справочнике в случае успешного выполнения, false - в противном случае
	 */
	function GetCountTax()
	{
		$aShop_Taxes = Core_Entity::factory('Shop_Tax')->findAll();
		return count($aShop_Taxes);
	}

	/**
	 * Получение числа валют в справочнике
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $count = $shop->GetCountCurrency();
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число валют в справочнике в случае успешного выполнения, false - в противном случае
	 */
	function GetCountCurrency()
	{
		$aShop_Currencies = Core_Entity::factory('Shop_Currency')->findAll();
		return count($aShop_Currencies);
	}

	/**
	 * Получение числа единиц измерения в справочнике
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $count = $shop->GetCountMesures();
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число единиц измерения в справочнике в случае успешного выполнения, false - в противном случае
	 */
	function GetCountMesures()
	{
		$aShop_Measures = Core_Entity::factory('Shop_Measure')->findAll();
		return count($aShop_Measures);
	}

	/**
	 * Получение числа статусов заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $count = $shop->GetCountOrderStatus();
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число статусов заказа в справочнике в случае успешного выполнения, false - в противном случае
	 */
	function GetCountOrderStatus()
	{
		$aShop_Order_Statuses = Core_Entity::factory('Shop_Order_Status')->findAll();
		return count($aShop_Order_Statuses);
	}

	/**
	 * Получение числа свойств товаров конкретного магазина
	 *
	 * @param int $shop_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $count = $shop->GetCountProperties($shop_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число свойств товаров магазина в случае успешного выполнения, false - в противном случае
	 */
	function GetCountProperties($shop_id)
	{
		$shop_id = intval($shop_id);
		$aShop_Item_Properties = Core_Entity::factory('Shop', $shop_id)->Shop_Item_Properties->findAll();
		return count($aShop_Item_Properties);
	}

	/**
	 * Получение числа типов доставок для данного магазина
	 *
	 * @param int $shop_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $count = $shop->GetCountTypeOfDelivery($shop_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число типов доставок для магазина в случае успешного выполнения, false - в противном случае
	 */
	function GetCountTypeOfDelivery($shop_id)
	{
		$shop_id = intval($shop_id);
		$aShop_Deliveries = Core_Entity::factory('Shop', $shop_id)->Shop_Deliveries->findAll();
		return count($aShop_Deliveries);
	}

	/**
	 * Получение числа производителей для данного магазина
	 *
	 * @param int $shop_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $count = $shop->GetCountProducers($shop_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число производителей для данного магазина в случае успешного выполнения, false - в противном случае
	 */
	function GetCountProducers($shop_id)
	{
		$shop_id = intval($shop_id);
		$aShop_Producers = Core_Entity::factory('Shop', $shop_id)->Shop_Producers->findAll();
		return count($aShop_Producers);
	}

	/**
	 * Получение числа типов цен для магазина
	 *
	 * @param int $shop_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $count = $shop->GetCountTypePrices($shop_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число типов цен для магазина в случае успешного выполнения, false - в противном случае
	 */
	function GetCountTypePrices($shop_id)
	{
		$shop_id = intval($shop_id);
		$aShop_Prices = Core_Entity::factory('Shop', $shop_id)->Shop_Prices->findAll();
		return count($aShop_Prices);
	}

	/**
	 * Получение числа скидок для магазина
	 *
	 * @param int $shop_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $count = $shop->GetCountDiscount($shop_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число скидок для магазина в случае успешного выполнения, false - в противном случае
	 */
	function GetCountDiscount($shop_id)
	{
		$shop_id = intval($shop_id);
		$aShop_Discounts = Core_Entity::factory('Shop', $shop_id)->Shop_Discounts->findAll();
		return count($aShop_Discounts);
	}

	/**
	 * Получение массива идентификаторов всего дерева подгрупп данной группы включая идентификатор родительской
	 *
	 * @param int $shop_current_group_id идентификатор родительской группы
	 * @param int $shop_id идентификатор магазина
	 * @param array $mass массив идентификаторов подгрупп, служебное поле. Не передается, по умолчанию array()
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $shop_current_group_id = 586;
	 *
	 * $row = $shop->GetGroupsTree($shop_current_group_id, $shop_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив идетификаторов подгрупп
	 */
	function GetGroupsTree($shop_current_group_id, $shop_id, $mass = array())
	{
		$shop_current_groups_id = intval($shop_current_group_id);
		$shop_id = intval($shop_id);

		// Добавим элемент с родительским ID для случаев, когда у группы нет потомков
		if (!isset($mass[$shop_current_group_id]))
		{
			$mass[$shop_current_group_id] = array();
		}

		// Если есть данные в кэше о структуре - берем оттуда
		if (isset($this->CacheGoupsIdTree[$shop_id]))
		{
			if (isset($this->CacheGoupsIdTree[$shop_id][$shop_current_group_id])
			&& is_array($this->CacheGoupsIdTree[$shop_id][$shop_current_group_id])
			&& count($this->CacheGoupsIdTree[$shop_id][$shop_current_group_id]) > 0)
			{
				foreach ($this->CacheGoupsIdTree[$shop_id][$shop_current_group_id] as $id)
				{
					$mass[$shop_current_group_id][] = $id;
					$mass = $this->GetGroupsTree($id, $shop_id, $mass);
				}
			}
		}
		else
		{
			// Получаем непосредственных потомков данной группы
			$result = $this->GetGroups($shop_id, $shop_current_groups_id);

			while ($row = mysql_fetch_assoc($result))
			{
				$mass[$shop_current_group_id][] = $row['shop_groups_id'];
				$mass = $this->GetGroupsTree($row['shop_groups_id'], $shop_id, $mass);
			}
		}

		return $mass;
	}

	/**
	 * Индексация групп
	 *
	 * @param int $limit
	 * @param int $on_step
	 * @param int $shop_groups_id
	 * @return array
	 * @access private
	 */
	function IndexationShopGroups($limit, $on_step, $shop_groups_id = 0)
	{
		if ($shop_groups_id)
		{
			Search_Controller::indexingSearchPages(array(
				Core_Entity::factory('Shop_Group', $shop_groups_id)->indexing()
			));
		}
	}

	/**
	 * Индексация товаров
	 *
	 * @param int $limit
	 * @param int $on_step
	 * @param int $shop_items_catalog_item_id идентификатор товара, необязательное поле, передается только при событийной индексации
	 * @return array
	 */
	function IndexationShopItems($limit, $on_step, $shop_items_catalog_item_id = 0)
	{
		if ($shop_items_catalog_item_id)
		{
			Search_Controller::indexingSearchPages(array(
				Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->indexing()
			));
		}
	}

	/**
	 * Индексация продавцов
	 *
	 * @param int $limit
	 * @param int $on_step
	 * @param int $shop_sallers_id
	 * @return array
	 * @access private
	 */
	function IndexationShopSallers($limit, $on_step, $shop_sallers_id = 0)
	{
		if ($shop_sallers_id)
		{
			Search_Controller::indexingSearchPages(array(
				Core_Entity::factory('Shop_Seller', $shop_sallers_id)->indexing()
			));
		}
	}

	/**
	 * Функция обратного вызова, используется модулем поисковой системы при выводе результатов поиска
	 *
	 * @param array $row массив с информацией о странице
	 * @return string дополнительный XML, включаемый в результат поиска
	 */
	function _CallbackSearch($row)
	{
		$xml = '';

		if (isset($row['search_page_module_value_type']) && isset($row['search_page_module_value_id']))
		{
			if (in_array($row['search_page_module_value_type'], array(1, 2)))
			{
				// Товары
				if ($row['search_page_module_value_type'] == 2)
				{
					$row_item = $this->GetItem($row['search_page_module_value_id']);

					if ($row_item)
					{
						// XML для товара
						$xml = $this->GenXml4Item(0, $row_item);

						// Идентификатор группы
						$group_id = $row_item['shop_groups_id'];
						$xml_show_group_type = 'current';

						// Информация о продавце
						$xml .= $this->GenXml4Saller($row_item['shop_sallers_id']);
					}
				}
				else
				{
					// Идентификатор группы
					$group_id = $row['search_page_module_value_id'];
					$xml_show_group_type = '';
				}

				if (isset($group_id))
				{
					// Добавим группу в дерево потомком самого себя, чтобы GetGroupsXmlTree() провел генерацию
					$this->CacheGoupsIdTree[$row['search_page_module_entity_id']][$group_id][] = $group_id;

					// Формируем XML для группы товара
					$xml .= $this->GetGroupsXmlTree($row['search_page_module_entity_id'], array(
					'xml_show_group_type' => $xml_show_group_type,
					'parent_group_id' => $group_id,
					'current_group_id' => $group_id
					));

					// Удалим созданное выше дерево
					unset($this->CacheGoupsIdTree[$row['search_page_module_entity_id']]);
				}
			}
		}

		return $xml;
	}

	/**
	 * Вывод данных для эксопрта в Yandex.Market. Метод отправляет соответствующий заголовок и выводит XML данные.
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $external_properties['all_items'] массив с внешними свойствами
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $xml = $shop->YandexMarket($shop_shops_id);
	 *
	 * echo $xml;
	 *
	 * exit();
	 * ?>
	 * </code>
	 * @return mixed данные в формате XML или false
	 */
	function YandexMarket($shop_shops_id, $external_properties = array())
	{
		$shop_shops_id = intval($shop_shops_id);
		$external_properties = Core_Type_Conversion::toArray($external_properties);

		// Получаем данные о магазине
		$shop_row = $this->GetShop($shop_shops_id);

		if (!$shop_row)
		{
			return FALSE;
		}

		// Отправляем заголовок, о том что данные имеют тип XML
		header("Content-Type: text/xml; charset=" . SITE_CODING);

		echo '<?xml version="1.0" encoding="' . SITE_CODING . '"?>' . "\n";
		echo '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . "\n";
		echo '<yml_catalog date="' . date("Y-m-d H:i") . '">' . "\n";
		echo "<shop>\n";

		// Название магазина
		$shop_name = trim(Core_Type_Conversion::toStr($shop_row['shop_shops_yandex_market_name']));
		if (empty ($shop_name))
		{
			$shop_name = $shop_row['shop_shops_name'];
		}
		echo "<name>" . str_for_xml(mb_substr($shop_name, 0, 20)) . "</name>\n";

		// Название компании.
		$shop_company_id = Core_Type_Conversion::toInt($shop_row['shop_company_id']);
		$shop_company_row = $this->GetCompany($shop_company_id);
		$company_name = trim(Core_Type_Conversion::toStr($shop_company_row['shop_company_name']));
		echo "<company>" . str_for_xml($company_name) . "</company>\n";

		// Получаем путь к магазину (относительный)
		$structure = & singleton('Structure');

		$path = $structure->GetStructurePath($shop_row['structure_id'], 0);

		// Получаем путь к сайту
		$site = & singleton('site');
		$alias_row = $site->GetCurrentAlias($shop_row['site_id']);

		$url = $alias_row
			? 'http://' . $alias_row . '/' . $path
			: '';

		echo "<url>" . str_for_xml($url) . "</url>\n";
		echo "<platform>HostCMS</platform>\n";
		echo "<version>" . str_for_xml(CURRENT_VERSION) . "</version>\n";

		// ВАЛЮТЫ
		echo "<currencies>\n";
		$currency_resource = $this->GetAllCurrency();
		// Определяем число строк в ресурсе
		$currency_count = mysql_num_rows($currency_resource);

		$aCurr = array('RUR', 'RUB', 'USD', 'BYR', 'KZT', 'EUR', 'UAH');

		for ($i = 0; $i < $currency_count; $i++)
		{
			$row_currency = mysql_fetch_assoc($currency_resource);

			if (trim($row_currency['shop_currency_international_name']) != ''
			&& in_array($row_currency['shop_currency_international_name'], $aCurr))
			{
				echo '<currency id="' . $row_currency['shop_currency_international_name'] .
				'" rate="' . $row_currency['shop_currency_value_in_basic_currency'] . '"' . "/>\n";
			}

		}
		echo "</currencies>\n";

		// КАТЕГОРИИ
		$group_result = $this->GetAllGroups($shop_shops_id, array('groups_activity' => 1));
		if ($group_result)
		{
			echo "<categories>\n";
			foreach ($group_result as $row_group)
			{
				if ((trim($row_group['shop_groups_parent_id']) == '') || ($row_group['shop_groups_parent_id'] == 0))
				{
					$group_parent_id_tag_attr = "";
				}
				else
				{
					$group_parent_id_tag_attr = ' parentId="'.$row_group['shop_groups_parent_id'].'"';
				}

				$group_name = Core_Type_Conversion::toStr($row_group['shop_groups_name']);

				echo '<category id="' . $row_group['shop_groups_id'] . '"' . $group_parent_id_tag_attr . '>' . str_for_xml($group_name) . "</category>\n";
			}
			echo "</categories>\n";
		}

		flush();

		// ТОВАРЫ
		echo "<offers>\n";

		// Ограничиваем условия отбора по shop_items_catalog_yandex_market_allow = 1
		$current_date = date('Y-m-d H:i:s');

		$queryBuilder = Core_QueryBuilder::select(
				array('shop_items.id', 'shop_items_catalog_item_id'),
				array('shop_items.shortcut_id', 'shop_items_catalog_shortcut_id'),
				'shop_tax_id',
				array('shop_items.shop_seller_id', 'shop_sallers_id'),
				array('shop_items.shop_group_id', 'shop_groups_id'),
				'shop_currency_id',
				array('shop_items.shop_id', 'shop_shops_id'),
				array('shop_items.shop_producer_id', 'shop_producers_list_id'),
				array('shop_items.shop_measure_id', 'shop_mesures_id'),
				array('shop_items.type', 'shop_items_catalog_type'),
				array('shop_items.name', 'shop_items_catalog_name'),
				array('shop_items.marking', 'shop_items_catalog_marking'),
				array('shop_items.vendorcode', 'shop_vendorcode'),
				array('shop_items.description', 'shop_items_catalog_description'),
				array('shop_items.text', 'shop_items_catalog_text'),
				array('shop_items.image_large', 'shop_items_catalog_image'),
				array('shop_items.image_small', 'shop_items_catalog_small_image'),
				array('shop_items.weight', 'shop_items_catalog_weight'),
				array('shop_items.price', 'shop_items_catalog_price'),
				array('shop_items.active', 'shop_items_catalog_is_active'),
				array('shop_items.siteuser_group_id', 'shop_items_catalog_access'),
				array('shop_items.sorting', 'shop_items_catalog_order'),
				array('shop_items.path', 'shop_items_catalog_path'),
				array('shop_items.seo_title', 'shop_items_catalog_seo_title'),
				array('shop_items.seo_description', 'shop_items_catalog_seo_description'),
				array('shop_items.seo_keywords', 'shop_items_catalog_seo_keywords'),
				array('shop_items.indexing', 'shop_items_catalog_indexation'),
				array('shop_items.image_small_height', 'shop_items_catalog_small_image_height'),
				array('shop_items.image_small_width', 'shop_items_catalog_small_image_width'),
				array('shop_items.image_large_height', 'shop_items_catalog_big_image_height'),
				array('shop_items.image_large_width', 'shop_items_catalog_big_image_width'),
				array('shop_items.yandex_market', 'shop_items_catalog_yandex_market_allow'),
				array('shop_items.yandex_market_bid', 'shop_items_catalog_yandex_market_bid'),
				array('shop_items.yandex_market_cid', 'shop_items_catalog_yandex_market_cid'),
				array('shop_items.yandex_market_sales_notes', 'shop_items_catalog_yandex_market_sales_notes'),
				array('shop_items.siteuser_id', 'site_users_id'),
				array('shop_items.datetime', 'shop_items_catalog_date_time'),
				array('shop_items.modification_id', 'shop_items_catalog_modification_id'),
				array('shop_items.guid', 'shop_items_cml_id'),
				array('shop_items.start_datetime', 'shop_items_catalog_putoff_date'),
				array('shop_items.end_datetime', 'shop_items_catalog_putend_date'),
				array('shop_items.showed', 'shop_items_catalog_show_count'),
				array('shop_items.user_id', 'users_id')
			)
			->distinct()
			->from('shop_items')
			->join('shop_groups', 'shop_groups.id', '=', 'shop_items.shop_group_id',
				array(
						array('AND' => array('shop_groups.active', '=', 1)),
						array('OR' => array('shop_items.shop_group_id', '=', 0)),
					)
			)
			->where('shop_items.shortcut_id', '=', 0)
			->where('shop_items.active', '=', 1)
			->where('shop_items.siteuser_group_id', 'IN', array(0, -1))
			->where('shop_items.shop_id', '=', $shop_shops_id)
			->open()
			->where('shop_items.end_datetime', '>=', $current_date)
			->setOr()
			->where('shop_items.end_datetime', '=', '0000-00-00 00:00:00')
			->close()
			->where('shop_items.start_datetime', '<=', $current_date)
			->where('yandex_market', '=', 1)
			->where('price', '>', '0.00')
			->where('shop_items.deleted', '=', 0)
			->where('shop_groups.deleted', '=', 0)
			->orderBy('shop_items_catalog_order')
			->orderBy('shop_items_catalog_name');

		$aItemResult = $queryBuilder->execute()->asAssoc()->result();

		foreach($aItemResult as $row_item)
		{
			// Закэшируем временно для ускорения GetItem в вызываемых методах
			$this->CacheGetItem[$row_item['shop_items_catalog_item_id']] = $row_item;

			// Устанавливаем атрибуты тега <offer>
			$tag_bid = $row_item['shop_items_catalog_yandex_market_bid'];

			$tag_bid = $tag_bid
				? ' bid="' . $tag_bid . '"'
				: "";

			$tag_cid = $row_item['shop_items_catalog_yandex_market_cid'];
			$tag_cid = $tag_cid
				? ' cbid="' . $tag_cid . '"'
				: "";

			$warehouse = & singleton('warehouse');

			if ($warehouse->GetItemCountForAllWarehouses($row_item['shop_items_catalog_item_id']) > 0)
			{
				echo '<offer id="' . $row_item['shop_items_catalog_item_id'] . '"' .
				$tag_bid . $tag_cid . " available=\"true\">\n";
			}
			else
			{
				echo '<offer id="' . $row_item['shop_items_catalog_item_id'] . '"' .
				$tag_bid . $tag_cid . " available=\"false\">\n";
			}

			// URL
			echo '<url>' . str_for_xml($url.$this->GetPathItem($row_item['shop_items_catalog_item_id']) . rawurlencode($row_item['shop_items_catalog_path'])) . "/</url>\n";

			// Определяем цену со скидкой.
			$price = $this->GetPriceForUser(0, $row_item['shop_items_catalog_item_id'], $row_item);

			// Цена
			echo '<price>' . str_for_xml($price['price_discount']) . "</price>\n";

			// CURRENCY
			$row_currency = $this->GetCurrency($shop_row['shop_currency_id']);

			if ($row_currency)
			{
				echo '<currencyId>' . str_for_xml($row_currency['shop_currency_international_name']) . "</currencyId>\n";
			}

			// Идентификатор категории
			// Убрано по тикету 000031546
			//if ($row_item['shop_groups_id'] != 0)
			//{
			// Основной товар
			if (!$row_item['shop_items_catalog_modification_id'])
			{
				$categoryId = $row_item['shop_groups_id'];
			}
			else // Модификация, берем ID родительской группы
			{
				$modification_row = $this->GetItem($row_item['shop_items_catalog_modification_id']);
				if ($modification_row)
				{
					$categoryId = $modification_row['shop_groups_id'];
				}
				else
				{
					$categoryId = 0;
				}
			}

			echo '<categoryId>' . str_for_xml($categoryId) . "</categoryId>\n";

			// Указывает код товара производителя
			$bVendorCode = $row_item['shop_vendorcode'] && trim($row_item['shop_vendorcode']) != "";

			/* PICTURE*/
			// При указании VendorCode картинки нельзя
			if (/*!$bVendorCode && */trim($row_item['shop_items_catalog_image']) != "")
			{
				// Строим путь по новому стандарту
				$uploaddir = "/" . $this->GetItemDir($row_item['shop_items_catalog_item_id']);
				if (!empty ($uploaddir))
				{
					echo '<picture>' . str_for_xml('http://' . $alias_row . $uploaddir . $row_item['shop_items_catalog_image']) . "</picture>\n";
				}
			}

			if (strlen($row_item['shop_items_catalog_name']) > 0)
			{
				// NAME
				echo '<name>' . str_for_xml($row_item['shop_items_catalog_name']) . "</name>\n";

				// vendorCode
				if ($bVendorCode)
				{
					echo '<vendorCode>' . str_for_xml($row_item['shop_vendorcode']) . "</vendorCode>\n";
				}
			}

			// VENDOR NAME
			/*
			 // Удалено 22-10-08, т.к. нельзя использовать совместно с <name>
			 // см. http://www.hostcms.ru/forums/17/2065/
			 $row_producers_list = $this->GetProducer($row_item['shop_producers_list_id']);
			 if ($row_currency)
			 {
			 if (trim($row_producers_list['shop_producers_list_name']) != "")
			 {
			 echo '<vendor>'.str_for_xml($row_producers_list['shop_producers_list_name'])."</vendor>\n";
			 }
			 }
			 */

			/* DESCRIPTION*/
			if (!empty($row_item['shop_items_catalog_description']))
			{
				echo '<description>' . str_for_xml(html_entity_decode(strip_tags($row_item['shop_items_catalog_description']), ENT_COMPAT, 'UTF-8')) . "</description>\n";
			}

			/* sales_notes*/
			// если значение элемента для sales_notes не указано, тогда подставляем по умолчанию
			$sales_notes = strlen(trim($row_item['shop_items_catalog_yandex_market_sales_notes']))
				? $row_item['shop_items_catalog_yandex_market_sales_notes']
				: $shop_row['shop_yandex_market_sales_notes_default'];

			echo '<sales_notes>' . str_for_xml(html_entity_decode(strip_tags($sales_notes), ENT_COMPAT, 'UTF-8')) . "</sales_notes>\n";

			// Дополнительные параметры.
			if (isset($external_properties['all_items']))
			{
				if (is_array($external_properties['all_items']))
				{
					foreach ($external_properties['all_items'] as $key2 => $value)
					{
						echo "<$key2>" . str_for_xml($value) . "</$key2>\n";
					}
				}
			}

			echo "</offer>\n";

			// Очищаем кэш
			$this->CacheGetItem = array();
			//$this->MasGroup = array();

			flush();

			//$item_result[$key] = array();
			//unset($item_result[$key]);
		}

		echo "</offers>\n";
		echo "</shop>\n";
		echo '</yml_catalog>' . "\n";
	}

	/**
	 * Вывод данных для эксопрта в RamblerPokupki. Метод отправляет соответствующий заголовок и возвращает XML данные.
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $external_properties массив с внешними свойствами
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $xml = $shop->RamblerPokupki($shop_shops_id);
	 *
	 * echo $xml;
	 *
	 * exit();
	 * ?>
	 * </code>
	 * @return string данные в формате XML
	 */
	function RamblerPokupki($shop_shops_id, $external_properties = array())
	{

	}

	/**
	 * Получение информации о продавце по его идентификатору.
	 *
	 * @param int $seller_id Идентификатор продавца
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $seller_id = 1;
	 *
	 * $row = $shop->GetSeller($seller_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 *
	 * </code>
	 * @return	array ассоциативный массив с информацией о продавце
	 */
	function GetSeller($seller_id, $param = array())
	{
		$seller_id = intval($seller_id);
		$param = Core_Type_Conversion::toArray($param);

		$cache_name = 'SHOP_SALLER';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($seller_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oShop_Seller = Core_Entity::factory('Shop_Seller')->find($seller_id);
		if(!is_null($oShop_Seller->id))
		{
			return $this->getArrayShopSeller($oShop_Seller);
		}

		return FALSE;
	}

	/**
	 * Получение информации о всех продавцах магазина $shop_id, если $shop_id = false,
	 * то о продавцах всех магазинов
	 *
	 * @param int $shop_id - идентификатор магазина
	 * @param array $param массив дополнительных параметров
	 * - $param['xml_show_all_sellers'] отображать всех продавцов магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $shop->GetAllSellers($shop_id = false);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource информация о продавцах
	 */
	function GetAllSellers($shop_id = FALSE, $param = array())
	{
		if (!isset($param['xml_show_all_sellers'])	|| $param['xml_show_all_sellers'])
		{
			$queryBuilder = Core_QueryBuilder::select(
					array('shop_sellers.id', 'shop_sallers_id'),
					array('shop_sellers.shop_id', 'shop_shops_id'),
					array('shop_sellers.siteuser_id', 'site_users_id'),
					array('shop_sellers.name', 'shop_sallers_name'),
					array('shop_sellers.description', 'shop_sallers_comment'),
					array('shop_sellers.contact_person', 'shop_sallers_contact_person'),
					array('shop_sellers.image_large', 'shop_sallers_image'),
					array('shop_sellers.image_small', 'shop_sallers_small_image'),
					array('shop_sellers.image_large_height', 'shop_sallers_image_height'),
					array('shop_sellers.image_large_width', 'shop_sallers_image_width'),
					array('shop_sellers.image_small_height', 'shop_sallers_small_image_height'),
					array('shop_sellers.image_small_width', 'shop_sallers_small_image_width'),
					array('shop_sellers.address', 'shop_sallers_address'),
					array('shop_sellers.phone', 'shop_sallers_phone'),
					array('shop_sellers.fax', 'shop_sallers_fax'),
					array('shop_sellers.site', 'shop_sallers_http'),
					array('shop_sellers.email', 'shop_sallers_email'),
					array('shop_sellers.tin', 'shop_sallers_inn'),
					array('shop_sellers.user_id', 'users_id')
				)
				->from('shop_sellers')
				->where('shop_sellers.deleted', '=', 0)
				->orderBy('shop_sellers.name');

			// Если учитываем идентификаторы групп магазина
			if (!isset($param['xml_show_all_sellers']))
			{
				// Если текущая группа не массив и не false
				if (isset($param['current_group_id']) && !is_array($param['current_group_id']) && $param['current_group_id'] !== FALSE)
				{
					$param['current_group_id'] = array($param['current_group_id']);
				}

				if (isset($param['current_group_id']) && is_array($param['current_group_id']) && count($param['current_group_id']) > 0)
				{
					$param['current_group_id'] = Core_Array::toInt($param['current_group_id']);

					if (isset($param['shop_items_catalog_is_active']) && intval($param['shop_items_catalog_is_active']) == 0)
					{
						$queryBuilder->where('shop_items.active', '=', 0);
					}
					else
					{
						$queryBuilder->where('shop_items.active', '=', 1);
					}

					$queryBuilder
						->distinct('shop_sellers.id')
						->join('shop_items', 'shop_items.shop_seller_id', '=', 'shop_sellers.id')
						->where('shop_items.shop_group_id', 'IN', $param['current_group_id']);
				}
			}

			if ($shop_id && (!isset($param['shop_id']) || !$param['shop_id']))
			{
				$param['shop_id'] = $shop_id;
			}

			// Проверяем наличие id магазина
			if (isset($param['shop_id']) && $param['shop_id'])
			{
				$param['shop_id'] = intval($param['shop_id']);
				$queryBuilder->where('shop_sellers.shop_id', '=', $param['shop_id']);
			}

			return $queryBuilder->execute()->asAssoc()->getResult();
		}

		return FALSE;
	}

	/**
	 * Получение числа продавцов в справочнике
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $count = $shop->GetCountSellers();
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число продавцов в справочнике в случае успешного выполнения, false - в противном случае
	 */
	function GetCountSellers()
	{
		$aShop_Sellers = Core_Entity::factory('Shop_Seller')->findAll();
		return count($aShop_Sellers);
	}

	/**
	 * Вставка/обновление информации о продавце.
	 *
	 * @param array $param ассоциативный массив параметров
	 * - $param['shop_sallers_name'] имя (название) продавца
	 * - $param['shop_sallers_contact_person'] контактное лицо
	 * - $param['shop_sallers_comment'] комментарий к продавцу
	 * - $param['shop_sallers_address'] адрес продавца
	 * - $param['shop_sallers_phone'] телефон продавца
	 * - $param['shop_sallers_fax'] факс продавца
	 * - $param['shop_sallers_http'] сайт продавца
	 * - $param['site_users_id'] идентификатор пользователя сайта
	 * - $param['shop_sallers_email'] e-mail продавца
	 * - $param['shop_sallers_inn'] ИНН продавца
	 * - $param['shop_shops_id'] идентификатор магазина
	 * - $param['shop_sallers_id'] идентификатор продавца, указывается если нужно обновить информацию
	 * - $param['path_source_big_image'] путь к файлу-источнику большого изображения;
	 * - $param['path_source_small_image'] путь к файлу-источнику малого изображения;
	 * - $param['original_file_name_big_image'] оригинальное имя файла большого изображения
	 * - $param['original_file_name_small_image'] оригинальное имя файла малого изображения
	 * - $param['use_big_image'] использовать большое изображение для создания малого (true - использовать (по умолчанию), false - не использовать)
	 * - $param['max_width_big_image'] значение максимальной ширины большого изображения
	 * - $param['max_height_big_image'] значение максимальной высоты большого изображения
	 * - $param['max_width_small_image'] значение максимальной ширины малого изображения;
	 * - $param['max_height_small_image'] значение максимальной высоты малого изображения;
	 * - $param['watermark_file_path'] путь к файлу с "водяным знаком"
	 * - $param['watermark_position_x'] позиция "водяного знака" по оси X
	 * - $param['watermark_position_y'] позиция "водяного знака" по оси Y
	 * - $param['used_watermark_big_image'] наложить "водяной знак" на большое изображение (true - наложить (по умолчанию), false - не наложить);
	 * - $param['used_watermark_small_image'] наложить "водяной знак" на малое изображение (true - наложить (по умолчанию), false - не наложить);
	 * об уже существующем продавце.
	 * - $param  int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_sallers_name'] = 'Новый продавец';
	 * $param['shop_shops_id'] = 1;
	 *
	 * $newid = $shop->InsertSeller($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного или обновленного продавца.
	 */

	function InsertSeller($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['shop_sallers_id']) || !$param['shop_sallers_id'])
		{
			$param['shop_sallers_id'] = NULL;
		}

		$oShop_Seller = Core_Entity::factory('Shop_Seller', $param['shop_sallers_id']);

		isset($param['shop_sallers_name']) && $oShop_Seller->name = $param['shop_sallers_name'];
		isset($param['shop_sallers_contact_person']) && $oShop_Seller->contact_person = $param['shop_sallers_contact_person'];
		isset($param['shop_sallers_comment']) && $oShop_Seller->description = $param['shop_sallers_comment'];
		isset($param['shop_sallers_address']) && $oShop_Seller->address = $param['shop_sallers_address'];
		isset($param['shop_sallers_phone']) && $oShop_Seller->phone = $param['shop_sallers_phone'];
		isset($param['shop_sallers_fax']) && $oShop_Seller->fax = $param['shop_sallers_fax'];
		isset($param['shop_sallers_http']) && $oShop_Seller->site = $param['shop_sallers_http'];
		isset($param['shop_sallers_http']) && $oShop_Seller->email = $param['shop_sallers_email'];
		isset($param['shop_sallers_inn']) && $oShop_Seller->tin = $param['shop_sallers_inn'];
		isset($param['shop_shops_id']) && $oShop_Seller->shop_id = intval($param['shop_shops_id']);
		isset($param['site_users_id']) && $oShop_Seller->siteuser_id = intval($param['site_users_id']);

		is_null($oShop_Item_Associated->id) && isset($param['users_id']) && $param['users_id'] && $oShop_Item_Associated->user_id = intval($param['users_id']);

		if(!is_null($oShop_Seller->id) && class_exists('Cache'))
		{
			// Очистка файлового кэша
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_SALLER';
			$cache->DeleteCacheItem($cache_name, $oShop_Seller->id);
		}

		// Обрабатываем изображения для товара
		$param_file_save = array();
		$param_file_save['path_source_big_image'] = isset($param['path_source_big_image'])
			? $param['path_source_big_image']
			: '';

		$param_file_save['path_source_small_image'] = isset($param['path_source_small_image'])
			? $param['path_source_small_image']
			: '';

		// Путь к папке продавцов магазина
		$dir_seller_path = CMS_FOLDER . UPLOADDIR . 'shop_' . $shop_shops_id . '/sellers';

		$param_file_save['original_file_name_big_image'] = Core_Type_Conversion::toStr($param['original_file_name_big_image']);

		$param_file_save['original_file_name_small_image'] = Core_Type_Conversion::toStr($param['original_file_name_small_image']);

		$shop_row = $this->GetShop($shop_shops_id);

		if ($param_file_save['path_source_big_image'] != '')
		{
			// Преобразовываем название файла
			if ($shop_row['shop_shops_file_name_conversion'] == 1)
			{
				$ext = Core_File::getExtension($param_file_save['original_file_name_big_image']);

				if ($ext != '')
				{
					$ext = '.' . $ext;
				}

				// Получаем информацию о редактируемом товаре
				$seller_big_image = $dir_seller_path . '/seller_' . $seller_id . $ext;
			}
			else // Оставляем оригинальное имя файла
			{
				$seller_big_image = $dir_seller_path . '/' .  $param_file_save['original_file_name_big_image'];
			}
		}
		else
		{
			$seller_big_image = '';
		}

		$available_extantions = array('JPG', 'JPEG', 'PNG', 'GIF');

		if ($param_file_save['path_source_small_image'] != '' )
		{
			// Не задан файл большого изображения и задан файл малого
			if (empty($seller_big_image) && !empty($param_file_save['path_source_small_image']))
			{
				// Редактирование товара
				$create_big_image = isset($param['shop_sallers_id']) && $param['shop_sallers_id'] > 0
					? empty($seller_old_image_big)
					: empty($seller_old_image_small);
			}
			else
			{
				$create_big_image = FALSE;
			}

			$param_file_save['isset_big_image'] = !$create_big_image;

			$ext = Core_File::getExtension($param_file_save['original_file_name_small_image']);

			if ($create_big_image)
			{
				if (in_array(mb_strtoupper($ext), $available_extantions))
				{
					if ($ext != '')
					{
						$ext = '.' . $ext;
					}
					if ($shop_row['shop_shops_file_name_conversion'] == 1)
					{
						$seller_big_image = $dir_seller_path . '/seller_' . $seller_id . $ext;
						$seller_small_image = $dir_seller_path . '/small_seller_' . $seller_id . $ext;
					}
					else
					{
						$seller_big_image = $dir_seller_path . '/' . $param_file_save['original_file_name_small_image'];
						$seller_small_image = $dir_seller_path . '/small_' . $param_file_save['original_file_name_small_image'];
					}

					$param_file_save['original_file_name_big_image'] = $param_file_save['original_file_name_small_image'];
				}
				else
				{
					$seller_big_image = '';

					if ($ext != '')
					{
						$ext = '.' . $ext;
					}

					if ($shop_row['shop_shops_file_name_conversion'] == 1)
					{
						$seller_small_image = $dir_seller_path . '/small_seller_' . $seller_id . $ext;
					}
					else
					{
						$seller_small_image = $dir_seller_path . '/' . $param_file_save['original_file_name_small_image'];
					}
				}
			}
			else
			{
				if ($ext != '')
				{
					$ext = '.' . $ext;
				}

				if ($shop_row['shop_shops_file_name_conversion'] == 1)
				{
					$seller_small_image = $dir_seller_path . '/small_seller_' . $seller_id . $ext;
				}
				else
				{
					$seller_small_image = $dir_seller_path . '/' .$param_file_save['original_file_name_small_image'];
				}
			}
		}
		else // Не задано малое изображение
		{
			// Создаем малое изображение из большого
			if (isset($param['use_big_image']) && $param_file_save['path_source_big_image'] != '')
			{
				$param_file_save['use_big_image'] = $param['use_big_image'];

				if ($shop_row['shop_shops_file_name_conversion'] == 1)
				{
					$seller_small_image = $dir_seller_path . '/small_seller_' . $seller_id . $ext;
				}
				else
				{
					$seller_small_image = $dir_seller_path . '/small_' . $param_file_save['original_file_name_big_image'];
				}
			}
			else
			{
				$seller_small_image = '';
			}
		}

		// Заданы файлы малого или большого изображений и нет директории для хранения файлов изображений
		if ((!empty ($seller_big_image) || !empty ($seller_small_image)) && !is_dir($dir_seller_path))
		{
			@ mkdir($dir_seller_path, CHMOD);
			@ chmod($dir_seller_path, CHMOD);
		}

		$param_file_save['path_target_big_image'] = $seller_big_image;
		$param_file_save['path_target_small_image'] = $seller_small_image;

		isset($param['max_width_big_image']) && $param_file_save['max_width_big_image'] = $param['max_width_big_image'];
		isset($param['max_height_big_image']) && $param_file_save['max_height_big_image'] = $param['max_height_big_image'];
		isset($param['max_width_small_image']) && $param_file_save['max_width_small_image'] = $param['max_width_small_image'];
		isset($param['max_height_small_image']) && $param_file_save['max_height_small_image'] = $param['max_height_small_image'];
		isset($param['watermark_file_path']) && $param_file_save['watermark_file_path'] = $param['watermark_file_path'];
		isset($param['watermark_position_x']) && $param_file_save['watermark_position_x'] = $param['watermark_position_x'];
		isset($param['watermark_position_y']) && $param_file_save['watermark_position_y'] = $param['watermark_position_y'];
		isset($param['used_watermark_big_image']) && $param_file_save['used_watermark_big_image'] = Core_Type_Conversion::toBool($param['used_watermark_big_image']);
		isset($param['used_watermark_small_image']) && $param_file_save['used_watermark_small_image'] = Core_Type_Conversion::toBool($param['used_watermark_small_image']);

		// Вызываем метод загрузки изображений с определенными параметрами
		$lf_result = $kernel->AdminLoadFiles($param_file_save);

		$image = new Image();
		if ($lf_result['big_image'])
		{
			// Большое изображение успешно загружено, нужно обновить информацию о нем в БД
			$height = 0;
			$width = 0;

			// Обрабатываем размеры изображения
			if (is_file($param_file_save['path_target_big_image'])
			&& is_readable($param_file_save['path_target_big_image'])
			&& filesize($param_file_save['path_target_big_image']) > 12)
			{
				if (Core_Image::instance()->exifImagetype($param_file_save['path_target_big_image']))
				{
					$arr_of_image_sizes = $image->GetImageSize($param_file_save['path_target_big_image']);
					$height = $arr_of_image_sizes['height'];
					$width = $arr_of_image_sizes['width'];
				}
			}

			$temp_big_image = quote_smart(basename($param_file_save['path_target_big_image']));

			// Редактирование информации о товаре и новый файл имеет иное расширение
			if (isset($param['shop_sallers_id']) && $param['shop_sallers_id'] > 0 && $temp_big_image != Core_Type_Conversion::toStr($seller_info['shop_sallers_image']))
			{
				// Удаляем преждний файл
				if (is_file($dir_seller_path . '/' . $seller_info['shop_sallers_image']))
				{
					@unlink($dir_seller_path . '/' . $seller_info['shop_sallers_image']);
				}
			}

			$oShop_Seller->image_large = $temp_big_image;
			$oShop_Seller->image_large_height = $height;
			$oShop_Seller->image_large_width = $width;
			$oShop_Seller->save();
		}

		if ($lf_result['small_image'])
		{
			$height = 0;
			$width = 0;

			// Обрабатываем размеры изображения
			if (is_file($param_file_save['path_target_small_image'])
			&& is_readable($param_file_save['path_target_small_image'])
			&& filesize($param_file_save['path_target_small_image']) > 12)
			{
				if (Core_Image::instance()->exifImagetype($param_file_save['path_target_small_image']))
				{
					$arr_of_image_sizes = $image->GetImageSize($param_file_save['path_target_small_image']);
					$height = $arr_of_image_sizes['height'];
					$width = $arr_of_image_sizes['width'];
				}
			}

			$temp_small_image = quote_smart(basename($param_file_save['path_target_small_image']));

			// Редактирование информации о товаре и новый файл малого изображения имеет иное расширение
			if (isset($param['shop_sallers_id']) && $param['shop_sallers_id'] > 0 && $temp_small_image != Core_Type_Conversion::toStr($seller_info['shop_sallers_small_image']))
			{
				// Удаляем преждний файл
				if (is_file($dir_seller_path . '/' . $seller_info['shop_sallers_small_image']))
				{
					@unlink($dir_seller_path . '/' . $seller_info['shop_sallers_small_image']);
				}
			}

			$oShop_Seller->image_small = $temp_small_image;
			$oShop_Seller->image_small_height = $height;
			$oShop_Seller->image_small_width = $width;
			$oShop_Seller->save();
		}

		return $oShop_Seller->id;
	}

	/**
	 * Удаление продавца
	 *
	 * @param int $seller_id идентификационный номер продавца
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $seller_id = 8;
	 *
	 * $result = $shop->DeleteSeller($seller_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return mixed возвращает результат удаления продавца
	 */
	function DeleteSeller($seller_id)
	{
		$seller_id = intval($seller_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_SALLER';
			$cache->DeleteCacheItem($cache_name, $seller_id);
		}

		return Core_Entity::factory('Shop_Seller')->markDeleted();
	}

	/**
	 * Построения XML для продавцов
	 *
	 * @param int $shop_id идентификатор магазина, если 0, то XML генерируется для всех магазинов
	 * @param array $param массив дополнительных параметров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 *
	 * $xmlData = $shop->GenXml4Sallers($shop_id = 0);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);*
	 * ?>
	 * </code>
	 * @return string XML для продавцов
	 */
	function GenXml4Sallers($shop_id = 0, $param = array())
	{
		$resource_sellers = $this->GetAllSellers($shop_id, $param);

		if (!$resource_sellers)
		{
			return '';
		}

		// Отправляем заголовок, о том что данные имеют тип XML
		//header("Content-Type: text/xml");
		$xmlData = "<sallers>\n";
		$count_sellers = mysql_num_rows($resource_sellers);

		for ($i = 0; $i < $count_sellers; $i++)
		{
			$row = mysql_fetch_assoc($resource_sellers);
			$xmlData .= $this->GenXml4Saller($row['shop_sallers_id'], $row);
		}

		$xmlData .= "</sallers>\n";
		return $xmlData;
	}

	/**
	 * Возвращает XML продавца
	 *
	 * @param int $shop_sallers_id идентификатор продавца
	 * @param array $shop_sallers_row информация о продавце, если не false, то берется не из базы а отсюда.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_sallers_id = 1;
	 *
	 * $xmlData = $shop->GenXml4Saller($shop_sallers_id = 0);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return str XML текст с информацией о продавце.
	 */
	function GenXml4Saller($shop_sallers_id, $shop_sallers_row = FALSE)
	{
		// Если не передана строка с данными о продавце - получаем эти даные
		if (!$shop_sallers_row)
		{
			$shop_sallers_row = $this->GetSeller($shop_sallers_id);
		}

		$oShop_Seller = Core_Entity::factory('Shop_Seller')->find($shop_sallers_id);

		if ($shop_sallers_row)
		{
			$xmlData = '<saller id="' . str_for_xml($shop_sallers_row['shop_sallers_id']) . '">' . "\n";
			$xmlData .= '<sallers_name>' . str_for_xml($shop_sallers_row['shop_sallers_name']) . "</sallers_name>\n";
			$xmlData .= '<sallers_contact_person>' . str_for_xml($shop_sallers_row['shop_sallers_contact_person'])  ."</sallers_contact_person>\n";
			$xmlData .= '<sallers_comment>' . str_for_xml($shop_sallers_row['shop_sallers_comment']) . "</sallers_comment>\n";
			$xmlData .= '<sallers_address>' . str_for_xml($shop_sallers_row['shop_sallers_address']) .  "</sallers_address>\n";
			$xmlData .= '<sallers_phone>' . str_for_xml($shop_sallers_row['shop_sallers_phone']) . "</sallers_phone>\n";
			$xmlData .= '<sallers_fax>' . str_for_xml($shop_sallers_row['shop_sallers_fax']) . "</sallers_fax>\n";
			$xmlData .= '<sallers_http>' . str_for_xml($shop_sallers_row['shop_sallers_http']) . "</sallers_http>\n";
			$xmlData .= '<sallers_email>' . str_for_xml($shop_sallers_row['shop_sallers_email']) . "</sallers_email>\n";
			$xmlData .= '<sallers_inn>' . str_for_xml($shop_sallers_row['shop_sallers_inn']) . "</sallers_inn>\n";
			$xmlData .= '<site_users_id>' . str_for_xml($shop_sallers_row['site_users_id']) . "</site_users_id>\n";

			// Задано большое изображение для продавца
			if (!empty($shop_sallers_row['shop_sallers_image']))
			{
				// Путь к изображениям
				//$path_sellers = UPLOADDIR . 'shop_' . $shop_sallers_row['shop_shops_id'] . '/sellers/';
				//$seller_image_path = $path_sellers . $shop_sallers_row['shop_sallers_image'];

				// Определяем размер большого изображения
				$size_seller_big_image = Core_Image::instance()->getImageSize($oShop_Seller->getLargeFilePath());

				$size_seller_big_image_width = $size_seller_big_image['width'];
				$size_seller_big_image_height = $size_seller_big_image['height'];

				$xmlData .= '<sallers_image width = "' . $size_seller_big_image_width . '" height = "' . $size_seller_big_image_height . '">' . '/' . $oShop_Seller->getLargeFileHref() . '</sallers_image>' . "\n";
			}

			// Задано малое изображение для продавца
			if (!empty($shop_sallers_row['shop_sallers_small_image']))
			{
				// Определяем размер малого изображения
				$size_seller_small_image = Core_Image::instance()->getImageSize($oShop_Seller->getSmallFilePath());

				$size_seller_small_image_width = $size_seller_small_image['width'];
				$size_seller_small_image_height = $size_seller_small_image['height'];

				$xmlData .= '<sallers_small_image width = "' . $size_seller_small_image_width . '" height = "' . $size_seller_small_image_height . '">' . '/' . $oShop_Seller->getSmallFileHref() . '</sallers_small_image>' . "\n";
			}

			$xmlData .= "</saller>\n";
		}
		else
		{
			$xmlData = '<error>1</error>';
		}

		return $xmlData;
	}

	/**
	 * Получение хэша для кэширвоания количества товаров и групп
	 *
	 * @param $param массив параметров
	 * @return str md5
	 */
	function GetMd5CacheCountItemsAndGroup($param)
	{
		$md5_array = array();

		isset($param['show_catalog_item_type']) && $md5_array['show_catalog_item_type'] = $param['show_catalog_item_type'];
		isset($param['tags']) && $md5_array['tags'] = $param['tags'];
		isset($param['xml_show_group_type']) && $md5_array['xml_show_group_type'] = $param['xml_show_group_type'];
		isset($param['select']) && $md5_array['select'] = $param['select'];

		return crc32(serialize($md5_array));
	}

	/**
	 * Внутренний метод производит расчет числа подгрупп и элементов, содержащихся в группах магазина.
	 * Информация сохраняется в массиве:
	 * <br/>для числа групп в группе $this->CacheCountGroupsAndItems[shop_shops_id][information_groups_id][1]
	 * <br/>для числа элементов в группе $this->CacheCountGroupsAndItems[shop_shops_id][information_groups_id][0]
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $param массив дополнительных атрибутов
	 */
	function FillMemCacheCountItemsAndGroup($shop_shops_id, $param = array())
	{
		$shop_shops_id = intval($shop_shops_id);
		$md5 = $this->GetMd5CacheCountItemsAndGroup($param);

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_CACHE_COUNT_ITEMS_AND_GROUPS';
			$cache_key = "{$shop_shops_id}_{$md5}";

			if ($in_cache = $cache->GetCacheContent($cache_key, $cache_name))
			{
				$this->CacheCountGroupsAndItems[$shop_shops_id][$md5] = $in_cache['value'];
				return;
			}
		}

		$this->CacheCountGroupsAndItems[$shop_shops_id][$md5] = array();

		!isset($param['show_catalog_item_type']) && $param['show_catalog_item_type'] = array('active');

		// Заполняем массив для групп
		$queryBuilder = Core_QueryBuilder::select('parent_id', array('COUNT(id)', 'count'))
			->from('shop_groups')
			->where('shop_groups.shop_id', '=', $shop_shops_id)
			->where('shop_groups.deleted', '=', 0)
			->groupBy('shop_groups.parent_id');

		$aResult = $queryBuilder->execute()->asAssoc()->result();
		foreach ($aResult as $row)
		{
			$this->CacheCountGroupsAndItems[$shop_shops_id][$md5][$row['parent_id']][1] = $row['count'];
		}

		$current_date = date('Y-m-d H:i:s');

		$queryBuilder
			->clear()
			->select(array('shop_items.shop_group_id', 'shop_groups_id'), 'COUNT(shop_items.id) as count')
			->from('shop_items')
			->where('shop_items.shop_id', '=', $shop_shops_id)
			->where('shop_items.modification_id', '=', 0)
			->where('shop_items.deleted', '=', 0)
			->groupBy('shop_group_id');

		// Если только активные (без неактивных)
		if (in_array('active', $param['show_catalog_item_type']) && !in_array('inactive', $param['show_catalog_item_type']))
		{
			$queryBuilder->where('shop_items.active', '=', 1);
		}
		// только неактивные
		elseif (in_array('inactive', $param['show_catalog_item_type']) && !in_array('active', $param['show_catalog_item_type']))
		{
			$queryBuilder->where('shop_items.active', '=', 0);
		}

		// Если не содержит putend_date - ограничиваем по дате окончания публикации
		if (!in_array('putend_date', $param['show_catalog_item_type']))
		{
			$queryBuilder
				->open()
				->where('shop_items.end_datetime', '>=', $current_date)
				->setOr()
				->where('shop_items.end_datetime', '=', '0000-00-00 00:00:00')
				->close();
		}

		// если не содержит putoff_date - ограничиваем по дате начала публикации
		if (!in_array('putoff_date', $param['show_catalog_item_type']))
		{
			$queryBuilder->where('shop_items.start_datetime', '<=', $current_date);
		}

		// Объединяем с тегами и ограничиваем по ним
		if (isset($param['tags']) && is_array($param['tags']) && count($param['tags']) > 0)
		{
			$param['tags'] = Core_Array::toInt($param['tags']);

			$queryBuilder
				->leftJoin('tag_shop_items', 'shop_items.id', '=', 'shop_item_id')
				->where('tag_shop_items.tag_id', 'IN', $param['tags']);
		}

		if (isset($param['sql_from']))
		{
			$aSqlFrom = explode(',', strval($param['sql_from']));
			foreach($aSqlFrom as $sSqlFrom)
			{
				trim($sSqlFrom) != '' && $queryBuilder->from(trim($sSqlFrom));
				//$this->parseQueryBuilder(trim($sSqlFrom), $queryBuilder);
			}
		}

		// Флаг, указывающий наличие условий для товаров
		$isset_items_property = FALSE;

		$isset_shop_items_catalog_rest = FALSE;

		if (isset($param['select']) && count($param['select']) > 0)
		{
			foreach ($param['select'] as $value)
			{
				if ($value['value'] !== '')
				{
					$where_value = $value['value'];

					// Если не IN ()
					/*if (strtoupper(Core_Type_Conversion::toStr($value['if'])) != 'IN')
					{
						$where_value = "'$where_value'";
					}*/
				}
				else
				{
					$where_value = '';
				}

				// Ограничение по остатку на складе
				if (isset($value['name']) && $value['name'] == 'shop_items_catalog_rest')
				{
					if (!$isset_shop_items_catalog_rest)
					{
						$queryBuilder
							->select(array('SUM(shop_warehouse_items.count)', 'shop_items_catalog_rest'))
							->leftJoin('shop_warehouse_items', 'shop_warehouse_items.shop_item_id', '=', 'shop_items.id')
							->clearGroupBy()
							->groupBy('shop_items.id')
							->groupBy('shop_group_id');
					}
					$queryBuilder->having('shop_items_catalog_rest', $value['if'], $where_value);
				}
				else
				{
					// Есть ограничение на значения доп. свойств товаров
					if (Core_Type_Conversion::toInt($value['property_id']) != 0)
					{
						$isset_items_property = TRUE;
					}

					if (Core_Type_Conversion::toInt($value['type']) == 0) // Основное свойство
					{
						$this->parseQueryBuilder($value['prefix'], $queryBuilder);

						$value['name'] != '' && $value['if'] != ''
							&& $this->parseQueryBuilderWhere($value['name'], $value['if'], $where_value, $queryBuilder);

						$this->parseQueryBuilder($value['sufix'], $queryBuilder);
					}
					else // Дополнительное свойство
					{
						$this->parseQueryBuilder($value['prefix'], $queryBuilder);
						$queryBuilder->where('shop_item_properties.property_id', '=', $value['property_id']);
						$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);

						$this->parseQueryBuilderWhere(
							$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $where_value, $queryBuilder
						);

						$this->parseQueryBuilder($value['sufix'], $queryBuilder);
					}
				}
			}

			// Объединение со св-вами делаем только тогда, когда есть внешняя фильтрация по ним
			if ($isset_items_property)
			{
					$queryBuilder
						->leftJoin('shop_item_properties', 'shop_items.shop_id', '=', 'shop_item_properties.shop_id')
						->leftJoin('property_value_ints', 'shop_items.id', '=', 'property_value_ints.entity_id',
							array(
								array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_ints.property_id')))
							)
						)
						->leftJoin('property_value_strings', 'shop_items.id', '=', 'property_value_strings.entity_id',
							array(
								array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_strings.property_id')))
							)
						)
						->leftJoin('property_value_texts', 'shop_items.id', '=', 'property_value_texts.entity_id',
							array(
								array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_texts.property_id')))
							)
						)
						->leftJoin('property_value_datetimes', 'shop_items.id', '=', 'property_value_datetimes.entity_id',
							array(
								array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_datetimes.property_id')))
							)
						)
						->leftJoin('property_value_files', 'shop_items.id', '=', 'property_value_files.entity_id',
							array(
								array('AND' => array('shop_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_files.property_id')))
							)
						);
			}
		}

		$aResult = $queryBuilder->execute()->asAssoc()->result();

		foreach($aResult as $row)
		{
			// Из-за двойной группировки
			if (!isset($this->CacheCountGroupsAndItems[$shop_shops_id][$md5][$row['shop_groups_id']][0]))
			{
				$this->CacheCountGroupsAndItems[$shop_shops_id][$md5][$row['shop_groups_id']][0] = $row['count'];
			}
			else
			{
				// Суммируем
				$this->CacheCountGroupsAndItems[$shop_shops_id][$md5][$row['shop_groups_id']][0] += $row['count'];
			}
		}

		// Запись в файловый кэш
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache->Insert($cache_key, $this->CacheCountGroupsAndItems[$shop_shops_id][$md5], $cache_name);
		}
	}

	/**
	 * Внутренний метод формирует дерево групп и подгрупп в массиве по их идентификаторам. Заполянется также в методе FillMasGroup()
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 */
	function FillMemCacheGoupsIdTree($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		$shop_row = $this->GetShop($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select('id', 'parent_id')
			->from('shop_groups')
			->where('shop_groups.shop_id', '=', $shop_shops_id)
			->where('shop_groups.deleted', '=', 0);

		switch ($shop_row['shop_group_sort_order_type'])
		{
			case 0 :
				$group_order = 'ASC';
			break;
			default :
				$group_order = 'DESC';
			break;
		}

		switch ($shop_row['shop_group_sort_order_field'])
		{
			case 0 :
				$queryBuilder->orderBy('name');
			break;
			default :
				$queryBuilder
					->orderBy('sorting', $group_order)
					->orderBy('name');
		}

		$this->CacheGoupsIdTree[$shop_shops_id] = array();
		$aResult = $queryBuilder->execute()->asAssoc()->result();

		foreach($aResult as $row)
		{
			$this->CacheGoupsIdTree[$shop_shops_id][$row['parent_id']][] = $row['id'];
		}
	}

	/**
	 * Получение числа элементов и групп для переданной родительской группы
	 *
	 * @param int $parent_group_id идентификатор группы, для которой необходимо получить число элементов и групп.
	 * @param int $shop_shops_id идентификатор магазина, к которому принадлежит группа.
	 * @param boolean $sub параметр, определяющий будут ли учитываться подгруппы данной группы при подсчете элементов и групп (true - подгруппы учитываются, false - не учитываются). по умолчанию $sub = true
	 * @param array массив дополнительных атрибутов
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $parent_group_id = 589;
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetCountItemsAndGroups($parent_group_id, $shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array $mas массив из 4-х элементов
	 * - $mas['count_items'] число элементов в группе без учета элементов в подгруппах
	 * - $mas['count_all_items'] число элементов в группе с учетом элементов в подгруппах
	 * - $mas['count_groups'] число групп в данной группе без учета вложенности подгрупп
	 * - $mas['count_all_groups'] число групп в данной группе с учетом вложенности подгрупп
	 */
	function GetCountItemsAndGroups($parent_group_id, $shop_shops_id, $sub = TRUE, $param = array())
	{
		$parent_group_id = intval($parent_group_id);
		$shop_shops_id = intval($shop_shops_id);
		$sub = Core_Type_Conversion::toBool($sub);

		$param['sub'] = $sub;

		if (!isset($param['md5']))
		{
			$md5 = $this->GetMd5CacheCountItemsAndGroup($param);
			$param['md5'] = $md5;
		}

		$md5 = $param['md5'];

		// Если есть в кэше памяти
		if (isset($this->CacheGetCountItemsAndGroups[$shop_shops_id][$md5][$parent_group_id]))
		{
			return $this->CacheGetCountItemsAndGroups[$shop_shops_id][$md5][$parent_group_id];
		}

		/* Проверка на наличие в файловом кэше*/
		/*
		if (class_exists('Cache'))
		{
		$cache = & singleton('Cache');
		$cache_field = $parent_group_id.'_'.$shop_shops_id.'_'.$sub.'_'.$md5;
		$cache_name = 'SHOP_COUNT_ITEM_AND_GROUP';
		if (($in_cache = $cache->GetCacheContent($cache_field, $cache_name)) && $in_cache)
		{
		$this->CacheGetCountItemsAndGroups[$shop_shops_id][$md5][$parent_group_id] = $in_cache['value'];
		return $in_cache['value'];
		}
		}*/

		// Если есть данные для магазина. Если подгрупп было 0, то они не добавлены в массив
		if (!isset($this->CacheCountGroupsAndItems[$shop_shops_id][$md5]))
		{
			// Производим расчет числа элементов в группе
			$this->FillMemCacheCountItemsAndGroup($shop_shops_id, $param);
		}

		$mas = array();
		$mas['count_items'] = Core_Type_Conversion::toInt($this->CacheCountGroupsAndItems[$shop_shops_id][$md5][$parent_group_id][0]);
		$mas['count_all_items'] = $mas['count_items'];

		$mas['count_groups'] = Core_Type_Conversion::toInt($this->CacheCountGroupsAndItems[$shop_shops_id][$md5][$parent_group_id][1]);
		$mas['count_all_groups'] = $mas['count_groups'];

		// Учитывать подгруппы
		if ($sub)
		{
			// Если дерево групп не заполнено для магазина - заполняем его
			if (!isset($this->CacheGoupsIdTree[$shop_shops_id]))
			{
				$this->FillMemCacheGoupsIdTree($shop_shops_id);
			}

			// Если у группы есть подгруппы
			if (isset($this->CacheGoupsIdTree[$shop_shops_id][$parent_group_id]))
			{
				// Если массив отсутствует - определим его пустым
				if (!isset($this->recursion_tmp[$shop_shops_id][$md5]))
				{
					$this->recursion_tmp[$shop_shops_id][$md5] = array();
				}

				foreach ($this->CacheGoupsIdTree[$shop_shops_id][$parent_group_id] as $group_id)
				{
					// Для исключения зацикливания, в $this->recursion_tmp хранится список элементов,
					// которые уже были в рекурсии и в дальнейшем если снова они пришли
					// (зацикливание узла структуры родитель --> ребенок)
					if ($group_id != $parent_group_id
					&& !isset($this->recursion_tmp[$shop_shops_id][$md5][$group_id])
					)
					{
						$this->recursion_tmp[$shop_shops_id][$md5][$group_id] = 0;

						$mas_subgroup = $this->GetCountItemsAndGroups($group_id, $shop_shops_id, $sub, $param);

						$mas['count_all_items'] += $mas_subgroup['count_all_items'];
						$mas['count_all_groups'] += $mas_subgroup['count_all_groups'];
					}
				}
			}
		}

		// Кэшируем в памяти число элементов
		$this->CacheGetCountItemsAndGroups[$shop_shops_id][$md5][$parent_group_id] = $mas;

		/* Запись в файловый кэш*/
		/*if (class_exists('Cache'))
		 {
		 $cache->Insert($cache_field, $mas, $cache_name);
		 }*/

		if (!$parent_group_id)
		{
			$this->recursion_tmp[$shop_shops_id] = array();
		}

		return $mas;
	}

	/**
	 * Формирование дерева групп для магазина.
	 *
	 * @param int $shop_parent_group_id идентификатор группы, относительно которой строится дерево групп.
	 * @param int $shop_id идентификатор магазина, для которого строится дерево групп.
	 * @param string $separator символ, отделяющий группу нижнего уровня от родительской группы.
	 * @param int $shop_groups_id идентификатор группы, которую вместе с ее подгруппами не нужно включать в дерево групп, если id = false, то включать в дерево групп все подгруппы.
	 * @param array $param дополнительные параметры
	 * - $param['sum_separator'] - служебный элемент
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $shop_parent_group_id = 586;
	 *
	 * $row = $shop->GetDelimitedGroups($shop_parent_group_id, $shop_id , $separator='');
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array двумерный массив, содержащий дерево подгрупп.
	 */
	function GetDelimitedGroups($shop_parent_group_id, $shop_id, $separator = '', $shop_groups_id = FALSE, $param = array())
	{
		$shop_parent_group_id = intval($shop_parent_group_id);
		$shop_id = intval($shop_id);

		if (!isset($this->CacheGoupsIdTree[$shop_id]))
		{
			$this->FillMemCacheGoupsIdTree($shop_id);
		}

		$param['sum_separator'] = !isset($param['sum_separator'])
			? $separator
			: $param['sum_separator'] . $separator;

		if (isset($this->CacheGoupsIdTree[$shop_id][$shop_parent_group_id]))
		{
			foreach ($this->CacheGoupsIdTree[$shop_id][$shop_parent_group_id] as $shop_group_id)
			{
				$row = $this->GetGroup($shop_group_id, array(
				'cache_off' => TRUE
				));

				if (is_array($row) && $shop_groups_id !== $row['shop_groups_id'])
				{
					$count_mas = count($this->mas_groups);
					$row['separator'] = $param['sum_separator'];
					$this->mas_groups[$count_mas] = $row;

					$this->GetDelimitedGroups($row['shop_groups_id'], $shop_id, $separator, $shop_groups_id, $param);
				}
			}
		}

		return $this->mas_groups;
	}

	/**
	 * Получение списка идентификаторов свойств товаров для определенной группы.
	 *
	 * @param int $shop_groups_id идентификатор группы
	 * @param int $shop_items_catalog_item_id идентификатор товара, необязательное поле, по умолчанию false
	 * @param int $shop_shops_id идентификатор магазина, необязательное поле, по умолчанию false
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_groups_id = 586;
	 *
	 * $row = $shop->GetPropertiesOfGroup($shop_groups_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив со списком
	 */
	function GetPropertiesOfGroup($shop_groups_id, $shop_items_catalog_item_id = FALSE, $shop_shops_id = FALSE)
	{
		$shop_groups_id = intval($shop_groups_id);

		$cache_key = $shop_groups_id . '_' . serialize($shop_items_catalog_item_id) . '_' . serialize($shop_shops_id);
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ALL_PROPERTIES_FOR_GROUP';
			if ($in_cache = $cache->GetCacheContent($cache_key, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		// Выбираем свойства, разрешенные для данной группы товаров
		$queryBuilder = Core_QueryBuilder::select('properties.id')
			->distinct()
			->from('properties')
			->join('shop_item_properties', 'shop_item_properties.property_id', '=', 'properties.id')
			->join('shop_item_property_for_groups', 'shop_item_property_id', '=', 'shop_item_properties.id')
			//->leftJoin('shop_groups', 'shop_item_property_for_groups.shop_group_id', '=', 'shop_groups.id')
			->where('shop_item_property_for_groups.shop_group_id', '=', $shop_groups_id)
			// ->where('shop_groups.id', '=', $shop_groups_id) // Ограничение выше эквивалентно, записи для группы 0 в таблице не будет
			->where('properties.deleted', '=', 0)
			//->where('shop_groups.deleted', '=', 0) // Если группа 0 то deleted нет
			//->groupBy('properties.id')
			->orderBy('properties.sorting')
			->orderBy('properties.name');

		/*
		->distinct()
			->from('properties')
			->join('shop_item_properties', 'shop_item_properties.property_id', '=', 'properties.id')
			->join('shop_item_property_for_groups', 'shop_item_property_id', '=', 'shop_item_properties.id')
			->join('shops', 'shops.id', '=', 'shop_item_property_for_groups.shop_id')
			->join('shop_groups', 'shop_groups.id', '=', 'shop_item_property_for_groups.shop_group_id')
			->where('shop_item_property_for_groups.shop_group_id', 'IN', $groups_array)
			->where('shop_item_properties.shop_id', '=', $shop_id)
			->where('properties.deleted', '=', 0)
			->where('shops.deleted', '=', 0)
			->where('shop_groups.deleted', '=', 0)
			->orderBy('shop_list_of_properties_order')
			->orderBy('shop_list_of_properties_name');
		*/

		if ($shop_shops_id)
		{
			$queryBuilder
				->where('shop_item_properties.shop_id', '=', $shop_shops_id)
				->where('shop_item_property_for_groups.shop_id', '=', $shop_shops_id);
		}

		$aResult = $queryBuilder->execute()->asAssoc()->result();

		$result = array();
		foreach ($aResult as $aTmpResult)
		{
			$result[] = $aTmpResult['id'];
		}

		//echo count($result);

		/* Выбираем свойства, для которых уже установлены значения, даже если они
		 не разрешены для данной группы товаров.*/
		if ($shop_items_catalog_item_id)
		{
			$queryBuilder
				->clear()
				->select('id')
				->from('properties')
				->where('deleted', '=', 0);

			$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
			$oShop_Item = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id);
			$oProperties = Core_Entity::factory('Shop_Item_Property_List', $oShop_Item->shop_id)->Properties;

			if (count($aResult) > 0)
			{
				$aNotIn = array();
				foreach($aResult as $row)
				{
					$aNotIn[] = $row['id'];
				}

				$oProperties
					->queryBuilder()
					->where('properties.id', 'NOT IN', $aNotIn);

				$aProperties = $oProperties->findAll();
				foreach($aProperties as $oProperty)
				{
					$result[] = $oProperty->id;
				}
			}
		}

		if (class_exists('Cache'))
		{
			$cache->Insert($cache_key, $result, $cache_name);
		}

		return $result;
	}

	/**
	 * Построение XML для свойств товаров, доступных группе.
	 *
	 * @param int $shop_groups_id идентификатор группы, если 0, то XML генерируется для корневой группы.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $shop_groups_id = 589;
	 *
	 * $xml = $shop->GenXml4Properties($shop_shops_id, $shop_groups_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xml);
	 * ?>
	 * </code>
	 * @return string XML для свойств
	 */
	function GenXml4Properties($shop_shops_id, $shop_groups_id = 0, $param = array())
	{
		$shop_shops_id = intval($shop_shops_id);
		$shop_groups_id = intval($shop_groups_id);

		$resource_properties = $this->GetPropertiesOfGroupForXml($shop_shops_id, $shop_groups_id, $param);

		if (!$resource_properties)
		{
			return '';
		}

		$xmlData = "<properties_for_group>\n";

		$count_properties = mysql_num_rows($resource_properties);
		for ($i = 0; $i < $count_properties; $i++)
		{
			$row = mysql_fetch_assoc($resource_properties);

			// Корректировка типа свойства
			$row['shop_list_of_properties_type'] = $this->correctItemPropertyType($row['shop_list_of_properties_type']);

			$xmlData .= $this->GenXml4Property($row['shop_list_of_properties_id'], $row);
		}
		$xmlData .= "</properties_for_group>\n";

		return $xmlData;
	}

	/**
	 * Возвращает XML свойства.
	 *
	 * @param int $shop_list_of_properties_id свойства
	 * @param array $property_row информация о свойстве
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_list_of_properties_id = 133;
	 *
	 * $xmlData = $shop->GenXml4Property($shop_list_of_properties_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return str XML текст с информацией о свойстве.
	 */
	function GenXml4Property($shop_list_of_properties_id, $property_row = false)
	{
		// Если не передан массив с данными о свойстве - получаем эти даные.
		if (!$property_row)
		{
			$property_row = $this->GetPropretyOfItems($shop_list_of_properties_id);
		}

		$xmlData = '';

		if ($property_row)
		{
			$xmlData .= '<property id="' . str_for_xml($property_row['shop_list_of_properties_id']) . '" parent_id="' . str_for_xml($property_row['shop_properties_items_dir_id']) . '" xml_name="' . str_for_xml($property_row['shop_list_of_properties_xml_name']) . '">' . "\n";

			// Устаревший тег
			$xmlData .= '<name>' . str_for_xml($property_row['shop_list_of_properties_name']) . "</name>\n";

			$xmlData .= '<property_name>' . str_for_xml($property_row['shop_list_of_properties_name']) . "</property_name>\n";
			$show_kind = Core_Type_Conversion::toInt($property_row['shop_list_of_properties_show_kind']);

			switch (Core_Type_Conversion::toInt($property_row['shop_list_of_properties_type']))
			{
				case 2: // Список, код откорректированный
					if (class_exists('lists'))
					{
						$Lists = & singleton('lists');
						$xmlData .= $Lists->GenXml4ListItems(Core_Type_Conversion::toInt($property_row['lists_id']));
						break;
					}
			}

			$xmlData .= '<shop_list_of_properties_type>' . $property_row['shop_list_of_properties_type'] . "</shop_list_of_properties_type>\n";
			$xmlData .= '<shop_list_of_properties_description>' . str_for_xml($property_row['shop_list_of_properties_description']) . "</shop_list_of_properties_description>\n";
			$xmlData .= '<property_show_kind>' . $show_kind . "</property_show_kind>\n";
			// Выбираем единицу измерения свойства товара из таблицы единиц измерения
			$mesure = $this->GetMesure($property_row['shop_mesures_id']);
			if ($mesure)
			{
				$xmlData .= '<mesure>' . str_for_xml($mesure['shop_mesures_name']) . "</mesure>\n";
			}
			$xmlData .= "</property>\n";
		}

		return $xmlData;
	}

	/**
	 * Возвращает список свойств, доступных для вывода в группе
	 *
	 * @param int $shop_groups_id идентификатор группы, если 0, то возвращает свойства, разрешенные для корневой группы
	 * @return mixed resource или fasle
	 */
	function GetPropertiesOfGroupForXml($shop_shops_id, $shop_groups_id = 0, $param = array())
	{
		$shop_shops_id = intval($shop_shops_id);

		// Получаем массив идентификаторов свойств
		$property_id_array = $this->GetPropertiesOfGroup($shop_groups_id);

		// Если были указаны и другие группы в $param['current_group_id'], то выбираем и для них
		if (isset($param['current_group_id'])
		&& is_array($param['current_group_id'])
		&& count($param['current_group_id']) > 0)
		{
			foreach ($param['current_group_id'] as $tmp_group_id)
			{
				$aTmp = $this->GetPropertiesOfGroup($tmp_group_id);

				if (is_array($aTmp) && count($aTmp) > 0)
				{
					foreach ($aTmp as $value)
					{
						if (!in_array($value, $property_id_array))
						{
							$property_id_array[] = $value;
						}
					}
				}
			}
		}

		// Свойств нет
		if (!count($property_id_array))
		{
			return FALSE;
		}

		$queryBuilder = Core_QueryBuilder::select(
				array('properties.id', 'shop_list_of_properties_id'),
				array('shop_item_properties.shop_id', 'shop_shops_id'),
				array('shop_measure_id', 'shop_mesures_id'),
				array('list_id', 'lists_id'),
				array('name', 'shop_list_of_properties_name'),
				array('tag_name', 'shop_list_of_properties_xml_name'),
				array('type', 'shop_list_of_properties_type'),
				array('shop_item_properties.prefix', 'shop_list_of_properties_prefics'),
				array('default_value', 'shop_list_of_properties_default_value'),
				array('sorting', 'shop_list_of_properties_order'),
				array('filter', 'shop_list_of_properties_show_kind'),
				array('user_id', 'users_id'),
				array('guid', 'shop_list_of_properties_cml_id'),
				array('property_dir_id', 'shop_properties_items_dir_id'),
				array('image_large_max_width', 'shop_list_of_properties_default_big_width'),
				array('image_large_max_height', 'shop_list_of_properties_default_big_height'),
				array('image_small_max_width', 'shop_list_of_properties_default_small_width'),
				array('image_small_max_height', 'shop_list_of_properties_default_small_height'),
				array('description', 'shop_list_of_properties_description')
			)
			->from('properties')
			->join('shop_item_properties', 'properties.id', '=', 'shop_item_properties.property_id')
			->where('shop_item_properties.shop_id', '=', $shop_shops_id)
			->where('properties.id', 'IN', $property_id_array)
			->where('deleted', '=', 0)
			->orderBy('shop_list_of_properties_order')
			->orderBy('shop_list_of_properties_name');

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Генерация xml для товаров, выбранных для сравнения
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $xmlData = $shop->GenXml4CompareItems();
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 *
	 * ?>
	 * </code>
	 * @return str сгенерированный XML
	 */
	function GenXml4CompareItems()
	{
		if (isset($_COOKIE['SHOPCOMPARE']))
		{
			$sCookeContent = $this->GetCookie('SHOPCOMPARE');
			$compare_items = @ unserialize(Core_Type_Conversion::toStr($sCookeContent));
			$compare_items = Core_Type_Conversion::toArray($compare_items);
		}
		else
		{
			return '';
		}

		$xmlData = "<compare_items>\n";

		foreach ($compare_items as $key => $value)
		{
			$row = $this->GetItem($value);

			if ($row)
			{
				$xmlData .= "<compare_item>\n";
				$xmlData .= "<compare_item_id>" . intval($row['shop_items_catalog_item_id']) . "</compare_item_id>\n";
				$xmlData .= "<compare_item_name>" . str_for_xml($row['shop_items_catalog_name']) . "</compare_item_name>\n";
				$xmlData .= "<compare_item_path>" . str_for_xml($row['shop_items_catalog_path']) . "</compare_item_path>\n";
				$xmlData .= "<compare_item_fullpath>" . str_for_xml($this->GetPathGroup(intval($row['shop_groups_id']), '')) . "</compare_item_fullpath>\n";
				$xmlData .= "</compare_item>\n";
			}
		}
		$xmlData .= "</compare_items>\n";

		return $xmlData;
	}

	/**
	 * Отображение сравнения выбранных товаров в магазине
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param string $xsl_name имя XSL-шаблона
	 * @param $param массив дополнительных параметров
	 * - $param['xml_show_group_property'] разрешает указание в XML значений свойств групп магазина, по умолчанию true
	 * - $param['xml_show_item_property'] разрешает указание в XML значений свойств товаров магазина, по умолчанию true
	 * - $param['xml_show_group_type'] тип генерации XML для групп, может принимать значения (по умолчанию 'tree'):
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>all - все группы всех уровней;
	 * <li>current - группы только текущего уровня;
	 * <li>tree - группы, находящиеся выше по дереву;
	 * <li>none - не выбирать группы.
	 * </ul>
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $xsl_name= 'СравнениеТоваров';
	 *
	 * $shop->ShowItemsCompare($shop_id, $xsl_name);
	 * ?>
	 * </code>
	 * @return false в случае неудачи
	 */
	function ShowItemsCompare($shop_id, $xsl_name, $param = array(), $external_propertys = array())
	{
		$shop_id = intval($shop_id);

		// Десериализуем массив
		$sCookeContent = $this->GetCookie('SHOPCOMPARE');
		$compare_items = @unserialize(Core_Type_Conversion::toStr($sCookeContent));
		$compare_items = Core_Type_Conversion::toArray($compare_items);

		if (count($compare_items) == 0)
		{
			return FALSE;
		}

		$compare_items = Core_Array::toInt($compare_items);

		$queryBuilder = Core_QueryBuilder::select()
			->distinct('shop_group_id')
			->from('shop_items')
			->where('id', 'IN', $compare_items)
			->where('deleted', '=', 0);

		$aResult = $queryBuilder->execute()->asAssoc()->result();

		// Если есть хоть одна группа, в т.ч. 0, то выполняем выборку свойств
		if (count($aResult) == 0)
		{
			return FALSE;
		}

		foreach($aResult as $row)
		{
			$groups_array[] = $row['shop_group_id'];
		}

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= "<compare>\n";

		// Вносим в XML дополнительные теги из массива дополнительных параметров
		$ExternalXml = new ExternalXml;
		$xmlData .= $ExternalXml->GenXml($external_propertys);
		unset($ExternalXml);

		// Получаем путь к магазину
		$shop_row = $this->GetShop($shop_id);

		if (!$shop_row)
		{
			return FALSE;
		}

		// Формируем дерево групп товаров
		$xmlData .= $this->GetGroupsXmlTree($shop_id, $param);

		$Structure = & singleton('Structure');
		$shop_path = $Structure->GetStructurePath($shop_row['structure_id'], 0);

		if ($shop_path != '/')
		{
			$shop_path = '/' . $shop_path;
		}

		$xmlData .= "<shop_path>" . str_for_xml($shop_path) . "</shop_path>\n";

		// Обрабатываем группы дополнительных свойств товаров, очищаем кэши директорий для элементов для магазина
		// Формируем XML-данные для групп дополнительных свойств товара
		$dir_prop_array = $this->GetAllPropertiesItemsDirForShop($shop_id);

		$this->cache_propertys_items_dir_tree[$shop_id] = array();

		if (mysql_num_rows($dir_prop_array) > 0)
		{
			while ($dir_prop_row = mysql_fetch_assoc($dir_prop_array))
			{
				$this->cache_propertys_items_dir[$dir_prop_row['shop_properties_items_dir_id']] = $dir_prop_row;
				$this->cache_propertys_items_dir_tree[$shop_id][$dir_prop_row['shop_properties_items_dir_parent_id']][] = $dir_prop_row['shop_properties_items_dir_id'];
			}
		}

		// Временный буфер
		$this->buffer = '';

		// Формируем XML для групп дополнительных свойств товаров
		$this->GenXmlForItemsPropertyDir($shop_id);

		$xmlData .= $this->buffer;

		// Временный буфер
		$this->buffer = '';

		/* Запрос, возвращающий список разрешенных свойств для данных элементов,
		 с учетом их групп, в т.ч. и корневой */

		$queryBuilder = Core_QueryBuilder::select(
				array('properties.id', 'shop_list_of_properties_id'),
				array('shop_item_properties.shop_id', 'shop_shops_id'),
				array('shop_item_properties.shop_measure_id', 'shop_mesures_id'),
				array('list_id', 'lists_id'),
				array('properties.name', 'shop_list_of_properties_name'),
				array('tag_name', 'shop_list_of_properties_xml_name'),
				array('type', 'shop_list_of_properties_type'),
				array('shop_item_properties.prefix', 'shop_list_of_properties_prefics'),
				array('default_value', 'shop_list_of_properties_default_value'),
				array('properties.sorting', 'shop_list_of_properties_order'),
				array('filter', 'shop_list_of_properties_show_kind'),
				array('properties.user_id', 'users_id'),
				array('properties.guid', 'shop_list_of_properties_cml_id'),
				array('property_dir_id', 'shop_properties_items_dir_id'),
				array('properties.image_large_max_width', 'shop_list_of_properties_default_big_width'),
				array('properties.image_large_max_height', 'shop_list_of_properties_default_big_height'),
				array('properties.image_small_max_width', 'shop_list_of_properties_default_small_width'),
				array('properties.image_small_max_height', 'shop_list_of_properties_default_small_height'),
				array('properties.description', 'shop_list_of_properties_description')
			)
			->distinct()
			->from('properties')
			->join('shop_item_properties', 'shop_item_properties.property_id', '=', 'properties.id')
			->join('shop_item_property_for_groups', 'shop_item_property_id', '=', 'shop_item_properties.id')
			->join('shops', 'shops.id', '=', 'shop_item_property_for_groups.shop_id')
			->join('shop_groups', 'shop_groups.id', '=', 'shop_item_property_for_groups.shop_group_id')
			->where('shop_item_property_for_groups.shop_group_id', 'IN', $groups_array)
			->where('shop_item_properties.shop_id', '=', $shop_id)
			->where('properties.deleted', '=', 0)
			->where('shops.deleted', '=', 0)
			->where('shop_groups.deleted', '=', 0)
			->orderBy('shop_list_of_properties_order')
			->orderBy('shop_list_of_properties_name');

		$aResult = $queryBuilder->execute()->asAssoc()->result();

		// Формируем XML для свойств
		$xmlData .= "<compare_properties>\n";
		foreach($aResult as $row)
		{
			$xmlData .= $this->GenXml4Property($row['shop_list_of_properties_id'], $row);
		}
		$xmlData .= "</compare_properties>\n";

		// Формируем XML для товаров (вместе со значениями свойств)
		$xmlData .= "<compare_items>\n";
		foreach ($compare_items as $key => $value)
		{
			$row = $this->GetItem($value);
			if ($row)
			{
				$xmlData .= $this->GenXml4Item(0, $row, 0, $param);
			}
		}
		$xmlData .= "</compare_items>\n";

		// Добавляем в XML данные о продавцах
		$xmlData .= $this->GenXml4Sallers($shop_id);

		$xmlData .= "</compare>\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Получение информации о комментарии к товару
	 *
	 * @param int $shop_comment_id
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_comment_id = 3;
	 *
	 * $row = $shop->GetComment($shop_comment_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array с информацией о комментарии или false
	 */
	function GetComment($shop_comment_id, $param = array())
	{
		$shop_comment_id = intval($shop_comment_id);
		$param = Core_Type_Conversion::toArray($param);

		$cache_name = 'SHOP_COMMENT';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($shop_comment_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oComment = Core_Entity::factory('Comment')->find($shop_comment_id);
		$row = !is_null($oComment->id)
			? $this->getArrayShopItemComment($oComment)
			: FALSE;

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($shop_comment_id, $row, $cache_name);
		}
		return $row;
	}

	/**
	 * Добавление/обновление информации об отзыве на товар
	 *
	 * @param array $param массив доп. параметров
	 * - int $param['shop_comment_id'] идентификатор комментария
	 * - int $param['shop_items_catalog_item_id'] идентификатор товара
	 * - int $param['shop_comment_active'] активность комментария
	 * - int $param['shop_comment_grade'] оценка
	 * - int $param['site_users_id'] идентификатор пользователя сайта
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	 * - string $param['shop_comment_date_time'] дата/время комментария (в формате гггг-мм-дд чч:мм:сс)
	 * - string $param['shop_comment_user_name'] имя пользователя, оставившего комментарий
	 * - string $param['shop_comment_user_email'] e-mail пользователя, оставившего комментарий
	 * - string $param['shop_comment_subject'] тема комментария
	 * - string $param['shop_comment_text'] текст комментария
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 1;
	 * $param['shop_comment_active'] = 1;
	 * $param['shop_comment_subject'] = 'Товар 1';
	 * $param['shop_comment_text'] = 'Хороший товар';
	 * $param['shop_comment_user_name'] = 'Петр Николаевич';
	 * $param['shop_comment_user_email'] = 'petr@test.ru';
	 * $param['shop_comment_date_time'] = '2008-08-20 15:16:00';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$param['site_users_id'] = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$param['site_users_id'] = 0;
	 * }
	 *
	 * $newid = $shop->InsertComment($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор комментария (отзыва) или false
	 */
	function InsertComment($param)
	{
		if (!isset($param['shop_comment_id']) || !$param['shop_comment_id'])
		{
			$param['shop_comment_id'] = NULL;
		}

		$oComment = Core_Entity::factory('Comment', $param['shop_comment_id']);

		if (!isset($param['shop_comment_date_time']))
		{
			$oComment->datetime = Core_Type_Conversion::toStr($param['shop_comment_date_time']);
		}
		elseif(is_null($oComment))
		{
			$oComment->datetime = date('Y-m-d H:i:s');
		}

		isset($param['shop_comment_user_name']) && $oComment->author = $param['shop_comment_user_name'];
		isset($param['shop_comment_user_email']) && $oComment->email = $param['shop_comment_user_email'];

		if (isset($param['shop_comment_user_ip']))
		{
			$oComment->ip = $param['shop_comment_user_ip'];
		}
		elseif(is_null($oComment->id))
		{
			$oComment->ip = $_SERVER['REMOTE_ADDR'];
		}

		isset($param['shop_comment_subject']) && $oComment->subject = $param['shop_comment_subject'];
		isset($param['shop_comment_text']) && $oComment->text = $param['shop_comment_text'];

		if(isset($param['shop_comment_grade']))
		{
			$param['shop_comment_grade'] = intval($param['shop_comment_grade']);

			if ($param['shop_comment_grade'] > 5)
			{
				$oComment->grade = 5;
			}
			elseif ($param['shop_comment_grade'] < 0)
			{
				$oComment->grade = 0;
			}
			else
			{
				$oComment->grade = $param['shop_comment_grade'];
			}
		}

		isset($param['shop_comment_active']) && $oComment->active = intval($param['shop_comment_active']);
		isset($param['site_users_id']) && $oComment->siteuser_id = intval($param['site_users_id']);
		is_null($oComment->id) && isset($param['users_id']) && $param['users_id'] && $oComment->user_id = $param['users_id'];

		if (!is_null($oComment->id) && class_exists('Cache'))
		{
			// Очистка файлового кэша
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_COMMENT';
			$cache->DeleteCacheItem($cache_name, $oComment->id);
		}

		$oComment->save();

		if (isset($param['shop_items_catalog_item_id']))
		{
			Core_Entity::factory('Shop_Item', intval($param['shop_items_catalog_item_id']))->add($oComment);
		}

		return $oComment->id;
	}

	/**
	 * Удаление информации об отзыве на товар
	 *
	 * @param int $shop_comment_id идентификатор отзыва
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_comment_id = 1;
	 *
	 * $result = $shop->DeleteComment($shop_comment_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return mixed результат выполнения запроса
	 */
	function DeleteComment($shop_comment_id)
	{
		$shop_comment_id = intval($shop_comment_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_COMMENT';
			$cache->DeleteCacheItem($cache_name, $shop_comment_id);
		}

		Core_Entity::factory('Comment', $shop_comment_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Генерация XML для отзыва о товаре
	 *
	 * @param int $shop_comment_id идентификатор отзыва о товаре, может быть 0, если указан $comment_row
	 * @param array $comment_row массив с данными о  комментарии или false (тогда должен быть указан $shop_comment_id)
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_comment_id = 1;
	 *
	 * $xmlData = $shop->GenXml4Comment($shop_comment_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return string XML текс с данными об отзыве
	 */
	function GenXml4Comment($shop_comment_id, $comment_row = FALSE)
	{
		$shop_comment_id = intval($shop_comment_id);

		$row = count(Core_Type_Conversion::toArray($comment_row)) > 0
			? $comment_row
			: $this->GetComment($shop_comment_id);

		$xmlData = '';

		if ($row)
		{
			if ($row['shop_comment_is_comment'])
			{
				// Получаем информацию о товаре
				$shop_item_info = $this->GetItem($row['shop_items_catalog_item_id']);

				$shop_id = $shop_item_info['shop_shops_id'];

				// Получаем информацию о интернет-магазине
				$shop_row = $this->GetShop($shop_id);

				$xmlData = '<comment id="' . Core_Type_Conversion::toInt($row['shop_comment_id']) . '">' . "\n";
				$xmlData .= "<subject>" . str_for_xml($row['shop_comment_subject']) . "</subject>\n";
				$xmlData .= "<text>" . str_for_xml($row['shop_comment_text']) . "</text>\n";

				$xmlData .= "<date_time>" . str_for_xml(strftime(Core_Type_Conversion::toStr($shop_row['shop_format_datetime']), Core_Date::sql2timestamp($row['shop_comment_date_time']))) . "</date_time>\n";

				$xmlData .= "<date>" . str_for_xml(strftime(Core_Type_Conversion::toStr($shop_row['shop_format_date']), Core_Date::sql2timestamp($row['shop_comment_date_time']))) . "</date>\n";

				if (!$row['site_users_id'])
				{
					$xmlData .= "<user_name>" . str_for_xml($row['shop_comment_user_name']) . "</user_name>\n";
					$xmlData .= '<user_email>' . str_for_xml($row['shop_comment_user_email']) . '</user_email>' . "\n";
					$xmlData .= '<user_ip>' . str_for_xml($row['shop_comment_user_ip']) . '</user_ip>' . "\n";
				}
				elseif (class_exists('SiteUsers'))
				{
					$SiteUsers = & singleton('SiteUsers');
					// Добавляем информацию о пользователе
					$xmlData .= $SiteUsers->GetSiteUserXml($row['site_users_id']);
				}

				$xmlData .= "<active>" . intval($row['shop_comment_active']) . "</active>\n";
				$xmlData .= "<is_comment>" . intval($row['shop_comment_is_comment']) . "</is_comment>\n";
				$xmlData .= "<grade>" . str_for_xml($row['shop_comment_grade']) . "</grade>\n";

				$xmlData .= '<comment_shop_name>' . str_for_xml($shop_row['shop_shops_name']) . '</comment_shop_name>' .  "\n";
				$xmlData .= '<comment_shop_id>' . $shop_id . '</comment_shop_id>' . "\n";
				$xmlData .= '<comment_shop_group_id>' . intval($shop_item_info['shop_groups_id']) . '</comment_shop_group_id>' . "\n";
				$xmlData .= '<comment_shop_item_name>' . str_for_xml($shop_item_info['shop_items_catalog_name']) . '</comment_shop_item_name>' . "\n";
				$xmlData .= '<comment_shop_item_id>' . intval($shop_item_info['shop_items_catalog_item_id']) . '</comment_shop_item_id>' . "\n";

				$site = & singleton('site');
				$alias_name = $site->GetCurrentAlias(CURRENT_SITE);

				$xmlData .= '<comment_domain_name>' . $alias_name . '</comment_domain_name>' . "\n";
				$xmlData .= "</comment>\n";
			}
		}

		return $xmlData;
	}

	/**
	 * Получение информации о всех комментариях, относящиеся к данному товару.
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @param array массив с доп. параметрами
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 *
	 * $xmlData = $shop->GenXml4ItemCatalogComments($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return string XML с данными об отзывах
	 */
	function GenXml4ItemCatalogComments($shop_items_catalog_item_id, $param = array())
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
		$param = Core_Type_Conversion::toArray($param);

		// Выбиарем только активные элементы
		$param['shop_comment_active'] = 1;

		$comment_array = $this->GetAllComments($shop_items_catalog_item_id, $param);

		if (!$comment_array)
		{
			return '';
		}

		$xmlData = "<comments>\n";
		$xmlData .= $this->GenXml4ItemCatalogCommentAverageGrade($shop_items_catalog_item_id);
		foreach ($comment_array as $key => $row)
		{
			$xmlData .= $this->GenXml4Comment(0, $row);
		}
		$xmlData .= '<count_comments>' . $this->GetCountAllComments() . '</count_comments>' . "\n";
		$xmlData .= "</comments>\n";
		return $xmlData;
	}

	/**
	 * Возвращает XML со средней оценкой описания активных отзывов по товару.
	 *
	 * @param int $shop_items_catalog_item_id метод генерирующий XML активных отзывов для данного товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 *
	 * $xmlData = $shop->GenXml4ItemCatalogCommentAverageGrade($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return int средняя оценка
	 */
	function GenXml4ItemCatalogCommentAverageGrade($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		$param['shop_comment_active'] = 1;
		$comment_array = $this->GetAllComments($shop_items_catalog_item_id, $param);

		$grade_sum = 0;
		$count = 0;

		if ($comment_array)
		{
			foreach ($comment_array as $key => $row)
			{
				if ($row['shop_comment_grade'] > 0)
				{
					$count++;
					$grade_sum += $row['shop_comment_grade'];
				}
			}
		}

		$average_grade = $count > 0
			? $grade_sum / $count
			: 0;

		$mod = $average_grade - intval($average_grade);
		$average_grade = intval($average_grade);

		if ($mod >= 0.25 && $mod < 0.75)
		{
			$average_grade += 0.5;
		}
		elseif ($mod >= 0.75)
		{
			$average_grade += 1;
		}

		$xmlData = "<grade_sum>$grade_sum</grade_sum>\n";
		$xmlData .= "<grade_count>$count</grade_count>\n";
		$xmlData .= "<average_grade>$average_grade</average_grade>\n";

		return $xmlData;
	}

	/**
	 * Отображение информации о заказах пользователя
	 *
	 * @param int $site_users_id идентификатор пользователя сайта
	 * @param string $xsl_name имя XSL шаблонав
	 * @param array $param массив дополнительных параметров
	 * - $param['shop_shops_id'] идентификатор магазина, для которого осуществляется показ заказов
	 * - $param['orders_begin'] номер заказа в выборке, с которого начинать отображение
	 * - $param['orders_on_page'] число заказов, отображаемых на странице
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $site_users_id = 19;
	 * $xsl_name = 'СписокЗаказов';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 *	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $shop->ShowUserOrders($site_users_id, $xsl_name);
	 *
	 * ?>
	 * </code>
	 * @return false в случае неудачи
	 */
	function ShowUserOrders($site_users_id, $xsl_name, $param = array())
	{
		$site_users_id = intval($site_users_id);
		$xsl_name = Core_Type_Conversion::toStr($xsl_name);

		$oShop_Orders = Core_Entity::factory('Siteuser', $site_users_id)->Shop_Orders;

		if (isset($param['orders_on_page']) && $param['orders_on_page'] !== FALSE)
		{
			$orders_on_page = Core_Type_Conversion::toInt($param['orders_on_page']);
			$orders_begin = Core_Type_Conversion::toInt($param['orders_begin']);
			$oShop_Orders->queryBuilder()->limit($orders_begin, $orders_on_page);
		}

		$aShop_Orders = isset($param['shop_shops_id'])
			? $oShop_Orders->getByShopId($param['shop_shops_id'])
			: $oShop_Orders->findAll();

		$shop_shops_id = isset($param['shop_shops_id'])
			? Core_Type_Conversion::toInt($param['shop_shops_id'])
			: FALSE;

		if (count($aShop_Orders) == 0)
		{
			return FALSE;
		}

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xmlData .= "<orders>\n";

		foreach ($aShop_Orders as $oShop_Order)
		{
			$row = $this->getArrayShopOrder($oShop_Order);

			// Выводим список заказов БЕЗ информации о пользователе сайта
			$xmlData .= $this->GetXmlForOrder(0, $row, FALSE);
		}

		// Выводим информацию о пользователе сайта
		$xmlData .= '<site_user_id>' . $site_users_id . '</site_user_id>' . "\n";

		if (class_exists('SiteUsers'))
		{
			// Получаем xml для пользователя
			$SiteUsers = & singleton('SiteUsers');
			$xmlData .= $SiteUsers->GetSiteUserXml($site_users_id);
		}

		$xmlData .= '<system_of_pays>' . "\n";
		$xmlData .= $this->GenXmlForSystemOfPays($shop_shops_id);
		$xmlData .= '</system_of_pays>' . "\n";

		$xmlData .= "</orders>\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Получение информации об организации
	 *
	 * @param int $shop_company_id идентификатор организации
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_company_id = 1;
	 *
	 * $row = $shop->GetCompany($shop_company_id);
	 *
	 * //Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с информацией об организации или false
	 */
	function GetCompany($shop_company_id)
	{
		$shop_company_id = intval($shop_company_id);
		$oShop_Company = Core_Entity::factory('Shop_Company')->find($shop_company_id);

		if (!is_null($oShop_Company->id))
		{
			return $this->getArrayShopCompany($oShop_Company);
		}

		return FALSE;
	}

	/**
	 * Вставка/обновление информации об организации
	 *
	 * @param array $param массив доп. параметров
	 * - int $param['shop_company_id'] идентификатор организации
	 * - string $param['shop_company_name'] название организации
	 * - string $param['shop_company_description'] описание организации
	 * - string $param['shop_company_inn'] ИНН организации
	 * - string $param['shop_company_kpp'] КПП организации
	 * - string $param['shop_company_ogrn'] ОГРН организации
	 * - string $param['shop_company_okpo'] ОКПО организации
	 * - string $param['shop_company_okved'] ОКВЕД организации
	 * - string $param['shop_company_bik'] БИК организации
	 * - string $param['shop_company_account'] счет организации
	 * - string $param['shop_company_corr_account'] корр. счет организации
	 * - string $param['shop_company_bank_name'] название банка
	 * - string $param['shop_company_bank_address'] адрес банка
	 * - string $param['shop_company_fio'] ФИО директора организации
	 * - string $param['shop_company_accountant_fio'] ФИО бухгалтера организации
	 * - string $param['shop_company_address'] адрес организации
	 * - string $param['shop_company_phone'] телефон организации
	 * - string $param['shop_company_fax'] факс организации
	 * - string $param['shop_company_site'] сайт организации
	 * - string $param['shop_company_email'] e-mail организации
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_company_name'] = 'Новая организация';
	 *
	 * $newid = $shop->InsertCompany($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор организации или false
	 */
	function InsertCompany($param)
	{
		if (!isset($param['shop_company_id']) || !$param['shop_company_id'])
		{
			$param['shop_company_id'] = NULL;
		}

		$oShop_Company = Core_Entity::factory('Shop_Company', $param['shop_company_id']);

		isset($param['shop_company_name']) && $oShop_Company->name = $param['shop_company_name'];
		isset($param['shop_company_description']) && $oShop_Company->description = $param['shop_company_description'];
		isset($param['shop_company_inn']) && $oShop_Company->tin = $param['shop_company_inn'];
		isset($param['shop_company_kpp']) && $oShop_Company->kpp = $param['shop_company_kpp'];
		isset($param['shop_company_ogrn']) && $oShop_Company->psrn = $param['shop_company_ogrn'];
		isset($param['shop_company_okpo']) && $oShop_Company->okpo = $param['shop_company_okpo'];
		isset($param['shop_company_okved']) && $oShop_Company->okved = $param['shop_company_okved'];
		isset($param['shop_company_bik']) && $oShop_Company->bic = $param['shop_company_bik'];
		isset($param['shop_company_account']) && $oShop_Company->current_account = $param['shop_company_account'];
		isset($param['shop_company_corr_account']) && $oShop_Company->correspondent_account = $param['shop_company_corr_account'];
		isset($param['shop_company_bank_name']) && $oShop_Company->bank_name = $param['shop_company_bank_name'];
		isset($param['shop_company_bank_address']) && $oShop_Company->bank_address = $param['shop_company_bank_address'];
		isset($param['shop_company_fio']) && $oShop_Company->legal_name = $param['shop_company_fio'];
		isset($param['shop_company_accountant_fio']) && $oShop_Company->accountant_legal_name = $param['shop_company_accountant_fio'];
		isset($param['shop_company_address']) && $oShop_Company->address = $param['shop_company_address'];
		isset($param['shop_company_phone']) && $oShop_Company->phone = $param['shop_company_phone'];
		isset($param['shop_company_fax']) && $oShop_Company->fax = $param['shop_company_fax'];
		isset($param['shop_company_site']) && $oShop_Company->site = $param['shop_company_site'];
		isset($param['shop_company_email']) && $oShop_Company->email = $param['shop_company_email'];

		if(isset($param['shop_company_guid']) && $param['shop_company_guid'] != '')
		{
			$oShop_Company->guid = $param['shop_company_guid'];
		}
		else
		{
			// GUID не передан, генерируем автоматически
			$oShop_Company->guid = Core_Guid::get();
		}

		if (is_null($oCompany->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Company->user_id = $param['users_id'];
		}

		$oShop_Company->save();

		return $oShop_Company->id;
	}

	/**
	 * Удаление информации об организации
	 *
	 * @param int $shop_company_id идентификатор организации
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_company_id = 2;
	 *
	 * $result = $shop->DeleteCompany($shop_company_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return mixed результат выполнения запроса
	 */
	function DeleteCompany($shop_company_id)
	{
		$shop_company_id = intval($shop_company_id);
		Core_Entity::factory('Shop_Company', $shop_company_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Получение списка всех компаний
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $shop->GetAllCompanies();
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed ресурс или false
	 */
	function GetAllCompanies()
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_company_id'),
				array('name', 'shop_company_name'),
				array('description', 'shop_company_description'),
				array('tin', 'shop_company_inn'),
				array('kpp', 'shop_company_kpp'),
				array('psrn', 'shop_company_ogrn'),
				array('okpo', 'shop_company_okpo'),
				array('okved', 'shop_company_okved'),
				array('bic', 'shop_company_bik'),
				array('current_account', 'shop_company_account'),
				array('correspondent_account', 'shop_company_corr_account'),
				array('bank_name', 'shop_company_bank_name'),
				array('bank_address', 'shop_company_bank_address'),
				array('legal_name', 'shop_company_fio'),
				array('accountant_legal_name', 'shop_company_accountant_fio'),
				array('address', 'shop_company_address'),
				array('phone', 'shop_company_phone'),
				array('fax', 'shop_company_fax'),
				array('site', 'shop_company_site'),
				array('email', 'shop_company_email'),
				array('user_id', 'users_id'),
				array('guid', 'shop_company_guid')
			)
			->from('shop_company')
			->where('deleted', '=', 0)
			->orderBy('shop_company_name');

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Определение принадлежности заказа пользователю сайта
	 *
	 * @param int $shop_order_id идентификатор заказа
	 * @param int $site_users_id идентификатор пользователя
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 48;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $row = $shop->IsOrderOfThisSiteUser($shop_order_id, $site_users_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return bool
	 */
	function IsOrderOfThisSiteUser($shop_order_id, $site_users_id)
	{
		$shop_order_id = intval($shop_order_id);
		$site_users_id = intval($site_users_id);

		if (class_exists('SiteUsers'))
		{
			$aShop_Order = Core_Entity::factory('Siteuser', $site_users_id)->Shop_Orders;
			$aShop_Order->queryBuilder()->where('id', '=', $shop_order_id);
			$aShop_Orders = $aShop_Order->findAll();
			return count($aShop_Orders) > 0;
		}
		return FALSE;
	}

	/**
	 * Генерация XML для организации
	 *
	 * @param int $shop_company_id идентификатор организации
	 * @param array $company_row массив с параметрами, может быть указан вместо $shop_company_id
	 *
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_company_id = 1;
	 *
	 * $xmlData = $shop->GenXml4Company($shop_company_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xmlData);
	 * ?>
	 * </code>
	 * @return string с XML
	 */
	function GenXml4Company($shop_company_id, $company_row = FALSE)
	{
		if ($company_row)
		{
			$row = Core_Type_Conversion::toArray($company_row);
			$shop_company_id = Core_Type_Conversion::toInt($row['shop_company_id']);
			if (!$shop_company_id)
			{
				return '';
			}
		}
		else
		{
			$shop_company_id = intval($shop_company_id);
			$row = $this->GetCompany($shop_company_id);
			if (!$row)
			{
				return '';
			}
		}

		$xmlData = '<shop_company id="' . $shop_company_id . '">' . "\n";
		$xmlData .= '<name>' . str_for_xml($row['shop_company_name']) . '</name>' . "\n";
		$xmlData .= '<description>' . str_for_xml($row['shop_company_description']) . '</description>' . "\n";
		$xmlData .= '<inn>' . str_for_xml($row['shop_company_inn']) . '</inn>' . "\n";
		$xmlData .= '<kpp>' . str_for_xml($row['shop_company_kpp']) . '</kpp>' . "\n";
		$xmlData .= '<ogrn>' . str_for_xml($row['shop_company_ogrn']) . '</ogrn>' . "\n";
		$xmlData .= '<okpo>' . str_for_xml($row['shop_company_okpo']) . '</okpo>' . "\n";
		$xmlData .= '<okved>' . str_for_xml($row['shop_company_okved']) . '</okved>' . "\n";
		$xmlData .= '<bik>' . str_for_xml($row['shop_company_bik']) . '</bik>' . "\n";
		$xmlData .= '<account>' . str_for_xml($row['shop_company_account']) . '</account>' . "\n";
		$xmlData .= '<corr_account>' . str_for_xml($row['shop_company_corr_account']) . '</corr_account>' . "\n";
		$xmlData .= '<bank_name>' . str_for_xml($row['shop_company_bank_name']) . '</bank_name>' . "\n";
		$xmlData .= '<bank_address>' . str_for_xml($row['shop_company_bank_address']) . '</bank_address>' . "\n";
		$xmlData .= '<fio>' . str_for_xml($row['shop_company_fio']) . '</fio>' . "\n";
		$xmlData .= '<accountant_fio>' . str_for_xml($row['shop_company_accountant_fio']) . '</accountant_fio>' . "\n";
		$xmlData .= '<address>' . str_for_xml($row['shop_company_address']) . '</address>' . "\n";
		$xmlData .= '<phone>' . str_for_xml($row['shop_company_phone']) . '</phone>' . "\n";
		$xmlData .= '<fax>' . str_for_xml($row['shop_company_fax']) . '</fax>' . "\n";
		$xmlData .= '<site>' . str_for_xml($row['shop_company_site']) . '</site>' . "\n";
		$xmlData .= '<email>' . str_for_xml($row['shop_company_email']) . '</email>' . "\n";
		$xmlData .= "</shop_company>\n";

		return $xmlData;
	}

	/**
	 * Проверка существования поддиректории $dir_name в upload/shop, если нет - создает
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param string $dir_name директория, которую необходимо создать
	 * @param bool $link_path возвращает путь для отображения в ссылке (не используя CMS_FOLDER)
	 * @param bool $create создавать каталог, если он не существует
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $dir_name = 'Новая';
	 *
	 * $dir = $shop->CreateDirectory($shop_shops_id, $dir_name);
	 *
	 * // Распечатаем результат
	 * echo $dir
	 * ?>
	 * </code>
	 * @return mixed созданая или уже существующая директория, либо false, если не удалось создать.
	 */
	function CreateDirectory($shop_shops_id, $dir_name, $link_path = FALSE, $create = TRUE)
	{
		$shop_shops_id = intval($shop_shops_id);
		$dir_name = strval($dir_name);

		if (!$create)
		{
			if ($link_path)
			{
				return '/' . UPLOADDIR . "shop_$shop_shops_id/$dir_name/";
			}
			else
			{
				return CMS_FOLDER . UPLOADDIR . "shop_$shop_shops_id/$dir_name/";
			}
		}

		// Проверяем, существует ли shop
		if (!is_dir(CMS_FOLDER . UPLOADDIR . 'shop_' . $shop_shops_id))
		{
			if (!@mkdir(CMS_FOLDER . UPLOADDIR . 'shop_' . $shop_shops_id, CHMOD))
			{
				return FALSE;
			}
			@ chmod(CMS_FOLDER . UPLOADDIR . 'shop_' . $shop_shops_id, CHMOD);
		}

		// Проверяем, существует ли директория
		if (is_dir(CMS_FOLDER . UPLOADDIR . "shop_$shop_shops_id/$dir_name"))
		{
			// Директория уже существует, возвращаем её путь
			if ($link_path)
			{
				return '/' . UPLOADDIR . "shop_$shop_shops_id/$dir_name/";
			}
			else
			{
				return CMS_FOLDER . UPLOADDIR . "shop_$shop_shops_id/$dir_name/";
			}
		}

		// Создаем директорию только если указан параметр $create
		if ($create)
		{
			// Создаем директорию
			if (!@mkdir(CMS_FOLDER . UPLOADDIR . "shop_$shop_shops_id/" . $dir_name, CHMOD))
			{
				return FALSE;
			}
			@ chmod(CMS_FOLDER . UPLOADDIR . "shop_$shop_shops_id/" . $dir_name, CHMOD);
		}

		if ($link_path)
		{
			return '/' . UPLOADDIR . "shop_$shop_shops_id/$dir_name/";
		}
		else
		{
			return CMS_FOLDER . UPLOADDIR . "shop_$shop_shops_id/$dir_name/";
		}
	}

	/**
	 * Обновление данных о файле watermark'a для магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param string $shop_watermark_ext имя расширения файла watermark'a, если false - очищаем имя файла
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $shop_watermark_ext = false;
	 *
	 * $watermark = $shop->AssignShopWatermark($shop_shops_id, $shop_watermark_ext = false);
	 *
	 * // Распечатаем результат
	 * echo $watermark;
	 * ?>
	 * </code>
	 * @return mixed строку с именем файла или false
	 */
	function AssignShopWatermark($shop_shops_id, $shop_watermark_ext = FALSE)
	{
		$shop_shops_id = intval($shop_shops_id);

		$file_name = $shop_watermark_ext === FALSE
			? ''
			: "shop_watermark_{$shop_shops_id}.{$shop_watermark_ext}";

		$oShop = Core_Entity::factory('Shop')->find($shop_shops_id);

		if (!is_null($oShop->id))
		{
			$oShop->watermark_file = $file_name;
			$oShop->save();

			return $file_name;
		}

		return FALSE;
	}

	function DeleteShopWatermark($shop_shops_id)
	{
		// Извлекаем информацию об имени файла
		$oShop = Core_Entity::factory('Shop')->find($shop_shops_id);

		if (is_null($oShop->id))
		{
			return FALSE;
		}

		$file_name = $oShop->watermark_file;

		// Удаляем из БД
		$oShop->watermark_file = '';
		$oShop->save();

		// Данных о файле нет
		if (empty ($file_name))
		{
			return TRUE;
		}

		// Проверяем/создаем директорию для хранения файла watermark'a
		$uploaddir = $this->CreateDirectory($shop_shops_id, 'watermarks', FALSE);
		if (!$uploaddir)
		{
			// Ставим путь по умолчанию
			$uploaddir = CMS_FOLDER . UPLOADDIR;
		}

		$file_name = $uploaddir . $file_name;
		if (is_file($file_name))
		{
			return @ unlink($file_name);
		}

		return TRUE;
	}

	/**
	 * Установка имени изображения для товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @param string $shop_items_catalog_image название картинки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 170;
	 * $shop_items_catalog_image = 'shop_items_catalog_image170.jpg';
	 *
	 * $image = $shop->AssignItemImage($shop_items_catalog_item_id, $shop_items_catalog_image);
	 *
	 * // Распечатаем результат
	 * echo $image;
	 * ?>
	 * </code>
	 * @return mixed название картинки или false
	 */
	function AssignItemImage($shop_items_catalog_item_id, $shop_items_catalog_image)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		$oShop_Item = Core_Entity::factory('Shop_Item')->find($shop_items_catalog_item_id);
		if (is_null($oShop_Item->id))
		{
			return FALSE;
		}

		$oShop_Item->image_large = $shop_items_catalog_image;
		$oShop_Item->save();

		return $shop_items_catalog_image;
	}

	/**
	 * Отображение информации о продавце в клиентской части
	 *
	 * @param int $shop_sallers_id идентификатор продавца
	 * @param string $xsl_name имя XSL шаблона
	 */
	function ShowSaller($shop_sallers_id, $xsl_name)
	{
		$shop_sallers_id = intval($shop_sallers_id);

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= $this->GenXml4Saller($shop_sallers_id, FALSE);

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Установка имени изображения для группы товаров
	 *
	 * @param int $shop_groups_id идентификатор группы товаров
	 * @param string $shop_groups_image название картинки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_groups_id = 599;
	 * $shop_groups_image = '599.jpg';
	 *
	 * $image = $shop->AssignGroupImage($shop_groups_id, $shop_groups_image);
	 *
	 * // Распечатаем результат
	 * echo $image;
	 * ?>
	 * </code>
	 * @return mixed название картинки или false
	 */
	function AssignGroupImage($shop_groups_id, $shop_groups_image)
	{
		$shop_groups_id = intval($shop_groups_id);
		$oShop_Group = Core_Entity::factory('Shop_Group')->find($shop_groups_id);

		if(is_null($oShop_Group->id))
		{
			return FALSE;
		}

		$oShop_Group->image_large = $shop_groups_image;
		$oShop_Group->save();

		return $shop_groups_image;
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров сопутствующими товарами.
	 * Заполнению подвергается массив $this->CacheComments[shop_items_catalog_item_id][]
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 */
	function FillMemCacheComments($mas_items_in)
	{
		$mas_items_in = Core_Type_Conversion::toArray($mas_items_in);

		if (count($mas_items_in) > 0)
		{
			// Меняем местами значения и ключи массива
			$aTmp = array_flip($mas_items_in);

			// Вычислить пересечение массивов, сравнивая ключи
			$aTmpIntersect = array_intersect_key($aTmp, $this->CacheComments);

			if (count($aTmpIntersect) != count($mas_items_in))
			{
				// Заполянем комментарии пустыми массивами
				foreach ($mas_items_in as $key => $shop_items_catalog_item_id)
				{
					$this->CacheComments[$shop_items_catalog_item_id] = FALSE;
				}

				// для переданых элементов выбираем активные комментарии/оценки
				$queryBuilder = Core_QueryBuilder::select(
						array('shop_item_id', 'shop_items_catalog_item_id'),
						array('comments.id', 'shop_comment_id'),
						array('siteuser_id', 'site_users_id'),
						array('author', 'shop_comment_user_name'),
						array('email', 'shop_comment_user_email'),
						array('ip', 'shop_comment_user_ip'),
						array('subject', 'shop_comment_subject'),
						array('text', 'shop_comment_text'),
						array('grade', 'shop_comment_grade'),
						array('datetime', 'shop_comment_date_time'),
						array('active', 'shop_comment_active'),
						array('user_id', 'users_id')
					)
					->from('comments')
					->join('comment_shop_items', 'comment_shop_items.comment_id', '=', 'comments.id')
					->where('shop_item_id', 'IN', $mas_items_in)
					->where('active', '=', 1)
					->where('deleted', '=', 0);

				$aResult = $queryBuilder->execute()->asAssoc()->result();
				foreach($aResult as $row)
				{
					$row['shop_comment_is_comment'] = trim($row['shop_comment_user_name']) == '' && trim($row['shop_comment_subject']) == '' && trim($row['shop_comment_text']) == '' ? 0 : 1;
					$this->CacheComments[$row['shop_items_catalog_item_id']][] = $row;
				}

				/*
				 // Вызывает повышенную нагрузку
				 if (class_exists('Cache'))
				 {
				 $cache_name = 'SHOP_COMMENT';
				 $cache = & singleton('Cache');

				 foreach ($this->CacheComments as $key => $value)
				 {
				 $cache->Insert($key, $value, $cache_name);
				 }
				 }*/
			}
		}
	}

	/**
	 * Получение списка всех комментариев
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара, если не равен false, то метод
	 * вернет список комментариев, относящихся только к данному товару
	 * @param array $param массив дополнительных параметров
	 * - $param['shop_id'] идентификатор интернет-магазина
	 * - $param['shop_comment_active'] статус активности выбираемых элементов (0/1). Параметр может быть не задан.
	 * - $param['comments_begin'] параметр, определяющий порядковый номер комментария, с которого отображать комментарии (по умолчанию 0)
	 * - $param['comments_count'] параметр, определяющий число отображаемых комментариев
	 * - $param['comments_sort_field'] поле сортировки комментариев (по умолчанию shop_comment_date_time)
	 * - $param['comments_sort_order'] направление сортировки комментариев. 'ASC' - сортировка по возрастанию, 'DESC' (по умолчанию) - по убыванию, 'RAND()' - в произвольном порядке
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 *
	 * $resource = $shop->GetAllComments($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($resource);
	 * ?>
	 * </code>
	 * @return array или false - результат выборки
	 */
	function GetAllComments($shop_items_catalog_item_id, $param = array())
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
		$comments_sort_field = Core_Type_Conversion::toStr($param['comments_sort_field']);
		$comments_sort_order = Core_Type_Conversion::toStr($param['comments_sort_order']);
		$comments_begin = Core_Type_Conversion::toInt($param['comments_begin']);
		$comments_count = Core_Type_Conversion::toInt($param['comments_count']);

		$queryBuilder = Core_QueryBuilder::select(
				array('shop_item_id', 'shop_items_catalog_item_id'),
				array('comments.id', 'shop_comment_id'),
				array('comments.siteuser_id', 'site_users_id'),
				array('author', 'shop_comment_user_name'),
				array('email', 'shop_comment_user_email'),
				array('ip', 'shop_comment_user_ip'),
				array('subject', 'shop_comment_subject'),
				array('comments.text', 'shop_comment_text'),
				array('grade', 'shop_comment_grade'),
				array('comments.datetime', 'shop_comment_date_time'),
				array('comments.active', 'shop_comment_active'),
				array('comments.user_id', 'users_id')
			)
			->sqlCalcFoundRows()
			->from('comments')
			->join('comment_shop_items', 'comments.id', '=', 'comment_shop_items.comment_id')
			->where('comments.deleted', '=', 0);

		if ($comments_count > 0 && $comments_begin >= 0)
		{
			$queryBuilder->limit($comments_begin, $comments_count);
		}

		switch (strtoupper($comments_sort_order))
		{
			case 'ASC':
				$sql_sort_order = 'ASC';
			break;
			case 'RAND()':
				$sql_sort_order = 'RAND()';
			break;
			default:
				$sql_sort_order = 'DESC';
		}

		if ($sql_sort_order != 'RAND()')
		{
			$sql_sort_field = !empty($comments_sort_field)
				? $comments_sort_field
				: 'shop_comment_date_time';
		}
		else
		{
			$sql_sort_field = '';
		}

		$queryBuilder->orderBy($sql_sort_field, $sql_sort_order);

		if (isset($param['shop_id']))
		{
			$queryBuilder
				->join('shop_items', 'shop_items.id', '=', 'comment_shop_items.shop_item_id')
				->where('shop_items.shop_id', '=', intval($param['shop_id']))
				->where('shop_items.deleted', '=', 0);
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$kernel = & singleton('kernel');
			$cache_key = $shop_items_catalog_item_id . $kernel->implode_array($param);

			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ALL_COMMENT';
			if ($in_cache = $cache->GetCacheContent($cache_key, $cache_name))
			{
				$return = $in_cache['value'];
				if (isset($return['list']) && isset($return['count']))
				{
					// Восстанавливаем количество комментариев
					$this->SetCountAllComments($return['count']);
					return $return['list'];
				}
			}
		}

		if (!isset($this->CacheComments[$shop_items_catalog_item_id]) || Core_Type_Conversion::toInt($param['shop_comment_active']) == 0)
		{
			$this->CacheComments[$shop_items_catalog_item_id] = FALSE;

			$shop_items_catalog_item_id && $queryBuilder->where('comment_shop_items.shop_item_id', '=', $shop_items_catalog_item_id);

			// Если передана активность
			if (isset($param['shop_comment_active']))
			{
				$param['shop_comment_active'] = intval($param['shop_comment_active']);
				$queryBuilder->where('comments.active', '=', $param['shop_comment_active']);
			}

			$result = $queryBuilder->execute()->asAssoc()->getResult();

			// Определим количество элементов
			$count_comments = Core_QueryBuilder::select(array('FOUND_ROWS()', 'count'))
				->execute()->asAssoc()->current();
			$this->SetCountAllComments($count_comments['count']);

			while ($row = mysql_fetch_assoc($result))
			{
				$row['shop_comment_is_comment'] = trim($row['shop_comment_user_name']) == '' && trim($row['shop_comment_subject']) == '' && trim($row['shop_comment_text']) == '' ? 0 : 1;

				$this->CacheComments[$shop_items_catalog_item_id][] = $row;
			}
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($cache_key,
			array(
				'list' => $this->CacheComments[$shop_items_catalog_item_id],
				'count' => $this->GetCountAllComments()
			),
			$cache_name);
		}

		return $this->CacheComments[$shop_items_catalog_item_id];
	}

	/**
	 * Автоматизация оформления заказа, рекомендуется использовать в Handler'ах систем оплаты
	 *
	 * @param int $shop_id идентификатор магазина
	 * @param int $site_users_id идентификатор пользователя сайта, если false - определяем внутри метода
	 * @param int $system_of_pay_id идентификатор системы оплаты
	 * @param array &$order_row ВОЗВРАЩАЕТ информацию о заказе, в случае успешной вставки, иначе - false
	 * Может принимать значения:
	 * <br/>$order_row['status_of_pay'] - статус платежа, по умолчанию 0;
	 * <br/>$order_row['date_of_pay'] - дата платежа, по умолчанию пустая строка;
	 * <br/>$order_row['description'] - описание и системная информация, по умолчанию пустая строка.
	 * @param array $param массив дополнительных параметров
	 * - $param['ignore_delivery_price'] игнорировать нулевую цену доставки и добавлять ее в любом случае
	 * - $param['discount_name'] наименование скидки для включения в товары заказа, если не указано, то используется "Скидка"
	 * @return int результат выполнения метода,
	 * - идентификатор добавленного заказа в случае успеха
	 * - -1 ошибка вставки в БД
	 * - -2 возможно не найден магазин
	 * - -3 корзина пуста
	 */
	function ProcessOrder($shop_id, $site_users_id, $system_of_pay_id, & $order_row, $param = Array())
	{
		if (!$site_users_id) // 0 или false
		{
			// на случай, если пользователь имеет ID 0, тогда преобразуем его в ложь
			if ($site_users_id !== 0 && class_exists('SiteUsers'))
			{
				$SiteUsers = & singleton('SiteUsers');
				$site_users_id = $SiteUsers->GetCurrentSiteUser();
			}
			else
			{
				$site_users_id = FALSE;
			}
		}
		else
		{
			$site_users_id = intval($site_users_id);
		}

		$shop_id = intval($shop_id);
		$system_of_pay_id = intval($system_of_pay_id);

		// по умолчанию - не оплачен
		$order_row['status_of_pay'] = isset($order_row['status_of_pay'])
			? intval($order_row['status_of_pay'])
			: 0;

		!isset($param['ignore_delivery_price']) && $param['ignore_delivery_price'] = FALSE;

		// Получаем информацию о заказе
		$InfoArray = $this->GetOrderInfoArray($shop_id, $site_users_id);

		// Заполняем таблицу с заказанными товарами
		$items_array = $this->GetItemsFromCart($site_users_id, $shop_id, FALSE);

		// Есть информация о заказе
		if ($InfoArray)
		{
			// Есть товары в заказе
			if ($items_array)
			{
				// Идентификатор платежной системы
				$InfoArray['shop_system_of_pay_id'] = $system_of_pay_id;

				// Статус оплаты - 0, т.к. оплата при получении
				$InfoArray['status_of_pay'] = $order_row['status_of_pay'];

				// Дата платежа
				$InfoArray['date_of_pay'] = Core_Type_Conversion::toStr($order_row['date_of_pay']);

				// Описание и системная информация
				$InfoArray['description'] = Core_Type_Conversion::toStr($order_row['description']);

				// Пользовательский номер заказа
				if (isset($order_row['shop_order_account_number']))
				{
					$InfoArray['shop_order_account_number'] = $order_row['shop_order_account_number'];
				}

				// Записываем информацию в таблицу заказов
				if ($order_id = $this->InsertOrder($InfoArray))
				{
					$order_row = Core_Type_Conversion::toArray($InfoArray);

					$param_all_items = array();

					$param_array = array();
					$param_array['shop_id'] = $shop_id;
					$param_array['user_id'] = $site_users_id;

					foreach ($items_array as $value)
					{
						// Работаем только с не отложенными товарами (0 - товар в корзине, 1 - отложен).
						if ($value['shop_cart_flag_postpone'] == 0)
						{
							$param_item = array();
							$param_item['shop_items_catalog_item_id'] = $value['shop_items_catalog_item_id'];
							$param_item['shop_order_id'] = $order_id;
							$param_item['shop_order_items_quantity'] = $value['shop_cart_item_quantity'];
							$param_item['shop_warehouse_id'] = Core_Type_Conversion::toInt($value['shop_warehouse_id']);

							// Получаем цену товара
							$price_array = $this->GetPriceForUser($site_users_id, $param_item['shop_items_catalog_item_id'], array(), array('item_count' => $param_item['shop_order_items_quantity']));

							// В заказ идет цена товара со скидкой
							$param_item['shop_order_items_price'] = $price_array['price_discount'];

							// Выбираем наименование и артикул товара
							$row_item = $this->GetItem($param_item['shop_items_catalog_item_id']);

							if ($row_item)
							{
								$param_item['shop_order_items_name'] = $row_item['shop_items_catalog_name'];
								$param_item['shop_order_items_marking'] = $row_item['shop_items_catalog_marking'];
							}
							else
							{
								$param_item['shop_order_items_name'] = '';
								$param_item['shop_order_items_marking'] = '';
							}

							// Вставляем товары для заказа
							if ($this->InsertOrderItems($param_item))
							{
								$param_all_items[] = $param_item;

								// Удаляем данные из корзины для пользователя
								$param_array['item_id'] = $value['shop_items_catalog_item_id'];
								$this->DeleteCart($param_array);
							}
						}
					}

					if (Core_Type_Conversion::toFloat($InfoArray['order_discount']) > 0)
					{
						// Добавляем скидку, как отдельный товар с отрицательной ценой
						$param_item = array();
						$param_item['shop_items_catalog_item_id'] = 0;
						$param_item['shop_order_id'] = $order_id;
						$param_item['shop_order_items_quantity'] = 1;

						if (isset($param['discount_name']))
						{
							$discount_name = $param['discount_name'];
						}
						else
						{
							$discount_name = $GLOBALS['MSG_shops']['shop_order_item_discount'];
						}

						$param_item['shop_order_items_name'] = $discount_name;

						// Рассчиытваем % НДС для всего заказа (могут быть разные НДС для разных товаров)
						$tax_persent = Core_Type_Conversion::toFloat($InfoArray['order_discount_tax']) * 100
							/ ($InfoArray['order_discount'] - $InfoArray['order_discount_tax']);

						$param_item['shop_tax_rate'] = round($tax_persent);
						$param_item['shop_order_items_price'] = -Core_Type_Conversion::toFloat($InfoArray['order_discount']);
						$this->InsertOrderItems($param_item);
					}

					// Добавляем стоимость доставки, как отдельный товар.
					if ($InfoArray['delivery_price'] || $param['ignore_delivery_price'])
					{
						// Получаем информацию об условии доставки
						$cond_of_delivery_row = $this->GetCondOfDelivery(Core_Type_Conversion::toInt($InfoArray['shop_cond_of_delivery_id']));

						// Получаем информацию о доставке.
						$type_of_delivery_row = $this->GetTypeOfDelivery($cond_of_delivery_row['shop_type_of_delivery_id']);

						$param_item = array();
						$param_item['shop_items_catalog_item_id'] = 0;
						$param_item['shop_order_id'] = $order_id;
						$param_item['shop_order_items_quantity'] = 1;

						// Параметр shop_order_items_type, равный 1 указывает что товар - доставка
						$param_item['shop_order_items_type'] = 1;
						$param_item['shop_order_items_name'] = sprintf($GLOBALS['MSG_shops']['shop_order_item_delivery'], Core_Type_Conversion::toStr($type_of_delivery_row['shop_type_of_delivery_name']));
						$param_item['shop_order_items_price'] = Core_Type_Conversion::toFloat($InfoArray['delivery_price']);
						$param_item['shop_tax_rate'] = Core_Type_Conversion::toFloat($InfoArray['delivery_price_tax']);

						$this->InsertOrderItems($param_item);
					}

					// Если не установлен модуль пользователей сайта - записываем в сессию
					// идентификатор вставленного заказа, чтобы далее можно было посмотреть квитаницию
					// об оплате или счет.
					if (!class_exists('SiteUsers'))
					{
						$_SESSION['order_' . $order_id] = TRUE;
					}

					// Очищаем корзину на кукисах и сессиях.
					// Нельзя, т.к. есть еще отложенные товары в корзине
					//$this->ClearCookieAndSessionCart($shop_id);

					return $order_id;
				}
				else
				{
					$order_row = FALSE;
					return -1;
				}
			}
			else
			{
				$order_row = FALSE;
				return -3;
			}
		}
		else
		{
			$order_row = FALSE;
			return -2;
		}
	}

	/**
	 * Получение информации о скидке для заказов
	 *
	 * @param int $shop_order_discount_id идентификатор скидки для заказов
	 * @param array $param ассоциативный массив параметрв
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_discount_id = 1;
	 *
	 * $row = $shop->GetOrderDiscount($shop_order_discount_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed ассоциативный массив с результатом выборки или false
	 */
	function GetOrderDiscount($shop_order_discount_id, $param = array())
	{
		$shop_order_discount_id = intval($shop_order_discount_id);
		$param = Core_Type_Conversion::toArray($param);

		$cache_name = 'SHOP_ORDER_DISCOUNT';
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');

			if ($in_cache = $cache->GetCacheContent($shop_order_discount_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oShop_Purchase_Discount = Core_Entity::factory('Shop_Purchase_Discount')->find($shop_order_discount_id);

		$row = !is_null($oShop_Purchase_Discounts->id)
			? $this->getArrayShopPurchaseDiscount($oShop_Purchase_Discount)
			: FALSE;

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache->Insert($shop_order_discount_id, $row, $cache_name);
		}

		return $row;
	}

	/**
	 * Вставка/обновление информации о скидке для заказов
	 *
	 * @param array $param массив с параметрами скидки
	 * - int $param['shop_order_discount_id'] идентификатор скидки (указывается в случае обновления)
	 * - int $param['shop_shops_id'] идентификатор магазина
	 * - int $param['shop_currency_id'] идентификатор валюты
	 * - string $param['shop_order_discount_name'] название скидки
	 * - int $param['shop_order_discount_sum_from'] скидка активна для сумм товара начиная с данной
	 * - int $param['shop_order_discount_sum_to'] скидка активна для сумм товара не более данной
	 * - int $param['shop_order_discount_active'] активность скидки
	 * - int $param['shop_order_discount_active_from'] дата, начиная с которой данная скидка активна
	 * - int $param['shop_order_discount_active_to'] дата до которой активна скидка
	 * - int $param['shop_order_discount_type'] тип скидки (процент или фиксированная сумма)
	 * - int $param['shop_order_discount_value'] величина скидки
	 * - int $param['shop_order_discount_is_coupon'] применять скидку только к купонам - 1, обычная скидка - 0
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_currency_id'] = 1;
	 * $param['shop_order_discount_name'] = 'Новая скидка';
	 * $param['shop_order_discount_active_from'] = '01.09.2008';
	 * $param['shop_order_discount_active_to'] = '30.09.2008';
	 *
	 * $newid = $shop->InsertOrderDiscount($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленной/обновленной скидки или false
	 */
	function InsertOrderDiscount($param)
	{
		if (!isset($param['shop_order_discount_id']) || !$param['shop_order_discount_id'])
		{
			$param['shop_order_discount_id'] = NULL;
		}

		$oShop_Purchase_Discount = Core_Entity::factory('Shop_Purchase_Discount', $param['shop_order_discount_id']);

		isset($param['shop_shops_id']) && $oShop_Purchase_Discount->shop_id = intval($param['shop_shops_id']);
		isset($param['shop_currency_id']) && $oShop_Purchase_Discount->shop_currency_id = intval($param['shop_currency_id']);
		isset($param['shop_order_discount_name']) && $oShop_Purchase_Discount->name = $param['shop_order_discount_name'];
		isset($param['shop_order_discount_sum_from']) && $oShop_Purchase_Discount->min_amount = floatval($param['shop_order_discount_sum_from']);
		isset($param['shop_order_discount_sum_to']) && $oShop_Purchase_Discount->max_amount = floatval($param['shop_order_discount_sum_to']);
		isset($param['shop_order_discount_active']) && $oShop_Purchase_Discount->active = intval($param['shop_order_discount_active']);
		isset($param['shop_order_discount_active_from']) && $oShop_Purchase_Discount->start_datetime = $param['shop_order_discount_active_from'];
		isset($param['shop_order_discount_active_to']) && $oShop_Purchase_Discount->end_datetime = $param['shop_order_discount_active_to'];
		isset($param['shop_order_discount_type']) && $oShop_Purchase_Discount->type = intval($param['shop_order_discount_type']);
		isset($param['shop_order_discount_value']) && $oShop_Purchase_Discount->value = floatval($param['shop_order_discount_value']);
		isset($param['shop_order_discount_is_coupon']) && $oShop_Purchase_Discount->coupon = intval($param['shop_order_discount_is_coupon']);
		isset($param['shop_order_discount_count_from']) && $oShop_Purchase_Discount->min_count = intval($param['shop_order_discount_count_from']);
		isset($param['shop_order_discount_count_to']) && $oShop_Purchase_Discount->max_count = intval($param['shop_order_discount_count_to']);
		isset($param['shop_order_discount_logic_between_elements']) && $oShop_Purchase_Discount->mode = intval($param['shop_order_discount_logic_between_elements']);

		is_null($oShop_Purchase_Discount->id) && isset($param['users_id']) && $param['users_id'] && $oShop_Purchase_Discount->user_id = $param['users_id'];

		if (!is_null($oShop_Purchase_Discount->id) && class_exists('Cache'))
		{
			// Очистка файлового кэша
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ORDER_DISCOUNT';
			$cache->DeleteCacheItem($cache_name, $oShop_Purchase_Discount->id);
		}

		$oShop_Purchase_Discount->save();
		return $oShop_Purchase_Discount->id;
	}

	/**
	 * Удаление информации о скидке для заказов
	 *
	 * @param int $shop_order_discount_id идентификатор скидки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_discount_id = 1;
	 *
	 * $result = $shop->DeleteOrderDiscount($shop_order_discount_id));
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return mixed результат выполнения запроса
	 */
	function DeleteOrderDiscount($shop_order_discount_id)
	{
		$shop_order_discount_id = intval($shop_order_discount_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SHOP_ORDER_DISCOUNT';
			$cache->DeleteCacheItem($cache_name, $shop_order_discount_id);
		}

		return Core_Entity::factory('Shop_Purchase_Discount', $shop_order_discount_id)->markDeleted();
	}

	/**
	 * Получение информации обо всех скидках
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $resource = $shop->GetAllOrderDiscounts($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource результат выборки
	 */
	function GetAllOrderDiscounts($shop_shops_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_order_discount_id'),
				array('shop_id', 'shop_shops_id'),
				'shop_currency_id',
				array('name', 'shop_order_discount_name'),
				array('min_amount', 'shop_order_discount_sum_from'),
				array('max_amount', 'shop_order_discount_sum_to'),
				array('min_count', 'shop_order_discount_count_from'),
				array('max_count', 'shop_order_discount_count_to'),
				array('mode', 'shop_order_discount_logic_between_elements'),
				array('active', 'shop_order_discount_active'),
				array('start_datetime', 'shop_order_discount_active_from'),
				array('end_datetime', 'shop_order_discount_active_to'),
				array('type', 'shop_order_discount_type'),
				array('value', 'shop_order_discount_value'),
				array('coupon', 'shop_order_discount_is_coupon'),
				array('user_id', 'users_id')
			)
			->from('shop_purchase_discounts')
			->where('deleted', '=', 0);

		if ($shop_shops_id)
		{
			$shop_shops_id = intval($shop_shops_id);
			$queryBuilder->where('shop_id', '=', $shop_shops_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Список скидок на сумму заказа по заданным условиям
	 *
	 * @param array $param массив с дополнительными параметрами
	 * - int $param['shop_shops_id'] идентификатор магазина
	 * - int $param['shop_order_discount_active'] активность неактивность скидки
	 * - int $param['date'] дата, должна быть в интервале действия скидки
	 * - int $param['shop_order_discount_is_coupon'] скидка для купона
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_order_discount_active'] = 1;
	 * $param['date'] = date('Y-m-d H:i:s');
	 *
	 * $resource = $shop->GetOrderDiscountWithConditions($param);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource результат выборки
	 */
	function GetOrderDiscountWithConditions($param)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_order_discount_id'),
				array('shop_id', 'shop_shops_id'),
				'shop_currency_id',
				array('name', 'shop_order_discount_name'),
				array('min_amount', 'shop_order_discount_sum_from'),
				array('max_amount', 'shop_order_discount_sum_to'),
				array('min_count', 'shop_order_discount_count_from'),
				array('max_count', 'shop_order_discount_count_to'),
				array('mode', 'shop_order_discount_logic_between_elements'),
				array('active', 'shop_order_discount_active'),
				array('start_datetime', 'shop_order_discount_active_from'),
				array('end_datetime', 'shop_order_discount_active_to'),
				array('type', 'shop_order_discount_type'),
				array('value', 'shop_order_discount_value'),
				array('coupon', 'shop_order_discount_is_coupon'),
				array('user_id', 'users_id')
			)
			->from('shop_purchase_discounts')
			->where('deleted', '=', 0);

		// Идентификатор магазина
		if (isset($param['shop_shops_id']))
		{
			$queryBuilder->where('shop_id', '=', intval($param['shop_shops_id']));
		}

		// Активность
		if (isset($param['shop_order_discount_active']))
		{
			$queryBuilder->where('active', '=', intval($param['shop_order_discount_active']));
		}

		// Дата
		if (isset($param['date']))
		{
			$value = Core_Type_Conversion::toStr($param['date']);
			$queryBuilder
				->where('start_datetime', '<=', $value)
				->open()
				->where('end_datetime', '>=', $value)
				->setOr()
				->where('end_datetime', '=', '0000-00-00 00:00:00')
				->close();
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Расчет скидки на сумму товара, в соответствии со списком скидок, доступных для указанного магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param float $sum сумма заказа
	 * @param int $count количество товаров в заказе
	 * @param string $shop_coupon_text текст купона, если таковой есть
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $sum = 1500;
	 *
	 * $discount = $shop->GetOrderDiscountForSumAndCount($shop_shops_id, $sum);
	 *
	 * // Распечатаем результат
	 * echo $discount;
	 * ?>
	 * </code>
	 * @return float величина скидки, -1 в случае ошибки
	 */
	function GetOrderDiscountForSumAndCount($shop_shops_id, $sum, $count, $shop_coupon_text = '')
	{
		$shop_shops_id = intval($shop_shops_id);
		$sum = floatval($sum);
		$count = intval($count);

		if ($sum <= 0 || $count <= 0)
		{
			return 0;
		}

		$row_shop = $this->GetShop($shop_shops_id);
		if (!$row_shop)
		{
			return 0;
		}

		// Идентификатор скидки по купону
		$coupon_discount_id = 0;

		// Получаем данные о купоне
		if (!empty ($shop_coupon_text))
		{
			$row = $this->GetCouponByText($shop_coupon_text, TRUE, array('shop_shops_id' => $shop_shops_id));

			if ($row)
			{
				$coupon_discount_id = Core_Type_Conversion::toInt($row['shop_order_discount_id']);
			}
		}

		// Извлекаем все активные скидки, доступные для текущей даты
		$discount_param['shop_shops_id'] = $shop_shops_id;
		$discount_param['shop_order_discount_active'] = 1;
		$discount_param['date'] = date('Y-m-d H:i:s');
		$dicount_resource = $this->GetOrderDiscountWithConditions($discount_param);

		$discount = 0;
		if (mysql_num_rows($dicount_resource) > 0)
		{
			while ($discount_row = mysql_fetch_assoc($dicount_resource))
			{
				// Определяем коэффициент перерасчета
				$currency_k = $this->GetCurrencyCoefficientToShopCurrency($discount_row['shop_currency_id'], $row_shop['shop_currency_id']);

				// Нижний предел скидки
				$discount_from = $currency_k * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_sum_from']);

				// Верхний предел скидки
				$discount_to = $currency_k * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_sum_to']);

				// Пределы по количеству
				$count_from = Core_Type_Conversion::toInt($discount_row['shop_order_discount_count_from']);

				$count_to = Core_Type_Conversion::toInt($discount_row['shop_order_discount_count_to']);

				// Получаем логику связки (И/ИЛИ)
				$shop_order_discount_logic_between_elements = Core_Type_Conversion::toInt($discount_row['shop_order_discount_logic_between_elements']);

				// Если ИЛИ
				if($shop_order_discount_logic_between_elements == 1)
				{
					if (($sum >= $discount_from
					&& ($sum < $discount_to || $discount_to == 0)
					&& !Core_Type_Conversion::toInt($discount_row['shop_order_discount_is_coupon'])
					|| Core_Type_Conversion::toInt($discount_row['shop_order_discount_id']) == $coupon_discount_id)
					||
					($count >= $count_from
					&& ($count < $count_to || $count_to == 0)
					&& !Core_Type_Conversion::toInt($discount_row['shop_order_discount_is_coupon'])
					|| Core_Type_Conversion::toInt($discount_row['shop_order_discount_id'])
					== $coupon_discount_id))
					{
						// Определяем тип скидки
						switch (Core_Type_Conversion::toInt($discount_row['shop_order_discount_type']))
						{
							case 0 : // Процент
								// Учитываем перерасчет суммы скидки в валюту магазина
								$discount += $currency_k * $sum * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_value']) / 100;
							break;
							case 1 : // Фиксированная скидка
								// Учитываем перерасчет суммы скидки в валюту магазина
								$discount += $currency_k * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_value']);
							break;
						}
					}
				}
				// И
				else
				{
					if (($sum >= $discount_from
					&& ($sum < $discount_to || $discount_to == 0)
					&& (!Core_Type_Conversion::toInt($discount_row['shop_order_discount_is_coupon'])
					|| Core_Type_Conversion::toInt($discount_row['shop_order_discount_id'])
					== $coupon_discount_id))
					&&
					($count >= $count_from
					&& ($count < $count_to || $count_to == 0)
					&& (!Core_Type_Conversion::toInt($discount_row['shop_order_discount_is_coupon'])
					|| Core_Type_Conversion::toInt($discount_row['shop_order_discount_id'])
					== $coupon_discount_id)))
					{
						// Определяем тип скидки
						switch (Core_Type_Conversion::toInt($discount_row['shop_order_discount_type']))
						{
							case 0 : // Процент
								// Учитываем перерасчет суммы скидки в валюту магазина
								$discount += $currency_k * $sum * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_value']) / 100;
							break;
							case 1 : // Фиксированная скидка
								// Учитываем перерасчет суммы скидки в валюту магазина
								$discount += $currency_k * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_value']);
							break;
						}
					}
				}
			}
		}

		if ($sum - $discount < 0)
		{
			return -1;
		}

		return $discount;
	}

	/**
	 * Устаревший метод. Заменен на GetOrderDiscountForSumAndCount()
		* Метод расчитывает скидку на сумму товара, в соответствии со списком скидок, доступных указанного магазина
		*
		* @param int $shop_shops_id идентификатор магазина
		* @param float $sum сумма заказа
		* @param string $shop_coupon_text текст купона, если таковой есть
		* <code>
		* <?php
		* $shop = new shop();
		*
		* $shop_shops_id = 1;
		* $sum = 1500;
		*
		* $discount = $shop->GetOrderDiscountForSum($shop_shops_id, $sum);
		*
		* // Распечатаем результат
		* echo $discount;
		* ?>
		* </code>
		* @return float величина скидки, -1 в случае ошибки
		*/
	function GetOrderDiscountForSum($shop_shops_id, $sum, $shop_coupon_text = '')
	{
		$shop_shops_id = intval($shop_shops_id);
		$sum = floatval($sum);
		$shop_coupon_text = Core_Type_Conversion::toStr($shop_coupon_text);

		if ($sum <= 0)
		{
			return 0;
		}

		$row_shop = $this->GetShop($shop_shops_id);
		if (!$row_shop)
		{
			return 0;
		}

		// Идентификатор скидки по купону
		$coupon_discount_id = 0;

		// Получаем данные о купоне
		if (!empty ($shop_coupon_text))
		{
			$row = $this->GetCouponByText($shop_coupon_text, TRUE, array('shop_shops_id' => $shop_shops_id));
			if ($row)
			{
				$coupon_discount_id = Core_Type_Conversion::toInt($row['shop_order_discount_id']);
			}
		}

		// Извлекаем все активные скидки, доступные для текущей даты
		$discount_param['shop_shops_id'] = $shop_shops_id;
		$discount_param['shop_order_discount_active'] = 1;
		$discount_param['date'] = date('Y-m-d H:i:s');
		$dicount_resource = $this->GetOrderDiscountWithConditions($discount_param);

		$discount = 0;
		if (mysql_num_rows($dicount_resource) > 0)
		{
			while ($discount_row = mysql_fetch_assoc($dicount_resource))
			{
				// Определяем коэффициент перерасчета
				$currency_k = $this->GetCurrencyCoefficientToShopCurrency($discount_row['shop_currency_id'], $row_shop['shop_currency_id']);

				// Нижний предел скидки
				$discount_from = $currency_k * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_sum_from']);
				// Верхний предел скидки
				$discount_to = $currency_k * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_sum_to']);

				if ($sum >= $discount_from && ($sum < $discount_to || $discount_to == 0) && (!$discount_row['shop_order_discount_is_coupon']) || $discount_row['shop_order_discount_id'] == $coupon_discount_id)
				{
					// Определяем тип скидки
					switch (Core_Type_Conversion::toInt($discount_row['shop_order_discount_type']))
					{
						case 0 : // Процент
						{
							// Учитываем перерасчет суммы скидки в валюту магазина
							$discount += $currency_k * $sum * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_value']) / 100;
							break;
						}
						case 1 : // Фиксированная скидка
						{
							// Учитываем перерасчет суммы скидки в валюту магазина
							$discount += $currency_k * Core_Type_Conversion::toFloat($discount_row['shop_order_discount_value']);
							break;
						}
					}
				}
			}
		}

		if ($sum - $discount < 0)
		{
			return -1;
		}

		return $discount;
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров сопутствующими товарами.
	 * Заполнению подвергается массив $this->CacheModificationItems[shop_items_catalog_modification_id][]
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 * @param array $param массив дополнительных параметров
	 */
	function FillMemCacheModificationItems($mas_items_in, $param = array())
	{
		$mas_items_in = Core_Type_Conversion::toArray($mas_items_in);

		if (count($mas_items_in) > 0)
		{
			// Меняем местами значения и ключи массива
			$aTmp = array_flip($mas_items_in);

			// Вычисляем пересечение массивов, сравнивая ключи
			$aTmpIntersect = array_intersect_key($aTmp, $this->CacheModificationItems);

			if (count($aTmpIntersect) != count($mas_items_in))
			{
				// Заполянем товары пустыми массивами
				foreach ($mas_items_in as $key => $shop_items_catalog_item_id)
				{
					$this->CacheModificationItems[$shop_items_catalog_item_id] = FALSE;
				}

				$queryBuilder = Core_QueryBuilder::select(
						array('id', 'shop_items_catalog_item_id'),
						array('shortcut_id', 'shop_items_catalog_shortcut_id'),
						'shop_tax_id',
						array('shop_seller_id', 'shop_sallers_id'),
						array('shop_group_id', 'shop_groups_id'),
						array('shop_currency_id', 'shop_currency_id'),
						array('shop_id', 'shop_shops_id'),
						array('shop_producer_id', 'shop_producers_list_id'),
						array('shop_measure_id', 'shop_mesures_id'),
						array('type', 'shop_items_catalog_type'),
						array('name', 'shop_items_catalog_name'),
						array('marking', 'shop_items_catalog_marking'),
						array('vendorcode', 'shop_vendorcode'),
						array('description', 'shop_items_catalog_description'),
						array('text', 'shop_items_catalog_text'),
						array('image_large', 'shop_items_catalog_image'),
						array('image_small', 'shop_items_catalog_small_image'),
						array('weight', 'shop_items_catalog_weight'),
						array('price', 'shop_items_catalog_price'),
						array('active', 'shop_items_catalog_is_active'),
						array('siteuser_group_id', 'shop_items_catalog_access'),
						array('sorting', 'shop_items_catalog_order'),
						array('path', 'shop_items_catalog_path'),
						array('seo_title', 'shop_items_catalog_seo_title'),
						array('seo_description', 'shop_items_catalog_seo_description'),
						array('seo_keywords', 'shop_items_catalog_seo_keywords'),
						array('indexing', 'shop_items_catalog_indexation'),
						array('image_small_height', 'shop_items_catalog_small_image_height'),
						array('image_small_width', 'shop_items_catalog_small_image_width'),
						array('image_large_height', 'shop_items_catalog_big_image_height'),
						array('image_large_width', 'shop_items_catalog_big_image_width'),
						array('yandex_market', 'shop_items_catalog_yandex_market_allow'),
						array('yandex_market_bid', 'shop_items_catalog_yandex_market_bid'),
						array('yandex_market_cid', 'shop_items_catalog_yandex_market_cid'),
						array('yandex_market_sales_notes', 'shop_items_catalog_yandex_market_sales_notes'),
						array('siteuser_id', 'site_users_id'),
						array('datetime', 'shop_items_catalog_date_time'),
						array('modification_id', 'shop_items_catalog_modification_id'),
						array('guid', 'shop_items_cml_id'),
						array('start_datetime', 'shop_items_catalog_putoff_date'),
						array('end_datetime', 'shop_items_catalog_putend_date'),
						array('showed', 'shop_items_catalog_show_count'),
						array('user_id', 'users_id')
					)
					->from('shop_items')
					->where('modification_id', 'IN', $mas_items_in)
					->where('deleted', '=', 0);

				if (isset($param['shop_items_catalog_is_active']) && intval($param['shop_items_catalog_is_active']) == 0)
				{
					$queryBuilder->where('active', '=', 0);
				}
				// если параметр не передан, берем только активные
				else
				{
					$queryBuilder->where('active', '=', 1);
				}

				$shop_id = Core_Type_Conversion::toInt($param['shop_id']);

				if (isset($param['items_order']))
				{
					$items_order = trim(Core_Type_Conversion::toStr($param['items_order']));
					$items_order = strtolower($items_order);

					// неправильно задали название сортировки
					if ($items_order != 'asc' && $items_order != 'desc' && $items_order != 'rand')
					{
						$items_order = 'ASC';
					}

					if ($items_order == 'rand')
					{
						$items_order = 'Rand()';
						$param['items_field_order'] = '';
					}
				}
				else
				{
					// Если порядок сортировки не передан - берем из полей магазина
					$shop_row = $this->GetShop($shop_id);

					if ($shop_row)
					{
						switch ($shop_row['shop_sort_order_type'])
						{
							case 0 :
								$items_order = 'Asc';
							break;
							default :
								$items_order = 'Desc';
							break;
						}
					}
					else
					{
						$items_order = 'Asc';
					}
				}

				if (isset($param['items_field_order']))
				{
					$queryBuilder
						->orderBy('sorting', $items_order)
						->orderBy('name');
				}
				else
				{
					// Если поле сортирвоки не передан - берем из полей магазина
					$shop_row = $this->GetShop($shop_id);

					if ($shop_row)
					{
						switch ($shop_row['shop_sort_order_field'])
						{
							case 0 :
								$queryBuilder->orderBy('datetime', $items_order);
							break;
							case 1 :
								$queryBuilder->orderBy('name', $items_order);
							break;
							default :
								$queryBuilder
									->orderBy('sorting', $items_order)
									->orderBy('name');
							break;
						}
					}
					else
					{
						$queryBuilder
							->orderBy('sorting', $items_order)
							->orderBy('name');
					}
				}

				$aResult = $queryBuilder->execute()->asAssoc()->result();
				foreach ($aResult as $aTmp)
				{
					$this->CacheModificationItems[$aTmp['shop_items_catalog_modification_id']][] = $aTmp;
				}
			}
		}
	}

	/**
	 * Информация обо всех модификациях данного товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @param array $param массив дополнительных параметров
	 * - $param['shop_items_catalog_is_active'] статус активности выбираемых элементов (0/1). Параметр может быть не задан.
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * @return mixed ассоциативный массив с информацией о модификации товаров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 *
	 * $array = $shop->GetAllModificationItems($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($array);
	 *
	 * ?>
	 * </code>
	 */
	function GetAllModificationItems($shop_items_catalog_item_id, $param = array())
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		if (isset($this->CacheModificationItems[$shop_items_catalog_item_id]) && !isset($param['cache_off']))
		{
			return $this->CacheModificationItems[$shop_items_catalog_item_id];
		}

		// по умолчанию показываем только активные элементы
		if (!isset($param['show_catalog_item_type']))
		{
			$param['show_catalog_item_type'] = array('active');
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$kernel = & singleton('kernel');
			$cache_key = $shop_items_catalog_item_id . $kernel->implode_array($param);
			$cache_name = 'SHOP_ALL_MODIFICATION_ITEMS';

			if ($in_cache = $cache->GetCacheContent($cache_key, $cache_name))
			{
				$this->CacheModificationItems[$shop_items_catalog_item_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}

		$this->CacheModificationItems[$shop_items_catalog_item_id] = FALSE;

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_items_catalog_item_id'),
				array('shortcut_id', 'shop_items_catalog_shortcut_id'),
				'shop_tax_id',
				array('shop_seller_id', 'shop_sallers_id'),
				array('shop_group_id', 'shop_groups_id'),
				array('shop_currency_id', 'shop_currency_id'),
				array('shop_id', 'shop_shops_id'),
				array('shop_producer_id', 'shop_producers_list_id'),
				array('shop_measure_id', 'shop_mesures_id'),
				array('type', 'shop_items_catalog_type'),
				array('name', 'shop_items_catalog_name'),
				array('marking', 'shop_items_catalog_marking'),
				array('vendorcode', 'shop_vendorcode'),
				array('description', 'shop_items_catalog_description'),
				array('text', 'shop_items_catalog_text'),
				array('image_large', 'shop_items_catalog_image'),
				array('image_small', 'shop_items_catalog_small_image'),
				array('weight', 'shop_items_catalog_weight'),
				array('price', 'shop_items_catalog_price'),
				array('active', 'shop_items_catalog_is_active'),
				array('siteuser_group_id', 'shop_items_catalog_access'),
				array('sorting', 'shop_items_catalog_order'),
				array('path', 'shop_items_catalog_path'),
				array('seo_title', 'shop_items_catalog_seo_title'),
				array('seo_description', 'shop_items_catalog_seo_description'),
				array('seo_keywords', 'shop_items_catalog_seo_keywords'),
				array('indexing', 'shop_items_catalog_indexation'),
				array('image_small_height', 'shop_items_catalog_small_image_height'),
				array('image_small_width', 'shop_items_catalog_small_image_width'),
				array('image_large_height', 'shop_items_catalog_big_image_height'),
				array('image_large_width', 'shop_items_catalog_big_image_width'),
				array('yandex_market', 'shop_items_catalog_yandex_market_allow'),
				array('yandex_market_bid', 'shop_items_catalog_yandex_market_bid'),
				array('yandex_market_cid', 'shop_items_catalog_yandex_market_cid'),
				array('yandex_market_sales_notes', 'shop_items_catalog_yandex_market_sales_notes'),
				array('siteuser_id', 'site_users_id'),
				array('datetime', 'shop_items_catalog_date_time'),
				array('modification_id', 'shop_items_catalog_modification_id'),
				array('guid', 'shop_items_cml_id'),
				array('start_datetime', 'shop_items_catalog_putoff_date'),
				array('end_datetime', 'shop_items_catalog_putend_date'),
				array('showed', 'shop_items_catalog_show_count'),
				array('user_id', 'users_id')
			)
			->from('shop_items')
			->where('deleted', '=', 0);

		if ($shop_items_catalog_item_id)
		{
			$queryBuilder->where('modification_id', '=', $shop_items_catalog_item_id);
		}

		// Если передана активность
		if (isset($param['shop_items_catalog_is_active']))
		{
			$queryBuilder->where('active', '=', intval($param['shop_items_catalog_is_active']));
		}

		$item_row = $this->GetItem($shop_items_catalog_item_id);
		$shop_row = $this->GetShop(Core_Type_Conversion::toInt($item_row['shop_shops_id']));

		// Направление сортировки
		if (isset($param['items_order']))
		{
			$items_order = trim(Core_Type_Conversion::toStr($param['items_order']));
			$items_order = strtolower($items_order);

			// неправильно задали название сортировки
			if (!in_array($items_order, array('asc', 'desc', 'rand')))
			{
				$items_order = 'ASC';
			}

			if ($items_order == 'rand')
			{
				$items_order = 'Rand()';
				$param['items_field_order'] = '';
			}
		}
		else
		{
			// Если порядок сортировки не передан - берем из полей магазина
			if ($shop_row)
			{
				switch ($shop_row['shop_sort_order_type'])
				{
					case 0 :
						$items_order = 'Asc';
						break;
					default :
						$items_order = 'Desc';
					break;
				}
			}
			else
			{
				$items_order = 'Asc';
			}
		}

		// Поле сортировки
		if (isset($param['items_field_order']))
		{
			$items_field_order = Core_Type_Conversion::toStr($param['items_field_order']);
			$queryBuilder->orderBy($items_field_order, $items_order);
		}
		else
		{
			// Если поле сортирвоки не передан - берем из полей магазина
			if ($shop_row)
			{
				switch ($shop_row['shop_sort_order_field'])
				{
					case 0 :
						$queryBuilder->orderBy('shop_items_catalog_date_time');
					break;
					case 1 :
						$queryBuilder->orderBy('shop_items_catalog_name');
					break;
					default :
						$queryBuilder
							->orderBy('shop_items_catalog_order', $items_order)
							->orderBy('shop_items_catalog_name');
					break;
				}
			}
			else
			{
				$queryBuilder
					->orderBy('shop_items_catalog_order', $items_order)
					->orderBy('shop_items_catalog_name');
			}
		}
		$current_date = date('Y-m-d H:i:s');

		// Если не содержит putend_date - ограничиваем по дате окончания публикации
		if (!in_array('putend_date', $param['show_catalog_item_type']))
		{
			$queryBuilder
				->open()
				->where('end_datetime', '>=', $current_date)
				->setOr()
				->where('end_datetime', '=', '0000-00-00 00:00:00')
				->close();
		}

		// если не содержит putoff_date - ограничиваем по дате начала публикации
		if (!in_array('putoff_date', $param['show_catalog_item_type']))
		{
			$queryBuilder->where('start_datetime', '<=', $current_date);
		}

		$this->CacheModificationItems[$shop_items_catalog_item_id] = $queryBuilder->execute()->asAssoc()->result();

		// Запись в файловый кэш
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($cache_key, $this->CacheModificationItems[$shop_items_catalog_item_id], $cache_name);
		}

		return $this->CacheModificationItems[$shop_items_catalog_item_id];
	}

	/**
	 * Получение информации о купоне
	 *
	 * @param int $shop_coupon_id идентификатор купона
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_coupon_id = 1;
	 *
	 * $row = $shop->GetCoupon($shop_coupon_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed данные о купоне или false
	 */
	function GetCoupon($shop_coupon_id)
	{
		$shop_coupon_id = intval($shop_coupon_id);
		$oShop_Purchase_Discount_Coupon = Core_Entity::factory('Shop_Purchase_Discount_Coupon')->find($shop_coupon_id);

		if (is_null($oShop_Purchase_Discount_Coupon->id))
		{
			return FALSE;
		}

		return $this->getArrayShopPurchaseDiscountCoupon($oShop_Purchase_Discount_Coupon);
	}

	/**
	 * Вставка/обновление информации о купоне
	 *
	 * @param array $param массив с данными о купоне
	 * - int $param['shop_coupon_id'] идентификатор купона
	 * - int $param['shop_order_discount_id'] идентификатор скидки на сумму заказа
	 * - int $param['shop_shops_id'] идентификатор магазина
	 * - str $param['shop_coupon_name'] название купона
	 * - int $param['shop_coupon_active'] активность купона
	 * - int $param['shop_coupon_count'] количество купонов
	 * - int $param['shop_coupon_text'] текст купона
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_order_discount_id'] = 1;
	 * $param['shop_coupon_name'] = 'Новый купон';
	 * $param['shop_coupon_text'] = '868-570-864-822';
	 * $param['shop_coupon_count'] = 11;
	 * $param['shop_coupon_active'] = 1;
	 *
	 * $newid = $shop->InsertCoupon($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленного/обновленного купона или false
	 */
	function InsertCoupon($param)
	{
		if (!isset($param['shop_coupon_id']) || !$param['shop_coupon_id'])
		{
			$param['shop_coupon_id'] = NULL;
		}

		$oShop_Purchase_Discount_Coupon = Core_Entity::factory('Shop_Purchase_Discount_Coupon', $param['shop_coupon_id']);

		$shop_shops_id = Core_Type_Conversion::toInt($param['shop_shops_id']);
		$shop_coupon_text = Core_Type_Conversion::toStr($param['shop_coupon_text']);

		// Проверяем, существует ли купон с таким же текстом
		$queryBuilder = Core_QueryBuilder::select()
			->from('shop_purchase_discounts')
			->join('shop_purchase_discount_coupons', 'shop_purchase_discount_id', '=', 'shop_purchase_discounts.id')
			->where('text', '=', $shop_coupon_text)
			->where('shop_purchase_discounts.deleted', '=', 0)
			->where('shop_purchase_discount_coupons.deleted', '=', 0);

		$shop_shops_id > 0 && $queryBuilder->where('shop_id', '=', $shop_shops_id);

		$count = $queryBuilder->execute()->getNumRows();

		if ($count != 0 && is_null($oShop_Purchase_Discount_Coupon->id))
		{
			return -1;
		}

		isset($param['shop_order_discount_id']) && $oShop_Purchase_Discount_Coupon->shop_purchase_discount_id = intval($param['shop_order_discount_id']);
		isset($param['shop_coupon_name']) && $oShop_Purchase_Discount_Coupon->name = $param['shop_coupon_name'];
		isset($param['shop_coupon_active']) && $oShop_Purchase_Discount_Coupon->active = intval($param['shop_coupon_active']);
		isset($param['shop_coupon_count']) && $oShop_Purchase_Discount_Coupon->count = intval($param['shop_coupon_count']);
		isset($param['shop_coupon_text']) && $oShop_Purchase_Discount_Coupon->text = $param['shop_coupon_text'];

		is_null($oShop_Purchase_Discount_Coupon->id) && isset($param['users_id']) && $param['users_id'] && $oShop_Purchase_Discount_Coupon->user_id = $param['users_id'];
		$oShop_Purchase_Discount_Coupon->save();

		return $oShop_Purchase_Discount_Coupon->id;
	}

	/**
	 * Удаление информации о купоне
	 *
	 * @param int $shop_coupon_id идентификатор купона
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_coupon_id = 1;
	 *
	 * $result = $shop->DeleteCoupon($shop_coupon_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return mixed результат выполнения запроса
	 */
	function DeleteCoupon($shop_coupon_id)
	{
		$shop_coupon_id = intval($shop_coupon_id);
		Core_Entity::factory('Shop_Purchase_Discount_Coupon')->markDeleted();
		return TRUE;
	}

	/**
	 * Список купонов по заданным условиям
	 *
	 * @param array $param список дополнительных параметров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param = array();
	 *
	 * $resource = $shop->GetAllCoupons($param);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource ресурс с данными выборки
	 */
	function GetAllCoupons($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_coupon_id'),
				array('shop_purchase_discount_id', 'shop_order_discount_id'),
				array('name', 'shop_coupon_name'),
				array('active', 'shop_coupon_active'),
				array('count', 'shop_coupon_count'),
				array('text', 'shop_coupon_text'),
				array('user_id', 'users_id')
			)
			->from('shop_purchase_discount_coupons')
			->where('deleted', '=', 0)
			->orderBy('name');

		isset($param['shop_coupon_active']) && $queryBuilder->active = intval($param['shop_coupon_active']);
		isset($param['shop_order_discount_id']) && $queryBuilder->shop_purchase_discount_id = intval($param['shop_order_discount_id']);
		isset($param['shop_coupon_text']) && $queryBuilder->text = $param['shop_coupon_text'];
		isset($param['shop_coupon_name']) && $queryBuilder->name = $param['shop_coupon_name'];

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Возвращает информацию о купоне по его тексту
	 *
	 * @param string $shop_coupon_text текст купона
	 * @param bool $shop_coupon_active активность купона, false - все купоны, т.е. активные и не активные
	 * @param array $param массив дополнительных свойств
	 * - $param['shop_shops_id'] идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_coupon_text = '868-570-864-820';
	 *
	 * $row = $shop->GetCouponByText($shop_coupon_text);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed результат выборки в виде ассоциативного массива или false
	 */
	function GetCouponByText($shop_coupon_text, $shop_coupon_active = FALSE, $param = array())
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('shop_purchase_discounts.id', 'shop_order_discount_id'),
				array('shop_id', 'shop_shops_id'),
				'shop_currency_id',
				array('shop_purchase_discounts.name', 'shop_order_discount_name'),
				array('min_amount', 'shop_order_discount_sum_from'),
				array('max_amount', 'shop_order_discount_sum_to'),
				array('min_count', 'shop_order_discount_count_from'),
				array('max_count', 'shop_order_discount_count_to'),
				array('mode', 'shop_order_discount_logic_between_elements'),
				array('shop_purchase_discounts.active', 'shop_order_discount_active'),
				array('start_datetime', 'shop_order_discount_active_from'),
				array('end_datetime', 'shop_order_discount_active_to'),
				array('type', 'shop_order_discount_type'),
				array('value', 'shop_order_discount_value'),
				array('coupon', 'shop_order_discount_is_coupon'),
				array('shop_purchase_discount_coupons.id', 'shop_coupon_id'),
				array('shop_purchase_discount_coupons.name', 'shop_coupon_name'),
				array('shop_purchase_discount_coupons.active', 'shop_coupon_active'),
				array('count', 'shop_coupon_count'),
				array('text', 'shop_coupon_text'),
				array('shop_purchase_discounts.user_id', 'users_id')
			)
			->from('shop_purchase_discounts')
			->join('shop_purchase_discount_coupons', 'shop_purchase_discount_coupons.shop_purchase_discount_id', '=', 'shop_purchase_discounts.id')
			->where('text', '=', $shop_coupon_text)
			->where('shop_purchase_discounts.deleted', '=', 0)
			->where('shop_purchase_discount_coupons.deleted', '=', 0);

		if ($shop_coupon_active)
		{
			$queryBuilder
				->where('shop_purchase_discount_coupons.active', '=', $shop_coupon_active)
				->open()
				->where('count', '>', 0)
				->setOr()
				->where('count', '=', -1)
				->close();
		}

		// купоня связан со скидкой, а она с магазином
		if (isset($param['shop_shops_id']))
		{
			$queryBuilder->where('shop_id', '=', intval($param['shop_shops_id']));
		}

		$aResult = $queryBuilder->execute()->asAssoc()->current();

		return $aResult;
	}

	/**
	 * Список всех скидок
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $param ассоциативный массив параметров
	 * - $param['shop_discount_is_active'] int активность скидки
	 * - $param['shop_discount_is_hold'] bool выбирать только действующие скидки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $resource = $resource = $shop->GetAllDiscounts($shop_shops_id = false);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function GetAllDiscounts($shop_shops_id = FALSE, $param = array())
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_discount_id'),
				array('shop_id', 'shop_shops_id'),
				array('name', 'shop_discount_name'),
				array('start_datetime', 'shop_discount_from'),
				array('end_datetime', 'shop_discount_to'),
				array('active', 'shop_discount_is_active'),
				array('percent', 'shop_discount_percent'),
				array('user_id', 'users_id')
			)
			->from('shop_discounts')
			->where('deleted', '=', 0)
			->orderBy('shop_discount_name');

		$shop_shops_id !== FALSE && $queryBuilder->where('shop_id', '=', intval($shop_shops_id));

		isset($param['shop_discount_is_active']) && $queryBuilder->where('active', '=', intval($param['shop_discount_is_active']));

		if(isset($param['shop_discount_is_hold']))
		{
			$current_date = date('Y-m-d H:i:s');

			$queryBuilder
				->open()
				->where('end_datetime', '>=', $current_date)
				->setOr()
				->where('end_datetime', '=', '0000-00-00 00:00:00')
				->close()
				->where('start_datetime', '<=', $current_date);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение списка всех магазинов
	 *
	 * @param int $site_id идентификатор сайта
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $site_id = 1;
	 *
	 * $resource = $shop->GetAllShops($site_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource результат выборки
	 */
	function GetAllShops($site_id)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_shops_id'),
				array('shop_dir_id', 'shop_dir_id'),
				array('shop_company_id', 'shop_company_id'),
				array('name', 'shop_shops_name'),
				array('description', 'shop_shops_description'),
				array('yandex_market_name', 'shop_shops_yandex_market_name'),
				array('image_small_max_width', 'shop_image_small_max_width'),
				array('image_large_max_width', 'shop_image_big_max_width'),
				array('image_small_max_height', 'shop_image_small_max_height'),
				array('image_large_max_height', 'shop_image_big_max_height'),
				'structure_id',
				'shop_country_id',
				'shop_currency_id',
				'shop_order_status_id',
				array('shop_measure_id', 'shop_mesures_id'),
				array('send_order_email_admin', 'shop_shops_send_order_mail_admin'),
				array('send_order_email_user', 'shop_shops_send_order_mail_user'),
				array('email', 'shop_shops_admin_mail'),
				array('items_sorting_field', 'shop_sort_order_field'),
				array('items_sorting_direction', 'shop_sort_order_type'),
				array('groups_sorting_field', 'shop_group_sort_order_field'),
				array('groups_sorting_direction', 'shop_group_sort_order_type'),
				array('user_id', 'users_id'),
				array('comment_active', 'shop_comment_active'),
				array('watermark_file', 'shop_watermark_file'),
				array('watermark_default_use_large_image', 'shop_watermark_default_use_big'),
				array('watermark_default_use_small_image', 'shop_watermark_default_use_small'),
				array('watermark_default_position_x', 'shop_watermark_default_position_x'),
				array('watermark_default_position_y', 'shop_watermark_default_position_y'),
				array('items_on_page', 'shop_items_on_page'),
				array('guid', 'shop_shops_guid'),
				array('url_type', 'shop_shops_url_type'),
				array('format_date', 'shop_format_date'),
				array('format_datetime', 'shop_format_datetime'),
				array('typograph_default_items', 'shop_typograph_item_by_default'),
				array('typograph_default_groups', 'shop_typograph_group_by_default'),
				array('apply_tags_automatically', 'shop_shops_apply_tags_automatic'),
				array('write_off_paid_items', 'shop_shops_writeoff_payed_items'),
				array('apply_keywords_automatically', 'shop_shops_apply_keywords_automatic'),
				array('change_filename', 'shop_shops_file_name_conversion'),
				array('attach_digital_items', 'shop_shops_attach_eitem'),
				array('yandex_market_sales_notes_default', 'shop_yandex_market_sales_notes_default'),
				array('siteuser_group_id', 'shop_shops_access'),
				array('group_image_small_max_width', 'shop_image_small_max_width_group'),
				array('group_image_large_max_width', 'shop_image_big_max_width_group'),
				array('group_image_large_max_width', 'shop_image_big_max_width_group'),
				array('group_image_small_max_height', 'shop_image_small_max_height_group'),
				array('group_image_large_max_height', 'shop_image_big_max_height_group'),
				array('preserve_aspect_ratio', 'shop_shops_default_save_proportions'),
				'site_id'
			)
			->from('shops')
			->where('deleted', '=', 0);

		if ($site_id !== FALSE)
		{
			$queryBuilder->where('site_id', '=', intval($site_id));
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Конвертирование цены в различных форматах к цене в формате xxxx.yy
	 *
	 * @param string $price цена
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $price = '100-123';
	 *
	 * $newprice = $shop->ConvertPrice($price);
	 *
	 * // Распечатаем результат
	 * echo $newprice;
	 * ?>
	 * </code>
	 * @return float отформатированная цена
	 */
	function ConvertPrice($price)
	{
		return Shop_Controller::instance()->convertPrice($price, $this->iConvertPriceFractionalPart);
	}

	/**
	 * Отмена заказа (устанавливает поле shop_order_cancel = 1)
	 *
	 * @param int $shop_order_id идентификатор заказа
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 38;
	 *
	 * $shop->CancelOrder($shop_order_id);
	 * ?>
	 * </code>
	 * @return bool результат выполнения функции
	 */
	function CancelOrder($shop_order_id)
	{
		$shop_order_id = intval($shop_order_id);
		$oShop_Order = Core_Entity::factory('Shop_Order')->find($shop_order_id);

		if (is_null($oShop_Order->id))
		{
			return FALSE;
		}

		$oShop_Order->canceled(1)->save();

		return TRUE;
	}

	/**
	 * Получение списка валют с указанным международным названием
	 *
	 * @param string $shop_currency_international_name международное название валюты, к примеру - RUR, USD и т.д.
	 * <code>
	 * <?php
	 *
	 * $shop = new shop();
	 *
	 * $shop_currency_international_name = 'RUR';
	 *
	 * $row = $shop->GetCurrencyByInternationalName($shop_currency_international_name);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с выбранными валютами или false
	 */
	function GetCurrencyByInternationalName($shop_currency_international_name)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_currency_id'),
				array('name', 'shop_currency_name'),
				array('code', 'shop_currency_international_name'),
				array('exchange_rate', 'shop_currency_value_in_basic_currency'),
				array('default', 'shop_currency_is_default'),
				array('sorting', 'shop_currency_order'),
				array('user_id', 'users_id')
			)
			->from('shop_currencies')
			->where('code', '=', $shop_currency_international_name)
			->where('deleted', '=', 0);

		$aResult = $queryBuilder->execute()->asAssoc()->result();
		return count($aResult) != 0 ? $aResult : FALSE;
	}

	/**
	 * Получение числа районов города
	 * @param int $shop_city_id идентификатор города
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_city_id = 1;
	 *
	 * $count = $shop->GetCountCityAreas($shop_city_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число районов в городе или false
	 */
	function GetCountCityAreas($shop_city_id = 0)
	{
		$shop_city_id = intval($shop_city_id);
		$oShop_Country_Location_City_Area = Core_Entity::factory('Shop_Country_Location_City_Area');

		if ($shop_city_id > 0)
		{
			$oShop_Country_Location_City_Area->queryBuilder()->where('shop_country_location_city_id', '=', $shop_city_id);
		}

		$aShop_Country_Location_City_Areas = $oShop_Country_Location_City_Area->findAll();
		return count($aShop_Country_Location_City_Areas);
	}

	/**
	 * Получение числа условий доставки для типа доставки
	 * @param int $shop_type_of_delivery_id идентификатор типа доставки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_type_of_delivery_id = 1;
	 *
	 * $count = $shop->GetCountCondOfDelivery($shop_type_of_delivery_id);
	 *
	 * // Распечатаем результат
	 * echo $count;
	 * ?>
	 * </code>
	 * @return mixed число условий доставок или false
	 */
	function GetCountCondOfDelivery($shop_type_of_delivery_id = 0)
	{
		$shop_type_of_delivery_id = intval($shop_type_of_delivery_id);

		$oShop_Delivery_Condition = Core_Entity::factory('Shop_Delivery_Condition')->find($shop_type_of_delivery_id);

		$shop_type_of_delivery_id > 0 && $oShop_Delivery_Condition->queryBuilder()->where('shop_delivery_id', '=', $shop_type_of_delivery_id);

		$aShop_Delivery_Conditions = $oShop_Delivery_Condition->findAll();

		return count($aShop_Delivery_Conditions);
	}

	/**
	 * Получение списка производителей по названию
	 *
	 * @param string $shop_producers_list_name название производителя
	 * @param int $shop_id идентификатор магазина, необязательное поле
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_producers_list_name = 'HostCMS';
	 *
	 * $row = $shop->GetProducerByName($shop_producers_list_name);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с выбранными производителями или false
	 */
	function GetProducerByName($shop_producers_list_name, $shop_id = FALSE)
	{
		$oShop_Producer = Core_Entity::factory('Shop_Producer');
		$oShop_Producer->queryBuilder()->where('name', '=', $shop_producers_list_name);

		if ($shop_id)
		{
			$oShop_Producer->queryBuilder()->where('shop_id', '=', $shop_id);
		}

		$aShop_Producers = $oShop_Producer->findAll();

		$aResult = array();
		foreach($aShop_Producers as $oShop_Producer)
		{
			$aResult[] = $this->getArrayShopProducer($oShop_Producer);
		}

		return $aResult;
	}

	/**
	 * Получение списка продавцов по названию
	 *
	 * @param string $shop_saller_name название продавца
	 * @param int $shop_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_saller_name = 'HostCMS';
	 *
	 * $row = $shop->GetSellerByName($shop_saller_name);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с выбранными продавцами или false
	 */
	function GetSellerByName($shop_saller_name, $shop_id = FALSE)
	{
		$oShop_Seller = Core_Entity::factory('Shop_Seller');
		$oShop_Seller->queryBuilder()->where('name', '=', $shop_saller_name);

		if ($shop_id)
		{
			$oShop_Seller->queryBuilder()->where('shop_id', '=', $shop_id);
		}

		$aShop_Sellers = $oShop_Seller->findAll();

		$aResult = array();
		foreach($aShop_Sellers as $oShop_Seller)
		{
			$aResult[] = $this->getArrayShopSeller($oShop_Seller);
		}

		return $aResult;
	}

	/**
	 * Получение пути от текущего каталога к корневому
	 *
	 * @param integer $dir_id идентификатор текущего каталога
	 * @param boolean $first_call первый ли это вызов функции
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $dir_id = 600;
	 *
	 * $row = $shop->GetShopPathArray($dir_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив данных
	 */
	function GetShopPathArray($dir_id, $first_call = TRUE)
	{
		$dir_id = intval($dir_id);
		$first_call = Core_Type_Conversion::toBool($first_call);

		// Обнуляем массив.
		$first_call && $this->path_array = array();

		$group_row = $this->GetGroup($dir_id, array('cache_off' => TRUE));
		if ($group_row)
		{
			$this->path_array = $this->GetShopPathArray($group_row['shop_groups_parent_id'], FALSE);
			$this->path_array[$dir_id] = $group_row['shop_groups_name'];
		}
		else
		{
			$this->path_array[0] = '';
			unset($this->path_array[0]);
		}

		return $this->path_array;
	}

	/**
	 * Вставка/обновление группы товаров
	 *
	 * @param array $param ассоциативный массив параметров
	 * - int $param['group_id'] идентификационный номер группы
	 * - string $param['shop_groups_name'] имя группы
	 * - int $param['shop_shops_id'] идентификатор магазина
	 * - string $param['shop_groups_description'] описание группы
	 * - string $param['shop_groups_image'] путь к изображению (логотипу) группы
	 * - string $param['groups_image_small'] путь к уменьшенному изображению (логотипу) группы
	 * - int $param['shop_groups_order'] порядок сортировки
	 * - int $param['shop_groups_parent_id'] идентификатор родительской группы
	 * - int $param['users_id'] идентификатор пользователя
	 * - int $param['shop_groups_indexation'] флаг индексации
	 * - int $param['shop_groups_activity'] параметр, определяющий доступность группы и ее дочерних групп и элементов (1 (по умолчанию) - доступна, 0 - не доступна)
	 * - int $param['shop_groups_access'] параметр, определяющий тип доступа для группы товаров (0 - доступна всем, -1 - доступ как у родителя)
	 * - string $param['shop_groups_path'] путь к группе
	 * - string $param['shop_groups_seo_title'] заголовок страницы
	 * - string $param['shop_groups_seo_description'] задание значения мета-тега description страницы
	 * - string $param['shop_groups_seo_keywords'] задание значения мета-тега keywords страницы
	 * - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	 * - int $param ['shop_groups_big_image_width'] ширина большой картинки
	 * - int $param ['shop_groups_big_image_height'] высота большой картинки
	 * - int $param ['shop_groups_small_image_width'] ширина маленькой картинки
	 * - int $param ['shop_groups_small_image_height'] высота маленькой картинки
	 * - bool $param['search_event_indexation'] использовать ли событийную индексацию при вставке элемента, по умолчанию true
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_groups_name'] = 'Новая группа';
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_groups_parent_id'] = 586;
	 *
	 * $newid = $shop->InsertGroup($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed возвращает идентификатор вставленной группы (false при неудачной вставке)
	 */
	function InsertGroup($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if(!isset($param['group_id']) || !$param['group_id'])
		{
			$param['group_id'] = NULL;
		}

		$oShop_Group = Core_Entity::factory('Shop_Group', $param['group_id']);

		if(!is_null($oShop_Group->id))
		{
			if (isset($this->MasGroup[$oShop_Group->id]))
			{
				unset($this->MasGroup[$oShop_Group->id]);
			}
		}

		if (is_null($oShop_Group->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Group->user_id = $param['users_id'];
		}

		// Заполнение параметров
		if (isset($param['shop_shops_id']))
		{
			$oShop_Group->shop_id = $shop_shops_id = intval($param['shop_shops_id']);
		}
		elseif(is_null($oShop_Group->id))
		{
			// При вставке группы обязательно должен быть передан ID магазина
			return FALSE;
		}

		if (isset($param['shop_groups_parent_id']))
		{
			$oShop_Group->parent_id = $shop_groups_parent_id = intval($param['shop_groups_parent_id']);
		}
		else
		{
			$shop_groups_parent_id = 0;
		}

		!isset($param['search_event_indexation']) && $param['search_event_indexation'] = TRUE;

		if (isset($param['shop_groups_name']))
		{
			$oShop_Group->name = $param['shop_groups_name'];
		}
		else
		{
			$shop_groups_name = '';
		}

		isset($param['shop_groups_description']) && $oShop_Group->description = $param['shop_groups_description'];

		isset($param['shop_groups_cml_id']) && $oShop_Group->guid = $param['shop_groups_cml_id'];

		isset($param['shop_groups_image']) && $param['shop_groups_image'] != '' && $oShop_Group->image_large = $param['shop_groups_image'];

		isset($param['groups_image_small']) && $param['groups_image_small'] != '' && $oShop_Group->image_small = $param['groups_image_small'];

		if (isset($param['shop_groups_order']))
		{
			$oShop_Group->sorting = intval($param['shop_groups_order']);
		}
		elseif (is_null($oShop_Group->id))
		{
			$oShop_Group->sorting = 0;
		}

		if (isset($param['shop_groups_indexation']))
		{
			$oShop_Group->indexing = intval($param['shop_groups_indexation']);
		}
		elseif (is_null($oShop_Group->id))
		{
			$oShop_Group->indexing = 1;
		}

		isset($param['shop_groups_activity']) && $oShop_Group->active = intval($param['shop_groups_activity']);
		isset($param['shop_groups_access']) && $oShop_Group->siteuser_group_id = intval($param['shop_groups_access']);
		isset($param['shop_groups_seo_title']) && $oShop_Group->seo_title = $param['shop_groups_seo_title'];
		isset($param['shop_groups_seo_description']) && $oShop_Group->seo_description = $param['shop_groups_seo_description'];
		isset($param['shop_groups_seo_keywords']) && $oShop_Group->seo_keywords = $param['shop_groups_seo_keywords'];

		// Получаем данные о магазине
		$shop_data_row = $this->GetShop($param['shop_shops_id']);
		$shop_groups_big_image_width = Core_Type_Conversion::toInt($param['shop_groups_big_image_width']);

		if(!$shop_groups_big_image_width)
		{
			// Данные не переданы, получаем настройки из магазина
			if($shop_data_row && isset($param['shop_groups_image']))
			{
				$shop_groups_big_image_width = Core_Type_Conversion::toInt($shop_data_row['shop_image_big_max_width_group']);
				$oShop_Group->image_large_width = $shop_groups_big_image_width;
			}
		}

		$shop_groups_big_image_height = Core_Type_Conversion::toInt($param['shop_groups_big_image_height']);
		if(!$shop_groups_big_image_height)
		{
			// Данные не переданы, получаем настройки из магазина
			if($shop_data_row && isset($param['shop_groups_image']))
			{
				$shop_groups_big_image_height = Core_Type_Conversion::toInt($shop_data_row['shop_image_big_max_height_group']);
				$oShop_Group->image_large_height = $shop_groups_big_image_height;
			}
		}

		$shop_groups_small_image_width = Core_Type_Conversion::toInt($param['shop_groups_small_image_width']);

		if(!$shop_groups_small_image_width)
		{
			// Данные не переданы, получаем настройки из магазина
			if($shop_data_row && isset($param['groups_image_small']))
			{
				$shop_groups_small_image_width = Core_Type_Conversion::toInt($shop_data_row['shop_image_small_max_width_group']);
				$oShop_Group->image_small_width = $shop_groups_small_image_width;
			}
		}

		$shop_groups_small_image_height = Core_Type_Conversion::toInt($param['shop_groups_small_image_height']);

		if(!$shop_groups_small_image_height)
		{
			// Данные не переданы, получаем настройки из магазина
			if($shop_data_row && isset($param['groups_image_small']))
			{
				$shop_groups_small_image_height = Core_Type_Conversion::toInt($shop_data_row['shop_image_small_max_height_group']);
				$oShop_Group->image_small_height = $shop_groups_small_image_height;
			}
		}

		// Проверяем наличие группы в данной подгруппе с таким же именем
		// Автоматически обрабатываем только, если явно передан путь (может быть пустым)
		// или идет создание группы
		if (isset($param['shop_groups_path']) || is_null($oShop_Group->id))
		{
			$shop_groups_path = Core_Type_Conversion::toStr($param['shop_groups_path']);
			$shop_groups_path = $this->ClearPath($shop_groups_path);

			$shop_row = $this->GetShop($shop_shops_id);
			if ($shop_row)
			{
				if (trim($shop_groups_path) == '' && $shop_row['shop_shops_url_type'] == 1)
				{
					$shop_groups_path = Core_Str::transliteration($shop_groups_name);
				}
			}

			$queryBuilder = Core_QueryBuilder::select('id')
				->from('shop_groups')
				->where('path', '=', $shop_groups_path)
				->where('shop_id', '=', $shop_shops_id)
				->where('parent_id', '=', $shop_groups_parent_id)
				->where('deleted', '=', 0)
				->limit(1);

			// Проверяем наличие товара с таким же путем.
			if (!is_null($oShop_Group->id))
			{
				$queryBuilder->where('shop_group_id', '!=',  $oShop_Group->id);
			}

			$count = $queryBuilder->execute()->getNumRows();

			// Уже существует товар в данной группе - с таким же путем.
			// Путь заменяем на пустоту - далее он будет изменен на путь по умолчанию.
			$count > 0 && $shop_groups_path = '';

			// Добавляем к запросу.
			$oShop_Group->path = $shop_groups_path;
		}
		// Обновление
		if (!is_null($oShop_Group->id))
		{
			// Удаляем индексирование группы
			if (class_exists('Search'))
			{
				$this->IndexationShopGroups(0, 1, $oShop_Group->id);
			}

			// Очистка файлового кэша
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOP_GROUP';
				$cache->DeleteCacheItem($cache_name, $oShop_Group->id);
			}
		}

		$oShop_Group->save();

		// Если был передан пустой путь - устанавливаем путь - group_ + идентификатор.
		if (isset($shop_groups_path) && $shop_groups_path == '')
		{
			$prefix = defined('SHOP_GROUP_PATH_PREFIX')
				? SHOP_GROUP_PATH_PREFIX
				: 'group_';
			$oShop_Group->path = $prefix . $group_id;
			$oShop_Group->save();
		}

		if (isset($this->MasGroup[$oShop_Group->id]))
		{
			unset($this->MasGroup[$oShop_Group->id]);
		}

		// Добавляем индексирование группы
		if ($param['search_event_indexation']
		&& isset($param['shop_groups_indexation'])
		&& intval($param['shop_groups_indexation']) == 1
		&& isset($param['shop_groups_activity'])
		&& intval($param['shop_groups_activity']) == 1
		&& class_exists('Search'))
		{
			$this->IndexationShopGroups(0, 1, $oShop_Group->id);
		}

		return $oShop_Group->id;
	}

	/**
	 * Получение информации об электронном товаре
	 *
	 * @param int $shop_eitem_id идентификационный номер товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_eitem_id = 7;
	 *
	 * $row = $shop->GetEItem($shop_eitem_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array возвраает результат выборки товара
	 */
	function GetEItem($shop_eitem_id)
	{
		$shop_eitem_id = intval($shop_eitem_id);
		$oShop_Item_Digital = Core_Entity::factory('Shop_Item_Digital')->find($shop_eitem_id);

		return !is_null($oShop_Item_Digital->id)
			? $this->getArrayShopItemDigital($oShop_Item_Digital)
			: FALSE;
	}

	/**
	 * Вставка/обновление информации об электронном товаре
	 *
	 * @param array $param
	 * <br />int $param['shop_eitem_id'] Идентификатор электронного товара, при добавлении не передается или равен 0.
	 * <br />int $param['shop_items_catalog_item_id'] Идентификатор обычного товара, для которого добавляется эл. товар
	 * <br />int $param['shop_eitem_name'] Описание электронного товара
	 * <br />int $param['shop_eitem_value'] Текст электронного товара
	 * <br />int $param['shop_eitem_filename'] Оригинальное имя файла, если нет файла - передается пустая строка.
	 * Обратите внимание, что сам файл эл.товара необходимо размещать в директории отдельно после вставки записи в БД:
	 * <code>
	 * // ID магазина
	 * $shop_shops_id = 1;
	 *
	 * // ID товара
	 * $item_id = 123;
	 *
	 * $param['shop_items_catalog_item_id'] = $item_id;
	 * $param['shop_eitem_name'] = Core_Type_Conversion::toStr($_REQUEST['shop_eitem_name']);
	 * $param['shop_eitem_value'] = Core_Type_Conversion::toStr($_REQUEST['shop_eitem_value']);
	 * $param['shop_eitem_count'] = Core_Type_Conversion::toInt($_REQUEST['shop_eitem_count']);
	 *
	 * // Оригинальное имя файла
	 * $param['shop_eitem_filename'] = Core_Type_Conversion::toStr($_FILES['shop_eitem_filename']['name']);
	 *
	 * $eitem_id = $shop->InsertEItem($param);
	 *
	 * // Прояверяем, существует ли каталог для файла
	 * $dir_name = CMS_FOLDER . UPLOADDIR . 'shop_' . $shop_shops_id . '/eitems/item_catalog_' . $item_id;
	 *
	 * if (!is_dir($dir_name))
	 * {
	 * 		if (!@mkdir($dir_name))
	 * 		{
	 * 			// Выводим ошибку
	 * 		}
	 * }
	 *
	 * if (is_dir($dir_name))
	 * {
	 * 		// Получаем расширение файла
	 * 		$exp = Core_File::getExtension($_FILES['shop_eitem_filename']['name']);
	 *
	 * 		if (!move_uploaded_file($_FILES['shop_eitem_filename']['tmp_name'], $dir_name . '/' . $eitem_id . '.' . $exp))
	 * 		{
	 * 			// Отображаем ошибку
	 * 		}
	 * }
	 *
	 * </code>
	 * <br />int $param['shop_eitem_count'] Количество копий электронного товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['shop_eitem_value'] = 'Текст';
	 * $param['shop_eitem_name'] = 'Электронный товар';
	 * $param['shop_eitem_count'] = 5;
	 *
	 * $newid = $shop->InsertEItem($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленного элемента или false
	 */
	function InsertEItem($param)
	{
		if (!isset($param['shop_eitem_id']) || !$param['shop_eitem_id'])
		{
			$param['shop_eitem_id'] = NULL;
		}

		$oShop_Item_Digital =  Core_Entity::factory('Shop_Item_Digital', $param['shop_eitem_id'])->findAll();

		isset($param['shop_items_catalog_item_id']) && $oShop_Item_Digital->shop_item_id = intval($param['shop_items_catalog_item_id']);
		isset($param['shop_eitem_name']) && $oShop_Item_Digital->name = $param['shop_eitem_name'];
		isset($param['shop_eitem_value']) && $oShop_Item_Digital->value = $param['shop_eitem_value'];
		isset($param['shop_eitem_filename']) && $oShop_Item_Digital->filename = $param['shop_eitem_filename'];
		isset($param['shop_eitem_count']) && $oShop_Item_Digital->count = intval($param['shop_eitem_count']);

		if (is_null($oShop_Item_Digital->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Item_Digital->user_id = $param['users_id'];
		}

		$oShop_Item_Digital->save();

		return $oShop_Item_Digital->id;
	}

	/**
	 * Удаление информации об электронном товаре
	 *
	 * @param integer $shop_eitem_id Идентификатор электронного товара
	 * @param integer $shop_shops_id Идентификатор магазина
	 * @param integer $item_id Идентификатор обычного товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_eitem_id = 4;
	 * $shop_shops_id = 1;
	 * $item_id = 158;
	 *
	 * $result = $shop->DeleteEItem($shop_eitem_id, $shop_shops_id, $item_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 */

	function DeleteEItem($shop_eitem_id, $shop_shops_id, $item_id)
	{
		Core_Entity::factory('Shop_Item_Digital', $shop_eitem_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Удаление всех электронных товаров заданного товара
	 *
	 * @param integer $shop_items_catalog_item_id Идентификатор товара, для которого необходимо удалить все электронные товары
	 * @param integer $shop_shops_id Идентификатор магазина
	 *
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 171;
	 * $shop_shops_id = 1;
	 *
	 * $result = $shop->DeleteAllEItems($shop_items_catalog_item_id, $shop_shops_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function DeleteAllEItems($shop_items_catalog_item_id, $shop_shops_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		$aShop_Item_Digitals = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Item_Digitals->findAll();

		foreach($aShop_Item_Digitals as $oShop_Item_Digital)
		{
			$oShop_Item_Digital->markDeleted();
		}

		return TRUE;
	}

	/**
	 * Внутренний метод возвращает данные о группе товаров по идентификатору группы товаров стандарта CommerceML
	 *
	 * @param string $cml_id Идентификатор группы товаров при импорте из CommerceML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_groups_cml_id = 'ID00000034';
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetGroupIdByCmlId($shop_groups_cml_id, $shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed результат выборки базы или false
	 */
	function GetGroupIdByCmlId($shop_groups_cml_id, $shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		// Получаем из БД все каталоги товаров с shop_groups_cml_id равным $cml_id
		if(strlen(trim($shop_groups_cml_id)) == 0)
		{
			return FALSE;
		}

		$oShop_Group = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Groups;

		$oShop_Group
			->queryBuilder()
			->where('guid', '=', $shop_groups_cml_id);

		$aShop_Groups = $oShop_Group->findAll();

		if (isset($aShop_Groups[0]))
		{
			return $this->getArrayShopGroup($aShop_Groups[0]);
		}

		return FALSE;
	}

	/**
	 * Получение данных о товаре по идентификатору товара стандарта CommerceML
	 *
	 * @param string $shop_items_cml_id Идентификатор товара при импорте из CommerceML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_cml_id = 'ID12345678';
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetCatalogItemIdByCmlId($shop_items_cml_id, $shop_shops_id);
	 *
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed результат выборки базы или false
	 */
	function GetCatalogItemIdByCmlId($shop_items_cml_id, $shop_shops_id)
	{
		if (isset($this->CacheGetCatalogItemIdByCmlId[$shop_items_cml_id]))
		{
			return $this->CacheGetCatalogItemIdByCmlId[$shop_items_cml_id];
		}

		$shop_shops_id = intval($shop_shops_id);

		$oShop_Item = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Items;
		$oShop_Item
			->queryBuilder()
			->where('guid', '=', $shop_items_cml_id);

		$aShop_Items = $oShop_Item->findAll();

		if (isset($aShop_Items[0]))
		{
			$row = $this->getArrayShopItem($aShop_Items[0]);
			$this->CacheGetCatalogItemIdByCmlId[$shop_items_cml_id] = $row;

			return $row;
		}

		return FALSE;
	}

	/**
	 * Внутренний метод возвращает данные о налоге по идентификатору налога формата CommerceML
	 *
	 * @param string $cml_id Идентификатор налога при импорте из CommerceML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_tax_cml_id = 'ID00000034';
	 *
	 * $row = $shop->GetTaxIdByCmlId($shop_tax_cml_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed результат выборки базы или false
	 */
	function GetTaxIdByCmlId($shop_tax_cml_id, $shop_tax_rate = FALSE)
	{
		$oShop_Tax = Core_Entity::factory('Shop_Tax');

		$shop_tax_rate !== FALSE && $oShop_Tax->queryBuilder()->where('rate', '=', intval($shop_tax_rate));

		$oShop_Tax->queryBuilder()->where('guid', '=', $shop_tax_cml_id);

		$aShop_Taxes = $oShop_Tax->findAll();
		if (isset($aShop_Taxes[0]))
		{
			return $this->getArrayShopTax($aShop_Taxes[0]);
		}

		return FALSE;
	}

	/**
	 * Получение данных о цене по идентификатору цены формата CommerceML
	 *
	 * @param string $shop_list_of_prices_cml_id
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_list_of_prices_cml_id = 'ID00000555';
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetPriceByCmlId($shop_list_of_prices_cml_id, $shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed результат выборки базы или false
	 */
	function GetPriceByCmlId($shop_list_of_prices_cml_id, $shop_shops_id)
	{
		if (isset($this->CacheGetPriceByCmlId[$shop_list_of_prices_cml_id . '_' . $shop_shops_id]))
		{
			return $this->CacheGetPriceByCmlId[$shop_list_of_prices_cml_id . '_' . $shop_shops_id];
		}
		$shop_shops_id = intval($shop_shops_id);

		$oShop_Price = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Prices;
		$oShop_Price
			->queryBuilder
			->where('guid', '=', $shop_list_of_prices_cml_id);

		$aShop_Prices = $oShop_Price->findAll();

		if (isset($aShop_Prices[0]))
		{
			return $this->CacheGetPriceByCmlId[$shop_list_of_prices_cml_id . '_' . $shop_shops_id] = $this->getArrayShopPrice($aShop_Prices[0]);
		}

		return FALSE;
	}

	/**
	 * Получение данных о дополнительном свойстве товара по идентификатору дополнительного свойства формата CommerceML
	 *
	 * @param string $item_property_cml_id
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $item_property_cml_id = 'ID00000555';
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetItemPropertyIdByCmlId($item_property_cml_id, $shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed результат выборки базы или false
	 */
	function GetItemPropertyIdByCmlId($item_property_cml_id, $shop_shops_id)
	{
		if (isset($this->CacheGetItemPropertyIdByCmlId[$item_property_cml_id . '_' . $shop_shops_id]))
		{
			return $this->CacheGetItemPropertyIdByCmlId[$item_property_cml_id . '_' . $shop_shops_id];
		}

		$shop_shops_id = intval($shop_shops_id);

		$oShop_Item_Property = Core_Entity::factory('Shop_Item_Property_List', $shop_shops_id)->Properties->getByGuid($item_property_cml_id);

		if (!is_null($oShop_Item_Property))
		{
			return $this->CacheGetItemPropertyIdByCmlId[$item_property_cml_id . '_' . $shop_shops_id] = $this->getArrayItemProperty($oShop_Item_Property);
		}

		return FALSE;
	}

	/**
	 * Получение информации о мере измерения из базы согласно имени $string используя оператор LIKE
	 *
	 * @param string $string Строка с именем меры измерения
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $string = 'м';
	 *
	 * $row = $shop->GetMesuresByLike($string);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed строка с данными о мере измерения или false
	 */
	function GetMesuresByLike($string)
	{
		// Извлекаем из кэша в памяти, если есть
		if (isset($this->CacheGetMesuresByLike[$string]))
		{
			return $this->CacheGetMesuresByLike[$string];
		}

		$oShop_Measure = Core_Entity::factory('Shop_Measure');
		$oShop_Measure->queryBuilder()->where('name', 'LIKE', '%' . $string. '%');
		$aShop_Measures = $oShop_Measure->findAll();

		if (isset($aShop_Measures[0]))
		{
			return $this->CacheGetMesuresByLike[$string] = $this->getArrayShopMeasure($aShop_Measures[0]);
		}

		return FALSE;
	}

	/**
	 * Определение принадлежности дополнительного свойства группе товаров
	 *
	 * @param array $param массив атрибутов
	 * <br />int $param['shop_groups_id'] Идентификатор каталога товаров
	 * <br />int $param['shop_list_of_properties_id'] Идентификатор дополнительного свойства
	 * <br />int $param['shop_shops_id'] Идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_groups_id'] = 589;
	 * $param['shop_list_of_properties_id'] = 135;
	 * $param['shop_shops_id'] = 1;
	 *
	 * $row = $shop->IssetPropertyForGroup($param);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function IssetPropertyForGroup($param)
	{
		if (isset($param['shop_groups_id']))
		{
			$shop_groups_id = Core_Type_Conversion::toInt($param['shop_groups_id']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['shop_list_of_properties_id']))
		{
			$shop_list_of_properties_id = Core_Type_Conversion::toInt($param['shop_list_of_properties_id']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['shop_shops_id']))
		{
			$shop_shops_id = Core_Type_Conversion::toInt($param['shop_shops_id']);
		}
		else
		{
			return FALSE;
		}

		$oShop_Item_Property_For_Group = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Item_Property_For_Groups->getByShopItemPropertyIdAndGroupId($shop_list_of_properties_id, $shop_group_id);

		return !is_null($oShop_Item_Property_For_Group);
	}

	/**
	 * Установка принадлежности дополнительного свойства группе товаров
	 *
	 * @param array $param массив атрибутов
	 * <br />int $param['shop_groups_id'] Идентификатор каталога товаров
	 * <br />int $param['shop_list_of_properties_id'] Идентификатор дополнительного свойства
	 * <br />int $param['shop_shops_id'] Идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_groups_id'] = 589;
	 * $param['shop_list_of_properties_id'] = 135;
	 * $param['shop_shops_id'] = 1;
	 *
	 * $newid = $shop->InsertPropertyForGroup($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленной записи или false
	 */
	function InsertPropertyForGroup($param)
	{
		if (isset($param['shop_groups_id']))
		{
			$shop_groups_id = Core_Type_Conversion::toInt($param['shop_groups_id']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['shop_list_of_properties_id']))
		{
			$shop_list_of_properties_id = Core_Type_Conversion::toInt($param['shop_list_of_properties_id']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['shop_shops_id']))
		{
			$shop_shops_id = Core_Type_Conversion::toInt($param['shop_shops_id']);
		}
		else
		{
			return FALSE;
		}

		if(!$this->IssetPropertyForGroup(array('shop_groups_id' => $shop_groups_id, 'shop_list_of_properties_id' => $shop_list_of_properties_id, 'shop_shops_id' => $shop_shops_id)))
		{

			$oShop_Item_Property_For_Group = Core_Entity::factory('Shop_Item_Property_For_Group');
			$oShop_Item_Property_For_Group->shop_id = $shop_shops_id;
			$oShop_Item_Property_For_Group->shop_group_id = $shop_groups_id;
			$oShop_Item_Property_For_Group->shop_item_property_id = $shop_list_of_properties_id;

			$oShop_Item_Property_For_Group->save();

			return $oShop_Item_Property_For_Group->id;
		}

		return false;
	}

	/**
	 *
	 * Удаляет принадлежность дополнительного свойтсва группе товаров
	 *
	 * @param int $shop_properties_item_for_groups_id Идентификатор соответствия дополнительного свойства группе из таблицы shop_properties_item_for_groups_table
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_item_for_groups_id = 777;
	 *
	 * $result = $shop->DeletePropertyForGroup($shop_properties_item_for_groups_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function DeletePropertyForGroup($shop_properties_item_for_groups_id)
	{
		$Shop_Item_Property_For_Group = Core_Entity::factory('Shop_Item_Property_For_Group')->find($shop_properties_item_for_groups_id);

		if (!is_null($Shop_Item_Property_For_Group->id))
		{
			$Shop_Item_Property_For_Group->delete();
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Получение суммы пользователя сайта в базовой валюте
	 *
	 * @param int $site_users_id Идентификатор пользователя сайта
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $site_users_id = '';
	 * $shop_shops_id = 1;
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 * $sum = $shop->GetSiteUserAccountSum($site_users_id, $shop_shops_id);
	 *
	 * // Распечатаем результат
	 * echo $sum;
	 * ?>
	 * </code>
	 * @return int Сумма
	 */
	function GetSiteUserAccountSum($site_users_id, $shop_shops_id)
	{
		$site_users_id = intval($site_users_id);
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('SUM(amount_base_currency)', 'summ')
			)
			->from('shop_siteuser_transactions')
			->where('siteuser_id', '=', $site_users_id)
			->where('active', '=', 1)
			->where('shop_id', '=', $shop_shops_id);

		$aResult = $queryBuilder->execute()->asAssoc()->current();

		if (isset($aResult['summ']))
		{
			return floatval($aResult['summ']);
		}

		return FALSE;
	}

	/**
	 * Вставка транзакции по лицевому счету пользователя
	 *
	 * array $param ассоциативный массив параметров
	 * <br/>int $param['shop_site_users_account_id'] Идентификатор транзакции, необязательное поле, указывается при обновлении транзакции
	 * <br/>int $param['shop_shops_id'] Идентификатор магазина
	 * <br/>int $param['site_users_id'] Идентификатор пользователя сайта
	 * <br/>int $param['shop_site_users_account_active'] Активность транзакции (1 - Активна, 0 - Неактивна), необязательное поле, по умолчанию 1 - активна
	 * <br/>str $param['shop_site_users_account_datetime'] Время проведения транзакции, необязательное поле, по умолчанию указывается текущая дата
	 * <br/>int $param['shop_site_users_account_sum'] Сумма транзакции
	 * <br/>int $param['shop_currency_id'] Идентификатор валюты
	 * <br/>int $param['shop_site_users_account_sum_in_base_currency'] Сумма транзакции в базовой валюте
	 * <br/>int $param['shop_order_id'] Идентификатор заказа (Необязательно)
	 * <br/>str $param['shop_site_users_account_description'] Описание транзакции
	 * <br/>int $param['users_id'] Идентификатор пользователя центра администрирования
	 * <br/>int $param['shop_site_users_account_type'] Тип транзакции, 0 -  обычная, 1 - бонусное начисление
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_site_users_account_sum'] = 1000;
	 * $param['shop_currency_id'] = 1;
	 * $param['shop_site_users_account_sum_in_base_currency'] = 1000;
	 * $param['shop_site_users_account_description'] = 'Новая транзакция';
	 * $param['site_users_id'] = 1;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $newid = $shop->InsertSiteUserAccountTransaction($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed Идентификатор вставленной записи или false
	 */
	function InsertSiteUserAccountTransaction($param)
	{
		if (!isset($param['shop_site_users_account_id']) || !$param['shop_site_users_account_id'])
		{
			$param['shop_site_users_account_id'] = NULL;
		}

		$oShop_Siteuser_Transaction = Core_Entity::factory('Shop_Siteuser_Transaction', $param['shop_site_users_account_id']);

		if (isset($param['shop_shops_id']))
		{
			$oShop_Siteuser_Transaction->shop_id = intval($param['shop_shops_id']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['site_users_id']))
		{
			$oShop_Siteuser_Transaction->siteuser_id = intval($param['site_users_id']);
		}
		else
		{
			return FALSE;
		}

		$oShop_Siteuser_Transaction->active = isset($param['shop_site_users_account_active'])
			? intval($param['shop_site_users_account_active'])
			: 1;

		$oShop_Siteuser_Transaction->datetime = isset($param['shop_site_users_account_datetime'])
			? Core_Date::datetime2sql($param['shop_site_users_account_datetime'])
			: Core_Date::datetime2sql(time());

		if (isset($param['shop_site_users_account_sum']))
		{
			$oShop_Siteuser_Transaction->amount = floatval($param['shop_site_users_account_sum']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['shop_currency_id']))
		{
			$oShop_Siteuser_Transaction->shop_currency_id = intval($param['shop_currency_id']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['shop_site_users_account_sum_in_base_currency']))
		{
			$oShop_Siteuser_Transaction->amount_base_currency = floatval($param['shop_site_users_account_sum_in_base_currency']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['shop_order_id']))
		{
			$oShop_Siteuser_Transaction->shop_order_id = intval($param['shop_order_id']);
		}

		if (isset($param['shop_site_users_account_type']))
		{
			$oShop_Siteuser_Transaction->type = intval($param['shop_site_users_account_type']);
		}

		if (isset($param['shop_site_users_account_description']))
		{
			$oShop_Siteuser_Transaction->description = $param['shop_site_users_account_description'];
		}
		else
		{
			return FALSE;
		}

		if (is_null($oShop_Siteuser_Transaction->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Siteuser_Transaction->user_id = $param['users_id'];
		}

		$oShop_Siteuser_Transaction->save();

		return $oShop_Siteuser_Transaction->id;
	}

	/**
	 * Установка активности транзакции в 0 (делает неактивной)
	 *
	 * @param int $shop_site_users_account_id Идентификатор транзакции
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_site_users_account_id = 19;
	 *
	 * $result = $shop->UnsetSiteUserAccountTransaction($shop_site_users_account_id);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function UnsetSiteUserAccountTransaction($shop_site_users_account_id)
	{
		$shop_site_users_account_id = intval($shop_site_users_account_id);

		$oShop_Siteuser_Transaction = Core_Entity::factory('Shop_Siteuser_Transaction')->find($shop_site_users_account_id);

		if(!is_null($oShop_Siteuser_Transaction->id))
		{
			$oShop_Siteuser_Transaction->active(0)->save();
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Удаление транзакции
	 *
	 * @param int $shop_site_users_account_id Идентификатор транзакции
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_site_users_account_id = 9;
	 *
	 * $result = $shop->DeleteSiteUserAccountTransaction($shop_site_users_account_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function DeleteSiteUserAccountTransaction($shop_site_users_account_id)
	{
		$shop_site_users_account_id = intval($shop_site_users_account_id);
		Core_Entity::factory('Shop_Siteuser_Transaction', $shop_site_users_account_id)->delete();
		return TRUE;
	}

	/**
	 * Получение информации о транзакции
	 *
	 * @param int $shop_site_users_account_id Идентификатор транзакции
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_site_users_account_id = 20;
	 *
	 * $row = $shop->GetSiteUserAccountTransaction($shop_site_users_account_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed Массив с данными, либо false
	 */
	function GetSiteUserAccountTransaction($shop_site_users_account_id)
	{
		$shop_site_users_account_id = intval($shop_site_users_account_id);

		$oShop_Siteuser_Transaction = Core_Entity::factory('Shop_Siteuser_Transaction')->find($shop_site_users_account_id);

		return !is_null($oShop_Siteuser_Transaction->id)
			? $this->getArrayShopSiteuserTransaction($oShop_Siteuser_Transaction)
			: FALSE;
	}

	/**
	 * Получение данных обо всех транзакциях пользователя сайта
	 *
	 * @param int $site_users_id Идентификатор пользователя сайта
	 * @param int $shop_shops_id Идентификатор магазина, необязательный параметр, по умолчанию false
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * 	else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $resource = $shop->GetAllSiteUserAccountTransaction($site_users_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed Результат выборки базы или false
	 */
	function GetAllSiteUserAccountTransaction($site_users_id, $shop_shops_id = FALSE)
	{
		$site_users_id = intval($site_users_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_site_users_account_id'),
				array('shop_id', 'shop_shops_id'),
				array('siteuser_id', 'site_users_id'),
				array('active', 'shop_site_users_account_active'),
				array('datetime', 'shop_site_users_account_datetime'),
				array('amount', 'shop_site_users_account_sum'),
				array('shop_currency_id', 'shop_currency_id'),
				array('amount_base_currency', 'shop_site_users_account_sum_in_base_currency'),
				array('shop_order_id', 'shop_order_id'),
				array('description', 'shop_site_users_account_description'),
				array('user_id', 'users_id'),
				array('type', 'shop_site_users_account_type')
			)
			->from('shop_siteuser_transactions')
			->where('siteuser_id', '=', $site_users_id)
			->orderBy('shop_site_users_account_datetime', 'DESC');

		if ($shop_shops_id)
		{
			$queryBuilder->where('shop_id', '=', intval($shop_shops_id));
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Отображение списка лицевых счетов пользователя
	 *
	 * @param array $param массив атрибутов
	 * - $param['site_users_id'] идентификатор пользователя, необязательный параметр. Если не передан - определяется автоматически.
	 * - $param['site_id'] идентификатор сайта, необязательный параметр. Если не передан - определяется автоматически.
	 * - $param['xsl_name'] наименование XSL-шаблона
	 * <code>
	 * $shop = new shop();
	 *
	 * $param['site_users_id'] = 1;
	 * $param['site_id'] = 1;
	 * $param['xsl_name'] = 'СписокЛицевыхСчетов ';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$site_users_id = 0;
	 * }
	 *
	 * $shop->ShowSiteUsersAccount($param);
	 * ?>
	 * </code>
	 */
	function ShowSiteUsersAccount($param)
	{
		if (isset($param['site_users_id']))
		{
			$site_users_id = intval($param['site_users_id']);
		}
		elseif (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_users_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_users_id = 0;
		}

		$site_id = isset($param['site_id'])
			? intval($param['site_id'])
			: CURRENT_SITE;

		if (isset($param['xsl_name']))
		{
			$xsl_name = strval($param['xsl_name']);
		}
		else
		{
			return FALSE;
		}

		// Формируем строковую переменную с данными xml
		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<accounts>' . "\n";

		if (isset($param['external_xml']) && is_array($param['external_xml']))
		{
			// Формируем xml для переданных массивов
			$ExternalXml = new ExternalXml();
			$xmlData .= $ExternalXml->GenXml($param['external_xml']);
		}

		// Получаем список магазинов для текущего сайта
		$shops_array = $this->GetAllShops($site_id);

		if (mysql_num_rows($shops_array) > 0)
		{
			while ($shops_row = mysql_fetch_assoc($shops_array))
			{
				// Данные о магазине в xml
				$xmlData .= '<shop id="' . $shops_row['shop_shops_id'] . '">' . "\n";
				$xmlData .= $this->GenXml4Shop($shops_row['shop_shops_id'], $shops_row);

				// Расчитываем сумму на аккаунте пользователя
				$site_users_account = $this->GetSiteUserAccountSum($site_users_id, $shops_row['shop_shops_id']);

				$xmlData .= '<site_users_account>' . str_for_xml($site_users_account) . '</site_users_account>' . "\n";

				$xmlData .= '</shop>' . "\n";
			}
		}
		$xmlData .= '</accounts>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Отображение списка транзакций пользователя
	 *
	 * @param array $param массив атрибутов
	 * - $param['site_users_id'] идентификатор пользователя, необязательный параметр. Если не передан - определяется автоматически.
	 * - $param['shop_shops_id'] идентификатор магазина
	 * - $param['xsl_name'] наименование XSL-шаблона
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['xsl_name'] = 'ДвиженияПоЛицевомуСчету';
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 *	$site_users_id = 0;
	 * }
	 *
	 * $shop->ShowSiteUsersAccountTransaction($param);
	 * ?>
	 * </code>
	 */
	function ShowSiteUsersAccountTransaction($param)
	{
		if (isset($param['site_users_id']))
		{
			$site_users_id = intval($param['site_users_id']);
		}
		elseif (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_users_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_users_id = 0;
		}

		if (isset($param['shop_shops_id']))
		{
			$shop_shops_id = intval($param['shop_shops_id']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['xsl_name']))
		{
			$xsl_name = strval($param['xsl_name']);
		}
		else
		{
			return FALSE;
		}

		// Формируем строковую переменную с данными xml
		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<accounts_transaction>' . "\n";

		if (isset($param['external_xml']) && is_array($param['external_xml']))
		{
			// Формируем xml для переданных массивов
			$ExternalXml = new ExternalXml();
			$xmlData .= $ExternalXml->GenXml($param['external_xml']);
		}

		// Данные о магазине в xml
		$xmlData .= '<shop id="' . $shop_shops_id . '">' . "\n";
		$xmlData .= $this->GenXml4Shop($shop_shops_id);

		// Расчитываем сумму на аккаунте пользователя
		$site_users_account = $this->GetSiteUserAccountSum($site_users_id, $shop_shops_id);

		$xmlData .= '<site_users_account>' . str_for_xml($site_users_account) . '</site_users_account>' . "\n";

		$xmlData .= '</shop>' . "\n";

		// Получаем информацию о транзакциях
		$transactions_res = $this->GetAllSiteUserAccountTransaction($site_users_id, $shop_shops_id);

		// Список транзакций
		$xmlData .= '<site_users_account_transactions>' . "\n";

		while ($transactions_row = mysql_fetch_assoc($transactions_res))
		{
			// Транзакция
			$xmlData .= '<site_users_account_transaction>' . "\n";

			$xmlData .= '<site_users_account_id>' . $transactions_row['shop_site_users_account_id'] . '</site_users_account_id>' . "\n";
			$xmlData .= '<shops_id>' . $transactions_row['shop_shops_id'] . '</shops_id>' . "\n";
			$xmlData .= '<site_users_id>' . $transactions_row['site_users_id'] . '</site_users_id>' . "\n";
			$xmlData .= '<site_users_account_active>' . $transactions_row['shop_site_users_account_active'] . '</site_users_account_active>' . "\n";
			$xmlData .= '<site_users_account_datetime>' . Core_Date::sql2datetime($transactions_row['shop_site_users_account_datetime']) . '</site_users_account_datetime>' . "\n";
			$xmlData .= '<site_users_account_sum>' . $transactions_row['shop_site_users_account_sum'] . '</site_users_account_sum>' . "\n";
			$xmlData .= '<currency_id>' . $transactions_row['shop_currency_id'] . '</currency_id>' . "\n";

			$xmlData .= $this->GenXML4Currency($transactions_row['shop_currency_id']);

			$xmlData .= '<site_users_account_sum_in_base_currency>' . $transactions_row['shop_site_users_account_sum_in_base_currency'] . '</site_users_account_sum_in_base_currency>' . "\n";
			$xmlData .= '<order_id>' . $transactions_row['shop_order_id'] . '</order_id>' . "\n";
			$xmlData .= '<site_users_account_description>'.str_for_xml($transactions_row['shop_site_users_account_description']) . '</site_users_account_description>' . "\n";
			$xmlData .= '<shop_site_users_account_type>' . $transactions_row['shop_site_users_account_type'] . '</shop_site_users_account_type>' . "\n";
			$xmlData .= '</site_users_account_transaction>' . "\n";
		}

		$xmlData .= '</site_users_account_transactions>' . "\n";

		$xmlData .= '</accounts_transaction>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Вставка тегов для товаров
	 *
	 * @param array $array массив атрибутов
	 * <br />str $array['tags'] - теги для товара с разделителем запятая
	 * <br />str $array['shop_items_catalog_item_id'] - идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $array['tags'] = 'Tag';
	 * $array['shop_items_catalog_item_id'] = 1;
	 *
	 * $newtag = $shop->InsertItemsCatalogTags($array);
	 *
	 * echo $newtag;
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function InsertItemsCatalogTags($array)
	{
		if (class_exists('Tag'))
		{
			$array['tags'] = Core_Type_Conversion::toStr($array['tags']);
			$array['shop_items_catalog_item_id'] = Core_Type_Conversion::toInt($array['shop_items_catalog_item_id']);

			if ($array['shop_items_catalog_item_id'] <= 0)
			{
				return FALSE;
			}

			$tags_array = explode(',', $array['tags']);
			$oTag = & singleton('Tag');
			$insert_tag = array();
			foreach ($tags_array as $tag)
			{
				$tag = trim($tag);

				$tag_id = $oTag->InsertTag(array('tag_name' => $tag));

				// Сохраняем в спсике вставленных тегов для последующей проверки
				$insert_tag[] = $tag_id;

				$oTag->InsertTagRelation(array(
					'tag_id' => $tag_id,
					'shop_items_catalog_item_id' => $array['shop_items_catalog_item_id']
				));
			}

			// Удаляем другие соответствия, если они были вставлены ранее, для этого
			// получаем список всех соответствий для элемента
			$tags_temp = $oTag->GetTagRelation(array(
			'shop_items_catalog_item_id' => $array['shop_items_catalog_item_id']
			));

			if ($tags_temp)
			{
				foreach ($tags_temp as $mytag)
				{
					// Если тега не было в списке на добавление - удалим его связь с ИЭ
					if (!in_array($mytag['tag_id'], $insert_tag))
					{
						// Удаляем связь
						$oTag->DeleteTagRelation(array(
						'tag_id' => $mytag['tag_id'],
						'shop_items_catalog_item_id' => $array['shop_items_catalog_item_id']
						));
					}
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Получение информации о дополнительном свойстве группы товаров
	 *
	 * @param int $shop_properties_group_id Идентификатор дополнительного свойства группы товаров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_group_id = 11;
	 *
	 * $row = $shop->GetPropretyOfGroup($shop_properties_group_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed Массив с данными или false
	 */
	function GetPropretyOfGroup($shop_properties_group_id)
	{
		$shop_properties_group_id = intval($shop_properties_group_id);
		$oProperty = Core_Entity::factory('Property')->find($shop_properties_group_id);

		return !is_null($oProperty->id)
			? $this->getArrayGroupProperty($oProperty)
			: FALSE;
	}

	/**
	 * Удаление дополнительного свойства группы товаров
	 *
	 * @param int $shop_properties_group_id Идентификатор дополнительного свойства
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_group_id = 2;
	 *
	 * $result = $shop->DeletePropretyOfGroup($shop_properties_group_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return bool Результат выполнения запроса
	 */
	function DeletePropretyOfGroup($shop_properties_group_id)
	{
		$shop_properties_group_id = intval($shop_properties_group_id);
		Core_Entity::factory('Property')->markDeleted();
		return FALSE;
	}

	/**
	 * Получение информации о ВСЕХ дополнительных свойствах групп магазина
	 *
	 * @param int $shop_shops_id Идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $resource = $shop->GetAllPropretyOfGroup($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource Результат выборки из базы
	 */
	function GetAllPropretyOfGroup($shop_shops_id, $shop_properties_groups_dir_id = FALSE)
	{
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select()
			->from('properties')
			->join('shop_group_properties', 'properties.id', '=', 'shop_group_properties.property_id')
			->where('deleted', '=', 0)
			->orderBy('sorting');

		if ($shop_properties_groups_dir_id)
		{
			$queryBuilder->where('property_dir_id', '=', $shop_properties_groups_dir_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Вставка дополнительного свойства группы товаров
	 *
	 * @param array $param Массив параметров
	 * - $param['shop_properties_group_id'] Идентификатор дополнительного свойства, используется при обновлении (необязательное поле при вставке)
	 * - $param['shop_shops_id'] Идентификатор магазина
	 * - $param['shop_properties_groups_dir_id'] Идентификатор раздела, в котором размещается доп. св-во группы
	 * - $param['shop_mesures_id'] Идентификатор единицы измерения
	 * - $param['lists_id'] Идентификатор списка
	 * - $param['shop_properties_group_name'] Название дополнительного свойства
	 * - $param['shop_properties_group_xml_name'] Название XML-тега
	 * - $param['shop_properties_group_type'] Тип дополнительного свойства
	 * <br />0 - Строка
	 * <br />1 - Файл
	 * <br />2 - Список
	 * <br />3 - Большое текстовое поле
	 * <br />4 - Визуальный редактор
	 * <br />5 - Дата
	 * <br />6 - ДатаВремя
	 * <br />7 - Флажок
	 * - $param['shop_properties_group_default_value'] Значение дополнительного свойства по умолчанию
	 * - $param['shop_properties_group_order'] Порядок сортировки дополнительного свойства
	 * - $param['users_id'] Идентрификатор пользователя центра администрирования
	 * - $param['shop_properties_group_cml'] уникальный идентификатор
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_properties_group_name'] = 'Новое свойство';
	 * $param['shop_properties_groups_dir_id'] = 0; // Раздел корневой
	 * $param['shop_properties_group_type'] = 4; // Тип
	 *
	 * $newid = $shop->InsertPropretyOfGroup($param);
	 *
	 * // Распечатаем результат
	 * echo ($newid);
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленного/обновленного свойства группы
	 */
	function InsertPropretyOfGroup($param)
	{
		if (!isset($param['shop_properties_group_id']) || !$param['shop_properties_group_id'])
		{
			$param['shop_properties_group_id'] = NULL;
		}

		$shop_id = intval($param['shop_shops_id']);
		$oShop_Group_Property_List = Core_Entity::factory('Shop_Group_Property_List', $shop_id);

		$oProperty = Core_Entity::factory('Property', $param['shop_properties_group_id']);

		$oProperty->property_dir_id = isset($param['shop_properties_groups_dir_id'])
			? intval($param['shop_properties_groups_dir_id'])
			: 0;

		if (isset($param['shop_properties_group_name']))
		{
			$oProperty->name = $param['shop_properties_group_name'];
		}
		elseif(is_null($oProperty->id))
		{
			$oProperty->name = '';
		}

		isset($param['shop_properties_group_type']) && $oProperty->type = intval($param['shop_properties_group_type']);
		isset($param['shop_properties_group_order']) && $oProperty->sorting = intval($param['shop_properties_group_order']);
		isset($param['shop_properties_group_default_small_height']) && $oProperty->image_small_max_height = intval($param['shop_properties_group_default_small_height']);
		isset($param['shop_properties_group_default_small_width']) && $oProperty->image_small_max_width = intval($param['shop_properties_group_default_small_width']);
		isset($param['shop_properties_group_default_big_height']) && $oProperty->image_large_max_height = intval($param['shop_properties_group_default_big_height']);
		isset($param['shop_properties_group_default_big_width']) && $oProperty->image_large_max_width = intval($param['shop_properties_group_default_big_width']);
		isset($param['shop_properties_group_default_value']) && $oProperty->default_value = $param['shop_properties_group_default_value'];
		isset($param['shop_properties_group_xml_name']) && $oProperty->tag_name = preg_replace('/[^a-zA-Z0-9а-яА-ЯЁ.\-_]/u', '', $param['shop_properties_group_xml_name']);
		isset($param['lists_id']) && $oProperty->list_id = intval($param['lists_id']);

		$oProperty->guid = isset($param['shop_properties_group_cml'])
			? $param['shop_properties_group_cml']
			: Core_Guid::get();

		if (is_null($oProperty->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oProperty->user_id = $param['users_id'];
		}

		$oShop_Group_Property_List->add($oProperty);

		return $oProperty->id;
	}

	/**
	 * Показ облака тегов для магазина
	 *
	 * @param int $shop_id Идентификатор магазина
	 * @param str $xsl_name имя XSL-шаблона
	 * @param array $property массив дополнительных атрибутов
	 * - $property['begin'] начальная позиция отображения тегов (по умолчанию 0)
	 * - $property['count'] количество отображаемых тегов
	 * - $property['TagsOrder'] параметр, определяющий порядок сортировки тегов. Принимаемые значения: ASC - по возрастанию (по умолчанию), DESC - по убыванию
	 * - $property['TagsOrderField'] поле сортировки тегов, если случайная сортировка, то записать RAND(). по умолчанию теги сортируются по названию.
	 * - $property['tags_group_id'] идентификатор или массив идентификаторов групп тегов, из которых необходимо вести отбор тегов
	 * - $property['shop_groups_id'] идентификатор группы магазина, для которой необходимо вести отбор тегов
	 * - $property['NotIn'] строка идентификаторов товаров, исключаемых из выборки тегов
	 * - $property['In'] массив идентификаторов товаров, только для которых выбирать теги
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_id = 1;
	 * $xsl_name = 'ОблакотеговМагазин';
	 *
	 * $row = $shop->ShowTagsCloud($shop_id, $xsl_name);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed
	 */
	function ShowTagsCloud($shop_id, $xsl_name, $property = array(), $external_propertys = array())
	{
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		// по умолчанию кэширование - включено
		!isset($property['cache']) && $property['cache'] = TRUE;

		// По умолчанию показываем только активные элементы
		!isset($property['show_catalog_item_type']) && $property['show_catalog_item_type'] = array('active');

		// Проверяем, установлен ли модуль кэширования
		if (class_exists('Cache') && $property['cache'])
		{
			$cache = & singleton('Cache');
			$kernel = & singleton('kernel');

			$cache_element_name = 'ShowTagsCloud_' . $shop_id . '_' . $xsl_name . '_' . $kernel->implode_array($property, '_') . '_' . $kernel->implode_array($external_propertys, '_') . '_' . $site_user_id;

			$cache_name = 'SHOP_TAGS_CLOUD_HTML';
			if (($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name)) && $in_cache)
			{
				echo $in_cache['value'];
				return TRUE;
			}
		}

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<shop id="' . $shop_id . '">' . "\n";

		/* Вносим внешний XML в документ.
		 Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа*/
		if (isset($property['external_xml']))
		{
			$xmlData .= $property['external_xml'];
		}

		// Вносим в XML дополнительные теги из массива дополнительных параметров
		$ExternalXml = new ExternalXml;
		$xmlData .= $ExternalXml->GenXml($external_propertys);
		unset($ExternalXml);

		// Получаем данные о магазине
		$row = $this->GetShop($shop_id);

		// Магазин доступен текущему зарегистрированному пользователю
		if ($this->IssetAccessForShopGroup(array('site_users_id' => $site_user_id,
		'shop_group_id' => 0,
		'$shop_id' => $shop_id)))
		{
			if ($row !== FALSE)
			{
				// Генерируем общие данные о магазине
				$xmlData .= $this->GenXml4Shop($shop_id, $row);

				$xmlData .= $this->GetXml4Tags($shop_id, $property);
			}
		}
		$xmlData .= '</shop>' . "\n";

		$xsl = & singleton('xsl');
		$result = $xsl->build($xmlData, $xsl_name);

		// Проверяем, начинали ли мы кэширование
		if (class_exists('Cache') && $property['cache'])
		{
			$cache->Insert($cache_element_name, $result, $cache_name);
		}

		// Печатаем результат
		echo $result;
		return TRUE;
	}

	/**
	 * Получение информации о значении дополнительного свойства товара по идентификатору значения
	 *
	 * @param int $shop_properties_items_id идентификатор значения дополнительного свойства
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_items_id = 40;
	 *
	 * $row = $shop->GetItemPropertyValueById($shop_properties_items_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с информацией о значении дополнительного свойства в случае успешного выполнения, false - в противном случае
	 */
	function GetItemPropertyValueById($shop_properties_items_id)
	{
		throw new Core_Exception('Method GetItemPropertyValueById() does not allow');
	}

	/**
	 * Получения информации об электронных товарах конкретного товара
	 *
	 * @param int $shop_item_id идентификатор товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_item_id = 159;
	 *
	 * $resource = $shop->GetEitemsForItem($shop_item_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function GetEitemsForItem($shop_item_id)
	{
		$shop_item_id = intval($shop_item_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_eitem_id'),
				array('shop_item_id', 'shop_items_catalog_item_id'),
				array('name', 'shop_eitem_name'),
				array('value', 'shop_eitem_value'),
				array('filename', 'shop_eitem_filename'),
				array('count', 'shop_eitem_count'),
				array('user_id', 'users_id'),
				array(Core_QueryBuilder::expression("IF(`count` = '-1', 2, IF(`count` = '0', 3, 1))"), 'order_by_count')
			)
			->from('shop_item_digitals')
			->where('shop_item_id', '=', $shop_item_id)
			->where('deleted', '=', 0)
			->orderBy('order_by_count')
			->orderBy('count')
			->orderBy('id');

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение данных о значении дополнительного свойства групп товаров
	 *
	 * @param array $param массив параметров
	 * - int $param['shop_groups_id'] Идентификатор группы товаров
	 * - int $param['shop_properties_group_id'] Идентификатор дополнительного свойства
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_groups_id'] = 586;
	 * $param['shop_properties_group_id'] = 12;
	 *
	 * $row = $shop->GetPropertiesGroupValue($param);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed Массив с данными, или false
	 */
	function GetPropertiesGroupValue($param)
	{
		// Идентификатор группы
		if (isset($param['shop_groups_id']))
		{
			$shop_groups_id = intval($param['shop_groups_id']);
		}
		else
		{
			return FALSE;
		}

		// Идентификатор дополнительного свойства
		if (isset($param['shop_properties_group_id']))
		{
			$shop_properties_group_id = intval($param['shop_properties_group_id']);
		}
		else
		{
			return FALSE;
		}

		$oProperty = Core_Entity::factory('Property', $shop_properties_group_id);
		$aPropertyValues = $oProperty->getValues($shop_groups_id);

		if (isset($aPropertyValues[0]))
		{
			return $this->getArrayGroupPropertyValue($aPropertyValues[0]);
		}

		return FALSE;
	}

	/**
	 * Удаление значения дополнительного свойства группы
	 *
	 * @param array $param массив параметров
	 * - int $param['shop_groups_id'] Идентификатор группы товаров
	 * - int $param['shop_properties_group_id'] Идентификатор дополнительного свойства
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_groups_id'] = 599;
	 * $param['shop_properties_group_id'] = 1;
	 *
	 * $result = $shop->DeletePropertiesGroupValue($param);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return mixed Результат выполнения запроса удаления или false
	 */
	function DeletePropertiesGroupValue($param)
	{
		// Идентификатор группы
		if (isset($param['shop_groups_id']))
		{
			$shop_groups_id = intval($param['shop_groups_id']);
		}
		else
		{
			return FALSE;
		}

		// Идентификатор дополнительного свойства
		if (isset($param['shop_properties_group_id']))
		{
			$shop_properties_group_id = intval($param['shop_properties_group_id']);
		}
		else
		{
			return FALSE;
		}

		$oProperty = Core_Entity::factory('Property', $shop_properties_group_id);
		$aPropertyValues = $oProperty->getValues($shop_groups_id);

		if (isset($aPropertyValues[0]))
		{
			$aPropertyValues[0]->delete();

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Вставка значение дополнительного свойства групп товаров
	 *
	 * @param array $param Массив параметров
	 * - str $param['shop_properties_group_value_value'] Значение дополнительного свойства или оригинальное имя файла большого изображения)
	 * - str $param['shop_properties_group_value_value_small'] Оригинальное имя файла малого изображения
	 * - str $param['shop_properties_group_value_file'] Файл изображения
	 * - str $param['shop_properties_group_value_file_small']  Файла малого изображения
	 * <br />int $param['shop_properties_group_id'] Идентификатор дополнительного свойства
	 * <br />int $param['shop_groups_id'] Идентификатор группы товаров
	 * <code>
	 * $shop = new shop();
	 *
	 * $param['shop_properties_group_value_value'] = 'Hello World!!!';
	 * $param['shop_properties_group_value_value_small'] = '';
	 * $param['shop_properties_group_value_file'] = '';
	 * $param['shop_properties_group_value_file_small'] = '';
	 * $param['shop_properties_group_id'] = 9;
	 * $param['shop_groups_id'] = 589;
	 *
	 * $newid = $shop->InsertPropertiesGroupValue($param);
	 *
	 * //Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed
	 */
	function InsertPropertiesGroupValue($param)
	{
		$oProperty = Core_Entity::factory('Property', intval($param['shop_properties_group_id']));
		$shop_groups_id = intval($param['shop_groups_id']);
		$aValues = $oProperty->getValues($shop_groups_id);

		$oValue = count($aValues) > 0
			? $aValues[0] // Value already exist
			: $oProperty->createNewValue($shop_groups_id);

		$information_propertys_groups_value_value = $param['shop_properties_group_value_value'];
		if ($oProperty->type != 2)
		{
			$oValue->setValue($information_propertys_groups_value_value);
		}
		else
		{
			$oValue->file = $param['shop_properties_group_value_file'];
			$oValue->file_name = $information_propertys_groups_value_value;
			$oValue->file_small = $param['shop_properties_group_value_file_small'];
			$oValue->file_small_name = $param['shop_properties_group_value_value_small'];
		}

		$oValue->save();

		return $oValue->id;
	}

	/**
	 * Получение данных о группе дополнительных свойств товара
	 * @param int $shop_properties_items_dir_id Идентификатор группы дополнительных свойств товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_items_dir_id = 3;
	 *
	 * $row = $shop->GetPropertiesItemsDir($shop_properties_items_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed Массив данных, либо False
	 */
	function GetPropertiesItemsDir($shop_properties_items_dir_id)
	{
		$shop_properties_items_dir_id = intval($shop_properties_items_dir_id);
		if (isset($this->cache_propertys_items_dir[$shop_properties_items_dir_id]))
		{
			return $this->cache_propertys_items_dir[$shop_properties_items_dir_id];
		}

		$oProperty_Dir = Core_Entity::factory('Property_Dir')->find($shop_properties_items_dir_id);
		if (!is_null($oProperty_Dir->id))
		{
			return $this->getArrayItemPropertyDir($oProperty_Dir);
		}

		return FALSE;
	}

	/**
	 * Добавление информации о группе дополнительных свойств товара
	 *
	 * @param array $param
	 * - int $param['shop_shops_id'] Идентификатор магазина
	 * - int $param['shop_properties_items_dir_id'] Идентификатор группы дополнительных свойств товара (указывается при редактировании группы)
	 * - str $param['shop_properties_items_dir_parent_id'] Идентификатор родительской группы
	 * - str $param['shop_properties_items_dir_name'] Название группы
	 * - int $param['shop_properties_items_dir_description'] Описание группы
	 * - str $param['shop_properties_items_dir_order'] Порядок сортировки группы
	 * - int $param['users_id'] Идентификатор пользователя центра администрирования, который создал группу. Если не передан - определяется автоматически.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_properties_items_dir_parent_id'] = 0;
	 * $param['shop_properties_items_dir_name'] = 'Новый раздел';
	 * $param['shop_properties_items_dir_description'] = 'Описание раздела';
	 * $param['shop_properties_items_dir_order'] = 0;
	 *
	 * $newid = $shop->InsertPropertiesItemsDir($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed Идентификатор вставленной записи, либо False
	 */
	function InsertPropertiesItemsDir($param)
	{
		if (!isset($param['shop_properties_items_dir_id']) || !$param['shop_properties_items_dir_id'])
		{
			$param['shop_properties_items_dir_id'] = NULL;
		}

		$oProperty_Dir = Core_Entity::factory('Property_Dir', $param['shop_properties_items_dir_id']);
		isset($param['shop_properties_items_dir_parent_id']) && $oProperty_Dir->parent_id = intval($param['shop_properties_items_dir_parent_id']);
		isset($param['shop_properties_items_dir_name']) && $oProperty_Dir->name = $param['shop_properties_items_dir_name'];
		isset($param['shop_properties_items_dir_description']) && $oProperty_Dir->description = $param['shop_properties_items_dir_description'];
		isset($param['shop_properties_items_dir_order']) && $oProperty_Dir->sorting = intval($param['shop_properties_items_dir_order']);
		is_null($oProperty_Dir->id) && isset($param['users_id']) && $param['users_id'] && $oProperty_Dir->user_id = intval($param['users_id']);

		$oProperty_Dir->save();

		if (isset($param['shop_shops_id']))
		{
			$oPropertyDir->Shop_Item_Property_Dir->shop_id = intval($param['shop_shops_id']);
			$oPropertyDir->Shop_Item_Property_Dir->save();
		}

		return $oProperty_Dir->id;
	}

	/**
	 * Удаление информации о группе дополнительных свойств товара
	 *
	 * @param int $shop_properties_items_dir_id Идентификатор группы дополнительных свойств
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_items_dir_id = 1;
	 *
	 * $shop->DeletePropertiesItemsDir($shop_properties_items_dir_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return resource
	 */
	function DeletePropertiesItemsDir($shop_properties_items_dir_id, $shop_shops_id = FALSE)
	{
		$shop_properties_items_dir_id = intval($shop_properties_items_dir_id);
		Core_Entity::factory('Property_Dir', $shop_properties_items_dir_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Получение информации обо всех группах дополнительных свойств товара для конкретного магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param array $param массив параметров
	 * - $param['parent_properties_items_dir_id'] идентификатор группы дополнительных свойств товаров, информацию о подгруппах которой необходимо получить.
	 * <br /> по умолчанию равен false - получаем информацию о всех группах дополнительных свойств товаров.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $resource = $shop->GetAllPropertiesItemsDirForShop($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function GetAllPropertiesItemsDirForShop($shop_shops_id, $param = array())
	{
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('property_dirs.id', 'shop_properties_items_dir_id'),
			array('shop_id', 'shop_shops_id'),
			array('parent_id', 'shop_properties_items_dir_parent_id'),
			array('name', 'shop_properties_items_dir_name'),
			array('description', 'shop_properties_items_dir_description'),
			array('sorting', 'shop_properties_items_dir_order'),
			array('user_id', 'users_id')
		)
			->from('property_dirs')
			->join('shop_item_property_dirs', 'property_dirs.id', '=', 'shop_item_property_dirs.property_dir_id')
			->where('property_dirs.deleted', '=', 0)
			->where('shop_id', '=', $shop_shops_id)
			->orderBy('sorting');

		if (isset($param['parent_properties_items_dir_id'])
		&& $param['parent_properties_items_dir_id'] !== FALSE)
		{
			$queryBuilder->where('parent_id', '=', intval($param['parent_properties_items_dir_id']));
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение информации обо всех свойствах хранящихся в конкретной группе дополнительных свойств конкртеного магазина
	 *
	 * @param array $param Массив параметров
	 * - $param['shop_shops_id'] Идентификатор магазина
	 * - $param['shop_properties_items_dir_id'] Идентификатор группы дополнительных свойств товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_properties_items_dir_id'] = 3;
	 *
	 * $resource = $shop->GetAllPropertiesItemsForDir($param);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource Ответ базы
	 */
	function GetAllPropertiesItemsForDir($param)
	{
		$shop_shops_id = Core_Type_Conversion::toInt($param['shop_shops_id']);
		$shop_properties_items_dir_id = Core_Type_Conversion::toInt($param['shop_properties_items_dir_id']);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_list_of_properties_id'),
				array('shop_id', 'shop_shops_id'),
				array('shop_measure_id', 'shop_mesures_id'),
				array('list_id', 'lists_id'),
				array('name', 'shop_list_of_properties_name'),
				array('tag_name', 'shop_list_of_properties_xml_name'),
				array('type', 'shop_list_of_properties_type'),
				array('prefix', 'shop_list_of_properties_prefics'),
				array('default_value', 'shop_list_of_properties_default_value'),
				array('sorting', 'shop_list_of_properties_order'),
				array('filter', 'shop_list_of_properties_show_kind'),
				array('user_id', 'users_id'),
				array('guid', 'shop_list_of_properties_cml_id'),
				array('property_dir_id', 'shop_properties_items_dir_id'),
				array('image_large_max_width', 'shop_list_of_properties_default_big_width'),
				array('image_large_max_height', 'shop_list_of_properties_default_big_height'),
				array('image_small_max_width', 'shop_list_of_properties_default_small_width'),
				array('image_small_max_height', 'shop_list_of_properties_default_small_height'),
				array('description', 'shop_list_of_properties_description')
			)
			->from('properties')
			->join('shop_item_properties', 'shop_item_properties.property_id', '=', 'properties.id')
			->where('shop_item_properties.shop_id', '=', $shop_shops_id)
			->where('properties.property_dir_id', '=', $shop_properties_items_dir_id)
			->where('properties.deleted', '=', 0);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение пути от текущего каталога дополнительных свойств товара к корневому
	 *
	 * @param integer $shop_properties_items_dir_id идентификатор текущего каталога
	 * @param boolean $first_call первый ли это вызов функции
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_items_dir_id = 3;
	 *
	 * $row = $shop->GetAdditionalPropertyPathArray($shop_properties_items_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array массив данных
	 */
	function GetAdditionalPropertyPathArray($shop_properties_items_dir_id, $first_call = TRUE)
	{
		$shop_properties_items_dir_id = intval($shop_properties_items_dir_id);

		$first_call = Core_Type_Conversion::toBool($first_call);

		// Обнуляем массив
		$first_call && $this->property_path_array = array();

		$shop_properties_items_dir_row = $this->GetPropertiesItemsDir($shop_properties_items_dir_id);

		if ($shop_properties_items_dir_row)
		{
			$this->property_path_array = $this->GetAdditionalPropertyPathArray($shop_properties_items_dir_row['shop_properties_items_dir_parent_id'], FALSE);
			$this->property_path_array[$shop_properties_items_dir_id] = $shop_properties_items_dir_row['shop_properties_items_dir_name'];
		}
		else
		{
			$this->property_path_array[0] = '';
			unset($this->property_path_array[0]);
		}

		return $this->property_path_array;
	}

	/**
	 * Получение пути от текущего каталога дополнительных свойств групп товаров к корневому
	 *
	 * @param integer $shop_properties_items_dir_id идентификатор текущего каталога
	 * @param boolean $first_call первый ли это вызов функции
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_groups_dir_id =7;
	 *
	 * $row = $shop->GetAdditionalPropertyDirPathArray($shop_properties_groups_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array массив данных
	 */
	function GetAdditionalPropertyDirPathArray($shop_properties_groups_dir_id, $first_call = TRUE)
	{
		$shop_properties_groups_dir_id = intval($shop_properties_groups_dir_id);
		$first_call = Core_Type_Conversion::toBool($first_call);

		if ($first_call)
		{
			// Обнуляем массив.
			$this->property_path_dir_array = array();
		}

		$shop_properties_groups_dir_row = $this->GetPropertiesGroupsDir($shop_properties_groups_dir_id);
		if ($shop_properties_groups_dir_row)
		{
			$this->property_path_dir_array = $this->GetAdditionalPropertyDirPathArray($shop_properties_groups_dir_row['shop_properties_groups_dir_parent_id'], FALSE);
			$this->property_path_dir_array[$shop_properties_groups_dir_id] = $shop_properties_groups_dir_row['shop_properties_groups_dir_name'];
		}
		else
		{
			$this->property_path_dir_array[0] = '';
			unset($this->property_path_dir_array[0]);
		}

		return $this->property_path_dir_array;
	}

	/**
	 * Формирование массива групп дополнительных свойств товара самого верхнего уровня для данного магазина
	 *
	 * @param int $shop_id идентификатор магазина, для которого заполняем массив групп самого верхнего уровня
	 * @param array $param массив дополнительных параметров
	 * - $param['cache_off']  - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id =1;
	 *
	 * $row = $shop->FillMasGroupExtProperty($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 */
	function FillMasGroupExtProperty($shop_shops_id, $param = array())
	{
		if ($shop_shops_id !== FALSE)
		{
			$shop_shops_id = intval($shop_shops_id);
		}

		// Очищаем текущий массив
		$this->mas_groups_dir = array();

		$this->CachePropertiesItemsDir[$shop_shops_id] = array();
		$result = $this->GetAllPropertiesItemsDirForShop($shop_shops_id);
		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$this->mas_groups_dir[$row['shop_properties_items_dir_id']] = $row;
				$this->CachePropertiesItemsDir[$shop_shops_id][$row['shop_properties_items_dir_parent_id']][] = $row['shop_properties_items_dir_id'];
			}
		}

		return $this->mas_groups_dir;
	}

	/**
	 * Формирование дерева групп для магазина
	 *
	 * @param int $shop_parent_group_id идентификатор группы, относительно которой строится дерево групп.
	 * @param int $shop_id идентификатор магазина, для которого строится дерево групп.
	 * @param string $separator символ, отделяющий группу нижнего уровня от родительской группы.
	 * @param int $shop_groups_id идентификатор группы, которую вместе с ее подгруппами не нужно включать в дерево групп, если id = false, то включать в дерево групп все подгруппы.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_items_dir_parent_id = 0;
	 * $shop_shops_id = 1;
	 * $separator = '';
	 *
	 * $row = $shop->GetDelimitedGroupsExtProperty($shop_properties_items_dir_parent_id, $shop_shops_id , $separator='');
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array двумерный массив, содержащий дерево подгрупп.
	 */
	function GetDelimitedGroupsExtProperty($shop_properties_items_dir_parent_id, $shop_shops_id, $separator = '', $shop_properties_items_dir_id = FALSE)
	{
		$shop_properties_items_dir_parent_id = intval($shop_properties_items_dir_parent_id);
		$shop_shops_id = intval($shop_shops_id);
		$separator = quote_smart($separator);

		if (!isset($this->CachePropertiesItemsDir[$shop_shops_id]))
		{
			$this->FillMasGroupExtProperty($shop_shops_id, array('cache_off' => TRUE));
		}

		if (isset($this->CachePropertiesItemsDir[$shop_shops_id][$shop_properties_items_dir_parent_id]))
		{
			foreach ($this->CachePropertiesItemsDir[$shop_shops_id][$shop_properties_items_dir_parent_id] as $shop_properties_item_dir_id)
			{
				$row = $this->GetPropertiesItemsDir($shop_properties_item_dir_id);

				if ($shop_properties_items_dir_id !== Core_Type_Conversion::toInt($row['shop_properties_items_dir_id']))
				{
					$count_mas = count($this->mas_property_dir_groups);
					$row['separator'] = $separator;
					$this->mas_property_dir_groups[$count_mas] = $row;

					$this->GetDelimitedGroupsExtProperty($row['shop_properties_items_dir_id'], $shop_shops_id, $separator.$separator, $shop_properties_items_dir_id);
				}
			}
		}

		return $this->mas_property_dir_groups;
	}

	/**
	 * Получение информации о группе дополнительных свойств групп товаров
	 *
	 * @param int $shop_properties_groups_dir_id Идентификатор группы дополнительных свойств групп товаров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_groups_dir_id = 7;
	 *
	 * $row = $shop->GetPropertiesGroupsDir($shop_properties_groups_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed Массив данных, либо False
	 */
	function GetPropertiesGroupsDir($shop_properties_groups_dir_id)
	{
		$shop_properties_groups_dir_id = intval($shop_properties_groups_dir_id);
		$oProperty_Dir = Core_Entity::factory('Property_Dir')->find($shop_properties_groups_dir_id);
		if (!is_null($oProperty_Dir->id))
		{
			return $this->getArrayGroupPropertyDir($oProperty_Dir);
		}

		return FALSE;
	}

	/**
	 * Добавление информации о группе дополнительных свойств каталогов товаров
	 *
	 * @param array $param Массив параметров
	 * - int $param['shop_properties_groups_dir_id'] Идентификатор группы дополнительных свойств группы товаров
	 * - int $param['shop_shops_id'] Идентификатор магазина
	 * - int $param['shop_properties_groups_dir_parent_id'] Идентификатор родительской группы дополнительных свойств группы товаров
	 * - int $param['shop_properties_groups_dir_name'] Название группы дополнительных свойств группы товаров
	 * - int $param['shop_properties_groups_dir_description'] Описание группы дополнительных свойств группы товаров
	 * - int $param['shop_properties_groups_dir_order'] Порядок сортировки группы дополнительных свойств группы товаров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_properties_groups_dir_id'] = '';
	 * $param['shop_shops_id'] = 1;
	 * $param['shop_properties_groups_dir_parent_id'] = 0;
	 * $param['shop_properties_groups_dir_name'] = 'new';
	 * $param['shop_properties_groups_dir_description'] = 'новая';
	 * $param['shop_properties_groups_dir_order'] = 'ASC';
	 *
	 * $newid = $shop->InsertPropertiesGroupsDir($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed Идентификатор вставленной записи, либо False
	 */
	function InsertPropertiesGroupsDir($param)
	{
		if (!isset($param['shop_properties_groups_dir_id']) || !$param['shop_properties_groups_dir_id'])
		{
			$param['shop_properties_groups_dir_id'] = NULL;
		}

		$oProperty_Dir = Core_Entity::factory('Property_Dir', $param['shop_properties_groups_dir_id']);
		isset($param['shop_properties_groups_dir_parent_id']) && $oProperty_Dir->parent_id = intval($param['shop_properties_groups_dir_parent_id']);
		isset($param['shop_properties_groups_dir_name']) && $oProperty_Dir->name = $param['shop_properties_groups_dir_name'];
		isset($param['shop_properties_groups_dir_description']) && $oProperty_Dir->description = $param['shop_properties_groups_dir_description'];
		isset($param['shop_properties_groups_dir_order']) && $oProperty_Dir->sorting = intval($param['shop_properties_groups_dir_order']);
		is_null($oProperty_Dir->id) && isset($param['users_id']) && $param['users_id'] && $oProperty_Dir->user_id = intval($param['users_id']);

		$oProperty_Dir->save();

		if (isset($param['shop_shops_id']))
		{
			$oPropertyDir->Shop_Group_Property_Dir->shop_id = intval($param['shop_shops_id']);
			$oPropertyDir->Shop_Group_Property_Dir->save();
		}

		return $oProperty_Dir->id;
	}

	/**
	 * Удаление информации о группе дополнительных свойств групп товаров
	 *
	 * @param int $shop_properties_groups_dir_id Идентификатор группы дополнительных свойств
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_groups_dir_id = 3;
	 * $shop_shops_id = 1;
	 *
	 * $result = $shop->DeletePropertiesGroupsDir($shop_properties_groups_dir_id, $shop_shops_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 *
	 * ?>
	 * </code>
	 * @return resource
	 */
	function DeletePropertiesGroupsDir($shop_properties_groups_dir_id, $shop_shops_id)
	{
		$shop_properties_groups_dir_id = intval($shop_properties_groups_dir_id);
		Core_Entity::factory('Property_Dir', $shop_properties_groups_dir_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Получение инфомации обо всех группах дополнительных свойств групп товаров конкретного магазина
	 *
	 * @param int $shop_shops_id Идентификатор магазина
	 * @param array $param массив параметров
	 * - $param['parent_properties_groups_dir_id'] идентификатор группы дополнительных свойств групп товаров, информацию о подгруппах которой необходимо получить.
	 * <br /> по умолчанию равен false - получаем информацию о всех группах дополнительных свойств групп товаров.
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $resource = $shop->GetAllPropertiesGroupsDirForShop($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed Resource или False
	 */
	function GetAllPropertiesGroupsDirForShop($shop_shops_id, $param = array())
	{
		$shop_shops_id = Core_Type_Conversion::toInt($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('property_dirs.id', 'shop_properties_groups_dir_id'),
			array('shop_id', 'shop_shops_id'),
			array('parent_id', 'shop_properties_groups_dir_parent_id'),
			array('name', 'shop_properties_groups_dir_name'),
			array('description', 'shop_properties_groups_dir_description'),
			array('sorting', '	shop_properties_groups_dir_order'),
			array('user_id', 'users_id')
		)
			->from('property_dirs')
			->join('shop_group_property_dirs', 'property_dirs.id', '=', 'shop_group_property_dirs.property_dir_id')
			->where('deleted', '=', 0)
			->where('shop_id', '=', $shop_shops_id)
			->orderBy('sorting');

		if (isset($param['parent_properties_groups_dir_id'])
		&& $param['parent_properties_groups_dir_id'] !== FALSE)
		{
			$queryBuilder->where('parent_id', '=', intval($param['parent_properties_groups_dir_id']));
		}

		$result = $queryBuilder->execute()->asAssoc()->getResult();

		return mysql_num_rows($result)
			? $result
			: FALSE;
	}

	/**
	 * Получение списка всех групп дополнительных свойств групп товаров
	 *
	 * @param int $shop_properties_groups_dir_id Идентификатор группы дополнительных свойств групп товаров
	 * @param int $shop_shops_id Идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_groups_dir_id = 9;
	 * $shop_shops_id = 1;
	 *
	 * $resource = $shop->GetAllPropertiesGroupsDirForDir($shop_properties_groups_dir_id, $shop_shops_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource, либо False
	 */
	function GetAllPropertiesGroupsDirForDir($shop_properties_groups_dir_id, $shop_shops_id)
	{
		$shop_properties_groups_dir_id = intval($shop_properties_groups_dir_id);
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('property_dirs.id', 'shop_properties_groups_dir_id'),
				array('shop_id', 'shop_shops_id'),
				array('parent_id', 'shop_properties_groups_dir_parent_id'),
				array('name', 'shop_properties_groups_dir_name'),
				array('description', 'shop_properties_groups_dir_description'),
				array('sorting', 'shop_properties_groups_dir_order'),
				array('user_id', 'users_id')
			)
			->from('property_dirs')
			->join('shop_group_property_dirs', 'property_dirs.id', '=', 'shop_group_property_dirs.property_dir_id')
			->where('shop_id', '=', $shop_shops_id)
			->where('parent_id', '=', $shop_properties_groups_dir_id)
			->where('deleted', '=', 0)
			->orderBy('sorting');

		$result = $queryBuilder->execute()->asAssoc()->getResult();

		return $result;
	}

	/**
	 * Заполнение массива групп дополнительных свойств групп товаров
	 *
	 * @param int $shop_shops_id Идентификатор магазина
	 * @param array $param Массив дополнительных параметров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->FillMasGroupDirExtProperty($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array Массив групп
	 */
	function FillMasGroupDirExtProperty($shop_shops_id, $param = array())
	{
		if ($shop_shops_id !== FALSE)
		{
			$shop_shops_id = intval($shop_shops_id);
		}

		// Очищаем текущий массив
		$this->mas_ext_groups_dir = array();

		$this->CachePropertiesGroupsDir[$shop_shops_id] = array();

		$result = $this->GetAllPropertiesGroupsDirForShop($shop_shops_id);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$this->mas_ext_groups_dir[$row['shop_properties_groups_dir_id']] = $row;

				$this->CachePropertiesGroupsDir[$shop_shops_id][$row['shop_properties_groups_dir_parent_id']][] = $row['shop_properties_groups_dir_id'];
			}
		}

		return $this->mas_ext_groups_dir;
	}

	/**
	 * Метод формирования дерева групп дополнительных свойств групп товаров для магазина.
	 *
	 * @param int $shop_properties_groups_dir_parent_id Идентификатор директории с которой начинать построение дерева
	 * @param int $shop_shops_id Идентификатор магазина
	 * @param int $separator Разделитель между двумя разными уровнями вложения
	 * @param int $shop_properties_groups_dir_id Идентификатор группы дополнительных свойств групп товаров
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_groups_dir_parent_id = 9;
	 * $shop_shops_id = 1;
	 *
	 * $row = $shop->GetDelimitedGroupsDirExtProperty($shop_properties_groups_dir_parent_id,$shop_shops_id , $separator='');
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array двумерный массив, содержащий дерево подгрупп.
	 */
	function GetDelimitedGroupsDirExtProperty($shop_properties_groups_dir_parent_id, $shop_shops_id, $separator = '', $shop_properties_groups_dir_id = FALSE)
	{
		$shop_properties_groups_dir_parent_id = intval($shop_properties_groups_dir_parent_id);
		$shop_shops_id = intval($shop_shops_id);
		$separator = quote_smart($separator);

		if (!isset($this->CachePropertiesGroupsDir[$shop_shops_id]))
		{
			$this->FillMasGroupDirExtProperty($shop_shops_id, array(
			'cache_off' => TRUE
			));
		}

		if (isset($this->CachePropertiesGroupsDir[$shop_shops_id][$shop_properties_groups_dir_parent_id]))
		{
			foreach ($this->CachePropertiesGroupsDir[$shop_shops_id][$shop_properties_groups_dir_parent_id] as $shop_properties_group_dir_id)
			{
				$row = $this->GetPropertiesGroupsDir($shop_properties_group_dir_id);

				if ($shop_properties_groups_dir_id !== $row['shop_properties_groups_dir_id'])
				{
					$count_mas = count($this->mas_property_dir_groups);
					$row['separator'] = $separator;
					$this->mas_property_dir_groups[$count_mas] = $row;

					$this->GetDelimitedGroupsDirExtProperty($row['shop_properties_groups_dir_id'], $shop_shops_id, $separator.$separator, $shop_properties_groups_dir_id);
				}
			}
		}

		return $this->mas_property_dir_groups;
	}

	/**
	 * Копирование значений дополнительных свойств товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор копируемого товара
	 * @param int $shop_items_catalog_item_id_copy идентификатор копии товара
	 * @param int $shop_shops_id идентификатор магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 158;
	 * $shop_items_catalog_item_id_copy = 159;
	 * $shop_shops_id = 1;
	 *
	 * $shop->CopyExternalPropertiesForItem($shop_items_catalog_item_id, $shop_items_catalog_item_id_copy, $shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyExternalPropertiesForItem($shop_items_catalog_item_id, $shop_items_catalog_item_id_copy, $shop_shops_id)
	{
		$oSource_Item = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id);
		$oTarget_Item = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id_copy);

		$aPropertyValues = $oSource_Item->getPropertyValues();
		foreach($aPropertyValues as $oPropertyValue)
		{
			$oNewPropertyValue = clone $oPropertyValue;
			$oNewPropertyValue->entity_id = $oTarget_Item->id;
			$oNewPropertyValue->save();

			if ($oNewPropertyValue->Property->type == 2)
			{
				// Копируем файлы
				// Warning: Добавить
			}
		}

		return TRUE;
	}

	/**
	 * Копирование товара
	 *
	 * @param int $item_catalog_id идентификатор копируемого товара
	 * @param bool $copy_external_properties флаг, указывающий, нужно ли копировать доп. свойства товара (по умолчанию true)
	 * @param bool $property Массив дополнительных параметров
	 * - bool $property['copy_original_name'] флаг, указывающий, нужно ли копировать оригинальное имя товара, не изменяя его, либо добавлять слово "копия" (по умолчанию false)
	 *
	 * @return int идентификатор копии товара
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $item_catalog_id = 158;
	 *
	 * $newid = $shop->CopyItem($item_catalog_id);
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 */
	function CopyItem($item_catalog_id, $copy_external_properties = TRUE, $property = array())
	{
		$item_catalog_id = intval($item_catalog_id);
		$oNew_Shop_Item = Core_Entity::factory('Shop_Item', $item_catalog_id)->copy();
		return $oNew_Shop_Item->id;
	}

	/**
	 * Получение информации о принадлежности дополнительного свойства группе товаров
	 * @param int $shop_properties_item_for_groups_id Идентификатор принадлежности дополнительного свойства группе товаров
	 * @return mixed массив данных, либо False
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_item_for_groups_id = 2;
	 *
	 * $row = $shop->GetPropertiesItemForGroups($shop_properties_item_for_groups_id);
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 */
	function GetPropertiesItemForGroups($shop_properties_item_for_groups_id)
	{
		$shop_properties_item_for_groups_id = intval($shop_properties_item_for_groups_id);

		$oShop_Item_Property_For_Group = Core_Entity::factory('Shop_Item_Property_For_Group')->find($shop_properties_item_for_groups_id);

		return !is_null($oShop_Item_Property_For_Group->id)
			? $this->getArrayShopItemPropertyForGroup($oShop_Item_Property_For_Group)
			: FALSE;
	}

	/**
	 * Получение информации о специальной цене
	 *
	 * @param int $shop_special_prices_id идентификатор специальной цены
	 * @return mixed массив данных, либо False
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_special_prices_id = 1;
	 *
	 * $row = $shop->GetSpecialPrice($shop_special_prices_id);
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 */
	function GetSpecialPrice($shop_special_prices_id)
	{
		$shop_special_prices_id = intval($shop_special_prices_id);

		if (isset($this->CacheGetSpecialPrice[$shop_special_prices_id]))
		{
			return $this->CacheGetSpecialPrice[$shop_special_prices_id];
		}

		$oShop_Specialprice = Core_Entity::factory('Shop_Specialprice')->find($shop_special_prices_id);

		return $this->CacheGetSpecialPrice[$shop_special_prices_id] = !is_null($oShop_Specialprice)
			? $this->getArrayShopSpecialprice($oShop_Specialprice)
			: FALSE;
	}

	/**
	 * Вставка/обновление информации о специальной цене
	 * @param arr $param массив параметров
	 * - int $param['shop_special_prices_id'] идентификатор специальной цены
	 * - int $param['shop_items_catalog_item_id'] идентификатор товара, которому принадлежит специальная цена
	 * - int $param['shop_special_prices_from'] минимальное количество товара, с которого начинает действовать специальная цена
	 * - int $param['shop_special_prices_to'] максимальное количество товара, с которого начинает действовать специальная цена
	 * - int $param['shop_special_prices_price'] значение цены за один товар
	 * - int $param['shop_special_prices_percent'] процент от базовой цены
	 *
	 * @return mixed Идентификатор вставленной записи, либо код ошибки: <br>false = ошибка вставки информации в БД <br>-1 = произошло пересечение множеств, на которых доступна цена редактируемой цены с существующими (в этом случае происходит удаление редактируемой цены) <br>-2 произошло пересечение множеств, на которых доступна цена добавляемой цены с существующими <br>-3 некорректно задан интервал количества товаров редактируемой цены (в этом случае происходит удаление редактируемой цены) <br>-4 некорректно задан интервал количества товаров при добавлении
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $param['shop_items_catalog_item_id'] = 159;
	 * $param['shop_special_prices_from'] = 55;
	 * $param['shop_special_prices_to'] = 75;
	 * $param['shop_special_prices_price'] = 400;
	 *
	 * $newid = $shop->InsertSpecialPrice($param);
	 *
	 * echo $newid;
	 * ?>
	 * </code>
	 */
	function InsertSpecialPrice($param)
	{
		if(!isset($param['shop_special_prices_id']) || !$param['shop_special_prices_id'])
		{
			$param['shop_special_prices_id'] = NULL;
		}

		$oShop_Specialprice = Core_Entity::factory('Shop_Specialprice', $param['shop_special_prices_id']);

		if (isset($param['shop_items_catalog_item_id']))
		{
			$oShop_Specialprice->shop_item_id = intval($param['shop_items_catalog_item_id']);
		}
		else
		{
			return FALSE;
		}

		if (isset($param['shop_special_prices_from']))
		{
			$shop_special_prices_from = intval($param['shop_special_prices_from']);
			$oShop_Specialprice->min_quantity = $shop_special_prices_from;
		}
		else
		{
			return FALSE;
		}

		$oShop_Specialprice->percent = isset($param['shop_special_prices_percent'])
			? floatval($param['shop_special_prices_percent'])
			: '0.00';

		$queryBuilder = Core_QueryBuilder::select()
			->from('shop_specialprices')
			->where('deleted', '=', 0);

		// При редактировании - не учитываем пересечение множеств, на которых доступна цена, цены самих с собой
		if (isset($param['shop_special_prices_id']) && $this->GetSpecialPrice($param['shop_special_prices_id']))
		{
			$update = TRUE;
			$queryBuilder->where('id', '!=', intval($param['shop_special_prices_id']));
		}
		else
		{
			$update = FALSE;
		}

		if (isset($param['shop_special_prices_to']) && Core_Type_Conversion::toInt($param['shop_special_prices_to']) > 0 && Core_Type_Conversion::toInt($param['shop_special_prices_to']) >= $shop_special_prices_from)
		{
			$shop_special_prices_to = intval($param['shop_special_prices_to']);
			$oShop_Specialprice->max_quantity = $shop_special_prices_to;
		}
		// некорректно задан интервал количества товаров
		else
		{
			// При обновлении - удаляем цену
			if ($update)
			{
				$this->DeleteSpecialPrice($param['shop_special_prices_id']);
				return -3;
			}
			return -4;
		}

		// Множества, на которых доступны цены, не могут иметь общих значений
		$count = $queryBuilder->execute()->getNumRows();

		if ($count > 0)
		{
			// Обнаружено пересечение, выход
			// При обновлении - удаляем цену
			if ($update)
			{
				$this->DeleteSpecialPrice($param['shop_special_prices_id']);
				return -1;
			}

			return -2;
		}

		if (isset($param['shop_special_prices_price']))
		{
			$shop_special_prices_price = floatval($param['shop_special_prices_price']);

			$shop_special_prices_price > 2147483647 && $shop_special_prices_price = 2147483647;
			$oShop_Specialprice->price = $shop_special_prices_price;
		}

		$oShop_Specialprice->save();

		return $oShop_Specialprice->id;
	}

	/**
	 * Удаление информации о специальной цене
	 *
	 * @param int $shop_special_prices_id идентификатор специальной цены
	 * @return resource
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_special_prices_id = 7;
	 *
	 * $result = $shop->DeleteSpecialPrice($shop_special_prices_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Удаление выполнено успешно";
	 * }
	 * 	else
	 * {
	 *	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 */
	function DeleteSpecialPrice($shop_special_prices_id)
	{
		$shop_special_prices_id = intval($shop_special_prices_id);
		Core_Entity::factory('Shop_Specialprice', $shop_special_prices_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Получение информации о всех специальных ценах товара
	 *
	 * @param mixed $shop_items_catalog_item_id идентификатор или массив идентификаторов товаров
	 * @return mixed Resource или False
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 159;
	 *
	 * $resource = $shop->GetAllSpecialPricesForItem($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 */
	function GetAllSpecialPricesForItem($shop_items_catalog_item_id)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_special_prices_id'),
				array('shop_item_id', 'shop_items_catalog_item_id'),
				array('min_quantity', 'shop_special_prices_from'),
				array('max_quantity', 'shop_special_prices_to'),
				array('price', 'shop_special_prices_price'),
				array('percent', 'shop_special_prices_percent')
			)
			->from('shop_specialprices')
			->orderBy('min_quantity')
			->orderBy('max_quantity')
			->orderBy('price');

		is_array($shop_items_catalog_item_id) && count($shop_items_catalog_item_id) > 0
			? $queryBuilder->where('shop_item_id', 'IN', $shop_items_catalog_item_id)
			: $queryBuilder->where('shop_item_id', '=', intval($shop_items_catalog_item_id));

		$result = $queryBuilder->execute()->asAssoc()->getResult();

		return mysql_num_rows($result)
			? $result
			: FALSE;
	}

	/**
	 * Получение информации в виде массива о всех специальных ценах товара
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @return mixed Array или False
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 159;
	 *
	 * $array = $shop->GetSpecialPricesForItem($shop_items_catalog_item_id);
	 *
	 * // Распечатаем результат
	 * if($array)
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 */
	function GetSpecialPricesForItem($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		if (isset($this->CacheGetSpecialPricesForItem[$shop_items_catalog_item_id]))
		{
			return $this->CacheGetSpecialPricesForItem[$shop_items_catalog_item_id];
		}

		$result = $this->GetAllSpecialPricesForItem($shop_items_catalog_item_id);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$this->CacheGetSpecialPricesForItem[$shop_items_catalog_item_id][$row['shop_special_prices_id']] = $row;

				// Кэш для метода GetSpecialPrice()
				$this->CacheGetSpecialPrice[$row['shop_special_prices_id']] = $row;
			}
		}
		else
		{
			$this->CacheGetSpecialPricesForItem[$shop_items_catalog_item_id] = FALSE;
		}

		return $this->CacheGetSpecialPricesForItem[$shop_items_catalog_item_id];
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров
	 * специальными ценами. Заполнению подвергается массив
	 * $this->CacheGetSpecialPricesForItem [$shop_items_catalog_item_id]
	 *
	 * @param array $mas_items_in массив идентификаторов товаров
	 */
	function FillMemCacheSpecialPricesForItem($mas_items_in)
	{
		$mas_items_in = Core_Type_Conversion::toArray($mas_items_in);

		if (count($mas_items_in) > 0)
		{
			// Меняем местами значения и ключи массива
			$aTmp = array_flip($mas_items_in);

			// Вычислить пересечение массивов, сравнивая ключи
			$aTmpIntersect = array_intersect_key($aTmp, $this->CacheGetSpecialPricesForItem);

			if (count($aTmpIntersect) != count($mas_items_in))
			{
				// Заполянем массив значениями false
				foreach ($mas_items_in as $shop_items_catalog_item_id)
				{
					$this->CacheGetSpecialPricesForItem[$shop_items_catalog_item_id] = FALSE;
				}

				$result = $this->GetAllSpecialPricesForItem($mas_items_in);

				if ($result)
				{
					while ($row = mysql_fetch_assoc($result))
					{
						$this->CacheGetSpecialPricesForItem[$row['shop_items_catalog_item_id']][$row['shop_special_prices_id']] = $row;

						// Кэш для метода GetSpecialPrice()
						$this->CacheGetSpecialPrice[$row['shop_special_prices_id']] = $row;
					}
				}
			}
		}
	}

	/**
	 * Формирование XML для специальных цен товара
	 *
	 * @param int $shop_special_prices_id идентификатор специальной цены
	 * @return str XML данные
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_special_prices_id= 8;
	 *
	 * $newxml = $shop->GenXML4SpecialPrice($shop_special_prices_id);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($newxml);
	 * ?>
	 * </code>
	 */
	function GenXML4SpecialPrice($shop_special_prices_id)
	{
		$shop_special_prices_id = intval($shop_special_prices_id);
		$special_prices_row = $this->GetSpecialPrice($shop_special_prices_id);

		if ($special_prices_row)
		{
			$xmlData = '<special_price id="' . $shop_special_prices_id . '" item_id="' . $special_prices_row['shop_items_catalog_item_id'] . '">' . "\n";
			$xmlData .= '<shop_special_prices_from>' . str_for_xml($special_prices_row['shop_special_prices_from']) . '</shop_special_prices_from>' . "\n";
			$xmlData .= '<shop_special_prices_to>' . str_for_xml($special_prices_row['shop_special_prices_to']) . '</shop_special_prices_to>' . "\n";
			$xmlData .= '<shop_special_prices_percent>' . str_for_xml($special_prices_row['shop_special_prices_percent']) . '</shop_special_prices_percent>' . "\n";

			// Обработка shop_special_prices_percent идет в GetPriceForUser()
			//if ($special_prices_row['shop_special_prices_percent'] != 0)
			//{
				// Если есть модуль "Пользователи сайта", получим текущего пользователя
				if (class_exists('SiteUsers'))
				{
					$SiteUsers = & singleton('SiteUsers');
					$site_users_id = $SiteUsers->GetCurrentSiteUser();
				}
				else
				{
					$site_users_id = 0;
				}

				$aPrice = $this->GetPriceForUser($site_users_id,
				$special_prices_row['shop_items_catalog_item_id'], array(),
				array('shop_special_prices_id' => $shop_special_prices_id));

				$price = $aPrice['price_discount'];
			/*}
			else
			{
				$item_row = $this->GetItem($special_prices_row['shop_items_catalog_item_id']);

				// Получаем данные о магазине
				$shop_row = $this->GetShop($item_row['shop_shops_id']);

				// Определяем коэффициент пересчета
				$currency_k = $this->GetCurrencyCoefficientToShopCurrency($item_row['shop_currency_id'], $shop_row['shop_currency_id']);

				$price = $currency_k * $special_prices_row['shop_special_prices_price'];
			}*/

			$xmlData .= '<shop_special_prices_price>' . str_for_xml($price) . '</shop_special_prices_price>' . "\n";
			$xmlData .= '</special_price>' . "\n";
		}

		return $xmlData;
	}

	/**
	 * Показ списка магазинов
	 *
	 * @param int $site_id Идентификатор сайта
	 * @param str $xsl_name Имя xsl шаблона
	 * @param array $param массив параметров
	 * @param array $external_propertys массив внешних данных для включения в XML
	 * @return bool
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $site_id = CURRENT_SITE;
	 * $xsl_name = 'СписокМагазинов';
	 *
	 * $shop->ShowShops($site_id, $xsl_name);
	 * ?>
	 * </code>
	 */
	function ShowShops($site_id, $xsl_name, $param = array(), $external_propertys = array())
	{
		$site_id = Core_Type_Conversion::toInt($site_id);
		$xsl_name = Core_Type_Conversion::toStr($xsl_name);
		$param = Core_Type_Conversion::toArray($param);
		$external_propertys = Core_Type_Conversion::toArray($external_propertys);

		// Получаем список магазинов
		$shops_res = $this->GetAllShops($site_id);

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

		if ($shops_res)
		{
			$xmlData .= '<shops>' . "\n";

			// Вносим в XML дополнительные теги из массива дополнительных параметров
			$ExternalXml = new ExternalXml();
			$xmlData .= $ExternalXml->GenXml($external_propertys);

			while ($shops_row = mysql_fetch_assoc($shops_res))
			{
				// получаем информацию о каждом магазине
				$shop_row = $this->GetShop($shops_row['shop_shops_id']);

				if ($shop_row)
				{
					$xmlData .= '<shop id="' . str_for_xml($shop_row['shop_shops_id']) . '">' . "\n";

					// Генерируем данные о магазине
					$xmlData .= $this->GenXml4Shop($shop_row['shop_shops_id'], $shop_row);

					$xmlData .= '</shop>' . "\n";
				}
			}

			$xmlData .= '</shops>' . "\n";
		}

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
		return TRUE;
	}

	/**
	 * Копирование информации о магазине, а также дополнительных свойствах товаров, групп и групп этих свойств
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @return mixed идентификатор нового магазина, либо False
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id= 1;
	 *
	 * $newid = $shop->CopyShop($shop_shops_id);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 */
	function CopyShop($shop_shops_id, $new_site_id = FALSE, $structure_id = FALSE, $shop_dir_id = FALSE)
	{
		$shop_shops_id = intval($shop_shops_id);

		$oShop = Core_Entity::factory('Shop')->find($shop_shops_id);

		if (!is_null($oShop->id))
		{
			$oNew_Shop = $oShop->copy();

			$oShop->structure_id = $structure_id === FALSE
				? 0
				: intval($structure_id);

			$shop_dir_id !== FALSE && $oShop->shop_dir_id = intval($shop_dir_id);

			if ($new_site_id !== FALSE)
			{
				$oShop->site_id = intval($new_site_id);
			}
			else
			{
				$oShop->site_id = CURRENT_SITE;
				$oShop->name .= "' [Копия " . date('d.m.Y H:i:s') . "]')";
			}

			return $oNew_Shop->id;
		}

		return FALSE;
	}

	/**
	 * Копирование дополнительных свойств товаров, включая подгруппы дополнительных свойств
	 *
	 * @param int $shop_properties_items_dir_parent_id идентификатор группы дополнительных свойств
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_properties_items_dir_parent_id идентификатор скопированной группы дополнительных свойств (параметр используется при рекурсии - передавать не нужно)
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_items_dir_parent_id = 3;
	 * $shop_shops_id = 1;
	 * $new_shop_properties_items_dir_parent_id = 0;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyPropertiesForItem($shop_properties_items_dir_parent_id, $shop_shops_id, $new_shop_properties_items_dir_parent_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyPropertiesForItem($shop_properties_items_dir_parent_id, $shop_shops_id = 0, $new_shop_properties_items_dir_parent_id = 0, $new_shop_shops_id = 0)
	{
		$shop_properties_items_dir_parent_id = intval($shop_properties_items_dir_parent_id);
		$shop_shops_id = intval($shop_shops_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $shop_shops_id);

		$oProperty_Dirs = $oShop_Item_Property_List->Property_Dirs;
		$oProperty_Dirs->queryBuilder()->where('parent_id', '=', $shop_properties_items_dir_parent_id);
		$aProperty_Dirs = $oProperty_Dirs->findAll();

		// Меняем linked object, если магазин сменился
		if ($new_shop_shops_id)
		{
			$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $new_shop_shops_id);
		}

		foreach($aProperty_Dirs as $oProperty_Dir)
		{
			$oNewProperty_Dir = $oProperty_Dir->copy();
			$oShop_Item_Property_List->add($oNewProperty_Dir);
		}
	}

	/**
	 * Копирование дополнительных свойств групп товаров, включая группы дополнительных свойств групп
	 *
	 * @param int $shop_properties_groups_dir_parent_id идентификатор группы дополнительных свойств групп товаров
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_properties_groups_dir_parent_id идентификатор скопированной группы дополнительных свойств групп товаров (параметр используется при рекурсии - передавать не нужно)
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_properties_groups_dir_parent_id = 1;
	 * $shop_shops_id = 1;
	 * $new_shop_properties_groups_dir_parent_id = 0;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyPropertiesForGroup($shop_properties_groups_dir_parent_id, $shop_shops_id, $new_shop_properties_groups_dir_parent_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyPropertiesForGroup($shop_properties_groups_dir_parent_id, $shop_shops_id = 0, $new_shop_properties_groups_dir_parent_id = 0, $new_shop_shops_id = 0)
	{
		$shop_properties_groups_dir_parent_id = intval($shop_properties_groups_dir_parent_id);
		$shop_shops_id = intval($shop_shops_id);
		//$new_shop_properties_groups_dir_parent_id = intval($new_shop_properties_groups_dir_parent_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$oShop_Group_Property_List = Core_Entity::factory('Shop_Item_Property_List', $shop_shops_id);

		$oProperty_Dir = $oShop_Group_Property_List->Property_Dirs;
		$oProperty_Dir->queryBuilder()->where('parent_id', '=', $shop_properties_groups_dir_parent_id);
		$aProperty_Dirs = $oProperty_Dir->findAll();

		// Меняем linked object, если ИС сменилась
		if ($new_shop_shops_id)
		{
			$oShop_Group_Property_List = Core_Entity::factory('Shop_Group_Property_List', $new_shop_shops_id);
		}

		foreach($aProperty_Dirs as $oProperty_Dir)
		{
			$oNewProperty_Dir = $oProperty_Dir->copy();
			$oShop_Group_Property_List->add($oNewProperty_Dir);
		}
	}

	/**
	 * Копирование типа доставки
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyTypesOfDelivery($shop_shops_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyTypesOfDelivery($shop_shops_id, $new_shop_shops_id)
	{
		$aShop_Deliveries = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Deliveries->findAll();
		$oNewShop = Core_Entity::factory('Shop', $new_shop_shops_id);

		foreach ($aShop_Deliveries as $oShop_Delivery)
		{
			$oNewShop->add($oShop_Delivery->copy());
		}

		return TRUE;
	}

	/**
	 * Копирование условий типа доставки
	 *
	 * @param int $shop_type_of_delivery_id идентификатор типа доставки
	 * @param int $new_shop_type_of_delivery_id идентификатор скопированного типа доставки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_type_of_delivery_id = 2;
	 * $new_shop_type_of_delivery_id = 12;
	 *
	 * $shop->CopyCondOfDelivery($shop_type_of_delivery_id, $new_shop_type_of_delivery_id);
	 * ?>
	 * </code>
	 */
	function CopyCondOfDelivery($shop_type_of_delivery_id, $new_shop_type_of_delivery_id)
	{
		$shop_type_of_delivery_id = intval($shop_type_of_delivery_id);
		$new_shop_type_of_delivery_id = intval($new_shop_type_of_delivery_id);

		// Получаем все условия доставки данного типа доставки
		$aShop_Delivery_Conditions = Core_Entity::factory('Shop_Delivery', $shop_type_of_delivery_id)->Shop_Delivery_Conditions->findAll();

		$oNew_Shop_Delivery = Core_Entity::factory('Shop_Delivery', $new_shop_type_of_delivery_id);

		foreach($aShop_Delivery_Conditions as $oShop_Delivery_Condition)
		{
			$oNew_Shop_Delivery->add($oShop_Delivery_Condition->copy());
		}
	}

	/**
	 * Копирование цен для магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyPrices($shop_shops_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyPrices($shop_shops_id, $new_shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$oNew_Shop = Core_Entity::factory('Shop', $new_shop_shops_id);

		$aShop_Prices = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Prices->findAll();
		foreach($aShop_Prices as $oShop_Price)
		{
			$oNew_Shop->add($oShop_Price->copy());
		}
	}

	/**
	 * Копирование производителей
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyProducers($shop_shops_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyProducers($shop_shops_id, $new_shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$aShop_Producers = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Producers->findAll();
		$oNew_Shop = Core_Entity::factory('Shop', $new_shop_shops_id);

		foreach($aShop_Producers as $oShop_Producer)
		{
			$oNew_Shop->add($oShop_Producer->copy());
		}

		return TRUE;
	}

	/**
	 * Копирование продавцов
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_shops_id идентификатор магазина, в который помещаются скопированные продавцы
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopySallers($shop_shops_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopySallers($shop_shops_id, $new_shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$aSellers = Core_Entity::factory('Shop', $shop_shops_id)->Sellers->findAll();
		$oNew_Shop = Core_Entity::factory('Shop', $new_shop_shops_id);

		foreach($aSellers as $oSeller)
		{
			$oNew_Shop->add($oSeller->copy());
		}

		return TRUE;
	}

	/**
	 * Копирование скидок на товары
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyDiscounts($shop_shops_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyDiscounts($shop_shops_id, $new_shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$oNew_Shop = Core_Entity::factory('Shop', $new_shop_shops_id);

		$aShop_Discounts = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Discounts->findAll();
		foreach($aShop_Discounts as $oShop_Discount)
		{
			$oNew_Shop->add($oShop_Discount->copy());
		}
	}

	/**
	 * Копирование скидок от суммы заказа и их купонов
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyOrderDiscounts($shop_shops_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyOrderDiscounts($shop_shops_id, $new_shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$oNew_Shop = Core_Entity::factory('Shop', $new_shop_shops_id);

		// Получаем список всех скидок от суммы заказа магазина
		$aShop_Purchase_Discounts = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Purchase_Discounts->findAll();

		foreach($aShop_Purchase_Discounts as $oShop_Purchase_Discount)
		{
			$oNew_Shop->add($oShop_Purchase_Discount->copy());
		}
	}

	/**
	 * Копирование купонов магазина на скидку
	 *
	 * @param int $shop_order_discount_id идентификатор скидки
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * @param int $new_shop_order_discount_id идентификатор скопированной скидки
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_discount_id = 1;
	 * $new_shop_order_discount_id = 3;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyCoupons($shop_order_discount_id, $new_shop_order_discount_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyCoupons($shop_order_discount_id, $new_shop_order_discount_id, $new_shop_shops_id)
	{
		$shop_order_discount_id = intval($shop_order_discount_id);
		$new_shop_order_discount_id = intval($new_shop_order_discount_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$oNew_Shop_Purchase_Discount = Core_Entity::factory('Shop_Purchase_Discount', $new_shop_order_discount_id);

		// Получаем список всех купонов скидки
		$aShop_Purchase_Discount_Coupons = Core_Entity::factory('Shop_Purchase_Discount', $shop_order_discount_id)->Shop_Purchase_Discount_Coupons->findAll();

		foreach($aShop_Purchase_Discount_Coupons as $oShop_Purchase_Discount_Coupon)
		{
			$oNew_Shop_Purchase_Discount->add($oShop_Purchase_Discount_Coupon->copy());
		}
	}

	/**
	 * Копирование парнерских программ
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $new_shop_shops_id идентификатор скопированного магазина
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_shops_id = 1;
	 * $new_shop_shops_id = 3;
	 *
	 * $shop->CopyAffiliats($shop_shops_id, $new_shop_shops_id);
	 * ?>
	 * </code>
	 */
	function CopyAffiliats($shop_shops_id, $new_shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$new_shop_shops_id = intval($new_shop_shops_id);

		$oNew_Shop = Core_Entity::factory('Shop', $new_shop_shops_id);

		$aAffiliate_Plans = Core_Entity::factory('Shop', $shop_shops_id)->Affiliate_Plans->findAll();
		foreach($aAffiliate_Plans as $oAffiliate_Plan)
		{
			$oNew_Shop->add($oAffiliate_Plan);
		}
	}

	/**
	 * Получение специальной цены (в зависимости от количества этого товара в корзине),
	 * подходящей для товара.
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @param int $item_count количество купленного товара
	 * @return mixed Цена для товара, либо False
	 * @see GetSpecialPriceForItem()
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 1;
	 * $item_count = 10;
	 *
	 * $price = $shop->GetSpecialPriceValueForItem($shop_items_catalog_item_id, $item_count);
	 *
	 * if ($price)
	 * {
	 * 	echo $price;
	 * }
	 * else
	 * {
	 * 	echo "Специальной цены нет";
	 * }
	 * ?>
	 * </code>
	 */
	function GetSpecialPriceValueForItem($shop_items_catalog_item_id, $item_count)
	{
		$price_row = $this->GetSpecialPriceForItem($shop_items_catalog_item_id, $item_count);

		return $price_row
			? $price_row['shop_special_prices_price']
			: FALSE;
	}

	/**
	 * Получение информации о специальной цене (в зависимости от количества этого товара в корзине),
	 * подходящей для товара.
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * @param int $item_count количество купленного товара
	 * @return mixed массив либо False
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_items_catalog_item_id = 1;
	 * $item_count = 10;
	 *
	 * $price_row = $shop->GetSpecialPriceForItem($shop_items_catalog_item_id, $item_count);
	 *
	 * if ($price_row)
	 * {
	 * 	print_r($price_row);
	 * }
	 * else
	 * {
	 * 	echo "Специальной цены нет";
	 * }
	 * ?>
	 * </code>
	 */
	function GetSpecialPriceForItem($shop_items_catalog_item_id, $item_count)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
		$item_count = floatval($item_count);

		if (isset($this->CacheGetSpecialPriceForItem[$shop_items_catalog_item_id . '_' . $item_count]))
		{
			return $this->CacheGetSpecialPriceForItem[$shop_items_catalog_item_id . '_' . $item_count];
		}

		$oShop_Specialprice = Core_Entity::factory('Shop_Item', $shop_items_catalog_item_id)->Shop_Specialprices;

		$oShop_Specialprice
			->queryBuilder()
			->where('min_quantity', '<=', $item_count)
			->where('max_quantity', '>=', $item_count);

		$aShop_Specialprices = $oShop_Specialprice->findAll();

		return $this->CacheGetSpecialPriceForItem[$shop_items_catalog_item_id . '_' . $item_count] = isset($aShop_Specialprices[0])
			? $this->getArrayShopSpecialprice($aShop_Specialprices[0])
			: FALSE;
	}

	/**
	 * Изменение статуса оплаты заказа
	 *
	 * @param int $shop_order_id Идентификатор заказа
	 * @return bool
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_order_id = 38;
	 *
	 * $newid = $shop->SetOrderPaymentStatus($shop_order_id);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 */
	function SetOrderPaymentStatus($shop_order_id)
	{
		$shop_order_id = intval($shop_order_id);

		if ($order_row = $this->GetOrder($shop_order_id))
		{
			$shop_shops_id = Core_Type_Conversion::toInt($order_row['shop_shops_id']);

			// Получаем информацию о магазине
			$shop_row = $this->GetShop($shop_shops_id);

			// Товар был оплачен - делаем его неоплаченным
			if ($order_row['shop_order_status_of_pay'])
			{
				$date_of_pay = "0000-00-00 00:00:00";

				// Вернуть списанные товары
				$reduse_rest = FALSE;
			}
			// Товар не был оплачен - делаем его оплаченным
			else
			{
				$date_of_pay = date("Y-m-d H:i:s");

				// Списать товары
				$reduse_rest = TRUE;
			}

			// Получаем список товаров заказа
			$order_items_res = $this->GetOrderItems($shop_order_id);

			if ($order_items_res)
			{
				while($order_item_row = mysql_fetch_assoc($order_items_res))
				{
					// Информация о товаре получена и это электронный товар
					if (($item_row = $this->GetItem($order_item_row['shop_items_catalog_item_id'])) && $item_row['shop_items_catalog_type'] == 1 && $reduse_rest)
					{
						// Генерируем случайную ссылку
						$shop_order_items_eitem_resource = md5(mt_rand(0, 9999999) . serialize($order_item_row));

						// Если товар электронный
						if ($item_row['shop_items_catalog_type'] == 1)
						{
							// Получаем все файлы электронного товара
							$eitems_for_item = $this->GetEitemsForItem($item_row['shop_items_catalog_item_id']);

							if (mysql_num_rows($eitems_for_item))
							{
								// Указываем, какой именно электронный товар добавляем в заказ
								$eitems_for_item_row = mysql_fetch_assoc($eitems_for_item);
								$shop_eitem_id = $eitems_for_item_row['shop_eitem_id'];
							}
						}

						$oShop_Order_Item = Core_Entity::factory('Shop_Order_Item', $order_item_row['shop_order_items_id']);

						// Обновляем Информацию о заказанном товаре
						$oShop_Order_Item->hash = $shop_order_items_eitem_resource;
						$oShop_Order_Item->shop_item_digital_id = $shop_eitem_id;
						$oShop_Order_Item->save();
					}

					// Если пополнение лицевого счета
					if(Core_Type_Conversion::toInt($order_item_row['shop_order_items_type']) == 2)
					{
						$shop_currency_id = $shop_row['shop_currency_id'];
						$site_users_id = $order_row['site_users_id'];

						$order_item_price = $reduse_rest
							? $order_item_row['shop_order_items_price']
							: Core_Type_Conversion::toFloat($order_item_row['shop_order_items_price']) * -1.00;

						// Проводим транзакцию
						$this->InsertSiteUserAccountTransaction(array(
						'shop_shops_id' => $shop_shops_id,
						'shop_site_users_account_sum' => $order_item_price,
						'shop_currency_id' => $shop_currency_id,
						'shop_site_users_account_sum_in_base_currency' => $order_item_price,
						'shop_site_users_account_description' => $order_item_row['shop_order_items_name'],
						'site_users_id' => $site_users_id
						));
					}
				}
			}

			// Изменять остаток оплаченных товаров
			$shop_row && $shop_row['shop_shops_writeoff_payed_items'] == 1 && $this->ChangeItemsOfOrderRest($shop_order_id, $reduse_rest);

			$oShop_Order = Core_Entity::factory('Shop_Order', $shop_order_id);
			$oShop_Order->paid(1 - $oShop_Order->paid)->payment_datetime($date_of_pay)->save();

			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Получение информации обо всех группах магазина, имеющих название $shop_groups_name
	 *
	 * @param int $shop_shops_id Идентификатор магазина
	 * @param str $shop_groups_name Название группы
	 * @param int $shop_groups_parent_id Идентификатор родительской группы (не обязательный параметр)
	 * @return resource
	 */
	function SelectGroupsByName($shop_shops_id, $shop_groups_name, $shop_groups_parent_id = FALSE)
	{
		$shop_shops_id = intval($shop_shops_id);

		$queryBuilder = Core_QueryBuilder::select()
			->from('shop_groups')
			->where('shop_id', '=', $shop_shops_id)
			->where('name', '=', $shop_groups_name)
			->where('deleted', '=', 0);

		if ($shop_groups_parent_id !== FALSE)
		{
			$queryBuilder->where('parent_id', '=', intval($shop_groups_parent_id));
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение информации обо всех группах, по значению доп. свойства
	 *
	 * @param int $shop_properties_group_id идентификатор доп. свойства группы
	 * @param str $value значение доп. свойства группы
	 * @return resource
	 */
	function GetGroupsByPropertyValue($shop_properties_group_id, $value)
	{
		$shop_properties_group_id = intval($shop_properties_group_id);

		$oProperty = Core_Entity::factory('Property', $shop_properties_group_id);

		$aPropertyValueTable = $this->getPropertyValueTableName($oProperty->type);

		$tableName = $aPropertyValueTable['tableName'];
		$fieldName = $aPropertyValueTable['fieldName'];

		$queryBuilder = Core_QueryBuilder::select(
				array('shop_groups.id', 'shop_groups_id'),
				array('shop_groups.shop_id', 'shop_shops_id'),
				array('shop_groups.parent_id', 'shop_groups_parent_id'),
				array('shop_groups.name', 'shop_groups_name'),
				array('shop_groups.description', 'shop_groups_description'),
				array('shop_groups.image_large', 'shop_groups_image'),
				array('shop_groups.image_small', 'shop_groups_small_image'),
				array('shop_groups.sorting', 'shop_groups_order'),
				array('shop_groups.indexing', 'shop_groups_indexation'),
				array('shop_groups.active', 'shop_groups_activity'),
				array('shop_groups.siteuser_group_id', 'shop_groups_access'),
				array('shop_groups.path', 'shop_groups_path'),
				array('shop_groups.seo_title', 'shop_groups_seo_title'),
				array('shop_groups.seo_description', 'shop_groups_seo_description'),
				array('shop_groups.seo_keywords', 'shop_groups_seo_keywords'),
				array('shop_groups.user_id', 'users_id'),
				array('shop_groups.image_large_width', 'shop_groups_big_image_width'),
				array('shop_groups.image_large_height', 'shop_groups_big_image_height'),
				array('shop_groups.image_small_width', 'shop_groups_small_image_width'),
				array('shop_groups.image_small_height', 'shop_groups_small_image_height'),
				array('shop_groups.guid', 'shop_groups_cml_id')
			)
			->from('shop_groups')
			->join('shop_group_properties', 'shop_groups.shop_id', '=', 'shop_group_properties.shop_id')
			->join('properties', 'properties.id', '=', 'shop_group_properties.property_id')
			->leftJoin($tableName, 'shop_groups.id', '=', $tableName . '.entity_id',
				array(
					array('AND' => array('shop_group_properties.property_id', '=', Core_QueryBuilder::expression($tableName . '.property_id'))),
				)
			)
			->where('shop_group_properties.property_id', '=', $shop_properties_group_id)
			//->where('properties.id', '=', $shop_properties_group_id)
			->where($tableName . '.' . $fieldName, '=', $value)
			->where('shop_groups.deleted', '=', 0)
			->where('properties.deleted', '=', 0)
			->having(Core_Querybuilder::expression('COUNT(DISTINCT `shop_group_properties`.`property_id`)'), '=', 1)
			->groupBy('shop_groups.id');

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Изменение остатка товаров в заказе
	 *
	 * @param int $shop_order_id Идентификатор заказа
	 * @param bool $reduce_rest Флаг, указывающий, списать товары со склада (true), или вернуть (false - по умолчанию)
	 * @return bool
	 */
	function ChangeItemsOfOrderRest($shop_order_id, $reduce_rest = FALSE)
	{
		$shop_order_id = intval($shop_order_id);

		// Получаем информацию о товарах в заказе
		$order_items_res = $this->GetOrderItems($shop_order_id);

		$warehouse = & singleton('warehouse');

		// Хотя бы один товар
		if (mysql_num_rows($order_items_res))
		{
			while ($order_item_row = mysql_fetch_assoc($order_items_res))
			{
				// Получаем информацию о товаре
				$item_row = $this->GetItem($order_item_row['shop_items_catalog_item_id']);

				if ($item_row)
				{
					// Списать товары
					if ($reduce_rest)
					{
						$rest_change = -1 * floatval($order_item_row['shop_order_items_quantity']);
					}
					// Вернуть товары
					else
					{
						$rest_change = floatval($order_item_row['shop_order_items_quantity']);
					}

					$shop_warehouse_id = Core_Type_Conversion::toInt($order_item_row['shop_warehouse_id']);

					if(!$shop_warehouse_id)
					{
						// Склад не указан в товаре заказа, пытаемся определить склад по умолчанию для магазина
						if(($answer = $warehouse->GetDefaultWarehouse($item_row['shop_shops_id'])) !== FALSE)
						{
							// Склад по умолчанию обнаружен
							$shop_warehouse_id = Core_Type_Conversion::toInt($answer['shop_warehouse_id']);
						}
						else
						{
							// Склад по умолчанию не обнаружен, проводить операцию списания/возврата товара не на чем, выходим
							return FALSE;
						}
					}

					$warehouse->AcceptTransaction($shop_warehouse_id, $order_item_row['shop_items_catalog_item_id'], $rest_change);
				}
			}
		}

		return TRUE;
	}

	/**
	 * Возвращает XML цены для группы пользователей.
	 *
	 * @param int $shop_list_of_prices_id идентификатор цены для группы пользователей
	 * @param array $list_of_price_row информация о продавце, если false - получается автоматически
	 *
	 * @return str XML текст с информацией о цене для группы пользователей.
	 */
	function GenXML4PriceForShop($shop_list_of_prices_id, $list_of_price_row = FALSE)
	{
		$shop_list_of_prices_id = intval($shop_list_of_prices_id);

		// Если не передан массив с данными о цене для группы пользователей
		if (!$list_of_price_row)
		{
			$list_of_price_row = $this->GetPrice($shop_list_of_prices_id);
		}

		if ($list_of_price_row)
		{
			$xmlData = '<shop_list_of_price id="' . Core_Type_Conversion::toInt($list_of_price_row['shop_list_of_prices_id']) . '" name="' . str_for_xml($list_of_price_row['shop_list_of_prices_name']) . '">' . "\n";

			// Если есть модуль "пользователи сайта" - добавляем информацию о группе
			if (class_exists('SiteUsers'))
			{
				$SiteUsers = & singleton('SiteUsers');

				// Получаем информацию о группе пользователей сайта
				$site_users_group_row = $SiteUsers->GetSiteUsersGroup(Core_Type_Conversion::toInt($list_of_price_row['site_users_group_id']));

				if ($site_users_group_row)
				{
					$xmlData .= '<site_users_group id="' . Core_Type_Conversion::toInt($site_users_group_row['site_users_group_id']) . '">' . str_for_xml($site_users_group_row['site_users_group_name']) . '</site_users_group>' . "\n";
				}
			}

			$xmlData .= '<percent>' . Core_Type_Conversion::toFloat($list_of_price_row['shop_list_of_prices_percent_to_basic']) . '</percent>' . "\n";
			$xmlData .= '</shop_list_of_price>' . "\n";

			return $xmlData;
		}

		return '';
	}

	/**
	 * Получение информации об электронном товаре в заказе по его пути
	 * @param str $path путь к электронному товару
	 * @return mixed Массив с информацией о товаре в заказе, или false
	 * @access private
	 */
	function GetOrderItemByPath($path)
	{
		$Shop_Order_Item_Digital = Core_Entity::factory('Shop_Order_Item_Digital')->getByGuid($path);

		return !is_null($Shop_Order_Item_Digital)
			? array(
				'shop_order_items_eitem_resource' => $Shop_Order_Item_Digital->guid
			) + $this->getArrayShopOrderItem($Shop_Order_Item_Digital->Shop_Order_Item)
			: FALSE;
	}

	/**
	 * Формирование XML для налогов
	 *
	 * @return str XML для налогов
	 */
	function GenXml4Taxes()
	{
		$xmlData = '';

		// Получаем список всех налогов
		$taxes_res = $this->GetAllTax();

		if ($taxes_res)
		{
			$xmlData .= '<taxes>' . "\n";

			while($tax_row = mysql_fetch_assoc($taxes_res))
			{
				$xmlData .= '<tax id="' . Core_Type_Conversion::toInt($tax_row['shop_tax_id']) . '">' . "\n";
				$xmlData .= '<tax_name>' . str_for_xml($tax_row['shop_tax_name']) . '</tax_name>' . "\n";
				$xmlData .= '<tax_rate>' . str_for_xml($tax_row['shop_tax_rate']) . '</tax_rate>' . "\n";
				$xmlData .= '<tax_is_in_price>' . str_for_xml($tax_row['shop_tax_is_in_price']) . '</tax_is_in_price>' . "\n";
				$xmlData .= '</tax>' . "\n";
			}

			$xmlData .= '</taxes>' . "\n";
		}

		return $xmlData;
	}

	/**
	 * Определение уровня доступности группы товаров
	 *
	 * @param int $shop_group_id идентификатор группы товаров
	 * @param int $shop_id идентификатор интернет-магазина
	 * @param array $row_group ассоциативный массив с информацией о группе, по умолчанию пустой

	 * @return int уровень (группа) доступа пользователя к группе товаров, 0 - доступ разрешен всем
	 */
	function GetShopGroupAccess($shop_group_id, $shop_id = 0, $row_group = array())
	{
		$shop_group_id = intval($shop_group_id);
		$shop_id = intval($shop_id);

		// Если есть уже определенные права доступа - возвращаем их
		if ($shop_group_id != 0 && isset($this->ShopGroupAccess[$shop_group_id]))
		{
			return $this->ShopGroupAccess[$shop_group_id];
		}

		// Некорневая группа
		if ($shop_group_id)
		{
			// Получаем данные о группе товаров
			if (count($row_group) == 0)
			{
				$row_group = $this->GetGroup($shop_group_id);
			}

			switch ($row_group['shop_groups_access'])
			{
				case -1: // тип доступа к группе товаров - как у родителя
					// родительская группа не является корневой
					if ($row_group['shop_groups_parent_id'] != 0)
					{
						$result = $this->GetShopGroupAccess($row_group['shop_groups_parent_id'], $shop_id /* , $row_group нельзя, т.к. это даные родителя, а не элемента. будет зациливание*/);
					}
					else // родительская группа - корневая
					{
						$row_shop = $this->GetShop($shop_id);
						$result = $row_shop['shop_shops_access'];
					}
				break;
				case 0: // доступ разрешен всем
					$result = 0;
				break;
				default:
					$result = $row_group['shop_groups_access'];
				break;
			}

			// Запишим в кэш (именно здесь, т.к. ниже идет корень и там участвует ID ИС)
			$this->ShopGroupAccess[$shop_group_id] = $result;
		}
		else // Корневая группа
		{
			// Получаем данные доступа из интернет-магазина
			$row_shop = $this->GetShop($shop_id);
			$result = $row_shop['shop_shops_access'];
		}

		return $result;
	}

	/**
	 * Проверка возможности доступа пользователя к группе товаров
	 * @param array $param ассоциативный массив с параметров
	 * - $param['site_users_id'] идентификатор пользователя
	 * - $param['shop_group_id'] идентификатор группы товаров
	 * - $param['shop_id'] идентификатор магазина, не обязательный параметр, по умолчанию 0
	 * - $param['shop_group_info'] ассоциативный массив с информацией о группе, по умолчанию пустой
	 * - $param['cache'] использовать кэширование, по умолчанию true

	 * @return bool
	 */
	function IssetAccessForShopGroup($param)
	{
		$site_users_id = Core_Type_Conversion::toInt($param['site_users_id']);
		$shop_group_id = Core_Type_Conversion::toInt($param['shop_group_id']);
		$shop_id = Core_Type_Conversion::toInt($param['shop_id']);

		$row_group = Core_Type_Conversion::toArray($param['shop_group_info']);

		// определяем группу доступа
		$group_access = $this->GetShopGroupAccess($shop_group_id, $shop_id, $row_group);

		// Файловое кэширование в данном блоке дает сильную нагрузку на файловую систему

		// Кэширование в памяти
		// Если есть уже определенные права доступа пользователя к данной группе доступа
		if ($shop_group_id != 0 && isset($this->CacheIssetAccessForShopGroup[$group_access][$site_users_id]))
		{
			return $this->CacheIssetAccessForShopGroup[$group_access][$site_users_id];
		}

		// определяем относится ли данный пользователь к группе пользователей, указанных в списке доступа для инфогруппы
		if ($site_users_id > 0 && class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			// получаем список групп доступа, в которые входит данный пользователь
			$mas_group_access = $SiteUsers->GetGroupsForUser($site_users_id);
		}
		else
		{
			$mas_group_access = array(0);
		}

		$result = in_array($group_access, $mas_group_access) || $group_access == 0;

		// Запишем в кэш памяти, если только группа не 0 (там участвует ID ИС)
		if ($shop_group_id)
		{
			$this->CacheIssetAccessForShopGroup[$group_access][$site_users_id] = $result;
		}

		return $result;
	}

	/**
	 * Определение уровня доступности товара
	 *
	 * @param int shop_item_id идентификатор товара
	 *
	 * @return int уровень (группа) доступа пользователя к товару, 0 - доступ разрешен всем
	 */
	function GetShopItemAccess($shop_item_id)
	{
		$shop_item_id = intval($shop_item_id);

		// Получаем информацию о товаре
		$row_item = $this->GetItem($shop_item_id);

		// Тип доступа - не "Как у родителя"
		return $row_item['shop_items_catalog_access'] != -1
			? $row_item['shop_items_catalog_access']
			// Тип доступа - "Как у родителя"
			// Получаем и возвращаем группу доступа для информационной группы, в которой находится элемент
			: $this->GetShopGroupAccess($row_item['shop_items_catalog_access'], $row_item['shop_shops_id']);
	}

	/**
	 * Определение доступности товара
	 *
	 * @param int $site_user_id идентификатор пользователя
	 * @param int $shop_item_id идентификатор инфоэлемента
	 * @param int $parent параметр, определяющий наследует ли информационный элемент тип доступа от родителя (1 - наследует, 0 - не наследует)
	 * @param array $row_item ассоциативный массив свойств информационного элемента
	 *
	 * @return boolean true - товар доступен пользователю, false - не доступен
	 */
	function GetAccessShopItem($site_user_id, $shop_item_id, $row_item = array())
	{
		$site_user_id = intval($site_user_id);
		$shop_item_id = intval($shop_item_id);
		$row_item = Core_Type_Conversion::toArray($row_item);

		// массив не содержит информации о товаре
		if (count($row_item) == 0)
		{
			$row_item = $this->GetItem($shop_item_id);
		}

		// Определяем относится ли данный пользователь к
		// группе пользователей, указанных в списке доступа для интернет-магазина
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			// получаем список групп доступа, в которые входит данный пользователь
			$mas_group_access = $SiteUsers->GetGroupsForUser($site_user_id);
		}
		else
		{
			$mas_group_access = array(0);
		}

		// Элемент не наследует тип доступа от родительской группы
		if ($row_item['shop_items_catalog_access'] != -1)
		{
			return in_array($row_item['shop_items_catalog_access'], $mas_group_access);
		}

		// Если товар находится в корне инфосистемы (не принадлежит ни одной группе)
		if ($row_item['shop_groups_id'] == 0)
		{
			$row_shop = $this->GetShop($row_item['shop_shops_id']);

			switch ($row_shop['shop_shops_access'])
			{
				// магазин доступен всем -  элемент доступен
				case 0:
					return TRUE;
					// тип доступа  как у родителя - элемент доступен
				case -1:
					return TRUE;
				// Типы доступа для различных групп доступа
				default:
					// Если среди групп, в которые входит данный пользователь,
					// есть группа доступа, указанная для данного магазина,
					// то пользователю разрешен доступ к товару данного магазина
					return in_array($row_shop['shop_shops_access'], $mas_group_access);
			}
		}
		else // Товар находится в группе товаров
		{
			// Получаем данные о группе товаров
			$row_group = $this->GetGroup($row_item['shop_groups_id']);

			//echo $row_group['shop_groups_access'];
			switch ($row_group['shop_groups_access'])
			{
				case 0: // Доступ к группе разрешен всем
					return TRUE;
				case -1: // Доступ к группе как у родительской группы
					$param_access = array();
					$param_access['site_users_id'] = $site_user_id;
					$param_access['shop_group_id'] = $row_group['shop_groups_id'];
					$param_access['shop_id'] = $row_group['shop_shops_id'];
					$param_access['shop_group_info'] = $row_group;

					return $this->IssetAccessForShopGroup($param_access);
				// типы доступа для различных групп доступа
				default:
					// Если среди групп, в которые входит данный пользователь,
					// есть группа доступа, указанная для данной группы товаров,
					// то пользователю разрешен доступ к элементу данной группы товаров
					return in_array($row_group['shop_groups_access'], $mas_group_access);
			}
		}

		return FALSE;
	}

	/**
	 * Копирование дополнительного свойства товаров
	 *
	 * @param $items_property_id идентификатор дополнительного свойства товаров
	 * @param $shop_id идентификатор магазина, в который будет скопировано дополнительное свойство.
	 * <br />По умолчанию равен false - используется магазин, к которому принадлежит копируемое дополнительное свойство.
	 * @return mixed идентификатор копии дополнительного свойства товаров в случае успешного выполнения, false - в противном случае
	 */
	function CopyItemsProperty($items_property_id, $shop_id = FALSE)
	{
		$oProperty = Core_Entity::factory('Property')->find($items_property_id);

		if (!is_null($oProperty->id))
		{
			$oNew_Property = $oProperty->copy(FALSE);

			if ($shop_id !== FALSE)
			{
				$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $shop_id);
				$oShop_Item_Property_List->add($oNew_Property);
			}

			return $oNew_Property->id;
		}

		return FALSE;
	}

	/**
	 * Копирование дополнительного свойства товаров
	 *
	 * @param $groups_items_property_id идентификатор дополнительного свойства групп товаров
	 * @param $shop_id идентификатор магазина, в который будет скопировано дополнительное свойство.
	 * <br />По умолчанию равен false - используется магазин, к которому принадлежит копируемое дополнительное свойство.
	 * @return mixed идентификатор копии дополнительного свойства групп товаров в случае успешного выполнения, false - в противном случае
	 */
	function CopyGroupsItemsProperty($groups_items_property_id, $shop_id = FALSE)
	{
		$groups_items_property_id = intval($groups_items_property_id);
		$oProperty = Core_Entity::factory('Property')->find($groups_items_property_id);

		if (!is_null($oProperty->id))
		{
			$oNew_Property = $oProperty->copy(FALSE);

			if ($shop_id !== FALSE)
			{
				$oShop_Group_Property_List = Core_Entity::factory('Shop_Group_Property_List', $shop_id);
				$oShop_Group_Property_List->add($oNew_Property);
			}

			return $oNew_Property->id;
		}

		return FALSE;
	}

	/**
	 * Копирование платежных систем
	 *
	 * @param int $shop_shops_id идентификатор магазина из которого будет производиться копирование
	 * @param int $new_shop_shops_id идентификатор магазина куда будет производиться копирование
	 */
	function CopySystemsOfPay($shop_shops_id, $new_shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$new_shop_shops_id = intval($new_shop_shops_id);
		$oShop = Core_Entity::factory('Shop', $shop_shops_id);

		$aShop_Payment_Systems = $oShop->Shop_Payment_Systems->findAll();
		foreach($aShop_Payment_Systems as $oShop_Payment_System)
		{
			$oShop_Payment_System->copy();
		}
	}

	/**
	 * Вставки дерева групп для CML v. 2.0x
	 * @param $parant_group_id родительская группа
	 * @param $array массив с иерархической структурой групп
	 */
	function InsertTreeGroup($cur_parent_id, $shop_shops_id, & $metadata)
	{
		$count_dir = 0;

		$kernel = & singleton('kernel');
		//$DataBase = & singleton('DataBase');

		if (isset($metadata['children']))
		{
			// Строим дерево групп
			foreach ($metadata['children'] as $c_number => $c_metadata)
			{
				$tmp_cur_parent_id = $cur_parent_id;

				if ($c_metadata['name'] == 'Группа')
				{
					if (isset($c_metadata['children']))
					{
						$array_of_data = array();
						$group_cml_id = FALSE;
						$group_pictures_counter = 0;

						// Строим дерево групп
						foreach ($c_metadata['children'] as $c_number_group_field => $c_metadata_group_field)
						{
							switch ($c_metadata_group_field['name'])
							{
								case 'Ид':
									$group_cml_id = $c_metadata_group_field['value'];
								break;
								case 'Наименование':
									$array_of_data['Наименование'] = $c_metadata_group_field['value'];
								break;
								case 'Описание':
									$array_of_data['Описание'] = $c_metadata_group_field['value'];
								break;
								case 'Картинка':
									$array_of_data['pictures'][$group_pictures_counter++]['Картинка'] = $c_metadata_group_field['value'];
								break;
							}
						}

						$new_group_id = $this->GetGroupIdByCmlId($group_cml_id, $shop_shops_id);

						// Проверяем, есть ли группа в базе с текущим $group_cml_id
						if (!$new_group_id
						&& isset($array_of_data['Наименование']))
						{
							// Формируем массив параметров для вставки
							$param = array();
							$param['shop_groups_name'] = $array_of_data['Наименование'];
							$param['shop_shops_id'] = $shop_shops_id;

							if(Core_Type_Conversion::toStr($array_of_data['Описание']) != '')
							{
								$param['shop_groups_description'] = Core_Type_Conversion::toStr($array_of_data['Описание']);
							}

							$param['shop_groups_parent_id'] = $cur_parent_id;
							$param['shop_groups_cml_id'] = $group_cml_id;

							$tmp_cur_parent_id = $this->InsertGroup($param);

							$count_dir++;
						}
						elseif(isset($array_of_data['Наименование']))
						{
							$param = array();
							$param['shop_groups_name'] = $array_of_data['Наименование'];
							$param['shop_shops_id'] = $shop_shops_id;

							if(Core_Type_Conversion::toStr($array_of_data['Описание']) != '')
							{
								$param['shop_groups_description'] = Core_Type_Conversion::toStr($array_of_data['Описание']);
							}

							//$param['shop_groups_parent_id'] = $cur_parent_id;
							// 11-05-2010 При обновлении сохраняем ID родителя группы, инц. 000037196
							$param['shop_groups_parent_id'] = $new_group_id['shop_groups_parent_id'];
							$param['shop_groups_cml_id'] = $group_cml_id;
							$param['group_id'] = Core_Type_Conversion::toInt($new_group_id['shop_groups_id']);

							$tmp_cur_parent_id = $this->InsertGroup($param);
						}

						if(isset($array_of_data['pictures'])
						&& isset($array_of_data['pictures'][0]))
						{
							// Первая картинка - всегда основное свойство
							$big_image_group = Core_Type_Conversion::toStr($array_of_data['pictures'][0]['Картинка']);

							// Удаляем первую картинку
							unset($array_of_data['pictures'][0]);
							// Получаем данные о картинках из магазина

							$shop_row = $this->GetShop($shop_shops_id);

							// Картинка для группы $tmp_cur_parent_id передана, необходимо обработать
							if($big_image_group != '' && is_file(CMS_FOLDER . $big_image_group))
							{
								$big_image_source = CMS_FOLDER . $big_image_group;

								// Необходимо определить значение константы UPLOADDIR, если константа не определена, нужно искать нужное значение в текущем сайте
								if (!defined('UPLOADDIR'))
								{
									$site_row = $site->GetSite($shop_row['site_id']);
									$uploaddir = $site_row['site_uploaddir'];
								}
								else
								{
									$uploaddir = UPLOADDIR;
								}

								// Формируем путь к файлу водяного знака
								$watermark_file = CMS_FOLDER . $uploaddir . "shop_" . $shop_shops_id . "/watermarks/" . $shop_row['shop_watermark_file'];

								$admin_load_files_array = array();

								// Отдельно для метода PathMkdiк, ибо он не любит CMS_FOLDER
								$group_images_dir_for_mkdir_special = $this->GetGroupDir($tmp_cur_parent_id);

								$kernel->PathMkdir($group_images_dir_for_mkdir_special);

								// Формируем путь к каталогу изображений группы товаров
								$group_images_dir = CMS_FOLDER . $group_images_dir_for_mkdir_special;

								// Необходимо проверить, существуют ли старые картинки для данной группы товаров
								$group_row = $this->GetGroup($tmp_cur_parent_id);

								if($group_row['shop_groups_image'] != '')
								{
									// старая большая картинка задана, удаляем ее из файловой системы
									if(is_file($group_images_dir . $group_row['shop_groups_image']))
									{
										@unlink($group_images_dir . $group_row['shop_groups_image']);
									}
								}

								if($group_row['shop_groups_small_image'] != '')
								{
									// старая малая картинка задана, удаляем ее из файловой системы
									if(is_file($group_images_dir . $group_row['shop_groups_small_image']))
									{
										@unlink($group_images_dir . $group_row['shop_groups_small_image']);
									}
								}

								$admin_load_files_array['max_width_big_image'] = $shop_row['shop_image_big_max_width_group'];
								$admin_load_files_array['max_height_big_image'] = $shop_row['shop_image_big_max_height_group'];
								$admin_load_files_array['isset_big_image'] = 1;
								$admin_load_files_array['path_source_big_image'] = $big_image_source;
								$admin_load_files_array['original_file_name_big_image'] = basename($big_image_group);
								$admin_load_files_array['original_file_name_small_image'] = $admin_load_files_array['original_file_name_big_image'];
								$admin_load_files_array['use_big_image'] = 1;
								$admin_load_files_array['path_source_small_image'] = $admin_load_files_array['path_source_big_image'];

								if(Core_Type_Conversion::toInt($shop_row['shop_shops_file_name_conversion']) == 1)
								{
									// Стоит галочка "Изменять названия загружаемых файлов"
									$big_image_extension = mb_strtolower(Core_File::getExtension(basename($big_image_group)));
									$big_image_file_target = $group_images_dir . 'shop_group_image' . $tmp_cur_parent_id . '.' . $big_image_extension;
									$small_image_file_target = $group_images_dir . 'small_shop_group_image' . $tmp_cur_parent_id . '.' . $big_image_extension;
									$big_image_file_name = 'shop_group_image' . $tmp_cur_parent_id . '.' . $big_image_extension;
									$small_image_file_name = 'small_shop_group_image' . $tmp_cur_parent_id . '.' . $big_image_extension;
								}
								else
								{
									// Галочка "Изменять названия загружаемых файлов" не стоит
									$big_image_file_target = $group_images_dir . basename($big_image_group);
									$small_image_file_target = $group_images_dir . "small_" . basename($big_image_group);
									$big_image_file_name = basename($big_image_group);
									$small_image_file_name = "small_" . basename($big_image_group);
								}

								$admin_load_files_array['path_target_big_image'] = $big_image_file_target;

								$admin_load_files_array['path_target_small_image'] = $small_image_file_target;


								$admin_load_files_array['max_width_small_image'] = $shop_row['shop_image_small_max_width_group'];
								$admin_load_files_array['max_height_small_image'] = $shop_row['shop_image_small_max_height_group'];

								$admin_load_files_array['watermark_file_path'] = $watermark_file;

								$admin_load_files_array['used_watermark_big_image'] = $shop_row['shop_watermark_default_use_big'];
								$admin_load_files_array['used_watermark_small_image'] = $shop_row['shop_watermark_default_use_small'];

								$admin_load_files_array['watermark_position_x'] = $shop_row['shop_watermark_default_position_x'];
								$admin_load_files_array['watermark_position_y'] = $shop_row['shop_watermark_default_position_y'];

								$result = $kernel->AdminLoadFiles($admin_load_files_array);

								$oShop_Group = Core_Entity::factory('Shop_Group', $tmp_cur_parent_id);

								if($result['big_image'])
								{
									// Если операция завершилась успешно необходимо получить сведения о размерах изображения
									if(file_exists($big_image_file_target) && filesize($big_image_file_target))
									{
										$group_big_image_sizes = getimagesize($big_image_file_target);

										$group_big_image_width = Core_Type_Conversion::toInt($group_big_image_sizes[0]);
										$group_big_image_height = Core_Type_Conversion::toInt($group_big_image_sizes[1]);
									}
									else
									{
										$group_big_image_width = $shop_row['shop_image_big_max_width_group'];
										$group_big_image_height = $shop_row['shop_image_big_max_height_group'];
									}

									// Операции с файлами успешно завершены, необходимо обновить данные в базе
									$oShop_Group->image_large = $big_image_file_name;
									$oShop_Group->image_large_width = $group_big_image_width;
									$oShop_Group->image_large_height = $group_big_image_height;
									$oShop_Group->save();
								}

								if($result['small_image'])
								{
									// Если операция завершилась успешно необходимо получить сведения о размерах изображения
									if(file_exists($small_image_file_target) && filesize($small_image_file_target))
									{
										$group_small_image_sizes = getimagesize($small_image_file_target);
										$group_small_image_width = Core_Type_Conversion::toInt($group_small_image_sizes[0]);
										$group_small_image_height = Core_Type_Conversion::toInt($group_small_image_sizes[1]);
									}
									else
									{
										$group_small_image_width = $shop_row['shop_image_small_max_width_group'];
										$group_small_image_height = $shop_row['shop_image_small_max_height_group'];
									}

									$oShop_Group->image_small = $small_image_file_name;
									$oShop_Group->image_small_width = $group_small_image_width;
									$oShop_Group->image_small_height = $group_small_image_height;
									$oShop_Group->save();
								}
							}
							// Проверяем, есть ли еще картинки
							if(count($array_of_data['pictures']) > 0)
							{
								foreach($array_of_data['pictures'] as $picture_array)
								{
									if(isset($picture_array['Картинка']))
									{
										$group_property_picture = strval($picture_array['Картинка']);

										if($group_property_picture != '')
										{
											//echo "Обнаружена дополнительная картинка: $group_property_picture для группы tmp_cur_parent_id<br/>";

											$file_extension = Core_File::getExtension($group_property_picture);

											if($file_extension != '')
											{
												$file_extension = "." . $file_extension;
											}

											$file_name_without_extension = basename($group_property_picture, $file_extension);

											if(mb_strpos($file_name_without_extension, '_') !== false)
											{
												$property_cml_id = explode("_", $file_name_without_extension);

												$property_cml_id = quote_smart($property_cml_id[1]);
											}
											else
											{
												$property_cml_id = $file_name_without_extension;
											}

											$oProperty = Core_Entity::factory('Property')->getByGuid($property_cml_id);

											$group_dir = $this->GetGroupDir($tmp_cur_parent_id);

											$kernel->PathMkdir($group_dir);

											if (is_null($oProperty))
											{
												$oProperty = Core_Entity::factory('Property');

												$oProperty->property_dir_id = 0;
												$oProperty->name = $property_cml_id;
												$oProperty->type = 1;
												$oProperty->default_value;
												$oProperty->tag_name = $property_cml_id;
												$oProperty->image_large_max_width =  $shop_row['shop_image_big_max_width_group'];
												$oProperty->image_large_max_height = $shop_row['shop_image_big_max_height_group'];
												$oProperty->image_small_max_width = $shop_row['shop_image_small_max_width_group'];
												$oProperty->image_small_max_height = $shop_row['shop_image_small_max_height_group'];
												$oProperty->guid = $property_cml_id;
												$oProperty->save();

												$oShop_Group_Property_List = Core_Entity::factory('Shop_Group_Property_List', $shop_shops_id);

												$oShop_Group_Property_List->add($oProperty);
											}
											else
											{
												$group_property_row = mysql_fetch_assoc($result);

												$group_property_id = $group_property_row['shop_properties_group_id'];

												// Необходимо проверить, существуют ли старые изображения
												$property_value_row = $this->GetPropertiesGroupValue(array(
													'shop_properties_group_id' => $group_property_id,
													'shop_groups_id' => $tmp_cur_parent_id
												));

												if($property_value_row)
												{
													$big_image_old_path = CMS_FOLDER . $group_dir . $property_value_row['shop_properties_group_value_value'];

													is_file($big_image_old_path) && @unlink($big_image_old_path);

													$small_image_old_path = CMS_FOLDER . $group_dir . $property_value_row['shop_properties_group_value_value_small'];

													if(is_file($small_image_old_path))
													{
														@unlink($small_image_old_path);
													}
												}
											}

											// Необходимо добавить картинку дополнительному свойству $group_property_id для группы tmp_cur_parent_id

											$final_group_property_picture = CMS_FOLDER . $group_property_picture;

											if(is_file($final_group_property_picture))
											{
												$admin_load_files_array = array();

												// Получаем настройки картинок для групп из магазина
												$admin_load_files_array['max_width_big_image'] = $shop_row['shop_image_big_max_width_group'];
												$admin_load_files_array['max_height_big_image'] = $shop_row['shop_image_big_max_height_group'];
												$admin_load_files_array['max_height_big_image'] = $shop_row['shop_image_big_max_height_group'];

												// Большое изображение задано всегда
												$admin_load_files_array['isset_big_image'] = 1;

												// Источник большого изображения
												$admin_load_files_array['path_source_big_image'] = $final_group_property_picture;

												// Источник малого изображения равен источнику большого
												$admin_load_files_array['path_source_small_image'] = $admin_load_files_array['path_source_big_image'];

												// Оригинальное имя большого изображения
												$admin_load_files_array['original_file_name_big_image'] = basename($final_group_property_picture);
												$admin_load_files_array['original_file_name_small_image'] = $admin_load_files_array['original_file_name_big_image'];

												if(Core_Type_Conversion::toInt($shop_row['shop_shops_file_name_conversion']))
												{
													// Необходимо создавать свои названия для файлов
													$property_name = "shop_property_file_$tmp_cur_parent_id"."_" . "$group_property_id" . $file_extension;
												}
												else
												{
													// Название файлов не нужно изменять
													$property_name = $admin_load_files_array['original_file_name_big_image'];
												}

												$property_name_small = "small_" . $property_name;

												$property_name_full = CMS_FOLDER . $group_dir . $property_name;

												$property_name_full_small = CMS_FOLDER . $group_dir . $property_name_small;

												// Целевой файл большого изображения
												$admin_load_files_array['path_target_big_image'] = $property_name_full;
												$admin_load_files_array['path_target_small_image'] = $property_name_full_small;

												// Параметры водяного знака
												$admin_load_files_array['watermark_file_path'] = CMS_FOLDER . UPLOADDIR . "shop_" . $shop_shops_id . "/watermarks/" . $shop_row['shop_watermark_file'];
												$admin_load_files_array['used_watermark_big_image'] = $shop_row['shop_watermark_default_use_big'];
												$admin_load_files_array['used_watermark_small_image'] = $shop_row['shop_watermark_default_use_small'];
												$admin_load_files_array['watermark_position_x'] = $shop_row['shop_watermark_default_position_x'];
												$admin_load_files_array['watermark_position_y'] = $shop_row['shop_watermark_default_position_y'];

												$result_download_files = $kernel->AdminLoadFiles($admin_load_files_array);

												if($result_download_files['big_image'] && $result_download_files['small_image'])
												{
													// Файлы успешно загрузились, необходимо вставить информацию в БД
													$oProperty_Value_File = Core_Entity::factory('Property_Value_File');

													$oProperty_Value_File->file_name = $property_name;
													$oProperty_Value_File->file_small_name = $property_name_small;
													$oProperty_Value_File->file = $property_name;
													$oProperty_Value_File->file_small = $property_name_small;
													$oProperty_Value_File->entity_id = $tmp_cur_parent_id;
													$oProperty_Value_File->save();

													$oProperty = Core_Entity('Property', $group_property_id);
													$oProperty->add($oProperty_Value_File);
												}
											}
										}
									}
								}
							}
						}

						reset($c_metadata['children']);

						// Повторно проходимся для выбора подгрупп после вставки самой группы
						foreach ($c_metadata['children'] as $c_number_group_field => $c_metadata_group_field)
						{
							if ($c_metadata_group_field['name'] == 'Группы')
							{
								// Для вложенных групп вызываем рекурсивно
								$count_dir += $this->InsertTreeGroup($tmp_cur_parent_id, $shop_shops_id,  $c_metadata_group_field);
							}
						}
					}
				}

				if ($c_metadata['name'] == 'Группы')
				{
					// Для вложенных групп вызываем рекурсивно
					$count_dir += $this->InsertTreeGroup($tmp_cur_parent_id, $shop_shops_id,  $c_metadata);
				}
			}
		}

		return $count_dir;
	}

	/**
	 * Генерация идентификатора в формате CommerceML для группы товаров
	 * @param int $shop_groups_id идентификатор группы товаров
	 * @return string идентификатор группы товаров в формате CommerceML
	 */
	function GenCmlId($shop_groups_id)
	{
		if (!is_int($shop_groups_id))
		{
			return $shop_groups_id;
		}

		$temp = 'ID';

		$shop_groups_id = Core_Type_Conversion::toStr($shop_groups_id);

		$len = 8 - strlen($shop_groups_id);
		for ($i = 0; $i < $len; $i++)
		{
			$temp .= '0';
		}

		return $temp .= $shop_groups_id;
	}

	/**
	 *	Импорт из формата CommerceML
	 * @param int $array_of_cml_data массив с данными в формате CommerceML, полученный в результате работы метода Xml2Array класса kernel, выполненного на целевом XML файле, содержащим данные в формате CommerceML
	 * @param int $shop_shops_id идентификатор интернет-магазина
	 * @param int $import_price_action_items идентификатор действия с уже существующими товарами (0 - Удалить существующие товары (во всех группах), 1 - Обновить информацию для существующих товаров, 2 - Оставить без изменений)
	 * @param int $shop_groups_parent_id идентификатор группы в которую выгружать данные из CommerceML
	 * @param str $images_path путь к картинкам
	 * @return array ассоциативный массив с данными о количестве обработанных данных
	 * - array['count_insert_item'] - количество вставленных товаров
	 * - array['count_update_item'] - количество обновленных товаров
	 * - array['count_dir'] - количество вставленных групп товаров
	 */
	function ImportCML($array_of_cml_data, $shop_shops_id, $import_price_action_items, $shop_groups_parent_id, $images_path = '', $nl2br = TRUE)
	{
		$cml = & singleton('cml');
		return $cml->ImportCML($array_of_cml_data, $shop_shops_id, $import_price_action_items, $shop_groups_parent_id, $images_path = '', $nl2br);
	}

	/**
	 * Добавление/обновление раздела иентернет-магазинов
	 *
	 * @param array $param масcив параметров
	 * - $param['shop_dir_id'] идентификатор редактируемого раздела информационных систем
	 * - $param['shop_dir_parent_id'] идентификатор родительского раздела информационных систем
	 * - $param['shop_dir_name'] название раздела информационных систем
	 * - $param['shop_dir_description'] описание раздела информационных систем
	 * - $param['site_id'] идентификатор сайта
	 * - $param['users_id'] идентификатор пользователя центра администрирования, если false - берется текущий пользователь.
	 *
	 * @return mixed идентификатор добавленного/обновленного раздела информационных систем в случае успешного выполнения,  false - в противном случае
	 */
	function InsertShopsDir($param)
	{
		$oShopDir = Core_Entity::factory('Shop_Dir', isset($param['shop_dir_id']) ? intval($param['shop_dir_id']) : NULL);

		$oShopDir->parent_id = Core_Type_Conversion::toInt($param['shop_dir_parent_id']);
		$oShopDir->name = Core_Type_Conversion::toStr($param['shop_dir_name']);
		$oShopDir->description = Core_Type_Conversion::toStr($param['shop_dir_description']);
		$oShopDir->site_id = Core_Type_Conversion::toInt($param['site_id']);
		$oShopDir->user_id = Core_Type_Conversion::toInt($param['users_id']);

		if (is_null($oShopDir->id) && Core_Type_Conversion::toInt($param['users_id']))
		{
			$oShopDir->user_id = $param['users_id'];
		}

		$oShopDir->save();
		return $oShopDir->id;
	}

	/**
	 * Получение информации о разделах интернет-магазинов
	 *
	 * @param array $param массив параметров
	 * - $param['shop_dir_parent_id'] идентификатор родительского раздела магазинов. По умолчанию равен 0.
	 * - $param['site_id'] идентификатор сайта. По умолчанию имеет значение идентификатора текущего сайта.	*
	 */
	function GetAllShopsDirs($param = array())
	{
		$parent_id = Core_Type_Conversion::toInt($param['shop_dir_parent_id']);

		$queryBuilder = Core_QueryBuilder::select
		(
			array('id', 'shop_dir_id'),
			array('parent_id', 'shop_dir_parent_id'),
			array('name', 'shop_dir_name'),
			array('description', 'shop_dir_description'),
			array('site_id', 'site_id'),
			array('user_id', 'users_id')
		)
			->from('shop_dirs')
			->where('deleted', '=', '0')
			->where('parent_id', '=', $parent_id)
			->orderBy('id');

		if($parent_id)
		{
			if(!($site_id = Core_Type_Conversion::toInt($param['site_id'])))
			{
				$site_id = CURRENT_SITE;
			}
			$queryBuilder->where('site_id', '=', $site_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение информации о разделе интернет-магазинов
	 *
	 * @param int $shop_dir_id идентификатор раздела магазинов
	 *
	 * @return mixed ассоциативный массив с информацией о разделе магазинов в случае успешного выполнения, false - в противном случае
	 */
	function GetShopsDir($shop_dir_id)
	{
		$oShopDir = Core_Entity::factory('Shop_Dir')->find($shop_dir_id);

		return $oShopDir->id
			? $this->getArrayShopDir($oShopDir)
			: FALSE;
	}

	/**
	 * Получение информации о интернет-магазинах раздела
	 *
	 * @param int $shop_dir_id идентификатор раздела магазинов
	 *
	 * @return resource
	 */
	function GetAllShopsFromDir($shop_dir_id)
	{
		$shop_dir_id = intval($shop_dir_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_shops_id'),
				array('shop_dir_id', 'shop_dir_id'),
				array('shop_company_id', 'shop_company_id'),
				array('name', 'shop_shops_name'),
				array('description', 'shop_shops_description'),
				array('yandex_market_name', 'shop_shops_yandex_market_name'),
				array('image_small_max_width', 'shop_image_small_max_width'),
				array('image_large_max_width', 'shop_image_big_max_width'),
				array('image_small_max_height', 'shop_image_small_max_height'),
				array('image_large_max_height', 'shop_image_big_max_height'),
				'structure_id',
				'shop_country_id',
				'shop_currency_id',
				'shop_order_status_id',
				array('shop_measure_id', 'shop_mesures_id'),
				array('send_order_email_admin', 'shop_shops_send_order_mail_admin'),
				array('send_order_email_user', 'shop_shops_send_order_mail_user'),
				array('email', 'shop_shops_admin_mail'),
				array('items_sorting_field', 'shop_sort_order_field'),
				array('items_sorting_direction', 'shop_sort_order_type'),
				array('groups_sorting_field', 'shop_group_sort_order_field'),
				array('groups_sorting_direction', 'shop_group_sort_order_type'),
				array('user_id', 'users_id'),
				array('comment_active', 'shop_comment_active'),
				array('watermark_file', 'shop_watermark_file'),
				array('watermark_default_use_large_image', 'shop_watermark_default_use_big'),
				array('watermark_default_use_small_image', 'shop_watermark_default_use_small'),
				array('watermark_default_position_x', 'shop_watermark_default_position_x'),
				array('watermark_default_position_y', 'shop_watermark_default_position_y'),
				array('items_on_page', 'shop_items_on_page'),
				array('guid', 'shop_shops_guid'),
				array('url_type', 'shop_shops_url_type'),
				array('format_date', 'shop_format_date'),
				array('format_datetime', 'shop_format_datetime'),
				array('typograph_default_items', 'shop_typograph_item_by_default'),
				array('typograph_default_groups', 'shop_typograph_group_by_default'),
				array('apply_tags_automatically', 'shop_shops_apply_tags_automatic'),
				array('write_off_paid_items', 'shop_shops_writeoff_payed_items'),
				array('apply_keywords_automatically', 'shop_shops_apply_keywords_automatic'),
				array('change_filename', 'shop_shops_file_name_conversion'),
				array('attach_digital_items', 'shop_shops_attach_eitem'),
				array('yandex_market_sales_notes_default', 'shop_yandex_market_sales_notes_default'),
				array('siteuser_group_id', 'shop_shops_access'),
				array('group_image_small_max_width', 'shop_image_small_max_width_group'),
				array('group_image_large_max_width', 'shop_image_big_max_width_group'),
				array('group_image_large_max_width', 'shop_image_big_max_width_group'),
				array('group_image_small_max_height', 'shop_image_small_max_height_group'),
				array('group_image_large_max_height', 'shop_image_big_max_height_group'),
				array('preserve_aspect_ratio', 'shop_shops_default_save_proportions'),
				'site_id'
			)
			->from('shops')
			->where('shop_dir_id', '=', $shop_dir_id)
			->where('deleted', '=', 0);

		return 	$queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Удаление раздела интернет-магазинов. Магазины, находящиеся в разделе не удаляютя, а переносятся в корневой раздел.
	 *
	 * @param int $shop_dir_id идентификатор раздела магазинов
	 *
	 * @return boolean
	 */
	function DeleteShopsDir($shop_dir_id)
	{
		$aShops = Core_Entity::factory('Shop_Dir', $shop_dir_id)->Shops->findAll();
		foreach($aShops as $oShop)
		{
			$oShop->markDeleted();
		}

		$oShopDir->markDeleted();

		return TRUE;
	}

	/**
	 * Построение массива пути от текущего раздела интернет-магазинов к корневому
	 *
	 * @param int $shop_dir_id идентификатор раздела магазинов, для которого необходимо построить путь
	 * @param array $return_path_array
	 * <code>
	 * <?php
	 * $shop = new shop();
	 *
	 * $shop_dir_id = 1;
	 *
	 * $row = $shop->GetShopsDirPathArray($shop_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array ассоциативный массив, элементы которого содержат информацию о разделах, составляющих путь от текущего раздела до корневого
	 */
	function GetShopsDirPathArray($shop_dir_id, $return_path_array = array())
	{
		$shop_dir_id = intval($shop_dir_id);

		if ($shop_dir_id != 0)
		{
			$return_path_array[$row['shop_dir_id']] = $row = $this->GetShopsDir($shop_dir_id);
			$return_path_array = $this->GetShopsDirPathArray($row['shop_dir_parent_id'], $return_path_array);
		}

		return $return_path_array;
	}

	/**
	 * Формирование дерева разделов интернет-магазинов
	 *
	 * @param array $param массив параметров
	 * - $param['shop_dir_parent_id'] идентификатор раздела, относительно которого строится дерево групп. По умолчанию равен 0.
	 * - $param['site_id'] идентификатор сайта, для которого строится дерево разделов. По умолчанию равен CURRENT_SITE
	 * - $param['separator'] символ, отделяющий раздел нижнего уровня от родительского раздела
	 * - $param['shop_dir_id'] идентификатор раздела, который вместе с его подразделами не нужно включать в дерево разделов, если равен false или не передан, то включать в дерево разделов все разделы.
	 * - $param['array'] - служебный параметр
	 * - $param['sum_separator'] - служебный параметр
	 *
	 * @return array двумерный массив, содержащий дерево подгрупп
	 */
	function GetShopsDirs($param = array())
	{
		$shop_dir_parent_id = Core_Type_Conversion::toInt($param['shop_dir_parent_id']);

		$site_id = isset($param['site_id'])
			? intval($param['site_id'])
			: CURRENT_SITE;

		$separator = isset($param['separator'])
			? $param['separator']
			: '&nbsp;';

		$shop_dir_id = isset($param['shop_dir_id']) && $param['shop_dir_id'] !== FALSE
			? intval($param['shop_dir_id'])
			: FALSE;

		$param['sum_separator'] = isset($param['sum_separator'])
			? $param['sum_separator'] . $separator
			: $separator;

		$array = array();
		// Получаем информацию о подразделах информационных систем для текущего родительского раздела
		$result = $this->GetAllShopsDirs(array('shop_dir_parent_id'=>$shop_dir_parent_id, 'site_id'=>$site_id));

		// Цикл по подразделам
		while ($row = mysql_fetch_assoc($result))
		{
			if ($shop_dir_id != $row['shop_dir_id'])
			{
				$row['separator'] = $param['sum_separator'];
				$param['shop_dir_parent_id'] = $row['shop_dir_id'];
				$array[] = $row;
				/* Объединяем выбранные данные с данными из подгрупп*/
				$array = array_merge($array, $this->GetShopsDirs($param));
			}
		}

		return $array;
	}

	/**
	 * Получение пути хранения файлов товара
	 *
	 * @param $shop_items_catalog_item_id идентификатор информационного элемента
	 * @param $shop_item_row массив с данными о товаре
	 * @return mixed путь к папке товара или ложь, если товара не существует
	 */
	function GetItemDir($shop_items_catalog_item_id, $shop_item_row = false)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		if (!$shop_item_row)
		{
			$shop_item_row = $this->GetItem($shop_items_catalog_item_id);
		}

		if ($shop_item_row)
		{
			$kernel = & singleton('kernel');
			$site = & singleton('site');

			// Константа UPLOADDIR не определена
			/*if (!defined('UPLOADDIR'))
			{*/
				// Получаем информацию о информационной системе
				$shop_row = $this->GetShop($shop_item_row['shop_shops_id']);
				$site_row = $site->GetSite($shop_row['site_id']);
				$uploaddir = $site_row['site_uploaddir'];
			/*}
			else
			{
				$uploaddir = UPLOADDIR;
			}*/

			// Константа SITE_NESTING_LEVEL не определена
			if (!defined('SITE_NESTING_LEVEL'))
			{
				if (!isset($site_row))
				{
					// Получаем информацию о информационной системе
					$shop_row = $this->GetShop($shop_item_row['shop_shops_id']);
					$site_row = $site->GetSite($shop_row['site_id']);
				}

				$site_nesting_level = $site_row['site_nesting_level'];
			}
			else
			{
				$site_nesting_level = SITE_NESTING_LEVEL;
			}

			return $uploaddir . 'shop_' . Core_Type_Conversion::toInt($shop_item_row['shop_shops_id']) . '/' . $kernel->GetDirPath($shop_items_catalog_item_id, $site_nesting_level) . '/item_' . $shop_items_catalog_item_id . '/';
		}

		return FALSE;
	}

	/**
	 * Получение пути хранения файлов группы товаров
	 *
	 * @param $shop_groups_id идентификатор группы товаров
	 * @return mixed путь к папке группы товаров или ложь, если группы товаров не существует
	 */
	function GetGroupDir($shop_groups_id)
	{
		$shop_groups_id = intval($shop_groups_id);

		if ($shop_group_row = $this->GetGroup($shop_groups_id))
		{
			$kernel = & singleton('kernel');

			$site = & singleton('site');

			// Константа UPLOADDIR не определена
			/*if (!defined('UPLOADDIR'))
			{*/
				// Получаем информацию о информационной системе
				$shop_row = $this->GetShop($shop_group_row['shop_shops_id']);
				$site_row = $site->GetSite($shop_row['site_id']);
				$uploaddir = $site_row['site_uploaddir'];
			/*}
			else
			{
				$uploaddir = UPLOADDIR;
			}*/

			// Константа SITE_NESTING_LEVEL не определена
			if (!defined('SITE_NESTING_LEVEL'))
			{
				if (!isset($site_row))
				{
					// Получаем информацию о информационной системе
					$shop_row = $this->GetShop($shop_group_row['shop_shops_id']);
					$site_row = $site->GetSite($shop_row['site_id']);
				}
				$site_nesting_level = $site_row['site_nesting_level'];
			}
			else
			{
				$site_nesting_level = SITE_NESTING_LEVEL;
			}

			return $uploaddir . 'shop_' . Core_Type_Conversion::toInt($shop_group_row['shop_shops_id']) . '/' . $kernel->GetDirPath($shop_groups_id, $site_nesting_level) . '/group_' . $shop_groups_id . '/';
		}

		return FALSE;
	}

	/**
	 * Корректирует путь
	 *
	 * @param $images_path путь к директории к файлу картинки (без CMS_FOLDER)
	 * @return str
	 */
	function GetRealPath($images_path)
	{
		$images_path = str_replace('\\', '/', $images_path);
		$catalog_image_fullpath = realpath(dirname(CMS_FOLDER . $images_path)) . "/";
		$catalog_image_fullpath .= basename($images_path);

		$kernel = & singleton('kernel');
		$catalog_image_fullpath = $kernel->Utf8ToWindows1251($catalog_image_fullpath);

		return $catalog_image_fullpath;
	}

	/**
	 * Установка статуса выгрузки заказа
	 *
	 * @param int $order_id идентификатор заказа
	 * @param int $status (0 или 1) статус
	 */
	function SetUnload($order_id, $status)
	{
		$order_id = intval($order_id);
		$status = intval($status);

		$oShop_Order = Core_Entity::factory('Shop_Order', $order_id);
		$oShop_Order->unloaded = $status;
		$oShop_Order->save();
	}

	function CopyShopDir($param)
	{
		$shop_dir_parent_id = Core_Type_Conversion::toInt($param['shop_dir_parent_id']);
		$oShop_Dir = Core_Entity::factory('Shop_Dir');
		$oShop_Dir->queryBuilder()->where('parent_id', '=', $shop_dir_parent_id);

		$aShop_Dirs = $oShop_Dir->findAll();
		foreach($aShop_Dirs as $oShop_Dir)
		{
			$oShop_Dir->copy();
		}

		return TRUE;
	}
}