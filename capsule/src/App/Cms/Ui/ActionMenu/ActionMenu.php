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
 * Time: 20:09
 */

namespace App\Cms\Ui\ActionMenu;

class ActionMenu implements \JsonSerializable
{
    /**
     * @var static[]
     */
    protected static $instances = [];

    /**
     * @var string
     */
    protected $instanceName;

    /**
     * @var MenuItem[]
     */
    protected $items = [];

    /**
     * @return string
     */
    public function getInstanceName()
    {
        return $this->instanceName;
    }

    /**
     * MainMenu constructor.
     * @param string $instance_name
     * @throws Exception
     */
    public function __construct($instance_name)
    {
        if (array_key_exists($instance_name, static::$instances)) {
            throw new Exception('Instance name already exists: ' . $instance_name);
        }
        $this->instanceName = $instance_name;
        static::$instances[$instance_name] = $this;
    }

    /**
     * @param null $caption
     * @param Action $action
     * @param Icon $icon
     * @return MenuItem
     */
    public function newMenuItem($caption = null, Action $action = null, Icon $icon = null)
    {
        $menu_item = new MenuItem($this, $caption, $action, $icon);
        $this->items[$menu_item->getId()] = $menu_item;
        return $menu_item;
    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'instanceName' => $this->instanceName,
            'items' => array_map(function(MenuItem $i) {
                return $i->jsonSerialize();
            }, $this->items)
        ];
    }

    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}