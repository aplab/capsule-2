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

namespace App\Cms\Ui\DataModel\ObjectEditor\Element;

use Capsule\DataModel\Config\Properties\FormElement;
use Capsule\DataModel\Config\Properties\Property;
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
    protected static $counter = 0;
    
    /**
     * Internal data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Принимает ссылку на объект и имя свойства
     *
     * @param DataModel $model
     * @param FormElement $form_element
     */
    public function __construct(DataModel $model, Property $property, FormElement $form_element)
    {
        $this->data['id'] = ++static::$counter;
        $this->data['model'] = $model;
        $this->data['formElement'] = $form_element;
        $this->data['property'] = $property;
        if (isset($model->{$property->name})) {
            $this->data['value'] = $model->{$property->name};
            $this->data['hasValue'] = true;
        }
    }
    
    /**
     * Getter
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }
    
    /**
     * Setter
     *
     * @param string $name
     * @param mixed $value
     * @return self
     */
    public function __set($name, $value)
    {
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
    public function update(\SplSubject $group)
    {
        
    }
}