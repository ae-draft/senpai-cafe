<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 *
 * Контроллер загрузки списка тегов(меток), соответствующих фильтру
 *
 * @package HostCMS 6\Tag
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Tag_Controller_Ajaxload extends Admin_Form_Action_Controller
{
	protected $_tagFilter = null;

	public function query($tagFilter = '')
	{
		$this->_tagFilter = $tagFilter;
		return $this;
	}

	public function execute($operation = NULL)
	{		
		$aReturn = array();
				
		$oTag = Core_Entity::factory('Tag');
		$oTag->queryBuilder()->orderBy('name');
		$aTags = $oTag->getAllByName('%' . $this->_tagFilter . '%', FALSE, 'LIKE');
		
		foreach ($aTags as $oTag)
		{
			$aReturn[] = $oTag->name;
		}

		echo json_encode($aReturn);

		die();
	}
}