<?php

/*
 * 0 - PHP-генерация
 * 1 - XSL-генерация
 */
$type = 0;

// Создавать индекс
$createIndex = FALSE;

// Количество страниц в каждый файл
$perFile = 2000;

$oSite = Core_Entity::factory('Site')->getByAlias(Core::$url['host']);

$oSite_Alias = $oSite->getCurrentAlias();

if (is_null($oSite_Alias))
{
	?>Site hasn't had a default alias!<?php
	exit();
}

if ($type == 0)
{
	$oCore_Sitemap = new Core_Sitemap($oSite);
	$oCore_Sitemap
		->createIndex($createIndex)
		->perFile($perFile)
		// Показывать группы информационных систем в карте сайта
		->showInformationsystemGroups(Core_Page::instance()->libParams['showInformationsystemGroups'])
		// Показывать элементы информационных систем в карте сайта
		->showInformationsystemItems(Core_Page::instance()->libParams['showInformationsystemItems'])
		// Показывать группы магазина в карте сайта
		->showShopGroups(Core_Page::instance()->libParams['showShopGroups'])
		// Показывать товары магазина в карте сайта
		->showShopItems(Core_Page::instance()->libParams['showShopItems'])
		// Показывать модификации в карте сайта
		->showModifications(Core_Array::get(Core_Page::instance()->libParams, 'showModifications', 1))
		// Раскомментируйте при наличии достаточного объема оперативной памяти
		//->limit(10000)
		->fillNodes()
		->execute();
}
else
{
	$Structure_Controller_Show = new Structure_Controller_Show(
		$oSite->showXmlAlias(TRUE)
	);

	$Structure_Controller_Show
		->xsl(
			Core_Entity::factory('Xsl')->getByName(Core_Page::instance()->libParams['xsl'])
		)
		//->parentId(0)
		// Показывать группы информационных систем в карте сайта
		->showInformationsystemGroups(Core_Page::instance()->libParams['showInformationsystemGroups'])
		// Показывать элементы информационных систем в карте сайта
		->showInformationsystemItems(Core_Page::instance()->libParams['showInformationsystemItems'])
		// Показывать группы магазина в карте сайта
		->showShopGroups(Core_Page::instance()->libParams['showShopGroups'])
		// Показывать товары магазина в карте сайта
		->showShopItems(Core_Page::instance()->libParams['showShopItems'])
		->show();
}

exit();