<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 08.02.2017
 * Time: 19:11
 */

namespace App\Cms\Component;


use Capsule\Traits\elementsByToken;
use Capsule\Unit\NamedTsUsr;

class Preferences extends NamedTsUsr
{
    use elementsByToken;
}