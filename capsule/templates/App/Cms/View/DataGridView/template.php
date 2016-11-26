<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 24.11.2016
 * Time: 1:59
 */
$columns = $this->instance->columns;
use Capsule\I18n\I18n as _;
?>
<!--data grid-->
<div class="capsule-cms-data-grid" id="capsule-cms-data-grid">
    <!--data grid body-->
    <div class="capsule-cms-data-grid-body">
        <!--data grid content-->
        <div class="capsule-cms-data-grid-content">

            <div class="capsule-cms-data-grid-header">
                <div class="capsule-cms-data-grid-header-row wExt">
                    <?php foreach ($columns as $column) : ?>
                        <div class="w<?= $column->column->width ?>"
                             title="<?= _::_($column->property->title) ?>">
                            <?= _::_($column->property->title) ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="capsule-cms-data-grid-sidebar">
                <div class="capsule-cms-data-grid-sidebar-header">
                    <input type="checkbox">
                </div>
                <div class="capsule-cms-data-grid-sidebar-body">
                    <div class="capsule-cms-data-grid-sidebar-body-col"></div>
                </div>
            </div>

            <div class="capsule-cms-data-grid-data-wrapper">
                <div class="capsule-cms-data-grid-data wSum">
                    <?php foreach ($this->instance->items as $item) : ?>
                        <div class="wExt">
                            <?php foreach ($columns as $column) : ?>
                                <div class="w<?= $column->column->width ?>"
                                     title="<?= _::_($column->property->title) ?>">
                                    <?= $item->get($column->property->name) ?>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endforeach ?>
                </div>
            </div>

        </div>
        <!--end of capsule-cms-data-grid-content-->

        <div class="capsule-cms-data-grid-scroll-horizontal">
            <div class="capsule-cms-data-grid-scroll-horizontal-content"></div>
        </div>

        <div class="capsule-cms-data-grid-scroll-vertical">
            <div class="capsule-cms-data-grid-scroll-vertical-content"></div>
        </div>

    </div>
    <!--end of data grid body-->
    <!--data grid footer-->
    <div class="capsule-cms-data-grid-footer">
        <div class="capsule-cms-data-grid-prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        </div>
        <div class="capsule-cms-data-grid-page">
            <select class="form-control">
                <option>999000000</option>
                <option>999000000</option>
                <option>999000000</option>
                <option>999000000</option>
                <option>999000000</option>
            </select>
        </div>
        <div class="capsule-cms-data-grid-next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        </div>
        <div class="capsule-cms-data-grid-limit">
            <select class="form-control">
                <option>10</option>
                <option>50</option>
                <option>100</option>
                <option>500</option>
                <option selected>1000</option>
            </select>
        </div>
    </div>
    <!--end of data grid footer-->
</div>
<!--end of data grid-->
