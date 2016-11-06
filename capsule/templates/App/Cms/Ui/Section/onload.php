<?php # onload scripts
use App\Cms\Cms as c; ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#capsule-cms-main-menu').css('height', '<?=c::getInstance()->config->ui->mainMenu->height?>px');
    $('#capsule-cms-workplace').css('top', '<?=c::getInstance()->config->ui->mainMenu->height?>px');
    <?php foreach ($this->{pathinfo(__FILE__, PATHINFO_FILENAME)} as $_){?><?=$_.PHP_EOL;}?>
});
</script>