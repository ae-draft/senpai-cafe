<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Information systems.
 *
 * @package HostCMS 6\Informationsystem
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Informationsystem_Group_Model extends Core_Entity
{
	/**
	 * Model name
	 * @var mixed
	 */
	protected $_modelName = 'informationsystem_group';

	/**
	 * Backend property
	 * @var mixed
	 */
	public $img = 0;

	/**
	 * Backend property
	 * @var mixed
	 */
	public $comment_field = NULL;

	/**
	 * Backend property
	 * @var mixed
	 */
	public $datetime = NULL;

	/**
	 * Backend property
	 * @var mixed
	 */
	public $adminComment = NULL;

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'informationsystem_item' => array(),
		'informationsystem_group' => array('foreign_key' => 'parent_id')
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'informationsystem_group' => array('foreign_key' => 'parent_id'),
		'informationsystem' => array(),
		'siteuser' => array(),
		'siteuser_group' => array(),
		'user' => array()
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
		}
	}

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'sorting' => 0,
		'indexing' => 1,
		'siteuser_id' => 0,
		'active' => 1,
		'image_large' => '',
		'image_small' => ''
	);

	/**
	 * Values of all properties of group
	 * @var array
	 */
	protected $_propertyValues = NULL;

	/**
	 * Values of all properties of element
	 * @param boolean $bCache cache mode
	 * @return array Property_Value
	 */
	public function getPropertyValues($bCache = TRUE)
	{
		if ($bCache && !is_null($this->_propertyValues))
		{
			return $this->_propertyValues;
		}

		// Warning: Need cache
		$aProperties = Core_Entity::factory('Informationsystem_Group_Property_List', $this->informationsystem_id)
			->Properties
			->findAll();

		$aReturn = array();
		$aProperiesId = array();
		foreach ($aProperties as $oProperty)
		{
			$aProperiesId[] = $oProperty->id;

			/*$aProperty_Values = $oProperty->getValues($this->id, $bCache);

			foreach ($aProperty_Values as $oProperty_Value)
			{
				if ($oProperty->type == 2)
				{
					$oProperty_Value->setHref($this->getGroupHref());
				}

				$aReturn[] = $oProperty_Value;
			}*/
		}

		$aReturn = Property_Controller_Value::getPropertiesValues($aProperiesId, $this->id, $bCache);

		// setHref()
		foreach ($aReturn as $oProperty_Value)
		{
			if ($oProperty_Value->Property->type == 2)
			{
				$oProperty_Value
					->setHref($this->getGroupHref())
					->setDir($this->getGroupPath());
			}
		}

		if ($bCache)
		{
			$this->_propertyValues = $aReturn;
		}

		return $aReturn;
	}

	/**
	 * Check and correct duplicate path
	 * @return self
	 */
	public function checkDuplicatePath()
	{
		$oInformationsystem = $this->InformationSystem;

		// Search the same item or group
		$oSameInformationsystemGroup = $oInformationsystem->Informationsystem_Groups->getByParentIdAndPath($this->parent_id, $this->path);
		if (!is_null($oSameInformationsystemGroup) && $oSameInformationsystemGroup->id != $this->id)
		{
			$this->path = Core_Guid::get();
		}

		$oSameInformationsystemItem = $oInformationsystem->Informationsystem_Items->getByGroupIdAndPath($this->parent_id, $this->path);
		if (!is_null($oSameInformationsystemItem))
		{
			$this->path = Core_Guid::get();
		}

		return $this;
	}

	/**
	 * Make url path
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
	 * @return Core_Entity
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

		$aInformationsystem_Items = $this->Informationsystem_Items->deleteAll(FALSE);
		$aInformationsystem_Groups = $this->Informationsystem_Groups->deleteAll(FALSE);

		// Удаляем значения доп. свойств
		$aPropertyValues = $this->getPropertyValues();
		foreach($aPropertyValues as $oPropertyValue)
		{
			$oPropertyValue->Property->type == 2 && $oPropertyValue->setDir($this->getGroupPath());
			$oPropertyValue->delete();
		}

		// Удаляем директорию информационной группы
		$this->deleteDir();

		return parent::delete($primaryKey);
	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		$newObject = parent::copy();

		$this->_changeCopiedName && $newObject->path(Core_Guid::get())->save();

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

		$aChildrenGroups = $this->Informationsystem_Groups->findAll();
		foreach($aChildrenGroups as $oChildrenGroup)
		{
			$oChild = $oChildrenGroup->copy();
			$oChild->parent_id = $newObject->id;
			$oChild->save();
		}

		$aInformationsystem_Items = $this->Informationsystem_Items->findAll();
		foreach($aInformationsystem_Items as $oInformationsystem_Item)
		{
			$newObject->add($oInformationsystem_Item->copy());
			// Recount for current group
			$this->decCountItems();
		}

		$aPropertyValues = $this->getPropertyValues();
		foreach($aPropertyValues as $oPropertyValue)
		{
			$oNewPropertyValue = clone $oPropertyValue;
			$oNewPropertyValue->entity_id = $newObject->id;
			$oNewPropertyValue->save();

			if ($oNewPropertyValue->Property->type == 2)
			{
				$oPropertyValue->setDir($this->getGroupPath());
				$oNewPropertyValue->setDir($newObject->getGroupPath());

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

		return $newObject;
	}

	/**
	 * Get parent group
	 * @return Informationsystem_Group_Model|NULL
	 */
	public function getParent()
	{
		return $this->parent_id
			? Core_Entity::factory('Informationsystem_Group', $this->parent_id)
			: NULL;
	}

	/**
	 * Get group by parent id
	 * @param int $parent_id parent id
	 * @param boolean $bCache cache mode
	 * @return array
	 */
	public function getByParentId($parent_id, $bCache = TRUE)
	{
		$this->queryBuilder()
			//->clear()
			->where('parent_id', '=', $parent_id);

		return $this->findAll($bCache);
	}

	/**
	 * Get group by parent id and path
	 * @param int $parent_id parent id
	 * @param string $path path
	 * @return Informationsystem_Group|NULL
	 */
	public function getByParentIdAndPath($parent_id, $path)
	{
		$this->queryBuilder()
			//->clear()
			->where('path', '=', $path)
			->where('parent_id', '=', $parent_id)
			->limit(1);

		$aInformationsystem_Groups = $this->findAll();

		if (isset($aInformationsystem_Groups[0]))
		{
			return $aInformationsystem_Groups[0];
		}

		return NULL;
	}

	/**
	 * Get group path
	 * @return string
	 */
	public function getGroupPath()
	{
		return $this->Informationsystem->getPath() . '/' . Core_File::getNestingDirPath($this->id, $this->Informationsystem->Site->nesting_level) . '/group_' . $this->id . '/';
	}

	/**
	 * Get group href
	 * @return string
	 */
	public function getGroupHref()
	{
		return '/' . $this->Informationsystem->getHref() . '/' . Core_File::getNestingDirPath($this->id, $this->Informationsystem->Site->nesting_level) . '/group_' . $this->id . '/';
	}

	/**
	 * Get small file path
	 * @return string
	 */
	public function getSmallFilePath()
	{
		return $this->getGroupPath() . $this->image_small;
	}

	/**
	 * Get small file href
	 * @return string
	 */
	public function getSmallFileHref()
	{
		return $this->getGroupHref() . rawurlencode($this->image_small);
	}

	/**
	 * Get large file path
	 * @return string
	 */
	public function getLargeFilePath()
	{
		return $this->getGroupPath() . $this->image_large;
	}

	/**
	 * Get large file href
	 * @return string
	 */
	public function getLargeFileHref()
	{
		return $this->getGroupHref() . rawurlencode($this->image_large);
	}

	/**
	 * Specify large image for group
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
		Core_File::upload($fileSourcePath, $this->getGroupPath() . $fileName);
		return $this;
	}

	/**
	 * Specify small image for group
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
		Core_File::upload($fileSourcePath, $this->getGroupPath() . $fileName);
		return $this;
	}

	/**
	 * Move group to another group
	 * @param int $parent_id group id
	 * @return self
	 */
	public function move($parent_id)
	{
		$this->parent_id = $parent_id;
		return $this->save();
	}

	/**
	 * Create group directory
	 * @return self
	 */
	public function createDir()
	{
		if (!is_dir($this->getGroupPath()))
		{
			try
			{
				Core_File::mkdir($this->getGroupPath(), CHMOD, TRUE);
			} catch (Exception $e) {}
		}
		return $this;
	}

	/**
	 * Delete group directory
	 * @return self
	 */
	public function deleteDir()
	{
		// Удаляем файл большого изображения группы
		$this->deleteLargeImage();

		// Удаляем файл малого изображения группы
		$this->deleteSmallImage();

		if (is_dir($this->getGroupPath()))
		{
			try
			{
				Core_File::deleteDir($this->getGroupPath());
			} catch (Exception $e) {}
		}
		return $this;
	}

	/**
	 * Delete group's large image
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
	 * Delete group's small image
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
	 * Change group status
	 * @return Informationsystem_Group_Model
	 */
	public function changeActive()
	{
		$this->active = 1 - $this->active;
		return $this->save();
	}

	/**
	 * Switch indexation mode
	 * @return self
	 */
	public function changeIndexation()
	{
		$this->indexing = 1 - $this->indexing;
		return $this->save();
	}

	/**
	 * Get group path
	 * @return string
	 */
	public function getPath()
	{
		$sPath = $this->path . '/';

		if (!is_null($oParentGroup = $this->getParent()))
		{
			$sPath = $oParentGroup->getPath() . $sPath;
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
			$result = $this->parent_id
				? $this->Informationsystem_Group->getSiteuserGroupId()
				: $this->Informationsystem->siteuser_group_id;
		}
		else
		{
			$result = $this->siteuser_group_id;
		}

		return intval($result);
	}

	/**
	 * Увеличение на 1 количества элементов в группе и во всех родительских группах
	 */
	public function incCountItems()
	{
		return $this->modifyCountItems(1);
	}
	/**
	 * Уменьшение на 1 количества элементов в группе и во всех родительских группах
	 */
	public function decCountItems()
	{
		return $this->modifyCountItems(-1);
	}

	/**
	 * Modify count of items in group
	 * @param int $int value
	 * @return self
	 */
	public function modifyCountItems($int = 1)
	{
		$this->items_count += $int;
		$this->items_total_count += $int;
		$this->save();

		if ($this->parent_id != 0 /*&& $this->parent_id != $this->id*/ && !is_null($this->Informationsystem_Group->id))
		{
			$this->Informationsystem_Group->modifyCountItems($int);
		}
		return $this;
	}

	/**
	 * Увеличение на 1 количества подгрупп в группе и во всех родительских группах
	 */
	public function incCountGroups()
	{
		return $this->modifyCountGroups(1);
	}

	/**
	 * Уменьшение на 1 количества подгрупп в группе и во всех родительских группах
	 */
	public function decCountGroups()
	{
		return $this->modifyCountGroups(-1);
	}

	/**
	 * Update subgroups count
	 * @param int $int group count
	 * @return self
	 */
	public function modifyCountGroups($int = 1)
	{
		$this->subgroups_count += $int;
		$this->subgroups_total_count += $int;
		$this->save();

		if ($this->parent_id != 0)
		{
			$this->Informationsystem_Group->modifyCountGroups($int);
		}
		return $this;
	}

	/**
	 * Create group
	 * @return Core_ORM
	 */
	public function create()
	{
		$return = parent::create();

		if ($this->parent_id != 0)
		{
			// Увеличение количества элементов в группе
			$this->Informationsystem_Group->incCountGroups();
		}
		return $return;
	}

	/**
	 * Backend callback method
	 * @param Admin_Form_Field $oAdmin_Form_Field
	 * @param Admin_Form_Controller $oAdmin_Form_Controller
	 * @return string
	 */
	public function name($oAdmin_Form_Field, $oAdmin_Form_Controller)
	{
		$link = $oAdmin_Form_Field->link;
		$onclick = $oAdmin_Form_Field->onclick;

		$link = $oAdmin_Form_Controller->doReplaces($oAdmin_Form_Field, $this, $link);
		$onclick = $oAdmin_Form_Controller->doReplaces($oAdmin_Form_Field, $this, $onclick);

		$oCore_Html_Entity_Div = Core::factory('Core_Html_Entity_Div');

		if ($this->active == 0)
		{
			$oCore_Html_Entity_Div
				->style("text-decoration: line-through");
		}

		$oCore_Html_Entity_Div
			->add(
				Core::factory('Core_Html_Entity_A')
					->href($link)
					->onclick($onclick)
					->value(htmlspecialchars($this->name))
			);

		if ($this->active == 1)
		{
			$oCurrentAlias = $this->Informationsystem->Site->getCurrentAlias();

			if ($oCurrentAlias)
			{
				$href = 'http://' . $oCurrentAlias->name
					. $this->Informationsystem->Structure->getPath()
					. $this->getPath();

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

		$this->items_total_count > 0 && $oCore_Html_Entity_Div
			->add(
				Core::factory('Core_Html_Entity_Span')
					->class('count')
					->value(htmlspecialchars($this->items_total_count))
			);

		$oCore_Html_Entity_Div->execute();
	}

	/**
	 * Search indexation
	 * @return Search_Page
	 * @hostcms-event informationsystem_group.onBeforeIndexing
	 * @hostcms-event informationsystem_group.onAfterIndexing
	 */
	public function indexing()
	{
		$oSearch_Page = Core_Entity::factory('Search_Page');

		Core_Event::notify($this->_modelName . '.onBeforeIndexing', $this, array($oSearch_Page));

		$oSearch_Page->text = $this->name . ' ' . $this->description . ' ' . $this->id . ' ' . $this->seo_title . ' ' . $this->seo_description . ' ' . $this->seo_keywords . ' ' . $this->path;

		$oSearch_Page->title = $this->name;

		$aPropertyValues = $this->getPropertyValues(FALSE);
		foreach ($aPropertyValues as $oPropertyValue)
		{
			// List
			if ($oPropertyValue->Property->type == 3 && Core::moduleIsActive('list'))
			{
				if ($oPropertyValue->value != 0)
				{
					$oList_Item = $oPropertyValue->List_Item;
					$oList_Item->id && $oSearch_Page->text .= $oList_Item->value;
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
						$oSearch_Page->text .= $oInformationsystem_Item->name;
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
		$oSearch_Page->datetime = date('Y-m-d H:i:s');
		$oSearch_Page->module = 1;
		$oSearch_Page->module_id = $this->informationsystem_id;
		$oSearch_Page->inner = 0;
		$oSearch_Page->module_value_type = 1; // search_page_module_value_type
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
	 * Get XML for entity and children entities
	 * @return string
	 * @hostcms-event informationsystem_group.onBeforeRedeclaredGetXml
	 */
	public function getXml()
	{
		Core_Event::notify($this->_modelName . '.onBeforeRedeclaredGetXml', $this);

		$this->clearXmlTags()
			->addXmlTag('url', $this->Informationsystem->Structure->getPath() . $this->getPath())
			->addXmlTag('dir', $this->getGroupHref());

		if ($this->_showXmlProperties)
		{
			if (is_array($this->_showXmlProperties))
			{
				$aProperty_Values = Property_Controller_Value::getPropertiesValues($this->_showXmlProperties, $this->id);
				foreach ($aProperty_Values as $oProperty_Value)
				{
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
}