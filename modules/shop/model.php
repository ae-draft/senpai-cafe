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
class Shop_Model extends Core_Entity{
	/**
	 * Model name
	 * @var mixed
	 */
	protected $_modelName = 'shop';

	/**
	 * Backend property
	 * @var int
	 */	public $img = 1;

	/**
	 * Backend property
	 * @var string
	 */	public $shop_currency_name = '';

	/**
	 * Backend property
	 * @var string
	 */
	public $img_transactions = NULL;

	/**
	 * Backend property
	 * @var string
	 */
	public $currency_name = NULL;
	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */	protected $_hasMany = array(		'affiliate_plan' => array('through' => 'shop_affiliate_plan'),
		'shop_affiliate_plan' => array(),
		'shop_cart' => array(),
		'shop_delivery' => array(),
		'shop_discount' => array(),
		'shop_group' => array(),
		'shop_group_property' => array(),
		'shop_group_property_dir' => array(),
		'shop_item' => array(),
		'shop_item_property' => array(),
		'shop_item_property_dir' => array(),
		'shop_order' => array(),
		'shop_order_property' => array(),
		'shop_order_property_dir' => array(),
		'shop_payment_system' => array(),
		'shop_price' => array(),
		'shop_producer' => array(),
		'shop_purchase_discount' => array(),
		'shop_seller' => array(),
		'shop_siteuser_transaction' => array(),
		'shop_warehouse' => array(),
		'shop_item_property_for_group' => array(),	);
	/**
	 * List of preloaded values
	 * @var array
	 */	protected $_preloadValues = array(
		'use_captcha' => 1,		'image_small_max_width' => 100,		'image_large_max_width' => 800,		'image_small_max_height' => 100,		'image_large_max_height' => 800,		'group_image_small_max_width' => 100,		'group_image_large_max_width' => 800,		'group_image_small_max_height' => 100,		'group_image_large_max_height' => 800,		'group_image_large_max_height' => 800,
		'items_sorting_field' => 0,
		'items_sorting_direction' => 0,
		'groups_sorting_field' => 0,
		'groups_sorting_direction' => 0,
		'url_type' => 0,
		'apply_tags_automatically' => 1,
		'write_off_paid_items' => 0,
		'comment_active' => 0,		'format_date' => '%d.%m.%Y',		'format_datetime' => '%d.%m.%Y %H:%M:%S',		'typograph_default_items' => 1,		'typograph_default_groups' => 1,		'watermark_default_position_x' => '50%',		'watermark_default_position_y' => '100%',		'preserve_aspect_ratio' => 1,		'items_on_page' => 10,		'watermark_file' => '',
		'producer_image_small_max_width' => 100,
		'producer_image_large_max_width' => 800,
		'producer_image_small_max_height' => 100,
		'producer_image_large_max_height' => 800,	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(		'shop_dir' => array(),		'site' => array(),		'structure' => array(),		'shop_sountry' => array(),		'shop_currency' => array(),		'shop_order_status' => array(),		'shop_measure' => array(),		'user' => array(),		'siteuser_group' => array(),		'shop_company' => array(),		'shop_country' => array()	);
	/**
	 * Forbidden tags. If list of tags is empty, all tags will be shown.
	 * @var array
	 */
	protected $_forbiddenTags = array(
		'size_measure',
	);

	/**
	 * Tree of groups
	 * @var array
	 */
	protected $_groupsTree = array();

	/**
	 * Cache of groups
	 * @var array
	 */
	protected $_cacheGroups = array();

	/**
	 * Cache of items
	 * @var array
	 */
	protected $_cacheItems = array();

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */	public function __construct($id = NULL)	{		parent::__construct($id);		if (is_null($id))		{			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;			$this->_preloadValues['site_id'] = defined('CURRENT_SITE') ? CURRENT_SITE : 0;			$this->_preloadValues['guid'] = Core_Guid::get();		}	}
	/**
	 * Get shop by structure id.
	 * @param int $structure_id
	 * @return Shop_Model|NULL
	 */	public function getByStructureId($structure_id)	{		$this->queryBuilder()			->clear()			->where('structure_id', '=', $structure_id)			->limit(1);		$aShops = $this->findAll();		if (isset($aShops[0]))		{			return $aShops[0];		}		return NULL;	}
	/**
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Shop_Model
	 */	public function delete($primaryKey = NULL)	{		if (is_null($primaryKey))		{			$primaryKey = $this->getPrimaryKey();		}		$this->id = $primaryKey;

		// Fix bug with 'deleted' relations
		$this->deleted = 0;
		$this->save();
		// Доп. свойства товаров
		$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $this->id);
		$oShop_Item_Property_List->Properties->deleteAll(FALSE);
		$oShop_Item_Property_List->Property_Dirs->deleteAll(FALSE);

		// Доп. свойства групп
		$oShop_Group_Property_List = Core_Entity::factory('Shop_Group_Property_List', $this->id);
		$oShop_Group_Property_List->Properties->deleteAll(FALSE);
		$oShop_Group_Property_List->Property_Dirs->deleteAll(FALSE);

		// Доп. свойства заказов
		$oShop_Order_Property_List = Core_Entity::factory('Shop_Order_Property_List', $this->id);
		$oShop_Order_Property_List->Properties->deleteAll(FALSE);
		$oShop_Order_Property_List->Property_Dirs->deleteAll(FALSE);

		$this->Shop_Item_Property_Dirs->deleteAll(FALSE);
		$this->Shop_Item_Properties->deleteAll(FALSE);
		$this->Shop_Group_Property_Dirs->deleteAll(FALSE);
		$this->Shop_Group_Properties->deleteAll(FALSE);
		$this->Shop_Order_Property_Dirs->deleteAll(FALSE);
		$this->Shop_Order_Properties->deleteAll(FALSE);
		$this->Shop_Affiliate_Plans->deleteAll(FALSE);
		$this->Shop_Carts->deleteAll(FALSE);
		$this->Shop_Deliveries->deleteAll(FALSE);
		$this->Shop_Discounts->deleteAll(FALSE);
		$this->Shop_Groups->deleteAll(FALSE);
		$this->Shop_Items->deleteAll(FALSE);
		$this->Shop_Orders->deleteAll(FALSE);
		$this->Shop_Payment_Systems->deleteAll(FALSE);
		$this->Shop_Prices->deleteAll(FALSE);
		$this->Shop_Producers->deleteAll(FALSE);
		$this->Shop_Purchase_Discounts->deleteAll(FALSE);
		$this->Shop_Sellers->deleteAll(FALSE);
		$this->Shop_Siteuser_Transactions->deleteAll(FALSE);
		$this->Shop_Warehouses->deleteAll(FALSE);
		$this->Shop_Item_Property_For_Groups->deleteAll(FALSE);

		// Shop dir
		Core_File::deleteDir($this->getPath());
		return parent::delete($primaryKey);	}
	/**
	 * Get watermark file path
	 * @return string|NULL
	 */	public function getWatermarkFilePath()	{		return $this->watermark_file != ''
			? $this->getPath() . '/watermarks/' . $this->watermark_file
			: NULL;	}
	/**
	 * Get watermark file href
	 * @return string
	 */	public function getWatermarkFileHref()	{		return '/' . $this->getHref() . '/watermarks/' . $this->watermark_file;	}
	/**
	 * Get shop path include CMS_FOLDER
	 * @return string
	 */
	public function getPath()
	{
		return CMS_FOLDER . $this->getHref();
	}

