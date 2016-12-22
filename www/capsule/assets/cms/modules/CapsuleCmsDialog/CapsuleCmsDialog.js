/**
 * Created by polyanin on 21.12.2016.
 */
(function() {
    /**
     * @constructor
     */
    window.CapsuleCmsDialog = function () {


    };

    var m = window.CapsuleCmsDialog;

    /**
     * z coordinate of window
     *
     * @type {number}
     */
    CapsuleCmsDialog.zIndex = 1000000;

    /**
     * Instances of window
     *
     * @type {Array}
     */
    CapsuleCmsDialog.instances = [];

    /**
     * Returns instance by name or false
     *
     * @param instance_name
     * @returns {*|boolean}
     */
    CapsuleCmsDialog.getInstance = function (instance_name) {
        return this.instances[instance_name] || false;
    };

    /**
     * Returns true if instance exists or false
     *
     * @param instance_name
     * @returns {*|boolean}
     */
    CapsuleCmsDialog.instanceExists = function (instance_name) {
        return undefined !== this.instances[instance_name];
    };



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
})();
