<?php

function processQueue($connDB) {
	// 1. Получаем первую запись из очереди
	$sql	= "SELECT id, task_name, task_data, status, retry_count FROM queue_triumf WHERE status='pending' LIMIT 1";
	$resSql	= mysqli_query($connDB, $sql);
	$task	= mysqli_fetch_assoc($resSql);

	if (!$task) return;

	$rowID		= $task['id'];
	$taskName	= $task['task_name'];
	$taskData	= json_decode($task['task_data'], true);

	// 2 Определяем скрипт/url обработки и данные для него
	$url = null;
	switch ($taskName) {
		case 'google_form_sent':
			$url = 'https://xxx.ru/clients/xxx/module-3/google-form-sent/index.php?tocken=xxx';
			break;
	
		case 'google_cell_changed':
			$url = 'https://xxx.ru/clients/xxx/module-3/google-cell-changed/index.php?tocken=xxx';
			break;

		case 'b24_deal_changed':
			$url = 'https://xxx.ru/clients/xxx/module-3/b24-deal-changed/index.php?tocken=xxx';
			$taskData['deal_id'] = $taskData['data']['FIELDS']['ID'];	// т.к. CURLOPT_POSTFIELDS не передает вложенные массивы
			$taskData['data'] = null;
			$taskData['auth'] = null;
			break;
		
		case 'b24_send_telegram':
			$url = 'https://xxx.ru/clients/xxx/module-3/send-telegram/index.php?tocken=xxx';
			break;
			
		default:
			return;
	}

	// 3. Назначаем статус, что задача взята в обработку
	if ($task['retry_count']) {
		$retryCount = $task['retry_count'] + 1;
	} else {
		$retryCount = 1;
	}

	if ($retryCount > 1) {
		sleep(30);
	}

	$sql = "UPDATE queue_triumf SET status='in_processing', retry_count='{$retryCount}' WHERE id='{$rowID}'";
	mysqli_query($connDB, $sql);

	// 4. Обрабатываем задачу
	if ($url) {
		$ch = curl_init();
		curl_setopt_array(
			$ch,
			array(
				CURLOPT_URL				=> $url,
				CURLOPT_POST			=> TRUE,
				CURLOPT_RETURNTRANSFER	=> TRUE,
				CURLOPT_TIMEOUT			=> 40,
				CURLOPT_POSTFIELDS		=> $taskData
			)
		);

		$resJson	= curl_exec($ch);
		$res		= json_decode($resJson, true);
		curl_close($ch);

		if ($res['success']) {
			$sql = "DELETE FROM queue_triumf WHERE id='{$rowID}' AND status = 'in_processing'";
		} else {
			if ($retryCount < 4) {
				$sql = "UPDATE queue_triumf SET status='pending' WHERE id='{$rowID}'";
			} else {
				$sql = "UPDATE queue_triumf SET status='failed' WHERE id='{$rowID}'";
			}
		}
		mysqli_query($connDB, $sql);
	}
}