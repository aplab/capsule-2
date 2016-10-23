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
\Capsule\Tools\Tools::dump(\Capsule\Unit\UnitTs::config());
//$storages = \Capsule\DataModel\Config\Storage::getInstances();
//\Capsule\Tools\Tools::dump($storages);
//foreach ($storages as $storage) {
//    $storage->destroy();
//}
$o = new \Capsule\Unit\UnitTs;
\Capsule\Tools\Tools::dump($o);
$o->store();
\Capsule\Tools\Tools::dump($o);