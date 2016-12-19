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
 */
$class = '';
(function() use(&$class) {
    try {
        $capsule_cms_data = json_decode($_COOKIE['capsule-cms-data']);
        $tmp = [];
        if ($capsule_cms_data->sidebar_pin) {
            $tmp[] = 'capsule-cms-sidebar-wrapper-pinned';
            if ($capsule_cms_data->sidebar_open) {
                $tmp[] = 'capsule-cms-sidebar-wrapper-expanded';
            }
        }
        if (sizeof($tmp)) {
            $class = ' class="' . join(' ', $tmp) . '"';
        }
    } catch (\Throwable $e) {
        $class = '';
    }
})();
?><body<?=$class?>>
    <?php foreach ($this->{pathinfo(__FILE__, PATHINFO_FILENAME)} as $_){?><?=$_.PHP_EOL;}?>
    <?php include "dialog.php"; ?>
</body>
