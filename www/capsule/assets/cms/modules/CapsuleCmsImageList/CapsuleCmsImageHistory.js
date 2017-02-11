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
     * Before done
     */
    this.beforeDone = function ()
    {
        console.log('before done callback');
    };

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
     * Files items wrapper
     */
    var list_items;

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
     * load favorites only
     *
     * @type {boolean}
     */
    var favorites = false;

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
            'capsule-cms-image-history',
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

        list_items = ce();
        list_items.addClass(class_prefix + 'list-items clearfix');

        list.append(list_items);

        button_cancel.click(function ()
        {
            CapsuleCmsImageHistory.getInstance().purgeWindow();
        });

        button_done.click(function ()
        {
            var o = CapsuleCmsImageHistory.getInstance();
            if (typeof o.beforeDone === 'function') {
                o.beforeDone();
            }
            CapsuleCmsImageHistory.getInstance().purgeWindow();
        });
        button_group.show();
        dialog_window_exists = true;
    };

    /**
     * Show window
     *
     * @access public
     */
    this.showWindow = function (param)
    {
        param = param || {};
        try {
            if (param.hasOwnProperty('favorites') && param.favorites) {
                favorites = true;
            } else {
                favorites = false;
            }
        } catch (err) {
            favorites = false;
        }
        list_items.empty();
        dialog_window.show();
        last_scroll_value = 0;
        load_offset = 0;
        loadData();
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

    /**
     *
     * @type {number}
     */
    var load_offset = 0;

    /**
     * Must be equal ImageHistory::LIST_ITEMS_LIMIT
     *
     * @type {number}
     */
    var load_limit = 100;

    /**
     * Load in progress
     *
     * @type {boolean}
     */
    var load_in_progress = false;

    /**
     * List items last scroll value
     *
     * @type {number}
     */
    var last_scroll_value = 0;

    /**
     * load data
     */
    var loadData = function ()
    {
        if (load_in_progress) {
            return;
        }
        load_in_progress = true;
        var url = '/ajax/historyUploadImage/listItems/' + load_offset + '/';
        if (favorites) {
            url = '/ajax/historyUploadImage/listFavorites/' + load_offset + '/';
        }
        $.get(
            url, {}, function (data, status, jqXHR)
            {
                for (var i = 0; i < data.length; i++) {
                    if (i == load_limit) {
                        break;
                    }
                    var o = data[i];
                    var item = ce();
                    item.data(o);
                    item.addClass(class_prefix + 'item');
                    list_items.append(item);
                    var img = ce();
                    img.addClass(class_prefix + 'image');
                    img.css({
                        backgroundImage: 'url("' + (o.thumbnail.length ? o.thumbnail : o.path) + '")'
                    });
                    var title = ce().addClass(class_prefix + 'title').text(o.name);
                    var metadata = ce().addClass(class_prefix + 'metadata').text(o.width + 'x' + o.height);
                    item.append(img);
                    item.append(title);
                    item.append(metadata);
                    var buttons = ce().addClass(class_prefix + 'buttons');
                    buttons.data({
                        itemId: o.id
                    });
                    item.append(buttons);
                    var select = ce().addClass(class_prefix + 'button ' +
                        class_prefix + 'select glyphicon glyphicon-ok');
                    buttons.append(select);
                    var fav = ce().addClass(class_prefix + 'button ' +
                        class_prefix + 'fav glyphicon glyphicon-star');
                    if (1 == o.favorites) {
                        fav.addClass('selected');
                    }
                    buttons.append(fav);
                    var comment = ce().addClass(class_prefix + 'button ' +
                        class_prefix + 'comment glyphicon glyphicon-pencil');
                    buttons.append(comment);
                    var link = ce().addClass(class_prefix + 'button ' +
                        class_prefix + 'link glyphicon glyphicon-link');
                    link.attr('data-clipboard-text', o.path);
                    buttons.append(link);
                    var drop = ce().addClass(class_prefix + 'button ' +
                        class_prefix + 'drop glyphicon glyphicon-remove');
                    buttons.append(drop);

                    select.click(function ()
                    {
                        $(this).toggleClass('selected');
                    });
                    fav.click(function ()
                    {
                        favItem(this);
                    });
                    drop.click(function ()
                    {
                        dropItem(this);
                    });
                    comment.click(function ()
                    {
                        renameItem(this);
                    });
                    link.click(function ()
                    {
                         copyLink(this);
                    });
                }
                load_offset += i;
                new Clipboard('.' + class_prefix + 'button.' + class_prefix + 'link.glyphicon.glyphicon-link');
                load_in_progress = false;
            },
            'json'
        );
    };

    create_window();

    /**
     * neverending load
     */
    setInterval(function ()
    {
        var scroll = list.scrollTop();
        if (scroll <= last_scroll_value) {
            return;
        }
        last_scroll_value = scroll;
        if (scroll < 1) {
            return;
        }
        var list_height = list.height();
        var list_items_height = list_items.height();
        var diff = list_items_height - list_height;
        if ((diff - scroll) < 20) {
            loadData();
        }

    }, 1000);

    /**
     * Drop item handler
     *
     * @param o
     */
    var dropItem = function (o)
    {
        if (!confirm('You really want to drop item?')) {
            return;
        }
        var id = $(o).parent().data('itemId');
        $.post(
            '/ajax/historyUploadImage/dropItem/' + id + '/', {},
            function (data, status, jqXHR)
            {
                if (!data.hasOwnProperty('status')) {
                    return;
                }
                if ('ok' === data.status) {
                    o.closest('.' + class_prefix + 'item').remove();
                }
            },
            'json'
        );
    };

    var favItem = function (o)
    {
        if ($(o).hasClass('selected')) {
            if (!confirm('Unstar image?')) {
                return;
            }
        } else {
            // if (!confirm('Add image to favorites?')) {
            //     return;
            // }
        }
        var id = $(o).parent().data('itemId');
        $.post(
            '/ajax/historyUploadImage/favItem/' + id + '/', {},
            function (data, status, jqXHR)
            {
                if (!data.hasOwnProperty('status')) {
                    return;
                }
                if ('ok' === data.status) {
                    $(o).toggleClass('selected');
                }
            },
            'json'
        );
    };

    var renameItem = function (o)
    {
        $(o).addClass('selected');
        prompt('Enter new name:');
        $(o).removeClass('selected');
    }

    /**
     * Copy link handler
     *
     * @param o
     */
    var copyLink = function (o)
    {
        o = $(o);
        o.addClass('selected');
        setTimeout(function ()
        {
            o.removeClass('selected');
        }, 300);
    }

    /**
     * Returns selected items
     */
    this.getSelectedItems = function()
    {
        var obj = list_items.find('.capsule-cms-image-history-item').has('.capsule-cms-image-history-select.selected');
        var ret = [];
        obj.each(function (i, o)
        {
            ret[i] = $(o).data();
        });
        return ret;
    };
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
