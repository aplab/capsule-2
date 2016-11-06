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


/**
 * Class MenuItem
 * @package App\Cms\Ui\MainMenu
 */
class MenuItem implements \JsonSerializable
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
     * @var Action
     */
    protected $action;

    /**
     * @var Icon
     */
    protected $icon;

    /**
     * @var static[]
     */
    protected static $instances = [];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var string
     */
    protected $caption;

    /**
     * @var bool
     */
    public $disabled = false;

    /**
     * Punct constructor.
     * @param MenuItem|MainMenu $container
     * @param null $caption
     * @param Action $action
     * @param Icon $icon
     * @throws Exception
     */
    public function __construct($container, $caption = null, Action $action = null, Icon $icon = null)
    {
        if ($caption) {
            settype($caption, 'string');
        }
        if ($container instanceof MainMenu) {
            $this->container = $container;
            $this->id = $container->getInstanceName() . '-' . sizeof(static::$instances);
            static::$instances[$this->id] = $this;
            $this->caption = $caption ?: $this->id;
        } elseif ($container instanceof static) {
            $this->container = $container;
            $this->id = $container->getId() . '-' . sizeof(static::$instances);
            static::$instances[$this->id] = $this;
            $this->caption = $caption ?: $this->id;
        } else {
            throw new Exception('Wrong container type');
        }
        if ($action) {
            $this->action = $action;
        }
        if ($icon) {
            $this->icon = $icon;
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
     * @param null $caption
     * @return MenuItem
     */
    public function newSubMenuItem($caption = null, Action $action = null, Icon $icon = null)
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
        $data = [
            'id' => $this->id,
            'caption' => $this->caption,
            'disabled' => !!$this->disabled,
            'items' => array_map(function(MenuItem $i) {
                return $i->jsonSerialize();
            }, $this->items)
        ];
        if ($this->action) {
            $data['action'] = $this->action->jsonSerialize();
        }
        if ($this->icon) {
            $data['icon'] = $this->icon->jsonSerialize();
        }
        return $data;
    }
}