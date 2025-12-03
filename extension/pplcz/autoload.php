<?php

require_once __DIR__ . '/build/vendor/autoload.php';

global $registry;

$registry->get("load")->model('extension/pplcz/log');
$pplcz_log = $registry->get("model_extension_pplcz_log");
$pplcz_log->attach();