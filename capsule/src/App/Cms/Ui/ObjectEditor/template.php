<?php
use Capsule\Core\Fn;
use Capsule\Common\TplVar;
?><div class="capsule-ui-object-editor" id="<?=$this->model->instanceName?>">
    <div class="capsule-ui-object-editor-tabs" id="<?=$this->model->instanceName?>-tabs">
        <?=$this->tabView?>
    </div>
    <div class="capsule-ui-object-editor-container" id="<?=$this->model->instanceName?>-container">
        <form method="post" id="<?=$this->model->instanceName?>-form">
        <?php foreach ($this->model as $group) : ?>
            <div class="capsule-ui-object-editor-panel" id="id<?=md5($group->name)?>">
                <?php if ($group->ckeditor) : ?>
                    <?php foreach ($group as $element) {
                            TplVar::getInstance()->element = $element;
                            include strtolower(Fn::get_classname($element)) . '.php';
                    } ?>
                <?php else : ?>
                <div class="capsule-ui-object-editor-elements">
                    <div class="capsule-ui-object-editor-el-list">
                        <?php foreach ($group as $element) {
                            TplVar::getInstance()->element = $element;
                            include strtolower(Fn::get_classname($element)) . '.php';
                        } ?>
                    </div>
                </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
        </form>
    </div>
</div>