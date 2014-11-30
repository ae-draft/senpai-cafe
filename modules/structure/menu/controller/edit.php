<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Structure.
 *
 * @package HostCMS 6\Structure
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Structure_Menu_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$this->getField('sorting')
			->style('width: 220px');
		
		$title = is_null($this->_object->id)
			? Core::_('Structure_Menu.add_title')
			: Core::_('Structure_Menu.edit_title');

		$this->title($title);

		return $this;
	}
}