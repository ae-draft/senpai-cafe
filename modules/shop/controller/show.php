<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Показ магазина.
 *
 * Доступные методы:
 *
 * - group($id) идентификатор группы магазина, если FALSE, то вывод товаров осуществляется из всех групп
 * - groupsProperties(TRUE|FALSE|array()) выводить значения дополнительных свойств групп, по умолчанию FALSE. Может принимать массив с идентификаторами дополнительных свойств, значения которых необходимо вывести.
 * - groupsPropertiesList(TRUE|FALSE) выводить список дополнительных свойств групп товаров, по умолчанию TRUE
 * - propertiesForGroups(array()) устанавливает дополнительное ограничение на вывод значений дополнительных свойств групп для массива идентификаторов групп.
 * - groupsMode('tree') режим показа групп, может принимать следующие значения:
	none - не показывать группы,
	tree - показывать дерево групп и все группы на текущем уровне (по умолчанию),
	all - показывать все группы.
 * - groupsForbiddenTags(array('description')) массив тегов групп, запрещенных к передаче в генерируемый XML
 * - item(123) идентификатор показываемого товара
 * - itemsProperties(TRUE|FALSE|array()) выводить значения дополнительных свойств товаров, по умолчанию FALSE. Может принимать массив с идентификаторами дополнительных свойств, значения которых необходимо вывести.
 * - itemsPropertiesList(TRUE|FALSE) выводить список дополнительных свойств товаров, по умолчанию TRUE
 * - itemsForbiddenTags(array('description')) массив тегов товаров, запрещенных к передаче в генерируемый XML
 * - parentItem(123) идентификатор родительского товара для отображаемой модификации
 * - modifications(TRUE|FALSE) показывать модификации для выбранных товаров, по умолчанию FALSE
 * - specialprices(TRUE|FALSE) показывать специальные цены для выбранных товаров, по умолчанию FALSE
 * - associatedItems(TRUE|FALSE) показывать сопутствующие товары для выбранных товаров, по умолчанию FALSE
 * - comments(TRUE|FALSE) показывать комментарии для выбранных товаров, по умолчанию FALSE
 * - tags(TRUE|FALSE) выводить метки
 * - siteuser(TRUE|FALSE) показывать данные о пользователе сайта, связанного с выбранным товаром, по умолчанию TRUE
 * - siteuserProperties(TRUE|FALSE) выводить значения дополнительных свойств пользователей сайта, по умолчанию FALSE
 * - comparing(TRUE|FALSE) выводить сравниваемые товары, по умолчанию TRUE
 * - favorite(TRUE|FALSE) выводить избранные товары, по умолчанию TRUE
 * - favoriteOrder('ASC'|'DESC'|'RAND') направление сортировки избранных товаров, по умолчанию RAND
 * - viewed(TRUE|FALSE) выводить просмотренные товары, по умолчанию TRUE
 * - cart(TRUE|FALSE) выводить товары в корзине, по умолчанию FALSE
 * - viewedOrder('ASC'|'DESC'|'RAND') направление сортировки просмотренных товаров, по умолчанию DESC
 * - warehousesItems(TRUE|FALSE) выводить остаток на каждом складе для товара, по умолчанию FALSE
 * - offset($offset) смещение, с которого выводить товары. По умолчанию 0
 * - limit($limit) количество выводимых товаров
 * - page(2) текущая страница, по умолчанию 0, счет ведется с 0
 * - pattern($pattern) шаблон разбора данных в URI, см. __construct()
 * - tag($path) путь тега, с использованием которого ведется отбор товаров
 * - producer($producer_id) идентификатор производителя, с использованием которого ведется отбор товаров
 * - cache(TRUE|FALSE) использовать кэширование, по умолчанию TRUE
 * - itemsActivity('active'|'inactive'|'all') отображать элементы: active - только активные, inactive - только неактивные, all - все, по умолчанию - active
 * - groupsActivity('active'|'inactive'|'all') отображать группы: active - только активные, inactive - только неактивные, all - все, по умолчанию - active
 * - commentsActivity('active'|'inactive'|'all') отображать комментарии: active - только активные, inactive - только неактивные, all - все, по умолчанию - active
 * - showPanel(TRUE|FALSE) показывать панель быстрого редактирования, по умолчанию TRUE
 *
 * Доступные свойства:
 *
 * - total общее количество доступных для отображения записей
 * - patternParams массив данных, извелеченных из URI при применении pattern
 *
 * <code>
 * $Shop_Controller_Show = new Shop_Controller_Show(
 * 	Core_Entity::factory('Shop', 1)
 * );
 *
 * $Shop_Controller_Show
 * 	->xsl(
 * 		Core_Entity::factory('Xsl')->getByName('МагазинКаталогТоваров')
 * 	)
 * 	->limit(5)
 * 	->show();
 * </code>
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Controller_Show extends Core_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'group',
		'groupsProperties',
		'groupsPropertiesList',
		'propertiesForGroups',
		'groupsMode',
		'groupsForbiddenTags',
		'item',
		'itemsProperties',
		'itemsPropertiesList',
		'itemsForbiddenTags',
		'parentItem',
		'modifications',
		'specialprices',
		'associatedItems',
		'comments',
		'tags',
		'siteuser',
		'siteuserProperties',
		'comparing',
		'favorite',
		'favoriteOrder',
		'viewed',
		'viewedOrder',
		'cart',
		'warehousesItems',
		'offset',
		'limit',
		'page',
		'total',
		'pattern',
		'patternExpressions',
		'patternParams',
		'tag',
		'producer',
		'cache',
		'itemsActivity',
		'groupsActivity',
		'commentsActivity',
		'showPanel',
	);

	/**
	 * List of groups of shop
	 * @var array
	 */
	protected $_aShop_Groups = array();

	/**
	 * List of properties for item
	 * @var array
	 */
	protected $_aItem_Properties = array();

	/**
	 * List of property directories for item
	 * @var array
	 */
	protected $_aItem_Property_Dirs = array();

	/**
	 * List of properties for group
	 * @var array
	 */
	protected $_aGroup_Properties = array();

	/**
	 * List of property directories for group
	 * @var array
	 */
	protected $_aGroup_Property_Dirs = array();

	/**
	 * Shop's items object
	 * @var Shop_Item_Model
	 */
	protected $_Shop_Items = NULL;

	/**
	 * Shop's groups object
	 * @var Shop_Group_Model
	 */
	protected $_Shop_Groups = NULL;

	/**
	 * Array of siteuser's groups allowed for current siteuser
	 * @var array
	 */
	protected $_aSiteuserGroups = array();

	/**
	 * Cache name
	 * @var string
	 */
	protected $_cacheName = 'shop_show';

	/**
	 * Constructor.
	 * @param Shop_Model $oShop shop
	 */
	public function __construct(Shop_Model $oShop)
	{
		parent::__construct($oShop->clearEntities());

		$siteuser_id = 0;

		$this->_aSiteuserGroups = array(0, -1);
		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$siteuser_id = $oSiteuser->id;

				$this->addCacheSignature('siteuser_id=' . $siteuser_id);

				$aSiteuser_Groups = $oSiteuser->Siteuser_Groups->findAll();
				foreach ($aSiteuser_Groups as $oSiteuser_Group)
				{
					$this->_aSiteuserGroups[] = $oSiteuser_Group->id;
				}
			}
		}

		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('siteuser_id')
				->value($siteuser_id)
		);

		$this->_setShopItems()->_setShopGroups();

		$this->group = 0;
		$this->item = $this->producer = NULL;
		$this->groupsProperties = $this->itemsProperties = $this->propertiesForGroups
			= $this->comments = $this->tags = $this->siteuserProperties = $this->warehousesItems = $this->cart = FALSE;
		$this->siteuser = $this->cache = $this->itemsPropertiesList = $this->groupsPropertiesList = $this->comparing = $this->favorite = $this->viewed = TRUE;

		$this->favoriteOrder = 'RAND';
		$this->viewedOrder = 'DESC';

		$this->groupsMode = 'tree';
		$this->offset = 0;
		$this->page = 0;
		$this->showPanel = TRUE;

		$this->itemsActivity = $this->groupsActivity = $this->commentsActivity = 'active'; // inactive, all

		$this->pattern = rawurldecode(trim($this->getEntity()->Structure->getPath(), '/')) . '({path})(/user-{user}/)(page-{page}/)(tag/{tag}/)(producer-{producer}/)';
		$this->patternExpressions = array(
			'page' => '\d+',
			'producer' => '\d+',
		);

		if ($this->favorite && isset($_SESSION))
		{
			$hostcmsFavorite = Core_Array::get(Core_Array::get($_SESSION, 'hostcmsFavorite', array()), $oShop->id, array());
			count($hostcmsFavorite) && $this->addCacheSignature('hostcmsFavorite=' . implode(',', $hostcmsFavorite));
		}
	}

	/**
	 * Set item's conditions
	 * @return self
	 */
	protected function _setShopItems()
	{
		$oShop = $this->getEntity();

		$this->_Shop_Items = $oShop->Shop_Items;

		switch ($oShop->items_sorting_direction)
		{
			case 1:
				$items_sorting_direction = 'DESC';
			break;
			case 0:
			default:
				$items_sorting_direction = 'ASC';
		}

		$this->_Shop_Items
			->queryBuilder()
			->clearOrderBy();

		// Определяем поле сортировки товаров
		switch ($oShop->items_sorting_field)
		{
			case 1:
				$this->_Shop_Items
					->queryBuilder()
					->orderBy('shop_items.name', $items_sorting_direction)
					->orderBy('shop_items.sorting', $items_sorting_direction);
				break;
			case 2:
				$this->_Shop_Items
					->queryBuilder()
					->orderBy('shop_items.sorting', $items_sorting_direction)
					->orderBy('shop_items.name', $items_sorting_direction);
				break;
			case 0:
			default:
				$this->_Shop_Items
					->queryBuilder()
					->orderBy('shop_items.datetime', $items_sorting_direction)
					->orderBy('shop_items.sorting', $items_sorting_direction);
		}



		$this->_Shop_Items
			->queryBuilder()
			->select('shop_items.*')
			//->where('shop_items.active', '=', 1)
			//->where('shop_items.modification_id', '=', 0)
			;

		$this->_applyItemConditions($this->_Shop_Items);

		return $this;
	}

	/**
	 * Apply item's conditions
	 *
	 * @param Shop_Item_Model $oShop_Items
	 * @return self
	 */
	protected function _applyItemConditions(Shop_Item_Model $oShop_Items)
	{
		$dateTime = Core_Date::timestamp2sql(time());
		$oShop_Items
			->queryBuilder()
			->open()
			->where('shop_items.start_datetime', '<', $dateTime)
			->setOr()
			->where('shop_items.start_datetime', '=', '0000-00-00 00:00:00')
			->close()
			->setAnd()
			->open()
			->where('shop_items.end_datetime', '>', $dateTime)
			->setOr()
			->where('shop_items.end_datetime', '=', '0000-00-00 00:00:00')
			->close()
			->where('shop_items.siteuser_group_id', 'IN', $this->_aSiteuserGroups);

		return $this;
	}

	/**
	 * Set group's conditions
	 * @return self
	 */
	protected function _setShopGroups()
	{
		$oShop = $this->getEntity();

		$this->_Shop_Groups = $oShop->Shop_Groups;
		$this->_Shop_Groups
			->queryBuilder()
			->select('shop_groups.*')
			->where('shop_groups.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
			//->where('shop_groups.active', '=', 1)
			;

		switch ($oShop->groups_sorting_direction)
		{
			case 0:
				$groups_sorting_direction = 'ASC';
				break;
			case 1:
			default:
				$groups_sorting_direction = 'DESC';
		}

		// Определяем поле сортировки групп
		switch ($oShop->groups_sorting_field)
		{
			case 0:
				$this->_Shop_Groups
					->queryBuilder()
					->orderBy('shop_groups.name', $groups_sorting_direction);
				break;
			case 1:
			default:
				$this->_Shop_Groups
					->queryBuilder()
					->orderBy('shop_groups.sorting', $groups_sorting_direction);
				break;
		}

		return $this;
	}

	/**
	 * Get items set
	 * @return Shop_Item_Model
	 */
	public function shopItems()
	{
		return $this->_Shop_Items;
	}

	/**
	 * Get groups set
	 * @return Shop_Item_Model
	 */
	public function shopGroups()
	{
		return $this->_Shop_Groups;
	}

	/**
	 * Add comparing goods
	 * @return self
	 */
	protected function _addComparing()
	{
		$oShop = $this->getEntity();

		$hostcmsCompare = Core_Array::get(Core_Array::get($_SESSION, 'hostcmsCompare', array()), $oShop->id, array());

		if (count($hostcmsCompare))
		{
			$this->addEntity(
				$oCompareEntity = Core::factory('Core_Xml_Entity')
					->name('comparing')
			);

			while (list($key) = each($hostcmsCompare))
			{
				$oShop_Item = Core_Entity::factory('Shop_Item')->find($key);
				if (!is_null($oShop_Item->id))
				{
					$this->itemsProperties && $oShop_Item->showXmlProperties($this->itemsProperties);
					$oCompareEntity->addEntity($oShop_Item->clearEntities());
				}
			}
		}

		return $this;
	}

	/**
	 * Add favorite goods
	 * @return self
	 */
	protected function _addFavorite()
	{
		$oShop = $this->getEntity();

		$hostcmsFavorite = Core_Array::get(Core_Array::get($_SESSION, 'hostcmsFavorite', array()), $oShop->id, array());

		if (count($hostcmsFavorite))
		{
			$this->addEntity(
				$oFavouriteEntity = Core::factory('Core_Xml_Entity')
					->name('favorite')
			);

			switch ($this->favoriteOrder)
			{
				case 'RAND':
					shuffle($hostcmsFavorite);
				break;
				case 'ASC':
					asort($hostcmsFavorite);
				break;
				case 'DESC':
					arsort($hostcmsFavorite);
				break;
				default:
					throw new Core_Exception("The favoriteOrder direction '%direction' doesn't allow",
						array('%direction' => $this->favoriteOrder)
					);
			}

			foreach ($hostcmsFavorite as $shop_item_id)
			{
				$oShop_Item = Core_Entity::factory('Shop_Item')->find($shop_item_id);
				if (!is_null($oShop_Item->id))
				{
					$this->itemsProperties && $oShop_Item->showXmlProperties($this->itemsProperties);
					$oFavouriteEntity->addEntity($oShop_Item->clearEntities());
				}
			}
		}

		return $this;
	}

	/**
	 * Add viewed goods
	 * @return self
	 */
	protected function _addViewed()
	{
		$oShop = $this->getEntity();

		$hostcmsViewed = Core_Array::get(Core_Array::get($_SESSION, 'hostcmsViewed', array()), $oShop->id, array());

		if (count($hostcmsViewed))
		{
			$this->addEntity(
				$oViewedEntity = Core::factory('Core_Xml_Entity')
					->name('viewed')
			);

			switch ($this->viewedOrder)
			{
				case 'RAND':
					shuffle($hostcmsViewed);
				break;
				case 'ASC':
					asort($hostcmsViewed);
				break;
				case 'DESC':
					arsort($hostcmsViewed);
				break;
				default:
					throw new Core_Exception("The viewedOrder direction '%direction' doesn't allow",
						array('%direction' => $this->viewedOrder)
					);
			}

			foreach ($hostcmsViewed as $view_item_id)
			{
				$oShop_Item = Core_Entity::factory('Shop_Item')->find($view_item_id);

				if (!is_null($oShop_Item->id) && $oShop_Item->id != $this->item)
				{
					$this->itemsProperties && $oShop_Item->showXmlProperties($this->itemsProperties);
					$oViewedEntity->addEntity($oShop_Item->clearEntities());
				}
			}
		}

		return $this;
	}

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Shop_Controller_Show.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);

		$this->showPanel && Core::checkPanel() && $this->_showPanel();

		if ($this->cache && Core::moduleIsActive('cache'))
		{
			$oCore_Cache = Core_Cache::instance(Core::$mainConfig['defaultCache']);
			$inCache = $oCore_Cache->get($cacheKey = strval($this), $this->_cacheName);

			if (!is_null($inCache))
			{
				echo $inCache;
				return $this;
			}
		}

		$oShop = $this->getEntity();

		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('group')
				->value(intval($this->group)) // FALSE => 0
		)->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('page')
				->value(intval($this->page))
		)->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('limit')
				->value(intval($this->limit))
		);

		// Comparing, favorite and viewed goods
		if (isset($_SESSION))
		{
			// Comparing goods
			$this->comparing && $this->_addComparing();

			// Favorite goods
			$this->favorite && $this->_addFavorite();

			// Viewed goods
			$this->viewed && $this->_addViewed();

			// Товары в корзине
			if ($this->cart)
			{
				// Проверяем наличие товара в корзины
				$Shop_Cart_Controller = Shop_Cart_Controller::instance();
				$aShop_Cart = $Shop_Cart_Controller->getAll($oShop);

				if (count($aShop_Cart))
				{
					$this->addEntity(
						$oCartEntity = Core::factory('Core_Xml_Entity')
							->name('items_in_cart')
					);

					foreach ($aShop_Cart as $oShop_Cart)
					{
						$oShop_Item = Core_Entity::factory('Shop_Item')->find($oShop_Cart->shop_item_id);
						if (!is_null($oShop_Item->id))
						{
							$this->itemsProperties && $oShop_Item->showXmlProperties($this->itemsProperties);
							$oCartEntity->addEntity($oShop_Item->clearEntities());
						}
					}
				}
			}
		}

		// До вывода свойств групп
		if ($this->limit > 0 || $this->item)
		{
			$this->_itemCondition();

			// Group condition for shop item
			$this->group !== FALSE && $this->_groupCondition();

			if (!$this->item)
			{
				// Load model columns BEFORE FOUND_ROWS()
				Core_Entity::factory('Shop_Item')->getTableColums();

				// Load user BEFORE FOUND_ROWS()
				$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();

				$this->_Shop_Items
					->queryBuilder()
					->sqlCalcFoundRows()
					->offset(intval($this->offset))
					->limit(intval($this->limit));
			}

			$aShop_Items = $this->_Shop_Items->findAll();

			if (!$this->item)
			{
				if ($this->page && !count($aShop_Items))
				{
					return $this->error404();
				}

				$row = Core_QueryBuilder::select(array('FOUND_ROWS()', 'count'))->execute()->asAssoc()->current();
				$this->total = $row['count'];

				$this->addEntity(
					Core::factory('Core_Xml_Entity')
						->name('total')
						->value(intval($this->total))
				);
			}
		}

		// Показывать дополнительные свойства групп
		if ($this->groupsProperties)
		{
			$oShop_Group_Property_List = Core_Entity::factory('Shop_Group_Property_List', $oShop->id);

			$aProperties = $oShop_Group_Property_List->Properties->findAll();
			foreach ($aProperties as $oProperty)
			{
				$this->_aGroup_Properties[$oProperty->property_dir_id][] = $oProperty;

				// Load all values for property
				//$oProperty->loadAllValues();
			}

			$aProperty_Dirs = $oShop_Group_Property_List->Property_Dirs->findAll();
			foreach ($aProperty_Dirs as $oProperty_Dir)
			{
				$oProperty_Dir->clearEntities();
				$this->_aGroup_Property_Dirs[$oProperty_Dir->parent_id][] = $oProperty_Dir;
			}

			// Список свойств групп товаров
			if ($this->groupsPropertiesList)
			{
				$Shop_Group_Properties = Core::factory('Core_Xml_Entity')
					->name('shop_group_properties');

				$this->addEntity($Shop_Group_Properties);

				$this->_addGroupsPropertiesList(0, $Shop_Group_Properties);
			}
		}

		is_array($this->groupsProperties) && $this->groupsProperties = array_combine($this->groupsProperties, $this->groupsProperties);

		// Устанавливаем активность групп
		$this->_setGroupsActivity();

		// Группы магазина
		switch ($this->groupsMode)
		{
			case 'none':
			break;
			// По одной группе от корня до текущего раздела, все потомки текущего раздела
			case 'tree':
				$this->addTreeGroups();
			break;
			// Все группы
			case 'all':
				$this->addAllGroups();
			break;
			default:
				throw new Core_Exception('Group mode "%groupsMode" does not allow', array('%groupsMode' => $this->groupsMode));
			break;
		}

		// Показывать дополнительные свойства товара
		if ($this->itemsProperties)
		{
			$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $oShop->id);

			$aProperties = $this->group === FALSE
				? $oShop_Item_Property_List->Properties->findAll()
				: $oShop_Item_Property_List->getPropertiesForGroup($this->group);

			$aShowPropertyIDs = array();

			foreach ($aProperties as $oProperty)
			{
				$oShop_Item_Property = $oProperty->Shop_Item_Property;

				if ($oShop_Item_Property->show_in_item && $this->item || $oShop_Item_Property->show_in_group && !$this->item)
				{
					// Используется ниже для ограничение показа значений св-в товара в модели
					$aShowPropertyIDs[] = $oProperty->id;
				}

				$this->_aItem_Properties[$oProperty->property_dir_id][] = $oProperty->clearEntities();

				$oProperty->addEntity(
					Core::factory('Core_Xml_Entity')->name('prefix')->value($oShop_Item_Property->prefix)
				)
				->addEntity(
					Core::factory('Core_Xml_Entity')->name('filter')->value($oShop_Item_Property->filter)
				)
				->addEntity(
					Core::factory('Core_Xml_Entity')->name('show_in_group')->value($oShop_Item_Property->show_in_group)
				)
				->addEntity(
					Core::factory('Core_Xml_Entity')->name('show_in_item')->value($oShop_Item_Property->show_in_item)
				);

				$oShop_Item_Property->shop_measure_id && $oProperty->addEntity(
					$oShop_Item_Property->Shop_Measure
				);

				// Load all values for property
				//$oProperty->loadAllValues();
			}

			$aProperty_Dirs = $oShop_Item_Property_List->Property_Dirs->findAll();
			foreach ($aProperty_Dirs as $oProperty_Dir)
			{
				$oProperty_Dir->clearEntities();
				$this->_aItem_Property_Dirs[$oProperty_Dir->parent_id][] = $oProperty_Dir;
			}

			// Список свойств товаров
			if ($this->itemsPropertiesList)
			{
				$Shop_Item_Properties = Core::factory('Core_Xml_Entity')
						->name('shop_item_properties');

				$this->addEntity($Shop_Item_Properties);

				$this->_addItemsPropertiesList(0, $Shop_Item_Properties);
			}
		}

		if ($this->limit > 0)
		{
			if ($this->itemsProperties)
			{
				// Показываются свойства, явно указанные пользователем в itemsProperties и разрешенные для товаров
				/*$mShowPropertyIDs = count($aShowPropertyIDs)
					? (array_merge(is_array($this->itemsProperties) ? $this->itemsProperties : array(), $aShowPropertyIDs))
					: $this->itemsProperties;*/

				$mShowPropertyIDs = is_array($this->itemsProperties)
					? $this->itemsProperties
					: $aShowPropertyIDs;

				is_array($mShowPropertyIDs) && !count($mShowPropertyIDs) && $mShowPropertyIDs = FALSE;
			}
			else
			{
				$mShowPropertyIDs = FALSE;
			}

			foreach ($aShop_Items as $oShop_Item)
			{
				// Shortcut
				$bShortcut = $oShop_Item->shortcut_id;

				$bShortcut && $oShop_Item = $oShop_Item->Shop_Item;

				// Ярлык может ссылаться на отключенный товар
				$desiredActivity = strtolower($this->itemsActivity) == 'active'
					? 1
					: (strtolower($this->itemsActivity) == 'all' ? $oShop_Item->active : 0);

				//Ярлык может ссылаться на товар с истекшим или не наступившим сроком публикации
				$iCurrentTimestamp = time();

				if ($oShop_Item->active == $desiredActivity
					&& (!$bShortcut
						|| (Core_Date::sql2timestamp($oShop_Item->end_datetime) >= $iCurrentTimestamp
							|| $oShop_Item->end_datetime == '0000-00-00 00:00:00')
						&& (Core_Date::sql2timestamp($oShop_Item->start_datetime) <= $iCurrentTimestamp
							|| $oShop_Item->start_datetime == '0000-00-00 00:00:00')
					)
				)
				{
					$oShop_Item->clearEntities();

					$this->applyItemsForbiddenTags($oShop_Item);

					// Comments
					$this->comments && $oShop_Item->showXmlComments(TRUE)->commentsActivity($this->commentsActivity);

					$this->warehousesItems && $oShop_Item->showXmlWarehousesItems(TRUE);

					$this->associatedItems && $oShop_Item->showXmlAssociatedItems(TRUE);
					$this->modifications && $oShop_Item->showXmlModifications(TRUE);
					$this->specialprices && $oShop_Item->showXmlSpecialprices(TRUE);

					// Properties for shop's item entity
					$this->itemsProperties && $oShop_Item->showXmlProperties($mShowPropertyIDs);

					// Tags
					$this->tags && $oShop_Item->showXmlTags(TRUE);

					// Siteuser
					$this->siteuser && $oShop_Item->showXmlSiteuser(TRUE)
						->showXmlSiteuserProperties($this->siteuserProperties);

					$this->addEntity($oShop_Item);

					// Parent item for modification
					$this->parentItem && $oShop_Item->addEntity(
						Core_Entity::factory('Shop_Item', $this->parentItem)
							->showXmlProperties($this->itemsProperties)
							->showXmlTags($this->tags)
					);
				}
			}
		}

		// Clear
		$this->_aShop_Groups = $this->_aItem_Property_Dirs = $this->_aItem_Properties
			= $this->_aGroup_Properties = $this->_aGroup_Property_Dirs = array();

		echo $content = parent::get();
		$this->cache && Core::moduleIsActive('cache') && $oCore_Cache->set($cacheKey, $content, $this->_cacheName);

		return $this;
	}

	/**
	 * Inc Shop_Item->showed
	 * @return self
	 */
	protected function _incShowed()
	{
		$oShop_Item = Core_Entity::factory('Shop_Item')->find($this->item);
		if (!is_null($oShop_Item->id))
		{
			$oShop_Item->showed += 1;
			$oShop_Item->save();
		}

		return $this;
	}

	/**
	 * Set item's conditions
	 * @return self
	 */
	protected function _itemCondition()
	{
		// Товары
		if ($this->item)
		{
			$this->_Shop_Items
				->queryBuilder()
				->where('shop_items.id', '=', intval($this->item));

			// Inc
			$this->_incShowed();
		}
		elseif (!is_null($this->tag))
		{
			if (Core::moduleIsActive('tag'))
			{
				$oTag = Core_Entity::factory('Tag')->getByPath($this->tag);

				if ($oTag)
				{
					$this->addEntity($oTag);

					$this->_Shop_Items
						->queryBuilder()
						->leftJoin('tag_shop_items', 'shop_items.id', '=', 'tag_shop_items.shop_item_id')
						->where('tag_shop_items.tag_id', '=', $oTag->id);

					// В корне при фильтрации по меткам вывод идет из всех групп
					$this->group == 0 && $this->group = FALSE;
				}
			}
		}
		elseif (!is_null($this->producer))
		{
			$oShop_Producer = Core_Entity::factory('Shop_Producer', $this->producer);

			$this->addEntity($oShop_Producer);

			$this->_Shop_Items
				->queryBuilder()
				->where('shop_items.shop_producer_id', '=', $this->producer);

			// В корне при фильтрации по меткам вывод идет из всех групп
			$this->group == 0 && $this->group = FALSE;
		}

		$this->_setItemsActivity();

		return $this;
	}

	/**
	 * Set item's condition by shop_group_id
	 * @return self
	 */
	protected function _groupCondition()
	{
		$this->_Shop_Items
			->queryBuilder()
			// Для модификаций ограничение по группе 0
			->where('shop_items.shop_group_id', '=', !$this->parentItem
				? intval($this->group)
				: 0
			);

		return $this;
	}

	/**
	 * Parse URL and set controller properties
	 * @return self
	 */
	public function parseUrl()
	{
		$oShop = $this->getEntity();

		$Core_Router_Route = new Core_Router_Route($this->pattern, $this->patternExpressions);
		$this->patternParams = $matches = $Core_Router_Route->applyPattern(Core::$url['path']);

		if (isset($matches['page']) && is_numeric($matches['page']))
		{
			if ($matches['page'] > 1)
			{
				$this->page($matches['page'] - 1)
					->offset($this->limit * $this->page);
			}
			else
			{
				return $this->error404();
			}
		}

		if (isset($matches['tag']) && $matches['tag'] != '' && Core::moduleIsActive('tag'))
		{
			$this->tag($matches['tag']);

			$oTag = Core_Entity::factory('Tag')->getByPath($this->tag);
			if (is_null($oTag))
			{
				return $this->error404();
			}
		}

		if (isset($matches['producer']) && $matches['producer'] != '')
		{
			$this->producer($matches['producer']);

			$oShop_Producer = Core_Entity::factory('Shop_Producer')->find($this->producer);
			if (is_null($oShop_Producer->id))
			{
				return $this->error404();
			}
		}

		// Cookie для аффилиат-программы
		if (isset($matches['user']))
		{
			setcookie('affiliate_name', $matches['user'], time() + 31536000, '/');
		}

		$path = isset($matches['path'])
			? trim($matches['path'], '/')
			: NULL;

		$this->group = 0;

		if ($path != '')
		{
			$aPath = explode('/', $path);
			foreach ($aPath as $sPath)
			{
				// Attempt to receive Shop_Group
				$oShop_Groups = $oShop->Shop_Groups;

				$this->groupsActivity = strtolower($this->groupsActivity);
				if($this->groupsActivity != 'all')
				{
					$oShop_Groups
						->queryBuilder()
						->where('active', '=', $this->groupsActivity == 'inactive' ? 0 : 1);
				}

				$oShop_Group = $oShop_Groups->getByParentIdAndPath($this->group, $sPath);

				if (!is_null($oShop_Group))
				{
					if (in_array($oShop_Group->getSiteuserGroupId(), $this->_aSiteuserGroups))
					{
						$this->group = $oShop_Group->id;
					}
					else
					{
						return $this->error403();
					}
				}
				else
				{
					// Attempt to receive Shop_Item
					$oShop_Items = $oShop->Shop_Items;

					$this->itemsActivity = strtolower($this->itemsActivity);
					if($this->itemsActivity != 'all')
					{
						$oShop_Items
							->queryBuilder()
							->where('shop_items.active', '=', $this->itemsActivity == 'inactive' ? 0 : 1);
					}

					$this->_applyItemConditions($oShop_Items);

					$oShop_Items->queryBuilder()->where('shop_items.modification_id', '=', 0);

					$oShop_Item = $oShop_Items->getByGroupIdAndPath($this->group, $sPath);

					if (!$this->item && !is_null($oShop_Item))
					{
						if (in_array($oShop_Item->getSiteuserGroupId(), $this->_aSiteuserGroups))
						{
							$this->group = $oShop_Item->shop_group_id;
							$this->item = $oShop_Item->id;
						}
						else
						{
							return $this->error403();
						}
					}
					else
					{
						// Товар был уже определен, по пути ищем модификацию
						if ($this->item)
						{
							$oShop_Modification_Items = $oShop->Shop_Items;
							$oShop_Modification_Items
								->queryBuilder()
								->where('active', '=', 1)
								->where('shop_items.modification_id', '=', $this->item);

							$oShop_Modification_Item = $oShop_Modification_Items->getByGroupIdAndPath(0, $sPath);
							if (!is_null($oShop_Modification_Item))
							{
								// Родительский товар для модификации
								$this->parentItem = $this->item;

								// Модификация в основной товар
								$this->item = $oShop_Modification_Item->id;
							}
							else
							{
								$this->item = FALSE;
								return $this->error404();
							}
						}
						else
						{
							return $this->error404();
						}
					}
				}
			}
		}

		// Ограничение на список товаров
		!$this->item && is_null($this->tag) && $this->forbidSelectModifications();

		return $this;
	}

	/**
	 * Forbids to select modifications
	 * @return self
	 */
	public function forbidSelectModifications()
	{
		$this->_Shop_Items
			->queryBuilder()
			->where('shop_items.modification_id', '=', 0);
		return $this;
	}

	/**
	 * Define handler for 404 error
	 * @return self
	 */
	public function error404()
	{
		$oCore_Response = Core_Page::instance()->deleteChild()->response->status(404);

		// Если определена константа с ID страницы для 404 ошибки и она не равна нулю
		$oSite = Core_Entity::factory('Site', CURRENT_SITE);
		if ($oSite->error404)
		{
			$oStructure = Core_Entity::factory('Structure')->find($oSite->error404);

			$oCore_Page = Core_Page::instance();

			// страница с 404 ошибкой не найдена
			if (is_null($oStructure->id))
			{
				throw new Core_Exception('Structure not found');
			}

			if ($oStructure->type == 0)
			{
				$oDocument_Versions = $oStructure->Document->Document_Versions->getCurrent();

				if (!is_null($oDocument_Versions))
				{
					$oCore_Page->template($oDocument_Versions->Template);
				}
			}
			// Если динамическая страница или типовая дин. страница
			elseif ($oStructure->type == 1 || $oStructure->type == 2)
			{
				$oCore_Page->template($oStructure->Template);
			}

			$oCore_Page->addChild($oStructure->getRelatedObjectByType());
			$oStructure->setCorePageSeo($oCore_Page);

			// Если уже идет генерация страницы, то добавленный потомок не будет вызван
			$oCore_Page->buildingPage && $oCore_Page->execute();
		}
		else
		{
			if (Core::$url['path'] != '/')
			{
				// Редирект на главную страницу
				$oCore_Response->header('Location', '/');
			}
		}
		return $this;
	}

	/**
	 * Define handler for 403 error
	 * @return self
	 */
	public function error403()
	{
		$oCore_Response = Core_Page::instance()->deleteChild()->response->status(403);

		// Если определена константа с ID страницы для 403 ошибки и она не равна нулю
		$oSite = Core_Entity::factory('Site', CURRENT_SITE);
		if ($oSite->error403)
		{
			$oStructure = Core_Entity::factory('Structure')->find($oSite->error403);

			$oCore_Page = Core_Page::instance();

			// страница с 403 ошибкой не найдена
			if (is_null($oStructure->id))
			{
				throw new Core_Exception('Group not found');
			}

			if ($oStructure->type == 0)
			{
				$oDocument_Versions = $oStructure->Document->Document_Versions->getCurrent();

				if (!is_null($oDocument_Versions))
				{
					$oCore_Page->template($oDocument_Versions->Template);
				}
			}
			// Если динамическая страница или типовая дин. страница
			elseif ($oStructure->type == 1 || $oStructure->type == 2)
			{
				$oCore_Page->template($oStructure->Template);
			}

			$oCore_Page->addChild($oStructure->getRelatedObjectByType());
			$oStructure->setCorePageSeo($oCore_Page);
		}
		else
		{
			if (Core::$url['path'] != '/')
			{
				// Редирект на главную страницу
				$oCore_Response->header('Location', '/');
			}
		}
		return $this;
	}

	/**
	 * Apply forbidden xml tags for groups
	 * @param Shop_Group_Model $oShop_Group group
	 * @return self
	 */
	public function applyGroupsForbiddenTags($oShop_Group)
	{
		if (!is_null($this->groupsForbiddenTags))
		{
			foreach ($this->groupsForbiddenTags as $forbiddenTag)
			{
				$oShop_Group->addForbiddenTag($forbiddenTag);
			}
		}

		return $this;
	}

	/**
	 * Apply forbidden xml tags for items
	 * @param Shop_Item_Model $oShop_Item item
	 * @return self
	 */
	public function applyItemsForbiddenTags($oShop_Item)
	{
		if (!is_null($this->itemsForbiddenTags))
		{
			foreach ($this->itemsForbiddenTags as $forbiddenTag)
			{
				$oShop_Item->addForbiddenTag($forbiddenTag);
			}
		}

		return $this;
	}

	/**
	 * Add all groups to XML
	 * @return self
	 */
	public function addAllGroups()
	{
		$this->_aShop_Groups = array();

		$aShop_Groups = $this->_Shop_Groups->findAll();

		foreach ($aShop_Groups as $oShop_Group)
		{
			$oShop_Group->clearEntities();
			$this->applyGroupsForbiddenTags($oShop_Group);
			$this->_aShop_Groups[$oShop_Group->parent_id][] = $oShop_Group;
		}

		$this->_addGroupsByParentId(0, $this);

		return $this;
	}

	/**
	 * Add tree groups to XML
	 * @return self
	 */
	public function addTreeGroups()
	{
		$this->_aShop_Groups = array();

		$group_id = !$this->parentItem
			? $this->group
			: Core_Entity::factory('Shop_Item', $this->parentItem)->shop_group_id;

		// Потомки текущего уровня
		$aShop_Groups = $this->_Shop_Groups->getByParentId($group_id);

		foreach ($aShop_Groups as $oShop_Group)
		{
			$oShop_Group->clearEntities();
			$this->applyGroupsForbiddenTags($oShop_Group);
			$this->_aShop_Groups[$oShop_Group->parent_id][] = $oShop_Group;
		}

		if ($group_id != 0)
		{
			$oShop_Group = Core_Entity::factory('Shop_Group', $group_id)
				->clearEntities();

			do {
				$this->applyGroupsForbiddenTags($oShop_Group);

				$this->_aShop_Groups[$oShop_Group->parent_id][] = $oShop_Group;
			} while($oShop_Group = $oShop_Group->getParent());
		}

		$this->_addGroupsByParentId(0, $this);

		return $this;
	}

	/**
	 * Add groups by parent to XML
	 * @param int $parent_id
	 * @param object $parentObject
	 * @return self
	 */
	protected function _addGroupsByParentId($parent_id, $parentObject)
	{
		if (isset($this->_aShop_Groups[$parent_id]))
		{
			$bIsArrayGroupsProperties = is_array($this->groupsProperties);
			$bIsArrayPropertiesForGroups = is_array($this->propertiesForGroups);

			foreach ($this->_aShop_Groups[$parent_id] as $oShop_Group)
			{
				// Properties for shop's group entity
				if ($this->groupsProperties
					&& (!$bIsArrayPropertiesForGroups || in_array($oShop_Group->id, $this->propertiesForGroups)))
				{
					$aProperty_Values = $oShop_Group->getPropertyValues();

					if ($bIsArrayGroupsProperties)
					{
						foreach ($aProperty_Values as $oProperty_Value)
						{
							isset($this->groupsProperties[$oProperty_Value->property_id]) && $oShop_Group->addEntity($oProperty_Value);
						}
					}
					else
					{
						$oShop_Group->addEntities($aProperty_Values);
					}
				}

				$parentObject->addEntity($oShop_Group);

				$this->_addGroupsByParentId($oShop_Group->id, $oShop_Group);
			}
		}
		return $this;
	}

	/**
	 * Add items properties to XML
	 * @param int $parent_id
	 * @param object $parentObject
	 * @return self
	 */
	protected function _addItemsPropertiesList($parent_id, $parentObject)
	{
		if (isset($this->_aItem_Property_Dirs[$parent_id]))
		{
			foreach ($this->_aItem_Property_Dirs[$parent_id] as $oProperty_Dir)
			{
				$parentObject->addEntity($oProperty_Dir);
				$this->_addItemsPropertiesList($oProperty_Dir->id, $oProperty_Dir);
			}
		}

		if (isset($this->_aItem_Properties[$parent_id]))
		{
			$parentObject->addEntities($this->_aItem_Properties[$parent_id]);
		}

		return $this;
	}

	/**
	 * Add groups properties to XML
	 * @param int $parent_id
	 * @param object $parentObject
	 * @return self
	 */
	protected function _addGroupsPropertiesList($parent_id, $parentObject)
	{
		if (isset($this->_aGroup_Property_Dirs[$parent_id]))
		{
			foreach ($this->_aGroup_Property_Dirs[$parent_id] as $oProperty_Dir)
			{
				$parentObject->addEntity($oProperty_Dir);
				$this->_addGroupsPropertiesList($oProperty_Dir->id, $oProperty_Dir);
			}
		}

		if (isset($this->_aGroup_Properties[$parent_id]))
		{
			$parentObject->addEntities($this->_aGroup_Properties[$parent_id]);
		}

		return $this;
	}

	/**
	 * Show frontend panel
	 * @return $this
	 */
	protected function _showPanel()
	{
		$oShop = $this->getEntity();

		// Panel
		$oXslPanel = Core::factory('Core_Html_Entity_Div')
			->class('hostcmsPanel');

		$oXslSubPanel = Core::factory('Core_Html_Entity_Div')
			->class('hostcmsSubPanel hostcmsWindow hostcmsXsl')
			->add(
				Core::factory('Core_Html_Entity_Img')
					->width(3)->height(16)
					->src('/hostcmsfiles/images/drag_bg.gif')
			);

		if ($this->item == 0)
		{
			$sPath = '/admin/shop/item/index.php';
			$sAdditional = "hostcms[action]=edit&shop_id={$oShop->id}&shop_group_id={$this->group}&hostcms[checked][1][0]=1";
			$sTitle = Core::_('Shop_Item.items_catalog_add_form_title');

			$oXslSubPanel->add(
				Core::factory('Core_Html_Entity_A')
					->href("{$sPath}?{$sAdditional}")
					->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
					->add(
						Core::factory('Core_Html_Entity_Img')
							->width(16)->height(16)
							->src('/admin/images/page_add.gif')
							->alt($sTitle)
							->title($sTitle)
					)
			);

			$sPath = '/admin/shop/item/index.php';
			$sAdditional = "hostcms[action]=edit&shop_id={$oShop->id}&shop_group_id={$this->group}&hostcms[checked][0][0]=1";
			$sTitle = Core::_('Shop_Group.groups_add_form_title');

			$oXslSubPanel->add(
				Core::factory('Core_Html_Entity_A')
					->href("{$sPath}?{$sAdditional}")
					->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
					->add(
						Core::factory('Core_Html_Entity_Img')
							->width(16)->height(16)
							->src('/admin/images/folder_add.gif')
							->alt($sTitle)
							->title($sTitle)
					)
			);

			if ($this->group)
			{
				$oShop_Group = Core_Entity::factory('Shop_Group', $this->group);

				$sPath = '/admin/shop/item/index.php';
				$sAdditional = "hostcms[action]=edit&shop_id={$oShop->id}&shop_group_id={$this->group}&hostcms[checked][0][{$this->group}]=1";
				$sTitle = Core::_('Shop_Group.groups_edit_form_title');

				$oXslSubPanel->add(
					Core::factory('Core_Html_Entity_A')
						->href("{$sPath}?{$sAdditional}")
						->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
						->add(
							Core::factory('Core_Html_Entity_Img')
								->width(16)->height(16)
								->src('/admin/images/folder_edit.gif')
								->alt($sTitle)
								->title($sTitle)
						)
				);
			}

			$sPath = '/admin/shop/index.php';
			$sAdditional = "hostcms[action]=edit&shop_dir_id={$oShop->shop_dir_id}&hostcms[checked][1][{$oShop->id}]=1";
			$sTitle = Core::_('Shop.edit_title');

			$oXslSubPanel->add(
				Core::factory('Core_Html_Entity_A')
					->href("{$sPath}?{$sAdditional}")
					->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
					->add(
						Core::factory('Core_Html_Entity_Img')
							->width(16)->height(16)
							->src('/admin/images/folder_page_edit.gif')
							->alt($sTitle)
							->title($sTitle)
					)
			);
		}
		else
		{
			$sPath = '/admin/shop/item/index.php';
			$sAdditional = "hostcms[action]=edit&shop_id={$oShop->id}&shop_group_id={$this->group}&hostcms[checked][1][{$this->item}]=1";
			$sTitle = Core::_('Shop_Item.items_catalog_edit_form_title');

			$oXslSubPanel->add(
				Core::factory('Core_Html_Entity_A')
					->href("{$sPath}?{$sAdditional}")
					->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
					->add(
						Core::factory('Core_Html_Entity_Img')
							->width(16)->height(16)
							->src('/admin/images/edit.gif')
							->alt($sTitle)
							->title($sTitle)
					)
			);

			$sPath = '/admin/shop/item/index.php';
			$sAdditional = "hostcms[action]=markDeleted&shop_id={$oShop->id}&shop_group_id={$this->group}&hostcms[checked][1][{$this->item}]=1";
			$sTitle = Core::_('Shop_Item.markDeleted');

			$oXslSubPanel->add(
				Core::factory('Core_Html_Entity_A')
					->href("{$sPath}?{$sAdditional}")
					->onclick("res = confirm('" . Core::_('Admin_Form.msg_information_delete') . "'); if (res) { $.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'});} return false")
					->add(
						Core::factory('Core_Html_Entity_Img')
							->width(16)->height(16)
							->src('/admin/images/delete.gif')
							->alt($sTitle)
							->title($sTitle)
					)
			);
		}

		$oXslPanel
			->add($oXslSubPanel)
			->execute();

		return $this;
	}

	/**
	 * Set items activity
	 * @return self
	 */
	protected function _setItemsActivity()
	{
		$this->itemsActivity = strtolower($this->itemsActivity);
		if($this->itemsActivity != 'all')
		{
			$this->_Shop_Items
				->queryBuilder()
				->where('shop_items.active', '=', $this->itemsActivity == 'inactive' ? 0 : 1);
		}

		return $this;
	}

	/**
	 * Set groups activity
	 * @return self
	 */
	protected function _setGroupsActivity()
	{
		$this->groupsActivity = strtolower($this->groupsActivity);
		if($this->groupsActivity != 'all')
		{
			$this->_Shop_Groups
				->queryBuilder()
				->where('shop_groups.active', '=', $this->groupsActivity == 'inactive' ? 0 : 1);
		}

		return $this;
	}

	/**
	 * Add minimum and maximum price
	 * @return self
	 */
	public function addMinMaxPrice()
	{
		$oShop = $this->getEntity();

		$iCurrentShopGroup = intval($this->group);

		$aShop_Currencies = Core_Entity::factory('Shop_Currency')->findAll();

		$query_currency_switch = 'price';
		foreach ($aShop_Currencies as $oShop_Currency)
		{
			// Получаем коэффициент пересчета для каждой валюты
			$currency_coefficient = Shop_Controller::instance()->getCurrencyCoefficientInShopCurrency(
				$oShop_Currency, $oShop->Shop_Currency
			);

			$query_currency_switch = "IF (`shop_items`.`shop_currency_id` = '{$oShop_Currency->id}', IF (shop_discounts.percent, price * (100 - shop_discounts.percent) * {$currency_coefficient} / 100, shop_items.price * {$currency_coefficient}), {$query_currency_switch})";
		}

		$current_date = date('Y-m-d H:i:s');

		$oSubMinMaxQueryBuilder = Core_QueryBuilder::select(array(Core_QueryBuilder::expression($query_currency_switch), 'absolute_price'))
			->from('shop_items')
			->where('shop_items.shop_id', '=', $oShop->id)
			->where('shop_items.shop_group_id', '=', $iCurrentShopGroup)
			->leftJoin('shop_item_discounts', 'shop_items.id', '=', 'shop_item_discounts.shop_item_id')
			->leftJoin('shop_discounts', 'shop_item_discounts.shop_discount_id', '=', 'shop_discounts.id', array(
				array('AND (' => array('shop_discounts.end_datetime', '>=', $current_date)),
				array('OR' => array('shop_discounts.end_datetime', '=', '0000-00-00 00:00:00')),
				array('AND' => array('shop_discounts.start_datetime', '<=', $current_date)),
				array(')' => NULL)
			))
			->groupBy('shop_items.id');

		$oMinMaxQueryBuilder = Core_QueryBuilder::select(
			array(Core_QueryBuilder::expression('MIN(t.absolute_price)'), 'min'),
			array(Core_QueryBuilder::expression('MAX(t.absolute_price)'), 'max')
		)
		->from(array($oSubMinMaxQueryBuilder, 't'));

		$rows = $oMinMaxQueryBuilder->asAssoc()->execute()->current();

		$oShop_Controller = Shop_Controller::instance();

		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('min_price')
				->value(
					round($rows['min'])
				)
		)->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('max_price')
				->value(
					round($rows['max'])
				)
		);

		return $this;
	}
}