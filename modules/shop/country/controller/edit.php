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
class Shop_Country_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit{
	/**
	 * Load object's fields when object has been set
	 * После установки объекта загружаются данные о его полях
	 * @param object $object
	 * @return Shop_Country_Controller_Edit
	 */	public function setObject($object)	{		parent::setObject($object);		$title = $this->_object->id					? Core::_('Shop_Country.country_edit_form_title')					: Core::_('Shop_Country.country_add_form_title');		$this->title($title);		return $this;	}}