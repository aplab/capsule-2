<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 14.12.2016
 * Time: 0:48
 */
$module_class = $this->moduleClass;
$module_config = $module_class::config();
$container_class = \Capsule\Core\Fn::cc($module_config->container, \Capsule\Core\Fn::ns($module_class));

$variants = array_replace($this->filterVariants, $container_class::optionsDataList());
ob_start();

?>
<div class="capsule-cms-dialog" id="filter-by-container-window">
    <div class="capsule-cms-dialog-backdrop"></div>
    <div class="capsule-cms-dialog-container">
        <div class="capsule-cms-dialog-content capsule-cms-nested-item-filter">
            <div class="capsule-cms-dialog-header">
                <h4 class="modal-title">Filter</h4>
                <?php /*
                <div class="capsule-cms-dialog-header-menu">
                    <div class="dropdown">
                        <i class="fa fa-ellipsis-v capsule-cms-dialog-toggle-menu dropdown-toggle"
                           aria-hidden="true"
                           id="dropdownMenu1"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                        <ul class="dropdown-menu dropdown-menu-right"
                            aria-labelledby="dropdownMenu1">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                        </ul>
                    </div>
                </div>
                */ ?>
            </div>
            <div class="capsule-cms-dialog-body">
                <div class="capsule-cms-dialog-panel">
                    <form method="post" id="apply-filter-by-container-form"
                          action="<?=$filter($this->mod);?>">
                        <select class="form-control" name="<?=self::FILTER_BY_CONTAINER?>">
                            <?php foreach ($variants as $val => $op) : ?>
                                <option<?=!ctype_digit((string)$val)?' class="bold"':''?>
                                    <?=($val==$this->filterByContainer)?' selected="selected"':''?>
                                        value="<?=$val?>"><?=$op['text']?></option>
                            <?php endforeach ?>
                        </select>
                    </form>
                </div>
            </div>
            <div class="capsule-cms-dialog-footer">
                    <div class="capsule-cms-dialog-footer-button-2">
                        <button type="button" id="apply-filter-by-container-btn"
                                class="btn btn-primary"><?=Capsule\I18n\I18n::_('Apply')?></button>
                    </div>
                    <div class="capsule-cms-dialog-footer-button-2">
                        <button type="button"
                                class="btn btn-default capsule-cms-dialog-close"><?=Capsule\I18n\I18n::_('Cancel')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#apply-filter-by-container-btn').click(function() {
        $('#apply-filter-by-container-form').submit();
    });
</script>
<?php return ob_get_clean();