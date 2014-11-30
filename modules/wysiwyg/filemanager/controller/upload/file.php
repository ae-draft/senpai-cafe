<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Filemanager.
 *
 * @package HostCMS 6\Wysiwyg
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Wysiwyg_Filemanager_Controller_Upload_File extends Admin_Form_Action_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'cdir',
		'file'
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
	 * @return boolean
	 */
	public function execute($operation = NULL)
	{
		if (is_null($this->cdir))
		{
			throw new Core_Exception('cdir is NULL.');
		}

		if (is_null($this->file))
		{
			throw new Core_Exception('file is NULL.');
		}

		if (isset($this->file['name']))
		{
			$target = CMS_FOLDER . $this->cdir . Core_File::filenameCorrection(Core_File::convertfileNameToLocalEncoding($this->file['name']));

			Core_File::moveUploadedFile($this->file['tmp_name'], $target);
		}
		return FALSE;
	}
}