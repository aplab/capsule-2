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
    <link rel="stylesheet" href="/capsule/components/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="/capsule/components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/capsule/components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/capsule/assets/modules/AplAccordionMenu/AplAccordionMenu.css?v=20161127103924">
    <link rel="stylesheet" href="/capsule/assets/cms/modules/CapsuleCmsActionMenu/CapsuleCmsActionMenu.css?v=20161127122756">
    <link rel="stylesheet" href="/capsule/assets/modules/Scrollable/CapsuleUiScrollable.css?v=20161125020631">
    <link rel="stylesheet" href="/capsule/assets/cms/modules/CapsuleCmsDataGrid/CapsuleCmsDataGrid.css?v=20161127171323">
    <link rel="stylesheet" href="/capsule/assets/cms/css/style.css?v=20161118014933">
    <script src="/capsule/components/jquery/jquery-3.1.1.min.js"></script>
    <script src="/capsule/components/jquery.mousewheel/jquery.mousewheel.min.js"></script>
    <script src="/capsule/components/bootstrap/js/bootstrap.min.js"></script>
    <script src="/capsule/components/jquery-ui/jquery-ui.min.js"></script>
    <script src="/capsule/components/js-cookie/js.cookie-2.1.3.min.js"></script>
    <script src="/capsule/assets/modules/AplAccordionMenu/AplAccordionMenu.js?v=20161113172505"></script>
    <script src="/capsule/assets/modules/Scrollable/CapsuleUiScrollable.js?v=20161111011517"></script>
    <script src="/capsule/assets/cms/modules/CapsuleCmsActionMenu/CapsuleCmsActionMenu.js?v=20161117023043"></script>
    <script src="/capsule/assets/cms/modules/CapsuleCmsDataGrid/CapsuleCmsDataGrid.js?v=20161127171026"></script>
    <script src="/capsule/components/viewport-units-buggyfill/viewport-units-buggyfill.js"></script>
    <script src="/capsule/assets/cms/js/js.js?v=20161126200521"></script>
</head>
<body>
    you are <?=$user->login?>
    <a href="<?=$_SERVER['REQUEST_URI']?>?logout">logout</a><br>


    <script>
        $(document).ready(function () {
            var outer = $('<div>');
            outer.css({
                width: 100,
                height: 100,
                overflow: 'scroll',
                background: '#f00',
                position: 'relative'
            });
            var inner = $('<div>');
            inner.css({
                position: 'absolute',
                left: 0,
                top: 0,
                right: 0,
                bottom: 0,
                background: '#ff0'
            });

            $('body').append(outer);
            outer.append(inner);
            console.log(Math.round(outer.width() - inner.width()));
            console.log(Math.round(outer.height() - inner.height()));
            $(window).resize(function () {
                console.clear();
                console.log(Math.round(outer.width() - inner.width()));
                console.log(Math.round(outer.height() - inner.height()));
            });

        });

    </script>


</body>
</html>

