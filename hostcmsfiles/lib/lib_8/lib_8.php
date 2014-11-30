<?php
$guid = Core_Array::end(explode('/', trim(Core::$url['path'], '/')));

if ($guid)
{
	$oShop_Order = Core_Entity::factory('Shop_Order')->getByGuid($guid);

	if (!is_null($oShop_Order))
	{
		$siteuser_id = 0;
		if (Core::moduleIsActive('siteuser'))
		{
			$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();

			if ($oSiteuser)
			{
				$siteuser_id = $oSiteuser->id;
			}
		}

		// Проверяем, принадлежит ли заказ текущему юзеру
		if ($siteuser_id != 0 && $oShop_Order->siteuser_id == $siteuser_id || isset($_SESSION['order_' . $oShop_Order->id]))
		{
			$oShop_Payment_System_Handler = Shop_Payment_System_Handler::factory($oShop_Order->Shop_Payment_System);

			$oShop_Payment_System_Handler
				->shopOrder($oShop_Order)
				->printInvoice();
		}
		else
		{
			?>
			<h1>У Вас недостаточно прав на просмотр данного документа</h1>
			<p>Авторизируйтесь в <a href="/users/">личном кабинете</a>.</p>
			<?php
		}
	}
	else
	{
		?><h1>Не найден заказ</h1><?php
	}
}