<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2013 3:31:29 YEKT 2013                                             |
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
 * Punct.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Punct
{
    /**
     * Наименование
     *
     * @var string
     */
    private $name;

    /**
     * Массив объектов - подпунктов
     *
     * @var array
     */
    private $subPuncts = array();

    /**
     * Constructor
     *
     * @param string $name
     * @param string $action
     * @return Punct
     */
    public function __construct($name) {
        settype($name, 'string');
        $this->name = $name;
    }

    /**
     * Добавляет подпункт
     *
     * @param DropDownMenuSubPunct $sub_punct
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
}