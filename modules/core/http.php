<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Abstract HTTP
 *
 * @package HostCMS 6\Core\Http
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
abstract class Core_Http
{
	/**
	 * Get full driver name
	 * @param string $driver driver name
	 * @return string
	 */
	static protected function _getDriverName($driver)
	{
		return __CLASS__ . '_' . ucfirst($driver);
	}

	/**
	 * Register an existing instance as a singleton.
	 * @param string $name
	 * @return object
	 */
	static public function instance($name = 'default')
	{
		if (!is_string($name))
		{
			throw new Core_Exception('Wrong argument type (expected String)');
		}

		$aConfig = Core::$config->get('core_http');

		if (!isset($aConfig[$name]) || !isset($aConfig[$name]['driver']))
		{
			throw new Core_Exception('Core_Http "%name" configuration doesn\'t defined', array('%name' => $name));
		}

		$aConfig[$name] += array('options' => array());

		$driver = self::_getDriverName($aConfig[$name]['driver']);
		$oDriver = new $driver($aConfig[$name]);

		return $oDriver->config($aConfig[$name]);
	}

	/**
	 * Additional parameters
	 * @var array
	 */
	protected $_config = array();

	/**
	 * Set additional parameters
	 * @param array $array params
	 * @return self
	 */
	public function config($array)
	{
		$this->_config = $array;
		return $this;
	}

	/**
	 * Additional headers
	 * @var array
	 */
	protected $_additionalHeaders = array();

	/**
	 * Add additional headers
	 * @param string $name Header name
	 * @param string $value Value
	 * @return self
	 */
	public function additionalHeader($name, $value)
	{
		$this->_additionalHeaders[$name] = $value;
		return $this;
	}

