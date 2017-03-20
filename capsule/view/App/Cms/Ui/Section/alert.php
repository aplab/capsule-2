<?php
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
 *
 * msgboxes after the onload section (javascript)
 */
if (sizeof($this->alert)) : ?>
$(document).ready(function() {
<?php foreach ($this->alert as $_){?><?='alert(\'' . str_replace('\'', '\\\'', $_) . '\')' . PHP_EOL;}?>
});
<?php endif ?>