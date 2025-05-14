<?php
// СОЗДАЕМ ЛИД ПРИ НЕОБХОДИМОСТИ ИЛИ ЗАПИСЫВАЕМ В НЕГО ДАННЫЕ

use Adamart_lib\oop\bitrix24\web_hooks\crm\B24WebHookCrmLead;

// Общий алгоритм:
// 1.Если лид существует, то
// 		- 1.1 если в лиде отсутствует контакт, то прописываем контакт
// 		- 1.2 если в лиде существет контакт, то ничего не делаем
// 2. Иначе, если лид отсутсвует, то
// 		- 2.1 если существует сделка, то ничего не делаем (т.е. не создаем лид, т.к. в этом нет смысла - мы не сможем связать сделку и лид)
// 		- 2.2 если сделка отсутсвет, то создаем лид
// 3. "Запоминаем", с каким лидом "работаем" дальше
// 4. Если есть сделка, то переводим лид в успешный статус (если сделки нет, то лид сам перейдет в успешный статус при конвертации)


// 1.Если лид существует, то
if ($DATA['existEssenceData']['lead']) {
	// 1.1 если в лиде отсутствует контакт, то прописываем контакт
	if (!isset($DATA['existEssenceData']['lead']['CONTACT_ID']) || empty($DATA['existEssenceData']['lead']['CONTACT_ID'])) {
		$leadData = [
			'ID' => $DATA['existEssenceData']['lead']['ID'],
			'FIELDS' => [
				'CONTACT_ID' => $DATA['candidateData']['contactID']
			]
		];
		B24WebHookCrmLead::update($leadData);
	}
	// 1.2 если в лиде существет контакт, то ничего не делаем

	$DATA['candidateData']['leadID'] = $DATA['existEssenceData']['lead']['ID'];

} else {
	// 2. Иначе, если лид отсутсвует 
	$DATA['candidateData']['leadID'] = null;

	// 2.1 если существует сделка, то ничего не делаем (т.е. не создаем лид, т.к. в этом нет смысла - мы не сможем связать сделку и лид)

	// 2.2 если сделка отсутствует, то создаем лид
	if(!$DATA['existEssenceData']['deal']) {
		$leadData = [
			'FIELDS' => [
				'TITLE'			=> $DATA['candidateData']['phone_1'] . ' - заполнение гугл-формы',
				'CONTACT_ID'	=> $DATA['candidateData']['contactID'],
				'PHONE'			=> [ [ 'VALUE' => $DATA['candidateData']['phone_1'], 'VALUE_TYPE' => 'WORK'] ]
			]
		];
		$DATA['candidateData']['leadID'] = B24WebHookCrmLead::add($leadData);
	}
}

// 4. Если есть сделка, то переводим лид в успешный статус (если сделки нет, то лид сам перейдет в успешный статус при конвертации)
if ($DATA['existEssenceData']['deal'] && $DATA['existEssenceData']['lead']) {
	$leadData = [
		'ID' => $DATA['candidateData']['leadID'],
		'FIELDS' => [
			'STATUS_ID' => 'CONVERTED' 	//Качественный лид
		]
	];
	B24WebHookCrmLead::update($leadData);
}
