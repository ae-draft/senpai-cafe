function function_exists(function_name){
	if (typeof function_name == 'string'){
		return (typeof window[function_name] == 'function');
	}
	else{
		return (function_name instanceof Function);
	}
}

// Установка или снятие всех флажков для checkbox'ов элементов.
function SelectAllItems(WindowId, checked)
{
	WindowId = getWindowId(WindowId);
	$("#"+WindowId).highlightAllRows(checked);
}

// Функция выделят строку таблицы
// RowId - идентификатор строки
function RowHighlight(WindowId, RowId)
{
	WindowId = getWindowId(WindowId);
	$('#' + WindowId + ' #' + RowId).toggleHighlight();
}

// Функция проверяет, установлены ли все checkbox'ы, если уcтановлены - устанавливает и главный,
// если не установлены, снимает флажок с главного.
function SetTopCheckbox(WindowId)
{
	WindowId = getWindowId(WindowId);
	$("#"+WindowId).setTopCheckbox();
}

// Функция устанавливает checkbox редактирования у указанного элемента
function DoModification(checkboxId){
	$.setCheckbox('id_content', checkboxId);
}

function DisableTinyMCE()
{
	// Работу с визуальным редактором ведём
	if (typeof tinyMCE != 'undefined')
	{
		var textarea_array = document.getElementsByTagName("textarea");

		for (var i = 0; i < textarea_array.length; i++)
		{
			var elementId = textarea_array[i].id;

			if (tinyMCE.getInstanceById(elementId) != null)
			{
				textarea_array[i].disabled = true;
				tinyMCE.execCommand('mceRemoveControl', false, elementId);
			}
		}
	}

	// Отключаем все редакторы CodePress, сохраняем код в textarea
	//CodePressAction('disable');
}


// Функция выполняет событие, убирает выделение всех checkbox'ов кроме нужного.
// AAction - относительный адрес файла, который будет запрошен
// AAdditionalParams - внешние переметры, передаваемые в строку запроса. Должны начинаться с &
// AOperation - название события
// AItemName - кодовое имя элемента, над которым производится действие
// AAdminFromsId - идентификатор формы
// ALimit - текущая страница
// AOnPage - число элементов на страницу
// AOrderFieldId - ID поля, по которому идет сортировка
// AOrderDirection - направление сортировки, 1 - по возрастанию, 2 - по убыванию
function TrigerSingleAction(AAction, AAdditionalParams, AOperation, AItemName, AAdminFromsId, ALimit, AOnPage, AOrderFieldId, AOrderDirection, WindowId)
{
	WindowId = getWindowId(WindowId);

	var ElementID = 'id_' + AItemName;

	cbItem = $("#"+WindowId+" #"+ElementID);

	if (cbItem.length > 0)
	{
		// Uncheck all checkboxes with name like 'check_'
		$("#"+WindowId+" input[type='checkbox'][id^='check_']:not([name*='_fv_'])").prop('checked', false);

		// Check checkbox
		cbItem.prop('checked', true);
	}
	else
	{
		var Check_0_0 = $('<input>')
			.prop('type', 'checkbox')
			.prop('name', AItemName)
			.prop('id', ElementID);

		$('<div>')
			.prop("style", 'display: none')
			.append(Check_0_0)
			.appendTo($("#"+WindowId));

		// After insert into DOM
		Check_0_0.prop('checked', true);
	}

	SetTopCheckbox(WindowId);

	// Если для действия был указан 0, то устанавливаем в false, чтобы не передавать явно limit в DoLoadAjax()
	if (ALimit == 0)
	{
		ALimit = false;
	}

	DoLoadAjax(AAction, AAdditionalParams, AAdminFromsId, AOperation, ALimit, AOnPage, AOrderFieldId, AOrderDirection, WindowId);
}

// Вызов групповой операции.
// AAction - относительный адрес файла, который будет запрошен
// AAdditionalParams - внешние переметры, передаваемые в строку запроса. Должны начинаться с &
function CallGroupOperation(AAction, AAdditionalParams, AAdminFromsId, ALimit, AOnPage, AOrderFieldId,
AOrderDirection)
{
	cbGroupOperation = document.getElementById('id_admin_forms_group_operation');
	if (cbGroupOperation)
	{
		sOperation = cbGroupOperation.options[cbGroupOperation.selectedIndex].value;
		if (sOperation != '')
		{
			// Вызываем тригер операции.
			DoLoadAjax(AAction, AAdditionalParams, AAdminFromsId, sOperation, ALimit, AOnPage, AOrderFieldId, AOrderDirection);

			return true;
		}
	}
	return false;
}


