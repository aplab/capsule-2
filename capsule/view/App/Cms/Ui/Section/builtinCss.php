<?php if (sizeof($this->{pathinfo(__FILE__, PATHINFO_FILENAME)})) : ?>
<style>
<?php foreach ($this->{pathinfo(__FILE__, PATHINFO_FILENAME)} as $_){?><?=$_.PHP_EOL;}?>
</style>
<?php endif ?>