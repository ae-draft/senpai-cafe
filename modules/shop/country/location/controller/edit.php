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
class Shop_Country_Location_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
			$object->shop_country_id = Core_Array::getGet('shop_country_id');
		}

		parent::setObject($object);

		$title = $this->_object->id
					? Core::_('Shop_Country_Location.location_edit_form_title')
					: Core::_('Shop_Country_Location.location_add_form_title');

		$this->title($title);

		return $this;
	}
}