// выполняет скрипты из полученного ответа от сервера
function runScripts(scripts)
{
	if (!scripts)
	{
		return false;
	}

    var thisScript, text;

	for (var i = 0; i < scripts.length; i++)
	{
		thisScript = scripts[i];

		if (thisScript.src)
		{
			var newScript = document.createElement("script");
			newScript.type = thisScript.type;
			newScript.language = thisScript.language;

			newScript.src = thisScript.src;
			document.getElementsByTagName('head')[0].appendChild(newScript);
		}
		else if (text = (thisScript.text || thisScript.innerHTML))
		{
			var text = (""+text).replace(/^\s*<!\-\-/, '').replace(/\-\->\s*$/, '');

			var newScript = document.createElement("script");
			newScript.setAttribute("type", "text/javascript");
			newScript.text = text;

			var script_node = document.getElementsByTagName('head')[0].appendChild(newScript);
		}
	}
}

function doSendForm(AAction, AAdditionalParams, ButtonObject, AAdminFromsId, AOperation, ALimit, AOnPage, WindowId)
{
	return SendForm('', AAction, AAdditionalParams, ButtonObject, AAdminFromsId, AOperation, ALimit, AOnPage, WindowId);
}

// Отправка формы методом Get или Post
// AAction - относительный адрес файла, который будет запрошен
// AAdditionalParams - внешние переметры, передаваемые в строку запроса. Должны начинаться с &
// ButtonObject - Объект нажатой кнопки
// AAdminFromsId - идентификатор формы центра администрирования
function SendForm(WindowId, AAction, AAdditionalParams, ButtonObject, AAdminFromsId, AOperation, ALimit, AOnPage)
{
	WindowId = getWindowId(WindowId);

	var FormNode = $(ButtonObject).closest('form');

	// Пытаемся получить скрытый объект для input-а
	var jHiddenInput = $("#" + WindowId + " #"+ButtonObject.name);

	// Элемента нет, добавим его
	if (jHiddenInput.length == 0)
	{
		// Создадим скрытй input, т.к. нажатый не передается в форму
		FormNode.append($('<input>')
				.prop('type', 'hidden')
				.prop('name', ButtonObject.name)
				.prop('id', ButtonObject.name));
	}

	// Сохраним из визуальных редакторов данные
	if (typeof tinyMCE != 'undefined')
	{
		tinyMCE.triggerSave();
	}

	//CodePressAction('save');

	var JsHttpRequestSendForm = new JsHttpRequest();

	// Код вызывается, когда загрузка завершена
	JsHttpRequestSendForm.onreadystatechange = function ()
	{
		if (JsHttpRequestSendForm.readyState == 4)
		{
			// Убираем затемнение.
			$.loadingScreen('hide');

			if (typeof JsHttpRequestSendForm.responseJS != 'undefined')
			{
				// Данные записываем только тогда, если они есть и не пустые.
				if (typeof JsHttpRequestSendForm.responseJS.form_html != 'undefined'
					&& JsHttpRequestSendForm.responseJS.form_html != '')
				{
					// Устанавливаем путь для возможности в браузере ходить вперед-назад
					AjaxSetLocation(FormAction);

					// Отключаем связь с редакторами
					DisableTinyMCE();

					var div_id_content = $("#"+WindowId);
					$.insertContent(div_id_content, JsHttpRequestSendForm.responseJS.form_html);
					$.afterContentLoad(div_id_content);
				}

				if (typeof JsHttpRequestSendForm.responseJS.error != 'undefined')
				{
					var div_id_message = $("#"+WindowId+" #id_message");

					if (div_id_message.length == 1)
					{
						$.insertContent(div_id_message, JsHttpRequestSendForm.responseJS.error);
					}
				}

				// Title.
				if (typeof JsHttpRequestSendForm.responseJS.title != 'undefined' && JsHttpRequestSendForm.responseJS.title != '')
				{
					document.title = JsHttpRequestSendForm.responseJS.title;
				}
			}

			return true;
		}
	}

	var FormAction, FormMethod, sOnPage = '', ALimit = '';

	FormAction = FormNode.prop('action');
	FormMethod = FormNode.prop('method');

	if (AOnPage)
	{
		sOnPage = '&admin_forms_on_page=' + AOnPage;
	}

	// Текущая страница.
	if (ALimit != 0)
	{
		ALimit = '&limit=' + ALimit;
	}

	// передача параметров AAdditionalParams сделана явно, а не через hostcmsAAdditionalParams
	FormAction += (FormAction.indexOf('?') >= 0 ? '&' : '?') + 'hostcmsAAction=' + encodeURIComponent(AAction) +
	'&hostcmsAAdditionalParams=' + encodeURIComponent(AAdditionalParams) + AAdditionalParams +
	'&operation=' + AOperation + ALimit + sOnPage;

	FormAction += '&window_id=' + WindowId;

	JsHttpRequestSendForm.open(FormMethod, FormAction, true);

	// Отправляем запрос в backend.
	JsHttpRequestSendForm.send( { query: FormNode.get() } );

	// Очистим поле для сообщений
	$("#"+WindowId+" #id_message").empty();

	// Отображаем экран загрузки
	$.loadingScreen('show');

	return false;
}

