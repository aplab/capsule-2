<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 11 мая 2015 г. 1:15:01 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Module\Catalog;

use Capsule\Traits\optionsDataList;
use Capsule\Db\Db;
use Capsule\DataModel\Config\Properties\Property;
use Capsule\Module\Catalog\Type\Type;
use Capsule\Core\Fn;
use Capsule\Unit\Nested\Item;


/**
 * Property.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Attribute extends Item
{
    use optionsDataList;

    /**
     * Кеширование атрибутов по секциям
     *
     * @var array
     */
    protected static $section_cache = array();

    /**
     * Виртуальное свойство
     *
     * @var Property
     */
    protected $virtualProperty;

    /**
     * Возвращает массив объектов атрибутов, привязанных к данномй разделу
     *
     * @param Section|int $section
     * @return Attribute[]
     */
    public static function section($section, $reload = false) {
        $db = Db::getInstance();
        $section_id = ($section instanceof Section) ? $section->id : intval($section, 10);
        $class = get_called_class();
        if (!isset(self::$cache[$class][$section_id])) {
            $attr_table = self::config()->table->name;
            $link_table = forward_static_call(array(Fn::cc('AttributeSectionLink',
                Fn::ns(get_called_class())), 'config'))->table->name;
            $sql = 'SELECT `at`.*, `lt`.`sort_order`, `lt`.`tab_name`
                    FROM `' . $attr_table . '` AS `at`
                    INNER JOIN `' . $link_table . '` AS `lt`
                    ON `at`.`id` = `lt`.`attribute_id`
                    WHERE `lt`.`container_id` = ' . $db->qt($section_id) . '
                    ORDER BY `lt`.`sort_order` ASC';
            self::$cache[$class][$section_id] = static::populate(Db::getInstance()->query($sql));
        }
        return self::$cache[$class][$section_id];
    }

    /**
     * Возвращает массив объектов атрибутов, привязанных к данномй продукту
     *
     * @param Product $product
     * @return array
     */
    public static function product(Product $product, $reload = false) {
        return static::section($product->get('containerId'), $reload);
    }

    /**
     * Создает свойство. Могут быть дополнительные свойства
     * sortOrder и tabName у атрибута, если атрибут загружен с привязкой к
     * секции или продукту
     *
     * @param void
     * @return Property|null
     */
    public function property() {
        if (is_null($this->virtualProperty)) {
            $type = Fn::cc(ucfirst($this->type), Type::ns());
            $config = $type::config();
            $config['title'] = $this->name;
            $config['name'] = $this->token ?: Catalog::ATTRIBUTE_TOKEN_PREFIX . $this->id;
            if (isset($config['formElement'])) {
                foreach ($config['formElement'] as & $f) {
                    $f['tab'] = $this->tabName;
                    $f['order'] = $this->sortOrder;
                }
            }
            $this->virtualProperty = new Property($config);
        }
        return $this->virtualProperty;
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