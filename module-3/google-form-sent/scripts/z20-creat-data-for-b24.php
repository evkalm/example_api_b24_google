<?php
// ФОРМИРУЕМ ДАННЫЕ ДЛЯ ЗАПИСИ В СДЕЛКУ

use Adamart_lib\oop\google\sheets\GoogleSheetsFunctions;
use Adamart_lib\oop\bitrix24\web_hooks\portal\B24WebHookPortalUser;
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDealcategory;


// 1. ЗАПИСЫВАЕМ ЯВНЫЕ ДАННЫЕ
$DATA['newDealData'] = [
	'FIELDS' => [ 
		'CATEGORY_ID'			=> CATEGORY_ID,
		'UF_CRM_1741207427700'	=> $DATA['googleData']['ssID'],
		'UF_CRM_1741207471689'	=> $DATA['googleData']['sheetID']
	]
];
$DATA['candidateData'] = [];

// 2. ПОЛУЧАЕМ СООТВЕТСТВИЯ ПОЛЕЙ ИЗ "НАСТРОЕЧНОЙ" ТАБЛИЦЫ
$sheetSettingName = GoogleSheetsFunctions::getSheetName($serviceSheet, SS_SETTING_ID, SHEET_SETTING_ID);
$fieldMapping = $serviceSheet->spreadsheets_values->get(SS_SETTING_ID, $sheetSettingName)['values'];

// 3. ЗАПИСЫВАЕМ "СЫРЫЕ" ДАННЫЕ ИЗ ГУГЛ ТАБЛИЦЫ
foreach (array_slice($fieldMapping, 1) as $item) {	// первый элемент массива удаляем
	if ($item[4] === 'deal_id') {
		$DATA['googleData']['colLetterDealID'] = $item[1];
		break;
	}

	$b24FieldID = trim($item[4]);
	if ($b24FieldID) {
		$val = isset($DATA['googleData']['rowValues'][$item[0]-1]) ? $DATA['googleData']['rowValues'][$item[0]-1] : '';
		$DATA['newDealData']['FIELDS'][$b24FieldID] = $val;
	}
}

