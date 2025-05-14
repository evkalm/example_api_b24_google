<?php

namespace Adamart_lib\oop\bitrix24;

use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmContact;
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDeal;

// ПОИСК СУЩНОСТЕЙ
abstract class B24findEssenses {

	// ИЩЕМ КОНТАКТ ПО НОМЕРУ ТЕЛЕФОНА
	public static function contactPhone($phone_1) {
		// Алгоритм:
		// 1. Ищем контакт по номеру "+7....." (стандартный с "+")
		// 2. Если не нашли, то ищем по номеру "7....." (без "+")
		// 3. Если не нашли, то ищем по номеру "8....." (Российский, начинающийся на "8")

		$phone_1 = transPhone($phone_1);
		$contact_id = NULL;

		$filter = [
			'FILTER' 	=> [ 'PHONE' => '' ],
			'SELECT' 	=> [ 'ID' ]
		];
	
		if ($phone_1) {		// обязательно делать проверку по NULL, иначе вернет первый контакт
			// 1. Ищем вонтакт по номеру "+7....." (стандартный с "+")
			$filter['FILTER'] = [ 'PHONE' => $phone_1 ];
			$res = B24WebHookCrmContact::list($filter);
		
			if (!empty($res['result'][0])) {
				$contact_id = $res['result'][0]['ID'];
	
			} else {
				// 2. Если не нашли, то ищем по номеру "7....." (без "+")
				$phone_1_izm = str_replace('+7', '7', $phone_1);
				$filter['FILTER'] = [ 'PHONE' => $phone_1_izm ];
				$res = B24WebHookCrmContact::list($filter);
	
				if (!empty($res['result'][0])) {
					$contact_id = $res['result'][0]['ID'];
				} else {
					// 3. Если не нашли, то ищем по номеру "8....." (Российский, начинающийся на "8")
					$phone_1_izm = str_replace('+7', '8', $phone_1);
					$filter['FILTER'] = [ 'PHONE' => $phone_1_izm ];
					$res = B24WebHookCrmContact::list($filter);
	
					if (!empty($res['result'][0])) {
						$contact_id = $res['result'][0]['ID'];
					}
				}
			}
		}

		$contact_id = $contact_id ? $contact_id : NULL;

		return $contact_id;
	}

	// ИЩЕМ КОНТАКТ ПО НОМЕРУ EMAIL
	public static function contactEmail($email_1) {

		$email_1 = str_replace(' ', '', $email_1);
		$contact_id = NULL;

		if ($email_1) { 	// обязательно делать проверку email по NULL, иначе вернет первый контакт

			$filter = [
				'FILTER' 	=> [ 'EMAIL' => $email_1 ],
				'SELECT' 	=> [ 'ID' ]
			];
		
			$res = B24WebHookCrmContact::list($filter);
			if (!empty($res['result'][0])) {
				$contact_id = $res['result'][0]['ID'];
			}

			return $contact_id;
		}
	}


	// ИЩЕМ СДЕЛКИ В РАБОТЕ ПО КОНТАКТУ
	public static function dealsInWorkByContact($contact_id, $stage_id_arr_for_search = ['all']) {

		$deal_id_arr = [];

		if ($contact_id) {
			$filter = [
				'ORDER' 	=> [ 'DATE_CREATE' => 'DESC' ],  // ASC / DESC
				'FILTER' 	=> [ 
					'CONTACT_ID' => $contact_id,
					'STAGE_SEMANTIC_ID' => 'P'
				],
				'SELECT' 	=> [ 'ID' , 'STAGE_ID']
			];
			$res = B24WebHookCrmDeal::list($filter, 1);
			
			if ( !empty($res['result'][0]) ) {
				foreach ( $res['result'] as $item ) {

					if ($stage_id_arr_for_search === ['all']) {
						$deal_id_arr[] = $item['ID'];
					} else {
						if ( in_array($item['STAGE_ID'], $stage_id_arr_for_search) ) {
							$deal_id_arr[] = $item['ID'];
						}
					}
				}
			}
		}

		return $deal_id_arr;
	}
}
