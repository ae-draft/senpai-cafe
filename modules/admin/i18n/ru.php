<?php

/**
 * Admin forms.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
return array(
	'authorization_form_title' => 'Центр администрирования',
	'authorization_form_login' => 'Пользователь:',
	'authorization_form_password' => 'Пароль:',
	'authorization_form_ip' => 'Привязать сессию к IP-адресу',
	'authorization_form_button' => 'Войти',
	'themes' => 'Темы',
	'authorization_error_valid_user' => 'Ошибка! Неверные данные!<br />Ваш IP-адрес <b>%s</b>, администратор уведомлен о событии.',
	'authorization_error_access_temporarily_unavailable' => 'Доступ временно запрещен. Вы можете попробовать ввести пароль через %s сек.',
	'authorization_title' => 'Вход в центр администрирования HostCMS',
	'authorization_notice' => '<p>* Для входа в центр администрирования в браузере должно быть разрешено использование Cookies.</p>',
	'authorization_notice2' => '<p>** Привязка сессии к IP-адресу уменьшает риск несанкционированного доступа к центру администрирования.</p>',

	'index_title' => "Система управления сайтом %s v. %s",

	'index_systems_events' => 'Журнал событий',
	'index_systems_characteristics' => 'Системные характеристики',
	'index_events_journal_link' => 'Другие события',
	'index_error_open_log' => 'Ошибка открытия log-файла ',
	'index_events_journal_date' => 'Дата',
	'index_events_journal_user' => 'User',
	'index_events_journal_event' => 'Событие',

	// Технические данные
	'index_title2' => 'Технические данные',
	'index_tech_date_hostcms' => 'Версия HostCMS: ',
	'index_tech_date_php' => 'Версия PHP: ',
	'index_tech_date_sql' => 'Версия MySQL: ',
	'index_tech_date_gd' => 'Версия GD: ',
	'index_tech_date_pcre' => 'Версия PCRE: ',
	'index_tech_date_mb' => 'Multibyte String: ',
	'index_tech_date_json' => 'JSON: ',
	'index_tech_date_simplexml' => 'SimpleXML:',
	'index_tech_date_iconv' => 'Iconv:',
	'index_tech_date_max_date_size' => 'Макс. размер POST: ',
	'index_tech_date_max_time' => 'Макс. время исполнения: %d сек.',
	'index_tech_date_session_save_path' => 'Путь сохранения сессий: ',
	'index_tmp_dir' => 'Временная папка: ',
	'index_tmp_not_dir' => 'Не определена',
	'index_free_space' => 'Доступно места: ',
	'index_memory_limit' => 'Доступная память: ',
	'index_memory_unit' => ' Мб.',
	'index_memory_not_limit' => 'Не определена',
	'index_safe_mode' => 'Защищенный режим: ',
	'index_register_globals' => 'Register Globals: ',
	'index_magic_quotes' => 'Magic Quotes: ',
	'index_on' => 'Включено',
	'index_off' => 'Отключено',

	'free' => 'Приобретите коммерческую версию и получите:',
	'notes' => 'Заметки',
	'notes_save' => 'Заметки успешно сохранены.',
	'save_notes' => 'Сохранить',

	'index_events_bad_password' => 'В системе существует пользователь со стандартными логином и паролем, необходимо изменить пароль пользователя "admin"',
	'index_unset_tmp_dir' => 'Не существует директория для временных файлов "%s", необходимо создать ее. Изменить путь к директории можно через константу TMP_DIR',

	'updates_count_access' => 'Доступно %s %s. <a href="/admin/update/index.php">Посмотреть список обновлений</a>.',

	'unknown' => 'Не определено',

	'base_word' => 'обновле',
	'add_word0' => 'ний',
	'add_word1' => 'ние',
	'add_word2' => 'ния',

	'seconds' => 'сек.',

	'delete_success' => 'Элемент удален!',
	'undelete_success' => 'Элемент восстановлен!',

	'website' => 'Официальный сайт HostCMS: ',
	'support_email' => 'Служба технической поддержки: ',
	'viewSite' => 'Посмотреть сайт в новом окне',

	'copy' => '%s [Копия от %s]',
);