<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Google sitemap
 * http://www.sitemaps.org/protocol.html
 *
 * - rebuildTime время в секундах, которое должно пройти с момента создания sitemap.xml для его перегенерации. По умолчанию 14400
 * - limit ограничение на единичную выборку элементов, по умолчанию 1000. При наличии достаточного объема памяти рекомендуется увеличить параметр
 * - createIndex разбивать карту на несколько файлов
 * - perFile Count of nodes per one file
 *
 * @package HostCMS 6\Core
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Sitemap extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'showInformationsystemGroups',
		'showInformationsystemItems',
		'showShopGroups',
		'showShopItems',
		'showModifications',
		'rebuildTime',
		'limit',
		'createIndex',
		'perFile'
	);

	/**
	 * Site
	 * @var Site_Model
	 */
	protected $_oSite = NULL;

	/**
	 * Constructor
	 * @param Site_Model $oSite Site object
	 */
	public function __construct(Site_Model $oSite)
	{
		parent::__construct();

		$this->_oSite = $oSite;

		$this->_aSiteuserGroups = array(0, -1);
		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$aSiteuser_Groups = $oSiteuser->Siteuser_Groups->findAll(FALSE);
				foreach ($aSiteuser_Groups as $oSiteuser_Group)
				{
					$this->_aSiteuserGroups[] = $oSiteuser_Group->id;
				}
			}
		}

		$this->rebuildTime = 14400; // 4 часа
		$this->limit = 1000;
		$this->createIndex = FALSE;
	}

	/**
	 * List of user groups
	 * @var array
	 */
	protected $_aSiteuserGroups = NULL;

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
	 * Get site
	 * @return Site_Model
	 */
	public function getSite()
	{
		return $this->_oSite;
	}

	/**
	 * Add structure nodes by parent
	 * @param int $structure_id structure ID
	 * @return self
	 */
	protected function _structure($structure_id = 0)
	{
		$oSite = $this->getSite();

		$oStructures = $oSite->Structures;
		$oStructures
			->queryBuilder()
			->where('structures.parent_id', '=', $structure_id)
			->where('structures.active', '=', 1)
			->where('structures.indexing', '=', 1)
			->where('structures.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
			->orderBy('sorting')
			->orderBy('name');

		$aStructure = $oStructures->findAll(FALSE);

		$dateTime = Core_Date::timestamp2sql(time());

		$oSite_Alias = $oSite->getCurrentAlias();

		foreach ($aStructure as $oStructure)
		{
			$this->addNode('http://' . $oSite_Alias->name . $oStructure->getPath(), $oStructure->changefreq, $oStructure->priority);

			// Informationsystem
			if ($this->showInformationsystemGroups && isset($this->_Informationsystems[$oStructure->id]))
			{
				$oInformationsystem = $this->_Informationsystems[$oStructure->id];

				$offset = 0;

				do {
					$oInformationsystem_Groups = $oInformationsystem->Informationsystem_Groups;
					$oInformationsystem_Groups->queryBuilder()
						->select('informationsystem_groups.id',
							'informationsystem_groups.informationsystem_id',
							'informationsystem_groups.parent_id',
							'informationsystem_groups.path'
							)
						->where('informationsystem_groups.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
						->where('informationsystem_groups.active', '=', 1)
						->where('informationsystem_groups.indexing', '=', 1)
						->offset($offset)->limit($this->limit);

					$aInformationsystem_Groups = $oInformationsystem_Groups->findAll(FALSE);

					$path = 'http://' . $oSite_Alias->name . $oInformationsystem->Structure->getPath();

					foreach ($aInformationsystem_Groups as $oInformationsystem_Group)
					{
						$this->addNode($path . $oInformationsystem_Group->getPath(), $oStructure->changefreq, $oStructure->priority);
					}
					$offset += $this->limit;
				}
				while (count($aInformationsystem_Groups));

				// Informationsystem's items
				if ($this->showInformationsystemItems)
				{
					$offset = 0;

					do {
						$oInformationsystem_Items = $oInformationsystem->Informationsystem_Items;
						$oInformationsystem_Items->queryBuilder()
							->select('informationsystem_items.id',
								'informationsystem_items.informationsystem_id',
								'informationsystem_items.informationsystem_group_id',
								'informationsystem_items.shortcut_id',
								'informationsystem_items.path'
								)
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
							->where('informationsystem_items.active', '=', 1)
							->where('informationsystem_items.shortcut_id', '=', 0)
							->where('informationsystem_items.indexing', '=', 1)
							->offset($offset)->limit($this->limit);

						$aInformationsystem_Items = $oInformationsystem_Items->findAll(FALSE);
						foreach ($aInformationsystem_Items as $oInformationsystem_Item)
						{
							$this->addNode($path . $oInformationsystem_Item->getPath(), $oStructure->changefreq, $oStructure->priority);
						}

						$offset += $this->limit;
					}
					while (count($aInformationsystem_Items));
				}
			}

			// Shop
			if ($this->showShopGroups && isset($this->_Shops[$oStructure->id]))
			{
				$oShop = $this->_Shops[$oStructure->id];

				$offset = 0;

				do {
					$oShop_Groups = $oShop->Shop_Groups;
					$oShop_Groups->queryBuilder()
						->select('shop_groups.id',
							'shop_groups.shop_id',
							'shop_groups.parent_id',
							'shop_groups.path'
							)
						->where('shop_groups.siteuser_group_id', 'IN', $this->_aSiteuserGroups)
						->where('shop_groups.active', '=', 1)
						->where('shop_groups.indexing', '=', 1)
						->offset($offset)->limit($this->limit);

					$aShop_Groups = $oShop_Groups->findAll(FALSE);

					$path = 'http://' . $oSite_Alias->name . $oShop->Structure->getPath();
					foreach ($aShop_Groups as $oShop_Group)
					{
						$this->addNode($path . $oShop_Group->getPath(), $oStructure->changefreq, $oStructure->priority);
					}

					$offset += $this->limit;
				}
				while (count($aShop_Groups));

				// Shop's items
				if ($this->showShopItems)
				{
					$offset = 0;

					do {
						$oShop_Items = $oShop->Shop_Items;
						$oShop_Items->queryBuilder()
							->select('shop_items.id',
								'shop_items.shop_id',
								'shop_items.shop_group_id',
								'shop_items.shortcut_id',
								'shop_items.modification_id',
								'shop_items.path'
								)
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
							->where('shop_items.active', '=', 1)
							->where('shop_items.shortcut_id', '=', 0)
							->where('shop_items.indexing', '=', 1)
							->offset($offset)->limit($this->limit);

						// Modifications
						if (!$this->showModifications)
						{
							$oShop_Items->queryBuilder()
								->where('shop_items.modification_id', '=', 0);
						}

						$aShop_Items = $oShop_Items->findAll(FALSE);
						foreach ($aShop_Items as $oShop_Item)
						{
							$this->addNode($path . $oShop_Item->getPath(), $oStructure->changefreq, $oStructure->priority);
						}

						$offset += $this->limit;
					}
					while (count($aShop_Items));
				}
			}

			// Structure
			$this->_structure($oStructure->id);
		}

		return $this;
	}

	/**
	 * Is it necessary to rebuild sitemap?
	 */
	protected $_bRebuild = TRUE;

	/**
	 * Fill nodes of structure
	 * @return self
	 */
	public function fillNodes()
	{
		$sIndexFilePath = $this->_getIndexFilePath();

		$this->_bRebuild = !is_file($sIndexFilePath) || time() > filemtime($sIndexFilePath) + $this->rebuildTime;

		if ($this->_bRebuild)
		{
			$this->_Informationsystems = $this->_Shops = array();

			$oSite = $this->getSite();

			if ($this->showInformationsystemGroups || $this->showInformationsystemItems)
			{
				$aInformationsystems = $oSite->Informationsystems->findAll();
				foreach ($aInformationsystems as $oInformationsystem)
				{
					$this->_Informationsystems[$oInformationsystem->structure_id] = $oInformationsystem;
				}
			}

			if ($this->showShopGroups || $this->showShopItems)
			{
				$aShops = $oSite->Shops->findAll();
				foreach ($aShops as $oShop)
				{
					$this->_Shops[$oShop->structure_id] = $oShop;
				}
			}

			$this->_structure(0);
		}

		return $this;
	}

	/**
	 * List of sitemap files
	 * @var array
	 */
	protected $_aIndexedFiles = array();

	/**
	 * Current output file
	 * @var Core_Out_File
	 */
	protected $_currentOut = NULL;

	/**
	 * Get current output file
	 * @return Core_Out_File
	 */
	protected function _getOut()
	{
		if ($this->createIndex)
		{
			if (is_null($this->_currentOut) || $this->_inFile >= $this->perFile)
			{
				$this->_getNewOutFile();
			}
		}
		elseif (is_null($this->_currentOut))
		{
			$this->_currentOut = new Core_Out_Std();
			$this->_open();
		}

		return $this->_currentOut;
	}

	/**
	 * Count URL in current file
	 * @var int
	 */
	protected $_inFile = 0;

	/**
	 * Sitemap files count
	 * @var int
	 */
	protected $_countFile = 1;

	/**
	 * Open current output file
	 * @return self
	 */
	protected function _open()
	{
		$this->_currentOut->open();
		$this->_currentOut->write('<?xml version="1.0" encoding="UTF-8"?>' . "\n")
			->write('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");
		return $this;
	}

	/**
	 * Close current output file
	 * @return self
	 */
	protected function _close()
	{
		if ($this->_currentOut)
		{
			$this->_currentOut->write("</urlset>\n");
			$this->_currentOut->close();
		}
		return $this;
	}

	/**
	 * Get new file for sitemap
	 */
	protected function _getNewOutFile()
	{
		if (!is_null($this->_currentOut))
		{
			$this->_close();

			$this->_countFile++;
			$this->_inFile = 0;
		}

		$this->_aIndexedFiles[] = $filename = "sitemap{$this->_countFile}.xml";

		$this->_currentOut = new Core_Out_File();
		$this->_currentOut->filePath(CMS_FOLDER . $filename);
		$this->_open();
	}

	/**
	 * Add node to sitemap
	 * @param string $loc location
	 * @param int $changefreq change frequency
	 * @param float $priority priority
	 * @return self
	 */
	public function addNode($loc, $changefreq, $priority)
	{
		switch ($changefreq)
		{
			case 0 : $sChangefreq = 'always'; break;
			case 1 : $sChangefreq = 'hourly'; break;
			default:
			case 2 : $sChangefreq = 'daily'; break;
			case 3 : $sChangefreq = 'weekly'; break;
			case 4 : $sChangefreq = 'monthly'; break;
			case 5 : $sChangefreq = 'yearly'; break;
			case 6 : $sChangefreq = 'never'; break;
		}

		$this->_getOut()->write(
			"<url>\n" .
			"<loc>{$loc}</loc>\n" .
			"<changefreq>" . Core_Str::xml($sChangefreq) . "</changefreq>\n" .
			"<priority>" . Core_Str::xml($priority) . "</priority>\n" .
			"</url>\n"
		);

		$this->_inFile++;

		return $this;
	}

	/**
	 * Get index file path
	 * @return string
	 */
	protected function _getIndexFilePath()
	{
		return CMS_FOLDER . 'sitemap.xml';
	}

	/**
	 * Executes the business logic.
	 */
	public function execute()
	{
		$this->_close();

		$sIndexFilePath = $this->_getIndexFilePath();

		if ($this->createIndex)
		{
			if ($this->_bRebuild)
			{
				$sIndex = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

				$oSite_Alias = $this->_oSite->getCurrentAlias();

				$sIndex .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
				foreach ($this->_aIndexedFiles as $filename)
				{
					$sIndex .= "<sitemap>\n";
					$sIndex .= "<loc>http://{$oSite_Alias->name}/{$filename}</loc>\n";
					$sIndex .= "<lastmod>" . date('Y-m-d') . "</lastmod>\n";
					$sIndex .= "</sitemap>\n";
				}

				$sIndex .= '</sitemapindex>';

				echo $sIndex;

				Core_File::write($sIndexFilePath, $sIndex);
			}
		}

		if (!$this->_bRebuild)
		{
			echo Core_File::read($sIndexFilePath);
		}

		return $this;
	}
}