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
 * Time: 19:04
 */

namespace Capsule\Tools\Assets;


use Capsule\Capsule;
use Capsule\Component\Path\Path;

abstract class Asset
{
    protected $path;

    protected $versioning;

    protected $filemtime;

    public function __construct($path, $versioning = false)
    {
        $this->path = $path;
        $this->versioning = $versioning;
        if ($this->versioning) {
            $this->filemtime = filemtime(new Path(Capsule::getInstance()->documentRoot, $this->path));
        }
    }

    abstract public function __toString();
}