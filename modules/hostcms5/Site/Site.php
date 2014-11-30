<?php 

/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Модуль Site.
 * 
 * Файл: /modules/Site/Site.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */

$module_path_name = 'Site';
$module_name = 'Сайты';

$kernel = & singleton('kernel');

/* Список файлов для загрузки */
$kernel->AddModuleFile($module_path_name, CMS_FOLDER . "modules/hostcms5/{$module_path_name}/{$module_path_name}.class.php");
$kernel->AddModuleFile($module_path_name, CMS_FOLDER . "modules/hostcms5/{$module_path_name}/SiteTemplate.class.php");

// Добавляем версию модуля
$kernel->add_modules_version($module_path_name, '5.9', '28.04.2012');
