<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
*
* Класс модуля "warehouse".
*
* Файл: /modules/shop/warehouse.class.php
*
* @author Hostmake LLC
* @version 5.x
*/
class warehouse
{
	/**
	 * Кэш для метода GetWarehouse()
	 * @var array
	 */
	var $CacheGetWarehouse = array();

	/**
	 * Кэш для метода GetItemCountForWarehouse()
	 * @var array
	 */
	var $CacheGetItemCountForWarehouse = array();

	/**
	 * Кэш для метода GetAllWarehousesForShop()
	 * @var array
	 */
	var $CacheGetAllWarehousesForShop = array();

	function getArrayShopWarehouse($oShop_Warehouse)
	{
		return array(
			'shop_warehouse_id' => $oShop_Warehouse->id,
			'shop_shops_id' => $oShop_Warehouse->shop_id,
			'shop_warehouse_name' => $oShop_Warehouse->name,
			'shop_warehouse_activity' => $oShop_Warehouse->active,
			'shop_country_id' => $oShop_Warehouse->shop_country_id,
			'shop_location_id' => $oShop_Warehouse->shop_country_location_id,
			'shop_city_id' => $oShop_Warehouse->shop_country_location_city_id,
			'shop_city_area_id' => $oShop_Warehouse->shop_country_location_city_area_id,
			'shop_warehouse_address' => $oShop_Warehouse->address,
			'shop_warehouse_order' => $oShop_Warehouse->sorting,
			'shop_warehouse_default' => $oShop_Warehouse->default,
			'users_id' => $oShop_Warehouse->user_id
		);
	}

	function getArrayShopWarehouseItem($oShop_Warehouse_Item)
	{
		return array(
			'shop_warehouse_items_id' => $oShop_Warehouse_Item->id,
			'shop_warehouse_id' => $oShop_Warehouse_Item->shop_warehouse_id,
			'shop_items_catalog_item_id' => $oShop_Warehouse_Item->shop_item_id,
			'shop_warehouse_items_count' => $oShop_Warehouse_Item->count,
			'users_id' => $oShop_Warehouse_Item->user_id
		);
	}

