<?php

return array (
	'default' => array (
		'driver' => 'sendmail',
	),
	'sendmail' => array (
		'driver' => 'sendmail',
	),
	'smtp' => array (
		'driver' => 'smtp',
		'username' => 'address@domain.com',
		'port' => '25', // для SSL порт 465
		'host' => 'smtp.server.com', // для SSL используйте ssl://smtp.gmail.com
		'password' => ''
	)
);