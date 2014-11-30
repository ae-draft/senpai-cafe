<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для формирования меню в разделе администрирования.
 * 
 * Файл: /modules/Kernel/AdminMenu.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class AdminMenu
{
	/**
	 * Метод добавляет модуль в меню раздела администрирования
	 *
	 * @param int $order порядок элемента в меню
	 * @param string $name имя элемента
	 * @param string $onclick обработчик события onclick
	 * @param string $link ссылка
	 * @param string $module_name имя модуля-владельца раздела меню
	 * @param int $sub_menu_id идентификатор подменю, в которое будет включен модуль
	 */
	function AddAdminMenuItem($order, $name, $onclick, $link, $module_name, $sub_menu_id = 0)
	{
		$order = intval($order);
		// Приводим к массиву или пишем пустой массив
		$GLOBALS['gAdminMenu'][$sub_menu_id] = Core_Type_Conversion::toArray($GLOBALS['gAdminMenu'][$sub_menu_id]);

		// Добавляем новый пункт меню в массив
		$GLOBALS['gAdminMenu'][$sub_menu_id][] = array(
			$order, // порядок сортировки для меню
			$name, // наименование раздела меню
			$link, // ссылка
			$onclick, // ссылка
			$module_name // имя модуля
		);
	}
}
