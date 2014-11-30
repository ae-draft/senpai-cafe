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
	'authorization_form_title' => 'Administration center',
	'authorization_form_login' => 'User:',
	'authorization_form_password' => 'Password:',
	'authorization_form_ip' => 'Attach session to IP address',
	'authorization_form_button' => 'Enter',
	'themes' => 'Themes',
	'authorization_error_valid_user' => 'Error! Invalid login and password!<br />Your IP-address is <b>%s</b>, administrator informed of this event.',
	'authorization_error_access_temporarily_unavailable' => 'Access temporarily unavailable. Please try again in %s seconds',
	'authorization_title' => 'HostCMS administration center',
	'authorization_notice' => '<p>* In order to enter administration center, your browser should support Cookies.</p>',
	'authorization_notice2' => '<p>** Attachment of session to IP address decreases the risk of an authorized access to administration center.</p>',

	'index_title' => "Administration center of %s v. %s",

	'index_systems_events' => 'Events log',
	'index_systems_characteristics' => 'System characteristics',
	'index_events_journal_link' => 'Other events',
	'index_error_open_log' => 'Error while opening log file ',
	'index_events_journal_date' => 'Date',
	'index_events_journal_user' => 'User',
	'index_events_journal_event' => 'Event',

	// Технические данные
	'index_title2' => 'Technical data',
	'index_tech_date_hostcms' => 'HostCMS version: ',
	'index_tech_date_php' => 'PHP version: ',
	'index_tech_date_sql' => 'MySQL version: ',
	'index_tech_date_gd' => 'GD version: ',
	'index_tech_date_pcre' => 'PCRE version: ',
	'index_tech_date_mb' => 'Multibyte String: ',
	'index_tech_date_json' => 'JSON: ',
	'index_tech_date_simplexml' => 'SimpleXML:',
	'index_tech_date_iconv' => 'Iconv:',
	'index_tech_date_max_date_size' => 'Maximum size of uploaded data: ',
	'index_tech_date_max_time' => 'Maximum execution time: %d sec',
	'index_tech_date_session_save_path' => 'Path to save sessions: ',
	'index_tmp_dir' => 'Temporary folder: ',
	'index_tmp_not_dir' => 'Not identified',
	'index_free_space' => 'Space available: ',
	'index_memory_limit' => 'Memory available: ',
	'index_memory_unit' => ' Mb.',
	'index_memory_not_limit' => 'Not identified',
	'index_safe_mode' => 'Safe mode: ',
	'index_register_globals' => 'Global variables: ',
	'index_magic_quotes' => 'Magic quotes: ',
	'index_on' => 'Enable',
	'index_off' => 'Disable',

	'free' => 'Purchase the commercial version, and you will acquire:',
	'notes' => 'Notes',
	'notes_save' => 'Notes saved successfully.',
	'save_notes' => 'Save',

	'index_events_bad_password' => 'A user with standard login and password exists in the system; you need to change password of user "admin"',
	'index_unset_tmp_dir' => 'No such directory "%s" for temporary file, you must create it. You can change path to the temporary directory in constant TMP_DIR',

	'updates_count_access' => '%s %s available. <a href="/admin/update/index.php">Display updates list</a>.',

	'unknown' => 'Unknown',

	'base_word' => 'updat',
	'add_word0' => 'es',
	'add_word1' => 'e',
	'add_word2' => 'es',

	'seconds' => 'sec',

	'delete_success' => 'Item deleted successfully!',
	'undelete_success' => 'Item restored successfully!',

	'website' => 'Content management system website: ',
	'support_email' => 'Support email: ',
	'viewSite' => 'View site in a new window',

	'copy' => '%s [Copy %s]',
);