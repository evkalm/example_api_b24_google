<?php

// ФУНКЦИИ ДЛЯ РАБОТЫ С БИБЛИОТЕКОЙ PHPWord

// Делаем подстроку жирным шрифтом (подстрока-исходник должна быть ограничена символами "[B]" и "[/B]")
function convertBoldSubstringPHPWord($text, $font_size = 11.5, $font = 'Times New Roman') {

	$font_size_px = $font_size * 2;		// импирически определено размер шрифта в пикселях
	// TAB и переносы на строку не делать, возникают ошибки
	$startWordTags = '</w:t></w:r><w:r><w:rPr><w:b/><w:rFonts w:ascii="' . $font .'" w:eastAsia="' . $font .'" w:hAnsi="' . $font .'" w:cs="' . $font .'" /><w:sz w:val="' . $font_size_px . '" /><w:szCs w:val="' . $font_size_px . '" /><w:lang w:val="en-US" w:eastAsia="en-US" w:bidi="ar-SA" /></w:rPr><w:t xml:space="preserve"> ';

	$endWordTags = '</w:t></w:r><w:r><w:rPr><w:rFonts w:ascii="' . $font .'" w:eastAsia="' . $font .'" w:hAnsi="' . $font .'" w:cs="' . $font .'" /><w:sz w:val="' . $font_size_px . '" /><w:szCs w:val="' . $font_size_px . '" /><w:lang w:val="en-US" w:eastAsia="en-US" w:bidi="ar-SA" /></w:rPr><w:t xml:space="preserve">';

	// Пример из чата: https://github.com/PHPOffice/PHPWord/issues/750
	// $replace = str_replace('&lt;B&gt;', '</w:t></w:r><w:r><w:rPr><w:b/></w:rPr><w:t xml:space="preserve"> ', $replace);
	// $replace = str_replace('&lt;/B&gt;', '</w:t></w:r><w:r><w:t xml:space="preserve">', $replace);

	$text = str_replace('[B]', $startWordTags, $text);
	$text = str_replace('[/B]', $endWordTags, $text);
	return $text;
}

// Формируем переходы на новую строку + функция convertBoldSubstringPHPWord()
function convertNewLineAndBoldPHPWord($text, $font_size = 11.5, $font = 'Times New Roman') {
	// прим.: сначала делаем переход на новую строку, потом делаем жирный шрифт, иначе word документ не формируется
	$text = str_replace(PHP_EOL, '</w:t><w:br/><w:t>', $text);
	$text = convertBoldSubstringPHPWord($text, $font_size, $font);
	return $text;
}

// Про неразрывный пробел почитать
// https://searchengines.guru/ru/forum/780402