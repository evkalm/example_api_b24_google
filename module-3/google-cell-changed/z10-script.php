<?php
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDeal;
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDealcategory;
use Adamart_lib\oop\google\sheets\GoogleSheetsFunctions;

// 1. Данные измененной ячейки
$changedCellData = [
	'colLetter'	=> $_POST['col_letter'],
	'cellValue'	=> $_POST['cell_value'],
	'dealId'	=> (int) $_POST['deal_id']
];

// 2. Данные Сделки
$dealData = B24WebHookCrmDeal::get($changedCellData['dealId']);

if (!isset($dealData['STAGE_ID'])) {	// было один раз некорректный возврат 30.04.2025, вроде из-за сбоя в Битрикс. Для проверки взял просто поле "STAGE_ID"
	echo json_encode(['success' => false]);
	die;
}


// 3. Получаем соответствия полей из "настроечной" таблицы
$sheetSettingName = GoogleSheetsFunctions::getSheetName($serviceSheet, SS_SETTING_ID, SHEET_SETTING_ID);
$fieldMapping = $serviceSheet->spreadsheets_values->get(SS_SETTING_ID, $sheetSettingName)['values'];

// 4. Сравниваем значения из Google таблицы и Б24
$b24Val		= null;
$b24Field	= null;
foreach ($fieldMapping as $item) {
	if ($item[4] === 'deal_id') {
		break;
	}

	if ($item[1] === $changedCellData['colLetter']) {
		$b24Val		= $dealData[$item[4]];
		$b24Field	= $item[4];
	}
}

// 5. Переопределяем название стадии на его ID
if ($b24Field === 'STAGE_ID') {
	$stageArr = B24WebHookCrmDealcategory::stageList(CATEGORY_ID);
	foreach ($stageArr as $item) {
		if ($item['NAME'] === $changedCellData['cellValue']) {
			$changedCellData['cellValue'] = $item['STATUS_ID'];
		}
	}
}

// 6. Делаем изменения в Сделках Б24
if ($b24Val !== null && $changedCellData['cellValue'] !== $b24Val) {
	$dealUpdateData = [
		'ID' => $changedCellData['dealId'],
		'FIELDS'=>[
			$b24Field => $changedCellData['cellValue']
		]
	];


	if ($changedCellData['colLetter'] === 'B') {	// добавляем изменение в поле TITLE
		$dealUpdateData['FIELDS']['TITLE'] = $changedCellData['cellValue'];
	}

	B24WebHookCrmDeal::update($dealUpdateData);
}

