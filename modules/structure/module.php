<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Structure.
 *
 * @package HostCMS 6\Structure
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Structure_Module extends Core_Module{	/**
	 * Module version
	 * @var string
	 */
	public $version = '6.1';

	/**
	 * Module date
	 * @var date
	 */
	public $date = '2014-08-22';
	/**
	 * Constructor.
	 */	public function __construct()	{
		parent::__construct();
		$this->menu = array(			array(				'sorting' => 10,				'block' => 0,
				'ico' => 'fa-sitemap',				'name' => Core::_('Structure.menu'),				'href' => "/admin/structure/index.php",				'onclick' => "$.adminLoad({path: '/admin/structure/index.php'}); return false"			)		);	}

	/**
	 * Индексация структуры сайта
	 *
	 * @param $offset
	 * @param $limit
	 * @return array
	 * @hostcms-event Structure_Module.indexing
	 */
	public function indexing($offset, $limit)
	{
		$offset = intval($offset);
		$limit = intval($limit);

		$oStructure = Core_Entity::factory('Structure');

		$oStructure
			->queryBuilder()
			->join('sites', 'structures.site_id', '=', 'sites.id')
			->where('structures.active', '=', 1)
			->where('structures.indexing', '=', 1)
			->where('structures.path', '!=', '')
			->where('structures.url', '=', '')
			->where('sites.deleted', '=', 0)
			->limit($offset, $limit);

		Core_Event::notify(get_class($this) . '.indexing', $this, array($oStructure));

		$aStructures = $oStructure->findAll();

		$result = array();
		foreach ($aStructures as $oStructure)
		{
			$result[] = $oStructure->indexing();
		}

		return $result;
	}

	/**
	 * Search callback function
	 * @param Search_Page_Model $oSearch_Page
	 * @return self
	 * @hostcms-event Structure_Module.searchCallback
	 */
	public function searchCallback($oSearch_Page)
	{
		if ($oSearch_Page->module_value_id)
		{
			$oStructure = Core_Entity::factory('Structure')->find($oSearch_Page->module_value_id);

			Core_Event::notify(get_class($this) . '.searchCallback', $this, array($oSearch_Page, $oStructure));

			!is_null($oStructure->id) && $oSearch_Page->addEntity($oStructure);
		}

		return $this;
	}
}