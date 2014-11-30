<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Экспорт в Yandex.Realty для магазина.
 *
 * <code>
 * $Shop_Controller_YandexRealty = new Shop_Controller_YandexRealty(
 * 	Core_Entity::factory('Shop', 1)
 * );
 *
 * $Shop_Controller_YandexRealty->show();
 * </code>
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Controller_YandexRealty extends Core_Controller
{
	/**
	 * Shop's items object
	 * @var Shop_Item_Model
	 */
	protected $_Shop_Items = NULL;

	/**
	 * Array of siteuser's groups allowed for current siteuser
	 * @var array
	 */
	protected $_aSiteuserGroups = array();

	public $aListTags = array(
		/* Основные */
		'type',
		'property-type',
		'category',
		'payed-adv',
		'manually-added',
		'not-for-agents',
		'haggle',
		'quality',
		'mortgage',
		'prepayment',
		'rent-pledge',
		'agent-fee',
		'with-pets',
		'with-children',
		'renovation',
		'lot-type',

		/* Описание жилого помещения */
		'new-flat',
		'rooms',
		'rooms-offered',
		'open-plan',
		'rooms-type',
		'phone',
		'internet',
		'room-furniture',
		'kitchen-furniture',
		'television',
		'washing-machine',
		'refrigerator',
		'balcony',
		'bathroom-unit',
		'floor-covering',
		'window-view',

		/* Описание здания  */
		'floors-total',
		'building-name',
		'building-type',
		'building-series',
		'building-state',
		'built-year',
		'ready-quarter',
		'lift',
		'rubbish-chute',
		'is-elite',
		'parking',
		'alarm',
		'ceiling-height',

		/* Для загородной недвижимости */
		'pmg',
		'toilet',
		'shower',
		'kitchen',
		'pool',
		'sauna',
		'heating-supply',
		'water-supply',
		'sewerage-supply',
		'electricity-supply',
		'gas-supply',
		);

		public $aLocationTags = array(
		'country',
		'region',
		'district',
		'locality-name',
		'sub-locality-name',
		'non-admin-sub-locality',
		'address',
		'direction',
		'distance',
		'latitude',
		'longitude',
		'metro',
		'name',
		'time-on-transport',
		'time-on-foot',
		'railway-station',
	);

		public $aAreaTags = array(
		/* Информация о площадях объекта */
			'area',
			'living-space',
			'kitchen-space',
			'lot-area',
		);

	/**
	 * Constructor.
	 * @param Shop_Model $oShop shop
	 */
	public function __construct(Shop_Model $oShop)
	{
		parent::__construct($oShop->clearEntities());

		$this->_Shop_Items = $oShop->Shop_Items;

		$siteuser_id = 0;

		$this->_aSiteuserGroups = array(0, -1);

		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$siteuser_id = $oSiteuser->id;

				$aSiteuser_Groups = $oSiteuser->Siteuser_Groups->findAll(FALSE);
				foreach ($aSiteuser_Groups as $oSiteuser_Group)
				{
					$this->_aSiteuserGroups[] = $oSiteuser_Group->id;
				}
			}
		}

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
				$this->_Shop_Items
					->queryBuilder()
					->orderBy('shop_items.name', $items_sorting_direction);
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
					->orderBy('shop_items.datetime', $items_sorting_direction);
		}

		$dateTime = Core_Date::timestamp2sql(time());
		$this->_Shop_Items
			->queryBuilder()
			->select('shop_items.*')
			->join('shop_groups', 'shop_groups.id', '=', 'shop_items.shop_group_id',
				array(
						array('AND' => array('shop_groups.active', '=', 1)),
						array('OR' => array('shop_items.shop_group_id', '=', 0))
					)
			)
			->where('shop_items.shortcut_id', '=', 0)
			->where('shop_items.active', '=', 1)
			->where('shop_items.siteuser_id', 'IN', $this->_aSiteuserGroups)
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
			->where('shop_items.yandex_market', '=', 1)
			->where('shop_items.price', '>', 0)
			->groupBy('shop_items.id');
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
	 * Show offers
	 * @return self
	 */
	protected function _offers()
	{
		$oShop = $this->getEntity();

		$offset = 0;
		$limit = 100;

		$oShop_Item_Property_List = Core_Entity::factory('Shop_Item_Property_List', $oShop->id);

		/* Получаем свойства по имени */
		$aListProperties = array();
		foreach ($this->aListTags as $tagName)
		{
			$aListProperties[$tagName] = $oShop_Item_Property_List->Properties->getByTag_name($tagName);
		}

		/* Получаем свойства по имени */
		$aLocationProperties = array();
		foreach ($this->aLocationTags as $locationTagName)
		{
			$aLocationProperties[$locationTagName] = $oShop_Item_Property_List->Properties->getByTag_name($locationTagName);
		}

		foreach ($this->aAreaTags as $areaTagName)
		{
			$aAreaProperties[$areaTagName]['value'] = $oShop_Item_Property_List->Properties->getByTag_name($areaTagName . '-value');
			$aAreaProperties[$areaTagName]['unit'] = $oShop_Item_Property_List->Properties->getByTag_name($areaTagName . '-unit');
		}

		/* Описание параметров, входящих в элемент */
		do {
			$oShop_Items = $this->_Shop_Items;
			$oShop_Items->queryBuilder()->offset($offset)->limit($limit);
			$aShop_Items = $oShop_Items->findAll(FALSE);

			foreach ($aShop_Items as $oShop_Item)
			{
				/* Объявление */
				echo '<offer internal-id="'. $oShop_Item->id . '">'."\n";

				foreach ($this->aListTags as $tagName)
				{
					$oProperty = $aListProperties[$tagName];

					if (!is_null($oProperty))
					{
						$aProperty_Values = $oProperty->getValues($oShop_Item->id);

						foreach ($aProperty_Values as $oProperty_Value)
						{
							$oProperty = $oProperty_Value->Property;

							switch ($oProperty->type)
							{
								case 0: // Int
								case 1: // String
								case 4: // Textarea
								case 6: // Wysiwyg
								case 8: // Date
								case 9: // Datetime
									$value = $oProperty_Value->value;
								break;

								case 3: // List
									$value = NULL;

									$oList_Item = $oProperty->List->List_Items->getById(
										$oProperty_Value->value, FALSE
									);

									!is_null($oList_Item) && $value = $oList_Item->value;
								break;

								case 7: // Checkbox
									$value = $oProperty_Value->value == 1 ? 'есть' : NULL;
								break;

								case 2: // File
								case 5: // ИС
								case 10: // Hidden field
								default:
									$value = NULL;
								break;
							}

							if (!is_null($value))
							{
								echo '<' . $tagName . '>' . $value . '</' . $tagName . '>'."\n";
							}
						}
					}
				}

				echo '<url>' . Core_Str::xml($this->_shopPath . $oShop_Item->getPath()) . '</url>'. "\n";
				echo '<creation-date>' . date('c', Core_Date::sql2timestamp($oShop_Item->datetime)) . '</creation-date>'."\n";
				echo '<expire-date>' . date('c', $oShop_Item->end_datetime == '0000-00-00 00:00:00'
					? time() + 60*60*24*30
					: Core_Date::sql2timestamp($oShop_Item->end_datetime)) . '</expire-date>'."\n";
				echo '<last-update-date>' . date('c', Core_Date::sql2timestamp($oShop_Item->datetime)) . '</last-update-date>'."\n";

				/* Информация о местоположении */
				echo '<location>'."\n";
				foreach ($this->aLocationTags as $locationTagName)
				{
					$oLocation = $aLocationProperties[$locationTagName];

					if (!is_null($oLocation))
					{
						$aLocationValues = $oLocation->getValues($oShop_Item->id);
						if(isset($aLocationValues[0]))
						{
							echo '<' . $locationTagName . '>' . $aLocationValues[0]->value . '</' . $locationTagName . '>'."\n";
						}
					}
				}
				echo '</location>'."\n";

				/* Информация о продавце */
				echo '<sales-agent>'."\n";

				if ($oShop_Item->Shop_Seller->contact_person != '')
				{
					echo '<name>' . $oShop_Item->Shop_Seller->contact_person . '</name>'."\n";
				}

				echo '<phone>' . $oShop_Item->Shop_Seller->phone . '</phone>'."\n";
				echo '<organization>' . $oShop_Item->Shop_Seller->name . '</organization>'."\n";

				if ($oShop_Item->Shop_Seller->site != '')
				{
					echo '<url>' . $oShop_Item->Shop_Seller->site . '</url>'."\n";
				}

				if ($oShop_Item->Shop_Seller->email != '')
				{
					echo '<email>' . $oShop_Item->Shop_Seller->email . '</email>'."\n";
				}
				echo '</sales-agent>'."\n";

				/* Информация о сделке */
				echo '<price>'."\n";
					echo '<value>' . $oShop_Item->price . '</value>'."\n";
					echo '<currency>' . $oShop_Item->Shop_Currency->code . '</currency>'."\n";
				echo '</price>'."\n";

				if ($oShop_Item->image_large != '')
				{
					echo '<image>' . 'http://' . Core_Str::xml($this->_siteAlias->name . $oShop_Item->getLargeFileHref()) . '</image>'."\n";
				}

				if ($oShop_Item->description != '')
				{
					echo '<description>' . $oShop_Item->description . '</description>'."\n";
				}

				foreach ($this->aAreaTags as $areaTagName)
				{
					echo '<'. $areaTagName . '>'."\n";
					foreach (array('value', 'unit') as $subAreaTagName)
					{
						$oArea = $aAreaProperties[$areaTagName][$subAreaTagName];

						if (!is_null($oArea))
						{
							$aAreaValues = $oArea->getValues($oShop_Item->id);
							if(isset($aAreaValues[0]))
							{
								echo '<' . $areaTagName . '-' . $subAreaTagName . '>' .
									$aAreaValues[0]->value .
								'</' . $areaTagName . '-' . $subAreaTagName . '>'."\n";
							}
						}
					}

					echo '</'. $areaTagName . '>'."\n";
				}

				echo '</offer>'."\n";
			}

			Core_File::flush();
			$offset += $limit;
		}
		while (count($aShop_Items));

		return $this;
	}

	/**
	 * Current site alias
	 * @var string
	 */
	protected $_siteAlias = NULL;

	/**
	 * Shop URL
	 * @var string
	 */
	protected $_shopPath = NULL;

	/**
	 * Show built data
	 * @return self
	 * @hostcms-event Shop_Controller_YandexRealty.onBeforeRedeclaredShow
	 */
	public function show()
	{
		Core_Event::notify(get_class($this) . '.onBeforeRedeclaredShow', $this);

		$oShop = $this->getEntity();
		$oSite = $oShop->Site;

		Core_Page::instance()->response
			->header('Content-Type', "text/xml; charset={$oSite->coding}")
			->sendHeaders();

		$this->_siteAlias = $oSite->getCurrentAlias();
		$this->_shopPath = 'http://' . $this->_siteAlias->name . $oShop->Structure->getPath();

		echo '<?xml version="1.0" encoding="' . $oSite->coding . '"?>' . "\n";
		echo '<realty-feed xmlns="http://webmaster.yandex.ru/schemas/feed/realty/2010-06">'."\n";
		echo '<generation-date>'. date('c') . '</generation-date>'."\n";

		/* Товары */
		$this->_offers();

		echo '</realty-feed>'."\n";

		Core_File::flush();
	}
}