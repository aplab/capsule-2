/**
 * Created by polyanin on 21.05.2016.
 */
function CapsuleUiScrollable(instance_name, target)
{
    var instanceName = instance_name;

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

    var target = target;
    if (!target.length) {
        console.log('Target not found: ' + instanceName);
        console.error('Target not found: ' + instanceName);
        throw new Error('Target not found: ' + instanceName);
    }
    target = target.eq(0);
    var target_position = target.css('position').toLowerCase();
    if ('static' === target_position) {
        target.css({
            position: 'relative'
        });
    }

    var container = $('<div>');
    var wrapper = $('<div>');
    var content = $('<div>');
    var scrollbar = $('<div>');

    content.appendTo(wrapper);
    wrapper.appendTo(container);
    scrollbar.appendTo(container);
    content.append(target.contents());
    target.append(container);

    container.addClass('capsule-ui-scrollable');
    wrapper.addClass('capsule-ui-scrollable-wrapper');
    content.addClass('capsule-ui-scrollable-content');
    scrollbar.addClass('capsule-ui-scrollable-scrollbar');

    var wrapperHeight;
    var contentHeight;
    var scrollbarHeight;
    var scrollbarTop;
    var skipInit = false;

    var init = function() {
        if (skipInit) {
            return;
        }
        content.width(container.width());
        wrapperHeight = wrapper.height();
        contentHeight = content.height();
        if (wrapperHeight >= contentHeight) {
            scrollbar.hide();
            return;
        }
        scrollbarHeight = wrapperHeight * wrapperHeight / contentHeight;
        if (scrollbarHeight < 20) {
            scrollbarHeight = 20;
        }
        content_scroll_distance = contentHeight - wrapperHeight;
        scrollbar_move_distance = wrapperHeight - scrollbarHeight;

        scrollbarTop = wrapper.scrollTop() * scrollbar_move_distance / content_scroll_distance;
        scrollbar.css({
            height: scrollbarHeight,
            top: scrollbarTop
        })
        scrollbar.show();
    }

    $(window).resize(function() {
        init();
    });

    wrapper.scroll(function() {
        init();
    });

    target.on('mouseup', function() {
        setTimeout(function () {
            init();
        }, 500);
    });

    scrollbar.draggable({
        axis: 'y',
        containment: 'parent',
        scroll: false,
        start: function() {
            skipInit = true;
            scrollbarTop = parseInt(scrollbar.css('top'), 10);
            wrapper.scrollTop(scrollbarTop / (scrollbar_move_distance / content_scroll_distance));
        },
        drag: function() {
            skipInit = true;
            scrollbarTop = parseInt(scrollbar.css('top'), 10);
            wrapper.scrollTop(scrollbarTop / (scrollbar_move_distance / content_scroll_distance));
        },
        stop: function() {
            skipInit = false;
            scrollbarTop = parseInt(scrollbar.css('top'), 10);
            wrapper.scrollTop(scrollbarTop / (scrollbar_move_distance / content_scroll_distance));
        }
    });

    init();
}