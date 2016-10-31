<?php
/**
 * Created by PhpStorm.
 * User: polyanin
 * Date: 16.10.2016
 * Time: 14:34
 */
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', -1);
include dirname(__DIR__, 3) . '/capsule/src/Capsule/Capsule.php';
$system = \Capsule\Capsule::getInstance(dirname(__DIR__, 2));
//$section = new \App\Cms\Ui\Section();
//$html = clone $section;
//$html->id = 'html';
//
//$head = clone $section;
//$head->id = 'head';
//
//$body = clone $section;
//$body->id = 'body';
//
//$title = clone $section;
//$title->id = 'title';
//$title->append('Capsule ');
//$title->append(\Capsule\Capsule::getInstance()->config->version);
//
//$html->append($head);
//$html->append($body);
//$head->append($title);
//
//$css = clone $section;
//$css->id = 'css';
//$head->append($css);
//
//$js = clone $section;
//$js->id = 'js';
//$head->append($js);
//
//echo $html;
//
\Capsule\Tools\Tools::dump(\Capsule\User\User::_configDataJson());
$user = \Capsule\User\User::id(1);
\Capsule\Tools\Tools::dump($user);
//\Capsule\Tools\Tools::dump($user->password('capsule2'));
\Capsule\Component\Session\Session::getInstance()->asdfasdf = 444;
\Capsule\Tools\Tools::dump($_SESSION);
