<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 12.11.2016
 * Time: 7:50
 */
?><div id="capsule-cms-content-wrapper">
    <div id="capsule-cms-content">


        <div class="capsule-cms-data-grid" id="capsule-cms-data-grid">
            <div class="capsule-cms-data-grid-header">
                <div>
                    <?php for ($i = 0; $i < 49; $i++) : ?>
                    <div>
                        cell
                    </div>
                    <?php endfor ?>
                </div>
            </div>
            <div class="capsule-cms-data-grid-body">
                <?php for ($i = 0; $i < 100; $i++) : ?>
                <div>
                    <?php for ($j = 0; $j < 49; $j++) : ?>
                        <div>
                            cell
                        </div>
                    <?php endfor ?>
                </div>
                <?php endfor ?>
            </div>
            <div class="capsule-cms-data-grid-footer">
                <div class="capsule-cms-data-grid-prev">
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                </div>
                <div class="capsule-cms-data-grid-page">
                    <select class="form-control">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                </div>
                <div class="capsule-cms-data-grid-next">
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                </div>
                <div class="capsule-cms-data-grid-limit">
                    <select class="form-control">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                </div>
            </div>

        </div>





    </div>
</div>