<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 04.11.2016
 * Time: 20:19
 */

namespace App\Cms\Ui\MainMenu;


class MenuItem
{
    /**
     * @var MainMenu|static
     */
    protected $container;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var static[]
     */
    protected static $instances = [];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * Punct constructor.
     * @param MenuItem|MainMenu $container
     */
    public function __construct($container)
    {
        if ($container instanceof MainMenu) {
            $this->container = $container;
            $this->id = $container->getInstanceName() . '-' . sizeof(static::$instances);
            static::$instances[$this->id] = $this;
            return;
        }
        if ($container instanceof static) {
            $this->container = $container;
            $this->id = $container->getId() . '-' . sizeof(static::$instances);
            static::$instances[$this->id] = $this;
            return;
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return MenuItem
     */
    public function newSubMenuItem()
    {
        $menu_item = new MenuItem($this);
        $this->items[$menu_item->getId()] = $menu_item;
        return $menu_item;
    }
}