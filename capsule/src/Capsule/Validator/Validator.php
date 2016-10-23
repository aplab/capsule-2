<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2013-2013                                                   |
// +---------------------------------------------------------------------------+
// | 15.04.2013 10:16:13 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Validator;

use Capsule\Common\String;

/**
 * ValidatorAbstract.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property string $name
 * @property string $message
 */
abstract class Validator implements ValidatorInterface
{
    /**
     * Свойства объекта
     *
     * @var array
     */
    protected $data = array();

    /**
     * Общие свойства класса
     *
     * @var array
     */
    protected static $common = array();

    /**
     * Значение проверяемого параметра.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Сообщение об ошибке
     *
     * @var string
     */
    protected $message;

    /**
     * Флаг: прошло значение проверку(валидно) или нет
     *
     * @var boolean
     */
    protected $isValid = false;

    /**
     * Флаг: Валидация была проведена.
     *
     * @var boolean
     */
    protected $validationWasPerformed = false;

    /**
     * Обозначение методов-обработчиков переменных в сообщениях об ошибке
     *
     * @var string
     */
    const PLACEHOLDER_SUFFIX = 'Placeholder';

    /**
     * Constructor
     *
     * @param void
     * @return self
     */
    public function __construct()
    {
        $this->messageTemplates = array();
    }

    /**
     * @param void
     * @return void
     * @throws Exception
     */
    protected function setValidationWasPerformed()
    {
        $msg = 'Cannot set it directly.';
        throw new Exception($msg);
    }

    /**
     * Возвращает флаг: Валидация была проведена.
     *
     * @param void
     * @return boolean
     */
    protected function getValidationWasPerformed()
    {
        return $this->validationWasPerformed;
    }

    /**
     * Задает шаблоны сообщений
     *
     * @param array $value
     * @param string $name
     * @return $this
     */
    protected function setMessageTemplates(array $value, $name)
    {
        if (!array_key_exists($name, $this->data)) {
            $this->data[$name] = array();
        }
        $this->data[$name] = array_replace($this->data[$name], $value);
        return $this;
    }

    /**
     * @param void
     * @return void
     * @throws Exception
     */
    protected function getValue()
    {
        $msg = 'Cannot retrieve value directly. Use method getClean';
        throw new Exception($msg);
    }

    /**
     * @param void
     * @return void
     * @throws Exception
     */
    protected function setValue()
    {
        $msg = 'Cannot set value directly. Use method isValid';
        throw new Exception($msg);
    }

    /**
     * Disable direct data modification
     *
     * @param void
     * @return void
     * @throws Exception
     */
    protected function setData()
    {
        $msg = 'Cannot directly data modification';
        throw new Exception($msg);
    }

    /**
     * Возвращает значение свойства
     *
     * @param  string
     * @throws Exception
     * @return mixed
     */
    public function __get($name)
    {
        $getter = self::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        $msg = 'Unknown property: ' . get_class($this) . '::$' . $name;
        throw new Exception($msg);
    }

