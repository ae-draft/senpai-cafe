<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Модуль admin_forms.
 * 
 * Файл: /modules/admin_forms/admin_forms.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
$module_path_name = 'admin_forms';
$module_name = 'Формы центра управления системой';

$kernel = & singleton('kernel');

/* Список файлов для загрузки */
$kernel->AddModuleFile($module_path_name, CMS_FOLDER . "modules/hostcms5/$module_path_name/$module_path_name.class.php");
$kernel->AddModuleFile($module_path_name, CMS_FOLDER . "modules/hostcms5/$module_path_name/admin_forms_fields.class.php", 'admin_forms_fields');

// Добавляем версию модуля.
$kernel->add_modules_version($module_path_name, '5.9', '28.04.2012');