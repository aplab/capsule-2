<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 15.02.2017
 * Time: 1:24
 */

namespace Pkg\Product\Tree\Controller;


use App\Cms\Controller\NestedContainer;

class SectionController extends NestedContainer
{
    protected function listItems(array $param = []) {
        $class = $this->moduleClass;
        $err = $class::repair();
        if (sizeof($err)) {
            $msg = 'Corrupted elements detected: ' . join(', ', $err);
            $this->ui->alert->append(I18n::_($msg));
        }
        parent::listItems();
    }

    protected $moduleClass = 'Pkg\\Product\\Tree\\Section';
}