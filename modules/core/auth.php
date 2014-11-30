<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * HostCMS administration center authorization
 *
 * @package HostCMS 6\Core
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Auth
{
	/**
	 * Authorization
	 * @param string $moduleName name of the module
	 */
	static public function authorization($moduleName)
	{
		self::systemInit();

		if (!is_array($moduleName))
		{
			$aModuleNames = array($moduleName);
		}

		$sModuleName = implode(', ', $aModuleNames);

		if (!self::logged())
		{
			Core_Log::instance()->clear()
				->status(Core_Log::$ERROR)
				->write(Core::_('Core.error_log_attempt_to_access', $sModuleName));

			if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])
				&& Core_Type_Conversion::toBool($_SESSION['HOSTCMS_HTTP_AUTH_FLAG']) == TRUE)
			{
				ob_start();

				try
				{
					// При HTTP-Авторизации сессию привязываем к IP
					self::login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $assignSessionToIp = TRUE);
				}
				catch (Exception $e)
				{
					Core_Message::show($e->getMessage(), 'error');
				}

				$message = ob_get_clean();
			}
			else
			{
				$message = '';
			}

			if (!self::logged())
			{
				header("Pragma: no-cashe");
				header("WWW-authenticate: basic realm='HostCMS'");
				header("HTTP/1.0 401 Unauthorized");

				// Нужен старт сессии, чтобы записать в нее HOSTCMS_HTTP_AUTH_FLAG
				if (@session_id() == '')
				{
					@session_start();
				}

				// Флаг начала HTTP-авторизации
				$_SESSION['HOSTCMS_HTTP_AUTH_FLAG'] = TRUE;

				ob_start();
				?><h1><?php echo Core::_('Core.error_log_access_was_denied', $sModuleName)?></h1><?php
				$content = ob_get_clean();

				// Выводим страницу, которая отобразится, если пользователь нажмет "Отмена"
				$title = Core::_('Core.error_log_access_was_denied', $sModuleName);

				$oAdmin_Answer = Core_Skin::instance()->answer();
				$oAdmin_Answer
					->ajax(Core_Array::getRequest('_', FALSE))
					->content($content)
					->message($message)
					->title($title)
					->execute();

				exit();
			}

			// Флаг того, что окно авторизации было выведено удаляем
			$_SESSION['HOSTCMS_HTTP_AUTH_FLAG'] = FALSE;
			unset($_SESSION['HOSTCMS_HTTP_AUTH_FLAG']);
		}

		try
		{
			// Устанавливаем текущий сайт
			self::setCurrentSite();

			$oUser = Core_Entity::factory('User')->getByLogin(
				$_SESSION['valid_user']
			);

			if (is_null($oUser))
			{
				unset($_SESSION['valid_user']);
				throw new Core_Exception(
					'User not found, please relogin.'
				);
			}

			$oSite = Core_Entity::factory('Site', $_SESSION['current_site_id']);

			$allow_access = $oUser->checkModuleAccess($aModuleNames, $oSite);

			if (!$allow_access)
			{
				$sModuleName = implode(', ', $aModuleNames);
				$sMessage = Core::_('Core.error_log_access_was_denied', $sModuleName);

				Core_Log::instance()->clear()
					->status(Core_Log::$NOTICE)
					->write($sMessage);

				$oAdmin_Answer = Core_Skin::instance()->answer();
				$oAdmin_Answer
						->ajax(Core_Array::getRequest('_', FALSE))
						->content(Core_Message::get($sMessage, 'error'))
						//->message($sMessage)
						->title($sMessage)
						->execute();

				exit();
			}

			// Имя формы
			$sFormName = Core::_('Core.default_form_name');

			// Имя действия
			$sEventName = Core::_('Core.default_event_name');

			$aHostCMS = Core_Array::getRequest('hostcms', array());

			if (isset($aHostCMS['action']))
			{
				$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action')->getByName($aHostCMS['action']);

				if ($oAdmin_Form_Action)
				{
					$oAdmin_Word_Value = $oAdmin_Form_Action->Admin_Word->getWordByLanguage();

					if ($oAdmin_Word_Value)
					{
						$sEventName = $oAdmin_Word_Value->name;
					}

					// Название формы для действия
					$oAdmin_Word_Value = $oAdmin_Form_Action->Admin_Form->Admin_Word->getWordByLanguage();

					if ($oAdmin_Word_Value)
					{
						$sFormName = $oAdmin_Word_Value->name;
					}
				}
			}
		}
		catch (Exception $e)
		{
			$oAdmin_Answer = Core_Skin::instance()->answer();
			$oAdmin_Answer
				->ajax(Core_Array::getRequest('_', FALSE))
				->message(
					Core_Message::get($e->getMessage(), 'error')
				)
				->title($e->getMessage())
				->execute();

			exit();
		}

		Core_Log::instance()->clear()
			->status(Core_Log::$SUCCESS)
			->write(Core::_('Core.error_log_access_allowed', $moduleName, $sFormName, $sEventName));

		Core_Session::close();
	}

	/**
	 * System initialization
	 */
	static public function systemInit()
	{
		Core_Event::notify('Core_Auth.onBeforeSystemInit');

		// Если не используется HTTPS-доступ
		if (defined('USE_ONLY_HTTPS_AUTHORIZATION') && !Core::httpsUses())
		{
			$url = strtolower(Core_Array::get($_SERVER, 'HTTP_HOST')) . $_SERVER['REQUEST_URI'];

			$url = str_replace ("\r", '', $url);
			$url = str_replace ("\n", '', $url);

			header("HTTP/1.1 302 Found");
			header("Location: https://{$url}");

			exit();
		}

		header("Content-type: text/html; charset=UTF-8");

		if (!defined('DENY_INI_SET') || !DENY_INI_SET)
		{
			ini_set('display_errors', 1);
		}

		// Если есть ID сессии и сессия еще не запущена - то стартуем ее,
		// первоначальный старт осуществляется при авторизации пользователя
		self::adminSessionStart();

		define('IS_ADMIN_PART', TRUE);

		$lng = Core_Array::get($_SESSION, 'current_lng');

		// если получен язык - пишем в сессию
		isset($_REQUEST['lng_value']) && $lng = strval($_REQUEST['lng_value']);

		// Выбираем
		empty($lng) && $lng = strtolower(htmlspecialchars(
			substr(Core_Array::get($_SERVER, 'HTTP_ACCEPT_LANGUAGE'), 0, 2)
		));

		$oAdmin_Language = Core_Entity::factory('Admin_Language')->getByShortname($lng);
		!$oAdmin_Language && $lng = NULL;

		// Записываем в сессию язык, содержащийся в константе
		$_SESSION['current_lng'] = !is_null($lng) ? $lng : DEFAULT_LNG;

		// Устанавливаем полученный язык
		Core_I18n::instance()->setLng($_SESSION['current_lng']);
		define('CURRENT_LNG', $_SESSION['current_lng']);

		$oAdmin_Language = Core_Entity::factory('Admin_Language')->getByShortname(CURRENT_LNG);
		$cur_lng_id = ($oAdmin_Language->active == 1) ? $oAdmin_Language->id : 0;

		define("CURRENT_LANGUAGE_ID", $cur_lng_id);

		Core_Event::notify('Core_Auth.onAfterSystemInit');
	}

	/**
	 * Starts the session
	 */
	static public function adminSessionStart()
	{
		Core_Session::setMaxLifeTime(14400);
		Core_Session::start();
	}

	/**
	 * Checks user's authorization
	 * Проверка авторизации пользователя
	 * @return boolean
	 */
	static public function logged()
	{
		return
			(
				// Привязки к IP не было или IP совпадают
				!isset($_SESSION['current_user_ip']) || $_SESSION['current_user_ip'] == Core_Array::get($_SERVER, 'REMOTE_ADDR', '127.0.0.1')
			)
			&& isset($_SESSION['valid_user']) && strlen($_SESSION['valid_user']) > 0
			&& isset($_SESSION['date_user']) && strlen($_SESSION['date_user']) > 0
			&& isset($_SESSION['current_users_id']) && $_SESSION['current_users_id'] > 0
			&& isset($_SESSION['is_superuser']);
	}

	/**
	 * Метод устанавливает текущий сайт, обрабатывает изменение текущего сайта
	 */
	static public function setCurrentSite()
	{
		Core_Event::notify('Core_Auth.onBeforeSetCurrentSite');

		// Выполняем только после регистрации пользователя
		if (self::logged())
		{
			// Выбранный в меню сайт
			$iSelectedSite = Core_Array::getGet('changeSiteId');

			if (!is_null($iSelectedSite))
			{
				$_SESSION['current_site_id'] = intval($iSelectedSite);
			}

			// Если нет выбранного сайта
			if (!isset($_SESSION['current_site_id']))
			{
				$domain = strtolower(Core_Array::get($_SERVER, 'HTTP_HOST'));
				$oSiteAlias = Core_Entity::factory('Site_Alias')->getByName($domain);

				if (!is_null($oSiteAlias))
				{
					$site_id = $oSiteAlias->site_id;
				}
				else
				{
					$oUser = Core_Entity::factory('User')->getByLogin($_SESSION['valid_user']);

					if (is_null($oUser->id))
					{
						exit('User does not exist!');
					}

					// Для суперпользователя выбираем все сайты
					if ($oUser->superuser == 1)
					{
						$oSite = Core_Entity::factory('Site')->getFirstSite();
						$site_id = $oSite->id;
					}
					else
					{
						$oSites = Core_Entity::factory('Site');

						$oSites->queryBuilder()
							->select('sites.*')
							->join('user_modules', 'user_modules.site_id', '=', 'sites.id')
							->where('user_modules.user_group_id', '=', $oUser->user_group_id)
							->groupBy('sites.id')
							->limit(1);

						$aSites = $oSites->findAll();

						$site_id = isset($aSites[0])
							? $aSites[0]->id
							: NULL;
					}
				}

				if (!$site_id)
				{
					exit('Site does not exist! Check aliases and permissions for a users.');
				}

				// Заносим значение в сессию
				$_SESSION['current_site_id'] = $site_id;
			}

			// Определяем константу
			if (!defined('CURRENT_SITE'))
			{
				define('CURRENT_SITE', $_SESSION['current_site_id']);
			}
		}

		Core_Event::notify('Core_Auth.onAfterSetCurrentSite');
	}

	/**
	 * Метод производит авторизацию пользователя в разделе администрирования
	 *
	 * @param string $login логин
	 * @param string $password пароль
	 * @param boolean $assignSessionToIp привязать сессию к IP-адресу
	 * @return mixed
	 * <br />true -- автооризация произведена успешно
	 * <br />false -- неправильные данные доступа
	 * <br />-1 -- не истекло время до следующей попытки авторизации
	 */
	static public function login($login, $password, $assignSessionToIp = TRUE)
	{
		Core_Event::notify('Core_Auth.onBeforeLogin', NULL, array($login));

		$error_admin_access = false;

		$timestamp = time();

		// выбираем информацию по данному пользователю за последние 24 часа из таблицы неудачных входов
		$aUser_Accessdenieds = Core_Entity::factory('User_Accessdenied')->getByIp(Core_Array::get($_SERVER, 'REMOTE_ADDR', '127.0.0.1'));

		// Получаем количество неудачных попыток
		$count_unsuccess_denied = count($aUser_Accessdenieds);

		// Были ли у данного пользователя неудачные попытки входа в систему администрирования за последние 24 часа?
		if ($count_unsuccess_denied > 0)
		{
			$oUser_Accessdenied = $aUser_Accessdenieds[0];

			// определяем интервал времени между последней неудачной попыткой входа в систему
			// и текущим временем входа в систему
			$delta = $timestamp - Core_Date::sql2timestamp($oUser_Accessdenied->datetime);

			// определяем период времени, в течении которого пользователю, имевшему неудачные
			// попытки доступа в систему запрещен вход в систему
			$delta_access_denied = $count_unsuccess_denied > 2
				? 5 * exp(2 * log($count_unsuccess_denied - 1))
				: 5;

			// если период запрета доступа в систему не истек
			if ($delta_access_denied > $delta)
			{
				throw new Core_Exception(
					Core::_('Admin.authorization_error_access_temporarily_unavailable'),
						array('%s' => round($delta_access_denied - $delta)), 0, $bShowDebugTrace = FALSE
				);
			}
		}

		if (strlen($login) > 255)
		{
			return FALSE;
		}

		$oUser = Core_Entity::factory('User')->getByLoginAndPassword($login, $password);

		if ($oUser)
		{
			/*If the server time is not properly set, e.g(it is behind the client time).    Excution of the following code
			session_set_cookie_params(2000);
			will NOT set/send cookie to  Internet Explorer 6.0,*/
			/*
			$expiry = 60*60*4;
			setcookie(session_name(),session_id(), time()+$expiry, "/");
			*/
			/****/

			// Сессия может быть уже запущена и при повторном отправке данных POST-ом при авторизации
			//if (!isset($_SESSION['valid_user']))
			if (@session_id() == '')
			{
				Core_Session::start();
			}

			// Записываем ID пользователя
			$_SESSION["current_users_id"] = $oUser->id;
			$_SESSION["valid_user"] = $oUser->login;
			$_SESSION["date_user"] = date("d.m.Y H:i:s");
			$_SESSION["is_superuser"] = $oUser->superuser;

			if ($assignSessionToIp)
			{
				$_SESSION["current_user_ip"] = Core_Array::get($_SERVER, 'REMOTE_ADDR', '127.0.0.1');
			}

			Core_Log::instance()->clear()
				->status(Core_Log::$ERROR)
				->notify(FALSE)
				->write(Core::_('Core.error_log_logged'));

			// Удаление всех неудачных попыток входа систему за период ранее 24 часов с момента удачного входа в систему
			$oUser_Accessdenied = Core_Entity::factory('User_Accessdenied');
			$oUser_Accessdenied->queryBuilder()
				->clear()
				->where('datetime', '<', Core_Date::timestamp2sql(time() - 86400))
				// Удаляем все попытки доступа с текущего IP
				->setOr()
				->where('ip', '=', Core_Array::get($_SERVER, 'REMOTE_ADDR', '127.0.0.1'));

			$aUser_Accessdenieds = $oUser_Accessdenied->findAll();
			foreach ($aUser_Accessdenieds as $oUser_Accessdenied)
			{
				$oUser_Accessdenied->delete();
			}
		}
		else
		{
			// Запись в базу об ошибке доступа
			$oUser_Accessdenied = Core_Entity::factory('User_Accessdenied');
			$oUser_Accessdenied->datetime = Core_Date::timestamp2sql($timestamp);
			$oUser_Accessdenied->ip = Core_Array::get($_SERVER, 'REMOTE_ADDR', '127.0.0.1');
			$oUser_Accessdenied->save();

			Core_Log::instance()->clear()
				->status(Core_Log::$ERROR)
				->notify(FALSE)
				->write(Core::_('Core.error_log_authorization_error'));

			return FALSE;
		}

		Core_Event::notify('Core_Auth.onAfterLogin', NULL, array($login));

		return TRUE;
	}
}