<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Ядро, методы управления сообщениями центра администрирования.
 *
 * Файл: /modules/Kernel/Message.fns.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */

 /**
 * Функция выводит сообщение об успешной операции
 * @param string $text
 * @package HostCMS 5
 */
function show_message($text)
{
	Core_Message::show($text);
}

/**
 * Функция выводит сообщение об ошибке
 * @param string $text
 * @package HostCMS 5
 */
function show_error_message($text)
{
	Core_Message::show($text, 'error');
}

/**
 * Функция выводит информационное сообщение
 *
 * @param string $text
 * @param string $width
 * @package HostCMS 5
 */
function show_information_message($text, $width='100%')
{
	if (Core::moduleIsActive('typograph'))
	{

		$text = Typograph_Controller::instance()->process($text, true);
	}
	?>
	<table cellspacing="2" cellpadding="2" border="0" class="table_border_message" style="width:<?php echo  $width ?>; font-family: Tahoma, Arial, Verdana, 'MS Sans Serif'; font-size: 8pt; border-collapse: collapse; border: 1px #EEEEEE solid;">
	<tr>
	<td width="30"><img src="/admin/images/attention.gif" border="0" style="margin: 2px"></td>
	<td><?php echo $text;?></td>
	</tr>
	</table>
	<?php
}

/**
 * Функция выводит фатальную ошибку в FrontOffice
 * @param string $title
 * @param string $text
 * @access private
 * @package HostCMS 5
 */
function show_index_message($title = '',$text = '')
{
	$oAdmin_Answer = Core_Skin::instance()->answer();

	$oAdmin_Answer
		->content($text)
		->title($title)
		->execute();
}
