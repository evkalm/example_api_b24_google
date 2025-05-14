<?php

// ПОЛУЧАЕМ ДАНЫЕ ИЗ ГУГЛ ТАБЛИЦЫ
$DATA['googleData']['ssID']			= $_POST['ss_id'];
$DATA['googleData']['sheetID']		= (int) $_POST['sheet_id'];
$DATA['googleData']['colNumDealID']	= (int) $_POST['col_num_deal_id'];
$DATA['googleData']['rowNum']		= (int) $_POST['row_num'];	// номер заполненной строки в гугл таблице

$DATA['googleData']['sheetName'] = $_POST['sheet_name'];

// Получаем данные нужной строки из гугл таблицы
$sheetName	= $DATA['googleData']['sheetName'];
$rowNum		= $DATA['googleData']['rowNum'];
$range		= "{$sheetName}!{$rowNum}:{$rowNum}";

$DATA['googleData']['rowValues'] = $serviceSheet->spreadsheets_values->get($DATA['googleData']['ssID'], $range)['values'][0];
