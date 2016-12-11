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
<textarea class="<?=$prefix?>-ckeditor"
          name="<?=$this->property->name?>"
          id="<?=$prefix?>-element-<?=$this->id?>"><?=
    \Capsule\Component\Utf8String::hsc($this->value)
    ?></textarea>