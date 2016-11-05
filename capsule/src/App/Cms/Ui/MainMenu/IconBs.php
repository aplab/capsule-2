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
 * Time: 23:38
 */

namespace App\Cms\Ui\MainMenu;


/**
 * Bootstrap icon
 *
 * Class IconBs
 * @package App\Cms\Ui\MainMenu
 */
class IconBs extends Icon
{
    /**
     * @var
     */
    protected $name;

    /**
     * IconBs constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }


    /**
     * @return array
     */
    public function JsonSerialize()
    {
        return [
            'type' => 'bootstrap',
            'name' => $this->name
        ];
    }
}