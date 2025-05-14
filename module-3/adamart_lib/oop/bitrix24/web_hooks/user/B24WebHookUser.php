<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\user;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookUser extends B24WebHookBase {

	static protected $pre_method = 'user.';

	// ПОЛУЧАЕМ СПИСОК ПОЛЬЗОВАТЕЛЕЙ (<=50)
	public static function get($filter, $full_data = false) {
		$url = self::whUrl('get');
		$res = self::executeHook($url, $filter);

		if ($full_data) return $res;
			else return $res['result'];
	}
		// Пример $filter
		// $filter = [
		// 	'SORT'		=> 'ID',
		// 	'ORDER' 	=> 'ASC',	// ASC/DESC
		// 	'FILTER' 	=> [ 'USER_TYPE' => 'employee' ]
		// ];


	// ПОЛУЧАЕМ СПИСОК ПОЛЬЗОВАТЕЛЕЙ (>50)
	public static function getBatch($filter, $with_tech_data = false) {
		$res = self::batchForListMethod('user.get', $filter, $with_tech_data);
		return $res;
	}


	// ИЩЕМ СОТРУДНИКА
	public static function search($filter, $full_data = false) {
		$url = self::whUrl('search');
		$res = self::executeHook($url, $filter);

		if ($full_data) return $res;
			else return $res['result'];

		// Пример
		// $filter = [
		// 	'NAME'					=> '',		// имя
		// 	'LAST_NAME'				=> '',		// фамилия
		// 	'WORK_POSITION'			=> '',		// должность
		// 	'UF_DEPARTMENT_NAME'	=> '',		// название подразделения
		// 	'USER_TYPE'				=> '',		// ип пользователя
		// ]
	}
}