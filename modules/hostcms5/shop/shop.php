<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Модуль shop.
 *
 * Файл: /modules/shop/shop.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */

$module_path_name = 'shop';
$module_name = 'Интернет-магазин';

$kernel = & singleton('kernel');

/* Список файлов для загрузки */
$kernel->AddModuleFile($module_path_name, CMS_FOLDER."modules/hostcms5/{$module_path_name}/{$module_path_name}.class.php");

$kernel->AddModuleFile($module_path_name, CMS_FOLDER."modules/hostcms5/{$module_path_name}/affiliate.class.php", 'affiliate');
$kernel->AddModuleFile($module_path_name, CMS_FOLDER."modules/hostcms5/{$module_path_name}/cml.class.php", 'cml');
$kernel->AddModuleFile($module_path_name, CMS_FOLDER."modules/hostcms5/{$module_path_name}/warehouse.class.php", 'cml');

// Добавляем версию модуля
$kernel->add_modules_version($module_path_name, '5.9', '28.04.2012');

// Подключаем языковой файл (ВСЕГДА, Т.К. В НЕМ ЕСТЬ ИНФОРМАЦИЯ О МАСКАХ ДЛЯ ЗАКАЗА)
$kernel->LoadModulesLngFile($module_path_name, $module_name);