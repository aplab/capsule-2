<?php
use Capsule\Ui\TabControl\TabControl;
use Capsule\Ui\TabControl\Tab;
use App\Cms\Ui\TabControl\View;
use Capsule\I18n\I18n;
use Capsule\Core\Fn;
$inst_name = 'nested-item-filter-dialog-window';
$tab_control = new TabControl($inst_name);

$tab = new Tab();
$tab->bind = $inst_name . '-body-t1';
$tab_control->add($tab);
$tab->name = I18n::_('Filter');

$tab = new Tab();
$tab->bind = $inst_name . '-body-t2';
$tab_control->add($tab);
$tab->name = I18n::_('Additional');

$view = new View($tab_control);

$module_class = $this->moduleClass;
$module_config = $module_class::config();
$container_class = Fn::cc($module_config->container, Fn::ns($module_class));

$variants = array_replace($this->filterVariants, $container_class::optionsDataList());

ob_start() ?>
<div class="dialog-window-panel"><div class="dialog-window-tabs"><?=$view?></div>
<div class="dialog-window-body" id="<?=$inst_name?>-body-t1">
    <div class="capsule-cms-control-select">
        <div>
            <form id="apply-filter-by-container-form" action="<?=$filter($this->mod);?>" method="post">
                <select name="<?=self::FILTER_BY_CONTAINER?>">
                    <?php foreach ($variants as $val => $op) : ?>
                    <option<?=!ctype_digit((string)$val)?' class="bold"':''?>
                        <?=($val==$this->filterByContainer)?' selected="selected"':''?>
                        value="<?=$val?>"><?=$op['text']?></option>
                    <?php endforeach ?>
                </select>
            </form>
        </div>
    </div>
</div>
<div class="dialog-window-body" id="<?=$inst_name?>-body-t2">
empty
</div>
</div>
<div class="dialog-window-buttons-place">
    <div class="dialog-window-button">
        <div class="capsule-cms-control-button minw83" id="apply-filter-by-container-btn">
            <span><?=I18n::_('Ok')?></span>
            <button type="button">&nbsp;</button>
        </div>
    </div>
</div>
<script>
$('#apply-filter-by-container-btn').click(function() {
    $('#apply-filter-by-container-form').submit();
});
</script>
<?php return ob_get_clean();