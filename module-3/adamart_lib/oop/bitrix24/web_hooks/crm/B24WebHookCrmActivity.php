<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmActivity extends B24WebHookBase {

	static protected $pre_method = 'crm.activity.';

	// ПОЛУЧАЕМ ДАННЫЕ ДЕЛА
	public static function get($deal_id, $full_data = false) {
		$url	= self::whUrl('get');
		$data	= [ "id" => $deal_id ];
		$res	= self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];
	}


}