<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * XSL.
 *
 * @package HostCMS 6\Xsl
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Xsl_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
			case 'xsl':
				$title = $this->_object->id
					? Core::_('Xsl.edit_title')
					: Core::_('Xsl.add_title');

				if (is_null($this->_object->id))
				{
					$this->_object->xsl_dir_id = Core_Array::getGet('xsl_dir_id');
				}

				// Удаляем стандартный <input>
				$oAdditionalTab->delete(
					 $this->getField('xsl_dir_id')
				);

				// Селектор с группой
				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillXslDir()
					)
					->name('xsl_dir_id')
					->value($this->_object->xsl_dir_id)
					->caption(Core::_('Xsl.xsl_dir_id'));

				$oMainTab->addAfter(
					$oSelect_Dirs, $this->getField('name')
				);

				$oTextarea_Xsl = Admin_Form_Entity::factory('Textarea');

				$oTmpOptions = $oTextarea_Xsl->syntaxHighlighterOptions;
				$oTmpOptions['mode'] = 'xml';
				
				$oTextarea_Xsl
					->value(
						$this->_object->loadXslFile()
					)
					->cols(140)
					->rows(30)
					->caption(Core::_('Xsl.value'))
					->name('xsl_value')
					->syntaxHighlighter(TRUE)
					->syntaxHighlighterOptions($oTmpOptions);

				// Добавляем на основную вкладку большое текстовое поле с кодом XSL-шаблона
				// после выпадающего списка разделов XSL
				$oMainTab->addAfter($oTextarea_Xsl, $oSelect_Dirs);

				// Объект вкладки 'Комментарий'
				$oDescriptionTab = Admin_Form_Entity::factory('Tab')
					->caption(Core::_('Xsl.tab2'))
					->name('tab_xsl_description');

				$this->addTabAfter($oDescriptionTab, $oMainTab);

				// Перемещаем поле "Комментарий"
				$oMainTab->move($this->getField('description'), $oDescriptionTab);
			break;

			case 'xsl_dir':
			default:
				$title = $this->_object->id
						? Core::_('Xsl_Dir.edit_title')
						: Core::_('Xsl_Dir.add_title');

				// Значения директории для добавляемого объекта
				if (is_null($this->_object->id))
				{
					$this->_object->parent_id = Core_Array::getGet('xsl_dir_id');
				}

				// Удаляем стандартный <input>
				$oAdditionalTab->delete(
					 $this->getField('parent_id')
				);

				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillXslDir(0, $this->_object->id)
					)
					->name('parent_id')
					->value($this->_object->parent_id)
					->caption(Core::_('Xsl_Dir.parent_id'));

				$oMainTab->addAfter($oSelect_Dirs,  $this->getField('name'));
			break;
		}

		$this->title($title);

		return $this;
	}

	/**
	 * Create visual tree of the directories
	 * @param int $iXslDirParentId parent directory ID
	 * @param boolean $bExclude exclude group ID
	 * @param int $iLevel current nesting level
	 * @return array
	 */
	public function fillXslDir($iXslDirParentId = 0, $bExclude = FALSE, $iLevel = 0)
	{
		$iXslDirParentId = intval($iXslDirParentId);
		$iLevel = intval($iLevel);

		$oXsl_Dir = Core_Entity::factory('Xsl_Dir', $iXslDirParentId);

		$aReturn = array();

		// Дочерние разделы
		$childrenDirs = $oXsl_Dir->Xsl_Dirs->findAll();

		if (count($childrenDirs))
		{
			foreach ($childrenDirs as $childrenDir)
			{
				if ($bExclude != $childrenDir->id)
				{
					$aReturn[$childrenDir->id] = str_repeat('  ', $iLevel) . $childrenDir->name;
					$aReturn += $this->fillXslDir($childrenDir->id, $bExclude, $iLevel+1);
				}
			}
		}

		return $aReturn;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Xsl_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		parent::_applyObjectProperty();

		$modelName = $this->_object->getModelName();

		switch($modelName)
		{
			case 'xsl':
				$xsl_value = Core_Array::getPost('xsl_value');

				if (Core_Array::getPost('format'))
				{
					$xsl_value = Xsl_Processor::instance()->formatXml($xsl_value);
				}

				$this->_object->saveXslFile($xsl_value);
			break;
		}
		
		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}
	
	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 * @return boolean
	 */
	public function execute($operation = NULL)
	{
		if (!is_null($operation))
		{
			$modelName = $this->_object->getModelName();

			switch($modelName)
			{
				case 'xsl':
					$name = Core_Array::getRequest('name');
					$id = Core_Array::getRequest('id');
					$oSameXsl = Core_Entity::factory('Xsl')->getByName($name);
					
					if (!is_null($oSameXsl) && $oSameXsl->id != $id)
					{
						$this->addMessage(
							Core_Message::get(Core::_('Xsl.xsl_already_exists'))
						);
						return TRUE;
					}
			}
		}
		
		return parent::execute($operation);
	}
}