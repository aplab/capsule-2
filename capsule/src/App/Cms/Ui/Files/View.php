<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 18.01.2013 4:42:07 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Ui\Storage;


use App\Cms\Ui\Ui;
use App\Cms\Ui\Stylesheet;
use App\Cms\Ui\Script;
use App\Cms\Cms;
/**
 * View.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class View
{
    private $object;
    
    private $instanceName;
    
    private static $assetsIncluded;
    
    public function __construct($instance_name) {
        if (!self::$assetsIncluded) {
            Ui::getInstance()->css->append(
                new Stylesheet(Cms::getInstance()->config->ui->storage->css),
                'storagecss'
            );
            Ui::getInstance()->js->append(
                new Script(Cms::getInstance()->config->ui->storage->js),
                'storagejs'
            );
        }
        $this->instanceName = $instance_name;
        Ui::getInstance()->onload->append(
            'new CapsuleUiStorage({
                instanceName: "' . $this->instanceName . '",
                top: ' . Cms::getInstance()->config->ui->dataGrid->top . '});'
        );
    }
    
    public function __toString() {
        ob_start();
        include 'template.php';
        return ob_get_clean();
    }
}