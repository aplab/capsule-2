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

namespace Capsule\Ui;

use Capsule\Core\Singleton;
use Capsule\Exception;
use Capsule\I18n\I18n;
/**
 * WebUi.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Ui extends Singleton
{
    /**
     * Getter
     * Возвращает секцию по id
     *
     * @param string $name
     * @return Section
     */
    public function __get($name)
    {
        return  Section::getElementById($name);
    }

    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return Section
     * @throws Exception
     */
    public function __set($name, $value)
    {
        throw new Exception('Object has no properties');
    }

    /**
     * The __invoke() method is called when a script tries to call an object as a function.
     * Подключает шаблон секции, которая передана в качестве параметра.
     *
     * @param Section $o
     * @return string;
     */
    public function __invoke(Section $o)
    {
        $template = $o->template;
        if ($template) {
            ob_start();
            include $template;
            return ob_get_clean();
        }
        return '';
    }
}