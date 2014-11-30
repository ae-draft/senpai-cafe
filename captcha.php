<?php

// Подключаем основные классы
require_once ('bootstrap.php');

$captchaId = Core_Array::getGet('id', Core_Array::getGet('get_captcha'));

// Не показываем капчу, если ее запрашивает бот
if ($captchaId && !preg_match('/HTTP|BOT|SPIDE|CRAW|YANDEX|APORT|RAMBLER|SEARCH|SEEK|SITE/iu', Core_Array::get($_SERVER, 'HTTP_USER_AGENT', '')))
{
	$Core_Captcha = new Core_Captcha();

	$width = intval(Core_Array::getGet('width'));
	$height = intval(Core_Array::getGet('height'));
	
	if ($width >= 50 && $width <= 100)
	{
		$Core_Captcha->setConfig('width', $width);
	}

	if ($height >= 10 && $height <= 50)
	{
		$Core_Captcha->setConfig('height', $height);
	}

	$Core_Captcha->build($captchaId);
}