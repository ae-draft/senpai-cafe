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
class Skin_Default_Admin_Form_Entity_Select extends Admin_Form_Entity
{
	/**
	 * Skip properties
	 * @var array
	 */
	protected $_skipProperies = array(
		'divAttr', // array
		'options', // array
		'caption',
		'value', // идет в selected
		'format', // array, массив условий форматирования
		'filter',
		'invertor',
		'invertor_id',
		'invertorCaption', 
		'inverted'
	);

	/**
	 * Counter of Admin_Form_Entity_Select used in the form
	 * @var int
	 */
	static $iFilterCount = 0;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		// Combine
		$this->_skipProperies = array_combine($this->_skipProperies, $this->_skipProperies);

		$oCore_Html_Entity_Select = new Core_Html_Entity_Select();
		$this->_allowedProperties += $oCore_Html_Entity_Select->getAllowedProperties();

		// Свойства, исключаемые для <select>, добавляем в список разрешенных объекта
		$this->_allowedProperties += $this->_skipProperies;

		parent::__construct();

		$oCore_Registry = Core_Registry::instance();
		$iAdmin_Form_Count = $oCore_Registry->get('Admin_Form_Count', 0);
		$oCore_Registry->set('Admin_Form_Count', $iAdmin_Form_Count + 2);

		$this->id = $this->name = 'field_id_' . $iAdmin_Form_Count;
	
		$iAdmin_Form_Count++;
		$this->invertor_id = 'field_id_' . $iAdmin_Form_Count;
	}

	/**
	 * Executes the business logic.
	 */
	public function execute()
	{
		if (is_null($this->size) && is_null($this->style))
		{
			$this->style('width: 100%');
		}

		/*$windowId = $this->_Admin_Form_Controller->getWindowId();
		if (is_null($this->onkeydown))
		{
			$this->onkeydown = $this->onkeyup = $this->onblur = "FieldCheck('{$windowId}', this)";
		}*/

		$aDefaultDivAttr = array('class' => 'item_div');
		$this->divAttr = Core_Array::union($this->divAttr, $aDefaultDivAttr);

		$aAttr = $this->getAttrsString();

		// Установим атрибуты div'a.
		$aDivAttr = array();
		if (is_array($this->divAttr))
		{
			foreach ($this->divAttr as $attrName => $attrValue)
			{
				$aDivAttr[] = "{$attrName}=\"" . htmlspecialchars($attrValue) . "\"";
			}
		}

		?><div <?php echo implode(' ', $aDivAttr)?>><?php

		?><span class="caption"><?php echo $this->caption?></span><?php

		if($this->invertor)
		{
			$oCore_Html_Entity_Input = Core::factory('Core_Html_Entity_Input')
				->type("checkbox")
				->id($this->invertor_id)
				->name($this->name . '_inverted')
				->value(1);

			$this->inverted && $oCore_Html_Entity_Input->checked(true);

			$oCore_Html_Entity_Input->execute();

			Core::factory('Core_Html_Entity_Span')
				->class('caption')
				->style('display:inline')
				->value($this->invertorCaption . '&nbsp;')
				->execute();
		}
		?><select <?php echo implode(' ', $aAttr) ?>><?php
		if (is_array($this->options))
		{
			foreach ($this->options as $key => $xValue)
			{
				$sAttr = '';

				if (is_array($xValue))
				{
					$value = Core_Array::get($xValue, 'value');
					$attr = Core_Array::get($xValue, 'attr', array());

					!empty($attr) && $sAttr = ' ';
					foreach($attr as $attrKey => $attrValue)
					{
						$sAttr .= Core_Str::xml($attrKey) . '="' . Core_Str::xml($attrValue) . '"';
					}
				}
				else
				{
					$value = $xValue;
				}

				?><option value="<?php echo htmlspecialchars($key)?>"<?php echo ($this->value == $key) ? ' selected="selected"' : ''?><?php echo $sAttr?>><?php
				?><?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8')?><?php
				?></option><?php
			}
		}
		?></select><?php

		if ($this->filter)
		{
			$windowId = $this->_Admin_Form_Controller->getWindowId();
			$iFilterCount = self::$iFilterCount;

			Core::factory('Core_Html_Entity_Div')
				->style("float: left; opacity: 0.7")
				->add(
					Core::factory('Core_Html_Entity_Img')
						->src('/admin/images/filter.gif')
						->class('img_line')
						->style('margin-left: 10px')
				)
				->add(
					Core::factory('Core_Html_Entity_Input')
						->size(15)
						->id("filer_{$this->id}")
						->onkeyup("clearTimeout(oSelectFilter{$iFilterCount}.timeout); oSelectFilter{$iFilterCount}.timeout = setTimeout(function(){oSelectFilter{$iFilterCount}.Set(document.getElementById('filer_{$this->id}').value); oSelectFilter{$iFilterCount}.Filter();}, 500)")
						->onkeypress("if (event.keyCode == 13) return false;")
				)
				->add(
					Core::factory('Core_Html_Entity_Input')
						->type("button")
						->onclick("this.form.filer_{$this->id}.value = '';oSelectFilter{$iFilterCount}.Set('');oSelectFilter{$iFilterCount}.Filter();")
						->value(Core::_('Admin_Form.input_clear_filter'))
						->class('saveButton')
				)
				->add(
					Core::factory('Core_Html_Entity_Input')
						->id("IgnoreCase_{$this->id}")
						->type("checkbox")
						->onclick("oSelectFilter{$iFilterCount}.SetIgnoreCase(!this.checked);oSelectFilter{$iFilterCount}.Filter()")
				)
				->add(
					Core::factory('Core_Html_Entity_Label')
						->for("IgnoreCase_{$this->id}")
						->value(Core::_('Admin_Form.input_case_sensitive'))
				)
				->add(
					Core::factory('Core_Html_Entity_Script')
						->type("text/javascript")
						->value("var oSelectFilter{$iFilterCount} = new cSelectFilter('{$windowId}', '{$this->id}');")
				)
				->execute();

			Admin_Form_Entity::factory('Separator')
				->execute();

			self::$iFilterCount++;
		}

		parent::execute();
		?></div><?php
	}
}