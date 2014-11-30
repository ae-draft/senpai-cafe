<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Templates.
 *
 * @package HostCMS 6\Template
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Template_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var int
	 */
	public $img = 1;

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'template_dir' => array(),
		'template' => array(),
		'site' => array()
	);

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'template' => array()
	);

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'sorting' => 0,
		'data_template_id' => 0
	);

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'templates.sorting' => 'ASC'
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
	 * Executes the business logic.
	 * @hostcms-event template.onBeforeExecute
	 * @hostcms-event template.onAfterExecute
	 */
	public function execute()
	{
		// Совместимость с HostCMS 5
		if (defined('USE_HOSTCMS_5') && USE_HOSTCMS_5)
		{
			$kernel = & singleton('kernel');
		}

		Core_Event::notify($this->_modelName . '.onBeforeExecute', $this);

		include $this->getTemplateFilePath();

		Core_Event::notify($this->_modelName . '.onAfterExecute', $this);

		return $this;
	}

	/**
	 * Get all site templates
	 * @param int $site_id site ID
	 * @return array
	 */
	public function getBySiteId($site_id)
	{
		$this->queryBuilder()
			//->clear()
			->where('site_id', '=', $site_id)
			->orderBy('name');

		return $this->findAll();
	}

	/**
	 * Get template file path
	 * @return string
	 */
	public function getTemplateFilePath()
	{
		return CMS_FOLDER . $this->_getDir() . '/template.htm';
	}

	/**
	 * Specify template content
	 * @param string $content content
	 * @return self
	 */
	public function saveTemplateFile($content)
	{
		$this->save();
		$this->_createDir();
		$content = trim($content);
		Core_File::write($this->getTemplateFilePath(), $content);
		return $this;
	}

	/**
	 * Save object.
	 *
	 * @return Core_Entity
	 */
	public function save()
	{
		$this->timestamp = Core_Date::timestamp2sql(time());
		return parent::save();
	}

	/**
	 * Get template
	 * @return string|NULL
	 */
	public function loadTemplateFile()
	{
		$path = $this->getTemplateFilePath();

		if (is_file($path))
		{
			return Core_File::read($path);
		}

		return NULL;
	}

	/**
	 * Get directory for template
	 * @return string
	 */
	protected function _getDir()
	{
		return 'templates/template' . intval($this->id);
	}

	/**
	 * Get href to template's CSS file
	 * @return string
	 */
	public function getTemplateCssFileHref()
	{
		return '/' . $this->_getDir() . '/style.css';
	}

	/**
	 * Get path to template's CSS file
	 * @return string
	 */
	public function getTemplateCssFilePath()
	{
		return CMS_FOLDER . $this->_getDir() . '/style.css';
	}

	/**
	 * Specify CSS for template
	 * @param string $content CSS
	 */
	public function saveTemplateCssFile($content)
	{
		$this->save();
		$this->_createDir();
		$content = trim($content);
		Core_File::write($this->getTemplateCssFilePath(), $content);
	}

	/**
	 * Get CSS for template
	 * @return string|NULL
	 */
	public function loadTemplateCssFile()
	{
		$path = $this->getTemplateCssFilePath();

		if (is_file($path))
		{
			return Core_File::read($path);
		}

		return NULL;
	}

	/**
	 * Create directory for template
	 * @return self
	 */
	protected function _createDir()
	{
		$sDirPath = dirname($this->getTemplateFilePath());

		if (!is_dir($sDirPath))
		{
			try
			{
				Core_File::mkdir($sDirPath, CHMOD, TRUE);
			} catch (Exception $e) {}
		}

		return $this;
	}

	/**
	 * Get parent comment
	 * @return Template_Model|NULL
	 */
	public function getParent()
	{
		if ($this->template_id)
		{
			return Core_Entity::factory('Template', $this->template_id);
		}
		return NULL;
	}

	/**
	 * Used when transferring templates for layouts
	 * Используется при переносе шаблонов к макетам
	 * @param int $data_template_id template ID
	 * @return Template_Model|NULL
	 */
	public function getByDataTemplateId($data_template_id)
	{
		$oTemplates = $this->Templates;
		$oTemplates->queryBuilder()
			//->clear()
			->where('data_template_id', '=', $data_template_id)
			->limit(1);

		$aTemplates = $oTemplates->findAll(FALSE);

		return isset($aTemplates[0])
			? $aTemplates[0]
			: NULL;
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

		// Удаляем файл макета
		try
		{
			$path = $this->getTemplateFilePath();
			is_file($path) && Core_File::delete($path);
		}
		catch (Exception $e) {}

		try
		{
			$path = $this->getTemplateCssFilePath();
			is_file($path) && Core_File::delete($path);
		}
		catch (Exception $e) {}

		try
		{
			is_dir(CMS_FOLDER . $this->_getDir()) && Core_File::deleteDir(CMS_FOLDER . $this->_getDir());
		}
		catch (Exception $e) {}

		$aTemplates = $this->Templates->findAll(FALSE);
		foreach ($aTemplates as $oTemplate)
		{
			$oTemplate->delete();
		}

		return parent::delete($primaryKey);
	}

	/**
	 * Copy object
	 * @return Core_Entity
	 */
	public function copy()
	{
		$newObject = parent::copy();

		$newObject->saveTemplateCssFile($this->loadTemplateCssFile());
		$newObject->saveTemplateFile($this->loadTemplateFile());

		$aTemplates = $this->Templates->findAll();

		foreach ($aTemplates as $oTemplate)
		{
			$subTemplate = $oTemplate->copy();
			$subTemplate->template_id = $newObject->id;
			$subTemplate->save();
			//$newObject->add();
		}

		return $newObject;
	}
}