	/**
	* Вставка/обновление склада
	* @param array $param массив параметров
	* - int $param['shop_warehouse_id'] идентификатор склада (для обновления)
	* - int $param['shop_shops_id'] идентификатор магазина
	* - str $param['shop_warehouse_name'] наименование склада
	* - int $param['shop_warehouse_activity'] флажок активности склада
	* - int $param['shop_country_id'] идентификатор страны
	* - int $param['shop_location_id'] идентификатор области
	* - int $param['shop_city_id'] идентификатор города
	* - int $param['shop_city_area_id'] идентификатор района
	* - str $param['shop_warehouse_address'] адрес склада
	* - int $param['users_id'] идентификатор пользователя центра администрирования для установки владельца записи (используется только если $param['shop_warehouse_id'] == 0  или не передан)
	* - int $param['shop_warehouse_order'] порядок сортировки склада
	* - int $param['shop_warehouse_default'] флаг "склад по умолчанию" (с этим параметром обязательно должен быть передан параметр $param['shop_shops_id'])
	*
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $param = array();
	*
	* $param['shop_warehouse_id'] = 1;
	* $param['shop_shops_id'] = 10;
	* $param['shop_warehouse_name'] = "Основной склад";
	* $param['shop_warehouse_activity'] = 1;
	* $param['shop_country_id'] = 1;
	* $param['shop_location_id'] = 1;
	* $param['shop_city_id'] = 1;
	* $param['shop_city_area_id'] = 1;
	* $param['shop_warehouse_address'] = "ул. Ленина 176";
	* $param['shop_warehouse_order'] = 10;
	* $param['shop_warehouse_default'] = 1;
	*
	* if(($new_warehouse_id = $warehouse->InsertWarehouse($param)) !== false)
	* {
	* 	echo "Склад с идентификатором $new_warehouse_id успешно добавлен!";
	* }
	* else
	* {
	* 	echo "Ошибка добавления склада!";
	* }
	*
	* ?>
	* </code>
	* @return mixed идентификатор вставленной/обновленной записи, либо false
	*/
	function InsertWarehouse($param)
	{
		if (!isset($param['shop_warehouse_id']) || !$param['shop_warehouse_id'])
		{
			$param['shop_warehouse_id'] = NULL;
		}

		$oShop_Warehouse = Core_Entity::factory('Shop_Warehouse', $param['shop_warehouse_id']);
		if (isset($param['shop_shops_id']))
		{
			$shop_shops_id = intval($param['shop_shops_id']);
			$oShop_Warehouse->shop_id = $shop_shops_id;

			// Удаляем кэш складов для этого сайта
			if (isset($this->CacheGetAllWarehousesForShop[$shop_shops_id]))
			{
				unset($this->CacheGetAllWarehousesForShop[$shop_shops_id]);
			}
		}

		isset($param['shop_warehouse_name']) && $oShop_Warehouse->name = $param['shop_warehouse_name'];

		if (isset($param['shop_warehouse_activity']))
		{
			$shop_warehouse_activity = intval($param['shop_warehouse_activity']);

			if(!$shop_warehouse_activity)
			{
				$shop_warehouse_id_check = intval($oShop_Warehouse->id);
				if($shop_warehouse_id_check)
				{
					// нам передали идентификатор склада
					if(($answer = $this->GetDefaultWarehouse(Core_Type_Conversion::toInt($param['shop_shops_id']))) !== FALSE && ($shop_warehouse_id_check == $answer['shop_warehouse_id']))
					{
						// невозможно сделать неактивным склад по умолчанию
						return FALSE;
					}
				}
			}

			$oShop_Warehouse->active = $shop_warehouse_activity;
		}

		isset($param['shop_country_id']) && $oShop_Warehouse->shop_country_id = intval($param['shop_country_id']);
		isset($param['shop_location_id']) && $oShop_Warehouse->shop_country_location_id = intval($param['shop_location_id']);
		isset($param['shop_city_id']) && $oShop_Warehouse->shop_country_location_city_id = intval($param['shop_city_id']);
		isset($param['shop_city_area_id']) && $oShop_Warehouse->shop_country_location_city_area_id = intval($param['shop_city_area_id']);
		isset($param['shop_warehouse_address']) && $oShop_Warehouse->address = $param['shop_warehouse_address'];
		isset($param['shop_warehouse_order']) && $oShop_Warehouse->sorting = intval($param['shop_warehouse_order']);

		if (isset($param['shop_warehouse_default']))
		{
			if (intval($param['shop_warehouse_default']))
			{
				// Склад нужно сделать складом по умолчанию
				// Проверяем, существует ли уже склад по умолчанию для этого магазина
				if(($answer = $this->GetDefaultWarehouse(Core_Type_Conversion::toInt($param['shop_shops_id']))) !== FALSE)
				{
					// существует, снимаем галочку "по умолчанию"
					Core_Entity::factory('Shop_Warehouse', $answer['shop_warehouse_id'])->default(0)->save();
				}

				// склад по умолчанию не может быть неактивным
				$oShop_Warehouse->active = 1;
			}
			else
			{
				if (!is_null($oShop_Warehouse->id))
				{
					// Проверяем, останется ли после снятия галочки склад, на который можно повесить статус "по умолчанию"
					$queryBuilder = Core_QueryBuilder::select()
						->from('shop_warehouses')
						->where('shop_id', '=', Core_Type_Conversion::toInt($param['shop_shops_id']))
						->where('id', '!=', intval($param['shop_warehouse_id']))
						->where('deleted', '=', 0);
					$aResult = $queryBuilder->execute()->asAssoc()->current();

					// другие склады обнаружены
					if(isset($aResult['id']) && $aResult['id'])
					{
						$oSame_Shop_Warehouse = Core_Entity::factory('Shop_Warehouse', $aResult['id']);
						$oSame_Shop_Warehouse->active = 1;
						$oSame_Shop_Warehouse->default = 1;
						$oSame_Shop_Warehouse->save();
					}
					else
					{
						// других складов не обнаружено - это последний, нельзя снимать флажок "по умолчанию" с последнего склада, выходим
						return FALSE;
					}
				}
			}
			$oShop_Warehouse->default = intval($param['shop_warehouse_default']);
		}

		if (is_null($oShop_Warehouse->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oShop_Warehouse->user_id = intval($param['users_id']);
		}

		$oShop_Warehouse->save();

		return $oShop_Warehouse->id;
	}

	/**
	* Вставка/обновление количества товара на складе.
	* Обновление ведется по идентификатору связи, либо по индентификатору склада и товара
	*
	* @param array $param массив параметров
	* - int $param['shop_warehouse_items_id'] идентификатор связи (для обновления)
	* - int $param['shop_warehouse_id'] идентификатор склада
	* - int $param['shop_items_catalog_item_id'] идентификатор товара
	* - float $param['shop_warehouse_items_count'] количество товара на складе
	* - int $param['users_id'] идентификатор пользователя центра администрирования для установки владельца записи (используется только если $param['shop_warehouse_items_id'] == 0 или не передан)
	*
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $param = array();
	* $param['shop_warehouse_id'] = 1;
	* $param['shop_items_catalog_item_id'] = 10;
	* $param['shop_warehouse_items_count'] = 100;
	*
	* if(($new_warehouse_items_id = $warehouse->InsertWarehouseItems($param)) !== false)
	* {
	* 	echo "Отстаток товара с идентификатором $new_warehouse_items_id успешно добавлен!";
	* }
	* else
	* {
	* 	echo "Ошибка добавления остатка!";
	* }
	* ?>
	* </code>
	* @return mixed идентификатор вставленной/обновленной записи, либо false
	*/
	function InsertWarehouseItems($param)
	{
		// Необходимо проверить, существует ли переданная нам пара товар/склад в данной таблице
		$row = $this->GetWarehouseItem(array('shop_items_catalog_item_id' => Core_Type_Conversion::toInt($param['shop_items_catalog_item_id']), 'shop_warehouse_id' => Core_Type_Conversion::toInt($param['shop_warehouse_id'])));

		if ($row)
		{
			$param['shop_warehouse_items_id'] = $row['shop_warehouse_items_id'];
		}

		if (!isset($param['shop_warehouse_items_id']) || !$param['shop_warehouse_items_id'])
		{
			$param['shop_warehouse_items_id'] = NULL;
		}

		$oShop_Warehouse_Item = Core_Entity::factory('Shop_Warehouse_Item', $param['shop_warehouse_items_id']);

		isset($param['shop_warehouse_id']) && $oShop_Warehouse_Item->shop_warehouse_id = intval($param['shop_warehouse_id']);

		if (isset($param['shop_items_catalog_item_id']))
		{
			if ($param['shop_items_catalog_item_id'] > 0)
			{
				$oShop_Warehouse_Item->shop_item_id = intval($param['shop_items_catalog_item_id']);
			}
			else
			{
				return FALSE;
			}
		}

		isset($param['shop_warehouse_items_count']) && $oShop_Warehouse_Item->count = floatval($param['shop_warehouse_items_count']);

		is_null($oShop_Warehouse_Item->id) && isset($param['users_id']) && $param['users_id'] && $oShop_Warehouse_Item->user_id = intval($param['users_id']);

		$oShop_Warehouse_Item->save();
		return $oShop_Warehouse_Item->id;
	}

	/**
	 * Получение информации о складе
	 *
	 * @param int $shop_warehouse_id идентификатор склада
	 *
	 * <code>
	 * <?php
	 * $warehouse = & singleton('warehouse');
	 *
	 * $shop_warehouse_id = 1;
	 *
	 * if(($warehouse_row = $warehouse->GetWarehouse($shop_warehouse_id)) !== false)
	 * {
	 * 	var_dump($warehouse_row);
	 * }
	 * else
	 * {
	 * 	echo "Ошибка получения данных о складе!";
	 * }
	 * ?>
	 * </code>
	 * @return mixed массив данных, либо false
	 */
	function GetWarehouse($shop_warehouse_id)
	{
		$shop_warehouse_id = intval($shop_warehouse_id);

		if (isset($this->CacheGetWarehouse[$shop_warehouse_id]))
		{
			return $this->CacheGetWarehouse[$shop_warehouse_id];
		}

		$oShop_Warehouse = Core_Entity::factory('Shop_Warehouse')->find($shop_warehouse_id);

		return $this->CacheGetWarehouse[$shop_warehouse_id] = !is_null($oShop_Warehouse->id)
			? $this->getArrayShopWarehouse($oShop_Warehouse)
			: FALSE;
	}

	/**
	* Получение информации об остатке товара на складе, получение возможно
	* по shop_warehouse_items_id или паре значений идентификатор склада и идентификатор товара
	*
	* @param int $shop_warehouse_id идентификатор склада
	* @param array $param массив параметров
	* - int $param['shop_warehouse_items_id'] идентификатор связи
	* - int $param['shop_warehouse_id'] идентификатор склада
	* - int $param['shop_items_catalog_item_id'] идентификатор товара
	*
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $param = array();
	* $param['shop_warehouse_items_id'] = 1;
	*
	* // либо, используя составной ключ, можно задать идентификаторы склада и товара
	* $param['shop_warehouse_id'] = 10;
	* $param['shop_items_catalog_item_id'] = 20;
	*
	* if(($warehouse_item_row = $warehouse->GetWarehouseItem($param)) !== false)
	* {
	* 	var_dump($warehouse_item_row);
	* }
	* else
	* {
	* 	echo "Ошибка получения данных об остатке!";
	* }
	* ?>
	* </code>
	* @return mixed массив данных, либо false
	*/
	function GetWarehouseItem($param)
	{
		$oShop_Warehouse_Item = Core_Entity::factory('Shop_Warehouse_Item');
		if(($shop_warehouse_items_id = Core_Type_Conversion::toInt($param['shop_warehouse_items_id'])) > 0)
		{
			$oShop_Warehouse_Item = $oShop_Warehouse_Item->find($shop_warehouse_items_id);
			if (is_null($oShop_Warehouse_Item->id))
			{
				return FALSE;
			}
		}
		elseif(($shop_warehouse_id = Core_Type_Conversion::toInt($param['shop_warehouse_id'])) > 0
		&& ($shop_items_catalog_item_id = Core_Type_Conversion::toInt($param['shop_items_catalog_item_id'])) > 0)
		{
			$oShop_Warehouse_Item
				->queryBuilder()
				->where('shop_warehouse_id', '=', $shop_warehouse_id)
				->where('shop_item_id', '=', $shop_items_catalog_item_id);

			$aShop_Warehouse_Items = $oShop_Warehouse_Item->findAll();
			if (isset($aShop_Warehouse_Items[0]) && !is_null($aShop_Warehouse_Items[0]->id))
			{
				$oShop_Warehouse_Item = $aShop_Warehouse_Items[0];
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}

		return $this->getArrayShopWarehouseItem($oShop_Warehouse_Item);
	}

	/**
	* Удаление информации о связи товара и склада
	*
	* @param int $shop_warehouse_items_id идентификатор связи из таблицы `shop_warehouse_items_table`
	*
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_warehouse_items_id = 1;
	*
	* if($warehouse->DeleteWarehouseItem($shop_warehouse_items_id))
	* {
	* 	echo "Информация успешно удалена!";
	* }
	* else
	* {
	* 	echo "Ошибка удаления информации!";
	* }
	* ?>
	* </code>
	*
	* @return resource
	*/
	function DeleteWarehouseItem($shop_warehouse_items_id)
	{
		$shop_warehouse_items_id = intval($shop_warehouse_items_id);
		Core_Entity::factory('Shop_Warehouse_Item', $shop_warehouse_items_id)->delete();
		return TRUE;
	}

	/**
	 * Получение всех товаров склада
	 *
	 * @param int $shop_warehouse_id идентификатор склада
	 * <code>
	 * <?php
	 * $warehouse = & singleton('warehouse');
	 *
	 * $shop_warehouse_id = 1;
	 *
	 * if(($warehouse_item_res = $warehouse->GetAllWarehouseItems($param)) !== false)
	 * {
	 * 	while($warehouse_item_row = mysql_fetch_assoc($warehouse_item_res))
	 * 	{
	 * 		var_dump($warehouse_item_row);
	 * 	}
	 * }
	 * else
	 * {
	 * 	echo "Данные отсутствуют";
	 * }
	 * ?>
	 * </code>
	 * @return mixed resource или false
	 */
	function GetAllWarehouseItems($shop_warehouse_id)
	{
		$shop_warehouse_id = intval($shop_warehouse_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_warehouse_items_id'),
				array('shop_warehouse_id', 'shop_warehouse_id'),
				array('shop_item_id', 'shop_items_catalog_item_id'),
				array('count', 'shop_warehouse_items_count'),
				array('user_id', 'users_id')
			)
			->from('shop_warehouse_items')
			->where('shop_warehouse_id', '=', $shop_warehouse_id);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение всех складов, которым принадлежит товар
	 *
	 * @param int $shop_items_catalog_item_id идентификатор товара
	 * <code>
	 * <?php
	 * $warehouse = & singleton('warehouse');
	 *
	 * $shop_items_catalog_item_id = 101;
	 *
	 * if(($item_warehouses_item_res = $warehouse->GetAllItemWarehouses($shop_items_catalog_item_id)) !== false)
	 * {
	 * 	while($item_warehouses_item_row = mysql_fetch_assoc($item_warehouses_item_res))
	 * 	{
	 * 		var_dump($item_warehouses_item_row);
	 * 	}
	 * }
	 * else
	 * {
	 * 	echo "Данные отсутствуют";
	 * }
	 * ?>
	 * </code>
	 * @return mixed resource или false
	 */
	function GetAllItemWarehouses($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'shop_warehouse_items_id'),
				array('shop_warehouse_id', 'shop_warehouse_id'),
				array('shop_item_id', 'shop_items_catalog_item_id'),
				array('count', 'shop_warehouse_items_count'),
				array('user_id', 'users_id')
			)
			->from('shop_warehouse_items')
			->where('shop_item_id', '=', $shop_items_catalog_item_id);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	* Удаление информации о складе
	*
	* @param int $shop_warehouse_id идентификатор склада
	*
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_warehouse_id = 10;
	*
	* if($warehouse->DeleteWarehouse($shop_warehouse_id))
	* {
	* 	echo "Информация успешно удалена!";
	* }
	* else
	* {
	* 	echo "Ошибка удаления информации!";
	* }
	* ?>
	* </code>
	* @return resource
	*/
	function DeleteWarehouse($shop_warehouse_id)
	{
		$shop_warehouse_id = intval($shop_warehouse_id);
		Core_Entity::factory('Shop_Warehouse', $shop_warehouse_id)->markDeleted();
		return TRUE;
	}

	/**
	* Получение всех складов магазина
	*
	* @param int $shop_shops_id идентификатор магазина
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_shops_id = 1;
	*
	* if(($aWarehouses = $warehouse->GetAllWarehousesForShop($shop_shops_id)) !== false)
	* {
	*   foreach ($aWarehouses as $shop_warehouses_row)
	* 	{
	* 		var_dump($shop_warehouses_row);
	* 	}
	* }
	* else
	* {
	* 	echo "Данные отсутствуют";
	* }
	* ?>
	* </code>
	* @return mixed массив или false
	*/
	function GetAllWarehousesForShop($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		if (isset($this->CacheGetAllWarehousesForShop[$shop_shops_id]))
		{
			return $this->CacheGetAllWarehousesForShop[$shop_shops_id];
		}

		$oShop_Warehouse = Core_Entity::factory('Shop', $shop_shops_id)->Shop_Warehouses;
		$oShop_Warehouse
			->queryBuilder()
			->where('shop_id', '=', $shop_shops_id)
			->where('active', '=', 1)
			->orderBy('sorting');

		$aShop_Warehouses = $oShop_Warehouse->findAll();
		if (count($aShop_Warehouses) > 0)
		{
			foreach($aShop_Warehouses as $oShop_Warehouse)
			{
				$return[$oShop_Warehouse->id] = $this->getArrayShopWarehouse($oShop_Warehouse);
			}
		}
		else
		{
			$return = FALSE;
		}

		$this->CacheGetAllWarehousesForShop[$shop_shops_id] = $return;

		return $return;
	}

	function FillItemCountForWarehouse($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		if ($shop_items_catalog_item_id > 0)
		{
			$this->CacheGetItemCountForWarehouse[$shop_items_catalog_item_id] = array();

			$queryBuilder = Core_QueryBuilder::select()
				->from('shop_warehouse_items')
				->where('shop_item_id', '=', $shop_items_catalog_item_id);

			$aResult = $queryBuilder->execute()->asAssoc()->result();

			foreach($aResult as $row)
			{
				$this->CacheGetItemCountForWarehouse[$row['shop_item_id']][$row['shop_warehouse_id']] = $row['count'];
			}
		}
	}

	/**
	* Получение количества определенного товара на определенном складе
	*
	* @param int $shop_warehouse_id идентификатор склада
	* @param int $shop_items_catalog_item_id идентификатор товара
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_warehouse_id = 1;
	* $shop_items_catalog_item_id = 10;
	*
	* if(($item_count = $warehouse->GetItemCountForWarehouse($shop_warehouse_id, $shop_items_catalog_item_id)) > 0)
	* {
	* 	echo "Количество товара на складе: $item_count";
	* }
	* else
	* {
	* 	echo "Товар на складе отсутствует";
	* }
	* ?>
	* </code>
	* @return int количество товара на складе, либо 0, если товара на складе нет
	*/
	function GetItemCountForWarehouse($shop_warehouse_id, $shop_items_catalog_item_id)
	{
		$shop_warehouse_id = intval($shop_warehouse_id);
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		$return = 0;

		if ($shop_warehouse_id > 0 && $shop_items_catalog_item_id > 0)
		{
			// Если кэша в пямяти с остатком на складах для товара нет
			if (!isset($this->CacheGetItemCountForWarehouse[$shop_items_catalog_item_id]))
			{
				$this->FillItemCountForWarehouse($shop_items_catalog_item_id);
			}

			// Кэширование в памяти
			if (isset($this->CacheGetItemCountForWarehouse[$shop_items_catalog_item_id]))
			{
				if (isset($this->CacheGetItemCountForWarehouse[$shop_items_catalog_item_id][$shop_warehouse_id]))
				{
					return $this->CacheGetItemCountForWarehouse[$shop_items_catalog_item_id][$shop_warehouse_id];
				}
				else
				{
					return 0;
				}
			}

			$queryBuilder = Core_QueryBuilder::select()
				->from('shop_warehouse_items')
				->where('shop_warehouse_id', '=', $shop_warehouse_id)
				->where('shop_item_id', '=', $shop_items_catalog_item_id);

			$aResult = $queryBuilder->execute()->asAssoc()->current();

			if (isset($aResult['count']))
			{
				$return = $aResult['count'];
			}
		}

		return $return;
	}

	/**
	* Получение количества определенного товара на всех складах
	*
	* @param int $shop_items_catalog_item_id идентификатор товара
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_items_catalog_item_id = 1;
	*
	* if(($item_count = $warehouse->GetItemCountForAllWarehouses($shop_items_catalog_item_id)) > 0)
	* {
	* 	echo "Количество товара на всех складах: $item_count";
	* }
	* else
	* {
	* 	echo "Товар отсутствует на всех складах";
	* }
	* ?>
	* </code>
	* @return int количество товара на складах, либо 0, если товара на складах нет
	*/
	function GetItemCountForAllWarehouses($shop_items_catalog_item_id)
	{
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);

		$queryBuilder = Core_QueryBuilder::select(array('SUM(count)', 'counter'))
			->from('shop_warehouse_items')
			->leftJoin('shop_warehouses', 'shop_warehouses.id', '=', 'shop_warehouse_items.shop_warehouse_id')
			->where('shop_item_id', '=', $shop_items_catalog_item_id)
			->where('active', '=', 1)
			->where('deleted', '=', 0)
			->groupBy('shop_item_id');

		$aResult = $queryBuilder->execute()->asAssoc()->current();

		return isset($aResult['counter']) ? $aResult['counter'] : 0.0;
	}

	/**
	* Получение склада по умолчанию для магазина
	*
	* @param int $shop_shops_id идентификатор магазина
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_shops_id = 1;
	*
	* if(($default_warehouse_row = $warehouse->GetDefaultWarehouse($shop_shops_id)) !== false)
	* {
	* 	var_dump($default_warehouse_row);
	* }
	* else
	* {
	* 	echo "Склад \"По умолчанию\" не обнаружен!";
	* }
	* ?>
	* </code>
	* @return mixed массив с данными, либо false
	*/
	function GetDefaultWarehouse($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);
		$oShop_Warehouse = Core_Entity::factory('Shop',$shop_shops_id)->Shop_Warehouses->getDefault();
		return !is_null($oShop_Warehouse) ? $this->getArrayShopWarehouse($oShop_Warehouse) : FALSE;
	}

