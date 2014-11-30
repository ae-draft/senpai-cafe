<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Load object's fields when object has been set
	 * После установки объекта загружаются данные о его полях
	 * @param object $object
	 * @return Shop_Controller_Edit
	 */
	public function setObject($object)
	{
		$modelName = $object->getModelName();

		$oAdminFormEntitySelect = Admin_Form_Entity::factory('Select');

		$oSeparatorField = Admin_Form_Entity::factory('Separator');

		$oAdminFormEntitySelect->caption(Core::_('Shop_Dir.parent_id'));

		switch($modelName)
		{
			case 'shop_dir':
			$title = $object->id
					? Core::_('Shop_Dir.edit_title')
					: Core::_('Shop_Dir.add_title');

			if (is_null($object->id))
			{
				$object->parent_id = intval(Core_Array::getGet('shop_dir_id', 0));
			}

			parent::setObject($object);

			$oMainTab = $this->getTab('main');

			$oAdditionalTab = $this->getTab('additional');

			$oAdditionalTab->delete(
					$this->getField('parent_id')
			);

			$oAdminFormEntitySelect
				->options(
					array(' … ') + $this->_fillShopDir(0, $object->id)
				)
				->name('parent_id')
				->value($this->_object->parent_id);

			$oMainTab->addAfter(
					$oAdminFormEntitySelect, $this->getField('description')
				);

			break;

			case 'shop':

			// Исключение поля из формы и обработки
			$this
				->addSkipColumn('watermark_file');

			$title = $object->id
					? Core::_('Shop.edit_title')
					: Core::_('Shop.add_title');

			if (is_null($object->id))
			{
				$object->shop_dir_id = intval(Core_Array::getGet('shop_dir_id', 0));
			}

			parent::setObject($object);

			// Получаем экземпляр класса разделителя
			$oSeparatorField = Admin_Form_Entity::factory('Separator');

			$oShopTabFormats = Admin_Form_Entity::factory('Tab')
				->caption(Core::_('Shop.tab_formats'))
				->name('Formats');
			$oShopTabExport = Admin_Form_Entity::factory('Tab')
				->caption(Core::_('Shop.tab_export'))
				->name('Export');
			$oShopTabWatermark = Admin_Form_Entity::factory('Tab')
				->caption(Core::_('Shop.tab_watermark'))
				->name('Watermark');
			$oShopTabOrders = Admin_Form_Entity::factory('Tab')
				->caption(Core::_('Shop.tab_sort'))
				->name('Orders');

			$oMainTab = $this->getTab('main');
			$oAdditionalTab = $this->getTab('additional');

			$this
				->addTabAfter($oShopTabFormats, $oMainTab)
				->addTabAfter($oShopTabExport, $oShopTabFormats)
				->addTabAfter($oShopTabWatermark, $oShopTabExport)
				->addTabAfter($oShopTabOrders, $oShopTabWatermark)
			;

			// Перемещаем поля на их вкладки
			$oMainTab
			// Formats
			->move($this->getField('image_small_max_width'), $oShopTabFormats)
			->move($this->getField('image_small_max_height'), $oShopTabFormats)
			->move($this->getField('image_large_max_width'), $oShopTabFormats)
			->move($this->getField('image_large_max_height'), $oShopTabFormats)
			->move($this->getField('group_image_small_max_width'), $oShopTabFormats)
			->move($this->getField('group_image_small_max_height'), $oShopTabFormats)
			->move($this->getField('group_image_large_max_width'), $oShopTabFormats)
			->move($this->getField('group_image_large_max_height'), $oShopTabFormats)
			->move($this->getField('producer_image_small_max_width'), $oShopTabFormats)
			->move($this->getField('producer_image_small_max_height'), $oShopTabFormats)
			->move($this->getField('producer_image_large_max_width'), $oShopTabFormats)
			->move($this->getField('producer_image_large_max_height'), $oShopTabFormats)
			->move($this->getField('format_date'), $oShopTabFormats)
			->move($this->getField('format_datetime'), $oShopTabFormats)
			->move($this->getField('typograph_default_items'), $oShopTabFormats)
			->move($this->getField('typograph_default_groups'), $oShopTabFormats)
			// Export
			->move($this->getField('yandex_market_name'), $oShopTabExport)
			->move($this->getField('guid'), $oShopTabExport)
			->move($this->getField('yandex_market_sales_notes_default'), $oShopTabExport)
			// Watermark
			//->move($this->getField('watermark_file'), $oShopTabWatermark)
			->move($this->getField('preserve_aspect_ratio'), $oShopTabWatermark)
			->move($this->getField('preserve_aspect_ratio_small'), $oShopTabWatermark)
			->move($this->getField('preserve_aspect_ratio_group'), $oShopTabWatermark)
			->move($this->getField('preserve_aspect_ratio_group_small'), $oShopTabWatermark)
			->move($this->getField('watermark_default_use_large_image'), $oShopTabWatermark)
			->move($this->getField('watermark_default_use_small_image'), $oShopTabWatermark)
			->move($this->getField('watermark_default_position_x'), $oShopTabWatermark)
			->move($this->getField('watermark_default_position_y'), $oShopTabWatermark)
			// Orders
			->move($this->getField('items_sorting_field'), $oShopTabOrders)
			->move($this->getField('items_sorting_direction'), $oShopTabOrders)
			->move($this->getField('groups_sorting_field'), $oShopTabOrders)
			->move($this->getField('groups_sorting_direction'), $oShopTabOrders)
			;

			// Переопределяем стандартные поля на нужный нам вид

			// Удаляем группу магазинов
			$oAdditionalTab->delete
			(
				$this->getField('shop_dir_id')
			);

			// Удаляем структуру
			$oAdditionalTab->delete
			(
				$this->getField('structure_id')
			);

			// Удаляем страну
			$oAdditionalTab->delete
			(
				$this->getField('shop_country_id')
			);

			// Удаляем группу пользователей сайта
			$oAdditionalTab->delete
			(
				$this->getField('siteuser_group_id')
			);

			// Удаляем единицы измерения
			$oAdditionalTab->delete(
				$this->getField('shop_measure_id')
			);

			// Удаляем валюты
			$oAdditionalTab->delete(
				$this->getField('shop_currency_id')
			);

			// Удаляем статусы заказов
			$oAdditionalTab->delete
			(
				$this->getField('shop_order_status_id')
			);

			// Удаляем тип URL
			$oMainTab->delete
			(
				$this->getField('url_type')
			);

			// Удаляем компании
			$oAdditionalTab->delete
			(
				$this->getField('shop_company_id')
			);

			// Удаляем поле сортировки товара
			$oShopTabOrders->delete
			(
				$this->getField('items_sorting_field')
			);

			// Удаляем направление сортировки товара
			$oShopTabOrders->delete
			(
				$this->getField('items_sorting_direction')
			);

			// Удаляем поле сортировки групп товаров
			$oShopTabOrders->delete
			(
				$this->getField('groups_sorting_field')
			);

			// Удаляем направление сортировки групп товаров
			$oShopTabOrders->delete
			(
				$this->getField('groups_sorting_direction')
			);

			// Удаляем водяной знак
			/*$oShopTabWatermark->delete
			(
				$this->getField('watermark_file')
			);*/

			// Добавляем группу магазинов
			$oMainTab->addAfter
			(
				Admin_Form_Entity::factory('Select')
					->name('shop_dir_id')
					->caption(Core::_('Shop.shop_dir_id'))
					//->divAttr(array('style' => 'float: left'))
					->style("width: 320px")
					->options(
						array(' … ') + $this->_fillShopDir()
					)
					->value($this->_object->shop_dir_id),
					$this->getField('name')
			);

			// Получаем поле описания магазина
			$oFieldDescription = $this->getField('description');

			// Переопределяем тип поля описания на WYSIWYG
			$oFieldDescription
				->wysiwyg(TRUE)
				->template_id($this->_object->Structure->template_id
					? $this->_object->Structure->template_id
					: 0);

			$Structure_Controller_Edit = new Structure_Controller_Edit($this->_Admin_Form_Action);

			// Добавляем структуру
			$oStructureSelectField = Admin_Form_Entity::factory('Select')
				->name('structure_id')
				->caption(Core::_('Shop.structure_id'))
				->options(
					array(' … ') + $Structure_Controller_Edit->fillStructureList($this->_object->site_id)
				)
				->value($this->_object->structure_id);

			$oMainTab->addAfter($oStructureSelectField, $oFieldDescription);

			if (Core::moduleIsActive('siteuser'))
			{
				$oSiteuser_Controller_Edit = new Siteuser_Controller_Edit($this->_Admin_Form_Action);
				$aSiteuser_Groups = $oSiteuser_Controller_Edit->fillSiteuserGroups($this->_object->site_id);
			}
			else
			{
				$aSiteuser_Groups = array();
			}

			// Добавляем группы пользователей сайта
			$oShopUserGroupSelect = Admin_Form_Entity::factory('Select')
				->name('siteuser_group_id')
				->caption(Core::_('Shop.siteuser_group_id'))
				->style("width: 190px")
				->divAttr(array('style' => 'float: left'))
				->options(array(Core::_('Shop.allgroupsaccess')) + $aSiteuser_Groups)
				->value($this->_object->siteuser_group_id);

			$oMainTab->addAfter($oShopUserGroupSelect, $oStructureSelectField);

			// Добавляем компании
			$oCompaniesField = Admin_Form_Entity::factory('Select')
				->name('shop_company_id')
				->caption(Core::_('Shop.shop_company_id'))
				->style("width: 190px")
				->divAttr(array('style' => 'float: left'))
				->options(
					$this->_fillCompanies()
				)
				->value($this->_object->shop_company_id);

			$oMainTab->addAfter
			(
				$oCompaniesField,
				$oShopUserGroupSelect
			);

			// Добавляем валюты
			$oCurrencyField = Admin_Form_Entity::factory('Select')
				->name('shop_currency_id')
				->caption(Core::_('Shop.shop_currency_id'))
				->style("width: 190px")
				//->divAttr(array('style' => 'float: left'))
				->options(
					$this->fillCurrencies()
				)
				->value($this->_object->shop_currency_id);

			$oMainTab->addAfter
			(
				$oCurrencyField,
				$oCompaniesField
			);

			// Добавляем страны
			$oCountriesField = Admin_Form_Entity::factory('Select')
				->name('shop_country_id')
				->caption(Core::_('Shop.shop_country_id'))
				->style("width: 190px")
				->divAttr(array('style' => 'float: left'))
				->options(
					$this->fillCountries()
				)
				->value($this->_object->shop_country_id);

			$oMainTab->addAfter
			(
				$oCountriesField,
				$oCurrencyField
			);

			// Добавляем статусы заказов
			$oOrderStatusField = Admin_Form_Entity::factory('Select')
				->name('shop_order_status_id')
				->caption(Core::_('Shop.shop_order_status_id'))
				->style("width: 190px")
				->divAttr(array('style' => 'float: left'))
				->options(
					$this->fillOrderStatuses()
				)
				->value($this->_object->shop_order_status_id);

			$oMainTab->addAfter
			(
				$oOrderStatusField,
				$oCountriesField
			);

			// Добавляем единицы измерения
			$oMeasuresField = Admin_Form_Entity::factory('Select')
				->name('shop_measure_id')
				->caption(Core::_('Shop.shop_measure_id'))
				->style("width: 190px")
				//->divAttr(array('style' => 'float: left'))
				->options(
					$this->fillMeasures()
				)
				->value($this->_object->shop_measure_id);

			$oMainTab->addAfter
			(
				$oMeasuresField,
				$oOrderStatusField
			);

			$this->getField('email')
				->style("width: 190px")
				->divAttr(array('style' => 'float: left'))
				// clear standart url pattern
				->format(array('lib' => array()));

			$oItemsOnPageField = $this->getField('items_on_page')
				->style("width: 190px")
				->divAttr(array('style' => 'float: left'));

			// Добавляем тип URL
			$oUrlTypeField = Admin_Form_Entity::factory('Select')
				->name('url_type')
				->caption(Core::_('Shop.url_type'))
				->style("width: 190px")
				//->divAttr(array('style' => 'float: left'))
				->options(
					array(
						Core::_('Shop.shop_shops_url_type_element_0'),
						Core::_('Shop.shop_shops_url_type_element_1'))
				)
				->value($this->_object->url_type);

			$oMainTab->addAfter
			(
				$oUrlTypeField,
				$oItemsOnPageField
			);

			$oMainTab->addAfter($oSeparatorField, $oUrlTypeField);

			$this->getField('image_small_max_width')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));

			$oShopTabFormats->addAfter($oSeparatorField,
				$this->getField('image_small_max_height')
					->style("width: 300px")
					->divAttr(array('style' => 'float: left')));

			$this->getField('image_large_max_width')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));

			$oShopTabFormats->addAfter($oSeparatorField,
				$this->getField('image_large_max_height')
					->style("width: 300px")
					->divAttr(array('style' => 'float: left')));

			$this->getField('group_image_small_max_width')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));

			$oShopTabFormats->addAfter($oSeparatorField,
				$this->getField('group_image_small_max_height')
					->style("width: 300px")
					->divAttr(array('style' => 'float: left')));

			$this->getField('group_image_large_max_width')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));

			$oShopTabFormats->addAfter($oSeparatorField,
				$this->getField('group_image_large_max_height')
					->style("width: 300px")
					->divAttr(array('style' => 'float: left')));

			$this->getField('format_date')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));

			$this->getField('producer_image_small_max_width')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));
			$this->getField('producer_image_small_max_height')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));

			$oShopTabFormats->addAfter($oSeparatorField, $this->getField('producer_image_small_max_height'));

			$this->getField('producer_image_large_max_width')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));
			$this->getField('producer_image_large_max_height')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));

			$oShopTabFormats->addAfter($oSeparatorField, $this->getField('producer_image_large_max_height'));

			$oShopTabFormats->addAfter($oSeparatorField,
				$this->getField('format_datetime')
					->style("width: 300px")
					->divAttr(array('style' => 'float: left')));

			$this->getField('watermark_default_position_x')
				->style("width: 300px; margin-right: 30px")
				->divAttr(array('style' => 'float: left'));

			$oShopTabWatermark->addAfter($oSeparatorField,
				$this->getField('watermark_default_position_y')
					->style("width: 300px")
					->divAttr(array('style' => 'float: left')));


			$oWatermarkFileField = Admin_Form_Entity::factory('File');

			$watermarkPath =
				is_file($this->_object->getWatermarkFilePath())
				? $this->_object->getWatermarkFileHref()
				: '';

			$sFormPath = $this->_Admin_Form_Controller->getPath();

			$windowId = $this->_Admin_Form_Controller->getWindowId();

			$oWatermarkFileField
				->type("file")
				->caption(Core::_('Shop.watermark_file'))
				->style("width: 400px;")
				->name("watermark_file")
				->id("watermark_file")
				->largeImage
				(
					array
					(
						'path' => $watermarkPath,
						'show_params' => FALSE,
						'delete_onclick' => "$.adminLoad({path: '{$sFormPath}', additionalParams: 'hostcms[checked][{$this->_datasetId}][{$this->_object->id}]=1', action: 'deleteWatermarkFile', windowId: '{$windowId}'}); return false",
					)
				)
				->smallImage
				(
					array
					(
						'show' => FALSE
					)
				);

			$oShopTabWatermark->addBefore
			(
				$oWatermarkFileField,
				$this->getField('preserve_aspect_ratio')
			);

			$oShopTabWatermark->addAfter
			(
				$oSeparatorField,
				$oWatermarkFileField
			);

			// Добавляем поле сортировки товара
			$oItemsSortingField = Admin_Form_Entity::factory('Select')
				->name('items_sorting_field')
				->caption(Core::_('Shop.items_sorting_field'))
				->style("width: 300px")
				->divAttr(array('style' => 'float: left'))
				->options(
					array
					(
						Core::_('Shop.sort_by_date'),
						Core::_('Shop.sort_by_name'),
						Core::_('Shop.sort_by_order')
					)
				)
				->value($this->_object->items_sorting_field);

			$oShopTabOrders->add
			(
				$oItemsSortingField
			);


			// Добавляем направление сортировки товара
			$oItemsSortingDirection = Admin_Form_Entity::factory('Select')
				->name('items_sorting_direction')
				->caption(Core::_('Shop.items_sorting_direction'))
				->style("width: 300px")
				->divAttr(array('style' => 'float: left'))
				->options(
					array
					(
						Core::_('Shop.sort_to_increase'),
						Core::_('Shop.sort_to_decrease')
					)
				)
				->value($this->_object->items_sorting_direction);

			$oShopTabOrders->add
			(
				$oItemsSortingDirection
			);

			$oShopTabOrders->addAfter
			(
				$oSeparatorField,
				$oItemsSortingDirection
			);

			// Добавляем поле сортировки групп
			$oGroupsSortingField = Admin_Form_Entity::factory('Select')
				->name('groups_sorting_field')
				->caption(Core::_('Shop.groups_sorting_field'))
				->style("width: 300px")
				->divAttr(array('style' => 'float: left'))
				->options(
					array
					(
						Core::_('Shop.sort_by_name'),
						Core::_('Shop.sort_by_order'),
					)
				)
				->value($this->_object->groups_sorting_field);

			$oShopTabOrders->add
			(
				$oGroupsSortingField
			);

			// Добавляем направление сортировки групп
			$oGroupsSortingDirection = Admin_Form_Entity::factory('Select')
				->name('groups_sorting_direction')
				->caption(Core::_('Shop.groups_sorting_direction'))
				->style("width: 300px")
				->divAttr(array('style' => 'float: left'))
				->options(
					array
					(
						Core::_('Shop.sort_to_increase'),
						Core::_('Shop.sort_to_decrease')
					)
				)
				->value($this->_object->groups_sorting_direction);

			$oShopTabOrders->add
			(
				$oGroupsSortingDirection
			);

			$oMainTab->delete($this->getField('size_measure'));

			$oMainTab->addAfter(Admin_Form_Entity::factory('Select')
				->name('size_measure')
				->caption(Core::_('Shop.size_measure'))
				->style("width: 190px")
				->divAttr(array('style' => 'float: left'))
				->options(array(Core::_('Shop.size_measure_0'),
					Core::_('Shop.size_measure_1'),
					Core::_('Shop.size_measure_2'),
					Core::_('Shop.size_measure_3'),
					Core::_('Shop.size_measure_4')))
				->value($this->_object->size_measure), $oUrlTypeField);

			break;
		}

		$this->title($title);

		return $this;
	}

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 * @return self
	 */
	public function execute($operation = NULL)
	{
		if (!is_null($operation))
		{
			$modelName = $this->_object->getModelName();

			if ($modelName == 'shop')
			{
				$oShop = Core_Entity::factory('Shop');

				$iStructureId = intval(Core_Array::get($this->_formValues, 'structure_id'));

				$oShop->queryBuilder()
					->where('shops.structure_id', '=', $iStructureId);

				$aShop = $oShop->findAll();

				$iCount = count($aShop);

				if ($iStructureId && $iCount && (is_null($this->_object->id) || $iCount > 1 || $aShop[0]->id != $this->_object->id))
				{
					$oStructure = Core_Entity::factory('Structure', $iStructureId);

					$this->addMessage(
						Core_Message::get(
							Core::_('Shop.structureIsExist', $oStructure->name),
							'error'
						)
					);

					return TRUE;
				}
			}
		}

		return parent::execute($operation);
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Shop_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		parent::_applyObjectProperty();

		if(
			// Поле файла существует
			!is_null($aFileData = Core_Array::getFiles('watermark_file', NULL))
			// и передан файл
			&& intval($aFileData['size']) > 0)
		{
			if (Core_File::isValidExtension($aFileData['name'], array('png')))
			{
				$this->_object->saveWatermarkFile($aFileData['tmp_name']);
			}
			else
			{
				$this->addMessage(
					Core_Message::get(
						Core::_('Core.extension_does_not_allow', Core_File::getExtension($aFileData['name'])),
						'error'
					)
				);
			}
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}

	/**
	 * Get currency array
	 * @return array
	 */
	public function fillCurrencies()
	{
		$oCurrency = Core_Entity::factory('Shop_Currency');

		$oCurrency->queryBuilder()
			->orderBy('sorting')
			->orderBy('name');

		$aCurrencies = $oCurrency->findAll();

		$aCurrencyArray = array(' … ');

		foreach($aCurrencies as $oCurrency)
		{
			$aCurrencyArray[$oCurrency->id] = $oCurrency->name;
		}

		return $aCurrencyArray;
	}

	/**
	 * Get order statuses array
	 * @return array
	 */
	public function fillOrderStatuses()
	{
		$oOrderStatus = Core_Entity::factory('Shop_Order_Status');

		$oOrderStatus->queryBuilder()
			->orderBy('name');

		$aOrderStatuses = $oOrderStatus->findAll();

		$aOrderStatusArray = array(' … ');

		foreach($aOrderStatuses as $oOrderStatus)
		{
			$aOrderStatusArray[$oOrderStatus->id] = $oOrderStatus->name;
		}

		return $aOrderStatusArray;
	}

	/**
	 * Get measures array
	 * @return array
	 */
	public function fillMeasures()
	{
		$oMeasure = Core_Entity::factory('Shop_Measure');

		$oMeasure->queryBuilder()
			->orderBy('name');

		$aMeasures = $oMeasure->findAll();

		$aMeasureArray = array(' … ');

		foreach($aMeasures as $oMeasure)
		{
			$aMeasureArray[$oMeasure->id] = $oMeasure->name;
		}

		return $aMeasureArray;
	}

	/**
	 * Get countries array
	 * @return array
	 */
	public function fillCountries()
	{
		$oCountry = Core_Entity::factory('Shop_Country');

		$oCountry->queryBuilder()
			->orderBy('sorting')
			->orderBy('name');

		$aCountries = $oCountry->findAll();

		$aCountryArray = array(' … ');

		foreach($aCountries as $oCountry)
		{
			$aCountryArray[$oCountry->id] = $oCountry->name;
		}

		return $aCountryArray;
	}

	/**
	 * Get country locations
	 * @param int $iCountryId country ID
	 * @return array
	 */
	public function fillCountryLocations($iCountryId)
	{
		$iCountryId = intval($iCountryId);

		$oCountryLocation = Core_Entity::factory('Shop_Country_Location');

		$oCountryLocation->queryBuilder()
			->where('shop_country_id', '=', $iCountryId)
			->orderBy('sorting')
			->orderBy('name');

		$oCountryLocations = $oCountryLocation->findAll();

		$aCountryLocationArray = array(' … ');

		foreach($oCountryLocations as $oCountryLocation)
		{
			$aCountryLocationArray[$oCountryLocation->id] = $oCountryLocation->name;
		}

		return $aCountryLocationArray;
	}

	/**
	 * Get location cities
	 * @param int $iCountryLocationId location ID
	 * @return array
	 */
	public function fillCountryLocationCities($iCountryLocationId)
	{
		$iCountryLocationId = intval($iCountryLocationId);

		$oCountryLocationCity = Core_Entity::factory('Shop_Country_Location_City');

		$oCountryLocationCity->queryBuilder()
			->where('shop_country_location_id', '=', $iCountryLocationId)
			->orderBy('sorting')
			->orderBy('name');

		$oCountryLocationCities = $oCountryLocationCity->findAll();

		$aCountryLocationCityArray = array(' … ');

		foreach($oCountryLocationCities as $oCountryLocationCity)
		{
			$aCountryLocationCityArray[$oCountryLocationCity->id] = $oCountryLocationCity->name;
		}

		return $aCountryLocationCityArray;
	}

	/**
	 * Get city areas
	 * @param int $iCountryLocationCityId city ID
	 * @return array
	 */
	public function fillCountryLocationCityAreas($iCountryLocationCityId)
	{
		$iCountryLocationCityId = intval($iCountryLocationCityId);

		$oCountryLocationCityArea = Core_Entity::factory('Shop_Country_Location_City_Area');

		$oCountryLocationCityArea->queryBuilder()
			->where('shop_country_location_city_id', '=', $iCountryLocationCityId)
			->orderBy('sorting')
			->orderBy('name');

		$oCountryLocationCityAreas = $oCountryLocationCityArea->findAll();

		$aCountryLocationCityAreaArray = array(' … ');

		foreach($oCountryLocationCityAreas as $oCountryLocationCityArea)
		{
			$aCountryLocationCityAreaArray[$oCountryLocationCityArea->id] = $oCountryLocationCityArea->name;
		}

		return $aCountryLocationCityAreaArray;
	}

	/**
	 * Get companies array
	 * @return array
	 */
	protected function _fillCompanies()
	{
		$oCompany = Core_Entity::factory('Shop_Company');

		$oCompany->queryBuilder()
			->orderBy('name');

		$aCompanies = $oCompany->findAll();

		$aCompanyArray = array(' … ');
		foreach($aCompanies as $oCompany)
		{
			$aCompanyArray[$oCompany->id] = $oCompany->name;
		}

		return $aCompanyArray;
	}

	/**
	 * Create visual tree of the directories
	 * @param int $iShopDirParentId parent directory ID
	 * @param boolean $bExclude exclude group ID
	 * @param int $iLevel current nesting level
	 * @return array
	 */
	protected function _fillShopDir($iShopDirParentId = 0, $bExclude = FALSE, $iLevel = 0)
	{
		$iShopDirParentId = intval($iShopDirParentId);

		$iLevel = intval($iLevel);

		$oShopDir = Core_Entity::factory('Shop_Dir', $iShopDirParentId);

		$aResult = array();

		$aChildrenDirs = $oShopDir->Shop_Dirs;
		$aChildrenDirs->queryBuilder()
			->where('site_id', '=', CURRENT_SITE);

		$aChildrenDirs = $aChildrenDirs->findAll();

		foreach ($aChildrenDirs as $oChildrenDir)
		{
			if ($bExclude != $oChildrenDir->id)
			{
				$aResult[$oChildrenDir->id] = str_repeat('  ', $iLevel) . $oChildrenDir->name;

				$aResult += $this->_fillShopDir($oChildrenDir->id, $bExclude, $iLevel+1);
			}
		}

		return $aResult;
	}

	/**
	 * Fill list of shops for site
	 * @param int $iSiteId site ID
	 * @return array
	 */
	public function fillShops($iSiteId)
	{
		$iSiteId = intval($iSiteId);

		$aReturn = array();

		$aObjects = Core_Entity::factory('Site', $iSiteId)->Shops->findAll();
		foreach ($aObjects as $oObject)
		{
			$aReturn[$oObject->id] = $oObject->name;
		}

		return $aReturn;
	}
}