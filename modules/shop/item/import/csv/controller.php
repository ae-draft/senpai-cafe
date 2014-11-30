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

class Shop_Item_Import_Csv_Controller extends Core_Servant_Properties
{
	// Массивы для хранения идентификаторов вставленных/обновленных сущностей
	/**
	 * Array of inserted groups
	 * @var array
	 */
	protected $_aInsertedGroups = array();

	/**
	 * Array of property values
	 * @var array
	 */
	protected $_aClearedPropertyValues = array();

	/**
	 * Array of updated groups
	 * @var array
	 */
	protected $_aUpdatedGroups = array();

	/**
	 * Array of inserted items
	 * @var array
	 */
	protected $_aInsertedItems = array();

	/**
	 * Array of updated items
	 * @var array
	 */
	protected $_aUpdatedItems = array();

	/**
	 * Get inserted items count
	 * @return int
	 */
	public function getInsertedItemsCount()
	{
		return $this->_InsertedItemsCount;
	}

	/**
	 * Get inserted groups count
	 * @return int
	 */
	public function getInsertedGroupsCount()
	{
		return $this->_InsertedGroupsCount;
	}

	/**
	 * Get updated items count
	 * @return int
	 */
	public function getUpdatedItemsCount()
	{
		return $this->_UpdatedItemsCount;
	}

	/**
	 * Get updated groups count
	 * @return int
	 */
	public function getUpdatedGroupsCount()
	{
		return $this->_UpdatedGroupsCount;
	}

	/**
	 * ID of current shop
	 * @var int
	 */
	protected $_iCurrentShopId = 0;

	/**
	 * ID of current group
	 * @var int
	 */
	protected $_iCurrentGroupId = 0;

	// Текущие сущности
	/**
	 * Current shop
	 * @var Shop_Model
	 */
	protected $_oCurrentShop;

	/**
	 * Current group
	 * @var Shop_Group_Model
	 */
	protected $_oCurrentGroup;

	/**
	 * Current item
	 * @var Shop_Item_Model
	 */
	protected $_oCurrentItem;

	/**
	 * Current order
	 * @var Shop_Item_Model
	 */
	protected $_oCurrentOrder;

	/**
	 * Current order item
	 * @var Shop_Order_Item_Model
	 */
	protected $_oCurrentOrderItem;

	/**
	 * Current tags
	 * @var string
	 */
	protected $_sCurrentTags;

	/**
	 * Mark of associated item
	 * Артикул родительского товара - признак того, что данный товар сопутствует товару с данным артикулом
	 * @var string
	 */
	protected $_sAssociatedItemMark;

	/**
	 * Current digital item
	 * Текущий электронный товар
	 * @var Shop_Item_Digital_Model
	 */
	protected $_oCurrentShopEItem;

	/**
	 * Current special price
	 * Текущая специальная цена для товара
	 * @var Shop_Specialprice_Model
	 */
	protected $_oCurrentShopSpecialPrice;

	/**
	 * List of external prices
	 * Вспомогательные массивы данных
	 * @var array
	 */
	protected $_aExternalPrices = array();

	/**
	 * List of warehouses
	 * @var array
	 */
	protected $_aWarehouses = array();

	/**
	 * List of small parts of external properties
	 * @var array
	 */
	protected $_aExternalPropertiesSmall = array();

	/**
	 * List of external properties
	 * @var array
	 */
	protected $_aExternalProperties = array();

