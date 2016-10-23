<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 25.05.2013 23:36:21 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Filter;

use Capsule\Core\Singleton;

/**
 * Inflector.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Inflector extends Singleton
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * Возвращает таблицу преобразованиия символов camelCase и underscore
     *
     * @param boolean $flip
     * @return array
     */
    private function getSymbolConversionTable($flip = false)
    {
        $key = 'symbol_conversion_table';
        if (!isset($this->data[$key])) {
            $keys = range('A', 'Z');
            $values = str_split('_' . join('_', range('a', 'z')), 2);
            $this->data[$key]['normal'] = array_combine($keys, $values);
            $this->data[$key]['flip'] = array_flip($this->data[$key]['normal']);
        }
        return $this->data[$key][$flip ? 'flip' : 'normal'];
    }

    /**
     * Возвращает имя соответствующей таблицы таблицы
     *
     * @param string|object $class
     * @return string
     */
    public function getAssociatedTable($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $key = 'class_to_table';
        if (!isset($this->data[$key][$class])) {
            $this->data[$key][$class] = trim(preg_replace('/[^a-zA-Z0-9]+/',
                '_', strtr($class, $this->getSymbolConversionTable())), '_');
        }
        return $this->data[$key][$class];
    }

    /**
     * Возвращает имя класса включая пространство имен, удалив разделители
     *
     * @param string|object $class
     * @return string
     */
    public function getClassKey($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $key = __FUNCTION__;
        if (!isset($this->data[$key][$class])) {
            $this->data[$key][$class] = trim(
                preg_replace('/[^a-zA-Z0-9]+/', '', $class), '_');
        }
        return $this->data[$key][$class];
    }

    /**
     * Возвращает имя свойства объекта
     *
     * @param string $field имя поля таблицы в базе данных
     * @return string
     */
    protected function convertFieldToProperty($field)
    {
        return strtr($field, $this->getSymbolConversionTable(true));
    }

    /**
     * Возвращает имя соответствующего свойства
     *
     * @param string $field имя поля таблицы в базе данных
     * @return string
     */
    public function getAssociatedProperty($field)
    {
        $key = 'field_to_property';
        if (!isset($this->data[$key][$field])) {
            $property = $this->convertFieldToProperty($field);
            $this->data[$key][$field] = $property;
            $this->data['property_to_field'][$property] = $field;
        }
        return $this->data[$key][$field];
    }

    /**
     * Возвращает массив имен соответствующих свойств
     *
     * @param array $fields массив имен полей таблицы в базе данных
     * @return array
     */
    public function getAssociatedProperties(array $fields)
    {
        $properties = array();
        foreach ($fields as $field) {
            $properties[] = $this->getAssociatedProperty($field);
        }
        return $properties;
    }

    /**
     * Возвращает имя поля таблицы в базе данных
     *
     * @param string $property имя свойства объекта
     * @return string
     */
    protected function convertPropertyToField($property)
    {
        return trim(strtr($property, $this->getSymbolConversionTable()), '_');
    }

    /**
     * Возвращает имя соответствующего поля таблицы в базе данных
     *
     * @param string $property имя свойства объекта
     * @return string
     */
    public function getAssociatedField($property)
    {
        $key = 'property_to_field';
        if (!isset($this->data[$key][$property])) {
            $field = $this->convertPropertyToField($property);
            $this->data[$key][$property] = $field;
            $this->data['field_to_property'][$field] = $property;
        }
        return $this->data[$key][$property];
    }

    /**
     * Возвращает массив имен полей таблицы в базе данных
     *
     * @param array $properties массив имен соответствующих свойств
     * @return array
     */
    public function getAssociatedFields(array $properties)
    {
        $fields = array();
        foreach ($properties as $property) {
            $fields[] = $this->getAssociatedField($property);
        }
        return $fields;
    }
}