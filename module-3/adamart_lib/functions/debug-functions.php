<?php

function loadEnv($file) {
	if (file_exists($file)) {
		$lines = file($file);
		foreach ($lines as $line) {
			$line = trim($line);
			if ($line && $line[0] !== '#') {
				list($key, $value) = explode('=', $line, 2);
				putenv(trim($key) . '=' . trim($value));
			}
		}
	}
}

// Для отладки
function prD($data, $var_dump = false, $title = '') {
	// Выполняем явный показ типов для "невидимых" строчных значений
	if ((is_string($data) && trim($data) === '') || $data === 0 ) {
		$var_dump = true;
	}

	echo '<pre>';
		if (!empty($title)) {
			echo '<strong>' . $title . ' = </strong>';
		}

		if ($var_dump) {
			var_dump($data);
		} else {
			var_export($data);
		}
	echo '</pre>';
}

// Отобразить значение или массив в файле debug.php
function prDbg($arr, $var_dump = false) {
	
	// $arr_json = addslashes(json_encode($arr));
	$arr_json = json_encode($arr);
	$arr_json = str_replace("'", "\'", $arr_json);

	$php_code = '$arr_json=' . "'" . $arr_json ."'" . ';' . PHP_EOL;
	$php_code .= '$arr=json_decode($arr_json, true);' . PHP_EOL;
	$php_code .= 'echo "<pre>";' . PHP_EOL;


	if(is_bool($arr) || $arr === null || $arr === '') $var_dump = true;	// чтобы видеть результат при $var_dump = false

	if ($var_dump) {
		$php_code .= 'var_dump($arr);' . PHP_EOL;
	} else {
		$php_code .= 'print_r($arr);' . PHP_EOL;
	}

	$php_code .= 'echo "</pre>";' . PHP_EOL;

	file_put_contents('debug.php', $php_code, FILE_APPEND);
}

// Для отправки в телеграм
function sendTelegramMessage($message, $chatId = false ) {
	$token = getenv('TELEGRAM_BOT_TOKEN');
	$url = 'https://api.telegram.org/bot' . $token . '/sendMessage';

	if ($chatId === false) {
		$chatId = getenv('TELEGRAM_CHAT_ID');
	}

	$params = [
		'chat_id'	=> $chatId,
		'text'		=> $message
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);

	$response = curl_exec($ch);
	curl_close($ch);

	$result = json_decode($response, true);

	return $result;
}

function prTg($data, $var_dump = false) {
	$token = getenv('TELEGRAM_BOT_TOKEN');
	$chatId = getenv('TELEGRAM_CHAT_ID');
	$url	= 'https://api.telegram.org/bot' . $token . '/sendMessage';

	// Message
		$mess = date('d.m.Y H:i:s') . PHP_EOL;
		if (defined('MODUL_NAME')) {
			$mess .= 'Modul: ' . MODUL_NAME . PHP_EOL;
		}
		if ($var_dump) {
			ob_start();
			var_dump($data);
			$mess .= ob_get_clean();
		} else {
			$mess .= var_export($data, true);
		}
		$mess = strlen($mess) > 4000 ? substr($mess, 0, 4000) : $mess;

	$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL				=> $url,
			CURLOPT_POST			=> 1,
			CURLOPT_RETURNTRANSFER	=> 1,
			CURLOPT_TIMEOUT			=> 10,
			CURLOPT_POSTFIELDS		=> [
				'chat_id'	=> $chatId,
				'text'		=> $mess
			]
		));
		curl_exec($curl);
	curl_close($curl);
}


// Время работы скрипта
// $startTime	= microtime(true);
function prScriptTime($startTime) {
	$total_time = number_format(microtime(true) - $startTime, 6, ',', ' ');
	echo "Время выполнения скрипта: " . $total_time . " сек.";
}