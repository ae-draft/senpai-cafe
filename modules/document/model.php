<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Documents.
 *
 * @package HostCMS 6\Document
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Document_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var mixed
	 */
	public $img = 1;

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'document_version' => array()
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'document_dir' => array(),
		'document_status' => array(),
		'user' => array(),
		'site' => array()
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
	 * Delete old version of document
	 */
	public function deleteOldVersions()
	{
		$oDocument_Versions = $this->Document_Versions->findAll();

		foreach ($oDocument_Versions as $oDocument_Version)
		{
			if ($oDocument_Version->current == 0)
			{
				$oDocument_Version->markDeleted();
			}
		}
	}

	/**
	 * Get document by site id
	 * @param int $site_id site id
	 * @return array
	 */
	public function getBySiteId($site_id)
	{
		$this->queryBuilder()
			//->clear()
			->where('site_id', '=', $site_id)
			->orderBy('name');

		return $this->findAll();
	}

	/**
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Document_Model
	 */
	public function delete($primaryKey = NULL)
	{
		if (is_null($primaryKey))
		{
			$primaryKey = $this->getPrimaryKey();
		}

		$this->id = $primaryKey;

		$aDocument_Versions = $this->Document_Versions->findAll();
		foreach($aDocument_Versions as $oDocument_Version)
		{
			$oDocument_Version->delete();
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

		$oCurrent_Version = $this->Document_Versions->getCurrent(FALSE);
		if ($oCurrent_Version)
		{
			$oNewCurrent_Version = $oCurrent_Version->copy();
			$newObject->add($oNewCurrent_Version);
		}

		return $newObject;
	}

	/**
	 * Backend callback method
	 * @return string
	 */
	public function adminTemplate()
	{
		$oDocument_Version = $this->Document_Versions->getCurrent(FALSE);
		if (!is_null($oDocument_Version))
		{
			return $oDocument_Version->Template->name;
		}
	}

	/**
	 * Edit-in-Place callback
	 * @param string $text Text of document verison
	 * @return self
	 */
	public function editInPlaceVersion($text)
	{
		$oNewDocument_Version = Core_Entity::factory('Document_Version');

		$oDocument_Version_Current = $this->Document_Versions->getCurrent(FALSE);

		!is_null($oDocument_Version_Current) && $oNewDocument_Version->description = $oDocument_Version_Current->description;
		!is_null($oDocument_Version_Current) && $oNewDocument_Version->template_id = $oDocument_Version_Current->template_id;
		$oNewDocument_Version->saveFile($text);
		$this->add($oNewDocument_Version);
		$oNewDocument_Version->setCurrent();

		return $this;
	}
}