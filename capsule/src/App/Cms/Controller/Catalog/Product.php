<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2015                                                   |
// +---------------------------------------------------------------------------+
// | 12 мая 2015 г. 0:35:17 YEKT 2015                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Controller\Catalog;

use Capsule\User\Env;
use Capsule\Superglobals\Post;
use Capsule\DataStruct\ReturnValue;
use Capsule\I18n\I18n;
use Capsule\Common\Filter;
use Capsule\DataModel\DataModel;
use Capsule\Module\Catalog\Value;
use App\Cms\Controller\NestedItem;
use Capsule\Core\Fn;
/**
 * Product.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Product extends NestedItem
{
    protected $moduleClass = 'Capsule/Module/Catalog/Product';

    const CONTAINER_ID = 'containerId';

    /**
     * @param string $class
     * @return ReturnValue
     */
    protected function createElement($class) {
        $item = ($class instanceof DataModel) ? $class : new $class;
        $this->filterByContainer = Env::getInstance()->get($this->filterByContainerKey());
        if (Filter::digit($this->filterByContainer)) $item->{self::CONTAINER_ID} = $this->filterByContainer;
        $item->attrInit();// Strictly after assign container id
        $config = $class::config();
        $properties = $config->properties;
        $post = Post::getInstance();
        $ret = new ReturnValue;
        $ret->item = $item;
        foreach ($properties as $name => $property) {
            if ($class::isKey($name)) {
                continue;
            }
            if (!isset($property->formElement)) {
                continue;
            }
            if (!isset($post->$name)) {
                $ret->status = 1;
                return $ret;
            }
            try {
                $item->$name = $post->$name;
            } catch (\Exception $e) {
                $ret->status = 1;
                $this->ui->alert->append(I18n::_($e->getMessage()));
            }
        }
        if ($ret->status) {
            return $ret;
        }
        try {
            $item->store();
            $item->attrPush();
            $value = forward_static_call(array(Fn::cc('Value', $item), 'getInstance'));
            $value->store();
        } catch (\Exception $e) {
            $this->ui->alert->append(I18n::_($e->getMessage()));
            $ret->status = 1;
            return $ret;
        }
        $ret->status = 0;
        return $ret;
    }

    /**
     * @param string $class
     * @return ReturnValue
     */
    protected function updateItem($item) {
        $post = Post::getInstance();
        $k = self::CONTAINER_ID;
        if (isset($post->$k)) {
            try {
                $item->$k = $post->$k;
            } catch (\Exception $e) {
                // return old $k property value if needed
            }
        }
        $item->attrInit();
        $item->attrPull();
        $config = $item::config();
        $properties = $config->properties;
        $ret = new ReturnValue;
        $ret->item = $item;
        foreach ($properties as $name => $property) {
            if ($item::isKey($name)) {
                continue;
            }
            if (!isset($property->formElement)) {
                continue;
            }
            if (!isset($post->$name)) {
                $ret->status = 1;
                return $ret;
            }
            try {
                $item->$name = $post->$name;
            } catch (\Exception $e) {
                $ret->status = 1;
                $this->ui->alert->append(I18n::_($e->getMessage()));
            }
        }
        if (isset($ret->status) && $ret->status) {
            return $ret;
        }
        try {
            $item->store();
            $item->attrPush();
            $value = forward_static_call(array(Fn::cc('Value', $item), 'getInstance'));
            $value->store();
        } catch (\Exception $e) {
            $this->ui->alert->append(I18n::_($e->getMessage()));
            $ret->status = 1;
            return $ret;
        }
        $ret->status = 0;
        return $ret;
    }
}