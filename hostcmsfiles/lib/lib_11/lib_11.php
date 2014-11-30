<?php

$oShop = Core_Entity::factory('Shop', Core_Array::get(Core_Page::instance()->libParams, 'shopId'));

$Shop_Controller_Show = new Shop_Controller_Show($oShop);

$path = Core_Page::instance()->structure->getPath();

$Shop_Controller_Show
	->pattern($path . '(page-{page}/)')
	->addEntity(
		Core::factory('Core_Xml_Entity')
			->name('path')
			->value($path)
	)
	->limit(500)
	->parseUrl();

$Shop_Controller_Show
	->shopItems()
	->queryBuilder()
	->clearOrderBy()
	->leftJoin('shop_groups', 'shop_groups.id', '=', 'shop_items.shop_group_id')
	->where('shop_items.active', '=', 1)
	->where('shop_groups.active', '=', 1)
	->clearOrderBy()
	->orderBy('shop_items.shop_group_id')
	->orderBy('shop_items.name');

$Shop_Controller_Show
	->shopGroups()
	->queryBuilder()
	->where('shop_groups.active', '=', 1)
	->clearOrderBy()
	->orderBy('shop_groups.id');

$xslName = Core_Array::get(Core_Page::instance()->libParams, 'xsl');

$Shop_Controller_Show
	->xsl(
		Core_Entity::factory('Xsl')->getByName($xslName)
	)
	->groupsMode('all')
	->itemsProperties(TRUE)
	->group(FALSE)
	->show();