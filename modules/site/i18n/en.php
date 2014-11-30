<?php
/**
 * Sites.
 *
 * @package HostCMS 6\Site
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
return array(
	'model_name' => 'Sites',
	'name' => '<acronym title="Website name in system">Website name</acronym>',
	'active' => '<acronym title="Website status. Inactive websites are not displayed to users">Activity</acronym>',
	'sorting' => '<acronym title="Field according to which websites should be sorted">Sorting order</acronym>',
	'locale' => '<acronym title="Website locale. E.g., «ru_RU.utf8»">Locale</acronym>',
	'coding' => '<acronym title="Site encoding, e.g., UTF-8">Encoding</acronym>',
	'timezone' => '<acronym title="Site timezone. Use to change the site time zone offset">Timezone</acronym>',
	'max_size_load_image' => '<acronym title="Maximum size of small image when converting">Maximum size of small image when converting</acronym>',
	'max_size_load_image_big' => '<acronym title="Maximum size of large image">Maximum size of large image when converting</acronym>',
	'admin_email' => '<acronym title="E-mail of website administrator">E-mail</acronym>',
	'send_attendance_report' => '<acronym title="If you activate this parameter, a message containing website visiting statistics will be sent to the e-mail address of administrator on a daily basis">Send daily report on website visiting statistics</acronym>',
	'files_chmod' => '<acronym title="Access mode to files after their creating, e.g. 0644">File mode</acronym>',
	'chmod' => '<acronym title="Access mode to folders after their creation, e.g. 0755">Directory mode</acronym>',
	'date_format' => '<acronym title="Date format. E.g., «d.m.Y.»">Date format</acronym>',
	'date_time_format' => '<acronym title="Date and time format. E.g., «d.m.Y H:i:s.»">Date and time format</acronym>',
	'error' => '<acronym title="Errors output mode. E.g., «E_ERROR» or «E_ALL»">Errors output mode</acronym>',
	'error404' => '<acronym title="Page to be displayed in case 404 error occurs (page not found); if page is not specified, user will be redirected to the home page after 404 error occurred">Page for &quot;404 error&quot; (page not found)</acronym>',
	'error403' => '<acronym title="Page to be displayed when a user having no access to website is trying to access a website section">Page for &quot;403 error&quot; (access denied)</acronym>',
	'robots' => '<acronym title="Content of file robots.txt for this website">robots.txt</acronym>',
	'key' => '<acronym title="License keys">License key</acronym>',
	'closed' => '<acronym title="Page to be displayed in case website was deactivated by administrator">Page to be displayed when website is inactive</acronym>',
	'safe_email' => '<acronym title="Parameter to specify whether e-mail should be protected from spam-robots on website pages">Protect e-mail on website pages</acronym>',
	'html_cache_use' => 'Cache website pages into static files',
	'html_cache_with' => 'Include pages into cache',
	'html_cache_without' => 'Do not include pages into cache',
	'css_left' => '<acronym title="Custom style of elements placing beyond the typing border on the left side; if not specified, style should be outlined clearly">CSS style for left optical alignment</acronym>',
	'css_right' => '<acronym title="Custom style of elements placing beyond the typing border on the right side; if not specified, style should be outlined clearly">CSS style for right optical alignment</acronym>',
	'html_cache_clear_probability' => '<acronym title="Defines the probability of cache flush in static files for the current website. E.g., if you specify 10000, cache will be flushed one time per 10000 requests to website. If 0 is specified, cache will not be flushed in static files">Number to determine probability of cache flush.</acronym>',
	'uploaddir' => '<acronym title="Relative path to folder where uploaded files are stored. Path should end with character /. E.g., upload/">Folder to store uploaded files</acronym>',
	'nesting_level' => '<acronym title="Number of nesting levels of directories (min. 1) to store files of system entities (main and additional properties of type \'File\' of information elements, main and additional properties of type \'File\' of information groups, additional properties of type \'File\' of structure nodes etc.)">Nesting levels</acronym>',
	'id' => 'Id',
	'site_add_site_form_title' => 'Add website information',
	'site_edit_site_form_title' => 'Edit website information',
	'site_chmod' => 'Access mode',
	'site_dates' => 'Formats',
	'site_errors' => 'Errors',
	'site_robots_txt' => 'robots.txt',
	'site_licence' => 'License',
	'site_cache_options' => ' Cache',
	'edit_success' => 'Website information modified successfully!',
	'edit_error' => 'Error occurred during changing website information!',
	'markDeleted_success' => 'Website deleted successfully!',
	'markDeleted_error' => 'Error occurred during deleting website information!',
	'changeStatus_success' => 'Website activity changed successfully!',
	'changeStatus_error' => 'Error occured during changing website activity!',
	'apply_success' => 'Information changed successfully!',
	'apply_error' => 'Error occured during changing information!',
	'notes' => 'Notes',
	'menu' => 'Websites',
	'save_notes' => 'Save',
	'site_show_site_title_list' => 'Websites',
	'site_show_site_title' => 'Websites',
	'site_link_add' => 'Add',
	'copy_success' => 'Website information copied successfully',
	'copy_error' => 'Error while copying website information',
	'ico_files_uploaded' => '<acronym title="Site favicon">Favicon</acronym>',
	'default' => 'Default',
	'menu2_caption' => 'Settings',
	'menu2_sub_caption' => 'Registration data',

	'accountinfo_title' => 'Edit registration data',
	'accountinfo_login' => '<acronym title="User login">Login</acronym>',
	'accountinfo_contract_number' => '<acronym title="Contract number">Contract number</acronym>',
	'accountinfo_pin_code' => '<acronym title="PIN code">PIN code</acronym>',
	'accountInfo_success' => 'Registration data modified successfully.',

	'delete_success' => 'Item deleted successfully!',
	'undelete_success' => 'Item restored successfully!',

	'add_site_with_template' => 'Add site with template',
	'choose_site_template' => 'Choose site template',
	'choose_color_scheme' => 'Choose color scheme',
	'template_settings' => 'Template settings',

	'delete_current_site' => 'User that belongs to the website cannot delete it!',
	'delete_last_site' => 'You cannot delete the last website!',
	'delete_site_all_superusers_belongs' => 'Unable to delete website that all superusers belong to!',
);