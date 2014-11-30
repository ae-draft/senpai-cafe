<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Http cUrl driver
 *
 * @package HostCMS 6\Core\Http
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Http_Curl extends Core_Http
{
	/**
	 * Send request
	 * @param string $host host
	 * @param string $path path
	 * @param string $query query
	 * @return self
	 */
	protected function _execute($host, $path, $query)
	{
		$curl = @curl_init();

		curl_setopt($curl, CURLOPT_URL, "http://{$host}{$path}{$query}");

		if ($this->_method == 'GET')
		{
			curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
			curl_setopt($curl, CURLOPT_POST, FALSE);
		}
		else
		{
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_HTTPGET, FALSE);

			count($this->_data) && curl_setopt($curl, CURLOPT_POSTFIELDS, $this->_data);
		}

		foreach ($this->_config['options'] as $optionName => $optionValue)
		{
			curl_setopt($curl, $optionName, $optionValue);
		}

		curl_setopt($curl, CURLOPT_HEADER, TRUE);
		curl_setopt($curl, CURLOPT_NOBODY, FALSE); // Return body

		curl_setopt($curl, CURLOPT_TIMEOUT, $this->_timeout);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
		curl_setopt($curl, CURLOPT_REFERER, $this->_referer);

		curl_setopt($curl, CURLOPT_VERBOSE, FALSE); // Minimize logs
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // No certificate
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); // Return in string

		if (ini_get('open_basedir') == '' && ini_get('safe_mode') != 1 && strtolower(ini_get('safe_mode')) != 'off')
		{
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
		}
		else
		{
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, FALSE);

			$mr = 5;

			if ($mr > 0)
			{
				$newurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

				$rch = curl_copy_handle($curl);
				curl_setopt($rch, CURLOPT_HEADER, TRUE);
				curl_setopt($rch, CURLOPT_NOBODY, TRUE);
				curl_setopt($rch, CURLOPT_FORBID_REUSE, TRUE);
				curl_setopt($rch, CURLOPT_RETURNTRANSFER, TRUE);
				do {
					curl_setopt($rch, CURLOPT_URL, $newurl);
					$header = curl_exec($rch);
					if (curl_errno($rch))
					{
						$code = 0;
					}
					else
					{
						$code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
						if ($code == 301 || $code == 302) {
							preg_match('/Location:(.*?)\n/', $header, $matches);
							$newurl = trim(array_pop($matches));
						}
						else
						{
							$code = 0;
						}
					}
				} while ($code && --$mr);

				curl_close($rch);

				if (!$mr)
				{
					if (is_null($maxredirect))
					{
						trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
					}
					else
					{
						$maxredirect = 0;
					}
					return false;
				}
				curl_setopt($curl, CURLOPT_URL, $newurl);
			}
		}

		// Additional headers
		if (count($this->_additionalHeaders))
		{
			$aTmp = array();
			foreach ($this->_additionalHeaders as $name => $value)
			{
				$aTmp[] = "{$name}: {$value}";
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $aTmp);
		}

		// Get the target contents
		$datastr = @curl_exec($curl);

		// Close PHP cURL handle
		@curl_close($curl);

		$aTmp = explode("\r\n\r\n", $datastr, 2);
		unset ($datastr);

		$this->_headers = Core_Array::get($aTmp, 0);
		$this->_body = Core_Array::get($aTmp, 1);

		return $this;
	}
}