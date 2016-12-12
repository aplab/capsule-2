<div class="w<?=$this->col->column->width?> text-right"
     title="<?=$this->item->get($this->col->property->name)?>">
    <a href="<?=$this->col->container->baseUrl?>edit/<?=$this->item->get($this->col->property->name)?>/"><?=sprintf('%07d', $this->item->get($this->col->property->name))?></a>
</div>