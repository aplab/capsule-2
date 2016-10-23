<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-nested">
    <div class="capsule-ui-oe-el-nested-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-nested-value">
        <div class="capsule-cms-control-select">
            <div><input type="hidden">
                <select id="capsule-ui-oe-el-nested-master<?=$element->id?>" name="<?=$element->name?>">
                    <option value="0"></option>
                    <?php foreach ($element->options as $value => $optn) : ?>
                        <option value="<?=$value?>"<?=$optn['selected']?' selected="selected"':''?>><?=$optn['text']?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="capsule-ui-oe-el-nested">
    <div class="capsule-ui-oe-el-nested-title"><?=I18n::_('parent')?>:</div>
    <div class="capsule-ui-oe-el-nested-value">
        <div class="capsule-cms-control-select">
            <div>
                <select id="capsule-ui-oe-el-nested-slave<?=$element->id?>" name="<?=$element->settings->depend?>">
                    <option value="0"></option>
                </select>
                <input type="hidden" id="capsule-ui-oe-el-nested-slave-default-value<?=$element->id?>" 
                    value="<?=$element->model->get($element->settings->depend)?>">
                <input type="hidden" id="capsule-ui-oe-el-nested-classname<?=$element->id?>" 
                    value="<?=$element->class?>">
            </div>
        </div>
    </div>
</div>