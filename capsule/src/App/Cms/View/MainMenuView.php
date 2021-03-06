<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 05.11.2016
 * Time: 19:26
 */

namespace App\Cms\View;


use App\Cms\Ui\MainMenu\MainMenu;

class MainMenuView
{
    protected $instance;

    public function __construct(MainMenu $instance)
    {
        $this->instance = $instance;
    }

    public function __toString()
    {
        return <<<JS
new AplAccordionMenu($this->instance, $('#capsule-cms-main-menu-wrapper'));
new CapsuleUiScrollable('capsule-cms-main-menu-scrollable', $('#capsule-cms-main-menu-wrapper'));
$('#capsule-cms-main-menu-wrapper').find('.capsule-ui-scrollable-wrapper').scrollTop(Cookies('capsule-cms-main-menu-scroll-top') || 0);

JS;
    }
}