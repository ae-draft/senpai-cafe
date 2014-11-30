<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Administration center users.
 *
 * @package HostCMS 6\User
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class User_Module extends Core_Module{	/**
	 * Module version
	 * @var string
	 */
	public $version = '6.1';

	/**
	 * Module date
	 * @var date
	 */
	public $date = '2014-08-22';
	/**
	 * Constructor.
	 */	public function __construct()	{
		parent::__construct();
				$this->menu = array(			array(				'sorting' => 10,				'block' => 2,
				'ico' => 'fa-user',				'name' => Core::_('User.menu'),				'href' => "/admin/user/index.php",				'onclick' => "$.adminLoad({path: '/admin/user/index.php'}); return false"			)		);	}}