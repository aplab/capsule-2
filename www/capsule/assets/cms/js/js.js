/**
 * Created by polyanin on 31.10.2016.
 */
$(document).ready(function () 
{
    $('#capsule-cms-sidebar-action .fa-bars').click(function ()
    {
        $('#capsule-cms-sidebar-nav').addClass('capsule-cms-sidebar-nav-hide').removeClass('capsule-cms-sidebar-nav-show');
    });

    $('#capsule-cms-nav .fa-bars').click(function ()
    {
        $('#capsule-cms-sidebar-nav').addClass('capsule-cms-sidebar-nav-show').removeClass('capsule-cms-sidebar-nav-hide');
    });
});