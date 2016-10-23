<?php $o = $cell->getValue($cell->val); if (is_null($o)) : ?>
<div title="undefined" class="cell-bind w<?=$cell->col->width?>">
<div class="undef">undefined</div></div>
<?php else : ?>
<div title="<?=$o['text']?>" class="cell-bind w<?=$cell->col->width?>">
<div><?=$o['text']?></div></div>
<?php endif ?>