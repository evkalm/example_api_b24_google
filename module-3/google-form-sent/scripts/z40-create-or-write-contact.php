<?php
// СОЗДАЕМ КОНТАКТ ПРИ НЕОБХОДИМОСТИ ИЛИ ЗАПИСЫВАЕМ В НЕГО ДАННЫЕ

// Общий алгоритм:
// 1. Если контакт есть, то дозаисываем/перезаписываем данные в нем
// 2. Если контакт отсутствует, то
// 		- 2.1 если есть лид, то проверяем, не привязан ли к нему какой нибудь контакт
// 		- 2.2 если так, берем за основу этот контакт.
// 		- 2.3 и добавляем новый номер телефона так, чтобы он был первым
// 3. В противном случае, если есть лид и отсутствует в нем контакт, то создаем новый контакт

use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmContact;

// ДАННЫЕ КОНТАКТА
$conactData = [
	'FIELDS' => [
		'NAME'					=> $DATA['candidateData']['name'] . ' ' . $DATA['candidateData']['secondName'],
		'LAST_NAME'				=> $DATA['candidateData']['lastName'],
		'ASSIGNED_BY_ID'		=> $DATA['newDealData']['FIELDS']['ASSIGNED_BY_ID'],
		'UF_CRM_5F7ADC981785D'	=> REGOINS_MAP[$DATA['candidateData']['region']]['regionIdInContact'],
		'UF_CRM_1597378072963'	=> 361,		// поле "Клиенская база" = "Соискатель"
		'BIRTHDATE'				=> $DATA['candidateData']['birthdate']
	]
];
if ($DATA['candidateData']['email']) {
	$conactData['FIELDS']['EMAIL'] = [
		[
			'VALUE'			=> $DATA['candidateData']['email'],
			'VALUE_TYPE'	=> 'HOME'
		]
	];
}

// 1. Если контакт есть, то дозаписываем/перезаписываем данные в нем
if ($DATA['existEssenceData']['contact']) {
	$conactData['ID'] = $DATA['existEssenceData']['contact']['ID'];
	B24WebHookCrmContact::update($conactData);
	$DATA['candidateData']['contactID'] = $DATA['existEssenceData']['contact']['ID'];

} else {
// 2. Если контакт отсутствует, то

	// 2.1 если есть лид, то проверяем, не привязан ли к нему какой-нибудь контакт
	if ($DATA['existEssenceData']['lead'] 
		&& isset($DATA['existEssenceData']['lead']['CONTACT_ID']) 
		&& !empty($DATA['existEssenceData']['lead']['CONTACT_ID'])
	){
		// 2.2 если так, берем за основу этот контакт.
		$conactData['ID'] = $DATA['existEssenceData']['lead']['CONTACT_ID'];
		$DATA['existEssenceData']['contact']['ID'] = $conactData['ID'];

		// 2.3 и добавляем новый номер телефона так, чтобы он был первым
		$oldContactPhone = B24WebHookCrmContact::get($conactData['ID'])['PHONE'][0];
		// стираем старый телефон 
		$data = [
			'ID'		=> $conactData['ID'],
			'FIELDS'	=> [
				'PHONE' => []
			]
		];
		B24WebHookCrmContact::update($data);

		// указываем новый и старый телефоны (новый на 1-е место, старый - на 2-е)
		$conactData['FIELDS']['PHONE'] = [
			['VALUE' => $DATA['candidateData']['phone_1'], 'VALUE_TYPE' => 'WORK'],
			['VALUE' => $oldContactPhone['VALUE'], 'VALUE_TYPE' => 'WORK']
		];

		// 2.4 Записываем в контакт сформированные данные
		B24WebHookCrmContact::update($conactData);

		$DATA['candidateData']['contactID'] = $DATA['existEssenceData']['contact']['ID'];

	} else {
	// 3. В противном случае, если есть лид и отсутствует в нем контакт, то создаем новый контакт
		$conactData['FIELDS']['PHONE'] = [
			['VALUE' => $DATA['candidateData']['phone_1'], 'VALUE_TYPE' => 'WORK']
		];
		$DATA['candidateData']['contactID'] = B24WebHookCrmContact::add($conactData);
	}
}
