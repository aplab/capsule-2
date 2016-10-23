<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 24.05.2014 7:22:57 YEKT 2014                                              |
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

use Capsule\Core\Singleton;
use Capsule\Url\Path;
use Capsule\Core\Fn;
use PHP\Exceptionizer\Exceptionizer;
/**
 * Router.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Router extends Singleton
{
    /**
     * части пути
     *
     * @var array
     */
    protected $path = array();
    
    /**
     * параметры
     *
     * @var array
     */
    protected $parameters = array();
    
    /**
     * все части включая части пути и параметры
     *
     * @var array
     */
    protected $parts = array();
    
    /**
     * Текущая страница
     *
     * @var Page
     */
    protected $page;
    
    /**
     * Массив данных об имеющихся страницах
     *
     * @var array
     */
    protected $data = array();
    
    /**
     * Возвращает текущую страницу
     *
     * @param void
     * @return CSSiteStructurePage
     */
    public function getPage() {
        return $this->page;
    }
    
    /**
     * Влзвращает параметры
     *
     * @param void
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }
    
    /**
     * Влзвращает количество параметров
     *
     * @param void
     * @return int
     */
    public function hasParameters() {
        return sizeof($this->parameters);
    }
    
    /**
     * Влзвращает путь
     *
     * @param boolean
     * @return string|array
     */
    public function getPath($as_string = true) {
        return $as_string ? '/' . Fn::join_ne('/', $this->path) : $this->path;
    }
    
    /**
     * Влзвращает все части
     *
     * @param boolean
     * @return string|array
     */
    public function getParts($as_string = false) {
        return $as_string ? '/' . Fn::join_ne('/', $this->parts) : $this->parts;
    }
    
    /**
     * Constructor
     *
     * @param void
     * @return self
     */
    protected function __construct() {
        $data = Structure::getInstance()->getRoutesList();
        array_walk($data, function ($value, $key) {
            $value = Structure::normalizePath($value);
            $parts = explode('/', trim($value, '/'));
            $this->data[$value]['parts'] = $parts;
            $this->data[$value]['count'] = sizeof($parts);
            $this->data[$value]['length'] = strlen(join($parts));
            $this->data[$value]['key'] = $value;
        });
        uasort($this->data, function($a, $b) {
            return $a['count'] === $b['count']
                ? ($a['length'] === $b['length'] ? 0 : ($a['length'] < $b['length'] ? 1 : -1))
                : ($a['count'] < $b['count'] ? 1 : -1);
        });
        $this->route();
    }
    
    /**
     * Обработка пути
     *
     * @param void
     * @return void
     */
    protected function route() {
        // @TODO костыль, придумать как быть в случае главной страницы когда пустой Path
        $this->parts = Path::getInstance()->data ?: array('');
        $count = sizeof($this->parts);
        $current_page = null;
        foreach ($this->data as $data_item) {
            $intersect = array_intersect($data_item['parts'], $this->parts);
            $match_num = sizeof($intersect);
            if ($match_num === $data_item['count']) {
                $page = Structure::getInstance()->get($data_item['key']);
                if (!$page) {
                    continue;
                }
                if (!$page->active) {
                    continue;
                }
                $parameters = array_values(array_diff($this->parts, $data_item['parts']));
                if (!$this->checkParam($page->paramNum, $parameters)) {
                    continue;
                }
                $current_page = $page;
                break;
            }
        }
        if (is_null($current_page)) {
            return;
        }
        $this->page = $current_page;
        $this->path = $intersect;
        $this->parameters = $parameters;
    }
    
    /**
     * Проверяет соответствие количества параметров заданному в конфигурации
     * 
     * @param mixed $config
     * @param array $parameters
     */
    private function checkParam($config, array $parameters = array()) {
        $pn = sizeof($parameters);
        if (is_null($config) && !$pn) {
            // Конфиг не задан и параметров нет
            return true;
        }
        if (is_scalar($config)) {
            settype($config, 'string');
            if (!ctype_digit($config)) { 
                $msg = 'Wrong paramNum value';
                throw new \Exception($msg);
            }
            settype($config, 'int');
            // Точно заданное количество параметров
            return $pn === $config;
        }
        if (!is_array($config)) return false;
        $e = new Exceptionizer;
        if (array_key_exists('min', $config) && array_key_exists('max', $config)) {
            $min = $config['min'];
            $max = $config['max'];
            settype($min, 'string');
            settype($max, 'string');
            if (!ctype_digit($min)) {
                $msg = 'Wrong paramNum min value';
                throw new \Exception($msg);
            }
            if (!ctype_digit($max)) {
                $msg = 'Wrong paramNum max value';
                throw new \Exception($msg);
            }
            settype($min, 'int');
            settype($max, 'int');
            if ($pn < $min) return false;
            if ($pn > $max) return false;
            return true;
        }
        if (array_key_exists('min', $config)) {
            $min = $config['min'];
            settype($min, 'string');
            if (!ctype_digit($min)) {
                $msg = 'Wrong paramNum min value';
                throw new \Exception($msg);
            }
            settype($min, 'int');
            if ($pn < $min) return false;
            return true;
        }
        if (array_key_exists('max', $config)) {
            $max = $config['max'];
            settype($max, 'string');
            if (!ctype_digit($max)) {
                $msg = 'Wrong paramNum max value';
                throw new \Exception($msg);
            }
            settype($max, 'int');
            if ($pn > $max) return false;
            return true;
        }
        $msg = 'Wrong paramNum value';
        throw new \Exception($msg);
    }
}