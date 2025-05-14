<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\sonet_group;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

abstract class B24WebHookSonet_group extends B24WebHookBase {

	static protected $pre_method = 'sonet_group.';

	// ПОЛУЧАЕМ МАССИВ ГРУПП


	static public function get($data = [], $full_data = false) {
		$url = self::whUrl('get');
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];

		// Пример аргумента
		// $data = [
		// 	'ORDER' => [
		// 		'NAME'	=> 'ASC',
		// 	],
		// 	'FILTER' => [
		// 		'%NAME'	=> 'Прод',
		// 	],
		// ];
	}
}