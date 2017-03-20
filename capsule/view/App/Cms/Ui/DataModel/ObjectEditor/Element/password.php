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
    <div class="<?=$prefix?>-value">
        <input type="password" class="form-control" placeholder="" autocomplete="new-password"
               name="<?=$this->property->name?>"
               id="<?=$prefix?>-element-<?=$this->id?>"
               value="<?=\Capsule\Component\Utf8String::hsc($this->value)?>">
    </div>
</div>
