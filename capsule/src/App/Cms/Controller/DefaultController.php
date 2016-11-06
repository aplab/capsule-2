<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 10.05.2014 9:08:07 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Controller;

use App\Cms\Ui\MainMenu\MainMenu;
use App\Cms\Ui\MainMenu\MenuItem;
use Capsule\Ui\DropdownMenu\SubPunct;
use Capsule\Ui\DropdownMenu\Punct;
use App\Cms\Ui\SectionManager;
use Capsule\Ui\Toolbar\Toolbar;
use App\Cms\Ui\Section;
use Capsule\Capsule;
/**
 * DefaultController.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class DefaultController extends AbstractController
{
    /**
     * @var SectionManager
     */
    protected $ui;
    
    protected function _init()
    {
        parent::_init();
        $this->ui = $this->app->ui;
    }
    
    public function handle()
    {
        $this->initSections();
        $this->initMainMenu();
//        $this->initToolbar();
//        $this->ui->menu->append(new MainMenu($this->app->registry->mainMenu));
//        $this->ui->toolbar->append(new \App\Cms\Ui\Toolbar\View($this->app->registry->toolbar));
        echo $this->ui->html;
    }
    
    /**
     * Инициализация секций
     *
     * @param void
     * @return void
     */
    protected function initSections()
    {
        $section = new Section;
        $html = clone $section;
        $html->id = 'html';

        $head = clone $section;
        $head->id = 'head';
        $html->append($head);
        
        $title = clone $section;
        $title->id = 'title';
        $title->append('Capsule ' . Capsule::getInstance()->config->version);
        $head->append($title);
        
        $body = clone $section;
        $body->id = 'body';
        $html->append($body);
        
        $buffer = clone $section;
        $buffer->id = 'buffer';
        $body->append($buffer);
        
        $pop_calendar = clone $section;
        $pop_calendar->id = 'popcalendar';
        $body->append($pop_calendar);

        $css = clone $section;
        $css->id = 'css';
        $head->append($css);

        $builtinCss = clone $section;
        $builtinCss->id = 'builtinCss';
        $head->append($builtinCss);

        $js = clone $section;
        $js->id = 'js';
        $head->append($js);

        $builtinJs = clone $section;
        $builtinJs->id = 'builtinJs';
        $head->append($builtinJs);

        $onload = clone $section;
        $onload->id = 'onload';
        $head->append($onload);
        
        $alert = clone $section;
        $alert->id = 'alert';
        $builtinJs->append($alert);

        $wrapper = clone $section;
        $wrapper->id = 'wrapper';

        $body->append($wrapper);

        $menu = clone $section;
        $menu->id = 'menu';
        
        $toolbar = clone $section;
        $toolbar->id = 'toolbar';

        $wrapper->append($menu);
        
        $workplace = clone $section;
        $workplace->id = 'workplace';
        
        $workplace->append($toolbar);

        $wrapper->append($workplace);
//        $content = include(new Path(Capsule::getInstance()->systemRoot, $this->app->config->templates, 'about.php'));
//        $buffer->append($content);
//        new Dialog(array(
//            'instanceName' => 'about',
//            'contentSrc' => '#capsule-cms-about-window-content',
//            'appendTo' => 'capsule-cms-wrapper',
//            'hidden' => true,
//            'minWidth' => 320,
//            'minHeight' => 240
//        ));
//         $window = new DialogWindow('about');
//         $window->hidden = true;
//         $window->caption = 'About';
//         $window->width = 320;
//         $window->height = 240;
//         $window->content = include(new Path(Capsule::getInstance()->systemRoot, $this->app->config->templates, 'about.php'));
//         $view = new \App\Cms\Ui\DialogWindow\View($window);
//         $wrapper->append($view);
        
        
    }
    
    /**
     * init toolbar
     *
     * @param void
     * @return void
     */
    protected function initToolbar()
    {
        $toolbar = new Toolbar('cms-toolbar');
        $this->app->registry->toolbar = $toolbar;
    }

    /**
     * init main menu
     *
     * @param void
     * @return void
     */
    protected function initMainMenu()
    {
        $menu = new MainMenu('main-menu');
        $this->app->registry->mainMenu = $menu;
        $menu_config = $this->app->config->mainMenu;
        foreach ($menu_config->item as $id => $config) {
            if ($config->get('disabled')) continue;
            echo $id . ' ';
        }
    }
}