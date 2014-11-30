<?php
/**
* Online shop.
*
* @package HostCMS
* @version 6.x
* @author Hostmake LLC
* @copyright © 2005-2014 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
*/
require_once('../../../../bootstrap.php');

Core_Auth::authorization('shop');

// Получаем параметры, getRequest, т.к. изначально данные идут GET'ом, затем hidden-полями формы, т.е. POST'ом
$oShop = Core_Entity::factory('Shop', Core_Array::getRequest('shop_id', 0));
$oShopDir = $oShop->Shop_Dir;
$oShopGroup = Core_Entity::factory('Shop_Group', Core_Array::getRequest('shop_group_id', 0));

$oAdmin_Form_Controller = Admin_Form_Controller::create();
$oAdmin_Form_Entity_Breadcrumbs = Admin_Form_Entity::factory('Breadcrumbs');

// Контроллер формы
$oAdmin_Form_Controller->setUp()->path('/admin/shop/order/report/index.php');

$sSuffix = " ";
$sSuffix .= $oShop->Shop_Currency->name;

$oXml = simplexml_load_string("<?xml version=\"1.0\" encoding=\"UTF-8\"?><graph numberSuffix='$sSuffix' decimalPrecision='2'  hoverCapBorder='A7BD34' hoverCapBgColor='A7BD34' formatNumberScale='0' showvalues='0' numdivlines='3' numVdivlines='10' showShadow='0' lineThickness='2' animation='1' showLegend='1' canvasBorderColor='cccccc' canvasBorderThickness='1' anchorSides='10' anchorRadius='2' baseFontColor='ffffff' outCnvBaseFontColor='000000'><categories></categories><dataset seriesName='Сумма' color='A7BD34' anchorBgColor='A7BD34'></dataset></graph>");

