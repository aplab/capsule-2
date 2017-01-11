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
        <div class="capsule-cms-dialog-content">
            <div class="capsule-cms-dialog-header">
                <h4>Filter</h4>
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
                                class="btn btn-primary">Save changes</button>
                    </div>
                    <div class="capsule-cms-dialog-footer-button-2">
                        <button type="button" class="btn btn-default capsule-cms-dialog-close">Close</button>
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