<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Показ информационной системы.
 *
 * Доступные методы:
 *
 * - group($id) идентификатор информационной группы, если FALSE, то вывод информационных элементов осуществляется из всех групп
 * - groupsProperties(TRUE|FALSE|array()) выводить значения дополнительных свойств групп, по умолчанию FALSE. Может принимать массив с идентификаторами дополнительных свойств, значения которых необходимо вывести.
 * - groupsPropertiesList(TRUE|FALSE) выводить список дополнительных свойств групп информационных элементов, по умолчанию TRUE
 * - propertiesForGroups(array()) устанавливает дополнительное ограничение на вывод значений дополнительных свойств групп для массива идентификаторов групп.
 * - groupsMode('tree') режим показа групп, может принимать следующие значения:
	none - не показывать группы,
	tree - показывать дерево групп и все группы на текущем уровне (по умолчанию),
	all - показывать все группы.
 * - groupsForbiddenTags(array('description')) массив тегов групп, запрещенных к передаче в генерируемый XML
 * - item(123) идентификатор показываемого информационного элемента
 * - itemsProperties(TRUE|FALSE|array()) выводить значения дополнительных свойств информационных элементов, по умолчанию FALSE. Может принимать массив с идентификаторами дополнительных свойств, значения которых необходимо вывести.
 * - itemsPropertiesList(TRUE|FALSE) выводить список дополнительных свойств информационных элементов, по умолчанию TRUE
 * - itemsForbiddenTags(array('description')) массив тегов информационных элементов, запрещенных к передаче в генерируемый XML
 * - comments(TRUE|FALSE) показывать комментарии для выбранных информационных элементов, по умолчанию FALSE
 * - tags(TRUE|FALSE) выводить метки
 * - siteuser(TRUE|FALSE) показывать данные о пользователе сайта, связанного с выбранным информационным элементом, по умолчанию TRUE
 * - siteuserProperties(TRUE|FALSE) выводить значения дополнительных свойств пользователей сайта, по умолчанию FALSE
 * - offset($offset) смещение, с которого выводить информационные элементы. По умолчанию 0
 * - limit($limit) количество выводимых элементов
 * - page(2) текущая страница, по умолчанию 0, счет ведется с 0
 * - part($int) номер отображаемой части информационного элемента
 * - pattern($pattern) шаблон разбора данных в URI, см. __construct()
 * - tag($path) путь тега, с использованием которого ведется отбор информационных элементов
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
 * $Informationsystem_Controller_Show = new Informationsystem_Controller_Show(
 * 	Core_Entity::factory('Informationsystem', 1)
 * );
 *
 * $Informationsystem_Controller_Show
 * 	->xsl(
 * 		Core_Entity::factory('Xsl')->getByName('СписокНовостейНаГлавной')
 * 	)
 * 	->limit(5)
 * 	->show();
 * </code>
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Controller_Show extends Core_Controller
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
		'comments',
		'tags',
		'siteuser',
		'siteuserProperties',
		'offset',
		'limit',
		'page',
		'part',
		'total',
		'pattern',
		'patternExpressions',
		'patternParams',
		'tag',
		'cache',
		'itemsActivity',
		'groupsActivity',
		'commentsActivity',
		'showPanel',
	);

	/**
	 * List of groups of information systems
	 * @var array
	 */
	protected $_aInformationsystem_Groups = array();

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
	 * Information system's items object
	 * @var Informationsystem_Item_Model
	 */
	protected $_Informationsystem_Items = NULL;

	/**
	 * Information system's group object
	 * @var Informationsystem_Group_Model
	 */
	protected $_Informationsystem_Groups = NULL;

	/**
	 * Array of siteuser's groups allowed for current siteuser
	 * @var array
	 */
	protected $_aSiteuserGroups = array();

	/**
	 * Cache name
	 * @var string
	 */
	protected $_cacheName = 'informationsystem_show';

	/**
	 * Constructor.
	 * @param Informationsystem_Model $oInformationsystem information system
	 */
	public function __construct(Informationsystem_Model $oInformationsystem)
	{
		parent::__construct($oInformationsystem->clearEntities());

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

		$this->_setInformationsystemItems()->_setInformationsystemGroups();

		$this->group = 0;
		$this->item = NULL;
		$this->groupsProperties = $this->itemsProperties = $this->propertiesForGroups
			= $this->comments = $this->tags = $this->siteuserProperties = FALSE;
		$this->siteuser = $this->cache = $this->itemsPropertiesList = $this->groupsPropertiesList = TRUE;

		$this->groupsMode = 'tree';
		$this->offset = 0;
		$this->page = 0;
		$this->part = 1;
		$this->showPanel = TRUE;

		$this->itemsActivity = $this->groupsActivity = $this->commentsActivity = 'active'; // inactive, all

		$this->pattern = rawurldecode($this->getEntity()->Structure->getPath()) . '({path})(/part-{part}/)(page-{page}/)(tag/{tag}/)';
		$this->patternExpressions = array(
			'part' => '\d+',
			'page' => '\d+',
		);
	}

	/**
	 * Prepare items for showing
	 * @return self
	 */
	protected function _setInformationsystemItems()
	{
		$oInformationsystem = $this->getEntity();

		$this->_Informationsystem_Items = $oInformationsystem->Informationsystem_Items;

		switch ($oInformationsystem->items_sorting_direction)
		{
			case 1:
				$items_sorting_direction = 'DESC';
			break;
			case 0:
			default:
				$items_sorting_direction = 'ASC';
		}

		$this->_Informationsystem_Items
			->queryBuilder()
			->clearOrderBy();

		// Определяем поле сортировки информационных элементов
		switch ($oInformationsystem->items_sorting_field)
		{
			case 1:
				$this->_Informationsystem_Items
					->queryBuilder()
					->orderBy('informationsystem_items.name', $items_sorting_direction)
					->orderBy('informationsystem_items.sorting', $items_sorting_direction);
				break;
			case 2:
				$this->_Informationsystem_Items
					->queryBuilder()
					->orderBy('informationsystem_items.sorting', $items_sorting_direction)
					->orderBy('informationsystem_items.name', $items_sorting_direction);
				break;
			case 0:
			default:
				$this->_Informationsystem_Items
					->queryBuilder()
					->orderBy('informationsystem_items.datetime', $items_sorting_direction)
					->orderBy('informationsystem_items.sorting', $items_sorting_direction);
		}


		$this->_Informationsystem_Items
			->queryBuilder()
			->select('informationsystem_items.*')
			//->where('informationsystem_items.active', '=', 1)
			;

		$this->_applyItemConditions($this->_Informationsystem_Items);

		return $this;
	}

	/**
	 * Apply item's conditions
	 *
	 * @param Informationsystem_Item_Model $oInformationsystem_Items
	 * @return self
	 */
	protected function _applyItemConditions(Informationsystem_Item_Model $oInformationsystem_Items)
	{
		$dateTime = Core_Date::timestamp2sql(time());
		$oInformationsystem_Items
			->queryBuilder()
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
			->where('informationsystem_items.siteuser_group_id', 'IN', $this->_aSiteuserGroups);

		return $this;
	}

	/**
	 * Prepare groups for showing
	 * @return self
	 */
	protected function _setInformationsystemGroups()
	{
		$oInformationsystem = $this->getEntity();

		$this->_Informationsystem_Groups = $oInformationsystem->Informationsystem_Groups;
		$this->_Informationsystem_Groups
			->queryBuilder()
			->select('informationsystem_groups.*')
			->where('informationsystem_groups.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
			//->where('informationsystem_groups.active', '=', 1)
			;

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
				$this->_Informationsystem_Groups
					->queryBuilder()
					->orderBy('informationsystem_groups.name', $groups_sorting_direction);
				break;
			case 1:
			default:
				$this->_Informationsystem_Groups
					->queryBuilder()
					->orderBy('informationsystem_groups.sorting', $groups_sorting_direction);
				break;
		}

		return $this;
	}

	/**
	 * Get items
	 * @return Informationsystem_Item_Model
	 */
	public function informationsystemItems()
	{
		return $this->_Informationsystem_Items;
	}

	/**
	 * Get groups
	 * @return Informationsystem_Item_Model
	 */
	public function informationsystemGroups()
	{
		return $this->_Informationsystem_Groups;
	}

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Informationsystem_Controller_Show.onBeforeRedeclaredShow
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

		$oInformationsystem = $this->getEntity();

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
				->name('part')
				->value(intval($this->part - 1))
		)->addEntity(
			Core::factory('Core_Xml_Entity')
				->name('limit')
				->value(intval($this->limit))
		);

		// До вывода свойств групп
		if ($this->limit > 0 || $this->item)
		{
			$this->_itemCondition();

			// Group's conditions for information system item
			$this->group !== FALSE && $this->_groupCondition();

			if (!$this->item)
			{
				// Load model columns BEFORE FOUND_ROWS()
				Core_Entity::factory('Informationsystem_Item')->getTableColums();

				// Load user BEFORE FOUND_ROWS()
				Core_Entity::factory('User', 0)->getCurrent();

				$this->_Informationsystem_Items
					->queryBuilder()
					->sqlCalcFoundRows()
					->offset(intval($this->offset))
					->limit(intval($this->limit));
			}

			$aInformationsystem_Items = $this->_Informationsystem_Items->findAll();

			if (!$this->item)
			{
				if ($this->page && !count($aInformationsystem_Items))
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
			$oInformationsystem_Group_Property_List = Core_Entity::factory('Informationsystem_Group_Property_List', $oInformationsystem->id);

			$aProperties = $oInformationsystem_Group_Property_List->Properties->findAll();
			foreach ($aProperties as $oProperty)
			{
				$this->_aGroup_Properties[$oProperty->property_dir_id][] = $oProperty;

				// Load all values for property
				//$oProperty->loadAllValues();
			}

			$aProperty_Dirs = $oInformationsystem_Group_Property_List->Property_Dirs->findAll();
			foreach ($aProperty_Dirs as $oProperty_Dir)
			{
				$oProperty_Dir->clearEntities();
				$this->_aGroup_Property_Dirs[$oProperty_Dir->parent_id][] = $oProperty_Dir;
			}

			// Список свойств информационных элементов
			if ($this->groupsPropertiesList)
			{
				$Informationsystem_Group_Properties = Core::factory('Core_Xml_Entity')
						->name('informationsystem_group_properties');

				$this->addEntity($Informationsystem_Group_Properties);

				$this->_addGroupsPropertiesList(0, $Informationsystem_Group_Properties);
			}
		}

		is_array($this->groupsProperties) && $this->groupsProperties = array_combine($this->groupsProperties, $this->groupsProperties);

		// Устанавливаем активность групп
		$this->_setGroupsActivity();

		// Группы информационной системы
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

		// Показывать дополнительные свойства информационного элемента
		if ($this->itemsProperties)
		{
			$oInformationsystem_Item_Property_List = Core_Entity::factory('Informationsystem_Item_Property_List', $oInformationsystem->id);

			$aProperties = $oInformationsystem_Item_Property_List->Properties->findAll();
			foreach ($aProperties as $oProperty)
			{
				$this->_aItem_Properties[$oProperty->property_dir_id][] = $oProperty->clearEntities();

				// Load all values for property
				//$oProperty->loadAllValues();
			}

			$aProperty_Dirs = $oInformationsystem_Item_Property_List->Property_Dirs->findAll();
			foreach ($aProperty_Dirs as $oProperty_Dir)
			{
				$oProperty_Dir->clearEntities();
				$this->_aItem_Property_Dirs[$oProperty_Dir->parent_id][] = $oProperty_Dir->clearEntities();
			}

			// Список свойств информационных элементов
			if ($this->itemsPropertiesList)
			{
				$Informationsystem_Item_Properties = Core::factory('Core_Xml_Entity')
						->name('informationsystem_item_properties');

				$this->addEntity($Informationsystem_Item_Properties);

				$this->_addItemsPropertiesList(0, $Informationsystem_Item_Properties);
			}
		}

		if ($this->limit > 0)
		{
			foreach ($aInformationsystem_Items as $oInformationsystem_Item)
			{
				// Shortcut
				$bShortcut = $oInformationsystem_Item->shortcut_id;

				$bShortcut && $oInformationsystem_Item = $oInformationsystem_Item->Informationsystem_Item;

				// Ярлык может ссылаться на отключенный элемент
				$desiredActivity = strtolower($this->itemsActivity) == 'active'
					? 1
					: (strtolower($this->itemsActivity) == 'all' ? $oInformationsystem_Item->active : 0);

				//Ярлык может ссылаться на элемент с истекшим или не наступившим сроком публикации
				$iCurrentTimestamp = time();

				if ($oInformationsystem_Item->active == $desiredActivity
					&& (!$bShortcut
						||  (Core_Date::sql2timestamp($oInformationsystem_Item->end_datetime) >= $iCurrentTimestamp
							|| $oInformationsystem_Item->end_datetime == '0000-00-00 00:00:00')
						&& (Core_Date::sql2timestamp($oInformationsystem_Item->start_datetime) <= $iCurrentTimestamp
							|| $oInformationsystem_Item->start_datetime == '0000-00-00 00:00:00')
					)
				)
				{
					$oInformationsystem_Item->clearEntities();

					$this->applyItemsForbiddenTags($oInformationsystem_Item);

					// Comments
					$this->comments && $oInformationsystem_Item->showXmlComments(TRUE)->commentsActivity($this->commentsActivity);

					// Properties for informationsystem's item entity
					$this->itemsProperties && $oInformationsystem_Item->showXmlProperties($this->itemsProperties);

					// Tags
					$this->tags && $oInformationsystem_Item->showXmlTags(TRUE);

					// Siteuser
					$this->siteuser && $oInformationsystem_Item
						->showXmlSiteuser(TRUE)
						->showXmlSiteuserProperties($this->siteuserProperties);

					// <!-- pagebreak -->
					if ($this->part || $this->item)
					{
						$oInformationsystem_Item->showXmlPart($this->part);
					}

					$this->addEntity($oInformationsystem_Item);
				}
			}
		}

		// Clear
		$this->_aInformationsystem_Groups = $this->_aItem_Property_Dirs = $this->_aItem_Properties
			= $this->_aGroup_Properties = $this->_aGroup_Property_Dirs = array();

		echo $content = parent::get();
		$this->cache && Core::moduleIsActive('cache') && $oCore_Cache->set($cacheKey, $content, $this->_cacheName);

		return $this;
	}

	/**
	 * Inc Informationsystem_Item->showed
	 * @return self
	 */
	protected function _incShowed()
	{
		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($this->item);
		if (!is_null($oInformationsystem_Item->id))
		{
			$oInformationsystem_Item->showed += 1;
			$oInformationsystem_Item->save();
		}

		return $this;
	}

	/**
	 * Set item's conditions
	 * @return self
	 */
	protected function _itemCondition()
	{
		// Информационные элементы
		if ($this->item)
		{
			$this->_Informationsystem_Items
				->queryBuilder()
				->where('informationsystem_items.id', '=', intval($this->item));

			// Inc
			$this->_incShowed();
		}
		elseif (!is_null($this->tag) && Core::moduleIsActive('tag'))
		{
			$oTag = Core_Entity::factory('Tag')->getByPath($this->tag);

			if ($oTag)
			{
				$this->addEntity($oTag);

				$this->_Informationsystem_Items
					->queryBuilder()
					->leftJoin('tag_informationsystem_items', 'informationsystem_items.id', '=', 'tag_informationsystem_items.informationsystem_item_id')
					->where('tag_informationsystem_items.tag_id', '=', $oTag->id);

				// В корне при фильтрации по меткам вывод идет из всех групп ИС
				$this->group == 0 && $this->group = FALSE;
			}
		}

		$this->_setItemsActivity();

		return $this;
	}

	/**
	 * Set item's condition by informationsystem_group_id
	 * @return self
	 */
	protected function _groupCondition()
	{
		$this->_Informationsystem_Items
			->queryBuilder()
			->where('informationsystem_items.informationsystem_group_id', '=', intval($this->group));

		return $this;
	}

	/**
	 * Parse URL and set controller properties
	 * @return informationsystem_Controller_Show
	 */
	public function parseUrl()
	{
		$oInformationsystem = $this->getEntity();

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

		isset($matches['part']) && $this->part($matches['part']);

		if (isset($matches['tag']) && $matches['tag'] != '' && Core::moduleIsActive('tag'))
		{
			$this->tag($matches['tag']);

			$oTag = Core_Entity::factory('Tag')->getByPath($this->tag);
			if (is_null($oTag))
			{
				return $this->error404();
			}
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
				// Attempt to receive Informationsystem_Group
				$oInformationsystem_Groups = $oInformationsystem->Informationsystem_Groups;

				$this->groupsActivity = strtolower($this->groupsActivity);
				if($this->groupsActivity != 'all')
				{
					$oInformationsystem_Groups
						->queryBuilder()
						->where('active', '=', $this->groupsActivity == 'inactive' ? 0 : 1);
				}

				$oInformationsystem_Group = $oInformationsystem_Groups->getByParentIdAndPath($this->group, $sPath);

				if (!is_null($oInformationsystem_Group))
				{
					if (in_array($oInformationsystem_Group->getSiteuserGroupId(), $this->_aSiteuserGroups))
					{
						$this->group = $oInformationsystem_Group->id;
					}
					else
					{
						return $this->error403();
					}
				}
				else
				{
					// Attempt to receive Informationsystem_Item
					$oInformationsystem_Items = $oInformationsystem->Informationsystem_Items;

					$this->itemsActivity = strtolower($this->itemsActivity);
					if($this->itemsActivity != 'all')
					{
						$oInformationsystem_Items
							->queryBuilder()
							->where('informationsystem_items.active', '=', $this->itemsActivity == 'inactive' ? 0 : 1);
					}

					$this->_applyItemConditions($oInformationsystem_Items);
					
					$Informationsystem_Item = $oInformationsystem_Items->getByGroupIdAndPath($this->group, $sPath);

					if (!$this->item && !is_null($Informationsystem_Item))
					{
						if (in_array($Informationsystem_Item->getSiteuserGroupId(), $this->_aSiteuserGroups))
						{
							$this->group = $Informationsystem_Item->informationsystem_group_id;
							$this->item = $Informationsystem_Item->id;
						}
						else
						{
							return $this->error403();
						}
					}
					else
					{
						$this->item = FALSE;
						return $this->error404();
					}
				}
			}
		}

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
	 * Apply forbidden tags
	 * @param Informationsystem_Group $oInformationsystem_Group ticket
	 * @return self
	 */
	public function applyGroupsForbiddenTags($oInformationsystem_Group)
	{
		if (!is_null($this->groupsForbiddenTags))
		{
			foreach ($this->groupsForbiddenTags as $forbiddenTag)
			{
				$oInformationsystem_Group->addForbiddenTag($forbiddenTag);
			}
		}

		return $this;
	}

	/**
	 * Apply forbidden xml tags for items
	 * @param Informationsystem_Item_Model $oInformationsystem_Item item
	 * @return self
	 */
	public function applyItemsForbiddenTags($oInformationsystem_Item)
	{
		if (!is_null($this->itemsForbiddenTags))
		{
			foreach ($this->itemsForbiddenTags as $forbiddenTag)
			{
				$oInformationsystem_Item->addForbiddenTag($forbiddenTag);
			}
		}

		return $this;
	}

	/**
	 * Adding all groups for showing
	 * @return self
	 */
	public function addAllGroups()
	{
		$this->_aInformationsystem_Groups = array();

		$aInformationsystem_Groups = $this->_Informationsystem_Groups->findAll();

		foreach ($aInformationsystem_Groups as $oInformationsystem_Group)
		{
			$oInformationsystem_Group->clearEntities();
			$this->applyGroupsForbiddenTags($oInformationsystem_Group);
			$this->_aInformationsystem_Groups[$oInformationsystem_Group->parent_id][] = $oInformationsystem_Group;
		}

		$this->_addGroupsByParentId(0, $this);

		return $this;
	}

	/**
	 * Add groups tree
	 * @return self
	 */
	public function addTreeGroups()
	{
		$this->_aInformationsystem_Groups = array();

		// Потомки текущего уровня
		$aInformationsystem_Groups = $this->_Informationsystem_Groups->getByParentId($this->group);

		foreach ($aInformationsystem_Groups as $oInformationsystem_Group)
		{
			$oInformationsystem_Group->clearEntities();
			$this->applyGroupsForbiddenTags($oInformationsystem_Group);
			$this->_aInformationsystem_Groups[$oInformationsystem_Group->parent_id][] = $oInformationsystem_Group;
		}

		if ($this->group != 0)
		{
			$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group', $this->group)
				->clearEntities();

			do {
				$this->applyGroupsForbiddenTags($oInformationsystem_Group);

				$this->_aInformationsystem_Groups[$oInformationsystem_Group->parent_id][] = $oInformationsystem_Group;
			} while($oInformationsystem_Group = $oInformationsystem_Group->getParent());
		}

		$this->_addGroupsByParentId(0, $this);

		return $this;
	}

	/**
	 * Add groups to object by parent ID
	 * @param int $parent_id parent group ID
	 * @param object $parentObject object
	 * @return self
	 */
	protected function _addGroupsByParentId($parent_id, $parentObject)
	{
		if (isset($this->_aInformationsystem_Groups[$parent_id]))
		{
			$bIsArrayGroupsProperties = is_array($this->groupsProperties);
			$bIsArrayPropertiesForGroups = is_array($this->propertiesForGroups);

			foreach ($this->_aInformationsystem_Groups[$parent_id] as $oInformationsystem_Group)
			{
				// Properties for informationsystem's group entity
				if ($this->groupsProperties
					&& (!$bIsArrayPropertiesForGroups || in_array($oInformationsystem_Group->id, $this->propertiesForGroups)))
				{
					$aProperty_Values = $oInformationsystem_Group->getPropertyValues();

					if ($bIsArrayGroupsProperties)
					{
						foreach ($aProperty_Values as $oProperty_Value)
						{
							isset($this->groupsProperties[$oProperty_Value->property_id]) && $oInformationsystem_Group->addEntity($oProperty_Value);
						}
					}
					else
					{
						$oInformationsystem_Group->addEntities($aProperty_Values);
					}
				}

				$parentObject->addEntity($oInformationsystem_Group);

				$this->_addGroupsByParentId($oInformationsystem_Group->id, $oInformationsystem_Group);
			}
		}
		return $this;
	}

	/**
	 * Add items properties list to $parentObject
	 * @param int $parent_id parent group ID
	 * @param object $parentObject object
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
	 * Add groups properties list to $parentObject
	 * @param int $parent_id parent group ID
	 * @param object $parentObject object
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
	 * @return self
	 */
	protected function _showPanel()
	{
		$oInformationsystem = $this->getEntity();

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
			$sPath = '/admin/informationsystem/item/index.php';
			$sAdditional = "hostcms[action]=edit&informationsystem_id={$oInformationsystem->id}&informationsystem_group_id={$this->group}&hostcms[checked][1][0]=1";
			$sTitle = Core::_('Informationsystem_Item.information_items_add_form_title');

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

			$sPath = '/admin/informationsystem/item/index.php';
			$sAdditional = "hostcms[action]=edit&informationsystem_id={$oInformationsystem->id}&informationsystem_group_id={$this->group}&hostcms[checked][0][0]=1";
			$sTitle = Core::_('Informationsystem_Group.information_groups_add_form_title');

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
				$sPath = '/admin/informationsystem/item/index.php';
				$sAdditional = "hostcms[action]=edit&informationsystem_id={$oInformationsystem->id}&informationsystem_group_id={$this->group}&hostcms[checked][0][{$this->group}]=1";
				$sTitle = Core::_('Informationsystem_Group.information_groups_edit_form_title');

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

			$sPath = '/admin/informationsystem/index.php';
			$sAdditional = "hostcms[action]=edit&informationsystem_dir_id={$oInformationsystem->informationsystem_dir_id}&hostcms[checked][1][{$oInformationsystem->id}]=1";
			$sTitle = Core::_('Informationsystem.edit_title');

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
			$sPath = '/admin/informationsystem/item/index.php';
			$sAdditional = "hostcms[action]=edit&informationsystem_id={$oInformationsystem->id}&informationsystem_group_id={$this->group}&hostcms[checked][1][{$this->item}]=1";
			$sTitle = Core::_('Informationsystem_Item.information_items_edit_form_title');

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

			$sPath = '/admin/informationsystem/item/index.php';
			$sAdditional = "hostcms[action]=markDeleted&informationsystem_id={$oInformationsystem->id}&informationsystem_group_id={$this->group}&hostcms[checked][1][{$this->item}]=1";
			$sTitle = Core::_('Informationsystem_Item.markDeleted');

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
			$this->_Informationsystem_Items
				->queryBuilder()
				->where('informationsystem_items.active', '=', $this->itemsActivity == 'inactive' ? 0 : 1);
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
			$this->_Informationsystem_Groups
				->queryBuilder()
				->where('informationsystem_groups.active', '=', $this->groupsActivity == 'inactive' ? 0 : 1);
		}

		return $this;
	}
}
