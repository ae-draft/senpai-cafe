<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Modules.
 *
 * @package HostCMS 6\Module
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Module_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$title = $this->_object->id
			? Core::_('Module.modules_edit_form_title')
			: Core::_('Module.modules_add_form_title');

		$oMainTab = $this->getTab('main');

		$this->getField('active')
			->divAttr(array('style' => 'float: left'));

		// Объект вкладки 'Настройки модуля'
		$oSettingsTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Module.tab_parameters'))
			->name('parameters');

		// Добавляем вкладку выпадающий список
		$this->addTabAfter($oSettingsTab, $oMainTab);

		// Создаем текстовое поле "PHP-код с параметрами модуля"
		$oParameters = Admin_Form_Entity::factory('Textarea');

		$oParameters
			->value(
				$this->_object->loadConfigFile()
			)
			->cols(140)
			->rows(30)
			->caption(Core::_('Module.modules_add_form_params'))
			->name('parameters');

		// Добавляем на вкладку 'Настройки модуля' большое текстовое поле "PHP-код с параметрами модуля"
		$oSettingsTab->add($oParameters);

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Module_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		$oldActive = $this->_object->active;
		parent::_applyObjectProperty();

		if ($oldActive != $this->_object->active)
		{
			$this->_object->setupModule();
		}

		$this->_object->saveConfigFile(Core_Array::getPost('parameters'));

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}
}
