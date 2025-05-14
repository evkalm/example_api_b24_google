<?php

namespace Adamart_lib\oop\bitrix24\web_hooks;

abstract class B24WebHookBase {
	
	static protected $pre_method = '';						// определяем свойство, чтобы не ругалось IDE
	static protected $common_web_hook_url = WEB_HOOK_URL;

	// Переопределяем веб-хук
	public static function setWebHookURL($url) {
		self::$common_web_hook_url = $url;
	}


	// Тестовая функция для проверки подключения библиотеки
	static public function test() {
		echo '<br>Библиотека adamart_lib подключена!';
	}


	// "Строим" URL
	static protected function whUrl($method_name) {
		return self::$common_web_hook_url . static::$pre_method . $method_name . '.json';
	}

	
	// "Строим" запускаем curl
	static public function executeHook($url, $data = [], $return_res = true) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL				=> $url,
			CURLOPT_POST			=> 1,
			CURLOPT_POSTFIELDS		=> http_build_query($data),
			CURLOPT_SSL_VERIFYPEER	=> 0,
			CURLOPT_HEADER			=> 0,
			CURLOPT_RETURNTRANSFER	=> 1,
			CURLOPT_TIMEOUT			=> 20
		));
		
		$res = curl_exec($curl);
		curl_close($curl);

		// Получение HTTP-кода ответа
		$res = json_decode($res, true);
		usleep(100000);
		
		// Отлавливаем ошибки
			if ( isset($res['error']) ) {
				
				$res['B24Method']	= self::defineMethodB24($url);

				if ( isset($data['id']) )	$res['id'] = $data['id'];
				if ( isset($data['ID']) )	$res['id'] = $data['ID'];
				throw new \Exception(strMessageExceptoinBitrix($res));
			}
			
		if ($return_res) return $res;
	}

	// Находим подсторку из URL с названием метода Б24
	static public function defineMethodB24($url) {
		$parts			= parse_url($url);
		$path			= trim($parts['path'], '/');
		$last_slash_pos	= strrpos($path, '/');
		$json_pos		= strpos($path, '.json', $last_slash_pos);
		$result			= substr($path, $last_slash_pos + 1, $json_pos - $last_slash_pos - 1);
		return $result;
	}


	// BATH_2500 МЕТОД ДЛЯ ВСЕХ СПИСОЧНЫХ МЕТОДОВ
	// применять индивидуально с проверкой времени operating, чтобы не получить блокировку метода
	// для считывания данных один BATH_2500 метод тратит примерно 1 сек., что не критично
	// для изменения данных один BATH_2500 метод может тратить до нескольких минут, что крайне критично. Проверять operating!!!
	// Не под все методы настроено. Пока настроено и проверено для методов:
	//	- crm.deal.list
	//	- crm.stagehistory.list

	// Метод еще не проверен для кол-ва более 2500 элементов выборки
	static public function batchForListMethod($full_method, $params, $with_tech_data) {

		// 1. ВЫПОЛНЯЕМ ПЕРВЫЙ ЗАПРОС НА 50 ЭЛЕМЕНТОВ, чтобы получить:
			// - первые 50 элементов
			// - узнать общее кол-во элементов в выборке [result_total]
			$batch[0]		= $full_method . '?' . http_build_query($params);
			$data_for_send	= ['cmd' => $batch];
			$url			= self::$common_web_hook_url . 'batch.json';
			$res_first_50	= self::executeHook($url, $data_for_send);

			$total_found_units = $res_first_50['result']['result_total'][0];
			$res_first_50['data']['total_elem'] = $res_first_50['result']['result_total'][0];

		// 2. ЕСЛИ КОЛИЧЕСТВО ВЫБОРКИ МЕНЬШЕ 50,
			// то возвращаем результат и завершаем скрипт
			if ( $total_found_units < 50 ) {
				if ($with_tech_data) {

					if ($full_method === 'crm.stagehistory.list') {
						$res['result']	= $res_first_50['result']['result'][0]['items'];
					} else {
						$res['result']	= $res_first_50['result']['result'][0];
					}
					
					$res['time']	= $res_first_50['time'];
					$res['finish_minus_start'] = $res_first_50['time']['finish'] - $res_first_50['time']['start'];
					$res['operating']		= $res_first_50['result']['result_time'][0]['operating'];
					$res['total_elem']		= $res_first_50['data']['total_elem'];
					$res['result_error']	= $res_first_50['result']['result_error'];
				} else {
					$res = $res_first_50['result']['result'][0];
				}
				return $res;
			}


		// 3. ЕСЛИ КОЛИЧЕСТВО ВЫБОРКИ БОЛЬШЕ 50, то:
			// Формируем весь перечень batch запросов
			$total_batch_50		= (int) ceil( $total_found_units / 50 );
			for ($i = 0; $i < $total_batch_50; $i++) {
				$params['start'] = 50 * $i;		// получаем стартовый индекс на каждой итерации
				$batches[$i] = $full_method . '?' . http_build_query($params);
			}
			
			// Разбиваем batch запросы по партиям по 50 шт.
			$parties_2500 = array_chunk($batches, 50);

			$k = 0;
			$start_time			= 0;
			$finish_time		= 0;
			$duration_time		= 0;	// продолжительность выполнения скрипта, сек.
			$operating_time		= 0;	// время выполнения запроса к методу данным приложением
			$operating_reset_at	= 0;	// дата расчета сброса operating_time (через 10 мин. после последнего запуска)
			$res_array = [];

			foreach ($parties_2500 as $items) {
				$data_for_send = ['cmd' => $parties_2500[$k]];
				$res_2500 = self::executeHook($url, $data_for_send);

				// Записываем результаты в однородный массив
				// т.к. полученные данные разделены на подмассивы с 50-ю элементами
				$res_raw = $res_2500['result']['result'];
				
				foreach ($res_raw as $arr50) {
					if ($full_method === 'crm.stagehistory.list') {
						foreach ($arr50['items'] as $elem) {
							$res_array[] = $elem;
						}
					} else {
						foreach ($arr50 as $elem) {
							$res_array[] = $elem;
						}
					}
				}

				// "Технические" данные
				if($k === 0) {
					$start_time		= $res_2500['time']['start'];
					$total_elem		= $res_2500['result']['result_total'][0];
					$result_error	= $res_2500['result']['result_error'];
					$operating_time	= $res_2500['result']['result_time'][0]['operating'];
				}
				$finish_time	= $res_2500['time']['finish'];
				$duration_time	+= $res_2500['time']['duration'];
				$operating_reset_at = $res_2500['time']['operating_reset_at'];
				
				$k++;
			}

			// Делаем задержку, в случае приближения operating_reset_at к критичным 480 сек.
			// очень примерная "защита" от блокировки
			if ($operating_time > 240) sleep(10);
			if ($operating_time > 300) sleep(20);
			if ($operating_time > 360) sleep(40);
			if ($operating_time > 420) sleep(80);
			if ($operating_time > 450) sleep(150);


			// Формируем результат на выдачу
			if ($with_tech_data) {
				$res['result'] = $res_array;
				$res['time']['start']		= $start_time;
				$res['time']['finish']		= $finish_time;
				$res['time']['finish_minus_start'] = $finish_time - $start_time;
				$res['time']['duration']	= $duration_time;
				$res['time']['operating']	= $operating_time;
				$res['time']['operating_reset_at']	= $operating_reset_at;
				$res['total_elem']			= $total_elem;
				$res['result_error']		= $result_error;
			} else {
				$res = $res_array;
			}

			return $res;
	}

}