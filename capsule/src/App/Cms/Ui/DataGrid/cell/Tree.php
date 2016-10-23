<?php $level = $cell->item->get('level');
if ($level) : ?>
<div title="<?=$cell->val?>" class="cell-tree w<?=$cell->col->width?>"><div style="margin-left: <?=18*$level?>px;"><?=$cell->val?></div></div>
<?php else : ?>
<div title="<?=$cell->val?>" class="cell-tree w<?=$cell->col->width?>"><div><?=$cell->val?></div></div>
<?php endif ?>