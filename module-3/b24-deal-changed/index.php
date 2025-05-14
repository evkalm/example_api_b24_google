<?php
// Точка входа
// http://xxxh.ru/clients/buldogrf/module-3/b24-deal-changed/index.php?tocken=xxx

header('Content-Type: application/json');

// 1. Оработка ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Блокировка входа
if (empty($_GET['tocken']) || $_GET['tocken'] !== 'xxx') {
	die;
}

require_once '../settings.php';
require_once '../../vendor/autoload.php';
require_once '../adamart_lib/error-handler/controller.php';
require_once '../adamart_lib/functions/controller.php';
set_error_handler('myErrorHandler');	// регистрируем нашу функцию обработки ошибок
loadEnv('../.env');


// СОЗДАЕМ СЛУЖБУ Google_Sheet
$client = new Google\Client();
$client->setAuthConfig(GOOGLE_KEY_JSON);
// Области, к которым будет доступ
$client->addScope( 'https://www.googleapis.com/auth/spreadsheets' );
$serviceSheet = new Google\Service\Sheets($client);

// Веб-хук
define('WEB_HOOK_URL',getenv('WEB_HOOK_URL'));

try {
	require_once 'z10-script.php';
	echo json_encode(['success' => true]);
} catch (Throwable $err) {
	handlerExceptionAndErrors($err);
	echo json_encode(['success' => false]);
}