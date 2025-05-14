<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmUserfield extends B24WebHookBase {

	static protected $pre_method = 'crm.userfield.';

	// ПОЛУЧАЕМ ОПИСАНИЕ ПОЛЕЙ (техническая информация)
	public static function fields() {
		$url = self::whUrl('fields');
		$res = self::executeHook($url);

		return $res;
	}

	// ПОЛУЧАЕМ ОПИСАНИЕ ПОЛЕЙ ДЛЯ ТИПА "enumeration" (техническая информация)
	public static function enumerationFields() {
		$url = self::whUrl('enumeration.fields');
		$res = self::executeHook($url);

		return $res;
	}

}