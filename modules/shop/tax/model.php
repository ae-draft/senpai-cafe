<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Tax_Model extends Core_Entity
{
	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'shop' => array()
	);

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;
		}
	}

	/**
	 * Switch tax_is_included flag
	 * @return self
	 */
	public function changeIncluded()
	{
		$this->tax_is_included = 1 - $this->tax_is_included;
		return $this->save();
	}

	/**
	 * Get tax by guid
	 * @param string $guid guid
	 * @return Shop_Tax_Model|NULL
	 */
	public function getByGuid($guid)
	{
		$this->queryBuilder()
			//->clear()
			->where('guid', '=', $guid)
			->limit(1);

		$aObjects = $this->findAll(FALSE);

		return isset($aObjects[0]) ? $aObjects[0] : NULL;
	}
}