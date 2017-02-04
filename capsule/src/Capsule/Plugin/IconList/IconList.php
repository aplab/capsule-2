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
 * Time: 11:06
 */

namespace Capsule\Plugin\IconList;


use Capsule\Capsule;
use Capsule\Component\Path\Path;
use Capsule\Core\Singleton;

/**
 * Class IconList
 * @package Capsule\Plugin\IconList
 */
class IconList extends Singleton
{
    /**
     * @var Path
     */
    private $path;

    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * IconList constructor.
     */
    protected function __construct()
    {
        $this->path = new Path(
            Capsule::getInstance()->documentRoot,
            Capsule::getInstance()->config->plugin->IconList->faRelativePath
        );
        $this->iterator = new Iterator($this->path);
        $this->iterator->uasort(function($a, $b) {
            return $a <=> $b;
        });
    }

    /**
     * @return Iterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }
}