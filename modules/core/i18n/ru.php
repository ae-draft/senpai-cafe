<?php

/**
 * Core.
 *
 * @package HostCMS 6\Core
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
return array(
	'admin_menu_0' => 'Структура сайта',
	'admin_menu_1' => 'Сервисы',
	'admin_menu_2' => 'Пользователи',
	'admin_menu_3' => 'Системные функции',

	'error_file_write' => 'Ошибка открытия файла для записи %s, проверьте права доступа к директории.',
	'error_resize' => 'Ошибка уменьшения малого изображения до максимально допустимого размера. Вероятной причиной является указание размера изображения меньше 0.',
	'error_upload' => 'Файл не загружен!',

	'error_log_message_stack' => "Файл: %s, строка %s",
	'error_log_message_short' => "<strong>%s:</strong> %s в файле %s (строка %s)",
	'error_log_message' => "<strong>%s:</strong> %s в файле %s (строка %s)\nСтек вызовов:\n%s",

	'info_cms' => 'Система управления сайтом',
	'info_cms_site_support' => 'Техническая поддержка сайта: ',
	'info_cms_site' => 'Официальный сайт: ',
	'info_cms_support' => 'Служба технической поддержки: ',
	'info_cms_sales' => 'Отдел продаж: ',

	'purchase_commercial_version' => 'Купить полную версию',

	'administration_center' => 'Центр администрирования',
	'debug_information' => 'Отладочная информация',
	'sql_queries' => 'SQL-запросы',
	'sql_statistics' => 'Время: <strong>%.3f</strong> сек. <a onclick="ShowHide(\'sql_h%d\')" class="pointer">Вызовы</a>',
	'sql_debug_backtrace' => '%s, строка %d<br/>',
	'show_xml' => 'Показать XML/XSL',
	'hide_xml' => 'Скрыть XML/XSL',
	'logout' => 'Выход',

	'total_time' => 'Время выполнения: <strong>%.3f</strong> с, из них',
	'time_load_modules' => "время загрузки модулей: <strong>%.3f</strong> с",
	'time_page_generation' => "время генерации содержания страницы: <strong>%.3f</strong> с",
	'time_database_connection' => "время соединения с СУБД: <strong>%.3f</strong> с",
	'time_database_select' => "время выбора БД: <strong>%.3f</strong> с",
	'time_sql_execution' => "время выполнения запросов: <strong>%.3f</strong> с",
	'time_xml_execution' => "время обработки XML: <strong>%.3f</strong> с",
	'memory_usage' => "Использовано памяти: <strong>%.2f</strong> Мб.",
	'number_of_queries' => "Количество запросов: <strong>%d</strong>.",
	'enabled' => 'Включено',
	'disabled' => 'Отключено',
	'compression' => 'Компрессия: <strong>%s</strong>.',
	'cache' => 'Кэширование: <strong>%s</strong>.',

	'cache_insert_time' => 'время записи в кэш: <strong>%.3f</strong> с',
	'cache_read_time' => 'время чтения из кэша: <strong>%.3f</strong> с',

	'cache_write_requests' => 'запросов на запись: <strong>%d</strong>',
	'cache_read_requests' => 'запросов на чтение: <strong>%d</strong>',

	'error_create_log' => "Невозможно создать log-файл <b>%s</b><br /><b>Проверьте наличие указанной директории и установите необходимые права доступа для нее.</b>",
	'error_log_level_0' => "Нейтральное",
	'error_log_level_1' => "Успешное",
	'error_log_level_2' => "Низкий уровень критичности",
	'error_log_level_3' => "Средний уровень критичности",
	'error_log_level_4' => "Наивысший уровень критичности",

	'error_log_attempt_to_access' => "Попытка доступа к модулю %s незарегистрированным пользователем",
	'error_log_several_modules' => "Ошибка! Найдено несколько экземпляров одинаковых модулей!",
	'error_log_access_was_denied' => "Доступ к модулю %s запрещен",
	'error_log_module_disabled' => "Модуль %s отключен",
	'error_log_access_allowed' => "Доступ к модулю \"%s\" разрешен, форма \"%s\", действие \"%s\"",
	'error_log_logged' => "Вход в систему управления",
	'error_log_authorization_error' => 'Неверные данные для аутентификации',
	'error_log_exit' => 'Выход из системы управления',
	'session_destroy_error' => 'Ошибка закрытия сеанса',

	'error_message' => "Здравствуйте!\n"
	. "Только что на сайте произошло событие, информация о котором представлена ниже:\n"
	. "Дата: %s\n"
	. "Событие: %s\n"
	. "Статус события: %s\n"
	. "Пользователь: %s\n"
	. "Сайт: %s\n"
	. "Страница: %s\n"
	. "IP-адрес: %s\n"
	. "Система управления сайтом %s,\n"
	. "http://%s/\n",

	'E_ERROR' => "Ошибка",
	'E_WARNING' => "Предупреждение",
	'E_PARSE' => "Parse Error",
	'E_NOTICE' => "Замечание",
	'E_CORE_ERROR' => "Core Error",
	'E_CORE_WARNING' => "Core Warning",
	'E_COMPILE_ERROR' => "Compile Error",
	'E_COMPILE_WARNING' => "Compile Warning",
	'E_USER_ERROR' => "Ошибка",
	'E_USER_WARNING' => "Предупреждение",
	'E_USER_NOTICE' => "Замечание",
	'E_STRICT' => "Strict",

	'default_form_name' => 'Основная',
	'default_event_name' => 'Просмотр',

	'widgets' => 'Виджеты',
	'addNote' => 'Добавить заметку',
	'deleteNote' => 'Удалить заметку',

	'key_not_found' => 'Не найден лицензионный ключ!',
	'getting_key' => '<h2>Получение ключа</h2>
	<div style="overflow: auto; position: absolute; z-index: 9999; height: 400px"><p>После установки системы управления необходимо зарегистрироваться на нашем сайте в разделе «<a href="http://www.hostcms.ru/users/" target="_blank">Личный кабинет</a>»</p>
	<p>После подтверждения регистрации пользователя и входа в личный кабинет, в разделе «Лицензии» доступен список выданных лицензий:<br/>
	<img src="http://www.hostcms.ru/images/news/user-orders/img1.gif" class="screen" /></p>
	<p>Коммерческие пользователи могут узнать свой номер договора и PIN-код из таблицы в разделе «Лицензии» личного кабинета, пользователи HostCMS.Халява могут добавить новую лицензию.</p>
	<p>Узнав номер договора и PIN-код можно вернуться в <a href="/admin/" target="_blank">центр администрирования</a> и ввести эти данные в разделе «Сайты» — «Настройки» — «Регистрационные данные».</p>
	<p>Далее можно получать ключи в <a href="/admin/" target="_blank">центре администрирования</a> системы управления (начиная с версии 5.1.1), перейдя в раздел «Сайты» — «Домены»:
	<br/>
	<img src="http://www.hostcms.ru/images/keys/domain_list.gif" class="screen" /></p>
	<p>При нажатии на пиктограмму «Ключ» система запросит ключ для выбранного домена по вашей лицензии и внесет его в список ключей сайта.
	<h2>Вход в центр администрирования</h2>
	<p>Перейдите в <a href="/admin/" target="_blank">центр администрирования</a>, далее действуйте по инструкции.</p>
	</div>',

	'access_forbidden_title' => 'Доступ к сайту запрещен',
	'access_forbidden' => 'Доступ к сайту запрещен. Обратитесь к администратору.',
	
	'extension_does_not_allow' => 'Загружать файл с расширением "%s" запрещено.',
	'delete_success' => 'Элемент удален!',
	'undelete_success' => 'Элемент восстановлен!',
	'redaction0'=>'Халява',
	'redaction1'=>'Мой сайт',
	'redaction3'=>'Малый бизнес',
	'redaction5'=>'Бизнес',
	'redaction7'=>'Корпорация',
);