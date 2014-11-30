<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * SMTP driver
 *
 * @package HostCMS 6\Core\Mail
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Mail_Smtp extends Core_Mail
{
	/**
	 * Send mail
	 * @param string $to recipient
	 * @param string $subject subject
	 * @param string $message message
	 * @param string $additional_headers additional_headers
	 * @return self
	 */
	protected function _send($to, $subject, $message, $additional_headers)
	{
		$sSingleSeparator = $this->_separator;

		$header = "Date: " . date("D, d M Y H:i:s") . " UT{$sSingleSeparator}";
		$header .= "Subject: {$subject}{$sSingleSeparator}";
		$header .= "To: <{$to}>{$sSingleSeparator}";
		$header .= $additional_headers . $sSingleSeparator . $sSingleSeparator;

		$header .=  $message . $sSingleSeparator;
		$timeout = 5;

		$fp = function_exists('stream_socket_client')
			? stream_socket_client($this->_config['host'] . ":" . $this->_config['port'], $errno, $errstr, $timeout)
			: fsockopen($this->_config['host'], $this->_config['port'], $errno, $errstr, $timeout);

		if ($fp)
		{
			stream_set_timeout($fp, $timeout);

			$server_response = $this->_serverFgets($fp);
			if (!$this->_serverParse($server_response, "220"))
			{
				fclose($fp);
				return FALSE;
			}

			fputs($fp, "EHLO {$this->_config['host']}\r\n");

			$server_response = $this->_serverFgets($fp);
			if (!$this->_serverParse($server_response, "250"))
			{
				fclose($fp);
				return FALSE;
			}

			// Может быть много 250-х, последний отделяется пробелом, а не минусом
			do {
				$server_response = $this->_serverFgets($fp);
			}
			while(!feof($fp)
				&& $this->_getResponseStatus($server_response) == "250"
				&& substr($server_response, 3, 1) != ' '
			);

			fputs($fp, "AUTH LOGIN\r\n");
			$server_response = $this->_serverFgets($fp); // Получен выше в цикле
			if (!$this->_serverParse($server_response, "334"))
			{
				fclose($fp);
				return FALSE;
			}

			fputs($fp, base64_encode($this->_config['username']) . "\r\n");
			$server_response = $this->_serverFgets($fp);
			if (!$this->_serverParse($server_response, "334"))
			{
				fclose($fp);
				return FALSE;
			}

			fputs($fp, base64_encode($this->_config['password']) . "\r\n");
			$server_response = $this->_serverFgets($fp);
			if (!$this->_serverParse($server_response, "235"))
			{
				fclose($fp);
				return FALSE;
			}

			fputs($fp, "MAIL FROM: <{$this->_config['username']}>\r\n");
			$server_response = $this->_serverFgets($fp);
			if (!$this->_serverParse($server_response, "250")) {
				fclose($fp);
				return FALSE;
			}

			$aRecipients = explode(',', $to);
			foreach ($aRecipients as $sTo)
			{
				fputs($fp, "RCPT TO: <{$sTo}>\r\n");
				$server_response = $this->_serverFgets($fp);
				if (!$this->_serverParse($server_response, "250"))
				{
					fclose($fp);
					return FALSE;
				}
			}

			fputs($fp, "DATA\r\n");
			$server_response = $this->_serverFgets($fp);
			if (!$this->_serverParse($server_response, "354"))
			{
				fclose($fp);
				return FALSE;
			}

			fputs($fp, $header."\r\n.\r\n");
			$server_response = $this->_serverFgets($fp);
			if (!$this->_serverParse($server_response, "250"))
			{
				fclose($fp);
				return FALSE;
			}

			fputs($fp, "QUIT\r\n");
			fclose($fp);

			$this->_status = TRUE;
		}
		else
		{
			$this->_status = FALSE;
		}

		return $this;
	}

	/**
	 * fgets 256 bytes
	 * @param pointer $socket
	 * @return mixed
	 */
	protected function _serverFgets($socket)
	{
		return fgets($socket, 256);
	}

	/**
	 * Get status of response
	 * @param string $server_response
	 * @return string
	 */
	protected function _getResponseStatus($server_response)
	{
		return substr($server_response, 0, 3);
	}

	/**
	 * Parse server answer
	 * @param string $server_response
	 * @param string $response response
	 * @return string
	 */
	protected function _serverParse($server_response, $response)
	{
		$result = $this->_getResponseStatus($server_response) == $response;

		if (!$result)
		{
			//throw new Core_Exception('SMTP error: "%error"', array('%error' => $server_response));
			Core_Log::instance()->clear()
				->notify(FALSE) // avoid recursion
				->status(Core_Log::$ERROR)
				->write(sprintf('SMTP error: "%s"', $server_response));
		}

		return $result;
	}
}