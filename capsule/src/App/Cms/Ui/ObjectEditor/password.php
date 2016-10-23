<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-password">
    <div class="capsule-ui-oe-el-password-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-password-value">
        <div class="capsule-ui-oe-el-password-brdr">
            <input type="password" value="" name="<?=$element->name?>" autocomplete="off" />
        </div>
    </div>
</div>