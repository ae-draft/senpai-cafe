<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Меню".
 *
 * Файл: /modules/Menu/Menu.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class menu
{
	/**
	 * *
	 *
	 * @param int $menu_id
	 * @param string $xsl
	 * @param int $parent_id
	 * @return string
	 * @access private
	 */
	function show_menu($menu_id, $xsl, $parent_id = 0)
	{
		if (isset($_SESSION['siteuser_id']) && class_exists('SiteUsers'))
		{
			$SiteUsers = & singleton('SiteUsers');
			$access = $SiteUsers->GetGroupsForUser($_SESSION['siteuser_id']);
		}
		else
		{
			$access = array(0);
		}

		// Вызов рекурсивной функции по формированию дерева рубрик
		$structure = & singleton('Structure');

		$level = -1;

		// Получаем меню, игнорируя текущий сайт
		$mass = $structure->GetStructure("&nbsp;", FALSE, $menu_id, $level, $parent_id);

		$xmlData = '<?xml version="1.0" encoding="UTF-8"?>'."\n";

		// Формируем XML для Меню
		$xmlData .= '<document>'."\n";

		// окончание формирования массива
		foreach ($mass as $key => $value)
		{
			$structure_access = $structure->GetStructureAccess($value['structure_id']);

			// если есть доступ к данному разделу и которые активны
			if (in_array($structure_access, $access) && $value['structure_activity']=='1')
			{
				// Формируем XML дерево
				$xmlData .= '<menu id="'.$value['menu_id'].'">'."\n";
				$xmlData .= '<structure_id>'.$value['structure_id'].'</structure_id>'."\n";
				$xmlData .= '<current_structure_id>'.CURRENT_STRUCTURE_ID.'</current_structure_id>'."\n";
				$xmlData .= '<name>'.str_for_xml($value['structure_menu_name']).'</name>'."\n";
				$xmlData .= '<show>'.$value['structure_show'].'</show>'."\n";

				// Ссылка показывается только при связи раздела структуры с каким либо документом
				$xmlData .= '<show_link>'. (($value['documents_id'] == 0 &&
				mb_strlen(trim($value['structure_external_link'])) == 0 &&
				$value['current_level'] !=0) ? "0" : "1").'</show_link>'."\n";

				$xmlData .= '<level>'.$value['current_level'].'</level>'."\n";
				$xmlData .= '<id_parent>'.$value['structure_parent_id'].'</id_parent>'."\n";

				// Внешняя ссылка есть, если значение внешней ссылки не пустой
				$xmlData .= '<is_external_link>'.((mb_strlen((trim($value['structure_external_link'])))==0) ? "0" : "1").'</is_external_link>'."\n";

				$xmlData .= '<external_link>'.str_for_xml($value['structure_external_link']).'</external_link>'."\n";
				$xmlData .= '<link>'.str_for_xml($value['full_path']).'</link>'."\n";
				$xmlData .= '</menu>'."\n";
			}
		}

		$xmlData .= '</document>';

		$xslt = & singleton('xsl');
		echo $xslt->build($xmlData, $xsl);
	}

	/**
	 * Метод, осуществляющий вставку или обновление меню.
	 *
	 * @param array $param ассоциативный массив параметров
	 * <br> int $param['menu_id'] идентификатор меню
	 * <br> int $param['site_id'] идентификатор сайта
	 * <br> string $param['menu_name'] название меню
	 * <br> int $param['users_id'] идентификатор пользователя, если false - берется текущий пользователь.
	 * @return int идентификатор вставленного/обновленного меню или false.
	 */
	function InsertMenu($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		if (!isset($param['menu_id']) || $param['menu_id'] == 0)
		{
			$param['menu_id'] = NULL;
		}

		$oStructure_Menu = Core_Entity::factory('Structure_Menu', $param['menu_id']);

		$oStructure_Menu->name = Core_Type_Conversion::toStr($param['menu_name']);
		$oStructure_Menu->site_id = Core_Type_Conversion::toInt($param['site_id']);
		$oStructure_Menu->sorting = Core_Type_Conversion::toInt($param['menu_order']);

		if (is_null($param['menu_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oStructure_Menu->user_id = $param['users_id'];
		}

		$oStructure_Menu->save();

		return $oStructure_Menu->id;
	}

	/**
	 * Метод для получения списка всех меню
	 *
	 * @param int $site_id идентификатор сайта, которому принадлежит меню, если false - учитываются все сайты
	 * @return resource с информацией о меню
	 */
	function GetAllMenu($site_id = false)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'menu_id'),
				array('site_id', 'site_id'),
				array('name', 'menu_name'),
				array('user_id', 'users_id'),
				array('sorting', 'menu_order')
			)
			->from('structure_menus')
			->where('deleted', '=', 0)
			->orderBy('sorting');

		if ($site_id !== FALSE)
		{
			$queryBuilder->where('site_id', '=', $site_id);
		}

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Метод для удаления меню
	 *
	 * @param int $menu_id идентификатор меню, которое нужно удалить
	 * @return результат выполнения запроса
	 */
	function DeleteMenu($menu_id)
	{
		Core_Entity::factory('Structure_Menu', $menu_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Метод для получения информации о меню
	 *
	 * @param int $menu_id Идентификатор меню
	 * @return array данные о меню
	 */

	function SelectMenu($menu_id)
	{
		$oStructure_Menu = Core_Entity::factory('Structure_Menu', $menu_id);

		return array(
			'menu_id' => $oStructure_Menu->id,
			'site_id' => $oStructure_Menu->site_id,
			'menu_name' => $oStructure_Menu->name,
			'users_id' => $oStructure_Menu->user_id,
			'menu_order' => $oStructure_Menu->sorting
		);
	}

	/**
	 * Копирование информации о меню сайта
	 *
	 * @param int $menu_id
	 * @param array $param Массив дополнительных параметров (не обязателен)
	 * - int $param['site_id'] Идентификатор сайта, к которому будет относиться скопированное меню (по умолчанию - к тому же сайту, что и копируемое)
	 * @return bool
	 */
	function CopyMenu($menu_id, $param = array())
	{
		$menu_id = intval($menu_id);
		$oStructure_Menu = Core_Entity::factory('Structure_Menu', $menu_id);
		$oNewStructure_Menu = $oStructure_Menu->copy();

		$site_id = Core_Type_Conversion::toInt($param['site_id']);
		if ($site_id)
		{
			$oNewStructure_Menu->site_id = $site_id;
		}

		$oNewStructure_Menu->save();

		return $oNewStructure_Menu->id;
	}
}