	/**
	 * Get shop href
	 * @return string
	 */
	public function getHref()
	{
		return $this->Site->uploaddir . "shop_" . intval($this->id);
	}

	/**
	 * Save watermark file
	 * @param string $fileSourcePath file to upload
	 */	public function saveWatermarkFile($fileSourcePath)	{		$this->watermark_file = 'shop_watermark_' . $this->id . '.png';		$this->save();		Core_File::upload($fileSourcePath, $this->getWatermarkFilePath());	}
	/**
	 * Save object. Use self::update() or self::create()
	 * @return Shop_Model
	 */	public function save()	{		parent::save();		// Создание директории для Watermark		$sWatermarkDirPath = $this->getPath() . '/watermarks';
		if (!is_dir($sWatermarkDirPath))		{			try			{				Core_File::mkdir($sWatermarkDirPath, CHMOD, TRUE);			} catch (Exception $e) {}		}		return $this;	}
	/**
	 * Delete watermark file
	 */	public function deleteWatermarkFile()	{		try		{			Core_File::delete($this->getWatermarkFilePath());		} catch (Exception $e) {}		$this->watermark_file = '';		$this->save();	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{		$newObject = parent::copy();		try		{			is_file($this->getWatermarkFilePath()) && Core_File::copy($this->getWatermarkFilePath(), $newObject->getWatermarkFilePath());		} catch (Exception $e) {}		// Копирование доп. свойств и разделов доп. свойств товаров
		$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $this->id);

		// Linked object for new shop
		$oNewObject_Shop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $newObject->id);

