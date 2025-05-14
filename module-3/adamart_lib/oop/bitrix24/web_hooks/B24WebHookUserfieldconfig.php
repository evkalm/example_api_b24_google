<?php
namespace Adamart_lib\oop\bitrix24\web_hooks;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookUserfieldconfig extends B24WebHookBase {

	static protected $pre_method = 'userfieldconfig.';

	// ПОЛУЧАЕМ ДАННЫЕ О НАСТРОЙКАХ ПОЛЬЗОВАТЕЛЬСКОГО ПОЛЯ
	public static function get() {
		$url = self::whUrl('get');
		$res = self::executeHook($url);
		if (isset($res['error'])) {
			throw new \Exception(strMessageExceptoinBitrix($res));
		}
		return $res;
	}

	// ПОЛУЧАЕМ СПИСОК НАСТРОЕК ПОЛЬЗОВАТЕЛЬСКИХ ПОЛЕЙ
	public static function list($filter) {
		$url = self::whUrl('list');
		$res = self::executeHook($url, $filter);
		if (isset($res['error'])) {
			throw new \Exception(strMessageExceptoinBitrix($res));
		}
		return $res;
	}
	// Пример
	// $filter = [
	// 	'moduleId' => 'rpa',
	// 	'select' => [
	// 		'0' => '*',
	// 		'language' => 'ru'
	// 	],
	// 	'order' => [
	// 		'id' => 'DESC'
	// 	],
	// 	'filter' => [
	// 		'multiple' => 'Y'
	// 	]
	// ];
}