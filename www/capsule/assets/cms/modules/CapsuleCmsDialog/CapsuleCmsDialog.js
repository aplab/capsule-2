/**
 * Created by polyanin on 21.12.2016.
 */
/**
 * @constructor
 */
CapsuleCmsDialog = function () {

    /**
     * show window
     */
    this.show = function () {
        this.wrapper.css({
            zIndex: ++ CapsuleCmsDialog.zIndex
        }).show();
    };

    /**
     * hide window
     */
    this.hide = function () {
        this.wrapper.hide();
    };
};

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

/**
 * Create element
 *
 * @param instance_name
 * @param options
 */
CapsuleCmsDialog.createElement = function(instance_name, options) {


};



CapsuleCmsDialog.init = function () {
    var close_window = function () {
        $(this).closest('.capsule-cms-dialog').hide();
    };
    $('.capsule-cms-dialog-close').off('click', close_window()).click(close_window);
};

$(document).ready(function () {
    CapsuleCmsDialog.init();
});
