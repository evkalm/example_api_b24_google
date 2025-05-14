<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\tasks;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

abstract class B24WebHookTasksStages extends B24WebHookBase {

	static protected $pre_method = 'task.stages.';

	// ПОЛУЧАЕМ СТАДИИ КАНБАНА
	static public function get($data, $full_data = false) {
		$url = self::whUrl('get');
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result'];

		// Пример аргумента
		// $data = [ 
		// 	'entityId' => 1,	// id группы
		// 	'isAdmin' => true
		// ];
	}
}