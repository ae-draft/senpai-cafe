<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Documents.
 *
 * @package HostCMS 6\Document
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Document_Status_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
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
			? Core::_('Document_Status.edit_title')
			: Core::_('Document_Status.add_title');

		$this->title($title);

		return $this;
	}

	/**
	 * Fill document statuses list
	 * @param int $iSiteId site ID
	 * @return array
	 */
	public function fillDocumentStatus($iSiteId)
	{
		$iSiteId = intval($iSiteId);

		$aReturn = array();
		$aChildren = Core_Entity::factory('Document_Status')->getBySiteId($iSiteId);

		if (count($aChildren))
		{
			foreach ($aChildren as $oMenu)
			{
				$aReturn[$oMenu->id] = $oMenu->name;
			}
		}

		return $aReturn;
	}
}