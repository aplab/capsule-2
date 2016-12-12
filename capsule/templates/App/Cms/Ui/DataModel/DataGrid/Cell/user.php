<?php $user = $this->getUser($this->val); if ($user) : ?>
<div title="<?=$user->login?>" class="cell-user w<?=$this->col->width?>">
<div><?=$user->login?></div></div>
<?php else : ?>
<div title="nobody" class="cell-user w<?=$this->col->width?>">
<div class="nobody">nobody</div></div>
<?php endif ?>