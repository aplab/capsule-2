<?php
use Capsule\Common\TplVar;
$tplvar = TplVar::getInstance();
$in = $tplvar->instanceName;
?><div class="capsule-ui-storage" id="<?=$in?>">
    <div class="capsule-ui-storage-tabs" id="<?=$in?>-tabs">
        <?=$tplvar->tab?>
    </div>
    <div class="capsule-ui-storage-container" id="<?=$in?>-container">
        
        <div class="capsule-ui-storage-panel" id="<?=$in?>-upload-file">
            <div class="capsule-ui-storage-elements">
                <div class="capsule-ui-storage-el-list">
                    <div class="capsule-ui-storage-uf-container">
                        <form action="/ajax/" target="<?=$in?>" enctype="multipart/form-data" id="<?=$in?>-form" method="post">
                            <div class="capsule-cms-control-choose-file">
                                <span></span><div>Обзор...</div>
                                <input type="file" onchange="CapsuleCmsControlChooseFile(this)" size="1" name="file" />
                            </div>
                            <input type="hidden" name="cmd" value="storageUploadFileSimple">
                        </form>
                    </div>
                    <div class="capsule-ui-storage-uf-result" id="<?=$in?>-uf-result">
                        <input type="text" value="">
                    </div>
                </div>
                <div class="capsule-ui-storage-uf-image" id="<?=$in?>-uf-image">
                    1234
                </div>
            </div>
        </div>
        <div class="capsule-ui-storage-panel" id="<?=$in?>-paste-image">
                java applet
        </div>
        <div class="capsule-ui-storage-panel" id="<?=$in?>-multi-upload">
                blueimp/jQuery-File-Upload
        </div>
    </div>
</div>