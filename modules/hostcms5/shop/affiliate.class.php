<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Аффилиаты".
 *
 * Файл: /modules/affiliate/affiliate.class.php
 *
 * @author Hostmake LLC

 * @version 5.x
 */
class affiliate
{
	/**
	* Массив идентификаторов аффилиатов пользователя сайта
	*
	* @var array
	* @access private
	*/
	var $affiliats_id_array;

	function getArrayAffiliatePlan($oAffiliatePlan)
	{
		return array(
			'affiliate_plans_id' => $oAffiliatePlan->id,
			'site_id' => $oAffiliatePlan->site_id,
			'affiliate_plans_name' => $oAffiliatePlan->name,
			'affiliate_plans_description' => $oAffiliatePlan->description,
			'affiliate_plans_activity' => $oAffiliatePlan->active,
			'site_users_group_id' => $oAffiliatePlan->siteuser_group_id,
			'affiliate_plans_last_change_datetime' => $oAffiliatePlan->datetime,
			'affiliate_plans_min_num_of_items' => $oAffiliatePlan->min_count_of_items,
			'affiliate_plans_min_sum_of_items' => $oAffiliatePlan->min_amount_of_items,
			'affiliate_plans_delivery_on' => $oAffiliatePlan->include_delivery,
			'users_id' => $oAffiliatePlan->user_id
		);
	}

	function getArrayAffiliatePlanLevel($oAffiliatePlanLevel)
	{
		return array(
			'affiliate_values_id' => $oAffiliatePlanLevel->id,
			'affiliate_plans_id' => $oAffiliatePlanLevel->affiliate_plan_id,
			'affiliate_values_inner_level' => $oAffiliatePlanLevel->level,
			'affiliate_values_percent' => $oAffiliatePlanLevel->percent,
			'affiliate_values_value' => $oAffiliatePlanLevel->value,
			'affiliate_values_type' => $oAffiliatePlanLevel->type,
			'users_id' => $oAffiliatePlanLevel->user_id
		);
	}

	function getArraySiteuserAffiliate($oSiteuser_Affiliate)
	{
		return array(
			'affiliate_id' => $oSiteuser_Affiliate->id,
			'affiliate_site_users_id' => $oSiteuser_Affiliate->siteuser_id,
			'site_users_id' => $oSiteuser_Affiliate->referral_siteuser_id,
			'affiliate_active' => $oSiteuser_Affiliate->active,
			'affiliate_invite_date' => $oSiteuser_Affiliate->date,
			'users_id' => $oSiteuser_Affiliate->user_id
		);
	}

	/**
	 * Получение информации о плане аффилиата
	 *
	 * @param int $affiliate_plans_id идентификатор плана аффилиата
	 * @return mixed массив данных, либо False
	 */
	function GetAffiliatePlans($affiliate_plans_id)
	{
		$affiliate_plans_id = intval($affiliate_plans_id);
		$oAffiliate_Plan = Core_Entity::factory('Affiliate_Plan')->find($affiliate_plans_id);
		return !is_null($oAffiliate_Plan->id) ? $this->getArrayAffiliatePlan($oAffiliate_Plan) : FALSE;
	}

