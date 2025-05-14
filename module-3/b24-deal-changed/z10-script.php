<?php
// СКРИПТ СРАБАТЫВАЕТ ПРИ ИЗМЕНЕНИИ СДЕЛКИ (стадии и значений "3-х ячеек")

use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDeal;
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDealcategory;
use Adamart_lib\oop\google\sheets\GoogleSheetsFunctions;

// АЛГОРИТМ
// 1. ОПРЕДЕЛЯЕМ ДАННЫЕ ИЗ HTTP ЗАПРОСА И СДЕЛКИ
// 2. ЗАВЕРШАЕМ СКРИПТ, ЕСЛИ СДЕЛКА НЕ ОТНОСИТСЯ К НАШЕЙ ВОРОНКЕ
// 3. ПОЛУЧАЕМ МАПУ СООТВЕТСТВИЯ КОЛОНОК
// 4. ОПРЕДЕЛЯЕМ НАЗВАНИЕ СТАДИИ ИЗМЕНЕННОЙ СДЕЛКИ
// 5. ПОЛУЧАЕМ СУЩЕСТВУЮЩИЕ ЗНАЧЕНИЯ ЯЧЕЕК ИЗ GOOGLE ТАБЛИЦЫ
// 6. ЗАПИСЫВАЕМ ЗНАЧЕНИЯ В GOOGLE ТАБЛИЦУ, ЕСЛИ ОНИ ОТЛИЧАЮТСЯ ОТ Б24


// 1. ОПРЕДЕЛЯЕМ ДАННЫЕ ИЗ ЗАПРОСА И СДЕЛКИ	
	if (!$_POST['deal_id']) {	// было один раз, что $_POST['deal_id']=null, вроде из-за сбоя в Битрикс
		echo json_encode(['success' => true]);
		die;
	}
	$dealId		= $_POST['deal_id'];
	$dealData	= B24WebHookCrmDeal::get($dealId);
	$ssID		= $dealData['UF_CRM_1741207427700'];
	$sheetID	= $dealData['UF_CRM_1741207471689'];

// 2. ЗАВЕРШАЕМ СКРИПТ, ЕСЛИ:
// - сделка не относится к нашей воронке
// - у сделок отсутсвуют ID Google таблицы (прим.: пока согласовали с заказчиком, что обновление данных для "старых" кандидатов не нужна)
	if ( $dealData['CATEGORY_ID'] != CATEGORY_ID || !$ssID || !$sheetID) {
		echo json_encode(['success' => true]);
		die;
	}


// 3. ПОЛУЧАЕМ МАПУ СООТВЕТСТВИЯ КОЛОНОК
	$sheetSettingName = GoogleSheetsFunctions::getSheetName($serviceSheet, SS_SETTING_ID, SHEET_SETTING_ID);
	$fieldMapping = $serviceSheet->spreadsheets_values->get(SS_SETTING_ID, $sheetSettingName)['values'];

	foreach (array_slice($fieldMapping, 1) as $item) {
		if ($item[4] === 'deal_id') {
			break;
		}

		if (isset($item[3]) && $item[3]) {
			$colomnMap[$item[3]] = $item;
		}
	}


// 4. ОПРЕДЕЛЯЕМ НАЗВАНИЕ СТАДИИ ИЗМЕНЕННОЙ СДЕЛКИ
	$stageArr = B24WebHookCrmDealcategory::stageList(CATEGORY_ID);
	foreach ($stageArr as $item) {
		if ($item['STATUS_ID'] === $dealData['STAGE_ID']) {
			$B24_DATA['stageName'] = $item['NAME'];
			break;
		}
	}


// 5. ПОЛУЧАЕМ СУЩЕСТВУЮЩИЕ ЗНАЧЕНИЯ ЯЧЕЕК ИЗ GOOGLE ТАБЛИЦЫ
	$sheetName = GoogleSheetsFunctions::getSheetName($serviceSheet, $ssID, $sheetID);
	$sheetData = $serviceSheet->spreadsheets_values->get($ssID, $sheetName)['values'];

	$dealIdIndex = array_search('DEAL_ID', $sheetData[0]);		// индекс колонки DEAL_ID

	$googleRowData	= null;
	$rowNum			= null;
	foreach ($sheetData as $rowIdx => $rowData) {
		if (isset($rowData[$dealIdIndex]) && $rowData[$dealIdIndex] == $dealId) {
			$googleRowData	= $rowData;
			$rowNum			= $rowIdx + 1;
			break;
		}
	}
	if (!$googleRowData) {
		echo json_encode(['success' => true]);
		die;
	}

// 6. ЗАПИСЫВАЕМ ЗНАЧЕНИЯ В GOOGLE ТАБЛИЦУ, ЕСЛИ ОНИ ОТЛИЧАЮТСЯ ОТ Б24
	function updateGoogleSheet($b24Value, $googleCellValue, $colLetter, $rowNum) {
		global $serviceSheet, $ssID, $sheetName;

		if ($b24Value != $googleCellValue) {
			$body = new Google\Service\Sheets\ValueRange([
				'values' => [[$b24Value]]
			]);
			$options = ['valueInputOption' => 'RAW'];
			$serviceSheet->spreadsheets_values->update($ssID, $sheetName . '!' . $colLetter . $rowNum, $body, $options);
		}
	}

	// Стадия
	updateGoogleSheet($B24_DATA['stageName'], $googleRowData[$colomnMap['stageName'][0] - 1], $colomnMap['stageName'][1], $rowNum);

	// Другие поля
	$arrFieldsKey = [
		'FIO',
		'sity',
		'age',
		'birthdate',
		'phone',
		'hrDirectorFeedback',
		'ucManagerFeedback'
	];
	foreach ($arrFieldsKey as $key) {
		$b24Value			= $dealData[$colomnMap[$key][4]];
		$googleCellValue	= $googleRowData[$colomnMap[$key][0] - 1];
		$colLetter			= $colomnMap[$key][1];

		if ($key === 'birthdate') {
			$date = new DateTime($b24Value);
			$b24Value = $date->format('d.m.Y');
		}

		if ($key === 'phone') {
			$b24Value = $b24Value[0];
		}

		updateGoogleSheet($b24Value, $googleCellValue, $colLetter, $rowNum);
		sleep(1);
	}