// action - адрес страницы для запрос
// method - GET, POST, null - автоматическое определение
// callback_function - функция обратного вызова, которая будет вызвана после получения ответа от backenad-а
function sendRequest(action, method, callback_function)
{
	var req = new JsHttpRequest();

	// Отображаем экран загрузки
	$.loadingScreen('show');

	// Этот код вызовется автоматически, когда загрузка данных завершится.
	req.onreadystatechange = function()
	{
		if (req.readyState == 4)
		{
			// Убираем затемнение.
			$.loadingScreen('hide');

			if (typeof callback_function != 'undefined')
			{
				callback_function(req.responseJS);
			}

			return true;
		}
	}

	req.open(method, action, true);

	// Отсылаем данные в обработчик.
	req.send(null);
}

// Загрузка формы
// AAction - относительный адрес файла, который будет запрошен
// AAdditionalParams - внешние переметры, передаваемые в строку запроса. Должны начинаться с &
// AAdminFromsId - идентификатор формы центра администрирования
// AOperation - имя события
// ALimit - текущая страница, false - не отправлять страницу
// AOnPage - число элементов на страницу
// AOrderFieldId - ID поля, по которому идет сортировка
// AOrderDirection - направление сортировки, 1 - по возрастанию, 2 - по убыванию
function DoLoadAjax(AAction, AAdditionalParams, AAdminFromsId, AOperation, ALimit, AOnPage, AOrderFieldId, AOrderDirection, WindowId)
{
	WindowId = getWindowId(WindowId);

	if (AOperation == '')
	{
		return false;
	}

	if (AAdditionalParams == ' ')
	{
		AAdditionalParams = '';
	}

	var element_name, sOrder = '', sOnPage = '',
		sElements = '', sFilter = '';

	// Если поле сортировки было указано - передадим поле и направление сортировки
	if (AOrderFieldId != 0)
	{
		sOrder = '&order_field_id=' + AOrderFieldId + '&order_field_direction=' + AOrderDirection;
	}

	if (AOnPage)
	{
		sOnPage = '&admin_forms_on_page=' + AOnPage;
	}

	// Элементы списка
	var jChekedItems = $("#"+WindowId+" :input[type='checkbox'][id^='check_']:checked"),
		iChekedItemsCount = jChekedItems.length,
		jItemsValue, iItemsValueCount, sValue;

	for (var jChekedItem, i=0; i < iChekedItemsCount; i++)
	{
		jChekedItem = jChekedItems.eq(i);
		element_name = jChekedItem.prop('name');

		sElements += '&' + element_name + '=1';

		// Ищем значения записей, имя поля должно начинаться с имени checkbox-а
		jItemsValue = $("#"+WindowId+" :input[name^='"+element_name+"_fv_']"),
		iItemsValueCount = jItemsValue.length;

		for (var jValueItem, k=0; k < iItemsValueCount; k++)
		{
			jValueItem = jItemsValue.eq(k);

			if (jValueItem.prop("type") == 'checkbox')
			{
				sValue = jValueItem.prop('checked') ? '1' : '0';
			}
			else
			{
				sValue = jValueItem.val();
			}

			sElements += '&' + jValueItem.prop('name') + '=' + sValue;
		}
	}

	// Фильтр
	var jFiltersItems = $("#"+WindowId+" :input[name^='admin_form_filter_']"),
		iFiltersItemsCount = jFiltersItems.length;

	for (var jFiltersItem, i=0; i < iFiltersItemsCount; i++)
	{
		jFiltersItem = jFiltersItems.eq(i);

		// Если значение фильтра до 255 символов
		if (jFiltersItem.val().length < 256)
		{
			// Дописываем к передаваемым данным
			sFilter += '&' + jFiltersItem.prop('name') + '=' + encodeURIComponent(jFiltersItem.val());
		}
	}

	// Текущая страница.
	//if (ALimit == 0)
	if (ALimit === false)
	{
		ALimit = '';
	}
	else
	{
		ALimit = '&limit=' + ALimit;
	}

	cmsrequest = AAction + '?admin_forms_id=' + AAdminFromsId +
	'&hostcmsAAction=' + encodeURIComponent(AAction) +
	'&hostcmsAAdditionalParams=' + encodeURIComponent(AAdditionalParams) +
	'&operation=' + AOperation + ALimit + sOnPage + sFilter +
	sElements + sOrder + AAdditionalParams;

	if (cmsrequest.length < 2000)
	{
		method = 'get';
	}
	else
	{
		method = 'post';
	}

	// Отправляем запрос backend-у
	sendRequest(cmsrequest, method, callbackfunction_DoLoadAjax);
}

