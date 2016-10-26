<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 23.01.2013 15:05:37 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\DialogWindow;

/**
 * DialogWindow.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string     $instanceName
 * @property int        $left
 * @property int        $top
 * @property int        $width
 * @property int        $height
 * @property int        $minWidth
 * @property int        $minHeight
 * @property string     $caption
 * @property float      $opacity
 * @property string     $content
 * @property boolean    $hidden
 */
                        
class DialogWindow implements \JsonSerializable
{
    /**
     * Properties
     *
     * @var array
     */
    private $data = array(
            'instanceName' => null,
            'left'      => 100,
            'top'       => 100,
            'width'     => 320,
            'height'    => 240,
            'minWidth'  => 100,
            'minHeight' => 100,
            'caption'   => 'Untitled window',
            'opacity'   => null,
            'content'   => '',
            'hidden'    => false);
                        
    /**
     * Хранилище экземпляров
     *
     * @var array
     */
    private static $instances = array();
    
    /**
     * Constructor
     *
     * @param string $instance_name
     * @return self
     * @throws Exception
     */
    public function __construct($instance_name) {
        if (array_key_exists($instance_name, self::$instances)) {
            $msg = 'Instance name "' . $instance_name . '" already exists';
            throw new \Exception($msg);
        }
        self::$instances[$instance_name] = $this;
        $this->instanceName = $instance_name;
    }
    
    /**
     * Getter
     *
     * @param string $name
     * @throwsException
     * @return mixed
     */
    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @throws Exception
     * @return mixed
     */
    public function __set($name, $value) {
        if (array_key_exists($name, $this->data)) {
            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
                return $this;
            }
            $this->data[$name] = $value;
            return $this;
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new \Exception($msg);
    }
    
    /**
     *
     * @param unknown $value
     */
    protected function setHidden($value) {
        $this->data['hidden'] = $value ? true : false;
    }
    
    /**
     * Возвращает json объект для передачи в JS конструктор
     *
     * @param void
     * @return string
     */
    public function jsonSerialize() {
        return $this->data;
    }
}