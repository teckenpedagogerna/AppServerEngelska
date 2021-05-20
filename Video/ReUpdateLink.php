<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';

ignore_user_abort(true);
set_time_limit(0);
ini_set("memory_limit", "-1");

include_once __DIR__ . '/UpdateWords.php';
include_once __DIR__ . '/UpdateTexts.php';
include_once __DIR__ . '/ReLinkTranslations.php';


exit();