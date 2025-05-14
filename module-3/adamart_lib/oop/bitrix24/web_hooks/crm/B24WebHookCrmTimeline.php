<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\crm;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

class B24WebHookCrmTimeline extends B24WebHookBase {

	static protected $pre_method = 'crm.timeline.';


	// ДОБАВИТЬ КОММЕНТАРИЙ В ТАЙМЛАЙН
	public static function addComment($data) {
		$url = self::whUrl('comment.add');
		$res = self::executeHook($url, $data);

		return $res;

		// Пример
		// $data = [
		// 	'fields' => [
		// 		'ENTITY_ID'		=> $DEAL_ID,
		// 		'ENTITY_TYPE'	=> 'deal',
		// 		'COMMENT'		=> 'Формируется загружается INVOCE'
		// 	]
		// ];
	}

}