<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin answer.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2012 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Admin_Answer extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'ajax',
		'title',
		'message',
		'content',
		'skin'
	);

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->skin = TRUE;
		$this->title = '';
		$this->message = '';
		$this->content = '';
	}

	/**
	 * Send AJAX answer with headers
	 */
	protected function _sendAjax()
	{
		header('Pragma: no-cache');
		header('Cache-Control: private, no-cache');
		header('Content-Disposition: inline; filename="files.json"');
		header('Vary: Accept');

		if (strpos(Core_Array::get($_SERVER, 'HTTP_ACCEPT', ''), 'application/json') !== FALSE)
		{
			header('Content-type: application/json; charset=utf-8');
		}
		else
		{
			header('X-Content-Type-Options: nosniff');
			header('Content-type: text/plain; charset=utf-8');
		}

		// utf-8: http://www.iana.org/assignments/character-sets
		//header('Content-Type: text/javascript; charset=utf-8');

		// bug in Chrome
		//header("Content-Length: " . strlen($content));
		echo json_encode(
			array(
				'form_html' => $this->content,
				'error' => $this->message,
				'title' => $this->title
			)
		);

		exit();
	}

	/**
	 * Send header and HTML answer
	 */
	protected function _sendHtml()
	{
		$this->skin && Core_Skin::instance()
			->title($this->title)
			->header();

		?><div id="id_content"><?php
		?><div id="id_message"><?php echo $this->message?></div><?php echo $this->content?><?php
		?></div><?php

		?><script type="text/javascript"><?php
		if (!is_null($this->title))
		{
			?>document.title = '<?php echo str_replace("'", "\'", $this->title)?>';<?php
		}
		?>$.afterContentLoad($("#id_content"));<?php
		?></script><?php

		$this->skin && Core_Skin::instance()->footer();
	}

	/**
	 * Send answer (AJAX or HTML)
	 * @return self
	 */
	public function execute()
	{
		$this->ajax
			? $this->_sendAjax()
			: $this->_sendHtml();

		return $this;
	}

	/**
	 * Get current window id
	 * @return int
	 */
	protected function _getWindowId()
	{
		$aHostCMS = Core_Array::getGet('hostcms', array());
		return Core_Array::get($aHostCMS, 'window', 'id_content');
	}

	/**
	 * Open window, default FALSE
	 */
	protected $_openWindow = FALSE;

	/**
	 * Open window, default FALSE
	 * @param boolean $openWindow open mode
	 * @return self
	 */
	public function openWindow($openWindow)
	{
		$this->_openWindow = $openWindow;
		return $this;
	}

	/**
	 * Window's settings
	 */
	protected $_windowSettings = array();

	/**
	 * Set window's settings
	 * @param array $windowSettings settings
	 * @return self
	 */
	public function windowSettings($windowSettings)
	{
		$this->_windowSettings = $windowSettings;
		return $this;
	}

	/**
	 * Add into taskbar
	 */
	protected $_addTaskbar = TRUE;

	/**
	 * Add into taskbar, default TRUE
	 * @param boolean $addTaskbar TRUE/FALSE
	 * @return self
	 */
	public function addTaskbar($addTaskbar)
	{
		$this->_addTaskbar = $addTaskbar;
		return $this;
	}
}