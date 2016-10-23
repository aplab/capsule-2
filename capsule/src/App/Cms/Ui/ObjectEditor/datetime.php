<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-datetime">
    <div class="capsule-ui-oe-el-datetime-title"><?=I18n::_($element->property->title)?>:</div>
    <div class="capsule-ui-oe-el-datetime-value" class="PopcalTrigger"
        onclick="if(self.gfPop)gfPop.fPopCalendar($(this).find('input').get(0));return false;">
        <div class="capsule-ui-oe-el-datetime-brdr">
            <input type="datetime" value="<?=$element->value?>"
                name="<?=$element->name?>" readonly="readonly" />
        </div>
    </div>
</div>