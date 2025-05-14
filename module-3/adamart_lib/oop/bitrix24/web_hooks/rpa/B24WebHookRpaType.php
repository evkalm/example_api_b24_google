<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\rpa;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookRpaType extends B24WebHookBase {

	static protected $pre_method = 'rpa.type.';


	// ПОЛУЧАЕМ МАССИВ ПРОЦЕССОВ С ПОЛЯМИ
	public static function list($filter, $full_data = false) {
		$url = self::whUrl('list');
		$res = self::executeHook($url, $filter);

		if ($full_data) return $res;
			else return $res['result']['types'];

		// Примеры
			// получить все процессы со всеми полями
			// $filter = [];

			// с фильтрацией
			// $filter = [
			// 	'filter' => [
			// 		'>id' => 3
			// 	],
			// 	'order'		=> [
			// 		'id' => 'DESC',	// ASC/DESC'
			// 	],
			// 	'select'	=> ['id', 'title'],
			// 	'start'		=> 0
			// ];
	}



}