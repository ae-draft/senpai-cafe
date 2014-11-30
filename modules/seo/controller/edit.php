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
class Seo_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$this->title(
			$this->_object->id
				? Core::_('Seo.edit_title')
				: Core::_('Seo.add_title')
		);

		$oMainTab = $this->getTab('main');

		// Закладка обратных ссылок
		$oLinksTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Seo.tab_links'))
			->name('links');
		$this->addTabAfter($oLinksTab, $oMainTab);
		
		// Закладка проиндексированных страниц
		$oIndexedTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Seo.tab_indexed'))
			->name('indexed');
		$this->addTabAfter($oIndexedTab, $oLinksTab);

		// Закладка каталогов
		$oCatalogTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Seo.tab_catalog'))
			->name('catalog');
		$this->addTabAfter($oCatalogTab, $oIndexedTab);
		
		// Закладка счетчиков
		$oCounterTab = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Seo.tab_counter'))
			->name('counter');
		$this->addTabAfter($oCounterTab, $oCatalogTab);

		$this->getField('pr')
			->style('width: 110px;')
			->divAttr(array('style' => 'float: left;'));

		$this->getField('tcy')
			->style('width: 110px;')
			->divAttr(array('style' => 'float: left;'));

		$this->getField('tcy_topic')
			->style('width: 220px;')
			->divAttr(array('style' => 'float: left;'));

		// Закладка обратных ссылок
		$oMainTab->move($this->getField('yandex_links')
				->style('width: 300px;')
				->divAttr(array('style' => 'float: left;')),
				$oLinksTab)
			->move($this->getField('google_links')
				->style('width: 300px;'),
				$oLinksTab)
			->move($this->getField('yahoo_links')
				->style('width: 300px;')
				->divAttr(array('style' => 'float: left;')),
				$oLinksTab)
			->move($this->getField('bing_links')
				->style('width: 300px;'),
				$oLinksTab);

		// Закладка проиндексированных страниц
		$oMainTab->move($this->getField('yandex_indexed')
				->style('width: 300px;')
				->divAttr(array('style' => 'float: left;')),
				$oIndexedTab)
			->move($this->getField('google_indexed')
				->style('width: 300px;'),
				$oIndexedTab)
			->move($this->getField('yahoo_indexed')
				->style('width: 300px;')
				->divAttr(array('style' => 'float: left;')),
				$oIndexedTab)
			->move($this->getField('bing_indexed')
				->style('width: 300px;'),
				$oIndexedTab)
			->move($this->getField('rambler_indexed')
				->style('width: 300px;'),
				$oIndexedTab);

		// Закладка каталогов
		$oMainTab->move($this->getField('yandex_catalog'), $oCatalogTab)
			->move($this->getField('rambler_catalog'), $oCatalogTab)
			->move($this->getField('dmoz_catalog'), $oCatalogTab)
			->move($this->getField('aport_catalog'), $oCatalogTab)
			->move($this->getField('mail_catalog'), $oCatalogTab);

		// Закладка счетчиков
		$oMainTab->move($this->getField('rambler_counter'), $oCounterTab)
			->move($this->getField('spylog_counter'), $oCounterTab)
			->move($this->getField('hotlog_counter'), $oCounterTab)
			->move($this->getField('liveinternet_counter'), $oCounterTab)
			->move($this->getField('mail_counter'), $oCounterTab);

		return $this;
	}
}