<?php
use Capsule\Common\TplVar;
use Capsule\Common\String;
$element = TplVar::getInstance()->element;
?><textarea class="capsule-oe-ckeditor" name="<?=$element->name?>"><?=String::htmlspecialchars($element->value)?></textarea>