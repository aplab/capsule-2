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
    <label class="<?=$prefix?>-label-checkbox"
           for="<?=$prefix?>-element-<?=$this->id?>"><?=$this->property->name?></label>
    <div class="<?=$prefix?>-value-checkbox">
        <input type="hidden" name="<?=$this->property->name?>" value="0">
        <input type="checkbox"<?=$this->value?' checked="checked"':''?>
               name="<?=$this->property->name?>"
               value="1"
               id="<?=$prefix?>-element-<?=$this->id?>"><label
                for="<?=$prefix?>-element-<?=$this->id?>"></label>
    </div>
</div>