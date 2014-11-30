<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * IP addresses.
 *
 * @package HostCMS 6\Ipaddress
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Ipaddress_Model extends Core_Entity{
	/**
	 * Column consist item's name
	 * @var string
	 */
	protected $_nameColumn = 'ip';

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */	public function __construct($id = NULL)	{		parent::__construct($id);		if (is_null($id))		{			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;		}	}
	/**
	 * Get ipaddress by ip
	 * @param string $ip ip
	 * @return Ip|NULL
	 */	public function getByIp($ip)	{		$this->queryBuilder()			->clear()			->where('ip', '=', $ip)			->limit(1);
		$aIp = $this->findAll();
		return isset($aIp[0])
			? $aIp[0]
			: NULL;
	}
	/**
	 * Change access mode
	 * @return self
	 */	public function changeAccess()	{		$this->deny_access = 1 - $this->deny_access;		$this->save();		return $this;	}
	    /**
	 * Change statistic mode
	 * @return self
	 */	public function changeStatistic()	{		$this->no_statistic = 1 - $this->no_statistic;		$this->save();		return $this;	}

	/**
	 * Check if there another ip with this address is
	 * @return self
	 */
	protected function _checkDuplicate()
	{
		$oIpaddressDublicate = Core_Entity::factory('Ipaddress')->getByIp($this->ip);

		if (!is_null($oIpaddressDublicate) && $oIpaddressDublicate->id != $this->id)
		{
			$this->id = $oIpaddressDublicate->id;
		}

		return $this;
	}

	/**
	 * Update object data into database
	 * @return Core_ORM
	 */
	public function update()
	{
		$this->_checkDuplicate();
		return parent::update();
	}

	/**
	 * Save object.
	 *
	 * @return Core_Entity
	 */
	public function save()
	{
		$this->_checkDuplicate();
		return parent::save();
	}}