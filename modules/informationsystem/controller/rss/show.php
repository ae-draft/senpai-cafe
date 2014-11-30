<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Показ RSS-ленты информационной системы.
 *
 * Доступные методы:
 *
 * - group($id) идентификатор информационной группы, если FALSE, то вывод инофрмационных элементов
 * осуществляется из всех групп
 * - offset($offset) смещение, с которого выводить информационные элементы. По умолчанию 0
 * - limit($limit) количество выводимых элементов
 *
 * <code>
 * $Informationsystem_Controller_Rss_Show = new Informationsystem_Controller_Rss_Show(
 * 		Core_Entity::factory('Informationsystem', 1)
 * 	);
 *
 * 	$Informationsystem_Controller_Rss_Show
 * 		->limit(10)
 * 		->show();
 * </code>
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Controller_Rss_Show extends Core_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'title',
		'description',
		'link',
		'image',
		'group',
		'offset',
		'limit',
		'yandex',
		'stripTags',
		'cache',
	);

	/**
	 * Information system's items object
	 * @var array
	 */
	protected $_Informationsystem_Items = array();

	/**
	 * RSS
	 * @var Core_Rss
	 */
	protected $_Core_Rss = array();

	/**
	 * Path
	 * @var string
	 */
	protected $_path = NULL;

	/**
	 * Constructor.
	 * @param Informationsystem_Model $oInformationsystem information system
	 */
	public function __construct(Informationsystem_Model $oInformationsystem)
	{
		parent::__construct($oInformationsystem->clearEntities());

		$this->_Informationsystem_Items = $oInformationsystem->Informationsystem_Items;

		$siteuser_id = 0;

		$aSiteuserGroups = array(0, -1);
		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$siteuser_id = $oSiteuser->id;

				$aSiteuser_Groups = $oSiteuser->Siteuser_Groups->findAll();
				foreach ($aSiteuser_Groups as $oSiteuser_Group)
				{
					$aSiteuserGroups[] = $oSiteuser_Group->id;
				}
			}
		}

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
				$this->_Informationsystem_Items
					->queryBuilder()
					->clearOrderBy()
					->orderBy('informationsystem_items.name', $items_sorting_direction);
				break;
			case 2:
				$this->_Informationsystem_Items
					->queryBuilder()
					->clearOrderBy()
					->orderBy('informationsystem_items.sorting', $items_sorting_direction)
					->orderBy('informationsystem_items.name', $items_sorting_direction);
				break;
			case 0:
			default:
				$this->_Informationsystem_Items
					->queryBuilder()
					->clearOrderBy()
					->orderBy('informationsystem_items.datetime', $items_sorting_direction);
		}

		$dateTime = Core_Date::timestamp2sql(time());
		$this->_Informationsystem_Items
			->queryBuilder()
			->sqlCalcFoundRows()
			->select('informationsystem_items.*')
			->where('informationsystem_items.active', '=', 1)
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
			->where('informationsystem_items.siteuser_group_id', 'IN', $aSiteuserGroups);

		$this->group = FALSE;
		$this->offset = 0;
		$this->stripTags = TRUE;
		$this->yandex = FALSE;
		$this->cache = TRUE;

		$this->_Core_Rss = new Core_Rss();
	}

	/**
	 * Get information items
	 * @return array
	 */
	public function informationsystemItems()
	{
		return $this->_Informationsystem_Items;
	}

	/**
	 * Get RSS
	 * @return Core_Rss
	 */
	public function coreRss()
	{
		return $this->_Core_Rss;
	}

	/**
	 * Show RSS
	 * @return self
	 * @hostcms-event Informationsystem_Controller_Rss_Show.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);
	
		$oInformationsystem = $this->getEntity();

		$oSiteAlias = $oInformationsystem->Site->getCurrentAlias();
		if ($oSiteAlias)
		{
			$this->_path = 'http://' . $oSiteAlias->name . $oInformationsystem->Structure->getPath();
		}

		$this->_Core_Rss
			->add('title', !is_null($this->title) ? $this->title : $oInformationsystem->name)
			->add('description', !is_null($this->description) ? $this->description : ($this->stripTags
					? strip_tags($oInformationsystem->description)
					: $oInformationsystem->description));

		$this->_Core_Rss->add('link', !is_null($this->link)
			? $this->link
			: $this->_path
		);

		if (is_array($this->image) && count($this->image))
		{
			$this->_Core_Rss->add('image', $this->image);
		}

		if ($this->cache && Core::moduleIsActive('cache'))
		{
			$oCore_Cache = Core_Cache::instance(Core::$mainConfig['defaultCache']);
			$inCache = $oCore_Cache->get($cacheKey = strval($this), $cacheName = 'informationsystem_rss');

			if (!is_null($inCache))
			{
				$this->_Core_Rss->showWithHeader($inCache);
				return $this;
			}
		}

		if ($this->yandex)
		{
			$this->_Core_Rss->xmlns('yandex', 'http://news.yandex.ru');
		}

		if ($this->group !== FALSE)
		{
			$this->_Informationsystem_Items
				->queryBuilder()
				->where('informationsystem_group_id', '=', intval($this->group));
		}

		// Load model columns BEFORE FOUND_ROWS()
		Core_Entity::factory('Informationsystem_Item')->getTableColums();

		// Load user BEFORE FOUND_ROWS()
		$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();

		if ($this->limit)
		{
			$this->_Informationsystem_Items
				->queryBuilder()
				->offset(intval($this->offset))
				->limit(intval($this->limit));
		}

		$aInformationsystem_Items = $this->_Informationsystem_Items->findAll();

		$oSiteAlias = $oInformationsystem->Site->getCurrentAlias();
		$sitePath = $oSiteAlias
			? 'http://' . $oSiteAlias->name
			: NULL;

		foreach ($aInformationsystem_Items as $oInformationsystem_Item)
		{
			$aItem = array();
			$aItem['pubDate'] = date('r', Core_Date::sql2timestamp($oInformationsystem_Item->datetime));
			$aItem['title'] = Core_Str::str2ncr(
				Core_Str::xml($this->stripTags
					? strip_tags($oInformationsystem_Item->name)
					: $oInformationsystem_Item->name
				)
			);

			$aItem['description'] = Core_Str::str2ncr(
				Core_Str::xml($this->stripTags
					? strip_tags($oInformationsystem_Item->description)
					: $oInformationsystem_Item->description)
			);

			if ($this->yandex)
			{
				$aItem['yandex:full-text'] = Core_Str::str2ncr(
					Core_Str::xml($this->stripTags
						? strip_tags($oInformationsystem_Item->text)
						: $oInformationsystem_Item->text)
				);

				if ($oInformationsystem_Item->Informationsystem_Group->id)
				{
					$aItem['category'] = Core_Str::str2ncr(Core_Str::xml($oInformationsystem_Item->Informationsystem_Group->name));
				}
			}

			$aItem['link'] = $aItem['guid'] = Core_Str::str2ncr(Core_Str::xml($this->_path . $oInformationsystem_Item->getPath()));

			if ($oInformationsystem_Item->image_large)
			{
				$file_enclosure = $oInformationsystem_Item->getLargeFilePath();

				$aItem['enclosure'][0]['url'] = $sitePath . $oInformationsystem_Item->getLargeFileHref();
				$aItem['enclosure'][0]['type'] = Core_Mime::getFileMime($aItem['enclosure'][0]['url']);

				if (is_file($file_enclosure))
				{
					$aItem['enclosure'][0]['length'] = filesize($file_enclosure);
				}
			}

			$this->_Core_Rss->add('item', $aItem);
		}

		$content = $this->_Core_Rss->get();
		$this->_Core_Rss->showWithHeader($content);
		$this->cache && Core::moduleIsActive('cache') && $oCore_Cache->set($cacheKey, $content, $cacheName);

		return $this;
	}
}
