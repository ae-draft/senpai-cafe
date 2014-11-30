<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Properties.
 *
 * @package HostCMS 6\Property
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Property_Value_Int_Model extends Core_Entity
{
	/**
	 * Model name
	 * @var mixed
	 */
	protected $_modelName = 'property_value_int';

	/**
	 * Disable markDeleted()
	 * @var mixed
	 */
	protected $_marksDeleted = NULL;

	/**
	 * Column consist item's name
	 * @var string
	 */
	protected $_nameColumn = 'id';

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'property' => array(),
		'list_item' => array('foreign_key' => 'value'),
		'informationsystem_item' => array('foreign_key' => 'value'),
		'shop_item' => array('foreign_key' => 'value'),
	);

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'property_value_ints.id' => 'ASC'
	);

	/**
	 * Set property value
	 * @param int $value value
	 * @return self
	 */
	public function setValue($value)
	{
		$this->value = intval($value);
		return $this;
	}

	/**
	 * Name of the tag in XML
	 * @var string
	 */
	protected $_tagName = 'property_value';

	/**
	 * Module config
	 */
	static public $aConfig = NULL;

	/**
	 * Constructor.
	 * @param string $primaryKey
	 */
	public function __construct($primaryKey = NULL)
	{
		parent::__construct($primaryKey);

		if (is_null(self::$aConfig))
		{
			self::$aConfig = Core_Config::instance()->get('property_config', array()) + array(
				'recursive_properties' => TRUE,
			);
		}
	}

	/**
	 * Get XML for entity and children entities
	 * @return string
	 * @hostcms-event property_value_int.onBeforeRedeclaredGetXml
	 */
	public function getXml()
	{
		Core_Event::notify($this->_modelName . '.onBeforeRedeclaredGetXml', $this);

		$oProperty = $this->Property;

		$this->clearXmlTags()
			->addXmlTag('property_dir_id', $this->Property->property_dir_id)
			->addXmlTag('tag_name', $oProperty->tag_name);

		// List
		if ($oProperty->type == 3 && Core::moduleIsActive('list'))
		{
			$this->addForbiddenTag('value');

			if ($this->value != 0)
			{
				$oList_Item = $this->List_Item;

				if ($oList_Item->id)
				{
					$this
						->addXmlTag('value', $oList_Item->value)
						->addXmlTag('description', $oList_Item->description);
				}
			}
		}

		// Informationsystem
		if ($oProperty->type == 5 && Core::moduleIsActive('informationsystem'))
		{
			$this->addForbiddenTag('value');

			if ($this->value != 0)
			{
				// Allow all kinds of properties except informationsystem
				$oInformationsystem_Item_Property_List = Core_Entity::factory('Informationsystem_Item_Property_List', $this->Informationsystem_Item->informationsystem_id);

				$aTmp = array();
				$aItemProperties = $oInformationsystem_Item_Property_List->Properties->findAll();
				foreach ($aItemProperties as $oItemProperty)
				{
					// Зацикленность через Св-во типа ИЭ/Товар, у которого св-во ИЭ/Товар
					($oItemProperty->type != 5 && $oItemProperty->type != 12
						|| self::$aConfig['recursive_properties'] && $oItemProperty->informationsystem_id != $oProperty->informationsystem_id
					) && $aTmp[] = $oItemProperty->id;
				}

				$oInformationsystem_Item = $this->Informationsystem_Item;
				if ($oInformationsystem_Item->id)
				{
					$oInformationsystem_Item->shortcut_id && $oInformationsystem_Item = $oInformationsystem_Item->Informationsystem_Item;

					$this->addEntity(
						$oInformationsystem_Item->clearEntities()->showXmlProperties(count($aTmp) ? $aTmp : FALSE)
					);
				}
			}
		}

		// Shop
		if ($oProperty->type == 12 && Core::moduleIsActive('shop'))
		{
			$this->addForbiddenTag('value');

			if ($this->value != 0)
			{
				// Allow all kinds of properties except shop
				$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $this->Shop_Item->shop_id);

				$aTmp = array();
				$aItemProperties = $oShop_Item_Property_List->Properties->findAll();
				foreach ($aItemProperties as $oItemProperty)
				{
					// Зацикленность через Св-во типа ИЭ/Товар, у которого св-во ИЭ/Товар
					($oItemProperty->type != 12 && $oItemProperty->type != 5
						|| self::$aConfig['recursive_properties'] && $oItemProperty->shop_id != $oProperty->shop_id
					) && $aTmp[] = $oItemProperty->id;
				}

				$oShop_Item = $this->Shop_Item;
				if ($oShop_Item->id)
				{
					$oShop_Item->shortcut_id && $oShop_Item = $oShop_Item->Shop_Item;

					$this->addEntity(
						$oShop_Item->clearEntities()->showXmlProperties(count($aTmp) ? $aTmp : FALSE)
					);
				}
			}
		}

		return parent::getXml();
	}
}