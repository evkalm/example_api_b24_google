<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmDealcategory extends B24WebHookBase {

	static protected $pre_method = 'crm.dealcategory.';


	// Получаем список стадий воронки
	// устаревший метод
	public static function stageList($stage_id, $full_data = false) {
		$url = self::whUrl('stage.list');
		$data = [ "id" => $stage_id ];
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];
	}
}