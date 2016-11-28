/**
 * Created by polyanin on 17.11.2016.
 */
function CapsuleCmsDataGrid (container)
{
    /**
     * static init
     *
     * @param self o same object
     * @param c same function
     */
    (function(o, c) {
        if (undefined === c.instance) {
            c.instance = o;
        } else {
            if (c.instance !== o) {
                console.log('Instance already exists. Only one instance allowed!');
                throw new Error('Instance already exists. Only one instance allowed!');
            }
        }
        if (undefined === c.getInstance) {
            c.getInstance = function()
            {
                return c.instance;
            };
        }
    })(this, arguments.callee);

    var prefix = '.capsule-cms-data-grid-';

    var content = container.find(prefix + 'content').eq(0);

    var scroll_horizontal_wrapper = container.find(prefix + 'scroll-horizontal-wrapper').eq(0);
    var scroll_horizontal = container.find(prefix + 'scroll-horizontal').eq(0);
    var scroll_horizontal_content = container.find(prefix + 'scroll-horizontal-content').eq(0);

    var scroll_vertical_wrapper = container.find(prefix + 'scroll-vertical-wrapper').eq(0);
    var scroll_vertical = container.find(prefix + 'scroll-vertical').eq(0);
    var scroll_vertical_content = container.find(prefix + 'scroll-vertical-content').eq(0);

    var body = container.find(prefix + 'body').eq(0);
    var data = container.find(prefix + 'data').eq(0);

    var sidebar_body_col = container.find(prefix + 'sidebar-body-col').eq(0);
    var sidebar_header = container.find(prefix + 'sidebar-header').eq(0);
    var sidebar_header_checkbox = container.find(prefix + 'sidebar-header :checkbox').eq(0);

    var header_row = container.find(prefix + 'header-row').eq(0);

    var scrollbar_calc_outer = $('<div>').addClass('capsule-cms-data-grid-scrollbar-calc-outer');
    var scrollbar_calc_inner = $('<div>').addClass('capsule-cms-data-grid-scrollbar-calc-inner');

    container.append(scrollbar_calc_outer);
    scrollbar_calc_outer.append(scrollbar_calc_inner);

    data.children('div').on('click', function ()
    {
        var o = $(this);
        data.children('div').not(o).removeClass('capsule-cms-data-grid-active');
        o.toggleClass('capsule-cms-data-grid-active');
    });

    data.children('div').each(function (i, o)
    {
        var div = $('<div>');
        var o = $(o);
        div.css({
            height: o.height()
        });
        sidebar_body_col.append(div);
        div.append('<input type="checkbox">');
    });

    /**
     *
     * @returns {{h: number, v: number}}
     */
    var calcScrollbarWidth = function ()
    {
        var ow = scrollbar_calc_outer.width();
        var iw = scrollbar_calc_inner.width();
        var oh = scrollbar_calc_outer.height();
        var ih = scrollbar_calc_inner.height();
        return {
            h: Math.round(oh - ih),
            v: Math.round(ow - iw)
        }
    };

    var init = function ()
    {
        var system_scrollbar_size = calcScrollbarWidth();
        var data_width = data.width();
        var data_height = data.height();
        scroll_horizontal_content.css({
            width: data_width
        });
        scroll_vertical_content.css({
            height: data_height
        });
        var threshold = 0;
        var hcalc = function () {
            if ((data_width - scroll_horizontal.width()) > threshold) {
                content.css({
                    bottom: system_scrollbar_size.h
                });
                scroll_horizontal_wrapper.css({
                    height: system_scrollbar_size.h || 4,
                });
                scroll_vertical_wrapper.css({
                    bottom: system_scrollbar_size.h
                });
            } else {
                content.css({
                    bottom: 0
                });
                scroll_horizontal_wrapper.css({
                    height: 0
                });
                scroll_vertical_wrapper.css({
                    bottom: 0
                });
            }
        };
        var vcalc = function () {
            if ((data_height - scroll_vertical.height()) > threshold) {
                content.css({
                    right: system_scrollbar_size.v
                });
                scroll_vertical_wrapper.css({
                    width: system_scrollbar_size.v || 4
                });
                scroll_horizontal_wrapper.css({
                    right: system_scrollbar_size.v
                });
            } else {
                content.css({
                    right: 0
                });
                scroll_vertical_wrapper.css({
                    width: 0
                });
                scroll_horizontal_wrapper.css({
                    right: 0
                });
            }
        };
        vcalc();
        hcalc();
        vcalc();
    };

    init();

    $(window).resize(function()
    {
        init();
    });

    scroll_vertical.scroll(function ()
    {
        var scroll_top = -1 * $(this).scrollTop();
        data.css({
            top: scroll_top
        })
        sidebar_body_col.css({
            top: scroll_top
        })
    });

    scroll_horizontal.scroll(function ()
    {
        var scroll_left = -1 * $(this).scrollLeft();
        data.css({
            left: scroll_left
        })
        header_row.css({
            left: scroll_left
        })
    });

    body.on('mousewheel', function (event)
    {
        var current = scroll_vertical.scrollTop();
        scroll_vertical.scrollTop(current + -111 * event.deltaY);
    });

    var start_position_x = 0;
    var start_position_y = 0;


    body.on('touchstart', function(event)
    {
        var e = event.originalEvent;
        start_position_x = scroll_horizontal.scrollLeft() + e.touches[0].pageX;
        start_position_y = scroll_vertical.scrollTop() + e.touches[0].pageY;
        // e.preventDefault();
    });

    body.on('touchend', function(event)
    {
        var e = event.originalEvent;
        start_position_x = 0;
        start_position_y = 0;
        // e.preventDefault();
    });

    body.on('touchmove', function(event)
    {
        var e = event.originalEvent;
        scroll_horizontal.scrollLeft(start_position_x - e.touches[0].pageX);
        scroll_vertical.scrollTop(start_position_y - e.touches[0].pageY);
        // e.preventDefault();
    });

    sidebar_header_checkbox.prop({
        checked: false
    }).change(function ()
    {
        sidebar_body_col.find(':checkbox').prop({
            checked: sidebar_header_checkbox.prop('checked')
        });
    });

    /**
     * Обработчик группового выделения строк
     */
    var last_sidebar_checkbox;
    var all_sidebar_checkboxes = sidebar_body_col.find(':checkbox');
    all_sidebar_checkboxes.each(function (i, o)
    {
        $(o).dblclick(function(event)
        {
            event.stopPropagation();
        });
        // click handler
        $(o).click(function(event)
        {
            event.stopPropagation();
            if (!last_sidebar_checkbox) {
                last_sidebar_checkbox = this;
                return;
            }
            if (last_sidebar_checkbox == this) {
                return;
            }
            if (!event.shiftKey) {
                last_sidebar_checkbox = this;
                return;
            }
            var flag = 0;
            for (var i = 0; i < all_sidebar_checkboxes.length; i++) {
                if (all_sidebar_checkboxes[i] == this || all_sidebar_checkboxes[i] == last_sidebar_checkbox) {
                    if (flag == 0) {
                        flag = 1;
                    } else if (flag == 1) {
                        all_sidebar_checkboxes[i].checked = last_sidebar_checkbox.checked;
                        break;
                    }
                }
                if (flag == 1) {
                    all_sidebar_checkboxes[i].checked = last_sidebar_checkbox.checked;
                }
            }
            last_sidebar_checkbox = this;
        });
    });

    /**
     * Retrieve checked rows
     *
     * @returns {{length: number}}
     */
    this.getCheckedRows = function ()
    {
        var elements = {
            length: 0
        };
        var rows = data.children('div');
        var index, element;
        var checked = sidebar_body_col.children('div').has(':checked');
        if (checked.length) {
            for (var i = 0; i < checked.length; i++) {
                index = sidebar_body_col.children('div').index(checked[i]);
                element = rows.eq(index);
                elements[index] = element.data('pk');
            }
            elements.length = i;
        }
        return elements;
    };

    /**
     * Retrieve currently-selected row if it only one!
     *
     * @returns {{length: number}}
     */
    this.getCurrentRow = function ()
    {
        var elements = {
            length: 0
        };
        var rows = data.children('div');
        var selected = data.children('[class$="-active"]');
        if (selected.length == 1) {
            var element = selected.eq(0);
            var index = rows.index(element);
            elements[index] = element.data('pk');
            elements.length = 1;
        }
        return elements;
    };


}