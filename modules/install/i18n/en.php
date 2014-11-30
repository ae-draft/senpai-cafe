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
	'changeLanguage' => 'Select your language:',
	'constant_on' => 'Enabled',
	'constant_off' => 'Disabled',
	'undefined' => 'Undefined',
	'not_installed' => 'Not installed',

	'supported' => 'Supported',
	'unsupported' => 'Unsupported',

	'success' => 'Success',
	'error' => 'Error',

	'start' => 'Start',
	'back' => 'Back',
	'next' => 'Next',

	'yes' => 'Yes',
	'seconds' => 'sec',
	'megabytes' => 'M',

	'step_0' => 'HostCMS installation',
	'step_1' => 'Step 1: HostCMS information',
	'step_2' => 'Step 2: License agreement',
	'step_3' => 'Step 3: Check server settings',
	'step_4' => 'Step 4: Settings',
	'step_5' => 'Step 5: Preliminary configuration',
	'step_6' => 'Step 6: Choose template',
	'step_7' => 'Step 7: Choose color scheme',
	'step_8' => 'Step 8: Template settings',
	'step_9' => 'Step 9: Installation complete',

	'write_error' => 'Error while writing into file %s.',
	'template_data_information' => 'Information for template.',
	'allowed_extension' => 'Allowed file extensions: %s',
	'max_file_size' => 'Maximum file size: %s x %s',
	'empty_settings' => 'Template hasn\'t settings',
	'file_not_found' => 'File %s not found.',
	'template_files_copy_success' => 'Template files copied successfully!',
	'template_install_success' => 'Template installed successfully.',
	'template_files_copy_error' => 'Error! Template files has not been copied!',
	'file_copy_error' => 'Error! File has not been copied in %s!',
	'file_disabled_extension' => 'File for %s has forbidden extension!',

	'templates_dont_exist' => 'List of templates is unavailable. CMS will be installed with the default template.',

	'license_agreement_error' => 'Please accept the License Agreement terms to continue the installation process!',
	'license_agreement' => 'I accept the terms in the license agreement.',

	'table_field_param' => 'Parameters',
	'table_field_need' => 'Required',
	'table_field_thereis' => 'Available',
	'table_field_value' => 'Value',

	'table_field_php_version' => 'PHP version:',
	'table_field_mbstring' => 'Multibyte String:',
	'table_field_json' => 'JSON:',
	'table_field_simplexml' => 'SimpleXML:',
	'table_field_iconv' => 'Iconv:',
	'table_field_gd_version' => 'GD version:',
	'table_field_pcre_version' => 'PCRE version:',
	'table_field_maximum_upload_data_size' => 'Maximum uploaded data size:',
	'table_field_maximum_execution_time' => 'Maximum execution time:',
	'table_field_disc_space' => 'Space available:',
	'table_field_ram_space' => 'Memory available:',
	'table_field_safe_mode' => 'PHP safe mode:',
	'table_field_register_globals' => 'Global variables:',
	'table_field_magic_quotes_gpc' => 'Magic quotes:',
	'table_field_xslt_support' => 'XSLT:',

	'parameter_corresponds' => 'OK',
	'parameter_not_corresponds_but_it_is_safe' => 'Performance-independent mismatch.',
	'parameter_not_corresponds' => 'The parameter doesn\'t conform to.',

	'access_parameter' => 'Access parameters',
	'file_access' => 'File mode:',
	'directory_access' => 'Directory mode:',
	'example' => '(for example, %s)',
	'database_params' => 'Database parameters',
	'mysql_server' => 'MySQL server:',
	'database_login' => 'Database login:',
	'database_pass' => 'Database password:',
	'database_mysql' => 'MySQL database:',
	'database_driver' => 'MySQL driver:',
	'create_database' => 'Create database:',
	'create_database_flag' => 'If your database was created do not select this checkbox.',
	'clear_database' => 'Clear database:',
	'clear_database_caution' => 'All data will be removed after clearing your database!',

	'action' => 'Action',
	'result' => 'Result',
	'comment' => 'Comment',

	'store_database_params' => 'Database parameters record',
	'not_enough_rights_error' => 'File write error <b>%s</b>. Please set the required directory access rights.',
	'database_connection' => 'Database connection',
	'database_connection_check_params' => 'Please verify your data to be connected to your database.',
	'database_creation' => 'Database creation',
	'attention_message' => 'The database user to be connected through should have enough rights to create a database. The database users of most hosting do not possess such rights. If the case, it is recommended to create your database using the hosting control panel. Do not mark with a flag "Create database".',

	'attention_message2' => '<p>If reinstalling, it is recommended to install in the new database otherwise all your database data shall be lost.</p><p>To reinstall HostCMS push the button <strong>"Next"</strong>, and the button <strong>"Start"</strong> to start your work.</p><p>if the installer cannot be removed delete manually the directory <b>/install/</b> from the website.</p>',
	'attention_message3' => '<p>To continue the installation process please connect to the FTP server and <strong>delete the file install.php</strong>, from the website roots.</p>',

	'attention_message4' => '<p>The installer checks the server software compliance to the HostCMS system requirements, performs the installation together with the initial configuration of the HostCMS.</p>
	<p>If you have any questions or in case of inaccurate running of the HostCMS please contact <a href="http://www.hostcms.ru/support/" target="_blank">our Technical Support Service</a>.
	</p><p>Thank you for choosing HostCMS content management system!</p>',

	'database_selection' => 'Database selection',
	'database_selection_access_denied' => 'The user access to the selected database is denied or this database does not exist.',
	'database_clearing' => 'Database clearing',
	'sql_dump_loading' => 'SQL dump loading and execution',
	'sql_dump_loading_error' => 'Error. MySQL version %s',
	'domen_installing' => 'Domain installation',
	'lng_installing' => 'Language installation',
	'sql_error' => 'Error %s',

	'error_system_already_install' => 'The HostCMS has been installed!',
	'delete_install_file' => 'Delete file install.php',
	'attention_complete_install' => '<p>To finish the installation process, to go to the website home page as well as to delete the installation system please push the button <strong>"Start"</strong>.</p><p>To go to the administration section, please, enter <a href="/admin/" target="_blank">http://[your_site]/admin/</a> in the browser address line having changed [your_site] to the website address.</p><p>To enter the administration section use: <br />Login: <strong>admin</strong> <br />Password: <strong>admin</strong></p><p>Thank you for choosing HostCMS content management system!</p>',
);