	/**
	 * Добавление информации о плане аффилиата
	 *
	 * @param array $param Массив параметров
	 * - int $param['affiliate_plans_id'] идентификатор плана аффилиата
	 * - int $param['site_id'] идентификатор сайта
	 * - str $param['affiliate_plans_name'] название плана
	 * - str $param['affiliate_plans_description'] описание плана
	 * - int $param['affiliate_plans_activity'] статус активности
	 * - int $param['site_users_group_id'] идентификатор группы пользователей сайта
	 * - str $param['affiliate_plans_last_change_datetime'] дата последнего изменения плана
	 * - int $param['affiliate_plans_min_num_of_items'] минимальное количество товаров в заказе для активации плана
	 * - int $param['affiliate_plans_min_sum_of_items'] минимальное сумма единовременной покупки для активации плана
	 * - int $param['affiliate_plans_delivery_on'] флаг учета доставки при просчете комиссии
	 * @return mixed Идентификатор вставленной записи, либо False
	 */
	function InsertAffiliatePlans($param)
	{
		if (!isset($param['affiliate_plans_id']) || !$param['affiliate_plans_id'])
		{
			$param['affiliate_plans_id'] = NULL;
		}

		$oAffiliate_Plan = Core_Entity::factory('Affiliate_Plan', $param['affiliate_plans_id']);

		if (isset($param['site_id']))
		{
			$oAffiliate_Plan->site_id = intval($param['site_id']);
		}
		elseif(is_null($oAffiliate_Plan->id))
		{
			$oAffiliate_Plan->site_id = CURRENT_SITE;
		}

		isset($param['affiliate_plans_name']) && $oAffiliate_Plan->name = $param['affiliate_plans_name'];
		isset($param['affiliate_plans_description']) && $oAffiliate_Plan->description = $param['affiliate_plans_description'];
		isset($param['affiliate_plans_activity']) && $oAffiliate_Plan->active = intval($param['affiliate_plans_activity']);
		isset($param['site_users_group_id']) && $oAffiliate_Plan->siteuser_group_id = intval($param['site_users_group_id']);
		isset($param['affiliate_plans_last_change_datetime']) && $oAffiliate_Plan->datetime = Core_Date::datetime2sql($param['affiliate_plans_last_change_datetime']);
		isset($param['affiliate_plans_min_num_of_items']) && $oAffiliate_Plan->min_count_of_items = intval($param['affiliate_plans_min_num_of_items']);
		isset($param['affiliate_plans_min_sum_of_items']) && $oAffiliate_Plan->min_amount_of_items = floatval($param['affiliate_plans_min_sum_of_items']);
		isset($param['affiliate_plans_delivery_on']) && $oAffiliate_Plan->include_delivery = intval($param['affiliate_plans_delivery_on']);

		if (is_null($oAffiliate_Plan->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oAffiliate_Plan->user_id = intval($param['users_id']);
		}

		$oAffiliate_Plan->save();
		return $oAffiliate_Plan->id;
	}

	/**
	 * Удаление информации о плане аффилиата
	 *
	 * @param int $affiliate_plans_id идентификатор плана
	 * @return resource
	 */
	function DeleteAffiliatePlans($affiliate_plans_id)
	{
		$affiliate_plans_id = intval($affiliate_plans_id);
		Core_Entity::factory('Affiliate_Plan', $affiliate_plans_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Получение информации о комисии
	 *
	 * @param int $affiliate_values_id идентификатор комиссии
	 * @return mixed массив данных, либо False
	 */
	function GetAffiliateValues($affiliate_values_id)
	{
		$affiliate_values_id = intval($affiliate_values_id);
		$oAffiliate_Plan_Level = Core_Entity::factory('Affiliate_Plan_Level')->find($affiliate_values_id);
		return !is_null($oAffiliate_Plan_Level->id)
			? $this->getArrayAffiliatePlanLevel($oAffiliatePlanLevel)
			: FALSE;
	}

	/**
	 * Добавление информации о комиссии
	 *
	 * @param array $param Массив параметров
	 * - int $param['affiliate_values_id'] идентификатор комиссии
	 * - int $param['affiliate_plans_id'] идентификатор плана
	 * - int $param['affiliate_values_inner_level'] уровень вложенности
	 * - int $param['affiliate_values_percent'] комиссия в процентах
	 * - int $param['affiliate_values_value'] комиссия в одной из валют магазина
	 * - int $param['affiliate_values_type'] тип комиссии
	 * @return mixed Идентификатор вставленной записи, либо False
	 */
	function InsertAffiliateValues($param)
	{
		if(!isset($param['affiliate_values_id']) || !$param['affiliate_values_id'])
		{
			$param['affiliate_values_id'] = NULL;
		}

		$oAffiliate_Plan_Level = Core_Entity::factory('Affiliate_Plan_Level', $param['affiliate_values_id']);

		isset($param['affiliate_plans_id']) && $oAffiliate_Plan_Level->affiliate_plan_id = intval($param['affiliate_plans_id']);

		isset($param['affiliate_values_inner_level']) && $oAffiliate_Plan_Level->level = intval($param['affiliate_values_inner_level']);

		if (isset($param['affiliate_values_percent']))
		{
			$affiliate_values_percent = floatval($param['affiliate_values_percent']);
			if ($affiliate_values_percent > 2147483647)
			{
				$affiliate_values_percent = 2147483647;
			}
			$oAffiliate_Plan_Level->percent = $affiliate_values_percent;
		}

		if (isset($param['affiliate_values_value']))
		{
			$affiliate_values_value = floatval($param['affiliate_values_value']);
			if ($affiliate_values_value > 2147483647)
			{
				$affiliate_values_value = 2147483647;
			}
			$oAffiliate_Plan_Level->value = $affiliate_values_value;
		}

		if (isset($param['affiliate_values_type']))
		{
			$oAffiliate_Plan_Level->type = intval($param['affiliate_values_type']);
			//$affiliate_values_type = intval($param['affiliate_values_type']);
			//$sql_param[] = "`affiliate_values_type` = '$affiliate_values_type'";
		}

		is_null($oAffiliate_Plan_Level->id) && isset($param['users_id']) && $param['users_id'] && $oAffiliate_Plan_Level->user_id = intval($param['users_id']);

		$oAffiliate_Plan_Level->save();
		return $oAffiliate_Plan_Level->id;
	}

	/**
	 * Удаление информации о комиссии
	 *
	 * @param int $affiliate_values_id идентификатор комиссии
	 * @return resource
	 */
	function DeleteAffiliateValues($affiliate_values_id)
	{
		$affiliate_values_id = intval($affiliate_values_id);
		Core_Entity::factory('Affiliate_Plan_Level', $affiliate_values_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Получение всех уровней партнерской программы
	 *
	 * @param int $affiliate_plans_id идентификатор партнерской программы
	 * @return mixed Resource или False
	 */
	function GetAllAffiliateValuesForAffiliate($affiliate_plans_id)
	{
		$affiliate_plans_id = intval($affiliate_plans_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'affiliate_values_id'),
				array('affiliate_plan_id', 'affiliate_plans_id'),
				array('level', 'affiliate_values_inner_level'),
				array('percent', 'affiliate_values_percent'),
				array('value', 'affiliate_values_value'),
				array('type', 'affiliate_values_type'),
				array('user_id', 'users_id')
			)
			->from('affiliate_plan_levels')
			->where('affiliate_plan_id', '=', $affiliate_plans_id)
			->where('deleted', '=', 0);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение всех партнерских программ
	 * @param arr $site_users_group_array массив идентификаторов групп пользователей сайта
	 * @param int $site_id идентификатор сайта (0 - выборка со всех сайтов)
	 * @return mixed Resource или False
	 */
	function GetAllAffiliatePlans($site_users_group_array, $site_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'affiliate_plans_id'),
				'site_id',
				array('name', 'affiliate_plans_name'),
				array('description', 'affiliate_plans_description'),
				array('active', 'affiliate_plans_activity'),
				array('siteuser_group_id', 'site_users_group_id'),
				array('datetime', 'affiliate_plans_last_change_datetime'),
				array('min_count_of_items', 'affiliate_plans_min_num_of_items'),
				array('min_amount_of_items', 'affiliate_plans_min_sum_of_items'),
				array('include_delivery', 'affiliate_plans_delivery_on'),
				array('user_id', 'users_id')
			)
			->from('affiliate_plans')
			->where('deleted', '=', 0);

		$site_id = $site_id === FALSE
			? CURRENT_SITE
			: intval($site_id);

		$site_id && $queryBuilder->where('site_id', '=', $site_id);

		if (is_array($site_users_group_array) && count($site_users_group_array) > 1)
		{
			$queryBuilder->where('siteuser_group_id', 'IN', Core_Array::toInt($site_users_group_array));
		}
		elseif($site_users_group_array > 0)
		{
			$queryBuilder->where('siteuser_group_id', '=', intval($site_users_group_array));
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение информации об аффилиате
	 *
	 * @param int $affiliate_id идентификатор аффилиата
	 * @return mixed массив данных, либо False
	 */
	function GetAffiliate($affiliate_id)
	{
		$affiliate_id = intval($affiliate_id);
		$oSiteuser_Affiliate = Core_Entity::factory('Siteuser_Affiliate')->find($affiliate_id);
		return !is_null($oSiteuser_Affiliate->id)
			? $this->getArraySiteuserAffiliate($oSiteuser_Affiliate)
			: FALSE;
	}

	/**
	 * Получение всех пользователей, привлеченных определенным аффилиатом
	 *
	 * @param unknown_type $affiliate_site_users_id
	 * @return unknown
	 */
	function GetAllUsersForAffiliate($affiliate_site_users_id)
	{
		$affiliate_site_users_id = intval($affiliate_site_users_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'affiliate_id'),
				array('referral_siteuser_id', 'site_users_id'),
				array('siteuser_id', 'affiliate_site_users_id'),
				array('active', 'affiliate_active'),
				array('date', 'affiliate_invite_date'),
				array('user_id', 'users_id')
			)
			->from('siteuser_affiliates')
			->where('siteuser_id', '=', $affiliate_site_users_id);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Добавление информации об аффилиате
	 *
	 * @param array $param Массив параметров
	 * - int $param['affiliate_id'] идентификатор связи пользователя и аффилиата
	 * - int $param['site_users_id'] идентификатор пользователя, которого привел аффилиат
	 * - int $param['affiliate_plans_id'] идентификатор патрнерской программы
	 * - int $param['affiliate_site_users_id'] идентификатор аффилиата
	 * - int $param['affiliate_active'] флаг активности польщователя аффилиата
	 * - str $param['affiliate_invite_date'] дата приглашения аффилиата, если не передана, ставится текущая
	 * @return mixed Идентификатор вставленной записи, либо False
	 */
	function InsertAffiliate($param)
	{
		if(!isset($param['affiliate_id']) || !$param['affiliate_id'])
		{
			$param['affiliate_id'] = NULL;
		}

		$oSiteuser_Affiliate = Core_Entity::factory('Siteuser_Affiliate', $param['affiliate_id']);

		isset($param['site_users_id']) && $oSiteuser_Affiliate->referral_siteuser_id = intval($param['site_users_id']);
		isset($param['affiliate_site_users_id']) && $oSiteuser_Affiliate->siteuser_id = intval($param['affiliate_site_users_id']);

		if (isset($param['affiliate_invite_date']))
		{
			$oSiteuser_Affiliate->date = $param['affiliate_invite_date'];
		}
		elseif (is_null($oSiteuser_Affiliate->id))
		{
			$oSiteuser_Affiliate->date = date("Y-m-d");
		}

		isset($param['affiliate_active']) && $oSiteuser_Affiliate->active = intval($param['affiliate_active']);

		is_null($oSiteuser_Affiliate->id) && isset($param['users_id']) && $param['users_id'] && $oSiteuser_Affiliate->user_id = intval($param['users_id']);

		$oSiteuser_Affiliate->save();
		return $oSiteuser_Affiliate->id;
	}

	/**
	 * Удаление информации об аффилиате
	 *
	 * @param int $affiliate_id идентификатор аффилиата
	 * @return resource
	 */
	function DeleteAffiliate($affiliate_id)
	{
		$affiliate_id = intval($affiliate_id);
		Core_Entity::factory('Siteuser_Affiliate', $affiliate_id)->delete();
		return TRUE;
	}

	/**
	 * Проверка участия пользователя сайта в аффилиат программе в качестве привлеченного пользователя
	 *
	 * @param int $site_users_id идентификатор пользователя сайта
	 * @param int $affiliate_site_users_id идентификатор аффилиата
	 * @return mixed массив данных, либо False
	 */
	function CheckSiteUserAffiliate($site_users_id, $affiliate_site_users_id)
	{
		$site_users_id = intval($site_users_id);
		$affiliate_site_users_id = intval($affiliate_site_users_id);

		$oSiteuser_Affiliate = Core_Entity::factory('Siteuser', $site_users_id)->Siteuser_Affiliates->getByReferralId($affiliate_site_users_id);

		return !is_null($oSiteuser_Affiliate)
			? $this->getArraySiteuserAffiliate($oSiteuser_Affiliate)
			: FALSE;
	}

	/**
	 * Добавление/удаление ассоциации партнерской программы магазину
	 *
	 * @param int $affiliate_plans_id идентификатор партнерской программы
	 * @param int $shop_shops_id идентификатор магазина
	 */
	function AcceptAffiliatePlanToShop($affiliate_plans_id, $shop_shops_id)
	{
		$affiliate_plans_id = intval($affiliate_plans_id);
		$shop_shops_id = intval($shop_shops_id);

		$oShop_Affiliate_Plan = Core_Entity::factory('Affiliate_Plan', $affiliate_plans_id)->Shop_Affiliate_Plans->getByShopId($shop_shops_id);

		if (!is_null($oShop_Affiliate_Plan))
		{
			$oShop_Affiliate_Plan->delete();
		}
		else
		{
			$oShop_Affiliate_Plan = Core_Entity::factory('Shop_Affiliate_Plan');
			$oShop_Affiliate_Plan->affiliate_plan_id = $affiliate_plans_id;
			$oShop_Affiliate_Plan->shop_id = $shop_shops_id;
			$oShop_Affiliate_Plan->save();
		}

		return TRUE;
	}

	/**
	 * Удаление ассоциации партнерской программы магазину
	 *
	 * @param int $affiliate_plans_id идентификатор партнерской программы
	 * @param int $shop_shops_id идентификатор магазина
	 */
	function DisableAffiliatePlanToShop($affiliate_plans_id, $shop_shops_id)
	{
		$affiliate_plans_id = intval($affiliate_plans_id);
		$shop_shops_id = intval($shop_shops_id);

		$oShop_Affiliate_Plan = Core_Entity::factory('Affiliate_Plan', $affiliate_plans_id)->Shop_Affiliate_Plans->getByShopId($shop_shops_id);

		if (!is_null($oShop_Affiliate_Plan))
		{
			$oShop_Affiliate_Plan->delete();
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Добавление ассоциации партнерской программы магазину
	 *
	 * @param int $affiliate_plans_id идентификатор партнерской программы
	 * @param int $shop_shops_id идентификатор магазина
	 */
	function EnableAffiliatePlanToShop($affiliate_plans_id, $shop_shops_id)
	{
		$affiliate_plans_id = intval($affiliate_plans_id);
		$shop_shops_id = intval($shop_shops_id);

		$oShop_Affiliate_Plan = Core_Entity::factory('Affiliate_Plan', $affiliate_plans_id)->Shop_Affiliate_Plans->getByShopId($shop_shops_id);

		if (is_null($oShop_Affiliate_Plan))
		{
			$oShop_Affiliate_Plan = Core_Entity::factory('Shop_Affiliate_Plan');
			$oShop_Affiliate_Plan->affiliate_plan_id = $affiliate_plans_id;
			$oShop_Affiliate_Plan->shop_id = $shop_shops_id;
			$oShop_Affiliate_Plan->save();
		}

		return TRUE;
	}

	/**
	 * Показ партнерских программ.
	 *
	 * @param int $site_id идентификатор сайта
	 * @param str $xsl_name имя XSL-шаблона
	 * @param arr $param ассоциативный массив параметров
	 * - array['site_user_id'] идентификатор пользователя сайта
	 * @param array $external_propertys массив дополнительных свойств для включения в исходный XML-код
	 */
	function ShowAffiliate($site_id, $xsl_name, $param = array(), $external_propertys = array())
	{
		$param = Core_Type_Conversion::toArray($param);
		$site_user_id = Core_Type_Conversion::toInt($param['site_user_id']);
		$external_propertys = Core_Type_Conversion::toArray($external_propertys);

		// Начинаем формирование xml-а
		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

		$users_id_array = array();

		// Получаем список идентификаторов пользователей сайта
		$this->getUsersID($users_id_array, $site_user_id);

		$date_from = Core_Type_Conversion::toStr($param['date_from']);
		$date_to = Core_Type_Conversion::toStr($param['date_to']);

		$queryBuilder = Core_QueryBuilder::select(
				array('shop_order_items.name', 'shop_order_items_name'),
				array('SUM(shop_order_items.quantity)',  'quantity'),
				array('SUM(shop_order_items.price)', 'price')
			)
			->from('shop_order_items')
			->join('shop_orders', 'shop_orders.id', '=', 'shop_order_items.shop_order_id')
			->join('shop_currencies', 'shop_currencies.id', '=', 'shop_orders.shop_currency_id')
			->where('datetime', '>', $date_from . ' 23:59:59')
			->where('datetime', '<', $date_to . ' 23:59:59')
			->where('shop_orders.siteuser_id', 'IN', $users_id_array)
			->where('paid', '=', 1)
			->where('shop_order_items.shop_item_id', '!=', 0)
			->where('shop_order_items.deleted', '=', 0)
			->where('shop_orders.deleted', '=', 0)
			->where('shop_currencies.deleted', '=', 0)
			->groupBy('shop_item_id');

		$aResult = $queryBuilder->execute()->asAssoc()->result();

		$xmlData .= '<affiliate_plans site_id="' . $site_id . '">' . "\n";

		// Выводим данные в XML
		$xmlData .= '<affiliate_items_data>' . "\n";
		$xmlData .= '<affiliate_items_date_from>' . Core_Date::sql2date($date_from) . '</affiliate_items_date_from>' . "\n";
		$xmlData .= '<affiliate_items_date_to>' . Core_Date::sql2date($date_to) . '</affiliate_items_date_to>' . "\n";

		//while($row = mysql_fetch_assoc($result))
		foreach($aResult as $row)
		{
			$xmlData .= '<affiliate_items_item>' . "\n";
			$xmlData .= '<affiliate_items_name>' . $row['shop_order_items_name'] . '</affiliate_items_name>' . "\n";
			$xmlData .= '<affiliate_items_quantity>' . $row['quantity'] . '</affiliate_items_quantity>' . "\n";
			$xmlData .= '<affiliate_items_price>' . $row['price'] . '</affiliate_items_price>' . "\n";
			$xmlData .= '</affiliate_items_item>'  ."\n";
		}

		$xmlData .= '</affiliate_items_data>' . "\n";

		// Выводим информацию о структуре партнерских отношений до формирования данных о программах аффилиатов
		$xmlData .= $this->GenXmlAffiliatTree($site_user_id);

		// Вносим в XML дополнительные теги из массива дополнительных параметров
		$ExternalXml = new ExternalXml();
		$xmlData .= $ExternalXml->GenXml($external_propertys);

		$site = & singleton('site');
		$xmlData .= $site->GetXmlForSite($site_id);

		$shop = & singleton('shop');
		$shops_res = $shop->GetAllShops($site_id);

		if ($shops_res)
		{
			while ($shops_row = mysql_fetch_assoc($shops_res))
			{
				$xmlData .= $this->GenXML4AffiliatePlan($shops_row['shop_shops_id'], $site_user_id);
			}
		}
		$xmlData .= '</affiliate_plans>' . "\n";

		$xsl = & singleton('xsl');
		echo $xsl->build($xmlData, $xsl_name);
	}

	/**
	 * Генерация XML-данных для партнерских программ магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $site_user_id идентификатор пользователя сайта - аффилиата
	 * @return str XML данные
	 */
	function GenXML4AffiliatePlan($shop_shops_id, $site_user_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$site_user_id = intval($site_user_id);

		$shop = & singleton('shop');

		// Получаем все аффилиат-программы для данного магазина
		$affiliat_plans_id_row = $this->GetAffiliatePlansIdForShop($shop_shops_id, $site_user_id);
		$xmlData = '<affiliate_plan shop_id="' . $shop_shops_id . '">' . "\n";
		$xmlData .= $shop->GenXml4Shop($shop_shops_id);

		if ($affiliat_plans_id_row)
		{
			foreach ($affiliat_plans_id_row as $affiliate_plans_id)
			{
				$affiliate_plans_row = $this->GetAffiliatePlans($affiliate_plans_id);

				if ($affiliate_plans_row)
				{
					$xmlData .= '<affiliate_program id="' . $affiliate_plans_id . '">' . "\n";
					$xmlData .= '<site_id>' . str_for_xml($affiliate_plans_row['site_id']) . '</site_id>' . "\n";
					$xmlData .= '<affiliate_plans_name>' . str_for_xml($affiliate_plans_row['affiliate_plans_name']) . '</affiliate_plans_name>' . "\n";
					$xmlData .= '<affiliate_plans_description>' . str_for_xml($affiliate_plans_row['affiliate_plans_description']) . '</affiliate_plans_description>' . "\n";
					$xmlData .= '<affiliate_plans_activity>' . str_for_xml($affiliate_plans_row['affiliate_plans_activity']) . '</affiliate_plans_activity>' . "\n";
					$xmlData .= '<affiliate_plans_last_change_datetime>' . Core_Date::sql2datetime($affiliate_plans_row['affiliate_plans_last_change_datetime']) . '</affiliate_plans_last_change_datetime>' . "\n";
					$xmlData .= '<affiliate_plans_min_num_of_items>' . str_for_xml($affiliate_plans_row['affiliate_plans_min_num_of_items']) . '</affiliate_plans_min_num_of_items>' . "\n";
					$xmlData .= '<affiliate_plans_min_sum_of_items>' . str_for_xml($affiliate_plans_row['affiliate_plans_min_sum_of_items']) . '</affiliate_plans_min_sum_of_items>' . "\n";
					$xmlData .= '<users_id>' . str_for_xml($affiliate_plans_row['users_id']) . '</users_id>' . "\n";

					$xmlData .= $this->GenXML4AffiliateValues($affiliate_plans_id);

					$xmlData .= '</affiliate_program>' . "\n";
				}
			}
		}

		$xmlData .= '</affiliate_plan>'."\n";

		return $xmlData;
	}

	/**
	 * Получение идентификаторов всех магазинов, в которых доступна парнерская программа
	 *
	 * @param int $affiliate_plans_id идентификатор партнерской программы
	 * @return arr массив идентификаторов магазинов
	 */
	function GetShopsIdForAffiliatePlans($affiliate_plans_id)
	{
		$affiliate_plans_id = intval($affiliate_plans_id);

		$oShop_Affiliate_Plan = Core_Entity::factory('Shop_Affiliate_Plan');
		$oShop_Affiliate_Plan
			->queryBuilder()
			->where('affiliate_plan_id', '=', $affiliate_plans_id);

		$oShop_Affiliate_Plans = $oShop_Affiliate_Plan->findAll();

		$result_row = array();
		foreach($oShop_Affiliate_Plans as $oShop_Affiliate_Plan)
		{
			$result_row[] = $oShop_Affiliate_Plan->shop_id;
		}

		return count($result_row) == 0 ? FALSE : $result_row;
	}

	/**
	 * Получение идентификаторов всех парнерских программ для магазина
	 *
	 * @param int $shop_shops_id идентификатор магазина
	 * @param int $site_user_id идентификатор пользователя сайта - Аффилиата
	 * @param arr $param массив дополнительных параметров
	 * - array['affiliate_plans_min_num_of_items'] минимальное количество купленного товара
	 * - array['affiliate_plans_min_sum_of_items'] минимальная сумма, на которую куплен товар
	 * @return arr массив идентификаторов партнерских программ
	 */
	function GetAffiliatePlansIdForShop($shop_shops_id, $site_user_id, $param = array())
	{
		$shop_shops_id = intval($shop_shops_id);
		$site_user_id = intval($site_user_id);

		$param = Core_Type_Conversion::toArray($param);

		$oShop_Affiliate_Plan = Core_Entity::factory('Shop_Affiliate_Plan');
		$oShop_Affiliate_Plan->queryBuilder()->where('shop_id', '=', $shop_shops_id);

		$oShop_Affiliate_Plans = $oShop_Affiliate_Plan->findAll();

		$result_row = array();
		foreach($oShop_Affiliate_Plans as $oShop_Affiliate_Plan)
		{
			$result_row[] = $oShop_Affiliate_Plan->affiliate_plan_id;
		}

		if (count($result_row))
		{
			$SiteUsers = new SiteUsers();

			// Фильруем по группе пользователй сайта
			$site_users_groups_row = $SiteUsers->GetGroupsForUser($site_user_id);

			$queryBuilder = Core_QueryBuilder::select('id')
				->from('affiliate_plans')
				->where('id', 'IN', $result_row)
				->where('siteuser_group_id', 'IN', $site_users_groups_row)
				->where('active', '=', 1)
				->where('deleted', '=', 0);

			if (isset($param['affiliate_plans_min_num_of_items']) && $param['affiliate_plans_min_num_of_items'] != '')
			{
				$queryBuilder->where('min_count_of_items', '<=', intval($param['affiliate_plans_min_num_of_items']));
			}

			if (isset($param['affiliate_plans_min_sum_of_items']) && $param['affiliate_plans_min_sum_of_items'] != '')
			{
				$queryBuilder->where('min_amount_of_items', '<=', intval($param['affiliate_plans_min_sum_of_items']));
			}

			$aResult = $queryBuilder->execute()->asAssoc()->result();

			$result_row = array();
			foreach($aResult as $row)
			{
				$result_row[] = $row['id'];
			}
		}
		else
		{
			return FALSE;
		}

		return count($result_row) == 0 ? FALSE : $result_row;
	}

	/**
	 * Генерация XML для уровней партнерской программы
	 *
	 * @param int $affiliate_plans_id идентификатор партнерской программы
	 * @return str XML-код
	 */
	function GenXML4AffiliateValues($affiliate_plans_id)
	{
		$affiliate_plans_id = intval($affiliate_plans_id);
		$xmlData = '<affiliate_levels>' . "\n";
		$affiliate_levels_res = $this->GetAllAffiliateValuesForAffiliate($affiliate_plans_id);
		if ($affiliate_levels_res)
		{
			while ($affiliate_levels_row = mysql_fetch_assoc($affiliate_levels_res))
			{
				$xmlData .= '<affiliate_level id="' . str_for_xml($affiliate_levels_row['affiliate_values_id']) . '">' . "\n";
				$xmlData .= '<affiliate_plans_id>' . str_for_xml($affiliate_levels_row['affiliate_plans_id']) . '</affiliate_plans_id>' . "\n";
				$xmlData .= '<affiliate_values_inner_level>' . str_for_xml($affiliate_levels_row['affiliate_values_inner_level']) . '</affiliate_values_inner_level>' . "\n";
				$xmlData .= '<affiliate_values_percent>' . str_for_xml($affiliate_levels_row['affiliate_values_percent']) . '</affiliate_values_percent>' . "\n";
				$xmlData .= '<affiliate_values_value>' . str_for_xml($affiliate_levels_row['affiliate_values_value']) . '</affiliate_values_value>' . "\n";
				$xmlData .= '<affiliate_values_type>' . str_for_xml($affiliate_levels_row['affiliate_values_type']) . '</affiliate_values_type>' . "\n";
				$xmlData .= '<users_id>' . str_for_xml($affiliate_levels_row['users_id']) . '</users_id>' . "\n";
				$xmlData .= '</affiliate_level>' . "\n";
			}
		}

		$xmlData .= '</affiliate_levels>' . "\n";

		return $xmlData;
	}

	/**
	 * Начисление пользователю сумм с заказа по партнеской программе
	 *
	 * @param $site_users_id идентификатор пользователя сайта (реферала), который оформил заказ
	 * @param $shop_order_id идентификатор заказа
	 * @return
	 */
	function AcceptAffiliatePlanForSiteUsers($site_users_id, $shop_order_id)
	{
		$site_users_id = intval($site_users_id);
		$shop_order_id = intval($shop_order_id);

		// Получаем список всех аффилиатов данного пользователя
		$affiliats_array = $this->GetAffiliatsForUser($site_users_id);
		if (count($affiliats_array) == 0)
		{
			// Некому начислять деньги
			return FALSE;
		}

		// Получаем количестов товаров в заказе
		$shop = & singleton('shop');
		$order_items_res = $shop->GetOrderItems($shop_order_id);

		$items_count = mysql_num_rows($order_items_res);
		if ($items_count == 0)
		{
			// Заказ пустой
			return FALSE;
		}

		$order_sum = $shop->GetOrderSum($shop_order_id);
		$order_row = $shop->GetOrder($shop_order_id);

		// План аффилиата расчитывается для непосредственного аффилиата пользователя,
		// который оформил заказ
		$site_users_id_first_affiliat = Core_Type_Conversion::toInt($affiliats_array[0]);

		$array_of_plans_id = $this->GetAffiliatePlansIdForShop($order_row['shop_shops_id'], $site_users_id_first_affiliat, array('affiliate_plans_min_num_of_items' => $items_count, 'affiliate_plans_min_sum_of_items' => $order_sum));

		if (count($array_of_plans_id) == 0)
		{
			return FALSE;
		}
		elseif (is_array($array_of_plans_id))
		{
			// Приводим элементы к INT
			foreach ($array_of_plans_id as $key => $value)
			{
				$array_of_plans_id[$key] = intval($value);
			}

			$queryBuilder = Core_QueryBuilder::select('affiliate_plan_id', Core_QueryBuilder::expression("IF((" . $order_sum . " * percent/100 ) > value AND type = '0', " . $order_sum . " * percent/100, value) AS value"))
				->from('affiliate_plan_levels')
				->where('affiliate_plan_id', 'IN', $array_of_plans_id)
				->where('deleted', '=', 0)
				->orderBy('value', 'DESC')
				->limit(1);

			$aResult = $queryBuilder->execute()->asAssoc()->result();
			$affiliate_plans_id = $aResult[0]['affiliate_plan_id'];
		}
		else
		{
			$affiliate_plans_id = 0;
		}

		// Начисляем каждому аффилиату пользователя на счет деньги
		foreach ($affiliats_array as $affiliate_level => $affiliate_id)
		{
			// Получаем сумму которую нужно начислить текущему аффилиату
			$true_affiliate_level = $affiliate_level + 1; // т.к. $affiliate_level начинается с 0, 0-го уровня аффилиатства нет

			$oAffiliate_Plan_Levels = Core_Entity::factory('Affiliate_Plan', $affiliate_plans_id)->Affiliate_Plan_Levels;
			$oAffiliate_Plan_Levels->queryBuilder()->where('level', '=', $true_affiliate_level);

			$aAffiliate_Plan_Levels = $oAffiliate_Plan_Levels->findAll();

			if (count($aAffiliate_Plan_Levels))
			{
				$oAffiliate_Plan_Level = $aAffiliate_Plan_Levels[0];
				if ($oAffiliate_Plan_Level->type == 0)
				{
					// Получаем сумму
					$sum = $order_sum * ($oAffiliate_Plan_Level->percent / 100);
				}
				else
				{
					// Получаем сумму
					$sum = $oAffiliate_Plan_Level->value;
				}

				if ($sum > 0)
				{
					// Получаем информацию о заказе
					$order_row = $shop->GetOrder($shop_order_id);

					$shop_shops_id = $order_row['shop_shops_id'];

					$shop_row = $shop->GetShop($shop_shops_id);

					// Получаем идентификатор валюты из магазина
					$shop_currency_id = $shop_row['shop_currency_id'];

					// Начисляем сумму на счет аффилиату
					$shop->InsertSiteUserAccountTransaction(array(
					'shop_shops_id' => $shop_shops_id,
					'site_users_id' => $affiliate_id,
					'shop_site_users_account_active' => 1,
					'shop_site_users_account_datetime' => date("d.m.Y H:i:s"),
					'shop_site_users_account_sum' => $sum,
					'shop_currency_id' => $shop_currency_id,
					'shop_site_users_account_sum_in_base_currency' => $sum,
					'shop_order_id' => $shop_order_id,
					'shop_site_users_account_type' => 1,
					'shop_site_users_account_description' => Core::_('Shop.form_edit_add_shop_special_prices_price', $shop_order_id)
					));
				}
			}
			else
			{
				// Если уровню аффилиата нет соответствующего уровня программы аффилиатства то прерываем цикл
				break;
			}
		}
	}

	/**
	 * Получение массива идентификаторов аффилиатов пользователя
	 *
	 * @param int $site_users_id идентификатор пользователя сайта
	 * @param bool $first_call флаг первого вызова
	 * @return array массив аффилиатов данного пользователя
	 */
	function GetAffiliatsForUser($site_users_id, $first_call = TRUE, $overflow_protection = 0)
	{
		$site_users_id = intval($site_users_id);

		// При первом вызове обнуляем массив
		if ($first_call)
		{
			$this->affiliats_id_array = array();
		}

		$aSiteuser_Affiliates = Core_Entity::factory('Siteuser', $site_users_id)->Siteuser_Affiliates->findAll();
		if (count($aSiteuser_Affiliates) == 1)
		{
			if ($overflow_protection == 100)
			{
				// Зацикливание
				return FALSE;
			}
			else
			{
				$overflow_protection++;
			}

			$oSiteuser_Affiliate = $aSiteuser_Affiliates[0];
			$this->affiliats_id_array[] = $oSiteuser_Affiliate->site_users_id;
			$this->GetAffiliatsForUser($oSiteuser_Affiliate->site_users_id, FALSE, $overflow_protection);
		}

		return $this->affiliats_id_array;
	}


	function GenXmlAffiliatTree($affiliate_site_users_id)
	{
		$affiliate_site_users_id = intval($affiliate_site_users_id);

		$SiteUsers = & singleton('SiteUsers');
		$shop = & singleton('shop');

		$xml_data = '<affiliat_user>' . "\n";
		// При первом вызове генерируем информацию о самом пользователе
		$xml_data .= $SiteUsers->GetSiteUserXml($affiliate_site_users_id, array(), array(), array('xml_show_external_property' => FALSE));

		$aSiteuser_Affiliates = Core_Entity::factory('Siteuser', $affiliate_site_users_id)
			->Siteuser_Affiliates->findAll();

		if(count($aSiteuser_Affiliates))
		{
			$xml_data .= '<affiliat_invite_date>' . Core_Date::sql2date($aSiteuser_Affiliates[0]->date) .'</affiliat_invite_date>' . "\n";
		}

		$oShop_Siteuser_Transactions = Core_Entity::factory('Siteuser', $affiliate_site_users_id)
			->Shop_Siteuser_Transactions;

		$oShop_Siteuser_Transactions
			->queryBuilder()
			->where('active', '=', 1)
			->where('type', '=', 1);

		$aShop_Siteuser_Transactions = $oShop_Siteuser_Transactions->findAll();
		foreach($aShop_Siteuser_Transactions as $oShop_Siteuser_Transaction)
		{
			$xml_data .= '<transaction id="' . $oShop_Siteuser_Transaction->id . '">' . "\n";

			$xml_data .= '<shop_currency_id>' . $oShop_Siteuser_Transaction->shop_currency_id . '</shop_currency_id>' . "\n";
			$xml_data .= '<shop_site_users_account_sum_in_base_currency>' . $oShop_Siteuser_Transaction->amount_base_currency . '</shop_site_users_account_sum_in_base_currency>' . "\n";
			$xml_data .= '<shop_order_id>' . $oShop_Siteuser_Transaction->shop_order_id . '</shop_order_id>' . "\n";
			$xml_data .= '<shop_shops_id>' . $oShop_Siteuser_Transaction->shop_id . '</shop_shops_id>' . "\n";

			$xml_data .= '</transaction>' . "\n";
		}

		$users_res = $this->GetAllUsersForAffiliate($affiliate_site_users_id);
		if($users_res)
		{
			while($users_row = mysql_fetch_assoc($users_res))
			{
				// Вызываем себя рекурсивно для построения XML-данных
				$xml_data .= $this->GenXmlAffiliatTree($users_row['site_users_id']);
			}
		}

		$xml_data .= '</affiliat_user>';

		return $xml_data;
	}

	function ShowAffiliatStatistics($site_user_id, $xsl_name, $date_from, $date_to)
	{
		$Site_users = & singleton('Site_users');

		$site_user_id = intval($site_user_id);

		$queryBuilder = Core_QueryBuilder::select(
				'shop_siteuser_transactions.siteuser_id',
				'shop_siteuser_transactions.datetime',
				'shop_siteuser_transactions.amount_base_currency',
				'shop_siteuser_transactions.shop_id',
				'shop_currencies.name'
			)
			->from('shop_siteuser_transactions')
			->join('shop_orders', 'shop_orders.id', '=', 'shop_siteuser_transactions.shop_order_id')
			->join('shop_currencies', 'shop_currencies.id', '=', 'shop_siteuser_transactions.shop_currency_id')
			->where('shop_siteuser_transactions.siteuser_id', '=', $site_user_id)
			->where('shop_siteuser_transactions.type', '=', 1)
			->where('shop_siteuser_transactions.active', '=', 1)
			->where('shop_siteuser_transactions.datetime', '>', $date_from . ' 00:00:00')
			->where('shop_siteuser_transactions.datetime', '<=', $date_to . ' 23:59:59');

		$return = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$aResult = $queryBuilder->execute()->asAssoc()->result();
		$return .= '<affiliat_data>';

		$return .= '<affiliat_date_from>' . Core_Date::sql2date($date_from) . '</affiliat_date_from>';
		$return .= '<affiliat_date_to>' . Core_Date::sql2date($date_to) . '</affiliat_date_to>';

		foreach($aResult as $row)
		{
			// Получаем информацию о пользователе сайта
			$site_user_row = $Site_users->GetSiteUser($row['siteuser_id']);
			$current_date = explode(' ', $row['datetime']);
			$current_date = Core_Date::sql2date($current_date[0]);

			$innerLevel = $this->getAffiliatInnerLevelForUser($site_user_id, $row['siteuser_id']);

			$return .= '<affiliat>';
			$return .= '<affiliat_login>' . $site_user_row['site_users_login'] . '</affiliat_login>';
			$return .= '<affiliat_amount>' . $row['amount_base_currency'] . '</affiliat_amount>';
			$return .= '<affiliat_inner_level>' . $innerLevel . '</affiliat_inner_level>';
			$return .= '<affiliat_shops_id>' . $row['shop_id'] . '</affiliat_shops_id>';
			$return .= '<affiliat_currency>' . $row['name'] . '</affiliat_currency>';
			$return .= '<affiliat_date>' . $current_date . '</affiliat_date>';
			$return .= '</affiliat>';
		}
		$return .= '</affiliat_data>';

		$xsl = & singleton('xsl');
		echo $xsl->build($return, $xsl_name);
	}

	/**
	 * Формирование массива идентификаторов пользователей сайта, участвующих в программе партнерских отношений.
	 *
	 * @param point arr $users_id_array указатель на массив, в который будут возвращены данные
	 * @param bool $current_site_user_id идентификатор пользователя сайта, для которого требуется получить цепочку идентификаторов
	 */
	function getUsersID(&$users_id_array, $current_site_user_id)
	{
		$result = $this->GetAllUsersForAffiliate($current_site_user_id);

		$users_id_array[] = $current_site_user_id;

		if($result)
		{
			while($row = mysql_fetch_assoc($result))
			{
				$this->getUsersID($users_id_array, $row['site_users_id']);
			}
		}
	}

	function getAffiliatInnerLevelForUser($site_user_id, $affiliat_users_id, $innerLevel = 0)
	{
		$site_user_id = intval($site_user_id);
		$affiliat_users_id = intval($affiliat_users_id);

		if($site_user_id == $affiliat_users_id)
		{
			return 1;
		}
		else
		{
			$affiliat_users_res = $this->GetAllUsersForAffiliate($site_user_id);

			if($affiliat_users_res)
			{
				while($affiliat_users_row = mysql_fetch_assoc($affiliat_users_res))
				{
					return $innerLevel + $this->getAffiliatInnerLevelForUser($affiliat_users_row['site_users_id'], $affiliat_users_id, $innerLevel + 1);
				}
			}
		}
	}
}
