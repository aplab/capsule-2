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
    <div class="capsule-cms-dialog-window">
        <div class="capsule-cms-dialog-content">
            <div class="capsule-cms-dialog-header">
                header
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
