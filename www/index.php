<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
include dirname(__DIR__) . '/capsule/src/Capsule/Capsule.php';
$system = \Capsule\Capsule::getInstance(__DIR__);
print_r($system);