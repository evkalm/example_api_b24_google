<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\rpa;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookRpaItem extends B24WebHookBase {

	static protected $pre_method = 'rpa.item.';


	// ПОЛУЧАЕМ ИНФОРМАЦИЮ ОБ ЭЛЕМЕНТЕ
	public static function get($typeId, $id, $full_data = false) {
		$url = self::whUrl('get');
		$data = [
			'typeId' => $typeId,
			'id' => $id
		];
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result']['item'];
	}

}