<?php # msgboxes after the onload section (javascript)
if (sizeof($this->{pathinfo(__FILE__, PATHINFO_FILENAME)})) :
?>$(document).ready(function() {
<?php foreach ($this->{pathinfo(__FILE__, PATHINFO_FILENAME)} as $_){?><?='alert(\'' . str_replace('\'', '\\\'', $_) . '\')' . PHP_EOL;}?>
});<?php endif ?>