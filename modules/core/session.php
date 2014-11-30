<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Sessions
 *
 * @package HostCMS 6\Core
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Session
{
	/**
	 * Lock prefix
	 * @var string
	 */
	protected $_lockPrefix = NULL;

	/**
	 * GET_LOCK timeout (sec)
	 * @var int
	 */
	protected $_getLockTimeout = 1;

	/**
	 * Next step delay (microseconds)
	 * Default 0,5 sec - 500000 microseconds
	 * @var int
	 */
	protected $_nextStepDelay = 500000;

	/**
	 * Lock timeout
	 * @var int
	 */
	protected $_lockTimeout = 10;

	/**
	 * DataBase instance
	 * @var Core_DataBase
	 */
	protected $_dataBase = NULL;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->_dataBase = Core_DataBase::instance();

		if ($this->_lockPrefix === NULL)
		{
			$aDataBaseConfig = $this->_dataBase->getConfig();
			$this->_lockPrefix = $aDataBaseConfig['database'] . '_' . 'sessions';
		}
	}

	/**
	 * Session has been started
	 * @var boolean
	 */
	static protected $_started = FALSE;

	/**
	 * Callback function
	 * @return boolean
	 */
	static public function start()
	{
		if (!self::$_started)
		{
			$oCore_Session = new self();
			session_set_save_handler(
				array($oCore_Session, 'sessionOpen'),
				array($oCore_Session, 'sessionClose'),
				array($oCore_Session, 'sessionRead'),
				array($oCore_Session, 'sessionWrite'),
				array($oCore_Session, 'sessionDestroyer'),
				array($oCore_Session, 'sessionGc')
			);

			//$expires = self::getMaxLifeTime();
			$expires = 31536000;

			if (!defined('DENY_INI_SET') || !DENY_INI_SET)
			{
				// Период хранения куки в секундах. Значение 0 означает "пока браузер не будет закрыт"
				ini_set('session.cookie_lifetime', $expires);
				//ini_set('session.gc_maxlifetime', $expires);
			}

			$domain = strtolower(Core_Array::get($_SERVER, 'HTTP_HOST'));
			if (!empty($domain) && !headers_sent())
			{
				// Обрезаем www у домена
				strpos($domain, 'www.') === 0 && $domain = substr($domain, 4);

				// Явное указание domain возможно только для домена второго и более уровня
				// http://wp.netscape.com/newsref/std/cookie_spec.html
				// http://web-notes.ru/2008/07/cookies_within_local_domains/
				$domain = strpos($domain, '.') !== FALSE && !Core_Valid::ip($domain)
					? '.' . $domain
					: '';

				session_set_cookie_params($expires, '/', $domain);
			}

			// При повторном запуске $_SESSION уже будет
			//if (Core_Array::getRequest(session_name())/* && !isset($_SESSION)*/)
			//{
				@session_start();
				self::$_started = TRUE;
			//}

			//self::_setCookie();
		}

		return TRUE;
	}

	/**
	 * Set cookie with expiration date
	 */
	/*static protected function _setCookie()
	{
		$domain = strtolower(Core_Array::get($_SERVER, 'HTTP_HOST'));
		if (!empty($domain) && !headers_sent())
		{
			// Обрезаем www у домена
			strpos($domain, 'www.') === 0 && $domain = substr($domain, 4);

			// Явное указание domain возможно только для домена второго и более уровня
			// http://wp.netscape.com/newsref/std/cookie_spec.html
			// http://web-notes.ru/2008/07/cookies_within_local_domains/
			$domain = strpos($domain, '.') !== FALSE && !Core_Valid::ip($domain)
				? '.' . $domain
				: '';

			$expires = self::getMaxLifeTime();

			setcookie(session_name(), session_id(), time() + $expires, '/', $domain);

			// Заменяем заголовок ($replace = TRUE)
			//Core::setcookie(session_name(), session_id(), time() + $expires, '/', $domain, FALSE, TRUE, $replace = TRUE);
			//session_set_cookie_params(time() + $expires, '/', $domain);
			//session_id(session_id());
		}
	}*/

	/**
	 * Callback function
	 * @return boolean
	 */
	static public function close()
	{
		//if (self::$_started)
		//{
			self::$_started = FALSE;
			session_write_close();
		//}
		return FALSE;
	}

	/**
	 * The open callback works like a constructor in classes and is executed when the session is being opened.
	 * @param string $save_path save path
	 * @param string $session_name session name
	 * @return boolean
	 */
	public function sessionOpen($save_path, $session_name)
	{
		return TRUE;
	}

	/**
	 * The close callback works like a destructor in classes and is executed after the session write callback has been called.
	 * @return boolean
	 */
	public function sessionClose()
	{
		return TRUE;
	}

	/**
	 * Lock session
	 * @param int $id session ID
	 * @return boolean
	 */
	protected function _lock($id)
	{
		$iStartTime = time();

		while (TRUE)
		{
			$oDataBase = $this->_dataBase->setQueryType(0)
				->query('SELECT GET_LOCK("' . $this->_dataBase->quote($this->_lockPrefix . '_' . $id) . '",'
				. intval($this->_getLockTimeout) . ') AS `lock`');

			$row = $oDataBase->asAssoc()->current();

			if (!is_array($row))
			{
				exit('HostCMS session lock error: Get row failure');
			}

			if (isset($row['lock']) && $row['lock'] == 1)
			{
				// Блокировка удалась
				break;
			}

			// Время, прошедшее с начала попытки блокировки
			$iTime = time() - $iStartTime;

			if ($iTime > $this->_lockTimeout)
			{
				exit('HostCMS session lock error: Timeout');
			}

			usleep($this->_nextStepDelay);
		}

		return TRUE;
	}

	/**
	 * Unlock session
	 * @param int $id session ID
	 * @return boolean
	 */
	protected function _unlock($id)
	{
		$oDataBase = $this->_dataBase->setQueryType(0)
			->query('SELECT RELEASE_LOCK("' . $this->_dataBase->quote($this->_lockPrefix . '_' . $id) . '") AS `lock`');

		$row = $oDataBase->asAssoc()->current();

		if (!is_array($row))
		{
			exit('HostCMS session unlock error: Get row failure');
		}

		return TRUE;
	}

	/**
	 * The read callback must always return a session encoded (serialized) string, or an empty string if there is no data to read.
	 * @param string $id session ID
	 * @return boolean
	 */
	public function sessionRead($id)
	{
		$this->_lock($id);

		$queryBuilder = Core_QueryBuilder::select('value')
			->from('sessions')
			->where('id', '=', $id)
			->limit(1);

		$row = $queryBuilder->execute()->asAssoc()->current();

		if ($row)
		{
			// Update last change time
			Core_QueryBuilder::update('sessions')
				//->columns(array('time' => 'UNIX_TIMESTAMP(NOW())'))
				->columns(array('time' => time()))
				->where('id', '=', $id)
				->execute()
				;

			return $row['value'];
		}

		return FALSE;
	}

	/**
	 * Session maxlifetime
	 * @var int
	 */
	static protected $_maxlifetime = NULL;

	/**
	 * Set session maxlifetime
	 * @param int $maxlifetime
	 * @return TRUE
	 */
	static public function setMaxLifeTime($maxlifetime)
	{
		self::$_maxlifetime = $maxlifetime;

		if (!defined('DENY_INI_SET') || !DENY_INI_SET)
		{
			ini_set('session.gc_maxlifetime', $maxlifetime);
		}

		// Для уже запущенной сесии обновляем время жизни
		if (self::$_started)
		{
			$id = session_id();

			Core_QueryBuilder::update('sessions')
				->set('maxlifetime', $maxlifetime)
				->where('id', '=', $id)
				->execute();

			// Set cookie with expiration date
			//self::_setCookie();
		}

		return TRUE;
	}

	/**
	 * Get session maxlifetime
	 * @return int
	 */
	static public function getMaxLifeTime()
	{
		return !is_null(self::$_maxlifetime)
			? intval(self::$_maxlifetime)
			: intval(ini_get('session.gc_maxlifetime'));
	}

	/**
	 * The write callback is called when the session needs to be saved and closed.
	 * @param string $id session ID
	 * @param string $value data
	 * @return boolean
	 */
	public function sessionWrite($id, $value)
	{
		$this->_lock($id);

		$oDataBase = Core_QueryBuilder::update('sessions')
				//->columns(array('time' => 'UNIX_TIMESTAMP(NOW())'))
				->set('value', $value)
				->set('time', time())
				->where('id', '=', $id)
				->execute();

		if ($oDataBase->getAffectedRows() == 0)
		{
			$maxlifetime = self::getMaxLifeTime();

			Core_QueryBuilder::insert('sessions')
				->ignore()
				->columns('id', 'value', 'time', 'maxlifetime')
				->values($id, $value, time(), $maxlifetime)
				->execute();
		}

		$this->_unlock($id);

		return TRUE;
	}

	/**
	 * This callback is executed when a session is destroyed with session_destroy()
	 * @param string $id session ID
	 * @return boolean
	 */
	public function sessionDestroyer($id)
	{
		$this->_lock($id);

		Core_QueryBuilder::delete('sessions')
			->where('id', '=', $id)
			->execute();

		// для предотвращения автоматической повторной регистрации сеанса
		$_SESSION = array();

		return TRUE;
	}

	/**
	 * The garbage collector callback is invoked internally by PHP periodically in order to purge old session data.
	 * @param string $maxlifetime max life time
	 * @return boolean
	 */
	public function sessionGc($maxlifetime)
	{
		Core_QueryBuilder::delete('sessions')
			->where('time + maxlifetime', '<', time())
			->execute();

		return TRUE;
	}
}