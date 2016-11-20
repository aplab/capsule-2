/**
 * Created by polyanin on 17.11.2016.
 */
var CapsuleCmsDataGrid = function (container)
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

    var content = container.find('.capsule-cms-data-grid-content').eq(0);

    var scroll_horizontal = container.find('.capsule-cms-data-grid-scroll-horizontal').eq(0);
    var scroll_horizontal_content = container.find('.capsule-cms-data-grid-scroll-horizontal-content').eq(0);

    var scroll_vertical = container.find('.capsule-cms-data-grid-scroll-vertical').eq(0);
    var scroll_vertical_content = container.find('.capsule-cms-data-grid-scroll-vertical-content').eq(0);

    var body = container.find('.capsule-cms-data-grid-body').eq(0);
    var data = container.find('.capsule-cms-data-grid-data').eq(0);

    var sidebar_body_col = container.find('.capsule-cms-data-grid-sidebar-body-col').eq(0);
    var sidebar_header = container.find('.capsule-cms-data-grid-sidebar-header').eq(0);
    var sidebar_header_checkbox = container.find('.capsule-cms-data-grid-sidebar-header :checkbox').eq(0);

    var header_row = container.find('.capsule-cms-data-grid-header-row').eq(0);

    data.children('div').on('click', function () {
        var o = $(this);
        data.children('div').not(o).removeClass('capsule-cms-data-grid-active');
        o.addClass('capsule-cms-data-grid-active');
    });

    data.children('div').each(function (i, o) {
        var div = $('<div>');
        var o = $(o);
        div.css({
            height: o.height()
        });
        sidebar_body_col.append(div);
        div.append('<input type="checkbox">');
    });

    var init = function () {
        var scroll_horizontal_height = scroll_horizontal.height() - scroll_horizontal_content.height();
        var scroll_vertical_width = scroll_vertical.width() - scroll_vertical_content.width();
        content.css({
            right: scroll_horizontal_height,
            bottom: scroll_horizontal_height
        });
        scroll_horizontal.css({
            right: scroll_horizontal_height,
        });
        scroll_horizontal_content.css({
            width: data.width(),
        });
        scroll_vertical.css({
            bottom: scroll_horizontal_height
        });
        scroll_vertical_content.css({
            height: data.height(),
        });
    }

    init();

    $(window).resize(function() {
        init();
    });

    scroll_vertical.scroll(function () {
        var scroll_top = -1 * $(this).scrollTop();
        data.css({
            top: scroll_top
        })
        sidebar_body_col.css({
            top: scroll_top
        })
    });

    scroll_horizontal.scroll(function () {
        var scroll_left = -1 * $(this).scrollLeft();
        data.css({
            left: scroll_left
        })
        header_row.css({
            left: scroll_left
        })
    });

    body.on('mousewheel', function (event) {
        var current = scroll_vertical.scrollTop();
        scroll_vertical.scrollTop(current + -111 * event.deltaY);
    });

    var start_position_x = 0;
    var start_position_y = 0;


    body.on('touchstart', function(event) {
        var e = event.originalEvent;
        start_position_x = scroll_horizontal.scrollLeft() + e.touches[0].pageX;
        start_position_y = scroll_vertical.scrollTop() + e.touches[0].pageY;
        // e.preventDefault();
    });

    body.on('touchend', function(event) {
        var e = event.originalEvent;
        start_position_x = 0;
        start_position_y = 0;
        // e.preventDefault();
    });

    body.on('touchmove', function(event) {
        var e = event.originalEvent;
        scroll_horizontal.scrollLeft(start_position_x - e.touches[0].pageX);
        scroll_vertical.scrollTop(start_position_y - e.touches[0].pageY);
        // e.preventDefault();
    });

    sidebar_header_checkbox.prop({
        checked: false
    }).change(function () {
        sidebar_body_col.find(':checkbox').prop({
            checked: sidebar_header_checkbox.prop('checked')
        });
    });

    /**
     * Обработчик группового выделения строк
     */
    var last_sidebar_checkbox;
    var all_sidebar_checkboxes = sidebar_body_col.find(':checkbox');
    all_sidebar_checkboxes.each(function (i, o) {
        $(o).dblclick(function(event) {
            event.stopPropagation();
        });
        // click handler
        $(o).click(function(event) {
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

}