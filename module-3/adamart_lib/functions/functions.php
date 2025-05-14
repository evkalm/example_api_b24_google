<?php

// РАЗБИРАЕМ СТРОКУ НА = [
	// 'LAST_NAME' 		=> 'Фамилия',
	// 'NAME' 			=> 'Имя'
	// 'SECOND_NAME' 	=> 'Отчество'
	// ]
function explodeFIO($fio) {
	// Алгоритм: 
	// ФИО разделяем по пробелам, первое слово - делаем фамилию, второе - имя, третье - отчество
	// Если в ФИО больше 3-х слов, то все слова начиная с 3-го будут в поле Отчествто		

	$fio = trim($fio);
	$fio = preg_replace("/\s+/u", " ", $fio);	// заменяем несколько пробелов на один
	$arr = explode(' ', $fio);

	// Фамилия
	$fio_arr['LAST_NAME'] 	= isset($arr[0]) ? $arr[0] : '';
	// Имя
	$fio_arr['NAME'] 		= isset($arr[1]) ? $arr[1] : '';
	
	// Отчество
	$fio_arr['SECOND_NAME'] = '';
	if ( isset($arr[2]) ) {
		$arr_izm = array_slice($arr, 2);

		foreach ($arr_izm as $item) {
			$fio_arr['SECOND_NAME'] = $fio_arr['SECOND_NAME'] . ' ' . $item;
		}

		$fio_arr['SECOND_NAME'] = trim( $fio_arr['SECOND_NAME'] );
	}
	// printArr(debug_backtrace());
	return $fio_arr;
}

// ПРЕОБРАЗУЕМ НОМЕР ТЕЛЕФОНА В ПРАВИЛЬНЫЙ ВИД
function transPhone($phone_str, $short = false) {
	// $short = false - приводим телефон к виду "+79620000000"
	// $short = true - оставляем только 10 цифр без "+7"
	if ( $phone_str ) {
		$phone_str		= str_replace(' ', '', $phone_str);
		$first_symbol	= mb_substr($phone_str,0,1,'UTF-8');
		$phone			= preg_replace("/[^0-9]/", '', $phone_str);	// оставляем только цифры
		
		// если тел. начинался на "8..." или на "7...", заменяем на "+7..."
		if ($first_symbol  === '8' || $first_symbol  === '7') {
			$phone = substr($phone, 1); 	// удаляем первый символ
			$phone = '+7' . $phone;
		
		// если телефон был со знаком '+'
		// } elseif ($first_symbol === '+') {	// пока не применяем, не известно, нужно ли
	
		// иначе, всегда добавляем "+" в начале номера
		} else {
			$phone = '+' . $phone;
		}
		
		if ($short) $phone = substr($phone, 2); 	// удаляем первые два символа

	} else {
		$phone = null;
	}

	return $phone;
}


// ОПРЕДЕЛЯЕМ БУКВЕННОЕ ОБОЗНАЧЕНИЕ КОЛОНКИ GOOGLE ТАБЛИЦЫ ПО НОМЕРУ КОЛОНКИ
function nameColSheet($num_col) {
	$sheet_col_names_arr = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW' ];

	return $sheet_col_names_arr[$num_col - 1];
}


// ПРЕОБРАЗОВАНИЕ НОМЕРА ТЕЛЕФОНА В ФОРМАТ +7 (945) 111-22-33
function phoneFormatToNormView($phone) {
	$phone = trim($phone);

	$res = preg_replace(
		array(
			'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{3})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
			'/[\+]?([7|8])[-|\s]?(\d{3})[-|\s]?(\d{3})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
			'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
			'/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{2})[-|\s]?(\d{2})[-|\s]?(\d{2})/',
			'/[\+]?([7|8])[-|\s]?\([-|\s]?(\d{4})[-|\s]?\)[-|\s]?(\d{3})[-|\s]?(\d{3})/',
			'/[\+]?([7|8])[-|\s]?(\d{4})[-|\s]?(\d{3})[-|\s]?(\d{3})/'
		),
		array(
			'+7 ($2) $3-$4-$5',
			'+7 ($2) $3-$4-$5',
			'+7 ($2) $3-$4-$5',
			'+7 ($2) $3-$4-$5',
			'+7 ($2) $3-$4',
			'+7 ($2) $3-$4'
		),
		$phone
	);

	return $res;
}
// примеры, что может обработать:
// +7 (495) 1234567
// +8 (495) 123 45 67
// +7(495)123-45-67
// +7(495)1234567
// +7 495 123-45-67
// +8 495 123 45 67
// +7 495 1234567
// +7-495-123-45-67
// 84951234567
// взято из https://snipp.ru/php/phone-format


function validateEmail($email) {
	$email = trim($email);
	
	// Проверяем формат электронной почты
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	} else {
		return false;
	}
}


function bizprocWorkflowStart($data) {
	$url = WEB_HOOK_URL . 'bizproc.workflow.start.json';

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => http_build_query($data),
		CURLOPT_TIMEOUT => 20
	));
	
	$result = curl_exec($curl);
	curl_close($curl);
	
	$result = json_decode($result, true);
	return $result;
	// приемр аргумента
	// $data = [
	// 	'TEMPLATE_ID' => 1717,
	// 	'DOCUMENT_ID' => ['crm', 'CCrmDocumentLead', 'LEAD_777']
	// ];
}