// Функция обратного вызова для DoLoadAjax
function callbackfunction_DoLoadAjax(responseJS)
{
	// Результат принят
	//sended_request = false;

	if (responseJS != null)
	{
		// Данные.
		if (typeof responseJS.form_html != 'undefined')
		{
			if (typeof cmsrequest != 'undefined')
			{
				// Устанавливаем путь для возможности в браузере ходить вперед-назад
				AjaxSetLocation(cmsrequest);
			}

			// Отключаем связь с редакторами
			DisableTinyMCE();

			if (responseJS.form_html != '')
			{
				var div_id_content = $("#id_content");

				// Занесем текст
				div_id_content.empty().html(responseJS.form_html);
				$.afterContentLoad(div_id_content);
			}

			// Сбрасываем cmsrequest
			cmsrequest = '';
		}

		if (typeof responseJS.error != 'undefined')
		{
			var div_id_message = $("#id_message");

			if (div_id_message.length == 1)
			{
				div_id_message.empty().html(responseJS.error);
			}
		}

		// Title.
		if (typeof responseJS.title != 'undefined' && responseJS.title != '')
		{
			document.title = responseJS.title;
		}

		// Редирект.
		if (typeof responseJS.redirect != 'undefined')
		{
			if (responseJS.redirect != '')
			{
				$.loadingScreen('show');
				location = responseJS.redirect;
			}
		}
	}
}

// Функция обратного вызова, вызываемая при получении ответа на удаление изображений
function callback_function_exec_from_return(responseJS)
{
	// Выполняем присланные данные
	if (typeof responseJS != 'undefined')
	{
		// Данные.
		if (typeof responseJS.result != 'undefined')
		{
			var temp_div = document.createElement('div');
			temp_div.style.display = 'none';
			temp_div.innerHTML = responseJS.result;

			// Выполняем скрипты из полученного с сервера HTML-а
			runScripts(temp_div.getElementsByTagName('SCRIPT'));
		}
	}
}

function AddLoadFileField(container_id, input_prefix)
{
	cbItem = document.getElementById(container_id);

	if (cbItem)
	{
		// Получаем все input-ы
		element_array = cbItem.getElementsByTagName("input");

		count_input = element_array.length;

		// <br/>
		var ElementBr = document.createElement("br");
		cbItem.appendChild(ElementBr);

		//<input
		var ElementInput = document.createElement("input");
		ElementInput.setAttribute("size", "60");
		ElementInput.setAttribute("name", input_prefix + count_input);
		ElementInput.setAttribute("type", "file");
		ElementInput.setAttribute("title", "Прикрепить файл");
		ElementInput.setAttribute("style", "margin-bottom: 20px");
		cbItem.appendChild(ElementInput);
	}
}

