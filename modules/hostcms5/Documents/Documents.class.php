<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "Старницы и документы".
 *
 * Файл: /modules/Documents/Documents.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class documents
{
	/**
	* Массив разделов документов
	*
	* @var array
	*/
	var $mas_documents_dir = array();

	/**
	* Разделитель
	*
	* @var int
	* @access private
	*/
	var $separator='';

	/**
	* Путь к разделу документов
	*
	* @var string
	* @access private
	*/
	var $section_path='';

	var $path_array = array();

	/**
	 * Массив соответствия старых и новых идентификаторов скопированных документов
	 *
	 * @var array
	 */
	var $documents_id_array = array();

	/**
	 * Кэш для метода GetCurrentDocumentVersion()
	 *
	 * @var array
	 */
	var $CacheGetCurrentDocumentVersion = array();

	function getArrayDocument($oDocument)
	{
		return array(
			'documents_id' => $oDocument->id,
			'documents_dir_id' => $oDocument->document_dir_id,
			'documents_status_id' => $oDocument->document_status_id,
			'documents_name' => $oDocument->name,
			'users_id' => $oDocument->user_id,
			'site_id' => $oDocument->site_id
		);
	}

	function getArrayDocumentDir($oDocumentDir)
	{
		return array(
			'documents_dir_id' => $oDocumentDir->id,
			'documents_dir_parent_id' => $oDocumentDir->parent_id,
			'documents_dir_name' => $oDocumentDir->name,
			'users_id' => $oDocumentDir->user_id,
			'site_id' => $oDocumentDir->site_id
		);
	}

	function getArrayDocumentStatus($oDocumentStatus)
	{
		return array(
			'documents_status_id' => $oDocumentStatus->id,
			'documents_status_name' => $oDocumentStatus->name,
			'documents_status_description' => $oDocumentStatus->description,
			'users_id' => $oDocumentStatus->user_id,
			'site_id' => $oDocumentStatus->site_id
		);
	}

	function getArrayDocumentVersion($oDocumentVersion)
	{
		return array(
			'documents_version_id' => $oDocumentVersion->id,
			'documents_id' => $oDocumentVersion->document_id,
			'documents_version_date_time' => $oDocumentVersion->datetime,
			'documents_version_current' => $oDocumentVersion->current,
			'documents_version_comment' => $oDocumentVersion->description,
			'users_id' => $oDocumentVersion->user_id,
			'templates_id' => $oDocumentVersion->template_id
		);
	}

	/**
	* Показ текста текущей версии страницы
	*
	* @param int $document_id иднетификатор отображаемого документа
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор документа
	* $document_id = 12;
	*
	* $documents->ShowDocument($document_id);
	* ?>
	* </code>
	*/
	function ShowDocument($document_id)
	{
		$oDocument_Versions = Core_Entity::factory('Document', $document_id)
			->Document_Versions->getCurrent();

		if (is_object($oDocument_Versions) && is_file($oDocument_Versions->getPath()))
		{
			echo Core_File::read($oDocument_Versions->getPath());
		}
		else
		{
			show_error_message('Ошибка! Файл версии страницы документа ' . $document_id . ' не найден!');
		}
	}

	/**
	* Получение пути к файлу версии документа
	*
	* @param int $documents_version_id идентификатор версии документа
	* @return string путь к документу
	*/
	function GetDocumentVersionPath($documents_version_id)
	{
		return Core_Entity::factory('Document_Version', $documents_version_id)->getPath();
	}

	/**
	* Получение информации о текущей версии документа
	*
	* @param int $documents_id идентификатор отображаемого документа
	* @return mixed информарция о версии документа в случае успешного выполнения или false, если текущая версия не найдена
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор документа
	* $documents_id = 12;
	*
	* $row = $documents->GetCurrentDocumentVersion($documents_id);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	*/
	function GetCurrentDocumentVersion($documents_id)
	{
		$documents_id = intval($documents_id);

		if (isset($this->CacheGetCurrentDocumentVersion[$documents_id]))
		{
			return $this->CacheGetCurrentDocumentVersion[$documents_id];
		}

		/* Если добавлено кэширование*/
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'DOCUMENT_CURRENT_VERSION';

			if ($in_cache = $cache->GetCacheContent($documents_id, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$oDocument_Version = Core_Entity::factory('Document', $documents_id)->Document_Versions->getCurrent();

		// Проверяем наличие текущей версии
		// Информация о текущей версии
		$document_version_row = !is_null($oDocument_Version)
			? $this->getArrayDocumentVersion($oDocument_Version)
			: FALSE;

		// Если добавлено кэширование
		if (class_exists('Cache'))
		{
			$cache->Insert($documents_id, $document_version_row, $cache_name);
		}

		$this->CacheGetCurrentDocumentVersion[$documents_id] = $document_version_row;

		return $document_version_row;
	}

	/**
	* Получение информации о версии документа
	*
	* @param int $documents_version_id идентификатор версии документа
	* @return mixed массив с данными о версии документа или false
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор версии документа
	* $documents_version_id = 29;
	*
	* $row = $documents->GetDocumentVersion($documents_version_id);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	*/
	function GetDocumentVersion($documents_version_id)
	{
		$documents_version_id = intval($documents_version_id);
		$oDocument_Version = Core_Entity::factory('Document_Version', $documents_version_id);

		// Информация о текущей версии
		$document_version_row = !is_null($oDocument_Version)
			? $this->getArrayDocumentVersion($oDocument_Version)
			: FALSE;

		return $document_version_row;
	}

	/**
	* Вставка/обновление новых документов
	*
	* @param int $type тип действия 0 - вставка, 1 - обновление
	* @param int $documents_id идентификатор документа
	* @param int $documents_dir_id идентификатор радздела
	* @param int $users_id идентификатор пользователя
	* @param int $documents_status_id идентификатор статуса документа
	* @param string $documents_name наименование документа
	* @param int $documents_version_id идентификатор версии документа
	* @param int $version_current указатель на текущую версию
	* @param int $version_comment комментарий к версии документа
	* @param string $documents_text текст документа
	* @param int $templates_id идентификатор шаблона
	* @return int идентификатор документа
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* $type = 0;
	* $documents_id = 0;
	* $documents_dir_id = 0;
	* $users_id = false;
	* $documents_status_id = 0;
	* $documents_name = 'Тестовый документ';
	* $documents_version_id = 0;
	* $version_current = 1;
	* $version_comment = 'Комментарий к версии';
	* $documents_text = 'Тестовый текст';
	* $templates_id = 1;
	* $site_id = 1;
	*
	* $newid = $documents->insert_documents($type, $documents_id, $documents_dir_id, $users_id, $documents_status_id, $documents_name, $documents_version_id, $version_current, $version_comment, $documents_text, $templates_id, $site_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function insert_documents($type, $documents_id, $documents_dir_id, $users_id, $documents_status_id, $documents_name, $documents_version_id, $version_current, $version_comment, $documents_text, $templates_id, $site_id)
	{
		if ($documents_id == 0)
		{
			$documents_id = NULL;
			$version_current = 1;
		}

		$oDocument = Core_Entity::factory('Document', $documents_id);

		$oDocument->document_dir_id = intval($documents_dir_id);
		$oDocument->document_status_id = intval($documents_status_id);
		$oDocument->name = $documents_name;
		$version_current = intval($version_current);
		$oDocument->site_id = intval($site_id);

		$oDocument->template_id = intval($templates_id);

		if (is_null($documents_id) && $users_id)
		{
			$oDocument->user_id = $users_id;
		}

		$oDocument->save();
		$documents_id = $oDocument->id;

		/* Очистка файлового кэша*/
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'DOCUMENT_CURRENT_VERSION';
			$cache->DeleteCacheItem($cache_name, $documents_id);
		}

		$this->insert_version(0,0,$documents_id,$version_current,$version_comment,$documents_text,$users_id,$templates_id);

		// Событийная индексация
		if ($documents_id != 0)
		{
			// если имется модуль поиска
			if (class_exists('Search'))
			{
				$structure = & singleton('Structure');

				$result=$structure->IndexationStructure(0,0,array('document_id'=>$documents_id));

				if (count($result)!=0)
				{
					$Search = new Search();

					foreach ($result AS $key => $line)
					{
						$Search->Delete_search_words($line[1], $line[4]);
					}

					$Search->Insert_search_word($result);
				}
			}
		}

		return $documents_id;
	}

	/**
	* Вставка/обновления разделов документов
	*
	* @param int $type тип действия 0 - вставка, 1 - обновление
	* @param int $documents_dir_id идентификатор раздела
	* @param int $documents_dir_parent_id идентификатор родительского раздела
	* @param string $documents_dir_name наименование раздела
	* @param int $users_id идентификатор пользователя, если false - берется текущий пользователь.
	* @param int $site_id идентификатор сайта. Если 0, то система получит текущий сайт из константы CURRENT_SITE. В противном случае можно указать ID сайта явно. по умолчанию равен 0.
	* @return int идентификатор отредактированного (вставленного) раздела
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* $type = 0;
	* $documents_dir_id = '';
	* $documents_dir_parent_id = 0;
	* $documents_dir_name = 'Тестовый раздел';
	*
	* // Если 0, то система получит текущий сайт из константы CURRENT_SITE. В противном случае можно указать ID сайта явно
	* $site_id = 0;
	* $users_id = false;
	*
	* $newid = $documents->insert_documents_dir($type, $documents_dir_id, $documents_dir_parent_id, $documents_dir_name, $users_id, $site_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function insert_documents_dir($type, $documents_dir_id, $documents_dir_parent_id,
	$documents_dir_name, $users_id = false, $site_id = 0)
	{
		if ($documents_dir_id == 0)
		{
			$documents_dir_id = NULL;
		}

		$oDocument_Dir = Core_Entity::factory('Document_Dir', $documents_dir_id);

		$oDocument_Dir->parent_id = intval($documents_dir_parent_id);
		$oDocument_Dir->name = $documents_dir_name;
		$oDocument_Dir->site_id = intval($site_id);

		if (is_null($documents_dir_id) && $users_id)
		{
			$oDocument_Dir->user_id = $users_id;
		}

		$oDocument_Dir->save();
	}

	/**
	* Получение информации о документе или обо всех документах.
	*
	* @param int $documents_id идентификатор документа (-1 - выбор всех документов)
	* @return resource реестр документов или данные о конкретном документе
	* @see GetDocument()
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор выбираемого документа
	* $documents_id = 12;
	*
	* $resource = $documents->select_documents($documents_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	*		 print_r($row);
	* }
	* ?>
	* </code>
	*/
	function select_documents($documents_id, $site_id = false)
	{
		$documents_id = intval($documents_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'documents_id'),
				array('document_dir_id', 'documents_dir_id'),
				array('document_status_id', 'documents_status_id'),
				array('name', 'documents_name'),
				array('user_id', 'users_id'),
				array('site_id', 'site_id')
			)
			->from('documents');

		if ($documents_id != -1)
		{
			$queryBuilder->where('id', '=', $documents_id);
		}

		if ($site_id)
		{
			$queryBuilder->where('site_id', '=', $site_id);
		}

		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение информации о документе
	*
	* @param int $documents_id идентификатор документа
	* @return mixed массив с информацией о документе или false
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор документа
	* $documents_id = 12;
	*
	* $row = $documents->GetDocument($documents_id);
	*
	* // Распечатаем результат
	* print_r($row);
	* ?>
	* </code>
	*/
	function GetDocument($documents_id)
	{
		$documents_id = intval($documents_id);
		$oDocument = Core_Entity::factory('Document')->find($documents_id);

		if ($oDocument)
		{
			return $this->getArrayDocument($oDocument);
		}

		return FALSE;
	}

	/**
	* Метод выбора раздела документов
	*
	* @param int $documents_dir_id идентификатор раздела (-1 - выбор всех разделов)
	* @return resource список всех разделов или данные о конкретном разделе
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	* // Идентификатор выбираемого раздела
	* $documents_dir_id = 4;
	*
	* $resource = $documents->select_documents_dir($documents_dir_id);
	*
	* // Распечатаем результат
	* $row = mysql_fetch_assoc($resource);
	* print_r($row);
	* ?>
	* </code>
	*/
	function select_documents_dir($documents_dir_id, $site_id = false)
	{
		$documents_dir_id = intval($documents_dir_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'documents_dir_id'),
				array('parent_id', 'documents_dir_parent_id'),
				array('name', 'documents_dir_name'),
				array('user_id', 'users_id'),
				array('site_id', 'site_id')
			)
			->from('document_dirs');

		if ($documents_dir_id != -1)
		{
			$queryBuilder->where('id', '=', $documents_dir_id);
		}

		if ($site_id)
		{
			$queryBuilder->where('site_id', '=', $site_id);
		}

		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение информации о разделе документов
	*
	* @param int $documents_dir_id идентификатор раздела документов
	* @return resource список всех разделов или данные о конкретном разделе
	*
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор раздела документов
	* $documents_dir_id = 4;
	*
	* $row = $documents->GetDocumentDir($documents_dir_id);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	*/
	function GetDocumentDir($documents_dir_id)
	{
		$documents_dir_id = intval($documents_dir_id);
		$oDocument_Dir = Core_Entity::factory('Document_Dir', $documents_dir_id);

		return $this->getArrayDocumentDir($oDocument_Dir);
	}

	/**
	* Удаления документов
	*
	* @param int $documents_id идентификатор документа
	* @return boolean true при удачном удалении, false - в обратном случае
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор удаляемого документа
	* $documents_id = 7;
	*
	* $result = $documents->del_documents($documents_id);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function del_documents($documents_id)
	{
		$documents_id = intval($documents_id);

		Core_Entity::factory('Document', $documents_id)->markDeleted();

		/* Очистка файлового кэша*/
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'DOCUMENT_CURRENT_VERSION';
			$cache->DeleteCacheItem($cache_name, $documents_id);
		}

		// Событийная индексация, получаем данные о странице
		if (class_exists('Search'))
		{
			$structure= & singleton('Structure');

			$result = $structure->IndexationStructure(0,0,array('document_id' => $documents_id));

			// Событийная индексация - удаляем из базы поиска
			if (count($result)!=0)
			{
				$Search=new Search();
				foreach ($result AS $key => $line)
				{
					$Search->Delete_search_words($line[1], $line[4]);
				}
			}
		}

		return TRUE;
	}

	/**
	* Удаление разделов документов
	*
	* @param int $documents_dir_id идентификатор раздела
	* @return boolean true при удачном удалении, false - в обратном случае
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	* // Идентификатор удаляемого раздела документов
	* $documents_dir_id = 3;
	*
	* $result = $documents->del_documents_dir($documents_dir_id);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	*/
	function del_documents_dir($documents_dir_id)
	{
		$documents_dir_id = intval($documents_dir_id);
		Core_Entity::factory('Document_Dir', $documents_dir_id)->markDeleted();

		return TRUE;
	}

	/**
	* Метод формирования дерева разделов текущего сайта
	*
	* @param int $documents_dir_parent_id идентификатор родительского раздела, с которого начинается формирование
	* @param int $separator_dir отступ для подразделов
	* @param int $documents_dir_id идентификатор раздела, который необходимо пропустить (не включать в дерево вместе с подразделами)
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* $documents_dir_parent_id = 0;
	* $separator_dir = '&nbsp';
	*
	* $documents_tree = $documents->get_documents_dir($documents_dir_parent_id, $separator_dir);
	*
	* // Распечатаем результат
	* print_r ($documents_tree);
	* ?>
	* </code>
	* @return array ассоциативный массив с информацией о дереве разделов
	*/
	function get_documents_dir($documents_dir_parent_id, $separator_dir, $documents_dir_id = 0)
	{
		$documents_dir_parent_id = intval($documents_dir_parent_id);
		$documents_dir_id = intval($documents_dir_id);

		$oDocument_Dir = Core_Entity::factory('Document_Dir');

		if ($documents_dir_id != 0)
		{
			$oDocument_Dir->queryBuilder()
				->where('id', '!=', $documents_dir_id);
		}

		$oDocument_Dir->queryBuilder()
			->where('parent_id', '!=', $documents_dir_parent_id)
			->where('site_id', '!=', CURRENT_SITE)
			->orderBy('name');

		$aDocument_Dirs = $oDocument_Dir->findAll();

		foreach ($aDocument_Dirs as $oDocument_Dir)
		{
			$row = $this->getArrayDocumentDir($oDocument_Dir);

			if ($row['documents_dir_parent_id'] == 0)
			{
				$row['documents_dir_with_separator'] = $row['documents_dir_name'];
			}
			else
			{
				$row['documents_dir_with_separator'] = $separator_dir . $row['documents_dir_name'];
			}

			$this->mas_documents_dir[] = $row;

			// Осуществляем вызов данной функции
			$this->get_documents_dir($row['documents_dir_id'], $separator_dir . $this->separator, $documents_dir_id);
		}

		return $this->mas_documents_dir;
	}

	/**
	* Устаревший метод формирования строки ссылок с родительскими разделами.
	* Использовался в версии 4.х.
	*
	* @param int $documents_dir_id идентификатор раздела
	* @param string $prefix префикс для формирования ссылки на раздел
	* @param string $sufix строку, дописываемую к ссылке
	* @return string строка ссылок с родительскими разделами
	* @access private
	*/
	function get_path($documents_dir_id, $prefix, $sufix)
	{
		$documents_dir_id = intval($documents_dir_id);

		$oDocument_Dir = Core_Entity::factory('Document_Dir')->find($documents_dir_id);

		if (!is_null($oDocument_Dir->id))
		{
			return $this->get_path($oDocument_Dir->parent_id, $prefix, $sufix) .
			'<a href=' . $prefix . $oDocument_Dir->id . $sufix . '>' .
			htmlspecialchars($oDocument_Dir->name) . '</a> // ';
		}
	}

	/**
	* Метод добавления(редактирования) статусов документов
	*
	* @param int $type тип действия 0 - вставка, 1 - обновление
	* @param int $status_id идентификатор статуса документа
	* @param string_type $status_name наименование статуса
	* @param string $status_description описание статуса
	* @param int $users_id идентификатор пользователя, если false - берется текущий пользователь.
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* $type = 0;
	* $status_id = '';
	* $status_name = 'Новый статус';
	* $status_description = 'Описание нового статуса';
	*
	* $newid = $documents->insert_status($type, $status_id, $status_name, $status_description);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return int идентификатор нового или редатируемого статуса (в зависимости от типа действия)
	*/
	function insert_status($type, $status_id, $status_name, $status_description,
	$users_id = false, $site_id = false)
	{
		if ($status_id == 0)
		{
			$status_id = NULL;
		}

		$oDocument_Status = Core_Entity::factory('Document_Status', $status_id);

		$oDocument_Status->name = $status_name;
		$oDocument_Status->description = $status_description;

		if ($site_id)
		{
			$oDocument_Status->site_id = $site_id;
		}

		if (is_null($status_id) && $users_id)
		{
			$oDocument_Status->user_id = $users_id;
		}

		$oDocument_Status->save();

		return TRUE;
	}

	/**
	* Получение информации о статусе документа
	*
	* @param int $documents_status_id идентификатор статуса (false - выбор всех статусов)
	* @param int $site_id идентификатор сайта
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор статуса
	* $documents_status_id = 1;
	*
	* $resource = $documents->select_status($documents_status_id);
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
	function select_status($documents_status_id, $site_id = false)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'documents_status_id'),
				array('name', 'documents_status_name'),
				array('description', 'documents_status_description'),
				array('user_id', 'users_id'),
				array('site_id', 'site_id')
			)
			->from('document_statuses');

		if ($documents_status_id != -1 && $documents_status_id)
		{
			$queryBuilder->where('id', '=', $documents_status_id);
		}

		if ($site_id == FALSE)
		{
			$site_id = CURRENT_SITE;
		}
		$queryBuilder->where('site_id', '=', $site_id);
		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Метод удаления статуса документов
	*
	* @param int $status_id идентификатор удаляемого статуса документов
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор статуса документа
	* $status_id = 3;
	*
	* $result = $documents->delete_status($status_id);
	*
	* // Распечатаем результат
	* echo $result;
	*
	* ?>
	* </code>
	* @return boolean истина при удачном удалении, ложь - в обратном случае
	*/
	function delete_status($status_id)
	{
		$status_id = intval($status_id);
		Core_Entity::factory('Document_Status', $status_id)->markDeleted();

		return TRUE;
	}

	/**
	* Метод добавления/редактирования записей о версиях
	*
	* @param int $type тип действия 0 - вставка, 1 - обновление
	* @param int $documents_version_id идентификатор версии документа
	* @param int $documents_id идентификатор документа
	* @param int $version_current флаг текущей версии
	* @param string $version_comment комментарий к версии
	* @param string $documents_text текст документа
	* @param int $users_id идентификатор пользователя
	* @param int $templates_id идентификатор шаблона
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* $type = 0;
	* $documents_version_id = '';
	* $documents_id = 12;
	* $version_current = 1;
	* $version_comment = 'Новая версия';
	* $documents_text = 'Тестовый текст';
	* $users_id = '';
	* $templates_id = '';
	*
	* $newid = $documents->insert_version($type, $documents_version_id, $documents_id, $version_current, $version_comment, $documents_text, $users_id, $templates_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	* @return int идентификатор новой или редатируемой версии (в зависимости от типа действия)
	*/
	function insert_version($type, $documents_version_id, $documents_id, $version_current, $version_comment, $documents_text, $users_id, $templates_id)
	{
		if ($documents_version_id == 0)
		{
			$documents_version_id = NULL;
		}

		$oDocument_Version = Core_Entity::factory('Document_Version', $documents_version_id);

		$oDocument_Version->document_id = intval($documents_id);
		$oDocument_Version->current = intval($version_current);
		$oDocument_Version->description = $version_comment;
		$oDocument_Version->template_id = intval($templates_id);
		$oDocument_Version->datetime = date("Y-m-d H:i:s");

		if (is_null($documents_version_id) && $users_id)
		{
			$oDocument_Version->user_id = $users_id;
		}

		$oDocument_Version->save();
		$oDocument_Version->saveFile($documents_text);

		if ($version_current == 1)
		{
			$oDocument_Version->setCurrent();
		}

		// Очищаем кэш для текущих версий документа
		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'DOCUMENT_CURRENT_VERSION';
			$cache->DeleteCacheItem($cache_name, $documents_id);
		}

		return TRUE;
	}

	/**
	* Метод удаления записей о версиях
	*
	* @param int $documents_version_id идентификатор удаляемой версии
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор версии документа
	* $documents_version_id = 28;
	*
	* $result = $documents->delete_version($documents_version_id);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	* @return boolean true при удачном удалении, false - в обратном случае
	*/
	function delete_version($documents_version_id)
	{
		$documents_version_id = intval($documents_version_id);
		Core_Entity::factory('Document_Version', $documents_version_id)->markDeleted();
	}

	/**
	* Метод удаления записей обо всех не текущих версиях
	*
	* @param int $documents_id идентификатор документа
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор документа
	* $documents_id = 12;
	*
	* $result = $documents->delete_old_version($documents_id);
	*
	* // Распечатаем результат
	* echo $result;
	* ?>
	* </code>
	* @return boolean true при удачном удалении, false - в обратном случае
	*/
	function delete_old_version($documents_id)
	{
		$documents_id = intval($documents_id);
		Core_Entity::factory('Document', $documents_id)->deleteOldVersions();

		return TRUE;
	}

	/**
	* Метод выборки информации о версиях
	*
	* @param int $documents_id идентификатор документа
	* @param int $documents_version_id идентификатор версии, если false - версия неизвестна. При этом текущую версию необходимо установить в 1
	* @param int $current указатель на текущую версию, по умолчанию равен 1 - выбрать текущую версию.
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* $documents_id = 12;
	* $documents_version_id = false;
	*
	* $resource = $documents->select_version($documents_id, $documents_version_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($resource))
	* {
	* 		print_r($row);
	* }
	* ?>
	* </code>
	* @return resource информация о версиях документа
	*/
	function select_version($documents_id, $documents_version_id, $current = 1)
	{
		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'documents_version_id'),
				array('document_id', 'documents_id'),
				array('datetime', 'documents_version_date_time'),
				array('current', 'documents_version_current'),
				array('description', 'documents_version_comment'),
				array('user_id', 'users_id'),
				array('template_id', 'templates_id')
			)
			->from('document_versions')
			->where('document_id', '=', $documents_id);


		if ($documents_version_id != -1 && $documents_version_id !== false)
		{
			$queryBuilder->where('id', '=', $documents_version_id);
		}

		if ($current == 1)
		{
			$queryBuilder->where('current', '=', 1);
		}

		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Метод для переобозначения текущей версии документа
	*
	* @param int $documents_id идентификатор документа
	* @param int $documents_version_id идентификатор текущей версии
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();

	* $documents_id = 7;
 	* $documents_version_id = 20;

	* $new_current_version = $documents->current_version($documents_id, $documents_version_id);
 	* // Распечатаем результат
	* echo $new_current_version;
	* ?>
	* </code>
	* @return boolean
	*/
	function current_version($documents_id, $documents_version_id)
	{
		$documents_id = intval($documents_id);
		$documents_version_id = intval($documents_version_id);

		$oDocument_Version = Core_Entity::factory('Document_Version', $documents_version_id)->setCurrent();

		return TRUE;
	}

	/**
	* Метод копирования документа
	*
	* @param int $documents_id идентификатор копируемого документа
	* @param int $documents_dir_parent_id идентификатор категории, к которой необходимо отнести скопированный документ (не обязательный параметр. Если имеет значение -1 - скопированные документы кладутся в ту же директорию, что и копируемые. по умолчанию -1)
	* @param bool $return_array_ids Флаг, указывающий, нужно ли запоминать соответсятвия старых и новых идентификаторов (по умолчанию - false)
	* @param array $array_template_ids Массив с соответствиями старых и новых идентификаторов (не обязательный параметр)
	* @param array $array_document_status_ids Массив с соответствиями новых и старых идентификаторов статусов документов (не обязательный параметр)
	* <code>
	* <?php
	* $documents = new documents();
	*
	* $documents_id = 7;
	*
	* $newid = $documents->CopyDocuments($documents_id);
	*
	* // Распечатаем результат
	* echo $newid;
	* ?>
	* </code>
	*/
	function CopyDocuments($documents_id, $new_site_id = false, $documents_dir_parent_id = -1, $return_array_ids = false, $array_template_ids = array(), $array_document_status_ids = array())
	{
		$documents_id = intval($documents_id);

		$oNewDocument = Core_Entity::factory('Document', $documents_id)->copy();

		if ($documents_dir_parent_id != -1)
		{
			$oNewDocument->document_dir_id = $documents_dir_parent_id;
		}

		if ($new_site_id !== false)
		{
			$oNewDocument->site_id = intval($new_site_id);
		}

		$oNewDocument->save();

		return $oNewDocument->id;
	}

	/**
	* Построение массива пути от текущего узла к корневому. Предназначен для использования только в центре администрирования.
	*
	* @param int $documents_dir_id идентификатор текущего узла
	* @param bool $first_call первый вызов ф-ции, по умолчанию - true
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор раздела документов
	* $documents_dir_id = 4;
	*
	* $path = $documents->GetDocumentsPathArray($documents_dir_id);
	*
	* // Распечатаем результат
	* print_r($path);
	* ?>
	* </code>
	* @return array массив с элементами пути группы
	*/
	function GetDocumentsPathArray($documents_dir_id, $first_call = TRUE)
	{
		throw new Core_Exception("Method GetDocumentsPathArray() excluded.");
	}

	/**
	* Метод формирования дерева разделов документов.
	*
	* @param int $documents_dir_parent_id - идентификатор родительского раздела
	* @param string $separator - символ (строка)-разделитель
	* @param bool $first_call первый вызов ф-ции, по умолчанию - true
	* @param bool $first_call
	* @param int $site_id идентификатор сайта, необязательный параметр. Если не передан - определяется автоматически.
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор родительского раздела документов
	* $documents_dir_parent_id = 0;
	* $separator = '';
	* $documents_dir_tree = $documents->GetDocumentsDirTree($documents_dir_parent_id, $separator);
	*
	* // Распечатаем результат
	* print_r ($documents_dir_tree);
	* ?>
	* </code>
	* @return array массив с данными о дереве разделов документов
	*/
	function GetDocumentsDirTree($documents_dir_parent_id, $separator,
	$current_group_id = FALSE, $first_call = TRUE, $site_id = FALSE)
	{
		$documents_dir_parent_id = intval($documents_dir_parent_id);
		$first_call = Core_Type_Conversion::toBool($first_call);

		if ($first_call)
		{
			$this->path_array = array();
		}

		if ($site_id !== FALSE)
		{
			$site_id = intval($site_id);
		}
		else
		{
			$site_id = CURRENT_SITE;
		}

		$aDocument_Dirs = Core_Entity::factory('Document_Dir')
			->getByParentIdAndSiteId($documents_dir_parent_id, $site_id);

		foreach ($aDocument_Dirs as $oDocument_Dir)
		{
			if ($current_group_id)
			{
				if ($oDocument_Dir->id == $current_group_id)
				{
					continue;
				}
			}

			$this->path_array[] = array(
				$oDocument_Dir->id,
				$separator . htmlspecialchars($oDocument_Dir->name)
			);

			$this->GetDocumentsDirTree($oDocument_Dir->id, $separator . $separator, $current_group_id, false);
		}

		return $this->path_array;
	}

	/**
	* Получение списка документов раздела без учета подразделов
	* @param int $documents_dir_id идентификатор раздела документов
	* @param int $site_id идентификатор сайта (не обязательный параметр. Если не передан, или имеет значение 0, то подставляется идентификатор текущего сайта)
	* <br />Пример использования:
	* <code>
	* <?php
	* $documents = new documents();
	*
	* // Идентификатор раздела документов
	* $documents_dir_id = 0;
	*
	* $documents_from_dir = $documents->GetDocumentsFromDir($documents_dir_id);
	*
	* // Распечатаем результат
	* while($row = mysql_fetch_assoc($documents_from_dir))
	* {
	* 		print_r($row);
	* }
	* ?>
	* </code>
	* @return resource в случае успешного выполнения метода, false - в противном случае
	*/
	function GetDocumentsFromDir($documents_dir_id, $site_id = 0)
	{
		$documents_dir_id = intval($documents_dir_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'documents_id'),
				array('document_dir_id', 'documents_dir_id'),
				array('document_status_id', 'documents_status_id'),
				array('name', 'documents_name'),
				array('user_id', 'users_id'),
				array('site_id', 'site_id')
			)
			->from('document_versions')
			->where('document_dir_id', '=', $documents_dir_id)
			->orderBy('name');

		$site_id = $site_id ? intval($site_id) : CURRENT_SITE;
		$queryBuilder->where('site_id', '=', $site_id);

		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	 * Копирование групп документов с документами
	 *
	 * @param int $documents_dir_parent_id идентификатор группы документов
	 * @param int $new_documents_dir_parent_id идентификатор скопированной группы документов
	 * @param bool $return_array_ids Флаг, указывающий, нужно ли возвращать массив с соответствиями старых и новых идентификаторов фокументов (по умолчанию false)
	 * @param array $array_template_ids Массив с соответствиями новых и старых идентификаторов макетов (не обязательный параметр)
	 * @param array $array_document_status_ids Массив с соответствиями новых и старых идентификаторов статусов документов (не обязательный параметр)
	 */
	function CopyDocumentsDir($documents_dir_parent_id, $site_id, $new_site_id = false, $new_documents_dir_parent_id = 0, $return_array_ids = false, $array_template_ids = array(), $array_document_status_ids = array())
	{
		$oNewDocument_Dir = Core_Entity::factory('Document_Dir', $documents_dir_parent_id)->copy();

		if ($new_site_id)
		{
			$oNewDocument_Dir->site_id = $new_site_id;
			$oNewDocument_Dir->save();
		}

		return $oNewDocument_Dir->id;
	}

	/**
	 * Копирование текущей версии документа
	 *
	 * @param int $documents_id Идентификатор документа
	 * @return mixed int или false
	 */
	function CopyCurrentDocumentVersion($documents_id, $new_document_id = 0, $array_template_ids = array())
	{
		throw new Core_Exception("Method CopyCurrentDocumentVersion() excluded.");
	}

	/**
	 * Копирование статуса документа
	 *
	 * @param int $documents_status_id Идентификатор копируемого статуса документа
	 * @param int $new_site_id Идентификатор сайта, к которому необходимо отнести скопированный статус (не обязательный параметр. Если не указан, скопированный статус будет отнесен к тому же сайту, что и копируемый)
	 * @return mixed false или int Идентификатор скопированного статуса
	 */
	function CopyDocumentStatus($documents_status_id, $new_site_id = 0)
	{
		$documents_status_id = intval($documents_status_id);

		$oDocument_Status = Core_Entity::factory('Document_Status', $documents_status_id)->copy();

		if ($new_site_id)
		{
			$oDocument_Status->site_id = intval($new_site_id);
			$oDocument_Status->save();
		}

		return $oDocument_Status->id;
	}
}