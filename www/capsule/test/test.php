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
$section = new \App\Cms\Ui\Section;
$section->id = 'login';
if (isset($_GET['logout'])) {
    \Capsule\User\Auth::getInstance()->logout();
    header('Location: ' . parse_url($_SERVER['REQUEST_URI'],  PHP_URL_PATH));
    die;
}
$user = \Capsule\User\Auth::getInstance()->user();
if (!$user) {
    echo $section;
    exit;
} ?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    you are <?=$user->login?>
    <a href="<?=$_SERVER['REQUEST_URI']?>?logout">logout</a><br>

<?php

$env = \Capsule\User\Env\Env::getInstance();
//$env->test->test = 123546;
\Capsule\Tools\Tools::dump($env);




?>

</body>
</html>

