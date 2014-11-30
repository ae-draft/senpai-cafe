<?php

defined('HOSTCMS') || exit('HostCMS: access denied.');

/**
 * Admin forms.
 * Типовой контроллер редактирования
 *
 * @package HostCMS 6\Admin
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
class Admin_Form_Action_Controller_Type_Edit extends Admin_Form_Action_Controller
{
	/**
	 * Allowed object properties
	 * @var array
	 */
	protected $_allowedProperties = array(
		'title', // Form Title
		'skipColumns' // Array of skipped columns
	);

	/**
	 * Model's key list
	 * @var array
	 */
	protected $_keys = array();

	/**
	 * Form's ID
	 * @var string
	 */
	protected $_formId = 'formEdit';

	/**
	 * Stores POST, which can change the controller
	 * @var mixed
	 */
	protected $_formValues = NULL;

	/**
	 * Constructor.
	 * @param Admin_Form_Action_Model $oAdmin_Form_Action action
	 */
	public function __construct(Admin_Form_Action_Model $oAdmin_Form_Action)
	{
		parent::__construct($oAdmin_Form_Action);

		// Set default title
		$oAdmin_Word = $this->_Admin_Form_Action->Admin_Word->getWordByLanguage(
			Core_Entity::factory('Admin_Language')->getCurrent()->id
		);
		$this->title = is_object($oAdmin_Word) ? $oAdmin_Word->name : 'undefined';

		// Пропускаемые свойства модели
		$this->skipColumns = array(
			'user_id',
			'deleted'
		);

		$this->skipColumns = array_combine($this->skipColumns, $this->skipColumns);

		// Далее может быть изменено
		$this->_formValues = $_POST;
	}

	/**
	 * Add skiping column
	 * @param string $column column name
	 * @return self
	 */
	public function addSkipColumn($column)
	{
		$this->skipColumns += array($column => $column);
		return $this;
	}

	/**
	 * Get model's key list
	 * Получение списка ключей модели (PK и FK)
	 * @return self
	 */
	protected function _loadKeys()
	{
		// Массив ключей, которые будут выводиться на дополнительной вкладке
		$this->_keys = array(
			$this->_object->getPrimaryKeyName()
		);

		$aRelations = $this->_object->getRelations();

		foreach ($aRelations as $relation)
		{
			$this->_keys[] = $relation['foreign_key'];
		}

		if (!empty($this->_keys))
		{
			$this->_keys = array_combine($this->_keys, $this->_keys);
		}

		return $this;
	}

	/**
	 * Form fields
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * Form tabs
	 * @var array
	 */
	protected $_tabs = array();

	/**
	 * Add new tab into form
	 * @param Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab new tab
	 * @return self
	 */
	public function addTab(Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab)
	{
		$this->_tabs[$oAdmin_Form_Entity_Tab->name] = $oAdmin_Form_Entity_Tab;
		return $this;
	}

	/**
	 * Add new tab into form before $oAdmin_Form_Entity_Tab_Before
	 * @param Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab new tab
	 * @param Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab_Before old tab
	 * @return self
	 */
	public function addTabBefore(Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab, Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab_Before)
	{
		// Find key for before object
		$key = array_search($oAdmin_Form_Entity_Tab_Before, $this->_tabs, $strict = TRUE);

		if ($key !== FALSE)
		{
			$aArrayKeys = array_keys($this->_tabs);
			// Порядковый номер для найденного символьного ключа
			$key = array_search($key, $aArrayKeys, TRUE);

			array_splice($this->_tabs, $key, 0, array($oAdmin_Form_Entity_Tab->name => $oAdmin_Form_Entity_Tab));
			return $this;
		}

		throw new Core_Exception("Before adding tab does not exist.");
	}

	/**
	 * Add new tab into form after $oAdmin_Form_Entity_Tab_After
	 * @param Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab new tab
	 * @param Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab_After old tab
	 * @return self
	 */
	public function addTabAfter(Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab, Skin_Default_Admin_Form_Entity_Tab $oAdmin_Form_Entity_Tab_After)
	{
		// Find key for after object
		$key = array_search($oAdmin_Form_Entity_Tab_After, $this->_tabs, $strict = FALSE);

		if ($key !== FALSE)
		{
			$aArrayKeys = array_keys($this->_tabs);
			// Порядковый номер для найденного символьного ключа
			$key = array_search($key, $aArrayKeys, TRUE);

			array_splice($this->_tabs, $key + 1, 0, array($oAdmin_Form_Entity_Tab->name => $oAdmin_Form_Entity_Tab));
			return $this;
		}

		throw new Core_Exception("After adding tab does not exist.");
	}

	/**
	 * Get all ordinary fields, created by table's fileds
	 * @return array
	 */
	public function getFields()
	{
		return $this->_fields;
	}

	/**
	 * Get all tabs
	 * @return array
	 */
	public function getTabs()
	{
		return $this->_tabs;
	}

	/**
	 * Check is tab isset
	 * @param string $tabName tab name
	 * @return boolean
	 */
	public function issetTab($tabName)
	{
		foreach ($this->_tabs as $oTab)
		{
			if ($oTab->name == $tabName)
			{
				return TRUE;
			}
		}

		return FALSE;
		//return isset($this->_tabs[$tabName]);
	}

	/**
	 * Get tab
	 * @param string $tabName
	 * @return Admin_Form_Entity_Tab
	 */
	public function getTab($tabName)
	{
		/*if (isset($this->_tabs[$tabName]))
		{
			return $this->_tabs[$tabName];
		}*/
		foreach ($this->_tabs as $oTab)
		{
			if ($oTab->name == $tabName)
			{
				return $oTab;
			}
		}

		throw new Core_Exception("Tab %tab does not exist.", array('%tab' => $tabName));
	}

	/**
	 * Get form field by name
	 * @param string $fieldName name
	 * @return Admin_Form_Entity
	 */
	public function getField($fieldName)
	{
		if (isset($this->_fields[$fieldName]))
		{
			return $this->_fields[$fieldName];
		}

		throw new Core_Exception("Field %fieldName does not exist.", array('%fieldName' => $fieldName));
	}

	/**
	 * Add field
	 * @param Admin_Form_Entity $oAdmin_Form_Entity field
	 * @return self
	 */
	public function addField(Admin_Form_Entity $oAdmin_Form_Entity)
	{
		$this->_fields[$oAdmin_Form_Entity->name] = $oAdmin_Form_Entity;
		return $this;
	}

	/**
	 * Load object's fields when object has been set
	 * После установки объекта загружаются данные о его полях
	 * @param object $object
	 * @return Admin_Form_Action_Controller_Type_Edit
	 * @hostcms-event Admin_Form_Action_Controller_Type_Edit.onBeforeSetObject
	 * @hostcms-event Admin_Form_Action_Controller_Type_Edit.onAfterSetObject
	 */
	public function setObject($object)
	{
		Core_Event::notify(get_class($this) . '.onBeforeSetObject', $this, array($object, $this->_Admin_Form_Controller));

		parent::setObject($object);

		$this->_loadKeys();

		// Получение списка полей объекта
		$aColumns = $this->_object->getTableColums();

		// Список закладок
		// Основная закладка
		$oAdmin_Form_Tab_EntityMain = Admin_Form_Entity::factory('Tab')
			->caption(Core::_('admin_form.form_forms_tab_1'))
			->name('main');

		$this->addTab($oAdmin_Form_Tab_EntityMain);

		//if (!is_null($this->_object->id))
		//{
			// Дополнительные (ключи)
			$oAdmin_Form_Tab_EntityAdditional = Admin_Form_Entity::factory('Tab')
				->caption(Core::_('admin_form.form_forms_tab_2'))
				->name('additional');

			$oUser = Core_Entity::factory('User')->getCurrent();

			!$oUser->superuser && $oAdmin_Form_Tab_EntityAdditional->active(FALSE);

			$this->addTab($oAdmin_Form_Tab_EntityAdditional);
		//}

		$modelName = $this->_object->getModelName();
		$primaryKeyName = $this->_object->getPrimaryKeyName();

		foreach ($aColumns as $columnName => $columnArray)
		{
			if (!isset($this->skipColumns[$columnName]))
			{
				$sTabName = isset($this->_keys[$columnName])
					? 'additional'
					: 'main';

				switch ($columnArray['datatype'])
				{
					case 'datetime':
						$oAdmin_Form_Entity_For_Column = Admin_Form_Entity::factory('DateTime');

						/*$date = ($this->_object->$columnName == '0000-00-00 00:00:00')
							? $this->_object->$columnName
							: Core_Date::sql2datetime($this->_object->$columnName);*/

						$oAdmin_Form_Entity_For_Column
							->value(
								//Core_Date::sql2datetime($this->_object->$columnName)
								$this->_object->$columnName
							);

						break;
					case 'date':
						$oAdmin_Form_Entity_For_Column = Admin_Form_Entity::factory('Date');

						$oAdmin_Form_Entity_For_Column
							->value(
								$this->_object->$columnName
							);

						break;
					case 'tinytext':
					case 'text':
					case 'mediumtext':
					case 'longtext':
					case 'tinyblob':
					case 'blob':
					case 'mediumblob':
					case 'longblob':
						$oAdmin_Form_Entity_For_Column = Admin_Form_Entity::factory('Textarea');

						$oAdmin_Form_Entity_For_Column
							->value(
								$this->_object->$columnName
							);
						break;
					case 'tinyint':
					case 'tinyint unsigned':
						// Только при длине 1 символ
						if ($columnArray['max_length'] == 1)
						{
							$oAdmin_Form_Entity_For_Column = Admin_Form_Entity::factory('Checkbox');

							$oAdmin_Form_Entity_For_Column
							->value(
								$this->_object->$columnName
							);
							break;
						}
					default:
						$oAdmin_Form_Entity_For_Column = Admin_Form_Entity::factory('Input');

						$oAdmin_Form_Entity_For_Column
							//->size(12) // изменить на расчет
							->value($this->_object->$columnName);

						if ($sTabName == 'main'
						&& $this->_tabs[$sTabName]->getCountChildren() == 0)
						{
							$oAdmin_Form_Entity_For_Column->class('large');
						}

						$columnName == 'id' && $oAdmin_Form_Entity_For_Column->readonly('readonly');

						break;
				}

				$format = array();

				// Найден формат по названию столбца
				if (!is_null($oAdmin_Form_Entity_For_Column->getFormat($columnName)))
				{
					$format += array('lib' => array('value' => $columnName));
				}

				switch ($columnArray['type'])
				{
					case 'string':
						if (!is_null($columnArray['max_length']))
						{
							$format += array('maxlen' =>
								// ограничение длины поля
								array('value' => $columnArray['max_length'])
							);
						}

						if (is_null($columnArray['default']) && !$columnArray['null'])
						{
							$format += array('minlen' =>
								// ограничение длины поля
								array('value' => 1)
							);
						}
					break;
					case 'int':
						$format += array('lib' => array(
							'value' => 'integer'
						));
						// В ограничение значений
						// $columnArray['min']
						// $columnArray['max']
					break;
				}

				if (!empty($format))
				{
					$oAdmin_Form_Entity_For_Column
						->format($format);
				}

				$oAdmin_Form_Entity_For_Column
					->name($columnName)
					->caption(Core::_($modelName . '.' . $columnName));

				// На дополнительную или основную вкладку
				/*$sTabName = isset($this->_keys[$columnName])
					? 'oAdmin_Form_Tab_EntityAdditional'
					: 'oAdmin_Form_Tab_EntityMain';*/

				if (/*!is_null($this->_object->getPrimaryKey())
					|| $sTabName == 'main'*/
					!(is_null($this->_object->getPrimaryKey()) && $columnName == $primaryKeyName)
					)
				{
					$this->_tabs[$sTabName]->add(
						$oAdmin_Form_Entity_For_Column
					);
				}

				$this->addField($oAdmin_Form_Entity_For_Column);
			}
		}

		Core_Event::notify(get_class($this) . '.onAfterSetObject', $this, array($object, $this->_Admin_Form_Controller));

		return $this;
	}

	/**
	 * Executes the business logic.
	 * @param mixed $operation Operation for action
	 * @return boolean
	 * @hostcms-event Admin_Form_Action_Controller_Type_Edit.onBeforeExecute
	 * @hostcms-event Admin_Form_Action_Controller_Type_Edit.onAfterExecute
	 */
	public function execute($operation = NULL)
	{
		Core_Event::notify(get_class($this) . '.onBeforeExecute', $this, array($operation, $this->_Admin_Form_Controller));

		switch ($operation)
		{
			case NULL: // Показ формы
				$this->_Admin_Form_Controller->title(
					$this->title
				);
				$return = $this->_showEditForm();
			break;
			case 'save':
				$primaryKeyName = $this->_object->getPrimaryKeyName();

				// Значение первичного ключа до сохранения
				$prevPrimaryKeyValue = $this->_object->$primaryKeyName;

				$this->_applyObjectProperty();

				ob_start();
				$modelName = $this->_object->getModelName();
				$actionName = $this->_Admin_Form_Controller->getAction();

				Core_Message::show(Core::_("{$modelName}.{$actionName}_success"));

				if (is_null($prevPrimaryKeyValue))
				{
					$windowId = $this->_Admin_Form_Controller->getWindowId();
					?><script type="text/javascript"><?php
					?>$.appendInput('<?php echo $windowId?>', '<?php echo $this->_formId?>', '<?php echo $primaryKeyName?>', '<?php echo $this->_object->$primaryKeyName?>');<?php
					?></script><?php
				}

				$this->addMessage(ob_get_clean());
				$return = TRUE;
			break;
			default:
				$this->_applyObjectProperty();
				$return = FALSE; // Показываем форму
			break;
		}

		Core_Event::notify(get_class($this) . '.onAfterExecute', $this, array($operation, $this->_Admin_Form_Controller));

		return $return;
	}

	/**
	 * Processing of the form. Apply object fields.
	 * @return self
	 * @hostcms-event Admin_Form_Action_Controller_Type_Edit.onBeforeApplyObjectProperty
	 * @hostcms-event Admin_Form_Action_Controller_Type_Edit.onAfterApplyObjectProperty
	 */
	protected function _applyObjectProperty()
	{
		ob_start();

		Core_Event::notify(get_class($this) . '.onBeforeApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		$aColumns = $this->_object->getTableColums();

		// Применение данных к объекту
		foreach ($aColumns as $columnName => $columnArray)
		{
			if (!isset($this->skipColumns[$columnName]))
			{
				$value = Core_Array::get($this->_formValues, $columnName);

				switch ($columnArray['datatype'])
				{
					case 'datetime':
						$value = $value != ''
							? Core_Date::datetime2sql($value)
							: '0000-00-00 00:00:00';
						break;
					case 'date':
						$value = $value != ''
							? Core_Date::date2sql($value)
							: '0000-00-00';
						break;
					case 'tinytext':
					case 'text':
					case 'mediumtext':
					case 'longtext':
					case 'tinyblob':
					case 'blob':
					case 'mediumblob':
					case 'longblob':
						// Nothing to do
						break;
					case 'tinyint':
					case 'tinyint unsigned':
						// Только при длине 1 символ
						if ($columnArray['max_length'] == 1)
						{
							// Checkbox
							$value = is_null($value) ? 0 : $value;
						}
					break;
					default:
						// Nothing to do
					break;
				}
				$this->_object->$columnName = $value;
			}
		}

		$this->_object->save();

		$message = ob_get_clean();

		!empty($message) && $this->addMessage($message);

		Core_Event::notify(get_class($this) . '.onAfterApplyObjectProperty', $this, array($this->_Admin_Form_Controller));

		return $this;
	}

	/**
	 * Show edit form
	 * @return boolean
	 */
	protected function _showEditForm()
	{
		ob_start();

		// Заголовок формы добавляется до вывода крошек, которые могут быть добавлены в контроллере
		array_unshift($this->_children,
			Admin_Form_Entity::factory('Title')
				->name($this->title)
			);

		// Форма
		$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form(
			$this->_Admin_Form_Controller
		);

		$oAdmin_Form_Entity_Form
			->id($this->_formId)
			->class('adminForm')
			->action(
				$this->_Admin_Form_Controller->getPath()
			);

		// Закладки
		$oAdmin_Form_Entity_Tabs = Admin_Form_Entity::factory('Tabs');
		$oAdmin_Form_Entity_Tabs->formId($this->_formId);

		// Все закладки к форме
		$oAdmin_Form_Entity_Form->add(
			$oAdmin_Form_Entity_Tabs
		);

		// Add all tabs to $oAdmin_Form_Entity_Tabs
		foreach ($this->_tabs as $oAdmin_Form_Tab_Entity)
		{
			if ($oAdmin_Form_Tab_Entity->getCountChildren() > 0)
			{
				$oAdmin_Form_Entity_Tabs->add(
					$oAdmin_Form_Tab_Entity
				);
			}
		}

		// Кнопки
		$oAdmin_Form_Entity_Form->add(
			$this->_addButtons()
		);

		// Форма добавляется к контроллеру
		$this->addEntity($oAdmin_Form_Entity_Form);

		foreach ($this->_children as $oAdmin_Form_Entity)
		{
			$oAdmin_Form_Entity->execute();
		}

		$this->addContent(
			ob_get_clean()
		);

		return TRUE;
	}

	/**
	 * Add save and apply buttons
	 * @return Admin_Form_Entity_Buttons
	 */
	protected function _addButtons()
	{
		// Кнопки
		$oAdmin_Form_Entity_Buttons = Admin_Form_Entity::factory('Buttons');

		// Кнопка Сохранить
		$oAdmin_Form_Entity_Button_Save = Admin_Form_Entity::factory('Button')
			->name('save')
			->class('saveButton')
			->value(Core::_('admin_form.save'))
			->onclick(
				$this->_Admin_Form_Controller->getAdminSendForm(NULL, 'save')
			);

		$oAdmin_Form_Entity_Button_Apply = Admin_Form_Entity::factory('Button')
			->name('apply')
			->class('applyButton')
			->type('submit')
			->value(Core::_('admin_form.apply'))
			->onclick(
				$this->_Admin_Form_Controller->getAdminSendForm(NULL, 'apply')
			);

		$oAdmin_Form_Entity_Buttons
			->add($oAdmin_Form_Entity_Button_Save)
			->add($oAdmin_Form_Entity_Button_Apply);

		return $oAdmin_Form_Entity_Buttons;
	}
}