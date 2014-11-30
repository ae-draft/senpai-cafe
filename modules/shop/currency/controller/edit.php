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
class Shop_Currency_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit{
	/**
	 * Load object's fields when object has been set
	 * После установки объекта загружаются данные о его полях
	 * @param object $object
	 * @return Shop_Currency_Controller_Edit
	 */	public function setObject($object)	{		parent::setObject($object);		$title = $this->_object->id					? Core::_('Shop_Currency.currency_edit_form_title')					: Core::_('Shop_Currency.currency_add_form_title');		$this->title($title);		return $this;	}

	/**
	 * Processing of the form. Apply object fields.
	 * @return self
	 * @hostcms-event Shop_Currency_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		// Reset default for other currencies
		if (Core_Array::get($this->_formValues, 'default'))
		{
			$aShop_Currencies = Core_Entity::factory('Shop_Currency')->findAll();
			foreach ($aShop_Currencies as $oShop_Currency)
			{
				if ($oShop_Currency->default)
				{
					$oShop_Currency->default = 0;
					$oShop_Currency->save();
				}
			}
		}

		parent::_applyObjectProperty();

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		return $this;
	}}