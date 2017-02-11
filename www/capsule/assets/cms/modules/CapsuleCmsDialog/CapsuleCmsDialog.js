/**
 * Created by polyanin on 21.12.2016.
 */
/**
 * @constructor
 */
CapsuleCmsDialog = function (instance_name)
{
    if (CapsuleCmsDialog.instanceExists(instance_name)) {
        throw new Error('Duplicate instance name: "' + instance_name + '"');
    }

    CapsuleCmsDialog.instances[instance_name] = this;

    /**
     * parts
     */
    var dialog, backdrop, container, content, header, body, footer;

    /**
     * retrieve parts
     */
    (function() {
        dialog = $('#' + instance_name).eq(0);
        backdrop = dialog.find('.' + CapsuleCmsDialog.prefix + '-backdrop').eq(0);
        container = dialog.find('.' + CapsuleCmsDialog.prefix + '-container').eq(0);
        content = container.find('.' + CapsuleCmsDialog.prefix + '-content').eq(0);
        header = content.find('.' + CapsuleCmsDialog.prefix + '-header').eq(0);
        body = content.find('.' + CapsuleCmsDialog.prefix + '-body').eq(0);
        footer = content.find('.' + CapsuleCmsDialog.prefix + '-footer').eq(0);
        dialog.css({
            zIndex: ++CapsuleCmsDialog.zIndex
        });
    })();

    /**
     * Returns dialog part
     *
     * @returns {*}
     */
    this.getDialog = function ()
    {
        return dialog;
    };

    /**
     * Returns backdrop part
     *
     * @returns {*}
     */
    this.getBackdrop = function ()
    {
        return backdrop;
    };

    /**
     * Returns container part
     *
     * @returns {*}
     */
    this.getContainer = function ()
    {
        return container;
    };

    /**
     * Returns content part
     *
     * @returns {*}
     */
    this.getContent = function ()
    {
        return content;
    };

    /**
     * Returns header part
     *
     * @returns {*}
     */
    this.getHeader = function ()
    {
        return header;
    };

    /**
     * Returns body part
     *
     * @returns {*}
     */
    this.getBody = function ()
    {
        return body;
    };

    /**
     * Returns footer part
     *
     * @returns {*}
     */
    this.getFooter = function ()
    {
        return footer;
    };

    this.setTitle = function (title)
    {
        var h = this.getHeader();
        h.find('h4.modal-title').text(title);
    };

    /**
     * show window
     */
    this.show = function ()
    {
        dialog.css({
            zIndex: ++CapsuleCmsDialog.zIndex
        }).show();
    };

    /**
     * hide window
     */
    this.hide = function ()
    {
        dialog.hide();
    };

    /**
     * destruct window
     */
    this.purge = function()
    {
        dialog.remove();
        delete CapsuleCmsDialog.instances[instance_name];
    };
};

/**
 * z coordinate of window
 *
 * @type {number}
 */
CapsuleCmsDialog.prefix = 'capsule-cms-dialog';

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
CapsuleCmsDialog.getInstance = function (instance_name)
{
    return this.instances[instance_name] || false;
};

/**
 * Returns true if instance exists or false
 *
 * @param instance_name
 * @returns {*|boolean}
 */
CapsuleCmsDialog.instanceExists = function (instance_name)
{
    return undefined !== this.instances[instance_name];
};

/**
 * Create element
 *
 * @param instance_name
 * @param options
 */
CapsuleCmsDialog.createElement = function (instance_name, options)
{
    if (CapsuleCmsDialog.instanceExists(instance_name)) {
        throw new Error('Duplicate instance name: "' + instance_name + '"');
    }
    options = options || {};
    var e = function (c)
    {
        c = c || '';
        if (c.length) {
            c = CapsuleCmsDialog.prefix + '-' + c;
        } else {
            c = CapsuleCmsDialog.prefix;
        }
        return $(document.createElement('div')).addClass(c);
    };
    var dialog = e().prop({
        id: instance_name
    }).css({
        zIndex: ++CapsuleCmsDialog.zIndex
    });
    var backdrop = e('backdrop');
    var container = e('container');
    var content = e('content');
    var header = e('header');
    var body = e('body');
    var footer = e('footer');
    content.append(header);
    content.append(body);
    content.append(footer);
    container.append(content);
    dialog.append(backdrop);
    dialog.append(container);
    if (options.maximize) {
        content.addClass(CapsuleCmsDialog.prefix + '-maximize');
    } else {
        if (options.width && options.height) {
            content.css({
                width: options.width,
                height: options.height
            });
        } else if (options.width) {
            content.css({
                width: options.width
            }).addClass(CapsuleCmsDialog.prefix + '-maximize-height');
        } else if (options.height) {
            content.css({
                height: options.height
            }).addClass(CapsuleCmsDialog.prefix + '-maximize-width');
        } else {
            content.addClass(CapsuleCmsDialog.prefix + '-maximize');
        }
    }
    if (options.hasOwnProperty('button')) {
        var l = options.button.length;
        if (l) {
            for (var i = 0; i < l; i++) {
                footer.append($(options.button[i]));
            }
        }
    }
    if (options.hasOwnProperty('title')) {
        header.append($('<h4 class="modal-title">' + options.title + '</h4>'));
    }
    dialog.appendTo($('body'));
    var o = new CapsuleCmsDialog(instance_name);
    CapsuleCmsDialog.init();
    return o;
};

/**
 * Обработчик кнопки закрытия окна
 *
 * @param event
 */
CapsuleCmsDialog.closeButtonHandler = function (event)
{
    $(event.target).closest('.' + CapsuleCmsDialog.prefix).hide();
};

/**
 * Обработчик кнопки уничтожения окна
 *
 * @param event
 */
CapsuleCmsDialog.purgeButtonHandler = function (event)
{
    var dialog = $(event.target).closest('.' + CapsuleCmsDialog.prefix);
    var id = dialog.prop('id');
    CapsuleCmsDialog.getInstance(id).purge();
};

/**
 * Инициализация окон
 */
CapsuleCmsDialog.init = function ()
{
    $('.' + CapsuleCmsDialog.prefix).each(function (i, o)
    {
        o = $(o);
        var id = o.prop('id');
        if (!CapsuleCmsDialog.instanceExists(id)) {
            new CapsuleCmsDialog(id);
        }

        /**
         * close handler
         */
        o.find('.' + CapsuleCmsDialog.prefix + '-close').off(
            'click',
            CapsuleCmsDialog.closeButtonHandler
        ).click(CapsuleCmsDialog.closeButtonHandler);

        /**
         * purge handler
         */
        o.find('.' + CapsuleCmsDialog.prefix + '-purge').off(
            'click',
            CapsuleCmsDialog.purgeButtonHandler
        ).click(CapsuleCmsDialog.purgeButtonHandler);
    });
};

/**
 * init
 */
$(document).ready(function ()
{
    CapsuleCmsDialog.init();
    // CapsuleCmsDialog.createElement('test', {
    //     width: 400,
    //     height: 400,
    //     button: [
    //         $('<button type="button" class="btn btn-success capsule-cms-dialog-close">Close</button>')
    //     ]
    // });
    // CapsuleCmsDialog.createElement('test1', {
    //     width: 500,
    //     height: 300,
    //     button: [
    //         $('<button type="button" class="btn btn-info capsule-cms-dialog-purge">Close</button>')
    //     ]
    // });
});
