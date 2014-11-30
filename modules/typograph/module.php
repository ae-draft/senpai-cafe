<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Typograph.
 *
 * @package HostCMS 6\Typograph
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Typograph_Module extends Core_Module{	/**
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
		$this->menu = array(			array(				'sorting' => 260,				'block' => 3,
				'ico' => 'fa-paragraph',				'name' => Core::_('typograph.menu'),				'href' => "/admin/typograph/index.php",				'onclick' => "$.adminLoad({path: '/admin/typograph/index.php'}); return false"			)		);	}}