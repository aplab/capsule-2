/**
 * Created by polyanin on 31.10.2016.
 */
$(document).ready(function ()
{
    /**
     * Global object
     *
     * @constructor
     */
    window.CapsuleCms = function() {};

    CapsuleCms.init = function()
    {
        CapsuleCms.checkWindowWidth();
        $(window).on('resize', CapsuleCms.checkWindowWidth);
    };

    CapsuleCms.checkWindowWidth = function ()
    {
        var window_width = parseInt($(document).width(), 10);
        if (window_width < 768) {
            if (CapsuleCms.getSidebarPin()) {
                CapsuleCms.unpinSidebar();
            }
        }
    };

    /**
     * Expand sidebar handler
     */
    CapsuleCms.expandSidebar = function ()
    {
        $('body')
            .addClass('capsule-cms-sidebar-wrapper-expanded')
            .on('click', CapsuleCms.clickOutsideSidebarHandler);
        CapsuleCms.setSidebarOpen(true);
        if (CapsuleCmsObjectEditor.getInstance !== undefined) {
            CapsuleCmsObjectEditor.getInstance().fitEditors();
        }
    };

    /**
     * Collapse sidebar handler
     */
    CapsuleCms.collapseSidebar = function ()
    {
        $('body').removeClass('capsule-cms-sidebar-wrapper-expanded');
        CapsuleCms.setSidebarOpen(false);
        if (CapsuleCmsObjectEditor.getInstance !== undefined) {
            CapsuleCmsObjectEditor.getInstance().fitEditors();
        }
    };

    /**
     * Returns cookie stored data
     *
     * @returns {*}
     */
    CapsuleCms.getCookieData = function ()
    {
        var data = Cookies.getJSON('capsule-cms-data');
        var type = typeof(data);
        if ('object' !== type.toLowerCase()) {
            data = {};
            Cookies.set('capsule-cms-data', data);
        }
        return data;
    };

    /**
     * Returns pin sidebar state
     *
     * @returns {boolean|*}
     */
    CapsuleCms.getSidebarPin = function ()
    {
        var data = CapsuleCms.getCookieData();
        if (undefined === data.sidebar_pin) {
            data.sidebar_pin = false;
            Cookies.set('capsule-cms-data', data);
        }
        return data.sidebar_pin;
    };

    /**
     * Set pin sidebar state
     *
     * @param value
     */
    CapsuleCms.setSidebarPin = function (value)
    {
        var data = CapsuleCms.getCookieData();
        data.sidebar_pin = !!value;
        Cookies.set('capsule-cms-data', data);
    };

    /**
     * Returns open sidebar state
     *
     * @returns {boolean|*}
     */
    CapsuleCms.getSidebarOpen = function ()
    {
        var data = CapsuleCms.getCookieData();
        if (undefined === data.sidebar_open) {
            data.sidebar_open = false;
            Cookies.set('capsule-cms-data', data);
        }
        return data.sidebar_open;
    };

    /**
     * Set pin sidebar state
     *
     * @param value
     */
    CapsuleCms.setSidebarOpen = function (value)
    {
        var data = CapsuleCms.getCookieData();
        data.sidebar_open = !!value;
        Cookies.set('capsule-cms-data', data);
    };

    /**
     *
     */
    CapsuleCms.togglePinSidebar = function ()
    {
        CapsuleCms.setSidebarPin(
            $('body').toggleClass('capsule-cms-sidebar-wrapper-pinned')
                .hasClass('capsule-cms-sidebar-wrapper-pinned')
        );
        if (CapsuleCmsObjectEditor.getInstance !== undefined) {
            CapsuleCmsObjectEditor.getInstance().fitEditors();
        }
        if (CapsuleCms.getSidebarPin()) {
            return;
        }
        CapsuleCms.collapseSidebar();
    };

    /**
     *
     */
    CapsuleCms.unpinSidebar = function ()
    {
        CapsuleCms.setSidebarPin(
            $('body').removeClass('capsule-cms-sidebar-wrapper-pinned')
                .hasClass('capsule-cms-sidebar-wrapper-pinned')
        );
        CapsuleCms.collapseSidebar();
    };

    /**
     * Close sidebar if click outside and sidebar is not pinned
     *
     * @param event
     */
    CapsuleCms.clickOutsideSidebarHandler = function (event)
    {
        if (CapsuleCms.getSidebarPin()) {
            return;
        }
        if ($(event.target).closest('#capsule-cms-sidebar-wrapper').length) {
            return;
        }
        if ($(event.target).closest('#capsule-cms-open-sidebar').length) {
            return;
        }
        $('body').off('click', CapsuleCms.clickOutsideSidebarHandler);
        CapsuleCms.collapseSidebar();
    };

    /**
     * Expand actionsbar handler
     */
    CapsuleCms.expandActionMenu = function ()
    {
        CapsuleCmsActionMenu.getInstance('capsule-cms-action-menu').show();
        $('body').on('click', CapsuleCms.clickOutsideActionMenuHandler);
    };

    CapsuleCms.collapseActionMenu = function ()
    {
        $('body').off('click', CapsuleCms.clickOutsideActionMenuHandler);
        CapsuleCmsActionMenu.getInstance('capsule-cms-action-menu').hide();
    };

    CapsuleCms.clickOutsideActionMenuHandler = function (event)
    {
        if ($(event.target).closest('#capsule-cms-action-menu').length) {
            return;
        }
        if ($(event.target).closest('#capsule-cms-open-actions').length) {
            return;
        }
        CapsuleCms.collapseActionMenu();
    };

    $('#capsule-cms-close-sidebar').click(function ()
    {
        CapsuleCms.collapseSidebar();
    });

    $('#capsule-cms-open-sidebar').click(function ()
    {
        CapsuleCms.expandSidebar();
    });

    $('#capsule-cms-toggle-pin-sidebar').on('click', function ()
    {
        CapsuleCms.togglePinSidebar();
    });

    $('#capsule-cms-open-actions').on('click', function ()
    {
        CapsuleCms.expandActionMenu();
    });

    CapsuleCms.init();

    window.viewportUnitsBuggyfill.init();

    // $(function () {
    //     $('[data-toggle="tooltip"]').tooltip()
    // });

    CapsuleCms.showModalDialog = function(selector, options)
    {
        CapsuleCms.collapseActionMenu();
        options = options || {};
        $(selector).modal(options);
    };

    $(window).on('beforeunload', function() {
        Cookies.set(
            'capsule-cms-main-menu-scroll-top',
            $('#capsule-cms-main-menu-wrapper').find('.capsule-ui-scrollable-wrapper').scrollTop());
    });
});