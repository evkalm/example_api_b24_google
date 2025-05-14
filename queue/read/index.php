<?php
// Точка входа
// http://xxx.ru/clients/xxx/queue/read/index.php?tocken=xxx_cron1min


// 1. Оработка ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Блокировка входа
if (empty($_GET['tocken']) || $_GET['tocken'] !== 'xxx_cron1min') {
	die;
}

// 3. Подключаем функции и пр.
require_once '../../module-3/adamart_lib/functions/debug-functions.php';
require_once 'functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/adamart/general-settings/access_db.php';		// доступы к ДБ
loadEnv('../../module-3/.env');

$startTime = microtime(true);
$connDB = mysqli_connect(MY_SERVER_NAME, DB_USER_NAME, DB_PASSWORD, DB_NAME);
if (!$connDB) die;

// 2. "Прослушиваем" очередь раз в сек., но не дольше 59 сек.
for ($i = 0; $i < 59; $i++) {
	$timeLimit = 59 - (microtime(true) - $startTime);
	if ($timeLimit < 0) {
		exit;
	}

	processQueue($connDB);
	sleep(1);
}

mysqli_close($connDB);

// ВРЕМЯ РАБОТЫ СКРИПТА
// $total_time = number_format(microtime(true) - $startTime, 6, ',', ' ');
// echo "Время выполнения скрипта: " . $total_time . " сек.";