//показ окна редактирования
function ShowEditWindow(caption, path, name)
{
	var oWindowId = 'edit_window_'+name;

	if ($('#'+oWindowId).length == 0)
	{
		// Создаем окно
		CreateWindow(oWindowId, caption, '90%', '90%');

		var oWindow = document.getElementById(oWindowId);

		// <div id="subdiv">
		var ElementDiv = document.createElement("div");
		ElementDiv.setAttribute("id", "subdiv");
		var SubDiv = oWindow.appendChild(ElementDiv);

		var DivMessage = document.createElement("div");
		DivMessage.setAttribute("id", "id_message");
		var oDivMessage = SubDiv.appendChild(DivMessage);

		var DivContent = document.createElement("div");
		DivContent.setAttribute("id", "id_content");
		var oDivContent = SubDiv.appendChild(DivContent);

		// Запрос backend-у
		var req = new JsHttpRequest();

		// Отображаем экран загрузки
		$.loadingScreen('show');

		req.onreadystatechange = function()
		{
			if (req.readyState == 4)
			{
				// Убираем затемнение.
				$.loadingScreen('hide');

				if (req.responseJS != undefined)
				{
					// Сообщение.
					// Выводим результат ошибки в переменную.
					if (typeof req.responseJS.error != 'undefined')
					{
						if (oDivMessage)
						{
							// Создадим скрытый SPAN для IE, в который поместим текст + скрипт.
							// Если перед <script> не будет текста, нехороший IE не увидит SCRIPT
							var span = document.createElement("span");
							span.style.display = 'none';
							span.innerHTML = "Stupid IE. " + req.responseJS.error;

							runScripts(span.getElementsByTagName('SCRIPT'));

							// Занесем текст сообщения только после выполнения скрипта
							oDivMessage.innerHTML = req.responseJS.error;
						}
					}

					// Данные записываем только тогда, если они есть и не пустые.
					if (typeof req.responseJS.form_html != 'undefined' && req.responseJS.form_html != '')
					{
						cmsrequest = path;

						// Занесем текст сообщения ДО выполнения скрипта
						oDivContent.innerHTML = req.responseJS.form_html;

						// Создадим скрытый SPAN для IE, в который поместим текст + скрипт.
						// Если перед <script> не будет текста, нехороший IE не увидит SCRIPT
						var span = document.createElement("span");
						span.style.display = 'none';
						span.innerHTML = "Stupid IE. " + req.responseJS.form_html;

						runScripts(span.getElementsByTagName('SCRIPT'));
					}
				}
				return true;
			}
		}

		req.open('get', path, true);

		// Отсылаем данные в обработчик.
		req.send(null);
	}
	else
	{
		// Отключаем связь с редакторами
		DisableTinyMCE();
		oDivMessage = document.getElementById("id_message");
		oDivMessage.innerHTML = '';
	}

	SlideWindow(oWindowId);
}


// Скрипт для заполнения списка свойств
function DoLoadLibProperties(ALibId, structure_id)
{
	// Этот код вызовется автоматически, когда загрузка данных завершится.
	function callbackfunction(responseJS)
	{
		if (typeof responseJS != 'undefined')
		{
			// Выводим результат в переменную.
			html = responseJS.lib_properties_html;

			if (responseJS.java_script != '')
			{
				eval(responseJS.java_script);
			}
		}
		else
		{
			html = '';
		}

		document.getElementById('lib_properties').innerHTML = html;
	}

	// Отправляем запрос backend-у
	sendRequest('/admin/structure/structure.php?ajax_structure=1&get_lib_properties_id=' + ALibId +
	'&structure_id=' + structure_id, 'get', callbackfunction);
}

function SetViewField(ASelectedItem)
{
	var oPercent = document.getElementById("affiliate_values_percent_id");
	var oValue = document.getElementById("affiliate_values_value_id");

	if (oPercent != 'undefined' && oValue != 'undefined')
	{
		oPercent.style.display = "none";
		oValue.style.display = "none";

		switch (ASelectedItem)
		{
			case 0: // Процент.
			{
				oPercent.style.display = '';
				break;
			}
			case 1: // Сумма.
			{
				oValue.style.display = '';
				break;
			}
		}
	}
}

