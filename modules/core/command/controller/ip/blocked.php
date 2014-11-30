<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Core command controller.
 *
 * @package HostCMS 6\Core\Command
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Command_Controller_Ip_Blocked extends Core_Command_Controller
{
	/**
	 * Default controller action
	 * @return Core_Response
	 * @hostcms-event Core_Command_Controller_Ip_Blocked.onBeforeShowAction
	 * @hostcms-event Core_Command_Controller_Ip_Blocked.onAfterShowAction
	 */
	public function showAction()
	{
		Core_Event::notify(get_class($this) . '.onBeforeShowAction', $this);

		$oCore_Response = new Core_Response();

		$oCore_Response
			->header('Content-Type', "text/html; charset=UTF-8")
			->header('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT')
			->header('X-Powered-By', 'HostCMS');

		$oSite = Core_Entity::factory('Site', CURRENT_SITE);

		// Если определена страница для 403 ошибки
		if ($oSite->error403)
		{
			$oStructure = Core_Entity::factory('Structure')->find($oSite->error403);

			// страница с 403 ошибкой найдена
			if (!is_null($oStructure))
			{
				// Текущий узел структуры
				define('CURRENT_STRUCTURE_ID', $oStructure->id);

				$oCore_Response->status(403);

				$oCore_Page = Core_Page::instance()/*->deleteChild()*/;

				$oStructure->setCorePageSeo($oCore_Page);

				if ($oStructure->type == 0)
				{
					$oDocument_Versions = $oStructure->Document->Document_Versions->getCurrent();

					if (is_null($oDocument_Versions))
					{
						// Document version not found
						$oCore_Router_Route = new Core_Router_Route('()');
						return $oCore_Router_Route
							->controller('Core_Command_Controller_Document_Not_Found')
							->execute();
					}

					$oTemplate = $oDocument_Versions->Template;
				}
				// Если динамическая страница или типовая дин. страница
				elseif ($oStructure->type == 1 || $oStructure->type == 2)
				{
					$oTemplate = $oStructure->Template;
				}

				ob_start();
				$oCore_Page
					->addChild($oStructure->getRelatedObjectByType())
					->template($oTemplate)
					->addTemplates($oTemplate)
					->structure($oStructure)
					->execute();

				$oCore_Response->body(ob_get_clean());

				return $oCore_Response;
			}
		}

		$oCore_Response->status(503);

		$title = Core::_('Core.access_forbidden_title');

		ob_start();
		$oSkin = Core_Skin::instance()
			->title($title)
			->header();

		Core::factory('Core_Html_Entity_Div')
			->id('indexMessage')
			->add(Core::factory('Core_Html_Entity_H1')->value($title))
			->add(Core::factory('Core_Html_Entity_P')->value(
				$title = Core::_('Core.access_forbidden')
			))
			->execute();

		$oSkin->footer();

		$oCore_Response->body(ob_get_clean());

		Core_Event::notify(get_class($this) . '.onAfterShowAction', $this, array($oCore_Response));

		return $oCore_Response;
	}
}