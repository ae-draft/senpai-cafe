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
class Shop_Discount_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
		
		$this->getField('description')->wysiwyg(TRUE);
		
		
		$this->getField('start_datetime')->divAttr(array('style' => 'float: left; width: 150px'));
		$oEnd_Time = $this->getField('end_datetime');
		$oEnd_Time->divAttr(array('style' => 'float: left; width: 150px'));
		$this->getTab('main')->addAfter(Admin_Form_Entity::factory('Separator'), $oEnd_Time);
		$this->getField('percent')->divAttr(array('style' => 'width: 150px'));
		
		$title = $this->_object->id
					? Core::_('Shop_Discount.item_discount_edit_form_title')
					: Core::_('Shop_Discount.item_discount_add_form_title');

		$this->title($title);

		return $this;
	}
}