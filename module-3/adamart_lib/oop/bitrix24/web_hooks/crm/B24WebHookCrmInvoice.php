<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmInvoice extends B24WebHookBase {

	static protected $pre_method = 'crm.invoice.';

	// Создаем новый счет
	public static function add($data) {
		$url = self::whUrl('add');
		$res = self::executeHook($url, $data);

		return $res;
	}

	// Пример, сырой, не рабочий

	// $data = [
	// 	'fields' => [
	// 		'STATUS_ID' => 'P',
	// 		'UF_DEAL_ID' => 59,
	// 		// 'UF_COMPANY_ID' => 5,
	// 		'UF_CONTACT_ID'	=> 39,
	// 		'PRODUCT_ROWS' => [
	// 			['ID' => 0, 'PRODUCT_ID' => 533, 'PRODUCT_NAME' => 'Товар 01', 'QUANTITY' => 1, 'PRICE' => 122]
	// 		],
	// 	]
	// ];
}