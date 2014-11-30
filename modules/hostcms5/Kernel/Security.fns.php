<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */

/**
 * Вызывает stripslashes для всех элементов массива
 *
 * @param array $var
 * @package HostCMS 5
 */
function strips(&$var)
{
	if (is_array($var))
	{
		foreach($var as $k => $v)
		{
			strips($var[$k]);
		}
	}
	else
	{
		$var = stripslashes($var);
	}
}

/**
 * Экранирует потенциально опасные символы для MySQL запросов
 *
 * @param string $value значение
 * @param boolean $remove_xss удалять элементы XSS атак
 * @return string
 * @package HostCMS 5
 */
function quote_smart($value, $remove_xss = false)
{
	return Core_DataBase::instance()->escape($value);
}

/**
 * Синоним функции remove_xss($string)
 *
 * @access private
 * @package HostCMS 5
 */
function xss($string)
{
	return remove_xss($string);
}

/**
 * Функция удаляет потенциально опасные виды CSS (XSS) атак
 * @param string $string
 * @access private
 * @package HostCMS 5
 */
function remove_xss($string)
{
	return $string;
}

/**
 * Проверка правильности электронного адреса
 *
 * @param string $address e-mail адрес
 * @return boolean
 * @package HostCMS 5
 */
function valid_email($address)
{
	return Core_Valid::email($address);
}

/**
 * Функции для работы с UTF-8
 *
 * @param int $num
 * @return string
 * @access private
 * @package HostCMS 5
 */
function code2utf($num)
{
	if ($num < 256)
	return chr($num);

	if ($num < 2048)
	return chr(($num >> 6) + 192) . chr(($num &63) + 128);

	if ($num < 65536)
	return chr(($num >> 12) + 224) . chr((($num >> 6) &63) + 128) . chr(($num &63) + 128);

	if ($num < 2097152)
	return chr(($num >> 18) + 240) . chr((($num >> 12) &63) + 128) . chr((($num >> 6) &63) + 128) . chr(($num &63) + 128);

	return '';
}
/**
 *
 * @param string $str
 * @return string
 * @access private
 * @package HostCMS 5
 */
function encode($str)
{
	return preg_replace('/&#(\\d+);/e', 'code2utf($1)', $str);
}

/**
 * Возвращает текст с печатаемыми символами ASCII кода, а также \n, \r, \t
 *
 * @param string $num
 * @return string
 * @package HostCMS 5
 */
function symbol2ASCII(&$string)
{
	$string = to_str($string);

	if (defined('USE_OLD_symbol2ASCII') && USE_OLD_symbol2ASCII)
	{
		$size = strlen($string);
		$str = '';

		for ($i=0; $i < $size; $i++)
		{
			$ord = ord($string[$i]);
			if (($ord >= 0x20 && $ord <= 0xFF && $ord != 0x98) || $ord == 0x0A || $ord==0x0D || $ord== 0x09)
			{
				$str.= $string[$i];
			}
		}
		return $str;
	}
	else
	{
		// Ест много памяти!
		return preg_replace("/[^\x20-\x97\x99-\xFF\x0A\x0D\x09]/",'',$string);
	}
}

/**
 * @package HostCMS 5
 */
function StripInvalidXml($xml)
{
    return Core_Str::xml($xml);
}

/**
 * Функция преобразует строку для публикации в XML
 *
 * @param string $str
 * @return string
 * @package HostCMS 5
 */
function str_for_xml($str)
{
	return Core_Str::xml(trim($str));
}

/**
 * Функция приведения значений одномерного массива к целому, ключи сохраняются
 *
 * @param array $array
 * @return array
 * @package HostCMS 5
 */
function array_values_to_int($array)
{
	return Core_Array::toInt($array);
}

/**
 * Преобразует текст из KOI8-R в WIN-1251
 *
 * @param string $string исходная строка
 * @return string
 * @package HostCMS 5
 */
