<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 23.05.2014 8:01:30 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Website\Structure;

use Capsule\Core\Fn;
use App\Website\Cache;
use App\Website\Website;
/**
 * Unit.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Unit extends Element
{
    /**
     * Explicit conversion to string
     *
     * @param void
     * @return string
     */
	public function toString() {
	    return $this->content;
	}
	
	/**
	 * Подготавливает контент страницы.
	 * Заранее, т.к. внутри контента может происходить обработка зависимостей.
	 *
	 * @param void
	 * @return void
	 */
	public function prepare() {
	    if (!array_key_exists('content', $this->data)) {
	        if ($this->cache) {
	            $id = Fn::concat_ws('=>#', $this->pageId, $this->areaId, $this->id);
	            $cache = Cache::getInstance();
	            $content = $cache->get($id);
	            if (is_null($content)) {
	                $content = $this->_build();
	                $cache->set($id, $content, $this->cache);
	            }
	            $this->data['content'] = $content;
	        } else {
	            $this->data['content'] = $this->_build();
	        }
	    }
	}
	
	/**
	 * Собирает и возвращает контент страницы
	 *
	 * @param void
	 * @return string
	 */
	protected function _build() {
	    $namespace = Website::getInstance()->config->controller->defaultNamespace;
	    $controller_classname = Fn::create_classname($this->controller, $namespace);
	    $controller = new $controller_classname($this);
	    ob_start(); // буферизация
	    $controller->handle();
	    return ob_get_clean();
	}
}