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
class Shop_Company_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit{
	/**
	 * Load object's fields when object has been set
	 * После установки объекта загружаются данные о его полях
	 * @param object $object
	 * @return Shop_Company_Controller_Edit
	 */	public function setObject($object)	{		parent::setObject($object);				// Основная вкладка		$oMainTab = $this->getTab('main');				// Добавляем вкладки		$this				->addTabAfter(				$oTabManagers = Admin_Form_Entity::factory('Tab')					->caption(Core::_('Shop_Company.tabManagers'))					->name('Managers'), 				$oMainTab)				->addTabAfter(				$oTabContacts = Admin_Form_Entity::factory('Tab')					->caption(Core::_('Shop_Company.tabContacts'))					->name('Contacts'), 				$oTabManagers)				->addTabAfter(				$oTabBankingDetails = Admin_Form_Entity::factory('Tab')					->caption(Core::_('Shop_Company.tabBankingDetails'))					->name('BankingDetails'), 				$oTabContacts)				->addTabAfter(				$oTabGUID = Admin_Form_Entity::factory('Tab')					->caption(Core::_('Shop_Company.guid'))					->name('GUID'), 				$oTabBankingDetails)		;				$oMainTab			// Managers			->move($this->getField('legal_name'), $oTabManagers)			->move($this->getField('accountant_legal_name'), $oTabManagers)			// Contacts			->move($this->getField('address'), $oTabContacts)			->move($this->getField('phone'), $oTabContacts)			->move($this->getField('fax'), $oTabContacts)			->move($this->getField('site'), $oTabContacts)			->move($this->getField('email'), $oTabContacts)			// BankingDetails			->move($this->getField('tin'), $oTabBankingDetails)			->move($this->getField('kpp'), $oTabBankingDetails)			->move($this->getField('psrn'), $oTabBankingDetails)			->move($this->getField('okpo'), $oTabBankingDetails)			->move($this->getField('okved'), $oTabBankingDetails)			->move($this->getField('bic'), $oTabBankingDetails)			->move($this->getField('current_account'), $oTabBankingDetails)			->move($this->getField('correspondent_account'), $oTabBankingDetails)			->move($this->getField('bank_name'), $oTabBankingDetails)			->move($this->getField('bank_address'), $oTabBankingDetails)			// GUID			->move($this->getField('guid'), $oTabGUID)		;						$title = $this->_object->id					? Core::_('Shop_Company.company_form_edit_title')					: Core::_('Shop_Company.company_form_add_title');		$this->title($title);		return $this;	}}