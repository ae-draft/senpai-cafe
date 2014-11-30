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
class Shop_Purchase_Discount_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		if (is_null($object->id))
		{
			$object->shop_id = Core_Array::getGet('shop_id');
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');
		$oSeparator = Admin_Form_Entity::factory('Separator');

		$oValueField = $this->getField('value');

		$oValueField
			->style("width: 170px;")
			->divAttr(array('style' => 'float: left'));

		$oMainTab->delete($this->getField('type'));
		$oAdditionalTab->delete($this->getField('shop_currency_id'));
		$oMainTab->delete($this->getField('mode'));

		$oTypeSelectField = Admin_Form_Entity::factory('Select');

		$oTypeSelectField
			->name('type')
			->caption(Core::_('Shop_Purchase_Discount.type'))
			->options(array(
				Core::_('Shop_Purchase_Discount.form_edit_affiliate_values_type_percent'),
				Core::_('Shop_Purchase_Discount.form_edit_affiliate_values_type_summ'))
			)
			->style("width: 100px;")
			->value($this->_object->type);

		$oMainTab->addAfter($oTypeSelectField, $oValueField);
		$oMainTab->addAfter($oSeparator, $oTypeSelectField);

		$this->getField('start_datetime')
			->divAttr(array('style' => 'float: left; width: 170px; padding-right: 20px;'));

		$this->getField('min_amount')
			->divAttr(array('style' => 'float: left; width: 170px; padding-right: 20px;'));

		$oMaxAmountField = $this->getField('max_amount');

		$oMaxAmountField
			->style("width: 170px;")
			->divAttr(array('style' => 'float: left;'));

		$Shop_Controller_Edit = new Shop_Controller_Edit($this->_Admin_Form_Action);

		$oCurrencySelectField = Admin_Form_Entity::factory('Select');

		$oCurrencySelectField
			->name('shop_currency_id')
			->caption(Core::_('Shop_Purchase_Discount.shop_currency_id'))
			->options($Shop_Controller_Edit->fillCurrencies())
			->style("width: 100px;")
			->value($this->_object->shop_currency_id);

		$oMainTab->addAfter($oCurrencySelectField, $oMaxAmountField);

		$oLogicSwitcherField = Admin_Form_Entity::factory('Radiogroup');

		$oLogicSwitcherField
			->name('mode')
			->value($this->_object->mode)
			->radio(array(
				Core::_('Shop_Purchase_Discount.order_discount_case_and'),
				Core::_('Shop_Purchase_Discount.order_discount_case_or'),
				Core::_('Shop_Purchase_Discount.order_discount_case_accumulative')
			))
			->divAttr(array('style' => 'font-weight: bold;'));

		$oMainTab->addAfter($oLogicSwitcherField, $oCurrencySelectField);
		$oMainTab->addAfter($oSeparator, $oCurrencySelectField);

		$this->getField('min_count')
			->style("width: 170px;")
			->divAttr(array('style' => 'float: left;'));

		$this->getField('max_count')->style("width: 170px;");

		$this->title($this->_object->id
			? Core::_('Shop_Purchase_Discount.edit_order_discount_form_title')
			: Core::_('Shop_Purchase_Discount.add_order_discount_form_title'));

		return $this;
	}
}