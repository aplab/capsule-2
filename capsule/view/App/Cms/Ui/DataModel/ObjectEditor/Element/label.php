<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 11.12.2016
 * Time: 3:22
 */
$prefix = 'capsule-cms-object-editor' ?>
<div class="<?=$prefix?>-element">
    <label class="<?=$prefix?>-label" for="<?=$prefix?>-element-<?=$this->id?>"><?=$this->property->name?></label>
    <?php if ($this->hasValue) : ?>
        <div class="<?=$prefix?>-value <?=$prefix?>-value-label">
            <?=$this->value?>
        </div>
    <?php else : ?>
        <div class="<?=$prefix?>-value <?=$prefix?>-value-label <?=$prefix?>-value-label-undefined text-muted"></div>
    <?php endif ?>
    <input type="hidden" name="<?=$this->property->name?>" value="" id="<?=$prefix?>-element-<?=$this->id?>">
</div>
