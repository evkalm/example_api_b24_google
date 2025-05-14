<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmStatus extends B24WebHookBase {

	static protected $pre_method = 'crm.status.';

	// ВЫгрузка стадий, статусов и пр.
	// не требует применение batch, т.к. выгружет все элементы, даже если их больше 50
	public static function list($filter, $full_data = false) {

		$url = self::whUrl('list');
		$res = self::executeHook($url, $filter);

		if ($full_data) return $res;
			else return $res['result'];

		// Пример $filter
		// $filter = [
		// 	'order'			=> [ 'ID' => 'ASC' ],			// ASC или DESC
		// 	'filter'		=> [ 'ENTITY_ID' => 'STATUS'],	// стадии сделок
		// ];
	}
}

