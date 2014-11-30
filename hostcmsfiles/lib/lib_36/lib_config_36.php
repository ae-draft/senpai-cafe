<?php
@ini_set('display_errors', 1);
error_reporting(E_ALL);
@set_time_limit(90000);

// Временная директория
$sTemporaryDirectoryWithoutCmsfolder = TMP_DIR . "1c_exchange_files/";
$sTemporaryDirectory = CMS_FOLDER . $sTemporaryDirectoryWithoutCmsfolder;

// Магазин для выгрузки
$oShop = Core_Entity::factory('Shop')->find(Core_Array::get(Core_Page::instance()->libParams, 'shopId'));

// Размер блока выгружаемых данных (100000000 = 100 мБ)
$iFileLimit = 100000000;

// bugfix
usleep(10);

// Решение проблемы авторизации при PHP в режиме CGI
if (isset($_REQUEST['authorization'])
|| (isset($_SERVER['argv'][0])
			&& empty($_SERVER['PHP_AUTH_USER'])
			&& empty($_SERVER['PHP_AUTH_PW'])))
{
	$authorization_base64 = isset($_REQUEST['authorization'])
		? $_REQUEST['authorization']
		: mb_substr($_SERVER['argv'][0], 14);

	$authorization = base64_decode(mb_substr($authorization_base64, 6));
	$authorization_explode = explode(':', $authorization);

	if (count($authorization_explode) == 2)
	{
		list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = $authorization_explode;
	}

	unset($authorization);
}

if (!isset($_SERVER['PHP_AUTH_USER']))
{
	header('WWW-Authenticate: Basic realm="HostCMS"');
	header('HTTP/1.0 401 Unauthorized');
	exit;
}
elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
{
	$answr = Core_Auth::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

	Core_Auth::setCurrentSite();

	$oUser = Core_Entity::factory('User')->getByLogin(
		$_SERVER['PHP_AUTH_USER']
	);

	if ($answr !== TRUE || !is_null($oUser) && $oUser->read_only)
	{
		// авторизация не пройдена
		exit('Authentication failed!');
	}
}
else
{
	exit();
}

if (!is_null($sType = Core_Array::getGet('type'))
	&& ($sType == 'catalog' || $sType == 'sale')
	&& Core_Array::getGet('mode') == 'checkauth')
{
	// Удаляем файлы предыдущего сеанса
	if(is_dir($sTemporaryDirectory) && Core_File::deleteDir($sTemporaryDirectory) === FALSE)
	{
		echo sprintf("\xEF\xBB\xBFfailure\nCan't delete temporary folder $sTemporaryDirectory");
		die();
	}

	// Генерируем Guid сеанса обмена
	$sGUID = Core_Guid::get();
	setcookie("1c_exchange", $sGUID);
	echo sprintf("\xEF\xBB\xBFsuccess\n1c_exchange\n%s", $sGUID);
}
elseif (!is_null($sType = Core_Array::getGet('type'))
	&& ($sType == 'catalog' || $sType == 'sale')
	&& Core_Array::getGet('mode') == 'init')
{
	echo sprintf("\xEF\xBB\xBFzip=no\nfile_limit=%s", $iFileLimit);
}
elseif (Core_Array::getGet('type') == 'catalog'
	&& Core_Array::getGet('mode') == 'file'
	&& ($sFileName = Core_Array::get($_SERVER, 'REQUEST_URI')) != '')
{
	parse_str($sFileName, $_myGet);
	$sFileName = $_myGet['filename'];

	$sFullFileName = $sTemporaryDirectory.$sFileName;
	Core_File::mkdir(dirname($sFullFileName), CHMOD, TRUE);

	if (file_put_contents($sFullFileName, file_get_contents("php://input"), FILE_APPEND) !== FALSE
		&& @chmod($sFullFileName, CHMOD_FILE))
	{
		echo "\xEF\xBB\xBFsuccess";
	}
	else
	{
		echo sprintf("\xEF\xBB\xBFfailure\nCan't save incoming data to file: $sFullFileName");
	}
}
elseif (Core_Array::getGet('type') == 'catalog'
	&& Core_Array::getGet('mode') == 'import'
	&& !is_null($sFileName = Core_Array::getGet('filename')))
{
	try
	{
		$oShop_Item_Import_Cml_Controller = new Shop_Item_Import_Cml_Controller($sTemporaryDirectory . $sFileName);
		$oShop_Item_Import_Cml_Controller->iShopId = $oShop->id;
		$oShop_Item_Import_Cml_Controller->iShopGroupId = 0;
		$oShop_Item_Import_Cml_Controller->sPicturesPath = $sTemporaryDirectoryWithoutCmsfolder;
		$oShop_Item_Import_Cml_Controller->importAction = 1;
		$fRoznPrice_name = defined('SHOP_DEFAULT_CML_CURRENCY_NAME')
			?	SHOP_DEFAULT_CML_CURRENCY_NAME
			: 'Розничная';
		$oShop_Item_Import_Cml_Controller->sShopDefaultPriceName = $fRoznPrice_name;
		$oShop_Item_Import_Cml_Controller->import();
		echo "\xEF\xBB\xBFsuccess";
	}
	catch(Exception $exc)
	{
		echo sprintf("\xEF\xBB\xBFfailure\n%s", $exc/*->getMessage()*/);
	}
}
elseif (Core_Array::getGet('type') == 'sale'
	&& Core_Array::getGet('mode') == 'query')
{
	$oXml = new Core_SimpleXMLElement(sprintf(
		"<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<КоммерческаяИнформация ВерсияСхемы=\"2.04\" ДатаФормирования=\"%s\"></КоммерческаяИнформация>",
		date("Y-m-d")));

	$aShopOrders = $oShop->Shop_Orders->getAllByUnloaded(0);

	foreach($aShopOrders as $oShopOrder)
	{
		$oShopOrder->addCml($oXml);
	}

	header('Content-type: text/xml; charset=UTF-8');
	echo "\xEF\xBB\xBF";
	echo $oXml->asXML();
}
elseif (Core_Array::getGet('type') == 'sale'
	&& Core_Array::getGet('mode') == 'success')
{
	$aShopOrders = $oShop->Shop_Orders->getAllByUnloaded(0);

	foreach($aShopOrders as $oShopOrder)
	{
		$oShopOrder->unloaded = 1;
		$oShopOrder->save();
	}

	echo "\xEF\xBB\xBFsuccess\n";
}
elseif (Core_Array::getGet('type') == 'sale'
	&& Core_Array::getGet('mode') == 'file')
{
	echo "\xEF\xBB\xBFsuccess\n";
}

die();