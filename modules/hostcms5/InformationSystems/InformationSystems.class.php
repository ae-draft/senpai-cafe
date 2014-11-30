<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Информационные системы".
 *
 * Файл: /modules/InformationSystems/InformationSystems.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class InformationSystem
{
	/**
	 * Текущий массив ID подгрупп групп для информационной системы (может содержать информацию не обо всех группах)
	 *
	 * @var array
	 * @access private
	 */
	var $CacheGoupsIdTree;

	/**
	 * Полный массив ID подгрупп групп для информационной системы
	 *
	 * @var array
	 * @access private
	 */
	var $FullCacheGoupsIdTree;

	/**
	 * Массив ID подгрупп групп для информационной системы
	 *
	 * @var array
	 * @access private
	 */
	var $CacheGoupsPropertyIdTree;

	/**
	 * Массив числа подгрупп и элементов для групп инфосистем
	 *
	 * @var array
	 * @access private
	 */
	var $CacheCountGroupsAndItems;

	/**
	 * @var int
	 * @access private
	 */
	var $error;

	/**
	 * Массив групп дополнительных свойств информационных элементов
	 *
	 * @var array
	 * @access private
	 */
	var $mas_property_dir_groups;

	/**
	 * @var array
	 * @access private
	 */
	var $mas_information_groups_for_xml;

	/**
	 * @var string
	 * @access private
	 */
	var $section_path;

	/**
	 * Кэш с данными об элементах
	 *
	 * @var array
	 */
	var $ItemMass = array();

	/**
	 * Кэш с данными о комментариях
	 *
	 * @var array
	 */
	var $CommentMass = array();

	/**
	 * Кэш с данными о свойствах инфоррмационных элементов
	 *
	 * @var array
	 */
	var $PropertyMass = array();

	/**
	 * Кэш с данными о дополнительных свойствах информационных групп
	 *
	 * @var array
	 */
	var $PropertyGroupMass = array();

	/**
	 * Флаг, показывающий строить все дерево групп или групп данной подгруппы
	 * FALSE - не строить все дерево групп
	 *
	 * @var boolean
	 * @access private
	 */
	var $flag_recurs = FALSE;

	/**
	 * Массив групп
	 *
	 * @var array
	 */
	var $MasGroup = array();

	/**
	 * Массив групп дополнительных свойств информационных элементов
	 *
	 * @var array
	 * @access private
	 */
	var $CachePropertiesItemsDir;

	/**
	 * Массив групп
	 *
	 * @var array
	 * @access private
	 */
	var $mas_groups_dir;

	/**
	 * Массив групп дополнительных свойств
	 *
	 * @var array
	 * @access private
	 */
	var $mas_groups_property;

	/**
	 * Кэш количества свойств
	 *
	 * @var array
	 * @access private
	 */
	var $a_count_property=array();

	/**
	 * Кэш данных о информационной системе
	 *
	 * @var array
	 * @access private
	 */
	var $cache_InformationSystem = array();

	/**
	 * Кэш прав доступа к информационной группе
	 *
	 * @var array
	 * @access private
	 */
	var $InformationSystemGroupAccess = array();

	/**
	 * Определенные права доступа пользователя к группе
	 *
	 * @var array
	 * @access private
	 */
	var $CacheIssetAccessForInformationSystemGroup;

	/**
	 * Устаревший массив ID дочерних комментариев
	 *
	 * @var array
	 * @access private
	 */
	var $cpm = array();

	/**
	 * Массив комментариев
	 *
	 * @var array
	 * @access private
	 */
	var $cm = array();

	/**
	 * Общее количество комментариев
	 *
	 * @var int
	 * @access private
	 */
	var $ctotalcount = 0;

	/**
	 * Количество комментариев
	 *
	 * @var int
	 * @access private
	 */
	var $count_comments = 0;

	/**
	 * Суммарная оценка для комментария
	 *
	 * @var int
	 * @access private
	 */
	var $grade_sum = 0;

	/**
	 * Количество оценок для комментария
	 *
	 * @var int
	 * @access private
	 */
	var $grade_count = 0;

	function getPropertyValueTableName($type)
	{
		switch ($type)
		{
			default:
			case 0:
			case 3:
			case 5:
			case 7:
				$tableName = 'property_value_ints';
				$fieldName = 'value';
			break;
			case 1:
			case 10:
				$tableName = 'property_value_strings';
				$fieldName = 'value';
			break;
			case 4:
			case 6:
				$tableName = 'property_value_texts';
				$fieldName = 'value';
			break;
			case 8:
			case 9:
				$tableName = 'property_value_datetimes';
				$fieldName = 'value';
			break;
			case 2:
				$tableName = 'property_value_files';
				$fieldName = 'file';
			break;
		}

		return array('tableName' => $tableName, 'fieldName' => $fieldName);
	}

	/**
	 * Массив имен узлов, для которых необходимо в результирующем массиве метода GetInformationFromPath() указать наличие этих узлов.
	 * Наиболее часто используется при работе с блогами для передачи имен таких узлов, как:
	 * community, sns, my_community, my_sns и т.д.
	 * Если имя узла совпадает с элементом переданного массива, в результирующем массиве, возвращаемом методом GetInformationFromPath(), будет индекс с именем этого узла и значением true
	 *
	 * @var array
	 */
	var $PathArrayGetInformationFromPath = array();

	function getArrayInformationsystemDir($oInformationsystem_Dir)
	{
		return array (
		'information_systems_dir_id' => $oInformationsystem_Dir->id,
		'information_systems_dir_parent_id' => $oInformationsystem_Dir->parent_id,
		'information_systems_dir_name' => $oInformationsystem_Dir->name,
		'information_systems_dir_description' => $oInformationsystem_Dir->description,
		'site_id' => $oInformationsystem_Dir->site_id,
		'users_id' => $oInformationsystem_Dir->user_id
		);
	}

	function getArrayInformationsystem($oInformationsystem)
	{
		return array(
			'information_systems_id' => $oInformationsystem->id,
			'information_systems_dir_id' => $oInformationsystem->informationsystem_dir_id,
			'structure_id' => $oInformationsystem->structure_id,
			'site_id' => $oInformationsystem->site_id,
			'information_systems_name' => $oInformationsystem->name,
			'information_systems_description' => $oInformationsystem->description,
			'information_systems_items_order_type' => $oInformationsystem->items_sorting_direction,
			'information_systems_items_order_field' => $oInformationsystem->items_sorting_field,
			'information_systems_group_items_order_type' => $oInformationsystem->groups_sorting_direction,
			'information_systems_group_items_order_field' => $oInformationsystem->groups_sorting_field,
			'information_systems_image_big_max_width' => $oInformationsystem->image_large_max_width,
			'information_systems_image_big_max_height' => $oInformationsystem->image_large_max_height,
			'information_systems_image_small_max_width' => $oInformationsystem->image_small_max_width,
			'information_systems_image_small_max_height' => $oInformationsystem->image_small_max_height,
			'information_systems_access' => $oInformationsystem->siteuser_group_id,
			'information_systems_captcha_used' => $oInformationsystem->use_captcha,
			'information_systems_watermark_file' => $oInformationsystem->watermark_file,
			'information_systems_default_used_watermark' => $oInformationsystem->watermark_default_use_large_image,
			'information_systems_default_used_small_watermark' => $oInformationsystem->watermark_default_use_small_image,
			'information_systems_watermark_default_position_x' => $oInformationsystem->watermark_default_position_x,
			'information_systems_watermark_default_position_y' => $oInformationsystem->watermark_default_position_y,
			'users_id' => $oInformationsystem->user_id,
			'information_systems_items_on_page' => $oInformationsystem->items_on_page,
			'information_systems_format_date' => $oInformationsystem->format_date,
			'information_systems_format_datetime' => $oInformationsystem->format_datetime,
			'information_systems_url_type' => $oInformationsystem->url_type,
			'information_systems_typograph_item' => $oInformationsystem->typograph_default_items,
			'information_systems_typograph_group' => $oInformationsystem->typograph_default_groups,
			'information_systems_apply_tags_automatic' => $oInformationsystem->apply_tags_automatically,
			'information_systems_file_name_conversion' => $oInformationsystem->change_filename,
			'information_systems_apply_keywords_automatic' => $oInformationsystem->apply_keywords_automatically,
			'information_systems_image_big_max_width_group' => $oInformationsystem->group_image_large_max_width,
			'information_systems_image_big_max_height_group' => $oInformationsystem->group_image_large_max_height,
			'information_systems_image_small_max_width_group' => $oInformationsystem->group_image_small_max_width,
			'information_systems_image_small_max_height_group' => $oInformationsystem->group_image_small_max_height,
			'information_systems_default_save_proportions' => $oInformationsystem->preserve_aspect_ratio
		);
	}

	function getArrayInformationsystemItem($oInformationsystemItem)
	{
		return array(
			'information_items_id' => $oInformationsystemItem->id,
			'information_systems_id' => $oInformationsystemItem->informationsystem_id,
			'information_groups_id' => $oInformationsystemItem->informationsystem_group_id,
			'information_items_shortcut_id' => $oInformationsystemItem->shortcut_id,
			'information_items_date' => $oInformationsystemItem->datetime,
			'information_items_putoff_date' => $oInformationsystemItem->start_datetime,
			'information_items_putend_date' => $oInformationsystemItem->end_datetime,
			'information_items_name' => $oInformationsystemItem->name,
			'information_items_description' => $oInformationsystemItem->description,
			'information_items_status' => $oInformationsystemItem->active,
			'information_items_text' => $oInformationsystemItem->text,
			'information_items_image' => $oInformationsystemItem->image_large,
			'information_items_small_image' => $oInformationsystemItem->image_small,
			'information_items_image_width' => $oInformationsystemItem->image_large_width,
			'information_items_image_height' => $oInformationsystemItem->image_large_height,
			'information_items_small_image_width' => $oInformationsystemItem->image_small_width,
			'information_items_small_image_height' => $oInformationsystemItem->image_small_height,
			'information_items_order' => $oInformationsystemItem->sorting,
			'information_items_ip' => $oInformationsystemItem->ip,
			'information_items_url' => $oInformationsystemItem->path,
			'information_items_allow_indexation' => $oInformationsystemItem->indexing,
			'information_items_seo_title' => $oInformationsystemItem->seo_title,
			'information_items_seo_description' => $oInformationsystemItem->seo_description,
			'information_items_seo_keywords' => $oInformationsystemItem->seo_keywords,
			'information_items_access' => $oInformationsystemItem->siteuser_group_id,
			'information_items_show_count' => $oInformationsystemItem->showed,
			'users_id' => $oInformationsystemItem->user_id,
			'site_users_id' => $oInformationsystemItem->siteuser_id
		);
	}

	function getArrayInformationsystemGroup($oInformationsystemGroup)
	{
		return array(
			'information_groups_id' => $oInformationsystemGroup->id,
			'information_systems_id' => $oInformationsystemGroup->informationsystem_id,
			'site_users_id' => $oInformationsystemGroup->siteuser_id,
			'information_groups_parent_id' => $oInformationsystemGroup->parent_id,
			'information_groups_top_parent_id' => $oInformationsystemGroup->top_parent_id,
			'information_groups_name' => $oInformationsystemGroup->name,
			'information_groups_description' => $oInformationsystemGroup->description,
			'information_groups_order' => $oInformationsystemGroup->sorting,
			'information_groups_path' => $oInformationsystemGroup->path,
			'information_groups_image' => $oInformationsystemGroup->image_large,
			'information_groups_small_image' => $oInformationsystemGroup->image_small,
			'information_groups_allow_indexation' => $oInformationsystemGroup->indexing,
			'information_groups_seo_title' => $oInformationsystemGroup->seo_title,
			'information_groups_seo_description' => $oInformationsystemGroup->seo_description,
			'information_groups_seo_keywords' => $oInformationsystemGroup->seo_keywords,
			'information_groups_access' => $oInformationsystemGroup->siteuser_group_id,
			'information_groups_activity' => $oInformationsystemGroup->active,
			'users_id' => $oInformationsystemGroup->user_id,
			'sns_type_id' => $oInformationsystemGroup->sns_type_id,
			'count_items' => $oInformationsystemGroup->items_count,
			'count_all_items' => $oInformationsystemGroup->items_total_count,
			'count_groups' => $oInformationsystemGroup->subgroups_count,
			'count_all_groups' => $oInformationsystemGroup->subgroups_total_count
		);
	}

	function getArrayInformationsystemItemComment($oComment)
	{
		return array(
			'comment_id' => $oComment->id,
			'comment_parent_id' => $oComment->parent_id,
			'information_items_id' => $oComment->Comment_Informationsystem_Item->Informationsystem_Item->id,
			'comment_fio' => $oComment->author,
			'comment_email' => $oComment->email,
			'comment_text' => $oComment->text,
			'comment_status' => $oComment->active,
			'comment_subject' => $oComment->subject,
			'comment_ip' => $oComment->ip,
			'comment_date' => $oComment->datetime,
			'comment_grade' => $oComment->grade,
			'comment_phone' => $oComment->phone,
			'site_users_id' => $oComment->siteuser_id,
			'users_id' => $oComment->user_id
		);
	}

	function getArrayItemProperty($oProperty)
	{
		return array(
			'information_propertys_id' => $oProperty->id,
			'information_systems_id' => $oProperty->Informationsystem_Item_Property->informationsystem_id,
			'information_propertys_items_dir_id' => $oProperty->property_dir_id,
			'information_propertys_name' => $oProperty->name,
			'information_propertys_type' => $oProperty->type,
			'information_propertys_order' => $oProperty->sorting,
			'information_propertys_define_value' => $oProperty->default_value,
			'information_propertys_xml_name' => $oProperty->tag_name,
			'information_propertys_lists_id' => $oProperty->list_id,
			'information_propertys_information_systems_id' => $oProperty->informationsystem_id,
			'users_id' => $oProperty->user_id,
			'information_propertys_default_big_width' => $oProperty->image_large_max_width,
			'information_propertys_default_small_width' => $oProperty->image_small_max_width,
			'information_propertys_default_big_height' => $oProperty->image_large_max_height,
			'information_propertys_default_small_height' => $oProperty->image_small_max_height
		);
	}

	function getArrayItemPropertyValue($oPropertyValue)
	{
		$oProperty = Core_Entity::factory('Property', $oPropertyValue->property_id);

		$array = array(
			'information_propertys_items_id' => $oPropertyValue->id
		);

		if ($oProperty->type != 2)
		{
			$array['information_propertys_items_value'] = $oPropertyValue->value;
			$array['information_propertys_items_file'] = '';
			$array['information_propertys_items_value_small'] = '';
			$array['information_propertys_items_file_small'] = '';
		}
		else
		{
			$array['information_propertys_items_value'] = $oPropertyValue->file_name;
			$array['information_propertys_items_file'] = $oPropertyValue->file;
			$array['information_propertys_items_value_small'] = $oPropertyValue->file_small_name;
			$array['information_propertys_items_file_small'] = $oPropertyValue->file_small;
		}

		return $array;
	}

	function getArrayItemPropertyDir($oPropertyDir)
	{
		return array(
			'information_propertys_items_dir_id' => $oPropertyDir->id,
			'information_systems_id' => $oPropertyDir->Informationsystem_Item_Property_Dir->informationsystem_id,
			'information_propertys_items_dir_parent_id' => $oPropertyDir->parent_id,
			'information_propertys_items_dir_name' => $oPropertyDir->name,
			'information_propertys_items_dir_description' => $oPropertyDir->description,
			'information_propertys_items_dir_order' => $oPropertyDir->sorting,
			'users_id' => $oPropertyDir->user_id
		);
	}

	function getArrayGroupProperty($oProperty)
	{
		return array(
			'information_propertys_groups_id' => $oProperty->id,
			'information_systems_id' => $oProperty->Informationsystem_Group_Property->informationsystem_id,
			'information_propertys_groups_dir_id' => $oProperty->property_dir_id,
			'information_propertys_groups_name' => $oProperty->name,
			'information_propertys_groups_type' => $oProperty->type,
			'information_propertys_groups_order' => $oProperty->sorting,
			'information_propertys_groups_default_value' => $oProperty->default_value,
			'information_propertys_groups_xml_name' => $oProperty->tag_name,
			'information_propertys_groups_lists_id' => $oProperty->list_id,
			'information_propertys_groups_information_systems_id' => $oProperty->informationsystem_id,
			'users_id' => $oProperty->user_id,
			'information_propertys_groups_big_width' => $oProperty->image_large_max_width,
			'information_propertys_groups_small_width' => $oProperty->image_small_max_width,
			'information_propertys_groups_big_height' => $oProperty->image_large_max_height,
			'information_propertys_groups_small_height' => $oProperty->image_small_max_height
		);
	}

	function getArrayGroupPropertyValue($oPropertyValue)
	{
		$oProperty = $oPropertyValue->Property;

		$array = array();

		if ($oProperty->type != 2)
		{
			$array['information_propertys_groups_value_value'] = $oPropertyValue->value;
			$array['information_propertys_groups_value_file'] = '';
			$array['information_propertys_groups_value_value_small'] = '';
			$array['information_propertys_groups_value_file_small'] = '';
		}
		else
		{
			$array['information_propertys_groups_value_value'] = $oPropertyValue->file_name;
			$array['information_propertys_groups_value_file'] = $oPropertyValue->file;
			$array['information_propertys_groups_value_value_small'] = $oPropertyValue->file_small_name;
			$array['information_propertys_groups_value_file_small'] = $oPropertyValue->file_small;
		}

		return $array;
	}

	function getArrayGroupPropertyDir($oPropertyDir)
	{
		return array(
			'information_propertys_groups_dir_id' => $oPropertyDir->id,
			'information_systems_id' => $oPropertyDir->Informationsystem_Group_Property_Dir->informationsystem_id,
			'information_propertys_groups_dir_parent_id' => $oPropertyDir->parent_id,
			'information_propertys_groups_dir_name' => $oPropertyDir->name,
			'information_propertys_groups_dir_description' => $oPropertyDir->description,
			'information_propertys_groups_dir_order' => $oPropertyDir->sorting,
			'users_id' => $oPropertyDir->user_id
		);
	}

	/**
	 * Устаналивает свойство $this->PathArrayGetInformationFromPath
	 *
	 * @param array $array
	 */
	function SetPathArrayGetInformationFromPath($array)
	{
		$this->PathArrayGetInformationFromPath = $array;
	}

	/**
	 * Метод возвращает XML для информационных групп
	 * @return $this->mas_information_groups_for_xml
	 */
	function get_mas_information_groups_for_xml()
	{
		return $this->mas_information_groups_for_xml;
	}

	/**
	 * Метод возвращающий код ошибки, возникающей при работе с методами класса
	 *
	 * @return $this->error
	 */
	function GetPropertyError()
	{
		return $this->error;
	}

	/**
	 * Получение значения свойтва, содержащего пути по группам
	 *
	 * @return string
	 */
	function GetPropertySectionPath()
	{
		return $this->section_path;
	}

	/**
	 * Получение массива информационных групп
	 *
	 * @return array  ассоциативный массив с данными об информационных группах
	 */
	function GetMasGroup()
	{
		return $this->MasGroup;
	}

	/**
	 * Функция обратного вызова для отображения блока
	 * на основной странице центра администрирования.
	 *
	 */
	function AdminMainPage(){}

	/**
	 * Получение элементов информационной системы. Переименован с GetInformationSystemItem
	 *
	 * @param array $select параметр, определяющий параметры для отбора информационных элементов
	 * @param array $param
	 *  - $param['Order'] = ASC/DESC порядок сортировки информационных элементов
	 *  - $param['OrderField'] поле сортировки информационных элементов, если случайная сортировка, то записать RAND()
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * // Получаем информацию об информационных элементах, находящихся в корне
	 * // информационной системы с идентификатором 1
	 * $param_select = array();
	 * $infsys_id = 1;
	 * $information_group_id = 0;
	 * $param_select['information_systems_id'] = $infsys_id;
	 * $param_select['information_groups_id'] = $information_group_id;
	 *
	 * $resource = $InformationSystem->GetExternalInformationSystemItem($param_select);
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
	function GetExternalInformationSystemItem($select = array(), $param = array())
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('informationsystem_items.id','information_items_id'),
			array('informationsystem_items.informationsystem_id', 'information_systems_id'),
			array('informationsystem_items.informationsystem_group_id', 'information_groups_id'),
			array('informationsystem_items.shortcut_id', 'information_items_shortcut_id'),
			array('informationsystem_items.datetime', 'information_items_date'),
			array('informationsystem_items.start_datetime', 'information_items_putoff_date'),
			array('informationsystem_items.end_datetime', 'information_items_putend_date'),
			array('informationsystem_items.name', 'information_items_name'),
			array('informationsystem_items.description', 'information_items_description'),
			array('informationsystem_items.active', 'information_items_status'),
			array('informationsystem_items.text', 'information_items_text'),
			array('informationsystem_items.image_large', 'information_items_image'),
			array('informationsystem_items.image_small', 'information_items_small_image'),
			array('informationsystem_items.image_large_width', 'information_items_image_width'),
			array('informationsystem_items.image_large_height', 'information_items_image_height'),
			array('informationsystem_items.image_small_width', 'information_items_small_image_width'),
			array('informationsystem_items.image_small_height', 'information_items_small_image_height'),
			array('informationsystem_items.sorting', 'information_items_order'),
			array('informationsystem_items.ip', 'information_items_ip'),
			array('informationsystem_items.path', 'information_items_url'),
			array('informationsystem_items.indexing', 'information_items_allow_indexation'),
			array('informationsystem_items.seo_title', 'information_items_seo_title'),
			array('informationsystem_items.seo_description', 'information_items_seo_description'),
			array('informationsystem_items.seo_keywords', 'information_items_seo_keywords'),
			array('informationsystem_items.siteuser_group_id', 'information_items_access'),
			array('informationsystem_items.showed', 'information_items_show_count'),
			array('informationsystem_items.user_id', 'users_id'),
			array('informationsystem_items.siteuser_id', 'site_users_id')
		)
		->from('informationsystem_items')
		->where('deleted', '=', 0);

		if (isset($param['Order']))
		{
			$order_type = $param['Order'];
		}
		else
		{
			$order_type = 'ASC';
		}

		if (isset($param['OrderField']))
		{
			$order_field = $param['OrderField'];
		}
		else
		{
			$order_field = 'information_items_name';
		}

		foreach ($select as $key => $value)
		{
			// where => having, т.к. псевдонимы
			$queryBuilder->having($key, '=', $value);
		}

		$queryBuilder->orderBy($order_field, $order_type);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение списка групп пользователий, в которых содержится пользователь сайта
	 *
	 * @param int $site_user_id идентификатор пользователя сайтов
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $site_user_id = 1;
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 * 	$SiteUsers = & singleton('SiteUsers');
	 * 	$site_users_id = $SiteUsers->GetCurrentSiteUser();
	 *}
	 *
	 * else
	 * {
	 *	$site_users_id = 0;
	 *}
	 *
	 * $row = $InformationSystem->GetSiteUsersGroupsForUser($site_user_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив групп пользователей
	 */
	function GetSiteUsersGroupsForUser($site_user_id)
	{
		$site_user_id = intval($site_user_id);

		/* Определяем группы доступа для текущего авторизированного	пользователя */
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			// Получаем список групп доступа, в которые входит данный пользователь
			$mas_result = $SiteUsers->GetGroupsForUser($site_user_id);
		}
		else
		{
			$mas_result = array();
			$mas_result[] = 0;
		}

		/* Добавляем всегда "Как у родителя"*/
		if (!in_array(-1, $mas_result))
		{
			$mas_result[] = -1;
		}

		return $mas_result;
	}

	/**
	 * Получение количества элементов информационной системы
	 *
	 * @param mixed $information_system_id идентификатор информационной системы, по умолчанию - FALSE
	 * @param mixed $information_group_id идентификатор группы информационной системы, по умолчанию - FALSE
	 * @param array $property дополнительные атрибуты
	 * - $property['cache'] - использование кэширования, по умолчанию true
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * // Получаем число информационных элементов, находящихся в корневой группе
	 * // информационной системы с идентификатором 1
	 * $information_system_id = 1;
	 * $information_group_id = 0;
	 *
	 * $row = $InformationSystem->GetCountInformationSystemItem($information_system_id, $information_group_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed количество элементов информационной системы или FALSE в случае ошибки
	 */
	function GetCountInformationSystemItem($information_system_id = FALSE, $information_group_id = FALSE, $property = array())
	{
		$queryBuilder = Core_QueryBuilder::select(array('COUNT(*)', 'count'))
			->from('informationsystem_items')
			->where('deleted', '=', 0);

		if ($information_system_id !== FALSE)
		{
			$queryBuilder->where('informationsystem_id', '=', intval($information_system_id));
		}

		if ($information_group_id !== FALSE)
		{
			$queryBuilder->where('informationsystem_group_id', '=', intval($information_group_id));
		}

		$current_date = date('Y-m-d H:i:s');

		$queryBuilder
			->where('start_datetime', '<=', $current_date)
			->open()
			->where('end_datetime', '>=', $current_date)
			->setOr()
			->where('end_datetime', '=', '0000-00-00 00:00:00')
			->close()
			->where('active', '=', 1);

		$row = $queryBuilder->execute()->asAssoc()->current();

		return $row['count'];
	}

	/**
	 * Вставка/обновление данных об информационной системе
	 *
	 * @param array $param
	 * - $param['type'] элемент, определяющий будет производиться вставка или обновление данных об информационной системе (0 – вставка, 1 - обновление);
	 * - $param['id'] идентификатор обновляемой информационной системы (при вставке равен 0)
	 * - $param['information_systems_dir_id'] идентификатор раздела информационных систем
	 * - $param['site_id'] идентификатор сайта, к которому принадлежит информационная система
	 * - $param['name'] название информационной системы
	 * - $param['description']  описание информационной системы
	 * - $param['items_order_field'] поле сортировки элементов данной информационной системы
	 * - $param['items_order_type'] направление сортировки элементов информационной системы
	 * - $param['information_systems_access'] параметр, определяющий группу пользователей, имеющих доступ к информационной системе (0 - доступна всем)
	 * - $param['information_systems_group_items_order_field'] поле сортировки групп данной информационной системы
	 * - $param['information_systems_group_items_order_type'] направление сортировки элементов информационной системы
	 * - $param['information_systems_captcha_used'] использовать автоматизированный тест Тьюринга разделения людей и компьютеров (1 - использовать, 0 - не использовать). по умолчанию используется
	 * - $param['watermark_file'] - файл марки для наложения на изображения
	 * - $param['watermark_file_expantion'] - расширение файла марки для наложения
	 * - $param['watermark_default_used'] - параметр, определяющий используется ли файл марки по умолчанию (1 - используется, 0 - не используется).
	 * - $param['watermark_default_used_small'] - параметр, определяющий используется ли файл марки по умолчанию для малых изображений(1 - используется, 0 - не используется).
	 * - $param['structure_id'] - параметр определяющий идентификатор узла структуры, где будет отображаться данный элемент
	 * - $param['information_systems_format_date'] формат даты
	 * - $param['information_systems_format_datetime'] формат дата/время
	 * - $param['information_systems_image_big_max_width'] максимальная ширина «большого» варианта изображения при уменьшении загружаемого изображения элемента информационной системы
	 * - $param['information_systems_image_big_max_height'] максимальная высота «большого» варианта изображения при уменьшении загружаемого изображения элемента информационной системы
	 * - $param['information_systems_image_small_max_width'] максимальная ширина «маленького» варианта изображения при уменьшении загружаемого изображения элемента информационной системы
	 * - $param['information_systems_image_small_max_height'] максимальная высота «маленького» варианта изображения при уменьшении загружаемого изображения элемента информационной системы. по умолчанию - используется.
	 * - $param['watermark_default_position_x'] позиция изображения по оси X по умолчанию. по умолчанию равна 50%.
	 * - $param['watermark_default_position_y'] позиция изображения по оси Y по умолчанию. по умолчанию равна 100%.
	 * - $param['information_systems_default_save_proportions'] флаг, определющий, сохранять ли пропорции изображений
	 * - $param['items_on_page'] число информационных элементов выводимых на страницу
	 * - $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь.
	 * - $param['information_systems_url_type'] параметр, определяющий тип формирования URL для элементов информационной системы
	 * - 0 (по умолчанию) - по идентификатору информационного элемента;
	 * - 1 - по транслитерации от названия информационного элемента.
	 * - $param['information_systems_typograph_item'] параметр, определяющий использование типографирования по умолчанию для информационных элементов,
	 * <br />1 - применять типографирование (по умолчанию), 0 - не применять
	 * - $param['information_systems_typograph_group'] параметр, определяющий использование типографирования по умолчанию для информационных групп,
	 * <br />1 - применять типографирование (по умолчанию), 0 - не применять
	 * - $param['information_systems_apply_tags_automatic'] параметр, определяющий будут при добавлении информационных элементов в случае отсутствия тегов автоматически формироваться теги для данных информационных элементов из их названия, описания и текста
	 *  <br /> 1 - теги формируются автоматически (по умолчанию), 0 - не формируюся автоматически
	 * - $param['information_systems_file_name_conversion'] параметр, определяющий будут ли преобразовываться названия загружаемых файлов в служебные. Данный параметр влияет на все объекты информационной системы - элементы, группы,
	 * 	<br />долнительные свойства элементов, дополнительные свойства групп.
	 * 	- 0 - названия файлов не преобразуются, 1 - преобразуются (по умолчанию)
	 * <code>
	 *<?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param = array()
	 *
	 * $param['type'] = 0;
	 * $param['id'] = 1;
	 * $param['site_id'] = 1;
	 * $param['name'] = 'Статьи';
	 * $param['description'] = 'Описание информационной системы Статьи...';
	 * $param['items_order_field'] = 0;
	 * $param['items_order_type'] = 1;
	 * $param['information_systems_access'] = 0;
	 * $param['information_systems_captcha_used'] = 1;
	 * $param['watermark_file'] = '';
	 * $param['watermark_file_expantion'] = '';
	 * $param['watermark_default_used'] = 1;
	 * $param['watermark_default_used_small'] = 1;
	 * $param['watermark_default_position_x'] = '50%';
	 * $param['watermark_default_position_y'] = '100%';
	 * $param['structure_id'] = 7;
	 * $param['items_on_page'] = 3;
	 * $param['information_systems_group_items_order_field'] = 0;
	 * $param['information_systems_group_items_order_type'] = 0;
	 * $param['information_systems_format_date'] = '%d.%m.%Y';
	 * $param['information_systems_format_datetime'] = '%d.%m.%Y %H:%M:%S';
	 * $param['information_systems_image_big_max_width'] = 900;
	 * $param['information_systems_image_big_max_height'] = 900;
	 * $param['information_systems_image_small_max_width'] = 100;
	 * $param['information_systems_image_small_max_height'] = 100;
	 * $param['information_systems_url_type'] = 0;
	 * $param['information_systems_typograph_item'] = 1;
	 * $param['information_systems_typograph_group'] = 1;
	 *
	 * $information_system_id = $InformationSystem->InsertInfotmationSystem($param);
	 *
	 * ?>
	 * </code>

	 * @return int идентификатор вставленной/обновленной информационной системы
	 */
	function InsertInfotmationSystem($param)
	{
		if (!isset($param['id']) || !$param['id'])
		{
			$param['id'] = NULL;
		}

		if (isset($param['name']))
		{
			$oInformationsystem->name = $param['name'];
		}
		elseif(is_null($param['id']))
		{
			return FALSE;
		}

		$oInformationsystem = Core_Entity::factory('Informationsystem', $param['id']);

		if (isset($param['information_systems_dir_id']))
		{
			$oInformationsystem->informationsystem_dir_id = intval($param['information_systems_dir_id']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->informationsystem_dir_id = 0;
		}

		if (isset($param['site_id']))
		{
			$oInformationsystem->site_id = intval($param['site_id']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->site_id = CURRENT_SITE;
		}

		if (isset($param['description']))
		{
			$oInformationsystem->description = $param['description'];
		}

		if (isset($param['items_order_field']))
		{
			$oInformationsystem->items_sorting_field = intval($param['items_order_field']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->items_sorting_field = 0;
		}

		if (isset($param['items_order_type']))
		{
			$oInformationsystem->items_sorting_direction = intval($param['items_order_type']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->items_sorting_direction = 0;
		}

		// Ширина большой картинки
		if (isset($param['information_systems_image_big_max_width']))
		{
			$oInformationsystem->image_large_max_width = intval($param['information_systems_image_big_max_width']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->image_large_max_width = MAX_SIZE_LOAD_IMAGE_BIG;
		}

		// Высота большой картинки.
		if (isset($param['information_systems_image_big_max_height']))
		{
			$oInformationsystem->image_large_max_height = intval($param['information_systems_image_big_max_height']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->image_large_max_height = MAX_SIZE_LOAD_IMAGE_BIG;
		}

		// Ширина малой картинки.
		if (isset($param['information_systems_image_small_max_width']))
		{
			$oInformationsystem->image_small_max_width = intval($param['information_systems_image_small_max_width']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->image_small_max_width = MAX_SIZE_LOAD_IMAGE;
		}

		// Высота малой картинки.
		if (isset($param['information_systems_image_small_max_height']))
		{
			$oInformationsystem->image_small_max_height = intval($param['information_systems_image_small_max_height']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->image_small_max_height = MAX_SIZE_LOAD_IMAGE;
		}

		if (isset($param['information_systems_access']))
		{
			$oInformationsystem->siteuser_group_id = intval($param['information_systems_access']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->siteuser_group_id = -1;
		}

		if (isset($param['information_systems_default_save_proportions']))
		{
			$oInformationsystem->preserve_aspect_ratio = intval($param['information_systems_default_save_proportions']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->preserve_aspect_ratio = 1;
		}

		if (isset($param['information_systems_captcha_used']))
		{
			$oInformationsystem->use_captcha = intval($param['information_systems_captcha_used']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->use_captcha = 1;
		}

		if (isset($param['watermark_file_expantion']))
		{
			$watermark_file_expantion = Core_Type_Conversion::toStr($param['watermark_file_expantion']);
		}
		else
		{
			$watermark_file_expantion = '';
		}

		if (isset($param['watermark_default_used']))
		{
			$oInformationsystem->watermark_default_use_large_image = intval($param['watermark_default_used']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->watermark_default_use_large_image = 1;
		}

		if (isset($param['watermark_default_used_small']))
		{
			$oInformationsystem->watermark_default_use_small_image = intval($param['watermark_default_used_small']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->watermark_default_used_small_image = 1;
		}

		if ($param['watermark_default_position_x'])
		{
			$oInformationsystem->watermark_default_position_x = $param['watermark_default_position_x'];
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->watermark_default_position_x = '50%';
		}

		if ($param['watermark_default_position_y'])
		{
			$oInformationsystem->watermark_default_position_y = $param['watermark_default_position_y'];
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->watermark_default_position_y = '100%';
		}

		if (isset($param['structure_id']))
		{
			$oInformationsystem->structure_id = intval($param['structure_id']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->structure_id = 0;
		}

		if (is_null($oInformationsystem->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oInformationsystem->user_id = intval($param['users_id']);
		}

		if(isset($param['items_on_page']))
		{
			$oInformationsystem->items_on_page = intval($param['items_on_page']);
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->items_on_page = 10;
		}

		if (isset($param['information_systems_group_items_order_field']))
		{
			$oInformationsystem->groups_sorting_field = intval($param['information_systems_group_items_order_field']);
		}

		if(isset($param['information_systems_group_items_order_type']))
		{
			$oInformationsystem->groups_sorting_direction = intval($param['information_systems_group_items_order_type']);
		}

		if(isset($param['information_systems_image_big_max_width_group']))
		{
			$oInformationsystem->group_image_large_max_width = intval($param['information_systems_image_big_max_width_group']);
		}

		if(isset($param['information_systems_image_big_max_height_group']))
		{
			$oInformationsystem->group_image_large_max_height = intval($param['information_systems_image_big_max_height_group']);
		}

		if(isset($param['information_systems_image_small_max_width_group']))
		{
			$oInformationsystem->group_image_small_max_width = intval($param['information_systems_image_small_max_width_group']);
		}

		if(isset($param['information_systems_image_small_max_height_group']))
		{
			$oInformationsystem->group_image_small_max_height = intval($param['information_systems_image_small_max_height_group']);
		}

		if(isset($param['information_systems_format_date']))
		{
			$oInformationsystem->format_date = $param['information_systems_format_date'];
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->format_date = '%d.%m.%Y';
		}

		if(isset($param['information_systems_format_datetime']))
		{
			$oInformationsystem->format_datetime = $param['information_systems_format_datetime'];
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->format_datetime = '%d.%m.%Y %H:%M:%S';
		}

		if(isset($param['information_systems_url_type']))
		{
			$oInformationsystem->url_type = intval($param['information_systems_url_type']);
		}

		if (isset($param['information_systems_typograph_item']))
		{
			$oInformationsystem->typograph_default_items = intval($param['information_systems_typograph_item']) != 0 ? 1 : 0;
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->typograph_default_items = 1;
		}

		if(isset($param['information_systems_typograph_group']))
		{
			$oInformationsystem->typograph_default_groups = intval($param['information_systems_typograph_group']) != 0 ? 1 : 0;
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->typograph_default_groups = 1;
		}

		if (isset($param['information_systems_apply_tags_automatic']))
		{
			$oInformationsystem->apply_tags_automatically = $param['information_systems_apply_tags_automatic'];
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->apply_tags_automatically = 1;
		}

		if (isset($param['information_systems_file_name_conversion']))
		{
			$oInformationsystem->change_filename = $param['information_systems_file_name_conversion'];
		}
		elseif(is_null($param['id']))
		{
			$oInformationsystem->change_filename = 1;
		}

		$oInformationsystem->save();

		/* Очистка файлового кэша*/
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'INF_SYS';
			$cache->DeleteCacheItem($cache_name, $oInformationsystem->id);
		}

		// загрузили файл watermark
		if (!empty($param['watermark_file']))
		{
			// расширение файла непустое
			if ($watermark_file_expantion !== '')
			{
				$watermark_file_expantion = '.' . $watermark_file_expantion;
			}

			// Формируем путь к папке информационной системы
			$information_system_dir = CMS_FOLDER . UPLOADDIR . 'information_system_' . $oInformationsystem->id . '/';

			// Не существует папки информационной системы
			if (!is_dir($information_system_dir))
			{
				// Папка информационной системы создана
				if (mkdir($information_system_dir, CHMOD))
				{
					// Устанавливаем права доступа к папке информационной системы
					@chmod($information_system_dir, CHMOD);
				}
			}

			$file_name = $information_system_dir . 'information_system_watermark' . $oInformationsystem->id . $watermark_file_expantion;

			// Существует файл ватермарка
			if (is_file($file_name))
			{
				// Удаляем преждний файл ватермарка
				unlink($file_name);
			}

			move_uploaded_file($param['watermark_file'], $file_name);

			$oInformationsystem->watermark_file = $file_name;
			$oInformationsystem->save();
		}

		return $oInformationsystem->id;
	}

	/**
	 * Устаревший метод
	 *
	 * @param int $type
	 * @param int $InformationSystem_id
	 * @param int $site_id
	 * @param string $InformationSystem_name
	 * @param string $InformationSystem_description
	 * @param string $InformationSystem_url
	 * @param int $InformationSystem_items_order_field
	 * @param int $InformationSystem_items_order_type
	 * @param int $InformationSystem_big_image_max_size
	 * @param int $InformationSystem_small_image_max_size
	 * @param int $template_id
	 * @param int $information_systems_access
	 * @param int $structure_id
	 * @param int $users_id идентификатор пользователя, если FALSE - берется текущий пользователь.
	 * @return int Идентификатор вставленной/обновленной записи
	 * @access private
	 */
	function InsertInformationBlocks($type, $InformationSystem_id, $site_id, $InformationSystem_name, $InformationSystem_description, $InformationSystem_url, $InformationSystem_items_order_field,
	$InformationSystem_items_order_type, $InformationSystem_big_image_max_size,
	$InformationSystem_small_image_max_size, $template_id, $information_systems_access,
	$structure_id, $users_id = FALSE)
	{
		$param = array(
		'type' => $type,
		'id' => $InformationSystem_id,
		'site_id' => $site_id,
		'name' => $InformationSystem_name,
		'description' => $InformationSystem_description,
		'items_order_field' => $InformationSystem_items_order_field,
		'items_order_type' => $InformationSystem_items_order_type,
		'big_image_max_size' => $InformationSystem_big_image_max_size,
		'small_image_max_size' => $InformationSystem_small_image_max_size,
		'template_id' => $template_id,
		'information_systems_access' => $information_systems_access,
		'structure_id' => $structure_id,
		'users_id' => $users_id
		);

		return $this->InsertInfotmationSystem($param);
	}

	/**
	 * Вставка/обновление данных об информационной группе. Устаревший метод
	 *
	 * @param int $type параметр, определяющий будет производиться вставка или обновление данных об информационной группе (0 – вставка, 1 - обновление)
	 * @param int $information_groups_id идентификатор обновляемой информационной группы (при вставке равен 0)
	 * @param int $InformationSystem_id идентификатор информационной системы, к которой принадлежит группа
	 * @param int $information_groups_parent_id идентификатор родительской группы данной информационной группы
	 * @param string $information_groups_name название информационной группы
	 * @param string $information_groups_description описание информационной группы
	 * @param int $information_groups_order порядковый номер группы в родительской группе
	 * @param string $information_groups_path элемент URL информационной системы для данной группы
	 * @param string $information_groups_image имя файла с изображением для информационной группы
	 * @param int $information_groups_allow_indexation параметр, определяющий индексировать данную информационную группу или нет (1- индексировать, 0 - неиндексировать)
	 * @param string $information_groups_seo_title параметр, используемый для задания заголовка страницы
	 * @param string $information_groups_seo_description параметр, используемый для задания значения мета-тега description страницы
	 * @param string $information_groups_seo_keywords параметр, используемый для задания значения мета-тега keywords страницы
	 * @param int $information_groups_access параметр, определяющий тип доступа для данной информационной группы (0 - доступна всем, -1 - доступ как у родителя)
	 * @param int $users_id идентификатор пользователя, если FALSE - берется текущий пользователь.
	 * @param array $param массив параметров
	 * - $param['information_groups_small_image'] имя файла с малым изображением для информационной группы
	 * - $param['site_users_id'] идентификатор пользователя сайта, которому принадлежит информационная группа
	 * - $param['information_groups_activity'] параметр, определяющий доступность группы и ее дочерних групп, и элементов (1 (по умолчанию) - доступна, 0 - не доступна)
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $type = 0; // Добавляем группу
	 * $information_groups_id = 0;
	 * $InformationSystem_id = 1; // Идентификатор информационной системы
	 * $information_groups_parent_id = 0; // Добавляем в корневую группу
	 * $information_groups_name = 'Тестовая группа'; // Название группы
	 * $information_groups_description = 'Описание группы Тестовая группа';
	 * $information_groups_order = 10;
	 * $information_groups_path = 'test-group';
	 * $information_groups_image = ''; // Нет изображения
	 * $information_groups_allow_indexation = 1; // Индексируем групу
	 * $information_groups_seo_title = 'Тестовая группа';
	 * $information_groups_seo_description = 'Тестовая группа';
	 * $information_groups_seo_keywords = 'Тестовая группа';
	 * $information_groups_access = -1; // Доступ как у родителя, т.е. в данном случае как у информационной системы с идентификатором 1
	 *
	 * $InformationSystem->InsertInformationGroups($type, $information_groups_id, $InformationSystem_id,
	 * $information_groups_parent_id, $information_groups_name, $information_groups_description, $information_groups_order,
	 * $information_groups_path, $information_groups_image, $information_groups_allow_indexation = 1,
	 * $information_groups_seo_title = '', $information_groups_seo_description = '', $information_groups_seo_keywords = '',
	 * $information_groups_access = -1);
	 *
	 * ?>
	 * </code>
	 * @return int идентификатор вставленной/обновленной информационной группы
	 */
	function InsertInformationGroups($type, $information_groups_id, $InformationSystem_id,
	$information_groups_parent_id, $information_groups_name, $information_groups_description,
	$information_groups_order, $information_groups_path, $information_groups_image,
	$information_groups_allow_indexation = 1, $information_groups_seo_title = '',
	$information_groups_seo_description = '', $information_groups_seo_keywords = '',
	$information_groups_access = -1, $users_id = FALSE, $param = array())
	{
		$new_param['information_groups_id'] = $information_groups_id;
		$new_param['information_system_id'] = $InformationSystem_id;
		$new_param['information_groups_parent_id'] = $information_groups_parent_id;
		$new_param['information_groups_name'] = $information_groups_name;
		$new_param['information_groups_description'] = $information_groups_description;
		$new_param['information_groups_order'] = $information_groups_order;
		$new_param['information_groups_path'] = $information_groups_path;
		$new_param['information_groups_image'] = $information_groups_image;

		if (isset($param['information_groups_small_image']))
		{
			$new_param['information_groups_small_image'] = $param['information_groups_small_image'];
		}

		$new_param['information_groups_allow_indexation'] = $information_groups_allow_indexation;
		$new_param['information_groups_seo_title'] = $information_groups_seo_title;
		$new_param['information_groups_seo_description'] = $information_groups_seo_description;
		$new_param['information_groups_seo_keywords'] = $information_groups_seo_keywords;
		$new_param['information_groups_access'] = $information_groups_access;

		if (isset($param['site_users_id']))
		{
			$new_param['site_users_id'] = $param['site_users_id'];
		}

		if (isset($param['information_groups_activity']))
		{
			$new_param['information_groups_activity'] = $param['information_groups_activity'];
		}

		if (isset($param['sns_type_id']))
		{
			$new_param['sns_type_id'] = $param['sns_type_id'];
		}

		$new_param['users_id'] = $users_id;

		return $this->InsertInformationGroup($new_param);
	}

	/**
	 * Вставка/обновление информации об информационной группе
	 *
	 * @param array $param массив параметров
	 * - $param['information_groups_id'] идентификатор обновляемой информационной группы
	 * - $param['information_system_id'] идентификатор информационной системы, к которой принадлежит группа
	 * - $param['information_groups_parent_id'] идентификатор родительской группы данной информационной группы
	 * - $param['information_groups_name'] название информационной группы
	 * - $param['information_groups_description'] описание информационной группы
	 * - $param['information_groups_order'] порядковый номер группы в родительской группе
	 * - $param['information_groups_path'] элемент URL информационной системы для данной группы
	 * - $param['information_groups_image'] имя файла большого изображения для информационной группы
	 * - $param['information_groups_small_image'] имя файла малого изображения для информационной группы
	 * - $param['information_groups_allow_indexation'] параметр, определяющий индексировать данную информационную группу или нет (1- индексировать, 0 - неиндексировать)
	 * - $param['information_groups_seo_title'] параметр, используемый для задания заголовка страницы
	 * - $param['information_groups_seo_description'] параметр, используемый для задания значения мета-тега description страницы
	 * - $param['information_groups_seo_keywords'] параметр, используемый для задания значения мета-тега keywords страницы
	 * - $param['information_groups_access'] параметр, определяющий тип доступа для данной информационной группы (0 - доступна всем, -1 - доступ как у родителя)
	 * - $param['site_users_id'] идентификатор пользователя сайта, которому принадлежит информационная группа
	 * - $param['information_groups_activity'] параметр, определяющий доступность группы и ее дочерних групп, и элементов (1 (по умолчанию) - доступна, 0 - не доступна)
	 * - $param['sns_type_id'] идентификатор типа блога
	 * - $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь.
	 *
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param = array();
	 *
	 * $param['information_system_id'] = 1;
	 * $param['information_groups_parent_id'] = 0;
	 * $param['information_groups_name'] = 'Тестовая группа';
	 * $param['information_groups_description'] = 'Описание группы';
	 * $param['information_groups_order'] = 10;
	 * $param['information_groups_path'] = 'test-group';
	 *
	 * // Если для добавляемой/редактируемой инфорационной группы необходимо загрузить
	 * // изображение, то перед вызовом метода вставки/обновления информационной группы
	 * // необходимо загрузить данное изображение используя метод AdminLoadFiles класса kernel
	 * $kernel = & singleton('kernel');
	 *
	 * $param_load_files = array();
	 *
	 *
	 * $param_load_files['path_source_big_image'] = 'C:\test\test_big_image1.jpg'; // путь к файлу-источнику большого изображения
	 * $param_load_files['path_source_small_image'] = ''; // путь к файлу-источнику малого изображения
	 * $param_load_files['original_file_name_big_image'] = 'test_big_image1.jpg'; // оригинальное имя файла большого изображения
	 *
	 * $sufix = date('U');
	 *
	 * $param_load_files['path_target_big_image'] = CMS_FOLDER . UPLOADDIR . 'information_items_' . $sufix . '.jpg'; // путь к создаваемому файлу большого изображения
	 * $param_load_files['path_target_small_image'] =CMS_FOLDER . UPLOADDIR . 'small_information_items_' . $sufix . '.jpg'; // путь к создаваемому файлу малого изображения
	 * $param_load_files['original_file_name_small_image'] = ''; // оригинальное имя файла малого изображения
	 * $param_load_files['use_big_image'] = true; //  использовать большое изображение для создания малого (true - использовать (по умолчанию), FALSE - не использовать)
	 * $param_load_files['max_width_big_image'] = 900; // значение максимальной ширины большого изображения
	 * $param_load_files['max_height_big_image'] = 900; // значение максимальной высоты большого изображения
	 * $param_load_files['max_width_small_image'] = 100; // значение максимальной ширины малого изображения;
	 * $param_load_files['max_height_small_image'] = 100; // значение максимальной высоты малого изображения;
	 *
	 * $result = $kernel->AdminLoadFiles($param_load_files);
	 *
	 * if ($result['big_image'])
	 * {
	 * 		$param['information_groups_image'] = basename($param['path_target_big_image']);
	 * }
	 * else
	 * {
	 * 		$param['information_groups_image'] = '';
	 * }
	 *
	 * if ($result['small_image'])
	 * {
	 * 		$param['information_groups_small_image'] = basename($param['path_target_small_image']);
	 * }
	 * else
	 * {
	 * 		$param['information_groups_small_image'] = '';
	 * }
	 *
	 * $param['information_groups_allow_indexation'] = 1;
	 * $param['information_groups_seo_title'] = 'Тестовая группа';
	 * $param['information_groups_seo_description'] = 'Тестовая группа';
	 * $param['information_groups_seo_keywords'] = 'Тестовая группа';
	 * $param['information_groups_access'] = -1;
	 *
	 * if ($InformationSystem->InsertInformationGroup($param))
	 * {
	 * 		echo 'Группа добавлена';
	 * }
	 * else
	 * {
	 * 		echo 'Ошибка! Группа не добавлена!';
	 * }
	 * ?>
	 * </code>
	 * @return int идентификатор добаленной/обновленной группы, в случае ошибки возвращает 0
	 * @see AdminLoadFiles()
	 */
	function InsertInformationGroup($param)
	{
		if (!isset($param['information_groups_id']) || !$param['information_groups_id'])
		{
			$param['information_groups_id'] = NULL;
		}

		$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group', $param['information_groups_id']);

		if (isset($param['information_system_id']))
		{
			$oInformationsystem_Group->informationsystem_id = $param['information_system_id'];
		}
		elseif(is_null($param['information_groups_id']))
		{
			return 0;
		}

		$information_groups_name = trim(Core_Type_Conversion::toStr($param['information_groups_name']));

		if ($information_groups_name != '')
		{
			$oInformationsystem_Group->name = $information_groups_name;
		}
		elseif(is_null($param['information_groups_id']))
		{
			return 0;
		}

		if (isset($param['information_groups_parent_id']))
		{
			$oInformationsystem_Group->parent_id = $param['information_groups_parent_id'];
		}

		if (isset($param['information_groups_description']))
		{
			$oInformationsystem_Group->description = $param['information_groups_description'];
		}

		if (isset($param['information_groups_order']))
		{
			$oInformationsystem_Group->sorting = $param['information_groups_order'];
		}

		if (isset($param['sns_type_id']))
		{
			$oInformationsystem_Group->sns_type_id = $param['sns_type_id'];
		}

		$information_groups_path = trim(Core_Type_Conversion::toStr($param['information_groups_path']));

		if ($information_groups_path != '')
		{
			$oInformationsystem_Group->path = mb_substr($information_groups_path, 0, 255);

			$isset_url = TRUE;
		}
		elseif (is_null($param['information_groups_id'])) // При добавлении группы путь не задан
		{
			$information_system_id = intval($param['information_system_id']);

			// Транслитерация используется только если она включена в атрибутах ИС (information_systems_url_type равен 1)
			$row_information_system = $this->GetInformationSystem($information_system_id);

			if ($row_information_system['information_systems_url_type'] == 1 && trim($information_groups_name) != '')
			{
				$oInformationsystem_Group->path = mb_substr(Core_Str::transliteration($information_groups_name), 0, 255);

				// Флаг, указывающий, что url для информационной группы задан
				$isset_url = TRUE;
			}
			else
			{
				$oInformationsystem_Group->path = '';
				$isset_url = FALSE;
			}
		}

		if (isset($param['information_groups_image']) && !empty($param['information_groups_image']))
		{
			$oInformationsystem_Group->image_large = $param['information_groups_image'];

			// При редактировании удаляем файл прежднего большого изображения
			if (!is_null($oInformationsystem_Group->id))
			{
				// Получаем данные об информационной группе
				$group_info = $this->GetInformationGroup($oInformationsystem_Group->id);

				if ($group_info)
				{
					// Путь к папке информационной группы
					$information_group_dir = CMS_FOLDER . $this->GetInformationGroupDir($oInformationsystem_Group->id);

					// Имя файла большого изображения
					$fname = $information_group_dir . $group_info['information_groups_image'];

					// Имя создаваемого и существующего файла не совпадают и существует файл большого изображения
					if ($group_info['information_groups_image'] != $information_groups_image && is_file($fname))
					{
						// Удаляем файл большого изображения
						@unlink($fname);
					}
				}
			}
		}

		if (isset($param['information_groups_small_image']) && !empty($param['information_groups_small_image']))
		{
			$oInformationsystem_Group->image_small = $param['information_groups_small_image'];

			// При редактировании удаляем файл прежднего малого изображения
			if (!is_null($oInformationsystem_Group->id))
			{
				if (!isset($group_info))
				{
					// Получаем данные об информационной группе
					$group_info = $this->GetInformationGroup($information_groups_id);
				}

				if ($group_info)
				{
					if (!isset($information_group_dir))
					{
						// Путь к папке информационной группы
						$information_group_dir = CMS_FOLDER . $this->GetInformationGroupDir($information_groups_id);
					}

					// Имя файла малого изображения
					$fname = $information_group_dir . $group_info['information_groups_small_image'];

					// Имя создаваемого и существующего файла малого изображения не совпадают и существует файл малого изображения
					if ($group_info['information_groups_small_image'] != $information_groups_small_image && is_file($fname))
					{
						// Удаляем файл малого изображения
						unlink($fname);
					}
				}
			}
		}

		if (isset($param['information_groups_allow_indexation']))
		{
			$oInformationsystem_Group->indexing = $param['information_groups_allow_indexation'];
		}
		elseif(is_null($oInformationsystem_Group->id))
		{
			$oInformationsystem_Group->indexing = 1;
		}

		if (isset($param['information_groups_seo_title']))
		{
			$oInformationsystem_Group->seo_title = $param['information_groups_seo_title'];
		}

		if (isset($param['information_groups_seo_description']))
		{
			$oInformationsystem_Group->seo_description = $param['information_groups_seo_description'];
		}

		if (isset($param['information_groups_seo_keywords']))
		{
			$oInformationsystem_Group->seo_keywords = $param['information_groups_seo_keywords'];
		}

		if (isset($param['information_groups_access']))
		{
			$oInformationsystem_Group->siteuser_group_id = intval($param['information_groups_access']);
		}
		elseif(is_null($oInformationsystem_Group->id))
		{
			$oInformationsystem_Group->siteuser_group_id = -1;
		}

		if (isset($param['site_users_id']))
		{
			$oInformationsystem_Group->siteuser_id = intval($param['site_users_id']);
		}

		if (isset($param['information_groups_activity']))
		{
			$oInformationsystem_Group->active = intval($param['information_groups_activity']);
		}
		elseif (is_null($oInformationsystem_Group->id))
		{
			$oInformationsystem_Group->active = 1;
		}

		if (is_null($oInformationsystem_Group->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oInformationsystem->user_id = intval($param['users_id']);
		}

		if (!is_null($oInformationsystem_Group->id))
		{
			// Удаляем индексирование информационной группы
			if (class_exists('Search'))
			{
				$result = $this->IndexationInfGroups(0, 1, $oInformationsystem_Group->id);

				if (count($result) != 0)
				{
					$Search = & singleton('Search');
					$line = each($result);
					if (isset($line['value'][1]))
					{
						$Search->Delete_search_words($line['value'][1], $line['value'][4]);
					}
				}
			}
		}

		$oInformationsystem_Group->save();

		// Вставка
		if (is_null($param['information_groups_id']))
		{
			// Url для группы ранее не был сформирован
			if (!$isset_url)
			{
				$oInformationsystem_Group->path = $oInformationsystem_Group->id;
				$oInformationsystem_Group->save();
			}
		}

		// Удаляем информацию из кэша
		if (isset($this->MasGroup[$oInformationsystem_Group->id]))
		{
			unset($this->MasGroup[$oInformationsystem_Group->id]);
		}

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'INF_SYS_GROUP';
			$cache->DeleteCacheItem($cache_name, $oInformationsystem_Group->id);
		}

		// Добавляем индексирование информационной группы
		if (isset($param['information_groups_activity'])
		&& $param['information_groups_activity'] == 1
		&& isset($param['information_groups_allow_indexation'])
		&& intval($param['information_groups_allow_indexation']) == 1
		&& class_exists('Search'))
		{
			$result = $this->IndexationInfGroups(0, 1, $oInformationsystem_Group->id);

			if (count($result) != 0)
			{
				$Search = & singleton('Search');
				$line = each($result);
				if (isset($line['value'][1]))
				{
					$Search->Insert_search_word($result);
				}
			}
		}

		return $oInformationsystem_Group->id;
	}

	/**
	 * Вставка/обновление данных об информационном элементе. Устаревший метод
	 *
	 * @param int $type  параметр, определяющий производится вставка или обновление данных об информационном элементе (0 – вставка, 1 обновление)
	 * @param int $information_items_id идентификатор обновляемого информационного элемента (при вставке равен 0)
	 * @param int $InformationSystem_id идентификатор информационной системы, к которой принадлежит элемент
	 * @param int $information_groups_id идентификатор информационной группы, к которой принадлежит элемент
	 * @param string $information_items_date дата добавления/обновления информационного элемента;
	 * @param string $information_items_name название информационного элемента
	 * @param string $information_items_description описание информационного элемента
	 * @param int $information_items_status параметр, определяющий активность информационного элемента (0 – неактивен, 1 -активен)
	 * @param string $information_items_text текст информационного элемента
	 * @param string $information_items_image название файла изображения для информационного элемента
	 * @param int $information_items_order порядковый номер информационного элемента
	 * @param string $information_items_ip ip-адрес компьтера, с которого был добавлен инфорнмационный элемент
	 * @param string $information_items_url название информационного элемента в URL
	 * @param int $information_items_allow_indexation параметр, определяющий индексировать информационный элемент или нет (0 неиндексировать, 1- индексировать)
	 * @param string $information_items_seo_title параметр, определяющий заголовок страницы при отображении информационного элемента
	 * @param string $information_items_seo_description параметр, определяющий значение мета-тега description страницы, на которой отображается содержимое информационного элемента
	 * @param string $information_items_seo_keywords параметр, определяющий значение мета-тега keywords страницы, на которой отображается содержимое информационного элемента
	 * @param int $information_items_access параметр, определяющий тип доступа для данного информационного элемента (0 доступна всем, -1 доступ как у родителя, 6 доступно пользователю, 7 недоступно всем, 8 доступно модераторам)
	 * @param array $param  массив дополнительных параметров
	 * - $param['putoff_date'] дата начала публикации, по умолчанию текущее значение даты
	 * - $param['putend_date'] дата окончания публикации, по умолчанию отсутствует
	 * - $param['information_items_small_image'] название файла малого изображения для информационного элемента
	 * - $param['indexation'] параметр, определяющий индексировать информационный элемент или нет
	 * - $param['site_users_id'] идентификатор пользователя сайта, добавившего информационный элемент. Если не передан - определяется автоматически.
	 * - $param['show_count'] число просмотров информационного элемента
	 * @param int $users_id идентификатор пользователя, если FALSE - берется текущий пользователь.
	 * @return int идентификатор вставленного/обновленного  элемента информационной системы
	 */
	function InsertInformationItems($type, $information_items_id, $InformationSystem_id,
	$information_groups_id, $information_items_date, $information_items_name,
	$information_items_description, $information_items_status, $information_items_text,
	$information_items_image, $information_items_order, $information_items_ip,
	$information_items_url = '', $information_items_allow_indexation = 1,
	$information_items_seo_title = '', $information_items_seo_description = '',
	$information_items_seo_keywords = '', $information_items_access = -1, $param = array(),
	$users_id = FALSE)
	{
		$new_param['information_items_id'] = $information_items_id;
		$new_param['information_systems_id'] = $InformationSystem_id;
		$new_param['information_groups_id'] = $information_groups_id;
		$new_param['information_items_date'] = $information_items_date;
		$new_param['information_items_name'] = $information_items_name;
		$new_param['information_items_description'] = $information_items_description;
		$new_param['information_items_status'] = $information_items_status;
		$new_param['information_items_text'] = $information_items_text;
		$new_param['information_items_image'] = $information_items_image;
		$new_param['information_items_order'] = $information_items_order;
		$new_param['information_items_ip'] = $information_items_ip;
		$new_param['information_items_url'] = $information_items_url;
		$new_param['information_items_allow_indexation'] = $information_items_allow_indexation;
		$new_param['information_items_seo_title'] = $information_items_seo_title;
		$new_param['information_items_seo_description'] = $information_items_seo_description;
		$new_param['information_items_seo_keywords'] = $information_items_seo_keywords;
		$new_param['information_items_access'] = $information_items_access;

		if (isset($param['putoff_date']) && mb_strlen($param['putoff_date']) > 0)
		{
			$new_param['information_items_putoff_date'] = $param['putoff_date'];
		}

		if (isset($param['putend_date']) && mb_strlen($param['putend_date']) > 0)
		{
			$new_param['information_items_putend_date'] = $param['putend_date'];
		}

		if (isset($param['information_items_small_image']))
		{
			$new_param['information_items_small_image'] = $param['information_items_small_image'];
		}

		if (isset($param['site_users_id']))
		{
			$new_param['site_users_id'] = $param['site_users_id'];
		}

		if (isset($param['show_count']))
		{
			$new_param['information_items_show_count'] = $param['show_count'];
		}

		$new_param['users_id'] = $users_id;

		return $this->InsertInformationItem($new_param);

	}

	/**
	 * Вставка/обновление данных об информационном элементе
	 *
	 * @param array $param массив параметров
	 * - int $param['information_items_id'] идентификатор обновляемого информационного элемента
	 * - int $param['information_systems_id'] идентификатор информационной системы, к которой принадлежит элемент
	 * - int $param['information_groups_id'] идентификатор информационной группы, к которой принадлежит элемент
	 * - int $param['information_items_shortcut_id'] идентификатор информационного элемента, на который ссылается ярлык. По умолчанию равен 0.
	 * - string $param['information_items_date'] дата добавления/обновления информационного элемента
	 * - string $param['information_items_name'] название информационного элемента
	 * - string $param['information_items_description'] описание информационного элемента
	 * - string $param['information_items_text'] текст информационного элемента
	 * - int $param['information_items_status'] параметр, определяющий активность информационного элемента (0 – неактивен, 1 -активен)
	 * - string $param['information_items_image'] название файла изображения для информационного элемента
	 * - int $param['information_items_order'] порядковый номер информационного элемента
	 * - string $param['information_items_ip'] ip-адрес компьтера, с которого был добавлен инфорнмационный элемент
	 * - string $param['information_items_url'] название информационного элемента в URL
	 * - int $param['information_items_allow_indexation'] параметр, определяющий индексировать информационный элемент или нет (0 неиндексировать, 1- индексировать)
	 * - string $param['information_items_seo_title'] параметр, определяющий заголовок страницы при отображении информационного элемента
	 * - string $param['information_items_seo_description'] параметр, определяющий значение мета-тега description страницы, на которой отображается содержимое информационного элемента
	 * - string $param['information_items_seo_keywords'] параметр, определяющий значение мета-тега keywords страницы, на которой отображается содержимое информационного элемента
	 * - int $param['information_items_access'] параметр, определяющий тип доступа для данного информационного элемента (0 доступна всем, -1 доступ как у родителя, 6 доступно пользователю, 7 недоступно всем, 8 доступно модераторам)
	 * - string $param['information_items_putoff_date'] дата начала публикации, по умолчанию текущее значение даты
	 * - string $param['information_items_putend_date'] дата окончания публикации, по умолчанию отсутствует
	 * - string $param['information_items_small_image'] название файла малого изображения для информационного элемента
	 * - int $param['site_users_id'] идентификатор пользователя сайта, добавившего информационный элемент. Если не передан - определяется автоматически.
	 * - int $param['information_items_show_count'] число просмотров информационного элемента
	 * - mixed $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь.
	 * - bool $param['search_event_indexation'] использовать ли событийную индексацию при вставке элемента, по умолчанию true
	 *
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param = array();
	 *
	 * $param['information_systems_id'] = 1;
	 * $param['information_groups_id'] = 0;
	 * $param['information_items_date'] = '2008-03-14 11:28:12';
	 * $param['information_items_name'] = 'Тестовый элемент';
	 * $param['information_items_description'] = 'Описание тестового элемента ...';
	 * $param['information_items_text'] = 'Текст тестового элемента ...';
	 * $param['information_items_status'] = 1;
	 * $param['information_items_order'] = 10;
	 * $param['information_items_ip'] = '195.178.0.2';
	 * $param['information_items_url'] = 'test_item';
	 * $param['information_items_allow_indexation'] = 1;
	 * $param['information_items_seo_title'] = 'Тестовый элемент';
	 * $param['information_items_seo_description'] =  'Тестовый элемент';
	 * $param['information_items_seo_keywords'] = 'Тестовый элемент';
	 * $param['information_items_access'] = -1;
	 * $param['information_items_putoff_date'] = '2008-03-14 11:28:12';
	 * $param['information_items_putend_date'] = '2008-10-16 15:46:00';
	 *
	 * // Загрузка изображения для информационного элемента осуществляется аналогично загрузке
	 * // изображения при добавлении/редактировании информационной группы
	 *
	 * if ($InformationSystem->InsertInformationItem($param))
	 * {
	 * 		echo 'Элемент добавлен';
	 * }
	 * else
	 * {
	 * 		echo 'Ошибка! Элемент не добавлен!'
	 * }
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного/обновленного  элемента информационной системы
	 */
	function InsertInformationItem($param)
	{
		if (!isset($param['information_items_id']) || !$param['information_items_id'])
		{
			$param['information_items_id'] = NULL;
		}

		$information_system_id = Core_Type_Conversion::toInt($param['information_systems_id']);
		$information_items_name = Core_Type_Conversion::toStr($param['information_items_name']);

		if (is_null($param['information_items_id'])
		&& (strlen($information_items_name) == 0 && !isset($param['information_items_shortcut_id'])
			|| !$information_system_id))
		{
			return 0;
		}

		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item', $param['information_items_id']);

		if (!isset ($param['search_event_indexation']))
		{
			$param['search_event_indexation'] = TRUE;
		}

		if ($information_system_id)
		{
			$oInformationsystem_Item->informationsystem_id = $information_system_id;
		}

		if (isset($param['information_groups_id']))
		{
			$oInformationsystem_Item->informationsystem_group_id = intval($param['information_groups_id']);
		}

		if (isset($param['information_items_shortcut_id']))
		{
			$oInformationsystem_Item->shortcut_id = intval($param['information_items_shortcut_id']);
		}

		if (isset($param['information_items_date']))
		{
			$oInformationsystem_Item->datetime = $param['information_items_date'];
		}

		if ($information_items_name != '')
		{
			$oInformationsystem_Item->name = $information_items_name;
		}

		if (isset($param['information_items_description']))
		{
			$oInformationsystem_Item->description = $param['information_items_description'];
		}

		if (isset($param['information_items_status']))
		{
			$oInformationsystem_Item->active = intval($param['information_items_status']);
		}
		elseif (is_null($param['information_items_id']))
		{
			$oInformationsystem_Item->active = 0;
		}

		if (isset($param['information_items_text']))
		{
			$oInformationsystem_Item->text = $param['information_items_text'];
		}

		// Получаем путь к папке информационного элемента
		$item_dir = $this->GetInformationItemDir($param['information_items_id']);

		$uploaddir = CMS_FOLDER . $item_dir;

		$Image = & singleton('Image');

		// Загружено большое изображение
		if (isset($param['information_items_image']))
		{
			if ($param['information_items_image'] != '')
			{
				// Путь к загружаемому файлу большого изображения
				$big_image_path = $uploaddir . $param['information_items_image'];

				// Файл большого изображения существует
				if (is_file($big_image_path))
				{
					// Определяем размер большого изображения
					$size_big_image = $Image->GetImageSize($big_image_path);

					$oInformationsystem_Item->image_large = $param['information_items_image'];
					$oInformationsystem_Item->image_large_width = $size_big_image['width'];
					$oInformationsystem_Item->image_large_height = $size_big_image['height'];
				}

				// При редактировании удаляем файл прежднего большого изображения
				if (!is_null($oInformationsystem_Item->id))
				{
					// Получаем данные об информационном элементе
					$item_info = $this->GetInformationSystemItem($oInformationsystem_Item->id, array('cache_off' => TRUE));

					if ($item_info)
					{
						if ($item_info['information_items_image'] != $param['information_items_image'])
						{
							// Путь к "устаревшему" файлу большого изображения
							$fname = $uploaddir . $item_info['information_items_image'];

							// Существует файл "устаревшего" большого изображения
							if (is_file($fname))
							{
								// Удаляем файл "устаревшего" большого изображения
								@unlink($fname);
							}
						}
					}
				}
			}
		}

		// Загружено малое изображение
		if (isset($param['information_items_small_image']) && $param['information_items_small_image'] != '')
		{
			$small_image_path = $uploaddir . $param['information_items_small_image'];

			// Файл малого изображения существует
			if (is_file($small_image_path))
			{
				// Определяем размер малого изображения
				$size_small_image = $Image->GetImageSize($small_image_path);

				$oInformationsystem_Item->image_small = $param['information_items_image'];
				$oInformationsystem_Item->image_small_width = $size_small_image['width'];
				$oInformationsystem_Item->image_small_height = $size_small_image['height'];
			}

			// При редактировании удаляем файл прежднего малого изображения
			if (!is_null($oInformationsystem_Item->id))
			{
				if (!isset($item_info))
				{
					// Получаем данные об информационном элементе
					$item_info = $this->GetInformationSystemItem($oInformationsystem_Item->id, array('cache_off' => TRUE));
				}

				if ($item_info)
				{
					if ($item_info['information_items_small_image'] != $param['information_items_small_image'])
					{
						// Путь к "устаревшему" файлу малого изображения
						$fname = $uploaddir . $item_info['information_items_small_image'];

						// Существует файл малого изображения
						if (is_file($fname))
						{
							// Удаляем файл малого изображения
							@unlink($fname);
						}
					}
				}
			}
		}

		if (isset($param['information_items_order']))
		{
			$oInformationsystem_Item->sorting = intval($param['information_items_order']);
		}

		if (isset($param['information_items_ip']))
		{
			$oInformationsystem_Item->ip = $param['information_items_ip'];
		}

		if (isset($param['information_items_url']))
		{
			$information_items_url = trim(Core_Type_Conversion::toStr($param['information_items_url']));
		}
		else
		{
			$information_items_url = '';
		}

		$isset_url = FALSE;

		if ($information_items_url != '')
		{
			$oInformationsystem_Item->path = mb_substr($information_items_url, 0, 255);

			// Флаг, указывающий, что url для информационного элемента задан
			$isset_url = TRUE;
		}
		// Добавление информационного элемента или передан путь и он пустой
		elseif (is_null($oInformationsystem_Item->id)
		|| isset($param['information_items_url']) && $param['information_items_url'] == '')
		{
			// Транслитерация используется только если она включена в атрибутах ИС (information_systems_url_type равен 1)
			$row_information_system = $this->GetInformationSystem($information_system_id);

			if ($row_information_system)
			{
				if ($row_information_system['information_systems_url_type'] == 1
				&& trim($information_items_name) != '')
				{
					$oInformationsystem_Item->path = mb_substr(Core_Str::transliteration($information_items_name), 0, 255);
					$isset_url = TRUE;
				}
				else
				{
					$oInformationsystem_Item->path = '';
					$isset_url = FALSE;
				}
			}
		}

		if (isset($param['information_items_allow_indexation']))
		{
			$oInformationsystem_Item->indexing = intval($param['information_items_allow_indexation']);
		}
		// При вставке присваиваем значение по умолчанию
		elseif (is_null($oInformationsystem_Item->id))
		{
			$oInformationsystem_Item->indexing = 1;
		}

		if (isset($param['information_items_seo_title']))
		{
			$oInformationsystem_Item->seo_title = $param['information_items_seo_title'];
		}

		if (isset($param['information_items_seo_description']))
		{
			$oInformationsystem_Item->seo_description = $param['information_items_seo_description'];
		}

		if (isset($param['information_items_seo_keywords']))
		{
			$oInformationsystem_Item->seo_keywords = $param['information_items_seo_keywords'];
		}

		if (isset($param['information_items_access']))
		{
			$oInformationsystem_Item->siteuser_group_id = intval($param['information_items_access']);
		}
		// При вставке присваиваем значение по умолчанию
		elseif (is_null($oInformationsystem_Item->id))
		{
			$oInformationsystem_Item->siteuser_group_id = -1;
		}

		if (isset($param['information_items_putoff_date']) && mb_strlen($param['information_items_putoff_date']) > 0)
		{
			$oInformationsystem_Item->start_datetime = $param['information_items_putoff_date'];
		}
		// по умолчанию дата окончания публикации отсутствует
		elseif (is_null($oInformationsystem_Item->id))
		{
			$oInformationsystem_Item->start_datetime = '0000-00-00 00:00:00';
		}

		if (isset($param['information_items_putend_date']) && mb_strlen($param['information_items_putend_date']) > 0)
		{
			$oInformationsystem_Item->end_datetime = $param['information_items_putend_date'];
		}
		// по умолчанию дата окончания публикации отсутствует
		elseif (is_null($oInformationsystem_Item->id))
		{
			$oInformationsystem_Item->end_datetime = '0000-00-00 00:00:00';
		}

		if (isset($param['site_users_id']))
		{
			$oInformationsystem_Item->siteuser_id = intval($param['site_users_id']);
		}

		if (isset($param['information_items_show_count']))
		{
			$oInformationsystem_Item->showed = intval($param['information_items_show_count']);
		}

		if (is_null($oInformationsystem_Item->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oInformationsystem_Item->user_id = intval($param['users_id']);
		}

		if (!is_null($oInformationsystem_Item->id))
		{
			// Удаляем информацию о проиндексированном информационном элементе
			// до обновления, т.к. при изменении группы элементы мы не сможем удалить
			// его предыдущие данные
			if (class_exists('Search') && $param['search_event_indexation'])
			{
				$result = $this->IndexationInfItems(0, 1, $oInformationsystem_Item->id);

				if (count($result) != 0)
				{
					$Search = & singleton('Search');
					$line = each($result);

					if (isset($line['value'][1]))
					{
						$Search->Delete_search_words($line['value'][1], $line['value'][4]);
					}
				}
			}

			// Если перенос между ИС, делается до выполнения запрос на обновление ИЭ
			if ($information_system_id && !is_null($oInformationsystem_Item->id))
			{
				$old_item_row = $this->GetInformationSystemItem($oInformationsystem_Item->id, array('cache_off' => TRUE));

				// Получили информацию о существующем элементе и ИС у них не совпадает
				if ($old_item_row && $information_system_id != $old_item_row['information_systems_id'])
				{
					// Получаем путь к папке информационного элемента
					$old_item_dir = $this->GetInformationItemDir($old_item_row['information_items_id']);

					if ($old_item_dir)
					{
						$new_item_row = $old_item_row;
						$new_item_row['information_systems_id'] = $information_system_id;

						// Получаем путь к папке информационного элемента
						$new_item_dir = $this->GetInformationItemDir($old_item_row['information_items_id'], $new_item_row);

						if ($new_item_dir)
						{
							$old_item_dir = CMS_FOLDER . $old_item_dir;

							if (is_dir($old_item_dir))
							{
								if (!is_dir(CMS_FOLDER . $new_item_dir))
								{
									// Создаем папку для нового информационного элемента
									$kernel->PathMkdir($new_item_dir);
								}

								$new_item_dir = CMS_FOLDER . $new_item_dir;

								// Удаляем последнюю созданную директорию для нового размещения
								// rename: File exists
								@rmdir($new_item_dir);

								// Перемещаем файлы
								@rename($old_item_dir, $new_item_dir);
							}
						}
					}
				}
			}
		}

		// Удаляем информацию из кэша в памяти. удаляем до индексации, т.к. в кэше устаревшая информация
		if (isset($this->ItemMass[$oInformationsystem_Item->id]))
		{
			unset($this->ItemMass[$oInformationsystem_Item->id]);
		}

		$oInformationsystem_Item->save();

		// Вставка
		if (is_null($param['information_items_id']))
		{
			// Url для информационного элемента ранее не был сформирован, в качестве Url используем идентификатор
			if (!$isset_url)
			{
				$oInformationsystem_Item->path = $oInformationsystem_Item->id;
				$oInformationsystem_Item->save();
			}
		}

		// Добавляем индексирование информационного элемента
		if (isset($param['information_items_status'])
		&& intval($param['information_items_status']) == 1
		&& isset($param['information_items_allow_indexation'])
		&& intval($param['information_items_allow_indexation']) == 1
		&& class_exists('Search')
		&& $param['search_event_indexation'])
		{
			$result = $this->IndexationInfItems(0, 1, $oInformationsystem_Item->id);

			if (count($result) != 0)
			{
				$Search = & singleton('Search');
				$line = each($result);
				if (isset($line['value'][1]))
				{
					//$Search->Delete_search_words($line['value'][1], $line['value'][4]);
					$Search->Insert_search_word($result);
				}
			}
		}

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'INF_SYS_ITEM';
			$cache->DeleteCacheItem($cache_name, $oInformationsystem_Item->id);
		}

		return $oInformationsystem_Item->id;
	}

	/**
	 * Вставка/обновление информации о дополнительном свойстве информационных элементов
	 *
	 * @param array $param массив параметров
	 * - $param['information_propertys_id'] идентификатор обновляемого свойства (при вставке равен 0);
	 * - $param['information_system_id'] идентификатор информационной системы, к которой принадлежит свойство
	 * - $param['information_propertys_name'] название свойства информационной системы
	 * - $param['information_propertys_type'] код типа свойства информационной системы (0 - число, 1 - строка, 2 - файл, 3 - список, 4 - большое текстовое поле, 5 - информационная система, 6 - визуальный редактор)
	 * - $param['information_propertys_order'] порядковый номер свойства информационной системы
	 * - $param['information_propertys_define_value'] значение свойства информационной системы по умолчанию
	 * - $param['information_propertys_xml_name'] имя XML тега для данного свойства информационной системы
	 * - $param['information_propertys_lists_id'] идентификатор списка, который указывается в качестве свойства информационной системы
	 * - $param['information_propertys_information_systems_id'] идентификатор информационной системы, которая указывается в качестве свойства данной информационной системы
	 * - $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param['information_propertys_id'] = 0;
	 * $param['information_system_id'] = 15;
	 * $param['information_propertys_name'] = 'Новое свойство';
	 * $param['information_propertys_type'] = 4;
	 * $param['information_propertys_xml_name'] = 'newproperty';
	 *
	 * $newid = $InformationSystem->InsertInformationPropertys($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного/обновленного свойства информационных элементов
	 */
	function InsertInformationPropertys($param)
	{
		if (!isset($param['information_propertys_id']))
		{
			$information_propertys_id = NULL;
		}
		else
		{
			$information_propertys_id = intval($param['information_propertys_id']);
		}

		$information_system_id = intval($param['information_system_id']);
		$oInformationsystem_Item_Property_List = Core_Entity::factory('Informationsystem_Item_Property_List', $information_system_id);

		$oProperty = Core_Entity::factory('Property', $information_propertys_id);

		if (isset($param['information_propertys_name']))
		{
			$oProperty->name = $param['information_propertys_name'];
		}
		elseif(is_null($information_propertys_id))
		{
			$oProperty->name = '';
		}

		if (isset($param['information_propertys_type']))
		{
			$oProperty->type = intval($param['information_propertys_type']);
		}

		if (isset($param['information_propertys_order']))
		{
			$oProperty->sorting = intval($param['information_propertys_order']);
		}

		if (isset($param['information_propertys_define_value']))
		{
			$oProperty->default_value = $param['information_propertys_define_value'];
		}

		if (isset($param['information_propertys_xml_name']))
		{
			// Оставляем только латинские буквы и цифры
			$oProperty->tag_name = preg_replace('/[^a-zA-Z0-9а-яА-ЯЁ.\-_]/u', '', $param['information_propertys_xml_name']);
		}

		if (isset($param['information_propertys_lists_id']))
		{
			$oProperty->list_id = intval($param['information_propertys_lists_id']);
		}

		if (isset($param['information_propertys_information_systems_id']))
		{
			$oProperty->informationsystem_id = intval($param['information_propertys_information_systems_id']);
		}

		if (is_null($oProperty->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oProperty->user_id = $param['users_id'];
		}

		$oInformationsystem_Item_Property_List->add($oProperty);

		return $oProperty->id;
	}

	/**
	 * Вставка/обновление значения свойства элемента информационной системы
	 *
	 * @param int $type параметр, определяющий будет производиться вставка или обновление значения свойства информационной системы (0 - вставка, 1 - обновление)
	 * @param int $information_propertys_items_id идентификатор значения свойства информационной системы для конкретного элемента информационной системы (при вставке равен 0)
	 * Можно указать при обновлении 0, тогда будет осуществлен поиск по другим параметрам
	 * @param int $information_propertys_id идентификатор свойства информационной системы
	 * @param int $information_items_id идентификатор элемента информационной системы
	 * @param string $information_propertys_items_value значение элемента информационной системы
	 * @param string $information_propertys_items_file имя хранящегося на сервере файла - значения дополнительного свойства
	 * @param string $information_propertys_items_value_small оригинальное имя файла малого изображения
	 * @param string $information_propertys_items_file_small имя хранящегося на сервере файла малого изображения
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $type = 0;
	 * $information_propertys_items_id = 0;
	 * $information_propertys_id = 23;
	 * $information_items_id = 106;
	 * $information_propertys_items_value = '';
	 *
	 * $newid = $InformationSystem->InsertInformationPropertysItems($type, $information_propertys_items_id, $information_propertys_id, $information_items_id, $information_propertys_items_value);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного/обновленного значения свойства информационного элемента
	 */
	function InsertInformationPropertysItems($type, $information_propertys_items_id, $information_propertys_id, $information_items_id, $information_propertys_items_value, $information_propertys_items_file = '', $information_propertys_items_value_small = '', $information_propertys_items_file_small = '')
	{
		$oProperty = Core_Entity::factory('Property', $information_propertys_id);

		$aValues = $oProperty->getValues($information_items_id);

		if (count($aValues) > 0)
		{
			// Value already exist
			$oValue = $aValues[0];
		}
		else
		{
			$oValue = $oProperty->createNewValue($information_items_id);
		}

		if ($oProperty->type != 2)
		{
			$oValue->setValue($information_propertys_items_value);
		}
		else
		{
			$oValue->file = $information_propertys_items_file;
			$oValue->file_name = $information_propertys_items_value;
			$oValue->file_small = $information_propertys_items_file_small;
			$oValue->file_small_name = $information_propertys_items_value_small;
		}

		$oValue->save();

		return $oValue->id;
	}

	/**
	 * Вставка/редактирование информации о дополнительном свойстве групп информационной системы
	 *
	 * @param array $param массив параметров
	 * - $param['information_propertys_groups_id'] идентификатор дополнительного свойства информационных групп
	 * - $param['information_system_id'] идентификатор информационной системы
	 * - $param['information_propertys_groups_name'] название дополнительного свойства информационных групп
	 * - $param['information_propertys_groups_type'] тип дополнительного свойства информационных групп (0 - число, 1 - строка, 2- файл, 3 - список, 4 - большое текстовое поле, 5 - информационная система, 6 - визуальный редактор)
	 * - $param['information_propertys_groups_order'] порядковый номер информационных групп
	 * - $param['information_propertys_groups_default_value'] значение дополнительного свойства информационных групп по умолчанию
	 * - $param['information_propertys_groups_xml_name'] название xml-тега, соответсвующего данному дополнительному свойству информационных групп
	 * - $param['information_propertys_groups_lists_id'] идентификатор списка, который указывается в качестве дополнительного свойства информационных групп
	 * - $param['information_propertys_groups_information_system_id'] идентификатор информационной системы, используемой в качестве дополнительного свойства информационных группы
	 * - $param['information_propertys_groups_dir_id'] идентификатор родительского раздела свойства
	 * - $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь.
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param['information_propertys_groups_id'] = 0;
	 * $param['information_system_id'] = 15;
	 * $param['information_propertys_groups_name'] = 'Свойство 1';
	 * $param['information_propertys_groups_type'] = 4;
	 * $param['information_propertys_groups_default_value'] = '';
	 * $param['information_propertys_groups_xml_name'] = 'svoystvo1';
	 * $param['information_propertys_groups_lists_id'] = '';
	 * $param['information_propertys_groups_information_system_id'] = '';
	 *
	 * $newid = $InformationSystem->InsertInformationPropertysGroups($param);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор вставленного/обновленного дополнительного свойства информационных групп
	 */
	function InsertInformationPropertysGroups($param)
	{
		if (!isset($param['information_propertys_groups_id']))
		{
			$information_propertys_groups_id = NULL;
		}
		else
		{
			$information_propertys_groups_id = intval($param['information_propertys_groups_id']);
		}

		$information_system_id = intval($param['information_system_id']);
		$oInformationsystem_Group_Property_List = Core_Entity::factory('Informationsystem_Group_Property_List', $information_system_id);

		$oProperty = Core_Entity::factory('Property', $information_propertys_groups_id);

		if (isset($param['information_propertys_groups_name']))
		{
			$oProperty->name = $param['information_propertys_groups_name'];
		}
		elseif(is_null($information_propertys_groups_id))
		{
			$oProperty->name = '';
		}

		if (isset($param['information_propertys_groups_type']))
		{
			// Только для групп ИС
			if ($param['information_propertys_groups_type'] == 5)
			{
				$param['information_propertys_groups_type'] = 6;
			}
			elseif ($param['information_propertys_groups_type'] == 6)
			{
				$param['information_propertys_groups_type'] = 5;
			}

			$oProperty->type = intval($param['information_propertys_groups_type']);
		}

		if (isset($param['information_propertys_groups_order']))
		{
			$oProperty->sorting = intval($param['information_propertys_groups_order']);
		}

		if (isset($param['information_propertys_groups_default_value']))
		{
			$oProperty->default_value = $param['information_propertys_groups_default_value'];
		}

		if (isset($param['information_propertys_groups_xml_name']))
		{
			$oProperty->tag_name = preg_replace('/[^a-zA-Z0-9а-яА-ЯЁ.\-_]/u', '', $param['information_propertys_groups_xml_name']);
		}

		if (isset($param['information_propertys_groups_lists_id']))
		{
			$oProperty->list_id = intval($param['information_propertys_groups_lists_id']);
		}

		if (isset($param['information_propertys_groups_information_system_id']))
		{
			$oProperty->informationsystem_id = intval($param['information_propertys_groups_information_system_id']);
		}

		if (is_null($oProperty->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oProperty->user_id = $param['users_id'];
		}

		$oInformationsystem_Group_Property_List->add($oProperty);

		return $oProperty->id;
	}

	/**
	 * Вставка/обновление значения свойства информационной группы
	 *
	 * @param int $type параметр, определяющий будет производиться вставка или обновление значения дополнительного свойства информационной группы (0 - вставка, 1 - обновление)
	 * @param int $information_propertys_groups_value_id идентификатор дополнительного свойства информационной группы (при вставке равен 0)
	 * @param int $information_groups_id идентификатор информационной группы
	 * @param int $information_propertys_groups_id идентификатор дополнительного свойства группы
	 * @param string $information_propertys_groups_value_value значение дополнительного свойства информационной группы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $type = 0;
	 * $information_propertys_groups_value_id = 0;
	 * $information_groups_id = 12;
	 * $information_propertys_groups_id = 9;
	 * $information_propertys_groups_value_value = 'Значение свойства';
	 *
	 * $newid = $InformationSystem->InsertInformationPropertysGroupsValue($type, $information_propertys_groups_value_id, $information_groups_id, $information_propertys_groups_id, $information_propertys_groups_value_value);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return int идентификатор добавленнного/обновленного значения дополнительного свойства информационной группы
	 */
	function InsertInformationPropertysGroupsValue($type, $information_propertys_groups_value_id, $information_groups_id, $information_propertys_groups_id, $information_propertys_groups_value_value, $information_propertys_groups_value_file = '', $remove_xss = FALSE, $information_propertys_groups_value_value_small = '', $information_propertys_groups_value_file_small = '')
	{
		$oProperty = Core_Entity::factory('Property', $information_propertys_groups_id);

		$aValues = $oProperty->getValues($information_groups_id);

		if (count($aValues) > 0)
		{
			// Value already exist
			$oValue = $aValues[0];
		}
		else
		{
			$oValue = $oProperty->createNewValue($information_groups_id);
		}

		if ($oProperty->type != 2)
		{
			$oValue->setValue($information_propertys_groups_value_value);
		}
		else
		{
			$oValue->file = $information_propertys_groups_value_file;
			$oValue->file_name = $information_propertys_groups_value_value;
			$oValue->file_small = $information_propertys_groups_value_file_small;
			$oValue->file_small_name = $information_propertys_groups_value_value_small;
		}

		$oValue->save();

		return $oValue->id;
	}

	/**
	 * Определение числа комментариев для информационного элемента
	 *
	 * @param int $information_items_id
	 * @param int $status статус комментариев, участвующих в подсчете может принимать значения
	 * <br/>false - все комментарии,
	 * <br/>0 - неактиные комментарии,
	 * <br/>1 - активные комментарии,
	 * <br/>Значение по умолчанию равно 1.
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_items_id = 106;
	 * $status = 1;
	 *
	 * $result = $InformationSystem->GetCountCommentInformationItem($information_items_id, $status);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return mixed целое значение, FALSE в случае ошибки
	 */
	function GetCountCommentInformationItem($information_items_id, $status = 1)
	{
		$information_items_id = intval($information_items_id);

		$oComment = Core_Entity::factory('Informationsystem_Item', $information_items_id)->Comments;

		$oComment
			->queryBuilder()
			->where('text', '!=', '')
			->where('informationsystem_item_id', '=', $information_items_id);

		switch ($status)
		{
			case FALSE:
				break;
			case 0:
				$oComment->queryBuilder()->where('active', '=', 0);
				break;
			default:
				$oComment->queryBuilder()->where('active', '=', 1);
				break;
		}

		return count($oComment->findAll());
	}

	/**
	 * Вставка/обновление комментария для элемента информационной системы
	 *
	 * @param array $param массив значений
	 * - int $param['comment_id'] идентификатор обновляемого комментария (при вставке равен 0)
	 * - int $param['information_items_id'] идентитфикатор информационного элемента
	 * - int $param['comment_parent_id'] идентитфикатор родительского комментария
	 * - string $param['comment_fio'] ФИО автора комментария
	 * - string $param['comment_email'] e-mail автора комментария
	 * - string $param['comment_text'] текст комментария
	 * - string $param['comment_status'] параметр, определяющий статус активности комментария
	 * 0 – не отображать, 1 - отображать
	 * - string $param['comment_subject'] тема комментария
	 * - string $param['comment_ip'] ip-адрес отправителя комментария
	 * - string $param['comment_date'] дата отправки комментария
	 * - int $param['users_id'] идентификатор пользователя, если FALSE - берется текущий пользователь.
	 * - int $param['comment_grade'] оценка по 5 бальной шкале
	 * - int $param['users_id'] идентификатор пользователя ЦЕНТРА АДМИНИСТРИРОВАНИЯ, добавившего элемент
	 * - int $param['site_users_id'] идентификатор пользователя САЙТА, добавившего элемент
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param['comment_id'] = 0;
	 * $param['information_items_id'] = 106;
	 * $param['comment_parent_id'] = 0;
	 * $param['comment_fio'] = 'ФИО';
	 * $param['comment_email'] = 'info@site.ru';
	 * $param['comment_text'] = 'Текст комментария';
	 * $param['comment_status'] = 1;
	 * $param['comment_subject'] = 'Тема комментария';
	 * $param['comment_ip'] = '';
	 * $param['comment_date'] = date('Y-m-d H:i:s');
	 * $param['comment_grade'] = 3;
	 * $param['users_id'] = '';
	 *
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 *	$SiteUsers = & singleton('SiteUsers');
	 *	$param['site_users_id'] =$SiteUsers->GetCurrentSiteUser();
	 * }
	 * else
	 * {
	 * 	$param['site_users_id'] =0;
	 * }
	 *
	 * $newid = $InformationSystem->AddComment($param);
	 *
	 * // Распечатаем результат
	 * if ($newid)
	 * {
	 * 	echo 'Комментарий добавлен';
	 * }
	 * else
	 * {
	 * 	echo 'Ошибка! Комментарий не добавлен!';
	 * }
	 * ?>
	 * </code>
	 * @return mixed идентификатор элемента или FALSE в случае ошибки
	 */
	function AddComment($param)
	{
		$param['comment_fio'] = strip_tags(Core_Type_Conversion::toStr($param['comment_fio']));
		$param['comment_email'] = strip_tags(Core_Type_Conversion::toStr($param['comment_email']));
		$param['comment_text'] = strip_tags(Core_Type_Conversion::toStr($param['comment_text']));
		$param['comment_subject'] = strip_tags(Core_Type_Conversion::toStr($param['comment_subject']));

		return $this->AddCommentWithoutStriptags($param);
	}

	/**
	 * Вставка/обновление комментария без удаления тегов в передаваемых параметрах
	 *
	 * @param array $param массив значений
	 * - int $param['comment_id'] идентификатор обновляемого комментария (при вставке равен 0)
	 * - int $param['information_items_id'] идентитфикатор информационного элемента
	 * - int $param['comment_parent_id'] идентитфикатор родительского комментария
	 * - string $param['comment_fio'] ФИО автора комментария
	 * - string $param['comment_email'] e-mail автора комментария
	 * - string $param['comment_text'] текст комментария
	 * - string $param['comment_status'] параметр, определяющий статус активности комментария^
	 * 0 – не отображать, 1 - отображать
	 * - string $param['comment_subject'] тема комментария
	 * - string $param['comment_ip'] ip-адрес отправителя комментария
	 * - string $param['comment_date'] дата отправки комментария
	 * - int $param['comment_grade'] оценка по 5 бальной шкале
	 * - int $param['users_id'] идентификатор пользователя ЦЕНТРА АДМИНИСТРИРОВАНИЯ, добавившего элемент
	 * - int $param['site_users_id'] идентификатор пользователя, добавившего элемент (необязательное поле,
	 * - string $param['comment_phone'] номер телефона
	 * значение может быть определено автоматически)
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param['comment_id'] = 9;
	 * $param['information_items_id'] = 106;
	 * $param['comment_parent_id'] = 0;
	 * $param['comment_fio'] = 'ФИО';
	 * $param['comment_email'] = 'info@site.ru';
	 * $param['comment_text'] = 'Измененный текст комментария';
	 * $param['comment_status'] = 1;
	 * $param['comment_subject'] = 'Тема комментария';
	 * $param['comment_ip'] = '';
	 * $param['comment_date'] = date('Y-m-d H:i:s');
	 * $param['comment_grade'] = 5;
	 * $param['users_id'] = '';
	 *
	 *
	 * // Если есть модуль "Пользователи сайта", получим текущего пользователя
	 * if (class_exists('SiteUsers'))
	 * {
	 *	$SiteUsers = & singleton('SiteUsers');
	 *	$param['site_users_id'] = $SiteUsers->GetCurrentSiteUser();
	 * }
	 *
	 * else
	 * {
	 *	$param['site_users_id'] = 0;
	 * }
	 *
	 * $newid = $InformationSystem->AddCommentWithoutStriptags($param);
	 *
	 * // Распечатаем результат
	 * if ($newid)
	 * {
	 * 	echo 'Комментарий изменен';
	 * }
	 * else
	 * {
	 * 	echo 'Ошибка! Комментарий не изменен!';
	 * }
	 * ?>
	 * </code>
	 * @return mixed идентификатор элемента или FALSE в случае ошибки
	 */
	function AddCommentWithoutStriptags($param)
	{
		if (!isset($param['comment_id']) || !$param['comment_id'])
		{
			$param['comment_id'] = NULL;
		}

		$oComment = Core_Entity::factory('Comment', $param['comment_id']);

		if (isset($param['comment_parent_id']))
		{
			$oComment->parent_id = intval($param['comment_parent_id']);
		}

		if (isset($param['comment_fio']))
		{
			$oComment->author = $param['comment_fio'];
		}

		if (isset($param['comment_email']))
		{
			$oComment->email = $param['comment_email'];
		}

		if (isset($param['comment_text']))
		{
			$oComment->text = $param['comment_text'];
		}

		if (isset($param['comment_status']))
		{
			$oComment->active = (intval($param['comment_status']) != 1) ? 0 : 1;
		}

		if (isset($param['comment_subject']))
		{
			$oComment->subject = $param['comment_subject'];
		}

		if (isset($param['comment_ip']))
		{
			$oComment->ip = $param['comment_ip'];
		}

		if (isset($param['comment_date']) && preg_match("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/", $param['comment_date']))
		{
			$oComment->datetime = $param['comment_date'];
		}

		if (isset($param['comment_grade']))
		{
			$comment_grade = intval($param['comment_grade']);

			if ($comment_grade > 5)
			{
				$comment_grade = 5;
			}
			elseif ($comment_grade < 0)
			{
				$comment_grade = 0;
			}

			$oComment->grade = $comment_grade;
		}

		if (isset($param['site_users_id']))
		{
			$oComment->siteuser_id = intval($param['site_users_id']);
		}

		if (isset($param['comment_phone']))
		{
			$oComment->phone = $param['comment_phone'];
		}

		if (is_null($oComment->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oComment->user_id = $param['users_id'];
		}

		$oComment->save();

		if (isset($param['information_items_id']))
		{
			Core_Entity::factory('Informationsystem_Item', intval($param['information_items_id']))->add($oComment);
		}

		return $oComment->id;
	}

	/**
	 * Устаревший метод
	 * @param int $information_systems_id
	 * @return resource
	 * @access  private
	 */
	function SelectInformationBlocks($information_systems_id)
	{
		return $this->SelectInformationSystem($information_systems_id);
	}

	/**
	 * Получение информации об информационной системе
	 *
	 * @param int $information_systems_id идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_systems_id = 1;
	 *
	 * $resource = $InformationSystem->SelectInformationSystem($information_systems_id);
	 *
	 * // Распечатаем результат
	 * $row = mysql_fetch_assoc($resource);
	 *
	 * print_r($row);
	 *
	 * ?>
	 * </code>
	 * @return resource
	 */
	function SelectInformationSystem($information_systems_id)
	{
		$information_systems_id = intval($information_systems_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'information_systems_id'),
			array('informationsystem_dir_id', 'information_systems_dir_id'),
			'structure_id',
			'site_id',
			array('name', 'information_systems_name'),
			array('description', 'information_systems_description'),
			array('items_sorting_direction', 'information_systems_items_order_type'),
			array('items_sorting_field', 'information_systems_items_order_field'),
			array('groups_sorting_direction', 'information_systems_group_items_order_type'),
			array('groups_sorting_field', 'information_systems_group_items_order_field'),
			array('image_large_max_width', 'information_systems_image_big_max_width'),
			array('image_large_max_height', 'information_systems_image_big_max_height'),
			array('image_small_max_width', 'information_systems_image_small_max_width'),
			array('image_small_max_height', 'information_systems_image_small_max_height'),
			array('siteuser_group_id', 'information_systems_access'),
			array('use_captcha', 'information_systems_captcha_used'),
			array('watermark_file', 'information_systems_watermark_file'),
			array('watermark_default_use_large_image', 'information_systems_default_used_watermark'),
			array('watermark_default_use_small_image', 'information_systems_default_used_small_watermark'),
			array('watermark_default_position_x', 'information_systems_watermark_default_position_x'),
			array('watermark_default_position_y', 'information_systems_watermark_default_position_y'),
			array('user_id', 'users_id'),
			array('items_on_page', 'information_systems_items_on_page'),
			array('format_date', 'information_systems_format_date'),
			array('format_datetime', 'information_systems_format_datetime'),
			array('url_type', 'information_systems_url_type'),
			array('typograph_default_items', 'information_systems_typograph_item'),
			array('typograph_default_groups', 'information_systems_typograph_group'),
			array('apply_tags_automatically', 'information_systems_apply_tags_automatic'),
			array('change_filename', 'information_systems_file_name_conversion'),
			array('apply_keywords_automatically', 'information_systems_apply_keywords_automatic'),
			array('group_image_large_max_width', 'information_systems_image_big_max_width_group'),
			array('group_image_large_max_height', 'information_systems_image_big_max_height_group'),
			array('group_image_small_max_width', 'information_systems_image_small_max_width_group'),
			array('group_image_small_max_height', 'information_systems_image_small_max_height_group'),
			array('preserve_aspect_ratio', 'information_systems_default_save_proportions')
			)
			->from('informationsystems')
			->where('deleted', '=', 0);

		if ($information_systems_id != -1)
		{
			$queryBuilder->where('id', '=', $information_systems_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение данных об информационной группе
	 * Устаревший метод, ОСТАВЛЕН ДЛЯ СОВМЕСТИМОСТИ.
	 *
	 * @param int $information_groups_id идентификатор выбираемой информационной группы, если id = -1 – выбираются все группы данной информационной системы;
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * @param int $groups_parent_id идентификатор родительской группы, не обязательный параметр, по умолчанию FALSE
	 * @return возвращает массив строк с данными о выбранных информационных группах
	 * @access private
	 */
	function select_information_groups($information_groups_id, $InformationSystem_id=false, $groups_parent_id=false)
	{
		$param = array();
		$param['groups_parent_id'] = $groups_parent_id;

		return $this->SelectInformationGroups($information_groups_id, $InformationSystem_id, $param);
	}


	/**
	 * Получение информации об информационных группах
	 * Устаревший метод, ОСТАВЛЕН ДЛЯ СОВМЕСТИМОСТИ.
	 *
	 * @param int $information_groups_id идентификатор выбираемой информационной группы, если $information_groups_id = -1 – выбираются все группы данной информационной системы
	 * @param int $information_system_id идентификатотр информационной системы
	 * @param array $param массив дополнительных параметров
	 * - $param['OrderGroup'] = ASC/DESC порядок сортировки информационных групп
	 * - $param['OrderGroupField'] поле сортировки, если случайная сортировка, то записать RAND()
	 * - $param['NotInGroup'] строка с идентификаторами информационных групп (через запятую), которые (группы) необходимо исключить из результатов. Не влияет на выборку информационных элементов.
	 * - $param['groups_parent_id'] идентификатор родительской группы, необязательный параметр, по умолчанию FALSE
	 * - $param['groups_on_page'] число информационных групп, отображаемых на странице
	 * - $param['groups_begin'] номер, начиная с которого выводить информационные группы
	 * - $param['groups_activity'] параметр, учитывающий активность групп при выборке. 1 - получаем информацию только об активных группах, если не задан, то активность группы не учитывается
	 * - $param['sql_from_select_groups'] дополнения для SQL-запроса выборки в секции FROM. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * - $param['select_groups'] массив ($element) с дополнительными параметрами для задания дополнительных условий отбора информационных групп
	 * <ul>
	 * <li>$element['type'] определяет, является ли поле основным свойством информационной группы или дополнительным (0 - основное, 1 - дополнительное)
	 * <li>$element['prefix'] префикс - строка, размещаемая перед условием
	 * <li>$element['name'] имя поля для основного свойства, если свойство дополнительное, то не указывается
	 * <li>$element['property_id'] идентификатор дополнительногого свойства информационных групп
	 * <li>$element['if'] строка, содержащая условный оператор
	 * <li>$element['value'] значение поля (или параметра)
	 * <li>$element['sufix'] суффикс - строка, размещаемая после условия
	 * </ul>
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * // Получаем информацию об информационных группах, находящихся в корне информационной системы с идентификатором 1, при этом отсортированных по убыванию порядкового номера
	 * $information_groups_id = -1;
	 * $information_system_id = 1;
	 *
	 * $param['OrderGroup'] = 'DESC';
	 * $param['OrderGroupField'] = 'information_groups_order';
	 *
	 *
	 * // Формируем дополнительные условия отбора информационных групп
	 * $element['type'] = 0;
	 * $element['prefix'] = ' AND';
	 * $element['name'] = 'information_groups_parent_id';
	 * $element['if'] = '=';
	 * $element['value'] = '0';
	 * $element['sufix'] = '';
	 * $param['select_groups'][] = $element;
	 *
	 * $resource = $InformationSystem->SelectInformationGroups($information_groups_id, $information_system_id, $param);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 *	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function SelectInformationGroups($information_groups_id, $information_system_id = FALSE, $param = array())
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('informationsystem_groups.id', 'information_groups_id'),
			array('informationsystem_groups.informationsystem_id', 'information_systems_id'),
			array('siteuser_id', 'site_users_id'),
			array('parent_id', 'information_groups_parent_id'),
			array('top_parent_id', 'information_groups_top_parent_id'),
			array('name', 'information_groups_name'),
			array('description', 'information_groups_description'),
			array('sorting', 'information_groups_order'),
			array('path', 'information_groups_path'),
			array('image_large', 'information_groups_image'),
			array('image_small', 'information_groups_small_image'),
			array('indexing', 'information_groups_allow_indexation'),
			array('seo_title', 'information_groups_seo_title'),
			array('seo_description', 'information_groups_seo_description'),
			array('seo_keywords', 'information_groups_seo_keywords'),
			array('siteuser_group_id', 'information_groups_access'),
			array('active', 'information_groups_activity'),
			array('user_id', 'users_id'),
			'sns_type_id',
			array('items_count', 'count_items'),
			array('items_total_count', 'count_all_items'),
			array('subgroups_count', 'count_groups'),
			array('subgroups_total_count', 'count_all_groups')
		)
		->from('informationsystem_groups')
		->where('informationsystem_groups.deleted', '=', 0);

		if ($information_groups_id != -1 && $information_groups_id !== FALSE)
		{
			$information_groups_id = intval($information_groups_id);
			$queryBuilder->where('informationsystem_groups.id', '=', $information_groups_id);
		}

		if (isset($param['groups_activity']) && $param['groups_activity'] == 1)
		{
			$queryBuilder->where('active', '=', 1);
		}

		if (isset($param['groups_parent_id']) && $param['groups_parent_id'] !== FALSE)
		{
			$param['groups_parent_id'] = intval($param['groups_parent_id']);
			$queryBuilder->where('parent_id', '=', $param['groups_parent_id']);
		}

		if ($information_system_id !== FALSE)
		{
			$information_system_id = intval($information_system_id);
			$queryBuilder->where('informationsystem_groups.informationsystem_id', '=', $information_system_id);
		}

		if (isset ($param['sql_from_select_groups']))
		{
			$aSqlFrom = explode(',', $param['sql_from_select_groups']);

			foreach($aSqlFrom as $sSqlFrom)
			{
				trim($sSqlFrom) != '' && $queryBuilder->from(trim($sSqlFrom));
			}
		}

		$oInformationsystem = Core_Entity::factory('Informationsystem')->find($information_system_id);

		// Если явно не передано поле сортировки
		if (!isset($param['OrderGroupField']) && !is_null($oInformationsystem->id))
		{
			// Определяем поле сортировки информационных групп
			switch ($oInformationsystem->groups_sorting_field)
			{
				case 0:
					$order_field = 'informationsystem_groups.name';
					break;
				case 1:
				default:
					$order_field = 'informationsystem_groups.sorting';
					break;
			}
		}
		elseif(isset($param['OrderGroupField']))
		{
			$order_field = $param['OrderGroupField'];
		}
		else
		{
			$order_field = 'sorting';
		}

		// Если явно не передано направление сортировки
		if (!isset($param['OrderGroup']) && !is_null($oInformationsystem->id))
		{
			switch ($oInformationsystem->groups_sorting_direction)
			{
				case 0:
					$order_type = 'ASC';
					break;
				case 1:
				default:
					$order_type = 'DESC';
					break;
			}
		}
		elseif(isset($param['OrderGroup']))
		{
			$order_type = $param['OrderGroup'];
		}
		else
		{
			$order_type = 'ASC';
		}

		$queryBuilder->orderBy($order_field, $order_type);

		// Определяем ID групп, которые не надо включать в выдачу
		if (isset($param['NotInGroup']))
		{
			// Разбиваем переданные параметры и копируем в массив
			$not_in_mass = Core_Array::toInt(explode(',', $param['NotInGroup']));
			$queryBuilder->where('id', 'NOT IN', $not_in_mass);
		}

		if (isset($param['groups_on_page']))
		{
			$queryBuilder->limit(Core_Type_Conversion::toInt($param['groups_begin']), Core_Type_Conversion::toInt($param['groups_on_page']));
		}

		// формируем дополнительные условия для выборки
		if (isset($param['select_groups']) && is_array($param['select_groups']))
		{
			foreach ($param['select_groups'] as $key => $value)
			{
				if ($value['type'] == 0) // основное свойство
				{
					$this->parseQueryBuilder($value['prefix'], $queryBuilder);
					$value['value'] = Core_Type_Conversion::toStr($value['value']);

					$value['name'] != '' && $value['if'] != ''
							&& $queryBuilder->where($value['name'], $value['if'], $value['value']);

					$this->parseQueryBuilder($value['sufix'], $queryBuilder);
				}
				else // дополнительное свойство
				{
					// Ограничение для дополнительного свойства информационных групп
					if (Core_Type_Conversion::toInt($value['property_id']) != 0)
					{
						$this->parseQueryBuilder($value['prefix'], $queryBuilder);

						$queryBuilder->where('informationsystem_group_properties.property_id', '=', $value['property_id']);

						$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);

						$queryBuilder->where(
							$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $value['value']
						);

						//$query_property .= ' ' . $value['prefix'] . ' information_propertys_groups_table.information_propertys_groups_id=' . "'" . $value['property_id'] . "'" . ' AND information_propertys_groups_value_table.information_propertys_groups_value_value ' . $value['if'] . " '" . quote_smart($value['value']) . "' " . $value['sufix'] . ' ';

						$this->parseQueryBuilder($value['sufix'], $queryBuilder);
					}
				}
			}

			$queryBuilder
				->leftJoin('informationsystem_group_properties', 'informationsystem_groups.informationsystem_id', '=', 'informationsystem_group_properties.informationsystem_id')
				->leftJoin('property_value_ints', 'informationsystem_groups.id', '=', 'property_value_ints.entity_id',
					array(
						array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_ints.property_id')))
					)
				)
				->leftJoin('property_value_strings', 'informationsystem_groups.id', '=', 'property_value_strings.entity_id',
					array(
						array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_strings.property_id')))
					)
				)
				->leftJoin('property_value_texts', 'informationsystem_groups.id', '=', 'property_value_texts.entity_id',
					array(
						array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_texts.property_id')))
					)
				)
				->leftJoin('property_value_datetimes', 'informationsystem_groups.id', '=', 'property_value_datetimes.entity_id',
					array(
						array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_datetimes.property_id')))
					)
				)
				->leftJoin('property_value_files', 'informationsystem_groups.id', '=', 'property_value_files.entity_id',
					array(
						array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_files.property_id')))
					)
				);

			$queryBuilder
				->select('informationsystem_groups.id')
				->distinct();

			$mas_group_id = $queryBuilder->execute()->asAssoc()->result();

			if (count($mas_group_id) == 0)
			{
				return $mas_group_id;
			}

			$queryBuilder
				->clear()
				->select(
					array('informationsystem_groups.id', 'information_groups_id'),
					array('informationsystem_groups.informationsystem_id', 'information_systems_id'),
					array('siteuser_id', 'site_users_id'),
					array('parent_id', 'information_groups_parent_id'),
					array('top_parent_id', 'information_groups_top_parent_id'),
					array('name', 'information_groups_name'),
					array('description', 'information_groups_description'),
					array('sorting', 'information_groups_order'),
					array('path', 'information_groups_path'),
					array('image_large', 'information_groups_image'),
					array('image_small', 'information_groups_small_image'),
					array('indexing', 'information_groups_allow_indexation'),
					array('seo_title', 'information_groups_seo_title'),
					array('seo_description', 'information_groups_seo_description'),
					array('seo_keywords', 'information_groups_seo_keywords'),
					array('siteuser_group_id', 'information_groups_access'),
					array('active', 'information_groups_activity'),
					array('user_id', 'users_id'),
					'sns_type_id',
					array('items_count', 'count_items'),
					array('items_total_count', 'count_all_items'),
					array('subgroups_count', 'count_groups'),
					array('subgroups_total_count', 'count_all_groups')
					)
				->from('informationsystem_groups')
				->where('id', 'IN', $mas_group_id);
		}
		else
		{
			$queryBuilder->select(
				array('informationsystem_groups.id', 'information_groups_id'),
				array('informationsystem_groups.informationsystem_id', 'information_systems_id'),
				array('siteuser_id', 'site_users_id'),
				array('parent_id', 'information_groups_parent_id'),
				array('top_parent_id', 'information_groups_top_parent_id'),
				array('name', 'information_groups_name'),
				array('description', 'information_groups_description'),
				array('sorting', 'information_groups_order'),
				array('path', 'information_groups_path'),
				array('image_large', 'information_groups_image'),
				array('image_small', 'information_groups_small_image'),
				array('indexing', 'information_groups_allow_indexation'),
				array('seo_title', 'information_groups_seo_title'),
				array('seo_description', 'information_groups_seo_description'),
				array('seo_keywords', 'information_groups_seo_keywords'),
				array('siteuser_group_id', 'information_groups_access'),
				array('active', 'information_groups_activity'),
				array('user_id', 'users_id'),
				'sns_type_id',
				array('items_count', 'count_items'),
				array('items_total_count', 'count_all_items'),
				array('subgroups_count', 'count_groups'),
				array('subgroups_total_count', 'count_all_groups')
				);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение информации об информационных группах
	 *
	 * @param array $param массив параметров параметров
	 * - $param['information_system_id'] идентификатор информационной системы, по умолчанию равен FALSE
	 * - $param['OrderGroup'] = ASC/DESC порядок сортировки информационных групп
	 * - $param['OrderGroupField'] поле сортировки, если случайная сортировка, то записать RAND()
	 * - $param['NotInGroup'] строка с идентификаторами информационных групп (через запятую), которые (группы) необходимо исключить из результатов. Не влияет на выборку информационных элементов.
	 * - $param['groups_parent_id'] идентификатор родительской группы, необязательный параметр, по умолчанию FALSE
	 * - $param['groups_on_page'] число информационных групп, отображаемых на странице
	 * - $param['groups_begin'] номер, начиная с которого выводить информационные группы
	 * - $param['groups_activity'] параметр, учитывающий активность групп при выборке. 1 - получаем информацию только об активных группах, если не задан, то активность группы не учитывается
	 * - $param['sql_from_select_groups'] дополнения для SQL-запроса выборки в секции FROM. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * - $param['select_groups'] массив ($element) с дополнительными параметрами для задания дополнительных условий отбора информационных групп
	 * <ul>
	 * <li>$element['type'] определяет, является ли поле основным свойством информационной группы или дополнительным (0 - основное, 1 - дополнительное)
	 * <li>$element['prefix'] префикс - строка, размещаемая перед условием
	 * <li>$element['name'] имя поля для основного свойства, если свойство дополнительное, то не указывается
	 * <li>$element['property_id'] идентификатор дополнительногого свойства информационных групп
	 * <li>$element['if'] строка, содержащая условный оператор
	 * <li>$element['value'] значение поля (или параметра)
	 * <li>$element['sufix'] суффикс - строка, размещаемая после условия
	 * </ul>
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * // Получаем информацию об активных информационных группах, находящихся в корне информационной системы с идентификатором 1, при этом отсортированных по убыванию порядкового номера
	 * $param['information_system_id'] = 1;
	 * $param['groups_activity'] = 1;
	 * $param['OrderGroup'] = 'DESC';
	 * $param['OrderGroupField'] = 'information_groups_order';
	 *
	 * // Формируем дополнительные условия отбора информационных групп
	 * $element['type'] = 0;
	 * $element['prefix'] = '';
	 * $element['name'] = 'information_groups_parent_id';
	 * $element['if'] = '=';
	 * $element['value'] = '0';
	 * $element['sufix'] = '';
	 * $param['select_groups'][] = $element;
	 *
	 * $mas_groups = $InformationSystem->GetAllInformationGroups($param);
	 *
	 * // Распечатаем результат
	 * foreach ($mas_groups as $row)
	 * {
	 *	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return массив с информацией о группах
	 *
	 */
	function GetAllInformationGroups($param)
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'information_groups_id'),
			array('informationsystem_id', 'information_systems_id'),
			array('siteuser_id', 'site_users_id'),
			array('parent_id', 'information_groups_parent_id'),
			array('top_parent_id', 'information_groups_top_parent_id'),
			array('name', 'information_groups_name'),
			array('description', 'information_groups_description'),
			array('sorting', 'information_groups_order'),
			array('path', 'information_groups_path'),
			array('image_large', 'information_groups_image'),
			array('image_small', 'information_groups_small_image'),
			array('indexing', 'information_groups_allow_indexation'),
			array('seo_title', 'information_groups_seo_title'),
			array('seo_description', 'information_groups_seo_description'),
			array('seo_keywords', 'information_groups_seo_keywords'),
			array('siteuser_group_id', 'information_groups_access'),
			array('active', 'information_groups_activity'),
			array('user_id', 'users_id'),
			'sns_type_id',
			array('items_count', 'count_items'),
			array('items_total_count', 'count_all_items'),
			array('subgroups_count', 'count_groups'),
			array('subgroups_total_count', 'count_all_groups')
		)
		->from('informationsystem_groups')
		->where('deleted', '=', 0);

		if (isset($param['select_fields']))
		{
			$queryBuilder->clearSelect();
			foreach ($param['select_fields'] as $field_name)
			{
				$queryBuilder->select($field_name);
			}
		}

		if (isset($param['groups_activity']) && $param['groups_activity'] == 1)
		{
			$queryBuilder->where('active', '=', 1);
		}

		if (isset($param['xml_show_group_id'])
		&& is_array($param['xml_show_group_id'])
		&& count($param['xml_show_group_id']) > 0)
		{
			$queryBuilder->where('id', 'IN', $param['xml_show_group_id']);
		}

		if (isset($param['groups_parent_id']) && $param['groups_parent_id'] !== FALSE)
		{
			$param['groups_parent_id'] = intval($param['groups_parent_id']);
			$queryBuilder->where('parent_id', '=', $param['groups_parent_id']);
		}

		$information_system_id = Core_Type_Conversion::toInt($param['information_system_id']);

		if ($information_system_id)
		{
			$queryBuilder->where('informationsystem_id', '=', $information_system_id);
		}

		$oInformationsystem = Core_Entity::factory('Informationsystem')->find($information_system_id);

		// Если явно не передано поле сортировки
		if (!isset($param['OrderGroupField']) && !is_null($oInformationsystem->id))
		{
			// Определяем поле сортировки информационных групп
			switch ($oInformationsystem->groups_sorting_field)
			{
				case 0:
					$order_field = 'informationsystem_groups.name';
				break;
				case 1:
				default:
					$order_field = 'informationsystem_groups.sorting';
				break;
			}
		}
		elseif(isset($param['OrderGroupField']))
		{
			$order_field = $param['OrderGroupField'];
		}
		else
		{
			$order_field = 'sorting';
		}

		// Если явно не передано направление сортировки
		if (!isset($param['OrderGroup']) && !is_null($oInformationsystem->id))
		{
			switch ($oInformationsystem->groups_sorting_direction)
			{
				case 0:
					$order_type = 'ASC';
				break;
				case 1:
				default:
					$order_type = 'DESC';
				break;
			}
		}
		elseif(isset($param['OrderGroup']))
		{
			$order_type = $param['OrderGroup'];
		}
		else
		{
			$order_type = 'ASC';
		}

		$queryBuilder->orderBy($order_field, $order_type);

		// Определяем ID групп, которые не надо включать в выдачу
		if (isset($param['NotInGroup']))
		{
			// Разбиваем переданные параметры и копируем в массив
			$not_in_mass = explode(',', $param['NotInGroup']);

			$not_in_mass = Core_Array::toInt($not_in_mass);
			$queryBuilder->where('id', 'NOT IN', $not_in_mass);
		}

		if (isset($param['groups_on_page']) )
		{
			$queryBuilder->limit(Core_Type_Conversion::toInt($param['groups_begin']), Core_Type_Conversion::toInt($param['groups_on_page']));
		}

		$aGroups = $queryBuilder->execute()->asAssoc()->result();

		$aReturn = array();
		foreach($aGroups as $aRow)
		{
			$aReturn[isset($param['select_fields']) ? $aRow['id'] : $aRow['information_groups_id']] = $aRow;
		}

		return $aReturn;
	}

	/**
	 * Получение информации о группе. Использует кэш "INF_SYS_GROUP"
	 *
	 * @param int $group_id идентификатор группы
	 * @param array $property массив дополнительных параметров
	 * - $property['sql_from_select_groups'] дополнения для SQL-запроса выборки в секции FROM. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * - $property ['select_groups'] массив ($element) с дополнительными параметрами для задания дополнительных условий отбора информационных групп (пример использования данного свойства смотрите в методе GetAllInformationGroups())
	 * <ul>
	 * <li>$element['type'] определяет, является ли поле основным свойством информационной группы или дополнительным (0 - основное, 1 - дополнительное)
	 * <li>$element['prefix'] префикс - строка, размещаемая перед условием
	 * <li>$element['name'] имя поля для основного свойства, если свойство дополнительное, то не указывается
	 * <li>$element['property_id'] идентификатор дополнительногого свойства информационных групп
	 * <li>$element['if'] строка, содержащая условный оператор
	 * <li>$element['value'] значение поля (или параметра)
	 * <li>$element['sufix'] суффикс - строка, размещаемая после условия
	 * </ul>
	 * @see GetAllInformationGroups()
	 *
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $group_id = 2;
	 *
	 * $row = $InformationSystem->GetInformationGroup($group_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed массив с данными о группе или FALSE
	 */
	function GetInformationGroup($group_id, $property = array())
	{
		$group_id = intval($group_id);

		if ($group_id > 0)
		{
			$cache_name = 'INF_SYS_GROUP';

			// Не заданы дополнительные условия для группы
			if (!isset($property['select_groups']))
			{
				if (isset($this->MasGroup[$group_id]))
				{
					return $this->MasGroup[$group_id];
				}

				// Проверка на наличие в файловом кэше
				if (class_exists('Cache') && !isset($property['cache_off']))
				{
					$cache = & singleton('Cache');

					if ($in_cache = $cache->GetCacheContent($group_id, $cache_name))
					{
						$this->MasGroup[$group_id] = $in_cache['value'];
						return $in_cache['value'];
					}
				}

				$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group')->find($group_id);

				if (is_null($oInformationsystem_Group->id))
				{
					return FALSE;
				}

				$row = $this->getArrayInformationsystemGroup($oInformationsystem_Group);

				// Сохраняем значение кэше в памяти
				$this->MasGroup[$group_id] = $row;

				// Запись в файловый кэш
				if (class_exists('Cache') && !isset($property['cache_off']))
				{
					$cache->Insert($group_id, $row, $cache_name);
				}

				return $row;
			}
			else
			{
				$queryBuilder = Core_QueryBuilder::select(
					array('informationsystem_groups.id', 'information_groups_id'),
					array('informationsystem_groups.informationsystem_id', 'information_systems_id'),
					array('siteuser_id', 'site_users_id'),
					array('parent_id', 'information_groups_parent_id'),
					array('top_parent_id', 'information_groups_top_parent_id'),
					array('name', 'information_groups_name'),
					array('description', 'information_groups_description'),
					array('sorting', 'information_groups_order'),
					array('path', 'information_groups_path'),
					array('image_large', 'information_groups_image'),
					array('image_small', 'information_groups_small_image'),
					array('indexing', 'information_groups_allow_indexation'),
					array('seo_title', 'information_groups_seo_title'),
					array('seo_description', 'information_groups_seo_description'),
					array('seo_keywords', 'information_groups_seo_keywords'),
					array('siteuser_group_id', 'information_groups_access'),
					array('active', 'information_groups_activity'),
					array('user_id', 'users_id'),
					'sns_type_id',
					array('items_count', 'count_items'),
					array('items_total_count', 'count_all_items'),
					array('subgroups_count', 'count_groups'),
					array('subgroups_total_count', 'count_all_groups')
					)
					->from('informationsystem_groups')
					->where('informationsystem_groups.deleted', '=', 0);

				// Формируем дополнительные условия для выборки
				if (is_array($property['select_groups']) && count($property['select_groups']) > 0)
				{
					foreach ($property['select_groups'] as $key => $value)
					{
						if ($value['type'] == 0) // Основное свойство
						{
							$this->parseQueryBuilder($value['prefix'], $queryBuilder);

							$value['value'] = Core_Type_Conversion::toStr($value['value']);

							$value['name'] != '' && $value['if'] != ''
								&& $queryBuilder->where($value['name'], $value['if'], $value['value']);

							$this->parseQueryBuilder($value['sufix'], $queryBuilder);
						}
						else // Дополнительное свойство
						{
							if (Core_Type_Conversion::toInt($value['property_id']) != 0)
							{
								$this->parseQueryBuilder($value['prefix'], $queryBuilder);

								$queryBuilder->where('informationsystem_group_properties.property_id', '=', $value['property_id']);

								$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);

								$queryBuilder->where(
									$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $value['value']
								);

								$this->parseQueryBuilder($value['sufix'], $queryBuilder);
							}
						}
					}

					// JOIN WITH PROPERTIES
					$queryBuilder
						->leftJoin('informationsystem_group_properties', 'informationsystem_groups.informationsystem_id', '=', 'informationsystem_group_properties.informationsystem_id')
						->leftJoin('property_value_ints', 'informationsystem_groups.id', '=', 'property_value_ints.entity_id',
							array(
								array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_ints.property_id')))
							)
						)
						->leftJoin('property_value_strings', 'informationsystem_groups.id', '=', 'property_value_strings.entity_id',
							array(
								array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_strings.property_id')))
							)
						)
						->leftJoin('property_value_texts', 'informationsystem_groups.id', '=', 'property_value_texts.entity_id',
							array(
								array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_texts.property_id')))
							)
						)
						->leftJoin('property_value_datetimes', 'informationsystem_groups.id', '=', 'property_value_datetimes.entity_id',
							array(
								array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_datetimes.property_id')))
							)
						)
						->leftJoin('property_value_files', 'informationsystem_groups.id', '=', 'property_value_files.entity_id',
							array(
								array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_files.property_id')))
							)
						);
				}

				if (isset ($property['sql_from_select_groups']))
				{
					$aSqlFrom = explode(',', $property['sql_from_select_groups']);

					foreach($aSqlFrom as $sSqlFrom)
					{
						trim($sSqlFrom) != '' && $queryBuilder->from(trim($sSqlFrom));
					}
				}

				$queryBuilder->where('informationsystem_groups.id', '=', $group_id);

				$aResult = $queryBuilder->execute()->asAssoc()->result;

				if (count($aResult) == 1)
				{
					$row = $aResult[0];

					// Сохраняем значение кэше в памяти
					$this->MasGroup[$group_id] = $row;

					return $row;
				}
			}
		}

		return FALSE;
	}

	/**
	 * Устаревший метод получения данных об информационном элементе. Рекомендуется использовать GetInformationSystemItem
	 *
	 * @param int $information_items_id идентификатор выбираемого информационного элемента, если $information_items_id = FALSE – выбираем все информационные элементы данной информационной системы
	 * @param int $information_groups_id идентификатор информационной группы, элементы которой необходимо выбрать, если $information_items_id = FALSE (по умолчанию), то выбираются все элементы данной информационной системы
	 * @param int $information_system_id идентификатор информационной системы
	 * @return resource
	 * @see GetExternalInformationSystemItem()
	 */
	function select_information_items($information_items_id, $information_groups_id = FALSE, $information_system_id = FALSE)
	{
		if ($information_items_id !== FALSE)
		{
			$information_items_id = intval($information_items_id);
		}
		if ($information_groups_id !== FALSE)
		{
			$information_groups_id = intval($information_groups_id);
		}
		$information_system_id = intval($information_system_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id','information_items_id'),
			array('informationsystem_id', 'information_systems_id'),
			array('informationsystem_group_id', 'information_groups_id'),
			array('shortcut_id', 'information_items_shortcut_id'),
			array('datetime', 'information_items_date'),
			array('start_datetime', 'information_items_putoff_date'),
			array('end_datetime', 'information_items_putend_date'),
			array('name', 'information_items_name'),
			array('description', 'information_items_description'),
			array('active', 'information_items_status'),
			array('text', 'information_items_text'),
			array('image_large', 'information_items_image'),
			array('image_small', 'information_items_small_image'),
			array('image_large_width', 'information_items_image_width'),
			array('image_large_height', 'information_items_image_height'),
			array('image_small_width', 'information_items_small_image_width'),
			array('image_small_height', 'information_items_small_image_height'),
			array('sorting', 'information_items_order'),
			array('ip', 'information_items_ip'),
			array('path', 'information_items_url'),
			array('indexing', 'information_items_allow_indexation'),
			array('seo_title', 'information_items_seo_title'),
			array('seo_description', 'information_items_seo_description'),
			array('seo_keywords', 'information_items_seo_keywords'),
			array('siteuser_group_id', 'information_items_access'),
			array('showed', 'information_items_show_count'),
			array('user_id', 'users_id'),
			array('siteuser_id', 'site_users_id')
		)
		->from('informationsystem_items')
		->where('deleted', '=', 0);

		if ($information_items_id === FALSE)
		{
			if ($information_groups_id)
			{
				$queryBuilder->where('informationsystem_group_id', '=', $information_groups_id);
			}
		}
		else
		{
			$queryBuilder->where('informationsystem_items.id', '=', $information_items_id);
		}
		// задана информационная система
		if ($information_system_id)
		{
			$queryBuilder->where('informationsystem_id', '=', $information_system_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Получение информации об элементах, находящихся в информационной группе
	 *
	 * @param int $information_groups_id идентификатор информационной группы
	 * @param int $information_systems_id идентификатор информационной системы
	 * @param array $param массив дополнительных параметров
	 * - $param['OrderField'] поле сортировки
	 * - $param['Order'] направление сортировки
	 *
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_id = 2;
	 * $information_systems_id = 1;
	 *
	 * // Сортируем элементы по убыванию порядкового номера
	 * $param = array();
	 * $param['OrderField'] = 'information_items_order';
	 * $param['Order'] = 'DESC';
	 *
	 * $resource = $InformationSystem->GetInformationItemsFromGroup($information_groups_id, $information_systems_id, $param);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 		print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function GetInformationItemsFromGroup($information_groups_id, $information_systems_id = FALSE, $param = array())
	{
		$information_groups_id = intval($information_groups_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id','information_items_id'),
				array('informationsystem_id', 'information_systems_id'),
				array('informationsystem_group_id', 'information_groups_id'),
				array('shortcut_id', 'information_items_shortcut_id'),
				array('datetime', 'information_items_date'),
				array('start_datetime', 'information_items_putoff_date'),
				array('end_datetime', 'information_items_putend_date'),
				array('name', 'information_items_name'),
				array('description', 'information_items_description'),
				array('active', 'information_items_status'),
				array('text', 'information_items_text'),
				array('image_large', 'information_items_image'),
				array('image_small', 'information_items_small_image'),
				array('image_large_width', 'information_items_image_width'),
				array('image_large_height', 'information_items_image_height'),
				array('image_small_width', 'information_items_small_image_width'),
				array('image_small_height', 'information_items_small_image_height'),
				array('sorting', 'information_items_order'),
				array('ip', 'information_items_ip'),
				array('path', 'information_items_url'),
				array('indexing', 'information_items_allow_indexation'),
				array('seo_title', 'information_items_seo_title'),
				array('seo_description', 'information_items_seo_description'),
				array('seo_keywords', 'information_items_seo_keywords'),
				array('siteuser_group_id', 'information_items_access'),
				array('showed', 'information_items_show_count'),
				array('user_id', 'users_id'),
				array('siteuser_id', 'site_users_id')
			)
			->from('informationsystem_items')
			->where('informationsystem_group_id', '=', $information_groups_id)
			->where('deleted', '=', 0);

		if ($information_systems_id !== FALSE)
		{
			$queryBuilder->where('informationsystem_id', '=', intval($information_systems_id));
		}

		if (isset($param['OrderField']))
		{
			$queryBuilder->orderBy($param['OrderField'], isset($param['Order']) ? $param['Order'] : 'ASC');
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение данных о дополнительных свойствах элементов информационной системы
	 *
	 * @param int $information_systems_id идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_systems_id = 1;
	 *
	 * $resource = $InformationSystem->GetAllInformationItemsPropertys($information_systems_id);
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
	function GetAllInformationItemsPropertys($information_systems_id)
	{
		$information_systems_id = intval($information_systems_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('properties.id', 'information_propertys_id'),
			array('informationsystem_item_properties.informationsystem_id', 'information_systems_id'),
			array('property_dir_id', 'information_propertys_items_dir_id'),
			array('name', 'information_propertys_name'),
			array('type', 'information_propertys_type'),
			array('sorting', 'information_propertys_order'),
			array('default_value', 'information_propertys_define_value'),
			array('tag_name', 'information_propertys_xml_name'),
			array('list_id', 'information_propertys_lists_id'),
			array('properties.informationsystem_id', 'information_propertys_information_systems_id'),
			array('user_id', 'users_id'),
			array('image_large_max_width', 'information_propertys_default_big_width'),
			array('image_small_max_width', 'information_propertys_default_small_width'),
			array('image_large_max_height', 'information_propertys_default_big_height'),
			array('image_small_max_height', 'information_propertys_default_small_height')
		)
			->from('properties')
			->join('informationsystem_item_properties', 'properties.id', '=', 'informationsystem_item_properties.property_id')
			->where('informationsystem_item_properties.informationsystem_id', '=', $information_systems_id)
			->where('deleted', '=', 0)
			->orderBy('sorting');

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение информации о значении дополнительного свойства информационного элемента по ID связи таблицы information_propertys_items_table
	 *
	 * @param int $information_propertys_items_id идентификатор значения дополнительного свойства
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_items_id = 99;
	 *
	 * $row = $InformationSystem->GetInformationItemsPropertyValue($information_propertys_items_id);
	 *
	 *  // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed ассоциативный массив с данными о значении дополнительного свойства информационного элемента или FALSE
	 */
	function GetInformationItemsPropertyValue($information_propertys_items_id)
	{
		throw new Core_Exception('Method GetInformationItemsPropertyValue() does not allow');
	}

	/**
	 * Получение информации о значении дополнительного свойства информационного элемента
	 *
	 * @param int $information_propertys_items_id идентификатор значения дополнительного свойства
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_items_id = 123;
	 * $information_propertys_id = 3;
	 *
	 * $row = $InformationSystem->GetInformationItemPropertyValue($information_items_id, $information_propertys_id);
	 *
	 *  // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed ассоциативный массив с данными о значении дополнительного свойства информационного элемента или FALSE
	 */
	function GetInformationItemPropertyValue($information_items_id, $information_propertys_id)
	{
		$oProperty = Core_Entity::factory('Property', $information_propertys_id);
		$aPropertyValues = $oProperty->getValues($information_items_id);

		if (isset($aPropertyValues[0]))
		{
			return $this->getArrayItemPropertyValue($aPropertyValues[0]);
		}

		return FALSE;
	}

	/**
	 * Получение данных о дополнительных свойствах информационных групп
	 *
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $InformationSystem_id = 1;
	 *
	 * $resource = $InformationSystem->GetAllInformationGroupsPropertys($InformationSystem_id);
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
	function GetAllInformationGroupsPropertys($InformationSystem_id)
	{
		$queryBuilder = Core_QueryBuilder::select('properties.*', 'informationsystem_id')
			->from('properties')
			->join('informationsystem_group_properties', 'properties.id', '=', 'informationsystem_group_properties.property_id')
			->where('informationsystem_id', '=', $InformationSystem_id)
			->where('deleted', '=', 0)
			->orderBy('sorting');

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение данных о дополнительном свойстве групп информационной системы
	 *
	 * @param int $information_propertys_groups_id идентификатор связи таблицы свойства информационной группы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_groups_id = 9;
	 *
	 * $row = $InformationSystem->GetInformationGroupsPropertys($information_propertys_groups_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с информацией о дополнительном свойстве информационных групп
	 */
	function GetInformationGroupsPropertys($information_propertys_groups_id)
	{
		$oProperty = Core_Entity::factory('Property')->find($information_propertys_groups_id);

		if (!is_null($oProperty->id))
		{
			return $this->getArrayGroupProperty($oProperty);
		}

		return FALSE;
	}

	/**
	 * Получение информации о значении дополнительного свойства информационных групп
	 *
	 * @param int $information_groups_property_value_id идентификатор значения дополнительного свойства
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_property_value_id = 31;
	 *
	 * $row = $InformationSystem->GetInformationGroupsPropertyValue($information_groups_property_value_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed ассоциативный массив с данными о  значении дополнительного свойства информационных групп в случае успешного выполнения, FALSE - в противном случае
	 */
	function GetInformationGroupsPropertyValue($information_groups_property_value_id)
	{
		throw new Core_Exception('Method GetInformationGroupsPropertyValue() does not allow');
	}

	/**
	 * Получение списка дополнительных свойств информационных группы информационной системы
	 *
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $InformationSystem_id = 1;
	 *
	 * $resource = $InformationSystem->SelectListInformationGroupPropertys($InformationSystem_id);
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
	function SelectListInformationGroupPropertys($InformationSystem_id)
	{
		$queryBuilder = Core_QueryBuilder::select('properties.*', 'informationsystem_id')
			->from('properties')
			->join('informationsystem_group_properties', 'properties.id', '=', 'informationsystem_group_properties.property_id')
			->where('informationsystem_id', '=', $InformationSystem_id)
			->where('deleted', '=', 0)
			->orderBy('sorting');

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение комментариев к информационному элементу
	 *
	 * @param int $comment_id идентификатор комментария (если  равен -1 - получаем информацию о всех комментариях информационного элемента)
	 * @param int $information_items_id идентификатор информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $comment_id = 9;
	 * $information_items_id = 106;
	 *
	 * $resource = $InformationSystem->select_comments($comment_id, $information_items_id);
	 *
	 * // Распечатаем результат
	 * $row = mysql_fetch_assoc($resource);
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return resource
	 */
	function select_comments($comment_id, $information_items_id)
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('comments.id', 'comment_id'),
			array('parent_id', 'comment_parent_id'),
			array('comment_informationsystem_items.	informationsystem_item_id', 'information_items_id'),
			array('author', 'comment_fio'),
			array('email', 'comment_email'),
			array('text', 'comment_text'),
			array('active', 'comment_status'),
			array('subject', 'comment_subject'),
			array('ip', 'comment_ip'),
			array('datetime', 'comment_date'),
			array('grade', 'comment_grade'),
			array('phone', 'comment_phone'),
			array('siteuser_id', 'site_users_id'),
			array('user_id', 'users_id')
		)
			->from('comments')
			->join('comment_informationsystem_items', 'comments.id', '=', 'comment_informationsystem_items.comment_id')
			->where('informationsystem_item_id', '=', $information_items_id)
			->where('deleted', '=', 0)
			->orderBy('datetime', 'DESC');

		if ($comment_id != -1)
		{
			$queryBuilder->where('comment_id', '=', $comment_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение числа комментариев к информационным элементам
	 *
	 * @param mixed $information_item_id идентификатор информационного элемента, для которого необходимо получить число комментариев. Если $information_item_id равен FALSE (по умолчанию), то получаем число комментариев для всех элементов всех инфосистем
	 * @param mixed $is_active статус активности отбираемых комментариев. Возможные значения:
	 * - 0 - неактивные
	 * <br />1 - активные
	 * - FALSE - все
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_item_id = FALSE;
	 * $is_active = 1;
	 *
	 * $count_comments = $InformationSystem->GetCountComments($information_item_id, $is_active);
	 *
	 * // Распечатаем результат
	 * echo $count_comments;
	 * ?>
	 * </code>
	 * @return  mixed  число комментариев в случае успешного выполнения, FALSE - в противном случае
	 */
	function GetCountComments($information_item_id = FALSE, $is_active = 1)
	{
		$queryBuilder = Core_QueryBuilder::select(array('COUNT(*)', 'count_comments'))
			->from('comment_informationsystem_items');

		if ($information_item_id !== FALSE)
		{
			$information_item_id = intval($information_item_id);
			$queryBuilder->where('informationsystem_item_id', '=', $information_item_id);
		}

		// Посчитать активные или неактивные комментарии
		if ($is_active !== FALSE)
		{
			$is_active = intval($is_active);

			$queryBuilder
				->join('comments', 'comment_informationsystem_items.comment_id', '=', 'comments.id')
				->where('deleted', '=', 0);

			if ($is_active == 0)
			{
				$queryBuilder->where('active', '=', 0);
			}
			elseif ($is_active == 1)
			{
				$queryBuilder->where('active', '=', 1);
			}
		}

		$aResult = $queryBuilder->execute()->asAssoc()->current();

		return $aResult['count_comments'];
	}

	/**
	 * Устаревший метод удаления информационной системы
	 *
	 * @param int $information_systems_id
	 * @return recource
	 * @access private
	 */
	function del_information_blocks($information_systems_id)
	{
		return $this->DelInformationSystem($information_systems_id);
	}

	/**
	 * Удаление информационной системы
	 *
	 * @param int $information_systems_id идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_systems_id = 16;
	 *
	 * $resource = $InformationSystem->DelInformationSystem($information_systems_id);
	 *
	 * if ($resource)
	 * {
	 *	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function DelInformationSystem($information_systems_id)
	{
		$information_systems_id = intval($information_systems_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'INF_SYS';
			$cache->DeleteCacheItem($cache_name, $information_systems_id);
		}

		Core_Entity::factory('informationsystem', $information_systems_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Удаление элемента информационной системы
	 *
	 * @param int $information_items_id идентификатор удаляемого информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_items_id = 106;
	 *
	 * $result = $InformationSystem->del_information_items($information_items_id);
	 *
	 * if ($result)
	 * {
	 *	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return boolean true - в случае успешного удаления, FALSE - при возникновении ошибки
	 */
	function del_information_items($information_items_id)
	{
		$information_items_id = intval($information_items_id);

		// Очистка файлового кэша
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'INF_SYS_ITEM';
			$cache->DeleteCacheItem($cache_name, $information_items_id);
		}

		// Событийная индексация
		if (class_exists('Search'))
		{
			$result = $this->IndexationInfItems(0, 1, $information_items_id);
			if (count($result) != 0)
			{
				$line = each($result);
				if (class_exists('Search') && isset($line['value'][1]))
				{
					$Search = & singleton('Search');
					$Search->Delete_search_words($line['value'][1], $line['value'][4]);
				}
			}
		}

		Core_Entity::factory('Informationsystem_Item', $information_items_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Удаление дополнительного свойства элементов информационной системы
	 *
	 * @param int $information_propertys_id идентификатор удаляемого свойства элементов информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_id = 99;
	 *
	 * $result = $InformationSystem->del_information_propertys($information_propertys_id);
	 *
	 * if ($result)
	 * {
	 *	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return boolean true - в случае успешного удаления, FALSE - при возникновении ошибки
	 */
	function del_information_propertys($information_propertys_id)
	{
		$information_propertys_id = intval($information_propertys_id);

		Core_Entity::factory('Property', $information_propertys_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Удаление значения дополнительного свойства информационного элемента
	 *
	 * @param int $information_propertys_item_id идентификатор зачения дополнительного свойства информационного элемента
	 * @param array $param массив дополнительных параметров
	 * - $param['del_big_image'] параметр, определяющий удалять файл большого изображения или нет (true - удалять (по умолчанию), FALSE - не удалять)
	 * - $param['del_small_image'] параметр, определяющий удалять файл малого изображения или нет (true - удалять (по умолчанию), FALSE - не удалять)
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_item_id= 67;
	 *
	 * $result = $InformationSystem->DelInformationItemPropertyValue($information_propertys_item_id);
	 *
	 * if ($result)
	 * {
	 *	echo "Удаление выполнено успешно";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка удаления";
	 * }
	 * ?>
	 * </code>
	 * @return boolean true - значение дополнительного свойства элемента удалено, FALSE - при возникновении ошибки
	 */
	function DelInformationItemPropertyValue($information_propertys_item_id, $param=array())
	{
		throw new Core_Exception('Method DelInformationItemPropertyValue() does not allow');
	}

	/**
	 * Удаление дополнительного свойства информационной группы
	 *
	 * @param int $property_id идентификатор дополнительного свойства информационной группы
	 * @param $group_id идентификатор группы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $property_id= 9;
	 *
	 * $result = $InformationSystem->DelInformationGroupPropertys($property_id);
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
	 * @return boolean true - в случае успешного удаления, FALSE - при возникновении ошибки
	 */
	function DelInformationGroupPropertys($property_id, $group_id = FALSE)
	{
		Core_Entity::factory('Property', $property_id)->markDeleted();

		return TRUE;
	}

	/**
	 *
	 * WARNING!!! Не изменен
	 * Удаление значений дополнительных свойств информационной группы по идентификатору дополнительного свойства и информационной группы
	 *
	 * @param int $property_id идентификатор дополнительного свойства информационной группы
	 * @param int $group_id идентификатор информационной группы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $property_id= 8;
	 * $group_id = 12;
	 *
	 * $result = $InformationSystem->DelInformationGroupPropertysValue($property_id, $group_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Значение дополнительного свойства инфорационной группы удалено";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка! Значение дополнительного свойства информационной группы не удалено!";
	 * }
	 * ?>
	 * </code>
	 * @return boolean true - в случае успешного удаления, FALSE - при возникновении ошибки
	 */
	function DelInformationGroupPropertysValue($property_id, $group_id, $param = array('del_big_image' => TRUE, 'del_small_image' => TRUE))
	{
		$property_id = intval($property_id);
		$group_id = intval($group_id);

		$oProperty = Core_Entity::factory('Property', $property_id);

		$aProperty_Values = $oProperty->getValues($group_id);

		foreach ($aProperty_Values as $oProperty_Value)
		{
			if ($oProperty->type == 2)
			{
				$oProperty_Value->setHref(
					Core_Entity::factory('Informationsystem_Group', $group_id)->getGroupHref()
				);
			}

			$oProperty_Value->delete();
		}

		return TRUE;
	}

	/**
	 * Удаление значения дополнительного свойства информационной группы по идентификатору значения дополнительного свойства
	 *
	 * @param int $property_value_id идентификатор значения дополнительного свойства информационной группы
	 * @param array $param массив дополнительных параметров
	 * - $param['del_big_image'] параметр, определяющий удалять файл большого изображения или нет (true - удалять (по умолчанию), FALSE - не удалять)
	 * - $param['del_small_image'] параметр, определяющий удалять файл малого изображения или нет (true - удалять (по умолчанию), FALSE - не удалять)
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $property_value_id = 8;
	 *
	 * $result = $InformationSystem->DelInformationGroupPropertyValue($property_value_id);
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
	 * @return boolean true - в случае успешного удаления, FALSE - при возникновении ошибки
	 */
	function DelInformationGroupPropertyValue($property_value_id, $param)
	{
		throw new Core_Exception('Method DelInformationGroupPropertyValue() does not allow');
	}

	/**
	 * Удаление комментария к информационному элементу
	 *
	 * @param int $comment_id идентификатор комментария
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $comment_id = 10;
	 *
	 * $result = $InformationSystem->del_comment($comment_id);
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
	 * @return boolean true - в случае успешного удаления, FALSE - при возникновении ошибки
	 */
	function del_comment($comment_id)
	{
		Core_Entity::factory('Comment', $comment_id)->markDeleted();
		return TRUE;
	}

	/**
	 * WARNING!!! Не переписан
	 * Копирование комментария к информационному элементу
	 *
	 * @param int $comment_id идентификатор копируемого комментария
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $comment_id = 11;
	 *
	 * $newid = $InformationSystem->CopyComment($comment_id);
	 *
	 * // Распечатаем результат
	 * if ($newid)
	 * {
	 * 	echo 'Комментарий скопирован';
	 * }
	 * else
	 * {
	 * 	echo 'Ошибка! Комментарий не скопирован!';
	 * }
	 * ?>
	 * </code>
	 * @return mixed идентификатор копии коментария в случае успешного выполнения, в противном случае -  FALSE
	 */
	function CopyComment($comment_id)
	{
		$comment_id = intval($comment_id);

		if (!$comment_id)
		{
			return FALSE;
		}

		$oComment = Core_Entity::factory('Comment')->find($comment_id);
		$oNewComment = $oComment->copy();
		$oNewComment->add($oComment->Comment_Informationsystem_Item->copy());

		return $oNewComment->id;
	}

	/**
	 * Удаление информационной группы
	 *
	 * @param int $information_groups_id идентификатор удаляемой информационной группы
	 * @param int $InformationSystem_id идентификатор информационной системы, к которой принадлежит информационная группа
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_id = 1;
	 * $InformationSystem_id = 1;
	 *
	 * $result = $InformationSystem->del_information_groups($information_groups_id, $InformationSystem_id);
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
	 * @return boolean true - в случае успешного удаления, FALSE - при возникновении ошибки
	 */
	function del_information_groups($information_groups_id, $InformationSystem_id)
	{
		Core_Entity::factory('Informationsystem_Group', $information_groups_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Формирование дерева групп информационной системы
	 *
	 * @param int $information_groups_parent_id идентификатор группы, относительно которой строится дерево групп
	 * @param int $InformationSystem_id идентификатор информационной системы, для которой строится дерево групп
	 * @param string $separator символ, отделяющий группу нижнего уровня от родительской группы
	 * @param int $information_grops_id идентификатор группы, которую вместе с ее подгруппами не нужно включать в дерево групп, если id = FALSE, то включать в дерево групп все подгруппы.
	 * @param array $property дополнительные атрибуты
	 * - $property['cache'] - использование кэширования, по умолчанию true
	 * - $property['array'] - служебный элемент
	 * - $property['sum_separator'] - служебный элемент
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_parent_id = 0;
	 * $InformationSystem_id = 15;
	 * $separator = '';
	 *
	 * $row = $InformationSystem->GetGroupsInformationSystem($information_groups_parent_id, $InformationSystem_id, $separator);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array двумерный массив, содержащий дерево подгрупп
	 */
	function GetGroupsInformationSystem($information_groups_parent_id, $InformationSystem_id,
	$separator = '', $information_grops_id = FALSE, $property = array())
	{
		$information_groups_parent_id = intval($information_groups_parent_id);
		$InformationSystem_id = intval($InformationSystem_id);

		if ($information_grops_id !== FALSE)
		{
			$information_grops_id = intval($information_grops_id);
		}

		if (!isset($property['cache']))
		{
			$property['cache'] = TRUE;
		}

		if (!isset($property['sum_separator']))
		{
			$property['sum_separator'] = $separator;
		}
		else
		{
			$property['sum_separator'] = $property['sum_separator'] . $separator;
		}

		/* Если модуль кэширования подключен*/
		if ($property['cache'] && class_exists('Cache'))
		{
			$kernel = & singleton('kernel');
			$cache_element_name = $information_groups_parent_id . "_" . $InformationSystem_id . "_" . $separator . $information_grops_id . $kernel->implode_array($property, '_');

			$cache = & singleton('Cache');

			$cache_name = 'INF_SYS_GROUPS_TREE';
			if (($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name)) && $in_cache)
			{
				return $in_cache['value'];
			}
		}

		// если выборка групп от корня - выбираем все группы ИС
		//if ($information_groups_parent_id === 0)
		if (/*$information_groups_parent_id == 0 && */
		!isset($this->FullCacheGoupsIdTree[$InformationSystem_id]))
		{
			// Выбираем данные обо всех группах
			$this->FillMasGroup($InformationSystem_id);

			// т.к. функционал FillMemFullCacheGoupsIdTree внесен в FillMasGroup
			//$this->FillMemFullCacheGoupsIdTree($InformationSystem_id, $property);
		}

		$array = array();

		if (isset($this->FullCacheGoupsIdTree[$InformationSystem_id][$information_groups_parent_id]))
		{
			$aParamTmp = array();
			$aParamTmp['information_system_id'] = $InformationSystem_id;
			$aParamTmp['groups_parent_id'] = $information_groups_parent_id;

			$aTmpGroups = $this->GetAllInformationGroups($aParamTmp);

			foreach ($aTmpGroups as $aTmpGroupsRow)
			{
				// Пишем в кэш для информационных групп
				$this->MasGroup[$aTmpGroupsRow['information_groups_id']] = $aTmpGroupsRow;
			}

			unset($aTmpGroups);

			// Разбираем закэшированное дерево групп для данного родителя
			foreach ($this->FullCacheGoupsIdTree[$InformationSystem_id][$information_groups_parent_id] as $group_id)
			{
				$row = $this->GetInformationGroup($group_id);

				if (isset($this->MasGroup[$group_id]))
				{
					unset($this->MasGroup[$group_id]);
				}

				if ($information_grops_id != $row['information_groups_id'])
				{
					$row['separator'] = $property['sum_separator'];

					$array[] = $row;

					// Объединяем выбранные данные с данными из подгрупп
					$array = array_merge($array, $this->GetGroupsInformationSystem($row['information_groups_id'], $InformationSystem_id, $separator, $information_grops_id, $property));
				}
			}
		}

		if ($property['cache'] && class_exists('Cache'))
		{
			$cache->Insert($cache_element_name, $array, $cache_name);
		}

		return $array;
	}

	/**
	 * Формирование пути по дереву информационных групп
	 *
	 * @param int $information_groups_id идентификатор информационной группы, для которой необходимо построить путь
	 * @param int $InformationSystem_id идентификатор информационной системы, к которой принадлежит группа.
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_id = 20;
	 * $InformationSystem_id = 1;
	 * $row = $InformationSystem->get_information_groups_path($information_groups_id, $InformationSystem_id);
	 *
	 * // Распечатаем результат
	 * echo $InformationSystem->section_path;
	 * ?>
	 * </code>
	 */
	function get_information_groups_path($information_groups_id, $InformationSystem_id)
	{
		$information_groups_id = intval($information_groups_id);
		$InformationSystem_id = intval($InformationSystem_id);

		$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group')->find($information_groups_id);

		if (is_null($oInformationsystem_Group->id))
		{
			$oInformationsystem = Core_Entity::factory('Informationsystem')->find($InformationSystem_id);

			$this->section_path='<a href=InformationSystems.php>Информационные системы</a> // <a href=InformationSystems.php?show_information_groups=1&information_systems_id=' . $InformationSystem_id . '>' . str_for_xml($oInformationsystem->name) . '</a> //' . $this->section_path;
		}
		else
		{
			$this->section_path='<a href=InformationSystems.php?show_information_groups=1&information_systems_id=' . $InformationSystem_id . '&information_groups_parent_id=' . $oInformationsystem_Group->id . '>' . $oInformationsystem_Group->name . '</a> // ' . $this->section_path;
			$this->get_information_groups_path($oInformationsystem_Group->parent_id, $InformationSystem_id);
		}
	}

	/**
	 * Построение массива пути от текущей группы к корневой
	 *
	 * @param int $information_group_id идентификатор информационной группы, для которой необходимо построить путь
	 * @param int $information_system_id идентификатор информационной системы, к которой принадлежит группа.
	 * @param array $return_path_array
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_group_id = 12;
	 * $information_system_id = 1;
	 *
	 * $row = $InformationSystem->GetInformationGroupsPathArray($information_group_id, $information_system_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array ассоциативный массив, элементы которого содержат информацию о группах, составляющих путь от текущей группы до корневой
	 */
	function GetInformationGroupsPathArray($information_group_id, $information_system_id, $return_path_array = array())
	{
		$information_group_id = intval($information_group_id);
		$information_system_id = intval($information_system_id);

		if (!$information_group_id)
		{
			// Определяем название информационной системы
			$row_inf_sys = $this->GetInformationSystem($information_system_id);

			if ($row_inf_sys)
			{
				$return_path_array[0] = array('information_groups_name' => $row_inf_sys['information_systems_name']);
			}
		}
		else
		{
			$row = $this->GetInformationGroup($information_group_id);
			$return_path_array[$row['information_groups_id']] = $row;
			$return_path_array = $this->GetInformationGroupsPathArray($row['information_groups_parent_id'], $information_system_id, $return_path_array);
		}

		return $return_path_array;
	}

	/**
	 * Устаревший метод.
	 * Заполянет массив $this->mas_information_groups_for_xml данными от текущей группе до корня
	 *
	 * @param int $information_groups_id идентификатор группы
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * @return boll true
	 * @access private
	 */
	function GetInformationGroupsForXml($information_groups_id, $InformationSystem_id)
	{
		$information_groups_id = intval($information_groups_id);
		$InformationSystem_id = intval($InformationSystem_id);

		$row = $this->GetInformationGroup($information_groups_id);

		if (!$row)
		{
			return $this->mas_information_groups_for_xml;
		}
		else
		{
			$count = count($this->mas_information_groups_for_xml);
			$this->mas_information_groups_for_xml[$count] = $row;
			$this->mas_information_groups_for_xml[$count][0] = $row['information_groups_id'];
			$this->mas_information_groups_for_xml[$count][1] = $row['information_groups_name'];
			$this->mas_information_groups_for_xml[$count][2] = $row['information_groups_description'];
			$this->GetInformationGroupsForXml($row['information_groups_parent_id'], $InformationSystem_id);
		}
		return TRUE;
	}

	/**
	 * Формирование XML дерева для информационных групп
	 *
	 * @param int $information_groups_parent_id идентификатор информационной группы, подгруппы которой будут включены для построения XML дерева
	 * @param int $InformationSystem_id идентификатор информационной системы, для групп которой будет строиться XML дерево
	 * @param string $xmlData XML с данными об информационной системе
	 * @param int $items_on_page параметр, указывающий количество элементов информационной системы отображаемых на странице
	 * @param int $items_begin параметр, указывающий с какого информационного элемента отображать элементы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_parent_id = 0;
	 * $InformationSystem_id = 1;
	 * $xmlData = '';
	 * $items_on_page = 5;
	 * $items_begin = 1;
	 *
	 * $xml = $InformationSystem->CreateGroupsXmlTree($information_groups_parent_id, $InformationSystem_id, $xmlData, $items_on_page, $items_begin);
	 *
	 * // Распечатаем результат
	 * echo nl2br(htmlspecialchars($xml));
	 * ?>
	 * </code>
	 * @return string XML дерево с данными об информационной системе и ее группах
	 */
	function CreateGroupsXmlTree($information_groups_parent_id, $InformationSystem_id, $xmlData, $items_on_page, $items_begin)
	{
		$information_groups_parent_id = intval($information_groups_parent_id);

		$items_on_page = intval($items_on_page);
		$items_begin = intval($items_begin);

		if ($information_groups_parent_id === FALSE)
		{
			$information_groups_parent_id = 0;
		}

		$oInformationsystem_Groups = Core_Entity::factory('Informationsystem', $InformationSystem_id)
			->Informationsystem_Groups;

		$oInformationsystem_Groups->queryBuilder()
			->where('parent_id', '=', $information_groups_parent_id)
			->limit($items_begin, $items_on_page);

		$aInformationsystem_Groups = $oInformationsystem_Groups->findAll();

		foreach($aInformationsystem_Groups as $oInformationsystem_GroupSame)
		{
			$xmlData .= '<group id="' . $oInformationsystem_GroupSame->id . '" order="' . $oInformationsystem_GroupSame->sorting .'">' . "\n";
			$xmlData .= '<group_name>' . htmlspecialchars($oInformationsystem_GroupSame->name) . '</group_name>' . "\n";
			$xmlData .= '<group_description>' . htmlspecialchars($oInformationsystem_GroupSame->description) . '</group_description>' . "\n";
			$xmlData .= '<group_path>' . htmlspecialchars($oInformationsystem_GroupSame->path) . '</group_path>' . "\n";
			$xmlData .= '<group_url>' . str_for_xml($this->GetPathGroup($oInformationsystem_GroupSame->id, '')) . '</group_url>' . "\n";
			$xmlData .= '<group_access>' . $oInformationsystem_GroupSame->siteuser_group_id . '</group_access>' . "\n";

			if ($oInformationsystem_GroupSame->image_large != '')
			{
				$xmlData .= "<group_big_image>" . htmlspecialchars($oInformationsystem_GroupSame->image_large) . "</group_big_image>" . "\n";
				$xmlData .= "<group_small_image>" . htmlspecialchars($oInformationsystem_GroupSame->image_small) . "</group_small_image>"."\n";
			}

			$xmlData .= '</group>'."\n";
		}
		return $xmlData;
	}

	/**
	 * Формирование полного XML дерева информационных групп для информационной системы
	 *
	 * @param int $information_system_id идентификатор информационной системы
	 * @param array $property массив дополнительных параметров
	 * - $property['is_get_information_for_property'] параметр, исключающий рекурсию, возникающую при вызове для группы свойства типа "Информационная система" , имеющего также свойство типа "Информационная система" (эффект зазеркаливания), по умолчанию FALSE
	 * - $property['groups_parent_id'] идентификатор группы, начиная с которой необходимо строить дерево
	 * - $property['current_group_id'] идентификатор текущей группы, используется для вывода всех дополнительных
	 * - $property['xml_show_group_property'] параметр, определяющий необходимо ли добавлять в XML-данные о дополнительных свойствах информацию о группах дополнительных свойств для информационной группы
	 * - $property['groups_activity'] параметр, учитывающий активность групп при выборке. 1 - получаем информацию только об активных группах, если не задан, то активность группы не учитывается
	 * - $property['sql_from_select_groups'] дополнения для SQL-запроса выборки в секции FROM. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * - $property['xml_show_group_type'] параметр, определяющий тип генерации XML групп, может принимать значения (по умолчанию 'tree'):
	 *
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>all - все группы всех уровней;
	 * <li>current - группы только текущего уровня;
	 * <li>tree - будет выбрана текущая группа, все группы, находящиеся на одном уровне с ней, непосредственные потомки текущей группы, а также все группы, являющиеся предками для текущей
	 * <li>one_group - только текущая группа;
	 * <li>none - не выбирать группы.
	 * </ul>
	 * </li>
	 * </ul>
	 *
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_system_id = 1;
	 *
	 * $property['is_get_information_for_property'] = FALSE;
	 * $property['groups_parent_id'] = 0;
	 * $property['xml_show_group_property'] = 1;
	 *
	 * $xmlData = $InformationSystem->GenGroupXmlTree($information_system_id, $property);
	 *
	 * echo nl2br(htmlspecialchars($xmlData));
	 * ?>
	 * </code>
	 *
	 * @see FillMasGroup(), GetAllInformationGroups()
	 * @return string XML дерево групп информационной системы
	 */
	function GenGroupXmlTree($information_system_id, $property = array('is_get_information_for_property' => FALSE))
	{
		$information_system_id = intval($information_system_id);

		// Массив, содержащий свойства данной группы
		$mas_property_group = array();

		// идентификатор текущей группы, используется для вывода всех дополнительных свойств для информационной группы
		$property['current_group_id'] = Core_Type_Conversion::toInt($property['current_group_id']);

		if(!isset($property['xml_show_group_type']))
		{
			$property['xml_show_group_type'] = 'tree';
		}

		// Если не построено дерево групп для указанной инфосистемы
		if (!isset($property['groups_parent_id'])
		|| !isset($this->CacheGoupsIdTree[$information_system_id][$property['groups_parent_id']]))
		{
			// Т.к. выборка ведется с ограниченными условиями, укажем для сохранение результата переменную
			$this->FillMasGroup($information_system_id, $property);

			//$this->FillMemFullCacheGoupsIdTree($information_system_id);
		}

		// идентификатор группы, начиная с которой необходимо строить дерево
		// Указывается после $this->FillMasGroup, т.к. для построения нужны все группы
		// в соответствии с внешними условиями на выборку групп, а не только потомки текущей группы, отсечение ненужных групп делается ниже
		$property['groups_parent_id'] = Core_Type_Conversion::toInt($property['groups_parent_id']);

		if (!isset($property['site_user_id']))
		{
			if (class_exists('SiteUsers'))
			{
				$SiteUsers = & singleton('SiteUsers');
				$site_user_id = $SiteUsers->GetCurrentSiteUser();
			}
			else
			{
				$site_user_id = 0;
			}
		}
		else
		{
			$site_user_id = $property['site_user_id'];
		}

		$xmlData = '';

		// сохраняем, т.к. $property['groups_parent_id'] в цикле меняется
		$groups_parent_id = $property['groups_parent_id'];

		// При выводе дерева групп нам необходимо для текущего элемента узнать его родителя,
		// чтобы на нижнем уровне выбрать все группы
		if ($property['xml_show_group_type'] == 'tree'
		|| $property['xml_show_group_type'] == 'one_group')
		{
			$current_group_row = $this->GetInformationGroup($property['current_group_id']);
		}

		$uploaddir = CMS_FOLDER . UPLOADDIR;

		// Если у данной группы имеются потомки
		if (isset($this->CacheGoupsIdTree[$information_system_id][$groups_parent_id]))
		{
			// Чтобы сохранились условия выборки
			$aParamTmp = $property;

			// Переопределяем
			$aParamTmp['information_system_id'] = $information_system_id;
			$aParamTmp['groups_parent_id'] = $groups_parent_id;

			$aTmpGroups = $this->GetAllInformationGroups($aParamTmp);

			foreach ($aTmpGroups as $aTmpGroupsRow)
			{
				// Пишем в кэш для информационных групп
				$this->MasGroup[$aTmpGroupsRow['information_groups_id']] = $aTmpGroupsRow;
			}

			unset($aTmpGroups);

			foreach ($this->CacheGoupsIdTree[$information_system_id][$groups_parent_id] as $group_id)
			{
				$row = $this->GetInformationGroup($group_id);

				// Проверяем, является ли группа текущий выбранный узел родителем отображаемого узла
				$group_id_is_parent = $property['current_group_id'];
				$group_parent_id_is_parent = $group_id;
				/*
				 Условие "|| $row['information_groups_parent_id'] == $current_group_row['information_groups_parent_id']"
				 необходимо для выбора всех групп на уровне выводимой группы.

				 Условие "|| $property['current_group_id'] == $groups_parent_id" чтобы вывести всех потомков
				 текущей группы, при этом сравнивать нужно именно с $groups_parent_id,
				 т.к. $property['groups_parent_id'] в цикле меняется
				 */
				if ($row &&
				(($property['xml_show_group_type'] == 'tree'
				&& ($this->GroupIsParent($group_id_is_parent, $group_parent_id_is_parent)
				|| $property['current_group_id'] == $groups_parent_id
				|| $row['information_groups_parent_id'] == $current_group_row['information_groups_parent_id']
				)
				)
				|| ($property['xml_show_group_type'] == 'one_group'
				&& ($this->GroupIsParent($group_id_is_parent, $group_parent_id_is_parent)
				|| $group_id_is_parent == $group_parent_id_is_parent
				))
				|| $property['xml_show_group_type'] == 'current'
				|| $property['xml_show_group_type'] == 'all'
				/* Если был передан массив групп, подлежащих отображению*/
				|| (isset($property['xml_show_group_id'])
				&& is_array($property['xml_show_group_id'])
				&& in_array($group_id, $property['xml_show_group_id']))
				))
				{
					// Определяем доступна ли данная группа текущему зарегистрированному пользователю
					// Информационная группа доступна текущему зарегистрированному пользователю
					if ($this->IssetAccessForInformationSystemGroup($site_user_id,
					$row['information_groups_id'], $information_system_id, $row))
					{
						$property['groups_parent_id'] = $row['information_groups_id'];

						$xmlData .= '<group id="' . $row['information_groups_id'] . '" parent_id="' . $row['information_groups_parent_id'] . '">' . "\n";

						if (!isset($property['xml_show_count_items_and_group']))
						{
							if (!isset($current_group_row)
							|| $current_group_row == FALSE // Для корня
							|| $row['information_groups_parent_id'] == $current_group_row['information_groups_parent_id'])
							{
								$property['xml_show_count_items_and_group'] = true;
							}
							else
							{
								$property['xml_show_count_items_and_group'] = FALSE;
							}
						}

						$xmlData .= $this->GenXmlForGroup($information_system_id, $row, $property, $site_user_id);

						// Если тип построения дерева групп - от текущей и выше
						if ($property['xml_show_group_type'] == 'tree')
						{
							$group_id = $property['current_group_id'];
							$group_parent_id = $property['groups_parent_id'];

							// Проверяем, является ли группа потомком текущей группы
							// или она является этой же группой, чтобы выбрать прямых потомков
							if ($this->GroupIsParent($group_id, $group_parent_id) || $group_id == $group_parent_id)
							{
								$xmlData .= $this->GenGroupXmlTree($information_system_id, $property);
							}
						}
						// если выводится только текущая группа - подгруппы не выводим
						elseif ($property['xml_show_group_type'] != 'current')
						{
							$xmlData .= $this->GenGroupXmlTree($information_system_id, $property);
						}

						$xmlData .= '</group>' . "\n";
					}
				}
			} // end foreach
		} // end if

		return $xmlData;
	}

	/**
	 * Генерация XML для группы
	 *
	 * @param int $information_system_id идентификатор информационной системы
	 * @param array $row массив с информацией о группе
	 * @param array $property массив свойств, принимаемых GenGroupXmlTree()
	 * - $property['xml_show_external_property'] параметр, разрешающий передачу в XML информации о дополнительных свойствах пользователя, по умолчанию FALSE
	 * - $property['xml_show_count_items_and_group'] отображать количество элементов и групп
	 * @param int $site_user_id идентификатор пользователя сайта, если не указан, определяется автоматически
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $group_info = $InformationSystem->GetInformationGroup(3);
	 *
	 * $information_system_id = 1;
	 * $property = array();
	 *
	 * $xmlData = $InformationSystem->GenXmlForGroup($information_system_id, $group_info, $property);
	 *
	 * echo nl2br(htmlspecialchars($xmlData));
	 * ?>
	 * </code>
	 * @return string
	 * @see GenGroupXmlTree()
	 */
	function GenXmlForGroup($information_system_id, $row, $property, $site_user_id = FALSE)
	{
		if ($site_user_id === FALSE)
		{
			if (class_exists('SiteUsers'))
			{
				$SiteUsers = & singleton('SiteUsers');
				$site_user_id = $SiteUsers->GetCurrentSiteUser();
			}
			else
			{
				$site_user_id = 0;
			}
		}

		if (!isset($property['xml_show_group_property']))
		{
			$property['xml_show_group_property'] = TRUE;
		}

		if (!isset($property['xml_show_count_items_and_group']))
		{
			$property['xml_show_count_items_and_group'] = TRUE;
		}

		$xmlData = '';

		$site_users_id = Core_Type_Conversion::toInt($row['site_users_id']);

		$xmlData .= '<site_user>' . $site_users_id . '</site_user>' . "\n";

		// Добавляем информацию о пользователе сайта
		if ($site_users_id && class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');

			$param_site_user = array();

			if (isset($property['xml_show_external_property']))
			{
				$param_site_user['xml_show_external_property'] = $property['xml_show_external_property'];
			}
			else
			{
				$param_site_user['xml_show_external_property'] = FALSE;
			}

			$xmlData .= $SiteUsers->GetSiteUserXml($site_users_id, array(), array(), $param_site_user);
		}

		$xmlData .= '<name>' . str_for_xml($row['information_groups_name']) . '</name>' . "\n";

		/* Типографирование на лету */
		if (defined('ALLOW_TYPOGRAPH_INFORMATION_SYSTEM') && ALLOW_TYPOGRAPH_INFORMATION_SYSTEM)
		{
			$use_trailing_punctuation = defined('ALLOW_TRAILING_PUNCTUATION_INFORMATION_SYSTEM') && ALLOW_TRAILING_PUNCTUATION_INFORMATION_SYSTEM;

			if (Core::moduleIsActive('typograph'))
			{
				$row['information_groups_description'] = Typograph_Controller::instance()->process(
					$row['information_groups_description'],$use_trailing_punctuation
				);
			}
		}

		$xmlData .= '<description>' . str_for_xml($row['information_groups_description']) . '</description>' . "\n";
		$xmlData .= '<order>' . Core_Type_Conversion::toInt($row['information_groups_order']) . '</order>' . "\n";
		$xmlData .= '<path>' . str_for_xml(rawurlencode($row['information_groups_path'])) . '</path>' . "\n";
		$xmlData .= '<fullpath>' . str_for_xml($this->GetPathGroup($row['information_groups_id'], '')) . '</fullpath>' . "\n";
		$xmlData .= '<access>' . Core_Type_Conversion::toInt($row['information_groups_access']) . '</access>' . "\n";
		$xmlData .= '<activity>' . Core_Type_Conversion::toInt($row['information_groups_activity']) . '</activity>' . "\n";

		if ($property['xml_show_count_items_and_group'])
		{
			if (isset($param['select_groups']) && is_array($param['select_groups']))
			{
				// Определяем число всех элементов и всех групп в данной группе
				$mas_item_group_count = $this->GetCountItemsAndGroups($row['information_groups_id'], $row['information_systems_id'], TRUE, $site_user_id, $property);
			}
			else
			{
				$mas_item_group_count = & $row;
			}

			$xmlData .= '<count_items>' . $mas_item_group_count['count_items'] . '</count_items>' . "\n";
			$xmlData .= '<count_all_items>' . $mas_item_group_count['count_all_items'] . '</count_all_items>' . "\n";
			$xmlData .= '<count_groups>' . $mas_item_group_count['count_groups'] . '</count_groups>' . "\n";
			$xmlData .= '<count_all_groups>' . $mas_item_group_count['count_all_groups'] . '</count_all_groups>' . "\n";
		}

		$image = & singleton('Image');

		$information_group_dir = $this->GetInformationGroupDir($row['information_groups_id']);

		// Путь к папке информационной группы
		$uploaddir = CMS_FOLDER . $information_group_dir;

		if ($row['information_groups_image'] != '')
		{
			// Определяем существует ли файл большого изображения
			$file_path = $uploaddir . $row['information_groups_image'];

			if (is_file($file_path))
			{
				$image_size = $image->GetImageSize($file_path);
				$image_width = Core_Type_Conversion::toInt($image_size['width']);
				$image_height = Core_Type_Conversion::toInt($image_size['height']);
			}
			else
			{
				$image_width = 0;
				$image_height = 0;
			}

			$xmlData .= '<big_image  width="' . $image_width . '" height="' . $image_height . '">' . '/' . $information_group_dir . str_for_xml($row['information_groups_image']) . '</big_image>' . "\n";
		}

		if ($row['information_groups_small_image'] != '')
		{
			$file_path = $uploaddir . $row['information_groups_small_image'];

			if (is_file($file_path))
			{
				$image_size = $image->GetImageSize($file_path);

				$image_width = Core_Type_Conversion::toInt($image_size['width']);
				$image_height = Core_Type_Conversion::toInt($image_size['height']);

			}
			else
			{
				$image_width = 0;
				$image_height = 0;
			}

			$xmlData .= '<small_image width="' . $image_width . '" height="' . $image_height . '">' . '/' . $information_group_dir . str_for_xml($row['information_groups_small_image']) . '</small_image>' . "\n";
		}

		$xmlData .= '<allow_indexation>' . $row['information_groups_allow_indexation'] . '</allow_indexation>' . "\n";

		if (!empty($row['information_groups_seo_title']))
		{
			$xmlData .= '<seo_title>' . str_for_xml($row['information_groups_seo_title']) . '</seo_title>' . "\n";
		}

		if (!empty($row['information_groups_seo_description']))
		{
			$xmlData .= '<seo_description>' . str_for_xml($row['information_groups_seo_description']) . '</seo_description>' . "\n";
		}

		if (!empty($row['information_groups_seo_keywords']))
		{
			$xmlData .= '<seo_keywords>' . str_for_xml($row['information_groups_seo_keywords']) . '</seo_keywords>' . "\n";
		}

		if (class_exists('Sns'))
		{
			$Sns = & singleton('Sns');

			$xmlData .= $Sns->GenXml4SnsType($row['sns_type_id']);

			$xmlData .= $Sns->GenLightXml4AccessActions(array(
				'site_user_id' => $site_user_id,
				'information_group_id' => $row['information_groups_id'])
			);
		}

		if ($property['xml_show_group_property'])
		{
			$xmlData .= '<propertys>' . "\n";

			// Получаем св-ва группы
			$mas_property_group = $this->GetPropertiesGroup($row['information_groups_id'], $information_system_id);

			if (is_array($mas_property_group))
			{
				foreach ($mas_property_group as $value)
				{
					/* Проверяем, разрешено ли показывать данное свойство
					 или разрешаем, если массив с разрешенными не определен
					 или выводим все св-ва для текущей группы
					 */
					if (!isset($property['xml_show_group_property_id'])
					|| count($property['xml_show_group_property_id']) == 0
					|| in_array($value['information_propertys_groups_id'], $property['xml_show_group_property_id'])
					|| $property['current_group_id'] == $row['information_groups_id']
					|| !$property['current_group_id'] /* Для групп в корне выбираем св-ва*/)
					{
						if (trim($value['information_propertys_groups_name']) != '')
						{
							switch ($value['information_propertys_groups_type'])
							{
								case 2:// свойство файл
								{
									if ($value['information_propertys_groups_value_value']!='')
									{
										$xmlData .= '<property type="File" xml_name="' . $value['information_propertys_groups_xml_name'] . '" id="' . $value['information_propertys_groups_id'] . '" parent_id="' . $value['information_propertys_groups_dir_id'] . '">' . "\n";
										$xmlData .= '<name>' .str_for_xml($value['information_propertys_groups_name']) . '</name>' . "\n";
										$xmlData .= '<value>' . str_for_xml($value['information_propertys_groups_value_value']) . '</value>' . "\n";
										$xmlData .= '<default_value>' . str_for_xml($value['information_propertys_groups_default_value']) . '</default_value>' . "\n";
										$xmlData .= '<order>' . $value['information_propertys_groups_order'] .  '</order>' . "\n";

										// Учитываем CMS_FOLDER
										$file_path = $uploaddir . $value['information_propertys_groups_value_file'];

										if (is_file($file_path)
										&& @ filesize($file_path) > 12)
										{
											// если дополнительное свойство является изображением, тегу value
											// дописываем атрибуты width - ширина и height - высота
											if (Core_Image::instance()->exifImagetype($file_path))
											{

												$size_property_big_image = $image->GetImageSize($file_path);
												$atributs = ' width="' . $size_property_big_image['width'] . '"  height="' . $size_property_big_image['height'] . '"';
											}
											else
											{
												$atributs = '';
											}

											// Определяем размер файла в байтах
											$size = @filesize($file_path);

											$atributs .= ' size="' . $size . '"';

											$xmlData .= '<value>' . str_for_xml($value['information_propertys_groups_value_value']) . '</value>' . "\n";
											$xmlData .= '<property_file_path ' . trim($atributs) . '>' . '/' . $information_group_dir . str_for_xml($value['information_propertys_groups_value_file']). '</property_file_path>' . "\n";
										}

										$xmlData .= '<small_image>' . "\n";

										// Учитываем CMS_FOLDER
										$file_path = $uploaddir . $value['information_propertys_groups_value_file_small'];

										// проверяем существует ли файл маленькой картинки
										if (is_file($file_path)
										&& @ filesize($file_path) > 12)
										{
											// если дополнительное свойство является изображением, тегу value
											// дописываем атрибуты width - ширина и height - высота
											if (Core_Image::instance()->exifImagetype($file_path))
											{
												$size_property_big_image = $image->GetImageSize($file_path);
												$atributs = ' width="' . $size_property_big_image['width'] . '"  height="' . $size_property_big_image['height'] . '"';
											}
											else
											{
												$atributs = '';
											}

											// Определяем размер файла в байтах
											$size = @filesize($file_path);

											$atributs .= ' size="' . $size . '"';

											$xmlData .= '<value>' . str_for_xml($value['information_propertys_groups_value_value_small']) . '</value>' . "\n";
											$xmlData .= '<property_file_path ' . trim($atributs) . '>' . '/' .  $information_group_dir . str_for_xml($value['information_propertys_groups_value_file_small']) . '</property_file_path>' . "\n";
										}

										$xmlData .= '</small_image>' . "\n";
										$xmlData .= '</property>' . "\n";
									}
									break;
								}
								case 3:// св-во является списком
								{
									if ($value['information_propertys_groups_value_value'] != '')
									{
										// проверяем определена ли консанта и равна ли она true (модуль списков существует)
										if (class_exists('lists'))
										{
											// проверяем существование объекта типа Lists и наличие модуля Lists
											$lists = & singleton('lists');

											$xmlData .= '<property type="List" xml_name="'. $value['information_propertys_groups_xml_name'] . '" id="' . $value['information_propertys_groups_id'] . '" parent_id="'.$value['information_propertys_groups_dir_id'].'">' . "\n";
											$xmlData .= '<name>' . str_for_xml($value['information_propertys_groups_name']) . '</name>' . "\n";
											$xmlData .= '<default_value>' . str_for_xml($value['information_propertys_groups_default_value']) . '</default_value>' . "\n";
											$xmlData .= '<order>' . $value['information_propertys_groups_order'] . '</order>' . "\n";

											// Определяем значение списка

											$row3 = $lists->GetListItem($value['information_propertys_groups_value_value']);
											// Если существует значение списка
											if ($row3)
											{
												$xmlData .= '<value>' . str_for_xml($row3['lists_items_value']) . '</value>' . "\n";
												$xmlData .= '<description>' . str_for_xml($row3['lists_items_description']) . '</description>'."\n";
												$xmlData .= '<value_list_id>' . intval($row3['lists_items_id']) . '</value_list_id>' . "\n";
											}
											$xmlData .= '</property>' . "\n";

										}
									}
									break;
								}

								case 6: // св-во является инфосистемой
								{
									if (!empty($value['information_propertys_groups_value_value']))
									{
										// Получаем данные об информационном элементе,  являющимся значением дополнитедьного свойства
										$row3 = $this->GetInformationSystemItem($value['information_propertys_groups_value_value']);

										$xmlData .= '<property type="InformationSystemItem" xml_name="' . $value['information_propertys_groups_xml_name'] . '" id="' . $value['information_propertys_groups_id'] . '" parent_id="' . $value['information_propertys_groups_dir_id'].'">' . "\n";
										$xmlData .= '<name>' . str_for_xml($value['information_propertys_groups_name']) . '</name>' . "\n";
										$xmlData .= '<order>' . $value['information_propertys_groups_order'] . '</order>' . "\n";
										$xmlData .= '<information_system_id>' . $row3['information_systems_id'] . '</information_system_id>' . "\n";
										$xmlData .= '<value>' . str_for_xml($value['information_propertys_groups_value_value']) . '</value>' . "\n";

										if ($row3)
										{
											// Значение свойства типа информационный элемент получаем
											// только в том случае, если это само не получение для свойства
											if (!isset($property['is_get_information_for_property']))
											{
												//закомментирована передача параметра в соответствии с тикетом 58179
												//$property['is_get_information_for_property'] = true;
												$property['site_user_id'] = $site_user_id;
												$xmlData.= $this->GetXmlForInformationItem($row3['information_items_id'], $property);
											}
										}
										$xmlData .= '</property>' . "\n";
									}
									break;
								}

								// свойство - флажок
								case 7:
								{
									$xmlData .= '<property type="Checkbox" xml_name="' . $value['information_propertys_groups_xml_name'] . '" id="' . $value['information_propertys_groups_id'] . '" parent_id="' . $value['information_propertys_groups_dir_id'] . '">' . "\n";
									$xmlData .= '<name>' . str_for_xml($value['information_propertys_groups_name']) . '</name>' . "\n";
									$xmlData .= '<value>' . str_for_xml($value['information_propertys_groups_value_value']) . '</value>' . "\n";
									$xmlData .= '<default_value>' . str_for_xml($value['information_propertys_groups_default_value']) . '</default_value>' . "\n";
									$xmlData .= '<order>' . $value['information_propertys_groups_order'] . '</order>' ."\n";

									$xmlData .= '</property>' . "\n";
									break;
								}
								// свойство - Дата
								case 8:
								{
									$xmlData .= '<property type="Data" xml_name="' . $value['information_propertys_groups_xml_name'] . '" id="' . $value['information_propertys_groups_id'] . '" parent_id="' . $value['information_propertys_groups_dir_id'] . '">' . "\n";
									$xmlData .= '<name>' . str_for_xml($value['information_propertys_groups_name']) . '</name>' . "\n";
									$xmlData .= '<value>' . str_for_xml($value['information_propertys_groups_value_value']) . '</value>' . "\n";
									$xmlData .= '<default_value>' . str_for_xml($value['information_propertys_groups_default_value']) . '</default_value>' . "\n";
									$xmlData .= '<order>' . $value['information_propertys_groups_order'] . '</order>'."\n";
									$xmlData .= '</property>' . "\n";

									break;
								}
								// свойство - ДатаВремя
								case 9:
								{
									$xmlData .= '<property type="DataTime" xml_name="' . $value['information_propertys_groups_xml_name'] . '" id="' . $value['information_propertys_groups_id'] . '" parent_id="' . $value['information_propertys_groups_dir_id'] . '">' . "\n";
									$xmlData .= '<name>' . str_for_xml($value['information_propertys_groups_name']) . '</name>' . "\n";
									$xmlData .= '<value>' . str_for_xml($value['information_propertys_groups_value_value']) . '</value>' . "\n";
									$xmlData .= '<default_value>' . str_for_xml($value['information_propertys_groups_default_value']) . '</default_value>' . "\n";
									$xmlData .= '<order>' . $value['information_propertys_groups_order'] . '</order>' . "\n";
									$xmlData .= '</property>' . "\n";

									break;
								}
								default:
								{
									switch ($value['information_propertys_groups_type'])
									{
										case 0:// Число
											$property_type_name = 'Number';
										break;
										case 1: // Строка
											$property_type_name = 'String';
										break;
										case 4: // Большое текстовое поле
											$property_type_name = 'Textarea';
										break;
										case 5: // Визуальный редактор
											$property_type_name = 'WYSIWYG';
										break;
										default:
											$property_type_name = 'Any';
									}

									$xmlData .= '<property type="' . $property_type_name . '" xml_name="' . $value['information_propertys_groups_xml_name'] . '" id="' . $value['information_propertys_groups_id'] . '" parent_id="' . $value['information_propertys_groups_dir_id'] . '">' . "\n";
									$xmlData .= '<name>' . str_for_xml($value['information_propertys_groups_name']) . '</name>' . "\n";
									$xmlData .= '<value>' . str_for_xml($value['information_propertys_groups_value_value']) . '</value>' . "\n";
									$xmlData .= '<default_value>' . str_for_xml($value['information_propertys_groups_default_value']) . '</default_value>'."\n";
									$xmlData .= '<order>' . $value['information_propertys_groups_order'] . '</order>' . "\n";
									$xmlData .= '</property>' . "\n";
									break;
								}
							}
						}
					}
				} // foreach
			}

			$xmlData .= '</propertys>' . "\n";
		}

		return $xmlData;
	}

	/**
	 * Определение, является ли группа $group_id непосредственным потомком группы $group_parent_id
	 *
	 * @param int $group_id идентификатор группы-потомка
	 * @param int $group_parent_id идентификатор группы-родителя
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $group_id = 14;
	 * $group_parent_id = 2;
	 *
	 * $result = $InformationSystem->GroupIsParent($group_id, $group_parent_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Группа является потомком";
	 * }
	 * else
	 * {
	 * 	echo "Группа не является потомком";
	 * }
	 * ?>
	 * </code>
	 * @return bool
	 */
	function GroupIsParent($group_id, $group_parent_id)
	{
		$group_id = intval($group_id);
		$group_parent_id = intval($group_parent_id);

		while ($group_id)
		{
			$row = $this->GetInformationGroup($group_id);

			if ($row)
			{
				$group_id = $row['information_groups_parent_id'];
			}
			else
			{
				break;
			}

			if ($group_id == $group_parent_id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Формирование в памяти данных о свойствах групп информационной системы.
	 * Рекомендуется использовать совместно с GetPropertiesGroup() при выборе свойств всех групп информационной системы.
	 *
	 * @param int $information_system_id идентификатор информационной системы
	 * @param array $information_propertys_groups_id_array массив свойств, для которых осущестлвяется выборка,
	 * если не передан (по умолчанию является пустым массивом) - выбираются все свойства
	 * param
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_system_id = 1;
	 *
	 * $row = $InformationSystem->FillMemCachePropertysGroup($information_system_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 */
	function FillMemCachePropertysGroup($information_system_id, $information_propertys_groups_id_array = array())
	{
		$oInformationsystem_Group_Property_List = Core_Entity::factory('Informationsystem_Group_Property_List', $information_system_id);

		$aProperties = $oInformationsystem_Group_Property_List->Properties->findAll();

		foreach ($aProperties as $oProperty)
		{
			if (count($information_propertys_groups_id_array) == 0
				|| in_array($oProperty->id, $information_propertys_groups_id_array))
			{
				// Load all values for property
				$oProperty->loadAllValues();
			}
		}

		return 1;
	}

	/**
	 * @see GetPropertiesGroup()
	 * @access private
	 */
	function GetPropertysGroup($information_groups_id, $information_system_id)
	{
		return $this->GetPropertiesGroup($information_groups_id, $information_system_id);
	}

	/**
	 * Получение данных о дополнительных свойствах информационной группы и их значениях .
	 * Рекомендуется использоваться совместно с FillMemCachePropertysGroup() при выборе свойств всех групп информационной системы.
	 *
	 * @param int $information_groups_id идентификатор группы
	 * @param int $information_system_id идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_id = 2;
	 * $information_system_id = 1;
	 *
	 * $row = $InformationSystem->GetPropertiesGroup($information_groups_id, $information_system_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив с информацией о дополнительных свойствах
	 */
	function GetPropertiesGroup($information_groups_id, $information_system_id)
	{
		$information_groups_id = intval($information_groups_id);
		$information_system_id = intval($information_system_id);

		// если в mem-кэше есть данные о свойствах текущей группы
		if (isset($this->PropertyGroupMass[$information_system_id][$information_groups_id]))
		{
			return $this->PropertyGroupMass[$information_system_id][$information_groups_id];
		}
		// Иначе если были выбраны все свойства инфосистемы, тогда свойств у данной группе нет
		elseif (isset($this->PropertyGroupMass[$information_system_id]['fill_all']))
		{
			return array();
		}

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');

			$cache_element_name = $information_groups_id . "_" . $information_system_id;
			$cache_name = 'INF_SYS_GROUP_PROPERTIS';

			if ($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name))
			{
				$this->PropertyGroupMass[$information_system_id][$information_groups_id] = $in_cache['value'];

				return $in_cache['value'];
			}
		}

		$this->PropertyGroupMass[$information_system_id][$information_groups_id] = array();

		$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group')->find($information_groups_id);

		$aPropertyValues = $oInformationsystem_Group->getPropertyValues();

		foreach ($aPropertyValues as $oPropertyValue)
		{
			$this->PropertyGroupMass[$information_system_id][$information_groups_id][] =
				$this->getArrayGroupPropertyValue($oPropertyValue) + $this->getArrayGroupProperty($oPropertyValue->Property);
		}

		// Если добавлено кэширование
		if (class_exists('Cache'))
		{
			$cache->Insert($cache_element_name, $this->PropertyGroupMass[$information_system_id][$information_groups_id], $cache_name);
		}

		return $this->PropertyGroupMass[$information_system_id][$information_groups_id];
	}

	/**
	 * Устаревший метод. Оставлен для совместимости
	 *
	 * @param int $InformationSystem_id
	 * @param int $information_groups_id
	 * @param string $xsl_name
	 * @param int $items_on_page
	 * @param int $items_begin
	 * @param array $external_propertys
	 * @param int $property
	 * @return array двумерный массив с данными о показанных записях информационной системы. Если включен кэш, эти данные не возвращаются
	 * @access private
	 */
	function ShowInformationBlock($InformationSystem_id,$information_groups_id ,$xsl_name,
	$items_on_page, $items_begin,$external_propertys = array(),$property = array())
	{
		return $this->ShowInformationSystem($InformationSystem_id, $information_groups_id ,$xsl_name,
		$items_on_page, $items_begin, $external_propertys, $property);
	}

	/**
	 * Генерация XML для групп дополнительных свойств информационных элементов
	 *
	 * @param int $information_systems_id идентификатор информационной системы
	 * @param int $information_propertys_items_dir_parent_id идентификатор родительской директории дополнительных свойств информационных элементов
	 */
	function GenXmlForItemsPropertyDir($information_systems_id, $information_propertys_items_dir_parent_id = 0)
	{
		$information_systems_id = intval($information_systems_id);
		$information_propertys_items_dir_parent_id = intval($information_propertys_items_dir_parent_id);

		if (isset($this->cache_propertys_items_dir_tree[$information_systems_id][$information_propertys_items_dir_parent_id]) && $this->cache_propertys_items_dir_tree[$information_systems_id][$information_propertys_items_dir_parent_id] > 0)
		{
			$counter = 0;

			foreach ($this->cache_propertys_items_dir_tree[$information_systems_id][$information_propertys_items_dir_parent_id] as $information_propertys_items_dir_id)
			{
				// Получаем информацию о текущей группе дополнительных свойств товаров
				$infosys_properties_items_dir_row = $this->GetPropertysItemsDir($information_propertys_items_dir_id);

				// Генерация XML
				if ($infosys_properties_items_dir_row)
				{
					$this->buffer .= '<properties_items_dir id="' . $infosys_properties_items_dir_row['information_propertys_items_dir_id'] . '" parent_id="' . $infosys_properties_items_dir_row['information_propertys_items_dir_parent_id'] . '">' . "\n";

					$this->buffer .= '<information_systems_id>' . $infosys_properties_items_dir_row['information_systems_id'] . '</information_systems_id>' . "\n";

					$this->buffer .= '<information_propertys_items_dir_name>' . $infosys_properties_items_dir_row['information_propertys_items_dir_name'] . '</information_propertys_items_dir_name>' . "\n";

					$this->buffer .= '<information_propertys_items_dir_description>' . $infosys_properties_items_dir_row['information_propertys_items_dir_description'] . '</information_propertys_items_dir_description>' . "\n";

					$this->buffer .= '<information_propertys_items_dir_order>' . $infosys_properties_items_dir_row['information_propertys_items_dir_order'] . '</information_propertys_items_dir_order>' . "\n";

					$this->GenXmlForItemsPropertyDir($information_systems_id, $infosys_properties_items_dir_row['information_propertys_items_dir_id']);

					$this->buffer .= '</properties_items_dir>' . "\n";
				}
			}
		}
	}

	/**
	 * String to QueryBuilder
	 * @param string $str source string
	 * @param Core_QueryBuilder_Select $queryBuilder queryBuilder
	 * @return self
	 */
	public function parseQueryBuilder($str, $queryBuilder)
	{
		$aStr = explode(' ', $str);

		foreach ($aStr as $value)
		{
			$value = strtoupper(trim($value));
			switch ($value)
			{
				case 'AND':
				$queryBuilder->setAnd();
				break;
				case 'OR':
				$queryBuilder->setOr();
				break;
				case '(':
				$queryBuilder->open();
				break;
				case ')':
				$queryBuilder->close();
				break;
				default:
					if (is_numeric($value))
					{
						$value = intval($value);
						$queryBuilder->where(Core_QueryBuilder::expression($value), '=', $value);
					}
				break;
			}
		}

		return $this;
	}

	/**
	 * Отображение групп и элементов информационной системы
	 *
	 * @param mixed $InformationSystemIdArray массив идентификаторов или идентификатор информационной системы
	 * @param int $information_groups_id идентификатор информационной группы, подгруппы и элементы которой необходимо показать. Для выбора элеменов из всех групп указывается FALSE
	 * @param string $xsl_name имя XSL шаблона для отображения групп и элементов информационной системы
	 * @param int $items_on_page число информационных элементов, отображаемых на странице
	 * @param int $items_begin номер, начиная с которого выводить информационные элементы
	 * @param array $external_propertys массив дополнительных свойств для включения в XML
	 * @param array $property массив дополнительных параметров
	 * - $property['Order'] = ASC/DESC порядок сортировки информационных элементов
	 * - $property['OrderField'] поле сортировки информационных элементов, если случайная сортировка, то записать RAND(). При сортировке по средней оценке информационного элемента указывается поле 'information_items_comment_grade'
	 * - $property['SelectPropertyInQuery'] указывает на необходимость выборки дополнительных свойств элементов в SQL-запросе, по умолчанию true
	 * - $property['NotIn'] идентификаторы элементов, которые необходимо исключить из результатов
	 * - $property['OrderGroup'] направление сортировки группы (ASC - по возрастанию, DESC - по убыванию)
	 * - $property['OrderGroupField'] поле сортировки группы, если случайная сортировка, то записать RAND()
	 * - $property['NotInGroup'] строка с идентификаторами информационных групп (через запятую), которые (группы) необходимо исключить из результатов. Не влияет на выборку информационных элементов.
	 * - $property['cache'] разрешение кэширования, по умолчанию true
	 * - $property['cache_off'] запрещает кэшировани в память различных фрагментов информационных систем
	 * - $property['GenXml_type'] тип генерации XML для метода GenXml() при обработке $external_propertys
	 * - $property['sql_from'] дополнения для SQL-запроса выборки в секции FROM. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * - $property['sql_from_select_groups'] дополнения для SQL-запроса выборки в секции FROM. При использовании параметра не забывайте о необходимости их фильтрации для защиты от SQL-инъекций.
	 * - $property['show_item_type'] array массив типов информационных элементов, которые должны отображаться. Может содержать следующие элементы:
	 * <ul>
	 * <li>active - активные элементы (внесен по умолчанию, если $property['show_item_type'] не задан);
	 * <li>inactive - неактивные элементы;
	 * <li>putend_date - элементы, у которых значение поля putend_date меньше текущей даты;
	 * <li>putoff_date - элементы, у которых значение поля putoff_date превышает текущую дату;
	 * </ul>
	 * </li>
	 * </ul>
	 *
	 * - $property['groups_activity'] тип информационных групп, которые должны отображаться. 1 - активные группы, 2 - все группы (по умолчанию 1)
	 * - $property['xml_show_item_comment'] разрешает указание в XML комментариев информационного элемента, по умолчанию true
	 * - $property['xml_show_item_property'] разрешает указание в XML значений свойств информационного элемента, по умолчанию true
	 * - $property['xml_show_group_property'] разрешает указание в XML значений свойств информационной группы, по умолчанию true
	 * - $property['xml_show_group_property_id'] массив идентификаторов дополнительных свойств для отображения в XML. Если не передан - отображаются все св-ва для текущей группы и групп в корне.
	 * - $property['xml_show_group_id'] массив идентификаторов групп для отображения в XML. Если не не передано - выводятся все группы
	 * - $property['xml_show_group_type'] тип генерации XML групп, может принимать значения (по умолчанию 'tree'):
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>all - все группы всех уровней;
	 * <li>current - группы только текущего уровня;
	 * <li>tree - будет выбрана текущая группа, все группы, находящиеся на одном уровне с ней, непосредственные потомки текущей группы, а также все группы, являющиеся предками для текущей
	 * <li>one_group - только текущая группа;
	 * <li>none - не выбирать группы.
	 * </ul>
	 * </li>
	 * </ul>
	 * - $property['xml_show_tags'] разрешает генерацию в XML облака тегов информационной системы, по умолчанию FALSE
	 * - $property['xml_show_all_count_items_and_groups'] разрешает отображение в XML информации об обще количестве элементов
	 *
	 * - $property['show_text'] параметр, указывающий включать в XML текст информационного элемента или нет, по умолчанию равен true
	 * - $property['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	 * - $property['select_fields'] строка, содержащая дополнительные значения для области select запроса выбора элементов информационной системы
	 * - $property['select'] массив ($element) с дополнительными параметрами для задания дополнительных условий отбора информационных элементов
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>$element['type'] определяет, является ли поле основным свойством информационного элемента или дополнительным (0 - основное, 1 - дополнительное)
	 * <li>$element['prefix'] префикс - строка, размещаемая перед условием
	 * <li>$element['name'] имя поля для основного свойства, если свойство дополнительное, то не указывается
	 * <li>$element['property_id'] идентификатор дополнительногого свойства информационных элементов
	 * <li>$element['group_property_id'] идентификатор дополнительногого свойства информационных групп
	 * <li>$element['if'] строка, содержащая условный оператор
	 * <li>$element['value'] значение поля (или параметра)
	 * <li>$element['sufix'] суффикс - строка, размещаемая после условия
	 * </ul>
	 * </li>
	 * </ul>
	 *
	 * - $property['groups_on_page'] число информационных групп, отображаемых на странице
	 * - $property['groups_begin'] номер, начиная с которого выводить информационные группы
	 *
	 * - $property['show_group'] содержит массив групп системы для показа
	 * - $property['TagsOrder'] параметр, определяющий порядок сортировки тегов. Принимаемые значения: ASC - по возрастанию (по умолчанию), DESC - по убыванию
	 * - $property['TagsOrderField'] поле сортировки тегов, если случайная сортировка, то записать RAND(). по умолчанию теги сортируются по названию.
	 * - $property['tags'] массив идентификаторов тегов, по которым необходим фильтрация информационных элементов
	 *
	 * Пример использования:
	 * <code>
	 * <?php
	 * $InformationSystem = & singleton('InformationSystem');
	 * $external_propertys = array();
	 * $external_propertys['ПоказыватьСсылкиНаДругиеСтраницы'] = 1;
	 *
	 * $property = array();
	 * // Заполняем первое условие
	 * $element['type'] = 0;
	 * $element['prefix'] = ' and (';
	 * $element['name'] = 'information_items_date';
	 * $element['if'] = '>';
	 * $element['value'] = '2005-12-23 00:00:00';
	 * $element['sufix'] = '';
	 * $property['select'][] = $element;
	 *
	 * // Заполняем второе условие, в данном случае по значению дополнительного св-ва
	 * $element['type'] = 1;
	 * $element['prefix'] = 'and';
	 * $element['property_id'] = 17;
	 * $element['if'] = '>';
	 * $element['value'] = '2005-12-23 00:00:00';
	 * $element['sufix'] = ')';
	 * $property['select'][] = $element;
	 * // Выводим элементы
	 * $InformationSystem->ShowInformationSystem(1, 0, 'СписокНовостей', 10, 0, $external_propertys, $property);
	 * ?>
	 * </code>
	 *
	 * <b>Обратите внимание, при фильтрации по нескольким дополнительным свойствам они должны указываться
	 * через условие OR и должен быть добавлен параметр HAVING, в условие которого количество полей ДОПОЛНИТЕЛЬНЫХ свойств,
	 * по которым идет фильтрация (в примере дано значение 2):</b>
	 * <code>
	 * $count_condition = 2;
	 * $having_count= ' HAVING COUNT(information_propertys_items_table.information_propertys_items_id)= '.$count_condition;
	 * $element['sufix']=' ) GROUP BY information_propertys_items_table.information_items_id '.$having_count;
	 * </code>
	 *
	 * Пример сортировки по значению дополнительного поля:
	 * <code>
	 * <?php
	 * $InformationSystem = & singleton('InformationSystem');
	 * $external_propertys = array();
	 * $external_propertys['ПоказыватьСсылкиНаДругиеСтраницы'] = 1;
	 *
	 * $property=array();
	 *
	 * $property_id = 4154;
	 *
	 * $element['type'] = 0;
	 * $element['prefix'] = ' and ('; // префикс
	 * $element['name'] = 'information_propertys_table.information_propertys_id';
	 * $element['if'] = '='; // Условие
	 * // Здесь указывается ID доп. св-ва, по которому производится сортировка
	 * $element['value'] = $property_id; // ID дополнительного св-ва, по которому сортируем
	 * $element['sufix'] = '  OR information_propertys_table.information_propertys_id IS NULL )';
	 * $property['select'][] = $element;
	 *
	 * // Фиктивное условие для подключения таблицы доп. свойств
	 * $element['type'] = 1;
	 * $element['prefix'] = 'or';
	 * $element['property_id'] = 9999;
	 * $element['if'] = '!=';
	 * $element['value'] = '0';
	 * $element['sufix'] = '';
	 * $property['select'][] = $element;
	 *
	 * // Указываем, что мы сортируем по значениям дополнительных полей
	 * $property['OrderField'] = 'information_propertys_items_value';
	 *
	 * // Если сортировка по значению св-ва, а по остальным случайная,
	 * // то закомментируйте строку $property['OrderField'] и раскомментируйте строки:
	 * //$property['OrderField'] = 'information_propertys_items_value DESC, RAND()';
	 * //$property['Order'] = '';
	 *
	 * // Выводим элементы
	 * $InformationSystem->ShowInformationSystem(1, 0, 'СписокНовостей', 10, 0, $external_propertys, $property);
	 *
	 * ?>
	 * </code>
	 *
	 * Если Вы создали дополнительное свойство после заполнения информационной системы,
	 * то при сортировке по дополнительному свойству будут показаны ТОЛЬКО те элементы,
	 * которые имеют установленные значения этого дополнительного свойства. Для решения
	 * указанной проблемы необходимо выполнить следующий запрос
	 * <code>
	 * INSERT INTO information_propertys_items_table( information_propertys_id, information_items_id, information_propertys_items_value )
	 * SELECT 1111, informationsystem_items.information_items_id, 0
	 * FROM informationsystem_items
	 * LEFT JOIN information_propertys_items_table ON informationsystem_items.information_items_id = information_propertys_items_table.information_items_id
	 * AND information_propertys_id = 1111
	 * WHERE information_propertys_items_value IS NULL
	 * AND information_groups_id = 2222 AND information_systems_id = 3333
	 * </code>
	 *
	 * В этом примере:
	 * 1111 - идентификатор дополнительного свойства, по которому идет сортировка
	 * 0 -  значение по умолчанию, дополнительного свойства, присваиваемое тем
	 * элементам, для которых не задано значение данного дополнительного свойства
	 * 2222 - идентификатор группы, к которой принадлежат элементы, для которых
	 * необходимо задать значение по умолчанию.
	 * Если необходимо задать несколько групп, то вместо information_groups_id = 2222
	 * укажите "information_groups_id IN (идентификатор группы 1, идентификатор группы 2, ...)".
	 * Если необходимо задать значение дополнительного свойства для всех элементов информационной системы, то
	 * из запроса уберите "AND information_groups_id = 2222".
	 * 3333 - идентификатор информационной системы.
	 *
	 *
	 * Пример сортировки по значению дополнительного поля, имеющего строковый тип, по принципу сортировки целочисленных полей
	 * <code>
	 * <?php
	 * $InformationSystem = & singleton('InformationSystem');
	 *
	 * $external_propertys = array();
	 * $external_propertys['ПоказыватьСсылкиНаДругиеСтраницы'] = 1;
	 *
	 * $property=array();
	 *
	 * // Идентификатор доп. свойства
	 * $property_id = 144;
	 *
	 * $element['type'] = 0;
	 * $element['prefix'] = ' and ('; // префикс
	 * $element['name'] = 'information_propertys_table.information_propertys_id';
	 * $element['if'] = '='; // Условие
	 * // Здесь указывается ID доп. св-ва, по которому производится сортировка
	 * $element['value'] = $property_id; // ID дополнительного св-ва, по которому сортируем
	 * $element['sufix'] = '  OR information_propertys_table.information_propertys_id IS NULL )';
	 * $property['select'][] = $element;
	 *
	 * // Фиктивное условие для подключения таблицы доп. свойств
	 * $element['type'] = 1;
	 * $element['prefix'] = 'or';
	 * $element['property_id'] = 9999;
	 * $element['if'] = '!=';
	 * $element['value'] = '0';
	 * $element['sufix'] = '';
	 * $property['select'][] = $element;
	 *
	 * // Указываем, что мы сортируем по значениям дополнительных полей, приведенных к числовому типу
	 * $property['OrderField'] = ' convert( `information_propertys_items_value` , UNSIGNED ) ';
	 * // Выводим элементы
	 * $InformationSystem->ShowInformationSystem(1, 0, 'СписокНовостей', 10, 0, $external_propertys, $property);
	 * ?>
	 * </code>
	 *
	 * Пример фильтрации по значению дополнительного поля, имеющего строковый тип, как полей с плавающей точкой
	 * <code>
	 * <?php
	 * $InformationSystem = & singleton('InformationSystem');
	 * $external_propertys=array();
	 * $external_propertys['ПоказыватьСсылкиНаДругиеСтраницы'] = 1;
	 * $property=array();
	 *  $property_id = 144;
	 * // Заполняем первое условие
	 * $element['type'] = 1;
	 * $element['property_id'] = $property_id;
	 * $element['prefix'] = ' AND ';
	 * $element['if'] = '!=';
	 * // Указываем идентификатор информационной системы, для которой производится отбор элементов
	 * $element['value'] = '';
	 * // Указываем идентификатор дополнительного свойства, по значениям которого производиться отбор записей,
	 * // а также верхняя и нижняя границы допустимых значений дополнительного свойства
	 * $element['sufix'] = " AND information_propertys_table.information_propertys_id='7' and (REPLACE(information_propertys_items_table.information_propertys_items_value, ',', '.') + 0.0 ) >= 2.1 AND (REPLACE(information_propertys_items_table.information_propertys_items_value, ',', '.') + 0.0 ) <= 30.7 ";
	 * $property['select'][] = $element;
	 *
	 * // Указываем, что мы сортируем по значениям дополнительных полей, приведенных к числовому типу
	 * $property['OrderField'] = ' convert(`information_propertys_items_value` , UNSIGNED) ';
	 * // Выводим элементы
	 * $InformationSystem->ShowInformationSystem(1, 0, 'СписокНовостей', 10, 0, $external_propertys, $property);
	 * ?>
	 * </code>
	 * 	*
	 *
	 * Пример фильтрации по значению дополнительного поля, имеющего тип "Дата"
	 * <code>
	 * <?php
	 * $InformationSystem = & singleton('InformationSystem');
	 * $external_propertys=array();
	 * $external_propertys['ПоказыватьСсылкиНаДругиеСтраницы'] = 1;
	 * $property=array();
	 *  $property_id = 144;
	 * // Заполняем первое условие
	 * $element['type'] = 1;
	 * $element['property_id'] = $property_id;
	 * $element['prefix'] = ' AND ';
	 * $element['if'] = '!=';
	 * $element['value'] = '';
	 * $element['sufix'] = " AND CONVERT(CONCAT(SUBSTR(information_propertys_items_value, 7, 4), CHAR(45), SUBSTR(information_propertys_items_value, 4, 2), CHAR(45), SUBSTR(information_propertys_items_value, 1, 2)),  DATE) > CURDATE()";
	 * $property['select'][] = $element;
	 *
	 * // Выводим элементы
	 * $InformationSystem->ShowInformationSystem(1, 0, 'СписокНовостей', 10, 0, $external_propertys, $property);
	 * ?>
	 * </code>
	 *
	 * $property['select_groups'] массив ($element) с дополнительными параметрами для задания дополнительных условий отбора информационных групп
	 * <ul>
	 * <li>
	 * <ul>
	 * <li>$element['type'] определяет, является ли поле основным свойством информационной группы или дополнительным (0 - основное, 1 - дополнительное)
	 * <li>$element['prefix'] префикс - строка, размещаемая перед условием
	 * <li>$element['name'] имя поля для основного свойства, если свойство дополнительное, то не указывается
	 * <li>$element['property_id'] идентификатор дополнительногого свойства информационных групп
	 * <li>$element['if'] строка, содержащая условный оператор
	 * <li>$element['value'] значение поля (или параметра)
	 * <li>$element['sufix'] суффикс - строка, размещаемая после условия
	 * </ul>
	 * </li>
	 * </ul>
	 *
	 * Пример использования:
	 * <code>
	 * <?php
	 * $InformationSystem = & singleton('InformationSystem');
	 *
	 * $property = array();
	 * // Заполняем первое условие
	 * $element['type'] = 0;
	 * $element['prefix'] = ' and ';
	 * $element['name'] = 'information_groups_order';
	 * $element['if'] = '>';
	 * $element['value'] = '10';
	 * $element['sufix'] = '';
	 * $property['select_groups'][] = $element;
	 * // Заполняем второе условие, в данном случае по значению дополнительного св-ва
	 * $element['type'] = 1;
	 * $element['prefix'] = 'and';
	 * $element['property_id'] = 7;
	 * $element['if'] = '=';
	 * $element['value'] = '10';
	 * $element['sufix'] = '';
	 * $property['select_groups'][] = $element;
	 * // Выводим элементы
	 * $InformationSystem->ShowInformationSystem(1, 0, 'СписокЭлементовИнфосистемы', 10, 0, array(), $property);
	 * ?>
	 * </code>
	 *
	 * @return array двумерный массив с идентификаторами показанных информационных элементов.
	 */
	function ShowInformationSystem($InformationSystemIdArray, $information_groups_id, $xsl_name,
	$items_on_page, $items_begin, $external_propertys = array(), $property = array())
	{
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		// По умолчанию кэширование - включено
		if (!isset($property['cache']))
		{
			$property['cache'] = TRUE;
		}

		if (is_array($InformationSystemIdArray))
		{
			$InformationSystemIdArray = Core_Type_Conversion::toArray($InformationSystemIdArray);
		}
		else
		{
			$InformationSystemId = $InformationSystemIdArray;
			$InformationSystemIdArray = array();
			$InformationSystemIdArray[] = Core_Type_Conversion::toInt($InformationSystemId);
			unset($InformationSystemId);
		}

		if (!isset($property['xml_show_group_property']))
		{
			$property['xml_show_group_property'] = TRUE;
		}

		if (!isset($property['xml_show_group_property_id']))
		{
			$property['xml_show_group_property_id'] = array();
		}

		if (!isset($property['xml_show_all_count_items_and_groups']))
		{
			$property['xml_show_all_count_items_and_groups'] = TRUE;
		}

		// По умолчанию выбираем данные по группам в виде дерева до текущей
		if (!isset($property['xml_show_group_type']))
		{
			$property['xml_show_group_type'] = 'tree';
		}

		// По умолчанию показываем только активные элементы
		if (!isset($property['show_item_type']))
		{
			$property['show_item_type'] = array('active');
		}

		// По умолчанию выбираем свойства в запросе (нужны для фильтрации по внешним параметрам)
		if (!isset($property['SelectPropertyInQuery']))
		{
			$property['SelectPropertyInQuery'] = TRUE;
		}

		if (!isset ($property['xml_show_tags']))
		{
			$property['xml_show_tags'] = FALSE;
		}

		// Цикл по всем показываемым инфосистемам
		foreach ($InformationSystemIdArray as $key => $InformationSystem_id)
		{
			$kernel = & singleton('kernel');

			// Показ плавающей панели
			if ($kernel->AllowShowPanel())
			{
				$param_panel = array();

				// Добавить инфоэлемент
				$param_panel[0]['image_path'] = "/hostcmsfiles/images/page_add.gif";

				$sPath = '/admin/informationsystem/item/index.php';
				$sAdditional = "hostcms[action]=edit&informationsystem_id={$InformationSystem_id}&informationsystem_group_id={$information_groups_id}&hostcms[checked][1][0]=1";

				$param_panel[0]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
				$param_panel[0]['href'] = "{$sPath}?{$sAdditional}";
				$param_panel[0]['alt'] = "Добавить информационный элемент";

				// Добавить Группу
				$param_panel[1]['image_path'] = "/hostcmsfiles/images/folder_add.gif";

				$sPath = '/admin/informationsystem/item/index.php';
				$sAdditional = "hostcms[action]=edit&informationsystem_id={$InformationSystem_id}&informationsystem_group_id={$information_groups_id}&hostcms[checked][0][0]=1";

				$param_panel[1]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
				$param_panel[1]['href'] = "{$sPath}?{$sAdditional}";
				$param_panel[1]['alt'] = "Добавить информационную группу";

				if ($information_groups_id)
				{
					// Редактировать текущую Группу
					if ($information_groups_row = $this->GetInformationGroup($information_groups_id))
					{
						$param_panel[2]['image_path'] = "/hostcmsfiles/images/folder_edit.gif";

						$sPath = '/admin/informationsystem/item/index.php';
						$sAdditional = "hostcms[action]=edit&informationsystem_id={$InformationSystem_id}&informationsystem_group_id={$information_groups_row['information_groups_parent_id']}&hostcms[checked][0][{$information_groups_id}]=1";

						$param_panel[2]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
						$param_panel[2]['href'] = "{$sPath}?{$sAdditional}";
						$param_panel[2]['alt'] = "Редактировать информационную группу";
					}
				}

				// Редактирование ИС
				$param_panel[3]['image_path'] = "/hostcmsfiles/images/folder_page_edit.gif";

				$oInformationsystem = Core_Entity::factory('InformationSystem', $InformationSystem_id);

				$sPath = '/admin/informationsystem/index.php';
				$sAdditional = "hostcms[action]=edit&informationsystem_dir_id={$oInformationsystem->informationsystem_dir_id}&hostcms[checked][1][{$InformationSystem_id}]=1";

				$param_panel[3]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
				$param_panel[3]['href'] = "{$sPath}?{$sAdditional}";
				$param_panel[3]['alt'] = "Редактировать инфосистему";

				echo $kernel->ShowFlyPanel($param_panel);
			}
		}

		if (class_exists('Cache') && $property['cache'])
		{
			$cache = & singleton('Cache');
			$kernel = & singleton('kernel');

			$cache_element_name = 'InformationSystems_' . implode(",", $InformationSystemIdArray) . '_' . $information_groups_id . '_' . $xsl_name . '_' . $items_on_page . '_' . $items_begin . '_' . $kernel->implode_array($external_propertys, '_') . '_' . $kernel->implode_array($property,'_') . '_' . $site_user_id;

			$cache_name = 'SHOW_INF_SYS_XML';

			if (($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name)) && $in_cache)
			{
				echo $in_cache['value']['result'];

				return $in_cache['value']['return_mass'];
			}
		}

		if ($items_on_page !== FALSE)
		{
			if ($items_on_page === TRUE && count($InformationSystemIdArray) == 1)
			{
				$InformationSystem_id = $InformationSystemIdArray[0];
				$row_inf_sys = $this->GetInformationSystem($InformationSystem_id);
				$items_on_page = $row_inf_sys['information_systems_items_on_page'];
			}
			else
			{
				$items_on_page = intval($items_on_page);
			}
		}

		if ($items_begin !== FALSE)
		{
			$items_begin = intval($items_begin);
		}

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xmlData .= '<document>' . "\n";

		// Вносим внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
		if (isset($property['external_xml']))
		{
			$xmlData .= $property['external_xml'];
		}

		if (isset($property['GenXml_type']))
		{
			$property['GenXml_type'] = intval($property['GenXml_type']);
		}
		else
		{
			$property['GenXml_type'] = 0;
		}

		if (isset($property['groups_activity']))
		{
			$property['groups_activity'] = intval($property['groups_activity']);
		}
		else
		{
			// Выводим только активные группы
			$property['groups_activity'] = 1;
		}

		// Добавляем информацию о пользователе
		$xmlData .= '<site_user_id>' . $site_user_id . '</site_user_id>' . "\n";

		// Проверяем доступна ли данная инфосистема текущему зарегистрированному пользователю

		// Вносим в XML дополнительные теги из массива дополнительных параметров
		$ExternalXml = new ExternalXml;
		$xmlData .= $ExternalXml->GenXml($external_propertys, $property['GenXml_type']);
		unset($ExternalXml);

		// Массив items-ов для возвращения
		$return_mass = array();

		foreach ($InformationSystemIdArray as $key => $InformationSystem_id)
		{
			// Инфосистема доступна текущему зарегистрированному пользователю
			if ($this->IssetAccessForInformationSystemGroup($site_user_id, 0, $InformationSystem_id))
			{
				// Получаем данные об информационной системе
				$row = $this->GetInformationSystem($InformationSystem_id);

				if ($row !== FALSE)
				{
					$xmlData .= '<blocks id="' . $row['information_systems_id'] . '">' . "\n";

					$xmlData .= $this->GenXml4InformationSystem($row['information_systems_id'], $row);

					$xmlData .= '<parent_group_id>' . intval($information_groups_id) . '</parent_group_id>' . "\n";

					if ($property['xml_show_all_count_items_and_groups'])
					{
						if (isset($param['select_groups']) && is_array($param['select_groups']))
						{
							// Определяем число всех элементов и всех групп в инфосистеме
							$mas_item_group_count = $this->GetCountItemsAndGroups(0, $row['information_systems_id'], TRUE, $site_user_id, $property);
						}
						else
						{
							$aInformationsystem_Items = Core_Entity::factory('InformationSystem', $row['information_systems_id'])
								->InformationSystem_Items->getByGroupId(0);

							$aInformationsystem_Groups = Core_Entity::factory('InformationSystem', $row['information_systems_id'])
								->InformationSystem_Groups->getByParentId(0);

							$mas_item_group_count = array(
								'count_items' => count($aInformationsystem_Items),
								'count_all_items' => count($aInformationsystem_Items),
								'count_groups' => count($aInformationsystem_Groups),
								'count_all_groups' => count($aInformationsystem_Groups)
							);

							foreach ($aInformationsystem_Groups as $oInformationsystem_Group)
							{
								$mas_item_group_count['count_all_items'] += $oInformationsystem_Group->items_total_count;
								$mas_item_group_count['count_all_groups'] += $oInformationsystem_Group->subgroups_total_count;
							}
						}

						$xmlData .= '<count_items>' . $mas_item_group_count['count_items'] . '</count_items>' . "\n";
						$xmlData .= '<count_all_items>' . $mas_item_group_count['count_all_items'] . '</count_all_items>' . "\n";
						$xmlData .= '<count_groups>' . $mas_item_group_count['count_groups'] . '</count_groups>' . "\n";
						$xmlData .= '<count_all_groups>' . $mas_item_group_count['count_all_groups'] . '</count_all_groups>' . "\n";
					}

					$xmlData .= '<count_items_on_page>' . $row['information_systems_items_on_page'] . '</count_items_on_page>' . "\n";

					if ($information_groups_id !== FALSE)
					{
						$information_groups_id = intval($information_groups_id);
					}

					// Проверка активности текущей группы
					if ($information_groups_id)
					{
						$information_groups_row = $this->GetInformationGroup($information_groups_id);

						$have_acces_group = $information_groups_row
						&& ($information_groups_row['information_groups_activity'] == 1
						|| $information_groups_row['information_groups_activity'] == 0 && $property['groups_activity'] == 2);
					}
					else
					{
						$have_acces_group = TRUE;
					}

					// Текущий зарегистрированный пользователь имеет доступ к данной информационной группе
					if ($have_acces_group
					&& $this->IssetAccessForInformationSystemGroup($site_user_id, intval($information_groups_id), $InformationSystem_id))
					{
						if (class_exists('Sns'))
						{
							$Sns = & singleton('Sns');

							// Получаем информацию о блогах, связанных с информационной системой
							$sns_row = $Sns->GetSnsAssociatedInformationSystem($row['information_systems_id']);

							if ($sns_row)
							{
								$param_sns_xml['sns_id'] = $sns_row['sns_id'];
								$param_sns_xml['sns_row'] = $sns_row;

								$param_sns_xml['show_information_system_xml'] = FALSE;

								$xmlData .= $Sns->GenXml4Sns($param_sns_xml);

								// XML с краткой информацией о доступных действиях
								$xmlData .= $Sns->GenLightXml4AccessActions(array(
									'sns_id' => $sns_row['sns_id'],
									'site_user_id' => $site_user_id,
									'information_group_id' => 0));
							}
						}

						if ($property['xml_show_group_type'] == 'current')
						{
							// Если в XML передаются только текущие группы - дерево групп строим от текущей группы, а не от корня.
							$property['groups_parent_id'] = $information_groups_id;
						}

						// Используется для вывода всех свойств для текущей группы
						$property['current_group_id'] = $information_groups_id;

						if ($property['xml_show_group_type'] != 'none')
						{
							if (!isset($this->cache_propertys_groups_dir_tree[$InformationSystem_id]))
							{
								// Формируем XML-данные для групп дополнительных свойств групп инфоэлементов
								$dir_prop_array = $this->GetAllPropertyGroupsDirForInformationSystem($InformationSystem_id);

								$this->cache_propertys_groups_dir_tree[$InformationSystem_id] = array();

								if ($dir_prop_array && mysql_num_rows($dir_prop_array) > 0)
								{
									while ($dir_prop_row = mysql_fetch_assoc($dir_prop_array))
									{
										$this->cache_propertys_groups_dir[$dir_prop_row['information_propertys_groups_dir_id']] = $dir_prop_row;
										$this->cache_propertys_groups_dir_tree[$InformationSystem_id][$dir_prop_row['information_propertys_groups_dir_parent_id']][] = $dir_prop_row['information_propertys_groups_dir_id'];
									}
								}

								$this->buffer = '';
								// Вызов функции генерацци XML для групп дополнительных свойств
								$this->GenXmlForGroupsPropertyDir($InformationSystem_id);
								$xmlData .= $this->buffer;
								$this->buffer = '';
							}

							if (!isset($this->cache_propertys_items_dir_tree[$InformationSystem_id]))
							{
								// Формируем XML-данные для групп дополнительных свойств инфоэлементов
								$dir_prop_array = $this->GetAllPropertysItemsDirForInformationSystem($InformationSystem_id);

								$this->cache_propertys_items_dir_tree[$InformationSystem_id] = array();

								if ($dir_prop_array && mysql_num_rows($dir_prop_array) > 0)
								{
									while ($dir_prop_row = mysql_fetch_assoc($dir_prop_array))
									{
										$this->cache_propertys_items_dir[$dir_prop_row['information_propertys_items_dir_id']] = $dir_prop_row;
										$this->cache_propertys_items_dir_tree[$InformationSystem_id][$dir_prop_row['information_propertys_items_dir_parent_id']][] = $dir_prop_row['information_propertys_items_dir_id'];
									}
								}

								$this->buffer = '';
								// Вызов функции генераци XML для групп дополнительных свойств
								$this->GenXmlForItemsPropertyDir($InformationSystem_id);
								$xmlData .= $this->buffer;
								$this->buffer = '';
							}

							if (class_exists('Cache') && $property['cache'])
							{
								$kernel = & singleton('kernel');
								$cache = & singleton('Cache');

								$cache_element_name_xml = $InformationSystem_id . "_" . $kernel->implode_array($property, '_');

								$cache_name_xml = 'INF_SYS_GEN_GROUP_XML_TREE';

								if (($in_cache = $cache->GetCacheContent($cache_element_name_xml, $cache_name_xml)) && $in_cache)
								{
									$GroupXmlTree = $in_cache['value'];
								}
							}

							// В кэше данных не было
							if (!isset($GroupXmlTree))
							{
								// Если стоит передавать свойства в информационную группу
								if ($property['xml_show_group_property'])
								{
									// Заполняем значения свойств всех групп информационной системы
									$this->FillMemCachePropertysGroup($InformationSystem_id, $property['xml_show_group_property_id']);
								}

								if ($information_groups_id !== FALSE)
								{
									$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group', $information_groups_id);

									$aGroupsParentsId = array(0);

									do {
										$aGroupsParentsId[] = $oInformationsystem_Group->id;
									}
									while($oInformationsystem_Group = $oInformationsystem_Group->getParent());

									foreach ($aGroupsParentsId as $iGroupParentId)
									{
										Core_Entity::factory('Informationsystem', $InformationSystem_id)
											->Informationsystem_Groups->getByParentId($iGroupParentId);
									}
								}

								// Дерево всех групп информационной системы
								$GroupXmlTree = $this->GenGroupXmlTree($InformationSystem_id, $property);

								if (class_exists('Cache') && $property['cache'])
								{
									$cache->Insert($cache_element_name_xml, $GroupXmlTree, $cache_name_xml);
								}
							}

							$xmlData .= $GroupXmlTree;

							unset($GroupXmlTree);
						}

						if (class_exists('Tag') && $property['xml_show_tags'])
						{
							// Облако тегов
							$xmlData .= $this->GetXml4Tags($InformationSystem_id, $property);
						}

						$queryBuilder = Core_QueryBuilder::select('informationsystem_items.id')
							->select(
								array('informationsystem_items.id','information_items_id'),
								array('informationsystem_items.informationsystem_id', 'information_systems_id'),
								array('informationsystem_items.informationsystem_group_id', 'information_groups_id'),
								array('informationsystem_items.shortcut_id', 'information_items_shortcut_id'),
								array('informationsystem_items.datetime', 'information_items_date'),
								array('informationsystem_items.start_datetime', 'information_items_putoff_date'),
								array('informationsystem_items.end_datetime', 'information_items_putend_date'),
								array('informationsystem_items.name', 'information_items_name'),
								array('informationsystem_items.description', 'information_items_description'),
								array('informationsystem_items.active', 'information_items_status'),
								array('informationsystem_items.text', 'information_items_text'),
								array('informationsystem_items.image_large', 'information_items_image'),
								array('informationsystem_items.image_small', 'information_items_small_image'),
								array('informationsystem_items.image_large_width', 'information_items_image_width'),
								array('informationsystem_items.image_large_height', 'information_items_image_height'),
								array('informationsystem_items.image_small_width', 'information_items_small_image_width'),
								array('informationsystem_items.image_small_height', 'information_items_small_image_height'),
								array('informationsystem_items.sorting', 'information_items_order'),
								array('informationsystem_items.ip', 'information_items_ip'),
								array('informationsystem_items.path', 'information_items_url'),
								array('informationsystem_items.indexing', 'information_items_allow_indexation'),
								array('informationsystem_items.seo_title', 'information_items_seo_title'),
								array('informationsystem_items.seo_description', 'information_items_seo_description'),
								array('informationsystem_items.seo_keywords', 'information_items_seo_keywords'),
								array('informationsystem_items.siteuser_group_id', 'information_items_access'),
								array('informationsystem_items.showed', 'information_items_show_count'),
								array('informationsystem_items.user_id', 'users_id'),
								array('informationsystem_items.siteuser_id', 'site_users_id')
							)
							->where('informationsystem_items.deleted', '=', 0)
							->sqlCalcFoundRows();

						// Выбираем элементы для информационной группы
						if ($information_groups_id !== FALSE)
						{
							$queryBuilder
								->open()
								->where('informationsystem_items.informationsystem_group_id', '=', $information_groups_id);

							// Выбираем элементы для информационной группы и всех ее подгрупп
							if (isset($property['show_group']) && is_array($property['show_group']) && count($property['show_group']) > 0)
							{
								$property['show_group'] = Core_Array::toInt($property['show_group']);

								$queryBuilder
									->setOr()
									->where('informationsystem_items.informationsystem_group_id', 'IN', $property['show_group']);
							}

							$queryBuilder->close();
						}

						/*
						 $element['type'] = 0; // 0 - основное св-во, 1 - дополнительное
						 $element['prefix'] = ''; // префикс
						 $element['name'] = 'information_date'; // Имя поля для основного св-ва, если тип = 1, то не указывается
						 $element['if'] = '>'; // Условие
						 $element['value'] = '2005-12-23 00:00:00'; // Значение поля (или параметра)
						 $element['sufix'] = '';

						 $property['select'][] = $element;

						 $element['type'] = 1; // 0 - основное св-во, 1 - дополнительное
						 $element['prefix'] = 'and'; // префикс
						 $element['property_id'] = 17; // ID дополнительного св-ва, указывается если тип = 1
						 $element['if'] = '>'; // Условие
						 $element['value'] = '2005-12-23 00:00:00'; // Значение поля (или параметра)
						 $element['sufix'] = '';

						 $property['select'][] = $element;
						 */

						$query_property = '';

						// формируем дополнительные условия для выборки
						if (is_array($property) && isset($property['select']))
						{
							foreach ($property['select'] as $key => $value)
							{
								if ($value['type'] == 0) // основное свойство
								{
									$this->parseQueryBuilder($value['prefix'], $queryBuilder);

									$value['if'] = trim($value['if']);
									if (strtoupper($value['if']) == 'IN')
									{
										$value['value'] = explode(',', $value['value']);
									}
									else
									{
										$value['value'] = Core_Type_Conversion::toStr($value['value']);
									}

									$value['name'] != '' && $value['if'] != ''
										&& $queryBuilder->where($value['name'], $value['if'], $value['value']);

									$this->parseQueryBuilder($value['sufix'], $queryBuilder);
								}
								else // дополнительное свойство
								{
									// Ограничение для дополнительного свойства информационных элементов
									if (Core_Type_Conversion::toInt($value['property_id']) != 0)
									{
										$isset_items_property = TRUE;

										$this->parseQueryBuilder($value['prefix'], $queryBuilder);

										$queryBuilder->where('informationsystem_item_properties.property_id', '=', $value['property_id']);

										$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);

										$queryBuilder->where(
											$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $value['value']
										);

										$this->parseQueryBuilder($value['sufix'], $queryBuilder);

										/*
										$query_property .= ' ' . $value['prefix'] . ' information_propertys_table.information_propertys_id=' . "'" . $value['property_id'] . "'" . ' and information_propertys_items_table.information_propertys_items_value ' . $value['if'] . " '" . quote_smart($value['value']) . "' " . $value['sufix'] . ' ';
										*/
									}
									// Ограничение для дополнительного свойства информационных групп
									elseif (Core_Type_Conversion::toInt($value['group_property_id']) != 0)
									{
										$isset_groups_property = TRUE;

										$this->parseQueryBuilder($value['prefix'], $queryBuilder);

										$queryBuilder->where('informationsystem_group_properties.property_id', '=', $value['property_id']);

										$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);

										$queryBuilder->where(
											$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $value['value']
										);

										$this->parseQueryBuilder($value['sufix'], $queryBuilder);

										/*
										$query_property .= ' ' . $value['prefix'] . ' information_propertys_groups_table.information_propertys_groups_id=' . "'" . $value['group_property_id'] . "'" . ' and information_propertys_groups_value_table.information_propertys_groups_value_value ' . $value['if'] . " '" . quote_smart($value['value']) . "' " . $value['sufix'] . ' ';
										*/
									}
								}
							}

							if (isset($isset_items_property) || isset($isset_groups_property))
							{
								if (isset($isset_items_property))
								{
									$queryBuilder
										->leftJoin('informationsystem_item_properties', 'informationsystem_items.informationsystem_id', '=', 'informationsystem_item_properties.informationsystem_id')
										->leftJoin('property_value_ints', 'informationsystem_items.id', '=', 'property_value_ints.entity_id',
											array(
												array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_ints.property_id')))
											)
										)
										->leftJoin('property_value_strings', 'informationsystem_items.id', '=', 'property_value_strings.entity_id',
											array(
												array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_strings.property_id')))
											)
										)
										->leftJoin('property_value_texts', 'informationsystem_items.id', '=', 'property_value_texts.entity_id',
											array(
												array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_texts.property_id')))
											)
										)
										->leftJoin('property_value_datetimes', 'informationsystem_items.id', '=', 'property_value_datetimes.entity_id',
											array(
												array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_datetimes.property_id')))
											)
										)
										->leftJoin('property_value_files', 'informationsystem_items.id', '=', 'property_value_files.entity_id',
											array(
												array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_files.property_id')))
											)
										);
								}
								else
								{
									$queryBuilder
										->leftJoin('informationsystem_group_properties', 'informationsystem_groups.informationsystem_id', '=', 'informationsystem_group_properties.informationsystem_id')
										->leftJoin('property_value_ints', 'informationsystem_groups.id', '=', 'property_value_ints.entity_id',
											array(
												array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_ints.property_id')))
											)
										)
										->leftJoin('property_value_strings', 'informationsystem_groups.id', '=', 'property_value_strings.entity_id',
											array(
												array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_strings.property_id')))
											)
										)
										->leftJoin('property_value_texts', 'informationsystem_groups.id', '=', 'property_value_texts.entity_id',
											array(
												array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_texts.property_id')))
											)
										)
										->leftJoin('property_value_datetimes', 'informationsystem_groups.id', '=', 'property_value_datetimes.entity_id',
											array(
												array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_datetimes.property_id')))
											)
										)
										->leftJoin('property_value_files', 'informationsystem_groups.id', '=', 'property_value_files.entity_id',
											array(
												array('AND' => array('informationsystem_group_properties.property_id', '=', Core_QueryBuilder::expression('property_value_files.property_id')))
											)
										);
								}
							}
						}

						$not_in = '';

						// Определяем ID элементов, которые не надо включать в выдачу
						if (isset($property['NotIn']))
						{
							$not_in_mass = Core_Array::toInt(explode(',', $property['NotIn']));
							$queryBuilder->where('informationsystem_items.id', 'NOT IN', $not_in_mass);
						}

						// Определяем направление сортировки
						$order_type = '';

						// Если явно передано направление сортировки
						if (isset($property['Order']))
						{
							$order_type = $property['Order'];
						}
						else
						{
							switch ($row['information_systems_items_order_type'])
							{
								case 1:
									$order_type = 'DESC';
									break;
								case 0:
								default:
									$order_type = 'ASC';
							}
						}

						// Поле сортировки явно не передано
						if (!isset($property['OrderField']))
						{
							// Определяем поле сортировки информационных элементов
							switch ($row['information_systems_items_order_field'])
							{
								case 1:
									$queryBuilder->orderBy('informationsystem_items.name', $order_type);
									break;
								case 2:
									$queryBuilder->orderBy('informationsystem_items.sorting', $order_type);
									$queryBuilder->orderBy('informationsystem_items.name', $order_type);
									break;
								case 0:
								default:
									$queryBuilder->orderBy('informationsystem_items.datetime', $order_type);
									break;
							}
						}
						else
						{
							if (strtolower($property['OrderField']) == 'information_items_comment_grade')
							{
								$queryBuilder->orderBy('AVG(comments.grade)', $order_type);
							}
							else
							{
								$queryBuilder->orderBy($property['OrderField'], $order_type);
							}
						}

						// Определяем группы доступа для текущего авторизированного	пользователя
						$mas_result = $this->GetSiteUsersGroupsForUser($site_user_id);

						$queryBuilder
							->from('informationsystem_items')
							->leftJoin('informationsystem_groups', 'informationsystem_items.informationsystem_group_id', '=', 'informationsystem_groups.id',
									array(
										array('AND' => array('informationsystem_groups.siteuser_group_id', 'IN', $mas_result))
									)
							)
							->where('informationsystem_items.siteuser_group_id', 'IN', $mas_result);

						$current_date = date('Y-m-d H:i:s');

						/* Если есть внешние параметры выборки и указана выборка элементов - $property['SelectPropertyInQuery'],
						 тогда объединяем с таблицей св-в и значений*/
						if (isset($property['select']) && count($property['select']) > 0
						&& $property['SelectPropertyInQuery'])
						{

							// Задано ограничение для информационных элементов или
							// не задано ограничений для инфоэлементов и информационных групп
							if (isset($isset_items_property) || !isset($isset_items_property) && !isset($isset_groups_property))
							{
								$queryBuilder->where('informationsystem_items.informationsystem_id', '=', $row['information_systems_id']);
							}

							if (isset($isset_groups_property))
							{
								$queryBuilder
									->where('informationsystem_group_properties.informationsystem_id', '=', $row['information_systems_id']);
							}
						}
						else
						{
							$queryBuilder
								->where('informationsystem_items.informationsystem_id', '=', $row['information_systems_id']);
						}

						// Если только активные (без неактивных)
						if (in_array('active', $property['show_item_type']) && !in_array('inactive', $property['show_item_type']))
						{
							$queryBuilder->where('informationsystem_items.active', '=', 1);
						}
						// только неактивные
						elseif (in_array('inactive', $property['show_item_type']) && !in_array('active', $property['show_item_type']))
						{
							$queryBuilder->where('informationsystem_items.active', '=', 0);
						}
						// иначе выбираем и активные, и неактивные

						// Если не содержит putend_date - ограничиваем по дате окончания публикации
						if (!in_array('putend_date', $property['show_item_type']))
						{
							$queryBuilder
								->open()
								->where('informationsystem_items.end_datetime', '>=', $current_date)
								->setOr()
								->where('informationsystem_items.end_datetime', '=', '0000-00-00 00:00:00')
								->close();
						}

						// Если не содержит putend_date - ограничиваем по дате окончания публикации
						if (!in_array('putoff_date', $property['show_item_type']))
						{
							$queryBuilder
								->where('informationsystem_items.start_datetime', '<=', $current_date);
						}

						// Объединяем с тегами и ограничиваем по ним
						if (isset($property['tags']) && count($property['tags']) > 0 && class_exists('Tag'))
						{
							$oTag = & singleton('Tag');

							$xmlData .= '<selected_tags>' . "\n";

							// Приводим к целому числу
							foreach ($property['tags'] as $key => $tag_id)
							{
								$property['tags'][$key] = intval($tag_id);

								// XML для тега
								$tag_xml = $oTag->GenXmlForTag($tag_id);

								if ($tag_xml)
								{
									$xmlData .= $tag_xml;
								}
							}

							$xmlData .= '</selected_tags>' . "\n";

							$queryBuilder
								->leftJoin('tag_informationsystem_items', 'informationsystem_items.id', '=', 'tag_informationsystem_items.informationsystem_item_id')
								->where('tag_informationsystem_items.tag_id', 'IN', $property['tags']);
						}

						if ($items_on_page !== FALSE && $items_on_page > 0)
						{
							// Если есть ограничение на количество на страницу
							if ($items_on_page !== FALSE)
							{
								$queryBuilder->limit($items_begin, $items_on_page);
							}

							if (isset($property['OrderField'])
								&& strtolower($property['OrderField']) == 'information_items_comment_grade')
							{
								$queryBuilder->groupBy('informationsystem_items.id');

								//$queryBuilder->orderBy('AVG(comments.grade)');

								$queryBuilder
									->leftJoin('comment_informationsystem_items', 'informationsystem_items.id', '=', 'comment_informationsystem_items.informationsystem_item_id')
									->leftJoin('comments', 'comment_informationsystem_items.comment_id', '=', 'comments.id');
							}
							else
							{
								$queryBuilder->distinct();
							}

							if (isset($property['sql_from']))
							{
								$aSqlFrom = explode(',', $sql_from);

								foreach($aSqlFrom as $sSqlFrom)
								{
									trim($sSqlFrom) != '' && $queryBuilder->from(trim($sSqlFrom));
								}
							}

							$oCore_DataBase = $queryBuilder->execute();
							$result = $oCore_DataBase->getResult();
							$count_selected_item = $oCore_DataBase->getNumRows();

							$queryBuilderSame = Core_QueryBuilder::select(array('FOUND_ROWS()', 'count'));

							// Определим количество элементов
							$count_items = $queryBuilderSame->execute()->asAssoc()->current();
							$count_items = $count_items['count'];

							$xmlData .= '<items>' . "\n";
							$xmlData .= '<count_items>' . $count_items . '</count_items>' . "\n";

							// Определяем число страниц
							if ($items_on_page > 0)
							{
								$current_page = $items_begin / $items_on_page;
							}
							else
							{
								$current_page = '';
							}

							$xmlData .= '<current_page>' . $current_page . '</current_page>' . "\n";
							$xmlData .= '<items_on_page>' . $items_on_page . '</items_on_page>' . "\n";

							$in = array();

							if ($count_items > 0)
							{
								for($i = 0; $i < $items_on_page; $i++)
								{
									// Элемент входит в заданый диапазон отбора
									if ($information_item = mysql_fetch_assoc($result))
									{
										$in[] = $information_item['id'];
									}
									else
									{
										break;
									}
								}
							}

							// Если есть хоть один элемент
							if ($count_selected_item > 0)
							{
								if (class_exists('Tag'))
								{
									// Заполняем кэш для тегов
									$oTag = & singleton('Tag');
									$oTag->FillMemCacheGetTagRelation(array('information_items_id' => $in));
								}

								$queryBuilder
									->clear()
									->select()
									->from('informationsystem_items')
									->where('deleted', '=', 0)
									->where('id', 'IN', $in);

								$aResultItems = $queryBuilder->execute()
									->asObject('Informationsystem_Item_Model')
									->result();

								foreach($aResultItems as $oItem)
								{
									// Пишем в кэш для инфоэлементов
									$this->ItemMass[$oItem->id] = $this->getArrayInformationsystemItem($oItem);
								}

								foreach ($in as $information_items_id)
								{
									$return_mass[] = $information_items_id;

									// Генерация XML для информационных элементов
									$xmlData .= $this->GetXmlForInformationItem($information_items_id, $property);

									if (isset($this->PropertyMass[$information_items_id]))
									{
										unset($this->PropertyMass[$information_items_id]);
									}
								}

								// Очищаем кэш с массивом отображенных элементов
								$this->ItemMass = array();
							}
							$xmlData .= '</items>' . "\n";
						}
					}
					$xmlData .= '</blocks>' . "\n";
				}
			}

			if (isset($this->CacheGoupsIdTree[$InformationSystem_id]))
			{
				unset($this->CacheGoupsIdTree[$InformationSystem_id]);
			}
		}// end foreach

		$xmlData .= '</document>'."\n";

		$xsl = & singleton('xsl');
		$result = $xsl->build($xmlData, $xsl_name);

		if (isset($property['cache']) && $property['cache'] && class_exists('Cache'))
		{
			$cache->Insert($cache_element_name,
			array('result' => $result, 'return_mass' => $return_mass),
			$cache_name);
		}

		echo $result;

		return $return_mass;
	}

	/**
	 * Определение идентификатора информационного элемента по URI элемента и идентификатору информационной группы
	 *
	 * @param string $information_item_url URL информационного элемента
	 * @param int $information_group_id идентификатор информационной группы
	 * @param int $information_system_id идентификатор информационной системы, к которой принадлежит информационный элемент, идентификатор которого надо определить
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_item_url = 'news1';
	 * $information_group_id= 2;
	 * $information_system_id = 1;
	 *
	 * $newid = $InformationSystem->GetIdInformationItem($information_item_url, $information_group_id, $information_system_id);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed идентификатор информационного элемента или FALSE
	 */
	function GetIdInformationItem($information_item_url, $information_group_id, $information_system_id=0)
	{
		$information_group_id = intval($information_group_id);
		$information_system_id = intval($information_system_id);

		$cache_name = 'INF_SYS_INFORMATION_ITEM_FROM_URL';
		$cache_filed_name = $information_group_id . ' ' . $information_system_id . ' ' . $information_item_url;

		/* Проверка на наличие в файловом кэше*/
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			if ($in_cache = $cache->GetCacheContent($cache_filed_name, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$queryBuilder = Core_QueryBuilder::select('id')
			->from('informationsystem_items')
			->where('informationsystem_group_id', '=', $information_group_id)
			->where('path', '=', $information_item_url)
			->where('deleted', '=', 0);

		if ($information_system_id)
		{
			$queryBuilder->where('informationsystem_id', '=', $information_system_id);
		}

		$aInformationsystemItem = $queryBuilder->execute()->asAssoc()->current();

		$item_id = is_null($aInformationsystemItem['id'])
			? $information_item_url
			: $aInformationsystemItem['id'];

		if (class_exists('Cache'))
		{
			$cache->Insert($cache_filed_name, $aInformationsystemItem['id'], $cache_name);
		}

		return $item_id;
	}

	/**
	 * Получение числа свойств информационных элементов информационной системы
	 *
	 * @param int $information_system_id идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_system_id = 1;
	 *
	 * $result = $InformationSystem->GetCountProperty4InformationSystem($information_system_id);
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int число свойств информационной системы
	 */
	function GetCountProperty4InformationSystem($information_system_id)
	{
		$information_system_id = intval($information_system_id);

		return count(Core_Entity::factory('Informationsystem_Item_Property_List', $information_system_id)
			->Properties
			->findAll());
	}

	/**
	 * Получение данных об информационной системе
	 *
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * @param boolean $use_cache параметр, определяющий использовать кэш в памяти или нет (по умолчанию истина)
	 * @param array $param ассоциативный массив параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $InformationSystem_id = 1;
	 *
	 * $row = $InformationSystem->GetInformationSystem($InformationSystem_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed ассоциативный массив с данными об информационной системе или FALSE, если информационная система не найдена
	 */
	function GetInformationSystem($InformationSystem_id, $use_cache = TRUE, $param = array())
	{
		$InformationSystem_id = intval($InformationSystem_id);

		if ($InformationSystem_id == 0)
		{
			return FALSE;
		}

		$param = Core_Type_Conversion::toArray($param);

		// Проверяем, есть ли в кэше данные для информационной системы
		if ($use_cache && isset($this->cache_InformationSystem[$InformationSystem_id]))
		{
			return $this->cache_InformationSystem[$InformationSystem_id];
		}

		// Файловый кэш
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'INF_SYS';

			if ($in_cache = $cache->GetCacheContent($InformationSystem_id, $cache_name))
			{
				// Сохраняем в кэше данные об информационной системе
				if ($use_cache)
				{
					$this->cache_InformationSystem[$InformationSystem_id] = $in_cache['value'];
				}

				return $in_cache['value'];
			}
		}

		$oInformationsystem = Core_Entity::factory('Informationsystem')->find($InformationSystem_id);
		if (!is_null($oInformationsystem->id))
		{
			$aInformationsystem = $this->getArrayInformationsystem($oInformationsystem);
		}
		else
		{
			$aInformationsystem = FALSE;
		}

		// Сохраняем в кэше данные об информационной системе
		if ($use_cache)
		{
			$this->cache_InformationSystem[$InformationSystem_id] = $aInformationsystem;
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache->Insert($InformationSystem_id, $aInformationsystem, $cache_name);
		}

		return $aInformationsystem;
	}

	/**
	 * @access private
	 */
	function GetXmlForInformatioItem($information_item_id, $property = array (
	'is_get_information_for_property' => FALSE,
	'show_text' => TRUE))
	{
		return $this->GetXmlForInformationItem($information_item_id, $property);
	}

	/**
	 * Формирование XML для отображения информационного элемента
	 *
	 * @param int $information_item_id идентификатор информационного элемента, который необходимо отобразить
	 * @param array $property массив дополнительных параметров
	 * - $property['part'] номер части документа, подлежащей отображению.
	 * Документ может быть разделен с помощью разделителя <!-- pagebreak -->.
	 * Нумерация разделителя ведется с 1. Если передан 0 - разделение не производится. по умолчанию имеет значение 1.
	 * - $property['is_get_information_for_property'] параметр, исключающий рекурсию, возникающую при вызове свойства типа "Информационная система" для элемента, имеющего также свойство типа "Информационная система" (эффект зазеркаливания), по умолчанию FALSE
	 * - $property['show_text'] параметр, указывающий включать в XML текст информационного элемента или нет, по умолчанию равен true. Если этот параметр не указан, то текст элемента включается в XML.
	 * - $property['xml_show_item_comment'] параметр, разрешающий добавление в XML информации о комментариях информационного элемента (true (по умолчанию) комментарии добавляеются, FALSE - не добавляются)
	 * - $property['xml_show_item_property'] параметр, разрешающий добавление в XML информации о дополнительных свойствах информационного элемента (true (по умолчанию) дополнительные свойства добавляеются, FALSE - не добавляются)
	 * - $property['xml_show_external_property'] параметр, разрешающий передачу в XML информации о дополнительных свойствах пользователя, по умолчанию FALSE
	 *
	 * <br />Замечание: перед вызовом необходимо заполнить массив групп для конкретной информационной системы, например $InformationSystem->FillMasGroup($information_system_id);
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_item_id = 1;
	 *
	 * $xml = $InformationSystem->GetXmlForInformationItem($information_item_id);
	 *
	 * // Распечатаем результат
	 * echo nl2br(htmlspecialchars($xml));
	 * ?>
	 * </code>
	 * @return string метод, формирующий XML для отображения информационного элемента
	 */
	function GetXmlForInformationItem($information_item_id, $property = array(
	'is_get_information_for_property' => FALSE,
	'show_text' => TRUE))
	{
		$information_item_id = intval($information_item_id);

		if (class_exists('lists'))
		{
			$lists = & singleton('lists');
		}

		// по умолчанию выводим первую часть
		if (!isset($property['part']))
		{
			$property['part'] = 1;
		}

		if (!isset($property['xml_show_item_property']))
		{
			$property['xml_show_item_property'] = TRUE;
		}

		if (!isset($property['xml_show_item_comment']))
		{
			$property['xml_show_item_comment'] = TRUE;
		}

		$row_original = $this->GetInformationSystemItem($information_item_id, $property);

		// Если это ярлык
		if ($row_original['information_items_shortcut_id'])
		{
			$row = $this->GetInformationSystemItem($row_original['information_items_shortcut_id'], $property);
		}
		else
		{
			$row = $row_original;
		}

		$xmlData = '';

		if ($row)
		{
			// Мог быть изменен в связи с ярлыком
			$information_item_id = $row['information_items_id'];

			// Путь к папке информационного элемента
			$information_item_dir = $this->GetInformationItemDir($information_item_id);

			$uploaddir = CMS_FOLDER . $information_item_dir;

			$row_infsys = $this->GetInformationSystem($row['information_systems_id']);

			if ($row_infsys)
			{
				// Информацию о пути к инфосистеме получаем по идентификатору узла структуры, с которым связана инфосистема
				$structure = & singleton('Structure');
				$url = $structure->GetStructurePath($row_infsys['structure_id'], 0);

				if ($url != '/')
				{
					$InformationSystem_url = '/' . $url;
				}
				else
				{
					$InformationSystem_url = $url;
				}

				$xmlData .= '<item id="' . $row['information_items_id'] . '" group_id="' . $row_original['information_groups_id'] . '">' . "\n";

				$xmlData .= '<item_date>' . str_for_xml(
				strftime(Core_Type_Conversion::toStr($row_infsys['information_systems_format_date']), Core_Date::sql2timestamp($row['information_items_date'])))
				. '</item_date>' . "\n";

				$xmlData .= '<item_datetime>' . str_for_xml(
				strftime(Core_Type_Conversion::toStr($row_infsys['information_systems_format_datetime']), Core_Date::sql2timestamp($row['information_items_date'])))
				. '</item_datetime>' . "\n";

				$date = explode(' ', $row['information_items_date']);

				// Добавляем в XML время
				if (isset($date[1]))
				{
					$xmlData .= '<item_time>' . $date[1] . '</item_time>' . "\n";
				}

				// Дата
				if ($row['information_items_putoff_date'] != '0000-00-00 00:00:00')
				{
					$item_putoff_date = strftime(Core_Type_Conversion::toStr($row_infsys['information_systems_format_datetime']), Core_Date::sql2timestamp($row['information_items_putoff_date']));
				}
				else
				{
					$item_putoff_date = '00.00.0000 00:00:00';
				}

				$xmlData .= '<item_putoff_date>' . str_for_xml($item_putoff_date) . '</item_putoff_date>' . "\n";

				// Дата
				if ($row['information_items_putend_date'] != '0000-00-00 00:00:00')
				{
					$item_putend_date = strftime(Core_Type_Conversion::toStr($row_infsys['information_systems_format_datetime']), Core_Date::sql2timestamp($row['information_items_putend_date']));
				}
				else
				{
					$item_putend_date = '00.00.0000 00:00:00';
				}

				$xmlData .= '<item_putend_date>' . str_for_xml($item_putend_date) . '</item_putend_date>' . "\n";

				$xmlData .= '<item_name>' . str_for_xml($row['information_items_name']) . '</item_name>' . "\n";

				// Типографика на лету
				if (defined('ALLOW_TYPOGRAPH_INFORMATION_SYSTEM') && ALLOW_TYPOGRAPH_INFORMATION_SYSTEM)
				{
					$use_trailing_punctuation = defined('ALLOW_TRAILING_PUNCTUATION_INFORMATION_SYSTEM') && ALLOW_TRAILING_PUNCTUATION_INFORMATION_SYSTEM;

					if (Core::moduleIsActive('typograph'))
					{
						$row['information_items_description'] = Typograph_Controller::instance()->process($row['information_items_description'], $use_trailing_punctuation);

						$row['information_items_text'] = Typograph_Controller::instance()->process($row['information_items_text'], $use_trailing_punctuation);
					}
				}

				$xmlData .= '<item_description>' . str_for_xml($row['information_items_description']) . '</item_description>' . "\n";

				if (!(isset($property['show_text']) && !$property['show_text']))
				{
					// Определяем, сколько частей содержится в документе
					if ($property['part'] != 0)
					{
						// Уменьшаем значение, т.к. в клиентском разделе счет идет с 1
						$property['part']--;

						$part_array = explode('<!-- pagebreak -->', $row['information_items_text']);

						$count_part = count($part_array);

						if ($property['part'] > $count_part)
						{
							$property['part'] = $count_part;
						}

						$text = & $part_array[$property['part']];

						$xmlData .= '<part count="' . $count_part . '">' . intval($property['part']) . '</part>' . "\n";
					}
					else
					{
						$text = $row['information_items_text'];
					}

					$xmlData .= '<item_text>' . str_for_xml($text) . '</item_text>' . "\n";
				}

				$size_big_image = array('width' => 0, 'height' => 0);
				$size_small_image = array('width' => 0, 'height' => 0);

				$Image = & singleton('Image');

				// Большое изображение загружено
				if ($row['information_items_image'] != '')
				{
					$item_image = $uploaddir . $row['information_items_image'];

					// Не задана ширина большого изображения
					if (intval($row['information_items_image_width']) == 0)
					{
						// размеры большой картинки
						$size_big_image = $Image->GetImageSize($item_image);

						$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($row['information_items_id']);

						$oInformationsystem_Item->image_large_width = Core_Type_Conversion::toInt($size_big_image['width']);
						$oInformationsystem_Item->image_large_height = Core_Type_Conversion::toInt($size_big_image['height']);

						$oInformationsystem_Item->save();
					}
					// Заданы размеры боьшого изображения
					else
					{
						$size_big_image['width'] = $row['information_items_image_width'];
						$size_big_image['height'] = $row['information_items_image_height'];
					}

					$fname_big_image_for_xml = '/' . $information_item_dir . str_for_xml($row['information_items_image']);
				}
				else
				{
					$item_image = '';
					$fname_big_image_for_xml = '';
				}

				// Малое изображение загружено
				if ($row['information_items_small_image'] != '')
				{
					$item_small_image = $uploaddir . $row['information_items_small_image'];

					// Не задана ширина малого изображения
					if (intval($row['information_items_small_image_width']) == 0)
					{
						// определяем размеры малой картинки
						$size_small_image = $Image->GetImageSize($item_small_image);

						$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($row['information_items_id']);

						$oInformationsystem_Item->image_small_width = Core_Type_Conversion::toInt($size_small_image['width']);
						$oInformationsystem_Item->image_small_height = Core_Type_Conversion::toInt($size_small_image['height']);
						$oInformationsystem_Item->save();
					}
					else
					{
						$size_small_image['width'] = $row['information_items_small_image_width'];
						$size_small_image['height'] = $row['information_items_small_image_height'];
					}

					$fname_small_image_for_xml = '/' . $information_item_dir . str_for_xml($row['information_items_small_image']);
				}
				// Малое изображение не загружено
				else
				{
					$fname_small_image_for_xml = '';
					$item_small_image = '';
				}

				$xmlData .= '<item_image width="' . Core_Type_Conversion::toInt($size_big_image['width']) . '" height="' . Core_Type_Conversion::toInt($size_big_image['height']) . '">' . $fname_big_image_for_xml . '</item_image>' . "\n";
				$xmlData .= '<item_small_image width="' . Core_Type_Conversion::toInt($size_small_image['width']) . '" height="' . Core_Type_Conversion::toInt($size_small_image['height']) . '">' . $fname_small_image_for_xml . '</item_small_image>' . "\n";
				$xmlData .= '<information_items_order>' . $row['information_items_order'] . '</information_items_order>' . "\n";
				$xmlData .= '<item_status>' . $row['information_items_status'] . '</item_status>' . "\n";
				$xmlData .= '<item_ip>' . $row['information_items_ip'] . '</item_ip>' . "\n";
				$xmlData .= '<item_seo_title>' . str_for_xml($row['information_items_seo_title']).'</item_seo_title>'."\n";
				$xmlData .= '<item_seo_description>' . str_for_xml($row['information_items_seo_description']).'</item_seo_description>'."\n";
				$xmlData .= '<item_seo_keywords>' . str_for_xml($row['information_items_seo_keywords']).'</item_seo_keywords>'."\n";
				$xmlData .= '<item_access>' . str_for_xml($row['information_items_access']).'</item_access>'."\n";
				$xmlData .= '<item_path>' . str_for_xml($InformationSystem_url.$this->GetPathItem($row['information_items_id'],'',$row)).'</item_path>'."\n";
				$xmlData .= '<item_path_field>' . str_for_xml($row['information_items_url']).'</item_path_field>'."\n";
				$xmlData .= '<item_show_count>' . str_for_xml($row['information_items_show_count']). '</item_show_count>' . "\n";

				$xmlData .= '<site_user_id>' . Core_Type_Conversion::toInt($row['site_users_id']) . '</site_user_id>' . "\n";

				// Если разрешен показ св-в элементов
				if ($property['xml_show_item_property'])
				{
					$xmlData .= '<item_propertys>' . "\n";

					// проверяем, есть ли в кэше значение количества свойств для инф. системы
					if (!isset($this->a_count_property[$row['information_systems_id']]))
					{
						$this->a_count_property[$row['information_systems_id']] = $this->GetCountProperty4InformationSystem($row['information_systems_id']);
					}

					// Дополнительные свойства считаем только в том случае, если они вообще есть для системы
					if ($this->a_count_property[$row['information_systems_id']] != 0)
					{
						// выводим свойства информационного элемента
						$row1 = $this->GetPropertysInformationSystemItem($row['information_items_id'], $property);

						$upload = CMS_FOLDER . $information_item_dir;

						if ($row1)
						{
							foreach ($row1 as $value)
							{
								if (trim($value['information_propertys_xml_name']) != '')
								{
									switch ($value['information_propertys_type'])
									{
										case 2: // свойство является файлом
											{
												if ($value['information_propertys_items_value'] != ''
												|| $value['information_propertys_items_value_small'] != '')
												{
													$xmlData .= '<item_property type="File" xml_name="' . $value['information_propertys_xml_name'] . '" parent_id="' . $value['information_propertys_items_dir_id'] . '" id="' . $value['information_propertys_id'] . '">' . "\n";
													$xmlData .= '<property_id>' . $value['information_propertys_items_id'] . '</property_id>' . "\n";
													$xmlData .= '<property_xml_name>' . str_for_xml($value['information_propertys_xml_name']) . '</property_xml_name>' . "\n";
													$xmlData .= '<property_name>' . str_for_xml($value['information_propertys_name']) . '</property_name>' . "\n";

													// Оставлено для обратной совместимости
													$xmlData .= '<' . $value['information_propertys_xml_name'] . '>' . str_for_xml($value['information_propertys_items_value']) . '</' . $value['information_propertys_xml_name'] . '>' . "\n";

													$xmlData .= '<property_order>' . $value['information_propertys_order'] . '</property_order>' . "\n";

													// Задана большая картинка
													if ($value['information_propertys_items_value'] != '')
													{
														$size_small_image = $Image->GetImageSize($item_small_image);

														$file_path = $upload . $value['information_propertys_items_file'];

														// проверяем существует ли файл большой картинки
														if (is_file($file_path)
														&& filesize($file_path) > 12)
														{
															// дополнительное свойство является изображением, тегу value
															// дописываем атрибуты width - ширина и height - высота
															if (Core_Image::instance()->exifImagetype($file_path))
															{
																$size_property_big_image = $Image->GetImageSize($file_path);
																$atributs = 'width="' . $size_property_big_image['width'] . '"  height="' . $size_property_big_image['height'] . '"';
															}
															else
															{
																$atributs = '';
															}

															// Определяем размер файла в байтах
															$size = @filesize($file_path);
															$atributs .= ' size="' . $size . '"';

															$xmlData .= '<value>' . str_for_xml($value['information_propertys_items_value']) . '</value>' . "\n";
															$xmlData .= '<property_file_path ' . trim($atributs) . '>' . '/' . $information_item_dir . $value['information_propertys_items_file'] . '</property_file_path>' . "\n";
														}
													}

													// Задана малая картинка
													if ($value['information_propertys_items_value_small'] != '')
													{
														$xmlData .= '<small_image>'."\n";

														$file_path = $upload . $value['information_propertys_items_file_small'];

														// проверяем существует ли файл маленькой картинки
														if (is_file($file_path)
														&& filesize($file_path) > 12)
														{
															if (Core_Image::instance()->exifImagetype($file_path))
															{
																$size_property_big_image=$Image->GetImageSize($file_path);
																$atributs = 'width="' . $size_property_big_image['width'] . '"  height="' . $size_property_big_image['height'] . '"';
															}
															else
															{
																$atributs = '';
															}

															// Определяем размер файла в байтах
															$size = @filesize($file_path);

															$atributs .= ' size="' . $size . '"';

															$xmlData .= '<value>' . str_for_xml($value['information_propertys_items_value']) . '</value>' . "\n";
															$xmlData .= '<property_file_path ' . trim($atributs) . '>' . '/' . $information_item_dir . $value['information_propertys_items_file_small'] . '</property_file_path>' . "\n";
														}

														$xmlData .= '</small_image>'."\n";
													}

													$xmlData .= '</item_property>'."\n";
												}
												break;
											}
										case 3:// св-во является списком
											{
												if ($value['information_propertys_items_value'] != '')
												{
													// проверяем определена ли консанта и равна ли она true (модуль списков существует)
													if (class_exists('lists'))
													{
														$xmlData .= '<item_property type="List" xml_name="' . $value['information_propertys_xml_name'] . '" parent_id="' . $value['information_propertys_items_dir_id'] . '" id="' . $value['information_propertys_id'] . '">' . "\n";
														$xmlData .= '<property_id>' . str_for_xml($value['information_propertys_items_id']) . '</property_id>' . "\n";
														$xmlData .= '<property_xml_name>' . str_for_xml($value['information_propertys_xml_name']) . '</property_xml_name>' . "\n";
														$xmlData .= '<property_name>' . str_for_xml($value['information_propertys_name']) . '</property_name>' . "\n";
														$xmlData .= '<property_order>' . str_for_xml($value['information_propertys_order']) . '</property_order>' . "\n";

														$row3 = $lists->GetListItem($value['information_propertys_items_value']);
														// Если существует значение списка
														if ($row3)
														{
															$xmlData .= '<' . $value['information_propertys_xml_name'] . '>' . str_for_xml($row3['lists_items_value']) . '</' . $value['information_propertys_xml_name'] . '>' . "\n";
															$xmlData .= '<value>' . str_for_xml($row3['lists_items_value']) . '</value>' . "\n";
															$xmlData .= '<description>' . str_for_xml($row3['lists_items_description']) . '</description>' . "\n";
															$xmlData .= '<value_id>' . intval($row3['lists_items_id']) . '</value_id>' . "\n";
														}
														$xmlData .= '</item_property>' . "\n";
													}
												}
												break;
											}
										case 5: // св-во является инфосистемой
											{
												if (!empty($value['information_propertys_items_value']))
												{
													$xmlData .= '<item_property type="InformationSystemItem" xml_name="' . $value['information_propertys_xml_name'] . '" parent_id="' . $value['information_propertys_items_dir_id'] . '" id="' . $value['information_propertys_id'] . '">' . "\n";
													$xmlData .= '<property_id>' . $value['information_propertys_items_id'] . '</property_id>' . "\n";
													$xmlData .= '<property_xml_name>' . str_for_xml($value['information_propertys_xml_name']) . '</property_xml_name>' . "\n";
													$xmlData .= '<property_name>' . str_for_xml($value['information_propertys_name']) . '</property_name>' . "\n";
													$xmlData .= '<property_order>' . $value['information_propertys_order'].'</property_order>'."\n";
													$xmlData .= '<value>' . str_for_xml($value['information_propertys_items_value']) . '</value>' . "\n";

													// Оставлено для обратной совместимости
													$xmlData .= '<' . $value['information_propertys_xml_name'].'>'.str_for_xml($value['information_propertys_items_value']).'</'.$value['information_propertys_xml_name'].'>'."\n";

													$row3 = $this->GetInformationSystemItem($value['information_propertys_items_value']);

													if ($row3)
													{
														// Значение свойства типа информационный элемент получаем
														// только в том случае, если это само не получение для свойства
														if (!isset($property['is_get_information_for_property']))
														{
															// Передаем новый массив, т.к. текущий массив св-в будет использоваться и для других св-в типа "Информационная система"
															$property4property = $property;
															$property4property['is_get_information_for_property'] = true;
															$xmlData.= $this->GetXmlForInformationItem($row3['information_items_id'], $property4property);
														}
													}
													$xmlData .= '</item_property>'."\n";
												}
												break;
											}
											// свойство - checkbox
										case 7:
											{
												$xmlData .= '<item_property type="Checkbox" xml_name="' . $value['information_propertys_xml_name'] . '" parent_id="' . $value['information_propertys_items_dir_id'] . '" id="' . $value['information_propertys_id'] . '">' . "\n";
												$xmlData .= '<property_id>'.$value['information_propertys_items_id'].'</property_id>'."\n";
												$xmlData .= '<property_xml_name>'.str_for_xml($value['information_propertys_xml_name']).'</property_xml_name>'."\n";
												$xmlData .= '<property_name>'.str_for_xml($value['information_propertys_name']).'</property_name>'."\n";
												$xmlData .= '<property_order>'.$value['information_propertys_order'].'</property_order>'."\n";

												// Оставлено для обратной совместимости
												$xmlData .= '<'.$value['information_propertys_xml_name'].'>'.str_for_xml($value['information_propertys_items_value']).'</'.$value['information_propertys_xml_name'].'>'."\n";

												$xmlData .= '<value>'.str_for_xml($value['information_propertys_items_value']).'</value>'."\n";
												$xmlData .= '</item_property>'."\n";

												break;
											}
											// свойство - Дата
										case 8:
											{
												$xmlData .= '<item_property type="Data" xml_name="' . $value['information_propertys_xml_name'] . '" parent_id="'.$value['information_propertys_items_dir_id'].'" id="'.$value['information_propertys_id'].'">' . "\n";
												$xmlData .= '<property_id>' . $value['information_propertys_items_id'] . '</property_id>' . "\n";
												$xmlData .= '<property_xml_name>' . str_for_xml($value['information_propertys_xml_name']) . '</property_xml_name>' . "\n";
												$xmlData .= '<property_name>' . str_for_xml($value['information_propertys_name']) . '</property_name>' . "\n";
												$xmlData .= '<property_order>' . $value['information_propertys_order'] . '</property_order>' . "\n";

												// Оставлено для обратной совместимости
												$xmlData .= '<' . $value['information_propertys_xml_name'] . '>' . str_for_xml($value['information_propertys_items_value']) . '</' . $value['information_propertys_xml_name'] . '>' . "\n";

												$xmlData .= '<value>' . str_for_xml($value['information_propertys_items_value']) . '</value>' . "\n";
												$xmlData .= '</item_property>' . "\n";

												break;
											}
											// свойство - ДатаВремя
										case 9:
											{
												$xmlData .= '<item_property type="DataTime" xml_name="'.$value['information_propertys_xml_name'] . '" parent_id="' . $value['information_propertys_items_dir_id'] . '" id="' . $value['information_propertys_id'] . '">' . "\n";
												$xmlData .= '<property_id>' . $value['information_propertys_items_id'] . '</property_id>' . "\n";
												$xmlData .= '<property_xml_name>' . str_for_xml($value['information_propertys_xml_name']) . '</property_xml_name>' . "\n";
												$xmlData .= '<property_name>' . str_for_xml($value['information_propertys_name']) . '</property_name>' . "\n";
												$xmlData .= '<property_order>' . $value['information_propertys_order'] . '</property_order>' . "\n";

												// Оставлено для обратной совместимости
												$xmlData .='<'.$value['information_propertys_xml_name'].'>'.str_for_xml($value['information_propertys_items_value']).'</'.$value['information_propertys_xml_name'].'>'."\n";

												$xmlData .= '<value>' . str_for_xml($value['information_propertys_items_value']).'</value>'."\n";
												$xmlData .= '</item_property>' . "\n";

												break;
											}
										default:
										{
											switch ($value['information_propertys_type'])
											{
												case 0:
													$property_type_name = 'Number';
												break;
												case 1:
													$property_type_name = 'String';
												break;
												case 4:
													$property_type_name = 'Textarea';
												break;
												case 6:
													$property_type_name = 'WYSIWYG';
												break;
												default:
													$property_type_name = 'Any';
												break;
											}

											$xmlData .= '<item_property type="' . $property_type_name . '" xml_name="' . $value['information_propertys_xml_name'] . '" parent_id="'.$value['information_propertys_items_dir_id'].'" id="'.$value['information_propertys_id'].'">' . "\n";
											$xmlData .= '<property_xml_name>' . str_for_xml($value['information_propertys_xml_name']) . '</property_xml_name>' . "\n";
											$xmlData .= '<property_name>' . str_for_xml($value['information_propertys_name']) . '</property_name>' . "\n";
											$xmlData .= '<property_order>' . $value['information_propertys_order'] . '</property_order>' . "\n";

											// Оставлено для обратной совместимости
											$xmlData .= '<' . $value['information_propertys_xml_name'] . '>' . str_for_xml($value['information_propertys_items_value']) . '</' .$value['information_propertys_xml_name'] . '>' . "\n";
											$xmlData .= '<value>' . str_for_xml($value['information_propertys_items_value']) . '</value>' . "\n";
											$xmlData .= '</item_property>' . "\n";

											break;
										}
									}
								}
							}
						}
					}

					$xmlData .= '</item_propertys>'."\n";
				}

				// Если разрешен показ комментариев элементов
				if ($property['xml_show_item_comment'])
				{
					// В GetXmlForInformatioItemComments() передаем переданные параметры для ИС + ID элемента
					$comment_property = $property;
					$comment_property['information_items_id'] = $information_item_id;

					// Выбираются корневые комментарии, дочерние выбираются при построении XML
					$comment_property['comment_parent_id'] = 0;

					// Добавляем комментарии для элемента
					$xmlData .= $this->GetXmlForInformatioItemComments($comment_property);
				}

				if (class_exists('Tag'))
				{
					// Получаем теги для ИЭ
					$oTag = & singleton('Tag');
					$tags = $oTag->GetTagRelation(array('information_items_id' => $information_item_id));

					if ($tags)
					{
						$xmlData .= "<tags>\n";

						foreach ($tags as $tag)
						{
							$tag_xml = $oTag->GenXmlForTag($tag['tag_id'], $tag);

							if ($tag_xml)
							{
								$xmlData .= $tag_xml;
							}
						}

						$xmlData .= "</tags>\n";
					}
				}

				// Пользователь, добавивший элемент
				if (class_exists('SiteUsers') && $row['site_users_id'] > 0)
				{
					$SiteUsers = & singleton('SiteUsers');
					$xmlData .= "<site_user>\n";

					$param_site_user = array();

					if (isset($property['xml_show_external_property']))
					{
						$param_site_user['xml_show_external_property'] = $property['xml_show_external_property'];
					}
					else
					{
						$param_site_user['xml_show_external_property'] = FALSE;
					}

					$xmlData .= $SiteUsers->GetSiteUserXml($row['site_users_id'], array(), array(), $param_site_user);
					$xmlData .= "</site_user>\n";
				}

				$xmlData .= '</item>'."\n";
			}
		}

		return $xmlData;
	}

	/**
	 * Создает XML для комметариев
	 *
	 * @param array $param массив атрибутов
	 * - $param['information_items_id'] mixed идентификатор информационного элемента,
	 * для которого получаем комментарии, если FALSE - выбираются все комментарии
	 * - $param['begin'] номер комментария, с которого начинать вывод
	 * - $param['count'] количество отбираемых комментариев
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param['information_items_id'] = 1;
	 * $param['begin'] = 13;
	 * $param['count'] = 2;
	 *
	 * $newxml = $InformationSystem->GetXmlForInformatioItemComments($param);
	 *
	 * // Распечатаем результат
	 * echo nl2br(htmlspecialchars($newxml));
	 * ?>
	 * </code>
	 */
	function GetXmlForInformatioItemComments($param)
	{
		// Идентификатор элемента не передан - выбираем все
		if (!isset($param['information_items_id']))
		{
			$param['information_items_id'] = FALSE;
		}

		// Массив комментариев
		$this->cm = $this->GetCommentInformationSystemItem($param);

		// Сохраняется сразу, т.к. далее будет перезапись при повторном вызове GetCommentInformationSystemItem()
 		// в других методах $this->ctotalcount будет увеличиваться
		$this->ctotalcount = $this->count_comments;

		if ($this->cm === FALSE)
		{
			$this->cm = array();
		}

		$xmlData = '<item_comments>' . "\n";

		$this->grade_sum = 0;
		$this->grade_count = 0;

		$aComments = array();

		foreach ($this->cm as $value)
		{
			$xmlData .= $this->GetXmlForOneComment($value['comment_id'], $param);
		}

		// Суммарная оценка
		$grade_sum = $this->grade_sum;
		// Число оценок
		$grade_count = $this->grade_count;

		// Средняя оценка
		if ($this->grade_count > 0)
		{
			$average_grade = $grade_sum / $grade_count;
		}
		else
		{
			$average_grade = 0;
		}

		$mod = $average_grade - intval($average_grade);
		$average_grade = intval($average_grade);

		if ($mod >= 0.25 && $mod < 0.75)
		{
			$average_grade += 0.5;
		}
		elseif ($mod >= 0.75)
		{
			$average_grade += 1;
		}

		// Общее количество комментариев
		$xmlData .= '<count_comments>' . intval($this->ctotalcount) . '</count_comments>' . "\n";

		// Суммарная оценка
		$xmlData .= '<grade_sum>' . $grade_sum . '</grade_sum>' . "\n";

		// Общее число оценок
		$xmlData .= '<grade_count>' . $grade_count . '</grade_count>' . "\n";
		$xmlData .= '<average_grade>' . $average_grade . '</average_grade>' . "\n";
		$xmlData .= '</item_comments>' . "\n";

		return $xmlData;
	}

	/**
	 * Генерация XML для комментария и всех его дочерних комментариев.
	 * Для генерации должны быть заполнены массивы $this->cm
	 *
	 * @param int $comment_id идентификатор комментария
	 * @param array $param массив дополнительных параметров
	 * - $param['xml_show_external_property'] параметр, разрешающий передачу в XML информации о дополнительных свойствах пользователя, по умолчанию FALSE
	 * - $param['GenXml_type'] тип генерации XML для метода GenXml() при обработке $external_propertys
	 * @return string
	 */
	function GetXmlForOneComment($comment_id, $param = array(), $external_propertys = array())
	{
		$xmlData = '';

		$DateClass = & singleton('DateClass');

		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
		}

		$row = $this->GetComment($comment_id);

		if (isset($param['GenXml_type']))
		{
			$param['GenXml_type'] = intval($param['GenXml_type']);
		}
		else
		{
			$param['GenXml_type'] = 0;
		}

		if ($row)
		{
			// Если была установлена оценка
			if ($row['comment_grade'] > 0)
			{
				$this->grade_sum += Core_Type_Conversion::toInt($row['comment_grade']);
				$this->grade_count++;
			}

			/* В список комментариев он идет, если есть текстовая информация,
			в противном случае коммнтарий считается только оценкой*/
			if (!(trim($row['comment_fio']) == ''
			&& trim($row['comment_subject']) == ''
			&& trim($row['comment_text']) == ''))
			{
				$row_item = $this->GetInformationSystemItem($row['information_items_id']);

				if ($row_item)
				{
					$row_infsys = $this->GetInformationSystem($row_item['information_systems_id']);

					if ($row_infsys)
					{
						$xmlData .= '<comment comment_parent_id="' . $row['comment_parent_id'] . '" information_item_id="' . $row['information_items_id'] . '">' . "\n";

						$ExternalXml = & singleton('ExternalXml');
						$xmlData .= $ExternalXml->GenXml($external_propertys, $param['GenXml_type']);
						unset($ExternalXml);

						$xmlData .= '<comment_id>' . str_for_xml($row['comment_id']) . '</comment_id>' . "\n";

						if ($row['site_users_id'] == 0)
						{
							$xmlData .= '<comment_fio>' . str_for_xml($row['comment_fio']) . '</comment_fio>' . "\n";
							$xmlData .= '<comment_email>' . str_for_xml($row['comment_email']) . '</comment_email>' . "\n";
							$xmlData .= '<comment_phone>' . str_for_xml($row['comment_phone']) . '</comment_phone>' . "\n";
						}
						elseif (class_exists('SiteUsers'))
						{
							$param_site_user = array();

							if (isset($param['xml_show_external_property']))
							{
								$param_site_user['xml_show_external_property'] = $param['xml_show_external_property'];
							}
							else
							{
								$param_site_user['xml_show_external_property'] = FALSE;
							}

							$row_siteuser = $SiteUsers->GetSiteUser($row['site_users_id']);

							if ($row_siteuser)
							{
								$xmlData .= '<comment_fio>' . str_for_xml($row_siteuser['site_users_login']) . '</comment_fio>' . "\n";
								$xmlData .= '<comment_email>' . str_for_xml($row_siteuser['site_users_email']) . '</comment_email>' . "\n";
							}

							// Добавляем информацию о пользователе
							$xmlData .= $SiteUsers->GetSiteUserXml($row['site_users_id'], array(), array(), $param_site_user);
						}

						$xmlData .= '<comment_text>' . htmlspecialchars($row['comment_text']) . '</comment_text>' . "\n";
						$xmlData .= '<comment_status>' . str_for_xml($row['comment_status']) . '</comment_status>' . "\n";
						$xmlData .= '<comment_subject>' . str_for_xml($row['comment_subject']) . '</comment_subject>' . "\n";

						$xmlData .= '<comment_date>' . str_for_xml(strftime(Core_Type_Conversion::toStr($row_infsys['information_systems_format_date']), Core_Date::sql2timestamp($row['comment_date'])/*$DateClass->DateSqlToUnix($row['comment_date'])*/)) . '</comment_date>' . "\n";
						$xmlData .= '<comment_datetime>' . str_for_xml(strftime(Core_Type_Conversion::toStr($row_infsys['information_systems_format_datetime']), Core_Date::sql2timestamp($row['comment_date'])/*$DateClass->DateSqlToUnix($row['comment_date'])*/ )) . '</comment_datetime>' . "\n";

						$xmlData .= '<comment_grade>' . str_for_xml($row['comment_grade']) . '</comment_grade>' . "\n";

						// Дочерние комментарии
						$aTmpArray = array('comment_parent_id' => $comment_id, 'begin' => 0) + $param;
						if (isset($aTmpArray['count']))
						{
							unset($aTmpArray['count']);
						}
						$subcomments = $this->GetCommentInformationSystemItem($aTmpArray);

						// Вывод информации для потомков комментария
						if (is_array($subcomments) && count($subcomments) > 0)
						{
							foreach ($subcomments as $comment_id => $comment_row)
							{
								// Увеличиваем число комментариев
								$this->ctotalcount++;

								$this->cm[$comment_id] = $comment_row;
								$xmlData .= $this->GetXmlForOneComment($comment_id, $param);
								unset($this->cm[$comment_id]);
							}
						}

						$xmlData .= '</comment>' . "\n";
					}
				}
			}
		}

		return $xmlData;
	}

	/**
	 * Получение информации об оценке информационного элемента
	 *
	 * @param int $information_item_id идентификатор информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_item_id = 107;
	 *
	 * $row = $InformationSystem->GetGradeInformationSystemItem($information_item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed в случае успешного выполнения - ассоциативный массив с информацией об  оценке элемента:<br />
	 * элемент с индексом 'count_grads'  - число оценок элемента;<br />
	 * 'sum_grade' - суммарная оценка;<br />
	 * 'avg_grade'  - средняя оценка;<br />
	 * 'round_avg_grade' - округленая средняя оценка.
	 */
	function GetGradeInformationSystemItem($information_item_id)
	{
		$information_item_id = intval($information_item_id);

		// Комментарии к информационному элементу
		$queryBuilder = Core_QueryBuilder::select(
			array('COUNT(id)', 'count_grads'),
			array('SUM(grade)', 'sum_grade'),
			array('AVG(grade)', 'avg_grade')
		)
		->from('comments')
		->join('comment_informationsystem_items', 'comments.id', '=', 'comment_informationsystem_items.comment_id')
		->join('informationsystem_items', 'comment_informationsystem_items.informationsystem_item_id', '=', 'id')
		->where('comments.deleted', '=', 0)
		->where('informationsystem_items.deleted', '=', 0)
		->where('informationsystem_items.id', '=', $information_item_id)
		->where('comments.active', '=', 1)
		->where('grade', '>', 0)
		->groupBy('informationsystem_items.id');

		$aGradeItemComments = $queryBuilder->execute()->asAssoc()->current();

		if ($aGradeItemComments)
		{
			// Средняя оценка
			if ($aGradeItemComments['count_grads'] > 0)
			{
				$average_grade = $aGradeItemComments['sum_grade'] / $aGradeItemComments['count_grads'];
			}
			else
			{
				$average_grade = 0;
			}

			$mod = $average_grade - intval($average_grade);
			$average_grade = intval($average_grade);

			if ($mod >= 0.25 && $mod < 0.75)
			{
				$average_grade += 0.5;
			}
			elseif ($mod >= 0.75)
			{
				$average_grade += 1;
			}

			$aGradeItemComments['round_avg_grade'] = $average_grade;

			return $aGradeItemComments;
		}

		return FALSE;
	}

	/**
	 * Устаревший метод отображения информации об информационном элементе, используйте метод ShowInformationSystemItem
	 *
	 * @access private
	 */
	function  ShowInformationBlockItem($information_item_id,  $xsl_name,$external_propertys=array())
	{
		return $this->ShowInformationSystemItem($information_item_id, $xsl_name,$external_propertys);
	}

	/**
	 * Отображение информации об информационном элементе
	 *
	 * @param int $information_item_id идентификатор, отображаемого информационного элемента
	 * @param string $xsl_name имя XSL-шаблона, применяемого для отображения информационного элемента
	 * @param array $external_propertys массив дополнительных свойств, индексы массива - имена дополнительных XML-тегов, элементы массива - значения этих тегов
	 * @param array $property массив дополнительных свойств
	 * - $property['part'] номер части документа, подлежащей отображению.
	 * Документ может быть разделен с помощью разделителя <!-- pagebreak -->.
	 * Нумерация разделителя ведется с 1. Если передан 0 - разделение не производится. по умолчанию имеет значение 1.
	 * - $property['xml_show_group_property'] разрешает указание в XML значений свойст информационной группы, по умолчанию true
	 * - $property['xml_show_group_property_id'] массив идентификаторов дополнительных свойств для отображения в XML. Если не не передано - выводятся все свойства
	 * - $property['cache'] - разрешает кэширование, по умолчанию true
	 * - $property['external_xml'] - внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
	 * - $property['show_item_type'] array массив ограничений для элемента. Может содержать следующие элементы:
	 * <ul>
	 * <li>active - активный (внесен по умолчанию, если $property['show_item_type'] не задан);
	 * <li>inactive - неактивный;
	 * <li>putend_date - не учитываем дату окончания публикации;
	 * <li>putoff_date - не учитываем дата начала публикации;
	 * </ul>
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_item_id = 1;
	 * $xsl_name = 'ВыводЕдиницыИнформационнойСистемы';
	 *
	 * $InformationSystem->ShowInformationSystemItem($information_item_id, $xsl_name);
	 *
	 * ?>
	 * </code>
	 * @return boolean true
	 */
	function ShowInformationSystemItem($information_item_id, $xsl_name, $external_propertys = array(), $property = array())
	{
		if (!isset($property['cache']))
		{
			$property['cache'] = TRUE;
		}

		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		if (!isset($property['xml_show_group_property']))
		{
			$property['xml_show_group_property'] = TRUE;
		}

		if (!isset($property['xml_show_group_property_id']))
		{
			$property['xml_show_group_property_id'] = array();
		}

		// по умолчанию выбираем данные по всем группам
		if (!isset($property['xml_show_group_type']))
		{
			$property['xml_show_group_type'] = 'tree';
		}

		// по умолчанию показываем только активный элемент
		if (!isset($property['show_item_type']))
		{
			$property['show_item_type'] = array('active');
		}

		// Получаем данные об информационном элементе
		$row_item = $this->GetInformationSystemItem($information_item_id, $property);

		// Проверяем доступен ли данный информационный элемент текущему авторизированному пользователю
		if ($this->GetAccessItem($site_user_id, $information_item_id, $row_item))
		{
			// Инкрементируем счетчик показов информационного элемента
			$this->InformationItemIncShowCount($information_item_id);

			$kernel = & singleton('kernel');

			// Показ плавающей панели
			if ($kernel->AllowShowPanel())
			{
				if ($item_row = $this->GetInformationSystemItem($information_item_id))
				{
					$param_panel = array();

					// Редактировать Инфоэл-т
					$param_panel[0]['image_path'] = "/hostcmsfiles/images/edit.gif";

					$sPath = '/admin/informationsystem/item/index.php';
					$sAdditional = "hostcms[action]=edit&informationsystem_id={$item_row['information_systems_id']}&informationsystem_group_id={$item_row['information_groups_id']}&hostcms[checked][1][{$information_item_id}]=1";

					$param_panel[0]['onclick'] = "$.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'}); return false";
					$param_panel[0]['href'] = "{$sPath}?{$sAdditional}";
					$param_panel[0]['alt'] = "Редактировать информационный элемент";

					// Удалить Инфоэл-т
					$param_panel[1]['image_path'] = "/hostcmsfiles/images/delete.gif";

					$sPath = '/admin/informationsystem/item/index.php';
					$sAdditional = "hostcms[action]=markDeleted&informationsystem_id={$item_row['information_systems_id']}&informationsystem_group_id={$item_row['information_groups_id']}&hostcms[checked][1][{$information_item_id}]=1";

					$param_panel[1]['onclick'] = "res = confirm('Вы уверены, что хотите Удалить?'); if (res) { $.openWindow({path: '{$sPath}', additionalParams: '{$sAdditional}', dialogClass: 'hostcms6'});} return false";
					$param_panel[1]['href'] = "{$sPath}?{$sAdditional}";
					$param_panel[1]['alt'] = "Удалить информационный элемент";

					// Выводим панель
					echo $kernel->ShowFlyPanel($param_panel);
				}
			}

			// Проверка на кэширование
			$cache_element_name = $information_item_id . '_' . $xsl_name . '_' . $site_user_id . '_' . $kernel->implode_array($external_propertys, '_') . '_' . $kernel->implode_array($property, '_');

			// Проверяем, установлен ли модуль кэширования
			if (class_exists('Cache') && $property['cache'])
			{
				$cache = & singleton('Cache');
				$cache_name = 'SHOW_INF_SYS_ITEM_XML';

				if (($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name)) && $in_cache)
				{
					echo $in_cache['value'];
					return TRUE;
				}
			}

			$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$xmlData .= '<document>' . "\n";

			// Вносим внешний XML в документ. Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа
			if (isset($property['external_xml']))
			{
				$xmlData .= $property['external_xml'];
			}

			$xmlData .= '<site_user_id>' . $site_user_id . '</site_user_id>' . "\n";

			// Вносим в XML дополнительные теги из массива дополнительных параметров
			$ExternalXml = new ExternalXml;
			$xmlData.= $ExternalXml->GenXml($external_propertys);

			$current_date = date('Y-m-d H:i:s');

			// Определяем группы доступа для текущего авторизированного пользователя
			$mas_result = $this->GetSiteUsersGroupsForUser($site_user_id);

			$queryBuilder = Core_QueryBuilder::select('*', array('informationsystems.id', 'information_system_id'),
			array('informationsystems.name', 'information_system_name'),
			array('informationsystems.description', 'information_system_description'))
			->from('informationsystems')
			->join('informationsystem_items', 'informationsystems.id', '=', 'informationsystem_id')
			->where('informationsystem_items.id', '=', $information_item_id)
			->where('informationsystems.deleted', '=', 0)
			->where('informationsystem_items.deleted', '=', 0);

			// Если только активные (без неактивных)
			if (in_array('active', $property['show_item_type']) && !in_array('inactive', $property['show_item_type']))
			{
				$queryBuilder->where('informationsystem_items.active', '=', 1);
			}
			// только неактивные
			elseif (in_array('inactive', $property['show_item_type']) && !in_array('active', $property['show_item_type']))
			{
				$queryBuilder->where('informationsystem_items.active', '=', 0);
			}

			// Если не содержит putend_date - ограничиваем по дате окончания публикации
			if (!in_array('putend_date', $property['show_item_type']))
			{
				$queryBuilder
					->open()
					->where('informationsystem_items.end_datetime', '>=', $current_date)
					->setOr()
					->where('informationsystem_items.end_datetime', '=', '0000-00-00 00:00:00')
					->close();
			}

			// если не содержит putoff_date - ограничиваем по дате начала публикации
			if (!in_array('putoff_date', $property['show_item_type']))
			{
				$queryBuilder->where('informationsystem_items.start_datetime', '<=', $current_date);
			}

			$queryBuilder->where('informationsystem_items.siteuser_group_id', 'IN', $mas_result);
			$row = $queryBuilder->execute()->asAssoc()->current();

			if ($row)
			{
				$xmlData .= '<information_system id="' . $row['informationsystem_id'] . '">' . "\n";

				$xmlData.= '<name>' . str_for_xml($row['information_system_name']) . '</name>' . "\n";

				$structure = & singleton('Structure');
				$url = $structure->GetStructurePath($row['structure_id'], 0);

				if ($url != '/')
				{
					$url = '/' . $url;
				}
				$xmlData .= '<url>' . str_for_xml($url) . '</url>' . "\n";
				$xmlData .= '<description>' . str_for_xml($row['information_system_description']) . '</description>' . "\n";
				$xmlData .= '<captcha_used>' . intval($row['use_captcha']) . '</captcha_used>' . "\n";

				$Captcha = new Captcha();
				$xmlData .= '<captcha_key>' . ((intval($row['use_captcha']) == 1)
				? $Captcha->GetCaptchaID()
				: 0) . '</captcha_key>' . "\n";

				$xmlData .= '<parent_group_id>' . $row['informationsystem_group_id'] . '</parent_group_id>' . "\n";

				if (class_exists('Sns'))
				{
					$Sns = & singleton('Sns');

					// Получаем информацию о блогах, связанных с информационной системой
					$sns_row = $Sns->GetSnsAssociatedInformationSystem($row['information_system_id']);

					if ($sns_row)
					{
						$param_sns_xml['sns_id'] = $sns_row['sns_id'];
						$param_sns_xml['sns_row'] = $sns_row;

						// Не добавлять данные об инфосистеме в XML
						$param_sns_xml['show_information_system_xml'] = FALSE;

						$xmlData .= $Sns->GenXml4Sns($param_sns_xml);
					}
				}

				$xmlData .= '</information_system>' . "\n";

				// Используется для вывода всех свойств для текущей группы
				$property['current_group_id'] = $row_item['information_groups_id'];

				// Если сказано отображать группы
				if ($property['xml_show_group_type'] != 'none')
				{
					$this->mas_information_groups_for_xml = array();

					// Кэш заполняем только при выборе всех групп
					if ($property['xml_show_group_type'] == 'all')
					{
						$this->FillMasGroup($row['information_system_id']);
					}

					// Формируем XML-данные для групп дополнительных свойств элеметов
					if (!isset($this->cache_propertys_items_dir_tree[$row['information_system_id']]))
					{
						$dir_prop_array = $this->GetAllPropertysItemsDirForInformationSystem($row['information_system_id']);

						$this->cache_propertys_items_dir_tree[$row['information_system_id']] = array();

						if ($dir_prop_array && mysql_num_rows($dir_prop_array) > 0)
						{
							while ($dir_prop_row = mysql_fetch_assoc($dir_prop_array))
							{
								$this->cache_propertys_items_dir[$dir_prop_row['information_propertys_items_dir_id']] = $dir_prop_row;
								$this->cache_propertys_items_dir_tree[$row['information_system_id']][$dir_prop_row['information_propertys_items_dir_parent_id']][] = $dir_prop_row['information_propertys_items_dir_id'];
							}
						}
					}

					$this->buffer = '';
					// Вызов функции генерацци XML для групп дополнительных свойств
					$this->GenXmlForItemsPropertyDir($row['information_system_id']);
					$xmlData .= $this->buffer;
					$this->buffer = '';

					// передавать свойства в информационную группу
					if ($property['xml_show_group_property'])
					{
						// Формируем XML-данные для групп дополнительных свойств групп инфоэлементов
						$dir_prop_array = $this->GetAllPropertyGroupsDirForInformationSystem($row['information_system_id']);

						if (!isset($this->cache_propertys_groups_dir_tree[$row['information_system_id']]))
						{
							$this->cache_propertys_groups_dir_tree[$row['information_system_id']] = array();

							if ($dir_prop_array && mysql_num_rows($dir_prop_array) > 0)
							{
								while ($dir_prop_row = mysql_fetch_assoc($dir_prop_array))
								{
									$this->cache_propertys_groups_dir[$dir_prop_row['information_propertys_groups_dir_id']] = $dir_prop_row;
									$this->cache_propertys_groups_dir_tree[$row['information_system_id']][$dir_prop_row['information_propertys_groups_dir_parent_id']][] = $dir_prop_row['information_propertys_groups_dir_id'];
								}
							}
						}

						$this->buffer = '';
						// Вызов функции генерацци XML для групп дополнительных свойств
						$this->GenXmlForGroupsPropertyDir($row['information_system_id']);
						$xmlData .= $this->buffer;
						$this->buffer = '';

						// Заполняем значения свойств всех групп информационной системы
						$this->FillMemCachePropertysGroup($row_item['information_systems_id'], $property['xml_show_group_property_id']);
					}

					if ($property['xml_show_group_type'] == 'current')
					{
						// Если в XML передаются только текущие группы - дерево групп строим от текущей группы, а не от корня.
						$property['groups_parent_id'] = $row_item['information_groups_id'];
					}

					// Если модуль кэширования подключен
					if (class_exists('Cache') && $property['cache'])
					{
						$kernel = & singleton('kernel');

						$cache_element_name_xml = $row_item['information_systems_id'] . "_" . $kernel->implode_array($property, '_');

						$cache = & singleton('Cache');

						$cache_name_xml = 'INF_SYS_GEN_GROUP_XML_TREE';

						if (($in_cache = $cache->GetCacheContent($cache_element_name_xml, $cache_name_xml)) && $in_cache)
						{
							$GroupXmlTree = $in_cache['value'];
						}
					}

					// В кэше данных не было
					if (!isset($GroupXmlTree))
					{
						$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group', $row_item['information_groups_id']);

						$aGroupsParentsId = array(0);

						do {
							$aGroupsParentsId[] = $oInformationsystem_Group->id;
						}
						while($oInformationsystem_Group = $oInformationsystem_Group->getParent());

						foreach ($aGroupsParentsId as $iGroupParentId)
						{
							Core_Entity::factory('Informationsystem', $row_item['information_systems_id'])
								->Informationsystem_Groups->getByParentId($iGroupParentId);
						}

						// Полное дерево всех групп информационной системы
						$GroupXmlTree = $this->GenGroupXmlTree($row_item['information_systems_id'], $property);

						// Если модуль кэширования
						if (class_exists('Cache') && $property['cache'])
						{
							$cache->Insert($cache_element_name_xml, $GroupXmlTree, $cache_name_xml);
						}
					}

					$xmlData .= $GroupXmlTree;
				}

				if (!isset($property['show_text']))
				{
					$property['show_text'] = TRUE;
				}

				$xmlData.= $this->GetXmlForInformationItem($information_item_id, $property);
			}

			$xmlData .= '</document>' . "\n";

			$xsl = & singleton('xsl');
			$result = $xsl->build($xmlData, $xsl_name);

			// Проверяем, установлен ли модуль кэширования
			if (class_exists('Cache') && $property['cache'])
			{
				$cache->Insert($cache_element_name, $result, $cache_name);
			}

			echo $result;

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Формирование пути к данному информационному элементу
	 *
	 * @param int $information_items_id идентификатор информационного элемента
	 * @param string $path параметр для хранения пути к данному информационному элементу
	 * @param array $row ассоциативный массив, содержащий информацию об информационном элементе
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_items_id= 1;
	 * $path = '';
	 *
	 * $path = $InformationSystem->GetPathItem($information_items_id, $path);
	 *
	 * // Распечатаем результат
	 * echo $path;
	 * ?>
	 * </code>
	 * @return string путь к информационному элементу относительно информационной системы.
	 * Путь к информационной системе в результирующую строку не входит.
	 */
	function GetPathItem($information_items_id, $path, $row = '', $param = array())
	{
		$information_items_id = intval($information_items_id);

		// если не переданы данные из строки
		if (!is_array($row))
		{
			$row = $this->GetInformationSystemItem($information_items_id, $param);

			if (!$row)
			{
				return FALSE;
			}
		}

		if (trim($row['information_items_url']) == '')
		{
			$information_items_url = $information_items_id;
		}
		else
		{
			$information_items_url = rawurlencode($row['information_items_url']);
		}

		$path = $this->GetPathGroup($row['information_groups_id'], $path, $param) . $information_items_url . '/';

		return $path;
	}

	/**
	 * Формирование массива групп для информационной системы (MasGroup)
	 * и массива дерева идентификаторов групп (FullCacheGoupsIdTree).
	 * При вызове метода FillMasGroup(), метод FillMemFullCacheGoupsIdTree() вызывать не следует,
	 * т.к. его функционал выполняется FillMasGroup
	 *
	 * @param int $InformationSystem_id идентификатор информационной системы, для которой заполняем массив групп самого верхнего уровня
	 *
	 * @param $param массив дополнительных параметров
	 * @see  GetAllInformationGroups()
	 */
	function FillMasGroup($InformationSystem_id = FALSE, $param = array())
	{
		/*if (isset($param['current_group_id'])
		 && isset($param['xml_show_group_type'])
		 && $param['xml_show_group_type'] != 'all')
		 {
			$information_groups_id = intval($param['current_group_id']);
			$param = array();
			}
			else
			{
			$information_groups_id = -1;
			}*/

		if ($InformationSystem_id !== FALSE)
		{
			$InformationSystem_id = intval($InformationSystem_id);
		}

		$count_param = count($param);

		// Очищаем текущий массив
		// $this->MasGroup=array();

		// Массив $this->FullCacheGoupsIdTree заполняем только тогда, когда условий для выборки не было передано
		if ($count_param == 0)
		{
			// Кэш уже заполнен
			if (isset($this->FullCacheGoupsIdTree[$InformationSystem_id]))
			{
				return ;
			}

			$this->FullCacheGoupsIdTree[$InformationSystem_id] = array();
		}
		else // Иначе дерево формируем в $this->CacheGoupsIdTree
		{
			$this->CacheGoupsIdTree[$InformationSystem_id] = array();
		}

		$param['information_system_id'] = $InformationSystem_id;

		$param['select_fields'] = array('id', 'parent_id');

		$mas_groups = $this->GetAllInformationGroups($param);

		foreach ($mas_groups as $key => $row)
		{
			//$this->MasGroup[$row['id']] = $row;

			// Массив $this->FullCacheGoupsIdTree заполняем только тогда, когда условий для выборки не было передано
			if ($count_param == 0)
			{
				$this->FullCacheGoupsIdTree[$InformationSystem_id][$row['parent_id']][] = $row['id'];
			}
			else // Иначе дерево формируем в $this->CacheGoupsIdTree
			{
				$this->CacheGoupsIdTree[$InformationSystem_id][$row['parent_id']][] = $row['id'];
			}
		}
	}

	/**
	 * Формирование пути по информационным группам
	 *
	 * @param int $information_groups_id идентификатор информационной группы, для которой надо сформировать путь
	 * @param string $path служебный параметр, используемый при формировании пути по группам
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_id = 2;
	 *
	 * $newpath = $InformationSystem->GetPathGroup($information_groups_id);
	 *
	 * // Распечатаем результат
	 * echo $newpath;
	 * ?>
	 * </code>
	 * @return string путь по группам от корневой до данной или FALSE, если путь для группы не удается определить
	 */
	function GetPathGroup($information_groups_id, $path = '', $param = array())
	{
		$information_groups_id = intval($information_groups_id);

		if (!$information_groups_id)
		{
			return $path;
		}

		$row = $this->GetInformationGroup($information_groups_id, $param);

		if ($row)
		{
			$path = rawurlencode($row['information_groups_path']) . '/' . $path;

			// Для исключения зацикливания рекурсии
			if ($information_groups_id != $row['information_groups_parent_id'])
			{
				return $this->GetPathGroup($row['information_groups_parent_id'], $path, $param);
			}
		}

		return FALSE;
	}

	/**
	 * Определение идентификатора информационной группы и идентификатора информационного элемента по значению URI
	 *
	 * @param int $InformationSystem_id идентификатор информационной системы, к которой принадлежит данный информационный элемент
	 * @param array $path_array массив, содержащий все элементы URL
	 * @param bool $break_if_path_not_found прерывает поиск пути, если очередной элемент не был найден, по умолчанию true
	 * @param array $param массив дополнительных параметров
	 * - array $param['information_groups_activity'] массив параметров активности информационной группы, по умолчанию только активные
	 * - array $param['information_items_status'] массив параметров активности информационного элемента, по умолчанию только активные
	 *
	 * @return mixed ассоциативный массив, содержащий идентификатор информационной группы и путь к информационному элементу в случае успешного выполнения, FALSE в противном случае
	 */
	function GetInformationFromPath($InformationSystem_id, $path_array = '', $break_if_path_not_found = TRUE, $param = array())
	{
		$InformationSystem_id = intval($InformationSystem_id);
		$aInformationGroupsActivity = Core_Type_Conversion::toArray($param['information_groups_activity']);
		$aInformationItemsStatus = Core_Type_Conversion::toArray($param['information_items_status']);

		$param = array();

		if (is_array($path_array))
		{
			$param = $path_array;
		}
		else
		{
			$param = Core_Type_Conversion::toArray($GLOBALS['URL_ARRAY']);
		}

		$kernel = & singleton('kernel');

		$cache_name = 'INF_SYS_INFORMATION_FROM_PATH';
		$cache_filed_name = $InformationSystem_id . ' ' . $kernel->implode_array($param, '=');

		// Проверка на наличие в файловом кэше
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');

			if ($in_cache = $cache->GetCacheContent($cache_filed_name, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$row = $this->GetInformationSystem($InformationSystem_id);

		// Информационная система с таким id существует
		if ($row !== FALSE)
		{
			// Информацию о пути к инфосистеме получаем по идентификатору узла структуры, с которым связана инфосистема
			$structure = & singleton('Structure');
			$url = $structure->GetStructurePath($row['structure_id'], 0);

			if ($url != '/')
			{
				$url = '/' . $url;
			}

			// проверяем правильно ли задан url информационной системы
			$mas_url = explode('/', trim($url));
			$count_mas_url = count($mas_url);

			$count_param = count($param);

			// корень
			$information_groups_parent_id = 0;

			// Возвращаемый массив по умолчанию
			$return['group'] = $information_groups_parent_id;
			$return['item'] = FALSE;

			// Выясняем точку отсечения
			if ($count_mas_url < 3)
			{
				if ($param[0] == '/')
				{
					$count_mas_url = $count_mas_url - 1; // -1, т.к. при пути на главную, получается / и 2 элемента.
				}
				else
				{
					$count_mas_url = $count_mas_url - 2;
				}
			}
			else // url информационной системы задан правильно
			{
				$count_mas_url = $count_mas_url - 2;
			}

			// получаем значения активности информационных групп для выборки
			if (count($aInformationGroupsActivity) == 0)
			{
				$aInformationGroupsActivity = array(1);
			}
			else
			{
				$aInformationGroupsActivity = Core_Array::toInt($aInformationGroupsActivity);
			}
			// получаем значения активности информационных элементов для выборки
			if (count($aInformationItemsStatus) == 0)
			{
				$aInformationItemsStatus = array(1);
			}
			else
			{
				$aInformationItemsStatus = Core_Array::toInt($aInformationItemsStatus);
			}

			$queryBuilder = Core_QueryBuilder::select('*');

			// Проходимся по $param с целью определения ID группы и элемента
			for($i = $count_mas_url; $i < $count_param; $i++)
			{
				/* Для того, чтобы выводилось, если нет последнего слэша (т.к. в $count_param может
				 быть на один элемент больше*/
				if (empty($param[$i]))
				{
					continue;
				}

				if (class_exists('SiteUsers'))
				{
					$SiteUsers = & singleton('SiteUsers');
					$site_user_id = $SiteUsers->GetCurrentSiteUser();
				}
				else
				{
					$site_user_id = 0;
				}

				// Получаем массив групп пользователий, в которых содержится текущий пользователь сайта
				$mas_result = $this->GetSiteUsersGroupsForUser($site_user_id);

				//$users_groups_access_in = implode(',', $mas_result);

				$queryBuilder
					->clear()
					->from('informationsystem_groups')
					->where('parent_id', '=', $information_groups_parent_id)
					->where('path', '=', $param[$i])
					->where('active', 'IN', $aInformationItemsStatus)
					->where('informationsystem_id', '=', $row['information_systems_id'])
					->where('siteuser_group_id', 'IN', $mas_result)
					->where('deleted', '=', 0)
					->limit(1);

				$oInformationsystemGroup = $queryBuilder->execute()
					->asObject('Informationsystem_Group_Model')
					->current();

				// существует группа c таким названием
				if ($oInformationsystemGroup)
				{
					$aInformationsystemGroup = $this->getArrayInformationsystemGroup($oInformationsystemGroup);

					if ($this->IssetAccessForInformationSystemGroup($site_user_id, $aInformationsystemGroup['information_groups_id'], $InformationSystem_id, $aInformationsystemGroup))
					{
						// Сохраняем ID группы
						$information_groups_parent_id = $aInformationsystemGroup['information_groups_id'];
						$return['group'] = $information_groups_parent_id;
					}
					else
					{
						$return = FALSE;
					}
				}
				else // не существует в данной родительской группе группы с таким названием
				{
					// проверяем является ли данный элемент массива param информационным элементом
					$queryBuilderSecond = Core_QueryBuilder::select('*')
						->from('informationsystem_items')
						->where('informationsystem_group_id', '=', $information_groups_parent_id)
						->where('informationsystem_id', '=', $row['information_systems_id'])
						->where('active', 'IN', $aInformationItemsStatus)
						->open()
						->where('id', '=', $param[$i])
						->where('path', '=', '')
						->setOr()
						->where('path', '=', $param[$i])
						->close()
						->where('siteuser_group_id', 'IN', $mas_result)
						->where('deleted', '=', 0);

					$oInformationsystemItem = $queryBuilderSecond->execute()
						->asObject('Informationsystem_Item_Model')
						->current();

					// элемент массива param является информационным элементом
					if (is_array($return) && !$return['item'] && $oInformationsystemItem)
					{
						$aInformationsystemItem = $this->getArrayInformationsystemItem($oInformationsystemItem);

						if ($this->GetAccessItem($site_user_id, $aInformationsystemItem['information_items_id'], $aInformationsystemItem))
						{
							$return['group'] = $aInformationsystemItem['information_groups_id'];

							// Метод возвращает ID или имя в URL, если оно задано.
							if (trim($aInformationsystemItem['information_items_url']) == '')
							{
								$return['item'] = $aInformationsystemItem['information_items_id'];
							}
							else
							{
								$return['item'] = $aInformationsystemItem['information_items_url'];
							}
						}
						else
						{
							$return = FALSE;
						}
						//break;
						// Продолжаем обработку, чтобы проверить на 404 ошибку
					}
					else
					{
						// Проверяем наличие тегов, указанных в $this->PathArrayGetInformationFromPath
						if (in_array($param[$i], $this->PathArrayGetInformationFromPath))
						{
							$return[$param[$i]] = TRUE;
							continue;
						}

						// Это и не элемент, и не группа, возвращаем ID предыдущей группы
						if (preg_match("/^page-([0-9]*)$/", $param[$i], $regs) && Core_Type_Conversion::toInt($regs[1]) > 0
						|| !$break_if_path_not_found)
						{
							// переход на страницу, сохраняем группу
							break;
						}

						// теги
						if ($param[$i] == 'tag' && class_exists('Tag'))
						{
							$oTag = & singleton ('Tag');
							$tag_name = Core_Type_Conversion::toStr($param[$i + 1]);

							if ($oTag->GetTagByPath($tag_name))
							{
								// Сохраним тег
								$return['tag_name'] = $tag_name;

								// Это вывод тегов - прерываем и сохраним в результате сам тег
								break;
							}
							else
							{
								// Тег отсутствует - возвращаем ложь
								$return = FALSE;
								break;
							}
						}

						// Это не элемент и не группа, возвращаем FALSE, чтобы вывести 404
						$return = FALSE;
						break;
					}
				}
			}
		}
		else
		{
			$return = FALSE;
		}

		// Запись в файловый кэш
		if (class_exists('Cache'))
		{
			$cache->Insert($cache_filed_name, $return, $cache_name);
		}

		return $return;
	}

	/**
	 * Устаревший метод. Оставлен для совместимости, используйте GetInformationSystemItem
	 *
	 * @param int $information_items_id
	 * @access private
	 */
	function GetInformationBlockItems($information_items_id)
	{
		return $this->GetInformationSystemItem($information_items_id);
	}

	/**
	 * Получение данных об информационном элементе
	 *
	 * @param int $information_items_id идентификатор информационного элемента
	 * @param array массив дополнительных параметров
	 * - $param['cache_off'] использовать кэш, по умолчанию не объявлен
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_items_id = 1;
	 *
	 * $row = $InformationSystem->GetInformationSystemItem($information_items_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed ассоциативный массив с данными об информационном элементе или ложь, если элемент не найден
	 */
	function GetInformationSystemItem($information_items_id, $param = array())
	{
		$information_items_id = intval($information_items_id);

		// Если есть в кэше - возвращаем из кэша
		if (isset($this->ItemMass[$information_items_id]) && !isset($param['cache_off']))
		{
			return $this->ItemMass[$information_items_id];
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'INF_SYS_ITEM';

			if ($in_cache = $cache->GetCacheContent($information_items_id, $cache_name))
			{
				$this->ItemMass[$information_items_id] = $in_cache['value'];

				return $in_cache['value'];
			}
		}

		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($information_items_id);

		if (!is_null($oInformationsystem_Item->id))
		{
			$aInformationsystem_Item = $this->getArrayInformationsystemItem($oInformationsystem_Item);
			if (!isset($param['cache_off']))
			{
				// Записываем в кэш
				$this->ItemMass[$information_items_id] = $aInformationsystem_Item;
			}
		}
		else
		{
			$aInformationsystem_Item = FALSE;
		}

		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache->Insert($information_items_id, $aInformationsystem_Item, $cache_name);
		}

		return $aInformationsystem_Item;
	}

	/**
	 * Добавление комментария к элементу информационной системы
	 *
	 * @param string $xslname имя XSL шаблона для отображения информации о добавлении комментария
	 * @param array $param массив дополнительных параметров
	 * - $param['information_items_id'] идентификатор информационной системы, для которой производится добавление комментария
	 * - $param['comment_parent_id'] идентификатор родительского комментария
	 * - $param['comment_autor'] автор комментария (передается, если не указан ID пользователя сайта)
	 * - $param['comment_email'] эл. почта комментирующего (передается, если не указан ID пользователя сайта)
	 * - $param['comment_phone'] телефон
	 * - $param['comment_subject'] тема комментария (передается, если не указан ID пользователя сайта)
	 * - $param['comment_text'] текст комментария
	 * - $param['comment_grade'] оценка комментария
	 * - $param['comment_phone'] номер телефона автора комментария
	 * - $param['allowable_tags'] список доступных тегов, например
	 * - $param['report_subject'] тема отчета о добавлении комментария администратору '<b><strong><i>'
	 * - $param['confirm_comment'] определяет имеет ли пользователь право на добавления комментария (true - имеет право, FALSE - не имеет право)
	 * - $param['status'] статус сообщения, добавленного пользователем (0 - недоступно посетителям, 1 - доступно посетителям сразу после публикации)
	 * - $param['admin_email_xsl'] наименование XSL-шаблона для отправки информации о комментарии администратору
	 * - $param['comment_mail_type'] формат письма с уведомлением о добавлении комментария (0 - текст, 1 - HTML)
	 * @return  boollean FALSE в случае отсутствия данного информационного элемента
	 */
	function ShowAddComment($xslname, $param = array('confirm_comment' => FALSE, 'status' => 0, 'comment_mail_type' => 1))
	{
		// Определяем группы доступа для текущего авторизированного пользователя
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		if (!isset($param['allowable_tags']))
		{
			$param['allowable_tags'] = '';
		}

		if (!isset($param['confirm_comment']))
		{
			$param['confirm_comment'] = FALSE;
		}

		$param['status'] = Core_Type_Conversion::toInt($param['status']);

		if (!isset($param['information_items_id']))
		{
			$information_items_id = Core_Type_Conversion::toInt($_POST['information_items_id']);
		}
		else
		{
			$information_items_id = Core_Type_Conversion::toInt($param['information_items_id']);
		}

		if (!isset($param['comment_parent_id']))
		{
			$comment_parent_id = Core_Type_Conversion::toInt($_POST['comment_parent_id']);
		}
		else
		{
			$comment_parent_id = Core_Type_Conversion::toInt($param['comment_parent_id']);
		}

		//
		if (!isset($param['comment_autor']))
		{
			$comment_autor = strip_tags(Core_Type_Conversion::toStr($_POST['comment_autor']));
		}
		else
		{
			$comment_autor = strip_tags(Core_Type_Conversion::toStr($param['comment_autor']));
		}

		if (!isset($param['comment_email']))
		{
			$comment_email = EMAIL_TO;
		}
		else
		{
			$comment_email = strip_tags(Core_Type_Conversion::toStr($param['comment_email']));
		}

		if (!isset($param['comment_phone']))
		{
			$comment_phone = strip_tags(Core_Type_Conversion::toStr($_POST['comment_phone']));
		}
		else
		{
			$comment_phone = strip_tags($param['comment_phone']);
		}

		if (!isset($param['comment_subject']))
		{
			$comment_subject = strip_tags(Core_Type_Conversion::toStr($_POST['comment_subject']));
		}
		else
		{
			$comment_subject = strip_tags($param['comment_subject']);
		}

		if (!isset($param['comment_text']))
		{
			$comment_text = strip_tags_attributes(Core_Type_Conversion::toStr($_POST['comment_text']), $param['allowable_tags']);
		}
		else
		{
			$comment_text = strip_tags_attributes(Core_Type_Conversion::toStr($param['comment_text']), $param['allowable_tags']);
		}

		if (!isset($param['comment_grade']))
		{
			$comment_grade = Core_Type_Conversion::toInt($_POST['comment_grade']);
		}
		else
		{
			$comment_grade = Core_Type_Conversion::toInt($param['comment_grade']);
		}

		if (!isset($param['comment_mail_type']))
		{
			// 1 - HTML
			$param['comment_mail_type'] = 1;
		}
		else
		{
			if (Core_Type_Conversion::toInt($param['comment_mail_type']) != 0 && Core_Type_Conversion::toInt($param['comment_mail_type']) != 1)
			{
				$param['comment_mail_type'] = 1;
			}
			else
			{
				$param['comment_mail_type'] = intval($param['comment_mail_type']);
			}
		}

		// XSL-шаблон для отправки письма администратору
		if (!isset($param['admin_email_xsl']) && defined('ADMIN_EMAIL_XSL'))
		{
			$param['admin_email_xsl'] = ADMIN_EMAIL_XSL;
		}
		elseif (!isset($param['admin_email_xsl']))
		{
			$param['admin_email_xsl'] = FALSE;
		}

		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($information_items_id);

		if (is_null($oInformationsystem_Item->id))
		{
			show_error_message('Ошибка! Информационный элемент, для которого осуществляется добавление записи, не найден!');
			return FALSE;
		}

		$result_return = FALSE;

		$Captcha = new Captcha();

		$row = $this->getArrayInformationsystemItem($oInformationsystem_Item);

		$row_infsys = $this->GetInformationSystem($oInformationsystem_Item->informationsystem_id);

		if ($row_infsys)
		{
			$forms_captcha_used = $row_infsys['information_systems_captcha_used'];

			$xmlData = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$xmlData .= '<document>'."\n";

			$xmlData .= '<information_system id="' . str_for_xml($row_infsys['information_systems_id']) . '">' . str_for_xml($row_infsys['information_systems_name']) . '</information_system>' . "\n";

			// Тип добавляемого комментария. 1 - опубликован сразу. 0 - опубликован после проверки.
			$xmlData .= '<type_add_comment>' . $param['status'] . '</type_add_comment>' . "\n";

			$xmlData .= '<comment>' . "\n";
			$xmlData .= '<comment_autor>' . str_for_xml($comment_autor) . '</comment_autor>' . "\n";
			$xmlData .= '<comment_email>' . str_for_xml($comment_email) . '</comment_email>' . "\n";
			$xmlData .= '<comment_phone>' . str_for_xml($comment_phone) . '</comment_phone>' . "\n";
			$xmlData .= '<comment_text>' . str_for_xml($comment_text) . '</comment_text>' . "\n";
			$xmlData .= '<comment_subject>' . str_for_xml($comment_subject) . '</comment_subject>' . "\n";

			// дата добавления комментария
			$date = time();

			$comment_ip = Core_Type_Conversion::toStr($_SERVER['REMOTE_ADDR']);

			$xmlData .= '<comment_date>' . str_for_xml(strftime(Core_Type_Conversion::toStr($row_infsys['information_systems_format_date']), $date)) . '</comment_date>' . "\n";
			$xmlData .= '<comment_datetime>' . str_for_xml(strftime(Core_Type_Conversion::toStr($row_infsys['information_systems_format_datetime']), $date)) . '</comment_datetime>' . "\n";

			$xmlData .= '<comment_ip>' . $comment_ip . '</comment_ip>' . "\n";
			$xmlData .= '<comment_information_block_name>' . str_for_xml($row_infsys['information_systems_name']) . '</comment_information_block_name>' . "\n";
			$xmlData .= '<comment_information_block_id>' .  intval($row['information_systems_id']) . '</comment_information_block_id>' . "\n";

			$xmlData .= '<comment_information_group_id>' . intval($row['information_groups_id']).'</comment_information_group_id>' . "\n";

			$xmlData .= '<comment_information_items_name>' . str_for_xml($row['information_items_name']) . '</comment_information_items_name>' . "\n";

			$xmlData .= '<comment_information_items_id>' . intval($row['information_items_id']) . '</comment_information_items_id>' . "\n";

			$site = & singleton('site');
			$alias_name = $site->GetCurrentAlias(CURRENT_SITE);

			$xmlData .= '<comment_domain_name>' . $alias_name . '</comment_domain_name>' . "\n";

			// Оставляем XML незакрытым, чтобы была возможность выдать сообщение об ошибке
			$xsl = & singleton('xsl');

			if ($forms_captcha_used == 0
			|| ($forms_captcha_used == 1
			&& $Captcha->ValidCaptcha(Core_Type_Conversion::toStr($_POST['captcha_key']), Core_Type_Conversion::toStr($_POST['captcha_keystring'])))
			|| $site_user_id > 0)
			{
				//определяем имеет ли право пользователь добавлять комментарии
				if ($this->UserCanAddComment($date, $comment_ip) || $param['confirm_comment'])
				{
					$param_comment = array();
					$param_comment['information_items_id'] = $information_items_id;
					$param_comment['comment_parent_id'] = $comment_parent_id;
					$param_comment['comment_fio'] = $comment_autor;
					$param_comment['comment_email'] = $comment_email;
					$param_comment['comment_phone'] = $comment_phone;
					$param_comment['comment_text'] = $comment_text;
					$param_comment['comment_status'] = $param['status'];
					$param_comment['comment_subject'] = $comment_subject;
					$param_comment['comment_ip'] = $comment_ip;
					$param_comment['comment_date'] = date('Y-m-d H:i:s', $date);
					$param_comment['comment_grade'] = $comment_grade;
					$param_comment['site_users_id'] = $site_user_id;
					$comment_id = $this->AddCommentWithoutStriptags($param_comment);

					$xmlData .= '<comment_id>' . $comment_id . '</comment_id>'."\n";
					$xmlData .= '</comment>'."\n";

					// формируем в xsl, заданном в константе ADMIN_EMAIL_XSL, текст письма администратору системы управления о добавлении комментария на сайте
					if ($param['admin_email_xsl'])
					{
						// Закрываем XML, чтобы отправить письмо администратору
						$message = $xsl->build($xmlData . '</document>', $param['admin_email_xsl']);

						// Формат письма - текст
						if ($param['comment_mail_type'] == 0)
						{
							$comment_mail_type = 'text/plain';
						}
						else
						{
							$comment_mail_type = 'text/html';
						}

						$subject = trim(Core_Type_Conversion::toStr($param['report_subject']));

						if ($subject == '')
						{
							$subject = Core::_('Informationsystem.comment_mail_subject');
						}

						$kernel = & singleton('kernel');
						$kernel->SendMailWithFile(EMAIL_TO, $comment_email, $subject, $message, array(), $comment_mail_type);
					}
				}
				else
				{
					$xmlData .= '</comment>'."\n";
					// Ошибка! С момента добавления Вами последнего комментария/ответа прошло слишком мало времени!';
					$xmlData .= '<is_error_time>1</is_error_time>' . "\n";
					$result_return = FALSE;
				}
			}
			else
			{
				$xmlData .= '</comment>'."\n";
				// Ошибка! Вы неверно ввели число подтврждения отправки комментария!';
				$xmlData .= '<is_error_capthca>1</is_error_capthca>' . "\n";
				$result_return = FALSE;
			}

			$xmlData .= '</document>' . "\n";
			echo $xsl->build($xmlData, $xslname);

			$result_return = TRUE;
		}

		return $result_return;
	}

	/**
	 * Устаревший метод получения расширения файла.
	 * Необходимо использовать $kernel->GetExtension()
	 *
	 * @param string $fname имя файла (путь к файлу)
	 * @return string расширение файла
	 * @access private
	 */
	function GetExpansion($fname)
	{
		return Core_Array::getExtension($fname);
	}

	/**
	 * Функция обратного вызова, используется модулем поисковой системы при выводе результатов поиска
	 *
	 * @param array $row массив с информацией о странице
	 * @return string дополнительный XML, включаемый в результат поиска
	 */
	function _CallbackSearch($row)
	{
		$xml = '';

		if (isset($row['search_page_module_value_type']) && isset($row['search_page_module_value_id']))
		{
			if (in_array($row['search_page_module_value_type'], array(1, 2)))
			{
				// Информационые элементы
				if ($row['search_page_module_value_type'] == 2)
				{
					$xml = $this->GetXmlForInformationItem($row['search_page_module_value_id']);

					// Получаем информацию об ИЭ
					$item_row = $this->GetInformationSystemItem($row['search_page_module_value_id']);

					// Идентификатор группы
					$group_id = Core_Type_Conversion::toInt($item_row['information_groups_id']);
				}
				else
				{
					// Идентификатор группы
					$group_id =	$row['search_page_module_value_id'];
				}

				$group_info = $this->GetInformationGroup($group_id);

				if ($group_info)
				{
					$xml .= '<group id="' . $group_info['information_groups_id'] . '" parent_id="' . $group_info['information_groups_parent_id'] . '">' . "\n";
					// Генерируем XML для группы
					$xml .= $this->GenXmlForGroup($row['search_page_module_entity_id'], $group_info, array());
					$xml .= '</group>' . "\n";
				}
			}
		}

		return $xml;
	}

	/**
	 * Устаревший метод, определяющий может ли пользователь добавлять комментарий на оснвании его предыдущих комментариев и разницы во времени. Время между комментариями задается с помощью ADD_COMMENT_DELAY
	 *
	 * @param string $date дата и время (в Unix-формате) добавления комментария
	 * @param string $comment_ipip-адрес компьютера пользователя, добавляющего комментарий
	 * @return boolean - true - пользователь может отправлять комментарий, FALSE - не может отправлять комментарий
	 * @access private
	 */
	function confirm_comment($date, $comment_ip)
	{
		return $this->UserCanAddComment($date, $comment_ip);
	}

	/**
	 * Определение возможности пользователя добавлять комментарий на оснвании его предыдущих комментариев и разницы во времени.
	 * Время между комментариями задается с помощью ADD_COMMENT_DELAY
	 *
	 * @param string $date дата и время (в Unix-формате) добавления комментария
	 * @param string $comment_ip ip-адрес компьютера пользователя, добавляющего комментарий
	 * @param array $param массив параметров
	 * - $param['information_system_id'] идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $date = time();
	 * $ip = '192.169.0.4';
	 *
	 * $result = $InformationSystem->UserCanAddComment($date, $ip);
	 *
	 * // Распечатаем результат
	 * if ($result)
	 * {
	 * 	echo 'Пользователь может добавить комментарий';
	 * }
	 * else
	 * {
	 * 	echo 'Пользователь не может добавить комментарий';
	 * }
	 * ?>
	 * </code>
	 * @return boolean true - пользователь может отправлять комментарий, FALSE - не может отправлять комментарий
	 */
	function UserCanAddComment($date, $ip, $param = array())
	{
		$oComment = Core_Entity::factory('Comment')->getLastCommentByIp($ip);

		// Пользователь оставлял сообщения
		if (!is_null($oComment))
		{
			// Дата и время добавления последнего комментария пользователя
			$date_last_message = $oComment->datetime;

			// Определяем дату следующего возможного добавления комментария
			// Массив даты и времени
			$date_next_message = getdate(strtotime($date_last_message));

			// Метка времени даты следующего добавления сообщения
			$date_next_message = $date_next_message[0] + ADD_COMMENT_DELAY;

			// Если время добавления комментария меньше допустимого для следующего добавления
			if ($date < $date_next_message)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}

		// Пользователь не оставлял комментариев - имеет право для добавления
		return TRUE;
	}

	/**
	 * Определение возможности пользователя добавлять информационный элемент
	 *
	 * @param string $date дата и время (в Unix-формате) добавления информационного элемента
	 * @param string $information_item_ip ip-адрес компьютера пользователя, добавляющего информационный элемент
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $date = time();
	 * $information_item_ip = '192.169.0.4';
	 *
	 * $result = $InformationSystem->confirm_information_item($date, $information_item_ip);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int 0 – пользователь не может добавлять информационный элемент, 1- может добавлять информационный элемент
	 */
	function confirm_information_item($date, $information_item_ip)
	{
		// определяем оставлял ли данный пользователь сообщения в гостевой книге
		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item');
		$oInformationsystem_Item
			->queryBuilder()
			->clear()
			->where('ip', '=', $information_item_ip)
			->orderBy('datetime', 'DESC')
			->limit(1);

		$aInformationsystem_Items = $oInformationsystem_Item->findAll();

		if (isset($aInformationsystem_Items[0]))
		{
			$oInformationsystem_Item = $aInformationsystem_Items[0];
			$row = $this->getArrayInformationsystemItem($oInformationsystem_Item);

			// дата и время добавления последнего информационного элемента пользователю
			$date_last_message = $row['information_items_date'];

			// определяем дату следующего возможного добавления комментария
			$date_next_message = getdate(strtotime($date_last_message));

			// метка времени даты следующего добавления сообщения
			$date_next_message = $date_next_message[0] + ADD_COMMENT_DELAY;

			// если время добавления информационного элемента меньше допустимого для следующего добавления
			if ($date < $date_next_message)
			{
				return 0;
			}
			else
			{
				return 1;
			}
		}

		return 1;
	}

	/**
	 * Копирование информационного элемента
	 *
	 * @param int $information_item_id идентификатор информационного элемента, который необходимо скопировать
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_item_id = 1;
	 *
	 * $InformationSystem->CopyInformationItem($information_item_id);
	 *
	 * ?>
	 * </code>
	 */
	function CopyInformationItem($information_item_id)
	{
		$information_item_id = intval($information_item_id);

		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item', $information_item_id);

		$oNew_Informationsystem_Item = $oInformationsystem_Item->copy();

		// Добавляем индексирование информационного элемента
		if ($oNew_Informationsystem_Item->indexing == 1 && class_exists('Search'))
		{
			$result = $this->IndexationInfItems(0, 1, $oNew_Informationsystem_Item->id);

			if (count($result) != 0)
			{
				$Search = & singleton('Search');
				$line = each($result);
				if (isset($line['value'][1]))
				{
					$Search->Insert_search_word($result);
				}
			}
		}
	}

	/**
	 * Проверка наличия элемента с конкретным URL среди информационных элементов данной группы
	 *
	 * @param int $information_groups_id идентификатор информационной группы, среди элементов которой проводится проверка
	 * @param int $InformationSystem_id идентификатор информационной системы, среди элементов которой проводится проверка
	 * @param string $information_items_url URL информационного элемента, для которого ищем совпадения среди URL других элементов данной информационной группы
	 * @param int $information_items_id идентификатор редактируемого информационного элемента, при добавлении $information_items_id = 0
	 * @param int $edit_mode параметр, определяющий  режим вставки или добавления элемента (0 – вставка, 1- редактирование)
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_id = 2;
	 * $InformationSystem_id = 1;
	 * $information_items_url = 'news1';
	 *
	 * $result = $InformationSystem->IssetItem($information_groups_id, $InformationSystem_id, $information_items_url);
	 *
	 * // Распечатаем результат
	 * if ($result)
	 * {
	 * 	echo 'В данной информационной группе есть элемент с таким URL';
	 * }
	 * else
	 * {
	 * 	echo 'В данной информационной группе отсутствует элемент с таким URL';
	 * }
	 * ?>
	 * </code>
	 * @return boolean true – в информационной группе уже существует информационный элемент с таким же URL как и у данного элемента, FALSE – в информационной группе не существует элементов с таким URL
	 */
	function IssetItem($information_groups_id, $InformationSystem_id, $information_items_url, $information_items_id = 0, $edit_mode = 0)
	{
		if ($information_items_url != '')
		{
			$information_groups_id = intval($information_groups_id);
			$InformationSystem_id = intval($InformationSystem_id);

			$oInformationsystem_Item = Core_Entity::factory('Informationsystem', $InformationSystem_id)
				->Informationsystem_Items;

			$oInformationsystem_Item
				->queryBuilder()
				->where('informationsystem_group_id', '=', $information_groups_id);

			$aInformationsystem_Items = $oInformationsystem_Item->findAll();

			foreach($aInformationsystem_Items as $oInformationsystem_Item)
			{
				if ($edit_mode == 1) // редактирование инф. элемента
				{
					if (mb_strtolower($oInformationsystem_Item->path) == mb_strtolower($information_items_url)
					&&	$oInformationsystem_Item->id != $information_items_id)
					{
						// для данной группы уже есть информационный элемент с таким $information_items_url
						return TRUE;
					}
				}
				else
				{
					if (mb_strtolower($oInformationsystem_Item->path) == mb_strtolower($information_items_url))
					{
						// для данной группы уже есть информационный элемент с таким $information_items_url
						return TRUE;
					}
				}
			}
		}
		return TRUE; // для данной группы нет информационных элементов с таким $information_items_url
	}

	/**
	 * Проверка наличия подгруппы с URL, совпадающим с URL информационного элемента относящегося к данной группе, среди подгрупп данной группы. Используется при вставке/обновлении информационного элемента
	 *
	 * @param int $information_groups_id идентификатор информационной группы, в которой осуществляется поиск
	 * @param int $InformationSystem_id идентификатор информационной системы, к которой относится группа
	 * @param string $information_items_url URL информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_id = 2;
	 * $InformationSystem_id = 1;
	 * $information_items_url = 'news1';
	 *
	 * $result = $InformationSystem->IssetGroupItemInsertUpdate($information_groups_id, $InformationSystem_id, $information_items_url);
	 * // Распечатаем результат
	 * if ($result)
	 * {
	 * 	echo 'В данной группе есть подгруппа, URL которой совпадает с URL информационного элемента';
	 * }
	 * else
	 * {
	 * 	echo 'В данной группе отсутствует подгруппа, URL которой совпадает с URL информационного элемента';
	 * }
	 * ?>
	 * </code>
	 * @return boolean true - в данной группе есть подгруппа, URL которой совпадает с URL информационного элемента, относящегося к данной группе. В противном случае возвращает FALSE
	 */
	function IssetGroupItemInsertUpdate($information_groups_id, $InformationSystem_id, $information_items_url)
	{
		if ($information_items_url != '')
		{
			$information_groups_id = intval($information_groups_id);
			$InformationSystem_id = intval($InformationSystem_id);

			$oInformationsystem_Group = Core_Entity::factory('Informationsystem', $InformationSystem_id)
				->Informationsystem_Groups;

			$oInformationsystem_Group
				->queryBuilder()
				->where('parent_id', '=', $information_groups_id);

			$aInformationsystem_Groups = $oInformationsystem_Group->findAll();

			foreach($aInformationsystem_Groups as $oInformationsystem_Group)
			{
				if (mb_strtolower($oInformationsystem_Group->path) == mb_strtolower($information_items_url))
				{
					return TRUE; // в данной группе есть подгруппа с URL, совпадающим с URL инф. элемента
				}
			}
		}
		return FALSE; // в данной группе нет подгрупп с URL, совпадающим с URL инф. элемента
	}

	/**
	 * Проверка наличия среди подгрупп данной группы подгруппы с таким же URL как и у вставляемой/редактируемой группы
	 *
	 * @param int $information_groups_parent_id идентификатор родительской группы
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * @param string $information_groups_path URL вставляемой/обновляемой группы
	 * @param int $edit_mode параметр, указывающий производится вставка или обновление группы (0 –вставка, 1 – обновление)
	 * @param int $information_groups_id идентификатор редактируемой информационной группы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_parent_id = 2;
	 * $InformationSystem_id = 1;
	 * $information_groups_path = 'films2';
	 * $edit_mode = 0;
	 * $information_groups_id = 15;
	 *
	 * $result = $InformationSystem->IssetGroup($information_groups_parent_id, $InformationSystem_id, $information_groups_path, $edit_mode, $information_groups_id);
	 *
	 * // Распечатаем результат
	 * if ($result)
	 * {
	 * 	echo 'В данной группе есть подгруппа, которая имеет такой же URL как и вставляемая/редактируемая подгруппа';
	 * }
	 * else
	 * {
	 * 	echo 'В данной группе отсутствует подгруппа, которая имеет такой же URL как и вставляемая/редактируемая подгруппа';
	 * }
	 * ?>
	 * </code>
	 * @return boolean true – в данной группе есть подгруппа, которая имеет такой же URL как и вставляемая/редактируемая подгруппа. В противном случае возвращает FALSE
	 */
	function IssetGroup($information_groups_parent_id, $InformationSystem_id, $information_groups_path, $edit_mode = 0, $information_groups_id = 0)
	{
		if ($information_groups_path != '')
		{
			$information_groups_parent_id = intval($information_groups_parent_id);
			$InformationSystem_id = intval($InformationSystem_id);

			$oInformationsystem_Group = Core_Entity::factory('Informationsystem', $InformationSystem_id)
				->Informationsystem_Groups;

			$oInformationsystem_Group
				->queryBuilder()
				->where('parent_id', '=', $information_groups_parent_id);

			$aInformationsystem_Groups = $oInformationsystem_Group->findAll();

			foreach($aInformationsystem_Groups as $oInformationsystem_Group)
			{
				// редактирование инф. группы
				if ($edit_mode == 1)
				{
					if (mb_strtolower($oInformationsystem_Group->path) == mb_strtolower($information_groups_path)
					&& $oInformationsystem_Group->id != $information_groups_id)
					{
						// для данной группы уже есть подгруппа с таким $information_groups_url
						return TRUE;
					}
				}
				else // добавление инф. группы
				{
					if (mb_strtolower($oInformationsystem_Group->path) == mb_strtolower($information_groups_path))
					{
						// для данной группы уже есть подгруппа с таким $information_groups_url
						return TRUE;
					}
				}
			}
		}

		return FALSE;
	}

	/**
	 * Проверка наличия среди информационных элементов данной группы такого, у которого URL совпадает с URL добавляемой/редактируемой информационной группы
	 *
	 * @param int $information_groups_parent_id идентификатор родительской группы
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * @param string $information_groups_path URL вставляемой/редактируемой группы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_parent_id = 2;
	 * $InformationSystem_id = 1;
	 * $information_groups_path = 'films1';
	 *
	 * $result = $InformationSystem->IssetItemGroupInsertUpdate($information_groups_parent_id, $InformationSystem_id, $information_groups_path);
	 *
	 * // Распечатаем результат
	 * if ($result)
	 * {
	 * 	echo 'В данной группе есть информационный элемент, URL которого совпадает с URL добавляемой/редактируемой группы';
	 * }
	 * else
	 * {
	 * 	echo 'В данной группе отсутствует информационный элемент, URL которого совпадает с URL добавляемой/редактируемой группы';
	 * }
	 * ?>
	 * </code>
	 * @return boolean true – в данной группе есть информационный элемент, URL которого совпадает с URL добавляемой/редактируемой группы. В противном случае возвращает FALSE
	 */
	function IssetItemGroupInsertUpdate($information_groups_parent_id, $InformationSystem_id, $information_groups_path)
	{
		if ($information_groups_path != '')
		{
			$information_groups_parent_id = intval($information_groups_parent_id);
			$InformationSystem_id = intval($InformationSystem_id);

			$oInformationsystem_Item = Core_Entity::factory('Informationsystem', $InformationSystem_id)
				->Informationsystem_Items;

			$oInformationsystem_Item
				->queryBuilder()
				->where('informationsystem_group_id', '=', $information_groups_parent_id);

			$aInformationsystem_Items = $oInformationsystem_Item->findAll();

			foreach($aInformationsystem_Items as $oInformationsystem_Item)
			{
				if (mb_strtolower($oInformationsystem_Item->path) == mb_strtolower($information_groups_path))
				{
					return TRUE; // в данной группе есть элемент с URL, совпадающим с URL  подгруппы
				}
			}
		}

		return FALSE;
	}

	/**
	 * Отображение RSS ленты
	 *
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * @param mixed $information_groups_id идентификатор информационной группы, если все группы – то FALSE
	 * @param int $items_on_page количество выводимых записей в ленте
	 * @param int $items_begin параметр, определяющий с какой записи начинать вывод
	 * @param array $property массив дополнительных параметров
	 * - $property['title'] string Заголовок канала
	 * - $property['description'] string Краткое описание RSS-канала
	 * - $property['link'] string Ссылка на сайт
	 * - $property['image'] Картинка для представления канала (необязательный элемент)
	 * - $property['image']['url'] string Ссылка на файл изображения
	 * - $property['image']['title'] string Заменяющий текст для изображения
	 * - $property['image']['link'] string Ссылка для перехода при щелчке по изображению
	 * - $property['yandex:full-text'] bool Вывод полного текста для Яндекс, по умолчанию FALSE
	 * - $property['strip-tags'] bool Указывает на необходимость удаления тегов из содержания RSS, по умолчанию FALSE
	 * - $property['show-image'] bool Разрешает передачу картинки для информационного элемента в теге enclosure
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $InformationSystem_id = 2;
	 * $information_groups_id = 1;
	 * $property['title'] = '';
	 * $property['description'] = '';
	 * $property['link'] = '';
	 * $items_on_page=5;
	 * $items_begin=1;
	 *
	 * $InformationSystem->ShowInformationSystemRss($InformationSystem_id, $information_groups_id, $items_on_page, $items_begin);
	 * ?>
	 * </code>
	 */
	function ShowInformationSystemRss($InformationSystem_id, $information_groups_id, $items_on_page = 10, $items_begin = 0, $property = array())
	{
		$site = & singleton('site');

		$rss = new RssWrite();

		$InformationSystem_id = intval($InformationSystem_id);

		$rss_mas_header = array(); // массив, содержащий данные заголовка канала
		$rss_mas_items = array(); // массив, содержащий данные новостных заголовков

		$items_on_page = intval($items_on_page);
		$items_begin = intval($items_begin);

		$row = $this->GetInformationSystem($InformationSystem_id);

		if (isset($property['title']))
		{
			$rss_mas_header['title'] = $property['title'];
		}
		else
		{
			$rss_mas_header['title'] = $row['information_systems_name'];
		}

		if (isset($property['description']))
		{
			$rss_mas_header['description'] = $property['description'];
		}
		else
		{
			$rss_mas_header['description'] = $row['information_systems_description'];
		}

		/* Если указано удалять теги - удаляем их*/
		if (Core_Type_Conversion::toBool($property['strip-tags']))
		{
			$rss_mas_header['title'] = strip_tags($rss_mas_header['title']);
			$rss_mas_header['description'] = strip_tags($rss_mas_header['description']);
		}

		/* Парметры картинки для канала*/
		if (isset($property['image']) && is_array($property['image']))
		{
			$rss_mas_header['image'] = $property['image'];
		}

		$site_alias = $site->GetCurrentAlias(CURRENT_SITE);

		// Информацию о пути к инфосистеме получаем по идентификатору узла структуры, с которым связана инфосистема
		$structure = & singleton('Structure');

		$url = $structure->GetStructurePath($row['structure_id'], 0);

		if ($url != '/')
		{
			$InformationSystem_url = '/' . $url;
		}
		else
		{
			$InformationSystem_url = $url;
		}

		if (isset($property['link']))
		{
			$rss_mas_header['link'] = $property['link'];
		}
		else
		{
			$rss_mas_header['link'] = 'http://' . $site_alias . $InformationSystem_url;
		}

		$queryBuilder = Core_QueryBuilder::select('informationsystem_items.id')
			->distinct();

		// выбираем элементы для конкретной информационной группы
		if ($information_groups_id !== FALSE)
		{
			$information_groups_id = intval($information_groups_id);
			$queryBuilder->where('informationsystem_group_id', '=', $information_groups_id);
		}

		$current_date = date('Y-m-d H:i:s');

		// формируем дополнительные условия для выборки
		if (is_array($property) && isset($property['select']))
		{
			foreach ($property['select'] as $key => $value)
			{
				if ($value['type'] == 0) // основное свойство
				{
					$this->parseQueryBuilder($value['prefix'], $queryBuilder);

					$value['value'] = Core_Type_Conversion::toStr($value['value']);

					$value['name'] != '' && $value['if'] != ''
						&& $queryBuilder->where($value['name'], $value['if'], $value['value']);

					$this->parseQueryBuilder($value['sufix'], $queryBuilder);
				}
				else // дополнительное свойство
				{
					$this->parseQueryBuilder($value['prefix'], $queryBuilder);

					$queryBuilder->where('informationsystem_item_properties.property_id', '=', $value['property_id']);

					$aPropertyValueTable = $this->getPropertyValueTableName(Core_Entity::factory('Property', $value['property_id'])->type);

					$queryBuilder->where(
						$aPropertyValueTable['tableName'] . '.' . $aPropertyValueTable['fieldName'], $value['if'], $value['value']
					);

					$this->parseQueryBuilder($value['sufix'], $queryBuilder);

					/*$query_property .= ' '.$value['prefix'].' information_propertys_table.information_propertys_id='."'".$value['property_id']."'".' and information_propertys_items_table.information_propertys_items_value '.$value['if']." '".quote_smart($value['value'])."' ".$value['sufix'].' ';*/
				}
			}
		}

		// Определяем ID элементов, которые не надо включать в выдачу
		// Если явно не передано поле сортировки
		if (isset($property['NotIn']))
		{
			$not_in_mass = Core_Array::toInt(explode(',', $property['NotIn']));
			$queryBuilder->where('informationsystem_items.id', 'NOT IN', $not_in_mass);
		}

		// Если явно не передано поле сортировки
		if (!isset($property['OrderField']))
		{
			// определяем поле сортировки информационных элементов
			switch ($row['information_systems_items_order_field'])
			{
				case 1:
					$order_field = 'name';
					break;
				case 2:
					$order_field = 'sorting';
					break;
				case 0:
				default:
					$order_field = 'datetime';
			}
		}
		else
		{
			$order_field = $property['OrderField'];
		}

		// Если явно передано направление сортировки
		if (isset($property['Order']))
		{
			$order_type = $property['Order'];
		}
		else
		{
			switch ($row['information_systems_items_order_type'])
			{
				case 1:
					$order_type = 'DESC';
				break;
				case 0:
				default:
					$order_type = 'ASC';
			}
		}

		$queryBuilder->orderBy($order_field, $order_type);

		// Определяем группы доступа для текущего авторизированного пользователя
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
			// получаем список групп доступа, в которые входит данный пользователь
			$mas_result = $SiteUsers->GetGroupsForUser($site_user_id);
		}
		else
		{
			$site_user_id = 0;
			$mas_result = array();
			$mas_result[] = 0;
		}

		// Добавляем в массив групп доступа для текущего пользователя
		// идентификаторы доступа, не противоречащие уже имеющимся в массиве
		$full_mas_result = $mas_result;

		foreach ($mas_result as $value => $value)
		{
			switch ($value)
			{
				case 0: // Доступ открыт всем
					$full_mas_result[-1] = 1;
					foreach ($full_mas_result as $full_key => $full_value)
					{
						if ($full_key != $value)
						{
							$full_mas_result[$value] = 1;
						}
					}
				break;
				case -1: // Доступ Как у родителя
					$full_mas_result[0] = 1;
					foreach ($full_mas_result as $full_key => $full_value)
					{
						if ($full_key != $value && $full_key != 0)
						{
							$full_mas_result[$value] = 1;
						}
					}

				break;
				default:
					$full_mas_result[-1] = 1;
					$full_mas_result[0] = 1;
				break;
			}
		}

		$mas_users_groups_access = array();
		foreach ($full_mas_result as $key => $value)
		{
			$mas_users_groups_access[] = $key;
		}

		$queryBuilder
			->from('informationsystem_items')
			->leftJoin('informationsystem_item_properties', 'informationsystem_items.informationsystem_id', '=', 'informationsystem_item_properties.informationsystem_id')
			->leftJoin('property_value_ints', 'informationsystem_items.id', '=', 'property_value_ints.entity_id',
				array(
					array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_ints.property_id')))
				)
			)
			->leftJoin('property_value_strings', 'informationsystem_items.id', '=', 'property_value_strings.entity_id',
				array(
					array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_strings.property_id')))
				)
			)
			->leftJoin('property_value_texts', 'informationsystem_items.id', '=', 'property_value_texts.entity_id',
				array(
					array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_texts.property_id')))
				)
			)
			->leftJoin('property_value_datetimes', 'informationsystem_items.id', '=', 'property_value_datetimes.entity_id',
				array(
					array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_datetimes.property_id')))
				)
			)
			->leftJoin('property_value_files', 'informationsystem_items.id', '=', 'property_value_files.entity_id',
				array(
					array('AND' => array('informationsystem_item_properties.property_id', '=', Core_QueryBuilder::expression('property_value_files.property_id')))
				)
			)
			->where('informationsystem_items.informationsystem_id', '=', $row['information_systems_id'])
			->where('informationsystem_items.siteuser_group_id', 'IN', $mas_users_groups_access)
			->where('start_datetime', '<=', $current_date)
			->open()
			->where('end_datetime', '>=', $current_date)
			->setOr()
			->where('end_datetime', '=', '0000-00-00 00:00:00')
			->close()
			->where('active', '=', 1);

		if ($items_on_page !== FALSE)
		{
			$queryBuilder->limit($items_begin, $items_on_page);
		}

		$aInformationsystemItems = $queryBuilder->execute()->asAssoc()->result();

		$in = array();

		// Формируем список для выбора одним запросом данных обо всех элементах + для
		// генерации XML (ниже)
		foreach($aInformationsystemItems as $aInformationsystemItem)
		{
			$in[] = $aInformationsystemItem['id'];
		}

		if (count($in) > 0)
		{
			$queryBuilder
				->clear()
				->select()
				->from('informationsystem_items')
				->where('id', 'IN', $in)
				->orderBy($order_field, $order_type);

			$aInformationsystemItems = $queryBuilder->execute()->asAssoc()->result();

			$i = 0;

			foreach($aInformationsystemItems as $aInformationsystemItem)
			{
				$rss_mas_items[$i]['pubDate'] = $aInformationsystemItem['datetime'];
				$rss_mas_items[$i]['title'] = $aInformationsystemItem['name'];
				$rss_mas_items[$i]['description'] = $aInformationsystemItem['description'];

				if (Core_Type_Conversion::toBool($property['yandex:full-text']))
				{
					$rss_mas_items[$i]['full-text'] = $aInformationsystemItem['text'];

					$group_row = $this->GetInformationGroup($aInformationsystemItem['informationsystem_group_id']);

					if ($group_row)
					{
						$rss_mas_items[$i]['category'] = $group_row['information_groups_name'];
					}
				}

				if (Core_Type_Conversion::toBool($property['strip-tags']))
				{
					$rss_mas_items[$i]['title'] = strip_tags($rss_mas_items[$i]['title']);
					$rss_mas_items[$i]['description'] = strip_tags($rss_mas_items[$i]['description']);

					if (Core_Type_Conversion::toBool($property['yandex:full-text']))
					{
						$rss_mas_items[$i]['full-text'] = strip_tags($rss_mas_items[$i]['full-text']);
					}
				}

				$path_item = $this->GetPathItem($aInformationsystemItem['id'], '');

				$rss_mas_items[$i]['link']= 'http://' . $site_alias . $InformationSystem_url . $path_item;

				// Уникальный идентификатор элемента
				$rss_mas_items[$i]['guid']= 'http://' . $site_alias . $InformationSystem_url . $path_item;

				// Если передавать изображение для информационного элемента
				if (Core_Type_Conversion::toBool($property['show-image']))
				{
					if (!empty($aInformationsystemItem['image_large']))
					{
						$information_item_dir = $this->GetInformationItemDir($aInformationsystemItem['id']);
						$file_enclosure = $information_item_dir . $aInformationsystemItem['image_large'];
						$rss_mas_items[$i]['enclosure'][0]['url'] = 'http://' . $site_alias . '/' . $file_enclosure;
						if (is_file($file_enclosure))
						{
							$rss_mas_items[$i]['enclosure'][0]['length'] = filesize($file_enclosure);
						}
					}
				}

				$i++;
			}
		}

		echo $rss->CreateRSS($rss_mas_header,$rss_mas_items,$property);
	}

	/**
	 * Получение информации об имени информационного элемента, информационной группы и системы, к которым он принадлежит
	 *
	 * @param int $information_item_id идентификатор информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_item_id = 1;
	 *
	 * $row = $InformationSystem->GetInformationSystemItems($information_item_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed ассоциативный массив с именами информационного элемента, информационной группы и системы, к которым он принадлежит, FALSE - в том случае если данный элемент отсутствует
	 */
	function GetInformationSystemItems($information_item_id)
	{
		$information_item_id = intval($information_item_id);

		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($information_item_id);

		if (is_null($oInformationsystem_Item->id))
		{
			return FALSE;
		}

		$aReturn = array();

		$aReturn['information_items_name'] = $oInformationsystem_Item->name;

		$oInformationsystem_Group = $oInformationsystem_Item->Informationsystem_Group;
		$aReturn['information_groups_name'] = $oInformationsystem_Group->name;

		$oInformationsystem = $oInformationsystem_Item->Informationsystem;
		$aReturn['information_systems_name'] = $oInformationsystem->name;

		return $aReturn;
	}

	/**
	 * Получение имени информационной группы
	 *
	 * @param int $information_groups_id идентификатор информационной группы, имя которой необходимо получить
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_groups_id = 2;
	 *
	 * $result = $InformationSystem->GetInformationGroupName($information_groups_id);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return mixed название группы информационной системы. Если группы с таким идентификатором нет, то возвращает FALSE
	 */
	function GetInformationGroupName($information_groups_id)
	{
		$information_groups_id = intval($information_groups_id);

		$oInformationsystem_Group = Core_Entity::factory('Informationsystem_Group')->find($information_groups_id);

		if (is_null($oInformationsystem_Group->id))
		{
			return FALSE;
		}

		return $oInformationsystem_Group->name;
	}

	/**
	 * Получение комментариев информационных элементов
	 *
	 * @param $param массив атрибутов
	 * - $param['information_items_id'] идентификатор информационного элемента или FALSE при выборе всех комментариев
	 * - $param['information_systems_id'] идентификатор информационной системы (при указании information_items_id = FALSE)
	 * - $param['comment_parent_id'] идентификатор родительского комментария. Для корневого раздела указывается 0
	 * - $param['comment_status'] статус активности отбираемых комментариев. Возможные значения:
	 * <br />0 - неактивные
	 * <br />1 - активные (по умолчанию)
	 * <br />false - все
	 * - $param['begin'] комментарий, с которого начинать выбор - по умолчанию 0.
	 * - $param['count'] количество отбираемых комментариев, по умолчанию выбираются все комментарии
	 * - $param['CommentOrderField'] поле сортировки, по умолчанию comment_date
	 * - $param['CommentOrder'] направление сортировки, по умолчанию DESC
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param['information_items_id'] = 1;
	 * $param['comment_status'] = 1;
	 *
	 * $row = $InformationSystem->GetCommentInformationSystemItem($param);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return mixed массив со строками - комментариями, FALSE в случае отсутствия комментариев к информационному элементу
	 */
	function GetCommentInformationSystemItem($param)
	{
		$information_items_id = Core_Type_Conversion::toInt($param['information_items_id']);
		$comment_status = isset($param['comment_status']) ? $param['comment_status'] : 1;

		$queryBuilder = Core_QueryBuilder::select('comments.*')
			->sqlCalcFoundRows()
			->from('comments')
			->join('comment_informationsystem_items', 'comments.id', '=', 'comment_id')
			->join('informationsystem_items', 'comment_informationsystem_items.informationsystem_item_id', '=', 'informationsystem_items.id')
			->where('comments.deleted', '=', 0)
			->where('informationsystem_items.deleted', '=', 0);

		if ($information_items_id)
		{
			$queryBuilder->where('informationsystem_item_id', '=', $information_items_id);
		}
		elseif(isset($param['information_systems_id']))
		{
			$param['information_systems_id'] = intval($param['information_systems_id']);
			$queryBuilder->where('informationsystem_id', '=', $param['information_systems_id']);
		}

		if (isset($param['comment_parent_id']))
		{
			$param['comment_parent_id'] = intval($param['comment_parent_id']);
			$queryBuilder->where('comments.parent_id', '=', $param['comment_parent_id']);
		}

		// Отображать не все комментарии
		if ($comment_status !== FALSE)
		{
			$comment_status = intval($comment_status);

			if ($comment_status)
			{
				$comment_status = 1;
			}

			$queryBuilder->where('comments.active', '=', $comment_status);
		}

		$order_field = isset($param['CommentOrderField']) ? $param['CommentOrderField'] : 'datetime';
		$order_type = isset($param['CommentOrder']) ? $param['CommentOrder'] : 'DESC';

		$queryBuilder->orderBy($order_field, $order_type);

		if (isset($param['begin']) && isset($param['count']))
		{
			$param['begin'] = intval($param['begin']);
			$param['count'] = intval($param['count']);

			$queryBuilder->limit($param['begin'], $param['count']);
		}

		$aComments = $queryBuilder->execute()->asObject('Comment_Model')->result();

		$this->count_comments = 0;
		foreach($aComments as $oComment)
		{
			$CommentMass[$oComment->id] = $this->getArrayInformationsystemItemComment($oComment);
			$this->count_comments++;
		}

		return $this->count_comments != 0
			? $CommentMass
			: FALSE;
	}

	/**
	 * Получение списка дополнительных свойств и их значений для информационного элемента
	 *
	 * @param int $information_items_id идентификатор информационного элемента
	 * @param int $param массив дополнительных параметров
	 * - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_items_id = 1;
	 *
	 * $row = $InformationSystem->GetPropertysInformationSystemItem($information_items_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с данными о свойствах информационного элемента или FALSE - в случае отсутствия дополнительных свойств у элементов данной информационной системы
	 */
	function GetPropertysInformationSystemItem($information_items_id, $param = array())
	{
		$information_items_id = intval($information_items_id);

		// Если есть в кэше - возвращаем из кэша
		if (isset($this->PropertyMass[$information_items_id]))
		{
			return $this->PropertyMass[$information_items_id];
		}

		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($information_items_id);

		$aPropertyValues = $oInformationsystem_Item->getPropertyValues();

		$return = array();

		foreach ($aPropertyValues as $oPropertyValue)
		{
			$return[$oPropertyValue->Property->id] =
				$this->getArrayItemPropertyValue($oPropertyValue) + $this->getArrayItemProperty($oPropertyValue->Property);
		}

		// если было найдено хоть одно свойство
		if (count($return))
		{
			if (!isset($param['cache_off']))
			{
				$this->PropertyMass[$information_items_id] = $return;
			}

			// Возвращаем массив со строками - свойствами элемента
			return $return;
		}

		return FALSE;
	}

	/**
	 * Определение доступности информационного элемента для пользователя
	 *
	 * @param int $site_users_id идентификатор пользователя
	 * @param int $information_item_id идентификатор информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * // Получим текущего пользователя
	 * $SiteUsers = new SiteUsers();
	 * $site_users_id = $SiteUsers->GetCurrentSiteUser();
	 *
	 * $information_item_id = 1;
	 *
	 * $result = $InformationSystem->InformationSystemItemAccess($site_users_id, $information_item_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Элемент доступен";
	 * }
	 * else
	 * {
	 * 	echo "Элемент недоступен";
	 * }
	 * ?>
	 * </code>
	 * @return boolean true - элемент доступен, FALSE - элемент недоступен
	 */
	function InformationSystemItemAccess($site_users_id, $information_item_id)
	{
		$row_item = $this->GetInformationSystemItem($information_item_id);

		switch ($row_item['information_items_access'])
		{
			case -1:// элемент имеет такой же статус доступа как и группа, к которой он относится
				return $this->GetAccessItem($site_users_id, $row_item['information_items_id'], $row_item);

			case 0:// элемент доступен всем
				return TRUE;
			default: // типы доступа для различных групп доступа пользователей
				return $this->GetAccessItem($site_users_id, $row_item['information_items_id'], $row_item);
		}
	}

	/**
	 * Определения уровня доступности информационной группы
	 *
	 * @param int $information_group_id идентификатор информационной группы
	 * @param int $information_system_id идентификатор информационной системы
	 * @param array $row_group ассоциативный массив с информацией о группе, по умолчанию пустой
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_group_id = 2;
	 * $information_system_id = 1;
	 *
	 * $result = $InformationSystem->GetInformationSystemGroupAccess($information_group_id, $information_system_id);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int уровень (группа) доступа пользователя к информационной группе, 0 - доступ разрешен всем
	 */
	function GetInformationSystemGroupAccess($information_group_id, $information_system_id = 0, $row_group = array(), $param = array())
	{
		$information_group_id = intval($information_group_id);
		$information_system_id = intval($information_system_id);

		// Если есть уже определенные права доступа - возвращаем их
		if ($information_group_id && isset($this->InformationSystemGroupAccess[$information_group_id]))
		{
			return $this->InformationSystemGroupAccess[$information_group_id];
		}

		// Некорневая группа
		if ($information_group_id)
		{
			$result = Core_Entity::factory('Informationsystem_Group', $information_group_id)->getSiteuserGroupId();

			// Запишим в кэш (именно здесь, т.к. ниже идет корень и там участвует езе ID ИС)
			$this->InformationSystemGroupAccess[$information_group_id] = $result;
		}
		else // Корневая группа
		{
			// Получаем данные доступа из инфосистемы
			$row_information_system = $this->GetInformationSystem($information_system_id);
			$result = $row_information_system['information_systems_access'];
		}

		return $result;
	}

	/**
	 * Определение уровня доступности информационного элемента
	 *
	 * @param int $information_items_id идентификатор информационного элемента
	 *
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_items_id = 1;
	 *
	 * $result = $InformationSystem->GetInformationSystemItemAccess($information_items_id);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int уровень (группа) доступа пользователя к информационной группе, 0 - доступ разрешен всем
	 */
	function GetInformationSystemItemAccess($information_items_id, $param = array())
	{
		$information_items_id = intval($information_items_id);
		return Core_Entity::factory('Informationsystem_Item', $information_items_id)->getSiteuserGroupId();
	}

	/**
	 * Проверка возможности доступа пользователя к информационной группе
	 *
	 * @param int $site_users_id идентификатор пользователя
	 * @param int $information_group_id идентификатор информационной группы
	 * @param int $information_system_id идентификатор информационной системы, не обязательный параметр, по умолчанию 0
	 * @param array $row_group ассоциативный массив с информацией о группе, по умолчанию пустой
	 * @param array $property ассоциативный массив с параметрами
	 * - $property['cache'] - использовать кэширование, по умолчанию true
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * // Получим текущего пользователя
	 * $SiteUsers = new SiteUsers();
	 * $site_users_id = $SiteUsers->GetCurrentSiteUser();
	 *
	 * $information_group_id = 2;
	 * $information_system_id = 1;
	 *
	 * $result = $InformationSystem->IssetAccessForInformationSystemGroup($site_users_id, $information_group_id, $information_system_id);
	 *
	 * // Распечатаем результат
	 * if ($result)
	 * {
	 * 	echo 'Информационная группа доступна пользователю';
	 * }
	 * else
	 * {
	 * 	echo 'Информационная группа не доступна пользователю';
	 * }
	 * ?>
	 * </code>
	 * @return bool
	 */
	function IssetAccessForInformationSystemGroup($site_users_id, $information_group_id, $information_system_id = 0, $row_group = array(), $property = array('cache' => true))
	{
		$site_users_id = intval($site_users_id);
		$information_group_id = intval($information_group_id);
		$information_system_id = intval($information_system_id);

		// определяем группу доступа
		$group_access = $this->GetInformationSystemGroupAccess($information_group_id, $information_system_id, $row_group);

		// Если есть уже определенные права доступа пользователя к данной группе доступа
		if ($information_group_id && isset($this->CacheIssetAccessForInformationSystemGroup[$group_access][$site_users_id]))
		{
			return $this->CacheIssetAccessForInformationSystemGroup[$group_access][$site_users_id];
		}

		// определяем относится ли данный пользователь к группе пользователей, указанных в списке доступа для инфогруппы
		if ($site_users_id > 0 && class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			// получаем список групп доступа, в которые входит данный пользователь
			$mas_group_access = $SiteUsers->GetGroupsForUser($site_users_id);
		}
		else
		{
			$mas_group_access = array();
			$mas_group_access[] = 0;
		}

		if (in_array($group_access, $mas_group_access) || $group_access == 0)
		{
			$result = TRUE;
		}
		// Пользователь не входит в группу доступа, указанную для данной инфогруппы
		else
		{
			$result = FALSE;
		}

		// Запишем в кэш памяти, если только группа не 0 (там участвует ID ИС)
		if ($information_group_id)
		{
			$this->CacheIssetAccessForInformationSystemGroup[$group_access][$site_users_id] = $result;
		}

		return $result;
	}

	/**
	 * Определение доступности информационного элемента
	 *
	 * @param int $site_users_id идентификатор пользователя
	 * @param int $information_items_id идентификатор инфоэлемента
	 * @param int $parent параметр, определяющий наследует ли информационный элемент тип доступа от родителя (1 - наследует, 0 - не наследует)
	 * @param array $row_item ассоциативный массив свойств информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * // Получим текущего пользователя
	 * $SiteUsers = new SiteUsers();
	 * $site_users_id = $SiteUsers->GetCurrentSiteUser();
	 *
	 * $information_items_id = 2;
	 *
	 * $result = $InformationSystem->GetAccessItem($site_users_id, $information_items_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Информационный элемент доступен пользователю";
	 * }
	 * else
	 * {
	 * echo "Информационный элемент не доступен пользователю";
	 * }
	 * ?>
	 * </code>
	 * @return boolean true - информационный элемент доступен пользователю, FALSE - не доступен пользователю
	 */
	function GetAccessItem($site_users_id, $information_items_id, $row_item = array())
	{
		$site_users_id = intval($site_users_id);
		$information_items_id = intval($information_items_id);
		$row_item = Core_Type_Conversion::toArray($row_item);

		// массив не содержит информации об информационном элементе
		if (count($row_item) == 0)
		{
			$row_item = $this->GetInformationSystemItem($information_items_id);
		}

		// определяем относится ли данный пользователь к
		// группе пользователей, указанных в списке доступа для инфосистемы
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			// получаем список групп доступа, в которые входит данный пользователь
			$mas_group_access = $SiteUsers->GetGroupsForUser($site_users_id);
		}
		else
		{
			$mas_group_access = array();
			$mas_group_access[] = 0;
		}

		// Элемент наследует тип доступа от родительской группы
		if ($row_item['information_items_access'] != -1)
		{
			return in_array($row_item['information_items_access'], $mas_group_access);
		}

		// если инфоэлемент находится в корне инфосистемы (не принадлежит ни одной группе)
		if (!$row_item['information_groups_id'])
		{
			$row_system = $this->GetInformationSystem($row_item['information_systems_id']);

			switch ($row_system['information_systems_access'])
			{
				case 0: // Инфосистема доступна всем -  элемент доступен
				case -1: // Тип доступа  как у родителя - элемент доступен
				{
					return TRUE;
				}
				// Типы доступа для различных групп доступа
				default:
				{
					// Если среди групп, в которые входит данный пользователь,
					// есть группа доступа, указанная для данной инфосистемы,
					// то пользователю разрешен доступ к элементу данной инфосистемы
					return in_array($row_system['information_systems_access'], $mas_group_access);
				}
			}
		}
		else // Инфоэлемент находится в информационной группе
		{
			// получаем данные об информационной группе
			$row_group = $this->GetInformationGroup($row_item['information_groups_id']);

			switch ($row_group['information_groups_access'])
			{
				case 0: // доступ к группе разрешен всем
					return TRUE;
				case -1: // доступ к группе как у родительской группы
					return  $this->IssetAccessForInformationSystemGroup($site_users_id, $row_group['information_groups_id']);

				// Типы доступа для различных групп доступа
				default:
					// Если среди групп, в которые входит данный пользователь,
					// есть группа доступа, указанная для данной инфогруппы,
					// то пользователю разрешен доступ к элементу данной инфогруппы
					return in_array($row_group['information_groups_access'], $mas_group_access);
			}
		}

		return FALSE;
	}

	/**
	 * Определение числа подгрупп и элементов, содержащихся в группах информационной системы
	 * Информация сохраняется в массиве:
	 * <br/>для числа групп в группе $this->CacheCountGroupsAndItems[information_system_id][information_groups_id]['GROUPS']
	 * <br/>для числа элементов в группе $this->CacheCountGroupsAndItems[information_system_id][information_groups_id]['ITEMS']
	 *
	 * @param int $information_system_id идентификатор информационной системы
	 * @param int $site_user_id идентификатор пользователя сайта, необязательный параметр. по умолчанию = 0.
	 */
	function FillMemCacheCountItemsAndGroup($information_system_id, $site_user_id = 0)
	{
		$information_system_id = intval($information_system_id);

		$this->CacheCountGroupsAndItems[$information_system_id] = array();

		$queryBuilder = Core_QueryBuilder::select('parent_id', array('COUNT(id)', 'count'))
		->from('informationsystem_groups')
		->where('informationsystem_id', '=', $information_system_id)
		->where('deleted', '=', 0)
		->groupBy('parent_id');

		$aInformationsystem_Groups = $queryBuilder->execute()->asAssoc()->result();

		foreach($aInformationsystem_Groups as $oInformationsystem_Group)
		{
			$this->CacheCountGroupsAndItems[$information_system_id][$oInformationsystem_Group['parent_id']]['GROUPS'] = $oInformationsystem_Group['count'];
		}

		$current_date = date('Y-m-d H:i:s');

		// Определяем группы доступа для текущего авторизированного	пользователя
		$mas_result = $this->GetSiteUsersGroupsForUser($site_user_id);

		$queryBuilder->clear()
		->select('informationsystem_group_id', array('COUNT(id)', 'count'))
		->from('informationsystem_items')
		->where('informationsystem_id', '=', $information_system_id)
		->where('active', '=', 1)
		->where('start_datetime', '<=', $current_date)
		->open()
		->where('end_datetime', '>=', $current_date)
		->setOr()
		->where('end_datetime', '=', '0000-00-00 00:00:00')
		->close()
		->where('siteuser_group_id', 'IN', $mas_result)
		->where('deleted', '=', 0)
		->groupBy('informationsystem_group_id');

		$aInformationsystem_Items = $queryBuilder->execute()->asAssoc()->result();

		foreach($aInformationsystem_Items as $Informationsystem_Item)
		{
			$this->CacheCountGroupsAndItems[$information_system_id][$Informationsystem_Item['informationsystem_group_id']]['ITEMS'] = $Informationsystem_Item['count'];
		}
	}

	/**
	 * Формирование дерева групп и подгрупп в массиве по их идентификаторам
	 *
	 * @param int $information_system_id идентификатор информационной системы
	 * @param array $param массив дополнительных параметров
	 * - Все параметры метода GetAllInformationGroups()
	 * - $param['cache_name'] - ссылка на переменную для сохранения дерева групп, по умолчанию $this->FullCacheGoupsIdTree, необязательный параметр
	 */
	function FillMemFullCacheGoupsIdTree($information_system_id, $param = array())
	{
		$information_system_id = intval($information_system_id);

		if (!isset($param['cache_name']))
		{
			$CacheName = & $this->FullCacheGoupsIdTree;

			// Если полный массив уже был заполнен для данной ИС - выходим.
			// Условие только для полных массивов, т.к. неполные могут меняться, например при выводе ИС с разными условиями для групп.
			if (isset($CacheName[$information_system_id]))
			{
				return ;
			}
		}
		else
		{
			// Иначе ссылка на переданное значение
			$CacheName = &$param['cache_name'];
		}

		$param['informationsystem_id'] = $information_system_id;

		$param['select_fields'] = array('id', 'parent_id');

		$mas_groups = $this->GetAllInformationGroups($param);

		foreach ($mas_groups as $key => $row)
		{
			$CacheName[$information_system_id][$row['parent_id']][] = $row['id'];
		}
	}

	/**
	 * Получение числа элементов и групп для переданной родительской группы. Для оптимизации числа запросов рекомендуется использовать совместно с FillMemCacheCountItemsAndGroup()
	 *
	 * @param int $parent_group_id идентификатор группы, для которой необходимо получить число
	 * <br />элементов и групп.
	 * @param int $information_system_id идентификатор информационной системы, к которой принадлежит группа
	 * @param boolean $sub параметр, определяющий будут ли учитываться подгруппы данной
	 * <br />группы при подсчете элементов и групп (true - подгруппы учитываются, FALSE - не учитываются).
	 * по умолчанию $sub = true
	 * @param int $site_user_id идентификатор пользователя сайта, необязательный параметр. по умолчанию = 0.
	 * @param array $param массив дополнительных параметров
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $parent_group_id = 2;
	 * $information_system_id = 1;
	 *
	 * $row = $InformationSystem->GetCountItemsAndGroups($parent_group_id, $information_system_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array $mas массив из 4-х элементов
	 * - $mas['count_items'] число элементов в группе без учета элементов в подгруппах
	 * <br />$mas['count_all_items'] число элементов в группе с учетом элементов в подгруппах
	 * <br />$mas['count_groups'] число групп в данной группе без учета вложенности подгрупп
	 * <br />$mas['count_all_groups'] число групп в данной группе с учетом вложенности подгрупп
	 */
	function GetCountItemsAndGroups($parent_group_id, $information_system_id, $sub = TRUE, $site_user_id = 0, $param = array())
	{
		$parent_group_id = intval($parent_group_id);
		$information_system_id = intval($information_system_id);

		$sub = Core_Type_Conversion::toBool($sub);

		if (isset($GLOBALS['INF_SYS_MEM_CACHE_count_items_and_groups'][$information_system_id][$parent_group_id]))
		{
			return $GLOBALS['INF_SYS_MEM_CACHE_count_items_and_groups'][$information_system_id][$parent_group_id];
		}

		/* Проверка на наличие в файловом кэше*/
		// Кэшируем только при построении для родителя = 0, т.к. при построении для всех может быть большая нагрузка на файловую систему
		if (!$parent_group_id || !isset($GLOBALS['INF_SYS_MEM_CACHE_count_items_and_groups'][$information_system_id]))
		{
			if (class_exists('Cache'))
			{
				$cache = & singleton('Cache');

				$cache_field = $information_system_id . '_' . serialize($sub) . '_' . serialize($site_user_id)/*.'_'.serialize($param)*/;

				$cache_name = 'INF_SYS_COUNT_ITEM_AND_GROUP';

				if (($in_cache = $cache->GetCacheContent($cache_field, $cache_name)) && $in_cache)
				{
					$GLOBALS['INF_SYS_MEM_CACHE_count_items_and_groups'][$information_system_id] = $in_cache['value'];

					if (isset($in_cache['value'][$parent_group_id]))
					{
						return $in_cache['value'][$parent_group_id];
					}
				}
			}
		}

		$mas = array();

		/* Определяем число элементов в группе */

		/* Если есть данные для инфосистемы. Если подгрупп было 0, то они не добавлены в массив*/
		if (!isset($this->CacheCountGroupsAndItems[$information_system_id]))
		{
			/* Производим расчет числа элементов в группе*/
			$this->FillMemCacheCountItemsAndGroup($information_system_id, $site_user_id);

			// Сразу заполним полное ДЕРЕВО групп
			if (!isset($this->FullCacheGoupsIdTree[$information_system_id]))
			{
				// Не передаем $param, т.к. необходимо рассчитать количество с учетом подгрупп
				$this->FillMemFullCacheGoupsIdTree($information_system_id
				// added 30-01-2011
				//array('groups_parent_id' => $parent_group_id)
				);
			}
		}

		// Элементы
		if (isset($this->CacheCountGroupsAndItems[$information_system_id][$parent_group_id]['ITEMS']))
		{
			$count_items = $this->CacheCountGroupsAndItems[$information_system_id][$parent_group_id]['ITEMS'];
		}
		else
		{
			$count_items = 0;
		}

		$mas['count_items'] = $count_items;
		$mas['count_all_items'] = $mas['count_items'];

		// Группы
		if (isset($this->CacheCountGroupsAndItems[$information_system_id][$parent_group_id]['GROUPS']))
		{
			$count_groups = $this->CacheCountGroupsAndItems[$information_system_id][$parent_group_id]['GROUPS'];
		}
		else
		{
			$count_groups = 0;
		}

		// Определяем число подгрупп в группе
		$mas['count_groups'] = $count_groups;
		$mas['count_all_groups'] = $mas['count_groups'];

		// Учитывать подгруппы
		if ($sub)
		{
			// Если дерево групп не заполнено для инфосистемы - заполняем его
			if (!isset($this->FullCacheGoupsIdTree[$information_system_id]))
			{
				//$this->FullCacheGoupsIdTree[$information_system_id] = array();
				$this->FillMemFullCacheGoupsIdTree($information_system_id, FALSE, array('groups_activity' => 1));
			}

			// Если у группы есть подгруппы
			if (isset($this->FullCacheGoupsIdTree[$information_system_id][$parent_group_id]))
			{
				foreach ($this->FullCacheGoupsIdTree[$information_system_id][$parent_group_id] as $group_id)
				{
					$mas_subgroup = $this->GetCountItemsAndGroups($group_id, $information_system_id, $sub, $site_user_id, $param);
					$mas['count_all_items'] += $mas_subgroup['count_all_items'];
					$mas['count_all_groups'] += $mas_subgroup['count_all_groups'];
				}
			}
		}

		// Кэшируем в памяти число элементов
		$GLOBALS['INF_SYS_MEM_CACHE_count_items_and_groups'][$information_system_id][$parent_group_id] = $mas;

		// Запись в файловый кэш
		if (!$parent_group_id)
		{
			if (class_exists('Cache'))
			{
				//$cache->Insert($cache_field, $mas, $cache_name);
				// В кэш записываем все для указанной ИС!
				$cache->Insert($cache_field, $GLOBALS['INF_SYS_MEM_CACHE_count_items_and_groups'][$information_system_id], $cache_name);
			}
		}
		return $mas;
	}

	/**
	 * Получение информации о инфосистемах
	 *
	 * @param int $site_id идентификатор сайта, если FALSE - ограничения по сайтам нет
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $site_id = 1;
	 *
	 * $resource = $InformationSystem->GetAllInformationSystems($site_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 *	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed результат выборки
	 */
	function GetAllInformationSystems($site_id = FALSE)
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'information_systems_id'),
			array('informationsystem_dir_id', 'information_systems_dir_id'),
			'structure_id',
			'site_id',
			array('name', 'information_systems_name'),
			array('description', 'information_systems_description'),
			array('items_sorting_direction', 'information_systems_items_order_type'),
			array('items_sorting_field', 'information_systems_items_order_field'),
			array('groups_sorting_direction', 'information_systems_group_items_order_type'),
			array('groups_sorting_field', 'information_systems_group_items_order_field'),
			array('image_large_max_width', 'information_systems_image_big_max_width'),
			array('image_large_max_height', 'information_systems_image_big_max_height'),
			array('image_small_max_width', 'information_systems_image_small_max_width'),
			array('image_small_max_height', 'information_systems_image_small_max_height'),
			array('siteuser_group_id', 'information_systems_access'),
			array('use_captcha', 'information_systems_captcha_used'),
			array('watermark_file', 'information_systems_watermark_file'),
			array('watermark_default_use_large_image', 'information_systems_default_used_watermark'),
			array('watermark_default_use_small_image', 'information_systems_default_used_small_watermark'),
			array('watermark_default_position_x', 'information_systems_watermark_default_position_x'),
			array('watermark_default_position_y', 'information_systems_watermark_default_position_y'),
			array('user_id', 'users_id'),
			array('items_on_page', 'information_systems_items_on_page'),
			array('format_date', 'information_systems_format_date'),
			array('format_datetime', 'information_systems_format_datetime'),
			array('url_type', 'information_systems_url_type'),
			array('typograph_default_items', 'information_systems_typograph_item'),
			array('typograph_default_groups', 'information_systems_typograph_group'),
			array('apply_tags_automatically', 'information_systems_apply_tags_automatic'),
			array('change_filename', 'information_systems_file_name_conversion'),
			array('apply_keywords_automatically', 'information_systems_apply_keywords_automatic'),
			array('group_image_large_max_width', 'information_systems_image_big_max_width_group'),
			array('group_image_large_max_height', 'information_systems_image_big_max_height_group'),
			array('group_image_small_max_width', 'information_systems_image_small_max_width_group'),
			array('group_image_small_max_height', 'information_systems_image_small_max_height_group'),
			array('preserve_aspect_ratio', 'information_systems_default_save_proportions')
		)
		->from('informationsystems')
		->where('deleted', '=', 0);

		if ($site_id !== FALSE)
		{
			$site_id = intval($site_id);
			$queryBuilder->where('site_id', '=', $site_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Получение информации о комментарии к информационному элементу
	 *
	 * @param int $comment_id идентификатор комментария
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $comment_id = 14;
	 *
	 * $row = $InformationSystem->GetComment($comment_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив с информацией о комментарии или FALSE
	 */
	function GetComment($comment_id)
	{
		$comment_id = intval($comment_id);

		if (isset($this->cm[$comment_id]))
		{
			return $this->cm[$comment_id];
		}

		$oComment = Core_Entity::factory('Comment')->find($comment_id);

		if (!is_null($oComment->id))
		{
			$this->cm[$comment_id] = $this->getArrayInformationsystemItemComment($oComment);
			return $this->cm[$comment_id];
		}

		return FALSE;
	}

	/**
	 * Определение количества инфосистем
	 *
	 * @param int $site_id идентификатор сайта, если 0 - для всех сайтов
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $site_id = CURRENT_SITE;
	 *
	 * $result = $InformationSystem->GetCountInformationSystem($site_id);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return int количество инфосистем
	 */
	function GetCountInformationSystem($site_id = CURRENT_SITE)
	{
		$site_id = intval($site_id);

		$queryBuilder = Core_QueryBuilder::select(array('COUNT(*)', 'count'))
			->from('informationsystems')
			->where('deleted', '=', 0);

		if ($site_id > 0)
		{
			$queryBuilder->where('site_id', '=', $site_id);
		}

		$row = $queryBuilder->execute()->asAssoc()->current();

		return $row['count'];
	}

	/**
	 * Вызов настроек информацонной системы, используется в настройках динамической страницы совместно с ShowInformationSystemPageContent в коде динамической страницы
	 *
	 * @param int $InformationSystem_id идентификатор информационной системы
	 * @param int $items_on_page количество элементов на страницу
	 * @param array $property массив дополнительных параметров<br />
	 * $property['page'] текстовая информация для указания номера страницы, например "страница"
	 * $property['separator'] разделитель, по умолчанию ' /'
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $InformationSystem_id = 1;
	 * $items_on_page = 5;
	 *
	 * $row = $InformationSystem->ShowInformationSystemPageConfig($InformationSystem_id, $items_on_page);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @param array массив с настройками информационной системы
	 */
	function ShowInformationSystemPageConfig($InformationSystem_id, $items_on_page, $property = array())
	{
		$InformationSystem_id = intval($InformationSystem_id);
		if ($items_on_page !== TRUE)
		{
			$items_on_page = intval($items_on_page);
		}
		$property = Core_Type_Conversion::toArray($property);

		// Текстовая информация для указания номера страницы, например "страница"
		if (isset($property['page']))
		{
			$property['page'] = Core_Type_Conversion::toStr($property['page']);
		}
		else
		{
			$property['page'] = 'страница';
		}

		// Разделитель
		if (isset($property['separator']))
		{
			$property['separator'] = Core_Type_Conversion::toStr($property['separator']);
		}
		else
		{
			$property['separator'] = ' / ';
		}

		$return_array = array();

		// получаем для пути ассоциативный массив с id группы и id/url элемента для данной инфосистемы
		$result = $this->GetInformationFromPath($InformationSystem_id);

		// Если путь существует
		if ($result)
		{
			// получаем массив с деревом от текущей группы до корня
			$this->GetInformationGroupsForXml($result['group'], $InformationSystem_id);

			$group_path = '';

			$mas_information_groups_for_xml = $this->get_mas_information_groups_for_xml();

			$row_group = $this->GetInformationGroup($result['group']);

			// получаем данные из seo - полей для групп
			$seo_title = trim($row_group['information_groups_seo_title']);
			$seo_description = trim($row_group['information_groups_seo_description']);
			$seo_keywords = trim($row_group['information_groups_seo_keywords']);

			// цикл по массиву с деревом для формирования пути по группам
			for($i = count($mas_information_groups_for_xml)-1; $i >= 0; $i--)
			{
				// seo_title для группы пустое, то в заголовок подставляем название
				if (trim($mas_information_groups_for_xml[$i]['information_groups_seo_title']) == '')
				{
					$group_path .= $property['separator'] . $mas_information_groups_for_xml[$i]['information_groups_name'];
				}
				else
				{
					$group_path .= $property['separator'] . $mas_information_groups_for_xml[$i]['information_groups_seo_title'];
				}
			}

			// Определяем название информационной системы
			$row = $this->GetInformationSystem($InformationSystem_id);

			// имя информационной системы
			$InformationSystem_name = $row['information_systems_name'];

			// Если вывод информационного элемента
			if ($result['item'])
			{
				// определяем id информационного элемента
				$information_items_id = $this->GetIdInformationItem($result['item'], $result['group'], $InformationSystem_id);

				$return_array['item_id'] = $information_items_id;

				// получаем данные об элементе
				$row_item = $this->GetInformationSystemItem($information_items_id);

				// Имя элемента
				$item_name = $property['separator'] . $row_item['information_items_name'];

				// проверяем если seo_title непустой, то в заголовок страницы подставляем его
				if (trim($row_item['information_items_seo_title']) != '')
				{
					$item_name = $property['separator'] . trim($row_item['information_items_seo_title']);
				}
			}
			else
			{
				// Вывод информационной группы
				// Определяем номер страницы для показа
				$end_array_item = end($GLOBALS['URL_ARRAY']);
				$page = Core_Type_Conversion::toStr($end_array_item);

				if (preg_match("/^page-([0-9]*)$/", $page, $regs) && Core_Type_Conversion::toInt($regs[1]) > 0)
				{
					// Страница умножается на кол-во элементов, выводимых на страницу
					$items_begin = ($regs[1] - 1)* $items_on_page;

					// Если показываем группу, а не элемент, то указываем страницу (страница N), если она не первая
					$page_number = "{$property['separator']}{$property['page']} {$regs[1]}";
				}
				else
				{
					$items_begin = 0;
				}

				// Массив, возвращаемый методом
				$return_array['items_begin'] = $items_begin;
			}

			// форимируем заголовок страницы
			$new_title = $InformationSystem_name.$group_path . Core_Type_Conversion::toStr($item_name) . Core_Type_Conversion::toStr($page_number);
		}

		if (!empty($new_title) && Core_Type_Conversion::toInt($result['group']) != 0)
		{
			$kernel = & singleton('kernel');

			// отображаем группу
			if (!isset($row_item))
			{
				// Заголовок для группы задан
				if (!empty($seo_title))
				{
					$kernel->set_title($seo_title);
				}
				else
				{
					$kernel->set_title($new_title);
				}

				// Описание для группы задано
				if (!empty($seo_description))
				{
					$kernel->set_description($seo_description);
				}
				else
				{
					$kernel->set_description($new_title);
				}

				// Ключевые слова для группы заданы
				if (!empty($seo_keywords))
				{
					$kernel->set_keywords($seo_keywords);
				}
				else
				{
					$kernel->set_keywords($new_title);
				}
			}
			else // отображаем элемент
			{
				if (!empty($row_item['information_items_seo_description']))
				{
					$kernel->set_title(trim($row_item['information_items_seo_title']));
				}
				else
				{
					$kernel->set_title($new_title);
				}

				// Описание для элемента задано
				if (!empty($row_item['information_items_seo_description']))
				{
					$kernel->set_description(trim($row_item['information_items_seo_description']));
				}
				else
				{
					$kernel->set_description($new_title);
				}

				// Ключевые слова для элемента заданы
				if (!empty($row_item['information_items_seo_keywords']))
				{
					$kernel->set_keywords(trim($row_item['information_items_seo_keywords']));
				}
				else
				{
					$kernel->set_keywords($new_title);
				}
			}
		}

		$return_array['items_on_page'] = $items_on_page;

		return $return_array;
	}

	/**
	 * Вызова информацонной системы, используется в динамической странице совместо с ShowInformationSystemPageContent в настройках динамической страницы
	 *
	 * @param int $InformationSystem_id
	 * @param string $xsl_list XSL шаблон для вывода спсика инфорамционных элементов
	 * @param string $xsl_item XSL шаблон для вывода одного элемента
	 * @param array $property массив дополнительных параметров<br />
	 * $property['items_on_page'] число элементов на страницу, по умолчанию 10
	 * $property['items_begin'] элемент, с которого начинать вывод, по умолчанию 0
	 * @param array $external_propertys внешние параметры, передаваемые в XML
	 * @param array $infsys_property дополнительные свойства для показа информационной системы, см. ShowInformationSystem
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $InformationSystem_id = 3;
	 * $xsl_list = 'СписокЭлементовИнфосистемы';
	 * $xsl_item = 'ВыводЕдиницыИнформационнойСистемы';
	 *
	 * $InformationSystem->ShowInformationSystemPageContent($InformationSystem_id, $xsl_list, $xsl_item);
	 * ?>
	 * </code>
	 */
	function ShowInformationSystemPageContent($InformationSystem_id, $xsl_list, $xsl_item, $property = array(), $external_propertys = array(), $infsys_property = array())
	{
		$InformationSystem_id = intval($InformationSystem_id);
		$xsl_list = Core_Type_Conversion::toStr($xsl_list);
		$xsl_item = Core_Type_Conversion::toStr($xsl_item);
		$property = Core_Type_Conversion::toArray($property);

		if (isset($property['items_on_page']))
		{
			if ($property['items_on_page'] !== TRUE)
			{
				$property['items_on_page'] = Core_Type_Conversion::toInt($property['items_on_page']);
			}
		}
		else
		{
			$property['items_on_page'] = 10;
		}

		if (isset($property['items_begin']))
		{
			$property['items_begin'] = Core_Type_Conversion::toInt($property['items_begin']);
		}
		else
		{
			$property['items_begin'] = 0;
		}

		// Принимает в качестве параметра ID информационной системы
		$result = $this->GetInformationFromPath($InformationSystem_id);

		if ($result != FALSE)
		{
			// Вывод списка
			if ($result['item'] == FALSE)
			{
				$this->ShowInformationSystem($InformationSystem_id, $result['group'], $xsl_list, $property['items_on_page'], $property['items_begin'], $external_propertys,$infsys_property);
			}
			else
			{
				// Определяем идентификатор информационного элемента
				$information_items_id = $this->GetIdInformationItem($result['item'], $result['group'], $InformationSystem_id);

				// Выводим элемент группы
				$this->ShowInformationSystemItem($information_items_id, $xsl_item,$external_propertys);
			}
		}
	}

	/**
	 * Получение данных о дополнительных свойствах элементов
	 * информационной системы
	 * @param int $information_propertys_id идентификатор свойства
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_id = 8;
	 *
	 * $row = $InformationSystem->GetInformationSystemItemProperty($information_propertys_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с информацией о свойстве или FALSE
	 */
	function GetInformationSystemItemProperty($information_propertys_id)
	{
		$information_propertys_id = intval($information_propertys_id);

		$oProperty = Core_Entity::factory('Property')->find($information_propertys_id);

		if (!is_null($oProperty->id))
		{
			return $this->getArrayItemProperty($oProperty);
		}

		return FALSE;
	}

	/**
	 * Вставка тегов для информационных элементов
	 *
	 * @param array $array массив атрибутов
	 * <br />str $array['tags'] - теги для информационного элемента с разделителем 'запятая'
	 * <br />str $array['information_items_id'] - идентификатор информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $array['tags'] = 'newtag';
	 * $array['information_items_id'] = 1;
	 *
	 * $result = $InformationSystem->InsertInformationItemTags($array);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function InsertInformationItemTags($array)
	{
		if (class_exists('Tag'))
		{
			$array['tags'] = Core_Type_Conversion::toStr($array['tags']);
			$array['information_items_id'] = Core_Type_Conversion::toInt($array['information_items_id']);

			if ($array['information_items_id'] <= 0)
			{
				return FALSE;
			}

			$tags_array = explode(',', $array['tags']);

			$oTag = & singleton('Tag');

			$insert_tag = array();

			foreach ($tags_array as $tag)
			{
				$tag = trim($tag);

				$tag_id = $oTag->InsertTag(array('tag_name' => $tag));

				// Сохраняем в списке вставленных тегов для последующей проверки
				$insert_tag[] = $tag_id;

				$oTag->InsertTagRelation(array('tag_id' => $tag_id, 'information_items_id' => $array['information_items_id']));
			}

			// Удаляем другие соответствия, если они были всnавлены ранее, для этого
			// получаем список всех соответствий для элемента
			$tags_temp = $oTag->GetTagRelation(array('information_items_id' => $array['information_items_id']));

			if ($tags_temp)
			{
				foreach ($tags_temp as $mytag)
				{
					// Если тега не было в списке на добавление - удалим его связь с ИЭ
					if (!in_array($mytag['tag_id'], $insert_tag))
					{
						// Удаляем связь
						$oTag->DeleteTagRelation(array('tag_id' => $mytag['tag_id'], 'information_items_id' => $array['information_items_id']));
					}
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Показ облака тегов для информационной системы
	 *
	 * @param int $InformationSystemId Идентификатор информационной системы
	 * @param str $xsl_name имя XSL-шаблона
	 * @param array $property массив дополнительных атрибутов
	 * - $property['begin'] начальная позиция отображения тегов (по умолчанию 0)
	 * - $property['count'] количество отображаемых тегов
	 * - $property['mas_groups_id'] массив идентификаторов информационных групп, используемые для получения списка тегов элементов, входящих в группы
	 * - $property['TagsOrder'] параметр, определяющий порядок сортировки тегов. Принимаемые значения: ASC - по возрастанию (по умолчанию), DESC - по убыванию
	 * - $property['TagsOrderField'] поле сортировки тегов, если случайная сортировка, то записать RAND(). по умолчанию теги сортируются по названию.
	 * - $property['tags_group_id'] идентификатор или массив идентификаторов групп тегов, из которых необходимо вести отбор тегов
	 * @param array $external_propertys массив дополнительных свойств для включения в XML
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $InformationSystemId= 1;
	 * $xsl_name = 'ОблакотеговИнформационнойСистемы';
	 *
	 * $InformationSystem->ShowTagsCloud($InformationSystemId, $xsl_name);
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function ShowTagsCloud($InformationSystemId, $xsl_name, $property = array(), $external_propertys = array())
	{
		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		/* по умолчанию кэширование - включено*/
		if (!isset($property['cache']))
		{
			$property['cache'] = TRUE;
		}

		// по умолчанию показываем только активные элементы
		if (!isset($property['show_item_type']))
		{
			$property['show_item_type'] = array('active');
		}

		/* Проверяем, установлен ли модуль кэширования*/
		if (class_exists('Cache') && $property['cache'])
		{
			$cache = & singleton('Cache');

			$kernel = & singleton('kernel');

			$cache_element_name = 'ShowTagsCloud_' . $InformationSystemId . '_' . $xsl_name . '_' . $kernel->implode_array($property, '_') . '_' . $kernel->implode_array($external_propertys,'_') . '_' . $site_user_id;

			$cache_name = 'INF_SYS_TAGS_CLOUD_HTML';
			if (($in_cache = $cache->GetCacheContent($cache_element_name, $cache_name)) && $in_cache)
			{
				echo $in_cache['value'];

				return TRUE;
			}
		}

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

		$xmlData .= '<document>' . "\n";

		/* Вносим внешний XML в документ.
		 Внешний XML отличается от параметров $external_propertys тем, что добавляется блоком в начало документа*/
		if (isset($property['external_xml']))
		{
			$xmlData .= $property['external_xml'];
		}

		/* Вносим в XML дополнительные теги из массива дополнительных параметров*/
		$ExternalXml = new ExternalXml;
		$xmlData .= $ExternalXml->GenXml($external_propertys);
		unset($ExternalXml);

		// Инфосистема доступна текущему зарегистрированному пользователю
		if ($this->IssetAccessForInformationSystemGroup($site_user_id, 0, $InformationSystemId))
		{
			// Получаем данные об информационной системе
			$row = $this->GetInformationSystem($InformationSystemId);

			if ($row !== FALSE)
			{
				$xmlData .= '<InformationSystem id="' . $row['information_systems_id'] . '">' . "\n";
				$xmlData .= '<name>' . str_for_xml($row['information_systems_name']) . '</name>' . "\n";
				$xmlData .= '<description>' . str_for_xml($row['information_systems_description']) . '</description>' . "\n";

				// Получаем глобально-доступный объект структуры
				$structure = & singleton('Structure');

				$url = $structure->GetStructurePath($row['structure_id'], 0);

				if ($url != '/')
				{
					$url = '/' . $url;
				}

				$xmlData .= '<url>' . str_for_xml($url) . '</url>' . "\n";
				$xmlData .= '<access>' . str_for_xml($row['information_systems_access']) . '</access>' . "\n";

				$xmlData .= $this->GetXml4Tags($InformationSystemId, $property);

				$xmlData .= '</InformationSystem>' . "\n";
			}
		}

		$xmlData .= '</document>' . "\n";

		$xsl = & singleton('xsl');
		$result = $xsl->build($xmlData, $xsl_name);

		/* Проверяем, начинали ли мы кэширование*/
		if (isset($property['cache']) && $property['cache'])
		{
			if (class_exists('Cache'))
			{
				$cache->Insert($cache_element_name, $result, $cache_name);
			}
		}

		// Печатаем результат
		echo $result;

		return TRUE;
	}

	/**
	 * Генерация XML для облака тегов информационной системы
	 *
	 * @param int $InformationSystemId идентификатор информационной системы
	 * @param array $property массив дополнительных атрибутов
	 * - $property['begin'] начальная позиция отображения тегов (по умолчанию 0)
	 * - $property['count'] количество отображаемых тегов
	 * - $property['mas_groups_id'] массив идентификаторов информационных групп, используемые для получения списка тегов элементов, входящих в группы
	 * - $property['TagsOrder'] параметр, определяющий порядок сортировки тегов. Принимаемые значения: ASC - по возрастанию (по умолчанию), DESC - по убыванию
	 * - $property['TagsOrderField'] поле сортировки тегов, если случайная сортировка, то записать RAND(). по умолчанию теги сортируются по названию.
	 * - $property['tags_group_id'] идентификатор или массив идентификаторов групп тегов, из которых необходимо вести отбор тегов
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $InformationSystemId = 1;
	 * $property['count'] = 10;
	 *
	 * $xml = $InformationSystem->GetXml4Tags($InformationSystemId, $property);
	 *
	 * // Распечатаем результат
	 * echo htmlspecialchars($xml);
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function GetXml4Tags($InformationSystemId, $property)
	{
		$InformationSystemId = intval($InformationSystemId);

		$queryBuilder = Core_QueryBuilder::select(
			array('COUNT(tag_id)', 'count'), array('tags.id', 'tag_id')
		);

		$property['mas_groups_id'] = Core_Type_Conversion::toArray($property['mas_groups_id']);

		if (!isset($property['show_item_type']))
		{
			$property['show_item_type'] = array('active');
		}

		// Задан список идентификаторов групп
		if (count($property['mas_groups_id']) > 0)
		{
			$queryBuilder->where('informationsystem_items.informationsystem_group_id', 'IN', $property['mas_groups_id']);
		}

		if (isset($property['tags_group_id']))
		{
			if (is_array($property['tags_group_id']) && count($property['tags_group_id']) > 0)
			{
				$tags_group_array = array();

				foreach($property['tags_group_id'] as $tags_group_id)
				{
					$tags_group_array[] = intval($tags_group_id);
				}

				$queryBuilder->where('tag_dir_id', 'IN', $tags_group_array);
			}
			else
			{
				$tags_group_id = intval($property['tags_group_id']);
				$queryBuilder->where('tag_dir_id', '=', $tags_group_id);
			}
		}

		// Определяем ID элементов, которые не надо включать в выдачу
		if (isset($property['NotIn']))
		{
			// Разбиваем переданные параметры и копируем в массив
			$not_in_mass = explode(',', $property['NotIn']);

			// Количество элементов массива
			$not_in_mass_count = count($not_in_mass);

			// Приводим все данные к (int)
			for ($i = 0; $i < $not_in_mass_count; $i++)
			{
				$not_in_mass[$i] = intval($not_in_mass[$i]);
			}

			$queryBuilder->where('informationsystem_items.id', 'NOT IN', $not_in_mass);
		}

		if (class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$site_user_id = $SiteUsers->GetCurrentSiteUser();
		}
		else
		{
			$site_user_id = 0;
		}

		$mas_result = $this->GetSiteUsersGroupsForUser($site_user_id);

		// Внешний запрос ограничения по активности и датам публикации на основе переданных параметров
		$select_inf_item_sql = '';

		// Если только активные (без неактивных)
		if (in_array('active', $property['show_item_type']) && !in_array('inactive', $property['show_item_type']))
		{
			$queryBuilder->where('informationsystem_items.active', '=', 1);
		}
		// только неактивные
		elseif (in_array('inactive', $property['show_item_type']) && !in_array('active', $property['show_item_type']))
		{
			$queryBuilder->where('informationsystem_items.active', '=', 0);
		}

		$current_date = date('Y-m-d H:i:s');
		// Если не содержит putend_date - ограничиваем по дате окончания публикации
		if (!in_array('putend_date', $property['show_item_type']))
		{
			$queryBuilder
				->open()
				->where('end_datetime', '>=', $current_date)
				->setOr()
				->where('end_datetime', '=', '0000-00-00 00:00:00')
				->close();
		}

		// если не содержит putoff_date - ограничиваем по дате начала публикации
		if (!in_array('putoff_date', $property['show_item_type']))
		{
			$queryBuilder->where('start_datetime', '<=', $current_date);
		}

		if (isset($property['count']))
		{
			$begin = Core_Type_Conversion::toInt($property['begin']);
			$count = Core_Type_Conversion::toInt($property['count']);

			$queryBuilder->limit($begin, $count);
		}

		// Поле сортировки
		if (isset($property['TagsOrderField']))
		{
			$order_field = $property['TagsOrderField'];
		}
		else
		{
			$order_field = 'tags.name';
		}

		// Не задана случайная сортировка
		if (mb_strtoupper($order_field) != 'RAND()')
		{
			if (isset($property['TagsOrder']))
			{
				$order = $property['TagsOrder'];
			}
			else
			{
				$order = 'ASC';
			}
		}
		else
		{
			$order = 'ASC';
		}

		// Выборку всех данных об элементах здесь не делаем, т.к. это потребует очень большой времменной таблицы
		// и намного замедлит выполнение запроса

		$queryBuilder
			->from('tag_informationsystem_items')
			->leftJoin('informationsystem_items', 'tag_informationsystem_items.informationsystem_item_id', '=', 'informationsystem_items.id')
			->leftJoin('informationsystem_groups', 'informationsystem_items.informationsystem_group_id', '=', 'informationsystem_groups.id',
				array(
					array('AND' => array('informationsystem_groups.siteuser_group_id', 'IN', $mas_result)),
					array('AND' => array('informationsystem_groups.deleted', '=', 0))
				)
			)
			->leftJoin('tags', 'tag_informationsystem_items.tag_id', '=', 'tags.id')
			->where('informationsystem_items.informationsystem_id', '=', $InformationSystemId)
			->where('informationsystem_items.siteuser_group_id', 'IN', $mas_result)
			->where('informationsystem_items.deleted', '=', 0)
			->where('tags.deleted', '=', 0)
			->groupBy('tag_informationsystem_items.tag_id')
			->having('count', '>', 0)
			->orderBy($order_field, $order);

		$aResultTags = $queryBuilder->execute()->asAssoc()->result();

		$xmlData = '<tags>' . "\n";

		if ($aResultTags)
		{
			if (class_exists('Tag'))
			{
				$oTag = & singleton('Tag');

				$aTags = array();

				foreach($aResultTags as $aResultTag)
				{
					$aTags[] = $aResultTag['tag_id'];
				}

				$oTag->GetTags($aTags);

				foreach($aResultTags as $aResultTag)
				{
					// генерация XML для информационных элементов
					$xmlData .= $oTag->GenXmlForTag($aResultTag['tag_id'], FALSE, $aResultTag['count']);
				}
			}
		}
		$xmlData .= '</tags>' . "\n";

		return $xmlData;
	}

	/**
	 * Получение информации об информационной системе, связанной с узлом структуры
	 *
	 * @param int $structure_id идентификатор узла структуры
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $structure_id = 16;
	 *
	 * $information_systems_id = $InformationSystem->GetInformationSystemByStructureId($structure_id);
	 *
	 * // Распечатаем результат
	 * echo $information_systems_id;
	 * ?>
	 * </code>
	 * @return int идентификатор информационной системе или FALSE
	 */
	function GetInformationSystemByStructureId($structure_id)
	{
		$structure_id = intval($structure_id);

		$oInformationsystem = Core_Entity::factory('InformationSystem')
			->getByStructureId($structure_id);

		if ($oInformationsystem)
		{
			return $oInformationsystem->id;
		}

		return FALSE;
	}

	/**
	 * Инкрементирование счетчика показов информационного элемента
	 *
	 * @param int $information_item_id идентификатор информационного элемента
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_item_id = 1;
	 *
	 * $result = $InformationSystem->InformationItemIncShowCount($information_item_id);
	 *
	 * if ($result)
	 * {
	 * 	echo 'Счетчик показов информационного элемента инкрементирован';
	 * }
	 * else
	 * {
	 * 	echo 'Ошибка! Счетчик показов информационного элемента не инкрементирован!';
	 * }
	 *
	 * ?>
	 * </code>
	 */
	function InformationItemIncShowCount($information_item_id)
	{
		$information_item_id = intval($information_item_id);

		$oInformationsystem_Item = Core_Entity::factory('Informationsystem_Item')->find($information_item_id);

		if (!is_null($oInformationsystem_Item->id))
		{
			$oInformationsystem_Item->showed++;
			$oInformationsystem_Item->save();
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Получение информации о группе дополнительных свойств информационных элементов
	 *
	 * @param int $information_propertys_items_dir_id Идентификатор группы информационных элементов
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_items_dir_id = 3;
	 *
	 * $row = $InformationSystem->GetPropertysItemsDir($information_propertys_items_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed Массив данных, либо FALSE
	 */
	function GetPropertysItemsDir($information_propertys_items_dir_id)
	{
		$information_propertys_items_dir_id = intval($information_propertys_items_dir_id);

		$oProperty_Dir = Core_Entity::factory('Property_Dir')->find($information_propertys_items_dir_id);

		if (!is_null($oProperty_Dir->id))
		{
			return $this->getArrayItemPropertyDir($oProperty_Dir);
		}

		return FALSE;
	}

	/**
	 * Добавление информации о группе дополнительных свойств информационных элементов
	 *
	 * @param array $param Массив параметров
	 * - $param['information_propertys_items_dir_id'] Идентификатор группы дополнительных свойств информационных элементов
	 * - $param['information_systems_id'] Идентификатор информационной системы
	 * - $param['information_propertys_items_dir_parent_id'] Идентификатор родительской группы дополнительных свойств информационных элементов
	 * - $param['information_propertys_items_dir_name'] Название группы дополнительных свойств информационных элементов
	 * - $param['information_propertys_items_dir_description'] Описание группы дополнительных свойств информационных элементов
	 * - $param['information_propertys_items_dir_order'] Порядок сортировки группы дополнительных свойств информационных элементов
	 * - $param['users_id'] Идентификатор пользователя центра администрирования, создавшего элемент
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param['information_systems_id'] = 1;
	 * $param['information_propertys_items_dir_parent_id'] = 3;
	 * $param['information_propertys_items_dir_name'] = 'Новая группа';
	 * $param['information_propertys_items_dir_description'] = 'Описание группы дополнительных свойств информационных элементов';
	 * $param['information_propertys_items_dir_description'] = 'Описание группы дополнительных свойств информационных элементов';
	 * $param['information_propertys_items_dir_order'] = 10;
	 *
	 * $newid = $InformationSystem->InsertPropertysItemsDir($param);
	 *
	 * // Распечатаем результат
	 * if ($newid)
	 * {
	 * 	echo 'Группа дополнительных свойств информационных элементов добавлена';
	 * }
	 * else
	 * {
	 * 	echo 'Ошибка! Группа дополнительных свойств информационных элементов не добавлена!';
	 * }
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленной записи, либо FALSE
	 */
	function InsertPropertysItemsDir($param)
	{
		if (!isset($param['information_propertys_items_dir_id']) || !$param['information_propertys_items_dir_id'])
		{
			$param['information_propertys_items_dir_id'] = NULL;
		}

		$oProperty_Dir = Core_Entity::factory('Property_Dir', $param['information_propertys_items_dir_id']);

		if (isset($param['information_propertys_items_dir_parent_id']))
		{
			$oProperty_Dir->parent_id = intval($param['information_propertys_items_dir_parent_id']);
		}

		if (isset($param['information_propertys_items_dir_name']))
		{
			$oProperty_Dir->name = $param['information_propertys_items_dir_name'];
		}

		if (isset($param['information_propertys_items_dir_description']))
		{
			$oProperty_Dir->description = $param['information_propertys_items_dir_description'];
		}

		if (isset($param['information_propertys_items_dir_order']))
		{
			$oProperty_Dir->sorting = intval($param['information_propertys_items_dir_order']);
		}

		if (is_null($oProperty_Dir->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oProperty_Dir->user_id = intval($param['users_id']);
		}

		$oProperty_Dir->save();

		if (isset($param['information_systems_id']))
		{
			$oPropertyDir->Informationsystem_Item_Property_Dir->informationsystem_id = intval($param['information_systems_id']);
			$oPropertyDir->Informationsystem_Item_Property_Dir->save();
		}

		return $oProperty_Dir->id;
	}

	/**
	 * Удаление информации о группе дополнительных свойств информационных элементов
	 *
	 * @param int $information_propertys_items_dir_id Идентификатор группы дополнительных свойств информационных элементов
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_items_dir_id = 2;
	 *
	 * $resource = $InformationSystem->DeletePropertysItemsDir($information_propertys_items_dir_id);
	 *
	 * // Распечатаем результат
	 * if ($resource)
	 * {
	 * 	echo 'Группа дополнительных свойств информационных элементов удалена';
	 * }
	 * else
	 * {
	 * 	echo 'Ошибка! Группа дополнительных свойств информационных элементов не удалена!';
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function DeletePropertysItemsDir($information_propertys_items_dir_id)
	{
		$information_propertys_items_dir_id = intval($information_propertys_items_dir_id);

		Core_Entity::factory('Property_Dir', $information_propertys_items_dir_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Получение информации обо всех группах дополнительных свойств информационных элементов конкретной информационной системы
	 *
	 * @param int $information_systems_id Идентификатор информационной системы
	 * @param array $param массив параметров
	 * - $param['parent_properties_items_dir_id'] идентификатор группы дополнительных свойств информационных элементов, информацию о подгруппах которой необходимо получить.
	 * <br /> по умолчанию равен FALSE - получаем информацию о всех группах дополнительных свойств информационных элементов.
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_systems_id = 1;
	 *
	 * $resource = $InformationSystem->GetAllPropertysItemsDirForInformationSystem($information_systems_id);
	 *
	 * // Распечатаем результат
	 * while($row = mysql_fetch_assoc($resource))
	 * {
	 * 	print_r($row);
	 * }
	 * ?>
	 * </code>
	 * @return mixed resource или FALSE
	 */
	function GetAllPropertysItemsDirForInformationSystem($information_systems_id, $param = array())
	{
		$information_systems_id	= intval($information_systems_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('property_dirs.id', 'information_propertys_items_dir_id'),
			array('informationsystem_id', 'information_systems_id'),
			array('parent_id', 'information_propertys_items_dir_parent_id'),
			array('name', 'information_propertys_items_dir_name'),
			array('description', 'information_propertys_items_dir_description'),
			array('sorting', 'information_propertys_items_dir_order'),
			array('user_id', 'users_id')
		)
			->from('property_dirs')
			->join('informationsystem_item_property_dirs', 'property_dirs.id', '=', 'informationsystem_item_property_dirs.property_dir_id')
			->where('deleted', '=', 0)
			->where('informationsystem_id', '=', $information_systems_id)
			->orderBy('sorting');

		if (isset($param['parent_properties_items_dir_id'])
		&& $param['parent_properties_items_dir_id'] !== FALSE)
		{
			$queryBuilder->where('parent_id', '=', intval($param['parent_properties_items_dir_id']));
		}

		$result = $queryBuilder->execute()->asAssoc()->getResult();

		return $result;
	}

	/**
	 * Построение пути от конкретной группы дополнительных свойств информационных элементов до корня
	 *
	 * @param int $information_propertys_items_dir_id Идентификатор группы дополнительных свойств информационных элементов
	 * @param boolean $first_call параметр, определяющий первый ли это вызов метода
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_items_dir_id = 4;
	 *
	 * $row = $InformationSystem->GetAdditionalPropertyPathArray($information_propertys_items_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив элементов пути
	 */
	function GetAdditionalPropertyPathArray($information_propertys_items_dir_id, $first_call = TRUE)
	{
		$information_propertys_items_dir_id = intval($information_propertys_items_dir_id);

		$first_call = Core_Type_Conversion::toBool($first_call);

		if ($first_call)
		{
			// Обнуляем массив.
			$this->buffer = array();
		}

		$information_properties_items_dir_row = $this->GetPropertysItemsDir($information_propertys_items_dir_id);

		if ($information_properties_items_dir_row)
		{
			$this->buffer = $this->GetAdditionalPropertyPathArray($information_properties_items_dir_row['information_propertys_items_dir_parent_id'], FALSE);
			$this->buffer[$information_propertys_items_dir_id] = $information_properties_items_dir_row['information_propertys_items_dir_name'];
		}
		else
		{
			$this->buffer[0] = '';
			unset($this->buffer[0]);
		}

		return $this->buffer;
	}

	/**
	 * Построение пути от конкретной группы дополнительных свойств информационных групп до корня
	 *
	 * @param int $information_propertys_items_dir_id Идентификатор группы дополнительных свойств информационных элементов
	 * @param boolean $first_call Первый ли это вызов функции
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_groups_dir_id = 3;
	 *
	 * $row = $InformationSystem->GetAdditionalPropertyPathArrayGroup($information_propertys_groups_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив элементов пути
	 */
	function GetAdditionalPropertyPathArrayGroup($information_propertys_groups_dir_id, $first_call = TRUE)
	{
		$information_propertys_groups_dir_id = intval($information_propertys_groups_dir_id);

		$first_call = Core_Type_Conversion::toBool($first_call);

		if ($first_call)
		{
			// Обнуляем массив.
			$this->buffer = array();
		}

		$information_properties_groups_dir_row = $this->GetPropertyGroupsDir($information_propertys_groups_dir_id);

		if ($information_properties_groups_dir_row)
		{
			$this->buffer = $this->GetAdditionalPropertyPathArrayGroup($information_properties_groups_dir_row['information_propertys_groups_dir_parent_id'], FALSE);
			$this->buffer[$information_propertys_groups_dir_id] = $information_properties_groups_dir_row['information_propertys_groups_dir_name'];
		}
		else
		{
			$this->buffer[0] = '';
			unset($this->buffer[0]);
		}

		return $this->buffer;
	}

	/**
	 * Формирование массива групп дополнительных свойств информационных элементов самого верхнего уровня для данной информационной системы
	 *
	 * @param int $information_systems_id идентификатор информационной системы, для которой заполняем массив групп самого верхнего уровня
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_systems_id = 1;
	 *
	 * $row = $InformationSystem->FillMasGroupExtProperty($information_systems_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @param array $param массив дополнительных параметров
	 */
	function FillMasGroupExtProperty($information_systems_id, $param = array())
	{
		if ($information_systems_id !== FALSE)
		{
			$information_systems_id = intval($information_systems_id);
		}

		$this->mas_groups_dir = array();

		$this->CachePropertiesItemsDir[$information_systems_id] = array();

		$result = $this->GetAllPropertysItemsDirForInformationSystem($information_systems_id);

		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$this->mas_groups_dir[$row['information_propertys_items_dir_id']] = $row;

				$this->CachePropertiesItemsDir[$information_systems_id][$row['information_propertys_items_dir_parent_id']][] = $row['information_propertys_items_dir_id'];
			}
		}

		return $this->mas_groups_dir;
	}

	/**
	 * Формирование дерева групп для информационной системы
	 *
	 * @param int $information_propertys_items_dir_parent_id идентификатор группы, относительно которой строится дерево групп
	 * @param int $information_systems_id идентификатор информационной системы, для которой строится дерево групп
	 * @param string $separator символ, отделяющий группу нижнего уровня от родительской группы
	 * @param int $information_propertys_items_dir_id идентификатор группы, которую вместе с ее подгруппами не нужно включать в дерево групп, если id = FALSE, то включать в дерево групп все подгруппы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_items_dir_parent_id = 3;
	 * $information_systems_id = 1;
	 * $separator = '';
	 *
	 * $row = $InformationSystem->GetDelimitedGroupsExtProperty($information_propertys_items_dir_parent_id, $information_systems_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array двумерный массив, содержащий дерево подгрупп.
	 */
	function GetDelimitedGroupsExtProperty($information_propertys_items_dir_parent_id, $information_systems_id, $separator='', $information_propertys_item_dir_id = FALSE)
	{
		$information_propertys_items_dir_parent_id = intval($information_propertys_items_dir_parent_id);
		$information_systems_id = intval($information_systems_id);
		$separator = quote_smart($separator);

		if (!isset($this->CachePropertiesItemsDir[$information_systems_id]))
		{
			$this->FillMasGroupExtProperty($information_systems_id, array('cache_off' => TRUE));
		}

		if (isset($this->CachePropertiesItemsDir[$information_systems_id][$information_propertys_items_dir_parent_id]))
		{

			foreach ($this->CachePropertiesItemsDir[$information_systems_id][$information_propertys_items_dir_parent_id] as $information_propertys_items_dir_id)
			{
				$row = $this->GetPropertysItemsDir($information_propertys_items_dir_id);

				if ($information_propertys_item_dir_id != $row['information_propertys_items_dir_id'])
				{
					$count_mas = count($this->mas_property_dir_groups);
					$row['separator'] = $separator;
					$this->mas_property_dir_groups[$count_mas] = $row;

					$this->GetDelimitedGroupsExtProperty($row['information_propertys_items_dir_id'], $information_systems_id, $separator.$separator, $information_propertys_item_dir_id);
				}
			}
		}

		return $this->mas_property_dir_groups;
	}

	/**
	 * Получение информации о группе дополнительных свойств групп информационной системы
	 *
	 * @param int $information_propertys_groups_dir_id Идентификатор группы дополнительных свойств групп информационных систем
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_groups_dir_id = 3;
	 *
	 * $row = $InformationSystem->GetPropertyGroupsDir($information_propertys_groups_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив с информацией о группе дополнительных свойств информационных групп
	 */
	function GetPropertyGroupsDir($information_propertys_groups_dir_id)
	{
		$information_propertys_groups_dir_id = intval($information_propertys_groups_dir_id);

		$oPropertyDir = Core_Entity::factory('Property_Dir')->find($information_propertys_groups_dir_id);

		if (!is_null($oPropertyDir->id))
		{
			return $this->getArrayGroupPropertyDir($oPropertyDir);
		}

		return FALSE;
	}

	/**
	 * Добавление информации о группе дополнительных свойств групп информационной системы
	 *
	 * @param array $param Массив параметров
	 * - array['information_propertys_groups_dir_id'] идентификатор группы дополнительных свойств групп информационных систем
	 * - array['information_systems_id'] идентификатор информационной системы
	 * - array['information_propertys_groups_dir_parent_id'] идентификатор информационной родительской группы дополнительных свойств
	 * - array['information_propertys_groups_dir_name'] название группы дополнительных свойств
	 * - array['information_propertys_groups_dir_description'] описание группы дополнительных свойств
	 * - array['information_propertys_groups_dir_order'] порядок сортировки группы дополнительных свойств
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $param['information_systems_id'] = 1;
	 * $param['information_propertys_items_dir_parent_id'] = 3;
	 * $param['information_propertys_groups_dir_name'] = 'Новая группа';
	 * $param['information_propertys_groups_dir_description'] = 'Описание группы дополнительных свойств';
	 * $param['information_propertys_groups_dir_order'] = 10;
	 *
	 * $newid = $InformationSystem->InsertPropertysItemsDir($param);
	 *
	 * // Распечатаем результат
	 * if ($newid)
	 * {
	 * 	echo 'Группа дополнительных свойств информационных групп добавлена';
	 * }
	 * else
	 * {
	 * 	echo 'Ошибка! Группа дополнительных свойств информационных групп не добавлена!';
	 * }
	 * ?>
	 * </code>
	 * @return mixed идентификатор вставленной записи, либо FALSE
	 */
	function InsertPropertyGroupsDir($param)
	{
		if (!isset($param['information_propertys_groups_dir_id']) || !$param['information_propertys_groups_dir_id'])
		{
			$param['information_propertys_groups_dir_id'] = NULL;
		}

		$oProperty_Dir = Core_Entity::factory('Property_Dir', $param['information_propertys_groups_dir_id']);

		if (isset($param['information_propertys_groups_dir_parent_id']))
		{
			$oProperty_Dir->parent_id = intval($param['information_propertys_groups_dir_parent_id']);
		}

		if (isset($param['information_propertys_groups_dir_name']))
		{
			$oProperty_Dir->name = $param['information_propertys_groups_dir_name'];
		}

		if (isset($param['information_propertys_groups_dir_description']))
		{
			$oProperty_Dir->description = $param['information_propertys_groups_dir_description'];
		}

		if (isset($param['information_propertys_groups_dir_order']))
		{
			$oProperty_Dir->sorting = intval($param['information_propertys_groups_dir_order']);
		}

		if (is_null($oProperty_Dir->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oProperty_Dir->user_id = intval($param['users_id']);
		}

		$oProperty_Dir->save();

		if (isset($param['information_systems_id']))
		{
			$oPropertyDir->Informationsystem_Group_Property_Dir->informationsystem_id = intval($param['information_systems_id']);
			$oPropertyDir->Informationsystem_Group_Property_Dir->save();
		}

		return $oProperty_Dir->id;
	}

	/**
	 * Удаление информации о группе дополнительных свойств групп информационной системы
	 *
	 * @param int $information_propertys_groups_dir_id
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_groups_dir_id = 3;
	 *
	 * $result = $InformationSystem->DeletePropertyGroupsDir($information_propertys_groups_dir_id);
	 *
	 * if ($result)
	 * {
	 * 	echo "Группа дополнительных cвойств информационных групп удалена";
	 * }
	 * else
	 * {
	 * 	echo "Ошибка! Группа дополнительных cвойств информационных групп не удалена!";
	 * }
	 * ?>
	 * </code>
	 * @return resource
	 */
	function DeletePropertyGroupsDir($information_propertys_groups_dir_id)
	{
		$information_propertys_groups_dir_id = intval($information_propertys_groups_dir_id);
		Core_Entity::factory('Property_Dir', $information_propertys_groups_dir_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Получение информации о всех группах дополнительных свойств групп информационных элементов конкретной информационной системы
	 *
	 * @param int $information_systems_id Идентификатор информационной системы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_systems_id = 1;
	 *
	 * $resource = $InformationSystem->GetAllPropertyGroupsDirForInformationSystem($information_systems_id);
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
	function GetAllPropertyGroupsDirForInformationSystem($information_systems_id, $param = array())
	{
		$information_systems_id = intval($information_systems_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('property_dirs.id', 'information_propertys_groups_dir_id'),
			array('informationsystem_id', 'information_systems_id'),
			array('parent_id', 'information_propertys_groups_dir_parent_id'),
			array('name', 'information_propertys_groups_dir_name'),
			array('description', 'information_propertys_groups_dir_description'),
			array('sorting', 'information_propertys_groups_dir_order'),
			array('user_id', 'users_id')
		)
			->from('property_dirs')
			->join('informationsystem_group_property_dirs', 'property_dirs.id', '=', 'informationsystem_group_property_dirs.property_dir_id')
			->where('deleted', '=', 0)
			->where('informationsystem_id', '=', $information_systems_id)
			->orderBy('sorting');

		if (isset($param['parent_properties_groups_dir_id'])
		&& $param['parent_properties_groups_dir_id'] !== FALSE)
		{
			$queryBuilder->where('parent_id', '=', intval($param['parent_properties_groups_dir_id']));
		}

		$result = $queryBuilder->execute()->asAssoc()->getResult();

		return $result;
	}

	/**
	 * Получение иерархического списка групп дополнительных свойств групп информационных элементов
	 *
	 * @param int $information_propertys_groups_dir_parent_id Идентификатор родительской группы
	 * @param int $information_systems_id Идентификатор информационной системы
	 * @param str $separator Разделитель
	 * @param int $information_propertys_groups_dir_id Идентификатор группы
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_propertys_groups_dir_parent_id = 2;
	 * $information_systems_id = 1;
	 * $separator='';
	 *
	 * $row = $InformationSystem->GetDelimitedGroups($information_propertys_groups_dir_parent_id, $information_systems_id, $separator);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив групп, разделенных строкой-разделителем в иерархическом порядке
	 */

	function GetDelimitedGroups($information_propertys_groups_dir_parent_id, $information_systems_id , $separator='', $information_propertys_groups_dir_id = FALSE)
	{
		$information_propertys_groups_dir_parent_id = intval($information_propertys_groups_dir_parent_id);

		$information_systems_id = intval($information_systems_id);

		$separator = quote_smart($separator);

		if (!isset($this->CacheGoupsPropertyIdTree[$information_systems_id]))
		{
			$this->FillMasGroupProperty($information_systems_id, array('cache_off' => TRUE));
		}

		if (isset($this->CacheGoupsPropertyIdTree[$information_systems_id][$information_propertys_groups_dir_parent_id]))
		{
			foreach ($this->CacheGoupsPropertyIdTree[$information_systems_id][$information_propertys_groups_dir_parent_id] as $information_propertys_groups_dir_id2)
			{
				$row = $this->GetPropertyGroupsDir($information_propertys_groups_dir_id2, array('cache_off' => TRUE));

				if ($information_propertys_groups_dir_id !== $row['information_propertys_groups_dir_id'])
				{
					$count_mas = count($this->mas_groups_property);
					$row['separator'] = $separator;
					$this->mas_groups_property[$count_mas] = $row;

					$this->GetDelimitedGroups($row['information_propertys_groups_dir_id'], $information_systems_id, $separator.$separator, $information_propertys_groups_dir_id);
				}
			}
		}

		return $this->mas_groups_property;
	}

	/**
	 * Заполнение массива групп дополнительных свойств групп информационных элементов
	 *
	 * @param int $information_systems_id идентифкатор информационной системы
	 * @param array $param массив дополнительных параметров
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_systems_id = 1;
	 *
	 * $row = $InformationSystem->FillMasGroupProperty($information_systems_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив групп дополнительных свойств
	 */
	function FillMasGroupProperty($information_systems_id, $param = array())
	{
		if ($information_systems_id !== FALSE)
		{
			$information_systems_id = intval($information_systems_id);
		}

		// Очищаем текущий массив
		$this->MasGroupProperty = array();
		$this->CacheGoupsPropertyIdTree[$information_systems_id] = array();

		$result = $this->GetAllPropertyGroupsDirForInformationSystem($information_systems_id);
		if ($result)
		{
			while ($row = mysql_fetch_assoc($result))
			{
				$this->MasGroupProperty[$row['information_propertys_groups_dir_id']] = $row;
				$this->CacheGoupsPropertyIdTree[$information_systems_id][$row['information_propertys_groups_dir_parent_id']][] = $row['information_propertys_groups_dir_id'];
			}
		}

		return $this->MasGroupProperty;
	}

	/**
	 * Генерация XML для групп дополнительных свойств групп информационных элементов
	 *
	 * @param int $information_systems_id Идентификатор информационной системы
	 * @param int $information_propertys_groups_dir_parent_id Идентификатор родительской группы
	 */
	function GenXmlForGroupsPropertyDir($information_systems_id, $information_propertys_groups_dir_parent_id = 0)
	{
		$information_systems_id = Core_Type_Conversion::toInt($information_systems_id);

		$information_propertys_groups_dir_parent_id = Core_Type_Conversion::toInt($information_propertys_groups_dir_parent_id);

		if (isset($this->cache_propertys_groups_dir_tree[$information_systems_id][$information_propertys_groups_dir_parent_id]) && $this->cache_propertys_groups_dir_tree[$information_systems_id][$information_propertys_groups_dir_parent_id] > 0)
		{
			$counter = 0;

			foreach ($this->cache_propertys_groups_dir_tree[$information_systems_id][$information_propertys_groups_dir_parent_id] as $information_propertys_groups_dir_id)
			{
				$shop_properties_groups_dir_row = $this->GetPropertyGroupsDir($information_propertys_groups_dir_id);

				// Генерация XML
				if ($shop_properties_groups_dir_row)
				{
					$this->buffer .= '<properties_groups_dir id="'.$shop_properties_groups_dir_row['information_propertys_groups_dir_id'].'" parent_id="'.str_for_xml($shop_properties_groups_dir_row['information_propertys_groups_dir_parent_id']).'">'."\n";

					$this->buffer .= '<information_systems_id>'.str_for_xml($shop_properties_groups_dir_row['information_systems_id']).'</information_systems_id>'."\n";

					$this->buffer .= '<information_propertys_groups_dir_name>'.str_for_xml($shop_properties_groups_dir_row['information_propertys_groups_dir_name']).'</information_propertys_groups_dir_name>'."\n";

					$this->buffer .= '<information_propertys_groups_dir_description>'.str_for_xml($shop_properties_groups_dir_row['information_propertys_groups_dir_description']).'</information_propertys_groups_dir_description>'."\n";

					$this->buffer .= '<information_propertys_groups_dir_order>'.str_for_xml($shop_properties_groups_dir_row['information_propertys_groups_dir_order']).'</information_propertys_groups_dir_order>'."\n";

					$this->GenXmlForGroupsPropertyDir($information_systems_id, $shop_properties_groups_dir_row['information_propertys_groups_dir_id']);

					$this->buffer .= '</properties_groups_dir>'."\n";
				}
			}
		}
	}

	/**
	 * Формирование XML для информационной системы
	 *
	 * @param int $information_system_id идентификатор информационной системы
	 * @param mixed $information_system_row массив с информацией об информационной системе
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_system_id = 1;
	 *
	 * $xml = $InformationSystem->GenXml4InformationSystem($information_system_id);
	 *
	 * // Распечатаем результат
	 * echo nl2br(htmlspecialchars($xml));
	 * ?>
	 * </code>
	 * @return string XML с данными о информационной системе
	 *
	 */
	function GenXml4InformationSystem($information_system_id, $information_system_row = FALSE)
	{
		if (!$information_system_row)
		{
			$information_system_row = $this->GetInformationSystem($information_system_id);
		}

		$xmlData = '<name>' . str_for_xml($information_system_row['information_systems_name']) . '</name>' . "\n";
		$xmlData .= '<description>' . str_for_xml($information_system_row['information_systems_description']) . '</description>' . "\n";

		$structure = & singleton('Structure');

		$url = $structure->GetStructurePath($information_system_row['structure_id'], 0);

		if ($url != '/')
		{
			$url = '/' . $url;
		}

		$xmlData .= '<url>' . str_for_xml($url) . '</url>' . "\n";
		$xmlData .= '<access>' . str_for_xml($information_system_row['information_systems_access']) . '</access>' . "\n";

		return $xmlData;
	}

	/**
	 * Копирование информационной системы
	 *
	 * @param int $information_system_id идентификатор копируемой информационной системы
	 * @param int $new_site_id идентификатор нового сайта, если не передан копируется в текущий сайт
	 * @param int $structure_id идентификатор узла структуры, которому следует ассоциировать копию информационной системы, если не передан используется "0"
	 * @param bool $copy_with_sns флаг, указывающий копировать ли все блоги, ассоциированные данной информационной системе с сохранением связей, либо не копировать
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_system_id = 1;
	 *
	 * $new_information_system_id = $InformationSystem->CopyInformationSystem($information_system_id);
	 *
	 * if ($new_information_system_id)
	 * {
	 * 	echo 'Информационная система скопирована!';
	 * }
	 * else
	 * {
	 * 	echo 'Ошибка! Информационная система не скопирована!';
	 * }
	 * ?>
	 * </code>
	 * @return mixed идентификатор копии информационной системы или FALSE
	 */
	function CopyInformationSystem($information_system_id, $new_site_id = FALSE, $structure_id = FALSE, $copy_with_sns = FALSE, $information_systems_dir_id = FALSE)
	{
		$oInformationsystem = Core_Entity::factory('Informationsystem')->find($information_system_id);

		if (!is_null($oInformationsystem))
		{
			$oNewInformationsystem = $oInformationsystem->copy();

			return $oNewInformationsystem->id;
		}

		return FALSE;
	}

	/**
	 * Копирование дочерних групп дополнительных свойств информационных элементов
	 *
	 * @param int $propertys_items_dir_parent_id идентификатор родительской группы дополнительных свойств информационных элементов
	 * @param int $information_system_id идентификатор информационной системы
	 * @param int $copy_propertys_items_dir_parent_id идентификатор копии родительской группы дополнительных свойств (служебный параметр)
	 * @param $copy_information_system_id идентификатор копируемой информационной системы
	 *
	 */
	function CopyPropertysItemsDirForInformationSystem($propertys_items_dir_parent_id, $information_system_id = 0, $copy_propertys_items_dir_parent_id = 0, $copy_information_system_id = 0)
	{
		$oInformationsystem_Item_Property_List = Core_Entity::factory('Informationsystem_Item_Property_List', $information_system_id);

		$oProperty_Dir = $oInformationsystem_Item_Property_List
			->Property_Dirs;

		$oProperty_Dir->queryBuilder()->where('parent_id', '=', $propertys_items_dir_parent_id);

		$aProperty_Dirs = $oProperty_Dir->findAll();

		// Меняем linked object, если ИС сменилась
		if ($copy_information_system_id)
		{
			$oInformationsystem_Item_Property_List = Core_Entity::factory('Informationsystem_Item_Property_List', $copy_information_system_id);
		}

		foreach($aProperty_Dirs as $oProperty_Dir)
		{
			$oNewProperty_Dir = $oProperty_Dir->copy();
			$oInformationsystem_Item_Property_List->add($oNewProperty_Dir);
		}
	}

	/**
	 * Получение списка дополнительных свойств информационных элементов по идентификатору группы дополнительных свойств
	 *
	 * @param int $propertys_items_dir_parent_id идентификатор группы дополнительных свойств информационных элементов
	 * @param int $information_system_id идентификатор информационной системы
	 *
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $propertys_items_dir_parent_id = 7;
	 * $resource = $InformationSystem->SelectPropertysItemsByDirParentId($propertys_items_dir_parent_id)
	 *
	 * $row = mysql_fetch_assoc($resource);
	 *
	 * print_r($row);
	 * ?>
	 * </code>
	 *
	 * @return resource
	 *
	 */
	function SelectPropertysItemsByDirParentId($propertys_items_dir_parent_id, $information_system_id = 0)
	{
		$propertys_items_dir_parent_id = intval($propertys_items_dir_parent_id);
		$information_system_id = intval($information_system_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('properties.id', 'information_propertys_id'),
			array('informationsystem_item_properties.informationsystem_id', 'information_systems_id'),
			array('property_dir_id', 'information_propertys_items_dir_id'),
			array('name', 'information_propertys_name'),
			array('type', 'information_propertys_type'),
			array('sorting', 'information_propertys_order'),
			array('default_value', 'information_propertys_define_value'),
			array('tag_name', 'information_propertys_xml_name'),
			array('list_id', 'information_propertys_lists_id'),
			array('properties.informationsystem_id', 'information_propertys_information_systems_id'),
			array('user_id', 'users_id'),
			array('image_large_max_width', 'information_propertys_default_big_width'),
			array('image_small_max_width', 'information_propertys_default_small_width'),
			array('image_large_max_height', 'information_propertys_default_big_height'),
			array('image_small_max_height', 'information_propertys_default_small_height')
		)
			->from('properties')
			->join('informationsystem_item_properties', 'properties.id', '=', 'informationsystem_item_properties.property_id')
			->where('deleted', '=');

		if (!$propertys_items_dir_parent_id)
		{
			// Задана корневая группа дополнительных свойств и не указан идентификатор информационной системы
			if (!$information_system_id)
			{
				return FALSE;
			}
			else
			{
				$queryBuilder->where('informationsystem_id', '=', $information_system_id);
			}
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Копирование дочерних групп дополнительных свойств информационных групп
	 *
	 * @param int $propertys_groups_dir_parent_id идентификатор родительской группы дополнительных свойств информационных групп
	 * @param int $information_system_id идентификатор информационной системы
	 * @param int $copy_propertys_groups_dir_parent_id идентификатор копии родительской группы дополнительных свойств групп (служебный параметр)
	 * @param $copy_information_system_id идентификатор копируемой информационной системы
	 *
	 */
	function CopyPropertysGroupsDirForInformationSystem($propertys_groups_dir_parent_id, $information_system_id = 0, $copy_propertys_groups_dir_parent_id = 0, $copy_information_system_id = 0)
	{
		$propertys_groups_dir_parent_id = intval($propertys_groups_dir_parent_id);
		$information_system_id = intval($information_system_id);
		//$copy_propertys_groups_dir_parent_id = intval($copy_propertys_groups_dir_parent_id);
		$copy_information_system_id = intval($copy_information_system_id);

		$oInformationsystem_Group_Property_List = Core_Entity::factory('Informationsystem_Item_Property_List', $information_system_id);

		$oProperty_Dir = $oInformationsystem_Group_Property_List
			->Property_Dirs;

		$oProperty_Dir->queryBuilder()->where('parent_id', '=', $propertys_groups_dir_parent_id);

		$aProperty_Dirs = $oProperty_Dir->findAll();

		// Меняем linked object, если ИС сменилась
		if ($copy_information_system_id)
		{
			$oInformationsystem_Group_Property_List = Core_Entity::factory('Informationsystem_Group_Property_List', $copy_information_system_id);
		}

		foreach($aProperty_Dirs as $oProperty_Dir)
		{
			$oNewProperty_Dir = $oProperty_Dir->copy();
			$oInformationsystem_Group_Property_List->add($oNewProperty_Dir);
		}
	}

	/**
	 * Получение списка дополнительных свойств информационных групп по идентификатору группы дополнительных свойств
	 *
	 * @param int $propertys_groups_dir_parent_id идентификатор группы дополнительных свойств информационных групп
	 * @param int $information_system_id идентификатор информационной системы
	 *
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $propertys_groups_dir_parent_id = 7;
	 *
	 * $resource = $InformationSystem->SelectPropertysGroupsByDirParentId($propertys_groups_dir_parent_id);
	 *
	 * while ($row = mysql_fetch_assoc($resource))
	 * {
	 * 		print_r($row);
	 * }
	 * ?>
	 * </code>
	 *
	 * @return resource
	 *
	 */
	function SelectPropertysGroupsByDirParentId($propertys_groups_dir_parent_id, $information_system_id = 0)
	{
		$propertys_groups_dir_parent_id = intval($propertys_groups_dir_parent_id);
		$information_system_id = intval($information_system_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('properties.id', 'information_propertys_groups_id'),
			array('informationsystem_group_properties.informationsystem_id', 'information_systems_id'),
			array('property_dir_id', 'information_propertys_groups_dir_id'),
			array('name', 'information_propertys_groups_name'),
			array('type', 'information_propertys_groups_type'),
			array('sorting', 'information_propertys_groups_order'),
			array('default_value', 'information_propertys_groups_default_value'),
			array('tag_name', 'information_propertys_groups_xml_name'),
			array('list_id', 'information_propertys_groups_lists_id'),
			array('properties.informationsystem_id', 'information_propertys_groups_information_systems_id'),
			array('user_id', 'users_id'),
			array('image_large_max_width', 'information_propertys_groups_big_width'),
			array('image_small_max_width', 'information_propertys_groups_small_width'),
			array('image_large_max_height', 'information_propertys_groups_big_height'),
			array('image_small_max_height', 'information_propertys_groups_small_height')
		)
			->from('properties')
			->join('informationsystem_group_properties', 'properties.id', '=', 'informationsystem_group_properties.property_id')
			->where('deleted', '=', 0);

		if (!$propertys_groups_dir_parent_id)
		{
			// Задана корневая группа дополнительных свойств и не указан идентификатор информационной системы
			if (!$information_system_id)
			{
				return FALSE;
			}
			else
			{
				$queryBuilder->where('informationsystem_id', '=', $information_system_id);
			}
		}
		else
		{
			$queryBuilder->where('property_dir_id', '=', $propertys_groups_dir_parent_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Проверка вхождения пользователя сайта в группу пользователей, связанную с информационной группой
	 *
	 * @param array $param массив параметров
	 * - $param['site_user_id'] идентификатор пользователя сайта
	 * - $param['site_users_group_id'] идентификатор группы пользователей сайта
	 * - $param['information_group_id'] идентификатор информационной группы
	 *
	 * @return boolean
	 */

	function IssetSiteUserInUsersGroupInformationGroup($param)
	{
		$site_user_id = Core_Type_Conversion::toInt($param['site_user_id']);
		$site_users_group_id = Core_Type_Conversion::toInt($param['site_users_group_id']);
		$information_group_id = Core_Type_Conversion::toInt($param['information_group_id']);

		$queryBuilder = Core_QueryBuilder::select()
			->from('siteuser_group_lists')
			->where('siteuser_group_id', '=', $site_users_group_id)
			->where('informationsystem_group_id', 'IN', array(0, $information_group_id));

		$aSiteuser_Group_Lists = $queryBuilder->execute()->asAssoc()->result();

		$result = array();

		foreach($aSiteuser_Group_Lists as $aSiteuser_Group_List)
		{
			// Формируем массив идентификаторов групп пользователей, в которых есть связь с текущим пользователем, без учета активности пользователя в группе
			if(!in_array($aSiteuser_Group_List['siteuser_group_id'], $result))
			{
				$result[] = $aSiteuser_Group_List['siteuser_group_id'];
			}
		}

		return count($result) > 0;
	}

	/**
	 * Добавление/удаление связи пользователя с группой пользователей, связанной с информационной группой
	 *
	 * @param array $param массив параметров
	 * - $param['site_user_id'] идентификатор пользователя сайта
	 * - $param['site_users_group_id'] идентификатор группы пользователей сайта
	 * - $param['information_group_id'] идентификатор информационной группы
	 * - $param['associate'] параметр, определяющий добавить или удалить связь пользователя с группой пользователей (1 - включить в группу, 0 - удалить из группы)
	 *
	 * @return boolean true в случае отсутствия ошибки, FALSE - в противном случае
	 */
	function ChangeAssociateSiteUserWithUsersGroupInformationGroup($param)
	{
		$site_user_id = Core_Type_Conversion::toInt($param['site_user_id']);
		$site_users_group_id = Core_Type_Conversion::toInt($param['site_users_group_id']);
		$information_group_id = Core_Type_Conversion::toInt($param['information_group_id']);
		$associate  = Core_Type_Conversion::toInt($param['associate']);

		if ($associate < 0 || $associate > 1)
		{
			return FALSE;
		}

		$kernel = & singleton('kernel');

		$users_id = $kernel->GetCurrentUser();

		// Определяем входит ли пользователь в группу пользователей
		$user_in_group = $this->IssetSiteUserInUsersGroupInformationGroup($param);

		//$DataBase = & singleton('DataBase');

		// Задана информационная группа
		if ($information_group_id)
		{
			$queryBuilder = Core_QueryBuilder::select(array('COUNT(id)', 'count'))
				->from('siteuser_group_lists')
				->where('site_user_id', '=', $site_user_id)
				->where('siteuser_group_id', '=', $site_users_group_id)
				->where('informationsystem_group_id', '=', $information_group_id);

			$aCountSiteuserGroupLists = $queryBuilder->execute()->asAssoc()->current();

			$count_associated = $aCountSiteuserGroupLists[0];
		}
		else
		{
			$count_associated = 0;
		}

		// Удаляем пользователя сайта из группы пользователей
		if (!$associate)
		{
			// Пользователь не входит в группу пользователей
			if (!$user_in_group)
			{
				return TRUE;
			}
			elseif ($information_group_id) // Пользователь входит в частную группу пользователей для информационной группы
			{
				// В таблице доступа есть запись для информационной группы - удаляем ее
				if ($count_associated)
				{
					$queryBuilderDelete = Core_QueryBuilder::delete('siteuser_group_lists')
						->where('site_user_id', '=', $site_user_id)
						->where('siteuser_group_id', '=', $site_users_group_id)
						->where('informationsystem_group_id', '=', $information_group_id);

					$queryBuilderDelete->execute();

					return TRUE;
				}
			}
		}
		else // Добавляем пользователя в группу
		{
			// Пользователь не входит в группу пользователей
			if (!$user_in_group)
			{
				// В таблице доступа отсутствует запись для информационной группы - вставляем ее
				if (!$count_associated)
				{
					$queryBuilderInsert = Core_QueryBuilder::insert('siteuser_group_lists')
						->columns('site_user_id', 'siteuser_group_id', 'informationsystem_group_id')
						->values($site_user_id, $site_users_group_id, $information_group_id);

					$queryBuilderInsert->execute();

					return TRUE;
				}
			}
			else // Пользователь входит в группу
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Получение пути хранения файлов информационного элемента
	 *
	 * @param $information_item_id идентификатор информационного элемента
	 * @param $information_item_row массив с данными об информационном элементе
	 * @return mixed путь к папке информационного элемента или ложь, если информационного элемента не существует
	 */
	function GetInformationItemDir($information_item_id, $information_item_row = FALSE)
	{
		$information_item_id = intval($information_item_id);

		if (!$information_item_row)
		{
			$information_item_row = $this->GetInformationSystemItem($information_item_id);
		}

		if ($information_item_row)
		{
			$kernel = & singleton('kernel');
			$site = new site();

			// Константа UPLOADDIR не определена
			if (!defined('UPLOADDIR'))
			{
				// Получаем информацию о информационной системе
				$informatio_system_row = $this->GetInformationSystem($information_item_row['information_systems_id']);

				$site_row = $site->GetSite($informatio_system_row['site_id']);

				$uploaddir = $site_row['site_uploaddir'];
			}
			else
			{
				$uploaddir = UPLOADDIR;
			}

			// Константа SITE_NESTING_LEVEL не определена
			if (!defined('SITE_NESTING_LEVEL'))
			{
				if (!isset($site_row))
				{
					// Получаем информацию о информационной системе
					$informatio_system_row = $this->GetInformationSystem($information_item_row['information_systems_id']);

					$site_row = $site->GetSite($informatio_system_row['site_id']);
				}

				$site_nesting_level = $site_row['site_nesting_level'];
			}
			else
			{
				$site_nesting_level = SITE_NESTING_LEVEL;
			}


			return $uploaddir . 'information_system_' . Core_Type_Conversion::toInt($information_item_row['information_systems_id']) . '/' . $kernel->GetDirPath($information_item_id, $site_nesting_level) . '/item_' . $information_item_id . '/';
		}

		return FALSE;
	}

	/**
	 * Получение пути хранения файлов информационной группы
	 *
	 * @param $information_group_id идентификатор информационной группы
	 * @return mixed путь к папке информационной группы или ложь, если информацтонной группы не существует
	 */
	function GetInformationGroupDir($information_group_id)
	{
		$information_group_id = intval($information_group_id);

		if ($information_group_row = $this->GetInformationGroup($information_group_id))
		{
			$site = new site();

			// Константа UPLOADDIR не определена
			if (!defined('UPLOADDIR'))
			{
				// Получаем информацию о информационной системе
				$informatio_system_row = $this->GetInformationSystem($information_group_row['information_systems_id']);

				$site_row = $site->GetSite($informatio_system_row['site_id']);

				$uploaddir = $site_row['site_uploaddir'];
			}
			else
			{
				$uploaddir = UPLOADDIR;
			}

			// Константа SITE_NESTING_LEVEL не определена
			if (!defined('SITE_NESTING_LEVEL'))
			{
				if (!isset($site_row))
				{
					// Получаем информацию о информационной системе
					$informatio_system_row = $this->GetInformationSystem($information_group_row['information_systems_id']);

					$site_row = $site->GetSite($informatio_system_row['site_id']);
				}

				$site_nesting_level = $site_row['site_nesting_level'];
			}
			else
			{
				$site_nesting_level = SITE_NESTING_LEVEL;
			}

			$kernel = & singleton('kernel');
			return $uploaddir . 'information_system_' . Core_Type_Conversion::toInt($information_group_row['information_systems_id']) . '/' . $kernel->GetDirPath($information_group_id, $site_nesting_level) . '/group_' . $information_group_id . '/';
		}

		return FALSE;
	}

	/**
	 * Копирование дополнительного свойства информационных элементов
	 *
	 * @param $items_property_id идентификатор дополнительного свойства информационных элементов
	 * @param $information_system_id идентификатор информационной системы, в которую будет скопировано дополнительное свойство.
	 * <br />По умолчанию равен FALSE - используется информационная система, к которой принадлежит копируемое дополнительное свойство.
	 * @return mixed идентификатор копии дополнительного свойства информационных элементов в случае успешного выполнения, FALSE - в противном случае
	 */
	function CopyItemsProperty($items_property_id, $information_system_id = FALSE)
	{
		$items_property_id = intval($items_property_id);

		$oProperty = Core_Entity::factory('Property')->find($items_property_id);

		if (!is_null($oProperty->id))
		{
			if (!$information_system_id)
			{
				$information_system_id = $oProperty->InformationSystem->id;
			}

			$oInformationsystem_Item_Property_List = Core_Entity::factory('Informationsystem_Item_Property_List', $information_system_id);

			$oNewProperty =	$oProperty->copy(FALSE);
			$oInformationsystem_Item_Property_List->add($oNewProperty);

			return $oNewProperty->id;
		}

		return FALSE;
	}

	/**
	 * Копирование дополнительного свойства информационных групп
	 *
	 * @param $groups_property_id идентификатор дополнительного свойства информационных групп
	 * @param $information_system_id идентификатор информационной системы, в которую будет скопировано дополнительное свойство.
	 * <br />По умолчанию равен FALSE - используется информационная система, к которой принадлежит копируемое дополнительное свойство.
	 * @return mixed идентификатор копии дополнительного свойства информационных групп в случае успешного выполнения, FALSE - в противном случае
	 */
	function CopyGroupsProperty($groups_property_id, $information_system_id = FALSE)
	{
		$groups_property_id = intval($groups_property_id);

		$oProperty = Core_Entity::factory('Property')->find($groups_property_id);

		if (!is_null($oProperty->id))
		{
			if (!$information_system_id)
			{
				$information_system_id = $oProperty->InformationSystem->id;
			}

			$oInformationsystem_Group_Property_List = Core_Entity::factory('Informationsystem_Group_Property_List', $information_system_id);

			$oNewProperty =	$oProperty->copy(FALSE);
			$oInformationsystem_Group_Property_List->add($oNewProperty);

			return $oNewProperty->id;
		}

		return FALSE;
	}

	/**
	* Добавление/обновление раздела информационных систем
	*
	* @param array $param масcив параметров
	* - $param['information_systems_dir_id'] идентификатор редактируемого раздела информационных систем
	* - $param['information_systems_dir_parent_id'] идентификатор родительского раздела информационных систем
	* - $param['information_systems_dir_name'] название раздела информационных систем
	* - $param['information_systems_dir_description'] описание раздела информационных систем
	* - $param['site_id'] идентификатор сайта
	* - $param['users_id'] идентификатор пользователя центра администрирования, если FALSE - берется текущий пользователь.
	*
	* @return mixed идентификатор добавленного/обновленного раздела информационных систем в случае успешного выполнения,  FALSE - в противном случае
	*/
	function InsertInformationSystemsDir($param)
	{
		if (!isset($param['information_systems_dir_id']) || $param['information_systems_dir_id'] == 0)
		{
			$param['information_systems_dir_id'] = NULL;
		}

		$oInformationsystem_Dir = Core_Entity::factory('Informationsystem_Dir', $param['information_systems_dir_id']);

		if (isset($param['information_systems_dir_parent_id']))
		{
			$oInformationsystem_Dir->parent_id = intval($param['information_systems_dir_parent_id']);
		}

		if (isset($param['information_systems_dir_name']))
		{
			$oInformationsystem_Dir->name = $param['information_systems_dir_name'];
		}

		if (isset($param['information_systems_dir_description']))
		{
			$oInformationsystem_Dir->description = $param['information_systems_dir_description'];
		}

		if (isset($param['site_id']))
		{
			$oInformationsystem_Dir->site_id = intval($param['site_id']);
		}

		if (is_null($oInformationsystem_Dir->id) && isset($param['users_id']) && $param['users_id'])
		{
			$oInformationsystem_Dir->user_id = intval($param['users_id']);
		}

		$oInformationsystem_Dir->save();
		return $oInformationsystem_Dir->id;
	}

	/**
	* Получение информации о разделах информационных систем
	*
	* @param array $param массив параметров
	* - $param['information_systems_dir_parent_id'] идентификатор родительского раздела информационных систем. По умолчанию равен 0.
	* - $param['site_id'] идентификатор сайта. По умолчанию имеет значение идентификатора текущего сайта.	*
	*/
	function GetAllInformationSystemsDirs($param = array())
	{
		$information_systems_dir_parent_id = Core_Type_Conversion::toInt($param['information_systems_dir_parent_id']);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'information_systems_dir_id'),
			array('parent_id', 'information_systems_dir_parent_id'),
			array('name', 'information_systems_dir_name'),
			array('description', 'information_systems_dir_description'),
			'site_id',
			array('users_id', 'user_id')
		)->from('informationsystem_dirs')
		->where('deleted', '=', 0)
		->where('parent_id', '=', $information_systems_dir_parent_id);

		if (!$information_systems_dir_parent_id)
		{
			if (!isset($param['site_id']))
			{
				$site_id = CURRENT_SITE;
			}
			else
			{
				$site_id = intval($param['site_id']);
			}

			$queryBuilder->where('site_id', '=', $site_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/*
	* Получение информации о разделе информационных систем
	*
	* @param int $information_systems_dir_id идентификатор раздела информационных систем
	*
	* @return mixed ассоциативный массив с информацией о разделе информационных систем в случае успешного выполнения, FALSE - в противном случае
	*/
	function GetInformationSystemsDir($information_systems_dir_id)
	{
		$information_systems_dir_id = intval($information_systems_dir_id);

		$oInformationsystem_Dir = Core_Entity::factory('Informationsystem_Dir')->find($information_systems_dir_id);

		if (!is_null($oInformationsystem_Dir->id))
		{
			return $this->getArrayInformationsystemDir($oInformationsystem_Dir);
		}

		return FALSE;
	}

	/**
	* Получение информации о информационных системах раздела
	*
	* @param int $information_systems_dir_id идентификатор раздела информационных систем
	*
	* @return resource
	*/
	function GetAllInformationSystemsFromDir($information_systems_dir_id)
	{
		$information_systems_dir_id = intval($information_systems_dir_id);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'information_systems_id'),
			array('informationsystem_dir_id', 'information_systems_dir_id'),
			'structure_id',
			'site_id',
			array('name', 'information_systems_name'),
			array('description', 'information_systems_description'),
			array('items_sorting_direction', 'information_systems_items_order_type'),
			array('items_sorting_field', 'information_systems_items_order_field'),
			array('groups_sorting_direction', 'information_systems_group_items_order_type'),
			array('groups_sorting_field', 'information_systems_group_items_order_field'),
			array('image_large_max_width', 'information_systems_image_big_max_width'),
			array('image_large_max_height', 'information_systems_image_big_max_height'),
			array('image_small_max_width', 'information_systems_image_small_max_width'),
			array('image_small_max_height', 'information_systems_image_small_max_height'),
			array('siteuser_group_id', 'information_systems_access'),
			array('use_captcha', 'information_systems_captcha_used'),
			array('watermark_file', 'information_systems_watermark_file'),
			array('watermark_default_use_large_image', 'information_systems_default_used_watermark'),
			array('watermark_default_use_small_image', 'information_systems_default_used_small_watermark'),
			array('watermark_default_position_x', 'information_systems_watermark_default_position_x'),
			array('watermark_default_position_y', 'information_systems_watermark_default_position_y'),
			array('user_id', 'users_id'),
			array('items_on_page', 'information_systems_items_on_page'),
			array('format_date', 'information_systems_format_date'),
			array('format_datetime', 'information_systems_format_datetime'),
			array('url_type', 'information_systems_url_type'),
			array('typograph_default_items', 'information_systems_typograph_item'),
			array('typograph_default_groups', 'information_systems_typograph_group'),
			array('apply_tags_automatically', 'information_systems_apply_tags_automatic'),
			array('change_filename', 'information_systems_file_name_conversion'),
			array('apply_keywords_automatically', 'information_systems_apply_keywords_automatic'),
			array('group_image_large_max_width', 'information_systems_image_big_max_width_group'),
			array('group_image_large_max_height', 'information_systems_image_big_max_height_group'),
			array('group_image_small_max_width', 'information_systems_image_small_max_width_group'),
			array('group_image_small_max_height', 'information_systems_image_small_max_height_group'),
			array('preserve_aspect_ratio', 'information_systems_default_save_proportions')
		)
		->from('informationsystems')
		->where('deleted', '=', 0)
		->where('informationsystem_dir_id', '=', $information_systems_dir_id);

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	* Удаление раздела информационных систем. Информационные системы, находящиеся в разделе не удаляютя, а переносятся в корневой раздел.
	*
	* @param int $information_systems_dir_id идентификатор раздела информационных систем
	*
	* @return boolean
	*/
	function DeleteInformationSystemsDir($information_systems_dir_id)
	{
		Core_Entity::factory('Informationsystem_Dir', $information_systems_dir_id)->markDeleted();

		return TRUE;
	}

	/**
	 * Построение массива пути от текущего раздела информационных систем к корневому
	 *
	 * @param int $information_systems_dir_id идентификатор раздела информационных систем, для которого необходимо построить путь
	 * @param array $return_path_array
	 * <code>
	 * <?php
	 * $InformationSystem = new InformationSystem();
	 *
	 * $information_systems_dir_id = 12;
	 *
	 * $row = $InformationSystem->GetInformationSystemsDirPathArray($information_systems_dir_id);
	 *
	 * // Распечатаем результат
	 * print_r($row);
	 * ?>
	 * </code>
	 * @return array ассоциативный массив, элементы которого содержат информацию о разделах, составляющих путь от текущего раздела до корневого
	 */
	function GetInformationSystemsDirPathArray($information_systems_dir_id, $return_path_array = array())
	{
		$information_systems_dir_id = intval($information_systems_dir_id);

		if ($information_systems_dir_id != 0)
		{
			$row = $this->GetInformationSystemsDir($information_systems_dir_id);

			$return_path_array[$row['information_systems_dir_id']] = $row;

			$return_path_array = $this->GetInformationSystemsDirPathArray($row['information_systems_dir_parent_id'], $return_path_array);
		}

		return $return_path_array;
	}

	/**
	 * Формирование дерева разделов информационных систем
	 *
	 * @param array $param массив параметров
	 * - $param['information_systems_dir_parent_id'] идентификатор раздела, относительно которого строится дерево групп. По умолчанию равен 0.
	 * - $param['site_id'] идентификатор сайта, для которого строится дерево разделов. По умолчанию равен CURRENT_SITE
	 * - $param['separator'] символ, отделяющий раздел нижнего уровня от родительского раздела
	 * - $param['information_systems_dir_id'] идентификатор раздела, который вместе с его подразделами не нужно включать в дерево разделов, если равен FALSE или не передан, то включать в дерево разделов все разделы.
	 * - $param['array'] - служебный параметр
	 * - $param['sum_separator'] - служебный параметр
	 *
	 * @return array двумерный массив, содержащий дерево подгрупп
	 */
	function GetInformationSystemsDirs($param = array())
	{
		$information_systems_dir_parent_id = Core_Type_Conversion::toInt($param['information_systems_dir_parent_id']);

		if (!isset($param['site_id']))
		{
			$site_id = CURRENT_SITE;
		}
		else
		{
			$site_id = intval($param['site_id']);
		}

		if (!isset($param['separator']))
		{
			$separator = '&nbsp;';
		}
		else
		{
			$separator = quote_smart($param['separator']);
		}

		if (isset($param['information_systems_dir_id']) && $param['information_systems_dir_id'] !== FALSE)
		{
			$information_systems_dir_id = intval($param['information_systems_dir_id']);
		}
		else
		{
			$information_systems_dir_id = FALSE;
		}

		if (!isset($param['sum_separator']))
		{
			$param['sum_separator'] = $separator;
		}
		else
		{
			$param['sum_separator'] = $param['sum_separator'] . $separator;
		}

		$array = array();

		// Получаем информацию о подразделах информационных систем для текущего родительского раздела
		$result = $this->GetAllInformationSystemsDirs(array('information_systems_dir_parent_id' => $information_systems_dir_parent_id, 'site_id' => $site_id));

		// Цикл по подразделам
		while ($row = mysql_fetch_assoc($result))
		{
			if ($information_systems_dir_id != $row['information_systems_dir_id'])
			{
				$row['separator'] = $param['sum_separator'];

				$param['information_systems_dir_parent_id'] = $row['information_systems_dir_id'];

				$array[] = $row;

				// Объединяем выбранные данные с данными из подразделов
				$array = array_merge($array, $this->GetInformationSystemsDirs($param));
			}
		}

		return $array;
	}

	/**
	* Получение родительской группы находящейся в корне для информационной группы
	*
	* @param int $information_group_id идентификатор информационной группы
	* @return mixed информацию о родительской группы или FALSE (информационная группа с переданным идентификатором не существует)
	*/
	function GetTopParentInformationGroup($information_group_id)
	{
		$information_group_id = intval($information_group_id);

		if (!$information_group_id)
		{
			return array('information_groups_id' => 0);
		}

		// Получаем данные о информационной группе
		if (!$group_row = $this->GetInformationGroup($information_group_id))
		{
			return FALSE;
		}

		// Группа находится в корне
		if (!$group_row['information_groups_parent_id'])
		{
			// Возвращаем идентификатор самой группы
			return $group_row;
		}

		// Получаем массив групп от текущей к корневой
		$path_array = $this->GetInformationGroupsPathArray($information_group_id, $group_row['information_systems_id']);

		// Получаем идентификатор родительской группы для текущей
		if (is_array($path_array) && count($path_array) > 0)
		{
			unset($path_array[0]);

			$parent_group_row = end($path_array);

			// Устанавливаем идентификатор родительской группы самого верхнего уровня
			return $parent_group_row;
		}

		return FALSE;
	}

	function CopyInformationSystemDir($param)
	{
		// Получаем начальную группу, с которой нужно начинать копирование
		$information_systems_dir_parent_id = Core_Type_Conversion::toInt($param['information_systems_dir_parent_id']);

		$oInformationsystem_Dir = Core_Entity::factory('Informationsystem_Dir')->find($information_systems_dir_parent_id);

		if (!is_null($oInformationsystem_Dir))
		{
			$oNewInformationsystem_Dir = $oInformationsystem_Dir->copy();

			return $oNewInformationsystem_Dir->id;
		}

		return FALSE;
	}

	/**
	 * Индексирование информационных элементов
	 *
	 * @param int $limit
	 * @param int $on_step
	 * @param int $infitem_id
	 * @return array
	 * @access private
	 */
	function IndexationInfItems($limit, $on_step, $infitem_id = 0)
	{
		if ($infitem_id)
		{
			Core_Entity::factory('Informationsystem_Item', $infitem_id)->indexing();
		}

		return array();
	}

	/**
	 * Индексация информационных групп
	 *
	 * @param int $limit
	 * @param int $on_step
	 * @param int $infgroup_id
	 * @return array
	 * @access private
	 */
	function IndexationInfGroups($limit, $on_step, $infgroup_id = 0)
	{
		if ($infgroup_id)
		{
			Core_Entity::factory('Informationsystem_Group', $infgroup_id)->indexing();
		}

		return array();
	}
}

/**
 * @access private
 */
class information_blocks extends InformationSystem{}
/**
 * @access private
 */
class InformationSystems extends InformationSystem{}