function ChangeXslImageLink(lib_property_id, xsl_id, xsl_dir_id)
{
	if (xsl_id > 0)
	{
		link_path = "/admin/xsl/xsl.php?admin_forms_id=22&operation=edit_xsl_dir&check_1_" + xsl_id+ "=1&xsl_dir_parent_id=" + xsl_dir_id;

		document.getElementById('xsl_link_image_id_' + lib_property_id).setAttribute('href', link_path);
		document.getElementById('xsl_link_image_id_' + lib_property_id).style.display = "";
 	}
	else
 	{
 		document.getElementById('xsl_link_image_id_' + lib_property_id).setAttribute('href', 'javascript:void(0)');
 		document.getElementById('xsl_link_image_id_' + lib_property_id).style.display = "none";
 	}
}

function DoLoadXSL(AXslName, AXslDirId, APropertyId)
{
	// Этот код вызовется автоматически, когда загрузка данных завершится.
	function callbackfunction(responseJS)
	{
		if (typeof responseJS != 'undefined')
		{
			var div = $("#id_xls_tamplate_" + APropertyId);
			div.empty();
			div.html(responseJS.property_xsl_html);
		}
	}

	sendRequest('/admin/structure/structure.php?ajax_structure=1&get_xsl=' + encodeURIComponent(AXslName) +
	'&property_id=' + APropertyId + '&xsl_dir_id=' + AXslDirId, 'get', callbackfunction);
}

function DoLoadLibs(ALibDirId, ALibId)
{
	// Этот код вызовется автоматически, когда загрузка данных завершится.
	function callbackfunction(responseJS)
	{
		if (typeof responseJS != 'undefined')
		{
			// Выводим результат в переменную.
			html = responseJS.lib_html;
		}
		else
		{
			html = '';
		}

		document.getElementById('id_lib_id').innerHTML = html;
	}

	// Отправляем запрос backend-у
	sendRequest('/admin/structure/structure.php?ajax_structure=1&get_libs=1&lib_dir_id=' + ALibDirId +
	'&lib_id=' + ALibId, 'get', callbackfunction);
}

