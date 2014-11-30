<?php

$oInformationsystem = Core_Entity::factory('Informationsystem', Core_Page::instance()->libParams['informationsystemId']);

$Informationsystem_Controller_Rss_Show = new Informationsystem_Controller_Rss_Show($oInformationsystem);

$Informationsystem_Controller_Rss_Show
	->offset(Core_Page::instance()->libParams['begin'])
	->limit(Core_Page::instance()->libParams['count'])
	->yandex(Core_Page::instance()->libParams['yandexFullText'])
	->group(Core_Page::instance()->libParams['informationGroupId'] == 0
		? FALSE
		: Core_Page::instance()->libParams['informationGroupId']
	)
	->stripTags(Core_Page::instance()->libParams['stripTags']);

if (Core_Page::instance()->libParams['rssTitle'])
{
	$Informationsystem_Controller_Rss_Show
		->title(Core_Page::instance()->libParams['rssTitle']);
}

if (Core_Page::instance()->libParams['rssDescription'])
{
	$Informationsystem_Controller_Rss_Show
		->description(Core_Page::instance()->libParams['rssDescription']);
}

if (Core_Page::instance()->libParams['rssUrl'])
{
	$Informationsystem_Controller_Rss_Show
		->link(Core_Page::instance()->libParams['rssUrl']);
}

if (Core_Page::instance()->libParams['rssImage'])
{
	$oSiteAlias = $oInformationsystem->Site->getCurrentAlias();
	if ($oSiteAlias)
	{
		$Informationsystem_Controller_Rss_Show->image(array(
			'url' => Core_Page::instance()->libParams['rssImage'],
			'title' => $oInformationsystem->name,
			'link' => 'http://' . $oSiteAlias->name . '/'
		));
	}
}

$Informationsystem_Controller_Rss_Show->show();

exit();