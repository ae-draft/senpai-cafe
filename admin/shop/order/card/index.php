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

$sAdminFormAction = '/admin/shop/order/card/index.php';

$oShopOrder = Core_Entity::factory('Shop_Order', Core_Array::getGet('shop_order_id', 0));
$oShop = $oShopOrder->Shop;
$oCompany = $oShop->Shop_Company;

$sFullAddress = implode(", ", array(
	$oShopOrder->postcode,
	$oShopOrder->Shop_Country->name,
	$oShopOrder->Shop_Country_Location->name,
	$oShopOrder->Shop_Country_Location_City->name,
	$oShopOrder->Shop_Country_Location_City_Area->name,
	$oShopOrder->address
));

if (defined('SHOP_ORDER_CARD_XSL'))
{
	$oXsl = Core_Entity::factory('Xsl')->getByName(SHOP_ORDER_CARD_XSL);

	if (!is_null($oXsl))
	{
		$oShop
			->addEntity($oShop->Shop_Company)
			->addEntity(
				$oShop->Site->clearEntities()->showXmlAlias()
			)
			->addEntity(
				$oShopOrder->clearEntities()
					->showXmlCurrency(TRUE)
					->showXmlCountry(TRUE)
					->showXmlItems(TRUE)
					->showXmlDelivery(TRUE)
					->showXmlPaymentSystem(TRUE)
			);

		$sXml = $oShop->getXml();

		$return = Xsl_Processor::instance()
				->xml($sXml)
				->xsl($oXsl)
				->process();

		echo $return;
	}
	else
	{
		throw new Core_Exception('XSL template %name does not exist.', array(
			'%name' => SHOP_ORDER_CARD_XSL
		));
	}
}
else
{
	//Формируем html-код страницы
	?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
	<html>
	<head>
	<title><?php echo Core::_("Shop_Order.order_card", $oShopOrder->invoice, Core_Date::sql2datetime($oShopOrder->datetime))?></title>
	<meta http-equiv="Content-Language" content="ru">
	<meta content="text/html; charset=UTF-8" http-equiv=Content-Type>

	<style type="text/css">html, body, td
		{
			font-family: Arial, Verdana, Tahoma, sans-serif;
			font-size: 9pt;
			background-color: #FFFFFF;
			color: #000000;
		}

		.main_div
		{
			margin-left: 0.5em;
			margin-right: 0.5em;
			margin-top: 2em;
			margin-bottom: 1em;
		}

		.td_main
		{
			border-top: black 1px solid;
			border-left: black 1px solid;
		}

		.td_header
		{
			border-left: black 1px solid;
			border-top: black 1px solid;
			border-bottom: black 1px solid;
			text-align: center;
			font-weight: bold;
		}

		.td_main_2
		{
			border-left: black 1px solid;
			border-bottom: black 1px solid;
		}

		.tr_footer td
		{
			font-size: 11pt;
			font-weight: bold;
			white-space: nowrap;
		}

		table, td
		{
			empty-cells: show;
		}
	</style>
	</head>
	<body style="margin: 3.5em">

	<p style="margin-bottom: 40px"><img src="/admin/images/logo.gif" alt="(^) HostCMS" title="HostCMS"></p>

	<table cellpadding="2" cellspacing="2" border="0" width="100%">
	<tr>
			<td valign="top" width="17%">
				<?php echo Core::_("Shop_Order.order_card_supplier") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oShop->Shop_Company->name?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_inn_kpp") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oCompany->tin . "/" . $oCompany->kpp?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_ogrn") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oCompany->psrn?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_address") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oCompany->address?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_phone") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oCompany->phone?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_fax") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oCompany->fax?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_email") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oCompany->email?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_site") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oCompany->site?>
				</b>
			</td>
		</tr>
	</table>

	<h2 align="center"><?php echo Core::_("Shop_Order.order_card_dt", $oShopOrder->invoice, Core_Date::sql2date($oShopOrder->datetime))?></h2>

	<table cellpadding="2" cellspacing="2" border="0" width="100%">
		<tr>
			<td valign="top" width="17%">
				<?php echo Core::_("Shop_Order.payer") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oShopOrder->company?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_contact_person") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oShopOrder->surname . " " . $oShopOrder->name . " " . $oShopOrder->patronymic?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_address") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $sFullAddress?>
				</b>
			</td>
		</tr>
		<?php
		if (class_exists("SiteUsers"))
		{
		?>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_site_user") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oShopOrder->Siteuser->login . " (" . Core::_("Shop_Order.order_card_site_user_id") . " " . $oShopOrder->Siteuser->id . ")"?>
				</b>
			</td>
		</tr>

		<?php
		}
		?>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_phone") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oShopOrder->phone?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_fax") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oShopOrder->fax?>
				</b>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_email") . ":"?>
			</td>
			<td valign="top">
				<b>
					<?php echo $oShopOrder->email?>
				</b>
			</td>
		</tr>
	</table>
	<br>
	<table cellspacing="0" cellpadding="3" width="100%">
	<tr>
		<td class="td_header">
			<?php echo "№"?>
		</td>
		<td class="td_header">
			<?php echo Core::_("Shop_Order.table_description")?>
		</td>
		<td class="td_header">
			<?php echo Core::_("Shop_Order.table_mark")?>
		</td>
		<td class="td_header">
			<?php echo Core::_("Shop_Order.table_mesures")?>
		</td>
		<td class="td_header">
			<?php echo Core::_("Shop_Order.table_price") . ", " . $oShop->Shop_Currency->name?>
		</td>
		<td class="td_header">
			<?php echo Core::_("Shop_Order.table_amount")?>
		</td>
		<td class="td_header">
			<?php echo Core::_("Shop_Order.table_nds_tax")?>
		</td>
		<td class="td_header">
			<?php echo Core::_("Shop_Order.table_nds_value") . ", " . $oShop->Shop_Currency->name?>
		</td>
		<td class="td_header" style="border-right: 1px solid black; white-space: nowrap;">
			<?php echo Core::_("Shop_Order.table_amount_value") . ", " . $oShop->Shop_Currency->name?>
		</td>
	</tr>
	<?php
	$i = 1;

	$aShopOrderItems = $oShopOrder->Shop_Order_Items->findAll();

	$fShopTaxValueSum = 0.0;
	$fShopOrderItemSum = 0.0;

	if(count($aShopOrderItems) > 0)
	{
		foreach ($aShopOrderItems as $oShopOrderItem)
		{
			$sShopTaxRate = $oShopOrderItem->rate;

			$sShopTaxValue = $sShopTaxRate
				? $oShopOrderItem->getTax() * $oShopOrderItem->quantity
				: 0;

			$sItemAmount = $oShopOrderItem->getAmount();

			$fShopTaxValueSum += $sShopTaxValue;
			$fShopOrderItemSum += $sItemAmount;

			?>
			<tr>
			<td style="text-align: center;" class="td_main_2" >
			<?php echo $i++?>
			</td>
			<td class="td_main_2">
			<?php echo $oShopOrderItem->name?>
			</td>
			<td class="td_main_2">
			<?php echo $oShopOrderItem->marking?>
			</td>
			<td class="td_main_2">
			<?php echo $oShopOrderItem->Shop_Item->Shop_Measure->name?>
			</td>
			<td class="td_main_2">
			<?php echo $oShopOrderItem->price?>
			</td>
			<td style="text-align: center;" class="td_main_2">
			<?php echo $oShopOrderItem->quantity?>
			</td>
			<td style="text-align: center;" class="td_main_2">
			<?php echo $sShopTaxRate != 0 ? "{$sShopTaxRate}%" : $sShopTaxRate?>
			</td>
			<td style="text-align: center;" class="td_main_2">
			<?php echo $sShopTaxValue?>
			</td>
			<td class="td_main_2" style="border-right: 1px solid black; white-space: nowrap;">
			<?php echo $sItemAmount?>
			</td>
			</tr><?php
		}
	}

	?>
	</table>
	<table width="100%" cellspacing="0" cellpadding="3">
	<tr class="tr_footer">
		<td width="80%" align="right" style="border-bottom: 1px solid black;" colspan="6">
			<?php echo Core::_("Shop_Order.table_nds")?>
		</td>
		<td width="80%" align="right"  style="border-bottom: 1px solid black;" colspan="2">
			<?php echo sprintf("%.2f", $fShopTaxValueSum) . " " . $oShop->Shop_Currency->name?>
		</td>
	</tr>
	<tr class="tr_footer">
		<td align="right" colspan="6">
			<?php echo Core::_("Shop_Order.table_all_to_pay")?>
		</td>
		<td align="right" colspan="2">
			<?php echo sprintf("%.2f", $fShopOrderItemSum) . " " . $oShop->Shop_Currency->name?>
		</td>
	</tr>
	</table>

	<table cellpadding="2" cellspacing="2" border="0"  width="100%">
	<tr>
		<td valign="top" width="17%">
			<?php echo Core::_("Shop_Order.order_card_system_of_pay") . ": "?>
		</td>
		<td valign="top">
			<b><?php echo $oShopOrder->Shop_Payment_System->name?></b>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<?php echo Core::_("Shop_Order.order_card_status_of_pay") . ": "?>
		</td>
		<td valign="top">
			<?php
			if ($oShopOrder->paid)
			{
				echo "<b>" . Core::_("Shop_Order.order_card_status_of_pay_yes") . "</b>";
			}
			else
			{
				echo Core::_("Shop_Order.order_card_status_of_pay_no");
			}
			?>
		</td>
	</tr>
	<tr>
		<td valign="top">
			<?php echo Core::_("Shop_Order.order_card_cancel") . ": "?>
		</td>
		<td valign="top">
			<?php
			if ($oShopOrder->canceled)
			{
				echo "<b>" . Core::_("Shop_Order.order_card_cancel_yes") . "</b>";
			}
			else
			{
				echo Core::_("Shop_Order.order_card_cancel_no");
			}

			?>
		</td>
	</tr>
	<?php
	if ($oShopOrder->shop_order_status_id)
	{
		?>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_order_status") . ": "?>
			</td>
			<td valign="top">
				<b><?php echo $oShopOrder->Shop_Order_Status->name . ' (' . Core_Date::sql2datetime($oShopOrder->status_datetime) . ')'?></b>
			</td>
		</tr>
		<?php
	}
	if ($oShopOrder->shop_delivery_condition_id)
	{
		?>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_type_of_delivery") . ": "?>
			</td>
			<td valign="top">
				<b><?php echo $oShopOrder->Shop_Delivery_Condition->Shop_Delivery->name . " (" . $oShopOrder->Shop_Delivery_Condition->name . ")"?></b>
			</td>
		</tr>
		<?php
	}
	if ($oShopOrder->description)
	{
		?>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_description") . ": "?>
			</td>
			<td>
				<?php echo nl2br($oShopOrder->description)?>
			</td>
		</tr>
		<?php
	}
	if ($oShopOrder->system_information)
	{
		?>
		<tr>
			<td valign="top">
				<?php echo Core::_("Shop_Order.order_card_system_info") . ": "?>
			</td>
			<td>
				<?php echo nl2br($oShopOrder->system_information)?>
			</td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}