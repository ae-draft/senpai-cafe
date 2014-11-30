<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Modules.
 *
 * @package HostCMS 6\Module
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Module_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var mixed
	 */
	public $version = NULL;

	/**
	 * Backend property
	 * @var mixed
	 */
	public $date = NULL;

	/**
	 * Backend property
	 * @var Core_Module
	 */
	public $Core_Module = NULL;

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'modules.sorting' => 'ASC'
	);

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;
		}

		$this->_getModuleInformation();
	}

	/**
	 * Get information about module
	 */
	protected function _getModuleInformation()
	{
		if (is_null($this->version) && !is_null($this->path))
		{
			$this->Core_Module = Core_Module::factory($this->path);
			if ($this->Core_Module)
			{
				$this->version = $this->Core_Module->version;
				$this->date = $this->Core_Module->date;
			}
		}
	}

	/**
	 * Find object in database and load one
	 * @param mixed $primaryKey default NULL
	 * @param bool $bCache use cache
	 * @return Core_ORM
	 */
	public function find($primaryKey = NULL, $bCache = TRUE)
	{
		$return = parent::find($primaryKey, $bCache);
		$this->_getModuleInformation();
		return $return;
	}

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'sorting' => 0
	);

	/**
	 * Get path to config file
	 * @return string
	 */
	public function getConfigFilePath()
	{
		$classPath = Core::getClassPath($this->path);
		return Core::$modulesPath . dirname($classPath) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
	}

	/**
	 * Save module config file
	 * @param string $content file content
	 */
	public function saveConfigFile($content)
	{
		$this->save();

		$configFilePath = $this->getConfigFilePath();
		$dir = dirname($configFilePath);
		if (!is_dir($dir))
		{
			Core_File::mkdir($dir, CHMOD, $recursive = TRUE);
		}

		$content = trim($content);
		Core_File::write($configFilePath, $content);
	}

	/**
	 * Load module config file
	 * @return string|NULL
	 */
	public function loadConfigFile()
	{
		$path = $this->getConfigFilePath();

		if (is_file($path))
		{
			return Core_File::read($path);
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Change module status
	 * @return Module_Model
	 */
	public function changeActive()
	{
		$this->active = 1 - $this->active;
		$this->save();

		return $this->setupModule();
	}

	/**
	 * Change indexing status
	 * @return Module_Model
	 */
	public function changeIndexing()
	{
		$this->indexing = 1 - $this->indexing;
		return $this->save();
	}

	/**
	 * Call install() or uninstall() that depends on active
	 */
	public function setupModule()
	{
		$this->active && $this->install();
		!$this->active && $this->uninstall();
		return $this;
	}

	/**
	 * Install module
	 * @return self
	 */
	public function install()
	{
		$path = $this->path . '_Module';
		if (class_exists($path))
		{
			$objectModule = new $path();
			method_exists($objectModule, 'install') && $objectModule->install();
		}

		return $this;
	}

	/**
	 * Uninstall module
	 * @return self
	 */
	public function uninstall()
	{
		$path = $this->path . '_Module';
		if (class_exists($path))
		{
			$objectModule = new $path();
			method_exists($objectModule, 'uninstall') && $objectModule->uninstall();
		}

		return $this;
	}
}