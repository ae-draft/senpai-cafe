<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Сайты".
 * 
 * Файл: /modules/Site/Site.class.php
 * 
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class site
{
	var $current_alias_cache;

	/**
	* Кэш данных о сайтах
	*
	* @var string
	*/
	var $CacheSite;

	/**
	 * Функция обратного вызова для отображения блока
	 * на основной странице центра администрирования. 
	 * 
	 */
	function AdminMainPage()
	{
		
	}
	
	/**
	* Вставка/обновление информации о сайте
	*
	* @param array $param массив параметров сайта
	* - $param['site_id'] идентификатор сайта, информация о котором обновляется
	* - $param['site_name'] название добавляемого/обновляемого сайта
	* - $param['site_is_active'] параметр, определяющий  активность (доступность) сайта (0 - неактивен (по умолчанию), 1 – активен)
	* - $param['site_coding'] кодировка, используемая сайтом
	* - $param['site_order'] порядок сортировки сайта в административной части
	* - $param['site_locale'] используемая локаль
	* - $param['site_timezone'] часовой пояс
	* - $param['site_max_size_load_image'] максимальный размер малых изображений загружаемых на сайт
	* - $param['site_max_size_load_image_big'] максимальный размер больших картинок загружаемых на сайт
	* - $param['site_admin_email'] электронный адрес администратора сайта
	* - $param['site_chmod'] права доступа к файлам
	* - $param['site_files_chmod'] права доступа к файлам
	* - $param['site_date_format'] формат даты
	* - $param['site_date_time_format'] формат даты-времени
	* - $param['site_error'] режим вывода ошибок
	* - $param['site_error404'] страница, отображаемая при возникновении 404 ошибки (страница не найдена),  если страница не указана, производится редирект на главную страницу
	* - $param['site_access_denied'] страница, отображаемая при попытке доступа пользователя, не имеющего права доступа
	* - $param['site_robots'] содержимое файла robots.txt для данного сайта
	* - $param['site_key'] регистрационный ключ
	* - $param['site_is_close'] идентификатор узла структуры, содержащего страницу, отображаемую при отключении сайта администратором
	* - $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	* - $param['site_safe_email'] параметр, определяющий защищен e-mail на страницах сайта от просмотра спам-ботами или нет (1 - защищен (по умолчанию), 0 - не защищен)
	* - $param['site_html_cache_use'] использовать ли кэш на сайте
	* - $param['site_html_cache_with'] список страниц которые должны кэшироваться
	* - $param['site_html_cache_without'] список страниц которые не должны кэшироваться
	* - $param['site_html_cache_clear_probability'] параметр, определяющий вероятность очистки кэша
	* - $param['site_send_attendance_report'] параметр, устанавливающий ежедневную отправку отчета о посещаемости сайта (1 - отправлять отчет (по умолчанию), 0 - не отправлять)
	* - $param['site_uploaddir'] параметр, определяющий путь к разделу, в котором будут сохраняться загруженные файлы. Первым символом значения параметра не может быть символ '/', данным символом обязательно должно заканчиваться значение. Значение по умолчанию 'UPLOAD/'.
	* - $param['site_nesting_level'] число уровней вложенности директорий для хранения файлов сущностей системы (основных и дополнительных свойств типа "Файл" информационных элементов, основных и дополнительных свойств типа "Файл" информационных групп, дополнительных свойств типа "Файл" узлов структуры).
	*   <br /> по умолчанию равен 3.   
	*     
	* <code>
	* <?php 
	* $site = new site();
	*
	* $param['site_name'] = 'Новый сайт';
	*
	* $newid = $site->insert_site($param);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return int идентификатор добавленного или обновленного сайта
	*/
	function insert_site($param)
	{
		if (Core_Type_Conversion::toInt($param['site_id']) == 0)
		{
			$site_id = NULL;
		}
		
		$site = Core_Entity::factory('Site', $site_id);
		
		$site->name = Core_Type_Conversion::toStr($param['site_name']);
		$site->active = Core_Type_Conversion::toInt($param['site_is_active']);
		$site->coding = Core_Type_Conversion::toStr($param['site_coding']);
		$site->sorting = Core_Type_Conversion::toInt($param['site_order']);
		$site->locale = Core_Type_Conversion::toStr($param['site_locale']);
		$site->timezone = Core_Type_Conversion::toStr($param['site_timezone']);
		$site->max_size_load_image = Core_Type_Conversion::toInt($param['site_max_size_load_image']);
		$site->max_size_load_image_big = Core_Type_Conversion::toInt($param['site_max_size_load_image_big']);
		$site->admin_email = Core_Type_Conversion::toStr($param['site_admin_email']);
		$site->send_attendance_report = Core_Type_Conversion::toInt($param['site_send_attendance_report']);
		$site->chmod = Core_Type_Conversion::toStr($param['site_chmod']);
		$site->files_chmod = Core_Type_Conversion::toStr($param['site_files_chmod']);
		$site->date_format = Core_Type_Conversion::toStr($param['site_date_format']);
		$site->date_time_format = Core_Type_Conversion::toStr($param['site_date_time_format']);
		$site->error = Core_Type_Conversion::toStr($param['site_error']);
		$site->error404 = Core_Type_Conversion::toStr($param['site_error404']);
		$site->error403 = Core_Type_Conversion::toInt($param['site_access_denied']);
		$site->robots = Core_Type_Conversion::toStr($param['site_robots']);
		$site->key = Core_Type_Conversion::toStr($param['site_key']);
		$site->closed = Core_Type_Conversion::toInt($param['site_is_close']);
		$site->safe_email = Core_Type_Conversion::toInt($param['site_safe_email']);
		$site->html_cache_use = Core_Type_Conversion::toInt($param['site_html_cache_use']);
		$site->html_cache_with = Core_Type_Conversion::toStr($param['site_html_cache_with']);
		$site->html_cache_without = Core_Type_Conversion::toStr($param['site_html_cache_without']);
		$site->css_left = Core_Type_Conversion::toStr($param['site_css_left']);
		$site->css_right = Core_Type_Conversion::toStr($param['site_css_right']);

		if (isset($param['site_html_cache_clear_probability']) && $param['site_html_cache_clear_probability'] >=0)
		{
			$site->html_cache_clear_probability = Core_Type_Conversion::toStr($param['site_html_cache_clear_probability']);
		}

		$site->notes = Core_Type_Conversion::toStr($param['notes']);
		$site->uploaddir = Core_Type_Conversion::toStr($param['site_uploaddir']);
		$site->nesting_level = Core_Type_Conversion::toStr($param['site_nesting_level']);

		if (is_null($site_id) && Core_Type_Conversion::toInt($param['users_id']))
		{
			$site->user_id = $param['users_id'];
		}
		
		$site->save();
		
		/* Очистка файлового кэша*/
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SITE';
			$cache->DeleteCacheItem($cache_name, $site_id);

			$cache_name = 'KEYS';
			$cache->DeleteCacheItem($cache_name, 'key');
		}
		
		return $site->id;
	}

	/**
	* Метод вставки/обновления информации об алиасе (домене) сайта
	*
	* @param int $type – параметр, определяющий производится вставка или обновление информации об алиасе сайта (0 – вставка, 1 - обновление)
	* @param int $alias_id – идентификатор обновляемого алиаса сайта (при вставке алиаса равен 0)
	* @param int $site_id – идентификатор сайта, для которого добавляется/обновляется алиас
	* @param string $alias_name – название алиаса сайта
	* @param int $alias_current – параметр, указывающий является ли алиас сайта основным (1 – основной, 0 - неосновной)
	* @param int $users_id идентификатор пользователя, если false - берется текущий пользователь.
	* <code>
	* <?php 
	* $site = new site();
	*
	* $type = 0;
	* $site_id = 4;
	* $alias_id = 0;
	* $alias_current = 1;
	* $alias_name = 'Новый алиас';
	*
	* $newid = $site->insert_alias($type, $alias_id, $site_id, $alias_name, $alias_current, $users_id = false);
	* 
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return int идентификатор добавляемого/обновляемого алиаса сайта
	*/
	function insert_alias($type, $alias_id, $site_id, $alias_name, $alias_current, $users_id = false)
	{
		if (intval($type) == 0)
		{
			$alias_id = NULL;
		}

		$site_alias = Core_Entity::factory('Site_Alias', $alias_id);
		
		$site_alias->name = $alias_name;
		
		if ($alias_current)
		{
			$site_alias->setCurrent();
		}
		else
		{
			$site_alias->current = 0;
		}
		
		if (is_null($alias_id) && $users_id)
		{
			$site_alias->user_id = $users_id;
		}
		
		$site_alias->save();
		
		Core_Entity::factory('Site', $site_id)
			->add($site_alias);
		
		return $site_alias->id;
	}

	/**
	* Выбор всех сайтов, поддерживаемых системой управления
	* @param array $param массив дополнительных параметров, необязательный параметр
	* - $param['limit0'] - с какого совпадения начинать отбор, по умолчанию 0
	* - $param['limit1'] - число элементов для выбора, по умолчанию не ограничено
	* - $param['users_type_id'] - группа пользователей центра администрирования, для которой выбираются доступные сайты
	* <code>
	* <?php 
	* $site = new site();
	*
	* $resource = $site->SelectSites();
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource
	*/
	function SelectSites($param = array())
	{
		$param = Core_Type_Conversion::toArray($param);
		
		$queryBuilder = Core_QueryBuilder::select
		(
				array('sites.id', 'site_id'),
				array('sites.name', 'site_name'),
				array('sites.active', 'site_is_active'),
				array('sites.coding', 'site_coding'),
				array('sites.sorting', 'site_order'),
				array('sites.locale', 'site_locale'),
				array('sites.timezone', 'site_timezone'),
				array('sites.max_size_load_image', 'site_max_size_load_image'),
				array('sites.max_size_load_image_big', 'site_max_size_load_image_big'),
				array('sites.admin_email', 'site_admin_email'),
				array('sites.send_attendance_report', 'site_send_attendance_report'),				
				array('sites.chmod', 'site_chmod'),				
				array('sites.files_chmod', 'site_files_chmod'),
				array('sites.date_format', 'site_date_format'),
				array('sites.date_time_format', 'site_date_time_format'),
				array('sites.error', 'site_error'),
				array('sites.error404', 'site_error404'),
				array('sites.error403', 'site_access_denied'),
				array('sites.robots', 'site_robots'),
				array('sites.key', 'site_key'),
				array('sites.user_id', 'users_id'),
				array('sites.closed', 'site_is_close'),
				array('sites.safe_email', 'site_safe_email'),
				array('sites.html_cache_use', 'site_html_cache_use'),
				array('sites.html_cache_with', 'site_html_cache_with'),
				array('sites.html_cache_without', 'site_html_cache_without'),
				array('sites.css_left', 'site_css_left'),
				array('sites.css_right', 'site_css_right'),
				array('sites.html_cache_clear_probability', 'site_html_cache_clear_probability'),
				array('sites.notes', 'site_notes'),
				array('sites.uploaddir', 'site_uploaddir'),
				array('sites.nesting_level', 'site_nesting_level')
		)
		->from('sites')
		->where('deleted', '=', '0')
		->orderBy('sorting');
		
		if (isset($param['limit0']) && isset($param['limit1']))
		{
			$queryBuilder->limit($param['limit0'], $param['limit1']);
		}
		
		// Если передан пользователь - определим его тип
		if (isset($param['users_type_id']) && isset($_SESSION['valid_user']))
		{
			$oUser = Core_Entity::factory('User')->getByLogin($_SESSION['valid_user']);

			$user_is_superuser = $oUser->superuser == 1;
		}
		else 
		{
			$user_is_superuser = FALSE;
		}
		
		// Если группа пользователей не передана или пользователь суперюзер - выбираем из всех сайтов
		if (!isset($param['users_type_id']) || $user_is_superuser)
		{
			$queryBuilder
				->orderBy('name');
		}
		else
		{
			$users_type_id = intval($param['users_type_id']);

			$queryBuilder
				->join('user_modules', 'sites.id', '=', 'user_modules.site_id')
				->where('user_modules.user_group_id', '=', $users_type_id)			
				->groupBy('sites.id');
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение данных о сайте
	*
	* @param int $site_id - идентификатор сайта, -1 выбрать все
	* <code>
	* <?php 
	* $site = new site();
	*
	* $site_id = 1;
	*
	* $resource = $site->select_site($site_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource
	*/
	function select_site($site_id)
	{
		$site_id = intval($site_id);
		
		$queryBuilder = Core_QueryBuilder::select
		(
			array('id', 'site_id'),
			array('name', 'site_name'),
			array('active', 'site_is_active'),
			array('coding', 'site_coding'),
			array('sorting', 'site_order'),
			array('locale', 'site_locale'),
			array('timezone', 'site_timezone'),
			array('max_size_load_image', 'site_max_size_load_image'),
			array('max_size_load_image_big', 'site_max_size_load_image_big'),
			array('admin_email', 'site_admin_email'),
			array('send_attendance_report', 'site_send_attendance_report'),				
			array('chmod', 'site_chmod'),				
			array('files_chmod', 'site_files_chmod'),
			array('date_format', 'site_date_format'),
			array('date_time_format', 'site_date_time_format'),
			array('error', 'site_error'),
			array('error404', 'site_error404'),
			array('error403', 'site_access_denied'),
			array('robots', 'site_robots'),
			array('key', 'site_key'),
			array('user_id', 'users_id'),
			array('closed', 'site_is_close'),
			array('safe_email', 'site_safe_email'),
			array('html_cache_use', 'site_html_cache_use'),
			array('html_cache_with', 'site_html_cache_with'),
			array('html_cache_without', 'site_html_cache_without'),
			array('css_left', 'site_css_left'),
			array('css_right', 'site_css_right'),
			array('html_cache_clear_probability', 'site_html_cache_clear_probability'),
			array('notes', 'site_notes'),
			array('uploaddir', 'site_uploaddir'),
			array('nesting_level', 'site_nesting_level')
		)
		->from('sites')
		->where('deleted', '=', '0');

		if ($site_id != -1)
		{
			$queryBuilder->where('id', '=', $site_id);
		}

		return $queryBuilder->execute()->getResult();	
	}

	/**
	* Получение информации об алиасе (домене) сайта
	*
	* @param int $alias_id – идентификатор алиаса, о котором необходимо получить информацию, -1 - выбрать все
	* <code>
	* <?php 
	* $site = new site();
	*
	* $alias_id = 2;
	*
	* $resource = $site->select_alias($alias_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource данные об алиасе сайта
	*/
	function select_alias($alias_id)
	{
		$alias_id = intval($alias_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'alias_id'),
			array('name', 'alias_name'),
			array('site_id', 'site_id'),
			array('user_id', 'users_id'),
			array('current', 'alias_current')
		)
		->from('site_aliases')
		->where('deleted', '=', '0');

		if ($alias_id != -1)
		{
			$queryBuilder->where('id', '=', $alias_id);
		}
		
		return $queryBuilder->execute()->getResult();
	}

	/**
	* Удаление информации о сайте
	*
	* @param int $site_id – идентификатор удаляемого сайта
	* <code>
	* <?php 
	* $site = new site();
	*
	* $site_id = 2;
	*
	* $resource = $site->del_site($site_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 	print_r($row);
	* }
	* ?>
	* </code>
	* @return resource рузельтат выполнения запроса
	*/
	function del_site($site_id)
	{
		$site_id = intval($site_id);
		
		$site = Core_Entity::factory('Site', $site_id);

		// WARNING: Заменить нижеследующий код на новые методы
		
		// Удаляем службы поддержки
		if (class_exists('helpdesk'))
		{
			$helpdesk = new helpdesk();

			// Получаем информацию обо всех службах поддержки
			$helpdesk_res = $helpdesk->GetAllHelpdesk($site_id);

			if (mysql_num_rows($helpdesk_res) > 0)
			{
				while ($helpdesk_row = mysql_fetch_assoc($helpdesk_res))
				{
					$helpdesk->DeleteHelpdesk($helpdesk_row['helpdesk_id']);
				}
			}
		}
		
		/* Удаляем макеты сайта */
		if (class_exists('templates'))
		{
			$templates = new templates();

			// Группы макетов
			$resource = $templates->GetAllTemplatesGroups(false, $site_id);

			if (is_array($resource) && count($resource) > 0)
			{
				foreach ($resource as $row)
				{
					$templates->DelTemplatesGroup($row['templates_group_id']);
				}
			}
			
			// Макеты
			$resource = $templates->GetAllTemplates(false, $site_id);
			
			if (is_array($resource) && count($resource) > 0)
			{
				foreach ($resource as $row)
				{
					$templates->del_templates($row['templates_id']);
				}
			}
		}
		
		/* Удаляем шаблоны страниц сайта */
		if (class_exists('templates'))
		{
			$templates = new templates();

			// Группы шаблонов страниц
			$resource = $templates->GetAllDataTemplatesGroups(false, $site_id);
			
			if (is_array($resource) && count($resource) > 0)
			{
				foreach ($resource as $row)
				{
					$templates->DelDataTemplatesGroup($row['data_templates_group_id']);
				}
			}
			
			// Шаблоны страниц
			$resource = $templates->GetAllDataTemplates(false, $site_id);
			
			if (is_array($resource) && count($resource) > 0)
			{
				foreach ($resource as $row)
				{
					$templates->del_data_templates($row['data_templates_id']);
				}
			}
		}
		
		
		/* Проверяем подключен ли модуль разделы меню*/
		if (class_exists('menu'))
		{
			$menu = new menu();

			/* Удаляем разделы меню*/
			$resource = $menu->GetAllMenu($site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$menu->DeleteMenu($row['menu_id']);
				}
			}
		}

		/* Проверяем существует ли модуль статистики и является ли он активным*/
		if (class_exists('counter'))
		{
			/* Удаляем статистические данные*/
			$Counter = new counter();
			$Counter->DelStatInfomationForSite($site_id);
		}

		/* Проверяем подключен ли модуль Структура сайта*/
		if (class_exists('Structure'))
		{
			$Structure = & singleton('Structure');

			/* Удаляем содержимое структуры сайта*/
			$resource = $Structure->GetAllStructure($site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$Structure->DeleteStructure($row['structure_id']);
				}
			}

			/* Удаляем доп. свойства*/
			$resource = $Structure->GetAllStructureProperties($site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$Structure->DelStructurePropertys($row['structure_propertys_id']);
				}
			}
			
			$file = & singleton('File');
									
			// Получаем информацию о сайте
			$site_row = $this->GetSite($site_id);
				
			$uploaddir = $site_row['site_uploaddir'];
									 
			$structure_site_dir = CMS_FOLDER . $uploaddir . 'structure_site_' . $site_id;
			
			$file->DeleteDir($structure_site_dir);			
		}

		/* Проверяем подключен ли модуль Инфосистемы*/
		if (class_exists('InformationSystem'))
		{
			$InformationSystem = new InformationSystem;

			/* Выбираем все информационные системы для данного сайта*/
			$resource = $InformationSystem->GetAllInformationSystems($site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$InformationSystem->DelInformationSystem($row['information_systems_id']);
				}
			}
		}

		/* Проверяем подключен ли модуль Страницы и документы*/
		if (class_exists('documents'))
		{
			$documents = new documents();
			
			// Выбираем все группы документов этого сайта
			$resource = $documents->select_documents_dir(-1, $site_id);
			
			if($resource)
			{
				while($row = mysql_fetch_assoc($resource))
				{
					$documents->del_documents_dir($row['documents_dir_id']);
				}
			}
			
			// Выбираем все документы этого сайта
			$resource = $documents->select_documents(-1, $site_id);
			
			if($resource)
			{
				while($row = mysql_fetch_assoc($resource))
				{
					$documents->del_documents($row['documents_id']);
				}
			}
			
			// Статусы документов
			$resource = $documents->select_status(-1, $site_id);
			
			if($resource)
			{
				while($row = mysql_fetch_assoc($resource))
				{
					$documents->delete_status($row['documents_status_id']);
				}
			}
		}

		/* Проверяем подключен ли модуль Интернет-магазин*/
		if (class_exists('shop'))
		{
			$shop = & singleton('shop');

			/* Выбираем все магазины для данного сайта*/
			$resource = $shop->GetAllShops($site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$shop->DeleteShop($row['shop_shops_id']);
				}
			}
		}

		/* Проверяем подключен ли модуль опросов*/
		if (class_exists('polls'))
		{
			$polls = new polls();

			/* Выбираем все магазины для данного сайта*/
			$resource = $polls->GetAllPollsGroups($site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$polls->DelPollsGroup($row['poll_group_id']);
				}
			}
		}

		/* Проверяем подключен ли модуль Пользователи сайта */
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');

			/* Выбираем всех пользователей для данного сайта*/
			$resource = $SiteUsers->GetAllUsers(array('site_id' => $site_id));
			
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$SiteUsers->DeleteSiteUser($row['site_users_id']);
				}
			}
			
			/* Выбираем все свойства пользователей данного сайта */
			$resource = $SiteUsers->SelectExtraProperties($site_id);
			
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$SiteUsers->DeleteExtraProperty($row['site_users_extra_property_id']);
				}
			}
			
			/* Выбираем все группы пользователей данного сайта */
			$resource = $SiteUsers->SelectSiteUsersGroups(array('site_id' => $site_id));
			
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$SiteUsers->DelUserGroup($row['site_users_group_id']);
				}
			}
		}
		
		/* Проверяем подключен ли модуль почтовых рассылок */
		if (class_exists('Maillist'))
		{
			$Maillist = new Maillist();
			
			/* Выбираем все рассылки */
			$resource = $Maillist->SelectMaillist(-1, array('site_id' => $site_id));
			
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$Maillist->DelMaillist($row['maillist_id']);
				}
			}
		}

		/* Проверяем подключен ли модуль списков */
		if (class_exists('lists'))
		{
			$lists = new lists();

			// Получаем списки сайта
			$lists_res = $lists->GetAllListsForSite($site_id);

			if (mysql_num_rows($lists_res) > 0)
			{
				while ($lists_row = mysql_fetch_assoc($lists_res))
				{
					$lists->del_lists($lists_row['lists_id']);
				}
			}
		}
		
		/* Проверяем подключен ли модуль поиска*/
		if (class_exists('Search'))
		{
			$Search = new Search();
			$Search->DeleteSearchInfomationForSite($site_id);
		}

		/* Проверяем подключен ли модуль форумов*/
		if (class_exists('Forums'))
		{
			$Forums = new Forums();

			/* Выбираем все магазины для данного сайта*/
			$resource = $Forums->GetAllConferences($site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$Forums->DeleteConference($row['forums_conference_id']);
				}
			}
		}

		/* Удаляем пользователей, группы пользователей и права доступа сайта*/
		$UserAccess = new user_access;

		/* Выбираем все типы пользователей для данного сайта*/
		$resource = $UserAccess->GetAllUserTypes($site_id);
		if (mysql_num_rows($resource) > 0)
		{
			while ($row = mysql_fetch_assoc($resource))
			{
				$UserAccess->del_user_type($row['users_type_id']);
			}
		}

		/* Проверяем подключен ли модуль Формы*/
		if (class_exists('Forms'))
		{
			$Forms = new Forms();

			// Выбираем все формы сайта
			$form_res = $Forms->GetAllForms($site_id);

			if (mysql_num_rows($form_res))
			{
				while ($form_row = mysql_fetch_assoc($form_res))
				{
					$Forms->DelForms($form_row['forms_id']);
				}
			}
		}

		/* Проверяем подключен ли модуль рекламы */
		if (class_exists('Advertisement'))
		{
			$Advertisement = new Advertisement();

			/* Выбираем все баннеры для данного сайта */
			$resource = $Advertisement->GetAllBanners($site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$Advertisement->DeleteBanner($row['advertisement_id']);
				}
			}
			
			/* Выбираем все группы для данного сайта */
			$resource = $Advertisement->SelectBannersGroup(false, $site_id);
			if (mysql_num_rows($resource) > 0)
			{
				while ($row = mysql_fetch_assoc($resource))
				{
					$Advertisement->DelBannersGroup($row['advertisement_group_id']);
				}
			}
		}
		
		/* Очистка файлового кэша*/
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SITE';
			$cache->DeleteCacheItem($cache_name, $site_id);

			$cache_name = 'KEYS';
			$cache->DeleteCacheItem($cache_name, 'key');
		}

		$DataBase = & singleton('DataBase');
		$DataBase->query("DELETE FROM `user_group_action_accesses` WHERE `site_id` = '{$site_id}'");

		$site->markDeleted();
		
		return TRUE;
	}

	/**
	* Метод преобразования алиаса (домена) сайта в основной
	*
	* @param int $alias_id – – идентификатор алиаса сайта, который необходимо сделать основным
	* <code>
	* <?php 
	* $site = new site();
	*
	* $alias_id = 7;
	*
	* $site->del_alias($alias_id);
	* ?>
	* </code>
	* @return bool результат работы ф-ции
	*/
	function del_alias($alias_id)
	{
		$alias_id = intval($alias_id);
		Core_Entity::factory('Site_Alias', $alias_id)->markDeleted();

		return TRUE;
	}

	/**
	* Метод установки алиаса (домена) сайта в статус "основной"
	*
	* @param int $site_id – идентификатор сайта
	* @param int $alias_id – идентификатор алиаса сайта, который необходимо сделать основным
	* <code>
	* <?php 
	* $site = new site();
	*
	* $alias_id = 8;
	* $site_id = 1;
	* 
	* $site->current_alias($site_id, $alias_id);
	* ?>
	* </code>
	* @return true
	*/
	function current_alias($site_id, $alias_id)
	{
		$alias_id = intval($alias_id);
		
		Core_Entity::factory('Site_Alias', $alias_id)->setCurrent();
		
		return TRUE;
	}

	/**
	* Удаляет маску "*." из адреса домена
	*
	* @param string $str
	* <code>
	* <?php 
	* $site = new site();
	*
	* $str = '*.test3';
	*
	* echo $site->ReplaceMask($str);
	* ?>
	* </code>
	* @return string
	*/
	function ReplaceMask($str)
	{
		return Core_Entity::factory('Site_Alias')->replaceMask($str);
	}

	/**
	* Определение основного алиаса сайта
	*
	* @param int $site_id – идентификатор сайта
	* <code>
	* <?php 
	* $site = new site();
	*
	* $site_id = 1;
	*
	* $result = $site->GetCurrentAlias($site_id);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	* @return string имя основного алиаса сайта или false – в случае если основной алиас не найден
	*/
	function GetCurrentAlias($site_id)
	{
		$site_id = intval($site_id);

		if (isset($this->current_alias_cache[$site_id]))
		{
			return $this->current_alias_cache[$site_id];
		}
		
		$oSiteAlias = Core_Entity::factory('Site', $site_id)->getCurrentAlias();
		
		if($oSiteAlias)
		{
			$this->current_alias_cache[$site_id] = $oSiteAlias->name;		
			return $oSiteAlias->name;
		}

		return FALSE;
	}

	/**
	* Получение данных об алиасе сайта
	*
	* @param string $alias_name
	* @param array $param ассоциативный массив параметров
	* - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	* - bool $param['use_star'] - использовать ли подставновки *.site.ru, по умолчанию true
	* <code>
	* <?php 
	* $site = new site();
	*
	* $alias_name = 'test3';
	*
	* $row = $site->GetAlias($alias_name);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed array данные об алиасе, false - если алиас не найден
	*/
	function GetAlias($alias_name, $param = array())
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['use_star']))
		{
			$param['use_star'] = true;
		}

		/* Кэширование*/
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'DOMEN';

			if (($in_cache = $cache->GetCacheContent($alias_name, $cache_name))	&& $in_cache)
			{
				return $in_cache['value'];
			}
		}
		
		$oSiteAlias = Core_Entity::factory('Site_Alias')->getByName($alias_name);
		
		$alias_row = NULL;
		
		if (!is_null($oSiteAlias))
		{
			$alias_row = array(
				'alias_id' => $oSiteAlias->id,
				'site_id' => $oSiteAlias->site_id,
				'alias_name' => $oSiteAlias->name,
				'alias_current' => $oSiteAlias->current,
				'users_id' => $oSiteAlias->user_id
			);
		}
		elseif($param['use_star'])
		{
			// Удаляем все переданные *. если они были
			$new_alias_name = $this->ReplaceMask($alias_name);

			if (mb_strpos($alias_name, '*.') === FALSE) // Если в переданном алиасе небыло *.
			{
				$new_alias_name = "*." . $alias_name;
				$alias_row = $this->GetAlias($new_alias_name);
			}
			// Если в пути осталась хоть одна точка
			elseif (mb_strpos($new_alias_name, '.') !== FALSE)
			{
				$new_alias_name = "*.".mb_substr($new_alias_name,mb_strpos($new_alias_name,'.')+1);
				$alias_row = $this->GetAlias($new_alias_name);
			}
		}
		
		// Если добавлено кэширование
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($alias_name, $alias_row, $cache_name);
		}
	
		return $alias_row;
	}

	/**
	* Получение данных о сайте по его идентификатору
	*
	* @param int $site_id – идентификатор сайта (по умолчанию равен идентификатору текущего сайта)
	* @param array $param ассоциативный массив параметров
	* - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	* <code>
	* <?php 
	* $site = new site();
	*
	* $site_id = 1;
	*
	* $row = $site->GetSite($site_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return array ассоциативный массив с информацией о сайте, если такой сайт существует или false – в противном случае
	*/
	function GetSite($site_id = CURRENT_SITE, $param = array())
	{
		$site_id = intval($site_id);
		$param = Core_Type_Conversion::toArray($param);
		
		if (!isset($param['cache_off']) && isset($this->CacheSite[$site_id]))
		{
			return $this->CacheSite[$site_id];
		}

		/* Кэширование*/
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'SITE';

			if ($in_cache = $cache->GetCacheContent($site_id, $cache_name))
			{
				$this->CacheSite[$site_id] = $in_cache['value'];
				return $in_cache['value'];
			}
		}
		
		$oSite = Core_Entity::factory('Site', $site_id);
		
		if(is_null($oSite->name))
		{
			return FALSE;
		}
		else
		{
			$site_row = array(
				'site_id' => $oSite->id,
				'site_name' => $oSite->name,
				'site_is_active' => $oSite->active,
				'site_coding' => $oSite->coding,
				'site_order' => $oSite->sorting,
				'site_locale' => $oSite->locale,
				'site_timezone' => $oSite->timezone,
				'site_max_size_load_image' => $oSite->max_size_load_image,
				'site_max_size_load_image_big' => $oSite->max_size_load_image_big,
				'site_admin_email' => $oSite->admin_email,
				'site_send_attendance_report' => $oSite->send_attendance_report,
				'site_chmod' => $oSite->chmod,
				'site_files_chmod' => $oSite->files_chmod,
				'site_date_format' => $oSite->date_format,
				'site_date_time_format' => $oSite->date_time_format,
				'site_error' => $oSite->error,
				'site_error404' => $oSite->error404,
				'site_access_denied' => $oSite->error403,
				'site_robots' => $oSite->robots,
				'site_key' => $oSite->key,
				'users_id' => $oSite->user_id,
				'site_is_close' => $oSite->closed,
				'site_safe_email' => $oSite->safe_email,
				'site_html_cache_use' => $oSite->html_cache_use,
				'site_html_cache_with' => $oSite->html_cache_with,
				'site_html_cache_without' => $oSite->html_cache_without,
				'site_css_left' => $oSite->css_left,
				'site_css_right' => $oSite->css_right,
				'site_html_cache_clear_probability' => $oSite->html_cache_clear_probability,
				'site_notes' => $oSite->notes,
				'site_uploaddir' => $oSite->uploaddir,
				'site_nesting_level' => $oSite->nesting_level
			);
		}
		
		/* Сохраняем в mem-кэше*/
		$this->CacheSite[$site_id] = $site_row;

		// Если добавлено кэширование
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($site_id, $site_row, $cache_name);
		}
		
		return $site_row;
	}

	/**
	* Метод возвращает информацию обо всех доменах
	*
	* @param int $site_id идентификатор сайта, которому принадлежит домен, если false - учитываются все сайты
	* <code>
	* <?php 
	* $site = new site();
	*
	* $site_id = 1;
	*
	* $row = $site->GetAllAlias($site_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return resource с информацией о доменах (алиасах)
	*/
	function GetAllAlias($site_id = FALSE)
	{
		$oQueryBuilder = Core_QueryBuilder::select(
				array('id', 'alias_id'),
				array('site_id', 'site_id'),
				array('name', 'alias_name'),
				array('current', 'alias_current'),
				array('user_id', 'users_id')
			)
			->from('site_aliases')
			->where('deleted', '=', '0');
	
		if ($site_id !== FALSE)
		{
			$oQueryBuilder->where('site_id', '=', $site_id);
		}
		
		return $oQueryBuilder->execute()->getResult();
	}

	/**
	* Метод возвращает информацию об алиасе (домене)
	*
	* @param int $alias_id идентификатор домена
	* <code>
	* <?php 
	* $site = new site();
	*
	* $alias_id = 2;
	*
	* $row = $site->GetAliasById($alias_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed ассоциативный массив с информацией об алиасе или false
	*/
	function GetAliasById($alias_id)
	{
		$alias_id = intval($alias_id);
		
		$oSiteAlias = Core_Entity::factory('Site_Alias', $alias_id);
		
		if(is_null($oSiteAlias->name))
		{
			return FALSE;
		}
		else
		{
			return array(
				'alias_id' => $oSiteAlias->id,
				'site_id' => $oSiteAlias->site_id,
				'alias_name' => $oSiteAlias->name,
				'alias_current' => $oSiteAlias->current,
				'users_id' => $oSiteAlias->user_id,
				'alias_name_without_mask' => $this->ReplaceMask($oSiteAlias->name)
			);
		}
	}

	/**
	* Создание XML с информацией о сайте и алиасах
	*
	* @param int $site_id идентификатор сайта
	* <code>
	* <?php 
	* $site = new site();
	*
	* $site_id = 1;
	*
	* $xml = $site->GetXmlForSite($site_id);
	*
	* // Распечатаем результат
	* echo htmlspecialchars($xml);
	* ?>
	* </code>
	* @return string созданный XML
	*/
	function GetXmlForSite($site_id)
	{
		$site_id = intval($site_id);

		$oSite = Core_Entity::factory('Site')->find($site_id);
		
		$sXMLData = '';
		
		if(!is_null($oSite->id))
		{
			$sXMLData .= '<site site_id="'.$oSite->id.'">'."\n";
			$sXMLData .= '<site_name>'.str_for_xml($oSite->name).'</site_name>'."\n";
			$sXMLData .= '<site_coding>'.str_for_xml($oSite->coding).'</site_coding>'."\n";
			$sXMLData .= '<site_locale>'.str_for_xml($oSite->locale).'</site_locale>'."\n";
			$sXMLData .= '<site_timezone>'.str_for_xml($oSite->timezone).'</site_timezone>'."\n";
			$sXMLData .= '<site_files_chmod>'.str_for_xml($oSite->files_chmod).'</site_files_chmod>'."\n";
			$sXMLData .= '<site_date_format>'.str_for_xml($oSite->date_format).'</site_date_format>'."\n";
			$sXMLData .= '<site_date_time_format>'.str_for_xml($oSite->date_time_format).'</site_date_time_format>'."\n";
			$sXMLData .= '<site_html_cache_clear_probability>'.str_for_xml($oSite->html_cache_clear_probability).'</site_html_cache_clear_probability>'."\n";
			
			$aoSiteAliases = $oSite->Site_Aliases->findAll();
			
			foreach($aoSiteAliases as $oSiteAlias)
			{
				$sXMLData .= '<alias alias_id="'.$oSiteAlias->id.'" alias_current="'.$oSiteAlias->current.'">'."\n";
				$sXMLData .= '<alias_name>'.str_for_xml($this->ReplaceMask($oSiteAlias->name)).'</alias_name>'."\n";
				$sXMLData .= '</alias>'."\n";
			}

			$sXMLData .= '</site>'."\n";
		}
		
		return $sXMLData;
	}

	/**
	 * Копирование сайта
	 *
	 * @param int $site_id идентификатор сайта
	 * @return unknown
	 */
	function CopySite($site_id)
	{
		$site_id = intval($site_id);

		$oNewSite = Core_Entity::factory('Site', $site_id)->copy();
		
		$new_site_id = $oNewSite->id;
		
		// Warning: Вынести в модель сайта
		// ----------------------------
		
		$DataBase =& singleton('DataBase');

		// Получаем данные об оригинальном сайте
		$original_site_row = $this->GetSite($site_id, array('cache_off' => true));

		// Копируем макеты
		if (class_exists('templates'))
		{
			$templates = new templates();

			// Очищаем массив с идентификаторами
			$templates->array_template_ids = array();

			// Очищаем массив соответствий идентификаторов макетов
			$templates->array_template_ids = array();
			// Копируем макеты
			$templates->CopyTemplatesDir(0, 0, $site_id, $new_site_id);
			// Массив идентификаторов макетов
			$array_template_ids = $templates->array_template_ids;

			// Очищаем массив соответствий идентификаторов шаблонов
			$templates->array_template_ids = array();
			// Копируем шаблоны
			$templates->CopyDataTemplatesDir(0,0, $site_id, $new_site_id);
			// Массив идентификаторов шаблонов
			$array_datatemplate_ids = $templates->array_template_ids;
		}

		if (!isset($array_template_ids))
		{
			$array_template_ids = array();
		}

		// Копируем страницы и документы, а также статусы документов
		if (class_exists('documents'))
		{
			$documents = new documents();
			$array_document_status_ids = array();

			// Получаем список всех статусов документов
			$document_status_res = $documents->select_status(false, $site_id);

			if ($document_status_res)
			{
				while ($document_status_row = mysql_fetch_assoc($document_status_res))
				{
					$array_document_status_ids[$document_status_row['documents_status_id']] = $documents->CopyDocumentStatus($document_status_row['documents_status_id'], $new_site_id);
				}
			}

			// Очищаем массив соответствий старых и новых идентификаторов документов
			$documents->documents_id_array = array();

			$documents->CopyDocumentsDir(0, $site_id, $new_site_id, 0, true, $array_template_ids, $array_document_status_ids);

			//массив соответствий старых и новых идентификаторов документов
			$array_document_ids = $documents->documents_id_array;
		}

		if (!isset($array_document_ids))
		{
			$array_document_ids = array();
		}

		$array_menu_ids = array();

		// Копируем меню сайта
		if (class_exists('menu'))
		{
			$menu = new menu();

			// Получаем информацию о всех меню
			$menu_res = $menu->GetAllMenu($site_id);

			if (mysql_num_rows($menu_res) > 0)
			{
				while ($menu_row = mysql_fetch_assoc($menu_res))
				{
					$array_menu_ids[$menu_row['menu_id']] = $menu->CopyMenu($menu_row['menu_id'], array('site_id' => $new_site_id));
				}
			}
		}

		// Копируем информационные системы
		if (class_exists('InformationSystem'))
		{
	
			$InformationSystem = new InformationSystem();

			$is_assign_arr = array();
			
			$is_assign_arr = $InformationSystem->CopyInformationSystemDir(array('source_site_id' => CURRENT_SITE, 'destination_site_id' => $new_site_id));
		}

		// Копирование интернет-магазинов
		if (class_exists('shop'))
		{
			$shop = new shop();
			$shop_assign_arr = array();
			$shop_assign_arr = $shop->CopyShopDir(array('source_site_id' => CURRENT_SITE, 'destination_site_id' => $new_site_id));
		}
		
		// Копирование структуры
		if (class_exists('Structure'))
		{
			$structure = new Structure();
			
			// Получаем список дополнительных свойств узлов структуры сайта
			$structure_propertys_resource = $structure->SelectStructurePropertys($site_id);
						
			// Массив для хранения информации о дополнительных свойствах
			$mas_structure_propertys = array();
			
			// В цикле копируем дополнительные свойства узлов структуры 
			while ($structure_propertys_row = mysql_fetch_assoc($structure_propertys_resource))
			{
				$new_structure_property_id = $structure->CopyStructureProperty($structure_propertys_row['structure_propertys_id'], $new_site_id);
				
				$mas_structure_propertys[$structure_propertys_row['structure_propertys_id']] = $structure_propertys_row;
				
				// Переопределяем идентификатор доп. свойства
				$mas_structure_propertys[$structure_propertys_row['structure_propertys_id']]['structure_propertys_id'] = $new_structure_property_id;
				
				// Переопределяем идентификатор сайта
				$mas_structure_propertys[$structure_propertys_row['structure_propertys_id']]['site_id'] = $new_site_id;
			}

			$structure_res = $structure->SelectStructureForParent(0, false, $site_id);
						
			while ($structure_row = mysql_fetch_assoc($structure_res))
			{
				$new_structure_id = $structure->CopyStructure($structure_row['structure_id'], $new_site_id, false, $array_document_ids, $array_menu_ids, Core_Type_Conversion::toArray($array_datatemplate_ids), Core_Type_Conversion::toArray($array_template_ids), Core_Type_Conversion::toArray($is_assign_arr), Core_Type_Conversion::toArray($shop_assign_arr));
				
				// Цикл по дополнительным свойствам узлов структуры
				foreach($mas_structure_propertys as $structure_propertys_id => $structure_property_info)
				{					
					// Получаем информацию о значении дополнительного свойства узла структуры
					//$structure_property_value = $structure->GetStructurePropertyValue($structure_row['structure_id'], $structure_propertys_id);

					// Добавляем значение дополнительного свойства скопированному узлу структуры  
					//$structure->InsertStructurePropertysValue(0, 0, $new_structure_id, $structure_property_info['structure_propertys_id'], $structure_property_value['structure_propertys_values_value'],$structure_property_value['structure_propertys_values_file'], $structure_property_value['structure_propertys_values_value_small'], $structure_property_value['structure_propertys_values_file_small']);
					
					$query = "UPDATE structure_propertys_values_table
							SET structure_propertys_id='{$structure_property_info['structure_propertys_id']}'
							WHERE structure_propertys_id = '$structure_propertys_id' 
							AND structure_id = $new_structure_id";
				
					$DataBase->query($query);										
				}
			}			
		}

		// Копируем форумы
		if (class_exists('Forums'))
		{
			$forums = new Forums();

			// Получаем список конференций не сопоставленных ни одному узлу структуры, т.к. те, что были сопоставлены, должны были быть скопированы при копировании узлов структуры
			$query = "SELECT * FROM `forums_conference_table`
			WHERE `structure_id` = 0";

			$conference_res = $DataBase->query($query);

			if (mysql_num_rows($conference_res) > 0)
			{
				while ($conference_row = mysql_fetch_assoc($conference_res))
				{
					$forums->CopyForumsConference($conference_row['forums_conference_id'], $new_site_id);
				}
			}
		}

		// Копируем опросы
		if (class_exists('polls'))
		{
			$polls = new polls();

			// получаем список опросов, которые не связаны ни с одним разделом структуры, т.к. те, что связаны, должны юыли быть скопированы при копировании структуры
			$polls_res = $polls->GetAllPollsGroups($site_id, 0);

			if ($polls_res)
			{
				while ($polls_row = mysql_fetch_assoc($polls_res))
				{
					$polls->CopyPollGroup($polls_row['poll_group_id'], $new_site_id);
				}
			}
		}

		// Копируем почтовые рассылки
		if (class_exists('Maillist'))
		{
			$maillist = new Maillist();

			// Получаем список почтовых рассылок, принадлежащих данному сайту
			$maillist_res = $maillist->GetAllMaillistsForSite($site_id);

			if ($maillist_res)
			{
				while ($maillist_row = mysql_fetch_assoc($maillist_res))
				{
					$maillist->CopyMaillist($maillist_row['maillist_id'], $new_site_id);
				}
			}
		}

		// Копируем рекламу
		if (class_exists('Advertisement'))
		{
			$advertisement = new Advertisement();

			$advertisement->CopyBannersAndBannersGroupsFromSiteToSite($site_id, $new_site_id);
		}

		// Копируем списки
		if (class_exists('lists'))
		{
			$lists = new lists();

			// Получаем списки сайта
			$lists_res = $lists->GetAllListsForSite($site_id);

			if (mysql_num_rows($lists_res) > 0)
			{
				while ($lists_row = mysql_fetch_assoc($lists_res))
				{
					$lists->CopyList($lists_row['lists_id'], $new_site_id);
				}
			}
		}

		// Копируем формы
		if (class_exists('Forms'))
		{
			$forms = new Forms();

			$forms_res = $forms->GetAllForms($site_id);

			if (mysql_num_rows($forms_res) > 0)
			{
				while ($forms_row = mysql_fetch_assoc($forms_res))
				{
					$forms->CopyForms($forms_row['forms_id'], $new_site_id);
				}
			}
		}

		if (class_exists('user_access'))
		{
			$user_access = new user_access();

			// Получаем список всех групп пользователей центра администрирования
			$user_access_group_res = $user_access->GetAllUserTypes($site_id);

			if (mysql_num_rows($user_access_group_res) > 0)
			{
				while ($user_access_group_row = mysql_fetch_assoc($user_access_group_res))
				{
					$user_access->CopyUserType($user_access_group_row['users_type_id'], $new_site_id);
				}
			}
		}

		// Копируем службы поддержки
		if (class_exists('helpdesk'))
		{
			$helpdesk = new helpdesk();

			// Получаем информацию обо всех службах поддержки
			$helpdesk_res = $helpdesk->GetAllHelpdesk($site_id);

			if (mysql_num_rows($helpdesk_res) > 0)
			{
				while ($helpdesk_row = mysql_fetch_assoc($helpdesk_res))
				{
					$helpdesk->CopyHelpdesk($helpdesk_row['helpdesk_id'], array('site_id' => $new_site_id));
				}
			}
		}
	}
}
