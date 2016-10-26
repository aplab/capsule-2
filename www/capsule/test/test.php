<?php
/**
 * Created by PhpStorm.
 * User: polyanin
 * Date: 16.10.2016
 * Time: 14:34
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
include dirname(__DIR__, 3) . '/capsule/src/Capsule/Capsule.php';
$system = \Capsule\Capsule::getInstance(dirname(__DIR__, 2));
\Capsule\Component\DataStorage\DataStorage::getInstance()->destroy();
$app_manager = \App\AppManager::getInstance();
$app = $app_manager->selectApp();
\Capsule\Tools\Tools::dump($app);
\Capsule\Tools\Tools::dump(\Capsule\Ui\Section::templatesDir()->createDir());
$p = new \Capsule\Component\Path\ComponentTemplatePath('\Capsule\Ui\Section', 'abstract');
\Capsule\Tools\Tools::dump($p->createFile());