	/**
	* Списание с определенного склада определенное количество определенного товара
	*
	* @param int $shop_shops_id идентификатор магазина
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_warehouse_id = 1;
	* $shop_items_catalog_item_id = 10;
	* $shop_warehouse_items_count = 100;
	*
	* if($warehouse->AcceptTransaction($shop_warehouse_id, $shop_items_catalog_item_id, $shop_warehouse_items_count))
	* {
	* 	echo "Списание товара прошло успешно";
	* }
	* else
	* {
	* 	echo "Ошибка списания товара!";
	* }
	* ?>
	* </code>
	* @return boolean результат выполнения операции
	*/
	function AcceptTransaction($shop_warehouse_id, $shop_items_catalog_item_id, $shop_warehouse_items_count)
	{
		$shop_warehouse_id = intval($shop_warehouse_id);
		$shop_items_catalog_item_id = intval($shop_items_catalog_item_id);
		$shop_warehouse_items_count = floatval($shop_warehouse_items_count);

		$oShop_Warehouse_Item = Core_Entity::factory('Shop_Warehouse', $shop_warehouse_id)->Shop_Warehouse_Items->getByShopItemId($shop_items_catalog_item_id);

		if (!is_null($oShop_Warehouse_Item))
		{
			$oShop_Warehouse_Item->count += $shop_warehouse_items_count;
			$oShop_Warehouse_Item->save();

			return TRUE;
		}

		return FALSE;
	}

