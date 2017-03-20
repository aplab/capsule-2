<div class="w<?=$this->col->column->width?>"
     title="<?=hsc($this->item->get($this->col->property->name))?>">
    <input type="checkbox"<?=$this->val?' checked="checked"':''?>
           name="<?=$this->col->property->name?>"
           id="c<?=$this->id?>"
           data-class="<?=get_class($this->item)?>"><label for="c<?=$this->id?>"></label></div>