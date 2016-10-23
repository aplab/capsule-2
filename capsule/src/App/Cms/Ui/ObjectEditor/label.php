<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-label">
    <div class="capsule-ui-oe-el-label-title"><?=I18n::_($element->property->title)?>:</div>
    <?php if ($element->hasValue) : ?>
        <div class="capsule-ui-oe-el-label-value"><?=$element->value?></div>
    <?php else: ?>
        <div class="capsule-ui-oe-el-label-value capsule-ui-oe-el-label-noval"><?=I18n::_('Undefined')?></div>
    <?php endif ?>
    <input type="hidden" name="<?=$element->name?>" value="<?=$element->value?>" />
</div>