<?php
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDeal;

$dealId = str_replace('D_','', $_POST['deal_id']);

// 1. ПОЛУЧАЕМ ДАННЫЕ СДЕЛКИ
$dealData = B24WebHookCrmDeal::get($dealId);

$FIO			= $dealData['TITLE'];
$sity			= $dealData['UF_CRM_1602589910262'];
$salary			= $dealData['UF_CRM_1668155823'];
$phone_1		= isset($dealData['UF_CRM_5DCB0740A2956'][0]) ? $dealData['UF_CRM_5DCB0740A2956'][0] : '';
$phone_2		= $dealData['UF_CRM_1669635064'];
$age			= $dealData['UF_CRM_1668086553712'];
$schedule		= $dealData['UF_CRM_1605603814503'];
$readyToWork	= $dealData['UF_CRM_1668157150'];
$commentMKA		= $dealData['UF_CRM_1741356382029'];


$license = '';
if (isset($dealData['UF_CRM_1590374924011'][0])) {
	$licenseId = $dealData['UF_CRM_1590374924011'][0];
	foreach (UDOSTOVERENIE_MAP as $key => $val) {
		if ($licenseId == $val ) {
			$license = $key;
		}
	}
}

$regionId		= $dealData['UF_CRM_5EE6C267BE3F6'];
$region = '';
foreach (REGOINS_MAP as $key => $arr) {
	if ($arr['regionIdInDeal'] == $regionId) {
		$region = $key;
	}
}

// 2. ФОРМИРУЕМ СООБЩЕНИЕ
$mess	= 'Новый кандидат!'				. PHP_EOL . PHP_EOL;
$mess	.= 'ФИО: '						. $FIO				. PHP_EOL;
$mess	.= 'Регион: '					. $region			. PHP_EOL;
$mess	.= 'Город: '					. $sity				. PHP_EOL;
$mess	.= 'ЗП в месяц: '				. $salary			. PHP_EOL;
$mess	.= 'Телефон: '					. $phone_1			. PHP_EOL;
$mess	.= 'Доп. телефон: '				. $phone_2			. PHP_EOL;
$mess	.= 'Возраст полных лет: '		. $age				. PHP_EOL;
$mess	.= 'Наличие УЧО: '				. $license			. PHP_EOL;
$mess	.= 'График: '					. $schedule			. PHP_EOL;
$mess	.= 'Готов(а) выйти на работу: '	. $readyToWork		. PHP_EOL;
$mess	.= 'Комментарий от МКА: '		. $commentMKA		. PHP_EOL;


// 3. ОТПРАВЛЯЕМ В ЧАТ ТЕЛЕГРАМ
$botToken	= getenv('TELEGRAM_BOT_TOKEN_TRIUMF');
$chatId		= [];
// $chatId[]	= '451502256'; // мой ТГ чат, для отладки
foreach (REGOINS_MAP as $regionName => $item) {
	if ($item['regionIdInDeal'] == $regionId) {
		$chatId[] = REGOINS_MAP[$regionName]['telegramChatId'];
	}
}

function sendTelegram($botToken, $chatIds, $message) {
	$url = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';

	$successFlag = true;
	foreach ($chatIds as $chat_id) {
		$ch = curl_init();
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_URL				=> $url,
				CURLOPT_POST			=> TRUE,
				CURLOPT_RETURNTRANSFER	=> TRUE,
				CURLOPT_TIMEOUT			=> 10,
				CURLOPT_POSTFIELDS		=> array(
					'chat_id' =>	$chat_id,
					'text' =>		$message,
				),
			)
		);
		$res_json	= curl_exec($ch);
		$res		= json_decode($res_json, true);
		
		if (isset($res['ok']) && $res['ok']) {
		} else {
			// if ($chat_id != 451502256) {
				prTg($res);
			// }
			$successFlag = false;
		}
	}
	return $successFlag;
}

$success = sendTelegram($botToken, $chatId, $mess);
if (!$success) {
	echo json_encode(['success' => false]);
	die;
}