	/**
	* Формирование XML для всех складов магазина
	*
	* @param int $shop_shops_id идентификатор магазина
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_shops_id = 1;
	*
	* if(($warehouses_xml = $warehouse->GetWarehousesXml($shop_shops_id)) != '')
	* {
	* 	echo $warehouses_xml;
	* }
	* else
	* {
	* 	echo "Нет XML-данных о складах!";
	* }
	* ?>
	* </code>
	* @return str строка XML в случае успеха, либо пустая строка
	*/
	function GetWarehousesXml($shop_shops_id)
	{
		$shop_shops_id = intval($shop_shops_id);

		// Получаем информацию обо всех складах магазина
		$aWarehouses = $this->GetAllWarehousesForShop($shop_shops_id);

		if($aWarehouses)
		{
			$return = "<warehouses>\n";
			foreach ($aWarehouses as $warehouses_row)
			{
				$return .= $this->GetWarehouseXml($warehouses_row['shop_warehouse_id']);
			}
			$return .= "</warehouses>\n";
		}
		else
		{
			$return = '';
		}

		return $return;
	}

	/**
	* Формирование XML для конкретного склада магазина
	*
	* @param int $shop_warehouse_id идентификатор склада
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_warehouse_id = 1;
	*
	* if(($warehouse_xml = $warehouse->GetWarehouseXml($shop_warehouse_id)) != '')
	* {
	* 	echo $warehouse_xml;
	* }
	* else
	* {
	* 	echo "Нет XML-данных о складе!";
	* }
	* ?>
	* </code>
	* @return str строка XML в случае успеха, либо пустая строка
	*/
	function GetWarehouseXml($shop_warehouse_id)
	{
		$shop_warehouse_id = intval($shop_warehouse_id);
		$warehouse_row = $this->GetWarehouse($shop_warehouse_id);

		$shop = & singleton('shop');
		$return = '';
		if($warehouse_row)
		{
			$return .= '<warehouse id="' . $warehouse_row['shop_warehouse_id'] . '">'."\n";
			$return .= '<shop_warehouse_name>' . str_for_xml($warehouse_row['shop_warehouse_name']) . '</shop_warehouse_name>' . "\n";
			$return .= '<shop_warehouse_activity>' . $warehouse_row['shop_warehouse_activity'] . '</shop_warehouse_activity>' . "\n";
			$return .= '<shop_warehouse_address>' . str_for_xml($warehouse_row['shop_warehouse_address']) . '</shop_warehouse_address>' . "\n";
			$return .= '<shop_warehouse_order>' . $warehouse_row['shop_warehouse_order'] . '</shop_warehouse_order>' . "\n";
			$return .= '<shop_warehouse_default>' . $warehouse_row['shop_warehouse_default'] . '</shop_warehouse_default>' . "\n";

			$country_row = $shop->GetCountry($warehouse_row['shop_country_id']);

			if($country_row)
			{
				$return .= '<shop_country_id>' . $country_row['shop_country_id'] . '</shop_country_id>' . "\n";
				$return .= '<shop_country_name>' . str_for_xml($country_row['shop_country_name']) . '</shop_country_name>' . "\n";
			}

			$location_row = $shop->GetLocation($warehouse_row['shop_location_id']);

			if($location_row)
			{
				$return .= '<shop_location_id>' . $location_row['shop_location_id'] . '</shop_location_id>' . "\n";
				$return .= '<shop_location_name>' . str_for_xml($location_row['shop_location_name']) . '</shop_location_name>' . "\n";
			}

			$city_row = $shop->GetCity($warehouse_row['shop_city_id']);

			if($city_row)
			{
				$return .= '<shop_city_id>' . $city_row['shop_city_id'] . '</shop_city_id>' . "\n";
				$return .= '<shop_city_name>' . str_for_xml($city_row['shop_city_name']) . '</shop_city_name>' . "\n";
			}

			$city_area_row = $shop->GetCityArea($warehouse_row['shop_city_area_id']);

			if($city_area_row)
			{
				$return .= '<shop_city_area_id>' . $city_area_row['shop_city_area_id'] . '</shop_city_area_id>' . "\n";
				$return .= '<shop_city_area_name>' . str_for_xml($city_area_row['shop_city_area_name']) . '</shop_city_area_name>' . "\n";
			}

			$return .= '</warehouse>' . "\n";
		}

		return $return;
	}

