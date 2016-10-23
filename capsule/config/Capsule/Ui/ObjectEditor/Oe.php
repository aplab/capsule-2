<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 31.03.2014 8:20:47 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\ObjectEditor;

use Capsule\DataModel\DataModel;
use Capsule\I18n\I18n;
use Capsule\Core\Fn;
use Capsule\DataModel\Config\Properties\FormElement;
use Capsule\Capsule;
use Iterator, Countable;
/**
 * Oe.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property DataModel $model
 * @property Config $config
 * @property array $groups
 */
class Oe implements Iterator, Countable
{
    /**
     * Internal data
     *
     * @var array
     */
    protected $data = array(
    	'model' => null,
        'config' => null,
        'properties' => array(),
        'groups' => array()
    );

    /**
     * Create instance
     *
     * @param DataModel $object
     * @return self
     */
    public function __construct(DataModel $object, $instance_name) {
        $this->data['model'] = $object;
        $this->data['config'] = $object->config();
        $this->data['instanceName'] = $instance_name;
        $this->configureProperties();
        $this->configureGroups();
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
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * Disable set model directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setModel($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Disable set instance_name
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setInstanceName($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Disable set config directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setConfig($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Disable set properties directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setProperties($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Disable set groups directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setGroups($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }

    /**
     * Конфигурация свойств для формы
     *
     * @param void
     * @return void
     */
    protected function configureProperties() {
        $properties = $this->config->properties;
        $tmp = array();
        foreach ($properties as $property_name => $property) {
            $form_element = $property->get('formElement');
            if (!is_array($form_element)) {
                continue;
            }
            foreach ($form_element as $form_item) {
                if ($form_item instanceof FormElement) {
                    if (!isset($form_item->order)) {
                        $form_item->order = 0;
                    }
                    $tmp[] = array(
                        'property' => $property,
                        'name' => $property_name,
                        'form_element' => $form_item
                    );
                }
            }
        }
        usort ($tmp, function($a, $b) {
            if ($a['form_element']->order == $b['form_element']->order) {
                return 0;
            }
            return ($a['form_element']->order < $b['form_element']->order) ? -1 : 1;
        });
        $this->data['properties'] = $tmp;
    }

    /**
     * Конфигурация вкладок для формы
     *
     * @param void
     * @return void
     */
    protected function configureGroups() {
        $groups = & $this->data['groups'];
        $element_ns = self::ns() . '\\Element';
        foreach ($this->properties as $property_data) {
            if (isset($property_data['form_element']->tab)) {
                $tab_name = $property_data['form_element']->tab;
                if (!array_key_exists($tab_name, $groups)) {
                    $group = new Group;
                    $group->name = $tab_name;
                    $groups[$tab_name] = $group;
                }
            }
        }
        foreach ($this->properties as $property_data) {
            if (isset($property_data['form_element']->tab)) {
                $tab_name = $property_data['form_element']->tab;
                $element_class = $property_data['form_element']->type;
                $element_class = Fn::cc($element_class, $element_ns);
                $element = new $element_class($this->model, $property_data['name'], $property_data['form_element']);
                $groups[$tab_name]->attach($element);
            }
        }
        $to = $this->model->config()->tabOrder;
        foreach ($this->groups as $name => $group) {
            if (isset($to->$name)) $group->order = $to->$name;
        }
        $tmp = $this->data['groups'];
        uasort($tmp, function($a, $b) {
            $ao = $a->order;
            $bo = $b->order;
            if (is_null($ao) && is_null($bo)) return 0;
            if ($ao === $bo) return 0;
            if (is_null($ao)) return 1;
            if (is_null($bo)) return -1;
            return ($a->order < $b->order) ? -1 : 1;
        });
        $this->data['groups'] = $tmp;
    }

    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count() {
        return sizeof($this->data['groups']);
    }

    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current() {
        return current($this->data['groups']);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key() {
        return key($this->data['groups']);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next() {
        next($this->data['groups']);
    }

    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() {
        reset($this->data['groups']);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return boolean
     */
    public function valid() {
        return ($this->key() !== null);
    }

    /**
     * Привязка к namespace
     *
     * @param void
     * @return string
     */
    public static function ns() {
        return __NAMESPACE__;
    }
}