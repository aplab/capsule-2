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

use Capsule\Ui\Section as s;
use Capsule\Core\Fn as f;
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
     * Default template file type for automatic generate template by id
     *
     * @var string
     */
    protected $defaultTplFileType = 'php';

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
            $path = new Path(
                self::_rootDir(),
                static::$localTplDir,
                f::concat_ws('.', $this->id, $this->defaultTplFileType));
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
        $ui = Ui::getInstance();
        try {
            return $ui($this);
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }
}