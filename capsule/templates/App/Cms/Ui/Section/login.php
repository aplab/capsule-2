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
    <link rel="stylesheet" href="/capsule/components/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/capsule/components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/capsule/assets/cms/css/login.css">
    <script src="/capsule/components/jquery/jquery-3.1.1.min.js"></script>
    <script src="/capsule/components/bootstrap/js/bootstrap.min.js"></script>
    <script src="/capsule/assets/cms/js/login.js"></script>
</head>
<body>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">Capsule <?=\Capsule\Capsule::getInstance()->config->version?></h2>
            </div>
            <form method="post" action="<?=parse_url($_SERVER['REQUEST_URI'],  PHP_URL_PATH)?>">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-users fa" aria-hidden="true"></i></span>
                            <input class="form-control input-lg" placeholder="login as" type="text"
                                   name="<?=\Capsule\User\Auth::POST_VAR_USERNAME?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-lock fa-lg" aria-hidden="true"></i></span>
                            <input class="form-control input-lg" placeholder="Password" type="password"
                                   name="<?=\Capsule\User\Auth::POST_VAR_PASSWORD?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-lg btn-block">Sign In</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
