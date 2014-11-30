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
class Document_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$modelName = $this->_object->getModelName();

		$oMainTab = $this->getTab('main');
		$oAdditionalTab = $this->getTab('additional');
		$oSelect_Dirs = Admin_Form_Entity::factory('Select');

		switch($modelName)
		{
			case 'document':
				$title = $this->_object->id
					? Core::_('Document.edit')
					: Core::_('Document.add');

				if (is_null($this->_object->id))
				{
					$this->_object->document_dir_id = Core_Array::getGet('document_dir_id');
				}

				$oDocument_Version_Current = $this->_object->Document_Versions->getCurrent(FALSE);

				$oTextarea_Document = Admin_Form_Entity::factory('Textarea')
					->value(
						!is_null($oDocument_Version_Current)
							? $oDocument_Version_Current->loadFile()
							: ''
					)
					//->cols(140)
					->rows(15)
					->style('height: 500px; width: 100%')
					->caption(Core::_('Document_Version.text'))
					->name('text')
					->wysiwyg(TRUE)
					->template_id(!is_null($oDocument_Version_Current)
						? $oDocument_Version_Current->template_id
						: 0);

				$oMainTab->addAfter($oTextarea_Document, $this->getField('name'));

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

				// Объект вкладки 'Атрибуты документа'
				$oAttrTab = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Document.tab_1'))
					->name('tab_1');

				$this->addTabAfter($oAttrTab, $oMainTab);

				// Удаляем стандартный <input>
				$oAdditionalTab->delete($this->getField('document_dir_id'));

				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillDocumentDir(CURRENT_SITE, 0)
					)
					->name('document_dir_id')
					->value($this->_object->document_dir_id)
					->caption(Core::_('Document.document_dir_id'))
					->divAttr(array('style' => 'float: left'))
					->style('width: 320px');

				$oAttrTab->add($oSelect_Dirs);

				// Выбор макета
				$Template_Controller_Edit = new Template_Controller_Edit($this->_Admin_Form_Action);

				$aTemplateOptions = $Template_Controller_Edit->fillTemplateList($this->_object->site_id);

				// Warning: TO DO: dynamic chain list template_dir -> template like Documents
				$oSelect_Template_Id = Admin_Form_Entity::factory('Select')
					->options(
						count($aTemplateOptions) ? $aTemplateOptions : array(' … ')
					)
					->name('template_id')
					->value(
						!is_null($oDocument_Version_Current)
						? $oDocument_Version_Current->template_id
						: 0
					)
					->caption(Core::_('Document_Version.template_id'))
					->divAttr(array('style' => 'float: left'))
					->style('width: 320px');

				$oAttrTab
					->add($oSelect_Template_Id)
					->add(Admin_Form_Entity::factory('Separator'));

				// Статус документа
				$oAdditionalTab
					->delete($this->getField('document_status_id'));

				$Document_Status_Controller_Edit = new Document_Status_Controller_Edit($this->_Admin_Form_Action);

				$oSelect_Statuses = Admin_Form_Entity::factory('Select')
					->options(
						array(' … ') + $Document_Status_Controller_Edit->fillDocumentStatus(CURRENT_SITE)
					)
					->name('document_status_id')
					->value($this->_object->document_status_id)
					->caption(Core::_('Document.document_status_id'))
					->divAttr(array('style' => 'float: left'))
					->style('width: 320px');

				$oAttrTab->add($oSelect_Statuses);

				// Текушая версия
				$oInput_Current = Admin_Form_Entity::factory('Checkbox')
					->name('current')
					->value(1)
					->caption(Core::_('Document_Version.current'))
					;

				if (is_null($oDocument_Version_Current) || $oDocument_Version_Current->current)
				{
					$oInput_Current->checked('checked');
				}

				$oAttrTab
					->add(Admin_Form_Entity::factory('Separator'))
					->add($oInput_Current);

				$oTextarea_Description = Admin_Form_Entity::factory('Textarea')
					->value(
						!is_null($oDocument_Version_Current)
							? $oDocument_Version_Current->description
							: ''
					)
					//->cols(140)
					->rows(15)
					->caption(Core::_('Document_Version.description'))
					->name('description');

				$oAttrTab
					->add($oTextarea_Description);

			break;
			case 'document_dir':
			default:
				$title = $this->_object->id
						? Core::_('Document_Dir.edit_title')
						: Core::_('Document_Dir.add_title');

				// Значения директории для добавляемого объекта
				if (is_null($this->_object->id))
				{
					$this->_object->parent_id = Core_Array::getGet('document_dir_id');
				}

				// Удаляем стандартный <input>
				$oAdditionalTab->delete(
					 $this->getField('parent_id')
				);

				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillDocumentDir(CURRENT_SITE, 0, $this->_object->id)
					)
					->name('parent_id')
					->value($this->_object->parent_id)
					->caption(Core::_('Document_Dir.parent_id'));

				$oMainTab->addAfter($oSelect_Dirs,  $this->getField('name'));
			break;
		}

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Document_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		parent::_applyObjectProperty();

		$modelName = $this->_object->getModelName();

		switch($modelName)
		{
			case 'document':
				$text = Core_Array::getPost('text');

				if (Core::moduleIsActive('typograph') && Core_Array::getPost('use_typograph'))
				{
					$text = Typograph_Controller::instance()->process($text, Core_Array::getPost('use_trailing_punctuation'));
				}

				$oNewDocument_Version = Core_Entity::factory('Document_Version');
				$oNewDocument_Version->description = Core_Array::getPost('description');
				$oNewDocument_Version->template_id = intval(Core_Array::getPost('template_id'));
				$oNewDocument_Version->current = Core_Array::getPost('current');
				$oNewDocument_Version->saveFile($text);
				$this->_object->add($oNewDocument_Version);

				if ($oNewDocument_Version->current)
				{
					$oNewDocument_Version->setCurrent();
				}
			break;
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}

	/**
	 * Create visual tree of the document directories
	 * @param int $iSiteId site ID
	 * @param int $iDocumentDirParentId initial directory
	 * @param boolean $bExclude exclude group ID
	 * @param int $iLevel current nesting level
	 * @return array
	 */
	public function fillDocumentDir($iSiteId, $iDocumentDirParentId = 0, $bExclude = FALSE, $iLevel = 0)
	{
		$iSiteId = intval($iSiteId);
		$iDocumentDirParentId = intval($iDocumentDirParentId);
		$iLevel = intval($iLevel);

		$oDocument_Dir = Core_Entity::factory('Document_Dir', $iDocumentDirParentId);

		$aReturn = array();

		// Дочерние разделы
		$childrenDirs = $oDocument_Dir->Document_Dirs->getBySiteId($iSiteId);

		if (count($childrenDirs))
		{
			foreach ($childrenDirs as $childrenDir)
			{
				if ($bExclude != $childrenDir->id)
				{
					$aReturn[$childrenDir->id] = str_repeat('  ', $iLevel) . $childrenDir->name;
					$aReturn += $this->fillDocumentDir($iSiteId, $childrenDir->id, $bExclude, $iLevel+1);
				}
			}
		}

		return $aReturn;
	}
}