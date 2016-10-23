<?php
use Capsule\Common\TplVar;
use Capsule\I18n\I18n;
$tplvar = TplVar::getInstance();
$in = $tplvar->instanceName;
?><div class="capsule-ui-upload-image" id="<?=$in?>">
    <div class="capsule-ui-upload-image info-bar" id="<?=$in?>-info-bar">
        <div class="item filename">
            <div class="capsule-cms-control-text">
                <input type="text" value="" readonly="readonly" id="<?=$in?>-filename">
            </div>
        </div>
        <div class="resize-bar" id="<?=$in?>-resize-bar">
            <div class="item label">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('W')?>:
                </div>
            </div>
            <div class="item value">
                <div class="capsule-cms-control-text">
                    <input type="text" value="" id="<?=$in?>-input-width">
                </div>
            </div>
            <div class="item measure">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('px')?>
                </div>
            </div>
            <div class="item label">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('H')?>:
                </div>
            </div>
            <div class="item value">
                <div class="capsule-cms-control-text">
                    <input type="text" value="" id="<?=$in?>-input-height">
                </div>
            </div>
            <div class="item measure">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('px')?>
                </div>
            </div>
        </div>
        <div class="crop-bar" id="<?=$in?>-crop-bar">
            <div class="item label">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('W')?>:
                </div>
            </div>
            <div class="item value">
                <div class="capsule-cms-control-text">
                    <input type="text" value="" id="<?=$in?>-input-crop-width">
                </div>
            </div>
            <div class="item measure">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('px')?>
                </div>
            </div>
            <div class="item label">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('H')?>:
                </div>
            </div>
            <div class="item value">
                <div class="capsule-cms-control-text">
                    <input type="text" value="" id="<?=$in?>-input-crop-height">
                </div>
            </div>
            <div class="item measure">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('px')?>
                </div>
            </div>
            <div class="item label">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('X1')?>:
                </div>
            </div>
            <div class="item value">
                <div class="capsule-cms-control-text">
                    <input type="text" value="" id="<?=$in?>-input-crop-x1">
                </div>
            </div>
            <div class="item measure">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('px')?>
                </div>
            </div>
            <div class="item label">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('Y1')?>:
                </div>
            </div>
            <div class="item value">
                <div class="capsule-cms-control-text">
                    <input type="text" value="" id="<?=$in?>-input-crop-y1">
                </div>
            </div>
            <div class="item measure">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('px')?>
                </div>
            </div>
            <div class="item label">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('X2')?>:
                </div>
            </div>
            <div class="item value">
                <div class="capsule-cms-control-text">
                    <input type="text" value="" id="<?=$in?>-input-crop-x2">
                </div>
            </div>
            <div class="item measure">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('px')?>
                </div>
            </div>
            <div class="item label">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('Y2')?>:
                </div>
            </div>
            <div class="item value">
                <div class="capsule-cms-control-text">
                    <input type="text" value="" id="<?=$in?>-input-crop-y2">
                </div>
            </div>
            <div class="item measure">
                <div class="capsule-cms-control-label">
                    <?=I18n::_('px')?>
                </div>
            </div>
        </div>
        <div class="form-container">
            <div class="form-wrapper">
                <form action="/ajax/" method="post" enctype="multipart/form-data" id="<?=$in?>-form" target="<?=$in?>">
                    <input type="hidden" name="cmd" value="uploadSingleImage">
                    <input type="hidden" name="width" value="" id="<?=$in?>-width">
                    <input type="hidden" name="height" value="" id="<?=$in?>-height">
                    <input type="hidden" name="x1" value="" id="<?=$in?>-x1">
                    <input type="hidden" name="y1" value="" id="<?=$in?>-y1">
                    <input type="hidden" name="x2" value="" id="<?=$in?>-x2">
                    <input type="hidden" name="y2" value="" id="<?=$in?>-y2">
                    <input type="hidden" name="imageString" value="" id="<?=$in?>-image-string">
                    <input type="file" name="file">
                </form>
            </div>
        </div>
    </div>
    <div class="workplace" id="<?=$in?>-workplace"></div>
</div>