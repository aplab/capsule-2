<div class="capsule-ui-dialog-window" id="<?=$this->instanceName?>">
    <div class="capsule-ui-dialog-window-wrapper" id="<?=$this->instanceName?>-wrapper">
        <div <?='unselectable="on"'?> class="capsule-ui-dialog-window-caption" id="<?=$this->instanceName?>-caption">
            <div class="capsule-ui-dialog-window-left-corner">
                <div class="capsule-ui-dialog-window-right-corner">
                    <div class="capsule-ui-dialog-window-label">
                        <div <?='unselectable="on"'?> class="capsule-ui-dialog-window-label-text">
                            <?=$this->object->caption?>
                        </div>
                        <div class="capsule-ui-dialog-window-close-button" id="<?=$this->instanceName?>-close-button"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="capsule-ui-dialog-window-container">
            <div class="capsule-ui-dialog-window-workplace" id="<?=$this->instanceName?>-workplace"><?=$this->object->content;?></div>
        </div>
    </div>
    <div class="capsule-ui-dialog-window-shadow" id="<?=$this->instanceName?>-shadow"></div>
</div>