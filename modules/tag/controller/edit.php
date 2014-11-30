<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Tags.
 *
 * @package HostCMS 6\Tag
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Tag_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
			case 'tag':
				$title = $this->_object->id
					? Core::_('Tag.form_edit_add_title_add')
					: Core::_('Tag.form_edit_add_title_edit');

				if (is_null($this->_object->id))
				{
					$this->_object->tag_dir_id = Core_Array::getGet('tag_dir_id');
				}

				// Удаляем стандартный <input>
				$oAdditionalTab->delete(
					 $this->getField('tag_dir_id')
				);

				// Селектор с группой
				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillTagDir()
					)
					->name('tag_dir_id')
					->value($this->_object->tag_dir_id)
					->caption(Core::_('Tag_Dir.parent_name'));

				$oMainTab->addBefore(
					$oSelect_Dirs, $this->getField('path')
				);

				$this->getField('description')->wysiwyg(TRUE);

				if (is_null($this->_object->id))
				{
					// Удаляем стандартный <input>
					$oMainTab->delete(
						 $this->getField('name')
					);

					$oTextarea_TagName = Admin_Form_Entity::factory('Textarea');

					$oTextarea_TagName
						//->value()
						->cols(140)
						->rows(5)
						->caption(Core::_('Tag.add_tag_name'))
						->name('name');

					$oMainTab->addBefore($oTextarea_TagName, $oSelect_Dirs);
				}

				// Tags SEO
				$this->addTabAfter($seoTab = Admin_Form_Entity::factory('Tab')
					->caption('SEO')
					->name('SEO'), $oMainTab);
				$oMainTab
					->move($this->getField('seo_title')->rows(3), $seoTab)
					->move($this->getField('seo_description')->rows(3), $seoTab)
					->move($this->getField('seo_keywords')->rows(3), $seoTab);
			break;
			case 'tag_dir':
			default:
				$title = $this->_object->id
					? Core::_('Tag_Dir.form_edit_add_tags_group_title_edit')
					: Core::_('Tag_Dir.form_edit_add_tags_group_title_add');

				// Значения директории для добавляемого объекта
				if (is_null($this->_object->id))
				{
					$this->_object->parent_id = Core_Array::getGet('tag_dir_id');
				}

				// Удаляем стандартный <input>
				$oAdditionalTab->delete(
					 $this->getField('parent_id')
				);

				$oSelect_Dirs
					->options(
						array(' … ') + $this->fillTagDir(0, array($this->_object->id))
					)
					->name('parent_id')
					->value($this->_object->parent_id)
					->caption(Core::_('Tag_Dir.parent_name'));

				$oMainTab->addAfter($oSelect_Dirs,  $this->getField('name'));
			break;
		}

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @return self
	 * @hostcms-event Tag_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		$id = $this->_object->id;

		$modelName = $this->_object->getModelName();

		if (is_null($id))
		{
			switch($modelName)
			{
				case 'tag':
					$sName = trim(Core_Array::getPost('name'));

					// Массив имен меток
					$aTags = explode("\n", $sName);

					$first = array_shift($aTags);

					// Sets name for first tag
					$this->_formValues['name'] = $first;
				break;
			}
		}

		parent::_applyObjectProperty();

		switch($modelName)
		{
			case 'tag':
			if (is_null($id))
			{
				foreach ($aTags as $tag_name)
				{
					$tag_name = trim($tag_name);

					$oNewTag = clone $this->_object;

					$oNewTag->name = $tag_name;
					$oNewTag->path = '';
					$oNewTag->save();
				}
			}
			break;
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		return $this;
	}

	/**
	 * Build visual representation of group tree
	 * @param int $iTagDirParentId parent ID
	 * @param int $aExclude exclude group ID
	 * @param int $iLevel current nesting level
	 * @return array
	 */
	public function fillTagDir($iTagDirParentId = 0, $aExclude = array(), $iLevel = 0)
	{
		$iTagDirParentId = intval($iTagDirParentId);
		$iLevel = intval($iLevel);

		$oTag_Dir = Core_Entity::factory('Tag_Dir', $iTagDirParentId);

		$aReturn = array();

		$childrenDirs = $oTag_Dir->Tag_Dirs->findAll();
		if (count($childrenDirs))
		{
			$countExclude = count($aExclude);
			foreach ($childrenDirs as $childrenDir)
			{
				if ($countExclude == 0 || !in_array($childrenDir->id, $aExclude))
				{
					$aReturn[$childrenDir->id] = str_repeat('  ', $iLevel) . $childrenDir->name;
					$aReturn += $this->fillTagDir($childrenDir->id, $aExclude, $iLevel+1);
				}
			}
		}

		return $aReturn;
	}
}