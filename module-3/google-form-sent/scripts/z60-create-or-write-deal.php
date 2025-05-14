<?php
// СОЗДАЕМ СДЕЛКУ ПРИ НЕОБХОДИМОСТИ ИЛИ ЗАПИСЫВАЕМ В НЕЕ ДАННЫЕ

use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmLead;
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDeal;

// Общий алгоритм:
// 1. Если сделка отсутсвует и лид существует или создан, то
// 		- 1.1 перед конвертацией лида вытаскиваем его из "Успешного лида" и вставляем в первый статус (иначе лид не сконвертируется)
// 		- 1.2 Заново прописываем контакт в лид
// 		- 1.3 конвертируем лид (в сделку)
// 		- 1.4 находим созданную сделку по лиду
// 		- 1.5 записываем данные в сделку
// 2. Иначе если сделка отсутствует и лид тоже отсутствует, то
// 		- 2.1 создаем сделку
// 3. Иначе, если сделка существует, то перезаписываем в ней данные
// 4. Определяем ID сделки, с которой "работали"


// 1. Если сделка отсутсвует и лид существует или создан, то конвертируем лид
if (!$DATA['existEssenceData']['deal'] && $DATA['candidateData']['leadID']) {
	// 1.1 перед конвертацией лида вытаскиваем его из "Успешного лида" и вставляем в первый статус (иначе лид не сконвертируется). После данного действия теряются связи
	$leadData = [
		'ID' => $DATA['candidateData']['leadID'],
		'FIELDS' => [
			'STATUS_ID' => 'NEW'
		]
	];
	B24WebHookCrmLead::update($leadData);

	// 1.2 Заново прописываем контакт в лид
	$leadData = [
		'ID' => $DATA['candidateData']['leadID'],
		'FIELDS' => [
			'CONTACT_ID' =>$DATA['candidateData']['contactID']
		]
	];
	B24WebHookCrmLead::update($leadData);
	
	// 1.3 конвертируем лид (в сделку). На этом этапе лид автоматически попадает в успешный статус
	$data_BP = [
		'TEMPLATE_ID' => 1717,
		'DOCUMENT_ID' => ['crm', 'CCrmDocumentLead', 'LEAD_' . $DATA['candidateData']['leadID']]
	];
	bizprocWorkflowStart($data_BP);

	// 1.4 находим созданную сделку по лиду
	$filter = [
		'FILTER' => [
			'CATEGORY_ID'	=> CATEGORY_ID,
			'LEAD_ID'		=> $DATA['candidateData']['leadID']
		]
	];
	$DATA['candidateData']['dealID'] = B24WebHookCrmDeal::list($filter)[0]['ID'];

	// 1.5 записываем данные в сделку
	$DATA['newDealData']['ID'] = $DATA['candidateData']['dealID'];
	B24WebHookCrmDeal::update($DATA['newDealData']);


// 2. Иначе если сделка отсуствует и лид тоже отсутствует, создаем сделку
} elseif (!$DATA['existEssenceData']['deal'] && !$DATA['candidateData']['leadID']) {
	$DATA['newDealData']['FIELDS']['CONTACT_ID'] = $DATA['candidateData']['contactID'];
	$DATA['candidateData']['dealID'] = B24WebHookCrmDeal::add($DATA['newDealData']);


// 3. Иначе, если сделка существует, то перезаписываем в ней данные
} elseif ($DATA['existEssenceData']['deal']) {
	// если сделка была на стадии "В работе у НОТ" (C15:UC_TU2E1B), то предварительно передвигаем ее в любую другую стадию, чтобы потом сработал робот на отправку сообщение в телеграм
	$res = B24WebHookCrmDeal::get($DATA['existEssenceData']['deal']['ID']);
	if ($res['STAGE_ID'] === 'C15:UC_TU2E1B') {
		$dealData = [
			'ID' => $DATA['existEssenceData']['deal']['ID'],
			'FIELDS'=>[
				'STAGE_ID' => 'C15:13' // 'Новые заявки'
			]
		];
		B24WebHookCrmDeal::update($dealData);
	}
	// Записываем окончательные данные в сделку
	$DATA['newDealData']['ID'] = $DATA['existEssenceData']['deal']['ID'];
	B24WebHookCrmDeal::update($DATA['newDealData']);

	$DATA['candidateData']['dealID'] = $DATA['existEssenceData']['deal']['ID'];
}

