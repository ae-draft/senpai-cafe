<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Administration center users.
 *
 * @package HostCMS 6\User
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class User_Accessdenied_Model extends Core_Entity{	/**
	 * Disable markDeleted()
	 * @var mixed
	 */	protected $_marksDeleted = NULL;
	/**
	 * Backend property
	 * @var mixed
	 */	protected $_deltaTime = NULL;
	/**
	 * Constructor.
	 * @param int $id entity ID
	 */	public function __construct($id = NULL)	{		parent::__construct($id);		$this->_deltaTime = 60*60*24;	}
	/**
	 * Get element by IP
	 * @param string $ip IP
	 * @return array
	 */	public function getByIp($ip)	{		$date = Core_Date::timestamp2sql(time() - $this->_deltaTime);		$this->queryBuilder()			->clear()			->where('datetime', '>=', $date)			->where('ip', '=', $ip)			->orderBy('datetime', 'DESC');		return $this->findAll();	}}