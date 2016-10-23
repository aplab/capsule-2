<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$element = TplVar::getInstance()->element;
?><div class="capsule-ui-oe-el-user">
    <div class="capsule-ui-oe-el-user-title"><?=I18n::_($element->property->title)?>:</div>
    <?php if ($element->hasValue) :
        $user = $element->getUser($element->value);
        if ($user) : ?>
        <div class="capsule-ui-oe-el-user-value"><?=$user->login?></div>
        <?php else : ?>
        <div class="capsule-ui-oe-el-user-value capsule-ui-oe-el-user-noval">nobody</div>
        <?php endif ?>
    <?php else: ?>
        <div class="capsule-ui-oe-el-user-value capsule-ui-oe-el-user-noval"><?=I18n::_('Undefined')?></div>
    <?php endif ?>
    <input type="hidden" name="<?=$element->name?>" value="<?=$element->value?>" />
</div>