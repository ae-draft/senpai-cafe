<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * SEO.
 *
 * @package HostCMS 6\Seo
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Seo_Module extends Core_Module
{
	/**
	 * Module version
	 * @var string
	 */
	public $version = '6.1';

	/**
	 * Module date
	 * @var date
	 */
	public $date = '2014-07-16';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->menu = array(
			array(
				'sorting' => 55,
				'block' => 1,
				'ico' => 'fa-bullseye',
				'name' => Core::_('Seo.menu'),
				'href' => "/admin/seo/index.php",
				'onclick' => "$.adminLoad({path: '/admin/seo/index.php'}); return false"
			)
		);
	}
}