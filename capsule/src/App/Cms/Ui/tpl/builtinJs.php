<?php # built-in scripts in the head section ?>
<?php if (sizeof($this->{pathinfo(__FILE__, PATHINFO_FILENAME)})) : ?>
<script type="text/javascript">
<?php foreach ($this->{pathinfo(__FILE__, PATHINFO_FILENAME)} as $_){?><?=$_.PHP_EOL;}?>
</script>
<?php endif ?>