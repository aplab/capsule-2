<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 12.10.2014 1:43:24 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Ui\Dialog;

use PHP\Exceptionizer\Exceptionizer;
use App\Cms\Ui\Ui;
use App\Cms\Ui\Stylesheet;
use App\Cms\Ui\Script;
use App\Cms\Cms;
/**
 * Dialog.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Dialog
{
    private static $instances;
    
    protected $instanceName;
    
    public function __construct(array $data = array()) {
        $e = new Exceptionizer();
        $this->instanceName = $data['instanceName'];
        settype($this->instanceName, 'string');
        if (!is_array(self::$instances)) {
            Ui::getInstance()->css->append(
                new Stylesheet(Cms::getInstance()->config->ui->dialog->css)
            );
            Ui::getInstance()->js->append(
                new Script(Cms::getInstance()->config->ui->dialog->js)
            );
            self::$instances = array();
        }
        if (isset(self::$instances[$this->instanceName])) {
            $msg = 'Instance already exists: ' . $this->instanceName;
            throw new \Exception($msg);
        }
        // Здесь -1 нужен потому что окно с контентом должно быть выведено до того, как 
        // будут вызваны обработчики контента окна. Иначе им нечего будет обрабатывать
        Ui::getInstance()->onload->insert('new CapsuleUiDialog(' . json_encode($data) . ');', -1);
    }
}