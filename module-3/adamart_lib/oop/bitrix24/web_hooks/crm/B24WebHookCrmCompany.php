<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmCompany extends B24WebHookBase {

	static protected $pre_method = 'crm.company.';


	// 1. ПОЛУЧАЕМ ДАННЫЕ КОМПАНИИ
	public static function get($company_id, $full_data = false) {
		$url = self::whUrl('get');
		$data = [ 'ID' => $company_id ];
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];
	}
}