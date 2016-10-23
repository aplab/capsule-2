<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 07.04.2014 6:25:09 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Ui\ObjectEditor;

use Capsule\Ui\ObjectEditor\Oe;
use Capsule\Ui\TabControl\Tab;
use Capsule\Ui\TabControl\TabControl;
use App\Cms\Ui\Ui;
use App\Cms\Ui\Stylesheet;
use App\Cms\Ui\Script;
use App\Cms\Cms;
use App\Cms\Ui\TabControl\View as tv;
use Capsule\Superglobals\Post;

/**
 * View.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class View
{
    /**
     * @var Oe
     */
    protected $model;
    
    /**
     * @var TabControl
     */
    protected $tabView;
    
    /**
     * Instances of View
     *
     * @var array
     */
    protected static $instances = array();
    
    /**
     * Constructor
     *
     * @param Oe $model
     * @return self
     */
    public function __construct(Oe $model) {
        self::$instances[] = $this;
        $this->model = $model;
        $tabs = new TabControl('object-editor-tab-control');
        $i = 0;
        foreach ($this->model->groups as $name => $group) {
            $tab = new Tab;
            $tab->name = $name;
            $tabs->add($tab, $name);
            $tab->callback = 'CapsuleUiObjectEditor.getInstance("' . $model->instanceName . '").fitEditors();';
            $tab->bind = 'id' . md5($name);
            if (intval(Post::getInstance()->get('activeTabNumber', 0)) === $i) {
                $tab->active = true;
            }
            $i++;
        }
        Ui::getInstance()->css->append(
            new Stylesheet(Cms::getInstance()->config->ui->objectEditor->css)
        );
        Ui::getInstance()->js->append(
            new Script(Cms::getInstance()->config->ui->objectEditor->js)
        );
        Ui::getInstance()->js->append(
            new Script(Cms::getInstance()->config->ui->ckeditor->js)
        );
        Ui::getInstance()->js->append(
            new Script(Cms::getInstance()->config->ui->ckeditor->adapter)
        );
        $c = 'new CapsuleUiObjectEditor("' . $model->instanceName . '");';
        Ui::getInstance()->onload->append($c);
        $c = '$("#' . $model->instanceName . '").css({top: ' . Cms::getInstance()->config->ui->toolbar->height . '})';
        Ui::getInstance()->onload->append($c);
        $c = '$("#' . $model->instanceName . '-container").css({top: ' . Cms::getInstance()->config->ui->tabControl->height . '})';
        Ui::getInstance()->onload->append($c);
        $this->tabView = new tv($tabs);
        $c = '$( \'textarea.capsule-oe-ckeditor\' ).ckeditor();';
        Ui::getInstance()->onload->append($c);
    }
    
    /**
     * Implicit conversion to string
     *
     * @param void
     * @return string
     */
    public function __toString() {
        try {
            ob_start();
            include 'template.php';
            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            ob_start();
            $trace = $e->getTrace();
            array_walk($trace, function(&$v, $k) {
            	unset($v['args']);
            });
            \Capsule\Tools\Tools::dump($trace);
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }
}