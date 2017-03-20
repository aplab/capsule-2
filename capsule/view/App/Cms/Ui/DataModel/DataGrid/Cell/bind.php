<?php $value = $this->value($this->val) ?>
<?php if (is_null($value)) : ?>
    <div class="w<?=$this->col->column->width?> text-muted"
         title="undefined">undefined</div>
<?php else : ?>
    <div class="w<?=$this->col->column->width?>"
         title="<?=hsc($value['text'])?>">
        <?=hsc($value['text'])?>
    </div>
<?php endif ?>