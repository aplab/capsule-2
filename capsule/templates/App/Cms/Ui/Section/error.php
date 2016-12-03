<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 03.11.2016
 * Time: 23:33
 */
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="/capsule/vendor/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/capsule/vendor/bower_components/bootstrap/dist/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/capsule/vendor/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/capsule/assets/cms/css/error.css">
    <script src="/capsule/vendor/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="/capsule/vendor/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="/capsule/assets/cms/js/error.js"></script>
</head>
<body class="bg-danger">
<div class="container">
    <div class="row">
        <div class="col apl-error-col">
            <h1 class="text-danger"><strong>
                    Fatal error!</strong></h1>
            <?php $section = \App\Cms\Ui\SectionManager::getInstance()->error ?>
            <h2 class="text-danger"><?=$section->exception->getMessage()?></h2>
            <?php $xdebug_message = $section->exception->xdebug_message?>

            <?php unset($section->exception->xdebug_message) ?>

                <?php ini_set('xdebug.var_display_max_data', 1000000) ?>
                <?php ini_set('xdebug.var_display_max_children', 1000000) ?>
                <?php ini_set('xdebug.var_display_max_depth', 64) ?>
                <?php var_dump($section->exception); ?>

        </div>
        <h2>Xdebug message</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-bordered">
                <?=$xdebug_message?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
