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
$html = clone $section;
$html->id = 'html';

$head = clone $section;
$head->id = 'head';

$body = clone $section;
$body->id = 'body';

$html->append($head);
$html->append($body);

echo $html;

