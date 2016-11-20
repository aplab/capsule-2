<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 20.11.2016
 * Time: 19:03
 */

namespace Capsule\Tools\Assets;


class Css extends Asset
{
    public function __toString()
    {
        if (!$this->versioning) {
            return '<link rel="stylesheet" href="' . $this->path . '">';
        }
        return '<link rel="stylesheet" href="' . $this->path . '?v=' . date('YmdHis', $this->filemtime) . '">';
    }
}