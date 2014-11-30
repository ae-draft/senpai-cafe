<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Abstract module
 *
 * @package HostCMS 6\Core
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
abstract class Core_Module
{
	/**
	 * Module version
	 * @var string
	 */
	public $version = NULL;

	/**
	 * Module date
	 * @var date
	 */
	public $date = NULL;

	/**
	 * Module menu
	 * @var array
	 */
	public $menu = array();

	/**
	 * Create module instance
	 * @param string $moduleName module name
	 * @return mixed
	 */
	static public function factory($moduleName)
	{
		$modelName = ucfirst($moduleName) . '_Module';
		if (class_exists($modelName))
		{
			return new $modelName();
		}

		return NULL;
	}

	/**
	 * List of admin pages
	 * @var array
	 */
	protected $_adminPages = array();

	/**
	 * Get list of admin pages
	 * @return array
	 */
	public function getAdminPages()
	{
		return $this->_adminPages;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Install module
	 * @return self
	 * @hostcms-event Core_Module.onBeforeInstall
	 */
	public function install()
	{
		Core_Event::notify(get_class($this) . '.onBeforeInstall', $this);

		return $this;
	}

	/**
	 * Uninstall module
	 * @return self
	 * @hostcms-event Core_Module.onBeforeUninstall
	 */
	public function uninstall()
	{
		Core_Event::notify(get_class($this) . '.onBeforeUninstall', $this);

		return $this;
	}
}