    /**
     * Возвращает значение свойства или значение по умолчанию, если свойство
     * не определено
     *
     * @param $name
     * @param  string
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $getter = self::_getter($name);
        if ($getter) {
            return $this->$getter($name);
        }
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return $default;
    }

    /**
     * Обрабатывает изменение значения свойства.
     *
     * @param  string $name
     * @param  mixed $value
     * @throws Exception
     * @return self
     */
    public function __set($name, $value)
    {
        $setter = self::_setter($name);
        if (method_exists($this, $setter)) {
            return $this->$setter($value, $name);
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * Возвращает список методов класса с учетом регистра
     *
     * @param void
     * @return array
     */
    protected function _listMethods()
    {
        $key = __FUNCTION__;
        $class = get_class($this);
        if (!isset(self::$common[$class][$key])) {
            self::$common[$class][$key] = get_class_methods($class);
        }
        return self::$common[$class][$key];
    }

    /**
     * Возвращает getter с учетом регистра
     *
     * @param void
     * @return string|false
     */
    protected function _getter($name)
    {
        $key = __FUNCTION__;
        $class = get_class($this);
        if (!isset(self::$common[$class][$key][$name])) {
            $getter = 'get' . ucfirst($name);
            self::$common[$class][$key][$name] =
                in_array($getter, $this->_listMethods()) ? $getter : false;
        }
        return self::$common[$class][__FUNCTION__][$name];

    }

    /**
     * Возвращает setter с учетом регистра
     *
     * @param string $name
     * @return string|false
     */
    protected function _setter($name)
    {
        $key = __FUNCTION__;
        $class = get_class($this);
        if (!isset(self::$common[$class][$key][$name])) {
            $setter = 'set' . ucfirst($name);
            self::$common[$class][$key][$name] =
                in_array($setter, $this->_listMethods()) ? $setter : false;
        }
        return self::$common[$class][$key][$name];
    }

    /**
     * Валидация
     *
     * @param mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->value = $value;
        $this->isValid = false;
        $this->validationWasPerformed = true;
        return $this->isValid;
    }

    /**
     * Возвращает значение проверяемого параметра после валидации.
     * @return mixed
     * @throws Exception
     * @param void
     */
    public function getClean()
    {
        if ($this->isValid) {
            return $this->value;
        }
        if (!$this->validationWasPerformed) {
            $msg = 'Validation was not performed.';
            throw new Exception($msg);
        }
        $msg = 'Clean value is not defined, because parameter is not valid.';
        throw new Exception($msg);
    }

    /**
     * Возвращает сообщение.
     *
     * (non-PHPdoc)
     * @see \Capsule\IValidator::getMessage()
     * @param void
     * @return string
     * @throws Exception
     */
    protected function getMessage()
    {
        if (!$this->validationWasPerformed) {
            $msg = 'Message is not defined, because validation was not performed.';
            throw new Exception($msg);
        }
        if ($this->isValid) {
            $msg = 'Message is not defined, because parameter is valid.';
            throw new Exception($msg);
        }
        return $this->message;
    }

    /**
     * Запрещает прямую установку сообщения об ошибке.
     *
     * @param void
     * @return void
     * @throws Exception
     */
    protected function setMessage()
    {
        $msg = 'Cannot set message directly.';
        throw new Exception($msg);
    }

    /**
     * Создает сообщение об ошибке
     *
     * @param string $key
     * @return string
     */
    protected function message($key)
    {
        $this->message = $this->messageTemplate($key);
        $placeholders = array();
        $matches = array();
        $methods = $this->_listMethods();
        preg_match_all('/%([^% ]+)%/', $this->message, $matches);
        if (is_array($matches) && isset($matches[1])) {
            $placeholders = $matches[1];
        }
        foreach ($placeholders as $key) {
            $replacement = '';
            $method = $key . self::PLACEHOLDER_SUFFIX;
            if (in_array($method, $methods)) {
                $replacement = $this->$method();
            } elseif (isset($this->$key)) {
                $replacement = $this->$key;
            }
            if (is_object($replacement)) {
                $replacement = 'object of class ' . get_class($replacement);
            }
            if (is_array($replacement)) {
                $replacement = 'Array';
            }
            $this->message = str_replace('%' . $key . '%',
                (string)$replacement, $this->message);
        }
        $this->message = String::replace('  ', ' ', $this->message);
    }

    /**
     * Возвращает нужный шаблон сообщения.
     *
     * @param string $key
     * @return string
     * @throws Exception
     */
    protected function messageTemplate($key)
    {
        if (isset($this->messageTemplates[$key])) {
            return $this->messageTemplates[$key];
        }
        $msg = 'Trying to create undefined message.';
        throw new Exception($msg);
    }

    /**
     * Возвращает тип значения для использования в сообщениях об ошибке
     *
     * @param void
     * @return string
     */
    protected function typePlaceholder()
    {
        return is_object($this->value) ?
            'object of class "' . get_class($this->value) . '"' :
            gettype($this->value);
    }

    /**
     * Возвращает значение для использования в сообщениях об ошибке
     *
     * @param void
     * @return string
     */
    protected function valuePlaceholder()
    {
        if (is_object($this->value)) {
            return 'object of class "' . get_class($this->value) . '"';
        }
        if (is_array($this->value)) {
            return 'Array';
        }
        if (is_scalar($this->value)) {
            return '"' . strval($this->value) . '"';
        }
        return gettype($this->value);
    }

    /**
     * Возвращает значение для использования в сообщениях об ошибке
     *
     * @param void
     * @return string
     */
    protected function namePlaceholder()
    {
        if (isset($this->name)) {
            if (is_scalar($this->name) && $this->name) {
                return '"' . strval($this->name) . '"';
            }
        }
        return '';
    }
}