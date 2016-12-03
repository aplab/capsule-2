<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 01.07.2013 23:54:49 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms;

use App\AbstractApp\App;
use Capsule\Cache\Cache;
use Capsule\Component\DataStorage\DataStorage;
use Capsule\Component\Url\Filter;
use Capsule\Component\Url\Path;
use App\Cms\Ui\SectionManager;
use Capsule\I18n\I18n;
use Capsule\Component\Url\Redirect;
use Capsule\Tools\Tools;
use Capsule\User\User;
use Capsule\Capsule;
use Capsule\User\Auth;
use App\Cms\Ui\Section;
use Capsule\Core\Fn;
use App\Cms\Controller\DefaultController;
use Capsule\DataModel\Config\Storage;

/**
 * Cms.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 * @property Filter $urlFilter
 * @property Registry $registry
 * @property string $base
 * @property string $mod
 * @property string $cmd
 * @property string $param
 * @property SectionManager $ui
 */
class Cms extends App
{
    /**
     * Cms constructor.
     */
    protected function __construct()
    {
        $this->_init();
    }

    /**
     *
     */
    protected function _init()
    {
        $data = Path::getInstance()->data;
        $this->data['base'] = array_shift($data); // base (switch (select) app trigger)
        $this->data['mod'] = array_shift($data); // module (e.g. User, News etc) or mode (operation) (e.g. install, logout etc)
        $this->data['cmd'] = array_shift($data); // module command (e.g. module user, command: add, edit etc)
        $this->data['param'] = $data; // /user/edit/12/ 12 - parameter
        $this->data['ui'] = SectionManager::getInstance();
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getUrlFilter($name)
    {
        if (!array_key_exists($name, $this->data)) {
            $filter = new Filter;
            $filter->autoRoot = true;
            $filter->detectBase = true;
            $filter->handleDot = true;
            $filter->base = $this->config->baseUrl;
            $filter->exclude('/');
            $this->data[$name] = $filter;
        }
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getRegistry($name)
    {
        if (!array_key_exists($name, $this->data)) {
            $this->data[$name] = Registry::getInstance();
        }
        return $this->data[$name];
    }

    /**
     * @throws \Throwable
     */
    public function run()
    {
        try {
            $mod = $this->mod;
            if ($this->config->installCommand === $mod) {
                if ($this->config->allowInstall) {
                    $this->install();
                    return;
                }
                return;
            }
            if ($this->config->logoutCommand === $mod) {
                echo '<a href="' . $this->config->baseUrl . '">logout</a>';
                Auth::getInstance()->logout();
                Redirect::go($this->config->baseUrl);
                die;
            }
            if (!Auth::getInstance()->user()) {
                $login_form = new Section;
                $login_form->id = 'login';
                ob_start();
                echo $this->ui->login;
                $str = ob_get_clean();
                echo Fn::strip_spaces($str);
                return;
            }
            $config = Cms::getInstance()->config->module;
            $controller_name = $config->get($mod);
            if ($controller_name) {
                $controller_name = Fn::cc($controller_name, $this->config->controller->defaultNamespace);
                if ($controller_name) {
                    $controller_name::getInstance()->handle();
                    return;
                }
            }
            DefaultController::getInstance()->handle();
        } catch (\Throwable $throwable) {
            $error_page = new Section;
            $error_page->id = 'error';
            $error_page->exception = $throwable;
            echo $error_page;
            return;
        }
    }

    /**
     *
     */
    private function install()
    {
        DataStorage::getInstance()->destroy();
        Storage::getInstance()->destroy();
        \App\Website\Structure\Storage::getInstance()->destroy();
        Cache::getInstance()->destroy();
        \App\Website\Cache::getInstance()->destroy();
        if (!User::number()) {
            $user = new User;
            $user->login = Capsule::getInstance()->config->defaultUser->login;
            $user->password = Capsule::getInstance()->config->defaultUser->password;
            $user->store();
        }
        Redirect::go($this->config->baseUrl);
    }
}