<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Information systems.
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Item_Comment_Model extends Comment_Model
	/**
	 * Name of the table
	 * @var string
	 */

	/**
	 * Name of the model
	 * @var string
	 */
	/**
	 * Callback function
	 */
		$href = $oInformationsystem_Item->Informationsystem->Structure->getPath() . $oInformationsystem_Item->getPath();

		$oSite = $oInformationsystem_Item->Informationsystem->Site;
		$oSite_Alias = $oSite->getCurrentAlias();
		!is_null($oSite_Alias) && $href = 'http://' . $oSite_Alias->name . $href;

		Core::factory('Core_Html_Entity_A')

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		// save original _nameColumn
		$nameColumn = $this->_nameColumn;
		$this->_nameColumn = 'subject';

		// restore original _nameColumn
		$this->_nameColumn = $nameColumn;
