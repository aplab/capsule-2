<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 14.12.2016
 * Time: 8:57
 */
ob_start(); ?>


    <div class="capsule-cms-dialog" id="capsule-about-window">
        <div class="capsule-cms-dialog-backdrop"></div>
        <div class="capsule-cms-dialog-container">
            <div class="capsule-cms-dialog-content">
                <div class="capsule-cms-dialog-header">
                    <h4 class="modal-title">About</h4>
                </div>
                <div class="capsule-cms-dialog-body">


                    <div>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-justified" role="tablist">
                            <li role="presentation" class="active"><a href="#about-program" aria-controls="about-program" role="tab" data-toggle="tab">About</a></li>
                            <li role="presentation"><a href="#about-author" aria-controls="about-author" role="tab" data-toggle="tab">Author</a></li>
                            <li role="presentation"><a href="#about-licence" aria-controls="about-licence" role="tab" data-toggle="tab">Licence</a></li>
                            <li role="presentation"><a href="#about-libraries" aria-controls="about-libraries" role="tab" data-toggle="tab">Libraries</a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="about-program">
                                <p>Capsule</p>
                                <p>Version: <?=\Capsule\Capsule::getInstance()->config->version?></p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="about-author">
                                <p><a target="_blank" href="http://www.aplab.ru">Alexander Polyanin</a></p>
                                <p><a target="_blank" href="http://www.aplab.ru">www.aplab.ru</a></p>
                                <p>Thanks to <a target="_blank" href="https://www.jetbrains.com/phpstorm/">JetBrains.</a> This Project was created in the best IDE - PhpStorm. Thanks to the company JetBrains for supporting the project in the form of a license for a great product PhpStorm.</p>
                                <p>Thanks to all contributors on <a target="_blank" href="https://github.com/aplab/capsule-2">GitHub.</a></p>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="about-licence">
                                <?php include 'licence.php' ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="about-libraries">
                                <p>"jquery": "^3.1.1",</p>
                                <p>"viewport-units-buggyfill": "^0.6.0",</p>
                                <p>"js-cookie": "^2.1.3",</p>
                                <p>"jquery-ui": "^1.12.1",</p>
                                <p>"jquery-mousewheel": "jquery/jquery-mousewheel#^3.1.13",</p>
                                <p>"bootstrap": "^3.3.7",</p>
                                <p>"font-awesome": "fontawesome#^4.7.0",</p>
                                <p>"ckeditor": "#full/4.6.0",</p>
                                <p>"screenfull": "^3.0.2",</p>
                                <p>"eonasdan-bootstrap-datetimepicker": "^4.17.43",</p>
                                <p>"swiftmailer/swiftmailer": "^5.4",</p>
                                <p>"easybook/geshi": "v1.0.8.18",</p>
                                <p>"respect/validation": "^1.1"</p>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="capsule-cms-dialog-footer">
                    <button type="button" class="btn btn-default capsule-cms-dialog-close">Close</button>
                </div>
            </div>
        </div>
    </div>



<?php return ob_get_clean();