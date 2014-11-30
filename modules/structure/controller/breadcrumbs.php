<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Показ навигационной цепочки структуры сайта.
 *
 * Доступные методы:
 * current($parentId) вывод потомков узла структуры $parentId
 * showProperties(TRUE) выводить значения дополнительных свойств, по умолчанию NULL
 *
 * <code>
 * $Structure_Controller_Breadcrumbs = new Structure_Controller_Breadcrumbs(
 * 		Core_Entity::factory('Site', 1)
 * 	);
 *
 * 	$Structure_Controller_Breadcrumbs
 * 		->xsl(
 * 			Core_Entity::factory('Xsl')->getByName('ХлебныеКрошки')
 * 		)
 * 		->show();
 * </code>
 *
 * @package HostCMS 6\Structure
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Structure_Controller_Breadcrumbs extends Core_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'menu',
		'current',
		'showProperties',
		'showInformationsystem',
		'showShop',
		'cache',
		'informationsystem_item_id',
		'informationsystem_group_id',
		'shop_item_id',
		'shop_group_id',
		'forbiddenTags',
	);

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
	 * Constructor.
	 * @param Site_Model $oSite site
	 */
	public function __construct(Site_Model $oSite)
	{
		parent::__construct(
			$oSite
				->showXmlAlias(FALSE)
				->showXmlSiteuserIdentityProviders(FALSE)
				->clearEntities()
		);

		$this->forbiddenTags = array('description', 'text', 'seo_title', 'seo_description', 'seo_keywords');

		$this->current = Core_Page::instance()->structure->id;

		$this->showInformationsystem = $this->showShop = TRUE;

		$this->cache = TRUE;
	}

	/**
	 * List of information systems
	 * @var array
	 */
	protected $_Informationsystems = array();

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Structure_Controller_Breadcrumbs.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);

		if (is_object(Core_Page::instance()->object))
		{
			if ($this->showInformationsystem && Core_Page::instance()->object instanceof Informationsystem_Controller_Show)
			{
				if (Core_Page::instance()->object->item)
				{
					$this->informationsystem_item_id = Core_Page::instance()->object->item;
				}

				if (Core_Page::instance()->object->group)
				{
					$this->informationsystem_group_id = Core_Page::instance()->object->group;
				}
			}

			if ($this->showShop && Core_Page::instance()->object instanceof Shop_Controller_Show)
			{
				if (Core_Page::instance()->object->item)
				{
					$this->shop_item_id = Core_Page::instance()->object->item;
				}

				if (Core_Page::instance()->object->group)
				{
					$this->shop_group_id = Core_Page::instance()->object->group;
				}
			}
		}

		if ($this->cache && Core::moduleIsActive('cache'))
		{
			$oCore_Cache = Core_Cache::instance(Core::$mainConfig['defaultCache']);
			$inCache = $oCore_Cache->get($cacheKey = strval($this), $cacheName = 'structure_breadcrumbs');

			if (!is_null($inCache))
			{
				echo $inCache;
				return $this;
			}
		}

		$this->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('current_structure_id')
				->value($this->current)
		);

		$aStructures = array();

		if (is_object(Core_Page::instance()->object))
		{
			if ($this->showInformationsystem && Core_Page::instance()->object instanceof Informationsystem_Controller_Show)
			{
				if ($this->informationsystem_item_id)
				{
					$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item', $this->informationsystem_item_id);

					$oInformationsystem_Item
						->clearEntities()
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

					$aStructures[] = $oInformationsystem_Item;
				}

				if ($this->informationsystem_group_id)
				{
					$groupId = $this->informationsystem_group_id;

					$aGroups = array();

					while ($groupId)
					{
						$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group', $groupId);

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

						$groupId = $oInformationsystem_Group->parent_id;
						$aGroups[] = $oInformationsystem_Group;
					}

					$aStructures = array_merge($aStructures, $aGroups);
				}
			}

			if ($this->showShop && Core_Page::instance()->object instanceof Shop_Controller_Show)
			{
				if ($this->shop_item_id)
				{
					$oShop_Item = Core_Entity::factory('Shop_Item', $this->shop_item_id);

					$oShop_Item
						->clearEntities()
						->addForbiddenTag('url')
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

					// Если модификация, то сначала идет родительский товар, а в нем модификация
					if ($oShop_Item->modification_id)
					{
						$oShop_Item = $oShop_Item->Modification
							->clearEntities()
							->addEntity($oShop_Item);
					}

					$aStructures[] = $oShop_Item;
				}

				if ($this->shop_group_id)
				{
					$groupId = $this->shop_group_id;

					$aGroups = array();

					while ($groupId)
					{
						$oShop_Group = Core_Entity::factory('Shop_Group', $groupId);

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

						$groupId = $oShop_Group->parent_id;
						$aGroups[] = $oShop_Group;
					}

					$aStructures = array_merge($aStructures, $aGroups);
				}
			}
		}

		$oStructure = Core_Entity::factory('Structure', $this->current)
			->clearEntities();

		do {
			$aStructures[] = $oStructure->clearEntities();
		} while($oStructure = $oStructure->getParent());

		$aStructures = array_reverse($aStructures);

		$object = $this;
		foreach ($aStructures as $oStructure)
		{
			$this->applyForbiddenTags($oStructure);
			$object->addEntity($oStructure);
			$object = $oStructure;
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

		// Clear
		$this->_aProperty_Dirs = $this->_aProperties = array();

		echo $content = parent::get();
		$this->cache && Core::moduleIsActive('cache') && $oCore_Cache->set($cacheKey, $content, $cacheName);

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
}
