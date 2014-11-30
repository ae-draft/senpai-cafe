<?php
/**
 * Typograph.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../../bootstrap.php');

Core_Auth::authorization('typograph');

$sAdminFormAction = '/admin/typograph/index.php';

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create();
$oAdmin_Form_Controller
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('typograph.title'))
	//->pageTitle(Core::_('typograph.title'))
	;

ob_start();

$sText = Typograph_Controller::instance()
	->process(Core_Array::getPost('text'), Core_Array::getPost('trailing_punctuation', FALSE));

Admin_Form_Entity::factory('Title')
	->name(Core::_('typograph.title'))
	->execute();

Core_Message::show(Core::_('typograph.warning'));

$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);

$oAdmin_Form_Entity_Form
	->action($sAdminFormAction)
	->add(
		Admin_Form_Entity::factory('Textarea')
			->name('text')
			->caption(Core::_('typograph.text'))
			->rows(15)
			->value($sText)
	)
	->add(
		Admin_Form_Entity::factory('Checkbox')
			->name('trailing_punctuation')
			->caption(Core::_('typograph.trailing_punctuation'))
			->value(Core_Array::getPost('trailing_punctuation'))
	);

// Оттипографированный текст
if ($oAdmin_Form_Controller->getAction() == 'process')
{
	ob_start();

	Core::factory('Core_Html_Entity_Div')
		->class('typograph_result')
		->value($sText)
		->execute();

	$oAdmin_Form_Entity_Form->add(
		Admin_Form_Entity::factory('Code')
			->html(ob_get_clean())
	);
}

$oAdmin_Form_Entity_Form
	->add(
		Admin_Form_Entity::factory('Button')
			->name('process')
			->type('submit')
			->value(Core::_('typograph.process'))
			->class('applyButton')
			->onclick(
				$oAdmin_Form_Controller->getAdminSendForm('process')
			)
	)
	->execute();

$oAdmin_Answer = Core_Skin::instance()->answer();

$oAdmin_Answer
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(ob_get_clean())
	->message('')
	->title(Core::_('typograph.title'))
	->execute();
