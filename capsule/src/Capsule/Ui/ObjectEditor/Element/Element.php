<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 01.04.2014 6:53:03 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\ObjectEditor\Element;

use Capsule\DataModel\DataModel;
use Capsule\I18n\I18n;
/**
 * Element.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property DataModel $model
 * @property Config $config
 * @property string $name
 */
abstract class Element implements IElement
{
    /**
     * Содержит счетчик для автоматической генерации id элементов
     * 
     * @var int
     */
    protected static $_idCounter = 1;
    
    /**
     * Internal data
     *
     * @var array
     */
    protected $data = array(
        'model' => null,
        'config' => null,
        'name' => null,
        'value' => null,
        'hasValue' => false,
        'property' => null,
        'settings' => array()
    );
    
    /**
     * Принимает ссылку на объект и имя свойства
     *
     * @param DataModel $object
     * @param string $name
     * @return self
     */
    public function __construct(DataModel $object, $name, $settings = array()) {
        $this->data['id'] = self::$_idCounter++;
        $this->data['model'] = $object;
        $this->data['config'] = $object->config();
        $this->data['name'] = $name;
        $this->data['settings'] = $settings;
        $this->data['class'] = get_class($object);
        $properties = $this->config->properties;
        $this->data['property'] = $properties->get($name);
        if (isset($object->$name)) {
            $this->data['value'] = $object->$name;
            $this->data['hasValue'] = true;
        }
    }
    
    /**
     * Getter
     *
     * @param string $name
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
            $this->$setter($value, $name);
            return $this;
        }
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }
    
    /**
     * (non-PHPdoc)
     * @see SplObserver::update()
     */
    public function update(\SplSubject $group) {
        
    }
}