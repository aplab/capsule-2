/**
 * Created by polyanin on 21.12.2016.
 */
$(document).ready(function () {
    /**
     *
     * @constructor
     */
    window.CapsuleCmsDialog = function () {

    };
    /**
     * init
     */
    CapsuleCmsDialog.init = function () {
        CapsuleCmsDialog.instances = $('.capsule-cms-dialog');
        CapsuleCmsDialog.initialZindex = 1000000;
        var close_window = function (trigger) {
            $(trigger).closest('.capsule-cms-dialog').hide();
        };
        $('.capsule-cms-dialog-close').off('click', CapsuleCmsDialog.hide).click(function () {
            close_window(this);
        })
    };

    CapsuleCmsDialog.show = function (o) {
        if ($(o).hasClass('capsule-cms-dialog')) {
            $(o).css({
                zIndex: ++ CapsuleCmsDialog.initialZindex
            }).show();
        }
    };

    CapsuleCmsDialog.hide = function (o) {
        if ($(o).hasClass('capsule-cms-dialog')) {
            $(o).hide();
        }
    };

    CapsuleCmsDialog.init();
});