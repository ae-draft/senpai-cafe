<?php

/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, методы работы с тегами.
 *
 * Файл: /modules/tag/tag.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class Tag
{
	/**
	 * mem-cache для GetTagRelation()
	 *
	 * @var array
	 */
	var $CacheGetTagRelation = array();

	/**
	 * mem-cache для GetTag()
	 *
	 * @var array
	 */
	var $CacheGetTag = array();

	function getArrayTag($oTag)
	{
		return array (
			'tag_id' => $oTag->id,
			'tags_group_id' => $oTag->tag_dir_id,
			'tag_name' => $oTag->name,
			'tag_description' => $oTag->description,
			'tag_path' => $oTag->path,
			'users_id' => $oTag->user_id
		);
	}

	function getArrayTagDir($oTagDir)
	{
		return array(
			'tags_group_id' => $oTagDir->id,
			'tags_group_parent_id' => $oTagDir->parent_id,
			'tags_group_name' => $oTagDir->name,
			'tags_group_description' => $oTagDir->description,
			'tags_group_order' => $oTagDir->sorting,
			'users_id' => $oTagDir->user_id
		);
	}

	function getArrayTagShopItem($oTagShopItem)
	{
		return array(
			'tag_relation_id' => $oTagShopItem->id,
			'tag_id' => $oTagShopItem->tag_id,
			'shop_items_catalog_item_id' => $oTagShopItem->shop_item_id,
			'site_id' => $oTagShopItem->site_id
		);
	}

	function getArrayTagInformationsystemItem($oTagInformationsystemItem)
	{
		return array(
			'tag_relation_id' => $oTagInformationsystemItem->id,
			'tag_id' => $oTagInformationsystemItem->tag_id,
			'information_items_id' => $oTagInformationsystemItem->informationsystem_item_id,
			'site_id' => $oTagInformationsystemItem->site_id
		);
	}

	/**
	 * Получение информации о теге
	 *
	 * @param int $tag_id идентификатор тега
	 * @param array $param массив параметров
	 * - $param['cache'] кэшировать данные, по умолчанию true
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $tag_id = 1;
	 *
	 * $row = $Tag->GetTag($tag_id);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив с данными о теге
	 */
	function GetTag($tag_id, $param = array())
	{
		$tag_id = intval($tag_id);

		if (isset($this->CacheGetTag[$tag_id])
			&& (!isset($param['cache']) || $param['cache']))
		{
			return $this->CacheGetTag[$tag_id];
		}

		$oTag = Core_Entity::factory('Tag')->find($tag_id);


		if (is_null($oTag->id))
		{
			$row = FALSE;
		}
		else
		{
			$row = $this->getArrayTag($oTag);
		}

		if (!isset($param['cache']) || $param['cache'])
		{
			$this->CacheGetTag[$tag_id] = $row;
		}

		return $row;
	}

	/**
	 * Получение информации о тегах
	 *
	 * @param int $aTags массив идентификатор тегов
	 * @param array $param массив параметров
	 * - $param['cache'] кэшировать данные, по умолчанию true
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $aTags = array(1, 2, 5, 7, 19);
	 *
	 * $rows = $Tag->GetTags($aTags);
	 *
	 * // Распечатаем результат
	 * print_r ($rows);
	 * ?>
	 * </code>
	 * @return array массив с данными о тегах
	 */
	function GetTags($aTags, $param = array())
	{
		$aTags = Core_Type_Conversion::toArray($aTags);
		$aTags = Core_Array::toInt($aTags);

		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'tag_id'),
			array('tag_dir_id', 'tags_group_id'),
			array('name', 'tag_name'),
			array('path', 'tag_path'),
			array('user_id', 'users_id'),
			array('description', 'tag_description')
		)->from('tags')
		->where('id', 'IN', $aTags)
		->where('deleted', '=', 0);

		$aTagsResult = $queryBuilder->execute()->asAssoc()->result();

		foreach ($aTagsResult as $aTag)
		{
			if (!isset($param['cache']) || $param['cache'])
			{
				$this->CacheGetTag[$aTag['tag_id']] = $aTag;
			}
		}

		return $aTagsResult;
	}

	/**
	 * Получение информации о теге по его имени
	 *
	 * @param int $tag_name имя тега
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $tag_name = 'CMS';
	 *
	 * $row = $Tag->GetTagByName($tag_name);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив с данными о теге
	 */
	function GetTagByName($tag_name)
	{
		$oTag = Core_Entity::factory('Tag')->getByName($tag_name);
		return $oTag ? $this->getArrayTag($oTag) : FALSE;
	}
	/**
	 * Получение информации о теге по его пути
	 *
	 * @param int $tag_name имя тега
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $tag_path = 'CMS';
	 *
	 * $row = $Tag->GetTagByPath($tag_name);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return array массив с данными о теге
	 */
	function GetTagByPath($tag_path)
	{
		$oTag = Core_Entity::factory('Tag')->getByPath($tag_path);
		return $oTag ? $this->getArrayTag($oTag) : FALSE;
	}

	/**
	 * Вставка/обновление тега
	 *
	 * @param array $array массив атрибутов
	 * - $array['tag_name'] имя тега
	 * - $array['tag_path'] имя тега в URL. по умолчанию используется имя тега.
	 * - $array['tag_description'] описание тега
	 * - $array['tag_id'] идентификатор тега, необязательный параметр, указывается при редактировании тега
	 * - $array['tags_group_id'] идентификатор группы тегов
	 * - $array['users_id'] идентификатор пользователя центра администрирования
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $array['tag_name'] = 'NewTag';
	 *
	 * $newid = $Tag->InsertTag($array);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return mixed
	 * <br />0 - передано пустое имя тега,
	 * <br />false - ошибка вставки/обновления,
	 * <br />положительноче число - идентификатор вставленного/обновленного тега
	 */
	function InsertTag($array)
	{
		if (!isset($array['tag_id']) || $array['tag_id'] == 0)
		{
			$array['tag_id'] = NULL;
		}

		$oTag = Core_Entity::factory('Tag', $array['tag_id']);

		if (isset($array['tag_name']) && ($array['tag_name'] = trim($array['tag_name'])) != '')
		{
			$oTag->name = $array['tag_name'];
		}
		elseif(!$array['tag_id'])
		{
			return 0;
		}

		if ((!isset($array['tag_path'])
			|| trim($array['tag_path']) == '')
			&& !$array['tag_id'])
		{
			if (defined('TAG_TRANSLIT') && TAG_TRANSLIT)
			{
				$oUrl = & singleton('Url');
				$array['tag_path'] = $oUrl->translit_lower($array['tag_name']);
			}
			else
			{
				$array['tag_path'] = $array['tag_name'];
			}

			$Tag->path = $array['tag_path'];
		}
		elseif(($array['tag_path'] = trim($array['tag_path'])) != '')
		{
			$oTag->path = $array['tag_path'];
		}

		if (isset($array['tag_description']))
		{
			$oTag->description = trim($array['tag_description']);
		}

		if (isset($array['tags_group_id']))
		{
			$oTag->tag_dir_id = intval($array['tags_group_id']);
		}

		if (is_null($array['tag_id']) && isset($array['users_id']) && $array['users_id'])
		{
			$oTag->user_id = $array['users_id'];
		}

		$oTag->save();

		return $oTag->id;
	}

	/**
	 * Удаление тега
	 *
	 * @param int $tag_id идентификатор тега
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $tag_id = 104;
	 *
	 * $result = $Tag->DeleteTag($tag_id);
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
	 * @return boolean
	 */
	function DeleteTag($tag_id)
	{
		$tag_id = intval($tag_id);
		Core_Entity::factory('Tag', $tag_id)->markDeleted();
		return TRUE;
	}

	/**
	 * Вставка соответствия тега элементам контента
	 *
	 * @param array $array массив атрибутов
	 * - $array['tag_id'] - идентификатор тега
	 * - $array['information_items_id'] - идентификатор информационного элемента
	 * - $array['shop_items_catalog_item_id'] - идентификатор товара
	 * - $array['site_id'] - идентификатор сайт, не обязательное поле. Если не указано - определяется автоматически.
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $array['tag_id'] = 27;
	 * $array['information_items_id'] = 1;
	 * $array['site_id'] = CURRENT_SITE;
	 *
	 * $newid = $Tag->InsertTagRelation($array);
	 *
	 * // Распечатаем результат
	 * echo $newid;
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function InsertTagRelation($array)
	{
		$array['tag_id'] = Core_Type_Conversion::toInt($array['tag_id']);

		if ($array['tag_id'] <= 0)
		{
			return 0;
		}

		if (isset($array['site_id']))
		{
			$array['site_id'] = intval($array['site_id']);
		}
		else
		{
			$array['site_id'] = CURRENT_SITE;
		}

		// Идентификатор информационного элемента
		if (isset($array['information_items_id']))
		{
			/*
			$field_name = 'information_items_id';
			$field_value = intval($array['information_items_id']);
			*/
			$array['information_items_id'] = intval($array['information_items_id']);

			$oTag = Core_Entity::factory('Informationsystem_Item', $array['information_items_id'])
				->Tags->getById($array['tag_id']);

			if (!is_null($oTag))
			{
				return TRUE;
			}
			else
			{
				$oTagInformationsystemItem = Core_Entity::factory('Tag_Informationsystem_Item');
				$oTagInformationsystemItem->tag_id = $array['tag_id'];
				$oTagInformationsystemItem->informationsystem_item_id = $array['information_items_id'];
				$oTagInformationsystemItem->site_id = $array['site_id'];
				$oTagInformationsystemItem->save();

				return TRUE;
			}
		}
		elseif (isset($array['shop_items_catalog_item_id']))
		{
			/*
			$field_name = 'shop_items_catalog_item_id';
			$field_value = intval($array['shop_items_catalog_item_id']);
			*/
			$array['shop_items_catalog_item_id'] = intval($array['shop_items_catalog_item_id']);

			$oTag = Core_Entity::factory('Shop_Item', $array['shop_items_catalog_item_id'])
				->Tags->getById($array['tag_id']);

			if (!is_null($oTag))
			{
				return TRUE;
			}
			else
			{
				$oTagShopItem = Core_Entity::factory('Tag_Shop_Item');

				$oTagShopItem->tag_id = $array['tag_id'];
				$oTagShopItem->shop_item_id = $array['shop_items_catalog_item_id'];
				$oTagShopItem->site_id = $array['site_id'];

				$oTagShopItem->save();

				return TRUE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Заполнение mem-кэша для переданного списка идентификаторов товаров соответствиями тегов.
	 * Заполнению подвергается массив $this->CacheGetTagRelation[X][shop_items_catalog_item_id][] = $row
	 * где X = information_items_id для ИС, = shop_items_catalog_item_id для товаров
	 * @param array $param массив атрибутов
	 * - $param['information_items_id'] массив идентификаторов информационных элементов
	 * - $param['shop_items_catalog_item_id'] массив идентификаторов товаров
	 */
	function FillMemCacheGetTagRelation($param)
	{
		$param = Core_Type_Conversion::toArray($param);

		// Если передан идентификатор инфоэлемента, обращаемся к инфоэлементам
		if (!isset($param['information_items_id']) && isset($param['shop_items_catalog_item_id']))
		{
			return FALSE;
		}

		if (isset($param['information_items_id']))
		{
			$index_name = 'information_items_id';
			$field_name = 'informationsystem_item_id';
			$field_array = $param['information_items_id'];

		}
		// иначе если передан идентификатор товара, обращаемся к товарам
		elseif (isset($param['shop_items_catalog_item_id']))
		{
			$index_name = 'shop_items_catalog_item_id';
			$field_name = 'shop_item_id';
			$field_array = $param['shop_items_catalog_item_id'];

		}
		else
		{
			return FALSE;
		}

		if (count($field_array) > 0)
		{
			$queryBuilder = Core_QueryBuilder::select();

			foreach ($field_array as $key => $shop_items_catalog_item_id)
			{
				$field_array[$key] = intval($shop_items_catalog_item_id);
				$this->CacheGetTagRelation[$index_name][$shop_items_catalog_item_id] = FALSE;
			}

			// Поля таблицы тегов
			$queryBuilder->select(
				array('tags.id', 'tag_id'),
				array('tag_dir_id', 'tags_group_id'),
				array('name', 'tag_name'),
				array('path', 'tag_path'),
				array('user_id', 'users_id'),
				array('description', 'tag_description')
			);

			if (isset($param['information_items_id']))
			{
				$queryBuilder->select(
					array('tag_informationsystem_items.id', 'tag_relation_id'),
					'tag_id',
					array('tag_informationsystem_items.informationsystem_item_id', 'information_items_id'),
					'site_id'
				);

				// Выбираем теги, связанные с информационным элементом
				$queryBuilder->from('tags')
					->join('tag_informationsystem_items', 'tags.id', '=', 'tag_informationsystem_items.tag_id')
					->where('informationsystem_item_id', 'IN', $field_array);
			}
			else
			{
				$queryBuilder->select(
					array('tag_shop_items.id', 'tag_relation_id'),
					'tag_id',
					array('tag_shop_items.shop_item_id', 'shop_items_catalog_item_id'),
					'site_id'
				);

				// Выбираем теги, связанные с магазином
				$queryBuilder->from('tags')
					->join('tag_shop_items', 'tags.id', '=', 'tag_shop_items.tag_id')
					->where('shop_item_id', 'IN', $field_array);
			}

			$aTags = $queryBuilder
				->where('deleted', '=', 0)
				->execute()->asAssoc()
				->result();

			if (count($aTags) > 0)
			{
				foreach($aTags as $aTag)
				{
					$this->CacheGetTagRelation[$index_name][$aTag[$index_name]][$aTag['tag_id']] = $aTag;
				}
			}
		}
	}

	/**
	 * Список соответствий тега
	 *
	 * @param array $array массив атрибутов
	 * - $array['information_items_id'] идентификатор информационного элемента
	 * - $array['shop_items_catalog_item_id'] идентификатор товара
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $array['information_items_id'] = 1;
	 *
	 * $row = $Tag->GetTagRelation($array);
	 *
	 * // Распечатаем результат
	 * print_r ($row);
	 * ?>
	 * </code>
	 * @return mixed массив с данными или false
	 */
	function GetTagRelation($array)
	{
		// Если передан идентификатор инфоэлемента, обращаемся к инфоэлементам
		if (isset($array['information_items_id']))
		{
			$index_name = 'information_items_id';
			$field_value = intval($array['information_items_id']);
		}
		// иначе если передан идентификатор товара, обращаемся к товарам
		elseif (isset($array['shop_items_catalog_item_id']))
		{
			$index_name = 'shop_items_catalog_item_id';
			$field_value = intval($array['shop_items_catalog_item_id']);
		}
		else
		{
			return false;
		}

		// Првоерка на наличие в mem-кэше
		if (isset($this->CacheGetTagRelation[$index_name][$field_value]))
		{
			return $this->CacheGetTagRelation[$index_name][$field_value];
		}

		if (isset($array['information_items_id']))
		{
			$aTags = Core_Entity::factory('Informationsystem_Item', $field_value)->Tags->findAll();
		}
		else
		{
			$aTags = Core_Entity::factory('Shop_Item', $field_value)->Tags->findAll();
		}

		if (count($aTags) > 0)
		{
			foreach($aTags as $oTag)
			{
				$aTag = $this->getArrayTag($oTag);

				if (isset($array['information_items_id']))
				{
					// Объект связи тега с информационным элементом
					$oTag_Informationsystem_Item = $oTag
						->Tag_Informationsystem_Items
						->getByInformationsystemItem($field_value, $oTag->id);

					$aTagRelationItem = $this->getArrayTagInformationsystemItem($oTag_Informationsystem_Item);
				}
				elseif (isset($array['shop_items_catalog_item_id']))
				{
					// Объект связи тега с товаром
					$oTag_Shop_Item = $oTag->Tag_Shop_Items->getByShopItem($field_value);

					$aTagRelationItem = $this->getArrayTagShopItem($oTag_Shop_Item);
				}
				// Подробная информация о теге и связи с информационным элементом/товаром
				$return[$aTag['tag_id']] = $aTag + $aTagRelationItem;
			}
		}
		else
		{
			$return = FALSE;
		}

		// Сохраняем в mem-кэш
		$this->CacheGetTagRelation[$index_name][$field_value] = $return;

		return $return;
	}

	/**
	 * Удаление соответствий тега
	 *
	 * @param array $array массив атрибутов
	 * - $array['information_items_id'] - идентификатор информационного элемента
	 * - $array['shop_items_catalog_item_id'] идентификатор товара
	 * - $array['tag_id'] - идентификатор тега
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $array['information_items_id'] = 1;
	 * $array['tag_id'] = 27;
	 *
	 * $result = $Tag->DeleteTagRelation($array);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return boolean
	 */
	function DeleteTagRelation($array)
	{
		if (!isset($array['information_items_id']) && !isset($array['shop_items_catalog_item_id']))
		{
			return FALSE;
		}

		// Если передан идентификатор инфоэлемента, обращаемся к инфоэлементам
		if (isset($array['information_items_id']))
		{
			$queryBuilder = Core_QueryBuilder::delete('tag_informationsystem_items');

			$field_name = 'informationsystem_item_id';
			$field_value = intval($array['information_items_id']);
		}
		// Иначе передан идентификатор товара, обращаемся к товарам
		else
		{
			$queryBuilder = Core_QueryBuilder::delete('tag_shop_items');

			$field_name = 'shop_item_id';
			$field_value = intval($array['shop_items_catalog_item_id']);
		}

		if (isset($array['tag_id']) && $array['tag_id'] > 0)
		{
			$queryBuilder->where('tag_id', '=', $array['tag_id']);
		}

		$queryBuilder->where($field_name, '=', $field_value);

		$queryBuilder->execute();

		return TRUE;
	}

	/**
	 * Генерация XML для тега
	 *
	 * @param int $tag_id Идентификатор тега
	 * @param array $tag_row массив с данными о теге (не обязательное поле), по умолчанию false
	 * <code>
	 * <?php
	 * $Tag = new Tag();
	 *
	 * $tag_id = 27;
	 *
	 * $result = $Tag->GenXmlForTag($tag_id);
	 *
	 * // Распечатаем результат
	 * echo $result;
	 * ?>
	 * </code>
	 * @return mixed строка с XML или false, если тег не найден
	 */
	function GenXmlForTag($tag_id, $tag_row = false, $count = false)
	{
		$tag_id = intval($tag_id);

		// Если данные о теге не переданы - получим их
		if (!$tag_row)
		{
			$tag_row = $this->GetTag($tag_id);
		}

		if ($count !== false)
		{
			$tag_row['count'] = $count;
		}

		if ($tag_row)
		{
			$return = '<tag id="'.$tag_row['tag_id'].'">'."\n";
			$return .= '<tag_name>'.str_for_xml($tag_row['tag_name']).'</tag_name>'."\n";
			$return .= '<tag_description>'.str_for_xml($tag_row['tag_description']).'</tag_description>'."\n";
			$return .= '<tag_path_name>'.str_for_xml(rawurlencode($tag_row['tag_path'])).'</tag_path_name>'."\n";

			if (isset($tag_row['count']))
			{
				$return .= '<count>'.intval($tag_row['count']).'</count>'."\n";
			}

			$return .= '</tag>'."\n";
		}
		else
		{
			$return = FALSE;
		}

		return $return;
	}

	/**
	 * Получение информации о тегах
	 * @param $param array массив дополнительных параметров
	 * - $param['tags_group_id'] идентификатор группы, для которой необходимо получить список тегов
	 *
	 * @return resource
	 */
	function GetAllTags($param = array())
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'tag_id'),
			array('tag_dir_id', 'tags_group_id'),
			array('name', 'tag_name'),
			array('path', 'tag_path'),
			array('user_id', 'users_id'),
			array('description', 'tag_description')
		)
		->from('tags')
		->where('deleted', '=', 0);

		if (isset($param['tags_group_id']))
		{
			$queryBuilder->where('tag_dir_id', '=', intavl($param['tags_group_id']));
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Вставка/обновление группы тегов
	 *
	 * @param array $param массив параметров
	 * - $param['tags_group_id'] идентификатор редактируемой группы тегов
	 * - $param['tags_group_parent_id'] идентификатор родительской группы тегов
	 * - $param['tags_group_name'] название группы тегов
	 * - $param['tags_group_description'] описание группы тегов
	 * - $param['tags_group_order'] порядковый номер группы
	 * - $param['users_id'] идентификатор пользователя центра администрирования, создавшего группу тегов
	 *
	 * @return mixed идентификатор добавленной/измененной группы или false
	 */
	function InsertTagsGroup($param)
	{
		if (!isset($param['tags_group_id']) || $param['tags_group_id'] == 0)
		{
			$param['tags_group_id'] = NULL;
		}

		$oTag_Dir = Core_Entity::factory('Tag_Dir', $param['tags_group_id']);

		if (isset($param['tags_group_parent_id']))
		{
			$oTag_Dir->parent_id = intval($param['tags_group_parent_id']);
		}

		if (isset($param['tags_group_name']))
		{
			$oTag_Dir->name = $param['tags_group_name'];
		}

		if (isset($param['tags_group_description']))
		{
			$oTag_Dir->description = $param['tags_group_description'];
		}

		if (isset($param['tags_group_order']))
		{
			$oTag_Dir->sorting = intval($param['tags_group_order']);
		}

		if (is_null($param['tags_group_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oTag_Dir->user_id = $param['users_id'];
		}

		$oTag_Dir->save();

		return $oTag_Dir->id;
	}

	/**
	 * Получение информации о группе меток
	 *
	 * @param int $tags_group_id идентификатор группы блогов
	 *
	 * @return mixed массив с данными о группе тегов или false
	 */
	function GetTagsGroup($tags_group_id)
	{
		return getArrayTagDir(
			Core_Entity::factory('Tag_Dir')->find($tags_group_id)
		);
	}

	/**
	 * Построение массива пути от текущей группы меток к корневой
	 *
	 * @param int $tags_group_id идентификатор группы меток, для которой необходимо построить путь
	 * @param array $return_path_array служебный параметр
	 * @return array ассоциативный массив, элементы которого содержат информацию о группах тегов, составляющих путь от текущей группы до корневой
	 */
	function GetTagsGroupsPathArray($tags_group_id, $return_path_array = array())
	{
		$tags_group_id = intval($tags_group_id);

		if ($tags_group_id == 0)
		{
			$return_path_array[0] = array();
		}
		else
		{
			$row = $this->GetTagsGroup($tags_group_id);

			$return_path_array[$row['tags_group_id']] = $row;

			$return_path_array = $this->GetTagsGroupsPathArray($row['tags_group_parent_id'], $return_path_array);
		}

		return $return_path_array;
	}

	/**
	 * Удаление группы тегов
	 *
	 * @param int $tags_group_id идентификатор группы тегов	 *
	 * @return boolean
	 */
	function DeleteTagsGroup($tags_group_id)
	{
		Core_Entity::factory('Tag_Dir', intval($tags_group_id))->markDeleted();

		return TRUE;
	}

	/**
	 * Получение информации о группах меток
	 *
	 * @param array $param массив параметров
	 *  - $param['tags_group_parent_id'] идентификатор родительской группы
	 *
	 * @return mixed resource с данными о группах тегов или false
	 */
	function GetAllTagsGroups($param)
	{
		$queryBuilder = Core_QueryBuilder::select(
			array('id', 'tags_group_id'),
			array('parent_id', 'tags_group_parent_id'),
			array('name', 'tags_group_name'),
			array('description', 'tags_group_description'),
			array('sorting', 'tags_group_order'),
			array('user_id', 'users_id')
		)
		->from('tag_dirs')
		->where('deleted', '=', 0);

		if (isset($param['tags_group_parent_id']))
		{
			$tags_group_parent_id = intval($param['tags_group_parent_id']);
			$queryBuilder->where('parent_id', '=', $tags_group_parent_id);
		}

		return $queryBuilder->execute()->asAssoc()->getResult();
	}

	/**
	 * Формирование дерева групп меток
	 *
	 * @param array $param массив параметров
	 * - $param['tags_group_parent_id'] идентификатор группы меток, относительно которой строится дерево групп
	 * - $param['separator'] символ, отделяющий группу нижнего уровня от родительской группы
	 * - $param['tags_group_id'] идентификатор группы, которую вместе с ее
	 * подгруппами не нужно включать в дерево групп, если равен false, то
	 * включать в дерево групп все подгруппы. - $param['array'] служебный
	 * элемент
	 *
	 * @return array двумерный массив, содержащий дерево подгрупп
	 */
	function GetTagsGroupsTree($param)
	{
		$tags_group_parent_id = Core_Type_Conversion::toInt($param['tags_group_parent_id']);
		$separator = Core_Type_Conversion::toStr($param['separator']);
		$tags_group_id = Core_Type_Conversion::toInt($param['tags_group_id']);

		$return_mas = array();

		// Получаем подгруппы тегов, входящие в текущую группу
		$param_group = array();
		$param_group['tags_group_parent_id'] = $tags_group_parent_id;

		$result = $this->GetAllTagsGroups($param_group);

		while ($row_tags_group = mysql_fetch_assoc($result))
		{
			if ($tags_group_id != $row_tags_group['tags_group_id'])
			{
				$row_tags_group['separator'] = $separator;

				$return_mas[] = $row_tags_group;

				$param_recurse = array();
				$param_recurse['tags_group_parent_id']  = $row_tags_group['tags_group_id'];
				$param_recurse['separator'] = $separator . '&nbsp;';
				$param_recurse['tags_group_id'] = $tags_group_id;
				$return_mas = array_merge($return_mas, $this->GetTagsGroupsTree($param_recurse));
			}
		}

		return $return_mas;
	}

	/**
	 * Объединение тегов, теги объединяются по наименьшему идентификатору, тег с наименьшим идентификатором получает данные всех остальных тегов, остальные теги удаляются
	 *
	 * @param array $tags_array массив тегов, подлежащих удалению
	 * @return boolean TRUE в случае успеха объединения, FALSE в случае невозможности объединения
	 */
	function JoinTags($tags_array)
	{
		$tags_array = Core_Type_Conversion::toArray($tags_array);

		// Сортируем по возрастанию
		sort($tags_array);

		reset($tags_array);

		// First item
		$each = each($tags_array);

		$min_item = $each['value'];

		// Удаляем минимальный элемент массива
		unset($tags_array[0]);

		if(count($tags_array) > 0)
		{
			foreach ($tags_array as $key => $value)
			{
				$tags_array[$key] = intval($value);
			}
		}
		else
		{
			return FALSE;
		}

		$queryBuilder = Core_QueryBuilder::update('tag_informationsystem_items')->set('tag_id', $min_item)
			->where('tag_id', 'IN', $tags_array)->execute();

		$queryBuilder = Core_QueryBuilder::update('tag_shop_items')->set('tag_id', $min_item)
			->where('tag_id', 'IN', $tags_array)->execute();

		// Нужно удалить метки, значения которых вошли в метку с минимальным идентификатором DeleteTag
		foreach($tags_array AS $tag_id)
		{
			Core_Entity::factory('Tag', $tag_id)->delete();
		}

		return TRUE;
	}
}