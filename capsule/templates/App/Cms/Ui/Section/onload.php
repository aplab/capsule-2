<?php # onload scripts
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 31.10.2016
 * Time: 0:31
 */
?><script type="text/javascript">
$(document).ready(function() {
    <?php foreach ($this->{pathinfo(__FILE__, PATHINFO_FILENAME)} as $_){?><?=$_.PHP_EOL;}?>
});
</script>