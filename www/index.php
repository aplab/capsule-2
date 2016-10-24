<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
include dirname(__DIR__) . '/capsule/src/Capsule/Capsule.php';
\Capsule\Capsule::getInstance(__DIR__);
$app_manager = \App\AppManager::getInstance();
\Capsule\DataModel\Config\Storage::getInstance()->destroy();
\App\Website\Structure\Storage::getInstance()->destroy();
\Capsule\Component\DataStorage\DataStorage::getInstance()->destroy();
$app_manager->selectApp()->run();