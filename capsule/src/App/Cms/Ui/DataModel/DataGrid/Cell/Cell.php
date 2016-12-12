<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 20.01.2014 20:42:50 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Ui\DataModel\DataGrid\Cell;

use App\Cms\Ui\DataModel\DataGrid\Col;
use Capsule\Capsule;
use Capsule\Component\Path\Path;
use Capsule\Core\Fn;
use Capsule\Core\ToStringExceptionizer;
use Capsule\DataModel\DataModel;

/**
 * Cell.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
abstract class Cell implements ICell
{
    /**
     * @var Path[]
     */
    protected static $template = [];

    /**
     * @var array
     */
    protected $data = array();

    /**
     * Cell constructor.
     * @param Col $col
     */
    public function __construct(Col $col)
    {
        $this->data['col'] = $col;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            $this->$setter($value, $name);
            return $this;
        }
        if (array_key_exists($name, $this->data)) {
            $msg = 'Readonly property: ' . $name;
            throw new \RuntimeException($msg);
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @param DataModel $item
     * @param $name
     */
    protected function setItem(DataModel $item, $name)
    {
        $this->data[$name] = $item;
        $this->data['val'] = $item->get($this->col->property->name);
    }

    protected function setId($value, $name)
    {
        $this->data[$name] = $value;
    }

    /**
     * @return Path
     */
    protected static function template()
    {
        $class = get_called_class();
        if (!array_key_exists($class, self::$template)) {
            $classname = Fn::get_classname($class);
            self::$template[$class] = new Path(
                Capsule::getInstance()->systemRoot,
                Capsule::DIR_TEMPLATES,
                __NAMESPACE__,
                strtolower($classname) . '.php'
            );
        }
        return self::$template[$class];
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        try {
            ob_start();
            include static::template();
            return ob_get_clean();
        } catch (\Exception $e) {
            set_error_handler(['\Capsule\Core\ToStringExceptionizer', 'errorHandler']);
            return ToStringExceptionizer::throwException($e);
        }
    }
}