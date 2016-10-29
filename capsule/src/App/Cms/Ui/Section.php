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

use Capsule\Component\Path\ComponentTemplatePath;
use Capsule\Component\SectionManager\Section as s;
use Capsule\Common\Path;
/**
 * Section.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Section extends s
{
    /**
     * Возвращает путь к файлу шаблона
     *
     * @param unknown $name
     * @throws Exception
     * @return \Capsule\Section
     */
    protected function getTemplate($name)
    {
        if ($this->id) {
            $path = new ComponentTemplatePath($this, $this->id);
        } else {
            return null;
        }
        if (file_exists($path)) {
            $this->data[$name] = $path;
            return $path;
        }
        return null;
    }

    /**
     * implicit conversion to a string
     *
     * (non-PHPdoc)
     * @see \Capsule\WebUiSection::__toString()
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        $ui = SectionManager::getInstance();
        try {
            return $ui($this);
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }
}