<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmLead extends B24WebHookBase {

	static protected $pre_method = 'crm.lead.';

	// 1. ПОЛУЧАЕМ ДАННЫЕ ЛИДА
	public static function get($deal_id, $full_data = false) {
		$url	= self::whUrl('get');
		$data	= [ "id" => $deal_id ];
		$res	= self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];
	}

	// 2. СОЗДАЕМ ЛИД
	public static function add($data, $full_data = false) {
		$url = self::whUrl('add');
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];

		return $res;
		// Пример аргумента
		// $data = [
		// 	'fields' => [
		// 		'TITLE'				=> 'ИП Тест111',
		// 		'NAME'				=> 'Тест',
		// 		'SECOND_NAME'		=> 'Тестов',
		// 		'LAST_NAME'			=> 'Тестович',
		// 		'STATUS_ID'			=> 'NEW',
		// 		'OPENED'			=> 'Y',
		// 		'ASSIGNED_BY_ID'	=> 1,
		// 		'CURRENCY_ID'		=> 'USD',
		// 		'OPPORTUNITY'		=> 111,
		// 		'PHONE'				=> [ ['VALUE' => '555888',			'VALUE_TYPE' => 'WORK'] ],
		// 		'WEB'				=> [ ['VALUE' => 'www.mysite.com',	'VALUE_TYPE' => 'WORK'] ]
		// 	]
		// ];
	}

	// 4. ОБНОВЛЯЕМ ДАННЫЕ ЛИДА (переделать метод, это пример для сделки)
	// public static function update($data) {
	// 	$url = self::whUrl('update');

	// 	// а) Защита от зацикливания (пока сырой вариант, неизвестно как с файлами работает и массивами)
	// 	// Защита (по идее) не работет, если записываем файл
	// 		$deal_id = isset($data['ID']) ? $data['ID'] : $data['id'];						// чтобы в методе можно было и 'ID' писать и 'id'
	// 		$new_deal_data = isset($data['FIELDS']) ? $data['FIELDS'] : $data['fields'];	// чтобы в методе можно было и 'FIELDS' писать и 'fields'

			
	// 		$exist_deal_data = self::get($deal_id);		// Получаем данные сделки
	// 		$flag_same = true;							// флаг указвающий на одинаковость данных в сущ. сделке и записываемых данных

	// 		// ищем, есть ли хотя бы одно неодинаковое значение, иначе запись не производим
	// 		foreach ($new_deal_data as $key => $val) {
	// 			if (isset($exist_deal_data[$key]) && $exist_deal_data[$key] != $val) {
	// 				$flag_same = false;
	// 				break;
				
	// 			// 2-я стадия проверки - если записываемое поле отсутствует в полученном запросе сущ. сделки
	// 			} else {
	// 				foreach ($new_deal_data as $key => $val) {
	// 					if (!array_key_exists($key, $exist_deal_data)) {
	// 						$flag_same = false;
	// 						break;
	// 					}
	// 				}
	// 			}

	// 		}

	// 	// б) Если хоть одно записываемое значение не одинаковое, то делаем запись
	// 	if (!$flag_same) {
	// 		$res = self::executeHook($url, $data);
	// 	}

	// 	if (isset($res)) return $res;

	// 	// Пример аргумента
	// 	// $data = [
	// 	// 	'ID' => 56983,
	// 	// 	'FIELDS'=>[
	// 	// 		'UF_CRM_1605686903246' => [ 615 ]	// списочное поле
	// 	// 	]
	// 	// ];
	// }

	public static function update($data) {
		$url = self::whUrl('update');

	// 	// а) Защита от зацикливания (пока сырой вариант, неизвестно как с файлами работает и массивами)
	// 	// Защита (по идее) не работет, если записываем файл
	// 		$deal_id = isset($data['ID']) ? $data['ID'] : $data['id'];						// чтобы в методе можно было и 'ID' писать и 'id'
	// 		$new_deal_data = isset($data['FIELDS']) ? $data['FIELDS'] : $data['fields'];	// чтобы в методе можно было и 'FIELDS' писать и 'fields'

			
	// 		$exist_deal_data = self::get($deal_id);		// Получаем данные сделки
	// 		$flag_same = true;							// флаг указвающий на одинаковость данных в сущ. сделке и записываемых данных

	// 		// ищем, есть ли хотя бы одно неодинаковое значение, иначе запись не производим
	// 		foreach ($new_deal_data as $key => $val) {
	// 			if (isset($exist_deal_data[$key]) && $exist_deal_data[$key] != $val) {
	// 				$flag_same = false;
	// 				break;
				
	// 			// 2-я стадия проверки - если записываемое поле отсутствует в полученном запросе сущ. сделки
	// 			} else {
	// 				foreach ($new_deal_data as $key => $val) {
	// 					if (!array_key_exists($key, $exist_deal_data)) {
	// 						$flag_same = false;
	// 						break;
	// 					}
	// 				}
	// 			}

	// 		}

	// 	// б) Если хоть одно записываемое значение не одинаковое, то делаем запись
	// 	if (!$flag_same) {
			$res = self::executeHook($url, $data);
		// }

	// 	if (isset($res)) 
		return $res;

		// Пример аргумента
		// $data = [
		// 	'ID' => 56983,
		// 	'FIELDS'=>[
		// 		'UF_CRM_1605686903246' => [ 615 ]	// списочное поле
		// 	]
		// ];
	}

	// Получаем лиды, >50 элементов
	public static function listBatch($filter, $with_tech_data = false) {
		$res = self::batchForListMethod('crm.lead.list', $filter, $with_tech_data);
		return $res;
	}
	
	// Пример $filter
	// $filter = [
	// 	'ORDER' 	=> [ 'DATE_CREATE' => 'ASC' ], // ASC/DESC
	// 	'ORDER' => [ 'ID' => 'ASC' ],
	// 	'FILTER' => [
	// 		'CATEGORY_ID' => $COME['category_id'] === 'all' ? null : $COME['category_id'],
	// 		'>ID'	=> 4300
	// 	],
	// 	'SELECT' => $SELECTABLE_FIELDS
	// ];
	

	// ПОЛУЧАЕМ ОПИСАНИЕ ПОЛЕЙ СДЕЛКИ, В ТОМ ЧИСЛЕ ПОЛЬЗОВАТЕЛЬСКИХ
	public static function fields($full_data = false) {
		$url = self::whUrl('fields');
		$res = self::executeHook($url);

		if ($full_data) return $res;
			else return $res['result'];
	}
}