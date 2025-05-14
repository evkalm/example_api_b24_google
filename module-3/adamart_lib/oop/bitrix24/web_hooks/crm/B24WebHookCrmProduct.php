<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmProduct extends B24WebHookBase {

	static protected $pre_method = 'crm.product.';

	// ПОЛУЧИТЬ ДАННЫЕ ПО ТОВАРУ
	public static function get($product_id, $full_data = false) {
		$url	= self::whUrl('get');
		$data	= ['id' => $product_id];
		$res	= self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];
	}


	// ОПИСАНИЕ ПОЛЕЙ
	public static function fields() {
		$url = self::whUrl('fields');
		$res = self::executeHook($url);

		return $res['result'];
	}

	// ПОЛУЧАЕМ СПИСОК ТОВАРОВ ПО ФИЛЬТРУ (crm.product.list)
	public static function listBatch($filter, $with_tech_data = false) {
		$res = self::batchForListMethod('crm.product.list', $filter, $with_tech_data);
		return $res;

		// Пример $filter
		// $filter = [
		// 	'ORDER' 	=> [ 'DATE_CREATE' => 'ASC' ], 
		// 	'FILTER' 	=> [ 
		// 		'CATALOG_ID' => 24,
		// 		'SECTION_ID' => 38
		// 	],
		// 	'SELECT' 	=> [ 'ID', 'NAME', 'PRICE' ]
		// ];
	}
}