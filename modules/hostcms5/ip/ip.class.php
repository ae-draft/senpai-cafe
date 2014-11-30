<?php
/**
 * Система управления сайтом HostCMS v. 5.xx
 *
 * Copyright © 2005-2011 ООО "Хостмэйк" (Hostmake LLC), http://www.hostcms.ru
 *
 * Класс модуля "IP-адреса".
 *
 * Файл: /modules/ip/ip.class.php
 *
 * @package HostCMS 5
 * @author Hostmake LLC
 * @version 5.x
 */
class ip
{
	/**
	* Вставка/обновление информации об IP-адресе
	*
	* @param array $param массив парметров
	* <br/>int $param['ip_id'] - идентификатор обновляемой записи
	* <br/>str $param['ip_ip'] - IP-адрес
	* <br/>int $param['ip_deny_access'] - запретить доступ к сайту, по умолчанию 1
	* <br/>int $param['ip_no_statistic'] - не учитывать в статистике, по умолчанию 1
	* <br/>str $param['ip_comment'] - комментарий
	* <code>
	* <?php
	* $ip = new ip();
	*
	* $param['ip_id'] = '';
	* $param['ip_ip'] = '192.168.0.1';
	*
	* $newip = $ip->InsertIp($param);
	*
	* // Распечатаем результат
	* echo $newip;
	* ?>
	* </code>
	* @return int идентификатор вставленной/обновленной записи
	*/
	function InsertIp($param)
	{
		if (!isset($param['ip_id']) || $param['ip_id'] == 0)
		{
			$param['ip_id'] = NULL;
		}

		if (!isset($param['ip_deny_access']))
		{
			$param['ip_deny_access'] = 1;
		}

		if (!isset($param['ip_no_statistic']))
		{
			$param['ip_no_statistic'] = 1;
		}

		$oIpaddress = Core_Entity::factory('Ipaddress', $param['ip_id']);

		$oIpaddress->ip = $param['ip_ip'];
		$oIpaddress->deny_access = $param['ip_deny_access'];
		$oIpaddress->no_statistic = $param['ip_deny_access'];
		$oIpaddress->comment = $param['ip_comment'];

		if (is_null($param['ip_id']) && isset($param['users_id']) && $param['users_id'])
		{
			$oIpaddress->user_id = $param['users_id'];
		}

		$oIpaddress->save();

		if (class_exists('Cache'))
		{
			$cache = & singleton('Cache');
			$cache_name = 'IP';
			$cache->DeleteCacheItem($cache_name, $param['ip_ip']);
		}

		return $oIpaddress->id;
	}

	/**
	* Получение информации об IP-адресах
	*
	* @param int $ip_id идентификатор IP-адреса, о котором необходимо получить информацию, если false, то получаем информацию обо всех IP-адресах
	* <code>
	* <?php
	* $ip = new ip();
	*
	* $ip_id = 1;
	*
	* $resource = $ip->SelectIp($ip_id);
	*
	* // Распечатаем результат
	* $row = mysql_fetch_assoc($resource);
	*
	* print_r($row);
	* ?>
	* </code>
	* @return resource
	*/
	function SelectIp($ip_id)
	{
		$ip_id = intval($ip_id);

		$queryBuilder = Core_QueryBuilder::select(
				array('id', 'ip_id'),
				array('ip', 'ip_ip'),
				array('deny_access', 'ip_deny_access'),
				array('no_statistic', 'ip_no_statistic'),
				array('comment', 'ip_comment'),
				array('user_id', 'users_id')
			)->from('ipaddresses');

		if ($ip_id)
		{
			$queryBuilder->where('id', '=', $ip_id);
		}

		$queryBuilder->where('deleted', '=', 0);

		return $queryBuilder->execute()->getResult();
	}

	/**
	* Получение информации об IP-адресе
	*
	* @param int $ip_id идентификатор IP-адреса
	* <code>
	* <?php
	* $ip = new ip();
	*
	* $ip_id = 1;
	*
	* $resource = $ip->GetIp($ip_id);
	*
	* // Распечатаем результат
	* print_r($resource);
	* ?>
	* </code>
	* @return mixed массив с данными об IP-адресе или false
	*/
	function GetIp($ip_id)
	{
		$ip_id = intval($ip_id);

		$ipaddress = Core_Entity::factory('Ipaddress', $ip_id);

		if (!is_null($ipaddress->ip))
		{
			return array(
				'ip_id' => $ipaddress->id,
				'ip_ip' => $ipaddress->ip,
				'ip_deny_access' => $ipaddress->deny_access,
				'ip_no_statistic' => $ipaddress->no_statistic,
				'ip_comment' => $ipaddress->comment,
				'users_id' => $ipaddress->user_id
			);
		}

		return FALSE;
	}

	/**
	* Удаление информации об IP-адресе
	*
	* @param int $ip_id идентификатор IP-адреса
	* <code>
	* <?php
	* $ip = new ip();
	*
	* $ip_id = 9;
	*
	* $resource = $ip->DelIp($ip_id);
	*
	* // Распечатаем результат
	* if ($resource)
	* {
	* 	echo 'IP-адрес удален';
	* }
	* else
	* {
	* 	echo 'Ошибка! IP-адрес не удален';
	* }
	* ?>
	* </code>
	* @return resource
	*/
	function DelIp($ip_id)
	{
		Core_Entity::factory('Ipaddress', $ip_id)->markDeleted();

		return TRUE;
	}

	/**
	* Получение информации о запретах для переданного ip-адреса
	*
	* @param string $ip_ip IP-адрес
	* @param array $param ассоциативный массив параметров
	* - bool $param['cache_off'] - если параметр установлен - данные не кэшируются
	* <code>
	* <?php
	* $ip = new ip();
	*
	* $ip_ip = '192.168.0.1';
	*
	* $row = $ip->GetIpInfo($ip_ip);
	*
	* // Распечатаем результат
	* print_r ($row);
	* ?>
	* </code>
	* @return mixed массив с информацией о запретах для IP-адреса, или false, если запретов нет
	*/
	function GetIpInfo($ip_ip, $param = array())
	{
		$param = Core_Type_Conversion::toArray($param);

		/* Проверка на наличие в файловом кэше */
		if (class_exists('Cache') && !isset($param['cache_off']))
		{
			$cache = & singleton('Cache');
			$cache_name = 'IP';

			if ($in_cache = $cache->GetCacheContent($ip_ip, $cache_name))
			{
				return $in_cache['value'];
			}
		}

		$ipaddress = Core_Entity::factory('Ipaddress');
		$ipaddress->queryBuilder()
			->where('ip', '=', $ip_ip)
			->where('deleted', '=', 0)
			->limit(1);

		$ipaddresses = $ipaddress->findAll();

		if (count($ipaddresses) > 0)
		{
			$ipaddress = $ipaddresses[0];
			$result_array = array(
				'ip_id' => $ipaddress->id,
				'ip_ip' => $ipaddress->ip,
				'ip_deny_access' => $ipaddress->deny_access,
				'ip_no_statistic' => $ipaddress->no_statistic,
				'ip_comment' => $ipaddress->comment,
				'users_id' => $ipaddress->user_id
			);
		}
		else
		{
			$result_array = FALSE;
		}

		if (class_exists('Cache'))
		{
			$cache->Insert($ip_ip, $result_array, $cache_name);
		}

		return $result_array;
	}
}
