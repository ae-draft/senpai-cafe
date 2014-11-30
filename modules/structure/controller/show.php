<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Показ структуры сайта.
 *
 * Доступные методы:
 *
 * - menu($menuId) вывод узлов структуры меню $menu
 * - parentId($parentId) идентификатор родительского узла, по умолчанию 0
 * - level($level) выводить узлы структуры только до уровня вложенности $level
 * - showProperties(TRUE|FALSE) выводить значения дополнительных свойств усзлов структуры, по умолчанию FALSE
 * - showInformationsystemGroups(TRUE|FALSE) выводить связанные с узлом структуры группы информационной системы, по умолчанию FALSE
 * - showInformationsystemItems(TRUE|FALSE) выводить связанные с узлом структуры информационные элементы, по умолчанию FALSE
 * - showShopGroups(TRUE|FALSE) выводить связанные с узлом структуры группы магазина, по умолчанию FALSE
 * - showShopItems(TRUE|FALSE) выводить связанные с узлом структуры товары, по умолчанию FALSE
 * - showInformationsystemGroupProperties(TRUE|FALSE) выводить значения дополнительных свойств групп информационной системы, по умолчанию FALSE
 * - showInformationsystemItemProperties(TRUE|FALSE) выводить значения дополнительных свойств информационных элементов, по умолчанию FALSE
 * - showShopGroupProperties(TRUE|FALSE) выводить значения дополнительных свойств групп магазина, по умолчанию FALSE
 * - showShopItemProperties(TRUE|FALSE) выводить значения дополнительных свойств товаров, по умолчанию FALSE
 * - forbiddenTags(array('name')) массив тегов узла структуры, запрещенных к передаче в генерируемый XML
 * - cache(TRUE|FALSE) использовать кэширование, по умолчанию TRUE
 * - showPanel(TRUE|FALSE) показывать панель быстрого редактирования, по умолчанию TRUE
 *
 * Доступные свойства:
 *
 * - currentStructureId идентификатор узла структуры
 *
 * <code>
 * $Structure_Controller_Show = new Structure_Controller_Show(
 * 		Core_Entity::factory('Site', 1)
 * 	);
 *
 * 	$Structure_Controller_Show
 * 		->xsl(
 * 			Core_Entity::factory('Xsl')->getByName('Меню')
 * 		)
 * 		->show();
 * </code>
 *
 * @package HostCMS 6\Structure
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Structure_Controller_Show extends Core_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'menu',
		'parentId',
		'level',
		'showProperties',
		'showInformationsystemGroups',
		'showInformationsystemItems',
		'showShopGroups',
		'showShopItems',
		'showInformationsystemGroupProperties',
		'showInformationsystemItemProperties',
		'showShopGroupProperties',
		'showShopItemProperties',
		'forbiddenTags',
		'cache',
		'currentStructureId',
		'showPanel',
	);

	/**
	 * List of structuries
	 * @var array
	 */
	protected $_aStructures = array();

	/**
	 * List of properties
	 * @var array
	 */
	protected $_aProperties = array();

	/**
	 * List of property directories
	 * @var array
	 */
	protected $_aProperty_Dirs = array();

	/**
	 * Array of siteuser's groups allowed for current siteuser
	 * @var array
	 */
	protected $_aSiteuserGroups = array();

	/**
	 * Constructor.
	 * @param Site_Model $oSite site
	 */
	public function __construct(Site_Model $oSite)
	{
		parent::__construct($oSite->clearEntities());

		$this->_Structure = $oSite->Structures;

		$this->_aSiteuserGroups = array(0, -1);
		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$this->addCacheSignature('siteuser_id=' . $oSiteuser->id);

				$aSiteuser_Groups = $oSiteuser->Siteuser_Groups->findAll();
				foreach ($aSiteuser_Groups as $oSiteuser_Group)
				{
					$this->_aSiteuserGroups[] = $oSiteuser_Group->id;
				}
			}
		}

		$this->_Structure
			->queryBuilder()
			->select('structures.*')
			->where('structures.active', '=', 1)
			->where('structures.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
			->clearOrderBy()
			->orderBy('structures.sorting')
			->orderBy('structures.name');

		$this->showProperties = $this->showInformationsystemGroups = $this->showInformationsystemItems = $this->showShopGroups = $this->showShopItems = $this->showInformationsystemGroupProperties = $this->showInformationsystemItemProperties = $this->showShopGroupProperties = $this->showShopItemProperties = FALSE;

		$this->showPanel = $this->cache = TRUE;

		$this->currentStructureId = Core_Page::instance()->structure->id;
	}

	/**
	 * Structure object
	 * @var array
	 */
	protected $_Structure = NULL;

	/**
	 * Set Structure
	 * @return Structure_Model
	 */
	public function structure()
	{
		return $this->_Structure;
	}

	/**
	 * List of information systems
	 * @var array
	 */
	protected $_Informationsystems = array();

	/**
	 * List of shops
	 * @var array
	 */
	protected $_Shops = array();

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Structure_Controller_Show.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);

		$this->showPanel && Core::checkPanel() && $this->_showPanel();

		if ($this->cache && Core::moduleIsActive('cache'))
		{
			$oCore_Cache = Core_Cache::instance(Core::$mainConfig['defaultCache']);
			$inCache = $oCore_Cache->get($cacheKey = strval($this), $cacheName = 'structure_show');

			if (!is_null($inCache))
			{
				echo $inCache;
				return $this;
			}
		}

		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('parent_id')
				->value(intval($this->parentId))
		)->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('current_structure_id')
				->value($this->currentStructureId)
		);

		$aStructures = is_null($this->menu)
			? $this->_Structure->findAll()
			: $this->_Structure->getByStructureMenuId($this->menu);

		foreach ($aStructures as $oStructure)
		{
			$this->_aStructures[$oStructure->parent_id][] = $oStructure->clearEntities();
		}

		$oSite = $this->getEntity();

		// Показывать дополнительные свойства
		if ($this->showProperties)
		{
			$oStructure_Property_List = Core_Entity::factory('Structure_Property_List', $oSite->id);

			$aProperties = $oStructure_Property_List->Properties->findAll();
			foreach ($aProperties as $oProperty)
			{
				$this->_aProperties[$oProperty->property_dir_id][] = $oProperty;

				// Load all values for property
				$oProperty->loadAllValues();
			}

			$aProperty_Dirs = $oStructure_Property_List->Property_Dirs->findAll();
			foreach ($aProperty_Dirs as $oProperty_Dir)
			{
				$oProperty_Dir->clearEntities();
				$this->_aProperty_Dirs[$oProperty_Dir->parent_id][] = $oProperty_Dir;
			}

			$this->_addPropertyList(0, $this);
		}

		is_null($this->parentId) && $this->parentId = 0;

		if ($this->showInformationsystemGroups || $this->showInformationsystemItems)
		{
			$aInformationsystems = $oSite->Informationsystems->findAll();
			foreach ($aInformationsystems as $oInformationsystem)
			{
				$oInformationsystem->structure_id && $this->_Informationsystems[$oInformationsystem->structure_id] = $oInformationsystem;
			}
		}

		if ($this->showShopGroups || $this->showShopItems)
		{
			$aShops = $oSite->Shops->findAll();
			foreach ($aShops as $oShop)
			{
				$oShop->structure_id && $this->_Shops[$oShop->structure_id] = $oShop;
			}
		}

		$this->_addStructuresByParentId($this->parentId, $this);

		// Clear
		$this->_aStructures = $this->_aProperty_Dirs = $this->_aProperties
			= $this->_Informationsystems = $this->_Shops = array();

		echo $content = parent::get();
		$this->cache && Core::moduleIsActive('cache') && $oCore_Cache->set($cacheKey, $content, $cacheName);

		return $this;
	}

	/**
	 * Create the tree of structures
	 * @param int $parent_id
	 * @param object $parentObject
	 * @param int $level
	 * @return self
	 */
	protected function _addStructuresByParentId($parent_id, $parentObject, $level = 0)
	{
		if (isset($this->_aStructures[$parent_id]))
		{
			foreach ($this->_aStructures[$parent_id] as $oStructure)
			{
				$this->applyForbiddenTags($oStructure);

				$parentObject->addEntity($oStructure);

				// Properties for structure entity
				$oStructure->showXmlProperties($this->showProperties);

				if (is_null($this->level) || $level < $this->level)
				{
					$this->_addStructuresByParentId($oStructure->id, $oStructure, $level + 1);
				}
			}
		}

		// Informationsystem
		if (($this->showInformationsystemGroups || $this->showInformationsystemItems) && isset($this->_Informationsystems[$parent_id]))
		{
			$this->_addInformationsystemGroups($parentObject, $this->_Informationsystems[$parent_id]);
		}

		// Shop
		if (($this->showShopGroups || $this->showShopItems) && isset($this->_Shops[$parent_id]))
		{
			$this->_addShopGroups($parentObject, $this->_Shops[$parent_id]);
		}

		return $this;
	}

	/**
	 * Create the tree of property dirs and properties
	 * @param int $parent_id property group ID
	 * @param object $parentObject
	 * @return self
	 */
	protected function _addPropertyList($parent_id, $parentObject)
	{
		if (isset($this->_aProperty_Dirs[$parent_id]))
		{
			foreach ($this->_aProperty_Dirs[$parent_id] as $oProperty_Dir)
			{
				$parentObject->addEntity($oProperty_Dir);
				$this->_addPropertyList($oProperty_Dir->id, $oProperty_Dir);
			}
		}

		if (isset($this->_aProperties[$parent_id]))
		{
			$parentObject->addEntities($this->_aProperties[$parent_id]);
		}

		return $this;
	}

	/**
	 * List of groups
	 * @var array
	 */
	protected $_aInformationsystem_Groups = array();

	/**
	 * List of items
	 * @var array
	 */
	protected $_aInformationsystem_Items = array();

	/**
	 * Add all groups of information system to XML
	 * @param object $parentObject
	 * @param Informationsystem_Model $oInformationsystem
	 */
	protected function _addInformationsystemGroups($parentObject, $oInformationsystem)
	{
		$this->_aInformationsystem_Groups = array();

		$dateTime = Core_Date::timestamp2sql(time());

		$oInformationsystem_Groups = $oInformationsystem->Informationsystem_Groups;
		$oInformationsystem_Groups->queryBuilder()
			->where('informationsystem_groups.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
			->where('informationsystem_groups.active', '=', 1)
			->clearOrderBy();

		switch ($oInformationsystem->groups_sorting_direction)
		{
			case 0:
				$groups_sorting_direction = 'ASC';
				break;
			case 1:
			default:
				$groups_sorting_direction = 'DESC';
		}

		// Определяем поле сортировки информационных групп
		switch ($oInformationsystem->groups_sorting_field)
		{
			case 0:
				$oInformationsystem_Groups
					->queryBuilder()
					->orderBy('informationsystem_groups.name', $groups_sorting_direction);
				break;
			case 1:
			default:
				$oInformationsystem_Groups
					->queryBuilder()
					->orderBy('informationsystem_groups.sorting', $groups_sorting_direction);
				break;
		}

		$this->_aInformationsystem_Groups = array();

		$aInformationsystem_Groups = $oInformationsystem_Groups->findAll();
		foreach ($aInformationsystem_Groups as $oInformationsystem_Group)
		{
			$this->_aInformationsystem_Groups[$oInformationsystem_Group->parent_id][] = $oInformationsystem_Group;
		}

		// Informationsystem's items
		if ($this->showInformationsystemItems)
		{
			$oInformationsystem_Items = $oInformationsystem->Informationsystem_Items;
			$oInformationsystem_Items->queryBuilder()
				->select('informationsystem_items.*')
				->open()
				->where('informationsystem_items.start_datetime', '<', $dateTime)
				->setOr()
				->where('informationsystem_items.start_datetime', '=', '0000-00-00 00:00:00')
				->close()
				->setAnd()
				->open()
				->where('informationsystem_items.end_datetime', '>', $dateTime)
				->setOr()
				->where('informationsystem_items.end_datetime', '=', '0000-00-00 00:00:00')
				->close()
				->where('informationsystem_items.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
				->clearOrderBy();

			switch ($oInformationsystem->items_sorting_direction)
			{
				case 1:
					$items_sorting_direction = 'DESC';
				break;
				case 0:
				default:
					$items_sorting_direction = 'ASC';
			}

			// Определяем поле сортировки информационных элементов
			switch ($oInformationsystem->items_sorting_field)
			{
				case 1:
					$oInformationsystem_Items
						->queryBuilder()
						->orderBy('informationsystem_items.name', $items_sorting_direction)
						->orderBy('informationsystem_items.sorting', $items_sorting_direction);
					break;
				case 2:
					$oInformationsystem_Items
						->queryBuilder()
						->orderBy('informationsystem_items.sorting', $items_sorting_direction)
						->orderBy('informationsystem_items.name', $items_sorting_direction);
					break;
				case 0:
				default:
					$oInformationsystem_Items
						->queryBuilder()
						->orderBy('informationsystem_items.datetime', $items_sorting_direction)
						->orderBy('informationsystem_items.sorting', $items_sorting_direction);
			}

			$this->_aInformationsystem_Items = array();

			$aInformationsystem_Items = $oInformationsystem_Items->findAll();
			foreach ($aInformationsystem_Items as $oInformationsystem_Item)
			{
				$this->_aInformationsystem_Items[$oInformationsystem_Item->informationsystem_group_id][] = $oInformationsystem_Item;
			}
		}

		$this->_addInformationsystemGroupsByParentId(0, $parentObject);
	}

	/**
	 * Add groups of information system to XML
	 * @param int $parent_id ID of parent group
	 * @param object $parentObject
	 * @return self
	 */
	protected function _addInformationsystemGroupsByParentId($parent_id, $parentObject)
	{
		if (isset($this->_aInformationsystem_Groups[$parent_id]))
		{
			foreach ($this->_aInformationsystem_Groups[$parent_id] as $oInformationsystem_Group)
			{
				$this->showInformationsystemGroupProperties && $oInformationsystem_Group->showXmlProperties($this->showInformationsystemGroupProperties);

				$oInformationsystem_Group
					->clearEntities()
					->addForbiddenTag('url')
					->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('link')
							->value(
								$oInformationsystem_Group->Informationsystem->Structure->getPath() . $oInformationsystem_Group->getPath()
							)
					)->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('show')
							->value($oInformationsystem_Group->active)
					);

				$parentObject->addEntity($oInformationsystem_Group);

				$this->_addInformationsystemGroupsByParentId($oInformationsystem_Group->id, $oInformationsystem_Group);
			}
		}

		if ($this->showInformationsystemItems && isset($this->_aInformationsystem_Items[$parent_id]))
		{
			foreach ($this->_aInformationsystem_Items[$parent_id] as $oInformationsystem_Item)
			{
				// Shortcut
				$oInformationsystem_Item->shortcut_id && $oInformationsystem_Item = $oInformationsystem_Item->Informationsystem_Item;

				$oInformationsystem_Item
					->clearEntities()
					->clearEntitiesAfterGetXml(FALSE)
					->addForbiddenTag('url')
					->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('link')
							->value(
								$oInformationsystem_Item->Informationsystem->Structure->getPath() . $oInformationsystem_Item->getPath()
							)
					)->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('show')
							->value($oInformationsystem_Item->active)
					);

				$this->showInformationsystemItemProperties && $oInformationsystem_Item->showXmlProperties($this->showInformationsystemItemProperties);

				$parentObject->addEntity($oInformationsystem_Item);
			}
		}

		return $this;
	}

	/**
	 * List of shop groups
	 * @var array
	 */
	protected $_aShop_Groups = array();

	/**
	 * List of shop items
	 * @var array
	 */
	protected $_aShop_Items = array();

	/**
	 * Add all groups of shop to XML
	 * @param object $parentObject
	 * @param Shop_Model $oShop shop
	 */
	protected function _addShopGroups($parentObject, $oShop)
	{
		$this->_aShop_Groups = array();

		$dateTime = Core_Date::timestamp2sql(time());

		$oShop_Groups = $oShop->Shop_Groups;
		$oShop_Groups->queryBuilder()
			->where('shop_groups.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
			->where('shop_groups.active', '=', 1)
			->clearOrderBy();

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
				$oShop_Groups
					->queryBuilder()
					->orderBy('shop_groups.name', $groups_sorting_direction);
				break;
			case 1:
			default:
				$oShop_Groups
					->queryBuilder()
					->orderBy('shop_groups.sorting', $groups_sorting_direction);
				break;
		}

		$this->_aShop_Groups = array();

		$aShop_Groups = $oShop_Groups->findAll();
		foreach ($aShop_Groups as $oShop_Group)
		{
			$this->_aShop_Groups[$oShop_Group->parent_id][] = $oShop_Group;
		}

		// Shop's items
		if ($this->showShopItems)
		{
			$oShop_Items = $oShop->Shop_Items;
			$oShop_Items->queryBuilder()
				->select('shop_items.*')
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
				->where('shop_items.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
				->where('shop_items.modification_id', '=', 0)
				->clearOrderBy();

			switch ($oShop->items_sorting_direction)
			{
				case 1:
					$items_sorting_direction = 'DESC';
				break;
				case 0:
				default:
					$items_sorting_direction = 'ASC';
			}

			// Определяем поле сортировки информационных элементов
			switch ($oShop->items_sorting_field)
			{
				case 1:
					$oShop_Items
						->queryBuilder()
						->orderBy('shop_items.name', $items_sorting_direction)
						->orderBy('shop_items.sorting', $items_sorting_direction);
					break;
				case 2:
					$oShop_Items
						->queryBuilder()
						->orderBy('shop_items.sorting', $items_sorting_direction)
						->orderBy('shop_items.name', $items_sorting_direction);
					break;
				case 0:
				default:
					$oShop_Items
						->queryBuilder()
						->orderBy('shop_items.datetime', $items_sorting_direction)
						->orderBy('shop_items.sorting', $items_sorting_direction);
			}

			$this->_aShop_Items = array();

			$aShop_Items = $oShop_Items->findAll();
			foreach ($aShop_Items as $oShop_Item)
			{
				$this->_aShop_Items[$oShop_Item->shop_group_id][] = $oShop_Item;
			}
		}

		$this->_addShopGroupsByParentId(0, $parentObject);
	}

	/**
	 * Add shop groups to object by parent group ID
	 * @param int $parent_id parent group ID
	 * @param object $parentObject
	 * @return self
	 */
	protected function _addShopGroupsByParentId($parent_id, $parentObject)
	{
		if (isset($this->_aShop_Groups[$parent_id]))
		{
			foreach ($this->_aShop_Groups[$parent_id] as $oShop_Group)
			{
				$this->showShopGroupProperties && $oShop_Group->showXmlProperties($this->showShopGroupProperties);

				$oShop_Group
					->clearEntities()
					->addForbiddenTag('url')
					->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('link')
							->value(
								$oShop_Group->Shop->Structure->getPath() . $oShop_Group->getPath()
							)
					)->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('show')
							->value($oShop_Group->active)
					);

				$parentObject->addEntity($oShop_Group);

				$this->_addShopGroupsByParentId($oShop_Group->id, $oShop_Group);
			}
		}

		if ($this->showShopItems && isset($this->_aShop_Items[$parent_id]))
		{
			foreach ($this->_aShop_Items[$parent_id] as $oShop_Item)
			{
				// Shortcut
				$oShop_Item->shortcut_id && $oShop_Item = $oShop_Item->Shop_Item;

				$oShop_Item
					->clearEntities()
					->clearEntitiesAfterGetXml(FALSE)
					->addForbiddenTag('url')
					->showXmlModifications(TRUE)
					->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('link')
							->value(
								$oShop_Item->Shop->Structure->getPath() . $oShop_Item->getPath()
							)
					)->addEntity(
						Core::factory('Core_Xml_Entity')
							->name('show')
							->value($oShop_Item->active)
					);

				$this->showShopItemProperties && $oShop_Item->showXmlProperties($this->showShopItemProperties);

				$parentObject->addEntity($oShop_Item);
			}
		}

		return $this;
	}

	/**
	 * Apply forbidden tags
	 * @param Structure $oStructure
	 * @return self
	 */
	public function applyForbiddenTags($oStructure)
	{
		if (!is_null($this->forbiddenTags))
		{
			foreach ($this->forbiddenTags as $forbiddenTag)
			{
				$oStructure->addForbiddenTag($forbiddenTag);
			}
		}

		return $this;
	}

	/**
	 * Show frontend panel
	 * @return $this
	 */
	protected function _showPanel()
	{
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

		$sPath = '/admin/structure/index.php';
		$sAdditional = "hostcms[action]=edit&hostcms[checked][0][0]=1";
		$sTitle = Core::_('Structure.add_title');

		$oXslSubPanel->add(
			Core::factory('Core_Html_Entity_A')
				->href("{$sPath}?{$sAdditional}")
				->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
				->add(
					Core::factory('Core_Html_Entity_Img')
						->width(16)->height(16)
						->src('/admin/images/structure_add.gif')
						->alt($sTitle)
						->title($sTitle)
				)
		);

		$oXslPanel
			->add($oXslSubPanel)
			->execute();

		return $this;
	}
}