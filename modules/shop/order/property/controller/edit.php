<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Properties.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Order_Property_Controller_Edit extends Property_Controller_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$modelName = $this->_object->getModelName();

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');

		switch($modelName)
		{
			case 'property':

				// Префикс
				$oShopPrefixInput = Admin_Form_Entity::factory('Input');
				$oShopPrefixInput
					->caption(Core::_('Shop_Order.prefix'))
					->style('width: 100px')
					->name('prefix')
					->value($this->_object->Shop_Order_Property->prefix)
					->divAttr(array('style' => 'float: left'));

				$oMainTab
					->add($oShopPrefixInput);

				// Способ отображения в фильтре
				$oShopFilterSelect = Admin_Form_Entity::factory('Select');
				$oShopFilterSelect
					->caption(Core::_('Shop_Order.display'))
					->style('width: 250px')
					->options(
						array(0 => Core::_('Shop_Order.properties_show_kind_none'),
						1 => Core::_('Shop_Order.properties_show_kind_text'),
						2 => Core::_('Shop_Order.properties_show_kind_list'),
						3 => Core::_('Shop_Order.properties_show_kind_radio'),
						4 => Core::_('Shop_Order.properties_show_kind_checkbox'),
						5 => Core::_('Shop_Order.properties_show_kind_checkbox_one'),
						//6 => Core::_('Shop_Order.properties_show_kind_from_to'),
						7 => Core::_('Shop_Order.properties_show_kind_listbox'),
						8 => Core::_('Shop_Order.properties_show_kind_textarea'))
					)
					->name('display')
					->value($this->_object->Shop_Order_Property->display)
					->divAttr(array('style' => 'float: left'));

				$oMainTab
					->add($oShopFilterSelect);

			break;
			case 'property_dir':
			default:
			break;
		}

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Shop_Order_Property_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		parent::_applyObjectProperty();

		$modelName = $this->_object->getModelName();

		switch($modelName)
		{
			case 'property':
				$Shop_Order_Property = $this->_object->Shop_Order_Property;
				$Shop_Order_Property->prefix = Core_Array::getPost('prefix');
				$Shop_Order_Property->display = Core_Array::getPost('display');
				$Shop_Order_Property->save();
			break;
			case 'property_dir':
			break;
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}
}