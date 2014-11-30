<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * SEO.
 *
 * @package HostCMS 6\Seo
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Seo_Query_Position_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$this->title(Core::_('Seo_Query_Position.edit_title'));

		$oMainTab = $this->getTab('main');

		$aFormat = array(
			'minlen' => array('value' => 1),
			'maxlen' => array('value' => 5),
			'lib' => array('value' => 'integer')
		);

		$this->getField('yandex')
			->style('width: 300px;')
			->divAttr(array('style' => 'float: left;'))
			->format($aFormat);

		$oMainTab->addAfter(
			Admin_Form_Entity::factory('Separator'), $this->getField('rambler')
				->style('width: 300px;')
				->format($aFormat));

		$this->getField('google')
			->style('width: 300px;')
			->divAttr(array('style' => 'float: left;'))
			->format($aFormat);

		$oMainTab->addAfter(
			Admin_Form_Entity::factory('Separator'), $this->getField('yahoo')
			->style('width: 300px;')
			->format($aFormat));

		$this->getField('bing')
			->style('width: 300px;')
			->format($aFormat);

		return $this;
	}
}