	/**
	* Копирование склада
	*
	* @param int $shop_warehouse_id идентификатор склада
	* @param int $shop_shops_id идентификатор магазина
	* <code>
	* <?php
	* $warehouse = & singleton('warehouse');
	*
	* $shop_warehouse_id = 1;
	* $shop_shops_id = 10;
	*
	* if(($warehouse_copy_id = $warehouse->CopyWarehouse($shop_warehouse_id, $shop_shops_id)) !== false)
	* {
	* 	echo "Копирование прошло успешно, идентификатор новго склада - $warehouse_copy_id";
	* }
	* else
	* {
	* 	echo "Ошибка копирования склада!";
	* }
	* ?>
	* </code>
	* @return mixed идентификатор нового склада, либо false
	*/
	function CopyWarehouse($shop_warehouse_id, $shop_shops_id)
	{
		$shop_warehouse_id = intval($shop_warehouse_id);
		$shop_shops_id = intval($shop_shops_id);

		$oShop_Warehouse = Core_Entity::factory('Shop_Warehouse')->find($shop_warehouse_id);
		if (!is_null($oShop_Warehouse->id))
		{
			$oNew_Shop_Warehouse = $oShop_Warehouse->copy();
			$oNew_Shop_Warehouse->shop_id = $shop_shops_id;
			$oNew_Shop_Warehouse->save();
			return TRUE;
		}

		return FALSE;
	}
}
