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


use App\Cms\Ui\ActionMenu\ActionMenu;

class ActionMenuView
{
    protected $instance;

    public function __construct(ActionMenu $instance)
    {
        $this->instance = $instance;
    }

    public function __toString()
    {
        return <<<JS
new CapsuleCmsActionMenu($this->instance, $('body'));
JS;
    }
}