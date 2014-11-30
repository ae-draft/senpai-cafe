<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * 1PS.
 *
 * @package HostCMS 6\1PS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Oneps_Module extends Core_Module{	/**
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
		$this->menu = array(			array(				'sorting' => 260,				'block' => 1,				'name' => Core::_('oneps.menu'),				'href' => "/admin/oneps/index.php",				'onclick' => "$.adminLoad({path: '/admin/oneps/index.php'}); return false"			)		);	}}