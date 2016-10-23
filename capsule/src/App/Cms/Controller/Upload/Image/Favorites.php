<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 11.05.2014 6:55:12 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Controller\Upload\Image;

use Capsule\Ui\Toolbar\Button;
use App\Cms\Ui\UploadImageFavorites\View;
use Capsule\Capsule;
use Capsule\I18n\I18n;
use App\Cms\Controller\DefaultController;
/**
 * Storage.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class Favorites extends DefaultController
{
    protected $instanceName = 'capsule-ui-upload-image-favorites';

    /**
     * (non-PHPdoc)
     * @see \Capsule\Controller\AbstractController::handle()
     */
    public function handle() {
        $this->initSections();

        $this->initMainMenu();
        $this->initToolbar();
        $this->overview();
        $this->ui->menu->append(new \App\Cms\Ui\MainMenu\View($this->app->registry->mainMenu));
        $this->ui->toolbar->append(new \App\Cms\Ui\Toolbar\View($this->app->registry->toolbar));
        echo $this->ui->html;
    }

    protected function overview() {
        $filter = $this->app->urlFilter;
        $toolbar = $this->app->registry->toolbar;

        $button = new Button;
        $toolbar->add($button);
        $button->caption = I18n::_('Show all history');
        $button->url = '/admin/uploadimagehistory/';
        $button->icon = $this->app->config->icons->cms . '/clock-history.png';

        $view = new View($this->instanceName);

        $this->ui->workplace->append($view);
    }


}