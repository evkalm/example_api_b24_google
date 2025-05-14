<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\catalog;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCatalogProduct extends B24WebHookBase {

	static protected $pre_method = 'catalog.product.';


	// ПОЛУЧАЕМ ДАННЫЕ ТОВАРА ПО ЕГО ID
	public static function get($product_id, $full_data = false) {
		$url = self::whUrl('get');
		$data = [ "id" => $product_id ];
		$res = self::executeHook($url, $data);
		if (isset($res['error'])) {
			throw new \Exception(strMessageExceptoinBitrix($res));
		}

		if ($full_data) return $res;
			else return $res['result'];
	}


	// ПОЛУЧАЕМ СПИСОК ТОВАРОВ КАТАЛОГА ПО ФИЛЬТРУ
	public static function list($filter) {
		$res = self::batchForListMethod('catalog.product.list', $filter);
		return $res;
		// Пример
		// $filter = [
		// 	'select' => ['id', 'iblockId','*'],		// id, iblockId - указывать обязательно
		// 	'filter' => [
		// 		'iblockId' => 15
		// 	],
		// 	'order' => [
		// 		'id' => 'ASC'
		// 	],
		// 	'start' => 1
		// ];

	}



}