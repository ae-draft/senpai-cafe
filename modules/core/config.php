<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Module configurations
 *
 * @package HostCMS 6\Core
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Config
{
	/**
	 * Loaded values
	 * @var array
	 */
	private $_values = array();

	/**
	 * The singleton instance.
	 * @var mixed
	 */
	static private $_instance = NULL;

	/**
	 * Register an existing instance as a singleton.
	 * @return object
	 */
	static public function instance()
	{
		if (!isset(self::$_instance))
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Get config, e.g. 'Core_DataBase' requires modules/core/config/database.php
	 * @param string $key
	 * @param mixed $defaultValue
	 * @return mixed Config or NULL
	 */
	public function get($key, $defaultValue = NULL)
	{
		$key = strtolower($key);
		$key = basename($key);

		if (!isset($this->_values[$key]))
		{
			$aConfig = explode('_', $key);

			$sFileName = array_pop($aConfig);

			$path = Core::$modulesPath;
			$path .= implode(DIRECTORY_SEPARATOR, $aConfig) . DIRECTORY_SEPARATOR;
			$path .= 'config' . DIRECTORY_SEPARATOR . $sFileName . '.php';

			$path = Core_File::pathCorrection($path);

			$this->_values[$key] = is_file($path)
				? require_once($path)
				: $defaultValue;
		}

		return $this->_values[$key];
	}

	/**
	 * Set config value
	 * @param string $key Config key
	 * @param string $value Config value
	 * @return Core_Config
	 */
	/*public function set($key, $value)
	{
		$this->_values[$key] = $value;
		return $this;
	}*/
}