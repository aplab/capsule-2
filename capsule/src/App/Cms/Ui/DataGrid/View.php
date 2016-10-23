<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.5.5                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 21.01.2014 21:38:17 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace App\Cms\Ui\DataGrid;

use Capsule\Ui\DataGrid\DataGrid;
use Capsule\Common\TplVar;
use App\Cms\Ui\Script;
use App\Cms\Ui\Stylesheet;
use App\Cms\Cms;
use App\Cms\Ui\Ui;

/**
 * View.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
class View
{
    /**
     * @var DataGrid
     */
    protected $dataGrid;

    public function __construct(DataGrid $data_grid)
    {
        $this->dataGrid = $data_grid;
        $ui = Ui::getInstance();
        $ui->builtinCss->append($this->getWidthDefinition());
        $ui->css->append(new Stylesheet(Cms::getInstance()->config->ui->dataGrid->css));
        $ui->js->append(new Script(Cms::getInstance()->config->ui->dataGrid->js));
        $ui->onload->append(
            'new CapsuleUiDataGrid({
                instanceName:"' . $data_grid->instanceName . '",
                top:"' . Cms::getInstance()->config->ui->dataGrid->top . '"});'
        );
    }

    /**
     * Implicit conversion to string
     *
     * @param void
     * @return string
     */
    public function __toString()
    {
        try {
            TplVar::getInstance()->dataGrid = $this->dataGrid;
            ob_start();
            include 'template.php';
            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            ob_start();
            \Capsule\Tools\Tools::dump($e->getTraceAsString());
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * Собираем CSS
     *
     * @param void
     * @return string|NULL
     */
    protected function getWidthDefinition()
    {
        $tmp = array();
        $sum = 0;
        foreach ($this->dataGrid->columns as $column) {
            $width = $column->width;
            $sum += $width;
            $tmp[$width] = '.w' . $width . '{width:' . $width . 'px;}';
            $column->baseUrl = $this->dataGrid->baseUrl;
        }
        $tmp[$sum] = '.wSum{width:' . $sum . 'px;}';
        $sum += 100;
        $tmp[$sum] = '.wExt{width:' . $sum . 'px;}';
        if (sizeof($tmp)) {
            return join(PHP_EOL, $tmp);
        }
        return null;
    }
}