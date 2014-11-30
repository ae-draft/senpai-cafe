<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Формы центра администрирования.
 *
 * Файл: /modules/admin_forms/admin_forms.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class admin_forms
{
	/**
	* Свойство с параметрами формы
	*
	* @var array
	* - $form_params['data'] - массив наборов данных
	* - $form_params['limit'] - массив лимитов для источников данных по схеме [] = array('begin' => $x, 'count' => $y)
	* - $form_params['error'] - тест ошибки, передаваемый из пользовательского обработчика
	* - $form_params['total_count'] - массив с общим числом элементов на страницу для каждого набора данных
	* - $form_params['redirect'] - адрес страницы, на которую необходимо осуществить редирект
	* - $form_params['current_page'] - номер текущей страницы
	* - $form_params['on_page'] - элементов на страницу
	* - $form_params['menus'] - массив меню
	* - $form_params['field_params'] - массив с перекрываемыми параметрами полей по наборам данных для каждого идентификатора набора данных
	* - в виде $form_params['field_params'][<идентификатор набора данных>][<имя поля>] - массив параметров
	* - $form_params['field_params'][<идентификатор набора данных>][<имя поля>]['callback_function'] - имя функции обратного вызова
	* - $form_params['path_array'] - массив хлебных крошек, передается по форме
	* <br />[x]['name'] - название ссылки
	* <br />[x]['link'] - адрес ссылки
	* <br />[x]['onclick'] - код события onclick
	* - $form_params['path_separator'] - разделитель для строки навигации
	*/
	var $form_params = array();

	/**
	* Массив внешнех подстановок, применяется в DoReplaces()
	*
	* @var array
	*/
	var $external_replaces = array();

	/**
	* Строка с суммарной строкой ошибок, произошедших в пользовательских событиях.
	*
	* @var string
	*/
	var $user_function_message = '';

	/**
	* Строка с результатом выполнения пользовательской функции.
	*
	* @var string
	*/
	var $user_function_result = '';

	/**
	* Идентификатор поля сортировки.
	*
	* @var int
	*/
	var $order_field_id = 0;

	/**
	* Направление сортировки: 1 - ASC, 2 - DESC.
	*
	* @var int
	*/
	var $order_field_direction = 1;

	/**
	* Число элементов формы на страницу
	*
	* @var int
	*/
	var $on_page = 0;

	/**
	* Номер текущей страницы формы
	*
	* @var int
	*/
	var $page_number = 0;

	/**
	* Сообщения отладки
	*
	* @var string
	*/
	var $debug;

	/**
	* Код для вывода под меню
	*
	* @var string
	*/
	var $data_under_menu;

	/**
	 * Флаг экспорта в CSV
	 *
	 * @var boolean
	 */
	var $export_csv = false;

	/**
	* Значение AAction, переданное DoLoadAjax
	*
	* @var str
	*/
	var $AAction = '';

	/**
	* Значение AAdditionalParams, переданное DoLoadAjax
	*
	* @var str
	*/
	var $AAdditionalParams = '';

	/**
	* Массив-кэш для метода GetAdminFormsWord()
	*
	* @var array
	*/
	var $CacheGetAdminFormsWord = array();

	/**
	 * Массив-кэш для GetLanguageByShortName()
	 *
	 * @var array
	 */
	var $CacheGetLanguageByShortName = array();

	/**
	 * Массив кэш для GetLanguage()
	 *
	 * @var array
	 */
	var $CacheGetLanguage = array();

	/**
	 * Наименование функции обратного вызова
	 * @var string
	 */
	var $CallbackFunctionName = 'LoadAjaxData';

	/**
	 * Показывать фильтр
	 * @var bool
	 */
	var $ShowFilter = true;

	/**
	 * Показывать действия
	 * @var bool
	 */
	var $ShowOperations = true;

	/**
	 * Показывать нижнюю строку с действиями
	 * @var bool
	 */
	var $ShowBottom = true;

	/**
	 * Идентификатор окна
	 */
	var $window_id = NULL;

	/**
	* Конструктор, инициализирует значения свойств класса $this->AAction, $this->AAdditionalParams
	*
	* @return admin_forms
	*/
	function admin_forms()
	{
		// Текущий URL страницы
		$current_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$url = parse_url(urldecode($current_url));

		// Если DoLoadAjax передал данные об Action и внешних параметрах - пропишем их
		if (isset($_REQUEST['hostcmsAAction']))
		{
			$this->AAction = str_replace("'", "\'", Core_Type_Conversion::toStr($_REQUEST['hostcmsAAction']));
		}
		else // Формируем самостоятельно
		{
			$this->AAction = Core_Type_Conversion::toStr($url['path']);
		}

		// был isset(), заменили на empty(), т.к. в ФМ терялся cdir (hostcmsAAdditionalParams передавался просто пустой)
		if (isset($_REQUEST['hostcmsAAdditionalParams']))
		{
			$this->AAdditionalParams = str_replace("'", "\'", Core_Type_Conversion::toStr($_REQUEST['hostcmsAAdditionalParams']));
		}
		else // Формируем самостоятельно
		{
			if (isset($url['query']))
			{
				// Отрезаем идентификатор формы и операцию
				$action = $url['query'];

				$action = preg_replace('/admin_forms_id=(\d+)&/iu', '', $action);
				$action = preg_replace('/operation=(\w+)&/iu', '', $action);
				$action = preg_replace('/operation=(\w+)/iu', '', $action); // если остался только operation (без ведущего &)

				$action = preg_replace('/admin_forms_on_page=(\d+)&/iu', '', $action);
				$action = preg_replace('/order_field_id=(\d+)&/iu', '', $action);
				$action = preg_replace('/order_field_direction=(\d+)&/iu', '', $action);
				$action = preg_replace('/limit=(\d+)&/iu', '', $action);
				$action = preg_replace('/limit=(\d+)/iu', '', $action);

				$action = preg_replace('/window_id=(\w+)/iu', '', $action);

				// Удаляем список предыдущих выбранных объектов, при этом & вначале может и не быть
				$action = preg_replace('/&?check_(\d+)_(\d+)=(\d+)/iu', '', $action);

				if (!empty($action))
				{
					$action = '&' . $action;
				}

				$this->AAdditionalParams = $action;
			}

			// Вместо получения из поста нужно скрытым параметром передавать hostcmsAAdditionalParams
		}

		// Если текущая страница не определена - определим ее
		if (empty($this->form_params['current_page']))
		{
			if (isset($_REQUEST['limit']) && $_REQUEST['limit'] > 0)
			{
				$this->form_params['current_page'] = Core_Type_Conversion::toInt($_REQUEST['limit']);
				$this->page_number = Core_Type_Conversion::toInt($_REQUEST['limit']);
			}
			else
			{
				$this->form_params['current_page'] = 0;
				$this->page_number = 0;
			}
		}

		// Флаг экспорта в CSV
		if (isset($_REQUEST['export_csv']))
		{
			$this->export_csv = true;
		}
	}

	/**
	* Устанавливает значение AAdditionalParams
	*
	* @param string $AAdditionalParams
	*/
	function setAAdditionalParams($AAdditionalParams)
	{
		$this->AAdditionalParams = $AAdditionalParams;
	}

	/**
	* Устанавливает значение AAction
	*
	* @param string $AAction
	*/
	function setAAction($AAction)
	{
		$this->AAction = $AAction;
	}

	function GetWindowId()
	{
		if ($this->window_id === NULL)
		{
			$window_id = isset($_REQUEST['window_id'])
			? str_replace("'", "\'", Core_Type_Conversion::toStr($_REQUEST['window_id']))
			: 'id_content';
		}
		else
		{
			$window_id = $this->window_id;
		}

		return $window_id;
	}

	/**
	* Получение кода вызова DoLoadAjax для события onClick
	*
	* @param string $AAction адрес страницы для обращения, например, '/admin/admin_forms/admin_forms.php'
	* @param string $AAdditionalParams дополнительные параметры для передачи на страницу, например "&admin_forms_edit_id=$admin_forms_id"
	* @param int $AAdminFromsId идентификатор формы центра администрирования
	* @param string $AOperation наименование операции, например, 'load_data'
	* @param int $ALimit позиция, начиная с которой начинается вывод записей, по умолчанию 0. Необязательный параметр.
	* @param int $AOnPage количество записей на страницу. Необязательный параметр.
	* @param mixed $AOrderFieldId Идентификатор поля сортировки. Необязательный параметр.
	* @param mixed $AOrderDirection Направление сортировки (1 - ASC, 2 - DESC). Необязательный параметр.
	* @return string строка кода вызова
	*/
	function GetOnClickCallDoLoadAjax($AAction, $AAdditionalParams, $AAdminFromsId, $AOperation, $ALimit = 0, $AOnPage = 0, $AOrderFieldId = false, $AOrderDirection = false)
	{
		if ($AAction)
		{
			// Нельзя, т.к. при изменении у предыдущего параметра URL-а, то действия сломаются
			//$this->AAction = str_replace("'", "\'", $AAction);
		}
		else
		{
			$AAction = '';
		}

		if ($AAdditionalParams)
		{
			$AAdditionalParams = str_replace("'", "\'", $AAdditionalParams);
		}
		else
		{
			$AAdditionalParams = $this->AAdditionalParams;
		}

		if (!$AOrderFieldId)
		{
			$AOrderFieldId = $this->order_field_id;
		}

		if (!$AOrderDirection)
		{
			$AOrderDirection = $this->order_field_direction;
		}

		if ($ALimit === false)
		{
			$ALimit = $this->page_number;
		}

		if ($AOnPage === false)
		{
			$AOnPage = $this->on_page;
		}

		// 6.0
		$window_id = $this->GetWindowId();

		return "DoLoadAjax('" . $AAction . "', '" . $AAdditionalParams."', '$AAdminFromsId', '$AOperation', ".intval($ALimit).", ".intval($AOnPage).", '$AOrderFieldId', '$AOrderDirection', '$window_id'); return false;";
	}

	/**
	* Получение кода вызова DoLoadAjax для атрибута href
	*
	* @param string $AAction адрес страницы для обращения, например, '/admin/admin_forms/admin_forms.php'
	* @param string $AAdditionalParams дополнительные параметры для передачи на страницу, например "&admin_forms_edit_id=$admin_forms_id"
	* @param int $AAdminFromsId идентификатор формы центра администрирования
	* @param string $AOperation наименование операции, например, 'load_data'
	* @param int $ALimit позиция, начиная с которой начинается вывод записей, по умолчанию 0. Необязательный параметр.
	* @param int $AOnPage количество записей на страницу. Необязательный параметр.
	* @param mixed $AOrderFieldId Идентификатор поля сортировки. Необязательный параметр.
	* @param mixed $AOrderDirection Направление сортировки (1 - ASC, 2 - DESC). Необязательный параметр.
	* @return string строка кода вызова
	*/
	function GetHtmlCallDoLoadAjax($AAction, $AAdditionalParams, $AAdminFromsId, $AOperation, $ALimit = 0, $AOnPage = 0, $AOrderFieldId = false, $AOrderDirection = false)
	{
		if ($AAction)
		{
			// Нельзя, т.к. при изменении у предыдущего параметра URL-а, то действия сломаются
			// $this->AAction = str_replace("'", "\'", $AAction);
		}
		else
		{
			$AAction = '';
		}

		if ($AAdditionalParams)
		{
			$AAdditionalParams = str_replace("'", "\'", $AAdditionalParams);
		}
		else
		{
			$AAdditionalParams = $this->AAdditionalParams;
		}

		$str = '';

		if (!$AOrderFieldId)
		{
			$AOrderFieldId = $this->order_field_id;
		}
		else
		{
			$str .= "&order_field_id={$AOrderFieldId}";
		}

		if (!$AOrderDirection)
		{
			$AOrderDirection = $this->order_field_direction;
		}
		else
		{
			$str .= "&order_field_direction={$AOrderDirection}";
		}

		$str .= "&limit={$ALimit}";
		/*
		if (!$ALimit)
		{
		//$ALimit = $this->page_number;
		}
		else
		{
		$str .= "&limit={$ALimit}";
		}*/

		if (!$AOnPage)
		{
			//$AOnPage = $this->on_page;
		}
		else
		{
			$str .= "&admin_forms_on_page={$AOnPage}";
		}

		return "{$AAction}?admin_forms_id={$AAdminFromsId}&operation={$AOperation}{$str}{$AAdditionalParams}";
	}


	/**
	* Формирует ссылку действия для содержимого атрибута onclick тега a
	*
	* @param string $AOperation Наименование действия, например, 'edit_affiliate'
	* @param string $AItemName Наименование объекта с указанием источника и ID объекта по схеме check_{номер источника}_{код объекта}, например, 'check_0_10'
	* @param int $AAdminFromsId Идентификатор формы центра администрирования
	* @param int $ALimit Позиция, начиная с которой начинается вывод записей, по умолчанию 0. Необязательный параметр.
	* @param int $AOnPage Количество записей на страницу. Необязательный параметр.
	* @param mixed $AOrderFieldId Идентификатор поля сортировки. Необязательный параметр.
	* @param mixed $AOrderDirection Направление сортировки (1 - ASC, 2 - DESC). Необязательный параметр.
	* @param mixed $AAction Адрес страницы для обращения, например, '/admin/admin_forms/admin_forms.php'.
	* @param mixed $AAdditionalParams Дополнительные параметры для передачи на страницу, например "&admin_forms_edit_id=$admin_forms_id"
	* @return string строка кода вызова
	*/
	function GetOnClickCallTrigerSingleAction($AOperation, $AItemName, $AAdminFromsId, $ALimit = 0, $AOnPage = 0, $AOrderFieldId = false, $AOrderDirection = false, $AAction = false, $AAdditionalParams = false)
	{
		if ($AAction)
		{
			// Нельзя, т.к. при изменении у предыдущего параметра URL-а, то действия сломаются
			$this->AAction = str_replace("'", "\'", $AAction);
		}
		else
		{
			//$AAction = './';
		}

		if ($AAdditionalParams)
		{
			$AAdditionalParams = str_replace("'", "\'", $AAdditionalParams);
		}
		else
		{
			$AAdditionalParams = $this->AAdditionalParams;
		}

		if (!$AOrderFieldId)
		{
			$AOrderFieldId = $this->order_field_id;
		}

		if (!$AOrderDirection)
		{
			$AOrderDirection = $this->order_field_direction;
		}

		if ($ALimit === false)
		{
			$ALimit = $this->page_number;
		}

		if ($AOnPage === false)
		{
			$AOnPage = $this->on_page;
		}

		// 6.0
		$window_id = $this->GetWindowId();

		return "TrigerSingleAction('" . $this->AAction . "', '" . $AAdditionalParams."', '$AOperation', '$AItemName', '$AAdminFromsId', ".intval($ALimit).", ".intval($AOnPage).", '$AOrderFieldId', '$AOrderDirection', '$window_id'); return false;";
	}

	/**
	* Формирует ссылку действия для содержимого атрибута href тега a
	*
	* @param string $AOperation Наименование действия, например, 'edit_affiliate'
	* @param string $AItemName Наименование объекта с указанием источника и ID объекта по схеме check_{номер источника}_{код объекта}, например, 'check_0_10'
	* @param int $AAdminFromsId Идентификатор формы центра администрирования
	* @param int $ALimit Позиция, начиная с которой начинается вывод записей, по умолчанию 0. Необязательный параметр.
	* @param int $AOnPage Количество записей на страницу. Необязательный параметр.
	* @param mixed $AOrderFieldId Идентификатор поля сортировки. Необязательный параметр.
	* @param mixed $AOrderDirection Направление сортировки (1 - ASC, 2 - DESC). Необязательный параметр.
	* @param mixed $AAction Адрес страницы для обращения, например, '/admin/admin_forms/admin_forms.php'.
	* @param mixed $AAdditionalParams Дополнительные параметры для передачи на страницу, например "&admin_forms_edit_id=$admin_forms_id"
	* @return string строка кода вызова
	*/
	function GetHtmlCallTrigerSingleAction($AOperation, $AItemName, $AAdminFromsId, $ALimit = 0, $AOnPage = 0, $AOrderFieldId = false, $AOrderDirection = false, $AAction = false, $AAdditionalParams = false)
	{
		if ($AAction)
		{
			// Нельзя, т.к. при изменении у предыдущего параметра URL-а, то действия сломаются
			$this->AAction = str_replace("'", "\'", $AAction);
		}
		else
		{
			//$AAction = './';
		}

		if ($AAdditionalParams)
		{
			$AAdditionalParams = str_replace("'", "\'", $AAdditionalParams);
		}
		else
		{
			$AAdditionalParams = $this->AAdditionalParams;
		}

		if (!$AOrderFieldId)
		{
			$AOrderFieldId = $this->order_field_id;
		}

		if (!$AOrderDirection)
		{
			$AOrderDirection = $this->order_field_direction;
		}

		return "{$this->AAction}?admin_forms_id={$AAdminFromsId}&operation={$AOperation}&$AItemName=1{$AAdditionalParams}";
	}

	/**
	* Добавление текста, выводимого перед формой
	*
	* @param string $message текст сообщения
	*/
	function AddUserMessage($message)
	{
		$this->user_function_message .= Core_Type_Conversion::toStr($message);
	}

	/**
	* Отображение хлебных крошек.
	* Используются данные $this->form_params['path_array'] и $this->form_params['path_separator']
	*
	*/
	function ShowBreadCrumbs()
	{
		// Отображение хлебных крошек.
		if (isset($this->form_params['path_array']))
		{
			if (is_array($this->form_params['path_array']))
			{
				if (isset($this->form_params['path_separator']))
				{
					$separator = Core_Type_Conversion::toStr($this->form_params['path_separator']);
				}
				else
				{
					$separator = '&nbsp;<span class="arrow_path">&#8594;</span>&nbsp;'; // ?
				}

				$array_link = array();

				/* Если была передана хотя бы одна крошка*/
				if (count($this->form_params['path_array']) > 0)
				{
					/* Строим ссылки по переданным крошкам, чтобы потом их объединить*/
					foreach ($this->form_params['path_array'] as $value_path_array)
					{
						$array_link[] = '<a href="'.$value_path_array['link'].'" onclick="'.Core_Type_Conversion::toStr($value_path_array['onclick']).'">'.htmlspecialchars($value_path_array['name']).'</a>';
					}
				}

				?><p><?php echo implode($separator, $array_link)?></p><?php
			}
		}
	}

	/**
	* Обработчик вызывает обработку действий и построение формы
	*
	* @param int $admin_forms_id Идентификатор формы центра администрирования
	* @return bool
	*/
	function ProcessAjax($admin_forms_id)
	{
		// Вызываем отображение формы
		$array = $this->ShowForm($admin_forms_id);

		if (is_array($array))
		{
			$GLOBALS['_RESULT'] = $array;

			// Был AJAX запрос - возвращаем данные
			if (isset($_REQUEST['JsHttpRequest']))
			{
				$JsHttpRequest = new JsHttpRequest('UTF-8');

				// Отображаем результат
				echo $JsHttpRequest->LOADER;
			}
			else // Иначе выводим в HTML
			{
				/* <div id="id_message"><?php echo $GLOBALS['_RESULT']['error']?></div> */
				?>
				<div id="id_content">
				<?php echo $GLOBALS['_RESULT']['form_html']?><?php

				// XML-код для ShowXML
			 if (isset ($_SESSION['valid_user']))
			 {
			 	$kernel = & singleton('kernel');
			 echo $kernel->GetXmlContent();
			 }
				?></div><?php

				if (!empty($GLOBALS['_RESULT']['title']))
				{
					?><script type="text/javascript">document.title = '<?php echo str_replace("'", "\'", $GLOBALS['_RESULT']['title'])?>';</script><?php
				}
				?><script type="text/javascript">$.afterContentLoad($("#id_content"));</script><?php
			}
		}
		else
		{
			show_error_message("Ошибка отображения формы");
		}

		//exit();
	}

	/**
	* Обрабатывает действия формы, вызывает построение формы
	*
	* @param int $admin_forms_id Идентификатор формы центра администрирования
	* @param mixed $operation наименование действия, если false - получается из $_GET['operation']. по умолчанию false
	* @return array массив для передачи на отправку ProcessAjax, формат:
	* <br/>array(
	* <br/>'form_html' => Основной код,
	* <br/>'error' => Текст ошибки,
	* <br/>'title' => Заголовок страницы);
	*/
	function ShowForm($admin_forms_id = 0, $operation = false)
	{
		$admin_forms_id = intval($admin_forms_id);

		$DataBase = & singleton('DataBase');

		$date_time = & singleton('DateClass');

		// В $_GET['operation'] хранится название события, которое необходимо выполнить.
		if (!$operation && isset($_REQUEST['operation']))
		{
			$operation = Core_Type_Conversion::toStr($_REQUEST['operation']);
		}
		else
		{
			$operation = 'load_data';
		}
		/*elseif (!$operation)
		{
		return true;
		}*/

		// В $_GET['operation'] хранится название события, которое необходимо выполнить.
		/*
		if (!isset($_GET['operation']))
		{
		return true;
		}
		*/

		// Получаем информацию о текущей форме.
		$form_row = $this->GetAdminForm($admin_forms_id);

		// Получаем настройки для текущей формы.
		$form_settings = $this->GetSettingsForForm($admin_forms_id);

		if (!$form_settings)
		{
			$kernel = & singleton('kernel');

			$form_settings = array();
			$form_settings['users_id'] = $kernel->GetCurrentUser();
			$form_settings['admin_forms_id'] = $admin_forms_id;
		}

		// Ошибка - Не найдена форма в БД
		if (!$form_row)
		{
			return array(
			'error' => Core_Message::get("Не найдена форма {$admin_forms_id} в БД"),
			'form_html' => ''
			);
		}

		ob_start();

		// Вызов пользовательских ф-ций.
		$result_exec_user_function = $this->ExecuteUsersEvents($admin_forms_id, $operation);

		// Функция пользователя должна возвращать true, если вывод необходимо прекратить
		// или false (или ничего), если вывод необходимо продолжить
		if ($result_exec_user_function === true

		// Не срабатывает действие при открытии в новом окне!
		/*&& isset($_REQUEST['JsHttpRequest'])*/
		)
		{
			return array(
			'error' => $this->user_function_message,
			'form_html' => $this->user_function_result,
			'title' => isset($this->form_params['title']) ? $this->form_params['title'] : '');
		}

		if (!isset($_REQUEST['limit'])
		|| $_REQUEST['limit'] == ''
		|| $_REQUEST['limit'] < 0)
		{
			// При выполнении через событие (смена активности, например) - теряется
			if (Core_Type_Conversion::toInt($form_settings['admin_forms_settings_page_number']) > 0
			|| (isset($_REQUEST['limit']) && $_REQUEST['limit'] < 0))
			{
				$_REQUEST['limit'] = Core_Type_Conversion::toInt($form_settings['admin_forms_settings_page_number']);
				$this->page_number = $_REQUEST['limit'];
			}
		}
		else
		{
			$form_settings['admin_forms_settings_page_number'] = Core_Type_Conversion::toInt($_REQUEST['limit']);
			$this->page_number = $form_settings['admin_forms_settings_page_number'];
		}

		// Корректировка номера страницы и количества записей на страницу
		// в соответствие с настройками пользователя.
		if (!isset($_REQUEST['admin_forms_on_page']))
		{
			if (Core_Type_Conversion::toInt($form_settings['admin_forms_settings_on_page']) > 0)
			{
				$_REQUEST['admin_forms_on_page'] = Core_Type_Conversion::toInt($form_settings['admin_forms_settings_on_page']);
			}
			else
			{
				$form_settings['admin_forms_settings_on_page'] = $this->GetOnPageCount($admin_forms_id);
			}
		}
		// Иначе обрабытваем, только если число страниц СМЕНИЛОСЬ !!!
		elseif (Core_Type_Conversion::toInt($_REQUEST['admin_forms_on_page']) != Core_Type_Conversion::toInt($form_settings['admin_forms_settings_on_page']))
		{
			// Сбрасываем текущую страницу в 0
			//$form_settings['admin_forms_settings_page_number'] = 0;

			// Рассчитаем страницу по формуле X = A* B / C,
			// где A - бывшее число элементов на страницу
			// B - бывшая страница
			// C - новое число элементов на страницу

			// Для сохранения в базе
			if (Core_Type_Conversion::toInt($_REQUEST['admin_forms_on_page']) > 0)
			{
				$form_settings['admin_forms_settings_page_number'] = intval($form_settings['admin_forms_settings_on_page']* Core_Type_Conversion::toInt($form_settings['admin_forms_settings_page_number']) / Core_Type_Conversion::toInt($_REQUEST['admin_forms_on_page']));
			}

			// Для применения на форме
			// 24-01-08
			//$_REQUEST['limit'] = $form_settings['admin_forms_settings_page_number'];

			// Устанавливаем переданное значение количества на страницу
			$form_settings['admin_forms_settings_on_page'] = Core_Type_Conversion::toInt($_REQUEST['admin_forms_on_page']);
		}

		if (isset($form_settings['admin_forms_settings_page_number']))
		{
			$this->page_number = $form_settings['admin_forms_settings_page_number'];
		}

		//echo "<br>Line = ".__LINE__.", c = ".$form_settings['admin_forms_settings_on_page'];
		$this->on_page = $form_settings['admin_forms_settings_on_page'];

		// Загружаем пользовательские данные.
		$fName = $this->CallbackFunctionName;

		if (function_exists($fName))
		{
			$this->form_params = $fName($this);

			// Добавлено 28-08-07
			if (isset($this->form_params['error']))
			{
				$this->AddUserMessage($this->form_params['error']);
			}
		}
		else
		{
			/*
			// Ошибка - Не найден обработчик для загрузки данных
			*/

			return array(
			'error' => '<font color="Red">Не найден обработчик для загрузки данных.</font>',
			'form_html' => ' ');
		}

		// Формируем фильтр: сначала извлекаем все поля.
		$all_fields = $this->GetAllAdminFormFields($admin_forms_id);

		// Для каждого набора данных формируем свой фильтр,
		// т.к. использовать псевдонимы в SQL операторе WHERE нельзя!
		$this->form_params['filter'] = array();

		if (isset($this->form_params['data']))
		{
			reset($this->form_params['data']);

			foreach ($this->form_params['data'] as $dataset_id => $dataset_value)
			{
				$filter_sql = '';
				if ($all_fields)
				{
					reset($all_fields);
					foreach ($all_fields as $row)
					{
						if ($row['admin_forms_field_allow_filter'])
						{
							// Имя поля.
							$field_name = quote_smart(Core_Type_Conversion::toStr($row['admin_forms_field_name']));

							// Преобразуем псевдонимы полей в реальные названия, для этого
							// в AJAX загрузчике пользователем должна быть реализована
							// функция ResolveFieldName, которая принимает название псевдонима поля (или реальное название)
							// и индекс набора данных, а возвращает реальное название поля по ссылке
							// первым параметром.
							if (function_exists("ResolveFieldName"))
							{
								/**
							* $field_name - название поля (передается по-ссылке)
							* $dataset_id - индекс набора данных
							*/
								ResolveFieldName($field_name, $dataset_id);
							}
							// Тип поля.
							switch ($row['admin_forms_field_type'])
							{
								case 1: // Строка.
								case 4: // Ссылка.
								case 10: // Вычислимое поле
								case 2: // Поле ввода.
								{
									if ($field_name != '')
									{
										$like = quote_smart(Core_Type_Conversion::toStr($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]));

										// Если значение больше 255 символов - прерываем
										if (mb_strlen($like) > 255)
										{
											break;
										}

										if ($like == '')
										{
											break;
										}
										// Формируем LIKE.
										$like = str_replace('*', '%', $like);
										$like = str_replace('?', '_', $like);
										$filter_sql .= " AND $field_name LIKE '$like' ";
										//echo "==$filter_sql==";
										break;
									}

								}
								case 3: // Checkbox.
								{
									if ($field_name != '')
									{

										$selected = Core_Type_Conversion::toInt($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]);

										if (!$selected)
										{
											break;
										}

										if ($selected != 1)
										{
											$filter_sql .= " AND ($field_name = '0' OR $field_name IS NULL)";
										}
										else
										{
											$filter_sql .= " AND $field_name != '0'";
										}
										break;
									}
								}
								case 5: // Дата-время.
								case 6: // Дата.
								{
									if ($field_name != '')
									{
										// Дата от.
										$date = trim(Core_Type_Conversion::toStr($_GET["admin_form_filter_from_{$row['admin_forms_field_id']}"]));
										if (!empty($date))
										{
											if ($row['admin_forms_field_type'] == 5)
											{
												// Преобразуем из d.m.Y H:i:s в SQL формат.
												$date = $date_time->datetime_format_sql($date);
											}
											else
											{
												// Преобразуем из d.m.Y в SQL формат.
												$date = $date_time->date_format_sql($date);
											}
											$filter_sql .= " AND $field_name >= '$date' ";
										}

										// Дата до.
										$date = trim(Core_Type_Conversion::toStr($_GET["admin_form_filter_to_{$row['admin_forms_field_id']}"]));
										if (!empty($date))
										{
											if ($row['admin_forms_field_type'] == 5)
											{
												// Преобразуем из d.m.Y H:i:s в SQL формат.
												$date = $date_time->datetime_format_sql($date);
											}
											else
											{
												// Преобразуем из d.m.Y в SQL формат.
												$date = $date_time->date_format_sql($date);
											}
											$filter_sql .= " AND $field_name <= '$date' ";
										}
										break;
									}
								}
								case 8: // Список
								{
									if ($field_name != '' && isset($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]))
									{
										$like = quote_smart(Core_Type_Conversion::toStr($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]));

										if (Core_Type_Conversion::toStr($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]) != 'HOST_CMS_ALL')
										{
											$filter_sql .= " AND $field_name = '$like' ";
										}
										break;
									}
								}
							}
						}
					}
				}

				// Добавляем фильтр с тем же индексом, что и у dataset'a.
				$this->form_params['filter'][$dataset_id] = $filter_sql;
			}
		}

		// Если фильтр передан, сохраняем.
		/*if (count($this->form_params['filter']) > 0)
		{
		$form_settings['admin_forms_settings_filter'] = serialize($this->form_params['filter']);
		}
		else
		{
		// Фильтр не передан, пытаемся считать сохраненный ранее фильтр.
		$filter = $form_settings['admin_forms_settings_filter'];
		if (!empty($filter))
		{
		$this->frm_params['filter'] = unserialize($filter);
		if (!is_array($this->form_params['filter']))
		{
		$this->form_params['filter'] = array();
		}
		}
		}*/

		// Определяем заданы ли поле и направление сортировки пользователем.
		$order_array = array();
		$order_direct_str = '';

		// Название ключей сортировки при сортировки многомерного массива (не запроса)
		$order_array_for_array = array();

		// Поле сортировки.
		$this->order_field_id = 0;

		// Направление сортировки (1 - ASC, 2 - DESC).
		$this->order_field_direction = 1;

		// Поле и направление сортировки были переданы от пользователя
		if (isset($_GET['order_field_id']) && isset($_GET['order_field_direction'])
		&& $_GET['order_field_id'] > 0 && $_GET['order_field_direction'] > 0)
		{
			$this->order_field_id = Core_Type_Conversion::toInt($_GET['order_field_id']);
			$this->order_field_direction = Core_Type_Conversion::toInt($_GET['order_field_direction']);

			// Поле сортировки было передано, запишем настройки.
			$form_settings['admin_forms_settings_order_field_id'] = $this->order_field_id;
			$form_settings['admin_forms_settings_order_direction'] = $this->order_field_direction;
		}
		// если данные еще не определены - получим их из сохраненных настроек
		elseif (!$this->order_field_id)
		{
			// Считываем данные из настроек.
			if (Core_Type_Conversion::toInt($form_settings['admin_forms_settings_order_field_id']))
			{
				//var_dump($form_settings);
				$this->order_field_id = Core_Type_Conversion::toInt($form_settings['admin_forms_settings_order_field_id']);
				$this->order_field_direction = Core_Type_Conversion::toInt($form_settings['admin_forms_settings_order_direction']);
			}
			// Запишем значение по умолчанию
			elseif (!empty($form_row['admin_forms_default_order_field']))
			{
				$oAdmin_Form_Field = Core_Entity::factory('Admin_Form', $admin_forms_id)
					->admin_form_fields
					->getByName($form_row['admin_forms_default_order_field']);

				if ($oAdmin_Form_Field)
				{
					$this->order_field_id = $oAdmin_Form_Field->id;
					$this->order_field_direction = $form_row['admin_forms_default_order_direction'];
				}
			}
		}

		// Если ИД формы получено - получим имя и направление сортировки и запишем в строку
		if ($this->order_field_id && isset($this->form_params['data'])
		&& is_array($this->form_params['data']) && count($this->form_params['data']) > 0)
		{
			reset($this->form_params['data']);

			foreach ($this->form_params['data'] as $dataset_id => $dataset_value)
			{
				reset ($all_fields);

				foreach ($all_fields as $row)
				{
					if ($row['admin_forms_field_allow_order'] &&
					$row['admin_forms_field_id'] == $this->order_field_id)
					{
						if ($this->order_field_direction == 1)
						{
							$order_direct_str = ' ASC ';
						}
						else
						{
							$order_direct_str = ' DESC ';
						}

						// Преобразуем псевдонимы полей в реальные названия, для этого
						// в AJAX загрузчике пользователем должна быть реализована
						// функция ResolveFieldName, которая принимает название псевдонима поля (или реальное название)
						// и индекс набора данных, а возвращает реальное название поля по ссылке
						// первым параметром.
						if (function_exists("ResolveFieldName"))
						{
							/**
							* $field_name - название поля (передается по-ссылке)
							* $dataset_id - индекс набора данных
							*/
							ResolveFieldName($row['admin_forms_field_name'], $dataset_id);
						}

						if ($row['admin_forms_field_name'] != '')
						{
							$order_array[$dataset_id] = " ORDER BY {$row['admin_forms_field_name']} {$order_direct_str}";

							// Поле для сортировки многомерного массива (если источник - массив)
							$order_array_for_array[$dataset_id] = $row['admin_forms_field_name'];

							// Подставляем направление сортировки, если оно явно было указано. Используется при указании
							// двойных сортировок через ResolveFieldName, например "field 1 {ORDER_DIRECTION}, field2"
							// при этом для последнего поля сортировка не указывается, т.к. она указана выше
							$order_array[$dataset_id] = str_replace('{ORDER_DIRECTION}', $order_direct_str, $order_array[$dataset_id]);
						}

						break;
					}
				}
			}
		}

		/*
		// Если поле и направление сортировки не заданы, пробуем установить значения
		// по умолчанию для формы.
		if (!$this->order_field_id)
		{
		if ($form_row['admin_forms_default_order_direction'] == 1)
		{
		$order_direct_str = ' ASC ';
		}
		else
		{
		$order_direct_str = ' DESC ';
		}

		if (!empty($form_row['admin_forms_default_order_field']))
		{
		//$order_array = ' ORDER BY '.$form_row['admin_forms_default_order_field'].' '.$order_direct_str;
		}
		}
		*/

		// Если поле и направление сортировки не заданы и по умолчанию, сортируем по ключевому полю.
		if (empty($order_array))
		{
			if (empty($form_row['admin_forms_key_field']))
			{
				// Ошибка! Для формы обязательно должно быть указано ключевое поле.
				return array(
				'error' => '<font color="Red">Ошибка! Не указано ключевое поле для формы.</font>',
				'form_html' => ' ');
			}

			// $order_array = ' ORDER BY '.$form_row['admin_forms_key_field'].' ASC ';
		}

		// Сохраняем настройки формы.
		$this->InsertSettings($form_settings);

		// Преобразуем общее количество.
		if (isset($this->form_params['total_count']) && $this->form_params['total_count'])
		{
			$count_array = array();

			reset($this->form_params['total_count']);

			foreach ($this->form_params['total_count'] as $key => $value)
			{
				if (is_string($value))
				{
					$count = 0;

					// Значение является запросом, выполняем.
					// Определяем фильтр.

					$value = str_replace('{FILTER_STRING}',
					Core_Type_Conversion::toStr($this->form_params['filter'][$key]), $value);

					if ($DataBase->select($value))
					{
						if ($DataBase->get_count_row() == 1)
						{
							if ($row = mysql_fetch_assoc($DataBase->result))
							{
								//echo "<pre>========= $value ============</pre>";
								$count = Core_Type_Conversion::toInt($row['count']);
							}
						}
					}
				}
				else
				{
					$count = Core_Type_Conversion::toInt($value);
				}

				// Добавим преобразованное значение.
				$count_array[$key] = $count;
			}

			// Подменяем массив с общим количеством на преобразованный.
			$this->form_params['total_count'] = $count_array;
		}
		/*elseif (isset($this->form_params['data']))
		{
		foreach ($this->form_params['data'] as $key => $value)
		{
		var_dump($value);
		}
		}*/

		// При экспорте в CSV лимиты недействительны
		if ($this->export_csv)
		{
			if (isset($this->form_params['limit']))
			{
				unset($this->form_params['limit']);
			}
		}

		// Преобразуем запросы в данные, путем выполнения этих запросов.
		$new_data = array();

		if (isset($this->form_params['data']))
		{
			reset($this->form_params['data']);

			foreach ($this->form_params['data'] as $key => $value)
			{
				// Корректируем лимиты, если они указаны общие для N источников
				if (isset($this->form_params['limit']['all']))
				{
					// Сумируем общее количество элементов из разных источников
					// и проверяем, меньше ли они $this->form_params['limit']['all']['begin']
					// если меньше - то расчитываем корректный begin
					if ($key == 0)
					{
						$iTotalCount = 0;

						foreach ($this->form_params['total_count'] as $iCount)
						{
							$iTotalCount += $iCount;
						}

						if ($iTotalCount < $this->form_params['limit']['all']['begin'])
						{
							$begin = floor($iTotalCount / intval($this->form_params['limit']['all']['count'])) * intval($this->form_params['limit']['all']['count']);

							if ($begin < 0)
							{
								$begin = 0;
							}

							$this->form_params['limit']['all']['begin'] = $begin;
						}
					}

					if ($this->form_params['total_count'][$key] > $this->form_params['limit']['all']['begin'])
					{
						$this->form_params['limit'][$key]['begin'] = $this->form_params['limit']['all']['begin'];
						$this->form_params['limit'][$key]['count'] = $this->form_params['limit']['all']['count'];
					}
					//elseif ($this->form_params['limit']['all']['begin'] == 0)
					else
					{
						$this->form_params['limit'][$key]['begin'] = 0;
						$this->form_params['limit'][$key]['count'] = 0;
					}
					/*else
					{
						$this->form_params['limit'][$key]['begin'] = 0;
						$this->form_params['limit'][$key]['count'] = $this->form_params['limit']['all']['count'];
					}*/

					// Предыдущие можем смотреть только для 1-го источника и следующих
					if ($key > 0)
					{
						//	echo "=".$this->form_params['total_count'][$key - 1];
						//	echo "=".$this->form_params['limit']['all']['begin'];

						// Если число элементов предыдущего источника меньше текущего начала
						if ($this->form_params['total_count'][$key - 1]
						- $this->form_params['limit']['all']['begin']
						< $this->form_params['limit']['all']['count'])
						{
							$begin = $this->form_params['limit']['all']['begin'] - $this->form_params['total_count'][$key - 1];

							if ($begin < 0)
							{
								$begin = 0;
							}

							$this->form_params['limit'][$key]['begin'] = $begin;
							$this->form_params['limit'][$key]['count'] = $this->form_params['limit']['all']['count']
							- ($this->form_params['total_count'][$key - 1] - $this->form_params['limit']['all']['begin'])
							- $begin;
						}
						else
						{
							$this->form_params['limit'][$key]['begin'] = 0;
							$this->form_params['limit'][$key]['count'] = 0;
						}
					}
				}

				// Корректируем лимиты, если они указаны для данного источника
				if (isset($this->form_params['limit'][$key])
				&& isset($this->form_params['limit'][$key]['begin'])
				&& isset($this->form_params['limit'][$key]['count']))
				{
					// Проверим, не превышают ли лимиты реального количества элементов,
					// и если превышают - установим на последние из них.
					if (isset($this->form_params['total_count'][$key]))
					{
						//var_dump($this->form_params['limit'][$key]);
						//var_dump($this->form_params['total_count'][$key]);

						// Если начало лимита больше или равно общему числа элементов в данном источнике
						if ($this->form_params['limit'][$key]['begin'] >= $this->form_params['total_count'][$key]
						&& $this->form_params['limit'][$key]['begin'] != 0
						/*&& $this->form_params['total_count'][$key] != 0*/)
						{
							// Определим начало лимита для такого числа count
							// по формуле intval((ВСЕГО ЭЛЕМЕНТОВ - ЭЛЕМЕНТОВ НА СТРАНИЦУ) / ЭЛЕМЕНТОВ НА СТРАНИЦУ)* ЭЛЕМЕНТОВ НА СТРАНИЦУ;
							//echo $this->form_params['total_count'][$key];

							if ($this->form_params['total_count'][$key] != 0)
							{
								$tmp_page_number = ($this->form_params['total_count'][$key]
								- $this->form_params['limit'][$key]['count'])
								/ $this->form_params['limit'][$key]['count'];

								// Если число страниц дробное - прибавляем 1
								if (!is_int($tmp_page_number) && $tmp_page_number > 0)
								{
									$tmp_page_number = intval($tmp_page_number) + 1;
								}
								elseif ($tmp_page_number < 0)
								{
									$tmp_page_number = 0;
								}

								$this->form_params['limit'][$key]['begin'] =
								$tmp_page_number* $this->form_params['limit'][$key]['count'];
							}
							else
							{
								$this->form_params['limit'][$key]['begin'] = 0;
								$_REQUEST['limit'] = 0;
							}

							// echo $this->form_params['limit'][$key]['begin'];
						}
					}
				}

				// Если вместо данных - запрос... выполняем и подменяем на данные.
				if (!is_array($value))
				{
					// Указываем сортировку для запроса.

					// если смогли определить поле и направление сортировки - заменяем на него
					if (isset($order_array[$key]))
					{
						$value = str_replace('{ORDER_STRING}', $order_array[$key], $value);
					}
					else // Иначе на пустоту
					{
						$value = str_replace('{ORDER_STRING}', 'ORDER BY 1', $value);
					}

					// Определяем структуру фильтра для каждого из запросов в отдельности
					//$current_filter_string = Core_Type_Conversion::toStr($this->form_params['filter'][$key]);

					$new_filter_string = Core_Type_Conversion::toStr($this->form_params['filter'][$key]);

					// Определяем фильтр.
					$value = str_replace('{FILTER_STRING}', $new_filter_string, $value);

					// Применем лимиты, если они указаны для данного источника (корректировка производится выше)
					if (isset($this->form_params['limit'][$key])
					&& isset($this->form_params['limit'][$key]['begin'])
					&& isset($this->form_params['limit'][$key]['count']))
					{
						$limit_sql = ' LIMIT ' . intval($this->form_params['limit'][$key]['begin']) . ', '. intval($this->form_params['limit'][$key]['count']);
						$value = str_replace('{LIMIT}', $limit_sql, $value);
					}
					else
					{
						$value = str_replace('{LIMIT}', '', $value);
					}

					// выводим запрос для отладки
					/*
					?>
					<pre><?php echo $value?></pre>
					<?php
					*/
					// Внедряем в запрос фильтр.
					$DataBase->select($value);

					if ($DataBase->result)
					{
						while ($row = mysql_fetch_assoc($DataBase->result))
						{
							$new_data[$key][] = $row;
						}
					}
				}
				else
				{
					// Есть поле сортировки для массива
					if (isset($order_array_for_array[$key]))
					{
						if ($this->order_field_direction == 1)
						{
							$type_order_array = SORT_ASC;
						}
						else
						{
							$type_order_array = SORT_DESC;
						}

						// Сортируем массив
						$kernel = & singleton('kernel');
						$value = $kernel->my_array_multisort($value, $order_array_for_array[$key], $type_order_array);
					}

					// Применем лимиты, если они указаны для данного источника (корректировка производится выше)
					if (isset($this->form_params['limit'][$key])
					&& isset($this->form_params['limit'][$key]['begin'])
					&& isset($this->form_params['limit'][$key]['count'])
					&& $this->form_params['limit'][$key]['count'] > 0
					// Усечение массива делаем только при превышении количества элементов
					&& count($value) > intval($this->form_params['limit'][$key]['count'])
					// если count == 0, то массив очищается
					&& intval($this->form_params['limit'][$key]['count']) != 0)
					{
						// Получаем срез с begin размером count
						$new_data[$key] = array_slice($value, $this->form_params['limit'][$key]['begin'], $this->form_params['limit'][$key]['count']);
					}
					elseif (isset($this->form_params['limit'][$key]['count'])
					&& intval($this->form_params['limit'][$key]['count']) == 0)
					{
						// Если count = 0, то очищаем массив данных для данного источника
						$new_data[$key] = array();
					}
					else
					{
						// Просто копируем данные
						$new_data[$key] = $value;
					}
				}
			}
		}

		$this->debug .= ob_get_clean();

		// Подменяем данные.
		$this->form_params['data'] = $new_data;

		unset($new_data); // Освобождаем память.

		if ($this->export_csv)
		{
			// Начинаем отправлять заголовки
			header("Pragma: public");
			header("Content-Description: File Transfer");
			header("Content-Type: application/force-download");
			// Force the download
			header("Content-Disposition: attachment; filename = " . 'Export_' .date("Y_m_d_H_i_s").'.csv'. ";");
			header("Content-Transfer-Encoding: binary");

			// Указана явно кодировка экспорта в CSV
			$export_csv_encoding = defined('EXPORT_CSV_ENCODING');
			$export_csv_encoding && ob_start();

			$all_fields = $this->GetAllAdminFormFields($admin_forms_id);
			if (!$all_fields)
			{
				// Ошибка - Не найдены поля формы в БД
				return false;
			}

			// Заголовки.
			reset($all_fields);

			foreach ($all_fields as $row)
			{
				if ($row['admin_forms_field_allow_filter'])
				{
					// Устанавливаем в true переменную, если был хотя бы один фильтр
					$allow_filter = true;
				}

				$word_row = $this->GetAdminFormsWordOrDefaultLanguageWord($row['admin_words_id']);

				// Слово найдено
				if ($word_row)
				{
					$field_name = htmlspecialchars($word_row['name']);
				}
				else
				{
					$field_name = "-";
				}

				echo '"' . trim(str_replace(array('"'), array('""'), $field_name)) . '";';
			}

			// Строка
			echo "\r\n";

			// Сбрасываем foreach на 0.
			reset($this->form_params);
			reset($all_fields);

			$position = 0;

			foreach ($this->form_params['data'] as $dataset_key => $dataset)
			{
				// Добавляем внешнюю замену по датасету
				$this->AddExternalReplace('{dataset_key}', $dataset_key);

				if ($dataset)
				{
					foreach ($dataset as $row)
					{
						// Цикл по столбцам.
						foreach ($all_fields as $field_value)
						{
							// Проверяем, установлено ли пользователем перекрытие параметров
							// для данного поля.
							if (isset($this->form_params['field_params'][$dataset_key][$field_value['admin_forms_field_name']]))
							{
								// Пользователь перекрыл параметры для данного поля.
								$field_value = array_merge($field_value, $this->form_params['field_params'][$dataset_key][$field_value['admin_forms_field_name']]);
							}
							elseif (isset($this->form_params['field_params'][$dataset_key][$field_value['admin_forms_field_id']]))
							{
								// Проверка перекрытых параметров по идентификатору.
								$field_value = array_merge($field_value, $this->form_params['field_params'][$dataset_key][$field_value['admin_forms_field_id']]);
							}

							$value = htmlspecialchars(Core_Type_Conversion::toStr($row[$field_value['admin_forms_field_name']]));

							ob_start();

							// Отображения элементов полей, в зависимости от их типа.
							switch ($field_value['admin_forms_field_type'])
							{
								default: // Тип не определен.
								case 1: // Текст.
									if (mb_strlen($value) != 0)
									{
										echo strip_tags($this->ApplyFormat($value, $field_value['admin_forms_field_format']));
									}

								break;
								case 3: // Checkbox.
									echo Core_Type_Conversion::toInt($value) ? '1' : '0';
								break;
								case 5: // Дата-время.
									$value = $date_time->datetime_format($value);
									echo $this->ApplyFormat($value, $field_value['admin_forms_field_format']);

								break;
								case 6: // Дата.
											$value = $date_time->date_format($value);
									echo $this->ApplyFormat($value, $field_value['admin_forms_field_format']);

								break;
								case 9: // Текст "AS IS"
									if (mb_strlen($value) != 0)
									{
										echo html_entity_decode($value, ENT_COMPAT, 'UTF-8');
									}
								break;
								case 10: // Вычисляемое поле с помощью функции обратного вызова,
								// имя функции обратного вызова f($field_value, $value)
								// передается функции с именем, содержащимся в $field_value['callback_function']
									// Выполним функцию обратного вызова
									if (isset($field_value['callback_function']))
									{
										if (function_exists($field_value['callback_function']))
										{
											$field_value['callback_function']($field_value, $value, $row);
										}
										else
										{
											show_error_message("Функция {$field_value['callback_function']} не найдена");
										}
									}
									else
									{
										show_error_message("Функция обратного вызова не определена");
									}

								break;
							}

							// chr(0xA0) - преобразованный неразрывный пробел
							echo '"' . trim(str_replace(array('"', chr(0xA0), "\r", "\t"), array('""', ' ', '', ''), strip_tags(html_entity_decode(ob_get_clean(), ENT_COMPAT, 'UTF-8')))) . '";';
						}

						// Строка
						echo "\r\n";

						// Увеличиваем счетчик позиций
						$position++;
					}
				}
			}

			if ($export_csv_encoding)
			{
				echo @iconv("UTF-8", EXPORT_CSV_ENCODING . "//IGNORE//TRANSLIT", ob_get_clean());
			}
			exit();
		}

		//$JsHttpRequest = new JsHttpRequest('UTF-8');

		// Возвращаем массив с отрисованной формой
		return $this->CreateForm($admin_forms_id);
	}


	/**
	* Вызов пользовательских ф-ций (событий)
	*
	* @param int $admin_forms_id идентификатор формы центра управления
	*/
	function ExecuteUsersEvents($admin_forms_id, $operation)
	{

		$admin_forms_id = intval($admin_forms_id);

		$operation = strval($operation);

		$param = array();
		$param['admin_forms_id'] = $admin_forms_id;

		$get_array = $_GET;

		// Результат вызова пользовательской функции
		$return = false;

		// Все поля формы.
		// $all_fields = $this->GetAllAdminFormFields($admin_forms_id);

		// в начало массива
		reset($get_array);

		// Пробигаемся по полученным данным из $_GET
		// Добавляем массив чекбоксов

		// Оьъединять в один foreach нельзя, т.к. чекбоксы иногда идут позже, чем поля
		foreach ($get_array as $get_key => $get_value)
		{
			// Проверяем, выбран ли чекбокс.
			//	echo "<br>get_key = $get_key, get_value = $get_value";

			// Записываем в массив список выбранных чекбоксов, для которых осуществлено действие
			if (!preg_match("/_fv_(\d*)$/u", $get_key, $get_matches) // Проверяем, чтобы не заканчивалось на _fv_, иначе будет двойное срабатывание
			&& preg_match("/check_(\d*)_([a-zA-Zа-яА-Я0-9.-_]*)$/u", $get_key, $get_matches)) // основной рабочий
			//if (preg_match("/check_(.*)_(.*)$/u", $get_key, $get_matches))
			{
				// Записываем пустой массив в массив по схеме ['data'][источник][ID_поля]
				$param['data'][$get_matches[1]][$get_matches[2]] = array();
			}
		}

		// в начало массива
		reset($get_array);

		// Пробигаемся по полученным данным из $_GET, добавляем значения полей.
		foreach ($get_array as $get_key => $get_value)
		{
			// Список значений.
			if (preg_match("/check_(\d*)_([a-zA-Zа-яА-Я0-9.-_]*)_fv_(\d*)/u", $get_key, $get_matches)) // оснвоной рабочий
			//if (preg_match("/check_(.*?)_(.*?)_fv_(.*?)/u", $get_key, $get_matches)) // не нужен, т.к. ниже добавили првоерку на выборку чекбокса
			{
				//echo "<b>OK3</b>";

				// если есть значение и чекбокс был выбран
				if (isset($get_matches[3]) && isset($param['data'][$get_matches[1]][$get_matches[2]]))
				{
					//echo "<b>OK2</b>";
					$field_row = $this->GetAdminFormsField($get_matches[3]);
					if ($field_row)
					{
						$param['data'][$get_matches[1]][$get_matches[2]][$field_row['admin_forms_field_name']] = $get_value;
					}
				}
			}

		}

		$user_access = & singleton('user_access');
		$user_row = $user_access->GetUserByName($_SESSION['valid_user']);

		// Вызываем события, определенные пользователем для данной формы.
		//$this->user_function_message = '';
		$all_events = $this->GetAllEvents($admin_forms_id, Core_Type_Conversion::toInt($user_row['users_id']));

		if ($all_events)
		{
			// Получаем данные о пользователе
			foreach ($all_events as $row)
			{
				// Проверяем права доступа пользователя к действию, он должен быть суперюзером или иметь права на доступ к событию
				if ($user_row['users_superuser'] == 1
				|| $user_access->IssetUserEventAccess(array('user_id' => Core_Type_Conversion::toInt($user_row['users_id']),
				'admin_form_event_id' => $row['admin_forms_events_id'])))
				{
					$function = $row['admin_forms_events_function'];

					if ($operation == $function && function_exists($function))
					{
						// Урезаем набор данных, если установлено ограничение
						// в случае, если действие связано только с одним набором данных
						if ($row['admin_forms_events_dataset_id'] != -1)
						{
							if (isset($param['data']) && is_array($param['data']))
							{
								reset($param['data']);
								foreach ($param['data'] as $key => $value)
								{
									if ($key != $row['admin_forms_events_dataset_id'])
									{
										unset($param['data'][$key]);
									}
								}
							}
						}

						// Вызов пользовательской ф-ции.
						// Если функция вернет истину - дальше не нужно выводить данныне,
						// выводим только результат работы функции
						$return = $function($param);

						// Добавляем к общей строке ошибок, ошибки, произошедшие в пользовательском
						// обработчике.
						//$this->user_function_message .= Core_Type_Conversion::toStr($param['error']);
						$this->AddUserMessage(Core_Type_Conversion::toStr($param['error']));

						// Результат работы пользовательской функции
						$this->user_function_result = Core_Type_Conversion::toStr($param['result']);

						if (isset($param['title']))
						{
							$this->form_params['title'] = $param['title'];
						}

						$redirect = trim(Core_Type_Conversion::toStr($param['redirect']));

						// Если был указан редирект, выполняем.
						if (!empty($redirect))
						{
							// Ajax результат.
							$JsHttpRequest = new JsHttpRequest('UTF-8');

							$GLOBALS['_RESULT'] = array(
							'error' => '', // Редирект...
							'form_html' => ' ',
							'redirect' => $redirect);

							echo $JsHttpRequest->LOADER;
							exit();
						}
					}
				}
				else
				{
					show_error_message(Core::_('admin_form.msg_error_access'));
				}
			}
		}
		// У Пользователя нет доступа вообще ни к каким действиям, но действие было передано
		elseif (!empty($operation) && $operation != 'load_data')
		{
			show_error_message(Core::_('admin_form.msg_error_access'));
		}

		return $return;
	}

	/**
	* Прорисовка формы центра администрирования.
	*
	* @param int $admin_forms_id идентификатор формы
	* @return array данные о форме в массиве:
	* <br/>array(
	* <br/>'form_html' => Основной код,
	* <br/>'error' => Текст ошибки,
	* <br/>'title' => Заголовок страницы);
	*/
	function CreateForm($admin_forms_id)
	{
		$date_time = new DateClass();

		$form_row = $this->GetAdminForm($admin_forms_id);

		if (!$form_row)
		{
			// Ошибка - Не найдена форма в БД
			return array(
			'error' => '<font color="Red">Не найдена форма '.$admin_forms_id.' в БД.</font>',
			'form_html' => ''
			);
		}

		$all_fields = $this->GetAllAdminFormFields($admin_forms_id);
		if (!$all_fields)
		{
			// Ошибка - Не найдены поля формы в БД
			return array(
			'error' => '<font color="Red">Не найдены поля формы '.$admin_forms_id.' в БД.</font>',
			'form_html' => ''
			);
		}

		$user_access = & singleton('user_access');
		$user_row = $user_access->GetUserByName($_SESSION['valid_user']);

		// Извлекаем все события (будут использоваться далее).
		$all_events = $this->GetAllEvents($admin_forms_id, Core_Type_Conversion::toInt($user_row['users_id']));

		// Формируем select для выбора on_page.

		$on_page = Core_Type_Conversion::toInt($this->form_params['on_page']);

		// 6.0
		$window_id = $this->GetWindowId();

		ob_start();
		?><select name="admin_forms_on_page" id="id_on_page" onchange="DoLoadAjax('<?php echo $this->AAction?>', '<?php echo $this->AAdditionalParams?>', <?php echo $admin_forms_id?>, 'load_data', 0, this.options[this.selectedIndex].value, <?php echo $this->order_field_id?>, <?php echo $this->order_field_direction?>, '<?php echo $window_id?>')">
			<option value="10" <?php echo $on_page == 10 ? "selected" : ""?>>10</option>
			<option value="20" <?php echo $on_page == 20 ? "selected" : ""?>>20</option>
			<option value="30" <?php echo $on_page == 30 ? "selected" : ""?>>30</option>
			<option value="40" <?php echo $on_page == 40 ? "selected" : ""?>>40</option>
			<option value="50" <?php echo $on_page == 50 ? "selected" : ""?>>50</option>
			<option value="100" <?php echo $on_page == 100 ? "selected" : ""?>>100</option>
		</select><?php
		$select_on_page_html = ob_get_clean();

		// Просуммируем общее число.
		$total_count = 0;

		if (isset($this->form_params['total_count']) && $this->form_params['total_count'])
		{
			reset($this->form_params['total_count']);
			foreach ($this->form_params['total_count'] as $value)
			{
				$total_count = $total_count + Core_Type_Conversion::toInt($value);
			}
		}

		// Ссылки 1, 2, 3...
		$param = array('admin_forms_id' => $admin_forms_id);
		$links_1_2_3 = $this->ShowLink($total_count, $this->form_params['on_page'], Core_Type_Conversion::toInt($this->form_params['current_page']), $param);

		ob_start();

		// 6.0
		?><div id="id_message"><?php echo $this->user_function_message?></div><?php

		// Заголовок формы
		if (isset($this->form_params['title']) && mb_strlen($this->form_params['title']) > 0)
		{
			?>
			<h1><?php echo htmlspecialchars($this->form_params['title'])?></h1>
			<?php
		}

		if (!empty($this->debug))
		{
			echo "<p>".$this->debug."</p>";
		}

		// Прорисовка верхнего меню.
		if (isset($this->form_params['menus']))
		{
			if (is_array($this->form_params['menus']))
			{
				reset($this->form_params['menus']);
				foreach ($this->form_params['menus'] as $menu)
				{
					$this->CreateMainMenu($menu);
				}
			}
		}

		// Отображение хлебных крошек.
		$this->ShowBreadCrumbs();

		// Данные, выводимые под меню
		if (!empty($this->data_under_menu))
		{
			echo $this->data_under_menu;
		}

		// Переменная устанавливается в true, если хотя бы у одного поля был разрешен фильтр
		$allow_filter = false;

		?>
		<table width="100%" cellpadding="2" cellspacing="2" class="admin_table">
		<tr class="admin_table_title">
			<?php
			// Ячейку над групповыми чекбоксами показываем только при наличии действий
			if ($form_row['admin_forms_show_operations'] && $this->ShowOperations)
			{
				?><td width="25">&nbsp;</td><?php
			}

			// Заголовки.
			reset($all_fields);

			foreach ($all_fields as $all_fields_key => $row)
			{
				// Проверяем, установлено ли пользователем перекрытие параметров для фильтра.
				if (isset($this->form_params['field_params']['filter'][$row['admin_forms_field_name']]))
				{
					// Пользователь перекрыл параметры для данного поля.
					$row = array_merge($row, $this->form_params['field_params']['filter'][$row['admin_forms_field_name']]);
				}
				elseif (isset($this->form_params['field_params']['filter'][$row['admin_forms_field_id']]))
				{
					// Проверка перекрытых параметров по идентификатору.
					$row = array_merge($row, $this->form_params['field_params']['filter'][$row['admin_forms_field_id']]);
				}

				// Сохраняем данные с учетом перекрытия
				$all_fields[$all_fields_key] = $row;

				if ($row['admin_forms_field_allow_filter'])
				{
					// Устанавливаем в true переменную, если был хотя бы один фильтр
					$allow_filter = true;
				}

				if (!empty($row['admin_forms_field_align']))
				{
					$align = ' align="'.htmlspecialchars($row['admin_forms_field_align_title']).'"';
				}
				else
				{
					$align = '';
				}

				$width = htmlspecialchars(trim($row['admin_forms_field_width']));

				if (!empty($width))
				{
					$width = 'width="'.$width.'"';
				}

				$word_row = $this->GetAdminFormsWordOrDefaultLanguageWord($row['admin_words_id']);

				// Слово найдено
				if ($word_row)
				{
					$field_name = htmlspecialchars($word_row['name']);
				}
				// Слово для этого языка не найдено
				else
				{
					$field_name = "&mdash;";
				}

				// Определяем нужно ли отображать стрелки сортировки
				ob_start();

				// Не подсвечивать
				$highlight = false;

				if ($row['admin_forms_field_allow_order'])
				{
					if ($row['admin_forms_field_id'] == $this->order_field_id)
					{
						// Подсвечивать
						$highlight = true;

						if ($this->order_field_direction == 1)
						{
							?><img src="/admin/images/arrow_up.gif" alt="&uarr" />
							<a href="<?php echo $this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 0, 0, $row['admin_forms_field_id'], 2)?>"
							onclick="<?php echo $this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 0, 0, $row['admin_forms_field_id'], 2)?>"
							><img src="/admin/images/arrow_down_gray.gif" alt="&darr" /></a>
							<?php
						}
						else
						{
							?><a href="<?php echo $this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 0, 0, $row['admin_forms_field_id'], 1)?>"
							onclick="<?php echo $this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 0, 0, $row['admin_forms_field_id'], 1)?>"
							><img src="/admin/images/arrow_up_gray.gif" alt="&uarr" /></a>
							<img src="/admin/images/arrow_down.gif" alt="&darr" />
							<?php
						}
					}
					else
					{
						?><a href="<?php echo $this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 0, 0, $row['admin_forms_field_id'], 1)?>"
						onclick="<?php echo $this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 0, 0, $row['admin_forms_field_id'], 1)?>"
						><img src="/admin/images/arrow_up_gray.gif" alt="&uarr" /></a>
						<a href="<?php echo $this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 0, 0, $row['admin_forms_field_id'], 2)?>"
						onclick="<?php echo $this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 0, 0, $row['admin_forms_field_id'], 2)?>"
						><img src="/admin/images/arrow_down_gray.gif" alt="&darr" /></a>
						<?php
					}
				}

				$sort_arrows = ob_get_clean();

				if (mb_strlen($field_name) == 0)
				{
					$field_name = '&nbsp;';
				}

				// Стиль подсвечивания
				if ($highlight)
				{
					$highlight_style = ' class="hl"';
				}
				else
				{
					$highlight_style = '';
				}

				?>
				<td <?php echo $width?><?php echo $align?><?php echo $highlight_style?>>
					<nobr><?php echo $field_name?> <?php echo $sort_arrows?></nobr>
				</td>
				<?php
			}
			if ($form_row['admin_forms_show_operations'] && $this->ShowOperations
			|| $allow_filter && $this->ShowFilter)
			{
				// -------------------------------
				if (isset($this->form_params['actions_width']))
				{
					$width = Core_Type_Conversion::toStr($this->form_params['actions_width']);
				}
				else
				{
					// min width action column
					$width = 10;

					if ($all_events)
					{
						foreach ($all_events as $event_row)
						{
							// Отображаем действие, только если разрешено.
							if (!$event_row['admin_forms_events_show_button'])
							{
								continue;
							}

							$width += 16;
						}
					}
				}

				// -------------------------------

				?><td width="<?php echo $width?>">&nbsp;</td><?php
			}
			?>
		</tr>
		<tr class="admin_table_filter"><?php

			$window_id = $this->GetWindowId();
			// Чекбокс "Выбрать все" показываем только при наличии действий
			if ($form_row['admin_forms_show_operations'] && $this->ShowOperations)
			{
				?><td align="center" width="25"><input type="checkbox" name="admin_forms_all_check" id="id_admin_forms_all_check" onclick="$('#<?php echo $window_id?>').highlightAllRows(this.checked)" /></td><?php
			}

			// Фильтр.
			reset($all_fields);

			foreach ($all_fields as $all_fields_key => $row)
			{
				$width = htmlspecialchars(trim($row['admin_forms_field_width']));

				if (!empty($width))
				{
					$width_str = 'width="'.$width.'"';
				}
				else
				{
					$width_str = "";
				}

				// Не подсвечивать
				$highlight = false;

				if ($row['admin_forms_field_allow_order'])
				{
					if ($row['admin_forms_field_id'] == $this->order_field_id)
					{
						// Подсвечивать
						$highlight = true;
					}
				}

				// Стиль подсвечивания
				if ($highlight)
				{
					$highlight_style = ' class="hl"';
				}
				else
				{
					$highlight_style = '';
				}

				?><td <?php echo $width_str?><?php echo $highlight_style?>><?php

					if ($row['admin_forms_field_allow_filter'])
					{
						switch ($row['admin_forms_field_type'])
						{
							case 1: // Строка
							case 2: // Поле ввода
							case 4: // Ссылка
							{
								if (isset($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]))
								{
									$value = htmlspecialchars(Core_Type_Conversion::toStr($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]));
								}
								else
								{
									$value = htmlspecialchars(Core_Type_Conversion::toStr($this->form_params['filter_values'][$row['admin_forms_field_id']]['value']));
								}

								if (!empty($width))
								{
									$style_input = "width: {$width};";
								}
								else
								{
									$style_input = "width: 97%;";
								}

								?><input type="text" name="admin_form_filter_<?php echo $row['admin_forms_field_id']?>" id="id_admin_form_filter_<?php echo $row['admin_forms_field_id']?>" value="<?php echo $value?>" style="<?php echo $style_input?>" /><?php
								break;
							}
							case 3: // Checkbox.
							{
								if (!empty($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]))
								{
									$selected = Core_Type_Conversion::toInt($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]);
								}
								else
								{
									$selected = htmlspecialchars(Core_Type_Conversion::toStr($this->form_params['filter_values'][$row['admin_forms_field_id']]['value']));
								}

								?><select name="admin_form_filter_<?php echo $row['admin_forms_field_id']?>" id="id_admin_form_filter_<?php echo $row['admin_forms_field_id']?>" >
									<option value="0" <?php echo $selected == 0 ? "selected" : ''?>><?php echo Core::_('admin_form.filter_selected_all')?></option>
									<option value="1" <?php echo $selected == 1 ? "selected" : ''?>><?php echo Core::_('admin_form.filter_selected')?></option>
									<option value="2" <?php echo $selected == 2 ? "selected" : ''?>><?php echo Core::_('admin_form.filter_not_selected')?></option>
								</select><?php
								break;
							}
							case 5: // Дата-время.
							{
								if (!empty($_GET["admin_form_filter_from_{$row['admin_forms_field_id']}"]))
								{
									$date_from = htmlspecialchars(Core_Type_Conversion::toStr($_GET["admin_form_filter_from_{$row['admin_forms_field_id']}"]));
								}
								else
								{
									$date_from = htmlspecialchars(Core_Type_Conversion::toStr($this->form_params['filter_values'][$row['admin_forms_field_id']]['date_from']));
								}

								if (!empty($_GET["admin_form_filter_to_{$row['admin_forms_field_id']}"]))
								{
									$date_to = htmlspecialchars(Core_Type_Conversion::toStr($_GET["admin_form_filter_to_{$row['admin_forms_field_id']}"]));
								}
								else
								{
									$date_to = htmlspecialchars(Core_Type_Conversion::toStr($this->form_params['filter_values'][$row['admin_forms_field_id']]['date_to']));
								}

								?><input type="text" name="admin_form_filter_from_<?php echo $row['admin_forms_field_id']?>" id="id_admin_form_filter_from_<?php echo $row['admin_forms_field_id']?>" value="<?php echo $date_from?>" size="17" class="calendar_field" />
								<script type="text/javascript">
								Calendar.setup({inputField: 'id_admin_form_filter_from_<?php echo $row['admin_forms_field_id']?>',
								ifFormat: '%d.%m.%Y %H:%M:00',
								showsTime: true,
								button: 'id_admin_form_filter_from_<?php echo $row['admin_forms_field_id']?>',
								align: 'Br',
								singleClick: true,
								timeFormat: 24,
								firstDay: 1});
								</script>
								<div><input type="text" name="admin_form_filter_to_<?php echo $row['admin_forms_field_id']?>" id="id_admin_form_filter_to_<?php echo $row['admin_forms_field_id']?>" value="<?php echo $date_to?>" size="17" class="calendar_field" /></div>
								<script type="text/javascript">
								Calendar.setup({inputField: 'id_admin_form_filter_to_<?php echo $row['admin_forms_field_id']?>',
								ifFormat: '%d.%m.%Y %H:%M:00',
								showsTime: true,
								button: 'id_admin_form_filter_to_<?php echo $row['admin_forms_field_id']?>',
								align: 'Br',
								singleClick: true,
								timeFormat: 24,
								firstDay: 1});
								</script><?php
								break;
							}
							case 6: // Дата.
							{
								if (!empty($_GET["admin_form_filter_from_{$row['admin_forms_field_id']}"]))
								{
									$date_from = htmlspecialchars(Core_Type_Conversion::toStr($_GET["admin_form_filter_from_{$row['admin_forms_field_id']}"]));
								}
								else
								{
									$date_from = htmlspecialchars(Core_Type_Conversion::toStr($this->form_params['filter_values'][$row['admin_forms_field_id']]['date_from']));
								}

								if (!empty($_GET["admin_form_filter_to_{$row['admin_forms_field_id']}"]))
								{
									$date_to = htmlspecialchars(Core_Type_Conversion::toStr($_GET["admin_form_filter_to_{$row['admin_forms_field_id']}"]));
								}
								else
								{
									$date_to = htmlspecialchars(Core_Type_Conversion::toStr($this->form_params['filter_values'][$row['admin_forms_field_id']]['date_to']));
								}

								?><input type="text" name="admin_form_filter_from_<?php echo $row['admin_forms_field_id']?>" id="id_admin_form_filter_from_<?php echo $row['admin_forms_field_id']?>" value="<?php echo $date_from?>" size="8" class="calendar_field" />
								<script type="text/javascript">
								Calendar.setup({inputField: 'id_admin_form_filter_from_<?php echo $row['admin_forms_field_id']?>',
								ifFormat: '%d.%m.%Y',
								showsTime: false,
								button: 'id_admin_form_filter_from_<?php echo $row['admin_forms_field_id']?>',
								align: 'Br',
								singleClick: true,
								timeFormat: 24,
								firstDay: 1});
								</script>
								<div><input type="text" name="admin_form_filter_to_<?php echo $row['admin_forms_field_id']?>" id="id_admin_form_filter_to_<?php echo $row['admin_forms_field_id']?>" value="<?php echo $date_to?>" size="8" class="calendar_field" /></div>
								<script type="text/javascript">
								Calendar.setup({inputField: 'id_admin_form_filter_to_<?php echo $row['admin_forms_field_id']?>',
								ifFormat: '%d.%m.%Y',
								showsTime: true,
								button: 'id_admin_form_filter_to_<?php echo $row['admin_forms_field_id']?>',
								align: 'Br',
								singleClick: true,
								timeFormat: 24,
								firstDay: 1});
								</script>
								<?php
								break;
							}
							case 8: // Выпадающий список.
							{
								$field_value = $row;

								if (isset($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]))
								{
									$selected = Core_Type_Conversion::toStr($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]);
								}
								else
								{
									$selected = htmlspecialchars(Core_Type_Conversion::toStr($this->form_params['filter_values'][$row['admin_forms_field_id']]['value']));
								}
						?>
						<select name="admin_form_filter_<?php echo $row['admin_forms_field_id']?>"
						id="id_admin_form_filter_<?php echo $row['admin_forms_field_id']?>"
						>
							<option value="HOST_CMS_ALL" <?php echo $selected == 'HOST_CMS_ALL' ? "selected" : ''?>>Все</option>
							<?php
							$str_array = explode("\n", $field_value['admin_forms_field_list']);

							$value_array = array();

							foreach ($str_array as $str_value)
							{
								// Каждую строку разделяем по равно
								$str_explode = explode('=', $str_value);

								// сохраняем в массив варинаты значений и ссылки для них
								$value_array[intval(trim(Core_Type_Conversion::toStr($str_explode[0])))] = trim(Core_Type_Conversion::toStr($str_explode[1]));

								?><option value="<?php echo $str_explode[0]?>" <?php echo $selected == $str_explode[0] ? "selected" : ''?>><?php echo trim(Core_Type_Conversion::toStr($str_explode[1]))?></option><?php
							}
							?>
							</select>
							<?php
							break;
							}
							case 10:
								{
									$field_value = $row;

									// Выполним функцию обратного вызова
									if (isset($field_value['filter_callback_function']))
									{
										if (function_exists($field_value['filter_callback_function']))
										{
											$field_value['filter_callback_function']($field_value, $value, $row);
										}
										else
										{
											echo "Ошибка! Функция {$field_value['filter_callback_function']} не найдена!";
										}
									}
									else
									{
										if (isset($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]))
										{
											$value = htmlspecialchars(Core_Type_Conversion::toStr($_GET["admin_form_filter_{$row['admin_forms_field_id']}"]));
										}
										else
										{
											$value = htmlspecialchars(Core_Type_Conversion::toStr($this->form_params['filter_values'][$row['admin_forms_field_id']]['value']));
										}

										if (!empty($width))
										{
											$style_input = "width: {$width};";
										}
										else
										{
											$style_input = "width: 97%;";
										}

										?><input type="text" name="admin_form_filter_<?php echo $row['admin_forms_field_id']?>" id="id_admin_form_filter_<?php echo $row['admin_forms_field_id']?>" value="<?php echo $value?>" style="<?php echo $style_input?>" /><?php
									}
									break;
								}
							default: // Иначе.
							{
								?><div style="color: #CEC3A3; text-align: center">&mdash;</div><?php
								break;
							}
						}
					}
					else
					{
						// Фильтр не разрешен.
						?><div style="color: #CEC3A3; text-align: center">&mdash;</div><?php
					}
				?></td><?php
			}

			// Фильтр показываем если есть события или хотя бы у одного есть фильтр
			if ($form_row['admin_forms_show_operations'] && $this->ShowOperations
			|| $allow_filter && $this->ShowFilter)
			{
				?><td valign="middle">
					<input title="Фильтровать" type="image" src="/admin/images/filter.gif" style="font-size: 7pt; padding: 3px;" id="admin_forms_apply_button" type="button" value="<?php echo Core::_('admin_form.button_to_filter')?>"
				onclick="<?php echo $this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $this->page_number, $this->on_page, $this->order_field_id, $this->order_field_direction)?>" />
				</td><?php
			}

		?></tr><?php

		// Сбрасываем foreach на 0.
		reset($this->form_params);
		reset($all_fields);

		$position = 0;

		$window_id = $this->GetWindowId();

		foreach ($this->form_params['data'] as $dataset_key => $dataset)
		{
			// Добавляем внешнюю замену по датасету
			$this->AddExternalReplace('{dataset_key}', $dataset_key);

			if ($dataset)
			{
				foreach ($dataset as $row)
				{
					// Core_Type_Conversion::toInt нельзя, т.к. имя индексного поля может быть символьным
					$key_field_value = Core_Type_Conversion::toStr($row[Core_Type_Conversion::toStr($form_row['admin_forms_key_field'])]);

					// Экранируем ' в имени индексного поля, т.к. дальше это значение пойдет в JS
					$key_field_value = str_replace("'", "\'", $key_field_value);

					?><tr id="row_<?php echo $dataset_key?>_<?php echo $key_field_value?>">
						<?php
						// Чекбокс "Для элемента" показываем только при наличии действий
						if ($form_row['admin_forms_show_operations'] && $this->ShowOperations)
						{
							?><td align="center" width="25"><?php
								?><input type="checkbox" name="check_<?php echo $dataset_key?>_<?php echo $key_field_value?>" id="check_<?php echo $dataset_key?>_<?php echo $key_field_value?>" onclick="$('#<?php echo $window_id?>').setTopCheckbox(); $('#' + getWindowId('<?php echo $window_id?>') + ' #row_<?php echo $dataset_key?>_<?php echo $key_field_value?>').toggleHighlight()" /><?php
							?></td><?php
						}

						// Цикл по столбцам.
						foreach ($all_fields as $field_value)
						{
							// Проверяем, установлено ли пользователем перекрытие параметров
							// для данного поля.
							if (isset($this->form_params['field_params'][$dataset_key][$field_value['admin_forms_field_name']]))
							{
								// Пользователь перекрыл параметры для данного поля.
								$field_value = array_merge($field_value, $this->form_params['field_params'][$dataset_key][$field_value['admin_forms_field_name']]);
							}
							elseif (isset($this->form_params['field_params'][$dataset_key][$field_value['admin_forms_field_id']]))
							{
								// Проверка перекрытых параметров по идентификатору.
								$field_value = array_merge($field_value, $this->form_params['field_params'][$dataset_key][$field_value['admin_forms_field_id']]);
							}

							// Параметры поля.
							$width_value = htmlspecialchars(trim($field_value['admin_forms_field_width']));

							if (!empty($width_value))
							{
								$width = 'width="'.$width_value.'"';
							}
							else
							{
								$width = '';
							}

							$style = htmlspecialchars(trim($field_value['admin_forms_field_style']));

							if (!empty($style))
							{
								$style = 'style="'.$style.'"';
							}
							else
							{
								$style = '';
							}

							$align = htmlspecialchars(trim($field_value['admin_forms_field_align']));

							if (!empty($align))
							{
								$align = 'align="'.$align.'"';
							}

							$attrib = trim($field_value['admin_forms_field_attrib']);

							// Не подсвечивать
							$highlight = false;

							if ($field_value['admin_forms_field_allow_order'])
							{
								if ($field_value['admin_forms_field_id'] == $this->order_field_id)
								{
									// Подсвечивать
									$highlight = true;
								}
							}

							// Стиль подсвечивания
							if ($highlight)
							{
								$highlight_style = ' class="hl"';
							}
							else
							{
								$highlight_style = '';
							}

							?><td <?php echo $width?> <?php echo $style?> <?php echo $align?> <?php echo $attrib?><?php echo $highlight_style?>><?php

							$value = htmlspecialchars(Core_Type_Conversion::toStr($row[$field_value['admin_forms_field_name']]));

							$element_name = "check_{$dataset_key}_{$key_field_value}_fv_{$field_value['admin_forms_field_id']}";

							// Отображения элементов полей, в зависимости от их типа.
							switch ($field_value['admin_forms_field_type'])
							{
								case 1: // Текст.
								{
									if (mb_strlen(trim($value)) != 0)
									{
										?><div><div style="width: <?php echo $width_value?>" class="dl"><?php
										echo $this->ApplyFormat(nl2br($value), $field_value['admin_forms_field_format']);
										?></div></div><?php
										// <div class="dr">&nbsp;</div>
									}
									else
									{
										?>&nbsp;<?php
									}

									break;
								}
								case 2: // Поле ввода.
								{
									?><input type="text" name="<?php echo $element_name?>" id="id_<?php echo $element_name?>" value="<?php echo $value?>" <?php echo $style?> <?php echo ''/*$size*/?> onchange="DoModification('check_<?php echo $dataset_key?>_<?php echo $key_field_value?>'); $('#' + getWindowId('<?php echo $window_id?>') + ' #row_<?php echo $dataset_key?>_<?php echo $key_field_value?>').toggleHighlight();" onkeydown="DoModification('check_<?php echo $dataset_key?>_<?php echo $key_field_value?>'); $('#' + getWindowId('<?php echo $window_id?>') + ' #row_<?php echo $dataset_key?>_<?php echo $key_field_value?>').toggleHighlight()" /><?php
									break;
								}
								case 3: // Checkbox.
								{
									?><input type="checkbox" name="<?php echo $element_name?>" id="id_<?php echo $element_name?>" <?php echo Core_Type_Conversion::toInt($value) ? 'checked ' : ''?> onclick="DoModification('check_<?php echo $dataset_key?>_<?php echo $key_field_value?>'); $('#' + getWindowId('<?php echo $window_id?>') + ' #row_<?php echo $dataset_key?>_<?php echo $key_field_value?>').toggleHighlight()" /><?php
									break;
								}
								case 4: // Ссылка.
								{
									$link = $field_value['admin_forms_field_link'];
									$onclick = Core_Type_Conversion::toStr($field_value['admin_forms_field_onclick']);

									//$link_text = trim($value);
									$link_text = $this->ApplyFormat($value, $field_value['admin_forms_field_format']);

									$link = $this->DoReplaces(false, $row, $link);
									$onclick = $this->DoReplaces(false, $row, $onclick);

									// Нельзя применять, т.к. 0 является пустотой if (empty($link_text))
									if (mb_strlen($link_text) != 0)
									{
										?><a href="<?php echo $link?>" <?php echo (!empty($onclick)) ? "onclick=\"{$onclick}\"" : ''?>><?php echo $link_text?></a><?php
									}
									else
									{
										?>&nbsp;<?php
									}
									break;
								}
								case 5: // Дата-время.
								{
									$value = $date_time->datetime_format($value);
									echo $this->ApplyFormat($value, $field_value['admin_forms_field_format']);

									break;
								}
								case 6: // Дата.
								{
									$value = $date_time->date_format($value);
									echo $this->ApplyFormat($value, $field_value['admin_forms_field_format']);

									break;
								}
								case 7: // Картинка-ссылка.
								{
									$link = $field_value['admin_forms_field_link'];
									$onclick = Core_Type_Conversion::toStr($field_value['admin_forms_field_onclick']);

									$link = $this->DoReplaces(false, $row, $link);
									$onclick = $this->DoReplaces(false, $row, $onclick);

									// ALT-ы к картинкам
									$alt_array = array();

									// TITLE-ы к картинкам
									$title_array = array();

									/*
									Разделяем варианты значений на строки, т.к. они приходят к нам в виде:
									0 = /images/off.gif
									1 = /images/on.gif
									*/

									$str_array = explode("\n", $field_value['admin_forms_field_image']);

									$value_array = array();

									foreach ($str_array as $str_value)
									{
										// Каждую строку разделяем по равно
										$str_explode = explode('=', $str_value/*, 2*/);

										// сохраняем в массив варинаты значений и ссылки для них
										$value_array[trim(Core_Type_Conversion::toStr($str_explode[0]))] = trim(Core_Type_Conversion::toStr($str_explode[1]));

										$value_trim = trim($value);

										// Если указано альтернативное значение для картинки - добавим его в alt и title
										if (isset($str_explode[2]) && $value_trim == trim($str_explode[0]))
										{
											$alt_array[$value_trim] = trim($str_explode[2]);
											$title_array[$value_trim] = trim($str_explode[2]);
										}
									}

									// Получаем заголовок столбца на случай, если для IMG не было указано alt-а или title
									$word_row = $this->GetAdminFormsWordOrDefaultLanguageWord($field_value['admin_words_id']);

									if ($word_row)
									{
										$field_name = htmlspecialchars($word_row['name']);
									}
									else
									{
										$field_name = "&mdash;";
									}

									if (isset($field_value['admin_forms_field_alt']))
									{
										$str_array_alt = explode("\n", $field_value['admin_forms_field_alt']);

										foreach ($str_array_alt as $str_value)
										{
											// Каждую строку разделяем по равно
											$str_explode_alt = explode('=', $str_value, 2);

											// сохраняем в массив варинаты значений и ссылки для них
											$alt_array[trim(Core_Type_Conversion::toStr($str_explode_alt[0]))] = trim(Core_Type_Conversion::toStr($str_explode_alt[1]));
										}
									}
									elseif (!isset($alt_array[$value]))
									{
										$alt_array[$value] = $field_name;
									}

									if (isset($field_value['admin_forms_field_title']))
									{
										$str_array_title = explode("\n", $field_value['admin_forms_field_title']);

										foreach ($str_array_title as $str_value)
										{
											// Каждую строку разделяем по равно
											$str_explode_title = explode('=', $str_value, 2);

											// сохраняем в массив варинаты значений и ссылки для них
											$title_array[trim(Core_Type_Conversion::toStr($str_explode_title[0]))] = trim(Core_Type_Conversion::toStr($str_explode_title[1]));
										}
									}
									elseif (!isset($title_array[$value]))
									{
										$title_array[$value] = $field_name;
									}

									if (!empty($link) && isset($value_array[$value]))
									{
										$src = $value_array[$value];

										// Отображаем картинку ссылкой.
										?><a href="<?php echo $link?>" onclick="$('#' + getWindowId('<?php echo $window_id?>') + ' #row_<?php echo $dataset_key?>_<?php echo $key_field_value?>').toggleHighlight();<?php echo $onclick?>"><img src="<?php echo htmlspecialchars($src)?>" alt="<?php echo Core_Type_Conversion::toStr($alt_array[$value])?>" title="<?php echo Core_Type_Conversion::toStr($title_array[$value])?>"></a><?php
									}
									elseif (isset($value_array[$value]) || isset($value_array['']))
									{
										if (isset($value_array[$value]))
										{
											$src = $value_array[$value];
										}
										else
										{
											$src = $value_array[''];
										}
										// Отображаем картинку без ссылки.
										?><img src="<?php echo htmlspecialchars($src)?>" alt="<?php echo Core_Type_Conversion::toStr($alt_array[$value])?>" title="<?php echo Core_Type_Conversion::toStr($title_array[$value])?>"><?php
									}
									elseif (!empty($link) && !isset($value_array[$value]))
									{
										// Картинки для такого значения не найдено, но есть ссылка
										?><a href="<?php echo $link?>" onclick="$('#' + getWindowId('<?php echo $window_id?>') + ' #row_<?php echo $dataset_key?>_<?php echo $key_field_value?>').toggleHighlight();<?php echo $onclick?> ">&mdash;</a><?php
									}
									else
									{
										// Картинки для такого значения не найдено
										echo "&mdash;";
									}

									break;
								}

								case 8: // Выпадающий список
								{
									/*
									Разделяем варианты значений на строки, т.к. они приходят к нам в виде:
									0 = /images/off.gif
									1 = /images/on.gif
									*/

									$str_array = explode("\n", $field_value['admin_forms_field_list']);

									$value_array = array();

									?><select name="<?php echo $element_name?>" id="id_<?php echo $element_name?>" onchange="DoModification('check_<?php echo $dataset_key?>_<?php echo $key_field_value?>');"><?php
									$list_value = Core_Type_Conversion::toInt($value);

									foreach ($str_array as $str_value)
									{
										// Каждую строку разделяем по равно
										$str_explode = explode('=', $str_value, 2);

										// сохраняем в массив варинаты значений и ссылки для них
										$value_array[intval(trim(Core_Type_Conversion::toStr($str_explode[0])))] = trim(Core_Type_Conversion::toStr($str_explode[1]));

										if ($list_value == $str_explode[0])
										{
											$selected = ' selected = "" ';
										}
										else
										{
											$selected = '';
										}
										?><option value="<?php echo $str_explode[0]?>" <?php echo $selected?>><?php echo trim(Core_Type_Conversion::toStr($str_explode[1]))?></option><?php
									}
									?>
									</select>
									<?php

									break;
								}

								case 9: // Текст "AS IS"
								{
									if (mb_strlen($value) != 0)
									{
										echo html_entity_decode($value, ENT_COMPAT, 'UTF-8');
									}
									else
									{
										?>&nbsp;<?php
									}

									break;
								}

								case 10: // Вычисляемое поле с помощью функции обратного вызова,
								// имя функции обратного вызова f($field_value, $value)
								// передается функции с именем, содержащимся в $field_value['callback_function']
								{
									// Выполним функцию обратного вызова
									if (isset($field_value['callback_function']))
									{
										if (function_exists($field_value['callback_function']))
										{
											$field_value['callback_function']($field_value, $value, $row);
										}
										else
										{
											echo "Ошибка! Функция {$field_value['callback_function']} не найдена!";
										}
									}
									else
									{
										echo "Функция обратного вызова не определена";
									}

									break;
								}

								default: // Тип не определен.
								{
									echo "&nbsp;";
									break;
								}
							}
							?></td><?php
						}

						// Действия для строки в правом столбце
						if ($form_row['admin_forms_show_operations'] && $this->ShowOperations
						|| $allow_filter && $this->ShowFilter)
						{
							// Определяем ширину столбца для действий.
							if (isset($this->form_params['actions_width']))
							{
								$width = Core_Type_Conversion::toStr($this->form_params['actions_width']);
							}
							else
							{
								$width = '10px'; // Минимальная ширина
							}

							// <nobr> из-за IE
							?><td class="admin_forms_action_td" style="width: <?php echo $width?>"><nobr><?php

							if ($all_events)
							{
								foreach ($all_events as $event_row)
								{
									// Отображаем действие, только если разрешено.
									if (!$event_row['admin_forms_events_show_button'])
									{
										continue;
									}

									// Проверяем, привязано ли действие к опр. dataset'у.
									if ($event_row['admin_forms_events_dataset_id'] != -1)
									{
										if ($event_row['admin_forms_events_dataset_id'] != $dataset_key)
										{
											continue;
										}
									}

									$word_row = $this->GetAdminFormsWordOrDefaultLanguageWord($event_row['admin_words_id']);

									if ($word_row)
									{
										$name = htmlspecialchars($word_row['name']);
									}
									else
									{
										$name = '';
									}

									// Делаем замены в AAdditionalParams
									$AAdditionalParams = $this->DoReplaces(false, $row, $this->AAdditionalParams);

									$row_link = $this->GetHtmlCallTrigerSingleAction(htmlspecialchars($event_row['admin_forms_events_function']), "check_{$dataset_key}_{$key_field_value}", $admin_forms_id, Core_Type_Conversion::toInt($this->form_params['current_page']), Core_Type_Conversion::toInt($this->form_params['on_page']), $this->order_field_id, $this->order_field_direction, $this->AAction, $AAdditionalParams);

									$row_onclick = $this->GetOnClickCallTrigerSingleAction(htmlspecialchars($event_row['admin_forms_events_function']), "check_{$dataset_key}_{$key_field_value}", $admin_forms_id, Core_Type_Conversion::toInt($this->form_params['current_page']), Core_Type_Conversion::toInt($this->form_params['on_page']), $this->order_field_id, $this->order_field_direction, $this->AAction, $AAdditionalParams);

									// Добавляем установку метки для чекбокса и строки + добавлем уведомление, если необходимо
									if ($event_row['admin_forms_events_ask'])
									{
										$row_onclick = "$('#' + getWindowId('{$window_id}') + ' #row_{$dataset_key}_{$key_field_value}').toggleHighlight(); res = confirm('".Core::_('admin_form.confirm_dialog', htmlspecialchars($name))."'); if (!res) { $('#' + getWindowId('{$window_id}') + ' #row_{$dataset_key}_{$key_field_value}').toggleHighlight(); } else { {$row_onclick}} return res;";
									}
									else
									{
										$row_onclick = "$('#' + getWindowId('{$window_id}') + ' #row_{$dataset_key}_{$key_field_value}').toggleHighlight(); {$row_onclick}";
									}

									?><a href="<?php echo $row_link?>" onclick="<?php echo $row_onclick?>"><img src="<?php echo htmlspecialchars($event_row['admin_forms_events_picture'])?>" alt="<?php echo $name?>" title="<?php echo $name?>"></a> <?php
								}
							}
							?></nobr></td><?php
						}

						?></tr><?php

					// Увеличиваем счетчик позиций
					$position++;
				}
			}
		}

		?></table><?php

		// Строка с действиями
		if ($this->ShowBottom)
		{
		?>
		<table cellpadding="5" cellspacing="0" border="1" width="100%" style="margin-top: 8px;" class="light_table">
		<tr>
		<?php
		// Чекбокс "Выбрать все" показываем только при наличии действий
		if ($form_row['admin_forms_show_operations'] && $this->ShowOperations)
		{
			?><td align="center" width="25">
				<input type="checkbox" name="admin_forms_all_check2" id="id_admin_forms_all_check2" onclick="$('#<?php echo $window_id?>').highlightAllRows(this.checked)" />
			</td><?php
		}

		?><td>
			<div class="admin_form_action"><?php

				if ($form_row['admin_forms_show_group_operations'])
				{
					if ($form_row['admin_forms_group_operations_as_images'])
					{
						// Групповые операции
						if ($all_events)
						{
							foreach ($all_events as $row)
							{
								if ($row['admin_forms_events_group_operation'])
								{
									$word_row = $this->GetAdminFormsWordOrDefaultLanguageWord($row['admin_words_id']);

									$text = htmlspecialchars(Core_Type_Conversion::toStr($word_row['name']));

									$action_href = $this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, $row['admin_forms_events_function'], Core_Type_Conversion::toInt($this->form_params['current_page']), Core_Type_Conversion::toInt($this->form_params['on_page']), $this->order_field_id, $this->order_field_direction);

									$action_onclick = $this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, $row['admin_forms_events_function'], Core_Type_Conversion::toInt($this->form_params['current_page']), Core_Type_Conversion::toInt($this->form_params['on_page']), $this->order_field_id, $this->order_field_direction);

									// Нужно подтверждение для действия
									if ($row['admin_forms_events_ask'])
									{
										$action_onclick = "res = confirm('" . Core::_('admin_form.confirm_dialog', htmlspecialchars($word_row['name']))."'); if (res) { $action_onclick } else {return false}";
									}

									// Если действие без предупреждения, то не выделяем красным ссылку
									if ($row['admin_forms_events_ask'] == 0)
									{
										$link_class = 'admin_form_action_link';
									}
									else
									{
										$link_class = 'admin_form_action_alert_link';
									}

									// ниже по тексту alt-ы и title-ы не выводятся, т.к. они дублируются текстовыми
									// надписями и при отключении картинок текст дублируется
									/* alt="<?php echo $text?>"*/
									?>
									<nobr>
									<a href="<?php echo $action_href?>" onclick="<?php echo $action_onclick?>"><img src="<?php echo htmlspecialchars($row['admin_forms_events_picture'])?>" title="<?php echo $text?>"></a>

									<a href="<?php echo $action_href?>" onclick="<?php echo $action_onclick?>" class="<?php echo $link_class?>"><?php echo $text?></a>
									</nobr>
									<?php
								}
							}
						}

					}
					else
					{
						?><select name="operation" id="id_admin_forms_group_operation">
							<option value="0">&hellip;</option>
							<?php
							if ($all_events)
							{
								foreach ($all_events as $row)
								{
									if ($row['admin_forms_events_group_operation'])
									{
										$word_row = $this->GetAdminFormsWordOrDefaultLanguageWord($row['admin_words_id']);
										if ($word_row)
										{
											?><option value="<?php echo htmlspecialchars($row['admin_forms_events_function'])?>">
												<?php echo htmlspecialchars(Core_Type_Conversion::toStr($word_row['name']))?>
											</option><?php
										}
									}
								}
							}
							?>
						</select>

						<input type="button" value="<?php echo Core::_('admin_form.button_execute')?>" onclick="CallGroupOperation('<?php echo $this->AAction?>', '<?php echo $this->AAdditionalParams?>', <?php echo $admin_forms_id?>, 0, 0, <?php echo $this->order_field_id?>, <?php echo $this->order_field_direction?>)" />
						<?php
					}
				}
				?>
			</div>
			</td>
			<td width="110" align="center">
				<div class="admin_form_action">
				<?php
				// Дописываем параметры фильтра
				if (count($_REQUEST) > 0)
				{
					foreach ($_REQUEST as $rkey => $rvalue)
					{
						// Передаем параметры фильтра
						if (mb_strpos($rkey, 'admin_form_filter_') === 0)
						{
							$this->AAdditionalParams .= "&{$rkey}=".urlencode($rvalue);
						}
					}
				}

				// &JsHttpRequest передается для исключения заголовка на странице
				$action_href = $this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams . "&JsHttpRequest&export_csv", $admin_forms_id, 'load_data', Core_Type_Conversion::toInt($this->form_params['current_page']), Core_Type_Conversion::toInt($this->form_params['on_page']), $this->order_field_id, $this->order_field_direction);
				?>
				<nobr>
				<a href="<?php echo $action_href?>" target="_blank"><img src="/admin/images/export.gif" title="<?php echo Core::_('admin_form.export_csv')?>"></a>
				<a href="<?php echo $action_href?>" target="_blank"><?php echo Core::_('admin_form.export_csv')?></a>
				</nobr>
				</div>
			</td>
			<td width="60" align="center">
				<!-- Отображаем select on_page -->
				<?php echo $select_on_page_html?>
			</td>
		</tr>
		</table>
		<?php
		} // end if ShowBottom

		// <!-- Ссылки 1, 2, 3 ... -->
		if (!empty($links_1_2_3))
		{
		?><p><?php echo $links_1_2_3?></p><?php
		}

		// XML-код для ShowXML
		if (isset ($_SESSION['valid_user']))
		{
			$kernel = & singleton('kernel');
			echo $kernel->GetXmlContent();
		}

		$html = ob_get_clean();

		return array(
		'form_html' => $html,
		'error' => $this->user_function_message,
		'title' => isset($this->form_params['title']) ? $this->form_params['title'] : '');
	}

	/**
	* Получение данных о форме центра управления.
	*
	* @param int $admin_forms_id идентификатор формы
	* @return array массив с данными о форме
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_id = 11;
	*
	* $row = $admin_forms->GetAdminForm($admin_forms_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetAdminForm($admin_forms_id)
	{
		$admin_forms_id = intval($admin_forms_id);

		$oAdmin_Form = Core_Entity::factory('Admin_Form')->find($admin_forms_id);

		if (!is_null($oAdmin_Form->id))
		{
			return array(
				'admin_forms_id' => $oAdmin_Form->id,
				'admin_words_id' => $oAdmin_Form->admin_word_id,
				'admin_forms_on_page' => $oAdmin_Form->on_page,
				'admin_forms_key_field' => $oAdmin_Form->key_field,
				'admin_forms_show_operations' => $oAdmin_Form->show_operations,
				'admin_forms_show_group_operations' => $oAdmin_Form->show_group_operations,
				'admin_forms_default_order_field' => $oAdmin_Form->default_order_field,
				'admin_forms_default_order_direction' => $oAdmin_Form->default_order_direction,
				'admin_forms_group_operations_as_images' => 1,
				'users_id' => $oAdmin_Form->user_id
			);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	* Получение списка всех полей формы администрирования
	*
	* @param int $admin_forms_id идентификатор формы, если false - извлекается информация о полях всех форм
	* @return array со данными о полях или false
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_id = false;
	*
	* $row = $admin_forms->GetAllAdminFormFields($admin_forms_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetAllAdminFormFields($admin_forms_id = false)
	{
		if ($admin_forms_id)
		{
			$aAdmin_Form_Fields = Core_Entity::factory('Admin_Form', $admin_forms_id)->admin_form_fields->findAll();
		}
		else
		{
			$aAdmin_Form_Fields = Core_Entity::factory('Admin_Form_Field')->findAll();
		}

		$result = FALSE;

		if (!empty($aAdmin_Form_Fields))
		{
			foreach ($aAdmin_Form_Fields as $oAdmin_Form_Field)
			{
				$result[] = $this->getArrayAdminFormField($oAdmin_Form_Field);
			}
		}

		return $result;
	}

	function getArrayAdminFormAction($oAdmin_Form_Action)
	{
		return array (
			'admin_forms_events_id' => $oAdmin_Form_Action->id,
			'admin_words_id' => $oAdmin_Form_Action->admin_word_id,
			'admin_forms_id' => $oAdmin_Form_Action->admin_form_id,
			'admin_forms_events_function' => $oAdmin_Form_Action->name,
			'admin_forms_events_picture' => $oAdmin_Form_Action->picture,
			'admin_forms_events_show_button' => $oAdmin_Form_Action->single,
			'admin_forms_events_group_operation' => $oAdmin_Form_Action->group,
			'admin_forms_events_order' => $oAdmin_Form_Action->sorting,
			'admin_forms_events_dataset_id' => $oAdmin_Form_Action->dataset,
			'admin_forms_events_ask' => $oAdmin_Form_Action->confirm,
			'users_id' => $oAdmin_Form_Action->user_id
		);
	}

	/**
	* Получение списка всех событий формы администрирования
	*
	* @param int $admin_forms_id идентификатор формы, если false - извлекается информация о событиях всех форм
	* @return array с данными о полях или false
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_id = false;
	*
	* $row = $admin_forms->GetAllAdminFormEvents($admin_forms_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetAllAdminFormEvents($admin_forms_id = false)
	{
		if ($admin_forms_id)
		{
			$aAdmin_Form_Actions = Core_Entity::factory('Admin_Form', $admin_forms_id)->Admin_Form_Actions->findAll();
		}
		else
		{
			$aAdmin_Form_Actions = Core_Entity::factory('Admin_Form_Action')->findAll();
		}

		$result = FALSE;

		if (!empty($aAdmin_Form_Actions))
		{
			foreach ($aAdmin_Form_Actions as $oAdmin_Form_Action)
			{
				$result[] = $this->getArrayAdminFormAction($oAdmin_Form_Action);
			}
		}

		return $result;
	}

	/**
	* Получение информации о настройках для формы у указанного пользователя.
	*
	* @param int $admin_forms_id идентификтор формы центры администрирования
	* @param int $users_id идентификтор пользователя
	* @return mixed массив с данными или false, если данные не найдены
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_id = 11;
	*
	* $row = $admin_forms->GetSettingsForForm($admin_forms_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetSettingsForForm($admin_forms_id, $users_id = FALSE)
	{
		$admin_forms_id = intval($admin_forms_id);

		if ($users_id === FALSE)
		{
			$kernel = & singleton('kernel');
			$users_id = $kernel->GetCurrentUser();
		}
		else
		{
			$users_id = intval($users_id);
		}

		$oAdmin_Form_Setting = Core_Entity::factory('Admin_Form', $admin_forms_id)->getSettingForUser($users_id);

		if ($oAdmin_Form_Setting)
		{
			return array(
				'admin_forms_settings_id' => $oAdmin_Form_Setting->id,
				'admin_forms_id' => $oAdmin_Form_Setting->admin_form_id,
				'users_id' => $oAdmin_Form_Setting->user_id,
				'admin_forms_settings_page_number' => $oAdmin_Form_Setting->page_number,
				'admin_forms_settings_order_field_id' => $oAdmin_Form_Setting->order_field_id,
				'admin_forms_settings_order_direction' => $oAdmin_Form_Setting->order_direction,
				'admin_forms_settings_filter' => $oAdmin_Form_Setting->filter,
				'admin_forms_settings_on_page' => $oAdmin_Form_Setting->on_page
			);
		}

		return FALSE;
	}

	/**
	* Получение названия и описания слова на выбранном языке. Если для выбранного
	* языка не найдено значение, получается значения для языка "по умолчанию".
	*
	* @param int $admin_words_id идентификатор слова
	* @param int $admin_language_id идентификатор языка, по умолчанию - текущий
	* @return mixed массив с результатом, ['name'] - имя, ['description'] - описание.
	* @see GetAdminFormsWord()
	*/
	function GetAdminFormsWordOrDefaultLanguageWord($admin_words_id, $admin_language_id = CURRENT_LANGUAGE_ID)
	{
		$word_row = $this->GetAdminFormsWord($admin_words_id, $admin_language_id);

		// Слово найдено
		// Пробуем найти текст для языка "по умолчанию"
		if (!$word_row)
		{
			// Получим ID языка "по умолчанию" по его имени
			$default_lng_row = $this->GetLanguageByShortName(DEFAULT_LNG);

			if ($default_lng_row)
			{
				$word_row = $this->GetAdminFormsWord($admin_words_id, Core_Type_Conversion::toInt($default_lng_row['admin_language_id']));
			}
		}

		return $word_row;
	}

	/**
	* Получение названия и описания слова на выбранном языке
	*
	* @param int $admin_words_id идентификатор слова
	* @param int $admin_language_id идентификатор языка, по умолчанию - текущий
	* @return mixed массив с результатом, ['name'] - имя, ['description'] - описание.
	*/
	function GetAdminFormsWord($admin_words_id, $admin_language_id = CURRENT_LANGUAGE_ID)
	{
		$admin_words_id = intval($admin_words_id);
		$admin_language_id = intval($admin_language_id);

		// Если есть в кэше - возвращаем информацию
		if (isset($this->CacheGetAdminFormsWord[$admin_language_id][$admin_words_id]))
		{
			return $this->CacheGetAdminFormsWord[$admin_language_id][$admin_words_id];
		}

		$oAdmin_Word_Value = Core_Entity::factory('Admin_Word', $admin_words_id)->getWordByLanguage($admin_language_id);

		$result = FAlSE;

		if ($oAdmin_Word_Value)
		{
			$result['name'] = $oAdmin_Word_Value->name;
			$result['description'] = $oAdmin_Word_Value->description;
			$result['admin_words_value_id'] = $oAdmin_Word_Value->id;

			$result['admin_words_id'] = $oAdmin_Word_Value->admin_word_id;
			$result['admin_language_id'] = $oAdmin_Word_Value->admin_language_id;
			$result['admin_words_value_name'] = $oAdmin_Word_Value->name;
			$result['admin_words_value_description'] = $oAdmin_Word_Value->description;
		}

		// Сохраняем в кэше
		$this->CacheGetAdminFormsWord[$admin_language_id][$admin_words_id] = $result;

		return $result;
	}

	/**
	* Проверка, существования идентификатора слова
	*
	* @param int $admin_words_id идентификатор слова
	* @return bool
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_words_id = 60;
	*
	* $result = $admin_forms->WordExists($admin_words_id);
	*
	* if ($result)
	* {
	* 	echo "Слово существует";
	* }
	* else
	* {
	* 	echo "Слово не существует";
	* }
	* ?>
	* </code>
	*/
	function WordExists($admin_words_id)
	{
		$admin_words_id = intval($admin_words_id);

		if (!$admin_words_id)
		{
			return FALSE;
		}

		$oAdmin_Word = Core_Entity::factory('Admin_Word')->find($admin_words_id);

		return !is_null($oAdmin_Word->id);
	}

	/**
	* Получение числа элементов на страницу
	*
	* @param mixed $admin_forms_id идентификатор формы. Если не указан, берется из $_REQUEST['admin_forms_id']. по умолчанию false
	* @return int число элементов на страницу
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $result = $admin_forms->GetOnPageCount();
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function GetOnPageCount($admin_forms_id = false)
	{
		if (!$admin_forms_id)
		{
			$admin_forms_id = Core_Type_Conversion::toInt($_REQUEST['admin_forms_id']);
		}

		if (isset($_REQUEST['admin_forms_on_page']))
		{
			return Core_Type_Conversion::toInt($_REQUEST['admin_forms_on_page']);
		}

		$row = $this->GetAdminForm($admin_forms_id);

		if (!$row)
		{
			return 10;
		}
		else
		{
			return $row['admin_forms_on_page'];
		}
	}

	/**
	* Показ строки ссылок
	*
	* @param int $total_count общее число записей
	* @param int $count_on_page число записей на страницу
	* @param int $current_page номер текущей страницы
	* @param array $param массив дополнительных параметров
	* - $param['separator'] разделитель между ссылками
	* - $param['count_num'] число ссылок перед и после текущей страницы, а также в окончании стороки ссылок
	* - $param['first'] строка для отображения ссылки на первую страницу
	* - $param['pred'] строка для отображения ссылки на предыдущую страницу
	* - $param['last'] строка для отображения ссылки на последнюю страницу
	* - $param['next'] строка для отображения ссылки на следующую страницу
	* - $param['show_first'] параметр, определяющий показывать ссылку на первую страницу
	* - $param['show_last'] параметр, определяющий показывать ссылку на последнюю страницу
	* - $param['show_pred'] параметр, определяющий показывать ссылку на предыдущую страницу
	* - $param['show_next'] параметр, определяющий показывать ссылку на следующую страницу
	* - $param['admin_forms_id'] идентификатор формы центра администрирования
	*/
	function ShowLink($total_count, $count_on_page, $current_page, $param = array())
	{
		// Итоговая строка ссылок.
		$link_string = '';

		$total_count = Core_Type_Conversion::toInt($total_count);
		if ($total_count < 0)
		{
			$total_count = 0;
		}

		$count_on_page = Core_Type_Conversion::toInt($count_on_page);

		if ($count_on_page < 1)
		{
			$count_on_page = 1;
		}

		$current_page = Core_Type_Conversion::toInt($current_page);

		if ($current_page < 1)
		{
			$current_page = 1;
		}

		$param = Core_Type_Conversion::toArray($param);

		if (isset($param['separator']))
		{
			$param['separator'] = Core_Type_Conversion::toStr($param['separator']);
		}
		else
		{
			$param['separator'] = ' - ';
		}

		// 3.04.2007 убираем separator вообще.
		$param['separator'] = '';

		if (isset($param['count_num']))
		{
			$param['count_num'] = Core_Type_Conversion::toInt($param['count_num']);

			if ($param['count_num'] < 0)
			{
				$param['count_num'] = 5;
			}
		}
		else
		{
			$param['count_num'] = 5;
		}

		// Home
		if (isset($param['first']))
		{
			$param['first'] = Core_Type_Conversion::toStr($param['first']);
		}
		else
		{
			$param['first'] = '&laquo;';
		}

		// End
		if (isset($param['last']))
		{
			$param['last'] = Core_Type_Conversion::toStr($param['last']);
		}
		else
		{
			$param['last'] = '&raquo;';
		}

		// Предыдущий
		if (isset($param['pred']))
		{
			$param['pred'] = Core_Type_Conversion::toStr($param['pred']);
		}
		else
		{
			$param['pred'] = '&larr;';
		}

		// Следующий
		if (isset($param['next']))
		{
			$param['next'] = Core_Type_Conversion::toStr($param['next']);
		}
		else
		{
			$param['next'] = '&rarr;';
		}

		if (isset($param['show_first']))
		{
			$param['show_first'] = Core_Type_Conversion::toBool($param['show_first']);
		}
		else
		{
			$param['show_first'] = false;
		}

		if (isset($param['show_last']))
		{
			$param['show_last'] = Core_Type_Conversion::toBool($param['show_last']);
		}
		else
		{
			$param['show_last'] = false;
		}

		if (isset($param['show_pred']))
		{
			$param['show_pred'] = Core_Type_Conversion::toBool($param['show_pred']);
		}
		else
		{
			$param['show_pred'] = false;
		}

		if (isset($param['show_next']))
		{
			$param['show_next'] = Core_Type_Conversion::toBool($param['show_next']);
		}
		else
		{
			$param['show_next'] = false;
		}

		// Идентификатор формы.
		$admin_forms_id = Core_Type_Conversion::toInt($param['admin_forms_id']);

		// Определяем общее число страниц.
		$total_page = $total_count / $count_on_page;

		// Округляем в большую сторону.
		if ($total_count % $count_on_page != 0)
		{
			$total_page = intval($total_count/$count_on_page) + 1;
		}

		// 6.0
		$window_id = $this->GetWindowId();

		// Формируем скрытые ссылки навигации для перехода по Ctrl + стрелка.
		if ($current_page < $total_page)
		{
			// Ссылка на следующую страницу.
			$page = $current_page + 1 ? $current_page + 1 : 1;

			$link_string .= '<a href="javascript:DoLoadAjax(\''.$this->AAction."', '" . $this->AAdditionalParams . "', " . $admin_forms_id.', \'load_data\', '.$page.', 0, '.$this->order_field_id.', '.$this->order_field_direction.', \''.$window_id.'\')" id="id_next"></a>';
		}

		if ($current_page > 1)
		{
			// Ссылка на предыдущую страницу.
			$page = $current_page - 1 ? $current_page - 1 : 1;
			$link_string .= '<a href="javascript:DoLoadAjax(\''.$this->AAction."', '" . $this->AAdditionalParams . "', " . $admin_forms_id.', \'load_data\', '.$page.', 0, '.$this->order_field_id.', '.$this->order_field_direction.', \''.$window_id.'\')" id="id_prev"></a>';
		}

		// Отображаем строку ссылок, если общее число страниц больше 1.
		if ($total_page > 1)
		{
			if ($current_page > $total_page)
			{
				$current_page = $total_page;
			}

			// Определяем номер ссылки, с которой начинается строка ссылок.
			if ($current_page - $param['count_num'] < 1)
			{
				$link_num_begin = 1;
			}
			else
			{
				$link_num_begin = $current_page - $param['count_num'];
			}

			// Определяем номер ссылки, которой заканчивается строка ссылок.
			$link_num_end = $current_page + $param['count_num'];

			if ($link_num_end > $total_page)
			{
				$link_num_end = $total_page;
			}

			// Определяем число ссылок выводимых на страницу.
			$count_link = $link_num_end - $link_num_begin + 1;

			if ($current_page == 1)
			{
				// Показывать символы перехода на первую страницу.
				if ($param['show_first'] === true)
				{
					$link_string .= '<span class="arrow_span">'.$param['first'].'</span>';
				}

				// Показывать символы перехода на предыдущую страницу.
				if ($param['show_pred'] === true)
				{
					$link_string .= '<span class="arrow_span">'.$param['pred'].'</span>';
				}

				$link_string .= ' <span class="current">'.$link_num_begin.'</span>';
			}
			else
			{
				// Отображать ссылку на первую страницу.
				if ($param['show_first'] === true)
				{
					$link_string .= '<a
					href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 1, 0, $this->order_field_id, $this->order_field_direction).'"
					onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 1, 0, $this->order_field_id, $this->order_field_direction).'"
					class="page_link">'.$param['first'].'</a>';
				}

				// Отображать ссылку на предыдущую страницу.
				if ($param['show_pred'] === true)
				{
					$page = $current_page - 1 ? $current_page - 1 : 1;
					$link_string .= ' <a
					href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $page, 0, $this->order_field_id, $this->order_field_direction) .'"
					onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $page, 0, $this->order_field_id, $this->order_field_direction) .'"
					class="page_link">'.$param['pred'].'</a>';
				}

				$link_string.= ' <a
				href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 1, 0, $this->order_field_id, $this->order_field_direction).'"
				onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 1, 0, $this->order_field_id, $this->order_field_direction).'"
				class="page_link">1</a>';

				// Выведем ... со ссылкой на 2-ю страницу, если показываем с 3-й
				if ($link_num_begin > 1)
				{
					$link_string.= ' <a
					href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 2, 0, $this->order_field_id, $this->order_field_direction) .'"
					onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', 2, 0, $this->order_field_id, $this->order_field_direction) .'"
					class="page_link">...</a>';
				}
			}

			for ($i = 0; $i < $count_link; $i++)
			{
				$link_number = $link_num_begin + $i;

				// Страница не является первой и не является последней.
				if ($i != 0 && $i != ($count_link - 1))
				{
					if ($link_number == $current_page)
					{
						// Страница является текущей.
						$link_string .= $param['separator'].'<span class="current">'.$link_number.'</span>';
					}
					else
					{
						// Страница - не текущая.
						$link_string .= $param['separator']. '<a
						href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $link_number, 0, $this->order_field_id, $this->order_field_direction).'"
						onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $link_number, 0, $this->order_field_id, $this->order_field_direction).'"
						class="page_link">'.$link_number.'</a>';
					}
				}
			}

			// Если последняя страница является текущей
			if ($current_page == $total_page)
			{
				$link_string.= $param['separator'].' <span class="current">'.$total_page.'</span>';

				// Отображать ссылку на следующую страницу.
				if ($param['show_next'] === true)
				{
					$link_string .= ' <span class="arrow_span">'.$param['next'].'</span>';
				}

				// Отображать ссылку на последнюю страницу.
				if ($param['show_last'] === true)
				{
					$link_string .= ' <span class="arrow_span">'.$param['last'].'</span>';
				}
			}
			else
			{
				// Выведем ... со ссылкой на предпоследнюю страницу
				if ($link_num_end < $total_page)
				{
					$link_string.= ' <a
					href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', ($total_page - 1), 0, $this->order_field_id, $this->order_field_direction).'"
					onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', ($total_page - 1), 0, $this->order_field_id, $this->order_field_direction).'"
					class="page_link">...</a>';
				}

				// Последняя страница не является текущей
				$link_string .= $param['separator'].'<a
				href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $total_page, 0, $this->order_field_id, $this->order_field_direction).'"
				onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $total_page, 0, $this->order_field_id, $this->order_field_direction).'"
				class="page_link">'.$total_page.'</a>';

				// Отображать ссылку на следующую страницу.
				if ($param['show_next'] === true)
				{
					$page = $current_page + 1 ? $current_page + 1 : 1;
					$link_string.=' <a
					href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $page, 0, $this->order_field_id, $this->order_field_direction).'"
					onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $page, 0, $this->order_field_id, $this->order_field_direction).'"
					class="page_link">'.$param['next'].'</a>';
				}
				// Отображать ссылку на последнюю страницу.
				if ($param['show_last'] === true)
				{
					$page = $total_page ? $total_page : 1;
					$link_string.=' <a
					href="'.$this->GetHtmlCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $page, 0, $this->order_field_id, $this->order_field_direction).'"
					onclick="'.$this->GetOnClickCallDoLoadAjax($this->AAction, $this->AAdditionalParams, $admin_forms_id, 'load_data', $page, 0, $this->order_field_id, $this->order_field_direction).'"
					class="page_link">'.$param['last'].'</a>';
				}
			}

			$link_string = '<div style="float: left; text-align: center;">'.$link_string.'</div>';

			$link_string .= '<div style="clear: both"></div>';
		}

		return $link_string;
	}

	/**
	* Получение списка всех событий формы центра администрирования
	*
	* @param int $admin_forms_id идентификатор формы центра администрирования
	* @param int $users_id идентификатор пользователя, по умолчанию false. Если передан - производится выборка только доступных действий для пользователя
	* @return array массив со списком событий
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_id = 11;
	*
	* $row = $admin_forms->GetAllEvents($admin_forms_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetAllEvents($admin_forms_id, $users_id = FALSE)
	{
		if ($admin_forms_id === FALSE)
		{
			$aAdmin_Form_Actions = Core_Entity::factory('Admin_Form_Action')->findAll();
		}
		else
		{
			$oAdmin_Form_Actions = Core_Entity::factory('Admin_Form', $admin_forms_id)
				->Admin_Form_Actions;

			if ($users_id)
			{
				$oUser = Core_Entity::factory('User', $users_id);
				$aAdmin_Form_Actions = $oAdmin_Form_Actions->getAllowedActionsForUser($oUser);
			}
			else
			{
				$aAdmin_Form_Actions = $oAdmin_Form_Actions->findAll();
			}
		}

		$result = array();

		foreach ($aAdmin_Form_Actions as $oAdmin_Form_Action)
		{
			$result[] = $this->getArrayAdminFormAction($oAdmin_Form_Action);
		}

		return $result;
		/*
		$from = "";

		if ($users_id !== false)
		{
			$users_id = intval($users_id);

			$user_access = & singleton('user_access');
			$user_row = $user_access->GetUser($users_id);

			// Ограничиваем, только если пользователь не супреюзер
			if (Core_Type_Conversion::toInt($user_row['users_superuser']) == 0)
			{
				$users_type_id = Core_Type_Conversion::toInt($user_row['users_type_id']);
				$from = ", user_group_action_accesses";
				$where .= " AND `admin_form_actions`.`id` = `user_group_action_accesses`.`admin_form_action_id`
				AND `user_group_id` = '$users_type_id'
				AND `site_id` = '".CURRENT_SITE."'";
			}
		}

		$query = "SELECT `admin_form_actions`.`id`
		FROM `admin_form_actions` {$from}
		WHERE 1 {$where}
		ORDER BY `sorting` ASC";

		$DataBase = & singleton('DataBase');
		$DataBase->select($query);

		$result = array();

		if ($DataBase->result)
		{
			while ($row = mysql_fetch_assoc($DataBase->result))
			{
				$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action', $row['id']);
				$result[] = $this->getArrayAdminFormAction($oAdmin_Form_Action);
			}
		}
		return $result;
		*/
	}

	/**
	* Добавление внешней подстановки
	*
	* @param string $name навание подстановки
	* @param string $value значение подстановки
	* @return self
	*/
	function AddExternalReplace($name, $value)
	{
		$this->external_replaces[$name] = $value;
	}

	function getArrayAdminFormField($oAdmin_Form_Field)
	{
		return array (
			'admin_forms_field_id' => $oAdmin_Form_Field->id,
			'admin_forms_id' => $oAdmin_Form_Field->admin_form_id,
			'admin_words_id' => $oAdmin_Form_Field->admin_word_id,
			'admin_forms_field_name' => $oAdmin_Form_Field->name,
			'admin_forms_field_order' => $oAdmin_Form_Field->sorting,
			'admin_forms_field_type' => $oAdmin_Form_Field->type,
			'admin_forms_field_format' => $oAdmin_Form_Field->format,
			'admin_forms_field_allow_order' => $oAdmin_Form_Field->allow_sorting,
			'admin_forms_field_allow_filter' => $oAdmin_Form_Field->allow_filter,
			'admin_forms_field_align_title' => $oAdmin_Form_Field->title_align,
			'admin_forms_field_align' => $oAdmin_Form_Field->align,
			'admin_forms_field_width' => $oAdmin_Form_Field->width,
			'admin_forms_field_style' => $oAdmin_Form_Field->style,
			'admin_forms_field_attrib' => $oAdmin_Form_Field->attributes,
			'admin_forms_field_image' => $oAdmin_Form_Field->image,
			'admin_forms_field_link' => $oAdmin_Form_Field->link,
			'admin_forms_field_onclick' => $oAdmin_Form_Field->onclick,
			'admin_forms_field_list' => $oAdmin_Form_Field->list,
			'users_id' => $oAdmin_Form_Field->user_id
		);
	}

	/**
	* Извлечение информации о поле формы центра администрирования
	*
	* @param int $admin_forms_field_id идентификатор поля
	* @return mixed массив с информацией о поле или false
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_field_id = 1;
	*
	* $row = $admin_forms->GetAdminFormsField($admin_forms_field_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetAdminFormsField($admin_forms_field_id)
	{
		$admin_forms_field_id = intval($admin_forms_field_id);

		$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field')->find($admin_forms_field_id);

		if ($oAdmin_Form_Field->id)
		{
			return $this->getArrayAdminFormField($oAdmin_Form_Field);
		}
		else
		{
			return FALSE;
		}
	}

	/**
	* Получение информации о событии (действии) формы центра администрирования
	*
	* @param int $admin_forms_events_id идентификатор события
	* @return mixed массив с информацией о событии или false
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_events_id = 1;
	*
	* $row = $admin_forms->GetAdminFormsEvent($admin_forms_events_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetAdminFormsEvent($admin_forms_events_id)
	{
		$admin_forms_events_id = intval($admin_forms_events_id);

		$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action')->find($admin_forms_events_id);

		if (!is_null($oAdmin_Form_Action->id))
		{
			return $this->getArrayAdminFormAction($oAdmin_Form_Action);
		}

		return FALSE;
	}

	/**
	* Получение информации о событии (действии) формы центра администрирования
	* по идентификатору формы и псевдониму действия
	*
	* @param int $admin_forms_id идентификатор формы
	* @param int $admin_forms_events_function псевдоним действия (имя функции обработчика)
	* @return mixed массив с информацией о событии или false
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_id = 1;
	* $admin_forms_events_function = 'edit_form';
	*
	* $row = $admin_forms->GetAdminFormsEvent($admin_forms_events_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetAdminFormsEventByName($admin_forms_id, $admin_forms_events_function)
	{
		$admin_forms_id = intval($admin_forms_id);

		$oAdmin_Form_Action = Core_Entity::factory('Admin_Form', $admin_forms_id)
					->Admin_Form_Actions
					->getByName($admin_forms_events_function);

		if ($oAdmin_Form_Action)
		{
			return $this->getArrayAdminFormAction($oAdmin_Form_Action);
		}
		else
		{
			return false;
		}
	}

	/**
	* Вставка/обновление формы центра управления
	*
	* @param array $param ассоциативный массив параметров
	* <br />int $param['admin_forms_id'] идентификатор формы
	* <br />int $param['admin_words_id'] идентификатор слова названия/описания формы
	* <br />int $param['admin_forms_on_page'] количество строк, выводимых на страницу
	* <br />string $param['admin_forms_key_field'] ключевое поле формы
	* <br />int $param['admin_forms_show_operations'] отображать столбец действий
	* <br />int $param['admin_forms_show_group_operations'] отображать групповые операции
	* <br />string $param['admin_forms_default_order_field'] поле для сортировки по умолчанию
	* <br />int $param['admin_forms_default_order_direction'] направление сортировки по умолчанию (1 - ASC, 2 - DESC)
	* <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	* @return int идентификатор вставленной/обновленной формы или false
	* @see InsertWord()
	* @see InsertWordsValue()
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $param['admin_words_id'] = 4;
	* $param['admin_forms_on_page'] = 5;
	* $param['admin_forms_key_field'] = '';
	* $param['admin_forms_show_operations'] = 1;
	* $param['admin_forms_show_group_operations'] = 1;
	* $param['admin_forms_default_order_field'] = '';
	* $param['admin_forms_default_order_direction'] = 1;
	*
	* $newid = $admin_forms->InsertAdminForm($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function InsertAdminForm($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['admin_forms_id']) || $param['admin_forms_id'] == 0)
		{
			$param['admin_forms_id'] = NULL;
		}

		$oAdmin_Form = Core_Entity::factory('Admin_Form', $param['admin_forms_id']);

		if (isset($param['admin_words_id']))
		{
			$oAdmin_Form->admin_word_id = Core_Type_Conversion::toInt($param['admin_words_id']);
		}

		if (isset($param['admin_forms_on_page']))
		{
			$oAdmin_Form->on_page = Core_Type_Conversion::toInt($param['admin_forms_on_page']);
		}

		if (isset($param['admin_forms_key_field']))
		{
			$oAdmin_Form->key_field = Core_Type_Conversion::toStr($param['admin_forms_key_field']);
		}

		if (isset($param['admin_forms_show_operations']))
		{
			$oAdmin_Form->show_operations = Core_Type_Conversion::toInt($param['admin_forms_show_operations']);
		}

		if (isset($param['admin_forms_show_group_operations']))
		{
			$oAdmin_Form->show_group_operations = Core_Type_Conversion::toInt($param['admin_forms_show_group_operations']);
		}

		/*if (isset($param['admin_forms_group_operations_as_images']))
		{
			$oAdmin_Form->show_group_operations_as_images = Core_Type_Conversion::toInt($param['admin_forms_group_operations_as_images']);
		}*/

		if (isset($param['admin_forms_default_order_field']))
		{
			$oAdmin_Form->default_order_field = Core_Type_Conversion::toStr($param['admin_forms_default_order_field']);
		}

		if (isset($param['admin_forms_default_order_direction']))
		{
			$oAdmin_Form->default_order_direction = Core_Type_Conversion::toInt($param['admin_forms_default_order_direction']);
		}

		if (is_null($param['admin_forms_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oAdmin_Form->user_id = $param['users_id'];
		}

		$oAdmin_Form->save();

		return $oAdmin_Form->id;
	}

	/**
	* Вставка информации о поле формы центра администрирования
	*
	* @param array $param массив с параметрами
	* <br />int $param['admin_forms_field_id'] идентификатор поля формы
	* <br />int $param['admin_forms_id'] идентификатор формы
	* <br />int $param['admin_words_id'] идентификатор слова названия/описания формы
	* <br />string $param['admin_forms_field_name'] название поля БД
	* <br />int $param['admin_forms_field_order'] порядок сортировки
	* <br />int $param['admin_forms_field_type'] тип поля
	* <br />string $param['admin_forms_field_format'] формат отображения данных
	* <br />int $param['admin_forms_field_allow_order'] разрешить сортировку поля
	* <br />int $param['admin_forms_field_allow_filter'] разрешить фильтр для поля
	* <br />string $param['admin_forms_field_align_title'] выравнивание заголовка (left, center, right)
	* <br />string $param['admin_forms_field_align'] выравнивание значения (left, center, right)
	* <br />string $param['admin_forms_field_width'] ширина поля
	* <br />string $param['admin_forms_field_style'] CSS-стиль
	* <br />string $param['admin_forms_field_attrib'] список атрибутов
	* <br />string $param['admin_forms_field_image'] путь к изображению с картинкой
	* <br />string $param['admin_forms_field_list'] массив значений выпадающего списка
	* <br />string $param['admin_forms_field_link'] адрес ссылки
	* <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	* @return int идентификатор вставленного поля
	* @see InsertWord()
	* @see InsertWordsValue()
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $param['admin_forms_id'] = 100000;
	* $param['admin_words_id'] = 5;
	* $param['admin_forms_field_name'] = 'New';
	* $param['admin_forms_field_order'] = 1;
	* $param['admin_forms_field_type'] = 1;
	* $param['admin_forms_field_allow_order'] = 1;
	* $param['admin_forms_field_allow_filter'] = 1;
	* $param['admin_forms_field_link'] = '';
	* $param['admin_forms_field_onclick'] = '';
	* $param['admin_forms_field_list'] = '';
	*
	* $newid = $admin_forms->InsertAdminFormsField($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function InsertAdminFormsField($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['admin_forms_field_id']) || $param['admin_forms_field_id'] == 0)
		{
			$param['admin_forms_field_id'] = NULL;
		}

		$oAdmin_Form_Field = Core_Entity::factory('Admin_Form_Field', $param['admin_forms_field_id']);

		if (isset($param['admin_forms_id']))
		{
			$oAdmin_Form_Field->admin_form_id = Core_Type_Conversion::toInt($param['admin_forms_id']);
		}

		if (isset($param['admin_words_id']))
		{
			$oAdmin_Form_Field->admin_word_id = Core_Type_Conversion::toInt($param['admin_words_id']);
		}

		if (isset($param['admin_forms_field_name']))
		{
			$oAdmin_Form_Field->name = Core_Type_Conversion::toStr($param['admin_forms_field_name']);
		}

		if (isset($param['admin_forms_field_order']))
		{
			$oAdmin_Form_Field->sorting = Core_Type_Conversion::toInt($param['admin_forms_field_order']);
		}

		if (isset($param['admin_forms_field_type']))
		{
			$oAdmin_Form_Field->type = Core_Type_Conversion::toInt($param['admin_forms_field_type']);
		}

		if (isset($param['admin_forms_field_format']))
		{
			$oAdmin_Form_Field->format = Core_Type_Conversion::toStr($param['admin_forms_field_format']);
		}

		if (isset($param['admin_forms_field_allow_order']))
		{
			$oAdmin_Form_Field->allow_sorting = Core_Type_Conversion::toInt($param['admin_forms_field_allow_order']);
		}

		if (isset($param['admin_forms_field_allow_filter']))
		{
			$oAdmin_Form_Field->allow_filter = Core_Type_Conversion::toInt($param['admin_forms_field_allow_filter']);
		}

		if (isset($param['admin_forms_field_align_title']))
		{
			$oAdmin_Form_Field->title_align = Core_Type_Conversion::toStr($param['admin_forms_field_align_title']);
		}

		if (isset($param['admin_forms_field_align']))
		{
			$oAdmin_Form_Field->align = Core_Type_Conversion::toStr($param['admin_forms_field_align']);
		}

		if (isset($param['admin_forms_field_width']))
		{
			$oAdmin_Form_Field->width = Core_Type_Conversion::toStr($param['admin_forms_field_width']);
		}

		if (isset($param['admin_forms_field_style']))
		{
			$oAdmin_Form_Field->style = Core_Type_Conversion::toStr($param['admin_forms_field_style']);
		}

		if (isset($param['admin_forms_field_attrib']))
		{
			$oAdmin_Form_Field->attributes = Core_Type_Conversion::toStr($param['admin_forms_field_attrib']);
		}

		if (isset($param['admin_forms_field_image']))
		{
			$oAdmin_Form_Field->image = Core_Type_Conversion::toStr($param['admin_forms_field_image']);
		}

		if (isset($param['admin_forms_field_list']))
		{
			$oAdmin_Form_Field->list = Core_Type_Conversion::toStr($param['admin_forms_field_list']);
		}

		if (isset($param['admin_forms_field_link']))
		{
			$oAdmin_Form_Field->link = Core_Type_Conversion::toStr($param['admin_forms_field_link']);
		}

		if (isset($param['admin_forms_field_onclick']))
		{
			$oAdmin_Form_Field->onclick = Core_Type_Conversion::toStr($param['admin_forms_field_onclick']);
		}

		if (is_null($param['admin_forms_field_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oAdmin_Form_Field->user_id = $param['users_id'];
		}

		$oAdmin_Form_Field->save();

		return $oAdmin_Form_Field->id;
	}

	/**
	* Вставка/обновление информации о событии (дейтсвии) формы администрирования
	*
	* @param array $param массив с параметрами
	* <br />int $param['admin_forms_events_id'] идентификатор события
	* <br />int $param['admin_forms_id'] идентификатор формы центра администрирования
	* <br />int $param['admin_words_id'] идентификатор слова с названием и описанием события
	* <br />string $param['admin_forms_events_function'] функция вызываемая загрузчиком
	* <br />string $param['admin_forms_events_picture'] путь к изображению
	* <br />int $param['admin_forms_events_show_button'] отображать кнопку действия
	* <br />int $param['admin_forms_events_group_operation'] отображать в групповых операциях
	* <br />int $param['admin_forms_events_order'] порядок сортировки
	* <br />int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	* @return mixed идентификатор вставленной/обновленной записи или false
	* @see InsertWord()
	* @see InsertWordsValue()
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $param['admin_forms_id'] = 100000;
	* $param['admin_words_id'] = 5;
	* $param['admin_forms_events_function'] = '';
	* $param['admin_forms_events_picture'] = '';
	* $param['admin_forms_events_show_button'] = 1;
	* $param['admin_forms_events_group_operation'] = 1;
	* $param['admin_forms_events_order'] = 10;
	*
	* $newid = $admin_forms->InsertAdminFormsEvent($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function InsertAdminFormsEvent($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['admin_forms_events_id']) || $param['admin_forms_events_id'] == 0)
		{
			$param['admin_forms_events_id'] = NULL;
		}

		$oAdmin_Form_Action = Core_Entity::factory('Admin_Form_Action', $param['admin_forms_events_id']);

		if (isset($param['admin_forms_id']))
		{
			$oAdmin_Form_Action->admin_form_id = Core_Type_Conversion::toInt($param['admin_forms_id']);
		}

		if (isset($param['admin_words_id']))
		{
			$oAdmin_Form_Action->admin_word_id = Core_Type_Conversion::toInt($param['admin_words_id']);
		}

		if (isset($param['admin_forms_events_function']))
		{
			$oAdmin_Form_Action->name = Core_Type_Conversion::toStr($param['admin_forms_events_function']);
		}

		if (isset($param['admin_forms_events_picture']))
		{
			$oAdmin_Form_Action->picture = Core_Type_Conversion::toStr($param['admin_forms_events_picture']);
		}

		if (isset($param['admin_forms_events_show_button']))
		{
			$oAdmin_Form_Action->single = Core_Type_Conversion::toInt($param['admin_forms_events_show_button']);
		}

		if (isset($param['admin_forms_events_group_operation']))
		{
			$oAdmin_Form_Action->group = Core_Type_Conversion::toInt($param['admin_forms_events_group_operation']);
		}

		if (isset($param['admin_forms_events_order']))
		{
			$oAdmin_Form_Action->sorting = Core_Type_Conversion::toInt($param['admin_forms_events_order']);
		}

		if (isset($param['admin_forms_events_ask']))
		{
			$oAdmin_Form_Action->confirm = Core_Type_Conversion::toInt($param['admin_forms_events_ask']);
		}

		if (isset($param['admin_forms_events_dataset_id']))
		{
			$oAdmin_Form_Action->dataset = Core_Type_Conversion::toInt($param['admin_forms_events_dataset_id']);
		}

		if (is_null($param['admin_forms_events_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oAdmin_Form_Action->user_id = $param['users_id'];
		}

		$oAdmin_Form_Action->save();

		return $oAdmin_Form_Action->id;
	}

	/**
	* Вставка/обновление информации о настройках формы для пользователя
	*
	* @param array $param массив параметров
	* <br />int $param['admin_forms_settings_id'] идентификатор настройки, если не указан - производится вставка записи
	* <br />int $param['users_id'] идентификатор пользователя
	* <br />int $param['admin_forms_id'] идентификатор формы
	* <br />int $param['admin_forms_settings_page_number'] номер страницы
	* <br />int $param['admin_forms_settings_order_field_id'] идентификатор поля сортировки
	* <br />int $param['admin_forms_settings_order_direction'] направление сортировки (1 - ASC, 2 - DESC)
	* <br />string $param['admin_forms_settings_filter'] сериализованный массив с фильтром
	* <br />int $param['admin_forms_settings_on_page'] число элементов на страницу
	* @return int идентификтор вставленной/обновленной настройки или false
	*/
	function InsertSettings($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['admin_forms_settings_id']) || $param['admin_forms_settings_id'] == 0)
		{
			$param['admin_forms_settings_id'] = NULL;
		}

		$oAdmin_Form_Setting = Core_Entity::factory('Admin_Form_Setting', $param['admin_forms_settings_id']);

		if (isset($param['admin_forms_id']))
		{
			$oAdmin_Form_Setting->admin_form_id = Core_Type_Conversion::toInt($param['admin_forms_id']);
		}

		if (isset($param['admin_forms_settings_page_number']))
		{
			$oAdmin_Form_Setting->page_number = Core_Type_Conversion::toInt($param['admin_forms_settings_page_number']);
		}

		if (isset($param['admin_forms_settings_order_field_id']))
		{
			$oAdmin_Form_Setting->order_field_id = Core_Type_Conversion::toInt($param['admin_forms_settings_order_field_id']);
		}

		if (isset($param['admin_forms_settings_order_direction']))
		{
			$oAdmin_Form_Setting->order_direction = Core_Type_Conversion::toInt($param['admin_forms_settings_order_direction']);
		}

		if (isset($param['admin_forms_settings_filter']))
		{
			$oAdmin_Form_Setting->filter = Core_Type_Conversion::toStr($param['admin_forms_settings_filter']);
		}

		if (isset($param['admin_forms_settings_on_page']))
		{
			$oAdmin_Form_Setting->on_page = Core_Type_Conversion::toInt($param['admin_forms_settings_on_page']);
		}

		if (is_null($param['admin_forms_settings_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oAdmin_Form_Setting->user_id = $param['users_id'];
		}

		$oAdmin_Form_Setting->save();

		return $oAdmin_Form_Setting->id;
	}

	/**
	* Удаление поля формы администрирования
	*
	* @param int $admin_forms_field_id идентификатор поля
	* @return resource результат выполнения запроса
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_field_id = 100002;
	*
	* $resource = $admin_forms->DeleteAdminFormsField($admin_forms_field_id);
	*
	* // Распечатаем результат
	* echo $resource;
	* ?>
	* </code>
	*/
	function DeleteAdminFormsField($admin_forms_field_id)
	{
		$admin_forms_field_id = intval($admin_forms_field_id);
		Core_Entity::factory('Admin_Form_Field', $admin_forms_field_id)->markDeleted();
		return TRUE;
	}

	/**
	* Удаление события (дейтсвия) формы администрирования
	*
	* @param int $admin_forms_events_id идентификатор события, которое нужно удалить
	* @return resource результат выполнения запроса
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_events_id = 100001;
	*
	* $resource = $admin_forms->DeleteAdminFormsEvent($admin_forms_events_id);
	*
	* // Распечатаем результат
	* echo $resource;
	* ?>
	* </code>
	*/
	function DeleteAdminFormsEvent($admin_forms_events_id)
	{
		$admin_forms_events_id = intval($admin_forms_events_id);
		Core_Entity::factory('Admin_Form_Action', $admin_forms_events_id)->markDeleted();
		return TRUE;
	}

	/**
	* Удаление слова и всех его значений
	*
	* @param int $admin_words_id идентификатор слова
	* @return bool результат выполнения запроса
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_words_id = 4;
	*
	* $result = $admin_forms->DeleteWord($admin_words_id);
	*
	* if ($result)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	*/
	function DeleteWord($admin_words_id)
	{
		$admin_words_id = intval($admin_words_id);
		Core_Entity::factory('Admin_Word', $admin_words_id)->markDeleted();
		return TRUE;
	}

	/**
	* Удаление формы центра администрирования
	*
	* @param int $admin_forms_id идентификатор формы центра администрирования
	* @return bool результат выполнения запроса
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_forms_id = 1;
	*
	* $result = $admin_forms->DeleteAdminForm($admin_forms_id);
	*
	* if ($result)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	*/
	function DeleteAdminForm($admin_forms_id)
	{
		$admin_forms_id = intval($admin_forms_id);
		Core_Entity::factory('Admin_Form', $admin_forms_id)->markDeleted();
		return TRUE;
	}

	/**
	* Подстановка стандартных значений полей
	*
	* @param int $admin_forms_id идентификатор формы, если указан - берется список полей данной формы.
	* @param array $field_list массив со списком полей, может быть false
	* @param string $subject строка, в которой необходимо произвести подстановки
	* @return string строка с заменой подстановок
	*/
	function DoReplaces($admin_forms_id = false, $field_list = false, $subject)
	{
		$replace_fields = false;

		if ($admin_forms_id)
		{
			$replace_fields = $this->GetAllAdminFormFields($admin_forms_id);
		}

		if ($field_list)
		{
			$replace_fields = $field_list;
		}

		// Делаем внешние подстановки, если указаны.
		reset($this->external_replaces);

		if ($this->external_replaces)
		{
			foreach ($this->external_replaces as $replace_key => $replace_value)
			{
				$subject = str_replace($replace_key, $replace_value, $subject);
			}
		}

		if (!$replace_fields)
		{
			return $subject;
		}

		// Делаем подстановки полей.
		reset($replace_fields);

		foreach ($replace_fields as $replace_key => $replace_value)
		{
			$subject = str_replace('{'.$replace_key.'}', $replace_value, $subject);
		}

		return $subject;
	}

	function CreateMainMenu($menu, $field_list = false, $first_level = true, $menu_id = false)
	{
		$menu = Core_Type_Conversion::toArray($menu);
		$i = 0;
		if ($first_level)
		{
			$level = 1;
		}
		else
		{
			$level = 0;
		}

		if ($menu_id === false)
		{
			// Уникальный ID меню не установлен, генерируем случайное число.
			$menu_id = rand(1, 999999);
		}

		if (!$first_level)
		{
			echo '<div id="id_'.$menu_id.'" class="shadowed" style="display: none;"><div class="tl"></div><div class="t"></div><div class="tr"></div><div class="l"></div><div class="r"></div><div class="bl"></div><div class="b"></div><div class="br"></div><ul>';
		}
		else
		{
			echo '<table cellpadding="0" cellspacing="0" border="0" class="main_ul"><tr>';
			//echo '<div border="0" class="main_ul">';
		}

		foreach ($menu as $item)
		{
			$li_id = "id_menu_item_$menu_id"."_$i";

			$menu_id++;
			$show_sub_items_script = 'OnMouseOver="HostCMSMenuOver(\''.$li_id.'\','.$level.", '".(isset($item['sub_items']) ? 'id_'.($menu_id) : '').'\');" OnMouseOut="HostCMSMenuOut(\''.$li_id.'\','.$level.", '".(isset($item['sub_items']) ? 'id_'.($menu_id) : '').'\');"';

			if (!$first_level)
			{
				//echo '<li id="'.$li_id.'" '.$show_sub_items_script.'>';
				echo '<li id="'.$li_id.'">';
			}
			else
			{
				echo '<td valign="bottom" id="'.$li_id.'" '.$show_sub_items_script.' class="li_lev_1">';
			}

			$link = trim(Core_Type_Conversion::toStr($item['link']));

			if ($link)
			{
				$link = $this->DoReplaces(false, $field_list, $link);

				$onclick = $this->DoReplaces(false, $field_list, trim(Core_Type_Conversion::toStr($item['onclick'])));

				echo '<a href="'.$link.'" onclick="'.$onclick.'">';
			}
			else
			{
				echo '<span>';
			}

			$icon = trim(Core_Type_Conversion::toStr($item['icon']));
			if (!empty($icon))
			{
				$icon = '<img align="absmiddle" src="'.htmlspecialchars($icon).'" />';
			}

			echo $icon;
			echo htmlspecialchars(Core_Type_Conversion::toStr($item['name']));
			if ($link)
			{
				echo '</a>';
			}
			else
			{
				echo '</span>';
			}

			if (isset($item['sub_items']))
			{
				$this->CreateMainMenu($item['sub_items'], $field_list, false, $menu_id);
			}
			if (!$first_level)
			{
				echo '</li>';
			}
			else
			{
				echo '</td>';
			}
			$i++;
		}
		if (!$first_level)
		{
			echo '</ul></div>';
		}
		else
		{
			echo '</tr></table>';
		}
	}

	/**
	* Вставка/обновление информации о языке
	*
	* @param array $param массив параметров
	* $param['admin_language_id'] идентификатор обновляемого языка
	* $param['admin_language_name'] название языка
	* $param['admin_language_active'] параметр, определяющий доступен язык или нет (1 - доступен (по умолчанию), 0 - не доступен )
	* $param['admin_language_order'] порядковый номер языка в списке языков
	* $param['admin_language_short_name'] короткое обозначение языка
	*
	* @return mixed идентификатор вставленного/обновленного языка в случае успешного завершения, false - в противном случае
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $param['admin_language_name'] = 'Французский';
	* $param['admin_language_active'] = 0;
	* $param['admin_language_order'] = 30;
	* $param['admin_language_short_name'] = 'fr';
	*
	* $newid = $admin_forms->AddLanguage($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function AddLanguage($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['admin_language_id']) || $param['admin_language_id'] == 0)
		{
			$param['admin_language_id'] = NULL;
		}

		$oAdmin_Language = Core_Entity::factory('Admin_Language', $param['admin_language_id']);

		if (isset($param['admin_language_name']))
		{
			$oAdmin_Language->name = Core_Type_Conversion::toStr($param['admin_language_name']);
		}

		if (isset($param['admin_language_active']))
		{
			$oAdmin_Language->active = Core_Type_Conversion::toInt($param['admin_language_active']);
		}

		if (isset($param['admin_language_order']))
		{
			$oAdmin_Language->sorting = Core_Type_Conversion::toInt($param['admin_language_order']);
		}

		if (isset($param['admin_language_short_name']))
		{
			$oAdmin_Language->shortname = Core_Type_Conversion::toStr($param['admin_language_short_name']);
		}

		if (is_null($param['admin_language_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oAdmin_Language->user_id = $param['users_id'];
		}

		$oAdmin_Language->save();

		return $oAdmin_Language->id;
	}

	/**
	* Получение информации о языке центра администрирования
	*
	* @param int $admin_language_id идентификатор языка
	*
	* @return mixed ассоциативный массив с даными о языке в случае успешного выполнения, false - в противном случае
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_language_id = 1;
	*
	* $row = $admin_forms->GetLanguage($admin_language_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetLanguage($admin_language_id)
	{
		$admin_language_id = Core_Type_Conversion::toInt($admin_language_id);

		// Если есть в кэше
		if (isset($this->CacheGetLanguage[$admin_language_id]))
		{
			return $this->CacheGetLanguage[$admin_language_id];
		}

		$oAdmin_Language = Core_Entity::factory('Admin_Language', $admin_language_id);

		if (!is_null($oAdmin_Language->name))
		{
			$return = $this->GetArrayByAdmin_Language($oAdmin_Language);

			// Кэшируем
			$this->CacheGetLanguage[$admin_language_id] = $return;

			return $return;
		}

		return FALSE;
	}

	function GetArrayByAdmin_Language($oAdmin_Language)
	{
		return array(
				'admin_language_id' => $oAdmin_Language->id,
				'admin_language_order' => $oAdmin_Language->sorting,
				'admin_language_name' => $oAdmin_Language->name,
				'admin_language_active' => $oAdmin_Language->active,
				'admin_language_short_name' => $oAdmin_Language->shortname,
				'users_id' => $oAdmin_Language->user_id
			);
	}

	/**
	* Получение информации о языке центра администрирования по его обозначени.
	*
	* @param int $admin_language_id идентификатор языка
	*
	* @return mixed ассоциативный массив с даными о языке в случае успешного выполнения, false - в противном случае
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_language_short_name = 'ru';
	*
	* $row = $admin_forms->GetLanguageByShortName($admin_language_short_name);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetLanguageByShortName($admin_language_short_name)
	{
		// Если есть в кэше
		if (isset($this->CacheGetLanguageByShortName[$admin_language_short_name]))
		{
			return $this->CacheGetLanguageByShortName[$admin_language_short_name];
		}

		$oAdmin_Language = Core_Entity::factory('Admin_Language')
			->getByShortname($admin_language_short_name);

		if (!is_null($oAdmin_Language->name))
		{
			$return = $this->GetArrayByAdmin_Language($oAdmin_Language);

			// Кэшируем
			$this->CacheGetLanguageByShortName[$admin_language_short_name] = $return;

			return $return;
		}

		return FALSE;
	}

	/**
	* Извлечение всех языков
	*
	* @return array со списком языков или false
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $rows = $admin_forms->GetAllLanguages();
	*
	* // Распечатаем результат
	* print_r ($rows);
	* ?>
	* </code>
	*/
	function GetAllLanguages()
	{
		$oAdmin_Languages = Core_Entity::factory('Admin_Language')->findAll();

		$result = false;

		foreach ($oAdmin_Languages as $oAdmin_Language)
		{
			$result[] = $this->GetArrayByAdmin_Language($oAdmin_Language);
		}

		return $result;
	}

	/**
	* Удаление языка центра администрирования
	*
	* @param int $admin_language_id идентификатор удаляемого языка центра администрирования
	* @return boolean в случае успешного удаления, false - в противном случае
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_language_id = 100000;
	*
	* $result = $admin_forms->DeleteAdminLanguage($admin_language_id);
	*
	* if ($result)
	* {
	* 	echo "Удаление выполнено успешно";
	* }
	* else
	* {
	* 	echo "Ошибка удаления";
	* }
	* ?>
	* </code>
	*/
	function DeleteAdminLanguage($admin_language_id)
	{
		$admin_language_id = intval($admin_language_id);
		Core_Entity::factory('Admin_Language', $admin_language_id)->markDeleted();
		return TRUE;
	}

	/**
	* Извлечения списка слов определенного языка
	*
	* @param int $admin_language_id идентификатор языка
	* @return array массив с данными о словах
	* <code>
	* <?php
	* $admin_forms = new admin_forms();
	*
	* $admin_language_id = 1;
	*
	* $row = $admin_forms->GetAllWordsByLanguage($admin_language_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetAllWordsByLanguage($admin_language_id = false)
	{
		$admin_language_id = intval($admin_language_id);

		$aAdmin_Word_Values = Core_Entity::factory('Admin_Language', $admin_language_id)
			->admin_word_values
			->findAll();

		foreach ($aAdmin_Word_Values as $oAdmin_Word_Value)
		{
			$result[] = array(
				'admin_words_value_id' => $oAdmin_Word_Value->id,
				'admin_words_id' => $oAdmin_Word_Value->admin_word_id,
				'admin_language_id' => $oAdmin_Word_Value->admin_language_id,
				'admin_words_value_name' => $oAdmin_Word_Value->name,
				'admin_words_value_description' => $oAdmin_Word_Value->description
			);
		}

		return $result;
	}

	/**
	* Вставка/обновление слова
	*
	* @param array $param массив параметров
	* - int $param['admin_words_id'] идентификатор слова
	* - int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь
	* @return int идентификатор вставленного/обновленного слова
	* @see InsertWordsValue()
	*/
	function InsertWord($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['admin_words_id']) || $param['admin_words_id'] == 0)
		{
			$param['admin_words_id'] = NULL;
		}

		$oAdmin_Word = Core_Entity::factory('Admin_Word', $param['admin_words_id']);

		if (is_null($param['admin_words_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oAdmin_Word->user_id = $param['users_id'];
		}

		$oAdmin_Word->save();

		return $oAdmin_Word->id;

	}

	/**
	* Вставка информации о значении слова
	*
	* @param array $param параметры
	* - int $param['admin_words_value_id'] идентификатор значения слова
	* - int $param['admin_words_id'] идентификатор слова (обязательный параметр)
	* - int $param['admin_language_id'] идентификатор языка (обязательный параметр)
	* - string $param['admin_words_value_name'] имя на указанном языке
	* - string $param['admin_words_value_description'] описание на указанном языке
	* @return int идентификатор вставленного значения слова
	* @see InsertWord()
	*/
	function InsertWordsValue($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['admin_words_value_id']) || $param['admin_words_value_id'] == 0)
		{
			$param['admin_words_value_id'] = NULL;
		}

		$oAdmin_Word_Value = Core_Entity::factory('Admin_Word_Value', $param['admin_words_value_id']);

		$oAdmin_Word_Value->admin_word_id = Core_Type_Conversion::toInt($param['admin_words_id']);
		$oAdmin_Word_Value->admin_language_id = Core_Type_Conversion::toInt($param['admin_language_id']);

		if (isset($param['admin_words_value_name']))
		{
			$oAdmin_Word_Value->name = Core_Type_Conversion::toStr($param['admin_words_value_name']);
		}

		if (isset($param['admin_words_value_description']))
		{
			$oAdmin_Word_Value->description = Core_Type_Conversion::toStr($param['admin_words_value_description']);
		}

		if (is_null($param['admin_language_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oAdmin_Word_Value->user_id = $param['users_id'];
		}

		$oAdmin_Word_Value->save();

		return $oAdmin_Word_Value->id;
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
	function ApplyFormat($str, $format)
	{
		if (!empty($format))
		{
			$str = sprintf($format, $str);
		}

		return $str;
	}
}