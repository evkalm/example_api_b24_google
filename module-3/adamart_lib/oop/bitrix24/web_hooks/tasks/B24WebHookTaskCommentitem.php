<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\tasks;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

abstract class B24WebHookTaskCommentitem extends B24WebHookBase {

	static protected $pre_method = 'task.commentitem.';

	// ПОЛУЧАЕМ КОММЕНТАРИЙ
	static public function get($data, $full_data = false) {
		$url	= self::whUrl('get');
		$data_	= [$data['TASKID'], $data['ITEMID']];
		$res	= self::executeHook($url, $data_);

		if ($full_data) return $res;
				else return $res['result'];

		// Пример аргумента
		// $data = [
		// 	'TASKID'	=> 111,
		// 	'ITEMID'	=> 222
		// ];
	}

	// ДОБАВЛЯЕМ КОММЕНТАРИЙ В ЗАДАЧУ
	static public function add($data, $full_data = false) {
		$url	= self::whUrl('add');
		$res	= self::executeHook($url, $data);

		if ($full_data) return $res;
				else return $res['result'];		// возвращается id созданного комментария

		// Пример аргумента
		// $data = [
		// 	'taskId'	=> 13729,			// id задачи
		// 	'fields'	=> [
		// 		'AUTHOR_ID'		=> ROBOT_ACCOUNT_ID,
		// 		'POST_MESSAGE'	=> 'text'
		// 	]
		// ];
	}

	// ПОЛУЧАЕМ СПИСОК КОММЕНТАРИЕВ К ЗАДАЧЕ (сырой метод!! -окончательно не реализована обработка передаваемых данных по сортировке и фильтрации)
	static public function getlist($data, $full_data = false) {
		$url	= self::whUrl('getlist');
		$data_	= [
			$data['TASKID'],
			[
				'ID' => 'asc'		// сортировка
			],
			[
				'>AUTHOR_ID' => 1	// фильтрация
			]
		];
		$res	= self::executeHook($url, $data_);

		if ($full_data) return $res;
				else return $res['result'];
	}

	// ПОЛУЧАЕМ СПИСОК КОММЕНТАРИЕВ К ЗАДАЧЕ (упрощенный метод, где в аргумент передаем только ID задачи)
	static public function getlistSimple($task_id, $full_data = false) {
		$url	= self::whUrl('getlist');
		$data_	= [$task_id];
		$res	= self::executeHook($url, $data_);

		if ($full_data) return $res;
				else return $res['result'];
	}
}

