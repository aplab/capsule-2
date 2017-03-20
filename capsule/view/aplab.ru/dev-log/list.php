<?php
use Capsule\Common\TplVar;
$items = TplVar::getInstance()->items;
$index = TplVar::getInstance()->index; 
?><div id="dev-log-list">
    <?php foreach ($items as $i) : ?>
        <div class="post">
            <h2><a href="/log/<?=$i->id?>/"><?=$i->datetime?></a></h2>
            <?php $title = $i->title ?: $i->name ?>
            <?php if ($title) : ?>
            <h3><?=$title?></h3>
            <?php endif ?>
            <div class="text">
                <?=$i->preview?:$i->fully?>
            </div>
        </div>
    <?php endforeach ?>
    <?php if ($index) : ?>
        <div class="index">
            <?php foreach ($index as $i) : ?>
                <?php if ($i['url']) : ?>
                <a class="page" href="<?=$i['url']?>"><?=$i['page']?></a>
                <?php else : ?>
                <span class="page current"><?=$i['page']?></span>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>
</div>