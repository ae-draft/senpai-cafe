<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Market.
 *
 * @package HostCMS 6\Market
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Market_Controller extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'login',
		'contract',
		'pin',
		'cms_folder',
		'php_version',
		'mysql_version',
		'update_id',
		'domain',
		'update_server',
		'keys',
		'category_id',
		'categories',
		'items',
		'total',
		'page',
		'limit',
		'error'
	);

	/**
	 * The singleton instances.
	 * @var mixed
	 */
	static public $instance = NULL;

	/**
	 * Register an existing instance as a singleton.
	 * @return object
	 */
	static public function instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->categories = $this->items = array();
		$this->page = 1;
		$this->limit = 4;
		$this->error = 0;
	}

	/**
	 * Get directory path
	 * @return string
	 */
	public function getPath()
	{
		return CMS_FOLDER . 'hostcmsfiles/tmp/install';
	}

	/**
	 * Set market options
	 * @return self
	 */
	public function setMarketOptions()
	{
		$oHOSTCMS_UPDATE_NUMBER = Core_Entity::factory('Constant')->getByName('HOSTCMS_UPDATE_NUMBER');
		$update_id = !is_null($oHOSTCMS_UPDATE_NUMBER)
			? $oHOSTCMS_UPDATE_NUMBER->value
			: 0;

		$oSite = Core_Entity::factory('Site', CURRENT_SITE);

		$aSite_Alias_Names = array();

		$aSite_Aliases = $oSite->Site_Aliases->findAll();
		foreach ($aSite_Aliases as $oSite_Alias)
		{
			$aSite_Alias_Names[] = $oSite_Alias->name;
		}

		$oSite_Alias = $oSite->getCurrentAlias();
		$domain = !is_null($oSite_Alias)
			? $oSite_Alias->name
			: 'undefined';

		$this->login(defined('HOSTCMS_USER_LOGIN') ? HOSTCMS_USER_LOGIN : '')
			->contract(defined('HOSTCMS_CONTRACT_NUMBER') ? HOSTCMS_CONTRACT_NUMBER : '')
			->pin(defined('HOSTCMS_PIN_CODE') ? HOSTCMS_PIN_CODE : '')
			->cms_folder(CMS_FOLDER)
			->php_version(phpversion())
			->mysql_version(Core_DataBase::instance()->getVersion())
			->update_id($update_id)
			->domain($domain)
			->update_server(HOSTCMS_UPDATE_SERVER)
			->keys($aSite_Alias_Names)
			->category_id(intval(Core_Array::getRequest('category_id')))
			->page(intval(Core_Array::getRequest('current', 1)));

		return $this;
	}

	/**
	 * Загрузка магазина
	 *
	 * @return Market_Controller
	 */
	public function getMarket()
	{
		$md5_contract = md5($this->contract);
		$md5_pin = md5($this->pin);

		$url = 'http://' . $this->update_server . "/hostcmsupdate/market/?action=load_market&domain=" . rawurlencode($this->domain) .
			"&login=" . rawurlencode($this->login) .
			"&contract=" . rawurlencode($md5_contract) .
			"&pin=" . rawurlencode($md5_pin) .
			"&cms_folder=" . rawurlencode($this->cms_folder) .
			"&php_version=" . rawurlencode($this->php_version) .
			"&mysql_version=" . rawurlencode($this->mysql_version) .
			"&update_id=" . $this->update_id .
			"&category_id=" . intval($this->category_id) .
			"&current=" . intval($this->page) .
			"&limit=" . intval($this->limit);

		try
		{
			$Core_Http = Core_Http::instance()
				->url($url)
				->port(80)
				->timeout(5)
				->execute();

			$data = $Core_Http->getBody();

			$oXml = @simplexml_load_string($data);

			$aShop_Groups = array();
			if (isset($oXml->shop_group) && count($oXml->shop_group))
			{
				foreach ($oXml->shop_group as $value)
				{
					if (intval($value->count))
					{
						$oObject = new StdClass();
						$oObject->id = intval($value->attributes()->id);
						$oObject->name = strval($value->name);
						$oObject->description = strval($value->description);

						$aShop_Groups[$oObject->id] = $oObject;
					}
				}

				$this->categories = $aShop_Groups;
			}

			$aShop_Items = array();
			if (isset($oXml->shop_item) && count($oXml->shop_item))
			{
				foreach ($oXml->shop_item as $value)
				{
					$oObject = new StdClass();
					$oObject->id = intval($value->attributes()->id);

					$shop_group_id = intval($value->shop_group_id);

					$oObject->category_name = isset($aShop_Groups[$shop_group_id])
						? $aShop_Groups[$shop_group_id]->name
						: '';

					$oObject->currency = strval($value->currency);
					$oObject->name = strval($value->name);
					$oObject->description = strval($value->description);
					$oObject->image_large = 'http://' . $this->update_server . strval($value->dir) . strval($value->image_large);
					$oObject->image_small = 'http://' . $this->update_server . strval($value->dir) . strval($value->image_small);
					$oObject->url = 'http://' . $this->update_server . strval($value->url) . '?contract=' . $md5_contract . '&pin=' . $md5_pin;
					$oObject->siteuser_id = intval($value->siteuser_id);
					$oObject->price = strval($value->price);
					$oObject->paid = isset($value->paid)
						? intval($value->paid)
						: 0;

					$oAdminModule = Core_Entity::factory('Module')->getByPath(strval($value->path), FALSE);
					$oObject->installed = !is_null($oAdminModule)
						? 1
						: 0;

					$aShop_Items[] = $oObject;
				}

				$this->items = $aShop_Items;
			}

			$this->category_id = isset($oXml->category_id)
				? intval($oXml->category_id)
				: 0;

			$this->total = isset($oXml->total)
				? intval($oXml->total)
				: 0;

			$this->page = isset($oXml->page)
				? intval($oXml->page)
				: 1;

			$this->error = isset($oXml->error)
				? intval($oXml->error)
				: 0;
		}
		catch (Exception $e)
		{
			Core_Message::show(Core::_('Market.server_error_respond_0'), 'error');
		}

		return $this;
	}

	/**
	 * Загрузка приложения
	 *
	 * @param int $module_id update ID
	 * @return string
	 */
	public function getModule($module_id)
	{
		$url = 'http://' . $this->update_server . "/hostcmsupdate/market/?action=get_module&domain=" . rawurlencode($this->domain) .
			"&login=" . rawurlencode($this->login) .
			"&contract=" . rawurlencode(md5($this->contract)) .
			"&pin=" . rawurlencode(md5($this->pin)) .
			"&cms_folder=" . rawurlencode($this->cms_folder) .
			"&php_version=" . rawurlencode($this->php_version) .
			"&mysql_version=" . rawurlencode($this->mysql_version) .
			"&update_id=" . $this->update_id .
			"&category_id=" . intval($this->category_id) .
			"&module_id=" . intval($module_id) .
			"&current=" . intval($this->page) .
			"&limit=" . intval($this->limit);

		try
		{
			$Core_Http = Core_Http::instance()
				->url($url)
				->port(80)
				->timeout(5)
				->execute();

			$data = $Core_Http->getBody();

			if (empty($data))
			{
				throw new Core_Exception(Core::_('Update.server_return_empty_answer'));
			}

			$oXml = @simplexml_load_string($data);

			//echo nl2br(htmlspecialchars($data));

			if (is_object($oXml))
			{
				$error = intval($oXml->error);

				if (!$error)
				{
					if (isset($oXml->module) && count($oXml->module))
					{
						//echo $oXml->module;

						$oModule = new StdClass();

						if (intval($oXml->module->attributes()->id))
						{
							$oModule->id = intval($oXml->module->attributes()->id);
							$oModule->shop_item_id = intval($oXml->module->shop_item_id);
							$oModule->name = strval($oXml->module->name);
							$oModule->description = strval($oXml->module->description);
							$oModule->number = strval($oXml->module->number);
							$oModule->path = strval($oXml->module->path);
							$oModule->php = strval($oXml->module->php);
							$oModule->sql = strval($oXml->module->sql);
							$oModule->file = strval($oXml->module->file);
						}

						if ($oModule->id)
						{
							$oAdminModule = Core_Entity::factory('Module')->getByPath($oModule->path, FALSE);

							if (!$oAdminModule)
							{
								$sInstallDir = $this->getPath() . '/' . $oModule->shop_item_id;
								!is_dir($sInstallDir) && Core_File::mkdir($sInstallDir, CHMOD, TRUE);

								// по умолчанию ошибок обновления нет
								$error_install = FALSE;

								$Core_Http = $this->getModuleFile($oModule->file);

								$sHeaders = $Core_Http->getHeaders();

								$original_filename = 'tmpfile.tar.gz';

								$aParseHeaders = $Core_Http->parseHeaders();
								if (isset($aParseHeaders['Content-Disposition']))
								{
									if (preg_match('/.*?filename\s*=\s*"?(.+?)"?;/i', $aParseHeaders['Content-Disposition'], $aTmp))
									{
										$original_filename = $aTmp[1];
									}
								}

								if (!empty($original_filename))
								{
									$source_file = $sInstallDir . '/' . $original_filename;

									Core_File::write($source_file, $Core_Http->getBody());

									$Core_Tar = new Core_Tar($source_file);

									// Распаковываем файлы
									if (!$Core_Tar->extractModify(CMS_FOLDER, CMS_FOLDER))
									{
										$error_install = TRUE;

										// Возникла ошибка распаковки
										Core_Message::show(Core::_('Update.update_files_error'), 'error');
									}
								}

								// Размещаем файлы обновления в директорию
								if (!$error_install)
								{
									//SQL
									$sSql = strval($oModule->sql);
									$sSqlFilename = $sInstallDir . '/' . $oModule->id . '.sql';
									Core_File::write($sSqlFilename, html_entity_decode($sSql, ENT_COMPAT, 'UTF-8'));
									$sSqlCode = html_entity_decode($sSql, ENT_COMPAT, 'UTF-8');
									Sql_Controller::instance()->execute($sSqlCode);

									//PHP
									$sPhp = strval($oModule->php);
									$sPhpFilename = $sInstallDir . '/' . $oModule->id . '.php';
									Core_File::write($sPhpFilename, html_entity_decode($sPhp, ENT_COMPAT, 'UTF-8'));
									include($sPhpFilename);

									$oAdminModule = Core_Entity::factory('Module');
									$oAdminModule
										->name($oModule->name)
										->description($oModule->description)
										->active(1)
										->indexing(1)
										->path($oModule->path)
										->save();

									// install() для модуля, если есть
									$oAdminModule->setupModule();
								}

								// Удаляем папку с файлами
								is_dir($sInstallDir) && Core_File::deleteDir($sInstallDir);

								// Если не было ошибок
								if (!$error_install)
								{
									Core_Message::show(Core::_('Market.install_success', $oModule->name));
								}
							}
							else
							{
								Core_Message::show(Core::_('Market.installed', $oModule->name));
							}
						}
					}
				}

				if ($error > 0)
				{
					$sModuleName = $error < 10 ? 'Update' : 'Market';

					Core_Message::show(Core::_($sModuleName . '.server_error_respond_' . $error), 'error');
				}
			}
		}
		catch (Exception $e)
		{
			Core_Message::show(Core::_('Market.server_error_respond_0'), 'error');
		}

		return NULL;
	}

	/**
	 * Загрузка файла модуля
	 *
	 * @param string $path
	 * @return Core_Http
	 */
	public function getModuleFile($path)
	{
		$url = 'http://' . $this->update_server . $path . "&domain=".rawurlencode($this->domain) .
		"&login=" . rawurlencode($this->login) .
		"&contract=" . rawurlencode(md5($this->contract)) .
		"&pin=" . rawurlencode(md5($this->pin)) .
		"&cms_folder=" . rawurlencode($this->cms_folder) .
		"&php_version=" . rawurlencode($this->php_version) .
		"&mysql_version=" . rawurlencode($this->mysql_version) .
		"&update_id=" . $this->update_id;

		$Core_Http = Core_Http::instance()
			->url($url)
			->port(80)
			->timeout(5)
			->execute();

		return $Core_Http;
	}
}