<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-flag">
    <div class="capsule-ui-oe-el-flag-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-flag-value">
            <input type="hidden" name="<?=$element->name?>" value="0" />
        <div class="capsule-cms-control-checkbox ">
            <input type="checkbox"<?=$element->value?' checked="checked"':''?> name="<?=$element->name?>" value="1" />
        </div>
    </div>
</div>