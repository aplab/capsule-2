/**
 * Created by polyanin on 31.10.2016.
 */
$(document).ready(function () 
{
    $('#capsule-cms-sidebar-action-buttons .fa-close').click(function ()
    {
        $('#capsule-cms-sidebar-wrapper')
            .addClass('capsule-cms-sidebar-wrapper-hide')
            .removeClass('capsule-cms-sidebar-wrapper-show');
    });

    $('#capsule-cms-nav .fa-bars').click(function ()
    {
        $('#capsule-cms-sidebar-wrapper')
            .addClass('capsule-cms-sidebar-wrapper-show')
            .removeClass('capsule-cms-sidebar-wrapper-hide');
    });
});