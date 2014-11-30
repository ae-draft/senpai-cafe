<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Tags.
 *
 * @package HostCMS 6\Tag
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Tag_Dir_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var string
	 */
	public $img = 0;
	
	/**
	 * Backend property
	 * @var string
	 */
	public $site_count = '';
	
	/**
	 * Backend property
	 * @var string
	 */
	public $all_count = '';

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'tag' => array(),
		'tag_dir' => array('foreign_key' => 'parent_id')
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array('sorting' => 0);

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

		$aTags = $this->Tags->findAll();

		foreach($aTags as $oTag)
		{
			$oTag->delete();
		}

		return parent::delete();
	}

	/**
	 * Get parent comment
	 * @return Tag_Dir_Model|NULL
	 */
	public function getParent()
	{
		if ($this->parent_id)
		{
			return Core_Entity::factory('Tag_Dir', $this->parent_id);
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Get dir by name
	 * @param string $name
	 * @return Tag_Dir_Model
	 */
	public function getByName($name)
	{
		$this->queryBuilder()
			->clear()
			->where('name', '=', $name)
			->limit(1);

		return $this->find();
	}

	/**
	 * Move dir to another
	 * @param int $tag_dir_id dir id
	 * @return self
	 */
	public function move($tag_dir_id)
	{
		$oDestinationDir = Core_Entity::factory('Tag_Dir', $tag_dir_id);

		do
		{
			if ($oDestinationDir->parent_id == $this->id
				|| $oDestinationDir->id == $this->id)
			{
				// Группа назначения является потомком текущей группы, перенос невозможен
				return $this;
			}
		} while($oDestinationDir = $oDestinationDir->getParent());

		$this->parent_id = $tag_dir_id;
		$this->save();
		return $this;
	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		$newObject = parent::copy();

		// Копируем права доступа группы пользователей к модулю
		$aAllRelatedUserModules = $this->User_Modules->findAll();

		$oUser = Core_Entity::factory('User')->getCurrent();
		$user_id = is_null($oUser) ? 0 : $oUser->id;

		foreach ($aAllRelatedUserModules as $oUserModule)
		{
			$oNewUserModule = clone $oUserModule;
			$oNewUserModule->user_id = $user_id;
			$newObject->add($oNewUserModule);
		}

		// Копируем права доступа группы пользователей к действиям
		$aAllRelatedUserGroupActionAccesses = $this->User_Group_Action_Access->findAll();

		foreach ($aAllRelatedUserGroupActionAccesses as $oUserGroupActionAccess)
		{
			$oNewUserGroupActionAccess = clone $oUserGroupActionAccess;
			$oNewUserGroupActionAccess->user_id = $user_id;
			$newObject->add($oNewUserGroupActionAccess);
		}

		return $newObject;
	}
}