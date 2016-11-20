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


/**
 * Class Assets
 * @package Capsule\Tools\Assets
 */
class Assets
{
    /**
     *
     */
    const CLASS_CSS = __NAMESPACE__ . '\\Css';
    /**
     *
     */
    const CLASS_JS = __NAMESPACE__ . '\\Js';

    /**
     * @var Asset[]
     */
    protected $instances = [];

    /**
     * @param Asset $asset
     * @return $this
     */
    public function add(Asset $asset)
    {
        $this->instances[get_class($asset)][] = $asset;
        return $this;
    }

    /**
     * @return array|Asset
     */
    public function getCss()
    {
        return $this->instances[static::CLASS_CSS] ?? [];
    }

    /**
     * @return array|Asset
     */
    public function getJs()
    {
        return $this->instances[static::CLASS_JS] ?? [];
    }

    /**
     *
     */
    public function putCss()
    {
        echo join(PHP_EOL, $this->getCss());
    }

    /**
     *
     */
    public function putJs()
    {
        echo join(PHP_EOL, $this->getJs());
    }
}