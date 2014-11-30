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
class Shop_Item_Export_Cml_Controller extends Core_Servant_Properties
{
	/**
	 * Backend property
	 * @var mixed
	 */
	private $_xml;
	
	/**
	 * Backend property
	 * @var array
	 */
	private $_groupsID = array();
	
	/**
	 * Backend property
	 * @var mixed
	 */
	private $_retailPriceGUID;

	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'group',
		'shop',
		'exportItemExternalProperties',
		'exportItemModifications'
	);

	/**
	 * Generate CML for groups
	 * @param Shop_Group_Model $group start group
	 * @param SimpleXMLElement $xml target XML object
	 */
	private function getGroupsCML(Shop_Group_Model $group, $xml)
	{
		!in_array($group->id, $this->_groupsID) && $this->_groupsID[] = $group->id;

		if(intval($group->id) != 0)
		{
			$xml = $xml->addChild('Группы');
			$xml = $xml->addChild('Группа');
			$xml->addChild('Ид', $group->guid);
			$xml->addChild('Наименование', $group->name);
			$xml->addChild('Описание', $group->description);

			$groups = $group->Shop_Groups->findALL(FALSE);
		}
		else
		{
			$groups = $this->shop->Shop_Groups->getAllByParent_id(0, FALSE);
		}

		foreach($groups as $group)
		{
			$this->getGroupsCML($group, $xml);
		}
	}

	/**
	 * Constructor.
	 * @param Shop_Model $oShop shop
	 */
	public function __construct(Shop_Model $oShop)
	{
		parent::__construct();

		$this->shop = $oShop;
		$this->group = NULL;
		$this->_retailPriceGUID = Core_Guid::get();
		$this->exportItemExternalProperties = TRUE;
		$this->exportItemModifications = TRUE;
	}

	/**
	 * Set SimpleXMLElement object
	 * @return self
	 */
	protected function _setSimpleXML()
	{
		$this->_xml = new Core_SimpleXMLElement(sprintf(
			'<?xml version="1.0" encoding="utf-8"?><КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="%sT%s"></КоммерческаяИнформация>',
			date("Y-m-d"),
			date("H:i:s")));

		return $this;
	}

	/**
	 * Export import.xml
	 * @return string
	 */
	public function exportImport()
	{
		if($this->group === NULL)
		{
			throw new Core_Exception('Parent group does not specified!');
		}

		$this->_setSimpleXML();

		$classifier = $this->_xml->addChild('Классификатор');
		$catalog = $this->_xml->addChild('Каталог');

		// Группы товаров
		$this->getGroupsCML($this->group, $classifier);

		// Свойства товаров
		if($this->exportItemExternalProperties)
		{
			$aItemProperties = Core_Entity::factory('Shop_Item_Property_List', $this->shop->id)->Properties->findAll();
		}
		else
		{
			$aItemProperties = array();
		}

		if(count($aItemProperties) > 0)
		{
			$properties = $classifier->addChild('Свойства');

			foreach($aItemProperties as $oItemProperty)
			{
				$property = $properties->addChild('Свойство');
				$property->addChild('Ид', $oItemProperty->guid);
				$property->addChild('Наименование', $oItemProperty->name);
			}
		}

		// Товары
		$oShopItems = Core_Entity::factory('Shop_Item');
		$oQueryBuilder = $oShopItems->queryBuilder()
			->where('shop_group_id', 'IN', $this->_groupsID)
			->where('shop_id', '=', $this->shop->id);

		if($this->exportItemModifications !== FALSE)
		{
			$aItemsID = array();
			$aShopItems = $oShopItems->findAll(FALSE);
		
			foreach($aShopItems as $oShopItem)
			{
				$aItemsID[] = $oShopItem->id;
			}
	
			count($aShopItems) > 0 && $oQueryBuilder->where('modification_id', 'IN', $aItemsID);
			$aShopItems += $oShopItems->findAll(FALSE);
		}
		else
		{
			$oQueryBuilder->where('modification_id', '=', 0);
			$aShopItems = $oShopItems->findAll(FALSE);
		}

		if(count($aShopItems) > 0)
		{
			$catalog = $catalog->addChild('Товары');

			foreach ($aShopItems as $oShopItem)
			{
				$oShopItem->modification_id == 0 ? $sMod = '' : $sMod = $oShopItem->Modification->guid . '#';

				$item = $catalog->addChild('Товар');
				$item->addChild('Ид', $sMod . $oShopItem->guid);
				$item->addChild('Артикул', $oShopItem->marking);
				$item->addChild('Наименование', $oShopItem->name);
				$item->addChild('Описание', $oShopItem->description);
				$item->addChild('БазоваяЕдиница', $oShopItem->Shop_Measure->name)
					->addAttribute('НаименованиеПолное', $oShopItem->Shop_Measure->description);

				if($oShopItem->modification_id && $oShopItem->Modification->Shop_Group->id)
				{
					$item->addChild('Группы')->addChild('Ид', $oShopItem->Modification->Shop_Group->guid);
				}
				elseif($oShopItem->Shop_Group->id)
				{
					$item->addChild('Группы')->addChild('Ид', $oShopItem->Shop_Group->guid);
				}

				$oShopItem->image_large && $item->addChild('Картинка', $oShopItem->getItemHref() . $oShopItem->image_large);

				// Обработка дополнительных свойств
				$aShopItemPropertyValues = $oShopItem->getPropertyValues();

				if(count($aShopItemPropertyValues) > 0)
				{
					$properties = $item->addChild('ЗначенияСвойств');

					foreach ($aShopItemPropertyValues as $oShopItemPropertyValue)
					{
						$property = $properties->addChild('ЗначенияСвойства');

						$property->addChild('Ид', $oShopItemPropertyValue->Property->guid);

						if($oShopItemPropertyValue->Property->type == 2)
						{
							$property->addChild('Значение', $oShopItemPropertyValue->getLargeFileHref());
						}
						else
						{
							$property->addChild('Значение', $oShopItemPropertyValue->value);
						}
					}
				}
			}
		}

		return $this->_xml->asXML();
	}

	/**
	 * Export offers.xml
	 * @return string
	 */
	public function exportOffers()
	{
		if($this->group === NULL)
		{
			throw new Core_Exception("Parent group does not specified!");
		}

		$this->_setSimpleXML();

		$packageOfProposals = $this->_xml->addChild('ПакетПредложений');

		$packageOfProposals->addChild('Наименование', 'Пакет предложений');

		$retailPrice = $packageOfProposals->addChild('ТипыЦен')->addChild('ТипЦены');
		$retailPrice->addChild('Ид', $this->_retailPriceGUID);
		$retailPrice->addChild('Наименование', 'Розничная');
		$retailPrice->addChild('Валюта', $this->shop->Shop_Currency->code);

		$this->getGroupsCML($this->group, new Core_SimpleXMLElement("<root></root>"));

		$aShopItems = $this->shop->Shop_Items;
		$aShopItems->queryBuilder()
				  ->where('shop_group_id', 'IN', $this->_groupsID);
		$this->exportItemModifications === FALSE && $aShopItems->queryBuilder()->where('modification_id', '=', 0);
		$aShopItems = $aShopItems->findAll();

		if(count($aShopItems))
		{
			$packageOfProposals = $packageOfProposals->addChild('Предложения');

			foreach($aShopItems as $oShopItem)
			{
				$oShopItem->modification_id == 0 ? $sMod = '' : $sMod = $oShopItem->Modification->guid . '#';
				$proposal = $packageOfProposals->addChild('Предложение');
				$proposal->addChild('Ид', $sMod . $oShopItem->guid);
				$proposal->addChild('Артикул', $oShopItem->marking);
				$proposal->addChild('Наименование', $oShopItem->name);
				$proposal->addChild('БазоваяЕдиница', $oShopItem->Shop_Measure->name)
								->addAttribute('НаименованиеПолное', $oShopItem->Shop_Measure->description);
				$price = $proposal->addChild('Цены')->addChild('Цена');

				$price->addChild('ИдТипаЦены', $this->_retailPriceGUID);
				$price->addChild('ЦенаЗаЕдиницу', $oShopItem->price);
				$price->addChild('Представление',
								sprintf('%s %s за %s',
										$oShopItem->price,
										$oShopItem->Shop_Currency->code,
										$oShopItem->Shop_Measure->name));
				$price->addChild('Единица', $oShopItem->Shop_Measure->name);
				$proposal->addChild('Количество', $oShopItem->getRest());
			}
		}

		return $this->_xml->asXML();
	}
}