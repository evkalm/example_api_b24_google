<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmDocumentgenerator extends B24WebHookBase {

	static protected $pre_method = 'crm.documentgenerator.';

	// ПОЛУЧАЕМ ШАБЛОН
	public static function templateGet($template_id, $full_data = false) {
		$url = self::whUrl('template.get');
		$data = [ "id" => $template_id ];
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result']['template'];
	}

}