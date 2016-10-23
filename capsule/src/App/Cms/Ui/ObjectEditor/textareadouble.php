<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
use Capsule\Common\String;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-textarea-double">
    <div class="capsule-ui-oe-el-textarea-double-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-textarea-double-value">
        <div class="capsule-cms-control-textarea">
            <textarea wrap="off" name="<?=$element->name?>"><?=String::htmlspecialchars($element->value)?></textarea>
        </div>
    </div>
</div>