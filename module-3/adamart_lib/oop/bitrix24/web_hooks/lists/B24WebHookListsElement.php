<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\lists;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookListsElement extends B24WebHookBase {

	static protected $pre_method = 'lists.element.';

	// Получаем список элементов (до 50 шт.)
	public static function get($data, $full_data = false) {
		$url = self::whUrl('get');
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];

		// Пример
		// $data = [
		// 	'IBLOCK_TYPE_ID' => 'lists',
		// 	'IBLOCK_ID'	=> '29'
		// ];
	}

	// Получаем список элементов (до 2550 шт.)
	public static function getBatch($params) {
		$res = self::batchForListMethod('lists.element.get', $params, $with_tech_data = false);
		return $res;
		// Пример
		// $params = [
		// 	'IBLOCK_TYPE_ID' => 'lists',	// тип инфоблока - список
		// 	'IBLOCK_ID'	=> '29',			// id списка
		// 	'filter' => [
		// 		'>ID' => 0
		// 	]
		// ];
	}

}