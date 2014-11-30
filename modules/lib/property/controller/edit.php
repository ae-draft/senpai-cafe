<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Libs.
 *
 * @package HostCMS 6\Lib
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Lib_Property_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Constructor.
	 * @param Admin_Form_Action_Model $oAdminFormAction action
	 */
	public function __construct(Admin_Form_Action_Model $oAdminFormAction)
	{
		parent::__construct($oAdminFormAction);
	}

	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		// При добавлении объекта
		if(is_null($object->id))
		{
			$object->lib_id = Core_Array::getGet('lib_id');
		}

		parent::setObject($object);

		$this->title($this->_object->id
			? Core::_('Lib_Property.lib_property_form_title_edit')
			: Core::_('Lib_Property.lib_property_form_title_add'));

		// Создаем элемент <select>
		$oHtmlFormSelect = Admin_Form_Entity::factory('Select');

		$oHtmlFormSelect
			->options
			(
				array
				(
					Core::_('Lib_Property.lib_property_type_0'),
					Core::_('Lib_Property.lib_property_type_1'),
					Core::_('Lib_Property.lib_property_type_2'),
					Core::_('Lib_Property.lib_property_type_3'),
					Core::_('Lib_Property.lib_property_type_4'),
					Core::_('Lib_Property.lib_property_type_5')
				)
			);

		$windowId = $this->_Admin_Form_Controller->getWindowId();

		$oHtmlFormSelect
				->name('type')
				->value($this->_object->type)
				->caption(Core::_('Lib_Property.type'))
				->onchange("ShowRowsLibProperty('{$windowId}', this.options[this.selectedIndex].value)");

		// Явно определяем ID <div>
		$this->getField('sql_request')->divAttr(
			array('id' => 'sql_request')
		);
		$this->getField('sql_caption_field')->divAttr(
			array('id' => 'sql_caption_field')
		);
		$this->getField('sql_value_field')->divAttr(
			array('id' => 'sql_value_field')
		);

		// Получаем основную вкладку
		$oMainTab = $this->getTab('main');

		// Удаляем стандартный <input>
		$oMainTab->delete($this->getField('type'));

		$oMainTab->addAfter($oHtmlFormSelect, $this->getField('varible_name'));

		$oAdmin_Form_Entity_Code = Admin_Form_Entity::factory('Code');
		$oAdmin_Form_Entity_Code->html(
			"<script>ShowRowsLibProperty('{$windowId}', " . intval($this->_object->type) . ")</script>"
		);

		$oMainTab->add($oAdmin_Form_Entity_Code);

		return $this;
	}
}