	/**
	 * List of additional group
	 * @var array
	 */
	protected $_aAdditionalGroups = array();

	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		// Кодировка импорта
		'encoding',
		// Файл импорта
		'file',
		// Позиция в файле импорта
		'seek',
		// Ограничение импорта по времени
		'time',
		// Ограничение импорта по количеству
		'step',
		// Настройка CSV: разделитель
		'separator',
		// Настройка CSV: ограничитель
		'limiter',
		// Настройка CSV: первая строка - название полей
		'firstlineheader',
		// Настройка CSV: массив соответствий полей CSV сущностям системы HostCMS
		'csv_fields',
		// Путь к картинкам
		'imagesPath',
		// Действие с существующими товарами:
		// 0 - удалить содержимое магазина до импорта
		// 1 - обновить существующие товары
		// 2 - не обновлять существующие товары
		'importAction',
		// Флаг, указывающий, включена ли индексация
		'searchIndexation',
		'deleteImage'
	);

	/**
	 * Count of inserted items
	 * @var int
	 */
	protected $_InsertedItemsCount;

	/**
	 * Count of updated items
	 * @var int
	 */
	protected $_UpdatedItemsCount;

	/**
	 * Count of inserted groups
	 * @var int
	 */
	protected $_InsertedGroupsCount;

	/**
	 * Count of updated groups
	 * @var int
	 */
	protected $_UpdatedGroupsCount;

	/**
	 * Path of the big image
	 * @var string
	 */
	protected $_sBigImageFile = '';

	/**
	 * Path of the small image
	 * @var string
	 */
	protected $_sSmallImageFile = '';

	/**
	 * Increment inserted groups
	 * @param int $iGroupId group ID
	 * @return self
	 */
	protected function _incInsertedGroups($iGroupId)
	{
		if (!in_array($iGroupId, $this->_aInsertedGroups))
		{
			$this->_aInsertedGroups[] = $iGroupId;
			$this->_InsertedGroupsCount++;
		}
		return $this;
	}

	/**
	 * Increment updated groups
	 * @param int $iGroupId group ID
	 * @return self
	 */
	protected function _incUpdatedGroups($iGroupId)
	{
		if (!in_array($iGroupId, $this->_aUpdatedGroups))
		{
			$this->_aUpdatedGroups[] = $iGroupId;
			$this->_UpdatedGroupsCount++;
		}
		return $this;
	}

	/**
	 * Increment inserted items
	 * @param int $iItemId item ID
	 * @return self
	 */
	protected function _incInsertedItems($iItemId)
	{
		if (!in_array($iItemId, $this->_aInsertedItems))
		{
			$this->_aInsertedItems[] = $iItemId;
			$this->_InsertedItemsCount++;
		}
		return $this;
	}

	/**
	 * Increment updated items
	 * @param int $iItemId item ID
	 * @return self
	 */
	protected function _incUpdatedItems($iItemId)
	{
		if (!in_array($iItemId, $this->_aUpdatedItems))
		{
			$this->_aUpdatedItems[] = $iItemId;
			$this->_UpdatedItemsCount++;
		}
		return $this;
	}

	/**
	 * Initialization
	 * @return self
	 */
	protected function init()
	{
		$this->_oCurrentShop = Core_Entity::factory('Shop')->find($this->_iCurrentShopId);

		// Инициализация текущей группы товаров
		$this->_oCurrentGroup = Core_Entity::factory('Shop_Group', $this->_iCurrentGroupId);
		$this->_oCurrentGroup->shop_id = $this->_oCurrentShop->id;

		// Инициализация текущего товара
		$this->_oCurrentItem = Core_Entity::factory('Shop_Item');
		$this->_oCurrentItem->shop_group_id = intval($this->_oCurrentGroup->id);

		// Инициализация текущего электронного товара
		$this->_oCurrentShopEItem = Core_Entity::factory('Shop_Item_Digital');

		// Инициализация текущей специальной цены для товара
		$this->_oCurrentShopSpecialPrice = Core_Entity::factory('Shop_Specialprice');

		$this->_oCurrentOrder = NULL;
		$this->_oCurrentOrderItem = NULL;

		return $this;
	}

	/**
	 * Constructor.
	 * @param int $iCurrentShopId shop ID
	 * @param int $iCurrentGroupId current group ID
	 */
	public function __construct($iCurrentShopId, $iCurrentGroupId = 0)
	{
		parent::__construct();

		$this->_iCurrentShopId = $iCurrentShopId;
		$this->_iCurrentGroupId = $iCurrentGroupId;

		$this->init();

		// Единожды в конструкторе, чтобы после __wakeup() не обнулялось
		$this->_InsertedItemsCount = 0;
		$this->_UpdatedItemsCount = 0;
		$this->_InsertedGroupsCount = 0;
		$this->_UpdatedGroupsCount = 0;
	}

	/**
	 * Save group
	 * @param Shop_Group_Model $oShop_Group group
	 * @return Shop_Group
	 */
	protected function _doSaveGroup(Shop_Group_Model $oShop_Group)
	{
		is_null($oShop_Group->path) && $oShop_Group->path = '';
		$this->_incInsertedGroups($oShop_Group->save()->id);
		return $oShop_Group;
	}

	/**
	* Импорт CSV
	* @hostcms-event Shop_Item_Import_Cml_Controller.onBeforeFindByMarking
	* @hostcms-event Shop_Item_Import_Cml_Controller.onAfterFindByMarking
	*/
	public function import()
	{
		if ($this->importAction == 0)
		{
			Core_QueryBuilder::update('shop_groups')
				->set('deleted', 1)
				->where('shop_id', '=', $this->_oCurrentShop->id)
				->execute();

			Core_QueryBuilder::update('shop_items')
				->set('deleted', 1)
				->where('shop_id', '=', $this->_oCurrentShop->id)
				->execute();
		}

		$fInputFile = fopen($this->file, 'rb');

		if ($fInputFile === FALSE) throw new Core_Exception("");

		fseek($fInputFile, $this->seek);

		$iCounter = 0;

		$timeout = Core::getmicrotime();

		while((Core::getmicrotime() - $timeout + 3 < $this->time)
			&& $iCounter < $this->step
			&& ($aCsvLine = $this->getCSVLine($fInputFile)))
		{
			if (count($aCsvLine) == 1
			&& (is_null($aCsvLine[0])
			|| $aCsvLine[0] == ""))
			{
				continue;
			}

			foreach($aCsvLine as $iKey => $sData)
			{
				if (!isset($this->csv_fields[$iKey]))
				{
					continue;
				}

				switch($this->csv_fields[$iKey])
				{
					//=================ЗАКАЗЫ=================//
					case 'order_guid':
						if (strval($sData))
						{
							$this->_oCurrentOrder = $this->_oCurrentShop->Shop_Orders->getByGUID($sData, FALSE);

							if(is_null($this->_oCurrentOrder))
							{
								$this->_oCurrentOrder = Core_Entity::factory('Shop_Order');
								$this->_oCurrentOrder->guid = $sData;
							}
						}
					break;
					case 'order_invoice':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->invoice = $sData;
						}
					break;
					case 'order_shop_country_id':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$oShop_Country = Core_Entity::factory('Shop_Country')->getByName($sData);

							if(!is_null($oShop_Country))
							{
								$this->_oCurrentOrder->shop_country_id = $oShop_Country->id;
							}
						}
					break;
					case 'order_shop_country_location_id':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$oShop_Country_Location = Core_Entity::factory('Shop_Country', $this->_oCurrentOrder->shop_country_id)->Shop_Country_Locations->getByName($sData);

							if(!is_null($oShop_Country_Location))
							{
								$this->_oCurrentOrder->shop_country_location_id = $oShop_Country_Location->id;
							}
						}
					break;
					case 'order_shop_country_location_city_id':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$oShop_Country_Location_City = Core_Entity::factory('Shop_Country_Location', $this->_oCurrentOrder->shop_country_location_id)->Shop_Country_Location_Cities->getByName($sData);

							if(!is_null($oShop_Country_Location_City))
							{
								$this->_oCurrentOrder->shop_country_location_city_id = $oShop_Country_Location_City->id;
							}
						}
					break;
					case 'order_shop_country_location_city_area_id':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$oShop_Country_Location_City_Area = Core_Entity::factory('Shop_Country_Location_City', $this->_oCurrentOrder->shop_country_location_city_id)->Shop_Country_Location_City_Areas->getByName($sData);

							if(!is_null($oShop_Country_Location_City_Area))
							{
								$this->_oCurrentOrder->shop_country_location_city_area_id = $oShop_Country_Location_City_Area->id;
							}
						}
					break;
					case 'order_name':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->name = $sData;
						}
					break;
					case 'order_surname':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->surname = $sData;
						}
					break;
					case 'order_patronymic':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->patronymic = $sData;
						}
					break;
					case 'order_email':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->email = $sData;
						}
					break;
					case 'order_acceptance_report':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->acceptance_report = $sData;
						}
					break;
					case 'order_vat_invoice':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->vat_invoice = $sData;
						}
					break;
					case 'order_company':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->company = $sData;
						}
					break;
					case 'order_tin':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->tin = $sData;
						}
					break;
					case 'order_kpp':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->kpp = $sData;
						}
					break;
					case 'order_phone':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->phone = $sData;
						}
					break;
					case 'order_fax':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->fax = $sData;
						}
					break;
					case 'order_address':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->address = $sData;
						}
					break;
					case 'order_shop_order_status_id':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$oShop_Order_Status = Core_Entity::factory('Shop_Order_Status')->getByName($sData);
							if(!is_null($oShop_Order_Status))
							{
								$this->_oCurrentOrder->shop_order_status_id = $oShop_Order_Status->id;
							}
						}
					break;
					case 'order_shop_currency_id':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$oShop_Currency = Core_Entity::factory('Shop_Currency')->getByName($sData);
							if(!is_null($oShop_Currency))
							{
								$this->_oCurrentOrder->shop_currency_id = $oShop_Currency->id;
							}
						}
					break;
					case 'order_shop_payment_system_id':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$oShop_Payment_System = $this->_oCurrentShop->Shop_Payment_Systems->getById($sData);
							if(!is_null($oShop_Payment_System))
							{
								$this->_oCurrentOrder->shop_payment_system_id = $oShop_Payment_System->id;
							}
						}
					break;
					case 'order_datetime':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sData))
							{
								$this->_oCurrentOrder->datetime = $sData;
							}
							else
							{
								$this->_oCurrentOrder->datetime = Core_Date::datetime2sql($sData);
							}
						}
					break;
					case 'order_paid':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->paid = ((bool)$sData)?1:0;
						}
					break;
					case 'order_payment_datetime':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sData))
							{
								$this->_oCurrentOrder->payment_datetime = $sData;
							}
							else
							{
								$this->_oCurrentOrder->payment_datetime = Core_Date::datetime2sql($sData);
							}
						}
					break;
					case 'order_description':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->description = $sData;
						}
					break;
					case 'order_system_information':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->system_information = $sData;
						}
					break;
					case 'order_canceled':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->canceled = ((bool)$sData)?1:0;
						}
					break;
					case 'order_status_datetime':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sData))
							{
								$this->_oCurrentOrder->status_datetime = $sData;
							}
							else
							{
								$this->_oCurrentOrder->status_datetime = Core_Date::datetime2sql($sData);
							}
						}
					break;
					case 'order_delivery_information':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrder->delivery_information = $sData;
						}
					break;

					//=======================================//

					//==============    order items    ==============//

					case 'order_item_marking':
						if (strval($sData) && !is_null($this->_oCurrentOrder))
						{
							$this->_oCurrentOrderItem = $this->_oCurrentOrder->Shop_Order_Items->getBymarking($sData, FALSE);

							if(is_null($this->_oCurrentOrderItem))
							{
								$this->_oCurrentOrderItem = Core_Entity::factory('Shop_Order_Item');
								$this->_oCurrentOrderItem->marking = $sData;
							}
						}
					break;
					case 'order_item_name':
						if (strval($sData) && !is_null($this->_oCurrentOrderItem))
						{
							$this->_oCurrentOrderItem->name = $sData;
						}
					break;
					case 'order_item_quantity':
						if (strval($sData) && !is_null($this->_oCurrentOrderItem))
						{
							$this->_oCurrentOrderItem->quantity = $sData;
						}
					break;
					case 'order_item_price':
						if (strval($sData) && !is_null($this->_oCurrentOrderItem))
						{
							$this->_oCurrentOrderItem->price = $sData;
						}
					break;
					case 'order_item_rate':
						if (strval($sData) && !is_null($this->_oCurrentOrderItem))
						{
							$this->_oCurrentOrderItem->rate = $sData;
						}
					break;
					case 'order_item_type':
						if (strval($sData) && !is_null($this->_oCurrentOrderItem))
						{
							$this->_oCurrentOrderItem->type = $sData;
						}
					break;

					//=======================================//

					// Идентификатор группы товаров
					case 'shop_groups_id':
						if (intval($sData))
						{
							$oTmpObject = Core_Entity::factory("Shop_Group")->find($sData);

							if (!is_null($oTmpObject->id))
							{
								$this->_oCurrentGroup = $oTmpObject;
							}
						}
					break;
					// Название группы товаров WARNING
					case 'shop_groups_value':
						if ($sData != '')
						{
							if(($sNeedKey = array_search("shop_shop_groups_parent_cml_id", $this->csv_fields)) !== false
							&& ($sCMLID = Core_Array::get($aCsvLine, $sNeedKey, '')) != '')
							{
								if ($sCMLID == 'ID00000000')
								{
									$oTmpParentObject = Core_Entity::factory('Shop_Group', 0);
								}
								else
								{
									$oTmpParentObject = $this->_oCurrentShop->Shop_Groups->getByGuid($sCMLID, FALSE);

									if(is_null($oTmpParentObject))
									{
										$oTmpParentObject = Core_Entity::factory('Shop_Group', 0);
									}
								}

								$oTmpObject = $this->_oCurrentShop->Shop_Groups;
								$oTmpObject->queryBuilder()
									->where('parent_id', '=', $oTmpParentObject->id)
									->where('name', '=', $sData)
									->limit(1);
								$oTmpObject = $oTmpObject->findAll(FALSE);

								if (count($oTmpObject) > 0)
								{
									$this->_oCurrentGroup = $oTmpObject[0];
								}
								else
								{
									$oTmpObject = Core_Entity::factory('Shop_Group');
									$oTmpObject->name = $sData;
									$oTmpObject->parent_id = $oTmpParentObject->id;
									$oTmpObject->shop_id = $this->_oCurrentShop->id;
									$this->_oCurrentGroup = $this->_doSaveGroup($oTmpObject);
								}
							}
							else
							{
								$oTmpObject = $this->_oCurrentShop->Shop_Groups;
								$oTmpObject->queryBuilder()
									->where('parent_id', '=', intval($this->_oCurrentGroup->id))
									->where('name', '=', $sData)
									->limit(1);
								$oTmpObject = $oTmpObject->findAll(FALSE);

								if (count($oTmpObject) > 0)
								{
									// Группа нашлась
									$this->_oCurrentGroup = $oTmpObject[0];
								}
								else
								{
									// Группа не нашлась
									$oTmpObject = Core_Entity::factory('Shop_Group');
									$oTmpObject->name = $sData;
									$oTmpObject->parent_id = intval($this->_oCurrentGroup->id);
									$oTmpObject->shop_id = $this->_oCurrentShop->id;
									$this->_oCurrentGroup = $this->_doSaveGroup($oTmpObject);
								}
							}

							$this->_oCurrentItem->shop_group_id = $this->_oCurrentGroup->id;
						}
					break;
					// Путь группы товаров
					case 'shop_groups_path':
						if ($sData != '')
						{
							$oTmpObject = Core_Entity::factory("Shop_Group");
							$oTmpObject
								->queryBuilder()
								->where('parent_id', '=', intval($this->_oCurrentGroup->id))
								->where('shop_id', '=', intval($this->_oCurrentShop->id))
								->where('path', '=', $sData)
							;
							$oTmpObject = $oTmpObject->findAll(FALSE);
							if (count($oTmpObject) >= 1)
							{
								// Группа найдена, делаем текущей
								$this->_oCurrentGroup = $oTmpObject[0];
							}
							else
							{
								// Группа не найдена, обновляем путь для текущей группы
								$this->_oCurrentGroup->path = $sData;
								$this->_oCurrentGroup->id && $this->_oCurrentGroup->save() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
							}
						}
					break;
					// Порядок сортировки группы товаров
					case 'shop_groups_order':
						$this->_oCurrentGroup->sorting = intval($sData);
						$this->_oCurrentGroup->id && $this->_oCurrentGroup->save() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
					break;
					// Описание группы товаров
					case 'shop_groups_description':
						$this->_oCurrentGroup->description = $sData;
						$this->_oCurrentGroup->id && $this->_oCurrentGroup->save() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
					break;
					// SEO Title группы товаров
					case 'shop_groups_seo_title':
						$this->_oCurrentGroup->seo_title = $sData;
						$this->_oCurrentGroup->id && $this->_oCurrentGroup->save() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
					break;
					// SEO Description группы товаров
					case 'shop_groups_seo_description':
						$this->_oCurrentGroup->seo_description = $sData;
						$this->_oCurrentGroup->id && $this->_oCurrentGroup->save() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
					break;
					// SEO Keywords группы товаров
					case 'shop_groups_seo_keywords':
						$this->_oCurrentGroup->seo_keywords = $sData;
						$this->_oCurrentGroup->id && $this->_oCurrentGroup->save() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
					break;
					// Активность группы товаров
					case 'shop_groups_activity':
						$this->_oCurrentGroup->active = intval($sData) >= 1 ? 1 : 0;
						$this->_oCurrentGroup->id && $this->_oCurrentGroup->save() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
					break;
					// Картинка группы товаров
					case 'shop_groups_image':
						if ($sData != '')
						{
							// Для гарантии получения идентификатора группы
							$this->_oCurrentGroup->save();
							$this->_incUpdatedGroups($this->_oCurrentGroup->id);

							// Папка назначения
							$sDestinationFolder = $this->_oCurrentGroup->getGroupPath();

							// Файл-источник
							$sSourceFile = $this->imagesPath . $sData;
							$sSourceFileBaseName = basename($sSourceFile, '');

							if (!Core_File::isValidExtension(
								$sSourceFile,
								Core::$mainConfig['availableExtension']))
							{
								// Неразрешенное расширение
								break;
							}

							// Создаем папку назначения
							$this->_oCurrentGroup->createDir();

							if (mb_strpos(mb_strtolower($sSourceFile), "http://") === 0)
							{
								// Файл из WEB'а, создаем временный файл
								$sTempFileName = tempnam($sDestinationFolder, "CMS");
								// Копируем содержимое WEB-файла в локальный временный файл
								file_put_contents($sTempFileName, file_get_contents($sSourceFile));
								// Файл-источник равен временному файлу
								$sSourceFile = $sTempFileName;
							}
							else
							{
								$sSourceFile = CMS_FOLDER . $sSourceFile;
							}

							if (!$this->_oCurrentShop->change_filename)
							{
								$sTargetFileName = $sSourceFileBaseName;
							}
							else
							{
								$sTargetFileExtension = Core_File::getExtension($sSourceFileBaseName);

								if ($sTargetFileExtension != '')
								{
									$sTargetFileExtension = ".{$sTargetFileExtension}";
								}

								$sTargetFileName = "shop_group_image{$this->_oCurrentGroup->id}{$sTargetFileExtension}";
							}

							// Создаем массив параметров для загрузки картинок элементу
							$aPicturesParam = array();
							$aPicturesParam['large_image_isset'] = TRUE;
							$aPicturesParam['large_image_source'] = $sSourceFile;
							$aPicturesParam['large_image_name'] = $sSourceFileBaseName;
							$aPicturesParam['large_image_target'] = $sDestinationFolder . $sTargetFileName;

							$aPicturesParam['watermark_file_path'] = $this->_oCurrentShop->getWatermarkFilePath();
							$aPicturesParam['watermark_position_x'] = $this->_oCurrentShop->watermark_default_position_x;
							$aPicturesParam['watermark_position_y'] = $this->_oCurrentShop->watermark_default_position_y;
							$aPicturesParam['large_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio;

							// Проверяем, передали ли нам малое изображение
							$iSmallImageIndex = array_search('shop_groups_small_image', $this->csv_fields);

							$bCreateSmallImage = $iSmallImageIndex === FALSE || strval($this->csv_fields[$iSmallImageIndex]) == '';

							if ($bCreateSmallImage)
							{
								// Малое изображение не передано, создаем его из большого
								$aPicturesParam['small_image_source'] = $aPicturesParam['large_image_source'];
								$aPicturesParam['small_image_name'] = $aPicturesParam['large_image_name'];
								$aPicturesParam['small_image_target'] = $sDestinationFolder . "small_{$sTargetFileName}";
								$aPicturesParam['create_small_image_from_large'] = TRUE;
								$aPicturesParam['small_image_max_width'] = $this->_oCurrentShop->group_image_small_max_width;
								$aPicturesParam['small_image_max_height'] = $this->_oCurrentShop->group_image_small_max_height;
								$aPicturesParam['small_image_watermark'] = $this->_oCurrentShop->watermark_default_use_small_image;
								$aPicturesParam['small_image_preserve_aspect_ratio'] = $aPicturesParam['large_image_preserve_aspect_ratio'];
							}
							else
							{
								$aPicturesParam['create_small_image_from_large'] = FALSE;
							}

							$aPicturesParam['large_image_max_width'] = $this->_oCurrentShop->group_image_large_max_width;
							$aPicturesParam['large_image_max_height'] = $this->_oCurrentShop->group_image_large_max_height;
							$aPicturesParam['large_image_watermark'] = $this->_oCurrentShop->watermark_default_use_large_image;

							// Удаляем старое большое изображение
							if ($this->_oCurrentGroup->image_large)
							{
								try
								{
									Core_File::delete($this->_oCurrentGroup->getLargeFilePath());
								} catch (Exception $e) {}
							}

							// Удаляем старое малое изображение
							if ($bCreateSmallImage && $this->_oCurrentGroup->image_small)
							{
								try
								{
									Core_File::delete($this->_oCurrentGroup->getSmallFilePath());
								} catch (Exception $e) {}
							}

							try {
								$result = Core_File::adminUpload($aPicturesParam);
							} catch (Exception $exc) {
								Core_Message::show($exc->getMessage(), 'error');
								$result = array('large_image' => FALSE, 'small_image' => FALSE);
							}

							if ($result['large_image'])
							{
								$this->_oCurrentGroup->image_large = $sTargetFileName;

								$this->_oCurrentGroup->id && $this->_oCurrentGroup->setLargeImageSizes() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
							}

							if ($result['small_image'])
							{
								$this->_oCurrentGroup->image_small = "small_{$sTargetFileName}";

								$this->_oCurrentGroup->id && $this->_oCurrentGroup->setSmallImageSizes() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
							}

							if (strpos(basename($sSourceFile), "CMS") === 0)
							{
								// Файл временный, подлежит удалению
								Core_File::delete($sSourceFile);
							}
						}
					break;
					// Малая картинка группы товаров
					case 'shop_groups_small_image':
						if ($sData != '')
						{
							// Для гарантии получения идентификатора группы
							$this->_oCurrentGroup->save();
							$this->_incUpdatedGroups($this->_oCurrentGroup->id);

							// Папка назначения
							$sDestinationFolder = $this->_oCurrentGroup->getGroupPath();

							// Файл-источник
							$sSourceFile = $this->imagesPath . $sData;
							$sSourceFileBaseName = basename($sSourceFile, '');

							if (!Core_File::isValidExtension(
							$sSourceFile,
							Core::$mainConfig['availableExtension']))
							{
								// Неразрешенное расширение
								break;
							}

							// Создаем папку назначения
							$this->_oCurrentGroup->createDir();

							if (mb_strpos(mb_strtolower($sSourceFile), "http://") === 0)
							{
								// Файл из WEB'а, создаем временный файл
								$sTempFileName = tempnam($sDestinationFolder, "CMS");
								// Копируем содержимое WEB-файла в локальный временный файл
								file_put_contents($sTempFileName, file_get_contents($sSourceFile));
								// Файл-источник равен временному файлу
								$sSourceFile = $sTempFileName;
							}
							else
							{
								$sSourceFile = CMS_FOLDER . $sSourceFile;
							}

							if (!$this->_oCurrentShop->change_filename)
							{
								$sTargetFileName = "small_{$sSourceFileBaseName}";
							}
							else
							{
								$sTargetFileExtension = Core_File::getExtension($sSourceFileBaseName);

								if ($sTargetFileExtension != '')
								{
									$sTargetFileExtension = ".{$sTargetFileExtension}";
								}

								$sTargetFileName = "small_shop_group_image{$this->_oCurrentGroup->id}{$sTargetFileExtension}";
							}

							$aPicturesParam = array();
							$aPicturesParam['small_image_source'] = $sSourceFile;
							$aPicturesParam['small_image_name'] = $sSourceFileBaseName;
							$aPicturesParam['small_image_target'] = $sDestinationFolder . $sTargetFileName;
							$aPicturesParam['create_small_image_from_large'] = FALSE;
							$aPicturesParam['small_image_max_width'] = $this->_oCurrentShop->group_image_small_max_width;
							$aPicturesParam['small_image_max_height'] = $this->_oCurrentShop->group_image_small_max_height;
							$aPicturesParam['small_image_watermark'] = $this->_oCurrentShop->watermark_default_use_small_image;
							$aPicturesParam['watermark_file_path'] = $this->_oCurrentShop->getWatermarkFilePath();
							$aPicturesParam['watermark_position_x'] = $this->_oCurrentShop->watermark_default_position_x;
							$aPicturesParam['watermark_position_y'] = $this->_oCurrentShop->watermark_default_position_y;
							$aPicturesParam['small_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio;

							// Удаляем старое малое изображение
							if ($this->_oCurrentGroup->image_small)
							{
								try
								{
									Core_File::delete($this->_oCurrentGroup->getSmallFilePath());
								} catch (Exception $e) {}
							}

							try {
								$result = Core_File::adminUpload($aPicturesParam);
							} catch (Exception $exc) {
								Core_Message::show($exc->getMessage(), 'error');
								$result = array('small_image' => FALSE);
							}

							if ($result['small_image'])
							{
								$this->_oCurrentGroup->image_small = $sTargetFileName;

								$this->_oCurrentGroup->id && $this->_oCurrentGroup->setSmallImageSizes() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
							}

							if (strpos(basename($sSourceFile), "CMS") === 0)
							{
								// Файл временный, подлежит удалению
								Core_File::delete($sSourceFile);
							}
						}
					break;
					// Передан GUID группы товаров
					case 'shop_groups_cml_id':
						if ($sData != '')
						{
							$oTmpObject = $this->_oCurrentShop->Shop_Groups;
							$oTmpObject->queryBuilder()
								->where('guid', '=', $sData);
							$oTmpObject = $oTmpObject->findAll(FALSE);

							if (count($oTmpObject) > 0)
							{
								// группа найдена
								$this->_oCurrentGroup = $oTmpObject[0];
								$this->_oCurrentItem->shop_group_id = $this->_oCurrentGroup->id;
							}
							else
							{
								// группа не найдена, присваиваем shop_groups_cml_id текущей группе
								$this->_oCurrentGroup->guid = $sData;
								$this->_oCurrentGroup->id && $this->_doSaveGroup($this->_oCurrentGroup);
							}
						}
					break;
					// Передан GUID родительской группы товаров
					case 'shop_shop_groups_parent_cml_id':
						if ($sData != '')
						{
							$oTmpObject = Core_Entity::factory('Shop_Group', 0);

							if ($sData != 'ID00000000')
							{
								$oTmpObject = $this->_oCurrentShop->Shop_Groups->getByGuid($sData, FALSE);
							}

							if (!is_null($oTmpObject))
							{
								if($oTmpObject->id != $this->_oCurrentGroup->id)
								{
									$this->_oCurrentGroup->parent_id = $oTmpObject->id;
									$this->_oCurrentGroup->id && $this->_oCurrentGroup->save() && $this->_incUpdatedGroups($this->_oCurrentGroup->id);
								}

								//$this->_oCurrentItem->shop_group_id = $oTmpObject->id;
							}
						}
					break;
					// Передан идентификатор валюты
					case 'shop_currency_id':
						if ($sData != '')
						{
							$oTmpObject = Core_Entity::factory("Shop_Currency")->find($sData);
							if (!is_null($oTmpObject->id))
							{
								$this->_oCurrentItem->shop_currency_id = $oTmpObject->id;
							}
						}
					break;
					// Передан идентификатор налога
					case 'shop_tax_id':
						if ($sData != '')
						{
							$oTmpObject = Core_Entity::factory("Shop_Tax")->find($sData);
							if (!is_null($oTmpObject->id))
							{
								$this->_oCurrentItem->shop_tax_id = $oTmpObject->id;
							}
						}
					break;
					// Передан идентификатор производителя
					case 'shop_producers_list_id':
						if ($sData != '')
						{
							$oTmpObject = Core_Entity::factory("Shop_Producer")->find($sData);
							if (!is_null($oTmpObject->id))
							{
								$this->_oCurrentItem->shop_producer_id = $oTmpObject->id;
							}
						}
					break;
					// Передано название производителя
					case 'shop_producers_list_value':
						if ($sData != '')
						{
							$oTmpObject = $this->_oCurrentShop->Shop_Producers;
							$oTmpObject->queryBuilder()->where('name', '=', $sData);
							$oTmpObject = $oTmpObject->findAll(FALSE);
							if (count($oTmpObject) > 0)
							{
								$this->_oCurrentItem->shop_producer_id = $oTmpObject[0]->id;
							}
							else
							{
								$this->_oCurrentItem->shop_producer_id = Core_Entity::factory("Shop_Producer")->name($sData)->shop_id($this->_oCurrentShop->id)->save()->id;
							}
						}
					break;
					// Передан идентификатор продавца
					case 'shop_shop_sallers_id':
						if ($sData != '')
						{
							$oTmpObject = $this->_oCurrentShop->Shop_Sellers;
							$oTmpObject->queryBuilder()->where('id', '=', $sData);
							$oTmpObject = $oTmpObject->findAll(FALSE);
							if (count($oTmpObject) > 0)
							{
								$this->_oCurrentItem->shop_seller_id = $oTmpObject[0]->id;
							}
						}
					break;
					// Передан Yandex Market Sales Notes
					case 'shop_items_catalog_yandex_market_sales_notes':
						if ($sData != '')
						{
							$this->_oCurrentItem->yandex_market_sales_notes = $sData;
						}
					break;
					// Передано название продавца
					case 'shop_sallers_name':
						if ($sData != '')
						{
							$oTmpObject = $this->_oCurrentShop->Shop_Sellers;
							$oTmpObject->queryBuilder()->where('name', '=', $sData);
							$oTmpObject = $oTmpObject->findAll(FALSE);
							if (count($oTmpObject) > 0)
							{
								$this->_oCurrentItem->shop_seller_id = $oTmpObject[0]->id;
							}
							else
							{
								$this->_oCurrentItem->shop_seller_id = Core_Entity::factory("Shop_Seller")->name($sData)->path(Core_Str::transliteration($sData))->save()->id;
							}
						}
					break;
					// Передана единица измерения
					case 'shop_mesures_id':
						if ($sData != '')
						{
							$oTmpObject = Core_Entity::factory("Shop_Measure")->find($sData);
							if (!is_null($oTmpObject->id))
							{
								$this->_oCurrentItem->shop_measure_id = $oTmpObject->id;
							}
						}
					break;
					// Передано название единицы измерения
					case 'shop_mesures_value':
						if ($sData != '')
						{
							$oTmpObject = Core_Entity::factory("Shop_Measure");
							$oTmpObject->queryBuilder()->where('name', '=', $sData);
							$oTmpObject = $oTmpObject->findAll(FALSE);
							if (count($oTmpObject) > 0)
							{
								$this->_oCurrentItem->shop_measure_id = $oTmpObject[0]->id;
							}
							else
							{
								$this->_oCurrentItem->shop_measure_id = Core_Entity::factory('Shop_Measure')->name($sData)->description($sData)->save()->id;
							}
						}
					break;
					// Дополнительные группы для товара (CML_ID), где нужно создавать ярлыки
					case 'additional_group':
						if ($sData != '')
						{
							$this->_aAdditionalGroups[] = $sData;
						}
					break;
					// Идентификатор товара
					case 'shop_items_catalog_item_id':
						if ($sData != '')
						{
							$oTmpObject = Core_Entity::factory("Shop_Item")->find($sData);
							if (!is_null($oTmpObject->id))
							{
								//$this->_oCurrentItem->id = $oTmpObject->id;
								$this->_oCurrentItem = $oTmpObject;
							}
						}
					break;
					// Передано название товара
					case 'shop_items_catalog_name':
						if ($sData != '')
						{
							$this->_oCurrentItem->name = $sData;
						}
					break;
					// Передан артикул товара
					case 'shop_items_catalog_marking':

						if ($sData != '')
						{
							Core_Event::notify('ImportShopItems.onBeforeFindByMarking', $this, array($this->_oCurrentShop, $this->_oCurrentItem));

							$oTmpObject = $this->_oCurrentShop->Shop_Items;
							$oTmpObject->queryBuilder()->where('marking', '=', $sData);
							$oTmpObject = $oTmpObject->findAll(FALSE);

							$this->_oCurrentItem->marking = $sData;

							if (count($oTmpObject) > 0)
							{
								$this->_oCurrentItem = $oTmpObject[0];
							}

							Core_Event::notify('ImportShopItems.onAfterFindByMarking', $this, array($this->_oCurrentShop, $this->_oCurrentItem));
						}
					break;
					// Передана дата добавления товара
					case 'shop_shop_items_catalog_date_time':
						if ($sData != '')
						{
							if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sData))
							{
								$this->_oCurrentItem->datetime = $sData;
							}
							else
							{
								$this->_oCurrentItem->datetime = Core_Date::datetime2sql($sData);
							}
						}
					break;
					// Передано описание товара
					case 'shop_items_catalog_description':
						if ($sData != '')
						{
							$this->_oCurrentItem->description = $sData;
						}
					break;
					// Передан текст товара
					case 'shop_items_catalog_text':
						if ($sData != '')
						{
							$this->_oCurrentItem->text = $sData;
						}
					break;
					// Передана большая картинка товара, обработка будет после вставки товара
					case 'shop_items_catalog_image':
						/*if ($sData != '')
						{*/
							$this->_sBigImageFile = $sData;
						//}
					break;
					// Передана малая картинка товара, обработка будет после вставки товара
					case 'shop_items_catalog_small_image':
						/*if ($sData != '')
						{*/
							$this->_sSmallImageFile = $sData;
						//}
					break;
					// Переданы метки товара, обработка будет после вставки товара
					case 'shop_items_catalog_label':
						if ($sData != '')
						{
							$this->_sCurrentTags = $sData;
						}
					break;
					// Передан вес товара
					case 'shop_items_catalog_weight':
						if ($sData != '')
						{
							$this->_oCurrentItem->weight = Shop_Controller::instance()->convertPrice($sData);
						}
					break;
					// Передана цена товара
					case 'shop_items_catalog_price':
						if ($sData != '')
						{
							$this->_oCurrentItem->price = Shop_Controller::instance()->convertPrice($sData);
						}
					break;
					// Передана активность товара
					case 'shop_items_catalog_is_active':
						if ($sData != '')
						{
							$this->_oCurrentItem->active = $sData;
						}
					break;
					// Передан порядок сортировки товара
					case 'shop_items_catalog_order':
						if ($sData != '')
						{
							$this->_oCurrentItem->sorting = $sData;
						}
					break;
					// Передан путь товара
					case 'shop_items_catalog_path':
						if ($sData != '')
						{
							$oTmpObject = $this->_oCurrentShop->Shop_Items;
							$oTmpObject->queryBuilder()
								->where('path', '=', $sData)
								->where('shop_group_id', '=', $this->_oCurrentGroup->id)
							;
							$oTmpObject = $oTmpObject->findAll(FALSE);
							if (count($oTmpObject) > 0)
							{
								$this->_oCurrentItem = $oTmpObject[0];
							}
							else
							{
								$this->_oCurrentItem->path = $sData;
							}
						}
					break;
					// Передан Seo Title для товара
					case 'shop_items_catalog_seo_title':
						if ($sData != '')
						{
							$this->_oCurrentItem->seo_title = $sData;
						}
					break;
					// Передан Seo Description для товара
					case 'shop_items_catalog_seo_description':
						if ($sData != '')
						{
							$this->_oCurrentItem->seo_description = $sData;
						}
					break;
					// Передан Seo Keywords для товара
					case 'shop_items_catalog_seo_keywords':
						if ($sData != '')
						{
							$this->_oCurrentItem->seo_keywords = $sData;
						}
					break;
					// Передан флаг индексации товара
					case 'shop_items_catalog_indexation':
						if ($sData != '')
						{
							$this->_oCurrentItem->indexing = $sData;
						}
					break;
					// Передан Yandex Market Allow
					case 'shop_shop_items_catalog_yandex_market_allow':
						if ($sData != '')
						{
							$this->_oCurrentItem->yandex_market = $sData;
						}
					break;
					// Передан Yandex Market BID
					case 'shop_shop_items_catalog_yandex_market_bid':
						if ($sData != '')
						{
							$this->_oCurrentItem->yandex_market_bid = $sData;
						}
					break;
					// Передан Yandex Market CID
					case 'shop_shop_items_catalog_yandex_market_cid':
						if ($sData != '')
						{
							$this->_oCurrentItem->yandex_market_cid = $sData;
						}
					break;
					// Передан артикул родительского товара (модификация)
					case 'shop_item_parent_mark':
						if ($sData != '')
						{
							$oTmpObject = $this->_oCurrentShop->Shop_Items;
							$oTmpObject->queryBuilder()->where('marking', '=', $sData);
							$oTmpObject = $oTmpObject->findAll(FALSE);
							if (count($oTmpObject) > 0 && $this->_oCurrentItem->id != $oTmpObject[0]->id)
							{
								$this->_oCurrentItem->shop_group_id = 0;
								$this->_oCurrentItem->modification_id = $oTmpObject[0]->id;
							}
						}
					break;
					// Передан идентификатор пользователя сайта
					case 'site_users_id':
						if ($sData != '')
						{
							$this->_oCurrentItem->siteuser_id = $sData;
						}
					break;
					// Передан артикул родительского товара для сопутствующего товара
					case 'shop_item_parent_soput':
						if ($sData != '')
						{
							$this->_sAssociatedItemMark = $sData;
						}
					break;
					case 'shop_eitem_name':
						if ($sData != '')
						{
							$this->_oCurrentShopEItem->name = $sData;
							$this->_oCurrentItem->type = 1;
						}
					break;
					case 'shop_eitems_text':
						if ($sData != '')
						{
							$this->_oCurrentShopEItem->value = $sData;
							$this->_oCurrentItem->type = 1;
						}
					break;
					case 'shop_eitems_file':
						if ($sData != '')
						{
							$this->_oCurrentShopEItem->filename = $sData;
							$this->_oCurrentItem->type = 1;
						}
					break;
					case 'shop_eitem_count':
						if ($sData != '')
						{
							$this->_oCurrentShopEItem->count = $sData;
							$this->_oCurrentItem->type = 1;
						}
					break;
					case 'shop_shop_items_catalog_putend_date':
						if ($sData != '')
						{
							// Передана дата завершения публикации, проверяем ее на соответствие стандарту времени MySQL
							if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sData))
							{
								$this->_oCurrentItem->end_datetime = $sData;
							}
							else
							{
								$this->_oCurrentItem->end_datetime = Core_Date::datetime2sql($sData);
							}
						}
					break;
					case 'shop_shop_items_catalog_putoff_date':
						if ($sData != '')
						{
							// Передана дата завершения публикации, проверяем ее на соответствие стандарту времени MySQL
							if (preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sData))
							{
								$this->_oCurrentItem->start_datetime = $sData;
							}
							else
							{
								$this->_oCurrentItem->start_datetime = Core_Date::datetime2sql($sData);
							}
						}
					break;
					case 'shop_shop_items_catalog_type':
						if ($sData != '')
						{
							$this->_oCurrentItem->type = $sData;
						}
					break;
					case 'shop_special_prices_from':
						if ($sData != '')
						{
							$this->_oCurrentShopSpecialPrice->min_quantity = $sData;
						}
					break;
					case 'shop_special_prices_to':
						if ($sData != '')
						{
							$this->_oCurrentShopSpecialPrice->max_quantity = $sData;
						}
					break;
					case 'shop_special_prices_price':
						if ($sData != '')
						{
							$this->_oCurrentShopSpecialPrice->price = $sData;
						}
					break;
					case 'shop_special_prices_percent':
						if ($sData != '')
						{
							$this->_oCurrentShopSpecialPrice->percent = $sData;
						}
					break;
					case 'shop_items_cml_id':
						if ($sData != '')
						{
							$oTmpObject = $this->_oCurrentShop->Shop_Items;
							$oTmpObject->queryBuilder()->where('guid', '=', $sData);
							$oTmpObject = $oTmpObject->findAll(FALSE);

							$this->_oCurrentItem->guid = $sData;

							if (count($oTmpObject) > 0)
							{
								$this->_oCurrentItem = $oTmpObject[0];
							}
						}
					break;
					default:
						if ($sData != '')
						{
							$sFieldName = $this->csv_fields[$iKey];

							if (strpos($sFieldName, "price-") === 0)
							{
								// Дополнительная цена товара
								$aPriceInfo = explode("-", $sFieldName);

								$this->_aExternalPrices[$aPriceInfo[1]] = $sData;
							}

							if (strpos($sFieldName, "warehouse-") === 0)
							{
								// Остаток на складе N
								$aWarehouseInfo = explode("-", $sFieldName);

								$this->_aWarehouses[$aWarehouseInfo[1]] = $sData;
							}

							if (strpos($sFieldName, "propsmall-") === 0)
							{
								// Дополнительный файл дополнительного свойства/Малое изображение картинки дополнительного свойства
								$aPropertySmallInfo = explode("-", $sFieldName);

								$this->_aExternalPropertiesSmall[$aPropertySmallInfo[1]] = $sData;
							}

							if (strpos($sFieldName, "prop-") === 0)
							{
								// Основной файл дополнительного свойства/Большое изображение картинки дополнительного свойства
								$aPropertyInfo = explode("-", $sFieldName);

								$this->_aExternalProperties[$aPropertyInfo[1]] = $sData;
							}

							if (strpos($sFieldName, "prop_group-") === 0)
							{
								// Дополнительное свойство группы товаров
								$iPropertyId = explode("-", $sFieldName);

								$iPropertyId = $iPropertyId[1];

								$oProperty = Core_Entity::factory('Property', $iPropertyId);

								$aPropertyValues = $oProperty->getValues($this->_oCurrentGroup->id, FALSE);

								$oProperty_Value = isset($aPropertyValues[0])
									? $aPropertyValues[0]
									: $oProperty->createNewValue($this->_oCurrentGroup->id);

								switch($oProperty->type)
								{
									// Файл
									case 2:
										// Для гарантии получения идентификатора группы
										$this->_oCurrentGroup->save();
										$this->_incUpdatedGroups($this->_oCurrentGroup->id);

										// Папка назначения
										$sDestinationFolder = $this->_oCurrentGroup->getGroupPath();

										// Файл-источник
										$sSourceFile = $this->imagesPath . $sData;
										$sSourceFileBaseName = basename($sSourceFile, '');

										if (!Core_File::isValidExtension($sSourceFile, Core::$mainConfig['availableExtension']))
										{
											// Неразрешенное расширение
											break;
										}

										// Создаем папку назначения
										$this->_oCurrentGroup->createDir();

										if (mb_strpos(mb_strtolower($sSourceFile), "http://") === 0)
										{
											// Файл из WEB'а, создаем временный файл
											$sTempFileName = tempnam($sDestinationFolder, "CMS");
											// Копируем содержимое WEB-файла в локальный временный файл
											file_put_contents($sTempFileName, file_get_contents($sSourceFile));
											// Файл-источник равен временному файлу
											$sSourceFile = $sTempFileName;
										}
										else
										{
											$sSourceFile = CMS_FOLDER . ltrim($sSourceFile, '/\\');
										}

										if (!$this->_oCurrentShop->change_filename)
										{
											$sTargetFileName = $sSourceFileBaseName;
										}
										else
										{
											$sTargetFileExtension = Core_File::getExtension($sSourceFileBaseName);

											if ($sTargetFileExtension != '')
											{
												$sTargetFileExtension = ".{$sTargetFileExtension}";
											}

											$oProperty_Value->save();
											$sTargetFileName = "shop_property_file_{$this->_oCurrentGroup->id}_{$oProperty_Value->id}{$sTargetFileExtension}";
										}

										// Создаем массив параметров для загрузки картинок элементу
										$aPicturesParam = array();
										$aPicturesParam['large_image_isset'] = TRUE;
										$aPicturesParam['large_image_source'] = $sSourceFile;
										$aPicturesParam['large_image_name'] = $sSourceFileBaseName;
										$aPicturesParam['large_image_target'] = $sDestinationFolder . $sTargetFileName;

										$aPicturesParam['watermark_file_path'] = $this->_oCurrentShop->getWatermarkFilePath();
										$aPicturesParam['watermark_position_x'] = $this->_oCurrentShop->watermark_default_position_x;
										$aPicturesParam['watermark_position_y'] = $this->_oCurrentShop->watermark_default_position_y;
										$aPicturesParam['large_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio;

										// Малое изображение для дополнительных свойств создается всегда
										$aPicturesParam['small_image_source'] = $aPicturesParam['large_image_source'];
										$aPicturesParam['small_image_name'] = $aPicturesParam['large_image_name'];
										$aPicturesParam['small_image_target'] = $sDestinationFolder . "small_{$sTargetFileName}";
										$aPicturesParam['create_small_image_from_large'] = TRUE;
										$aPicturesParam['small_image_max_width'] = $this->_oCurrentShop->group_image_small_max_width;
										$aPicturesParam['small_image_max_height'] = $this->_oCurrentShop->group_image_small_max_height;
										$aPicturesParam['small_image_watermark'] = $this->_oCurrentShop->watermark_default_use_small_image;
										$aPicturesParam['small_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio_small;

										$aPicturesParam['large_image_max_width'] = $this->_oCurrentShop->group_image_large_max_width;
										$aPicturesParam['large_image_max_height'] = $this->_oCurrentShop->group_image_large_max_height;
										$aPicturesParam['large_image_watermark'] = $this->_oCurrentShop->watermark_default_use_large_image;

										// Удаляем старое большое изображение
										if ($oProperty_Value->file != '')
										{
											try
											{
												Core_File::delete($sDestinationFolder . $oProperty_Value->file);
											} catch (Exception $e) {
											}
										}

										// Удаляем старое малое изображение
										if ($oProperty_Value->file_small != '')
										{
											try
											{
												Core_File::delete($sDestinationFolder . $oProperty_Value->file_small);
											} catch (Exception $e) {
											}
										}

										try {
											$result = Core_File::adminUpload($aPicturesParam);
										} catch (Exception $exc) {
											Core_Message::show($exc->getMessage(), 'error');
											$result = array('large_image' => FALSE, 'small_image' => FALSE);
										}

										if ($result['large_image'])
										{
											$oProperty_Value->file = $sTargetFileName;
											$oProperty_Value->file_name = '';
										}

										if ($result['small_image'])
										{
											$oProperty_Value->file_small = "small_{$sTargetFileName}";
											$oProperty_Value->file_small_name = '';
										}

										if (strpos(basename($sSourceFile), "CMS") === 0)
										{
											// Файл временный, подлежит удалению
											Core_File::delete($sSourceFile);
										}
									break;
									// Список
									case 3:
										if (Core::moduleIsActive('list'))
										{
											$oListItem = Core_Entity::factory('List', $oProperty->list_id)
												->List_Items
												->getByValue($sData, FALSE);

											if (is_null($oListItem))
											{
												$oListItem = Core_Entity::factory('List_Item')
													->list_id($oProperty->list_id)
													->value($sData)
													->save();
											}

											$oProperty_Value->setValue($oListItem->id);
										}
									break;
									 case 8:
										if (!preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $sData))
										{
											$sData = Core_Date::datetime2sql($sData);
										}

										$oProperty_Value->setValue($sData);
									break;
									case 9:
										if (!preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sData))
										{
											$sData = Core_Date::datetime2sql($sData);
										}

										$oProperty_Value->setValue($sData);
									break;
									default:
										$oProperty_Value->setValue($sData);
									break;
								}

								$oProperty_Value->save();
							}
						}
					break;
				}
			}

			$this->_oCurrentItem->shop_id = $this->_oCurrentShop->id;
			!$this->_oCurrentItem->id && $this->_oCurrentItem->shop_group_id = (int)$this->_oCurrentGroup->id;
			!$this->_oCurrentItem->id && is_null($this->_oCurrentItem->path) && $this->_oCurrentItem->path = '';
			$this->_oCurrentItem->id && $this->_oCurrentItem->id == $this->_oCurrentItem->modification_id && $this->_oCurrentItem->modification_id = 0;

			if(!is_null($this->_oCurrentOrder))
			{
				$this->_oCurrentShop->add($this->_oCurrentOrder);
			}


			if($this->_oCurrentItem->id && $this->importAction == 2)
			{
				// если сказано - оставить без изменений, затираем все изменения
				$this->_oCurrentItem = Core_Entity::factory('Shop_Item')->find($this->_oCurrentItem->id);
				$this->_sBigImageFile = '';
				$this->_sSmallImageFile = '';
				$this->deleteImage = 0;
			}

			$aTagsName = array();
			if(!$this->_oCurrentItem->id)
			{
				if (Core::moduleIsActive('tag'))
				{
					$oTag = Core_Entity::factory('Tag');

					// Вставка тэгов автоматически разрешена
					if ($this->_sCurrentTags == '' && $this->_oCurrentShop->apply_tags_automatically)
					{
						$sTmpString = '';

						$sTmpString .= $this->_oCurrentItem->name ? ' ' . $this->_oCurrentItem->name : '';
						$sTmpString .= $this->_oCurrentItem->description ? ' ' . $this->_oCurrentItem->description : '';
						$sTmpString .= $this->_oCurrentItem->text ? ' ' . $this->_oCurrentItem->text : '';

						// получаем хэш названия и описания группы
						$aText = Core_Str::getHashes($sTmpString, array ('hash_function' => 'crc32'));

						$aText = array_unique($aText);

						// Получаем список меток
						$aTags = $oTag->findAll(FALSE);

						// Есть хотя бы одна метка
						if (count($aTags) > 0)
						{
							// Удаляем уже существующие связи с метками
							$this->_oCurrentItem->Tag_Shop_Items->deleteAll(FALSE);

							foreach($aTags as $oTag)
							{
								$aTmpTags =  Core_Str::getHashes($oTag->name, array ('hash_function' => 'crc32'));

								$aTmpTags = array_unique($aTmpTags);

								if (count($aText) >= count($aTmpTags))
								{
									// Расчитываем пересечение
									$iIntersect = count(array_intersect($aText, $aTmpTags));

									if (count($aTmpTags) != 0)
									{
										$iCoefficient = $iIntersect / count($aTmpTags);
									}
									else
									{
										$iCoefficient = 0;
									}

									// Найдено полное вхождение
									if ($iCoefficient == 1)
									{
										// Если тэг еще не учтен
										if (!in_array($oTag->name, $aTmpTags))
										{
											// Добавляем в массив
											$aTagsName[] = $oTag->name;

											// Add relation
											$this->_oCurrentItem->add($oTag);
										}
									}
								}
							}
						}
					}
					elseif ($this->_sCurrentTags != '')
					{
						$this->_oCurrentItem->id && $this->_oCurrentItem->applyTags($this->_sCurrentTags);
					}
				}
			}

			if (($this->_oCurrentItem->id
			//&& $this->importAction == 1
			&& !is_null($this->_oCurrentItem->name)
			&& $this->_oCurrentItem->save()))
			{
				$this->_incUpdatedItems($this->_oCurrentItem->id);
			}
			elseif (!is_null($this->_oCurrentItem->name)
			&& $this->_oCurrentItem->save())
			{
				if(intval($this->_oCurrentItem->shop_currency_id) == 0)
				{
					$this->_oCurrentItem->shop_currency_id = $this->_oCurrentShop->shop_currency_id;
					$this->_oCurrentItem->save();
				}
				$this->_incInsertedItems($this->_oCurrentItem->id);
			}

			if ($this->_oCurrentItem->seo_keywords == '' && count($aTagsName) > 0)
			{
				$this->_oCurrentItem->seo_keywords = implode(", ", $aTagsName);
				$this->_oCurrentItem->save();
			}

			if ($this->searchIndexation
			&& $this->_oCurrentGroup->id)
			{
				Search_Controller::indexingSearchPages(array(Core_Entity::factory('Shop_Group', $this->_oCurrentGroup->id)->indexing()));
			}

			if ($this->_oCurrentItem->id)
				{

				if ($this->_sAssociatedItemMark)
				{
					$aShopItems = $this->_oCurrentShop->Shop_Items;
					$aShopItems->queryBuilder()->where('marking', '=', $this->_sAssociatedItemMark);
					$aShopItems = $aShopItems->findAll(FALSE);

					if (count($aShopItems) > 0)
					{
						if (is_null($aShopItems[0]
							->Shop_Item_Associateds
							->getByAssociatedId($this->_oCurrentItem->id)))
						{
							Core_Entity::factory('Shop_Item_Associated')
								->shop_item_id($aShopItems[0]->id)
								->shop_item_associated_id($this->_oCurrentItem->id)
								->count(1)
								->save();
						}
					}
				}

				// Обрабатываем склады
				foreach ($this->_aWarehouses as $iWarehouseID => $iWarehouseCount)
				{
					$oShop_Warehouse = Core_Entity::factory('Shop_Warehouse')->find($iWarehouseID);

					// Если склада не существует, связь не добавляется
					if (!is_null($oShop_Warehouse->id))
					{
						$oShop_Warehouse_Item = $oShop_Warehouse
							->Shop_Warehouse_Items
							->getByShopItemId($this->_oCurrentItem->id, FALSE);

						if (is_null($oShop_Warehouse_Item))
						{
							$oShop_Warehouse_Item = Core_Entity::factory('Shop_Warehouse_Item');
							$oShop_Warehouse_Item->shop_warehouse_id = $iWarehouseID;
							$oShop_Warehouse_Item->shop_item_id = $this->_oCurrentItem->id;
						}
						$oShop_Warehouse_Item->count = Shop_Controller::instance()->convertPrice($iWarehouseCount);
						$oShop_Warehouse_Item->save();
					}
				}

				// Обрабатываем специальные цены
				if ($this->_oCurrentShopSpecialPrice->changed())
				{
					$oTmpObject = Core_Entity::factory('Shop_Specialprice');
					$oTmpObject->queryBuilder()
						->where('shop_item_id', '=', $this->_oCurrentItem->id)
						->where('min_quantity', '=', $this->_oCurrentShopSpecialPrice->min_quantity)
						->where('max_quantity', '=', $this->_oCurrentShopSpecialPrice->max_quantity)
						->where('price', '=', $this->_oCurrentShopSpecialPrice->price)
						->where('percent', '=', $this->_oCurrentShopSpecialPrice->percent)
					;

					// Добавляем специальную цену, если её ещё не существовало
					if ($oTmpObject->getCount(FALSE) == 0)
					{
						$this->_oCurrentShopSpecialPrice->shop_item_id = $this->_oCurrentItem->id;
						$this->_oCurrentShopSpecialPrice->save();
					}
				}

				// Обрабатываем ярлыки
				if (count($this->_aAdditionalGroups) > 0)
				{
					$aShopGroups = $this->_oCurrentShop->Shop_Groups;
					$aShopItems = $this->_oCurrentShop->Shop_Items;
					$aShopItems->queryBuilder()
						->where('shortcut_id', '=', $this->_oCurrentItem->id);

					$aShopGroups->queryBuilder()->where('guid', 'IN', $this->_aAdditionalGroups);

					$aShopGroups = $aShopGroups->findAll(FALSE);

					foreach($aShopGroups as $oShopGroup)
					{
						$aShopItems->queryBuilder()
							->where('shop_group_id', '=', $oShopGroup->id);

						$aShopItems = $aShopItems->findAll(FALSE);

						if (count($aShopItems) == 0)
						{
							Core_Entity::factory('Shop_Item')
								->shop_group_id($oShopGroup->id)
								->shortcut_id($this->_oCurrentItem->id)
								->shop_id($this->_oCurrentShop->id)
								->save()
							;
						}
					}
				}

				// Обрабатываем электронные файлы электронного товара
				if ($this->_oCurrentItem->type == 1)
				{
					$this->_oCurrentShopEItem->shop_item_id = $this->_oCurrentItem->id;
					$sAdditionalPath = dirname($this->_oCurrentShopEItem->filename);
					$this->_oCurrentShopEItem->name = basename($this->_oCurrentShopEItem->filename);
					$this->_oCurrentShopEItem->filename = $this->_oCurrentShopEItem->name;
					$this->_oCurrentShopEItem->save();

					$sExtension = Core_File::getExtension($this->_oCurrentShopEItem->filename);

					$sSourceFile = CMS_FOLDER . $this->imagesPath . $sAdditionalPath . '/' . $this->_oCurrentShopEItem->filename;
					$sTargetFile = $this->_oCurrentShop->getPath() . '/eitems/item_catalog_' . $this->_oCurrentItem->id . '/' . $this->_oCurrentShopEItem->id . ($sExtension == '' ? '' : '.' . $sExtension);

					if (is_file($sSourceFile)
					&& Core_File::isValidExtension($sSourceFile,
					Core::$mainConfig['availableExtension']))
					{
						try
						{
							Core_File::copy($sSourceFile, $sTargetFile);
						} catch (Exception $e) {}
					}
				}

				if (/*!is_null($this->_sBigImageFile) && */$this->_sBigImageFile != ''/* && $this->importAction != 2*/)
				{
					// Папка назначения
					$sDestinationFolder = $this->_oCurrentItem->getItemPath();

					// Файл-источник
					$sSourceFile = $this->imagesPath . $this->_sBigImageFile;
					$sSourceFileBaseName = basename($sSourceFile, '');

					if (Core_File::isValidExtension($sSourceFile, Core::$mainConfig['availableExtension']))
					{
						// Удаляем папку назначения вместе со всеми старыми файлами
						//Core_File::deleteDir($sDestinationFolder);

						// Создаем папку назначения
						$this->_oCurrentItem->createDir();

						if (mb_strpos(mb_strtolower($sSourceFile), "http://") === 0)
						{
							// Файл из WEB'а, создаем временный файл
							$sTempFileName = tempnam($sDestinationFolder, "CMS");
							// Копируем содержимое WEB-файла в локальный временный файл
							file_put_contents($sTempFileName, file_get_contents($sSourceFile));
							// Файл-источник равен временному файлу
							$sSourceFile = $sTempFileName;
						}
						else
						{
							$sSourceFile = CMS_FOLDER . trim(Core_File::pathCorrection($sSourceFile), DIRECTORY_SEPARATOR);
						}

						if (!$this->_oCurrentShop->change_filename)
						{
							$sTargetFileName = $sSourceFileBaseName;
						}
						else
						{
							$sTargetFileExtension = Core_File::getExtension($sSourceFileBaseName);

							if ($sTargetFileExtension != '')
							{
								$sTargetFileExtension = ".{$sTargetFileExtension}";
							}

							$sTargetFileName = "shop_items_catalog_image{$this->_oCurrentItem->id}{$sTargetFileExtension}";
						}

						if ($this->_oCurrentItem->image_large != '')
						{
							try
							{
								Core_File::delete($sDestinationFolder . $this->_oCurrentItem->image_large);
							} catch (Exception $e) {}
						}

						// Создаем массив параметров для загрузки картинок элементу
						$aPicturesParam = array();
						$aPicturesParam['large_image_isset'] = TRUE;
						$aPicturesParam['large_image_source'] = $sSourceFile;
						$aPicturesParam['large_image_name'] = $sSourceFileBaseName;
						$aPicturesParam['large_image_target'] = $sDestinationFolder . $sTargetFileName;
						$aPicturesParam['watermark_file_path'] = $this->_oCurrentShop->getWatermarkFilePath();
						$aPicturesParam['watermark_position_x'] = $this->_oCurrentShop->watermark_default_position_x;
						$aPicturesParam['watermark_position_y'] = $this->_oCurrentShop->watermark_default_position_y;
						$aPicturesParam['large_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio;

						// Проверяем, передали ли нам малое изображение
						if (is_null($this->_oCurrentItem->image_small) || $this->_oCurrentItem->image_small == '')
						{
							// Малое изображение не передано, создаем его из большого
							$aPicturesParam['small_image_source'] = $aPicturesParam['large_image_source'];
							$aPicturesParam['small_image_name'] = $aPicturesParam['large_image_name'];
							$aPicturesParam['small_image_target'] = $sDestinationFolder . "small_{$sTargetFileName}";
							$aPicturesParam['create_small_image_from_large'] = TRUE;
							$aPicturesParam['small_image_max_width'] = $this->_oCurrentShop->image_small_max_width;
							$aPicturesParam['small_image_max_height'] = $this->_oCurrentShop->image_small_max_height;
							$aPicturesParam['small_image_watermark'] = $this->_oCurrentShop->watermark_default_use_small_image;
							$aPicturesParam['small_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio_small;
						}
						else
						{
							$aPicturesParam['create_small_image_from_large'] = FALSE;
						}

						$aPicturesParam['large_image_max_width'] = $this->_oCurrentShop->image_large_max_width;
						$aPicturesParam['large_image_max_height'] = $this->_oCurrentShop->image_large_max_height;
						$aPicturesParam['large_image_watermark'] = $this->_oCurrentShop->watermark_default_use_large_image;

						try
						{
							$result = Core_File::adminUpload($aPicturesParam);
						}
						catch (Exception $exc)
						{
							Core_Message::show($exc->getMessage(), 'error');
							$result = array('large_image' => FALSE, 'small_image' => FALSE);
						}

						if ($result['large_image'])
						{
							$this->_oCurrentItem->image_large = $sTargetFileName;
							$this->_oCurrentItem->setLargeImageSizes();
						}

						if ($result['small_image'])
						{
							$this->_oCurrentItem->image_small = "small_{$sTargetFileName}";
							$this->_oCurrentItem->setSmallImageSizes();
						}

						if (strpos(basename($sSourceFile), "CMS") === 0)
						{
							// Файл временный, подлежит удалению
							Core_File::delete($sSourceFile);
						}
					}
				}
				elseif($this->deleteImage)
				{
					// Удалить текущее большое изображение
					if ($this->_oCurrentItem->image_large != '')
					{
						try
						{
							Core_File::delete($this->_oCurrentItem->getItemPath() . $this->_oCurrentItem->image_large);
						} catch (Exception $e) {}
					}
				}

				if ($this->_sSmallImageFile != ''
				|| ($this->_sBigImageFile != ''
				&& !$this->deleteImage))
				{
					$this->_sSmallImageFile == '' && $this->_sSmallImageFile = $this->_sBigImageFile;

					// Папка назначения
					$sDestinationFolder = $this->_oCurrentItem->getItemPath();

					// Файл-источник
					$sSourceFile = $this->imagesPath . $this->_sSmallImageFile;

					$sSourceFileBaseName = basename($sSourceFile, '');

					if (Core_File::isValidExtension(
					$sSourceFile,
					Core::$mainConfig['availableExtension']))
					{
						// Создаем папку назначения
						$this->_oCurrentItem->createDir();

						if (mb_strpos(mb_strtolower($sSourceFile), "http://") === 0)
						{
							// Файл из WEB'а, создаем временный файл
							$sTempFileName = tempnam($sDestinationFolder, "CMS");
							// Копируем содержимое WEB-файла в локальный временный файл
							file_put_contents($sTempFileName, file_get_contents($sSourceFile));
							// Файл-источник равен временному файлу
							$sSourceFile = $sTempFileName;
						}
						else
						{
							$sSourceFile = CMS_FOLDER . trim(Core_File::pathCorrection($sSourceFile), DIRECTORY_SEPARATOR);
						}

						if (!$this->_oCurrentShop->change_filename)
						{
							$sTargetFileName = "small_{$sSourceFileBaseName}";
						}
						else
						{
							$sTargetFileExtension = Core_File::getExtension($sSourceFileBaseName);

							if ($sTargetFileExtension != '')
							{
								$sTargetFileExtension = ".{$sTargetFileExtension}";
							}

							$sTargetFileName = "small_shop_items_catalog_image{$this->_oCurrentItem->id}{$sTargetFileExtension}";
						}

						if (is_file($sSourceFile) && filesize($sSourceFile))
						{
							// Удаляем старое малое изображение
							if ($this->_oCurrentItem->image_small != '')
							{
								try
								{
									Core_File::delete($this->_oCurrentItem->getItemPath() . $this->_oCurrentItem->image_small);
								} catch (Exception $e) {}
							}

							$aPicturesParam = array();
							$aPicturesParam['small_image_source'] = $sSourceFile;
							$aPicturesParam['small_image_name'] = $sSourceFileBaseName;
							$aPicturesParam['small_image_target'] = $sDestinationFolder . $sTargetFileName;
							$aPicturesParam['create_small_image_from_large'] = FALSE;
							$aPicturesParam['small_image_max_width'] = $this->_oCurrentShop->image_small_max_width;
							$aPicturesParam['small_image_max_height'] = $this->_oCurrentShop->image_small_max_height;
							$aPicturesParam['small_image_watermark'] = $this->_oCurrentShop->watermark_default_use_small_image;
							$aPicturesParam['watermark_file_path'] = $this->_oCurrentShop->getWatermarkFilePath();
							$aPicturesParam['watermark_position_x'] = $this->_oCurrentShop->watermark_default_position_x;
							$aPicturesParam['watermark_position_y'] = $this->_oCurrentShop->watermark_default_position_y;
							$aPicturesParam['small_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio_small;

							try {
								$result = Core_File::adminUpload($aPicturesParam);
							} catch (Exception $exc) {
								Core_Message::show($exc->getMessage(), 'error');
								$result = array('small_image' => FALSE);
							}

							if ($result['small_image'])
							{
								$this->_oCurrentItem->image_small = $sTargetFileName;
								$this->_oCurrentItem->setSmallImageSizes();
							}
						}

						if (strpos(basename($sSourceFile), "CMS") === 0)
						{
							// Файл временный, подлежит удалению
							Core_File::delete($sSourceFile);
						}
					}

					$this->_sSmallImageFile = '';
				}
				elseif($this->deleteImage)
				{
					if ($this->_oCurrentItem->image_small != '')
					{
						try
						{
							Core_File::delete($this->_oCurrentItem->getItemPath() . $this->_oCurrentItem->image_small);
						} catch (Exception $e) {}
					}
				}
				$this->_sBigImageFile = '';

				// WARNING
				foreach ($this->_aExternalProperties as $iPropertyID => $sPropertyValue)
				{
					$oProperty = Core_Entity::factory('Property')->find($iPropertyID);

					$iShop_Item_Property_Id = $oProperty->Shop_Item_Property->id;

					$group_id = $this->_oCurrentItem->modification_id == 0
						? $this->_oCurrentItem->shop_group_id
						: $this->_oCurrentItem->Modification->shop_group_id;

					// Проверяем доступность дополнительного свойства для группы товаров
					if (is_null(Core_Entity::factory('Shop', $this->_oCurrentShop->id)
						->Shop_Item_Property_For_Groups
						->getByShopItemPropertyIdAndGroupId($iShop_Item_Property_Id, $group_id)))
					{
						// Свойство не доступно текущей группе, делаем его доступным
						$oShop_Item_Property_For_Group = Core_Entity::factory('Shop_Item_Property_For_Group');
						$oShop_Item_Property_For_Group->shop_group_id = intval($group_id);
						$oShop_Item_Property_For_Group->shop_item_property_id = $iShop_Item_Property_Id;
						$oShop_Item_Property_For_Group->shop_id = $this->_oCurrentShop->id;
						$oShop_Item_Property_For_Group->save();
					}

					$aPropertyValues = $oProperty->getValues($this->_oCurrentItem->id, FALSE);

					if(!isset($this->_aClearedPropertyValues[$this->_oCurrentItem->id]) || !in_array($oProperty->guid, $this->_aClearedPropertyValues[$this->_oCurrentItem->id]))
					{
						foreach($aPropertyValues as $oPropertyValue)
						{
							$oProperty->type == 2 && $oPropertyValue->setDir($this->_oCurrentItem->getItemPath());
							$oPropertyValue->delete();
						}

						$aPropertyValues = array();

						$this->_aClearedPropertyValues[$this->_oCurrentItem->id][] = $oProperty->guid;
					}


					if($oProperty->multiple)
					{
						$oProperty_Value = $oProperty->createNewValue($this->_oCurrentItem->id);
					}
					else
					{
						$oProperty_Value = isset($aPropertyValues[0])
							? $aPropertyValues[0]
							: $oProperty->createNewValue($this->_oCurrentItem->id);
					}

					switch($oProperty->type)
					{
						// Файл
						case 2:

							// Папка назначения
							$sDestinationFolder = $this->_oCurrentItem->getItemPath();

							// Файл-источник
							$sSourceFile = $this->imagesPath . $sPropertyValue;

							$sSourceFileBaseName = basename($sSourceFile, '');

							if (Core_File::isValidExtension($sSourceFile, Core::$mainConfig['availableExtension']))
							{
								// Создаем папку назначения
								$this->_oCurrentItem->createDir();

								if (mb_strpos(mb_strtolower($sSourceFile), "http://") === 0)
								{
									// Файл из WEB'а, создаем временный файл
									$sTempFileName = tempnam($sDestinationFolder, "CMS");
									// Копируем содержимое WEB-файла в локальный временный файл
									file_put_contents($sTempFileName, file_get_contents($sSourceFile));
									// Файл-источник равен временному файлу
									$sSourceFile = $sTempFileName;
								}
								else
								{
									$sSourceFile = CMS_FOLDER . ltrim($sSourceFile, '/\\');
								}

								if (!$this->_oCurrentShop->change_filename)
								{
									$sTargetFileName = $sSourceFileBaseName;
								}
								else
								{
									$sTargetFileExtension = Core_File::getExtension($sSourceFileBaseName);

									if ($sTargetFileExtension != '')
									{
										$sTargetFileExtension = ".{$sTargetFileExtension}";
									}

									$oProperty_Value->save();
									$sTargetFileName = "shop_property_file_{$this->_oCurrentItem->id}_{$oProperty_Value->id}{$sTargetFileExtension}";
									//$sTargetFileName = "shop_property_file_{$this->_oCurrentItem->id}_{$oProperty->id}{$sTargetFileExtension}";
								}

								// Создаем массив параметров для загрузки картинок элементу
								$aPicturesParam = array();
								$aPicturesParam['large_image_isset'] = TRUE;
								$aPicturesParam['large_image_source'] = $sSourceFile;
								$aPicturesParam['large_image_name'] = $sSourceFileBaseName;
								$aPicturesParam['large_image_target'] = $sDestinationFolder . $sTargetFileName;
								$aPicturesParam['watermark_file_path'] = $this->_oCurrentShop->getWatermarkFilePath();
								$aPicturesParam['watermark_position_x'] = $this->_oCurrentShop->watermark_default_position_x;
								$aPicturesParam['watermark_position_y'] = $this->_oCurrentShop->watermark_default_position_y;
								$aPicturesParam['large_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio;
								//$aPicturesParam['large_image_max_width'] = $this->_oCurrentShop->image_large_max_width;
								$aPicturesParam['large_image_max_width'] = $oProperty->image_large_max_width;
								//$aPicturesParam['large_image_max_height'] = $this->_oCurrentShop->image_large_max_height;
								$aPicturesParam['large_image_max_height'] = $oProperty->image_large_max_height;
								$aPicturesParam['large_image_watermark'] = $this->_oCurrentShop->watermark_default_use_large_image;

								if (isset($this->_aExternalPropertiesSmall[$iPropertyID]))
								{
									// Малое изображение передано
									$aPicturesParam['create_small_image_from_large'] = FALSE;
								}
								else
								{
									// Малое изображение не передано
									$aPicturesParam['create_small_image_from_large'] = TRUE;
									$aPicturesParam['small_image_source'] = $aPicturesParam['large_image_source'];
									$aPicturesParam['small_image_name'] = $aPicturesParam['large_image_name'];
									$aPicturesParam['small_image_target'] = $sDestinationFolder . "small_{$sTargetFileName}";
									$aPicturesParam['small_image_max_width'] = $oProperty->image_small_max_width;
									$aPicturesParam['small_image_max_height'] = $oProperty->image_small_max_height;
									$aPicturesParam['small_image_watermark'] = $this->_oCurrentShop->watermark_default_use_small_image;
									$aPicturesParam['small_image_preserve_aspect_ratio'] = $aPicturesParam['large_image_preserve_aspect_ratio'];
								}

								// Удаляем старое большое изображение
								if ($oProperty_Value->file != '')
								{
									try
									{
										Core_File::delete($sDestinationFolder . $oProperty_Value->file);
									} catch (Exception $e) {}
								}

								// Удаляем старое малое изображение
								if ($oProperty_Value->file_small != '')
								{
									try
									{
										Core_File::delete($sDestinationFolder . $oProperty_Value->file_small);
									} catch (Exception $e) {}
								}

								try {
									$aResult = Core_File::adminUpload($aPicturesParam);
								} catch (Exception $exc) {
									Core_Message::show($exc->getMessage(), 'error');
									$aResult = array('large_image' => FALSE, 'small_image' => FALSE);
								}

								if ($aResult['large_image'])
								{
									$oProperty_Value->file = $sTargetFileName;
									$oProperty_Value->file_name = '';
								}

								if ($aResult['small_image'])
								{
									$oProperty_Value->file_small = "small_{$sTargetFileName}";
									$oProperty_Value->file_small_name = '';
								}

								if (strpos(basename($sSourceFile), "CMS") === 0)
								{
									// Файл временный, подлежит удалению
									Core_File::delete($sSourceFile);
								}
							}
						break;
						// Список
						case 3:
							if (Core::moduleIsActive('list'))
							{
								$oListItem = Core_Entity::factory('List_Item');
								$oListItem
									->queryBuilder()
									->where('list_id', '=', $oProperty->list_id)
									->where('value', '=', $sPropertyValue)
								;
								$oListItem = $oListItem->findAll(FALSE);

								if (count($oListItem) > 0)
								{
									$oProperty_Value->setValue($oListItem[0]->id);
								}
								else
								{
									$oProperty_Value->setValue(Core_Entity::factory('List_Item')
										->list_id($oProperty->list_id)
										->value($sPropertyValue)
										->save()
										->id
									);
								}
							}
						break;
				   case 8:
					  if (!preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})/", $sPropertyValue))
					  {
						 $sPropertyValue = Core_Date::datetime2sql($sPropertyValue);
					  }

					  $oProperty_Value->setValue($sPropertyValue);
				   break;
				   case 9:
					  if (!preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $sPropertyValue))
					  {
						 $sPropertyValue = Core_Date::datetime2sql($sPropertyValue);
					  }

					  $oProperty_Value->setValue($sPropertyValue);
				   break;
						default:
							$oProperty_Value->setValue($sPropertyValue);
						break;
					}

					$oProperty_Value->save();
				}

				foreach ($this->_aExternalPropertiesSmall as $iPropertyID => $sPropertyValue)
				{
					// Проверяем доступность дополнительного свойства для группы товаров
					if (Core_Entity::factory('Shop', $this->_oCurrentShop->id)
						->Shop_Item_Property_For_Groups
						->getByShopItemPropertyIdAndGroupId($iPropertyID, $this->_oCurrentGroup->id))
					{
						// Свойство не доступно текущей группе, делаем его доступным
						Core_Entity::factory('Shop_Item_Property_For_Group')
							->shop_group_id($this->_oCurrentGroup->id)
							->shop_item_property_id($iPropertyID)
							->shop_id($this->_oCurrentShop->id)
							->save();
					}

					$oProperty = Core_Entity::factory('Property')->find($iPropertyID);

					$aPropertyValues = $oProperty->getValues($this->_oCurrentItem->id, FALSE);

					$oProperty_Value = isset($aPropertyValues[0])
						? $aPropertyValues[0]
						: $oProperty->createNewValue($this->_oCurrentItem->id);

					// Папка назначения
					$sDestinationFolder = $this->_oCurrentItem->getItemPath();

					// Файл-источник
					$sSourceFile = $this->imagesPath . $sPropertyValue;

					$sSourceFileBaseName = basename($sSourceFile, '');

					if (Core_File::isValidExtension(
					$sSourceFile,
					Core::$mainConfig['availableExtension']))
					{
						// Создаем папку назначения
						$this->_oCurrentItem->createDir();

						if (mb_strpos(mb_strtolower($sSourceFile), "http://") === 0)
						{
							// Файл из WEB'а, создаем временный файл
							$sTempFileName = tempnam($sDestinationFolder, "CMS");
							// Копируем содержимое WEB-файла в локальный временный файл
							file_put_contents($sTempFileName, file_get_contents($sSourceFile));
							// Файл-источник равен временному файлу
							$sSourceFile = $sTempFileName;
						}
						else
						{
							$sSourceFile = CMS_FOLDER . $sSourceFile;
						}

						if (!$this->_oCurrentShop->change_filename)
						{
							$sTargetFileName = "small_{$sSourceFileBaseName}";
						}
						else
						{
							$sTargetFileExtension = Core_File::getExtension($sSourceFileBaseName);

							if ($sTargetFileExtension != '')
							{
								$sTargetFileExtension = ".{$sTargetFileExtension}";
							}

							$oProperty_Value->save();
							$sTargetFileName = "small_shop_property_file_{$this->_oCurrentItem->id}_{$oProperty_Value->id}{$sTargetFileExtension}";
						}

						$aPicturesParam = array();
						$aPicturesParam['small_image_source'] = $sSourceFile;
						$aPicturesParam['small_image_name'] = $sSourceFileBaseName;
						$aPicturesParam['small_image_target'] = $sDestinationFolder . $sTargetFileName;
						$aPicturesParam['create_small_image_from_large'] = FALSE;
						$aPicturesParam['small_image_max_width'] = $this->_oCurrentShop->image_small_max_width;
						$aPicturesParam['small_image_max_height'] = $this->_oCurrentShop->image_small_max_height;
						$aPicturesParam['small_image_watermark'] = $this->_oCurrentShop->watermark_default_use_small_image;
						$aPicturesParam['watermark_file_path'] = $this->_oCurrentShop->getWatermarkFilePath();
						$aPicturesParam['watermark_position_x'] = $this->_oCurrentShop->watermark_default_position_x;
						$aPicturesParam['watermark_position_y'] = $this->_oCurrentShop->watermark_default_position_y;
						$aPicturesParam['small_image_preserve_aspect_ratio'] = $this->_oCurrentShop->preserve_aspect_ratio;

						// Удаляем старое малое изображение
						if ($oProperty_Value->file_small != '')
						{
							try
							{
								Core_File::delete($sDestinationFolder . $oProperty_Value->file_small);
							} catch (Exception $e) {}
						}

						try {
							$aResult = Core_File::adminUpload($aPicturesParam);
						} catch (Exception $exc) {
							Core_Message::show($exc->getMessage(), 'error');
							$aResult = array('large_image' => FALSE, 'small_image' => FALSE);
						}

						if ($aResult['small_image'])
						{
							$oProperty_Value->file_small = $sTargetFileName;
							$oProperty_Value->file_small_name = '';
						}

						if (strpos(basename($sSourceFile), "CMS") === 0)
						{
							// Файл временный, подлежит удалению
							Core_File::delete($sSourceFile);
						}
					}

					$oProperty_Value->save();
				}

				foreach ($this->_aExternalPrices as $iPriceID => $sPriceValue)
				{
					$oShop_Item_Price = Core_Entity::factory('Shop_Item', $this->_oCurrentItem->id)
						->Shop_Item_Prices
						->getByPriceId($iPriceID);

					if (is_null($oShop_Item_Price))
					{
						$oShop_Item_Price = Core_Entity::factory('Shop_Item_Price');
						$oShop_Item_Price->shop_item_id = $this->_oCurrentItem->id;
						$oShop_Item_Price->shop_price_id = $iPriceID;
					}

					$oShop_Item_Price->value($sPriceValue);
					$oShop_Item_Price->save();
				}
			}

			$iCounter++;

			//$this->_oCurrentItem->clear();
			$this->_oCurrentItem = Core_Entity::factory('Shop_Item');
			$this->_oCurrentGroup =  Core_Entity::factory('Shop_Group', $this->_iCurrentGroupId);
			$this->_oCurrentItem->shop_group_id = $this->_oCurrentGroup->id;

			if(!is_null($this->_oCurrentOrderItem))
			{
				$this->_oCurrentOrder->add($this->_oCurrentOrderItem);
			}
			$this->_oCurrentOrder = NULL;
			$this->_oCurrentOrderItem = NULL;


			// Очищаем временные массивы
			$this->_aExternalPrices = array();
			$this->_aWarehouses = array();
			$this->_aExternalPropertiesSmall = array();
			$this->_aExternalProperties = array();
			$this->_aAdditionalGroups = array();

			// Список меток для текущего товара
			$this->_sCurrentTags = '';
			// Артикул родительского товара - признак того, что данный товар сопутствует товару с данным артикулом
			$this->_sAssociatedItemMark = '';
			// Текущий электронный товар
			$this->_oCurrentShopEItem->clear();
			// Текущая специальная цена для товара
			$this->_oCurrentShopSpecialPrice->clear();
		}

		$iCurrentSeekPosition = !$aCsvLine ? $aCsvLine : ftell($fInputFile);

		fclose($fInputFile);

		return $iCurrentSeekPosition;
	}

	/**
	 * Convert object to string
	 * @return string
	 */
	public function __toString()
	{
		$aReturn = array();

		foreach ($this->_allowedProperties as $propertyName)
		{
			$aReturn[] = $propertyName . '=' . $this->$propertyName;
		}

		return implode(', ', $aReturn) . "<br/>";
	}

	/**
	 * Get CSV line from file
	 * @param handler file descriptor
	 * @return array
	 */
	public function getCSVLine($fileDescriptor)
	{
		if (strtoupper($this->encoding) != 'UTF-8' && defined('ALT_SITE_LOCALE'))
		{
			setlocale(LC_ALL, ALT_SITE_LOCALE);
		}

		$aCsvLine = @fgetcsv($fileDescriptor, 0, $this->separator, $this->limiter);

		if ($aCsvLine === FALSE)
		{
			return $aCsvLine;
		}

		setlocale(LC_ALL, SITE_LOCAL);
		setlocale(LC_NUMERIC, 'POSIX');

		return self::CorrectToEncoding($aCsvLine, 'UTF-8', $this->encoding);
	}

	public function clear()
	{
		$this->_oCurrentShop =
		$this->_oCurrentGroup =
		$this->_oCurrentItem =
		$this->_oCurrentOrder =
		$this->_oCurrentOrderItem =
		$this->_oCurrentShopEItem =
		$this->_oCurrentShopSpecialPrice = NULL;

		return $this;
	}

    public function __sleep()
    {
			$this->clear();

			return array_keys(
				get_object_vars($this)
			);
    }

	/**
	 * Reestablish any database connections that may have been lost during serialization and perform other reinitialization tasks
	 * @return self
	 */
	public function __wakeup()
	{
		date_default_timezone_set(Core::$mainConfig['timezone']);

		// Инициализация текущей группы товаров
		$this->_oCurrentGroup = Core_Entity::factory('Shop_Group', $this->_iCurrentGroupId
			? $this->_iCurrentGroupId
			: NULL);

		$this->init();

		$this->_oCurrentGroup->shop_id = $this->_oCurrentShop->id;

		// Инициализация текущего товара
		$this->_oCurrentItem = Core_Entity::factory('Shop_Item');
		$this->_oCurrentItem->shop_group_id = intval($this->_oCurrentGroup->id);

		// Инициализация текущего электронного товара
		$this->_oCurrentShopEItem = Core_Entity::factory('Shop_Item_Digital');

		// Инициализация текущей специальной цены для товара
		$this->_oCurrentShopSpecialPrice = Core_Entity::factory('Shop_Specialprice');

		return $this;
	}

	/**
	 * Correct CSV-line encoding
	 * @param array $sLine current CSV-file line
	 * @param string $encodeTo detination encoding
	 * @param string $encodeFrom source encoding
	 * @return array
	 */
	public static function CorrectToEncoding($sLine, $encodeTo, $encodeFrom = 'UTF-8')
	{
		if (is_array($sLine))
		{
			if (count($sLine) > 0)
			{
				foreach ($sLine as $key => $value)
				{
					$sLine[$key] = self::CorrectToEncoding($value, $encodeTo, $encodeFrom);
				}
			}
		}
		else
		{
			// Если кодировки не совпадают
			if (strtoupper($encodeTo) != strtoupper($encodeFrom))
			{
				// Перекодируем в указанную кодировку
				$sLine = @iconv($encodeFrom, $encodeTo . "//IGNORE//TRANSLIT", $sLine);
			}
		}

		return $sLine;
	}
}