	/**
	 * Send request
	 * @param string $host host
	 * @param string $path path
	 * @param string $query query
	 * @return mixed
	 */
	abstract protected function _execute($host, $path, $query);

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->_init();
	}

	/**
	 * Clear object
	 *
	 * @return self
	 */
	public function clear()
	{
		$this->_additionalHeaders = $this->_data = array();
		$this->_url = $this->_referer = $this->_headers = $this->_body = NULL;

		return $this->_init();
	}

	/**
	 * Initialize object
	 * @return self
	 */
	protected function _init()
	{
		$this
			->userAgent('Mozilla/5.0 (compatible; HostCMS/6.x; +http://www.hostcms.ru)')
			->method('GET')
			->timeout(10)
			->port(80)
			->contentType('application/x-www-form-urlencoded');

		$this
			->additionalHeader('Accept-Charset', 'windows-1251,utf-8;q=0.7,*;q=0.7')
			->additionalHeader('Accept-Language', 'ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3')
			//->additionalHeader('Keep-Alive', '300')
			->additionalHeader('Accept', 'text/html,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,**;q=0.5');

		return $this;
	}

	/**
	 * Request User-Agent
	 * @var string
	 */
	protected $_userAgent = NULL;

	/**
	 * Set HTTP User-Agent
	 * @param string $userAgent User-Agent
	 * @return self
	 */
	public function userAgent($userAgent)
	{
		$this->_userAgent = $userAgent;
		return $this;
	}

	/**
	 * Request URL
	 * @var string
	 */
	protected $_url = NULL;

	/**
	 * Set URL
	 * @param string $url URL
	 * @return self
	 */
	public function url($url)
	{
		$this->_url = $url;
		return $this;
	}

	/**
	 * Request referer
	 * @var string
	 */
	protected $_referer = NULL;

	/**
	 * Set HTTP referer
	 * @param string $referer referer
	 * @return self
	 */
	public function referer($referer)
	{
		$this->_referer = $referer;
		return $this;
	}

	/**
	 * Request method
	 * @var string
	 */
	protected $_method = NULL;

	/**
	 * Set HTTP method
	 * @param string $method method
	 * @return self
	 */
	public function method($method)
	{
		$this->_method = $method;
		return $this;
	}

	/**
	 * Request time out
	 * @var string
	 */
	protected $_timeout = NULL;

	/**
	 * Set timeout
	 * @param string $timeout timeout
	 * @return self
	 */
	public function timeout($timeout)
	{
		$this->_timeout = $timeout;
		return $this;
	}

	/**
	 * Request port
	 * @var int
	 */
	protected $_port = NULL;

	/**
	 * Set port
	 * @param string $port port
	 * @return self
	 */
	public function port($port)
	{
		$this->_port = $port;
		return $this;
	}

	/**
	 * Request Content-type
	 * @var string
	 */
	protected $_contentType = NULL;

	/**
	 * Set Content-type
	 * @param string $contentType content type
	 * @return self
	 */
	public function contentType($contentType)
	{
		$this->_contentType = $contentType;
		return $this;
	}

	/**
	 * Additional data of the request
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Additional POST data
	 * @param string $key key
	 * @param string $value value
	 * @return self
	 */
	public function data($key, $value)
	{
		$this->_data[$key] = $value;
		return $this;
	}

	/**
	 * Headers of the request
	 * @return string
	 */
	protected $_headers = NULL;

	/**
	 * Get headers
	 * @return string
	 */
	public function getHeaders()
	{
		return $this->_headers;
	}

	/**
	 * Body of the request
	 * @return string
	 */
	protected $_body = NULL;

	/**
	 * Get body
	 * @return string
	 */
	public function getBody()
	{
		return $this->_body;
	}

	/**
	 * Get decompressed body
	 * @return string
	 */
	public function getDecompressedBody()
	{
		$aHeaders = array_change_key_case($this->parseHeaders(), CASE_LOWER);;

		if (isset($aHeaders['content-encoding']))
		{
			switch ($aHeaders['content-encoding'])
			{
				case 'gzip':
					return gzinflate(substr($this->_body, 10));
				break;
				default:
					throw new Core_Exception('Core_Http unsupported compression method "%name"', array('%name' =>
					$aHeaders['content-encoding']));
			}
		}

		return $this->_body;
	}

	/**
	 * Executes the business logic.
	 */
	public function execute()
	{
		$aUrl = @parse_url(trim($this->_url));

		$path = isset($aUrl['host']) && isset ($aUrl['path'])
			? $aUrl['path']
			: '/';

		$host = Core_Array::get($aUrl, 'host', '');

		$query = isset($aUrl['query']) ?
			'?' . $aUrl['query']
			: '';

		$this->_referer = is_null($this->_referer)
			? "http://{$host}"
			: $this->_referer;

		return $this->_execute($host, $path, $query);
	}

	/**
	 * Parse header
	 * @return array
	 */
	public function parseHeaders()
	{
		return $this->_parseHeaders(
			$this->getHeaders()
		);
	}

	/**
	 * Parse HTTP status code
	 * @param string $status status code, e.g. 'HTTP/1.1 200 OK'
	 * @return int
	 */
	public function parseHttpStatusCode($status)
	{
		preg_match('|HTTP/\d\.\d\s+(\d+)\s+.*|', $status, $match);
		return Core_Array::get($match, 1);
	}

	/**
	 * Parse header
	 * @param string $header
	 * @return array
	 */
	protected function _parseHeaders($header)
	{
		$aReturn = array();
		$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));

		foreach ($fields as $field)
		{
			if (preg_match('/([^:]+): (.+)/m', $field, $match))
			{
				/*$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(
					trim($match[1]))
				);*/

				$match[1] = preg_replace_callback(
					'/(?<=^|[\x09\x20\x2D])./',
					create_function ('$matches', 'return strtoupper($matches[0]);'), strtolower(trim($match[1]))
				);

				if( isset($aReturn[$match[1]]))
				{
					if (is_array($aReturn[$match[1]]))
					{
						$aReturn[$match[1]][] = trim($match[2]);
					}
					else
					{
						$aReturn[$match[1]] = array($aReturn[$match[1]], $match[2]);
					}
				}
				else
				{
					$aReturn[$match[1]] = trim($match[2]);
				}
			}
			else
			{
				$aReturn['status'] = trim($field);
			}
		}
		return $aReturn;
	}
}