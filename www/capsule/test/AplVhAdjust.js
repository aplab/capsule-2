/**
 * Created by polyanin on 25.03.2017.
 */
(function() {
    window.AplVhAdjust = function ()
    {

    };

    AplVhAdjust.getDiff = function ()
    {
        var element = $('<div>').css({
            height: '100vh',
            width: 0,
            overflow: 'hidden',
            opacity: 0
        });
        $('body').append(element);
        var eh = element.height();
        element.remove();
        var wh = window.innerHeight;
        var r = Math.random();
        var diff = eh - wh;

        $('.monitor').append(diff);
    }
})();

$(document).ready(function ()
{
    AplVhAdjust.getDiff();
});


