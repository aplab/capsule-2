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

use App\Cms\Cms;
use App\Cms\Ui\ActionMenu\ActionMenu;
use App\Cms\Ui\DataModel\DataGrid\DataGrid;
use App\Cms\Ui\MainMenu\Callback;
use App\Cms\Ui\MainMenu\Icon;
use App\Cms\Ui\MainMenu\MainMenu;
use App\Cms\Ui\MainMenu\MenuItem;
use App\Cms\Ui\MainMenu\Url;
use App\Cms\View\ActionMenuView;
use App\Cms\View\DataGridView;
use App\Cms\View\MainMenuView;
use Capsule\Component\Path\ComponentTemplatePath;
use Capsule\I18n\I18n;
use App\Cms\Ui\SectionManager;
use App\Cms\Ui\Section;
use Capsule\Capsule;
use Capsule\Tools\Tools;
use Capsule\User\User;

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

    /**
     *
     */
    protected function _init()
    {
        parent::_init();
        $this->ui = $this->app->ui;
    }

    /**
     *
     */
    public function handle()
    {
        $this->initSections();
        $this->initMainMenu();
        $this->initActionMenu();
        $this->ui->onload->append(new MainMenuView($this->app->registry->mainMenu));
        $this->ui->onload->append(new ActionMenuView($this->app->registry->actionMenu));
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

        $alert = clone $section;
        $alert->id = 'alert';
        $builtinJs->append($alert);

        $onload = clone $section;
        $onload->id = 'onload';
        $head->append($onload);
        
        $title = clone $section;
        $title->id = 'title';
        $title->append('Capsule ' . Capsule::getInstance()->config->version);
        $head->append($title);
        
        $body = clone $section;
        $body->id = 'body';
        $html->append($body);
        
        $nav = clone $section;
        $nav->id = 'nav';
        $body->append($nav);

        $menu = clone $section;
        $menu->id = 'menu';
        $body->append($menu);

        $content = new Section('content');
        $body->append($content);

        $path = new ComponentTemplatePath(Capsule::getInstance(), 'about');
        SectionManager::getInstance()->body->append(include $path);
    }
    
    /**
     * init main menu
     *
     * @param void
     * @return void
     * @TODO Объединить в одну функцию!
     */
    protected function initMainMenu()
    {
        $filter = Cms::getInstance()->urlFilter;
        $menu = new MainMenu('capsule-cms-main-menu');
        $this->app->registry->mainMenu = $menu;
        $menu_config = $this->app->config->mainMenu;
        foreach ($menu_config->items as $config) {
            if ($config->get('disabled')) continue;
            $action = null;
            $url = $config->get('url');
            if ($url) {
                $action = new Url($filter($url));
            }
            $callback = $config->get('callback');
            if ($callback) {
                $action = new Callback($callback);
            }
            $icon = $config->get('icon');
            if ($icon) {
                $icon = new Icon($icon);
            }
            $item = $menu->newMenuItem(I18n::_($config->get('caption')), $action, $icon);
            $items = $config->get('items');
            if ($items) {
                $this->initSubmenu($item, $items);
            }
        }
    }

    /**
     * @param MenuItem $item
     * @param array $items
     */
    private function initSubmenu(MenuItem $item, array $items)
    {
        $filter = Cms::getInstance()->urlFilter;
        foreach ($items as $config) {
            if ($config->get('disabled')) continue;
            $action = null;
            $url = $config->get('url');
            if ($url) {
                $action = new Url($filter($url));
            }
            $callback = $config->get('callback');
            if ($callback) {
                $action = new Callback($callback);
            }
            $icon = $config->get('icon');
            if ($icon) {
                $icon = new Icon($icon);
            }
            $submenu_item = $item->newSubMenuItem(I18n::_($config->get('caption')), $action, $icon);
            $submenu_items = $config->get('items');
            if ($submenu_items) {
                $this->initSubmenu($submenu_item, $submenu_items);
            }
        }
    }

    /**
     *
     */
    protected function initActionMenu()
    {
        $menu = new ActionMenu('capsule-cms-action-menu');
        $this->app->registry->actionMenu = $menu;

        $menu->newMenuItem(
            'Toggle fullscreen',
            new \App\Cms\Ui\ActionMenu\Callback('CapsuleCms.collapseActionMenu();screenfull.toggle();')
        );
    }
}