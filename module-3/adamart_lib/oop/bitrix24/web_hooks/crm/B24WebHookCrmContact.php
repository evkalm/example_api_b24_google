<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmContact extends B24WebHookBase {

	static protected $pre_method = 'crm.contact.';

	// 1. ПОЛУЧАЕМ ДАННЫЕ КОНТАКТА
	public static function get($contact_id, $full_data = false) {
		$url = self::whUrl('get');
		$data = [ 'ID' => $contact_id ];
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];
	}

	// 2. СОЗДАЕМ НОВЫЙ КОНТАКТ
	public static function add($data, $full_data = false) {
		$url = self::whUrl('add');
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];

		// Пример аргумента:
		// $data = [
		// 	'FIELDS'=>[
		// 		'NAME' => 'Тест'
		// 	]
		// ];
		// Примечание: если задаваемые поля ('NAME' и пр.) отсутствуют в карточке контакта, то ошибка выдаваться не будет, равно как и если массив $data вообще будет пустой
	}

	// 3. ЗАПИСЫВАЕМ ДАННЫЕ В КОНТАКТ
	public static function update($data) {
		$url = self::whUrl('update');
		$res = self::executeHook($url, $data);

		return $res;
		// Пример аргумента
		// $data = [
		// 	'ID' => 56983,
		// 	'FIELDS'=>[
		// 		'UF_CRM_1605686903246' => [615] // списочное поле
		// 	]
		// ];
		// Примечание: если задаваемые поля ('NAME' и пр.) отсутствуют в карточке контакта, то ошибка выдаваться не будет, равно как и если массив $data вообще будет пустой
	}

	// 4 ПОЛУЧАЕМ КОНТАКТЫ ПО ФИЛЬТРУ (ПОИСК КОНТАКТОВ)
		// метод выдает не более 50 контактов
		public static function list($filter, $full_data = false) {
			$url = self::whUrl('list');
			$res = self::executeHook($url, $filter);

			if ($full_data) return $res;
				else return $res['result'];

			// Пример $filter
			// $filter = [
			// 	'ORDER' 	=> [ 'DATE_CREATE' => 'ASC' ],
			// 	'FILTER' 	=> [
			// 		'PHONE' => '+79912225157',
			// 		'>ID'	=> 1 
			// 	],
			// 	'SELECT' 	=> [ 'ID' ]
			// 	'SELECT' 	=> [ 'ID', 'NAME', 'LAST_NAME', 'TYPE_ID', 'SOURCE_ID' ]
			// ];
		}

		// метод выдает более 50 контактов
		public static function listBatch($filter, $with_tech_data = false) {
			$res = self::batchForListMethod('crm.contact.list', $filter, $with_tech_data);
			return $res;

			// Пример $filter
			// $filter = [
			// 	'ORDER' 	=> [ 'DATE_CREATE' => 'ASC' ],
			// 	'FILTER' 	=> [
			// 		'PHONE' => '+79912225157',
			// 	'	>ID'	=> 1 
			// 	],
			// 	'SELECT' 	=> [ 'ID' ]
			// 	'SELECT' 	=> [ 'ID', 'NAME', 'LAST_NAME', 'TYPE_ID', 'SOURCE_ID' ]
			// ];
		}

	
	// 5. ПОЛУЧАЕМ ОПИСАНИЕ (НАЗВАНИЯ) ПОЛЕЙ КОНТАКТА, В ТОМ ЧИСЛЕ ПОЛЬЗОВАТЕЛЬСКИХ
	public static function fields($full_data = false) {
		$url = self::whUrl('fields');
		$res = self::executeHook($url);

		if ($full_data) return $res;
			else return $res['result'];

		// результат получаем типа:
			// Array (
			// 	...
			// 	[NAME] => Array(
			// 			[type] => string
			// 			[isRequired] => 1
			// 			[isReadOnly] => 
			// 			[isImmutable] => 
			// 			[isMultiple] => 
			// 			[isDynamic] => 
			// 			[title] => Имя
			// 		)
			// 	},
			// 	[PHONE] => Array(
			// 		[type] => crm_multifield
			// 		[isRequired] => 
			// 		[isReadOnly] => 
			// 		[isImmutable] => 
			// 		[isMultiple] => 1
			// 		[isDynamic] => 
			// 		[title] => Телефон
			// 	)
			// 	[UF_CRM_63DA1F09B3858] => Array(
			// 		[type] => enumeration
			// 		[isRequired] => 
			// 		[isReadOnly] => 
			// 		[isImmutable] => 
			// 		[isMultiple] => 1
			// 		[isDynamic] => 1
			// 		[items] => Array(
			// 				[0] => Array(
			// 						[ID] => 2699
			// 						[VALUE] => Дирекция СУБД
			// 					)
			// 				[1] => Array(
			// 						[ID] => 2701
			// 						[VALUE] => Дирекция отечественной разработки
			// 					)
			// 				[2] => Array(
			// 						[ID] => 2703
			// 						[VALUE] => Проект 1
			// 					)
			// 	)
			// 	...
	}
}