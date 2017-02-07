<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 04.02.2017
 * Time: 14:58
 */
$items = \App\Cms\Plugin\DesktopIcon\DesktopIcon\DesktopIcon::all();
ob_start() ?>
<div class="capsule-cms-desktop-icons">
    <div class="capsule-cms-desktop-icons-list">
        <?php foreach ($items as $item) : ?>
            <a class="capsule-cms-desktop-icon" href="<?=$item->url?>">
                <div class="capsule-cms-desktop-icon-img">
                    <i style="background: <?=$item->background?>; color: <?=$item->color?>;" class="<?=$item->icon?>"></i>
                </div>
                <div class="capsule-cms-desktop-icon-text">
                    <?=$item->name?>
                </div>
            </a>
        <?php endforeach ?>
    </div>
</div>
<?php return ob_get_clean();