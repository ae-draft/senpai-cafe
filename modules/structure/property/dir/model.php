<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Structure.
 *
 * @package HostCMS 6\Structure
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2012 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Structure_Property_Dir_Model extends Core_Entity{
	/**
	 * Disable markDeleted()
	 * @var mixed
	 */	protected $_marksDeleted = NULL;
	/**
	 * Belongs to relations
	 * @var array
	 */	protected $_belongsTo = array(		'site' => array(),		'property_dir' => array(),	);}