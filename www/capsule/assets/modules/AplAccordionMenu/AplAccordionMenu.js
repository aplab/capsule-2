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
            ul.append(li);
            var child_number = 0;
            if (item.items !== undefined) {
                child_number = createMenuItems(li, item.items, level + 1);
            }
            var span = $('<span>');
            span.text(item.caption);
            if (child_number) {
                li.prepend(span);
                span.click(function() {
                    var $this = $(this);
                    var $next = $this.next();
                    var $parent = $this.parent();
                    $next.slideToggle();
                    $parent.toggleClass('open');
                    var exclude = append_to.find('.apl-accordion-submenu').has($next);
                    append_to.find('.apl-accordion-submenu').not($next).not(exclude).slideUp().parent().removeClass('open');
                });
                li.append('<i class="fa fa-chevron-down"></i>');
            } else {
                if (item.action !== undefined) {
                    if (item.action.type === 'url') {
                        var a = $('<a>');
                        a.text(item.caption);
                        a.prop('href', item.action.url);
                        li.append(a);
                    } else {
                        li.prepend(span);
                        if (item.action.type === 'callback') {
                            span.click(function () {
                                eval(item.action.callback);
                            });
                        }
                    }
                } else {
                    li.prepend(span);
                }
            }
        }
        if (counter) {
            container.append(ul);
        }
        return counter;
    }

    createMenuItems(append_to, data.items);
}