// Обработка данных формы
if(!is_null(Core_Array::getPost('do_show_report')))
{
	$sDateFrom = Core_Date::datetime2sql(Core_Array::getPost('sales_order_begin_date') . ' 00:00:00');
	$sDateTo = Core_Date::datetime2sql(Core_Array::getPost('sales_order_end_date') . ' 23:59:59');

	$oQueryBuilderSelect = Core_QueryBuilder::select(array(Core_QueryBuilder::expression('COUNT(DISTINCT (shop_orders.id))'), 'count_orders'),
			array(Core_QueryBuilder::expression('SUM(quantity)'), 'count_items'),
			array(Core_QueryBuilder::expression('SUM(shop_order_items.price * quantity)'), 'total_sum'))
		->from('shop_orders')
		->leftJoin('shop_order_items', 'shop_orders.id', '=', 'shop_order_items.shop_order_id')
		->where('shop_orders.shop_id', '=', $oShop->id)
		->where('shop_orders.canceled', '=', 0)
		->where('shop_orders.datetime', '>=', $sDateFrom)
		->where('shop_orders.datetime', '<=', $sDateTo)
		->groupBy('date_title')
		->orderBy('date_title')
	;

	if($shop_system_of_pay_id = Core_Array::getPost('shop_system_of_pay_id',0))
	{
		$oQueryBuilderSelect->where('shop_orders.shop_payment_system_id', '=', $shop_system_of_pay_id);
	}

	switch(Core_Array::getPost('sales_order_grouping'))
	{
		case 1: // группировка по неделям
			$sFormatDateTitle = '%u';
			$oQueryBuilderSelect->select(array(Core_QueryBuilder::expression("DATE_FORMAT(shop_orders.datetime, '%Y')"), 'year_title'));
			break;
		case 2: // группировка по дням
			$sFormatDateTitle = '%d.%m.%Y';
			break;
		default: // группировка по месяцам
			$sFormatDateTitle = '%m %Y';
		break;
	}

	$oQueryBuilderSelect->select(array(Core_QueryBuilder::expression("DATE_FORMAT(shop_orders.datetime, '{$sFormatDateTitle}')"), 'date_title'));


	if(!is_null($iSeller = Core_Array::getPost('shop_seller_id')) && $iSeller > 0)
	{
		$oQueryBuilderSelect
			->join('shop_items', 'shop_items.id', '=', 'shop_order_items.shop_item_id')
			->join('shop_sellers', 'shop_sellers.id', '=', 'shop_items.shop_seller_id');

		$oQueryBuilderSelect->where('shop_items.shop_seller_id', '=', $iSeller);
	}

	if(!is_null(Core_Array::getPost('sales_order_show_only_paid_items')))
	{
		$oQueryBuilderSelect->where('paid', '=', 1);
	}

	if(($iOrderStatusID = Core_Array::getPost('shop_order_status_id', 0)) != 0)
	{
		$oQueryBuilderSelect->where('shop_order_status_id', '=', $iOrderStatusID);
	}

	//echo $oQueryBuilderSelect->execute()->getLastQuery();
	$aOrdersResult = $oQueryBuilderSelect->execute()->asAssoc()->result();

	// Создаем второй запрос, отличается от первого выборкой полей в SELECT'е, и дополнительным GROUP BY
	$oQueryBuilderSelect->clearSelect();
	$oQueryBuilderSelect->select(array(Core_QueryBuilder::expression("DATE_FORMAT(shop_orders.datetime, '{$sFormatDateTitle}')"), 'date_title'), 'shop_orders.id');
	$oQueryBuilderSelect->groupBy('shop_order_items.shop_order_id');

	$aOrdersResultPeriod = $oQueryBuilderSelect->execute()->asAssoc()->result();

	$aOrdersResultPeriodParsed = array();

	foreach($aOrdersResultPeriod as $aTmpArray)
	{
		$aOrdersResultPeriodParsed[$aTmpArray['date_title']][] = $aTmpArray['id'];
	}

	$sDateFrom  = Core_Date::sql2date($sDateFrom);
	$sDateTo  = Core_Date::sql2date($sDateTo);
	?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html>
		<head>
			<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"></meta>
			<title><?php echo Core::_('Shop_Item.sales_report_title', $oShop->name, $sDateFrom, $sDateTo, "")?></title>
			<style>
				.admin_table
				{
					border: 1px solid #eeeeee;
					margin-top: 5px;
				}
				.admin_table td
				{
					empty-cells: show;
					border-bottom: 1px;
					border-bottom-style: solid;
					border-color: #dddddd;
				}
				.admin_table td div
				{
					clear: both;
					position: relative;
					margin: 0;
					padding: 0;
				}
				.admin_table td div.dl
				{
					overflow: hidden;
				}
				.admin_table_title
				{
					color: #000000;
					font-weight: bold;
					text-align: left;
					height: 25px
				}
				.admin_table_title td
				{
					padding-left: 5px;
					padding-right: 5px;
					padding-bottom: 2px;
					background-color: #EAEAEA;
					border-top: #D9D9D9 solid 1px;
					border-bottom: #D9D9D9 solid 1px;
				}
				.admin_table_title .hl
				{
					background-color: #E0E0E0;
				}
				.row_table
				{
					background-color: #FFFFFF;
				}
				.row_table .hl
				{
					background-color: #F6F6F6;
				}
				.row_table td,
				.row_table_over td,
				.admin_td,
				.row_table_odd td,
				.row_table_over_odd td,
				.Highlight_row_table td,
				.Highlight_row_table_odd td
				{
					padding-left: 3px;
					padding-right: 3px;
					padding-bottom: 2px;
					border-bottom: 1px;
					border-bottom-style: solid;
					border-color: #dddddd;
					overflow: hidden;
					whitespace: nowrap;
				}
				.admin_table_sub_title td
				{
					font-weight: bold;
					font-size: 120%;
				}
				.report_height td
				{
					height: 25px;
				}
				.admin_table_filter td
				{
					background-color: #F2F0EB;
					border-bottom: #CEC3A3 solid 1px;
					padding-right: 6px;
					padding-left: 4px;
				}
				.admin_table_filter .hl
				{
					background-color: #E9E7E2;
				}
				h1
				{
					position: relative;
					font-weight: normal;
					font-size: 18pt;
					color: #000000;
					border-left: 6px solid #db1905;
					padding-bottom: 0em;
					padding-left: 34px;
					left: -40px;
					margin-top: 15px;
				}
				body
				{
					margin: 3.5em;
					font-family: Arial, Verdana, 'MS Sans Serif', sans-serif;
					background-color: white;
					font-size: 75%;
					height: auto !important;
					min-height: 100%;
					padding: 0px;
					color: #333;
					position: relative;
					display: block;
				}
				table
				{
					empty-cells: show;
					border-spacing: 2px;
				}

			</style>
			<script src="/admin/js/jquery/jquery.js"></script>
			<script src="/admin/js/fusionchart/FusionCharts.js"></script>
		</head>
		<body style="margin: 3.5em">
			<p style="margin-bottom: 40px"><img src="/admin/images/logo.gif" alt="(^) <?php echo "www.hostcms.ru"?>" title="<?php echo "www.hostcms.ru"?>"></p>
			<h1><?php echo Core::_('Shop_Item.sales_report_title', $oShop->name, $sDateFrom, $sDateTo, "")?></h1>
			<div id="chartContainer">А вот и диаграмма!</div>
			<?php

			if(count($aOrdersResult) > 0)
			{
				?>
				<table cellpadding="2" cellspacing="2" width="100%" class="admin_table">
					<tr class="admin_table_title">
				    <td></td>
				    <td width="100"><?php echo Core::_('Shop_Item.catalog_marking')?></td>
				    <td width="50"><?php echo Core::_('Shop_Item.form_sales_order_count_orders')?></td>
				    <td width="50"><?php echo Core::_('Shop_Item.form_sales_order_count_items')?></td>
				    <td width="100"><?php echo Core::_('Shop_Item.form_sales_order_total_summ')?></td>
				    <td width="100"><?php echo Core::_('Shop_Item.form_sales_order_order_status')?></td>
				  </tr>
				<?php

				$aMonths = array(
					'01' => Core::_('Shop_Item.form_sales_order_month_january'),
					'02' => Core::_('Shop_Item.form_sales_order_month_february'),
					'03' => Core::_('Shop_Item.form_sales_order_month_march'),
					'04' => Core::_('Shop_Item.form_sales_order_month_april'),
					'05' => Core::_('Shop_Item.form_sales_order_month_may'),
					'06' => Core::_('Shop_Item.form_sales_order_month_june'),
					'07' => Core::_('Shop_Item.form_sales_order_month_july'),
					'08' => Core::_('Shop_Item.form_sales_order_month_august'),
					'09' => Core::_('Shop_Item.form_sales_order_month_september'),
					'10' => Core::_('Shop_Item.form_sales_order_month_october'),
					'11' => Core::_('Shop_Item.form_sales_order_month_november'),
					'12' => Core::_('Shop_Item.form_sales_order_month_december')
				);

				$iShopOrderItemsCount = 0;
				$iShopOrderItemsSum = 0;

				foreach($aOrdersResult as $rowOrdersResult)
				{
					switch (Core_Array::getPost('sales_order_grouping'))
					{
						case 0: // группировка по месяцам
							// Разделяем месяц и год
							$mas_date = explode(' ',$rowOrdersResult['date_title']);

							$period_title = Core_Array::get($aMonths, $mas_date[0]) . ' ' . $mas_date[1];
						break;
						case 1: // группировка по неделям
							$DayLen = 24 * 60 * 60;

							$WeekLen = 7 * $DayLen;

							$year = $rowOrdersResult['year_title'];//1993;
							$week = $rowOrdersResult['date_title'];//1;

							$StJ = gmmktime(0,0,0,1,1,$year); // 1 января, 00:00:00

							// Определим начало недели, к которой относится 1 января
							$DayStJ = gmdate("w",$StJ);
							$DayStJ = ($DayStJ == 0 ? 7 : $DayStJ);
							$StWeekJ = $StJ - ($DayStJ-1) * $DayLen;

							// Если 1 января относится к 1й неделе, то в $week получается одна "лишняя" неделя
							if( gmdate("W",$StJ) == "01" )$week--;

							// прибавили к началу "январской" недели номер нашей недели
							$start = $StWeekJ + $week * $WeekLen;

							// К началу прибавляем недели (получаем след. понедельник, 00:00) и отняли одну секунду - т.е. воскресенье, 23:59
							$end = $start + $WeekLen - 5*60*60;

							$period_title = $rowOrdersResult['date_title'] . Core::_('Shop_Item.form_sales_order_week') . date('d.m.Y', $start) . '&mdash;' . date('d.m.Y', $end);
						break;
						default: // группировка по дням
							$period_title = $rowOrdersResult['date_title'];
						break;
					}

					?>
					<tr class="row_table admin_table_sub_title report_height">
						<td><strong><?php echo $period_title?></strong></td>
				    <td>&nbsp;</td>
				    <td><?php echo $rowOrdersResult['count_orders']?></td>
				    <td><?php echo $rowOrdersResult['count_items']?></td>
				    <td><?php echo sprintf("%.2f %s", round($rowOrdersResult['total_sum'], 2), $oShop->Shop_Currency->name)?></td>
				    <td></td>
					</tr>
					<?php
					$oCategory = $oXml->categories->addChild('category');
					$oCategory->addAttribute('name', $period_title);
					//$oCategory->addAttribute('showName', 0);
					$oSet = $oXml->dataset->addChild('set');
					$oSet->addAttribute('value', sprintf("%.2f", $rowOrdersResult['total_sum']));

					if(!is_null(Core_Array::getPost('sales_order_show_list_items')))
					{
						if(count($aOrdersResultPeriodParsed[$rowOrdersResult['date_title']]) > 0)
						{
							$oShop_Orders = Core_Entity::factory('Shop_Order');

							$oShop_Orders->queryBuilder()->where('id', 'IN', $aOrdersResultPeriodParsed[$rowOrdersResult['date_title']]);

							$aShop_Orders = $oShop_Orders->findAll();

							foreach ($aShop_Orders as $oShop_Order)
							{
								?>
								<tr class="row_table report_height" style="font-size: 120%">
									<td colspan="2"><?php echo sprintf(Core::_('Shop_Item.form_sales_order_orders_number'), $oShop_Order->invoice, Core_Date::sql2date($oShop_Order->datetime))?><?php
									if ($oShop_Order->payment_datetime != '0000-00-00 00:00:00')
									{
										$payment_system_string = '';
									
										if(!is_null(Core_Entity::factory('Shop_Payment_System')->find($oShop_Order->shop_payment_system_id)->id))
										{
											$payment_system_string = ' (' . Core_Entity::factory('Shop_Payment_System', $oShop_Order->shop_payment_system_id)->name . ')';
										}
										
										echo sprintf(Core::_('Shop_Item.form_sales_order_date_of_paid'), Core_Date::sql2datetime($oShop_Order->payment_datetime)) . $payment_system_string;
									}
									?></td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td><?php echo $oShop_Order->sum()?></td>
									<td><?php echo $oShop_Order->Shop_Order_Status->name?></td>
								</tr>
								<?php
								// Получаем список товаров данного заказа
								$aShopOrderItems = $oShop_Order->Shop_Order_Items->findAll();

								foreach($aShopOrderItems as $oShopOrderItem)
								{
									?>
									<tr class="row_table report_height">
										<td>—<?php echo $oShopOrderItem->name?></td>
										<td><?php echo $oShopOrderItem->marking?></td>
										<td></td>
										<td><?php echo $oShopOrderItem->quantity?></td>
										<td><?php echo $oShopOrderItem->price?></td>
										<td></td>
									</tr>
									<?php
									$iShopOrderItemsCount += $oShopOrderItem->quantity;
									$iShopOrderItemsSum += $oShopOrderItem->price * $oShopOrderItem->quantity;
								}
							}
						}
					}
					else
					{
						$iShopOrderItemsCount += $rowOrdersResult['count_items'];
						$iShopOrderItemsSum += $rowOrdersResult['total_sum'];
					}
				}

				?>
				<tr class="admin_table_filter row_table admin_table_sub_title">
					<td></td>
					<td></td>
					<td>∑</td>
					<td><?php echo $iShopOrderItemsCount?></td>
					<td><?php echo $iShopOrderItemsSum?></td>
					<td></td>
				</tr>
				<?php

				?></table><?php
			}
			else
			{
				?><p><?php echo Core::_('Shop_Item.form_sales_order_empty_orders')?></p><?php
			}
			?>
			
		<script>
			jQuery(document).ready(function(){
				var chart = new FusionCharts('/admin/js/fusionchart/FCF_MSLine.swf', 'ChartId', 600, 250);
				chart.setDataXML('<?php echo Core_Str::escapeJavascriptVariable($oXml->asXml())?>');
				chart.render('chartContainer');
			});
		</script>
		</body>
	</html>
	<?php
}
else
{
	// Первая крошка на список магазинов
	$oAdmin_Form_Entity_Breadcrumbs
		->add(Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('Shop.menu'))
		->href($oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/index.php'))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/index.php')))
	;

	// Крошки по директориям магазинов
	if($oShopDir->id)
	{
		$oShopDirBreadcrumbs = $oShopDir;

		$aBreadcrumbs = array();

		do
		{
			$aBreadcrumbs[] = Admin_Form_Entity::factory('Breadcrumb')
				->name($oShopDirBreadcrumbs->name)
				->href($oAdmin_Form_Controller->getAdminLoadHref(
						'/admin/shop/index.php', NULL, NULL, "shop_dir_id={$oShopDirBreadcrumbs->id}"))
			->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
						'/admin/shop/index.php', NULL, NULL, "shop_dir_id={$oShopDirBreadcrumbs->id}"));
		}while($oShopDirBreadcrumbs = $oShopDirBreadcrumbs->getParent());

		$aBreadcrumbs = array_reverse($aBreadcrumbs);

		foreach ($aBreadcrumbs as $oBreadcrumb)
		{
			$oAdmin_Form_Entity_Breadcrumbs->add($oBreadcrumb);
		}
	}

	// Крошка на список товаров и групп товаров магазина
	$oAdmin_Form_Entity_Breadcrumbs
		->add(Admin_Form_Entity::factory('Breadcrumb')
			->name($oShop->name)
			->href($oAdmin_Form_Controller->getAdminLoadHref('/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}"))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax('/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}")));

	// Крошки по группам товаров
	if($oShopGroup->id)
	{
		$oShopGroupBreadcrumbs = $oShopGroup;
		$aBreadcrumbs = array();

		do
		{
			$aBreadcrumbs[] = Admin_Form_Entity::factory('Breadcrumb')
				->name($oShopGroupBreadcrumbs->name)
				->href($oAdmin_Form_Controller->getAdminLoadHref(
					'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroupBreadcrumbs->id}"))
			->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
						'/admin/shop/item/index.php', NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroupBreadcrumbs->id}"));
		}while($oShopGroupBreadcrumbs = $oShopGroupBreadcrumbs->getParent());

		$aBreadcrumbs = array_reverse($aBreadcrumbs);

		foreach ($aBreadcrumbs as $oBreadcrumb)
		{
			$oAdmin_Form_Entity_Breadcrumbs->add($oBreadcrumb);
		}
	}

	// Крошка на текущую форму
	$oAdmin_Form_Entity_Breadcrumbs->add(Admin_Form_Entity::factory('Breadcrumb')
		->name(Core::_('Shop_Item.show_sales_order_link'))
		->href($oAdmin_Form_Controller->getAdminLoadHref(
			$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}"))
		->onclick($oAdmin_Form_Controller->getAdminLoadAjax(
			$oAdmin_Form_Controller->getPath(), NULL, NULL, "shop_id={$oShop->id}&shop_group_id={$oShopGroup->id}")));

	ob_start();

	// Заголовок
	Admin_Form_Entity::factory('Title')->name(Core::_('Shop_Item.show_sales_order_link'))->execute();

	$oAdmin_Form_Entity_Form = new Admin_Form_Entity_Form($oAdmin_Form_Controller);
	$oAdmin_Form_Entity_Form->action($oAdmin_Form_Controller->getPath());
	$oAdmin_Form_Entity_Form->target('_blank');
	$oAdmin_Form_Entity_Form->add($oAdmin_Form_Entity_Breadcrumbs);

	$windowId = $oAdmin_Form_Controller->getWindowId();

	$oAdmin_Form_Entity_Form
		->add(
			Admin_Form_Entity::factory('Radiogroup')
				->radio(array(Core::_('Shop_Item.form_sales_order_grouping_monthly'),
					Core::_('Shop_Item.form_sales_order_grouping_weekly'),
					Core::_('Shop_Item.form_sales_order_grouping_daily')
				))
				->caption(Core::_('Shop_Item.form_sales_order_select_grouping'))
				->name('sales_order_grouping')
				->divAttr(array('id' => 'sales_order_grouping'))
		)
		->add(Admin_Form_Entity::factory('Code')
			->html("<script>$(function() {
				$('#{$windowId} #sales_order_grouping').buttonset();
			});</script>")
		)
		->add(Admin_Form_Entity::factory('Checkbox')
		->name('sales_order_show_list_items')
		->caption(Core::_('Shop_Item.form_sales_order_show_list_items'))
		->value(1))

		->add(Admin_Form_Entity::factory('Date')
		->caption(Core::_('Shop_Item.form_sales_order_begin_date'))
		->name('sales_order_begin_date')
		->value(Core_Date::timestamp2sql(strtotime("-2 months")))
		->divAttr(array('style' => 'float: left;')))

		->add(Admin_Form_Entity::factory('Date')
		->caption(Core::_('Shop_Item.form_sales_order_end_date'))
		->name('sales_order_end_date')
		->value(Core_Date::timestamp2sql(time())))

		->add(Admin_Form_Entity::factory('Checkbox')
		->name('sales_order_show_only_paid_items')
		->caption(Core::_('Shop_Item.form_sales_order_show_paid_items'))
		->value(1))
	;

	$aSellers = array(' … ');

	$aShop_Sellers = $oShop->Shop_Sellers->findAll();

	foreach($aShop_Sellers as $oShop_Seller)
	{
		$aSellers[$oShop_Seller->id] = $oShop_Seller->name;
	}

	$oAdmin_Form_Entity_Form->add(Admin_Form_Entity::factory('Select')
		->options($aSellers)
		->caption(Core::_('Shop_Item.form_sales_order_sallers'))
		->name('shop_seller_id')
		->style('width: 320px')
	);

	$aPaySystems = array(' … ');

	$aShop_Payment_Systems = $oShop->Shop_Payment_Systems->findAll();

	foreach($aShop_Payment_Systems as $oShop_Payment_System)
	{
		$aPaySystems[$oShop_Payment_System->id] = $oShop_Payment_System->name;
	}

	$oAdmin_Form_Entity_Form->add(Admin_Form_Entity::factory('Select')
		->options($aPaySystems)
		->caption(Core::_('Shop_Item.form_sales_order_sop'))
		->name('shop_system_of_pay_id')
		->style('width: 320px')
	);

	$aOrderStatuses = array(' … ');

	$aShop_Order_Statuses = Core_Entity::factory('Shop_Order_Status')->findAll();

	foreach ($aShop_Order_Statuses as $oShop_Order_Status)
	{
		$aOrderStatuses[$oShop_Order_Status->id] = $oShop_Order_Status->name;
	}

	$oAdmin_Form_Entity_Form
		->add(Admin_Form_Entity::factory('Select')
			->options($aOrderStatuses)
			->caption(Core::_('Shop_Item.form_sales_order_status'))
			->name('shop_order_status_id')
			->style('width: 320px')
	)->add(
		Admin_Form_Entity::factory('Input')
			->type('hidden')
			->name('shop_id')
			->value(Core_Array::getGet('shop_id'))
	)->add(
		Admin_Form_Entity::factory('Input')
			->type('hidden')
			->name('shop_group_id')
			->value(Core_Array::getGet('shop_group_id'))
	)->add(
		Admin_Form_Entity::factory('Button')
			->name('do_show_report')
			->type('submit')
			->class('applyButton')
	);

	$oAdmin_Form_Entity_Form->execute();
	$oAdmin_Answer = Core_Skin::instance()->answer();
	$oAdmin_Answer
		->ajax(Core_Array::getRequest('_', FALSE))
		->content(ob_get_clean())
		->title(Core::_('Shop_Item.show_sales_order_link'))
		->execute()
	;
}