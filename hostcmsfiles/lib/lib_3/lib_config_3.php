<?php

$text = strval(Core_Array::getGet('text'));
if ($text)
{
	Core_Page::instance()->title('Поиск: ' . $text);
}

//Autocomplete
if (!is_null(Core_Array::getGet('autocomplete')) && !is_null(Core_Array::getGet('query')))
{
	$iShopId = 1;
	$sQuery = strval(Core_Array::getGet('query'));

	$aJSON = array();
	$aJSON['query'] = $sQuery;
	$aJSON['suggestions'] = array();

	$oShop_Items = Core_Entity::factory('Shop', $iShopId)->Shop_Items;
	$oShop_Items->queryBuilder()
		->where('shop_items.name', 'LIKE', '%' . $sQuery . '%')
		->limit(10);

	$aShop_Items = $oShop_Items->findAll();

	foreach ($aShop_Items as $oShop_Item)
	{
		$aJSON['suggestions'][] = array(
			'value' => $oShop_Item->name,
			'price' => $oShop_Item->price,
			'data' => $oShop_Item->id
		);
	}

	Core_Page::instance()->response
		->status(200)
		->header('Pragma', "no-cache")
		->header('Cache-Control', "private, no-cache")
		->header('Vary', "Accept")
		->header('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT')
		->header('X-Powered-By', 'HostCMS')
		->header('Content-Disposition', 'inline; filename="files.json"');

	Core_Page::instance()->response
		->body(json_encode($aJSON))
		->header('Content-type', 'application/json; charset=utf-8');

	Core_Page::instance()->response
		->sendHeaders()
		->showBody();

	exit();
}