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

namespace App\Cms\Ui\ActionMenu;


/**
 * Class MenuItem
 * @package App\Cms\Ui\ActionMenu
 */
class MenuItem implements \JsonSerializable
{
    /**
     * @var ActionMenu
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
     * @var string
     */
    protected $caption;

    /**
     * @var bool
     */
    public $disabled = false;

    /**
     * Punct constructor.
     * @param ActionMenu $container
     * @param null $caption
     * @param Action $action
     * @param Icon $icon
     * @throws Exception
     */
    public function __construct(ActionMenu $container, $caption = null, Action $action = null, Icon $icon = null)
    {
        if ($caption) {
            settype($caption, 'string');
        }
        $this->container = $container;
        $this->id = $container->getInstanceName() . '-' . sizeof(static::$instances);
        static::$instances[$this->id] = $this;
        $this->caption = $caption ?: $this->id;
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
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            'id' => $this->id,
            'caption' => $this->caption,
            'disabled' => !!$this->disabled
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