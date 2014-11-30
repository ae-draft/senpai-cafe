<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Admin_Form_Field_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		$this
			->addSkipColumn('admin_word_id');

		if (is_null($object->admin_form_id))
		{
			$object->admin_form_id = Core_Array::getGet('admin_form_id', 0);
		}

		parent::setObject($object);

		$oMainTab = $this->getTab('main');

		$oNameTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Admin_Form_Field.admin_form_tab_0'))
			->name('Name');

		$oViewTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Admin_Form_Field.admin_form_tab_3'))
			->name('View');

		$this
			->addTabBefore($oNameTab, $oMainTab)
			->addTabAfter($oViewTab, $oMainTab);

		// Название и описание для всех языков
		$aAdmin_Languages = Core_Entity::factory('Admin_Language')->findAll();

		if (!empty($aAdmin_Languages))
		{
			foreach ($aAdmin_Languages as $oAdmin_Language)
			{
				$oAdmin_Word_Value = $this->_object->id
					? $this->_object->Admin_Word->getWordByLanguage($oAdmin_Language->id)
					: NULL;

				if ($oAdmin_Word_Value)
				{
					$name = $oAdmin_Word_Value->name;
					$description = $oAdmin_Word_Value->description;
				}
				else
				{
					$name = '';
					$description = '';
				}

				$oAdmin_Form_Entity_Input_Name = Admin_Form_Entity::factory('Input')
					->name('name_lng_' . $oAdmin_Language->id)
					->caption(Core::_('Admin_Form_Field.form_forms_field_lng_name') . ' (' . $oAdmin_Language->shortname . ')')
					->value($name)
					->class('large')
					->format(
						array(
							// 'minlen' => array('value' => 1),
							'maxlen' => array('value' => 255)
						)
					);

				$oAdmin_Form_Entity_Textarea_Description = Admin_Form_Entity::factory('Textarea')
					->name('description_lng_' . $oAdmin_Language->id)
					->caption(Core::_('Admin_Form_Field.form_forms_field_lng_description') . ' (' . $oAdmin_Language->shortname . ')')
					->value($description)
					->rows(2);

				$oNameTab
					->add($oAdmin_Form_Entity_Input_Name)
					->add($oAdmin_Form_Entity_Textarea_Description);
			}
		}

		$this->getField('name')
			->class('')
			->divAttr(array('style' => 'float: left'))
			->style('width: 320px');

		$this->getField('sorting')
			->style('width: 320px');

		$oMainTab->addAfter(Admin_Form_Entity::factory('Separator'), $this->getField('sorting'));

		$windowId = $this->_Admin_Form_Controller->getWindowId();

		$oSelect_Type = Admin_Form_Entity::factory('Select')
			->options(
				array(
					1 => Core::_('Admin_Form_Field.field_type_text'),
					2 => Core::_('Admin_Form_Field.field_type_input'),
					3 => Core::_('Admin_Form_Field.field_type_checkbox'),
					4 => Core::_('Admin_Form_Field.field_type_link'),
					5 => Core::_('Admin_Form_Field.field_type_date_time'),
					6 => Core::_('Admin_Form_Field.field_type_date'),
					7 => Core::_('Admin_Form_Field.field_type_image_link'),
					8 => Core::_('Admin_Form_Field.field_type_image_list'),
					9 => Core::_('Admin_Form_Field.field_type_text_as_is'),
					10 => Core::_('Admin_Form_Field.field_type_image_callback_function')
				)
			)
			->name('type')
			->value($this->_object->type)
			->caption(Core::_('Admin_Form_Field.type'))
			->divAttr(array('style' => 'float: left'))
			->style('width: 320px')
			->onchange("ShowRowsAdminForm('{$windowId}', this.options[this.selectedIndex].value)");

		$oAdmin_Form_Entity_Code = Admin_Form_Entity::factory('Code');
		$oAdmin_Form_Entity_Code->html(
			"<script>ShowRowsAdminForm('{$windowId}', " . intval($this->_object->type) . ")</script>"
		);

		$oMainTab->add($oAdmin_Form_Entity_Code);

		$oMainTab
			->delete($this->getField('type'))
			->addBefore($oSelect_Type, $this->getField('format'));

		$this->getField('format')
			//->divAttr(array('style' => 'float: left'))
			->style('width: 320px');

		$this->getField('link')
			->divAttr(array('id' => 'link'))
			->rows(2);

		$this->getField('onclick')
			->divAttr(array('id' => 'link'))
			->rows(2);

		$this->getField('image')
			->divAttr(array('id' => 'image'))
			->rows(3);

		$this->getField('list')
			->divAttr(array('id' => 'list'))
			->rows(3);

		$oFilter_Type = Admin_Form_Entity::factory('Select')
			->options(array(
					0 => Core::_('Admin_Form_Field.filter_where'),
					1 => Core::_('Admin_Form_Field.filter_having')
			))
			->name('filter_type')
			->value($this->_object->filter_type)
			->caption(Core::_('Admin_Form_Field.filter_type'))
			//->divAttr(array('style' => 'float: left'))
			->style('width: 110px');

		$oMainTab
			->delete($this->getField('filter_type'))
			->add($oFilter_Type);

		// Вкладка "Вид"
		$oSelect_Type_Title_Align = Admin_Form_Entity::factory('Select')
			->options(
				array(
					'left' => Core::_('Admin_Form_Field.left'),
					'center' => Core::_('Admin_Form_Field.center'),
					'right' => Core::_('Admin_Form_Field.right')
				)
			)
			->name('title_align')
			->value($this->_object->title_align)
			->caption(Core::_('Admin_Form_Field.title_align'))
			->divAttr(array('style' => 'float: left'))
			->style('width: 320px');

		$oSelect_Type_Align = Admin_Form_Entity::factory('Select')
			->options(
				array(
					'left' => Core::_('Admin_Form_Field.left'),
					'center' => Core::_('Admin_Form_Field.center'),
					'right' => Core::_('Admin_Form_Field.right')
				)
			)
			->name('align')
			->value($this->_object->align)
			->caption(Core::_('Admin_Form_Field.align'))
			->divAttr(array('style' => 'float: left'))
			->style('width: 320px');

		$oViewTab
			->add($oSelect_Type_Title_Align)
			->add($oSelect_Type_Align)
			->add(Admin_Form_Entity::factory('Separator'));

		$oMainTab
			->delete($this->getField('title_align'))
			->delete($this->getField('align'))
			->move($this->getField('width')
				->divAttr(array('style' => 'float: left'))
			->style('width: 320px'), $oViewTab)
			->move($this->getField('style')
				->divAttr(array('style' => 'float: left'))
				->style('width: 320px'), $oViewTab)
			->move($this->getField('attributes')
				->divAttr(array('style' => 'float: left'))
				->style('width: 650px'), $oViewTab);

		$oViewTab->addBefore(Admin_Form_Entity::factory('Separator'), $this->getField('attributes'));

		//
		$oAdmin_Word_Value = $this->_object->Admin_Word->getWordByLanguage(CURRENT_LANGUAGE_ID);
		$form_name = $oAdmin_Word_Value ? $oAdmin_Word_Value->name : '';

		$title = is_null($this->_object->id)
			? Core::_('Admin_Form_Field.form_add_forms_field_title')
			: Core::_('Admin_Form_Field.form_edit_forms_field_title', $form_name);

		$this->title($title);

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @return self
	 * @hostcms-event Admin_Form_Field_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		parent::_applyObjectProperty();

		$aAdmin_Languages = Core_Entity::factory('Admin_Language')->findAll();

		if (!empty($aAdmin_Languages))
		{
			$oAdmin_Form_Field = $this->_object;
			foreach ($aAdmin_Languages as $oAdmin_Language)
			{
				if ($oAdmin_Form_Field->admin_word_id)
				{
					$oAdmin_Word = $oAdmin_Form_Field->Admin_Word;
				}
				else
				{
					$oAdmin_Word = Core_Entity::factory('Admin_Word');
					$oAdmin_Form_Field->add($oAdmin_Word);
				}

				$oAdmin_Word_Value = $oAdmin_Word->getWordByLanguage($oAdmin_Language->id);

				$name = Core_Array::getPost('name_lng_' . $oAdmin_Language->id);
				$description = Core_Array::getPost('description_lng_' . $oAdmin_Language->id);

				if (!$oAdmin_Word_Value)
				{
					$oAdmin_Word_Value = Core_Entity::factory('Admin_Word_Value');
					$oAdmin_Word_Value->admin_language_id = $oAdmin_Language->id;
				}

				$oAdmin_Word_Value->name = $name;
				$oAdmin_Word_Value->description = $description;
				$oAdmin_Word_Value->save();
				$oAdmin_Word->add($oAdmin_Word_Value);
			}
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		return $this;
	}
}