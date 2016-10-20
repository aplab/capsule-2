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
//$c = new \stdClass;
//$c->jkj = 1234123;
//$storage->set('test', [
//    'field1' => 'val1',
//    'f2' => $c
//]);
$storage->destroy();
\Capsule\Tools\Tools::dump($storage->get('test'));

$a = array('a', 'b', 'c');
$a[100] = 'd';
$a[''] = 'd3';
\Capsule\Tools\Tools::dump($a);
\Capsule\Tools\Tools::dump(\Capsule\Tools\ArrayTools::isNumericKeys($a));