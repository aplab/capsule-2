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

namespace App\Cms\Ui\DialogWindow;

use Capsule\Ui\DialogWindow\DialogWindow;
use App\Cms\Cms;
use App\Cms\Ui\Stylesheet;
use App\Cms\Ui\Script;
use App\Cms\Ui\Ui;
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
    
    public function __construct(DialogWindow $object) {
        if (!self::$assetsIncluded) {
            Ui::getInstance()->css->append(
                new Stylesheet(Cms::getInstance()->config->ui->dialogWindow->css)
            );
            Ui::getInstance()->js->append(
                new Script(Cms::getInstance()->config->ui->dialogWindow->js)
            );
        }
        $this->object = $object;
        $this->instanceName = $this->object->instanceName;
        Ui::getInstance()->onload->append(
            'new CapsuleUiDialogWindow(' . json_encode($this->object) . ');');
    }
    
    public function __toString() {
        ob_start();
        include 'template.php';
        return ob_get_clean();
    }
}