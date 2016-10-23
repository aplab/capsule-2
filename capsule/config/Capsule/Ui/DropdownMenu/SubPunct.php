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

namespace Capsule\Ui\DropdownMenu;

/**
 * SubPunct.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class SubPunct
{
    /**
     * Наименование
     *
     * @var string
     */
    private $name;

    /**
     * Действие, выполняемое при клике (js)
     *
     * @var string
     */
    private $action;

    /**
     * Url ссылки или action формы
     *
     * @var string
     */
    private $url;
    
    /**
     * Target ссылки или формы
     *
     * @var string
     */
    private $target;
    
    /**
     * Путь к файлу иконки
     *
     * @var string
     */
    private $icon;

    /**
     * Флаг активен пункт или нет
     *
     * @var boolean
     */
    private $disabled;

    /**
     * Массив объектов - подпунктов
     *
     * @var array
     */
    private $subPuncts = array();

    /**
     * Массив объектов - параметров, передаваемых методом get
     *
     * @var array
    */
    private $getParameters = array();

    /**
     * Массив объектов - параметров, передаваемых методом post
     *
     * @var array
    */
    private $postParameters = array();

    /**
     * Constructor
     *
     * @param string $name
     * @param string $action
     * @return DropDownMenuPunct
    */
    public function __construct($name, $url = null) {
        settype($name, 'string');
        settype($url, 'string');
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * Добавляет подпункт
     *
     * @param SubPunct $sub_punct
     * @return void
     */
    public function addSubPunct(SubPunct $sub_punct) {
        $this->subPuncts[] = $sub_punct;
    }

    /**
     * Добавляет разделитель
     *
     * @param DropDownMenuDelimiter $delimiter
     * @return void
     */
    public function addDelimiter() {
        $this->subPuncts[] = new Delimiter;
    }

    /**
     * Добавляет параметр
     *
     * @param DropDownMenuParameter $parameter
     * @return void
     */
    public function addParameter(Parameter $parameter) {
        if ($parameter->isGet()) {
            $this->getParameters[] = $parameter;
        } else {
            $this->postParameters[] = $parameter;
        }
    }

    /**
     * Возвращает массив get параметров
     *
     * @param void
     * @return array
     */
    public function getGetParameters() {
        return $this->getParameters;
    }

    /**
     * Возвращает массив post параметров
     *
     * @param void
     * @return array
     */
    public function getPostParameters() {
        return $this->postParameters;
    }

    /**
     * Возвращает массив подпунктов и разделителей
     *
     * @param void
     * @return array
     */
    public function getSubPuncts() {
        return $this->subPuncts;
    }

    /**
     * Возвращает наименование
     *
     * @param void
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Возвращает action
     *
     * @param void
     * @return string
     */
    public function getAction() {
        return $this->action;
    }
    
    /**
     * Возвращает url
     *
     * @param void
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Возвращает target
     *
     * @param void
     * @return string
     */
    public function getTarget() {
        return $this->target;
    }
    
    /**
     * Возвращает путь к файлу изобржения
     *
     * @param void
     * @return string
     */
    public function getIcon() {
        return $this->icon;
    }

    /**
     * Задает путь к файлу изобржения
     *
     * @param void
     * @return string
     */
    public function setIcon($path) {
        settype($path, 'string');
        $this->icon = $path;
        return $this;
    }

    /**
     * Возвращает флаг активности пункта
     *
     * @param void
     * @return string
     */
    public function getDisabled() {
        return $this->disabled;
    }

    /**
     * Задает флаг активности пункта
     *
     * @param void
     * @return string
     */
    public function setDisabled($flag) {
        $this->disabled = $flag ? true : false;
        return $this;
    }
    
    public function setTarget($value) {
        $this->target = $value;
        return $this;
    }
    
    public function setAction($value) {
        $this->action = $value;
        return $this;
    }
}