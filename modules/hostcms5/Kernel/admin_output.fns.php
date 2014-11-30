<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Файл: /modules/Kernel/admin_output.fns.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */

/**
 * @package HostCMS 5
 */
function do_html_header_admin($title, $param = false)
{
	$oSkin = Core_Skin::instance()
		->title($title)
		->header();
}

/**
 * @package HostCMS 5
 */
function do_html_footer_admin()
{
	$oSkin = Core_Skin::instance()
		->footer();
	exit();
}