		$oProperty_Dir = $oShop_Item_Property_List->Property_Dirs;
		//$oProperty_Dir->queryBuilder()->where('parent_id', '=', 0);
		$aProperty_Dirs = $oProperty_Dir->findAll();

		$aMatchProperty_Dirs = array();
		foreach($aProperty_Dirs as $oProperty_Dir)
		{
			//$oNewProperty_Dir = $oProperty_Dir->copy();
			$oNewProperty_Dir = clone $oProperty_Dir;
			$oNewObject_Shop_Item_Property_List->add($oNewProperty_Dir);

			$aMatchProperty_Dirs[$oProperty_Dir->id] = $oNewProperty_Dir;
		}

		$oNewProperty_Dirs = $oNewObject_Shop_Item_Property_List->Property_Dirs->findAll();

		foreach($oNewProperty_Dirs as $oNewProperty_Dir)
		{
			if (isset($aMatchProperty_Dirs[$oNewProperty_Dir->parent_id]))
			{
				$oNewProperty_Dir->parent_id = $aMatchProperty_Dirs[$oNewProperty_Dir->parent_id]->id;
				$oNewProperty_Dir->save();
			}
		}

		$oProperty = $oShop_Item_Property_List->Properties;
		//$oProperty->queryBuilder()->where('property_dir_id', '=', 0);
		$aProperties = $oProperty->findAll();

		foreach($aProperties as $oProperty)
		{
			//$oNewProperty = $oProperty->copy(FALSE);
			$oNewProperty = clone $oProperty;
			$oNewObject_Shop_Item_Property_List->add($oNewProperty);
		}

		$oNewProperties = $oNewObject_Shop_Item_Property_List->Properties->findAll();
		foreach($oNewProperties as $oNewProperty)
		{
			if (isset($aMatchProperty_Dirs[$oNewProperty->property_dir_id]))
			{
				$oNewProperty->property_dir_id = $aMatchProperty_Dirs[$oNewProperty->property_dir_id]->id;
				$oNewProperty->save();
			}
		}

		// Копирование доп. свойств и разделов доп. свойств групп товаров
		$oShop_Group_Property_List = Core_Entity::factory('Shop_Group_Property_List', $this->id);
		$oNewObject_Shop_Group_Property_List = Core_Entity::factory('Shop_Group_Property_List', $newObject->id);

		$oProperty_Dir = $oShop_Group_Property_List->Property_Dirs;
		//$oProperty_Dir->queryBuilder()->where('parent_id', '=', 0);
		$aProperty_Dirs = $oProperty_Dir->findAll();

		$aMatchProperty_Dirs = array();
		foreach($aProperty_Dirs as $oProperty_Dir)
		{
			$oNewProperty_Dir = clone $oProperty_Dir;

			$oNewObject_Shop_Group_Property_List->add($oNewProperty_Dir);

			$aMatchProperty_Dirs[$oProperty_Dir->id] = $oNewProperty_Dir;
			/*
			$oNewObject_Shop_Group_Property_List->add(
				$oProperty_Dir->copy()
			);
			*/
		}

		$oNewProperty_Dirs = $oNewObject_Shop_Group_Property_List->Property_Dirs->findAll();

		foreach($oNewProperty_Dirs as $oNewProperty_Dir)
		{
			if (isset($aMatchProperty_Dirs[$oNewProperty_Dir->parent_id]))
			{
				$oNewProperty_Dir->parent_id = $aMatchProperty_Dirs[$oNewProperty_Dir->parent_id]->id;
				$oNewProperty_Dir->save();
			}
		}