/**
* Добавление полей для спец.цены
* container_id - div, в который добавляются элементы
*/
function AddSpecialPricesFields(container_id)
{
	cbItem = document.getElementById(container_id);

	if (cbItem)
	{
		// Получаем все input-ы
		element_array = cbItem.getElementsByTagName("input");

		count_input = element_array.length / 4 + 1;

		// <div style="clear: both;"/>
		var element_div = document.createElement("div");
		element_div.setAttribute("style", "clear: both;");
		cbItem.appendChild(element_div);

		// Создаем div
		var element_div = document.createElement("div");
		element_div.setAttribute("class", "item_div");
		element_div.setAttribute("style", "float: left;");
		cbItem.appendChild(element_div);

		var element_span = document.createElement("span");
		element_span.setAttribute("class", "caption");
		element_div.appendChild(element_span);

		var element_acronym = document.createElement("acronym");
		element_acronym.setAttribute("title", "Минимальное количество товара, которое нужно купить за один раз, чтобы задействовать цену");
		element_acronym.innerHTML = "Количество товара от";
		element_span.appendChild(element_acronym);

		//<input Количество товара от
		var ElementInput = document.createElement("input");
		ElementInput.setAttribute("id", "shop_special_prices_from_" + count_input);
		ElementInput.setAttribute("type", "text");
		ElementInput.setAttribute("onblur", "FieldCheck(this)");
		ElementInput.setAttribute("onkeyup", "FieldCheck(this)");
		ElementInput.setAttribute("onkeydown", "FieldCheck(this)");
		ElementInput.setAttribute("value", "");
		ElementInput.setAttribute("name", "shop_special_prices_from_js_" + count_input);
		ElementInput.setAttribute("style", "width: 120px; background-image: url(/admin/images/bullet_green.gif); background-position: right center; background-repeat: no-repeat;");
		element_div.appendChild(ElementInput);

		fieldType["shop_special_prices_from_" + count_input] = {'maxlen' : 11,
		'reg' : /^[0-9]*$/};
		fieldMessage["shop_special_prices_from_" + count_input] = {'reg' : "Значение поля не соответствует формату."};

		//<div id="_error" class="div_message_error"/>
		var element_div_error = document.createElement("div");
		element_div_error.setAttribute("class", "div_message_error");
		element_div_error.setAttribute("id", "shop_special_prices_from_" + count_input + "_error");
		element_div.appendChild(element_div_error);

		// Создаем div
		var element_div = document.createElement("div");
		element_div.setAttribute("class", "item_div");
		element_div.setAttribute("style", "float: left;");
		cbItem.appendChild(element_div);

		var element_span = document.createElement("span");
		element_span.setAttribute("class", "caption");
		element_div.appendChild(element_span);

		var element_acronym = document.createElement("acronym");
		element_acronym.setAttribute("title", "Максимальное количество товара, которое нужно купить за один раз, чтобы задействовать цену");
		element_acronym.innerHTML = "Количество товара до";
		element_span.appendChild(element_acronym);

		//<input Количество товара до
		var ElementInput = document.createElement("input");
		ElementInput.setAttribute("id", "shop_special_prices_to_" + count_input);
		ElementInput.setAttribute("type", "text");
		ElementInput.setAttribute("onblur", "FieldCheck(this)");
		ElementInput.setAttribute("onkeyup", "FieldCheck(this)");
		ElementInput.setAttribute("onkeydown", "FieldCheck(this)");
		ElementInput.setAttribute("value", "");
		ElementInput.setAttribute("name", "shop_special_prices_to_js_" + count_input);
		ElementInput.setAttribute("style", "width: 120px; background-image: url(/admin/images/bullet_green.gif); background-position: right center; background-repeat: no-repeat;");
		element_div.appendChild(ElementInput);

		fieldType["shop_special_prices_to_" + count_input] = {'maxlen' : 11,
		'reg' : /^[0-9]*$/};
		fieldMessage["shop_special_prices_to_" + count_input] = {'reg' : "Значение поля не соответствует формату."};

		//<div id="_error" class="div_message_error"/>
		var element_div_error = document.createElement("div");
		element_div_error.setAttribute("class", "div_message_error");
		element_div_error.setAttribute("id", "shop_special_prices_to_" + count_input + "_error");
		element_div.appendChild(element_div_error);

		// Создаем div
		var element_div = document.createElement("div");
		element_div.setAttribute("class", "item_div");
		element_div.setAttribute("style", "float: left;");
		cbItem.appendChild(element_div);

		var element_span = document.createElement("span");
		element_span.setAttribute("class", "caption");
		element_div.appendChild(element_span);

		var element_acronym = document.createElement("acronym");
		element_acronym.setAttribute("title", "Цена за единицу товара, купленного в определенном количестве");
		element_acronym.innerHTML = "Цена";
		element_span.appendChild(element_acronym);

		//<input Цена
		var ElementInput = document.createElement("input");
		ElementInput.setAttribute("id", "shop_special_prices_price_" + count_input);
		ElementInput.setAttribute("type", "text");
		ElementInput.setAttribute("onblur", "FieldCheck(this)");
		ElementInput.setAttribute("onkeyup", "FieldCheck(this)");
		ElementInput.setAttribute("onkeydown", "FieldCheck(this)");
		ElementInput.setAttribute("value", "");
		ElementInput.setAttribute("name", "shop_special_prices_price_js_" + count_input);
		ElementInput.setAttribute("style", "width: 120px; background-image: url(/admin/images/bullet_green.gif); background-position: right center; background-repeat: no-repeat;");
		element_div.appendChild(ElementInput);

		fieldType["shop_special_prices_price_" + count_input] = {'reg' : /^[0-9.]*$/};
		fieldMessage["shop_special_prices_price_" + count_input] = {'reg' : "Значение поля не соответствует формату."};

		//<div id="_error" class="div_message_error"/>
		var element_div_error = document.createElement("div");
		element_div_error.setAttribute("class", "div_message_error");
		element_div_error.setAttribute("id", "shop_special_prices_price_" + count_input + "_error");
		element_div.appendChild(element_div_error);

		// или
		var element_div = document.createElement("div");
		element_div.setAttribute("class", "item_div");
		element_div.setAttribute("style", "float: left; padding-top: 30px;");
		element_div.innerHTML = " или";
		cbItem.appendChild(element_div);

		// Создаем div
		var element_div = document.createElement("div");
		element_div.setAttribute("class", "item_div");
		element_div.setAttribute("style", "float: left;");
		cbItem.appendChild(element_div);

		var element_span = document.createElement("span");
		element_span.setAttribute("class", "caption");
		element_div.appendChild(element_span);

		var element_acronym = document.createElement("acronym");
		element_acronym.setAttribute("title", "Процент от базовой цены. Например для скидки 15% процент от базовой цены будет 85");
		element_acronym.innerHTML = "% от цены";
		element_span.appendChild(element_acronym);

		//<input %
		var ElementInput = document.createElement("input");
		ElementInput.setAttribute("id", "shop_special_prices_percent_" + count_input);
		ElementInput.setAttribute("type", "text");
		ElementInput.setAttribute("onblur", "FieldCheck(this)");
		ElementInput.setAttribute("onkeyup", "FieldCheck(this)");
		ElementInput.setAttribute("onkeydown", "FieldCheck(this)");
		ElementInput.setAttribute("value", "0.00");
		ElementInput.setAttribute("name", "shop_special_prices_percent_js_" + count_input);
		ElementInput.setAttribute("style", "width: 120px; background-image: url(/admin/images/bullet_green.gif); background-position: right center; background-repeat: no-repeat;");
		element_div.appendChild(ElementInput);

		fieldType["shop_special_prices_percent_" + count_input] = {'reg' : /^[0-9.]*$/};
		fieldMessage["shop_special_prices_percent_" + count_input] = {'reg' : "Значение поля не соответствует формату."};

		//<div id="_error" class="div_message_error"/>
		var element_div_error = document.createElement("div");
		element_div_error.setAttribute("class", "div_message_error");
		element_div_error.setAttribute("id", "shop_special_prices_percent_" + count_input + "_error");
		element_div.appendChild(element_div_error);
	}
}

