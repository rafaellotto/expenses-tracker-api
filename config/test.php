<?php
$db = require __DIR__ . '/test_db.php';
$config = require __DIR__ . '/web.php';

$config['components']['db'] = $db;
$config['components']['urlManager']['showScriptName'] = true;

return $config;