<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Comments.
 *
 * @package HostCMS 6\Comment
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Comment_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		if (is_null($object->id))
		{
			$object->parent_id = intval(Core_Array::getGet('parent_id'));
		}

		parent::setObject($object);

		$this->title(
			$this->_object->id
				? Core::_('Comment.edit_title')
				: Core::_('Comment.add_title')
			);

		$oMainTab = $this->getTab('main');

		$oSeparatorField = Admin_Form_Entity::factory('Separator');

		$this->getField('text')->wysiwyg(TRUE);

		$this->getField('author')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 220px;");

		$this->getField('email')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 220px;");

		$this->getField('phone')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 220px;");

		$oMainTab->addAfter($oSeparatorField, $this->getField('phone'));

		$this->getField('ip')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 220px;");

		$this->getField('datetime')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 220px;");

		$this->getField('grade')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 220px;");

		$oRadioType = Admin_Form_Entity::factory('Select')
			->name('grade')
			->id('grade')
			->caption(Core::_('Comment.grade'))
			->value($this->_object->grade)
			->divAttr(array('class' => 'item_div stars'))
			->options(
				array(
					1 => 'Poor',
					2 => 'Fair',
					3 => 'Average',
					4 => 'Good',
					5 => 'Excellent',
				)
			);

		/*if (is_null($object->id))
		{
			$shop_item_id = intval(Core_Array::getGet('shop_item_id'));

			if (!$shop_item_id && $object->parent_id)
			{
				 $oParentComment = Core_Entity::factory('Comment', $object->parent_id);
			}

			$oAdmin_Form_Entity_Input = Admin_Form_Entity::factory('Input');
			$oAdmin_Form_Entity_Input
				->name('shop_item_id')
				->value()
				->divAttr(array('style' => 'display:none'));
		}*/

		$oMainTab
			->delete($this->getField('grade'))
			->add($oRadioType);

		return $this;
	}

}