/**
 * Created by polyanin on 17.11.2016.
 */
var CapsuleCmsDataGrid = function (container)
{
    var container = container;
    var header = container.find('.capsule-cms-data-grid-header').eq(0);
    var header_row = container.find('.capsule-cms-data-grid-header-row').eq(0);
    var body = container.find('.capsule-cms-data-grid-body').eq(0);
    var content = container.find('.capsule-cms-data-grid-content').eq(0);
    var sidebar_body_col = container.find('.capsule-cms-data-grid-sidebar-body-col').eq(0);

    var scrollSync = function () {
        header_row.css('left', content.position().left);
        sidebar_body_col.css('top', content.position().top);
    }
    scrollSync();

    body.on('touchmove', scrollSync);

    content.children('div').on('click', function () {
        var o = $(this);
        content.children('div').not(o).removeClass('capsule-cms-data-grid-active');
        o.addClass('capsule-cms-data-grid-active');
    });

    content.children('div').each(function (i, o) {
        var div = $('<div>');
        var o = $(o);
        div.css({
            height: o.height()
        });
        sidebar_body_col.append(div);
        div.append('<input type="checkbox">');
    });
}