<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 18.03.2014 6:34:15 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Url;

/**
 * Filter.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Filter
{
    /**
     * Это будет прикрепляться к началам строк URL
     *
     * @var string
     */
    public $base = '/';

    /**
     * Определять, что переданный url уже начинается с baseUrl
     * и не приписывать базовый url к началу строки повторно в том
     * случае, если он там уже есть.
     *
     * Пример:
     * baseUrl /admin
     * detectBase true
     * Строка /settings будет преобразована в /admin/settings
     * Строка /admin/settings будет преобразована в /admin/settings
     *
     * detect base false
     * Строка /settings будет преобразована в /admin/settings
     * Строка /admin/settings будет преобразована в /admin/admin/settings
     *
     * @var boolean
     */
    public $detectBase = true;

    /**
     * Если строка не начинается с символа "/" (без кавычек)
     * то он будет добавлен в начало строки
     *
     * @var boolean
     */
    public $autoRoot = true;

    /**
     * Соглашение, используемое в системе
     * Если в строке есть точка то это файл
     * Если в строке нет точки то это каталог
     *
     * @var boolean
     */
    public $handleDot = true;

    /**
     * Не обрабатывать следующие строки
     *
     * @var array
     */
    protected $exclude = array();

    /**
     * Добавить исключения.
     * Если задать null то очистить список исключений.
     * Вызов без аргументов просто возвращает массив список исключений.
     * В любом случае всегда возвращает массив список исключений.
     *
     * @param array
     * @return array
     */
    public function exclude()
    {
        $args = func_get_args();
        if (empty($args)) {
            return $this->exclude;
        }
        $arg = current($args);
        if (is_null($arg)) {
            $this->exclude = array();
        }
        if (is_array($arg)) {
            $this->exclude = $arg;
        } else {
            $this->exclude = array($arg);
        }
        return $this->exclude;
    }

    /**
     * Handler
     * @return string
     */
    public function __invoke()
    {
        $args = func_get_args();
        $parts = array();
        foreach ($args as $arg) {
            $tmp = $this->prepare($arg);
            if ($tmp) {
                $parts[] = $tmp;
            }
        }
        $url = $this->prepare(join('/', $parts));
        foreach ($this->exclude as $e) {
            if ($url == $e) {
                return $url;
            }
        }
        if ($this->autoRoot) {
            // не известно был ли / на входе
            $url = '/' . ltrim($url, '/');
        }
        if ($this->detectBase) {
            if ((0 !== strpos($url, $this->base)) && (0 === strpos($url, '/'))) {
                $url = $this->base . '/' . $url;
            }
        } else {
            if (0 === strpos($url, '/')) { // начинается с "/"
                $url = $this->base . $url;
            }
        }
        if ($this->handleDot) {
            $url = rtrim($url, '/');
            if (false === strpos($url, '.')) {
                $url .= '/';
            }
        }
        return $this->prepare($url);
    }

    /**
     * helper
     *
     * @param string $part
     * @return string
     */
    private function prepare($part)
    {
        return preg_replace('|/{2,}|', '/', str_replace('\\', '/', trim($part)));
    }
}