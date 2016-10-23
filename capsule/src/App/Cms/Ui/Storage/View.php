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
use Capsule\I18n\I18n;
use Capsule\Ui\TabControl\TabControl;
use Capsule\Ui\TabControl\Tab;
use Capsule\Common\TplVar;
use App\Cms\Ui\TabControl\View as tv;
/**
 * View.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class View
{
    private $object;
    
    /**
     * @var TabControl
     */
    protected $tabView;
    
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
        
        $tabs = new TabControl('storage-tab-control');
        
        $tab = new Tab;
        $tab->name = I18n::_('Upload file');
        $tabs->add($tab);
        $tab->bind = $this->instanceName . '-upload-file';
        
        $tab = new Tab;
        $tab->name = I18n::_('Paste image');
        $tabs->add($tab);
        $tab->bind = $this->instanceName . '-paste-image';
        
        $tab = new Tab;
        $tab->name = I18n::_('Multi upload');
        $tabs->add($tab);
        $tab->bind = $this->instanceName . '-multi-upload';
        
        $this->tabView = new tv($tabs);
        
        Ui::getInstance()->onload->append(
            'new CapsuleUiStorage({
                instanceName: "' . $this->instanceName . '",
                top: ' . Cms::getInstance()->config->ui->dataGrid->top . '});'
        );
    }
    
    public function __toString() {
        TplVar::getInstance()->tab = $this->tabView;
        TplVar::getInstance()->instanceName = $this->instanceName;
        ob_start();
        include 'template.php';
        return ob_get_clean();
    }
}