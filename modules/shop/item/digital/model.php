<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Item_Digital_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var int
	 */
	public $iternal_order = NULL;

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'count' => -1
	);

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'shop_order_item_digital' => array(),
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'shop_item' => array(),
	);

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;
		}
	}

	/**
	 * Get digital items count
	 */
	public function getCountDigitalItems()
	{
		$sum = 0;

		$aShop_Item_Digitals = $this->getBySorting();
		foreach ($aShop_Item_Digitals as $oShop_Item_Digital)
		{
			// Если хотя бы у одного электронного товара количество равно -1 (бесконечность), то считаем что весь товар неограничен
			if ($oShop_Item_Digital->count == -1)
			{
				$sum = -1;
				break;
			}

			$sum += $oShop_Item_Digital->count;
		}

		return $sum;
	}

	/**
	 * Get the most suit digital item
	 * @return array
	 */
	public function getBySorting()
	{
		$this->queryBuilder()
			->select('*')
			->select(
				array(Core_QueryBuilder::expression("IF(`count` = '-1', 2, IF(`count` = '0', 3, 1))"), 'iternal_order')
			)
			->orderBy('iternal_order')
			->orderBy('count')
			->orderBy('id');

		return $this->findAll();
	}

	/**
	 * Get file path
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->Shop_Item->Shop->getPath() . '/eitems/item_catalog_' . $this->Shop_Item->id . '/';
	}

	/**
	 * Get file href
	 * @return string
	 */
	public function getFileHref()
	{
		return '/' . $this->Shop_Item->Shop->getHref() . '/eitems/item_catalog_' . $this->Shop_Item->id . '/';
	}

	/**
	 * Get full file path
	 * @return string
	 */
	public function getFullFilePath()
	{
		return $this->getFilePath() . $this->id . (Core_File::getExtension($this->filename) != ''
			? '.' . Core_File::getExtension($this->filename)
			: ''
		);
	}

	/**
	 * Get full file href
	 * @return string
	 */
	public function getFullFileHref()
	{
		return $this->getFileHref() . $this->id . rawurlencode(
			Core_File::getExtension($this->filename) != ''
				? '.' . Core_File::getExtension($this->filename)
				: ''
		);
	}

	/**
	 * Create directory for item
	 * @return self
	 */
	public function createDir()
	{
		if (!is_dir($this->getFilePath()))
		{
			try
			{
				Core_File::mkdir($this->getFilePath(), CHMOD, TRUE);
			} catch (Exception $e) {}
		}

		return $this;
	}

	/**
	 * Delete digital item's file
	 */
	public function deleteFile()
	{
		try
		{
			Core_File::delete($this->getFullFilePath());
		} catch (Exception $e) {}

		$this->filename = '';
		$this->save();
	}

	/**
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Core_Entity
	 */
	public function delete($primaryKey = NULL)
	{
		if (is_null($primaryKey))
		{
			$primaryKey = $this->getPrimaryKey();
		}

		$this->id = $primaryKey;

		$this->Shop_Order_Item_Digitals->deleteAll(FALSE);

		try
		{
			Core_File::delete($this->getFullFilePath());
		} catch (Exception $e){}

		return parent::delete($primaryKey);
	}

	/**
	 * Get XML for entity and children entities
	 * @return string
	 * @hostcms-event shop_item_digital.onBeforeRedeclaredGetXml
	 */
	public function getXml()
	{
		Core_Event::notify($this->_modelName . '.onBeforeRedeclaredGetXml', $this);

		if ($this->filename != '')
		{
			$this->clearXmlTags()
				->addXmlTag('path', $this->getFullFilePath());
		}

		return parent::getXml();
	}
}