<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2013                                                   |
// +---------------------------------------------------------------------------+
// | 07.07.2013 21:58:06 YEKT 2013                                             |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Website;

use App\AbstractApp\App;
use App\Website\Structure\Router;
use Capsule\Capsule;
use Capsule\Common\Path;
use App\Website\Exception\Error404Exception;
use App\Website\Structure\Page;
use Capsule\Tools\Seo\Nofollow;
use Capsule\Tools\Seo\Seo;
/**
 * Website.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 *
 * @property Page $page текущая страница сайта
 * @property string $path путь текущей страницы
 * @property string $tplpath абсолютный путь к файлу шаблона текущей страницы
 * @property Cache $cache кэш сайта
 */
class Website extends App
{
    /**
     * Disable create objects directly.
     *
     * @param void
     * @return self
     */
    protected function __construct() {
        include 'helper.php';
        $this->data['page'] = Router::getInstance()->getPage();
        $this->data['path'] = Router::getInstance()->getPath();
        $this->data['cache'] = Cache::getInstance();
        $this->data['tplpath'] = new Path(Capsule::getInstance()->systemRoot, $this->config->path->templates);
    }
    
    /**
     * (non-PHPdoc)
     * @see \App\AbstractApp\App::run()
     */
    public function run() {
        try {
            if ($this->page) {
                $this->displayPage();
                return;
            }
        } catch (Error404Exception $e) {
            $this->forward404();
        }
        $this->forward404();
    }
    
    public function forward404() {
        $this->clearBuffer();
        header("HTTP/1.0 404 Not Found", true, 404);
        header("HTTP/1.1 404 Not Found", true, 404);
        header("Status: 404 Not Found", true, 404);
        if (isset($this->config->error404)) {
            $path = new Path($this->tplpath, $this->config->error404);
            if (file_exists($path)) include $path;
        }
        die;
    }
    
    /**
     * @param void
     * @return void
     * @throws Error404Exception
     */
    public function error404() {
        throw new Error404Exception();
    }
    
    /**
     * Очищает все буферы вывода
     *
     * @param void
     * @return void
     */
    public function clearBuffer() {
        while (ob_get_level()) ob_end_clean();
    }
    
    /**
     * Separate function to display the page
     *
     * @param void
     * @return void
     */
    protected function displayPage() {
        $units = array();
        foreach ($this->page->area as $area) {
            foreach ($area->unit as $unit) {
                $units[] = $unit;
            }
        }
        // Сортировка юнитов по параметру "buildOrder" Если таковой не задан,
        // то сортировка в произвольном порядке, т.к. сортировка не важна
        usort($units, function($a, $b) {
            $a = $a->buildOrder;
            $b = $b->buildOrder;
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });
        // Подготовка контента в юнитах
        foreach ($units as $unit) $unit->prepare();
        $nofollow = new Nofollow;
        echo $nofollow($this->page->toString());
    }
}