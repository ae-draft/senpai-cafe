<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2013 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Skin_Default_Admin_Form_Entity_Link extends Admin_Form_Entity
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'divAttr', // array
		'a',
		'img',
		'div'
	);

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->a = Core::factory('Core_Html_Entity_A')
			->target('_blank');
		$this->img = Core::factory('Core_Html_Entity_Img');
		$this->div = Core::factory('Core_Html_Entity_Div')
			->style("width: 250px");
	}

	/**
	 * Executes the business logic.
	 */
	public function execute()
	{
		$aDefaultDivAttr = array('class' => 'large item_div item_div_as_is');

		$this->divAttr = Core_Array::union($this->divAttr, $aDefaultDivAttr);

		// Установим атрибуты div'a.
		$aDivAttr = array();
		if (is_array($this->divAttr))
		{
			foreach ($this->divAttr as $attrName => $attrValue)
			{
				$this->div->$attrName = $attrValue;
			}
		}

		$this->div
			->add($this->img)
			->add($this->a)
			->execute();
	}
}