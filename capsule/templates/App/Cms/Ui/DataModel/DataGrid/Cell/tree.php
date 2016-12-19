<?php $level = $this->item->get('level');
if ($level) : ?>
<div title="<?=$this->val?>"
    <div class="w<?=$this->col->column->width?>"
         title="<?=hsc($this->item->get($this->col->property->name))?>">
        <span style="padding-left: <?=18*$level?>px;"><?=hsc($this->item->get($this->col->property->name))?></span>
    </div>
<?php else : ?>
    <div class="w<?=$this->col->column->width?>"
         title="<?=hsc($this->item->get($this->col->property->name))?>">
        <?=hsc($this->item->get($this->col->property->name))?>
    </div>
<?php endif ?>