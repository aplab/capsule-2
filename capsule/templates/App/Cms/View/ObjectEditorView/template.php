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

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label" for="email">Email here</label>
                            <div class="<?=$prefix?>-value">
                                <input type="text" class="form-control" id="email" placeholder="Enter email">
                            </div>
                        </div>

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label-checkbox" for="custch1">Email here</label>
                            <div class="<?=$prefix?>-value-checkbox">
                                <input type="checkbox" id="custch1"><label for="custch1"></label>
                            </div>
                        </div>

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label" for="txt">Email here</label>
                            <div class="<?=$prefix?>-value">
                                <textarea class="form-control" rows="3" id="txt" placeholder="Enter email"></textarea>
                            </div>
                        </div>

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label" for="txt">Email here</label>
                            <div class="<?=$prefix?>-value">
                                <div class="input-group">
                                    <div class="input-group-addon">$</div>
                                    <input type="text" class="form-control" id="exampleInputAmount" placeholder="Amount">
                                    <div class="input-group-addon">.00</div>
                                </div>
                            </div>
                        </div>

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label" for="txt">Email here</label>
                            <div class="<?=$prefix?>-value">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#">Action</a></li>
                                            <li><a href="#">Another action</a></li>
                                            <li><a href="#">Something else here</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="#">Separated link</a></li>
                                        </ul>
                                    </div><!-- /btn-group -->
                                    <input type="text" class="form-control" aria-label="...">
                                </div><!-- /input-group -->
                            </div>
                        </div>

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label" for="txt">Email here</label>
                            <div class="<?=$prefix?>-value">
                                <select class="form-control">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                        </div>

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label" for="txt">Email here</label>
                            <div class="<?=$prefix?>-value">
                                <select multiple class="form-control">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                        </div>

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label" for="txt">Email here</label>
                            <div class="<?=$prefix?>-value">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#">Action</a></li>
                                            <li><a href="#">Another action</a></li>
                                            <li><a href="#">Something else here</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="#">Separated link</a></li>
                                        </ul>
                                    </div><!-- /btn-group -->
                                    <input type="text" class="form-control" aria-label="...">
                                </div><!-- /input-group -->
                            </div>
                        </div>

                        <div class="<?=$prefix?>-element">
                            <label class="<?=$prefix?>-label" for="txt">Email here</label>
                            <div class="<?=$prefix?>-value">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#">Action</a></li>
                                            <li><a href="#">Another action</a></li>
                                            <li><a href="#">Something else here</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="#">Separated link</a></li>
                                        </ul>
                                    </div><!-- /btn-group -->
                                    <input type="text" class="form-control" aria-label="...">
                                </div><!-- /input-group -->
                            </div>
                        </div>




                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
