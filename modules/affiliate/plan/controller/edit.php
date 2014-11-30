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
class Affiliate_Plan_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
			$object->site_id = CURRENT_SITE;
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');
		$oSeparatorField = Admin_Form_Entity::factory('Separator');

		$oAdditionalTab->delete(
			$this->getField('site_id')
		);

		$oAdditionalTab->delete(
			$this->getField('siteuser_group_id')
		);

		$Site_Controller_Edit = new Site_Controller_Edit($this->_Admin_Form_Action);

		$oSiteField = Admin_Form_Entity::factory('Select');
		$oSiteField
			->name('site_id')
			->caption(Core::_('Affiliate_Plan.site_id'))
			->options($Site_Controller_Edit->fillSites())
			->value($this->_object->site_id);

		$oMainTab->addAfter($oSiteField, $this->getField('description'));

		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser_Controller_Edit = new Siteuser_Controller_Edit($this->_Admin_Form_Action);
			$aSiteuser_Groups = $oSiteuser_Controller_Edit->fillSiteuserGroups(CURRENT_SITE);
		}
		else
		{
			$aSiteuser_Groups = array();
		}

		$oSiteUserGroupField = Admin_Form_Entity::factory('Select');
		$oSiteUserGroupField
			->name('siteuser_group_id')
			->caption(Core::_('Affiliate_Plan.siteuser_group_id'))
			->options($aSiteuser_Groups)
			->value($this->_object->siteuser_group_id);

		$oMainTab->addAfter($oSiteUserGroupField, $this->getField('active'));

		$this->getField('min_count_of_items')
			->divAttr(array('style' => 'float: left'))
			->style("width: 300px");

		$oMainTab->addAfter($oSeparatorField,
			$this->getField('min_amount_of_items')
				->style("width: 300px"));

		// Заголовок формы
		$title = $this->_object->id
					? Core::_('Affiliate_Plan.affiliate_form_edit')
					: Core::_('Affiliate_Plan.affiliate_form_add');

		$this->title($title);

		return $this;
	}
}