<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Frontend data, e.g. title, description, template and data hierarchy
 *
 * <code>
 * // Get Title
 * $title = Core_Page::instance()->title;
 * </code>
 *
 * <code>
 * // Set Title
 * Core_Page::instance()->title('New title');
 * </code>
 *
 * <code>
 * // Get description
 * $description = Core_Page::instance()->description;
 * </code>
 *
 * <code>
 * // Set description
 * Core_Page::instance()->description('New description');
 * </code>
 *
 * <code>
 * // Get keywords
 * $keywords = Core_Page::instance()->keywords;
 * </code>
 *
 * <code>
 * // Set keywords
 * Core_Page::instance()->keywords('New keywords');
 * </code>
 *
 * <code>
 * // Get Template object
 * $oTemplate = Core_Page::instance()->template;
 * var_dump($oTemplate->id);
 * </code>
 *
 * <code>
 * // Get Structure object
 * $oStructure = Core_Page::instance()->structure;
 * var_dump($oStructure->id);
 * </code>
 *
 * <code>
 * // Get Core_Response object
 * $oCore_Response = Core_Page::instance()->response;
 * // Set HTTP status
 * $oCore_Response->status(404);
 * </code>
 *
 * <code>
 * // Get array of lib params
 * $array = Core_Page::instance()->libParams;
 * </code>
 *
 *
 * <code>
 * // Get controller object
 * $object = Core_Page::instance()->object;
 *
 * if (is_object(Core_Page::instance()->object)
 * && get_class(Core_Page::instance()->object) == 'Informationsystem_Controller_Show')
 * {
 *    $Informationsystem_Controller_Show = Core_Page::instance()->object;
 * }
 * </code>
 *
 * @package HostCMS 6\Core
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Core_Page extends Core_Servant_Properties
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'title',
		'description',
		'keywords',
		'template',
		'structure',
		'response',
		'libParams',
		'object',
		'buildingPage'
	);

	/**
	 * Children entities
	 * @var array
	 */
	protected $_children = array();

	/**
	 * Add child to an hierarchy
	 * @param object $object object
	 * @return Core_Page
	 */
	public function addChild($object)
	{
		array_unshift($this->_children, $object);
		return $this;
	}

	/*public function addLastChild($object)
	{
		$this->_children[] = $object;
		return $this;
	}*/

	/**
	 * Delete first child
	 * @return Core_Page
	 */
	public function deleteChild()
	{
		array_shift($this->_children);
		return $this;
	}

	/**
	 * Executes the business logic.
	 */
	public function execute()
	{
		if (count($this->_children))
		{
			$object = array_shift($this->_children);
			return $object->execute();
		}

		return $this;
	}

	/**
	 * Get children
	 * @return array
	 */
	public function getChildren()
	{
		return $this->_children;
	}
	
	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->libParams = array();
		$this->buildingPage = FALSE;
	}

	/**
	 * The singleton instances.
	 * @var mixed
	 */
	static public $instance = NULL;

	/**
	 * Register an existing instance as a singleton.
	 * @return object
	 */
	static public function instance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Linking css
	 * @var array
	 */
	public $css = array();

	/**
	 * Link css
	 * @param string $css path
	 * @return Core_Page
	 */
	public function css($css)
	{
		$this->css[] = $css;
		return $this;
	}

	/**
	 * Get block of linked css
	 * @param boolean $bExternal add as link
	 * @return string
	 */
	public function getCss($bExternal = TRUE)
	{
		$sReturn = '';

		$aTmp = array_reverse($this->css);

		foreach ($aTmp as $css)
		{
			if ($bExternal)
			{
				$sReturn .= '<link rel="stylesheet" type="text/css" href="' . $css . '?' . Core_Date::sql2timestamp($this->template->timestamp) . '" />' . "\n";
			}
			else
			{
				$sPath = CMS_FOLDER . ltrim($css, DIRECTORY_SEPARATOR);
				$sReturn .= "<style type=\"text/css\">\n";
				is_file($sPath) && $sReturn .= Core_File::read($sPath);
				$sReturn .= "\n</style>\n";
			}
		}

		return $sReturn;
	}

	/**
	 * Show block of linked css
	 * @param boolean $bExternal add as link
	 * @return Core_Page
	 */
	public function showCss($bExternal = TRUE)
	{
		echo $this->getCss($bExternal);
		return $this;
	}

	/**
	 * Show page title
	 * @return Core_Page
	 */
	public function showTitle()
	{
		echo str_replace('&amp;', '&', htmlspecialchars($this->title));
		return $this;
	}

	/**
	 * Show page description
	 * @return Core_Page
	 */
	public function showDescription()
	{
		echo htmlspecialchars($this->description);
		return $this;
	}

	/**
	 * Show page keywords
	 * @return Core_Page
	 */
	public function showKeywords()
	{
		echo htmlspecialchars($this->keywords);
		return $this;
	}

	/**
	 * Add templates
	 * @param Template_Model $oTemplate Template
	 * @return Core_Page
	 */
	public function addTemplates(Template_Model $oTemplate)
	{
		do {
			$this
				->css($oTemplate->getTemplateCssFileHref())
				->addChild($oTemplate);

		} while($oTemplate = $oTemplate->getParent());

		return $this;
	}
}