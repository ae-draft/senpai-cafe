<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Payment_System_Model extends Core_Entity
{
	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'shop' => array(),
		'shop_currency' => array()
	);

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'shop_delivery_payment_system' => array()
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'sorting' => 0,
		'active' => 1,
	);

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'shop_payment_systems.sorting' => 'ASC'
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
	 * Get the path to the payment system
	 * @return string
	 */
	public function getPaymentSystemFilePath()
	{
		return CMS_FOLDER . "hostcmsfiles/shop/pay/handler" . intval($this->id) . ".php";
	}

	/**
	 * Get content of the payment system file
	 * @return string|NULL
	 */
	public function loadPaymentSystemFile()
	{
		$path = $this->getPaymentSystemFilePath();
		return is_file($path) ? Core_File::read($path) : NULL;
	}

	/**
	 * Specify content of the payment system file
	 * @param string $content content
	 * @return self
	 */
	public function savePaymentSystemFile($content)
	{
		$this->save();

		$sLibFilePath = $this->getPaymentSystemFilePath();
		Core_File::mkdir(dirname($sLibFilePath), CHMOD, TRUE);
		Core_File::write($sLibFilePath, trim($content));

		return $this;
	}

	/**
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Core_Entity
	 */
	public function delete($primaryKey = NULL)
	{
		if (is_null($primaryKey))
		{
			$primaryKey = $this->getPrimaryKey();
		}

		$this->id = $primaryKey;

		try
		{
			Core_File::delete($this->getPaymentSystemFilePath());
		} catch (Exception $e) {}

		$this->Shop_Delivery_Payment_Systems->deleteAll(FALSE);

		return parent::delete($primaryKey);
	}

	/**
	 * Change status of activity for payment system
	 * @return self
	 */
	public function changeStatus()
	{
		$this->active = 1 - $this->active;
		return $this->save();
	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		$newObject = parent::copy();

		try
		{
			Core_File::copy($this->getPaymentSystemFilePath(), $newObject->getPaymentSystemFilePath());
		} catch (Exception $e) {}

		return $newObject;
	}
}