function charset_koi_win($string)
{
	$kw = array(128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 140, 141, 142, 143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 162, 184, 164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183,  184, 185, 186, 187, 188, 189, 190, 191, 254, 224, 225, 246, 228, 229, 244, 227, 245, 232, 233, 234, 235, 236, 237, 238, 239, 255, 240, 241, 242, 243, 230, 226, 252, 251, 231, 248, 253, 249, 247, 250, 222, 192, 193, 214, 196, 197, 212, 195, 213, 200, 201, 202, 203, 204, 205, 206, 207, 223, 208, 209, 210, 211, 198, 194, 220, 219, 199, 216, 221, 217, 215, 218);

	$strlen = mb_strlen($string);

	for($i=0; $i < $strlen; $i++)
	{
		$c = ord($string[$i]);


		if ($c >= 128)
		{
			$string[$i] = chr($kw[$c-128]);
		}
	}
	return $string;
}

/**
 * Публикует с помощью JavaScript ссылку на эл.почту, защищенную от индексации ботами
 *
 * @param string $email Электронный адрес
 * @param string $name Текст ссылки
 * @return str HTML-код
 * @package HostCMS 5
 */
function email_encode_js($email, $name)
{
	$email = hexcode_str($email);
	$name = hexcode_str($name);

	$java = $email;

	ob_start();
	?>
	<script type="text/javascript">
	<!--
	document.write ('<a href="maito:' + '<?php echo $email?>' + '"><?php echo $name?>' + '</a>');
	-->
	</script>
	<?php
	$return = ob_get_clean();

	return $return;
}

/**
 * Преобразует строку в hex-последовательность.
 *
 * @param string $str исходная строка
 * @return str преобразованная строка
 * @package HostCMS 5
 */
function hexcode_str($str)
{
	$str = strval($str);

	$strlen = mb_strlen($str);

	$return = '';

	for ($i = 0; $i < $strlen; $i++)
	{
		$code = ord($str{$i});

		if ($code > 191)
		{
			$code = $code + 848;
		}
		$return .= "&#{$code};";
	}

	return $return;
}


/**
 * Получение значения доступа текущего пользователя на данном сайте к модулю
 *
 * @param string $sModuleName путь к модулю
 * @param int $user_type_id идентификатор типа пользователей
 * @param int $site_id идентификатор сайта
 * @access private
 * @package HostCMS 5
 */
function GetUserAccess($sModuleName, $user_type_id, $site_id)
{
	$oUser_Group = Core_Entity::factory('User_Group')->find($user_type_id);

	if (!is_null($oUser_Group->id))
	{
		$oModule = Core_Entity::factory('Module')->getByPath($sModuleName);
		$oSite = Core_Entity::factory('Site', $site_id);

		if (is_null($oModule) || $oModule->active != 1)
		{
			return FALSE;
		}

		$access = $oUser_Group->issetModuleAccess(
			$oModule, $oSite
		);

		if ($access)
		{
			return array('users_access_value' => 1);
		}
	}

	return FALSE;
}


/**
 * Функция приведения аргумента к строковому типу
 *
 * @param mixed $var
 * @return string
 * @package HostCMS 5
 */
function to_str(&$var,$remove_xss=true)
{
	return Core_Type_Conversion::toStr($var);
}

/**
 * Функция приведения аргумента к целочисленному типу
 *
 * @param mixed $var
 * @return int
 * @package HostCMS 5
 */
function to_int(&$var)
{
	return Core_Type_Conversion::toInt($var);
}

/**
 * Функция приведения аргумента к вещественному типу
 *
 * @param mixed $var
 * @return float
 * @package HostCMS 5
 */
function to_float(&$var)
{
	return Core_Type_Conversion::toFloat($var);
}

/**
 * Функция приведения аргумента к логическому типу
 *
 * @param mixed $var
 * @return bool
 * @package HostCMS 5
 */
function to_bool(&$var)
{
	return Core_Type_Conversion::toBool($var);
}

/**
 * Функция приведения аргумента к массиву
 *
 * @param mixed $var
 * @return array
 * @package HostCMS 5
 */
function to_array(&$var)
{
	return Core_Type_Conversion::toArray($var);
}

/**
 * Удаление HTML-тегов вместе с атрибутами
 *
 * @param string $sSource Исходная строка
 * @param string $aAllowedTags Список разрешенных тегов, например, "<b><i><strong>"
 * @param array $aDisabledAttributes Массив запрещенных атрибутов тегов
 * @return string
 * @package HostCMS 5
 */
function strip_tags_attributes($sSource, $aAllowedTags, $aDisabledAttributes = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'))
{
	return Core_Str::stripTags($sSource, $aAllowedTags, $aDisabledAttributes);
}