		$oProperty = $oShop_Group_Property_List->Properties;
		//$oProperty->queryBuilder()->where('property_dir_id', '=', 0);
		$aProperties = $oProperty->findAll();

		foreach($aProperties as $oProperty)
		{
			$oNewProperty = clone $oProperty;

			$oNewObject_Shop_Group_Property_List->add($oNewProperty);
			/*
			$oNewObject_Shop_Group_Property_List->add(
				$oProperty->copy(FALSE)
			);
			*/
		}

		$oNewProperties = $oNewObject_Shop_Group_Property_List->Properties->findAll();
		foreach($oNewProperties as $oNewProperty)
		{
			if (isset($aMatchProperty_Dirs[$oNewProperty->property_dir_id]))
			{
				$oNewProperty->property_dir_id = $aMatchProperty_Dirs[$oNewProperty->property_dir_id]->id;
				$oNewProperty->save();
			}
		}

		// Копирование доп. свойств и разделов доп. свойств заказов
		$oShop_Order_Property_List = Core_Entity::factory('Shop_Order_Property_List', $this->id);
		$oNewObject_Shop_Order_Property_List = Core_Entity::factory('Shop_Order_Property_List', $newObject->id);

		$aProperty_Dirs = $oShop_Order_Property_List->Property_Dirs->findAll();

		$aMatchProperty_Dirs = array();
		foreach($aProperty_Dirs as $oProperty_Dir)
		{
			$oNewProperty_Dir = clone $oProperty_Dir;
			$oNewObject_Shop_Order_Property_List->add($oNewProperty_Dir);
			$aMatchProperty_Dirs[$oProperty_Dir->id] = $oNewProperty_Dir;
		}

		$oNewProperty_Dirs = $oNewObject_Shop_Order_Property_List->Property_Dirs->findAll();
		foreach($oNewProperty_Dirs as $oNewProperty_Dir)
		{
			if (isset($aMatchProperty_Dirs[$oNewProperty_Dir->parent_id]))
			{
				$oNewProperty_Dir->parent_id = $aMatchProperty_Dirs[$oNewProperty_Dir->parent_id]->id;
				$oNewProperty_Dir->save();
			}
		}

		$aProperties = $oShop_Order_Property_List->Properties->findAll();
		foreach($aProperties as $oProperty)
		{
			$oNewProperty = clone $oProperty;
			$oNewObject_Shop_Order_Property_List->add($oNewProperty);
		}

		$oNewProperties = $oNewObject_Shop_Order_Property_List->Properties->findAll();
		foreach($oNewProperties as $oNewProperty)
		{
			if (isset($aMatchProperty_Dirs[$oNewProperty->property_dir_id]))
			{
				$oNewProperty->property_dir_id = $aMatchProperty_Dirs[$oNewProperty->property_dir_id]->id;
				$oNewProperty->save();
			}
		}

		// Копирование связи (!) с партнерскими программами
		$aAffiliate_Plans = $this->Affiliate_Plans->findAll();
		foreach($aAffiliate_Plans as $oAffiliate_Plan)
		{
			$newObject->add($oAffiliate_Plan);
		}

		// Копирование типов и условий доставки
		$aShop_Deliveries = $this->Shop_Deliveries->findAll();
		foreach($aShop_Deliveries as $oShop_Delivery)
		{
			$newObject->add(
				$oShop_Delivery->copy()
			);
		}

		// Копирование скидок на товары
		$aShop_Discounts = $this->Shop_Discounts->findAll();
		foreach($aShop_Discounts as $oShop_Discount)
		{
			$newObject->add(
				$oShop_Discount->copy()
			);
		}

		// Копирование платежных систем
		$aShop_Payment_Systems = $this->Shop_Payment_Systems->findAll();
		foreach($aShop_Payment_Systems as $oShop_Payment_System)
		{
			$newObject->add($oShop_Payment_System->copy());
		}

		// Копирование цен
		$aShop_Prices = $this->Shop_Prices->findAll();
		foreach($aShop_Prices as $Shop_Price)
		{
			$newObject->add($Shop_Price->copy());
		}

