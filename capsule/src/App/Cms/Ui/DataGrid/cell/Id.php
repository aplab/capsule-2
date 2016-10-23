<div title="<?=$cell->val?>" class="cell-id w<?=$cell->col->width?>"><div><?=$cell->val?></div>
<input type="hidden" name="id" value="<?=$cell->val?>"><?php
if ($cell->col->edit) :
?><input type="hidden" name="edit" value="<?=$cell->col->baseUrl?>edit/<?=$cell->val?>/"><?php
endif ?><?php if ($cell->col->del) :
?><input type="hidden" name="del" value="<?=$cell->col->baseUrl?>del/"><?php
endif ?></div>
