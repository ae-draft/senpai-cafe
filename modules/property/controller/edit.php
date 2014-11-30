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
class Property_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Constructor.
	 * @param Admin_Form_Action_Model $oAdmin_Form_Action action
	 */
	public function __construct(Admin_Form_Action_Model $oAdmin_Form_Action)
	{
		$this->_allowedProperties[] = 'linkedObject';

		parent::__construct($oAdmin_Form_Action);
	}

	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		$modelName = $object->getModelName();

		$bNewProperty = is_null($object->id);
		
		if ($bNewProperty && $modelName == 'property')
		{
			$object->image_large_max_width = $this->linkedObject->getLargeImageMaxWidth();
			$object->image_large_max_height = $this->linkedObject->getLargeImageMaxHeight();
			$object->image_small_max_width = $this->linkedObject->getSmallImageMaxWidth();
			$object->image_small_max_height = $this->linkedObject->getSmallImageMaxHeight();
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');
		$oSelect_Dirs = Admin_Form_Entity::factory('Select');

		switch($modelName)
		{
			case 'property':
				$title = $this->_object->id
					? Core::_('Property.edit_title')
					: Core::_('Property.add_title');

				if (is_null($this->_object->id))
				{
					$this->_object->property_dir_id = Core_Array::getGet('property_dir_id');
				}

				$oFormatTab = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Property.tab_format'))
					->name('Format');

				$this
					->addTabAfter($oFormatTab, $oMainTab);

				$this->getField('description')
					->wysiwyg(TRUE);

				$oMainTab
					->move($this->getField('guid'), $oAdditionalTab);

				// Удаляем стандартный <input>
				$oMainTab->delete($this->getField('type'));

				$windowId = $this->_Admin_Form_Controller->getWindowId();

				$aListTypes = array(
					0 => Core::_('Property.type0'),
					11 => Core::_('Property.type11'),
					1 => Core::_('Property.type1'),
					2 => Core::_('Property.type2'),
					3 => Core::_('Property.type3'),
					4 => Core::_('Property.type4'),
					5 => Core::_('Property.type5'),
					12 => Core::_('Property.type12'),
					6 => Core::_('Property.type6'),
					7 => Core::_('Property.type7'),
					8 => Core::_('Property.type8'),
					9 => Core::_('Property.type9'),
					10 => Core::_('Property.type10'),
				);

				// Delete list type if module is not active
				if (!Core::moduleIsActive('list'))
				{
					unset($aListTypes[3]);
				}
				// Delete informationsystem type if module is not active
				if (!Core::moduleIsActive('informationsystem'))
				{
					unset($aListTypes[5]);
				}
				// Delete shop type if module is not active
				if (!Core::moduleIsActive('shop'))
				{
					unset($aListTypes[12]);
				}

				// Селектор с группой
				$oSelect_Type = Admin_Form_Entity::factory('Select')
					->options($aListTypes)
					->name('type')
					->value($this->_object->type)
					->caption(Core::_('Property.type'))
					->onchange("ShowPropertyRows('{$windowId}', this.options[this.selectedIndex].value)")
					->style('width: 320px')
					->divAttr(array('style' => 'float: left'));

				$oMainTab->addAfter(
					$oSelect_Type, $this->getField('name')
				);

				// Удаляем стандартный <input>
				$oAdditionalTab->delete($this->getField('property_dir_id'));

				// Селектор с группой
				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillPropertyDir()
					)
					->name('property_dir_id')
					->value($this->_object->property_dir_id)
					->caption(Core::_('Property_Dir.parent_id'))
					->style('width: 320px');

				$oMainTab->addAfter(
					$oSelect_Dirs, $oSelect_Type
				);

				// Список
				if (Core::moduleIsActive('list'))
				{
					$oAdditionalTab->delete($this->getField('list_id'));

					$oList_Controller_Edit = new List_Controller_Edit($this->_Admin_Form_Action);
					// Селектор с группой
					$oSelect_Lists = Admin_Form_Entity::factory('Select')
						->options(
							array(' … ') + $oList_Controller_Edit->fillLists(CURRENT_SITE)
						)
						->name('list_id')
						->value($this->_object->list_id)
						->caption(Core::_('Property.list_id'))
						->style('width: 320px')
						->divAttr(array('id' => 'list_id'))
						;

					$oMainTab
						->addAfter($oSelect_Lists, $oSelect_Dirs);
				}

				// Информационные системы
				if (Core::moduleIsActive('informationsystem'))
				{
					$oAdditionalTab->delete($this->getField('informationsystem_id'));

					$oInformationsystem_Controller_Edit = new Informationsystem_Controller_Edit($this->_Admin_Form_Action);
					// Селектор с группой
					$oSelect_Informationsystems = Admin_Form_Entity::factory('Select')
						->options(
							array(' … ') + $oInformationsystem_Controller_Edit->fillInformationsystems(CURRENT_SITE)
						)
						->name('informationsystem_id')
						->value($this->_object->informationsystem_id)
						->caption(Core::_('Property.informationsystem_id'))
						->style('width: 320px')
						->divAttr(array('id' => 'informationsystem_id'))
						;

					$oMainTab
						->addAfter($oSelect_Informationsystems, $oSelect_Dirs);
				}

				// Магазин
				if (Core::moduleIsActive('shop'))
				{
					$oAdditionalTab->delete($this->getField('shop_id'));

					$oshop_Controller_Edit = new shop_Controller_Edit($this->_Admin_Form_Action);
					// Селектор с группой
					$oSelect_Shops = Admin_Form_Entity::factory('Select')
						->options(
							array(' … ') + $oshop_Controller_Edit->fillShops(CURRENT_SITE)
						)
						->name('shop_id')
						->value($this->_object->shop_id)
						->caption(Core::_('Property.shop_id'))
						->style('width: 320px')
						->divAttr(array('id' => 'shop_id'))
						;

					$oMainTab
						->addAfter($oSelect_Shops, $oSelect_Dirs);
				}

				// ---
				$this->getField('tag_name')
					->style('width: 220px')
					->divAttr(array('style' => 'float: left'));

				// Для тегов проверка на длину только при редактировании.
				!$bNewProperty && $this->getField('tag_name')->format(
						array(
							'maxlen' => array('value' => 255),
							'minlen' => array('value' => 1)
						)
					);
					
				$this->getField('sorting')
					->style('width: 220px');

				$oMainTab
					->addAfter(Admin_Form_Entity::factory('Separator'), $this->getField('sorting'));

				$this->getField('default_value')
					->divAttr(array('id' => 'default_value'));

				$oDefault_Value_Date = Admin_Form_Entity::factory('Date')
					->value($this->_object->default_value)
					->name('default_value_date')
					->caption(Core::_('Property.default_value'))
					->divAttr(array('id' => 'default_value_date'));

				$oMainTab
					->addAfter($oDefault_Value_Date, $this->getField('default_value'));

				$oDefault_Value_DateTime = Admin_Form_Entity::factory('DateTime')
					->value($this->_object->default_value)
					->name('default_value_datetime')
					->caption(Core::_('Property.default_value'))
					->divAttr(array('id' => 'default_value_datetime'));

				$oMainTab
					->addAfter($oDefault_Value_DateTime, $this->getField('default_value'));

				$oDefault_Value_Checkbox = Admin_Form_Entity::factory('Checkbox')
					->value($this->_object->default_value)
					->caption(Core::_('Property.default_value'))
					->name('default_value_checked')
					->divAttr(array('id' => 'default_value_checked'));

				$oMainTab
					->addAfter($oDefault_Value_Checkbox, $this->getField('default_value'));

				// Formats
				$this->getField('image_large_max_width')
					->style('width: 320px')
					->divAttr(array('style' => 'float: left'));

				$this->getField('image_large_max_height')
					->style('width: 320px')
					//->divAttr(array('style' => 'float: left'))
					;

				$this->getField('image_small_max_width')
					->style('width: 320px')
					->divAttr(array('style' => 'float: left'));

				$this->getField('image_small_max_height')
					->style('width: 320px')
					//->divAttr(array('style' => 'float: left'))
					;

				$oMainTab
					->move($this->getField('image_large_max_width'), $oFormatTab)
					->move($this->getField('image_large_max_height'), $oFormatTab)
					->move($this->getField('image_small_max_width'), $oFormatTab)
					->move($this->getField('image_small_max_height'), $oFormatTab)
					->move($this->getField('hide_small_image'), $oFormatTab)
					;

				$oAdmin_Form_Entity_Code = Admin_Form_Entity::factory('Code');
				$oAdmin_Form_Entity_Code->html(
					"<script>ShowPropertyRows('{$windowId}', " . intval($this->_object->type) . ")</script>"
				);

				$oMainTab->add($oAdmin_Form_Entity_Code);


			break;
			case 'property_dir':
			default:
				$title = $this->_object->id
						? Core::_('Property_Dir.edit_title')
						: Core::_('Property_Dir.add_title');

				// Значения директории для добавляемого объекта
				if (is_null($this->_object->id))
				{
					$this->_object->parent_id = Core_Array::getGet('property_dir_id');
				}

				// Удаляем стандартный <input>
				$oAdditionalTab->delete(
					 $this->getField('parent_id')
				);

				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillPropertyDir(0, $this->_object->id)
					)
					->name('parent_id')
					->value($this->_object->parent_id)
					->caption(Core::_('Property_Dir.parent_id'));

				$oMainTab->addAfter($oSelect_Dirs, $this->getField('name'));
			break;
		}

		$this->title($title);

		return $this;
	}

	/**
	 * Create visual tree of the directories
	 * @param int $iPropertyDirParentId parent directory ID
	 * @param boolean $bExclude exclude group ID
	 * @param int $iLevel current nesting level
	 * @return array
	 */
	public function fillPropertyDir($iPropertyDirParentId = 0, $bExclude = FALSE, $iLevel = 0)
	{
		$iPropertyDirParentId = intval($iPropertyDirParentId);
		$iLevel = intval($iLevel);

		$childrenDirs = $this->linkedObject->Property_Dirs->getByParentId($iPropertyDirParentId);

		$aReturn = array();

		foreach ($childrenDirs as $childrenDir)
		{
			if ($bExclude != $childrenDir->id)
			{
				$aReturn[$childrenDir->id] = str_repeat('  ', $iLevel) . $childrenDir->name;
				$aReturn += $this->fillPropertyDir($childrenDir->id, $bExclude, $iLevel+1);
			}
		}

		return $aReturn;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Property_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		$bNewProperty = is_null($this->_object->id);

		parent::_applyObjectProperty();

		$modelName = $this->_object->getModelName();

		switch($modelName)
		{
			case 'property':
				if ($bNewProperty && trim($this->_object->tag_name) == '')
				{
					 $this->_object->tag_name = Core_Str::transliteration(
						Core::$mainConfig['translate']
							? Core_Str::translate($this->_object->name)
							: $this->_object->name
						);
				}
				
				switch($this->_object->type)
				{
					case 7: // Флажок
						$this->_object->default_value = Core_Array::getPost('default_value_checked', 0);
					break;
					case 8: // Дата
						$this->_object->default_value = strlen(Core_Array::getPost('default_value_date'))
							? Core_Date::date2sql(Core_Array::getPost('default_value_date'))
							: '0000-00-00 00:00:00';
					break;
					case 9: // Дата-время
						$this->_object->default_value = strlen(Core_Array::getPost('default_value_date'))
							? Core_Date::datetime2sql(Core_Array::getPost('default_value_date'))
							: '0000-00-00 00:00:00';
					break;
				}
				$this->_object->save();
			break;
			case 'property_dir':
			break;
		}

		if (!Core_Array::getPost('id'))
		{
			$this->linkedObject->add($this->_object);
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}
}