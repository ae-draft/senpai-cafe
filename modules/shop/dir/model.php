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
class Shop_Dir_Model extends Core_Entity
	/**
	 * Backend property
	 * @var string
	 */
	
	/**
	 * Backend property
	 * @var string
	 */
	
	/**
	 * Backend property
	 * @var string
	 */
	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'shop_dir' => array('foreign_key' => 'parent_id'),
	/**
	 * Constructor.
	 * @param int $id entity ID
	 */

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
	/**
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Core_Entity
	 */
	{
		if (is_null($primaryKey))
		{
			$primaryKey = $this->getPrimaryKey();
		}

		$this->id = $primaryKey;

		// Shops
		$this->Shops->deleteAll(FALSE);
		// Dirs
		$this->Shop_Dirs->deleteAll(FALSE);

		return parent::delete($primaryKey);
	}
	/**
	 * Get parent comment
	 * @return Shop_Dir_Model|NULL
	 */