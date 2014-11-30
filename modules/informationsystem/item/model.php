<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Information systems.
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Item_Model extends Core_Entity
{
	/**
	 * Model name
	 * @var mixed
	 */
	protected $_modelName = 'informationsystem_item';

	/**
	 * Backend property
	 * @var mixed
	 */
	public $img = 1;

	/**
	 * Backend property
	 * @var mixed
	 */
	public $comment_field = NULL;

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'informationsystem_item' => array('foreign_key' => 'shortcut_id'),
		'tag' => array('through' => 'tag_informationsystem_item'),
		'tag_informationsystem_item' => array(),
		'comment' => array('through' => 'comment_informationsystem_item'),
		'vote' => array('through' => 'vote_informationsystem_item')
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'informationsystem' => array(),
		'informationsystem_group' => array(),
		'informationsystem_item' => array('foreign_key' => 'shortcut_id'),
		'siteuser' => array(),
		'siteuser_group' => array(),
		'user' => array()
	);

	/**
	 * Forbidden tags. If list of tags is empty, all tags will show.
	 * @var array
	 */
	protected $_forbiddenTags = array(
		'datetime',
		'start_datetime',
		'end_datetime',
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'sorting' => 0,
		'indexing' => 1,
		'siteuser_id' => 0,
		'active' => 1,
		'showed' => 0,
		'shortcut_id' => 0,
		'siteuser_group_id' => -1,
		'start_datetime' => '0000-00-00 00:00:00',
		'end_datetime' => '0000-00-00 00:00:00',
		'image_large' => '',
		'image_small' => ''
	);

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'informationsystem_items.sorting' => 'ASC',
		//'informationsystem_items.name' => 'ASC',
	);

	/**
	 * Constructor.
	 * @param int $id entity ID
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		if (is_null($id))
		{
			$oUserCurrent = Core_Entity::factory('User', 0)->getCurrent();
			$this->_preloadValues['user_id'] = is_null($oUserCurrent) ? 0 : $oUserCurrent->id;
			$this->_preloadValues['datetime'] = Core_Date::timestamp2sql(time());
			$this->_preloadValues['ip'] = Core_Array::get($_SERVER, 'REMOTE_ADDR', '127.0.0.1');
		}

		if (/*$this->_loaded && */$this->shortcut_id != 0)
		{
			$this->img = 2;
		}
	}

	/**
	 * Apply tags for item
	 * @param string $sTags string of tags, separated by comma
	 * @return self
	 */
	public function applyTags($sTags)
	{
		$aTags = explode(',', $sTags);

		// Удаляем связь метками
		$this->Tag_Informationsystem_Items->deleteAll(FALSE);

		foreach ($aTags as $tag_name)
		{
			$tag_name = trim($tag_name);

			if ($tag_name != '')
			{
				$oTag = Core_Entity::factory('Tag')->getByName($tag_name, FALSE);

				if (is_null($oTag))
				{
					$oTag = Core_Entity::factory('Tag');
					$oTag->name = $oTag->path = $tag_name;
					$oTag->save();
				}
				$this->add($oTag);
			}
		}

		return $this;
	}

	/**
	 * Values of all properties of item
	 * @var array
	 */
	protected $_propertyValues = NULL;

	/**
	 * Values of all properties of item
	 * Значения всех свойств товара
	 * @param boolean $bCache cache mode status
	 * @return array Property_Value
	 */
	public function getPropertyValues($bCache = TRUE)
	{
		if ($bCache && !is_null($this->_propertyValues))
		{
			return $this->_propertyValues;
		}

		// Need cache
		$aProperties = Core_Entity::factory('Informationsystem_Item_Property_List', $this->informationsystem_id)
			->Properties
			->findAll();

		//$aReturn = array();
		$aProperiesId = array();
		foreach ($aProperties as $oProperty)
		{
			$aProperiesId[] = $oProperty->id;

			/*$aProperty_Values = $oProperty->getValues($this->id, $bCache);

			foreach ($aProperty_Values as $oProperty_Value)
			{
				if ($oProperty->type == 2)
				{
					$oProperty_Value->setHref($this->getItemHref());
				}

				$aReturn[] = $oProperty_Value;
			}*/
		}

		$aReturn = Property_Controller_Value::getPropertiesValues($aProperiesId, $this->id);

		// setHref()
		foreach ($aReturn as $oProperty_Value)
		{
			if ($oProperty_Value->Property->type == 2)
			{
				$oProperty_Value
					->setHref($this->getItemHref())
					->setDir($this->getItemPath());
			}
		}

		if ($bCache)
		{
			$this->_propertyValues = $aReturn;
		}

		return $aReturn;
	}

	/**
	 * Delete object from database
	 * @param mixed $primaryKey primary key for deleting object
	 * @return Core_Entity
	 */
	public function delete($primaryKey = NULL)
	{
		if (is_null($primaryKey))
		{
			$primaryKey = $this->getPrimaryKey();
		}

		$this->id = $primaryKey;

		// Удаляем значения доп. свойств
		$aPropertyValues = $this->getPropertyValues();
		foreach($aPropertyValues as $oPropertyValue)
		{
			$oPropertyValue->Property->type == 2 && $oPropertyValue->setDir($this->getItemPath());
			$oPropertyValue->delete();
		}

		// Удаляем комментарии
		$this->Comments->deleteAll(FALSE);

		// Удаляем ярлыки
		$this->Informationsystem_Items->deleteAll(FALSE);

		// Удаляем теги
		$this->Tag_Informationsystem_Items->deleteAll(FALSE);

		// Удаляем директорию информационного элемента
		$this->deleteDir();

		if (!is_null($this->Informationsystem_Group->id))
		{
			// Уменьшение количества элементов в группе
			$this->Informationsystem_Group->decCountItems();
		}

		return parent::delete($primaryKey);
	}

	/**
	 * Get item by group id
	 * @param int $group_id group id
	 * @return array
	 */
	public function getByGroupId($group_id)
	{
		$this->queryBuilder()
			//->clear()
			->where('informationsystem_group_id', '=', $group_id)
			->where('shortcut_id', '=', 0);

		return $this->findAll();
	}

	/**
	 * Get item by group id and path
	 * @param int $group_id group id
	 * @param string $path path
	 * @return Informationsystem_Item|NULL
	 */
	public function getByGroupIdAndPath($group_id, $path)
	{
		$this->queryBuilder()
			//->clear()
			->where('path', '=', $path)
			->where('informationsystem_group_id', '=', $group_id)
			->where('shortcut_id', '=', 0)
			->limit(1);

		$aInformationsystem_Items = $this->findAll();

		return isset($aInformationsystem_Items[0]) ? $aInformationsystem_Items[0] : NULL;
	}

	/**
	 * Move item to another group
	 * @param int $informationsystem_group_id group id
	 * @return self
	 */
	public function move($informationsystem_group_id)
	{
		$this->informationsystem_group_id = $informationsystem_group_id;
		return $this->save();
	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		$newObject = parent::copy();
		$newObject->path = '';
		$newObject->showed = 0;
		$newObject->save();

		// Существует файл большого изображения для оригинального элемента
		if (is_file($this->getLargeFilePath()))
		{
			$newObject->saveLargeImageFile($this->getLargeFilePath(), $this->image_large);
		}

		// Существует файл малого изображения для оригинального элемента
		if (is_file($this->getSmallFilePath()))
		{
			$newObject->saveSmallImageFile($this->getSmallFilePath(), $this->image_small);
		}

		$aPropertyValues = $this->getPropertyValues();
		foreach($aPropertyValues as $oPropertyValue)
		{
			$oNewPropertyValue = clone $oPropertyValue;
			$oNewPropertyValue->entity_id = $newObject->id;
			$oNewPropertyValue->save();

			if ($oNewPropertyValue->Property->type == 2)
			{
				$oPropertyValue->setDir($this->getItemPath());
				$oNewPropertyValue->setDir($newObject->getItemPath());

				if (is_file($oPropertyValue->getLargeFilePath()))
				{
					try
					{
						Core_File::copy($oPropertyValue->getLargeFilePath(), $oNewPropertyValue->getLargeFilePath());
					} catch (Exception $e) {}
				}

				if (is_file($oPropertyValue->getSmallFilePath()))
				{
					try
					{
						Core_File::copy($oPropertyValue->getSmallFilePath(), $oNewPropertyValue->getSmallFilePath());
					} catch (Exception $e) {}
				}
			}
		}

		if (Core::moduleIsActive('tag'))
		{
			$aTags = $this->Tags->findAll();
			foreach($aTags as $oTag)
			{
				$newObject->add($oTag);
			}
		}

		return $newObject;
	}

	/**
	 * Get item path
	 * @return string
	 */
	public function getItemPath()
	{
		return $this->Informationsystem->getPath() . '/' . Core_File::getNestingDirPath($this->id, $this->Informationsystem->Site->nesting_level) . '/item_' . $this->id . '/';
	}

	/**
	 * Get item href
	 * @return string
	 */
	public function getItemHref()
	{
		return '/' . $this->Informationsystem->getHref() . '/' . Core_File::getNestingDirPath($this->id, $this->Informationsystem->Site->nesting_level) . '/item_' . $this->id . '/';
	}

	/**
	 * Get small file path
	 * @return string
	 */
	public function getSmallFilePath()
	{
		return $this->getItemPath() . $this->image_small;
	}

	/**
	 * Get small file href
	 * @return string
	 */
	public function getSmallFileHref()
	{
		return $this->getItemHref() . rawurlencode($this->image_small);
	}

	/**
	 * Get large file path
	 * @return string
	 */
	public function getLargeFilePath()
	{
		return $this->getItemPath() . $this->image_large;
	}

	/**
	 * Get large file href
	 * @return string
	 */
	public function getLargeFileHref()
	{
		return $this->getItemHref() . rawurlencode($this->image_large);
	}

	/**
	 * Set large image sizes
	 * @return self
	 */
	public function setLargeImageSizes()
	{
		$path = $this->getLargeFilePath();

		if (is_file($path))
		{
			$aSizes = Core_Image::instance()->getImageSize($path);
			if ($aSizes)
			{
				$this->image_large_width = $aSizes['width'];
				$this->image_large_height = $aSizes['height'];
				$this->save();
			}
		}

		return $this;
	}

	/**
	 * Specify large image for item
	 * @param string $fileSourcePath source file
	 * @param string $fileName target file name
	 * @return self
	 */
	public function saveLargeImageFile($fileSourcePath, $fileName)
	{
		$fileName = Core_File::filenameCorrection($fileName);
		$this->createDir();

		$this->image_large = $fileName;
		$this->save();
		Core_File::upload($fileSourcePath, $this->getItemPath() . $fileName);
		$this->setLargeImageSizes();
		return $this;
	}

	/**
	 * Set small image sizes
	 * @return self
	 */
	public function setSmallImageSizes()
	{
		$path = $this->getSmallFilePath();

		if (is_file($path))
		{
			$aSizes = Core_Image::instance()->getImageSize($path);
			if ($aSizes)
			{
				$this->image_small_width = $aSizes['width'];
				$this->image_small_height = $aSizes['height'];
				$this->save();
			}
		}

		return $this;
	}

	/**
	 * Specify small image for item
	 * @param string $fileSourcePath source file
	 * @param string $fileName target file name
	 * @return self
	 */
	public function saveSmallImageFile($fileSourcePath, $fileName)
	{
		$fileName = Core_File::filenameCorrection($fileName);
		$this->createDir();

		$this->image_small = $fileName;
		$this->save();
		Core_File::upload($fileSourcePath, $this->getItemPath() . $fileName);
		$this->setSmallImageSizes();
		return $this;
	}

	/**
	 * Check and correct duplicate path
	 * @return self
	 */
	public function checkDuplicatePath()
	{
		$oInformationsystem = $this->InformationSystem;

		// Search the same item or group
		$oSameInformationsystemItem = $oInformationsystem->Informationsystem_Items->getByGroupIdAndPath($this->informationsystem_group_id, $this->path);
		if (!is_null($oSameInformationsystemItem) && $oSameInformationsystemItem->id != $this->id)
		{
			$this->path = Core_Guid::get();
		}

		$oSameInformationsystemGroup = $oInformationsystem->Informationsystem_Groups->getByParentIdAndPath($this->informationsystem_group_id, $this->path);
		if (!is_null($oSameInformationsystemGroup))
		{
			$this->path = Core_Guid::get();
		}

		return $this;
	}

	/**
	 * Make url path
	 * @return self
	 */
	public function makePath()
	{
		if ($this->InformationSystem->url_type == 1)
		{
			try {
				$this->path = Core_Str::transliteration(
					Core::$mainConfig['translate']
						? Core_Str::translate($this->name)
						: $this->name
				);
			} catch (Exception $e) {
				$this->path = Core_Str::transliteration($this->name);
			}

			$this->checkDuplicatePath();
		}
		elseif ($this->id)
		{
			$this->path = $this->id;
		}
		else
		{
			$this->path = Core_Guid::get();
		}

		return $this;
	}

	/**
	 * Save object.
	 *
	 * @return self
	 */
	public function save()
	{
		if (is_null($this->path))
		{
			$this->makePath();
		}
		elseif (in_array('path', $this->_changedColumns))
		{
			$this->checkDuplicatePath();
		}

		parent::save();

		if ($this->path == '' && !$this->deleted && $this->makePath())
		{
			$this->path != '' && $this->save();
		}

		return $this;
	}

	/**
	 * Create item's directory for files
	 * @return self
	 */
	public function createDir()
	{
		if (!is_dir($this->getItemPath()))
		{
			try
			{
				Core_File::mkdir($this->getItemPath(), CHMOD, TRUE);
			} catch (Exception $e) {}
		}

		return $this;
	}

	/**
	 * Delete item's directory for files
	 * @return self
	 */
	public function deleteDir()
	{
		// Удаляем файл большого изображения элемента
		$this->deleteLargeImage();

		// Удаляем файл малого изображения элемента
		$this->deleteSmallImage();

		if (is_dir($this->getItemPath()))
		{
			try
			{
				Core_File::deleteDir($this->getItemPath());
			} catch (Exception $e) {}
		}

		return $this;
	}

	/**
	 * Delete item's large image
	 * @return self
	 */
	public function deleteLargeImage()
	{
		try
		{
			Core_File::delete($this->getLargeFilePath());
		} catch (Exception $e) {}
		$this->image_large = '';
		return $this->save();
	}

	/**
	 * Delete item's small image
	 * @return self
	 */
	public function deleteSmallImage()
	{
		try
		{
			Core_File::delete($this->getSmallFilePath());
		} catch (Exception $e) {}
		$this->image_small = '';
		return $this->save();
	}

	/**
	 * Change item status
	 * @return self
	 */
	public function changeActive()
	{
		$this->active = 1 - $this->active;
		$this->save();

		$aItemShortcuts = $this->Informationsystem_Items->findAll();
		foreach($aItemShortcuts as $oItemShortcut)
		{
			$oItemShortcut->active = 1 - $oItemShortcut->active;
			$oItemShortcut->save();
		}
		return $this;
	}

	/**
	 * Change indexation mode
	 *	@return self
	 */
	public function changeIndexation()
	{
		$this->indexing = 1 - $this->indexing;
		return $this->save();
	}

	/**
	 * Create shortcut and move into group $group_id
	 * @param int $group_id group id
	 * @return self
	 */
	public function shortcut($group_id = NULL)
	{
		$oInformationsystem_ItemShortcut = Core_Entity::factory('Informationsystem_Item');

		$object = $this->shortcut_id
			? $this->Informationsystem_Item
			: $this;

		$oInformationsystem_ItemShortcut->informationsystem_id = $object->informationsystem_id;
		$oInformationsystem_ItemShortcut->shortcut_id = $object->id;
		$oInformationsystem_ItemShortcut->name = ''/*$object->name*/;
		$oInformationsystem_ItemShortcut->path = '';
		$oInformationsystem_ItemShortcut->indexing = 0;

		$oInformationsystem_ItemShortcut->informationsystem_group_id =
			is_null($group_id)
			? $object->informationsystem_group_id
			: $group_id;

		$oInformationsystem_ItemShortcut->save();
		return $this;
	}

	/**
	 * Get path to item's files
	 * @return string
	 */
	public function getPath()
	{
		$sPath = ($this->path == ''
			? $this->id
			: $this->path) . '/';

		if ($this->informationsystem_group_id)
		{
			$sPath = $this->Informationsystem_Group->getPath() . $sPath;
		}

		return $sPath;
	}

	/**
	 * Get the ID of the user group
	 * @return int
	 */
	public function getSiteuserGroupId()
	{
		// как у родителя
		if ($this->siteuser_group_id == -1)
		{
			$result = $this->informationsystem_group_id
				? $this->InformationSystem_Group->getSiteuserGroupId()
				: $this->InformationSystem->siteuser_group_id;
		}
		else
		{
			$result = $this->siteuser_group_id;
		}

		return intval($result);
	}

	/**
	 * Backend callback method
	 * @return string
	 */
	public function name()
	{
		$object = $this->shortcut_id
			? $this->Informationsystem_Item
			: $this;

		$oCore_Html_Entity_Div = Core::factory('Core_Html_Entity_Div')->value(
			htmlspecialchars($object->name)
		);

		$bRightTime = ($this->start_datetime == '0000-00-00 00:00:00' || time() > Core_Date::sql2timestamp($this->start_datetime))
			&& ($this->end_datetime == '0000-00-00 00:00:00' || time() < Core_Date::sql2timestamp($this->end_datetime));

		!$bRightTime && $oCore_Html_Entity_Div->class('wrongTime');

		// Зачеркнут в зависимости от своего статуса
		if (!$this->active)
		{
			$oCore_Html_Entity_Div->class('inactive');
		}
		elseif ($bRightTime)
		{
			$oCurrentAlias = $object->Informationsystem->Site->getCurrentAlias();

			if ($oCurrentAlias)
			{
				$href = 'http://' . $oCurrentAlias->name
					. $object->Informationsystem->Structure->getPath()
					. $object->getPath();

				$oCore_Html_Entity_Div
				->add(
					Core::factory('Core_Html_Entity_A')
						->href($href)
						->target('_blank')
						->add(
							Core::factory('Core_Html_Entity_Img')
							->src('/admin/images/new_window.gif')
							->class('img_line')
						)
				);
			}
		}
		elseif (!$bRightTime)
		{
			$oCore_Html_Entity_Div
				->add(
					Core::factory('Core_Html_Entity_Img')
						->src('/admin/images/mesures.gif')
						->class('img_line')
				);
		}

		$oCore_Html_Entity_Div->execute();
	}

	/**
	 * Backend callback method
	 * @param Admin_Form_Field $oAdmin_Form_Field
	 * @param Admin_Form_Controller $oAdmin_Form_Controller
	 * @return string
	 */
	public function adminComment($oAdmin_Form_Field, $oAdmin_Form_Controller)
	{
		ob_start();

		$windowId = $oAdmin_Form_Controller->getWindowId();

		$queryBuilder = Core_QueryBuilder::select(array('COUNT(*)', 'count'))
			->from('comment_informationsystem_items')
			->join('comments', 'comments.id', '=', 'comment_informationsystem_items.comment_id')
			->where('informationsystem_item_id', '=', $this->id)
			->where('comments.deleted', '=', 0);

		$aCountComments = $queryBuilder->execute()->asAssoc()->current();

		$iCountComments = $aCountComments['count'];

		Core::factory('Core_Html_Entity_A')
			->href('/admin/informationsystem/item/comment/index.php?informationsystem_item_id=' . $this->id)
			->onclick("$.adminLoad({path: '/admin/informationsystem/item/comment/index.php', additionalParams: 'informationsystem_item_id={$this->id}', windowId: '{$windowId}'}); return false")
			->value(htmlspecialchars($iCountComments))
			->execute();

		return ob_get_clean();
	}

	/**
	 * Search indexation
	 * @return Search_Page_Model
	 * @hostcms-event informationsystem_item.onBeforeIndexing
	 * @hostcms-event informationsystem_item.onAfterIndexing
	 */
	public function indexing()
	{
		$oSearch_Page = Core_Entity::factory('Search_Page');

		Core_Event::notify($this->_modelName . '.onBeforeIndexing', $this, array($oSearch_Page));

		$oSearch_Page->text = $this->text . ' ' . $this->description . ' ' . $this->name . ' ' . $this->id . ' ' . $this->seo_title . ' ' . $this->seo_description . ' ' . $this->seo_keywords . ' ' . $this->path . ' ';

		$oSearch_Page->title = $this->name;

		// комментарии к информационному элементу
		$aComments = $this->Comments->findAll();
		foreach ($aComments as $oComment)
		{
			$oSearch_Page->text .= $oComment->author . ' ' . $oComment->text . ' ';
		}

		if (Core::moduleIsActive('tag'))
		{
			$aTags = $this->Tags->findAll();
			foreach ($aTags as $oTag)
			{
				$oSearch_Page->text .= $oTag->name . ' ';
			}
		}

		$aPropertyValues = $this->getPropertyValues();
		foreach ($aPropertyValues as $oPropertyValue)
		{
			// List
			if ($oPropertyValue->Property->type == 3 && Core::moduleIsActive('list'))
			{
				if ($oPropertyValue->value != 0)
				{
					$oList_Item = $oPropertyValue->List_Item;
					$oList_Item->id && $oSearch_Page->text .= $oList_Item->value . ' ';
				}
			}
			// Informationsystem
			elseif ($oPropertyValue->Property->type == 5 && Core::moduleIsActive('informationsystem'))
			{
				if ($oPropertyValue->value != 0)
				{
					$oInformationsystem_Item = $oPropertyValue->Informationsystem_Item;
					if ($oInformationsystem_Item->id)
					{
						$oSearch_Page->text .= $oInformationsystem_Item->name . ' ';
					}
				}
			}
			// Other type
			elseif ($oPropertyValue->Property->type != 2)
			{
				$oSearch_Page->text .= $oPropertyValue->value . ' ';
			}
		}

		$oSiteAlias = $this->Informationsystem->Site->getCurrentAlias();
		if ($oSiteAlias)
		{
			$oSearch_Page->url = 'http://' . $oSiteAlias->name
				. $this->Informationsystem->Structure->getPath()
				. $this->getPath();
		}

		$oSearch_Page->size = mb_strlen($oSearch_Page->text);
		$oSearch_Page->site_id = $this->Informationsystem->site_id;
		$oSearch_Page->datetime = !is_null($this->datetime) && $this->datetime != '0000-00-00 00:00:00'
			? $this->datetime
			: date('Y-m-d H:i:s');
		$oSearch_Page->module = 1;
		$oSearch_Page->module_id = $this->informationsystem_id;
		$oSearch_Page->inner = 0;
		$oSearch_Page->module_value_type = 2; // search_page_module_value_type
		$oSearch_Page->module_value_id = $this->id; // search_page_module_value_id

		Core_Event::notify($this->_modelName . '.onAfterIndexing', $this, array($oSearch_Page));

		$oSearch_Page->save();

		Core_QueryBuilder::delete('search_page_siteuser_groups')
			->where('search_page_id', '=', $oSearch_Page->id)
			->execute();

		$oSearch_Page_Siteuser_Group = Core_Entity::factory('Search_Page_Siteuser_Group');
		$oSearch_Page_Siteuser_Group->siteuser_group_id = $this->getSiteuserGroupId();
		$oSearch_Page->add($oSearch_Page_Siteuser_Group);

		return $oSearch_Page;
	}

	/**
	 * Show comments data in XML
	 * @var boolean
	 */
	protected $_showXmlComments = FALSE;

	/**
	 * Add comments XML to item
	 * @param boolean $showXmlComments mode
	 * @return self
	 */
	public function showXmlComments($showXmlComments = TRUE)
	{
		$this->_showXmlComments = $showXmlComments;
		return $this;
	}

	/**
	 * What comments show in XML? (active|inactive|all)
	 * @var string
	 */
	protected $_commentsActivity = 'active';

	/**
	 * Set comments filter rule
	 * @param string $commentsActivity (active|inactive|all)
	 * @return self
	 */
	public function commentsActivity($commentsActivity = 'active')
	{
		$this->_commentsActivity = $commentsActivity;
		return $this;
	}

	/**
	 * Show tags data in XML
	 * @var boolean
	 */
	protected $_showXmlTags = FALSE;

	/**
	 * Add tags XML to item
	 * @param boolean $showXmlTags mode
	 * @return self
	 */
	public function showXmlTags($showXmlTags = TRUE)
	{
		$this->_showXmlTags = $showXmlTags;
		return $this;
	}

	/**
	 * Show user data in XML
	 * @var boolean
	 */
	protected $_showXmlSiteuser = FALSE;

	/**
	 * Add site user XML to item
	 * @param boolean $showXmlSiteuser mode
	 * @return self
	 */
	public function showXmlSiteuser($showXmlSiteuser = TRUE)
	{
		$this->_showXmlSiteuser = $showXmlSiteuser;
		return $this;
	}

	/**
	 * Show siteuser properties in XML
	 * @var boolean
	 */
	protected $_showXmlSiteuserProperties = FALSE;

	/**
	 * Show siteuser properties in XML
	 * @param boolean $showXmlSiteuserProperties mode
	 * @return self
	 */
	public function showXmlSiteuserProperties($showXmlSiteuserProperties = TRUE)
	{
		$this->_showXmlSiteuserProperties = $showXmlSiteuserProperties;
		return $this;
	}

	/**
	 * Show properties in XML
	 * @var boolean
	 */
	protected $_showXmlProperties = FALSE;

	/**
	 * Show properties in XML
	 * @param mixed $showXmlProperties array of allowed properties ID or boolean
	 * @return Comment_Model
	 */
	public function showXmlProperties($showXmlProperties = TRUE)
	{
		$this->_showXmlProperties = is_array($showXmlProperties)
			? array_combine($showXmlProperties, $showXmlProperties)
			: $showXmlProperties;

		return $this;
	}

	/**
	 * Showing part of text in XML
	 * @var int
	 */
	protected $_showXmlPart = 1;

	/**
	 * Show part of text in XML
	 * @param int $showXmlPart
	 * @return Comment_Model
	 */
	public function showXmlPart($showXmlPart = 1)
	{
		$this->_showXmlPart = $showXmlPart;
		return $this;
	}

	/**
	 * Array of comments, [parent_id] => array(comments)
	 * @var array
	 */
	protected $_aComments = array();

	/**
	 * Set array of comments for getXml()
	 * @param array $aComments
	 * @return self
	 */
	public function setComments(array $aComments)
	{
		$this->_aComments = $aComments;
		return $this;
	}

	/**
	 * Get XML for entity and children entities
	 * @return string
	 * @hostcms-event informationsystem_item.onBeforeRedeclaredGetXml
	 */
	public function getXml()
	{
		Core_Event::notify($this->_modelName . '.onBeforeRedeclaredGetXml', $this);

		$oInformationsystem = $this->Informationsystem;

		$this->clearXmlTags()
			->addXmlTag('url', $this->Informationsystem->Structure->getPath() . $this->getPath());

		!isset($this->_forbiddenTags['date']) && $this->addXmlTag('date', strftime($oInformationsystem->format_date, Core_Date::sql2timestamp($this->datetime)));

		$this
			->addXmlTag('datetime', strftime($oInformationsystem->format_datetime, Core_Date::sql2timestamp($this->datetime)))
			->addXmlTag('start_datetime', $this->start_datetime == '0000-00-00 00:00:00'
				? $this->start_datetime
				: strftime($oInformationsystem->format_datetime, Core_Date::sql2timestamp($this->start_datetime)))
			->addXmlTag('end_datetime', $this->end_datetime == '0000-00-00 00:00:00'
				? $this->end_datetime
				: strftime($oInformationsystem->format_datetime, Core_Date::sql2timestamp($this->end_datetime)));

		!isset($this->_forbiddenTags['dir']) && $this->addXmlTag('dir', $this->getItemHref());

		// Отображается часть текста
		if ($this->_showXmlPart > 0 && !isset($this->_forbiddenTags['text']))
		{
			$aParts = explode('<!-- pagebreak -->', $this->text);
			$iPartsCount = count($aParts);

			$this->_showXmlPart > $iPartsCount && $this->_showXmlPart = $iPartsCount;

			$this->addForbiddenTag('text')
				->addXmlTag('parts_count', $iPartsCount)
				->addXmlTag('text', $aParts[$this->_showXmlPart - 1]);

			unset($aParts);
		}

		if (Core::moduleIsActive('siteuser'))
		{
			$aRate = Vote_Controller::instance()->getRateByObject($this);

			$this->addEntity(
				Core::factory('Core_Xml_Entity')
					->name('rate')
					->value($aRate['rate'])
					->addAttribute('likes', $aRate['likes'])
					->addAttribute('dislikes', $aRate['dislikes'])
			);

			if (!is_null($oCurrentSiteuser = Core_Entity::factory('Siteuser')->getCurrent()))
			{
				$oVote = $this->Votes->getBySiteuser_Id($oCurrentSiteuser->id);
				!is_null($oVote) && $this->addEntity($oVote);
			}
		}

		if ($this->_showXmlSiteuser && $this->siteuser_id && Core::moduleIsActive('siteuser'))
		{
			$this->Siteuser->showXmlProperties($this->_showXmlSiteuserProperties);
			$this->addEntity($this->Siteuser);
		}

		if ($this->_showXmlTags && Core::moduleIsActive('tag'))
		{
			$this->addEntities($this->Tags->findAll());
		}

		if ($this->_showXmlComments)
		{
			$this->_aComments = array();

			$gradeSum = 0;
			$gradeCount = 0;

			$oComments = $this->Comments;
			$oComments->queryBuilder()
				->orderBy('datetime', 'DESC');

			// учитываем заданную активность комментариев
			$this->_commentsActivity = strtolower($this->_commentsActivity);
			if ($this->_commentsActivity != 'all')
			{
				$oComments->queryBuilder()
					->where('active', '=', $this->_commentsActivity == 'inactive' ? 0 : 1);
			}

			$aComments = $oComments->findAll();
			foreach ($aComments as $oComment)
			{
				if ($oComment->grade > 0)
				{
					$gradeSum += $oComment->grade;
					$gradeCount++;
				}
				$this->_aComments[$oComment->parent_id][] = $oComment;
			}

			// Средняя оценка
			$avgGrade = $gradeCount > 0
				? $gradeSum / $gradeCount
				: 0;

			$fractionalPart = $avgGrade - floor($avgGrade);
			$avgGrade = floor($avgGrade);

			if ($fractionalPart >= 0.25 && $fractionalPart < 0.75)
			{
				$avgGrade += 0.5;
			}
			elseif ($fractionalPart >= 0.75)
			{
				$avgGrade += 1;
			}

			!isset($this->_forbiddenTags['comments_count']) && $this->addEntity(
				Core::factory('Core_Xml_Entity')
					->name('comments_count')
					->value(count($aComments))
			);

			!isset($this->_forbiddenTags['comments_grade_sum']) && $this->addEntity(
				Core::factory('Core_Xml_Entity')
					->name('comments_grade_sum')
					->value($gradeSum)
			);

			!isset($this->_forbiddenTags['comments_grade_count']) && $this->addEntity(
				Core::factory('Core_Xml_Entity')
					->name('comments_grade_count')
					->value($gradeCount)
			);

			!isset($this->_forbiddenTags['comments_average_grade']) && $this->addEntity(
				Core::factory('Core_Xml_Entity')
					->name('comments_average_grade')
					->value($avgGrade)
			);

			$this->_addComments(0, $this);
		}

		$this->_aComments = array();

		if ($this->_showXmlProperties)
		{
			if (is_array($this->_showXmlProperties))
			{
				$aProperty_Values = Property_Controller_Value::getPropertiesValues($this->_showXmlProperties, $this->id);
				foreach ($aProperty_Values as $oProperty_Value)
				{
					if ($oProperty_Value->Property->type == 2)
					{
						$oProperty_Value
							->setHref($this->getItemHref())
							->setDir($this->getItemPath());
					}

					/*isset($this->_showXmlProperties[$oProperty_Value->property_id]) && */$this->addEntity(
						$oProperty_Value
					);
				}
			}
			else
			{
				$aProperty_Values = $this->getPropertyValues();
				// Add all values
				$this->addEntities($aProperty_Values);
			}
		}
		return parent::getXml();
	}

	/**
	 * Add comments into object XML
	 * @param int $parent_id parent comment id
	 * @param Core_Entity $parentObject object
	 * @return self
	 * @hostcms-event informationsystem_item.onBeforeAddComments
	 * @hostcms-event informationsystem_item.onAfterAddComments
	 */
	protected function _addComments($parent_id, $parentObject)
	{
		Core_Event::notify($this->_modelName . '.onBeforeAddComments', $this, array(
			$parent_id, $parentObject, $this->_aComments
		));

		if (isset($this->_aComments[$parent_id]))
		{
			foreach ($this->_aComments[$parent_id] as $oComment)
			{
				$parentObject->addEntity($oComment
					->clearEntities()
					->showXmlProperties($this->_showXmlSiteuserProperties)
				);

				$this->_addComments($oComment->id, $oComment);
			}
		}

		Core_Event::notify($this->_modelName . '.onAfterAddComments', $this, array(
			$parent_id, $parentObject, $this->_aComments
		));

		return $this;
	}

	/**
	 * Create item
	 * @return self
	 */
	public function create()
	{
		$return = parent::create();

		if (!is_null($this->Informationsystem_Group->id))
		{
			// Увеличение количества элементов в группе
			$this->Informationsystem_Group->incCountItems();
		}

		return $return;
	}
}