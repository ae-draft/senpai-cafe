<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Information systems.
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Dir_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var mixed
	 */
	public $img = 0;
	
	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'informationsystem' => array(),
		'informationsystem_dir' => array('foreign_key' => 'parent_id')
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'informationsystem_dir' => array('foreign_key' => 'parent_id'),
		'site' => array(),
		'user' => array()
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'parent_id' => 0
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
			$this->_preloadValues['site_id'] = defined('CURRENT_SITE') ? CURRENT_SITE : 0;
		}
	}

	/**
	 * Get parent comment
	 * @return Informationsystem_Dir_Model|NULL
	 */
	public function getParent()
	{
		if ($this->parent_id)
		{
			return Core_Entity::factory('Informationsystem_Dir', $this->parent_id);
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

		// Удаление информационных систем
		$aInformationsystems = $this->Informationsystems->findAll(FALSE);
		foreach($aInformationsystems as $oInformationsystem)
		{
			$oInformationsystem->delete();
		}

		// Удаление дочерних разделов
		$aInformationsystem_Dirs = $this->Informationsystem_Dirs->findAll(FALSE);
		foreach($aInformationsystem_Dirs as $oInformationsystem_Dir)
		{
			$oInformationsystem_Dir->delete();
		}

		return parent::delete($primaryKey);
	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		$newObject = parent::copy();

		$aChildrenDirs = $this->Informationsystem_Dirs->findAll();
		foreach($aChildrenDirs as $oChildrenDir)
		{
			$newObject->add($oChildrenDir->copy());
		}

		$aInformationsystems = $this->Informationsystems->findAll();
		foreach($aInformationsystems as $oInformationsystem)
		{
			$newObject->add($oInformationsystem->copy());
		}

		return $newObject;
	}
}