<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 18.10.2016
 * Time: 0:18
 */

namespace App\Cms\Ui;

use Capsule\Ui\Ui as i;
/**
 * Ui.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 *
 * @property Section $section
 */
class Ui extends i
{
    /**
     * Getter
     * Возвращает секцию по id.
     * Этот метод переопределен для того, чтобы работать с Section из
     * своего Namespace а не из родительского класса
     *
     * @param string $name
     * @return Section
     */
    public function __get($name)
    {
        return Section::getElementById($name);
    }
}