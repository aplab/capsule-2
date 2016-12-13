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
<div class="modal fade" tabindex="-1" role="dialog" id="filter-by-container-window">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?=\Capsule\I18n\I18n::_('Cancel')?></button>
                <button type="button" id="apply-filter-by-container-btn"
                        class="btn btn-primary">Save changes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    $('#apply-filter-by-container-btn').click(function() {
        $('#apply-filter-by-container-form').submit();
    });
</script>
<?php return ob_get_clean();