<?php 

if (Core::moduleIsActive('siteuser'))
{
	$oSiteuser = Core_Entity::factory('Siteuser')->getCurrent();	
	
	// Если пользователь не авторизован
	if (!$oSiteuser->id)
	{
		header('Location: /users/');
		exit();
	}
}