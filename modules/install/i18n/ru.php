<?php

/**
 * Install.
 *
 * @package HostCMS 6\Install
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
return array(
	'changeLanguage' => 'Выберите язык:',
	'constant_on' => 'Включено',
	'constant_off' => 'Отключено',
	'undefined' => 'Не определено',
	'not_installed' => 'Не установлен',

	'supported' => 'Поддерживается',
	'unsupported' => 'Не поддерживается',

	'success' => 'Успешно',
	'error' => 'Ошибка',

	'start' => 'Запуск',
	'back' => 'Назад',
	'next' => 'Далее',

	'yes' => 'Да',
	'seconds' => 'сек.',
	'megabytes' => 'M',

	'step_0' => 'Установка HostCMS',
	'step_1' => 'Шаг 1: Информация о системе управления сайтом HostCMS',
	'step_2' => 'Шаг 2: Лицензионный договор',
	'step_3' => 'Шаг 3: Проверка соответствия параметров сервера',
	'step_4' => 'Шаг 4: Параметры',
	'step_5' => 'Шаг 5: Результат предварительного конфигурирования',
	'step_6' => 'Шаг 6: Выбор макета сайта',
	'step_7' => 'Шаг 7: Выбор цветовой схемы',
	'step_8' => 'Шаг 8: Настройки макета',
	'step_9' => 'Шаг 9: Завершение установки HostCMS',

	'write_error' => 'Ошибка записи в файл %s.',
	'template_data_information' => 'Внесенные данные будут использованы в макете.',
	'allowed_extension' => 'Разрешенные расширения файла: %s',
	'max_file_size' => 'Максимальный размер файла: %s x %s',
	'empty_settings' => 'Макет не имеет настроек.',
	'file_not_found' => 'Файл %s не найден.',
	'template_files_copy_success' => 'Файлы макета скопированы.',
	'template_install_success' => 'Установка макета выполнена.',
	'template_files_copy_error' => 'Ошибка копирования файлов макета!',
	'file_copy_error' => 'Ошибка копирования файла в %s!',
	'file_disabled_extension' => 'Файл для %s имеет запрещенное расширение!',

	'templates_dont_exist' => 'Недоступен список шаблонов. Система управления будет установлена с шаблоном по умолчанию.',

	'license_agreement_error' => 'Для продолжения установки Вам необходимо принять условия лицензионного соглашения!',
	'license_agreement' => 'Я согласен с условиями лицензионного договора.',

	'table_field_param' => 'Параметр',
	'table_field_need' => 'Требуется',
	'table_field_thereis' => 'Имеется',
	'table_field_value' => 'Значение',

	'table_field_php_version' => 'Версия PHP:',
	'table_field_mbstring' => 'Multibyte String:',
	'table_field_json' => 'JSON:',
	'table_field_simplexml' => 'SimpleXML:',
	'table_field_iconv' => 'Iconv:',
	'table_field_gd_version' => 'Версия GD:',
	'table_field_pcre_version' => 'Версия PCRE:',
	'table_field_maximum_upload_data_size' => 'Максимальный размер загружаемых данных:',
	'table_field_maximum_execution_time' => 'Максимальное время исполнения:',
	'table_field_disc_space' => 'Дисковое пространство:',
	'table_field_ram_space' => 'Объем памяти:',
	'table_field_safe_mode' => 'Защищённый режим PHP:',
	'table_field_register_globals' => 'Глобальные переменные:',
	'table_field_magic_quotes_gpc' => 'Магические кавычки:',
	'table_field_xslt_support' => 'Поддержка XSLT:',

	'parameter_corresponds' => 'Параметр соответствует.',
	'parameter_not_corresponds_but_it_is_safe' => 'Несоответствие, не влияющее на функционирование системы.',
	'parameter_not_corresponds' => 'Параметр не соответствует.',

	'access_parameter' => 'Параметры доступа',
	'file_access' => 'Права доступа к файлам:',
	'directory_access' => 'Права доступа к директориям:',
	'example' => '(например, %s)',
	'database_params' => 'Параметры базы данных',
	'mysql_server' => 'MySQL cервер:',
	'database_login' => 'Логин для базы данных:',
	'database_pass' => 'Пароль для базы данных:',
	'database_mysql' => 'База данных MySQL:',
	'database_driver' => 'Драйвер MySQL:',
	'create_database' => 'Создать базу данных:',
	'create_database_flag' => 'Не устанавливайте этот флажок, если база данных уже создана!',
	'clear_database' => 'Очистить базу данных:',
	'clear_database_caution' => 'При очищении базы данных все данные из нее будут удалены!',

	'action' => 'Действие',
	'result' => 'Результат',
	'comment' => 'Комментарий',

	'store_database_params' => 'Запись параметров БД',
	'not_enough_rights_error' => 'Ошибка записи файла <b>%s</b>. Установите необходимые права доступа для директории.',
	'database_connection' => 'Соединение с базой данных',
	'database_connection_check_params' => 'Проверьте правильность данных для соединения с БД.',
	'database_creation' => 'Создание базы данных',
	'attention_message' => 'У пользователя БД, с помощью которого происходит соединение, должно быть достаточно прав для создания БД. На большинстве виртуальных хостингов таких прав у пользователей БД нет. В таком случае рекомендуется создать базу данных из панели управления хостингом и не устанавливать галочку "Создать базу данных".',
	'attention_message2' => '<p>В случае повторной инсталляции рекомендуется производить установку в новую базу данных, в противном случае все данные в БД будут потеряны.</p><p>Для повторной установки системы управления сайтом HostCMS нажмите кнопку <strong>"Далее"</strong>, для начала работы нажмите кнопку <strong>"Запуск"</strong>.</p><p>При невозможности автоматически удалить инсталлятор, для продолжения работы удалите вручную с сайта директорию <b>/install/</b>.</p>',
	'attention_message3' => '<p>Для продолжения установки соединитесь с сервером по протоколу FTP и <strong>удалите файл install.php</strong>, размещенный в корне сайта.</p>',

	'attention_message4' => '<p>Программа установки проверит соответствие программного обеспечения на сервере системным требованиям HostCMS, произведет установку и первоначальное конфигурирование системы управления сайтом HostCMS.</p>
	<p>В случае возникновения вопросов или неточностей в работе системы управления сайтом HostCMS, просим обращаться в <a href="http://www.hostcms.ru/support/" target="_blank">службу технической поддержки</a>.
	</p><p>Благодарим за выбор системы управления сайтом HostCMS!</p>',

	'database_selection' => 'Выбор базы данных',
	'database_selection_access_denied' => 'Пользователь не имеет права на доступ к указанной БД или БД не существует.',
	'database_clearing' => 'Очистка базы данных',
	'sql_dump_loading' => 'Загрузка и выполнение дампа SQL',
	'sql_dump_loading_error' => 'Ошибка. Версия MySQL %s',
	'domen_installing' => 'Установка домена',
	'lng_installing' => 'Установка языка',
	'sql_error' => 'Ошибка %s',

	'error_system_already_install' => 'Система управления HostCMS уже установлена!',
	'delete_install_file' => 'Удалите файл install.php',
	'attention_complete_install' => '<p>Для завершения установки, перехода на главную страницу сайта и удаления системы инсталляции нажмите кнопку <strong>"Запуск"</strong>.</p><p>Для перехода в раздел администрирования введите в адресную строку браузера <a href="/admin/" target="_blank">http://[ваш_сайт]/admin/</a>, предварительно заменив [ваш_сайт] на адрес сайта.</p><p>Для входа в раздел администрирования используйте: <br />Пользователь: <strong>admin</strong> <br />Пароль: <strong>admin</strong></p><p>Благодарим за выбор системы управления сайтом HostCMS!</p>',
);