<?php 
/**
 * Система управления сайтом HostCMS v. 5.xx
 * 
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, класс для работы с паролями.
 * 
 * Файл: /modules/Kernel/Password.class.php
 * 
 * @author Ell Gree <ellgree@gmx.net>
 * @version 5.x
 */

Class Password
{
	/**
	 * Генерация пароля
	 * 
	 * @param int $len - длина пароля (1-49), по умолчанию 8
	 * @param string $prefix - префикс пароля, только латинские символы (до 10 символов, входит в длину пароля $len)
	 * @param int $fuzzy  - cмазанность (0-10), по умолчанию 3
	 * @return string
	 * 
	 * @access private
	 */
	function GenPassword($len = 8, $prefix = '', $fuzzy = 3)
	{
		return Core_Password::get($len, $prefix, $fuzzy);
	}
}
