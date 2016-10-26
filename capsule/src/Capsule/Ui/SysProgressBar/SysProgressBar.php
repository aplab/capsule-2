<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.2.1                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2010                                                   |
// +---------------------------------------------------------------------------+
// | 21.10.2010 22:36:19 YEKT 2010                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Engine
 */

namespace Capsule;

/**
 * SysProgressBar.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class SysProgressBar
{
    public static function init() {
        self::$cache = array();
        self::$path = dirname(__FILE__).'/themes/default';
        self::$path = String::replace(
            String::replace('\\', '/', Capsule::getInstance()->getDocumentRoot()), '', 
            String::replace('\\', '/', self::$path));
    }
    
    private static $path;
    
    private static $cache = null;
    
    protected $instanceName;
    
    protected $width;
    
    protected $value;
    
    public function __construct($instance_name, $width = 100, $value = 0) {
        if (is_null(self::$cache)) {
            self::init();
        }
        $this->instanceName = $instance_name;
        
        $this->width = Filter::digit($width);
        if (false === $width) {
            $msg = 'Invalid width';
        }
        
        
        $this->value = Filter::digit($value);
        if (false === $value) {
            $value = 0;
        }
        if ($value > 100) {
            $value = 100;
        }
        
        if (array_key_exists($this->instanceName, self::$cache)) {
            $msg = 'Instance name "' . $this->instanceName . '" already in use';
            throw new Exception($msg);
        }
        
        self::$cache[$this->instanceName] = $this;
    }
    
    public function html() {
        ob_start(); ?>
        <?=$this->instanceName?>.show(document.getElementById('sys-tabs-t0-associated'));
        <?php Ui::getInstance()->onLoadAddFragment(ob_get_clean());
        return
        '<script type="text/javascript">var ' . $this->instanceName . 
        ' = new SysProgressBar(\'' . self::$path . '\', ' . $this->width . ', ' . $this->value . ');' .
        '</script>';
    }
    
    public function __toString() {
        return $this->html();
    }
}