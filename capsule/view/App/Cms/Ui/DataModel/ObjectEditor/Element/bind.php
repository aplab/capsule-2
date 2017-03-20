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
    <label class="<?=$prefix?>-label" for="<?=$prefix?>-element-<?=$this->id?>"><?=$this->property->title?></label>
    <div class="<?=$prefix?>-value">
        <select class="form-control"
                name="<?=$this->property->name?>"
                id="<?=$prefix?>-element-<?=$this->id?>">
            <option value="0"></option>
            <?php if ($this->hasValue) : ?>
                <?php foreach ($this->options as $value => $optn) : ?>
                    <option value="<?=$value?>"<?=(string)$value===(string)$this->value?' selected="selected"':''?>><?=$optn['text']?></option>
                <?php endforeach ?>
            <?php else : ?>
                <?php foreach ($this->options as $value => $optn) : ?>
                    <option value="<?=$value?>"<?=(string)$value===(string)$this->default?' selected="selected"':''?>><?=$optn['text']?></option>
                <?php endforeach ?>
            <?php endif ?>
        </select>
    </div>
</div>