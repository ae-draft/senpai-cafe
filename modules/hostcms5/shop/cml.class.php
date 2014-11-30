<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
*
* Класс модуля "CommerceML".
*
* Файл: /modules/shop/cml.class.php
*
* @author Hostmake LLC
* @version 5.x
*/
class cml
{
	var $nl2br;
	/**
	* Импорт из формата CommerceML
	* @param int $array_of_cml_data массив с данными в формате CommerceML, полученный в результате работы метода Xml2Array класса kernel, выполненного на целевом XML файле, содержащим данные в формате CommerceML
	* @param int $shop_shops_id идентификатор интернет-магазина
	* @param int $import_price_action_items идентификатор действия с уже существующими товарами (0 - Удалить существующие товары (во всех группах), 1 - Обновить информацию для существующих товаров, 2 - Оставить без изменений)
	* @param int $shop_groups_parent_id идентификатор группы в которую выгружать данные из CommerceML
	* @param str $images_path путь к картинкам
	* @return array ассоциативный массив с данными о количестве обработанных данных
	* - array['count_insert_item'] - количество вставленных товаров
	* - array['count_update_item'] - количество обновленных товаров
	* - array['count_dir'] - количество вставленных групп товаров
	*/
	function ImportCML($array_of_cml_data, $shop_shops_id, $import_price_action_items, $shop_groups_parent_id, $images_path = '', $nl2br = TRUE)
	{
		throw new Core_Exception('Method ImportCML() does not allow. Please use API-6.');
	}
}