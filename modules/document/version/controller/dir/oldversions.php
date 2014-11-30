<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Documents.
 * Контроллер удаления нетекущих версий документа
 *
 * @package HostCMS 6\Document
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Document_Version_Controller_Dir_Oldversions extends Admin_Form_Action_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'document_dir_id',
	);

	/**
	 * Constructor.
	 * @param Admin_Form_Action_Model $oAdmin_Form_Action action
	 */
	public function __construct(Admin_Form_Action_Model $oAdmin_Form_Action)
	{
		parent::__construct($oAdmin_Form_Action);
	}

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 * @return self
	 */
	public function execute($operation = NULL)
	{
		if (is_null($this->document_dir_id))
		{
			throw new Core_Exception('document_dir_id is NULL.');
		}
		$aDocuments = Core_Entity::factory('Document_Dir', $this->document_dir_id)->Documents->findAll();
		foreach ($aDocuments as $oDocument)
		{
			$oDocument->deleteOldVersions();
		}
		return $this;
	}
}