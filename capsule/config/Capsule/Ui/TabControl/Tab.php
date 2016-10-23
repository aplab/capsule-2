<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2013 3:31:39 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\TabControl;

/**
 * Tab.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 *
 * @property string      $name
 * @property string|null $callback
 * @property string|null $bind
 * @property boolean $active
 */
class Tab
{
    protected $data = array(
            'name' => 'noname tab',
            'callback' => null,
            'active' => false,
            'bind' => null);
    
    /**
     * Getter
     *
     * @param string $name
     * @throwsException
     * @return mixed
     */
    public function __get($name) {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value) {
        $setter = 'set' . ucfirst($name);
        if (in_array($setter, get_class_methods($this))) {
            return $this->$setter($value, $name);
        }
        $this->data[$name] = $value;
        return $this;
    }
    
    /**
     * @param void
     * @return array
     */
    public function getData() {
        return $this->data;
    }
}