// -------------------------------------
function MainMenu(divId)
{
	if (action == '')
	{
		var data = {'_': Math.round(new Date().getTime())};

		if ($("div[id="+divId+"]").width() == 0)
		{
			action = 'showing';
			ShowMainMenu(divId);
			data['main_menu'] = 0;
		}
		else
		{
			action = 'hiding';
			HideMainMenu(divId);
			data['main_menu'] = 1;
		}

		$.ajax({
			url: '/admin/index.php',
			data: data,
			type: 'post',
			dataType: 'json',
			success: function(){}
		});
	}
}

function ShowMainMenu(divId)
{
	var RightMaxWidth = 215;
	var RightPadding = 30;

	$("div[id=body_left_box]")
	.css('border-right-style', "solid")
	.css('border-right-color', "white");

	$("div[id=body_left_box]")
	.css('margin-right', -1 * RightMaxWidth)
	.css('border-right-width', RightMaxWidth);

	$("div[id=body]")
	// Начинаем с 30, чтобы у IE не было горизонтальной полоски
	.css('padding-right', RightPadding)
	.animate({
		paddingRight: RightMaxWidth + RightPadding
		}, {
		queue: false,
		duration: 'normal',
		complete: function(){
			$(this).css('padding-right', '');
		}
	});

	$("div[id="+divId+"]")
	.css('display', 'block')
	.animate({
		width: RightMaxWidth,
		opacity: 1.0}, {
		duration: 'normal',
		complete: function(){
			action = '';
			$("img[id=MainMenuImg]").attr('src', '/admin/images/menu_show.gif');
		}
	});
}

function HideMainMenu(divId)
{
	var RightPadding = 30;

	$("div[id=body]")
	.animate({
		paddingRight: RightPadding
		}, {
		queue: false,
		duration: 'normal',
		complete: function(){
		}
	});

	$("div[id="+divId+"]")
	.animate({
		width: 0,
		opacity: 0}, {
		duration: 'normal',
		complete: function(){
			action = '';
			$(this).css('display', 'none');
			// Убираем оставшиеся 30 пикселей после скрытия самого блока
			$("div[id=body]").css('padding-right', 0);
			$("img[id=MainMenuImg]").attr('src', '/admin/images/menu_show_reverse.gif');

			$("div[id=body_left_box]")
			.css('margin-right', -1 * RightPadding)
			.css('border-right-width', 0);
		}
	});
}

// --- / [Menu] ---