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
 * Time: 11:02
 */

namespace App\Cms\Plugin\DesktopIcon\DesktopIcon;


use Capsule\Capsule;
use Capsule\Unit\NamedTsUsr;

class DesktopIcon extends NamedTsUsr
{
    protected function setName($v, $k)
    {
        $this->data[$k] = str_replace(' Capsule ' . Capsule::getInstance()->config->version, '', $v);
    }
}