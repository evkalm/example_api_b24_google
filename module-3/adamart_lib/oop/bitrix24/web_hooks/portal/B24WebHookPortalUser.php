<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\portal;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookPortalUser extends B24WebHookBase {

	static protected $pre_method = 'user.';


	// ПОИСК СОТРУДНИКОВ ПО ФИЛЬТРУ
	public static function getList($filter, $full_data = false) {
		$url = self::whUrl('get');
		$res = self::executeHook($url, $filter);

		if ($full_data) return $res;
			else return $res['result'];

		// $filter = [
		// 	'SORT' => 'ID',
		// 	'SORT' => 'LAST_NAME',
		// 	'ORDER' => 'ASC',	// ASC/DESC'
		// 	'FILTER' => [
		// 		'IS_ONLINE' => 'Y',
		// 		'ACTIVE' => 1,
		// 		'>ID' => 83,
		// 		'>=LAST_LOGIN' => '30.03.2023 00:00:00'
		// 	],
		// 	// 'SELECT' => ['LAST_NAME'],	// SELECT не работает
		// ];
	}


	// УСКОРЕННЫЙ ПОИСК СОТРУДНИКОВ ПО ПЕРСОНАЛЬНЫМ ДАННЫМ
	public static function searchList($filter, $full_data = false) {
		$url = self::whUrl('search');
		$res = self::executeHook($url, $filter);

		if ($full_data) return $res;
			else return $res['result'];

		// Пример $filter
		// $filter = [
		//	'ID' => 83
		//	'>ID' => 83
		// ];
	}


	// ПОЛУЧАЕМ СПИСОК ДОСТУПНЫХ ПОЛЕЙ
	// прим.: не все поля "отдают" данные. Например "LAST_ACTIVITY_DATE" работает только для коробки (как сказала тех. поддержка)
	public static function fields($full_data = false) {
		$url = self::whUrl('fields');
		$res = self::executeHook($url);
		
		if ($full_data) return $res;
			else return $res['result'];
	}

}