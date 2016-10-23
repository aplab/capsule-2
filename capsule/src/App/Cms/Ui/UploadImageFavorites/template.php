<?php
use Capsule\Common\TplVar;
use App\Cms\Model\HistoryUploadImage;
use Capsule\I18n\I18n;
$tplvar = TplVar::getInstance();
$in = $tplvar->instanceName;
?><div class="capsule-ui-upload-image-history" id="<?=$in?>">
    <div class="workplace" id="<?=$in?>-workplace">
    <?php foreach (HistoryUploadImage::favorites() as $i) : ?>
    <div class="item" title="<?=$i['comment']?>">
        <input type="hidden" value="<?=$i['id']?>" name="id">
        <div class="img"><img src="<?=$i['path']?>"></div>
        <div class="size"><?=$i['width']?>x<?=$i['height']?></div><div class="type"><?=pathinfo($i['path'], PATHINFO_EXTENSION)?></div>
        <div class="name"><?=$i['name']?></div>
        <div class="fav<?=$i['favorites']?' is-fav':''?>" title="<?=I18n::_('Add/remove to favorites')?>"></div>
        <div class="pen" title="<?=I18n::_('Edit comment')?>"></div>
        <div class="del" title="<?=I18n::_('Physically delete from storage')?>"></div>
    </div>
    <?php endforeach ?>
</div>