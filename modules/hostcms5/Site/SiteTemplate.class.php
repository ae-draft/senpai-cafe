<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Шаблоны дизайна для сайтов".
 *
 * Файл: /modules/Site/SiteTemplate.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class SiteTemplate
{
	/**
	 * Constructor.
	 */
	function __construct()
	{
		$this->SetTemplatePath(CMS_FOLDER . TMP_DIR);
	}

	/**
	 * Установка прав доступа к создаваемых объектом файлов
	 *
	 * @param int $chmod_file права доступа к создаваемых файлам, например, 0644
	 */
	function SetChmodFile($chmod_file)
	{
		Site_Controller_Template::instance()->chmodFile($chmod_file);
	}

	/**
	 * Получение прав доступа к создаваемым объектом файлов
	 */
	function GetChmodFile()
	{
		return Site_Controller_Template::instance()->chmodFile;
	}

	function GetTemplatesUrlServer()
	{
		return Site_Controller_Template::instance()->server;
	}

	function SetTemplatesUrlServer($sTemplatesUrlServer)
	{
		Site_Controller_Template::instance()->server($sTemplatesUrlServer);
	}

	function GetTemplatePath()
	{
		return Site_Controller_Template::instance()->templatePath;
	}

	function SetTemplatePath($template_path)
	{
		Site_Controller_Template::instance()->templatePath($template_path);
	}

	function GetTemplateFilePath()
	{
		return Site_Controller_Template::instance()->templatePath . Site_Controller_Template::instance()->templateFilename;
	}

	function GetTemplateContentFilePath()
	{
		return Site_Controller_Template::instance()->templatePath . Site_Controller_Template::instance()->templateSelectedFilename;
	}

	function GetTemplateContentXml()
	{
		return Site_Controller_Template::instance()->getSelectedTemplateXml();
	}

	function GetTemplateXml()
	{
		return Site_Controller_Template::instance()->getTemplateXmlArray();
	}

	function GetFields($array)
	{
		return Site_Controller_Template::instance()->getFields($array);
	}

	function MacroReplace($str, $aReplace)
	{
		return Site_Controller_Template::instance()->macroReplace($str, $aReplace);
	}

	function LoadFile($filename, $aReplace = array())
	{
		return Site_Controller_Template::instance()->loadFile($filename, $aReplace);
	}

	/**
	 * Заменяет макросы в уже существующем файле
	 *
	 *  @param $filename путь к файлу
	 *  @param $aReplace массив замен
	 */
	function ReplaceFile($filename, $aReplace = array())
	{
		return Site_Controller_Template::instance()->ReplaceFile($filename, $aReplace);
	}

	/**
	* Определение расширения файла по его названию без расширения
	*
	* @param $path_file_without_extension - путь к файлу без расширения
	*/
	function GetFileExtension($path_file_without_extension)
	{
		return Site_Controller_Template::instance()->getFileExtension($path_file_without_extension);
	}

	/**
	 * Копирование изображений для ИЭ
	 *
	 * @param $is_item_id - идентификатор нового созданого ИЭ
	 * @param $copy_is_id - идентификатор копирумой ИС
	 * @param $copy_item_id - идентификатор копируемого ИЭ
	 */
	function MoveInfItemImage($is_item_id, $copy_is_id, $copy_item_id)
	{
		$InformationSystem = & singleton('InformationSystem');

		$item_dir = $InformationSystem->GetInformationItemDir($is_item_id);
		$this->kernel->PathMkdir($item_dir);

		$param_item = array ();
		$param_item['information_items_id'] = $is_item_id;

		$information_item_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/information_system_{$copy_is_id}/".$this->kernel->GetDirPath($copy_item_id, 3)."/item_{$copy_item_id}/information_items_{$copy_item_id}";

		// Получаем расширение файла
		$ext = $this->GetFileExtension($information_item_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}
		$information_item_image_from .= $ext;

		if (is_file($information_item_image_from))
		{
			$information_item_image_to = $item_dir . "information_items_{$is_item_id}" . $ext;
			copy($information_item_image_from, CMS_FOLDER . $information_item_image_to);

			$param_item['information_items_image'] = basename($information_item_image_to);
		}

		$information_item_small_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/information_system_{$copy_is_id}/".$this->kernel->GetDirPath($copy_item_id, 3)."/item_{$copy_item_id}/small_information_items_{$copy_item_id}";

		// Получаем расширение файла
		$ext = $this->GetFileExtension($information_item_small_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}
		$information_item_small_image_from .= $ext;

		if (is_file($information_item_small_image_from))
		{
			$information_item_small_image_to = $item_dir . "small_information_items_{$is_item_id}" . $ext;
			copy($information_item_small_image_from, CMS_FOLDER . $information_item_small_image_to);

			$param_item['information_items_small_image'] = basename($information_item_small_image_to);
		}

		if (count($param_item) > 1)
		{
			// Обновляем информацио о информационном элементе после создания изображений
			$InformationSystem->InsertInformationItem($param_item);
		}
	}

	/**
	 * Копирование изображений для ИГ
	 *
	 * @param $is_group_id - идентификатор новой созданной ИГ
	 * @param $copy_is_id - идентификатор копирумой ИС
	 * @param $copy_group_id - идентификатор копируемой ИГ
	 */
	function MoveInfGroupImage($is_group_id, $copy_is_id, $copy_group_id)
	{
		$InformationSystem = & singleton('InformationSystem');


		$group_dir = $InformationSystem->GetInformationGroupDir($is_group_id);
		$this->kernel->PathMkdir($group_dir);

		$param_group = array ();
		$param_group['information_groups_id'] = $is_group_id;

		$information_group_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/information_system_{$copy_is_id}/".$this->kernel->GetDirPath($copy_group_id, 3)."/group_{$copy_group_id}/information_groups_{$copy_group_id}";

		// Получаем расширение файла
		$ext = $this->GetFileExtension($information_group_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}
		$information_group_image_from .= $ext;

		if (is_file($information_group_image_from))
		{
			$information_group_image_to = $group_dir . "information_groups_{$is_group_id}" . $ext;
			copy($information_group_image_from, CMS_FOLDER . $information_group_image_to);

			$param_group['information_groups_image'] = basename($information_group_image_to);
		}

		$information_group_small_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/information_system_{$copy_is_id}/".$this->kernel->GetDirPath($copy_group_id, 3)."/group_{$copy_group_id}/small_information_groups_{$copy_group_id}";

		// Получаем расширение файла
		$ext = $this->GetFileExtension($information_group_small_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}
		$information_group_small_image_from .= $ext;

		if (is_file($information_group_small_image_from))
		{
			$information_group_small_image_to = $group_dir . "small_information_groups_{$is_group_id}" . $ext;
			copy($information_group_small_image_from, CMS_FOLDER . $information_group_small_image_to);

			$param_group['information_groups_small_image'] = basename($information_group_small_image_to);
		}

		if (count($param_group) > 1)
		{
			// Обновляем информацио о информационном элементе после создания изображений
			$InformationSystem->InsertInformationGroup($param_group);
		}
	}

	/**
	 * Копирование значения доп. свойства типа "Файл" узла структуры
	 *
	 * @param $structure_id - Идентификатор нового созданого узла структуры
	 * @param $structure_property_image_id - Идентификатор нового созданного доп. свойства
	 * @param $copy_site_id - Идентификатор сайта, которому принадлежит копируемая структура
	 * @param $copy_structure_id - Идентификатор копируемого узла структуры
	 * @param $copy_structure_property_value_id - Идентификатор значения доп. свойства, значение которого копируется
	 */

	function MoveStructureItemPropertyImage($structure_id, $structure_property_image_id, $copy_site_id, $copy_structure_id, $copy_structure_property_value_id)
	{
		$structure_id = intval($structure_id);
		$structure_property_image_id = intval($structure_property_image_id);
		$copy_site_id = intval($copy_site_id);
		$copy_structure_id = intval($copy_structure_id);
		$copy_structure_property_value_id = intval($copy_structure_property_value_id);

		$DataBase = & singleton('DataBase');
		$Structure = & singleton('Structure');


		$file_path_to = '';
		$small_file_path_to = '';

		// Директория с файлами доп. свойств копируемого узла структуры
		$dir_structure_from = "tmp/upload/structure_site_{$copy_site_id}/" . $this->kernel->GetDirPath($copy_structure_id, 3) . '/structure_' . $copy_structure_id . '/';

		if (is_dir($dir_structure_from))
		{
			// Путь к файлу доп. свойства без расширения
			$file_path_from = Site_Controller_Template::instance()->templatePath . $dir_structure_from . 'structure_propertys_image_' . $copy_structure_property_value_id;

			// Получаем расширение файла
			$ext = $this->GetFileExtension($file_path_from);

			if (!empty($ext))
			{
				$ext = '.' . $ext;
			}

			$file_path_from = $file_path_from . $ext;

			// Файл существует
			if (is_file($file_path_from))
			{
				// Вставляем пустое значение доп. свойства
				$structure_property_value_id = $Structure->InsertStructurePropertysValue(0, 0, $structure_id, $structure_property_image_id, '');

				// Формируем относительный путь к папке с новыми файлами
				$structure_dir = $Structure->GetStructureItemDir($structure_id);

				// Создаем директорию для узла структуры
				$this->kernel->PathMkdir($structure_dir);

				// Имя создаваемого файла
				$file_name_to = 'structure_propertys_image_' . $structure_property_value_id . $ext;

				// Путь к создаваемому файлу
				$file_path_to = $structure_dir . $file_name_to;

				// Копируем файл доп. свойства
				copy($file_path_from, CMS_FOLDER . $file_path_to);
			}

			// Путь к малому файлу доп. свойства без расширения
			$small_file_path_from = $dir_structure_from . 'structure_propertys_small_image_' . $copy_structure_property_value_id;

			// Получаем расширение файла
			$ext = $this->GetFileExtension($small_file_path_from);

			if (!empty($ext))
			{
				$ext = '.' . $ext;
			}

			$small_file_path_from = Site_Controller_Template::instance()->templatePath . $small_file_path_from . $ext;

			// Файл существует
			if (is_file($small_file_path_from))
			{
				if (!isset($structure_property_value_id))
				{
					// Вставляем пустое значение доп. свойства
					$structure_property_value_id = $Structure->InsertStructurePropertysValue(0, 0, $structure_id, $structure_property_image_id, '');
				}

				if (!isset($structure_dir))
				{
					// Формируем относительный путь к папке с новыми файлами
					$structure_dir = $Structure->GetStructureItemDir($structure_id);

					// Создаем директорию для узла структуры
					$this->kernel->PathMkdir($structure_dir);
				}

				// Имя создаваемого файла
				$small_file_name_to = 'structure_propertys_image_' . $structure_property_value_id . $ext;

				// Путь к создаваемому файлу
				$small_file_path_to = $structure_dir . $small_file_name_to;

				// Копируем файл доп. свойства
				copy($small_file_path_from, CMS_FOLDER . $small_file_path_to);
			}

			if (isset($structure_property_value_id))
			{
				// Обновляем значение доп. свойства
				$Structure->InsertStructurePropertysValue(1, $structure_property_value_id, $structure_id, $structure_property_image_id, $file_name_to, $file_name_to, $small_file_name_to, $small_file_name_to);
			}
		}
	}

	/**
	 * Копирование изображений для группы товаров
	 *
	 * @param $shop_group_id - идентификатор новой созданной группы магазина
	 * @param $copy_shop_id - идентификатор копирумого магазина
	 * @param $copy_group_id - идентификатор копируемой группы товаров
	 */
	function MoveShopGroupImage($shop_group_id, $copy_shop_id, $copy_group_id)
	{
		$shop = & singleton('shop');


		$group_dir = $shop->GetGroupDir($shop_group_id);
		$this->kernel->PathMkdir($group_dir);

		$param_group = array ();
		$param_group['group_id'] = $shop_group_id;

		$shop_group_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/shop_{$copy_shop_id}/" . $this->kernel->GetDirPath($copy_group_id, 3) . "/group_{$copy_group_id}/shop_group_image{$copy_group_id}";

		// Получаем расширение файла
		$ext = $this->GetFileExtension($shop_group_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}
		$shop_group_image_from .= $ext;

		if (is_file($shop_group_image_from))
		{
			$shop_group_image_to = $group_dir . "shop_group_image{$shop_group_id}" . $ext;
			copy($shop_group_image_from, CMS_FOLDER . $shop_group_image_to);

			$param_group['shop_groups_image'] = basename($shop_group_image_to);
		}

		$shop_group_small_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/shop_{$copy_shop_id}/" . $this->kernel->GetDirPath($copy_group_id, 3)."/group_{$copy_group_id}/small_shop_group_image{$copy_group_id}";

		// Получаем расширение файла
		$ext = $this->GetFileExtension($shop_group_small_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}
		$shop_group_small_image_from .= $ext;

		if (is_file($shop_group_small_image_from))
		{
			$shop_group_small_image_to = $group_dir . "small_shop_group_image{$shop_group_id}" . $ext;
			copy($shop_group_small_image_from, CMS_FOLDER . $shop_group_small_image_to);

			$param_group['groups_image_small'] = basename($shop_group_small_image_to);
		}

		if (count($param_group) > 1)
		{
			// Получаем тнформацию о группе товаров
			$row_group = $shop->GetGroup($shop_group_id);

			$param_group['shop_shops_id'] = $row_group['shop_shops_id'];

			// Обновляем информацию о группе товаров после создания изображений
			$shop->InsertGroup($param_group);
		}
	}

	/**
	 * Копирование изображений для товара
	 *
	 * @param $shop_item_id - Идентификатор нового созданого товара
	 * @param $copy_shop_id - Идентификатор копирумого магазина
	 * @param $copy_shop_item_id - Идентификатор копируемого товара
	 */
	function MoveShopItemImage($shop_item_id, $copy_shop_id, $copy_shop_item_id)
	{
		$shop = & singleton('shop');

		$DataBase = & singleton('DataBase');
		$image = & singleton('Image');

		$shop_item_id = intval($shop_item_id);
		$copy_shop_id = intval($copy_shop_id);
		$copy_shop_item_id = intval($copy_shop_item_id);

		$item_dir = $shop->GetItemDir($shop_item_id);
		$this->kernel->PathMkdir($item_dir);

		$query = '';

		$shop_item_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/shop_{$copy_shop_id}/" . $this->kernel->GetDirPath($copy_shop_item_id, 3)."/item_{$copy_shop_item_id}/shop_items_catalog_image{$copy_shop_item_id}";

		// Получаем расширение файла
		$ext = $this->GetFileExtension($shop_item_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}

		$shop_item_image_from .= $ext;

		if (is_file($shop_item_image_from))
		{
			$shop_item_image_to = $item_dir . "shop_items_catalog_image{$shop_item_id}.jpg";
			copy($shop_item_image_from, CMS_FOLDER . $shop_item_image_to);
			$shop_items_catalog_image = quote_smart(basename($shop_item_image_to));

			// Определяем размеры изображения
			$big_image_sizes = $image->GetImageSize(CMS_FOLDER . $shop_item_image_to);

			$height = intval($big_image_sizes['height']);
			$width = intval($big_image_sizes['width']);

			$query = "`shop_items_catalog_image` = '{$shop_items_catalog_image}',
			`shop_items_catalog_big_image_height` = '{$height}',
			`shop_items_catalog_big_image_width` = '{$width}' ";
		}

		$shop_item_small_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/shop_{$copy_shop_id}/" . $this->kernel->GetDirPath($copy_shop_item_id, 3) . "/item_{$copy_shop_item_id}/small_shop_items_catalog_image{$copy_shop_item_id}";

		// Получаем расширение файла
		$ext = $this->GetFileExtension($shop_item_small_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}

		$shop_item_small_image_from .= $ext;

		if (is_file($shop_item_small_image_from))
		{
			$shop_item_small_image_to = $item_dir . "small_shop_items_catalog_image{$shop_item_id}.jpg";
			copy($shop_item_small_image_from, CMS_FOLDER . $shop_item_small_image_to);
			$shop_items_catalog_small_image = quote_smart(basename($shop_item_small_image_to));

			// Определяем размеры изображения
			$small_image_sizes = $image->GetImageSize(CMS_FOLDER . $shop_item_small_image_to);
			$height = intval($small_image_sizes['height']);
			$width = intval($small_image_sizes['width']);

			if (!empty($query))
			{
				$query .= ',';
			}

			$query .= "`shop_items_catalog_small_image` = '{$shop_items_catalog_small_image}',
			`shop_items_catalog_small_image_height` = '{$height}',
			`shop_items_catalog_small_image_width` = '{$width}' ";
		}

		if (!empty($query))
		{
			// Обновляем информацио об изображении объекта
			$query = "UPDATE `shop_items_catalog_table` SET {$query}
			WHERE `shop_items_catalog_item_id` = '{$shop_item_id}'";
			$DataBase->query($query);
		}
	}

	/**
	 * Копирование изображений для доп. свойств товара
	 *
	 * @param $shop_item_id - Идентификатор нового созданого товара
	 * @param $copy_shop_id - Идентификатор копирумого магазина
	 * @param $copy_shop_item_id - Идентификатор копируемого товара
	 * @param $copy_shop_item_property_id - Идентификатор доп. свойства типа "Файл" копируемого товара
	 */
	function MoveShopItemPropertyImage($shop_item_id, $shop_item_property_id, $copy_shop_id, $copy_shop_item_id, $copy_shop_item_property_id)
	{
		$shop = & singleton('shop');

		$shop_item_id = intval($shop_item_id);
		$shop_item_property_id = intval($shop_item_property_id);
		$copy_shop_id = intval($copy_shop_id);
		$copy_shop_item_id = intval($copy_shop_item_id);
		$copy_shop_item_property_id = intval($copy_shop_item_property_id);

		$item_dir = $shop->GetItemDir($shop_item_id);
		$this->kernel->PathMkdir($item_dir);

		$param_item_property = array();

		$shop_item_property_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/shop_{$copy_shop_id}/" . $this->kernel->GetDirPath($copy_shop_item_id, 3)."/item_{$copy_shop_item_id}/shop_property_file_{$copy_shop_item_id}_{$copy_shop_item_property_id}.jpg";

		if (is_file($shop_item_property_image_from))
		{
			$shop_item_property_image_to = $item_dir . "shop_property_file_{$shop_item_id}_{$shop_item_property_id}.jpg";
			copy($shop_item_property_image_from, CMS_FOLDER . $shop_item_property_image_to);

			$param_item_property['shop_properties_items_file'] = basename($shop_item_property_image_to);
			$param_item_property['shop_properties_items_value'] = $param_item_property['shop_properties_items_file'];
		}

		$shop_item_property_small_image_from = Site_Controller_Template::instance()->templatePath . "tmp/upload/shop_{$copy_shop_id}/" . $this->kernel->GetDirPath($copy_shop_item_id, 3) . "/item_{$copy_shop_item_id}/small_shop_property_file_{$copy_shop_item_id}_{$copy_shop_item_property_id}.jpg";

		if (is_file($shop_item_property_small_image_from))
		{
			$shop_item_property_small_image_to = $item_dir . "small_shop_property_file_{$shop_item_id}_{$shop_item_property_id}.jpg";
			copy($shop_item_property_small_image_from, CMS_FOLDER . $shop_item_property_small_image_to);

			$param_item_property['shop_properties_items_file_small'] = basename($shop_item_property_small_image_to);
			$param_item_property['shop_properties_items_value_small'] = $param_item_property['shop_properties_items_file_small'];
		}

		if (count($param_item_property) > 1)
		{
			// Вставляем информацию о доп. свойстве
			$param_item_property['shop_items_catalog_item_id'] = $shop_item_id;
			$param_item_property['shop_list_of_properties_id'] = $shop_item_property_id;

			$shop->InsertPropertiesItem($param_item_property);
		}
	}

	/**
	 * Копирование изображений для доп. свойств ИЭ
	 *
	 * @param $is_item_id - Идентификатор нового ИЭ
	 * @param $is_item_property_id - Идентификатор нового доп. свойства типа "Файл"
	 * @param $copy_is_id - Идентификатор копирумой ИС
	 * @param $copy_is_item_id - Идентификатор копируемого ИЭ
	 * @param $copy_is_item_property_value_id - Идентификатор значения доп. свойства типа "Файл" копируемого ИЭ
	 */
	function MoveInfItemPropertyImage($is_item_id, $is_item_property_id, $copy_is_id, $copy_is_item_id, $copy_is_item_property_value_id)
	{
		$InformationSystem = & singleton('InformationSystem');
		$kernel = & singleton('kernel');
		$SiteTemplate = & singleton ('SiteTemplate');

		$is_item_id = intval($is_item_id);
		$is_item_property_id = intval($is_item_property_id);
		$copy_is_id = intval($copy_is_id);
		$copy_is_item_id = intval($copy_is_item_id);
		$copy_is_item_property_value_id = intval($copy_is_item_property_value_id);

		$information_propertys_items_value = '';
		$information_propertys_items_value_small = '';

		$item_dir = $InformationSystem->GetInformationItemDir($is_item_id);
		$kernel->PathMkdir($item_dir);

		$param_item_property = array();

		$information_item_property_image_from = $SiteTemplate->GetTemplatePath() . "tmp/upload/information_system_{$copy_is_id}/".$kernel->GetDirPath($copy_is_item_id, 3)."/item_{$copy_is_item_id}/information_items_property_{$copy_is_item_property_value_id}";

		// Получаем расширение файла
		$ext = $SiteTemplate->GetFileExtension($information_item_property_image_from);

		if (!empty($ext))
		{
			$ext = '.' . $ext;
		}
		$information_item_property_image_from .= $ext;

		if (is_file($information_item_property_image_from))
		{
			// Вставляем пустое значение доп. свойства
			$property_value_id = $InformationSystem->InsertInformationPropertysItems(0, 0, $is_item_property_id, $is_item_id, '');

			$information_item_property_image_to = $item_dir . "information_items_property_{$property_value_id}" . $ext;
			copy($information_item_property_image_from, CMS_FOLDER . $information_item_property_image_to);

			$information_propertys_items_value = basename($information_item_property_image_to);
		}

		$information_item_property_small_image_from = $SiteTemplate->GetTemplatePath() . "tmp/upload/information_system_{$copy_is_id}/".$kernel->GetDirPath($copy_is_item_id, 3)."/item_{$copy_is_item_id}/small_information_items_property_{$copy_is_item_property_value_id}";

		if (is_file($information_item_property_small_image_from))
		{
			if (!isset($property_value_id))
			{
				// Вставляем пустое значение доп. свойства
				$property_value_id = $InformationSystem->InsertInformationPropertysItems(0, 0, $is_item_property_id, $is_item_id, '');
			}

			$information_item_property_small_image_to = $item_dir . "small_information_items_property_{$property_value_id}" . $ext;
			copy($information_item_property_small_image_from, CMS_FOLDER . $information_item_property_small_image_to);

			$information_propertys_items_value_small = basename($information_item_property_small_image_to);
		}

		if (!empty($information_propertys_items_value) || !empty($information_propertys_items_value_small))
		{
			// Вставляем информацию о доп. свойстве
			$InformationSystem->InsertInformationPropertysItems(1, $property_value_id, $is_item_property_id, $is_item_id, $information_propertys_items_value, $information_propertys_items_value, $information_propertys_items_value_small, $information_propertys_items_value_small);
		}
	}
}
