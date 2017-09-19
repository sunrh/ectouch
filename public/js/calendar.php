<?php

/**
 * 调用日历 JS
 */

$lang = (!empty($_GET['lang'])) ? trim($_GET['lang']) : 'zh-cn';

$calendar = dirname(__DIR__) . '/../resources/lang/' . $lang . '/calendar.php';
if (!file_exists($calendar) || strrchr($lang, '.')) {
    $lang = 'zh-cn';
}

require($calendar);

header('Content-type: application/x-javascript; charset=utf-8');

foreach ($_LANG['calendar_lang'] as $cal_key => $cal_data) {
    echo 'var ' . $cal_key . " = \"" . $cal_data . "\";\r\n";
}

require __DIR__ . '/calendar/calendar.js';