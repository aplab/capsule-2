/**
 * Created by polyanin on 16.11.2016.
 */
function CapsuleCmsActionMenu(data, append_to)
{
    var append_to = append_to || $('body');
    var instanceName = data.instanceName;

    /**
     * static init
     *
     * @param self o same object
     * @param c same function
     */
    (function(o, c) {
        if (undefined === c.instances) {
            c.instances = new Array();
        }
        if (undefined === c.getInstance) {
            c.getInstance = function(instance_name)
            {
                if (undefined !== c.instances[instance_name]) {
                    return c.instances[instance_name];
                }
                return null;
            };
        }
        if (undefined !== c.instances[instanceName]) {
            console.log('Instance already exists: ' + instanceName);
            console.error('Instance already exists: ' + instanceName);
            throw new Error('Instance already exists: ' + instanceName);
        }
        c.instances[instanceName] = o;
        c.instanceNumber = Object.keys(c.instances).length;
    })(this, arguments.callee);

    var menu = $('<div>').prop({
        id: instanceName,
        class: 'capsule-cms-action-menu'
    });
    append_to.append(menu);

    var wrapper = $('<div>').prop({
        class: 'capsule-cms-action-menu-wrapper'
    });
    menu.append(wrapper);

    var container = $('<div>').prop({
        class: 'capsule-cms-action-menu-container'
    });
    wrapper.append(container);

    var content = $('<div>').prop({
        class: 'capsule-cms-action-menu-content'
    });
    container.append(content);

    var scrollbar = $('<div>').prop({
        class: 'capsule-cms-action-menu-scrollbar'
    });
    wrapper.append(scrollbar);

    this.show = function () {
        menu.show();
        calcWidth();
        init();
    }

    this.hide = function () {
        menu.hide();
    }

    /**
     *
     * @param container
     * @param data
     */
    var createMenuItems = function ()
    {
        var items = data.items;
        for (var id in items) {
            var item = items[id];
            var icon = null;
            var span;
            if (item.icon !== undefined && item.icon.name !== undefined) {
                icon = $('<i class="' + item.icon.name + '" aria-hidden="true"></i>');
            }
            if (item.action !== undefined) {
                if (item.action.type === 'url') {
                    var a = $('<a>');
                    a.text(item.caption);
                    a.prop('href', item.action.url);
                    content.append(a);
                    a.append(icon);
                } else {
                    span = $('<span>');
                    span.text(item.caption);
                    content.append(span);
                    if (item.action.type === 'callback') {
                        (function (v)
                        {
                            span.click(function ()
                            {
                                eval(v);

                            });
                        })(item.action.callback);
                    }
                    span.append(icon);
                }
            } else {
                span = $('<span>');
                span.text(item.caption);
                content.append(span);
                span.append(icon);
            }
        }
    };

    createMenuItems();

    var containerHeight;
    var contentHeight;
    var scrollbarHeight;
    var scrollbarTop;
    var skipInit = false;

    var content_scroll_distance;
    var scrollbar_move_distance;

    var margin_right;

    var calcWidth = function () {
        var wrapper_width = wrapper.width();
        var content_width = content.width();
        var scrollbar_width = wrapper_width - content_width;
        margin_right = parseInt(container.css('marginRight'), 10) + (-scrollbar_width);
        container.css({
            marginRight: margin_right
        });
    }

    var init = function() {
        if (skipInit) {
            return;
        }
        containerHeight = container.height();
        contentHeight = content.height();
        if (containerHeight >= contentHeight) {
            scrollbar.hide();
            return;
        }
        scrollbarHeight = containerHeight * containerHeight / contentHeight;
        if (scrollbarHeight < 20) {
            scrollbarHeight = 20;
        }
        content_scroll_distance = contentHeight - containerHeight;
        scrollbar_move_distance = containerHeight - scrollbarHeight;

        scrollbarTop = container.scrollTop() * scrollbar_move_distance / content_scroll_distance;
        scrollbar.css({
            height: scrollbarHeight,
            top: scrollbarTop
        });
        if (margin_right) {
            scrollbar.show();
        }
    };

    $(window).resize(function() {
        init();
        calcWidth();
    });

    container.scroll(function() {
        init();
    });

    init();
}