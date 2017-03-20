<?php
use Capsule\Common\TplVar;
$i = TplVar::getInstance()->item;
$nav = TplVar::getInstance()->nav;
?><div id="dev-log-item">
    <div class="post">
        <h2><?=$i->datetime?></h2>
        <?php $title = $i->title ?: $i->name ?>
        <?php if ($title) : ?>
        <h3><?=$title?></h3>
        <?php endif ?>
        <div class="text">
            <?=$i->fully?>
        </div>
    </div>
    <?php if ($nav) : ?>
        <div class="nav">
            <?php foreach ($nav as $i) : ?>
                <a href="<?=$i['url']?>"><?=$i['label']?></a>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>