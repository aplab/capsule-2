<?php $user = $cell->getUser($cell->val); if ($user) : ?>
<div title="<?=$user->login?>" class="cell-user w<?=$cell->col->width?>">
<div><?=$user->login?></div></div>
<?php else : ?>
<div title="nobody" class="cell-user w<?=$cell->col->width?>">
<div class="nobody">nobody</div></div>
<?php endif ?>