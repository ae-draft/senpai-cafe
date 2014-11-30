<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * XSL.
 *
 * @package HostCMS 6\Xsl
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Xsl_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var int
	 */
	public $img = 1;

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'xsl_dir' => array()
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'format' => 1, 'sorting' => 0
	);

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'xsls.sorting' => 'ASC',
		'xsls.name' => 'ASC'
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
	 * Get XSL file path
	 * @return string
	 */
	public function getXslFilePath()
	{
		return CMS_FOLDER . "hostcmsfiles/xsl/" . intval($this->id) . ".xsl";
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

		// Удаляем файл
		$filename = $this->getXslFilePath();

		try
		{
			Core_File::delete($filename);
		} catch (Exception $e) {}

		return parent::delete($primaryKey);
	}

	/**
	 * Specify XSL file content
	 * @param string $content content
	 */
	public function saveXslFile($content)
	{
		$this->save();

		$content = trim($content);
		Core_File::write($this->getXslFilePath(), $content);
	}

	/**
	 * Get XSL file content
	 * @return string|NULL
	 */
	public function loadXslFile()
	{
		$path = $this->getXslFilePath();

		if (is_file($path))
		{
			return Core_File::read($path);
		}

		return NULL;
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
			Core_File::copy($this->getXslFilePath(), $newObject->getXslFilePath());
		}
		catch (Exception $e) {}

		return $newObject;
	}

	/**
	 * Get XSL by name
	 * @param string $name name
	 * @return Xsl_Model|NULL
	 */
	public function getByName($name)
	{
		$this->queryBuilder()
			->clear()
			->where('name', '=', $name)
			->limit(1);

		$aXsls = $this->findAll();

		return isset($aXsls[0]) ? $aXsls[0] : NULL;
	}
}