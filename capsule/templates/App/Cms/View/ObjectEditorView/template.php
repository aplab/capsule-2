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
                <?php \Capsule\Tools\Tools::dump($group) ?>
            </div>
        <?php endforeach ?>
    </div>
</div>
