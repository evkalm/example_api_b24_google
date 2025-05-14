<?php
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmLead;
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmContact;
use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmDeal;

// ПРОВЕРЯЕМ, СУЩЕСТВУЮТ ЛИ УЖЕ ЛИДЫ, КОНТАКТЫ И СДЕЛКИ
	$DATA['existEssenceData'] = [
		'lead'		=> null,
		'contact'	=> null,
		'deal'		=> null
	];


// 1. ИЩЕМ СУЩ. ЛИДЫ
	$filter = [ 
		'FILTER' => [
			'PHONE' => $DATA['candidateData']['phone_1']
		]
	];
	$findLeads = B24WebHookCrmLead::listBatch($filter);

	if (empty($findLeads)) {
		$filter = [ 
			'FILTER' => [
				'PHONE' => $DATA['candidateData']['phone7_1']
			]
		];
		$findLeads = B24WebHookCrmLead::listBatch($filter);
	}

	if (empty($findLeads)) {
		$filter = [ 
			'FILTER' => [
				'PHONE' => $DATA['candidateData']['phone8_1']
			]
		];
		$findLeads = B24WebHookCrmLead::listBatch($filter);
	}

	$DATA['existEssenceData']['lead'] = !empty($findLeads) ? $findLeads[0] : null;


// 2. ИЩЕМ СУЩ. CONTACT
	// ищем по номеру '+7...', но метод ищет также и по '7...' и по '8...'
	$filter = [
		'FILTER' => [
			'PHONE' => $DATA['candidateData']['phone_1']
		]
	];
	$findContacts = B24WebHookCrmContact::list($filter);
	$DATA['existEssenceData']['contact'] = !empty($findContacts) ? $findContacts[0] : null;


// 3. ИЩЕМ СУЩ. DEAL
	$existDealData = null;
	// 3.1. Ищем сделку по связи с лидом
	// 3.2. Ищем сделку по связи с контактом
	// 3.3. Ищем сделку по телефону, или TITLE

	// 3.1. Ищем сделку по связи с лидом
	if ($DATA['existEssenceData']['lead']) {
		$filter = [
			'FILTER' => [
				'CATEGORY_ID' => CATEGORY_ID,
				'LEAD_ID' => $DATA['existEssenceData']['lead']['ID']
			]
		];
		$boundDeals = B24WebHookCrmDeal::list($filter);
		if (!empty($boundDeals)) {
			$existDealData = $boundDeals[0];
		}
	}

	// 3.2. Ищем сделку по связи с контактом (если не нашли по связи с лидом)
	if (!$existDealData && $DATA['existEssenceData']['contact']) {
		$filter = [
			'FILTER' => [
				'CATEGORY_ID' => CATEGORY_ID,
				'CONTACT_ID' => $DATA['existEssenceData']['contact']['ID']
			]
		];
		$boundDeals = B24WebHookCrmDeal::list($filter);
		if (!empty($boundDeals)) {
			$existDealData = $boundDeals[0];
		}
	}

	// 3.3. Ищем сделку по телефону, или TITLE (если не нашли ни по связи с лидом, ни по связи с контактом)
	if (!$existDealData) {
		// ещем по пользовательскому полю "Телефон" UF_CRM_5DCB0740A2956 - это массив, можно просто указать подстроку телефона не задавая цикл обхода
		$filter = [
			'FILTER' => [
				'CATEGORY_ID' => CATEGORY_ID,
				'UF_CRM_5DCB0740A2956' => substr($DATA['candidateData']['phone_1'], 2)
			]
		];
		$findDeals = B24WebHookCrmDeal::list($filter);
		if (!empty($findDeals)) {
			$existDealData = $findDeals[0];
		}

		// если не нашли, то ищем по названию "+79963831395 - Исходящий звонок"
		if (!$existDealData) {
			$checked_title = $DATA['candidateData']['phone_1'] . ' - Исходящий звонок';
			$filter = [
				'FILTER' => [
					'CATEGORY_ID'	=> CATEGORY_ID,
					'TITLE'			=> $checked_title
				]
			];
			$findDeals = B24WebHookCrmDeal::list($filter);
			if (!empty($findDeals)) {
				$existDealData = $findDeals[0];
			}
		}

		// если не нашли, то ищем по названию "+79963831395  - Входящий звонок"
		if (!$existDealData) {
			$checked_title = $DATA['candidateData']['phone_1'] . ' - Входящий звонок';
			$filter = [
				'FILTER' => [
					'CATEGORY_ID'	=> CATEGORY_ID,
					'TITLE'			=> $checked_title
				]
			];
			$findDeals = B24WebHookCrmDeal::list($filter);
			if (!empty($findDeals)) {
				$existDealData = $findDeals[0];
			}
		}
	}

	if ($existDealData) {
		$DATA['existEssenceData']['deal'] = $existDealData;
	}


