<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Internationalization
 *
 * @package HostCMS 6\Core
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_I18n
{
	/**
	 * Current language
	 * @var string
	 */
	protected $_lng = NULL;

	/**
	 * Default language
	 * @var string
	 */
	protected $_defaultLng = 'ru';

	/**
	 * The singleton instance.
	 * @var mixed
	 */
	static protected $_instance;

	/**
	 * Cache
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * Set target language
	 * @param string $lng language short name
	 * @return self
	 */
	public function setLng($lng)
	{
		$this->_lng = strtolower($lng);
		return $this;
	}

	/**
	 * Get current language
	 */
	public function getLng()
	{
		return $this->_lng;
	}

	/**
	 * Constructor.
	 */
	protected function __construct()
	{
		defined('DEFAULT_LNG') && $this->_defaultLng = DEFAULT_LNG;

		if (!empty($_SESSION['current_lng']))
		{
			$this->setLng(strval($_SESSION['current_lng']));
		}
		elseif (defined('CURRENT_LNG'))
		{
			$this->setLng(CURRENT_LNG);
		}
		else
		{
			$this->setLng($this->_defaultLng);
		}
	}

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
	 * Get text for key
	 *
	 * <code>
	 * $value = Core_I18n::instance()->get('Constant.menu');
	 * </code>
	 *
	 * <code>
	 * $value = Core_I18n::instance()->get('Constant.menu', 'en');
	 * </code>
	 *
	 * Be careful when using short alias, function Core::_() has different parameters.
	 * <code>
	 * $value = Core::_('Constant.menu');
	 * </code>
	 * @param string $key module name dot key name, e.g. 'Constant.menu'
	 * @param mixed $lng language, default NULL
	 * @param mixed $count default NULL
	 * @return string
	 */
	public function get($key, $lng = NULL, $count = NULL)
	{
		$aKey = explode('.', $key, 2);

		if (is_null($lng))
		{
			$lng = $this->getLng();
		}

		if (count($aKey) == 2)
		{
			list($className, $textName) = $aKey;

			$className = strtolower($className);
			$className = basename($className);

			if ($className == '')
			{
				//throw new Core_Exception(, array('%key' => $textName, '%language' => $lng));
				return "Error! model name is empty.";
			}

			$textName = basename($textName);
			$lng = basename($lng);

			if (!isset($this->_cache[$lng][$className]))
			{
				$this->_cache[$lng][$className] = $this->getLngFile($className, $lng);
			}

			if (isset($this->_cache[$lng][$className][$textName]))
			{
				return $this->_cache[$lng][$className][$textName];
			}
			/*
			// Warning: Temporary switch off
			elseif ($lng != $this->_defaultLng)
			{
				return $this->get($key, $this->_defaultLng);
			}
			*/
			else
			{
				//throw new Core_Exception(, array('%key' => $textName, '%language' => $lng));
				return "Key '" . htmlspecialchars($textName) . "' in '" . htmlspecialchars($lng) . "' language does not exist for model '" . htmlspecialchars($className) . "'.";
			}
		}
		else
		{
			//throw new Core_Exception("Wrong argument '%key'", array('%key' => $key));
			return "Wrong argument '" . htmlspecialchars($key) . "'.";
		}

		return NULL;
	}

	/**
	 * Include lng file
	 * @param string $className class name
	 * @param string $lng language name
	 * @return array
	 */
	public function getLngFile($className, $lng)
	{
		$className = strtolower($className);

		$className = basename($className);
		$lng = basename($lng);

		$aPath = explode('_', $className);

		$path = Core::$modulesPath;

		$path .= implode(DIRECTORY_SEPARATOR, $aPath) . DIRECTORY_SEPARATOR;

		$path .= 'i18n' . DIRECTORY_SEPARATOR . $lng . '.php';

		$path = Core_File::pathCorrection($path);

		if (is_file($path))
		{
			return require($path);
		}

		throw new Core_Exception("Language file '%className' with path '%path' does not exist.",
			array('%className' => $className, '%path' => Core_Exception::cutRootPath($path)));
	}
}
