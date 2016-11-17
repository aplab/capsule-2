/**
 * Created by polyanin on 17.11.2016.
 */
var CapsuleCmsDataGrid = function (container)
{
    var container = container;
    var header = container.find('.capsule-cms-data-grid-header');
    var body = container.find('.capsule-cms-data-grid-body');

    body.on('scroll', function () {
        header.css({
            left: -body.scrollLeft()
        });
    });
    header.css({
        left: -body.scrollLeft()
    });

    body.children('div').on('click', function () {
        var o = $(this);
        body.children('div').not(o).removeClass('capsule-cms-data-grid-active');
        o.addClass('capsule-cms-data-grid-active');
    });

}