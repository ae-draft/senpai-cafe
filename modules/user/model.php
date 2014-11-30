<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Administration center users.
 *
 * @package HostCMS 6\User
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class User_Model extends Core_Entity
{
	/**
	 * Column consist item's name
	 * @var string
	 */
	protected $_nameColumn = 'login';

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'user_group' => array(),
		'user' => array(),
		'user_module' => array()
	);

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'user_note' => array(),
		'user_setting' => array(),
		'admin_form_setting' => array(),
		'informationsystem_dir' => array(),
		'informationsystem' => array(),
		'forum' => array(),
		'forum_group' => array(),
		'forum_category_siteuser_group' => array(),
		'forum_category' => array(),
		'forum_topic' => array(),
		'helpdesk' => array(),
		'helpdesk_category' => array(),
		'helpdesk_attachment' => array(),
		'helpdesk_message' => array(),
		'helpdesk_account' => array(),
		'helpdesk_holiday' => array(),
		'helpdesk_ticket' => array(),
		'helpdesk_ticket_flag' => array(),
		'helpdesk_status' => array(),
		'helpdesk_criticality_level' => array(),
		'helpdesk_responsible_user' => array(),
		'helpdesk_user_letter_template' => array(),
		'helpdesk_responsible_user_second' => array(
			'foreign_key' => 'responsible_user_id',
			'model' => 'Helpdesk_Responsible_User'
		),
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'superuser' => 1,
		'only_access_my_own' => 0,
		'read_only' => 0,
		'settings' => 0
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
	 * Get user by login and password
	 * @param string $login login
	 * @param string $password password
	 * @return User_Model|NULL
	 */
	public function getByLoginAndPassword($login, $password)
	{
		$this->queryBuilder()
			->clear()
			->where('login', '=', $login)
			->where('password', '=', Core_Hash::instance()->hash($password))
			->limit(1);

		$aUsers = $this->findAll(FALSE);

		return isset($aUsers[0]) ? $aUsers[0] : NULL;
	}

	/**
	 * Get current user
	 * @return User_Model|NULL
	 */
	public function getCurrent()
	{
		if (isset($_SESSION['current_users_id']))
		{
			$oUser = $this->find(intval($_SESSION['current_users_id']));

			if (!is_null($oUser->id))
			{
				return $oUser;
			}
		}

		return NULL;
	}

	/**
	 * Get user by name
	 * @param string $name name
	 * @return User_Model|NULL
	 */
	public function getByName($name)
	{
		$this->queryBuilder()
			->clear()
			->where('name', '=', $name)
			->limit(1);

		$aUsers = $this->findAll();

		if (isset($aUsers[0]))
		{
			return $aUsers[0];
		}

		return NULL;
	}

	/**
	 * Check if user has access to site
	 * @param Site_Model $oSite site
	 * @return boolean
	 */
	public function checkSiteAccess(Site_Model $oSite)
	{
		$oUser_Module = $this->User_Group->User_Modules;

		$oUser_Module
			->queryBuilder()
			->where('site_id', '=', $oSite->id)
			->limit(1);

		$aUser_Modules = $oUser_Module->findAll();

		return count($aUser_Modules) == 1;
	}

	/**
	 * Check if user has access to module
	 * @param string $aModuleNames module name
	 * @param Site_Model $oSite
	 * @return boolean
	 */
	public function checkModuleAccess($aModuleNames, Site_Model $oSite)
	{
		foreach ($aModuleNames as $sModuleName)
		{
			$oModule = Core_Entity::factory('Module')->getByPath($sModuleName);

			if (is_null($oModule))
			{
				throw new Core_Exception("Module '%s' does not exsit",
					array('%s' => $sModuleName), 0, $bShowDebugTrace = FALSE
				);
			}

			if ($oModule->active != 1)
			{
				throw new Core_Exception(Core::_('Core.error_log_module_disabled'),
					array('%s' => $sModuleName), 0, $bShowDebugTrace = FALSE
				);
			}

			$access = $this->User_Group->issetModuleAccess(
				$oModule, $oSite
			);

			// Если доступ разрешен
			if ($access)
			{
				// Прерываем проверку, т.к. доступ хотя бы к одному из указанных модулей уже есть
				return TRUE;
			}
		}

		// Вынесено после проверки прав доступа, т.к. идет отдельная проверка на активность модуля
		if ($this->superuser == 1)
		{
			// SU разрешен доступ ко всем модулям
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Check user access to object
	 * @param Core_Entity $oObject object
	 * @return boolean
	 */
	public function checkObjectAccess(Core_Entity $oObject)
	{
		if ($this->read_only)
		{
			return FALSE;
		}

		if ($this->superuser == 1 || $this->only_access_my_own == 0)
		{
			return TRUE;
		}

		$aTableColumns = $oObject->getTableColums();

		// Объект имеет поле user_id
		if (isset($aTableColumns['user_id']))
		{
			return ($oObject->user_id == 0 || $oObject->user_id == $this->id);
		}

		return FALSE;
	}

	/**
	 * Get allowed sites for user
	 * @return array
	 */
	public function getSites()
	{
		$oSite = Core_Entity::factory('Site');

		if ($this->superuser == 0)
		{
			$oSite->queryBuilder()
				->select('sites.*')
				->join('user_modules', 'sites.id', '=', 'user_modules.site_id')
				->where('user_modules.user_group_id', '=', $this->user_group_id)
				->groupBy('sites.id');
		}

		return $oSite->findAll();
	}

	/**
	 * Mark entity as deleted
	 * @return Core_Entity
	 */
	public function markDeleted()
	{
		$oCurrentUser = Core_Entity::factory('User', 0)->getCurrent();
		if (!$oCurrentUser || $oCurrentUser->id != $this->id)
		{
			parent::markDeleted();
		}
		return $this;
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

		$this->User_Notes->deleteAll(FALSE);
		$this->User_Settings->deleteAll(FALSE);
		$this->Admin_Form_Settings->deleteAll(FALSE);

		// Helpdesks
		if (Core::moduleIsActive('helpdesk'))
		{
			$this->Helpdesk_Responsible_User_Seconds->deleteAll(FALSE);
			$this->Helpdesk_User_Letter_Templates->deleteAll(FALSE);
		}

		return parent::delete($primaryKey);
	}
}