		// Копирование производителей
		$aShop_Producers = $this->Shop_Producers->findAll();
		foreach($aShop_Producers as $oShop_Producer)
		{
			$newObject->add($oShop_Producer->copy());
		}

		// Копирование скидок от суммы заказа
		$aShop_Purchase_Discounts = $this->Shop_Purchase_Discounts->findAll();
		foreach($aShop_Purchase_Discounts as $oShop_Purchase_Discount)
		{
			$newObject->add($oShop_Purchase_Discount->copy());
		}

		// Копирование продавцов
		$aShop_Sellers = $this->Shop_Sellers->findAll();
		foreach($aShop_Sellers as $oShop_Seller)
		{
			$newObject->add($oShop_Seller->copy());
		}

		// Копирование складов
		$aShop_Warehouses = $this->Shop_Warehouses->findAll();
		foreach($aShop_Warehouses as $oShop_Warehouse)
		{
			$newObject->add($oShop_Warehouse->copy());
		}
		return $newObject;	}
	/**
	 * Recount items and subgroups
	 * @return Shop_Model
	 */
	public function recount()
	{
		$shop_id = $this->id;

		$this->_groupsTree = array();
		$queryBuilder = Core_QueryBuilder::select('id', 'parent_id')
			->from('shop_groups')
			->where('shop_groups.shop_id', '=', $shop_id)
			->where('shop_groups.active', '=', 1)
			->where('shop_groups.deleted', '=', 0);

		$aShop_Groups = $queryBuilder->execute()->asAssoc()->result();

		foreach($aShop_Groups as $aShop_Group)
		{
			$this->_groupsTree[$aShop_Group['parent_id']][] = $aShop_Group['id'];
		}

		$this->_cacheGroups = array();

		$queryBuilder = Core_QueryBuilder::select('parent_id', array('COUNT(id)', 'count'))
			->from('shop_groups')
			->where('shop_groups.shop_id', '=', $shop_id)
			->where('shop_groups.active', '=', 1)
			->where('shop_groups.deleted', '=', 0)
			->groupBy('parent_id');

		$aShop_Groups = $queryBuilder->execute()->asAssoc()->result();

		foreach($aShop_Groups as $aShop_Group)
		{
			$this->_cacheGroups[$aShop_Group['parent_id']] = $aShop_Group['count'];
		}

		$this->_cacheItems = array();

		$current_date = date('Y-m-d H:i:s');

		$queryBuilder->clear()
			->select('shop_group_id', array('COUNT(id)', 'count'))
			->from('shop_items')
			->where('shop_items.shop_id', '=', $shop_id)
			->where('shop_items.active', '=', 1)
			->where('shop_items.start_datetime', '<=', $current_date)
			->open()
			->where('shop_items.end_datetime', '>=', $current_date)
			->setOr()
			->where('shop_items.end_datetime', '=', '0000-00-00 00:00:00')
			->close()
			//->where('siteuser_group_id', 'IN', $mas_result)
			->where('shop_items.deleted', '=', 0)
			->groupBy('shop_group_id');

		$aShop_Items = $queryBuilder->execute()->asAssoc()->result();
		foreach($aShop_Items as $Shop_Item)
		{
			$this->_cacheItems[$Shop_Item['shop_group_id']] = $Shop_Item['count'];
		}

		// DISABLE KEYS
		Core_DataBase::instance()->setQueryType(5)->query("ALTER TABLE `shop_groups` DISABLE KEYS");

		$this->_callSubgroup();

		// ENABLE KEYS
		Core_DataBase::instance()->setQueryType(5)->query("ALTER TABLE `shop_groups` ENABLE KEYS");

		$this->_groupsTree = $this->_cacheGroups = $this->_cacheItems = array();

		return $this;
	}

	/**
	 * Delete empty groups in UPLOAD path for shop
	 */
	public function deleteEmptyDirs()
	{
		Core_File::deleteEmptyDirs($this->getPath());
		return FALSE;
	}

	/**
	 * Recount subgroups
	 * @param int $parent_id parent group ID
	 * @return array
	 */
	protected function _callSubgroup($parent_id = 0)
	{
		$return = array(
			'subgroups' => 0,
			'subgroups_total' => 0,
			'items' => 0,
			'items_total' => 0
		);

		if (isset($this->_groupsTree[$parent_id]))
		{
			foreach($this->_groupsTree[$parent_id] as $groupId)
			{
				$aTmp = $this->_callSubgroup($groupId);
				$return['subgroups_total'] += $aTmp['subgroups_total'];
				$return['items_total'] += $aTmp['items_total'];
			}
		}

		if (isset($this->_cacheGroups[$parent_id]))
		{
			$return['subgroups'] = $this->_cacheGroups[$parent_id];
			$return['subgroups_total'] += $return['subgroups'];
		}

		if (isset($this->_cacheItems[$parent_id]))
		{
			$return['items'] = $this->_cacheItems[$parent_id];
			$return['items_total'] += $return['items'];
		}

		if ($parent_id)
		{
			$oShop_Group = Core_Entity::factory('Shop_Group', $parent_id);
			$oShop_Group->subgroups_count = $return['subgroups'];
			$oShop_Group->subgroups_total_count = $return['subgroups_total'];
			$oShop_Group->items_count = $return['items'];
			$oShop_Group->items_total_count = $return['items_total'];
			$oShop_Group->setCheck(FALSE)->save();
		}

		return $return;
	}

	/**
	 * Get first shop's admin email
	 * @return string
	 */
	public function getFirstEmail()
	{
		$aEmails = trim($this->email) != ''
			? explode(',', $this->email)
			: array(EMAIL_TO);

		return trim($aEmails[0]);
	}

	/**
	 * Backend callback method
	 * @return float
	 */
	public function adminTransactionAmount()
	{
		$siteuser_id = Core_Array::getGet('siteuser_id');

		$aShop_Siteuser_Transactions = Core_Entity::factory('Siteuser', $siteuser_id)->Shop_Siteuser_Transactions->getByShop($this->id);

		$amount = 0;
		foreach ($aShop_Siteuser_Transactions as $oShop_Siteuser_Transaction)
		{
			if ($oShop_Siteuser_Transaction->active)
			{
				$amount += $oShop_Siteuser_Transaction->amount_base_currency;
			}
		}

		return $amount;
	}

	/**
	 * Get XML for entity and children entities
	 * @return string
	 * @hostcms-event shop.onBeforeRedeclaredGetXml
	 */
	public function getXml()
	{
		Core_Event::notify($this->_modelName . '.onBeforeRedeclaredGetXml', $this);

		$this->clearXmlTags()
			->addXmlTag('url', $this->Structure->getPath())
			->addXmlTag('captcha_id', $this->use_captcha ? Core_Captcha::getCaptchaId() : 0)
			;

		$this->shop_currency_id && $this->addEntity($this->Shop_Currency);
		$this->shop_measure_id && $this->addEntity($this->Shop_Measure);

		$this->addEntity(
			Core::factory('Core_Xml_Entity')
					->name('size_measure')
					->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('name')
							->value(Core::_('Shop.size_measure_' . $this->size_measure))
					)
		);

		// Warehouses
		$this->addEntities($this->Shop_Warehouses->findAll());

		$oShop_Items = $this->Shop_Items;
		$oShop_Items->queryBuilder()
			->where('shop_items.shop_group_id', '=', 0);
		$iCountItems = $oShop_Items->getCount();

		$aShop_Groups = $this->Shop_Groups->getByParentId(0);
		$iCountGroups = count($aShop_Groups);

		$array = array(
			'items_count' => $iCountItems,
			'items_total_count' => $iCountItems,
			'subgroups_count' => $iCountGroups,
			'subgroups_total_count' => $iCountGroups
		);

		foreach ($aShop_Groups as $oShop_Group)
		{
			$array['items_total_count'] += $oShop_Group->items_total_count;
			$array['subgroups_total_count'] += $oShop_Group->subgroups_total_count;
		}

		$this
			->addXmlTag('items_count', $array['items_count'])
			->addXmlTag('items_total_count', $array['items_total_count'])
			->addXmlTag('subgroups_count', $array['subgroups_count'])
			->addXmlTag('subgroups_total_count', $array['subgroups_total_count']);

		return parent::getXml();
	}
}