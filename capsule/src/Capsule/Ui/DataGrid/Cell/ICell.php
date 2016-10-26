<?php
/* vim: set expandtab tabstop=4 softtabstop=4 shiftwidth=4: */
// +---------------------------------------------------------------------------+
// | PHP version 5.4.7                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2006-2014                                                   |
// +---------------------------------------------------------------------------+
// | 02.05.2014 11:25:00 YEKT 2014                                              |
// | Класс - type_description_here                                             |
// +---------------------------------------------------------------------------+
// | Author: Alexander Polyanin <polyanin@gmail.com>                           |
// +---------------------------------------------------------------------------+
//
// $Id$
/**
 * @package Capsule
 */

namespace Capsule\Ui\DataGrid\Cell;

use Capsule\Ui\DataGrid\Col;
/**
 * ICell.php
 *
 * @package Capsule
 * @author Alexander Polyanin <polyanin@gmail.com>
 */
interface ICell
{
    public function __construct(Col $col);
}