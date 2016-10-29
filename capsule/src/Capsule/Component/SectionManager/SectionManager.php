<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 28.10.2016
 * Time: 7:14
 */

namespace Capsule\Component\SectionManager;


use Capsule\Core\Singleton;

abstract class SectionManager extends Singleton
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