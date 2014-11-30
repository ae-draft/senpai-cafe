<?php
/**
 * Google sitemap.
 *
 * http://www.sitemaps.org/protocol.html
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */

require_once('bootstrap.php');

$oDefault_Router_Route = Core_Router::add('default', '()');
Core::parseUrl();

// Контроллер совместимости с HostCMS 5
if (defined('USE_HOSTCMS_5') && USE_HOSTCMS_5)
{
	$oDefault_Router_Route->controller('Core_Command_Controller_Hostcms5_Default');
}

$oSite = Core_Entity::factory('Site')->getByAlias(Core::$url['host']);

if (is_null($oSite))
{
	// Site not found
	Core_Router::add('domain_not_found', '()')
		->controller('Core_Command_Controller_Domain_Not_Found')
		->execute()
		->header('X-Powered-By', Core::xPoweredBy())
		->sendHeaders()->showBody();
	exit();
}

define('CURRENT_SITE', $oSite->id);
Core::initConstants($oSite);

Core::$url['path'] = '/sitemap/';

header('Content-Type: text/xml');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
header('X-Powered-By: ' . Core::xPoweredBy());

Core_Router::factory(Core::$url['path'])
	->execute() // consist exit()
	/*->header('Content-Type', 'text/xml')
	->header('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT')
	->header('X-Powered-By', Core::xPoweredBy())
	->compress()*/
	->sendHeaders()
	->showBody();