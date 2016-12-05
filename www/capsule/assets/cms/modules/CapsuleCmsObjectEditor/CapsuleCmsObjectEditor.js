/**
 * Created by polyanin on 06.12.2016.
 */
/**
 * Created by polyanin on 17.11.2016.
 */
function CapsuleCmsObjectEditor (container)
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

    var init = function ()
    {
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

    var initWidth = function ()
    {
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

    $(window).resize(function()
    {
        initWidth();
    });

    tabs.find(prefix + 'tab').click(function ()
    {
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
}