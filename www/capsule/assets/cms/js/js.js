/**
 * Created by polyanin on 31.10.2016.
 */
$(document).ready(function () 
{
    var collapseSidebarHandler = function (event)
    {
        if ($(event.target).closest('#capsule-cms-sidebar-wrapper').length) {
            return;
        }
        if ($(event.target).closest('#capsule-cms-sidebar-wrapper').length) {
            return;
        }
        $('body').off('click', collapseSidebarHandler);
        collapseSidebar();
    };

    var expandSidebar = function ()
    {
        $('body')
            .addClass('capsule-cms-sidebar-wrapper-expanded')
            .on('click', collapseSidebarHandler);
    };

    var collapseSidebar = function ()
    {
        $('body').removeClass('capsule-cms-sidebar-wrapper-expanded');
    };


    $('#capsule-cms-sidebar-action-buttons').find('.fa-bars').click(function (event)
    {
        event.stopPropagation();
        collapseSidebar();
    });

    var pinned = Cookies('capsule-cms-sidebar-wrapper-pinned');
    if ('yes' === pinned) {
        $('body').addClass('capsule-cms-sidebar-wrapper-pinned');
        expandSidebar();
    }


    $('#capsule-cms-pin-sidebar').click(function (event)
    {
        event.stopPropagation();
        $('body').toggleClass('capsule-cms-sidebar-wrapper-pinned');
        var pinned = $('body').hasClass('capsule-cms-sidebar-wrapper-pinned');
        Cookies(
            'capsule-cms-sidebar-wrapper-pinned',
            pinned ? 'yes' : 'no',
            {
                expires: 7,
                path: '/'
            }
        );
    });

    $('#capsule-cms-nav').find('.fa-bars').click(function (event)
    {
        event.stopPropagation();
        expandSidebar();
    });
});