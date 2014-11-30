<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Admin_Language_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$title = is_null($this->_object->id)
			? Core::_('Admin_Language.form_add_forms_add_language_title')
			: Core::_('Admin_Language.form_add_forms_edit_language_title');

		$this->title($title);

		return $this;
	}
}