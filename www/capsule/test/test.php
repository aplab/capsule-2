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
$storage = \Capsule\Component\DataStorage\DataStorage::getInstance();
\Capsule\Tools\Tools::dump($storage);
\Capsule\Tools\Tools::dump(\Capsule\I18n\I18n::t(\Capsule\DataModel\DataModel::config()));
