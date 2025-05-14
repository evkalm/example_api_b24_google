<?php
// ЗАПИСЫВАЕМ DEAL_ID В ГУГЛ ТАБЛИЦУ

$values = [ [$DATA['candidateData']['dealID']] ];
$body = new Google\Service\Sheets\ValueRange( [ 'values' => $values ] );
$options = ['valueInputOption' => 'RAW'];

$serviceSheet->spreadsheets_values->update( $DATA['googleData']['ssID'], $DATA['googleData']['sheetName'] . '!' . $DATA['googleData']['colLetterDealID'] . $DATA['googleData']['rowNum'], $body, $options );

