<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Libs.
 *
 * @package HostCMS 6\Lib
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Lib_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var int
	 */
	public $img = 1;

	/**
	 * Backend property
	 * @var int
	 */
	public $properties = 1;

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'lib_dir' => array(),
		'user' => array()
	);

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'lib_property' => array()
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
	}

	/**
	 * Get lib's directory path
	 * @return string
	 */
	public function getLibPath()
	{
		return CMS_FOLDER . "hostcmsfiles/lib/lib_" . intval($this->id) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get lib file path
	 * @return string
	 */
	public function getLibFilePath()
	{
		return $this->getLibPath() . "lib_" . intval($this->id) . ".php";
	}

	/**
	 * Get configuration file path
	 * @return string
	 */
	public function getLibConfigFilePath()
	{
		return $this->getLibPath() . "lib_config_" . intval($this->id) . ".php";
	}

	/**
	 * Get dat file path
	 * @param int $structure_id structure id
	 * @return string
	 */
	public function getLibDatFilePath($structure_id)
	{
		return $this->getLibPath() . "lib_values_" . intval($structure_id) . ".dat";
	}

	/**
	 * Save dat file
	 * @param array $array data
	 * @param int $structure_id structure id
	 */
	public function saveDatFile(array $array, $structure_id)
	{
		$this->save();

		$sLibDatFilePath = $this->getLibDatFilePath($structure_id);
		Core_File::mkdir(dirname($sLibDatFilePath), CHMOD, TRUE);

		foreach ($array as $key => $value)
		{
			if (strtolower($value) == "false")
			{
				$values[$key] = FALSE;
			}
			elseif (strtolower($value) == "true")
			{
				$values[$key] = TRUE;
			}
		}

		$content = strval(serialize($array));
		Core_File::write($sLibDatFilePath, $content);
	}

	/**
	 * Get array for unserialized dat-file
	 * @param int $structure_id structure id
	 * @return array
	 */
	public function getDat($structure_id)
	{
		$datContent = $this->loadDatFile($structure_id);
		if ($datContent)
		{
			$array = @unserialize(strval($datContent));
			return Core_Type_Conversion::toArray($array);
		}
		return array();
	}

	/**
	 * Read dat file content
	 * @param int $structure_id structure id
	 * @return string|NULL
	 */
	public function loadDatFile($structure_id)
	{
		$path = $this->getLibDatFilePath($structure_id);

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
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Core_Entity
	 */
	public function delete($primaryKey = NULL)
	{
		if (is_null($primaryKey))
		{
			$primaryKey = $this->getPrimaryKey();
		}

		$this->id = $primaryKey;

		// Удаляем код и настройки
		try
		{
			Core_File::delete($this->getLibFilePath());
		} catch (Exception $e) {}

		try
		{
			Core_File::delete($this->getLibConfigFilePath());
		} catch (Exception $e) {}

		try
		{
			Core_File::deleteDir($this->getLibPath());
		} catch (Exception $e) {}

		$aLibProperties = $this->Lib_Properties->findAll();
		foreach($aLibProperties as $oLibProperty)
		{
			$oLibProperty->delete();
		}

		return parent::delete($primaryKey);
	}

	/**
	 * Save lib content
	 * @param string $content content
	 */
	public function saveLibFile($content)
	{
		$this->save();

		Core_File::mkdir(dirname($sLibFilePath = $this->getLibFilePath()), CHMOD, TRUE);

		$content = trim($content);
		Core_File::write($sLibFilePath, $content);
	}

	/**
	 * Save config content
	 * @param string $content content
	 */
	public function saveLibConfigFile($content)
	{
		$this->save();

		Core_File::mkdir(dirname($sLibConfigFilePath = $this->getLibConfigFilePath()), CHMOD, TRUE);

		$content = trim($content);
		Core_File::write($sLibConfigFilePath, $content);
	}

	/**
	 * Get lib file content
	 * @return string|NULL
	 */
	public function loadLibFile()
	{
		$path = $this->getLibFilePath();

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
	 * Get config file content
	 * @return string|NULL
	 */
	public function loadLibConfigFile()
	{
		$path = $this->getLibConfigFilePath();

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
	 * Executes the business logic.
	 * @hostcms-event lib.onBeforeExecute
	 * @hostcms-event lib.onAfterExecute
	 */
	public function execute()
	{
		Core_Event::notify($this->_modelName . '.onBeforeExecute', $this);

		include $this->getLibFilePath();

		Core_Event::notify($this->_modelName . '.onAfterExecute', $this);

		return $this;
	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		$newObject = parent::copy();

		try
		{
			Core_File::copy($this->getLibFilePath(), $newObject->getLibFilePath());
		} catch (Exception $e) {}

		try
		{
			Core_File::copy($this->getLibConfigFilePath(), $newObject->getLibConfigFilePath());
		} catch (Exception $e) {}

		$aLibProperties = $this->lib_properties->findAll();

		foreach($aLibProperties as $oLibProperty)
		{
			$newObject->add($oLibProperty->copy());
		}

		return $newObject;
	}
}