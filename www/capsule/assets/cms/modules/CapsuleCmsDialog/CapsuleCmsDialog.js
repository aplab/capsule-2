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

    (function() {
        dialog = $('#' + instance_name).eq(0);
        backdrop = dialog.find('.' + CapsuleCmsDialog.prefix + '-backdrop').eq(0);
        container = dialog.find('.' + CapsuleCmsDialog.prefix + '-container').eq(0);
        content = container.find('.' + CapsuleCmsDialog.prefix + '-content').eq(0);
        header = content.find('.' + CapsuleCmsDialog.prefix + '-header').eq(0);
        body = content.find('.' + CapsuleCmsDialog.prefix + '-body').eq(0);
        footer = content.find('.' + CapsuleCmsDialog.prefix + '-footer').eq(0);
    })();

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
                width: options.width,
            }).addClass(CapsuleCmsDialog.prefix + '-maximize-height');
        } else if (options.height) {
            content.css({
                height: options.height
            }).addClass(CapsuleCmsDialog.prefix + '-maximize-width');
        } else {
            content.addClass(CapsuleCmsDialog.prefix + '-maximize');
        }
    }
    if (options.closeButton) {
        var close_button = $(options.closeButton);
        close_button.addClass(CapsuleCmsDialog.prefix + '-close');
        footer.append(close_button);
    }
    dialog.appendTo($('body'));
    new CapsuleCmsDialog(instance_name);
    CapsuleCmsDialog.init();
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
 * Инициализация окон
 */
CapsuleCmsDialog.init = function ()
{
    $('.capsule-cms-dialog-close').off(
        'click',
        CapsuleCmsDialog.closeButtonHandler
    ).click(CapsuleCmsDialog.closeButtonHandler);
};

$(document).ready(function ()
{
    CapsuleCmsDialog.init();
});