// 4. ДЕЛАЕМ ПРЕОБРАЗОВАНИЕ НЕОБХОДИМЫХ ДАННЫХ
foreach ($DATA['newDealData']['FIELDS'] as $fieldID => $val_i) {
	switch ($fieldID) {
		case 'UF_CRM_5EE6C267BE3F6':	// Регион
			$DATA['newDealData']['FIELDS'][$fieldID]	= REGOINS_MAP[$val_i]['regionIdInDeal'];
			$DATA['candidateData']['region']			= $val_i;
			break;

		case 'UF_CRM_1668153598363':	// время заполнения гугл формы
			// $DATA['newDealData']['FIELDS'][$fieldID] = $val_i . ' (по МСК)';
			break;

		case 'STAGE_ID':
			$stageName = $DATA['newDealData']['FIELDS'][$fieldID];
			$DATA['newDealData']['FIELDS'][$fieldID] = 'C15:13';	// по умолчанию "Новые заявки"
			
			$stageArr = B24WebHookCrmDealcategory::stageList(CATEGORY_ID);
			foreach ($stageArr as $item) {
				if ($item['NAME'] === $stageName) {
					$DATA['newDealData']['FIELDS'][$fieldID] = $item['STATUS_ID'];
				}
			}
			break;
		
		case 'ASSIGNED_BY_ID':		// ответственный
			$DATA['newDealData']['FIELDS'][$fieldID] = 301;		// по умолчанию Мария Сергеевна Морозова

			if ($val_i) {
				$fio = explodeFIO($val_i); // Сохраняем результат в переменной
				$filter = [
					'FILTER' => [
						'NAME'		=> $fio['NAME'],		// имя
						'LAST_NAME'	=> $fio['LAST_NAME']	// фамилия
					]
				];

				$findedUsers = B24WebHookPortalUser::searchList($filter);
				if (!empty($findedUsers)) {
					$DATA['newDealData']['FIELDS'][$fieldID] = $findedUsers[0]['ID'];
				}
			}
			break;

		case 'UF_CRM_1636371896':		// Ответственный НОТ + Ответственный НОТ (дублирование) UF_CRM_1668159978
			if ($val_i) {
				// Поле "Ответственный НОТ (дублирование)"
				$DATA['newDealData']['FIELDS']['UF_CRM_1668159978'] = $val_i;

				// Поле "Ответственный НОТ"
				$filter = [
					'FILTER' => [
						'NAME'		=> explodeFIO($val_i)['NAME'],			// имя
						'LAST_NAME'	=> explodeFIO($val_i)['LAST_NAME']		// фамилия
					]
				];
				$findedUsers = B24WebHookPortalUser::searchList($filter);
				if (!empty($findedUsers)) {
					$DATA['newDealData']['FIELDS'][$fieldID] = [$findedUsers[0]['ID']];
				} else {
					$DATA['newDealData']['FIELDS'][$fieldID] = null;
				}
			} else {
				$DATA['newDealData']['FIELDS'][$fieldID] = null;
			}
			break;
		
		case 'UF_CRM_1605686903246':		// мн. список, Как искали работу?
			$arr = explode(', ', $val_i);
			$DATA['newDealData']['FIELDS'][$fieldID] = [];
			foreach ($arr as $item) {
				if (isset(HOW_SEARCH_JOB_MAP[$item])) {
					$DATA['newDealData']['FIELDS'][$fieldID][] = HOW_SEARCH_JOB_MAP[$item];
				}
			}
			break;
		
		case 'UF_CRM_1590374924011':		// мн. список, но в форме единиичный, Удостоверение частного охранника
			$DATA['newDealData']['FIELDS'][$fieldID] = null;
			if (isset(UDOSTOVERENIE_MAP[$val_i])) {
				$DATA['newDealData']['FIELDS'][$fieldID] = [UDOSTOVERENIE_MAP[$val_i]];
			}
			break;

		case 'UF_CRM_1590375134815':	// ед. список, Ваше полное образование?
			$DATA['newDealData']['FIELDS'][$fieldID] = null;
			if (isset(EDUCATION_MAP[$val_i])) {
				$DATA['newDealData']['FIELDS'][$fieldID] = [EDUCATION_MAP[$val_i]];
			}
			break;

		case 'UF_CRM_1613456291':	// ФИО/Название
			$DATA['newDealData']['FIELDS']['TITLE']		= $val_i;
			$DATA['newDealData']['FIELDS'][$fieldID]	= $val_i;
			$DATA['candidateData']['name']				= explodeFIO($val_i)['NAME'];				// имя
			$DATA['candidateData']['lastName']			= explodeFIO($val_i)['LAST_NAME'];			// фамилия
			$DATA['candidateData']['secondName']		= explodeFIO($val_i)['SECOND_NAME'];		// отчество
			break;

		case 'UF_CRM_5DCB0740A2956':	// телефон 1
			$phone_1	= transPhone($val_i);			// в формате "+7..."
			$phone7_1	= substr($phone_1, 1);			// в формате "7..."
			$phone8_1	= '8' . substr($phone_1, 2);	// в формате "8..."

			$DATA['newDealData']['FIELDS'][$fieldID]	= [$phone_1];
			$DATA['candidateData']['phone_1']			= $phone_1;
			$DATA['candidateData']['phone7_1']			= $phone7_1;
			$DATA['candidateData']['phone8_1']			= $phone8_1;
			break;

		case 'UF_CRM_1621825011676':
			$DATA['candidateData']['email'] = validateEmail($val_i) ? $val_i : null;
			break;

		case 'UF_CRM_1613455202':		// дата рождения
			$DATA['candidateData']['birthdate'] = $val_i;
			break;
		
		default:
			break;
	}
}
// prD($DATA); die;
