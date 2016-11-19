/**
 * Created by polyanin on 17.11.2016.
 */
var CapsuleCmsDataGrid = function (container)
{
    var container = container;
    var header = container.find('.capsule-cms-data-grid-header').eq(0);
    var header_row = container.find('.capsule-cms-data-grid-header-row').eq(0);
    var body = container.find('.capsule-cms-data-grid-body').eq(0);
    var sidebar_body_col = container.find('.capsule-cms-data-grid-sidebar-body-col').eq(0);

    var scrollSync = function () {
        header_row.css('left', -body.scrollLeft());
        sidebar_body_col.css('top', -body.scrollTop());
    }
    scrollSync();

    var scroll_end;
    body.on('scroll', function () {
        if (scroll_end) {
            clearTimeout(scroll_end);
        }
        scrollSync();
        scroll_end = setTimeout(scrollSync, 1000);
    });

    body.children('div').on('click', function () {
        var o = $(this);
        body.children('div').not(o).removeClass('capsule-cms-data-grid-active');
        o.addClass('capsule-cms-data-grid-active');
    });

    body.children('div').each(function (i, o) {
        var div = $('<div>');
        var o = $(o);
        console.log(o);
        div.css({
            height: o.height()
        });
        sidebar_body_col.append(div);
        div.append('<input type="checkbox">');
    });
}