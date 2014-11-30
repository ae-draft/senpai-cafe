<?php

$oShop = Core_Entity::factory('Shop', Core_Array::get(Core_Page::instance()->libParams, 'shopId'));

$Shop_Controller_YandexMarket = new Shop_Controller_YandexMarket($oShop);
$Shop_Controller_YandexMarket->show();

exit();