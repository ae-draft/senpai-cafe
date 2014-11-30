<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Filemanager.
 *
 * @package HostCMS 6\Wysiwyg
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Wysiwyg_Filemanager_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Get file path
	 * @return string
	 */
	protected function _getFilePath()
	{
		return CMS_FOLDER . Core_File::pathCorrection(
			Core_Array::getRequest('cdir') . (!is_null(Core_Array::getRequest('dir')) ? Core_Array::getRequest('dir') . DIRECTORY_SEPARATOR : '')
			. Core_File::convertfileNameToLocalEncoding($this->_object->name));
	}

	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		//parent::setObject($object);
		$this->_object = $object;

		$oMainTab = Admin_Form_Entity::factory('Tab')
			->caption('main')
			->name('main');

		$this->addTab($oMainTab);

		$title = Core::_('Wysiwyg_Filemanager.edit_file', $object->name);

		$filePath = $this->_getFilePath();

		if (!is_file($filePath))
		{
			throw new Core_Exception('File %file not found', array('%file' => $this->_object->name));
		}

		$oFile_Content = Admin_Form_Entity::factory('Textarea')
			->value(
				Core_File::read($filePath)
			)
			->caption(Core::_('Wysiwyg_Filemanager.edit_file_text'))
			->name('text')
			->rows(20);

		$oMainTab->add($oFile_Content);

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Wysiwyg_Filemanager_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		$filePath = $this->_getFilePath();

		$content = Core_Array::getPost('text');
		if (!is_null($content))
		{
			Core_File::write($filePath, $content);
		}

		//parent::_applyObjectProperty();
		
		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}
}