<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Affiliates.
 *
 * @package HostCMS 6\Affiliate
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Affiliate_Plan_Level_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
			$object->affiliate_plan_id = intval(Core_Array::getGet('affiliate_plan_id'));
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');
		$oSeparatorField = Admin_Form_Entity::factory('Separator');

		$this->getField('level')
			->divAttr(array('style' => 'float: left'))
			->style("width: 100px");

		$this->getField('percent')
			->class('large')
			->divAttr(array('style' => 'float: left'))
			->style("width: 100px");

		$oMainTab->delete(
			$this->getField('type')
		);

		$oValueField = $this->getField('value');

		$oValueField
			->class('large')
			->divAttr(array('style' => 'float: left'))
			->style('width: 100px;');

		$oTypeField = Admin_Form_Entity::factory('Select');
		$oTypeField
			->name('type')
			->class('large')
			->style('width: 100px;')
			->caption(Core::_('Affiliate_Plan_Level.type'))
			->options(array
			(
				Core::_('Affiliate_Plan_Level.form_edit_affiliate_values_type_percent'),
				Core::_('Affiliate_Plan_Level.form_edit_affiliate_values_type_summ')
			))
			->value($this->_object->type);

		$oMainTab->addAfter($oTypeField, $oValueField);

		$oAdditionalTab->delete(
			$this->getField('affiliate_plan_id')
		);

		$oAffiliatePlanField = Admin_Form_Entity::factory('Select');
		$oAffiliatePlanField
			->name('affiliate_plan_id')
			->caption(Core::_('Affiliate_Plan_Level.affiliate_plan_id'))
			->options
			(
				$this->_fillAffiliatePlans($this->_object->Affiliate_Plan->site_id)
			)
			->value($this->_object->affiliate_plan_id);

		$oMainTab->addAfter($oSeparatorField, $oTypeField);
		$oMainTab->addAfter($oAffiliatePlanField, $oSeparatorField);

		// Заголовок формы
		$title = $this->_object->id
			? Core::_('Affiliate_Plan_Level.edit_affiliate_value')
			: Core::_('Affiliate_Plan_Level.add_affiliate_value');

		$this->title($title);

		return $this;
	}

	/**
	 * Fill affiliate plans list
	 * @param int $iSiteId site ID
	 * @return array
	 */
	protected function _fillAffiliatePlans($iSiteId)
	{
		$oAffiliatePlan = Core_Entity::factory('Affiliate_Plan');

		$oAffiliatePlan->queryBuilder()
			->where('site_id', '=', $iSiteId)
			->orderBy('name');

		$aAffiliatePlans = $oAffiliatePlan->findAll();

		$aReturn = array();

		foreach($aAffiliatePlans as $oAffiliatePlan)
		{
			$aReturn[$oAffiliatePlan->id] = $oAffiliatePlan->name;
		}

		return $aReturn;
	}
}