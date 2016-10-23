<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$data_grid = TplVar::getInstance()->dataGrid;
$instance_name = $data_grid->instanceName;
$columns = $data_grid->columns;
$items = $data_grid->items;
?><div id="<?=$instance_name?>" class="capsule-ui-datagrid">
    <div id="<?=$instance_name?>-header-place" class="capsule-ui-datagrid-header-place">
        <div id="<?=$instance_name?>-header" class="capsule-ui-datagrid-header wSum">
            <div id="<?=$instance_name?>-header-wrapper" class="capsule-ui-datagrid-header-wrapper wExt">
            <?php foreach ($columns as $c) : ?>
                <div class="capsule-ui-datagrid-hcell w<?=$c->width?>">
                    <?php if ('Checkbox' == $c->type) : ?>
                    <div><input type="checkbox" name="Checkbox"></div>
                    <?php else : ?>
                    <div title="<?=I18n::_($c->property->title)?>"><?=I18n::_($c->property->title)?></div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
            </div>
        </div>
    </div>
    <div id="<?=$instance_name?>-body" class="capsule-ui-datagrid-body">
        <div id="<?=$instance_name?>-body-wrapper" class="capsule-ui-datagrid-body-wrapper wSum">
        <?php foreach ($items as $i) : ?>
            <div class="capsule-ui-datagrid-body-row wExt">
            <?php foreach ($columns as $c) {
                $cell = $c->cell;
                $cell->item = $i;
                call_user_func(function($cell) {
                    require __DIR__ . '/cell/' . $cell->col->type . '.php';
                }, $cell);
            } ?>
            </div>
        <?php endforeach ?>
        </div>
    </div>
    <div id="<?=$instance_name?>-footer" class="capsule-ui-datagrid-footer">
        <div class="capsule-ui-datagrid-nav">
            <form action="<?=$data_grid->p->url?>" method="post">
                <div class="capsule-ui-datagrid-label">
                    Страница:
                </div>
                <div class="capsule-ui-datagrid-label<?=$data_grid->p->previousPage?'':'-disabled'?>">
                    &larr; Ctrl <span<?=$data_grid->p->previousPage?' id="prev-page-trigger"':''?>>предыдущая</span>
                </div>
                <div class="capsule-ui-datagrid-page">
                    <div class="capsule-cms-control-select">
                        <div>
                            <select name="pageNumber">
                                <?php foreach ($data_grid->p->listPages as $page) : ?>
                                <option<?=$page == $data_grid->p->currentPage?' selected="selected"':''?>><?=$page?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="capsule-ui-datagrid-label<?=$data_grid->p->nextPage?'':'-disabled'?>">
                    <span<?=$data_grid->p->previousPage?' id="next-page-trigger"':''?>>следующая</span> Ctrl &rarr;
                </div>
                <div class="capsule-ui-datagrid-label">
                    Показывать:
                </div>
                <div class="capsule-ui-datagrid-num">
                    <div class="capsule-cms-control-select">
                        <div>
                            <select name="itemsPerPage">
                                <?php foreach ($data_grid->p->itemsPerPageVariants as $n) : ?>
                                <option<?=$n == $data_grid->p->itemsPerPage?' selected="selected"':''?>><?=$n?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
            <?php if ($data_grid->p->nextPage) : ?>
            <form action="<?=$data_grid->p->url?>" method="post" id="next-page">
                <input type="hidden" name="pageNumber" value="<?=$data_grid->p->nextPage?>">
            </form>
            <?php endif ?>
            <?php if ($data_grid->p->previousPage) : ?>
            <form action="<?=$data_grid->p->url?>" method="post" id="previous-page">
                <input type="hidden" name="pageNumber" value="<?=$data_grid->p->previousPage?>">
            </form>
            <?php endif ?>
        </div>
    </div>
</div>