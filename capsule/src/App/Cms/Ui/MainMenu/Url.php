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
 * Time: 23:30
 */

namespace App\Cms\Ui\MainMenu;


class Url extends Action
{
    protected $url;
    protected $target;

    public function __construct($url, $target = null)
    {
        $this->url = $url;
        if ($target) {
            $this->target = $target;
        }
    }

    public function JsonSerialize()
    {
        if ($this->target) {
            return [
                'type' => self::TYPE_URL,
                'url' => $this->url,
                'target' => $this->target
            ];
        }
        return [
            'type' => self::TYPE_URL,
            'url' => $this->url
        ];
    }
}