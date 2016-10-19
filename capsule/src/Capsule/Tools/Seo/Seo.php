<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 24.08.2014 10:49:33 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Tools\Seo;

use Capsule\Capsule;
use Capsule\Tools\Sysinfo;
/**
 * Seo.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Seo
{
    protected static $exclude = array(
        'c1.aplab.ru',
        'aplab.ru'
    );
    
    private static $data = array();
    
    const FOLLOW = 'follow,index';
    
    /**
     * Добавляет атрибуты rel="nofollow" и target="_blank"
     * ко всем внешним ссылкам
     * Если требуется, чтобы внешняя ссылка была индексируема.
     * Чтобы пользователи не использовали это как уязвимость,
     * следует фильтровать входные данные
     * 
     * @param string
     * @return string
     */
    public static function nofollow($html) {
        $host = Sysinfo::host();
        $exclude = self::$exclude;
        if (!in_array($host, $exclude)) array_push($exclude, $host);
        array_walk($exclude, function(& $v, $k) {
            $v = preg_quote('//' . $v, '/');
        });
        $exclude = join('|', $exclude);
        return preg_replace_callback('/<a[^>]+/', function($matches) use ($exclude) {
            $link = $matches[0];
            if (strpos($link, self::FOLLOW) !== false) {
                return str_replace(self::FOLLOW, '', $link);
            }
            if (preg_match('/(href=\\S(?!' . $exclude . '))/i', $link)) {
                $link = rtrim(preg_replace('/(target=[\'"].*?[\'"]|rel=[\'"].*?[\'"])/', '', $link)) 
                . ' rel="nofollow" target="_blank"';
            }
            return $link;
        }, $html);
    }
    
    /**
     * Делает все ссылки абсолютными
     * 
     * @param string $html
     * @return string
     */
    public static function absolutize($html) {
        $host = Sysinfo::host();
        return preg_replace_callback('/<a[^>]+/', function($matches) use($host) {
            return preg_replace_callback(
                '/href=[\'"](?!\\/\\/|mailto:|[^\\/]*?:\\/\\/)\\/?(.*?)[\'"]/i', 
                function($m) use($host) {
                    return 'href="//' . Sysinfo::host() . 
                    (strlen($m[1]) ? '/' : '') . $m[1] . '"';
                }, 
                $matches[0]
            );
        }, $html);
    } 
}