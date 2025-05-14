<?php

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

$startTime = microtime(true);

try {
	$DATA = [
		'googleData' => [],
	];

	require_once 'scripts/z10-get-data-from-google.php';
	require_once 'scripts/z20-creat-data-for-b24.php';
	require_once 'scripts/z30-search-exsist-essences.php';
	require_once 'scripts/z40-create-or-write-contact.php';
	require_once 'scripts/z50-create-or-write-lead.php';
	require_once 'scripts/z60-create-or-write-deal.php';
	require_once 'scripts/z70-write-deal-id-to-sheet.php';

	echo json_encode(['success' => true]);
} catch (Throwable $err) {
	handlerExceptionAndErrors($err);
	echo json_encode(['success' => false]);
}