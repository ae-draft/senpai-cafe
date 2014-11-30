<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Sites.
 *
 * @package HostCMS 6\Site
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Site_Controller_Edit extends Admin_Form_Action_Controller_Type_Edit
{
	/**
	 * Set object
	 * @param object $object object
	 * @return self
	 */
	public function setObject($object)
	{
		parent::setObject($object);

		$oSeparatorField = Admin_Form_Entity::factory('Separator');

		$oSiteTabAccessRights = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Site.site_chmod'))
			->name('AccessRights');

		$oSiteTabFormats = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Site.site_dates'))
			->name('Formats');

		$oSiteTabErrors = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Site.site_errors'))
			->name('Errors');

		$oSiteTabRobots = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Site.site_robots_txt'))
			->name('Robots');

		$oSiteTabLicense = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Site.site_licence'))
			->name('License');

		$oSiteTabCache = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('Site.site_cache_options'))
			->name('Cache');

		$oMainTab = $this->getTab('main');

		$this->addTabAfter($oSiteTabAccessRights, $oMainTab)
			->addTabAfter($oSiteTabFormats, $oSiteTabAccessRights)
			->addTabAfter($oSiteTabErrors, $oSiteTabFormats)
			->addTabAfter($oSiteTabRobots, $oSiteTabErrors)
			->addTabAfter($oSiteTabLicense, $oSiteTabRobots);

		// Hide Cache tab
		if (Core::moduleIsActive('cache'))
		{
			$this->addTabAfter($oSiteTabCache, $oSiteTabLicense);
		}
		else
		{
			$this->skipColumns += array(
				'html_cache_use' => 'html_cache_use',
				'html_cache_with' => 'html_cache_with',
				'html_cache_without' => 'html_cache_without',
				'html_cache_clear_probability' => 'html_cache_clear_probability',
			);
		}

		$oMainTab
			// AccessRights
			->move($this->getField('chmod'), $oSiteTabAccessRights)
			->move($this->getField('files_chmod'), $oSiteTabAccessRights)
			// Formats
			->move($this->getField('date_format'), $oSiteTabFormats)
			->move($this->getField('date_time_format'), $oSiteTabFormats)
			->move($this->getField('css_left'), $oSiteTabFormats)
			->move($this->getField('css_right'), $oSiteTabFormats)
			// Errors
			->move($this->getField('error'), $oSiteTabErrors)
			->move($this->getField('error404'), $oSiteTabErrors)
			->move($this->getField('error403'), $oSiteTabErrors)
			->move($this->getField('closed'), $oSiteTabErrors)
			// Robots
			->move($this->getField('robots'), $oSiteTabRobots)
			// License
			->move($this->getField('key'), $oSiteTabLicense)
			// Cache
			->move($this->getField('html_cache_use'), $oSiteTabCache)
			->move($this->getField('html_cache_with'), $oSiteTabCache)
			->move($this->getField('html_cache_without'), $oSiteTabCache)
			->move($this->getField('html_cache_clear_probability'), $oSiteTabCache)
			;

		$this->getField('chmod')
			->style('width: 220px');

		$this->getField('files_chmod')
			->style('width: 220px');

		$this->getField('date_format')
			->divAttr(array('style' => 'float: left;'))
			->style('width: 330px');

		$this->getField('date_time_format')
			->style('width: 330px');

		$this->getField('css_left')
			->divAttr(array('style' => 'float: left;'))
			->style('width: 330px');

		$this->getField('css_right')
			->style('width: 330px');

		$this->getField('html_cache_clear_probability')
			->style('width: 330px');

		$this->getField('coding')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 110px;");

		$this->getField('sorting')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 110px;");

		$oLocaleField = $this->getField('locale');

		// Список локалей, если доступен
		if (Core::isFunctionEnable('php_uname') && mb_substr(php_uname(), 0, 7) != "Windows"
			&& Core::isFunctionEnable('exec'))
		{
			@exec("locale -a", $sys_result);

			if (isset($sys_result) && count($sys_result) > 0)
			{
				$aLocales = array();

				foreach ($sys_result as $sLocale)
				{
					$sLocale = iconv('ISO-8859-1', 'UTF-8//IGNORE//TRANSLIT', trim($sLocale));
					$aLocales[$sLocale] = $sLocale;
				}

				$oMainTab->delete($oLocaleField);

				$oLocaleField = Admin_Form_Entity::factory('Select');
				$oLocaleField
					->name('locale')
					->caption(Core::_('Site.locale'))
					->options($aLocales)
					->value($this->_object->locale);

				$oMainTab->addAfter($oLocaleField, $this->getField('sorting'));
			}
		}

		$oLocaleField
			->divAttr(array('style' => 'float: left;'))
			->style("width: 110px;");

		$oMainTab->delete(
			$this->getField('timezone')
		);

		// Timezone
		$aTimezone = array('Africa/Abidjan', 'Africa/Accra', 'Africa/Addis_Ababa', 'Africa/Algiers', 'Africa/Asmara', 'Africa/Asmera', 'Africa/Bamako', 'Africa/Bangui', 'Africa/Banjul', 'Africa/Bissau', 'Africa/Blantyre', 'Africa/Brazzaville', 'Africa/Bujumbura', 'Africa/Cairo', 'Africa/Casablanca', 'Africa/Ceuta', 'Africa/Conakry', 'Africa/Dakar', 'Africa/Dar_es_Salaam', 'Africa/Djibouti', 'Africa/Douala', 'Africa/El_Aaiun', 'Africa/Freetown', 'Africa/Gaborone', 'Africa/Harare', 'Africa/Johannesburg', 'Africa/Kampala', 'Africa/Khartoum', 'Africa/Kigali', 'Africa/Kinshasa', 'Africa/Lagos', 'Africa/Libreville', 'Africa/Lome', 'Africa/Luanda', 'Africa/Lubumbashi', 'Africa/Lusaka', 'Africa/Malabo', 'Africa/Maputo', 'Africa/Maseru', 'Africa/Mbabane', 'Africa/Mogadishu', 'Africa/Monrovia', 'Africa/Nairobi', 'Africa/Ndjamena', 'Africa/Niamey', 'Africa/Nouakchott', 'Africa/Ouagadougou', 'Africa/Porto-Novo', 'Africa/Sao_Tome', 'Africa/Timbuktu', 'Africa/Tripoli', 'Africa/Tunis', 'Africa/Windhoek', 'America/Adak', 'America/Anchorage', 'America/Anguilla', 'America/Antigua', 'America/Araguaina', 'America/Argentina/Buenos_Aires', 'America/Argentina/Catamarca', 'America/Argentina/ComodRivadavia', 'America/Argentina/Cordoba', 'America/Argentina/Jujuy', 'America/Argentina/La_Rioja', 'America/Argentina/Mendoza', 'America/Argentina/Rio_Gallegos', 'America/Argentina/Salta', 'America/Argentina/San_Juan', 'America/Argentina/San_Luis', 'America/Argentina/Tucuman', 'America/Argentina/Ushuaia', 'America/Aruba', 'America/Asuncion', 'America/Atikokan', 'America/Atka', 'America/Bahia', 'America/Barbados', 'America/Belem', 'America/Belize', 'America/Blanc-Sablon', 'America/Boa_Vista', 'America/Bogota', 'America/Boise', 'America/Buenos_Aires', 'America/Cambridge_Bay', 'America/Campo_Grande', 'America/Cancun', 'America/Caracas', 'America/Catamarca', 'America/Cayenne', 'America/Cayman', 'America/Chicago', 'America/Chihuahua', 'America/Coral_Harbour', 'America/Cordoba', 'America/Costa_Rica', 'America/Cuiaba', 'America/Curacao', 'America/Danmarkshavn', 'America/Dawson', 'America/Dawson_Creek', 'America/Denver', 'America/Detroit', 'America/Dominica', 'America/Edmonton', 'America/Eirunepe', 'America/El_Salvador', 'America/Ensenada', 'America/Fort_Wayne', 'America/Fortaleza', 'America/Glace_Bay', 'America/Godthab', 'America/Goose_Bay', 'America/Grand_Turk', 'America/Grenada', 'America/Guadeloupe', 'America/Guatemala', 'America/Guayaquil', 'America/Guyana', 'America/Halifax', 'America/Havana', 'America/Hermosillo', 'America/Indiana/Indianapolis', 'America/Indiana/Knox', 'America/Indiana/Marengo', 'America/Indiana/Petersburg', 'America/Indiana/Tell_City', 'America/Indiana/Vevay', 'America/Indiana/Vincennes', 'America/Indiana/Winamac', 'America/Indianapolis', 'America/Inuvik', 'America/Iqaluit', 'America/Jamaica', 'America/Jujuy', 'America/Juneau', 'America/Kentucky/Louisville', 'America/Kentucky/Monticello', 'America/Knox_IN', 'America/La_Paz', 'America/Lima', 'America/Los_Angeles', 'America/Louisville', 'America/Maceio', 'America/Managua', 'America/Manaus', 'America/Marigot', 'America/Martinique', 'America/Matamoros', 'America/Mazatlan', 'America/Mendoza', 'America/Menominee', 'America/Merida', 'America/Mexico_City', 'America/Miquelon', 'America/Moncton', 'America/Monterrey', 'America/Montevideo', 'America/Montreal', 'America/Montserrat', 'America/Nassau', 'America/New_York', 'America/Nipigon', 'America/Nome', 'America/Noronha', 'America/North_Dakota/Center', 'America/North_Dakota/New_Salem', 'America/Ojinaga', 'America/Panama', 'America/Pangnirtung', 'America/Paramaribo', 'America/Phoenix', 'America/Port-au-Prince', 'America/Port_of_Spain', 'America/Porto_Acre', 'America/Porto_Velho', 'America/Puerto_Rico', 'America/Rainy_River', 'America/Rankin_Inlet', 'America/Recife', 'America/Regina', 'America/Resolute', 'America/Rio_Branco', 'America/Rosario', 'America/Santa_Isabel', 'America/Santarem', 'America/Santiago', 'America/Santo_Domingo', 'America/Sao_Paulo', 'America/Scoresbysund', 'America/Shiprock', 'America/St_Barthelemy', 'America/St_Johns', 'America/St_Kitts', 'America/St_Lucia', 'America/St_Thomas', 'America/St_Vincent', 'America/Swift_Current', 'America/Tegucigalpa', 'America/Thule', 'America/Thunder_Bay', 'America/Tijuana', 'America/Toronto', 'America/Tortola', 'America/Vancouver', 'America/Virgin', 'America/Whitehorse', 'America/Winnipeg', 'America/Yakutat', 'America/Yellowknife', 'Antarctica/Casey', 'Antarctica/Davis', 'Antarctica/DumontDUrville', 'Antarctica/Macquarie', 'Antarctica/Mawson', 'Antarctica/McMurdo', 'Antarctica/Palmer', 'Antarctica/Rothera', 'Antarctica/South_Pole', 'Antarctica/Syowa', 'Antarctica/Vostok', 'Arctic/Longyearbyen', 'Asia/Aden', 'Asia/Almaty', 'Asia/Amman', 'Asia/Anadyr', 'Asia/Aqtau', 'Asia/Aqtobe', 'Asia/Ashgabat', 'Asia/Ashkhabad', 'Asia/Baghdad', 'Asia/Bahrain', 'Asia/Baku', 'Asia/Bangkok', 'Asia/Beirut', 'Asia/Bishkek', 'Asia/Brunei', 'Asia/Calcutta', 'Asia/Choibalsan', 'Asia/Chongqing', 'Asia/Chungking', 'Asia/Colombo', 'Asia/Dacca', 'Asia/Damascus', 'Asia/Dhaka', 'Asia/Dili', 'Asia/Dubai', 'Asia/Dushanbe', 'Asia/Gaza', 'Asia/Harbin', 'Asia/Ho_Chi_Minh', 'Asia/Hong_Kong', 'Asia/Hovd', 'Asia/Irkutsk', 'Asia/Istanbul', 'Asia/Jakarta', 'Asia/Jayapura', 'Asia/Jerusalem', 'Asia/Kabul', 'Asia/Kamchatka', 'Asia/Karachi', 'Asia/Kashgar', 'Asia/Kathmandu', 'Asia/Katmandu', 'Asia/Kolkata', 'Asia/Krasnoyarsk', 'Asia/Kuala_Lumpur', 'Asia/Kuching', 'Asia/Kuwait', 'Asia/Macao', 'Asia/Macau', 'Asia/Magadan', 'Asia/Makassar', 'Asia/Manila', 'Asia/Muscat', 'Asia/Nicosia', 'Asia/Novokuznetsk', 'Asia/Novosibirsk', 'Asia/Omsk', 'Asia/Oral', 'Asia/Phnom_Penh', 'Asia/Pontianak', 'Asia/Pyongyang', 'Asia/Qatar', 'Asia/Qyzylorda', 'Asia/Rangoon', 'Asia/Riyadh', 'Asia/Saigon', 'Asia/Sakhalin', 'Asia/Samarkand', 'Asia/Seoul', 'Asia/Shanghai', 'Asia/Singapore', 'Asia/Taipei', 'Asia/Tashkent', 'Asia/Tbilisi', 'Asia/Tehran', 'Asia/Tel_Aviv', 'Asia/Thimbu', 'Asia/Thimphu', 'Asia/Tokyo', 'Asia/Ujung_Pandang', 'Asia/Ulaanbaatar', 'Asia/Ulan_Bator', 'Asia/Urumqi', 'Asia/Vientiane', 'Asia/Vladivostok', 'Asia/Yakutsk', 'Asia/Yekaterinburg', 'Asia/Yerevan',	   'Atlantic/Azores', 'Atlantic/Bermuda', 'Atlantic/Canary', 'Atlantic/Cape_Verde', 'Atlantic/Faeroe', 'Atlantic/Faroe', 'Atlantic/Jan_Mayen', 'Atlantic/Madeira', 'Atlantic/Reykjavik', 'Atlantic/South_Georgia', 'Atlantic/St_Helena', 'Atlantic/Stanley',	  'Australia/ACT', 'Australia/Adelaide', 'Australia/Brisbane', 'Australia/Broken_Hill', 'Australia/Canberra', 'Australia/Currie', 'Australia/Darwin', 'Australia/Eucla', 'Australia/Hobart', 'Australia/LHI', 'Australia/Lindeman', 'Australia/Lord_Howe', 'Australia/Melbourne', 'Australia/North', 'Australia/NSW', 'Australia/Perth', 'Australia/Queensland', 'Australia/South', 'Australia/Sydney', 'Australia/Tasmania', 'Australia/Victoria', 'Australia/West', 'Australia/Yancowinna',	  'Europe/Amsterdam', 'Europe/Andorra', 'Europe/Athens', 'Europe/Belfast', 'Europe/Belgrade', 'Europe/Berlin', 'Europe/Bratislava', 'Europe/Brussels', 'Europe/Bucharest', 'Europe/Budapest', 'Europe/Chisinau', 'Europe/Copenhagen', 'Europe/Dublin', 'Europe/Gibraltar', 'Europe/Guernsey', 'Europe/Helsinki', 'Europe/Isle_of_Man', 'Europe/Istanbul', 'Europe/Jersey', 'Europe/Kaliningrad', 'Europe/Kiev', 'Europe/Lisbon', 'Europe/Ljubljana', 'Europe/London', 'Europe/Luxembourg', 'Europe/Madrid', 'Europe/Malta', 'Europe/Mariehamn', 'Europe/Minsk', 'Europe/Monaco', 'Europe/Moscow', 'Europe/Nicosia', 'Europe/Oslo', 'Europe/Paris', 'Europe/Podgorica', 'Europe/Prague', 'Europe/Riga', 'Europe/Rome', 'Europe/Samara', 'Europe/San_Marino', 'Europe/Sarajevo', 'Europe/Simferopol', 'Europe/Skopje', 'Europe/Sofia', 'Europe/Stockholm', 'Europe/Tallinn', 'Europe/Tirane', 'Europe/Tiraspol', 'Europe/Uzhgorod', 'Europe/Vaduz', 'Europe/Vatican', 'Europe/Vienna', 'Europe/Vilnius', 'Europe/Volgograd', 'Europe/Warsaw', 'Europe/Zagreb', 'Europe/Zaporozhye', 'Europe/Zurich', 'Indian/Antananarivo', 'Indian/Chagos', 'Indian/Christmas', 'Indian/Cocos', 'Indian/Comoro', 'Indian/Kerguelen', 'Indian/Mahe', 'Indian/Maldives', 'Indian/Mauritius', 'Indian/Mayotte', 'Indian/Reunion', 'Pacific/Apia', 'Pacific/Auckland', 'Pacific/Chatham', 'Pacific/Easter', 'Pacific/Efate', 'Pacific/Enderbury', 'Pacific/Fakaofo', 'Pacific/Fiji', 'Pacific/Funafuti', 'Pacific/Galapagos', 'Pacific/Gambier', 'Pacific/Guadalcanal', 'Pacific/Guam', 'Pacific/Honolulu', 'Pacific/Johnston', 'Pacific/Kiritimati', 'Pacific/Kosrae', 'Pacific/Kwajalein', 'Pacific/Majuro', 'Pacific/Marquesas', 'Pacific/Midway', 'Pacific/Nauru', 'Pacific/Niue', 'Pacific/Norfolk', 'Pacific/Noumea', 'Pacific/Pago_Pago', 'Pacific/Palau', 'Pacific/Pitcairn', 'Pacific/Ponape', 'Pacific/Port_Moresby', 'Pacific/Rarotonga', 'Pacific/Saipan', 'Pacific/Samoa', 'Pacific/Tahiti', 'Pacific/Tarawa', 'Pacific/Tongatapu', 'Pacific/Truk', 'Pacific/Wake', 'Pacific/Wallis', 'Pacific/Yap',	  'Brazil/Acre', 'Brazil/DeNoronha', 'Brazil/East', 'Brazil/West', 'Canada/Atlantic', 'Canada/Central', 'Canada/East-Saskatchewan', 'Canada/Eastern', 'Canada/Mountain', 'Canada/Newfoundland', 'Canada/Pacific', 'Canada/Saskatchewan', 'Canada/Yukon', 'CET', 'Chile/Continental', 'Chile/EasterIsland', 'CST6CDT', 'Cuba', 'EET', 'Egypt', 'Eire', 'EST', 'EST5EDT', 'Etc/GMT', 'Etc/GMT+0', 'Etc/GMT+1', 'Etc/GMT+2', 'Etc/GMT+3', 'Etc/GMT+4', 'Etc/GMT+5', 'Etc/GMT+6', 'Etc/GMT+7', 'Etc/GMT+8', 'Etc/GMT+9', 'Etc/GMT+10', 'Etc/GMT+11', 'Etc/GMT+12', 'Etc/GMT-0', 'Etc/GMT-1', 'Etc/GMT-2', 'Etc/GMT-3', 'Etc/GMT-4', 'Etc/GMT-5', 'Etc/GMT-6', 'Etc/GMT-7', 'Etc/GMT-8', 'Etc/GMT-9', 'Etc/GMT-10', 'Etc/GMT-11', 'Etc/GMT-12', 'Etc/GMT-13', 'Etc/GMT-14', 'Etc/GMT0', 'Etc/Greenwich', 'Etc/UCT', 'Etc/Universal', 'Etc/UTC', 'Etc/Zulu', 'Factory', 'GB', 'GB-Eire', 'GMT', 'GMT+0', 'GMT-0', 'GMT0', 'Greenwich', 'Hongkong', 'HST', 'Iceland', 'Iran', 'Israel', 'Jamaica', 'Japan', 'Kwajalein', 'Libya', 'MET', 'Mexico/BajaNorte', 'Mexico/BajaSur', 'Mexico/General', 'MST', 'MST7MDT', 'Navajo', 'NZ', 'NZ-CHAT', 'Poland', 'Portugal', 'PRC', 'PST8PDT', 'ROC', 'ROK', 'Singapore', 'Turkey', 'UCT', 'Universal', 'US/Alaska', 'US/Aleutian', 'US/Arizona', 'US/Central', 'US/East-Indiana', 'US/Eastern', 'US/Hawaii', 'US/Indiana-Starke', 'US/Michigan', 'US/Mountain', 'US/Pacific', 'US/Pacific-New', 'US/Samoa', 'UTC', 'W-SU', 'WET');

		$oTimezoneField = Admin_Form_Entity::factory('Select');
		$oTimezoneField
			->name('timezone')
			->caption(Core::_('Site.timezone'))
			->divAttr(array('style' => 'float: left'))
			->style("width: 110px")
			->options(
				array('' => Core::_('site.default')) + array_combine($aTimezone, $aTimezone)
			)
			->value($this->_object->timezone);

		$oMainTab->addAfter($oTimezoneField, $oLocaleField);

		$oMainTab->addAfter($oSeparatorField, $oTimezoneField);

		$this->getField('max_size_load_image')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 320px;");

		$oMaxSizeLoadImageBig = $this->getField('max_size_load_image_big');

		$oMaxSizeLoadImageBig->divAttr(array('style' => 'float: left;'))
			->style("width: 320px;");

		$oMainTab->addAfter($oSeparatorField, $oMaxSizeLoadImageBig);

		$oAdminEmail = $this->getField('admin_email');

		$oAdminEmail->divAttr(array('style' => 'float: left;'))
			->style("width: 320px;");

		$oMainTab->addAfter($oSeparatorField, $oAdminEmail);

		$oMainTab->delete(
			$this->getField('notes')
		);

		$this->getField('uploaddir')
			->divAttr(array('style' => 'float: left;'))
			->style("width: 320px;");

		$oNestingLevel = $this->getField('nesting_level');

		$oNestingLevel->divAttr(array('style' => 'float: left;'))
			->style("width: 110px;");

		$oMainTab->addAfter($oSeparatorField, $oNestingLevel);

		// Добавляем новое поле типа файл
		$oIcoFileField = Admin_Form_Entity::factory('File');

		$sFormPath = $this->_Admin_Form_Controller->getPath();
		$windowId = $this->_Admin_Form_Controller->getWindowId();

		$oIcoFileField
			->type("file")
			->caption(Core::_('Site.ico_files_uploaded'))
			->style("width: 400px;")
			->name("icofile")
			->id("icofile")
			->largeImage(
				array(
					'path' => is_file($this->_object->getIcoFilePath())
						? $this->_object->getIcoFileHref()
						: (
							is_file($this->_object->getPngFilePath())
								? $this->_object->getPngFileHref()
								: ''
						),
					'show_params' => FALSE,
					'delete_onclick' => "$.adminLoad({path: '{$sFormPath}', additionalParams: 'hostcms[checked][{$this->_datasetId}][{$this->_object->id}]=1', action: 'deleteIcoFile', windowId: '{$windowId}'}); return false",
				)
			)
			->smallImage(
				array(
					'show' => FALSE
				)
			);

		$oMainTab->add($oIcoFileField);

		// Переопределяем 3 поля со списком узлов структуры

		$oSiteTabErrors->delete(
			 $this->getField('error404') // Удаляем стандартный <input> structure_id
		)->delete(
			 $this->getField('error403') // Удаляем стандартный <input> structure_id
		)->delete(
			 $this->getField('closed') // Удаляем стандартный <input> structure_id
		);

		$Structure_Controller_Edit = new Structure_Controller_Edit($this->_Admin_Form_Action);

		$aStructureData = array(' … ') + $Structure_Controller_Edit->fillStructureList($this->_object->id);

		$oSelect_404 = Admin_Form_Entity::factory('Select');
		$oSelect_404
			->options(
				$aStructureData
			)
			->name('error404')
			->value($this->_object->error404)
			->caption(Core::_('Site.error404'));

		$oSiteTabErrors->addAfter(
				$oSelect_404, $this->getField('error')
			);

		$oSelect_403 = Admin_Form_Entity::factory('Select');
		$oSelect_403
			->options(
				$aStructureData
			)
			->name('error403')
			->value($this->_object->error403)
			->caption(Core::_('Site.error403'));
		$oSiteTabErrors->addAfter(
				$oSelect_403, $oSelect_404
			);

		$oSelect_503 = Admin_Form_Entity::factory('Select');
		$oSelect_503
			->options(
				$aStructureData
			)
			->name('closed')
			->value($this->_object->closed)
			->caption(Core::_('Site.closed'));
		$oSiteTabErrors->addAfter(
				$oSelect_503, $oSelect_403
			);


		$this->title($this->_object->id
			? Core::_('Site.site_edit_site_form_title')
			: Core::_('Site.site_add_site_form_title'));

		return $this;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @hostcms-event Site_Controller_Edit.onAfterRedeclaredApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		parent::_applyObjectProperty();

		if(
			// Поле файла существует
			!is_null($aFileData = Core_Array::getFiles('icofile', NULL))
			// и передан файл
			&& intval($aFileData['size']) > 0)
		{
			// ICO
			if (Core_File::isValidExtension($aFileData['name'], array('ico')))
			{
				$this->_object->saveIcoFile($aFileData['tmp_name']);
			}
			// PNG
			elseif (Core_File::isValidExtension($aFileData['name'], array('png')))
			{
				$this->_object->savePngFile($aFileData['tmp_name']);
			}
			else
			{
				$this->addMessage(
					Core_Message::get(
						Core::_('Core.extension_does_not_allow', Core_File::getExtension($aFileData['name'])),
						'error'
					)
				);
			}
		}

		Core_Event::notify(get_class($this) . '.onAfterRedeclaredApplyObjectProperty', $this, array($this->_Admin_Form_Controller));
	}

	/**
	 * Fill sites list
	 * @return array
	 */
	public function fillSites()
	{
		$aSites = Core_Entity::factory('Site')->findAll();

		$aReturn = array(' … ');

		foreach($aSites as $oSite)
		{
			$aReturn[$oSite->id] = $oSite->name;
		}

		return $aReturn;
	}

}