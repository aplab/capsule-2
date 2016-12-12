<?php $user = $this->getUser($this->val) ?>
<?php if ($user) : ?>
    <div class="w<?=$this->col->column->width?>"
         title="<?=hsc($user->login)?>">
        <?=hsc($user->login)?>
    </div>
<?php else : ?>
<div class="w<?=$this->col->column->width?> text-muted"
     title="nobody">nobody</div>
<?php endif ?>