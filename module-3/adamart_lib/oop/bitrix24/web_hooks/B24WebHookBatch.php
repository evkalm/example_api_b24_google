<?php

namespace Adamart_lib\oop\bitrix24\web_hooks;

class B24WebHookBatch extends B24WebHookBase {

	// ПРОСТО МЕТОД BATCH (max 2500 запросов)
	static public function batch($data_for_send) {
		$url = self::$common_web_hook_url . 'batch.json';
		$res = self::executeHook($url, $data_for_send);
		return $res;
	}

	// МЕТОД BATCH ДЛЯ ОДНОГО МЕТОДА И ОДИНАКОВЫХ ПАРАМЕТРОВ В ЗАПРОСЕ
	static public function batchGetForIdArr($method, $id_arr) {
		$res = [];
		$batch_50 = [];		// маленький пакет, состоящий из 50 шт. команд

		// Формируем пакет запросов
		$i = 1;
		foreach ($id_arr as $val) {
			$params = ['id' => $val];
			$batch_50[] = $method . '?' . http_build_query($params);

			if ( $i % 50 === 0 || $i === count($id_arr) ) {

				$data_for_send	= ['cmd' => $batch_50];
				$url = self::$common_web_hook_url . 'batch.json';
				$res_i = self::executeHook($url, $data_for_send)['result']['result'];
				$res = array_merge($res, $res_i);
				$batch_50 = [];
			}
			$i++;
		}

		return $res;
	}

	
	// Сырой метод, используется для элементов <50, только для метода crm.item.update? только для частного случая АМ-сервис
	static public function batch_1($params) {
		$url = self::$common_web_hook_url . 'batch.json';

		// if (count($params) >= 50) throw new \Exception(ErrorMessageForRestB24::resultError('Скрипт остановлен. В массиве более 50 элементов'));
		$cmd_arr = [];
		$i = 1;

		foreach ($params as $item) {
			$params_i = [
				'entityTypeId' => 148,
				'id' => $item[0],
				'fields' => [
					// 'UF_CRM_5_1677762430823' => $item[2],		// марка
					// 'UF_CRM_5_677762437453' => $item[1],	// модель
					
					'ufCrm5_1677762430823' => $item[2],		// марка
					'ufCrm5_1677762437453' => $item[1],	// модель
				]
			];
			$cmd_arr['update_' . $i] = 'crm.item.update' . '?' . http_build_query($params_i);
			$i++;
		}


		// $batch[0]	= $full_method . '?' . http_build_query($params);
			$data		= ['cmd' => $cmd_arr];
			$url		= self::$common_web_hook_url . 'batch.json';
			$res		= self::executeHook($url, $data);

		// return $cmd_arr;
		return $res;

	}
}