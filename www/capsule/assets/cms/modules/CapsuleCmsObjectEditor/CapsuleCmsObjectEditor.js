/**
 * Created by polyanin on 06.12.2016.
 */
/**
 * Created by polyanin on 17.11.2016.
 */
function CapsuleCmsObjectEditor(container) {
    /**
     * static init
     *
     * @param self o same object
     * @param CapsuleCmsObjectEditor c
     */
    (function (o, c) {
        if (undefined === c.instance) {
            c.instance = o;
        } else {
            if (c.instance !== o) {
                console.log('Instance already exists. Only one instance allowed!');
                throw new Error('Instance already exists. Only one instance allowed!');
            }
        }
        if (undefined === c.getInstance) {
            c.getInstance = function () {
                return c.instance;
            };
        }
    })(this, arguments.callee);

    var prefix = '.capsule-cms-object-editor-';
    var class_prefix = 'capsule-cms-object-editor-';

    var tabs = container.find(prefix + 'tabs').eq(0);
    var tabs_wrapper = container.find(prefix + 'tabs-wrapper').eq(0);
    var head = container.find(prefix + 'head').eq(0);
    var body = container.find(prefix + 'body').eq(0);
    var arrow_left = container.find(prefix + 'arrow-left').eq(0);
    var arrow_right = container.find(prefix + 'arrow-right').eq(0);

    var tabs_width = [];
    var tabs_scroll = [];
    var tabs_width_sum = 0;

    var init = function () {
        tabs.find(prefix + 'tab').each(function (i, o) {
            tabs_width[i] = $(o).outerWidth();
            tabs_scroll[i] = tabs_width_sum;
            tabs_width_sum += tabs_width[i];
        }).removeClass(class_prefix + 'tab-active').eq(0).addClass(class_prefix + 'tab-active');
        tabs_width_sum += .1;
        body.find(prefix + 'panel')
            .removeClass(class_prefix + 'panel-active')
            .eq(0).addClass(class_prefix + 'panel-active');
        tabs.css({
            width: tabs_width_sum
        });
    };

    var initWidth = function () {
        if (head.width() < tabs_width_sum) {
            arrow_left.show();
            arrow_right.show();
            tabs_wrapper.css({
                left: 30,
                right: 30
            });
        } else {
            arrow_left.hide();
            arrow_right.hide();
            tabs_wrapper.css({
                left: 0,
                right: 0
            })
        }
    };

    $(window).resize(function () {
        initWidth();
    });

    tabs.find(prefix + 'tab').click(function () {
        var all = tabs.find(prefix + 'tab');
        $(this).addClass(class_prefix + 'tab-active');
        all.not(this).removeClass(class_prefix + 'tab-active');
        var index = all.index(this);
        // пытаемся проскроллить так, чтобы выбранная вкладка оказалась посередине
        tabs_wrapper.scrollLeft(tabs_scroll[index] - (tabs_wrapper.width() - tabs_width[index]) / 2);
        body.find(prefix + 'panel')
            .removeClass(class_prefix + 'panel-active')
            .eq(index).addClass(class_prefix + 'panel-active');
    });

    arrow_left.click(function () {
        tabs_wrapper.scrollLeft(tabs_wrapper.scrollLeft() - 50);
    });
    arrow_right.click(function () {
        tabs_wrapper.scrollLeft(tabs_wrapper.scrollLeft() + 50);
    });

    init();
    initWidth();


    CKEDITOR.editorConfig = function (config) {
        // Define changes to default configuration here. For example:
        // config.language = 'fr';
        config.uiColor = 'f2f1f0';
        config.resize_enabled = false;
        config.toolbarCanCollapse = true;
        config.removePlugins = 'about,maximize';
        config.height = 10000;
        config.allowedContent = true;
    };

    /**
     * adjust editor size
     */
    var fitEditors = function () {
        var height = body.height();
        var width = body.width();
        for (var o in CKEDITOR.instances) {
            if (CKEDITOR.instances.hasOwnProperty(o)) {
                CKEDITOR.instances[o].resize(width, height);
            }
        }
    };

    if ('undefined' != typeof(CKEDITOR)) {
        CKEDITOR.on('instanceReady', function (ev) {
            var editor = ev.editor;
            var height = body.height();
            var width = body.width();
            editor.resize(width, height);
            editor.on('afterCommandExec', function () {
                var width = body.width();
                var height = body.height();
                editor.resize(width, height);
            });
            $(window).resize(function () {
                fitEditors();
            });
        });
    }

    var is_small = function () {
        var width = $(window).width();
        var height = $(window).height();
        var threshold = 768;
        return width <= threshold && height <= threshold;
    };

    var editor_config = function () {
        var config = {
            uiColor: '#ffffff',
            removePlugins: 'about,maximize',
            resize_enabled: false,
            height: 10000,
            removeButtons: 'Cut,Copy,Scayt'
        };
        // Define changes to default configuration here.
        // For complete reference see:
        // http://docs.ckeditor.com/#!/api/CKEDITOR.config
        // Set the most common block elements.
        config.format_tags = 'p;h1;h2;h3;pre';
        // Simplify the dialog windows.
        // config.removeDialogTabs = 'image:advanced;link:advanced';
        if (is_small()) {
            // The toolbar groups arrangement, optimized for a single toolbar row.
            config.toolbar = [
                {name: 'document', items: ['Source']},
                {name: 'clipboard', items: ['Undo', 'Redo']},
                {name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript']},
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                },
                {name: 'links', items: ['Link', 'Unlink']},
                {name: 'insert', items: ['Image', 'Table', 'Smiley', 'SpecialChar']},
                {name: 'colors', items: ['TextColor', 'BGColor']}
            ];
            // The default plugins included in the basic setup define some buttons that
            // are not needed in a basic editor. They are removed here.
            config.removeButtons = 'Cut,Copy,Paste,Undo,Redo,Anchor,Underline,Strike,Subscript,Superscript';
            // Dialog windows are also simplified.
            config.removeDialogTabs = 'link:advanced';
            config.toolbarStartupExpanded = false;
            config.toolbarCanCollapse = true;
        } else {
            config.toolbar = [
                {name: 'document', items: ['Source']},
                {name: 'clipboard', items: ['Paste', 'PasteText', 'PasteFromWord', 'Undo', 'Redo']},
                {name: 'editing', items: ['Find', 'Replace', 'SelectAll']},
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'CopyFormatting', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
                },
                {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {name: 'tools', items: ['ShowBlocks']},
                {
                    name: 'insert',
                    items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe']
                },
                {name: 'styles', items: ['Styles', 'Format', 'FontSize']}
            ];
            config.toolbarCanCollapse = false;
            config.toolbarStartupExpanded = true;
        }
        return config;
    };

    this.save = function () {
        body.children('form').eq(0).submit();
    };

    this.saveAndExit = function () {
        var e = document.createElement('input');
        $(e).attr({
            'type': 'hidden',
            'name': 'saveAndExit',
            'value': 'Y'
        });
        body.children('form').eq(0).append(e);
        this.save();
    }

    this.saveAndAdd = function () {
        var e = document.createElement('input');
        $(e).attr({
            'type': 'hidden',
            'name': 'saveAndAdd',
            'value': 'Y'
        });
        body.children('form').eq(0).append(e);
        this.save();
    }

    this.saveAsNew = function () {
        var e = document.createElement('input');
        $(e).attr({
            'type': 'hidden',
            'name': 'saveAsNew',
            'value': 'Y'
        });
        body.children('form').eq(0).append(e);
        this.save();
    }

    $('textarea' + prefix + 'ckeditor').ckeditor(editor_config());

    $(prefix + 'datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        ignoreReadonly: true,
        allowInputToggle: true,
        focusOnShow: true,
        showClose: true,
        showClear: true
    });

    /**
     * workaround function to clear autocomplete password
     */
    setTimeout(function () {
        body.find(':password').val('');
    }, 10);
}