<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\smart;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookSmartItem extends B24WebHookBase {

	static protected $pre_method = 'crm.item.';
	

	// Получаем массив элементов смарт-процесса
	public static function list($data) {
		$url = self::whUrl('list');
		$send_data = [
			'entityTypeId' => $data['entityTypeId'],
			'select'	=> isset($data['select'])	? $data['select']	: NULL,
			'order'		=> isset($data['order'])	? $data['order']	: NULL,
			'filter'	=> isset($data['filter'])	? $data['filter']	: NULL,
			'start'		=> isset($data['start'])	? $data['start']	: NULL
		];

		$res = self::executeHook($url, $send_data);

		return $res;

		// $data = [
		// 	'entityTypeId' => 0,	// number
		// 	'select'	=> ['*'],	// ["*"], ["title", "id", "uf_*"] - названия полей
		// 	'order'		=> null,	// ASC или DESC
		// 	'filter'	=> null,
		// 	'start'		=> 0		// number
		// ];

	}

	// Получаем данные элемента смарт-процесса
	public static function get($entityTypeId, $id, $full_data = false) {
		$url = self::whUrl('get');
		$data = [
			'entityTypeId' => $entityTypeId,
			'id' => $id
		];
		$res = self::executeHook($url, $data);
		if (isset($res['error'])) {
			throw new \Exception(strMessageExceptoinBitrix($res));
		}
		if ($full_data) return $res;
			else return $res['result']['item'];
	}

	
	// Записываем данные в элементы смарт-процесса
	public static function update($data) {
		$url = self::whUrl('update');
		$res = self::executeHook($url, $data);
		if (isset($res['error'])) {
			throw new \Exception(strMessageExceptoinBitrix($res));
		}
		return $res['result']['item'];
		// return $res;

		// !!!! Вид записи id полей отличается
		// ufCrm5_1677762430823 - правильно, UF_CRM_5_1677762430823 - не правильно
		// $data = [
		// 	'entityTypeId' => 148,
		// 	'id' => 84007,
		// 	'fields' => [
		// 		'ufCrm5_1677762430823' => 'Kia',		// марка
		// 		'ufCrm5_1677762437453' => 'Sportage',	// модель
		// 	]
		// ];
	}

}