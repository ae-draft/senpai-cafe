<?php

$oCore_Page = Core_Page::instance();

if ($oCore_Page->structure->getPath() != Core::$url['path'])
{
	$oCore_Response = $oCore_Page->deleteChild()->response->status(404);

	// Если определена константа с ID страницы для 404 ошибки и она не равна нулю
	$oSite = Core_Entity::factory('Site', CURRENT_SITE);
	if ($oSite->error404)
	{
		$oStructure = Core_Entity::factory('Structure')->find($oSite->error404);

		$oCore_Page = Core_Page::instance();

		// страница с 404 ошибкой не найдена
		if (is_null($oStructure->id))
		{
			throw new Core_Exception('Group not found');
		}

		if ($oStructure->type == 0)
		{
			$oDocument_Versions = $oStructure->Document->Document_Versions->getCurrent();

			if (!is_null($oDocument_Versions))
			{
				$oCore_Page->template($oDocument_Versions->Template);
			}
		}
		// Если динамическая страница или типовая дин. страница
		elseif ($oStructure->type == 1 || $oStructure->type == 2)
		{
			$oCore_Page->template($oStructure->Template);
		}

		$oCore_Page->addChild($oStructure->getRelatedObjectByType());
		$oStructure->setCorePageSeo($oCore_Page);
		
		// Если уже идет генерация страницы, то добавленный потомок не будет вызван
		$oCore_Page->buildingPage && $oCore_Page->execute();
	}
	else
	{
		if (Core::$url['path'] != '/')
		{
			// Редирект на главную страницу
			$oCore_Response->header('Location', '/');
		}
	}
}