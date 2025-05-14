<?php

// Точка входа:
// https://xxx.ru/clients/xxx/queue/write/index.php?tocken=xxxY&action=any

// 1. Оработка ошибок
error_reporting(E_ALL);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

// 2. Блокировка входа
if ( empty($_GET['tocken']) || $_GET['tocken'] !== 'xxx' || !isset($_GET['action']) ) {
	die;
}

// 3. Подключаем функции и пр.
require_once '../../module-3/adamart_lib/functions/debug-functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/adamart/general-settings/access_db.php';		// доступы к ДБ
loadEnv('../../module-3/.env');

// 4. Данные для записи
$taskName = $_GET['action'];

// Заменяем перенос на строку символом "/" для полей Б24
if (isset($_POST['cell_value'])) {
	$_POST['cell_value'] = str_replace(array("\r\n", "\r", "\n"), '/', $_POST['cell_value'] );
}

$jsonTaskData	= json_encode(array_merge($_GET, $_POST), JSON_UNESCAPED_UNICODE);

// 5. Запись в очередь
try {
	$connDB = mysqli_connect(MY_SERVER_NAME, DB_USER_NAME, DB_PASSWORD, DB_NAME);
	if ($connDB) {
		$curTime = date("Y-m-d H:i:s");
		$stmt = mysqli_prepare($connDB, "
			INSERT INTO queue_triumf (task_name, task_data, status, create_at, create_at_str)
			VALUES (?, ?, 'pending', NOW(), ?)
		");
		mysqli_stmt_bind_param($stmt, "sss", $taskName, $jsonTaskData, $curTime);
		mysqli_stmt_execute($stmt);
		mysqli_close($connDB);
	} else {
		throw new Exception("Нет доступа к БД (табл. 'general_queue'). Запись в очередь не выполнена. Данные: $data_json", 1);
	}

} catch (Throwable $err) {
	$message = "Exception: " . $err->getMessage() . " in " . $err->getFile() . " on line " . $err->getLine();
	sendTelegramMessage($message);
}

