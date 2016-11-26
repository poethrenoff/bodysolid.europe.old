<?php
// Подключение хелперов
include_once 'helpers.php';

// Подключение автозагрузчика
include_once 'autoload.php';

// Подключение пользовательских исключений
include_once 'exception.php';

Adminko\System::init();

if (rand(0, 10) == 0) {
	include_once '../script/exchange.php';
}

