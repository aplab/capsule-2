/**
 * Created by polyanin on 19.01.2017.
 */
function CapsuleCmsImageHistory()
{
    if (CapsuleCmsImageHistory.hasOwnProperty('instance')) {
        var m = 'Instance already exists. Only one instance allowed!';
        console.log(m);
        throw new Error(m);
    } else {
        CapsuleCmsImageHistory.instance = this;
    }

    /**
     * Class prefix
     *
     * @type {string}
     */
    var class_prefix = 'capsule-cms-image-history-';

    /**
     * Button wrapper class
     *
     * @see CapsuleCmsDialog
     * @type {string}
     */
    var button_wrapper_class_prefix = 'capsule-cms-dialog-footer-button-';

    /**
     * var CapsuleCmsDialog
     */
    var dialog_window;

    /**
     * Dialog window exists flag
     *
     * @type {boolean}
     */
    var dialog_window_exists = false;

    /**
     * Files list
     */
    var list;

    /**
     * button cancel
     */
    var button_cancel, button_cancel_wrapper;

    /**
     * button done
     */
    var button_done, button_done_wrapper;

    var button_group;

    /**
     * Create element wrapper
     *
     * @param tag
     * @returns {*|jQuery|HTMLElement}
     */
    var ce = function (tag)
    {
        tag = tag || 'div';
        return $(document.createElement(tag));
    };

    /**
     * Create dialog window
     */
    var create_window = function ()
    {
        dialog_window = CapsuleCmsDialog.createElement(
            'capsule-cms-image-uploader',
            {
                maximxze: 1,
                title: 'Browse history'
            }
        );
        var footer = dialog_window.getFooter();

        button_group = ce();
        footer.append(button_group);

        button_cancel_wrapper = ce();
        button_cancel_wrapper.addClass(button_wrapper_class_prefix + '3');
        button_group.append(button_cancel_wrapper);

        button_done_wrapper = ce();
        button_done_wrapper.addClass(button_wrapper_class_prefix + '2');
        button_group.append(button_done_wrapper);

        button_cancel = ce('button');
        button_cancel.prop({
            type: 'button'
        }).addClass('btn btn-default').text('Cancel');
        button_cancel_wrapper.append(button_cancel);

        button_done = ce('button');
        button_done.prop({
            type: 'button'
        }).addClass('btn btn-success').text('Done');
        button_done_wrapper.append(button_done);

        list = ce();
        list.addClass(class_prefix + 'list');

        dialog_window.getBody().append(list);

        button_cancel.click(function ()
        {
            CapsuleCmsImageHistory.getInstance().purgeWindow();
        });

        button_done.click(function ()
        {
            CapsuleCmsImageHistory.getInstance().purgeWindow();
        });


        button_group.show();
        dialog_window_exists = true;
    };

    create_window();

    /**
     * Show window
     *
     * @access public
     */
    this.showWindow = function ()
    {
        list.empty();
        dialog_window.show();
    };

    /**
     * Destroy window
     *
     * @access public
     */
    this.purgeWindow = function ()
    {
        dialog_window.hide();
    };

    /**
     * Visuslisation objects
     *
     * @type {Array}
     */
    var items = [];



}

/**
 * Static method
 */
CapsuleCmsImageHistory.getInstance = function ()
{
    if (CapsuleCmsImageHistory.hasOwnProperty('instance')) {
        return CapsuleCmsImageHistory.instance;
    }
    return new CapsuleCmsImageHistory();
};
