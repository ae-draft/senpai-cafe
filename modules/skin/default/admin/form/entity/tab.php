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
class Skin_Default_Admin_Form_Entity_Tab extends Admin_Form_Entity
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'name',
		'caption',
		'active'
	);

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->active = TRUE;
	}

	/**
	 * Check if there field with same name is
	 * @param string $fieldName name
	 * @return boolean
	 */
	public function issetField($fieldName)
	{
		foreach ($this->_children as $object)
		{
			if (isset($object->name) && $object->name == $fieldName)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Get field by name
	 * @param string $fieldName name
	 * @return object
	 */
	public function getField($fieldName)
	{
		foreach ($this->_children as $object)
		{
			if (isset($object->name) && $object->name == $fieldName)
			{
				return $object;
			}
		}

		throw new Core_Exception("Field %fieldName does not exist.", array('%fieldName' => $fieldName));
	}

	/**
	 * Get tab fields
	 * @return array
	 */
	public function getFields()
	{
		return $this->_children;
	}
}