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
$section = new \App\Cms\Ui\Section();
$section->id = 'test';
\Capsule\Tools\Tools::dump($section);
\Capsule\Tools\Tools::dump(\App\Cms\Ui\SectionManager::getInstance()->test);
for ($i = 0; $i < 10; $i ++) {
    $section->append($i);
}
\Capsule\Tools\Tools::dump(\App\Cms\Ui\SectionManager::getInstance()->test);
$section->prepend('append', 'asdl');
\Capsule\Tools\Tools::dump(\App\Cms\Ui\SectionManager::getInstance()->test);

$section->insert('insert', -1);
\Capsule\Tools\Tools::dump(\App\Cms\Ui\SectionManager::getInstance()->test);

$section->('insert', -1);
\Capsule\Tools\Tools::dump(\App\Cms\Ui\SectionManager::getInstance()->test);