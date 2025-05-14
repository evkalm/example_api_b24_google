<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmCategory extends B24WebHookBase {

	static protected $pre_method = 'crm.category.';

	// 1. ПОЛУЧАЕМ СПИСОК КАТЕГОРИЙ (ВОРОНОК) УКАЗАННОЙ СУЩНОСТИ
	public static function list($entityTypeId, $full_data = false) {
		$url = self::whUrl('list');
		$data = [ 'entityTypeId' => $entityTypeId ];
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result']['categories'];

		// Значения $entityTypeId:
			// Сделка			2
			// Контакт			3
			// Компания			4
			// Счет (новый)		31
			// для Лид (1), Счет старый (5), Предложение (7), Реквизит (8) - выдает ошибку
			// Более подробнее см. здесь: https://dev.1c-bitrix.ru/rest_help/crm/constants.php
	}
}

