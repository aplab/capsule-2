<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
use Capsule\Common\String;
$element = TplVar::getInstance()->element;
#\Capsule\Tools\Tools::dump($element);
?><div class="capsule-ui-oe-el-variants">
    <div class="capsule-ui-oe-el-variants-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-variants-value">
        <div class="capsule-cms-control-select">
            <div>
                <select name="<?=$element->name?>">
                    <?php foreach ($element->options as $value => $optn) : ?>
                        <option value="<?=String::htmlspecialchars($value)?>"<?=array_key_exists('selected', $optn)?' selected="selected"':''?>><?=String::htmlspecialchars($optn['text'])?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>
</div>