<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 06.12.2016
 * Time: 0:09
 */
$prefix = 'capsule-cms-object-editor';
?>
<div class="<?=$prefix?>" id="<?=$prefix?>">
    <div class="<?=$prefix?>-head">
        <div class="<?=$prefix?>-tabs-wrapper">
            <div class="<?=$prefix?>-tabs">
                <?php foreach ($this->instance->groups as $group) : ?>
                    <div class="<?=$prefix?>-tab">
                        <?=$group->name?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
        <div class="<?=$prefix?>-arrow-left glyphicon glyphicon-menu-left"></div>
        <div class="<?=$prefix?>-arrow-right glyphicon glyphicon-menu-right"></div>
    </div>
    <div class="<?=$prefix?>-body">
        <?php foreach ($this->instance->groups as $group) : ?>
            <div class="<?=$prefix?>-panel">
                <?php #\Capsule\Tools\Tools::dump($group) ?>
                <div class="<?=$prefix?>-elements-wrapper">
                    <div class="<?=$prefix?>-elements">

                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="email">Email:</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="email" placeholder="Enter email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="pwd">Password:</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" id="pwd" placeholder="Enter password">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <div class="checkbox">
                                        <label><input type="checkbox"> Remember me</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-3" for="comment">Password:</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="5" id="comment"></textarea>
                                </div>
                            </div>
                        </div>





                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
