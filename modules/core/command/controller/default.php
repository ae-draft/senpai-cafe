<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Core command controller.
 *
 * @package HostCMS 6\Core\Command
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Command_Controller_Default extends Core_Command_Controller
{
	/**
	 * Default controller action
	 * @return Core_Response
	 * @hostcms-event Core_Command_Controller_Default.onBeforeShowAction
	 * @hostcms-event Core_Command_Controller_Default.onAfterShowAction
	 * @hostcms-event Core_Command_Controller_Default.onBeforeSetTemplate
	 */
	public function showAction()
	{
		Core_Event::notify(get_class($this) . '.onBeforeShowAction', $this);

		$oCore_Response = new Core_Response();

		$oCore_Response->header('X-Powered-By', 'HostCMS');

		$oSite = Core_Entity::factory('Site', CURRENT_SITE);

		$this->_uri == '' && $this->_uri = '/';

		// Путь заканчивается на слэш
		if (substr($this->_uri, -1) == '/'
		// или передаются данные методом GET
		// || isset(Core::$url['query']) // style.css?1341303578 doesn't work
		// или запрет на 302 редирект к последнему слэшу
		|| defined('DENY_LOCATION_302_LAST_SLASH'))
		{
			// Получаем ID текущей страницы для указанного сайта по массиву
			$oStructure = $this->getStructure($this->_uri, CURRENT_SITE);

			if (is_null($oStructure) && $this->_uri == '/')
			{
				// Index page not found
				$oCore_Router_Route = new Core_Router_Route('()');
				return $oCore_Router_Route
					->controller('Core_Command_Controller_Index_Not_Found')
					->execute();
			}
		}
		else
		{
			// Если после последнего слэша указывается имя файла с расширением в два или более символов
			if (!defined('NOT_EXISTS_FILE_404_ERROR') || NOT_EXISTS_FILE_404_ERROR)
			{
				$aPath = explode('/', $this->_uri);

				// file.txt
				if (preg_match("/[а-яА-ЯёЁa-zA-Z0-9_\.\-]+\.[a-zA-Z0-9\-\.]{2,}$/Du", end($aPath)))
				{
					$oCore_Response
						->status(404)
						->body('HostCMS: File not found.');

					return $oCore_Response;
				}
			}

			if (str_replace(array("\r", "\n"), '', $this->_uri) != '/')
			{
				$oCore_Response
					->status(301)
					->header('Location', $this->_uri . '/');
			}
			else
			{
				$oCore_Response
					->status(404)
					->body('HostCMS: File not found.');
			}

			return $oCore_Response;
		}

		if (((~Core::convert64b32(Core_Array::get(Core::$config->get('core_hostcms'), 'hostcms'))) & 1176341605))
		{
			$b = explode('.', Core::$url[base64_decode('aG9zdA==')]);

			do {
				$a = explode('-', Core_Array::get(Core::$url, base64_decode('a2V5'))) + array(0, 0, 0, 0);
				$c = implode('.', $b);

				if (!(Core::convert64b32(hexdec($a[3]) ^ abs(Core::crc32($c))) ^ ~(Core::convert64b32(Core_Array::get(Core::$config->get('core_hostcms'), 'hostcms')) & abs(Core::crc32($c)) ^ Core::convert64b32(hexdec($a[2])))))
				{
					break;
				}
				array_shift($b);
			} while(count($b) > 1);

			if (hexdec($a[1]) & (~(Core::convert64b32(Core_Array::get(Core::$config->get('core_hostcms'), 'hostcms')) & abs(Core::crc32($c)) ^ Core::convert64b32(hexdec($a[2])))))
			{
				Core_Router::add('key_not_found', '()')
					->controller('Core_Command_Controller_Key_Not_Found')
					->execute()
					->header('X-Powered-By', Core::xPoweredBy())
					->sendHeaders()
					->showBody();

				exit();
			}
		}

		if (!is_null($oStructure))
		{
			$oCore_Response->status(200);
		}
		else
		{
			$oCore_Response->status(404);

			// Если определена константа с ID страницы для 404 ошибки и она не равна нулю
			if ($oSite->error404)
			{
				$oStructure = Core_Entity::factory('Structure')->find($oSite->error404);

				// страница с 404 ошибкой не найдена
				if (is_null($oStructure->id))
				{
					return $oCore_Response;
				}
			}
			else
			{
				// Редирект на главную страницу
				$this->_uri != '/' && $oCore_Response->header('Location', '/');

				return $oCore_Response;
			}
		}

		// Если доступ к узлу структуры только по HTTPS, а используется HTTP, то делаем редирект
		if ($oStructure->https == 1 && !Core::httpsUses())
		{
			$url = str_replace(array("\r", "\n"), '', Core::$url['host'] . $this->_uri);

			$oCore_Response
				->status(302)
				->header('Location', 'https://' . $url);

			return $oCore_Response;
		}

		$oCore_Response
			->header('Content-Type', 'text/html; charset=' . $oSite->coding);

		// Текущий узел структуры
		define('CURRENT_STRUCTURE_ID', $oStructure->id);

		// Проверка на доступ пользователя к странице
		$iStructureAccess = $oStructure->getSiteuserGroupId();

		$aSiteuserGroups = array(0);

		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$aSiteuser_Groups = $oSiteuser->Siteuser_Groups->findAll();
				foreach($aSiteuser_Groups as $aSiteuserGroup)
				{
					$aSiteuserGroups[] = $aSiteuserGroup->id;
				}
			}
		}

		if (!in_array($iStructureAccess, $aSiteuserGroups))
		{
			$oCore_Response->status(403);

			// Если определена страница для 403 ошибки
			if ($oSite->error403)
			{
				$oStructure = Core_Entity::factory('Structure')->find($oSite->error403);

				// страница с 403 ошибкой не найдена
				if (is_null($oStructure))
				{
					return $oCore_Response;
				}
			}
			else
			{
				// Access forbidden
				$oCore_Router_Route = new Core_Router_Route('()');
				return $oCore_Router_Route
					->controller('Core_Command_Controller_Access_Forbidden')
					->execute();
			}
		}

		/*if (Core_Array::get(Core::$config->get('core_hostcms'), 'integration') == 0 && $this->_uri == '/' // Free
			|| strtoupper($oSite->coding) != 'UTF-8'
			// Включено кэширование в статичные файлы
			|| Core::moduleIsActive('cache') && $oSite->html_cache_use == 1
			// Включена защита e-mail
			|| $oSite->safe_email
		)
		{*/
			// Старт в любом случае, т.к. содержимое идет в Core_Response->body($sContent);
			ob_start();
			ob_implicit_flush(0);
			define('OB_START', TRUE);
		//}

		/*
		Тип раздела
		0 - Страница из документооборота
		1 - Динамическая страница
		2 - Типовая динамическая страница
		*/
		$bExternalLink = $oStructure->type == 0 && strlen(trim($oStructure->url)) > 0;

		// Если тип - страница
		if ($oStructure->type == 0 && !$bExternalLink)
		{
			$oDocument_Versions = $oStructure->Document->Document_Versions->getCurrent();

			if (is_null($oDocument_Versions))
			{
				// Document version not found
				$oCore_Router_Route = new Core_Router_Route('()');
				return $oCore_Router_Route
					->controller('Core_Command_Controller_Document_Not_Found')
					->execute();
			}

			$oTemplate = $oDocument_Versions->Template;
		}
		// Если динамическая страница или типовая дин. страница
		elseif ($oStructure->type == 1 || $oStructure->type == 2)
		{
			$oTemplate = $oStructure->Template;
		}

		if ($bExternalLink)
		{
			$oCore_Response->status(301);

			// If page is not a child of the given
			if (mb_strpos($this->_uri, $oStructure->url) !== 0)
			{
				$oCore_Response
					->header('Location', $oStructure->url);
			}
			else
			{
				$oCore_Response->body(
					'HostCMS: This page has moved. <a href="' . htmlspecialchars($oStructure->url) . '">Click here.</a>'
				);
			}

			return $oCore_Response;
		}

		if (is_null($oTemplate->id))
		{
			// Template not found
			$oCore_Router_Route = new Core_Router_Route('()');
			return $oCore_Router_Route
				->controller('Core_Command_Controller_Template_Not_Found')
				->execute();
		}

		$oCore_Page = Core_Page::instance()
			->template($oTemplate)
			->structure($oStructure)
			->response($oCore_Response);

		$oStructure->setCorePageSeo($oCore_Page);
		$oCore_Page->addChild($oStructure->getRelatedObjectByType());

		// Counter is active and it's a bot
		if (Core::moduleIsActive('counter') && Counter_Controller::checkBot(Core_Array::get($_SERVER, 'HTTP_USER_AGENT')))
		{
			Counter_Controller::instance()
				->site($oSite)
				->page('http://' . strtolower(Core_Array::get($_SERVER, 'HTTP_HOST')) . Core_Array::get($_SERVER, 'REQUEST_URI'))
				->ip(Core_Array::get($_SERVER, 'REMOTE_ADDR'))
				->userAgent(Core_Array::get($_SERVER, 'HTTP_USER_AGENT'))
				->counterId(0)
				->buildCounter();
		}

		if (Core_Auth::logged())
		{
			$hostcmsAction = Core_Array::getGet('hostcmsAction');
			if ($hostcmsAction)
			{
				$_SESSION['HOSTCMS_SHOW_XML'] = $hostcmsAction == 'SHOW_XML';
			}
		}

		$fBeginTime = Core::getmicrotime();

		// Динамическая страница
		if ($oStructure->type == 1)
		{
			$StructureConfig = $oStructure->getStructureConfigFilePath();

			if (is_file($StructureConfig) && is_readable($StructureConfig))
			{
				include $StructureConfig;
			}
		}
		elseif ($oStructure->type == 2)
		{
			$oCore_Page->libParams
				= $oStructure->Lib->getDat($oStructure->id);

			// Совместимость с HostCMS 5
			if (defined('USE_HOSTCMS_5') && USE_HOSTCMS_5)
			{
				$this->_setLibParams();
			}

			$LibConfig = $oStructure->Lib->getLibConfigFilePath();

			if (is_file($LibConfig) && is_readable($LibConfig))
			{
				include $LibConfig;
			}
		}

		// Headers
		$iExpires = time() + (defined('EXPIRES_TIME')
			? EXPIRES_TIME
			: 300);

		if (!defined('SET_EXPIRES') || SET_EXPIRES)
		{
			$oCore_Response
				->header('Expires', gmdate("D, d M Y H:i:s", $iExpires) . " GMT");
		}

		if (!defined('SET_LAST_MODIFIED') || SET_LAST_MODIFIED)
		{
			$iLastModified = time() + (defined('LAST_MODIFIED_TIME')
				? LAST_MODIFIED_TIME
				: 0);

			$oCore_Response
				->header('Last-Modified', gmdate("D, d M Y H:i:s", $iLastModified) . " GMT");
		}

		if (!defined('SET_CACHE_CONTROL') || SET_CACHE_CONTROL)
		{
			$sCacheControlType = $iStructureAccess == 0
				? 'public'
				: 'private';

			// Расчитываем максимальное время истечения
			$max_age = $iExpires > time()
				? $iExpires - time()
				: 0;

			$oCore_Response
				->header('Cache-control', "{$sCacheControlType}, max-age={$max_age}");
		}

		Core_Event::notify(get_class($this) . '.onBeforeSetTemplate', $this);
		
		// Template might be changed at lib config
		$oTemplate = $oCore_Page->template;

		$oCore_Page
			->addTemplates($oTemplate)
			->buildingPage(TRUE)
			->execute();

		$oCore_Registry = Core_Registry::instance();
		$oCore_Registry->set('Core_Statistics.pageGenerationTime', Core::getmicrotime() - $fBeginTime);

		!defined('CURRENT_VERSION') && define('CURRENT_VERSION', '6.0');

		$bIsUtf8 = strtoupper($oSite->coding) == 'UTF-8';

		//if (defined('OB_START'))
		//{
		$sContent = ob_get_clean();

		// PHP Bug: pcre.recursion_limit too large.
		substr(PHP_OS, 0, 3) == 'WIN' && ini_set("pcre.recursion_limit", "524");

		// Если необходимо защищать электронные адреса, опубликованные на сайте
		if ($oSite->safe_email && strlen($sContent) < 204800)
		{
			/**
			 * Strip \n, \r, \ in $text
			 * @param string $text text
			 * @return string
			 */
			function strip_nl($text)
			{
				$text = str_replace("\n", "", $text);
				$text = str_replace("\r", "", $text);
				$text = str_replace("'", "\'", $text);

				return $text;
			}

			/**
			 * Callback function
			 * функция обратного вызова
			 * @param array $matches matches
			 * @return string
			 */
			function safe_email_callback($matches)
			{
				ob_start();
				?><script type="text/javascript"><?php
				echo "//<![CDATA[\n";
				?><?php
				?>function hostcmsEmail(c){return c.replace(/[a-zA-Z]/g, function (c){return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c-26);})}<?php
				?>document.write ('<a <?php echo strip_nl($matches[1])?> href="mailto:' + hostcmsEmail('<?php echo strip_nl(str_rot13($matches[2]))?>') + '"<?php echo strip_nl($matches[3])?>>' + hostcmsEmail('<?php echo strip_nl(str_rot13($matches[4]))?>') + '</a>');<?php
				echo "//]]>\n";
				?></script><?php

				return ob_get_clean();
			}

			$sTmpContent = preg_replace_callback('/<a\s([^>]*)?href=[\'|\"]?mailto:([^\"|\']*)[\"|\']?([^>]*)?>(.*?)<\/a>/is', "safe_email_callback", $sContent); // без /u

			strlen($sTmpContent) && $sContent = $sTmpContent;
		}

		if (Core_Array::get($_SERVER, 'REQUEST_URI') == '/' && !((~Core_Array::get(Core::$config->get('core_hostcms'), 'hostcms')) & (~1835217467)) && strlen($sContent) < 204800)
		{
			$search = array (
				"'<script[^>]*?>.*?</script>'siu",
				"'<noscript[^>]*?>.*?</noscript>'siu",
				"'<style[^>]*?>.*?</style>'siu",
				"'<select[^>]*?>.*?</select>'siu",
				"'<head[^>]*?>.*?</head>'siu",
				"'<!--.*?-->'siu"
			);

			$sTmpContent = preg_replace($search, ' ', str_replace(array("\r", "\n"), " ", $sContent));

			$pattern_index = "(?<!noindex)(?<!display)(?<!visible)";
			$pat = "#<a([^>]{$pattern_index})*href=((\"http://(www.|)hostcms.ru(/|)\")|(http://(www.|)hostcms.ru(/|)))([^>]{$pattern_index})*>(.{3,})</a>#u";

			if (!Core_Auth::logged() && !preg_match_all($pat, $sTmpContent, $matches))
			{
				$sContent = '<div style="border: 1px solid #E83531; border-radius: 5px; background: #FEEFDA; text-align: center; clear: both; height: 100px; position: relative;' . (Core::checkPanel() ? 'margin-top: 38px;' : '') . '">
					<div style="position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;"><a href="#" onclick="javascript:this.parentNode.parentNode.style.display=\"none\"; return false;"><img src="/admin/images/wclose.gif" style="border: none;" alt="Close this notice"/></a></div>
					<div style="width: 740px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;"><div style="width: 75px; float: left"><img src="http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg" alt="Warning!"/></div>
					<div style="width: 600px; float: left; font-family: Arial, sans-serif"><div style="font-size: 14px; font-weight: bold; margin-top: 12px;">Нарушение п. 3.4 лицензионого договора присоединения</div>
					<div style="font-size: 12px; margin-top: 6px; line-height: 12px">Пользователь бесплатной редакции HostCMS.Халява обязуется разместить на каждом сайте, работающем с использованием Программного продукта, активную, индексируемую и видимую при просмотре сайта ссылку
					<div><b>' . htmlspecialchars('<a href="http://www.hostcms.ru" target="_blank">Система управления сайтом Host CMS</a>') . '</b></div> на сайт производителя <a href="http://www.hostcms.ru" target="_blank">http://www.hostcms.ru</a>.</div>
					</div>
					</div>
				</div>' . $sContent;
			}
		}

		if (!$bIsUtf8)
		{
			$sContent = $this->_iconv($oSite->coding, $sContent);
		}

		if (Core::moduleIsActive('cache') && $oSite->html_cache_use == 1)
		{
			$Core_Cache = Core_Cache::instance('static');

			// Проверяем, нужно ли очищать кэш
			if ($oSite->html_cache_clear_probability > 0 && rand(0, $oSite->html_cache_clear_probability) == 0)
			{
				// Очищаем кэш в статичных файлах для сайта
				//$Cache->ClearStaticCache($oSite->id);
				$Core_Cache->deleteAll($oSite->id);
			}

			if (
				(!isset($_SESSION) || !isset($_SESSION['siteuser_id']) && !Core_Auth::logged() && empty($_SESSION['SCART']))
				&& empty($_COOKIE['CART']) && count($_POST) == 0 && strlen($sContent) > 0
			)
			{
				$Core_Cache->insert($this->_uri, $sContent);
			}
		}
		//}

		$oCore_Response->body($sContent);

		// Top panel
		if (Core::checkPanel())
		{
			ob_start();

			$oSkin = Core_Skin::instance()
				->addCss('/hostcmsfiles/style.css')
				->showHead();

			$oHostcmsTopPanel = Core::factory('Core_Html_Entity_Div')
				->class('hostcmsPanel hostcmsTopPanel')
				;

			$oHostcmsSubPanel = Core::factory('Core_Html_Entity_Div')
				->class('hostcmsSubPanel hostcmsWindow')
				->add(
					Core::factory('Core_Html_Entity_Img')
						->width(3)->height(16)
						->src('/hostcmsfiles/images/drag_bg.gif')
				);

			$oHostcmsTopPanel->add($oHostcmsSubPanel);

			if ($bIsUtf8)
			{
				// Structure
				$sPath = '/admin/structure/index.php';
				$sAdditional = "hostcms[action]=edit&parent_id={$oStructure->parent_id}&hostcms[checked][0][{$oStructure->id}]=1";

				$oHostcmsSubPanel->add(
					Core::factory('Core_Html_Entity_A')
						->href("{$sPath}?{$sAdditional}")
						->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
						->add(
							Core::factory('Core_Html_Entity_Img')
								->width(16)->height(16)
								->src('/hostcmsfiles/images/structure_edit.gif')
								->id('hostcmsEditStructure')
								->alt(Core::_('Structure.edit_title'))
								->title(Core::_('Structure.edit_title'))
						)
				);

				// Template
				if ($oStructure->type == 0)
				{
					$oDocument_Version = $oStructure->Document->Document_Versions->getCurrent();
					$oTemplate = is_null($oDocument_Version)
						? NULL
						: $oDocument_Version->Template;
				}
				else
				{
					$oTemplate = $oStructure->Template;
				}

				if ($oTemplate && $oTemplate->id)
				{
					$sPath = '/admin/template/index.php';
					$sAdditional = "hostcms[action]=edit&hostcms[checked][1][{$oTemplate->id}]=1";

					$oHostcmsSubPanel->add(
						Core::factory('Core_Html_Entity_A')
						->href("{$sPath}?{$sAdditional}")
						->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
						->add(
							Core::factory('Core_Html_Entity_Img')
								->width(16)->height(16)
								->src('/hostcmsfiles/images/template_edit.gif')
								->id('hostcmsEditTemplate')
								->alt(Core::_('Template.title_edit', $oTemplate->name))
								->title(Core::_('Template.title_edit', $oTemplate->name))
						)
					);
				}

				// Document
				if ($oStructure->type == 0 && $oStructure->document_id)
				{
					$sPath = '/admin/document/index.php';
					$sAdditional = "hostcms[action]=edit&document_dir_id={$oStructure->Document->document_dir_id}&hostcms[checked][1][{$oStructure->Document->id}]=1";

					$oHostcmsSubPanel->add(
						Core::factory('Core_Html_Entity_A')
							->href("{$sPath}?{$sAdditional}")
							->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
							->add(
								Core::factory('Core_Html_Entity_Img')
									->width(16)->height(16)
									->src('/hostcmsfiles/images/page_edit.gif')
									->id('hostcmsEditDocument')
									->alt(Core::_('Document.edit'))
									->title(Core::_('Document.edit'))
							)
					);
				}

				// Informationsystem
				if (Core::moduleIsActive('informationsystem'))
				{
					$oInformationsystem = Core_Entity::factory('Informationsystem')
						->getByStructureId($oStructure->id);

					if ($oInformationsystem)
					{
						$sPath = '/admin/informationsystem/index.php';
						$sAdditional = "hostcms[action]=edit&informationsystem_dir_id={$oInformationsystem->informationsystem_dir_id}&hostcms[checked][1][{$oInformationsystem->id}]=1";

						$oHostcmsSubPanel->add(
							Core::factory('Core_Html_Entity_A')
								->href("{$sPath}?{$sAdditional}")
								->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
								->add(
									Core::factory('Core_Html_Entity_Img')
										->width(16)->height(16)
										->src('/hostcmsfiles/images/folder_page_edit.gif')
										->id('hostcmsEditInformationsystem')
										->alt(Core::_('Informationsystem.edit_title'))
										->title(Core::_('Informationsystem.edit_title'))
								)
						);
					}
				}

				// Shop
				if (Core::moduleIsActive('shop'))
				{
					$oShop = Core_Entity::factory('Shop')
						->getByStructureId($oStructure->id);

					if ($oShop)
					{
						$sPath = '/admin/shop/index.php';
						$sAdditional = "hostcms[action]=edit&shop_dir_id={$oShop->shop_dir_id}&hostcms[checked][1][{$oShop->id}]=1";

						$oHostcmsSubPanel->add(
							Core::factory('Core_Html_Entity_A')
								->href("{$sPath}?{$sAdditional}")
								->onclick("$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false")
								->add(
									Core::factory('Core_Html_Entity_Img')
										->width(16)->height(16)
										->src('/hostcmsfiles/images/shop_edit.gif')
										->id('hostcmsEditShop')
										->alt(Core::_('Shop.edit_title'))
										->title(Core::_('Shop.edit_title'))
								)
						);
					}
				}
			}

			// Separator
			$oHostcmsSubPanel->add(
				Core::factory('Core_Html_Entity_Span')
					->style('padding-left: 10px')
			)
			->add(
				Core::factory('Core_Html_Entity_A')
					->href('/admin/')
					->target('_blank')
					->add(
						Core::factory('Core_Html_Entity_Img')
							->width(16)->height(16)
							->src('/hostcmsfiles/images/system.gif')
							->id('hostcmsAdministrationCenter')
							->alt(Core::_('Core.administration_center'))
							->title(Core::_('Core.administration_center'))
					)
			);

			// Debug window
			ob_start();

			$fTotalTime = Core::getmicrotime() - $fBeginTime;

			$oDebugWindow = Core::factory('Core_Html_Entity_Div')
				->class('modalwindow')
				->add(
					Core::factory('Core_Html_Entity_Span')
						->value(Core::_('Core.total_time', $fTotalTime))
				);

			$oDebugWindow->add(
				Core::factory('Core_Html_Entity_Ul')
					->add(
						Core::factory('Core_Html_Entity_Li')
							->liValue(Core::_('Core.time_load_modules', Core_Type_Conversion::toStr($GLOBALS['MTime'])))
					)
					->add(
						Core::factory('Core_Html_Entity_Li')
							->liValue(Core::_('Core.time_page_generation', $oCore_Registry->get('Core_Statistics.pageGenerationTime', 0)))
					)
					->add(
						Core::factory('Core_Html_Entity_Li')
							->liValue(Core::_('Core.time_database_connection', $oCore_Registry->get('Core_DataBase.connectTime', 0)))
					)
					->add(
						Core::factory('Core_Html_Entity_Li')
							->liValue(Core::_('Core.time_database_select', $oCore_Registry->get('Core_DataBase.selectDbTime', 0)))
					)
					->add(
						Core::factory('Core_Html_Entity_Li')
							->liValue(Core::_('Core.time_sql_execution', $oCore_Registry->get('Core_DataBase.queryTime', 0)))
					)
					->add(
						Core::factory('Core_Html_Entity_Li')
							->liValue(Core::_('Core.time_xml_execution',$oCore_Registry->get('Xsl_Processor.process', 0)))
					)
			);

			if (function_exists('memory_get_usage') && substr(PHP_OS, 0, 3) != 'WIN')
			{
				$oDebugWindow->add(
					Core::factory('Core_Html_Entity_Div')
						->value(Core::_('Core.memory_usage', memory_get_usage() / 1048576))
				);
			}

			$oDebugWindow->add(
				Core::factory('Core_Html_Entity_Div')
					->value(Core::_('Core.number_of_queries', $oCore_Registry->get('Core_DataBase.queryCount', 0)))
			)
			->add(
				Core::factory('Core_Html_Entity_Div')
					->value(
						Core::_('Core.compression', (Core::moduleIsActive('compression')
							? Core::_('Core.enabled') : Core::_('Core.disabled')))
					)
			)
			->add(
				Core::factory('Core_Html_Entity_Div')
					->value(Core::_('Core.cache', (Core::moduleIsActive('cache')
						? Core::_('Core.enabled') : Core::_('Core.disabled'))))
			);

			if (Core::moduleIsActive('cache'))
			{
				$oDebugWindow->add(
					Core::factory('Core_Html_Entity_Ul')
						->add(
							Core::factory('Core_Html_Entity_Li')
								->liValue(Core::_('Core.cache_insert_time', $oCore_Registry->get('Core_Cache.setTime', 0)))
						)
						->add(
							Core::factory('Core_Html_Entity_Li')
								->liValue(Core::_('Core.cache_write_requests', $oCore_Registry->get('Core_Cache.setCount', 0)))
						)
						->add(
							Core::factory('Core_Html_Entity_Li')
								->liValue(Core::_('Core.cache_read_time', $oCore_Registry->get('Core_Cache.getTime', 0)))
						)
						->add(
							Core::factory('Core_Html_Entity_Li')
								->liValue(Core::_('Core.cache_read_requests', $oCore_Registry->get('Core_Cache.getCount', 0)))
						)
				);
			}
			$oDebugWindow->execute();
			$form_content = ob_get_clean();

			$oHostcmsSubPanel->add(
				Core::factory('Core_Html_Entity_Img')
					->src('/hostcmsfiles/images/chart_bar.gif')
					->id('hostcmsShowDebugWindow')
					->alt(Core::_('Core.debug_information'))
					->title(Core::_('Core.debug_information'))
					->class('pointer')
					->onclick("$.showWindow('debugWindow', '" . Core_Str::escapeJavascriptVariable($form_content) . "', {width: 400, height: 220, title: '" . Core::_('Core.debug_information') . "', Maximize: false})")
			);

			if (defined('ALLOW_SHOW_SQL') && ALLOW_SHOW_SQL)
			{
				// SQL window
				ob_start();

				$oSqlWindow = Core::factory('Core_Html_Entity_Div')
					->class('modalwindow');

				$aQueryLogs = $oCore_Registry->get('Core_DataBase.queryLog', array());

				if (is_array($aQueryLogs) && count($aQueryLogs) > 0)
				{
					$aTmp = array();

					$oCore_DataBase = Core_DataBase::instance();

					$aTdColors = array(
						'system' => '#008000',
						'const' => '#008000',
						'eq_ref' => '#D9E700',
						'ref' => '#E7B300',
						'range' => '#E78200',
						'index' => '#E76200',
						'all' => '#E70B00'
					);

					foreach ($aQueryLogs as $key => $aQueryLog)
					{
						$iCrc32 = crc32($aQueryLog['trimquery']);

						$sClassName = in_array($iCrc32, $aTmp)
							? 'sql_qd'
							: 'sql_q';

						$aTmp[] = $iCrc32;

						$oSqlWindow
							->add(
								Core::factory('Core_Html_Entity_Div')
									->class($sClassName)
									->value(
										$oCore_DataBase->highlightSql(htmlspecialchars($aQueryLog['query']))
									)
							);

						if (isset($aQueryLog['debug_backtrace']) && count($aQueryLog['debug_backtrace']) > 0)
						{
							$sdebugBacktrace = '';

							foreach ($aQueryLog['debug_backtrace'] as $history)
							{
								if (isset($history['file']) && isset($history['line']))
								{
									$sdebugBacktrace .= Core::_('Core.sql_debug_backtrace', Core_Exception::cutRootPath($history['file']), $history['line']);
								}
							}

							$oSqlWindow->add(
								Core::factory('Core_Html_Entity_Div')
									->class('sql_db')
									->id("sql_h{$key}")
									->value($sdebugBacktrace)
							);
						}

						$oSqlDivDescription = Core::factory('Core_Html_Entity_Div')
							->class('sql_t')
							->value(Core::_('Core.sql_statistics', $aQueryLog['time'], $key));

						if (isset($aQueryLog['explain']) && count($aQueryLog['explain']) > 0)
						{
							$oSqlDivDescription
								->add(
									Core::factory('Core_Html_Entity_Div')
										->value('Explain:')
								);

							$oExplainTable = Core::factory('Core_Html_Entity_Table')
								->class('sql_explain');

							$oExplainTableTr = Core::factory('Core_Html_Entity_Tr');
							$oExplainTable->add($oExplainTableTr);

							foreach ($aQueryLog['explain'][0] as $explain_key => $aExplain)
							{
								$oExplainTableTr
									->add(
										Core::factory('Core_Html_Entity_Td')
											->add(
												Core::factory('Core_Html_Entity_Strong')
													->value($explain_key)
											)
									);
							}

							foreach ($aQueryLog['explain'] as $aExplain)
							{
								$oExplainTableTr = Core::factory('Core_Html_Entity_Tr');

								foreach ($aExplain as $sExplainKey => $sExplainValue)
								{
									$oExplainTableTd = Core::factory('Core_Html_Entity_Td');

									if ($sExplainKey == 'type')
									{
										$sIndexName = strtolower($sExplainValue);

										$color = isset($aTdColors[$sIndexName])
											? $aTdColors[$sIndexName]
											: '#777777';

										$oExplainTableTd->style("color: {$color}");
									}

									$oExplainTableTr
										->add($oExplainTableTd)
										->value(str_replace(',', ', ', $sExplainValue));
								}
							}
						}

						$oSqlWindow->add($oSqlDivDescription);
					}
					unset($aTmp);
				}

				$oSqlWindow->execute();
				$form_content = ob_get_clean();

				$oHostcmsSubPanel->add(
					Core::factory('Core_Html_Entity_Img')
						->src('/hostcmsfiles/images/sql.gif')
						->id('hostcmsShowSql')
						->alt(Core::_('Core.sql_queries'))
						->title(Core::_('Core.sql_queries'))
						->class('pointer')
						->onclick("$.showWindow('sqlWindow', '" . Core_Str::escapeJavascriptVariable($form_content) . "', {width: '70%', height: 500, title: '" . Core::_('Core.sql_queries') . "'})")
				);
			}

			if (defined('ALLOW_SHOW_XML') && ALLOW_SHOW_XML)
			{
				$oHostcmsSubPanel->add(
					Core::factory('Core_Html_Entity_A')
						->href(
							'?hostcmsAction=' . (Core_Type_Conversion::toBool($_SESSION['HOSTCMS_SHOW_XML'])
							? 'HIDE_XML'
							: 'SHOW_XML')
						)
						->add(
							Core::factory('Core_Html_Entity_Img')
								->width(16)->height(16)
								->src('/hostcmsfiles/images/xsl.gif')
								->id('hostcmsXml')
								->alt(Core::_(
									Core_Type_Conversion::toBool($_SESSION['HOSTCMS_SHOW_XML'])
										? 'Core.hide_xml'
										: 'Core.show_xml'
								))
								->title(Core::_(
									Core_Type_Conversion::toBool($_SESSION['HOSTCMS_SHOW_XML'])
										? 'Core.hide_xml'
										: 'Core.show_xml'
								))
						)
				);
			}

			$oHostcmsSubPanel->add(
				// Separator
				Core::factory('Core_Html_Entity_Span')
					->style('padding-left: 10px')
			)
			->add(
				Core::factory('Core_Html_Entity_A')
					->href('/admin/logout.php')
					->onclick("$.ajax({url: '/admin/logout.php', dataType: 'html', success: function() {location.reload()}}); return false;")
					->add(
						Core::factory('Core_Html_Entity_Img')
							->width(16)->height(16)
							->src('/hostcmsfiles/images/exit.gif')
							->id('hostcmsLogout')
							->alt(Core::_('Core.logout'))
							->title(Core::_('Core.logout'))
					)
			);

			$oHostcmsTopPanel
				->add(
					Core::factory('Core_Html_Entity_Script')
						->type('text/javascript')
						->value(
							'(function($){' .
							'$("body").addClass("backendBody");' .
							'$(".hostcmsPanel").applyShadow();' .
							'$(".hostcmsPanel").draggable({containment: "document"});' .
							'$("*[hostcms\\\\:id]").hostcmsEditable({path: "/edit-in-place.php"});'.
							'})(jQuery)'
						)
				);

			$oHostcmsTopPanel->execute();

			$sContent = ob_get_clean();

			if (!$bIsUtf8)
			{
				$sContent = $this->_iconv($oSite->coding, $sContent);
			}

			$oCore_Response->body($sContent);
		}

		Core_Event::notify(get_class($this) . '.onAfterShowAction', $this, array($oCore_Response));

		return $oCore_Response;
	}

	/**
	 * Get Structure_Model which satisfy URI $path
	 * @param string $path URI
	 * @param int $site_id site ID
	 * @return Structure_Model
	 */
	public function getStructure($path, $site_id)
	{
		$aPath = explode('/', trim($path, '/'));

		// Index page
		if (count($aPath) == 1 && $aPath[0] == '')
		{
			$aPath[0] = '/';
		}

		$oSite = Core_Entity::factory('Site', $site_id);

		$bINDEX_PAGE_IS_DEFAULT = defined('INDEX_PAGE_IS_DEFAULT') && INDEX_PAGE_IS_DEFAULT;

		$parent_id = 0;
		foreach ($aPath as $sPath)
		{
			$oStructure = $oSite->Structures
				->getByPathAndParentId($sPath, $parent_id);

			if (!is_null($oStructure) && $oStructure->active == 1)
			{
				$parent_id = $oStructure->id;
			}
			else
			{
				$oStructure = Core_Entity::factory('Structure')->find($parent_id);

				if (!$bINDEX_PAGE_IS_DEFAULT &&
					(is_null($oStructure->id) || $oStructure->type == 0))
				{
					// Узел структуры не найден
					return NULL;
				}

				// Прерываем, если у страницы нет таких дочерних
				break;
			}
		}

		// Обработчик и константа необходима на случай размещения инфосистемы на главной страницы
		if ($bINDEX_PAGE_IS_DEFAULT && $parent_id == 0)
		{
			// Получаем ID главной страницы
			$oStructure = $oSite->Structures->getByPath('/');
		}

		return $oStructure;
	}

	/**
	 * Convert string to requested character encoding
	 * @param string $out_charset The output charset.
	 * @param string $content The string to be converted.
	 * @return string
	 */
	protected function _iconv($out_charset, $content)
	{
		// Delete BOM (EF BB BF)
		//$sContent = str_replace(chr(0xEF) . chr(0xBB) . chr(0xBF), '', $sContent);
		return @iconv('UTF-8', $out_charset . '//IGNORE//TRANSLIT', $content);
	}
}