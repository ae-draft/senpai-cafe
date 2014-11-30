<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Structure.
 * Типовой контроллер загрузки свойст типовой дин. страницы для структуры
 *
 * @package HostCMS 6\Structure
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Structure_Controller_Libproperties extends Admin_Form_Action_Controller
{
	/**
	 * Lib ID
	 * @var int
	 */
	protected $_libId = NULL;

	/**
	 * Set lib ID
	 * @param int $libId
	 * @return self
	 */
	public function libId($libId)
	{
		$this->_libId = $libId;
		return $this;
	}

	/**
	 * Constructor.
	 * @param Admin_Form_Action_Model $oAdmin_Form_Action action
	 */
	public function __construct(Admin_Form_Action_Model $oAdmin_Form_Action)
	{
		parent::__construct($oAdmin_Form_Action);
	}

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation name
	 */
	public function execute($operation = NULL)
	{
		if (is_null($this->_libId))
		{
			throw new Core_Exception('libId is NULL.');
		}

		ob_start();

		$oLib = Core_Entity::factory('Lib')->find($this->_libId);

		if (is_null($oLib->id))
		{
			Core_Message::show(
				Core::_('Structure.lib_contains_no_parameters')
			);
		}
		else
		{
			if ($this->_object->id)
			{
				// Загружаем значения параметров
				$LA = $oLib->getDat($this->_object->id);

				if (is_array($LA) && count($LA) > 0)
				{
					// Булевы значения приводим к текстовым
					foreach ($LA as $key => $current_value)
					{
						if ($current_value === FALSE)
						{
							$LA[$key] = 'false';
						}
						elseif ($current_value === TRUE)
						{
							$LA[$key] = 'true';
						}
					}
				}
			}
			else
			{
				$LA = array();
			}

			ob_start();

			$windowId = $this->_Admin_Form_Controller->getWindowId();

			$oTable = Core::factory('Core_Html_Entity_Table');

			$aLib_Properties = $oLib->Lib_Properties->findAll();

			$oXsl_Controller_Edit = new Xsl_Controller_Edit($this->_Admin_Form_Action);
			$aXslDirs = $oXsl_Controller_Edit->fillXslDir(0);

			foreach ($aLib_Properties as $oLib_Property)
			{
				// Получаем значение параметра
				$value = isset($LA[$oLib_Property->varible_name])
					? $LA[$oLib_Property->varible_name]
					: $oLib_Property->default_value;

				$acronym = $oLib_Property->description == ''
					? htmlspecialchars($oLib_Property->name)
					: '<acronym title="' . htmlspecialchars($oLib_Property->description) . '">'
						. htmlspecialchars($oLib_Property->name) . '</acronym>';

				$oTr = Core::factory('Core_Html_Entity_Tr')
					->add(
						Core::factory('Core_Html_Entity_Td')
							->value($acronym)
					);

				$oTable->add($oTr);

				switch ($oLib_Property->type)
				{
					case 0: /* Текстовое поле */
						$oTr->add(
							Core::factory('Core_Html_Entity_Td')
								->add(
									Core::factory('Core_Html_Entity_Input')
										->size(50)
										->name("lib_property_id_{$oLib_Property->id}")
										->value($value)
								)
						);
					break;
					case 1: /* Флажок */
						$oCore_Html_Entity_Checkbox = Core::factory('Core_Html_Entity_Input')
							->size(30)
							->name("lib_property_id_{$oLib_Property->id}")
							->type('checkbox')
							->id("lib_property_id_{$oLib_Property->id}");

						if (strtolower($value) == 'true')
						{
							$oCore_Html_Entity_Checkbox->checked('checked');
						}

						$oTr->add(
							Core::factory('Core_Html_Entity_Td')
							->add(
								$oCore_Html_Entity_Checkbox
							)
							->add(
								Core::factory('Core_Html_Entity_Label')
									->for("lib_property_id_{$oLib_Property->id}")
									->value('&nbsp;' . Core::_('Admin_Form.yes'))
							)
						);
					break;
					case 2: // XSL шаблон
						$oXsl = Core_Entity::factory('Xsl')->getByName($value);

						if ($oXsl)
						{
							$xsl_id = $oXsl->id;
							$xsl_dir_id = $oXsl->xsl_dir_id;
						}
						else
						{
							$xsl_id = 0;
							$xsl_dir_id = 0;
						}

						$editXslId = "editXsl_{$this->_object->id}_{$xsl_id}";

						$oTr->add(
							Core::factory('Core_Html_Entity_Td')
								->add(
									Core::factory('Core_Html_Entity_Select')
										->name("xsl_dir_id_{$oLib_Property->id}")
										->id("xsl_dir_id_{$oLib_Property->id}")
										->style('width: 220px')
										->options(
											array(' … ') + $aXslDirs
										)
										->value($xsl_dir_id)
										// lib_property_id передается для исключения Abort в ajaxPrefilter
										->onchange("$.ajaxRequest({path: '/admin/structure/index.php', context: 'lib_property_id_{$oLib_Property->id}', callBack: [$.loadSelectOptionsCallback, function(){\$('#{$windowId} #lib_property_id_{$oLib_Property->id}').val('{$xsl_id}')}], action: 'loadXslList',additionalParams: 'xsl_dir_id=' + this.value + '&lib_property_id={$oLib_Property->id}',windowId: '{$windowId}'}); return false")
								)
								->add(
									Core::factory('Core_Html_Entity_Script')
										->type("text/javascript")
										->value("$('#{$windowId} #xsl_dir_id_{$oLib_Property->id}').change();")
								)
								->add(
									Core::factory('Core_Html_Entity_Div')
										->style('clear: both')
								)
								->add(
									Core::factory('Core_Html_Entity_Select')
										->name("lib_property_id_{$oLib_Property->id}")
										->id("lib_property_id_{$oLib_Property->id}")
										->style('width: 330px')
										->value($xsl_dir_id)
								)
								->add(
									Core::factory('Core_Html_Entity_Img')
										->src('/admin/images/edit.gif')
										->id($editXslId)
										->class('pointer left5px')
										->onclick("$.openWindow({path: '/admin/xsl/index.php', additionalParams: 'xsl_dir_id={$xsl_dir_id}&hostcms[checked][1][{$xsl_id}]=1&hostcms[action]=edit'})")
								)
						);
					break;
					case 3: // Список
						$aLib_Property_List_Values = $oLib_Property->Lib_Property_List_Values->findAll();
						$aOptions = array();
						foreach ($aLib_Property_List_Values as $oLib_Property_List_Value)
						{
							$aOptions[$oLib_Property_List_Value->value] = $oLib_Property_List_Value->name;
						}

						$oTr->add(
							Core::factory('Core_Html_Entity_Td')
								->add(
									Core::factory('Core_Html_Entity_Select')
										->name("lib_property_id_{$oLib_Property->id}")
										->id("lib_property_id_{$oLib_Property->id}")
										->style('width: 220px')
										->options(
											$aOptions
										)
										->value($value)
								)
						);
					break;
					case 4: // SQL-запрос
						// Выполняем запрос
						$query = $oLib_Property->sql_request;
						$query = str_replace('{SITE_ID}', CURRENT_SITE, $query);

						$aOptions = array();

						if (trim($query) != '')
						{
							try
							{
								$Core_DataBase = Core_DataBase::instance();
								$aRows = $Core_DataBase
									->setQueryType(0)
									->query($query)
									->asAssoc()
									->result();

								foreach ($aRows as $sql_row)
								{
									$aOptions[$sql_row[$oLib_Property->sql_value_field]]
										= htmlspecialchars(Core_Type_Conversion::toStr($sql_row[$oLib_Property->sql_caption_field])) . ' [' . htmlspecialchars(Core_Type_Conversion::toStr($sql_row[$oLib_Property->sql_value_field])) . ']';
								}
							}
							catch (Exception $e)
							{
								Core_Message::show(
									Core::_('Structure.query_error', htmlspecialchars($query)), 'error'
								);
								Core_Message::show($e->getMessage(), 'error');
							}

							$oTr->add(
								Core::factory('Core_Html_Entity_Td')
									->add(
										Core::factory('Core_Html_Entity_Select')
											->name("lib_property_id_{$oLib_Property->id}")
											->id("lib_property_id_{$oLib_Property->id}")
											->style('width: 220px')
											->options(
												$aOptions
											)
											->value($value)
									)
							);
						}
					break;
					case 5: // Текстовое поле
						$oTr->add(
							Core::factory('Core_Html_Entity_Td')
								->add(
									Core::factory('Core_Html_Entity_Textarea')
										->name("lib_property_id_{$oLib_Property->id}")
										->id("lib_property_id_{$oLib_Property->id}")
										->cols(48)
										->rows(5)
										->value($value)
								)
						);
					break;
				}
			}
			$oTable->execute();
		}

		echo json_encode(ob_get_clean());
		exit();
	}
}