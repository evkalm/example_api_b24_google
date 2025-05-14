<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmStagehistory extends B24WebHookBase {

	static protected $pre_method = 'crm.stagehistory.';

	// История движения по стадиям
	public static function list($filter, $full_data = false) {
		$url = self::whUrl('list');
		$res = self::executeHook($url, $filter);
		
		if ($full_data) return $res;
			else return $res['result']['items'];

		// Пример $filter
		// $filter = [
		// 	'entityTypeId'	=> 2,	// Значение $entityTypeId см. здесь: https://dev.1c-bitrix.ru/rest_help/crm/constants.php
		// 	'order'			=> [ 'ID' => 'ASC' ],	// ASC или DESC
		// 	'filter'		=> [ '>ID' => 0 ],
		// 	'select'		=> [ 'ID', 'STAGE_ID', 'CREATED_TIME' ],
		// 	'start'			=> 0
		// ];
	}

	public static function listBatch($filter, $with_tech_data = false) {
		$res = self::batchForListMethod('crm.stagehistory.list', $filter, $with_tech_data);
		return $res;
	}
	
}

