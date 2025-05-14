<?php
namespace Adamart_lib\oop\bitrix24\web_hooks\tasks;

use Adamart_lib\oop\bitrix24\web_hooks\B24WebHookBase;

abstract class B24WebHookTasksTask extends B24WebHookBase {

	static protected $pre_method = 'tasks.task.';

	// СОЗДАЕМ ЗАДАЧУ
	static public function add($data, $full_data = false) {
		$url = self::whUrl('add');
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
				else return $res['result']['task']['id'];
		// return $res;
		// Пример аргумента
		// $data = [
		// 	'fields' => [
		// 		'TITLE'				=> 'Задача тест3',
		// 		'DESCRIPTION'		=> 'Описание задачи',		// описание задачи
		// 		'DEADLINE'			=> '2023-01-10T18:00:00+03:00',	// крайний срок
		// 		'CREATED_BY'		=> 26,			// id постановщика
		// 		'RESPONSIBLE_ID'	=> 26,			// id исполнителя
		// 		'UF_CRM_TASK'		=> ['D_7230']		// связанная сделка
		// 	]
		// ];
	}

	// УДАЛЯЕМ ЗАДАЧУ
	static public function delete($task_id) {
		$url = self::whUrl('delete');
		$data = [ 'taskId' => $task_id ];
		$res = self::executeHook($url, $data);
		return $res;
	}


	// ПОЛУЧАЕМ ДАННЫЕ ИЗ ЗАДАЧИ
	static public function get($data, $full_data = false) {
		$url = self::whUrl('get');
		$res = self::executeHook($url, $data);

		if ($full_data) return $res;
			else return $res['result']['task'];
		
		// Пример
		// $data = [ 
		// 	'taskId' => $task_id,
		// 	'select' => ['*']
		// ];
	}
	

	// ПОЛУЧАЕМ ПЕРЕЧЕНЬ ЗАДАЧ ПО ФИЛЬТРУ
	static public function list($filter) {
		$url = self::whUrl('list');
		$send_filfer = [
			'order'		=> isset($filter['order'])	? $filter['order']	: NULL,
			'filter'	=> isset($filter['filter'])	? $filter['filter']	: NULL,
			'select'	=> isset($filter['select'])	? $filter['select']	: NULL,
			'limit'		=> isset($filter['limit'])	? $filter['limit']	: NULL,
			'start'		=> isset($filter['start'])	? $filter['start']	: NULL
		];
		
		$res = self::executeHook($url, $send_filfer);

		return $res;

		// Пример данных фильтра
		// $filter = [
		// 	'filter' => [
		// 		'>STATUS' => 2,
		// 		'REPLICATE' =>'N',
		// 		'::SUBFILTER-PARAMS' => ['FAVORITE' => 'Y']
		// 	]
		// ];
		
	}

	// Записываем данные в задачу
	static public function update($data, $full_data = false) {
		$url = self::whUrl('update');
		$res = self::executeHook($url, $data);
		if ($full_data) return $res;
			else return $res['result']['task'];

		// Пример
		// $data = [
		// 	'taskId'	=> 111,
		// 	'fields'	=> [
		//		'STAGE_ID'	> 222
		// 	]
		//]
	}
}