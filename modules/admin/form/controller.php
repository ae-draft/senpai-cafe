<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Admin_Form_Controller
{
	/**
	 * Use skin
	 * @var boolean
	 */
	protected $_skin = TRUE;

	/**
	 * Use AJAX
	 * @var boolean
	 */
	protected $_ajax = FALSE;

	/**
	 * Dataset array
	 * @var array
	 */
	protected $_datasets = array();

	/**
	 * Admin form
	 * @var Admin_Form
	 */
	protected $_Admin_Form = NULL;

	/**
	 * Current language in administrator's center
	 * @var string
	 */
	protected $_Admin_Language = NULL;

	/**
	 * Default form sorting field
	 * @var string
	 */
	protected $_sortingAdmin_Form_Field = NULL;

	/**
	 * String of additional parameters
	 * @var string
	 */
	protected $_additionalParams = NULL;

	/**
	 * Create new form controller
	 * @param Admin_Form_Model $oAdmin_Form
	 * @return object
	 */
	static public function create(Admin_Form_Model $oAdmin_Form = NULL)
	{
		$className = 'Skin_' . ucfirst(Core_Skin::instance()->getSkinName()) . '_'  . __CLASS__;

		if (!class_exists($className))
		{
			throw new Core_Exception("Class '%className' does not exist",
					array('%className' => $className));
		}

		return new $className($oAdmin_Form);
	}

	/**
	 * Add additional param
	 * @param string $key param name
	 * @param string $value param value
	 * @return self
	 */
	public function addAdditionalParam($key, $value)
	{
		$this->_additionalParams .= '&' . htmlspecialchars($key) . '=' . rawurlencode($value);
		return $this;
	}

	/**
	 * Form setup
	 * @return self
	 */
	public function setUp()
	{
		if (!defined('DISABLE_COMPRESSION') || !DISABLE_COMPRESSION)
		{
			// Если сжатие уже не включено на сервере директивой zlib.output_compression = On
			// http://php.net/manual/en/function.ini-get.php
			// A boolean ini value of off will be returned as an empty string or "0"
			// while a boolean ini value of on will be returned as "1".
			// The function can also return the literal string of INI value.

			// MSIE 8.0 has problem with the fastpage-enabled content was not being un-gzipped
			if (/*strpos(Core_Array::get($_SERVER, 'HTTP_USER_AGENT'), 'MSIE 8.0') === FALSE
				&& */ini_get('zlib.output_compression') == 0)
			{
				// включаем сжатие буфера вывода
				ob_start("ob_gzhandler");
			}
		}

		Core::initConstants(Core_Entity::factory('Site', CURRENT_SITE));

		$aTmp = array();
		foreach ($_GET as $key => $value)
		{
			if (!is_array($value))
			{
				//$aTmp[] = htmlspecialchars($key, ENT_QUOTES) . '=' . htmlspecialchars($value, ENT_QUOTES);
				$aTmp[] = htmlspecialchars($key) . '=' . rawurlencode($value);
			}
		}

		$this->_additionalParams = implode('&', $aTmp);

		$this->formSettings();
		return $this;
	}

	/**
	 * Data set from _REQUEST
	 * @var array
	 */
	public $request = array();

	/**
	 * Apply form settings
	 * @return self
	 */
	public function formSettings()
	{
		$this->request = $_REQUEST;

		$formSettings = Core_Array::get($this->request, 'hostcms', array())
			+ array(
				'limit' => NULL,
				'current' => NULL,
				'sortingfield' => NULL,
				'sortingdirection' => NULL,
				'action' => NULL,
				'operation' => NULL,
				'window' => 'id_content',
				'checked' => array()
			);

		$this
			->limit($formSettings['limit'])
			->current($formSettings['current'])
			->sortingDirection($formSettings['sortingdirection'])
			->sortingFieldId($formSettings['sortingfield'])
			->action($formSettings['action'])
			->operation($formSettings['operation'])
			->checked($formSettings['checked'])
			->window($formSettings['window'])
			->ajax(Core_Array::get($this->request, '_', FALSE));

		$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();

		if ($oUserCurrent && $this->_Admin_Form)
		{
			$user_id = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;

			$oAdmin_Form_Setting = $this->_Admin_Form->getSettingForUser(
				$user_id
			);

			$bAdmin_Form_Setting_Already_Exists = $oAdmin_Form_Setting;

			if (!$bAdmin_Form_Setting_Already_Exists)
			{
				$oAdmin_Form_Setting = Core_Entity::factory('Admin_Form_Setting');

				// Связываем с формой и пользователем сайта
				$this->_Admin_Form->add($oAdmin_Form_Setting);
				$oUserCurrent->add($oAdmin_Form_Setting);
			}

			!is_null($this->_limit) && $oAdmin_Form_Setting->on_page = intval($this->_limit);
			!is_null($this->_current) && $oAdmin_Form_Setting->page_number = intval($this->_current);

			if (!is_null($this->_sortingFieldId))
			{
				$oAdmin_Form_Setting->order_field_id = intval($this->_sortingFieldId);
			}
			// Восстанавливаем сохраненный
			elseif ($bAdmin_Form_Setting_Already_Exists)
			{
				$this->_sortingFieldId = $oAdmin_Form_Setting->order_field_id;
			}

			if (!is_null($this->_sortingDirection))
			{
				$oAdmin_Form_Setting->order_direction = intval($this->_sortingDirection);
			}
			// Восстанавливаем сохраненный
			elseif ($bAdmin_Form_Setting_Already_Exists)
			{
				$this->_sortingDirection = $oAdmin_Form_Setting->order_direction;
			}

			$oAdmin_Form_Setting->save();
		}

		// Добавляем замену для windowId
		$this->_externalReplace['{windowId}'] = $this->getWindowId();
		return $this;
	}

	/**
	 * Path
	 * @var string
	 */
	protected $_path = NULL;

	/**
	 * Set path
	 * @param string $path path
	 * @return self
	 */
	public function path($path)
	{
		$this->_path = $path;

		// Добавляем замену для path
		$this->_externalReplace['{path}'] = $this->_path;

		return $this;
	}

	/**
	 * Get path
	 * @return string
	 */
	public function getPath()
	{
		return $this->_path;
	}

	/**
	 * Array of checked items
	 * @var array
	 */
	protected $_checked = array();

	/**
	 * Set checked items on the form
	 * Выбранные элементы в форме
	 * @param array $checked checked items
	 * @return self
	 */
	public function checked(array $checked)
	{
		$this->_checked = $checked;
		return $this;
	}

	/**
	 * Get checked items on the form
	 * @return array
	 */
	public function getChecked()
	{
		return $this->_checked;
	}

	/**
	 * Clear checked items on the form
	 * @return self
	 */
	public function clearChecked()
	{
		$this->_checked = array();
		return $this;
	}

	/**
	 * List of handlers' actions
	 * @var array
	 */
	protected $_actionHandlers = array();

	/**
	 * Добавление обработчика действия
	 * @param Admin_Form_Action_Controller $oAdmin_Form_Action_Controller action controller
	 */
	public function addAction(Admin_Form_Action_Controller $oAdmin_Form_Action_Controller)
	{
		// Set link to controller
		$oAdmin_Form_Action_Controller->controller($this);

		$this->_actionHandlers[$oAdmin_Form_Action_Controller->getName()] = $oAdmin_Form_Action_Controller;
	}

	/**
	 * List of children entities
	 * @var array
	 */
	protected $_children = array();

	/**
	 * Add entity
	 * @param Admin_Form_Entity $oAdmin_Form_Entity
	 * @return self
	 */
	public function addEntity(Admin_Form_Entity $oAdmin_Form_Entity)
	{
		// Set link to controller
		$oAdmin_Form_Entity->controller($this);

		Core_Event::notify(get_class($this) . '.onBeforeAddEntity', $this, array($oAdmin_Form_Entity));

		$this->_children[] = $oAdmin_Form_Entity;
		return $this;
	}

	/**
	 * List of external replaces
	 * @var array
	 */
	protected $_externalReplace = array();

	/**
	* Add external replacement
	* Добавление внешней подстановки
	* @param string $key name of  replacement
	* @param string $value value of replacement
	* @return self
	*/
	public function addExternalReplace($key, $value)
	{
		$this->_externalReplace[$key] = $value;
		return $this;
	}

	/**
	 * Get Admin_Form
	 * @return Admin_Form_Model
	 */
	public function getAdminForm()
	{
		return $this->_Admin_Form;
	}

	/**
	 * Constructor.
	 * @param Admin_Form_Model $oAdmin_Form admin form
	 */
	public function __construct(Admin_Form_Model $oAdmin_Form = NULL)
	{
		$this->_Admin_Form = $oAdmin_Form;

		//$this->_Admin_Form->_load();

		if ($oAdmin_Form)
		{
			if (is_null($this->_Admin_Form->key_field))
			{
				throw new Core_Exception('Admin form does not exist.');
			}

			$this->_Admin_Language = Core_Entity::factory('Admin_Language')->getCurrent();

			$formName = $this->_Admin_Form->Admin_Word->getWordByLanguage($this->_Admin_Language->id);

			if ($formName->name)
			{
				$this->_title = $formName->name;
				$this->_pageTitle = $formName->name;
			}

			$sortingFieldName = $this->_Admin_Form->default_order_field;

			$this->_sortingAdmin_Form_Field = $this->_Admin_Form->Admin_Form_Fields->getByName($sortingFieldName);

			if (is_null($this->_sortingAdmin_Form_Field))
			{
				throw new Core_Exception("Default form sorting field '%sortingFieldName' does not exist.",
					array ('%sortingFieldName' => $sortingFieldName)
				);
			}

			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$user_id = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;

			$oAdmin_Form_Setting = $this->_Admin_Form->getSettingForUser(
				$user_id
			);

			// Данные поля сортировки и направления из настроек пользователя
			if ($oAdmin_Form_Setting)
			{
				$this
					->limit($oAdmin_Form_Setting->on_page)
					->current($oAdmin_Form_Setting->page_number)
					->sortingFieldId($oAdmin_Form_Setting->order_field_id)
					->sortingDirection($oAdmin_Form_Setting->order_direction);
			}
			else
			{
				// Данные по умолчанию из настроек формы
				$this->sortingFieldId(
					$this->_Admin_Form->Admin_Form_Fields
						->getByName($this->_Admin_Form->default_order_field)->id
				)
				->sortingDirection($this->_Admin_Form->default_order_direction);
			}
		}

		// Current path
		$this->path($_SERVER['PHP_SELF']);
	}

	/**
	 * Is showing operations necessary
	 * @var boolean
	 */
	protected $_showOperations = TRUE;

	/**
	 * Display operations of the form
	 * @param boolean $showOperations mode
	 * @return self
	 */
	public function showOperations($showOperations)
	{
		$this->_showOperations = $showOperations;
		return $this;
	}

	/**
	 * List of filter handlers
	 * @var array
	 */
	protected $_filters = array();

	/**
	 * Add handler of the filter
	 * Добавление обработчика фильтра
	 * @param string $fieldName field name
	 * @param string $function function name
	 * @return self
	 */
	public function addFilter($fieldName, $function)
	{
		$this->_filters[$fieldName] = $function;
		return $this;
	}

	/**
	 * Is showing filter necessary
	 * @var boolean
	 */
	protected $_showFilter = TRUE;

	/**
	 * Show filter of the form
	 * @param boolean $showFilter mode
	 * @return self
	 */
	public function showFilter($showFilter)
	{
		$this->_showFilter = $showFilter;
		return $this;
	}

	/**
	 * Is showing list of action necessary
	 * @var boolean
	 */
	protected $_showBottomActions = TRUE;

	/**
	 * Display actions of the bottom of the form
	 * @param boolean $showBottomActions mode
	 * @return self
	 */
	public function showBottomActions($showBottomActions)
	{
		$this->_showBottomActions = $showBottomActions;
		return $this;
	}

	/**
	 * Page title <h1>
	 */
	protected $_title = NULL;

	/**
	 * Set <h1> for form
	 * @param string $title content
	 * @return self
	 */
	public function title($title)
	{
		$this->_title = $title;
		return $this;
	}

	/**
	 * Page title <title>
	 * @var string
	 */
	protected $_pageTitle = NULL;

	/**
	 * Set page <title>
	 * @param $pageTitle title
	 * @return self
	 */
	public function pageTitle($pageTitle)
	{
		$this->_pageTitle = $pageTitle;
		return $this;
	}

	/**
	 * Get page <title>
	 * @return string
	 */
	public function getPageTitle()
	{
		return $this->_pageTitle;
	}

	/**
	 * Limits elements on page
	 * @var int
	 */
	protected $_limit = ON_PAGE;

	/**
	 * Current page
	 * @var int
	 */
	protected $_current = 1; // счет с 1

	/**
	 * Set limit of elements on page
	 * @param int $limit count
	 * @return self
	 */
	public function limit($limit)
	{
		$limit = intval($limit);

		if ($limit > 0)
		{
			$this->_limit = $limit;
		}
		return $this;
	}

	/**
	 * Set current page
	 * @param int $current page
	 * @return self
	 */
	public function current($current)
	{
		$current = intval($current);

		if ($current > 0)
		{
			$this->_current = intval($current);
		}
		return $this;
	}

	/**
	 * Action name
	 * @var string
	 */
	protected $_action = NULL;

	/**
	 * Set action
	 * @param string $action action
	 * @return self
	 */
	public function action($action)
	{
		$this->_action = $action;
		return $this;
	}

	/**
	 * Get action
	 * @return string
	 */
	public function getAction()
	{
		return $this->_action;
	}

	/**
	 * Action operation e.g. "save" or "apply"
	 */
	protected $_operation = NULL;

	/**
	 * Set operation
	 * @param string $operation operation
	 * @return self
	 */
	public function operation($operation)
	{
		$this->_operation = $operation;
		return $this;
	}

	/**
	 * Get operation
	 * @return string
	 */
	public function getOperation()
	{
		return $this->_operation;
	}

	/**
	 * Set AJAX
	 * @param boolean $ajax ajax
	 * @return self
	 */
	public function ajax($ajax)
	{
		$this->_ajax = ($ajax != FALSE);
		return $this;
	}

	/**
	 * Get AJAX
	 * @return boolean
	 */
	public function getAjax()
	{
		return $this->_ajax;
	}

	/**
	 * Show skin
	 * @param boolean $skin use skin mode
	 * @return self
	 */
	public function skin($skin)
	{
		$this->_skin = ($skin != FALSE);
		return $this;
	}

	/**
	 * Add dataset
	 * @param Admin_Form_Dataset $oAdmin_Form_Dataset dataset
	 * @return self
	 */
	public function addDataset(Admin_Form_Dataset $oAdmin_Form_Dataset)
	{
		$this->_datasets[] = $oAdmin_Form_Dataset->controller($this);
		return $this;
	}

	/**
	 * Get dataset
	 * @param int $key index
	 * @return Admin_Form_Dataset|NULL
	 */
	public function getDataset($key)
	{
		return isset($this->_datasets[$key])
			? $this->_datasets[$key]
			: NULL;
	}

	/**
	 * Window ID
	 * @var int
	 */
	protected $_windowId = NULL;

	/**
	 * Set window ID
	 * @param int $windowId ID
	 * @return self
	 */
	public function window($windowId)
	{
		$this->_windowId = $windowId;
		return $this;
	}

	/**
	 * Get window ID
	 * @return int
	 */
	public function getWindowId()
	{
		return $this->_windowId;
	}

	/**
	 * Count of elements on page
	 * @var array
	 */
	protected $_onPage = array (10 => 10, 20 => 20, 30 => 30, /*40 => 40, */50 => 50, 100 => 100, 500 => 500, 1000 => 1000);

	/**
	 * Show items count selector
	 */
	protected function _onPageSelector()
	{
		$sCurrentValue = $this->_limit;
		$windowId = Core_Str::escapeJavascriptVariable($this->getWindowId());
		$additionalParams = Core_Str::escapeJavascriptVariable(
			str_replace(array('"'), array('&quot;'), $this->_additionalParams)
		);
 		$path = Core_Str::escapeJavascriptVariable($this->getPath());

		$oCore_Html_Entity_Select = Core::factory('Core_Html_Entity_Select')
			->name('admin_forms_on_page')
			->id('id_on_page')
			->onchange("$.adminLoad({path: '{$path}', additionalParams: '{$additionalParams}', limit: this.options[this.selectedIndex].value, windowId : '{$windowId}'}); return false")
			->options($this->_onPage)
			->value($sCurrentValue)
			->execute();
	}

	/**
	 * Total founded items
	 * @var int
	 */
	protected $_totalCount = NULL;

	/**
	 * Get count of total founded items
	 * @return int
	 */
	public function getTotalCount()
	{
		if (is_null($this->_totalCount))
		{
			try
			{
				foreach ($this->_datasets as $oAdmin_Form_Dataset)
				{
					$this->_totalCount += $oAdmin_Form_Dataset->getCount();
				}
			}
			catch (Exception $e)
			{
				Core_Message::show($e->getMessage(), 'error');
			}
		}

		return $this->_totalCount;
	}

	/**
	 * Count of links to next pages
	 * @var int
	 */
	protected $_pageNavigationDelta = 5;

	/**
	* Показ строки ссылок
	*/
	protected function _pageNavigation()
	{
		$total_count = $this->getTotalCount();
		$total_page = $total_count / $this->_limit;

		// Округляем в большую сторону
		if ($total_count % $this->_limit != 0)
		{
			$total_page = intval($total_page) + 1;
		}

		$this->_current > $total_page && $this->_current = $total_page;

		$oCore_Html_Entity_Div = Core::factory('Core_Html_Entity_Div')
			->style('float: left; text-align: center; margin-top: 10px');

		// Формируем скрытые ссылки навигации для перехода по Ctrl + стрелка
		if ($this->_current < $total_page)
		{
			// Ссылка на следующую страницу
			$page = $this->_current + 1 ? $this->_current + 1 : 1;
			$oCore_Html_Entity_Div->add(
				Core::factory('Core_Html_Entity_A')
					->onclick($this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, $page))
					->id('id_next')
			);
		}

		if ($this->_current > 1)
		{
			// Ссылка на предыдущую страницу
			$page = $this->_current - 1 ? $this->_current - 1 : 1;
			$oCore_Html_Entity_Div->add(
				Core::factory('Core_Html_Entity_A')
					->onclick($this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, $page))
					->id('id_prev')
			);
		}

		// Отображаем строку ссылок, если общее число страниц больше 1.
		if ($total_page > 1)
		{
			// Определяем номер ссылки, с которой начинается строка ссылок.
			$link_num_begin = ($this->_current - $this->_pageNavigationDelta < 1)
				? 1
				: $this->_current - $this->_pageNavigationDelta;

			// Определяем номер ссылки, которой заканчивается строка ссылок.
			$link_num_end = $this->_current + $this->_pageNavigationDelta;
			$link_num_end > $total_page && $link_num_end = $total_page;

			// Определяем число ссылок выводимых на страницу.
			$count_link = $link_num_end - $link_num_begin + 1;

			if ($this->_current == 1)
			{
				$oCore_Html_Entity_Div->add(
					Core::factory('Core_Html_Entity_Span')
						->class('current')
						->value($link_num_begin)
				);
			}
			else
			{
				$href = $this->getAdminLoadHref($this->getPath(), NULL, NULL, NULL, NULL, 1);
				$onclick = $this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, 1);

				$oCore_Html_Entity_Div->add(
					Core::factory('Core_Html_Entity_A')
						->href($href)
						->onclick($onclick)
						->class('page_link')
						->value(1)
				);

				// Выведем … со ссылкой на 2-ю страницу, если показываем с 3-й
				if ($link_num_begin > 1)
				{
					$href = $this->getAdminLoadHref($this->getPath(), NULL, NULL, NULL, NULL, 2);
					$onclick = $this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, 2);

					$oCore_Html_Entity_Div->add(
						Core::factory('Core_Html_Entity_A')
							->href($href)
							->onclick($onclick)
							->class('page_link')
							->value('…')
					);
				}
			}

			// Страница не является первой и не является последней.
			for ($i = 1; $i < $count_link - 1; $i++)
			{
				$link_number = $link_num_begin + $i;

				if ($link_number == $this->_current)
				{
					// Страница является текущей
					$oCore_Html_Entity_Div->add(
						Core::factory('Core_Html_Entity_Span')
							->class('current')
							->value($link_number)
					);
				}
				else
				{
					$href = $this->getAdminLoadHref($this->getPath(), NULL, NULL, NULL, NULL, $link_number);
					$onclick = $this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, $link_number);
					$oCore_Html_Entity_Div->add(
						Core::factory('Core_Html_Entity_A')
							->href($href)
							->onclick($onclick)
							->class('page_link')
							->value($link_number)
					);
				}
			}

			// Если последняя страница является текущей
			if ($this->_current == $total_page)
			{
				$oCore_Html_Entity_Div->add(
					Core::factory('Core_Html_Entity_Span')
							->class('current')
							->value($total_page)
				);
			}
			else
			{
				// Выведем … со ссылкой на предпоследнюю страницу
				if ($link_num_end < $total_page)
				{
					$href = $this->getAdminLoadHref($this->getPath(), NULL, NULL, NULL, NULL, $total_page - 1);
					$onclick = $this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, $total_page - 1);

					$oCore_Html_Entity_Div->add(
						Core::factory('Core_Html_Entity_A')
							->href($href)
							->onclick($onclick)
							->class('page_link')
							->value('…')
					);
				}

				$href = $this->getAdminLoadHref($this->getPath(), NULL, NULL, NULL, NULL, $total_page);
				$onclick = $this->getAdminLoadAjax($this->getPath(), NULL, NULL, NULL, NULL, $total_page);

				// Последняя страница не является текущей
				$oCore_Html_Entity_Div->add(
					Core::factory('Core_Html_Entity_A')
						->href($href)
						->onclick($onclick)
						->class('page_link')
						->value($total_page)
				);
			}

			$oCore_Html_Entity_Div->execute();
			Core::factory('Core_Html_Entity_Div')
				->style('clear: both')
				->execute();
		}
	}

	/**
	 * Content
	 * @var string
	 */
	protected $_content = NULL;

	/**
	 * Message text
	 * @var string
	 */
	protected $_message = NULL;

	/**
	 * Get content message
	 * @return string
	 */
	public function getContent()
	{
		return $this->_content;
	}

	/**
	 * Add content for administration center form
	 * @param string $content content
	 * @return self
	 */
	public function addContent($content)
	{
		$this->_content .= $content;
		return $this;
	}

	/**
	 * Get form message
	 * @return string
	 */
	public function getMessage()
	{
		return $this->_message;
	}

	/**
	 * Add message for administration center form
	 * @param string $message message
	 * @return self
	 */
	public function addMessage($message)
	{
		$this->_message .= $message;
		return $this;
	}

	/**
	 * Show built data
	 */
	public function show()
	{
		$oAdmin_Answer = Core_Skin::instance()->answer();

		$oAdmin_Answer
			->ajax($this->_ajax)
			->skin($this->_skin)
			->content($this->getContent())
			->message($this->getMessage())
			->title($this->_title)
			->execute();
	}

	/**
	 * Executes the business logic.
	 */
	public function execute()
	{
		ob_start();

		if (!empty($this->_action))
		{
			$actionName = $this->_action;

			$aReadyAction = array();

			try
			{
				// Текущий пользователь
				$oUser = Core_Entity::factory('User')->getCurrent();

				// Read Only режим
				if (defined('READ_ONLY') && READ_ONLY || $oUser->read_only && !$oUser->superuser)
				{
					throw new Core_Exception(
						Core::_('User.demo_mode'), array(), 0, FALSE
					);
				}

				// Доступные действия для пользователя
				$aAdmin_Form_Actions = $this->_Admin_Form->Admin_Form_Actions->getAllowedActionsForUser($oUser);

				$bActionAllowed = FALSE;

				// Проверка на право доступа к действию
				foreach ($aAdmin_Form_Actions as $oAdmin_Form_Action)
				{
					if ($oAdmin_Form_Action->name == $actionName)
					{
						$bActionAllowed = TRUE;
						break;
					}
				}

				if ($bActionAllowed)
				{
					foreach ($this->_checked as $datasetKey => $checkedItems)
					{
						foreach ($checkedItems as $checkedItemId => $v1)
						{
							if (isset($this->_datasets[$datasetKey]))
							{
								$oObject = $this->_datasets[$datasetKey]->getObject($checkedItemId);

								// Проверка на наличие объекта и доступность действия к dataset
								if (!is_object($oObject) || $oAdmin_Form_Action->dataset != -1
									&& $oAdmin_Form_Action->dataset != $datasetKey)
								{
									break;
								}

								// Проверка через user_id на право выполнения действия над объектом
								$bAccessToObject = $oUser->checkObjectAccess($oObject);

								if (!$bAccessToObject)
								{
									throw new Core_Exception(
										Core::_('User_Module.error_object_owned_another_user'), array(), 0, FALSE
									);
								}

								if (isset($this->_actionHandlers[$actionName]))
								{
									$actionResult = $this->_actionHandlers[$actionName]
										->setDatasetId($datasetKey)
										->setObject($oObject)
										->execute($this->_operation);

									$this->addMessage(
										$this->_actionHandlers[$actionName]->getMessage()
									);

									$this->addContent(
										$this->_actionHandlers[$actionName]->getContent()
									);
								}
								else
								{
									// Уже есть выше при проверке права доступа к действию, если действие было, то здесь также есть доступ
									// Проверяем наличие действия с такими именем у формы
									/*$oAdmin_Form_Action = $this->_Admin_Form->Admin_Form_Actions->getByName($actionName);
									if (!is_null($oAdmin_Form_Action))
									{*/
										$actionResult = $oObject->$actionName();
									/*}
									else
									{
										throw new Core_Exception('Action "%actionName" does not exist.',
											array('%actionName' => $actionName)
										);
									}*/
								}

								// Действие вернуло TRUE, прерываем выполнение
								if ($actionResult === TRUE)
								{
									$this->addMessage(ob_get_clean())
										->addContent('')
										->pageTitle('')
										->title('');
									return $this->show();
								}
								elseif ($actionResult !== NULL)
								{
									$aReadyAction[$oObject->getModelName()] = isset($aReadyAction[$datasetKey])
										? $aReadyAction[$datasetKey] + 1
										: 1;
								}
							}
							else
							{
								throw new Core_Exception('Dataset %datasetKey does not exist.',
									array('%datasetKey' => $datasetKey)
								);
							}
						}
					}
				}
				else
				{
					throw new Core_Exception(
						Core::_('Admin_Form.msg_error_access'), array(), 0, FALSE
					);
				}
			}
			catch (Exception $e)
			{
				Core_Message::show($e->getMessage(), 'error');
			}

			// были успешные операции
			foreach ($aReadyAction as $modelName => $actionChangedCount)
			{
				Core_Message::show(Core::_("{$modelName}.{$actionName}_success", NULL, $actionChangedCount));
			}
		}

		$this
			->addMessage(ob_get_clean())
			->addContent($this->_getForm())
			->show();
	}

	/**
	 * Show children elements
	 * @return self
	 */
	public function showChildren()
	{
		// Связанные с формой элементы (меню, строка навигации и т.д.)
		foreach ($this->_children as $oAdmin_Form_Entity)
		{
			$oAdmin_Form_Entity->execute();
		}

		return $this;
	}

	/**
	 * Show form title in administration center
	 * @return self
	 */
	protected function _showFormTitle()
	{
		// Заголовок формы
		if (!is_null($this->_pageTitle) && strlen($this->_pageTitle) > 0)
		{
			// Заголовок
			Admin_Form_Entity::factory('Title')
				->name($this->_pageTitle)
				->execute();
		}

		return $this;
	}

	/**
	 * Edit-in-Place in administration center
	 * @return self
	 */
	protected function _applyEditable()
	{
		$windowId = Core_Str::escapeJavascriptVariable($this->getWindowId());
		$path = Core_Str::escapeJavascriptVariable($this->getPath());

		// Текущий пользователь
		$oUser = Core_Entity::factory('User')->getCurrent();

		// Доступные действия для пользователя
		$aAllowed_Admin_Form_Actions = $this->_Admin_Form->Admin_Form_Actions->getAllowedActionsForUser($oUser);

		// Editable
		$bEditable = FALSE;
		foreach ($aAllowed_Admin_Form_Actions as $o_Admin_Form_Action)
		{
			if ($o_Admin_Form_Action->name == 'apply')
			{
				$bEditable = TRUE;
				break;
			}
		}

		if ($bEditable)
		{
			Core::factory('Core_Html_Entity_Script')
				->type("text/javascript")
				->value("(function($){
					$('#{$windowId} table.admin_table .editable').editable({windowId: '{$windowId}', path: '{$path}'});
				})(jQuery);")
				->execute();
		}

		Core::factory('Core_Html_Entity_Script')
			->type("text/javascript")
			->value("(function($){
				$('#{$windowId} table.admin_table .admin_table_filter :input').on('keydown', $.filterKeyDown);
			})(jQuery);")
			->execute();

		return $this;
	}

	/**
	 * Get form
	 * @return string
	 */
	protected function _getForm()
	{
		ob_start();

		$this
			->_showFormTitle()
			->showChildren()
			->_showFormContent()
			->_showBottomActions()
			->_applyEditable()
			->_pageNavigation();

		return ob_get_clean();
	}

	/**
	 * Apply external replaces in $subject
	 * @param array $aAdmin_Form_Fields Admin_Form_Fields
	 * @param Core_Entity $oEntity entity
	 * @param string $subject
	 * @return string
	 */
	public function doReplaces($aAdmin_Form_Fields, $oEntity, $subject)
	{
		foreach ($this->_externalReplace as $replace_key => $replace_value)
		{
			$subject = str_replace($replace_key, $replace_value, $subject);
		}

		$aColumns = $oEntity->getTableColums();
		foreach ($aColumns as $columnName => $columnArray)
		{
			$subject = str_replace('{'.$columnName.'}', $oEntity->$columnName, $subject);
		}

		return $subject;
	}

	/**
	* Применяет формат отображения $format к строке $str.
	* Если формат является пустой строкой - $str возвращается в исходном виде.
	*
	* @param string $str исходная строка
	* @param string $format форма отображения. Строка формата состоит из директив: обычных символов (за исключением %),
	* которые копируются в результирующую строку, и описатели преобразований,
	* каждый из которых заменяется на один из параметров.
	*/
	public function applyFormat($str, $format)
	{
		!empty($format) && $str = sprintf($format, $str);

		return $str;
	}

	/**
	 * ID of sorting field
	 * @var int
	 */
	protected $_sortingFieldId = NULL;

	/**
	 * Set sorting field by ID
	 * @param int $sortingFieldId field ID
	 * @return self
	 */
	public function sortingFieldId($sortingFieldId)
	{
		if (!is_null($sortingFieldId) && $this->_Admin_Form)
		{
			// Проверка принадлежности форме
			$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field')->find($sortingFieldId);

			if ($oAdmin_Form_Field && $this->_Admin_Form->id == $oAdmin_Form_Field->admin_form_id)
			{
				$this->_sortingFieldId = $sortingFieldId;
			}
			else
			{
				$this->_sortingFieldId = NULL;
				$this->_sortingDirection = NULL;
			}
		}
		return $this;
	}

	/**
	 * Sorting direction
	 * @var int
	 */
	protected $_sortingDirection = NULL;

	/**
	 * Set sorting direction
	 * @param int $sortingDirection direction
	 * @return self
	 */
	public function sortingDirection($sortingDirection)
	{
		if (!is_null($sortingDirection))
		{
			$this->_sortingDirection = intval($sortingDirection);
		}

		return $this;
	}

	/**
	 * Backend callback method
	 * @param string $path path
	 * @param string $action action
	 * @param string $operation operation
	 * @param string $datasetKey dataset key
	 * @param string $datasetValue dataset value
	 * @param string $additionalParams additional params
	 * @param string $limit limit
	 * @param string $current current
	 * @param int $sortingFieldId sorting field ID
	 * @param string $sortingDirection sorting direction
	 * @return string
	 */
	public function getAdminActionLoadAjax($path, $action, $operation, $datasetKey, $datasetValue, $additionalParams = NULL,
		$limit = NULL, $current = NULL, $sortingFieldId = NULL, $sortingDirection = NULL)
	{
		$windowId = Core_Str::escapeJavascriptVariable($this->getWindowId());
		$datasetKey = Core_Str::escapeJavascriptVariable($datasetKey);
		$datasetValue = Core_Str::escapeJavascriptVariable($datasetValue);

		return "$('#{$windowId} #row_{$datasetKey}_{$datasetValue}').toggleHighlight(); "
			. "$.adminCheckObject({objectId: 'check_{$datasetKey}_{$datasetValue}', windowId: '{$windowId}'}); "
			. $this->getAdminLoadAjax($path, $action, $operation, $additionalParams, $limit, $current, $sortingFieldId, $sortingDirection);
	}

	/**
	 * Получение кода вызова adminLoad для события onclick
	 * @param string $path path
	 * @param string $action action
	 * @param string $operation operation
	 * @param string $additionalParams additional params
	 * @param string $limit limit
	 * @param mixed $current current
	 * @param int $sortingFieldId sorting field ID
	 * @param mixed $sortingDirection sorting direction
	 * @return string
	*/
	public function getAdminLoadAjax($path, $action = NULL, $operation = NULL, $additionalParams = NULL,
		$limit = NULL, $current = NULL, $sortingFieldId = NULL, $sortingDirection = NULL)
	{
		/*if ($path)
		{
			// Нельзя, т.к. при изменении у предыдущего параметра URL-а, то действия сломаются
			//$this->AAction = str_replace("'", "\'", $AAction);
		}
		else
		{
			$path = '';
		}*/

		$path = Core_Str::escapeJavascriptVariable($path);
		$action = Core_Str::escapeJavascriptVariable($action);
		$operation = Core_Str::escapeJavascriptVariable($operation);
		$windowId = Core_Str::escapeJavascriptVariable($this->getWindowId());

		$aData = array();

		$aData[] = "path: '{$path}'";

		/*if (is_null($action))
		{
			$action = $this->_action;
		}*/
		$aData[] = "action: '{$action}'";

		/*if (is_null($operation))
		{
			$operation = $this->_operation;
		}*/
		$aData[] = "operation: '{$operation}'";

		is_null($additionalParams) && $additionalParams = $this->_additionalParams;

		$additionalParams = Core_Str::escapeJavascriptVariable(
			str_replace(array('"'), array('&quot;'), $additionalParams)
		);
		$aData[] = "additionalParams: '{$additionalParams}'";

		if (is_null($limit))
		{
			$limit = $this->_limit;
		}
		$limit = intval($limit);
		$aData[] = "limit: '{$limit}'";

		if (is_null($current))
		{
			$current = $this->_current;
		}
		$current = intval($current);
		$aData[] = "current: '{$current}'";

		if (is_null($sortingFieldId))
		{
			$sortingFieldId = $this->_sortingFieldId;
		}
		$sortingFieldId = intval($sortingFieldId);
		$aData[] = "sortingFieldId: '{$sortingFieldId}'";

		if (is_null($sortingDirection))
		{
			$sortingDirection = $this->_sortingDirection;
		}
		$sortingDirection = intval($sortingDirection);
		$aData[] = "sortingDirection: '{$sortingDirection}'";

		$aData[] = "windowId: '{$windowId}'";

		return "$.adminLoad({" . implode(',', $aData) . "}); return false";
	}

	/**
	 * Backend callback method
	 * Для действия из списка элементов
	 * @param string $path path
	 * @param string $action action
	 * @param string $operation operation
	 * @param string $datasetKey dataset key
	 * @param string $datasetValue dataset value
	 * @param string $additionalParams additional params
	 * @param string $limit limit
	 * @param string $current current
	 * @param int $sortingFieldId sorting field ID
	 * @param string $sortingDirection sorting direction
	 * @return string
	 */
	public function getAdminActionLoadHref($path, $action, $operation, $datasetKey, $datasetValue, $additionalParams = NULL,
		$limit = NULL, $current = NULL, $sortingFieldId = NULL, $sortingDirection = NULL)
	{
		is_null($additionalParams) && $additionalParams .= $this->_additionalParams;

		$datasetKey = Core_Str::escapeJavascriptVariable($datasetKey);
		$datasetValue = Core_Str::escapeJavascriptVariable($datasetValue);

		$additionalParams .= '&hostcms[checked][' . $datasetKey . '][' . $datasetValue . ']=1';

		return $this->getAdminLoadHref($path, $action, $operation, $additionalParams, $limit, $current, $sortingFieldId, $sortingDirection);
	}

	/**
	* Получение кода вызова adminLoad для href
	* @param string $path path
	* @param string $action action name
	* @param string $operation operation name
	* @param string $additionalParams additional params
	* @param int $limit count of items on page
	* @param int $current current page number
	* @param int $sortingFieldId ID of sorting field
	* @param int $sortingDirection sorting direction
	* @return string
	*/
	public function getAdminLoadHref($path, $action = NULL, $operation = NULL, $additionalParams = NULL,
		$limit = NULL, $current = NULL, $sortingFieldId = NULL, $sortingDirection = NULL)
	{
		$aData = array();

		$action = rawurlencode($action);
		$aData[] = "hostcms[action]={$action}";

		$operation = rawurlencode($operation);
		$aData[] = "hostcms[operation]={$operation}";

		if (is_null($limit))
		{
			$limit = $this->_limit;
		}
		$limit = intval($limit);
		$aData[] = "hostcms[limit]={$limit}";

		if (is_null($current))
		{
			$current = $this->_current;
		}
		$current = intval($current);
		$aData[] = "hostcms[current]={$current}";

		if (is_null($sortingFieldId))
		{
			$sortingFieldId = $this->_sortingFieldId;
		}
		$sortingFieldId = intval($sortingFieldId);
		$aData[] = "hostcms[sortingfield]={$sortingFieldId}";

		if (is_null($sortingDirection))
		{
			$sortingDirection = $this->_sortingDirection;
		}
		$sortingDirection = intval($sortingDirection);
		$aData[] = "hostcms[sortingdirection]={$sortingDirection}";

		$windowId = rawurlencode($this->getWindowId());
		strlen($windowId) && $aData[] = "hostcms[window]={$windowId}";

		is_null($additionalParams) && $additionalParams = $this->_additionalParams;

		//$additionalParams = str_replace(array("'", '"'), array("\'", '&quot;'), $additionalParams);
		if ($additionalParams)
		{
			// Уже содержит перечень параметров, которые должны быть экранированы
			//$additionalParams = rawurlencode($additionalParams);
			$aData[] = $additionalParams;
		}

		return $path . '?' . implode('&', $aData);
	}

	/**
	* Получение кода вызова adminLoad для события onclick
	* @param string $action action name
	* @param string $operation operation name
	* @param string $additionalParams additional params
	* @param int $limit count of items on page
	* @param int $current current page number
	* @param int $sortingFieldId ID of sorting field
	* @param int $sortingDirection sorting direction
	* @return string
	*/
	public function getAdminSendForm($action = NULL, $operation = NULL, $additionalParams = NULL,
		$limit = NULL, $current = NULL, $sortingFieldId = NULL, $sortingDirection = NULL)
	{
		$aData = array();

		$aData[] = "buttonObject: this";

		// add
		if (is_null($action))
		{
			$action = $this->_action;
		}
		$action = Core_Str::escapeJavascriptVariable($action);
		$aData[] = "action: '{$action}'";

		/*if (is_null($operation))
		{
			$operation = $this->_operation;
		}*/
		$operation = Core_Str::escapeJavascriptVariable($operation);
		$aData[] = "operation: '{$operation}'";

		is_null($additionalParams) && $additionalParams = $this->_additionalParams;

		$additionalParams = Core_Str::escapeJavascriptVariable(
			str_replace(array('"'), array('&quot;'), $additionalParams)
		);
		// Выбранные элементы для действия
		foreach ($this->_checked as $datasetKey => $checkedItems)
		{
			foreach ($checkedItems as $checkedItemId => $v1)
			{
				$datasetKey = intval($datasetKey);
				$checkedItemId = htmlspecialchars($checkedItemId);

				$additionalParams .= empty($additionalParams) ? '' : '&';
				$additionalParams .= 'hostcms[checked][' . $datasetKey . '][' . $checkedItemId . ']=1';
			}
		}
		$aData[] = "additionalParams: '{$additionalParams}'";

		if (is_null($limit))
		{
			$limit = $this->_limit;
		}
		$limit = intval($limit);
		$aData[] = "limit: '{$limit}'";

		if (is_null($current))
		{
			$current = $this->_current;
		}
		$current = intval($current);
		$aData[] = "current: '{$current}'";

		if (is_null($sortingFieldId))
		{
			$sortingFieldId = $this->_sortingFieldId;
		}
		$sortingFieldId = intval($sortingFieldId);
		$aData[] = "sortingFieldId: '{$sortingFieldId}'";

		if (is_null($sortingDirection))
		{
			$sortingDirection = $this->_sortingDirection;
		}
		$sortingDirection = intval($sortingDirection);
		$aData[] = "sortingDirection: '{$sortingDirection}'";

		$windowId = Core_Str::escapeJavascriptVariable($this->getWindowId());
		$aData[] = "windowId: '{$windowId}'";

		return "$.adminSendForm({" . implode(',', $aData) . "}); return false";
	}

	/**
	 * Set dataset conditions
	 * @return self
	 */
	public function setDatasetConditions()
	{
		$aAdmin_Form_Fields = $this->_Admin_Form->Admin_Form_Fields->findAll();

		// Для каждого набора данных формируем свой фильтр,
		// т.к. использовать псевдонимы в SQL операторе WHERE нельзя!
		$aFilter = array();

		$oAdmin_Form_Field_Order = Core_Entity::factory('Admin_Form_Field', $this->_sortingFieldId);

		foreach ($this->_datasets as $datasetKey => $oAdmin_Form_Dataset)
		{
			$oEntity = $oAdmin_Form_Dataset->getEntity();

			if ($oAdmin_Form_Field_Order->allow_sorting)
			{
				// Check field exists in the model
				$fieldName = $oAdmin_Form_Field_Order->name;
				if (isset($oEntity->$fieldName) || method_exists($oEntity, $fieldName)
					// Для сортировки должно существовать св-во модели
					// || property_exists($oEntity, $fieldName)
					|| $oAdmin_Form_Dataset->issetExternalField($fieldName)
				)
				{
					$oAdmin_Form_Dataset->addCondition(array(
							'orderBy' => array($oAdmin_Form_Field_Order->name, $this->_sortingDirection
								? 'DESC'
								: 'ASC'
							)
						)
					);
				}
			}

			foreach ($aAdmin_Form_Fields as $oAdmin_Form_Field)
			{
				if ($oAdmin_Form_Field->allow_filter)
				{
					// Имя поля.
					$fieldName = $oAdmin_Form_Field->name;

					$sFilterValue = Core_Array::get($this->request, "admin_form_filter_{$oAdmin_Form_Field->id}", NULL);

					if ($fieldName != '')
					{
						$sFilterType = $oAdmin_Form_Field->filter_type == 0
							? 'where'
							: 'having';

						// Если имя поля counter_pages.date, то остается date
						$sTmpFieldName = $fieldName;
						strpos($fieldName, '.') !== FALSE && list(, $sTmpFieldName) = explode('.', $fieldName);

						// для HAVING не проверяем наличие поля
						if ($oAdmin_Form_Field->filter_type == 1
							|| isset($oEntity->$sTmpFieldName)
							|| method_exists($oEntity, $sTmpFieldName)
							|| property_exists($oEntity, $sTmpFieldName)
							|| $oAdmin_Form_Dataset->issetExternalField($sTmpFieldName)
						)
						{

							// Тип поля.
							switch ($oAdmin_Form_Field->type)
							{
								case 1: // Строка
								case 2: // Поле ввода
								case 4: // Ссылка
								case 10: // Вычислимое поле
									if (is_null($sFilterValue) || $sFilterValue == '' || mb_strlen($sFilterValue) > 255)
									{
										break;
									}

									$sFilterValue = str_replace(array('*', '?'), array('%', '_'), trim($sFilterValue));

									$oAdmin_Form_Dataset->addCondition(
										array($sFilterType =>
											array($fieldName, 'LIKE', $sFilterValue)
										)
									);
								break;

								case 3: // Checkbox.
								{
									if (!$sFilterValue)
									{
										break;
									}

									if ($sFilterValue != 1)
									{
										$openName = ($oAdmin_Form_Field->filter_type == 0)
											? 'open'
											: 'havingOpen';

										$closeName = ($oAdmin_Form_Field->filter_type == 0)
											? 'close'
											: 'havingClose';

										$oAdmin_Form_Dataset
										->addCondition(array($openName => array()))
										->addCondition(
											array($sFilterType =>
												array($fieldName, '=', 0)
											)
										)
										->addCondition(array('setOr' => array()))
										->addCondition(
											array($sFilterType =>
												array($fieldName, 'IS', NULL)
											)
										)
										->addCondition(array($closeName => array()));
									}
									else
									{
										$oAdmin_Form_Dataset->addCondition(
											array($sFilterType =>
												array($fieldName, '!=', 0)
											)
										);
									}
									break;
								}
								case 5: // Дата-время.
								case 6: // Дата.

									// Дата от.
									$date = trim(Core_Array::get($this->request, "admin_form_filter_from_{$oAdmin_Form_Field->id}"));

									if (!empty($date))
									{
										$date = $oAdmin_Form_Field->type == 5
											? Core_Date::datetime2sql($date)
											: date('Y-m-d 00:00:00', Core_Date::date2timestamp($date));

										$oAdmin_Form_Dataset->addCondition(
											array($sFilterType =>
												array($fieldName, '>=', $date)
											)
										);
									}

									// Дата до.
									$date = trim(Core_Array::get($this->request, "admin_form_filter_to_{$oAdmin_Form_Field->id}"));
									if (!empty($date))
									{
										if ($oAdmin_Form_Field->type == 5)
										{
											// Преобразуем из d.m.Y H:i:s в SQL формат.
											$date = Core_Date::datetime2sql($date);
										}
										else
										{
											// Преобразуем из d.m.Y в SQL формат.
											$date = date('Y-m-d 23:59:59', Core_Date::date2timestamp($date));
										}

										$oAdmin_Form_Dataset->addCondition(
											array($sFilterType =>
												array($fieldName, '<=', $date)
											)
										);
									}
								break;

								case 8: // Список
								{
									if (is_null($sFilterValue))
									{
										break;
									}

									if ($sFilterValue != 'HOST_CMS_ALL')
									{
										$oAdmin_Form_Dataset->addCondition(
											array($sFilterType =>
												array($fieldName, 'LIKE', $sFilterValue)
											)
										);
									}

									break;
								}
							}
						}
					}
				}
			}
		}

		// begin
		$offset = $this->_limit * ($this->_current - 1);

		// Корректируем лимиты, если они указаны общие для N источников
		//if (isset($this->form_params['limit']['all']))
		//{
		// Сумируем общее количество элементов из разных источников
		// и проверяем, меньше ли они $this->form_params['limit']['all']['begin']
		// если меньше - то расчитываем корректный begin
		//if ($datasetKey == 0)
		//{
			if (count($this->_datasets) == 1)
			{
				reset($this->_datasets);
				list(, $oAdmin_Form_Dataset) = each($this->_datasets);

				$oAdmin_Form_Dataset
					->limit($this->_limit)
					->offset($offset)
					->load();

				// Данные уже были загружены при первом применении лимитов и одном источнике
				$bLoaded = TRUE;
			}
			else
			{
				$bLoaded = FALSE;
			}

			$iTotalCount = $this->getTotalCount();

			if ($iTotalCount < $offset)
			{
				$current = floor($iTotalCount / $this->_limit);

				if ($current <= 0)
				{
					$current = 1;
					$offset = 0;
					$bLoaded = FALSE;
				}

				$this->current($current);
			}
			elseif($iTotalCount == $offset && $offset >= $this->_limit)
			{
				$offset -= $this->_limit;
				$bLoaded = FALSE;
			}
		//}

		// При экспорте в CSV лимиты недействительны
		/*if ($this->export_csv)
		{
			if (isset($this->form_params['limit']))
			{
				unset($this->form_params['limit']);
			}
		}*/

		try
		{
			// $iTotalCount - 48
			foreach ($this->_datasets as $datasetKey => $oAdmin_Form_Dataset)
			{
				// 0й - 17
				$datasetCount = $oAdmin_Form_Dataset->getCount();

				// 17 >
				if ($datasetCount > $offset)
				{
					$oAdmin_Form_Dataset
						->limit($this->_limit)
						->offset($offset)
						->loaded($bLoaded);
				}
				else // Не показывать, т.к. очередь другого датасета
				{
					$oAdmin_Form_Dataset
						->limit(0)
						->offset(0)
						->loaded($bLoaded);
				}

				// Предыдущие можем смотреть только для 1-го источника и следующих
				if ($datasetKey > 0)
				{
					// Если число элементов предыдущего источника меньше текущего начала
					$prevDatasetCount = $this->_datasets[$datasetKey - 1]->getCount();

					if ($prevDatasetCount - $offset // 17 - 10 = 7
						< $this->_limit // 10
					)
					{
						$begin = $offset - $prevDatasetCount;

						if ($begin < 0)
						{
							$begin = 0;
						}

						$oAdmin_Form_Dataset
						->limit($this->_limit - ($prevDatasetCount - $offset) - $begin)
						->offset($begin)
						->loaded($bLoaded);
					}
					else
					{
						$oAdmin_Form_Dataset
						->limit(0)
						->offset(0)
						->loaded($bLoaded);
					}
				}
			}
		}
		catch (Exception $e)
		{
			Core_Message::show($e->getMessage(), 'error');
		}

		return $this;
	}

	/**
	 * Apply external changes for filter
	 * @param Admin_Form_Dataset $oAdmin_Form_Dataset dataset
	 * @param Admin_Form_Field $oAdmin_Form_Field field
	 * @return object
	 */
	protected function _changeField($oAdmin_Form_Dataset, $oAdmin_Form_Field)
	{
		// Проверяем, установлено ли пользователем перекрытие параметров для данного поля.
		$aChangedFields = $oAdmin_Form_Dataset->getFieldChanges($oAdmin_Form_Field->name);

		if ($aChangedFields)
		{
			$aChanged = $aChangedFields + $oAdmin_Form_Field->toArray();
			$oAdmin_Form_Field_Changed = (object)$aChanged;
		}
		else
		{
			$oAdmin_Form_Field_Changed = $oAdmin_Form_Field;
		}

		return $oAdmin_Form_Field_Changed;
	}
}