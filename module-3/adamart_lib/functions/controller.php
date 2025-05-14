<?php

require_once __DIR__.'/debug-functions.php';

require_once __DIR__.'/functions.php';

if ( !function_exists('sendMessageToTelegram') )
	require_once $_SERVER['DOCUMENT_ROOT'] . '/adamart/adamart_lib/functions/telegram.php';

require_once __DIR__.'/other-app/controller.php';






