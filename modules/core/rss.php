<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Create an RSS 2.0 feed
 *
 * @package HostCMS 6\Core\Rss
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Rss
{
	/**
	 * Encoding
	 * @var string
	 */
	protected $_encoding = 'UTF-8';

	/**
	 * XMLNS
	 * @var string
	 */
	protected $_xmlns = NULL;

	/**
	 * Set XMLNS value
	 * @param string $name name
	 * @param string $value value
	 * @return self
	 */
	public function xmlns($name, $value)
	{
		$this->_xmlns = $name . '="' . $value . '"';
		return $this;
	}

	/**
	 * Entities list
	 * @var array
	 */
	protected $_entities = array();

	/**
	 * Add entity
	 * @param string $name entity name
	 * @param string $value entity value
	 * @return self
	 */
	public function add($name, $value)
	{
		$this->_entities[$name][] = $value;
		return $this;
	}

	/**
	 * Delete entity by name
	 * @param string $name entity name
	 * @return self
	 */
	public function delete($name)
	{
		if (isset($this->_entities[$name]))
		{
			unset($this->_entities[$name]);
		}
		return $this;
	}

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this
			->add('pubDate', date('r'))
			->add('generator', 'HostCMS');
	}

	/**
	 * Add child node
	 * @param object $object object
	 * @param string $name node name
	 * @param string $value node value
	 * @return self
	 */
	protected function __addChild($object, $name, $value)
	{
		if ($object->getName() == 'enclosure')
		{
			$object->addAttribute($name, $value);
		}
		else
		{
			$aTmp = explode(':', $name);

			isset($aTmp[1])
				? $object->addChild($name, $value, $aTmp[0])
				: $object->addChild($name, $value);
		}

		return $this;
	}

	/**
	 * Add child nodes
	 * @param object $object object
	 * @param array $array nodes
	 * @return self
	 */
	protected function _addChild($object, $array)
	{
		foreach ($array as $name => $aValues)
		{
			if (is_array($aValues))
			{
				foreach ($aValues as $value)
				{
					is_array($value)
						? $this->_addChild($object->addChild($name), $value)
						: $this->__addChild($object, $name, $value);
				}
			}
			else
			{
				$this->__addChild($object, $name, $aValues);
			}
		}

		return $this;
	}

	/**
	  * Show RSS with headers
	  * @param string $rss content
	  */
	public function showWithHeader($rss)
	{
		$oCore_Response = new Core_Response();

		$oCore_Response
			->status(200)
			->header('Content-Type', 'text/xml; charset=' . $this->_encoding)
			->header('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT')
			->header('X-Powered-By', 'HostCMS');

		$oCore_Response
			->body($rss)
			->compress()
			->sendHeaders()
			->showBody();
	}

	/**
	 * Show RSS
	 * @return void
	 */
	public function show()
	{
		$this->showWithHeader($this->get());
	}

	/**
	 * Get RSS
	 * @return string
	 */
	public function get()
	{
		$oRss = simplexml_load_string('<?xml version="1.0" encoding="' . $this->_encoding . '"?>' .
			'<rss version="2.0"' . (is_null($this->_xmlns) ? '' : ' xmlns:' . $this->_xmlns) . '>' .
			'<channel></channel>' .
			'</rss>');

		$this->_addChild($oRss->channel, $this->_entities);

		// $xml = $oRss->asXML();
		$dom = dom_import_simplexml($oRss)->ownerDocument;
		$dom->formatOutput = TRUE;
		$xml = $dom->saveXML();

		return $xml;
	}
}