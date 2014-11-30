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
class Shop_Dir_Model extends Core_Entity{
	/**
	 * Backend property
	 * @var string
	 */	public $img = 0;
	
	/**
	 * Backend property
	 * @var string
	 */	public $shop_currency_name = NULL;
	
	/**
	 * Backend property
	 * @var string
	 */	public $email = '';
	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */	protected $_hasMany = array(		'shop' => array(),		'shop_dir' => array('foreign_key' => 'parent_id')	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(		'site' => array(),
		'shop_dir' => array('foreign_key' => 'parent_id'),	);
	/**
	 * Constructor.
	 * @param int $id entity ID
	 */	public function __construct($id = NULL)	{		parent::__construct($id);		if (is_null($id))		{			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;			$this->_preloadValues['site_id'] = defined('CURRENT_SITE') ? CURRENT_SITE : 0;		}	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{		$newObject = parent::copy();		$aShop_Dirs = $this->Shop_Dirs->findAll();		foreach($aShop_Dirs as $oShop_Dir)		{			$newObject->add($oShop_Dir->copy());		}		$aShops = $this->Shops->findAll();		foreach($aShops as $oShop)		{			$newObject->add($oShop->copy());		}		return $newObject;	}
	/**
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Core_Entity
	 */	public function delete($primaryKey = NULL)
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
	 */	public function getParent()	{		if ($this->parent_id)		{			return Core_Entity::factory('Shop_Dir', $this->parent_id);		}		else		{			return NULL;		}	}}