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
class Property_Controller_Tab extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'linkedObject',
		'template_id',
	);

	/**
	 * POST data
	 * @var array
	 */
	protected $_POST = array();

	/**
	 * Form controller
	 * @var Admin_Form_Controller
	 */
	protected $_Admin_Form_Controller = NULL;

	/**
	 * Constructor.
	 * @param Admin_Form_Controller $Admin_Form_Controller controller
	 */
	public function __construct(Admin_Form_Controller $Admin_Form_Controller)
	{
		$this->_Admin_Form_Controller = $Admin_Form_Controller;

		// We use each for advance the array cursor
		$this->_POST = $_POST;

		parent::__construct();

		$this->template_id = 0;
	}

	/**
	 * Object
	 * @var object
	 */
	protected $_object = NULL;

	/**
	 * Set object
	 * @param Core_Entity $object object
	 * @return self
	 */
	public function setObject(Core_Entity $object)
	{
		$this->_object = $object;
		return $this;
	}

	/**
	 * Dataset ID
	 * @var int
	 */
	protected $_datasetId = NULL;

	/**
	 * Set ID of dataset
	 * @param int $datasetId ID of dataset
	 * @return self
	 */
	public function setDatasetId($datasetId)
	{
		$this->_datasetId = $datasetId;
		return $this;
	}

	/**
	 * Tab
	 * @var Skin_Default_Admin_Form_Entity_Tab
	 */
	protected $_tab = NULL;

	/**
	 * Set tab
	 * @param Skin_Default_Admin_Form_Entity_Tab $tab tab
	 * @return self
	 */
	public function setTab(Skin_Default_Admin_Form_Entity_Tab $tab)
	{
		$this->_tab = $tab;
		return $this;
	}

	/**
	 * Show properties on tab
	 * @return self
	 */
	public function fillTab()
	{
		$this->_setPropertyDirs(0, $this->_tab);
		return $this;
	}

	/**
	 * Show plus button
	 * @param Property_Model $oProperty property
	 * @param string $function function name
	 * @return string
	 */
	protected function _getImgAdd($oProperty, $function = '$.cloneProperty')
	{
		$windowId = $this->_Admin_Form_Controller->getWindowId();

		ob_start();
		Core::factory('Core_Html_Entity_Img')
			->src('/admin/images/action_add.gif')
			->id('add')
			->class('pointer left5px img_line')
			->onclick("{$function}('{$windowId}', '{$oProperty->id}')")
			->execute();
		$oAdmin_Form_Entity_Code = Admin_Form_Entity::factory('Code')->html(ob_get_clean());

		return $oAdmin_Form_Entity_Code;
	}

	/**
	 * Show minus button
	 * @param string $onclick onclick attribute value
	 * @return string
	 */
	protected function _getImgDelete($onclick = '$.deleteNewProperty(this)')
	{
		ob_start();
		Core::factory('Core_Html_Entity_Img')
			->src('/admin/images/action_delete.gif')
			->id('delete')
			->class('pointer left5px img_line')
			->onclick($onclick)
			->execute();

		$oAdmin_Form_Entity_Code = Admin_Form_Entity::factory('Code')
			->html(ob_get_clean());

		return $oAdmin_Form_Entity_Code;
	}

	/**
	  * Get path to delete image
	  * @return string
	  */
	protected function _getImgDeletePath()
	{
		return "res = confirm('" . Core::_('Admin_Form.msg_information_delete') . "'); if (res) { $.deleteProperty(this, {path: '{$this->_Admin_Form_Controller->getPath()}', action: 'deletePropertyValue', datasetId: '{$this->_datasetId}', objectId: '{$this->_object->id}'}) } else {return false}";
	}

	/**
	 * Add external properties container to $parentObject
	 * @param int $parent_id ID of parent directory of properties
	 * @param object $parentObject
	 * @hostcms-event Property_Controller_Tab.onBeforeAddFormEntity
	 */
	protected function _setPropertyDirs($parent_id = 0, $parentObject)
	{
		// Properties
		$oProperties = $this->_getProperties();

		$oProperties
			->queryBuilder()
			->where('property_dir_id', '=', $parent_id);

		$aProperties = $oProperties->findAll();

		$oAdmin_Form_Entity_Section = Admin_Form_Entity::factory('Section')
			->caption($parent_id == 0
				? Core::_('Property_Dir.main_section')
				: Core_Entity::factory('Property_Dir', $parent_id)->name
			);

		foreach ($aProperties as $oProperty)
		{
			$aProperty_Values = $this->_object->id
				? $oProperty->getValues($this->_object->id, FALSE)
				: array();

			$oAdmin_Form_Entity = NULL;

			switch ($oProperty->type)
			{
				case 0: // Int
				case 1: // String
				case 2: // File
				case 3: // List
				case 4: // Textarea
				case 6: // Wysiwyg
				case 7: // Checkbox
				case 8: // Date
				case 9: // Datetime
				case 10: // Hidden field
				case 11: // Float

					$width = 410;

					switch ($oProperty->type)
					{
						case 0: // Int
						case 1: // String
						case 10: // Hidden field
						case 11: // Float
						default:
							$oAdmin_Form_Entity = Admin_Form_Entity::factory('Input')
								->style('width: 340px');
						break;

						case 2: // File

							$largeImage = array(
								'max_width' => $oProperty->image_large_max_width,
								'max_height' => $oProperty->image_large_max_height,
								'show_description' => TRUE,
							);

							$smallImage = array(
								'caption' => Core::_('Property.small_file_caption', $oProperty->name),
								'show' => !$oProperty->hide_small_image,
								'max_width' => $oProperty->image_small_max_width,
								'max_height' => $oProperty->image_small_max_height,
								'show_description' => TRUE
							);

							if (method_exists($this->linkedObject, 'getWatermarkDefaultPositionX')
								&& method_exists($this->linkedObject, 'getWatermarkDefaultPositionY'))
							{
								$largeImage['watermark_position_x'] = $this->linkedObject->getWatermarkDefaultPositionX();
								$largeImage['watermark_position_y'] = $this->linkedObject->getWatermarkDefaultPositionY();
							}

							if (method_exists($this->linkedObject, 'layWatermarOnLargeImage')
								&& method_exists($this->linkedObject, 'layWatermarOnSmallImage'))
							{
								$largeImage['place_watermark_checkbox_checked'] = $this->linkedObject->layWatermarOnLargeImage();
								$smallImage['place_watermark_checkbox_checked'] = $this->linkedObject->layWatermarOnSmallImage();
							}

							if (method_exists($this->linkedObject, 'preserveAspectRatioOfLargeImage')
								&& method_exists($this->linkedObject, 'preserveAspectRatioOfSmallImage'))
							{
								$largeImage['preserve_aspect_ratio_checkbox_checked'] = $this->linkedObject->preserveAspectRatioOfLargeImage();
								$smallImage['preserve_aspect_ratio_checkbox_checked'] = $this->linkedObject->preserveAspectRatioOfSmallImage();
							}

							$oAdmin_Form_Entity = Admin_Form_Entity::factory('File')
								->style('width: 340px')
								->largeImage($largeImage)
								->smallImage($smallImage);

							$width = 710;
						break;

						case 3: // List
							if (Core::moduleIsActive('list'))
							{
								$aListItems = $oProperty->List->List_Items->getAllByActive(1, FALSE);

								$aOptions = array(' … ');
								foreach ($aListItems as $oListItem)
								{
									$aOptions[$oListItem->id] = $oListItem->value;
								}

								$oAdmin_Form_Entity = Admin_Form_Entity::factory('Select')
									->options($aOptions)
									->style('width: 340px');

								unset($aOptions);
							}
						break;

						case 4: // Textarea
							$oAdmin_Form_Entity = Admin_Form_Entity::factory('Textarea')
								->style('width: 340px');
						break;

						case 6: // Wysiwyg
							$oAdmin_Form_Entity = Admin_Form_Entity::factory('Textarea')
								->wysiwyg(TRUE)
								->template_id($this->template_id);
						break;

						case 7: // Checkbox
							$oAdmin_Form_Entity = Admin_Form_Entity::factory('Checkbox');

							count($aProperty_Values) && $oAdmin_Form_Entity->postingUnchecked(TRUE);
						break;

						case 8: // Date
							$oAdmin_Form_Entity = Admin_Form_Entity::factory('Date');
						break;

						case 9: // Datetime
							$oAdmin_Form_Entity = Core::factory('Admin_Form_Entity_Datetime');
						break;
					}

					if ($oAdmin_Form_Entity)
					{
						$oAdmin_Form_Entity->name("property_{$oProperty->id}[]")
							->caption($oProperty->name)
							->value(
								$this->_correctPrintValue($oProperty, $oProperty->default_value)
							)
							->divAttr(array(
								'style' => "width: {$width}px",
								'id' => "property_{$oProperty->id}"
							));

						$oProperty->multiple && $oAdmin_Form_Entity->add($this->_getImgAdd($oProperty));

						// Значений св-в нет для объекта
						if (count($aProperty_Values) == 0)
						{
							$oProperty->multiple && $oAdmin_Form_Entity->add($this->_getImgDelete());
							$oAdmin_Form_Entity_Section->add($oAdmin_Form_Entity);

							Core_Event::notify(get_class($this) . '.onBeforeAddFormEntity', $this, array($oAdmin_Form_Entity, $oAdmin_Form_Entity_Section, $oProperty));
						}
						else
						{
							foreach ($aProperty_Values as $oProperty_Value)
							{
								$oNewAdmin_Form_Entity = clone $oAdmin_Form_Entity;

								switch ($oProperty->type)
								{
									default:
										$oNewAdmin_Form_Entity->value($oProperty_Value->value);
									break;

									case 2: // File
										$sDirHref = $this->linkedObject->getDirHref($this->_object);

										if ($oProperty_Value->file != '')
										{
											$oNewAdmin_Form_Entity->largeImage(
												Core_Array::union($oNewAdmin_Form_Entity->largeImage, array(
													'path' => $sDirHref . rawurlencode($oProperty_Value->file),
													'delete_onclick' => $this->_Admin_Form_Controller->getAdminActionLoadAjax($this->_Admin_Form_Controller->getPath(), 'deletePropertyValue', "large_property_{$oProperty->id}_{$oProperty_Value->id}", $this->_datasetId, $this->_object->id)
												))
											);
										}
										// Description doesn't depend on loaded file
										$oNewAdmin_Form_Entity->largeImage(
											Core_Array::union($oNewAdmin_Form_Entity->largeImage, array(
												'description' => $oProperty_Value->file_description
											)
										));

										if ($oProperty_Value->file_small != '')
										{
											$oNewAdmin_Form_Entity->smallImage(
												Core_Array::union($oNewAdmin_Form_Entity->smallImage, array(
													'path' => $sDirHref . rawurlencode($oProperty_Value->file_small),
													'delete_onclick' => $this->_Admin_Form_Controller->getAdminActionLoadAjax($this->_Admin_Form_Controller->getPath(), 'deletePropertyValue', "small_property_{$oProperty->id}_{$oProperty_Value->id}", $this->_datasetId, $this->_object->id),
													'create_small_image_from_large_checked' => FALSE,
												))
											);
										}

										// Description doesn't depend on loaded file
										$oNewAdmin_Form_Entity->smallImage(
											Core_Array::union($oNewAdmin_Form_Entity->smallImage, array(
												'description' => $oProperty_Value->file_small_description
											)
										));
									break;

									case 8: // Date
										$oNewAdmin_Form_Entity->value(
											//Core_Date::sql2date($oProperty_Value->value)
											$this->_correctPrintValue($oProperty, $oProperty_Value->value)
										);
									break;

									case 9: // Datetime
										$oNewAdmin_Form_Entity->value(
											//Core_Date::sql2datetime($oProperty_Value->value)
											$this->_correctPrintValue($oProperty, $oProperty_Value->value)
										);
									break;
								}

								$oProperty->multiple && $oNewAdmin_Form_Entity->add($this->_getImgDelete($this->_getImgDeletePath()));

								$oNewAdmin_Form_Entity
									->name("property_{$oProperty->id}_{$oProperty_Value->id}")
									->id("property_{$oProperty->id}_{$oProperty_Value->id}");

								Core_Event::notify(get_class($this) . '.onBeforeAddFormEntity', $this, array($oNewAdmin_Form_Entity, $oAdmin_Form_Entity_Section, $oProperty));

								$oAdmin_Form_Entity_Section->add($oNewAdmin_Form_Entity);
							}
						}
					}
				break;

				case 5: // ИС

					// Директории
					$oAdmin_Form_Entity_InfGroups = Admin_Form_Entity::factory('Select')
						->caption($oProperty->name)
						->style('width: 340px')
						->divAttr(array(
							'style' => 'width: 410px',
							'id' => "property_{$oProperty->id}"
						));

					// Элементы
					$oAdmin_Form_Entity_InfItems = Admin_Form_Entity::factory('Select')
						->style('width: 340px')
						->name("property_{$oProperty->id}[]")
						->value($oProperty->default_value)
						->divAttr(array('class' => ''));

					$oProperty->multiple && $oAdmin_Form_Entity_InfItems->add($this->_getImgAdd($oProperty, '$.clonePropertyInfSys'));

					// Значений св-в нет для объекта
					if (count($aProperty_Values) == 0)
					{
						$oProperty->multiple && $oAdmin_Form_Entity_InfItems->add($this->_getImgDelete());

						$this->_fillInformationSystem($oProperty->default_value, $oProperty, $oAdmin_Form_Entity_InfGroups, $oAdmin_Form_Entity_InfItems);
						$oAdmin_Form_Entity_Section->add($oAdmin_Form_Entity_InfGroups);
					}
					else
					{
						foreach ($aProperty_Values as $key => $oProperty_Value)
						{
							$value = $oProperty_Value->value;

							$oNewAdmin_Form_Entity_InfGroups = clone $oAdmin_Form_Entity_InfGroups;

							$oNewAdmin_Form_Entity_InfItems = clone $oAdmin_Form_Entity_InfItems;
							$oNewAdmin_Form_Entity_InfItems
								->id("property_{$oProperty->id}_{$oProperty_Value->id}_{$key}")
								->name("property_{$oProperty->id}_{$oProperty_Value->id}")
								->value($value);
							$oProperty->multiple && $oNewAdmin_Form_Entity_InfItems->add($this->_getImgDelete($this->_getImgDeletePath()));

							$this->_fillInformationSystem($value, $oProperty, $oNewAdmin_Form_Entity_InfGroups, $oNewAdmin_Form_Entity_InfItems);

							$oAdmin_Form_Entity_Section->add($oNewAdmin_Form_Entity_InfGroups);
						}
					}

				break;

				case 12: // Интернет-магазин

					// Директории
					$oAdmin_Form_Entity_Shop_Groups = Admin_Form_Entity::factory('Select')
						->caption($oProperty->name)
						->style('width: 340px')
						->divAttr(array(
							'style' => 'width: 410px',
							'id' => "property_{$oProperty->id}"
						));

					// Элементы
					$oAdmin_Form_Entity_Shop_Items = Admin_Form_Entity::factory('Select')
						->style('width: 340px')
						->name("property_{$oProperty->id}[]")
						->value($oProperty->default_value)
						->divAttr(array('class' => ''));

					$oProperty->multiple && $oAdmin_Form_Entity_Shop_Items->add($this->_getImgAdd($oProperty, '$.clonePropertyInfSys'));

					// Значений св-в нет для объекта
					if (count($aProperty_Values) == 0)
					{
						$oProperty->multiple && $oAdmin_Form_Entity_Shop_Items->add($this->_getImgDelete());

						$this->_fillShop($oProperty->default_value, $oProperty, $oAdmin_Form_Entity_Shop_Groups, $oAdmin_Form_Entity_Shop_Items);
						$oAdmin_Form_Entity_Section->add($oAdmin_Form_Entity_Shop_Groups);
					}
					else
					{
						foreach ($aProperty_Values as $key => $oProperty_Value)
						{
							$value = $oProperty_Value->value;

							$oNewAdmin_Form_Entity_Shop_Groups = clone $oAdmin_Form_Entity_Shop_Groups;

							$oNewAdmin_Form_Entity_InfItems = clone $oAdmin_Form_Entity_Shop_Items;
							$oNewAdmin_Form_Entity_InfItems
								->id("property_{$oProperty->id}_{$oProperty_Value->id}_{$key}")
								->name("property_{$oProperty->id}_{$oProperty_Value->id}")
								->value($value);
							$oProperty->multiple && $oNewAdmin_Form_Entity_InfItems->add($this->_getImgDelete($this->_getImgDeletePath()));

							$this->_fillShop($value, $oProperty, $oNewAdmin_Form_Entity_Shop_Groups, $oNewAdmin_Form_Entity_InfItems);

							$oAdmin_Form_Entity_Section->add($oNewAdmin_Form_Entity_Shop_Groups);
						}
					}

				break;

				default:
					throw new Core_Exception(
						Core::_('Property.type_does_not_exist'),
							array('%d' => $oProperty->type)
					);
			}
		}

		// Property Dirs
		$oProperty_Dirs = $this->linkedObject->Property_Dirs;

		$oProperty_Dirs
			->queryBuilder()
			->where('parent_id', '=', $parent_id);

		$aProperty_Dirs = $oProperty_Dirs->findAll();
		foreach ($aProperty_Dirs as $oProperty_Dir)
		{
			$this->_setPropertyDirs($oProperty_Dir->id, $parent_id == 0 ? $this->_tab : $oAdmin_Form_Entity_Section);
		}

		$oAdmin_Form_Entity_Section->getCountChildren() && $parentObject->add($oAdmin_Form_Entity_Section);
	}

	/**
	 * Fill information systems/items list
	 * @param int $value informationsystem_item_id
	 * @param Property_Model $oProperty property
	 * @param Admin_Form_Entity_Select $oAdmin_Form_Entity_InfGroups
	 * @param Admin_Form_Entity_Select $oAdmin_Form_Entity_InfItems
	 */
	protected function _fillInformationSystem($value, $oProperty, $oAdmin_Form_Entity_InfGroups, $oAdmin_Form_Entity_InfItems)
	{
		$Informationsystem_Item = Core_Entity::factory('Informationsystem_Item', $value);

		$gropup_id = $value == 0
			? 0
			: intval($Informationsystem_Item->informationsystem_group_id);

		$windowId = $this->_Admin_Form_Controller->getWindowId();

		$oInformationsystem = $oProperty->Informationsystem;
		$oInformationsystem_Items = $oInformationsystem->Informationsystem_Items;

		switch ($oInformationsystem->items_sorting_direction)
		{
			case 1:
				$items_sorting_direction = 'DESC';
			break;
			case 0:
			default:
				$items_sorting_direction = 'ASC';
		}

		$oInformationsystem_Items
			->queryBuilder()
			->clearOrderBy();

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

		// Items
		$aInformationsystem_Items = $oInformationsystem_Items->getAllByinformationsystem_group_id($gropup_id);

		$aOptions = array(' … ');
		foreach ($aInformationsystem_Items as $oInformationsystem_Item)
		{
			$aOptions[$oInformationsystem_Item->id] = !$oInformationsystem_Item->shortcut_id
				? $oInformationsystem_Item->name
				: $oInformationsystem_Item->Informationsystem_Item->name;
		}
		$oAdmin_Form_Entity_InfItems->options($aOptions);

		// Groups
		$aOptions = Informationsystem_Item_Controller_Edit::fillInformationsystemGroup($oProperty->informationsystem_id, 0);
		$oAdmin_Form_Entity_InfGroups
			->value($Informationsystem_Item->informationsystem_group_id)
			->options(array(' … ') + $aOptions)
			->onchange("$.ajaxRequest({path: '/admin/informationsystem/item/index.php', context: '{$oAdmin_Form_Entity_InfItems->id}', callBack: $.loadSelectOptionsCallback, action: 'loadInformationItemList',additionalParams: 'informationsystem_group_id=' + this.value + '&informationsystem_id={$oProperty->informationsystem_id}',windowId: '{$windowId}'}); return false")
			;

		$oAdmin_Form_Entity_InfGroups->add($oAdmin_Form_Entity_InfItems);
	}

	/**
	 * Fill shops/items list
	 * @param int $value shop_item_id
	 * @param Property_Model $oProperty property
	 * @param Admin_Form_Entity_Select $oAdmin_Form_Entity_Shop_Groups
	 * @param Admin_Form_Entity_Select $oAdmin_Form_Entity_Shop_Items
	 */
	protected function _fillShop($value, $oProperty, $oAdmin_Form_Entity_Shop_Groups, $oAdmin_Form_Entity_Shop_Items)
	{
		$Shop_Item = Core_Entity::factory('Shop_Item', $value);

		$gropup_id = $value == 0
			? 0
			: intval($Shop_Item->shop_group_id);

		$windowId = $this->_Admin_Form_Controller->getWindowId();

		$oShop = $oProperty->Shop;
		$oShop_Items = $oShop->Shop_Items;

		switch ($oShop->items_sorting_direction)
		{
			case 1:
				$items_sorting_direction = 'DESC';
			break;
			case 0:
			default:
				$items_sorting_direction = 'ASC';
		}

		$oShop_Items
			->queryBuilder()
			->clearOrderBy();

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

		// Items
		$aShop_Items = $oShop_Items->getAllByshop_group_id($gropup_id);

		$aOptions = array(' … ');
		foreach ($aShop_Items as $oShop_Item)
		{
			$aOptions[$oShop_Item->id] = !$oShop_Item->shortcut_id
				? $oShop_Item->name
				: $oShop_Item->Shop_Item->name;
		}
		$oAdmin_Form_Entity_Shop_Items->options($aOptions);

		// Groups
		$aOptions = Shop_Item_Controller_Edit::fillShopGroup($oProperty->shop_id, 0);
		$oAdmin_Form_Entity_Shop_Groups
			->value($Shop_Item->shop_group_id)
			->options(array(' … ') + $aOptions)
			->onchange("$.ajaxRequest({path: '/admin/shop/item/index.php', context: '{$oAdmin_Form_Entity_Shop_Items->id}', callBack: $.loadSelectOptionsCallback, action: 'loadShopItemList',additionalParams: 'shop_group_id=' + this.value + '&shop_id={$oProperty->shop_id}',windowId: '{$windowId}'}); return false")
			;

		$oAdmin_Form_Entity_Shop_Groups->add($oAdmin_Form_Entity_Shop_Items);
	}

	/**
	 * Get property list
	 * @return array
	 */
	protected function _getProperties()
	{
		// Properties
		return $this->linkedObject->Properties;
	}

	/**
	 * Apply object property
	 */
	public function applyObjectProperty()
	{
		$aProperties = $this->_getProperties()->findAll();

		$windowId = $this->_Admin_Form_Controller->getWindowId();

		foreach ($aProperties as $oProperty)
		{
			// Values already exist
			$aProperty_Values = $oProperty->getValues($this->_object->id);

			switch ($oProperty->type)
			{
				case 0: // Int
				case 1: // String
				case 3: // List
				case 4: // Textarea
				case 5: // ИС
				case 6: // Wysiwyg
				case 7: // Checkbox
				case 8: // Date
				case 9: // Datetime
				case 10: // Hidden field
				case 11: // Float
				case 12: // Shop

					// Values already exist
					foreach ($aProperty_Values as $oProperty_Value)
					{
						$value = Core_Array::getPost("property_{$oProperty->id}_{$oProperty_Value->id}");
						$value = $this->_correctValue($oProperty, $value);

						$oProperty_Value
							->setValue($value)
							->save();
					}

					// New values of property
					$aNewValue = Core_Array::getPost("property_{$oProperty->id}", array());

					// Checkbox, значений раньше не было и не пришло новых значений
					if ($oProperty->type == 7 && count($aProperty_Values) == 0
						&& is_array($aNewValue) && !count($aNewValue))
					{
						$aNewValue = array(0);
					}

					// New values of property
					if (is_array($aNewValue))
					{
						foreach ($aNewValue as $newValue)
						{
							$oNewValue = $oProperty->createNewValue($this->_object->id);

							$newValue = $this->_correctValue($oProperty, $newValue);

							$oNewValue
								->setValue($newValue)
								->save();

							ob_start();
							Core::factory('Core_Html_Entity_Script')
								->type("text/javascript")
								->value("$(\"#{$windowId} *[name='property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'property_{$oProperty->id}_{$oNewValue->id}')")
								->execute();

							$this->_Admin_Form_Controller->addMessage(ob_get_clean());
						}
					}

				break;

				case 2: // File

					// Values already exist
					foreach ($aProperty_Values as $oFileValue)
					{
						$aLargeFile = Core_Array::getFiles("property_{$oProperty->id}_{$oFileValue->id}");
						$aSmallFile = Core_Array::getFiles("small_property_{$oProperty->id}_{$oFileValue->id}");

						// ----
						$description = Core_Array::getPost("description_property_{$oProperty->id}_{$oFileValue->id}");
						if (!is_null($description))
						{
							$oFileValue->file_description = $description;
							$oFileValue->save();
						}

						$description_small = Core_Array::getPost("description_small_property_{$oProperty->id}_{$oFileValue->id}");

						if (!is_null($description_small))
						{
							$oFileValue->file_small_description = $description_small;
							$oFileValue->save();
						}
						// ----

						$this->_loadFiles($aLargeFile, $aSmallFile, $oFileValue, $oProperty, "property_{$oProperty->id}_{$oFileValue->id}");
					}

					// New values of property
					$aNewValueLarge = Core_Array::getFiles("property_{$oProperty->id}", array());
					$aNewValueSmall = Core_Array::getFiles("small_property_{$oProperty->id}", array());

					// New values of property
					if (is_array($aNewValueLarge) && isset($aNewValueLarge['name']))
					{
						$iCount = count($aNewValueLarge['name']);

						for ($i = 0; $i < $iCount; $i++)
						{
							$oFileValue = $oProperty->createNewValue($this->_object->id);

							ob_start();

							$aLargeFile = array(
								'name' => $aNewValueLarge['name'][$i],
								'type' => $aNewValueLarge['type'][$i],
								'tmp_name' => $aNewValueLarge['tmp_name'][$i],
								'error' => $aNewValueLarge['error'][$i],
								'size' => $aNewValueLarge['size'][$i],
							);

							$aSmallFile = isset($aNewValueSmall['name'][$i])
								? array(
									'name' => $aNewValueSmall['name'][$i],
									'type' => $aNewValueSmall['type'][$i],
									'tmp_name' => $aNewValueSmall['tmp_name'][$i],
									'error' => $aNewValueSmall['error'][$i],
									'size' => $aNewValueSmall['size'][$i],
								)
								: NULL;

							// -------
							$description = $this->_getEachPost("description_property_{$oProperty->id}");
							if (!is_null($description))
							{
								$oFileValue->file_description = $description;
							}

							$description_small = $this->_getEachPost("description_small_property_{$oProperty->id}");

							if (!is_null($description_small))
							{
								$oFileValue->file_small_description = $description_small;
							}
							// -------

							$oFileValue->save();

							$this->_loadFiles($aLargeFile, $aSmallFile, $oFileValue, $oProperty, "property_{$oProperty->id}");

							$this->_Admin_Form_Controller->addMessage(ob_get_clean());

							ob_start();
							Core::factory('Core_Html_Entity_Script')
								->type("text/javascript")
								->value("$(\"#{$windowId} div[id^='file_large'] input[name='property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} div[id^='file_small'] input[name='small_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'small_property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} input[name='description_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'description_property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} input[name='description_small_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'description_small_property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} input[name='large_max_width_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'large_max_width_property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} input[name='large_max_height_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'large_max_height_property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} input[name='large_preserve_aspect_ratio_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'large_preserve_aspect_ratio_property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} input[name='large_place_watermark_checkbox_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'large_place_watermark_checkbox_property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} input[name='watermark_position_x_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'watermark_position_x_property_{$oProperty->id}_{$oFileValue->id}');" .
								"$(\"#{$windowId} input[name='watermark_position_y_property_{$oProperty->id}\\[\\]']\").eq(0).attr('name', 'watermark_position_y_property_{$oProperty->id}_{$oFileValue->id}');"
								)
								->execute();

							$this->_Admin_Form_Controller->addMessage(ob_get_clean());
						}
					}
				break;

				default:
					throw new Core_Exception(
						Core::_('Property.type_does_not_exist'),
							array('%d' => $oProperty->type)
					);
			}
		}
	}

	/**
	 * Return value by key from POST
	 * @param string $name key
	 * @return string
	 */
	protected function _getEachPost($name)
	{
		if (!isset($this->_POST[$name]))
		{
			return NULL;
		}

		if (is_array($this->_POST[$name]))
		{
			list(, $val) = each($this->_POST[$name]);
			return $val;
		}
		return $this->_POST[$name];
	}

	/**
	 * Load files
	 * @param array $aLargeFile large file data
	 * @param array $aSmallFile small file data
	 * @param Property_Value_File_Model $oFileValue value of file object
	 * @param Property_Model $oProperty property
	 * @param string $sPropertyName property name
	 */
	protected function _loadFiles($aLargeFile, $aSmallFile, $oFileValue, $oProperty, $sPropertyName)
	{
		$oFileValue->setDir(
			$this->linkedObject->getDirPath($this->_object)
		);

		$param = array();

		$aFileData = $aLargeFile;
		$aSmallFileData = $aSmallFile;

		$large_image = '';
		$small_image = '';

		$aCore_Config = Core::$mainConfig;

		$create_small_image_from_large = $this->_getEachPost("create_small_image_from_large_small_{$sPropertyName}");

		$bLargeImageIsCorrect =
			// Поле файла большого изображения существует
			!is_null($aFileData)
			// и передан файл
			&& intval($aFileData['size']) > 0;

		if($bLargeImageIsCorrect)
		{
			// Проверка на допустимый тип файла
			if (Core_File::isValidExtension($aFileData['name'], $aCore_Config['availableExtension']))
			{
				// Удаление файла большого изображения
				if ($oFileValue->file)
				{
					$oFileValue
						->deleteLargeFile()
						//->deleteSmallFile()
						;
				}

				$file_name = $aFileData['name'];

				// Не преобразовываем название загружаемого файла
				$large_image = !$this->linkedObject->changeFilename
					? $file_name
					: $this->linkedObject->getLargeFileName($this->_object, $oFileValue, $aFileData['name']);
			}
			else
			{
				$this->_Admin_Form_Controller->addMessage(
					Core_Message::get(
						Core::_('Core.extension_does_not_allow', Core_File::getExtension($aFileData['name'])),
						'error'
					)
				);
			}
		}

		$bSmallImageIsCorrect =
			// Поле файла малого изображения существует
			!is_null($aSmallFileData)
			&& $aSmallFileData['size'];

		// Задано малое изображение и при этом не задано создание малого изображения
		// из большого или задано создание малого изображения из большого и
		// при этом не задано большое изображение.
		if ($bSmallImageIsCorrect || $create_small_image_from_large && $bLargeImageIsCorrect)
		{
			// Удаление файла малого изображения
			if ($oFileValue->file_small)
			{
				$oFileValue->deleteSmallFile();
			}

			// Явно указано малое изображение
			if ($bSmallImageIsCorrect
				&& Core_File::isValidExtension($aSmallFileData['name'], $aCore_Config['availableExtension']))
			{
				// задано изображение
				if ($oFileValue->file != '')
				{
					// Существует ли большое изображение
					$param['large_image_isset'] = TRUE;
					$create_large_image = FALSE;
				}
				else // ранее не было задано большое изображение
				{
					$create_large_image = empty($large_image);
				}

				$file_name = $aSmallFileData['name'];

				// Не преобразовываем название загружаемого файла
				if (!$this->linkedObject->changeFilename)
				{
					if ($create_large_image)
					{
						$large_image = $file_name;
						$small_image = 'small_' . $large_image;
					}
					else
					{
						$small_image = $file_name;
					}
				}
				else
				{
					$small_image = $this->linkedObject
						->getSmallFileName($this->_object, $oFileValue, $aSmallFileData['name']);
				}
			}
			elseif ($create_small_image_from_large && $bLargeImageIsCorrect)
			{
				$small_image = 'small_' . $large_image;
				//$param['small_image_source'] = $aFileData['tmp_name'];
				// Имя большого изображения
				$param['small_image_name'] = $aFileData['name'];
			}
			// Тип загружаемого файла является недопустимым для загрузки файла
			else
			{
				$this->_Admin_Form_Controller->addMessage(
					Core_Message::get(
						Core::_('Core.extension_does_not_allow', Core_File::getExtension($aSmallFileData['name'])),
						'error'
					)
				);
			}
		}

		if ($bLargeImageIsCorrect || $bSmallImageIsCorrect)
		{
			if ($bLargeImageIsCorrect)
			{
				// Путь к файлу-источнику большого изображения;
				$param['large_image_source'] = $aFileData['tmp_name'];
				// Оригинальное имя файла большого изображения
				$param['large_image_name'] = $aFileData['name'];
			}

			if ($bSmallImageIsCorrect)
			{
				// Путь к файлу-источнику малого изображения;
				$param['small_image_source'] = $aSmallFileData['tmp_name'];
				// Оригинальное имя файла малого изображения
				$param['small_image_name'] = $aSmallFileData['name'];
			}

			// Путь к создаваемому файлу большого изображения;
			$param['large_image_target'] = !empty($large_image)
				? $this->linkedObject->getDirPath($this->_object) . $large_image
				: '';

			// Путь к создаваемому файлу малого изображения;
			$param['small_image_target'] = !empty($small_image)
				? $this->linkedObject->getDirPath($this->_object) . $small_image
				: '';

			// Использовать большое изображение для создания малого
			$param['create_small_image_from_large'] = $create_small_image_from_large;

			// Значение максимальной ширины большого изображения
			$param['large_image_max_width'] = $this->_getEachPost("large_max_width_{$sPropertyName}", 0);

			// Значение максимальной высоты большого изображения
			$param['large_image_max_height'] = $this->_getEachPost("large_max_height_{$sPropertyName}", 0);

			// Значение максимальной ширины малого изображения;
			$param['small_image_max_width'] = $this->_getEachPost("small_max_width_small_{$sPropertyName}");

			// Значение максимальной высоты малого изображения;
			$param['small_image_max_height'] = $this->_getEachPost("small_max_height_small_{$sPropertyName}");

			// Путь к файлу с "водяным знаком"
			$param['watermark_file_path'] = $this->linkedObject->watermarkFilePath;

			// Позиция "водяного знака" по оси X
			$param['watermark_position_x'] = $this->_getEachPost("watermark_position_x_{$sPropertyName}");

			// Позиция "водяного знака" по оси Y
			$param['watermark_position_y'] = $this->_getEachPost("watermark_position_y_{$sPropertyName}");

			// Наложить "водяной знак" на большое изображение (true - наложить (по умолчанию), FALSE - не наложить);
			$param['large_image_watermark'] = !is_null($this->_getEachPost("large_place_watermark_checkbox_{$sPropertyName}"));

			// Наложить "водяной знак" на малое изображение (true - наложить (по умолчанию), FALSE - не наложить);
			$param['small_image_watermark'] = !is_null($this->_getEachPost("small_place_watermark_checkbox_small_{$sPropertyName}"));

			// Сохранять пропорции изображения для большого изображения
			$param['large_image_preserve_aspect_ratio'] = !is_null($this->_getEachPost("large_preserve_aspect_ratio_{$sPropertyName}"));

			// Сохранять пропорции изображения для малого изображения
			$param['small_image_preserve_aspect_ratio'] = !is_null($this->_getEachPost("small_preserve_aspect_ratio_small_{$sPropertyName}"));

			$this->linkedObject->createPropertyDir($this->_object);

			try
			{
				$result = Core_File::adminUpload($param);

				if ($result['large_image'])
				{
					$oFileValue->file = $large_image;
					$oFileValue->file_name = is_null($param['large_image_name'])
						? ''
						: $param['large_image_name'];
				}

				if ($result['small_image'])
				{
					$oFileValue->file_small = $small_image;
					$oFileValue->file_small_name = is_null($param['small_image_name'])
						? ''
						: $param['small_image_name'];
				}

				$oFileValue->save();
			}
			catch (Exception $e)
			{
				Core_Message::show($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * Correct save value by property type
	 * @param Property $oProperty property
	 * @param string $value value
	 * @return string
	 */
	protected function _correctValue($oProperty, $value)
	{
		switch ($oProperty->type)
		{
			case 0: // Int
			case 7: // Checkbox
			case 3: // List
				$value = intval($value);
			break;
			case 1: // String
			case 4: // Textarea
			case 6: // Wysiwyg
				$value = strval($value);
			break;
			case 11: // Float
				$value = floatval(
					str_replace(array(',', '-'), '.', $value)
				);
			break;
			case 8: // Date
				$value = $value == ''
					? '0000-00-00 00:00:00'
					: Core_Date::date2sql($value);
			break;
			case 9: // Datetime
				$value = $value == ''
					? '0000-00-00 00:00:00'
					: Core_Date::datetime2sql($value);
			break;
		}

		return $value;
	}

	/**
	 * Correct print value by property type
	 * @param Property $oProperty property
	 * @param string $value value
	 * @return string
	 */
	protected function _correctPrintValue($oProperty, $value)
	{
		switch ($oProperty->type)
		{
			case 8: // Date
				$value = $value == '0000-00-00 00:00:00'
					? ''
					: Core_Date::date2sql($value);
			break;
			case 9: // Datetime
				$value = $value == '0000-00-00 00:00:00'
					? ''
					: Core_Date::datetime2sql($value);
			break;
		}
		return $value;
	}
}