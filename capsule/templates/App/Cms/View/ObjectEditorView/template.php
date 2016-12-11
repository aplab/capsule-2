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
        <form method="post" id="<?=$this->instance->instanceName?>-form">
        <?php foreach ($this->instance->groups as $group) : ?>
            <div class="<?=$prefix?>-panel">
                <?php if ($group->ckeditor) : ?>
                    <?php foreach ($group as $element) : ?>
                        <?=$element?>
                    <?php endforeach ?>
                <?php else : ?>
                <div class="<?=$prefix?>-elements-wrapper">
                    <div class="<?=$prefix?>-elements">
                    <?php foreach ($group as $element) : ?>
                        <?=$element?>
                    <?php endforeach ?>
                    </div>
                </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
        </form>
    </div>
</div>
