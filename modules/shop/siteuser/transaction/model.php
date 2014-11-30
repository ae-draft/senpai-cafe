<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Online shop.
 *
 * @package HostCMS 6\Shop
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Shop_Siteuser_Transaction_Model extends Core_Entity
{
	/**
	 * Backend property
	 * @var string
	 */
	public $currency_name = NULL;

	/**
	 * One-to-many or many-to-many relations
	 * @var array
	 */
	protected $_hasMany = array(
		'shop_group' => array('foreign_key' => 'parent_id')
	);

	/**
	 * Belongs to relations
	 * @var array
	 */
	protected $_belongsTo = array(
		'shop' => array(),
		'siteuser' => array(),
		'shop_currency' => array(),
		'shop_order' => array(),
		'user' => array()
	);

	/**
	 * Disable markDeleted()
	 * @var mixed
	 */
	protected $_marksDeleted = NULL;

	/**
	 * List of preloaded values
	 * @var array
	 */
	protected $_preloadValues = array(
		'amount' => 0.00,
		'amount_base_currency' => 0.00,
		'active' => 1,
		'shop_order_id' => 0
	);

	/**
	 * Forbidden tags. If list of tags is empty, all tags will show.
	 * @var array
	 */
	protected $_forbiddenTags = array(
		'datetime'
	);

	/**
	 * Default sorting for models
	 * @var array
	 */
	protected $_sorting = array(
		'shop_siteuser_transactions.datetime' => 'DESC'
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
		}
	}

	/**
	 * Get transactions by shop ID
	 * @param int $shop_id shop ID
	 * @return array
	 */
	public function getByShop($shop_id)
	{
		$this
			->queryBuilder()
			//->clear()
			->where('shop_id', '=', $shop_id);

		return $this->findAll();
	}

	/**
	 * Change transaction status
	 * @return Shop_Siteuser_Transaction_Model
	 */
	public function changeActive()
	{
		$this->active = 1 - $this->active;
		return $this->save();
	}

	/**
	 * Get XML for entity and children entities
	 * @return string
	 * @hostcms-event shop_siteuser_transaction.onBeforeRedeclaredGetXml
	 */
	public function getXml()
	{
		Core_Event::notify($this->_modelName . '.onBeforeRedeclaredGetXml', $this);

		$this->clearXmlTags()
			->addXmlTag('date', strftime($this->Shop->format_date, Core_Date::sql2timestamp($this->datetime)))
			->addXmlTag('datetime', strftime($this->Shop->format_datetime, Core_Date::sql2timestamp($this->datetime)));

		$this->shop_currency_id && $this->addEntity($this->Shop_Currency);
		$this->shop_order_id && $this->addEntity($this->Shop_Order);

		return parent::getXml();
	}
}