<?php 
/**
 * Administration center. Logout.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../bootstrap.php');

Core_Auth::systemInit();
Core_Auth::setCurrentSite();

Core_Log::instance()->clear()
	->status(Core_Log::$SUCCESS)
	->write(Core::_('Core.error_log_exit'));

Core_Session::start();

foreach($_SESSION as $key => $value)
{
	unset($_SESSION[$key]);
}

isset($_COOKIE[session_name()]) && setcookie(session_name(), '', time() - 42000, '/');

if (!@session_destroy())
{
	Core_Log::instance()->clear()
		->status(Core_Log::$WARNING)
		->write(Core::_('Core.session_destroy_error'));
}

header('Location: /admin/index.php');