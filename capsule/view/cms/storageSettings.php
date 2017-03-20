<?php
use Capsule\Ui\TabControl\TabControl;
use Capsule\Ui\TabControl\Tab;
use App\Cms\Ui\TabControl\View;
use Capsule\I18n\I18n;
use Capsule\Common\TplVar;
$in = TplVar::getInstance()->instanceName;
$tab_control = new TabControl($in . '-tab-dialog-window');

$tab = new Tab();
$tab->bind = $in . '-t1';
$tab_control->add($tab);
$tab->name = I18n::_('Storage');

$tab = new Tab();
$tab->bind = $in . '-t2';
$tab_control->add($tab);
$tab->name = I18n::_('Preferences');

$view = new View($tab_control);
ob_start() ?>
<div class="dialog-window-panel"><div class="dialog-window-tabs"><?=$view?></div>
    <div class="dialog-window-body" id="<?=$in?>-t1">
        <?=I18n::_('Local storage only')?>
    </div>
    <div class="dialog-window-body" id="<?=$in?>-t2">
    
    </div>
</div>
<div class="dialog-window-buttons-place">
    <div class="dialog-window-button" id="<?=$in?>close-window">
        <div class="capsule-cms-control-button">
            <span><?=I18n::_('Ok')?></span>
            <button type="button">&nbsp;</button>
        </div>
    </div>
</div>
<script>
$('#<?=$in?>close-window').find('button').click(function() {
    CapsuleUiDialogWindow.getInstance('<?=$in?>-settings').hide();
});
</script>
<?php return ob_get_clean();