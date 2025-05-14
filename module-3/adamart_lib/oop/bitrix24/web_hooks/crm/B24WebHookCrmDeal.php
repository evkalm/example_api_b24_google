<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmDeal extends B24WebHookBase {

	static protected $pre_method = 'crm.deal.';


	// 1. ПОЛУЧАЕМ ДАННЫЕ СДЕЛКИ
	public static function get($deal_id, $full_data = false) {
		$url	= self::whUrl('get');
		$data	= [ "id" => $deal_id ];
		$res	= self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];
	}


	// 2. СОЗДАЕМ СДЕЛКУ
	public static function add($data) {
		$url = self::whUrl('add');
		$res = self::executeHook($url, $data);

		return $res;
		// Пример аргумента
		// $data = [
		// 	'fields' => [
		// 		'TITLE' => 'Тест Тест5',
		// 		'TYPE_ID' => 'SALE',
		// 		'STAGE_ID' => 'C59:NEW',
		// 		'CATEGORY_ID' => 59,
		// 		'LEAD_ID' => '',
		// 		'CONTACT_ID' => '',
		// 		'ASSIGNED_BY_ID' => 3351, // ответственный,
		// 		'CREATED_BY_ID' => 3351	// кем создано
		// 	]
		// ];
	}


	// 3. УДАЛЯЕМ СДЕЛКУ
	// и все связанные с ней объекты: дела, история, дела Таймлайна и другие
	public static function delete($deal_id) {
		$url	= self::whUrl('delete');
		$data	= [ "id" => $deal_id ];
		$res	= self::executeHook($url, $data);

		return $res;
	}


	// 4. ОБНОВЛЯЕМ ДАННЫЕ СДЕЛКИ (протестить еще раз)
	public static function update($data) {
		$url = self::whUrl('update');

		// а) Защита от зацикливания (пока сырой вариант, неизвестно как с файлами работает и массивами)
		// Защита (по идее) не работет, если записываем файл
			$deal_id = isset($data['ID']) ? $data['ID'] : $data['id'];						// чтобы в методе можно было и 'ID' писать и 'id'
			$new_deal_data = isset($data['FIELDS']) ? $data['FIELDS'] : $data['fields'];	// чтобы в методе можно было и 'FIELDS' писать и 'fields'

			
			$exist_deal_data = self::get($deal_id);		// Получаем данные сделки
			$flag_same = true;							// флаг указвающий на одинаковость данных в сущ. сделке и записываемых данных

			// ищем, есть ли хотя бы одно неодинаковое значение, иначе запись не производим
			foreach ($new_deal_data as $key => $val) {
				if (isset($exist_deal_data[$key]) && $exist_deal_data[$key] != $val) {
					$flag_same = false;
					break;
				
				// 2-я стадия проверки - если записываемое поле отсутствует в полученном запросе сущ. сделки
				} else {
					foreach ($new_deal_data as $key => $val) {
						if (!array_key_exists($key, $exist_deal_data)) {
							$flag_same = false;
							break;
						}
					}
				}

			}

		// б) Если хоть одно записываемое значение не одинаковое, то делаем запись
		if (!$flag_same) {
			$res = self::executeHook($url, $data);
		}

		if (isset($res)) return $res;

		// Пример аргумента
		// $data = [
		// 	'ID' => 56983,
		// 	'FIELDS'=>[
		// 		'UF_CRM_1605686903246' => [ 615 ]	// списочное поле
		// 	]
		// ];
	}

	
	// 5. ПОЛУЧАЕМ СПИСОК СДЕЛОК ПО ФИЛЬТРУ (поиск сделок)
		// <50 элементов
		public static function list($filter, $full_data = false) {
			$url = self::whUrl('list');
			$res = self::executeHook($url, $filter);

			if ($full_data) return $res;
				else return $res['result'];

			// Пример $filter
			// $filter = [
			// 	'ORDER' 	=> [ 'DATE_CREATE' => 'ASC' ], // ASC - по умолчанию (по возрастанию) / DESC - по убыванию
			// 	'FILTER' 	=> [ 'PHONE' => '+79912225157' ],
			// 	'SELECT' 	=> [ 'ID' ]
			// 	'SELECT' 	=> [ 'ID', 'NAME', 'LAST_NAME', 'TYPE_ID', 'SOURCE_ID' ]
			// ];
		}
		// <2550 элементов (больше пока не проверял)
		public static function listBatch($filter, $with_tech_data = false) {
			$res = self::batchForListMethod('crm.deal.list', $filter, $with_tech_data);
			return $res;
		}


	// 6. ПОЛУЧАЕМ ПЕРЕЧЕНЬ ТОВАРОВ
	public static function productrowsGet($deal_id, $full_data = false) {
		$url = self::whUrl('productrows.get');
		$data = [ "id" => $deal_id ]; 
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];
		
		// Описание некоторых полей
		// [OWNER_ID] => 145		- id сделки
		// [PRICE] => 418.91		- стоимость 1 шт. для клиента, с учетом % налога и скидки
		// [PRICE_EXCLUSIVE] => 391.5	- стоимость 1 шт. с учетом скидки, но без учета налога
		// [PRICE_NETTO] => 435		- стоимость 1 шт. без учета скидок и налога
		// [PRICE_BRUTTO] => 465.45	- стоимость 1 шт. без учета скидок, но с учетом налога
		// [PRICE_ACCOUNT] => 465.45	- равно [PRICE], в чем отличие - пока не известно ("цена счета")

	}


	// 7. СПИСОК ПОЛЬЗОВАТЕЛЬСКИХ ПОЛЕЙ
	// batch не требуется, возвращает все поля, даже если их >50
	public static function userfieldList($data = NULL, $full_data = false) {
		$url = self::whUrl('userfield.list');
		if ($data) $res = self::executeHook($url, $data);
			else $res = self::executeHook($url);

		if ($full_data) return $res;
			else return $res['result'];

		// Пример. Чтобы получить названия полей
		// $data = [
		// 	'order'		=> ['SORT' => 'ASC'],
		// 	'filter'	=> ['LANG' => 'ru']
		// ];
	}
	

	// 8. ПОЛУЧАЕМ ОПИСАНИЕ (НАЗВАНИЯ) ПОЛЕЙ СДЕЛКИ, В ТОМ ЧИСЛЕ ПОЛЬЗОВАТЕЛЬСКИХ
	public static function fields($full_data = false) {
		$url = self::whUrl('fields');
		$res = self::executeHook($url);

		if ($full_data) return $res;
			else return $res['result'];
	}
}