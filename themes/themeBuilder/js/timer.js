var timer;
var timeOffset = 0;

$(function () {
    timeOffset = Math.floor(new Date() / 1000) - parseInt($("meta[name='timestamp']").attr("content"));
});

function checkTimer(interval) {

    $('[data-timer-type="name"], [data-timer-type="inline"], [data-timer-type="color"]').each(function () {
        var timeType = $(this).attr('data-timer-type');
        var ts = parseInt($(this).attr("data-timer"));
        var now = Math.round(new Date() / 1000) - timeOffset;
        var time = ts - now;
        
        var removeClass = "";
        var addClass = "";
        if (time > 0) {
            if ((time - interval) == -1) {
                removeClass = 'text-danger';
                addClass = 'text-success';

                // Timer has finished and we are in the timer loop
                clearInterval(timer);
            } else {
                removeClass = 'text-success';
                addClass = 'text-danger';
            }
        } else {
            var removeClass = 'text-danger';
            var addClass = 'text-success';
        }

        if((addClass == 'text-success') && $(this).attr('data-reload-when-done') !== undefined) {
            setTimeout(function () {
                clearInterval(timer);
                document.location.reload();
           }, 1000);
        }

        const redirect = $(this).attr('data-redirect-when-done');
        if((addClass == 'text-success') && (redirect !== undefined)) {
            setTimeout(function () {
                clearInterval(timer);
                document.location.href = (document.location.origin + "/" + redirect);
            }, 1000);
        }

        if((addClass == 'text-success') && $(this).attr('data-remove-when-done') !== undefined) {    
            clearInterval(timer);   
            $(this).parent().remove();
        }

        const enableWhenDoneText = $(this).attr('enable-with-text-when-done');
        if((addClass == 'text-success') && (enableWhenDoneText !== undefined)) {
            $(this).removeClass("disabled");
            $(this).removeAttr("data-timer-type");
            $(this).removeAttr("data-timer");
            $(this).removeAttr("enable-with-text-when-done");
            $(this).removeClass(removeClass).html(enableWhenDoneText);
            return;
        }
            
        var hours = Math.floor(time / 3600);
        var mins = Math.floor((time - (hours * 3600)) / 60);
        var sec = (time % 60);

        if (hours < 10) {
            hours = ('0' + hours);
        }
        if (mins < 10) {
            mins = ('0' + mins);
        }

        if (sec < 10) {
            sec = ('0' + sec);
        }

        if (time < 0) {
            var hours = "00";
            var mins = "00";
            var sec = "00";
        }

        if (timeType === 'color') {
            $(this).removeClass(removeClass).addClass(addClass);
		} else if (timeType === 'name') {
            $(this).removeClass(removeClass).addClass(addClass).find('span').eq(1).html(hours + ":" + mins + ":" + sec);
        } else {
            $(this).removeClass(removeClass).addClass(addClass).html(hours + ":" + mins + ":" + sec);
        }   
    });
}

$(function () {
    
    $(".alert .close").bind("click", function () {
        $(this).parent().remove();
    });

    var d = new Date();

    checkTimer(0);

    timer = setInterval(function () {
        checkTimer(1);
    }, 1000);

    $('[data-timer-type="name"]').bind("mouseover", function () {
        $(this).find('span').eq(0).hide();
        $(this).find('span').eq(1).show();
    });

    $('[data-timer-type="name"]').bind("mouseout", function () {
        $(this).find('span').eq(1).hide();
        $(this).find('span').eq(0).show();
    });

    $('[data-timer-type="name"]').each(function () {
        $(this).find('span').eq(1).hide();
    });
});