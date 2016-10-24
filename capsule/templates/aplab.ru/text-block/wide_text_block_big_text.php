<?php use Capsule\Common\TplVar;
$o = TplVar::getInstance()->o ?><div class="wide-text-block big-text">
    <?php if ($o->title) : ?>
    <h1 class="center"><?=$o->title?></h1>
    <?php endif ?>
    <?=$o->text?>
</div>