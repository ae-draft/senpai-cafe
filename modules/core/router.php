<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Routers
 *
 * <code>
 * // Add robots.txt route
 * Core_Router::add('robots.txt', '/robots.txt')
 * 	->controller('Core_Command_Controller_Robots');
 *
 * // Add news route
 * Core_Router::add('news', '/news/({path})(page-{page}/)(tag/{tag}/)')
 * 	->controller('Core_Command_Controller_News');
 * </code>
 * 
 * <code>
 * // Resolve route for URI $uri
 * Core_Router::factory(Core::$url['path'])
 * 	->execute()
 * 	->compress()
 * 	->sendHeaders()
 * 	->showBody();
 * </code>
 * 
 * @package HostCMS 6\Core\Router
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Router
{
	/** 
	 * List of routes
	 * @var array
	 */
	static protected $_routes = array();

	/**
	 * Add route Core_Router_Route with name $routeName for URI with routing rules $uri
	 * @param $routeName Name of route
	 * @param $uri
	 * @return Core_Router_Route
	 */
	static public function add($routeName, $uri = NULL)
	{
		return self::$_routes[$routeName] = new Core_Router_Route($uri);
	}

	/**
	 * Resolve route for URI $uri
	 * @param string $uri URI
	 * @return Core_Router_Route
	 */
	static public function factory($uri)
	{
		foreach (self::$_routes as $routeName => $oCore_Router_Route)
		{
			if ($oCore_Router_Route->check($uri))
			{
				return $oCore_Router_Route->setUri($uri);
			}
		}

		throw new Core_Exception("Unroutable URI '%uri'.", array('%uri' => $uri));
	}
}