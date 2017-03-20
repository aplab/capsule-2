<?php
/**
 * This file is part of the Capsule package.
 *
 * (c) Alexander Polyanin 2006 <polyanin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 21.12.2016
 * Time: 1:39
 */
?>
<div class="capsule-cms-dialog" id="test-dialog">
    <div class="capsule-cms-dialog-backdrop"></div>
    <div class="capsule-cms-dialog-container">
        <div class="capsule-cms-dialog-content capsule-cms-dialog-maximize">
            <div class="capsule-cms-dialog-header">
                <div class="dropdown">
                    <span class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </span>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                    </ul>
                </div>
            </div>
            <div class="capsule-cms-dialog-body">
<?php
$path = new \Capsule\Component\Path\ComponentTemplatePath(\Capsule\Capsule::getInstance(), 'licence');
include $path;
?>
            </div>
            <div class="capsule-cms-dialog-footer">
                <button type="button" class="btn btn-default capsule-cms-dialog-close">Close</button>
            </div>
        </div>
    </div>
</div>
