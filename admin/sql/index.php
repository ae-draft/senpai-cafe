<?php
/**
 * SQL.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../../bootstrap.php');

Core_Auth::authorization('sql');

$sAdminFormAction = '/admin/sql/index.php';

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create();
$oAdmin_Form_Controller
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('sql.title'))
	//->pageTitle(Core::_('sql.title'))
	;

// Меню формы
$oAdmin_Form_Entity_Menus = Admin_Form_Entity::factory('Menus');

$sOptimizePath = '/admin/sql/optimize/index.php';
$sRepairPath = '/admin/sql/repair/index.php';

// Элементы меню
$oAdmin_Form_Entity_Menus->add(
	Admin_Form_Entity::factory('Menu')
		->name(Core::_('Sql.table'))
		->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Sql.optimize_table'))
				->img('/admin/images/database_refresh.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref($sOptimizePath, '', NULL, 0, 0)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax($sOptimizePath, '', NULL, 0, 0)
				)
		)->add(
			Admin_Form_Entity::factory('Menu')
				->name(Core::_('Sql.repair_table'))
				->img('/admin/images/database_error.gif')
				->href(
					$oAdmin_Form_Controller->getAdminLoadHref($sRepairPath, '', NULL, 0, 0)
				)
				->onclick(
					$oAdmin_Form_Controller->getAdminLoadAjax($sRepairPath, '', NULL, 0, 0)
				)
		)
);

// Добавляем все меню контроллеру
$oAdmin_Form_Controller->addEntity($oAdmin_Form_Entity_Menus);

ob_start();

$iCount = 0;

try
{
	// Текущий пользователь
	$oUser = Core_Entity::factory('User')->getCurrent();

	// Read Only режим
	if (defined('READ_ONLY') && READ_ONLY || $oUser->read_only && !$oUser->superuser)
	{
		throw new Core_Exception(
			Core::_('User.demo_mode'), array(), 0, FALSE
		);
	}

	$aFile = Core_Array::getFiles('file');

	$sText = !is_null($aFile) && $aFile['size'] > 0
		? Core_File::read($aFile['tmp_name'])
		: Core_Array::getPost('text');

	if (!is_null($sText))
	{
		if (strlen(trim($sText)) > 0)
		{
			$iCount = Sql_Controller::instance()->execute($sText);
			if ($iCount != 0)
			{
				Core_Message::show(Core::_('Sql.success_message', $iCount));
			}

			$iLimit = 30;

			$result = Core_DataBase::instance()->asAssoc()->result();

			$iNumRows = count($result);

			if ($iNumRows > 0 && $iCount == 1)
			{
				$oTitleTr = Core::factory('Core_Html_Entity_Tr')
					->class('admin_table_title');

				$row = $result[0];
				if (is_array($row) && count($row) > 0)
				{
					foreach ($row as $key => $value)
					{
						$oTitleTr
							->add(
								Core::factory('Core_Html_Entity_Td')->value(htmlspecialchars($key))
							);
					}
				}

				$oDiv = Core::factory('Core_Html_Entity_Div')
					->style('height: 200px; overflow: auto');

				$oTable = Core::factory('Core_Html_Entity_Table')
					->class('admin_table sql_explain')
					// Top title
					->add($oTitleTr);

				$oDiv->add($oTable);

				$iShowedRows = 0;

				foreach ($result as $row)
				{
					// Выводим N первых записей
					if ($iShowedRows < $iLimit)
					{
						$oTr = Core::factory('Core_Html_Entity_Tr');

						if (is_array($row) && count($row) > 0)
						{
							foreach ($row as $value)
							{
								if (is_null($value))
								{
									$value = 'NULL';
								}
								$oTr->add(
									Core::factory('Core_Html_Entity_Td')->value(Core_Str::cut(strip_tags($value), 100))
								);
							}
						}
						$oTable->add($oTr);
						$iShowedRows++;
					}
					else
					{
						break;
					}
				}

				// Bottom title
				$oTable->add($oTitleTr);

				$oDiv->execute();

				Core::factory('Core_Html_Entity_P')
					->value(Core::_('Sql.rows_count', $iNumRows, $iShowedRows))
					->execute();
			}
		}
		else
		{
			Core_Message::show(Core::_('Sql.error_message'), 'error');
		}
	}
}
catch (Exception $e)
{
	$sText = NULL;
	Core_Message::show($e->getMessage(), 'error');
}

Admin_Form_Entity::factory('Title')
	->name(Core::_('sql.title'))
	->execute();

$oAdmin_Form_Controller->showChildren();

Core_Message::show(Core::_('sql.warning'));

$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);

$oAdmin_Form_Entity_Form
	->action($sAdminFormAction)
	->add(
		Admin_Form_Entity::factory('Textarea')
			->name('text')
			->caption(Core::_('sql.text'))
			->rows(15)
			->value(
			($iCount == 0 || mb_strlen($sText) < 10240)
				? $sText
				: NULL
			)
	)
	->add(
		Admin_Form_Entity::factory('File')
			->name('file')
			->caption(Core::_('sql.load_file'))
			->largeImage(array('show_params' => FALSE))
			->smallImage(array('show' => FALSE))
	);

$oAdmin_Form_Entity_Form
	->add(
		Admin_Form_Entity::factory('Button')
			->name('button')
			->type('submit')
			->value(Core::_('sql.button'))
			->class('applyButton')
			->onclick("res =confirm('" . Core::_('Sql.warningButton') . "'); if (res){ " . $oAdmin_Form_Controller->getAdminSendForm('exec') . " } return false")
	)
	->execute();

$oAdmin_Answer = Core_Skin::instance()->answer();

$oAdmin_Answer
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(ob_get_clean())
	->message('')
	->title(Core::_('sql.title'))
	->execute();
