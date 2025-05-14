<?php

namespace Adamart_lib\oop\bitrix24;

// ФУНКЦИИ ДЛЯ БИТРИКС24
abstract class B24Functions {

	// ОПРЕДЕЛЯЕМ СПИСОК ЗНАЧЕНИЙ ПОЛЬЗОВАТЕЛЬСКОГО ENUM ПОЛЯ ДЛЯ СДЕЛОК В ВИДЕ МАССИВА "ENUM_ID" =>"ENUM_NAME"
	public static function listEnumUserfieldDealAsIdName($all_userfield_list, $userfield_id) {

		// Находим массив данных для заданного поля
		foreach ($all_userfield_list as $item) {
			if ($item['FIELD_NAME'] === $userfield_id) $enum_userfield_data = $item;
		}

		$enum_userfield_id_name = [];
		foreach ($enum_userfield_data['LIST'] as $item) {
			$enum_userfield_id_name[ $item['ID'] ] = $item['VALUE'];
		}

		return $enum_userfield_id_name;
	}



// ОПРЕДЕЛЯЕМ СПИСОК ЗНАЧЕНИЙ ПОЛЬЗОВАТЕЛЬСКОГО ENUM ПОЛЯ ДЛЯ СДЕЛОК В ВИДЕ МАССИВА "ENUM_NAME" => "ENUM_ID"
	public static function listEnumUserfieldDealAsNameId($all_userfield_list, $userfield_id) {

		// Находим массив данных для заданного поля
		foreach ($all_userfield_list as $item) {
			if ($item['FIELD_NAME'] === $userfield_id) $enum_userfield_data = $item;
		}

		$enum_userfield_name_id = [];
		foreach ($enum_userfield_data['LIST'] as $item) {
			$enum_userfield_name_id[ $item['VALUE'] ] = $item['ID'];
		}

		return $enum_userfield_name_id;
	}

}
