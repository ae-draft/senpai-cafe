<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Templates.
 *
 * @package HostCMS 6\Template
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Template_Dir_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var int
	 */
	public $img = 0;

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'template' => array(),
		'template_dir' => array('foreign_key' => 'parent_id')
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'template_dir' => array('foreign_key' => 'parent_id'),
		'site' => array()
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'sorting' => 0
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

		$aTemplate_Dirs = $this->Template_Dirs->findAll(FALSE);
		foreach($aTemplate_Dirs as $oTemplate_Dir)
		{
			$oTemplate_Dir->delete();
		}

		$aTemplates = $this->Templates->findAll(FALSE);
		foreach($aTemplates as $oTemplate)
		{
			$oTemplate->delete();
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

		$aAllRelatedTemplates = $this->tempaltes->findAll();

		foreach ($aAllRelatedTemplates as $oTemplate)
		{
			$oNewTemplate = $oTemplate->copy();
			$newObject->add($oNewTemplate);
		}

		$aAllRelatedTemplateDirs = $this->template_dirs->findAll();

		foreach ($aAllRelatedTemplateDirs as $oTemplateDir)
		{
			$oNewTemplateDir = $oTemplateDir->copy();
			$newObject->add($oNewTemplateDir);
		}

		return $newObject;
	}

	/**
	 * Get parent comment
	 * @return Template_Dir_Model|NULL
	 */
	public function getParent()
	{
		if ($this->parent_id)
		{
			return Core_Entity::factory('Template_Dir', $this->parent_id);
		}
		return NULL;
	}
}