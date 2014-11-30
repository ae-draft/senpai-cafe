<?php

$oShop = Core_Entity::factory('Shop', Core_Array::get(Core_Page::instance()->libParams, 'shopId'));

$Shop_Controller_Show = new Shop_Controller_Show($oShop);

$Shop_Controller_Show
	->limit($oShop->items_on_page)
	->parseUrl();

// Текстовая информация для указания номера страницы, например "страница"
$pageName = Core_Array::get(Core_Page::instance()->libParams, 'page')
	? Core_Array::get(Core_Page::instance()->libParams, 'page')
	: 'страница';

// Разделитель в заголовке страницы
$pageSeparator = Core_Array::get(Core_Page::instance()->libParams, 'separator')
	? Core_Page::instance()->libParams['separator']
	: ' / ';

$aTitle = array($oShop->name);
$aDescription = array($oShop->name);
$aKeywords = array($oShop->name);

if (!is_null($Shop_Controller_Show->tag) && Core::moduleIsActive('tag'))
{
	$oTag = Core_Entity::factory('Tag')->getByPath($Shop_Controller_Show->tag);
	if ($oTag)
	{
		$aTitle[] = $oTag->seo_title != '' ? $oTag->seo_title : Core::_('Shop.tag', $oTag->name);
		$aDescription[] = $oTag->seo_description != '' ? $oTag->seo_description : $oTag->name;
		$aKeywords[] = $oTag->seo_keywords != '' ? $oTag->seo_keywords : $oTag->name;
	}
}

if ($Shop_Controller_Show->group)
{
	$oShop_Group = Core_Entity::factory('Shop_Group', $Shop_Controller_Show->group);

	do {
		$aTitle[] = $oShop_Group->seo_title != ''
			? $oShop_Group->seo_title
			: $oShop_Group->name;

		$aDescription[] = $oShop_Group->seo_description != ''
			? $oShop_Group->seo_description
			: $oShop_Group->name;

		$aKeywords[] = $oShop_Group->seo_keywords != ''
			? $oShop_Group->seo_keywords
			: $oShop_Group->name;

	} while($oShop_Group = $oShop_Group->getParent());
}

if ($Shop_Controller_Show->item)
{
	$oShop_Item = Core_Entity::factory('Shop_Item', $Shop_Controller_Show->item);

	$aTitle[] = $oShop_Item->seo_title != ''
		? $oShop_Item->seo_title
		: $oShop_Item->name;

	$aDescription[] = $oShop_Item->seo_description != ''
		? $oShop_Item->seo_description
		: $oShop_Item->name;

	$aKeywords[] = $oShop_Item->seo_keywords != ''
		? $oShop_Item->seo_keywords
		: $oShop_Item->name;
}

if ($Shop_Controller_Show->producer)
{
	$oShop_Producer = Core_Entity::factory('Shop_Producer', $Shop_Controller_Show->producer);
	$aKeywords[] = $aDescription[] = $aTitle[] = $oShop_Producer->name;
}

if ($Shop_Controller_Show->page)
{
	array_unshift($aTitle, $pageName . ' ' . ($Shop_Controller_Show->page + 1));
}

if (count($aTitle) > 1)
{
	$aTitle = array_reverse($aTitle);
	$aDescription = array_reverse($aDescription);
	$aKeywords = array_reverse($aKeywords);

	Core_Page::instance()->title(implode($pageSeparator, $aTitle));
	Core_Page::instance()->description(implode($pageSeparator, $aDescription));
	Core_Page::instance()->keywords(implode($pageSeparator, $aKeywords));
}

Core_Page::instance()->object = $Shop_Controller_Show;