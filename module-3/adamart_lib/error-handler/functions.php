<?php

	// 1. Подключаем функцию отправки сообщения в Телеграм, если она не подключена
	if ( !function_exists('sendMessageToTelegram') )
		require_once $_SERVER['DOCUMENT_ROOT'] . '/adamart/adamart_lib/functions/telegram.php';
	

	// 2. Текущая дата и время
	function datetimeNow() {
		return date('d.m.Y H:i:s');
	}

	// 3. Находим данные о функции, на которой сработала ошибка
	function filterStackTrace($stack_trace) {
		$excluded_file_keywords		= ['vendor'];
		$excluded_function_keywords	= ['executeHook'];
	
		foreach ($stack_trace as $stack) {
			$is_file_exclude		= false;
			$is_function_exclude	= false;
	
			if (isset($stack['file'])) {
				foreach ($excluded_file_keywords as $keyword) {
					if (strpos($stack['file'], $keyword) !== false) {
						$is_file_exclude = true;
						break;
					}
				}
			}
	
			if (isset($stack['function'])) {
				foreach ($excluded_function_keywords as $keyword) {
					if (strpos($stack['function'], $keyword) !== false) {
						$is_function_exclude = true;
						break;
					}
				}
			}

			if (!$is_file_exclude && !$is_function_exclude) {
				return $stack;
			}
		}
	
		return null;
	}

	// 3. Формируем сообщения для БД, для телеграм (для писем не готово)
	function composeMessage($error_data, $where_to_send) {
		$err_func	= null;
		$err_type	= null;
		$err_mess	= null;
		$err_file	= null;
		$err_line	= null;
		$err_args	= null;

		// "Вытаскиваем" данные об ошибке
			// Если сработал "наш" обработчик ошибок
			if ( is_array($error_data) && isset($error_data['key_ticket']) && $error_data['key_ticket'] === 'myErrorHandler' ) {
				$err_func = $error_data['key_ticket'];
				$err_type = $error_data['err_type'];
				$err_mess = $error_data['err_mess'];
				$err_file = $error_data['err_file'];
				$err_line = $error_data['err_line'];
	
			// если сработал Exception или Error
			} elseif( is_object($error_data) && method_exists($error_data,'getMessage') ) {

				$stack_trace	= $error_data->getTrace();
				$stack_data		= filterStackTrace($stack_trace);

				$err_func = 'Exception или Error';
				$err_type = '';
				$err_mess = $error_data->getMessage();
				$err_file = isset($stack_data['file']) ? $stack_data['file']: '';
				$err_line = isset($stack_data['line']) ? $stack_data['line']: '';
				
				$err_args = 'Отсутствуют';
				if (isset($stack_data['args'])) {
					$err_args = json_encode($stack_data['args'], JSON_PRETTY_PRINT);
				}
				

			} else {
				$err_func = 'это не myErrorHandler, не Exception и не Error';
				$err_type = '';
				$err_mess = 'какая-то не определенная ошибка';
				$err_file = '';
				$err_line = '';
			}


		// Формируем текст сообщения об ошибке
			$mess	= '';
			$file	= str_replace('/var/www/adamartt/data/www/', '', $err_file);

			if ($where_to_send === 'DB') {
				$mess	.= 'Func: "'	. $err_func	. '"' . PHP_EOL;
				$mess	.= 'Type: "'	. $err_type	. '"' . PHP_EOL;
				$mess	.= 'Mess: '		. $err_mess	. PHP_EOL;
				$mess	.= 'File: "'	. $file		. '"' . PHP_EOL;
				$mess	.= 'Line: "'	. $err_line	. '"' . PHP_EOL;
				$mess	.= 'Args: "'	. $err_args	. '"' . PHP_EOL;

				$mess = addslashes($mess);	// экранируем: ', ", \
			}

			if ($where_to_send === 'telegram') {
				$mess	.= date('d.m.Y H:i:s') . PHP_EOL;
				$mess	.= 'Modul: "'	. MODUL_NAME	. '"' . PHP_EOL;
				$mess	.= 'Func: "'	. $err_func		. '"' . PHP_EOL;
				$mess	.= 'Type: "'	. $err_type		. '"' . PHP_EOL;
				$mess	.= 'Mess: '		. $err_mess		. PHP_EOL;
				$mess	.= 'File: "'	. $file			. '"' . PHP_EOL;
				$mess	.= 'Line: "'	. $err_line		. '"' . PHP_EOL;
				$mess	.= 'Args: "'	. $err_args		. '"' . PHP_EOL;
			}

		return $mess;
	}

	


	// 4. Записываем ошибки в БД
	function writeErroreDB($error_message_for_DB ) {
		$connect = mysqli_connect(SERVER_NAME_ERR, USER_NAME_ERR, PASSWORD_ERR, DB_NAME_ERR);
		$module_name = MODUL_NAME;
		$sql = "INSERT INTO all_errors (modul_name, message, time)
			VALUES('{$module_name}', '{$error_message_for_DB}', NOW())";
			
		mysqli_query($connect, $sql);
		mysqli_close($connect);
	}
	
	// 5. "Наш" обработчик ошибок
	function myErrorHandler(int $errNo, string $errMsg, string $file, int $line) {

		// Предопределённые константы - уровни ошибок
		// https://www.php.net/manual/ru/errorfunc.constants.php
		$error_levels = [
			E_ERROR 			=> "E_ERROR",				// фатальная ошибка, set_error_handler() не отрабатывает
			E_WARNING 			=> "E_WARNING",
			E_PARSE 			=> "E_PARSE",				// фатальная ошибка, set_error_handler() не отрабатывает
			E_NOTICE 			=> "E_NOTICE",
			E_CORE_ERROR 		=> "E_CORE_ERROR",			// фатальная ошибка, set_error_handler() не отрабатывает
			E_CORE_WARNING 		=> "E_CORE_WARNING",		// не фатальная ошибка, set_error_handler() не отрабатывает
			E_COMPILE_ERROR 	=> "E_COMPILE_ERROR",		// фатальная ошибка, set_error_handler() не отрабатывает
			E_COMPILE_WARNING 	=> "E_COMPILE_WARNING",		// не фатальная ошибка, set_error_handler() не отрабатывает
			E_USER_ERROR 		=> "E_USER_ERROR",
			E_USER_WARNING 		=> "E_USER_WARNING",
			E_USER_NOTICE 		=> "E_USER_NOTICE",
			E_STRICT 			=> "E_STRICT",
			E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
			E_DEPRECATED 		=> "E_DEPRECATED",
			E_USER_DEPRECATED 	=> "E_USER_DEPRECATED",
			E_ALL 				=> "E_ALL"
		];

		$error_data = [
			'key_ticket'=> 'myErrorHandler',
			'err_type' 	=> $error_levels[$errNo],
			'err_mess' 	=> $errMsg,
			'err_file' 	=> $file,
			'err_line' 	=> $line
		];


		// Записываем ошибку в БД
			$error_message_for_DB = composeMessage($error_data, 'DB');
			writeErroreDB($error_message_for_DB );

		// Отправляем ошибку в телеграм
			$error_message_for_telegram = composeMessage($error_data, 'telegram');
			sendMessageToTelegram(
				ERROR_TELEGRAM_TOKEN,
				ERROR_TELEGRAM_CHATID,
				$error_message_for_telegram
			);
		
		return false;	// false - включаем обработку ошибки внутренним обработчиком PHP, true - отключаем обработчик PHP
	}


	// 6. Обработчик Throwable (класс Errors) и исключения (класс Exceptions))	
	function handlerExceptionAndErrors($err) {

		// Записываем ошибку в БД
		$error_message_for_DB = composeMessage($err, 'DB');
		writeErroreDB($error_message_for_DB );

		// Отправляем ошибку в телеграм
		$error_message_for_telegram = composeMessage($err, 'telegram');

		$telegram_chatid = defined('ERROR_TELEGRAM_CHATID_FOR_THIS_PROJECT') ? ERROR_TELEGRAM_CHATID_FOR_THIS_PROJECT : ERROR_TELEGRAM_CHATID;
		
		sendMessageToTelegram(
			ERROR_TELEGRAM_TOKEN,
			$telegram_chatid,
			$error_message_for_telegram
		);
	}


