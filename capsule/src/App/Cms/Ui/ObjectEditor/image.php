<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-image">
    <div class="capsule-ui-oe-el-image-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-image-preview"></div>
    <div class="capsule-ui-oe-el-image-value">
        <div class="capsule-ui-oe-el-image-brdr">
            <input type="text" value="<?=$element->value?>" name="<?=$element->name?>" />
        </div>
        <div class="capsule-ui-oe-el-image-meta"></div>
    </div>
</div>