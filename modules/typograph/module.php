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
class Typograph_Module extends Core_Module
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
	 */
		parent::__construct();

				'ico' => 'fa-paragraph',