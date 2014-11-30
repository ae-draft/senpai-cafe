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
abstract class Shop_Delivery_Handler
{
	/**
	 * customer company
	 * @var object
	 */
	protected $_shopCompany = NULL;
	/**
	 * customer country
	 * @var object
	 */
	protected $_shopCountry = NULL;
	/**
	 * customer location
	 * @var object
	 */
	protected $_shopLocation = NULL;
	/**
	 * customer city
	 * @var object
	 */
	protected $_shopCity = NULL;
	/**
	 * weight
	 * @var string
	 */
	protected $_weight = NULL;
	/**
	 * postcode
	 * @var string
	 */
	protected $_postcode = NULL;
	/**
	 * volume
	 * @var float
	 */
	protected $_volume = NULL;
	/**
	 * Set weight
	 * @param string $fWeight weight
	 * @return self
	 */
	public function weight($fWeight)
	{
		$this->_weight = $fWeight;
		return $this;
	}
	/**
	 * Set company
	 * @param Shop_Company_Model $oShop_Company company
	 * @return self
	 */
	public function company(Shop_Company_Model $oShop_Company)
	{
		$this->_shopCompany = $oShop_Company;
		return $this;
	}
	
	/** 
	 * Set postcode
	 * @param string $sPostcode volume
	 * @return self
	 */
	public function postcode($sPostcode)
	{
		$this->_postcode = $sPostcode;
		return $this;
	}
	
	/** 
	 * Set volume
	 * @param float $fVolume volume
	 * @return self
	 */
	public function volume($fVolume)
	{
		$this->_volume = $fVolume;
		return $this;
	}
	/**
	 * Set country
	 * @param int $iCountryID country ID
	 * @return self
	 */
	public function country($iCountryID)
	{
		$this->_shopCountry = Core_Entity::factory('Shop_Country')->find($iCountryID);
		return $this;
	}
	/**
	 * Set location
	 * @param int $iLocationID country ID
	 * @return self
	 */
	public function location($iLocationID)
	{
		$this->_shopLocation = Core_Entity::factory('Shop_Country_Location')->find($iLocationID);
		return $this;
	}
	/**
	 * Set city
	 * @param int $iCityID country ID
	 * @return self
	 */
	public function city($iCityID)
	{
		$this->_shopCity = Core_Entity::factory('Shop_Country_Location_City')->find($iCityID);
		return $this;
	}
	/**
	 * Build Shop_Delivery_Handler class
	 * @param Shop_Delivery_Model $oShop_Delivery_Model shop delivery
	 */
	static public function factory(Shop_Delivery_Model $oShop_Delivery_Model)
	{
		require_once($oShop_Delivery_Model->getHandlerFilePath());

		$name = 'Shop_Delivery_Handler' . $oShop_Delivery_Model->id;
		if (class_exists($name))
		{
			return new $name($oShop_Delivery_Model);
		}
		return NULL;
	}
	/**
	 * Execute business logic
	 */
	abstract public function execute();
}