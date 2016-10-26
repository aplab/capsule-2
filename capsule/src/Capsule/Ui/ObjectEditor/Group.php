<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 01.04.2014 7:19:24 YEKT 2014                                              |
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

use Capsule\I18n\I18n;
use Capsule\Ui\ObjectEditor\Element\IElement;
use Iterator, Countable;
/**
 * Group.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $name
 * @property int $order
 */
class Group implements IGroup, Iterator, Countable
{
    /**
     * Internal data
     *
     * @var array
     */
    private $data = array(
        'name' => null,
        'order' => null,
        'elements' => array()
    );
    
    /**
     * (non-PHPdoc)
     * @see SplSubject::attach()
     */
    public function attach(\SplObserver $element) {
        if (!($element instanceof IElement)) {
            $msg = 'The parameter must be instance of IElement';
            throw new \InvalidArgumentException($msg);
        }
        if (in_array($element, $this->data['elements'], true)) {
            $msg = I18n::t('Element already exists: ') . $element->name;
            throw new \RuntimeException($msg);
        }
        $this->data['elements'][] = $element;
        $this->notify();
    }
    
    /**
     * (non-PHPdoc)
     * @see SplSubject::detach()
     */
    public function detach(\SplObserver $element) {
        if (!($element instanceof IElement)) {
            $msg = 'The parameter must be instance of IElement';
            throw new \InvalidArgumentException($msg);
        }
        $keys = array_keys($this->data['elements'], $element, true);
        foreach ($keys as $key) {
            unset($this->data['elements'][$key]);
        }
        $this->notify();
    }
    
    /**
     * (non-PHPdoc)
     * @see SplSubject::notify()
     */
    public function notify() {
        foreach ($this->data['elements'] as $existent) {
            $existent->update($this);
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
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }
    
    /**
     * Disable set elements directly
     *
     * @param mixed $value
     * @param string $name
     */
    protected function setElements($value, $name) {
        $msg = I18n::t('Readonly property: ') . get_class($this) . '::$' . $name;
        throw new \RuntimeException($msg);
    }
    
    /**
     * count(): defined by Countable interface.
     *
     * @see    Countable::count()
     * @return integer
     */
    public function count() {
        return sizeof($this->data['elements']);
    }
    
    /**
     * current(): defined by Iterator interface.
     *
     * @see    Iterator::current()
     * @return mixed
     */
    public function current() {
        return current($this->data['elements']);
    }
    
    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key() {
        return key($this->data['elements']);
    }
    
    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next() {
        next($this->data['elements']);
    }
    
    /**
     * rewind(): defined by Iterator interface.
     *
     * @see    Iterator::rewind()
     * @return void
     */
    public function rewind() {
        reset($this->data['elements']);
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
}