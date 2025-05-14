<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\lists;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookLists extends B24WebHookBase {

	static protected $pre_method = 'lists.';

	// Получаем данные инфоблока
	public static function get($data) {
		$url = self::whUrl('get');
		$res = self::executeHook($url, $data);

		return $res;

		// Пример
		// $data = [
		// 	'IBLOCK_TYPE_ID' => 'lists'
		// ];
	}

}