<?php
/**
 * Market.
 *
 * @package HostCMS
 * @version 6.x
 * @author Hostmake LLC
 * @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 */
require_once('../../bootstrap.php');

Core_Auth::authorization('market');

$sAdminFormAction = '/admin/market/index.php';

// Контроллер формы
$oAdmin_Form_Controller = Admin_Form_Controller::create();
$oAdmin_Form_Controller
	->setUp()
	->path($sAdminFormAction)
	->title(Core::_('Market.title'));

$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);

ob_start();

Admin_Form_Entity::factory('Title')
	->name(Core::_('Market.title'))
	->execute();

$oMarket_Controller = Market_Controller::instance();

// Установка модуля
if (Core_Array::getRequest('install'))
{
	$oMarket_Controller
		->setMarketOptions()
		->getModule(intval(Core_Array::getRequest('install')));
}

// Вывод списка
$data = $oMarket_Controller
	->setMarketOptions()
	->getMarket();

$sWindowId = $oAdmin_Form_Controller->getWindowId();

if ($oMarket_Controller->error == 0)
{
	$aCategories = $aItems = array();

	$aCategories = $oMarket_Controller->categories;
	$aItems = $oMarket_Controller->items;

	$oAdmin_Form_Entity_Select_Category = Admin_Form_Entity::factory('Select');
	$oAdmin_Form_Entity_Select_Category
		->name('category_id')
		->value($oMarket_Controller->category_id)
		->onchange('changeCategory(this)')
		->style('width: 200px');

	$aTmp = array(
		array(
			//'attr' => array('disabled' => 'disabled'),
			'value' => 'Выбрать категорию'
		)
	);

	foreach($aCategories as $object)
	{
		$aTmp[$object->id] = $object->name;
	}

	$oAdmin_Form_Entity_Select_Category
		->options($aTmp)
		->execute();

	$count_pages = ceil($oMarket_Controller->total/$oMarket_Controller->limit);

	$sHtml = '<div class="market">';

	//$i = 1;
	$iCountItems = count($aItems);
	if($iCountItems)
	{
		foreach($aItems as $object)
		{
			$sHtml .= '
				<div class="module_block">
					<img src="' . $object->image_small . '"/>
					<span class="title">' . htmlspecialchars($object->name) . '</span>
					<span class="category_name">' . htmlspecialchars($object->category_name) . '</span>';

			if ($object->installed)
			{
				$sHtml .= '<span class="installed">Установлен</span>';
			}
			elseif ($object->paid && !$object->installed || $object->price == 0)
			{
				$sHtml .= '<a class="install" onclick="$.adminLoad({path:\'/admin/market/index.php\',action:\'\',operation:\'\',additionalParams:\'install=' . $object->id . '&category_id=' . $oMarket_Controller->category_id . '&current=' .  $oMarket_Controller->page . '\',windowId:\'' . $sWindowId . '\'}); return false"  href="/admin/market/index.php?hostcms[window]=' . $sWindowId . '&install=' . $object->id . '&category_id=' . $oMarket_Controller->category_id . '&current=' . $oMarket_Controller->page . '">Установить</a>';
			}
			else
			{
				$sHtml .= '
					<a class="price" target="_blank" href="' .  $object->url . '">' . round($object->price) . ' ' . $object->currency . ' ▶</a>';
			}

			$sHtml .= '</div>';
		}
	}

	$sHtml .= '</div>';

	if ($oMarket_Controller->category_id && $count_pages > 1)
	{
		$sHtml .= '<div class="pagination">';
		for ($i = 1; $i <= $count_pages; $i++)
		{
			if ($oMarket_Controller->page == $i)
			{
				$sHtml .= "<span class=\"current\">{$i}</span>";
			}
			else
			{
				$sHtml .='<a class="page_link" onclick="$.adminLoad({path:\'/admin/market/index.php\',action:\'\',operation:\'\',additionalParams:\'category_id=' . $oMarket_Controller->category_id . '&current=' . $i . '\',windowId:\'' . $sWindowId . '\'}); return false"  href="/admin/market/index.php?hostcms[window]=' . $sWindowId . '&category_id=' . $oMarket_Controller->category_id . '&current=' . $i . '">' . $i . '</a>';
			}
		}

		$sHtml .= '</div>';
	}

	$sHtml .='<script type="text/javascript">
	function changeCategory(object)
	{
		if (object && object.tagName == "SELECT")
		{
			category_id = parseInt(object.options[object.selectedIndex].value);
			$.adminLoad({path: "/admin/market/index.php", windowId:"' . $sWindowId . '", additionalParams: "category_id=" + category_id});
		}
		return false;
	}</script>';

	$oAdmin_Form_Entity_Form
		->action($sAdminFormAction)
		->add(
			Admin_Form_Entity::factory('Code')
				->html($sHtml)
		)
		->execute();
}
else
{
	// Показ ошибок
	Core_Message::show(Core::_('Update.server_error_respond_' . $oMarket_Controller->error), 'error');
}

$oAdmin_Answer = Core_Skin::instance()->answer();

$oAdmin_Answer
	->ajax(Core_Array::getRequest('_', FALSE))
	->content(ob_get_clean())
	->message('')
	->title(Core::_('Market.title'))
	->execute();