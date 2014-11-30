<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Модуль Core.
 * 
 * Файл: /modules/Core/Core.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */

define('PRODUCT_HTTP', 'www.hostcms.ru');
define('SUPPORT_EMAIL', 'support@hostcms.ru');
define('SALES_EMAIL', 'sales@hostcms.ru');
define('HTTP_PURCHASE', 'www.hostcms.ru/orders/');

if (!defined('ON_PAGE'))
{
	define('ON_PAGE', 20);
}

if (!defined('SUPERUSER_EMAIL'))
{
	define('SUPERUSER_EMAIL', 'email@not.exists');
}

if (!defined('ADD_COMMENT_DELAY'))
{
	define('ADD_COMMENT_DELAY',10);
}

if (!defined('MAIL_EVENTS_STATUS'))
{
	define('MAIL_EVENTS_STATUS',2);
}

if (!defined('DEFAULT_LNG'))
{
	define('DEFAULT_LNG','ru');
}

if (!defined('POLLS_WIDTH'))
{
	define('POLLS_WIDTH',100);
}

if (!defined('USER_NONE'))
{
	define('USER_NONE','#####');
}

if (!defined('JPG_QUALITY'))
{
	define('JPG_QUALITY',60);
}

if (!defined('PNG_QUALITY'))
{
	define('PNG_QUALITY',9);
}

if (!defined('TMP_DIR'))
{
	define('TMP_DIR','hostcmsfiles/tmp/');
}

if (!defined('INTEGRATION'))
{
	define('INTEGRATION', 7);
}
