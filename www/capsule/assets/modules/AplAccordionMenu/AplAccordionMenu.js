/**
 * Created by polyanin on 07.11.2016.
 */
function AplAccordionMenu(data, append_to)
{
    append_to = append_to || $('body');
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

    /**
     *
     * @param container
     * @param data
     */
    var createMenuItems = function (container, data, level)
    {
        level = level || 0;
        var ul = $('<ul>');
        if (0 === level) {
            ul.addClass('apl-accordion-menu');
        } else {
            ul.addClass('apl-accordion-submenu');
        }
        var counter = 0;
        for (var id in data) {
            counter++;
            var item = data[id];
            var li = $('<li>');
            li.prop('id', item.id);
            ul.append(li);
            var child_number = 0;
            if (item.items !== undefined) {
                child_number = createMenuItems(li, item.items, level + 1);
            }
            var span = $('<span>');
            span.text(item.caption);
            var icon = null;
            if (item.icon !== undefined && item.icon.name !== undefined) {
                icon = $('<i class="' + item.icon.name + '" aria-hidden="true"></i>');
            }
            if (child_number) {
                li.prepend(span);
                span.click(function()
                {
                    var $this = $(this);
                    var $next = $this.next();
                    var $parent = $this.parent();
                    $next.slideToggle();
                    $parent.toggleClass('open');
                    var exclude = append_to.find('.apl-accordion-submenu').has($next);
                    append_to.find('.apl-accordion-submenu').not($next).not(exclude).slideUp().parent().removeClass('open');
                    if ($parent.hasClass('open')) {
                        Cookies.set(
                            'apl-accordion-menu-' + instanceName,
                            $parent.prop('id'),
                            {
                                expires: 7,
                                path: '/'
                            }
                        );
                        return;
                    }
                    var closest = $parent.closest('.open');
                    if (closest.length) {
                        Cookies.set(
                            'apl-accordion-menu-' + instanceName,
                            closest.prop('id'),
                            {
                                expires: 7,
                                path: '/'
                            }
                        );
                        return;
                    }
                    Cookies.set(
                        'apl-accordion-menu-' + instanceName,
                        '',
                        {
                            expires: 7,
                            path: '/'
                        }
                    );
                });
                span.append('<i class="fa fa-chevron-down"></i>');
                if (icon) {
                    span.append(icon);
                }
            } else {
                if (item.action !== undefined) {
                    if (item.action.type === 'url') {
                        var a = $('<a>');
                        a.text(item.caption);
                        a.prop('href', item.action.url);
                        if (item.action.hasOwnProperty('target')) {
                            a.prop('target', item.action.target);
                        }
                        li.append(a);
                        a.append(icon);
                    } else {
                        li.prepend(span);
                        if (item.action.type === 'callback') {
                            (function (v)//isolation
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
                    li.prepend(span);
                    span.append(icon);
                }
            }
        }
        if (counter) {
            container.append(ul);
        }
        return counter;
    }

    createMenuItems(append_to, data.items);

    var setCurrent = function()
    {
        var current_id = Cookies.get('apl-accordion-menu-' + instanceName);
        var current = $('#' + current_id);
        if (!current.length) {
            return;
        }
        current = current.eq(0);
        current.addClass('open').children('.apl-accordion-submenu').show();
        append_to.find('.apl-accordion-submenu').has(current).show().parent().addClass('open');
    }

    setCurrent();

    setTimeout(function()
    {
        append_to.find('i.fa-chevron-down').addClass('trans');
    }, 100);

    var disableSelection = function(o)
    {
        $(o).onselectstart = function()
        {
            return false;
        };
        $(o).unselectable = "on";
        $(o).css({
            '-moz-user-select': 'none',
            '-khtml-user-select': 'none',
            '-webkit-user-select': 'none',
            '-o-user-select': 'none',
            'user-select': 'none'
        });
    }

    disableSelection(append_to);

}