<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Documents.
 *
 * @package HostCMS 6\Document
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Document_Version_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		$this
			->addSkipColumn('id')
			->addSkipColumn('datetime');

		if (is_null($object->id))
		{
			$object->document_id = intval(Core_Array::getGet('document_id'));
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');

		// Объект вкладки 'Атрибуты документа'
		$oAttrTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Document.tab_1'))
			->name('tab_1');

		$this->addTabAfter($oAttrTab, $oMainTab);

		$title = $this->_object->id
			? Core::_('Document_Version.edit')
			: Core::_('Document_Version.add');

		if (is_null($this->_object->id))
		{
			$this->_object->document_id = Core_Array::getGet('document_id');
		}

		$oDocument_Name = Admin_Form_Entity::factory('Input')
			->value(
				$this->_object->Document->Name
			)
			->caption(Core::_('Document.name'))
			->name('name')
			->class('large');

		$oMainTab->add($oDocument_Name);

		$oTextarea_Document = Admin_Form_Entity::factory('Textarea')
			->value(
				!is_null($this->_object->id)
					? $this->_object->loadFile()
					: ''
			)
			//->cols(140)
			->rows(20)
			->caption(Core::_('Document_Version.text'))
			->name('text')
			->wysiwyg(TRUE)
			->template_id($this->_object->template_id);

		// Добавляем на основную вкладку большое текстовое поле с кодом XSL-шаблона
		// после выпадающего списка разделов XSL
		$oMainTab->addAfter($oTextarea_Document, $oDocument_Name);

		if (Core::moduleIsActive('typograph'))
		{
			$oTextarea_Document->value(
				Typograph_Controller::instance()->eraseOpticalAlignment($oTextarea_Document->value)
			);

			$oUseTypograph = Admin_Form_Entity::factory('Checkbox');
			$oUseTypograph
				->name("use_typograph")
				->caption(Core::_('Document.use_typograph'))
				->value(1)
				->divAttr(array('style' => 'float: left;'));

			$oUseTrailingPunctuation = Admin_Form_Entity::factory('Checkbox');
			$oUseTrailingPunctuation
				->name("use_trailing_punctuation")
				->caption(Core::_('Document.use_trailing_punctuation'))
				->value(1)
				->divAttr(array('style' => 'float: left;'));

			$oMainTab
				->addAfter($oUseTypograph, $oTextarea_Document)
				->addAfter($oUseTrailingPunctuation, $oUseTypograph)
				->addAfter(Admin_Form_Entity::factory('Separator'), $oUseTrailingPunctuation);
		}

		// Выбор макета
		$oAdditionalTab->delete($this->getField('template_id'));
		$Template_Controller_Edit = new Template_Controller_Edit($this->_Admin_Form_Action);

		$aTemplateOptions = $Template_Controller_Edit->fillTemplateList($this->_object->Document->site_id);

		// Warning: TO DO: dynamic chain list template_dir -> template like Documents
		$oSelect_Template_Id = Admin_Form_Entity::factory('Select')
			->options(
				count($aTemplateOptions) ? $aTemplateOptions : array(' … ')
			)
			->name('template_id')
			->value(
				!is_null($this->_object->id)
				? $this->_object->template_id
				: 0
			)
			->caption(Core::_('Document_Version.template_id'))
			->divAttr(array('style' => 'float: left'))
			->style('width: 320px');

		$oAttrTab
			->add($oSelect_Template_Id)
			->add(Admin_Form_Entity::factory('Separator'));

		$oMainTab
			->move($this->getField('current'), $oAttrTab)
			->move($this->getField('description'), $oAttrTab);


		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Document_Version_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		// Create new document version
		$this->_object = clone $this->_object;

		parent::_applyObjectProperty();

		$text = Core_Array::getPost('text');

		if (Core::moduleIsActive('typograph') && Core_Array::getPost('use_typograph'))
		{
			$text = Typograph_Controller::instance()->process($text, Core_Array::getPost('use_trailing_punctuation'));
		}

		$this->_object->saveFile($text);
		if ($this->_object->current)
		{
			$this->_object->setCurrent();
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}
}